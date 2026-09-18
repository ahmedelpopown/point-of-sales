<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_id',
        'amount',
        'method',
        'payment_date',
        'order_id',
        'installment_plan_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function installmentPlan()
    {
        return $this->belongsTo(InstallmentPlan::class);
    }
}