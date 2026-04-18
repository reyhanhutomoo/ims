<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;

class BulkUsersSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat beberapa akun pengguna.
     */
    public function run(): void
    {
        $users = [
            ['nama' => 'Agus Santoso',       'email' => 'agus.santoso@example.com',       'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Budi Hartono',       'email' => 'budi.hartono@example.com',       'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Citra Dewi',         'email' => 'citra.dewi@example.com',         'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Dwi Ananda',         'email' => 'dwi.ananda@example.com',         'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Eko Prasetyo',       'email' => 'eko.prasetyo@example.com',       'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Faisal Ramadhan',    'email' => 'faisal.ramadhan@example.com',    'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Galih Saputra',      'email' => 'galih.saputra@example.com',      'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Hani Putri',         'email' => 'hani.putri@example.com',         'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Indra Kusuma',       'email' => 'indra.kusuma@example.com',       'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Joko Susilo',        'email' => 'joko.susilo@example.com',        'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Kiki Amelia',        'email' => 'kiki.amelia@example.com',        'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Lestari Wulandari',  'email' => 'lestari.wulandari@example.com',  'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Maya Sari',          'email' => 'maya.sari@example.com',          'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Nanda Permata',      'email' => 'nanda.permata@example.com',      'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Omar Hidayat',       'email' => 'omar.hidayat@example.com',       'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Putra Mahendra',     'email' => 'putra.mahendra@example.com',     'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Qori Annisa',        'email' => 'qori.annisa@example.com',        'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Rina Kurnia',        'email' => 'rina.kurnia@example.com',        'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Sigit Nugroho',      'email' => 'sigit.nugroho@example.com',      'kata_sandi' => bcrypt('abcd1234')],
            ['nama' => 'Tia Maharani',       'email' => 'tia.maharani@example.com',       'kata_sandi' => bcrypt('abcd1234')],
        ];

        foreach ($users as $value) {
            // Menggunakan updateOrCreate untuk idempoten: jika email sudah ada, update nama/kata_sandi
            User::updateOrCreate(['email' => $value['email']], $value);
        }
    }
}
