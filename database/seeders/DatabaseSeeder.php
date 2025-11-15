<?php

use Database\Seeders\CampusSeeder;
use Database\Seeders\DivisionSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\IpLocationSeeder;
use Database\Seeders\Roles;
use Database\Seeders\RolesSeeder;
use Database\Seeders\StatusAttenSeeder;
use Database\Seeders\UsersRoleSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(UsersRoleSeeder::class);
        $this->call(DivisionSeeder::class);
        $this->call(CampusSeeder::class);
        $this->call(EmployeeSeeder::class);
    }
}