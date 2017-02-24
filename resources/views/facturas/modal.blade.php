{{--
<!-- Modal -->
<div class="modal fade" id="modalFactura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Detalles del comprobante <span id="numComprobante"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <h4>Fecha del Comprobante</h4>
                        <p id="fechaComprobante">--</p>
                    </div>
                    <div class="col-sm-6">
                        <h4>Fecha Imputable</h4>
                        <p id="fechaImputable">--</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <h4>Tipo de Comprobante</h4>
                        <p id="tipoComprobante">--</p>
                    </div>
                    <div class="col-sm-6">
                        <h4>Total</h4>
                        <p id="total">--</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>--}}

<!-- Modal -->
<div class="modal fade bs-example-modal-lg" id="modalPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Detalles del Pago <span id="numPago"></span></h4>
            </div>
            <div class="modal-body">
                <h3 name="cheques">Cheques</h3>

                <table class="table table-condensed table-striped" id="tablaCheques" name="cheques">
                    <thead>
                    <tr>
                        <th><p><strong>Fecha: </strong></p></th>
                        <th><p><strong>Número de Cheque: </strong></p></th>
                        <th><p><strong>A Nombre de: </strong></p></th>
                        <th><p><strong>Monto: </strong></p></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="trVacioCheque">
                        <td class="col-sm-3">
                            <p>--</p>
                        </td>
                        <td class="col-sm-3">
                            <p>--</p>
                        </td>
                        <td class="col-sm-4">
                            <p>--</p>
                        </td>
                        <td class="col-sm-2">
                            <p>--</p>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <h3 name="transferencias">Transferencias</h3>
                <table class="table table-condensed table-striped" name="transferencias" id="tablaTransferencias">
                    <thead>
                    <tr>
                        <th><p><strong>Banco de origen: </strong></p></th>
                        <th><p><strong>Fecha de transferencia: </strong></p></th>
                        <th><p><strong>Monto: </strong></p></th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr id="trVacioTransferencia">
                        <td class="col-sm-4">
                            <p>--</p>
                        </td>
                        <td class="col-sm-4">
                            <p>--</p>
                        </td>
                        <td class="col-sm-4">
                            <p>--</p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h3 name="retenciones">Retenciones</h3>
                <table class="table table-condensed table-striped" name="retenciones" id="tablaRetenciones">
                    <thead>
                    <tr>
                        <th><p><strong>Número de Retención:</strong></p></th>
                        <th><p><strong>Tipo de Retención: </strong></p></th>
                        <th><p><strong>Monto:</strong></p></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="trVacioRetenciones">
                        <td class="col-sm-4">
                            <p>--</p>
                        </td>
                        <td class="col-sm-6">
                            <p>--</p>
                        </td>
                        <td class="col-sm-2">
                            <p>--</p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>