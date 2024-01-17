<?php
/**
 * User: Juan Jose Trujillo Ch.
 * Date: 20/11/19
 * Time: 11:30 AM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_paisagro\rg830119738;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;
use App\Http\Modulos\Parametros\ConceptosCorreccion\ParametrosConceptoCorreccion;
use App\Http\Modulos\Documentos\EtlCabeceraDocumentosDaop\EtlCabeceraDocumentoDaop;

class Rg830119738_1 extends RgBase
{

    public function getPdf() {

        //Extrayendo información de cabecera de la factura
        extract($this->getDatos());

        //PDF
        $fpdf = $this->pdfManager();
        $fpdf->AcceptPageBreak();
        $fpdf->SetFont('Arial','',8);
        $fpdf->AliasNbPages();
        $fpdf->SetMargins(0,0,0);
        $fpdf->SetAutoPageBreak(true,10);

        $fpdf->setImageHeader($this->getFullImage('logo'.$ofe_identificacion.'.png'));
        $datosComprobante['logonc'] = $this->getFullImage('logo_paisagro.png');
        $datosComprobante['no_valido'] = $this->getFullImage("no_valido.png");
        $datosComprobante['marca_agua'] = $this->getFullImage("marca_agua.png");

        $datosComprobante['impuesto_iva'] = "";
        if(isset($ofe_representacion_grafica->impuesto_iva) && $ofe_representacion_grafica->impuesto_iva != ""){
            $datosComprobante['impuesto_iva'] = $ofe_representacion_grafica->impuesto_iva;
        }

        $datosComprobante['actividad_economica'] = "";
        if(isset($ofe_representacion_grafica->actividad_economica) && $ofe_representacion_grafica->actividad_economica != ""){
            $datosComprobante['actividad_economica'] = $ofe_representacion_grafica->actividad_economica;
        }

        if ($cdo_tipo == "FC") {

            $datosComprobante['resolucion'] = "";
            if(isset($ofe_representacion_grafica->resolucion) && $ofe_representacion_grafica->resolucion != ""){
                $date1  = strtotime($ofe_resolucion_fecha);
                $date2  = strtotime($ofe_resolucion_fecha_hasta);
                $diff   = $date2 - $date1;
                $meses  = (string) round($diff / (60 * 60 * 24 * 30.5));

                $arrConv = array("{res}", "{res_fecha_desde}", "{res_prefijo}", "{res_desde}", "{res_prefijo}", "{res_hasta}", "{meses}");
                $arrRes  = array($ofe_resolucion, date("Y-m-d",strtotime($ofe_resolucion_fecha)), $ofe_resolucion_prefijo, $ofe_resolucion_desde, $ofe_resolucion_prefijo, $ofe_resolucion_hasta, $meses); 

                $datosComprobante['resolucion'] = str_replace($arrConv, $arrRes, $ofe_representacion_grafica->resolucion);
            }

            $datosComprobante['nit'] = "";
            if(isset($ofe_representacion_grafica->nit) && $ofe_representacion_grafica->nit != ""){
                $datosComprobante['nit'] = $ofe_representacion_grafica->nit;
            }

            $datosComprobante['cuatro_x_mil'] = array();
            if(isset($ofe_representacion_grafica->cuatro_x_mil) && $ofe_representacion_grafica->cuatro_x_mil != ""){
                $datosComprobante['cuatro_x_mil'] = explode('~', $ofe_representacion_grafica->cuatro_x_mil);
            }    

            $datosComprobante['texto_cabecera_1'] = "";
            if(isset($ofe_representacion_grafica->texto_cabecera_1) && $ofe_representacion_grafica->texto_cabecera_1 != ""){
                $datosComprobante['texto_cabecera_1'] = $ofe_representacion_grafica->texto_cabecera_1;
            }

            $datosComprobante['direccion'] = "";
            if(isset($ofe_representacion_grafica->direccion) && $ofe_representacion_grafica->direccion != ""){
                $datosComprobante['direccion'] = $ofe_representacion_grafica->direccion;
            }

            $datosComprobante['pbx'] = "";
            if(isset($ofe_representacion_grafica->pbx) && $ofe_representacion_grafica->pbx != ""){
                $datosComprobante['pbx'] = $ofe_representacion_grafica->pbx;
            }

            $datosComprobante['direccion_2'] = "";
            if(isset($ofe_representacion_grafica->direccion_2) && $ofe_representacion_grafica->direccion_2 != ""){
                $datosComprobante['direccion_2'] = $ofe_representacion_grafica->direccion_2;
            }

            $datosComprobante['pais'] = "";
            if(isset($ofe_representacion_grafica->pais) && $ofe_representacion_grafica->pais != ""){
                $datosComprobante['pais'] = $ofe_representacion_grafica->pais;
            }

            $datosComprobante['sitio_url'] = "";
            if(isset($ofe_representacion_grafica->sitio_url) && $ofe_representacion_grafica->sitio_url != ""){
                $datosComprobante['sitio_url'] = $ofe_representacion_grafica->sitio_url;
            }

            $datosComprobante['email'] = "";
            if(isset($ofe_representacion_grafica->email) && $ofe_representacion_grafica->email != ""){
                $datosComprobante['email'] = $ofe_representacion_grafica->email;
            }

            $datosComprobante['vendedor'] = "";
            if(isset($cdo_informacion_adicional->vendedor) && $cdo_informacion_adicional->vendedor != ""){
                $datosComprobante['vendedor'] = $cdo_informacion_adicional->vendedor;
            }

            $datosComprobante['plazo_pago'] = "";
            if(isset($cdo_informacion_adicional->plazo_pago) && $cdo_informacion_adicional->plazo_pago != ""){
                $datosComprobante['plazo_pago'] = $cdo_informacion_adicional->plazo_pago;
            }

            $datosComprobante['pie_pagina'] = "";
            if(isset($ofe_representacion_grafica->pie_pagina) && $ofe_representacion_grafica->pie_pagina != ""){
                $datosComprobante['pie_pagina'] = $ofe_representacion_grafica->pie_pagina;
            }
          
            //Encabezado
            $datosComprobante['cdo_tipo']            = $cdo_tipo; 
            $datosComprobante['ofe_dv']              = $ofe_dv; 
            $datosComprobante['ofe_nit']             = $ofe_nit;
            $datosComprobante['ofe_identificacion']  = $ofe_identificacion;
            $datosComprobante['numero_documento']    = $numero_documento;
            $datosComprobante['ofe_dir']             = $ofe_dir;
            $datosComprobante['ofe_tel']             = $ofe_tel;
            $datosComprobante['ofe_mun']             = $ofe_mun;
            $datosComprobante['ofe_web']             = $ofe_web;
            $datosComprobante['ofe_pais']            = $ofe_pais;
            $datosComprobante['ofe_correo']          = $ofe_correo;
            //Adquirente
            $datosComprobante['adquirente']          = $adquirente;
            $adqNit                                  = explode('-', $adq_nit);
            $datosComprobante['adq_nit']             = $adqNit[0];
            $datosComprobante['adq_nit_consecutivo'] = $adqNit[1];
            $datosComprobante['adq_dir']             = $adq_dir;
            $datosComprobante['adq_mun']             = $adq_mun;
            $datosComprobante['adq_tel']             = $adq_tel; 

            $datosComprobante['fecha_hora_documento'] = fechaCastellano($fecha_hora_documento);
            $datosComprobante['fecha_vencimiento']    = fechaCastellano($fecha_vencimiento);
            $datosComprobante['hora_documento']       = $cdo_hora;

            $datosComprobante['diferencia']           = date_diff(date_create($fecha_hora_documento),date_create($fecha_vencimiento),true);
            $datosComprobante['diferencia']           = $datosComprobante['diferencia']->d;
            $datosComprobante['diferencia']          .= $datosComprobante['diferencia'] == 1 ? " día" : " días";

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
            
            $datosComprobante['qr']              = "";
            $datosComprobante['signaturevalue']  = "";
            $datosComprobante['cufe']            = "";

            if($signaturevalue != '' && $qr !=''){
                $datosComprobante['qr']              = $qr;
                $datosComprobante['signaturevalue']  = $signaturevalue;
                $datosComprobante['cufe']            = $cufe;
            }

            $datosComprobante['razon_social_pt'] = $razon_social_pt;
            $datosComprobante['nit_pt']          = $nit_pt;

            $datosComprobante['nombre_software'] = "";
            if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
                $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
            }

            //Observacines
            try {
                $datosComprobante['observacion_decode'] = (array) json_decode($observacion);
            } catch (\Throwable $th) {
                $datosComprobante['observacion_decode'] = [];
            }

            $datosComprobante['base_1'] = "";
            if(isset($ofe_representacion_grafica->base_1) && $ofe_representacion_grafica->base_1 != ""){
                $datosComprobante['base_1'] = $ofe_representacion_grafica->base_1;
            }

            $datosComprobante['base_2'] = "";
            if(isset($ofe_representacion_grafica->base_2) && $ofe_representacion_grafica->base_2 != ""){
                $datosComprobante['base_2'] = $ofe_representacion_grafica->base_2;
            }

            $datosComprobante['nota_final_1'] = "";
            if(isset($ofe_representacion_grafica->nota_final_1) && $ofe_representacion_grafica->nota_final_1 != ""){
                $datosComprobante['nota_final_1'] = $ofe_representacion_grafica->nota_final_1;
            }

            $datosComprobante['nota_final_2'] = "";
            if(isset($ofe_representacion_grafica->nota_final_2) && $ofe_representacion_grafica->nota_final_2 != ""){
                $datosComprobante['nota_final_2'] = $ofe_representacion_grafica->nota_final_2;
            }

            $datosComprobante['nota_final_3'] = "";
            if(isset($ofe_representacion_grafica->nota_final_3) && $ofe_representacion_grafica->nota_final_3 != ""){
                $datosComprobante['nota_final_3'] = $ofe_representacion_grafica->nota_final_3;
            }

            $datosComprobante['consignar'] = array();
            if(isset($ofe_representacion_grafica->consignar) && $ofe_representacion_grafica->consignar != ""){
                $datosComprobante['consignar'] = explode('~',$ofe_representacion_grafica->consignar);
            }

            $datosComprobante['validacion_dian'] = "";
            if (isset($cdo_fecha_validacion_dian) && $cdo_fecha_validacion_dian != "") {
                $fecha_dian = explode(" ", $cdo_fecha_validacion_dian);
                $datosComprobante['validacion_dian'] = $fecha_dian[0] ." / ".$fecha_dian[1];
            }
            
            $datosComprobante['bandera_fondo'] = "SI";
            $fpdf->datosComprobante = $datosComprobante;

            $fpdf->AddPage('P','Letter');
            $posx = $fpdf->posx - 2;
            $posy = $fpdf->nPosYFin;
            $posfin = 177;

            // $items = array_merge($items,$items,$items);
            // $items = array_merge($items,$items,$items);
            // $items = array_merge($items,$items,$items);

            //Items
            $contItem = 0;

            //Propiedades de la tabla
            if(isset($items) && count($items) > 0){
                $fpdf->SetWidths(array(10, 21, 46, 11, 10, 9, 10, 25, 12, 24, 22));
                $fpdf->SetAligns(array("C", "L", "L", "C", "L", "C", "C", "C", "C", "R", "R"));
                $fpdf->SetLineHeight(4);
                $fpdf->setXY($posx,$posy);

                foreach ($items as $item) {
                    $contItem++;

                    if($fpdf->getY() > $posfin){
                        $fpdf->AddPage('P','Letter');
                        $posx = $fpdf->posx - 2;
                        $posy = $fpdf->nPosYFin;
                        $fpdf->setXY($posx,$posy);
                    }

                    $_ddo_informacion_adicional = json_decode($item['ddo_informacion_adicional']);

                    $strLote = "";
                    if (isset($_ddo_informacion_adicional->lote) && $_ddo_informacion_adicional->lote != "") {
                        $strLote = $_ddo_informacion_adicional->lote;
                    }

                    $intPrecioPublico = 0;
                    if (isset($_ddo_informacion_adicional->precio_publico) && is_numeric($_ddo_informacion_adicional->precio_publico)) { 
                        $intPrecioPublico = $_ddo_informacion_adicional->precio_publico;
                    }

                    $dtoIt = (isset($item['get_cargo_descuentos_items_documentos_daop'][0]['cdd_porcentaje'])) ? $item['get_cargo_descuentos_items_documentos_daop'][0]['cdd_porcentaje'] : 0;

                    //Porcentaje Iva Item 
                    $impuestoIva = $this->getIvaItem($item);
                    $porcentajeIva = $impuestoIva ? number_format($impuestoIva['iid_porcentaje'], 0, ',', '.') . '%' : '';

                    $fpdf->setX($posx);
                    $fpdf->SetFont('Arial','',7);
                    $fpdf->Row(array(
                                    number_format($contItem),
                                    utf8_decode($item['ddo_codigo']),
                                    utf8_decode($item['ddo_descripcion_uno']),
                                    utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                                    utf8_decode($strLote),
                                    $porcentajeIva,
                                    number_format($item['ddo_cantidad'], 0,'.','.'), 
                                    number_format($intPrecioPublico, 0,',','.'),
                                    utf8_decode($dtoIt),
                                    number_format($item['ddo_valor_unitario'], 0,',','.'),
                                    number_format($item['ddo_total'], 0,',','.')
                                ));
                }
            }
            
            $fpdf->SetFont('Arial', '', 7);
            $fpdf->setXY($posx + 1, $fpdf->getY() + 1);
            $fpdf->Cell(8,5,$contItem,'T',0,'C');

            $fpdf->SetFont('Arial','',8);

            ## Calculo los Totales y Retenciones ##
            $nBaseIva5FCCOP = 0;
            $nBaseIva19FCCOP = 0;
            $nValorIva5FCCOP = 0;
            $nValorIva19FCCOP = 0;

            foreach ($impuestos_items as $impuesto) {
                if ($impuesto['iid_porcentaje'] == 5.00) {
                    $nBaseIva5FCCOP += $impuesto['iid_base'];
                    $nValorIva5FCCOP += $impuesto['iid_valor'];
                } elseif ($impuesto['iid_porcentaje'] == 19.00) {
                    $nBaseIva19FCCOP += $impuesto['iid_base'];
                    $nValorIva19FCCOP += $impuesto['iid_valor'];
                }
            }

            $nBaseIva5FCCOP = ($nBaseIva5FCCOP > 0) ? number_format($nBaseIva5FCCOP,0,'.',',') : "";
            $nBaseIva19FCCOP = ($nBaseIva19FCCOP > 0) ? number_format($nBaseIva19FCCOP,0,'.',',') : "";

            $nValorIva5FCCOP = ($nValorIva5FCCOP > 0) ? number_format($nValorIva5FCCOP,0,'.',',') : "";
            $nValorIva19FCCOP = ($nValorIva19FCCOP > 0) ? number_format($nValorIva19FCCOP,0,'.',',') : "";

            $datosComprobante['intSubtotal']      = $this->parserNumberController($subtotal);
            $datosComprobante['nValorIva5FCCOP']  = $nValorIva5FCCOP;
            $datosComprobante['nValorIva19FCCOP'] = $nValorIva19FCCOP;

            $datosComprobante['intValorReteiva']    = 0;
            $datosComprobante['intValorReteica']    = 0;
            $datosComprobante['intValorRetefuente'] = 0;

            $data = $this->getCargoDescuentosRetencionesTipo($cdo_id, self::MODO_CONSULTA_CABECERA, self::MODO_PORCENTAJE_DETALLAR);

            foreach($data as $retencion => $grupo){
                foreach ($grupo as $porcentaje => $valores){
                    
                    if($retencion == 'RETEIVA'){
                        $datosComprobante['intValorReteiva'] = $valores['valor'];
                    }

                    if($retencion == 'RETEICA'){
                        $datosComprobante['intValorReteica'] = $valores['valor'];
                    }

                    if($retencion == 'RETEFUENTE'){
                        $datosComprobante['intValorRetefuente'] = $valores['valor'];
                    }
                }
            }

            $intSubtotal = $this->parserNumberController($subtotal);
            $intIva      = $this->parserNumberController($iva);
            
            $intTotalPagar = ($intSubtotal + $intIva - $datosComprobante['intValorReteiva'] - $datosComprobante['intValorReteica'] - $datosComprobante['intValorRetefuente']);

            $datosComprobante['intTotalPagar'] = $intTotalPagar;

            /*** Seteo Bandera para no pintar los totales. ***/
            $datosComprobante['bandera_fondo'] = "NO";
            $fpdf->datosComprobante = $datosComprobante;
            
            $posy = $fpdf->posy+186;
            $posx = 7;
    
            # Impresion valor base 5%  ~ base 10% 
            $fpdf->setXY($posx+50,$posy);
            $fpdf->SetFont('Arial','',7);
            $fpdf->MultiCell(30,4,$nBaseIva5FCCOP,0,'L');
            $fpdf->setXY($posx+95,$posy);
            $fpdf->SetFont('Arial','',7);
            $fpdf->MultiCell(30,4,$nBaseIva19FCCOP,0,'L');
          
        }else if ($cdo_tipo == "NC" || $cdo_tipo == "ND") {

            $datosComprobante['razon_social'] = "";
            if(isset($ofe_representacion_grafica->razon_social) && $ofe_representacion_grafica->razon_social != ""){
                $datosComprobante['razon_social'] = $ofe_representacion_grafica->razon_social;
            }

            $datosComprobante['vendedor'] = "";
            if(isset($cdo_informacion_adicional->vendedor) && $cdo_informacion_adicional->vendedor != ""){
                $datosComprobante['vendedor'] = $cdo_informacion_adicional->vendedor;
            }

            $datosComprobante['codigo_concepto'] = "";
            if(isset($cdo_informacion_adicional->codigo_concepto) && $cdo_informacion_adicional->codigo_concepto != ""){
                $datosComprobante['codigo_concepto'] = $cdo_informacion_adicional->codigo_concepto;
            }

            $datosComprobante['concepto_nota'] = "";
            if(isset($cdo_informacion_adicional->concepto_nota) && $cdo_informacion_adicional->concepto_nota != ""){
                $datosComprobante['concepto_nota'] = $cdo_informacion_adicional->concepto_nota;
            }

            list($factura_ref, $fecha_ref, $cufe_ref) = $this->getDocumentoReferencia($cdo_documento_referencia);
            $datosComprobante['consecutivo_ref'] = $factura_ref;
            $datosComprobante['fecha_emision']   = $fecha_ref;
            $datosComprobante['cufe_ref']        = $cufe_ref;

            $intSubtotal = $this->parserNumberController($subtotal);
            $intIva      = $this->parserNumberController($iva);

            $datosComprobante['cdo_conceptos_correccion'] = $cdo_conceptos_correccion; 
      
            $datosComprobante['cdo_tipo']           = $cdo_tipo;
            $datosComprobante['ofe_identificacion'] = $ofe_identificacion;
            $datosComprobante['ofe_dv']             = $ofe_dv;
            $datosComprobante['ofe_dir']            = $ofe_dir;
            $datosComprobante['ofe_tel']            = $ofe_tel;
            $datosComprobante['ofe_mun']            = $ofe_mun;
            $datosComprobante['ofe_web']            = $ofe_web;

            $datosComprobante['adquirente']           = $adquirente;
            $datosComprobante['adq_dir']              = $adq_dir;
            $datosComprobante['adq_nit']              = $adq_nit;
            $datosComprobante['numero_documento']     = $numero_documento;
            $datosComprobante['fecha_hora_documento'] = fechaCastellano($fecha_hora_documento, $bAbbr = TRUE);
    
            $datosComprobante['qr'] = "";
            $datosComprobante['signaturevalue'] = "";
            if($signaturevalue != '' && $qr !=''){
                $datosComprobante['qr']              = $qr;
                $datosComprobante['signaturevalue']  = $signaturevalue;
                $datosComprobante['cufe']            = $cufe;
            }

            $datosComprobante['razon_social_pt'] = $razon_social_pt;
            $datosComprobante['nit_pt']          = $nit_pt;

            $datosComprobante['nombre_software'] = "";
            if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
                $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
            }

            $datosComprobante['bandera_fondo'] = "SI";
            $fpdf->datosComprobante = $datosComprobante;

            $fpdf->AddPage('P','Letter');
            $posx = 10;
            $posy = $fpdf->nPosYFin;
            $posfin = 198;
        
            // $items = array_merge($items,$items,$items,$items);
            // $items = array_merge($items,$items,$items,$items);

            // Items
            $contItem = 0;

            if(isset($items) && count($items) > 0){
                $fpdf->SetWidths(array(10,10,20,66,20,10,25,25));
                $fpdf->SetAligns(array("C","C","C","L","L","C","C"));
                $fpdf->SetLineHeight(4);
                $fpdf->setXY($posx,$posy+8);

                foreach ($items as $item) {
                    $contItem++;

                    if($fpdf->getY() > $posfin){
                        $fpdf->AddPage('P','Letter');
                        $posy = $fpdf->nPosYFin;
                        $fpdf->setXY($posx,$posy+8);
                    }

                    // $_ddo_informacion_adicional = json_decode($item['ddo_informacion_adicional']);

                    // $strDcto = "";
                    // if (isset($_ddo_informacion_adicional->dcto) && $_ddo_informacion_adicional->dcto != "") { 
                    //     $strDcto = $_ddo_informacion_adicional->dcto;
                    // }

                    $dtoIt = (isset($item['get_cargo_descuentos_items_documentos_daop'][0]['cdd_porcentaje'])) ? $item['get_cargo_descuentos_items_documentos_daop'][0]['cdd_porcentaje'] : 0;
               
                    $fpdf->setX($posx+1);
                    $fpdf->SetFont('Arial','',7);
                    $fpdf->Row(array(
                                    number_format($contItem),
                                    number_format($item['ddo_cantidad'],0,'.','.'),
                                    utf8_decode($item['ddo_codigo']),
                                    utf8_decode($item['ddo_descripcion_uno']),
                                    utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                                    $dtoIt,
                                    number_format($item['ddo_valor_unitario'],2,',','.'),
                                    number_format($item['ddo_total'],2,',','.')
                                ));
                }
            }

            $fpdf->SetFont('Arial', '', 7);
            $fpdf->setXY($posx + 2, $fpdf->getY() + 1);
            $fpdf->Cell(8,5,$contItem,'T',0,'C');

        }

        $datosComprobante['intValorReteiva']    = 0;
        $datosComprobante['intValorReteica']    = 0;
        $datosComprobante['intValorRetefuente'] = 0;

        $data = $this->getCargoDescuentosRetencionesTipo($cdo_id, self::MODO_CONSULTA_CABECERA, self::MODO_PORCENTAJE_DETALLAR);

        foreach($data as $retencion => $grupo){
            foreach ($grupo as $porcentaje => $valores){
                
                if($retencion == 'RETEIVA'){
                    $datosComprobante['intValorReteiva'] = $valores['valor'];
                }

                if($retencion == 'RETEICA'){
                    $datosComprobante['intValorReteica'] = $valores['valor'];
                }

                if($retencion == 'RETEFUENTE'){
                    $datosComprobante['intValorRetefuente'] = $valores['valor'];
                }
            }
        }

        $datosComprobante['intSubtotal'] = $this->parserNumberController($subtotal);
        $datosComprobante['intIva']      = $this->parserNumberController($iva);
        
        $datosComprobante['intTotalPagar'] = ($datosComprobante['intSubtotal']  + $datosComprobante['intIva']) - ($datosComprobante['intValorReteiva'] + $datosComprobante['intValorReteica'] + $datosComprobante['intValorRetefuente']);

        /*** Seteo Bandera para no pintar los totales. ***/
        $datosComprobante['bandera_fondo'] = "NO";
        $fpdf->datosComprobante = $datosComprobante;

        return ['error' => false, 'pdf' => $fpdf->Output('S')];
    }
}

function fechaCastellano($dFecha, $bAbbr = FALSE) {
    $nombreCompletoMeses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    $nombreAbreviadosMeses = [
        1 => 'Ene',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ago',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dic'
        ];

    $dia = date("d", strtotime($dFecha));
    $anio = date("Y", strtotime($dFecha));

    if(!$bAbbr){
        $mes = $nombreAbreviadosMeses[ date("n", strtotime($dFecha)) ];
        return $dia."      ".$mes."      ".$anio;
    } else {
        $mes = $nombreAbreviadosMeses[ date("n", strtotime($dFecha)) ];
	    return $dia." ".$mes."/".$anio;
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