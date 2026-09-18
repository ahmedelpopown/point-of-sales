<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'address',
        'paypal_email',
        'phone',
        'governorate_id',
        'city_id',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            PurchasePayment::class,
            Purchase::class,
            'supplier_id',  // FK on purchases
            'purchase_id',  // FK on purchase_payments
            'id',           // PK on suppliers
            'id'            // PK on purchases
        );
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        });
    }
}