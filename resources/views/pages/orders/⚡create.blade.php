<?php

use App\Livewire\Forms\OrderForm;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Order')] class extends Component
{
    public OrderForm $form;

    public $users = [];

    public $employees = [];

    public $shops = [];

    public $products = [];

    public function mount()
    {
        $this->users = User::query()
            ->orderBy('name')
            ->get();

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

        $this->form->addItem();
    }

    public function save()
    {
        try {

            $this->form->store();

            session()->flash(
                'message',
                'Order created successfully.'
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

                {{-- Header --}}
                <div class="rounded-lg bg-white p-6 shadow">

                    <h2 class="mb-5 text-lg font-semibold">
                        Order Information
                    </h2>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        {{-- User --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700">
                                Customer
                            </label>

                            <select
                                wire:model="form.user_id"
                                class="mt-1 block w-full rounded-lg border-gray-300"
                            >

                                <option value="">
                                    Walk-in Customer
                                </option>

                                @foreach($users as $user)

                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('form.user_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        {{-- Employee --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700">
                                Employee
                            </label>

                            <select
                                wire:model="form.employee_id"
                                class="mt-1 block w-full rounded-lg border-gray-300"
                            >

                                <option value="">
                                    Select Employee
                                </option>

                                @foreach($employees as $employee)

                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('form.employee_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                </div>

                {{-- Items --}}
                <div class="rounded-lg bg-white p-6 shadow">

                    <div class="mb-6 flex items-center justify-between">

                        <h2 class="text-lg font-semibold">
                            Order Items
                        </h2>

                        <button
                            type="button"
                            wire:click="form.addItem"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white"
                        >
                            + Add Item
                        </button>

                    </div>

                    <div class="space-y-4">

                        @foreach($form->items as $index => $item)

                            <div
                                wire:key="order-item-{{ $index }}"
                                class="rounded-lg border p-4"
                            >

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-12">

                                    {{-- Product --}}
                                    <div class="md:col-span-4">

                                        <label class="block text-sm font-medium">
                                            Product
                                        </label>

                                        <select
                                            wire:model="form.items.{{ $index }}.product_id"
                                            class="mt-1 block w-full rounded-lg border-gray-300"
                                        >

                                            <option value="">
                                                Select Product
                                            </option>

                                            @foreach($products as $product)

                                                <option value="{{ $product->id }}">
                                                    {{ $product->name }}
                                                </option>

                                            @endforeach

                                        </select>

                                        @error("form.items.$index.product_id")
                                            <p class="mt-1 text-sm text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                    {{-- Shop --}}
                                    <div class="md:col-span-3">

                                        <label class="block text-sm font-medium">
                                            Shop
                                        </label>

                                        <select
                                            wire:model="form.items.{{ $index }}.shop_id"
                                            class="mt-1 block w-full rounded-lg border-gray-300"
                                        >

                                            <option value="">
                                                Select Shop
                                            </option>

                                            @foreach($shops as $shop)

                                                <option value="{{ $shop->id }}">
                                                    {{ $shop->name }}
                                                </option>

                                            @endforeach

                                        </select>

                                        @error("form.items.$index.shop_id")
                                            <p class="mt-1 text-sm text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                    {{-- Quantity --}}
                                    <div class="md:col-span-1">

                                        <label class="block text-sm font-medium">
                                            Quantity
                                        </label>

                                        <input
                                            wire:model="form.items.{{ $index }}.quantity"
                                            type="number"
                                            min="1"
                                            class="mt-1 block w-full rounded-lg border-gray-300"
                                        >

                                        @error("form.items.$index.quantity")
                                            <p class="mt-1 text-sm text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                    {{-- Price --}}
                                    <div class="md:col-span-2">

                                        <label class="block text-sm font-medium">
                                            Price
                                        </label>

                                        <input
                                            wire:model="form.items.{{ $index }}.price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="mt-1 block w-full rounded-lg border-gray-300"
                                        >

                                        @error("form.items.$index.price")
                                            <p class="mt-1 text-sm text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                    {{-- Total --}}
                                    <div class="md:col-span-2">

                                        <label class="block text-sm font-medium">
                                            Total
                                        </label>

                                        <div class="mt-1 flex h-10 items-center rounded-lg bg-gray-50 px-3 font-semibold">
                                            {{ number_format($this->form->itemTotal($index), 2) }}
                                        </div>

                                    </div>

                                    {{-- Remove --}}
                                    <div class="md:col-span-1 flex items-end">

                                        @if(count($form->items) > 1)

                                            <button
                                                type="button"
                                                wire:click="form.removeItem({{ $index }})"
                                                class="w-full rounded-lg bg-red-100 px-2 py-2 text-sm text-red-700"
                                            >
                                                Remove
                                            </button>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                    {{-- Grand Total --}}
                    <div class="mt-8 flex justify-end border-t pt-6">

                        <div class="text-right">

                            <p class="text-sm text-gray-500">
                                Grand Total
                            </p>

                            <p class="text-3xl font-bold text-gray-900">
                                {{ number_format($this->form->total(), 2) }}
                            </p>

                        </div>

                    </div>

                </div>

                <div class="flex justify-end gap-3">

                    <a
                        href="{{ route('orders.index') }}"
                        class="rounded-md border border-gray-300 px-4 py-2"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-md bg-indigo-600 px-5 py-2 text-white"
                    >
                        Create Order
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>