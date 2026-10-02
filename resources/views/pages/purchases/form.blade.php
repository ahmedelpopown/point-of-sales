<div class="px-4 py-6 sm:px-6 lg:px-8">

    {{-- Header --}}
    <x-ui.page-header
        :title="$title"
        :subtitle="$subtitle"
        :back-url="route('purchases.index')"
        back-text="Back to Purchases"
    />


    {{-- Error --}}
    @if(session('error'))

        <div class="mt-6">

            <x-ui.alert
                type="error"
                :message="session('error')"
            />

        </div>

    @endif


    <form wire:submit="save" class="mt-8">

        <div class="space-y-6">


            {{-- Purchase Information --}}
            <x-form.section
                title="Purchase Information"
                description="Select supplier, warehouse, and employee."
            >

                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">


                    {{-- Supplier --}}
                    <x-form.searchable-select
                        label="Supplier"
                        name="form.supplier_id"
                        model="form.supplier_id"
                        :options="$this->suppliers"
                        optionValue="id"
                        optionLabel="name"
                        placeholder="Select Supplier"
                        searchPlaceholder="Search suppliers..."
                        :clearable="true"
                        required
                    />


                    {{-- Warehouse --}}
                    <x-form.searchable-select
                        label="Warehouse"
                        name="form.warehouse_id"
                        model="form.warehouse_id"
                        :options="$this->warehouses"
                        optionValue="id"
                        optionLabel="name"
                        placeholder="Select Warehouse"
                        searchPlaceholder="Search warehouses..."
                        :clearable="true"
                        required
                    />


                    {{-- Employee --}}
                    <x-form.searchable-select
                        label="Employee"
                        name="form.employee_id"
                        model="form.employee_id"
                        :options="$this->employees"
                        optionValue="id"
                        optionLabel="full_name"
                        placeholder="Select Employee"
                        searchPlaceholder="Search employees..."
                        :clearable="true"
                        required
                    />

                </div>

            </x-form.section>


            {{-- Purchase Items --}}
            <x-form.section
                title="Purchase Items"
                description="Add products, quantities, and unit prices."
            >

                <div class="space-y-4">

                    @foreach($form->items as $index => $item)

                        <div
                           wire:key="purchase-item-{{ $item['key'] }}"
                            class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
                        >

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-12">


                                {{-- Product --}}
                                <div class="md:col-span-5">

                                    <x-form.searchable-select
                                        label="Product"
                                        name="form.items.{{ $index }}.product_id"
                                        model="form.items.{{ $index }}.product_id"
                                        :options="$this->products"
                                        optionValue="id"
                                        optionLabel="name"
                                        placeholder="Select Product"
                                        searchPlaceholder="Search products..."
                                        :clearable="true"
                                        required
                                    />

                                </div>


                                {{-- Quantity --}}
                                <div class="md:col-span-2">

                                    <x-form.input
                                        label="Quantity"
                                        name="form.items.{{ $index }}.quantity"
                                        type="number"
                                        min="1"
                                        wire:model.blur="form.items.{{ $index }}.quantity"
                                        required
                                    />

                                </div>


                                {{-- Unit Price --}}
                                <div class="md:col-span-2">

                                    <x-form.input
                                        label="Unit Price"
                                        name="form.items.{{ $index }}.unit_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        wire:model.blur="form.items.{{ $index }}.unit_price"
                                        required
                                    />

                                </div>


                                {{-- Total --}}
                                <div class="md:col-span-2">

                                    <label class="block text-sm font-medium text-slate-700">
                                        Total
                                    </label>

                                    <div class="mt-2 flex min-h-11 items-center rounded-2xl bg-white px-4 text-sm font-semibold text-slate-900 ring-1 ring-slate-200">

                                        {{ number_format(
                                            $form->itemTotal($index),
                                            2
                                        ) }}

                                    </div>

                                </div>


                                {{-- Remove --}}
                                <div class="flex items-end md:col-span-1">

                                   @if(count($form->items) > 1)

    <button
        type="button"
        wire:click="removeItem({{ $index }})"
        class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-red-50 px-3 text-xs font-semibold text-red-700 transition hover:bg-red-100"
    >
        Remove
    </button>

@endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- Add Item --}}
                <div class="mt-5">

                    <button
    type="button"
    wire:click="addItem"
    class="inline-flex min-h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-700"
>
    + Add Item
</button>

                </div>


                {{-- Grand Total --}}
                <div class="mt-8 flex justify-end border-t border-slate-200 pt-6">

                    <div class="text-right">

                        <p class="text-sm font-medium text-slate-500">
                            Grand Total
                        </p>

                        <p class="mt-1 text-3xl font-bold text-slate-900">
                            {{ number_format($form->totalPrice(), 2) }}
                        </p>

                    </div>

                </div>

            </x-form.section>


            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('purchases.index') }}"
                    wire:navigate
                    class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-indigo-600 px-6 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <span
                        wire:loading.remove
                        wire:target="save"
                    >
                        {{ $submitText }}
                    </span>

                    <span
                        wire:loading
                        wire:target="save"
                    >
                        {{ $loadingText }}
                    </span>

                </button>

            </div>

        </div>

    </form>

</div>