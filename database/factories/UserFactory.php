<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_terverifikasi_pada' => now(),
            'kata_sandi' => bcrypt('password'),
            'token_ingat_saya' => Str::random(10),
        ];
    }
}
