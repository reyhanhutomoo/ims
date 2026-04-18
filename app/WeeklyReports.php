<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyReports extends Model
{
    use HasFactory, SoftDeletes;

    // Tabel Bahasa Indonesia
    protected $table = 'laporan_mingguan';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    // Kolom tanggal
    protected $dates = ['created_at', 'updated_at', 'tanggal_review'];

    // Kolom yang dapat diisi (Bahasa Indonesia)
    protected $fillable = [
        'karyawan_id',
        'judul',
        'deskripsi',
        'file',
        'minggu_ke',
        'tahun',
        'nilai',
        'status',
        'direview_oleh',
        'tanggal_review',
        'catatan_review',
    ];

    /**
     * Relasi ke Karyawan (FK: karyawan_id)
     */
    public function employee() {
        return $this->belongsTo('App\Employee', 'karyawan_id', 'id');
    }

    /**
     * Relasi ke Reviewer (Pengguna yang mereview)
     */
    public function reviewer() {
        return $this->belongsTo('App\User', 'direview_oleh', 'id');
    }
}