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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $page_title = $trip->description;
        return view('trips.show', compact('trip_options', 'page_title', 'sort'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function trip_option($id)
    {
        $trip_option = TripOption::findOrFail($id);
        $page_title = "#$trip_option->id - Histórico de preços";
        return view('trips.option', compact('trip_option', 'page_title'));
    }

    public function bookmark($id)
    {
        $trip_option = TripOption::findOrFail($id);
        $trip_option->alert = !$trip_option->alert;
        $trip_option->save();
        return redirect()->route('trips.show', $trip_option->trip_id);
    }
}
