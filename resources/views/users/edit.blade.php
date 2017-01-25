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

                    @foreach($permisos as $permiso)
                        @if($loop->first)
                            {!! Form::open(['url' => '/usuarios/' . $user->id, 'method' => 'PATCH', 'class' => 'form-inline']) !!}
                            <table class="table table-hover">
                        @endif
                                <tr>
                                    <td>
                                        {{ $permiso->name }}
                                    </td>
                                    <td>
                                        {{--<div class="form-group">--}}
                                            @if($user->hasPermissionTo($permiso->name))
                                                <?php $valor = true ?>
                                            @else
                                                <?php $valor = null ?>
                                            @endif
                                            {!! Form::checkbox($permiso->name, 1, $valor) !!}
                                        {{--</div>--}}
                                    </td>
                                </tr>

                        @if($loop->last)
                            <tr>
                                <td colspan="2">
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