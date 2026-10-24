<div class="px-4 sm:px-6 lg:px-8">

    @include('pages.stock.partials.alerts')

  

    @include('pages.stock.partials.filters', [
        'locationType' => $locationType,
        'warehouses' => $this->warehouses,
        'shops' => $this->shops,
    ])

    @if(count($selected) > 0)
        <div class="mt-6">
            <x-ui.bulk-actions :count="count($selected)" item-label="stock record" items-label="stock records">
                <x-ui.action-button wire:click="clearBulkSelection">Clear selection</x-ui.action-button>
            </x-ui.bulk-actions>
        </div>
    @endif

    @include('pages.stock.partials.table', [
        'stocks' => $this->stocks,
        'sortField' => $sortField,
        'sortDirection' => $sortDirection,
    ])

    <div class="mt-4">
        {{ $this->stocks->links() }}
    </div>

</div>