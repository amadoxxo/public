<?php
/**
 * User: Jhon Escobar
 * Date: 24/11/20
 * Time: 11:55 AM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_munifaca\rg899999328;

use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;

class PDF899999328_1 extends PDFBase {

    function Header() {

        if ($this->datosComprobante['cdo_tipo'] != "FC") {
            if ($this->datosComprobante['signaturevalue'] == '' && $this->datosComprobante['qr'] == '') {
                $this->Image($this->datosComprobante['no_valido'], 20, 50, 180, 180);
            }
    
            $this->SetFont('Arial', '', 6);
            $this->TextWithDirection(208,42,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): " . $this->datosComprobante['razon_social_pt'] . " NIT: " . $this->datosComprobante['nit_pt'] . " NOMBRE DEL SOFTWARE: " . $this->datosComprobante['nombre_software']),'D');
    
            $posx = 10;
            $posy = 10;
            $this->SetFont('Arial', '', 6);

            //logo
            $this->Image($this->imageHeader, $posx + 1, $posy + 2, 43, 16);
            $this->Rect($posx, $posy, 45, 20);
            
            //Informacion OFE
            $this->setXY($posx + 45, $posy);
            $this->SetFont('Arial', 'B', 10);
            $this->MultiCell(110, 5, utf8_decode($this->datosComprobante['oferente']), 0, 'C');
            $this->Rect($posx + 45, $posy, 110, 10);
            $this->setXY($posx + 45, $posy + 11);
            $this->SetFont('Arial', 'B', 9);
            $this->MultiCell(110, 4, utf8_decode("PROCESO FINANCIERO\nNIT: " . $this->datosComprobante['ofe_nit']), 0, 'C');
            $this->Rect($posx + 45, $posy, 110, 20);
            $this->setXY($posx + 155, $posy);
            $this->MultiCell(40, 10, utf8_decode("Código:"), 1, 'L');
            $this->setX($posx + 155);
            $this->MultiCell(40, 5, utf8_decode("Versión:"), 1, 'L');
            $this->setX($posx + 155);
            $this->MultiCell(40, 5, utf8_decode("Página: " . $this->PageNo() . ' de {nb}'), 1, 'L');

            //Nombre del documento
            $strTitulo = $this->datosComprobante['cdo_tipo'] == "NC" ? "CREDITO" : "DEBITO";
            $this->ln(5);
            $this->setX($posx);
            $this->SetFont('Arial', 'B', 12);
            $this->MultiCell(195, 5, "NOTA " . $strTitulo . " ELECTRONICA Nro: " . $this->datosComprobante['numero_documento'], 0, 'C');

            $this->ln(5);
            $posy = $this->GetY();

            //Datos adquirente
            $this->setX($posx);
            $this->SetFont('Arial', '', 9);
            $this->Cell(20, 5, utf8_decode("SEÑORES:"), 0, 0, 'L');
            $this->SetFont('Arial', 'B', 9);
            $this->MultiCell(85, 4, utf8_decode($this->datosComprobante['adquirente']), 0, 'L');
            $posyI = $this->GetY();
            $this->setXY($posx + 105, $posy);
            $this->SetFont('Arial', '', 9);
            $this->Cell(8, 5, utf8_decode("NIT"), 0, 0, 'L');
            $this->ln(4);
            $this->setX($posx + 105);
            $this->SetFont('Arial', 'B', 9);
            $this->MultiCell(37, 5, utf8_decode($this->datosComprobante['adq_nit']), 0, 'L');
            $this->setXY($posx + 150, $posy);
            $this->SetFont('Arial', '', 9);
            $this->Cell(8, 5, utf8_decode("Fecha"), 0, 0, 'L');
            $this->ln(4);
            $this->setX($posx + 150);
            $this->SetFont('Arial', 'B', 9);
            $this->MultiCell(37, 5, date("d/m/Y", strtotime($this->datosComprobante['cdo_fecha'])), 0, 'L');
            $posyD = $this->GetY();
            $nPosyFin = $posyI > $posyD ? $posyI : $posyD;
            $this->Rect($posx,$posy,195,$nPosyFin - $posy);
            $this->Line($posx + 105, $posy, $posx + 105, $nPosyFin);
            $this->Line($posx + 150, $posy, $posx + 150, $nPosyFin);

            $posy = $this->GetY();
            $this->setXY($posx, $posy);
            $this->SetFont('Arial', '', 9);
            $this->Cell(21, 5, utf8_decode("DIRECCIÓN:"), 0, 0, 'L');
            $this->SetFont('Arial', 'B', 9);
            $this->MultiCell(84, 4, utf8_decode($this->datosComprobante['adq_dir']), 0, 'L');
            $posyI = $this->GetY();
            $this->setXY($posx + 105, $posy);
            $this->SetFont('Arial', '', 9);
            $this->Cell(8, 5, utf8_decode("TEL:"), 0, 0, 'L');
            $this->SetFont('Arial', 'B', 9);
            $this->MultiCell(37, 5, utf8_decode($this->datosComprobante['adq_tel']), 0, 'L');
            $this->setXY($posx + 150, $posy);
            $this->SetFont('Arial', '', 9);
            $this->Cell(14, 5, utf8_decode("CIUDAD:"), 0, 0, 'L');
            $this->SetFont('Arial', 'B', 9);
            $this->MultiCell(31, 5, utf8_decode($this->datosComprobante['adq_mun']), 0, 'L');
            $posyD = $this->GetY();
            $nPosyFin = $posyI > $posyD ? $posyI : $posyD;
            $this->Rect($posx,$posy,195,$nPosyFin - $posy);
            $posy = $this->getY();

            $this->setXY($posx, $posy+5);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(10, 5, utf8_decode("ITEM"), 1, 0, 'C');
            $this->Cell(16, 5, utf8_decode("CODIGO"), 1, 0, 'C');
            $this->Cell(16, 5, utf8_decode("CANTIDAD"), 1, 0, 'C');
            $this->Cell(77, 5, utf8_decode("DESCRIPCIÓN"), 1, 0, 'C');
            $this->Cell(16, 5, utf8_decode("UNIDAD"), 1, 0, 'C');
            $this->Cell(30, 5, utf8_decode("VALOR UNITARIO"), 1, 0, 'C');
            $this->Cell(30, 5, utf8_decode("VALOR TOTAL"), 1, 0, 'C');

            $this->posy = $this->GetY();
            $this->posx = $posx;
        }
        
    }

    function Footer() {
        if ($this->datosComprobante['cdo_tipo'] != "FC") {
            $strTitulo = $this->datosComprobante['cdo_tipo'] == "NC" ? "CREDITO" : "DEBITO";

            $this->SetFont('Arial', 'B', 6);
            $posx = $this->posx;
            $posy = 225;
            $this->setXY($posx, $posy);

            if ($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != "") {
                $this->SetFont('Arial', 'B', 8);
                $this->MultiCell(190, 5, utf8_decode($this->datosComprobante['FechaValidacionDian']), 0, 'L');
                $this->Ln(3);
                $this->setX($posx);
                if ($this->datosComprobante['cdo_tipo'] == 'FC') {
                    $this->Cell(155, 4, utf8_decode("REPRESENTACION IMPRESA DE LA FACTURA ELECTRONICA"), 0, 0, 'L');
                } else {
                    $this->Cell(155, 4, utf8_decode("REPRESENTACION IMPRESA DE LA NOTA " . $strTitulo . " ELECTRONICA"), 0, 0, 'L');
                }
                $this->SetFont('Arial', '', 7);
                $this->Ln(5);
                $this->setX($posx);
                $this->Cell(155, 3, utf8_decode("Firma Electrónica:"), 0, 0, 'L');
                $this->SetFont('Arial', '', 6);
                $this->Ln(3);
                $this->setX($posx);
                $this->MultiCell(135, 2.4, $this->datosComprobante['signaturevalue'], 0, 'J');
                $this->setX($posx, $posy);
                $this->SetFont('Arial', 'B', 8);
                $this->Ln(5);
                $this->setXY($posx + 145, $posy);
                $this->Cell(15, 5, ($this->datosComprobante['cdo_tipo'] == 'FC') ? "CUFE:" : "CUDE:", 0, 0, 'J');
                $this->Ln(4);
                $this->setX($posx + 145);
                $this->SetFont('Arial', '', 6);
                $this->MultiCell(50, 2.4, utf8_decode($this->datosComprobante['cufe']), 0, 'L');

                $dataURI = "data:image/png;base64, " . base64_encode((string) \QrCode::format('png')->size(85)->margin(0)->generate($this->datosComprobante['qr']));
                $pic = $this->getImage($dataURI);
                if ($pic !== false) $this->Image($pic[0], $posx + 153, $posy + 13, 29, 29, $pic[1]);
            }

            $posy = 268;
            $this->SetFont('Arial', 'B', 6);
            $this->setXY($posx, $posy);
            $this->Cell(190, 4, utf8_decode('pag. ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
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

    function SetDash($black=false, $white=false) {
        if($black and $white)
            $s=sprintf('[%.3f %.3f] 0 d', $black*$this->k, $white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }
}