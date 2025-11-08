<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;
   /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'nama_unit_kerja',
        'penelaah_peta'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'id_unit_kerja', 'id');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'id_unit_kerja', 'id');
    }
    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class, 'id_unit_kerja', 'id');
    }
}
