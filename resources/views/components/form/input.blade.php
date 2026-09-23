@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'hint' => null,
    'prefix' => null,
    'suffix' => null,
])

@php
    $inputId = $name
        ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $name)
        : 'input-' . uniqid();

    $hasError = filled($name) && $errors->has($name);

    $required = $attributes->has('required');

    $classes = [
        'form-control',
        'form-input',

        'form-control-error' => $hasError,

        'pl-11' => $prefix,
        'pr-11' => $suffix || $hasError,
    ];
@endphp

<x-form.field
    :label="$label"
    :name="$name"
    :hint="$hint"
    :required="$required"
    fieldId="{{ $inputId }}"
>

    <div class="relative">

        @if($prefix)
            <span class="form-prefix">
                {{ $prefix }}
            </span>
        @endif


        <input
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            placeholder="{{ $placeholder }}"
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"

            @if($hasError)
                aria-describedby="{{ $inputId }}-error"
            @endif

            {{ $attributes->class($classes) }}
        />


        @if($hasError)

            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">

                <span class="form-error-badge">

                    <svg
                        class="h-3.5 w-3.5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-1-5.5a1 1 0 112 0 1 1 0 01-2 0v3a1 1 0 01-2 0v-3Zm1 7.5a.875.875 0 100-1.75A.875.875 0 0010 20Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                </span>

            </div>

        @elseif($suffix)

            <span class="form-suffix">
                {{ $suffix }}
            </span>

        @endif

    </div>

</x-form.field>