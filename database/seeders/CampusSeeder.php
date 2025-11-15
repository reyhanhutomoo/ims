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
                'name' => 'Universitas Indonesia',
            ],[
                'name' => 'Institute Technology Bandung',
            ],[
                'name' => 'Universitas Negeri Semarang',
            ]
        ];
        foreach($campus as $key => $value){
            Campus::create($value);
        }
    }
}