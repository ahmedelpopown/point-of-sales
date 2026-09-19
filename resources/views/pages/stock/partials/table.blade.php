<div class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-50">

                <tr>

                    <th
                        wire:click="sortBy('product')"
                        class="cursor-pointer px-4 py-4 text-left text-sm font-semibold text-gray-900"
                    >
                        Product

                        @if($sortField === 'product')
                            {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>

                    <th class="px-4 py-4 text-left text-sm font-semibold text-gray-900">
                        Location Type
                    </th>

                    <th class="px-4 py-4 text-left text-sm font-semibold text-gray-900">
                        Location
                    </th>

                    <th
                        wire:click="sortBy('quantity')"
                        class="cursor-pointer px-4 py-4 text-left text-sm font-semibold text-gray-900"
                    >
                        Current Quantity

                        @if($sortField === 'quantity')
                            {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>

                    <th
                        wire:click="sortBy('minimum_quantity')"
                        class="cursor-pointer px-4 py-4 text-left text-sm font-semibold text-gray-900"
                    >
                        Minimum

                        @if($sortField === 'minimum_quantity')
                            {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>

                    <th class="px-4 py-4 text-left text-sm font-semibold text-gray-900">
                        Status
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse($stocks as $stock)

                    <tr
                        wire:key="stock-{{ $stock->id }}"
                        class="transition hover:bg-gray-50"
                    >

                        <td class="px-4 py-4">

                            <div class="text-sm font-semibold text-gray-900">
                                {{ $stock->product->name }}
                            </div>

                            <div class="mt-1 text-xs text-gray-500">
                                {{ $stock->product->barcode }}
                            </div>

                        </td>

                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $this->locationTypeName($stock) }}
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $this->locationName($stock) }}
                        </td>

                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">
                            {{ number_format($stock->quantity) }}
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ number_format($stock->minimum_quantity) }}
                        </td>

                        <td class="px-4 py-4">

                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $this->stockStatusClass($stock) }}"
                            >
                                {{ $this->stockStatus($stock) }}
                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="px-4 py-12 text-center text-sm text-gray-500"
                        >
                            No stock records found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>