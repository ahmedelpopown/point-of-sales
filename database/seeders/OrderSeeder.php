<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;
use RuntimeException;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $employee = Employee::first();

        $user = User::first();

        $products = Product::query()
            ->where('status', 'active')
            ->withSum('stocks as current_quantity', 'quantity')
            ->get();

        if (!$employee) {
            throw new RuntimeException(
                'No employee found.'
            );
        }

        if ($products->count() < 3) {
            throw new RuntimeException(
                'At least 3 products with stock are required.'
            );
        }

        $service = app(OrderService::class);

        for ($i = 1; $i <= 10; $i++) {

            $availableProducts = Product::query()
                ->where('status', 'active')
                ->withSum('stocks as current_quantity', 'quantity')
                ->get();

            if ($availableProducts->isEmpty()) {
                break;
            }

            $selectedProducts = $availableProducts
                ->random(
                    min(
                        rand(1, 3),
                        $availableProducts->count()
                    )
                );

            $items = [];

            foreach ($selectedProducts as $product) {

                $quantity = rand(
                    1,
                    min(
                        3,
                        (int) $product->current_quantity
                    )
                );

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => rand(100, 1000),
                ];
            }

            $order = $service->create(
                userId: $user?->id,
                employeeId: $employee->id,
                items: $items,
            );

            $this->command->info(
                "Order #{$order->id} created | "
                . "Items: {$order->orderItems()->count()} | "
                . "Total: {$order->total}"
            );
        }
    }
}