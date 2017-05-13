<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{

    protected $fillable = [
        'user_id',
        'description',
        'origin',
        'destination',
        'roundtrip',
        'departure_date',
        'return_date',
        'nonstop',
        'permitted_carriers',
        'prohibited_carriers',
        'max_connection_time',
        'max_stops',
        'max_price',
        'earliest_departure_time',
        'latest_departure_time',
        'adults',
        'infants',
        'children',
        'seniors'
    ];

    protected $dates = ['departure_date', 'return_date'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function trip_options()
    {
        return $this->hasMany('App\Models\TripOption');
    }

    public function setMaxStopsAttribute($value)
    {
        $this->attributes['max_stops'] = $this->nonstop ? 0 : $value;
    }


}