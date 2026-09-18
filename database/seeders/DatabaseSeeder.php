<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

    public function run(): void
    {
        $this->call([

            /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */
            GovernorateSeeder::class,
            CitySeeder::class,

             RolesAndPermissionsSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | Main Data
            |--------------------------------------------------------------------------
            */
            UserSeeder::class,
            EmployeeSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | Installment Plans
            |--------------------------------------------------------------------------
            */
            InstallmentPlanSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | Reports Demo
            |--------------------------------------------------------------------------
            */
            ReportDemoSeeder::class,



        ]);
    }
}