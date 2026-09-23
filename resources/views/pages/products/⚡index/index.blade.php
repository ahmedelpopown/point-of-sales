<div class="min-h-screen bg-slate-50/70">

    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        {{-- =====================================================
             Header
        ====================================================== --}}

        <x-ui.page-header
            title="Products"
            badge="Inventory"
            subtitle="Manage your products, stock, pricing, and product information.">

            <x-slot:icon>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm">

                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6.5A2.5 2.5 0 016.5 4h11A2.5 2.5 0 0120 6.5v11a2.5 2.5 0 01-2.5 2.5h-11A2.5 2.5 0 014 17.5v-11Z" />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 8h8M8 12h5" />
                    </svg>

                </div>

            </x-slot:icon>


            <x-slot:actions>

                <div class="flex w-full flex-wrap gap-2 sm:w-auto">

                    {{-- Export Excel --}}
                    <x-ui.action-button
                        wire:click="exportExcel"
                        class="flex-1 sm:flex-none">
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 4h16v16H4z" />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8 8l8 8M16 8l-8 8" />
                        </svg>

                        <span class="hidden sm:inline">
                            Export Excel
                        </span>

                        <span class="sm:hidden">
                            Excel
                        </span>
                    </x-ui.action-button>


                    {{-- Export PDF --}}
                    <x-ui.action-button
                        wire:click="exportPdf"
                        class="flex-1 sm:flex-none">
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 3h9l3 3v15H6z" />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M14 3v4h4" />
                        </svg>

                        <span class="hidden sm:inline">
                            Export PDF
                        </span>

                        <span class="sm:hidden">
                            PDF
                        </span>
                    </x-ui.action-button>


                    {{-- Import --}}
                    <label
                        class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 sm:flex-none">
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 16V4" />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m8 8 4-4 4 4" />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13v5a2 2 0 002 2h10a2 2 0 002-2v-5" />
                        </svg>

                        <span class="hidden sm:inline">
                            Import Excel
                        </span>

                        <span class="sm:hidden">
                            Import
                        </span>

                        <input
                            type="file"
                            wire:model="importFile"
                            accept=".xlsx,.xls,.csv"
                            class="hidden">
                    </label>


                    {{-- Process Import --}}
                    @if($importFile)

                    <x-ui.action-button
                        variant="success"
                        wire:click="import"
                        loading-target="import"
                        class="w-full sm:w-auto">
                        Process Import
                    </x-ui.action-button>

                    @endif


                    {{-- Add Product --}}
                    <a
                        href="{{ route('products.create') }}"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold  shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 sm:flex-none">
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 6v12M6 12h12" />
                        </svg>

                        <span class="hidden sm:inline ">Add Product</span>
                        <span class="sm:hidden">Add</span>
                    </a>

                </div>

            </x-slot:actions>

        </x-ui.page-header>


        {{-- =====================================================
             Alerts
        ====================================================== --}}

        <div class="space-y-3">

            @if(session()->has('message'))

            <x-ui.alert
                type="success"
                :message="session('message')" />

            @endif


            @if(session()->has('error'))

            <x-ui.alert
                type="error"
                :message="session('error')" />

            @endif

        </div>


        {{-- =====================================================
             Filters
        ====================================================== --}}

        <x-ui.filter-bar>

            {{-- Search --}}
            <div class="min-w-0 flex-1">

                <x-form.input
                    name="search"
                    placeholder="Search by name or barcode..."
                    wire:model.live.debounce.300ms="search" />

            </div>


            {{-- Filter --}}
            <div class="w-full lg:w-52">

                {{-- Example:
                <x-form.select-input
                    name="statusFilter"
                    :options="[
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]"
                    placeholder="All statuses"
                    wire:model.live="statusFilter"
                />
                --}}

            </div>


            {{-- Reset --}}
            <x-ui.action-button
                wire:click="resetFilters"
                class="w-full lg:w-auto">
                <svg
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 4v5h.58m0 0a8 8 0 111.42 8.42M4.58 9H9" />
                </svg>

                Reset
            </x-ui.action-button>

        </x-ui.filter-bar>


        {{-- =====================================================
             Bulk Actions
        ====================================================== --}}

        @if(count($selected) > 0)

        <x-ui.bulk-actions
            :count="count($selected)">

            <x-ui.action-button
                wire:click="exportSelected"
                class="flex-1 sm:flex-none">
                Export Selected
            </x-ui.action-button>


            <x-ui.action-button
                variant="danger"
                wire:click="bulkDelete"
                wire:confirm="Are you sure you want to delete the selected products?"
                class="flex-1 sm:flex-none">
                Delete Selected
            </x-ui.action-button>

        </x-ui.bulk-actions>

        @endif


        {{-- =====================================================
             DESKTOP TABLE
             >= 1024px
        ====================================================== --}}

        <div class="mt-8 hidden lg:block">

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50/80">

                            <tr>

                                {{-- Select --}}
                                <th class="w-12 px-5 py-4">

                                    <input
                                        type="checkbox"
                                        wire:model.live="selectAll"
                                        class="h-4 w-4 rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500">

                                </th>


                                {{-- Name --}}
                                <th
                                    wire:click="sortBy('name')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900">
                                    <div class="flex items-center gap-2">

                                        Name

                                        <span>
                                            @if($sortField === 'name')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                            @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                            @endif
                                        </span>

                                    </div>
                                </th>


                                {{-- Price --}}
                                <th
                                    wire:click="sortBy('price')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900">
                                    <div class="flex items-center gap-2">

                                        Price

                                        <span>
                                            @if($sortField === 'price')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                            @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                            @endif
                                        </span>

                                    </div>
                                </th>


                                {{-- Stock --}}
                                <th
                                    wire:click="sortBy('current_quantity')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900">
                                    <div class="flex items-center gap-2">

                                        Total Stock

                                        <span>
                                            @if($sortField === 'current_quantity')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                            @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                            @endif
                                        </span>

                                    </div>
                                </th>


                                {{-- Description --}}
                                <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Description
                                </th>


                                {{-- Barcode --}}
                                <th
                                    wire:click="sortBy('barcode')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900">
                                    <div class="flex items-center gap-2">

                                        Barcode

                                        <span>
                                            @if($sortField === 'barcode')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                            @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                            @endif
                                        </span>

                                    </div>
                                </th>


                                {{-- Status --}}
                                <th
                                    wire:click="sortBy('status')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900">
                                    <div class="flex items-center gap-2">

                                        Status

                                        <span>
                                            @if($sortField === 'status')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                            @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                            @endif
                                        </span>

                                    </div>
                                </th>


                                {{-- Actions --}}
                                <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @forelse($this->products as $product)

                            <tr
                                wire:key="desktop-product-{{ $product->id }}"
                                class="group transition-colors hover:bg-slate-50/70">

                                {{-- Checkbox --}}
                                <td class="px-5 py-4">

                                    <input
                                        type="checkbox"
                                        wire:model.live="selected"
                                        value="{{ $product->id }}"
                                        class="h-4 w-4 rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500">

                                </td>


                                {{-- Name --}}
                                <td class="px-4 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-xs font-bold text-indigo-600">
                                            {{ strtoupper(substr($product->name, 0, 1)) }}
                                        </div>

                                        <p class="max-w-[220px] truncate text-sm font-semibold text-slate-900">
                                            {{ $product->name }}
                                        </p>

                                    </div>

                                </td>


                                {{-- Price --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    <span class="text-sm font-semibold text-slate-900">
                                        {{ number_format((float) $product->price, 2) }}
                                    </span>

                                </td>


                                {{-- Stock --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    @php
                                    $stock = $product->current_quantity ?? 0;
                                    @endphp

                                    <span
                                        class="inline-flex rounded-lg px-2.5 py-1 text-xs font-bold
                                            {{
                                                $stock > 10
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : ($stock > 0
                                                        ? 'bg-amber-50 text-amber-700'
                                                        : 'bg-red-50 text-red-700')
                                            }}">
                                        {{ $stock }}
                                    </span>

                                </td>


                                {{-- Description --}}
                                <td class="max-w-xs px-4 py-4">

                                    <p
                                        class="truncate text-sm text-slate-500"
                                        title="{{ $product->description }}">
                                        {{ $product->description ?: 'No description' }}
                                    </p>

                                </td>


                                {{-- Barcode --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    <span class="font-mono text-xs font-medium text-slate-500">
                                        {{ $product->barcode }}
                                    </span>

                                </td>


                                {{-- Status --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold
                                            {{
                                                $product->status === 'active'
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : 'bg-slate-100 text-slate-600'
                                            }}">

                                        <span
                                            class="h-1.5 w-1.5 rounded-full
                                                {{
                                                    $product->status === 'active'
                                                        ? 'bg-emerald-500'
                                                        : 'bg-slate-400'
                                                }}"></span>

                                        {{ ucfirst($product->status) }}

                                    </span>

                                </td>


                                {{-- Actions --}}
                                <td class="px-5 py-4">

                                    <div class="flex justify-end gap-1">

                                        <a
                                            href="{{ route('products.edit', $product) }}"
                                            class="rounded-lg px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50">
                                            Edit
                                        </a>

                                        <button
                                            type="button"
                                            wire:click="confirmDelete({{ $product->id }})"
                                            class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                            Delete
                                        </button>

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="px-6 py-16 text-center">

                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                                        <svg
                                            class="h-6 w-6"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M4 6.5A2.5 2.5 0 016.5 4h11A2.5 2.5 0 0120 6.5v11a2.5 2.5 0 01-2.5 2.5H6.5A2.5 2.5 0 014 17.5v-11Z" />

                                            <path
                                                stroke-linecap="round"
                                                d="M8 10h8M8 14h5" />
                                        </svg>

                                    </div>

                                    <h3 class="mt-4 text-sm font-semibold text-slate-900">
                                        No products found
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Try changing your search or filters.
                                    </p>

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- =====================================================
             MOBILE CARDS
             < 1024px
        ====================================================== --}}

        <div class="mt-6 space-y-3 lg:hidden">

            @forelse($this->products as $product)

            @php
            $stock = $product->current_quantity ?? 0;
            @endphp

            <article
                wire:key="mobile-product-{{ $product->id }}"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Top --}}
                <div class="flex items-start justify-between gap-3 p-4">

                    <div class="flex min-w-0 items-center gap-3">

                        <input
                            type="checkbox"
                            wire:model.live="selected"
                            value="{{ $product->id }}"
                            class="mt-1 h-4 w-4 shrink-0 rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500">


                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-600">
                            {{ strtoupper(substr($product->name, 0, 1)) }}
                        </div>


                        <div class="min-w-0">

                            <h3 class="truncate text-sm font-semibold text-slate-900">
                                {{ $product->name }}
                            </h3>

                            <p class="mt-0.5 truncate font-mono text-[11px] text-slate-400">
                                {{ $product->barcode }}
                            </p>

                        </div>

                    </div>


                    {{-- Status --}}
                    <span
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold
                            {{
                                $product->status === 'active'
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-slate-100 text-slate-600'
                            }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full
                                {{
                                    $product->status === 'active'
                                        ? 'bg-emerald-500'
                                        : 'bg-slate-400'
                                }}"></span>

                        {{ ucfirst($product->status) }}
                    </span>

                </div>


                {{-- Data --}}
                <div class="grid grid-cols-2 gap-px border-y border-slate-100 bg-slate-100">

                    <div class="bg-white px-4 py-3">

                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                            Price
                        </p>

                        <p class="mt-1 text-sm font-bold text-slate-900">
                            {{ number_format((float) $product->price, 2) }}
                        </p>

                    </div>


                    <div class="bg-white px-4 py-3">

                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                            Stock
                        </p>

                        <p class="mt-1">

                            <span
                                class="inline-flex rounded-lg px-2.5 py-1 text-xs font-bold
                                    {{
                                        $stock > 10
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : ($stock > 0
                                                ? 'bg-amber-50 text-amber-700'
                                                : 'bg-red-50 text-red-700')
                                    }}">
                                {{ $stock }}
                            </span>

                        </p>

                    </div>

                </div>


                {{-- Description --}}
                <div class="px-4 py-3">

                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                        Description
                    </p>

                    <p class="mt-1 text-sm leading-5 text-slate-600">
                        {{ $product->description ?: 'No description' }}
                    </p>

                </div>


                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50/70 px-4 py-3">

                    <a
                        href="{{ route('products.edit', $product) }}"
                        class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-indigo-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-indigo-50">
                        Edit
                    </a>

                    <button
                        type="button"
                        wire:click="confirmDelete({{ $product->id }})"
                        class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-red-50">
                        Delete
                    </button>

                </div>

            </article>

            @empty

            <div class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">

                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6.5A2.5 2.5 0 016.5 4h11A2.5 2.5 0 0120 6.5v11a2.5 2.5 0 01-2.5 2.5H6.5A2.5 2.5 0 014 17.5v-11Z" />

                        <path
                            stroke-linecap="round"
                            d="M8 10h8M8 14h5" />
                    </svg>

                </div>

                <h3 class="mt-4 text-sm font-semibold text-slate-900">
                    No products found
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Try changing your search or filters.
                </p>

            </div>

            @endforelse

        </div>


        {{-- =====================================================
             Pagination
        ====================================================== --}}

        <div class="mt-5 overflow-x-auto">
            <div class="min-w-max">
                {{ $this->products->links() }}
            </div>
        </div>


        {{-- =====================================================
             Delete Modal
        ====================================================== --}}

        <x-ui.confirm-modal
            :show="$showDeleteModal"
            title="Delete Product"
            message="Are you sure you want to delete this product? This action cannot be undone."
            confirm-text="Delete Product"
            cancel-text="Cancel"
            confirm-action="deleteProduct"
            cancel-action="$set('showDeleteModal', false)" />

    </div>

</div>