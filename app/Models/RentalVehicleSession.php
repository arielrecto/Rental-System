<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalVehicleSession extends Model
{
    protected $fillable = [
        'rental_order_id',
        'vehicle_id',
        'session_token',
        'started_at',
        'ended_at',
        'status',
        'total_cost',
        'notes',
        'employee_name',
    ];



    public function rentalOrder()
    {
        return $this->belongsTo(RentalOrder::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function vehicleSessionLocations()
    {
        return $this->hasMany(VehicleSessionLocation::class);
    }
}
