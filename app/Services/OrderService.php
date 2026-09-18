<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {
    }

    
    public function create(
        ?int $userId,
        int $employeeId,
        
        array $items
    ): Order {
        return DB::transaction(function () use (
            $userId,
            $employeeId,
            $items
        ) {
            $this->validateItems($items);

            $preparedItems = $this->sortItems(
                $this->prepareItems($items)
            );

            $total = collect($preparedItems)->sum('total');

            $order = Order::create([
                'user_id' => $userId,
                'employee_id' => $employeeId,
                'total' => $total,
            ]);

            foreach ($preparedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);

                $this->inventoryService->decrease(
                    productId: $item['product_id'],
                    quantity: $item['quantity'],
                    type: StockMovementType::SALE,
                    reference: $order,
                    employeeId: $employeeId,
                    note: "Sale Order #{$order->id}",
                );
            }

            return $order->load([
                'user',
                'employee',
                'orderItems.product',
            ]);
        });
    }

    public function update(
        Order $order,
        ?int $userId,
        int $employeeId,
        array $items
    ): Order {
        return DB::transaction(function () use (
            $order,
            $userId,
            $employeeId,
            $items
        ) {
            $this->validateItems($items);

            $preparedItems = $this->sortItems(
                $this->prepareItems($items)
            );

            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            $oldItems = $order
                ->orderItems()
                ->get()
                ->sortBy('product_id');

            /*
            |--------------------------------------------------------------------------
            | Reverse Old Sale
            |--------------------------------------------------------------------------
            */

            foreach ($oldItems as $oldItem) {
                $this->inventoryService->increase(
                    productId: (int) $oldItem->product_id,
                    quantity: (int) $oldItem->quantity,
                    type: StockMovementType::SALE_REVERSAL,
                    reference: $order,
                    employeeId: $employeeId,
                    note: "Reversal of Sale Order #{$order->id} before update",
                );
            }

         $order->delete();
            $total = collect($preparedItems)->sum('total');

            $order->update([
                'user_id' => $userId,
                'employee_id' => $employeeId,
                'total' => $total,
            ]);

            foreach ($preparedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);

                $this->inventoryService->decrease(
                    productId: $item['product_id'],
                    quantity: $item['quantity'],
                    type: StockMovementType::SALE,
                    reference: $order,
                    employeeId: $employeeId,
                    note: "Updated Sale Order #{$order->id}",
                );
            }

            return $order->fresh([
                'user',
                'employee',
                'orderItems.product',
            ]);
        });
    }

  public function delete(Order $order): void
{
    DB::transaction(function () use ($order) {
        $order = Order::query()
            ->lockForUpdate()
            ->findOrFail($order->id);

        $items = $order
            ->orderItems()
            ->get()
            ->sortBy('product_id');

        foreach ($items as $item) {
            $this->inventoryService->increase(
                productId: (int) $item->product_id,
                quantity: (int) $item->quantity,
                type: StockMovementType::SALE_REVERSAL,
                reference: $order,
                employeeId: $order->employee_id,
                note: "Reversal of deleted Sale Order #{$order->id}",
            );
        }

        $order->delete();
    });
}

    private function prepareItems(array $items): array
    {
        return collect($items)
            ->map(function ($item) {
                $product = Product::findOrFail(
                    $item['product_id']
                );

                $quantity = (int) $item['quantity'];

                $price = (float) $item['price'];

                return [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price,
                ];
            })
            ->values()
            ->all();
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
                'Order must contain at least one product.'
            );
        }

        $productIds = collect($items)->pluck('product_id');

        if ($productIds->duplicates()->isNotEmpty()) {
            throw new RuntimeException(
                'The same product cannot be added twice in the same order.'
            );
        }

        foreach ($items as $item) {
            if (
                !isset($item['product_id']) ||
                !isset($item['quantity']) ||
                !isset($item['price'])
            ) {
                throw new RuntimeException(
                    'Invalid order item.'
                );
            }

            if ((int) $item['quantity'] < 1) {
                throw new RuntimeException(
                    'Quantity must be at least 1.'
                );
            }

            if ((float) $item['price'] < 0) {
                throw new RuntimeException(
                    'Price cannot be negative.'
                );
            }
        }
    }
    
}