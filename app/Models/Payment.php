<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payment_account_id',
        'ref_number',
        'total_amount',
        'memo',
        'status',
        'paid_by',
        'payable_id',
        'payable_type',
    ];


    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function attachments(){
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
