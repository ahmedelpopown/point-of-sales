@props([
    'items',
    'columns' => [],
    'emptyText' => 'No records found.',
    'selectable' => false,
    'selectedModel' => 'selected',
    'selectAllModel' => 'selectAll',
    'sortField' => null,
    'sortDirection' => 'asc',

    'showRoute' => null,
    'editRoute' => null,
    'deleteMethod' => null,

    'rowKey' => 'id',
    'showPagination' => true,
    'compact' => false,
])
{{-- ========================================================= --}}
{{-- Desktop --}}
{{-- ========================================================= --}}

<div class="hidden lg:block">

    <div
        class="
            overflow-hidden rounded-2xl border border-slate-200
            bg-white shadow-sm
        "
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                {{-- ================================================= --}}
                {{-- Head --}}
                {{-- ================================================= --}}

                <thead class="border-b border-slate-200 bg-slate-50/80">

                    <tr>

                        {{-- Select All --}}
                        @if($selectable)

                            <th class="w-12 px-4 py-3.5">

                                <input
                                    type="checkbox"
                                    wire:model.live="{{ $selectAllModel }}"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                >

                            </th>

                        @endif


                        {{-- Columns --}}
                        @foreach($columns as $column)

                            @php
                                $sortable = $column['sortable'] ?? false;
                                $isActiveSort = $sortField === ($column['field'] ?? null);
                            @endphp

                            <th
                                @if($sortable)
                                    wire:click="sortBy('{{ $column['field'] }}')"
                                @endif

                                class="
                                    px-4
                                    {{ $compact ? 'py-3' : 'py-3.5' }}
                                    text-left
                                    text-[11px]
                                    font-semibold
                                    uppercase
                                    tracking-wider
                                    text-slate-500
                                    whitespace-nowrap
                                    {{ $sortable ? 'cursor-pointer select-none hover:text-slate-900' : '' }}
                                "
                            >

                                <div class="flex items-center gap-1.5">

                                    <span>
                                        {{ $column['label'] }}
                                    </span>

                                    @if($sortable)

                                        <span
                                            class="
                                                text-[10px]
                                                {{ $isActiveSort
                                                    ? 'text-indigo-600'
                                                    : 'text-slate-300'
                                                }}
                                            "
                                        >
                                            @if($isActiveSort)
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            @else
                                                ↕
                                            @endif
                                        </span>

                                    @endif

                                </div>

                            </th>

                        @endforeach


                        {{-- Actions --}}
                      @if($showRoute || $editRoute || $deleteMethod)

                            <th class="w-28 px-4 py-3.5 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                Actions
                            </th>

                        @endif

                    </tr>

                </thead>


                {{-- ================================================= --}}
                {{-- Body --}}
                {{-- ================================================= --}}

                <tbody class="divide-y divide-slate-100">

                    @forelse($items as $item)

                        <tr
                            wire:key="row-{{ data_get($item, $rowKey) }}"
                            class="group transition-colors hover:bg-slate-50/70"
                        >

                            {{-- Select --}}
                            @if($selectable)

                                <td class="px-4 {{ $compact ? 'py-3' : 'py-3.5' }}">

                                    <input
                                        type="checkbox"
                                        wire:model.live="{{ $selectedModel }}"
                                        value="{{ data_get($item, $rowKey) }}"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    >

                                </td>

                            @endif


                            {{-- Data --}}
                            @foreach($columns as $column)

                                @php
                                    $value = data_get($item, $column['field']);
                                    $type = $column['type'] ?? 'text';
                                @endphp

                                <td
                                    class="
                                        px-4
                                        {{ $compact ? 'py-3' : 'py-3.5' }}
                                        text-sm
                                        text-slate-600
                                    "
                                >

                                    @if($type === 'avatar')

                                        <div class="flex min-w-0 items-center gap-2.5">

                                            <div
                                                class="
                                                    flex shrink-0 items-center justify-center
                                                    {{ $compact ? 'h-8 w-8 rounded-lg text-xs' : 'h-9 w-9 rounded-xl text-sm' }}
                                                    bg-indigo-50
                                                    font-bold
                                                    text-indigo-600
                                                "
                                            >
                                                {{ strtoupper(substr($value ?: '?', 0, 1)) }}
                                            </div>

                                            <span class="truncate font-semibold text-slate-900">
                                                {{ $value ?: '—' }}
                                            </span>

                                        </div>


                                    @elseif($type === 'badge')

                                        @php
                                            $badgeClasses = $column['badgeClasses'] ?? [];

                                            $badgeClass =
                                                $badgeClasses[$value]
                                                ?? 'bg-slate-100 text-slate-600';
                                        @endphp

                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badgeClass }}"
                                        >
                                            {{ ucfirst($value ?: '—') }}
                                        </span>


                                    @elseif($type === 'money')

                                        <span class="font-semibold text-slate-900">
                                            {{ number_format((float) $value, 2) }}
                                        </span>


                                    @elseif($type === 'date')

                                        <span class="text-slate-500">
                                            {{ $value ?: '—' }}
                                        </span>


                                    @else

                                        <span
                                            class="
                                                {{ $column['bold'] ?? false
                                                    ? 'font-semibold text-slate-900'
                                                    : ''
                                                }}
                                            "
                                        >
                                            {{ filled($value) ? $value : '—' }}
                                        </span>

                                    @endif

                                </td>

                            @endforeach


                            {{-- Actions --}}
                         <td class="     px-4
                                        {{ $compact ? 'py-3' : 'py-3.5' }}
                                        text-sm"> 
                            @if($showRoute)

    <a
        href="{{ route($showRoute, $item) }}"
        wire:navigate
        class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
    >
        View
    </a>

