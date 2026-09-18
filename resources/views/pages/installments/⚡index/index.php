<?php

use App\Models\Installment;
use App\Services\InstallmentService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Installments')] class extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    #[Computed]
    public function installments()
    {
        return Installment::query()
            ->with([
                'order',
                'installmentPlan',
            ])
            ->when($this->search, function ($query) {

                $search = '%' . $this->search . '%';

                $query->whereHas(
                    'installmentPlan',
                    function ($q) use ($search) {
                        $q->where(
                            'name',
                            'like',
                            $search
                        );
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
            'total_with_interest',
            'remaining_amount',
            'start_date',
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

    public function deleteInstallment(
        $id,
        InstallmentService $service
    ) {
        try {

            $installment = Installment::findOrFail($id);

            $service->delete($installment);

            session()->flash(
                'message',
                'Installment deleted successfully.'
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