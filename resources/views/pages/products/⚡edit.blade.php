<?php

use Livewire\Component;
use App\Livewire\Forms\ProductForm;
use Livewire\Attributes\Title;
use App\Models\Product;

new #[Title('Update Product')] class extends Component {
    public ProductForm $form;

 
    public function mount(Product $product)
    {
        $this->form->setProduct($product);
    }
    public function save(){
        $this->form->update();
        session()->flash('message', 'Product Updated successfully.');
        return $this->redirect('/products');
    }
};



?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Update product
            </h2>
        </div>
    </div>

      <div class="mt-8 max-w-3xl">
        <form wire:submit="save">
            <div class="space-y-6">

                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Information
                        </h3>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">


                            <div>

                                <x-form.input
                                    label="Name"
                                    name="name"
                                    type="text"
                                    wire:model.blur="form.name" />

                                @error("name")
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror

                            </div>
                            {{-- price --}}
                            <div>

                                <x-form.input
                                    label="Price"
                                    name="name"
                                    type="number"
                                    min="1"
                                    wire:model.blur="form.price" id="price" />

                                @error("price")
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror

                            </div>
                            {{-- description --}}
                            <div>

                                <x-form.input
                                    label="Description"
                                    name="description"
                                    type="text"

                                    wire:model.blur="form.description" id="description" />

                                @error("description")
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror

                            </div>
                            {{-- barcode --}}
                            <div>

                                <x-form.input
                                    label="Barcode"
                                    name="barcode"
                                    type="number"

                                    wire:model.blur="form.barcode" id="barcode" />

                                @error("barcode")
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror

                            </div>






                            {{-- Status --}}
                            <div class="sm:col-span-2">

                                <x-form.select-input
                                    label="Status"
                                    id="status"
                                    name="status"
                                    :options="[
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ]"
                                    placeholder="Select Product Status"
                                    wire:model.blur="form.status" />
                                @error('form.status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>


                        </div>
                    </div>
                </div>



                {{-- Actions --}}
                <div class="flex justify-end gap-3">
                    <a
                        href="{{ route('products.index') }}"
                        class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Update product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>