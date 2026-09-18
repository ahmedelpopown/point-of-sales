<?php

use App\Enums\StockMovementType;
use App\Models\Employee;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('increases product stock and creates a purchase movement', function () {
    $product = Product::factory()->create([
        'current_quantity' => 50,
    ]);

    $employee = Employee::factory()->create();

    $movement = app(InventoryService::class)->increase(
        productId: $product->id,
        quantity: 20,
        type: StockMovementType::PURCHASE,
        employeeId: $employee->id,
        note: 'Initial purchase',
    );

    expect($movement)->toBeInstanceOf(StockMovement::class)
        ->and($movement->type)->toBe(StockMovementType::PURCHASE)
        ->and($movement->quantity)->toBe(20)
        ->and($movement->quantity_before)->toBe(50)
        ->and($movement->quantity_after)->toBe(70);

    expect(
        $product->fresh()->current_quantity
    )->toBe(70);
});

it('decreases product stock and creates a sale movement', function () {
    $product = Product::factory()->create([
        'current_quantity' => 50,
    ]);

    $movement = app(InventoryService::class)->decrease(
        productId: $product->id,
        quantity: 15,
        type: StockMovementType::SALE,
    );

    expect($movement->type)
        ->toBe(StockMovementType::SALE);

    expect($movement->quantity)
        ->toBe(15);

    expect($movement->quantity_before)
        ->toBe(50);

    expect($movement->quantity_after)
        ->toBe(35);

    expect(
        $product->fresh()->current_quantity
    )->toBe(35);
});

it('does not allow stock to become negative', function () {
    $product = Product::factory()->create([
        'current_quantity' => 5,
    ]);

    expect(
        fn () => app(InventoryService::class)->decrease(
            productId: $product->id,
            quantity: 6,
            type: StockMovementType::SALE,
        )
    )->toThrow(RuntimeException::class);

    expect(
        $product->fresh()->current_quantity
    )->toBe(5);

    expect(
        StockMovement::query()->count()
    )->toBe(0);
});

it('creates adjustment in when new quantity is greater than current quantity', function () {
    $product = Product::factory()->create([
        'current_quantity' => 100,
    ]);

    $movement = app(InventoryService::class)->adjust(
        productId: $product->id,
        newQuantity: 125,
    );

    expect($movement)
        ->not->toBeNull();

    expect($movement->type)
        ->toBe(StockMovementType::ADJUSTMENT_IN);

    expect($movement->quantity)
        ->toBe(25);

    expect($movement->quantity_before)
        ->toBe(100);

    expect($movement->quantity_after)
        ->toBe(125);

    expect(
        $product->fresh()->current_quantity
    )->toBe(125);
});

it('creates adjustment out when new quantity is less than current quantity', function () {
    $product = Product::factory()->create([
        'current_quantity' => 100,
    ]);

    $movement = app(InventoryService::class)->adjust(
        productId: $product->id,
        newQuantity: 80,
    );

    expect($movement)
        ->not->toBeNull();

    expect($movement->type)
        ->toBe(StockMovementType::ADJUSTMENT_OUT);

    expect($movement->quantity)
        ->toBe(20);

    expect($movement->quantity_before)
        ->toBe(100);

    expect($movement->quantity_after)
        ->toBe(80);
});

it('does not create movement when adjustment does not change stock', function () {
    $product = Product::factory()->create([
        'current_quantity' => 100,
    ]);

    $movement = app(InventoryService::class)->adjust(
        productId: $product->id,
        newQuantity: 100,
    );

    expect($movement)->toBeNull();

    expect(
        StockMovement::query()->count()
    )->toBe(0);
});

it('does not allow negative adjustment quantity', function () {
    $product = Product::factory()->create([
        'current_quantity' => 100,
    ]);

    expect(
        fn () => app(InventoryService::class)->adjust(
            productId: $product->id,
            newQuantity: -1,
        )
    )->toThrow(InvalidArgumentException::class);
});

it('does not allow an incompatible movement type for increase', function () {
    $product = Product::factory()->create([
        'current_quantity' => 100,
    ]);

    expect(
        fn () => app(InventoryService::class)->increase(
            productId: $product->id,
            quantity: 10,
            type: StockMovementType::SALE,
        )
    )->toThrow(InvalidArgumentException::class);

    expect(
        $product->fresh()->current_quantity
    )->toBe(100);
});

it('does not allow an incompatible movement type for decrease', function () {
    $product = Product::factory()->create([
        'current_quantity' => 100,
    ]);

    expect(
        fn () => app(InventoryService::class)->decrease(
            productId: $product->id,
            quantity: 10,
            type: StockMovementType::PURCHASE,
        )
    )->toThrow(InvalidArgumentException::class);

    expect(
        $product->fresh()->current_quantity
    )->toBe(100);
});