@extends('layouts.master')

@section('content')

    @include('shared._errors')

    {!! Form::model($trip, ['method' => 'PATCH', 'action' => ['TripsController@update', $trip->id]]) !!}
    @include('trips._form')
    {!! Form::close() !!}

@stop