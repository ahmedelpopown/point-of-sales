<?php

use App\Models\Employee;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Purchase')] class extends Component
{
    public $supplier_id = '';

    public $employee_id = '';

    public $suppliers = [];

    public $employees = [];

    public $products = [];

    public array $items = [];

    public function mount()
    {
        $this->suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        $this->employees = Employee::query()
            ->orderBy('first_name')
            ->get();

        $this->products = Product::query()
            ->orderBy('name')
            ->get();

        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) <= 1) {
            return;
        }

        unset($this->items[$index]);

        $this->items = array_values($this->items);
    }

    public function itemTotal($index)
    {
        $item = $this->items[$index];

        return
            ((float) ($item['quantity'] ?? 0))
            *
            ((float) ($item['unit_price'] ?? 0));
    }

    public function totalPrice()
    {
        return collect($this->items)->sum(function ($item) {

            return
                ((float) ($item['quantity'] ?? 0))
                *
                ((float) ($item['unit_price'] ?? 0));
        });
    }

    public function save(PurchaseService $purchaseService)
    {
        $this->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id',
                'distinct',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        try {

            $purchaseService->create(
                supplierId: (int) $this->supplier_id,
                employeeId: (int) $this->employee_id,
                items: $this->items,
            );

            session()->flash(
                'message',
                'Purchase created successfully.'
            );

            return $this->redirect(
                route('purchases.index')
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

        <h2 class="text-2xl font-bold text-gray-900">
            Create Purchase
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Create one purchase with multiple products.
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

                    <h3 class="mb-5 text-lg font-semibold text-gray-900">
                        Purchase Information
                    </h3>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        <div>

                            <label class="block text-sm font-medium text-gray-700">
                                Supplier
                            </label>

                            <select
                                wire:model="supplier_id"
                                class="mt-1 block w-full rounded-lg border-gray-300"
                            >

                                <option value="">
                                    Select Supplier
                                </option>

                                @foreach($suppliers as $supplier)

                                    <option value="{{ $supplier->id }}">
                                        {{ $supplier->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('supplier_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        <div>

                            <label class="block text-sm font-medium text-gray-700">
                                Employee
                            </label>

                            <select
                                wire:model="employee_id"
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

                            @error('employee_id')
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

                        <h3 class="text-lg font-semibold text-gray-900">
                            Purchase Items
                        </h3>

                        <button
                            type="button"
                            wire:click="addItem"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                        >
                            + Add Item
                        </button>

                    </div>

                    <div class="space-y-4">

                        @foreach($items as $index => $item)

                            <div
                                wire:key="purchase-item-{{ $index }}"
                                class="rounded-lg border border-gray-200 p-4"
                            >

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-12">

                                    {{-- Product --}}
                                    <div class="md:col-span-5">

                                        <label class="block text-sm font-medium text-gray-700">
                                            Product
                                        </label>

                                        <select
                                            wire:model="items.{{ $index }}.product_id"
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

                                        @error("items.$index.product_id")
                                            <p class="mt-1 text-sm text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                    {{-- Quantity --}}
                                    <div class="md:col-span-2">

                                        <label class="block text-sm font-medium text-gray-700">
                                            Quantity
                                        </label>

                                        <input
                                            wire:model="items.{{ $index }}.quantity"
                                            type="number"
                                            min="1"
                                            class="mt-1 block w-full rounded-lg border-gray-300"
                                        >

                                        @error("items.$index.quantity")
                                            <p class="mt-1 text-sm text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                    {{-- Unit Price --}}
                                    <div class="md:col-span-2">

                                        <label class="block text-sm font-medium text-gray-700">
                                            Unit Price
                                        </label>

                                        <input
                                            wire:model="items.{{ $index }}.unit_price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="mt-1 block w-full rounded-lg border-gray-300"
                                        >

                                        @error("items.$index.unit_price")
                                            <p class="mt-1 text-sm text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>

                                    {{-- Item Total --}}
                                    <div class="md:col-span-2">

                                        <label class="block text-sm font-medium text-gray-700">
                                            Total
                                        </label>

                                        <div class="mt-1 flex h-10 items-center rounded-lg bg-gray-50 px-3 font-semibold text-gray-900">

                                            {{ number_format(
                                                $this->itemTotal($index),
                                                2
                                            ) }}

                                        </div>

                                    </div>

                                    {{-- Remove --}}
                                    <div class="md:col-span-1 flex items-end">

                                        @if(count($items) > 1)

                                            <button
                                                type="button"
                                                wire:click="removeItem({{ $index }})"
                                                class="w-full rounded-lg bg-red-100 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-200"
                                            >
                                                Remove
                                            </button>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                    {{-- Total --}}
                    <div class="mt-8 flex justify-end border-t pt-6">

                        <div class="text-right">

                            <p class="text-sm text-gray-500">
                                Grand Total
                            </p>

                            <p class="text-3xl font-bold text-gray-900">
                                {{ number_format($this->totalPrice(), 2) }}
                            </p>

                        </div>

                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3">

                    <a
                        href="{{ route('purchases.index') }}"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Create Purchase
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>