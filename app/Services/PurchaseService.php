<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Employee;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseService
{
    public function __construct(
        private readonly StockService $stockService,
    ) {
    }

    public function create(
        int $supplierId,
        int $employeeId,
        int $warehouseId,
        array $items,
    ): Purchase {
        return DB::transaction(function () use (
            $supplierId,
            $employeeId,
            $warehouseId,
            $items,
        ) {
            $this->validateItems($items);

            $employee = Employee::findOrFail($employeeId);
            $warehouse = Warehouse::findOrFail($warehouseId);

            $items = $this->sortItems($items);

            $purchase = Purchase::create([
                'supplier_id' => $supplierId,
                'employee_id' => $employee->id,
                'warehouse_id' => $warehouse->id,
                'total_price' => $this->calculateTotal($items),
            ]);

            $this->createItems(
                purchase: $purchase,
                items: $items,
            );

            $this->stockService->receivePurchase($purchase);

            return $purchase->fresh([
                'supplier',
                'employee',
                'warehouse',
                'purchaseItems.product',
            ]);
        });
    }

    public function update(
        Purchase $purchase,
        int $supplierId,
        int $employeeId,
        int $warehouseId,
        array $items,
    ): Purchase {
        return DB::transaction(function () use (
            $purchase,
            $supplierId,
            $employeeId,
            $warehouseId,
            $items,
        ) {
            $this->validateItems($items);

            $purchase = Purchase::query()
                ->lockForUpdate()
                ->findOrFail($purchase->id);

            $employee = Employee::findOrFail($employeeId);

            /*
             * Reverse the old stock first.
             */
            $this->stockService->reversePurchase(
                purchase: $purchase,
                employee: $employee,
            );

            $items = $this->sortItems($items);

            $purchase->update([
                'supplier_id' => $supplierId,
                'employee_id' => $employee->id,
                'warehouse_id' => $warehouseId,
                'total_price' => $this->calculateTotal($items),
            ]);

            $purchase->purchaseItems()->delete();

            $this->createItems(
                purchase: $purchase,
                items: $items,
            );

            $this->stockService->receivePurchase($purchase);

            return $purchase->fresh([
                'supplier',
                'employee',
                'warehouse',
                'purchaseItems.product',
            ]);
        });
    }

    public function delete(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {

            $purchase = Purchase::query()
                ->lockForUpdate()
                ->findOrFail($purchase->id);

            $employee = Employee::findOrFail(
                $purchase->employee_id
            );

            $this->stockService->reversePurchase(
                purchase: $purchase,
                employee: $employee,
            );

            $purchase->delete();
        });
    }

    private function createItems(
        Purchase $purchase,
        array $items,
    ): void {
        foreach ($items as $item) {

            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ]);
        }
    }

    private function calculateTotal(array $items): float
    {
        return (float) collect($items)->sum(
            fn ($item) =>
                (float) $item['quantity']
                * (float) $item['unit_price']
        );
    }

    private function sortItems(array $items): array
    {
        return collect($items)
            ->sortBy('product_id')
            ->values()
            ->all();
    }

    private function validateItems(array $items): void
    {
        if (empty($items)) {
            throw new RuntimeException(
                'Purchase must contain at least one item.'
            );
        }

        $productIds = collect($items)->pluck('product_id');

        if ($productIds->duplicates()->isNotEmpty()) {
            throw new RuntimeException(
                'The same product cannot be added twice in the same purchase.'
            );
        }

        foreach ($items as $item) {

            if (
                ! isset($item['product_id']) ||
                ! isset($item['quantity']) ||
                ! isset($item['unit_price'])
            ) {
                throw new RuntimeException(
                    'Invalid purchase item.'
                );
            }

            if ((int) $item['quantity'] < 1) {
                throw new RuntimeException(
                    'Quantity must be at least 1.'
                );
            }

            if ((float) $item['unit_price'] < 0) {
                throw new RuntimeException(
                    'Unit price cannot be negative.'
                );
            }
        }
    }
}