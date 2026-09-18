<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use RuntimeException;

class InventoryService
{
    /**
     * Increase product stock and record the movement.
     *
     * The caller is responsible for the surrounding transaction.
     */
    public function increase(
        int $productId,
        int $quantity,
        StockMovementType $type,
        ?Model $reference = null,
        ?int $employeeId = null,
        ?string $note = null,
    ): StockMovement {
        $this->validateQuantity($quantity);
        $this->validateIncreaseType($type);

        $product = $this->lockProduct($productId);

        $before = (int) $product->current_quantity;
        $after = $before + $quantity;

        $product->update([
            'current_quantity' => $after,
        ]);

        return $this->createMovement(
            product: $product,
            type: $type,
            quantity: $quantity,
            quantityBefore: $before,
            quantityAfter: $after,
            reference: $reference,
            employeeId: $employeeId,
            note: $note,
        );
    }

    /**
     * Decrease product stock and record the movement.
     *
     * The caller is responsible for the surrounding transaction.
     */
    public function decrease(
        int $productId,
        int $quantity,
        StockMovementType $type,
        ?Model $reference = null,
        ?int $employeeId = null,
        ?string $note = null,
    ): StockMovement {
        $this->validateQuantity($quantity);
        $this->validateDecreaseType($type);

        $product = $this->lockProduct($productId);

        $before = (int) $product->current_quantity;

        if ($before < $quantity) {
            throw new RuntimeException(
                "Not enough stock for product: {$product->name}. "
                . "Available: {$before}, Required: {$quantity}."
            );
        }

        $after = $before - $quantity;

        $product->update([
            'current_quantity' => $after,
        ]);

        return $this->createMovement(
            product: $product,
            type: $type,
            quantity: $quantity,
            quantityBefore: $before,
            quantityAfter: $after,
            reference: $reference,
            employeeId: $employeeId,
            note: $note,
        );
    }

    /**
     * Lock the product row for update.
     */
    private function lockProduct(int $productId): Product
    {
        return Product::query()
            ->lockForUpdate()
            ->findOrFail($productId);
    }

    /**
     * Create stock movement record.
     */
    private function createMovement(
        Product $product,
        StockMovementType $type,
        int $quantity,
        int $quantityBefore,
        int $quantityAfter,
        ?Model $reference,
        ?int $employeeId,
        ?string $note,
    ): StockMovement {
        return StockMovement::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'reference_type' => $reference?->getMorphClass(),
            'reference_id' => $reference?->getKey(),
            'employee_id' => $employeeId,
            'note' => $note,
        ]);
    }

    /**
     * Validate quantity.
     */
    private function validateQuantity(int $quantity): void
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException(
                'Stock quantity must be at least 1.'
            );
        }
    }

    /**
     * Validate movement type for stock increase.
     */
private function validateIncreaseType(
    StockMovementType $type
): void {
    if (!in_array($type, [
        StockMovementType::PURCHASE,
        StockMovementType::SALE_REVERSAL,
        StockMovementType::ADJUSTMENT_IN,
    ], true)) {
        throw new InvalidArgumentException(
            "Movement type [{$type->value}] cannot increase stock."
        );
    }
}

    /**
     * Validate movement type for stock decrease.
     */
private function validateDecreaseType(
    StockMovementType $type
): void {
    if (!in_array($type, [
        StockMovementType::SALE,
        StockMovementType::PURCHASE_RETURN,
        StockMovementType::PURCHASE_REVERSAL,
        StockMovementType::ADJUSTMENT_OUT,
    ], true)) {
        throw new InvalidArgumentException(
            "Movement type [{$type->value}] cannot decrease stock."
        );
    }
}
/**
 * Adjust product stock to a specific quantity.
 *
 * The caller is responsible for the surrounding transaction.
 */
public function adjust(
    int $productId,
    int $newQuantity,
    ?int $employeeId = null,
    ?string $note = null,
): ?StockMovement {
    if ($newQuantity < 0) {
        throw new InvalidArgumentException(
            'Stock quantity cannot be negative.'
        );
    }

    $product = $this->lockProduct($productId);

    $before = (int) $product->current_quantity;

    if ($before === $newQuantity) {
        return null;
    }

    if ($newQuantity > $before) {
        $type = StockMovementType::ADJUSTMENT_IN;
        $quantity = $newQuantity - $before;
    } else {
        $type = StockMovementType::ADJUSTMENT_OUT;
        $quantity = $before - $newQuantity;
    }

    $product->update([
        'current_quantity' => $newQuantity,
    ]);

    return $this->createMovement(
        product: $product,
        type: $type,
        quantity: $quantity,
        quantityBefore: $before,
        quantityAfter: $newQuantity,
        reference: null,
        employeeId: $employeeId,
        note: $note,
    );
}
}