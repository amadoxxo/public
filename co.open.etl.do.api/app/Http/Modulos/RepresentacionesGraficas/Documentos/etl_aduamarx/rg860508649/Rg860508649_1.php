<?php
/**
 * User: Juan Jose Trujillo
 * Date: 18/11/19
 * Time: 12:34 PM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_aduamarx\rg860508649;

use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;
use App\Http\Traits\NumToLetrasEngine;

class Rg860508649_1 extends RgBase
{    
    public function getPdf()
    {
        extract($this->getDatos());

        //PDF
        $fpdf = $this->pdfManager();
        $fpdf->AcceptPageBreak();
        $fpdf->SetFont('Courier', '', 8);
        $fpdf->AliasNbPages();
        $fpdf->SetMargins(0, 0, 0);
        $fpdf->SetAutoPageBreak(true, 10);
        
        $datosComprobante['logo']           = $this->getFullImage('logo' . $ofe_identificacion . '.png');
        $datosComprobante['logo_bureau']    = $this->getFullImage('logobureauveritas.png');

        $datosComprobante['no_valido']      = $this->getFullImage("no_valido.png");

        $datosComprobante['cdo_tipo'] = $cdo_tipo;
        $datosComprobante['qr'] = "";
        $datosComprobante['signaturevalue'] = "";
        $datosComprobante['cufe'] = "";
        if ($signaturevalue != '' && $qr != '') {
            $datosComprobante['qr'] = $qr;
            $datosComprobante['cufe'] = $cufe;
            $datosComprobante['signaturevalue'] = $signaturevalue;
        }

        $datosComprobante['razon_ofe'] = "";
        if(isset($ofe_representacion_grafica->razon_ofe) && $ofe_representacion_grafica->razon_ofe != ''){
            $datosComprobante['razon_ofe'] = $ofe_representacion_grafica->razon_ofe;
        }

        $datosComprobante['impo_expo'] = "";
        if(isset($ofe_representacion_grafica->impo_expo) && $ofe_representacion_grafica->impo_expo != ''){
            $datosComprobante['impo_expo'] = $ofe_representacion_grafica->impo_expo;
        }

        $datosComprobante['autorizacion'] = "";
        if(isset($ofe_representacion_grafica->autorizacion) && $ofe_representacion_grafica->autorizacion != ''){    
            $arrConv = array("{res_prefijo}", "{res}", "{res_fecha_desde}");
            $arrRes  = array($ofe_resolucion_prefijo, $ofe_resolucion, date("Y/m/d",strtotime($ofe_resolucion_fecha))); 

            $datosComprobante['autorizacion']  = str_replace($arrConv, $arrRes, $ofe_representacion_grafica->autorizacion);
        }

        $datosComprobante['vigencia'] = "";
        if(isset($ofe_representacion_grafica->vigencia) && $ofe_representacion_grafica->vigencia != ''){
            $date1  = strtotime($ofe_resolucion_fecha);
            $date2  = strtotime($ofe_resolucion_fecha_hasta);
            $diffe  = $date2 - $date1;
            $meses  = round($diffe / (60 * 60 * 24 * 30.5));
    
            $arrConv = array("{meses}", "{res_prefijo}", "{res_desde}", "{res_prefijo}", "{res_hasta}");
            $arrRes  = array($meses, $ofe_resolucion_prefijo, $ofe_resolucion_desde, $ofe_resolucion_prefijo, $ofe_resolucion_hasta); 

            $datosComprobante['vigencia']  = str_replace($arrConv, $arrRes, $ofe_representacion_grafica->vigencia);
        }

        $datosComprobante['nit_ofe'] = "";
        if(isset($ofe_representacion_grafica->nit_ofe) && $ofe_representacion_grafica->nit_ofe != ''){
            $datosComprobante['nit_ofe'] = $ofe_representacion_grafica->nit_ofe;
        }

        $datosComprobante['regimen_comun'] = "";
        if(isset($ofe_representacion_grafica->regimen_comun) && $ofe_representacion_grafica->regimen_comun != ''){
            $datosComprobante['regimen_comun'] = $ofe_representacion_grafica->regimen_comun;
        }

        $datosComprobante['actividad_economica'] = "";
        if(isset($ofe_representacion_grafica->actividad_economica) && $ofe_representacion_grafica->actividad_economica != ''){
            $datosComprobante['actividad_economica'] = $ofe_representacion_grafica->actividad_economica;
        }

        $datosComprobante['agente_retenedor'] = "";
        if(isset($ofe_representacion_grafica->agente_retenedor) && $ofe_representacion_grafica->agente_retenedor != ''){
            $datosComprobante['agente_retenedor'] = $ofe_representacion_grafica->agente_retenedor;
        }

        $datosComprobante['texto_cabecera'] = "";
        if(isset($ofe_representacion_grafica->texto_cabecera) && $ofe_representacion_grafica->texto_cabecera != ''){
            $datosComprobante['texto_cabecera'] = $ofe_representacion_grafica->texto_cabecera;
        }

        $datosComprobante['numero_documento']       = $numero_documento;
        $datosComprobante['fecha_documento']        = $cdo_fecha;
        $datosComprobante['hora_documento']         = $cdo_hora;
        $datosComprobante['adquirente']             = $adquirente;
        $datosComprobante['fecha_vencimiento']      = $fecha_vencimiento;
        $datosComprobante['adq_dir']                = $adq_dir;
        $datosComprobante['adq_tel']                = $adq_tel;
        $datosComprobante['adq_mun']                = $adq_mun;
        $datosComprobante['adq_nit']                = $adq_nit;
        $datosComprobante['razon_social_pt']        = $razon_social_pt;
        $datosComprobante['nit_pt']                 = $nit_pt;

        /** Variables Nota **/
        $datosComprobante['ofe_nit']                = $ofe_nit;
        $datosComprobante['ofe_dir']                = $ofe_dir;
        $datosComprobante['ofe_tel']                = $ofe_tel;
        $datosComprobante['ofe_mun']                = $ofe_mun;
        $datosComprobante['ofe_pais']               = $ofe_pais;
        $datosComprobante['fecha_hora_documento']   = $fecha_hora_documento;

        $datosComprobante['nombre_software'] = "";
        if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
            $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
        }

        $datosComprobante['transportadora'] = "";
        if(isset($cdo_informacion_adicional->transportadora) && $cdo_informacion_adicional->transportadora != ''){
            $datosComprobante['transportadora'] = $cdo_informacion_adicional->transportadora;
        }

        $datosComprobante['fecha_transporte'] = "";
        if(isset($cdo_informacion_adicional->fecha_transporte) && $cdo_informacion_adicional->fecha_transporte != ''){
            $datosComprobante['fecha_transporte'] = $cdo_informacion_adicional->fecha_transporte;
        }

        $datosComprobante['bultos'] = "";
        if(isset($cdo_informacion_adicional->bultos) && $cdo_informacion_adicional->bultos != ''){
            $datosComprobante['bultos'] = $cdo_informacion_adicional->bultos;
        }

        $datosComprobante['codigo_embalaje'] = "";
        if(isset($cdo_informacion_adicional->codigo_embalaje) && $cdo_informacion_adicional->codigo_embalaje != ''){
            $datosComprobante['codigo_embalaje'] = $cdo_informacion_adicional->codigo_embalaje;
        }

        $datosComprobante['peso_bruto'] = "";
        if(isset($cdo_informacion_adicional->peso_bruto) && $cdo_informacion_adicional->peso_bruto != ''){
            $datosComprobante['peso_bruto'] = $cdo_informacion_adicional->peso_bruto;
        }

        $datosComprobante['do'] = "";
        if(isset($cdo_informacion_adicional->do) && $cdo_informacion_adicional->do != ''){
            $datosComprobante['do'] = $cdo_informacion_adicional->do;
        }

        $datosComprobante['documento_transporte'] = "";
        if(isset($cdo_informacion_adicional->documento_transporte) && $cdo_informacion_adicional->documento_transporte != ''){
            $datosComprobante['documento_transporte'] = $cdo_informacion_adicional->documento_transporte;
        }

        /** Fin Variables Nota **/

        // Footer
        $datosComprobante['nota_final_1'] = "";
        if(isset($ofe_representacion_grafica->nota_final_1) && $ofe_representacion_grafica->nota_final_1 != ''){
            $datosComprobante['nota_final_1'] = $ofe_representacion_grafica->nota_final_1;
        }

        $datosComprobante['nota_final_2'] = "";
        if(isset($ofe_representacion_grafica->nota_final_2) && $ofe_representacion_grafica->nota_final_2 != ''){
            $datosComprobante['nota_final_2'] = $ofe_representacion_grafica->nota_final_2;
        }

        //Información de Forma y medios de pago
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
        $fecha_hora = ""; 
        $hora = "";
        if (isset($cdo_fecha_validacion_dian) && $cdo_fecha_validacion_dian != "") {
            $date_time = explode(" ", $cdo_fecha_validacion_dian);
            $time      = explode(" ", $date_time[1]);

            $fecha_hora = $date_time[0];
            $hora       = $time[0];
            $datosComprobante['validacion_dian'] = $fecha_hora ." / ".$time[0];
        }

        try {
            $datosComprobante['observacion_decode'] = (array) json_decode($observacion);
        } catch (\Throwable $th) {
            $datosComprobante['observacion_decode'] = [];
        }

        if($cdo_tipo == 'NC' || $cdo_tipo == 'ND'){
            list($factura_ref, $fecha_ref, $cufe_ref) = $this->getDocumentoReferencia($cdo_documento_referencia);
            $datosComprobante['consecutivo_ref'] = $factura_ref;
            $datosComprobante['fecha_emision']   = $fecha_ref;
            $datosComprobante['cufe_ref']        = $cufe_ref;
        }

        $fpdf->datosComprobante = $datosComprobante;

        /*** Impresion Comprobante. ***/
        $fpdf->AddPage('P', 'Letter');

        $posx = $fpdf->posx;
        $posy = $fpdf->nPosYDet;

        $contItem = 0;

        if($cdo_tipo == 'FC'){

            //Posicion final
            $nPosYFin = 193;
            
            /*** Separo los items en PCC e IP. ***/
            $items_pcc = array_filter($items, function($item){
            return ($item['ddo_tipo_item'] == 'PCC' || $item['ddo_tipo_item'] == 'GMF'); 
            });
            $items_ip = array_filter($items, function($item){
                return ($item['ddo_tipo_item'] == 'IP' || $item['ddo_tipo_item'] == ''); 
            });

            // $items_pcc = array_merge($items_pcc,$items_pcc,$items_pcc);
            // $items_pcc = array_merge($items_pcc,$items_pcc,$items_pcc);
            // $items_pcc = array_merge($items_pcc,$items_pcc,$items_pcc);

            if (isset($items_pcc) && count($items_pcc) > 0) {
                $fpdf->SetFont('Arial','B',8);
                $fpdf->setXY($posx + 30,$posy + 3);
                $fpdf->Cell(60,6,"** PAGOS A TERCEROS ** (NO GRAVABLES)",0,0,'L');

                //Propiedades de la tabla
                $fpdf->SetWidths(array(10, 20, 90, 20, 18, 20, 20));
                $fpdf->SetAligns(array("C", "L", "L", "C", "C", "R", "R"));
                $fpdf->SetLineHeight(4);
                $fpdf->setXY($posx, $posy + 8);

                foreach ($items_pcc as $item) {
                    $contItem++;

                    if ($fpdf->getY() > $nPosYFin) {
                        $fpdf->AddPage('P', 'Letter');
                        $posx = $fpdf->posx;
                        $posy = $fpdf->nPosYDet;
                        $fpdf->setXY($posx, $posy);
                    }

                    $fpdf->setX($posx);
                    $fpdf->SetFont('Arial','',7);
                    $fpdf->Row([
                        number_format($contItem),
                        utf8_decode($item['ddo_codigo']),
                        utf8_decode($item['ddo_descripcion_uno']),
                        utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                        number_format($item['ddo_cantidad'], 0, ',', '.'),
                        number_format($item['ddo_valor_unitario'], 0, ',', '.'),
                        $item['ddo_total'] > 0 ? number_format($item['ddo_total'], 0, ',', '.') : ''
                    ]);
                }

                $posy = $fpdf->GetY();
            }

            if (isset($items_ip) && count($items_ip) > 0) {

                $fpdf->SetFont('Arial','B',8);
                $fpdf->setXY($posx + 30, $posy + 4);
                $fpdf->Cell(60,6,"** SERVICIOS ADUAMAR DE COLOMBIA ** (GRAVABLES)",0,0,'L');

                //Propiedades de la tabla
                $fpdf->SetWidths(array(10, 20, 90, 20, 18, 20, 20));
                $fpdf->SetAligns(array("C", "L", "L", "C", "C", "R", "R"));
                $fpdf->SetLineHeight(4);

                $fpdf->setXY($posx, $posy + 10);
                foreach ($items_ip as $item) {
                    $contItem++;

                    if ($fpdf->getY() > $nPosYFin) {
                        $fpdf->AddPage('P', 'Letter');
                        $posx = $fpdf->posx;
                        $posy = $fpdf->nPosYDet;
                        $fpdf->setXY($posx, $posy);
                    }

                    $fpdf->setX($posx);
                    $fpdf->SetFont('Arial','',7);
                    $fpdf->Row([
                        number_format($contItem),
                        utf8_decode($item['ddo_codigo']),
                        utf8_decode($item['ddo_descripcion_uno']),
                        utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                        number_format($item['ddo_cantidad'], 0, ',', '.'),
                        number_format($item['ddo_valor_unitario'], 0, ',', '.'),
                        $item['ddo_total'] > 0 ? number_format($item['ddo_total'], 0, ',', '.') : ''
                    ]);
                }
            }

            $fpdf->setXY($posx+2, $fpdf->getY() + 2);
            $fpdf->Cell(6,4,$contItem,'T',0,'C');

            //Inicializo la posicion Y
            $posy = 213;

            //Declaro variables
            $intSubtotal = $this->parserNumberController($subtotal);
            $intIva      = $this->parserNumberController($iva);
            $intAnticipo = $this->parserNumberController($cdo_anticipo);
            $nTotalRetefuente = 0;
            $nTotalReteica    = 0;
            $nTotalReteiva    = 0;

            $fpdf->setY($posy-6);

            $data = $this->getCargoDescuentosRetencionesTipo($cdo_id, self::MODO_CONSULTA_CABECERA, self::MODO_PORCENTAJE_DETALLAR);

            foreach($data as $retencion => $grupo){
                foreach ($grupo as $porcentaje => $valores){

                    $fpdf->setX($posx);
                    $fpdf->SetFont('Arial', '', 7);
                    $fpdf->Cell(37,5, "Tarifas: ". $porcentaje ."% - Valor Base: ". number_format($valores['base'], 0, ',', '.') ." - Valor Retenido ". number_format($valores['valor'], 0, ',', '.'),0,0,'L');
                    $fpdf->Ln(3);

                    switch ($retencion) {
                        case "RETEFUENTE":
                            $nTotalRetefuente += $valores['valor'];
                        break;
                        case "RETEIVA":
                            $nTotalReteiva += $valores['valor'];
                        break;
                        case "RETEICA":
                            $nTotalReteica += $valores['valor'];
                        break;
                        default:
                        //no hace nada
                        break;   
                    }
                }
            }

            $nAnticipo_recibido = 0;
            if(isset($cdo_informacion_adicional->anticipo_recibido) && $cdo_informacion_adicional->anticipo_recibido != ''){
                $nAnticipo_recibido = $cdo_informacion_adicional->anticipo_recibido;
            } else {
                $nAnticipo_recibido = $this->parserNumberController($cdo_anticipo);
            }

            $nTotal_pagar = 0;
            if(isset($cdo_informacion_adicional->total_pagar) && $cdo_informacion_adicional->total_pagar != ''){
                $nTotal_pagar = $cdo_informacion_adicional->total_pagar;
            }else {
                $valor_a_pagar = $this->parserNumberController($valor_a_pagar);
            }

            $nSaldoFavor = 0;
            if(isset($cdo_informacion_adicional->saldo_a_favor) && $cdo_informacion_adicional->saldo_a_favor != ""){
                $nSaldoFavor = $cdo_informacion_adicional->saldo_a_favor;
            }


            $nTotalFactura = $this->parserNumberController($intSubtotal) + $this->parserNumberController($intIva) - ($nTotalRetefuente + $nTotalReteiva + $nTotalReteica);
            $nTotalPagar   = $nTotalFactura - $intAnticipo;

            ## TOTALES ##
            $fpdf->setXY($posx+168,$posy-1);
            $fpdf->SetFont('Arial','',8);
            $fpdf->Cell(30,4, number_format($intSubtotal, 0, ',', '.'),0,0,'R');
            $fpdf->Ln(6);
            $fpdf->setX($posx+168);
            $fpdf->Cell(30,4, number_format($intIva, 0, ',', '.'),0,0,'R');

            $fpdf->Ln(6);
            $fpdf->setX($posx+168);
            $fpdf->Cell(30,4, number_format($nTotalReteiva, 0, ',', '.'),0,0,'R');

            $fpdf->Ln(6);
            $fpdf->setX($posx+168);
            $fpdf->Cell(30,4, number_format($nTotalReteica, 0, ',', '.'),0,0,'R');

            $fpdf->Ln(6);
            $fpdf->setX($posx+168);
            $fpdf->Cell(30,4, number_format($nTotalRetefuente, 0, ',', '.'),0,0,'R');

            $fpdf->Ln(6);
            $fpdf->setX($posx+168);
            $fpdf->Cell(30,4, number_format($nTotalFactura, 0, ',', '.'),0,0,'R');

            $fpdf->Ln(6);
            $fpdf->setX($posx+168);
            $fpdf->Cell(30,4, number_format($intAnticipo, 0, ',', '.'),0,0,'R');

            $fpdf->Ln(6);
            $fpdf->setX($posx+168);
            $fpdf->Cell(30,4, ($nTotalPagar > 0) ? number_format($nTotalPagar, 0, ',', '.') : "0,00",0,0,'R');

            $fpdf->Ln(6);
            $fpdf->setX($posx+168);
            $fpdf->Cell(30,5, number_format($nSaldoFavor, 0, ',', '.'),0,0,'R');

        }elseif($cdo_tipo == 'NC' || $cdo_tipo == 'ND'){

            $datosComprobante['nota_final'] = "";
            if(isset($ofe_representacion_grafica->nota_final) && $ofe_representacion_grafica->nota_final != ''){
                $datosComprobante['nota_final'] = $ofe_representacion_grafica->nota_final;
            }

            //Posicion final
            $nPosYFin = 210;

            // $items = array_merge($items,$items,$items);
            // $items = array_merge($items,$items,$items,$items);
            // $items = array_merge($items,$items,$items,$items,$items);

            // Items
            if (isset($items) && count($items) > 0) {
                //Propiedades de la tabla
                $fpdf->SetTextColor(0);
                $fpdf->SetFont('Arial', '', 8);
                $fpdf->SetWidths(array(20, 29, 64, 22, 20, 22, 22));
                $fpdf->SetAligns(array("C", "C", "L", "C", "C", "R", "R"));
                $fpdf->SetLineHeight(4.5);
                $fpdf->setY($posy);
        
                foreach ($items as $item) {
                    $contItem++;
                    if ($fpdf->getY() > $nPosYFin) {
                        $fpdf->Rect($posx, $posy - 6, 199, 220 - $posy + 6);
                        $fpdf->AddPage('P', 'Letter');
                        $posx = $fpdf->posx;
                        $posy = $fpdf->nPosYDet;
                        $fpdf->setXY($posx, $posy);
                    }

                    $fpdf->setX($posx);
                    $fpdf->Row(array(
                        $contItem,
                        $item['ddo_codigo'],
                        utf8_decode($item['ddo_descripcion_uno']),
                        utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                        number_format($item['ddo_cantidad'], 0, ',', '.'),
                        number_format($item['ddo_valor_unitario'], 0, ',', '.'),
                        number_format($item['ddo_total'], 0, ',', '.')
                    ));
                }
            }

            $fpdf->setXY($posx + 5, $fpdf->getY() + 3);
            $fpdf->Cell(10,5,$contItem,'T',0,'C');

            $nPyyFin = 166;
            if ($fpdf->getY() > $nPyyFin) {
                $fpdf->Rect($posx, $posy - 6, 199, 220 - $posy + 6);
                $fpdf->AddPage('P', 'Letter');
            }
            $fpdf->Rect($posx, $posy-6, 199, 170-$posy+6);

            $intSubtotal = $this->parserNumberController($subtotal);
            $intIva      = $this->parserNumberController($iva);
            $nTotalRetefuente = 0;
            $nTotalReteica    = 0;
            $nTotalReteiva    = 0;

            $data = $this->getCargoDescuentosRetencionesTipo($cdo_id, self::MODO_CONSULTA_CABECERA, self::MODO_PORCENTAJE_DETALLAR);

            foreach($data as $retencion => $grupo){
                foreach ($grupo as $porcentaje => $valores){

                    switch ($retencion) {
                        case "RETEFUENTE":
                            $nTotalRetefuente += $valores['valor'];
                        break;
                        case "RETEIVA":
                            $nTotalReteiva += $valores['valor'];
                        break;
                        case "RETEICA":
                            $nTotalReteica += $valores['valor'];
                        break;
                        default:
                        //no hace nada
                        break;   
                    }
                }
            }

            $nTotalFactura = $this->parserNumberController($intSubtotal) + $this->parserNumberController($intIva) - ($nTotalRetefuente + $nTotalReteiva + $nTotalReteica);
            $valor_letras = NumToLetrasEngine::num2letras($this->parserNumberController($nTotalFactura), false, true, $cdo_moneda);

            ## Totales ##
            $posy = 170;
            $fpdf->setXY($posx, $posy+1);
            $fpdf->SetFont('Arial', 'B', 9);
            $fpdf->Cell(49,4,"SON: ",0,0,'L');
            $fpdf->SetFont('Arial', '', 8);
            $fpdf->Ln(0.5);
            $fpdf->setX($posx+10);
            $fpdf->MultiCell(112,3, utf8_decode($valor_letras),0,'L');

            $fpdf->setXY($posx+133, $posy+3);
            $fpdf->SetFont('Arial', 'B', 9.5);
            $fpdf->Cell(33,5,"SUBTOTAL $",0,0,'L');
            $fpdf->SetFont('Arial', '', 9);
            $fpdf->Cell(33,5, number_format($intSubtotal, 0, ',', '.'),0,0,'R');
            $fpdf->Ln(9);
            $fpdf->setX($posx+133);
            $fpdf->SetFont('Arial', 'B', 9.5);
            $fpdf->Cell(33,5,"IVA  ".number_format($porcentaje_iva, 0). "%",0,0,'L');
            $fpdf->SetFont('Arial', '', 9);
            $fpdf->Cell(33,5, number_format($intIva, 0, ',', '.'),0,0,'R');
            $fpdf->Ln(9);
            $fpdf->setX($posx+133);
            $fpdf->SetFont('Arial', 'B', 9.5);
            $fpdf->Cell(33,5,"RETEFUENTE",0,0,'L');
            $fpdf->SetFont('Arial', '', 9);
            $fpdf->Cell(33,5, number_format($nTotalRetefuente, 0, ',', '.'),0,0,'R');
            $fpdf->Ln(9);
            $fpdf->setX($posx+133);
            $fpdf->SetFont('Arial', 'B', 9.5);
            $fpdf->Cell(33,5,"RETE IVA",0,0,'L');
            $fpdf->SetFont('Arial', '', 9);
            $fpdf->Cell(33,5, number_format($nTotalReteiva, 0, ',', '.'),0,0,'R');
            $fpdf->Ln(9);
            $fpdf->setX($posx+133);
            $fpdf->SetFont('Arial', 'B', 9.5);
            $fpdf->Cell(33,5,"RETE ICA",0,0,'L');
            $fpdf->SetFont('Arial', '', 9);
            $fpdf->Cell(33,5, number_format($nTotalReteica, 0, ',', '.'),0,0,'R');
            $fpdf->Ln(9);
            $fpdf->setX($posx+133);
            $fpdf->SetFont('Arial', 'B', 9.5);
            $fpdf->Cell(33,5,"TOTAL $",0,0,'L');
            $fpdf->SetFont('Arial', '', 9);
            $fpdf->Cell(33,5, number_format($nTotalFactura, 0, ',', '.'),0,0,'R');

            $fpdf->setXY($posx, $posy+10.5);
            $fpdf->SetFont('Arial', 'B', 9);
            $fpdf->Cell(49,5, utf8_decode("Motivo Correción: "),0,0,'L');
            $fpdf->SetFont('Arial', '', 8);
            $fpdf->Ln(4.5);
            $fpdf->setX($posx);
            $fpdf->MultiCell(80,3,utf8_decode(implode("\n", $datosComprobante['observacion_decode'])),0,'L');

            $fpdf->setXY($posx, $posy+30);
            $fpdf->SetFont('Arial', '', 6);
            $fpdf->MultiCell(128,3.5, utf8_decode($datosComprobante['nota_final']),0,'J');
            
            $fpdf->Line($posx+133, $posy, $posx+133, $posy+55);

            $fpdf->Line($posx, $posy+10, $posx+199, $posy+10);
            $fpdf->Line($posx+133, $posy+19, $posx+199, $posy+19);
            $fpdf->Line($posx, $posy+28, $posx+199, $posy+28);
            $fpdf->Line($posx+133, $posy+37, $posx+199, $posy+37);
            $fpdf->Line($posx+133, $posy+46, $posx+199, $posy+46);

            $fpdf->Rect($posx, $posy, 199, 55);

            $fpdf->setXY($posx, $posy+57);
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(27,5, utf8_decode("Fecha de Validación "),0,0,'L');
            $fpdf->SetFont('Arial', '', 7);
            $fpdf->Cell(20,5, $fecha_hora,0,0,'L');
            $fpdf->setXY($posx+55, $posy+57);
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(27,5, utf8_decode("Hora de Validación "),0,0,'L');
            $fpdf->SetFont('Arial', '', 7);
            $fpdf->Cell(20,5, $hora,0,0,'L');

        }

        return ['error' => false, 'pdf' => $fpdf->Output('S')];
    }
}