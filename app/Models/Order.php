<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'total',
        'user_id',
        'employee_id',
        'stock_applied_at',
         'shop_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function shop()
{
    return $this->belongsTo(Shop::class);
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
    public function stockMovements(): MorphMany
{
    return $this->morphMany(StockMovement::class, 'reference');
}
}