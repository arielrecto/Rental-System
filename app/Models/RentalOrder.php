<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalOrder extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'rental_date',
        'return_date',
        'total_amount',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }


    public function attachments(){
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
