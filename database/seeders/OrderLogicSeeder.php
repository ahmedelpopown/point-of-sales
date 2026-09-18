<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;
use RuntimeException;

class OrderLogicSeeder extends Seeder
{
    public function run(): void
    {
        $employee = Employee::first();

        $user = User::first();

        $products = Product::query()
            ->where('status', 'active')
            ->take(3)
            ->get();

        if (!$employee) {
            throw new RuntimeException(
                'No employee found.'
            );
        }

        if ($products->count() < 3) {
            throw new RuntimeException(
                'At least 3 products are required.'
            );
        }

        $service = app(OrderService::class);

        /*
        |--------------------------------------------------------------------------
        | Save Stock
        |--------------------------------------------------------------------------
        */

        $stock = [];

        foreach ($products as $product) {
            $stock[$product->id] =
                (int) $product->current_quantity;
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        $order = $service->create(
            userId: $user?->id,
            employeeId: $employee->id,
            items: [
                [
                    'product_id' => $products[0]->id,
                    'quantity' => 2,
                    'price' => 100,
                ],
                [
                    'product_id' => $products[1]->id,
                    'quantity' => 3,
                    'price' => 200,
                ],
                [
                    'product_id' => $products[2]->id,
                    'quantity' => 1,
                    'price' => 500,
                ],
            ],
        );

        /*
        Expected:
        2 * 100 = 200
        3 * 200 = 600
        1 * 500 = 500

        Total = 1300
        */

        if ((float) $order->total !== 1300.0) {
            throw new RuntimeException(
                'CREATE TEST FAILED: Total is incorrect.'
            );
        }

        if ($order->orderItems()->count() !== 3) {
            throw new RuntimeException(
                'CREATE TEST FAILED: Items count is incorrect.'
            );
        }

        foreach ($products as $product) {
            $product->refresh();
        }

        if (
            $products[0]->current_quantity
            != $stock[$products[0]->id] - 2
        ) {
            throw new RuntimeException(
                'CREATE TEST FAILED: Product 1 stock.'
            );
        }

        if (
            $products[1]->current_quantity
            != $stock[$products[1]->id] - 3
        ) {
            throw new RuntimeException(
                'CREATE TEST FAILED: Product 2 stock.'
            );
        }

        if (
            $products[2]->current_quantity
            != $stock[$products[2]->id] - 1
        ) {
            throw new RuntimeException(
                'CREATE TEST FAILED: Product 3 stock.'
            );
        }

        $this->command->info(
            'CREATE ORDER TEST PASSED ✅'
        );

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $service->update(
            order: $order,
            userId: $user?->id,
            employeeId: $employee->id,
            items: [
                [
                    'product_id' => $products[0]->id,
                    'quantity' => 1,
                    'price' => 150,
                ],
                [
                    'product_id' => $products[1]->id,
                    'quantity' => 2,
                    'price' => 250,
                ],
                [
                    'product_id' => $products[2]->id,
                    'quantity' => 2,
                    'price' => 300,
                ],
            ],
        );

        foreach ($products as $product) {
            $product->refresh();
        }

        $order->refresh();

        /*
        New total:
        1 * 150 = 150
        2 * 250 = 500
        2 * 300 = 600

        Total = 1250
        */

        if ((float) $order->total !== 1250.0) {
            throw new RuntimeException(
                'UPDATE TEST FAILED: Total is incorrect.'
            );
        }

        if (
            $products[0]->current_quantity
            != $stock[$products[0]->id] - 1
        ) {
            throw new RuntimeException(
                'UPDATE TEST FAILED: Product 1 stock.'
            );
        }

        if (
            $products[1]->current_quantity
            != $stock[$products[1]->id] - 2
        ) {
            throw new RuntimeException(
                'UPDATE TEST FAILED: Product 2 stock.'
            );
        }

        if (
            $products[2]->current_quantity
            != $stock[$products[2]->id] - 2
        ) {
            throw new RuntimeException(
                'UPDATE TEST FAILED: Product 3 stock.'
            );
        }

        $this->command->info(
            'UPDATE ORDER TEST PASSED ✅'
        );

        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        $service->delete($order);

        foreach ($products as $product) {
            $product->refresh();
        }

        if (
            $products[0]->current_quantity
            != $stock[$products[0]->id]
        ) {
            throw new RuntimeException(
                'DELETE TEST FAILED: Product 1 stock.'
            );
        }

        if (
            $products[1]->current_quantity
            != $stock[$products[1]->id]
        ) {
            throw new RuntimeException(
                'DELETE TEST FAILED: Product 2 stock.'
            );
        }

        if (
            $products[2]->current_quantity
            != $stock[$products[2]->id]
        ) {
            throw new RuntimeException(
                'DELETE TEST FAILED: Product 3 stock.'
            );
        }

        $this->command->info(
            'DELETE ORDER TEST PASSED ✅'
        );

        $this->command->info(
            '================================'
        );

        $this->command->info(
            'ALL ORDER TESTS PASSED ✅'
        );

        $this->command->info(
            '================================'
        );
    }
}