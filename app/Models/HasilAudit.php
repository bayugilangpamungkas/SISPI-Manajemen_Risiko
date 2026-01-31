<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilAudit extends Model
{
    use HasFactory;

    protected $table = 'hasil_audit';

    protected $fillable = [
        'peta_id',
        'auditor_id',
        'komentar_1',
        'komentar_2',
        'komentar_3',
        'pengendalian',
        'mitigasi',
        'status_konfirmasi_auditee',
        'status_konfirmasi_auditor',
        'unit_kerja',
        'kode_risiko',
        'kegiatan',
        'level_risiko',
        'risiko_residual',
        'skor_total',
        'tahun_anggaran',
        'nama_pemonev',
        'nip_pemonev',
        'file_lampiran',
    ];

    protected $casts = [
        'skor_total' => 'integer',
    ];

    /**
     * Relasi ke tabel petas (risks)
     */
    public function peta()
    {
        return $this->belongsTo(Peta::class, 'peta_id');
    }

    /**
     * Relasi ke tabel users (auditor)
     */
    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }
}
