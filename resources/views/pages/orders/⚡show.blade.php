<?php

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Order Details')] class extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load([
            'user',
            'employee',
            'orderItems.product',
            'installments',
            'payments',
        ]);
    }
};
?>

<div class="px-4 sm:px-6 lg:px-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-2xl font-bold text-gray-900">
                Order #{{ $order->id }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Order details.
            </p>

        </div>

        <div class="flex gap-2">

            <a
                href="{{ route('orders.edit', $order) }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-white"
            >
                Edit
            </a>

            <a
                href="{{ route('orders.index') }}"
                class="rounded-md border border-gray-300 px-4 py-2"
            >
                Back
            </a>

        </div>

    </div>

    {{-- Header --}}
    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">

            <div>

                <p class="text-sm text-gray-500">
                    Customer
                </p>

                <p class="mt-1 font-semibold">
                    {{ $order->user->name ?? 'Walk-in Customer' }}
                </p>

            </div>

            <div>

                <p class="text-sm text-gray-500">
                    Employee
                </p>

                <p class="mt-1 font-semibold">
                    {{ $order->employee->full_name ?? 'N/A' }}
                </p>

            </div>

            <div>

                <p class="text-sm text-gray-500">
                    Items
                </p>

                <p class="mt-1 font-semibold">
                    {{ $order->orderItems->count() }}
                </p>

            </div>

            <div>

                <p class="text-sm text-gray-500">
                    Date
                </p>

                <p class="mt-1 font-semibold">
                    {{ $order->created_at?->format('Y-m-d H:i') }}
                </p>

            </div>

        </div>

    </div>

    {{-- Items --}}
    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <h2 class="mb-6 text-lg font-semibold">
            Order Items
        </h2>

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-300">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-3 py-3 text-left text-sm font-semibold">
                            #
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold">
                            Product
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold">
                            Quantity
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold">
                            Price
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold">
                            Total
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @foreach($order->orderItems as $index => $item)

                        <tr>

                            <td class="px-3 py-4 text-sm">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-3 py-4 text-sm font-medium">
                                {{ $item->product->name ?? 'N/A' }}
                            </td>

                            <td class="px-3 py-4 text-sm">
                                {{ $item->quantity }}
                            </td>

                            <td class="px-3 py-4 text-sm">
                                {{ number_format($item->price, 2) }}
                            </td>

                            <td class="px-3 py-4 text-sm font-semibold">
                                {{ number_format($item->total, 2) }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <div class="mt-6 flex justify-end border-t pt-6">

            <div class="text-right">

                <p class="text-sm text-gray-500">
                    Grand Total
                </p>

                <p class="text-3xl font-bold">
                    {{ number_format($order->total, 2) }}
                </p>

            </div>

        </div>

    </div>

</div>