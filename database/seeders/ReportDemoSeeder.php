<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InstallmentService;
use App\Services\OrderService;
use App\Services\PurchaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ReportDemoSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Get Base Data
        |--------------------------------------------------------------------------
        */

        $users = User::query()->get();

        $employees = Employee::query()
            ->where('status', 'active')
            ->get();

        $suppliers = Supplier::query()->get();

        $products = Product::query()
            ->where('status', 'active')
            ->get();

        $plans = InstallmentPlan::query()->get();

        if ($users->isEmpty()) {
            throw new \RuntimeException(
                'No users found.'
            );
        }

        if ($employees->isEmpty()) {
            throw new \RuntimeException(
                'No active employees found.'
            );
        }

        if ($suppliers->isEmpty()) {
            throw new \RuntimeException(
                'No suppliers found.'
            );
        }

        if ($products->count() < 10) {
            throw new \RuntimeException(
                'At least 10 products are required.'
            );
        }

        if ($plans->isEmpty()) {
            throw new \RuntimeException(
                'No installment plans found.'
            );
        }

        $purchaseService = app(
            PurchaseService::class
        );

        $orderService = app(
            OrderService::class
        );

        $installmentService = app(
            InstallmentService::class
        );

        /*
        |--------------------------------------------------------------------------
        | 1. PURCHASES
        |--------------------------------------------------------------------------
        |
        | Generate purchases first.
        | This gives products realistic purchase costs.
        |
        */

        $purchaseCount = 80;

        for ($i = 1; $i <= $purchaseCount; $i++) {

            $selectedProducts = Product::query()
                ->where('status', 'active')
                ->inRandomOrder()
                ->limit(fake()->numberBetween(2, 5))
                ->get();

            $items = [];

            foreach ($selectedProducts as $product) {

                $quantity = fake()->numberBetween(
                    20,
                    100
                );

                /*
                 * Purchase cost is lower than selling price.
                 */
                $unitPrice = round(
                    (float) $product->price
                    * fake()->randomFloat(2, 0.55, 0.80),
                    2
                );

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];
            }

            $purchase = $purchaseService->create(
                supplierId: $suppliers->random()->id,
                employeeId: $employees->random()->id,
                warehouseId:$warehouseId->random()->id,
                items: $items,
            );

            /*
             * Put purchase somewhere in the last 90 days.
             */
            $purchaseDate = Carbon::now()
                ->subDays(fake()->numberBetween(0, 89))
                ->setTime(
                    fake()->numberBetween(8, 20),
                    fake()->numberBetween(0, 59)
                );

            $purchase->forceFill([
                'created_at' => $purchaseDate,
                'updated_at' => $purchaseDate,
            ])->saveQuietly();
        }

        $this->command->info(
            "{$purchaseCount} purchases created."
        );

        /*
        |--------------------------------------------------------------------------
        | 2. ORDERS
        |--------------------------------------------------------------------------
        */

        $orderCount = 200;

        for ($i = 1; $i <= $orderCount; $i++) {

            $availableProducts = Product::query()
                ->where('status', 'active')
                ->withSum('stocks as current_quantity', 'quantity')
                ->inRandomOrder()
                ->get();

            if ($availableProducts->isEmpty()) {
                break;
            }

            $selectedProducts = $availableProducts
                ->take(
                    min(
                        fake()->numberBetween(2, 5),
                        $availableProducts->count()
                    )
                );

            $items = [];

            foreach ($selectedProducts as $product) {

                $maxQuantity = min(
                    8,
                    (int) $product->current_quantity
                );

                if ($maxQuantity < 1) {
                    continue;
                }

                $quantity = fake()->numberBetween(
                    1,
                    $maxQuantity
                );

                /*
                 * Product price is the selling price.
                 */
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => (float) $product->price,
                ];
            }

            if (empty($items)) {
                continue;
            }

            $employee = $employees->random();

            $customer = $users->random();

            $order = $orderService->create(
                userId: $customer->id,
                employeeId: $employee->id,
                warehouseId: $warehouse->id,
                items: $items,
            );

            /*
             * Historical date.
             */
            $orderDate = Carbon::now()
                ->subDays(fake()->numberBetween(0, 89))
                ->setTime(
                    fake()->numberBetween(9, 21),
                    fake()->numberBetween(0, 59)
                );

            $order->forceFill([
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ])->saveQuietly();
        }

        $this->command->info(
            "{$orderCount} orders created."
        );

        /*
        |--------------------------------------------------------------------------
        | 3. INSTALLMENTS
        |--------------------------------------------------------------------------
        */

        $orders = \App\Models\Order::query()
            ->with('installments')
            ->inRandomOrder()
            ->limit(50)
            ->get();

        $installmentCount = 0;

        foreach ($orders as $order) {

            if ($order->installments->isNotEmpty()) {
                continue;
            }

            $plan = $plans->random();

            $orderTotal = (float) $order->total;

            /*
             * Down payment between 10% and 30%.
             */
            $downPayment = round(
                $orderTotal
                * fake()->randomFloat(2, 0.10, 0.30),
                2
            );

            $startDate = Carbon::parse(
                $order->created_at
            )->format('Y-m-d');

            $installment = $installmentService->create(
                order: $order,
                plan: $plan,
                downPayment: $downPayment,
                startDate: $startDate,
            );

            $installmentCount++;
        }

        $this->command->info(
            "{$installmentCount} installments created."
        );

        /*
        |--------------------------------------------------------------------------
        | 4. PAYMENTS
        |--------------------------------------------------------------------------
        */

        $installments = Installment::query()
            ->where('remaining_amount', '>', 0)
            ->get();

        $paymentCount = 0;

        foreach ($installments as $installment) {

            /*
             * 60% of installments get payments.
             */
            if (fake()->boolean(60)) {

                $paymentsToCreate =
                    fake()->numberBetween(1, 3);

                for (
                    $i = 0;
                    $i < $paymentsToCreate;
                    $i++
                ) {

                    $installment->refresh();

                    $remaining =
                        (float) $installment->remaining_amount;

                    if ($remaining <= 0) {
                        break;
                    }

                    /*
                     * Payment between 10% and 35% of
                     * the current remaining amount.
                     */
                    $percentage =
                        fake()->randomFloat(
                            2,
                            0.10,
                            0.35
                        );

                    $amount = round(
                        $remaining * $percentage,
                        2
                    );

                    $amount = max(
                        1,
                        min($amount, $remaining)
                    );

                    /*
                     * Payment date between installment
                     * start date and today.
                     */
                    $startDate = Carbon::parse(
                        $installment->start_date
                    );

                    $paymentDate = $startDate
                        ->copy()
                        ->addDays(
                            fake()->numberBetween(
                                1,
                                max(
                                    1,
                                    $startDate->diffInDays(
                                        now()
                                    )
                                )
                            )
                        );

                    if (
                        $paymentDate->greaterThan(now())
                    ) {
                        $paymentDate = now();
                    }

                    $installmentService->addPayment(
                        installment: $installment,
                        amount: $amount,
                        method: fake()->randomElement([
                            'cash',
                            'card',
                            'bank_transfer',
                        ]),
                        paymentDate:
                            $paymentDate->format('Y-m-d'),
                    );

                    $paymentCount++;
                }
            }
        }

        $this->command->info(
            "{$paymentCount} payments created."
        );

        /*
        |--------------------------------------------------------------------------
        | 5. Summary
        |--------------------------------------------------------------------------
        */

        $this->command->info(
            '=========================================='
        );

        $this->command->info(
            'REPORT DEMO DATA CREATED SUCCESSFULLY ✅'
        );

        $this->command->info(
            '=========================================='
        );
    }
}