 {{-- Table --}}
    <div class="mt-8 flex flex-col">

        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">

            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">

                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">

                    <table class="min-w-full divide-y divide-gray-300">

                        <thead class="bg-gray-50">

                            <tr>

                                <th
                                    wire:click="sortBy('product')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Product
                                    @if($sortField === 'product')
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Location
                                </th>

                                <th
                                    wire:click="sortBy('type')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Type
                                    @if($sortField === 'type')
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th
                                    wire:click="sortBy('quantity')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Quantity
                                    @if($sortField === 'quantity')
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Before
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    After
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Employee
                                </th>

                                <th
                                    wire:click="sortBy('created_at')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Date
                                    @if($sortField === 'created_at')
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse($this->movements as $movement)

                            <tr
                                wire:key="movement-{{ $movement->id }}"
                                class="hover:bg-gray-50">

                                <td class="whitespace-nowrap px-3 py-4 text-sm">

                                    <div class="font-medium text-gray-900">
                                        {{ $movement->product->name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $movement->product->barcode }}
                                    </div>

                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $this->locationName($movement) }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm">

                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $this->movementTypeClass($movement) }}">
                                        {{ $this->movementTypeName($movement) }}
                                    </span>

                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">
                                    {{ number_format($movement->quantity) }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ number_format($movement->quantity_before) }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">
                                    {{ number_format($movement->quantity_after) }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $movement->employee?->full_name ?? 'System' }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                   {{ number_format($this->statistics['total_movements']) }}
                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td
                                    colspan="8"
                                    class="px-3 py-8 text-center text-sm text-gray-500">
                                    No stock movements found.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>