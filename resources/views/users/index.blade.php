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
                        <div class="table-responsivex">
                            <table class="table" id="users-table">
                                <thead>
                                <tr>
                                    <th>CUIT</th>
                                    <th>Razón Social</th>
                                    <th>Email</th>
                                    <th>Ver Detalle</th>
                                </tr>
                                </thead>
                                <tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('otros-scripts')
{{--    <link rel="stylesheet" href="{{ asset('/plugins/datatables/jquery.dataTables.css') }}">--}}
{{--    <link rel="stylesheet" href="{{ asset('/plugins/datatables/jquery.dataTables_themeroller.css') }}">--}}
<script src="{{ asset('/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('/plugins/datatables/dataTables.bootstrap.js') }}"></script>
<script>
    $(function() {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/api/usuarios',
            columns: [
                { data: 'cuit', name: 'cuit' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' }
            ],
            columnDefs: [{
                "targets": [3],
                "mData": null,
                "sortable": false,
                "searchable": false,
                "mRender": function(data, type, full) {
                    var botones;
//                    botones = '<a href="/usuarios/' + data['id'] + '" class="btn btn-sm btn-primary">Ver Pagos</a> ';
                    botones = '<a href="/pagos/' + data['id'] + '" class="btn btn-sm btn-primary">Ver Pagos</a> ';
                    botones = botones + ' <a href="/usuarios/' + data['id'] + '" class="btn btn-sm btn-default">Ver Perfil</a>';
                    return  botones;
                }
            }]
        });
    });

</script>
@endsection