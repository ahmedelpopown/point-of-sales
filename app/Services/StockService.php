<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use LogicException;

class StockService
{
    /**
     * Receives products from a purchase into the assigned warehouse stock.
     */
    public function receivePurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $purchase = Purchase::query()
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->with([
                    'warehouse',
                    'employee',
                    'purchaseItems.product',
                ])
                ->firstOrFail();

            if ($purchase->stock_applied_at !== null) {
                return;
            }

            if (! $purchase->warehouse) {
                throw new LogicException(
                    "Purchase #{$purchase->id} must have a warehouse assigned before receiving stock."
                );
            }

            // Sort purchase items by product_id to minimize deadlocks
            $sortedItems = $purchase->purchaseItems->sortBy('product_id');

            foreach ($sortedItems as $item) {
                $stock = $this->lockOrCreateStock(
                    product: $item->product,
                    stockable: $purchase->warehouse,
                );

                $this->increase(
                    stock: $stock,
                    quantity: (int) $item->quantity,
                    type: StockMovementType::PURCHASE,
                    reference: $purchase,
                    employee: $purchase->employee,
                    note: "Purchase #{$purchase->id}",
                );
            }

            $purchase->update([
                'stock_applied_at' => now(),
            ]);
        });
    }

    /**
     * Reverses stock previously received from a purchase.
     */
    public function reversePurchase(Purchase $purchase, Employee|int|null $employee = null): void
    {
        DB::transaction(function () use ($purchase, $employee) {
            $purchase = Purchase::query()
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->with([
                    'warehouse',
                    'employee',
                    'purchaseItems.product',
                ])
                ->firstOrFail();

            if ($purchase->stock_applied_at === null) {
                return;
            }

            if (! $purchase->warehouse) {
                throw new LogicException(
                    "Purchase #{$purchase->id} must have a warehouse."
                );
            }

            $resolvedEmployee = $employee instanceof Employee
                ? $employee
                : (is_int($employee) ? Employee::find($employee) : $purchase->employee);

            $sortedItems = $purchase->purchaseItems->sortBy('product_id');

            foreach ($sortedItems as $item) {
                $stock = $this->lockExistingStock(
                    product: $item->product,
                    stockable: $purchase->warehouse,
                );

                $this->decrease(
                    stock: $stock,
                    quantity: (int) $item->quantity,
                    type: StockMovementType::PURCHASE_REVERSAL,
                    reference: $purchase,
                    employee: $resolvedEmployee,
                    note: "Reversal of Purchase #{$purchase->id}",
                );
            }

            $purchase->update([
                'stock_applied_at' => null,
            ]);
        });
    }

    /**
     * Fulfills an order by deducting required quantities from each item's designated shop stock.
     */
    public function fulfillOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->with([
                    'employee',
                    'orderItems.shop',
                    'orderItems.product',
                ])
                ->firstOrFail();

            if ($order->stock_applied_at !== null) {
                return;
            }

            $items = $this->groupOrderItems($order->orderItems);

            foreach ($items as $item) {
                if (! $item['shop']) {
                    throw new LogicException(
                        "Order item with product #{$item['product']->id} does not have a shop assigned."
                    );
                }

                $stock = $this->lockExistingStock(
                    product: $item['product'],
                    stockable: $item['shop'],
                );

                $this->decrease(
                    stock: $stock,
                    quantity: (int) $item['quantity'],
                    type: StockMovementType::SALE,
                    reference: $order,
                    employee: $order->employee,
                    note: "Sale Order #{$order->id}",
                );
            }

            $order->update([
                'stock_applied_at' => now(),
            ]);
        });
    }

    /**
     * Reverses an order by returning product quantities back to each item's shop stock.
     */
    public function reverseOrder(Order $order, Employee|int|null $employee = null): void
    {
        DB::transaction(function () use ($order, $employee) {
            $order = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->with([
                    'employee',
                    'orderItems.product',
                    'orderItems.shop',
                ])
                ->firstOrFail();

            if ($order->stock_applied_at === null) {
                return;
            }

            $resolvedEmployee = $employee instanceof Employee
                ? $employee
                : (is_int($employee) ? Employee::find($employee) : $order->employee);

            $items = $this->groupOrderItems($order->orderItems);

            foreach ($items as $item) {
                if (! $item['shop']) {
                    throw new LogicException(
                        "Order item with product #{$item['product']->id} has no shop."
                    );
                }

                $stock = $this->lockOrCreateStock(
                    product: $item['product'],
                    stockable: $item['shop'],
                );

                $this->increase(
                    stock: $stock,
                    quantity: (int) $item['quantity'],
                    type: StockMovementType::SALE_REVERSAL,
                    reference: $order,
                    employee: $resolvedEmployee,
                    note: "Reversal of Sale Order #{$order->id}",
                );
            }

            $order->update([
                'stock_applied_at' => null,
            ]);
        });
    }

    /**
     * Transfers products immediately from a warehouse to an authorized shop.
     */
    public function transfer(
        Warehouse $warehouse,
        Shop $shop,
        array $items,
        Employee $employee,
        ?string $note = null,
    ): StockTransfer {
        $this->ensureWarehouseServesShop(
            warehouse: $warehouse,
            shop: $shop,
        );

        $normalizedItems = $this->normalizeTransferItems($items);

        return DB::transaction(function () use (
            $warehouse,
            $shop,
            $normalizedItems,
            $employee,
            $note,
        ) {
            $transfer = StockTransfer::create([
                'warehouse_id' => $warehouse->id,
                'shop_id' => $shop->id,
                'employee_id' => $employee->id,
                'note' => $note,
            ]);

            $productIds = $normalizedItems->pluck('product_id');
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            foreach ($normalizedItems as $item) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'products' => [
                            "Product #{$item['product_id']} not found.",
                        ],
                    ]);
                }

                $sourceStock = $this->lockExistingStock(
                    product: $product,
                    stockable: $warehouse,
                );

                $targetStock = $this->lockOrCreateStock(
                    product: $product,
                    stockable: $shop,
                );

                $this->decrease(
                    stock: $sourceStock,
                    quantity: $item['quantity'],
                    type: StockMovementType::TRANSFER_OUT,
                    reference: $transfer,
                    employee: $employee,
                    relatedStock: $targetStock,
                    note: "Transfer to {$shop->name}",
                );

                $this->increase(
                    stock: $targetStock,
                    quantity: $item['quantity'],
                    type: StockMovementType::TRANSFER_IN,
                    reference: $transfer,
                    employee: $employee,
                    relatedStock: $sourceStock,
                    note: "Transfer from {$warehouse->name}",
                );

                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                ]);
            }

            return $transfer->load([
                'items.product',
                'warehouse',
                'shop',
                'employee',
            ]);
        });
    }

    /**
     * Adjusts stock quantity at a specific location (Warehouse or Shop) to a target quantity.
     */
    public function adjust(
        Product $product,
        Model $stockable,
        int $newQuantity,
        ?Employee $employee = null,
        ?string $note = null,
    ): ?StockMovement {
        if ($newQuantity < 0) {
            throw new InvalidArgumentException(
                'Stock quantity cannot be negative.'
            );
        }

        return DB::transaction(function () use (
            $product,
            $stockable,
            $newQuantity,
            $employee,
            $note,
        ) {
            $stock = $this->lockOrCreateStock(
                product: $product,
                stockable: $stockable,
            );

            $before = (int) $stock->quantity;

            if ($before === $newQuantity) {
                return null;
            }

            if ($newQuantity > $before) {
                $difference = $newQuantity - $before;
                $this->increase(
                    stock: $stock,
                    quantity: $difference,
                    type: StockMovementType::ADJUSTMENT_IN,
                    employee: $employee,
                    note: $note ?? 'Stock adjustment up',
                );
            } else {
                $difference = $before - $newQuantity;
                $this->decrease(
                    stock: $stock,
                    quantity: $difference,
                    type: StockMovementType::ADJUSTMENT_OUT,
                    employee: $employee,
                    note: $note ?? 'Stock adjustment down',
                );
            }

            return $stock->movements()->latest('id')->first();
        });
    }

    /**
     * Increases a stock record's quantity and logs the movement.
     */
    public function increase(
        Stock $stock,
        int $quantity,
        StockMovementType $type,
        ?Model $reference = null,
        ?Employee $employee = null,
        ?Stock $relatedStock = null,
        ?string $note = null,
    ): StockMovement {
        if ($quantity <= 0) {
            throw new LogicException(
                'Stock quantity must be greater than zero.'
            );
        }

        $before = (int) $stock->quantity;
        $after = $before + $quantity;

        $stock->update([
            'quantity' => $after,
        ]);

        return $this->createMovement(
            stock: $stock,
            type: $type,
            quantity: $quantity,
            before: $before,
            after: $after,
            reference: $reference,
            employee: $employee,
            relatedStock: $relatedStock,
            note: $note,
        );
    }

    /**
     * Decreases a stock record's quantity and logs the movement.
     */
    public function decrease(
        Stock $stock,
        int $quantity,
        StockMovementType $type,
        ?Model $reference = null,
        ?Employee $employee = null,
        ?Stock $relatedStock = null,
        ?string $note = null,
    ): StockMovement {
        if ($quantity <= 0) {
            throw new LogicException(
                'Stock quantity must be greater than zero.'
            );
        }

        if ($stock->quantity < $quantity) {
            $locationName = $stock->stockable?->name ?? 'Location';
            throw ValidationException::withMessages([
                'stock' => [
                    "Insufficient stock for product #{$stock->product_id} at {$locationName}. "
                    . "Available: {$stock->quantity}, requested: {$quantity}.",
                ],
            ]);
        }

        $before = (int) $stock->quantity;
        $after = $before - $quantity;

        $stock->update([
            'quantity' => $after,
        ]);

        return $this->createMovement(
            stock: $stock,
            type: $type,
            quantity: $quantity,
            before: $before,
            after: $after,
            reference: $reference,
            employee: $employee,
            relatedStock: $relatedStock,
            note: $note,
        );
    }

    /**
     * Finds or creates a stock record with a lock for update.
     */
    public function lockOrCreateStock(Product $product, Model $stockable): Stock
    {
        $stock = $this->findStock(
            product: $product,
            stockable: $stockable,
            lock: true,
        );

        if ($stock) {
            return $stock;
        }

        return Stock::create([
            'product_id' => $product->id,
            'stockable_type' => $stockable->getMorphClass(),
            'stockable_id' => $stockable->getKey(),
            'quantity' => 0,
            'minimum_quantity' => 5,
        ]);
    }

    /**
     * Locks an existing stock record or throws a validation exception if it does not exist.
     */
    public function lockExistingStock(Product $product, Model $stockable): Stock
    {
        $stock = $this->findStock(
            product: $product,
            stockable: $stockable,
            lock: true,
        );

        if (! $stock) {
            $locationName = $stockable->name ?? class_basename($stockable);
            throw ValidationException::withMessages([
                'stock' => [
                    "No stock record exists for product #{$product->id} at {$locationName}.",
                ],
            ]);
        }

        return $stock;
    }

    /**
     * Retrieves the stock record for a product at a given stockable location.
     */
    public function findStock(Product $product, Model $stockable, bool $lock = false): ?Stock
    {
        $query = Stock::query()
            ->where('product_id', $product->id)
            ->where('stockable_type', $stockable->getMorphClass())
            ->where('stockable_id', $stockable->getKey());

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * Creates a StockMovement record.
     */
    private function createMovement(
        Stock $stock,
        StockMovementType $type,
        int $quantity,
        int $before,
        int $after,
        ?Model $reference = null,
        ?Employee $employee = null,
        ?Stock $relatedStock = null,
        ?string $note = null,
    ): StockMovement {
        $movement = new StockMovement([
            'product_id' => $stock->product_id,
            'stock_id' => $stock->id,
            'related_stock_id' => $relatedStock?->id,
            'type' => $type,
            'quantity' => $quantity,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'note' => $note,
        ]);

        $movement->stock()->associate($stock);

        if ($relatedStock) {
            $movement->relatedStock()->associate($relatedStock);
        }

        if ($employee) {
            $movement->employee()->associate($employee);
        }

        if ($reference) {
            $movement->reference()->associate($reference);
        }

        $movement->save();

        return $movement;
    }

    /**
     * Validates that the warehouse is linked to and serves the shop.
     */
private function ensureWarehouseServesShop(
    Warehouse $warehouse,
    Shop $shop
): void {
    $servesShop = $warehouse
        ->shops()
        ->whereKey($shop->id)
        ->exists();

    if (! $servesShop) {
        throw ValidationException::withMessages([
            'shopId' => [
                "Warehouse '{$warehouse->name}' does not serve shop '{$shop->name}'.",
            ],
        ]);
    }
}

    /**
     * Groups and sorts order items by shop and product to avoid deadlocks and aggregate quantities.
     */
    private function groupOrderItems(Collection $items): Collection
    {
        return $items
            ->sortBy([
                ['shop_id', 'asc'],
                ['product_id', 'asc'],
            ])
            ->groupBy(function ($item) {
                return "{$item->shop_id}:{$item->product_id}";
            })
            ->map(function (Collection $group) {
                $first = $group->first();

                return [
                    'product' => $first->product,
                    'shop' => $first->shop,
                    'quantity' => (int) $group->sum('quantity'),
                ];
            })
            ->values();
    }

    /**
     * Normalizes and sorts transfer items by product_id.
     */
    private function normalizeTransferItems(array $items): Collection
    {
        return collect($items)
            ->map(function (array $item) {
                return [
                    'product_id' => (int) ($item['product_id'] ?? 0),
                    'quantity' => (int) ($item['quantity'] ?? 0),
                ];
            })
            ->each(function (array $item) {
                if ($item['product_id'] <= 0 || $item['quantity'] <= 0) {
                    throw ValidationException::withMessages([
                        'products' => [
                            'Product and quantity must be valid positive integers.',
                        ],
                    ]);
                }
            })
            ->groupBy('product_id')
            ->map(function (Collection $group, $productId) {
                return [
                    'product_id' => (int) $productId,
                    'quantity' => (int) $group->sum('quantity'),
                ];
            })
            ->sortBy('product_id')
            ->values();
    }
}