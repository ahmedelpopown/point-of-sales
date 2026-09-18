<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->string('paypal_transaction_id')
                ->nullable()
                ->after('provider_reference');

            $table->decimal('paypal_fee', 12, 2)
                ->nullable()
                ->after('paypal_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->dropColumn([
                'paypal_transaction_id',
                'paypal_fee',
            ]);
        });
    }
};