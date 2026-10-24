<?php

use App\Exports\UserExport;
use App\Imports\UserImport;
use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithDeleteConfirmation;
use App\Livewire\Concerns\WithImportExport;
use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Users')] class extends Component
{
    use WithBulkSelection;
    use WithDeleteConfirmation;
    use WithFileUploads;
    use WithImportExport;
    use WithPagination;

    public string $search = '';

    public $governorate_id = null;

    public $city_id = null;

    public string $gender = '';

    public string $email = '';

    public $age = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'governorate_id' => ['except' => null],
        'city_id' => ['except' => null],
        'email' => ['except' => ''],
        'gender' => ['except' => ''],
        'age' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    protected function sortableFields(): array
    {
        return [
            'id', 'name', 'email', 'age', 'gender',
            'created_at', 'governorate_id', 'city_id',
        ];
    }

    protected function filteredQuery()
    {
        $sortField = in_array($this->sortField, $this->sortableFields(), true)
            ? $this->sortField
            : 'name';

        $sortDirection = in_array($this->sortDirection, ['asc', 'desc'], true)
            ? $this->sortDirection
            : 'asc';

        $query = User::query()
            ->when(trim($this->search) !== '', fn ($query) => $query->search(trim($this->search)))
            ->when($this->governorate_id, fn ($query) => $query->where('governorate_id', $this->governorate_id))
            ->when($this->city_id, fn ($query) => $query->where('city_id', $this->city_id))
            ->when(trim($this->email) !== '', fn ($query) => $query->where('email', 'like', '%'.trim($this->email).'%'))
            ->when($this->gender !== '', fn ($query) => $query->where('gender', $this->gender))
            ->when($this->age !== '' && $this->age !== null, fn ($query) => $query->where('age', $this->age))
            ->with(['governorate:id,name', 'city:id,name']);

        if ($sortField === 'governorate_id') {
            return $query
                ->orderBy(
                    Governorate::query()
                        ->select('name')
                        ->whereColumn('governorates.id', 'users.governorate_id'),
                    $sortDirection
                )
                ->orderBy('users.id');
        }

        if ($sortField === 'city_id') {
            return $query
                ->orderBy(
                    City::query()
                        ->select('name')
                        ->whereColumn('cities.id', 'users.city_id'),
                    $sortDirection
                )
                ->orderBy('users.id');
        }

        return $query->orderBy('users.'.$sortField, $sortDirection);
    }

    #[Computed]
    public function users()
    {
        return $this->filteredQuery()->paginate($this->perPage);
    }

    #[Computed]
    public function governorates()
    {
        return Governorate::query()->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function cities()
    {
        if (! $this->governorate_id) {
            return collect();
        }

        return City::query()
            ->where('governorate_id', $this->governorate_id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getBulkVisibleIds(): array
    {
        return $this->users->getCollection()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function updated($property): void
    {
        if (in_array($property, [
            'search', 'governorate_id', 'city_id', 'email', 'gender', 'age',
            'perPage', 'sortField', 'sortDirection',
        ], true)) {
            $this->resetPage();
            $this->clearSelected();
        }
    }

    public function updatedGovernorateId(): void
    {
        $this->city_id = null;
        $this->resetPage();
        $this->clearSelected();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search', 'governorate_id', 'city_id', 'email', 'gender', 'age',
        ]);

        $this->sortField = 'name';
        $this->sortDirection = 'asc';
        $this->clearSelected();
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, $this->sortableFields(), true)) {
            return;
        }

        $this->sortDirection = $this->sortField === $field
            && $this->sortDirection === 'asc' ? 'desc' : 'asc';

        $this->sortField = $field;
        $this->clearSelected();
        $this->resetPage();
    }

    public function clearSelected(): void
    {
        $this->clearBulkSelection();
    }

    public function bulkDelete(): void
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if ($ids === []) {
            session()->flash('error', 'Select at least one user first.');

            return;
        }

        $count = User::query()->whereIn('id', $ids)->delete();

        $this->clearSelected();
        $this->resetPage();

        session()->flash('message', $count.' selected user(s) deleted successfully.');
    }

    protected function deleteModel(): string
    {
        return User::class;
    }

    protected function deleteSuccessMessage(): string
    {
        return 'User deleted successfully.';
    }

    protected function modelClass(): string
    {
        return User::class;
    }

    protected function exportClass(): string
    {
        return UserExport::class;
    }

    protected function importClass(): string
    {
        return UserImport::class;
    }

    protected function pdfView(): string
    {
        return 'users.pdf';
    }

    protected function resourceName(): string
    {
        return 'users';
    }

    protected function resourceKey(): string
    {
        return 'users';
    }

    protected function indexRoute(): string
    {
        return 'users.index';
    }
};
