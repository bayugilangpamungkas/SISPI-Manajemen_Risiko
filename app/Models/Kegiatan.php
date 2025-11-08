<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_unit_kerja',
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
}
