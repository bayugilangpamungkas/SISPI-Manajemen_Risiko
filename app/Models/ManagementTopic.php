<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementTopic extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id_management_sub_element',
        'topik',
    ];

    public function ManagementSubElement()
    {
        return $this->belongsTo(ManagementSubElement::class, 'id_management_sub_element');
    }
    public function Uraian()
    {
        return $this->hasMany(Uraian::class, 'id_management_topic');
    }
    public function Simpulan()
    {
        return $this->hasMany(Simpulan::class, 'id_management_topic');
    }
}
