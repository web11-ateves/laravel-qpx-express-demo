<!DOCTYPE html>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>

    <?php $fontFamily = 'font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;'; ?>

    <body style="{{ $fontFamily }}">
        <p>{{ $title }}</p>
        @foreach($trip_option->slices as $slice)
            <p>Slice {{ $slice->id }}:
                {{ $slice->origin_airport }} para {{ $slice->destination_airport }}
                <ul>
                    <li>Saída: {{ display_datetime($slice->departure_time) }}</li>
                    <li>Chegada: {{ display_datetime($slice->arrival_time) }}</li>
                    <li>Tempo: {{ $slice->duration }} ({{ $slice->connection_duration }} conexão)</li>
                    <li>Companhia(s): {{ $slice->companies() }}</li>
                </ul>
            </p>
        @endforeach
        <p>Variação: {{ $text }}</p>
    </body>

</html>