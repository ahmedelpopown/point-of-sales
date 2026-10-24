<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): Product
    {
        return new Product([
            'name' => trim((string) $row['name']),
            'description' => isset($row['description']) && trim((string) $row['description']) !== ''
                ? trim((string) $row['description'])
                : null,
            'barcode' => (string) $row['barcode'],
            'price' => $row['price'],
            'status' => isset($row['status']) && $row['status'] !== ''
                ? $row['status']
                : 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string', 'max:255'],
            '*.barcode' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'min:8',
                'max:50',
                'distinct',
                'unique:products,barcode',
            ],
            '*.description' => ['nullable', 'string', 'max:1000'],
            '*.price' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:999999999.99'],
            '*.status' => ['nullable', 'in:active,inactive'],
        ];
    }
}
