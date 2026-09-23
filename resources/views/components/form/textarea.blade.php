@props([
    'label' => null,
    'name' => null,
    'placeholder' => '',
    'hint' => null,
    'rows' => 5,
])

@php
    $inputId = $name
        ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $name)
        : 'textarea-' . uniqid();

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

    <textarea
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        aria-invalid="{{ $hasError ? 'true' : 'false' }}"

        @if($hasError)
            aria-describedby="{{ $inputId }}-error"
        @endif

        {{ $attributes->class([
            'form-control',
            'form-textarea',
            'form-control-error' => $hasError,
        ]) }}
    ></textarea>

</x-form.field>