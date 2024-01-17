<?php
/**
 * User: Juan Jose Trujillo
 * Date: 09/10/19
 * Time: 11:25 AM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_opentecn\rg830135010;

use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;
use App\Http\Modulos\Parametros\Municipios\ParametrosMunicipio;
use App\Http\Modulos\Parametros\Departamentos\ParametrosDepartamento;
use Illuminate\Support\Facades\Storage;
use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;
use Ramsey\Uuid\Uuid;

class Rg830135010_1 extends RgBase
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
        $datosComprobante['no_valido']      = $this->getFullImage("no_valido.png");
        $datosComprobante['logo_cabecera']  = $this->getFullImage("logo_open_header.png");
        $datosComprobante['logo_footer']    = $this->getFullImage("logo_open_footer.png");

        $datosComprobante['cdo_tipo'] = $cdo_tipo;
        $datosComprobante['qr'] = "";
        $datosComprobante['signaturevalue'] = "";
        $datosComprobante['cufe'] = "";
        if (!empty($signaturevalue) && !empty($qr)) {
            $datosComprobante['qr'] = $qr;
            $datosComprobante['cufe'] = $cufe;
            $datosComprobante['signaturevalue'] = $signaturevalue;
        }

        $datosComprobante['cdo_tipo_nombre']        = $cdo_tipo_nombre;
        $datosComprobante['fecha_hora_documento']   = $fecha_hora_documento;
        $datosComprobante['fecha_vencimiento']      = $fecha_vencimiento;
        $datosComprobante['dias_pago']              = $dias_pago;
        $datosComprobante['adquirente']             = $adquirente;
        $datosComprobante['adq_nit']                = $adq_nit;
        $datosComprobante['adq_tel']                = $adq_tel;
        $datosComprobante['adq_dir']                = $adq_dir;
        $datosComprobante['adq_mun']                = $adq_mun;
        $datosComprobante['adq_dep']                = $adq_dep;
        $datosComprobante['adq_pais']               = $adq_pais;

        $datosComprobante['numero_documento']       = $numero_documento;
        $datosComprobante['oferente']               = $oferente;
        $datosComprobante['ofe_nit']                = $ofe_nit;
        $datosComprobante['ofe_dir']                = $ofe_dir;
        $datosComprobante['ofe_tel']                = $ofe_tel;
        $datosComprobante['ofe_mun']                = $ofe_mun;
        $datosComprobante['ofe_resolucion']         = $ofe_resolucion;
        $datosComprobante['ofe_resolucion_fecha']   = $ofe_resolucion_fecha;

        $datosComprobante['razon_social_pt']        = $razon_social_pt;
        $datosComprobante['nit_pt']                 = $nit_pt;

        $datosComprobante['nombre_software'] = "";
        if (isset($software_pt->sft_nombre) && $software_pt->sft_nombre != "" ){
            $datosComprobante['nombre_software'] = $software_pt->sft_nombre;
        }

        // Almacenar en un arreglo los clientes DHL a los cuales se les pintará la Ciudad Servicio
        $arrClientes = ['860030380', '860038063', '830002397', '830025224'];

        $datosComprobante['ciudad_servicio'] = "";
        if(in_array($adq_nit_sin_digito, $arrClientes)){
            if(isset($cdo_informacion_adicional) && !empty($cdo_informacion_adicional->ciudad_prestacion_servicio)){

                $intCodigoDpt = substr($cdo_informacion_adicional->ciudad_prestacion_servicio, 0,2);
                $intCodigoMun = substr($cdo_informacion_adicional->ciudad_prestacion_servicio, 2);

                $departamento = ParametrosDepartamento::select(['dep_id', 'dep_codigo'])->where('dep_codigo', $intCodigoDpt)->first();

                $municipio = ParametrosMunicipio::select(['mun_codigo', 'mun_descripcion'])
                    ->where('dep_id', $departamento->dep_id)
                    ->where('mun_codigo', $intCodigoMun)
                    ->first();

                if($municipio){
                    $datosComprobante['ciudad_servicio'] = $municipio->mun_descripcion;
                }
            }
        }

        $date1  = strtotime($ofe_resolucion_fecha);
        $date2  = strtotime($ofe_resolucion_fecha_hasta);
        $diffe  = $date2 - $date1;
        $meses  = round($diffe / (60 * 60 * 24 * 30.5));
        $datosComprobante['meses'] = $meses;

        $datosComprobante['ofe_resolucion_prefijo']  = $ofe_resolucion_prefijo;
        $datosComprobante['ofe_resolucion_desde']    = $ofe_resolucion_desde;
        $datosComprobante['ofe_resolucion_hasta']    = $ofe_resolucion_hasta;
        $datosComprobante['valor_letras']            = $valor_letras;
        $datosComprobante['dad_orden_referencia']    = $dad_orden_referencia;

        $datosComprobante['regimen'] = "";
        if(isset($ofe_representacion_grafica->regimen) && $ofe_representacion_grafica->regimen != ''){
            $datosComprobante['regimen'] = $ofe_representacion_grafica->regimen;
        }

        $datosComprobante['actividad_economica'] = "";
        if(isset($ofe_representacion_grafica->actividad_economica) && $ofe_representacion_grafica->actividad_economica != ''){
            $datosComprobante['actividad_economica'] = $ofe_representacion_grafica->actividad_economica;
        }

        $datosComprobante['informacion_bancaria'] = "";
        if(isset($ofe_representacion_grafica->informacion_bancaria) && $ofe_representacion_grafica->informacion_bancaria != ''){
            $datosComprobante['informacion_bancaria'] = $ofe_representacion_grafica->informacion_bancaria;
        }

        $datosComprobante['orden_pago'] = "";
        if(isset($dad_orden_referencia->referencia) && $dad_orden_referencia->referencia != ''){
            $datosComprobante['orden_pago'] = $dad_orden_referencia->referencia;
        }

        //Información de Forma y medios de pago
        $datosComprobante['forma_pago'] = "";
        $datosComprobante['medio_pago'] = "";
        foreach ($medios_pagos_documento as $key => $medios_pagos){
            //Forma
            $forma = $medios_pagos['forma'];
            $datosComprobante['forma_pago'] = $forma['fpa_descripcion'];
            //Medio
            $medio = $medios_pagos['medio'];
            $datosComprobante['medio_pago'] = (isset($medio['mpa_descripcion']) && $medio['mpa_descripcion'] != '') ? $medio['mpa_descripcion'] : '';
        }

        $datosComprobante['validacion_dian'] = "";
        if (isset($cdo_fecha_validacion_dian) && $cdo_fecha_validacion_dian != "") {
            $datosComprobante['validacion_dian'] = $cdo_fecha_validacion_dian;
        }

        try {
            $observacion_decode = (array) json_decode($observacion);
        } catch (\Throwable $th) {
            $observacion_decode = [];
        }

        if($cdo_tipo == 'NC' || $cdo_tipo == 'ND'){
            list($factura_ref, $fecha_ref, $cufe_ref) = $this->getDocumentoReferencia($cdo_documento_referencia);
            $datosComprobante['factura_ref'] = $factura_ref;
            $datosComprobante['fecha_emision']   = $fecha_ref;
            $datosComprobante['cufe_ref']        = $cufe_ref;
        }

        $fpdf->datosComprobante = $datosComprobante;

        /*** Impresion Comprobante. ***/
        $fpdf->AddPage('P', 'Letter');

        $posx = $fpdf->posx;
        $posy = $fpdf->nPosYDet;
        $nPosYFin = 170;

        // $items = array_merge($items,$items,$items,$items,$items,$items,$items,$items,$items,$items,$items,$items);
        // $items = array_merge($items,$items,$items);
        // $items = array_merge($items,$items,$items,$items);

        // Items
        $contItem = 0;

        if (isset($items) && count($items) > 0) {
            //Propiedades de la tabla
            $fpdf->SetWidths(array(10, 15, 15, 75, 15, 25, 25));
            $fpdf->SetAligns(array("C", "C", "C", "L", "C", "R", "R"));
            $fpdf->SetLineHeight(4.5);
            $fpdf->SetFont('Arial', '', 7.5);
            $fpdf->setXY($posx, $posy);

            foreach ($items as $item) {
                $contItem++;
                
                if ($fpdf->getY() > $nPosYFin) {
                    $fpdf->Rect($posx, $posy - 6, 180, $fpdf->getY() - $posy + 6);
                    $fpdf->Line($posx + 10, $posy - 6, $posx + 10, $fpdf->getY());
                    $fpdf->Line($posx + 25, $posy - 6, $posx + 25, $fpdf->getY());
                    $fpdf->Line($posx + 40, $posy - 6, $posx + 40, $fpdf->getY());
                    $fpdf->Line($posx + 115, $posy - 6, $posx + 115, $fpdf->getY());
                    $fpdf->Line($posx + 130, $posy - 6, $posx + 130, $fpdf->getY());
                    $fpdf->Line($posx + 155, $posy - 6, $posx + 155, $fpdf->getY());
                    $fpdf->AddPage('P', 'Letter');
                    $posx = $fpdf->posx;
                    $posy = $fpdf->nPosYDet;
                    $fpdf->setXY($posx, $posy);
                }

                $fpdf->setX($posx);
                $fpdf->Row(array(
                    number_format($contItem),
                    utf8_decode($item['ddo_codigo']),
                    number_format($item['ddo_cantidad'], 0, ',', '.'),
                    utf8_decode($item['ddo_descripcion_uno']),
                    $this->getUnidad($item['und_id'], 'und_descripcion'),
                    number_format($item['ddo_valor_unitario'], 2, ',', '.'),
                    number_format($item['ddo_total'], 2, ',', '.'),
                ));
            }

            $fpdf->SetFont('Arial', '', 7);
            $fpdf->setXY($posx, $fpdf->getY() + 1);
            $fpdf->Cell(10,5,$contItem,1,0,'C');
        }

        //Rectangulo Items
        $fpdf->Rect($posx, $posy - 6, 180, $fpdf->getY() - $posy + 6);
        $fpdf->Line($posx + 10, $posy - 6, $posx + 10, $fpdf->getY());
        $fpdf->Line($posx + 25, $posy - 6, $posx + 25, $fpdf->getY());
        $fpdf->Line($posx + 40, $posy - 6, $posx + 40, $fpdf->getY());
        $fpdf->Line($posx + 115, $posy - 6, $posx + 115, $fpdf->getY());
        $fpdf->Line($posx + 130, $posy - 6, $posx + 130, $fpdf->getY());
        $fpdf->Line($posx + 155, $posy - 6, $posx + 155, $fpdf->getY());

        //Subtotales
        $posy = $fpdf->getY();
        $posyIni = $posy;

        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->setXY($posx + 130, $posyIni);
        $fpdf->Cell(25, 5, "SUBTOTAL", 0, 0, 'R');
        $fpdf->Cell(25, 5, $subtotal, 0, 0, 'R');
        $fpdf->Ln(6);
    
        $fpdf->setX($posx + 130);
        $fpdf->Cell(25, 5, "IVA", 0, 0, 'R');
        $fpdf->Cell(25, 5, $iva, 0, 0, 'R');
        $fpdf->Ln(6);

        $fpdf->setX($posx + 130);
        $fpdf->Cell(25, 5, "TOTAL", 0, 0, 'R');
        $fpdf->Cell(25, 5, $total, 0, 0, 'R');
        $fpdf->Ln(6);

        $fpdf->Line($posx + 155, $posy, $posx + 155, $posy + 17);
        $fpdf->Line($posx + 130, $posy + 5, $posx + 180, $posy + 5);
        $fpdf->Line($posx + 130, $posy + 11, $posx + 180, $posy + 11);
        $fpdf->Rect($posx + 130, $posy, 50, 17);
        
        $posy = $fpdf->getY() - 2;
        if ($fpdf->getY() > $nPosYFin) {
            $fpdf->Imprime_Item = "NO";
            $fpdf->AddPage('P', 'Letter');
            $posx = $fpdf->posx;
            $posy = $fpdf->getY()+17;            
        }

        $fpdf->setXY($posx, $posy);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(30, 5, "OBSERVACIONES:", 0, 0, 'L');
        $fpdf->Ln(0.5);
        $fpdf->setX($posx+28);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->MultiCell(110, 4, utf8_decode(implode("\n",$observacion_decode)), 0, 'L');
        $fpdf->Ln(1);

        $nPosYFin = ($cdo_tipo == 'FC') ? 170 : 165;

        if ($fpdf->getY() > $nPosYFin) {
            $fpdf->Imprime_Item = "NO";
            $fpdf->AddPage('P', 'Letter');
        }

        if($cdo_tipo == 'NC' || $cdo_tipo == 'ND'){
            $posy = 172;
            $fpdf->SetFillColor(150);
            $fpdf->SetTextColor(255);
            $fpdf->setXY($posx, $posy);
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(180,5,"Documento Referencia",0,0,'L',true);
            $fpdf->Rect($posx, $posy, 180, 5);
            $fpdf->SetTextColor(0);

            $fpdf->setXY($posx+2, $posy+5);
            $fpdf->Cell(100,4,"Factura: ".$datosComprobante['factura_ref'],0,0,'L');
            $fpdf->Ln(4);
            $fpdf->setX($posx+2);
            $fpdf->Cell(30,4,"Fecha Factura:  ".$datosComprobante['fecha_emision'] ,0,0,'L');
            $fpdf->Ln(4);
            $fpdf->setX($posx+2);
            $fpdf->SetFont('Arial', 'B', 7);
            $fpdf->Cell(25,4,"CUFE: ",0,0,'L');
            $fpdf->Ln(0.5);
            $fpdf->setX($posx+25);
            $fpdf->SetFont('Arial', '', 7);
            $fpdf->MultiCell(160,3,$datosComprobante['cufe_ref'],0,'L');
            $fpdf->Rect($posx, $posy+5, 180, 14);
        }

        return ['error' => false, 'pdf' => $fpdf->Output('S')];
    }
}
