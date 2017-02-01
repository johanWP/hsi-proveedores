<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class BancoGaliciaController extends Controller
{
    /**
     * Devuelve la vista para solicitar la generación del archivo de transferencias
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('bancos.galicia.index');
    }

    /**
     * Devuelve una vista con el enlace para descargar el archivo de transferencias recién generado
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function generarArchivoTransferencias()
    {
        // inicializar variables
        $archivo = '/galicia/' . uniqid() . '.txt';
        $fullPath = storage_path() . $archivo;
        $contador = 0;
        $cantidadTransferencias = 0;
        $pd = array();
        $email ='';
        $o1 = '';
        $fc = array();

        if (!file_exists($fullPath)) {
            $query = "SELECT 
                        *
                    FROM web_detPagoProveedores_jockey 
                    WHERE montoTransferencia IS NOT NULL
                        AND montoTransferencia > 0 
                        AND cuit != '  -        -'
                        AND bancoTransferencia = 'GALICIA'
                    ORDER BY numeroPago, cuit DESC";
            $filas = collect( DB::connection('firebird')->select($query) );

            foreach ($filas as $fila)
            {
                $contador++;
                if ( $fila->CUIT != 0 and ! is_null($fila->CUIT) )
                {
                    $proveedor = $this->datosProveedor($fila->CUIT);
                    if (! empty($proveedor->CBU) )
                    {
                        // pago con transferencia
                        $pd = $this->crearLineaPD($fullPath, $fila, $proveedor, $contador); // esto es un arreglo

                        // email
                        $email = $this->crearLineaME($fullPath, $proveedor);
                        // Orden de pago
                        $o1 = $this->crearLineaO1($fullPath, $fila, $filas);
                        $fc = $this->crearLineaFC($fullPath, $fila, $filas); // esto es un arreglo
                    }

                } else {
                    $c1 = $this->crearLineaC1($fullPath, $fila, $proveedor); // comprobante de retencion
                }
            }


            if ($cantidadTransferencias > 0)
            {
                $bloque = $pd . $email . $o1;
                foreach ($fc as $factura)
                {
                    $bloque .= $factura;
                }
            }
            $pc = $this->crearLineaPC($pd);
            $result = $this->escribirLinea($fullPath, $pc);
            $result = $this->escribirLinea($fullPath, $bloque);
        }
        return view('bancos.galicia.transferencias', compact('archivo'));
    }


    /**
     * Crea la cabecera del archivo de transferencias para Banco Galicia
     * @param Array $pd Arreglo asociativo con el detalle de la tranasferencia y el monto
     * @return mixed Cantidad de bytes escritos en el archivo, False si hubo un error
     */
    private function crearLineaPC(Array $pd)
    {
        $codigoRegistro = 'PC';
        $tipoLista = 'T';
        $idLista = 1;  //TODO: buscar en la BD el numero que corresponde
        $idLista = 'EE' . $this->formatearConCeros($idLista, 6);
        $fechaProceso = Carbon::today()->format('dmY');
        $numCuenta = 'MMFFFFFFFDCCCE';
        $razonSocial = $this->formatearConEspacios('Jockey Club A C', 40);
        $cantidadPagos = $this->formatearConCeros($pd->count(), 6); //contar en el Firebird
        $importePagos = $this->formatearConCeros( $pd->sum('monto'), 17);   // en centavos
        $sucursal = $this->formatearConEspacios('000', 57);    //  000 siempre que son transferencias
        $moneda = $this->formatearConEspacios('001', 151);  // 001 = pesos arg

        $cabecera  = $codigoRegistro . $tipoLista . $idLista . $fechaProceso . $numCuenta;
        $cabecera .= $razonSocial . $cantidadPagos .$importePagos . $fechaProceso . $fechaProceso;
        $cabecera .= $sucursal . $moneda . "\n";

        return $cabecera;
    }

    /**
     * Crea la linea de detalle para un pago por transferencia
     * @param String $fullPath Ruta completa del archivo que se va generando
     * @param Collection $fila
     * @param $proveedor
     * @param $numeroRegistro
     * @return Collection $arr Asociativo con dos columnas: texto y monto en centavos
     */
    private function crearLineaPD($fullPath, $fila, $proveedor, $numeroRegistro)
    {
        $transf = '';
        $arr = array();

        if ( isset($proveedor->CBU) && !is_null($proveedor->CBU) )
        {
            $codigoRegistro = 'PD';
            $numeroRegistro = $this->formatearConCeros($numeroRegistro, 6);
            $importe = $fila->MONTOTRANSFERENCIA * 100;  // El monto debe ser en centavos
            $importe = $this->formatearConCeros($importe, 17);
            $moneda = '001';
            $cbu = $this->formatearConCeros($proveedor->CBU, 22);
            $fechaPago = Carbon::today()->format('dmY');

            $razonSocial = utf8_encode($this->formatearConEspacios(ucwords(strtolower($proveedor->RAZONSOCIAL)), 50));

            $direccion = utf8_encode($this->formatearConEspacios($proveedor->DIRECCION, 30));
            $localidad = utf8_encode($this->formatearConEspacios($proveedor->LOCALIDAD, 20));
            $codigoPostal = $this->formatearConEspacios($proveedor->CP, 6);
            $telefono = str_replace('-', '', $proveedor->TELEFONO);
            $telefono = $this->formatearConEspacios($telefono, 15);
            $cuit = str_replace('-', '', $fila->CUIT);
            $cuit = $this->formatearConCeros($cuit, 11);
            $ordenPago = $this->formatearConEspacios((int)$fila->NUMEROPAGO, 35);
            $conceptoPago = '01';
            $destinoComprobantes = $this->formatearConEspacios('02', 93);

            $transf = $codigoRegistro . $numeroRegistro . $importe . $moneda . $cbu . $fechaPago . $razonSocial;
            $transf .= $direccion . $localidad . $codigoPostal . $telefono . $cuit;
            $transf .= $ordenPago . $conceptoPago . $destinoComprobantes. "\n";

            $arr[] = ['texto' => $transf, 'monto' => $importe];
//            $bytes_written = file_put_contents($fullPath, $transf, FILE_APPEND);
        }
        $arr = collect($arr);

        return $arr;
    }


    /**
     * @param String $fullPath Ruta completa del archivo que se esta escribiendo
     * @param Collection $prov Contiene datos del proveedor
     * @return String
     */
    private function crearLineaME(String $fullPath, Collection $prov)
    {
        $lineaME = '';
        $codigoRegistro = 'ME';
        $arrProv = $prov->toArray();
        $validator = Validator::make($arrProv, [
            'EMAIL' => 'required|email',
        ]);

        if ( ! $validator->fails() ) {
            $email = $this->formatearConEspacios($prov->EMAIL, 320);
            $lineaME = $codigoRegistro . $email . "\n";
//            $bytes_written = file_put_contents($fullPath, $linea, FILE_APPEND);
        }

        return $lineaME;
    }

    /**
     * Crea la línea O1 si existen para las primeras diez retenciones y
     * agrega opcionalmente una línea O2 si existen mas de diez
     * @param String $fullPath
     * @param Collection $fila
     * @param Collection $filas
     */
    private function crearLineaO1($fullPath, Collection $fila, Collection $filas)
    {
        $linea = '';
        $codigoRegistro = 'O1';
        $numeroPago = (int)$fila->NUMEROPAGO;
        $numeroPago = $this->formatearConCeros($numeroPago, 10);
        $importeTotal = $fila->MONTORETENCION + $fila->MONTOTRANSFERENCIA * 100; // en centavos
        $importeTotal = $this->formatearConCeros($importeTotal, 17);
        $notasDebito = $this->formatearConCeros('0', 17);
        $notasCredito = $this->formatearConCeros('0', 17);
        $importePagar = $this->formatearConCeros($fila->MONTOTRANSFERENCIA *100, 17);
        $signo = '0';
        $condicionPago = $this->formatearConEspacios(' ', 30);

        $retenciones = $filas->filter(function($pago) use ($fila) {
            return ($fila->NUMEROPAGO == $pago->NUMEROPAGO) && ($pago->CUIT == '0');
        });
        $concepto_importe = '';
        // Divido la collection de retenciones en collections más pequeñas, cada una de 10 retenciones
        $retencionesFraccionadas = $retenciones->chunk(10);

        if ( ! is_null($retencionesFraccionadas) && $retencionesFraccionadas->count() > 0 )
        {
            $retenciones = $retencionesFraccionadas->first()->toArray();
        }
        $i = 0;
        foreach ($retenciones as $retencion)
        {
            $i++;
            $tipoRetencion = $retencion->TIPORETENCION;
            $montoRetencion = $retencion->MONTORETENCION;
            if ( str_contains($tipoRetencion, 'GANANCIAS') )
            {
                $concepto = '03';
            } elseif ( str_contains($retencion->TIPORETENCION, 'IVA') ) {
                $concepto = '04';
            } elseif ( str_contains($retencion->TIPORETENCION, 'SUSS') ) {
                $concepto = '07';
            } else {
                $concepto = '11';
            }

            $importe = (int)($montoRetencion * 100); // en centavos
            $importe = $this->formatearConCeros($importe, 17);
            $concepto_importe .= $concepto . $importe;
        }
        
        $relleno = $this->formatearConCeros('0', 19);
        while ($i < 10)
        {
            $i++;
            $concepto_importe .= $relleno;
        }

        // Si hay más de 10 retenciones, habrá más de 1 pedazo en $retencionesFraccionadas
        if ($retencionesFraccionadas->count() > 1)
        {
            // aqui hago la línea O2
        }


        $espacio = $this->formatearConEspacios(' ', 19);
        
        $linea =  $codigoRegistro . $numeroPago . $importeTotal . $notasCredito . $notasDebito;
        $linea .= $importePagar . $signo . $condicionPago . $concepto_importe . $espacio . "\n";
//        $bytes_written = file_put_contents($fullPath, $linea, FILE_APPEND);
        return $linea;
    }

    /**
     * Crea las líneas FC, que corresponden a las facturas que se pagan con la transferencia actual
     * @param String $fullPath
     * @param Collection $fila
     * @param Collection $filas
     * @return Collection $arrFc Arreglo con todas las lineas a escribir
     */
    private function crearLineaFC($fullPath, Collection $fila, Collection $filas)
    {
        $arrFc = array();
        $codigoRegistro = 'FC';
        $num = 0;
        $tipoDocumento = 'FC';
        $relleno = $this->formatearConCeros('0', 115) . $this->formatearConEspacios(' ', 97);
        $facturas = $filas->filter(function ($pago) use ($fila) {
            return ($fila->NUMEROPAGO == $pago->NUMEROPAGO) && ($pago->CUIT != '0');
        });

        $concepto = $this->formatearConEspacios(' ', 30);
        $fechaEmision = Carbon::today()->format('dmY');
        $baseImponible = $facturas->sum('TOTALCOMPROBANTE') * 100;
        foreach ($facturas as $factura)
        {
            $num++;
            $numRegistro        = $this->formatearConCeros($num, 3);
            $numComprobante     = (int)$factura->NUMEROCOMPROBANTE;
            $numComprobante     = $this->formatearConCeros($numComprobante, 12);
            $importeComprobante = $this->formatearConCeros($factura->TOTALCOMPROBANTE * 100, 17);
            $importeRetenido    = $this->formatearConCeros($factura->MONTORETENCION * 100, 17);
            $baseImponible      = $this->formatearConCeros($baseImponible, 17);
            $linea  = $codigoRegistro . $numRegistro . $tipoDocumento;
            $linea .= $numComprobante . $concepto . $fechaEmision;
            $linea .= $importeComprobante . $importeRetenido . $baseImponible . $relleno . "\n";

            $arrFc[] = $linea;
//            $bytes_written = file_put_contents($fullPath, $linea, FILE_APPEND);
            $linea = '';
        }
        return collect($arrFc);
    }

    /**
     * Crea la línea para el comprobante de retencion
     * @param $fullPath
     * @param Collection $fila
     * @param Collection $proveedor
     * @return String
     */
    private function crearLineaC1($fullPath, $fila,  $proveedor)
    {
        $codigoRegistro = 'C1';
        $tipoRetencion = '';
        switch ($fila->TIPORETENCION) {
            case 'RET GANANCIAS VENTA DE BIENES DE CAMBIO RI':
                // no break
            case 'RET GANANCIAS LOCACION OBRA Y SERVICIOS':
                // no break
            case 'RET GANANCIAS FACTURA M':
                $tipoRetencion = '02';
                break;
            case 'SERVICIOS DE SEGURIDAD SOCIAL SUSS':

                break;
            case 'RET IVA LOCACION Y PRESTACION SERVICIOS':
                // no break
            case 'RET IVA VENTA DE BIENES':
                // no break
            case 'RET IVA TASA REDUCIDA 10,5% ':
                $tipoRetencion = '01';
                break;

        }
        $tipoImpuesto = '1';
        $textoResolucion = $this->formatearConEspacios(' ', 20);
        $codigoPartido = '0137';
        $tituloRetencion = $this->formatearConEspacios(' ', 30);
        $centroEmisor = 'XXXX';
        $numeroCertificado = '1234567890';
        $numAgenteRetencion = $this->formatearConEspacios('X123456789X', 20);
        $condicionImpuesto = 'XX';
        ( empty($proveedor->IIBB) ) ? $iibb = ' ' : $iibb = $proveedor->IIBB;
//        ( is_null($proveedor->IIBB) ) ? $iibb = ' ' : $iibb = $proveedor->IIBB;
        $iibb = $this->formatearConEspacios($iibb, 20);
        // dd($proveedor);
        $tipoComprobante = '02';
        $numComprobante = (int)$fila->NUMEROCOMPROBANTE;
        $numComprobante = $this->formatearConEspacios($numComprobante, 35);
        $fechaRetencion = Carbon::today()->format('dmY');
        $importeRetenido = $fila->MONTORETENCION * 100; // en centavos
        $importeRetenido = $this->formatearConCeros($importeRetenido, 17);
        $declaracionJurada = 'MMYYYY';
        $espacio = $this->formatearConEspacios(' ', 137);
        $linea  = $codigoRegistro . $tipoRetencion . $textoResolucion . $tipoImpuesto;
        $linea .= $codigoPartido .  $tituloRetencion . $centroEmisor . $numeroCertificado;
        $linea .= $numAgenteRetencion . $condicionImpuesto . $iibb . $tipoComprobante . $numComprobante;
        $linea .= $fechaRetencion . $importeRetenido . $declaracionJurada . $espacio . "\n";

//        $bytes_written = file_put_contents($fullPath, $linea, FILE_APPEND);
        return $linea;
    }


    /**
     * Completa con espacios hasta que $str tenga el largo especificado en $espacios o recorta si es más largo
     * @param $str String original
     * @param $espacios Integer Largo final del $str
     * @param $orden Integer En qué lugar se agregan los espacios, por defecto del lado derecho
     * @return string
     */
    private function formatearConEspacios($str, $espacios, $orden = STR_PAD_RIGHT)
    {
        is_null($str) ? $str = '' : false;
        if ( strlen($str) > $espacios )
        {
            $str = substr($str, 0, $espacios);
        } else {
            $str = str_pad($str, $espacios, " ", $orden);
        }
        return $str;
    }

    /**
     * Completa con ceros hasta llegar al largo especificado
     * @param $str String original
     * @param $espacios Integer Largo final que tendrá el string procesado
     * @return string
     */
    private function formatearConCeros($str, $espacios)
    {
        if ( strlen($str) > $espacios )
        {
            // remuevo los caracteres sobrantes contando desde el final
            $str = substr($str, ( strlen($str) * -1 ), $espacios);
        } else {
            $str = str_pad($str, $espacios, "0", STR_PAD_LEFT);
        }
        return $str;
    }

    /**
     * Devuelve una collection con los datos de un proveedor
     * @param String $cuit con guiones
     * @return bool|Collection $datos False si no se encuentra, o una collection con la info
     */
    private function datosProveedor(String $cuit)
    {
        $query = "SELECT * FROM web_proveedores_jockey WHERE cuit = '" . $cuit . "'";
        $datos = collect( DB::connection('firebird')->select($query) )->first();
        if (count($datos) <= 0)
        {
            $datos = false;
        }
        return collect($datos);
    }

    private function escribirLinea($fullPath, $texto)
    {
        $bytes_written = file_put_contents($fullPath, $texto, FILE_APPEND);

        if ($bytes_written)
        {
            die('Ocurrió un error al escribir el archivo: ' . $texto);
        }
        return $bytes_written;
    }
    
    /**
     * @param $archivo
     * @return mixed
     */
    public function descargarArchivo($archivo)
    {
        // Reviso si el archivo existe
        $file_path = storage_path('/galicia/' . $archivo);
        if (file_exists($file_path)) {
            // Enviar la descarga
            return Response::download($file_path, $archivo, [
                'Content-Length: ' . filesize($file_path)
            ]);
        } else {
            // Error
            exit('El archivo no existe.');
        }
    }

}
