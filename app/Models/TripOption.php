<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TripOption extends Model
{

    protected $fillable = [
        'trip_id',
        'alert'
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

    public function scopeWithPrices($query)
    {
        return $query->join('prices', 'trip_options.id', '=', 'prices.trip_option_id');
    }

    public function scopeOrderByAgony($query)
    {
        return $query->withPrices()
            ->select(DB::raw('trip_options.*, (duration * prices.price_total_brl) AS agony'))
            ->orderBy("agony", "ASC");
    }

    public function scopeSortBy($query, $sort)
    {
        if($sort == 'price') {
            return $query->withPrices()->orderBy("price_total_brl", "ASC");
        } else if ($sort == 'duration') {
            return $query->orderBy("duration", "ASC");
        } else {
            return $query->orderByAgony();
        }
    }

    public function scopeSelectedColumns($query)
    {
        return $query->select(DB::raw('trip_options.*, prices.price_total_brl, prices.created_at, prices.id AS price_id'));
    }

    public function chartData()
    {
        $data = "";
        foreach ($this->prices as $price) {
            $value = $price->price_total_brl;
            $data = $data . "[new Date('{$price->created_at}'), {$value}],";
        }
        return $data;
    }
}