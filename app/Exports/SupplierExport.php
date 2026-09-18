<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $suppliers;
    public function __construct($suppliers = null)
    {
      
          $this->suppliers = $suppliers;
    }
    public function collection()
    {
        return $this->suppliers ?? Supplier::all();
    }


        public function headings(): array
    {
        return [
            
            'name',
            'email',
            'phone',
            'governorate',
            'city ',       
            'address ',       
            ];
            }

                public function map($supplier): array
            {
                return [
                 
                    $supplier->name,
                    $supplier->email,
                    $supplier->phone,
                    $supplier->governorate->name,
                    $supplier->city->name,
                    $supplier->address,
          
                ];
            }

}
