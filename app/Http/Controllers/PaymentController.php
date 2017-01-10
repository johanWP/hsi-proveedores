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
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($id = null )
    {
        $prov = null;
        if (! is_null( $id ) )
        {
            $prov = User::find($id);
        }
        return view('payments.index', compact('prov'));
    }


    /**
     * @param Integer $numeroPago
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($numeroPago )
    {
        $temp = [];
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
    public function anyData(User $user = null )
    {
        if ( !is_null($user->cuit) )
        {
            $cuit = $this->PonerGuionesAlCuit( $user->cuit );
            $query = "SELECT * FROM web_detpagoproveedores_jockey WHERE cuit = '" . $cuit . "'";
        } else
        {
            $query = "SELECT * FROM web_detpagoproveedores_jockey 
                  WHERE 
                    cuit <> '0' 
                    AND 
                    cuit <> '  -        -'
                    AND
                    (montoCheque IS NOT NULL OR efectivo > 0 OR montoTransferencia > 0)
                  ORDER BY numeropago";
        }

        $filas = $this->FormatearDetalleDePago( DB::connection('firebird')->select( $query ) );
        return Datatables::of(
            collect($filas)
        )->make(true);
    }


}
