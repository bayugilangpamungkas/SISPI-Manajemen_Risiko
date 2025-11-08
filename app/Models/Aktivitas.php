<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id_kualitas',
        'aktivitas_pengawasan',
    ];

    public function Kualitas()
    {
        return $this->belongsTo(Kualitas::class, 'id_kualitas');
    }
    public function ManagementPengawasan()
    {
        return $this->hasMany(ManagementPengawasan::class, 'id_aktivitas');
    }
}
