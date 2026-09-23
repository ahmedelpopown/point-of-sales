@props([
    'variant' => 'secondary',
    'type' => 'button',
    'loadingTarget' => null,
])

@php
    $variants = [
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 rounded-xl hover:bg-slate-50 focus:ring-slate-500/10',
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500/20',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500/20',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500/20',
    ];
@endphp

<button
    type="{{ $type }}"

    @if($loadingTarget)
        wire:loading.attr="disabled"
        wire:target="{{ $loadingTarget }}"
    @endif

    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2',
        'rounded-xl px-4 py-2.5',
        'text-sm font-semibold',
        'shadow-sm',
        'transition-all duration-200',
        'outline-none',
        'focus:ring-4',
        'disabled:cursor-not-allowed',
        'disabled:opacity-60',
        
        $variants[$variant] ?? $variants['secondary'],
    ]) }}
>
    {{ $slot }}
</button>