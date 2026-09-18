<div class="container-fluid py-4">

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">

        {{-- Roles --}}
        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Roles
                    </h5>

                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        wire:click="resetForm"
                    >
                        + New Role
                    </button>
                </div>

                <div class="card-body">

                    @forelse ($roles as $role)

                        <div
                            class="border rounded p-3 mb-2
                            {{ $roleId === $role->id ? 'border-primary bg-light' : '' }}"
                        >

                            <div class="d-flex justify-content-between align-items-center">

                                <div>
                                    <strong>
                                        {{ $role->name }}
                                    </strong>

                                    <div class="text-muted small">
                                        {{ $role->permissions_count }}
                                        permissions
                                    </div>
                                </div>

                                <div class="d-flex gap-1">

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

                        <div class="text-center text-muted py-4">
                            No roles found.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- Role Form --}}
        <div class="col-md-8">

            <div class="card shadow-sm">

                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $roleId ? 'Edit Role' : 'Create Role' }}
                    </h5>
                </div>

                <div class="card-body">

                    <form wire:submit="saveRole">

                        {{-- Role Name --}}
                        <div class="mb-4">

                            <label class="form-label">
                                Role Name
                            </label>

                            <input
                                type="text"
                                class="form-control @error('roleName') is-invalid @enderror"
                                wire:model="roleName"
                                placeholder="Example: Manager"
                            >

                            @error('roleName')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- All Permissions --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <h6 class="mb-0">
                                Permissions
                            </h6>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                wire:click="selectAllPermissions"
                            >
                                Select / Deselect All
                            </button>

                        </div>


                        {{-- Modules --}}
                        <div class="row">

                            @foreach ($modules as $module => $moduleLabel)

                                @php
                                    $modulePermissions = collect($actions)
                                        ->map(fn ($action) => "{$action} {$module}")
                                        ->toArray();

                                    $moduleSelectedCount = collect(
                                        $modulePermissions
                                    )
                                        ->filter(
                                            fn ($permission) =>
                                            in_array(
                                                $permission,
                                                $selectedPermissions,
                                                true
                                            )
                                        )
                                        ->count();
                                @endphp

                                <div class="col-md-6 mb-4">

                                    <div class="border rounded">

                                        {{-- Module Header --}}
                                        <div class="bg-light border-bottom p-3">

                                            <div class="d-flex justify-content-between align-items-center">

                                                <strong>
                                                    {{ $moduleLabel }}
                                                </strong>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    wire:click="selectAllModule('{{ $module }}')"
                                                >
                                                    {{ $moduleSelectedCount === count($actions)
                                                        ? 'Deselect All'
                                                        : 'Select All'
                                                    }}
                                                </button>

                                            </div>

                                        </div>


                                        {{-- Permissions --}}
                                        <div class="p-3">

                                            @foreach ($actions as $action)

                                                @php
                                                    $permissionName = "{$action} {$module}";
                                                @endphp

                                                <div class="form-check mb-2">

                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        id="permission_{{ md5($permissionName) }}"
                                                        value="{{ $permissionName }}"
                                                        wire:model="selectedPermissions"
                                                    >

                                                    <label
                                                        class="form-check-label"
                                                        for="permission_{{ md5($permissionName) }}"
                                                    >
                                                        {{ ucfirst($action) }}
                                                        {{ $moduleLabel }}
                                                    </label>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>


                        {{-- Buttons --}}
                        <div class="d-flex gap-2 mt-3">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                {{ $roleId ? 'Update Role' : 'Create Role' }}
                            </button>

                            @if ($roleId)

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    wire:click="resetForm"
                                >
                                    Cancel
                                </button>

                            @endif

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>