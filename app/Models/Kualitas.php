<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kualitas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'kualitas_pengawasan',
    ];

    public function Aktivitas()
    {
        return $this->hasMany(Aktivitas::class, 'id_kualitas');
    }
}
