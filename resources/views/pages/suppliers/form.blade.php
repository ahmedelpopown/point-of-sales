<div class="px-4 py-6 sm:px-6 lg:px-8">

    {{-- Header --}}
    <x-ui.page-header
        :title="$title"
        :subtitle="$subtitle"
        :back-url="route('suppliers.index')"
        back-text="Back to Suppliers" />

    <form wire:submit="save" class="mt-8">

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- Main --}}
            <div class="space-y-6 xl:col-span-2">

                {{-- Personal Information --}}
                <x-form.section
                    title="Personal Information"
                    description="Basic supplier information.">

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        {{-- Name --}}
                        <x-form.input
                            label="Name"
                            name="form.name"
                            placeholder="Enter supplier name"
                            wire:model.blur="form.name"
                            autocomplete="name"
                            required />

                        {{-- Email --}}
                        <x-form.input
                            label="Email Address"
                            name="form.email"
                            type="email"
                            placeholder="Enter email address"
                            wire:model.blur="form.email"
                            autocomplete="email" />

                        {{-- Phone --}}
                        <x-form.input
                            label="Phone Number"
                            name="form.phone"
                            placeholder="Enter phone number"
                            wire:model.blur="form.phone"
                            inputmode="tel"
                            autocomplete="tel" />

                        {{-- Address --}}
                        <x-form.input
                            label="Address"
                            name="form.address"
                            placeholder="Enter supplier address"
                            wire:model.blur="form.address"
                            autocomplete="street-address" />

                    </div>

                </x-form.section>


                {{-- Location Information --}}
                <x-form.section
                    title="Location Information"
                    description="Select the supplier governorate and city.">

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        {{-- Governorate --}}
                        <x-form.searchable-select
                            label="Governorate"
                            name="form.governorate_id"
                            model="form.governorate_id"
                            :options="$governorates"
                            optionValue="id"
                            optionLabel="name"
                            placeholder="Select Governorate"
                            searchPlaceholder="Search governorates..."
                            :clearable="true"
                            required />

                        <div wire:key="supplier-city-{{ $form->governorate_id ?: 'none' }}">

                            <x-form.searchable-select
                                label="City"
                                name="form.city_id"
                                model="form.city_id"
                                :options="$cities"
                                optionValue="id"
                                optionLabel="name"
                                placeholder="Select City"
                                searchPlaceholder="Search cities..."
                                :clearable="true"
                                :disabled="!$form->governorate_id"
                                required />

                        </div>

                    </div>

                </x-form.section>


                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('suppliers.index') }}"
                        wire:navigate
                        class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-indigo-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 disabled:cursor-not-allowed disabled:opacity-60">

                        <span
                            wire:loading.remove
                            wire:target="save">
                            {{ $submitText }}
                        </span>

                        <span
                            wire:loading
                            wire:target="save">
                            {{ $loadingText }}
                        </span>

                    </button>

                </div>

            </div>


            {{-- Sidebar --}}
            <div class="space-y-6">

                {{-- Supplier Preview --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-lg font-bold text-indigo-600">
                            {{ strtoupper(substr($form->name ?: 'S', 0, 1)) }}
                        </div>

                        <div class="min-w-0">

                            <h3 class="truncate text-sm font-semibold text-slate-900">
                                {{ $form->name ?: 'Supplier' }}
                            </h3>

                            <p class="truncate text-xs text-slate-500">
                                {{ $form->email ?: 'No email' }}
                            </p>

                        </div>

                    </div>


                    @if($form->phone)

                    <div class="mt-4 rounded-xl bg-slate-50 p-3">

                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                            Phone
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ $form->phone }}
                        </p>

                    </div>

                    @endif


                    @if($form->governorate_id || $form->city_id)

                    <div class="mt-3 rounded-xl bg-slate-50 p-3">

                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                            Location
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">

                            @if($form->governorate_id)
                            {{ $governorates->firstWhere('id', $form->governorate_id)?->name ?? '—' }}
                            @endif

                            @if($form->city_id)
                            , {{ $cities->firstWhere('id', $form->city_id)?->name ?? '—' }}
                            @endif

                        </p>

                    </div>

                    @endif

                </div>


                {{-- Info --}}
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-5">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <circle
                                cx="12"
                                cy="12"
                                r="9" />

                            <path
                                stroke-linecap="round"
                                d="M12 10v6m0-9h.01" />
                        </svg>

                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-slate-900">
                        Supplier Information
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Add the supplier's contact details and location.
                    </p>

                </div>


                {{-- Required Information --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <h3 class="text-sm font-semibold text-slate-900">
                        Required Information
                    </h3>

                    <div class="mt-4 space-y-3 text-sm text-slate-600">

                        @foreach([
                        'Supplier name',
                        'Governorate',
                        'City',
                        ] as $item)

                        <div class="flex items-center gap-2">

                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>

                            {{ $item }}

                        </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>