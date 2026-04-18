<?php

namespace Database\Factories;

use App\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

class DivisionFactory extends Factory
{
    protected $model = Division::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->randomElement([
                'IT Department',
                'Human Resources',
                'Marketing',
                'Finance',
                'Operations',
                'Sales',
                'Customer Service'
            ]),
        ];
    }
}