<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripOption extends Model
{

    protected $fillable = [
        'trip_id',
        'code',
    ];

    protected $dates = ['departure_date', 'arrival_date'];

    public function trip()
    {
        return $this->belongsTo('App\Models\Trip');
    }

    public function prices()
    {
        return $this->hasMany('App\Models\Price');
    }

    public function slices()
    {
        return $this->hasMany('App\Models\Slice');
    }
}