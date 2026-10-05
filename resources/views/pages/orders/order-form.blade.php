{{-- Order Information --}}
<div class="space-y-6">

    <div class="overflow-visible rounded-2xl border border-gray-200 bg-white shadow-sm">

        <div class="border-b border-gray-100 px-6 py-5">

            <h2 class="text-lg font-semibold text-gray-900">
                Order Information
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Select the customer and employee responsible for this order.
            </p>

        </div>


        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

            {{-- Customer --}}
            <x-form.searchable-select
                label="Customer"
                model="form.user_id"
                :options="$this->users"
                option-value="id"
                option-label="name"
                placeholder="Select Customer"
            />


            {{-- Employee --}}
            <div>

                <label class="block text-sm font-medium text-gray-700">
                    Employee
                </label>

                @if(auth()->user()->hasRole('admin'))

                    <select
                        wire:model="form.employee_id"
                        class="mt-1 block w-full rounded-xl border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                        <option value="">
                            Select Employee
                        </option>

                        @foreach($this->employees as $employee)

                            <option value="{{ $employee->id }}">
                                {{ $employee->full_name }}
                            </option>

                        @endforeach

                    </select>

                @else

                    <div class="mt-1 flex min-h-[42px] items-center rounded-xl border border-gray-200 bg-gray-50 px-4">

                        <div>

                            <p class="text-xs text-gray-500">
                                Logged in as
                            </p>

                            <p class="text-sm font-semibold text-gray-900">
                                {{ auth('employee')->user()->first_name }}
                            </p>

                        </div>

                    </div>

                @endif


                @error('form.employee_id')

                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>

        </div>

    </div>


    {{-- Order Items --}}
    <div class="overflow-visible rounded-2xl border border-gray-200 bg-white shadow-sm">

        <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="text-lg font-semibold text-gray-900">
                    Order Items
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Add products and set the required quantity.
                </p>

            </div>


            <button
                type="button"
                wire:click="form.addItem"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
            >
                + Add Item
            </button>

        </div>


        <div class="space-y-4 p-6">

            @foreach($form->items as $index => $item)

                <div
                    wire:key="order-item-{{ $index }}"
                    class="rounded-2xl border border-gray-200 bg-gray-50 p-5"
                >

                    {{-- Item Header --}}
                    <div class="mb-5 flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-sm font-bold text-indigo-700">
                                {{ $index + 1 }}
                            </div>

                            <div>

                                <h3 class="text-sm font-semibold text-gray-900">
                                    Order Item
                                </h3>

                                <p class="text-xs text-gray-500">
                                    Product details
                                </p>

                            </div>

                        </div>


                        @if(count($form->items) > 1)

                            <button
                                type="button"
                                wire:click="form.removeItem({{ $index }})"
                                class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700"
                            >
                                Remove
                            </button>

                        @endif

                    </div>


                    {{-- Product + Shop --}}
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                        {{-- Product --}}
                        <div>

                            <x-form.searchable-select
                                label="Product"
                                model="form.items.{{ $index }}.product_id"
                                :options="$this->products"
                                option-value="id"
                                option-label="name"
                                placeholder="Select Product"
                            />

                            @error("form.items.$index.product_id")

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Shop --}}
                        <div>

                            @if(auth()->user()->hasRole('admin'))

                                <x-form.searchable-select
                                    label="Shop"
                                    model="form.items.{{ $index }}.shop_id"
                                    :options="$this->shops"
                                    option-value="id"
                                    option-label="name"
                                    placeholder="Select Shop"
                                />

                            @else

                                <label class="block text-sm font-medium text-gray-700">
                                    Shop
                                </label>

                                <div class="mt-1 flex min-h-[42px] items-center rounded-xl border border-gray-200 bg-white px-4 shadow-sm">

                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ auth('employee')->user()->shop?->name ?? 'No Shop Assigned' }}
                                    </span>

                                </div>

                            @endif


                            @error("form.items.$index.shop_id")

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    </div>


                    {{-- Quantity + Price + Total --}}
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">

                        {{-- Quantity --}}
                        <div>

                            <x-form.input
                                label="Quantity"
                                name="items.{{ $index }}.quantity"
                                type="number"
                                min="1"
                                wire:model.live="form.items.{{ $index }}.quantity"
                            />

                            @error("form.items.$index.quantity")

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Unit Price --}}
                        <div>

                            <x-form.input
                                label="Unit Price"
                                name="items.{{ $index }}.price"
                                type="number"
                                min="0"
                                step="0.01"
                                wire:model="form.items.{{ $index }}.price"
                                readonly
                                class="cursor-not-allowed bg-gray-100 text-gray-700"
                                hint="Based on latest purchase price"
                            />

                            @error("form.items.$index.price")

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Total --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700">
                                Item Total
                            </label>

                            <div class="mt-1 flex min-h-[42px] items-center justify-between rounded-xl border border-gray-200 bg-white px-4 shadow-sm">

                                <span class="text-xs text-gray-500">
                                    Total
                                </span>

                                <span class="text-base font-bold text-gray-900">
                                    {{ number_format($this->form->itemTotal($index), 2) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        {{-- Grand Total --}}
        <div class="border-t border-gray-100 bg-gray-50 px-6 py-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-gray-500">
                        Grand Total
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Total amount for all order items
                    </p>

                </div>


                <div class="text-right">

                    <p class="text-3xl font-bold tracking-tight text-gray-900">
                        {{ number_format($this->form->total(), 2) }}
                    </p>

                    <p class="text-xs text-gray-500">
                        EGP
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>