<div class="container-fluid py-4">
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        {{-- Roles list and bulk actions --}}
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Roles</h5>
                    <button type="button" class="btn btn-primary btn-sm" wire:click="resetForm">
                        + New Role
                    </button>
                </div>

                <div class="card-body">
                    @if ($roles->isNotEmpty())
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="mb-0 d-flex align-items-center" style="gap: .5rem">
                                <input type="checkbox" wire:model.live="selectAllRoles" aria-label="Select all roles">
                                <span>Select all roles</span>
                            </label>
                            <span class="text-muted small">{{ count($selectedRoleIds) }} selected</span>
                        </div>
                    @endif

                    @if (count($selectedRoleIds) > 0)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex flex-column" style="gap: .5rem">
                                <span class="font-weight-bold">
                                    {{ count($selectedRoleIds) }} role(s) selected
                                </span>
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    wire:click="bulkDeleteRoles"
                                    wire:confirm="Are you sure you want to delete the selected roles? This action cannot be undone."
                                    wire:loading.attr="disabled"
                                    wire:target="bulkDeleteRoles"
                                >
                                    <span wire:loading.remove wire:target="bulkDeleteRoles">Delete Selected</span>
                                    <span wire:loading wire:target="bulkDeleteRoles">Deleting...</span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="clearRoleSelection">
                                    Clear Selection
                                </button>
                            </div>
                        </div>
                    @endif

                    @forelse ($roles as $role)
                        <div
                            wire:key="role-card-{{ $role->id }}"
                            class="border rounded p-3 mb-2 {{ $roleId === $role->id ? 'border-primary bg-light' : '' }}"
                        >
                            <div class="d-flex justify-content-between align-items-start" style="gap: .5rem">
                                <label class="d-flex align-items-start mb-0" style="gap: .5rem; min-width: 0">
                                    <input
                                        type="checkbox"
                                        class="mt-1"
                                        value="{{ $role->id }}"
                                        wire:model.live="selectedRoleIds"
                                        aria-label="Select role {{ $role->name }}"
                                    >
                                    <span>
                                        <strong>{{ $role->name }}</strong>
                                        <span class="d-block text-muted small">
                                            {{ $role->permissions_count }} permissions
                                        </span>
                                    </span>
                                </label>

                                <div class="d-flex flex-shrink-0" style="gap: .25rem">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:click="editRole({{ $role->id }})"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="deleteRole({{ $role->id }})"
                                        wire:confirm="Are you sure you want to delete this role?"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No roles found.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Role and permission form --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">{{ $roleId ? 'Edit Role' : 'Create Role' }}</h5>
                </div>

                <div class="card-body">
                    <form wire:submit="saveRole">
                        <div class="mb-4">
                            <label for="role-name" class="form-label">Role Name</label>
                            <input
                                id="role-name"
                                type="text"
                                class="form-control @error('roleName') is-invalid @enderror"
                                wire:model="roleName"
                                placeholder="Example: Manager"
                            >
                            @error('roleName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: .5rem">
                            <h6 class="mb-0">Permissions</h6>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                wire:click="selectAllPermissions"
                                wire:loading.attr="disabled"
                                wire:target="selectAllPermissions"
                            >
                                <span wire:loading.remove wire:target="selectAllPermissions">Select / Deselect All</span>
                                <span wire:loading wire:target="selectAllPermissions">Updating...</span>
                            </button>
                        </div>

                        <div class="row">
                            @foreach ($modules as $module => $moduleLabel)
                                @php
                                    $modulePermissions = collect($actions)
                                        ->map(fn ($action) => "{$action} {$module}")
                                        ->toArray();

                                    $moduleSelectedCount = collect($modulePermissions)
                                        ->filter(fn ($permission) => in_array($permission, $selectedPermissions, true))
                                        ->count();
                                @endphp

                                <div class="col-md-6 mb-4" wire:key="permission-module-{{ $module }}">
                                    <div class="border rounded h-100">
                                        <div class="bg-light border-bottom p-3">
                                            <div class="d-flex justify-content-between align-items-center" style="gap: .5rem">
                                                <strong>{{ $moduleLabel }}</strong>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    wire:click="selectAllModule('{{ $module }}')"
                                                >
                                                    {{ $moduleSelectedCount === count($actions) ? 'Deselect All' : 'Select All' }}
                                                </button>
                                            </div>
                                        </div>

                                        <div class="p-3">
                                            @foreach ($actions as $action)
                                                @php $permissionName = "{$action} {$module}"; @endphp
                                                <div class="form-check mb-2" wire:key="permission-{{ md5($permissionName) }}">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        id="permission_{{ md5($permissionName) }}"
                                                        value="{{ $permissionName }}"
                                                        wire:model="selectedPermissions"
                                                    >
                                                    <label class="form-check-label" for="permission_{{ md5($permissionName) }}">
                                                        {{ ucfirst($action) }} {{ $moduleLabel }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border rounded p-3 mb-4">
                            <h6 class="mb-3">Other Permissions</h6>
                            <div class="row">
                                @foreach ($specialPermissions as $permissionName => $permissionLabel)
                                    <div class="col-md-6 mb-2" wire:key="special-permission-{{ md5($permissionName) }}">
                                        <div class="form-check">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                id="permission_{{ md5($permissionName) }}"
                                                value="{{ $permissionName }}"
                                                wire:model="selectedPermissions"
                                            >
                                            <label class="form-check-label" for="permission_{{ md5($permissionName) }}">
                                                {{ $permissionLabel }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @error('selectedPermissions.*')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="d-flex flex-wrap mt-3" style="gap: .5rem">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="saveRole">
                                <span wire:loading.remove wire:target="saveRole">{{ $roleId ? 'Update Role' : 'Create Role' }}</span>
                                <span wire:loading wire:target="saveRole">Saving...</span>
                            </button>

                            @if ($roleId)
                                <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
