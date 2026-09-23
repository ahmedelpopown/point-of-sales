<div class="min-h-screen bg-slate-50/70">

    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        {{-- =====================================================
             Header
        ====================================================== --}}

        <x-ui.page-header
            title="Users"
            badge="Customers"
            subtitle="Manage users, locations, contact information, and account data."
        >

            <x-slot:icon>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm">

                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 19h4a1 1 0 001-1v-1a4 4 0 00-4-4h-1"
                        />

                        <circle
                            cx="9"
                            cy="8"
                            r="4"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 19v-1a6 6 0 016-6h1a6 6 0 016 6v1"
                        />

                        <path
                            stroke-linecap="round"
                            d="M16 4.5a3.5 3.5 0 010 7"
                        />
                    </svg>

                </div>

            </x-slot:icon>


            <x-slot:actions>

                <div class="flex w-full flex-wrap gap-2 sm:w-auto">

                    {{-- Export Excel --}}
                    <x-ui.action-button
                        wire:click="exportExcel"
                        class="flex-1 sm:flex-none"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 3h10l4 4v14H5V3Z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 3v5h5"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8 14l2-2m0 0l2 2m-2-2v5"
                            />
                        </svg>

                        <span class="hidden sm:inline">
                            Export Excel
                        </span>

                        <span class="sm:hidden">
                            Excel
                        </span>
                    </x-ui.action-button>


                    {{-- Export PDF --}}
                    <x-ui.action-button
                        wire:click="exportPdf"
                        class="flex-1 sm:flex-none"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 3h9l3 3v15H6V3Z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M14 3v4h4"
                            />
                        </svg>

                        <span class="hidden sm:inline">
                            Export PDF
                        </span>

                        <span class="sm:hidden">
                            PDF
                        </span>
                    </x-ui.action-button>


                    {{-- Import --}}
                    <label
                        class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 sm:flex-none"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 16V4"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m8 8 4-4 4 4"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13v5a2 2 0 002 2h10a2 2 0 002-2v-5"
                            />
                        </svg>

                        <span class="hidden sm:inline">
                            Import Excel
                        </span>

                        <span class="sm:hidden">
                            Import
                        </span>

                        <input
                            type="file"
                            wire:model="importFile"
                            accept=".xlsx,.xls,.csv"
                            class="hidden"
                        >
                    </label>


                    {{-- Process Import --}}
                    @if($importFile)

                        <x-ui.action-button
                            variant="success"
                            wire:click="import"
                            loading-target="import"
                            class="w-full sm:w-auto"
                        >
                            Process Import
                        </x-ui.action-button>

                    @endif


                    {{-- Add User --}}
                    <a
                        href="{{ route('users.create') }}"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 sm:flex-none"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 6v12M6 12h12"
                            />
                        </svg>

                        <span class="hidden sm:inline">
                            Add User
                        </span>

                        <span class="sm:hidden">
                            Add
                        </span>
                    </a>

                </div>

            </x-slot:actions>

        </x-ui.page-header>


        {{-- =====================================================
             Alerts
        ====================================================== --}}

        <div class="space-y-3">

            @if(session()->has('message'))

                <x-ui.alert
                    type="success"
                    :message="session('message')"
                />

            @endif


            @if(session()->has('error'))

                <x-ui.alert
                    type="error"
                    :message="session('error')"
                />

            @endif

        </div>


        {{-- =====================================================
             Filters
        ====================================================== --}}

        <x-ui.filter-bar>

            {{-- Search --}}
            <div class="min-w-0 flex-1">

                <x-form.input
                    name="search"
                    placeholder="Search by name or email..."
                    wire:model.live.debounce.300ms="search"
                />

            </div>


        <div class="w-full lg:w-56">
    <x-form.searchable-select
        label="Governorate"
        name="governorate_id"
        model="governorate_id"
        :options="$this->governorates"
        optionValue="id"
        optionLabel="name"
        placeholder="All Governorates"
        searchPlaceholder="Search governorates..."
        :clearable="true"
    />
