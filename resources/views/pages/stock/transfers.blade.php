<?php

use App\Models\Employee;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Stock Transfer')] class extends Component
{
    public string $warehouseId = '';

    public string $shopId = '';

    public string $employeeId = '';

    public string $note = '';

    public array $items = [
        [
            'product_id' => '',
            'quantity' => 1,
        ],
    ];

    #[Computed]
    public function warehouses()
    {
        return Warehouse::query()
            ->where('status', 'active')
            ->orderBy('name')
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
    public function employees()
    {
        return Employee::query()
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    #[Computed]
    public function warehouseProducts()
    {
        if (! $this->warehouseId) {
            return collect();
        }

        return Product::query()
            ->where('status', 'active')
            ->whereHas('stocks', function (Builder $query) {
                $query
                    ->where(
                        'stockable_type',
                        Warehouse::class
                    )
                    ->where(
                        'stockable_id',
                        $this->warehouseId
                    )
                    ->where(
                        'quantity',
                        '>',
                        0
                    );
            })
            ->with([
                'stocks' => function ($query) {
                    $query
                        ->where(
                            'stockable_type',
                            Warehouse::class
                        )
                        ->where(
                            'stockable_id',
                            $this->warehouseId
                        );
                },
            ])
            ->orderBy('name')
            ->get();
    }

    public function updatedWarehouseId(): void
    {
        $this->items = [
            [
                'product_id' => '',
                'quantity' => 1,
            ],
        ];

        $this->resetErrorBag();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) === 1) {
            return;
        }

        unset($this->items[$index]);

        $this->items = array_values(
            $this->items
        );
    }

    public function availableQuantity(
        string|int $productId
    ): int {
        if (! $productId) {
            return 0;
        }

        $product = $this->warehouseProducts
            ->firstWhere('id', (int) $productId);

        return (int) (
            $product
            ?->stocks
            ?->first()
            ?->quantity
            ?? 0
        );
    }

    public function submit(): void
    {
        $this->validate([
            'warehouseId' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],

            'shopId' => [
                'required',
                'integer',
                'exists:shops,id',
            ],

            'employeeId' => [
                'required',
                'integer',
                'exists:employees,id',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $productIds = collect($this->items)
            ->pluck('product_id')
            ->filter()
            ->map(fn($id) => (int) $id);

        if ($productIds->duplicates()->isNotEmpty()) {
            $this->addError(
                'items',
                'The same product cannot be added more than once.'
            );

            return;
        }

        $warehouse = Warehouse::findOrFail(
            $this->warehouseId
        );

        $shop = Shop::findOrFail(
            $this->shopId
        );

        $employee = Employee::findOrFail(
            $this->employeeId
        );

        $transferItems = collect($this->items)
            ->map(function (array $item) {
                return [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ];
            })
            ->values()
            ->all();


        try {

            $transfer = app(StockService::class)->transfer(
                warehouse: $warehouse,
                shop: $shop,
                items: $transferItems,
                employee: $employee,
                note: $this->note ?: null,
            );
        } catch (ValidationException $e) {

            foreach ($e->errors() as $field => $messages) {

                foreach ($messages as $message) {
                    $this->addError(
                        $field,
                        $message
                    );
                }
            }

            return;
        }


        $this->reset([
            'warehouseId',
            'shopId',
            'employeeId',
            'note',
        ]);

        $this->items = [
            [
                'product_id' => '',
                'quantity' => 1,
            ],
        ];

        session()->flash(
            'message',
            "Transfer #{$transfer->id} created successfully."
        );
    }
};
?>

<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="sm:flex sm:items-center">

        <div class="sm:flex-auto">

            <h1 class="text-2xl font-semibold text-gray-900">
                Stock Transfer
            </h1>

            <p class="mt-2 text-sm text-gray-700">
                Transfer products from a warehouse to an authorized shop.
            </p>

        </div>

    </div>

    {{-- Success --}}
    @if(session()->has('message'))

    <div class="mt-4 rounded-md bg-green-50 p-4">

        <p class="text-sm font-medium text-green-800">
            {{ session('message') }}
        </p>

    </div>

    @endif

    {{-- Error --}}
    @if($errors->has('items'))

    <div class="mt-4 rounded-md bg-red-50 p-4">

        <p class="text-sm font-medium text-red-800">
            {{ $errors->first('items') }}
        </p>

    </div>

    @endif

    @if(session()->has('error'))

    <div class="mt-4 rounded-md bg-red-50 p-4">

        <p class="text-sm font-medium text-red-800">
            {{ session('error') }}
        </p>

    </div>

    @endif

    {{-- Transfer Information --}}
    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

            {{-- Warehouse --}}
            <div>
                <x-form.searchable-select label="Warehouse" model="warehouseId" :options="$this->warehouses" option-value="id" option-label="name" placeholder="Select Warehouse" />

                @error('warehouseId') <p class="mt-1 text-sm text-red-600"> {{ $message }} </p> @enderror
            </div>

            {{-- Shop --}}
            <div>
                <x-form.searchable-select label="Shop" model="shopId" :options="$this->shops" option-value="id" option-label="name" placeholder="Select Shop" />

                @error('shopId') <p class="mt-1 text-sm text-red-600"> {{ $message }} </p> @enderror

            </div>

            {{-- Employee --}}
            <div>
                <x-form.searchable-select label="Employee" model="employeeId" :options="$this->employees" option-value="id" option-label="full_name" placeholder="Select Employee" />

                @error('employeeId') <p class="mt-1 text-sm text-red-600"> {{ $message }} </p> @enderror

            </div>

        </div>

        {{-- Note --}}
        <div class="mt-6">
            <x-form.textarea label="Note" name="note" rows="3" wire:model.live="note" placeholder="Optional transfer note..." />
            
            @error('note') <p class="mt-1 text-sm text-red-600"> {{ $message }} </p> @enderror
        </div>

    </div>

    {{-- Products --}}
    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Products
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Select products available in the selected warehouse.
                </p>
            </div>

            <button
                type="button"
                wire:click="addItem"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Add Product
            </button>

        </div>

        <div class="mt-6 space-y-4">

            @foreach($items as $index => $item)

            <div
                wire:key="transfer-item-{{ $index }}"
                class="grid grid-cols-1 gap-4 rounded-lg border border-gray-200 p-4 md:grid-cols-12">

                {{-- Product --}}
                <div class="md:col-span-6">
   <x-form.searchable-select
        label="Product"
        model="items.{{ $index }}.product_id"
        :options="$this->warehouseProducts"
        option-value="id"
        option-label="name"
        placeholder="Select Product"
    />

                    @error("items.{$index}.product_id")
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                </div>

                {{-- Quantity --}}
                <div class="md:col-span-3">

                 <x-form.input
    label="Quantity"
    name="items.{{ $index }}.quantity"
    type="number"
    min="1"
    wire:model.live="items.{{ $index }}.quantity"
    :hint="$item['product_id']
        ? 'Available: ' . number_format(
            $this->availableQuantity($item['product_id'])
        )
        : null"
/>

                    @error("items.{$index}.quantity")
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror

                </div>

                {{-- Remove --}}
                <div class="flex items-end md:col-span-2">

                    <button
                        type="button"
                        wire:click="removeItem({{ $index }})"
                        class="w-full rounded-md border border-red-300 bg-white px-4 py-3 text-sm font-medium text-red-700 hover:bg-red-50">
                        Remove
                    </button>

                </div>

            </div>

            @endforeach

        </div>
        <form wire:submit="submit">

            {{-- Transfer fields --}}

            <div class="mt-6 flex justify-end">

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50">

                    <span wire:loading.remove>
                        Create Transfer
                    </span>

                    <span wire:loading>
                        Creating...
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>