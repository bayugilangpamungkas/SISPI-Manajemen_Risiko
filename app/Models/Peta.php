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
     * - menunggu_audit: Admin sudah assign auditor, auditor belum input hasil audit (NEW WORKFLOW)
     * - menunggu_konfirmasi_auditee: Auditor sudah input hasil audit, auditee belum konfirmasi
     * - disetujui_auditee: Auditee sudah konfirmasi, menunggu finalisasi auditor
     * - final: Sudah difinalisasi auditor (LOCKED)
     * 
     * OLD WORKFLOW (tetap dipertahankan untuk backward compatibility):
     * - menunggu_wawancara: Auditor belum input pertanyaan
     * - menunggu_jawaban: Auditee belum jawab
     * - menunggu_review: Auditor belum review
     * - perlu_revisi: Auditor minta revisi
     * - menunggu_konfirmasi_auditor: Auditee sudah revisi, auditor belum konfirmasi
     */
    public function getStatusAuditAttribute()
    {
        // ✅ FINAL: status_telaah = 1 (Auditor sudah finalisasi)
        if ($this->status_telaah == 1) {
            return 'final';
        }

        // ✅ NEW WORKFLOW: Cek apakah menggunakan new workflow (ada pengendalian & mitigasi)
        $isNewWorkflow = $this->pengendalian || $this->mitigasi ||
            in_array($this->status_konfirmasi_auditor, ['Completed', 'Not Completed']);

        if ($isNewWorkflow) {
            // ✅ DISETUJUI AUDITEE: Kedua pihak sudah Completed
            if (
                $this->status_konfirmasi_auditee === 'Completed' &&
                $this->status_konfirmasi_auditor === 'Completed'
            ) {
                return 'disetujui_auditee';
            }

            // ✅ MENUNGGU KONFIRMASI AUDITEE: Auditor sudah submit (Completed/Not Completed)
            if (
                in_array($this->status_konfirmasi_auditor, ['Completed', 'Not Completed']) &&
                !$this->status_konfirmasi_auditee
            ) {
                return 'menunggu_konfirmasi_auditee';
            }

            // ✅ MENUNGGU AUDIT: Auditor belum submit hasil audit
            if ($this->auditor_id && !$this->status_konfirmasi_auditor) {
                return 'menunggu_audit';
            }
        } else {
            // ✅ OLD WORKFLOW: Tetap dipertahankan
            // DISETUJUI AUDITEE (old)
            if ($this->status_konfirmasi_auditee === 'confirmed') {
                return 'disetujui_auditee';
            }

            // MENUNGGU KONFIRMASI AUDITEE (old)
            if (
                $this->status_konfirmasi_auditor === 'reviewed' &&
                $this->status_konfirmasi_auditee !== 'confirmed'
            ) {
                return 'menunggu_konfirmasi_auditee';
            }

            // MENUNGGU KONFIRMASI AUDITOR (old)
            if ($this->status_konfirmasi_auditor === 'revision_submitted') {
                return 'menunggu_konfirmasi_auditor';
            }

            // PERLU REVISI (old)
            if ($this->status_konfirmasi_auditor === 'need_revision') {
                return 'perlu_revisi';
            }

            // MENUNGGU REVIEW (old)
            if (
                $this->auditee_response &&
                !in_array($this->status_konfirmasi_auditor, ['need_revision', 'revision_submitted', 'reviewed'])
            ) {
                return 'menunggu_review';
            }

            // MENUNGGU JAWABAN (old)
            if ($this->template_data && !$this->auditee_response) {
                return 'menunggu_jawaban';
            }

            // MENUNGGU WAWANCARA (old)
            if ($this->auditor_id && !$this->template_data) {
                return 'menunggu_wawancara';
            }
        }

        // Default: Belum ditugaskan
        return 'belum_ditugaskan';
    }

    /**
     * ✅ NEW WORKFLOW: Cek apakah Auditor bisa input hasil audit
     * (Sesuai requirement dosen: input pengendalian, mitigasi, komentar, status)
     */
    public function auditorCanInputAudit()
    {
        $status = $this->status_audit;
        // Auditor bisa input jika status = menunggu_audit
        // ATAU jika belum ada status_konfirmasi_auditor (edit pertama kali)
        return $status === 'menunggu_audit' ||
            ($this->auditor_id && !$this->status_konfirmasi_auditor);
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
     * ✅ Cek apakah Auditee bisa konfirmasi hasil audit
     */
    public function auditeeCanConfirm()
    {
        $status = $this->status_audit;
        // Hanya bisa konfirmasi saat menunggu_konfirmasi_auditee
        return $status === 'menunggu_konfirmasi_auditee';
    }

    /**
     * ✅ NEW: Cek apakah Auditor bisa review hasil tindak lanjut dari Auditee
     * (Setelah Auditee submit tindak lanjut karena status = Not Completed)
     */
    public function auditorCanReviewFollowUp()
    {
        // Auditor bisa review tindak lanjut jika:
        // 1. Auditor set status = Not Completed
        // 2. Auditee sudah submit tindak lanjut (status_konfirmasi_auditee = Completed/Not Completed)
        return $this->status_konfirmasi_auditor === 'Not Completed' &&
               in_array($this->status_konfirmasi_auditee, ['Completed', 'Not Completed']);
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
