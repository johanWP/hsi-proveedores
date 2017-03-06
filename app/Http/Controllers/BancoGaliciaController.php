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
        $pd = array();
        $me = '';
        $o1 = '';
        $lineaPago = '';
        $fc = array();

        if (!file_exists($fullPath)) {
            $file = fopen($fullPath, "w") or die("No se puedo crear el archivo.");
            // Busco todos los pagos y retenciones
            $query = "SELECT FIRST 10
                p.numeroPago,
                dp.razonSocial,
                dp.cbu,
                dp.cuit, dp.razonsocial, dp.cbu, dp.direccion, dp.barrio, dp.provincia, dp.localidad, dp.cp,                
                dp.email, dp.condicionIva, dp.iibb, dp.agenteIibb, dp.telefono,
                p.numeroComprobante, p.montoTransferencia, p.deudaComprobante,
                f.tipoComprobante,  f.fechaComprobante, f.fechaImputable,
                f.total AS TotalComprobante
            FROM
                web_proveedores_jockey dp INNER JOIN
                web_movimientosporproveedor f ON dp.cuit = f.cuit INNER JOIN
                web_detPagoProveedores_jockey p ON p.cuit = f.cuit 
                AND p.numeroComprobante = f.numeroComprobante
            WHERE
                p.montoTransferencia > 0
                AND p.bancoTransferencia = 'GALICIA'
                AND dp.cbu IS NOT NULL
            ORDER BY p.numeroPago DESC";

            $filas = collect(DB::connection('firebird')->select($query));

            $pagos = $filas->unique('NUMEROPAGO');


            $pc = $this->crearLineaPC($pagos);
            $lineaPago = $pc;
            fwrite($file, $pc);
//            $this->escribirLinea($fullPath, $pc);
            foreach ($pagos as $pago)  // v2
            {
                $contador++;
                $fc = '';
                $o1 = '';
                $me = '';
                $lineaC1 = '';
                $lineaC2 = '';
                $retenciones = '';

                $comprobantes = $filas->where('NUMEROPAGO', $pago->NUMEROPAGO)->unique('NUMEROCOMPROBANTE');
                $facturas = $filas->where('NUMEROPAGO', $pago->NUMEROPAGO)
                    ->whereIn('TIPOCOMPROBANTE', ['FA', 'FC', 'FM', 'FE']);
                $notasCredito = $filas->where('NUMEROPAGO', $pago->NUMEROPAGO)
                    ->whereIn('TIPOCOMPROBANTE', ['NCA', 'NCC']);

                $notasDebito = $filas->where('NUMEROPAGO', $pago->NUMEROPAGO)
                    ->whereIn('TIPOCOMPROBANTE', ['NDA', 'NDC']);

                $queryRetenciones = 'SELECT * FROM web_detPagoProveedores_jockey 
                    WHERE numeroPago = ' . (int)$pago->NUMEROPAGO . ' AND TipoRetencion IS NOT NULL AND MontoRetencion > 0';
                $retenciones = collect(DB::connection('firebird')->select($queryRetenciones));

                $pd = $this->crearLineaPD($pago, $contador);

                $o1 = $this->crearLineaO1($facturas, $notasCredito, $notasDebito);

                // Esto es un arreglo porque las facturas deben ir numeradas correlativamente
                $lineasFacturas = $this->crearLineaFC($comprobantes, $retenciones);
                foreach ($lineasFacturas as $lineaFc) {
                    $fc .= $lineaFc;
                }

                $email = $filas->where('NUMEROPAGO', $pago->NUMEROPAGO)->where('EMAIL', '!=', '');
                if ($email->count() > 0) {
                    $me = $this->crearLineaME(['EMAIL' => $email->first()->EMAIL]);
                }

                fwrite($file, $pd); //  Detalle para un pago con transferencia
                fwrite($file, $o1); //  Registro de Orden de pago
                fwrite($file, $fc); //  Registro de documento (Facturas pagadas)
                $lineaC1 = $this->crearLineaC1($pago, $retenciones);
                $lineaC2 = $this->crearLineaC2($retenciones);

                if (count($lineaC1) > 0) {
                    try {
                        foreach ($lineaC1 as $c1) {
                            fwrite($file, $c1); //  Comprobantes de retencion

                        }

                    } catch (\ErrorException $e) {
                        print_r(count($lineaC1));
                        print_r($pago);
                        print_r($lineaC1);
                        die($e);

                    }
                }
                fwrite($file, $me); //  Dirección de email del proveedor

            }
            fclose($file);
        }
        return view('bancos.galicia.transferencias', compact('archivo'));
    }


    /**
     * Crea la cabecera del archivo de transferencias para Banco Galicia
     * @param Colelction Arreglo asociativo con el detalle de la tranasferencia y el monto
     * @return String Línea con datos para escribir al archivo
     */
    private function crearLineaPC(\Illuminate\Support\Collection $pd)
    {
        $codigoRegistro = 'PC';
        $tipoLista = 'T';
        $idLista = $this->formatearConCeros('1', 6);  //TODO: buscar en la BD el numero que corresponde
        $idLista = 'J2' . $idLista;
        $fechaProceso = Carbon::today()->format('dmY');
        $numCuenta = '00007893499996';
        $razonSocial = $this->formatearConEspacios('Jockey Club A C', 40);
        $cantidadPagos = $this->formatearConCeros($pd->count(), 6);
        $importePagos = $pd->sum('MONTOTRANSFERENCIA') * 100;
        $importePagos = $this->formatearConCeros($importePagos, 17);   // en centavos
        $sucursal = $this->formatearConEspacios('000', 57);    //  000 siempre que son transferencias
        $moneda = $this->formatearConEspacios('001', 151);  // 001 = pesos arg

        $cabecera = $codigoRegistro . $tipoLista . $idLista . $fechaProceso . $numCuenta;
        $cabecera .= $razonSocial . $cantidadPagos . $importePagos . $fechaProceso . $fechaProceso;
        $cabecera .= $sucursal . $moneda;

        return $cabecera . "\r\n";
    }

    /**
     * Crea la linea de detalle para un pago por transferencia
     * @param \Illuminate\Support\Collection $fila
     * @param $proveedor
     * @param $numeroRegistro
     * @return Collection $arr Asociativo con dos columnas: texto y monto en centavos
     */
    private function crearLineaPD($fila, $numeroRegistro)
    {
        $transf = '';
        $codigoRegistro = 'PD';

        $numeroRegistro = $this->formatearConCeros($numeroRegistro, 6);
        $importe = $fila->MONTOTRANSFERENCIA * 100;  // El monto debe ser en centavos
        $importe = $this->formatearConCeros($importe, 17);
        $moneda = '001';
        $cbu = $this->formatearConCeros($fila->CBU, 22);
        $fechaPago = Carbon::today()->format('dmY');

        $razonSocial = utf8_encode($this->formatearConEspacios(ucwords(strtolower($fila->RAZONSOCIAL)), 50));

        $direccion = utf8_encode($this->formatearConEspacios($fila->DIRECCION, 30));
        $localidad = utf8_encode($this->formatearConEspacios($fila->LOCALIDAD, 20));
        $codigoPostal = $this->formatearConEspacios($fila->CP, 6);
        $telefono = str_replace('-', '', $fila->TELEFONO);
        $telefono = $this->formatearConEspacios($telefono, 15);
        $cuit = str_replace('-', '', $fila->CUIT);
        $cuit = $this->formatearConCeros($cuit, 11);
        $ordenPago = $this->formatearConEspacios((int)$fila->NUMEROPAGO, 35);
        $conceptoPago = '01';
        $destinoComprobantes = $this->formatearConEspacios('02', 93);

        $transf = $codigoRegistro . $numeroRegistro . $importe . $moneda . $cbu . $fechaPago . $razonSocial;
        $transf .= $direccion . $localidad . $codigoPostal . $telefono . $cuit;
        $transf .= $ordenPago . $conceptoPago . $destinoComprobantes . "\r\n";

        return $transf;
    }


    /**
     * @param String $fullPath Ruta completa del archivo que se esta escribiendo
     * @param Collection $prov Contiene datos del proveedor
     * @return String
     */
    private function crearLineaME($pago)
    {
        $lineaME = '';
        $codigoRegistro = 'ME';
        $arrPago = collect($pago)->toArray();   // esta vuelta es innecesaria, mejorar
        $validator = Validator::make($arrPago, [
            'EMAIL' => 'required|email',
        ]);

        if (!$validator->fails()) {
            $email = $this->formatearConEspacios($arrPago['EMAIL'], 318);
            $lineaME = $codigoRegistro . $email . "\r\n";
        }

        return $lineaME;
    }

    /**
     * Crea la línea O1 para las primeras diez facturas y
     * @param Collection $facturas
     * @param Collection $retenciones
     * @return String $linea Linea a escrbir
     */
    private function crearLineaO1($facturas, $notasCredito, $notasDebito)
    {
        $linea = "\r\n";

        $totalFC = 0;
        $totalNC = 0;
        $totalND = 0;
        $concepto_importe = '';
        $codigoRegistro = 'O1';
        $numeroPago = (int)$facturas->first()->NUMEROPAGO;
        $numeroPago = $this->formatearConCeros($numeroPago, 10);

//          Las facturas que tienen retenciones aparecen dos veces en el query,
//        filtro para eliminar las duplicadas
        $facturas = $facturas->unique('NUMEROCOMPROBANTE');

        foreach ($facturas as $factura) {
            $totalFC = $totalFC + $factura->TOTALCOMPROBANTE - $factura->DEUDACOMPROBANTE;
        }

        foreach ($notasCredito as $notaCredito) {
            $totalNC = ($totalNC + $notaCredito->TOTALCOMPROBANTE) * -1;
        }

        foreach ($notasDebito as $notaDebito) {
            $totalND = $totalND + $notaDebito->TOTALCOMPROBANTE;
        }

//        $importeTotal = ($retenciones->sum('MONTORETENCION') + $facturas->sum('MONTOTRANSFERENCIA'))  * 100; // en centavos

        $importeTotal = $totalFC + $totalND + $totalNC;
        ($importeTotal > 0) ? $signo = '0' : $signo = '1';
        $totalFacturas = $this->formatearConCeros($totalFC * 100, 17);
        $totalNotasDebito = $this->formatearConCeros($totalND * 100, 17);
        $totalNotasCredito = $this->formatearConCeros($totalNC * 100, 17);
        $importeTotal = $this->formatearConCeros($importeTotal * 100, 17);
        $importePagar = $this->formatearConCeros($facturas->first()->MONTOTRANSFERENCIA * 100, 17);

        if ($importeTotal != $importePagar) {
//            dd( "El importe Total y el importe a Pagar no coinciden en la línea O1 del pago " . $numeroPago . " \n
//                Importe Total: ". $importeTotal . "\nImporte Pagar: " . $importePagar);
        }

        $condicionPago = $this->formatearConEspacios('Pago por transferencia', 30);


        $relleno = $this->formatearConCeros('0', 209);

//        $espacio = $this->formatearConEspacios(' ', 19);
        $espacio = '';
        $linea = $codigoRegistro . $numeroPago . $totalFacturas . $totalNotasDebito . $totalNotasCredito;
        $linea .= $importePagar . $signo . $condicionPago . $relleno . $espacio . "\r\n";
        return $linea;
    }

    /**
     * Crea las líneas FC, que corresponden a las facturas que se pagan con la transferencia actual
     * @param Collection $comprobantes
     * @param Collection $retenciones
     * @return Collection $arrFc Arreglo con todas las lineas a escribir
     */
    private function crearLineaFC($comprobantes, $retenciones)
    {
        $arrFc = array();
        $codigoRegistro = 'FC';
        $num = 0;
//        $comprobantes = new Collection();
//        $comprobantes->push($facturas)->push($notasCredito)->push($notasDebito);
        $tipoDoc2 = '  ';
        $numComprobante2 = $this->formatearConCeros('0', 12);
        $concepto2 = $this->formatearConEspacios(' ', 30);
        $fechaEmision2 = $this->formatearConCeros('0', 8);
        $importeComprobante2 = $this->formatearConCeros('0', 17);
        $importeRetenido2 = $this->formatearConCeros('0', 17); // <-- cuando se pagan varias facturas (pago 7197), ¿cuanto de la retención se aplica a cada factura??
        $baseImponible2 = $this->formatearConCeros('0', 17);
        $alicuota2 = $this->formatearConCeros('0', 6);

        $relleno = $this->formatearConEspacios(' ', 97);

        $concepto = $this->formatearConEspacios(' ', 30);
//        $fechaEmision = Carbon::today()->format('dmY');
        foreach ($comprobantes as $comprobante) {
            $num++;
            $tipoDocumento = $comprobante->TIPOCOMPROBANTE;
            if ($tipoDocumento = 'FA' or $tipoDocumento = 'FC' or $tipoDocumento = 'FE' or $tipoDocumento = 'FM') {
                $tipoComprobante = 'FC';

            } elseif ($tipoDocumento = 'NCA' or $tipoDocumento = 'NCC') {
                $tipoComprobante = 'NC';

            } elseif ($tipoDocumento = 'NDA' or $tipoDocumento = 'NDC') {
                $tipoComprobante = 'ND';

            }

            if (($retenciones->count() > 0)) {
                $montoRetencion = $retenciones->sum('MONTORETENCION');
            } else {
                $montoRetencion = '0';
            }

            $numRegistro = $this->formatearConCeros($num, 3);
            $numComprobante = (int)$comprobante->NUMEROCOMPROBANTE;
            $numComprobante = $this->formatearConCeros($numComprobante, 12);
//            $numComprobante     = $this->formatearConEspacios($numComprobante, 30);
            $concepto = $this->formatearConEspacios(' ', 30);
            $importeComprobante = $this->formatearConCeros($comprobante->TOTALCOMPROBANTE * 100, 17);
            $importeRetenido = $this->formatearConCeros($montoRetencion * 100, 17); // <-- cuando se pagan varias facturas (pago 7197), ¿cuanto de la retención se aplica a cada factura??
            $baseImponible = $this->formatearConCeros($comprobante->TOTALCOMPROBANTE * 100, 17);
            $fechaEmision = Carbon::parse($comprobante->FECHACOMPROBANTE)->format('dmY');

            $alicuota = $this->formatearConCeros('0', 6);

            $linea = $codigoRegistro . $numRegistro . $tipoComprobante;
            $linea .= $numComprobante . $concepto . $fechaEmision;
            $linea .= $importeComprobante . $importeRetenido . $baseImponible;
            $linea .= $tipoDoc2 . $numComprobante2 . $concepto2 . $fechaEmision2;
            $linea .= $importeComprobante2 . $importeRetenido2 . $baseImponible2;
            $linea .= $alicuota . $alicuota2 . $relleno . "\r\n";

            $arrFc[] = $linea;
//            $bytes_written = file_put_contents($fullPath, $linea, FILE_APPEND);
            $linea = '';
        }


        return collect($arrFc);
    }

    /**
     * Crea la línea para el comprobante de retencion
     * @param Collection $pago
     * @param Collection $retenciones
     * @return array
     */
    private function crearLineaC1($pago, $retenciones)
    {
        $lineaC1 = array();
        if (count($retenciones) > 0) {
            $codigoRegistro = 'C1';
            $tipoRetencion = '';
            $tipoImpuesto = '1';
            $textoResolucion = $this->formatearConEspacios(' ', 20);
            $codigoPartido = '0137';
            $tituloRetencion = $this->formatearConEspacios(' ', 30);

            $centroEmisor = '0000';
            $numAgenteRetencion = $this->formatearConCeros('30527990773', 20);
            (empty($pago->IIBB)) ? $numIIBB = ' ' : $numIIBB = str_replace('-', '', $pago->IIBB);
            $numIIBB = $this->formatearConEspacios($numIIBB, 20);
            $tipoComprobante = '01';
            $condicionImpuesto = '  ';
            switch ($pago->CONDICIONIVA) {
                case 'EXENTO':
                    $condicionImpuesto = '03';
                    break;
                case 'EXENTO FC A':
                    $condicionImpuesto = '09';
                    break;
                case 'EXTERIOR':
                    $condicionImpuesto = '13';
                    break;
                case 'MONOTRIBUTO':
                    $condicionImpuesto = '11';
                    break;
                case 'RESPONSABLE INSCRIPTO':
                    $condicionImpuesto = '06';
                    break;
                case 'RESPONSABLE INSCRIPTO M':
                    $condicionImpuesto = '06';
                    break;
                case 'RET IVA VENTA DE BIENES':
                    // no break
                case 'RET IVA TASA REDUCIDA 10,5% ':
                    $tipoRetencion = '01';
                    break;
                default:
                    $condicionImpuesto = '12';
            }


            foreach ($retenciones as $retencion) {
                switch ($retencion->TIPORETENCION) {
                    case 'RET GANANCIAS VENTA DE BIENES DE CAMBIO RI':
                        // no break
                    case 'RET GANANCIAS LOCACION OBRA Y SERVICIOS':
                        // no break
                    case 'RET GANANCIAS FACTURA M':
                        // no break
                    case 'RET GANANCIAS HONORARIOS/COMISIONES':
                        $tipoRetencion = '02';
                        break;
                    case 'SERVICIOS DE SEGURIDAD SOCIAL SUSS':
                        // no break
                    case 'RET SUSS SERVICIOS DE LIMPIEZA':
                        // no break
                    case 'SEG SOCIAL VIGILANCIA':
                        $tipoRetencion = '04';
                        break;
                    case 'RET IVA LOCACION Y PRESTACION SERVICIOS':
                        // no break
                    case 'RET IVA VENTA DE BIENES':
                        // no break
                    case 'RET IVA 21% (COD 2 AFIPREPROWEB) O FM':
                        // no break
                    case 'RET IVA TASA REDUCIDA 10,5% ':
                        $tipoRetencion = '01';
                        break;
                    default:
                        dd('Hay retenciones que no están contempladas.  Por favor, revisar.');
                }


                $numeroCertificado = substr(str_replace('-', '', $retencion->NUMERORETENCION), 2);
                $numComprobante = (int)$pago->NUMEROCOMPROBANTE;
                $numComprobante = $this->formatearConEspacios($numComprobante, 35);
                $fechaRetencion = Carbon::parse($pago->FECHAIMPUTABLE)->format('dmY');
                $totalRetenido = $this->formatearConCeros($retencion->MONTORETENCION * 100, 17);
                $declaracionJurada = Carbon::parse($pago->FECHAIMPUTABLE)->format('mY');
                $relleno = $this->formatearConEspacios(' ', 137);
                $c1 = $codigoRegistro . $tipoRetencion . $textoResolucion . $tipoImpuesto . $codigoPartido . $tituloRetencion;
                $c1 .= $centroEmisor . $numeroCertificado . $numAgenteRetencion . $condicionImpuesto . $numIIBB;
                $c1 .= $tipoComprobante . $numComprobante . $fechaRetencion . $totalRetenido . $declaracionJurada;
                $c1 .= $relleno . "\r\n";


//                $numeroCertificado = substr(str_replace('-', '', $retencion->NUMERORETENCION), 2);
                $conceptoRetencion = $this->formatearConEspacios($retencion->TIPORETENCION, 30);
                $tipoImporte = '04';
                $signoImporte = '1';
                $importe = $this->formatearConCeros($retencion->MONTORETENCION * 100, 17);
                $relleno = $this->formatearConCeros('0', 100);
                $espacioLibre = $this->formatearConEspacios(' ', 154);
                $c2 = 'C2' . $centroEmisor . $numeroCertificado . $conceptoRetencion . $tipoImporte;
                $c2 .= $signoImporte . $importe . $relleno . $espacioLibre . "\r\n";
//                $lineaC2[] = $c2;

                $lineaC1[] = $c1 . $c2;
            }

        }

        return $lineaC1;
    }

    /**
     * Genera el detalle del comprobante de retenciones
     * @param Collection $retenciones
     * @return array
     */
    public function crearLineaC2($retenciones)
    {
        $codigoRegistro = 'C2';
        $centroEmisor = '0000';
        $lineaC2 = array();
        foreach ($retenciones as $retencion) {
            $numeroCertificado = substr(str_replace('-', '', $retencion->NUMERORETENCION), 2);
            $conceptoRetencion = $this->formatearConEspacios($retencion->TIPORETENCION, 30);
            $tipoImporte = '04';
            $signoImporte = '1';
            $importe = $this->formatearConCeros($retencion->MONTORETENCION * 100, 17);
            $relleno = $this->formatearConCeros('0', 95);
            $espacioLibre = $this->formatearConEspacios(' ', 154);
            $c2 = $codigoRegistro . $centroEmisor . $numeroCertificado . $conceptoRetencion . $tipoImporte;
            $c2 .= $signoImporte . $importe . $relleno . $espacioLibre . "\r\n";
            $lineaC2[] = $c2;
        }
        return $lineaC2;

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
        if (strlen($str) > $espacios) {
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
        if (strlen($str) > $espacios) {
            // remuevo los caracteres sobrantes contando desde el final
            $str = substr($str, (strlen($str) * -1), $espacios);
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
        $datos = collect(DB::connection('firebird')->select($query))->first();
        if (count($datos) <= 0) {
            $datos = false;
        }
        return collect($datos);
    }

    private function escribirLinea($fullPath, $texto)
    {
        $bytes_written = 0;
        if (strlen($texto) > 0) {
            $bytes_written = file_put_contents($fullPath, $texto, FILE_APPEND);

            if (!$bytes_written) {
//                abort(500, 'Ocurrió un error al escribir la línea: <strong>' . $texto . '</strong>');
                die('Ocurrió un error al escribir el archivo: ' . $texto);
            }
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



