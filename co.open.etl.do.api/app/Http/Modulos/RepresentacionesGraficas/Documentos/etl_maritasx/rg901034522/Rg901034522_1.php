<?php
/**
 * User: Jhon Escobar
 * Date: 23/09/20
 * Time: 12:04 PM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_maritasx\rg901034522;

use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;
use App\Http\Modulos\Documentos\EtlCargosDescuentosDocumentosDaop\EtlCargosDescuentosDocumentoDaop;
use App\Http\Modulos\Documentos\EtlImpuestosItemsDocumentosDaop\EtlImpuestosItemsDocumentoDaop;
use App\Http\Modulos\Parametros\Tributos\ParametrosTributo;
use App\Http\Traits\NumToLetrasEngine;

/**
 * Controlador para la generación de representaciones gráficas de indice 1
 *
 * @package App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_maritasx\rg901034522
 */
class Rg901034522_1 extends RgBase
{
    /**
     * RgGENERICA_1 constructor.
     */
    public function initEngine(){
        //PDF
        $fpdf = $this->pdfManager();
        $fpdf->AcceptPageBreak();
        $fpdf->SetFont('Arial','',8);
        $fpdf->AliasNbPages();
        $fpdf->SetMargins(0,0,0);
        $fpdf->SetAutoPageBreak(true,10);
        $this->fpdf = $fpdf;
    }

    /**
     * Proceso primario para la generación del PDF.
     *
     * @return array|mixed
     */
    public function getPdf() {
        extract($this->getDatos());
        $this->initEngine();
        $datosComprobante = [];
        $datosComprobante['no_valido'] = $this->getFullImage("no_valido.png");

        //Extrayendo información de cabecera de la factura
        $strOfeId = number_format($ofe_identificacion, 0, ",", ".");
        $ofe_nit = implode('-', array($strOfeId, $ofe_dv));
        $datosComprobante['qr']              = "";
        $datosComprobante['signaturevalue']  = "";
        $datosComprobante['cufe']            = "";

        if(!empty($signaturevalue) && !empty($qr)){
            $datosComprobante['qr']              = $qr;
            $datosComprobante['cufe']            = $cufe;
            $datosComprobante['signaturevalue']  = $signaturevalue;
        }

        $datosComprobante['cdo_consecutivo']      = $rfa_prefijo ." ". $cdo_consecutivo;
        $datosComprobante['rfa_prefijo']          = $rfa_prefijo;
        $datosComprobante['fecha_hora_documento'] = $fecha_hora_documento;
        $datosComprobante['fecha_vencimiento']    = $fecha_vencimiento;
        $datosComprobante['oferente']             = $oferente;
        $datosComprobante['ofe_nit']              = $ofe_nit;
        $datosComprobante['ofe_dir']              = $ofe_dir;
        $datosComprobante['ofe_tel']              = $ofe_tel;
        $datosComprobante['ofe_resolucion']       = $ofe_resolucion;
        $datosComprobante['ofe_resolucion_fecha'] = $ofe_resolucion_fecha;
        $datosComprobante['ofe_resolucion_desde'] = $ofe_resolucion_desde;
        $datosComprobante['ofe_resolucion_hasta'] = $ofe_resolucion_hasta;
        $datosComprobante['ofe_resolucion_prefijo'] = $ofe_resolucion_prefijo;
        $datosComprobante['ofe_resolucion_vigencia'] = $ofe_resolucion_vigencia;
        $datosComprobante['ofe_mun']              = $ofe_mun;
        $datosComprobante['cdo_tipo']             = $cdo_tipo;
        $datosComprobante['ofe_correo']           = $ofe_correo;
        $datosComprobante['adquirente']           = $adquirente;
        $datosComprobante['adq_nit']              = $adq_nit;
        $datosComprobante['adq_dir']              = $adq_dir;
        $datosComprobante['adq_pai']              = $adq_pais;
        $datosComprobante['adq_mun']              = $adq_mun;
        $datosComprobante['adq_tel']              = $adq_tel;
        $datosComprobante['dias_pago']            = $dias_pago;
        $datosComprobante['ofe_regimen']          = $ofe_regimen;
        $datosComprobante['numero_documento']     = $numero_documento;
        $datosComprobante['cufe']                 = $cufe;
        $datosComprobante['ofe_pais']             = $ofe_pais;
        
        $datosComprobante['observacion']          = is_array($observacion) ? implode('\n', json_decode($observacion)) : '';
        $datosComprobante['adq_correo']           = $adq_correo;
        $datosComprobante['cdo_fecha_validacion_dian'] = $cdo_fecha_validacion_dian;

        $datosComprobante['razon_social_pt']      = $razon_social_pt;
        $datosComprobante['nit_pt']               = $nit_pt;

        $datosComprobante['nombre_software'] = "";
        if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
            $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
        }

