<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).





















        Employees ✅ : 1        |    
                                 | ->1   ✅
        Users ✅     : 2        |


                    Suppliers : 3  ✅ |   
                    Purchases : 4   |   ---> 2
                    Products  : 5   |   

                            installments: 6 -> notifications  |                        
                                                              |---> 3
                            Orders : 7  -> paypal integrations|



                                                                     Reports : 8|-->4


















تمام نبدء بي ال user انا عاوز نفس ال design لو ينفع او حاجه قريبه منه 
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
________________________________
<?php

use App\Exports\UserExport;
use App\Imports\UserImport;
use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

new #[Title('User')] class extends Component {
    use WithPagination, WithFileUploads;

    public $importFile;

    public $search = '';

    public $governorate_id = null;

    public $city_id = null;

    public $gender = '';

    public $email = '';

    public $age = '';

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $perPage = 10;

    public $selected = [];

    public $selectAll = false;

    public $showDeleteModal = false;

    public $UserToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'governorate_id' => ['except' => null],
        'city_id' => ['except' => null],
        'email' => ['except' => ''],
        'gender' => ['except' => ''],
        'age' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];


    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function users()
    {
        return User::query()
            ->when(
                $this->search,
                fn ($q) => $q->search($this->search)
            )

            ->when(
                $this->governorate_id,
                fn ($q) => $q->where(
                    'governorate_id',
                    $this->governorate_id
                )
            )

            ->when(
                $this->city_id,
                fn ($q) => $q->where(
                    'city_id',
                    $this->city_id
                )
            )

            ->when(
                $this->age !== '',
                fn ($q) => $q->where(
                    'age',
                    $this->age
                )
            )

            ->when(
                $this->email,
                fn ($q) => $q->where(
                    'email',
                    $this->email
                )
            )

            ->when(
                $this->gender,
                fn ($q) => $q->where(
                    'gender',
                    $this->gender
                )
            )

            ->with([
                'governorate:id,name',
                'city:id,name',
            ])

            ->orderBy(
                $this->sortField,
                $this->sortDirection
            )

            ->paginate($this->perPage);
    }


    /*
    |--------------------------------------------------------------------------
    | Governorates
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function governorates()
    {
        return Governorate::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }


    /*
    |--------------------------------------------------------------------------
    | Cities
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function cities()
    {
        if (!$this->governorate_id) {
            return collect();
        }

        return City::query()
            ->where(
                'governorate_id',
                $this->governorate_id
            )
            ->orderBy('name')
            ->get(['id', 'name']);
    }


    /*
    |--------------------------------------------------------------------------
    | Emails
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function emails()
    {
        return User::query()
            ->whereNotNull('email')
            ->distinct()
            ->pluck('email')
            ->sort()
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Ages
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function ages()
    {
        return User::query()
            ->whereNotNull('age')
            ->distinct()
            ->pluck('age')
            ->sort()
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Reactive Filters
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }


    public function updatedGovernorateId($value): void
    {
        // المحافظة اتغيرت
        // إذن المدينة القديمة أصبحت غير صالحة
        $this->city_id = null;

        $this->resetPage();
    }


    public function updatedCityId(): void
    {
        $this->resetPage();
    }


    public function updatedEmail(): void
    {
        $this->resetPage();
    }


    public function updatedGender(): void
    {
        $this->resetPage();
    }


    public function updatedAge(): void
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'governorate_id',
            'city_id',
            'email',
            'gender',
            'age',
        ]);

        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc'
                    ? 'desc'
                    : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function confirmDelete($userId): void
    {
        $this->UserToDelete = $userId;
        $this->showDeleteModal = true;
    }


    public function deleteUser(): void
    {
        if (!$this->UserToDelete) {
            return;
        }

        User::find($this->UserToDelete)?->delete();

        $this->showDeleteModal = false;

        $this->UserToDelete = null;

        session()->flash(
            'message',
            'User Deleted Successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Bulk Selection
    |--------------------------------------------------------------------------
    */

    public function updateSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->users
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }


    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        User::whereIn('id', $this->selected)->delete();

        $this->selected = [];

        $this->selectAll = false;

        session()->flash(
            'message',
            'Selected User Deleted Successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Export PDF
    |--------------------------------------------------------------------------
    */

    public function exportPdf()
    {
        $users = $this->getFilteredUsers();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'dejavusans',
        ]);

        $html = view(
            'users.pdf',
            compact('users')
        )->render();

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            function () use ($mpdf) {
                echo $mpdf->Output('', 'S');
            },
            'users.pdf'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Export Selected PDF
    |--------------------------------------------------------------------------
    */

    public function exportSelected()
    {
        $users = User::whereIn(
            'id',
            $this->selected
        )->get();

        $pdf = Pdf::loadView(
            'users.pdf',
            compact('users')
        );

        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->stream();
            },
            'users-' . date('Y-m-d') . '.pdf'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Export Excel
    |--------------------------------------------------------------------------
    */

    public function exportExcel()
    {
        return Excel::download(
            new UserExport($this->getFilteredUsers()),
            'users-' . date('Y-m-d') . '.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Export Selected Excel
    |--------------------------------------------------------------------------
    */

    public function exportSelectedExcel()
    {
        $users = User::whereIn(
            'id',
            $this->selected
        )->get();

        return Excel::download(
            new UserExport($users),
            'users-' . date('Y-m-d') . '.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Filtered Users
    |--------------------------------------------------------------------------
    */

    public function getFilteredUsers()
    {
        return User::query()

            ->when(
                $this->search,
                fn ($q) => $q->search($this->search)
            )

            ->when(
                $this->governorate_id,
                fn ($q) => $q->where(
                    'governorate_id',
                    $this->governorate_id
                )
            )

            ->when(
                $this->city_id,
                fn ($q) => $q->where(
                    'city_id',
                    $this->city_id
                )
            )

            ->when(
                $this->email,
                fn ($q) => $q->where(
                    'email',
                    $this->email
                )
            )

            ->when(
                $this->gender,
                fn ($q) => $q->where(
                    'gender',
                    $this->gender
                )
            )

            ->when(
                $this->age !== '',
                fn ($q) => $q->where(
                    'age',
                    $this->age
                )
            )

            ->orderBy(
                $this->sortField,
                $this->sortDirection
            )

            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Import
    |--------------------------------------------------------------------------
    */

    public function import()
    {
        $this->validate([
            'importFile' =>
                'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {

            Excel::import(
                new UserImport(),
                $this->importFile
            );

            session()->flash(
                'message',
                'Users imported successfully.'
            );

            $this->importFile = null;

            return redirect('/users');

        } catch (ValidationException $e) {

            $failures = $e->failures();

            $errors = [];

            foreach ($failures as $failure) {

                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }

            Log::warning(
                'User import validation failed',
                ['errors' => $errors]
            );

            session()->flash(
                'error',
                'Some rows could not be imported.'
            );

        } catch (\Throwable $th) {

            Log::error(
                'Import Error',
                [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine(),
                    'trace' => $th->getTraceAsString(),
                ]
            );

            session()->flash(
                'error',
                $th->getMessage()
            );

            return redirect('/users');
        }
    }
};
_________________________
<?php

use App\Livewire\Forms\UserForm;
use App\Models\City;
use App\Models\Governorate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create User')] class extends Component
{
    public UserForm $form;

    #[Computed]
    public function governorates()
    {
        return Governorate::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function cities()
    {
        return $this->form->governorate_id
            ? City::query()
                ->where('governorate_id', $this->form->governorate_id)
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect();
    }

    public function updated($property): void
    {
        if ($property === 'form.governorate_id') {
            $this->form->city_id = null;
        }
    }

    public function save()
    {
        $this->form->store();

        session()->flash('message', 'User created successfully.');

        return $this->redirectRoute('users.index');
    }
};

 
?>
@include('pages.users.form', [
    'title' => 'Create User',
    'subtitle' => 'Create a new user and assign their information.',
    'submitText' => 'Create User',
    'loadingText' => 'Creating...',
    'isEdit' => false,
])
__________________________________
<?php

use App\Livewire\Forms\UserForm;
use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit User')] class extends Component
{
    public UserForm $form;

    public function mount(User $user): void
    {
        $this->form->setUser($user);
    }

    #[Computed]
    public function governorates()
    {
        return Governorate::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function cities()
    {
        return $this->form->governorate_id
            ? City::query()
                ->where('governorate_id', $this->form->governorate_id)
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect();
    }

    public function updated($property): void
    {
        if ($property === 'form.governorate_id') {
            $this->form->city_id = null;
        }
    }

    public function save()
    {
        $this->form->update();

        session()->flash('message', 'User updated successfully.');

        return $this->redirectRoute('users.index');
    }
};

 
?>
@include('pages.users.form', [
    'title' => 'Edit User',
    'subtitle' => 'Update the user account and personal information.',
    'submitText' => 'Update User',
    'loadingText' => 'Updating...',
    'isEdit' => true,
])____________
<div class="px-4 py-6 sm:px-6 lg:px-8">

    <x-ui.page-header
        :title="$title"
        :subtitle="$subtitle"
        :back-url="route('users.index')"
        back-text="Back to Users"
    />

    <form wire:submit="save" class="mt-8">

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            <div class="space-y-6 xl:col-span-2">

                {{-- Personal Information --}}
                <x-form.section
                    title="Personal Information"
                    description="Basic contact and account information."
                >
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <x-form.input
                            label="Full Name"
                            name="form.name"
                            placeholder="Enter full name"
                            wire:model.blur="form.name"
                            autocomplete="name"
                            required
                        />

                        <x-form.input
                            label="Email Address"
                            name="form.email"
                            type="email"
                            placeholder="Enter email address"
                            wire:model.blur="form.email"
                            autocomplete="email"
                            required
                        />

                        <x-form.input
                            label="Password"
                            name="form.password"
                            type="password"
                            :placeholder="$isEdit
                                ? 'Leave blank to keep current password'
                                : 'Enter password'"
                            :hint="$isEdit
                                ? 'Leave this field empty to keep the current password.'
                                : null"
                            wire:model.blur="form.password"
                            autocomplete="new-password"
                            :required="!$isEdit"
                        />

                        <x-form.input
                            label="Phone Number"
                            name="form.phone"
                            placeholder="Enter phone number"
                            wire:model.blur="form.phone"
                            inputmode="tel"
                            autocomplete="tel"
                        />

                        <div class="sm:col-span-2">
                            <x-form.input
                                label="Address"
                                name="form.address"
                                placeholder="Enter address"
                                wire:model.blur="form.address"
                                autocomplete="street-address"
                            />
                        </div>

                    </div>
                </x-form.section>


                {{-- User Details --}}
                <x-form.section
                    title="User Details"
                    description="Location, age, and gender information."
                >
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                          <div class="relative z-20">
        <x-form.searchable-select
            label="Governorate"
            name="form.governorate_id"
            model="form.governorate_id"
            :options="$this->governorates"
            optionValue="id"
            optionLabel="name"
            placeholder="Select Governorate"
            searchPlaceholder="Search governorates..."
            :clearable="true"
            required
        />
    </div>
 <div
        class="relative z-10"
        wire:key="city-{{ $form->governorate_id ?: 'all' }}"
    >
        <x-form.searchable-select
            label="City"
            name="form.city_id"
            model="form.city_id"
            :options="$this->cities"
            optionValue="id"
            optionLabel="name"
            placeholder="Select City"
            searchPlaceholder="Search cities..."
            :clearable="true"
            required
        />
    </div>
                        <x-form.input
                            label="Age"
                            name="form.age"
                            type="number"
                            placeholder="Enter age"
                            wire:model.blur="form.age"
                            min="1"
                            max="120"
                            inputmode="numeric"
                            required
                        />

                        <x-form.field
                            label="Gender"
                            name="form.gender"
                            fieldId="gender"
                            required
                        >
                            <select
                                id="gender"
                                wire:model.blur="form.gender"
                                class="form-control form-select w-full"
                            >
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </x-form.field>

                    </div>
                </x-form.section>


                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('users.index') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-indigo-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="save">
                            {{ $submitText }}
                        </span>

                        <span wire:loading wire:target="save">
                            {{ $loadingText }}
                        </span>
                    </button>

                </div>

            </div>


            {{-- Sidebar --}}
            <div class="space-y-6">

                @if($isEdit)

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                        <div class="flex items-center gap-3">

                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-lg font-bold text-indigo-600">
                                {{ strtoupper(substr($form->name ?: 'U', 0, 1)) }}
                            </div>

                            <div class="min-w-0">

                                <h3 class="truncate text-sm font-semibold text-slate-900">
                                    {{ $form->name ?: 'User' }}
                                </h3>

                                <p class="truncate text-xs text-slate-500">
                                    {{ $form->email ?: 'No email' }}
                                </p>

                            </div>

                        </div>

                    </div>

                @endif


                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-5">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9" />

                            <path
                                stroke-linecap="round"
                                d="M12 10v6m0-9h.01"
                            />
                        </svg>
                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-slate-900">
                        {{ $isEdit ? 'Before you save' : 'Before you create' }}
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Make sure the user's information and location are correct.
                        The city list updates automatically after selecting a governorate.
                    </p>

                </div>

            </div>

        </div>

    </form>

</div>
لحظ الحاجات الي ممكن تتكر معانا وخليها علي جنب بحيث الصفحه متعديش ال300 سطر