@extends('adminlte::layouts.auth')

@section('htmlheader_title')
    Oops!
@endsection

@section('content')

<body>
<div class="login-logo">
    <a href="{{ url('/home') }}"><img src="/img/logo-Jockey.png" alt="Jockey Club AC"></a>
</div><!-- /.login-logo -->

<div class="error-page">
    <h2 class="headline text-yellow"> 404</h2>
    <div class="error-content">
        <h3><i class="fa fa-warning text-yellow"></i> Oops! {{ trans('adminlte_lang::message.pagenotfound') }}.</h3>
        <p>
            {{ trans('adminlte_lang::message.notfindpage') }}
        </p>
    </div><!-- /.error-content -->
</div><!-- /.error-page -->
    {{--<div id="app">--}}
        {{--<div class="register-box">--}}
            {{--<div class="register-logo">--}}
                {{--<a href="{{ url('/home') }}"><img src="/img/logo-Jockey.png" alt="Jockey Club AC"></a>--}}
            {{--</div>--}}

            {{--@if (count($errors) > 0)--}}
                {{--<div class="alert alert-danger">--}}
                    {{--<strong>Whoops!</strong> {{ trans('adminlte_lang::message.someproblems') }}<br><br>--}}
                    {{--<ul>--}}
                        {{--@foreach ($errors->all() as $error)--}}
                            {{--<li>{{ $error }}</li>--}}
                        {{--@endforeach--}}
                    {{--</ul>--}}
                {{--</div>--}}
            {{--@endif--}}

            {{--<div class="register-box-body">--}}
                {{--<p class="login-box-msg">{{ trans('adminlte_lang::message.registermember') }}</p>--}}
                {{--<form action="{{ url('/register') }}" method="post">--}}
                    {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
                    {{--<div class="form-group has-feedback">--}}
                        {{--<input type="text" class="form-control" placeholder="{{ trans('adminlte_lang::message.fullname') }}" name="name" value="{{ old('name') }}"/>--}}
                        {{--<span class="glyphicon glyphicon-user form-control-feedback"></span>--}}
                    {{--</div>--}}
                    {{--<div class="form-group has-feedback">--}}
                        {{--<input type="email" class="form-control" placeholder="{{ trans('adminlte_lang::message.email') }}" name="email" value="{{ old('email') }}"/>--}}
                        {{--<span class="glyphicon glyphicon-envelope form-control-feedback"></span>--}}
                    {{--</div>--}}
                    {{--<div class="form-group has-feedback">--}}
                        {{--<input type="password" class="form-control" placeholder="{{ trans('adminlte_lang::message.password') }}" name="password"/>--}}
                        {{--<span class="glyphicon glyphicon-lock form-control-feedback"></span>--}}
                    {{--</div>--}}
                    {{--<div class="form-group has-feedback">--}}
                        {{--<input type="password" class="form-control" placeholder="{{ trans('adminlte_lang::message.retrypepassword') }}" name="password_confirmation"/>--}}
                        {{--<span class="glyphicon glyphicon-log-in form-control-feedback"></span>--}}
                    {{--</div>--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-xs-1">--}}
                            {{--<label>--}}
                                {{--<div class="checkbox_register icheck">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" name="terms">--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--</label>--}}
                        {{--</div><!-- /.col -->--}}
                        {{--<div class="col-xs-6">--}}
                            {{--<div class="form-group">--}}
                                {{--<button type="button" class="btn btn-block btn-flat" data-toggle="modal" data-target="#termsModal">{{ trans('adminlte_lang::message.terms') }}</button>--}}
                            {{--</div>--}}
                        {{--</div><!-- /.col -->--}}
                        {{--<div class="col-xs-4 col-xs-push-1">--}}
                            {{--<button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('adminlte_lang::message.register') }}</button>--}}
                        {{--</div><!-- /.col -->--}}
                    {{--</div>--}}
                {{--</form>--}}

                {{--@include('adminlte::auth.partials.social_login')--}}

                {{--<a href="{{ url('/login') }}" class="text-center">{{ trans('adminlte_lang::message.membreship') }}</a>--}}
            {{--</div><!-- /.form-box -->--}}
        {{--</div><!-- /.register-box -->--}}
    {{--</div>--}}

    {{--@include('adminlte::layouts.partials.scripts_auth')--}}

    {{--@include('adminlte::auth.terms')--}}

    {{--<script>--}}
        {{--$(function () {--}}
            {{--$('input').iCheck({--}}
                {{--checkboxClass: 'icheckbox_square-blue',--}}
                {{--radioClass: 'iradio_square-blue',--}}
                {{--increaseArea: '20%' // optional--}}
            {{--});--}}
        {{--});--}}
    {{--</script>--}}

</body>

@endsection
