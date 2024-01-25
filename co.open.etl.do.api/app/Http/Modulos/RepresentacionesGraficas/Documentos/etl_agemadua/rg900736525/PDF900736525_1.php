<?php 
/**
 * User: Jhon Escobar
 * Date: 21/09/2020
 * Time: 10:19 AM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_agemadua\rg900736525;

use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;

class PDF900736525_1 extends PDFBase{

	function Header() {

        if($this->datosComprobante['signaturevalue'] == '' && $this->datosComprobante['qr'] ==''){
            $this->Image($this->datosComprobante['no_valido'], 20, 50, 180);
        }

        $strTitulo = ($this->datosComprobante['cdo_tipo'] == "FC") ? "FACTURA ELECTRÓNICA\nDE VENTA" : $this->datosComprobante['cdo_tipo_nombre']."\nELECTRÓNICA";

        $posx = 10;
        $posy = 0;

        $this->posx = $posx;
        $this->posy = $posy;

        // membrete
        $this->Image($this->datosComprobante['hoja_membrete'], 0, 0, 217);

        $this->SetFont('Times', '', 6);
        $this->TextWithDirection(7,210,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): ".$this->datosComprobante['razon_social_pt']." NIT: ".$this->datosComprobante['nit_pt']." NOMBRE DEL SOFTWARE: ".$this->datosComprobante['nombre_software']),'U');

        //FACTURA
        $this->setXY($posx+75,$posy+7);
        $this->SetFont('Times','IB',9);
        $this->MultiCell(60,4, utf8_decode(mb_strtoupper($strTitulo)),0,"C");
        $this->setX($posx+75);
        $this->Cell(60,4, "No. ".$this->datosComprobante['rfa_prefijo']."    ".$this->datosComprobante['cdo_consecutivo'],0,0,"C");
        $this->RoundedRect($posx+75, $posy+6, 60, 14, 2);

        $this->setXY($posx+10,$posy+27);
        $this->SetFont('Times','',8);
        $this->MultiCell(170,3.5, utf8_decode($this->datosComprobante['resolucion']),0,"C");
        $this->Ln(0.5);
        $this->setX($posx);
        $this->MultiCell(195,3.5, utf8_decode($this->datosComprobante['regimen']),0,"C");

        //DATOS ADQUIRENTE
        $posy = $this->getY()+1;
        $posyIni = $posy;
        $this->setXY($posx, $posy+1);
        $this->SetFont('Times','B',10);
        $this->Cell(18,3.5, "Cliente:", 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+18);
        $this->MultiCell(92,3.5, utf8_decode($this->datosComprobante['adquirente']), 0,"L");
        $this->Ln(1);
        $this->setX($posx);
        $this->SetFont('Times','B',10);
        $this->Cell(18,3.5, "Nit:", 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+18);
        $this->MultiCell(92,3.5, $this->datosComprobante['adq_nit'], 0,"L");
        $this->Ln(1);
        $this->setX($posx);
        $this->SetFont('Times','B',10);
        $this->Cell(18,3.5, "Direccion:", 0,0,"L");
        $this->SetFont('Times','',9);
        $this->setX($posx+18);
        $this->MultiCell(92,3.5, utf8_decode($this->datosComprobante['adq_dir']), 0,"L");
        $this->Ln(1);
        $this->setX($posx);
        $this->SetFont('Times','B',10);
        $this->Cell(18,3.5, "Telefono:", 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+18);
        $this->MultiCell(92,3.5, $this->datosComprobante['adq_tel'], 0,"L");
        $this->Ln(1);
        $this->setX($posx);
        $this->SetFont('Times','B',10);
        $this->Cell(18,3.5, "Ciudad:", 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+18);
        $this->MultiCell(92,3.5, utf8_decode($this->datosComprobante['adq_mun']), 0,"L");
        $this->Ln(1);
        $this->setX($posx);
        $this->SetFont('Times','B',10);
        $this->Cell(18,3.5, "Email:", 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+18);
        $this->MultiCell(92,3.5, $this->datosComprobante['adq_correo'], 0,"L");
        $posyFin = $this->getY()+1;

        $this->setXY($posx+110, $posy+1);
        $this->SetFont('Times','B',10);
        $this->Cell(41,3.5, "DO:", 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+158);
        $this->MultiCell(38,3.5, $this->datosComprobante['do'], 0,"L");
        $this->Ln(1);
        $this->setX($posx+110);
        $this->SetFont('Times','B',10);
        $this->Cell(41,3.5, "Pedido:", 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+158);
        $this->MultiCell(38,3.5, $this->datosComprobante['pedido'], 0,"L");
        $this->Ln(1);
        $this->setX($posx+110);
        $this->SetFont('Times','B',10);
        $this->Cell(41,3.5, "Doc. Trans:", 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+158);
        $this->MultiCell(38,3.5, $this->datosComprobante['doc_transporte'], 0,"L");
        $this->Ln(1);
        $this->setX($posx+110);
        $this->SetFont('Times','B',10);
        $this->Cell(41,3.5, "Fecha y Hora ".(($this->datosComprobante['cdo_tipo'] == "FC") ? "Factura:" : "Nota:"), 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+158);
        $this->MultiCell(38,3.5, $this->datosComprobante['fecha_hora_documento'], 0,"L");
        $this->Ln(1);
        $this->setX($posx+110);
        $this->SetFont('Times','B',10);
        $this->Cell(41,3.5, "Fecha Vencimiento ".(($this->datosComprobante['cdo_tipo'] == "FC") ? "Factura:" : "Nota:"), 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+158);
        $this->MultiCell(38,3.5, $this->datosComprobante['fecha_vencimiento'], 0,"L");
        $this->Ln(1);
        $this->setX($posx+110);
        $this->SetFont('Times','B',10);
        $this->Cell(41,3.5, "Moneda:", 0,0,"L");
        $this->SetFont('Times','',8);
        $this->setX($posx+158);
        $this->MultiCell(38,3.5, $this->datosComprobante['moneda'], 0,"L");
        $this->Ln(1);
        $posy = ($posyFin > $this->getY()) ? $posyFin : $this->getY();
        $this->RoundedRect($posx, $posyIni, 195, $posy-$posyIni, 2);
        $posy -= 2;

        if ($this->datosComprobante['cdo_tipo'] != "FC") {
            $posy += 3;
            $this->setXY($posx, $posy+1);
            $this->SetFont('Times','B',10);
            $this->Cell(25,3.5, "FACTURA:", 0,0,"L");
            $this->SetFont('Times','B',8);
            $this->setX($posx+25);
            $this->MultiCell(67,3.5, utf8_decode($this->datosComprobante['consecutivo_ref']), 0,"L");
            $this->Ln(1);
            $this->setXY($posx+90, $posy+1);
            $this->SetFont('Times','B',10);
            $this->Cell(36,3.5, "FECHA FACTURA:", 0,0,"L");
            $this->SetFont('Times','',8);
            $this->setX($posx+126);
            $this->MultiCell(84,3.5, utf8_decode($this->datosComprobante['fecha_emision']), 0,"L");
            $this->Ln(1);
            $this->setX($posx);
            $this->SetFont('Times','B',10);
            $this->Cell(25,3.5, "CUFE:", 0,0,"L");
            $this->SetFont('Times','',8);
            $this->setX($posx+25);
            $this->MultiCell(165,3.5, utf8_decode($this->datosComprobante['cufe_ref']), 0,"L");
            $this->Ln(1);
            $this->RoundedRect($posx, $posy, 195, $this->getY()-$posy+2, 2);
            
            $posy = $this->getY();
        }

        $this->setXY($posx, $posy + 4);
        $this->SetFont('Times','B',8);
        $this->Cell(10,5, "ITEM",0,0,'C');
        $this->Cell(16,5, "CODIGO",0,0,'C');
        $this->Cell(62,5, utf8_decode("DESCRIPCIÓN"), 0,0,'C');
        $this->Cell(16,5, "UNIDAD",0,0,'C');
        $this->Cell(16,5, "CANTIDAD",0,0,'C');
        $this->Cell(25,5, "VLR. UNITARIO",0,0,'C');
        $this->Cell(25,5, "VALOR USD",0,0,'C');
        $this->Cell(25,5, "VALOR COP",0,0,'C');
        $this->ln(3);

        $this->nPosYFin = $posy+9;
    }

	function Footer() {

        //Datos QR y Firma
        $posx = 10;
        $posy = $this->posy+1;

        if ($this->datosComprobante['validacion_dian'] != "") {
            $this->setXY($posx, $posy+1);
            $this->SetFont('Times','',8);
            $this->Cell(50,3, utf8_decode("FECHA Y HORA VALIDACIÓN DIAN: ").$this->datosComprobante['validacion_dian'],0,0,'L');
        }
            
        if($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != ""){
            $dataURI = "data:image/png;base64, ".base64_encode((string) \QrCode::format('png')->size(82)->margin(0)->generate($this->datosComprobante['qr']));
            $pic = $this->getImage($dataURI);
            if ($pic!==false) $this->Image($pic[0], $posx + 146, $posy + 9,27,27, $pic[1]);
            
            $this->setXY($posx, $posy + 6);
            $this->SetFont('Times','B',8);
            $this->Cell(110,3,utf8_decode("REPRESENTACIÓN IMPRESA DE LA ". mb_strtoupper($this->datosComprobante['cdo_tipo_nombre'])." ELECTRÓNICA"),0,0,'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->SetFont('Times','B',7);
            $this->Cell(110,3,utf8_decode("Firma Electrónica:"),0,0,'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->SetFont('Times','',6);
            $this->MultiCell(140,3,$this->datosComprobante['signaturevalue'],0,'J');
            $this->Ln(1);
            $this->setXY($posx+146, $posy);
            $this->SetFont('Times','B',7);
            $this->Cell(100,3, ($this->datosComprobante['cdo_tipo'] == "FC") ? "CUFE:" : "CUDE:",0,0,'L');
            $this->Ln(3);
            $this->setX($posx+146);
            $this->SetFont('Times','',6);
            $this->MultiCell(50,2,utf8_decode($this->datosComprobante['cufe']),0,'L');
            $this->Rect($posx, $posy-1, 195, 36);
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
