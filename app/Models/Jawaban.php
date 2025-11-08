<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id_user',
        'id_management_uraian',
        'status',
        'dokumen',
        'tahun',
        'validasi',
        'waktu_validasi'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function Uraian()
    {
        return $this->belongsTo(Uraian::class, 'id_management_uraian');
    }
}
