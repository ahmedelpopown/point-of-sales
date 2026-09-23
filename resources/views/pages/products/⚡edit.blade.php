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
        $this->form->store();
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
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                     Name
                                </label>
                                <input 
                                    wire:model.blur="form.name"
                                    type="text" 
                                    id="name"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                >
                                @error('form.name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        

                            {{-- price --}}
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">
                                    price
                                </label>
                                <input 
                                    wire:model.blur="form.price"
                                    type="number" 
                                    id="price"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                >
                                @error('form.price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- description --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    description
                                </label>
                                <input 
                                    wire:model.blur="form.description"
                                    type="text" 
                                    id="description"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                >
                                @error('form.description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- barcode --}}
                            <div>
                                <label for="barcode" class="block text-sm font-medium text-gray-700">
                                    barcode
                                </label>
                                <input 
                                    wire:model="form.barcode"
                                    type="number" 
                                    id="barcode"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                >
                                @error('form.barcode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                          
                            {{-- Status --}}
                            <div class="sm:col-span-2">
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>
                                <select 
                                    wire:model="form.status"
                                    id="status"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
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
                        class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Create product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>