@endif


@if($editRoute)

    <a
        href="{{ route($editRoute, $item) }}"
        wire:navigate
        class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
    >
        Edit
    </a>

@endif


@if($deleteMethod)

    <button
        type="button"
        wire:click="{{ $deleteMethod }}({{ data_get($item, $rowKey) }})"
        class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 hover:text-red-700"
    >
        Delete
    </button>

@endif
                         </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="{{ count($columns) + ($selectable ? 1 : 0) + (($editRoute || $deleteMethod) ? 1 : 0) }}"
                                class="px-6 py-14 text-center"
                            >

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">

                                    <svg
                                        class="h-6 w-6"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    >
                                        <circle
                                            cx="11"
                                            cy="11"
                                            r="7"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            d="m20 20-4-4"
                                        />
                                    </svg>

                                </div>

                                <p class="mt-3 text-sm font-semibold text-slate-700">
                                    {{ $emptyText }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Try changing your search or filters.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ================================================= --}}
        {{-- Pagination --}}
        {{-- ================================================= --}}

        @if($showPagination)

            @if(method_exists($items, 'hasPages') && $items->hasPages())

                <div class="border-t border-slate-200 px-4 py-3">

                    <div class="flex justify-end">

                        {{ $items->links() }}

                    </div>

                </div>

            @endif

        @endif

    </div>

</div>


{{-- ========================================================= --}}
{{-- Mobile --}}
{{-- ========================================================= --}}

