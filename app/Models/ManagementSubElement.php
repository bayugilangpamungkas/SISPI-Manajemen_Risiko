<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementSubElement extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id_management_element',
        'sub_elemen',
        'bobot_sub_elemen'
    ];

    public function ManagementElement()
    {
        return $this->belongsTo(ManagementElement::class, 'id_management_element');
    }
    public function ManagementTopic()
    {
        return $this->hasMany(ManagementTopic::class, 'id_management_sub_element');
    }
}
