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
}
