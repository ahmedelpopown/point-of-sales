@props([
    'label' => null,
    'options' => collect(),
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'placeholder' => 'Select an option',
    'model' => null,
])

@php
    $optionsData = collect($options)
        ->map(function ($option) use ($optionValue, $optionLabel) {
            return [
                'value' => (string) data_get($option, $optionValue),
                'label' => (string) data_get($option, $optionLabel),
            ];
        })
        ->values()
        ->all();
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selected: @entangle($model).live,

        options: @js($optionsData),

        get selectedLabel() {
            const selectedOption = this.options.find(
                option => String(option.value) === String(this.selected)
            );

            return selectedOption?.label ?? '';
        },

        matches(label) {
            return label
                .toLowerCase()
                .includes(this.search.toLowerCase());
        },

        close() {
            this.open = false;
            this.search = '';
        },

        select(value) {
            this.selected = value;
            this.close();
        }
    }"
    @click.outside="close()"
    class="relative w-full"
>
    @if($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    {{-- Select Button --}}
    <button
        type="button"
        @click="
            open = !open;

            if (open) {
                $nextTick(() => $refs.search.focus());
            }
        "
        class="mt-2 flex w-full items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 text-left text-sm shadow-sm transition hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >
        <span
            x-text="selectedLabel || @js($placeholder)"
            :class="selectedLabel
                ? 'text-gray-900'
                : 'text-gray-400'"
        ></span>

        <svg
            class="h-5 w-5 text-gray-400 transition-transform duration-200"
            :class="open ? 'rotate-180' : ''"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
            />
        </svg>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition
        class="absolute z-50 mt-2 w-full overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl"
        style="display: none;"
    >
        {{-- Search --}}
        <div class="border-b border-gray-100 bg-white p-2">
            <div class="relative">
                <input
                    x-ref="search"
                    x-model="search"
                    type="text"
                    placeholder="Search..."
                    autocomplete="off"
                    class="w-full rounded-md border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-3 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                >

                <svg
                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0 0 10.6 10.6Z"
                    />
                </svg>
            </div>
        </div>

        {{-- Options --}}
        <div class="max-h-64 overflow-y-auto py-1">

            @foreach($optionsData as $option)

                <button
                    type="button"
                    @click="select(@js($option['value']))"
                    x-show="matches(@js(strtolower($option['label'])))"
                    class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm transition hover:bg-indigo-50"
                    :class="String(selected) === String(@js($option['value']))
                        ? 'bg-indigo-50 text-indigo-700'
                        : 'text-gray-700'"
                >
                    <span>
                        {{ $option['label'] }}
                    </span>

                    <svg
                        x-show="String(selected) === String(@js($option['value']))"
                        class="h-4 w-4 text-indigo-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m5 13 4 4L19 7"
                        />
                    </svg>
                </button>

            @endforeach

            {{-- No Results --}}
            <div
                x-show="options.filter(option => matches(option.label.toLowerCase())).length === 0"
                class="px-4 py-4 text-center text-sm text-gray-500"
            >
                No results found.
            </div>

        </div>
    </div>

    {{-- Validation --}}
    @if($model)

        @error($model)

            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>

        @enderror

    @endif

</div>

