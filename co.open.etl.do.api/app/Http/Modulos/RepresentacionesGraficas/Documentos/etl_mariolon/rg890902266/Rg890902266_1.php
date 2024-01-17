<?php

/**
 * User: Juan Jose Trujillo
 * Date: 13/08/19
 * Time: 06:30 PM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_mariolon\rg890902266;

use App\Http\Traits\NumToLetrasEngine;
use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;

class Rg890902266_1 extends RgBase
{

    /**
     * Ordena el listado de items para imprimirlos de la siguiente manera.
     * se filtra por el campo tipo de información adicional
     * primero hay que colocar los que tienen en ese campo *
     * despues los que tiene el campo tipo vacio
     * y por ultimo el valor GMF
     *
     * @param $items
     * @return array
     */
    private function sortItems($items)
    {
        $items_pcc = [];
        $asterisco = [];
        $vacios = [];
        $gmf = [];
        $sinTipo = [];

        foreach ($items as $item) {

            if ($item['ddo_tipo_item'] === 'PCC') {
                $items_pcc[] = $item;
            } else {
                $informacionAdicional = json_decode($item['ddo_informacion_adicional']);
                if (!is_null($informacionAdicional) && isset($informacionAdicional->tipo)) {
                    if ($informacionAdicional->tipo === '*') {
                        $asterisco[] = $item;
                    } elseif (strtoupper($informacionAdicional->tipo) === 'GMF') {
                        $gmf[] = $item;
                    } else {
                        $vacios[] = $item;
                    }
                } else {
                    $sinTipo[] = $item;
                }
            }
        }
        return array_merge($items_pcc, $asterisco, $vacios, $gmf, $sinTipo);
    }

    public function getPdf()
    {
        extract($this->getDatos());

        //PDF
        $fpdf = $this->pdfManager();
        $fpdf->AcceptPageBreak();
        $fpdf->AliasNbPages();
        $fpdf->SetMargins(0, 0, 0);
        $fpdf->SetAutoPageBreak(true, 10);

        $fpdf->setImageHeader($this->getFullImage('logo' . $ofe_identificacion . '.png'));
        $datosComprobante['no_valido'] = $this->getFullImage("no_valido.png");

        $datosComprobante['qr'] = $qr;
        $datosComprobante['cufe'] = $cufe;
        $datosComprobante['signaturevalue'] = $signaturevalue;

        $datosComprobante['oferente'] = strtoupper($oferente);
        $nit = explode('-', $ofe_nit);
        $datosComprobante['ofe_nit'] = number_format($nit[0], 0, '', '.') . '-' . $nit[1];

        $datosComprobante['sede_principal'] = "";
        if (isset($cdo_informacion_adicional->sede_principal) && $cdo_informacion_adicional->sede_principal != "") {
            $datosComprobante['sede_principal'] = $cdo_informacion_adicional->sede_principal;
        } elseif (isset($ofe_representacion_grafica->sede_principal) && $ofe_representacion_grafica->sede_principal != "") {
            $datosComprobante['sede_principal'] = $ofe_representacion_grafica->sede_principal;
        }

        $datosComprobante['cdo_tipo']               = $cdo_tipo;
        $datosComprobante['ofe_resolucion_prefijo'] = $ofe_resolucion_prefijo;
        $datosComprobante['rfa_prefijo']            = $rfa_prefijo;
        $datosComprobante['numero_documento']       = $numero_documento;
        $datosComprobante['adquirente']             = $adquirente;
        $datosComprobante['adq_nit']                = $adq_nit;
        $datosComprobante['adq_dir']                = $adq_dir;
        $datosComprobante['adq_tel']                = $adq_tel;
        $datosComprobante['adq_dep']                = $adq_dep;
        $datosComprobante['fecha_hora_documento']   = $fecha_hora_documento;
        $datosComprobante['razon_social_pt']        = $razon_social_pt;
        $datosComprobante['nit_pt']                = $nit_pt;

        $datosComprobante['nombre_software'] = "";
        if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
            $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
        }

        $fecha_validacion = '';
        if (isset($cdo_fecha_validacion_dian) && is_string($cdo_fecha_validacion_dian)) {
            $vec = explode(' ', $cdo_fecha_validacion_dian);
            if (is_array($vec) && count($vec) > 1) {
                $fecha_validacion = $vec[0] . ' ' . $vec[1];
            }
        }
        $datosComprobante['fecha_validacion'] = $fecha_validacion;
        $forma_medio = $this->getDataMetodosPago($medios_pagos_documento);
        $datosComprobante['forma_pago'] = (isset($forma_medio['forma'])) ? $forma_medio['forma'] : '';
        $datosComprobante['medio_pago'] = (isset($forma_medio['medio'])) ? $forma_medio['medio'] : '';

        $datosComprobante['do'] = "";
        if (isset($cdo_informacion_adicional->do) && $cdo_informacion_adicional->do != "") {
            $datosComprobante['do'] = $cdo_informacion_adicional->do;
        }

        $datosComprobante['pedido'] = "";
        if (isset($cdo_informacion_adicional->pedido) && $cdo_informacion_adicional->pedido != "") {
            $datosComprobante['pedido'] = $cdo_informacion_adicional->pedido;
        }

        $datosComprobante['valor_letras'] = "";
        if (isset($cdo_informacion_adicional->valor_letras) && $cdo_informacion_adicional->valor_letras != '') {
            $datosComprobante['valor_letras'] = $cdo_informacion_adicional->valor_letras;
        } else {
            // Valor en Letras
            $datosComprobante['valor_letras'] = NumToLetrasEngine::num2letras(number_format($this->parserNumberController($valor_a_pagar), 2, '.', ''), false, true, $cdo_moneda);
        }

        $datosComprobante['elaboro'] = "";
        if (isset($cdo_informacion_adicional->elaboro) && $cdo_informacion_adicional->elaboro != '') {
            $datosComprobante['elaboro'] = $cdo_informacion_adicional->elaboro;
        }

        if ($datosComprobante['cdo_tipo'] == "NC" || $datosComprobante['cdo_tipo'] == "ND") {
            $datosComprobante['clasificacion']         = "";
            $datosComprobante['numero_documento_ref']  = "";
            $datosComprobante['fecha_documento_ref']   = "";
            $datosComprobante['cufe_documento_ref']    = "";
            if (!empty($cdo_documento_referencia)) {
                $clasificacion = json_decode(json_encode($cdo_documento_referencia), true);
                $datosComprobante['clasificacion'] = $clasificacion[0]['clasificacion'];

                list($factura_ref, $fecha_ref, $cufe_ref) = $this->getDocumentoReferencia($cdo_documento_referencia, $datosComprobante['clasificacion']);
                $datosComprobante['numero_documento_ref']  = $factura_ref;
                $datosComprobante['fecha_documento_ref']   = $fecha_ref;
                $datosComprobante['cufe_documento_ref']    = $cufe_ref;
            }
        }

        $datosComprobante['ciudad_factura'] = "";
        if (isset($cdo_informacion_adicional->ciudad_factura) && $cdo_informacion_adicional->ciudad_factura != "") {
            $datosComprobante['ciudad_factura'] = $cdo_informacion_adicional->ciudad_factura;
        }

        $datosComprobante['adq_mun'] = $adq_mun;
        $datosComprobante['fecha_vencimiento'] = $fecha_vencimiento;

        $datosComprobante['comercial'] = "";
        if (isset($cdo_informacion_adicional->comercial) && $cdo_informacion_adicional->comercial != "") {
            $datosComprobante['comercial'] = $cdo_informacion_adicional->comercial;
        }

        $datosComprobante['trm'] = "";
        if (isset($cdo_informacion_adicional->trm) && $cdo_informacion_adicional->trm != "") {
            $datosComprobante['trm'] = $cdo_informacion_adicional->trm;
        }

        $datosComprobante['plazo'] = "";
        if (isset($cdo_informacion_adicional->plazo) && $cdo_informacion_adicional->plazo != "") {
            $datosComprobante['plazo'] = $cdo_informacion_adicional->plazo;
        }

        $datosComprobante['oc'] = "";
        if (isset($cdo_informacion_adicional->oc) && $cdo_informacion_adicional->oc != "") {
            $datosComprobante['oc'] = $cdo_informacion_adicional->oc;
        }

        // Observacion
        $datosComprobante['observacion'] = "";
        if (isset($cdo_informacion_adicional->observacion) && $cdo_informacion_adicional->observacion != '') {
            $datosComprobante['observacion'] = $cdo_informacion_adicional->observacion;
        }

        // Datos Footer
        $datosComprobante['info_consignacion'] = "";
        if (isset($ofe_representacion_grafica->info_consignacion) && $ofe_representacion_grafica->info_consignacion != '') {
            $datosComprobante['info_consignacion'] = $ofe_representacion_grafica->info_consignacion;
        }

        $datosComprobante['agentes_reteiva'] = "";
        if (isset($ofe_representacion_grafica->agentes_reteiva) && $ofe_representacion_grafica->agentes_reteiva != '') {
            $datosComprobante['agentes_reteiva'] = $ofe_representacion_grafica->agentes_reteiva;
        }

        $datosComprobante['autorizacion_dian'] = "";
        if (isset($ofe_representacion_grafica->autorizacion_dian) && $ofe_representacion_grafica->autorizacion_dian != '') {
            $date1  = strtotime($ofe_resolucion_fecha);
            $date2  = strtotime($ofe_resolucion_fecha_hasta);
            $diff   = $date2 - $date1;
            $meses  = (string) round($diff / (60 * 60 * 24 * 30.5));

            $arrConv = array("{res}", "{res_fecha_desde}", "{res_fecha_hasta}", "{res_prefijo}", "{res_desde}", "{res_prefijo}", "{res_hasta}", "{meses}");
            $arrRes  = array($ofe_resolucion, str_replace("-", "/", $ofe_resolucion_fecha), str_replace("-", "/", $ofe_resolucion_fecha_hasta), $ofe_resolucion_prefijo, $ofe_resolucion_desde, $ofe_resolucion_prefijo, $ofe_resolucion_hasta, $meses);

            $ofe_representacion_grafica->autorizacion_dian = str_replace($arrConv, $arrRes, $ofe_representacion_grafica->autorizacion_dian);
            $datosComprobante['autorizacion_dian'] = $ofe_representacion_grafica->autorizacion_dian;
        }

        $datosComprobante['advertencia_incumplimiento'] = "";
        if (isset($ofe_representacion_grafica->advertencia_incumplimiento) && $ofe_representacion_grafica->advertencia_incumplimiento != '') {
            $datosComprobante['advertencia_incumplimiento'] = $ofe_representacion_grafica->advertencia_incumplimiento;
        }

        $datosComprobante['actividad_economica'] = "";
        if (isset($ofe_representacion_grafica->actividad_economica) && $ofe_representacion_grafica->actividad_economica != '') {
            $datosComprobante['actividad_economica'] = $ofe_representacion_grafica->actividad_economica;
        }

        $datosComprobante['ciudad_ingreso'] = "";
        if (isset($cdo_informacion_adicional->ciudad_ingreso) && $cdo_informacion_adicional->ciudad_ingreso != '') {
            $datosComprobante['ciudad_ingreso'] = $cdo_informacion_adicional->ciudad_ingreso;
        }

        $datosComprobante['ofe_resolucion'] = $ofe_resolucion;
        $datosComprobante['ofe_resolucion_fecha'] = $ofe_resolucion_fecha;
        $datosComprobante['ofe_resolucion_desde'] = $ofe_resolucion_desde;
        $datosComprobante['ofe_resolucion_hasta'] = $ofe_resolucion_hasta;

        $date1 = strtotime($ofe_resolucion_fecha);
        $date2 = strtotime($ofe_resolucion_fecha_hasta);
        $diff = $date2 - $date1;
        $meses = (string) round($diff / (60 * 60 * 24 * 30.5));

        $datosComprobante['meses'] = $meses;

        // Observacion
        try {
            $datosComprobante['observacion_decode'] = (array) json_decode($observacion);
        } catch (\Throwable $th) {
            $datosComprobante['observacion_decode'] = [];
        }

        $intTotalReteIca = 0;
        if (isset($cdo_informacion_adicional->reteica) && $cdo_informacion_adicional->reteica != '') {
            $intTotalReteIca = $cdo_informacion_adicional->reteica;
        }

        $intTotalReteIva = 0;
        if (isset($cdo_informacion_adicional->reteiva) && $cdo_informacion_adicional->reteiva != '') {
            $intTotalReteIva = $cdo_informacion_adicional->reteiva;
        }

        $intAnticipoRecibido = 0;
        if (isset($cdo_informacion_adicional->anticipo_recibido) && $cdo_informacion_adicional->anticipo_recibido != '') {
            $intAnticipoRecibido = $cdo_informacion_adicional->anticipo_recibido;
        }

        $intSaldoaFavor = 0;
        if (isset($cdo_informacion_adicional->saldo_a_favor) && $cdo_informacion_adicional->saldo_a_favor != '') {
            $intSaldoaFavor = $cdo_informacion_adicional->saldo_a_favor;
        }

        $intIpGravados = 0;
        if (isset($cdo_informacion_adicional->ip_gravados) && $cdo_informacion_adicional->ip_gravados != '') {
            $intIpGravados = $cdo_informacion_adicional->ip_gravados;
        }

        $intTotalRetenciones = $intTotalReteIva + $intTotalReteIca;

        //Para la nota debito se debe calcular $intIpGravados, $intTotalReteIva, $intTotalReteIca
        if ($cdo_tipo == "ND") {
            //Sumatoria Items gravados
            foreach ($items as $item) {
                $impuestoIva = $this->getIvaItem($item);
                $valorIva = $impuestoIva ? $impuestoIva['iid_valor'] : 0;
                if ($valorIva > 0) {
                    if (isset($cdo_moneda) && $cdo_moneda == 'COP') {
                        $intIpGravados += $item['ddo_total'];
                    } else {
                        $intIpGravados += $item['ddo_total_moneda_extranjera'];
                    }
                }
            }
            //Retenciones
            $reteIva = $this->getTotalCargoDescuentoRetenciones($cdo_id, false, self::RETEIVA);
            $reteIca = $this->getTotalCargoDescuentoRetenciones($cdo_id, false, self::RETEICA);

            if (isset($cdo_moneda) && $cdo_moneda == 'COP') {
                $intTotalReteIva = $reteIva['local'];
                $intTotalReteIca = $reteIca['local'];
                $intTotalRetenciones = $intTotalReteIva + $intTotalReteIca;
            } else {
                $intTotalReteIva = $reteIva['extranjera'];
                $intTotalReteIca = $reteIca['extranjera'];
                $intTotalRetenciones = $intTotalReteIva + $intTotalReteIca;
            }

            //Usuario
            $datosComprobante['elaboro'] = $usuario_creacion;
        }

        if (isset($cdo_moneda) && $cdo_moneda == 'COP') {
            $subtotal          = $this->parserNumberController($subtotal);
            $intTotalPagar     = $this->parserNumberController($valor_a_pagar);
            $intIva               = $this->parserNumberController($iva);
            $intTotal          = $this->parserNumberController($subtotal) + $this->parserNumberController($iva);
        } else {
            $subtotal          = $this->parserNumberController($subtotal_moneda_extranjera);
            $intTotalPagar     = $this->parserNumberController($valor_a_pagar_moneda_extranjera);
            $intIva               = $this->parserNumberController($iva_moneda_extranjera);
            $intTotal          = $this->parserNumberController($subtotal_moneda_extranjera) + $this->parserNumberController($iva_moneda_extranjera);
        }

        $fpdf->datosComprobante = $datosComprobante;

        /*** Impresion Comprobante. ***/
        $fpdf->AddPage('P', 'Letter');

        $posx = $fpdf->posx;
        $posy = $fpdf->nPosYDet;

        $nPosYFin = 240;

        // Items
        $items = $this->sortItems($items);

        // $items = array_merge($items,$items,$items,$items);
        // $items = array_merge($items,$items,$items,$items,$items);
        // $items = array_merge($items,$items,$items,$items,$items);

        $countIP = 0;
        $contItem = 0;

        if (isset($items) && count($items) > 0) {
            //Propiedades de la tabla

            $fpdf->SetWidths(array(10, 20, 56, 20, 15, 24, 11, 13, 17, 19));
            $fpdf->SetAligns(array("C", "L", "L", "C", "C", "R", "C", "R", "R", "R"));
            $fpdf->SetLineHeight(4);

            $fpdf->setXY($posx, $posy);
            foreach ($items as $item) {
                $contItem++;

                if ($fpdf->getY() > $nPosYFin) {
                    $fpdf->printLines(242, true);
                    $fpdf->AddPage('P', 'Letter');
                    $posx = $fpdf->posx;
                    $posy = $fpdf->nPosYDet;
                    $fpdf->setXY($posx, $posy);
                }

                if ($item['ddo_tipo_item'] == 'PCC') {
                    $fpdf->SetFont('Arial', 'B', 8);
                } else {
                    $fpdf->SetFont('Arial', '', 8);
                    if ($countIP == 0) {
                        $fpdf->Ln(3);
                        $fpdf->setX($posx + 30);
                        $fpdf->SetFont('Arial', 'B', 8);
                        $fpdf->Cell(148, 4, "SERVICIOS PRESTADOS ", 0, 0, 'L');
                        $fpdf->SetFont('Arial', '', 8);
                        $fpdf->Ln(5);
                    }
                    $countIP += 1;
                }

                $item['ddo_informacion_adicional'] = json_decode($item['ddo_informacion_adicional']);
                $impustoIva = $this->getIvaItem($item);
                $porcenteajeIva = $impustoIva ? number_format($impustoIva['iid_porcentaje'], 0, ',', '.') . '%' : '0%';
                $valorIva = $impustoIva ? number_format($impustoIva['iid_valor'], 0, ',', '.') : '0';

                $strDescripcion = $item['ddo_descripcion_uno'];
                if ($item['ddo_informacion_adicional'] != null && isset($item['ddo_informacion_adicional']->tipo) && $item['ddo_informacion_adicional']->tipo == '*') {
                    if ($item['ddo_tipo_item'] == 'IP' || $item['ddo_tipo_item'] == '') {
                        $strDescripcion = "* " . $item['ddo_descripcion_uno'];
                    }
                }

                //Cantidad de decimales
                $nCanDec    = (strpos(($item['ddo_cantidad']+0),'.') > 0) ? 2 : 0;
                $nCanDecUni = (strpos(($item['ddo_valor_unitario']+0),'.') > 0) ? 2 : 0;

                $fpdf->setX($posx);
                $fpdf->Row([
                    number_format($contItem),
                    utf8_decode($item['ddo_codigo']),
                    utf8_decode($strDescripcion),
                    utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                    number_format($item['ddo_cantidad'], $nCanDec, ',', '.'),
                    number_format($item['ddo_valor_unitario'], $nCanDecUni, ',', '.'),
                    $porcenteajeIva,
                    $valorIva,
                    number_format($item['ddo_total_moneda_extranjera'], 2, ',', '.'),
                    number_format($item['ddo_total'], 0, ',', '.')
                ]);
            }
            // $fpdf->printLines(190, true);
        }

        //Pintar en la siguiente pagina
        if ($fpdf->getY() > 181) {
            $fpdf->printLines(240, true);
            $fpdf->AddPage('P', 'Letter');
            $posx = $fpdf->posx;
        }
        $fpdf->printLines(181, true);

        $fpdf->setXY($posx + 1, $fpdf->getY() + 6);
        $fpdf->Cell(8,5,$contItem,'T',0,'C');

        $posy = 190;
        // $posyIni = $posy;

        $fpdf->setXY($posx, $posy + 1);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->MultiCell(135, 3.2, "OBSERVACIONES: \n" . utf8_decode(implode("\n", $datosComprobante['observacion_decode'])), 0, 'L');

        $fpdf->setXY($posx, $posy + 23);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->MultiCell(135, 3.2, utf8_decode($datosComprobante['agentes_reteiva']), 0, 'L');
        $fpdf->setX($posx);
        $fpdf->MultiCell(135, 3.2, utf8_decode($datosComprobante['autorizacion_dian']), 0, 'L');
        $fpdf->setX($posx);
        $fpdf->MultiCell(135, 3.2, utf8_decode($datosComprobante['advertencia_incumplimiento']), 0, 'L');

        $fpdf->setXY($posx, $posy + 54);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->MultiCell(135, 3.2, utf8_decode($datosComprobante['info_consignacion']), 0, 'L');

        $fpdf->setXY($posx + 137, $posy + 50);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(68, 4, "TOTAL EN LETRAS", 0, 0, 'C');
        $fpdf->Ln(4);
        $fpdf->setX($posx + 137);
        $fpdf->SetFont('Arial', '', 7);
        $fpdf->MultiCell(68, 3, utf8_decode($datosComprobante['valor_letras']), 0, 'C');

        ### Columna de Subtotales ##
        $intTotalFactura = ($intIpGravados + $intIva) - $intTotalRetenciones;
        //Para la nota debito el campo de ip gravados se trae del subtotal de ingresos porpios gravados

        $fpdf->setXY($posx + 136, $posy + 1);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(35, 4, ($cdo_tipo === 'FC') ? "SUBTOTAL SERVICIOS GRAVADOS" : "SUBTOTAL", 0, 0, 'L');
        $fpdf->Cell(34, 4, number_format(floatval($intIpGravados), 0), 0, 0, 'R');
        $fpdf->Ln(4.5);
        $fpdf->setX($posx + 136);
        $fpdf->Cell(35, 4, ($cdo_tipo === 'FC') ? "+ IVA" : "IVA", 0, 0, 'L');
        $fpdf->Cell(34, 4, number_format($intIva, 0), 0, 0, 'R');
        $fpdf->Ln(4.5);
        $fpdf->setX($posx + 136);
        $fpdf->Cell(35, 4, ($cdo_tipo === 'FC') ? "- RETE IVA" : "RETE IVA", 0, 0, 'L');
        $fpdf->Cell(34, 4, number_format($intTotalReteIva, 0), 0, 0, 'R');
        $fpdf->Ln(4.5);
        $fpdf->setX($posx + 136);
        $fpdf->Cell(35, 4, ($cdo_tipo === 'FC') ? "- RETE ICA" : "RETE ICA", 0, 0, 'L');
        $fpdf->Cell(34, 4, number_format($intTotalReteIca, 0), 0, 0, 'R');
        if($cdo_tipo === 'FC'){
            $fpdf->Ln(4.5);
            $fpdf->setX($posx + 136);
            $fpdf->Cell(35, 4, "TOTAL PAGOS, SERVICIOS E IVA", 0, 0, 'L');
            $fpdf->Cell(34, 4, number_format(floatval($intTotal), 0), 0, 0, 'R');
        }
        $fpdf->Ln(4.5);
        $fpdf->setX($posx + 136);
        $fpdf->Cell(35, 4, ($cdo_tipo === 'FC') ? "TOTAL FACTURA" : "TOTAL". utf8_decode(mb_strtoupper($cdo_tipo_nombre)), 0, 0, 'L');
        $fpdf->Cell(34, 4, number_format(($intTotalFactura), 0), 0, 0, 'R');
        $fpdf->Ln(4.5);
        $fpdf->setX($posx + 136);
        $fpdf->Cell(35, 4, ($cdo_tipo === 'FC') ? "- ANTICIPOS" : "ANTICIPOS", 0, 0, 'L');
        $fpdf->Cell(34, 4, number_format($intAnticipoRecibido, 0), 0, 0, 'R');
        $fpdf->Ln(4.5);
        $fpdf->setX($posx + 136);
        $fpdf->Cell(35, 4,"TOTAL A PAGAR", 0, 0, 'L');
        $fpdf->Cell(34, 4, number_format($intTotalPagar, 0), 0, 0, 'R');
        $fpdf->Ln(4.5);
        $fpdf->setX($posx + 136);
        $fpdf->Cell(35, 4,"TOTAL SALDO A FAVOR", 0, 0, 'L');
        $fpdf->Cell(34, 4, number_format($intSaldoaFavor, 0), 0, 0, 'R');

        $fpdf->Line($posx, $posy + 20, $posx + 136, $posy + 20);
        $fpdf->Line($posx, $posy + 50, $posx + 205, $posy + 50);
        $fpdf->Line($posx + 136, $posy, $posx + 136, $posy + 65);
        $fpdf->Rect($posx, $posy, 205, 65);

        return ['error' => false, 'pdf' => $fpdf->Output('S')];
    }
}
