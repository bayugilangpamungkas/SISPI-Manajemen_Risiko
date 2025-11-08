<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Head_menu extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'icon'];

    public function Menu()
    {
        return $this->hasMany(Menu::class, 'id_head_menu');
    }
}
