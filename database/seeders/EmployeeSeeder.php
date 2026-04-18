<?php

namespace Database\Seeders;

use App\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employee = [
            [
                'pengguna_id' => 2,
                'nama' => 'Alfarizy',
                'usia' => 20,
                'kampus_id' => 1,
                'divisi_id' => 1,
                'tanggal_mulai' => '2024-06-09',
                'tanggal_selesai' => '2024-07-09',
            ],
        ];
        foreach($employee as $key => $value){
            Employee::create($value);
        }
    }
}