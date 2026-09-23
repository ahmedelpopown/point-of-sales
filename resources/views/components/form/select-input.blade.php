@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'optionValue' => null,
    'optionLabel' => null,
    'placeholder' => 'Select an option',
    'hint' => null,
])

@php
    $inputId = $name
        ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $name)
        : 'select-' . uniqid();

    $hasError = filled($name) && $errors->has($name);

    $required = $attributes->has('required');
@endphp

<x-form.field
    :label="$label"
    :name="$name"
    :hint="$hint"
    :required="$required"
    fieldId="{{ $inputId }}"
>

    <div class="relative">

        <select
            id="{{ $inputId }}"
            name="{{ $name }}"
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"

            @if($hasError)
                aria-describedby="{{ $inputId }}-error"
            @endif

            {{ $attributes->class([
                'form-control',
                'form-select',
                'form-control-error' => $hasError,
            ]) }}
        >

            <option value="">
                {{ $placeholder }}
            </option>

            @foreach($options as $key => $option)

                @php
                    if ($optionValue) {
                        $value = data_get($option, $optionValue);

                        $text = $optionLabel
                            ? data_get($option, $optionLabel)
                            : $value;
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


        {{-- Chevron --}}
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">

            @if($hasError)

                <span class="form-error-badge">

                    <svg
                        class="h-3.5 w-3.5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-1-5.5a1 1 0 112 0 1 1 0 01-2 0v3a1 1 0 01-2 0v-3Zm1 7.5a.875.875 0 100-1.75A.875.875 0 0010 20Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                </span>

            @else

                <svg
                    class="h-5 w-5 text-slate-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06Z"
                        clip-rule="evenodd"
                    />
                </svg>

            @endif

        </div>

    </div>

</x-form.field>