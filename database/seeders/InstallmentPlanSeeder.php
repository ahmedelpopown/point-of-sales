<?php

namespace Database\Seeders;

use App\Models\InstallmentPlan;
use Illuminate\Database\Seeder;

class InstallmentPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => '3 Months',
                'months_count' => 3,
                'interest_rate' => 5,
            ],

            [
                'name' => '6 Months',
                'months_count' => 6,
                'interest_rate' => 10,
            ],

            [
                'name' => '9 Months',
                'months_count' => 9,
                'interest_rate' => 12,
            ],

            [
                'name' => '12 Months',
                'months_count' => 12,
                'interest_rate' => 15,
            ],
        ];

        foreach ($plans as $plan) {

            InstallmentPlan::updateOrCreate(
                [
                    'name' => $plan['name'],
                ],
                $plan
            );
        }
    }
}