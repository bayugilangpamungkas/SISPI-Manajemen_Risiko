<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PIC_RTM extends Model
{
    use HasFactory;

    protected $table = 'pic_rtm';

    protected $fillable = [
        'id_rtm',
        'id_unit_kerja'
    ];

    public function rtm()
    {
        return $this->belongsTo(RTM::class, 'id_rtm', 'id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja', 'id');
    }
}
