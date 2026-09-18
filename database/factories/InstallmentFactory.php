<?php

namespace Database\Factories;

use App\Models\Installment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Installment>
 */
class InstallmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'total_with_interest' => 0,

            'down_payment' => 0,

            'remaining_amount' => 0,

            'start_date' => fake()->dateTimeBetween(
                '-90 days',
                'now'
            ),

            'installment_plan_id' => null,

            'order_id' => null,
        ];
    }
}