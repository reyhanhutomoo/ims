<?php

namespace App;

use App\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Tabel Bahasa Indonesia
    protected $table = 'pengguna';

    // Kolom Bahasa Indonesia
    protected $fillable = [
        'nama', 'email', 'kata_sandi',
    ];

    // Sembunyikan kolom sensitif
    protected $hidden = [
        'kata_sandi', 'token_ingat_saya',
    ];

    // Casting kolom tanggal verifikasi email
    protected $casts = [
        'email_terverifikasi_pada' => 'datetime',
    ];

    /**
     * Override untuk autentikasi password default Laravel (kolom 'kata_sandi')
     */
    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    /**
     * Override nama kolom remember token (menjadi 'token_ingat_saya')
     */
    public function getRememberTokenName()
    {
        return 'token_ingat_saya';
    }

    /**
     * Relasi ke Peran (pivot: peran_pengguna)
     */
    public function peran()
    {
        return $this->belongsToMany(Role::class, 'peran_pengguna', 'pengguna_id', 'peran_id');
    }

    public function hasAnyRoles($roles)
    {
        return (bool) $this->peran()->whereIn('nama', $roles)->first();
    }

    public function hasRole($role)
    {
        return (bool) $this->peran()->where('nama', $role)->first();
    }

    /**
     * Relasi ke Karyawan (FK: pengguna_id)
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'pengguna_id', 'id');
    }

    /**
     * Relasi ke Pengajuan MOA (FK: pengguna_id)
     */
    public function moas()
    {
        return $this->hasMany(Moa::class, 'pengguna_id', 'id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}