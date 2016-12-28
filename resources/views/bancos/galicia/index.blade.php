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
                        <div class="col-sm-12" id="divSubmit">
                            {{  Form::open() }}
                            <p>{{ \Carbon\Carbon::today()->format('d-M-Y') }}</p>
                            <button class="btn btn-primary" id="btnSubmit">Crear Archivo</button>
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
<script>
    $(function() {
//        $('#loading').hide();
        $('#btnSubmit').on('click', function(){
//            $('#loading').show();
            $('#loading').attr('style', '');
            $('#divSubmit').hide();
        });
    });
</script>
@endsection