<?php

use App\Exports\OrdersExport;
use App\Exports\OrdersImportTemplate;
use App\Imports\OrderImport;
use App\Livewire\Concerns\WithBulkSelection;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

new #[Title('Orders')] class extends Component
{
    use WithBulkSelection, WithFileUploads, WithPagination;

    public $importFile;

    public string $search = '';

    public string $customerId = '';

    public string $employeeId = '';

    public string $shopId = '';

    public string $productId = '';

    public array $paymentMethods = [];

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $minTotal = '';

    public string $maxTotal = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public function getBulkVisibleIds(): array
    {
        return $this->orders->getCollection()->pluck('id')->all();
    }

    public function clearSelection(): void
    {
        $this->clearBulkSelection();
    }

    protected function sortedOrdersQuery()
    {
        return $this->ordersQuery()
            ->orderBy(
                $this->sortField,
                $this->sortDirection
            );
    }

    public function exportSelected()
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if ($ids === []) {
            session()->flash('error', 'Select at least one order to export.');

            return null;
        }

        return Excel::download(
            new OrdersExport(
                $this->sortedOrdersQuery()
                    ->whereIn('id', $ids)
            ),
            'selected-orders-'.now()->format('Y-m-d-H-i-s').'.xlsx'
        );
    }

    public function deleteSelected(OrderService $service): void
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if ($ids === []) {
            session()->flash('error', 'Select at least one order first.');

            return;
        }

        try {

            foreach ($ids as $id) {

                $order = Order::find($id);

                if ($order) {
                    $service->delete($order);
                }
            }

            $count = count($ids);

            $this->clearSelection();

            session()->flash(
                'message',
                "{$count} orders deleted successfully."
            );

            $this->resetPage();

        } catch (Throwable $e) {

            report($e);

            session()->flash(
                'error',
                $e->getMessage()
            );
        }
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function employees()
    {
        return Employee::query()
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    #[Computed]
    public function shops()
    {
        return Shop::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function orders()
    {
        return $this->sortedOrdersQuery()
            ->paginate($this->perPage);
    }

    public function exportExcel()
    {
        return Excel::download(
            new OrdersExport($this->sortedOrdersQuery()),
            'orders-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function downloadImportTemplate()
    {
        return Excel::download(
            new OrdersImportTemplate,
            'orders-import-template.xlsx'
        );
    }

    public function exportPdf()
    {
        $orders = $this->sortedOrdersQuery()->get();

        return response()->streamDownload(
            function () use ($orders) {

                echo Pdf::loadView(
                    'pages.orders.pdf',
                    compact('orders')
                )->output();

            },
            'orders-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function updated($property): void
    {
        if (in_array($property, [
            'search',
            'customerId',
            'employeeId',
            'shopId',
            'productId',
            'paymentMethods',
            'dateFrom',
            'dateTo',
            'minTotal',
            'maxTotal',
            'perPage',
        ], true)) {
            $this->resetPage();

            $this->clearSelection();
        }
    }

    public function sortBy(string $field): void
    {
        $allowed = [
            'id',
            'total',
            'created_at',
        ];

        if (! in_array($field, $allowed, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc'
                    ? 'desc'
                    : 'asc';

            $this->resetPage();
            $this->clearSelection();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
        $this->clearSelection();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'customerId',
            'employeeId',
            'shopId',
            'productId',
            'perPage',
            'paymentMethods',
            'dateFrom',
            'dateTo',
            'minTotal',
            'maxTotal',
        ]);

        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;

        $this->resetPage();
    }

    protected function importValidationRules(): array
    {
        return [
            'importFile' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:10240',
            ],
        ];
    }

    public function importExcel(): void
    {

        $this->validate($this->importValidationRules());

        try {

            Excel::import(
                new OrderImport,
                $this->importFile
            );

            $this->reset('importFile');

            session()->flash(
                'message',
                'Orders imported successfully.'
            );

            $this->resetPage();

        } catch (Throwable $e) {

            report($e);

            session()->flash(
                'error',
                $e->getMessage()
            );
        }
    }

    protected function ordersQuery()
    {
        return Order::query()
            ->with([
                'user',
                'employee',
            ])
            ->withCount('orderItems')

            ->when($this->search, function ($query) {

                $search = "%{$this->search}%";

                $query->where(function ($query) use ($search) {

                    $query
                        ->where('id', 'like', $search)

                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', $search)
                                ->orWhere('email', 'like', $search);
                        })

                        ->orWhereHas('employee', function ($q) use ($search) {
                            $q->where('first_name', 'like', $search)
                                ->orWhere('last_name', 'like', $search);
                        })

                        ->orWhereHas('orderItems.product', function ($q) use ($search) {
                            $q->where('name', 'like', $search);
                        });

                });

            })

            ->when(
                $this->customerId,
                fn ($query) => $query->where('user_id', $this->customerId)
            )

            ->when(
                $this->employeeId,
                fn ($query) => $query->where('employee_id', $this->employeeId)
            )

            ->when($this->shopId, function ($query) {
                $query->whereHas(
                    'orderItems',
                    fn ($q) => $q->where('shop_id', $this->shopId)
                );
            })

            ->when($this->productId, function ($query) {
                $query->whereHas(
                    'orderItems',
                    fn ($q) => $q->where('product_id', $this->productId)
                );
            })

            ->when($this->paymentMethods, function ($query) {

                $query->where(function ($query) {

                    if (in_array('installment', $this->paymentMethods, true)) {
                        $query->whereHas('installments');
                    }

                    if (in_array('cash', $this->paymentMethods, true)) {
                        $query->orWhereDoesntHave('installments');
                    }

                });

            })

            ->when(
                $this->dateFrom,
                fn ($query) => $query->whereDate(
                    'created_at',
                    '>=',
                    $this->dateFrom
                )
            )

            ->when(
                $this->dateTo,
                fn ($query) => $query->whereDate(
                    'created_at',
                    '<=',
                    $this->dateTo
                )
            )

            ->when(
                $this->minTotal !== '',
                fn ($query) => $query->where(
                    'total',
                    '>=',
                    $this->minTotal
                )
            )

            ->when(
                $this->maxTotal !== '',
                fn ($query) => $query->where(
                    'total',
                    '<=',
                    $this->maxTotal
                )
            );
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function deleteOrder(
        int $id,
        OrderService $service
    ): void {
        try {

            $order = Order::findOrFail($id);

            $service->delete($order);
            $this->selected = array_values(array_diff(
                $this->selected,
                [$id]
            ));
            $this->syncBulkSelection();
            $this->resetPage();

            session()->flash(
                'message',
                'Order deleted successfully.'
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
