<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uraian extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id_management_topic',
        'level',
        'uraian',
    ];

    public function ManagementPengawasan()
    {
        return $this->hasMany(ManagementPengawasan::class, 'id_management_pengawasan');
    }
    public function ManagementTopic()
    {
        return $this->belongsTo(ManagementTopic::class, 'id_management_topic');
    }
    public function Jawaban()
    {
        return $this->hasMany(Jawaban::class, 'id_management_uraian');
    }
    
}
