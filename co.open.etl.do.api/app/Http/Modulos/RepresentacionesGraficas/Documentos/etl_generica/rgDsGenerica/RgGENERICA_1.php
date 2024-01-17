<?php
namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_generica\rgDsGenerica;

use App\Http\Traits\DoTrait;
use App\Http\Traits\NumToLetrasEngine;
use Illuminate\Support\Facades\Storage;
use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;

/**
 * Contorlador para la generación de representaciones gráficas de documentos soporte de indice 1.
 *
 * Class RgGENERICA_1
 * @package App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_generica\rgDsGenerica
 */
class RgGENERICA_1 extends RgBase
{
    /**
     * RgGENERICA_1 constructor.
     */
    public function initEngine(){
        //PDF
        $fpdf = $this->pdfManager();
        $fpdf->AcceptPageBreak();
        $fpdf->SetFont('Arial','',6);
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
        $this->establecerLogo($logo, $ofe_identificacion, $datosComprobante);

        //Extrayendo información de cabecera del documento soporte
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

        $datosComprobante['cdo_consecutivo']         = $cdo_consecutivo;
        $datosComprobante['rfa_prefijo']             = $rfa_prefijo;
        $datosComprobante['fecha_hora_documento']    = $fecha_hora_documento;
        $datosComprobante['fecha_vencimiento']       = $fecha_vencimiento;
        $datosComprobante['oferente']                = $oferente;
        $datosComprobante['ofe_nit']                 = $ofe_nit;
        $datosComprobante['ofe_dir']                 = $ofe_dir;
        $datosComprobante['ofe_tel']                 = $ofe_tel;
        $datosComprobante['ofe_resolucion']          = $ofe_resolucion;
        $datosComprobante['ofe_resolucion_prefijo']  = $ofe_resolucion_prefijo;
        $datosComprobante['ofe_resolucion_fecha']    = $ofe_resolucion_fecha;
        $datosComprobante['ofe_resolucion_desde']    = $ofe_resolucion_desde;
        $datosComprobante['ofe_resolucion_hasta']    = $ofe_resolucion_hasta;
        $datosComprobante['ofe_resolucion_vigencia'] = $ofe_resolucion_vigencia;
        $datosComprobante['ofe_mun']                 = $ofe_mun;
        $datosComprobante['cdo_tipo']                = $cdo_tipo;
        $datosComprobante['ofe_correo']              = $ofe_correo;
        $datosComprobante['adquirente']              = $adquirente;
        $datosComprobante['adq_nit']                 = ($tdo_codigo == "31") ? $adq_nit : $adq_nit_sin_digito;
        $datosComprobante['adq_dir']                 = $adq_dir; 
        $datosComprobante['adq_pai']                 = $adq_pais;
        $datosComprobante['adq_mun']                 = $adq_mun;
        $datosComprobante['adq_tel']                 = $adq_tel;
        $datosComprobante['dias_pago']               = $dias_pago;
        $datosComprobante['ofe_regimen']             = $ofe_regimen;
        $datosComprobante['numero_documento']        = $numero_documento;
        $datosComprobante['cufe']                    = $cufe;
        $datosComprobante['valor_letras']            = NumToLetrasEngine::num2letras($this->parserNumberController($valor_a_pagar), false, true, $cdo_moneda);

        $datosComprobante['razon_social_pt']        = $razon_social_pt;
        $datosComprobante['nit_pt']                 = $nit_pt;

        $datosComprobante['nombre_software'] = "";
        if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
            $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
        }

        try {
            $datosComprobante['observacion'] = (array) json_decode($observacion);
        } catch (\Throwable $th) {
            $datosComprobante['observacion'] = [];
        }

        // Se realiza la validación sobre la moneda del documento
        $datosComprobante['cdo_moneda'] = "";
        if($cdo_moneda != 'COP' && $cdo_moneda_extranjera == NULL){
            $datosComprobante['cdo_moneda'] = $cdo_moneda;
        }

