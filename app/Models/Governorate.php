<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    /** @use HasFactory<\Database\Factories\GovernorateFactory> */
    use HasFactory;
    protected $fillable =['name','governorate_id'];


    public function cities()
{
    return $this->hasMany(City::class, 'governorate_id');
}

public function users()
{
    return $this->hasMany(User::class, 'governorate_id');
}


public function supplier()
{
    return $this->hasMany(User::class, 'governorate_id');
}

 
public function cityUsers()
{
    return $this->hasManyThrough(User::class, City::class);
}

}
