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

    // Permission keys match the names checked by the sidebar.
    public array $modules = [
        'roles' => 'Roles',
        'users' => 'Users',
        'employees' => 'Employees',
        'suppliers' => 'Suppliers',
        'purchases' => 'Purchases',
        'products' => 'Products',
        'orders' => 'Orders',
        'installments' => 'Installments',
        'reports' => 'Reports',
    ];

    public array $actions = ['view', 'create', 'update', 'delete'];

    // These permissions do not follow the standard view/create/update/delete pattern.
    public array $specialPermissions = [
        'view dashboard' => 'View Dashboard',
        'adjust stock' => 'Adjust Stock',
        'view stock movements' => 'View Stock Movements',
        'transfer stock' => 'Transfer Stock',
    ];

    // Bulk selection for the role cards shown beside the permission form.
    public array $selectedRoleIds = [];
    public bool $selectAllRoles = false;

    public function mount(): void
    {
        $this->createPermissions();
    }

    private function createPermissions(): void
    {
        foreach ($this->modules as $module => $label) {
            foreach ($this->actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action} {$module}",
                    'guard_name' => 'employee',
                ]);
            }
        }

        foreach (array_keys($this->specialPermissions) as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'employee',
            ]);
        }
    }

    public function saveRole(): void
    {
        $uniqueRoleName = Rule::unique('roles', 'name')
            ->where('guard_name', 'employee');

        if ($this->roleId !== null) {
            $uniqueRoleName->ignore($this->roleId);
        }

        $validated = $this->validate([
            'roleName' => ['required', 'string', 'max:100', $uniqueRoleName],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', 'employee'),
            ],
        ]);

        DB::transaction(function () use ($validated): void {
            $role = $this->roleId !== null
                ? Role::query()->where('guard_name', 'employee')->findOrFail($this->roleId)
                : new Role(['guard_name' => 'employee']);

            $role->name = trim($validated['roleName']);
            $role->guard_name = 'employee';
            $role->save();

            $permissions = Permission::query()
                ->where('guard_name', 'employee')
                ->whereIn('name', $validated['selectedPermissions'] ?? [])
                ->get();

            $role->syncPermissions($permissions);
        });

        session()->flash(
            'success',
            $this->roleId !== null
                ? 'Role updated successfully.'
                : 'Role created successfully.'
        );

        $this->resetForm();
    }

    public function editRole(int $roleId): void
    {
        $role = Role::query()
            ->where('guard_name', 'employee')
            ->with(['permissions' => fn ($query) => $query->where('guard_name', 'employee')])
            ->findOrFail($roleId);

        $this->roleId = (int) $role->id;
        $this->roleName = $role->name;

        $manageablePermissions = $this->permissionNamesForForm();
        $this->selectedPermissions = $role->permissions
            ->pluck('name')
            ->filter(fn (string $name) => in_array($name, $manageablePermissions, true))
            ->values()
            ->all();
    }

    public function deleteRole(int $roleId): void
    {
        $role = Role::query()
            ->where('guard_name', 'employee')
            ->findOrFail($roleId);

        $role->delete();
        $this->selectedRoleIds = array_values(array_diff(
            $this->normalizeIds($this->selectedRoleIds),
            [$roleId]
        ));
        $this->syncRoleSelectAll();

        if ($this->roleId === $roleId) {
            $this->resetForm();
        }

        session()->flash('success', 'Role deleted successfully.');
    }

    public function bulkDeleteRoles(): void
    {
        $ids = $this->normalizeIds($this->selectedRoleIds);

        if ($ids === []) {
            session()->flash('error', 'Select at least one role first.');
            return;
        }

        $roles = Role::query()
            ->where('guard_name', 'employee')
            ->whereIn('id', $ids)
            ->get();

        $deletedIds = [];

        // Delete models individually to preserve model events and package behavior.
        foreach ($roles as $role) {
            $role->delete();
            $deletedIds[] = (int) $role->id;
        }

        if ($this->roleId !== null && in_array($this->roleId, $deletedIds, true)) {
            $this->resetForm();
        }

        $this->clearRoleSelection();
        session()->flash('success', count($deletedIds) . ' role(s) deleted successfully.');
    }

    public function updatedSelectAllRoles(mixed $checked): void
    {
        $this->selectAllRoles = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
        $this->selectedRoleIds = $this->selectAllRoles
            ? $this->roleIds()
            : [];
    }

    public function updatedSelectedRoleIds(): void
    {
        $this->syncRoleSelectAll();
    }

    public function selectAllModule(string $module): void
    {
        if (! array_key_exists($module, $this->modules)) {
            return;
        }

        $modulePermissions = collect($this->actions)
            ->map(fn (string $action) => "{$action} {$module}")
            ->all();

        $allSelected = collect($modulePermissions)->every(
            fn (string $permission) => in_array($permission, $this->selectedPermissions, true)
        );

        $this->selectedPermissions = $allSelected
            ? array_values(array_diff($this->selectedPermissions, $modulePermissions))
            : array_values(array_unique(array_merge($this->selectedPermissions, $modulePermissions)));
    }

    public function selectAllPermissions(): void
    {
        // The form uses the employee guard, not the web guard.
        $permissionNames = $this->permissionNamesForForm();
        $availablePermissions = Permission::query()
            ->where('guard_name', 'employee')
            ->whereIn('name', $permissionNames)
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $selected = array_values(array_unique($this->selectedPermissions));
        $allSelected = $availablePermissions !== []
            && count(array_intersect($availablePermissions, $selected)) === count($availablePermissions);

        // Toggle the permissions shown by this form only, not unrelated/stale permissions.
        $this->selectedPermissions = $allSelected ? [] : $availablePermissions;
    }

    public function resetForm(): void
    {
        $this->reset(['roleId', 'roleName', 'selectedPermissions']);
        $this->resetValidation();
    }

    public function clearRoleSelection(): void
    {
        $this->selectedRoleIds = [];
        $this->selectAllRoles = false;
    }

    private function permissionNamesForForm(): array
    {
        $modulePermissions = collect(array_keys($this->modules))
            ->flatMap(fn (string $module) => collect($this->actions)
                ->map(fn (string $action) => "{$action} {$module}"));

        return $modulePermissions
            ->merge(array_keys($this->specialPermissions))
            ->unique()
            ->values()
            ->all();
    }

    private function roleIds(): array
    {
        return Role::query()
            ->where('guard_name', 'employee')
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function syncRoleSelectAll(): void
    {
        $allRoleIds = $this->roleIds();
        $selectedIds = $this->normalizeIds($this->selectedRoleIds);

        $this->selectedRoleIds = $selectedIds;
        $this->selectAllRoles = $allRoleIds !== []
            && count(array_intersect($allRoleIds, $selectedIds)) === count($allRoleIds);
    }

    private function normalizeIds(array $ids): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', $ids),
            fn (int $id): bool => $id > 0
        )));
    }

    public function render()
    {
        return view('pages.roles.create', [
            'roles' => Role::query()
                ->where('guard_name', 'employee')
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
