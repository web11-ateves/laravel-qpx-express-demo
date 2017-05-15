@extends('layouts.master')

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div id="chart" ></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive no-padding">
                    <table class="table">
                        <thead><tr>
                            <th></th>
                            <th>Data / hora</th>
                            <th>Valor</th>
                        </tr></thead>
                        <tbody>
                        @foreach($trip_option->prices as $price)
                            <tr >
                                <td></td>
                                <td>{{ display_datetime($price->created_at) }}</td>
                                <td>{{ toReal($price->price_total_brl) }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        </tbody></table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <a href="{{ route('trips.show', $trip_option->trip_id) }}" class="btn btn-primary">Voltar</a>
        </div>
    </div>

@stop

@section("scripts")
    @parent
    <script src="https://www.gstatic.com/charts/loader.js"></script>

    <script>
        google.charts.load('current', {'packages':['corechart', 'line']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('datetime', 'Data da coleta');
            data.addColumn('number', 'R$');

            data.addRows([{!! $trip_option->chartData() !!}]);

            var options = {
                hAxis: { title: 'Data' },
                vAxis: { title: 'R$' },
                height: 400,
                legend: { position: 'none' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart'));

            chart.draw(data, options);
        }
    </script>
@endsection