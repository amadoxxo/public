<?php
/**
 * User: Sebastian Cardenas Suarez
 * Date: 15/12/2021
 * Time: 04:08 PM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_beltranx\rg800094624;

use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;
use App\Http\Modulos\RepresentacionesGraficas\BarcodeGs1128\GenerarCodigoGs1128;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use App\Http\Modulos\Documentos\EtlCargosDescuentosDocumentosDaop\EtlCargosDescuentosDocumentoDaop;
use App\Http\Traits\NumToLetrasEngine;
use Illuminate\Support\Facades\File;

class Rg800094624_1 extends RgBase
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

        // $fpdf->setImageHeader($this->getFullImage('logo' . $ofe_identificacion . '.png'));
        $datosComprobante['no_valido'] = $this->getFullImage("no_valido.png");

        $datosComprobante['qr'] = "";
        $datosComprobante['signaturevalue'] = "";
        $datosComprobante['cufe'] = "";
        if (!empty($signaturevalue) && !empty($qr)) {
            $datosComprobante['qr'] = $qr;
            $datosComprobante['cufe'] = $cufe;
            $datosComprobante['signaturevalue'] = $signaturevalue;
        }

        $datosComprobante['cdo_tipo']               = $cdo_tipo;
        $datosComprobante['cdo_tipo_nombre']        = $cdo_tipo_nombre;
        $datosComprobante['cdo_fecha']              = $cdo_fecha;
        $cdo_fecha                                  = explode("-", $cdo_fecha);
        $datosComprobante['adquirente']             = $adquirente;
        $datosComprobante['adq_nit']                = $adq_nit;
        $datosComprobante['adq_dir']                = $adq_dir;
        $datosComprobante['adq_tel']                = $adq_tel;
        $datosComprobante['adq_mun']                = $adq_mun;

        $datosComprobante['numero_documento']       = $numero_documento;
        $datosComprobante['oferente']               = $oferente;
        $nit = explode("-",$ofe_nit);
        $datosComprobante['ofe_nit']                = number_format($nit[0],0,'.','.') ."-". $nit[1];

        $datosComprobante['razon_social_pt']        = $razon_social_pt;
        $datosComprobante['nit_pt']                 = $nit_pt;

        $datosComprobante['nombre_software'] = "";
        if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
            $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
        }

        //Información de Forma y medios de pago
        $strFormaPago = "";
        foreach ($medios_pagos_documento as $key => $medios_pagos){
            //Forma
            $forma = $medios_pagos['forma'];
            $strFormaPago = utf8_decode(strtolower($forma['fpa_descripcion']));
        }

        $datosComprobante['FechaValidacionDian'] = "";
        if (isset($cdo_fecha_validacion_dian) && $cdo_fecha_validacion_dian != "") {
            $datosComprobante['FechaValidacionDian'] = $cdo_fecha_validacion_dian;
        }

        try {
            $datosComprobante['observacion'] = (array) json_decode($observacion);
        } catch (\Throwable $th) {
            $datosComprobante['observacion'] = [];
        }

        if($cdo_tipo == 'NC' || $cdo_tipo == 'ND'){
            list($factura_ref, $fecha_ref, $cufe_ref) = $this->getDocumentoReferencia($cdo_documento_referencia);
            $datosComprobante['factura_ref']     = $factura_ref;
            $datosComprobante['fecha_emision']   = $fecha_ref;
            $datosComprobante['cufe_ref']        = $cufe_ref;
        }

        $fpdf->datosComprobante = $datosComprobante;

        $strResolucion = "";
        if(isset($ofe_representacion_grafica->resolucion) && $ofe_representacion_grafica->resolucion != ""){

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
                $ofe_resolucion_fecha,
                $ofe_resolucion_prefijo, 
                $ofe_resolucion_desde, 
                $ofe_resolucion_hasta,
                $meses
            );
            $ofe_representacion_grafica->resolucion = str_replace($arrConv, $arrRes, $ofe_representacion_grafica->resolucion);
            $strResolucion = $ofe_representacion_grafica->resolucion;
        }

        $strNotaFinal = "";
        if(isset($ofe_representacion_grafica->nota_final) && $ofe_representacion_grafica->nota_final != ""){
            $strNotaFinal = $ofe_representacion_grafica->nota_final;
        }

        $strCodigoBarras = "";
        if(isset($cdo_informacion_adicional->codigo_barras) && $cdo_informacion_adicional->codigo_barras != ""){
            $strCodigoBarras = $cdo_informacion_adicional->codigo_barras;
        }
        $strBanco = "";
        if(isset($cdo_informacion_adicional->banco) && $cdo_informacion_adicional->banco != ""){
            $strBanco = $cdo_informacion_adicional->banco;
        }

        $strCodigoFormato = "";
        if(isset($cdo_informacion_adicional->codigo_formato) && $cdo_informacion_adicional->codigo_formato != ""){
            $strCodigoFormato = $cdo_informacion_adicional->codigo_formato;
        }

        $strReferenciaPago = "";
        if(isset($cdo_informacion_adicional->referencia_pago) && $cdo_informacion_adicional->referencia_pago != ""){
            $strReferenciaPago = $cdo_informacion_adicional->referencia_pago;
        }

        // Totales Retenciones
        $intTotalReteIva  = 0;
        $intTotalReteIca  = 0;
        $intTotalReteFte  = 0;
        $data = $this->getCargoDescuentosRetencionesTipo($cdo_id, self::MODO_CONSULTA_CABECERA, self::MODO_PORCENTAJE_DETALLAR);
        foreach($data as $retencion => $grupo){
            foreach ($grupo as $porcentaje => $valores){
                switch($retencion){
                    case 'RETEIVA':
                        $intTotalReteIva += $valores['valor'];
                    break;
                    case 'RETEICA':
                        $intTotalReteIca += $valores['valor'];
                    break;
                    case 'RETEFUENTE':
                        $intTotalReteFte += $valores['valor'];
                    break;
                    default:
                    break;
                }
            }
        }
        // unset($items[0]);
        // $items = array_merge($items,$items,$items,$items);
        // $items = array_merge($items,$items,$items,$items);
        // $items = array_merge($items,$items,$items,$items);

        /*** Separo los items en IP y AIU ***/
        foreach ($items as $item) {
            if ($item['ddo_notas'] == '[]'){
                $items_IP[] = $item;
            }else{
                $items_AIU[] = $item;
            }
        }
        /*** Fin Separo los items en IP y AIU ***/

        // Items
        $contItem = 0;
        
        if ($cdo_tipo == "FC") {

            /*** Impresion Comprobante. ***/
            $posx = 16;
            $posy = 10;
            $nPosyFinItems = 0;

            $fpdf->AddPage('P','Letter');

            if ($signaturevalue == '' && $qr == '') {
                $fpdf->Image($datosComprobante['no_valido'], 20, 50, 180, 180);
            }

            $fpdf->SetFont('Arial', '', 6);
            $fpdf->TextWithDirection(11,200,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): ".$razon_social_pt." NIT: ".$nit_pt." NOMBRE DEL SOFTWARE: ".$datosComprobante['nombre_software']),'U');

            $fpdf->SetFont('Arial', 'B', 10);
            $fpdf->TextWithDirection(203,100,utf8_decode("USUARIO"),'D');

            // informacion ofe
            $fpdf->setXY($posx + 52,$posy);
            $fpdf->SetFont('Arial','B',10);
            $fpdf->MultiCell(75,4,utf8_decode(strtoupper($oferente)),0,'C');
            $fpdf->setX($posx + 59);
            $fpdf->MultiCell(60,4,"NIT. ".$datosComprobante['ofe_nit'],0,'C');
            $fpdf->setX($posx + 59);
            $fpdf->MultiCell(60,4,utf8_decode("TEL: ".$ofe_tel),0,'C');

            //Logos
            // $fpdf->Image($this->getFullImage('logo'.$ofe_identificacion.'.png'), $posx, $posy-6, 35);

            // resolucion
            $fpdf->setXY($posx+140, $posy+8);
            $fpdf->SetFont('Arial','B',8);
            $fpdf->MultiCell(40,3.5,"FACTURA ELECTRONICA\nDE VENTA",0,'C');
            $fpdf->Ln(0.5);
            $fpdf->setX($posx+140);
            $fpdf->MultiCell(40,3.5,$rfa_prefijo." ".$cdo_consecutivo,1,'C');
            $fpdf->Ln(1.5);
            $fpdf->setX($posx);
            $fpdf->SetFont('Arial','B',7.5);
            $fpdf->MultiCell(181,3.5,utf8_decode($strResolucion),0,'C');

            $posy = 35;
            $fpdf->setXY($posx+1,$posy+1);
            $fpdf->SetFont('Arial','B',8);
            $fpdf->Cell(32,7,utf8_decode("Fecha de Expedición"),0,0,'L');
            $fpdf->Cell(7,7,$cdo_fecha[2],1,0,'C');
            $fpdf->Cell(2,7,"",0,0,'C');
            $fpdf->Cell(7,7,$cdo_fecha[1],1,0,'C');
            $fpdf->Cell(2,7,"",0,0,'C');
            $fpdf->Cell(14,7,$cdo_fecha[0],1,0,'C');
            $fpdf->Cell(5,7,"",0,0,'C');
            $fpdf->SetFont('Arial','',8);
            $fpdf->Cell(20,3.5,utf8_decode("Hora Emisión"),0,0,'L');
            $fpdf->SetFont('Arial','B',8);
            $fpdf->Ln(3.5);
            $fpdf->setX($posx+70);
            $fpdf->Cell(20,3.5,$cdo_hora,0,0,'L');

            $fpdf->setXY($posx+104,$posy+1);
            $fpdf->SetFont('Arial','B',8);
            $fpdf->Cell(31,7,utf8_decode("Fecha de vencimiento:"),0,0,'L');
            $fpdf->Cell(47,7,$fecha_vencimiento,0,0,'R');

            $fpdf->setXY($posx+1,$posy+11);
            $fpdf->SetFont('Arial','B',8);
            $fpdf->Cell(32,4,utf8_decode("Tercero"),0,0,'L');
            $fpdf->SetFont('Arial','',8);
            $fpdf->MultiCell(83,4,utf8_decode($adquirente),0,'L');
            $fpdf->Ln(1);
            $fpdf->setX($posx+1);
            $fpdf->SetFont('Arial','B',8);
            $fpdf->Cell(16,4,utf8_decode("Dirección:"),0,0,'L');
            $fpdf->SetFont('Arial','',8);
            $fpdf->MultiCell(70,4,utf8_decode($adq_dir),0,'L');
            $fpdf->Ln(1);
            $fpdf->setX($posx+1);
            $fpdf->SetFont('Arial','B',8);
            $fpdf->Cell(24,4,utf8_decode("Forma de Pago:"),0,0,'L');
            $fpdf->SetFont('Arial','',8);
            $fpdf->Cell(13,4,"Contado",0,0,'L');
            $fpdf->Cell(5,4,($strFormaPago == "contado") ? "X" : "",1,0,'C');
            $fpdf->Cell(3,4,"",0,0,'L');
            $fpdf->Cell(12,4,utf8_decode("Crédito"),0,0,'L');
            $fpdf->Cell(5,4,($strFormaPago == "credito") ? "X" : "",1,0,'C');
            $fpdf->Ln(6);
            $nPosy = $fpdf->getY();
            $fpdf->Rect($posx+1,$posy,181,$nPosy-$posy);

            $fpdf->setXY($posx+115,$posy+11);
            $fpdf->SetFont('Arial','',8);
            $fpdf->Cell(7,4,"NIT",0,0,'L');
            $fpdf->Cell(5,4,($tdo_codigo == '31') ? "X" : "",1,0,'C');
            $fpdf->Cell(1,4,"",0,0,'L');
            $fpdf->Cell(7,4,"C.C.",0,0,'L');
            $fpdf->Cell(5,4,($tdo_codigo == '13') ? "X" : "",1,0,'C');
            $fpdf->Cell(1,4,"",0,0,'L');
            $fpdf->Cell(20,4,$adq_nit,0,0,'L');
            $fpdf->Ln(4);
            $fpdf->setX($posx+94);
            $fpdf->SetFont('Arial','B',8);
            $fpdf->Cell(14,4,"Telefono: ",0,0,'L');
            $fpdf->SetFont('Arial','',8);
            $fpdf->Cell(18,4,$adq_tel,0,0,'L');

            $posy = $nPosy;
            $fpdf->SetFont('Arial','B',8);
            $fpdf->setXY($posx+1,$posy);
            $fpdf->Cell(10,6,"ITEM",1,0,'C');
            $fpdf->Cell(20,6,"CODIGO",1,0,'C');
            $fpdf->Cell(71,6,utf8_decode("CONCEPTO"),1,0,'C');
            $fpdf->Cell(15,6,"UNIDAD",1,0,'C');
            $fpdf->Cell(25,6,"VAL. UNIT.",1,0,'C');
            $fpdf->Cell(15,6,"CANT.",1,0,'C');
            $fpdf->Cell(25,6,"VALOR TOTAL",1,0,'C');
            $posy += 7;
            $posx += 1;
            $posyIni = $posy;

            if (isset($items_IP) && count($items_IP) > 0) {
                //Propiedades de la tabla
                $fpdf->SetWidths(array(10, 20, 71, 15, 25, 15, 25));
                $fpdf->SetAligns(array("C", "C", "L", "C", "R", "R", "R"));
                $fpdf->SetLineHeight(4);
                $fpdf->SetFont('Arial', '', 7.5);
                $fpdf->setXY($posx, $posy);

                foreach ($items_IP as $item) {
                    $contItem++;

                    $fpdf->setX($posx);
                    $fpdf->Row(array(
                        number_format($contItem),
                        utf8_decode($item['ddo_codigo']),
                        utf8_decode($item['ddo_descripcion_uno']),
                        utf8_decode($this->getUnidad($item['und_id'])),
                        number_format($item['ddo_valor_unitario'], 2, '.', ','),
                        number_format($item['ddo_cantidad'], 2, '.', ','),
                        number_format($item['ddo_total'], 2, '.', ',')
                    ));
                }
                $posyIni = $fpdf->getY();
            }

            if (isset($items_AIU) && count($items_AIU) > 0) {
                //Propiedades de la tabla
                $fpdf->SetWidths(array(10, 20, 71, 15, 25, 15, 25));
                $fpdf->SetAligns(array("C", "C", "L", "C", "R", "R", "R"));
                $fpdf->SetLineHeight(3.5);
                $fpdf->SetFont('Arial', '', 7.5);
                $fpdf->setXY($posx, $posyIni);

                foreach ($items_AIU as $item) {
                    if ($fpdf->GetY() > 180) {
                        //Rectangulo Items
                        $fpdf->Rect($posx, $posy-1, 181, ($nPosyFin - $posy)+1);
                        $fpdf->Line($posx + 10, $posy-1, $posx + 10, $nPosyFin);
                        $fpdf->Line($posx + 30, $posy-1, $posx + 30, $nPosyFin);
                        $fpdf->Line($posx + 101, $posy-1, $posx + 101, $nPosyFin);
                        $fpdf->Line($posx + 116, $posy-1, $posx + 116, $nPosyFin);
                        $fpdf->Line($posx + 141, $posy-1, $posx + 141, $nPosyFin);
                        $fpdf->Line($posx + 156, $posy-1, $posx + 156, $nPosyFin);
                        $fpdf->AddPage('P', 'Letter');
                        $fpdf->setXY($posx, $fpdf->posy + 6);
                        $posy = $fpdf->posy + 5;
                    }
                    $contItem++;

                    $fpdf->setX($posx);
                    $fpdf->Row(array(
                        number_format($contItem),
                        utf8_decode($item['ddo_codigo']),
                        utf8_decode($item['ddo_descripcion_uno']),
                        utf8_decode($this->getUnidad($item['und_id'])),
                        number_format($item['ddo_valor_unitario'], 2, '.', ','),
                        number_format($item['ddo_cantidad'], 2, '.', ','),
                        number_format($item['ddo_total'], 2, '.', ',')
                    ));
                }
            }

            $nPosyFin = $fpdf->getY();

            //Rectangulo Items
            $fpdf->Rect($posx, $posy-1, 181, ($nPosyFin - $posy)+1);
            $fpdf->Line($posx + 10, $posy-1, $posx + 10, $nPosyFin);
            $fpdf->Line($posx + 30, $posy-1, $posx + 30, $nPosyFin);
            $fpdf->Line($posx + 101, $posy-1, $posx + 101, $nPosyFin);
            $fpdf->Line($posx + 116, $posy-1, $posx + 116, $nPosyFin);
            $fpdf->Line($posx + 141, $posy-1, $posx + 141, $nPosyFin);
            $fpdf->Line($posx + 156, $posy-1, $posx + 156, $nPosyFin);

            $posy = $nPosyFin;

            $fpdf->setXY($posx+1, $posy+1);
            $fpdf->SetFont('Arial', '', 7);
            $fpdf->Cell(116,4,"Total Items: ".$contItem,0,0,'L');
            $fpdf->Ln(5);
            $fpdf->setX($posx);
            $fpdf->MultiCell(116,3.5,utf8_decode(implode("\n",$datosComprobante['observacion'])),0,'L');
            $fpdf->Rect($posx, $posy, 116, 24);

            // totales
            $intTotal = ($this->parserNumberController($subtotal) + $this->parserNumberController($iva)) - ($intTotalReteFte + $intTotalReteIva + $intTotalReteIca + $cdo_descuentos);
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->setXY($posx+116, $posy);
            $fpdf->Cell(40,4,"SUBTOTAL:","1",0,'R');
            $fpdf->Cell(25,4,"$".number_format($this->parserNumberController($subtotal), 2, '.', ','),"1",0,'R');
            $fpdf->Ln(4);
            $fpdf->setX($posx+116);
            $fpdf->Cell(40,4,"IVA 19%:","1",0,'R');
            $fpdf->Cell(25,4,"$".number_format($this->parserNumberController($iva), 2, '.', ','),"1",0,'R');
            $fpdf->Ln(4);
            $fpdf->setX($posx+116);
            $fpdf->Cell(40,4,"DESCUENTO:","1",0,'R');
            $fpdf->Cell(25,4,"$".number_format($cdo_descuentos, 2, '.', ','),"1",0,'R');
            $fpdf->Ln(4);
            $fpdf->setX($posx+116);
            $fpdf->Cell(40,4,"RETENCION EN LA FUENTE","1",0,'R');
            $fpdf->Cell(25,4,"$".number_format($intTotalReteFte, 2, '.', ','),"1",0,'R');
            $fpdf->Ln(4);
            $fpdf->setX($posx+116);
            $fpdf->Cell(40,4,"RETENCION IVA","1",0,'R');
            $fpdf->Cell(25,4,"$".number_format($intTotalReteIva, 2, '.', ','),"1",0,'R');
            $fpdf->Ln(4);
            $fpdf->setX($posx+116);
            $fpdf->Cell(40,4,"RETENCION ICA","1",0,'R');
            $fpdf->Cell(25,4,"$".number_format($intTotalReteIca, 2, '.', ','),"1",0,'R');  
            $fpdf->Ln(4);
            $fpdf->setX($posx+116);
            $fpdf->SetFillColor(170);
            $fpdf->Cell(40,4,"TOTAL","1",0,'R');
            $fpdf->Cell(25,4,"$".number_format($intTotal, 2, '.', ','),"1",0,'R');
            $fpdf->Ln(4);

            $posy = $fpdf->getY();
            $strValorLetras = NumToLetrasEngine::num3letras(number_format($intTotal, 2, '.', ''), false, true, 'COP');
            $fpdf->SetFont('Arial', 'B', 8);
            $fpdf->setXY($posx, $posy+1);
            $fpdf->MultiCell(181,4,"son: ".$strValorLetras,0,'L');
            $fpdf->Rect($posx, $posy, 181, $fpdf->getY()-$posy+1);

            $posy = $fpdf->getY()+1;
            $fpdf->SetFont('Arial', 'B', 8);
            $fpdf->setXY($posx, $posy+1);
            $fpdf->Cell(35,4,"Pagar en Banco:",0,0,'R');
            $fpdf->SetFont('Arial', '', 8);
            $fpdf->MultiCell(140,4,utf8_decode($strBanco),0,'L');
            $fpdf->Rect($posx, $posy, 181, $fpdf->getY()-$posy+1);

            $posy = $fpdf->getY()+0.5;
            $fpdf->SetFont('Arial', 'B', 7.5);
            $fpdf->setXY($posx, $posy+1);
            $fpdf->MultiCell(181,4,utf8_decode($strNotaFinal),0,'C');

            $posy += 6;
            $fpdf->setXY($posx, $posy);
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->MultiCell(100,4,utf8_decode("FECHA Y HORA DE VALIDACIÓN DIAN: ".$datosComprobante['FechaValidacionDian']),0,'L');
            $fpdf->Ln(1);
            if($signaturevalue != "" && $qr != ""){
                $dataURI = "data:image/png;base64, ".base64_encode((string) \QrCode::format('png')->size(82)->margin(0)->generate($qr));
                $pic = $fpdf->getImage($dataURI);
                if ($pic!==false) $fpdf->Image($pic[0], $posx + 145, $posy,33,33, $pic[1]);
                $fpdf->setX($posx);
                $fpdf->MultiCell(100,4,utf8_decode("REPRESENTACION IMPRESA DE LA FACTURA ELECTRÓNICA"),0,'L');
                $fpdf->Ln(1);
                $fpdf->setX($posx);
                $fpdf->SetFont('Arial', '', 6);
                $fpdf->MultiCell(140,4,utf8_decode("Firma Electrónica"),0,'L');
                $fpdf->setX($posx);
                $fpdf->MultiCell(140,3,$signaturevalue,0,'J');
                $fpdf->Ln(1);
                $fpdf->setX($posx);
                $fpdf->SetFont('Arial','B',7);
                $fpdf->Cell(160,3, ($cdo_tipo == "FC") ? "CUFE:" : "CUDE:",0,0,'L');
                $fpdf->Ln(4);
                $fpdf->setX($posx);
                $fpdf->SetFont('Arial','',7);
                $fpdf->MultiCell(140,2,utf8_decode($cufe),0,'L');
                $nPosyFinItems = $nPosyFin;
                $nPosyFin = $fpdf->getY();
            }
            // Imprime el desprendible si se envía el codigo de barras y referencia de pago
            if($strCodigoBarras != "" && $strReferenciaPago != ""){
                if( $nPosyFinItems > 135){
                    $fpdf->AddPage('P', 'Letter');
                    $fpdf->setXY($posx, $fpdf->posy + 6);
                    $posy = $fpdf->posy + 5;
                }
                $posy = 210;
                $fpdf->SetDash(1,1);
                $fpdf->Line($posx,$posy,$posx+181,$posy);
                $fpdf->SetDash(0,0);
                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->TextWithDirection(203,$posy+32,utf8_decode("BANCO"),'D');

                $fpdf->setXY($posx + 27,$posy+4);
                $fpdf->SetFont('Arial','B',8);
                $fpdf->MultiCell(77,3.7,utf8_decode($oferente),0,'C');
                $fpdf->SetFont('Arial','',8);
                $fpdf->Ln(1);
                $fpdf->setX($posx + 27);
                $fpdf->MultiCell(77,3.5,"Nit. ".$datosComprobante['ofe_nit'],0,'C');
                $fpdf->setX($posx + 27);
                $fpdf->MultiCell(77,3.5,"Dir. ".utf8_decode($ofe_dir),0,'C');
                $fpdf->setX($posx + 27);
                $fpdf->MultiCell(77,3.5," Tel(s): ".$ofe_tel,0,'C');

                $fpdf->setXY($posx + 104.5,$posy+5);
                $fpdf->SetFont('Arial','B',9);
                $fpdf->MultiCell(40,3.5,utf8_decode("FACTURA ELETRONICA\nDE VENTA"),0,'L');
                $fpdf->setXY($posx + 144,$fpdf->getY()-7);
                $fpdf->SetFont('Arial','B',10);
                $fpdf->MultiCell(37,7,$rfa_prefijo." ".$cdo_consecutivo,0,'C');

                $fpdf->setXY($posx+104.5,$posy+15);
                $fpdf->SetFont('Arial','B',9);
                $fpdf->Cell(39,7,utf8_decode("  Fecha de Expedición"),0,0,'L');
                $fpdf->Cell(8,7,$cdo_fecha[2],1,0,'C');
                $fpdf->Cell(2,7,"",0,0,'C');
                $fpdf->Cell(8,7,$cdo_fecha[1],1,0,'C');
                $fpdf->Cell(2,7,"",0,0,'C');
                $fpdf->Cell(16,7,$cdo_fecha[0],1,0,'C');
                $fpdf->Rect($posx+104,$posy+3,78,21);

                $fpdf->setXY($posx + 30,$posy+28);
                $fpdf->SetFont('Arial','B',12);
                $fpdf->MultiCell(121,3.5,"REFERENCIA DE PAGO No. ".$strReferenciaPago,0,'C');
                $pathCodigo = storage_path() . '/etl/descargas/'.$cdo_tipo.$rfa_prefijo.$cdo_consecutivo.'.png';
                GenerarCodigoGs1128::generarCodigoGs1128($pathCodigo, $strCodigoBarras);
                $contenidoCodigo = File::get($pathCodigo);
                @unlink($pathCodigo);
                $dataURI = 'data:image/png;base64,' . base64_encode($contenidoCodigo);
                $pic = $fpdf->getImage($dataURI);
                if ($pic !== false) {
                    $fpdf->Image($pic[0],$posx+30,$posy+34,121,20, $pic[1]);
                    $fpdf->setXY($posx, $posy + 56);
                    $fpdf->SetFont('Arial', '', 7);
                    $fpdf->Cell(180, 3, utf8_decode(str_replace("~F1", " ", $strCodigoBarras)), 0, 0, 'C');
                }
                $posy = $fpdf->getY()-2;
                $fpdf->setXY($posx, $posy);
                $fpdf->SetFont('Arial', '', 6);
                $fpdf->Cell(80,3,utf8_decode($strCodigoFormato),0,0,'L');
            }
        } else {

            $posfin = 159;

            //Inicializo variables y configuraciones para empezar a pintar
            $fpdf->AddPage('P', 'Letter');
            $posy = $fpdf->posy+1;
            $posx = $fpdf->posx;
            $fpdf->setXY($posx, $posy + 5);
            $fpdf->SetLineWidth(0.3);

            if (isset($items_IP) && count($items_IP) > 0) {
                $fpdf->setX($posx + 20);
                $fpdf->SetFont('Arial', 'BI', 6);
                //Anchos de las columnas
                $fpdf->SetWidths(array(10, 16, 16, 77, 16, 30, 30));
                //Alineacion de las columnas
                $fpdf->SetAligns(array("C", "C", "C", "L", "C", "R", "R"));
                $fpdf->SetLineHeight(3);

                $fpdf->SetFont('Arial', '', 7);
                foreach ($items_IP as $item) {
                    //Si no cabe el siguiente item creo otra pagina
                    if ($fpdf->GetY() > $posfin + 50) {
                        $fpdf->Rect($posx, $fpdf->posy, 195, $posfin - $fpdf->posy + 55);
                        $fpdf->Line($posx + 10, $fpdf->posy, $posx + 10, $posfin + 55);
                        $fpdf->Line($posx + 26, $fpdf->posy, $posx + 26, $posfin + 55);
                        $fpdf->Line($posx + 42, $fpdf->posy, $posx + 42, $posfin + 55);
                        $fpdf->Line($posx + 119, $fpdf->posy, $posx + 119, $posfin + 55);
                        $fpdf->Line($posx + 135, $fpdf->posy, $posx + 135, $posfin + 55);
                        $fpdf->Line($posx + 165, $fpdf->posy, $posx + 165, $posfin + 55);
                        $fpdf->AddPage('P', 'Letter');
                        $fpdf->setXY($posx, $fpdf->posy + 6);
                        $posy = $fpdf->posy + 5;
                    }

                    $contItem ++;
                    //Pinto la linea
                    $fpdf->setX($posx);
                    $fpdf->Row(array(
                        $contItem,
                        utf8_decode($item['ddo_codigo']),
                        number_format($item['ddo_cantidad'], 0, '.', ','),
                        utf8_decode($item['ddo_descripcion_uno']),
                        utf8_decode($this->getUnidad($item['und_id'])),
                        number_format($item['ddo_valor_unitario'], 2, '.', ','),
                        number_format($item['ddo_total'], 2, '.', ','),
                    ));
                }
            }

            if (isset($items_AIU) && count($items_AIU) > 0) {
                $fpdf->setX($posx + 20);
                $fpdf->SetFont('Arial', 'BI', 6);
                //Anchos de las columnas
                $fpdf->SetWidths(array(10, 16, 16, 77, 16, 30, 30));
                //Alineacion de las columnas
                $fpdf->SetAligns(array("C", "C", "C", "L", "C", "R", "R"));
                $fpdf->SetLineHeight(3);

                $fpdf->SetFont('Arial', '', 7);
                foreach ($items_AIU as $item) {
                    //Si no cabe el siguiente item creo otra pagina
                    if ($fpdf->GetY() > $posfin + 50) {
                        $fpdf->Rect($posx, $fpdf->posy, 195, $posfin - $fpdf->posy + 55);
                        $fpdf->Line($posx + 10, $fpdf->posy, $posx + 10, $posfin + 55);
                        $fpdf->Line($posx + 26, $fpdf->posy, $posx + 26, $posfin + 55);
                        $fpdf->Line($posx + 42, $fpdf->posy, $posx + 42, $posfin + 55);
                        $fpdf->Line($posx + 119, $fpdf->posy, $posx + 119, $posfin + 55);
                        $fpdf->Line($posx + 135, $fpdf->posy, $posx + 135, $posfin + 55);
                        $fpdf->Line($posx + 165, $fpdf->posy, $posx + 165, $posfin + 55);
                        $fpdf->AddPage('P', 'Letter');
                        $fpdf->setXY($posx, $fpdf->posy + 6);
                        $posy = $fpdf->posy + 5;
                    }

                    $contItem ++;
                    //Pinto la linea
                    $fpdf->setX($posx);
                    $fpdf->Row(array(
                        $contItem,
                        utf8_decode($item['ddo_codigo']),
                        number_format($item['ddo_cantidad'], 0, '.', ','),
                        utf8_decode($item['ddo_descripcion_uno']),
                        utf8_decode($this->getUnidad($item['und_id'])),
                        number_format($item['ddo_valor_unitario'], 2, '.', ','),
                        number_format($item['ddo_total'], 2, '.', ','),
                    ));
                }
            }

            if ($fpdf->GetY() > $posfin) {
                $fpdf->Rect($posx, $fpdf->posy, 195, $posfin - $fpdf->posy + 32);
                $fpdf->Line($posx + 10, $fpdf->posy, $posx + 10, $posfin + 32);
                $fpdf->Line($posx + 26, $fpdf->posy, $posx + 26, $posfin + 32);
                $fpdf->Line($posx + 42, $fpdf->posy, $posx + 42, $posfin + 32);
                $fpdf->Line($posx + 119, $fpdf->posy, $posx + 119, $posfin + 32);
                $fpdf->Line($posx + 135, $fpdf->posy, $posx + 135, $posfin + 32);
                $fpdf->Line($posx + 165, $fpdf->posy, $posx + 165, $posfin + 32);
                $fpdf->AddPage('P', 'Letter');
                $fpdf->setXY($posx, $fpdf->posy + 5);
                $posy = $fpdf->posy + 5;
            }

            //Hago el rectangulo y las lineas de la pagina final
            $fpdf->Rect($posx, $fpdf->posy, 195, $fpdf->GetY() - $fpdf->posy);
            $fpdf->Line($posx + 10, $fpdf->posy, $posx + 10, $fpdf->GetY());
            $fpdf->Line($posx + 26, $fpdf->posy, $posx + 26, $fpdf->GetY());
            $fpdf->Line($posx + 42, $fpdf->posy, $posx + 42, $fpdf->GetY());
            $fpdf->Line($posx + 119, $fpdf->posy, $posx + 119, $fpdf->GetY());
            $fpdf->Line($posx + 135, $fpdf->posy, $posx + 135, $fpdf->GetY());
            $fpdf->Line($posx + 165, $fpdf->posy, $posx + 165, $fpdf->GetY());

            $posy = $fpdf->getY();
            //Cantidad de Items
            $fpdf->setXY($posx + 3, $posy + 2);
            $fpdf->Cell(10, 6, "Total Item: " . $contItem, 0, 0, 'C');
            $fpdf->ln(5);
            $fpdf->SetFont('Arial', '', 8);
            $fpdf->setX($posx);
            $fpdf->MultiCell(135,3.5,utf8_decode(implode("\n",$datosComprobante['observacion'])),0,'L');

            $intTotal = ($this->parserNumberController($subtotal) + $this->parserNumberController($iva)) - ($intTotalReteFte + $intTotalReteIva + $intTotalReteIca + $cdo_descuentos);
            $fpdf->setXY($posx + 135, $posy);
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(30, 5, utf8_decode("SUBTOTAL"), 1, 0, 'R');
            $fpdf->Cell(30, 5, number_format($this->parserNumberController($subtotal), 2, '.', ','), 1, 0, 'R');
            $fpdf->ln(5);
            $fpdf->setX($posx + 135);
            $fpdf->Cell(30, 5, utf8_decode("IVA"), 1, 0, 'R');
            $fpdf->Cell(30, 5, number_format($this->parserNumberController($iva), 2, '.', ','), 1, 0, 'R');
            $fpdf->ln(5);
            $fpdf->setX($posx + 135);
            $fpdf->SetTextColor(250,46,46);
            $fpdf->Cell(30, 5, utf8_decode("(-) DESCUENTOS"), 1, 0, 'R');
            $fpdf->Cell(30, 5, number_format($cdo_descuentos, 2, '.', ','), 1, 0, 'R');
            $fpdf->SetTextColor(0);
            $fpdf->ln(5);
            $fpdf->setX($posx + 135);
            $fpdf->SetFont('Arial', 'B', 6);
            $fpdf->Cell(30, 5, utf8_decode("RETENCION EN LA FUENTE"), 1, 0, 'R');
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(30, 5, number_format($intTotalReteFte, 2, '.', ','), 1, 0, 'R');
            $fpdf->ln(5);
            $fpdf->setX($posx + 135);
            $fpdf->SetFont('Arial', 'B', 6);
            $fpdf->Cell(30, 5, utf8_decode("RETENCION IVA"), 1, 0, 'R');
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(30, 5, number_format($intTotalReteIva, 2, '.', ','), 1, 0, 'R');
            $fpdf->ln(5);
            $fpdf->setX($posx + 135);
            $fpdf->SetFont('Arial', 'B', 6);
            $fpdf->Cell(30, 5, utf8_decode("RETENCION ICA"), 1, 0, 'R');
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(30, 5, number_format($intTotalReteIca, 2, '.', ','), 1, 0, 'R');
            $fpdf->ln(5);
            $fpdf->setX($posx + 135);
            $fpdf->Cell(30, 5, utf8_decode("TOTAL"), 1, 0, 'R');
            $fpdf->Cell(30, 5, number_format($intTotal, 2, '.', ','), 1, 0, 'R');
            $fpdf->ln(5);
            $fpdf->Rect($posx,$posy,195,$fpdf->getY() - $posy);

            $posy = $fpdf->getY();
            $strValorLetras = NumToLetrasEngine::num3letras(number_format($intTotal, 2, '.', ''), false, true, 'COP');
            $fpdf->setXY($posx, $posy+1);
            $fpdf->SetFont('Arial', '', 8);
            $fpdf->MultiCell(195, 5, utf8_decode("SON: " . $strValorLetras), 0, 'L');
            $fpdf->Rect($posx, $posy , 195, $fpdf->getY() - $posy+2);
            $fpdf->ln(2);
            $fpdf->SetFont('Arial', '', 7);
            $fpdf->MultiCell(195, 5, utf8_decode($ofe_web), 0, 'C');

            $posy = 204;
            $fpdf->setXY($posx, $posy);
            $fpdf->SetFillColor(150, 150, 150);
            $fpdf->SetTextColor(250, 250, 250);
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->MultiCell(195, 5, utf8_decode("Documento Referencia"), 1, 'L', true);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->setX($posx);
            $fpdf->MultiCell(195, 5, utf8_decode("FACTURA: ". $datosComprobante['factura_ref']), 'RL', 'L');
            $fpdf->setX($posx);
            $fpdf->MultiCell(195, 5, utf8_decode("Fecha Factura: ". $datosComprobante['fecha_emision']), 'RL', 'L');
            $fpdf->setX($posx);
            $fpdf->MultiCell(195, 5, utf8_decode("CUFE: ". $datosComprobante['cufe_ref']), 'RLB', 'L');
        }

        return ['error' => false, 'pdf' => $fpdf->Output('S')];
    }
}
