<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallmentService
{
    /**
     * Create installment for an order.
     */
    public function create(
        Order $order,
        InstallmentPlan $plan,
        float $downPayment,
        string $startDate
    ): Installment {
        return DB::transaction(function () use (
            $order,
            $plan,
            $downPayment,
            $startDate
        ) {

            if ($plan->months_count <= 0) {
                throw new RuntimeException(
                    'Installment plan must contain at least one month.'
                );
            }

            if ($plan->interest_rate < 0) {
                throw new RuntimeException(
                    'Interest rate cannot be negative.'
                );
            }

            $existing = Installment::where(
                'order_id',
                $order->id
            )->exists();

            if ($existing) {
                throw new RuntimeException(
                    'This order already has an installment.'
                );
            }

            $orderTotal = (float) $order->total;

            /*
             * Simple interest calculation.
             *
             * Example:
             * Order = 10000
             * Interest = 10%
             *
             * Interest amount = 1000
             * Total = 11000
             */
            $interestAmount =
                $orderTotal * ((float) $plan->interest_rate / 100);

            $totalWithInterest =
                $orderTotal + $interestAmount;

            if ($downPayment < 0) {
                throw new RuntimeException(
                    'Down payment cannot be negative.'
                );
            }

            if ($downPayment > $totalWithInterest) {
                throw new RuntimeException(
                    'Down payment cannot be greater than total amount.'
                );
            }

            $remaining =
                $totalWithInterest - $downPayment;

            return Installment::create([
                'order_id' => $order->id,
                'installment_plan_id' => $plan->id,
                'total_with_interest' => $totalWithInterest,
                'down_payment' => $downPayment,
                'remaining_amount' => $remaining,
                'start_date' => $startDate,
            ]);
        });
    }

    /**
     * Record payment.
     */
    public function addPayment(
        Installment $installment,
        float $amount,
        string $method,
        string $paymentDate
    ): Payment {
        return DB::transaction(function () use (
            $installment,
            $amount,
            $method,
            $paymentDate
        ) {

            $installment = Installment::query()
                ->lockForUpdate()
                ->findOrFail($installment->id);

            if ($amount <= 0) {
                throw new RuntimeException(
                    'Payment amount must be greater than zero.'
                );
            }

            if ($amount > $installment->remaining_amount) {
                throw new RuntimeException(
                    'Payment cannot be greater than remaining amount.'
                );
            }

            $payment = Payment::create([
                'installment_id' => $installment->id,
                'amount' => $amount,
                'method' => $method,
                'payment_date' => $paymentDate,
                'order_id' => $installment->order_id,
                'installment_plan_id' => $installment->installment_plan_id,
            ]);

            $installment->decrement(
                'remaining_amount',
                $amount
            );

            return $payment;
        });
    }

    /**
     * Update installment.
     */
    public function update(
        Installment $installment,
        InstallmentPlan $plan,
        float $downPayment,
        string $startDate
    ): Installment {

        return DB::transaction(function () use (
            $installment,
            $plan,
            $downPayment,
            $startDate
        ) {

            $installment = Installment::query()
                ->lockForUpdate()
                ->findOrFail($installment->id);

            if ($installment->payments()->exists()) {
                throw new RuntimeException(
                    'You cannot modify an installment that already has payments.'
                );
            }

            if ($plan->months_count <= 0) {
                throw new RuntimeException(
                    'Installment plan must contain at least one month.'
                );
            }

            $orderTotal =
                (float) $installment->order->total;

            $interestAmount =
                $orderTotal * ((float) $plan->interest_rate / 100);

            $totalWithInterest =
                $orderTotal + $interestAmount;

            if ($downPayment > $totalWithInterest) {
                throw new RuntimeException(
                    'Down payment cannot be greater than total.'
                );
            }

            $remaining =
                $totalWithInterest - $downPayment;

            $installment->update([
                'installment_plan_id' => $plan->id,
                'total_with_interest' => $totalWithInterest,
                'down_payment' => $downPayment,
                'remaining_amount' => $remaining,
                'start_date' => $startDate,
            ]);

            return $installment->fresh();
        });
    }

    /**
     * Delete installment.
     */
    public function delete(Installment $installment): void
    {
        DB::transaction(function () use ($installment) {

            if ($installment->payments()->exists()) {
                throw new RuntimeException(
                    'You cannot delete an installment that has payments.'
                );
            }

            $installment->delete();
        });
    }

    /**
     * Calculate monthly amount.
     */
    public function monthlyAmount(
        Installment $installment
    ): float {
        $months =
            $installment->installmentPlan->months_count;

        if ($months <= 0) {
            return 0;
        }

        return round(
            (float) $installment->remaining_amount / $months,
            2
        );
    }
}