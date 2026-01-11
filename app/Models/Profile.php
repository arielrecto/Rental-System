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
        'user_id'
    ];




    public function user()
    {
        return $this->belongTo(User::class);
    }
}
