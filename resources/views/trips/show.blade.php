@extends('layouts.master')

@section('content')

    <div class="row">
        <div class="col-xs-12">
            Filtrar por:
            <a href="?s=agony" class="btn-sm {{ $sort == 'agony' ? 'btn-primary' : 'btn-default' }}">Agonia</a>
            <a href="?s=price" class="btn-sm {{ $sort == 'price' ? 'btn-primary' : 'btn-default' }}">Preço</a>
            <a href="?s=duration" class="btn-sm {{ $sort == 'duration' ? 'btn-primary' : 'btn-default' }}">Duração</a>
        </div>
    </div>

    <div class="row">&nbsp;</div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive no-padding">
                    <table class="table" id="trip-option-table">
                        <thead><tr>
                            <th></th>
                            <th>Saída</th>
                            <th>Chegada</th>
                            <th>Tempo</th>
                            <th>Conexão</th>
                            <th>Cias</th>
                            <th>Alerta</th>
                            <th>Ações</th>
                        </tr></thead>
                        <tbody>
                        <?php $count = 0 ?>
                        @foreach($trip_options as $trip_option)
                            <?php $style = ($count % 2 == 0) ? "active" : "";
                               $count += 1; ?>
                            <tr class="{{$style}}">
                                <td colspan="6">
                                    <strong>
                                    <?php $cheapest = $trip_option->prices()->cheapest(); ?>
                                    #{{ $trip_option->id }} - {{ toReal($cheapest->price_total_brl) }}
                                    </strong>
                                </td>
                                <td>{{ toAffirmative($trip_option->alert) }}</td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        <a title="Favoritar" class="btn btn-primary" href="{{route('trip_options.bookmark', $trip_option->id)}}">
                                            <i class="fa {{ $trip_option->alert ? 'fa-bookmark-o' : 'fa-bookmark' }}"></i>
                                        </a>
                                    </div>
                                    @include('shared._table_actions', ['object' => $trip_option, 'showUrl' => route("trip_options.show", $trip_option->id), 'deleteUrl' => route("trip_options.destroy", $trip_option->id)])
                                </td>
                            </tr>
                            @foreach($trip_option->slices as $slice)
                                <tr class="{{$style}}">
                                    <td></td>
                                    <td>{{ $slice->origin_airport }} - {{ display_datetime($slice->departure_time) }}</td>
                                    <td>{{ $slice->destination_airport }} - {{ display_datetime($slice->arrival_time) }}</td>
                                    <td>{{ $slice->duration }}</td>
                                    <td>{{ $slice->connection_duration }} ({{ $slice->stops }})</td>
                                    <td>{{ $slice->companies() }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody></table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <a href="{{ route('trips.index') }}" class="btn btn-primary">Voltar</a>
        </div>
    </div>

@stop

@section("scripts")
    @parent
    <script>
        //$("#trip-option-table").DataTable();
    </script>
@endsection

