<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    @include('pages.orders.partials.header')

    {{-- Order Files --}}
    @include('pages.orders.partials.file-actions')

    {{-- Messages --}}
    @include('pages.orders.partials.messages')

    {{-- Filters --}}
    @include('pages.orders.partials.filters')

    {{-- Bulk Actions --}}
    @include('pages.orders.partials.bulk-actions')

    {{-- Orders Table --}}
    @include('pages.orders.partials.table')

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $this->orders->links() }}
    </div>

</div>