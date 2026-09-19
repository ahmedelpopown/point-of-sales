@props([
    'label' => null,
    'name' => null,
    'placeholder' => '',
    'rows' => 4,
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

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'mt-2 block w-full resize-y rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500',
        ]) }}
    ></textarea>

    @if($name)
        @error($name)
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    @endif
</div>
