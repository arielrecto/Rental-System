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
        'purchase_cost',
        'purchase_date',
        'status',
    ];





    public function asset()
    {
        return $this->morphOne(Asset::class, 'assetable');
    }

    public function image(){
        return $this->morphOne(Attachment::class, 'attachable');
    }

    public function rentalVehicleSessions(){
        return $this->hasMany(RentalVehicleSession::class);
    }

    public function name(){
        return $this->brand . ' - ' . $this->model;
    }

    public function vehicleSessionLocations()
    {
        return $this->hasMany(VehicleSessionLocation::class);
    }


    public function maintainanceRequests()
    {
        return $this->hasMany(MaintainanceVehicleRequest::class);
    }
}
