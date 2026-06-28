<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'gender',
        'birth_date',
        'user_id',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'drivers_license_number',
        'drivers_license_expiry',
        'drivers_license_verified',
        'drivers_license_verified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
