<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementPengawasan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id_management_uraian',
        'kualitas_pengawasan',
        'aktivitas_pengawasan',
        'parameter',
        'sub_parameter',
        'cara_pengukuran',
    ];

    public function Uraian()
    {
        return $this->belongsTo(Uraian::class, 'id_management_uraian');
    }
    public function JawabanKP()
    {
        return $this->hasMany(JawabanKP::class, 'id_management_pengawasan');
    }
}
