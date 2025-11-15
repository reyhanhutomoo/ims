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
                'name' => 'Technology',
            ],[
                'name' => 'Media',
            ],[
                'name' => 'Production',
            ]
        ];
        foreach($division as $key => $value){
            Division::create($value);
        }
    }
}