@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
Archivo de Transferencias Banco Galicia
@endsection

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1>Falta validar las fechas</h1>
                        <div class="col-sm-12" id="divSubmit">
                            {{  Form::open() }}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('fecha_desde', 'Desde:') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-calendar"></span></span>
                                            {!! Form::text('fecha_desde', null, ['class'=>'form-control pull-right']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('fecha_hasta', 'Hasta:') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-calendar"></span></span>
                                            {!! Form::text('fecha_hasta', null, ['class'=>'form-control pull-right']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::submit('Crear Archivo de Pago Global', ['class'=>'btn btn-primary']) !!}
                            {{ Form::close() }}
                        </div>
                        <div class="col-sm-12" id="loading" style="display: none">
                            <p class="text-center"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                            <span class="sr-only">Loading...</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('otros-scripts')
<link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
    $(function() {
        $('#btnSubmit').on('click', function(){
            $('#loading').attr('style', '');
            $('#divSubmit').hide();
        });

        $('#fecha_desde').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            endDate: '0d'   // no se puede seleccionar una fecha después de hoy
        });

        $('#fecha_hasta').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            endDate: '0d'   // no se puede seleccionar una fecha después de hoy
        });

    });
</script>
@endsection