@extends('adminlte::layouts.app')

@section('htmlheader_title')
	{{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')

@endsection

@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">Home!!!</div>

					<div class="panel-body">
						{{ trans('adminlte_lang::message.logged') }}
						{{ Auth::user()->can('dar_permisos') }}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
