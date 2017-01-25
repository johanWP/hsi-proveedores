@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')

@endsection

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="error-page">
            <h2 class="headline text-yellow"> 403</h2>
            <div class="error-content">
                <h3><i class="fa fa-warning text-yellow"></i> Oops! No tienes permisos para ver esa página.</h3>
                <p>Si piensas que es un error, puedes reportarlo al área de Sistemas del Jockey Club A.C.
                    Mientras, <a href='{{ url('/home') }}'>regresa al inicio</a>.
                </p>
            </div><!-- /.error-content -->
        </div><!-- /.error-page -->
    </div>
@endsection