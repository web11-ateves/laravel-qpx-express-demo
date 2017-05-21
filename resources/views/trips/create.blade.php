@extends('layouts.master')

@section('content')

    @include('shared._errors')

    {!! Form::open(['route' => 'trips.store']) !!}
    @include('trips._form')
    {!! Form::close() !!}

@stop