</div>

<div
    class="w-full lg:w-48"
    wire:key="city-filter-{{ $governorate_id ?: 'all' }}"
>
    <x-form.searchable-select
        label="City"
        name="city_id"
        model="city_id"
        :options="$this->cities"
        optionValue="id"
        optionLabel="name"
        placeholder="All Cities"
        searchPlaceholder="Search cities..."
        :clearable="true"
    />
</div>

            {{-- Reset --}}
            <x-ui.action-button
                wire:click="resetFilters"
                class="w-full lg:w-auto"
            >
                <svg
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 4v5h.58m0 0a8 8 0 111.42 8.42M4.58 9H9"
                    />
                </svg>

                Reset
            </x-ui.action-button>

        </x-ui.filter-bar>


        {{-- =====================================================
             Bulk Actions
        ====================================================== --}}

        @if(count($selected) > 0)

            <x-ui.bulk-actions
                :count="count($selected)"
            >

                <x-ui.action-button
                    wire:click="exportSelected"
                    class="flex-1 sm:flex-none"
                >
                    Export Selected
                </x-ui.action-button>


                <x-ui.action-button
                    variant="danger"
                    wire:click="bulkDelete"
                    wire:confirm="Are you sure you want to delete the selected users?"
                    class="flex-1 sm:flex-none"
                >
                    Delete Selected
                </x-ui.action-button>

            </x-ui.bulk-actions>

        @endif


        {{-- =====================================================
             Desktop Table
        ====================================================== --}}

        <div class="mt-8 hidden lg:block">

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50/80">

                            <tr>

                                {{-- Select All --}}
                                <th class="w-12 px-5 py-4">

                                    <input
                                        type="checkbox"
                                        wire:model.live="selectAll"
                                        class="h-4 w-4 rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    >

                                </th>


                                {{-- Name --}}
                                <th
                                    wire:click="sortBy('name')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 transition hover:text-slate-900"
                                >
                                    <div class="flex items-center gap-2">

                                        <span>Name</span>

                                        @if($sortField === 'name')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                        @endif

                                    </div>
                                </th>


                                {{-- Email --}}
                                <th
                                    wire:click="sortBy('email')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 transition hover:text-slate-900"
                                >
                                    <div class="flex items-center gap-2">

                                        <span>Email</span>

                                        @if($sortField === 'email')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                        @endif

                                    </div>
                                </th>


                                {{-- Governorate --}}
                                <th
                                    wire:click="sortBy('governorate_id')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 transition hover:text-slate-900"
                                >
                                    <div class="flex items-center gap-2">

                                        <span>Governorate</span>

                                        @if($sortField === 'governorate_id')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                        @endif

                                    </div>
                                </th>


                                {{-- City --}}
                                <th
                                    wire:click="sortBy('city_id')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 transition hover:text-slate-900"
                                >
                                    <div class="flex items-center gap-2">

                                        <span>City</span>

                                        @if($sortField === 'city_id')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                        @endif

                                    </div>
                                </th>


                                {{-- Age --}}
                                <th
                                    wire:click="sortBy('age')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 transition hover:text-slate-900"
                                >
                                    <div class="flex items-center gap-2">

                                        <span>Age</span>

                                        @if($sortField === 'age')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                        @endif

                                    </div>
                                </th>


                                {{-- Address --}}
                                <th
                                    wire:click="sortBy('address')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 transition hover:text-slate-900"
                                >
                                    <div class="flex items-center gap-2">

                                        <span>Address</span>

                                        @if($sortField === 'address')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                        @endif

                                    </div>
                                </th>


                                {{-- Gender --}}
                                <th
                                    wire:click="sortBy('gender')"
                                    class="cursor-pointer px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 transition hover:text-slate-900"
                                >
                                    <div class="flex items-center gap-2">

                                        <span>Gender</span>

                                        @if($sortField === 'gender')
                                            <span class="text-indigo-600">
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">
                                                ↕
                                            </span>
                                        @endif

                                    </div>
                                </th>


                                {{-- Actions --}}
                                <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @forelse($this->users as $user)

                                <tr
                                    wire:key="desktop-user-{{ $user->id }}"
                                    class="group transition-colors hover:bg-slate-50/70"
                                >

                                    {{-- Checkbox --}}
                                    <td class="px-5 py-4">

                                        <input
                                            type="checkbox"
                                            wire:model.live="selected"
                                            value="{{ $user->id }}"
                                            class="h-4 w-4 rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        >

                                    </td>


                                    {{-- Name --}}
                                    <td class="px-4 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-xs font-bold text-indigo-600">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>

                                            <p class="max-w-[180px] truncate text-sm font-semibold text-slate-900">
                                                {{ $user->name }}
                                            </p>

                                        </div>

                                    </td>


                                    {{-- Email --}}
                                    <td class="max-w-[220px] px-4 py-4">

                                        <p
                                            class="truncate text-sm text-slate-500"
                                            title="{{ $user->email }}"
                                        >
                                            {{ $user->email }}
                                        </p>

                                    </td>


                                    {{-- Governorate --}}
                                    <td class="whitespace-nowrap px-4 py-4">

                                        <span class="text-sm font-medium text-slate-700">
                                            {{ $user->governorate?->name ?? '—' }}
                                        </span>

                                    </td>


                                    {{-- City --}}
                                    <td class="whitespace-nowrap px-4 py-4">

                                        <span class="text-sm font-medium text-slate-700">
                                            {{ $user->city?->name ?? '—' }}
                                        </span>

                                    </td>


                                    {{-- Age --}}
                                    <td class="whitespace-nowrap px-4 py-4">

                                        <span class="text-sm font-semibold text-slate-700">
                                            {{ $user->age ?? '—' }}
                                        </span>

                                    </td>


                                    {{-- Address --}}
                                    <td class="max-w-[220px] px-4 py-4">

                                        <p
                                            class="truncate text-sm text-slate-500"
                                            title="{{ $user->address }}"
                                        >
                                            {{ $user->address ?: 'No address' }}
                                        </p>

                                    </td>


                                    {{-- Gender --}}
                                    <td class="whitespace-nowrap px-4 py-4">

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold
                                            {{
                                                $user->gender === 'male'
                                                    ? 'bg-blue-50 text-blue-700'
                                                    : 'bg-pink-50 text-pink-700'
                                            }}"
                                        >

                                            <span
                                                class="h-1.5 w-1.5 rounded-full
                                                {{
                                                    $user->gender === 'male'
                                                        ? 'bg-blue-500'
                                                        : 'bg-pink-500'
                                                }}"
                                            ></span>

                                            {{ ucfirst($user->gender ?? 'Unknown') }}

                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-5 py-4">

                                        <div class="flex justify-end gap-1">

                                            <a
                                                href="{{ route('users.edit', $user) }}"
                                                class="rounded-lg px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50"
                                            >
                                                Edit
                                            </a>

                                            <button
                                                type="button"
                                                wire:click="confirmDelete({{ $user->id }})"
                                                class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                                            >
                                                Delete
                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="9"
                                        class="px-6 py-16 text-center"
                                    >

                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                                            <svg
                                                class="h-6 w-6"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16 19h4a1 1 0 001-1v-1a4 4 0 00-4-4h-1"
                                                />

                                                <circle
                                                    cx="9"
                                                    cy="8"
                                                    r="4"
                                                />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M3 19v-1a6 6 0 016-6h1a6 6 0 016 6v1"
                                                />
                                            </svg>

                                        </div>

                                        <h3 class="mt-4 text-sm font-semibold text-slate-900">
                                            No users found
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Try changing your search or filters.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- =====================================================
             Mobile Cards
        ====================================================== --}}

        <div class="mt-6 space-y-3 lg:hidden">

            @forelse($this->users as $user)

                <article
                    wire:key="mobile-user-{{ $user->id }}"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    {{-- User Header --}}
                    <div class="flex items-start justify-between gap-3 p-4">

                        <div class="flex min-w-0 items-center gap-3">

                            <input
                                type="checkbox"
                                wire:model.live="selected"
                                value="{{ $user->id }}"
                                class="mt-1 h-4 w-4 shrink-0 rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            >


                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-600">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>


                            <div class="min-w-0">

                                <h3 class="truncate text-sm font-semibold text-slate-900">
                                    {{ $user->name }}
                                </h3>

                                <p class="mt-0.5 truncate text-xs text-slate-400">
                                    {{ $user->email }}
                                </p>

                            </div>

                        </div>


                        {{-- Gender --}}
                        <span
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold
                            {{
                                $user->gender === 'male'
                                    ? 'bg-blue-50 text-blue-700'
                                    : 'bg-pink-50 text-pink-700'
                            }}"
                        >

                            <span
                                class="h-1.5 w-1.5 rounded-full
                                {{
                                    $user->gender === 'male'
                                        ? 'bg-blue-500'
                                        : 'bg-pink-500'
                                }}"
                            ></span>

                            {{ ucfirst($user->gender ?? 'Unknown') }}

                        </span>

                    </div>


                    {{-- Main Data --}}
                    <div class="grid grid-cols-2 gap-px border-y border-slate-100 bg-slate-100">

                        {{-- Governorate --}}
                        <div class="bg-white px-4 py-3">

                            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                Governorate
                            </p>

                            <p class="mt-1 truncate text-sm font-semibold text-slate-800">
                                {{ $user->governorate?->name ?? '—' }}
                            </p>

                        </div>


                        {{-- City --}}
                        <div class="bg-white px-4 py-3">

                            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                City
                            </p>

                            <p class="mt-1 truncate text-sm font-semibold text-slate-800">
                                {{ $user->city?->name ?? '—' }}
                            </p>

                        </div>


                        {{-- Age --}}
                        <div class="bg-white px-4 py-3">

                            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                Age
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $user->age ?? '—' }}
                            </p>

                        </div>


                        {{-- Email --}}
                        <div class="bg-white px-4 py-3">

                            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                Email
                            </p>

                            <p
                                class="mt-1 truncate text-sm font-medium text-slate-700"
                                title="{{ $user->email }}"
                            >
                                {{ $user->email }}
                            </p>

                        </div>

                    </div>


                    {{-- Address --}}
                    <div class="px-4 py-3">

                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                            Address
                        </p>

                        <p class="mt-1 text-sm leading-5 text-slate-600">
                            {{ $user->address ?: 'No address' }}
                        </p>

                    </div>


                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50/70 px-4 py-3">

                        <a
                            href="{{ route('users.edit', $user) }}"
                            class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-indigo-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-indigo-50"
                        >
                            Edit
                        </a>

                        <button
                            type="button"
                            wire:click="confirmDelete({{ $user->id }})"
                            class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-red-50"
                        >
                            Delete
                        </button>

                    </div>

                </article>

            @empty

                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16 19h4a1 1 0 001-1v-1a4 4 0 00-4-4h-1"
                            />

                            <circle
                                cx="9"
                                cy="8"
                                r="4"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 19v-1a6 6 0 016-6h1a6 6 0 016 6v1"
                            />
                        </svg>

                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-slate-900">
                        No users found
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Try changing your search or filters.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- =====================================================
             Pagination
        ====================================================== --}}

        <div class="mt-5 overflow-x-auto">
            <div class="min-w-max">
                {{ $this->users->links() }}
            </div>
        </div>


        {{-- =====================================================
             Delete Confirmation
        ====================================================== --}}

        <x-ui.confirm-modal
            :show="$showDeleteModal"
            title="Delete User"
            message="Are you sure you want to delete this user? This action cannot be undone."
            confirm-text="Delete User"
            cancel-text="Cancel"
            confirm-action="deleteUser"
            cancel-action="$set('showDeleteModal', false)"
        />

    </div>

</div>