        try {
            $observacion_decode = (array) json_decode($observacion);
        } catch (\Throwable $th) {
            $observacion_decode = [];
        }

        // Se inicializa el medio y la forma de pago
        $datosComprobante['forma_de_pago'] = "";
        $datosComprobante['medio_de_pago'] = "";
        foreach ($medios_pagos_documento as $key => $medios_pagos){
            //Forma
            $forma = $medios_pagos['forma'];
            $datosComprobante['forma_de_pago'] = (isset($forma['fpa_descripcion']) && $forma['fpa_descripcion'] != '') ? $forma['fpa_descripcion'] : '';
            //Medio
            $medio = $medios_pagos['medio'];
            $datosComprobante['medio_de_pago'] = (isset($medio['mpa_descripcion']) && $medio['mpa_descripcion'] != '') ? $medio['mpa_descripcion'] : '';
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
                "{res_fecha_hasta}",
                "{meses}", 
                "{res_prefijo}", 
                "{res_desde}", 
                "{res_prefijo}", 
                "{res_hasta}"
            );

            $datosComprobante['resolucion']  = array(
                $ofe_resolucion, 
                date("Y/m/d",strtotime($ofe_resolucion_fecha)), 
                date("Y/m/d", strtotime($ofe_resolucion_fecha_hasta)),
                $meses, 
                $ofe_resolucion_prefijo, 
                $ofe_resolucion_desde, 
                $ofe_resolucion_prefijo, 
                $ofe_resolucion_hasta
            ); 

