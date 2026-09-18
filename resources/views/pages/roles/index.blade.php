<div class="container-fluid py-4">

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Roles
            </h4>

            <p class="text-muted mb-0">
                Manage employee roles and their permissions
            </p>
        </div>

        <a
            href="{{ route('admin.roles.permissions') }}"
            class="btn btn-primary"
        >
            + Create Role
        </a>

    </div>


    {{-- Roles Table --}}
    <div class="card shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>
                                Role
                            </th>

                            <th>
                                Permissions
                            </th>

                            <th>
                                Guard
                            </th>

                            <th class="text-end">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($roles as $role)

                            <tr>

                                {{-- ID --}}
                                <td>
                                    {{ $role->id }}
                                </td>


                                {{-- Role Name --}}
                                <td>

                                    <div class="fw-semibold">
                                        {{ $role->name }}
                                    </div>

                                    <small class="text-muted">
                                        Created:
                                        {{ $role->created_at?->format('Y-m-d') }}
                                    </small>

                                </td>


                                {{-- Permissions --}}
                                <td>

                                    <div class="mb-2">

                                        <span class="badge bg-primary">
                                            {{ $role->permissions_count }}
                                            permissions
                                        </span>

                                    </div>

                                    <div class="d-flex flex-wrap gap-1">

                                        @foreach ($role->permissions as $permission)

                                            <span class="badge bg-light text-dark border">
                                                {{ $permission->name }}
                                            </span>

                                        @endforeach

                                    </div>

                                </td>


                                {{-- Guard --}}
                                <td>

                                    <span class="badge bg-secondary">
                                        {{ $role->guard_name }}
                                    </span>

                                </td>


                                {{-- Actions --}}
                                <td class="text-end">

                                    <div class="d-flex justify-content-end gap-2">

                                        <a
                                            href="{{ route('admin.roles.permissions', ['role' => $role->id]) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Edit
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            wire:click="deleteRole({{ $role->id }})"
                                            wire:confirm="Are you sure you want to delete this role?"
                                        >
                                            Delete
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center py-5"
                                >

                                    <div class="text-muted">
                                        No roles found.
                                    </div>

                                    <a
                                        href="{{ route('admin.roles.permissions') }}"
                                        class="btn btn-primary mt-3"
                                    >
                                        Create First Role
                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}

    </div>

</div>