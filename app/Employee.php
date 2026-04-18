<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Gunakan tabel Bahasa Indonesia
    protected $table = 'karyawan';
    protected $primaryKey = 'id';

    // Kolom tanggal yang digunakan
    protected $dates = ['created_at', 'updated_at', 'tanggal_mulai', 'tanggal_selesai'];

    // Kolom yang dapat diisi (Bahasa Indonesia)
    protected $fillable = [
        'pengguna_id',
        'nama',
        'usia',
        'kampus_id',
        'divisi_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'nomor_telepon',
        'alamat',
        'foto',
        'status',
    ];
    
    // Relasi ke pengguna (FK: pengguna_id)
    public function user() {
        return $this->belongsTo('App\User', 'pengguna_id', 'id');
    }

    // Relasi ke kampus (FK: kampus_id)
    public function campus() {
        return $this->belongsTo('App\Campus', 'kampus_id', 'id');
    }

    // Relasi ke divisi (FK: divisi_id)
    public function division() {
        return $this->belongsTo('App\Division', 'divisi_id', 'id');
    }
    
    // Relasi ke kehadiran (FK di anak: karyawan_id)
    public function attendance() {
        return $this->hasMany('App\Attendance', 'karyawan_id', 'id');
    }
    
    // Relasi ke laporan mingguan (FK di anak: karyawan_id)
    public function weeklyreports() {
        return $this->hasMany('App\WeeklyReports', 'karyawan_id', 'id');
    }

    // Relasi ke cuti (FK di anak: karyawan_id)
    public function leave() {
        return $this->hasMany('App\Leave', 'karyawan_id', 'id');
    }

    // Relasi ke expense (FK di anak: karyawan_id) - jika model tersedia
    public function expense() {
        return $this->hasMany('App\Expense', 'karyawan_id', 'id');
    }
}