<?php

use Livewire\Component;
use App\Models\User;
use App\Livewire\Forms\UserForm;
use App\Models\Governorate;
use App\Models\City;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
new #[Title('Edit User')] class extends Component {
    public UserForm $form;
        public $governorate_id;
    public function mount(User $user)
    {
        $this->form->setUser($user);
    }
    public function save()
    {
        $this->form->update();
        session()->flash('message', 'User updated successfully.');
        $this->redirect('/users');
    }
    #[Computed]
    public function governorates()
    {
        return Governorate::orderBy('name')->get();
    }
    #[Computed]
    public function cities()
    {
        if (!$this->form->governorate_id) {
            return collect(); // فاضي لحد ما يختار
        }

        return City::where('governorate_id', $this->form->governorate_id)
            ->orderBy('name')
            ->get();
    }
};
?>


<div class="px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Edit Employee
            </h2>
        </div>
    </div>

    <div class="mt-8 max-w-3xl">
        <form wire:submit="save">
            <div class="space-y-6">
                {{-- Personal Information --}}
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Personal Information
                        </h3>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Name
                                </label>
                                <input wire:model.blur="form.name" type="text" id="name"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                @error('form.name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input wire:model.blur="form.email" type="email" id="email"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                @error('form.email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Password
                                </label>
                                <input wire:model.blur="form.password" type="password" id="password"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                @error('form.password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Phone
                                </label>
                                <input wire:model="form.phone" type="text" id="phone"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                @error('form.phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- address --}}
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">
                                    address
                                </label>
                                <input wire:model="form.address" type="text" id="address"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                @error('form.address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Information --}}
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            User Information
                        </h3>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            {{-- Governorate --}}
                            <div>
                                <label for="governorate" class="block text-sm font-medium text-gray-700">
                                    governorate
                                </label>
                                <select wire:model.live="form.governorate_id" id="governorate_id"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <option value="">Select Governorate</option>
                                    @foreach($this->governorates as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.governorate_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- City --}}
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">
                                    City
                                </label>
                                <select wire:model.live="form.city_id" id="city_id"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <option value="">Select City</option>
                                    @foreach($this->cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.city_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- age --}}
                            <div>
                                <label for="age" class="block text-sm font-medium text-gray-700">
                                    Age
                                </label>
                                <input wire:model.blur="form.age" type="number" id="age" step="0.01"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                @error('form.age')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>



                            {{-- gender --}}
                            <div class="sm:col-span-2">
                                <label for="gender" class="block text-sm font-medium text-gray-700">
                                    Gender
                                </label>
                                <select wire:model="form.gender" id="gender"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <option value="male">male</option>
                                    <option value="female">female</option>
                                </select>
                                @error('form.gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('users.index') }}"
                        class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Update Employee
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>