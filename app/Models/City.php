<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /** @use HasFactory<\Database\Factories\CityFactory> */
    use HasFactory;
    protected $fillable=['name'];
    // City.php
public function governorate()
{
    return $this->belongsTo(Governorate::class, 'governorate_id');
}

public function users()
{
    return $this->hasMany(User::class, 'city_id');
}
public function supplier()
{
    return $this->hasMany(User::class, 'city_id');
}

}
