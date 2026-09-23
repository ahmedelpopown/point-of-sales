<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use HasFactory,HasRoles;
    
    protected string $guard_name = 'employee';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'department',
        'position',
        'salary',
        'phone',
        'hire_date',
        'status',
        'address',
         'shop_id',
    'warehouse_id',
        'password',
    ];
  protected function casts(): array
{
    return[

        'salary' => 'decimal:2',
        'hire_date' => 'date',
    ];
}

public function shop()
{
    return $this->belongsTo(Shop::class);
}

public function warehouse()
{
    return $this->belongsTo(Warehouse::class);
}

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }


    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('department', 'like', "%$search%")
                ->orWhere('position', 'like', "%$search%");
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }
}
