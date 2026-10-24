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
    $inputId = $attributes->get('id')
        ?: (filled($name) ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $name) : 'input-' . \Illuminate\Support\Str::uuid());

    $errorId = $inputId . '-error';
    $hasError = filled($name) && $errors->has($name);
    $required = $attributes->has('required');

    $classes = [
        'form-control',
        'form-input',
        'form-control-error' => $hasError,
        'pl-11' => filled($prefix),
        'pr-11' => filled($suffix) || $hasError,
    ];
@endphp

<x-form.field
    :label="$label"
    :name="$name"
    :hint="$hint"
    :required="$required"
    :field-id="$inputId"
>
    <div class="relative">
        @if($prefix)
            <span class="form-prefix">{{ $prefix }}</span>
        @endif

        <input
            id="{{ $inputId }}"
            @if($name) name="{{ $name }}" @endif
            type="{{ $type }}"
            placeholder="{{ $placeholder }}"
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if($hasError) aria-describedby="{{ $errorId }}" @endif
            {{ $attributes->except(['id'])->class($classes) }}
        >

        @if($hasError)
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5" aria-hidden="true">
                <span class="form-error-badge">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-.75-11a.75.75 0 011.5 0v4a.75.75 0 01-1.5 0V7Zm.75 6a1 1 0 100 2 1 1 0 000-2Z" clip-rule="evenodd" />
                    </svg>
                </span>
            </div>
        @elseif($suffix)
            <span class="form-suffix">{{ $suffix }}</span>
        @endif
    </div>
</x-form.field>
