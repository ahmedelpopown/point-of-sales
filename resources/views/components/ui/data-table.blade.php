@props([
    'items',
    'columns' => [],
    'emptyText' => 'No records found.',
    'selectable' => false,
    'selectedModel' => 'selected',
    'selectAllModel' => 'selectAll',
    'sortField' => null,
    'sortDirection' => 'asc',
    'editRoute' => null,
    'deleteMethod' => null,
    'rowKey' => 'id',
])

{{-- Desktop --}}
<div class="hidden lg:block">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>

                        @if($selectable)
                            <th class="w-12 px-6 py-4">
                                <input
                                    type="checkbox"
                                    wire:model.live="{{ $selectAllModel }}"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                >
                            </th>
                        @endif

                        @foreach($columns as $column)

                            <th
                                @if($column['sortable'] ?? false)
                                    wire:click="sortBy('{{ $column['field'] }}')"
                                    class="cursor-pointer"
                                @endif

                                class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >

                                <div class="flex items-center gap-2">
                                    {{ $column['label'] }}

                                    @if(
                                        ($column['sortable'] ?? false) &&
                                        $sortField === $column['field']
                                    )
                                        <span class="text-indigo-600">
                                            {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    @endif
                                </div>

                            </th>

                        @endforeach

                        @if($editRoute || $deleteMethod)
                            <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                                Actions
                            </th>
                        @endif

                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100 bg-white">

                    @forelse($items as $item)

                        <tr
                            wire:key="row-{{ data_get($item, $rowKey) }}"
                            class="transition hover:bg-slate-50/70"
                        >

                            @if($selectable)
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        wire:model.live="{{ $selectedModel }}"
                                        value="{{ data_get($item, $rowKey) }}"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    >
                                </td>
                            @endif


                            @foreach($columns as $column)

                                @php
                                    $value = data_get($item, $column['field']);
                                    $type = $column['type'] ?? 'text';
                                @endphp

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">

                                    @if($type === 'avatar')

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 font-bold text-indigo-600">
                                                {{ strtoupper(substr($value ?: '?', 0, 1)) }}
                                            </div>

                                            <span class="font-semibold text-slate-900">
                                                {{ $value ?: '—' }}
                                            </span>

                                        </div>


                                    @elseif($type === 'badge')

                                        @php
                                            $badgeClasses = $column['badgeClasses'] ?? [];

                                            $badgeClass = $badgeClasses[$value]
                                                ?? 'bg-slate-100 text-slate-600';
                                        @endphp

                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}"
                                        >
                                            {{ ucfirst($value ?: '—') }}
                                        </span>


                                    @else

                                        {{ filled($value) ? $value : '—' }}

                                    @endif

                                </td>

                            @endforeach


                            @if($editRoute || $deleteMethod)

                                <td class="whitespace-nowrap px-6 py-4">

                                    <div class="flex justify-end gap-2">

                                        @if($editRoute)
                                            <a
                                                href="{{ route($editRoute, $item) }}"
                                                class="inline-flex items-center rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-200"
                                            >
                                                Edit
                                            </a>
                                        @endif

                                        @if($deleteMethod)
                                            <button
                                                wire:click="{{ $deleteMethod }}({{ data_get($item, $rowKey) }})"
                                                class="inline-flex items-center rounded-xl bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100"
                                            >
                                                Delete
                                            </button>
                                        @endif

                                    </div>

                                </td>

                            @endif

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="{{ count($columns) + ($selectable ? 1 : 0) + (($editRoute || $deleteMethod) ? 1 : 0) }}"
                                class="px-6 py-16 text-center"
                            >

                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                                    <svg
                                        class="h-7 w-7"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
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

                                <p class="mt-4 text-sm font-semibold text-slate-700">
                                    {{ $emptyText }}
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Try changing your search or filters.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>
</div>


