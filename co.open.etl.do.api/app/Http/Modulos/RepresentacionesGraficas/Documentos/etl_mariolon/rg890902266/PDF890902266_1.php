<?php
/**
 * Created by PhpStorm.
 * User: Juan Jose Trujillo
 * Date: 13/08/19
 * Time: 06:30 PM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_mariolon\rg890902266;

use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;

class PDF890902266_1 extends PDFBase
{

    public $headers = [
        ['w' => 10, 'title' => 'ITEM'],
        ['w' => 20, 'title' => 'COD. REF'],
        ['w' => 56, 'title' => 'DETALLE'],
        ['w' => 20, 'title' => 'UNIDAD'],
        ['w' => 15, 'title' => 'CANT'],
        ['w' => 24, 'title' => 'VALOR UNITARIO'],
        ['w' => 11, 'title' => '% IVA'],
        ['w' => 13, 'title' => 'IVA'],
        ['w' => 17, 'title' => 'VALOR USD'],
        ['w' => 19, 'title' => 'VALOR COP'],
    ];

    private  $posYIniLines;

    function Header()
    {

        if ($this->datosComprobante['signaturevalue'] == '' && $this->datosComprobante['qr'] == '') {
            $this->Image($this->datosComprobante['no_valido'], 10, 50, 180, 180);
        }

        $posx = 5;
        $posy = 10;

        $this->posx = $posx;
        $this->posy = $posy;

        $titulo = '';
        $rGrafica = '';
        switch ($this->datosComprobante['cdo_tipo']) {
            case 'FC':
                $titulo = 'FACTURA ELECTRÓNICA DE VENTA No. ' . $this->datosComprobante['numero_documento'];
                $rGrafica = 'REPRESENTACIÓN GRÁFICA DE LA FACTURA ELECTRÓNICA';
                break;
            case 'NC':
                $titulo = 'NOTA CRÉDITO No. ' . $this->datosComprobante['numero_documento'];
                $rGrafica = 'REPRESENTACIÓN GRÁFICA DE NOTA CRÉDITO';
                break;
            case 'ND':
                $titulo = 'NOTA DÉBITO No. ' . $this->datosComprobante['numero_documento'];
                $rGrafica = 'REPRESENTACIÓN GRÁFICA DE NOTA DÉBITO';
                break;
        }

        //Logo
        $this->Image($this->imageHeader, $posx, $posy - 5, 30);

        $this->SetFont('Arial','',6);
        $this->TextWithDirection(211,70,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): ".$this->datosComprobante['razon_social_pt']." NIT: ".$this->datosComprobante['nit_pt']." NOMBRE DEL SOFTWARE: ".$this->datosComprobante['nombre_software']),'D');

        $this->SetFont('Arial', 'B', 10);
        $this->setXY($posx + 3, $posy);
        $this->Cell(200, 4, utf8_decode($this->datosComprobante['oferente']), 0, 0, 'C');
        $this->Ln(5);
        $this->setX($posx + 3);
        $this->Cell(200, 4, "NIT. " . $this->datosComprobante['ofe_nit'], 0, 0, 'C');
        $posy = $this->GetY() + 5;
        $this->setXY($posx + 32, $posy);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(50, 4, utf8_decode($rGrafica), 0, 0, 'L');
        $this->Ln(4);
        $this->setX($posx + 32);
        if ($this->datosComprobante['cdo_tipo'] === 'FC') {
            $this->Cell(50, 4, "CUFE:", 0, 0, 'L');
        }else{
            $this->Cell(50, 4, "CUDE:", 0, 0, 'L');
        }
        $this->Ln(4);
        $this->setX($posx + 32);
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(65, 3, $this->datosComprobante['cufe'], 0, 'L');
        $posyfin = $this->GetY();

        $this->setXY($posx + 110, $posy);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(50, 4, "SEDE PPAL:", 0, 0, 'L');
        $this->setX($posx + 126);
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(47, 4, utf8_decode($this->datosComprobante['sede_principal']), 0, 'L');
        $this->Ln(0.5);
        $this->setX($posx + 110);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(50, 4, "CIUDAD QUE GENERA EL INGRESO:", 0, 0, 'L');
        $this->setX($posx + 155);
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(50, 4, utf8_decode($this->datosComprobante['ciudad_ingreso']), 0, 'L');

        if ($this->datosComprobante['qr'] != "") {
            $dataURI = "data:image/png;base64, " . base64_encode((string) \QrCode::format('png')->size(85)->margin(0)->generate($this->datosComprobante['qr']));
            $pic = $this->getImage($dataURI);
            if ($pic !== false) $this->Image($pic[0], $posx + 173, $posy - 15, 0, 0, $pic[1]);
        }

        if($posyfin < $this->GetY()){
            $posyfin = $this->GetY() + 1;
        }

        $posy = $posyfin;
        $this->setXY($posx, $posy);
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(205, 7, utf8_decode($titulo), 0, 0, 'C', TRUE);

        $posyfin = $this->GetY() + 7;
        if ($this->PageNo() == 1) {
            $fontSizeHeaderSumarize = 7;
            $fontSizeHeader = 8;

            // Columna 1
            $posy = $this->GetY() + 10;
            $posyIni = $posy;

            $this->setXY($posx, $posy);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, utf8_decode("SEÑOR(ES):"), 0, 0, 'L');
            $this->Ln(0.5);
            $this->setX($posx + $this->getAnchoTexto("SEÑOR(ES):"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->MultiCell(70, 3, utf8_decode($this->datosComprobante['adquirente']), 0, 'L');
            $this->Ln(1);

            $this->setX($posx);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, "NIT/CC:", 0, 0, 'L');
            $this->setX($posx + $this->getAnchoTexto("NIT/CC:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->Cell(70, 4, $this->datosComprobante['adq_nit'], 0, 0, 'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(21, 4, utf8_decode("DIRECCIÓN:"), 0, 0, 'L');
            $this->Ln(0.5);
            $this->setX($posx + $this->getAnchoTexto("DIRECCIÓN"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->MultiCell(80, 3, utf8_decode($this->datosComprobante['adq_dir']), 0, 'L');
            $this->Ln(0.5);

            $this->setX($posx);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, utf8_decode("CIUDAD Y DEPARTAMENTO:"), 0, 0, 'L');
            $this->setX($posx + $this->getAnchoTexto("CIUDAD Y DEPARTAMENTO:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->MultiCell(57, 4, utf8_decode($this->datosComprobante['adq_mun'] . " - " . $this->datosComprobante['adq_dep']), 0, 'L');

            if ($this->datosComprobante['cdo_tipo'] == 'FC') {
                $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
                $this->setX($posx);
                $this->Cell(40, 4, utf8_decode("FORMA DE PAGO:"), 0, 0, 'L');
                $this->SetFont('Arial', '', $fontSizeHeader);
                $this->setX($posx + $this->getAnchoTexto("FORMA DE PAGO:"));
                $this->Cell(40, 4, utf8_decode($this->datosComprobante['forma_pago']), 0, 0, 'L');

                $this->Ln(4);
                $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
                $this->setX($posx);
                $this->Cell(40, 4, utf8_decode("MEDIO DE PAGO:"), 0, 0, 'L');
                $this->SetFont('Arial', '', $fontSizeHeader);
                $this->setX($posx + $this->getAnchoTexto("MEDIO DE PAGO:"));
                $this->Cell(40, 4, utf8_decode($this->datosComprobante['medio_pago']), 0, 0, 'L');
                $this->Ln(4);
            }
            $posyfin = $this->GetY();

            // Columna 2
            $offsetX = 88;
            $this->setXY($posx + $offsetX, $posy);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, utf8_decode("TELÉFONO:"), 0, 0, 'L');
            $this->setX($posx + $offsetX + $this->getAnchoTexto("TELÉFONO:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->Cell(70, 4, $this->datosComprobante['adq_tel'], 0, 0, 'L');
            $this->Ln(4);

            $this->setX($posx + $offsetX);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, utf8_decode("FECHA Y HORA DE GENERACIÓN:"), 0, 0, 'L');
            $this->setX($posx + $offsetX + $this->getAnchoTexto("FECHA Y HORA DE GENERACIÓN:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->Cell(70, 4, $this->datosComprobante['fecha_hora_documento'], 0, 0, 'L');
            $this->Ln(4);

            $this->setX($posx + $offsetX);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, utf8_decode("FECHA Y HORA DE VALIDACION DIAN:"), 0, 0, 'L');
            $this->setX($posx + $offsetX + $this->getAnchoTexto("FECHA Y HORA DE VALIDACION DIAN:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->Cell(70, 4, substr($this->datosComprobante['fecha_validacion'],0,16), 0, 0, 'L');
            $this->Ln(4);

            $this->setX($posx + $offsetX);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, "FECHA VENCIMIENTO:", 0, 0, 'L');
            $this->setX($posx + $offsetX + $this->getAnchoTexto("FECHA VENCIMIENTO:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->Cell(70, 4, $this->datosComprobante['fecha_vencimiento'], 0, 0, 'L');
            $this->Ln(4);
            if ($posyfin < $this->GetY())
                $posyfin = $this->GetY();

            if ($this->datosComprobante['cdo_tipo'] === 'FC') {

                $this->setX($posx + $offsetX);
                $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
                $this->Cell(20, 4, "COMERCIAL:", 0, 0, 'L');
                $this->Ln(0.5);
                $this->setX($posx + $offsetX + $this->getAnchoTexto("COMERCIAL:"));
                $this->SetFont('Arial', '', $fontSizeHeader);
                $this->Cell(70, 3, utf8_decode($this->datosComprobante['comercial']), 0, 0, 'L');
                $this->Ln(4);
                if ($posyfin < $this->GetY())
                    $posyfin = $this->GetY();
            }

            // Columna 3
            $offsetX = 165;

            $this->setXY($posx + $offsetX, $posy);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, "DO:", 0, 0, 'L');
            $this->setX($posx + $offsetX + $this->getAnchoTexto("DO:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->Cell(70, 4, $this->datosComprobante['do'], 0, 0, 'L');
            $this->Ln(4);

            if ($this->datosComprobante['cdo_tipo'] === 'FC') {
                $this->setX($posx + $offsetX);
                $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
                $this->Cell(20, 4, "TRM:", 0, 0, 'L');
                $this->setX($posx + $offsetX + $this->getAnchoTexto("TRM:"));
                $this->SetFont('Arial', '', $fontSizeHeader);
                $this->Cell(70, 4, $this->datosComprobante['trm'], 0, 0, 'L');
                $this->Ln(4);
            }

            $this->setX($posx + $offsetX);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, "PEDIDO:", 0, 0, 'L');
            $this->setX($posx + $offsetX + $this->getAnchoTexto("PEDIDO:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->Cell(70, 4, $this->datosComprobante['pedido'], 0, 0, 'L');
            $this->Ln(4.5);

            $this->setX($posx + $offsetX);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, "OC:", 0, 0, 'L');
            $this->setX($posx + $offsetX + $this->getAnchoTexto("OC:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->Cell(50, 4, utf8_decode($this->datosComprobante['oc']), 0, 0, 'L');
            $this->Ln(4);

            $this->setX($posx + $offsetX);
            $this->SetFont('Arial', 'B', $fontSizeHeaderSumarize);
            $this->Cell(20, 4, "PLAZO:", 0, 0, 'L');
            $this->setX($posx + $offsetX + $this->getAnchoTexto("PLAZO:"));
            $this->SetFont('Arial', '', $fontSizeHeader);
            $this->Cell(50, 4, utf8_decode($this->datosComprobante['plazo']), 0, 0, 'L');
            $this->Ln(4);
            if ($posyfin < $this->GetY()) {
                $posyfin = $this->GetY();
            }

            if ($this->datosComprobante['cdo_tipo'] !== 'FC') {
                $documentoReferencia = 'FACTURA ELECTRÓNICA DE VENTA: ';
                $fechaReferencia     = 'FECHA GENERACIÓN FACTURA ELECTRÓNICA DE VENTA: ';
                $cufeDocumento       = 'CUFE FACTURA ELECTRÓNICA DE VENTA: ';
                switch ($this->datosComprobante['clasificacion']) {
                    case 'FC':
                        $documentoReferencia = 'FACTURA ELECTRÓNICA DE VENTA: ';
                        $fechaReferencia     = 'FECHA GENERACIÓN FACTURA ELECTRÓNICA DE VENTA: ';
                        $cufeDocumento       = 'CUFE FACTURA ELECTRÓNICA DE VENTA: ';
                        break;
                    case 'NC':
                        $documentoReferencia = 'NOTA CRÉDITO ELECTRÓNICA: ';
                        $fechaReferencia     = 'FECHA GENERACIÓN NOTA CRÉDITO ELECTRÓNICA: ';
                        $cufeDocumento       = 'CUFE NOTA CRÉDITO ELECTRÓNICA: ';
                        break;
                    case 'ND':
                        $documentoReferencia = 'NOTA DÉBITO ELECTRÓNICA: ';
                        $fechaReferencia     = 'FECHA GENERACIÓN NOTA DÉBITO ELECTRÓNICA: ';
                        $cufeDocumento       = 'CUFE NOTA DÉBITO ELECTRÓNICA: ';
                        break;
                }

                $this->Line(5, $posyfin, 210, $posyfin);
                $fontSizeHeaderReference = 7;
                $this->setXY($posx, $posyfin + 1);
                $this->SetFont('Arial', 'B', $fontSizeHeaderReference);
                $this->Cell(20, 4, utf8_decode($documentoReferencia), 0, 0, 'L');
                $this->setX($posx + $this->getAnchoTexto($documentoReferencia));
                $this->SetFont('Arial', '', $fontSizeHeader);
                $this->Cell(70, 4, $this->datosComprobante['numero_documento_ref'], 0, 'L');
                $this->Ln(4);

                $this->setX($posx);
                $this->SetFont('Arial', 'B', $fontSizeHeaderReference);
                $this->Cell(20, 4, utf8_decode($fechaReferencia), 0, 0, 'L');
                $this->setX($posx + $this->getAnchoTexto($fechaReferencia));
                $this->SetFont('Arial', '', $fontSizeHeader);
                $this->Cell(70, 4, $this->datosComprobante['fecha_documento_ref'], 0, 'L');
                $this->Ln(4);

                $this->setX($posx);
                $this->SetFont('Arial', 'B', $fontSizeHeaderReference);
                $this->Cell(20, 4, utf8_decode($cufeDocumento), 0, 0, 'L');
                $this->setX($posx + $this->getAnchoTexto($cufeDocumento));
                $this->SetFont('Arial', '', $fontSizeHeaderReference);
                $this->Cell(70, 4, $this->datosComprobante['cufe_documento_ref'], 0, 'L');
                $this->Ln(4);

                $posyfin = $this->GetY();
            }
            $this->Rect($posx, $posy - 1, 205, $posyfin - ($posyIni-2));
        }

        $posy = $posyfin+3;
        $this->setXY($posx, $posy);
        $this->SetFillColor(220, 220, 220);
        $this->SetFont('Arial', 'B', 7.5);
        foreach ($this->headers as $head)
            $this->Cell($head['w'], 5, $head['title'], 0, 0, 'C', TRUE);

        $this->Rect($posx, $posy, 205, 5);
        $this->posYIniLines = $posy;
        $this->nPosYDet = $posy + 7;
    }

    /**
     * Imprime las lineas de seperaración en los items
     *
     * @param $posyfin
     * @param bool $horizontal_line
     */
    public function printLines($posyfin, $horizontal_line = true)
    {
        $posy = $horizontal_line ?  $posyfin : 200;
        $posx = 5;

        $N = count($this->headers);
        $acum = 0;
        for ($i = 0; $i < $N - 1; $i++) {
            $acum += $this->headers[$i]['w'];
            $this->Line($posx + $acum, $this->posYIniLines, $posx + $acum, $posy);
        }

        if ($horizontal_line)
            $this->Rect($posx, $this->posYIniLines, $posx + 200, $posyfin-$this->posYIniLines);

        $this->nPosYDet = $posy + 7;
    }

    function Footer()
    {
        $posx = $this->posx;

        //Defino posicion inicial Y para pintar la firma
        $posy = 255;

        if ($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != "") {

            $this->setXY($posx, $posy + 1);
            $this->SetFont('Arial', '', 8);
            $this->Cell(130, 3, utf8_decode("Firma Electrónica:"), 0, 0, 'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->SetFont('Arial', '', 6.5);
            $this->MultiCell(135, 3, $this->datosComprobante['signaturevalue'], 0, 'J');
        }

        $this->setXY($posx + 137, $posy + 4);
        $this->SetFont('Arial', '', 8.5);
        $this->MultiCell(70, 3, utf8_decode($this->datosComprobante['elaboro']), 0, 'C');

        $this->setXY($posx + 137, $posy + 13);
        $this->SetFont('Arial', 'B', 8);
        $this->MultiCell(70, 3, utf8_decode("ELABORÓ"), 0, 'C');

        $this->Rect($posx, $posy, 205, 17);
        $this->Line($posx + 136, $posy, $posx + 136, $posy + 17);
        $this->Line($posx + 136, $posy + 11, $posx + 205, $posy + 11);

        //Paginacion
        $this->setXY($posx, $posy + 17);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(205, 4, utf8_decode('Pág. ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
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