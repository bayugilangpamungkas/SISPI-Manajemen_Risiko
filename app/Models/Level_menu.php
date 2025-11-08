<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level_menu extends Model
{
    use HasFactory;
    protected $fillable = ['id_level', 'id_menu'];

    public function Level()
    {
        return $this->belongsTo(Level::class, 'id_level');
    }
    public function Menu()
    {
        return $this->belongsTo(MEnu::class, 'id_menu');
    }
}
