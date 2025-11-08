<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentPr extends Model
{
    use HasFactory;
    protected $fillable = ['peta_id', 'user_id', 'comment', 'jenis'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function peta()
    {
        return $this->belongsTo(Peta::class);
    }
}
