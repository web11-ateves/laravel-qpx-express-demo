<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'description',
        'origin',
        'destination',
        'roundtrip',
        'departure_date',
        'return_date',
        'departure_date_end',
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
        'seniors',
        'end_date',
        'alert'
    ];

    protected $dates = ['departure_date', 'return_date', 'end_date', 'departure_date_end', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function trip_options()
    {
        return $this->hasMany('App\Models\TripOption');
    }

    public function min_prices()
    {
        return $this->hasMany('App\Models\MinimumPrice');
    }

    public function setNonstopAttribute($value)
    {
        $this->attributes['nonstop'] = ($this->max_stops && $this->max_stops == 0);
    }

    public function setDepartureDateAttribute($val)
    {
        $date = Carbon::createFromFormat('d/m/Y', $val);
        $this->attributes['departure_date'] = Carbon::parse($date);
    }

    public function setReturnDateAttribute($val)
    {
        if(empty($val)){
            $this->attributes['return_date'] = null;
        } else {
            $date = Carbon::createFromFormat('d/m/Y', $val);
            $this->attributes['return_date'] = Carbon::parse($date);
        }
    }

    public function setEndDateAttribute($val)
    {
        if(empty($val)){
            $this->attributes['end_date'] = null;
        } else {
            $date = Carbon::createFromFormat('d/m/Y', $val);
            $this->attributes['end_date'] = Carbon::parse($date);
        }
    }

    public function setDepartureDateEndAttribute($val)
    {
        if(empty($val)){
            $this->attributes['departure_date_end'] = null;
        } else {
            $date = Carbon::createFromFormat('d/m/Y', $val);
            $this->attributes['departure_date_end'] = Carbon::parse($date);
        }

    }

    public function chartData()
    {
        $data = "";
        foreach ($this->min_prices as $price) {
            $data = $data . "[new Date('{$price->created_at}'), {$price->value}],";
        }
        return $data;
    }

}