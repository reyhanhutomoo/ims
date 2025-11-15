<?php

namespace Database\Seeders;

use App\StatusAtten;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusAttenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statusatten = [
            [
                'ip' => '127.0.0.1',
                'location' => 'Desa Wlahar Kec. Adipala, Dusun Silangsur Desa Wlahar, Wlahar, Cilacap, Jawa Tengah, Jawa, 53271, Indonesia',
            ],
        ];
        foreach($statusatten as $key => $value){
            StatusAtten::create($value);
        }
    }
}