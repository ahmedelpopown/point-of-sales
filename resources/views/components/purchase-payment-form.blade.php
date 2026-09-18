<div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif


    {{-- Purchase Summary --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        {{-- Total --}}
        <div class="rounded-lg border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">
                Total Purchase
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($purchase->total_price, 2) }}
                EGP
            </p>
        </div>


        {{-- Paid --}}
        <div class="rounded-lg border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">
                Paid
            </p>

            <p class="mt-2 text-2xl font-semibold text-green-600">
                {{ number_format($this->paidAmount, 2) }}
                EGP
            </p>
        </div>


        {{-- Remaining --}}
        <div class="rounded-lg border bg-white p-5 shadow-sm">

            <p class="text-sm text-gray-500">
                Remaining
            </p>

            <p class="mt-2 text-2xl font-semibold
                {{ $this->remainingAmount > 0
                    ? 'text-red-600'
                    : 'text-green-600' }}">
                {{ number_format($this->remainingAmount, 2) }}
                EGP
            </p>

        </div>

    </div>


    {{-- Supplier --}}
    <div class="mt-6 rounded-lg border bg-white p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    Supplier
                </h3>

                <p class="mt-1 text-sm text-gray-600">
                    {{ $purchase->supplier->name }}
                </p>

                @if ($purchase->supplier->paypal_email)

                    <p class="mt-1 text-sm text-gray-500">
                        PayPal:
                        {{ $purchase->supplier->paypal_email }}
                    </p>

                @else

                    <p class="mt-2 text-sm text-red-600">
                        PayPal email is not configured.
                    </p>

                @endif

            </div>


            <div>

                @if ($this->remainingAmount > 0 && $purchase->supplier->paypal_email)

                    <button
                        type="button"
                        wire:click="openPaymentModal"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm
                               font-medium text-white hover:bg-indigo-700"
                    >
                        Pay Supplier
                    </button>

                @elseif ($this->remainingAmount <= 0)

                    <span
                        class="rounded-full bg-green-100 px-3 py-1
                               text-sm font-medium text-green-700"
                    >
                        Fully Paid
                    </span>

                @endif

            </div>

        </div>

    </div>


    {{-- Payments --}}
    <div class="mt-6 overflow-hidden rounded-lg border bg-white shadow-sm">

        <div class="border-b px-5 py-4">
            <h3 class="text-lg font-semibold text-gray-900">
                Payment History
            </h3>
        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase text-gray-500">
                            Date
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase text-gray-500">
                            Method
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase text-gray-500">
                            Amount
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
           uppercase text-gray-500">
    PayPal Fee
</th>

                        
                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase text-gray-500">
                            Reference
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200 bg-white">

                    @forelse ($purchase->payments as $payment)

                        <tr>

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                {{ $payment->created_at?->format('Y-m-d H:i') }}
                            </td>
<td class="whitespace-nowrap px-6 py-4 text-sm">

    @php
        $status = strtoupper(
            $payment->provider_status ?? 'SUCCESS'
        );
    @endphp

    @if ($status === 'SUCCESS')

        <span class="rounded-full bg-green-100 px-2.5 py-1
                     text-xs font-medium text-green-700">
            Success
        </span>

    @elseif (
        in_array($status, ['PENDING', 'PROCESSING'])
    )

        <span class="rounded-full bg-yellow-100 px-2.5 py-1
                     text-xs font-medium text-yellow-700">
            {{ ucfirst(strtolower($status)) }}
        </span>

        <button
            type="button"
            wire:click="checkPaymentStatus({{ $payment->id }})"
            wire:loading.attr="disabled"
            wire:target="checkPaymentStatus({{ $payment->id }})"
            class="ml-2 text-xs font-medium text-indigo-600
                   hover:text-indigo-800"
        >
            Check Status
        </button>

    @else

        <span class="rounded-full bg-red-100 px-2.5 py-1
                     text-xs font-medium text-red-700">
            {{ ucfirst(strtolower($status)) }}
        </span>

    @endif

