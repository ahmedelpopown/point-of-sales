<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product = null;

    #[Validate('required|min:2')]
    public string $name = '';

    /**_______________________**/

    #[Validate('required|unique:products,barcode')]
    public string $barcode = '';

    /**_______________________**/

    #[Validate('nullable|min:20')]
    public string $description = '';

    /**_______________________**/

    #[Validate('required|numeric|min:0')]
    public string $price = '';

    /**_______________________**/

    #[Validate(['required', 'in:active,inactive'])]
    public string $status = 'active';

    /**_______________________**/

    #[Validate('nullable')]
    public $image = '';

    /**_______________________**/

    public function setProduct(Product $product): void
    {
        $this->product = $product;

        $this->name = $product->name;
        $this->barcode = $product->barcode;
        $this->description = $product->description ?? '';
        $this->price = $product->price;
        $this->status = $product->status;
        $this->image = $product->image;
    }

    public function store(): void
    {
        $this->validate();

        Product::create(
            $this->only([
                'name',
                'barcode',
                'description',
                'price',
                'status',
                'image',
            ])
        );

        $this->reset();

        $this->status = 'active';
    }

    public function update(): void
    {
        $this->validate([
            'name' => 'required|min:2',
            'barcode' => 'required|unique:products,barcode,' . $this->product->id,
            'description' => 'nullable|min:20',
            'price' => 'required|numeric|min:0',
            'status' => ['required', 'in:active,inactive'],
            'image' => 'nullable',
        ]);

        $this->product->update(
            $this->only([
                'name',
                'barcode',
                'description',
                'price',
                'status',
                'image',
            ])
        );

        $this->reset();

        $this->status = 'active';
    }
}
