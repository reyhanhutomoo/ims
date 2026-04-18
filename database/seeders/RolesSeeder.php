<?php

namespace Database\Seeders;
use App\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Gunakan kolom Bahasa Indonesia 'nama'
        \App\Role::updateOrCreate(['nama' => 'admin'], ['deskripsi' => null]);
        \App\Role::updateOrCreate(['nama' => 'employee'], ['deskripsi' => null]);
    }
}