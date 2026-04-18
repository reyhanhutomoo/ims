<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use HasFactory, SoftDeletes;

    // Tabel Bahasa Indonesia
    protected $table = 'divisi';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    // Kolom yang dapat diisi (Bahasa Indonesia)
    protected $fillable = [
        'nama',
        'deskripsi',
        'aktif',
    ];

    /**
     * Relasi ke Karyawan (FK di anak: divisi_id)
     */
    public function employees() {
        return $this->hasMany('App\Employee', 'divisi_id', 'id');
    }
}