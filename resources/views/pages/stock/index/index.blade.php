<div class="px-4 sm:px-6 lg:px-8">

    @include('pages.stock.partials.alerts')

  

    @include('pages.stock.partials.filters', [
        'locationType' => $locationType,
        'warehouses' => $this->warehouses,
        'shops' => $this->shops,
    ])

    @include('pages.stock.partials.table', [
        'stocks' => $this->stocks,
        'sortField' => $sortField,
        'sortDirection' => $sortDirection,
    ])

    <div class="mt-4">
        {{ $this->stocks->links() }}
    </div>

</div>