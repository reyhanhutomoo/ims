<?php

namespace Database\Factories;

use App\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->unique()->randomElement(['admin', 'employee', 'manager', 'supervisor']),
        ];
    }
}