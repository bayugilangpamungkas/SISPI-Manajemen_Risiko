<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'peta_id',
        'dokumen',
        'uploaded_at',
        'status',
    ];

    public function peta()
    {
        return $this->belongsTo(Peta::class);
    }

}
