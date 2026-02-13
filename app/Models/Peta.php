<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peta extends Model
{
    use HasFactory;
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'id_kegiatan',
        'waktu',
        'anggota',
        'jenis',
        // 'tahun',
        'judul',
        'dokumen',
        'approvalPr',
        'koreksiPr',
        'koreksiPr_at',
        'kode_regist',
        'anggaran',
        'pernyataan',
        'kategori',
        'uraian',
        'metode',
        'skor_kemungkinan',
        'skor_dampak',
        'tingkat_risiko',
        'status_telaah',
        'waktu_telaah_subtansi',
        'waktu_telaah_teknis',
        'waktu_telaah_spi',
        'auditor_id',
        'template_data',
        'template_sent_at',
        'auditee_response',
        'tampil_manajemen_risiko',
        'file_lampiran',
        'laporan_unit',
        'laporan_spi',
        'pengendalian',
        'mitigasi',
        'status_konfirmasi_auditee',
        'status_konfirmasi_auditor',
        'catatan_revisi', // ✅ Tambahkan kolom catatan_revisi
    ];

    public function getApprovalStatusAttribute()
    {
        $approvedCount = 0;

        if ($this->approvalPr == 'approved') $approvedCount++;

        return $approvedCount;
    }

    public function getStatusAttribute()
    {
        $approvedCount = $this->approval_status;

        if ($approvedCount == 0) {
            return 'Belum';
        } else {
            return 'Selesai';
        }
    }
    public function comment_prs()
    {
        return $this->hasMany(CommentPr::class);
    }
    public function komentarKeuangan()
    {
        return $this->comment_prs()->where('jenis', 'keuangan'); // Filter komentar aspek keuangan
    }

    public function komentarRisiko()
    {
        return $this->comment_prs()->where('jenis', 'analisis'); // Filter komentar analisis risiko
    }
    public function documentHistories()
    {
        return $this->hasMany(DocumentHistory::class);
    }

    public function ketuaPenelaah()
    {
        return $this->hasOne(KetuaPenelaah::class, 'id_peta', 'id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id');
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id', 'id');
    }

    /**
     * ========================================
     * WORKFLOW AUDIT WAWANCARA HELPERS
     * ========================================
     */

    /**
     * Get status audit wawancara berdasarkan field yang ada
     * 
     * Status workflow LENGKAP:
     * - belum_ditugaskan: Belum ada auditor
     * - menunggu_wawancara: Admin sudah assign auditor, auditor belum input pertanyaan
     * - menunggu_jawaban: Auditor sudah input pertanyaan, auditee belum jawab
     * - menunggu_review: Auditee sudah jawab, auditor belum review
     * - perlu_revisi: Auditor minta revisi ke auditee
     * - menunggu_konfirmasi_auditor: Auditee sudah revisi, auditor belum konfirmasi
     * - menunggu_konfirmasi_auditee: Auditor sudah review, auditee belum konfirmasi
     * - disetujui_auditee: Auditee sudah konfirmasi, menunggu finalisasi admin
     * - final: Sudah difinalisasi admin (LOCKED)
     */
    public function getStatusAuditAttribute()
    {
        // ✅ FINAL: status_telaah = 1 (Admin sudah finalisasi)
        if ($this->status_telaah == 1) {
            return 'final';
        }

        // ✅ DISETUJUI AUDITEE: Auditee sudah konfirmasi, menunggu finalisasi
        if ($this->status_konfirmasi_auditee === 'confirmed') {
            return 'disetujui_auditee';
        }

        // ✅ MENUNGGU KONFIRMASI AUDITEE: Auditor sudah review (status = 'reviewed')
        if (
            $this->status_konfirmasi_auditor === 'reviewed' &&
            $this->status_konfirmasi_auditee !== 'confirmed'
        ) {
            return 'menunggu_konfirmasi_auditee';
        }

        // ✅ MENUNGGU KONFIRMASI AUDITOR: Auditee sudah revisi (status = 'revision_submitted')
        if ($this->status_konfirmasi_auditor === 'revision_submitted') {
            return 'menunggu_konfirmasi_auditor';
        }

        // ✅ PERLU REVISI: Auditor minta revisi
        if ($this->status_konfirmasi_auditor === 'need_revision') {
            return 'perlu_revisi';
        }

        // ✅ MENUNGGU REVIEW: Auditee sudah jawab, auditor belum review
        if (
            $this->auditee_response &&
            !in_array($this->status_konfirmasi_auditor, ['need_revision', 'revision_submitted', 'reviewed'])
        ) {
            return 'menunggu_review';
        }

        // ✅ MENUNGGU JAWABAN: Auditor sudah input pertanyaan, auditee belum jawab
        if ($this->template_data && !$this->auditee_response) {
            return 'menunggu_jawaban';
        }

        // ✅ MENUNGGU WAWANCARA: Auditor sudah ditugaskan tapi belum input pertanyaan
        if ($this->auditor_id && !$this->template_data) {
            return 'menunggu_wawancara';
        }

        // Default: Belum ditugaskan
        return 'belum_ditugaskan';
    }

    /**
     * Cek apakah Auditor bisa menginput/edit pertanyaan
     */
    public function auditorCanInputQuestions()
    {
        $status = $this->status_audit;
        // Hanya bisa input saat menunggu_wawancara atau menunggu_jawaban (belum ada response)
        return in_array($status, ['menunggu_wawancara', 'menunggu_jawaban']) && !$this->auditee_response;
    }

    /**
     * Cek apakah Auditee bisa menjawab pertanyaan
     */
    public function auditeeCanAnswer()
    {
        $status = $this->status_audit;
        // Hanya bisa jawab saat menunggu_jawaban dan belum ada response
        return $status === 'menunggu_jawaban' && !$this->auditee_response;
    }

    /**
     * Cek apakah Auditor bisa melakukan review
     */
    public function auditorCanReview()
    {
        $status = $this->status_audit;
        // Hanya bisa review saat menunggu_review dan belum ada konfirmasi auditor
        return $status === 'menunggu_review' && !$this->status_konfirmasi_auditor;
    }

    /**
     * Cek apakah Auditor bisa kirim revisi
     */
    public function auditorCanSendRevision()
    {
        $status = $this->status_audit;
        // Auditor bisa kirim revisi saat menunggu_review (ada jawaban auditee)
        return $status === 'menunggu_review';
    }

    /**
     * ✅ Cek apakah Auditor bisa konfirmasi revisi Auditee
     */
    public function auditorCanConfirmRevision()
    {
        $status = $this->status_audit;
        // Auditor bisa konfirmasi saat status = menunggu_konfirmasi_auditor
        return $status === 'menunggu_konfirmasi_auditor';
    }

    /**
     * Cek apakah Auditee perlu melakukan revisi
     */
    public function auditeeNeedRevision()
    {
        // Jika status_konfirmasi_auditor = 'need_revision'
        return $this->status_konfirmasi_auditor === 'need_revision';
    }

    /**
     * ✅ Cek apakah Auditee bisa melakukan revisi
     */
    public function auditeeCanRevise()
    {
        $status = $this->status_audit;
        // Auditee bisa revisi saat status = perlu_revisi
        return $status === 'perlu_revisi';
    }

    /**
     * Cek apakah Auditee bisa konfirmasi hasil audit
     */
    public function auditeeCanConfirm()
    {
        $status = $this->status_audit;
        // Hanya bisa konfirmasi saat menunggu_konfirmasi_auditee
        return $status === 'menunggu_konfirmasi_auditee';
    }

    /**
     * ✅ Cek apakah Admin/Auditor bisa finalisasi
     */
    public function canBeFinalized()
    {
        $status = $this->status_audit;
        // Hanya bisa difinalisasi saat status = disetujui_auditee
        return $status === 'disetujui_auditee';
    }

    /**
     * Cek apakah audit sudah final (locked)
     */
    public function isAuditFinal()
    {
        return $this->status_audit === 'final';
    }

    /**
     * ✅ Get catatan revisi (decode JSON)
     */
    public function getRevisionNotesAttribute()
    {
        return $this->catatan_revisi ? json_decode($this->catatan_revisi, true) : null;
    }

    /**
     * Get label status untuk tampilan
     */
    public function getStatusAuditLabelAttribute()
    {
        $labels = [
            'belum_ditugaskan' => 'Belum Ditugaskan',
            'menunggu_wawancara' => 'Menunggu Wawancara',
            'menunggu_jawaban' => 'Menunggu Jawaban Auditee',
            'menunggu_review' => 'Menunggu Review Auditor',
            'perlu_revisi' => 'Perlu Revisi',
            'menunggu_konfirmasi_auditor' => 'Menunggu Konfirmasi Auditor',
            'menunggu_konfirmasi_auditee' => 'Menunggu Konfirmasi Auditee',
            'disetujui_auditee' => 'Disetujui Auditee',
            'final' => 'Final',
        ];

        return $labels[$this->status_audit] ?? 'Unknown';
    }

    /**
     * Get badge class untuk status
     */
    public function getStatusAuditBadgeAttribute()
    {
        $badges = [
            'belum_ditugaskan' => 'badge-secondary',
            'menunggu_wawancara' => 'badge-info',
            'menunggu_jawaban' => 'badge-warning',
            'menunggu_review' => 'badge-primary',
            'perlu_revisi' => 'badge-danger',
            'menunggu_konfirmasi_auditor' => 'badge-warning',
            'menunggu_konfirmasi_auditee' => 'badge-success',
            'disetujui_auditee' => 'badge-info',
            'final' => 'badge-dark',
        ];

        return $badges[$this->status_audit] ?? 'badge-secondary';
    }

    /**
     * Get template data (decode JSON)
     */
    public function getQuestionsAttribute()
    {
        return $this->template_data ? json_decode($this->template_data, true) : [];
    }

    /**
     * Get auditee response (decode JSON)
     */
    public function getResponsesAttribute()
    {
        return $this->auditee_response ? json_decode($this->auditee_response, true) : [];
    }
}
