<?php

use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Warehouse Stock')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $warehouseId = '';

    public bool $lowStockOnly = false;

    public string $sortField = 'product';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'warehouseId' => ['except' => ''],
        'lowStockOnly' => ['except' => false],
        'sortField' => ['except' => 'product'],
        'sortDirection' => ['except' => 'asc'],
    ];

    #[Computed]
    public function warehouses()
    {
        return Warehouse::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function stocks()
    {
        $sortFields = [
            'product' => 'products.name',
            'quantity' => 'stocks.quantity',
            'minimum_quantity' => 'stocks.minimum_quantity',
            'updated_at' => 'stocks.updated_at',
        ];

        $query = Stock::query()
            ->join(
                'products',
                'products.id',
                '=',
                'stocks.product_id'
            )
            ->select('stocks.*')
            ->where(
                'stocks.stockable_type',
                Warehouse::class
            )
            ->with([
                'product',
                'stockable',
            ])
            ->when(
                $this->warehouseId,
                fn (Builder $query) => $query->where(
                    'stocks.stockable_id',
                    $this->warehouseId
                )
            )
            ->when($this->search, function (Builder $query) {
                $search = "%{$this->search}%";

                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where(
                            'products.name',
                            'like',
                            $search
                        )
                        ->orWhere(
                            'products.barcode',
                            'like',
                            $search
                        );
                });
            })
            ->when(
                $this->lowStockOnly,
                fn (Builder $query) => $query->whereColumn(
                    'stocks.quantity',
                    '<=',
                    'stocks.minimum_quantity')
                );

        $sortColumn = $sortFields[$this->sortField]
            ?? $sortFields['product'];

        return $query
            ->orderBy(
                $sortColumn,
                $this->sortDirection
            )
            ->paginate($this->perPage);
    }

#[Computed]
public function statistics(): array
    {
        $query = Stock::query()
            ->where(
                'stockable_type',
                Warehouse::class
            )
            ->when(
                $this->warehouseId,
                fn (Builder $query) => $query->where(
                    'stockable_id',
                    $this->warehouseId
                )
            );

        return [
            'total_quantity' => (clone $query)->sum('quantity'),

            'products_count' => (clone $query)
                ->distinct('product_id')
                ->count('product_id'),

            'low_stock_count' => (clone $query)
                ->whereColumn(
                    'quantity',
                    '<=',
                    'minimum_quantity'
                )
                ->distinct('product_id')
                ->count('product_id'),

            'out_of_stock_count' => (clone $query)
                ->where('quantity', 0)
                ->distinct('product_id')
                ->count('product_id'),
        ];
    }

    public function sortBy(string $field): void
    {
        $allowedFields = [
            'product',
            'quantity',
            'minimum_quantity',
            'updated_at',
        ];

        if (! in_array($field, $allowedFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc'
                    ? 'desc'
                    : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedWarehouseId(): void
    {
        $this->resetPage();
    }

    public function updatedLowStockOnly(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'warehouseId',
            'lowStockOnly',
        ]);

        $this->resetPage();
    }

    public function stockStatus(Stock $stock): string
    {
        if ($stock->quantity === 0) {
            return 'Out of Stock';
        }

        if ($stock->isLowStock()) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    public function stockStatusClass(Stock $stock): string
    {
        return match ($this->stockStatus($stock)) {
            'Out of Stock' => 'bg-red-100 text-red-800',
            'Low Stock' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-green-100 text-green-800',
        };
    }
};
?>

<div class="px-4 sm:px-6 lg:px-8">

    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">
                Warehouse Stock
            </h1>

            <p class="mt-2 text-sm text-gray-700">
                View and monitor stock quantities across warehouses.
            </p>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Total Quantity
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
               {{ number_format($this->statistics['total_quantity']) }}
            </p>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Products
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['products_count']) }}
            </p>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Low Stock
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['low_stock_count']) }}
            </p>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Out of Stock
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['out_of_stock_count']) }}
            </p>
        </div>

    </div>

    {{-- Filters --}}
    <div class="mt-8 flex flex-col gap-4 md:flex-row">

        <div class="flex-1">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search products by name or barcode..."
                class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        <div class="w-full md:w-64">
            <select
                wire:model.live="warehouseId"
                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="">
                    All Warehouses
                </option>

                @foreach($this->warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">
                        {{ $warehouse->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-3">

            <input
                type="checkbox"
                wire:model.live="lowStockOnly"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            >

            <span class="text-sm font-medium text-gray-700">
                Low Stock Only
            </span>

        </label>

        <button
            wire:click="resetFilters"
            type="button"
            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
            Reset
        </button>

    </div>

    {{-- Table --}}
    <div class="mt-8 flex flex-col">

        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">

            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">

                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">

                    <table class="min-w-full divide-y divide-gray-300">

                        <thead class="bg-gray-50">
                            <tr>

                                <th
                                    wire:click="sortBy('product')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >
                                    Product
                                    @if($sortField === 'product')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Warehouse
                                </th>

                                <th
                                    wire:click="sortBy('quantity')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >
                                    Quantity
                                    @if($sortField === 'quantity')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th
                                    wire:click="sortBy('minimum_quantity')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >
                                    Minimum
                                    @if($sortField === 'minimum_quantity')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Status
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse($this->stocks as $stock)

                                <tr
                                    wire:key="warehouse-stock-{{ $stock->id }}"
                                    class="hover:bg-gray-50"
                                >

                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <div class="font-medium text-gray-900">
                                            {{ $stock->product->name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $stock->product->barcode }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $stock->stockable?->name ?? 'Unknown' }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">
                                        {{ number_format($stock->quantity) }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ number_format($stock->minimum_quantity) }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $this->stockStatusClass($stock) }}">
                                            {{ $this->stockStatus($stock) }}
                                        </span>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="5"
                                        class="px-3 py-8 text-center text-sm text-gray-500"
                                    >
                                        No warehouse stock records found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <div class="mt-4">
        {{ $this->stocks->links() }}
    </div>

</div>