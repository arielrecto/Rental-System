<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'type',
        'brand',
        'model',
        'serial_no',
        'condition',
        'assetable_id',
        'assetable_type',
        'status',
        'purchase_date',
        'warranty_expiry_date',
        'purchase_price',
    ];



    public function assetable()
    {
        return $this->morphTo();
    }


    public function depreciation(){
        return $this->hasOne(Depreciation::class);
    }
}
