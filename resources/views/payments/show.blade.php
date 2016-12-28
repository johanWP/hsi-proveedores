@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
Número de Pago {{ $pago[0]->NUMEROPAGO }}
    <?PHP // dd($pago)?>
@endsection

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @foreach ( $pago as $cheque )
                        @if ( ($cheque->TIPORETENCION == null) AND ($cheque->MONTOCHEQUE != null) )
                            @if ($loop->first)
                                <h3>Cheques</h3>
                            @endif

                        <div class="row">
                            <div class="col-sm-2">
                                <p><strong>Fecha: </strong></p>
                                <p>
                                    @if (is_null($cheque->FECHACHEQUE))
                                    --
                                    @else
                                    {{ $cheque->FECHACHEQUE }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-sm-2">
                                <p><strong>Número de Cheque:</strong></p>
                                <p>
                                    @if (is_null($cheque->NUMERCOCHEQUE))
                                    --
                                    @else
                                    {{ $cheque->NUMERCOCHEQUE }}
                                    @endif
                                </p>

                            </div>
                            <div class="col-sm-2">
                                <p><strong>Monto:</strong></p>
                                <p>
                                    @if (is_null($cheque->MONTOCHEQUE))
                                    --
                                    @else
                                    {{ $cheque->MONTOCHEQUE }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p><strong>A Nombre de:</strong></p>
                                <p>
                                    @if (is_null($cheque->NOMBREBENEFICIARIO))
                                    --
                                    @else
                                    {{ $cheque->NOMBREBENEFICIARIO }}
                                    @endif
                                </p>

                            </div>
                        </div>
                        @endif
                        @endforeach

                        @foreach( $transferencias as $transferencia )

                        @if ( ($transferencia->TIPORETENCION == null) AND ($transferencia->MONTOTRANSFERENCIA > 0) )
                            @if ($loop->first)
                                <h3>Transferencias</h3>
                            @endif
                        @if ($loop->last)
                            <hr>
                        @endif
                        <div class="row">
                            <div class="col-sm-2">
                                <p><strong>Banco: </strong></p>
                                <p>
                                    @if (is_null($transferencia->BANCOTRANSFERENCIA))
                                    --
                                    @else
                                    {{ ucwords( strtolower($transferencia->BANCOTRANSFERENCIA) ) }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-sm-2">
                                <p><strong>Sucursal:</strong></p>
                                <p>
                                    @if (is_null($transferencia->SUCURSALTRANSFERENCIA))
                                    --
                                    @else
                                    {{ $transferencia->SUCURSALTRANSFERENCIA }}
                                    @endif
                                </p>

                            </div>
                            <div class="col-sm-2">
                                <p><strong>Número de Cuenta:</strong></p>
                                <p>
                                    @if (is_null($transferencia->NUMEROCUENTA))
                                    --
                                    @else
                                    {{ $transferencia->NUMEROCUENTA }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-sm-3">
                                <p><strong>Monto:</strong></p>
                                <p>
                                    @if (is_null($transferencia->MONTOTRANSFERENCIA))
                                    --
                                    @else
                                    {{ $transferencia->MONTOTRANSFERENCIA }}
                                    @endif
                                </p>

                            </div>
                            <div class="col-sm-3">
                                <p><strong>Número de Comprobante:</strong></p>
                                <p>
                                    @if (is_null($transferencia->NUMEROCOMPROBANTE))
                                    --
                                    @else
                                    {{ $transferencia->NUMEROCOMPROBANTE }}
                                    @endif
                                </p>

                            </div>
                        </div>
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
        $(function() {

        });

    </script>
@endsection