</td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                {{ number_format($payment->amount, 2) }}
                                {{ $payment->currency }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm">

                                @php
                                    $status = strtoupper(
                                        $payment->provider_status
                                        ?? 'SUCCESS'
                                    );
                                @endphp

                                @if ($status === 'SUCCESS')

                                    <span class="rounded-full bg-green-100 px-2.5 py-1
                                                 text-xs font-medium text-green-700">
                                        Success
                                    </span>

                                @elseif (
                                    in_array(
                                        $status,
                                        ['PENDING', 'PROCESSING']
                                    )
                                )

                                    <span class="rounded-full bg-yellow-100 px-2.5 py-1
                                                 text-xs font-medium text-yellow-700">
                                        {{ ucfirst(strtolower($status)) }}
                                    </span>

                                @else

                                    <span class="rounded-full bg-red-100 px-2.5 py-1
                                                 text-xs font-medium text-red-700">
                                        {{ ucfirst(strtolower($status)) }}
                                    </span>

                                @endif

                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">

    @if ($payment->paypal_fee !== null)

        {{ number_format(
            (float) $payment->paypal_fee,
            2
        ) }}

        {{ $payment->paypal_currency }}

    @else

        -

    @endif

</td>

                            <td class="whitespace-nowrap px-6 py-4 text-xs text-gray-500">
                                {{ $payment->provider_reference ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-8 text-center text-sm text-gray-500"
                            >
                                No payments yet.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Payment Modal --}}
    @if ($showPaymentModal)

        <div
            class="fixed inset-0 z-50 flex items-center justify-center
                   bg-black/50 px-4"
        >

            <div
                class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
            >

                <div class="flex items-center justify-between">

                    <h2 class="text-lg font-semibold text-gray-900">
                        Pay Supplier
                    </h2>

                    <button
                        type="button"
                        wire:click="closePaymentModal"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        ✕
                    </button>

                </div>


                <div class="mt-5 space-y-4">

                    <div>
                        <p class="text-sm text-gray-500">
                            Supplier
                        </p>

                        <p class="font-medium text-gray-900">
                            {{ $purchase->supplier->name }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            PayPal Email
                        </p>

                        <p class="font-medium text-gray-900">
                            {{ $purchase->supplier->paypal_email }}
                        </p>
                    </div>


                    <div class="rounded-lg bg-gray-50 p-4">

                        <p class="text-sm text-gray-500">
                            Remaining Debt
                        </p>

                        <p class="mt-1 text-xl font-semibold text-red-600">
                            {{ number_format($this->remainingAmount, 2) }}
                            EGP
                        </p>

                    </div>
<div>

    <label
        for="amount"
        class="block text-sm font-medium text-gray-700"
    >
        Amount to Pay
    </label>

    <div class="relative mt-1">

        <input
            id="amount"
            type="number"
            step="0.01"
            min="0.01"
            max="{{ $this->remainingAmount }}"
            wire:model.live="amount"
            class="block w-full rounded-lg border-gray-300 pr-16
                   shadow-sm focus:border-indigo-500
                   focus:ring-indigo-500"
            placeholder="Enter amount"
        >

        <span
            class="absolute inset-y-0 right-3 flex
                   items-center text-sm text-gray-500"
        >
            EGP
        </span>

    </div>

    @error('amount')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror

</div>


@if((float) $amount > 0)

    <div class="rounded-lg border border-indigo-100
                bg-indigo-50 p-4">

        <div class="flex justify-between text-sm">

            <span class="text-gray-600">
                Amount
            </span>

            <span class="font-semibold text-gray-900">
                {{ number_format((float) $amount, 2) }}
                EGP
            </span>

        </div>

        <div class="mt-2 flex justify-between text-sm">

            <span class="text-gray-600">
                PayPal Amount
            </span>

            <span class="font-semibold text-indigo-600">
                {{ number_format($this->paypalAmount, 2) }}
                USD
            </span>

        </div>

        <div class="mt-2 text-xs text-gray-500">
            Sandbox rate:
            1 USD =
            {{ number_format(
                (float) config('services.paypal.egp_usd_rate'),
                2
            ) }}
            EGP
        </div>

    </div>

@endif


                    <div class="rounded-lg border p-3">

                        <div class="flex items-center gap-3">

                            <input
                                type="radio"
                                checked
                                disabled
                                class="h-4 w-4"
                            >

                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    PayPal
                                </p>

                                <p class="text-xs text-gray-500">
                                    Send payment to supplier's PayPal account
                                </p>
                            </div>

                        </div>

                    </div>

                </div>


                <div class="mt-6 flex justify-end gap-3">

                    <button
                        type="button"
                        wire:click="closePaymentModal"
                        wire:loading.attr="disabled"
                        class="rounded-lg border border-gray-300 bg-white
                               px-4 py-2 text-sm font-medium text-gray-700
                               hover:bg-gray-50"
                    >
                        Cancel
                    </button>

 <button
    type="button"
    wire:click="payWithPaypal"
    wire:loading.attr="disabled"
    wire:target="payWithPaypal"
    class="rounded-lg bg-indigo-600 px-4 py-2
           text-sm font-medium text-white
           hover:bg-indigo-700
           disabled:opacity-50"
>
    <span
        wire:loading.remove
        wire:target="payWithPaypal"
    >
        Send Payment via PayPal
    </span>

    <span
        wire:loading
        wire:target="payWithPaypal"
    >
        Sending Payment...
    </span>
</button>


                </div>

            </div>

        </div>

    @endif

</div>