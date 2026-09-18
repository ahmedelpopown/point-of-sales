<?php

use App\Models\InstallmentPlan;
use App\Models\Order;
use App\Services\InstallmentService;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Installment')] class extends Component
{
    public $order_id = '';

    public $installment_plan_id = '';

    public $down_payment = 0;

    public $start_date;

    public $orders = [];

    public $plans = [];

    public function mount()
    {
        $this->orders = Order::query()
            ->orderByDesc('id')
            ->get();

        $this->plans = InstallmentPlan::query()
            ->orderBy('months_count')
            ->get();

        $this->start_date = now()->format('Y-m-d');
    }

    public function save(
        InstallmentService $service
    ) {
        $this->validate([
            'order_id' => [
                'required',
                'exists:orders,id',
            ],

            'installment_plan_id' => [
                'required',
                'exists:installment_plans,id',
            ],

            'down_payment' => [
                'required',
                'numeric',
                'min:0',
            ],

            'start_date' => [
                'required',
                'date',
            ],
        ]);

        try {

            $order = Order::findOrFail($this->order_id);

            $plan = InstallmentPlan::findOrFail(
                $this->installment_plan_id
            );

            $service->create(
                order: $order,
                plan: $plan,
                downPayment: (float) $this->down_payment,
                startDate: $this->start_date,
            );

            session()->flash(
                'message',
                'Installment created successfully.'
            );

            return $this->redirect(
                route('installments.index')
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

    <h1 class="text-2xl font-bold text-gray-900">
        Create Installment
    </h1>

    @if(session()->has('error'))

        <div class="mt-4 rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-800">
                {{ session('error') }}
            </p>
        </div>

    @endif

    <div class="mt-8 max-w-3xl">

        <form wire:submit="save">

            <div class="space-y-6">

                <div class="rounded-lg bg-white p-6 shadow">

                    <div>

                        <label class="block text-sm font-medium text-gray-700">
                            Order
                        </label>

                        <select
                            wire:model="order_id"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

                            <option value="">
                                Select Order
                            </option>

                            @foreach($orders as $order)

                                <option value="{{ $order->id }}">
                                    Order #{{ $order->id }}
                                    - {{ number_format($order->total, 2) }}
                                </option>

                            @endforeach

                        </select>

                        @error('order_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div class="mt-6">

                        <label class="block text-sm font-medium text-gray-700">
                            Installment Plan
                        </label>

                        <select
                            wire:model="installment_plan_id"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

                            <option value="">
                                Select Plan
                            </option>

                            @foreach($plans as $plan)

                                <option value="{{ $plan->id }}">
                                    {{ $plan->name }}
                                    -
                                    {{ $plan->months_count }} Months
                                    -
                                    {{ $plan->interest_rate }}%
                                </option>

                            @endforeach

                        </select>

                        @error('installment_plan_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div class="mt-6">

                        <label class="block text-sm font-medium text-gray-700">
                            Down Payment
                        </label>

                        <input
                            wire:model="down_payment"
                            type="number"
                            min="0"
                            step="0.01"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

                        @error('down_payment')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div class="mt-6">

                        <label class="block text-sm font-medium text-gray-700">
                            Start Date
                        </label>

                        <input
                            wire:model="start_date"
                            type="date"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

                    </div>

                </div>

                <div class="flex justify-end gap-3">

                    <a
                        href="{{ route('installments.index') }}"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white"
                    >
                        Create Installment
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>