<div class="space-y-3 lg:hidden">

    @forelse($items as $item)

        @php
            $firstColumn = $columns[0] ?? null;

            $firstValue = $firstColumn
                ? data_get($item, $firstColumn['field'])
                : null;
        @endphp

        <div
            wire:key="mobile-row-{{ data_get($item, $rowKey) }}"
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
        >

            <div class="flex items-start gap-3">

                {{-- Select --}}
                @if($selectable)

                    <input
                        type="checkbox"
                        wire:model.live="{{ $selectedModel }}"
                        value="{{ data_get($item, $rowKey) }}"
                        class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    >

                @endif


                {{-- Avatar --}}
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-600"
                >
                    {{ strtoupper(substr($firstValue ?: '?', 0, 1)) }}
                </div>


                <div class="min-w-0 flex-1">

                    {{-- Main --}}
                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <h3 class="truncate text-sm font-semibold text-slate-900">
                                {{ $firstValue ?: '—' }}
                            </h3>

                            @if(isset($columns[1]))

                                <p class="mt-0.5 truncate text-xs text-slate-500">
                                    {{ data_get($item, $columns[1]['field']) ?: '—' }}
                                </p>

                            @endif

                        </div>


                        {{-- Badge --}}
                        @foreach($columns as $column)

                            @if(($column['type'] ?? null) === 'badge')

                                @php
                                    $badgeValue = data_get(
                                        $item,
                                        $column['field']
                                    );

                                    $badgeClasses =
                                        $column['badgeClasses'] ?? [];

                                    $badgeClass =
                                        $badgeClasses[$badgeValue]
                                        ?? 'bg-slate-100 text-slate-600';
                                @endphp

                                <span
                                    class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badgeClass }}"
                                >
                                    {{ ucfirst($badgeValue ?: '—') }}
                                </span>

                                @break

                            @endif

                        @endforeach

                    </div>


                    {{-- Details --}}
                    <div class="mt-3 grid grid-cols-2 gap-2">

                        @foreach(array_slice($columns, 2) as $column)

                            @if(($column['mobile'] ?? true) === false)
                                @continue
                            @endif

                            @php
                                $value = data_get(
                                    $item,
                                    $column['field']
                                );

                                $type =
                                    $column['type'] ?? 'text';
                            @endphp

                            <div class="rounded-lg bg-slate-50 px-3 py-2.5">

                                <p class="text-[10px] font-medium uppercase tracking-wide text-slate-400">
                                    {{ $column['label'] }}
                                </p>


                                @if($type === 'badge')

                                    <span
                                        class="mt-1 inline-flex rounded-full px-2 py-1 text-[10px] font-semibold {{ $column['badgeClasses'][$value] ?? 'bg-slate-100 text-slate-600' }}"
                                    >
                                        {{ ucfirst($value ?: '—') }}
                                    </span>

                                @else

                                    <p class="mt-0.5 truncate text-xs font-semibold text-slate-700">
                                        {{ filled($value) ? $value : '—' }}
                                    </p>

                                @endif

                            </div>

                        @endforeach

                    </div>


                    {{-- Actions --}}
            @if($showRoute)

    <a
        href="{{ route($showRoute, $item) }}"
        wire:navigate
        class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
    >
        View
    </a>

@endif


@if($editRoute)

    <a
        href="{{ route($editRoute, $item) }}"
        wire:navigate
        class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
    >
        Edit
    </a>

@endif


@if($deleteMethod)

    <button
        type="button"
        wire:click="{{ $deleteMethod }}({{ data_get($item, $rowKey) }})"
        class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 hover:text-red-700"
    >
        Delete
    </button>

@endif

                </div>

            </div>

        </div>

    @empty

        <div class="rounded-xl border border-slate-200 bg-white px-6 py-14 text-center shadow-sm">

            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">

                <svg
                    class="h-6 w-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                >
                    <circle
                        cx="11"
                        cy="11"
                        r="7"
                    />

                    <path
                        stroke-linecap="round"
                        d="m20 20-4-4"
                    />
                </svg>

            </div>

            <p class="mt-3 text-sm font-semibold text-slate-700">
                {{ $emptyText }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Try changing your search or filters.
            </p>

        </div>

    @endforelse


    {{-- Mobile Pagination --}}
    @if($showPagination)

        @if(method_exists($items, 'hasPages') && $items->hasPages())

            <div class="pt-2">

                {{ $items->links() }}

            </div>

        @endif

    @endif

</div>