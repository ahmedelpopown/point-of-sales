<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\InstallmentPlan;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\InstallmentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $rows = $rows
            ->map(fn ($row) => $row instanceof Collection
                ? $row->toArray()
                : $row
            )
            ->filter(fn (array $row) => collect($row)->filter()->isNotEmpty())
            ->groupBy('order_reference');

        foreach ($rows as $orderRows) {
            $this->createOrder(
                $orderRows->values()->all()
            );
        }
    }

    protected function createOrder(array $rows): void
    {
        $first = $rows[0];

        DB::transaction(function () use ($rows, $first) {

            $this->validateOrderData($first);

            $employee = Employee::findOrFail(
                $first['employee_id']
            );

            $customer = null;

            if (! empty($first['customer_id'])) {
                $customer = User::findOrFail(
                    $first['customer_id']
                );
            }

            $total = collect($rows)->sum(
                fn (array $row) =>
                    (float) $row['quantity']
                    * (float) $row['price']
            );

            $order = new Order();

            $order->user_id = $customer?->id;
            $order->employee_id = $employee->id;
            $order->total = $total;

            $order->save();

            foreach ($rows as $row) {

                $product = Product::query()
                    ->where('status', 'active')
                    ->findOrFail(
                        $row['product_id']
                    );

                $shop = Shop::query()
                    ->where('status', 'active')
                    ->findOrFail(
                        $row['shop_id']
                    );

                $quantity = (int) $row['quantity'];
                $price = (float) $row['price'];

                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'shop_id' => $shop->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price,
                ]);
            }

            if (($first['payment_method'] ?? 'cash') === 'installment') {
                $this->createInstallment(
                    $order,
                    $first
                );
            }
        });
    }

    protected function validateOrderData(array $row): void
    {
        $errors = [];

        if (empty($row['employee_id'])) {
            $errors[] = 'employee_id is required.';
        }

        if (empty($row['shop_id'])) {
            $errors[] = 'shop_id is required.';
        }

        if (empty($row['product_id'])) {
            $errors[] = 'product_id is required.';
        }

        if (
            ! isset($row['quantity']) ||
            ! is_numeric($row['quantity']) ||
            $row['quantity'] < 1
        ) {
            $errors[] = 'quantity must be at least 1.';
        }

        if (
            ! isset($row['price']) ||
            ! is_numeric($row['price']) ||
            $row['price'] < 0
        ) {
            $errors[] = 'price must be zero or greater.';
        }

        if ($errors) {
            throw ValidationException::withMessages([
                'import' => $errors,
            ]);
        }
    }

    protected function createInstallment(
        Order $order,
        array $row
    ): void {
        if (empty($row['installment_plan_id'])) {
            throw ValidationException::withMessages([
                'installment_plan_id' =>
                    'Installment plan is required for installment orders.',
            ]);
        }

        if (
            ! isset($row['down_payment']) ||
            $row['down_payment'] === ''
        ) {
            throw ValidationException::withMessages([
                'down_payment' =>
                    'Down payment is required for installment orders.',
            ]);
        }

        if (empty($row['start_date'])) {
            throw ValidationException::withMessages([
                'start_date' =>
                    'Start date is required for installment orders.',
            ]);
        }

        $plan = InstallmentPlan::findOrFail(
            $row['installment_plan_id']
        );

        app(InstallmentService::class)->create(
            order: $order,
            plan: $plan,
            downPayment: (float) $row['down_payment'],
            startDate: $row['start_date'],
        );
    }
}