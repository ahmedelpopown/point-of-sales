<?php

use App\Livewire\Forms\OrderForm;
use App\Models\Employee;
use App\Models\InstallmentPlan;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\InstallmentService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Order')] class extends Component
{
    public OrderForm $form;

    public $users = [];

    public $employees = [];

    public $shops = [];

    public $products = [];
    public $plans = [];

public $payment_method = 'cash';

public $installment_plan_id = '';

public $down_payment = 0;

public $start_date;
public function mount()
{
    $this->users = User::query()
        ->select('id', 'name')
        ->orderBy('name')
        ->get()
        ->toArray();

    $this->employees = Employee::query()
        ->orderBy('first_name')
        ->get();

    $this->shops = Shop::query()
        ->where('status', 'active')
        ->orderBy('name')
        ->get();

    $this->products = Product::query()
        ->where('status', 'active')
        ->orderBy('name')
        ->get();

    $this->plans = InstallmentPlan::query()
        ->orderBy('months_count')
        ->get();

    $this->start_date = now()->format('Y-m-d');

    if (auth('employee')->check()) {
        $this->form->employee_id = auth('employee')->id();
    }

    $this->form->addItem();
}

    public function addItem(): void
{
    $this->form->addItem();
}

public function removeItem(int $index): void
{
    $this->form->removeItem($index);
}
public function updatedPaymentMethod($value): void
{
    if ($value !== 'installment') {
        $this->installment_plan_id = '';
        $this->down_payment = 0;
        $this->start_date = now()->format('Y-m-d');
    }
}
public function save(InstallmentService $service)
{
    try {

        if ($this->payment_method === 'installment') {
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
        }

        DB::transaction(function () use ($service) {

            $order = $this->form->store();

            if ($this->payment_method === 'installment') {

                $plan = InstallmentPlan::findOrFail(
                    $this->installment_plan_id
                );

                $service->create(
                    order: $order,
                    plan: $plan,
                    downPayment: (float) $this->down_payment,
                    startDate: $this->start_date,
                );
            }
        });

        session()->flash(
            'message',
            $this->payment_method === 'installment'
                ? 'Order and installment created successfully.'
                : 'Order created successfully.'
        );

        return $this->redirect(
            route('orders.index')
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

    <div>

        <h1 class="text-2xl font-bold text-gray-900">
            Create Order
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Create an order with multiple products.
        </p>

    </div>

    @if(session()->has('error'))

    <div class="mt-4 rounded-md bg-red-50 p-4">
        <p class="text-sm text-red-800">
            {{ session('error') }}
        </p>
    </div>

    @endif

    <div class="mt-8 max-w-7xl">

        <form wire:submit="save">

        <div class="space-y-6">

    {{-- Order Information --}}
   <div class="overflow-visible rounded-2xl border border-gray-200 bg-white shadow-sm">

        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-lg font-semibold text-gray-900">
                Order Information
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Select the customer and employee responsible for this order.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

            {{-- Customer --}}
            
            <x-form.searchable-select
    label="Customer"
    model="form.user_id"
    :options="$this->users"
    option-value="id"
    option-label="name"
    placeholder="Select Customer"
/>

            {{-- Employee --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Employee
                </label>

                @if(auth()->user()->hasRole('admin'))

                    <select
                        wire:model="form.employee_id"
                        class="mt-1 block w-full rounded-xl border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Select Employee</option>

                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->full_name }}
                            </option>
                        @endforeach
                    </select>

                @else

                    <div class="mt-1 flex min-h-[42px] items-center rounded-xl border border-gray-200 bg-gray-50 px-4">
                        <div>
                            <p class="text-xs text-gray-500">
                                Logged in as
                            </p>

                            <p class="text-sm font-semibold text-gray-900">
                                {{ auth('employee')->user()->first_name }}
                            </p>
                        </div>
                    </div>

                @endif

                @error('form.employee_id')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>


    {{-- Order Items --}}
    <div class="overflow-visible rounded-2xl border border-gray-200 bg-white shadow-sm">

        {{-- Header --}}
        <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Order Items
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Add products and set the required quantity.
                </p>
            </div>
<button
    type="button"
    wire:click="addItem"
    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
>
    <svg
        class="mr-2 h-4 w-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4v16m8-8H4"
        />
    </svg>

    Add Item
</button>
        </div>


        {{-- Items --}}
        <div class="space-y-4 p-6">

            @foreach($form->items as $index => $item)

                <div
                    wire:key="order-item-{{ $index }}"
                    class="rounded-2xl border border-gray-200 bg-gray-50 p-5"
                >

                    {{-- Item Header --}}
                    <div class="mb-5 flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-sm font-bold text-indigo-700">
                                {{ $index + 1 }}
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">
                                    Order Item
                                </h3>

                                <p class="text-xs text-gray-500">
                                    Product details
                                </p>
                            </div>

                        </div>

                        @if(count($form->items) > 1)

                            <button
                                type="button"
                                wire:click="removeItem({{ $index }})"
                                class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700"
                            >
                                <svg
                                    class="mr-1.5 h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h10"
                                    />
                                </svg>

                                Remove
                            </button>

                        @endif

                    </div>


                    {{-- Product + Shop --}}
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                        {{-- Product --}}
                        <div>
                            <x-form.searchable-select
                                label="Product"
                                model="form.items.{{ $index }}.product_id"
                                :options="$this->products"
                                option-value="id"
                                option-label="name"
                                placeholder="Select Product"
                            />
                        </div>


                        {{-- Shop --}}
                        <div>

                            @if(auth()->user()->hasRole('admin'))

                                <x-form.searchable-select
                                    label="Shop"
                                    model="form.items.{{ $index }}.shop_id"
                                    :options="$this->shops"
                                    option-value="id"
                                    option-label="name"
                                    placeholder="Select Shop"
                                />

                            @else

                                <label class="block text-sm font-medium text-gray-700">
                                    Shop
                                </label>

                                <div class="mt-1 flex min-h-[42px] items-center rounded-xl border border-gray-200 bg-white px-4 shadow-sm">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100">
                                            <svg
                                                class="h-4 w-4 text-gray-600"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"
                                                />
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ auth('employee')->user()->shop?->name ?? 'No Shop Assigned' }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                Assigned to your account
                                            </p>
                                        </div>

                                    </div>

                                </div>

                            @endif

                            @error("form.items.$index.shop_id")
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>


                    {{-- Quantity + Price + Total --}}
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">

                        {{-- Quantity --}}
                        <div>

                            <x-form.input
                                label="Quantity"
                                name="items.{{ $index }}.quantity"
                                type="number"
                                min="1"
                                wire:model.live="form.items.{{ $index }}.quantity"
                            />

                            @error("form.items.$index.quantity")
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Unit Price --}}
                        <div>

                            <x-form.input
                                label="Unit Price"
                                name="items.{{ $index }}.price"
                                type="number"
                                wire:model="form.items.{{ $index }}.price"
                                readonly
                                step="0.01"
                                class="cursor-not-allowed bg-gray-100 text-gray-700"
                                hint="Based on latest purchase price"
                            />

                            @error("form.items.$index.price")
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Item Total --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700">
                                Item Total
                            </label>

                            <div class="mt-1 flex min-h-[42px] items-center justify-between rounded-xl border border-gray-200 bg-white px-4 shadow-sm">

                                <span class="text-xs text-gray-500">
                                    Total
                                </span>

                                <span class="text-base font-bold text-gray-900">
                                    {{ number_format($this->form->itemTotal($index), 2) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        {{-- Grand Total --}}
        <div class="border-t border-gray-100 bg-gray-50 px-6 py-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Grand Total
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Total amount for all order items
                    </p>
                </div>

                <div class="text-right">

                    <p class="text-3xl font-bold tracking-tight text-gray-900">
                        {{ number_format($this->form->total(), 2) }}
                    </p>

                    <p class="text-xs text-gray-500">
                        EGP
                    </p>

                </div>

            </div>

            <div>
    <label class="block text-sm font-medium text-gray-700">
        Payment Method
    </label>

    <select
        wire:model.live="payment_method"
        class="mt-1 block w-full rounded-xl border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    >
        <option value="cash">
            Cash
        </option>

        <option value="installment">
            Installment
        </option>
    </select>

    @error('payment_method')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>
@if($payment_method === 'installment')

    <div class="overflow-visible rounded-2xl border border-amber-200 bg-white shadow-sm">

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
                    $plan = $plans->firstWhere(
                        'id',
                        (int) $installment_plan_id
                    );

                    $orderTotal = (float) $this->form->total();

                    $interestAmount = $plan
                        ? $orderTotal * ((float) $plan->interest_rate / 100)
                        : 0;

                    $totalWithInterest =
                        $orderTotal + $interestAmount;

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

        </div>

    </div>


    {{-- Actions --}}
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

        <a
            href="{{ route('orders.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            Create Order
        </button>

    </div>

</div>

        </form>

    </div>

</div>