<?php

use App\Livewire\Forms\ProductForm;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Product')] class extends Component
{
    public ProductForm $form;

    public function save()
    {
        $this->form->store();

        session()->flash(
            'message',
            'Product created successfully.'
        );

        return $this->redirectRoute('products.index');
    }
};
?>

<div class="min-h-screen bg-slate-50/70">

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <x-ui.page-header
            title="Create Product"
            badge="New Product"
            subtitle="Add a new product to your inventory and configure its basic information."
            :back-url="route('products.index')"
            back-text="Back to products"
        >

            <x-slot:icon>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm">

                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v12M6 12h12"
                        />
                    </svg>

                </div>
            </x-slot:icon>


            <x-slot:actions>
                <div
                    wire:dirty
                    class="hidden items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    Unsaved changes
                </div>
            </x-slot:actions>

        </x-ui.page-header>


        {{-- Form --}}
        <form wire:submit="save">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Main --}}
                <div class="space-y-6 lg:col-span-2">


                    {{-- Basic Information --}}
                    <x-form.section
                        title="Basic information"
                        description="General information that identifies the product."
                    >

                        <x-slot:icon>

                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 6.5A2.5 2.5 0 016.5 4h11A2.5 2.5 0 0120 6.5v11a2.5 2.5 0 01-2.5 2.5h-11A2.5 2.5 0 014 17.5v-11Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8 8h8M8 12h5"
                                />
                            </svg>

                        </x-slot:icon>


                        <div class="grid grid-cols-1 gap-6">

                            <x-form.input
                                label="Product Name"
                                name="form.name"
                                placeholder="e.g. Fresh Orange Juice"
                                required
                                wire:model.blur="form.name"
                            />


                            <x-form.textarea
                                label="Description"
                                name="form.description"
                                placeholder="Describe the product..."
                                rows="5"
                                wire:model.blur="form.description"
                            />

                        </div>

                    </x-form.section>


                    {{-- Pricing --}}
                    <x-form.section
                        title="Pricing & identification"
                        description="Set the selling price and product barcode."
                    >

                        <x-slot:icon>

                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3.75 7.75A2.75 2.75 0 016.5 5h11a2.75 2.75 0 012.75 2.75v8.5A2.75 2.75 0 0117.5 19h-11a2.75 2.75 0 01-2.75-2.75v-8.5Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8 9.5h8M8 13h5"
                                />
                            </svg>

                        </x-slot:icon>


                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                            <x-form.input
                                label="Price"
                                name="form.price"
                                type="number"
                                prefix="$"
                                placeholder="0.00"
                                required
                                wire:model.blur="form.price"
                            />


                            <x-form.input
                                label="Barcode"
                                name="form.barcode"
                                type="text"
                                inputmode="numeric"
                                placeholder="Enter barcode"
                                required
                                wire:model.blur="form.barcode"
                            />

                        </div>

                    </x-form.section>

                </div>


                {{-- Sidebar --}}
                <aside class="space-y-6">

                    {{-- Status --}}
                    <x-form.section
                        title="Product status"
                        description="Control whether this product is currently active."
                    >

                        <x-form.select-input
                            label="Status"
                            name="form.status"
                            :options="[
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ]"
                            placeholder="Select product status"
                            required
                            wire:model.live="form.status"
                        />

                    </x-form.section>


                    {{-- Info --}}
                    <x-ui.info-card
                        title="Before you create"
                        text="Make sure the product name, price, and barcode are correct. These values may be used later in sales and inventory operations."
                    />

                </aside>


                {{-- Actions --}}
                <div class="lg:col-span-3">

                    <x-form.actions
                        :cancel-url="route('products.index')"
                        cancel-text="Cancel"
                        submit-text="Create Product"
                        loading-text="Creating..."
                        loading-target="save"
                    >
                        Your product information will be saved securely.
                    </x-form.actions>

                </div>

            </div>

        </form>

    </div>

</div>