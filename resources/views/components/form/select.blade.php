@props([
    'label' => null,
    'name' => null,
    'placeholder' => 'Select option',
    'options' => [],
])

<div>
    @if($label)
        <label
            for="{{ $name }}"
            class="block text-sm font-medium text-gray-700"
        >
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'mt-2 py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500',
        ]) }}
    >

        <option value="">
            {{ $placeholder }}
        </option>

        @foreach($options as $value => $optionLabel)

            <option value="{{ $value }}">
                {{ $optionLabel }}
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