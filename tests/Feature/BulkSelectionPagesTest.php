<?php

use App\Livewire\RolesIndex;
use App\Models\Employee;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('all record listings with selection render successfully', function () {
    foreach ([
        'pages::products.index',
        'pages::users.index',
        'pages::employees.index',
        'pages::suppliers.index',
        'pages::orders.index',
        'pages::purchases.index',
        'pages::stock.index',
        'pages::stock.warehouses',
        'pages::stock.shops',
        'pages::stock.stock-movements',
        'pages::installments.index',
        RolesIndex::class,
    ] as $component) {
        Livewire::test($component)->assertOk();
    }
});

test('role selection uses the roles paginator without a destructive bulk action', function () {
    $role = Role::query()->create([
        'name' => 'bulk-selection-test',
        'guard_name' => 'employee',
    ]);

    Livewire::test(RolesIndex::class)
        ->set('selectAll', true)
        ->assertSet('selected', [$role->id])
        ->assertSet('selectAll', true)
        ->call('clearBulkSelection')
        ->assertSet('selected', [])
        ->assertSet('selectAll', false);
});

test('product selection stays page-scoped and clears when the result set changes', function () {
    $olderProduct = Product::factory()->create([
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2),
    ]);
    $middleProduct = Product::factory()->create([
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);
    $newestProduct = Product::factory()->create([
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $component = Livewire::test('pages::products.index')
        ->set('perPage', 2)
        ->set('selectAll', true)
        ->assertSet('selected', [
            $newestProduct->id,
            $middleProduct->id,
        ]);

    $component
        ->call('gotoPage', 2)
        ->assertSet('selected', [
            $newestProduct->id,
            $middleProduct->id,
        ])
        ->assertSet('selectAll', false);

    $component
        ->set('selectAll', true)
        ->assertSet('selected', [
            $newestProduct->id,
            $middleProduct->id,
            $olderProduct->id,
        ]);

    $component
        ->set('selectAll', false)
        ->assertSet('selected', [
            $newestProduct->id,
            $middleProduct->id,
        ]);

    $component
        ->set('search', 'no matching product')
        ->assertSet('selected', []);

    $component
        ->set('selected', [$olderProduct->id])
        ->call('sortBy', 'price')
        ->assertSet('selected', []);
});

test('product selected export and bulk deletion use the selected IDs', function () {
    $product = Product::factory()->create();

    Excel::fake();

    Livewire::test('pages::products.index')
        ->set('selected', [$product->id])
        ->call('exportSelectedExcel');

    Excel::assertDownloaded('selected-products.xlsx');

    Livewire::test('pages::products.index')
        ->set('selected', [$product->id])
        ->call('deleteSelected')
        ->assertSet('selected', [])
        ->assertSet('selectAll', false);

    expect(Product::withTrashed()->find($product->id)->trashed())->toBeTrue();
});

test('selected employee export uses the employee export path', function () {
    $employee = Employee::factory()->create();

    Excel::fake();

    Livewire::test('pages::employees.index')
        ->set('selected', [$employee->id])
        ->call('exportSelectedExcel');

    Excel::assertDownloaded('employees-'.now()->format('Y-m-d').'.xlsx');
});
