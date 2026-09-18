<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'description', 'barcode', 'price', 'image', 'status'];

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }
public function getCurrentQuantityAttribute($value): int
{
    if (array_key_exists('current_quantity', $this->attributes)) {
        return (int) $value;
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

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
                ->orWhere('barcode', 'like', "%$search%");
        });
    }
}
