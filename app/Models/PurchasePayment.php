<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'employee_id',
        'amount',
        'currency',

        'paypal_amount',
        'paypal_currency',
        'exchange_rate',

        'payment_method',
        'paid_at',

        'provider',
        'provider_reference',
        'paypal_transaction_id',
        'paypal_fee',
        'provider_status',

        'provider_error',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paypal_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'paypal_fee' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}