<div class="min-h-screen bg-slate-50/70">
    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Inventory</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Products</h1>
                <p class="mt-1 text-sm text-slate-500">Search, filter, and manage your inventory.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="exportExcel" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    Export Excel
                </button>
                <a href="{{ route('products.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Add Product
                </a>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <label for="product-search" class="mb-1.5 block text-sm font-medium text-slate-700">Search</label>
                    <div class="relative">
                        <input id="product-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Search by name or barcode..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label class="text-sm font-medium text-slate-700">Price range</label>
                        <span class="text-sm font-semibold tabular-nums text-slate-700">
                            {{ number_format($minPrice, 0) }} – {{ number_format($maxPrice, 0) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="min-price-range" class="mb-1 block text-xs text-slate-500">Min range</label>
                            <input id="min-price-range" type="range" min="{{ $priceFloor }}" max="{{ $priceCeiling }}" step="1"
                                wire:model.live.debounce.150ms="minPrice" aria-label="Minimum product price"
                                class="h-2 w-full cursor-pointer accent-indigo-600">
                            <div class="mt-1 flex justify-between text-xs text-slate-400">
                                <span>{{ number_format($priceFloor, 0) }}</span><span>{{ number_format($maxPrice, 0) }}</span>
                            </div>
                        </div>
                        <div>
                            <label for="max-price-range" class="mb-1 block text-xs text-slate-500">Max range</label>
                            <input id="max-price-range" type="range" min="{{ $priceFloor }}" max="{{ $priceCeiling }}" step="1"
                                wire:model.live.debounce.150ms="maxPrice" aria-label="Maximum product price"
                                class="h-2 w-full cursor-pointer accent-indigo-600">
                            <div class="mt-1 flex justify-between text-xs text-slate-400">
                                <span>{{ number_format($minPrice, 0) }}</span><span>{{ number_format($priceCeiling, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 lg:col-span-3">
                    <div>
                        <label for="product-status" class="mb-1.5 block text-sm font-medium text-slate-700">Status</label>
                        <select id="product-status" wire:model.live="status" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="all">All statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" wire:model.live="lowStockOnly" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Low stock only <span class="text-xs text-slate-500">(≤ {{ $lowStockThreshold }} total)</span></span>
                    </label>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                <p class="text-sm text-slate-500"><span class="font-semibold text-slate-800">{{ number_format($this->totalProducts) }}</span> product(s) found</p>
                <button type="button" wire:click="resetFilters" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Reset filters</button>
            </div>
        </section>

        {{-- Table and bulk actions --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div>
                    <h2 class="font-semibold text-slate-900">Product list</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Select rows to run bulk actions.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <label for="per-page" class="text-sm text-slate-500">Rows</label>
                    <select id="per-page" wire:model.live="perPage" class="rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-sm">
                        <option value="10">10</option><option value="25">25</option><option value="50">50</option>
                    </select>
                </div>
            </div>

            @if (count($selected) > 0)
                <div class="flex flex-col gap-3 border-b border-indigo-100 bg-indigo-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <p class="text-sm font-semibold text-indigo-900">{{ count($selected) }} selected</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" wire:click="exportSelectedExcel" wire:loading.attr="disabled" class="rounded-lg border border-indigo-200 bg-white px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 disabled:opacity-50">
                            Export selected
                        </button>
                        <button type="button" wire:click="deleteSelected" wire:confirm="Delete all selected products?" class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                            Delete selected
                        </button>
                        <button type="button" wire:click="clearSelected" class="rounded-lg border border-indigo-200 bg-white px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                            Clear selection
                        </button>
                        <button type="button" wire:click="clearSelected" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-white">
                            Clear selection
                        </button>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="w-12 px-4 py-3 text-left">
                                <input type="checkbox" wire:model.live="selectAll" aria-label="Select all products on this page" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <button type="button" wire:click="sortBy('name')" class="hover:text-slate-900">Product {{ $sortField === 'name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Barcode</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <button type="button" wire:click="sortBy('price')" class="hover:text-slate-900">Price {{ $sortField === 'price' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <button type="button" wire:click="sortBy('current_quantity')" class="hover:text-slate-900">Total stock {{ $sortField === 'current_quantity' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($this->products as $product)
                            @php($quantity = (int) ($product->current_quantity ?? 0))
                            <tr wire:key="product-row-{{ $product->id }}" class="hover:bg-slate-50/80">
                                <td class="px-4 py-4">
                                    <input type="checkbox" wire:model.live="selected" value="{{ $product->id }}" aria-label="Select {{ $product->name }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    <div class="font-semibold text-slate-900">{{ $product->name }}</div>
                                    @if ($product->description)
                                        <div class="mt-0.5 max-w-xs truncate text-xs text-slate-500">{{ $product->description }}</div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">{{ $product->barcode ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-medium tabular-nums text-slate-900">{{ number_format((float) $product->price, 2) }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-right">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $quantity <= $lowStockThreshold ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ number_format($quantity) }}
                                    </span>
                                    @if ($quantity <= $lowStockThreshold)
                                        <div class="mt-1 text-xs text-amber-700">Low stock</div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($product->status ?? 'unknown') }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-right text-sm">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('products.edit', $product) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">Edit</a>
                                        <button type="button" wire:click="deleteProduct({{ $product->id }})" wire:confirm="Delete {{ addslashes($product->name) }}?" class="font-semibold text-rose-600 hover:text-rose-800">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center">
                                    <p class="font-semibold text-slate-800">No products found</p>
                                    <p class="mt-1 text-sm text-slate-500">Try changing your search or filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-4 sm:px-5">
                {{ $this->products->links() }}
            </div>
        </section>
    </div>
</div>
