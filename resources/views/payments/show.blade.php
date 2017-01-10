@extends('adminlte::layouts.app')

@section('htmlheader_title')
    Detalle de pago
@endsection

@section('contentheader_title')
Número de Pago {{ $pago[0]->NUMEROPAGO }}
@endsection

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    @foreach ( $cheques as $cheque )
                        @if ($loop->first)
                            <h3>Cheques</h3>
                            <table class="table table-condensed table-striped">
                                <thead>
                                <tr>
                                    <th><p><strong>Fecha: </strong></p></th>
                                    <th><p><strong>Número de Cheque: </strong></p></th>
                                    <th><p><strong>A Nombre de: </strong></p></th>
                                    <th><p><strong>Monto: </strong></p></th>
                                </tr>
                                </thead>
                                <tbody>
                        @endif

                    <tr>
                        <td class="col-sm-3">
                            <p>{{ $cheque->FECHACHEQUE }}</p>
                        </td>

                        <td class="col-sm-3">
                            <p>{{ $cheque->NUMERCOCHEQUE }}</p>
                        </td>


                        <td class="col-sm-4">
                            <p>{{ $cheque->NOMBREBENEFICIARIO }}</p>
                        </td>

                        <td class="col-sm-2">
                            <p>${{ $cheque->MONTOCHEQUE }}</p>
                        </td>
                    </tr>
                    @if ($loop->last)
                            </tbody>
                        </table>
                    @endif

                    @endforeach

                    @foreach( $transferencias as $transferencia )

                        @if ($loop->first)
                            <h3>Transferencias</h3>
                            <table class="table table-condensed table-striped">
                                <thead>
                                <tr>
                                    <th><p><strong>Banco: </strong></p></th>
                                    <th><p><strong>Sucursal: </strong></p></th>
                                    <th><p><strong>Número de Cuenta: </strong></p></th>
                                    <th><p><strong>Número de Comprobante: </strong></p></th>
                                    <th><p><strong>Monto: </strong></p></th>
                                </tr>
                                </thead>
                                <tbody>
                        @endif
                                    <tr>
                                        <td class="col-sm-2">
                                            <p>{{ ucwords( strtolower($transferencia->BANCOTRANSFERENCIA) ) }}</p>
                                        </td>

                                        <td class="col-sm-2">
                                            <p>{{ ucwords(strtolower($transferencia->SUCURSALTRANSFERENCIA)) }}</p>

                                        </td>

                                        <td class="col-sm-2">
                                            <p>{{ $transferencia->NUMEROCUENTA }}</p>
                                        </td>


                                        <td class="col-sm-3">
                                            <p>{{ $transferencia->NUMEROCOMPROBANTE }}</p>
                                        </td>

                                        <td class="col-sm-3">
                                            <p>${{ $transferencia->MONTOTRANSFERENCIA }}</p>
                                        </td>
                                    </tr>
                        @if ($loop->last)
                                </tbody>
                            </table>
                        @endif

                    @endforeach

                    @foreach ($retenciones as $retencion)

                        @if ($loop->first)
                            <h3>Retenciones</h3>
                                <table class="table table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th><p><strong>Número de Retención:</strong></p></th>
                                            <th><p><strong>Tipo de Retención: </strong></p></th>
                                            <th><p><strong>Monto:</strong></p></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        @endif
                                    <tr>
                                        <td class="col-sm-4">
                                            <p>{{ $retencion->NUMERORETENCION }}</p>
                                        </td>
                                        <td class="col-sm-6">
                                            <p>{{ ucwords( strtolower($retencion->TIPORETENCION) ) }}</p>
                                        </td>
                                        <td class="col-sm-2">
                                            <p>${{ $retencion->MONTORETENCION }}</p>
                                        </td>

                                    </tr>
                        @if ($loop->last)
                                    </tbody>
                                </table>
                        @endif
                    @endforeach

                    @foreach( $comprobantes as $comprobante )

                    @if ($loop->first)
                        <h3>Comprobantes</h3>
                        <table class="table table-condensed table-striped">
                            <thead>
                            <tr>
                                <th><p><strong>Número de Comprobante:</strong></p></th>
                                <th><p><strong>Monto del Comprobante:</strong></p></th>
                                <th><p><strong>Deuda del Comprobante:</strong></p></th>
                            </tr>
                            </thead>
                            <tbody>

                    @endif
                    <tr>
                        <td class="col-sm-4">
                            <p>{{ $comprobante->NUMEROCOMPROBANTE }}</p>

                        </td>
                        <td class="col-sm-5">
                            <p>${{ $comprobante->TOTALCOMPROBANTE }}</p>
                        </td>


                        <td class="col-sm-3">
                            <p>${{ $comprobante->DEUDACOMPROBANTE }}</p>
                        </td>

                    </tr>
                    @if ($loop->last)
                            </tbody>
                        </table>
                    @endif

                        @endforeach
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-default" id="btnBack" onclick="javascript:history.back()">
                            <i class="fa fa-arrow-left"></i> Volver
                        </button>
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