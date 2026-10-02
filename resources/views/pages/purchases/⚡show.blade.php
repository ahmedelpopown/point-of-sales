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
<div class="px-4 py-6 sm:px-6 lg:px-8">

    <div class="mx-auto max-w-7xl space-y-6">

        {{-- ========================================================= --}}
        {{-- Header --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <div class="flex items-center gap-2">

                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
                        Purchase #{{ $purchase->id }}
                    </h1>

                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                        Purchase
                    </span>

                </div>

                <p class="mt-1 text-sm text-slate-500">
                    Complete purchase details and payment information.
                </p>

            </div>


            <div class="flex items-center gap-2">

                <a
                    href="{{ route('purchases.index') }}"
                    wire:navigate
                    class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                >
                    Back
                </a>

                <a
                    href="{{ route('purchases.edit', $purchase) }}"
                    wire:navigate
                    class="inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-700"
                >
                    Edit Purchase
                </a>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- Purchase Summary --}}
        {{-- ========================================================= --}}

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-5 py-4">

                <h2 class="text-sm font-semibold text-slate-900">
                    Purchase Information
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Basic information about this purchase.
                </p>

            </div>


            <div class="grid grid-cols-1 gap-5 px-5 py-5 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Purchase ID --}}
                <div>

                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        Purchase ID
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-900">
                        #{{ $purchase->id }}
                    </p>

                </div>


                {{-- Supplier --}}
                <div>

                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        Supplier
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-900">
                        {{ $purchase->supplier?->name ?? 'N/A' }}
                    </p>

                </div>


                {{-- Employee --}}
                <div>

                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        Employee
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-900">
                        {{ $purchase->employee?->full_name ?? 'N/A' }}
                    </p>

                </div>


                {{-- Date --}}
                <div>

                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        Date
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-900">
                        {{ $purchase->created_at?->format('Y-m-d H:i') ?? 'N/A' }}
                    </p>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- Payment --}}
        {{-- ========================================================= --}}

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-5 py-4">

                <h2 class="text-sm font-semibold text-slate-900">
                    Payment
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Manage payments related to this purchase.
                </p>

            </div>

            <div class="px-5 py-5">

                <livewire:purchase-payment-form
                    :purchase="$purchase"
                />

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- Purchase Items --}}
        {{-- ========================================================= --}}

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Header --}}
            <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="text-sm font-semibold text-slate-900">
                        Purchase Items
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ $purchase->purchaseItems->count() }} product(s) in this purchase.
                    </p>

                </div>


                <div class="rounded-xl bg-slate-50 px-3 py-2">

                    <span class="text-xs text-slate-500">
                        Total Items
                    </span>

                    <span class="ml-1 text-sm font-semibold text-slate-900">
                        {{ $purchase->purchaseItems->count() }}
                    </span>

                </div>

            </div>


            {{-- Table --}}
            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="border-b border-slate-200 bg-slate-50/70">

                        <tr>

                            <th class="w-16 px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                #
                            </th>

                            <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                Product
                            </th>

                            <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                Quantity
                            </th>

                            <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                Unit Price
                            </th>

                            <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                Total
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($purchase->purchaseItems as $index => $item)

                            <tr class="transition hover:bg-slate-50/60">

                                {{-- Number --}}
                                <td class="px-5 py-4 text-sm text-slate-400">
                                    {{ $index + 1 }}
                                </td>


                                {{-- Product --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-xs font-bold text-indigo-600">
                                            {{ strtoupper(substr($item->product?->name ?: 'P', 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-semibold text-slate-900">
                                                {{ $item->product?->name ?? 'N/A' }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                Product
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- Quantity --}}
                                <td class="px-5 py-4 text-right">

                                    <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $item->quantity }}
                                    </span>

                                </td>


                                {{-- Unit Price --}}
                                <td class="px-5 py-4 text-right text-sm text-slate-600">
                                    {{ number_format((float) $item->unit_price, 2) }}
                                </td>


                                {{-- Total --}}
                                <td class="px-5 py-4 text-right">

                                    <span class="text-sm font-semibold text-slate-900">
                                        {{ number_format((float) $item->total_price, 2) }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-5 py-12 text-center"
                                >

                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">

                                        <svg
                                            class="h-6 w-6"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6 7h12M7 7V5a2 2 0 012-2h6a2 2 0 012 2v2m-9 0v12m9-12v12M5 7h14M9 11v4m6-4v4"
                                            />
                                        </svg>

                                    </div>

                                    <p class="mt-3 text-sm font-semibold text-slate-700">
                                        No purchase items found.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Grand Total --}}
            <div class="border-t border-slate-100 px-5 py-5">

                <div class="flex justify-end">

                    <div class="w-full max-w-sm rounded-2xl bg-slate-50 p-4">

                        <div class="flex items-center justify-between">

                            <span class="text-sm font-medium text-slate-500">
                                Grand Total
                            </span>

                            <span class="text-xl font-bold text-slate-900">
                                {{ number_format((float) $purchase->total_price, 2) }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- Bottom Actions --}}
        {{-- ========================================================= --}}

        <div class="flex justify-end pt-1">

            <a
                href="{{ route('purchases.index') }}"
                wire:navigate
                class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
            >
                Back to Purchases
            </a>

        </div>

    </div>

</div>