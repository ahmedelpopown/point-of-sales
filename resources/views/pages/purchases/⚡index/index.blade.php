<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="sm:flex sm:items-center">

        <div class="sm:flex-auto">

            <h1 class="text-2xl font-semibold text-gray-900">
                Purchases
            </h1>

            <p class="mt-2 text-sm text-gray-700">
                A list of all purchase transactions.
            </p>

        </div>

        <div class="mt-4 sm:mt-0">

            <a
                href="{{ route('purchases.create') }}"
                class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
            >
                Add Purchase
            </a>

        </div>

    </div>

    {{-- Success --}}
    @if(session()->has('message'))

        <div class="mt-4 rounded-md bg-green-50 p-4">

            <p class="text-sm font-medium text-green-800">
                {{ session('message') }}
            </p>

        </div>

    @endif

    {{-- Error --}}
    @if(session()->has('error'))

        <div class="mt-4 rounded-md bg-red-50 p-4">

            <p class="text-sm font-medium text-red-800">
                {{ session('error') }}
            </p>

        </div>

    @endif

    {{-- Search --}}
    <div class="mt-8 flex flex-col gap-4 md:flex-row">

        <div class="flex-1">

            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search supplier or employee..."
                class="block w-full rounded-lg border-gray-300"
            >

        </div>

        <button
            wire:click="resetFilters"
            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            Reset
        </button>

    </div>

    {{-- Table --}}
    <div class="mt-8 flex flex-col">

        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">

            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">

                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">

                    <table class="min-w-full divide-y divide-gray-300">

                        <thead class="bg-gray-50">

                            <tr>

                                <th
                                    wire:click="sortBy('id')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >
                                    ID

                                    @if($sortField === 'id')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Supplier
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Employee
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Items
                                </th>

                                <th
                                    wire:click="sortBy('total_price')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >
                                    Total

                                    @if($sortField === 'total_price')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th
                                    wire:click="sortBy('created_at')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >
                                    Date

                                    @if($sortField === 'created_at')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">
                                        Actions
                                    </span>
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse($this->purchases as $purchase)

                                <tr
                                    wire:key="purchase-{{ $purchase->id }}"
                                    class="hover:bg-gray-50"
                                >

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        #{{ $purchase->id }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">
                                        {{ $purchase->supplier->name ?? 'N/A' }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $purchase->employee->full_name ?? 'N/A' }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $purchase->purchaseItems()->count() }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">
                                        {{ number_format($purchase->total_price, 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $purchase->created_at?->format('Y-m-d H:i') }}
                                    </td>

                                    <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm sm:pr-6">

                                        <div class="flex justify-end gap-3">

                                            <a
                                                href="{{ route('purchases.show', $purchase) }}"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                Show
                                            </a>

                                            <a
                                                href="{{ route('purchases.edit', $purchase) }}"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Edit
                                            </a>

                                            <button
                                                wire:click="deletePurchase({{ $purchase->id }})"
                                                wire:confirm="Are you sure you want to delete this purchase?"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="px-3 py-8 text-center text-sm text-gray-500"
                                    >
                                        No purchases found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $this->purchases->links() }}
    </div>

</div>