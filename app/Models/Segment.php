<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{

    protected $fillable = [
        'slice_id',
        'code',
        'duration',
        'carrier_code',
        'carrier_name',
        'flight_number',
        'seats_available',
        'connection_duration',
        'origin_airport',
        'destination_airport',
        'departure_time',
        'arrival_time'
    ];

    protected $dates = ['departure_time', 'arrival_time'];

    public function slice()
    {
        return $this->belongsTo('App\Models\Slice');
    }

    public function legs()
    {
        return $this->hasMany('App\Models\Leg');
    }

}