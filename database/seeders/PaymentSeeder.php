<?php

namespace Database\Seeders;

use App\Models\Installment;
use App\Services\InstallmentService;
use Illuminate\Database\Seeder;
use RuntimeException;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $installments = Installment::query()
            ->with('payments')
            ->get();

        if ($installments->isEmpty()) {
            throw new RuntimeException(
                'No installments found. Create installments before seeding payments.'
            );
        }

        $service = app(InstallmentService::class);

        $paymentCount = 0;

        foreach ($installments as $installment) {

            if ($paymentCount >= 10) {
                break;
            }

            $remaining = (float) $installment->remaining_amount;

            if ($remaining <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Create 1 or 2 payments for this installment
            |--------------------------------------------------------------------------
            */

            $numberOfPayments = fake()->numberBetween(1, 2);

            for ($i = 0; $i < $numberOfPayments; $i++) {

                if ($paymentCount >= 10) {
                    break;
                }

                $installment->refresh();

                $remaining = (float) $installment->remaining_amount;

                if ($remaining <= 0) {
                    break;
                }

                $amount = fake()->randomFloat(
                    2,
                    1,
                    max(1, min($remaining, 500))
                );

                $service->addPayment(
                    installment: $installment,
                    amount: $amount,
                    method: fake()->randomElement([
                        'cash',
                        'card',
                        'bank_transfer',
                    ]),
                    paymentDate: now()->format('Y-m-d'),
                );

                $paymentCount++;
            }
        }

        $this->command->info(
            "{$paymentCount} payments created successfully."
        );
    }
}
