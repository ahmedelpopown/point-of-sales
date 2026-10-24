@props([
    'type' => 'success',
    'message',
])

@php
    $styles = [
        'success' => ['wrapper' => 'border-emerald-100 bg-emerald-50', 'icon' => 'text-emerald-500', 'text' => 'text-emerald-700'],
        'error' => ['wrapper' => 'border-red-100 bg-red-50', 'icon' => 'text-red-500', 'text' => 'text-red-700'],
        'warning' => ['wrapper' => 'border-amber-100 bg-amber-50', 'icon' => 'text-amber-500', 'text' => 'text-amber-700'],
    ];

    $style = $styles[$type] ?? $styles['success'];
@endphp

<div
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->class(['rounded-2xl border px-4 py-3.5', $style['wrapper']]) }}
>
    <div class="flex items-start gap-3">
        <svg class="mt-0.5 h-5 w-5 shrink-0 {{ $style['icon'] }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            @if($type === 'error')
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-.75-12a.75.75 0 011.5 0v4a.75.75 0 01-1.5 0V6Zm.75 6a1 1 0 100 2 1 1 0 000-2Z" clip-rule="evenodd" />
            @elseif($type === 'warning')
                <path fill-rule="evenodd" d="M8.485 2.495a1.75 1.75 0 013.03 0l7.06 12.25A1.75 1.75 0 0117.06 17.5H2.94a1.75 1.75 0 01-1.515-2.755l7.06-12.25ZM10 6.25a.75.75 0 00-.75.75v3.5a.75.75 0 001.5 0V7a.75.75 0 00-.75-.75Zm0 7a1 1 0 100 2 1 1 0 000-2Z" clip-rule="evenodd" />
            @else
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-10.707a1 1 0 00-1.414-1.414L9 9.172 7.707 7.879a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4Z" clip-rule="evenodd" />
            @endif
        </svg>

        <p class="text-sm font-medium {{ $style['text'] }}">{{ $message }}</p>
    </div>
</div>
