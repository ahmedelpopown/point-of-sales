<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('type');

            $table->unsignedInteger('quantity');

            $table->unsignedInteger('quantity_before');

            $table->unsignedInteger('quantity_after');

            $table->nullableMorphs('reference');

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index([
                'product_id',
                'created_at',
            ]);

            $table->index([
                'type',
                'created_at',
            ]);

            $table->index([
                'employee_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};