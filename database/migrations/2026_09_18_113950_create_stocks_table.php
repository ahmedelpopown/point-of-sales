<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

            $table->morphs('stockable');

            $table->unsignedInteger('quantity')->default(0);

            // عندها نطلق Low Stock Alert
            $table->unsignedInteger('minimum_quantity')->default(5);

            $table->timestamps();

            $table->unique([
                'product_id',
                'stockable_type',
                'stockable_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};