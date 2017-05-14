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

Route::get('trips/options/{id}', 'TripsController@trip_option')->name('trips.option');
Route::get('trips/options/{id}/bookmark', 'TripsController@bookmark')->name('trips.option.bookmark');