<?php

use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Services\InstallmentService;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Installment')] class extends Component
{
    public Installment $installment;

    public $installment_plan_id;

    public $down_payment;

    public $start_date;

    public $plans = [];

    public function mount(Installment $installment)
    {
        $this->installment = $installment->load([
            'installmentPlan',
            'order',
            'payments',
        ]);

        $this->installment_plan_id =
            $installment->installment_plan_id;

        $this->down_payment =
            $installment->down_payment;

        $this->start_date =
            $installment->start_date?->format('Y-m-d');

        $this->plans = InstallmentPlan::query()
            ->orderBy('months_count')
            ->get();
    }

    public function save(
        InstallmentService $service
    ) {
        $this->validate([
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

            $plan = InstallmentPlan::findOrFail(
                $this->installment_plan_id
            );

            $service->update(
                installment: $this->installment,
                plan: $plan,
                downPayment: (float) $this->down_payment,
                startDate: $this->start_date,
            );

            session()->flash(
                'message',
                'Installment updated successfully.'
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
        Edit Installment #{{ $installment->id }}
    </h1>

    @if(session()->has('error'))

        <div class="mt-4 rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-800">
                {{ session('error') }}
            </p>
        </div>

    @endif

    @if($installment->payments->count() > 0)

        <div class="mt-4 rounded-md bg-yellow-50 p-4">

            <p class="text-sm text-yellow-800">
                This installment already has payments and cannot be edited.
            </p>

        </div>

    @else

        <div class="mt-8 max-w-3xl">

            <form wire:submit="save">

                <div class="rounded-lg bg-white p-6 shadow">

                    <div>

                        <label class="block text-sm font-medium">
                            Installment Plan
                        </label>

                        <select
                            wire:model="installment_plan_id"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

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

                    </div>

                    <div class="mt-6">

                        <label class="block text-sm font-medium">
                            Down Payment
                        </label>

                        <input
                            wire:model="down_payment"
                            type="number"
                            min="0"
                            step="0.01"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

                    </div>

                    <div class="mt-6">

                        <label class="block text-sm font-medium">
                            Start Date
                        </label>

                        <input
                            wire:model="start_date"
                            type="date"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                        >

                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-3">

                    <a
                        href="{{ route('installments.index') }}"
                        class="rounded-md border border-gray-300 px-4 py-2"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-white"
                    >
                        Update Installment
                    </button>

                </div>

            </form>

        </div>

    @endif

</div>