
<div class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    {{-- Header --}}
    <div class="border-b border-gray-100 px-6 py-5">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Stock Filters
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Filter stock by product and location.
                </p>
            </div>

            <button
                wire:click="resetFilters"
                type="button"
                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
                Reset Filters
            </button>

        </div>

    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 xl:grid-cols-4">

        {{-- Search --}}
        <div class="xl:col-span-2">

            <x-form.input
                label="Search Product"
                name="search"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by product name or barcode..."
            />

        </div>

        {{-- Location Type --}}
        <div>

            <x-form.select-input
                label="Location Type"
                name="locationType"
                :options="[
                    'warehouse' => 'Warehouses',
                    'shop' => 'Shops',
                ]"
                placeholder="All Locations"
                wire:model.live="locationType"
            />

        </div>

        {{-- Warehouse --}}
        @if($locationType === 'warehouse')

            <div>

                <x-form.select-input
                    label="Warehouse"
                    name="warehouseId"
                    :options="$this->warehouses"
                    option-value="id"
                    option-label="name"
                    placeholder="All Warehouses"
                    wire:model.live="warehouseId"
                />

            </div>

        @endif

        {{-- Shop --}}
        @if($locationType === 'shop')

            <div>

                <x-form.select-input
                    label="Shop"
                    name="shopId"
                    :options="$shops"
                    option-value="id"
                    option-label="name"
                    placeholder="All Shops"
                    wire:model.live="shopId"
                />

            </div>

        @endif

        {{-- Low Stock --}}
        <div class="flex items-end">

            <label
                class="flex min-h-[46px] w-full cursor-pointer items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 transition hover:bg-gray-100"
            >

                <input
                    type="checkbox"
                    wire:model.live="lowStockOnly"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                >

                <div>
                    <p class="text-sm font-medium text-gray-700">
                        Low Stock Only
                    </p>

                    <p class="text-xs text-gray-500">
                        Show products below minimum stock
                    </p>
                </div>

            </label>

        </div>

    </div>

</div>
