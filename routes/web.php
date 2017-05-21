<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', 'TripsController@index');

Route::resource('trips', 'TripsController');
Route::resource('trip_options', 'TripOptionsController');

Route::get('trips_options/{id}/bookmark', 'TripOptionsController@bookmark')->name('trip_options.bookmark');
Route::get('trips/{id}/bookmark', 'TripsController@bookmark')->name('trips.bookmark');