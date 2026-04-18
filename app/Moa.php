<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Moa extends Model
{
    use HasFactory, SoftDeletes;

    // Tabel Bahasa Indonesia
    protected $table = 'pengajuan_moa';

    // Kolom yang dapat diisi (Bahasa Indonesia)
    protected $fillable = [
        'pengguna_id',
        'nomor_pelacakan',
        'judul',
        'jenis_dokumen',
        'path_berkas',
        'path_berkas_ttd',
        'status',
        'catatan_admin',
    ];

    /**
     * Pemetaan status Inggris -> DB (Bahasa Indonesia) dan sebaliknya
     */
    protected static $statusMapToDb = [
        'pending'   => 'menunggu',
        'reviewed'  => 'direview',
        'approved'  => 'disetujui',
        'rejected'  => 'ditolak',
    ];

    protected static $statusMapToApp = [
        'menunggu'  => 'pending',
        'direview'  => 'reviewed',
        'disetujui' => 'approved',
        'ditolak'   => 'rejected',
    ];

    /**
     * Helper publik untuk memetakan nilai status dari request (Inggris) ke enum DB (Indonesia)
     */
    public static function mapStatusToDb($value)
    {
        if (!is_string($value)) {
            return null;
        }
        $lower = strtolower($value);
        if (isset(self::$statusMapToDb[$lower])) {
            return self::$statusMapToDb[$lower];
        }
        if (isset(self::$statusMapToApp[$lower])) {
            // Sudah dalam Bahasa Indonesia
            return $lower;
        }
        return null;
    }

    /**
     * Helper publik untuk memetakan nilai status dari DB (Indonesia) ke aplikasi (Inggris)
     */
    public static function mapStatusToApp($value)
    {
        if (!is_string($value)) {
            return null;
        }
        $lower = strtolower($value);
        return self::$statusMapToApp[$lower] ?? null;
    }

    /**
     * Mutator: normalisasi nilai status sebelum disimpan ke DB (enum Bahasa Indonesia)
     */
    public function setStatusAttribute($value)
    {
        if (!is_string($value)) {
            $this->attributes['status'] = 'menunggu';
            return;
        }

        $lower = strtolower($value);

        if (isset(self::$statusMapToDb[$lower])) {
            $this->attributes['status'] = self::$statusMapToDb[$lower];
        } elseif (isset(self::$statusMapToApp[$lower])) {
            // Sudah dalam Bahasa Indonesia, simpan apa adanya
            $this->attributes['status'] = $lower;
        } else {
            // Fallback ke default enum
            $this->attributes['status'] = 'menunggu';
        }
    }

    /**
     * Accessor: kembalikan status versi aplikasi (Inggris) untuk konsistensi di view/kode
     */
    public function getStatusAttribute($value)
    {
        $lower = is_string($value) ? strtolower($value) : '';
        return self::$statusMapToApp[$lower] ?? $value;
    }

    /**
     * Relasi ke Pengguna (mahasiswa) yang membuat pengajuan ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'pengguna_id', 'id');
    }
}
