<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class OrderService
{
    public function __construct(
        private readonly StockService $stockService,
    ) {
    }

    /**
     * Creates a new order, stores order items with their shops, and fulfills the stock.
     */
    public function create(
        ?int $userId,
        int $employeeId,
        array $items,
    ): Order {
        return DB::transaction(function () use (
            $userId,
            $employeeId,
            $items,
        ) {
            $this->validateItems($items);

            $employee = Employee::findOrFail($employeeId);

            $preparedItems = $this->sortItems(
                $this->prepareItems($items)
            );

            $total = (float) collect($preparedItems)->sum('total');

            $order = Order::create([
                'user_id' => $userId,
                'employee_id' => $employee->id,
                'total' => $total,
            ]);

            foreach ($preparedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'shop_id' => $item['shop_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            $this->stockService->fulfillOrder($order);

            return $order->fresh([
                'user',
                'employee',
                'orderItems.product',
                'orderItems.shop',
            ]);
        });
    }

    /**
     * Updates an existing order by reversing old stock deductions, replacing items, and fulfilling new items.
     */
    public function update(
        Order $order,
        ?int $userId,
        int $employeeId,
        array $items,
    ): Order {
        return DB::transaction(function () use (
            $order,
            $userId,
            $employeeId,
            $items,
        ) {
            $this->validateItems($items);

            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            $employee = Employee::findOrFail($employeeId);

            // 1. Reverse the existing stock deduction from each item's shop
            $this->stockService->reverseOrder(
                order: $order,
                employee: $employee,
            );

            $preparedItems = $this->sortItems(
                $this->prepareItems($items)
            );

            $total = (float) collect($preparedItems)->sum('total');

            // 2. Update order attributes
            $order->update([
                'user_id' => $userId,
                'employee_id' => $employee->id,
                'total' => $total,
            ]);

            // 3. Replace old order items
            $order->orderItems()->delete();

            foreach ($preparedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'shop_id' => $item['shop_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            // 4. Fulfill new order stock
            $this->stockService->fulfillOrder($order);

            return $order->fresh([
                'user',
                'employee',
                'orderItems.product',
                'orderItems.shop',
            ]);
        });
    }

    /**
     * Deletes an order and restores its stock to the original shops.
     */
    public function delete(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            $employee = Employee::find($order->employee_id);

            $this->stockService->reverseOrder(
                order: $order,
                employee: $employee,
            );

            $order->delete();
        });
    }

    /**
     * Validates that items exist, have valid quantities and prices, and no duplicate (product_id, shop_id) pairs.
     */
    private function validateItems(array $items): void
    {
        if (empty($items)) {
            throw new RuntimeException('Order must contain at least one item.');
        }

        $pairs = collect($items)->map(function ($item) {
            $productId = $item['product_id'] ?? null;
            $shopId = $item['shop_id'] ?? null;

            return "{$productId}:{$shopId}";
        });

        if ($pairs->duplicates()->isNotEmpty()) {
            throw new RuntimeException(
                'The same product cannot be added more than once for the same shop in the same order.'
            );
        }

        foreach ($items as $item) {
            if (
                empty($item['product_id']) ||
                empty($item['shop_id']) ||
                ! isset($item['quantity']) ||
                ! isset($item['price'])
            ) {
                throw new InvalidArgumentException('Invalid order item provided.');
            }

            if ((int) $item['quantity'] < 1) {
                throw new InvalidArgumentException('Item quantity must be at least 1.');
            }

            if ((float) $item['price'] < 0) {
                throw new InvalidArgumentException('Item price cannot be negative.');
            }
        }
    }

    /**
     * Prepares normalized item shapes with computed totals.
     */
    private function prepareItems(array $items): array
    {
        return collect($items)->map(function ($item) {
            $quantity = (int) $item['quantity'];
            $price = (float) $item['price'];

            return [
                'product_id' => (int) $item['product_id'],
                'shop_id' => (int) $item['shop_id'],
                'quantity' => $quantity,
                'price' => $price,
                'total' => $quantity * $price,
            ];
        })->all();
    }

    /**
     * Sorts items by shop_id and product_id to ensure deterministic locking order.
     */
    private function sortItems(array $items): array
    {
        return collect($items)
            ->sortBy([
                ['shop_id', 'asc'],
                ['product_id', 'asc'],
            ])
            ->values()
            ->all();
    }
}