<div class="container-fluid py-4">
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title mb-0">Roles</h3>
            <a href="{{ route('admin.roles.permissions') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-shield mr-1"></i> Manage Roles & Permissions
            </a>
        </div>

        @if (count($selected) > 0)
            <div class="card-body pb-0">
                <div class="d-flex align-items-center justify-content-between flex-wrap border rounded p-3">
                    <span class="font-weight-bold mb-2 mb-md-0">
                        {{ count($selected) }} role(s) selected
                    </span>
                    <div class="d-flex flex-wrap" style="gap: .5rem">
                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm"
                            wire:click="clearBulkSelection"
                        >
                            Clear Selection
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            wire:click="deleteSelected"
                            wire:confirm="Are you sure you want to delete the selected roles?"
                            wire:loading.attr="disabled"
                            wire:target="deleteSelected"
                        >
                            <span wire:loading.remove wire:target="deleteSelected">Delete Selected</span>
                            <span wire:loading wire:target="deleteSelected">Deleting...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th style="width: 44px">
                            <input
                                type="checkbox"
                                aria-label="Select all roles on this page"
                                wire:model.live="selectAll"
                            >
                        </th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Created</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr wire:key="role-row-{{ $role->id }}">
                            <td>
                                <input
                                    type="checkbox"
                                    aria-label="Select {{ $role->name }}"
                                    value="{{ $role->id }}"
                                    wire:model.live="selected"
                                >
                            </td>
                            <td class="align-middle">
                                <strong>{{ $role->name }}</strong>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-info">{{ $role->permissions_count }}</span>
                            </td>
                            <td class="align-middle">{{ $role->created_at?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td class="text-right align-middle">
                                <a
                                    href="{{ route('admin.roles.permissions') }}"
                                    class="btn btn-outline-primary btn-xs"
                                    title="Manage roles and permissions"
                                >
                                    <i class="fas fa-edit"></i> Manage
                                </a>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-xs"
                                    wire:click="deleteRole({{ $role->id }})"
                                    wire:confirm="Are you sure you want to delete the role '{{ $role->name }}'?"
                                >
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No employee roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($roles->hasPages())
            <div class="card-footer clearfix">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
