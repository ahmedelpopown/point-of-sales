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
 Schema::create('purchase_payments', function (Blueprint $table) {
    $table->id();

    $table->foreignId('purchase_id')
        ->constrained('purchases')
        ->cascadeOnDelete();

    $table->foreignId('employee_id')
        ->nullable()
        ->constrained('employees')
        ->nullOnDelete();

    // Accounting amount
    $table->decimal('amount', 12, 2);

    // Currency of the purchase/payment in your system
    $table->string('currency', 3)->default('EGP');

    // PayPal actual transaction details
    $table->decimal('paypal_amount', 12, 2)->nullable();
    $table->string('paypal_currency', 3)->nullable();
    $table->decimal('exchange_rate', 12, 6)->nullable();

    $table->string('payment_method');
    $table->dateTime('paid_at')->nullable();

    $table->string('provider')->nullable();
    $table->string('provider_reference')->nullable();
    $table->string('provider_status')->nullable();

    $table->text('notes')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
