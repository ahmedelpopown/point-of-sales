<?php

use App\Models\Order;
use App\Services\OrderService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Orders')] class extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $perPage = 10;

    #[Computed]
    public function orders()
    {
        return Order::query()
            ->with([
                'user',
                'employee',
            ])
            ->when($this->search, function ($query) {

                $search = '%' . $this->search . '%';

                $query->whereHas(
                    'user',
                    function ($q) use ($search) {
                        $q->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    }
                )
                ->orWhereHas(
                    'employee',
                    function ($q) use ($search) {
                        $q->where('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search);
                    }
                );
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
        $allowed = [
            'id',
            'total',
            'created_at',
        ];

        if (!in_array($field, $allowed)) {
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

    public function deleteOrder(
        $id,
        OrderService $service
    ) {
        try {

            $order = Order::findOrFail($id);

            $service->delete($order);

            session()->flash(
                'message',
                'Order deleted successfully.'
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

