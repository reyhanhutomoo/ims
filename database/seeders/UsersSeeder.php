<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'nama' => 'Admin',
                'email' => 'admin@gmail.com',
                'kata_sandi' => bcrypt('abcd1234'),
                'email_terverifikasi_pada' => now(),
                'token_ingat_saya' => Str::random(10),
            ],
            [
                'id' => 2,
                'nama' => 'Alfarizy',
                'email' => 'alfarizy@gmail.com',
                'kata_sandi' => bcrypt('abcd1234'),
                'email_terverifikasi_pada' => now(),
                'token_ingat_saya' => Str::random(10),
            ],
        ];

        foreach ($users as $value) {
            User::updateOrCreate(['email' => $value['email']], $value);
        }
    }
}