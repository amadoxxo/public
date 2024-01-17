<?php

/**
 * User: David Castillo
 * Date: 24/04/2019
 * Time: 09:00 AM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_solucion\rg800219100;

use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use App\Http\Traits\NumToLetrasEngine;

class Rg800219100_1 extends RgBase
{
    public function getPdf()
    {
        extract($this->getDatos());
        $posx = 10;

        //Extrayendo información de cabecera de la factura
        $strOfeId = number_format($ofe_identificacion, 0, ",", ".");
        $ofe_nit = implode('-', array($strOfeId, $ofe_dv));

        //PDF
        $fpdf = $this->pdfManager();
        $fpdf->AcceptPageBreak();
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->AliasNbPages();
        $fpdf->SetMargins(0, 0, 0);
        $fpdf->SetAutoPageBreak(true, 10);

        //Cargo imagenes de storage
        $fpdf->setImageHeader($this->getFullImage('logo' . $ofe_identificacion . '.png'));
        $datosComprobante['no_valido'] = $this->getFullImage("no_valido.png");

        //Extraigo forma y medio de pago
        $datosComprobante['forma_pago'] = "";
        $datosComprobante['medio_pago'] = "";
        foreach ($medios_pagos_documento as $key => $medios_pagos) {
            $forma = $medios_pagos['forma'];
            $datosComprobante['forma_pago'] = (isset($forma['fpa_descripcion']) && $forma['fpa_descripcion'] != '') ? $forma['fpa_descripcion'] : '';
            $medio = $medios_pagos['medio'];
            $datosComprobante['medio_pago'] = (isset($medio['mpa_descripcion']) && $medio['mpa_descripcion'] != '') ? $medio['mpa_descripcion'] : '';
        }

        //Traigo las observaciones en un JSON
        try {
            $datosComprobante['observacion'] = (array) json_decode($observacion);
        } catch (\Throwable $th) {
            $datosComprobante['observacion'] = [];
        }

        // Totales Retenciones
        $intPorcRetefte = 0;
        $nTotalRetefteCOP = 0;
        $data = $this->getCargoDescuentosRetencionesTipo($cdo_id, self::MODO_CONSULTA_CABECERA, self::MODO_PORCENTAJE_DETALLAR);
        foreach ($data as $retencion => $grupo) {
            foreach ($grupo as $porcentaje => $valores) {
                switch ($retencion) {
                    case 'RETEFUENTE':
                        $nTotalRetefteCOP += $valores['valor'];
                        $intPorcRetefte = $porcentaje;
                        break;
                    default:
                        break;
                }
            }
        }

        
        //Convierto los valores en valores numericos
        $intIvaCOP       = $this->parserNumberController($iva);
        $intValorPagar   = $this->parserNumberController($valor_a_pagar);

        //Leo la fecha y hora de validacion por separado
        $datosComprobante['FechaValidacionDian'] = "";
        if (isset($cdo_fecha_validacion_dian) && $cdo_fecha_validacion_dian != "") {
            $fecha_dian = explode(" ", $cdo_fecha_validacion_dian);
            $datosComprobante['FechaValidacionDian'] = "Validación DIAN: " . $fecha_dian[0] . " / " . $fecha_dian[1];
        }

        //Cargo datos de referencia para las notas
        if ($cdo_tipo == "NC" || $cdo_tipo == "ND") {
            list($factura_ref, $fecha_ref, $cufe_ref) = $this->getDocumentoReferencia($cdo_documento_referencia);
            $datosComprobante['numero_documento_ref'] = $factura_ref;
            $datosComprobante['fecha_documento_ref']  = $fecha_ref;
            $datosComprobante['cufe_documento_ref']   = $cufe_ref;
        }

        /**
         * Cargo campos del ofe en un array
         * Creo un array con los nombres de los campos
         * Recorro los campos asignandolos en el array datosComprobante
         */
        $ofe_representacion_grafica = (array) $ofe_representacion_grafica;
        $vCampos = array_keys($ofe_representacion_grafica);

        foreach ($vCampos as $value) {
            $datosComprobante[$value] = "";
            if (isset($ofe_representacion_grafica[$value]) && $ofe_representacion_grafica[$value] != "") {
                $datosComprobante[$value] = $ofe_representacion_grafica[$value];
            }
        }

        $datosComprobante['nota_1'] = "";
        if (isset($ofe_representacion_grafica['nota_1']) && $ofe_representacion_grafica['nota_1'] != "") {
            $datosComprobante['nota_1'] = $ofe_representacion_grafica['nota_1'];
        }

        $datosComprobante['nota_final_1'] = "";
        if (isset($ofe_representacion_grafica['nota_final_1']) && $ofe_representacion_grafica['nota_final_1'] != "") {
            $datosComprobante['nota_final_1'] = $ofe_representacion_grafica['nota_final_1'];
        }

        $datosComprobante['nota_final_2'] = "";
        if (isset($ofe_representacion_grafica['nota_final_2']) && $ofe_representacion_grafica['nota_final_2'] != "") {
            $datosComprobante['nota_final_2'] = $ofe_representacion_grafica['nota_final_2'];
        }

        $datosComprobante['nota_final_3'] = "";
        if (isset($ofe_representacion_grafica['nota_final_3']) && $ofe_representacion_grafica['nota_final_3'] != "") {
            $datosComprobante['nota_final_3'] = $ofe_representacion_grafica['nota_final_3'];
        }

        $datosComprobante['resolucion'] = "";
        //Remplazo array de la resolucion con los valores del documento
        if (isset($ofe_representacion_grafica['resolucion']) && $ofe_representacion_grafica['resolucion'] != "") {
            $date1  = strtotime($ofe_resolucion_fecha);
            $date2  = strtotime($ofe_resolucion_fecha_hasta);
            $diff   = $date2 - $date1;
            $meses  = (string) round($diff / (60 * 60 * 24 * 30.5));
            $arrConv = array(
                "{res}",
                "{res_fecha_desde}",
                "{res_prefijo}",
                "{res_desde}",
                "{res_hasta}",
                "{meses}"
            );

            $arrRes  = array(
                $ofe_resolucion,
                date(
                    "Y-m-d",
                    strtotime($ofe_resolucion_fecha)
                ),
                $ofe_resolucion_prefijo,
                $ofe_resolucion_desde,
                $ofe_resolucion_hasta,
                $meses
            );
            $datosComprobante['resolucion'] = str_replace($arrConv, $arrRes, $ofe_representacion_grafica['resolucion']);
        }

        $datosComprobante['saldo_a_favor'] = 0;
        if (isset($cdo_informacion_adicional->saldo_a_favor) && $cdo_informacion_adicional->saldo_a_favor != '') {
            $datosComprobante['saldo_a_favor'] = $this->parserNumberController(str_replace('.',',',$cdo_informacion_adicional->saldo_a_favor));
        }
        
        $datosComprobante['anticipo_recibido'] = 0;
        if (isset($cdo_informacion_adicional->anticipo_recibido) && $cdo_informacion_adicional->anticipo_recibido != '') {
            $datosComprobante['anticipo_recibido'] = $this->parserNumberController(str_replace('.',',',$cdo_informacion_adicional->anticipo_recibido));
        }

        //Datos del proveedor tecnologico
        $datosComprobante['nombre_software'] = "";
        if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
            $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
        }

        /**
         * Cargo campos del documento en un array
         * Creo un array con los nombres de los campos
         * Recorro los campos asignandolos en el array datosComprobante
         */
        $cdo_informacion_adicional = (array) $cdo_informacion_adicional;
        $vCampos = array(
            "documento_transporte", "peso", "do", "piezas"
        );

        foreach ($vCampos as $value) {
            $datosComprobante[$value] = "";
            if (isset($cdo_informacion_adicional[$value]) && $cdo_informacion_adicional[$value] != "") {
                $datosComprobante[$value] = $cdo_informacion_adicional[$value];
            }
        }

        //Valores estandar extraios del core y cargados en el array datosComprobante
        $datosComprobante['cdo_conceptos_correccion']   = $cdo_conceptos_correccion;
        $datosComprobante['cdo_tipo']                   = $cdo_tipo;
        $datosComprobante['cdo_tipo_nombre']            = $cdo_tipo_nombre;
        $datosComprobante['cdo_trm']                    = $cdo_trm;
        $datosComprobante['oferente']                   = $oferente;
        $datosComprobante['ofe_nit']                    = $ofe_nit;
        $datosComprobante['ofe_dir']                    = $ofe_dir;
        $datosComprobante['ofe_tel']                    = $ofe_tel;
        $datosComprobante['ofe_mun']                    = $ofe_mun;
        $datosComprobante['ofe_pais']                   = $ofe_pais;
        $datosComprobante['ofe_web']                    = $ofe_web;
        $datosComprobante['numero_documento']           = $numero_documento;
        $datosComprobante['cdo_fecha']                  = $cdo_fecha;
        $datosComprobante['fecha_hora_documento']       = $fecha_hora_documento;
        $datosComprobante['cdo_hora']                   = $cdo_hora;
        $datosComprobante['fecha_vencimiento']          = $fecha_vencimiento;
        $datosComprobante['adquirente']                 = $adquirente;
        $datosComprobante['adq_nit']                    = $adq_nit;
        $datosComprobante['adq_dir']                    = $adq_dir;
        $datosComprobante['adq_mun']                    = $adq_mun;
        $datosComprobante['adq_dep']                    = $adq_dep;
        $datosComprobante['adq_pais']                   = $adq_pais;
        $datosComprobante['adq_tel']                    = $adq_tel;
        $datosComprobante['razon_social_pt']            = $razon_social_pt;
        $datosComprobante['nit_pt']                     = $nit_pt;

        $datosComprobante['qr']             = "";
        $datosComprobante['signaturevalue'] = "";
        $datosComprobante['cufe']           = "";
        if ($signaturevalue != '' && $qr != '') {
            $datosComprobante['qr']             = $qr;
            $datosComprobante['cufe']           = $cufe;
            $datosComprobante['signaturevalue'] = $signaturevalue;
        }

        //Agrego el array al objeto
        $fpdf->datosComprobante = $datosComprobante;

        //Posicion final para los items
        $posfin = $cdo_tipo == "FC" ? 155 : 195;

        //Inicializo variables y configuraciones para empezar a pintar
        $fpdf->AddPage('P', 'Letter');
        $posy = $fpdf->posy;
        $posx = $fpdf->posx;
        $fpdf->setXY($posx, $posy + 5);
        $fpdf->SetLineWidth(0.1);

        // $items = array_merge($items, $items, $items, $items, $items, $items, $items, $items, $items, $items);

        /*** Separo los items en PCC e IP. ** */
        $items_pcc = array_filter($items, function ($item) {
            return $item['ddo_tipo_item'] == 'PCC' || $item['ddo_tipo_item'] == 'GMF';
        });

        $items_ip = array_filter($items, function ($item) {
            return ($item['ddo_tipo_item'] == 'IP' || $item['ddo_tipo_item'] == '');
        });
        /*** Fin Separo los items en PCC e IP. ***/

        //Pintando Pcc si existen, cargo su total en intTotalPcc
        $intTotalPcc = 0;
        $intCountItem = 0;
        if (isset($items_pcc) && count($items_pcc) > 0) {
            $fpdf->SetFont('Arial', 'BI', 9);
            $fpdf->Cell(80, 5, "PAGOS A TERCEROS", 0, 0, 'L');
            $fpdf->ln(5);
            //Anchos de las columnas
            $fpdf->SetWidths(array(10, 20, 50, 20, 20, 25, 25, 25));
            //Alineacion de las columnas
            $fpdf->SetAligns(array("C", "C", "L", "C", "C", "C", "R", "R"));
            $fpdf->setX($posx);
            $fpdf->SetFont('Arial', 'BI', 7);
            $fpdf->Cell(10, 5, utf8_decode('ITEM'), 0, 0, 'C');
            $fpdf->Cell(20, 5, utf8_decode('CÓDIGO'), 0, 0, 'C');
            $fpdf->Cell(50, 5, utf8_decode('DESCRIPCIÓN'), 0, 0, 'L');
            $fpdf->Cell(20, 5, utf8_decode('UNIDAD'), 0, 0, 'C');
            $fpdf->Cell(20, 5, utf8_decode('CANT'), 0, 0, 'C');
            $fpdf->setXY($posx + 120, $posy + 10);
            $fpdf->MultiCell(25, 2.5, utf8_decode("FACTURA\nGASTOS"), 0, 'C');
            $fpdf->setXY($posx + 145, $posy + 10);
            $fpdf->MultiCell(25, 2.5, utf8_decode("VLR\nUNITARIO"), 0, 'C');
            $fpdf->setXY($posx + 170, $posy + 10);
            $fpdf->Cell(25, 5, utf8_decode('VLR TOTAL'), 0, 0, 'C');
            $fpdf->ln(6);
            $fpdf->SetLineHeight(3);

            foreach ($items_pcc as $item) {
                //Si no cabe el siguiente item creo otra pagina
                if ($fpdf->GetY() > $posfin + ($cdo_tipo == "FC" ? 66 : 26)) {
                    $fpdf->Rect($posx, $fpdf->posy + 5, 195, $posfin - $fpdf->posy + ($cdo_tipo == "FC" ? 70 : 30) - 5);
                    $fpdf->AddPage('P', 'Letter');
                    $fpdf->setXY($posx, $posy + 5);
                    $fpdf->SetFont('Arial', 'BI', 9);
                    $fpdf->Cell(80, 5, "PAGOS A TERCEROS", 0, 0, 'L');
                    $fpdf->ln(5);
                    $fpdf->setX($posx);
                    $fpdf->SetFont('Arial', 'BI', 7);
                    $fpdf->Cell(10, 5, utf8_decode('ITEM'), 0, 0, 'C');
                    $fpdf->Cell(20, 5, utf8_decode('CÓDIGO'), 0, 0, 'C');
                    $fpdf->Cell(50, 5, utf8_decode('DESCRIPCIÓN'), 0, 0, 'L');
                    $fpdf->Cell(20, 5, utf8_decode('UNIDAD'), 0, 0, 'C');
                    $fpdf->Cell(20, 5, utf8_decode('CANT'), 0, 0, 'C');
                    $fpdf->setXY($posx + 120, $posy + 10);
                    $fpdf->MultiCell(25, 2.5, utf8_decode("FACTURA\nGASTOS"), 0, 'C');
                    $fpdf->setXY($posx + 145, $posy + 10);
                    $fpdf->MultiCell(25, 2.5, utf8_decode("VLR\nUNITARIO"), 0, 'C');
                    $fpdf->setXY($posx + 170, $posy + 10);
                    $fpdf->Cell(25, 5, utf8_decode('VLR TOTAL'), 0, 0, 'C');
                    $fpdf->ln(6);
                }
                $intTotalPcc += $item['ddo_total'];

                $_ddo_informacion_adicional = json_decode($item['ddo_informacion_adicional']);

                $strFacturaGastos = '';
                if (isset($_ddo_informacion_adicional->factura_gastos) && $_ddo_informacion_adicional->factura_gastos != "") {
                    $strFacturaGastos = $_ddo_informacion_adicional->factura_gastos;
                }

                //Pinto la linea
                $intCountItem ++;
                $fpdf->SetFont('Arial', '', 7);
                $fpdf->setX($posx);
                $fpdf->Row(array(
                    $intCountItem,
                    '',
                    utf8_decode($item['ddo_descripcion_uno']),
                    utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                    number_format($item['ddo_cantidad'], 0, '.', ','),
                    utf8_decode($strFacturaGastos),
                    number_format($item['ddo_valor_unitario'], 0, '.', ','),
                    number_format($item['ddo_total'], 0, '.', ',')
                ));
            }
            //Pinto el subtotal de pagos a teceros
            $fpdf->setX($posx);
            $fpdf->SetFont('Arial', 'BI', 7);
            $fpdf->Cell(170, 5, utf8_decode("TOTAL PAGOS A TERCEROS"), 0, 0, 'R');
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(25, 5, number_format($intTotalPcc, 0, '.', ','), 0, 0, 'R');
            $fpdf->ln(3);
        }

        //Pintando IP si existen, cargo su total en intTotalIp
        $intTotalIp = 0;
        if (isset($items_ip) && count($items_ip) > 0) {
            $fpdf->SetFont('Arial', 'BI', 9);
            $fpdf->setX($posx);
            $fpdf->Cell(80, 5, "SERVICIOS GRAVADOS CON IVA", 0, 0, 'L');
            $fpdf->ln(5);
            //Anchos de las columnas
            $fpdf->SetWidths(array(10, 20, 50, 20, 20, 25, 25, 25));
            //Alineacion de las columnas
            $fpdf->SetAligns(array("C", "C", "L", "C", "C", "C", "R", "R"));
            $fpdf->setX($posx);
            //Si no he colocado titulos de las columnas los coloco
            if(!isset($items_pcc) || count($items_ip) == 0){
                $fpdf->SetFont('Arial', 'BI', 7);
                $fpdf->Cell(10, 5, utf8_decode('ITEM'), 0, 0, 'C');
                $fpdf->Cell(20, 5, utf8_decode('CODIGO'), 0, 0, 'C');
                $fpdf->Cell(50, 5, utf8_decode('DESCRIPCIÓN'), 0, 0, 'L');
                $fpdf->Cell(20, 5, utf8_decode('UNIDAD'), 0, 0, 'C');
                $fpdf->Cell(20, 5, utf8_decode('CANT'), 0, 0, 'C');
                $fpdf->setXY($posx + 120, $posy + 10);
                $fpdf->MultiCell(25, 2.5, utf8_decode("FACTURA\nGASTOS"), 0, 'C');
                $fpdf->setXY($posx + 145, $posy + 10);
                $fpdf->MultiCell(25, 2.5, utf8_decode("VLR\nUNITARIO"), 0, 'C');
                $fpdf->setXY($posx + 170, $posy + 10);
                $fpdf->Cell(25, 5, utf8_decode('VLR TOTAL'), 0, 0, 'C');
                $fpdf->ln(6);
            }
            $fpdf->SetLineHeight(3);

            foreach ($items_ip as $item) {
                //Si no cabe el siguiente item creo otra pagina
                if ($fpdf->GetY() > $posfin + ($cdo_tipo == "FC" ? 66 : 26)) {
                    $fpdf->Rect($posx, $fpdf->posy + 5, 195, $posfin - $fpdf->posy + ($cdo_tipo == "FC" ? 70 : 30) - 5);
                    $fpdf->AddPage('P', 'Letter');
                    $fpdf->setXY($posx, $posy + 5);
                    $fpdf->SetFont('Arial', 'BI', 9);
                    $fpdf->Cell(80, 5, "SERVICIOS GRAVADOS CON IVA", 0, 0, 'L');
                    $fpdf->ln(5);
                    $fpdf->setX($posx);
                    $fpdf->SetFont('Arial', 'BI', 7);
                    $fpdf->Cell(10, 5, utf8_decode('ITEM'), 0, 0, 'C');
                    $fpdf->Cell(20, 5, utf8_decode('CODIGO'), 0, 0, 'C');
                    $fpdf->Cell(50, 5, utf8_decode('DESCRIPCIÓN'), 0, 0, 'L');
                    $fpdf->Cell(20, 5, utf8_decode('UNIDAD'), 0, 0, 'C');
                    $fpdf->Cell(20, 5, utf8_decode('CANT'), 0, 0, 'C');
                    $fpdf->setXY($posx + 120, $posy + 10);
                    $fpdf->MultiCell(25, 2.5, utf8_decode("FACTURA\nGASTOS"), 0, 'C');
                    $fpdf->setXY($posx + 145, $posy + 10);
                    $fpdf->MultiCell(25, 2.5, utf8_decode("VLR\nUNITARIO"), 0, 'C');
                    $fpdf->setXY($posx + 170, $posy + 10);
                    $fpdf->Cell(25, 5, utf8_decode('VLR TOTAL'), 0, 0, 'C');
                    $fpdf->ln(6);
                }
                $intTotalIp += $item['ddo_total'];

                $_ddo_informacion_adicional = json_decode($item['ddo_informacion_adicional']);

                $strFacturaGastos = '';
                if (isset($_ddo_informacion_adicional->factura_gastos) && $_ddo_informacion_adicional->factura_gastos != "") {
                    $strFacturaGastos = $_ddo_informacion_adicional->factura_gastos;
                }

                //Pinto la linea
                $intCountItem ++;
                $fpdf->SetFont('Arial', '', 7);
                $fpdf->setX($posx);
                $fpdf->Row(array(
                    $intCountItem,
                    utf8_decode($item['ddo_codigo']),
                    utf8_decode($item['ddo_descripcion_uno']),
                    utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                    number_format($item['ddo_cantidad'], 0, '.', ','),
                    utf8_decode($strFacturaGastos),
                    number_format($item['ddo_valor_unitario'], 0, '.', ','),
                    number_format($item['ddo_total'], 0, '.', ',')
                ));
            }
            //Pinto el subtotal de pagos a teceros
            $fpdf->setX($posx);
            $fpdf->SetFont('Arial', 'BI', 7);
            $fpdf->Cell(50, 5, utf8_decode("SUBTOTAL"), 0, 0, 'L');
            $fpdf->Cell(120, 5, utf8_decode("TOTAL INGRESOS PROPIOS"), 0, 0, 'R');
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(25, 5, number_format($intTotalIp, 0, '.', ','), 0, 1, 'R');
        }
        $fpdf->setX($posx);
        $fpdf->Cell(10, 5, $intCountItem, 'T', 0, 'C');
        $fpdf->Ln(2);

        //Si se han exedido los items del espacio predispuesto hago otra pagina
        if ($fpdf->GetY() > $posfin) {
            $fpdf->Rect($posx, $fpdf->posy, 195, $posfin - $fpdf->posy + ($cdo_tipo == "FC" ? 70 : 30));
            $fpdf->AddPage('P', 'Letter');
            $fpdf->setXY($posx, $fpdf->posy);
            $posy = $fpdf->posy;
        }

        //Hago el rectangulo y las lineas de la pagina final
        $fpdf->Rect($posx, $fpdf->posy + 5, 195, $posfin - $fpdf->posy - 5);
        $nTotalFactura = ($intTotalIp + $intTotalPcc + $intIvaCOP) - $nTotalRetefteCOP;

        $fpdf->SetFont('Arial', '', 7);
        $fpdf->SetTextColor(100, 100, 100);
        $fpdf->setXY($posx + 115, $posfin);
        $fpdf->Cell(40, 5, "Subtotal", 0, 0, 'L');
        $fpdf->Cell(40, 5, number_format($intTotalIp + $intTotalPcc, 0, '.', ','), 0, 0, 'R');
        $fpdf->ln(5);
        $fpdf->setX($posx + 115);
        $fpdf->Cell(40, 5, "IVA", 0, 0, 'L');
        $fpdf->Cell(40, 5, number_format($intIvaCOP, 0, '.', ','), 0, 0, 'R');
        $fpdf->ln(5);
        $fpdf->setX($posx + 115);
        $fpdf->Cell(40, 5, "Retefuente (" . number_format($intPorcRetefte, 0, '.', ',') . "%)", 0, 0, 'L');
        $fpdf->Cell(40, 5, number_format($nTotalRetefteCOP, 0, '.', ','), 0, 0, 'R');
        $fpdf->ln(5);
        $fpdf->setX($posx + 115);
        $fpdf->Cell(40, 5, "Total", 0, 0, 'L');
        $fpdf->Cell(40, 5, number_format($nTotalFactura, 0, '.', ','), 0, 0, 'R');
        $fpdf->ln(5);
        $fpdf->setX($posx + 115);
        $fpdf->Cell(40, 5, "Anticipo", 0, 0, 'L');
        $fpdf->Cell(40, 5, number_format($datosComprobante['anticipo_recibido'], 0, '.', ','), 0, 0, 'R');
        $fpdf->ln(5);
        $fpdf->setX($posx + 115);
        $fpdf->Cell(40, 5, "Total a Pagar", 0, 0, 'L');
        $fpdf->Cell(40, 5, number_format($intValorPagar, 0, '.', ','), 0, 0, 'R');
        $fpdf->ln(5);
        $fpdf->setX($posx + 115);
        $fpdf->Cell(40, 5, "Saldo a Favor Del Cliente", 0, 0, 'L');
        $fpdf->Cell(40, 5, number_format($datosComprobante['saldo_a_favor'], 0, '.', ','), 0, 0, 'R');
        $fpdf->ln(5);
        $posyFinal = $fpdf->GetY();
        $strValorLetras = NumToLetrasEngine::num2letras(number_format($datosComprobante['saldo_a_favor'] > 0 ? $datosComprobante['saldo_a_favor'] : $intValorPagar, 2, '.', ''), false, true, $cdo_moneda);

        $fpdf->SetFillColor(217, 217, 217);
        $fpdf->Rect($posx, $posfin, 115, 35);

        $fpdf->Rect($posx + 115, $posfin, 80, $fpdf->GetY() - $posfin);
        $fpdf->setXY($posx, $posfin);
        $fpdf->MultiCell(115, 3, "OBSERVACIONES:\n" . utf8_decode(implode("\n", $datosComprobante['observacion'])), 0, 'L');

        $fpdf->setXY($posx, $posyFinal);
        $fpdf->SetTextColor(0, 0, 0);
        $fpdf->SetFont('Arial', 'IB', 8);
        $fpdf->MultiCell(195, 5, utf8_decode("SON: " . $strValorLetras), 1, 'L', true);
        $fpdf->setX($posx);
        if($cdo_tipo == 'FC'){
            $fpdf->SetFont('Arial', '', 7);
            $fpdf->MultiCell(195, 3.7, utf8_decode($datosComprobante['nota_final_1']), 1, 'L');
            $fpdf->setX($posx);
            $fpdf->MultiCell(195, 3.7, utf8_decode($datosComprobante['nota_final_2']), 1, 'L');
            $fpdf->setX($posx);
            $fpdf->MultiCell(195, 3.7, utf8_decode($datosComprobante['nota_final_3']), 1, 'L');
        }

        return ['error' => false, 'pdf' => $fpdf->Output('S')];
    }
}
