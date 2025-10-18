<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    protected $fillable = [
        'account_number',
        'account_name',
        'provider',
        'descriptions',
        'is_active',
    ];


    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
