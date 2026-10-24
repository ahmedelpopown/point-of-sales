<?php

use App\Exports\ProductsExport;
use App\Imports\ProductImport;
use App\Livewire\Concerns\WithBulkSelection;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component
{
    use WithBulkSelection;
    use WithFileUploads, WithPagination;

    public int $lowStockThreshold = 5;

    public string $search = '';

    public string $status = 'all';

    public bool $lowStockOnly = false;

    public int $priceFloor = 0;

    public int $priceCeiling = 5000;

    public int $minPrice = 0;

    public int $maxPrice = 5000;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public $file = null;

    public function mount(): void
    {
        $bounds = Product::query()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $this->priceFloor = max(
            0,
            (int) floor((float) ($bounds?->min_price ?? 0))
        );

        $this->priceCeiling = max(
            $this->priceFloor,
            (int) ceil((float) ($bounds?->max_price ?? 5000))
        );

        $this->minPrice = $this->priceFloor;
        $this->maxPrice = $this->priceCeiling;
    }

    #[Computed]
    public function products()
    {
        return $this->filteredProductsQuery()->paginate($this->perPage);
    }

    #[Computed]
    public function totalProducts(): int
    {
        return $this->filteredProductsQuery()->count();
    }

    private function filteredProductsQuery(): Builder
    {
        $query = Product::query()
            ->withSum('stocks as current_quantity', 'quantity');

        if (trim($this->search) !== '') {
            $like = '%'.trim($this->search).'%';

            $query->where(fn (Builder $q) => $q
                ->where('name', 'like', $like)
                ->orWhere('barcode', 'like', $like));
        }

        $min = max($this->priceFloor, min($this->minPrice, $this->priceCeiling));
        $max = max($this->priceFloor, min($this->maxPrice, $this->priceCeiling));

        $query->whereBetween('price', [min($min, $max), max($min, $max)]);

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->lowStockOnly) {
            $query->whereRaw(
                'COALESCE((SELECT SUM(s.quantity) FROM stocks s
                WHERE s.product_id = products.id), 0) <= ?',
                [$this->lowStockThreshold]
            );
        }

        $allowed = ['name', 'price', 'status', 'created_at'];
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        if ($this->sortField === 'current_quantity') {
            $query->orderByRaw(
                '(SELECT COALESCE(SUM(s.quantity), 0) FROM stocks s
                WHERE s.product_id = products.id) '.$direction
            );
        } else {
            $field = in_array($this->sortField, $allowed, true)
                ? $this->sortField
                : 'created_at';

            $query->orderBy($field, $direction);
        }

        return $query;
    }

    private function refreshTable(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedSearch(): void
    {
        $this->refreshTable();
    }

    public function updatedStatus(): void
    {
        $this->refreshTable();
    }

    public function updatedLowStockOnly(): void
    {
        $this->refreshTable();
    }

    public function updatedPerPage(): void
    {
        $this->refreshTable();
    }

    public function updatedMinPrice($value): void
    {
        $this->minPrice = max(
            $this->priceFloor,
            min((int) $value, $this->priceCeiling)
        );

        if ($this->minPrice > $this->maxPrice) {
            $this->maxPrice = $this->minPrice;
        }

        $this->refreshTable();
    }

    public function updatedMaxPrice($value): void
    {
        $this->maxPrice = max(
            $this->priceFloor,
            min((int) $value, $this->priceCeiling)
        );

        if ($this->maxPrice < $this->minPrice) {
            $this->minPrice = $this->maxPrice;
        }

        $this->refreshTable();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->lowStockOnly = false;
        $this->minPrice = $this->priceFloor;
        $this->maxPrice = $this->priceCeiling;

        $this->refreshTable();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, [
            'name', 'price', 'status', 'created_at', 'current_quantity',
        ], true)) {
            return;
        }

        $this->sortDirection = $this->sortField === $field
            && $this->sortDirection === 'asc' ? 'desc' : 'asc';

        $this->sortField = $field;
        $this->refreshTable();
    }

    public function getBulkVisibleIds(): array
    {
        return $this->products->getCollection()->pluck('id')->all();
    }

    public function deleteProduct(int $productId): void
    {
        Product::query()->findOrFail($productId)->delete();

        $this->selected = array_values(array_diff(
            array_map('intval', $this->selected),
            [$productId]
        ));

        $this->syncBulkSelection();
        $this->resetPageIfNeeded();

        session()->flash('success', 'Product deleted successfully.');
    }

    public function deleteSelected(): void
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if (! $ids) {
            session()->flash('error', 'Select at least one product first.');

            return;
        }

        $count = Product::query()->whereIn('id', $ids)->delete();

        $this->clearSelection();
        $this->resetPageIfNeeded();

        session()->flash('success', "$count product(s) deleted successfully.");
    }

    public function exportExcel()
    {
        return Excel::download(
            new ProductsExport($this->filteredProductsQuery()->get()),
            'products.xlsx'
        );
    }

    public function exportSelectedExcel()
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if (! $ids) {
            session()->flash('error', 'Select at least one product to export.');

            return null;
        }

        $products = Product::query()
            ->withSum('stocks as current_quantity', 'quantity')
            ->whereIn('id', $ids)
            ->get();

        return Excel::download(
            new ProductsExport($products),
            'selected-products.xlsx'
        );
    }

    public function import(): void
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        Excel::import(new ProductImport, $this->file->getRealPath());

        $this->reset('file');
        $this->refreshTable();

        session()->flash('success', 'Products imported successfully.');
    }

    public function clearSelected(): void
    {
        $this->clearSelection();
    }

    private function clearSelection(): void
    {
        $this->clearBulkSelection();
    }

    private function resetPageIfNeeded(): void
    {
        if ($this->products->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
        }
    }
};
