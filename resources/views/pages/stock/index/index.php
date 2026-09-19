<?php

use App\Models\Shop;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Stock Overview')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $locationType = '';

    public string $warehouseId = '';

    public string $shopId = '';

    public bool $lowStockOnly = false;

    public string $sortField = 'product';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'locationType' => ['except' => ''],
        'warehouseId' => ['except' => ''],
        'shopId' => ['except' => ''],
        'lowStockOnly' => ['except' => false],
        'sortField' => ['except' => 'product'],
        'sortDirection' => ['except' => 'asc'],
    ];

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
            ->with([
                'product',
                'stockable',
            ]);

        $this->applyFilters($query);

        $sortColumn = $sortFields[$this->sortField]
            ?? $sortFields['product'];

        return $query
            ->orderBy($sortColumn, $this->sortDirection)
            ->paginate($this->perPage);
    }

    private function applyFilters(Builder $query): void
    {
        $query
            ->when(
                $this->search,
                function (Builder $query) {
                    $search = "%{$this->search}%";

                    $query->where(function (Builder $query) use ($search) {
                        $query
                            ->where('products.name', 'like', $search)
                            ->orWhere('products.barcode', 'like', $search);
                    });
                }
            )
            ->when(
                $this->locationType === 'warehouse',
                fn (Builder $query) => $query->where(
                    'stocks.stockable_type',
                    Warehouse::class
                )
            )
            ->when(
                $this->locationType === 'shop',
                fn (Builder $query) => $query->where(
                    'stocks.stockable_type',
                    Shop::class
                )
            )
            ->when(
                $this->warehouseId,
                fn (Builder $query) => $query
                    ->where(
                        'stocks.stockable_type',
                        Warehouse::class
                    )
                    ->where(
                        'stocks.stockable_id',
                        $this->warehouseId
                    )
            )
            ->when(
                $this->shopId,
                fn (Builder $query) => $query
                    ->where(
                        'stocks.stockable_type',
                        Shop::class
                    )
                    ->where(
                        'stocks.stockable_id',
                        $this->shopId
                    )
            )
            ->when(
                $this->lowStockOnly,
                fn (Builder $query) => $query->whereColumn(
                    'stocks.quantity',
                    '<=',
                    'stocks.minimum_quantity'
                )
            );
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function shops()
    {
        return Shop::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function statistics(): array
    {
        return [
            'total_quantity' => Stock::query()->sum('quantity'),

            'products_count' => Stock::query()
                ->distinct('product_id')
                ->count('product_id'),

            'warehouses_count' => Warehouse::query()
                ->where('status', 'active')
                ->count(),

            'shops_count' => Shop::query()
                ->where('status', 'active')
                ->count(),

            'low_stock_count' => Stock::query()
                ->whereColumn(
                    'quantity',
                    '<=',
                    'minimum_quantity'
                )
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

            $this->resetPage();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocationType(): void
    {
        $this->warehouseId = '';
        $this->shopId = '';

        $this->resetPage();
    }

    public function updatedWarehouseId(): void
    {
        $this->locationType = 'warehouse';
        $this->shopId = '';

        $this->resetPage();
    }

    public function updatedShopId(): void
    {
        $this->locationType = 'shop';
        $this->warehouseId = '';

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
            'locationType',
            'warehouseId',
            'shopId',
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

    public function locationName(Stock $stock): string
    {
        return $stock->stockable?->name ?? 'Unknown';
    }

    public function locationTypeName(Stock $stock): string
    {
        return match ($stock->stockable_type) {
            Warehouse::class => 'Warehouse',
            Shop::class => 'Shop',
            default => 'Unknown',
        };
    }
};