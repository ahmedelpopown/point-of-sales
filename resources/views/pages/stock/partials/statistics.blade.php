<div class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    <div class="border-b border-gray-100 px-6 py-5">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Stock Filters
                </h2>

                <p class="text-sm text-gray-500">
                    Filter stock by product and location.
                </p>
            </div>

            <button
                wire:click="resetFilters"
                type="button"
                class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Reset Filters
            </button>

        </div>

    </div>

    <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 xl:grid-cols-4">

        <div class="xl:col-span-2">

            <x-form.input
                label="Search Product"
                name="search"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by product name or barcode..."
            />

        </div>

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

        @if($locationType === 'warehouse')

            <div>

                <x-form.select-input
                    label="Warehouse"
                    name="warehouseId"
                    :options="$warehouses"
                    option-value="id"
                    option-label="name"
                    placeholder="All Warehouses"
                    wire:model.live="warehouseId"
                />

            </div>

        @endif

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

        <div class="flex items-end">

            <label class="flex min-h-[46px] w-full cursor-pointer items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 hover:bg-gray-100">

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