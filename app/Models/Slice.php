<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slice extends Model
{

    protected $fillable = [
        'trip_option_id',
        'code',
        'duration',
        'origin_airport',
        'destination_airport',
        'departure_time',
        'arrival_time',
        'connection_duration'
    ];

    protected $dates = ['departure_time', 'arrival_time'];

    public function trip_option()
    {
        return $this->belongsTo('App\Models\TripOption');
    }

    public function segments()
    {
        return $this->hasMany('App\Models\Segment');
    }

}