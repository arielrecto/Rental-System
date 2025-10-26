<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintainanceVehicleRequest extends Model
{
    protected $fillable = [
        'ref_number',
        'vehicle_id',
        'description',
        'requested_date',
        'maintainance_date',
        'is_completed',
        'cost',
        'requested_by',
    ];


    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
