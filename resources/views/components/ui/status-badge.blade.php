@props([
    'status' => null,
    'variants' => [],
    'emptyText' => '—',
])

@php
    $rawStatus = is_scalar($status) ? trim((string) $status) : '';
    $normalizedStatus = strtolower($rawStatus);
    $defaultVariants = [
        'active' => 'bg-emerald-50 text-emerald-700',
        'success' => 'bg-emerald-50 text-emerald-700',
        'completed' => 'bg-emerald-50 text-emerald-700',
        'paid' => 'bg-emerald-50 text-emerald-700',
        'pending' => 'bg-amber-50 text-amber-700',
        'processing' => 'bg-amber-50 text-amber-700',
        'inactive' => 'bg-slate-100 text-slate-600',
        'cancelled' => 'bg-slate-100 text-slate-600',
        'canceled' => 'bg-slate-100 text-slate-600',
        'failed' => 'bg-red-50 text-red-700',
        'denied' => 'bg-red-50 text-red-700',
        'error' => 'bg-red-50 text-red-700',
    ];

    $classes = array_merge($defaultVariants, $variants);
    $badgeClass = $classes[$normalizedStatus] ?? 'bg-slate-100 text-slate-600';
    $displayStatus = $rawStatus !== '' ? ucfirst(strtolower($rawStatus)) : $emptyText;
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold', $badgeClass]) }}>
    {{ $displayStatus }}
</span>
