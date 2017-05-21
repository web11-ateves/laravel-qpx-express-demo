<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripOption;
use Illuminate\Http\Request;

class TripsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trips = Trip::all();
        $page_title = "Pesquisas";
        return view('trips.index', compact('trips', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = "Nova viagem";
        $trip = new Trip;
        return view('trips.create', compact('page_title', 'trip'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $trip = Trip::create($request->all());
        $request->session()->flash('message', 'Viagem criada com sucesso!');
        return redirect('trips');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $sort = $request->input('s', 'agony');
        $trip_options = $trip->trip_options()->sortBy($sort)->get()->unique();
        $min_prices = $trip->min_prices;
        $page_title = $trip->description;
        return view('trips.show', compact('trip', 'min_prices', 'trip_options', 'page_title', 'sort'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $trip = Trip::findOrFail($id);
        $page_title = "Editando Viagem #". $trip->id;
        return view('trips.edit', compact('trip', 'page_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $trip->fill($request->all());
        $flexible_dates = $request->input('flexible_dates');
        if(!$flexible_dates) {
            $trip->departure_date_end = null;
        }
        if(!$trip->roundtrip) {
            $trip->return_date = null;
        }
        $trip->save();
        $request->session()->flash('message', 'Viagem atualizada com sucesso!');
        return redirect('trips');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();
        $request->session()->flash('message', 'Viagem removida com sucesso!');

        return redirect('trips');
    }

    public function bookmark($id)
    {
        $trip = Trip::findOrFail($id);
        $trip->alert = !$trip->alert;
        $trip->save();
        return redirect('trips');
    }
}
