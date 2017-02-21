@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ $header }}
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
                        @include('facturas.modal')
                        @if(Auth::user()->hasPermissionTo('ver_pagos_todos'))
                        {{ Form::open(['url' => '/facturas', 'class' => 'form-inline', 'id' => 'frmCuit' ]) }}
                            <div class="form-group">
                                {{ Form::label('cuit', 'Razón social del Proveedor: ') }}
                                {{ Form::select('cuit', $proveedores, null,
                                    ['style'=>'width:300px', 'placeholder' => 'Seleccione...', 'class' => 'form-control'])
                                }}
                            </div>
                        {{ Form::submit('Buscar', ['class' => 'btn btn-default']) }}
                        {{ Form::close() }}
                        <hr>
                        @endif
                        <table class="table" id="facturas-table">
                            <thead>
                            <tr>
                                <th>CUIT Proveedor</th>
                                <th>Razón Social</th>
                                <th>Número de Factura</th>
                                <th style="text-align: right">Total Comprobante</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
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
    <script src="/plugins/select2/select2.min.js"></script>
    <link rel="stylesheet" href="/plugins/select2/select2.min.css">

    <link rel="stylesheet" href="{{ asset('/plugins/datatables/jquery.dataTables.css') }}">
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/dataTables.bootstrap.js') }}"></script>
    <script>
        $(function() {
            $('#cuit').select2();
            var table = $('#facturas-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/api/facturas/',
                "language": {
                    processing: "Espera...",
                    search: "Buscar:&nbsp;",
                    info:   "Mostrando registros _START_ a _END_ de _TOTAL_ en total",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Ultimo"
                    }
                },
                columns: [
                    { data: 'CUIT', name: 'CUIT' },
                    { data: 'RAZONSOCIAL', name: 'RAZONSOCIAL' },
                    { data: 'NUMEROCOMPROBANTE', name: 'NUMEROCOMPROBANTE' },
                    { data: 'TOTAL', name: 'TOTAL' }
                ],
                columnDefs: [{
                    "targets": [4],
                    "mData": null,
                    "sortable": false,
                    "searchable": false,
                    "mRender": function(data) {
                        var botones;
                        botones = '<button type="button" data-toggle="modal" data-target="#modalFactura"' +
                                ' class="btn btn-sm btn-default" data-cuit="'+ data['CUIT'] +
                                '" data-numcomprobante="'+ data['NUMEROCOMPROBANTE'] +
                                '" data-fechaimputable="'+ data['FECHAIMPUTABLE'] +
                                '" data-fechacomprobante="'+ data['FECHACOMPROBANTE'] +
                                '" data-tipocomprobante="'+ data['TIPOCOMPROBANTE'] +
                                '" data-total  ="'+ data['TOTAL'] +
                                '">Ver factura</button>';

                        return  botones;
                    }
                },
                    {
                        "targets": [5],
                        "mData": null,
                        "sortable": false,
                        "searchable": false,
                        "mRender": function(data) {
                            var botones;
                            var pago = '';
                            if(data['NUMEROPAGO'] != null)
                            {
                                pago = ' <button type="button" data-toggle="modal" data-target="#modalPago"' +
                                        'data-pago="' + data['NUMEROPAGO'] +
                                        '" class="btn btn-sm btn-primary">Ver Pago</button>';
                            }
                            return  pago;
                        }
                    }]
            });


