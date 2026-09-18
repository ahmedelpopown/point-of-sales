<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
   $purchases = Purchase::all();
    $products = Product::all();

    foreach ($purchases as $purchase) {
        // بنختار عدد عشوائي من المنتجات لكل عملية شراء، وليكن من 1 لـ 5
        $randomProducts = $products->random(rand(1, 5));

        foreach ($randomProducts as $product) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id'  => $product->id,
                'quantity'    => rand(1, 10),
                'unit_price'  => rand(10, 100),
                'total_price' => rand(100, 1000),
            ]);
        }
    }
    }
}
