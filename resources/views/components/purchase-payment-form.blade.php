<div class="space-y-6">
    {{-- Flash messages --}}
    @if(session()->has('message'))
        <x-ui.alert type="success" :message="session('message')" />
    @endif

    @if(session()->has('error'))
        <x-ui.alert type="error" :message="session('error')" />
    @endif

    {{-- Purchase summary --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <x-ui.stat-card
            label="Total Purchase"
            :value="number_format((float) $purchase->total_price, 2) . ' EGP'"
        />
        <x-ui.stat-card
            label="Paid"
            :value="number_format((float) $this->paidAmount, 2) . ' EGP'"
            value-class="text-emerald-600"
        />
        <x-ui.stat-card
            label="Remaining"
            :value="number_format((float) $this->remainingAmount, 2) . ' EGP'"
            :value-class="$this->remainingAmount > 0 ? 'text-red-600' : 'text-emerald-600'"
        />
    </div>

    {{-- Supplier details --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Supplier</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $purchase->supplier->name }}</p>

                @if($purchase->supplier->paypal_email)
                    <p class="mt-1 text-sm text-slate-500">PayPal: {{ $purchase->supplier->paypal_email }}</p>
                @else
                    <p class="mt-2 text-sm text-red-600">PayPal email is not configured.</p>
                @endif
            </div>

            <div class="shrink-0">
                @if($this->remainingAmount > 0 && $purchase->supplier->paypal_email)
                    <x-ui.action-button type="button" variant="primary" wire:click="openPaymentModal">Pay Supplier</x-ui.action-button>
                @elseif($this->remainingAmount <= 0)
                    <x-ui.status-badge status="Fully Paid" :variants="['fully paid' => 'bg-emerald-50 text-emerald-700']" />
                @endif
            </div>
        </div>
    </section>

    {{-- Payment history --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">Payment History</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                        <th scope="col" class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Method</th>
                        <th scope="col" class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                        <th scope="col" class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th scope="col" class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">PayPal Fee</th>
                        <th scope="col" class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Reference</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($purchase->payments as $payment)
                        @php
                            $status = strtoupper(trim((string) ($payment->provider_status ?? 'UNKNOWN')));
                            $paymentMethod = $payment->payment_method ?? $payment->method ?? 'PayPal';
                            $successfulStatuses = ['SUCCESS', 'COMPLETED'];
                            $pendingStatuses = ['PENDING', 'PROCESSING', 'CREATED', 'APPROVED'];
                        @endphp

                        <tr wire:key="payment-row-{{ $payment->id }}" class="hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $payment->created_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-700">
                                {{ $paymentMethod }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency ?: '—' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm">
                                @if(in_array($status, $successfulStatuses, true))
                                    <x-ui.status-badge :status="$status" />
                                @elseif(in_array($status, $pendingStatuses, true))
                                    <div class="flex items-center gap-2">
                                        <x-ui.status-badge :status="$status" />
                                        <x-ui.action-button
                                            type="button"
                                            wire:click="checkPaymentStatus({{ $payment->id }})"
                                            loading-target="checkPaymentStatus({{ $payment->id }})"
                                            class="px-2.5 py-1.5 text-xs"
                                        >
                                            Check Status
                                        </x-ui.action-button>
                                    </div>
                                @else
                                    <x-ui.status-badge
                                        :status="$status"
                                        :variants="['failed' => 'bg-red-50 text-red-700', 'denied' => 'bg-red-50 text-red-700', 'cancelled' => 'bg-slate-100 text-slate-600', 'canceled' => 'bg-slate-100 text-slate-600', 'unknown' => 'bg-slate-100 text-slate-600']"
                                    />
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                @if($payment->paypal_fee !== null)
                                    {{ number_format((float) $payment->paypal_fee, 2) }} {{ $payment->paypal_currency ?: '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">
                                {{ $payment->provider_reference ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8">
                                <x-ui.empty-state title="No payments yet" description="Payments for this purchase will appear here." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Payment modal --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-950/50 px-4 py-8" role="dialog" aria-modal="true" aria-labelledby="supplier-payment-modal-title">
            <div class="my-auto w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between gap-4">
                    <h2 id="supplier-payment-modal-title" class="text-lg font-semibold text-slate-900">Pay Supplier</h2>
                    <button type="button" wire:click="closePaymentModal" aria-label="Close payment dialog" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 01-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-sm text-slate-500">Supplier</p>
                        <p class="font-medium text-slate-900">{{ $purchase->supplier->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">PayPal Email</p>
                        <p class="break-all font-medium text-slate-900">{{ $purchase->supplier->paypal_email }}</p>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Remaining Debt</p>
                        <p class="mt-1 text-xl font-semibold text-red-600">{{ number_format((float) $this->remainingAmount, 2) }} EGP</p>
                    </div>

                    <x-form.input
                        label="Amount to Pay"
                        name="amount"
                        type="number"
                        min="0.01"
                        step="0.01"
                        :max="$this->remainingAmount"
                        wire:model.live.debounce.300ms="amount"
                        placeholder="Enter amount"
                        suffix="EGP"
                        required
                    />

                    @if(is_numeric($amount) && (float) $amount > 0)
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                            <div class="flex justify-between gap-4 text-sm">
                                <span class="text-slate-600">Amount</span>
                                <span class="font-semibold text-slate-900">{{ number_format((float) $amount, 2) }} EGP</span>
                            </div>
                            <div class="mt-2 flex justify-between gap-4 text-sm">
                                <span class="text-slate-600">PayPal Amount</span>
                                <span class="font-semibold text-indigo-600">{{ number_format((float) $this->paypalAmount, 2) }} USD</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                Configured rate: 1 USD = {{ number_format((float) config('services.paypal.egp_usd_rate'), 2) }} EGP
                            </p>
                        </div>
                    @endif

                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M9 7.5h4.2a2.3 2.3 0 010 4.6H10v4.4H8.2V7.5H9Zm1.8 1.6v1.4h2.2a.7.7 0 000-1.4h-2.2Z" fill="white" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-medium text-slate-900">PayPal</p>
                                <p class="text-xs text-slate-500">Send payment to the supplier's PayPal account.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse justify-end gap-3 sm:flex-row">
                    <x-ui.action-button
                        type="button"
                        variant="secondary"
                        wire:click="closePaymentModal"
                        loading-target="payWithPaypal"
                        class="w-full sm:w-auto"
                    >
                        Cancel
                    </x-ui.action-button>

                    <x-ui.action-button
                        type="button"
                        variant="primary"
                        wire:click="payWithPaypal"
                        loading-target="payWithPaypal"
                        class="w-full sm:w-auto"
                    >
                        <span wire:loading.remove wire:target="payWithPaypal">Send Payment via PayPal</span>
                        <span wire:loading wire:target="payWithPaypal">Sending Payment...</span>
                    </x-ui.action-button>
                </div>
            </div>
        </div>
    @endif
</div>
