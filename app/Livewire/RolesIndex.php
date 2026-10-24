<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithBulkSelection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class RolesIndex extends Component
{
    use WithBulkSelection;
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public function getBulkVisibleIds(): array
    {
        return $this->roles->getCollection()->pluck('id')->all();
    }

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->where('guard_name', 'employee')
            ->with('permissions')
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
        $role->delete();

        $this->selected = array_values(array_diff(
            $this->selected,
            [$roleId]
        ));
        $this->syncBulkSelection();

        session()->flash('success', 'Role deleted successfully.');
    }

    public function render()
    {
        return view('pages.roles.index', [
            'roles' => $this->roles,
        ]);
    }
}
