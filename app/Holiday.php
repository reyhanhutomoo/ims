<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasFactory, SoftDeletes;
    
    // Tabel Bahasa Indonesia
    protected $table = 'hari_libur';

    // Kolom tanggal
    protected $dates = ['created_at', 'updated_at', 'tanggal_mulai', 'tanggal_selesai'];

    // Kolom yang dapat diisi (Bahasa Indonesia)
    protected $fillable = [
        'nama',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis',
        'berulang_tahunan',
    ];

    // Casting tipe data
    protected $casts = [
        'berulang_tahunan' => 'boolean',
    ];
}