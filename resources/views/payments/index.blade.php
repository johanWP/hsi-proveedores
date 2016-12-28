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
                    <div class="panel-body">
                        {{--<div class="table-responsive">--}}
                            <table class="table" id="payments-table">
                                <thead>
                                <tr>
                                    <th>CUIT Proveedor</th>
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
    @php
        $id = '';
        if( isset($prov) )
        {
            $id = $prov->id;
        }
    @endphp
    <link rel="stylesheet" href="{{ asset('/plugins/datatables/jquery.dataTables.css') }}">
    {{--    <link rel="stylesheet" href="{{ asset('/plugins/datatables/jquery.dataTables_themeroller.css') }}">--}}
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/dataTables.bootstrap.js') }}"></script>
    <script>
        $(function() {
            $('#payments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/api/pagos/{!! $id !!}',
                columns: [
                    { data: 'CUIT', name: 'CUIT' },
                    { data: 'NUMEROPAGO', name: 'NUMEROPAGO' },
                    { data: 'NUMEROCOMPROBANTE', name: 'NUMEROCOMPROBANTE' },
                    { data: 'TOTALCOMPROBANTE', name: 'TOTALCOMPROBANTE' },
                    { data: 'DEUDACOMPROBANTE', name: 'DEUDACOMPROBANTE' }
                ],
                columnDefs: [{
                    "targets": [5],
                    "mData": null,
                    "sortable": false,
                    "searchable": false,
                    "mRender": function(data, type, full) {
                        var botones;
                        botones = '<a href="/pagos/ver/' + data['NUMEROPAGO'] + '" class="btn btn-sm btn-primary">Ver Detalle</a>';
//                        botones = botones + '<a href="/usuarios/' + data['id'] + '" class="btn btn-sm btn-default">Ver Perfil</a>';
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