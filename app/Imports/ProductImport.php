<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Product([
            'name' => $row['name'],
            'description' => $row['description'],
            'barcode' => $row['barcode'],
            'current_quantity' => $row['current_quantity'],
            'price' => $row['price'],
            'status' => $row['status'] ?? 'active',
            'image' => $row['image'] ?? 'no image',
        ]);
    }


    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'barcode' => 'required',
            'current_quantity' => 'required',
            'price' => 'required',
            'status' => 'required',
            'image' => 'required',
        ];

    }
}
