
@extends('adminlte::layouts.app')

@section('htmlheader_title')
	{{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')

@endsection

@section('main-content')
	@if(Auth::user()->hasRole('proveedor') == '')
		<script type="text/javascript">
			window.location = "{{ url('/facturas') }}";
		</script>
	@endif
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">Home!!!</div>

					<div class="panel-body">
						{{ trans('adminlte_lang::message.logged') }}
						>> {{  Auth::user()->hasRole('proveedor')}}. <<
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
