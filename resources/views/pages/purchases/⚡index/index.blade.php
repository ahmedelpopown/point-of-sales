<div class="px-4 py-6 sm:px-6 lg:px-8">

    <div class="mx-auto max-w-7xl">

        {{-- Header --}}
        <x-ui.page-header
            title="Purchases"
            subtitle="Manage purchase transactions, suppliers, warehouses, and products."
        >
            <x-slot:actions>

                <div class="flex flex-wrap items-center gap-2">

                    {{-- Export --}}
                    <button
                        type="button"
                        wire:click="exportExcel"
                        wire:loading.attr="disabled"
                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="exportExcel">
                            Export Excel
                        </span>

                        <span wire:loading wire:target="exportExcel">
                            Exporting...
                        </span>
                    </button>


                    {{-- Import --}}
                    <label
                        class="inline-flex h-10 cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        Import Excel

                        <input
                            type="file"
                            wire:model="importFile"
                            accept=".xlsx,.xls,.csv"
                            class="hidden"
                        >
                    </label>


                    {{-- Process Import --}}
                    @if($importFile)

                        <button
                            type="button"
                            wire:click="import"
                            wire:loading.attr="disabled"
                            class="inline-flex h-10 items-center justify-center rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="import">
                                Process Import
                            </span>

                            <span wire:loading wire:target="import">
                                Importing...
                            </span>
                        </button>

                    @endif


                    {{-- Add --}}
                    <a
                        href="{{ route('purchases.create') }}"
                        wire:navigate
                        class="inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        + Add Purchase
                    </a>

                </div>

            </x-slot:actions>
        </x-ui.page-header>


        {{-- Alerts --}}
        @if(session('message'))

            <div class="mt-5">
                <x-ui.alert
                    type="success"
                    :message="session('message')"
                />
            </div>

        @endif


        @if(session('error'))

            <div class="mt-5">
                <x-ui.alert
                    type="error"
                    :message="session('error')"
                />
            </div>

        @endif


        {{-- Selected Import File --}}
        @if($importFile)

            <div class="mt-4 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">

                <div>

                    <p class="text-sm font-semibold text-emerald-800">
                        File selected
                    </p>

                    <p class="mt-0.5 text-xs text-emerald-700">
                        {{ $importFile->getClientOriginalName() }}
                    </p>

                </div>

                <button
                    type="button"
                    wire:click="$set('importFile', null)"
                    class="text-xs font-semibold text-emerald-700 hover:text-emerald-900"
                >
                    Remove
                </button>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- Filters --}}
        {{-- ========================================================= --}}

        <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Header --}}
            <div class="border-b border-slate-100 px-5 py-4">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <h2 class="text-sm font-semibold text-slate-900">
                            Filters
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Search and filter purchase transactions.
                        </p>

                    </div>


                    @if(
                        $search ||
                        $supplier_id ||
                        $employee_id ||
                        $warehouse_id ||
                        $product_id ||
                        $date_from ||
                        $date_to ||
                        $total_min !== '' ||
                        $total_max !== ''
                    )

                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="shrink-0 text-xs font-semibold text-slate-500 transition hover:text-slate-900"
                        >
                            Clear all
                        </button>

                    @endif

                </div>

            </div>


            {{-- Body --}}
            <div class="px-5 py-5">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">

                    {{-- Search --}}
                    <div class="xl:col-span-4">

                        <x-form.input
                            label="Search"
                            name="search"
                            placeholder="Purchase ID, supplier, employee, warehouse..."
                            wire:model.live.debounce.300ms="search"
                        />

                    </div>


                    {{-- Supplier --}}
                    <div class="xl:col-span-2">

                        <x-form.searchable-select
                            label="Supplier"
                            name="supplier"
                            model="supplier_id"
                            :options="$this->suppliers"
                            optionValue="id"
                            optionLabel="name"
                            placeholder="All Suppliers"
                            searchPlaceholder="Search suppliers..."
                            :clearable="true"
                        />

                    </div>


                    {{-- Employee --}}
                    <div class="xl:col-span-3">

                        <x-form.searchable-select
                            label="Employee"
                            name="employee"
                            model="employee_id"
                            :options="$this->employees"
                            optionValue="id"
                            optionLabel="full_name"
                            placeholder="All Employees"
                            searchPlaceholder="Search employees..."
                            :clearable="true"
                        />

                    </div>


                    {{-- Warehouse --}}
                    <div class="xl:col-span-3">

                        <x-form.searchable-select
                            label="Warehouse"
                            name="warehouse"
                            model="warehouse_id"
                            :options="$this->warehouses"
                            optionValue="id"
                            optionLabel="name"
                            placeholder="All Warehouses"
                            searchPlaceholder="Search warehouses..."
                            :clearable="true"
                        />

                    </div>


                    {{-- Product --}}
                    <div class="xl:col-span-4">

                        <x-form.searchable-select
                            label="Product"
                            name="product"
                            model="product_id"
                            :options="$this->products"
                            optionValue="id"
                            optionLabel="name"
                            placeholder="All Products"
                            searchPlaceholder="Search products..."
                            :clearable="true"
                        />

                    </div>


                    {{-- Date From --}}
                    <div class="xl:col-span-2">

                        <x-form.input
                            label="Date From"
                            name="date_from"
                            type="date"
                            wire:model.live="date_from"
                        />

                    </div>


                    {{-- Date To --}}
                    <div class="xl:col-span-2">

                        <x-form.input
                            label="Date To"
                            name="date_to"
                            type="date"
                            wire:model.live="date_to"
                        />

                    </div>


                    {{-- Minimum Total --}}
                    <div class="xl:col-span-2">

                        <x-form.input
                            label="Min Total"
                            name="total_min"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            wire:model.live.debounce.400ms="total_min"
                        />

                    </div>


                    {{-- Maximum Total --}}
                    <div class="xl:col-span-2">

                        <x-form.input
                            label="Max Total"
                            name="total_max"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            wire:model.live.debounce.400ms="total_max"
                        />

                    </div>


                    {{-- Reset --}}
                    <div class="flex items-end xl:col-span-2">

                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="inline-flex h-10 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                        >
                            Reset Filters
                        </button>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- Results --}}
        {{-- ========================================================= --}}

        <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Results Header --}}
            <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="text-sm font-semibold text-slate-900">
                        Purchase Transactions
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">

                        @if($this->purchases->total())

                            Showing

                            <span class="font-semibold text-slate-700">
                                {{ $this->purchases->firstItem() }}
                            </span>

                            -

                            <span class="font-semibold text-slate-700">
                                {{ $this->purchases->lastItem() }}
                            </span>

                            of

                            <span class="font-semibold text-slate-700">
                                {{ $this->purchases->total() }}
                            </span>

                            purchases

                        @else

                            No purchases found.

                        @endif

                    </p>

                </div>


                @if($this->purchases->hasPages())

                    <p class="text-xs text-slate-500">

                        Page

                        <span class="font-semibold text-slate-700">
                            {{ $this->purchases->currentPage() }}
                        </span>

                        of

                        <span class="font-semibold text-slate-700">
                            {{ $this->purchases->lastPage() }}
                        </span>

                    </p>

                @endif

            </div>


            {{-- Table --}}
            <div class="overflow-x-auto">

                <x-ui.data-table
                    :items="$this->purchases"

                    :columns="[
                        [
                            'field' => 'id',
                            'label' => 'ID',
                            'sortable' => true,
                        ],
                        [
                            'field' => 'supplier_name',
                            'label' => 'Supplier',
                            'type' => 'avatar',
                        ],
                        [
                            'field' => 'employee_name',
                            'label' => 'Employee',
                        ],
                        [
                            'field' => 'warehouse_name',
                            'label' => 'Warehouse',
                        ],
                        [
                            'field' => 'items_count',
                            'label' => 'Items',
                        ],
                        [
                            'field' => 'total_price',
                            'label' => 'Total',
                            'type' => 'money',
                            'bold' => true,
                            'sortable' => true,
                        ],
                        [
                            'field' => 'created_at_formatted',
                            'label' => 'Date',
                        ],
                    ]"

                    emptyText="No purchases found."

                    :sortField="$sortField"
                    :sortDirection="$sortDirection"

                    showRoute="purchases.show"
                    editRoute="purchases.edit"
                    deleteMethod="deletePurchase"

                    rowKey="id"

                    :compact="true"
                    :showPagination="false"
                />

            </div>


            {{-- Pagination --}}
            @if($this->purchases->hasPages())

                <div class="border-t border-slate-100 px-5 py-4">

                    <div class="flex justify-end">

                        {{ $this->purchases->links() }}

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>