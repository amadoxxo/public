<?php
namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_generica\rgDsGenerica;

use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;

/**
 * Gestor para la generación de representaciones graficas genericas de documentos soporte.
 *
 * Class PDFGENERICA_1
 * @package App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_generica\rgDsGenerica
 */
class PDFGENERICA_1 extends PDFBase
{
    /**
     * Retorna la fila correspodiente a la identificación del documento.
     * 
     * @return string
     */
    private function getDocumento() {
        $documento = '';
        if ($this->datosComprobante['cdo_tipo'] === "DS")
            $documento = 'DOCUMENTO SOPORTE EN ADQUISICIONES EFECTUADAS A NO OBLIGADOS A FACTURAR No. ';
        else
            $documento = 'NOTA DE AJUSTE DEL DOCUMENTO SOPORTE EN ADQUISICIONES EFECTUADAS A NO OBLIGADOS A EXPEDIR FACTURA O DOCUMENTO EQUIVALENTE No. ';

        return $documento . $this->datosComprobante['numero_documento'];
    }

    /**
     * Imprime la cabecera de la RG.
     */
    function Header() {
        if (empty($this->datosComprobante['signaturevalue']) && empty($this->datosComprobante['qr']))
            $this->Image($this->datosComprobante['no_valido'], 10, 50, 180, 180);

        $posx = 10;
        $posy = 5;
        $this->posx = $posx;
        $this->posy = $posy;

        if(is_file($this->imageHeader)) {
            $dimension = $this->calcularProporcionLogo($this->imageHeader);
            $this->Image($this->imageHeader, $posx + 2, $posy + $dimension['posy'], $dimension['tamano']);
        }
        $this->SetFont('Arial','',6);
        $this->TextWithDirection(208,70,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): ".$this->datosComprobante['razon_social_pt']." NIT: ".$this->datosComprobante['nit_pt']." NOMBRE DEL SOFTWARE: ".$this->datosComprobante['nombre_software']),'D');

        // Datos del documento soporte
        $this->SetFont("Helvetica", '', 7);
        $this->setXY($posx, $posy + 30);
        $this->Cell(66, 4, utf8_decode("Fecha y hora: " . date("Y-m-d H:i:s", strtotime($this->datosComprobante['fecha_hora_documento']))), 0, 0, 'L');
        $this->setXY($posx, $posy + 33);
        $this->Cell(66, 4, utf8_decode("Fecha de vencimiento: " . date("Y-m-d", strtotime($this->datosComprobante['fecha_vencimiento']))), 0, 0, 'L');
        $this->setXY($posx, $posy + 39);
        $this->Cell(66, 4, utf8_decode("Forma de pago: " . strtoupper($this->datosComprobante['forma_pago'])), 0, 'L');
        $this->setXY($posx, $posy + 42);
        $this->Cell(66, 4, utf8_decode("Medio de pago: " . strtoupper($this->datosComprobante['medio_pago'])), 0, 'L');

        //  Datos del adquirente
        $this->setXY($posx, $posy + 48);
        $this->SetFont("Helvetica", 'b', 7);
        $this->Cell(66, 4, utf8_decode("Vendedor"), 0, 0, 'L');
        $this->SetFont("Helvetica", '', 7);
        $this->setXY($posx, $posy + 52);
        $this->MultiCell(120, 4, utf8_decode(strtoupper($this->datosComprobante['adquirente'])), 0, 'L');
        $this->setXY($posx, $posy + 56);
        $this->MultiCell(66, 4, utf8_decode($this->datosComprobante['tipo_documento'] . ': ' . $this->datosComprobante['adq_nit']), 0, 'L');
        $this->setXY($posx, $posy + 60);
        $this->Cell(66, 4, utf8_decode("Tel: " . $this->datosComprobante['adq_tel']), 0, 0, 'L');
        $this->setXY($posx, $posy + 64);
        $this->Cell(66, 4, utf8_decode("Dir: " . strtoupper($this->datosComprobante['adq_dir'])), 0, 0, 'L');
        $this->setXY($posx, $posy + 68);
        $this->Cell(108, 4, utf8_decode(strtoupper($this->datosComprobante['adq_mun'])), 0, 0, 'L');
        $this->setXY($posx, $posy + 72);
        $this->Cell(108, 4, utf8_decode(strtoupper($this->datosComprobante['adq_pai'])), 0, 0, 'L');

        // Datos del documento soporte
        // Ubicamos el cursor a la deracha de "Fecha del documento soporte:"
        $this->SetFont("Helvetica", 'b', 7);
        $this->setXY((($this->datosComprobante['cdo_tipo'] == "DS_NC") ? $posx + 73 : $posx + 120) , $posy + 30);
        $this->MultiCell((($this->datosComprobante['cdo_tipo'] == "DS_NC") ? 122 : 75), 4, utf8_decode($this->getDocumento()), 0, 'R');
        $this->Ln(1);
        $this->SetFont("Helvetica", '', 7);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode(strtoupper($this->datosComprobante['oferente'])), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode('N.I.T Nº: ' . strtoupper($this->datosComprobante['ofe_nit'])), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode($this->datosComprobante['ofe_regimen']), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode('Dir.: ' . strtoupper($this->datosComprobante['ofe_dir'])), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode('Tel.: ' . strtoupper($this->datosComprobante['ofe_tel'])), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode(strtoupper($this->datosComprobante['ofe_mun'])), 0, 0, 'R');
        $this->Ln(4);

