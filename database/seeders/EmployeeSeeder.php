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
                'user_id' => 2,
                'name' => 'Alfarizy',
                'age' => 20,
                'campus_id' => 1,
                'division_id' => 1,
                'start_date' => '2024-06-09',
                'end_date' => '2024-07-09',
            ],
        ];
        foreach($employee as $key => $value){
            Employee::create($value);
        }
    }
}