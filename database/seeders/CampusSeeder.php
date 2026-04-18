<?php

namespace Database\Seeders;

use App\Campus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campus = [
            [
                'nama' => 'Universitas Indonesia',
                'alamat' => 'Depok, Jawa Barat',
                'kota' => 'Depok',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '16424',
                'telepon' => '021-7867222',
                'aktif' => true,
            ],[
                'nama' => 'Institute Technology Bandung',
                'alamat' => 'Jl. Ganesha No.10, Bandung',
                'kota' => 'Bandung',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '40132',
                'telepon' => '022-2500935',
                'aktif' => true,
            ],[
                'nama' => 'Universitas Negeri Semarang',
                'alamat' => 'Sekaran, Gunungpati, Semarang',
                'kota' => 'Semarang',
                'provinsi' => 'Jawa Tengah',
                'kode_pos' => '50229',
                'telepon' => '024-8508015',
                'aktif' => true,
            ]
        ];
        foreach($campus as $key => $value){
            Campus::create($value);
        }
    }
}