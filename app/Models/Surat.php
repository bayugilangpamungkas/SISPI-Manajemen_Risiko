<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'jenis_surat',
        'tujuan_surat',
        'perihal',
        'isi_surat',
        'tanggal_surat',
        'file_pdf',
        'file_scan',
        'lampiran',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    // Relasi ke User (Admin yang membuat)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke Peta (jika referensi Peta Risiko)
    public function petaRisiko()
    {
        return $this->belongsTo(Peta::class, 'referensi_id');
    }

    // Relasi ke Hasil Audit (jika referensi Audit)
    public function hasilAudit()
    {
        return $this->belongsTo(\App\Models\HasilAudit::class, 'referensi_id');
    }

    // Accessor untuk mendapatkan nama referensi
    public function getReferensiNamaAttribute()
    {
        if ($this->tipe_referensi === 'Peta Risiko' && $this->petaRisiko) {
            return $this->petaRisiko->judul ?? $this->petaRisiko->kode_regist;
        } elseif ($this->tipe_referensi === 'Audit' && $this->hasilAudit) {
            return $this->hasilAudit->kode_risiko ?? 'Audit #' . $this->referensi_id;
        }
        return '-';
    }
}
