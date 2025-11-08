<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanKP extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_user',
        'id_management_pengawasan',
        'tahun',
        'nilai',
        'evaluator',
        'status',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function ManagementPengawasan()
    {
        return $this->belongsTo(ManagementPengawasan::class, 'id_management_pengawasan');
    }
}
