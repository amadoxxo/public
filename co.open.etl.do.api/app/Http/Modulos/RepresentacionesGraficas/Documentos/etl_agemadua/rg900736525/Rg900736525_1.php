<?php
/**
 * User: Jhon Escobar
 * Date: 21/09/2020
 * Time: 10:19 AM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_agemadua\rg900736525;

use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use App\Http\Traits\NumToLetrasEngine;
use App\Http\Modulos\Documentos\EtlCargosDescuentosDocumentosDaop\EtlCargosDescuentosDocumentoDaop;


class Rg900736525_1 extends RgBase
{

    public function getPdf() {

        //Extrayendo información de cabecera de la factura
        extract($this->getDatos());

        //PDF
        $fpdf = $this->pdfManager();
        $fpdf->AcceptPageBreak();
        $fpdf->SetFont('Times','',8);
        $fpdf->AliasNbPages();
        $fpdf->SetMargins(0,0,0);
        $fpdf->SetAutoPageBreak(true,10);

        $fpdf->setImageHeader($this->getFullImage('logo'.$ofe_identificacion.'.png'));
        $datosComprobante['hoja_membrete'] = $this->getFullImage("hoja_membrete.png");
        $datosComprobante['no_valido']     = $this->getFullImage("no_valido.png");

        //Encabezado
        $datosComprobante['cdo_tipo']              = $cdo_tipo;
        $datosComprobante['adquirente']            = $adquirente;
        $datosComprobante['adq_dir']               = $adq_dir;
        $adqNit = explode('-', $adq_nit);
        $datosComprobante['adq_nit']               = number_format($adqNit[0], 0, ',', '.')."-".$adqNit[1];
        $datosComprobante['adq_mun']               = $adq_mun;
        $datosComprobante['adq_tel']               = $adq_tel;
        $datosComprobante['adq_correo']            = $adq_correo;
        $datosComprobante['fecha_hora_documento']  = $fecha_hora_documento;
        $datosComprobante['fecha_vencimiento']     = $fecha_vencimiento;
        $datosComprobante['cdo_tipo_nombre']       = $cdo_tipo_nombre;
        $datosComprobante['rfa_prefijo']           = $rfa_prefijo;
        $datosComprobante['cdo_consecutivo']       = $cdo_consecutivo;

        $datosComprobante['qr']              = "";
        $datosComprobante['signaturevalue']  = "";
        $datosComprobante['cufe']            = "";
        if($signaturevalue != '' && $qr !=''){
            $datosComprobante['qr']              = $qr;
            $datosComprobante['signaturevalue']  = $signaturevalue;
            $datosComprobante['cufe']            = $cufe;
        }

        $datosComprobante['razon_social_pt']      = $razon_social_pt;
        $datosComprobante['nit_pt']               = $nit_pt;

        $datosComprobante['nombre_software'] = "";
        if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
            $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
        }

        $datosComprobante['resolucion'] = "";
        if(isset($ofe_representacion_grafica->resolucion) && $ofe_representacion_grafica->resolucion != ""){
            $date1  = strtotime($ofe_resolucion_fecha);
            $date2  = strtotime($ofe_resolucion_fecha_hasta);
            $diff   = $date2 - $date1;
            $meses  = (string) round($diff / (60 * 60 * 24 * 30.5));

            $arrConv = array(
                "{res}", 
                "{res_fecha_desde}", 
                "{meses}", 
                "{res_prefijo}", 
                "{res_desde}", 
                "{res_prefijo}", 
                "{res_hasta}"
            );

            $arrRes  = array(
                $ofe_resolucion, 
                date("Y/m/d",strtotime($ofe_resolucion_fecha)), 
                $meses, 
                $ofe_resolucion_prefijo, 
                $ofe_resolucion_desde, 
                $ofe_resolucion_prefijo, 
                $ofe_resolucion_hasta
            ); 

            $datosComprobante['resolucion'] = str_replace($arrConv, $arrRes, $ofe_representacion_grafica->resolucion);
        }

        $datosComprobante['regimen'] = "";
        if(isset($ofe_representacion_grafica->regimen) && $ofe_representacion_grafica->regimen != ""){
            $datosComprobante['regimen'] = $ofe_representacion_grafica->regimen;
        }
        
        //Extrayendo información de Forma y medios de pago
        $datosComprobante['forma_pago'] = "";
        $datosComprobante['medio_pago'] = "";
        foreach ($medios_pagos_documento as $key => $medios_pagos){
            //Forma
            $forma = $medios_pagos['forma'];
            $datosComprobante['forma_pago'] = (isset($forma['fpa_descripcion']) && $forma['fpa_descripcion'] != '') ? $forma['fpa_descripcion'] : '';
            //Medio
            $medio = $medios_pagos['medio'];
            $datosComprobante['medio_pago'] = (isset($medio['mpa_descripcion']) && $medio['mpa_descripcion'] != '') ? $medio['mpa_descripcion'] : '';
        }

        $datosComprobante['validacion_dian'] = "";
        if (isset($cdo_fecha_validacion_dian) && $cdo_fecha_validacion_dian != "") {
            $fecha_dian = explode(" ", $cdo_fecha_validacion_dian);
            $datosComprobante['validacion_dian'] = $fecha_dian[0] ." / ".$fecha_dian[1];
        }

        if ($cdo_tipo == "NC" || $cdo_tipo == "ND") {
            list($factura, $fecha, $cufe) = $this->getDocumentoReferencia($cdo_documento_referencia);
            $datosComprobante['consecutivo_ref']    = $factura;
            $datosComprobante['fecha_emision']      = $fecha;
            $datosComprobante['cufe_ref']           = $cufe;
        }

        $datosComprobante['do'] = "";
        if(isset($cdo_informacion_adicional->do) && $cdo_informacion_adicional->do != ""){
            $datosComprobante['do'] = $cdo_informacion_adicional->do;
        }

        $datosComprobante['pedido'] = "";
        if(isset($cdo_informacion_adicional->pedido) && $cdo_informacion_adicional->pedido != ""){
            $datosComprobante['pedido'] = $cdo_informacion_adicional->pedido;
        }

        $datosComprobante['doc_transporte'] = "";
        if(isset($cdo_informacion_adicional->doc_transporte) && $cdo_informacion_adicional->doc_transporte != ""){
            $datosComprobante['doc_transporte'] = $cdo_informacion_adicional->doc_transporte;
        }

        $datosComprobante['moneda'] = "";
        if(isset($cdo_informacion_adicional->moneda) && $cdo_informacion_adicional->moneda != ""){
            $datosComprobante['moneda'] = $cdo_informacion_adicional->moneda;
        }

        $fpdf->datosComprobante = $datosComprobante;

        try {
            $observacion_decode = (array) json_decode($observacion);
        } catch (\Throwable $th) {
            $observacion_decode = [];
        }

        //Notas finales
        $strCuenta = "";
        if(isset($ofe_representacion_grafica->cuenta) && $ofe_representacion_grafica->cuenta != ""){
            $strCuenta = $ofe_representacion_grafica->cuenta;
        }

        $strNota_final = "";
        if(isset($ofe_representacion_grafica->nota_final_1) && $ofe_representacion_grafica->nota_final_1 != ""){
            $strNota_final = $ofe_representacion_grafica->nota_final_1;
        }

        $strDocumentoSoporte = "";
        if(isset($cdo_informacion_adicional->documento_soporte) && $cdo_informacion_adicional->documento_soporte != ""){
            $strDocumentoSoporte = $cdo_informacion_adicional->documento_soporte;
        }

        $intAnticipoRecibido = 0;
        if(isset($cdo_informacion_adicional->anticipo_recibido) && is_numeric($cdo_informacion_adicional->anticipo_recibido)){
            $intAnticipoRecibido = $cdo_informacion_adicional->anticipo_recibido;
        }

        $intSaldoFavor = 0;
        if(isset($cdo_informacion_adicional->saldo_favor) && is_numeric($cdo_informacion_adicional->saldo_favor)){
            $intSaldoFavor = $cdo_informacion_adicional->saldo_favor;
        }

        $strPorcentajeReteica = "";
        if(isset($cdo_informacion_adicional->porcentaje_reteica) && $cdo_informacion_adicional->porcentaje_reteica != ""){
            $strPorcentajeReteica = $cdo_informacion_adicional->porcentaje_reteica;
        }

        $strBaseReteica = "";
        if(isset($cdo_informacion_adicional->base_reteica) && $cdo_informacion_adicional->base_reteica != ""){
            $strBaseReteica = $cdo_informacion_adicional->base_reteica;
        }

        $intIva         = $this->parserNumberController($iva);
        $intIvaUSD      = $this->parserNumberController($iva_moneda_extranjera);
        $intTotal       = $this->parserNumberController($valor_a_pagar);
        $intTotalUSD    = $this->parserNumberController($valor_a_pagar_moneda_extranjera);
        $strMoneda      = $datosComprobante['moneda'] == 'USD' ? "USD" : "COP";
        $nDecimal       = $datosComprobante['moneda'] == 'USD' ? 2 : 0;

        // Totales Retenciones
        $intPorcenReteIva     = 0;
        $intTotalReteIvaCOP   = 0;
        $intTotalReteIvaUSD   = 0;
        $intTotalReteIcaCOP   = 0;
        $intTotalReteIcaUSD   = 0;
        $intTotalReteFteCOP4  = 0;
        $intTotalReteFteUSD4  = 0;
        $intTotalReteFteCOP11 = 0;
        $intTotalReteFteUSD11 = 0;
        $intBaseReteFte4      = 0;
        $intBaseReteFte11     = 0;

        $data = $this->getCargoDescuentosRetencionesTipo($cdo_id, self::MODO_CONSULTA_CABECERA, self::MODO_PORCENTAJE_DETALLAR);
        foreach($data as $retencion => $grupo){
            foreach ($grupo as $porcentaje => $valores){
                switch($retencion){
                    case 'RETEIVA':
                        $intTotalReteIvaCOP += $valores['valor'];
                        $intTotalReteIvaUSD += $valores['valor_extranjera'];
                        $intPorcenReteIva    = $porcentaje;
                    break;
                    case 'RETEICA':
                        $intTotalReteIcaCOP += $valores['valor'];
                        $intTotalReteIcaUSD += $valores['valor_extranjera'];
                    break;
                    case 'RETEFUENTE':
                        if ($porcentaje == "4.00") {
                            $intTotalReteFteCOP4 += $valores['valor'];
                            $intTotalReteFteUSD4 += $valores['valor_extranjera'];
                            $intBaseReteFte4     += $datosComprobante['moneda'] == "USD" ? $valores['base_extranjera'] : $valores['base'];
                        } elseif ($porcentaje == "11.00") {
                            $intTotalReteFteCOP11 += $valores['valor'];
                            $intTotalReteFteUSD11 += $valores['valor_extranjera'];
                            $intBaseReteFte11     += $datosComprobante['moneda'] == "USD" ? $valores['base_extranjera'] : $valores['base'];
                        }
                    break;
                    default:
                    break;
                }
            }
        }

        $fpdf->AddPage('P','Letter');
        $intConLines = 0;
        $intMaxLines = 35;
        $posx = $fpdf->posx;
        $posy = $fpdf->nPosYFin;
        $posfin = 195;

        // $items = array_merge($items,$items,$items,$items,$items,$items,$items,$items,$items, $items);
        // $items = array_merge($items,$items,$items,$items,$items,$items);
        // $items = array_merge($items,$items,$items,$items);

        /*** Separo los items en PCC e IP. ***/
        $items_pcc = array_filter($items, function($item){
            return ($item['ddo_tipo_item'] == 'PCC' || $item['ddo_tipo_item'] == 'GMF'); 
        });
        $items_ip = array_filter($items, function($item){
            return ($item['ddo_tipo_item'] == 'IP' || $item['ddo_tipo_item'] == ''); 
        });

        // Contador de items
        $contItem = 0;

        $intSubTotalVlrUniPcc = 0;
        $intSubTotalVlrUsdPcc = 0;
        $intSubTotalVlrCopPcc = 0;
        $intSubTotalVlrUniIp  = 0;
        $intSubTotalVlrUsdIp  = 0;
        $intSubTotalVlrCopIp  = 0;

        //Items
        if (isset($items_pcc) && count($items_pcc) > 0) {
            $fpdf->SetFont('Times','B',8);
            $fpdf->setXY($posx + 30, $posy + 2);
            $fpdf->Cell(65,6,"PAGOS A TERCEROS",0,0,'L');

            //Propiedades de la tabla
            $fpdf->SetWidths(array(10, 16, 62, 16, 16, 25, 25, 25));
            $fpdf->SetAligns(array("C", "C", "L", "C", "C", "R", "R", "R"));
            $fpdf->SetLineHeight(3);
            $fpdf->SetFont('Times','',8);

            $fpdf->setXY($posx, $posy + 8);
            foreach ($items_pcc as $item) {
                $contItem++;
    
                if($fpdf->getY() > $posfin - 3){
                    $fpdf->posy = 202;
                    $fpdf->RoundedRect($posx, $posy - 6, 195, ($posfin-$posy) + 10, 2);
                    $fpdf->AddPage('P','Letter');
                    $fpdf->setXY($posx,$posy+1);
                }

                $intValorTotalItemCOP = 0;
                $intValorTotalItemUSD = 0;
                $intValorUnitarioItem = 0;
                if ($datosComprobante['moneda'] == "USD") {
                    $intValorUnitarioItem = $item['ddo_valor_unitario_moneda_extranjera'];
                    $intValorTotalItemUSD = $item['ddo_total_moneda_extranjera'];
                } elseif ($datosComprobante['moneda'] == "COP") {
                    $intValorUnitarioItem = $item['ddo_valor_unitario'];
                    $intValorTotalItemCOP = $item['ddo_total'];
                } else {
                    $intValorUnitarioItem = $item['ddo_valor_unitario'];
                    $intValorTotalItemCOP = $item['ddo_total'];
                    $intValorTotalItemUSD = $item['ddo_total_moneda_extranjera'];
                }

                $intSubTotalVlrUniPcc += $intValorUnitarioItem;
                $intSubTotalVlrUsdPcc += $intValorTotalItemUSD;
                $intSubTotalVlrCopPcc += $intValorTotalItemCOP;

                $fpdf->setX($posx);
                $fpdf->Row([
                    number_format($contItem),
                    utf8_decode($item['ddo_codigo']),
                    utf8_decode($item['ddo_descripcion_uno']),
                    ucwords(utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion'))),
                    number_format($item['ddo_cantidad'], 0),
                    number_format($intValorUnitarioItem, $nDecimal, '.', ','),
                    number_format($intValorTotalItemUSD, 2, '.', ','),
                    number_format($intValorTotalItemCOP, $nDecimal, '.', ',')
                ]);
            }

            $fpdf->SetFont('Times','B',8);
            $fpdf->setXY($posx + 30, $fpdf->GetY()-1);
            $fpdf->Cell(90,6,"SUBTOTAL PAGOS A TERCEROS",0,0,'L');
            $fpdf->Cell(25,6,number_format($intSubTotalVlrUniPcc, $nDecimal, '.', ','), 0,0,'R');
            $fpdf->Cell(25,6,number_format($intSubTotalVlrUsdPcc, 2, '.', ','), 0,0,'R');
            $fpdf->Cell(25,6,number_format($intSubTotalVlrCopPcc, $nDecimal, '.', ','), 0,0,'R');
            $fpdf->Ln(6);
        }

        if (isset($items_ip) && count($items_ip) > 0) {
            $fpdf->SetFont('Times','B',8);
            $fpdf->setXY($posx + 30,$fpdf->GetY());
            $fpdf->Cell(65,6,"INGRESOS PROPIOS",0,0,'L');
            $fpdf->Ln(5);

            //Propiedades de la tabla
            $fpdf->SetWidths(array(10, 16, 62, 16, 16, 25, 25, 25));
            $fpdf->SetAligns(array("C", "C", "L", "C", "C", "R", "R", "R"));
            $fpdf->SetLineHeight(3);
            $fpdf->setXY($posx, $fpdf->GetY() + 1);
            $fpdf->SetFont('Times','',8);

            foreach ($items_ip as $item) {
                $contItem++;
    
                if($fpdf->getY() > $posfin - 3){
                    $fpdf->posy = 202;
                    $fpdf->RoundedRect($posx, $posy - 6, 195, ($posfin-$posy) + 10, 2);
                    $fpdf->AddPage('P','Letter');
                    $fpdf->setXY($posx,$posy+1);
                }

                $intValorTotalItemCOP = 0;
                $intValorTotalItemUSD = 0;
                $intValorUnitarioItem = 0;
                if ($datosComprobante['moneda'] == "USD") {
                    $intValorUnitarioItem = $item['ddo_valor_unitario_moneda_extranjera'];
                    $intValorTotalItemUSD = $item['ddo_total_moneda_extranjera'];
                } elseif ($datosComprobante['moneda'] == "COP") {
                    $intValorUnitarioItem = $item['ddo_valor_unitario'];
                    $intValorTotalItemCOP = $item['ddo_total'];
                } else {
                    $intValorUnitarioItem = $item['ddo_valor_unitario'];
                    $intValorTotalItemCOP = $item['ddo_total'];
                    $intValorTotalItemUSD = $item['ddo_total_moneda_extranjera'];
                }

                $intSubTotalVlrUniIp += $intValorUnitarioItem;
                $intSubTotalVlrUsdIp += $intValorTotalItemUSD;
                $intSubTotalVlrCopIp += $intValorTotalItemCOP;

                $fpdf->setX($posx);
                $fpdf->Row([
                    number_format($contItem),
                    utf8_decode($item['ddo_codigo']),
                    utf8_decode($item['ddo_descripcion_uno']),
                    ucwords(utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion'))),
                    number_format($item['ddo_cantidad'], 0),
                    number_format($intValorUnitarioItem, $nDecimal, '.', ','),
                    number_format($intValorTotalItemUSD, 2, '.', ','),
                    number_format($intValorTotalItemCOP, $nDecimal, '.', ',')
                ]);
            }

            $fpdf->SetFont('Times','B',8);
            $fpdf->setXY($posx + 30, $fpdf->GetY()-1);
            $fpdf->Cell(90,6,"SUBTOTAL INGRESOS PROPIOS",0,0,'L');
            $fpdf->Cell(25,6,number_format($intSubTotalVlrUniIp, $nDecimal, '.', ','),0,0,'R');
            $fpdf->Cell(25,6,number_format($intSubTotalVlrUsdIp, 2, '.', ','),0,0,'R');
            $fpdf->Cell(25,6,number_format($intSubTotalVlrCopIp, $nDecimal, '.', ','),0,0,'R');
            $fpdf->Ln(4);
        }

        $nLimit = $cdo_tipo == "FC" ? 110 : 155;
        if ($fpdf->GetY() > $nLimit) {
            $fpdf->posy = 202;
            $fpdf->RoundedRect($posx, $posy - 6, 195, ($posfin - $posy) + 12, 2);
            $fpdf->AddPage('P', 'Letter');
        }
        $nTamRect = $cdo_tipo == "FC" ? 75 : 44;
        $fpdf->RoundedRect($posx, $posy - 6, 195, ($posfin - $nTamRect) - ($posy), 2);

        $posy = $nLimit+1;
        $fpdf->setXY($posx, $posy+5);
        $fpdf->SetFont('Times', 'B', 6);
        $fpdf->MultiCell(45, 10, "Total Item: ".$contItem, 0, 'L');
        $fpdf->Ln(3);

        $intSubtotalVlrUni = $intSubTotalVlrUniIp + $intSubTotalVlrUniPcc;
        $intSubtotalVlrUSD = $intSubTotalVlrUsdIp + $intSubTotalVlrUsdPcc;
        $intSubtotalVlrCOP = $intSubTotalVlrCopIp + $intSubTotalVlrCopPcc;

        $intTotalFacturaCOP = $intSubtotalVlrCOP + $intIva    - ($intTotalReteIcaCOP + $intTotalReteFteCOP4 + $intTotalReteFteCOP11 + $intTotalReteIvaCOP);
        $intTotalFacturaUSD = $intSubtotalVlrUSD + $intIvaUSD - ($intTotalReteIcaUSD + $intTotalReteFteUSD4 + $intTotalReteFteUSD11 + $intTotalReteIvaUSD);

        $fpdf->setXY($posx + 67, $posy+6);
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Cell(53, 4, "SUBTOTAL", 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format($intSubtotalVlrUni, $nDecimal, '.', ','), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intSubtotalVlrUSD, 2, '.', ','), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intSubtotalVlrCOP, $nDecimal, '.', ','), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(4);
        $fpdf->setX($posx + 67);
        $fpdf->Cell(53, 4, "IVA ".number_format($porcentaje_iva,0)."%", 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] == "USD") ? $intIvaUSD : $intIva, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intIvaUSD, 2, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intIva, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(4);
        $fpdf->setX($posx + 67);
        $fpdf->Cell(53, 4, "RETENCION ICA (".$strPorcentajeReteica.") BASE ".$strBaseReteica, 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] == "USD") ? $intTotalReteIcaUSD : $intTotalReteIcaCOP, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalReteIcaUSD, 2, '.', ','), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalReteIcaCOP, $nDecimal, '.', ','), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(4);
        $fpdf->setX($posx + 67);
        $fpdf->Cell(53, 4, "RETENCION FUENTE: 4% BASE ".number_format($intBaseReteFte4, $nDecimal, '.', ','), 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] == "USD") ? $intTotalReteFteUSD4 : $intTotalReteFteCOP4, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalReteFteUSD4, 2, '.', ','), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalReteFteCOP4, $nDecimal, '.', ','), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(4);
        $fpdf->setX($posx + 67);
        $fpdf->Cell(53, 4, "RETENCION FUENTE: 11% BASE ".number_format($intBaseReteFte11, $nDecimal, '.', ','), 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] == "USD") ? $intTotalReteFteUSD11 : $intTotalReteFteCOP11, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalReteFteUSD11, 2, '.', ','), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalReteFteCOP11, $nDecimal, '.', ','), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(4);
        $fpdf->setX($posx + 67);
        $fpdf->Cell(53, 4, "RETENCION IVA ".number_format($intPorcenReteIva,0)."%", 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] == "USD") ? $intTotalReteIvaUSD : $intTotalReteIvaCOP, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalReteIvaUSD, 2, '.', ','), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalReteIvaCOP, $nDecimal, '.', ','), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(4);
        $fpdf->setX($posx + 67);
        $fpdf->Cell(53, 4, "TOTAL FACTURA", 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] == "USD") ? $intTotalFacturaUSD : $intTotalFacturaCOP, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalFacturaUSD, 2, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalFacturaCOP, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(4);
        $fpdf->setX($posx + 67);
        $fpdf->Cell(53, 4, "ANTICIPO(S):", 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format($intAnticipoRecibido, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] == "USD") ? $intAnticipoRecibido : 0, 2, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] != "USD") ? $intAnticipoRecibido : 0,$nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(4);
        $fpdf->setX($posx + 67);
        $fpdf->Cell(53, 4, "TOTAL A PAGAR", 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] == "USD") ? $intTotalUSD : $intTotal, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotalUSD, 2, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format($intTotal, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(4);
        $fpdf->setX($posx + 67);
        $fpdf->Cell(53, 4, "SALDO A FAVOR", 0, 0, 'L');
        $fpdf->SetFont('Times', '', 7.5);
        $fpdf->Cell(25, 4, number_format($intSaldoFavor, $nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] == "USD") ? $intSaldoFavor : 0, 2, ',', '.'), 0, 0, 'R');
        $fpdf->Cell(25, 4, number_format(($datosComprobante['moneda'] != "USD") ? $intSaldoFavor : 0,$nDecimal, ',', '.'), 0, 0, 'R');
        $fpdf->SetFont('Times', 'B', 7.5);
        $fpdf->Ln(3);
        $fpdf->RoundedRect($posx + 67, $posy+5, 128, ($fpdf->getY() - $posy)-4, 2);
        $fpdf->RoundedRect($posx, $posy+5, 65, ($fpdf->getY() - $posy)-4, 2);
        $posy = $fpdf->getY() + 2;

        $strValorLetras = NumToLetrasEngine::num3letras(number_format(($datosComprobante['moneda'] == "USD") ? $intTotalUSD : $intTotal, $nDecimal, '.', ''), false, true, $strMoneda);
        $fpdf->setXY($posx, $posy+1);
        $fpdf->SetFont('Times', '', 7);
        $fpdf->MultiCell(193, 3.5, "SON : ". utf8_decode($strValorLetras), 0, 'L');
        $fpdf->RoundedRect($posx, $posy, 195, ($fpdf->getY()+2)-$posy, 2);
        if ($cdo_tipo == "FC") {
            $fpdf->Ln(2);
            $posy = $fpdf->getY() + 1;
            $fpdf->setXY($posx, $posy + 1);
            $fpdf->SetFont('Times', 'B', 10);
            $fpdf->MultiCell(193, 4.5, "DOCUMENTO SOPORTE : ". utf8_decode($strDocumentoSoporte), 0, 'L');
            $fpdf->RoundedRect($posx, $posy, 195, $fpdf->getY()-$posy+1, 2);
            $fpdf->Ln(2);
            $posy = $fpdf->getY();

            $fpdf->SetFont('Times', '', 8);
            $fpdf->setXY($posx, $posy + 0.5);
            $fpdf->MultiCell(195, 3.2, utf8_decode($strCuenta), 0, 'C');
            $fpdf->Rect($posx, $posy, 195, $fpdf->getY()-$posy+1);

            $posy = $fpdf->getY()+1;
            $fpdf->setXY($posx, $posy+0.5);
            $fpdf->SetFont('Times', 'B', 6.5);
            $fpdf->MultiCell(190, 3, utf8_decode($strNota_final), 0, 'L');
            $fpdf->Rect($posx, $posy, 195, $fpdf->getY()-$posy+1);
        }
        $fpdf->posy = $fpdf->getY()+3;

        return ['error' => false, 'pdf' => $fpdf->Output('S')];
    }
}

function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '') {
    $k = $this->k;
    $hp = $this->h;
    if($style=='F')
        $op='f';
    elseif($style=='FD' || $style=='DF')
        $op='B';
    else
        $op='S';
    $MyArc = 4/3 * (sqrt(2) - 1);
    $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
	
	$xc = $x+$w-$r;
    $yc = $y+$r;
    $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
    if (strpos($corners, '2')===false)
        $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
    else
        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

    $xc = $x+$w-$r;
    $yc = $y+$h-$r;
    $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
    if (strpos($corners, '3')===false)
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
    else
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

    $xc = $x+$r;
    $yc = $y+$h-$r;
    $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
    if (strpos($corners, '4')===false)
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
    else
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

    $xc = $x+$r ;
    $yc = $y+$r;
    $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
    if (strpos($corners, '1')===false)
    {
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
        $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
    }
    else
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
    $this->_out($op);
}

function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
    $h = $this->h;
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
        $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
}