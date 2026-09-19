<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // shop_id belongs on order_items table, not orders table
        if (Schema::hasColumn('orders', 'shop_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['shop_id']);
                $table->dropColumn('shop_id');
            });
        }
    }

    public function down(): void
    {
    }
};