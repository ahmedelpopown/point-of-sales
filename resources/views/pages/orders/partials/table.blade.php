   {{-- Orders Table --}}
    <div class="mt-8 overflow-hidden rounded-2xl bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-300">

                <thead class="bg-gray-50">

                    <tr>
<th class="w-12 px-4 py-3">

    <input
        type="checkbox"
        wire:model.live="selectAll"
        aria-label="Select all orders on this page"
        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
    >

</th>
                        {{-- ID --}}
                        <th
                            wire:click="sortBy('id')"
                            class="cursor-pointer whitespace-nowrap px-4 py-3 text-left text-sm font-semibold text-gray-900"
                        >
                            <div class="flex items-center gap-1">

                                ID

                                @if($sortField === 'id')
                                    <span>
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    </span>
                                @endif

                            </div>
                        </th>


                        <th class="whitespace-nowrap px-4 py-3 text-left text-sm font-semibold text-gray-900">
                            Customer
                        </th>


                        <th class="whitespace-nowrap px-4 py-3 text-left text-sm font-semibold text-gray-900">
                            Employee
                        </th>


                        <th class="whitespace-nowrap px-4 py-3 text-left text-sm font-semibold text-gray-900">
                            Items
                        </th>


                        {{-- Total --}}
                        <th
                            wire:click="sortBy('total')"
                            class="cursor-pointer whitespace-nowrap px-4 py-3 text-left text-sm font-semibold text-gray-900"
                        >
                            <div class="flex items-center gap-1">

                                Total

                                @if($sortField === 'total')
                                    <span>
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    </span>
                                @endif

                            </div>
                        </th>


                        {{-- Date --}}
                        <th
                            wire:click="sortBy('created_at')"
                            class="cursor-pointer whitespace-nowrap px-4 py-3 text-left text-sm font-semibold text-gray-900"
                        >
                            <div class="flex items-center gap-1">

                                Date

                                @if($sortField === 'created_at')
                                    <span>
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    </span>
                                @endif

                            </div>
                        </th>


                        <th class="px-4 py-3"></th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200">

                    @forelse($this->orders as $order)

                        <tr
                            wire:key="order-{{ $order->id }}"
                            class="hover:bg-gray-50"
                        >
                        <td class="px-4 py-4">

    <input
        type="checkbox"
        value="{{ $order->id }}"
        wire:model.live="selected"
        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
    >

</td>

                            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900">
                                #{{ $order->id }}
                            </td>


                            <td class="px-4 py-4 text-sm">
                                {{ $order->user?->name ?? 'Walk-in Customer' }}
                            </td>


                            <td class="px-4 py-4 text-sm">
                                {{ $order->employee?->full_name ?? 'N/A' }}
                            </td>


                            <td class="px-4 py-4 text-sm">
                                {{ $order->order_items_count }}
                            </td>


                            <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold">
                                {{ number_format($order->total, 2) }} EGP
                            </td>


                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">
                                {{ $order->created_at?->format('Y-m-d H:i') }}
                            </td>


                            <td class="px-4 py-4 text-right">

                                <div class="flex justify-end gap-3">

                                    <a
                                        href="{{ route('orders.show', $order) }}"
                                        class="text-sm font-medium text-green-600 hover:text-green-800"
                                    >
                                        Show
                                    </a>

                                    <a
                                        href="{{ route('orders.edit', $order) }}"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                    >
                                        Edit
                                    </a>

                                    <button
                                        type="button"
                                        wire:click="deleteOrder({{ $order->id }})"
                                        wire:confirm="Are you sure you want to delete this order?"
                                        class="text-sm font-medium text-red-600 hover:text-red-800"
                                    >
                                        Delete
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-4 py-12 text-center text-gray-500"
                            >
                                No orders found matching the selected filters.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>