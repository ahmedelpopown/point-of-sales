
<div class="px-4 py-6 sm:px-6 lg:px-8">

    {{-- Header --}}
    <x-ui.page-header
        title="Employees"
        subtitle="Manage employees, departments, positions, and status."
    >
        <x-slot:actions>

            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">

                <button
                    wire:click="exportExcel"
                    class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Export Excel
                </button>

                <button
                    wire:click="exportPdf"
                    class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Export PDF
                </button>

                <label class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Import Excel

                    <input
                        type="file"
                        wire:model="importFile"
                        accept=".xlsx,.xls,.csv"
                        class="hidden"
                    >
                </label>

                @if($importFile)
                    <button
                        wire:click="import"
                        class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700"
                    >
                        Process Import
                    </button>
                @endif

                <a
                    href="{{ route('employees.create') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    Add Employee
                </a>

            </div>

        </x-slot:actions>
    </x-ui.page-header>


    {{-- Alerts --}}
    @if(session('message'))
        <div class="mt-6">
            <x-ui.alert
                type="success"
                :message="session('message')"
            />
        </div>
    @endif

    @if(session('error'))
        <div class="mt-6">
            <x-ui.alert
                type="error"
                :message="session('error')"
            />
        </div>
    @endif


    {{-- Filters --}}
    <x-ui.filter-bar>

        <div class="w-full flex-1">
            <x-form.input
                name="search"
                placeholder="Search employees..."
                wire:model.live.debounce.300ms="search"
            />
        </div>

        <div class="w-full lg:w-56">
            <x-form.searchable-select
                name="department"
                model="department"
                :options="$this->departments"
                optionValue="id"
                optionLabel="name"
                placeholder="All Departments"
                searchPlaceholder="Search departments..."
            />
        </div>

        <div class="w-full lg:w-48">
            <x-form.searchable-select
                name="status"
                model="status"
                :options="$this->statuses"
                optionValue="id"
                optionLabel="name"
                placeholder="All Status"
                searchPlaceholder="Search status..."
            />
        </div>

        <button
            wire:click="resetFilters"
            class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
        >
            Reset
        </button>

    </x-ui.filter-bar>


    {{-- Bulk Actions --}}
    @if(count($selected))
        <div class="mt-6">

            <x-ui.bulk-actions :count="count($selected)">

                <button
                    wire:click="exportSelected"
                    class="inline-flex min-h-10 items-center rounded-xl bg-indigo-100 px-4 text-sm font-semibold text-indigo-700 hover:bg-indigo-200"
                >
                    Export Selected
                </button>

                <button
                    wire:click="exportSelectedExcel"
                    class="inline-flex min-h-10 items-center rounded-xl bg-emerald-100 px-4 text-sm font-semibold text-emerald-700 hover:bg-emerald-200"
                >
                    Excel Selected
                </button>

                <button
                    wire:click="bulkDelete"
                    wire:confirm="Are you sure you want to delete the selected employees?"
                    class="inline-flex min-h-10 items-center rounded-xl bg-red-100 px-4 text-sm font-semibold text-red-700 hover:bg-red-200"
                >
                    Delete Selected
                </button>

            </x-ui.bulk-actions>

        </div>
    @endif


    {{-- Data Table --}}
    <div class="mt-8">
<x-ui.data-table
    :items="$this->employees"
    :columns="[
        [
            'field' => 'full_name',
            'label' => 'Name',
            'type' => 'avatar',
            'sortable' => true,
        ],
        [
            'field' => 'email',
            'label' => 'Email',
        ],
        [
            'field' => 'department',
            'label' => 'Department',
            'sortable' => true,
        ],
        [
            'field' => 'position',
            'label' => 'Position',
        ],
        [
            'field' => 'status',
            'label' => 'Status',
            'type' => 'badge',
            'badgeClasses' => [
                'active' => 'bg-emerald-50 text-emerald-700',
                'inactive' => 'bg-slate-100 text-slate-600',
            ],
        ],
    ]"
    emptyText="No employees found."
    :selectable="true"
    selectedModel="selected"
    selectAllModel="selectAll"
    :sortField="$sortField"
    :sortDirection="$sortDirection"
    editRoute="employees.edit"
    deleteMethod="confirmDelete"
    rowKey="id"
/>
    </div>


    {{-- Delete Modal --}}
   <x-ui.confirm-modal
    :show="$showDeleteModal"
    title="Delete Employee"
    message="Are you sure you want to delete this employee? This action cannot be undone."
    confirmText="Delete Employee"
    cancelText="Cancel"
    confirmAction="deleteItem"
    cancelAction="showDeleteModal = false"
/>

</div>