<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stockable_type',
        'stockable_id',
        'quantity',
        'minimum_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'minimum_quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_quantity;
    }
}