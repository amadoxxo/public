<?php
/**
 * Created by PhpStorm.
 * User: Juan Jose Trujillo
 * Date: 18/11/19
 * Time: 12:40 PM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_aduamarx\rg860508649;

use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;

class PDF860508649_1 extends PDFBase {
    
    public $Imprime_Item;

    function Header() {

        if($this->datosComprobante['signaturevalue'] == '' && $this->datosComprobante['qr'] ==''){
            $this->Image($this->datosComprobante['no_valido'],20,50,180,180);
        }
        
        $posx = 9;
        $posy = 9;
        
        $this->posx = $posx;
        $this->posy = $posy;

        //Logo
        $this->Image($this->datosComprobante['logo'], $posx+4, $posy, 30);

        $this->SetFont('Arial','',6);
        $this->TextWithDirection(209,80,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): ".$this->datosComprobante['razon_social_pt']." NIT: ".$this->datosComprobante['nit_pt']." NOMBRE DEL SOFTWARE: ".$this->datosComprobante['nombre_software']),'D');

        if ($this->datosComprobante['cdo_tipo'] === "FC") {
            //Logo Bureau Veritas
            $this->Image($this->datosComprobante['logo_bureau'], $posx+157, $posy+6, 37);            

            ## Datos OFE ##
            $this->setXY($posx+73,$posy);
            $this->SetFont('Arial','B',8);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['razon_ofe']),0,0,'C');
            $this->Ln(4);
            $this->setX($posx+73);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['impo_expo']),0,0,'C');
            $this->Ln(6);
            $this->setX($posx+73);
            $this->SetFont('Arial','',7.5);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['ofe_dir'] . ", Teléfono: " . $this->datosComprobante['ofe_tel']),0,0,'C');
            $this->Ln(3.5);
            $this->setX($posx+73);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['autorizacion']),0,0,'C');
            $this->Ln(3.5);
            $this->setX($posx+73);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['vigencia']),0,0,'C');
            $this->Ln(5);
            $this->setX($posx+73);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['nit_ofe']),0,0,'C');
            $this->Ln(3.5);
            $this->setX($posx+73);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['regimen_comun']),0,0,'C');
            $this->Ln(3.5);
            $this->setX($posx+73);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['actividad_economica']),0,0,'C');
            $this->Ln(3.5);
            $this->setX($posx+73);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['agente_retenedor']),0,0,'C');

            //Fecha Elaboracion
            $posx = 8;
            $this->setXY($posx, $posy+33);
            $this->SetFont('Arial','B',7);
            $this->Cell(30,5, utf8_decode("FECHA ELABORACIÓN"),0,0,'C');
            $this->Cell(30,5, utf8_decode("HORA GENERACIÓN"),0,0,'C');
            $this->setXY($posx, $posy+39);
            $this->SetFont('Arial','',7);
            $this->Cell(9,5, date('d', strtotime($this->datosComprobante['fecha_documento'])),0,0,'C');
            $this->Cell(9,5, date('m', strtotime($this->datosComprobante['fecha_documento'])),0,0,'C');
            $this->Cell(11,5, date('Y', strtotime($this->datosComprobante['fecha_documento'])),0,0,'C');
            $this->setXY($posx+31, $posy+39);
            $this->Cell(9,5, date('H', strtotime($this->datosComprobante['hora_documento'])),0,0,'C');
            $this->Cell(9,5, date('i', strtotime($this->datosComprobante['hora_documento'])),0,0,'C');
            $this->Cell(9,5, date('s', strtotime($this->datosComprobante['hora_documento'])),0,0,'C');

            $posy += 33;
            $this->Rect($posx, $posy, 60, 12);
            $this->Line($posx, $posy+5, $posx+60, $posy+5);
            $this->Line($posx+9, $posy+5, $posx+9, $posy+12);
            $this->Line($posx+18, $posy+5, $posx+18, $posy+12);
            $this->Line($posx+31, $posy, $posx+31, $posy+12);
            $this->Line($posx+40, $posy+5, $posx+40, $posy+12);
            $this->Line($posx+49, $posy+5, $posx+49, $posy+12);

            //Numero de la Factura
            $this->setXY($posx+164, $posy);
            $this->SetFont('Arial','B',7);
            $this->MultiCell(35, 3, utf8_decode("FACTURA ELECTRÓNICA DE VENTA"),0,'C');
            $this->Ln(0.5);
            $this->setX($posx+164);
            $this->setTextColor(255,0,0);
            $this->SetFont('arial', '', 8);
            $this->Cell(30, 5, $this->datosComprobante['numero_documento'],0,0,'C');
            $this->setTextColor(0);
            $this->Line($posx+163, $posy+6, $posx+199, $posy+6);
            $this->Rect($posx+163, $posy-1, 36, 13);

            //Datos del Adquirente
            $posy += 13;
            $posyIni = $posy;
            //Primera fila
            $this->setXY($posx, $posy);
            $this->SetFont('arial', 'B', 7);
            $this->Cell(30, 3, "CLIENTE",0,0,'L');
            $this->setXY($posx+19, $posy);
            $this->SetFont('arial', '', 7);
            $this->MultiCell(80, 3.5, utf8_decode($this->datosComprobante['adquirente'] ),0,'L');
            $posyfin = $this->getY()+0.5;
            $this->setXY($posx+100, $posy);
            $this->SetFont('arial', 'B', 7);
            $this->Cell(25, 3, "FORMA DE PAGO",0,0,'C');
            $this->SetFont('arial', '', 7);
            $this->Cell(20, 3, utf8_decode($this->datosComprobante['forma_pago']),0,0,'L');
            $this->setXY($posx+147, $posy);
            $this->SetFont('arial', 'B', 7);
            $this->Cell(36, 3, "FECHA DE VENCIMIENTO",0,0,'C');
            $this->SetFont('arial', '', 7);
            $this->Cell(20, 3, $this->datosComprobante['fecha_vencimiento'],0,0,'L');

            $this->Line($posx+18, $posy-1, $posx+18, $posyfin);
            $this->Line($posx+100, $posy-1, $posx+100, $posyfin);
            $this->Line($posx+125, $posy-1, $posx+125, $posyfin);
            $this->Line($posx+147, $posy-1, $posx+147, $posyfin);
            $this->Line($posx+182, $posy-1, $posx+182, $posyfin);

            $this->Line($posx, $posyfin, $posx+199, $posyfin);
            $posy = $posyfin;
            $this->setXY($posx,$posy);
            //Segunda fila
            $this->SetFont('arial', 'B', 7);
            $this->Cell(30, 5, "TELEFONO",0,0,'L');
            $this->setX($posx+23);
            $this->Cell(30, 5, utf8_decode("DIRECCIÓN"),0,0,'L');
            $this->setX($posx+45);
            $this->SetFont('arial', '', 7);
            $this->Cell(30, 5, substr($this->datosComprobante['adq_dir'], 0, 57),0,0,'L');
            $this->setX($posx+149);
            $this->SetFont('arial', 'B', 7);
            $this->Cell(30, 5, "NIT",0,0,'L');
            $this->Ln(5);
            //Tercera fila
            $this->setX($posx);
            $this->SetFont('arial', '', 7);
            $this->Cell(30, 4, $this->datosComprobante['adq_tel'],0,0,'L');
            $this->setX($posx+23);
            $this->SetFont('arial', 'B', 7);
            $this->Cell(30, 4, "CIUDAD",0,0,'L');
            $this->setX($posx+45);
            $this->SetFont('arial', '', 7);
            $this->Cell(30, 4, substr(utf8_decode($this->datosComprobante['adq_mun']), 0, 58),0,0,'L');
            $this->setX($posx+149);
            $this->Cell(30, 4, $this->datosComprobante['adq_nit'], 0, 0, 'L');
            $this->Ln(4.5);

            $this->Line($posx+23, $posyfin, $posx+23, $this->getY());
            $this->Line($posx+45, $posyfin, $posx+45, $this->getY());
            $this->Line($posx+147, $posyfin, $posx+147, $this->getY());
            $this->Line($posx, $posyfin+5, $posx+199, $posyfin+5);
            $this->Rect($posx, $posyIni-1, 199, $this->getY() - ($posyIni-1));

            $posy = $this->getY()+1;
            $this->setXY($posx, $posy);
            $this->SetFont('arial', '', 6);  
            $this->Cell(199, 3, utf8_decode($this->datosComprobante['texto_cabecera']),0,0,'C');

            //Datos Generales del DO
            $posy += 7;
            $posyIni = $posy;
            $this->setXY($posx+7, $posy);
            $this->SetFont('arial', 'B', 7);
            $this->Cell(30, 3, "VAPOR Y/O EMPRESA AREA",0,0,'C');
            $this->setX($posx+50);
            $this->Cell(20, 3, "FECHA",0,0,'C');
            $this->setX($posx+75);
            $this->Cell(20, 3, "BULTOS",0,0,'C');
            $this->setX($posx+95);
            $this->Cell(20, 3, "DE",0,0,'C');
            $this->setX($posx+120);
            $this->Cell(20, 3, "KILOS BRUTOS",0,0,'C');
            $this->setX($posx+149);
            $this->Cell(20, 3, "LICENCIA No.",0,0,'C');
            $this->setX($posx+175);
            $this->Cell(20, 3, "FECHA",0,0,'C');

            $posy += 4;
            $this->setXY($posx, $posy);
            $this->SetFont('arial', '', 7);
            $this->MultiCell(45, 3, substr($this->datosComprobante['transportadora'], 0, 52),0,'L');
            $this->setXY($posx+50, $posy);
            $this->Cell(20, 3, $this->datosComprobante['fecha_transporte'],0,0,'C');
            $this->setXY($posx+75, $posy);
            $this->Cell(20, 3, $this->datosComprobante['bultos'],0,0,'C');
            $this->setXY($posx+95, $posy);
            $this->Cell(20, 3, $this->datosComprobante['codigo_embalaje'],0,0,'C');
            $this->setXY($posx+120, $posy);
            $this->Cell(20, 3, $this->datosComprobante['peso_bruto'],0,0,'C');
            $this->setXY($posx+149, $posy);
            $this->Cell(20, 3, "",0,0,'C');
            $this->setXY($posx+175, $posy);
            $this->Cell(20, 3, "",0,0,'C'); 

            $posy += 7;
            $this->setXY($posx+12, $posy);
            $this->SetFont('arial', 'B', 7);
            $this->Cell(20, 3, "D.O No.",0,0,'C');
            $this->setXY($posx+85, $posy);
            $this->Cell(20, 3, "PEDIDO No.",0,0,'C');
            $this->setXY($posx+163, $posy);
            $this->Cell(20, 3, "B/L - AWB No.",0,0,'C');

            $posy += 4;
            $this->setXY($posx, $posy);
            $this->SetFont('arial', '', 7);
            $this->Cell(60, 3, $this->datosComprobante['do'],0,0,'L');
            $this->setXY($posx+143, $posy);
            $this->Cell(60, 3, $this->datosComprobante['documento_transporte'],0,0,'C');
            $this->setXY($posx+50, $posy);
            $this->MultiCell(93, 3, utf8_decode(implode("\n", $this->datosComprobante['observacion_decode'])),0,'L');
            $posyFin = $this->getY();

            $this->Line($posx, $posyIni+3, $posx+199, $posyIni+3);
            $this->Line($posx, $posyIni+10, $posx+199, $posyIni+10);
            $this->Line($posx, $posyIni+14, $posx+199, $posyIni+14);

            $this->Line($posx+48, $posyIni-1, $posx+48, $posyFin+1);
            $this->Line($posx+73, $posyIni-1, $posx+73, $posyIni+10);
            $this->Line($posx+115, $posyIni-1, $posx+115, $posyIni+10);
            $this->Line($posx+95, $posyIni-1, $posx+95, $posyIni+10);
            $this->Line($posx+146, $posyIni-1, $posx+146, $posyFin+1);
            $this->Line($posx+172, $posyIni-1, $posx+172, $posyIni+10);
            $this->Rect($posx, $posyIni-1, 199, $posyFin - ($posyIni-2));

            $posyfin = $this->getY();
        
            $posy = $posyfin+5;
            $this->setXY($posx,$posy);
            $this->SetFont('Arial','B',8);
            $this->Cell(10,6,utf8_decode("ITEM"),0,0,'C');
            $this->Cell(20,6,utf8_decode("CÓDIGO"),0,0,'C');
            $this->Cell(90,6,utf8_decode("DESCRIPCIÓN"),0,0,'C');
            $this->Cell(20,6,utf8_decode("UNIDAD"),0,0,'C');
            $this->Cell(19,6,"CANTIDAD",0,0,'C');
            $this->MultiCell(21, 3, "VALOR\nUNITARIO", 0, 'C');
            $this->setXY($posx,$posy);
            $this->Cell(378,6,"VALOR",0,0,'C');

            $this->Rect($posx, $posy-1, 199, 200-$posy+1, 2);
            $pyy = (200-$posy)+$posy;
            $this->Line($posx + 10, $posy - 1, $posx + 10, $pyy);
            $this->Line($posx + 30, $posy - 1, $posx + 30, $pyy);
            $this->Line($posx + 120, $posy - 1, $posx + 120, $pyy);
            $this->Line($posx + 140, $posy - 1, $posx + 140, $pyy);
            $this->Line($posx + 160, $posy - 1, $posx + 160, $pyy);
            $this->Line($posx + 180, $posy - 1, $posx + 180, $pyy);
            
            $this->nPosYDet = $posy+6;

        }elseif($this->datosComprobante['cdo_tipo'] === "NC" || $this->datosComprobante['cdo_tipo'] === "ND") {
            $this->setXY($posx+40, $posy + 15);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['regimen_comun']),0,0,'C');
            $this->Ln(3.5);
            $this->setX($posx+40);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['actividad_economica']),0,0,'C');
            $this->Ln(3.5);
            $this->setX($posx+40);
            $this->Cell(50,5,utf8_decode($this->datosComprobante['agente_retenedor']),0,0,'C');

            //Label nit, direccion y telefono
            $this->setXY($posx, $posy + 35);
            $this->SetFont('Arial','B',9);
            $this->Cell(195, 4, utf8_decode('Nit:'), 0, 0, 'L');
            $this->Ln(5);
            $this->setX($posx);
            $this->Cell(4, 4, utf8_decode('Dir:'), 0, 0, 'L');
            $this->setX($posx + 70);
            $this->Cell(4, 4, utf8_decode('Tel:'), 0, 0, 'L');

            // Valor direccion y telefono
            // Nit
            $this->setXY($posx+7, $posy + 35);
            $this->SetFont('Arial','',9);
            $this->Cell(195, 4, $this->datosComprobante['ofe_nit'], 0, 0, 'L');
            $this->Ln(5);
            $dir = substr($this->datosComprobante['ofe_dir'],0,40);
            $this->setX($posx +7);
            $this->Cell(32, 4, utf8_decode($dir), 0, 0, 'L');
            $this->setX($posx +78);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['ofe_tel']), 0, 0, 'L');
            $this->Ln(5);
            $this->setX($posx);
            $this->SetFont('Arial','B',9);
            $this->Cell(195, 4, utf8_decode(strtoupper($this->datosComprobante['ofe_mun'].' - '.$this->datosComprobante['ofe_pais'])), 0, 0, 'L');

            /* Tipo de documento Factura de Venta o Nota de Crédito */
            $this->SetFont("Arial", 'b', 15);
            $this->setXY($posx, $posy + 6);
            $this->Cell(195, 4, utf8_decode("NOTA CRÉDITO ELECTRÓNICA"), 0, 0, 'R');
            // Cuadro del N°
            $this->Rect($posx+ 132, $posy + 12, 62, 15);
            // N°
            $this->SetFont("Arial", 'B', 13);
            $this->setXY($posx + 132, $posy + 18);
            $this->Cell(62, 4, utf8_decode('N°: '.$this->datosComprobante['numero_documento']), 0, 0, 'C');
            /* Datos para caso Factura Electrónica*/

            /*  Datos del adquirente */
            $posyini = $posy + 50;
            $this->setXY($posx, $posy + 50.5);
            $this->SetFont("Arial", 'B', 8);
            //adquirente
            $this->Cell(10, 3, utf8_decode('Señores:'), 0, 0, 'L');
            $this->setX($posx+16);
            $this->SetFont("Arial", '', 8);
            $this->MultiCell(97, 3, utf8_decode($this->datosComprobante['adquirente']), 0, 'L');

            //Nit
            $this->Ln(1);
            $this->setX($posx);
            $this->SetFont("Arial", 'B', 8);
            $this->Cell(16, 4, utf8_decode('Nit:'), 0, 0, 'L');
            $this->SetFont("Arial", '', 8);
            $this->Cell(20, 4, $this->datosComprobante['adq_nit'], 0, 0, 'L');
            //Direccion
            $this->Ln(5);
            $this->setX($posx);
            $this->SetFont("Arial", 'B', 8);
            $this->Cell(10, 3, utf8_decode('Dirección:'), 0, 0, 'L');
            $this->setX($posx+16);
            $this->SetFont("Arial", '', 8);
            $this->MultiCell(97, 3, utf8_decode($this->datosComprobante['adq_dir']), 0, 'L');
            
            //Telefono
            $this->Ln(1.5);
            $this->setX($posx);
            $this->SetFont("Arial", 'B', 8);
            $this->Cell(16, 4, utf8_decode('Teléfono:'), 0, 0, 'L');
            $this->SetFont("Arial", '', 8);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adq_tel']), 0, 0, 'L');
            //Ciudad
            $this->Ln(5);
            $this->setX($posx);
            $this->SetFont("Arial", 'B', 8);
            $this->Cell(16, 4, utf8_decode('Ciudad:'), 0, 0, 'L');
            $this->SetFont("Arial", '', 8);
            $this->Cell(20, 4, utf8_decode($this->datosComprobante['adq_mun']), 0, 0, 'L');
            $this->Ln(4);
            $posyfin = $this->getY();

            /** Datos Factura **/
            $this->setXY($posx+120, $posy + 50);
            $this->SetFont("Arial", 'B', 8);
            //Fecha emision
            $this->Cell(23, 4, utf8_decode('Fecha Emisión:'), 0, 0, 'L');
            $this->SetFont("Arial", '', 8);
            $this->Cell(30, 4, date("Y-m-d", strtotime($this->datosComprobante['fecha_hora_documento'])), 0, 0, 'L');
            //Hora emision
            $this->Ln(5);
            $this->setX($posx+120);
            $this->SetFont("Arial", 'B', 8);
            $this->Cell(23, 4, utf8_decode('Hora Emisión:'), 0, 0, 'L');
            $this->SetFont("Arial", '', 8);
            $this->Cell(30, 4, date("h:i:s", strtotime($this->datosComprobante['fecha_hora_documento'])), 0, 0, 'L');
            //Afecta factura
            $this->Ln(5);
            $this->setX($posx+120);
            $this->SetFont("Arial", 'B', 8);
            $this->Cell(23, 4, utf8_decode('Afecta Factura:'), 0, 0, 'L');
            $this->SetFont("Arial", '', 8);
            $this->Cell(30, 4, $this->datosComprobante['consecutivo_ref'] , 0, 0, 'L');
            //Fecha Exp.
            $this->Ln(5);
            $this->setX($posx+120);
            $this->SetFont("Arial", 'B', 8);
            $this->Cell(27, 4, utf8_decode('Fecha Expedición:'), 0, 0, 'L');
            $this->SetFont("Arial", '', 8);
            $this->Cell(30, 4, $this->datosComprobante['fecha_emision'], 0, 0, 'L');
            //Fecha Exp.
            $this->Ln(5);
            $this->setX($posx+120);
            $this->SetFont("Arial", 'B', 8);
            $this->Cell(23, 3, utf8_decode('Cufe:'), 0, 0, 'L');
            $this->SetFont("Arial", '', 8);
            $this->setX($posx+133);
            $this->MultiCell(60, 3, $this->datosComprobante['cufe_ref'], 0, 'L');
            $this->Ln(2);

            if($posyfin < $this->getY()){
                $posyfin = $this->getY();
            }
            // Rectangulo datos adquirente
            $this->Rect($posx, $posyini, 199, $posyfin-$posyini);

            $posy = $posyfin+2;
            $this->SetFillColor(45, 168, 221);
            $this->SetTextColor(0);
            $this->setXY($posx, $posy);
            $this->SetFont('Arial', '', 8);
            $this->Cell(20, 6, utf8_decode("Item"), 0, 0, 'C', true);
            $this->Cell(29, 6, utf8_decode("Código"), 0, 0, 'C', true);
            $this->Cell(64, 6, utf8_decode("Descripción"), 0, 0, 'C', true);
            $this->Cell(22, 6, utf8_decode("Unidad"), 0, 0, 'C', true);
            $this->Cell(20, 6, utf8_decode("Cant"), 0, 0, 'C', true);
            $this->Cell(22, 6, utf8_decode("Valor Unitario"), 0, 0, 'R', true);
            $this->Cell(22, 6, utf8_decode("Valor Total"), 0, 0, 'R', true);
            $this->nPosYDet = $posy+6;
        }
    }

    function Footer() {

        /*** Impresion Datos QR y Firma. ***/
        $posy = 200;
        $posx = 8;

        if ($this->datosComprobante['cdo_tipo'] === "FC") {
        
            ## TOTALES ##
            $this->setXY($posx+148,$posy+12);
            $this->setTextColor(100);
            $this->Cell(20,4,"SUB TOTAL",0,0,'R');
            $this->Ln(6);
            $this->setX($posx+148);
            $this->Cell(20,4,"I.V.A.",0,0,'R');
            $this->Ln(6);
            $this->setX($posx+148);
            $this->Cell(20,4,"RETEIVA",0,0,'R');
            $this->Ln(6);
            $this->setX($posx+148);
            $this->Cell(20,4,"RETEICA",0,0,'R');
            $this->Ln(6);
            $this->setX($posx+148);
            $this->Cell(20,4,"RETEFUENTE",0,0,'R');
            $this->Ln(6);
            $this->setX($posx+148);
            $this->Cell(20,4,"TOTAL",0,0,'R');
            $this->setTextColor(0);
            $this->Ln(6);
            $this->setX($posx+148);
            $this->SetFont('Arial','B',8);
            $this->Cell(20,4,"ANTICIPO",0,0,'R');
            $this->Ln(6);
            $this->setTextColor(100);
            $this->setX($posx+148);
            $this->SetFont('Arial','B',8);
            $this->Cell(20,4,"TOTAL A PAGAR",0,0,'R');
            $this->Ln(6);
            $this->setXY($posx+143, $posy+60);
            $this->SetFont('Arial','B',8);
            $this->MultiCell(28,4,"SALDO A FAVOR DEL CLIENTE",0,'L');
            //Nota final
            $this->setXY($posx,$posy+1);
            $this->SetFont('Arial','',7);
            $this->MultiCell(190,3,utf8_decode($this->datosComprobante['nota_final_1']),0,'L');
            $this->Rect($posx, $posy, 199, 8);

            //Recuadro de retenciones
            $this->Rect($posx, $posy+9, 100, 18);

            $this->setXY($posx,$posy+28.5);
            $this->SetFont('Arial','',7);
            $this->setTextColor(120);
            $this->MultiCell(100,3,utf8_decode($this->datosComprobante['nota_final_2']),0,'L');
            $this->setTextColor(0);
            $this->Rect($posx, $posy+28, 100, 25);

            if($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != ""){

                $dataURI = "data:image/png;base64, ".base64_encode((string) \QrCode::format('png')->size(85)->margin(0)->generate($this->datosComprobante['qr']));
                $pic = $this->getImage($dataURI);
                if ($pic!==false) $this->Image($pic[0], $posx+101,$posy+13,38,38, $pic[1]);

                $this->setXY($posx,$posy+55);
                $this->SetFont('Arial','',8);
                $this->Cell(23,4,"VALOR CUFE:",1,0,'L');
                $this->setX($posx+24);
                $this->SetFont('Arial','',6.5);
                $this->MultiCell(113,4,utf8_decode($this->datosComprobante['cufe']),1,'L');
            }

            $this->setXY($posx,$posy+64);
            $this->SetFont('Arial','B',7);
            $this->Cell(100,4,utf8_decode("FECHA Y HORA DE VALIDACIÓN DIAN: ").$this->datosComprobante['validacion_dian'],0,0,'L');
            $this->Rect($posx, $posy+28, 100, 25);

            //Paginacion
            $this->setXY($posx,$posy+67);           
            $this->SetFont('Arial','',7);
            $this->Cell(194,4,utf8_decode('Pág. ').$this->PageNo().'/{nb}',0,0,'C');

        }elseif($this->datosComprobante['cdo_tipo'] === "NC" || $this->datosComprobante['cdo_tipo'] === "ND"){

            $posy +=30;
            $this->setXY($posx,$posy);
            $this->SetFont('arial','B',7);
            $this->Cell(20,4, utf8_decode("REPRESENTACIÓN IMPRESA DE LA NOTA CRÉDITO ELECTRÓNICA"),0,0,'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->SetFont('Arial', '', 6);
            $this->MultiCell(130, 4, $this->datosComprobante['signaturevalue'], 0, 'J');

            if($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != ""){
                $this->setXY($posx+157,$posy-5);
                $this->SetFont('Arial','B',7);
                $this->Cell(23,4, "CUDE",0,0,'L');
                $this->Ln(3.5);
                $this->setX($posx+157);
                $this->SetFont('Arial','',6.5);
                $this->MultiCell(40,3, utf8_decode($this->datosComprobante['cufe']),0,'L');

                $dataURI = "data:image/png;base64, ".base64_encode((string) \QrCode::format('png')->size(85)->margin(0)->generate($this->datosComprobante['qr']));
                $pic = $this->getImage($dataURI);
                if ($pic!==false) $this->Image($pic[0], $posx+165,$posy+10,25,25, $pic[1]);
            }

            //Paginacion
            $this->setXY($posx,$posy+36);           
            $this->SetFont('Arial','',7);
            $this->Cell(194,4,utf8_decode('Pág. ').$this->PageNo().'/{nb}',0,0,'C');
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