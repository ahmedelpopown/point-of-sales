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
    $inputId = $attributes->get('id')
        ?: (filled($name) ? preg_replace('/[^A-Za-z0-9_-]+/', '-', $name) : 'select-' . \Illuminate\Support\Str::uuid());

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
    <div class="relative">
        <select
            id="{{ $inputId }}"
            @if($name) name="{{ $name }}" @endif
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if($hasError) aria-describedby="{{ $errorId }}" @endif
            {{ $attributes->except(['id'])->class([
                'form-control',
                'form-select',
                'form-control-error' => $hasError,
            ]) }}
        >
            <option value="">{{ $placeholder }}</option>

            @foreach($options as $key => $option)
                @php
                    if ($optionValue) {
                        $value = data_get($option, $optionValue);
                        $text = $optionLabel ? data_get($option, $optionLabel) : $value;
                    } else {
                        $value = $key;
                        $text = $optionLabel ? data_get($option, $optionLabel) : $option;
                    }
                @endphp

                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>

        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4" aria-hidden="true">
            @if($hasError)
                <span class="form-error-badge">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-.75-11a.75.75 0 011.5 0v4a.75.75 0 01-1.5 0V7Zm.75 6a1 1 0 100 2 1 1 0 000-2Z" clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.03l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.05Z" clip-rule="evenodd" />
                </svg>
            @endif
        </div>
    </div>
</x-form.field>
