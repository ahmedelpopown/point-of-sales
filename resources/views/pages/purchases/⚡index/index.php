<?php

use App\Exports\PurchaseExport;
use App\Imports\PurchaseImport;
use App\Livewire\Concerns\WithBulkSelection;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

new #[Title('Purchases')] class extends Component
{
    use WithBulkSelection, WithFileUploads, WithPagination;

    public $importFile = null;

    public string $search = '';

    public $supplier_id = '';

    public $employee_id = '';

    public $warehouse_id = '';

    public $product_id = '';

    public $date_from = '';

    public $date_to = '';

    public $total_min = '';

    public $total_max = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public function getBulkVisibleIds(): array
    {
        return $this->purchases->getCollection()->pluck('id')->all();
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'supplier_id' => ['except' => ''],
        'employee_id' => ['except' => ''],
        'warehouse_id' => ['except' => ''],
        'product_id' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'total_min' => ['except' => ''],
        'total_max' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    /*
    |--------------------------------------------------------------------------
    | Filter Options
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function suppliers()
    {
        return Supplier::query()
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function employees()
    {
        return Employee::query()
            ->orderBy('first_name')
            ->get();
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::query()
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->orderBy('name')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    private function filteredQuery()
    {
        $query = Purchase::query()
            ->with([
                'supplier:id,name',
                'employee:id,first_name,last_name,email',
                'warehouse:id,name',
                'purchaseItems.product:id,name',
            ])
            ->withCount('purchaseItems');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $query->when(
            trim($this->search) !== '',
            function ($query) {

                $search = '%'.trim($this->search).'%';

                $query->where(function ($q) use ($search) {

                    if (is_numeric($this->search)) {
                        $q->where(
                            'id',
                            (int) $this->search
                        );
                    }

                    $q->orWhereHas(
                        'supplier',
                        function ($supplierQuery) use ($search) {

                            $supplierQuery->where(
                                'name',
                                'like',
                                $search
                            );
                        }
                    );

                    $q->orWhereHas(
                        'employee',
                        function ($employeeQuery) use ($search) {

                            $employeeQuery
                                ->where(
                                    'first_name',
                                    'like',
                                    $search
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    $search
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    $search
                                );
                        }
                    );

                    $q->orWhereHas(
                        'warehouse',
                        function ($warehouseQuery) use ($search) {

                            $warehouseQuery->where(
                                'name',
                                'like',
                                $search
                            );
                        }
                    );

                    $q->orWhereHas(
                        'purchaseItems.product',
                        function ($productQuery) use ($search) {

                            $productQuery->where(
                                'name',
                                'like',
                                $search
                            );
                        }
                    );

                });
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        $query->when(
            $this->supplier_id,
            fn ($query) => $query->where(
                'supplier_id',
                $this->supplier_id
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Employee
        |--------------------------------------------------------------------------
        */

        $query->when(
            $this->employee_id,
            fn ($query) => $query->where(
                'employee_id',
                $this->employee_id
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Warehouse
        |--------------------------------------------------------------------------
        */

        $query->when(
            $this->warehouse_id,
            fn ($query) => $query->where(
                'warehouse_id',
                $this->warehouse_id
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Product
        |--------------------------------------------------------------------------
        */

        $query->when(
            $this->product_id,
            fn ($query) => $query->whereHas(
                'purchaseItems',
                fn ($itemQuery) => $itemQuery->where(
                    'product_id',
                    $this->product_id
                )
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        $query->when(
            $this->date_from,
            fn ($query) => $query->whereDate(
                'created_at',
                '>=',
                $this->date_from
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        $query->when(
            $this->date_to,
            fn ($query) => $query->whereDate(
                'created_at',
                '<=',
                $this->date_to
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Total From
        |--------------------------------------------------------------------------
        */

        $query->when(
            $this->total_min !== '',
            fn ($query) => $query->where(
                'total_price',
                '>=',
                (float) $this->total_min
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Total To
        |--------------------------------------------------------------------------
        */

        $query->when(
            $this->total_max !== '',
            fn ($query) => $query->where(
                'total_price',
                '<=',
                (float) $this->total_max
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            $this->sortField,
            $this->sortDirection
        );

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function purchases()
    {
        $paginator = $this->filteredQuery()
            ->paginate($this->perPage);

        $paginator->through(
            function ($purchase) {

                $purchase->supplier_name =
                    $purchase->supplier?->name ?? 'N/A';

                $purchase->employee_name =
                    $purchase->employee?->full_name ?? 'N/A';

                $purchase->warehouse_name =
                    $purchase->warehouse?->name ?? 'N/A';

                $purchase->items_count =
                    $purchase->purchase_items_count;

                $purchase->created_at_formatted =
                    $purchase->created_at?->format(
                        'Y-m-d H:i'
                    );

                return $purchase;
            }
        );

        return $paginator;
    }

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    public function sortBy(string $field): void
    {
        $allowedFields = [
            'id',
            'total_price',
            'created_at',
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
        $this->clearBulkSelection();
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Pagination When Filters Change
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedSupplierId(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedEmployeeId(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedWarehouseId(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedProductId(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedTotalMin(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedTotalMax(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'supplier_id',
            'employee_id',
            'warehouse_id',
            'product_id',
            'date_from',
            'date_to',
            'total_min',
            'total_max',
        ]);

        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';

        $this->resetPage();
        $this->clearBulkSelection();
    }

    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */

    public function exportExcel()
    {
        $purchases = $this->filteredQuery()
            ->with([
                'supplier:id,name',
                'employee:id,first_name,last_name',
                'warehouse:id,name',
                'purchaseItems.product:id,name',
            ])
            ->get();

        return Excel::download(
            new PurchaseExport($purchases),
            'purchases-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function exportSelectedExcel()
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if ($ids === []) {
            session()->flash('error', 'Select at least one purchase to export.');

            return null;
        }

        $purchases = Purchase::query()
            ->with([
                'supplier:id,name',
                'employee:id,first_name,last_name',
                'warehouse:id,name',
                'purchaseItems.product:id,name',
            ])
            ->whereIn('id', $ids)
            ->get();

        return Excel::download(
            new PurchaseExport($purchases),
            'selected-purchases-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Import
    |--------------------------------------------------------------------------
    */

    public function import(): void
    {
        $this->validate([
            'importFile' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:5120',
            ],
        ]);

        try {

            Excel::import(
                new PurchaseImport,
                $this->importFile
            );

            $this->reset('importFile');

            $this->resetPage();

            session()->flash(
                'message',
                'Purchases imported successfully.'
            );

        } catch (ValidationException $e) {

            $messages = collect($e->errors())
                ->flatten()
                ->implode(' ');

            session()->flash(
                'error',
                $messages
            );

        } catch (Throwable $e) {

            Log::error(
                'Purchase Import Error',
                [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            session()->flash(
                'error',
                $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function deletePurchase(
        int $purchaseId,
        PurchaseService $purchaseService
    ): void {

        try {

            $purchase = Purchase::findOrFail(
                $purchaseId
            );

            $purchaseService->delete($purchase);
            $this->selected = array_values(array_diff(
                $this->selected,
                [$purchaseId]
            ));
            $this->syncBulkSelection();
            $this->resetPage();

            session()->flash(
                'message',
                'Purchase deleted successfully.'
            );

        } catch (Throwable $e) {

            report($e);

            session()->flash(
                'error',
                $e->getMessage()
            );
        }
    }
};
