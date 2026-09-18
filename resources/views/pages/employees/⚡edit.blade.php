<?php

use App\Livewire\Forms\EmployeeForm;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Employee')] class extends Component
{
    public EmployeeForm $form;

    public array $departments = [
        'sales',
        'inventory',
        'purchasing',
        'supplier',
    ];

    public array $positions = [
        'sales associate',
        'inventory clerk',
        'purchasing coordinator',
        'supplier relationship manager',
    ];

    public string $role = '';

    public function mount(Employee $employee): void
    {
        $this->form->setEmployee($employee);

        $this->role = $employee->roles->first()?->name ?? '';
    }

    public function roles()
    {
        return Role::query()
            ->where('guard_name', 'employee')
            ->orderBy('name')
            ->get();
    }

    public function save(): void
    {
        $this->validate([
            'role' => [
                'required',
                'string',
                'exists:roles,name',
            ],
        ]);

        $role = Role::query()
            ->where('name', $this->role)
            ->where('guard_name', 'employee')
            ->first();

        if (! $role) {
            $this->addError(
                'role',
                'The selected role is invalid.'
            );

            return;
        }

        $this->form->update();

        $employee = $this->form->employee;

        $employee->syncRoles([
            $role,
        ]);

        session()->flash(
            'message',
            'Employee updated successfully.'
        );

        $this->redirectRoute('employees.index');
    }
};
?>

<div class="px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">

            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Edit Employee
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                Update employee personal, employment, and role information.
            </p>

        </div>
    </div>


    <div class="mt-8 max-w-4xl">

        <form wire:submit="save">

            <div class="space-y-6">

                {{-- ===================================================== --}}
                {{-- Personal Information --}}
                {{-- ===================================================== --}}

                <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-200 sm:rounded-xl">

                    <div class="px-4 py-5 sm:p-6">

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Personal Information
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Basic employee information.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                            {{-- First Name --}}
                            <div>
                                <label
                                    for="first_name"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    First Name
                                </label>

                                <input
                                    id="first_name"
                                    type="text"
                                    wire:model.blur="form.first_name"
                                    autocomplete="given-name"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('form.first_name')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Last Name --}}
                            <div>
                                <label
                                    for="last_name"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Last Name
                                </label>

                                <input
                                    id="last_name"
                                    type="text"
                                    wire:model.blur="form.last_name"
                                    autocomplete="family-name"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('form.last_name')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Email --}}
                            <div>
                                <label
                                    for="email"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Email
                                </label>

                                <input
                                    id="email"
                                    type="email"
                                    wire:model.blur="form.email"
                                    autocomplete="email"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('form.email')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Password --}}
                            <div>
                                <label
                                    for="password"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Password
                                </label>

                                <input
                                    id="password"
                                    type="password"
                                    wire:model.blur="form.password"
                                    autocomplete="new-password"
                                    placeholder="Leave empty to keep current password"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                <p class="mt-1 text-xs text-gray-500">
                                    Leave empty to keep the current password.
                                </p>

                                @error('form.password')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Phone --}}
                            <div>
                                <label
                                    for="phone"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Phone
                                </label>

                                <input
                                    id="phone"
                                    type="text"
                                    wire:model="form.phone"
                                    autocomplete="tel"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('form.phone')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Address --}}
                            <div>
                                <label
                                    for="address"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Address
                                </label>

                                <input
                                    id="address"
                                    type="text"
                                    wire:model="form.address"
                                    autocomplete="street-address"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('form.address')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>


                {{-- ===================================================== --}}
                {{-- Employment Information --}}
                {{-- ===================================================== --}}

                <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-200 sm:rounded-xl">

                    <div class="px-4 py-5 sm:p-6">

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Employment Information
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Update work-related information.
                            </p>
                        </div>


                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                            {{-- Department --}}
                            <div>
                                <label
                                    for="department"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Department
                                </label>

                                <select
                                    id="department"
                                    wire:model="form.department"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">
                                        Select Department
                                    </option>

                                    @foreach($departments as $department)
                                        <option value="{{ $department }}">
                                            {{ Illuminate\Support\Str::headline($department) }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('form.department')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Position --}}
                            <div>
                                <label
                                    for="position"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Position
                                </label>

                                <select
                                    id="position"
                                    wire:model="form.position"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">
                                        Select Position
                                    </option>

                                    @foreach($positions as $position)
                                        <option value="{{ $position }}">
                                            {{ Illuminate\Support\Str::headline($position) }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('form.position')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Salary --}}
                            <div>
                                <label
                                    for="salary"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Salary
                                </label>

                                <input
                                    id="salary"
                                    type="number"
                                    wire:model.blur="form.salary"
                                    step="0.01"
                                    min="0"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('form.salary')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Hire Date --}}
                            <div>
                                <label
                                    for="hire_date"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Hire Date
                                </label>

                                <input
                                    id="hire_date"
                                    type="date"
                                    wire:model="form.hire_date"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('form.hire_date')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Status --}}
                            <div class="sm:col-span-2">
                                <label
                                    for="status"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Status
                                </label>

                                <select
                                    id="status"
                                    wire:model="form.status"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="active">
                                        Active
                                    </option>

                                    <option value="inactive">
                                        Inactive
                                    </option>
                                </select>

                                @error('form.status')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>


                {{-- ===================================================== --}}
                {{-- Role --}}
                {{-- ===================================================== --}}

                @can('manage permissions')

                    <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-200 sm:rounded-xl">

                        <div class="px-4 py-5 sm:p-6">

                            <div class="mb-6">

                                <h3 class="text-lg font-semibold text-gray-900">
                                    Role
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Select the role assigned to this employee.
                                </p>

                            </div>


                            <div>

                                <label
                                    for="role"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Employee Role
                                </label>

                                <select
                                    id="role"
                                    wire:model="role"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                    <option value="">
                                        Select Role
                                    </option>

                                    @foreach($this->roles() as $availableRole)

                                        <option value="{{ $availableRole->name }}">
                                            {{ Illuminate\Support\Str::headline($availableRole->name) }}
                                        </option>

                                    @endforeach

                                </select>

                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>

                    </div>

                @endcan


                {{-- ===================================================== --}}
                {{-- Actions --}}
                {{-- ===================================================== --}}

                <div class="flex items-center justify-end gap-3">

                    <a
                        href="{{ route('employees.index') }}"
                        wire:navigate
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >

                        <span
                            wire:loading.remove
                            wire:target="save"
                        >
                            Update Employee
                        </span>

                        <span
                            wire:loading
                            wire:target="save"
                        >
                            Updating...
                        </span>

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>