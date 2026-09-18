<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('stock_applied_at')
                ->nullable()
                ->after('employee_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->timestamp('stock_applied_at')
                ->nullable()
                ->after('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('stock_applied_at');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('stock_applied_at');
        });
    }
};