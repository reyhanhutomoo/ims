<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Moa;
use App\User;
use Illuminate\Support\Facades\Storage;

class MoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get first user or create one if doesn't exist
        $user = User::first();
        
        if (!$user) {
            echo "No users found. Please create users first.\n";
            return;
        }

        $moas = [
            [
                'pengguna_id' => $user->id,
                'judul' => 'Universitas Indonesia - Kerja Sama Magang',
                'jenis_dokumen' => 'MOA',
                'path_berkas' => 'moa/dummy_moa_ui.pdf',
                'path_berkas_ttd' => null,
                'status' => 'pending',
                'catatan_admin' => null,
                'nomor_pelacakan' => 'MOA-' . date('Ymd') . '-001',
            ],
            [
                'pengguna_id' => $user->id,
                'judul' => 'Universitas Gadjah Mada - Program Internship',
                'jenis_dokumen' => 'IA',
                'path_berkas' => 'moa/dummy_ia_ugm.pdf',
                'path_berkas_ttd' => 'moa/signed_ia_ugm.pdf',
                'status' => 'approved',
                'catatan_admin' => 'Dokumen telah disetujui dan ditandatangani',
                'nomor_pelacakan' => 'IA-' . date('Ymd') . '-001',
            ],
            [
                'pengguna_id' => $user->id,
                'judul' => 'Institut Teknologi Bandung - Kolaborasi Riset',
                'jenis_dokumen' => 'MOA',
                'path_berkas' => 'moa/dummy_moa_itb.pdf',
                'path_berkas_ttd' => null,
                'status' => 'reviewed',
                'catatan_admin' => 'Sedang dalam proses review oleh pihak terkait',
                'nomor_pelacakan' => 'MOA-' . date('Ymd') . '-002',
            ],
            [
                'pengguna_id' => $user->id,
                'judul' => 'Universitas Airlangga - Kerjasama Pendidikan',
                'jenis_dokumen' => 'IA',
                'path_berkas' => 'moa/dummy_ia_unair.pdf',
                'path_berkas_ttd' => null,
                'status' => 'rejected',
                'catatan_admin' => 'Dokumen perlu revisi pada bagian durasi dan tanggung jawab',
                'nomor_pelacakan' => 'IA-' . date('Ymd') . '-002',
            ],
            [
                'pengguna_id' => $user->id,
                'judul' => 'Universitas Brawijaya - Program Magang Mahasiswa',
                'jenis_dokumen' => 'MOA',
                'path_berkas' => 'moa/dummy_moa_ub.pdf',
                'path_berkas_ttd' => 'moa/signed_moa_ub.pdf',
                'status' => 'approved',
                'catatan_admin' => 'Disetujui dan siap dilaksanakan',
                'nomor_pelacakan' => 'MOA-' . date('Ymd') . '-003',
            ],
        ];

        foreach ($moas as $moaData) {
            Moa::create($moaData);
        }

        echo "MoA seeder completed! Created " . count($moas) . " records.\n";
    }
}