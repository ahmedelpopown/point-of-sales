<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements
    FromQuery,
    WithHeadings,
    WithMapping
{
    public function __construct(
        protected Builder $query
    ) {}

    public function query()
    {
        return $this->query
            ->with([
                'user',
                'employee',
            ])
            ->withCount('orderItems');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer',
            'Employee',
            'Items',
            'Total',
            'Date',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->user?->name ?? 'Walk-in Customer',
            $order->employee?->full_name ?? 'N/A',
            $order->order_items_count,
            $order->total,
            $order->created_at?->format('Y-m-d H:i'),
        ];
    }
}