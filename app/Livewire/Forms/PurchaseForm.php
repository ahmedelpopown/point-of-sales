<?php

namespace App\Livewire\Forms;

use App\Models\Purchase;
use App\Services\PurchaseService;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PurchaseForm extends Form
{
    public ?Purchase $purchase = null;

    #[Validate('required|exists:suppliers,id')]
    public $supplier_id = '';

    #[Validate('required|exists:employees,id')]
    public $employee_id = '';

    #[Validate('required|exists:warehouses,id')]
    public $warehouse_id = '';

    public array $items = [];

    public function setPurchase(Purchase $purchase): void
    {
        $this->purchase = $purchase;

        $this->supplier_id = $purchase->supplier_id;
        $this->employee_id = $purchase->employee_id;
        $this->warehouse_id = $purchase->warehouse_id;

        $this->items = $purchase
            ->purchaseItems()
            ->get()
            ->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ])
            ->toArray();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
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

    public function totalPrice(): float
    {
        return (float) collect($this->items)->sum(
            fn ($item) =>
                (float) ($item['quantity'] ?? 0)
                *
                (float) ($item['unit_price'] ?? 0)
        );
    }

    public function store(): Purchase
    {
        $this->validate();
        $this->validateItems();

        return app(PurchaseService::class)->create(
            supplierId: (int) $this->supplier_id,
            employeeId: (int) $this->employee_id,
            warehouseId: (int) $this->warehouse_id,
            items: $this->items,
        );
    }

    public function update(): Purchase
    {
        $this->validate();
        $this->validateItems();

        return app(PurchaseService::class)->update(
            purchase: $this->purchase,
            supplierId: (int) $this->supplier_id,
            employeeId: (int) $this->employee_id,
            warehouseId: (int) $this->warehouse_id,
            items: $this->items,
        );
    }

    private function validateItems(): void
    {
        $this->validate([
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
    }
}