<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GovernorateSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $governorates = [
            ['id' => 1, 'name' => 'القاهرة'],
            ['id' => 2, 'name' => 'الجيزة'],
            ['id' => 3, 'name' => 'الأسكندرية'],
            ['id' => 4, 'name' => 'الدقهلية'],
            ['id' => 5, 'name' => 'البحر الأحمر'],
            ['id' => 6, 'name' => 'البحيرة'],
            ['id' => 7, 'name' => 'الفيوم'],
            ['id' => 8, 'name' => 'الغربية'],
            ['id' => 9, 'name' => 'الإسماعلية'],
            ['id' => 10, 'name' => 'المنوفية'],
            ['id' => 11, 'name' => 'المنيا'],
            ['id' => 12, 'name' => 'القليوبية'],
            ['id' => 13, 'name' => 'الوادي الجديد'],
            ['id' => 14, 'name' => 'السويس'],
            ['id' => 15, 'name' => 'اسوان'],
            ['id' => 16, 'name' => 'اسيوط'],
            ['id' => 17, 'name' => 'بني سويف'],
            ['id' => 18, 'name' => 'بورسعيد'],
            ['id' => 19, 'name' => 'دمياط'],
            ['id' => 20, 'name' => 'الشرقية'],
            ['id' => 21, 'name' => 'جنوب سيناء'],
            ['id' => 22, 'name' => 'كفر الشيخ'],
            ['id' => 23, 'name' => 'مطروح'],
            ['id' => 24, 'name' => 'الأقصر'],
            ['id' => 25, 'name' => 'قنا'],
            ['id' => 26, 'name' => 'شمال سيناء'],
            ['id' => 27, 'name' => 'سوهاج'],
        ];

        DB::table('governorates')->insert($governorates);
    }
}
