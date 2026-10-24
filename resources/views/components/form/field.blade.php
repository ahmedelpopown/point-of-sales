@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'required' => false,
    'fieldId' => null,
])

@php
    $fieldId ??= filled($name)
        ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $name)
        : null;

    $errorId = $fieldId ? $fieldId . '-error' : null;
    $hasError = filled($name) && $errors->has($name);
@endphp

<div class="w-full">
    @if($label)
        <div class="form-field-label">
            <label @if($fieldId) for="{{ $fieldId }}" @endif>
                {{ $label }}

                @if($required)
                    <span class="form-field-required">*</span>
                @endif
            </label>

            @if($hint)
                <span class="form-field-hint">{{ $hint }}</span>
            @endif
        </div>
    @elseif($hint)
        <p class="form-field-hint mb-2">{{ $hint }}</p>
    @endif

    {{ $slot }}

    @if($hasError)
        <div id="{{ $errorId }}" class="form-error" role="alert">
            <svg class="form-error-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-.75-11a.75.75 0 011.5 0v4a.75.75 0 01-1.5 0V7Zm.75 6a1 1 0 100 2 1 1 0 000-2Z" clip-rule="evenodd" />
            </svg>
            <span>{{ $errors->first($name) }}</span>
        </div>
    @endif
</div>
