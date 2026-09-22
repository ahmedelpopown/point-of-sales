
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

 @include('pages.stock.stock-movements.partials.table', [
    'movements' => $this->movements,
    'sortField' => $sortField,
    'sortDirection' => $sortDirection,
])

   

    <div class="mt-4">
        {{ $this->movements->links() }}
    </div>

</div>