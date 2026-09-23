<?php

use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Update Product')] class extends Component
{
    public ProductForm $form;

    public function mount(Product $product): void
    {
        $this->form->setProduct($product);
    }

    public function save()
    {
        $this->form->update();

        session()->flash(
            'message',
            'Product updated successfully.'
        );

        return $this->redirectRoute('products.index');
    }
};
?>

<div class="min-h-screen bg-slate-50/70">

    <div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        {{-- =====================================================
             Header
        ====================================================== --}}

        <x-ui.page-header
            title="Edit Product"
            badge="Update Product"
            subtitle="Update product information, pricing, barcode, and status."
            :back-url="route('products.index')"
            back-text="Back to products"
        >

            <x-slot:icon>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm">

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
                            d="M12 20h9"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16.5 3.5a2.121 2.121 0 013 3L8 18l-4 1 1-4 11.5-11.5Z"
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


        {{-- =====================================================
             Form
        ====================================================== --}}

        <form wire:submit="save">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- =================================================
                     Main Content
                ================================================== --}}

                <div class="space-y-6 lg:col-span-2">


                    {{-- Basic Information --}}
                    <x-form.section
                        title="Basic information"
                        description="Update the general information that identifies this product."
                    >

                        <x-slot:icon>

                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect
                                    x="4"
                                    y="4"
                                    width="16"
                                    height="16"
                                    rx="2"
                                />

                                <path
                                    stroke-linecap="round"
                                    d="M8 9h8M8 13h5M8 17h3"
                                />
                            </svg>

                        </x-slot:icon>


                        <div class="space-y-6">

                            {{-- Product Name --}}
                            <x-form.input
                                label="Product Name"
                                name="form.name"
                                placeholder="e.g. Fresh Orange Juice"
                                required
                                wire:model.blur="form.name"
                            />


                            {{-- Description --}}
                            <x-form.textarea
                                label="Description"
                                name="form.description"
                                placeholder="Describe the product..."
                                rows="5"
                                wire:model.blur="form.description"
                            />

                        </div>

                    </x-form.section>


                    {{-- Pricing & Identification --}}
                    <x-form.section
                        title="Pricing & identification"
                        description="Update the selling price and barcode used to identify this product."
                    >

                        <x-slot:icon>

                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect
                                    x="3.5"
                                    y="5"
                                    width="17"
                                    height="14"
                                    rx="2.5"
                                />

                                <path
                                    stroke-linecap="round"
                                    d="M8 9v6M11 9v6M14 9v6M17 9v6"
                                />
                            </svg>

                        </x-slot:icon>


                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                            {{-- Price --}}
                            <x-form.input
                                label="Price"
                                name="form.price"
                                type="number"
                                min="0"
                                step="0.01"
                                inputmode="decimal"
                                prefix="$"
                                placeholder="0.00"
                                required
                                wire:model.blur="form.price"
                            />


                            {{-- Barcode --}}
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


                {{-- =================================================
                     Sidebar
                ================================================== --}}

                <aside class="space-y-6">


                    {{-- Product Status --}}
                    <x-form.section
                        title="Product status"
                        description="Control whether this product is currently available."
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


                    {{-- Current Product --}}
                    @if($form->product)

                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                            <div class="border-b border-slate-100 px-6 py-5">

                                <h2 class="text-base font-semibold text-slate-900">
                                    Current product
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    Quick overview of the product you're editing.
                                </p>

                            </div>


                            <div class="space-y-4 px-6 py-6">

                                {{-- Product Name --}}
                                <div>

                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                        Product
                                    </p>

                                    <p class="mt-1 truncate text-sm font-semibold text-slate-900">
                                        {{ $form->product->name }}
                                    </p>

                                </div>


                                {{-- Barcode --}}
                                <div>

                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                        Barcode
                                    </p>

                                    <p class="mt-1 font-mono text-xs font-medium text-slate-600">
                                        {{ $form->product->barcode }}
                                    </p>

                                </div>


                                {{-- Price --}}
                                <div>

                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                        Current Price
                                    </p>

                                    <p class="mt-1 text-sm font-bold text-slate-900">
                                        {{ number_format((float) $form->product->price, 2) }}
                                    </p>

                                </div>


                                {{-- Status --}}
                                <div>

                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                        Current Status
                                    </p>

                                    <div class="mt-1">

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold
                                            {{
                                                $form->product->status === 'active'
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : 'bg-slate-100 text-slate-600'
                                            }}"
                                        >

                                            <span
                                                class="h-1.5 w-1.5 rounded-full
                                                {{
                                                    $form->product->status === 'active'
                                                        ? 'bg-emerald-500'
                                                        : 'bg-slate-400'
                                                }}"
                                            ></span>

                                            {{ ucfirst($form->product->status) }}

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </section>

                    @endif


                    {{-- Information --}}
                    <x-ui.info-card
                        title="Before you save"
                        text="Review the updated product information carefully. Changes to price, barcode, and status may affect future sales and inventory operations."
                    />

                </aside>


                {{-- =================================================
                     Actions
                ================================================== --}}

                <div class="lg:col-span-3">

                    <x-form.actions
                        :cancel-url="route('products.index')"
                        cancel-text="Cancel"
                        submit-text="Save Changes"
                        loading-text="Saving..."
                        loading-target="save"
                    >
                        Changes will be applied to this product.
                    </x-form.actions>

                </div>

            </div>

        </form>

    </div>

</div>