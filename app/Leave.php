<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use HasFactory, SoftDeletes;

    // Tabel Bahasa Indonesia
    protected $table = 'cuti';

    // Kolom tanggal
    protected $dates = ['created_at', 'updated_at', 'tanggal_mulai', 'tanggal_selesai', 'tanggal_disetujui'];

    // Kolom yang dapat diisi (Bahasa Indonesia)
    protected $fillable = [
        'karyawan_id',
        'jenis_cuti',
        'alasan',
        'deskripsi',
        'bukti',
        'setengah_hari',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_persetujuan',
    ];

    // Casting
    protected $casts = [
        'setengah_hari' => 'boolean',
    ];

    /**
     * Relasi ke Karyawan (FK: karyawan_id)
     */
    public function employee() {
        return $this->belongsTo('App\Employee', 'karyawan_id', 'id');
    }

    /**
     * Relasi ke Pengguna yang menyetujui (FK: disetujui_oleh)
     */
    public function approver() {
        return $this->belongsTo('App\User', 'disetujui_oleh', 'id');
    }
}