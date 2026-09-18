<?php

use App\Models\Purchase;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Purchase Details')] class extends Component
{
    public Purchase $purchase;

    public function mount(Purchase $purchase)
    {
        $this->purchase = $purchase->load([
            'supplier',
            'employee',
            'purchaseItems.product',
        ]);
    }
};
?>

<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-gray-900">
                Purchase #{{ $purchase->id }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Complete purchase details.
            </p>
            <livewire:purchase-payment-form
    :purchase="$purchase"
/>

        </div>

        <div class="flex gap-2">

            <a
                href="{{ route('purchases.edit', $purchase) }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                Edit
            </a>

            <a
                href="{{ route('purchases.index') }}"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700"
            >
                Back
            </a>

        </div>

    </div>

    {{-- Purchase Header --}}
    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">

            <div>

                <p class="text-sm text-gray-500">
                    Purchase ID
                </p>

                <p class="mt-1 text-lg font-semibold text-gray-900">
                    #{{ $purchase->id }}
                </p>

            </div>

            <div>

                <p class="text-sm text-gray-500">
                    Supplier
                </p>

                <p class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $purchase->supplier->name ?? 'N/A' }}
                </p>

            </div>

            <div>

                <p class="text-sm text-gray-500">
                    Employee
                </p>

                <p class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $purchase->employee->full_name ?? 'N/A' }}
                </p>

            </div>

            <div>

                <p class="text-sm text-gray-500">
                    Date
                </p>

                <p class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $purchase->created_at?->format('Y-m-d H:i') }}
                </p>

            </div>

        </div>

    </div>

    {{-- Items --}}
    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <div class="mb-6 flex items-center justify-between">

            <div>

                <h2 class="text-lg font-semibold text-gray-900">
                    Purchase Items
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $purchase->purchaseItems->count() }} product(s)
                </p>

            </div>

        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-300">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-900">
                            #
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-900">
                            Product
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-900">
                            Quantity
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-900">
                            Unit Price
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-900">
                            Total
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($purchase->purchaseItems as $index => $item)

                        <tr>

                            <td class="px-3 py-4 text-sm text-gray-500">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-3 py-4 text-sm font-medium text-gray-900">
                                {{ $item->product->name ?? 'N/A' }}
                            </td>

                            <td class="px-3 py-4 text-sm text-gray-500">
                                {{ $item->quantity }}
                            </td>

                            <td class="px-3 py-4 text-sm text-gray-500">
                                {{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="px-3 py-4 text-sm font-semibold text-gray-900">
                                {{ number_format($item->total_price, 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-3 py-8 text-center text-sm text-gray-500"
                            >
                                No items found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Total --}}
        <div class="mt-8 flex justify-end border-t pt-6">

            <div class="rounded-lg bg-gray-50 px-6 py-4 text-right">

                <p class="text-sm text-gray-500">
                    Grand Total
                </p>

                <p class="mt-1 text-3xl font-bold text-gray-900">
                    {{ number_format($purchase->total_price, 2) }}
                </p>

            </div>

        </div>

    </div>

</div>