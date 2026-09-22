<?php

namespace App\Livewire\Forms;

use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Order;
use App\Services\InstallmentService;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InstallmentForm extends Form
{
    
    public ?Installment $installment = null;

    #[Validate('required|exists:orders,id')]
    public $order_id = '';

    #[Validate('required|exists:installment_plans,id')]
    public $installment_plan_id = '';

    #[Validate('required|numeric|min:0')]
    public $down_payment = 0;

    #[Validate('required|date')]
    public $start_date = '';

    /*
    |--------------------------------------------------------------------------
    | Set Existing Installment
    |--------------------------------------------------------------------------
    */

    public function setInstallment(Installment $installment): void
    {
        $this->installment = $installment;

        $this->order_id = $installment->order_id;

        $this->installment_plan_id =
            $installment->installment_plan_id;

        $this->down_payment =
            $installment->down_payment;

        $this->start_date =
            $installment->start_date?->format('Y-m-d');
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate Preview
    |--------------------------------------------------------------------------
    */

    public function preview(): array
    {
        if (
            !$this->order_id ||
            !$this->installment_plan_id
        ) {
            return [
                'order_total' => 0,
                'interest_amount' => 0,
                'total_with_interest' => 0,
                'down_payment' => (float) $this->down_payment,
                'remaining_amount' => 0,
                'monthly_amount' => 0,
            ];
        }

        $order = Order::find($this->order_id);

        $plan = InstallmentPlan::find(
            $this->installment_plan_id
        );

        if (!$order || !$plan) {
            return [
                'order_total' => 0,
                'interest_amount' => 0,
                'total_with_interest' => 0,
                'down_payment' => (float) $this->down_payment,
                'remaining_amount' => 0,
                'monthly_amount' => 0,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Order Total
        |--------------------------------------------------------------------------
        */

        $orderTotal = (float) $order->total;

        /*
        |--------------------------------------------------------------------------
        | Interest
        |--------------------------------------------------------------------------
        */

        $interestAmount =
            $orderTotal
            * ((float) $plan->interest_rate / 100);

        /*
        |--------------------------------------------------------------------------
        | Total With Interest
        |--------------------------------------------------------------------------
        */

        $totalWithInterest =
            $orderTotal + $interestAmount;

        /*
        |--------------------------------------------------------------------------
        | Remaining
        |--------------------------------------------------------------------------
        */

        $downPayment = max(
            0,
            (float) $this->down_payment
        );

        $remainingAmount =
            max(
                0,
                $totalWithInterest - $downPayment
            );

        /*
        |--------------------------------------------------------------------------
        | Monthly
        |--------------------------------------------------------------------------
        */

        $monthlyAmount =
            $plan->months_count > 0
                ? $remainingAmount / $plan->months_count
                : 0;

        return [
            'order_total' => round($orderTotal, 2),
            'interest_amount' => round($interestAmount, 2),
            'total_with_interest' => round($totalWithInterest, 2),
            'down_payment' => round($downPayment, 2),
            'remaining_amount' => round($remainingAmount, 2),
            'monthly_amount' => round($monthlyAmount, 2),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(): Installment
    {
        $this->validate();

        $order = Order::findOrFail(
            $this->order_id
        );

        $plan = InstallmentPlan::findOrFail(
            $this->installment_plan_id
        );

        return app(InstallmentService::class)->create(
            order: $order,
            plan: $plan,
            downPayment: (float) $this->down_payment,
            startDate: $this->start_date,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(): Installment
    {
        $this->validate();

        $plan = InstallmentPlan::findOrFail(
            $this->installment_plan_id
        );

        return app(InstallmentService::class)->update(
            installment: $this->installment,
            plan: $plan,
            downPayment: (float) $this->down_payment,
            startDate: $this->start_date,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    public function resetForm(): void
    {
        $this->reset([
            'installment',
            'order_id',
            'installment_plan_id',
            'down_payment',
            'start_date',
        ]);
    }
}
