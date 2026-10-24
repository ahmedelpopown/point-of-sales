@props([
    'label' => null,
    'name' => null,
    'placeholder' => '',
    'hint' => null,
    'rows' => 5,
])

@php
    $inputId = $attributes->get('id')
        ?: (filled($name) ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $name) : 'textarea-' . \Illuminate\Support\Str::uuid());

    $errorId = $inputId . '-error';
    $hasError = filled($name) && $errors->has($name);
    $required = $attributes->has('required');
@endphp

<x-form.field
    :label="$label"
    :name="$name"
    :hint="$hint"
    :required="$required"
    :field-id="$inputId"
>
    <textarea
        id="{{ $inputId }}"
        @if($name) name="{{ $name }}" @endif
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        aria-invalid="{{ $hasError ? 'true' : 'false' }}"
        @if($hasError) aria-describedby="{{ $errorId }}" @endif
        {{ $attributes->except(['id'])->class([
            'form-control',
            'form-textarea',
            'form-control-error' => $hasError,
        ]) }}
    ></textarea>
</x-form.field>
