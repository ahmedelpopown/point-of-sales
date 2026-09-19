<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = 'employee';

        $permissions = [
            'view dashboard',

            'view employees',
            'create employees',
            'update employees',
            'delete employees',

            'view users',
            'create users',
            'update users',
            'delete users',

            'view products',
            'create products',
            'update products',
            'delete products',

            'view suppliers',
            'create suppliers',
            'update suppliers',
            'delete suppliers',

            'view purchases',
            'create purchases',
            'update purchases',
            'delete purchases',

             'view roles',
            'create roles',
            'update roles',
            'delete roles',
            
            'view orders',
            'create orders',
            'update orders',
            'delete orders',

            'view installments',
            'create installments',
            'update installments',
            'delete installments',

            'view stock',
            'adjust stock',

            'view reports',

            'manage permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate(
                $permission,
                $guardName
            );
        }

        $superAdmin = Role::findOrCreate(
            'super-admin',
            $guardName
        );

        $manager = Role::findOrCreate(
            'manager',
            $guardName
        );

        $cashier = Role::findOrCreate(
            'cashier',
            $guardName
        );

        $inventoryManager = Role::findOrCreate(
            'inventory-manager',
            $guardName
        );

        $accountant = Role::findOrCreate(
            'accountant',
            $guardName
        );

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdmin->syncPermissions(
            Permission::where('guard_name', $guardName)->get()
        );

        /*
        |--------------------------------------------------------------------------
        | Manager
        |--------------------------------------------------------------------------
        */

        $manager->syncPermissions([
            'view dashboard',
           


            'view employees',
            'create employees',
            'update employees',

            'view users',
            'create users',
            'update users',

            'view products',
            'create products',
            'update products',

            'view suppliers',
            'create suppliers',
            'update suppliers',

            'view purchases',
            'create purchases',
            'update purchases',
            'delete purchases',

            'view orders',
            'create orders',
            'update orders',
            'delete orders',

            'view installments',
            'create installments',
            'update installments',

            'view stock',
            'adjust stock',
'transfer stock',
'view stock movements',
            'view reports',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cashier
        |--------------------------------------------------------------------------
        */

        $cashier->syncPermissions([
            'view dashboard',

            'view products',

            'view users',

            'view orders',
            'create orders',

            'view installments',
            'create installments',

            'view stock',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Inventory Manager
        |--------------------------------------------------------------------------
        */

        $inventoryManager->syncPermissions([
            'view dashboard',

            'view products',
            'create products',
            'update products',

            'view suppliers',

            'view purchases',
            'create purchases',
            'update purchases',

            'view stock',
            'adjust stock',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Accountant
        |--------------------------------------------------------------------------
        */

        $accountant->syncPermissions([
            'view dashboard',

            'view orders',

            'view purchases',

            'view installments',

            'view reports',
        ]);
    }
}