<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leg extends Model
{

    protected $fillable = [
        'segment_id',
        'code',
        'aircraft',
        'origin_airport',
        'origin_airport_name',
        'origin_city',
        'destination_airport',
        'destination_airport_name',
        'destination_city',
        'destination_airport',
        'departure_time',
        'arrival_time',
        'duration'
    ];

    protected $dates = ['departure_time', 'arrival_time'];

    public function segment()
    {
        return $this->belongsTo('App\Models\Segment');
    }

}