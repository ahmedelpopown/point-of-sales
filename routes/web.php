<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\auth\EmployeeAuthController;
use App\Http\Controllers\PurchasePaymentController;
use App\Http\Controllers\SupplierPaymentController;
use App\Livewire\RolePermissionManager;
use App\Livewire\RolesIndex;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::livewire(
    '/login',
    'pages::auth.login'
)->name('login');

Route::post(
    '/logout',
    [EmployeeAuthController::class, 'logout']
)
    ->middleware('auth:employee')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| POS
|--------------------------------------------------------------------------
*/

Route::middleware('auth:employee')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )
        ->middleware('permission:view dashboard')
        ->name('dashboard');

 Route::get(
        '/admin/roles-permissions',
        RolePermissionManager::class
    )->name('admin.roles.permissions');
 Route::get(
        '/admin/roles',
        RolesIndex::class
    )->name('admin.roles');

    Route::post(
        '/purchases/{purchase}/payments/paypal',
        [PurchasePaymentController::class, 'payWithPaypal']
    )->name('purchases.payments.paypal');

    Route::get(
        '/purchases/{purchase}/payments/{payment}/status',
        [PurchasePaymentController::class, 'status']
    )->name('purchases.payments.status');
    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    Route::prefix('employees')
        ->name('employees.')
        ->group(function () {

            Route::livewire(
                '/',
                'pages::employees.index'
            )
                ->middleware('permission:view employees')
                ->name('index');

            Route::livewire(
                '/create',
                'pages::employees.create'
            )
                ->middleware('permission:create employees')
                ->name('create');

            Route::livewire(
                '/{employee}/edit',
                'pages::employees.edit'
            )
                ->middleware('permission:update employees')
                ->name('edit');
        });


    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::prefix('users')
        ->name('users.')
        ->group(function () {

            Route::livewire(
                '/',
                'pages::users.index'
            )
                ->middleware('permission:view users')
                ->name('index');

            Route::livewire(
                '/create',
                'pages::users.create'
            )
                ->middleware('permission:create users')
                ->name('create');

            Route::livewire(
                '/{user}/edit',
                'pages::users.edit'
            )
                ->middleware('permission:update users')
                ->name('edit');
        });


    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    Route::prefix('products')
        ->name('products.')
        ->group(function () {

            Route::livewire(
                '/',
                'pages::products.index'
            )
                ->middleware('permission:view products')
                ->name('index');

            Route::livewire(
                '/create',
                'pages::products.create'
            )
                ->middleware('permission:create products')
                ->name('create');

            Route::livewire(
                '/{product}/edit',
                'pages::products.edit'
            )
                ->middleware('permission:update products')
                ->name('edit');
        });


    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    */

    Route::prefix('suppliers')
        ->name('suppliers.')
        ->group(function () {

            Route::livewire(
                '/',
                'pages::suppliers.index'
            )
                ->middleware('permission:view suppliers')
                ->name('index');

            Route::livewire(
                '/create',
                'pages::suppliers.create'
            )
                ->middleware('permission:create suppliers')
                ->name('create');

            Route::livewire(
                '/{supplier}/edit',
                'pages::suppliers.edit'
            )
                ->middleware('permission:update suppliers')
                ->name('edit');
        });


    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */

    Route::prefix('purchases')
        ->name('purchases.')
        ->group(function () {

            Route::livewire(
                '/',
                'pages::purchases.index'
            )
                ->middleware('permission:view purchases')
                ->name('index');

            Route::livewire(
                '/create',
                'pages::purchases.create'
            )
                ->middleware('permission:create purchases')
                ->name('create');

            Route::livewire(
                '/{purchase}/edit',
                'pages::purchases.edit'
            )
                ->middleware('permission:update purchases')
                ->name('edit');

            Route::livewire(
                '/{purchase}/show',
                'pages::purchases.show'
            )
                ->middleware('permission:view purchases')
                ->name('show');
        });


    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    Route::prefix('orders')
        ->name('orders.')
        ->group(function () {

            Route::livewire(
                '/',
                'pages::orders.index'
            )
                ->middleware('permission:view orders')
                ->name('index');

            Route::livewire(
                '/create',
                'pages::orders.create'
            )
                ->middleware('permission:create orders')
                ->name('create');

            Route::livewire(
                '/{order}/edit',
                'pages::orders.edit'
            )
                ->middleware('permission:update orders')
                ->name('edit');

            Route::livewire(
                '/{order}/show',
                'pages::orders.show'
            )
                ->middleware('permission:view orders')
                ->name('show');
        });


    /*
    |--------------------------------------------------------------------------
    | Installments
    |--------------------------------------------------------------------------
    */

    Route::prefix('installments')
        ->name('installments.')
        ->group(function () {

            Route::livewire(
                '/',
                'pages::installments.index'
            )
                ->middleware('permission:view installments')
                ->name('index');

            Route::livewire(
                '/create',
                'pages::installments.create'
            )
                ->middleware('permission:create installments')
                ->name('create');

            Route::livewire(
                '/{installment}/edit',
                'pages::installments.edit'
            )
                ->middleware('permission:update installments')
                ->name('edit');

            Route::livewire(
                '/{installment}/show',
                'pages::installments.show'
            )
                ->middleware('permission:view installments')
                ->name('show');
        });


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::livewire(
        '/reports',
        'pages::reports.index'
    )
        ->middleware('permission:view reports')
        ->name('reports.index');


    /*
    |--------------------------------------------------------------------------
    | Stock
    |--------------------------------------------------------------------------
    */

    Route::livewire(
        '/stock',
        'pages::stock.index'
    )
        ->middleware('permission:view stock')
        ->name('stock.index');


    /*
    |--------------------------------------------------------------------------
    | Stock Adjustment
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/stock/adjust',
        [\App\Http\Controllers\StockAdjustmentController::class, 'store']
    )
        ->middleware('permission:adjust stock')
        ->name('stock.adjust');

});