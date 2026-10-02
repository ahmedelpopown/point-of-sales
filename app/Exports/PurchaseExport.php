<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseExport implements FromCollection, WithHeadings
{
    private Collection $rows;

    public function __construct(Collection $purchases)
    {
        $this->rows = collect();

        foreach ($purchases as $purchase) {

            foreach ($purchase->purchaseItems as $item) {

                $this->rows->push([
                    'import_key' => 'purchase-' . $purchase->id,

                    'purchase_id' => $purchase->id,

                    'supplier_id' => $purchase->supplier_id,

                    'supplier' => $purchase->supplier?->name,

                    'employee_id' => $purchase->employee_id,

                    'employee' => $purchase->employee?->full_name,

                    'warehouse_id' => $purchase->warehouse_id,

                    'warehouse' => $purchase->warehouse?->name,

                    'product_id' => $item->product_id,

                    'product' => $item->product?->name,

                    'quantity' => $item->quantity,

                    'unit_price' => $item->unit_price,

                    'item_total' => $item->total_price,

                    'purchase_total' => $purchase->total_price,

                    'purchase_date' =>
                        $purchase->created_at?->format(
                            'Y-m-d H:i:s'
                        ),
                ]);
            }
        }
    }

    public function headings(): array
    {
        return [
            'import_key',
            'purchase_id',
            'supplier_id',
            'supplier',
            'employee_id',
            'employee',
            'warehouse_id',
            'warehouse',
            'product_id',
            'product',
            'quantity',
            'unit_price',
            'item_total',
            'purchase_total',
            'purchase_date',
        ];
    }

    public function collection(): Collection
    {
        return $this->rows;
    }
}