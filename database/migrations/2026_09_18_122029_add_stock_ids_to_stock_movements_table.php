<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('stock_id')
                ->nullable()
                ->after('product_id')
                ->constrained('stocks')
                ->restrictOnDelete();

            $table->foreignId('related_stock_id')
                ->nullable()
                ->after('stock_id')
                ->constrained('stocks')
                ->nullOnDelete();

            $table->index([
                'stock_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex([
                'stock_id',
                'created_at',
            ]);

            $table->dropForeign(['stock_id']);
            $table->dropForeign(['related_stock_id']);

            $table->dropColumn([
                'stock_id',
                'related_stock_id',
            ]);
        });
    }
};