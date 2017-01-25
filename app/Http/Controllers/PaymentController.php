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
        return view('payments.index', compact('param'));
    }

    public function VerTodos()
    {
        return view('payments.all');
    }

    public function PorProveedor($prov_id)
    {
        $param = (int)$prov_id;
        return view('payments.index', compact('param'));
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
    public function anyData($param= null )
    {

        if(is_null($param))
        {
            // Si no envía un parámetro, muestro los pagos del usuario logueado
            $cuit = $this->PonerGuionesAlCuit(Auth::user()->cuit);
            $query = "SELECT * FROM web_detpagoproveedores_jockey 
                WHERE 
                cuit = '" . $cuit . "'
                AND
                (montoCheque IS NOT NULL OR efectivo > 0 OR montoTransferencia > 0)
                ORDER BY numeropago"; //dd($query);
        } elseif($param == 'all') {
            // Muestro
            $query = "SELECT * FROM web_detpagoproveedores_jockey 
                WHERE 
                cuit <> '0' 
                AND 
                cuit <> '  -        -'
                AND
                (montoCheque IS NOT NULL OR efectivo > 0 OR montoTransferencia > 0)
                ORDER BY numeropago";

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


}
