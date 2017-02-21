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
                        <div class="col-sm-6">
                            <a class="btn btn-default" href="javascript:window.history.back()"><i class="fa fa-arrow-left"></i> Volver</a>

                        </div>
                        <div class="col-sm-6 text-right">
                            Descargar <a href="/banco{{ $archivo }}">Archivo.</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('otros-scripts')
    <script>
        $(function() {

        });

    </script>
@endsection