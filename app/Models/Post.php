<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'waktu',
        'id_unit_kerja',
        'anggota',
        'tempat',
        'jenis',
        'judul',
        'deskripsi',
        'bidang',
        'tanggungjawab',
        'status_task',
        'hasilReviu',
        'hasilBerita',
        'hasilPengesahan',
        'hasilRubrik',
        'approvalReviu',
        'approvalReviuPIC',
        'commentPIC',
        'commenter',
        'approvalBerita',
        'approvalPengesahan',
        'approvalRubrik',
        'koreksiReviuPIC',
        'laporan_akhir',
        'koreksiReviu',
        'koreksiBerita',
        'koreksiPengesahan',
        'koreksiRubrik',
        'judul_tindak_lanjut',
        'dokumen_tindak_lanjut',
        'rekomendasi',
        'temuan',
        'status_rtm'
    ];
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function getApprovalStatusAttribute()
    {
        $approvedCount = 0;

        if ($this->approvalReviu == 'approved') $approvedCount++;
        if ($this->approvalBerita == 'approved') $approvedCount++;
        if ($this->approvalPengesahan == 'approved') $approvedCount++;
        if ($this->approvalRubrik == 'approved') $approvedCount++;

        return $approvedCount;
    }

    public function getStatusAttribute()
    {
        $approvedCount = $this->approval_status;

        if ($approvedCount == 0) {
            return 'Belum';
        } elseif ($approvedCount < 4) {
            return 'Progres';
        } else {
            return 'Selesai';
        }
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja', 'id');
    }

    public function sertifikat()
    {
        return $this->hasMany(Sertifikat::class, 'id_post', 'id');
    }

    public function rtm()
    {
        return $this->hasMany(RTM::class, 'id_post', 'id');
    }
}