        $datosComprobante['aplica_dos_monedas']    = false;
        $datosComprobante['cdo_moneda_extranjera'] = "";
        if($cdo_moneda != $cdo_moneda_extranjera && $cdo_moneda_extranjera != NULL){
            $datosComprobante['aplica_dos_monedas']    = true;
            $datosComprobante['cdo_moneda']            = $cdo_moneda;
            $datosComprobante['cdo_moneda_extranjera'] = $cdo_moneda_extranjera;
        }

        if($cdo_tipo != 'DS'){
            list($factura_ref, $fecha_ref, $cufe_ref) = $this->getDocumentoReferencia($cdo_documento_referencia, 'DS');
            $datosComprobante['consecutivo_ref']   = $factura_ref;
            $datosComprobante['fecha_emision_ref'] = $fecha_ref;
            $datosComprobante['cufe_ref']          = $cufe_ref;
        }

        if(isset($ofe_representacion_grafica->pais) && !empty($ofe_representacion_grafica->pais))
            $datosComprobante['adq_pai'] = $ofe_representacion_grafica->pais;
        if(isset($ofe_representacion_grafica->ciudad) && !empty($ofe_representacion_grafica->ciudad))
            $datosComprobante['adq_mun'] = $ofe_representacion_grafica->ciudad;
        if(isset($ofe_representacion_grafica->regimen) && !empty($ofe_representacion_grafica->regimen))
            $datosComprobante['ofe_regimen'] = $ofe_representacion_grafica->regimen;

        if(isset($cdo_informacion_adicional->valor_letras) && !empty($cdo_informacion_adicional->valor_letras))
            $datosComprobante['valor_letras'] = $cdo_informacion_adicional->valor_letras;

        $intTotalDescuentos = $descuentos;
        if(isset($cdo_informacion_adicional->descuento) && is_numeric($cdo_informacion_adicional->descuento))
            $intTotalDescuentos = $cdo_informacion_adicional->descuento;

        $intTotalCargos = $cargos;
        if(isset($cdo_informacion_adicional->cargo) && is_numeric($cdo_informacion_adicional->cargo))
            $intTotalCargos = $cdo_informacion_adicional->cargo;

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

        // Valores Personalizados
        $datosComprobante['valores_personalizados_ds'] = [];
        if (isset($ofe_campos_personalizados_factura) && $ofe_campos_personalizados_factura != null) {
            if(array_key_exists('valores_personalizados_ds', $ofe_campos_personalizados_factura) && !empty($ofe_campos_personalizados_factura['valores_personalizados_ds'])) {
                foreach ($ofe_campos_personalizados_factura['valores_personalizados_ds'] as $key => $campo) {
                    $valorPersonalizadoSaneado = strtolower($this->sanear_string($campo['campo']));
                    if(isset($cdo_informacion_adicional->$valorPersonalizadoSaneado) && !empty($cdo_informacion_adicional->$valorPersonalizadoSaneado)) {
                        $datosComprobante['valores_personalizados_ds'][] = [
                            'nombre_campo' => $campo['campo'],
                            'valor_campo'  => $cdo_informacion_adicional->$valorPersonalizadoSaneado
                        ];
                    }
                }
            }
        }

        // Valores Personalizados Items
        $datosComprobante['valores_personalizados_item_ds'] = [];
        if (isset($ofe_campos_personalizados_factura) && $ofe_campos_personalizados_factura != null) {
            if(array_key_exists('valores_personalizados_item_ds', $ofe_campos_personalizados_factura) && !empty($ofe_campos_personalizados_factura['valores_personalizados_item_ds'])) {
                $datosComprobante['valores_personalizados_item_ds'] = $ofe_campos_personalizados_factura['valores_personalizados_item_ds'];
            }
        }

