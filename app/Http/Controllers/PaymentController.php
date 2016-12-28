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
    
    public function index( $id = null )
    {
        $prov = null;
        if (! is_null( $id ) )
        {
            $prov = User::find($id);
        }
        return view('payments.index', compact('prov'));
    }
    

    public function show( $numeroPago )
    {
        $temp = [];
        $query = "SELECT * FROM web_detpagoproveedores_jockey WHERE numeropago = '" . $numeroPago . "'";
        $pago = collect(DB::connection('firebird')->select( $query ));
        if ( $pago->count() > 0 )
        {
            $temp = $this->FormatearDetalleDePago($pago);
            $pago = collect($temp);

            $transferencias = $pago->filter( function ($value, $key) {
                return ($value->MONTOTRANSFERENCIA != null);
            } );
//            dd($transferencias);
            return view('payments.show', compact('pago', 'transferencias'));
        } else {
            return view('errors.404');
        }
    }
    public function anyData( User $user = null )
    {
        if ( !is_null($user->cuit) )
        {
            $cuit = $this->PonerGuionesAlCuit( $user->cuit );
            $query = "SELECT * FROM web_detpagoproveedores_jockey WHERE cuit = '" . $cuit . "'";
        } else
        {
            $query = "SELECT * FROM web_detpagoproveedores_jockey WHERE cuit <> '0'";
        }

        $filas = $this->FormatearDetalleDePago( DB::connection('firebird')->select( $query ) );
        return Datatables::of(
            collect( $filas )
        )->make( true );
    }


}
