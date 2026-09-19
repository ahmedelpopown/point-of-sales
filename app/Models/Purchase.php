<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'total_price',
        'supplier_id',
        'employee_id',
        'warehouse_id',
        'stock_applied_at',

    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'stock_applied_at' => 'datetime',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(
            (float) $this->total_price - $this->paid_amount,
            0
        );
    }
    public function successfulPayments()
    {
        return $this->payments()
            ->where(function ($query) {
                $query
                    ->whereNull('provider')
                    ->orWhere(function ($query) {
                        $query
                            ->where('provider', 'paypal')
                            ->where('provider_status', 'SUCCESS');
                    });
            });
    }
}
