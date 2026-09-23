@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'required' => false,
])

@php
    $errorId = $name
        ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $name) . '-error'
        : null;

    $hasError = filled($name) && $errors->has($name);
@endphp

<div class="w-full">

    @if($label)

        <div class="form-field-label">

            <label
                for="{{ $attributes->get('fieldId') }}"
            >
                {{ $label }}

                @if($required)
                    <span class="form-field-required">*</span>
                @endif
            </label>

            @if($hint)
                <span class="form-field-hint">
                    {{ $hint }}
                </span>
            @endif

        </div>

    @endif


    {{ $slot }}


    @if($hasError)

        <div
            id="{{ $errorId }}"
            class="form-error"
        >

            <svg
                class="form-error-icon"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-1-5.5a1 1 0 112 0 1 1 0 012 0v3a1 1 0 01-2 0v-3Zm1 7.5a.875.875 0 100-1.75A.875.875 0 0010 20Z"
                    clip-rule="evenodd"
                />
            </svg>

            <span>
                {{ $errors->first($name) }}
            </span>

        </div>

    @endif

</div>