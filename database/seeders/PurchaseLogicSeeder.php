<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Database\Seeder;
use RuntimeException;

class PurchaseLogicSeeder extends Seeder
{
    public function run(): void
    {
        $supplier = Supplier::first();
        $employee = Employee::first();

        $products = Product::take(2)->get();

        if (!$supplier) {
            throw new RuntimeException(
                'No supplier found.'
            );
        }

        if (!$employee) {
            throw new RuntimeException(
                'No employee found.'
            );
        }

        if ($products->count() < 2) {
            throw new RuntimeException(
                'At least 2 products are required.'
            );
        }

        $product1 = $products[0];
        $product2 = $products[1];

        $beforeProduct1 = (int) $product1->current_quantity;
        $beforeProduct2 = (int) $product2->current_quantity;

        $service = app(PurchaseService::class);

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        $purchase = $service->create(
            supplierId: $supplier->id,
            employeeId: $employee->id,
            items: [
                [
                    'product_id' => $product1->id,
                    'quantity' => 5,
                    'unit_price' => 100,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 3,
                    'unit_price' => 200,
                ],
            ],
        );

        $product1->refresh();
        $product2->refresh();

        $expectedTotal = 5 * 100 + 3 * 200;

        if ((float) $purchase->total_price !== (float) $expectedTotal) {
            throw new RuntimeException(
                'CREATE FAILED: Purchase total is incorrect.'
            );
        }

        if (
            (int) $product1->current_quantity
            !== $beforeProduct1 + 5
        ) {
            throw new RuntimeException(
                'CREATE FAILED: Product 1 stock was not increased correctly.'
            );
        }

        if (
            (int) $product2->current_quantity
            !== $beforeProduct2 + 3
        ) {
            throw new RuntimeException(
                'CREATE FAILED: Product 2 stock was not increased correctly.'
            );
        }

        $this->command->info(
            'CREATE TEST PASSED'
        );

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $purchase = $service->update(
            purchase: $purchase,
            supplierId: $supplier->id,
            employeeId: $employee->id,
            items: [
                [
                    'product_id' => $product1->id,
                    'quantity' => 2,
                    'unit_price' => 150,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 4,
                    'unit_price' => 250,
                ],
            ],
        );

        $product1->refresh();
        $product2->refresh();

        $expectedUpdatedTotal = (2 * 150) + (4 * 250);

        if (
            (float) $purchase->total_price
            !== (float) $expectedUpdatedTotal
        ) {
            throw new RuntimeException(
                'UPDATE FAILED: Purchase total is incorrect.'
            );
        }

        if (
            (int) $product1->current_quantity
            !== $beforeProduct1 + 2
        ) {
            throw new RuntimeException(
                'UPDATE FAILED: Product 1 stock is incorrect.'
            );
        }

        if (
            (int) $product2->current_quantity
            !== $beforeProduct2 + 4
        ) {
            throw new RuntimeException(
                'UPDATE FAILED: Product 2 stock is incorrect.'
            );
        }

        $this->command->info(
            'UPDATE TEST PASSED'
        );

        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        $service->delete($purchase);

        $product1->refresh();
        $product2->refresh();

        if (
            (int) $product1->current_quantity
            !== $beforeProduct1
        ) {
            throw new RuntimeException(
                'DELETE FAILED: Product 1 stock did not return to original value.'
            );
        }

        if (
            (int) $product2->current_quantity
            !== $beforeProduct2
        ) {
            throw new RuntimeException(
                'DELETE FAILED: Product 2 stock did not return to original value.'
            );
        }

        $this->command->info(
            'DELETE TEST PASSED'
        );

        $this->command->info(
            '--------------------------------------'
        );

        $this->command->info(
            'ALL PURCHASE LOGIC TESTS PASSED ✅'
        );

        $this->command->info(
            '--------------------------------------'
        );
    }
}