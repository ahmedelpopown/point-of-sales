<?php

use App\Exports\EmployeeExport;
use App\Imports\EmployeeImport;
use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithDeleteConfirmation;
use App\Livewire\Concerns\WithImportExport;
use App\Models\Employee;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Employees')] class extends Component
{
    use WithBulkSelection;
    use WithDeleteConfirmation;
    use WithFileUploads;
    use WithImportExport;
    use WithPagination;

    public $importFile = null;

    public string $search = '';

    public string $department = '';

    public string $status = '';

    public string $sortField = 'first_name';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    protected function filteredQuery()
    {
        return Employee::query()
            ->when(
                $this->search,
                fn ($q) => $q->search($this->search)
            )
            ->when(
                $this->department,
                fn ($q) => $q->where(
                    'department',
                    $this->department
                )
            )
            ->when(
                $this->status,
                fn ($q) => $q->where(
                    'status',
                    $this->status
                )
            )
            ->orderBy(
                $this->sortField,
                $this->sortDirection
            );
    }

    #[Computed]
    public function employees()
    {
        return $this->filteredQuery()
            ->paginate($this->perPage);
    }

    public function getBulkVisibleIds(): array
    {
        return $this->employees->getCollection()->pluck('id')->all();
    }

    #[Computed]
    public function departments()
    {
        return Employee::query()
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values()
            ->map(fn ($department) => [
                'id' => $department,
                'name' => $department,
            ]);
    }

    #[Computed]
    public function statuses()
    {
        return collect([
            [
                'id' => 'active',
                'name' => 'Active',
            ],
            [
                'id' => 'inactive',
                'name' => 'Inactive',
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    public function updated($property): void
    {
        if (
            in_array($property, [
                'search',
                'department',
                'status',
                'perPage',
            ], true)
        ) {
            $this->resetPage();
            $this->clearBulkSelection();
        }
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['first_name', 'department'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc'
                ? 'desc'
                : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'department',
            'status',
        ]);

        $this->resetPage();
        $this->clearBulkSelection();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    protected function deleteModel(): string
    {
        return Employee::class;
    }

    protected function deleteSuccessMessage(): string
    {
        return 'Employee deleted successfully.';
    }

    public function bulkDelete(): void
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if ($ids === []) {
            session()->flash('error', 'Select at least one employee first.');

            return;
        }

        $count = Employee::query()->whereIn('id', $ids)->delete();

        $this->clearBulkSelection();
        $this->resetPage();

        session()->flash(
            'message',
            $count.' selected employee(s) deleted successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */
    protected function modelClass(): string
    {
        return Employee::class;
    }

    protected function exportClass(): string
    {
        return EmployeeExport::class;
    }

    protected function importClass(): string
    {
        return EmployeeImport::class;
    }

    protected function pdfView(): string
    {
        return 'employees.pdf';
    }

    protected function resourceName(): string
    {
        return 'employees';
    }

    protected function resourceKey(): string
    {
        return 'employees';
    }

    protected function indexRoute(): string
    {
        return 'employees.index';
    }
};
