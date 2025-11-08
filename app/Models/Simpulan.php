<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpulan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id_management_topic',
        'id_user',
        'tahun',
        'simpulan',
        'improvement',
    ];

    public function ManagementTopic()
    {
        return $this->belongsTo(ManagementTopic::class, 'id_management_topic');
    }
    public function User()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
