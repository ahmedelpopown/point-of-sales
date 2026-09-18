<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $employees = Employee::all();
        $products = Product::all();

        if ($suppliers->isEmpty()) {
            throw new \RuntimeException(
                'No suppliers found. Run SupplierSeeder first.'
            );
        }

        if ($employees->isEmpty()) {
            throw new \RuntimeException(
                'No employees found. Run EmployeeSeeder first.'
            );
        }

        if ($products->count() < 5) {
            throw new \RuntimeException(
                'You need at least 5 products before running PurchaseSeeder.'
            );
        }

        $purchaseService = app(PurchaseService::class);

        for ($i = 1; $i <= 10; $i++) {

            $randomProducts = $products
                ->random(rand(1, min(5, $products->count())));

            $items = $randomProducts->map(function ($product) {

                $quantity = rand(1, 10);

                $unitPrice = rand(50, 500);

                return [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];
            })->values()->toArray();

            $purchase = $purchaseService->create(
                supplierId: $suppliers->random()->id,
                employeeId: $employees->random()->id,
                items: $items,
            );

            $this->command->info(
                "Purchase #{$purchase->id} created | "
                . "Items: {$purchase->purchaseItems()->count()} | "
                . "Total: {$purchase->total_price}"
            );
        }
    }
}