{{-- Mobile --}}
<div class="space-y-4 lg:hidden">

    @forelse($items as $item)

        <div
            wire:key="mobile-row-{{ data_get($item, $rowKey) }}"
            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
        >

            <div class="flex items-start gap-3">

                @if($selectable)
                    <input
                        type="checkbox"
                        wire:model.live="{{ $selectedModel }}"
                        value="{{ data_get($item, $rowKey) }}"
                        class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    >
                @endif


                @php
                    $firstColumn = $columns[0] ?? null;
                    $firstValue = $firstColumn
                        ? data_get($item, $firstColumn['field'])
                        : null;
                @endphp


                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 font-bold text-indigo-600">
                    {{ strtoupper(substr($firstValue ?: '?', 0, 1)) }}
                </div>


                <div class="min-w-0 flex-1">

                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <h3 class="truncate text-sm font-bold text-slate-900">
                                {{ $firstValue ?: '—' }}
                            </h3>

                            @if(isset($columns[1]))
                                <p class="mt-1 truncate text-xs text-slate-500">
                                    {{ data_get($item, $columns[1]['field']) ?: '—' }}
                                </p>
                            @endif

                        </div>


                        @foreach($columns as $column)

                            @if(($column['type'] ?? null) === 'badge')

                                @php
                                    $value = data_get($item, $column['field']);

                                    $badgeClasses = $column['badgeClasses'] ?? [];

                                    $badgeClass = $badgeClasses[$value]
                                        ?? 'bg-slate-100 text-slate-600';
                                @endphp

                                <span
                                    class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}"
                                >
                                    {{ ucfirst($value ?: '—') }}
                                </span>

                                @break

                            @endif

                        @endforeach

                    </div>


                    <div class="mt-4 grid grid-cols-2 gap-3">

                        @foreach(array_slice($columns, 2) as $column)

                            @if(($column['mobile'] ?? true) === false)
                                @continue
                            @endif

                            @php
                                $value = data_get($item, $column['field']);
                                $type = $column['type'] ?? 'text';
                            @endphp

                            <div class="rounded-xl bg-slate-50 p-3">

                                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                    {{ $column['label'] }}
                                </p>

                                @if($type === 'badge')

                                    <span
                                        class="mt-1 inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ ($column['badgeClasses'][$value] ?? 'bg-slate-100 text-slate-600') }}"
                                    >
                                        {{ ucfirst($value ?: '—') }}
                                    </span>

                                @else

                                    <p class="mt-1 truncate text-sm font-semibold text-slate-700">
                                        {{ filled($value) ? $value : '—' }}
                                    </p>

                                @endif

                            </div>

                        @endforeach

                    </div>


                    @if($editRoute || $deleteMethod)

                        <div class="mt-4 flex gap-2">

                            @if($editRoute)
                                <a
                                    href="{{ route($editRoute, $item) }}"
                                    class="inline-flex flex-1 items-center justify-center rounded-xl bg-slate-100 px-3 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-200"
                                >
                                    Edit
                                </a>
                            @endif

                            @if($deleteMethod)
                                <button
                                    wire:click="{{ $deleteMethod }}({{ data_get($item, $rowKey) }})"
                                    class="inline-flex flex-1 items-center justify-center rounded-xl bg-red-50 px-3 py-2.5 text-xs font-semibold text-red-700 transition hover:bg-red-100"
                                >
                                    Delete
                                </button>
                            @endif

                        </div>

                    @endif

                </div>

            </div>

        </div>

    @empty

        <div class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                <svg
                    class="h-7 w-7"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
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

            <p class="mt-4 text-sm font-semibold text-slate-700">
                {{ $emptyText }}
            </p>

            <p class="mt-1 text-sm text-slate-400">
                Try changing your search or filters.
            </p>

        </div>

    @endforelse

</div>


{{-- Pagination --}}
<div class="mt-6 overflow-x-auto">
    <div class="min-w-max">
        {{ $items->links() }}
    </div>
</div>