<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
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

        $warehouses = Warehouse::query()
            ->where('status', 'active')
            ->get();

        $shops = Shop::query()
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

        if ($warehouses->isEmpty()) {
            throw new \RuntimeException(
                'No active warehouses found.'
            );
        }

        if ($shops->isEmpty()) {
            throw new \RuntimeException(
                'No active shops found.'
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
        | Purchases are created against warehouses.
        | The PurchaseService is responsible for updating stock.
        |
        */

        $purchaseCount = 80;
        $createdPurchases = 0;

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
                    * fake()->randomFloat(
                        2,
                        0.55,
                        0.80
                    ),
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
                warehouseId: $warehouses->random()->id,
                items: $items,
            );

            /*
             * Put purchase somewhere in the last 90 days.
             */
            $purchaseDate = Carbon::now()
                ->subDays(
                    fake()->numberBetween(0, 89)
                )
                ->setTime(
                    fake()->numberBetween(8, 20),
                    fake()->numberBetween(0, 59)
                );

            $purchase->forceFill([
                'created_at' => $purchaseDate,
                'updated_at' => $purchaseDate,
            ])->saveQuietly();

            $createdPurchases++;
        }

        $this->command->info(
            "{$createdPurchases} purchases created."
        );

        /*
        |--------------------------------------------------------------------------
        | 2. ORDERS
        |--------------------------------------------------------------------------
        |
        | Orders belong to shops.
        |
        | IMPORTANT:
        | We only select products that have available stock
        | in the selected shop.
        |
        */

        $orderCount = 200;
        $createdOrders = 0;

        for ($i = 1; $i <= $orderCount; $i++) {

            /*
             * Every order is assigned to one shop.
             */
            $shop = $shops->random();

            /*
             * Get products that actually have stock
             * in this specific shop.
             */
            $availableProducts = Product::query()
                ->where('status', 'active')
                ->whereHas('stocks', function ($query) use ($shop) {
                    $query
                        ->where(
                            'stockable_type',
                            Shop::class
                        )
                        ->where(
                            'stockable_id',
                            $shop->id
                        )
                        ->where(
                            'quantity',
                            '>',
                            0
                        );
                })
                ->with([
                    'stocks' => function ($query) use ($shop) {
                        $query
                            ->where(
                                'stockable_type',
                                Shop::class
                            )
                            ->where(
                                'stockable_id',
                                $shop->id
                            );
                    },
                ])
                ->inRandomOrder()
                ->get();

            /*
             * Nothing available in this shop.
             */
            if ($availableProducts->isEmpty()) {
                continue;
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

                /*
                 * This is the stock of THIS product
                 * in THIS selected shop.
                 */
                $stock = $product->stocks->first();

                if (! $stock || $stock->quantity < 1) {
                    continue;
                }

                /*
                 * Never request more than the actual
                 * quantity available in the shop.
                 */
                $quantity = fake()->numberBetween(
                    1,
                    min(8, $stock->quantity)
                );

                $items[] = [
                    'product_id' => $product->id,
                    'shop_id' => $shop->id,
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
                items: $items,
            );

            /*
             * Historical date.
             */
            $orderDate = Carbon::now()
                ->subDays(
                    fake()->numberBetween(0, 89)
                )
                ->setTime(
                    fake()->numberBetween(9, 21),
                    fake()->numberBetween(0, 59)
                );

            $order->forceFill([
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ])->saveQuietly();

            $createdOrders++;
        }

        $this->command->info(
            "{$createdOrders} orders created."
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
                * fake()->randomFloat(
                    2,
                    0.10,
                    0.30
                ),
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
                     * Payment between 10% and 35%
                     * of the current remaining amount.
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
                        min(
                            $amount,
                            $remaining
                        )
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
            "Purchases: {$createdPurchases}"
        );

        $this->command->info(
            "Orders: {$createdOrders}"
        );

        $this->command->info(
            "Installments: {$installmentCount}"
        );

        $this->command->info(
            "Payments: {$paymentCount}"
        );

        $this->command->info(
            '=========================================='
        );
    }
}