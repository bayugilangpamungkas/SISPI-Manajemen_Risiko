<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BeritaAcaraImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'berita_acara_id',
        'file_name',
        'file_path',
        'caption',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    public function beritaAcara(): BelongsTo
    {
        return $this->belongsTo(BeritaAcara::class);
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
