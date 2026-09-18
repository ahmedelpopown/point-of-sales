<?php

use App\Models\Installment;
use App\Services\InstallmentService;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Installment Details')] class extends Component
{
    public Installment $installment;

    public $paymentAmount = '';

    public $paymentMethod = 'cash';

    public $paymentDate = '';

    public function mount(Installment $installment)
    {
        $this->installment = $installment->load([
            'order',
            'installmentPlan',
            'payments',
        ]);

        $this->paymentDate = now()->format('Y-m-d');
    }

    public function addPayment(
        InstallmentService $service
    ) {
        $this->validate([
            'paymentAmount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'paymentMethod' => [
                'required',
                'in:cash,card,bank_transfer',
            ],

            'paymentDate' => [
                'required',
                'date',
            ],
        ]);

        try {

            $service->addPayment(
                installment: $this->installment,
                amount: (float) $this->paymentAmount,
                method: $this->paymentMethod,
                paymentDate: $this->paymentDate,
            );

            $this->installment->refresh();

            $this->installment->load([
                'order',
                'installmentPlan',
                'payments',
            ]);

            $this->paymentAmount = '';

            session()->flash(
                'message',
                'Payment recorded successfully.'
            );

        } catch (\Throwable $e) {

            report($e);

            session()->flash(
                'error',
                $e->getMessage()
            );
        }
    }
};
?>

<div class="px-4 sm:px-6 lg:px-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-2xl font-bold text-gray-900">
                Installment #{{ $installment->id }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Order #{{ $installment->order_id }}
            </p>

        </div>

        <div class="flex gap-2">

            <a
                href="{{ route('installments.edit', $installment) }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white"
            >
                Edit
            </a>

            <a
                href="{{ route('installments.index') }}"
                class="rounded-md border border-gray-300 px-4 py-2 text-sm"
            >
                Back
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

    {{-- Summary --}}
    <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-4">

        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Original Order
            </p>

            <p class="mt-2 text-2xl font-bold">
                {{ number_format($installment->order->total ?? 0, 2) }}
            </p>

        </div>

        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Total With Interest
            </p>

            <p class="mt-2 text-2xl font-bold">
                {{ number_format($installment->total_with_interest, 2) }}
            </p>

        </div>

        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Paid
            </p>

            <p class="mt-2 text-2xl font-bold text-green-600">
                {{ number_format($installment->paid_amount + $installment->down_payment, 2) }}
            </p>

        </div>

        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Remaining
            </p>

            <p class="mt-2 text-2xl font-bold text-red-600">
                {{ number_format($installment->remaining_amount, 2) }}
            </p>

        </div>

    </div>

    {{-- Installment Details --}}
    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">

            <div>

                <p class="text-sm text-gray-500">
                    Plan
                </p>

                <p class="mt-1 font-semibold">
                    {{ $installment->installmentPlan->name }}
                </p>

            </div>

            <div>

                <p class="text-sm text-gray-500">
                    Months
                </p>

                <p class="mt-1 font-semibold">
                    {{ $installment->installmentPlan->months_count }}
                </p>

            </div>

            <div>

                <p class="text-sm text-gray-500">
                    Interest
                </p>

                <p class="mt-1 font-semibold">
                    {{ $installment->installmentPlan->interest_rate }}%
                </p>

            </div>

            <div>

                <p class="text-sm text-gray-500">
                    Monthly Amount
                </p>

                <p class="mt-1 font-semibold">
                    {{ number_format($installment->monthly_amount, 2) }}
                </p>

            </div>

        </div>

    </div>

    {{-- Add Payment --}}
    @if($installment->remaining_amount > 0)

        <div class="mt-8 rounded-lg bg-white p-6 shadow">

            <h2 class="mb-5 text-lg font-semibold">
                Add Payment
            </h2>

            <form wire:submit="addPayment">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                    <div>

                        <label class="block text-sm font-medium">
                            Amount
                        </label>

                        <input
                            wire:model="paymentAmount"
                            type="number"
                            min="0.01"
                            step="0.01"
                            max="{{ $installment->remaining_amount }}"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

                        @error('paymentAmount')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>

                        <label class="block text-sm font-medium">
                            Method
                        </label>

                        <select
                            wire:model="paymentMethod"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

                            <option value="cash">
                                Cash
                            </option>

                            <option value="card">
                                Card
                            </option>

                            <option value="bank_transfer">
                                Bank Transfer
                            </option>

                        </select>

                    </div>

                    <div>

                        <label class="block text-sm font-medium">
                            Payment Date
                        </label>

                        <input
                            wire:model="paymentDate"
                            type="date"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

                    </div>

                    <div class="flex items-end">

                        <button
                            type="submit"
                            class="w-full rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                        >
                            Add Payment
                        </button>

                    </div>

                </div>

            </form>

        </div>

    @else

        <div class="mt-8 rounded-lg bg-green-50 p-6">

            <p class="font-semibold text-green-800">
                This installment has been fully paid ✅
            </p>

        </div>

    @endif

    {{-- Payments --}}
    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <h2 class="mb-5 text-lg font-semibold">
            Payment History
        </h2>

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-300">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-3 py-3 text-left text-sm font-semibold">
                            #
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold">
                            Amount
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold">
                            Method
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold">
                            Date
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($installment->payments as $index => $payment)

                        <tr>

                            <td class="px-3 py-4 text-sm">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-3 py-4 text-sm font-semibold">
                                {{ number_format($payment->amount, 2) }}
                            </td>

                            <td class="px-3 py-4 text-sm">
                                {{ ucfirst(str_replace('_', ' ', $payment->method)) }}
                            </td>

                            <td class="px-3 py-4 text-sm">
                                {{ $payment->payment_date?->format('Y-m-d') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="px-3 py-8 text-center text-sm text-gray-500"
                            >
                                No payments yet.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>