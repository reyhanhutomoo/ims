<?php

namespace Database\Seeders;

use App\RoleUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleuser = [
            [
                'role_id' => 1,
                'user_id' => 1,
            ],
            [
                'role_id' => 2,
                'user_id' => 2,
            ],
        ];
        foreach($roleuser as $key => $value){
            RoleUser::create($value);
        }
    }
}