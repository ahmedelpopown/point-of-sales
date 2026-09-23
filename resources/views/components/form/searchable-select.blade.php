@props([
    'label' => null,
    'name' => null,
    'model' => null,
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'placeholder' => 'Select an option',
    'searchPlaceholder' => 'Search...',
    'hint' => null,
    'clearable' => true,
])

@php
    $model = $model ?: $name;

    $inputId = $name
        ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $name)
        : 'search-select-' . uniqid();

    $hasError = filled($model) && $errors->has($model);

    $required = $attributes->has('required');

    $optionsData = collect($options)
        ->map(function ($option, $key) use ($optionValue, $optionLabel) {
            if ($optionValue) {
                return [
                    'value' => (string) data_get($option, $optionValue),
                    'label' => (string) data_get($option, $optionLabel),
                ];
            }

            return [
                'value' => (string) $key,
                'label' => (string) $option,
            ];
        })
        ->values()
        ->all();
@endphp

<x-form.field
    :label="$label"
    :name="$model"
    :hint="$hint"
    :required="$required"
    fieldId="{{ $inputId }}"
>
    <div
        x-data="{
            open: false,
            search: '',

            selected: $wire.entangle(@js($model)).live,

            options: @js($optionsData),

            get selectedOption() {
                return this.options.find(
                    option => String(option.value) === String(this.selected)
                );
            },

            get selectedLabel() {
                return this.selectedOption?.label ?? '';
            },

            get filteredOptions() {
                const query = this.search.trim().toLowerCase();

                if (!query) {
                    return this.options;
                }

                return this.options.filter(option =>
                    option.label.toLowerCase().includes(query)
                );
            },

            openDropdown() {
                this.open = true;

                this.$nextTick(() => {
                    this.$refs.search?.focus();
                });
            },

            closeDropdown() {
                this.open = false;
                this.search = '';
            },

            select(value) {
                this.selected = value;

                // تحديث Livewire فوراً
                $wire.set(@js($model), value);

                this.closeDropdown();
            },

            clear() {
                this.selected = null;

                // تصفير Livewire فوراً
                $wire.set(@js($model), null);

                this.search = '';
                this.closeDropdown();
            }
        }"

        @click.outside="closeDropdown()"
        @keydown.escape.window="closeDropdown()"

        class="relative"
    >

        {{-- Trigger --}}
        <button
            id="{{ $inputId }}"
            type="button"

            @click="open ? closeDropdown() : openDropdown()"

            :aria-expanded="open"
            aria-haspopup="listbox"

            @if($hasError)
                aria-invalid="true"
                aria-describedby="{{ $inputId }}-error"
            @endif

            {{ $attributes
                ->except([
                    'wire:model',
                    'wire:model.live',
                    'wire:model.blur',
                    'wire:model.defer',
                ])
                ->class([
                    'form-search-trigger',
                    'form-control-error' => $hasError,
                ])
            }}
        >

            {{-- Value --}}
            <span
                class="min-w-0 flex-1 truncate text-sm"
                :class="
                    selectedLabel
                        ? 'font-semibold text-slate-800'
                        : 'font-normal text-slate-400'
                "
                x-text="selectedLabel || @js($placeholder)"
            ></span>

            {{-- Actions --}}
            <span class="ml-3 flex shrink-0 items-center gap-1">

                @if($clearable)

                    <span
                        x-show="selectedLabel"
                        x-cloak

                        @click.stop="clear()"

                        class="flex h-7 w-7 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                d="M6.28 5.22a.75.75 0 011.06 0L10 7.94l2.66-2.72a.75.75 0 111.08 1.04L11.06 9l2.68 2.72a.75.75 0 01-1.08 1.04L10 10.06l-2.66 2.7a.75.75 0 11-1.06-1.04L8.94 9 6.28 6.28a.75.75 0 010-1.06Z"
                            />
                        </svg>
                    </span>

                @endif

                <svg
                    class="h-5 w-5 text-slate-400 transition-transform duration-200"
                    :class="open ? 'rotate-180' : ''"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 0v-.01a.75.75 0 01-1.08.01l-4.25-4.5a.75.75 0 01.02-1.06Z"
                        clip-rule="evenodd"
                    />
                </svg>

            </span>

        </button>

        {{-- Dropdown --}}
        <div
            x-show="open"
            x-cloak

            x-transition:enter="transition duration-150 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"

            x-transition:leave="transition duration-100 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-1 scale-[0.98]"

            class="form-search-dropdown"
        >

            {{-- Search --}}
            <div class="border-b border-slate-100 p-3">

                <div class="relative">

                    <input
                        x-ref="search"
                        x-model="search"
                        type="search"
                        placeholder="{{ $searchPlaceholder }}"
                        autocomplete="off"
                        class="form-search"
                    />

                    {{-- Search icon --}}
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">

                        <svg
                            class="h-4 w-4 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0Z"
                            />
                        </svg>

                    </div>

                    {{-- Clear search --}}
                    <button
                        type="button"
                        x-show="search"
                        x-cloak

                        @click="
                            search = '';
                            $nextTick(() => $refs.search?.focus());
                        "

                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 transition hover:text-slate-700"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                d="M6.28 5.22a.75.75 0 011.06 0L10 7.94l2.66-2.72a.75.75 0 111.08 1.04L11.06 9l2.68 2.72a.75.75 0 01-1.08 1.04L10 10.06l-2.66 2.7a.75.75 0 11-1.06-1.04L8.94 9 6.28 6.28a.75.75 0 010-1.06Z"
                            />
                        </svg>
                    </button>

                </div>

            </div>

            {{-- Options --}}
            <div
                class="max-h-72 overflow-y-auto p-1.5"
                role="listbox"
            >

                <template
                    x-for="option in filteredOptions"
                    :key="option.value"
                >

                    <button
                        type="button"
                        role="option"

                        @click="select(option.value)"

                        :aria-selected="
                            String(selected) === String(option.value)
                        "

                        class="form-option"

                        :class="{
                            'form-option-selected':
                                String(selected) === String(option.value)
                        }"
                    >

                        <span
                            class="min-w-0 flex-1 truncate"
                            x-text="option.label"
                        ></span>

                        {{-- Selected --}}
                        <span
                            x-show="
                                String(selected) === String(option.value)
                            "

                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600"
                        >
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.704 5.29a.75.75 0 01.006 1.06l-7.25 7.3a.75.75 0 01-1.07 0l-3.1-3.1a.75.75 0 111.06-1.06l2.565 2.565 6.72-6.765a.75.75 0 011.069-.006Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </span>

                    </button>

                </template>

                {{-- Empty --}}
                <div
                    x-show="filteredOptions.length === 0"
                    x-cloak
                    class="px-5 py-9 text-center"
                >

                    <div
                        class="mx-auto flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path
                                stroke-linecap="round"
                                d="m20 20-4-4"
                            />
                        </svg>
                    </div>

                    <p class="mt-3 text-sm font-semibold text-slate-700">
                        No results found
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Try another search term.
                    </p>

                </div>

            </div>

        </div>

    </div>

</x-form.field>