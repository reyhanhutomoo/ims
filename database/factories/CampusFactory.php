<?php

namespace Database\Factories;

use App\Campus;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampusFactory extends Factory
{
    protected $model = Campus::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->company . ' Campus',
        ];
    }
}