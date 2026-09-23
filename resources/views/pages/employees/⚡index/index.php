<?php
use App\Livewire\Concerns\WithDeleteConfirmation;
use App\Livewire\Concerns\WithImportExport;
use App\Livewire\Concerns\WithTableManagement;
use App\Models\Employee;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Employees')] class extends Component
{
  use WithPagination;
    use WithFileUploads;
    use WithTableManagement;
    use WithDeleteConfirmation;
    use WithImportExport;

    public $importFile;

    public string $search = '';

    public string $department = '';

    public string $status = '';

    public string $sortField = 'first_name';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public array $selected = [];

    public bool $selectAll = false;


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


    protected function tableItems()
    {
        return $this->employees;
    }


    #[Computed]
    public function employees()
    {
        return $this->filteredQuery()
            ->paginate($this->perPage);
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
            ])
        ) {
            $this->resetPage();
        }
    }


    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'department',
            'status',
        ]);

        $this->resetPage();
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
        if (!$this->selected) {
            return;
        }

        Employee::whereIn(
            'id',
            $this->selected
        )->delete();

        $this->selected = [];
        $this->selectAll = false;

        session()->flash(
            'message',
            'Selected employees deleted successfully.'
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
    return \App\Exports\EmployeeExport::class;
}

protected function importClass(): string
{
    return \App\Imports\EmployeeImport::class;
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
?>