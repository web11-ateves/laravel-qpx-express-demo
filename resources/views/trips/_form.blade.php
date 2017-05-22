<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('description', 'Titulo') !!}
            {!! Form::text('description', null, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('origin', 'Origem') !!}
            {!! Form::text('origin', null, ['class' => 'form-control']) !!}
        </div>
        <div class="checkbox">
            {!! Form::hidden('roundtrip', 0) !!}
            <label>{!! Form::checkbox('roundtrip', 1, null, ['id' => 'roundtrip']) !!} Ida e volta</label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('destination', 'Destino') !!}
            {!! Form::text('destination', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">


        <div class="form-group">
            {!! Form::label('departure_date', 'Saída') !!}

            <div class="input-group input-daterange" id="departure_date_selector" style="display: none;">
                {!! Form::text('departure_date', ($trip->departure_date ? display_date($trip->departure_date) :  ''), ['id' => 'departure_date_1', 'class' => 'datepicker form-control']) !!}
                <div class="input-group-addon">-</div>
                {!! Form::text('departure_date_end', ($trip->departure_date_end ? display_date($trip->departure_date_end) :  ''), ['class' => 'datepicker form-control']) !!}
            </div>

            {!! Form::text('departure_date', ($trip->departure_date ? display_date($trip->departure_date) :  ''), ['id' => 'departure_date_2', 'class' => 'datepicker form-control']) !!}

            <div class="checkbox">
                <label>{!! Form::checkbox('flexible_dates', true, null, ['id' => 'flexible_dates']) !!} Datas flexíveis</label>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('return_date', 'Retorno') !!}
            {!! Form::text('return_date', ($trip->return_date ? display_date($trip->return_date) :  ''), ['class' => 'datepicker form-control', 'id' => 'return_date']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::hidden('nonstop', false) !!}
            {!! Form::label('max_stops', 'Max. Escalas') !!}
            {!! Form::text('max_stops', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('max_connection_time', 'Max. Tempo Conexão (min)') !!}
            {!! Form::text('max_connection_time', null, ['class' => 'form-control']) !!}
        </div>
        <div class="checkbox">
            {!! Form::hidden('alert', 0) !!}
            <label>{!! Form::checkbox('alert', 1) !!} Monitorar</label>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('max_price', 'Max. Preço') !!}
            {!! Form::text('max_price', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('end_date', 'Monitorar até') !!}
            {!! Form::text('end_date', ($trip->end_date ? display_date($trip->end_date) :  ''), ['class' => 'datepicker form-control']) !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('earliest_departure_time', 'Horário de partida (min)') !!}
            {!! Form::text('earliest_departure_time', null, ['class' => 'timemask form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('latest_departure_time', 'Horário de partida (max)') !!}
            {!! Form::text('latest_departure_time', null, ['class' => 'timemask form-control']) !!}
        </div>
    </div>
    <div class="col-md-1">
        <div class="form-group">
            {!! Form::label('adults', 'Adultos') !!}
            {!! Form::text('adults', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('infants', 'Bebês') !!}
            {!! Form::text('infants', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-1">
        <div class="form-group">
            {!! Form::label('children', 'Crianças') !!}
            {!! Form::text('children', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('seniors', 'Idosos') !!}
            {!! Form::text('seniors', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('permitted_carriers', 'Cias permitidas') !!}
            {!! Form::text('permitted_carriers', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('prohibited_carriers', 'Cias proibidas') !!}
            {!! Form::text('prohibited_carriers', null, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

{!! Form::hidden('user_id', 1) !!}

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            Ref: <a href="https://developers.google.com/qpx-express/v1/trips/search" target="_blank">Documentação Google QPX Express</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <a href="{{ route('trips.index') }}" class="btn btn-primary">Voltar</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group pull-right">
            {!! Form::submit('Salvar', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
</div>

@section("scripts")
    @parent

    <script>
        //Date range picker
        $('#reservation').daterangepicker();
        //Date range picker with time picker
        $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
        //Date picker
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        });
        $(".timemask").inputmask("99:99", {"placeholder": "hh:mm"});
        $("#roundtrip").on('change', function () {
            $("#return_date").prop('disabled', !$(this)[0].checked);
        });
        $("#flexible_dates").on('change', function () {
            if($(this)[0].checked) {
                $("#departure_date_selector").show();
                $("#departure_date_2").hide();
                $("#departure_date_2").prop('disabled', true);
            } else {
                $("#departure_date_selector").hide();
                $("#departure_date_2").show();
                $("#departure_date_2").prop('disabled', false);
            }
        });
        @if(isset($trip->departure_date_end))
            $("#flexible_dates").trigger("click");
        @endif
    </script>
@endsection