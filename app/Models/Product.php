<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'barcode',
        'price',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * The total quantity is an aggregate of stock records, not a products-table column.
     * Prefer withSum('stocks as current_quantity', 'quantity') in list/export queries.
     */
    public function getCurrentQuantityAttribute($value): int
    {
        if ($value !== null) {
            return (int) $value;
        }

        if ($this->relationLoaded('stocks')) {
            return (int) $this->stocks->sum('quantity');
        }

        return (int) $this->stocks()->sum('quantity');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
        });
    }
}
