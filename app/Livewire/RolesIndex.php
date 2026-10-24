<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithBulkSelection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class RolesIndex extends Component
{
    use WithPagination;
    use WithBulkSelection;

    protected string $paginationTheme = 'bootstrap';

    public function getBulkVisibleIds(): array
    {
        return $this->roles
            ->getCollection()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->where('guard_name', 'employee')
            ->withCount('permissions')
            ->latest()
            ->paginate(10);
    }

    public function deleteRole(int $roleId): void
    {
        $role = Role::query()
            ->where('guard_name', 'employee')
            ->findOrFail($roleId);

        $role->delete();
        $this->selected = array_values(array_diff(
            array_map('intval', $this->selected),
            [$roleId]
        ));

        $this->refreshAfterDelete();
        session()->flash('success', 'Role deleted successfully.');
    }

    public function deleteSelected(): void
    {
        $ids = array_values(array_unique(array_filter(
            array_map('intval', $this->selected),
            fn (int $id): bool => $id > 0
        )));

        if ($ids === []) {
            session()->flash('error', 'Select at least one role first.');
            return;
        }

        $roles = Role::query()
            ->where('guard_name', 'employee')
            ->whereIn('id', $ids)
            ->get();

        $deletedCount = 0;

        // Delete individually so Eloquent model events are not bypassed.
        foreach ($roles as $role) {
            $role->delete();
            $deletedCount++;
        }

        $this->clearBulkSelection();
        $this->refreshAfterDelete();

        session()->flash('success', $deletedCount . ' role(s) deleted successfully.');
    }

    private function refreshAfterDelete(): void
    {
        unset($this->roles);

        if ($this->roles->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
            unset($this->roles);
        }

        $this->syncBulkSelection();
    }

    public function render()
    {
        return view('pages.roles.index', [
            'roles' => $this->roles,
        ]);
    }
}
