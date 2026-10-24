<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product = null;

    public string $name = '';

    public string $barcode = '';

    public string $description = '';

    public string $price = '';

    public string $status = 'active';

    public $image = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'barcode' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'min:8',
                'max:50',
                Rule::unique('products', 'barcode')->ignore($this->product?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:999999999.99'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.min' => 'Product name must be at least 2 characters.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'barcode.required' => 'Barcode is required.',
            'barcode.regex' => 'Barcode must contain numbers only.',
            'barcode.min' => 'Barcode must be at least 8 digits.',
            'barcode.max' => 'Barcode cannot exceed 50 digits.',
            'barcode.unique' => 'This barcode is already registered.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Product price must be a valid number.',
            'price.min' => 'Product price cannot be negative.',
            'price.decimal' => 'Product price may contain up to 2 decimal places.',
            'price.max' => 'Product price is too large.',
            'status.required' => 'Please select a product status.',
            'status.in' => 'Invalid product status.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'Image must be JPG, JPEG, PNG, or WEBP.',
            'image.max' => 'Image size cannot exceed 2 MB.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'product name',
            'barcode' => 'barcode',
            'description' => 'description',
            'price' => 'price',
            'status' => 'status',
            'image' => 'product image',
        ];
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->barcode = $product->barcode;
        $this->description = $product->description ?? '';
        $this->price = (string) $product->price;
        $this->status = $product->status;
        $this->image = null;
    }

    public function store(): Product
    {
        $this->validate();

        $data = $this->only([
            'name',
            'barcode',
            'description',
            'price',
            'status',
        ]);

        if ($this->image) {
            $data['image'] = $this->image->storePublicly('products', 'public');
        }

        return Product::create($data);
    }

    public function update(): Product
    {
        if (!$this->product) {
            throw new \LogicException('A product must be loaded before updating.');
        }

        $this->validate();

        $data = $this->only([
            'name',
            'barcode',
            'description',
            'price',
            'status',
        ]);

        $oldImage = $this->product->image;

        if ($this->image) {
            $data['image'] = $this->image->storePublicly('products', 'public');
        }

        $this->product->update($data);

        if ($this->image && $oldImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        return $this->product->refresh();
    }
}
