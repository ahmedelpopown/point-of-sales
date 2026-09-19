<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::create([
            'name' => 'Main Warehouse',
            'address' => 'Industrial Zone, Cairo',
            'phone' => '01000000001',
            'status' => 'active',
        ]);

        Warehouse::create([
            'name' => 'Cairo Warehouse',
            'address' => 'Nasr City, Cairo',
            'phone' => '01000000002',
            'status' => 'active',
        ]);

        Warehouse::create([
            'name' => 'Giza Warehouse',
            'address' => 'Dokki, Giza',
            'phone' => '01000000003',
            'status' => 'active',
        ]);

        Warehouse::create([
            'name' => 'Old Warehouse',
            'address' => 'Helwan, Cairo',
            'phone' => '01000000004',
            'status' => 'inactive',
        ]);
    }
}