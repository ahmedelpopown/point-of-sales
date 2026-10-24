<div class="min-h-screen bg-slate-50/70">
    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-ui.page-header
            title="Users"
            badge="Customers"
            subtitle="Manage users, locations, contact information, and account data."
        >
            <x-slot:icon>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 19h4a1 1 0 001-1v-1a4 4 0 00-4-4h-1" />
                        <circle cx="9" cy="8" r="4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-1a6 6 0 016-6h1a6 6 0 016 6v1" />
                        <path stroke-linecap="round" d="M16 4.5a3.5 3.5 0 010 7" />
                    </svg>
                </div>
            </x-slot:icon>

            <x-slot:actions>
                <div class="flex w-full flex-wrap gap-2 sm:w-auto">
                    <x-ui.action-button wire:click="exportExcel" class="flex-1 sm:flex-none">
                        <span class="hidden sm:inline">Export Excel</span>
                        <span class="sm:hidden">Excel</span>
                    </x-ui.action-button>

                    <x-ui.action-button wire:click="exportPdf" class="flex-1 sm:flex-none">
                        <span class="hidden sm:inline">Export PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </x-ui.action-button>

                    <label class="inline-flex flex-1 cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 sm:flex-none">
                        <span class="hidden sm:inline">Import Excel</span>
                        <span class="sm:hidden">Import</span>
                        <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="sr-only">
                    </label>

                    @error('importFile')
                        <p class="w-full text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @if($importFile)
                        <x-ui.action-button variant="success" wire:click="import" loading-target="import" class="w-full sm:w-auto">
                            Process Import
                        </x-ui.action-button>
                    @endif

                    <a href="{{ route('users.create') }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 sm:flex-none">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12M6 12h12" />
                        </svg>
                        <span class="hidden sm:inline">Add User</span>
                        <span class="sm:hidden">Add</span>
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
            <div class="min-w-0 flex-1">
                <x-form.input name="search" placeholder="Search by name or email..." wire:model.live.debounce.300ms="search" />
            </div>

            <div class="w-full lg:w-56">
                <x-form.searchable-select
                    label="Governorate"
                    name="governorate_id"
                    model="governorate_id"
                    :options="$this->governorates"
                    option-value="id"
                    option-label="name"
                    placeholder="All Governorates"
                    search-placeholder="Search governorates..."
                    :clearable="true"
                />
            </div>

            <div class="w-full lg:w-48" wire:key="city-filter-{{ $governorate_id ?: 'all' }}">
                <x-form.searchable-select
                    label="City"
                    name="city_id"
                    model="city_id"
                    :options="$this->cities"
                    option-value="id"
                    option-label="name"
                    placeholder="All Cities"
                    search-placeholder="Search cities..."
                    :clearable="true"
                />
            </div>

            <div class="w-full sm:w-44">
                <x-form.input name="email" type="email" placeholder="Filter by email" wire:model.live.debounce.300ms="email" />
            </div>

            <div class="w-full sm:w-32">
                <x-form.input name="age" type="number" min="1" max="120" placeholder="Age" wire:model.live.debounce.300ms="age" />
            </div>

            <div class="w-full sm:w-36">
                <label for="user-gender-filter" class="sr-only">Filter by gender</label>
                <select id="user-gender-filter" wire:model.live="gender" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                    <option value="">All genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <x-ui.action-button wire:click="resetFilters" class="w-full shrink-0 lg:w-auto">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.58m0 0a8 8 0 111.42 8.42M4.58 9H9" />
                </svg>
                Reset
            </x-ui.action-button>
        </x-ui.filter-bar>

        @if(count($selected) > 0)
            <div class="mt-6">
                <x-ui.bulk-actions :count="count($selected)" item-label="user" items-label="users">
                    <x-ui.action-button wire:click="exportSelected" class="flex-1 sm:flex-none">Export Selected PDF</x-ui.action-button>
                    <x-ui.action-button wire:click="exportSelectedExcel" class="flex-1 sm:flex-none">Export Selected Excel</x-ui.action-button>
                    <x-ui.action-button variant="danger" wire:click="bulkDelete" wire:confirm="Are you sure you want to delete the selected users?" class="flex-1 sm:flex-none">Delete Selected</x-ui.action-button>
                    <x-ui.action-button wire:click="clearSelected" class="flex-1 sm:flex-none">Clear selection</x-ui.action-button>
                </x-ui.bulk-actions>
            </div>
        @endif

        <div class="mt-8">
            <x-ui.data-table
                :items="$this->users"
                :columns="[
                    ['field' => 'name', 'label' => 'Name', 'type' => 'avatar', 'sortable' => true],
                    ['field' => 'email', 'label' => 'Email', 'sortable' => true, 'truncate' => true],
                    ['field' => 'governorate.name', 'label' => 'Governorate', 'sortable' => true, 'sortField' => 'governorate_id'],
                    ['field' => 'city.name', 'label' => 'City', 'sortable' => true, 'sortField' => 'city_id'],
                    ['field' => 'age', 'label' => 'Age', 'sortable' => true],
                    ['field' => 'address', 'label' => 'Address', 'truncate' => true, 'wrapMobile' => true],
                    ['field' => 'gender', 'label' => 'Gender', 'type' => 'badge', 'badgeClasses' => ['male' => 'bg-blue-50 text-blue-700', 'female' => 'bg-pink-50 text-pink-700'], 'emptyText' => 'Unknown'],
                ]"
                empty-text="No users found"
                empty-description="Try changing your search or filters."
                :selectable="true"
                selected-model="selected"
                select-all-model="selectAll"
                :sort-field="$sortField"
                :sort-direction="$sortDirection"
                edit-route="users.edit"
                delete-method="confirmDelete"
                row-key="id"
            />
        </div>

        <x-ui.confirm-modal
            :show="$showDeleteModal"
            title="Delete User"
            message="Are you sure you want to delete this user? This action cannot be undone."
            confirm-text="Delete User"
            cancel-text="Cancel"
            confirm-action="deleteItem"
            cancel-action="$set('showDeleteModal', false)"
            modal-id="delete-user-modal-title"
        />
    </div>
</div>
