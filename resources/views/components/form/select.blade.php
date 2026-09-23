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
    $errorId = $inputId . '-error';
@endphp

<div class="w-full">

    @if($label)
        <div class="form-label">

            <label for="{{ $inputId }}">
                {{ $label }}

                @if($attributes->has('required'))
                    <span class="ml-1 text-red-500">*</span>
                @endif
            </label>

            @if($hint)
                <span class="form-hint">{{ $hint }}</span>
            @endif

        </div>
    @endif


    <div class="relative">

        <select
            id="{{ $inputId }}"
            name="{{ $name }}"
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"

            @if($hasError)
                aria-describedby="{{ $errorId }}"
            @endif

            {{ $attributes->class([
                'form-select',
                'form-control-error' => $hasError,
            ]) }}
        >

            <option value="">
                {{ $placeholder }}
            </option>

            @foreach($options as $key => $option)

                @php
                    $value = $optionValue
                        ? data_get($option, $optionValue)
                        : $key;

                    $text = $optionValue && $optionLabel
                        ? data_get($option, $optionLabel)
                        : ($optionLabel
                            ? data_get($option, $optionLabel)
                            : ($optionValue
                                ? $value
                                : $option));
                @endphp

                <option value="{{ $value }}">
                    {{ $text }}
                </option>

            @endforeach

        </select>


        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">

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

        </div>

    </div>


    @if($hasError)
        <div id="{{ $errorId }}" class="form-error">
            <span>
                {{ $errors->first($name) }}
            </span>
        </div>
    @endif

</div>