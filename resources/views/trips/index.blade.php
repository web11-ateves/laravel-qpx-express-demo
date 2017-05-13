@extends('layouts.master')

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <table class="table table-hover" id="trips-table">
                        <thead><tr>
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Origem</th>
                            <th>Destino</th>
                            <th>Ida</th>
                            <th>Volta</th>
                            <th>Roundtrip</th>
                            <th>Nonstop</th>
                            <th>Ações</th>
                        </tr></thead>
                        <tbody>
                        @foreach($trips as $trip)
                            <tr>
                                <td>{{ $trip->id }}</td>
                                <td>{{ $trip->description }}</td>
                                <td>{{ $trip->origin }}</td>
                                <td>{{ $trip->destination }}</td>
                                <td>{{ display_date($trip->departure_date) }}</td>
                                <td>{{ display_date($trip->return_date) }}</td>
                                <td>{{ toAffirmative($trip->roundtrip) }}</td>
                                <td>{{ toAffirmative($trip->nonstop) }}</td>
                                <td>
                                    @include('shared._table_actions', ['object' => $trip, 'showUrl' => route("trips.show", $trip->id),'deleteUrl' => route("trips.destroy", $trip->id)])
                                </td>
                            </tr>
                        @endforeach
                        </tbody></table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <a href="" class="btn btn-primary">Nova pesquisa</a>
        </div>
    </div>

@stop

@section("scripts")
    @parent
    <script>
        $("#trips-table").DataTable();
    </script>
@endsection