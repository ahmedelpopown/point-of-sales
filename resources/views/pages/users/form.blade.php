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