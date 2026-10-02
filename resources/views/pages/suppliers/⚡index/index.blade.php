<div class="px-4 py-6 sm:px-6 lg:px-8">


{{-- Header --}}
<x-ui.page-header
    title="Suppliers"
    subtitle="Manage suppliers, contact information, locations, and financial status."
>
    <x-slot:actions>

        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">

            <button
                wire:click="exportExcel"
                class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Export Excel
            </button>

            <button
                wire:click="exportPdf"
                class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Export PDF
            </button>

            <label
                class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Import Excel

                <input
                    type="file"
                    wire:model="importFile"
                    accept=".xlsx,.xls,.csv"
                    class="hidden"
                >
            </label>

            @if($importFile)
                <button
                    wire:click="import"
                    class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700"
                >
                    Process Import
                </button>
            @endif

            <a
                href="{{ route('suppliers.create') }}"
                wire:navigate
                class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700"
            >
                Add Supplier
            </a>

        </div>

    </x-slot:actions>
</x-ui.page-header>


{{-- Alerts --}}
@if(session('message'))
    <div class="mt-6">
        <x-ui.alert
            type="success"
            :message="session('message')"
        />
    </div>
@endif

@if(session('error'))
    <div class="mt-6">
        <x-ui.alert
            type="error"
            :message="session('error')"
        />
    </div>
@endif


{{-- Filters --}}
<x-ui.filter-bar>

    {{-- Search --}}
    <div class="w-full flex-1">

        <x-form.input
            name="search"
            placeholder="Search suppliers..."
            wire:model.live.debounce.300ms="search"
        />

    </div>


    {{-- Governorate --}}
    <div class="w-full lg:w-56">

        <x-form.searchable-select
            name="governorate"
            model="governorate_id"
            :options="$this->governorates"
            optionValue="id"
            optionLabel="name"
            placeholder="All Governorates"
            searchPlaceholder="Search governorates..."
            :clearable="true"
        />

    </div>


    {{-- City --}}
    <div class="w-full lg:w-48">

        <x-form.searchable-select
            name="city"
            model="city_id"
            :options="$this->cities"
            optionValue="id"
            optionLabel="name"
            placeholder="All Cities"
            searchPlaceholder="Search cities..."
            :clearable="true"
        />

    </div>


    {{-- Reset --}}
    <button
        wire:click="resetFilters"
        class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
    >
        Reset
    </button>

</x-ui.filter-bar>


{{-- Bulk Actions --}}
@if(count($selected))

    <div class="mt-6">

        <x-ui.bulk-actions :count="count($selected)">

            <button
                wire:click="exportSelected"
                class="inline-flex min-h-10 items-center rounded-xl bg-indigo-100 px-4 text-sm font-semibold text-indigo-700 hover:bg-indigo-200"
            >
                Export Selected
            </button>

            <button
                wire:click="exportSelectedExcel"
                class="inline-flex min-h-10 items-center rounded-xl bg-emerald-100 px-4 text-sm font-semibold text-emerald-700 hover:bg-emerald-200"
            >
                Excel Selected
            </button>

            <button
                wire:click="bulkDelete"
                wire:confirm="Are you sure you want to delete the selected suppliers?"
                class="inline-flex min-h-10 items-center rounded-xl bg-red-100 px-4 text-sm font-semibold text-red-700 hover:bg-red-200"
            >
                Delete Selected
            </button>

        </x-ui.bulk-actions>

    </div>

@endif


{{-- Data Table --}}
<div class="mt-8">

    <x-ui.data-table
        :items="$this->suppliers"
        :columns="[
            [
                'field' => 'name',
                'label' => 'Name',
                'type' => 'avatar',
                'sortable' => true,
            ],
            [
                'field' => 'email',
                'label' => 'Email',
                'sortable' => true,
            ],
            [
                'field' => 'governorate.name',
                'label' => 'Governorate',
                'sortable' => true,
            ],
            [
                'field' => 'city.name',
                'label' => 'City',
                'sortable' => true,
            ],
            [
                'field' => 'phone',
                'label' => 'Phone',
                'sortable' => true,
            ],
            [
                'field' => 'address',
                'label' => 'Address',
            ],
            [
                'field' => 'purchases_sum_total_price',
                'label' => 'Total Purchases',
            ],
            [
                'field' => 'payments_sum_amount',
                'label' => 'Paid',
            ],
        ]"
        emptyText="No suppliers found."
        :selectable="true"
        selectedModel="selected"
        selectAllModel="selectAll"
        :sortField="$sortField"
        :sortDirection="$sortDirection"
        editRoute="suppliers.edit"
        deleteMethod="confirmDelete"
        rowKey="id"
    />

</div>


{{-- Delete Modal --}}
<x-ui.confirm-modal
    :show="$showDeleteModal"
    title="Delete Supplier"
    message="Are you sure you want to delete this supplier? This action cannot be undone."
    confirmText="Delete Supplier"
    cancelText="Cancel"
    confirmAction="deleteSupplier"
    cancelAction="showDeleteModal = false"
/>

</div>
