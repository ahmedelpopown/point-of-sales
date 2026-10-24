@props([
    'items',
    'columns' => [],
    'emptyText' => 'No records found.',
    'emptyDescription' => 'Try changing your search or filters.',
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
    'mobilePrimaryField' => null,
    'mobileSecondaryField' => null,
    'mobileShowAvatar' => null,
    'mobileShowBadge' => true,
])

@php
    $hasActions = filled($showRoute) || filled($editRoute) || filled($deleteMethod);
    $emptyColspan = count($columns) + ($selectable ? 1 : 0) + ($hasActions ? 1 : 0);

    $primaryColumn = null;
    if ($mobilePrimaryField) {
        foreach ($columns as $candidate) {
            if (($candidate['field'] ?? null) === $mobilePrimaryField) {
                $primaryColumn = $candidate;
                break;
            }
        }
    }

    if (!$primaryColumn) {
        foreach ($columns as $candidate) {
            if (($candidate['type'] ?? null) === 'avatar') {
                $primaryColumn = $candidate;
                break;
            }
        }
    }

    $primaryColumn ??= $columns[0] ?? null;
    $primaryField = $mobilePrimaryField ?: ($primaryColumn['field'] ?? null);

    $secondaryColumn = null;
    if ($mobileSecondaryField) {
        foreach ($columns as $candidate) {
            if (($candidate['field'] ?? null) === $mobileSecondaryField) {
                $secondaryColumn = $candidate;
                break;
            }
        }
    }

    if (!$secondaryColumn) {
        foreach ($columns as $candidate) {
            if (($candidate['field'] ?? null) !== $primaryField) {
                $secondaryColumn = $candidate;
                break;
            }
        }
    }

    $secondaryField = $mobileSecondaryField ?: ($secondaryColumn['field'] ?? null);
    $showMobileAvatar = $mobileShowAvatar ?? (($primaryColumn['type'] ?? null) === 'avatar');

    $mobileBadgeColumn = null;
    if ($mobileShowBadge) {
        foreach ($columns as $candidate) {
            if (($candidate['type'] ?? null) === 'badge') {
                $mobileBadgeColumn = $candidate;
                break;
            }
        }
    }
@endphp

