<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depreciation extends Model
{
    protected $fillable = [
        'asset_id',
        'depreciation_date',
        'amount',
        'description',
        'status',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
