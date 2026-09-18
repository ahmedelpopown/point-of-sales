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
use LogicException;

class StockService
{

/*
receivePurchase:
use the transaction to  make the action run in the time 
get the purchase through the id with the relation ships
make shure if this columns (stock_applied_at & warehouse)
after that get single of each item in purchase
make shure this product have record  with specified class ? 
true 
increase
else create through lockOrCreateStock and contenu
*/
      public function reversePurchase(
    Purchase $purchase,
    int $employeeId,
): void {
    DB::transaction(function () use ($purchase, $employeeId) {

        $purchase = Purchase::query()
            ->whereKey($purchase->id)
            ->lockForUpdate()
            ->with([
                'warehouse',
                'purchaseItems.product',
            ])
            ->firstOrFail();

        if (! $purchase->stock_applied_at) {
            return;
        }

        if (! $purchase->warehouse) {
            throw new LogicException(
                'Purchase must have a warehouse.'
            );
        }

        foreach ($purchase->purchaseItems as $item) {

            $stock = $this->lockExistingStock(
                product: $item->product,
                stockable: $purchase->warehouse,
            );

            $this->decrease(
                stock: $stock,
                quantity: (int) $item->quantity,
                type: StockMovementType::PURCHASE_REVERSAL,
                reference: $purchase,
                employee: Employee::find($employeeId),
                note: "Reversal of Purchase #{$purchase->id}",
            );
        }

        $purchase->update([
            'stock_applied_at' => null,
        ]);
    });
}

    /*
 Fulfills an order by deducting the required product quantities from stock.
 
  Process steps:
  1: Locks the order record for update within a database transaction to prevent race conditions.
  2: Checks idempotency via 'stock_applied_at' to prevent duplicate stock deduction.
  3: Aggregates order items grouped by shop and product.
  4: Locks each target stock record and deducts the corresponding quantities.
  5: Logs stock movements linked to the employee and order reference.
  6: Marks the order as stock-applied with the current timestamp.
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

            if ($order->stock_applied_at) {
                return;
            }

            $items = $this->groupOrderItems($order->orderItems);

            foreach ($items as $item) {
                $stock = $this->lockExistingStock(
                    product: $item['product'],
                    stockable: $item['shop'],
                );

                $this->decrease(
                    stock: $stock,
                    quantity: $item['quantity'],
                    type: StockMovementType::SALE,
                    reference: $order,
                    employee: $order->employee,
                    note: "Order #{$order->id}",
                );
            }

            $order->update([
                'stock_applied_at' => now(),
            ]);
        });
    }


    /*
* Transfers stock items from a source warehouse to a destination shop.

Process steps:
   1: Validates that the warehouse is assigned to serve the target shop.
   2: Normalizes, validates, and sorts the input transfer items.
   3: Executes the transfer process within a database transaction:
   a: Creates a parent StockTransfer record.
   b: Fetches and key-indexes target products in a single bulk query (N+1 optimization).
   c: Iterates through items, ensuring product existence.
   d: Locks source stock (Warehouse) and locks/creates destination stock (Shop).
   e: Performs deduction from source stock and logs StockMovementType::TRANSFER_OUT.
   f: Performs addition to target stock and logs StockMovementType::TRANSFER_IN.
   g: Records detail lines in StockTransferItem.
   4: Loads and returns the transfer instance with its relationships.
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

        $items = $this->normalizeTransferItems($items);

        return DB::transaction(function () use (
            $warehouse,
            $shop,
            $items,
            $employee,
            $note,
        ) {
            $transfer = StockTransfer::create([
                'warehouse_id' => $warehouse->id,
                'shop_id' => $shop->id,
                'employee_id' => $employee->id,
                'note' => $note,
            ]);
// get the product record within id == item->product_id 
            $products = Product::query()
                ->whereIn('id',$items->pluck('product_id'))
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $product = $products->get(
                    $item['product_id']
                );

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
                    employee: $employee,
                    note: "Transfer to {$shop->name}",
                    relatedStock: $targetStock,
                    reference: $transfer,
                );

                $this->increase(
                    stock: $targetStock,
                    quantity: $item['quantity'],
                    type: StockMovementType::TRANSFER_IN,
                    employee: $employee,
                    note: "Transfer from {$warehouse->name}",
                    relatedStock: $sourceStock,
                    reference: $transfer,
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
// 1: The quantity is not less than 0
//  2:Stores the stock before adding the new quantity and after 
// 3 update the quantity
// 4 create movement : create record StockMovement
    private function increase(Stock $stock,int $quantity,StockMovementType $type,?Model $reference = null,?Employee $employee = null,?Stock $relatedStock = null,?string $note = null,
    ): void {
        if ($quantity <= 0) {
            throw new LogicException(
                'Stock quantity must be greater than zero.'
            );
        }

        $before = $stock->quantity;
        $after = $before + $quantity;

        $stock->update([
            'quantity' => $after,
        ]);

        $this->createMovement(
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

    /**same increase function but this for decrease*/

