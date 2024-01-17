<?php

/**
 * User: Jhon Escobar
 * Date: 23/09/20
 * Time: 12:04 PM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_maritasx\rg901034522;

use Illuminate\Support\Facades\Storage;
use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;

/**
 * Gestor para la generación de representaciones graficas
 *
 * @package  App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_maritasx\rg901034522
 */
class PDF901034522_1 extends PDFBase
{

    /**
     * Retorna la fila correspodiente a la identificación del documento
     * @return string
     */
    private function getDocumento()
    {
        $documento = '';
        if ($this->datosComprobante['cdo_tipo'] === "NC")
            $documento = 'NOTA CRÉDITO ELECTRONICA. ';
        elseif ($this->datosComprobante['cdo_tipo'] === "NC") {
            $documento = 'FACTURA ELECTRÓNICA DE VENTA';
        };
        return $documento;
    }

    /**
     * Imprime la cabecera de la RG
     */
    function Header()
    {

        if (empty($this->datosComprobante['signaturevalue']) && empty($this->datosComprobante['qr']))
            $this->Image($this->datosComprobante['no_valido'], 10, 50, 180, 180);

        $posx = 10;
        $posy = 5;
        $this->posx = $posx;
        $this->posy = $posy;

        $this->SetFont("Helvetica", '', 7);
        $this->setXY($posx, $posy);
        $this->Cell(195, 4, utf8_decode($this->datosComprobante['oferente']), 0, 0, 'C');
        $this->setXY($posx, $posy + 4);
        $this->Cell(195, 4, utf8_decode('Nit: ' . $this->datosComprobante['ofe_nit']), 0, 0, 'C');

        $this->setXY($posx + 50, $posy + 50);
        $this->SetFont("Helvetica", '', 20);

        $this->SetFont('Arial', '', 6);
        $this->TextWithDirection(206, 70, utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): " . $this->datosComprobante['razon_social_pt'] . " NIT: " . $this->datosComprobante['nit_pt'] . " NOMBRE DEL SOFTWARE: " . $this->datosComprobante['nombre_software']), 'D');

        /* Tipo de documento Factura de Venta o Nota de Crédito */
        if ($this->datosComprobante['cdo_tipo'] === "FC") {
            $this->setXY($posx, $posy + 8);
            $this->Cell(195, 4, utf8_decode($this->datosComprobante['ofe_dir']), 0, 0, 'C');
            $this->setXY($posx, $posy + 12);
            $this->Cell(195, 4, utf8_decode($this->datosComprobante['ofe_mun'] . ' - ' . $this->datosComprobante['ofe_pais']), 0, 0, 'C');
            $this->setXY($posx, $posy + 16);
            $this->Cell(195, 4, utf8_decode('Teléfono: ' . $this->datosComprobante['ofe_tel'] . ' - ' . $this->datosComprobante['ofe_correo']), 0, 0, 'C');
            $this->setXY($posx, $posy + 20);
            $this->Cell(195, 4, utf8_decode($this->datosComprobante['actividad_economica']), 0, 0, 'C');
            $this->setXY($posx + 67.5, $posy + 24);
            $this->MultiCell(60, 4, utf8_decode($this->datosComprobante['resolucion']), 0, 'C');
            $this->setXY($posx, $posy + 32);
            $this->Cell(195, 4, utf8_decode($this->datosComprobante['regimen_comun']), 0, 0, 'C');
            $this->setXY($posx, $posy + 46);
            $this->Cell(195, 4, utf8_decode($this->datosComprobante['somos']), 0, 0, 'C');

            /*  Datos del adquirente */
            // Cuadro de datos
            $this->Rect($posx, $posy + 41, 195, 31);
            $this->Rect($posx + 93, $posy + 41, 102, 31);
            //Labels
            $this->SetFont("Helvetica", '', 9);
            $this->setXY($posx, $posy + 43);
            $this->Cell(20, 4, utf8_decode('Senor(es):'), 0, 0, 'L');

            $this->SetFont("Helvetica", '', 9);
            // Cliente
            $this->setXY($posx + 20, $posy + 43);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adquirente']), 0, 0, 'L');
            // Nit
            $this->setXY($posx + 20, $posy + 48);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adq_nit']), 0, 0, 'L');
            // Direccion
            $this->setXY($posx + 20, $posy + 53);
            $this->MultiCell(75, 4, utf8_decode($this->datosComprobante['adq_dir']), 0, 'L');
            // Telefono
            $this->setXY($posx + 20, $posy + 62);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adq_tel']), 0, 0, 'L');
            // Email
            $this->setXY($posx + 20, $posy + 66);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adq_correo']), 0, 0, 'L');
            $this->SetFont("Helvetica", 'b', 13);
            $this->setXY($posx + 49, $posy + 43);
            $this->Cell(129, 4, utf8_decode('FACTURA ELECTRONICA DE VENTA'), 0, 0, 'R');

            //Labels

            $this->SetFont("Helvetica", 'b', 9);
            $this->setXY($posx + 95, $posy + 55);
            $this->Cell(20, 4, utf8_decode('FECHA FAC'), 0, 0, 'L');
            $this->setXY($posx + 115, $posy + 55);
            $this->Cell(20, 4, utf8_decode('HORA FAC'), 0, 0, 'L');

            $this->SetFont("Helvetica", '', 9);
            // Fecha
            $this->setXY($posx + 95, $posy + 60);
            $this->Cell(20, 4, utf8_decode(date("Y-m-d", strtotime($this->datosComprobante['fecha_hora_documento']))), 0, 0, 'L');
            // Hora
            $this->setXY($posx + 115, $posy + 60);
            $this->Cell(20, 4, utf8_decode(date("h:i:s", strtotime($this->datosComprobante['fecha_hora_documento']))), 0, 0, 'L');

            // Terminos de negociacion
            $this->Rect($posx + 93, $posy + 66, 51, 6);
            $this->Rect($posx + 144, $posy + 66, 51, 6);
            $this->SetFont("Helvetica", 'b', 8);
            $this->setXY($posx + 93, $posy + 67);
            //$this->Cell(51, 4, utf8_decode($this->datosComprobante['cen_descripcion']), 0, 0, 'C');
            // Cuadro contenedor cabecera tabla
            $this->Rect($posx, $posy + 74, 195, 13);
            // Cuadro dividor labels y monto
            $this->Rect($posx, $posy + 74, 195, 6);
            $this->SetFont("Helvetica", '', 7);
            // Cuadro forma de pago
            $this->Rect($posx, $posy + 74, 27, 13);
            $this->setXY($posx, $posy + 74);
            $this->Cell(27, 4, utf8_decode('Forma de pago'), 0, 0, 'C');
            $this->Cell(50, 32, "what's up danger", 1, 1, 'R' );
            $this->setXY($posx, $posy + 80);
            $this->Cell(27, 4, utf8_decode($this->datosComprobante['forma_de_pago']), 0, 0, 'C');
            // Medio de pago
            $this->Rect($posx + 27, $posy + 74, 27, 13);
            $this->setXY($posx + 27, $posy + 74);
            $this->Cell(27, 4, utf8_decode('Medio de pago'), 0, 0, 'C');
            $this->setXY($posx + 27, $posy + 80);
            $this->Cell(27, 4, utf8_decode($this->datosComprobante['medio_de_pago']), 0, 0, 'C');
            // Fecha de vencimiento
            $this->Rect($posx + 54, $posy + 74, 30, 13);
            $this->setXY($posx + 54, $posy + 74);
            $this->Cell(30, 4, utf8_decode('Fecha de vencimiento'), 0, 0, 'C');
            $this->setXY($posx + 54, $posy + 80);
            $this->Cell(30, 4, utf8_decode(date("Y-m-d", strtotime($this->datosComprobante['fecha_vencimiento']))), 0, 0, 'C');
            // Moneda de negociacion
            $this->Rect($posx + 84, $posy + 74, 30, 13);
            $this->setXY($posx + 84, $posy + 74);
            $this->Cell(30, 4, utf8_decode('Moneda de negociacion'), 0, 0, 'C');
            $this->setXY($posx + 84, $posy + 80);
            $this->Cell(30, 4, utf8_decode($this->datosComprobante['moneda_de_negociacion']), 0, 0, 'C');
            // Vendedor
            $this->Rect($posx + 114, $posy + 74, 27, 13);
            $this->setXY($posx + 114, $posy + 74);
            $this->Cell(27, 4, utf8_decode('Vendedor'), 0, 0, 'C');
            $this->setXY($posx + 114, $posy + 80);
            $this->Cell(27, 4, utf8_decode($this->datosComprobante['vendedor']), 0, 0, 'C');
            // Punto de venta
            $this->Rect($posx + 141, $posy + 74, 27, 13);
            $this->setXY($posx + 141, $posy + 74);
            $this->Cell(27, 4, utf8_decode('Punto de venta'), 0, 0, 'C');
            $this->setXY($posx + 141, $posy + 80);
            $this->Cell(27, 4, utf8_decode($this->datosComprobante['punto_venta']), 0, 0, 'C');
            // No. de pedido
            $this->Rect($posx + 168, $posy + 74, 27, 13);
            $this->setXY($posx + 168, $posy + 74);
            $this->Cell(27, 4, utf8_decode('No. de pedido'), 0, 0, 'C');
            $this->setXY($posx + 168, $posy + 80);
            $this->Cell(27, 4, utf8_decode($this->datosComprobante['numero_pedido']), 0, 0, 'C');

            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);
            $this->setXY($posx, $posy + 87);
            $this->SetFont('Helvetica', '', 7);
            $this->Cell(10, 6, utf8_decode(strtoupper("Item")), "B", 0, 'C', true);
            $this->Line(0,10, 100,100);
            $this->Cell(15, 6, utf8_decode(strtoupper("Codigo")), "B", 0, 'C', true);
            $this->Cell(18, 6, utf8_decode(strtoupper("Cantidad")), "B", 0, 'C', true);
            $this->Rect(18, 6, 20, 50);
            $this->Cell(28, 6, utf8_decode(strtoupper("Descripcion")), "B", 0, 'C', true);
            $this->Cell(30, 6, utf8_decode(strtoupper("Unidad")), "B", 0, 'C', true);
            $this->Cell(30, 6, utf8_decode(strtoupper("Valor Unitario")), "B", 0, 'C', true);
            $this->Cell(100, 6, utf8_decode(strtoupper("Valor")), "B", 0, 'C', true);
            $posx = 10;
            $this->setXY($posx, $posy + 95);
            $this->nPosYDet = $posy + 95;
        } elseif ($this->datosComprobante['cdo_tipo'] === "NC") {
            $this->setXY($posx + 82, $posy + 8);
            $dir = substr($this->datosComprobante['ofe_dir'], 0, 40);
            $this->MultiCell(30, 4, utf8_decode($dir), 0, 'C');
            $this->setXY($posx, $posy + 17);
            $this->Cell(195, 4, utf8_decode($this->datosComprobante['ofe_mun'] . ' - ' . $this->datosComprobante['ofe_pais']), 0, 0, 'C');
            $this->setXY($posx, $posy + 23);
            $this->Cell(195, 4, utf8_decode($this->datosComprobante['regimen_comun']), 0, 0, 'C');
            $this->setXY($posx, $posy + 27);
            $this->Cell(195, 4, utf8_decode($this->datosComprobante['somos']), 0, 0, 'C');

            $this->SetFont('Helvetica', 'B', 13);
            $this->setXY($posx, $posy + 5);
            $this->Cell(195, 4, utf8_decode('NOTA CREDITO ELECTRONICA'), 0, 0, 'R');
            $this->SetFont("Helvetica", '', 8);
            $this->setXY($posx + 165, $posy + 12);
            $this->Cell(30, 4, utf8_decode('N°: ' . $this->datosComprobante['cdo_consecutivo']), 0, 0, 'L');
            // Fecha
            $this->setXY($posx + 165, $posy + 16);
            $this->Cell(30, 4, utf8_decode('Fecha: ' . date("Y-m-d", strtotime($this->datosComprobante['fecha_hora_documento']))), 0, 0, 'L');
            // Hora
            $this->setXY($posx + 165, $posy + 20);
            $this->Cell(30, 4, utf8_decode('Hora: ' . date("h:i:s", strtotime($this->datosComprobante['fecha_hora_documento']))), 0, 0, 'L');

            $this->SetFont("Helvetica", '', 8);
            // Cuadro contenedor
            $this->Rect($posx, $posy + 37, 195, 25);
            // Primer cuadro
            $this->Rect($posx, $posy + 37, 65, 25);
            $this->setXY($posx + 2, $posy + 39);
            $this->Cell(20, 4, utf8_decode('Cliente:'), 0, 0, 'L');
            $this->setXY($posx + 20, $posy + 39);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adquirente']), 0, 0, 'L');
            $this->setXY($posx + 2, $posy + 46);
            $this->Cell(20, 4, utf8_decode('Nit / CC:'), 0, 0, 'L');
            $this->setXY($posx + 20, $posy + 46);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adq_nit']), 0, 0, 'L');
            // Segundo cuadro
            $this->Rect($posx + 65, $posy + 37, 65, 25);
            $this->setXY($posx + 67, $posy + 39);
            $this->Cell(20, 4, utf8_decode('Dirección:'), 0, 0, 'L');
            $dir = substr($this->datosComprobante['adq_dir'], 0, 50);
            $this->setXY($posx + 87, $posy + 39);
            $this->MultiCell(40, 4, utf8_decode($dir), 0, 'C');

            $this->setXY($posx + 67, $posy + 49);
            $this->Cell(20, 4, utf8_decode('Teléfono:'), 0, 0, 'L');

            $this->setXY($posx + 87, $posy + 49);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adq_tel']), 0, 0, 'L');
            $this->setXY($posx + 67, $posy + 54);
            $this->Cell(20, 4, utf8_decode('Ciudad:'), 0, 0, 'L');
            $this->setXY($posx + 87, $posy + 54);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adq_mun']), 0, 0, 'L');

            // Tercer cuadro
            $this->Rect($posx + 130, $posy + 37, 65, 25);
            $this->setXY($posx + 132, $posy + 39);
            $this->Cell(25, 4, utf8_decode('Afecta Factura:'), 0, 0, 'L');
            $this->setXY($posx + 157, $posy + 39);
            $this->Cell(25, 4, utf8_decode($this->datosComprobante['numero_documento']), 0, 0, 'L');
            $this->setXY($posx + 132, $posy + 44);
            $this->Cell(25, 4, utf8_decode('Fecha Expedición:'), 0, 0, 'L');
            $this->SetFont("Helvetica", '', 7);
            $this->setXY($posx + 157, $posy + 44);
            $this->Cell(25, 4, utf8_decode(date("Y-m-d", strtotime($this->datosComprobante['fecha_hora_documento']))), 0, 0, 'L');
            $this->SetFont("Helvetica", '', 7);

            $this->setXY($posx + 132, $posy + 51);
            $this->Cell(10, 4, utf8_decode('Cufe: '), 0, 0, 'L');
            $posyCufe = $posy + 51;
            // Si el cufe es muy largo, lo divide en fragmentos mas pequeños
            for ($i = 0; $i < strlen($this->datosComprobante['cufe']); $i += 36) {
                $rest = substr($this->datosComprobante['cufe'], $i, 36);
                $this->setXY($posx + 140, $posyCufe);
                $this->Cell(10, 4, utf8_decode($rest), 0, 0, 'L');
                $posyCufe += 4;
            }

            $this->SetFont("Helvetica", '', 8);
            // Segundo cuadro contenedor
            $this->Rect($posx, $posy + 68, 195, 14);
            // Cuadro divisor
            $this->Rect($posx, $posy + 68, 195, 7);
            // Centro
            $this->Rect($posx, $posy + 68, 39, 14);
            $this->setXY($posx, $posy + 70);
            $this->Cell(39, 4, utf8_decode('Centro'), 0, 0, 'C');
            $this->setXY($posx, $posy + 77);
            $this->Cell(39, 4, utf8_decode($this->datosComprobante['centro']), 0, 0, 'C');

            /*{"centro": "Principal", "numero_pedido": "25", "fecha_pedido": "2019-01-10", "numero_remision": "1234", "fecha_entrada": "2019-01-10"}*/
            // No. Pedido
            $this->Rect($posx + 39, $posy + 68, 39, 14);
            $this->setXY($posx + 39, $posy + 70);
            $this->Cell(39, 4, utf8_decode('No. Pedido'), 0, 0, 'C');
            $this->setXY($posx + 39, $posy + 77);
            $this->Cell(39, 4, utf8_decode($this->datosComprobante['numero_pedido']), 0, 0, 'C');
            //Fecha de Pedido
            $this->Rect($posx + 78, $posy + 68, 39, 14);
            $this->setXY($posx + 78, $posy + 70);
            $this->Cell(39, 4, utf8_decode('Fecha de Pedido'), 0, 0, 'C');
            $this->setXY($posx + 78, $posy + 77);
            $this->Cell(39, 4, utf8_decode($this->datosComprobante['fecha_pedido']), 0, 0, 'C');
            // Fecha de Entrada
            $this->Rect($posx + 117, $posy + 68, 39, 14);
            $this->setXY($posx + 117, $posy + 70);
            $this->Cell(39, 4, utf8_decode('Fecha de Entrada'), 0, 0, 'C');
            $this->setXY($posx + 117, $posy + 77);
            $this->Cell(39, 4, utf8_decode($this->datosComprobante['fecha_entrada']), 0, 0, 'C');
            // Nro. Remisión
            $this->Rect($posx + 156, $posy + 68, 39, 14);
            $this->setXY($posx + 156, $posy + 70);
            $this->Cell(39, 4, utf8_decode('Nro. Remisión'), 0, 0, 'C');
            $this->setXY($posx + 156, $posy + 77);
            $this->Cell(39, 4, utf8_decode($this->datosComprobante['numero_remision']), 0, 0, 'C');

            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);
            $this->setXY($posx, $posy + 200);
            $this->SetFont('Helvetica', '', 7);
            $this->Cell(10, 16, utf8_decode(strtoupper("Item")), "B", 0, 'C', true);
            $this->Cell(15, 16, utf8_decode(strtoupper("Codigo")), "B", 0, 'C', true);
            $this->Cell(15, 16, utf8_decode(strtoupper("Cantidad")), "B", 0, 'C', true);
            $this->Cell(15, 16, utf8_decode(strtoupper("Medida")), "B", 0, 'C', true);
            $this->Cell(44, 16, utf8_decode(strtoupper("Descripcion del producto")), "B", 0, 'C', true);
            $this->Cell(18, 16, utf8_decode(strtoupper("Composicion")), "B", 0, 'C', true);
            $this->Cell(18, 16, utf8_decode(strtoupper("Subpartida")), "B", 0, 'C', true);
            $this->Cell(10, 16, utf8_decode(strtoupper("Tejido")), "B", 0, 'C', true);
            $this->Cell(55, 16, utf8_decode(strtoupper("Valor unitario")), "B", 0, 'C', true);
            $this->Cell(100, 16, utf8_decode(strtoupper("Valor total")), "B", 0, 'C', true);
            $posx = 10;
            $this->setXY($posx, $posy + 200);
            $this->nPosYDet = $posy + 95;
        }
    }

    function Footer()
    {
        // Datos QR y Firma
        $posx = $this->posx;
        $posy = 175;
        $this->SetFont('Helvetica', '', 10);

        if ($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != "") {

            if ($this->datosComprobante['cdo_tipo'] === "FC") {
                $tipo = 'CUFE';
            } elseif ($this->datosComprobante['cdo_tipo'] === "NC") {
                $tipo = 'CUDE';
            }

            $this->setXY($posx, $posy + 59);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(70, 3, utf8_decode($tipo), 0, 0, 'L');
            $this->Ln(3);
            $this->setX($posx);
            $this->SetFont('Arial', 'B', 6);
            $this->MultiCell(45, 3, utf8_decode($this->datosComprobante['cufe']), 0, 'L');

            $dataURI = "data:image/png;base64, " . base64_encode((string) \QrCode::format('png')->size(85)->margin(0)->generate($this->datosComprobante['qr']));
            $pic = $this->getImage($dataURI);
            if ($pic !== false) $this->Image($pic[0], $posx, $posy + 73, 0, 0, $pic[1]);

            // Tipo de Factura
            if ($this->datosComprobante['cdo_tipo'] === "FC") {
                $tipo = 'factura';
            } elseif ($this->datosComprobante['cdo_tipo'] === "NC") {
                $tipo = 'nota de credito';
            }

            $this->SetFont('Arial', 'B', 7);
            $this->setXY($posx  + 65, $posy + 60);
            $this->Cell(130, 4, utf8_decode("Representacion impresa de la " . $tipo . " electronica:"), 0, 0, 'L');
            $this->Ln(4);
            $this->setX($posx + 65);
            $this->Cell(130, 3, utf8_decode("Firma Electrónica:"), 0, 0, 'L');
            $this->Ln(4);
            $this->setX($posx + 65);
            $this->SetFont('Arial', 'B', 6);
            $this->MultiCell(130, 4, $this->datosComprobante['signaturevalue'], 0, 'J');
        }

        //Paginacion
        $this->setXY($posx, $posy + 91);
        $this->SetFont('Arial', '', 7);
        $this->Cell(190, 4, utf8_decode('Pág. ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
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

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c ',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }

    function TextWithDirection($x, $y, $txt, $direction = 'U')
    {
        if ($direction == 'R')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 1, 0, 0, 1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        elseif ($direction == 'L')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', -1, 0, 0, -1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        elseif ($direction == 'U')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, 1, -1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        elseif ($direction == 'D')
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, -1, 1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        else
            $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
        if ($this->ColorFlag)
            $s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
        $this->_out($s);
    }
}
