<div class="px-4 sm:px-6 lg:px-8">

    <div class="sm:flex sm:items-center">

        <div class="sm:flex-auto">

            <h1 class="text-2xl font-semibold text-gray-900">
                Orders
            </h1>

            <p class="mt-2 text-sm text-gray-700">
                A list of all orders.
            </p>

        </div>

        <div class="mt-4 sm:mt-0">

            <a
                href="{{ route('orders.create') }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white"
            >
                Add Order
            </a>

        </div>

    </div>

    @if(session()->has('message'))

        <div class="mt-4 rounded-md bg-green-50 p-4">
            <p class="text-sm text-green-800">
                {{ session('message') }}
            </p>
        </div>

    @endif

    @if(session()->has('error'))

        <div class="mt-4 rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-800">
                {{ session('error') }}
            </p>
        </div>

    @endif

    <div class="mt-8">

        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Search customer or employee..."
            class="block w-full rounded-lg border-gray-300"
        >

    </div>

    <div class="mt-8 overflow-hidden rounded-lg bg-white shadow">

        <table class="min-w-full divide-y divide-gray-300">

            <thead class="bg-gray-50">

                <tr>

                    <th
                        wire:click="sortBy('id')"
                        class="cursor-pointer px-4 py-3 text-left text-sm font-semibold"
                    >
                        ID
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Customer
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Employee
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Items
                    </th>

                    <th
                        wire:click="sortBy('total')"
                        class="cursor-pointer px-4 py-3 text-left text-sm font-semibold"
                    >
                        Total
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Date
                    </th>

                    <th class="px-4 py-3">
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-gray-200">

                @forelse($this->orders as $order)

                    <tr wire:key="order-{{ $order->id }}">

                        <td class="px-4 py-4 text-sm">
                            #{{ $order->id }}
                        </td>

                        <td class="px-4 py-4 text-sm">
                            {{ $order->user->name ?? 'Walk-in Customer' }}
                        </td>

                        <td class="px-4 py-4 text-sm">
                            {{ $order->employee->full_name ?? 'N/A' }}
                        </td>

                        <td class="px-4 py-4 text-sm">
                            {{ $order->orderItems()->count() }}
                        </td>

                        <td class="px-4 py-4 text-sm font-semibold">
                            {{ number_format($order->total, 2) }}
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $order->created_at?->format('Y-m-d H:i') }}
                        </td>

                        <td class="px-4 py-4 text-right">

                            <div class="flex justify-end gap-3">

                                <a
                                    href="{{ route('orders.show', $order) }}"
                                    class="text-green-600"
                                >
                                    Show
                                </a>

                                <a
                                    href="{{ route('orders.edit', $order) }}"
                                    class="text-indigo-600"
                                >
                                    Edit
                                </a>

                                <button
                                    wire:click="deleteOrder({{ $order->id }})"
                                    wire:confirm="Are you sure?"
                                    class="text-red-600"
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
                            class="px-4 py-8 text-center text-gray-500"
                        >
                            No orders found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4">
        {{ $this->orders->links() }}
    </div>

</div>