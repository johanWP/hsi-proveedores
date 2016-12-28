@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')

@endsection

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="error-page">
            <h2 class="headline text-yellow"> 404</h2>
            <div class="error-content">
                <h3><i class="fa fa-warning text-yellow"></i> Oops! La página no se encontró.</h3>
                <p>
                    La página que busca no existe en este sitio, compruebe la dirección, use el menú de navegación o
                    reporte el problmea al área de Sistemas del Jockey Club A.C.
                    Mientras, puede <a href='{{ url('/home') }}'>regresar al inicio</a>.
                </p>
            </div><!-- /.error-content -->
        </div><!-- /.error-page -->
    </div>
@endsection