        // Valor personalizado encabezado y pie de pagina
        $lnValorPiePagina = 0;
        $datosComprobante['valor_personalizado_pie'] = "";
        $datosComprobante['valor_personalizado_cabecera'] = "";
        if (isset($ofe_campos_personalizados_factura) && $ofe_campos_personalizados_factura != null) {
          if(isset($ofe_campos_personalizados_factura['encabezado_ds']) && $ofe_campos_personalizados_factura['encabezado_ds']) {
            $datosComprobante['valor_personalizado_cabecera'] = $ofe_campos_personalizados_factura['encabezado_ds'];
          }

          if(isset($ofe_campos_personalizados_factura['pie_ds']) && $ofe_campos_personalizados_factura['pie_ds']) {
            $datosComprobante['valor_personalizado_pie'] = $ofe_campos_personalizados_factura['pie_ds'];
            //Se calcula la cantidad de lineas que llegan en el campo personalizado de pie de pagina
            $lnValorPiePagina = $this->fpdf->NbLines(195, $datosComprobante['valor_personalizado_pie']);
          }
        }

        $datosComprobante['validacion_dian'] = ""; 
        if (isset($cdo_fecha_validacion_dian) && $cdo_fecha_validacion_dian != "") {
            $datosComprobante['validacion_dian'] = str_replace('-05:00', '', $cdo_fecha_validacion_dian);
        }

        $datosComprobante['tipo_documento'] = "";
        switch ($tdo_codigo) {
            case '22':
                $datosComprobante['tipo_documento'] = "CE";
                break;
            case '42':
                $datosComprobante['tipo_documento'] = "DIE";
                break;
            case '31':
                $datosComprobante['tipo_documento'] = "NIT";
                break;
            case '50':
                $datosComprobante['tipo_documento'] = "NOP";
                break;
            case '41':
                $datosComprobante['tipo_documento'] = "PA";
                break;
            case '47':
                $datosComprobante['tipo_documento'] = "PE";
                break;
            case '21':
                $datosComprobante['tipo_documento'] = "TE";
                break;
            case '12':
                $datosComprobante['tipo_documento'] = "TI";
                break;
            case '13':
                $datosComprobante['tipo_documento'] = "CC";
                break;
            case '91':
                $datosComprobante['tipo_documento'] = "NUIP";
                break;
            case '11':
                $datosComprobante['tipo_documento'] = "RC";
                break;
            default:
                break;
        }

        $datosComprobante['posy_fin_footer'] = $lnValorPiePagina * 3;
        $datosComprobante['line_pie_pagina'] = $lnValorPiePagina;

        //Propiedades de la tabla
        $this->nPosYDet = $this->fpdf->nPosYDet;
        $posx = $this->fpdf->GetX();
        $posy = $this->fpdf->GetY();

        if (!$datosComprobante['aplica_dos_monedas']) {
            $this->fpdf->SetWidths([10, 20, 93, 12, 30, 30]);
            $this->fpdf->SetAligns(['C', 'L', 'L', 'R', 'R', 'R']);
        } else {
            $this->fpdf->SetWidths([10, 13, 65, 11, 24, 22, 25, 25]);
            $this->fpdf->SetAligns(['C', 'L', 'L', 'R', 'R', 'R', 'R', 'R']);
        }

        $this->fpdf->SetLineHeight(3);
        $this->fpdf->SetFont('Helvetica','',7);
        $this->fpdf->setXY($posx + 10, $posy);
        //Se calcula el alto de las lineas del pie de pagina
        $nLinesPiePagina = ($lnValorPiePagina * 4.5);
        $nPosYFin = (230 - $nLinesPiePagina);

