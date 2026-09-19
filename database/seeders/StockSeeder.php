<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::query()
            ->where('status', 'active')
            ->get();

        $warehouses = Warehouse::query()
            ->where('status', 'active')
            ->get();

        $shops = Shop::query()
            ->where('status', 'active')
            ->get();

        if ($products->isEmpty()) {
            $this->command->warn('No active products found.');

            return;
        }

        if ($warehouses->isEmpty()) {
            $this->command->warn('No active warehouses found.');

            return;
        }

        if ($shops->isEmpty()) {
            $this->command->warn('No active shops found.');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Warehouse Stock
        |--------------------------------------------------------------------------
        */

        foreach ($warehouses as $warehouseIndex => $warehouse) {

            foreach ($products as $productIndex => $product) {

                // نخلي بعض المنتجات بدون stock في بعض الـlocations
                // عشان ما يبقاش كل Product موجود في كل مكان.
                if (($productIndex + $warehouseIndex) % 5 === 0) {
                    continue;
                }

                $minimumQuantity = match ($productIndex % 4) {
                    0 => 5,
                    1 => 10,
                    2 => 20,
                    default => 15,
                };

                $quantity = match ($productIndex % 6) {
                    0 => 0,       // Out of Stock
                    1 => $minimumQuantity, // Low Stock
                    2 => $minimumQuantity - 2, // Low Stock
                    3 => 25,
                    4 => 75,
                    default => 150,
                };

                Stock::create([
                    'product_id' => $product->id,
                    'stockable_type' => Warehouse::class,
                    'stockable_id' => $warehouse->id,
                    'quantity' => max(0, $quantity),
                    'minimum_quantity' => $minimumQuantity,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Shop Stock
        |--------------------------------------------------------------------------
        */

        foreach ($shops as $shopIndex => $shop) {

            foreach ($products as $productIndex => $product) {

                // نخلي توزيع المنتجات مختلف عن الـwarehouses
                if (($productIndex + $shopIndex) % 4 === 0) {
                    continue;
                }

                $minimumQuantity = match ($productIndex % 3) {
                    0 => 5,
                    1 => 10,
                    default => 15,
                };

                $quantity = match ($productIndex % 5) {
                    0 => 0,       // Out of Stock
                    1 => $minimumQuantity, // Low Stock
                    2 => $minimumQuantity - 1, // Low Stock
                    3 => 40,
                    default => 90,
                };

                Stock::create([
                    'product_id' => $product->id,
                    'stockable_type' => Shop::class,
                    'stockable_id' => $shop->id,
                    'quantity' => max(0, $quantity),
                    'minimum_quantity' => $minimumQuantity,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update products.current_quantity
        |--------------------------------------------------------------------------
        |
        | current_quantity موجود في جدول products، لكن الـStock هو
        | المصدر الحقيقي للكمية.
        |
        */

        foreach ($products as $product) {

            $totalQuantity = Stock::query()
                ->where('product_id', $product->id)
                ->sum('quantity');

            $product->update([
                'current_quantity' => $totalQuantity,
            ]);
        }

        $this->command->info('Stock data seeded successfully.');
    }
}