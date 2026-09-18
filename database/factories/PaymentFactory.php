<?php

namespace Database\Factories;

use App\Models\Installment;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $installment = Installment::query()
            ->inRandomOrder()
            ->first();

        if (!$installment) {
            throw new \RuntimeException(
                'No installment exists. Create installments first.'
            );
        }

        $remaining = (float) $installment->remaining_amount;

        return [
            'installment_id' => $installment->id,

            'amount' => $remaining > 0
                ? min(
                    $remaining,
                    fake()->randomFloat(2, 50, 500)
                )
                : 0,

            'method' => fake()->randomElement([
                'cash',
                'card',
                'bank_transfer',
            ]),

            'payment_date' => fake()->date(
                'Y-m-d',
                'now'
            ),

            'order_id' => $installment->order_id,

            'installment_plan_id' =>
                $installment->installment_plan_id,
        ];
    }
}