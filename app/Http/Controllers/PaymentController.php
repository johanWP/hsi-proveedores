<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\User;
use App\Http\Traits\FormatFirebirdTrait;

class PaymentController extends Controller
{
    use FormatFirebirdTrait;

    /**
     * Muestra el listado completo de pagos del sistema incluyendo a todos los proveedores
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $param = '';
        $header = 'Mis Pagos';
        return view('payments.index', compact('param', 'header'));
    }


    
    /**
     * Devuelve un Datatable con todos los pagos registrados.  Los datos se envían por AJAX desde el método
     * anyData con el parámetro 'all' enviado desde la vista
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function VerTodos()
    {
        return view('payments.all');
    }

    /**
     * 
     * @param $prov_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function PorProveedor($prov_id)
    {
        $param = (int)$prov_id;
        $header = 'Pagos de ' . $this->PonerGuionesAlCuit($prov_id);
        return view('payments.index', compact('param', 'header'));
    }

    /**
     * @param Integer $numeroPago
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($numeroPago)
    {
        $numeroPago = (int)$numeroPago;
        $query = "SELECT * FROM web_detpagoproveedores_jockey WHERE numeropago = '" . $numeroPago . "'";
        $pago = collect(DB::connection('firebird')->select( $query ));
        if ( $pago->count() > 0 )
        {
            $temp = $this->FormatearDetalleDePago($pago);
            $pago = collect($temp);

            $transferencias = $pago->filter(function ($value, $key) {
                return ($value->MONTOTRANSFERENCIA > 0 AND $value->CUIT != '0');
            })->unique('MONTOTRANSFERENCIA');

            $cheques = $pago->filter(function ($value, $key){
                return $value->MONTOCHEQUE != null AND $value->CUIT != '0';
            })->unique('NUMERCOCHEQUE');

            $retenciones = $pago->filter(function($value, $key){
                return $value->MONTORETENCION > 0;
            });

            $comprobantes = $pago->filter(function($value, $key) {
                return $value->NUMEROCOMPROBANTE != '0';
            })->sortBy('NUMEROCOMPROBANTE');
            return view('payments.show', compact('pago', 'transferencias', 'cheques', 'retenciones', 'comprobantes'));
        } else {
            return view('errors.404');
        }
    }

    /**
     * @param User|null $user
     * @return mixed
     */
    public function anyData($param = null)
    {

        if(is_null($param))
        {
            // Si no envía un parámetro, muestro las facturas del usuario logueado
            $cuit = $this->PonerGuionesAlCuit(Auth::user()->cuit);
            $query = "SELECT 
                    a.numeroPago, a.cuit, b.razonSocial, 
                    a.fechaCheque, a.numeroRetencion, a.montoRetencion, a.numeroComprobante, a.fechaPago, 
                    b.fechaImputable, b.total
                FROM
                    web_detPagoProveedores_jockey a 
                JOIN web_movimientosPorProveedor b 
                ON a.numeroComprobante = b.numeroComprobante
                WHERE a.cuit = '" . $cuit . "' 
                ORDER BY a.numeroPago DESC";
        } elseif($param == 'all') {
            // Con el parametro ' all'  muestro todas las facturas
            $query = "SELECT 
                    a.numeroPago, a.cuit, b.razonSocial, 
                    a.fechaCheque, a.numeroRetencion, a.montoRetencion, a.numeroComprobante, a.fechaPago, 
                    b.fechaImputable, b.total
                FROM
                    web_detPagoProveedores_jockey a 
                JOIN web_movimientosPorProveedor b 
                ON a.numeroComprobante = b.numeroComprobante
                ORDER BY a.numeroPago DESC";

        } else {
            $param = (int)$param;
            $cuit = $this->PonerGuionesAlCuit($param);
            $query = "SELECT * FROM web_detpagoproveedores_jockey 
                WHERE 
                cuit = '" . $cuit . "'
                AND
                (montoCheque IS NOT NULL OR efectivo > 0 OR montoTransferencia > 0)
                ORDER BY numeropago";
        }
        $filas = $this->FormatearDetalleDePago( DB::connection('firebird')->select($query) );
        return Datatables::of(collect($filas))->make(true);
    }


    public function ApiShow(Int $numeroPago)
    {
        $numeroPago = (int)$numeroPago;
        $query = "SELECT * FROM web_detpagoproveedores_jockey WHERE numeropago = '" . $numeroPago . "'";
        $pago = collect(DB::connection('firebird')->select( $query ));
        if ($pago->count() > 0)
        {
            $temp = $this->FormatearDetalleDePago($pago);
            $pago = collect($temp);

            $transferencias = $pago->filter(function ($value, $key) {
                return ($value->MONTOTRANSFERENCIA > 0 AND $value->CUIT != '0');
            })->unique('MONTOTRANSFERENCIA');

            $cheques = $pago->filter(function ($value, $key){
                return $value->MONTOCHEQUE != null AND $value->CUIT != '0';
            })->unique('NUMERCOCHEQUE');

            $retenciones = $pago->filter(function($value, $key){
                return $value->MONTORETENCION > 0;
            });

            $comprobantes = $pago->filter(function($value, $key) {
                return $value->NUMEROCOMPROBANTE != '0';
            })->sortBy('NUMEROCOMPROBANTE');


//            $detPago['pago'] = $pago;
            $detPago['transferencias'] = $transferencias;
            $detPago['cheques'] = $cheques->flatten();
            $detPago['retenciones'] = $retenciones;
            $detPago['comprobantes'] = $comprobantes;
//dd($cheques->flatten());
            return json_encode($detPago);
        } else {
            return response('pagoNoExiste', 404);
        }
    }

}
