{{-- Payment Method --}}
<div class="mt-6 rounded-2xl border border-gray-200 bg-white shadow-sm">

    <div class="border-b border-gray-100 px-6 py-5">

        <h2 class="text-lg font-semibold text-gray-900">
            Payment
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Select the payment method for this order.
        </p>

    </div>

    <div class="p-6">

        <x-form.select-input
            label="Payment Method"
            name="payment_method"
            :options="[
                'cash' => 'Cash',
                'installment' => 'Installment',
            ]"
            placeholder="Select payment method"
            required
            wire:model.live="payment_method"
        />

    </div>

</div>


{{-- Installment --}}
@if($payment_method === 'installment')

    <div class="mt-6 overflow-visible rounded-2xl border border-amber-200 bg-white shadow-sm">

        <div class="border-b border-amber-100 bg-amber-50 px-6 py-5">

            <h2 class="text-lg font-semibold text-gray-900">
                Installment Details
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Configure the installment plan for this order.
            </p>

        </div>


        <div class="space-y-6 p-6">

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                {{-- Installment Plan --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700">
                        Installment Plan
                    </label>

                    <select
                        wire:model.live="installment_plan_id"
                        class="mt-1 block w-full rounded-xl border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                        <option value="">
                            Select Plan
                        </option>

                        @foreach($this->plans as $plan)

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


                {{-- Down Payment --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700">
                        Down Payment
                    </label>

                    <input
                        wire:model.live="down_payment"
                        type="number"
                        min="0"
                        step="0.01"
                        class="mt-1 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                    @error('down_payment')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Start Date --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700">
                        Start Date
                    </label>

                    <input
                        wire:model="start_date"
                        type="date"
                        class="mt-1 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                    @error('start_date')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>


            {{-- Preview --}}
            @if($installment_plan_id)

                @php
                    $plan = $this->plans->firstWhere(
                        'id',
                        (int) $installment_plan_id
                    );

                    $orderTotal = (float) $this->form->total();

                    $interestAmount = $plan
                        ? $orderTotal * ((float) $plan->interest_rate / 100)
                        : 0;

                    $totalWithInterest = $orderTotal + $interestAmount;

                    $remainingAmount = max(
                        0,
                        $totalWithInterest - (float) $down_payment
                    );

                    $monthlyAmount =
                        $plan && $plan->months_count > 0
                            ? $remainingAmount / $plan->months_count
                            : 0;
                @endphp


                @if($plan)

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">

                        <div class="mb-4">

                            <h3 class="text-sm font-semibold text-gray-900">
                                Installment Preview
                            </h3>

                            <p class="text-xs text-gray-500">
                                Calculated based on the current order total.
                            </p>

                        </div>


                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Order Total
                                </p>

                                <p class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ number_format($orderTotal, 2) }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Interest
                                </p>

                                <p class="mt-1 text-lg font-semibold text-amber-600">
                                    {{ number_format($interestAmount, 2) }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Total With Interest
                                </p>

                                <p class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ number_format($totalWithInterest, 2) }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Remaining
                                </p>

                                <p class="mt-1 text-lg font-semibold text-red-600">
                                    {{ number_format($remainingAmount, 2) }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Monthly Payment
                                </p>

                                <p class="mt-1 text-lg font-bold text-indigo-600">
                                    {{ number_format($monthlyAmount, 2) }}
                                </p>

                            </div>

                        </div>

                    </div>

                @endif

            @endif

        </div>

    </div>

@endif
