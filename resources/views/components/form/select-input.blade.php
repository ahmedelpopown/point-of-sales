@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'optionValue' => null,
    'optionLabel' => null,
    'placeholder' => 'Select an option',
])

@php
    $inputId = $name
        ? str_replace(['.', '[]'], ['-', ''], $name)
        : null;
@endphp

<div class="w-full">

    @if($label)
        <label
            for="{{ $inputId }}"
            class="block text-sm font-medium text-gray-700"
        >
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $inputId }}"
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 pe-10 text-sm text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400',
        ]) }}
    >

        <option value="">
            {{ $placeholder }}
        </option>

        @foreach($options as $key => $option)

            @php
                /*
                 * Model / Object:
                 * optionValue + optionLabel are provided.
                 */
                if ($optionValue) {

                    $value = data_get($option, $optionValue);

                    $text = $optionLabel
                        ? data_get($option, $optionLabel)
                        : $value;

                /*
                 * Simple / Associative Array:
                 *
                 * [
                 *     'warehouse' => 'Warehouses',
                 *     'shop' => 'Shops',
                 * ]
                 */
                } else {

                    $value = $key;
                    $text = $option;

                }
            @endphp

            <option value="{{ $value }}">
                {{ $text }}
            </option>

        @endforeach

    </select>

    @if($name)

        @error($name)

            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>

        @enderror

    @endif

</div>