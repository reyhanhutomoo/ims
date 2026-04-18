<?php

namespace Database\Seeders;

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
        // Hubungkan pengguna dengan peran menggunakan relasi pivot 'peran_pengguna'
        $admin = \App\User::where('email', 'admin@gmail.com')->first();
        $roleAdmin = \App\Role::where('nama', 'admin')->first();

        if ($admin && $roleAdmin) {
            $admin->peran()->syncWithoutDetaching([$roleAdmin->id]);
        }

        $employee = \App\User::where('email', 'alfarizy@gmail.com')->first();
        $roleEmployee = \App\Role::where('nama', 'employee')->first();

        if ($employee && $roleEmployee) {
            $employee->peran()->syncWithoutDetaching([$roleEmployee->id]);
        }
    }
}