<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementElement extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'elemen',
        'bobot_elemen',
    ];

    public function ManagementSubElement()
    {
        return $this->hasMany(ManagementSubElement::class, 'id_management_element');
    }
}
