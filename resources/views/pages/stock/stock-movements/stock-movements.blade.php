
<div class="px-4 sm:px-6 lg:px-8">

    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">

            <h1 class="text-2xl font-semibold text-gray-900">
                Stock Movements
            </h1>

            <p class="mt-2 text-sm text-gray-700">
                Track stock purchases, sales, transfers and adjustments.
            </p>

        </div>
    </div>



     @include('pages.stock.stock-movements.partials.filters', [
        'locationType' => $locationType,
        'warehouses' => $this->warehouses,
        'shops' => $this->shops,
    ])

        @if(count($selected) > 0)
            <div class="mt-6">
                <x-ui.bulk-actions :count="count($selected)" item-label="stock movement" items-label="stock movements">
                    <x-ui.action-button wire:click="clearBulkSelection">Clear selection</x-ui.action-button>
                </x-ui.bulk-actions>
            </div>
        @endif

 @include('pages.stock.stock-movements.partials.table', [
    'movements' => $this->movements,
    'sortField' => $sortField,
    'sortDirection' => $sortDirection,
])

   

    <div class="mt-4">
        {{ $this->movements->links() }}
    </div>

</div>