        // Sección para imprimir la data de resolución de facturación
        if ($this->datosComprobante['cdo_tipo'] === 'DS') {
            $this->setX($posx + 66);
            $this->Cell(129, 4, utf8_decode("Resolución DIAN {$this->datosComprobante['ofe_resolucion']}"), 0, 0, 'R');
            $this->Ln(4);
            $this->setX($posx + 66);
            $this->Cell(129, 4, utf8_decode("Fecha de Expedición {$this->datosComprobante['ofe_resolucion_fecha']}"), 0, 0, 'R');
            $this->Ln(4);
            $this->setX($posx + 66);
            $this->Cell(129, 4, utf8_decode("Vigencia de Resolución {$this->datosComprobante['ofe_resolucion_vigencia']} meses"), 0, 0, 'R');
            $this->Ln(4);
            $this->setX($posx + 66);
            $this->Cell(129, 4, utf8_decode("Numeración Autorizada desde el No. {$this->datosComprobante['ofe_resolucion_prefijo']}{$this->datosComprobante['ofe_resolucion_desde']} hasta el No. {$this->datosComprobante['ofe_resolucion_prefijo']}{$this->datosComprobante['ofe_resolucion_hasta']}"), 0, 0, 'R');
        }
        $this->Ln(5);

        // Valor personalizado encabezado
        $posyIni = $this->GetY();
        $this->setXY($posx + 66, $posyIni);
        $this->MultiCell(129, 3.5, utf8_decode($this->datosComprobante['valor_personalizado_cabecera']), 0, 'R');
        $posyFin = $this->GetY() + 2;

       // Valores Personalizadas
        $posy = $posyFin;
        if(array_key_exists('valores_personalizados_ds', $this->datosComprobante) && !empty($this->datosComprobante['valores_personalizados_ds'])) {
            $contColumna = 1;
            $numFilas    = ceil(count($this->datosComprobante['valores_personalizados_ds'])/4);
            foreach($this->datosComprobante['valores_personalizados_ds'] as $key => $item) {
                if($contColumna > 4) {
                    $contColumna = 1;
                    $posy += 8;
                }
                $this->SetFont("Helvetica", 'b', 7);
                $this->setXY($posx + (($contColumna - 1) * 50), $posy);
                $this->Cell(129, 4, utf8_decode($item['nombre_campo']), 0, 0, 'L');
                $this->SetFont('Helvetica', '', 7);
                $this->setXY($posx + (($contColumna - 1) * 50), $posy + 3);
                $this->Cell(129, 4, utf8_decode($item['valor_campo']), 0, 0, 'L');
                $contColumna++;
            }
        }

