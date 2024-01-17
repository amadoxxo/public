<?php
/**
 * Created by PhpStorm.
 * User: Juan Jose Trujillo
 * Date: 08/10/19
 * Time: 11:25 AM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_opentecn\rg830135010;

use App\Http\Modulos\RepresentacionesGraficas\Core\RgBase;
use Illuminate\Support\Facades\Storage;
use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;
use Ramsey\Uuid\Uuid;

class PDF830135010_1 extends PDFBase {
    
    public $Imprime_Item;

    function Header() {


        if($this->datosComprobante['signaturevalue'] == '' && $this->datosComprobante['qr'] ==''){
            $this->Image($this->datosComprobante['no_valido'],20,50,180,180);
        }
        
        $posx = 20;
        $posy = 8;
        
        $this->posx = $posx;
        $this->posy = $posy;

        //Logos
        $this->Image($this->datosComprobante['logo_cabecera'], $posx, $posy-6, 180);
        $this->Image($this->datosComprobante['logo_footer'], $posx, $posy+225, 180);

        $this->SetFont('Arial','',6);
        $this->TextWithDirection(201,80,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): ".$this->datosComprobante['razon_social_pt']." NIT: ".$this->datosComprobante['nit_pt']." NOMBRE DEL SOFTWARE: ".$this->datosComprobante['nombre_software']),'D');

        ## Datos Adquirente ##
        $this->setXY($posx,$posy+30);
        $this->SetFont('Arial','',7.5);
        $this->Cell(30,5,"Fecha de la ".utf8_decode($this->datosComprobante['cdo_tipo_nombre']).": ".$this->datosComprobante['fecha_hora_documento'],0,0,'L');
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(30,5,"Fecha de vencimiento: ".$this->datosComprobante['fecha_vencimiento'],0,0,'L');
        $this->Ln(4.5);
        if($this->datosComprobante['cdo_tipo'] == "FC"){
            $this->setX($posx);
            $this->Cell(30,5,"Forma de pago: ".utf8_decode($this->datosComprobante['forma_pago']),0,0,'L');
            $this->Ln(4.5);
            if($this->datosComprobante['ciudad_servicio'] != ""){
                $this->setX($posx);
                $this->Cell(30,5,utf8_decode("Ciudad Prestación Servicio: ".$this->datosComprobante['ciudad_servicio']),0,0,'L');
                $this->Ln(4.5);
            }
            $this->setX($posx);
            $this->Cell(30,5,"Medio de pago: ".utf8_decode($this->datosComprobante['medio_pago']),0,0,'L');
            $this->Ln(4.5);
            $this->setX($posx);
            $this->Cell(30,5,"Orden de pago: ".utf8_decode($this->datosComprobante['orden_pago']),0,0,'L');
        }else{
            $this->setX($posx);
            $this->Cell(30,5,"Forma de pago: A ". $this->datosComprobante['dias_pago'] .utf8_decode(" días"),0,0,'L');
        }
        $this->Ln(8);

        $this->setX($posx);
        $this->Cell(30,5,"Facturado a",0,0,'L');
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(30,5,utf8_decode($this->datosComprobante['adquirente']),0,0,'L');
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(30,5,"N.I.T: ".$this->datosComprobante['adq_nit'],0,0,'L');
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(30,5,"Tel: ".$this->datosComprobante['adq_tel'],0,0,'L');
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(30,5,"Dir: ".$this->datosComprobante['adq_dir'],0,0,'L');
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(30,5,utf8_decode($this->datosComprobante['adq_mun']." ".$this->datosComprobante['adq_dep']),0,0,'L');
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(30,5,"Pais: ".utf8_decode($this->datosComprobante['adq_pais']),0,0,'L');
        $this->Ln(4.5);
        $posyfin = $this->getY();

        ## Datos OFE ##
        $this->setXY($posx+150,$posy+30);
        $this->SetFont('Arial','B',7.5);
        if($this->datosComprobante['cdo_tipo'] == "FC"){
            $this->Cell(30,5,utf8_decode("FACTURA ELECTRÓNICA DE VENTA No. ").$this->datosComprobante['numero_documento'],0,0,'R');
            $this->Ln(4.5);
        }else{
            $this->Cell(30,5,utf8_decode(mb_strtoupper($this->datosComprobante['cdo_tipo_nombre']))." No. ".$this->datosComprobante['numero_documento'],0,0,'R');
            $this->Ln(4.5);
        }
        $this->SetFont('Arial','',7.5);
        $this->setX($posx+150);
        $this->Cell(30,5,utf8_decode($this->datosComprobante['oferente']),0,0,'R');
        $this->Ln(4.5);
        $this->setX($posx+150);
        $this->Cell(30,5,utf8_decode("N.I.T Nº: ").$this->datosComprobante['ofe_nit'],0,0,'R');
        $this->Ln(4.5);
        $this->setX($posx+150);
        $this->Cell(30,5,utf8_decode($this->datosComprobante['regimen']),0,0,'R');
        $this->Ln(4.5);
        if(trim($this->datosComprobante['actividad_economica']) != ""){
            $this->setX($posx+150);
            $this->Cell(30,5,utf8_decode($this->datosComprobante['actividad_economica']),0,0,'R');
            $this->Ln(4.5);
        }
        $this->setX($posx+150);
        $this->Cell(30,5,"Dir.: ".utf8_decode($this->datosComprobante['ofe_dir']),0,0,'R');
        $this->Ln(4.5);
        $this->setX($posx+150);
        $this->Cell(30,5,"Tel.: ".$this->datosComprobante['ofe_tel'],0,0,'R');
        $this->Ln(4.5);
        $this->setX($posx+150);
        $this->Cell(30,5,utf8_decode($this->datosComprobante['ofe_mun']),0,0,'R');
        $this->Ln(4.5);

        if($this->datosComprobante['cdo_tipo'] == "FC"){
            $this->setX($posx+150);
            $this->Cell(30,5,utf8_decode("Resolución DIAN: ".$this->datosComprobante['ofe_resolucion']),0,0,'R');
            $this->Ln(4.5);
            $this->setX($posx+150);
            $this->Cell(30,5,utf8_decode(" Fecha de Expedición ").$this->datosComprobante['ofe_resolucion_fecha'],0,0,'R');
            $this->Ln(4.5);
            $this->setX($posx+150);
            $this->Cell(30,5,"Vigencia: ".$this->datosComprobante['meses']." Meses",0,0,'R');
            $this->Ln(4.5);
            $this->setX($posx+150);
            $this->Cell(30,5,utf8_decode("Numeracion Autorizada ").$this->datosComprobante['ofe_resolucion_prefijo']." desde el No. ".$this->datosComprobante['ofe_resolucion_desde']." hasta el No. ".$this->datosComprobante['ofe_resolucion_hasta'],0,0,'R');
            $this->Ln(4.5);
        }

        if($this->datosComprobante['cdo_tipo'] == "FC"){
            if(trim($this->datosComprobante['informacion_bancaria']) != ""){
                $this->setX($posx+150);
                $this->Cell(30,5,utf8_decode($this->datosComprobante['informacion_bancaria']),0,0,'R');
                $this->Ln(4.5);
            }
        }

        if($posyfin < $this->getY()){
            $posyfin = $this->getY();
        }
        if($this->Imprime_Item != "NO"){
            $posy = $posyfin+1;
            $this->setXY($posx,$posy);
            $this->SetFillColor(150);
            $this->SetTextColor(255);
            $this->SetFont('Arial','B',8);
            $this->Cell(10,6,"Item",0,0,'C',true);
            $this->Cell(15,6,"Codigo.",0,0,'C',true);
            $this->Cell(15,6,"Cantidad.",0,0,'C',true);
            $this->Cell(75,6,utf8_decode("Descripción"),0,0,'C',true);
            $this->Cell(15,6,utf8_decode("Unidad"),0,0,'C',true);
            $this->Cell(25,6,"Valor Unitario.",0,0,'C',true);
            $this->Cell(25,6,"Valor Total",0,0,'C',true);
            
            $this->Line($posx,$posy+6,$posx+180,$posy+6);
        }
        $this->nPosYDet = $posy+6;
    }

    function Footer() {

        /*** Impresion Datos QR y Firma. ***/
        $posy = 192;
        $posx = 20;

        $this->setXY($posx,$posy);
        if($this->datosComprobante['cdo_tipo'] == "FC"){
            $this->SetFont('Arial','',7.5);
            $this->Cell(50,4,utf8_decode("FECHA Y HORA VALIDACIÓN DIAN: ").$this->datosComprobante['validacion_dian'],0,0,'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->SetFont('Arial','B',7.5);
            $this->MultiCell(145,4,utf8_decode("ESTA FACTURA DE VENTA SE ASIMILA EN SUS EFECTOS LEGALES A LA LETRA DE CAMBIO, Art. 774, 775, 776 Y SIGUIENTES DE C.C. LA NO CANCELACION A SU VENCIMIENTO, CAUSARA EL MAXIMO INTERES PERMITIDO LEGALMENTE."),0,'L');
            $this->Ln(1);
        }
        $this->setX($posx);
        $this->SetFont('Arial','B',7.5);
        $this->MultiCell(145,4,utf8_decode("SON: ".$this->datosComprobante['valor_letras']),0,'L');
        $this->Ln(1);

        if($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != ""){

            $this->setX($posx);
            $this->SetFont('Arial','',8);
            $this->Cell(40,4,utf8_decode("Representación Impresa de la ". $this->datosComprobante['cdo_tipo_nombre'] ." electrónica:"),0,0,'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->SetFont('Arial','',7);
            $this->Cell(130,3,utf8_decode("Firma Electrónica:"),0,0,'L');
            $this->Ln(3);
            $this->setX($posx);
            $this->SetFont('Arial','',6.5);
            $this->MultiCell(145,3.5,$this->datosComprobante['signaturevalue'],0,'J');

            
            $dataURI = "data:image/png;base64, ".base64_encode((string) \QrCode::format('png')->size(80)->margin(0)->generate($this->datosComprobante['qr']));
            $pic = $this->getImage($dataURI);
            if ($pic!==false) $this->Image($pic[0], $posx+148,$posy+18,30,30, $pic[1]);

            $this->setXY($posx+145,$posy+1);
            $this->SetFont('Arial','B',8);
            $this->Cell(70,3,($this->datosComprobante['cdo_tipo'] == "FC") ? "CUFE:" : "CUDE:",0,0,'L');
            $this->Ln(4);
            $this->setX($posx+145);
            $this->SetFont('Arial','',6.5);
            $this->MultiCell(35,3,utf8_decode($this->datosComprobante['cufe']),0,'J');
        }
    }

    function TextWithDirection($x, $y, $txt, $direction='U') {
        if ($direction=='R')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='L')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='U')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='D')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        else
            $s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        if ($this->ColorFlag)
            $s='q '.$this->TextColor.' '.$s.' Q';
        $this->_out($s);
    }
}