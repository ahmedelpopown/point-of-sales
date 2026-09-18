<?php

namespace Database\Seeders;

use App\Models\InstallmentPlan;
use App\Models\Order;
use App\Services\InstallmentService;
use Illuminate\Database\Seeder;
use RuntimeException;

class InstallmentLogicSeeder extends Seeder
{
    public function run(): void
    {
        $order = Order::first();

        $plan = InstallmentPlan::where(
            'months_count',
            6
        )->first();

        if (!$order) {
            throw new RuntimeException(
                'No order found.'
            );
        }

        if (!$plan) {
            throw new RuntimeException(
                'Installment plan not found.'
            );
        }

        $service = app(InstallmentService::class);

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        $orderTotal = (float) $order->total;

        $interest =
            $orderTotal
            * ((float) $plan->interest_rate / 100);

        $expectedTotal =
            $orderTotal + $interest;

        $downPayment = 100;

        $expectedRemaining =
            $expectedTotal - $downPayment;

        $installment = $service->create(
            order: $order,
            plan: $plan,
            downPayment: $downPayment,
            startDate: now()->format('Y-m-d'),
        );

        if (
            (float) $installment->total_with_interest
            !== round($expectedTotal, 2)
        ) {
            throw new RuntimeException(
                'Installment total calculation failed.'
            );
        }

        if (
            (float) $installment->remaining_amount
            !== round($expectedRemaining, 2)
        ) {
            throw new RuntimeException(
                'Remaining amount calculation failed.'
            );
        }

        $this->command->info(
            'CREATE INSTALLMENT TEST PASSED ✅'
        );

        /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

        $paymentAmount = 50;

        $service->addPayment(
            installment: $installment,
            amount: $paymentAmount,
            method: 'cash',
            paymentDate: now()->format('Y-m-d'),
        );

        $installment->refresh();

        $expectedAfterPayment =
            $expectedRemaining - $paymentAmount;

        if (
            (float) $installment->remaining_amount
            !== round($expectedAfterPayment, 2)
        ) {
            throw new RuntimeException(
                'Payment remaining calculation failed.'
            );
        }

        if (
            $installment->payments()->count() !== 1
        ) {
            throw new RuntimeException(
                'Payment was not created.'
            );
        }

        $this->command->info(
            'PAYMENT TEST PASSED ✅'
        );

        /*
        |--------------------------------------------------------------------------
        | OVER PAYMENT TEST
        |--------------------------------------------------------------------------
        */

        try {

            $service->addPayment(
                installment: $installment,
                amount: $installment->remaining_amount + 1,
                method: 'cash',
                paymentDate: now()->format('Y-m-d'),
            );

            throw new RuntimeException(
                'Overpayment validation FAILED.'
            );

        } catch (RuntimeException $e) {

            if (
                !str_contains(
                    $e->getMessage(),
                    'greater than remaining'
                )
            ) {
                throw $e;
            }

        }

        $this->command->info(
            'OVER PAYMENT TEST PASSED ✅'
        );

        /*
        |--------------------------------------------------------------------------
        | RESULT
        |--------------------------------------------------------------------------
        */

        $this->command->info(
            '================================'
        );

        $this->command->info(
            'ALL INSTALLMENT TESTS PASSED ✅'
        );

        $this->command->info(
            '================================'
        );
    }
}