
<?php

use App\Livewire\Concerns\WithBulkSelection;
use App\Models\Installment;
use App\Services\InstallmentService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Installments')] class extends Component
{
    use WithBulkSelection;
    use WithPagination;

    private const SORTABLE_FIELDS = [
        'id',
        'total_with_interest',
        'remaining_amount',
        'start_date',
        'created_at',
    ];

    public string $search = '';

    public int $perPage = 10;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public function getBulkVisibleIds(): array
    {
        return $this->installments->getCollection()->pluck('id')->all();
    }

    #[Computed]
    public function installments()
    {
        $sortField = in_array(
            $this->sortField,
            self::SORTABLE_FIELDS,
            true
        )
            ? $this->sortField
            : 'created_at';

        $sortDirection = in_array(
            strtolower($this->sortDirection),
            ['asc', 'desc'],
            true
        )
            ? strtolower($this->sortDirection)
            : 'desc';

        $perPage = max(1, min($this->perPage, 100));

        return Installment::query()
            ->with([
                'order',
                'installmentPlan',
            ])
            ->when(trim($this->search) !== '', function ($query) {
                $search = '%'.trim($this->search).'%';

                $query->whereHas(
                    'installmentPlan',
                    fn ($planQuery) => $planQuery->where(
                        'name',
                        'like',
                        $search
                    )
                );
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORTABLE_FIELDS, true)) {
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

    public function deleteInstallment(
        int $id,
        InstallmentService $service
    ): void {
        try {
            $installment = Installment::findOrFail($id);

            $service->delete($installment);
            $this->selected = array_values(array_diff(
                $this->selected,
                [$id]
            ));
            $this->syncBulkSelection();
            $this->resetPage();

            unset($this->installments);

            session()->flash(
                'message',
                'Installment deleted successfully.'
            );
        } catch (Throwable $exception) {
            report($exception);

            session()->flash(
                'error',
                'Unable to delete this installment. Please try again.'
            );
        }
    }
};
?>
