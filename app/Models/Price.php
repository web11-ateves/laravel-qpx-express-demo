<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{

    protected $fillable = [
        'trip_option_id',
        'price_total_brl',
        'base_fare_brl',
        'base_fare_usd',
        'taxes_brl'
    ];
    
    public function trip_option()
    {
        return $this->belongsTo('App\Models\TripOption');
    }

}