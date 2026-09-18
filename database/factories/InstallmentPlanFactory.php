<?php

namespace Database\Factories;

use App\Models\InstallmentPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstallmentPlan>
 */
class InstallmentPlanFactory extends Factory
{
    public function definition(): array
    {
        return fake()->randomElement([
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
        ]);
    }
}