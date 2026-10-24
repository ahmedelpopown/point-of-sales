<?php

use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Edit Product')] class extends Component
{
    use WithFileUploads;

    public ProductForm $form;

    public function mount(Product $product): void
    {
        $this->form->setProduct($product);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('message', 'Product updated successfully.');

        return $this->redirectRoute('products.index');
    }
};
?>

@include('pages.products.form', [
    'title' => 'Edit Product',
    'subtitle' => 'Update product information, pricing, barcode, image, and status.',
    'submitText' => 'Save Changes',
    'loadingText' => 'Saving...',
    'isEdit' => true,
])
