<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    // Tabel Bahasa Indonesia
    protected $table = 'kehadiran';
    protected $primaryKey = 'id';

    // Kolom yang dapat diisi sesuai migrasi baru
    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'waktu_masuk',
        'waktu_keluar',
        'ip_masuk',
        'lokasi_masuk',
        'ip_keluar',
        'lokasi_keluar',
        'status_masuk',
        'status_keluar',
        'laporan_harian',
    ];

    /**
     * Relasi ke Karyawan (FK: karyawan_id)
     */
    public function employee()
    {
        return $this->belongsTo('App\Employee', 'karyawan_id', 'id');
    }
}