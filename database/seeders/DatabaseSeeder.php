<?php

use Database\Seeders\BulkUsersSeeder;
use Database\Seeders\CampusSeeder;
use Database\Seeders\CurateCampusDivisionAndEmployeesSeeder;
use Database\Seeders\DivisionSeeder;
use Database\Seeders\DummyWeeklyReportsAndLeavesSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\MoaSeeder;
use Database\Seeders\MoaYear2021Seeder;
use Database\Seeders\MoaYears2022To2026Seeder;
use Database\Seeders\Roles;
use Database\Seeders\RolesSeeder;
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
        // Seed minimal data required for login
        $this->call(RolesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(UsersRoleSeeder::class);

        // Enable all seeders for complete database population
        $this->call(DivisionSeeder::class);
        $this->call(CampusSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(MoaSeeder::class);
        $this->call(MoaYear2021Seeder::class);
        $this->call(MoaYears2022To2026Seeder::class);
        $this->call(CurateCampusDivisionAndEmployeesSeeder::class);
        $this->call(DummyWeeklyReportsAndLeavesSeeder::class);
        $this->call(BulkUsersSeeder::class);
    }
}