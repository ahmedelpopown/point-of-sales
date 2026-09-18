<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'quantity',
        'price',
        'order_id',
        'product_id',
        'shop_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function shop(): BelongsTo
{
    return $this->belongsTo(Shop::class);
}

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}