@extends('adminlte::layouts.auth')

@section('htmlheader_title')
    Restablecer contraseña
@endsection

@section('content')

<body class="login-page">
    <div id="app">

        <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/home') }}"><img src="/img/logo-Jockey.png" alt="Jockey Club AC"></a>
        </div><!-- /.login-logo -->

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> {{ trans('adminlte_lang::message.someproblems') }}<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="login-box-body">
            <p class="login-box-msg">Restablecer Contraseña</p>
            <form action="{{ url('/password/email') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}"/>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="row">
                    <!--<div class="col-xs-2">
                    </div> /.col -->
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('adminlte_lang::message.sendpassword') }}</button>
                    </div><!-- /.col -->
                    <!--<div class="col-xs-2">
                    </div> /.col -->
                </div>
            </form>

            <p><br><a href="{{ url('/login') }}">Ingresar al sistema</a><br>
                <a href="{{ url('/Sistema Proveedores - Manual de ingreso v1.pdf') }}">Ayuda</a>

            </p>
            {{--<a href="{{ url('/register') }}" class="text-center">{{ trans('adminlte_lang::message.registermember') }}</a>--}}

        </div><!-- /.login-box-body -->

    </div><!-- /.login-box -->
    </div>

    @include('adminlte::layouts.partials.scripts_auth')

    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
</body>

@endsection
