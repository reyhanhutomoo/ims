<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
        $user = [
            [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('abcd1234')
            ], [
                'id' => 2,
                'name' => 'Alfarizy',
                'email' => 'alfarizy@gmail.com',
                'password' => bcrypt('abcd1234')
            ]
        ];
        foreach($user as $key => $value){
            User::create($value);
        }
    }
}