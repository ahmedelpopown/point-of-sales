<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_with_interest',
        'down_payment',
        'remaining_amount',
        'start_date',
        'installment_plan_id',
        'order_id',
    ];

    protected $casts = [
        'total_with_interest' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'start_date' => 'date',
    ];

    public function installmentPlan()
    {
        return $this->belongsTo(InstallmentPlan::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getMonthlyAmountAttribute()
    {
        if (!$this->installmentPlan || $this->installmentPlan->months_count <= 0) {
            return 0;
        }

        return round(
            $this->remaining_amount / $this->installmentPlan->months_count,
            2
        );
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }
}