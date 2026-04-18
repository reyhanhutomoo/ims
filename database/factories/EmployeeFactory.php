<?php

namespace Database\Factories;

use App\Employee;
use App\User;
use App\Campus;
use App\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        $startDate = $this->faker->date();
        
        return [
            'pengguna_id' => User::factory(),
            'nama' => $this->faker->name,
            'usia' => $this->faker->numberBetween(18, 60),
            'kampus_id' => Campus::factory(),
            'divisi_id' => Division::factory(),
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $this->faker->dateTimeBetween($startDate, '+2 years')->format('Y-m-d'),
        ];
    }
}