        /*** Impresion Comprobante. ***/
        $this->fpdf->datosComprobante = $datosComprobante;
        $this->fpdf->AddPage('P','Letter');
        $posx = 10;
        $intCount = 0;
        foreach ($items as $item) {
            $intCount++;

            if($this->fpdf->getY() > $nPosYFin){
                $this->fpdf->AddPage('P','Letter');
                $posx = 10;
                $posy = $this->fpdf->nPosYDet;
                $this->fpdf->setXY($posx, $posy);
            }

            $_ddo_informacion_adicional = json_decode($item['ddo_informacion_adicional']);

            $strInformacionAdicional = '';
            foreach ($datosComprobante['valores_personalizados_item_ds'] as $key => $campo) {
                $keyCampo = trim(strtolower($this->sanear_string(str_replace(' ', '_', $campo['campo']))));

                if(isset($_ddo_informacion_adicional->$keyCampo) && !empty($_ddo_informacion_adicional->$keyCampo)) {
                    $strInformacionAdicional .= $campo['campo'] . ": " . $_ddo_informacion_adicional->$keyCampo . ", ";
                }
            }

            if (!$datosComprobante['aplica_dos_monedas']) {
                $arrRows = [
                    $intCount,
                    utf8_decode($item['ddo_codigo']),
                    utf8_decode($item['ddo_descripcion_uno']."\n".rtrim($strInformacionAdicional,', ')),
                    number_format($item['ddo_cantidad'], 2,'.', ','),
                    number_format($item['ddo_valor_unitario'],2,'.',','),
                    number_format($item['ddo_total'],2,'.',',')
                ];
            } else {
                $arrRows = [
                    $intCount,
                    utf8_decode($item['ddo_codigo']),
                    utf8_decode($item['ddo_descripcion_uno']."\n".rtrim($strInformacionAdicional,', ')),
                    number_format($item['ddo_cantidad'], 2,'.', ','),
                    number_format($item['ddo_valor_unitario_moneda_extranjera'],2,'.',','),
                    number_format($item['ddo_total_moneda_extranjera'],2,'.',','),
                    number_format($item['ddo_valor_unitario'],2,'.',','),
                    number_format($item['ddo_total'],2,'.',',')
                ];
            }

            $this->fpdf->setX($posx);
            $this->fpdf->Row($arrRows);
        }
        $this->fpdf->SetFont('Arial', '', 7);
        $this->fpdf->setXY($posx+1, $this->fpdf->getY()+1);
        $this->fpdf->Cell(8,5,utf8_decode("Total Ítem: ") . $intCount,0,0,'L');
        $this->fpdf->Ln(5);

        $this->fpdf->SetLineHeight(4);
        $posy = $this->fpdf->getY();
        $valores_resumen = (isset($ofe_campos_personalizados_factura) && $ofe_campos_personalizados_factura != null && array_key_exists('valores_resumen_ds', $ofe_campos_personalizados_factura)) ? $ofe_campos_personalizados_factura['valores_resumen_ds'] : [];
        $descuentos_cabecera_personalizados = (isset($ofe_campos_personalizados_factura) && $ofe_campos_personalizados_factura != null && array_key_exists('descuentos_cabecera_personalizados_ds', $ofe_campos_personalizados_factura)) ? $ofe_campos_personalizados_factura['descuentos_cabecera_personalizados_ds'] : [];
        $cargos_cabecera_personalizados = (isset($ofe_campos_personalizados_factura) && $ofe_campos_personalizados_factura != null && array_key_exists('cargos_cabecera_personalizados_ds', $ofe_campos_personalizados_factura)) ? $ofe_campos_personalizados_factura['cargos_cabecera_personalizados_ds'] : [];
        $mostrarRetiva = (isset($ofe_campos_personalizados_factura) && $ofe_campos_personalizados_factura != null && array_key_exists('valores_resumen_ds', $ofe_campos_personalizados_factura) && !is_null($ofe_campos_personalizados_factura['valores_resumen_ds'])) ? in_array('reteiva-a-nivel-documento', $valores_resumen) : false;
        $mostrarRetefuente = (isset($ofe_campos_personalizados_factura) && $ofe_campos_personalizados_factura != null && array_key_exists('valores_resumen_ds', $ofe_campos_personalizados_factura) && !is_null($ofe_campos_personalizados_factura['valores_resumen_ds'])) ? in_array('retefuente-a-nivel-documento', $valores_resumen) : false;

