<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Campus;
use App\Division;
use App\Employee;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CurateCampusDivisionAndEmployeesSeeder extends Seeder
{
    /**
     * Rombak data: bersihkan kampus tidak jelas, siapkan kampus & divisi dummy sesuai daftar,
     * dan buat karyawan usia 20–23 dengan pengguna unik per karyawan.
     */
    public function run(): void
    {
        // Daftar kampus & divisi yang diizinkan
        $allowedCampuses = [
            'Universitas Sebelas Maret',
            'Institut Teknologi Bandung',
            'Institut Pertanian Bogor',
            'Bina Nusantara University',
            'London School of Public Relation',
        ];

        $allowedDivisions = [
            'Jatinter',
            'Taud',
            'Renmin',
            'Baglotas',
            'Konferin',
        ];

        // Upsert kampus
        foreach ($allowedCampuses as $name) {
            Campus::updateOrCreate(
                ['nama' => $name],
                ['aktif' => true]
            );
        }
        $campusMap = Campus::whereIn('nama', $allowedCampuses)->pluck('id', 'nama');

        // Hapus kampus yang tidak termasuk daftar (soft delete)
        Campus::whereNotIn('nama', $allowedCampuses)->delete();

        // Upsert divisi
        foreach ($allowedDivisions as $name) {
            Division::updateOrCreate(
                ['nama' => $name],
                ['aktif' => true]
            );
        }
        $divisionMap = Division::whereIn('nama', $allowedDivisions)->pluck('id', 'nama');

        // Hapus divisi yang tidak termasuk daftar (soft delete)
        Division::whereNotIn('nama', $allowedDivisions)->delete();

        // Hapus karyawan yang asal kampusnya tidak jelas (NULL atau bukan kampus diizinkan)
        $allowedCampusIds = $campusMap->values()->toArray();
        Employee::whereNull('kampus_id')
            ->orWhereNotIn('kampus_id', $allowedCampusIds)
            ->delete();

        // Buat karyawan baru terdistribusi (3 per kombinasi kampus-divisi), usia 20–23
        $namesPool = [
            'Agus Santoso', 'Budi Hartono', 'Citra Dewi', 'Dwi Ananda', 'Eko Prasetyo',
            'Faisal Ramadhan', 'Galih Saputra', 'Hani Putri', 'Indra Kusuma', 'Joko Susilo',
            'Kiki Amelia', 'Lestari Wulandari', 'Maya Sari', 'Nanda Permata', 'Omar Hidayat',
            'Putra Mahendra', 'Qori Annisa', 'Rina Kurnia', 'Sigit Nugroho', 'Tia Maharani',
        ];
        $status = 'aktif';
        $startDate = Carbon::create(2025, 1, 1);

        foreach ($allowedCampuses as $campName) {
            $campId = $campusMap[$campName] ?? null;
            if (!$campId) { continue; }

            foreach ($allowedDivisions as $divName) {
                $divId = $divisionMap[$divName] ?? null;
                if (!$divId) { continue; }

                for ($n = 0; $n < 3; $n++) {
                    // Buat pengguna unik untuk setiap karyawan agar tidak melanggar unique(karyawan.pengguna_id)
                    $userId = DB::table('pengguna')->insertGetId([
                        'nama' => $namesPool[array_rand($namesPool)],
                        'email' => 'employee+' . Str::uuid() . '@example.com',
                        'kata_sandi' => bcrypt('abcd1234'),
                        'email_terverifikasi_pada' => now(),
                        'token_ingat_saya' => Str::random(10),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $age = rand(20, 23);
                    $namaKaryawan = $namesPool[array_rand($namesPool)];
 
                    Employee::create([
                        'pengguna_id'    => $userId,
                        'nama'           => $namaKaryawan,
                        'usia'           => $age,
                        'kampus_id'      => $campId,
                        'divisi_id'      => $divId,
                        'tanggal_mulai'  => $startDate->copy()->addDays(rand(0, 180))->toDateString(),
                        'tanggal_selesai'=> null,
                        'nomor_telepon'  => '08' . rand(100000000, 999999999),
                        'alamat'         => 'Alamat ' . $campName,
                        'foto'           => null,
                        'status'         => $status,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }
        }

        // Normalisasi usia untuk karyawan yang tersisa (jika ada yang di luar range)
        // Gunakan batch update per id agar nilai acak tidak sama untuk semua baris
        $outOfRange = Employee::where(function ($q) {
            $q->where('usia', '<', 20)->orWhere('usia', '>', 23)->orWhereNull('usia');
        })->pluck('id');

        foreach ($outOfRange as $empId) {
            Employee::where('id', $empId)->update(['usia' => rand(20, 23)]);
        }
    }
}
