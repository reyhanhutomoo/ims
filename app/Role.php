<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Tabel Bahasa Indonesia
    protected $table = 'peran';

    public $timestamps = true;

    // Kolom Bahasa Indonesia
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    /**
     * Relasi ke Pengguna melalui pivot 'peran_pengguna'
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'peran_pengguna', 'peran_id', 'pengguna_id');
    }
}