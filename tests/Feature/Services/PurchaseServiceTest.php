<?php

use App\Models\Employee;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Services\PurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rolls back purchase and stock movements when stock operation fails', function () {
    $supplier = Supplier::factory()->create();

    $employee = Employee::factory()->create();

    $product = Product::factory()->create([
        'current_quantity' => 10,
    ]);

    $service = app(PurchaseService::class);

    $purchase = $service->create(
        supplierId: $supplier->id,
        employeeId: $employee->id,
        items: [
            [
                'product_id' => $product->id,
                'quantity' => 5,
                'unit_price' => 100,
            ],
        ],
    );

    expect($purchase)
        ->toBeInstanceOf(Purchase::class);

    expect(
        $product->fresh()->current_quantity
    )->toBe(15);

    expect(
        StockMovement::query()->count()
    )->toBe(1);
});