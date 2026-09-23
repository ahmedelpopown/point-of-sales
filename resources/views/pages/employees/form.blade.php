<div class="px-4 py-6 sm:px-6 lg:px-8">

    {{-- Header --}}
    <x-ui.page-header
        :title="$title"
        :subtitle="$subtitle"
        :back-url="route('employees.index')"
        back-text="Back to Employees"
    />


    <form wire:submit="save" class="mt-8">

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- ========================================================= --}}
            {{-- Main --}}
            {{-- ========================================================= --}}

            <div class="space-y-6 xl:col-span-2">


                {{-- ===================================================== --}}
                {{-- Personal Information --}}
                {{-- ===================================================== --}}

                <x-form.section
                    title="Personal Information"
                    description="Basic employee information."
                >

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        {{-- First Name --}}
                        <x-form.input
                            label="First Name"
                            name="form.first_name"
                            placeholder="Enter first name"
                            wire:model.blur="form.first_name"
                            autocomplete="given-name"
                            required
                        />


                        {{-- Last Name --}}
                        <x-form.input
                            label="Last Name"
                            name="form.last_name"
                            placeholder="Enter last name"
                            wire:model.blur="form.last_name"
                            autocomplete="family-name"
                            required
                        />


                        {{-- Email --}}
                        <x-form.input
                            label="Email Address"
                            name="form.email"
                            type="email"
                            placeholder="Enter email address"
                            wire:model.blur="form.email"
                            autocomplete="email"
                            required
                        />


                        {{-- Password --}}
                        <x-form.input
                            label="Password"
                            name="form.password"
                            type="password"
                            :placeholder="$isEdit
                                ? 'Leave blank to keep current password'
                                : 'Enter password'"
                            :hint="$isEdit
                                ? 'Leave empty to keep the current password.'
                                : null"
                            wire:model.blur="form.password"
                            autocomplete="new-password"
                            :required="!$isEdit"
                        />


                        {{-- Phone --}}
                        <x-form.input
                            label="Phone Number"
                            name="form.phone"
                            placeholder="Enter phone number"
                            wire:model.blur="form.phone"
                            inputmode="tel"
                            autocomplete="tel"
                        />


                        {{-- Address --}}
                        <x-form.input
                            label="Address"
                            name="form.address"
                            placeholder="Enter address"
                            wire:model.blur="form.address"
                            autocomplete="street-address"
                        />

                    </div>

                </x-form.section>


                {{-- ===================================================== --}}
                {{-- Employment Information --}}
                {{-- ===================================================== --}}

                <x-form.section
                    title="Employment Information"
                    description="Department, position, salary, work location, and status."
                >

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">


                        {{-- Department --}}
                        <x-form.field
                            label="Department"
                            name="form.department"
                            fieldId="department"
                            required
                        >

                            <select
                                id="department"
                                wire:model.live="form.department"
                                class="form-control form-select w-full"
                            >

                                <option value="">
                                    Select Department
                                </option>

                                @foreach($departments as $department)

                                    <option value="{{ $department }}">
                                        {{ \Illuminate\Support\Str::headline($department) }}
                                    </option>

                                @endforeach

                            </select>

                        </x-form.field>


                        {{-- Position --}}
                        <x-form.field
                            label="Position"
                            name="form.position"
                            fieldId="position"
                            required
                        >

                            <select
                                id="position"
                                wire:model.blur="form.position"
                                class="form-control form-select w-full"
                            >

                                <option value="">
                                    Select Position
                                </option>

                                @foreach($positions as $position)

                                    <option value="{{ $position }}">
                                        {{ \Illuminate\Support\Str::headline($position) }}
                                    </option>

                                @endforeach

                            </select>

                        </x-form.field>


                        {{-- Shop --}}
                        @if($form->department === 'sales')

                            <div
                                wire:key="employee-shop-{{ $form->department }}"
                            >

                                <x-form.searchable-select
                                    label="Shop"
                                    name="form.shop_id"
                                    model="form.shop_id"
                                    :options="$this->shops"
                                    optionValue="id"
                                    optionLabel="name"
                                    placeholder="Select Shop"
                                    searchPlaceholder="Search shops..."
                                    :clearable="true"
                                    required
                                />

                            </div>

                        @endif


                        {{-- Warehouse --}}
                        @if($form->department === 'inventory')

                            <div
                                wire:key="employee-warehouse-{{ $form->department }}"
                            >

                                <x-form.searchable-select
                                    label="Warehouse"
                                    name="form.warehouse_id"
                                    model="form.warehouse_id"
                                    :options="$this->warehouses"
                                    optionValue="id"
                                    optionLabel="name"
                                    placeholder="Select Warehouse"
                                    searchPlaceholder="Search warehouses..."
                                    :clearable="true"
                                    required
                                />

                            </div>

                        @endif


                        {{-- Salary --}}
                        <x-form.input
                            label="Salary"
                            name="form.salary"
                            type="number"
                            placeholder="Enter salary"
                            wire:model.blur="form.salary"
                            min="0"
                            step="0.01"
                            inputmode="decimal"
                            required
                        />


                        {{-- Hire Date --}}
                        <x-form.input
                            label="Hire Date"
                            name="form.hire_date"
                            type="date"
                            wire:model.blur="form.hire_date"
                            required
                        />


                        {{-- Status --}}
                        <x-form.field
                            label="Status"
                            name="form.status"
                            fieldId="status"
                            required
                        >

                            <select
                                id="status"
                                wire:model.blur="form.status"
                                class="form-control form-select w-full"
                            >

                                <option value="active">
                                    Active
                                </option>

                                <option value="inactive">
                                    Inactive
                                </option>

                            </select>

                        </x-form.field>

                    </div>

                </x-form.section>


                {{-- ===================================================== --}}
                {{-- Role --}}
                {{-- ===================================================== --}}

                @can('manage permissions')

                    <x-form.section
                        title="Role"
                        description="Select the permissions role assigned to this employee."
                    >

                        <x-form.searchable-select
                            label="Employee Role"
                            name="role"
                            model="role"
                            :options="$this->roles"
                            optionValue="name"
                            optionLabel="name"
                            placeholder="Select Role"
                            searchPlaceholder="Search roles..."
                            :clearable="true"
                            required
                        />

                    </x-form.section>

                @endcan


                {{-- ===================================================== --}}
                {{-- Actions --}}
                {{-- ===================================================== --}}

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('employees.index') }}"
                        wire:navigate
                        class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-indigo-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 disabled:cursor-not-allowed disabled:opacity-60"
                    >

                        <span
                            wire:loading.remove
                            wire:target="save"
                        >
                            {{ $submitText }}
                        </span>


                        <span
                            wire:loading
                            wire:target="save"
                        >
                            {{ $loadingText }}
                        </span>

                    </button>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- Sidebar --}}
            {{-- ========================================================= --}}

            <div class="space-y-6">


                {{-- Employee Preview --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-lg font-bold text-indigo-600">
                            {{ strtoupper(substr($form->first_name ?: 'E', 0, 1)) }}
                        </div>


                        <div class="min-w-0">

                            <h3 class="truncate text-sm font-semibold text-slate-900">
                                {{ trim($form->first_name . ' ' . $form->last_name) ?: 'Employee' }}
                            </h3>

                            <p class="truncate text-xs text-slate-500">
                                {{ $form->email ?: 'No email' }}
                            </p>

                        </div>

                    </div>


                    @if($form->department)

                        <div class="mt-4 rounded-xl bg-slate-50 p-3">

                            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                Department
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-700">
                                {{ \Illuminate\Support\Str::headline($form->department) }}
                            </p>

                        </div>

                    @endif


                    @if($form->shop_id || $form->warehouse_id)

                        <div class="mt-3 rounded-xl bg-slate-50 p-3">

                            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                Work Location
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-700">

                                @if($form->shop_id)

                                    Shop:
                                    {{ $this->shops->firstWhere('id', $form->shop_id)?->name ?? '—' }}

                                @elseif($form->warehouse_id)

                                    Warehouse:
                                    {{ $this->warehouses->firstWhere('id', $form->warehouse_id)?->name ?? '—' }}

                                @endif

                            </p>

                        </div>

                    @endif

                </div>


                {{-- Info --}}
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-5">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                            />

                            <path
                                stroke-linecap="round"
                                d="M12 10v6m0-9h.01"
                            />
                        </svg>

                    </div>


                    <h3 class="mt-4 text-sm font-semibold text-slate-900">
                        Work Location
                    </h3>


                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Sales employees are assigned to a shop,
                        while inventory employees are assigned to a warehouse.
                    </p>

                </div>


                {{-- Required Information --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <h3 class="text-sm font-semibold text-slate-900">
                        Required Information
                    </h3>

                    <div class="mt-4 space-y-3 text-sm text-slate-600">

                        @foreach([
                            'First name',
                            'Last name',
                            'Email address',
                            $isEdit ? null : 'Password',
                            'Department',
                            'Position',
                            'Salary',
                            'Hire date',
                        ] as $item)

                            @if($item)

                                <div class="flex items-center gap-2">

                                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>

                                    {{ $item }}

                                </div>

                            @endif

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>