            $datosComprobante['resolucion'] = str_replace($arrConv, $datosComprobante['resolucion'], $ofe_representacion_grafica->resolucion);
        }

        $datosComprobante['actividad_economica'] = "";
        if(isset($ofe_representacion_grafica->actividad_economica) && !empty($ofe_representacion_grafica->actividad_economica))
            $datosComprobante['actividad_economica'] = $ofe_representacion_grafica->actividad_economica;

        $datosComprobante['regimen_comun'] = "";
        if(isset($ofe_representacion_grafica->regimen_comun) && !empty($ofe_representacion_grafica->regimen_comun))
            $datosComprobante['regimen_comun'] = $ofe_representacion_grafica->regimen_comun;

        $datosComprobante['somos'] = "";
        if(isset($ofe_representacion_grafica->somos) && !empty($ofe_representacion_grafica->somos))
            $datosComprobante['somos'] = $ofe_representacion_grafica->somos;

        $datosComprobante['notas_finales_1'] = "";
        if(isset($ofe_representacion_grafica->notas_finales_1) && !empty($ofe_representacion_grafica->notas_finales_1))
            $datosComprobante['notas_finales_1'] = $ofe_representacion_grafica->notas_finales_1;

        $datosComprobante['termino_negocio'] = "";
        if(isset($cdo_informacion_adicional) && !empty($cdo_informacion_adicional->termino_negocio)){
            $datosComprobante['termino_negocio'] = $cdo_informacion_adicional->termino_negocio;
        }

        $datosComprobante['cen_descripcion'] = $this->getCondicionesEntrega($datosComprobante['termino_negocio'],'cen_descripcion');

        $datosComprobante['vendedor'] = "";
        if(isset($cdo_informacion_adicional) && !empty($cdo_informacion_adicional->vendedor)){
            $datosComprobante['vendedor'] = $cdo_informacion_adicional->vendedor;
        }

        $datosComprobante['punto_venta'] = "";
        if(isset($cdo_informacion_adicional) && !empty($cdo_informacion_adicional->punto_venta)){
            $datosComprobante['punto_venta'] = $cdo_informacion_adicional->punto_venta;
        }

        $datosComprobante['total_unidades'] = 0;
        if(isset($cdo_informacion_adicional->total_unidades) && is_numeric($cdo_informacion_adicional->total_unidades)){
            $datosComprobante['total_unidades'] = $cdo_informacion_adicional->total_unidades;
        }

        $datosComprobante['moneda_de_negociacion'] = "Peso Colombiano";
        //Valor Moneda Extranjera
        if(isset($cdo_moneda_extranjera) && trim($cdo_moneda_extranjera) != ''){
            $datosComprobante['moneda_de_negociacion'] = "Dolar Americano";
        }

        $datosComprobante['numero_pedido'] = "";
        if(isset($cdo_informacion_adicional) && !empty($cdo_informacion_adicional->numero_pedido)){
            $datosComprobante['numero_pedido'] = $cdo_informacion_adicional->numero_pedido;
        }

        $datosComprobante['centro'] = "";
        if(isset($cdo_informacion_adicional) && !empty($cdo_informacion_adicional->centro)){
            $datosComprobante['centro'] = $cdo_informacion_adicional->centro;
        }

        $datosComprobante['fecha_pedido'] = "";
        if(isset($cdo_informacion_adicional) && !empty($cdo_informacion_adicional->fecha_pedido)){
            $datosComprobante['fecha_pedido'] = $cdo_informacion_adicional->fecha_pedido;
        }

        $datosComprobante['numero_remision'] = "";
        if(isset($cdo_informacion_adicional) && !empty($cdo_informacion_adicional->numero_remision)){
            $datosComprobante['numero_remision'] = $cdo_informacion_adicional->numero_remision;
        }

        $datosComprobante['fecha_entrada'] = "";
        if(isset($cdo_informacion_adicional) && !empty($cdo_informacion_adicional->fecha_entrada)){
            $datosComprobante['fecha_entrada'] = $cdo_informacion_adicional->fecha_entrada;
        }

        $intTotalDescuentos = $descuentos;
        if(isset($cdo_informacion_adicional->descuento) && is_numeric($cdo_informacion_adicional->descuento))
            $intTotalDescuentos = $cdo_informacion_adicional->descuento;

        $intTotalAnticipos = $anticipo;
        if(isset($cdo_informacion_adicional->anticipo) && is_numeric($cdo_informacion_adicional->anticipo))
            $intTotalAnticipos = $cdo_informacion_adicional->anticipo;

        $intTotalCargos = $cargos;
        if(isset($cdo_informacion_adicional->cargo) && is_numeric($cdo_informacion_adicional->cargo))
            $intTotalCargos = $cdo_informacion_adicional->cargo;


        // $items = array_merge($items,$items,$items);
        // $items = array_merge($items,$items,$items);

        $contItem = 0;

        if ($datosComprobante['cdo_tipo'] === "FC"){
            //Propiedades de la tabla
            $this->nPosYDet = 84;
            $posx = $this->fpdf->GetX();
            $posy = $this->fpdf->GetY();
            $this->fpdf->SetWidths([10, 15, 18, 28, 20, 13, 16, 14, 12, 11, 18, 20]);
            $this->fpdf->SetAligns([ 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'R', 'R', 'R']);
            $this->fpdf->SetLineHeight(4);
            $this->fpdf->SetFont('Helvetica','',7);
            $this->fpdf->setXY($posx + 10, $posy);
            //$nPosYFin = 167;
            $nPosYFin = 210;
            /*** Impresion Comprobante. ***/
            $this->fpdf->datosComprobante = $datosComprobante;
            $this->fpdf->AddPage('P','Letter');
            $posx = 10;
            foreach ($items as $item) {
                $contItem++;

                $ddo_informacion_adicional = json_decode($item['ddo_informacion_adicional']);

                $lnDescripcion = $this->fpdf->NbLines(28, $item['ddo_descripcion_uno']);
                $lnComposicion = $this->fpdf->NbLines(20, isset($ddo_informacion_adicional->composicion) ? $ddo_informacion_adicional->composicion : '');
                $lnTejedijo    = $this->fpdf->NbLines(13, isset($ddo_informacion_adicional->tejido) ? $ddo_informacion_adicional->tejido : '');

                if ($this->fpdf->getY() > $nPosYFin) {
                    $this->fpdf->AddPage('P','Letter');
                    $posx = 10;
                    $posy = $this->fpdf->nPosYDet;
                    $this->fpdf->setXY($posx, $posy);

                } elseif ($this->fpdf->getY() > 205 && ($lnDescripcion > 2 || $lnComposicion > 0 || $lnTejedijo > 2)) {
                    $this->fpdf->AddPage('P','Letter');
                    $posx = 10;
                    $posy = $this->fpdf->nPosYDet;
                    $this->fpdf->setXY($posx, $posy);
                }

                $intValorUnitario = $item['ddo_valor_unitario'];
                $intValorTotal    = $item['ddo_total'];
                //Valor Moneda Extranjera
                if(isset($cdo_moneda_extranjera) && trim($cdo_moneda_extranjera) != ''){
                    $intValorUnitario = $item['ddo_valor_unitario_moneda_extranjera'];
                    $intValorTotal    = $item['ddo_total_moneda_extranjera'];
                }

                // Cuadro de los items
                $this->fpdf->Rect($posx, $this->fpdf->posy+87, 195, 124);                
                $this->fpdf->setX($posx);
                $this->fpdf->Row(
                    [
                    number_format($contItem),
                    isset($ddo_informacion_adicional->linea) ? $ddo_informacion_adicional->linea : '',
                    $item['ddo_codigo'],
                    $item['ddo_descripcion_uno'],
                    isset($ddo_informacion_adicional->composicion) ? $ddo_informacion_adicional->composicion : '',
                    isset($ddo_informacion_adicional->tejido) ? $ddo_informacion_adicional->tejido : '',
                    isset($ddo_informacion_adicional->subpartida) ? $ddo_informacion_adicional->subpartida : '',
                    utf8_decode(number_format($item['ddo_cantidad'], 2,'.', ',')),
                    utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                    $porcentaje_iva ? $porcentaje_iva."%" : '',
                    number_format($intValorUnitario,2,'.',','),
                    number_format($intValorTotal,2,'.',',')
                    ]
                );                   
            }

            $this->fpdf->setXY($posx + 1, $this->fpdf->GetY());
            $this->fpdf->Cell(8,6,$contItem,'T',0,'C');
            $this->fpdf->Ln(4);

            // Si se llega al máximo, se agrega una página nueva
            if($this->fpdf->getY() > 164){
                $this->fpdf->AddPage('P','Letter');
                $posx = 10;
                $posy = $this->fpdf->nPosYDet;
                $this->fpdf->setXY($posx, $posy);
                // Cuadro de los items
                $this->fpdf->Rect($posx, $this->fpdf->posy+87, 195, 124);                 
            }            
        }elseif ($datosComprobante['cdo_tipo'] === "NC"){
            //Propiedades de la tabla
            $this->nPosYDet = 84;
            $posx = $this->fpdf->GetX();
            $posy = $this->fpdf->GetY();
            $this->fpdf->SetWidths([10,15,15,15,45,15,15,13,26,26]);
            $this->fpdf->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C']);
            $this->fpdf->SetLineHeight(4);
            $this->fpdf->SetFont('Helvetica','',7);
            $this->fpdf->setXY($posx + 10, $posy);
            $nPosYFin = 187;
            /*** Impresion Comprobante. ***/
            $this->fpdf->datosComprobante = $datosComprobante;
            $this->fpdf->AddPage('P','Letter');
            $posx = 10;
            foreach ($items as $item) {
                $contItem++;

                $ddo_informacion_adicional = json_decode($item['ddo_informacion_adicional']);

                if($this->fpdf->getY() > $nPosYFin){
                    $this->fpdf->AddPage('P','Letter');
                    $posx = 10;
                    $posy = $this->fpdf->nPosYDet;
                    $this->fpdf->setXY($posx, $posy);
                }

                $intValorUnitario = $item['ddo_valor_unitario'];
                $intValorTotal    = $item['ddo_total'];
                //Valor Moneda Extranjera
                if(isset($cdo_moneda_extranjera) && trim($cdo_moneda_extranjera) != ''){
                    $intValorUnitario = $item['ddo_valor_unitario_moneda_extranjera'];
                    $intValorTotal    = $item['ddo_total_moneda_extranjera'];
                }

                // Cuadro de los items
                $this->fpdf->Rect($posx, $this->fpdf->posy+87, 195, 120);                
                $this->fpdf->setX($posx);
                $this->fpdf->Row(
                    [   
                        number_format($contItem),
                        $item['ddo_codigo'],
                        utf8_decode(number_format($item['ddo_cantidad'], 2,'.', ',')),
                        utf8_decode($this->getUnidad($item['und_id'], 'und_descripcion')),
                        $item['ddo_descripcion_uno'],                            
                        isset($ddo_informacion_adicional->composicion) ? $ddo_informacion_adicional->composicion : '',
                        isset($ddo_informacion_adicional->subpartida) ? $ddo_informacion_adicional->subpartida : '',
                        isset($ddo_informacion_adicional->tejido) ? $ddo_informacion_adicional->tejido : '',
                        number_format($intValorUnitario,2,'.',','),
                        number_format($intValorTotal,2,'.',',')
                    ]
                );
            }

            $this->fpdf->setXY($posx + 1, $this->fpdf->GetY());
            $this->fpdf->Cell(8,6,$contItem,'T',0,'C');
            $this->fpdf->Ln(4);

            // Si se llega al máximo, se agrega una página nueva
            if($this->fpdf->getY() > 180){
                $this->fpdf->AddPage('P','Letter');
                $posx = 10;
                $posy = $this->fpdf->nPosYDet;
                $this->fpdf->setXY($posx, $posy);
                // Cuadro de los items
                $this->fpdf->Rect($posx, $this->fpdf->posy+87, 195, 120);                 
            }            
        }

        $posy = $this->fpdf->getY();
        $campos_personalizados = isset($ofe_campos_personalizados_factura['valores_resumen']) ? $ofe_campos_personalizados_factura['valores_resumen'] : [];
        $mostrarRetiva = !empty($campos_personalizados) ? in_array('reteiva', $campos_personalizados) : false;
        $mostrarReteica = !empty($campos_personalizados) ? in_array('reteica', $campos_personalizados) : false;
        $mostrarRetefuente = !empty($campos_personalizados) ? in_array('retefuente', $campos_personalizados) : false;

        $reteiva = 0;
        $reteica = 0;
        $retefuente = 0;

        $ica  = 0;
        $impuesto_consumo = 0;
        $iva = 0;

        foreach ($impuestos_documento as $impuesto) {
            if (array_key_exists($impuesto->tri_id, $impuestos_registrados)) {
                if ($impuestos_registrados[$impuesto->tri_id] === self::IMPUESTO_IVA)
                    $iva = $iva + $impuesto->iid_valor;
                elseif($impuestos_registrados[$impuesto->tri_id] === self::IMPUESTO_CONSUMO)
                    $impuesto_consumo = $impuesto_consumo + $impuesto->iid_valor;
                elseif ($impuestos_registrados[$impuesto->tri_id] === self::IMPUESTO_ICA)
                    $ica = $ica + $impuesto->iid_valor;
            }
        }

        $intTotalRetefuente = 0;
        $intTotalReteica = 0;
        $intTotalReteiva = 0;
        if ($mostrarRetefuente || $mostrarRetiva || $mostrarReteica) {
            foreach ($detalle_cargos_descuentos as $retencion) {
                switch ($retencion->cdd_tipo) {
                    case 'RETEICA':
                        $intTotalReteica = $intTotalReteica + $retencion->cdd_valor;
                        break;
                    case 'RETEIVA':
                        $intTotalReteiva = $intTotalReteiva + $retencion->cdd_valor;
                        break;
                    case 'RETEFUENTE':
                        $intTotalRetefuente = $intTotalRetefuente + $retencion->cdd_valor;
                        break;
                }
            }

            if(isset($cdo_informacion_adicional->total_retefuente) && is_numeric($cdo_informacion_adicional->total_retefuente))
                $intTotalRetefuente = $cdo_informacion_adicional->total_retefuente;
            if(isset($cdo_informacion_adicional->total_reteica) && is_numeric($cdo_informacion_adicional->total_reteica))
                $intTotalReteica = $cdo_informacion_adicional->total_reteica;
            if(isset($cdo_informacion_adicional->total_reteiva) && is_numeric($cdo_informacion_adicional->total_reteiva))
                $intTotalReteiva = $cdo_informacion_adicional->total_reteiva;
        }

        $intSubtotal = $this->formatWithDecimals($subtotal);
        $intIva = $this->formatWithDecimals($iva);
        $intTotalPagar = $this->formatWithDecimals($valor_a_pagar);
        $cdo_moneda = "COP";
        //Valor Moneda Extranjera
        if(isset($cdo_moneda_extranjera) && trim($cdo_moneda_extranjera) != ''){
            $intSubtotal = $this->formatWithDecimals($subtotal_moneda_extranjera);
            $intIva = $this->formatWithDecimals($iva_moneda_extranjera);
            $intTotalPagar = $this->formatWithDecimals($valor_a_pagar_moneda_extranjera);
            $cdo_moneda = "USD";
        }
       
        $cargos = $this->formatWithDecimals($cargos);
        $intTotalDescuentos = $this->formatWithDecimals($intTotalDescuentos);
        $intTotalRetefuente = $this->formatWithDecimals($intTotalRetefuente);
        $intTotalReteica = $this->formatWithDecimals($intTotalReteica);
        $intTotalReteiva = $this->formatWithDecimals($intTotalReteiva);
        $intTotalAnticipos = $this->formatWithDecimals($intTotalAnticipos);
        $intTotal = $this->formatWithDecimals($total);

        $ica = $this->formatWithDecimals($ica);
        $impuesto_consumo = $this->formatWithDecimals($impuesto_consumo);
        $iva = $this->formatWithDecimals($iva);

        //Valor en Letras
        $datosComprobante['valor_letras'] = NumToLetrasEngine::num3letras(number_format($this->parserNumberController($intTotalPagar), 2, '.', ''), false, true, $cdo_moneda);

        $registroTotales = [
            ['value' => 'subtotal', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
            ['value' => 'iva', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
        ];
        $registroTotalesLabels = [
            ['value' => 'SUBTOTAL', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]],
            ['value' => 'IVA', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
        ];

        $registroTotales[] = ['value' => 'iva', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]];

        if ($impuesto_consumo > 0.0) {
            $registroTotales[] = ['value' => 'impuesto_consumo', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]];
            $registroTotalesLabels[] = ['value' => 'IMPUESTO CONSUMO', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
        }

        if ($ica > 0.0) {
            $registroTotales[] = ['value' => 'ica', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]];
            $registroTotalesLabels[] = ['value' => 'ICA', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
        }

        $registroTotalesLabels[] = ['value' => 'TOTAL', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];

        if ($mostrarRetiva) {
            $registroTotales[] = ['value' => 'intTotalReteiva', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]] ;
            $registroTotalesLabels[] = ['value' => 'RETEIVA', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
        }

        if ($mostrarReteica) {
            $registroTotales[] = ['value' => 'intTotalReteica', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]] ;
            $registroTotalesLabels[] = ['value' => 'RETEICA', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
        }

        if ($mostrarRetefuente) {
            $registroTotales[] = ['value' => 'intTotalRetefuente', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]] ;
            $registroTotalesLabels[] = ['value' => 'RETEFUENTE', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
        }

        /**
        * Aca estan los cargos, descuentos y retenciones
        * Ver la columan cdd_tipo
        * Puede tener los valores CARGO, DESCUENTO, RETEIVA, RETEICA, RETEFUENTE
        *
        */
        $cargosDescuentosRetenciones = EtlCargosDescuentosDocumentoDaop::where('cdo_id', $cdo_id)
        ->whereNull('ddo_id')
        ->get();

        $intReteiva = 0;
        if (isset($cdo_informacion_adicional->valor_reteiva) && $cdo_informacion_adicional->valor_reteiva != '') {
            $intReteiva = number_format($cdo_informacion_adicional->valor_reteiva, 2, ',', '.');
        }

        $this->fpdf->SetFont('Arial','',7);

        /* Tipo de documento Factura de Venta o Nota de Crédito */
        if ($datosComprobante['cdo_tipo'] === "FC"){

            // Label SUBTOTAL
            $this->fpdf->setXY($posx +120,$this->fpdf->posy+163);
            $this->fpdf->Cell(40, 3, utf8_decode("SUBTOTAL: "), 0, 0, 'L');
            // Monto SUBTOTAL
            $this->fpdf->setXY($posx,$this->fpdf->posy+163);
            $this->fpdf->Cell(195, 3, utf8_decode($intSubtotal), 0, 0, 'R');                
            // Label IVA 19%
            $this->fpdf->setXY($posx +120,$this->fpdf->posy+167);
            $this->fpdf->Cell(40, 3, utf8_decode("Impuestos (".$porcentaje_iva."%): "), 0, 0, 'L');
            // Monto IVA
            $this->fpdf->setXY($posx,$this->fpdf->posy+167);
            $this->fpdf->Cell(195, 3, utf8_decode($intIva), 0, 0, 'R');
            // Label TOTAL
            $this->fpdf->setXY($posx +120,$this->fpdf->posy+176);
            $this->fpdf->Cell(40, 3, utf8_decode("TOTAL: "), 0, 0, 'L');
            // Monto TOTAL
            $this->fpdf->setXY($posx,$this->fpdf->posy+176);
            $this->fpdf->Cell(195, 3, utf8_decode($intTotalPagar), 0, 0, 'R');

            $this->fpdf->setXY($posx,$this->fpdf->posy+182);
            if($this->fpdf->NbLines(195, implode("\n", $observacion_decode)) > 2){
                $intEspacio = 3;
            }else{
                $intEspacio = 5;
            }
            $this->fpdf->MultiCell(195,$intEspacio,"Observaciones: ".utf8_decode(implode("\n", $observacion_decode)),'T','L');

            // Total en letras
            $this->fpdf->setXY($posx,$this->fpdf->posy+193);
            $this->fpdf->SetFont('Arial','',7);
            $this->fpdf->Cell(195,4,"Valor en letras: ".utf8_decode($datosComprobante['valor_letras']),1,0,'L');        
            $this->fpdf->Rect($posx, $this->fpdf->posy+197, 50, 7);
            $this->fpdf->Rect($posx, $this->fpdf->posy+197, 50, 14);
            $this->fpdf->setXY($posx +10,$this->fpdf->posy+199);
            $this->fpdf->Cell(192,4,"Total de Unidades: ".$datosComprobante['total_unidades'],0,0,'L');
            $this->fpdf->setXY($posx +51,$this->fpdf->posy+199);
            $this->fpdf->SetFont('Arial','b',7);
            $this->fpdf->MultiCell(145, 4, utf8_decode($datosComprobante['notas_finales_1']), 0, 'L');
            $this->fpdf->SetFont('Helvetica', '', 9);
            $this->fpdf->setXY($posx,$this->fpdf->posy+223);
            $this->fpdf->Cell(40, 3, utf8_decode("Fecha de validación: ".date("Y-m-d", strtotime($datosComprobante['cdo_fecha_validacion_dian']))), 0, 0, 'L');
            $this->fpdf->setXY($posx +60,$this->fpdf->posy+223);
            $this->fpdf->Cell(40, 3, utf8_decode("Hora de validación: ".date("h:i:s", strtotime($datosComprobante['cdo_fecha_validacion_dian']))), 0, 0, 'L');            
        }elseif ($datosComprobante['cdo_tipo'] === "NC"){

            // Label SUBTOTAL
            $this->fpdf->setXY($posx +120,$this->fpdf->posy+180);
            $this->fpdf->Cell(40, 3, utf8_decode("SUBTOTAL: "), 0, 0, 'L');
            // Monto SUBTOTAL
            $this->fpdf->setXY($posx +171,$this->fpdf->posy+180);
            $this->fpdf->Cell(40, 3, utf8_decode($intSubtotal), 0, 0, 'L');                
            // Label IVA 19%
            $this->fpdf->setXY($posx +120,$this->fpdf->posy+184);
            $this->fpdf->Cell(40, 3, utf8_decode("Impuestos (IVA 19%): "), 0, 0, 'L');
            // Monto IVA
            $this->fpdf->setXY($posx +171,$this->fpdf->posy+184);
            $this->fpdf->Cell(40, 3, utf8_decode($intIva), 0, 0, 'L'); 
            // Label TOTAL
            $this->fpdf->setXY($posx +120,$this->fpdf->posy+188);
            $this->fpdf->Cell(40, 3, utf8_decode("TOTAL: "), 0, 0, 'L');
            // Monto TOTAL
            $this->fpdf->setXY($posx +171,$this->fpdf->posy+188);
            $this->fpdf->Cell(40, 3, utf8_decode($intTotalPagar), 0, 0, 'L');

            // Total en letras
            // $this->fpdf->setXY($posx +3,$this->fpdf->posy+187);
            // $this->fpdf->SetFont('Arial','',7);
            // $this->fpdf->setXY($posx,$this->fpdf->posy+194);
            // $this->fpdf->Cell(100,4,"Observaciones: ".utf8_decode(strtoupper($datosComprobante['observacion'])),0,0,'L');
        }

        return ['error' => false, 'pdf' => $this->fpdf->Output('S')];
    }
}