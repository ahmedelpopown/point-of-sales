<?php

use App\Livewire\Forms\OrderForm;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Order')] class extends Component
{
    public OrderForm $form;

    public $shops = [];

    #[Computed]
    public function users()
    {
        return User::query()
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
    public function products()
    {
        return Product::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function mount(Order $order): void
    {
        $order->load([
            'orderItems.product',
            'orderItems.shop',
        ]);

        $this->form->setOrder($order);

        $shopIds = $order->orderItems
            ->pluck('shop_id')
            ->filter();

        $this->shops = Shop::query()
            ->where(function ($query) use ($shopIds) {
                $query
                    ->where('status', 'active')
                    ->orWhereIn('id', $shopIds);
            })
            ->orderBy('name')
            ->get();
    }

    public function save(): void
    {
        try {

            $this->form->update();

            session()->flash(
                'message',
                'Order updated successfully.'
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

    {{-- Header --}}
    <div class="mb-8">

        <h1 class="text-2xl font-bold text-gray-900">
            Edit Order
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Update order details.
        </p>

    </div>


    {{-- Error --}}
    @if(session()->has('error'))

        <div class="mb-6 rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-800">
                {{ session('error') }}
            </p>
        </div>

    @endif


    <form wire:submit="save">

        {{-- Shared Order Form --}}
        @include('pages.orders.order-form')


        {{-- Actions --}}
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
                Update Order
            </button>

        </div>

    </form>

</div>