        // Documento Referencia, aplica para DS_NC
        $mover = false;
        if ($this->datosComprobante['cdo_tipo'] != 'DS') {
            $this->SetFillColor(240, 240, 240);
            $this->SetTextColor(0, 0, 0);
            $this->setXY($posx, $posy + 14);
            $this->SetFont('Helvetica', 'B', 7);
            $this->Cell(40, 6, utf8_decode("Documento Referencia"), "B", 0, 'L', true);
            $this->Cell(40, 6, utf8_decode("Fecha Documento Referencia"), "B", 0, 'L', true);
            $this->Cell(115, 6, utf8_decode("CUFE"), "B", 0, 'L', true);
            $posx = 10;
            $this->setXY($posx, $posy + 21);
            $this->nPosYDet = $posy + 21;

            $this->SetFillColor(255, 255, 255);
            $this->SetFont('Helvetica', '', 7);
            $this->Cell(40, 6, utf8_decode($this->datosComprobante['consecutivo_ref']), "", 0, 'L', true);
            $this->Cell(40, 6, utf8_decode($this->datosComprobante['fecha_emision_ref']), "", 0, 'L', true);
            $this->MultiCell(115, 3, $this->datosComprobante['cufe_ref'], 0, 'J');
            $posx = 10;
            $this->setXY($posx, $posy + 21);
            $this->nPosYDet = $posy + 21;

            $mover = true;
        }

        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->setXY($posx, $posy + ($mover ? 29 : 10));
        $this->SetFont('Helvetica', 'B', 7);

        if (!$this->datosComprobante['aplica_dos_monedas']) {
            $this->Cell(10, 6, utf8_decode("Ítem"), "B", 0, 'C', true);
            $this->Cell(15, 6, utf8_decode("Código"), "B", 0, 'C', true);
            $this->Cell(98, 6, utf8_decode("Descripción"), "B", 0, 'C', true);
            $this->Cell(12, 6, utf8_decode("Cantidad"), "B", 0, 'C', true);
            $this->Cell(30, 6, utf8_decode("Valor Unitario " . $this->datosComprobante['cdo_moneda']), "B", 0, 'C', true);
            $this->Cell(30, 6, utf8_decode("Valor Total " . $this->datosComprobante['cdo_moneda']), "B", 0, 'C', true);
        } else {
            $this->Cell(10, 6, utf8_decode("Ítem"), "B", 0, 'C', true);
            $this->Cell(13, 6, utf8_decode("Código"), "B", 0, 'C', true);
            $this->Cell(65, 6, utf8_decode("Descripción"), "B", 0, 'C', true);
            $this->Cell(11, 6, utf8_decode("Cantidad"), "B", 0, 'C', true);
            $this->Cell(24, 6, utf8_decode("Vlr. Unitario " . $this->datosComprobante['cdo_moneda_extranjera']), "B", 0, 'R', true);
            $this->Cell(22, 6, utf8_decode("Vlr. Total " . $this->datosComprobante['cdo_moneda_extranjera']), "B", 0, 'R', true);
            $this->Cell(25, 6, utf8_decode("Vlr. Unitario " . $this->datosComprobante['cdo_moneda']), "B", 0, 'R', true);
            $this->Cell(25, 6, utf8_decode("Vlr. Total " . $this->datosComprobante['cdo_moneda']), "B", 0, 'R', true);
        }

