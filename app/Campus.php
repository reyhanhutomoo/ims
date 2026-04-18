<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use HasFactory, SoftDeletes;

    // Tabel Bahasa Indonesia
    protected $table = 'kampus';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    // Kolom yang dapat diisi (Bahasa Indonesia)
    protected $fillable = [
        'nama',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'telepon',
        'aktif',
    ];
}