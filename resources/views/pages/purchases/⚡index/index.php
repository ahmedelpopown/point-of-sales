<?php

use App\Models\Purchase;
use App\Services\PurchaseService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Purchases')] class extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    #[Computed]
    public function purchases()
    {
        return Purchase::query()
            ->with([
                'supplier',
                'employee',
            ])
            ->when($this->search, function ($query) {

                $search = '%' . $this->search . '%';

                $query->where(function ($q) use ($search) {

                    $q->whereHas('supplier', function ($supplierQuery) use ($search) {
                        $supplierQuery->where('name', 'like', $search);
                    })

                    ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                        $employeeQuery
                            ->where('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });

                });
            })
            ->orderBy(
                $this->sortField,
                $this->sortDirection
            )
            ->paginate($this->perPage);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        $allowedFields = [
            'id',
            'total_price',
            'created_at',
        ];

        if (!in_array($field, $allowedFields)) {
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

    public function resetFilters()
    {
        $this->reset([
            'search',
            'sortField',
            'sortDirection',
        ]);

        $this->resetPage();
    }

    public function deletePurchase(
        $purchaseId,
        PurchaseService $purchaseService
    ) {
        try {

            $purchase = Purchase::findOrFail($purchaseId);

            $purchaseService->delete($purchase);

            session()->flash(
                'message',
                'Purchase deleted successfully.'
            );

        } catch (\Throwable $e) {

            report($e);

            session()->flash(
                'error',
                $e->getMessage()
            );
        }
    }
};
?>

