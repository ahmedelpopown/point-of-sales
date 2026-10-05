<?php

use App\Livewire\Forms\OrderForm;
use App\Models\Employee;
use App\Models\InstallmentPlan;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\InstallmentService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Order')] class extends Component
{
    public OrderForm $form;

    public string $payment_method = '';

    public string $installment_plan_id = '';

    public float|int $down_payment = 0;

    public string $start_date = '';

    #[Computed]
    public function users()
    {
        return User::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function employees()
    {
        return Employee::query()
            ->orderBy('first_name')
            ->get();
    }

    #[Computed]
    public function shops()
    {
        return Shop::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function plans()
    {
        return InstallmentPlan::query()
            ->orderBy('months_count')
            ->get();
    }

    public function mount(): void
    {
        $this->start_date = now()->format('Y-m-d');

        if (auth('employee')->check()) {
            $this->form->employee_id = auth('employee')->id();
        }

        $this->form->addItem();
    }

    public function updatedPaymentMethod($value): void
    {
        if ($value === 'installment') {
            return;
        }

        $this->installment_plan_id = '';
        $this->down_payment = 0;
        $this->start_date = now()->format('Y-m-d');
    }

    protected function isInstallment(): bool
    {
        return $this->payment_method === 'installment';
    }

    public function save(InstallmentService $service): void
    {
        try {

            if ($this->isInstallment()) {
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

                if (! $this->isInstallment()) {
                    return;
                }

                $plan = InstallmentPlan::findOrFail(
                    $this->installment_plan_id
                );

                $service->create(
                    order: $order,
                    plan: $plan,
                    downPayment: (float) $this->down_payment,
                    startDate: $this->start_date,
                );
            });

            session()->flash(
                'message',
                $this->isInstallment()
                    ? 'Order and installment created successfully.'
                    : 'Order created successfully.'
            );

            $this->redirect(
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

    <div class="mb-8">

        <h1 class="text-2xl font-bold text-gray-900">
            Create Order
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Create an order with multiple products.
        </p>

    </div>


    @if(session()->has('error'))

        <div class="mb-6 rounded-md bg-red-50 p-4">

            <p class="text-sm text-red-800">
                {{ session('error') }}
            </p>

        </div>

    @endif


    <form wire:submit="save">

        @include('pages.orders.order-form')

        @include('pages.orders.payment')

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

            <a
                href="{{ route('orders.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
            >
                Create Order
            </button>

        </div>

    </form>

</div>