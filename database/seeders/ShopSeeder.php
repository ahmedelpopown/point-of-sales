<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        Shop::create([
            'name' => 'Main Branch',
            'address' => 'Downtown, Cairo',
            'phone' => '01100000001',
            'status' => 'active',
        ]);

        Shop::create([
            'name' => 'Giza Branch',
            'address' => 'Faisal, Giza',
            'phone' => '01100000002',
            'status' => 'active',
        ]);

        Shop::create([
            'name' => 'Nasr City Branch',
            'address' => 'Nasr City, Cairo',
            'phone' => '01100000003',
            'status' => 'active',
        ]);

        Shop::create([
            'name' => 'Old Branch',
            'address' => 'Shubra, Cairo',
            'phone' => '01100000004',
            'status' => 'inactive',
        ]);
    }
}