{{-- Desktop table --}}
<div class="hidden lg:block">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50/80">
                    <tr>
                        @if($selectable)
                            <th scope="col" class="w-12 px-4 {{ $compact ? 'py-3' : 'py-3.5' }}">
                                <input
                                    type="checkbox"
                                    wire:model.live="{{ $selectAllModel }}"
                                    aria-label="Select all records on this page"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                >
                            </th>
                        @endif

                        @foreach($columns as $column)
                            @php
                                $sortBy = $column['sortField'] ?? $column['field'] ?? null;
                                $sortable = ($column['sortable'] ?? false) && filled($sortBy);
                                $isActiveSort = $sortField === $sortBy;
                            @endphp
                            <th scope="col" class="px-4 {{ $compact ? 'py-3' : 'py-3.5' }} text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                                @if($sortable)
                                    <button
                                        type="button"
                                        wire:click="sortBy('{{ $sortBy }}')"
                                        class="inline-flex items-center gap-1.5 transition hover:text-slate-900 focus:outline-none focus:text-indigo-700"
                                        aria-label="Sort by {{ $column['label'] }}"
                                    >
                                        <span>{{ $column['label'] }}</span>
                                        <span class="text-[10px] {{ $isActiveSort ? 'text-indigo-600' : 'text-slate-300' }}" aria-hidden="true">
                                            @if($isActiveSort)
                                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                            @else
                                                ↕
                                            @endif
                                        </span>
                                    </button>
                                @else
                                    {{ $column['label'] }}
                                @endif
                            </th>
                        @endforeach

                        @if($hasActions)
                            <th scope="col" class="px-4 {{ $compact ? 'py-3' : 'py-3.5' }} text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                Actions
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr wire:key="desktop-row-{{ data_get($item, $rowKey) }}" class="group transition-colors hover:bg-slate-50/70">
                            @if($selectable)
                                <td class="px-4 {{ $compact ? 'py-3' : 'py-3.5' }}">
                                    <input
                                        type="checkbox"
                                        wire:model.live="{{ $selectedModel }}"
                                        value="{{ data_get($item, $rowKey) }}"
                                        aria-label="Select record {{ data_get($item, $rowKey) }}"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    >
                                </td>
                            @endif

                            @foreach($columns as $column)
                                @php
                                    $value = data_get($item, $column['field'] ?? '');
                                    $type = $column['type'] ?? 'text';
                                @endphp
                                <td class="px-4 {{ $compact ? 'py-3' : 'py-3.5' }} text-sm text-slate-600">
                                    @if($type === 'avatar')
                                        <div class="flex min-w-0 items-center gap-2.5">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-600" aria-hidden="true">
                                                {{ strtoupper(substr(is_scalar($value) ? (string) $value : '', 0, 1) ?: '?') }}
                                            </div>
                                            <span class="max-w-xs truncate font-semibold text-slate-900">
                                                {{ is_scalar($value) && filled($value) ? $value : '—' }}
                                            </span>
                                        </div>
                                    @elseif($type === 'badge')
                                        <x-ui.status-badge
                                            :status="$value"
                                            :variants="$column['badgeClasses'] ?? []"
                                            :empty-text="$column['emptyText'] ?? '—'"
                                        />
                                    @elseif($type === 'money')
                                        <span class="font-semibold text-slate-900">
                                            {{ is_numeric($value) ? number_format((float) $value, 2) : '—' }}
                                        </span>
                                    @elseif($type === 'date')
                                        <span class="whitespace-nowrap text-slate-500">
                                            {{ $value instanceof \DateTimeInterface ? $value->format($column['dateFormat'] ?? 'Y-m-d') : (filled($value) ? $value : '—') }}
                                        </span>
                                    @else
                                        <span class="{{ ($column['bold'] ?? false) ? 'font-semibold text-slate-900' : '' }} {{ ($column['truncate'] ?? false) ? 'block max-w-xs truncate' : '' }}">
                                            {{ is_scalar($value) && filled($value) ? $value : '—' }}
                                        </span>
                                    @endif
                                </td>
                            @endforeach

                            @if($hasActions)
                                <td class="px-4 {{ $compact ? 'py-3' : 'py-3.5' }} text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if($showRoute)
                                            <a href="{{ route($showRoute, $item) }}" wire:navigate class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">View</a>
                                        @endif
                                        @if($editRoute)
                                            <a href="{{ route($editRoute, $item) }}" wire:navigate class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50 hover:text-indigo-800">Edit</a>
                                        @endif
                                        @if($deleteMethod)
                                            <button
                                                type="button"
                                                wire:click="{{ $deleteMethod }}({{ \Illuminate\Support\Js::from(data_get($item, $rowKey)) }})"
                                                class="inline-flex h-8 items-center rounded-lg px-2.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 hover:text-red-700"
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
                            <td colspan="{{ max(1, $emptyColspan) }}" class="px-6 py-8">
                                <x-ui.empty-state :title="$emptyText" :description="$emptyDescription" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Mobile cards --}}