/** Cada vez que se postea el form, se actualiza la tabla **/
            $('#frmCuit').submit(function(e){
                var cuit = $('#cuit').val();
                e.preventDefault();
                if(cuit) {
                    table.ajax.url( '/api/facturas/' + cuit ).load();
                }
            });


            $('#modalFactura').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var cuit = button.data('cuit'); // Extract info from data-* attributes
                var numComprobante = button.data('numcomprobante'); // Extract info from data-* attributes
                var fechaImputable = button.data('fechaimputable'); // Extract info from data-* attributes
                var fechaComprobante = button.data('fechacomprobante'); // Extract info from data-* attributes
                var tipoComprobante = button.data('tipocomprobante'); // Extract info from data-* attributes
                var total = button.data('total'); // Extract info from data-* attributes
                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                var modal = $(this);
                modal.find('#numComprobante').text(numComprobante);
                modal.find('#fechaImputable').text(fechaImputable);
                modal.find('#fechaComprobante').text(fechaComprobante);
                modal.find('#tipoComprobante').text(tipoComprobante);
                modal.find('#total').text('$ ' + total);
            });

            $('#modalPago').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var numPago = button.data('pago'); // Extract info from data-* attributes
                $('#numPago').text(numPago);
                $.ajax('/api/pagos/' + numPago, {
                    dataType: 'json',
                    success: function(data) {
                        if (Object.keys(data.cheques).length > 0) {
                            $('h3[name="cheques"]').show();
                            $('table[name="cheques"]').show();
                            $('#tablaCheques tbody tr').remove();
                            $.each(data.cheques, function (key, value){
                                trCheques = '<tr>' +
                                        '<td class="col-sm-3">' +
                                        '<p>' + value.FECHACHEQUE + '</p> </td>' +
                                        '<td class="col-sm-3">' +
                                        '<p>' + value.NUMERCOCHEQUE + '</p> </td>' +
                                        '<td class="col-sm-3">' +
                                        '<p>' + value.NOMBREBENEFICIARIO + '</p> </td>' +
                                        '<td class="col-sm-3">' +
                                        '<p class="text-right">$' + value.MONTOCHEQUE + '</p> </td>' +
                                        '</tr>';

                                $('#tablaCheques tbody').append(trCheques);
                            });
                        } else {
                            $('h3[name="cheques"]').hide()
                            $('table[name="cheques"]').hide()
                        }

                        if (Object.keys(data.transferencias).length > 0) {
                            $('h3[name="transferencias"]').show();
                            $('table[name="transferencias"]').show();
                            $('#tablaTransferencias tbody tr').remove();
                            $.each(data.transferencias, function (key, value){
                                trTransferencias = '<tr>' +
                                        '<td class="col-sm-4">' +
                                        '<p>' + value.BANCOTRANSFERENCIA + '</p> </td>' +
                                        '<td class="col-sm-4">' +
                                        '<p>' + value.FECHAPAGO + '</p> </td>' +
                                        '<td class="col-sm-4">' +
                                        '<p class="text-right">$' + value.MONTOTRANSFERENCIA + '</p> </td>' +
                                        '</tr>';

                                $('#tablaTransferencias tbody').append(trTransferencias);
                            });
                        } else {
                            $('h3[name="transferencias"]').hide();
                            $('table[name="transferencias"]').hide();
                        }


                        if (Object.keys(data.retenciones).length > 0) {
                            $('h3[name="retenciones"]').show();
                            $('table[name="retenciones"]').show();
                            $('#tablaRetenciones tbody tr').remove();
                            $.each(data.retenciones, function (key, value){
                                trRetenciones = '<tr>' +
                                        '<td class="col-sm-4">' +
                                        '<p>' + value.NUMERORETENCION + '</p> </td>' +
                                        '<td class="col-sm-4">' +
                                        '<p>' + value.TIPORETENCION + '</p> </td>' +
                                        '<td class="col-sm-4">' +
                                        '<p class="text-right">$' + value.MONTORETENCION + '</p> </td>' +
                                        '</tr>';

                                $('#tablaRetenciones tbody').append(trRetenciones);
                            });
                        } else {
                            $('h3[name="retenciones"]').hide();
                            $('table[name="retenciones"]').hide();
                        }

                    },
                    error: function() {
                        modal.find('.modal-body').html('<h4>Hubo un error al cargar el pago.  ' +
                                'Por favor, recargue la pagina e intente de nuevo</h4>');
                    }
                });

                var modal = $(this);



            })

        });

    </script>
    <style>
        #facturas-table td { text-align: right }
    </style>
@endsection