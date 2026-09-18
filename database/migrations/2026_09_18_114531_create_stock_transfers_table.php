<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('shop_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};