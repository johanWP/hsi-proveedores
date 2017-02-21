@extends('adminlte::layouts.app')

@section('htmlheader_title')
    Detalle de Factura {{ (int)$factura->NUMEROCOMPROBANTE }}
@endsection

@section('contentheader_title')
    Detalle de Factura {{ (int)$factura->NUMEROCOMPROBANTE }}
@endsection

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4>CUIT</h4>
                            <p>{{ $factura->CUIT }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h4>Razón Social</h4>
                            <p>{{ $factura->RAZONSOCIAL }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <h4>Fecha del Comprobante</h4>
                            <p>{{ Carbon\Carbon::parse($factura->FECHACOMPROBANTE)->format('d-m-Y') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h4>Fecha Imputable</h4>
                            <p>{{ Carbon\Carbon::parse($factura->FECHAIMPUTABLE)->format('d-m-Y') }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <h4>Tipo de Comprobante</h4>
                            <p>{{ $factura->TIPOCOMPROBANTE }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h4>Total</h4>
                            <p>${{ number_format($factura->TOTAL, 2, ',', '.' ) }}</p>
                        </div>
                    </div>

                </div> {{-- Panel body--}}
            </div> {{-- Panel--}}
        </div> {{-- col-sm-12 --}}
    </div>
@endsection

@section('otros-scripts')
    <script>
        $(function() {

        });

    </script>
@endsection