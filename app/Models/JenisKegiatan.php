<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKegiatan extends Model
{
    use HasFactory;

    public $fillable = [
        'jenis',
    ];

    public $table = 'jenis_kegiatan';

    public function templateDokumen()
    {
        return $this->hasMany(TemplateDokumen::class, 'id_jenis', 'id');
    }
}
