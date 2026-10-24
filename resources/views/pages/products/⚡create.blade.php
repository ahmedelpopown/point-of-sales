<?php

use App\Livewire\Forms\ProductForm;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Create Product')] class extends Component
{
    use WithFileUploads;

    public ProductForm $form;

    public function save()
    {
        $this->form->store();

        session()->flash('message', 'Product created successfully.');

        return $this->redirectRoute('products.index');
    }
};
?>

@include('pages.products.form', [
    'title' => 'Create Product',
    'subtitle' => 'Add a new product to your inventory and configure its basic information.',
    'submitText' => 'Create Product',
    'loadingText' => 'Creating...',
    'isEdit' => false,
])
