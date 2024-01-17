<?php
/**
 * Created by PhpStorm.
 * User: Juan Jose Trujillo
 * Date: 24/05/19
 * Time: 10:45 a.m
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_dhlexcxx\rg830076778;

use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Rg830076778_8 extends RgBase
{

    public function getPdf() {
        extract($this->getDatos());

        // FPDF
        $fpdf = $this->pdfManager();
        $fpdf->AcceptPageBreak();
        $fpdf->SetFont('Arial','',8);
        $fpdf->AliasNbPages();
        $fpdf->SetMargins(0,0,0);
        $fpdf->SetAutoPageBreak(true,10);
        
        $fpdf->setImageHeader($this->getFullImage('logo'.$ofe_identificacion.'.png'));

        $datosComprobante['agencia'] = "";
        if(isset($ofe_representacion_grafica->agencia) && $ofe_representacion_grafica->agencia != ""){
            $datosComprobante['agencia'] = $ofe_representacion_grafica->agencia;
        }

        $datosComprobante['resolucion'] = [];
            if(isset($ofe_representacion_grafica->resolucion) && $ofe_representacion_grafica->resolucion != ""){

            $res_meses = Carbon::parse($ofe_resolucion_fecha_hasta)->diffInMonths($ofe_resolucion_fecha);
            $arrConv = array("{res}", "{res_fecha_desde}", "{res_desde}", "{res_hasta}", "{prefijo}", "{res_meses}");
            $arrRes  = array($ofe_resolucion, $ofe_resolucion_fecha, $ofe_resolucion_desde, $ofe_resolucion_hasta, $ofe_resolucion_prefijo, $res_meses);

            $ofe_representacion_grafica->resolucion = str_replace($arrConv, $arrRes, $ofe_representacion_grafica->resolucion);
            $datosComprobante['resolucion'] = explode('||', $ofe_representacion_grafica->resolucion);
        }

        $datosComprobante['facturacion_electronica'] = "";
        if(isset($ofe_representacion_grafica->facturacion_electronica) && $ofe_representacion_grafica->facturacion_electronica != ""){
            $datosComprobante['facturacion_electronica'] = $ofe_representacion_grafica->facturacion_electronica;
        }

        $datosComprobante['regimen'] = [];
        if(isset($ofe_representacion_grafica->regimen) && $ofe_representacion_grafica->regimen != ""){
            $datosComprobante['regimen'] = explode("||", $ofe_representacion_grafica->regimen);
        }

        $datosComprobante['do'] = "";
        if(isset($cdo_informacion_adicional->do) && $cdo_informacion_adicional->do != ""){
            $datosComprobante['do'] = $cdo_informacion_adicional->do;
        }

        $datosComprobante['valor_cif'] = 0;
        if(isset($cdo_informacion_adicional->valor_cif) && is_numeric($cdo_informacion_adicional->valor_cif)){
            $datosComprobante['valor_cif'] = $cdo_informacion_adicional->valor_cif;
        }

        $datosComprobante['guia'] = "";
        if(isset($cdo_informacion_adicional->guia) && $cdo_informacion_adicional->guia != ""){
            $datosComprobante['guia'] = $cdo_informacion_adicional->guia;
        }

        $datosComprobante['trm'] = 0;
        if(isset($cdo_informacion_adicional->trm) && is_numeric($cdo_informacion_adicional->trm)){
            $datosComprobante['trm'] = $cdo_informacion_adicional->trm;
        }

        $datosComprobante['codigo_barras'] = "";
        if(isset($cdo_informacion_adicional->codigo_barras) && $cdo_informacion_adicional->codigo_barras != ""){
            $datosComprobante['codigo_barras'] = $cdo_informacion_adicional->codigo_barras;
        }

        $datosComprobante['observaciones'] = "";
        if(isset($cdo_informacion_adicional->observaciones) && $cdo_informacion_adicional->observaciones != ""){
            $datosComprobante['observaciones'] = $cdo_informacion_adicional->observaciones;
        }

        $datosComprobante['estimado_cliente'] = "";
        if(isset($cdo_informacion_adicional->estimado_cliente) && $cdo_informacion_adicional->estimado_cliente != ""){
            $datosComprobante['estimado_cliente'] = $cdo_informacion_adicional->estimado_cliente;
        }

        $datosComprobante['contactenos'] = [];
        if(isset($cdo_informacion_adicional->contactenos) && $cdo_informacion_adicional->contactenos != ""){
            $datosComprobante['contactenos'] = explode("||", $cdo_informacion_adicional->contactenos);
        }

        $datosComprobante['titulo'] = "";
        if(isset($cdo_informacion_adicional->titulo) && $cdo_informacion_adicional->titulo != ""){
            $datosComprobante['titulo'] = $cdo_informacion_adicional->titulo;
        }

        $datosComprobante['contenido'] = "";
        if(isset($cdo_informacion_adicional->contenido) && $cdo_informacion_adicional->contenido != ""){
            $datosComprobante['contenido'] = str_replace(array("// ", " //", " // "), "\n", $cdo_informacion_adicional->contenido);
        }

        //Encabezado
        $nit = explode('-', $ofe_nit);
        $datosComprobante['oferente']               = $oferente;
        $datosComprobante['ofe_nit']                = $nit[0]; 
        $datosComprobante['ofe_nit_consecutivo']    = $nit[1];
        $datosComprobante['ofe_dir']                = $ofe_dir;
        $datosComprobante['ofe_tel']                = $ofe_tel;
        $datosComprobante['ofe_mun']                = $ofe_mun;
        $datosComprobante['numero_documento']       = $numero_documento; 
        $datosComprobante['fecha_hora_documento']   = $fecha_hora_documento; 
        $datosComprobante['fecha_vencimiento']      = $fecha_vencimiento;
        $datosComprobante['cdo_hora']               = $cdo_hora;
        $datosComprobante['adquirente']             = $adquirente;
        $datosComprobante['adq_nit']                = ($tdo_codigo == "31" ? $adq_nit : $adq_nit_sin_digito);
        $datosComprobante['adq_dir']                = $adq_dir;
        $datosComprobante['adq_tel']                = $adq_tel;
        $datosComprobante['adq_mun']                = $adq_mun;
        $datosComprobante['tdo_codigo']             = $tdo_codigo;
        $datosComprobante['tdo_descripcion']        = $tdo_descripcion;
        $datosComprobante['fecha_validacion']       = "";

        if (isset($cdo_fecha_validacion_dian) && $cdo_fecha_validacion_dian != "") {
            $datosComprobante['fecha_validacion'] = substr($cdo_fecha_validacion_dian,0,16);
        }

        if($tdo_codigo == '42')
            $datosComprobante['tdo_descripcion']  = "Doc. Extranjería";

        // Informacion del PT.
        $datosComprobante['razon_social_pt']        = $razon_social_pt;
        $datosComprobante['nit_pt']                 = $nit_pt;

        $datosComprobante['nombre_software'] = "";
        if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
            $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
        }

        //Extrayendo información de Forma y medios de pago
        $datosComprobante['forma_pago'] = "";
        $datosComprobante['medio_pago'] = "";
        foreach ($medios_pagos_documento as $key => $medios_pagos){
            //Forma
            $forma = $medios_pagos['forma'];
            $datosComprobante['forma_pago'] = mb_strtoupper($forma['fpa_descripcion']);
            //Medio
            $medio = $medios_pagos['medio'];
            $datosComprobante['medio_pago'] = (isset($medio['mpa_descripcion']) && $medio['mpa_descripcion'] != '') ? mb_strtoupper($medio['mpa_descripcion']) : '';
        }

        $datosComprobante['cdo_tipo']               = $cdo_tipo;
        $datosComprobante['qr']                     = "";
        $datosComprobante['signaturevalue']         = "";
        $datosComprobante['cufe']                   = $cufe;
        if($signaturevalue != '' && $qr !=''){
            $datosComprobante['qr']                 = $qr;
            $datosComprobante['signaturevalue']     = $signaturevalue;
        }

        try {
            $datosComprobante['observacion'] = (array) json_decode($observacion);
        } catch (\Throwable $th) {
            $datosComprobante['observacion'] = [];
        }
    
        $datosComprobante['bandera_fondo'] = "SI";

        $datosComprobante['razon_nota_credito'] = "";
        if(isset($cdo_informacion_adicional->razon_nota_credito) && $cdo_informacion_adicional->razon_nota_credito != ""){
            $datosComprobante['razon_nota_credito'] = $cdo_informacion_adicional->razon_nota_credito;
        }

        if ($cdo_tipo == 'NC' || $cdo_tipo == 'ND') {
            list($factura_ref, $fecha_ref, $cufe_ref) = $this->getDocumentoReferencia($cdo_documento_referencia);
            $datosComprobante['numero_documento_ref'] = $factura_ref;
            $datosComprobante['fecha_emision'] = $fecha_ref;
            $datosComprobante['cufe_ref'] = $cufe_ref;
        }

        $datosComprobante['anticipo_recibido'] = 0;
        if(isset($cdo_informacion_adicional->anticipo_recibido) && is_numeric($cdo_informacion_adicional->anticipo_recibido)){
            $datosComprobante['anticipo_recibido'] = floatval($cdo_informacion_adicional->anticipo_recibido);
        }

        $fpdf->datosComprobante = $datosComprobante;

        if ($cdo_tipo == "FC") {

            $strDosIncluidos = "";
            if(isset($cdo_informacion_adicional->dos_incluidos) && $cdo_informacion_adicional->dos_incluidos != ""){
                $strDosIncluidos = $cdo_informacion_adicional->dos_incluidos;
            }

            $strCampo1Descripcion = "";
            if(isset($cdo_informacion_adicional->campo_1['descripcion']) && $cdo_informacion_adicional->campo_1['descripcion'] != ""){
                $strCampo1Descripcion = $cdo_informacion_adicional->campo_1['descripcion'];
            }

            $strCampo1Valor = "";
            if(isset($cdo_informacion_adicional->campo_1['valor']) && $cdo_informacion_adicional->campo_1['valor'] != ""){
                $strCampo1Valor = $cdo_informacion_adicional->campo_1['valor'];
            }

            $strCampo2Descripcion = "";
            if(isset($cdo_informacion_adicional->campo_2['descripcion']) && $cdo_informacion_adicional->campo_2['descripcion'] != ""){
                $strCampo2Descripcion = $cdo_informacion_adicional->campo_2['descripcion'];
            }

            $strCampo2Valor = "";
            if(isset($cdo_informacion_adicional->campo_2['valor']) && $cdo_informacion_adicional->campo_2['valor'] != ""){
                $strCampo2Valor = $cdo_informacion_adicional->campo_2['valor'];
            }

            $strCampo3Descripcion = "";
            if(isset($cdo_informacion_adicional->campo_3['descripcion']) && $cdo_informacion_adicional->campo_3['descripcion'] != ""){
                $strCampo3Descripcion = $cdo_informacion_adicional->campo_3['descripcion'];
            }

            $strCampo3Valor = "";
            if(isset($cdo_informacion_adicional->campo_3['valor']) && $cdo_informacion_adicional->campo_3['valor'] != ""){
                $strCampo3Valor = $cdo_informacion_adicional->campo_3['valor'];
            }

            $strCampo4Descripcion = "";
            if(isset($cdo_informacion_adicional->campo_4['descripcion']) && $cdo_informacion_adicional->campo_4['descripcion'] != ""){
                $strCampo4Descripcion = $cdo_informacion_adicional->campo_4['descripcion'];
            }

            $strCampo4Valor = "";
            if(isset($cdo_informacion_adicional->campo_4['valor']) && $cdo_informacion_adicional->campo_4['valor'] != ""){
                $strCampo4Valor = $cdo_informacion_adicional->campo_4['valor'];
            }
            
            $intReteIva = 0;
            if(isset($cdo_informacion_adicional->reteiva) && is_numeric($cdo_informacion_adicional->reteiva)){
                $intReteIva = $cdo_informacion_adicional->reteiva;
            }

            $intReteIca = 0;
            if(isset($cdo_informacion_adicional->reteica) && is_numeric($cdo_informacion_adicional->reteica)){
                $intReteIca = $cdo_informacion_adicional->reteica;
            }

            $intSaldoFavor = 0;
            if(isset($cdo_informacion_adicional->saldo_a_favor) && is_numeric($cdo_informacion_adicional->saldo_a_favor)){
                $intSaldoFavor = $cdo_informacion_adicional->saldo_a_favor;
            }

            $intSaldoCargo = 0;
            if(isset($cdo_informacion_adicional->saldo_cargo) && is_numeric($cdo_informacion_adicional->saldo_cargo)){
                $intSaldoCargo = $cdo_informacion_adicional->saldo_cargo;
            }
            /*** Impresion Comprobante. ***/
            $fpdf->AddPage('P','Letter');

            $posx = $fpdf->posx;
            $posy = $fpdf->nPosYIni;
            $posfin = 172;
            $contItem = 0;

            /*** Separo los items en PCC e IP. ***/
            $items_pcc = array_filter($items, function($item){
                return ($item['ddo_tipo_item'] == 'PCC' || $item['ddo_tipo_item'] == 'IPT' || $item['ddo_tipo_item'] == 'GMF'); 
            });
            $items_ip = array_filter($items, function($item){
                return ($item['ddo_tipo_item'] == 'IP' || $item['ddo_tipo_item'] == ''); ; 
            });


            // $items_pcc = array_merge($items_pcc,$items_pcc,$items_pcc,$items_pcc);
            // $items_pcc = array_merge($items_pcc,$items_pcc,$items_pcc,$items_pcc);
            // $items_ip = array_merge($items_ip,$items_ip,$items_ip);

            ## Propiedades de la tabla
            $fpdf->SetWidths(array(8,22,73,19,17,27,29));
            $fpdf->SetAligns(array("C","C","L","C","C","R","R"));
            $fpdf->SetLineHeight(4);

            ## Items PCC
            if(isset($items_pcc) && count($items_pcc) > 0){

                $fpdf->SetFont('Arial','B',7);
                $fpdf->setXY($posx+8,$posy);
                $fpdf->Cell(50,4,"PAGO A TERCEROS",0,0,'L');
                
                $fpdf->SetFont('Arial','',7);
                $fpdf->setXY($posx,$posy+4);
                
                foreach ($items_pcc as $item) {
                    if($fpdf->GetY() > $posfin){
                        $fpdf->AddPage('P','Letter');
                        $posx = $fpdf->posx;
                        $posy = $fpdf->nPosYIni;
                        $fpdf->setXY($posx,$posy);
                    }

                    // Contar Item
                    $contItem++;
                    
                    $fpdf->setX($posx);
                    $fpdf->Row(array(
                        $contItem,
                        $item['ddo_codigo'],
                        utf8_decode($item['ddo_descripcion_dos']),
                        $this->getUnidad($item['und_id'], 'und_descripcion'),
                        number_format($item['ddo_cantidad'],0,',','.'),
                        number_format($item['ddo_valor_unitario'],0,',','.'),
                        number_format($item['ddo_total'],0,',','.')
                    ));
                }
                $posy = $fpdf->getY()+5;
            }

            ## Items IP
            if(isset($items_ip) && count($items_ip) > 0){

                $fpdf->SetFont('Arial','B',7);
                $fpdf->setXY($posx+8,$posy);
                $fpdf->Cell(50,4,"INGRESOS PROPIOS",0,0,'L');
                
                $fpdf->SetFont('Arial','',7);
                $fpdf->setXY($posx,$posy+4);
                
                foreach ($items_ip as $item) {
                    if($fpdf->GetY() > $posfin){
                        $fpdf->AddPage('P','Letter');
                        $posx = $fpdf->posx;
                        $posy = $fpdf->nPosYIni;
                        $fpdf->setXY($posx,$posy);
                    }

                    // Contar Item
                    $contItem++;
                    
                    $fpdf->setX($posx);
                    $fpdf->Row(array(
                        $contItem,
                        $item['ddo_codigo'],
                        utf8_decode($item['ddo_descripcion_dos']),
                        $this->getUnidad($item['und_id'], 'und_descripcion'),
                        number_format($item['ddo_cantidad'],0,',','.'),
                        number_format($item['ddo_valor_unitario'],0,',','.'),
                        number_format($item['ddo_total'],0,',','.')
                    ));
                }
            }

            $fpdf->setXY($posx+1, $fpdf->GetY());
            $fpdf->Cell(6,4,$contItem,'T',0,'C');
            $fpdf->Ln(4);

            if($fpdf->GetY() > 150){
                $fpdf->AddPage('P','Letter');
            }

            $posy = $fpdf->GetY()+7;
            $fpdf->SetFont('Arial','',7);
            $fpdf->setXY($posx+10,$posy);
            if(trim($strCampo1Descripcion) != "" && trim($strCampo1Valor) != ""){
                $fpdf->setX($posx+10);
                $fpdf->Cell(60,4,$strCampo1Descripcion.": ".$strCampo1Valor,0,0,'L');
                $fpdf->ln(4);
            }

            if(trim($strCampo2Descripcion) != "" && trim($strCampo2Valor) != ""){
                $fpdf->setX($posx+10);
                $fpdf->Cell(60,4,$strCampo2Descripcion.": ".$strCampo2Valor,0,0,'L');
                $fpdf->ln(4);
            }

            if(trim($strCampo3Descripcion) != "" && trim($strCampo3Valor) != ""){
                $fpdf->setX($posx+10);
                $fpdf->Cell(60,4,$strCampo3Descripcion.": ".$strCampo3Valor,0,0,'L');
                $fpdf->ln(4);
            }

            if(trim($strCampo4Descripcion) != "" && trim($strCampo4Valor) != ""){
                $fpdf->setX($posx+10);
                $fpdf->Cell(60,4,$strCampo4Descripcion.": ".$strCampo4Valor,0,0,'L');
            }

            if(isset($strDosIncluidos) && $strDosIncluidos != ""){
                $fpdf->ln(6);
                $fpdf->setX($posx+8);
                $fpdf->SetFont('Arial','B',7);
                $fpdf->Cell(60,4,"Numeros DO incluidos en esta factura",0,0,'L');
                $fpdf->ln(4);
                $fpdf->setX($posx+10);
                $fpdf->SetFont('Arial','',7);
                $fpdf->MultiCell(143,4,$strDosIncluidos,0,'L');
            }

            /*** Valor Totales ***/
            $datosComprobante['cdo_anticipo']   = number_format($this->parserNumberController($cdo_anticipo),0,',',',');
            $datosComprobante['subtotal']       = number_format($this->parserNumberController($subtotal),0,',',',');
            $datosComprobante['iva']            = number_format($this->parserNumberController($iva),0,',',',');
            $datosComprobante['reteiva']        = number_format($intReteIva,0,',',',');
            $datosComprobante['reteica']        = number_format($intReteIca,0,',',',');
            $datosComprobante['total']          = $this->parserNumberController($subtotal) + $this->parserNumberController($iva);
            $datosComprobante['total_pagar']    = number_format($intSaldoCargo, 0,',',',');
            $datosComprobante['saldo_a_favor']  = floatval($intSaldoFavor);

            /*** Seteo Bandera para no pintar los Totales***/
            $datosComprobante['bandera_fondo'] = "NO";
            $fpdf->datosComprobante = $datosComprobante;
            
        }elseif($cdo_tipo == "NC"){

            if ($cdo_tipo == 'NC' || $cdo_tipo == 'ND') {
                list($factura_ref, $fecha_ref, $cufe_ref) = $this->getDocumentoReferencia($cdo_documento_referencia);
                $consecutivo_ref = $factura_ref;
                $fecha_emision = $fecha_ref;
                $cufe_ref = $cufe_ref;
            }

            /*** Impresion Comprobante. ***/
            $fpdf->AddPage('P','Letter');

            $posx = $fpdf->posx;
            $posy = $fpdf->nPosYIni;
            $posfin = 185;

            /*** Separo los items en PCC e IP. ***/
            $items_pcc = array_filter($items, function($item){
                return ($item['ddo_tipo_item'] == 'PCC' || $item['ddo_tipo_item'] == 'IPT' || $item['ddo_tipo_item'] == 'GMF');
            });
            $items_ip = array_filter($items, function($item){
                return ($item['ddo_tipo_item'] == 'IP' || $item['ddo_tipo_item'] == ''); 
            });

            // $items_pcc = array_merge($items_pcc,$items_pcc,$items_pcc);
            // $items_pcc = array_merge($items_pcc,$items_pcc,$items_pcc);
            // $items_pcc = array_merge($items_pcc,$items_pcc,$items_pcc);

            ## Imprimiendo Items
            $contItem = 0;
            if(isset($items_pcc) && count($items_pcc) > 0){
                $fpdf->SetFont('Arial','B',8);
                $fpdf->setXY($posx + 5, $posy);
                $fpdf->Cell(60,6,"PAGO A TERCEROS",0,0,'L');

                $fpdf->SetWidths(array(10, 23, 71, 20, 10, 28, 33));
                $fpdf->SetAligns(array("C", "C", "L", "C", "C", "R", "R"));
                $fpdf->SetLineHeight(4);
                $fpdf->SetFont('Arial','',8);
                $fpdf->setXY($posx,$posy+5);

                foreach ($items_pcc as $item) {
                    $contItem++;
                    if($fpdf->getY() > $posfin){
                        $fpdf->AddPage('P','Letter');
                        $posx = $fpdf->posx;
                        $posy = $fpdf->nPosYIni;
                        $fpdf->setXY($posx,$posy+3);
                    }

                    $fpdf->setX($posx);
                    $fpdf->SetFont('Arial','',8);
                    $fpdf->Row(array($contItem,
                                    $item['ddo_codigo'],
                                    utf8_decode($item['ddo_descripcion_dos']),
                                    utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                                    number_format($item['ddo_cantidad'], 0),
                                    number_format($item['ddo_valor_unitario'], 0, '.', ','),
                                    number_format($item['ddo_total'], 0, '.', ',')
                    ));
                }
                $posy = $fpdf->GetY()+2;
            }

            if(isset($items_ip) && count($items_ip) > 0){
                $fpdf->SetFont('Arial','B',8);
                $fpdf->setXY($posx + 5, $posy);
                $fpdf->Cell(60,6,"INGRESOS PROPIOS",0,0,'L');

                $fpdf->SetWidths(array(10, 23, 71, 20, 10, 28, 33));
                $fpdf->SetAligns(array("C", "C", "L", "C", "C", "R", "R"));
                $fpdf->SetLineHeight(4);
                $fpdf->SetFont('Arial','',8);
                $fpdf->setXY($posx,$posy+5);

                foreach ($items_ip as $item) {
                    $contItem++;
                    if($fpdf->getY() > $posfin){
                        $fpdf->AddPage('P','Letter');
                        $posx = $fpdf->posx;
                        $posy = $fpdf->nPosYIni;
                        $fpdf->setXY($posx,$posy+3);
                    }
                
                    $fpdf->setX($posx);
                    $fpdf->SetFont('Arial','',8);
                    $fpdf->Row(array($contItem,
                                    $item['ddo_codigo'],
                                    utf8_decode($item['ddo_descripcion_dos']),
                                    utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                                    number_format($item['ddo_cantidad'], 0),
                                    number_format($item['ddo_valor_unitario'], 0, '.', ','),
                                    number_format($item['ddo_total'], 0, '.', ',')
                    ));
                }
            }

            $fpdf->setX($posx+2);
            $fpdf->Cell(6,4,$contItem,'T',0,'C');

            /*** Valor Totales ***/ 
            $nValorIvaCOP   = 0;
            foreach ($impuestos_items as $impuesto) {
                if ($impuesto['iid_valor'] > 0 && $impuesto['iid_tipo'] == "TRIBUTO") {
                    $nValorIvaCOP += $impuesto['iid_valor'];
                }
            }

            $posy = $fpdf->posy+195;
            $fpdf->SetDrawColor(94,93,93);

            $fpdf->setXY($posx+5,$posy);
            $fpdf->SetFont('Arial','B',7);
            $fpdf->Cell(80,3,"FV: ".$consecutivo_ref,0,0,'L');
            $fpdf->setXY($posx+5,$posy+5);
            $fpdf->Cell(80,3,utf8_decode("Fecha Expedición: ").$fecha_emision,0,0,'L');
            $fpdf->setXY($posx+5,$posy+10);
            $fpdf->MultiCell(80,3,"CUFE: ".$cufe_ref,0,'L');

            $fpdf->setXY($posx+117,$posy);
            $fpdf->SetFont('Arial','B',7);
            $fpdf->SetFillColor(206, 210, 225);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->Cell(78,5,"",1,0,'L',true);
            $fpdf->ln(5);

            ## SubTotal
            $fpdf->setX($posx+122);
            $fpdf->SetFont('Arial','B',8);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->Cell(40,5,"SubTotal",0,0,'L');
            $fpdf->SetFont('Arial','',8);
            $fpdf->SetFillColor(250, 196, 196);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->Cell(33,5,number_format($this->parserNumberController($subtotal),0,',','.'),1,0,'R',true);
            $fpdf->ln(5);

            ## IVA
            $fpdf->setX($posx+122);
            $fpdf->SetFont('Arial','B',8);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->Cell(40,5,"IVA",0,0,'L');
            $fpdf->SetFont('Arial','',8);
            $fpdf->SetFillColor(206, 210, 225);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->Cell(33,5,number_format($this->parserNumberController($iva),0,',','.'),1,0,'R',true);
            $fpdf->ln(5);

            ## VALOR TOTAL DE
            $fpdf->setX($posx+117);
            $fpdf->SetFont('Arial','B',7);
            $fpdf->SetFillColor(206, 210, 225);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->Cell(45,5,"VALOR TOTAL DE",1,0,'R',true);
            $fpdf->SetFont('Arial','',8);
            $fpdf->SetFillColor(250, 196, 196);            
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->Cell(33,5,number_format($this->parserNumberController($total),0,',','.'),1,0,'R',true);

            /*** Seteo Bandera para no pintar fondo de los Totales***/
            $datosComprobante['bandera_fondo'] = "NO";
            $fpdf->datosComprobante = $datosComprobante;

        }
        return ['error' => false, 'pdf' => $fpdf->Output('S')];
    }
}