        $intIva   = 0;
        $intIvaME = 0;
        foreach ($impuestos_documento as $impuesto) {
            if (array_key_exists($impuesto->tri_id, $impuestos_registrados)) {
                if ($impuestos_registrados[$impuesto->tri_id] === self::IMPUESTO_IVA) {
                    $intIva   += $impuesto->iid_valor;
                    $intIvaME += $impuesto->iid_valor_moneda_extranjera;
                }
            }
        }

        $intTotalReteiva      = 0;
        $intTotalRetefuente   = 0;
        $intTotalReteivaME    = 0;
        $intTotalRetefuenteME = 0;
        if ($mostrarRetefuente || $mostrarRetiva) {
            foreach ($detalle_cargos_descuentos as $retencion) {
                switch ($retencion->cdd_tipo) {
                    case 'RETEIVA':
                        $intTotalReteiva   += $retencion->cdd_valor;
                        $intTotalReteivaME += $retencion->cdd_valor_moneda_extranjera;
                        break;
                    case 'RETEFUENTE':
                        $intTotalRetefuente   += $retencion->cdd_valor;
                        $intTotalRetefuenteME += $retencion->cdd_valor_moneda_extranjera;
                        break;
                }
            }

            if(isset($cdo_informacion_adicional->total_retefuente) && is_numeric($cdo_informacion_adicional->total_retefuente))
                $intTotalRetefuente = $cdo_informacion_adicional->total_retefuente;
            if(isset($cdo_informacion_adicional->total_reteiva) && is_numeric($cdo_informacion_adicional->total_reteiva))
                $intTotalReteiva = $cdo_informacion_adicional->total_reteiva;
        }

        // Valores en moneda nacional
        $intSubtotal        = $this->formatWithDecimals($subtotal);
        $intIva             = $this->formatWithDecimals($intIva);
        $intTotalCargos     = $this->formatWithDecimals($intTotalCargos);
        $intTotalDescuentos = $this->formatWithDecimals($intTotalDescuentos);
        $intTotalRetefuente = $this->formatWithDecimals($intTotalRetefuente);
        $intTotalReteiva    = $this->formatWithDecimals($intTotalReteiva);
        $intTotal           = $this->formatWithDecimals($total);
        $intTotalPagar      = $this->formatWithDecimals($valor_a_pagar);

        // Valores en moneda extranjera
        $intIvaME             = $this->formatWithDecimals($intIvaME);
        $intTotalReteivaME    = $this->formatWithDecimals($intTotalReteivaME);
        $intTotalRetefuenteME = $this->formatWithDecimals($intTotalRetefuenteME);
        $intTotalDescuentosME = $this->formatWithDecimals($descuentos_moneda_extranjera);
        $intTotalCargosME     = $this->formatWithDecimals($cargos_moneda_extranjera);
        $intTotalPagarME      = $this->formatWithDecimals($valor_a_pagar_moneda_extranjera);

