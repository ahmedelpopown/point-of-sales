@props([
    'title' => 'No records found.',
    'description' => 'Try changing your search or filters.',
])

<div {{ $attributes->class('px-6 py-14 text-center') }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400" aria-hidden="true">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7" />
            <path stroke-linecap="round" d="m20 20-4-4" />
        </svg>
    </div>

    <p class="mt-3 text-sm font-semibold text-slate-700">{{ $title }}</p>

    @if($description)
        <p class="mt-1 text-xs text-slate-400">{{ $description }}</p>
    @endif
</div>
