@props([
    'label',
    'value',
    'valueClass' => 'text-slate-900',
    'description' => null,
])

<section {{ $attributes->class('rounded-2xl border border-slate-200 bg-white p-5 shadow-sm') }}>
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold tracking-tight {{ $valueClass }}">{{ $value }}</p>
    @if($description)
        <p class="mt-1 text-xs text-slate-500">{{ $description }}</p>
    @endif
</section>
