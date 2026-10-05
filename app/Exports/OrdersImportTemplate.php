<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersImportTemplate implements
    FromArray,
    WithHeadings
{
    public function headings(): array
    {
        return [
            'order_reference',
            'customer_id',
            'employee_id',
            'shop_id',
            'product_id',
            'quantity',
            'price',
            'payment_method',
            'installment_plan_id',
            'down_payment',
            'start_date',
        ];
    }

    public function array(): array
    {
        return [];
    }
}