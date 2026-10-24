{{-- Backward-compatible alias. Migrate existing usages to x-form.select, then remove this file. --}}
@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'optionValue' => null,
    'optionLabel' => null,
    'placeholder' => 'Select an option',
    'hint' => null,
])

<x-form.select
    :label="$label"
    :name="$name"
    :options="$options"
    :option-value="$optionValue"
    :option-label="$optionLabel"
    :placeholder="$placeholder"
    :hint="$hint"
    {{ $attributes }}
/>
