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

    public function companies()
    {
        return str_limit(join(" / ", $this->segments->pluck('carrier_name')->all()), $limit = 40, $end = '...');
    }

    public function getConnectionDurationAttribute($value)
    {
        return $this->minToHours($value);
    }

    public function getDurationAttribute($value)
    {
        return $this->minToHours($value);
    }

    private function minToHours($value){
        return date('H:i', mktime(0,$value));
    }

}