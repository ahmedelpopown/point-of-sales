@props([
    'type' => 'success',
    'message',
])

@php
    $styles = [
        'success' => [
            'wrapper' => 'border-emerald-100 bg-emerald-50',
            'icon' => 'text-emerald-500',
            'text' => 'text-emerald-700',
        ],

        'error' => [
            'wrapper' => 'border-red-100 bg-red-50',
            'icon' => 'text-red-500',
            'text' => 'text-red-700',
        ],

        'warning' => [
            'wrapper' => 'border-amber-100 bg-amber-50',
            'icon' => 'text-amber-500',
            'text' => 'text-amber-700',
        ],
    ];

    $style = $styles[$type] ?? $styles['success'];
@endphp

<div class="rounded-2xl border px-4 py-3.5 {{ $style['wrapper'] }}">

    <div class="flex items-start gap-3">

        <svg
            class="mt-0.5 h-5 w-5 shrink-0 {{ $style['icon'] }}"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
        >
            @if($type === 'error')

                <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16ZM10 5.75a.75.75 0 01.75.75v3a.75.75 0 01-1.5 0v-3A.75.75 0 0110 5.75ZM10 13a.875.875 0 100 1.75A.875.875 0 0010 13Z"
                    clip-rule="evenodd"
                />

            @else

                <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-10.707a1 1 0 00-1.414-1.414L9 9.172 7.707 7.879a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4Z"
                    clip-rule="evenodd"
                />

            @endif
        </svg>

        <p class="text-sm font-medium {{ $style['text'] }}">
            {{ $message }}
        </p>

    </div>

</div>