        $registroTotales = [
            [
                ['value' => 'subtotal', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
                ['value' => 'subtotal_moneda_extranjera', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
            ],
            [
                ['value' => 'intIva', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
                ['value' => 'intIvaME', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
            ]
        ];
        $registroTotalesLabels = [
            ['value' => 'SUBTOTAL', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]],
            ['value' => 'IVA', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
        ];

        $registroTotales[]       = [
            ['value' => 'total', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
            ['value' => 'total_moneda_extranjera', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
        ];
        $registroTotalesLabels[] = ['value' => 'TOTAL', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];

        if ($mostrarRetiva) {
            $registroTotalesRte[] = [
                ['value' => 'intTotalReteiva', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
                ['value' => 'intTotalReteivaME', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
            ];
            $registroTotalesLabelsRte[] = ['value' => 'RETEIVA', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
        }

        if ($mostrarRetefuente) {
            $registroTotalesRte[] = [
                ['value' => 'intTotalRetefuente', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
                ['value' => 'intTotalRetefuenteME', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
            ];
            $registroTotalesLabelsRte[] = [
                'value' => 'RETEFUENTE', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]
            ];
        }

        if (in_array('descuentos-a-nivel-documento', $valores_resumen)) {
            if (!empty($descuentos_cabecera_personalizados)) {
                $detalleCargosDescuentos = collect($detalle_cargos_descuentos);
                $descuentosCabeceraPersonalizados = $detalleCargosDescuentos->where('cdd_aplica', 'CABECERA')
                    ->where('cdd_tipo', 'DESCUENTO')
                    ->values()
                    ->groupBy('cdd_nombre');
                
                $cont = 1;
                foreach ($descuentosCabeceraPersonalizados as $descuento => $detalle) {
                    $tmpTotal   = 'totalAgrupadoDescuentos' . $cont;
                    $tmpTotalME = 'totalAgrupadoDescuentosME' . $cont;

                    $$tmpTotal   = 0;
                    $$tmpTotalME = 0;
                    foreach ($detalle as $objeto) {
                        $$tmpTotal   += $objeto->cdd_valor;
                        $$tmpTotalME += $objeto->cdd_valor_moneda_extranjera;
                    }
                    $$tmpTotal   = $this->formatWithDecimals($$tmpTotal);
                    $$tmpTotalME = $this->formatWithDecimals($$tmpTotalME);

                    $registroTotales[] = [
                        ['value' => 'totalAgrupadoDescuentos' . $cont, 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
                        ['value' => 'totalAgrupadoDescuentosME' . $cont, 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
                    ];

                    $strDescuento = $descuento != '' ? strtoupper($descuento) : 'DESCUENTOS';
                    $registroTotalesLabels[] = ['value' => $strDescuento, 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
                    $cont++;
                }
                $datosComprobante['valor_letras'] = NumToLetrasEngine::num2letras($this->parserNumberController($intTotalPagar), false, true, $cdo_moneda);
            } else {
                $registroTotales[] = [
                    ['value' => 'intTotalDescuentos', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
                    ['value' => 'intTotalDescuentosME', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
                ];
                $registroTotalesLabels[] = ['value' => 'DESCUENTOS', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
            }
        }

        if (in_array('cargos-a-nivel-documento', $valores_resumen)) {
            if (!empty($cargos_cabecera_personalizados)) {
                $detalleCargosDescuentos = collect($detalle_cargos_descuentos);
                $descuentosCabeceraPersonalizados = $detalleCargosDescuentos->where('cdd_aplica', 'CABECERA')
                    ->where('cdd_tipo', 'CARGO')
                    ->values()
                    ->groupBy('cdd_nombre');
                
                $cont = 1;
                foreach ($descuentosCabeceraPersonalizados as $cargo => $detalle) {
                    $tmpTotal   = 'totalAgrupadoCargos' . $cont;
                    $tmpTotalME = 'totalAgrupadoCargosME' . $cont;

                    $$tmpTotal   = 0;
                    $$tmpTotalME = 0;
                    foreach ($detalle as $objeto) {
                        $$tmpTotal   += $objeto->cdd_valor;
                        $$tmpTotalME += $objeto->cdd_valor_moneda_extranjera;
                    }
                    $$tmpTotal   = $this->formatWithDecimals($$tmpTotal);
                    $$tmpTotalME = $this->formatWithDecimals($$tmpTotalME);

                    $registroTotales[] = [
                        ['value' => 'totalAgrupadoCargos' . $cont, 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
                        ['value' => 'totalAgrupadoCargosME' . $cont, 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
                    ];

                    $strCargo = $cargo != '' ? strtoupper($cargo) : "CARGOS";
                    $registroTotalesLabels[] = ['value' => $strCargo, 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
                    $cont++;
                }
                $datosComprobante['valor_letras'] = NumToLetrasEngine::num2letras($this->parserNumberController($intTotalPagar), false, true, $cdo_moneda);
            } else {
                $registroTotales[] = [
                    ['value' => 'intTotalCargos', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
                    ['value' => 'intTotalCargosME', 'opciones' => [ 'format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
                ];
                $registroTotalesLabels[] = ['value' => 'CARGOS', 'opciones' => [ 'format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
            }
        }

        if (in_array('total-a-pagar', $valores_resumen)) {
            $registroTotales[] = [
                ['value' => 'intTotalPagar', 'opciones' => ['format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]],
                ['value' => 'intTotalPagarME', 'opciones' => ['format_number' => true, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => '', 'size' => '7.5']]]
            ];
            $registroTotalesLabels[] = ['value' => 'TOTAL A PAGAR', 'opciones' => ['format_number' => false, 'border' => 0, 'ln' => 0, 'font' => ['family' => 'Helvetica', 'style' => 'b', 'size' => '7.5']]];
        }
        
        $intTotalValores = (count($registroTotales) * 3);
        $posyFin = ((225 - $nLinesPiePagina) - $intTotalValores);
        if($this->fpdf->getY() > $posyFin){
            $this->fpdf->AddPage('P','Letter');
            $posx = 10;
        }

        $posy    = (($this->fpdf->posy + 190) - $intTotalValores) - $nLinesPiePagina;
        $posyIni = $posy;
        $this->fpdf->setXY($posx, $posy);
        $this->fpdf->Cell(195, 0, '', "B", 0, 'C', true);
        $this->fpdf->setXY($posx, $posy+1);
        $this->fpdf->MultiCell(127,3.5,"SON: ".utf8_decode($datosComprobante['valor_letras']),0,'L');
        $posy = ($this->fpdf->getY() - 1);
        $this->fpdf->setXY($posx, $posy);
        $this->fpdf->Cell(135, 6, utf8_decode('Observación:'), 0, 0, 'L');
        $this->fpdf->setXY($posx, $posy + 5);
        $this->fpdf->MultiCell(125, 3, utf8_decode(implode("\n",$datosComprobante['observacion'])), 0, 'J');
        if ($datosComprobante['aplica_dos_monedas']) {
            $this->fpdf->setXY($posx, $this->fpdf->getY() - 1);
            $this->fpdf->Cell(135, 6, "Tasa de Cambio: " . number_format($cdo_trm, 2, ',', '.'), 0, 0, 'L');
        }

        // Si aplica las dos monedas se pinta el título de la moneda al inicio de cada columna de totales
        if ($datosComprobante['aplica_dos_monedas']) {
            $this->fpdf->SetFont('Arial','B',7);
            $this->fpdf->setXY($posx + 145, $posyIni);
            $this->fpdf->Cell(25, 6, $datosComprobante['cdo_moneda_extranjera'], 0, 0, 'R');
            $this->fpdf->Cell(25, 6, $datosComprobante['cdo_moneda'], 0, 0, 'R');
            $posyIni += 3;
        }

        if(${$registroTotales[1][0]['value']} == 0) {
            $registroTotalesLabels[1]['value'] = '';
            ${$registroTotales[1][0]['value']} = '';
        }

        $this->fpdf->SetFont('Arial','',7);
        foreach ($registroTotales as $k => $totales) {
            $this->fpdf->SetFillColor(240, 240, 240);
            if (!$datosComprobante['aplica_dos_monedas']) {
                $this->imprimirValor($posx + 140, $posyIni + 2 + $k * 3, $registroTotalesLabels[$k]['value'], 60, 3, 'L', $registroTotalesLabels);
                $this->imprimirValor($posx + 140, $posyIni + 2 + $k * 3, ${$totales[0]['value']}, 50, 3, 'R');
            } else {
                $this->imprimirValor($posx + 125, $posyIni + 2 + $k * 3, $registroTotalesLabels[$k]['value'], 60, 3, 'L', $registroTotalesLabels);
                $this->imprimirValor($posx + 120, $posyIni + 2 + $k * 3, ${$totales[1]['value']}, 50, 3, 'R');
                $this->imprimirValor($posx + 145, $posyIni + 2 + $k * 3, ${$totales[0]['value']}, 50, 3, 'R');
            }
        }

        // Valida si tiene no tiene ReteIVA para no mostrar sus valores
        if(${$registroTotalesRte[0][0]['value']} == 0) {
            $registroTotalesLabelsRte[0]['value'] = '';
            ${$registroTotalesRte[0][0]['value']} = '';
        }

        // Valida si tiene no tiene ReteFuente para no mostrar sus valores
        if(${$registroTotalesRte[1][0]['value']} == 0) {
            $registroTotalesLabelsRte[1]['value'] = '';
            ${$registroTotalesRte[1][0]['value']} = '';
        }

        if(${$registroTotalesRte[0][0]['value']} != 0 || ${$registroTotalesRte[1][0]['value']} != 0){
            // Recuadro donde se muestran las retenciones
            $this->fpdf->SetFont('Arial','B',7);
            $this->fpdf->setXY($posx + 153, 200);
            $this->fpdf->Cell(25, 6, "VALORES INFORMATIVOS",0,0,'C');
            $this->fpdf->setXY($posx + 140, 208);
            $this->fpdf->Cell(50, 3.3, "RETENCIONES",1,1,'L', true);
            $this->fpdf->SetFont('Arial','',7);
            $this->fpdf->Rect($posx + 140, 211.3, 50, 8);
            foreach ($registroTotalesRte as $k => $totales) {
                $this->fpdf->SetFillColor(240, 240, 240);
                if (!$datosComprobante['aplica_dos_monedas']) {
                    $this->imprimirValor($posx + 140, $posyIni + 35 + $k * 3, $registroTotalesLabelsRte[$k]['value'], 60, 3, 'L', $registroTotalesLabelsRte);
                    $this->imprimirValor($posx + 140, $posyIni + 35 + $k * 3, ${$totales[0]['value']}, 50, 3, 'R');
                } else {
                    $this->imprimirValor($posx + 125, $posyIni + 35 + $k * 3, $registroTotalesLabelsRte[$k]['value'], 60, 3, 'L', $registroTotalesLabelsRte);
                    $this->imprimirValor($posx + 120, $posyIni + 35 + $k * 3, ${$totales[1]['value']}, 50, 3, 'R');
                    $this->imprimirValor($posx + 145, $posyIni + 35 + $k * 3, ${$totales[0]['value']}, 50, 3, 'R');
                }
            }
        }

        return ['error' => false, 'pdf' => $this->fpdf->Output('S')];
    }

    /**
     * Configura el log de la representacion gráfica.
     *
     * @param $logo
     * @param string $ofe_identificacion Identificación del Oferente
     * @param array $datosComprobante Infomración para pintar la RG
     */
    public function establecerLogo($logo, string $ofe_identificacion, array &$datosComprobante) {
        // No se ha cargado un logo para una RG Generica
        if (empty($logo)) {
            $this->fpdf->setImageHeader($this->getFullImage('logo' . $ofe_identificacion . '_ds.png'));
            DoTrait::setFilesystemsInfo();
            $datosComprobante['no_valido'] = $directorio = Storage::disk(config('variables_sistema.ETL_LOCAL_STORAGE'))->getDriver()->getAdapter()->getPathPrefix()  .
                'commons/no_valido.png';
        }
        else {
            // La ruta del logo ha sido resuelta en el controlador
            $this->fpdf->setImageHeader($logo);
            DoTrait::setFilesystemsInfo();
            $datosComprobante['no_valido'] = $directorio = Storage::disk(config('variables_sistema.ETL_LOCAL_STORAGE'))->getDriver()->getAdapter()->getPathPrefix()  .
                'commons/no_valido.png';
        }
    }
}
