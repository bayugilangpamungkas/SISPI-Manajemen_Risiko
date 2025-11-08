<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetuaPenelaah extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_peta',
        'id_ketua',
    ];

    public function peta()
    {
        return $this->belongsTo(Peta::class, 'id_peta', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_ketua', 'id');
    }
}
