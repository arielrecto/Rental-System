<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{

    use HasFactory;

    protected $fillable = [
        'brand',
        'plate_no',
        'country_of_origin',
        'manufacturer',
        'model',
        'color',
        'type',
        'year',
        'mileage',
        'condition',
        'rental_rate',
        'status',
    ];



    public function asset()
    {
        return $this->morphOne(Asset::class, 'assetable');
    }
}
