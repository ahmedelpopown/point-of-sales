<?php

namespace App\Imports;

use App\Models\Supplier;
 
 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupplierImport implements ToModel, WithHeadingRow, WithValidation
{
  
    public function model(array $row)
    {
        return new Supplier([
            'name' => $row['name'],
            'email' => $row['email'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'city_id' => $row['city_id'],
            'governorate_id' => $row['governorate_id'],
        ]);
    }


        public function rules(): array
    {
        return [

            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'city_id' => 'required',
            'governorate_id' => 'required',

        ];
    }
}
