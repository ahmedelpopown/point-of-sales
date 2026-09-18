<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionManager extends Component
{
    public ?int $roleId = null;

    public string $roleName = '';

    public array $selectedPermissions = [];

    public array $modules = [
        'order' => 'Order',
        'user' => 'User',
        'purchase' => 'Purchase',
        'product' => 'Product',
        'employee' => 'Employee',
        'instalment' => 'Instalment',
        'supplier' => 'Suppliers',
        'report' => 'Report',
    ];

    public array $actions = [
        'view',
        'create',
        'update',
        'delete',
    ];

    public function mount(): void
    {
        $this->createPermissions();
    }

    /**
     * Create all system permissions if they don't exist.
     */
    private function createPermissions(): void
    {
        foreach ($this->modules as $module => $moduleLabel) {
            foreach ($this->actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action} {$module}",
                    'guard_name' => 'employee',
                ]);
            }
        }
    }

    public function saveRole(): void
    {
        $validated = $this->validate([
            'roleName' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->ignore($this->roleId),
            ],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => [
                'string',
                Rule::exists('permissions', 'name')
                    ->where('guard_name', 'employee'),
            ],
        ]);

        DB::transaction(function () use ($validated) {

            if ($this->roleId) {

                $role = Role::findOrFail($this->roleId);

                $role->update([
                    'name' => $validated['roleName'],
                ]);
            } else {

                $role = Role::create([
                    'name' => $validated['roleName'],
                    'guard_name' => 'web',
                ]);
            }

            $role->syncPermissions($validated['selectedPermissions']);
        });

        session()->flash(
            'success',
            $this->roleId
                ? 'Role updated successfully.'
                : 'Role created successfully.'
        );

        $this->resetForm();
    }

    public function editRole(int $roleId): void
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        $this->roleId = $role->id;
        $this->roleName = $role->name;

        $this->selectedPermissions = $role
            ->permissions
            ->pluck('name')
            ->toArray();
    }

    public function deleteRole(int $roleId): void
    {
        $role = Role::findOrFail($roleId);

        $role->delete();

        if ($this->roleId === $roleId) {
            $this->resetForm();
        }

        session()->flash(
            'success',
            'Role deleted successfully.'
        );
    }

    public function selectAllModule(string $module): void
    {
        $modulePermissions = collect($this->actions)
            ->map(fn($action) => "{$action} {$module}")
            ->toArray();

        $allSelected = collect($modulePermissions)
            ->every(
                fn($permission) =>
                in_array(
                    $permission,
                    $this->selectedPermissions,
                    true
                )
            );

        if ($allSelected) {
            $this->selectedPermissions = array_values(
                array_diff(
                    $this->selectedPermissions,
                    $modulePermissions
                )
            );
        } else {
            $this->selectedPermissions = array_values(
                array_unique(
                    array_merge(
                        $this->selectedPermissions,
                        $modulePermissions
                    )
                )
            );
        }
    }

    public function selectAllPermissions(): void
    {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->pluck('name')
            ->toArray();

        if (count($this->selectedPermissions) === count($permissions)) {
            $this->selectedPermissions = [];
            return;
        }

        $this->selectedPermissions = $permissions;
    }

    public function resetForm(): void
    {
        $this->reset([
            'roleId',
            'roleName',
            'selectedPermissions',
        ]);
    }

    public function render()
    {
        return view('pages.roles.create', [
            'roles' => Role::query()
                ->withCount('permissions')
                ->latest()
                ->get(),


            'permissions' => Permission::query()
                ->where('guard_name', 'employee')
                ->get()
                ->keyBy('name'),
        ]);
    }
}