        $this->SetFont('Helvetica', '', 7);
        $posx = 10;
        $this->setXY($posx, $posy + ($mover ? 36 : 17));
        $this->nPosYDet = $posy + ($mover ? 36 : 17);
    }

    function Footer() {
        // Datos QR y Firma
        $posx = $this->posx;
        $posy = (224 - $this->datosComprobante['posy_fin_footer']);

        // Valor personalizado pie de pagina
        $this->setXY($posx, $posy + 5);
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(195, 3, utf8_decode($this->datosComprobante['valor_personalizado_pie']), 0, 'C');

        $posy+=$this->datosComprobante['posy_fin_footer'];
        if ($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != "") {

            $this->setXY($posx + 155, $posy + 10);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(70, 3, ($this->datosComprobante['cdo_tipo'] === "DS") ? 'CUFE' : 'CUDE', 0, 0, 'L');
            $this->Ln(3);
            $this->setX($posx + 155);
            $this->SetFont('Arial', '', 6);
            $this->MultiCell(40, 3, utf8_decode($this->datosComprobante['cufe']), 0, 'L');

            $dataURI = "data:image/png;base64, " . base64_encode((string) \QrCode::format('png')->size(85)->margin(0)->generate($this->datosComprobante['qr']));
            $pic = $this->getImage($dataURI);
            if ($pic !== false) $this->Image($pic[0], $posx + 162, $posy + 24, 0, 0, $pic[1]);

            $this->SetFont('Arial', '', 7);
            $this->setXY($posx, $posy + 13);
            $this->Cell(130, 4, utf8_decode("Representación gráfica de documento soporte electrónico:"), 0, 0, 'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->SetFont('Arial', '', 7);
            $this->Cell(130, 3, utf8_decode("Firma Electrónica:"), 0, 0, 'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->SetFont('Arial', '', 6);
            $this->MultiCell(130, 3, $this->datosComprobante['signaturevalue'], 0, 'J');
        }

        $this->setXY($posx,$posy+10);
        $this->SetFont('Arial','',7);
        $this->Cell(100,4,utf8_decode("Fecha y hora de validación dian: ").$this->datosComprobante['validacion_dian'],0,0,'L');

        //Paginación
        $this->setXY($posx, $posy + 41);
        $this->SetFont('Arial', '', 7);
        $this->Cell(190, 4, utf8_decode('Pág. ') . $this->PageNo() . '/{nb}', 0, 0, 'C');

    }

    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '') {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));

        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        if (strpos($corners, '2') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $y) * $k));
        else
            $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);

        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '3') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        if (strpos($corners, '4') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);

        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '1') === false) {
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $y) * $k));
            $this->_out(sprintf('%.2F %.2F l', ($x + $r) * $k, ($hp - $y) * $k));
        } else
            $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
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
    
    /**
     * Dimensiona el logo para ajustarlo en la cabecera de la Representación Gráfica.
     *
     * @param  mixed $imagen File de la imagen del logo
     * @return array
     */
    function calcularProporcionLogo($logo) {
        $arrInfoLogo = getimagesize($logo);
        // Se hace la siguiente operación para calcular el porcentaje de la altura y anchura
        // respecto al máximo de altura (150) y máximo de anchura (200).
        $anchoPorcentaje = (intval($arrInfoLogo[0])/200) * 100;
        $altoPorcentaje = (intval($arrInfoLogo[1])/150) * 100;

        // El porcentaje de la anchura se multiplica por 40 que es el limite de proporción que
        // puede tomar en sus maximas medidas (200x150) para que no se remonte en el texto debajo.
        $proporcion = ($anchoPorcentaje * 40) / 100;

        // El porcentaje de la altura se resta al 100% de alto y se multiplica por 15 que es lo máximo
        // que se puede adicionar en la posY y no se remonte sobre el texto.
        $adicionalY = ((100 - $altoPorcentaje) * 15) / 100;

        $dimensiones = [
            'posy' => $adicionalY,
            'tamano' => $proporcion
        ];

        return $dimensiones;
    }
}
