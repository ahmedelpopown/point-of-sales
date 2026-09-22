<?php

namespace App\Livewire\Forms;

use App\Models\Order;
use App\Models\PurchaseItem;
use App\Services\OrderService;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class OrderForm extends Form
{
    public ?Order $order = null;

    #[Validate('nullable|exists:users,id')]
    public $user_id = '';

    #[Validate('required|exists:employees,id')]
    public $employee_id = '';

    public array $items = [];

    public function setOrder(Order $order): void
    {
        $this->order = $order;

        $this->user_id = $order->user_id;
        $this->employee_id = $order->employee_id;

        $this->items = $order->orderItems
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'shop_id' => $item->shop_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            })
            ->toArray();
    }

  public function addItem(): void
{
    $shopId = null;

    if (auth('employee')->check()) {
        $shopId = auth('employee')->user()->shop_id;
    }

    $this->items[] = [
        'product_id' => '',
        'shop_id' => $shopId,
        'quantity' => 1,
        'price' => 0,
    ];
}

    public function removeItem(int $index): void
    {
        if (count($this->items) <= 1) {
            return;
        }

        unset($this->items[$index]);

        $this->items = array_values($this->items);
    }

    public function itemTotal(int $index): float
    {
        $item = $this->items[$index];

        return (float) ($item['quantity'] ?? 0) * (float) ($item['price'] ?? 0);
    }

    public function total(): float
    {
        return (float) collect($this->items)
            ->sum(function ($item) {
                return (float) ($item['quantity'] ?? 0) * (float) ($item['price'] ?? 0);
            });
    }
    private function refreshPricesFromLastPurchase(): void
{
    foreach ($this->items as $index => $item) {
        $productId = $item['product_id'] ?? null;

        if (!$productId) {
            continue;
        }

        $lastPurchasePrice = PurchaseItem::query()
            ->where('product_id', $productId)
            ->latest('created_at')
            ->value('unit_price');

        $this->items[$index]['price'] = $lastPurchasePrice !== null
            ? (float) $lastPurchasePrice
            : 0;
    }
}

 public function validateOrder(): void
{
    $this->refreshPricesFromLastPurchase();

    $this->validate([
        'user_id' => [
            'nullable',
            'exists:users,id',
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
        ],
        'items.*.shop_id' => [
            'required',
            'exists:shops,id',
        ],
        'items.*.quantity' => [
            'required',
            'integer',
            'min:1',
        ],
        'items.*.price' => [
            'required',
            'numeric',
            'min:0',
        ],
    ]);

    $duplicates = collect($this->items)
        ->map(
            fn ($item) =>
                ($item['product_id'] ?? '') . '-' . ($item['shop_id'] ?? '')
        )
        ->duplicates();

    if ($duplicates->isNotEmpty()) {
        throw ValidationException::withMessages([
            'items' =>
                'The same product cannot be added more than once for the same shop in the same order.',
        ]);
    }
}

    public function store(): Order
    {
        $this->validateOrder();

        return app(OrderService::class)->create(
            userId: $this->user_id ? (int) $this->user_id : null,
            employeeId: (int) $this->employee_id,
            items: $this->items,
        );
    }
public function updatedItems($value, $key): void
{
    if (!str_ends_with($key, '.product_id')) {
        return;
    }

    $index = (int) explode('.', $key)[0];

    if (!$value) {
        $this->items[$index]['price'] = 0;

        return;
    }

    $price = PurchaseItem::query()
        ->where('product_id', $value)
        ->latest('created_at')
        ->value('unit_price');

    $this->items[$index]['price'] = $price ?? 0;
}
    public function update(): Order
    {
        $this->validateOrder();

        return app(OrderService::class)->update(
            order: $this->order,
            userId: $this->user_id ? (int) $this->user_id : null,
            employeeId: (int) $this->employee_id,
            items: $this->items,
        );
    }
}
