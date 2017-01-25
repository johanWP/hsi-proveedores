@extends('adminlte::layouts.app')

@section('htmlheader_title')
    Administrar Permisos
@endsection

@section('contentheader_title')
    Administrar Permisos
@endsection

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                   {!! Form::open(['url' => '/permisos/', 'method' => 'POST', 'class' => 'form-inline']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Permiso: ') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nombre del permiso']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', 'Descripción: ') !!}
                        {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' => 'Describa el permiso']) !!}
                    </div>
                    {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    @foreach($permisos as $permiso)
                        @if($loop->first)
                            <div class="table-responsive">
                            <table class="table" id="permission-table">
                                <thead>
                                <tr>
                                    <th>Permiso</th>
                                    <th>Descripción</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                        @endif
                                <tr>
                                    <td>{{ $permiso->name }}</td>
                                    <td>{{ $permiso->description }}</td>
                                    <td class="text-center">
                                        {!! Form::open(['url' => '/permisos/'.$permiso->id, 'method' => 'DELETE']) !!}
                                        {!! Form::hidden('id', $permiso->id) !!}
                                        <button class="btn btn-danger" type="button" data-id="{!! $permiso->id !!}">
                                            <i class="fa fa-trash"></i> Borrar
                                        </button>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                        @if($loop->last)
                                </tbody>
                            </table>
                            </div>
                        @endif
                    @endforeach

                    @if($permisos->count() == 0)
                        <p class="text-center text-muted">Todavía no hay permisos cargados</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{--Fin del row/--}}
</div>
@endsection

@section('otros-scripts')
    <script src="/plugins/sweetalert/sweetalert.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/plugins/sweetalert/sweetalert.css">
    <script>
        $(document).ready(function() {
            $('.btn-danger').on('click', function(){
                var id = $(this).data('id');
                var boton = $(this);
                swal({
                    title: "¿Estás seguro de borrar el permiso?",
                    text: "Esta acción no puede deshacerse",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Si, borralo",
                    closeOnConfirm: false
                },
                function(){
                    boton.closest('form').submit();
//                    swal("Deleted!", "Your imaginary file has been deleted.", "success");
                });
            });
        });
    </script>
@endsection