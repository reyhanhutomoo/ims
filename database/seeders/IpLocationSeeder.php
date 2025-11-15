<?php

namespace Database\Seeders;

use App\StatusAtten;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IpLocationSeeder extends Seeder
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
                'location' => 'Jl. Trunojoyo No.3, RT.5/RW.2, Selong, Kec. Kby. Baru, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12110',
            ]
        ];
        foreach($statusatten as $key => $value){
            StatusAtten::create($value);
        }
    }
}