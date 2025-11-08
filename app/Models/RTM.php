<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RTM extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_post',
        'temuan',
        'rekomendasi',
        'rencanaTinJut',
        'rencanaWaktuTinJut',
        'status_rtm',
    ];

    protected $table = 'rtm';

    public function post()
    {
        return $this->belongsTo(Post::class, 'id_post', 'id');
    }

    public function pic_rtm()
    {
        return $this->hasMany(PIC_RTM::class, 'id_rtm', 'id');
        // return $this->belongsToMany(UnitKerja::class, 'pic_rtm', 'id_rtm', 'id_unit_kerja');
    }
}
