<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_unit_kerja',
        'id_kegiatan',
        'judul',
        'iku',
        'sasaran',
        'proker',
        'indikator',
        'anggaran',
    ];

    public function peta()
    {
        return $this->hasOne(Peta::class, 'id', 'id_kegiatan');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja', 'id');
    }

    /**
     * SCOPE: Kegiatan yang memiliki risiko TAMPIL di tahun tertentu
     */
    public function scopeDenganRisikoTampil($query, $tahun)
    {
        return $query->whereHas('peta', function ($q) use ($tahun) {
            $q->whereYear('created_at', $tahun)
                ->where('tampil_manajemen_risiko', 1);
        });
    }

    /**
     * METHOD: Hitung jumlah kegiatan dengan risiko tampil untuk unit kerja tertentu
     */
    public static function hitungKegiatanTampil($unitKerjaId, $tahun)
    {
        return self::where('id_unit_kerja', $unitKerjaId)
            ->denganRisikoTampil($tahun)
            ->count();
    }
}
