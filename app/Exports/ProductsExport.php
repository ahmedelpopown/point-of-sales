<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected ?Collection $products = null
    ) {
    }

    public function collection(): Collection
    {
        return $this->products ?? Product::query()
            ->withSum('stocks as current_quantity', 'quantity')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'name',
            'barcode',
            'status',
            'price',
            'quantity',
            'description',
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->barcode,
            $product->status,
            $product->price,
            $product->current_quantity,
            $product->description,
        ];
    }
}
