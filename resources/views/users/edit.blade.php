@extends('adminlte::layouts.app')

@section('htmlheader_title')
    Administrar Permisos de {{ $user->name }}
@endsection

@section('contentheader_title')
    Administrar Permisos de {{ $user->name }}
@endsection

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <h3>Datos Registrados.</h3>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted" style="margin-top: 1.8em">Edite estos datos usando Flexxus Proveedores.</p>

                        </div>
                    </div>


                    <div class="col-sm-6">
                        <p><strong>CUIT:</strong></p>
                        <p>{{ $user->cuit }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Nombre:</strong></p>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>E-Mail:</strong></p>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Ultima Actualización:</strong></p>
                        <p>{{ $user->updated_at }}</p>
                    </div>
                    <h3>Roles</h3>

                    @foreach($roles as $role)
                        @if($loop->first)
                            {!! Form::open(['url' => '/usuarios/' . $user->id, 'method' => 'PATCH', 'class' => '']) !!}
                            <table class="table table-hover">
                        @endif
                                <tr>
                                    <td>
                                        {{ $role->name }}
                                    </td>
                                    <td>
                                        {{ $role->description }}
                                    </td>
                                    <td>
                                            @if($user->hasRole($role->name))
                                                <?php $valor = $role->name ?>
                                            @else
                                                <?php $valor = null ?>
                                            @endif
                                            {!! Form::checkbox($role->name, 1, $valor) !!}
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
                            </table>
                            {!! Form::close() !!}
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('otros-scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection