<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'nip',
        'password',
        'username',
        'id_level',
        'id_unit_kerja',
        'menu_config',
        'profile_picture',
        'is_approved',
    ];

    public function Level()
    {
        return $this->belongsTo(Level::class, 'id_level');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function comment_prs()
    {
        return $this->hasMany(CommentPr::class);
    }
    public function ketuaPenelaah(){
        return $this->hasOne(KetuaPenelaah::class, 'id', 'id_ketua');
    }
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja', 'id');
    }
    public function sertifikat()
    {
        return $this->hasMany(Sertifikat::class, 'id_user', 'id');
    }
    // public function menus(){
    //     return $this->belongsToMany(Menu::class, 'user_menu','user_id','menu_id');
    // }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
