<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinimumPrice extends Model
{

    protected $fillable = [
        'trip_id',
        'trip_option_id',
        'value',
    ];

    public function trip()
    {
        return $this->belongsTo('App\Models\Trip');
    }

    public function trip_option()
    {
        return $this->belongsTo('App\Models\TripOption');
    }

}