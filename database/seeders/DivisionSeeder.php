<?php

namespace Database\Seeders;

use App\Division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $division = [
            [
                'nama' => 'Technology',
                'deskripsi' => 'Divisi teknologi dan pengembangan sistem',
                'aktif' => true,
            ],[
                'nama' => 'Media',
                'deskripsi' => 'Divisi media dan komunikasi',
                'aktif' => true,
            ],[
                'nama' => 'Production',
                'deskripsi' => 'Divisi produksi dan operasional',
                'aktif' => true,
            ]
        ];
        foreach($division as $key => $value){
            Division::create($value);
        }
    }
}