@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
    {{ $header }}
@endsection

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        {{--<div class="table-responsive">--}}
                            <table class="table" id="payments-table">
                                <thead>
                                <tr>
                                    {{--<th>CUIT Proveedor</th>--}}
                                    <th>Número de Pago</th>
                                    <th>Comprobante</th>
                                    <th>Total Comprobante</th>
                                    <th>Deuda Comprobante</th>
                                    <th>Ver Detalle</th>
                                </tr>
                                </thead>
                                <tbody>

                            </table>
                        {{--</div>--}}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('otros-scripts')

    <link rel="stylesheet" href="{{ asset('/plugins/datatables/jquery.dataTables.css') }}">
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/dataTables.bootstrap.js') }}"></script>
    <script>
        $(function() {

            $('#payments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/api/pagos/{!! $param !!}',
                columns: [
//                    { data: 'CUIT', name: 'CUIT' },
                    { data: 'NUMEROPAGO', name: 'NUMEROPAGO' },
                    { data: 'NUMEROCOMPROBANTE', name: 'NUMEROCOMPROBANTE' },
                    { data: 'TOTALCOMPROBANTE', name: 'TOTALCOMPROBANTE' },
                    { data: 'DEUDACOMPROBANTE', name: 'DEUDACOMPROBANTE' }
                ],
                columnDefs: [{
                    "targets": [4],
                    "mData": null,
                    "sortable": false,
                    "searchable": false,
                    "mRender": function(data, type, full) {
                        var botones;
                        botones = '<a href="/pagos/detalle/' + data['NUMEROPAGO'] + '" class="btn btn-sm btn-primary">Ver Detalle</a>';
                        return  botones;

                    }
                }]
            });
        });

    </script>
    <style>
        #payments-table td { text-align: right }
    </style>
@endsection