<div class="space-y-3 lg:hidden">
    @if($selectable)
        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700">
            <input
                type="checkbox"
                wire:model.live="{{ $selectAllModel }}"
                aria-label="Select all records on this page"
                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
            >
            <span>Select all records on this page</span>
        </label>
    @endif

    @forelse($items as $item)
        @php
            $primaryValue = $primaryField ? data_get($item, $primaryField) : null;
            $secondaryValue = $secondaryField ? data_get($item, $secondaryField) : null;
            $mobileBadgeValue = $mobileBadgeColumn ? data_get($item, $mobileBadgeColumn['field']) : null;
        @endphp

        <article wire:key="mobile-row-{{ data_get($item, $rowKey) }}" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start gap-3 p-4">
                @if($selectable)
                    <input
                        type="checkbox"
                        wire:model.live="{{ $selectedModel }}"
                        value="{{ data_get($item, $rowKey) }}"
                        aria-label="Select record {{ data_get($item, $rowKey) }}"
                        class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    >
                @endif

                @if($showMobileAvatar)
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-600" aria-hidden="true">
                        {{ strtoupper(substr(is_scalar($primaryValue) ? (string) $primaryValue : '', 0, 1) ?: '?') }}
                    </div>
                @endif

                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-semibold text-slate-900">
                                {{ is_scalar($primaryValue) && filled($primaryValue) ? $primaryValue : '—' }}
                            </h3>
                            @if($secondaryField && $secondaryField !== $primaryField)
                                <p class="mt-0.5 truncate text-xs text-slate-500">
                                    {{ is_scalar($secondaryValue) && filled($secondaryValue) ? $secondaryValue : '—' }}
                                </p>
                            @endif
                        </div>

                        @if($mobileBadgeColumn)
                            <x-ui.status-badge
                                :status="$mobileBadgeValue"
                                :variants="$mobileBadgeColumn['badgeClasses'] ?? []"
                                :empty-text="$mobileBadgeColumn['emptyText'] ?? '—'"
                                class="shrink-0"
                            />
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-px border-t border-slate-100 bg-slate-100">
                @foreach($columns as $column)
                    @php
                        $field = $column['field'] ?? '';
                        $value = data_get($item, $field);
                        $type = $column['type'] ?? 'text';
                        $isPrimaryOrSecondary = in_array($field, [$primaryField, $secondaryField], true);
                        $isTopBadge = $mobileBadgeColumn && $field === ($mobileBadgeColumn['field'] ?? null);
                        $skipMobile = ($column['mobile'] ?? true) === false;
                    @endphp

                    @if($isPrimaryOrSecondary || $isTopBadge || $skipMobile)
                        @continue
                    @endif

                    <div class="min-w-0 bg-white px-4 py-3">
                        <p class="text-[10px] font-medium uppercase tracking-wide text-slate-400">{{ $column['label'] }}</p>

                        @if($type === 'badge')
                            <div class="mt-1">
                                <x-ui.status-badge :status="$value" :variants="$column['badgeClasses'] ?? []" :empty-text="$column['emptyText'] ?? '—'" />
                            </div>
                        @elseif($type === 'money')
                            <p class="mt-1 truncate text-xs font-semibold text-slate-700">{{ is_numeric($value) ? number_format((float) $value, 2) : '—' }}</p>
                        @elseif($type === 'date')
                            <p class="mt-1 truncate text-xs font-semibold text-slate-700">{{ $value instanceof \DateTimeInterface ? $value->format($column['dateFormat'] ?? 'Y-m-d') : (filled($value) ? $value : '—') }}</p>
                        @else
                            <p class="mt-1 {{ ($column['wrapMobile'] ?? false) ? 'break-words' : 'truncate' }} text-xs font-semibold text-slate-700">
                                {{ is_scalar($value) && filled($value) ? $value : '—' }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($hasActions)
                <div class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50/70 px-4 py-3">
                    @if($showRoute)
                        <a href="{{ route($showRoute, $item) }}" wire:navigate class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50">View</a>
                    @endif
                    @if($editRoute)
                        <a href="{{ route($editRoute, $item) }}" wire:navigate class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-indigo-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-indigo-50">Edit</a>
                    @endif
                    @if($deleteMethod)
                        <button
                            type="button"
                            wire:click="{{ $deleteMethod }}({{ \Illuminate\Support\Js::from(data_get($item, $rowKey)) }})"
                            class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-red-50"
                        >
                            Delete
                        </button>
                    @endif
                </div>
            @endif
        </article>
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <x-ui.empty-state :title="$emptyText" :description="$emptyDescription" />
        </div>
    @endforelse
</div>

{{-- Render pagination once, not once for desktop and again for mobile. --}}
{{-- Pagination --}}
@if(
    $showPagination
    && is_object($items)
    && method_exists($items, 'hasPages')
    && $items->hasPages()
)
    <div class="mt-5 overflow-x-auto">
        <div class="min-w-max">
            {{ $items->links() }}
        </div>
    </div>
@endif