<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class RolesIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public function deleteRole(int $roleId): void
    {
        $role = Role::query()
            ->where('guard_name', 'employee')
            ->findOrFail($roleId);

        $role->delete();

        session()->flash('success', 'Role deleted successfully.');
    }

    public function render()
    {
        $roles = Role::query()
            ->where('guard_name', 'employee')
            ->with('permissions')
            ->withCount('permissions')
            ->latest()
            ->paginate(10);

        return view('pages.roles.index', [
            'roles' => $roles,
        ]);
    }
}