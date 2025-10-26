<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleSessionLocation extends Model
{
    protected $fillable = [
        'rental_vehicle_session_id',
        'latitude',
        'longitude',
        'recorded_at',
        'location_description',
        'speed',
        'direction',
        'vehicle_id',
    ];

    public function rentalVehicleSession()
    {
        return $this->belongsTo(RentalVehicleSession::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
