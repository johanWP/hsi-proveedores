<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Http\Traits\FormatFirebirdTrait;

class FacturaController extends Controller
{
    use FormatFirebirdTrait;

    /**
     * Devuelve una vista con el datatable de facturas
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $param = '';
        $header = 'Mis Facturas';
        $cuit = '';
        $user = Auth::user();
        if($user->hasPermissionTo('ver_pagos_todos'))
        {
            $param = 'all';
            $header = 'Todas las Facturas';
            $query = 'SELECT cuit, razonSocial from web_proveedores_jockey';
            $proveedores = collect(DB::connection('firebird')->select($query))->pluck('RAZONSOCIAL', 'CUIT')->toArray();
        }
        return view('facturas.index', compact('param', 'header', 'proveedores'));
    }


    /**
     * Muestra el detalle de una factura
     * @param String $cuit el cuit del proveedor con guiones
     * @param Int $numComprobante el número de la factura
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($cuit, $numComprobante)
    {
        $numComprobante = (int)$numComprobante;
        $cuit = strip_tags($cuit);
        $query = "SELECT * from web_movimientosPorProveedor WHERE numeroComprobante = " . $numComprobante ." 
            AND cuit = '" . $cuit . "'";
        $factura = collect(DB::connection('firebird')->select($query))->first();
//        $factura = $this->FormatearDetalleDePago($factura);
        return view('facturas.show', compact('factura'));
    }

    /**
     * Muestra el JSON que toma el datatable para mostrar las facturas de un proveedor
     * @param null|String $param   El cuit del proveedor con guiones 
     * @return JSON 
     */
    public function anyData($param = null)
    {

        if(is_null($param))
        {
            // Si no envía un parámetro, muestro las facturas del usuario logueado
            $facturas = collect([]);

        } elseif($param == 'all') {
            // Con el parametro ' all'  muestro todas las facturas??

        } else {
            $cuit = strip_tags($param);
            $query = "SELECT * FROM web_movimientosPorProveedor WHERE cuit = '" . $cuit . "'";
            $facturas = collect(DB::connection('firebird')->select($query));

            $query = "SELECT * FROM web_detPagoProveedores_jockey WHERE cuit = '" . $cuit . "'
            AND (montoCheque IS NOT NULL OR montoTransferencia > 0)";
            $pagos = collect(DB::connection('firebird')->select($query));

            foreach($facturas as $factura)
            {
                $pago = $pagos->where('NUMEROCOMPROBANTE', $factura->NUMEROCOMPROBANTE)->first();

                if ((!isset($pago->NUMEROPAGO))) {
                    $factura->NUMEROPAGO = null;
                } else {
                    $factura->NUMEROPAGO = (int)$pago->NUMEROPAGO;
                }
            }

        }
        return Datatables::of($this->FormatearDetalleDePago($facturas))->make(true);
    }

    public function apiFacturas($cuit, $numComprobante)
    {
        $numComprobante = (int)$numComprobante;
        $cuit = strip_tags($cuit);
        $query = "SELECT * from web_movimientosPorProveedor WHERE numeroComprobante = " . $numComprobante ." 
            AND cuit = '" . $cuit . "'";
        $factura = collect(DB::connection('firebird')->select($query))->first();
        $factura->FECHAIMPUTABLE = Carbon::parse($factura->FECHAIMPUTABLE)->format('d-m-Y');
        $factura->FECHACOMPROBANTE = Carbon::parse($factura->FECHACOMPROBANTE)->format('d-m-Y');
        $factura->TOTAL = number_format( (float)$factura->TOTAL, 2, ',', '.' );
        return json_encode($factura);
    }

}
