<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('installment_id');
            $table->decimal('amount', 10, 2);
            $table->string('method');
            $table->date('payment_date');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('installment_plan_id')->constrained('installment_plans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
