<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BeritaAcara extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meeting_date',
        'location',
        'summary',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(BeritaAcaraDocument::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BeritaAcaraImage::class)->orderBy('display_order');
    }
}