    private function decrease(
        Stock $stock,
        int $quantity,
        StockMovementType $type,
        ?Model $reference = null,
        ?Employee $employee = null,
        ?Stock $relatedStock = null,
        ?string $note = null,
    ): void {
        if ($quantity <= 0) {
            throw new LogicException(
                'Stock quantity must be greater than zero.'
            );
        }

        if ($stock->quantity < $quantity) {
            throw ValidationException::withMessages([
                'stock' => [
                    "Insufficient stock for product #{$stock->product_id}. "
                    . "Available: {$stock->quantity}, "
                    . "requested: {$quantity}.",
                ],
            ]);
        }

        $before = $stock->quantity;
        $after = $before - $quantity;

        $stock->update([
            'quantity' => $after,
        ]);

        $this->createMovement(
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

    /*
connects stoke with the related_stock_id in stoke movement
connects employee with the related_stock_id in stoke movement
connects reference with the related_stock_id in stoke movement
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
            'type' => $type,
            'quantity' => $quantity,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'note' => $note,
        ]);

        $movement->stock()->associate($stock);

        if ($relatedStock) {
            $movement->relatedStock()->associate(
                $relatedStock
            );
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

    /*
    take to argument Product and stockable [findStock=inventory record for product]
if we have stoke return the data 
else create
this function make shure we have record for this product with specified class
    */
    private function lockOrCreateStock(Product $product,Model $stockable,): Stock {
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
// findStock thgtou the product 
    private function lockExistingStock(
        Product $product,
        Model $stockable,
    ): Stock {
        $stock = $this->findStock(
            product: $product,
            stockable: $stockable,
            lock: true,
        );

        if (! $stock) {
            throw ValidationException::withMessages([
                'stock' => [
                    "No stock record exists for product "
                    . "#{$product->id}.",
                ],
            ]);
        }

        return $stock;
    }
/*
This query asks the database: "Retrieve the inventory record for product `$product` linked to the specified `$stockable` (whether it is a warehouse, a branch, etc.)."
*/
    private function findStock( Product $product, Model $stockable, bool $lock = false,): ?Stock {
        $query = Stock::query()
            ->where('product_id', $product->id) // 1
            ->where(
                'stockable_type',
                $stockable->getMorphClass() // to get the class ==  relation  like wherehowns supplier ...
            )
            ->where(
                'stockable_id',
                $stockable->getKey() // get primary key
            );

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }
// It stores the incoming `Warehouse` value in order to retrieve the `shop` ID via the relationship between the `Warehouse` and the `shop`.
// and make shure  it's exists
    private function ensureWarehouseServesShop(
        Warehouse $warehouse,
        Shop $shop,
    ): void {
        $servesShop = $warehouse
            ->shops()
            ->whereKey($shop->id)
            ->exists();

        if (! $servesShop) {
            throw ValidationException::withMessages([
                'shop_id' => [
                    "Warehouse {$warehouse->name} "
                    . "does not serve shop {$shop->name}.",
                ],
            ]);
        }
    }

    private function normalizeTransferItems(
        array $items,
    ): Collection {
        return collect($items)
            ->map(function (array $item) {
                return [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ];
            })
            ->each(function (array $item) {
                if (
                    $item['product_id'] <= 0
                    || $item['quantity'] <= 0
                ) {
                    throw ValidationException::withMessages([
                        'products' => [
                            'Product and quantity must be valid.',
                        ],
                    ]);
                }
            })
            ->groupBy('product_id')
            ->map(function (Collection $group, $productId) {
                return [
                    'product_id' => (int) $productId,
                    'quantity' => $group->sum('quantity'),
                ];
            })
            ->sortBy('product_id')
            ->values();
    }

    /*
* Sorts, groups, and aggregates order items by shop and product.
 *
 * Steps:
 * 1: Sorts items by shop_id and product_id.
 * 2: Groups items by "shop_id:product_id" key.
 * 3: Maps each group into a single record containing product, shop, and total quantity.
 * 4: Re-indexes the collection keys.
    */
    public function reverseOrder(
    Order $order,
    Employee $employee,
): void {
    DB::transaction(function () use ($order, $employee) {

        $order = Order::query()
            ->whereKey($order->id)
            ->lockForUpdate()
            ->with([
                'orderItems.product',
                'orderItems.shop',
            ])
            ->firstOrFail();

        if (! $order->stock_applied_at) {
            return;
        }

        foreach ($order->orderItems as $item) {

            if (! $item->shop) {
                throw new LogicException(
                    "Order item #{$item->id} has no shop."
                );
            }

            $stock = $this->lockOrCreateStock(
                product: $item->product,
                stockable: $item->shop,
            );

            $this->increase(
                stock: $stock,
                quantity: (int) $item->quantity,
                type: StockMovementType::SALE_REVERSAL,
                reference: $order,
                employee: $employee,
                note: "Reversal of Sale Order #{$order->id}",
            );
        }

        $order->update([
            'stock_applied_at' => null,
        ]);
    });
}
    private function groupOrderItems(
        Collection $items,
    ): Collection {
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
                    'quantity' => $group->sum('quantity'),
                ];
            })
            ->values();
    }
}