<?php

namespace App\Imports;

use App\Services\PurchaseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PurchaseImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $rows = $rows
            ->filter(
                fn ($row) =>
                    filled($row['import_key'] ?? null)
            )
            ->values();


        if ($rows->isEmpty()) {

            throw ValidationException::withMessages([
                'importFile' =>
                    'The imported file does not contain valid purchase rows.',
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Row Validation
        |--------------------------------------------------------------------------
        */

        Validator::make(
            $rows->toArray(),
            [
                '*.import_key' => [
                    'required',
                    'string',
                    'max:100',
                ],

                '*.supplier_id' => [
                    'required',
                    'integer',
                    'exists:suppliers,id',
                ],

                '*.employee_id' => [
                    'required',
                    'integer',
                    'exists:employees,id',
                ],

                '*.warehouse_id' => [
                    'required',
                    'integer',
                    'exists:warehouses,id',
                ],

                '*.product_id' => [
                    'required',
                    'integer',
                    'exists:products,id',
                ],

                '*.quantity' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                '*.unit_price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],
            ]
        )->validate();


        /*
        |--------------------------------------------------------------------------
        | Group Rows
        |--------------------------------------------------------------------------
        */

        $groups = $rows->groupBy(
            fn ($row) =>
                trim($row['import_key'])
        );


        foreach ($groups as $importKey => $group) {

            $first = $group->first();


            /*
            |--------------------------------------------------------------------------
            | Same Purchase Header
            |--------------------------------------------------------------------------
            */

            foreach ([
                'supplier_id',
                'employee_id',
                'warehouse_id',
            ] as $field) {

                if (
                    $group
                        ->pluck($field)
                        ->map(fn ($value) => (string) $value)
                        ->unique()
                        ->count() > 1
                ) {

                    throw ValidationException::withMessages([
                        'importFile' =>
                            "Import key [{$importKey}] has different values for {$field}.",
                    ]);

                }
            }


            /*
            |--------------------------------------------------------------------------
            | No Duplicate Products
            |--------------------------------------------------------------------------
            */

            $productIds = $group
                ->pluck('product_id')
                ->map(fn ($id) => (string) $id);

            if ($productIds->duplicates()->isNotEmpty()) {

                throw ValidationException::withMessages([
                    'importFile' =>
                        "Import key [{$importKey}] contains duplicate products.",
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Prepare Items
            |--------------------------------------------------------------------------
            */

            $items = $group
                ->map(
                    fn ($row) => [
                        'product_id' => $row['product_id'],
                        'quantity' => $row['quantity'],
                        'unit_price' => $row['unit_price'],
                    ]
                )
                ->values()
                ->toArray();


            /*
            |--------------------------------------------------------------------------
            | Create Purchase
            |--------------------------------------------------------------------------
            */

            DB::transaction(
                function () use ($first, $items) {

                    app(PurchaseService::class)->create(
                        supplierId: (int) $first['supplier_id'],
                        employeeId: (int) $first['employee_id'],
                        warehouseId: (int) $first['warehouse_id'],
                        items: $items,
                    );

                }
            );
        }
    }
}