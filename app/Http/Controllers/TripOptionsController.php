<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripOption;
use Illuminate\Http\Request;

class TripOptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $trip_option = TripOption::findOrFail($id);
        $page_title = "#$trip_option->id - Histórico de preços";
        return view('trip_options.show', compact('trip_option', 'page_title'));
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
        $trip_option = TripOption::findOrFail($id);
        $trip_option->delete();
        return redirect()->route('trips.show', $trip_option->trip_id);
    }

    public function bookmark($id)
    {
        $trip_option = TripOption::findOrFail($id);
        $trip_option->alert = !$trip_option->alert;
        $trip_option->save();
        return redirect()->route('trips.show', $trip_option->trip_id);
    }
}
