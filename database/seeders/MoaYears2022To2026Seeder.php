<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MoaYears2022To2026Seeder extends Seeder
{
    public function run(): void
    {
        // Ambil salah satu pengguna jika ada, fallback ke 1 (UsersSeeder biasanya membuat admin id=1)
        $userId = DB::table('pengguna')->value('id') ?? 1;

        // Nonaktifkan constraint FK sementara (hindari gagal insert jika data pengguna kosong di lingkungan dev)
        Schema::disableForeignKeyConstraints();

        // Seed data untuk tahun 2022 sampai 2026
        foreach (range(2022, 2026) as $year) {
            // Lewati jika data untuk tahun ini sudah ada agar tidak dobel
            $alreadySeeded = DB::table('pengajuan_moa')->whereYear('created_at', $year)->exists();
            if ($alreadySeeded) {
                continue;
            }

            $rows = [];

            for ($m = 1; $m <= 12; $m++) {
                // Jumlah dummy per bulan (bisa disesuaikan)
                $moaCount = 3; // 3 MOA per bulan
                $iaCount  = 2; // 2 IA per bulan

                for ($i = 1; $i <= $moaCount; $i++) {
                    $created = Carbon::create($year, $m, rand(1, 28), rand(8, 18), rand(0, 59));
                    $rows[] = [
                        'pengguna_id'     => $userId,
                        'nomor_pelacakan' => 'MOA-' . $year . '-' . str_pad((string)$m, 2, '0', STR_PAD_LEFT) . '-' . $i . '-' . (string) Str::uuid(),
                        'judul'           => 'Dummy MOA ' . Carbon::create($year, $m, 1)->translatedFormat('F') . ' ' . $year,
                        'jenis_dokumen'   => 'MOA',
                        'path_berkas'     => 'moa/dummy_' . $year . '_moa_' . $m . '_' . $i . '.pdf',
                        'path_berkas_ttd' => null,
                        'status'          => 'menunggu', // enum DB dalam bahasa Indonesia
                        'created_at'      => $created,
                        'updated_at'      => $created,
                    ];
                }

                for ($j = 1; $j <= $iaCount; $j++) {
                    $created = Carbon::create($year, $m, rand(1, 28), rand(8, 18), rand(0, 59));
                    $rows[] = [
                        'pengguna_id'     => $userId,
                        'nomor_pelacakan' => 'IA-' . $year . '-' . str_pad((string)$m, 2, '0', STR_PAD_LEFT) . '-' . $j . '-' . (string) Str::uuid(),
                        'judul'           => 'Dummy IA ' . Carbon::create($year, $m, 1)->translatedFormat('F') . ' ' . $year,
                        'jenis_dokumen'   => 'IA',
                        'path_berkas'     => 'moa/dummy_' . $year . '_ia_' . $m . '_' . $j . '.pdf',
                        'path_berkas_ttd' => null,
                        'status'          => 'menunggu',
                        'created_at'      => $created,
                        'updated_at'      => $created,
                    ];
                }
            }

            if (!empty($rows)) {
                DB::table('pengajuan_moa')->insert($rows);
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}
