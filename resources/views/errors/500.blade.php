@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')

@endsection

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="error-page">
            <h2 class="headline text-yellow"> 500</h2>
            <div class="error-content">
                <h3><i class="fa fa-warning text-yellow"></i> Oops! Ocurrió un error en el sistema..</h3>

                <p>
                    Mientras, puede <a href='{{ url('/home') }}'>regresar al inicio</a>.
                </p>
            </div><!-- /.error-content -->
        </div><!-- /.error-page -->
    </div>
@endsection