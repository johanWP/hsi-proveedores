<?php
namespace App\Http\Traits;


trait FormatFirebirdTrait {

    public function FormatearDetalleDePago($var)
    {
        foreach ($var as $fila )
        {
            $fila = $this->Format($fila);
        }
        return $var;
    }

    public function PonerGuionesAlCuit($cuit)
    {
        $tipo = substr($cuit, 0, 2);
        $verificador = substr($cuit, -1, 1);
        $dni = substr($cuit, 2, strlen($cuit) - 3);
        $cuit_formateado = $tipo . '-' . $dni . '-' . $verificador;
        return $cuit_formateado;
    }

    private function Format($fila)
    {
        isset($fila->NUMEROPAGO) ? $fila->NUMEROPAGO = (int)$fila->NUMEROPAGO: $fila->NUMEROPAGO = null;
        isset($fila->NUMEROCOMPROBANTE) ? $fila->NUMEROCOMPROBANTE = (int)$fila->NUMEROCOMPROBANTE: $fila->NUMEROCOMPROBANTE = null;
        isset($fila->EFECTIVO) ? $fila->EFECTIVO = number_format( (float)$fila->EFECTIVO, 2, ',', '.' ): $fila->EFECTIVO = null;
        isset($fila->NOMBREBENEFICIARIO) ? $fila->NOMBREBENEFICIARIO = utf8_encode( $fila->NOMBREBENEFICIARIO ): $fila->NOMBREBENEFICIARIO = null;
        isset($fila->MONTOCHEQUE) ? $fila->MONTOCHEQUE = number_format( (float)$fila->MONTOCHEQUE, 2, ',', '.' ): $fila->MONTOCHEQUE = null;
        isset($fila->FECHACHEQUE) ? $fila->FECHACHEQUE = date( "d-m-Y", strtotime($fila->FECHACHEQUE) ): $fila->FECHACHEQUE = null;
        isset($fila->FECHACOMPROBANTE) ? $fila->FECHACOMPROBANTE = date( "d-m-Y", strtotime($fila->FECHACOMPROBANTE) ): $fila->FECHACOMPROBANTE = null;
        isset($fila->FECHAIMPUTABLE) ? $fila->FECHAIMPUTABLE = date( "d-m-Y", strtotime($fila->FECHAIMPUTABLE) ): $fila->FECHAIMPUTABLE = null;
        isset($fila->NUMEROCOMPROBANTE) ? $fila->NUMEROCOMPROBANTE = (int)$fila->NUMEROCOMPROBANTE: $fila->NUMEROCOMPROBANTE = null;
        isset($fila->MONTOTRANSFERENCIA) ? $fila->MONTOTRANSFERENCIA = number_format( (float)$fila->MONTOTRANSFERENCIA, 2, ',', '.' ): $fila->MONTOTRANSFERENCIA = null;
        isset($fila->MONTORETENCION) ? $fila->MONTORETENCION = number_format( (float)$fila->MONTORETENCION, 2, ',', '.' ): $fila->MONTORETENCION = null;
        isset($fila->TOTAL) ? $fila->TOTAL = number_format( (float)$fila->TOTAL, 2, ',', '.' ): $fila->TOTAL = null;
        isset($fila->TOTALCOMPROBANTE) ? $fila->TOTALCOMPROBANTE = number_format( (float)$fila->TOTALCOMPROBANTE, 2, ',', '.' ): $fila->TOTALCOMPROBANTE = null;
        isset($fila->DEUDACOMPROBANTE) ? $fila->DEUDACOMPROBANTE = number_format( (float)$fila->DEUDACOMPROBANTE, 2, ',', '.' ): $fila->DEUDACOMPROBANTE = null;
        isset($fila->RAZONSOCIAL) ? $fila->RAZONSOCIAL = utf8_encode($fila->RAZONSOCIAL): $fila->RAZONSOCIAL = null;
        isset($fila->DIRECCION) ? $fila->DIRECCION = utf8_encode($fila->DIRECCION) : $fila->DIRECCION = null;
        isset($fila->LOCALIDAD) ? $fila->LOCALIDAD = utf8_encode($fila->LOCALIDAD) : $fila->LOCALIDAD = null;

        return $fila;
    }
}