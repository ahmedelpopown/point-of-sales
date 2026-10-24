<div class="min-h-screen bg-slate-50/70">
    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-ui.page-header
            title="Employees"
            subtitle="Manage employees, departments, positions, and status."
        >
            <x-slot:actions>
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                    <x-ui.action-button wire:click="exportExcel" class="w-full sm:w-auto">Export Excel</x-ui.action-button>
                    <x-ui.action-button wire:click="exportPdf" class="w-full sm:w-auto">Export PDF</x-ui.action-button>

                    <label class="inline-flex min-h-11 w-full cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 sm:w-auto">
                        Import Excel
                        <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="sr-only">
                    </label>

                    @if($importFile)
                        <x-ui.action-button variant="success" wire:click="import" loading-target="import" class="w-full sm:w-auto">Process Import</x-ui.action-button>
                    @endif

                    <a href="{{ route('employees.create') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 sm:w-auto">
                        Add Employee
                    </a>
                </div>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="space-y-3">
            @if(session()->has('message'))
                <x-ui.alert type="success" :message="session('message')" />
            @endif
            @if(session()->has('error'))
                <x-ui.alert type="error" :message="session('error')" />
            @endif
        </div>

        <x-ui.filter-bar>
            <div class="w-full flex-1">
                <x-form.input name="search" placeholder="Search employees..." wire:model.live.debounce.300ms="search" />
            </div>

            <div class="w-full lg:w-56">
                <x-form.searchable-select
                    name="department"
                    model="department"
                    :options="$this->departments"
                    option-value="id"
                    option-label="name"
                    placeholder="All Departments"
                    search-placeholder="Search departments..."
                    :clearable="true"
                />
            </div>

            <div class="w-full lg:w-48">
                <x-form.searchable-select
                    name="status"
                    model="status"
                    :options="$this->statuses"
                    option-value="id"
                    option-label="name"
                    placeholder="All Status"
                    search-placeholder="Search status..."
                    :clearable="true"
                />
            </div>

            <x-ui.action-button wire:click="resetFilters" class="w-full shrink-0 lg:w-auto">Reset</x-ui.action-button>
        </x-ui.filter-bar>

        @if(count($selected) > 0)
            <div class="mt-6">
                <x-ui.bulk-actions :count="count($selected)" item-label="employee" items-label="employees">
                    <x-ui.action-button wire:click="exportSelected" class="flex-1 sm:flex-none">Export Selected</x-ui.action-button>
                    <x-ui.action-button variant="success" wire:click="exportSelectedExcel" class="flex-1 sm:flex-none">Excel Selected</x-ui.action-button>
                    <x-ui.action-button variant="danger" wire:click="bulkDelete" wire:confirm="Are you sure you want to delete the selected employees?" class="flex-1 sm:flex-none">Delete Selected</x-ui.action-button>
                    <x-ui.action-button wire:click="clearBulkSelection" class="flex-1 sm:flex-none">Clear selection</x-ui.action-button>
                </x-ui.bulk-actions>
            </div>
        @endif

        <div class="mt-8">
            <x-ui.data-table
                :items="$this->employees"
                :columns="[
                    ['field' => 'full_name', 'label' => 'Name', 'type' => 'avatar', 'sortable' => true, 'sortField' => 'first_name'],
                    ['field' => 'email', 'label' => 'Email', 'truncate' => true],
                    ['field' => 'department', 'label' => 'Department', 'sortable' => true],
                    ['field' => 'position', 'label' => 'Position'],
                    ['field' => 'status', 'label' => 'Status', 'type' => 'badge', 'badgeClasses' => ['active' => 'bg-emerald-50 text-emerald-700', 'inactive' => 'bg-slate-100 text-slate-600']],
                ]"
                empty-text="No employees found"
                empty-description="Try changing your search or filters."
                :selectable="true"
                selected-model="selected"
                select-all-model="selectAll"
                :sort-field="$sortField"
                :sort-direction="$sortDirection"
                edit-route="employees.edit"
                delete-method="confirmDelete"
                row-key="id"
            />
        </div>

        <x-ui.confirm-modal
            :show="$showDeleteModal"
            title="Delete Employee"
            message="Are you sure you want to delete this employee? This action cannot be undone."
            confirm-text="Delete Employee"
            cancel-text="Cancel"
            confirm-action="deleteItem"
            cancel-action="$set('showDeleteModal', false)"
            modal-id="delete-employee-modal-title"
        />
    </div>
</div>
