<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
   protected $products; 
    public function __construct( $products=null)
    {
        $this->products = $products;

    }
    public function collection()
    {
        return $this->products ??Product::all()   ;
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
