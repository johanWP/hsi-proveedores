@extends('adminlte::layouts.app')

@section('htmlheader_title')
    Editar Rol: {{ $rol->name }}
@endsection

@section('contentheader_title')
    Editar Rol: {{ $rol->name }}
@endsection

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    @foreach($permisos as $permiso)
                        @if($loop->first)
                            {!! Form::open(['url' => '/roles/'.$rol->id, 'method' => 'PUT']) !!}
{{--                            {!! Form::hidden('id', $rol->id) !!}--}}
                            <div class="table-responsive">
                                <table class="table table-hover" id="roles-table">
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
                                            @if($rol->hasPermissionTo($permiso->name))
                                                <?php $valor = true ?>
                                            @else
                                                <?php $valor = null ?>
                                            @endif
                                            {!! Form::checkbox($permiso->name, 1, $valor) !!}
                                        </td>
                                    </tr>
                                    @if($loop->last)
                                        <tr>
                                            <td colspan="3">
                                                <a href="javascript:window.history.back()" class="btn btn-default">
                                                    <i class="fa fa-arrow-left"></i> Volver
                                                </a>
                                                {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                {!! Form::close() !!}
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
                            title: "¿Estás seguro de borrar el rol?",
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