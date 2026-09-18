<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'quantity', 'date', 'payment', 'employee_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
