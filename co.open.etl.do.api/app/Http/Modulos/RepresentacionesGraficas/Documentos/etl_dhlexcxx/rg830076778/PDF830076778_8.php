<?php
/**
 * Created by PhpStorm.
 * User: Juan Jose Trujillo
 * Date: 24/05/19
 * Time: 10:45 a.m
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_dhlexcxx\rg830076778;

use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;

class PDF830076778_8 extends PDFBase {
    
    function Header() {

        //Logica para hacer Homologación en el nombre de tipo documento 
        $tdo_descripcion = $this->datosComprobante['tdo_descripcion'];
        if (strlen($tdo_descripcion) > 10)
            $offsetX    = round($this->getAnchoTexto(utf8_decode(mb_strtoupper($tdo_descripcion))) - 2);
        else
            $offsetX    = 20;

        $nSubStr = 55;

        if ($this->datosComprobante['tdo_codigo'] == '31'){
            $nSubStr = 55;
        } elseif($this->datosComprobante['tdo_codigo'] == '50'){
            $nSubStr = 52;
        } elseif($this->datosComprobante['tdo_codigo'] == '91'){
            $nSubStr = 55;
        } elseif($this->datosComprobante['tdo_codigo'] == '41'){
            $nSubStr = 52;
        } elseif($this->datosComprobante['tdo_codigo'] == '11'){
            $nSubStr = 52;
        } elseif($this->datosComprobante['tdo_codigo'] == '21'){
            $nSubStr = 48;
        } elseif($this->datosComprobante['tdo_codigo'] == '12'){
            $nSubStr = 94;
        } elseif($this->datosComprobante['tdo_codigo'] == '22'){
            $nSubStr = 48;
        } elseif($this->datosComprobante['tdo_codigo'] == '13'){
            $nSubStr = 52;
        } elseif($this->datosComprobante['tdo_codigo'] == '42'){
            $nSubStr = 48;
        }
        //FIN Logica para hacer Homologacion en el nombre de tipo documento

        $posy = 5;
        $posx = 10;            
        $this->posx = $posx;
        $this->posy = $posy;
        
        ## Contenedor Principal 
        $this->SetFillColor(255, 204, 0);
        $this->Rect($posx-2,$posy,200,270,'DF');
        ## Contenedor Datos Encabezado
        $this->SetFillColor(255, 255, 255);
        $this->Rect($posx+1,$posy+2,194,40,'DF');
        ## Logo
        $this->Image($this->imageHeader, $posx+5, $posy+7, 49, 14);

        ## Informacion del PT.
        $this->SetFont('Arial', '', 6);
        $this->TextWithDirection(210,50,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): ".$this->datosComprobante['razon_social_pt']." NIT: ".$this->datosComprobante['nit_pt']." NOMBRE DEL SOFTWARE: ".$this->datosComprobante['nombre_software']),'D');

        ## Datos Ofe
        $this->SetFillColor(255, 255, 255);
        $this->setXY($posx+4, $posy+23);
        $this->SetFont('Arial','B',9);
        $this->Cell(50,4,"NIT ".number_format($this->datosComprobante['ofe_nit'], 0, '', '.')."-".$this->datosComprobante['ofe_nit_consecutivo'],0,0,'C');
        $this->Ln(4.5);
        $this->setX($posx+4);
        $this->SetFont('Arial','',8);
        $this->Cell(50,4,utf8_decode($this->datosComprobante['ofe_dir']),0,0,'C');
        $this->Ln(4.5);
        $this->setX($posx+4);
        $this->Cell(50,4,utf8_decode("Teléfono ").$this->datosComprobante['ofe_tel'],0,0,'C');
        $this->Ln(4.5);
        $this->setX($posx+4);
        $this->Cell(50,4,utf8_decode($this->datosComprobante['ofe_mun']),0,0,'C');
        
        if($this->datosComprobante['cdo_tipo'] == "FC"){
            ## Datos resolucion
            $this->setXY($posx+75,$posy+4);
            $this->SetFont('Arial','B',7.5);
            $this->Cell(50,4,utf8_decode($this->datosComprobante['agencia']),0,0,'C');
            $this->Ln(5);
            $this->SetFont('Arial','',7);
            foreach ($this->datosComprobante['resolucion'] as $strResolucion) {
                $this->setX($posx+75);
                $this->Cell(50,4,utf8_decode($strResolucion),0,0,'C');
                $this->Ln(4);
            }
            $this->setX($posx+75);
            $this->SetFont('Arial','B',7.5);
            $this->Cell(50,4,utf8_decode($this->datosComprobante['facturacion_electronica']),0,0,'C');
            $this->Ln(8);
            $this->SetFont('Arial','B',7.5);
            foreach($this->datosComprobante['regimen'] as $strRegimen){
                $this->setX($posx+75);
                $this->Cell(50,4,utf8_decode($strRegimen),0,0,'C');
                $this->Ln(4);
            }

            //Tabla de Datos Factura
            //Rectangulo Rojo
            $this->SetFillColor(128, 0, 0);
            $this->RoundedRect($posx+144, $posy+2, 50, 5, 1, '1234','F');
            $this->setXY($posx+144, $posy+3);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(50,4,utf8_decode("FACTURA ELECTRÓNICA DE VENTA No."),0,0,'C');

            //Rectangulo Gris
            //Numero de Documento
            $this->SetFillColor(206, 210, 225);
            $this->RoundedRect($posx+144, $posy+7, 50, 6, 1, '34','F');
            $this->setXY($posx+144,$posy+8);
            $this->SetFont('Arial','B',8);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(50,4,$this->datosComprobante['numero_documento'],0,0,'C');
            
            //Rectangulo Rojo FECHA
            $this->SetFillColor(128, 0, 0);
            $this->RoundedRect($posx+144, $posy+13, 50, 5, 1, '1234','F');
            $this->setXY($posx+144,$posy+14);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(50,4,"FECHA DOCUMENTO",0,0,'C');
            $this->SetTextColor(0, 0, 0);

            //Rectangulo Gris FECHA
            $this->SetFillColor(206, 210, 225);
            $this->RoundedRect($posx+144,$posy+18, 50, 9, 1, '34','F');

            //Separadores color Gris de los valores de la Fecha del Documento
            $this->SetDrawColor(255, 255, 255);
            $this->SetLineWidth(0.2);
            $this->Line($posx+144,$posy+22,$posx+193.8,$posy+22);
            $this->Line($posx+156.5,$posy+18.3,$posx+156.5,$posy+27);
            $this->Line($posx+169,$posy+18.3,$posx+169,$posy+27);
            $this->Line($posx+181.5,$posy+18.3,$posx+181.5,$posy+27);

            $this->setXY($posx+143,$posy+18);
            $this->SetFont('Arial','B',6);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(15,4,utf8_decode("DÍA"),0,0,'C');
            $this->setX($posx+155);
            $this->Cell(15,4,utf8_decode("MES"),0,0,'C');
            $this->setX($posx+168);
            $this->Cell(15,4,utf8_decode("AÑO"),0,0,'C');
            $this->setX($posx+180);
            $this->Cell(15,4,utf8_decode("HORA"),0,0,'C');

            //Valores Fecha de Documento
            $this->setXY($posx+143, $posy+23);
            $this->SetFont('Arial','',8);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(15,4,date('d', strtotime($this->datosComprobante['fecha_hora_documento'])),0,0,'C');
            $this->setX($posx+155);
            $this->Cell(15,4,date('m', strtotime($this->datosComprobante['fecha_hora_documento'])),0,0,'C');
            $this->setX($posx+168);
            $this->Cell(15,4,date('Y', strtotime($this->datosComprobante['fecha_hora_documento'])),0,0,'C');
            $this->setX($posx+180);
            $this->Cell(15,4,date('H:i', strtotime($this->datosComprobante['fecha_hora_documento'])),0,0,'C');

            //Rectangulo Rojo VENCIMIENTO
            $this->SetFillColor(128, 0, 0);
            $this->RoundedRect($posx+144, $posy+27, 50, 5, 1, '1234','F');
            $this->setXY($posx+144, $posy+28);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(50,4,"FECHA VENCIMIENTO PAGO",0,0,'C');
            $this->SetTextColor(0, 0, 0);

            //Rectangulo Gris VENCIMIENTO
            $this->SetFillColor(206, 210, 225);
            $this->RoundedRect($posx+144, $posy+32, 50, 9.3, 1, '34','F');

            //Separadores color Gris de los valores de la Fecha del Documento
            $this->SetDrawColor(255, 255, 255);
            $this->SetLineWidth(0.2);
            $this->Line($posx+144,$posy+36,$posx+193.8,$posy+36);
            $this->Line($posx+156.5,$posy+32.3,$posx+156.5,$posy+41);
            $this->Line($posx+169,$posy+32.3,$posx+169,$posy+41);
            $this->Line($posx+181.5,$posy+32.3,$posx+181.5,$posy+41);

            $this->setXY($posx+143,$posy+32);
            $this->SetFont('Arial','B',6);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(15,4,utf8_decode("DÍA"),0,0,'C');
            $this->setX($posx+155);
            $this->Cell(15,4,utf8_decode("MES"),0,0,'C');
            $this->setX($posx+168);
            $this->Cell(15,4,utf8_decode("AÑO"),0,0,'C');
            $this->setX($posx+180);
            $this->Cell(15,4,utf8_decode("HORA"),0,0,'C');

            //Valores Fecha de Documento
            $this->setXY($posx+143, $posy+37);
            $this->SetFont('Arial','',8);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(15,4,date('d', strtotime($this->datosComprobante['fecha_vencimiento'])),0,0,'C');
            $this->setX($posx+155);
            $this->Cell(15,4,date('m', strtotime($this->datosComprobante['fecha_vencimiento'])),0,0,'C');
            $this->setX($posx+168);
            $this->Cell(15,4,date('Y', strtotime($this->datosComprobante['fecha_vencimiento'])),0,0,'C');
            $this->setX($posx+180);
            $this->Cell(15,4,date('H:i', strtotime($this->datosComprobante['cdo_hora'])),0,0,'C');

            /*****  Datos Cliente FC *****/
            $posy = $posy+45;
            $this->SetFillColor(255, 255, 255);
            if(strtoupper($this->datosComprobante['forma_pago']) == "CONTADO"){
                $this->RoundedRect($posx+1,$posy, 194, 35, 2, '1234','F');
            }else{
                $this->RoundedRect($posx+1,$posy, 194, 30, 2, '1234','F');
            }

            //Columna 1
            $this->setXY($posx+7,$posy+1);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(0, 0, 0);
            $this->Cell($offsetX, 4,utf8_decode("SEÑORES:"),0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(150,4,utf8_decode(substr($this->datosComprobante['adquirente'],0,$nSubStr)),0,0,'L');

            $this->Ln(5);
            $this->setX($posx+7);
            $this->SetFont('Arial','B',7);
            $this->Cell($offsetX, 4,utf8_decode("DIRECCIÓN:"),0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(160,3,utf8_decode(substr($this->datosComprobante['adq_dir'],0,$nSubStr)),0,0,'L');
            $this->Ln(5);
            $this->setX($posx+7);
            $this->SetFont('Arial','B',7);
            $this->Cell($offsetX, 4,"CIUDAD:",0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(30,4,utf8_decode($this->datosComprobante['adq_mun']),0,0,'L');
            $this->Ln(5);
            $this->setX($posx+7);
            $this->SetFont('Arial','b',7);
            $this->Cell($offsetX, 4,utf8_decode(mb_strtoupper($tdo_descripcion)),0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(150,4,$this->datosComprobante['adq_nit'],0,0,'L');
            $this->Ln(5);
            $this->setX($posx+7);
            $this->SetFont('Arial','B',7);
            $this->Cell($offsetX, 4,"TELEFONO:",0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(160,3,$this->datosComprobante['adq_tel'],0,0,'L');

            //Columna 2
            $posx += 125;
            $this->setXY($posx,$posy+1);
            $this->SetFont('Arial','B',7);
            $this->Cell(27,4,"DO:",0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(20,4,$this->datosComprobante['do'],0,0,'L');
            $this->Ln(5);
            $this->setX($posx);
            $this->SetFont('Arial','b',7);
            $this->Cell(27,4,utf8_decode("GUÍA AÉREA:"),0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(20,4,$this->datosComprobante['guia'],0,0,'L');
            $this->Ln(5);
            $this->setX($posx);
            $this->SetFont('Arial','B',7);
            $this->Cell(27,4,"VALOR CIF:",0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(25,4,number_format($this->datosComprobante['valor_cif'],2,'.','.'),0,0,'L');
            $this->Ln(5);
            $this->setX($posx);
            $this->SetFont('Arial','B',7);
            $this->Cell(27,4,"TASA DE CAMBIO:",0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(25,4,number_format($this->datosComprobante['trm'],2,'.','.'),0,0,'L');
            $this->Ln(5);
            $this->setX($posx);
            $this->SetFont('Arial','B',7);
            $this->Cell(27,4,"FORMA DE PAGO:",0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(25,4,utf8_decode($this->datosComprobante['forma_pago']),0,0,'L');
            if(strtoupper($this->datosComprobante['forma_pago']) == "CONTADO"){
                $this->Ln(5);
                $this->setX($posx);
                $this->SetFont('Arial','B',7);
                $this->Cell(27,4,"MEDIO DE PAGO:",0,0,'L');
                $this->SetFont('Arial','',7);
                $this->Ln(0.5);
                $this->setX($posx+27);
                $this->MultiCell(40,3,substr(utf8_decode($this->datosComprobante['medio_pago']),0,58),0,'L');
            }
        
            // if($this->datosComprobante['codigo_barras'] != ""){
            //     $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            //     $dataURI = 'data:image/png;base64,' . base64_encode($generator->getBarcode(str_replace("~F1", " ",$this->datosComprobante['codigo_barras']), $generator::TYPE_CODE_128));
            //     $pic = $this->getImage($dataURI);
            //     if ($pic!==false) $this->Image($pic[0], $posx-27,$posy+18,60,6, $pic[1]);
            // }

            // $this->Ln(11);
            // $this->setX($posx-27);
            // $this->SetFont('Arial','B',7);
            // $this->Cell(58,4,$this->datosComprobante['codigo_barras'],0,0,'C');

            ## Cabecera de los Conceptos
            $posx = 11;
            $posy = 87;
            $this->SetFillColor(128, 0, 0);
            $this->RoundedRect($posx,$posy, 194, 5, 1, '1234', 'F');

            $this->setXY($posx,$posy+1);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(8,4,"ITEM",0,0,'C');
            $this->Cell(22,4,"COD. PRODUCTO",0,0,'C');
            $this->Cell(73,4,utf8_decode("DESCRIPCIÓN"),0,0,'C');
            $this->Cell(17,4,"UNIDAD",0,0,'C');
            $this->Cell(17,4,utf8_decode("CANTIDAD"),0,0,'C');
            $this->Cell(25,4,"VR. UNITARIO",0,0,'R');
            $this->Cell(25,4,"VALOR",0,0,'R');

            $this->nPosYIni = $posy+7;
            ## Rectangulo que contiene los conceptos
            $posy = $this->GetY()+4.5;
            $this->SetFillColor(255, 255, 255);
            $this->Rect($posx,$posy,194,148,'F');
            $this->SetDrawColor(0,0,0);
            $this->Line($posx+166,$posy,$posx+166,$posy+98,'F');
            
            
        }elseif($this->datosComprobante['cdo_tipo'] == "NC"){
            ## Datos resolucion
            $this->setXY($posx+72,$posy+10);
            $this->SetFont('Arial','B',7.5);
            $this->MultiCell(55,6,utf8_decode($this->datosComprobante['agencia']),0,'C');
            $this->Ln(5);
            foreach ($this->datosComprobante['regimen'] as $strRegimen) {
                $this->setX($posx+75);
                $this->Cell(50,4,utf8_decode($strRegimen),0,0,'C');
                $this->Ln(4);
            }

            ## Tabla de Datos Nota Credito
            ## Rectangulo Rojo
            $this->SetFillColor(128, 0, 0);
            $this->RoundedRect($posx+147.5, $posy+2, 47, 5, 1, '1234','F');
            $this->setXY($posx+147.5, $posy+3);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(47,4,utf8_decode("NOTA CREDITO No."),0,0,'C');

            ## Rectangulo Gris
            ## Numero de Documento
            $this->SetFillColor(206, 210, 225);
            $this->RoundedRect($posx+147.5,$posy+7, 47, 7, 1, '34','F');
            $this->setXY($posx+147.5,$posy+8);
            $this->SetFont('Arial','',8);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(47,4,$this->datosComprobante['numero_documento'],0,0,'C');

            ## Rectangulo Rojo FECHA
            $this->SetFillColor(128, 0, 0);
            $this->RoundedRect($posx+147.5, $posy+14, 47, 4, 1, '1234','F');
            $this->setXY($posx+147.5,$posy+14);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(47,4,"FECHA",0,0,'C');
            $this->SetTextColor(0, 0, 0); 

            ## Rectangulo Gris FECHA
            $this->SetFillColor(206, 210, 225);
            $this->RoundedRect($posx+147.5,$posy+18, 47, 9, 1, '34','F');

            ## Separadores color Gris de los valores de la Fecha del Documento
            $this->SetDrawColor(255, 255, 255);
            $this->SetLineWidth(0.2);
            $this->Line($posx+145,$posy+22,$posx+194.5,$posy+22);            
            $this->Line($posx+163,$posy+18,$posx+163,$posy+27);
            $this->Line($posx+179,$posy+18,$posx+179,$posy+27);

            $this->setXY($posx+148, $posy+18);
            $this->SetFont('Arial','B',6);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(15,4,utf8_decode("AÑO"),0,0,'C');
            $this->setX($posx+163);
            $this->Cell(15,4,utf8_decode("MES"),0,0,'C');
            $this->setX($posx+180);
            $this->Cell(15,4,utf8_decode("DÍA"),0,0,'C');

            ## Valores Fecha de Documento
            $this->setXY($posx+148, $posy+23);
            $this->SetFont('Arial','B',8);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(15,4,date('Y', strtotime($this->datosComprobante['fecha_hora_documento'])),0,0,'C');
            $this->setX($posx+163);
            $this->Cell(15,4,date('m', strtotime($this->datosComprobante['fecha_hora_documento'])),0,0,'C');
            $this->setX($posx + 180);
            $this->Cell(15,4,date('d', strtotime($this->datosComprobante['fecha_hora_documento'])),0,0,'C');
            $this->ln(10);
            $this->SetFont('Arial','B',12);
            $this->setX($posx+147);
            $this->Cell(47,4,utf8_decode("NOTA CRÉDITO"),0,0,'C');

            /*****  Datos Cliente NC *****/
            $posy = $this->GetY()+10;
            $this->setXY($posx+5,$posy);
            $this->SetFont('Arial','B',7);
            $this->Cell(189,4,utf8_decode("DATOS DE CLIENTE"),0,0,'C');
            
            $posy = $this->GetY()+4;                
            $this->SetFillColor(255, 255, 255);
            $this->RoundedRect($posx+1,$posy, 194, 22, 2, '1234','F');
            $this->setXY($posx+7,$posy+1);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(20,4,"NOMBRE:",0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(150,4,utf8_decode(substr($this->datosComprobante['adquirente'],0,70)),0,0,'L');

            $this->Ln(6);
            $this->setX($posx+7);
            $this->SetFont('Arial','b',7);
            $this->Cell(20,4,utf8_decode("DIRECCIÓN:"),0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(150,4,utf8_decode(substr($this->datosComprobante['adq_dir'],0,70)),0,0,'L');
            $this->Ln(6);
            $this->setX($posx+7);
            $this->SetFont('Arial','B',7);
            $this->Cell(30,4,utf8_decode("RAZON NOTA CRÉDITO:"),0,0,'L');
            $this->SetFont('Arial','',7);
            $this->MultiCell(110,4,substr($this->datosComprobante['razon_nota_credito'],0,135),0,'L');

            $posx += 143;
            $this->setXY($posx,$posy+1);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(0, 0, 0);
            $this->Cell($offsetX, 4,utf8_decode(mb_strtoupper($tdo_descripcion)),0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(30,4,$this->datosComprobante['adq_nit'],0,0,'L');
            $this->Ln(6);
            $this->setX($posx);
            $this->SetFont('Arial','B',7);
            $this->Cell(23,4,"TELEFONO:",0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(30,4,$this->datosComprobante['adq_tel'],0,0,'L');
            $this->Ln(6);
            $this->setX($posx);
            $this->SetFont('Arial','B',7);
            $this->Cell(23,4,"FACTURA:",0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(30,4,$this->datosComprobante['numero_documento_ref'],0,0,'L');

            #### Cabecera de los Conceptos ####
            $posx = 11;
            $posy = $this->GetY()+11;
            $this->SetFillColor(128, 0, 0);
            $this->RoundedRect($posx, $posy, 194, 5, 1, '1234', 'F');

            $this->setXY($posx,$posy+1);
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(10,4,"ITEM",0,0,'C');
            $this->Cell(23,4,"COD. PRODUCTO",0,0,'C');
            $this->Cell(74.5,4,"DESCRIPCION",0,0,'C');
            $this->Cell(10,4,"UNIDAD",0,0,'C');
            $this->Cell(20,4,"CANTIDAD",0,0,'C');
            $this->Cell(30,4,"VLR. UNIT.",0,0,'C');
            $this->Cell(30,4,"VLR. COP",0,0,'C');

            $this->nPosYIni = $posy+7;
            ## Rectangulo que contiene los conceptos
            $posy = $this->GetY()+4.5;
            $this->SetFillColor(255, 255, 255);
            $this->Rect($posx,$posy,194,140,'F');
            ## Lineas separadoras de Conceptos
            $this->SetDrawColor(94,93,93);
            $this->SetLineWidth(0.1);
            $this->SetDash(1,1);
            $this->Line($posx+162,$posy,$posx+162,$posy+118);
            $this->SetLineWidth(0.2);
            $this->SetDash(0,0);
        }
    }

    function Footer() {
        $posx = $this->posx;
        
        if( $this->datosComprobante['cdo_tipo'] == "FC"){ 
            $posy = $this->posy+175;

            $this->SetLineWidth(0.2);
            $this->Line($posx+1.3,$posy,$posx+194.7,$posy);
            $this->Line($posx+126,$posy,$posx+126,$posy+35);
            $this->Line($posx+167,$posy,$posx+167,$posy+35);
            $this->Line($posx+1.3,$posy+35,$posx+194.7,$posy+35);

            $this->setXY($posx+2,$posy+1.5); 
            $this->SetFont('Arial','B',7);
            $this->Cell(25,4,"OBSERVACIONES: ",0,0,'L');
            $this->ln(4);
            $this->setX($posx+2); 
            $this->SetFont('Arial','',8);
            $this->MultiCell(123,4,utf8_decode(implode("\n",$this->datosComprobante['observacion']) . " " . $this->datosComprobante['observaciones']),0,'J');
            if($this->datosComprobante['anticipo_recibido'] > 0){
                $this->ln(1);
                $this->setX($posx+2);
                $this->SetFont('Arial','B',7);
                $this->Cell(123,4,"ANTICIPO:",0,0,'L');
                $this->SetFont('Arial','',7);
                $this->setX($posx+18); 
                $this->Cell(123,4,number_format($this->datosComprobante['anticipo_recibido'],0,',','.'),0,0,'L');
            }

            $this->setXY($posx+128,$posy); 
            $this->SetFont('Arial','B',7);
            $this->Cell(32,5,"SUBTOTAL ",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Cell(35,5,($this->datosComprobante['bandera_fondo'] == "SI") ? "" : $this->datosComprobante['subtotal'],0,0,'R');
            $this->ln(5);
            $this->setX($posx+128); 
            $this->SetFont('Arial','B',7);
            $this->Cell(32,5,"IVA 19%",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Cell(35,5,($this->datosComprobante['bandera_fondo'] == "SI") ? "" : $this->datosComprobante['iva'],0,0,'R');
            $this->ln(5);
            $this->setX($posx+128); 
            $this->SetFont('Arial','B',7);
            $this->Cell(32,5,"RETEIVA ",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Cell(35,5,($this->datosComprobante['bandera_fondo'] == "SI") ? "" : $this->datosComprobante['reteiva'],0,0,'R');
            $this->ln(5);
            $this->setX($posx+128); 
            $this->SetFont('Arial','B',7);
            $this->Cell(32,5,"RETEICA ",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Cell(35,5,($this->datosComprobante['bandera_fondo'] == "SI") ? "" : $this->datosComprobante['reteica'],0,0,'R');
            $this->ln(5);
            $this->setX($posx+128); 
            $this->SetFont('Arial','B',7);
            $this->Cell(32,5,"TOTAL ",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Cell(35,5,($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['total'],0,',','.'),0,0,'R');
            $this->ln(5);
            $this->setX($posx+128); 
            $this->SetFont('Arial','B',7);
            $this->Cell(32,5,"TOTAL A PAGAR ",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Cell(35,5,($this->datosComprobante['bandera_fondo'] == "SI") ? "" : $this->datosComprobante['total_pagar'],0,0,'R');
            $this->ln(5);
            $this->setX($posx+128); 
            $this->SetFont('Arial','B',7);
            $this->Cell(32,5,"SALDO A SU FAVOR ",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Cell(35,5,($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['saldo_a_favor'],0,',','.'),0,0,'R');

            $this->setXY($posx+2,$posy+35.5); 
            $this->SetFont('Arial','B',7);
            $this->SetTextColor(255,0,0);
            $this->Cell(25,4,utf8_decode($this->datosComprobante['titulo']),0,0,'L');
            $this->SetTextColor(0,0,0);
            $this->ln(4);
            $this->setX($posx+2); 
            $this->SetFont('Arial','',8);
            $this->MultiCell(190,3.5,utf8_decode($this->datosComprobante['contenido']),0,'J');

        }elseif($this->datosComprobante['bandera_fondo'] == "SI" && $this->datosComprobante['cdo_tipo'] == "NC"){
            $posy = $this->posy+195;
            $this->SetDrawColor(94,93,93);

            $this->setXY($posx+117,$posy);
            $this->SetFont('Arial','B',7);
            $this->SetFillColor(206, 210, 225);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(78,5,"",1,0,'L',true);
            $this->ln(5);
            
            ## SubTotal
            $this->setX($posx+122);
            $this->SetFont('Arial','B',8);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(40,5,"SubTotal",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->SetFillColor(250, 196, 196);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(33,5,"",1,0,'R',true);
            $this->ln(5);
            
            ## IVA
            $this->setX($posx+122);
            $this->SetFont('Arial','B',8);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(40,5,"IVA",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->SetFillColor(206, 210, 225);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(33,5,"",1,0,'R',true);
            $this->ln(5);

            ## VALOR TOTAL DE
            $this->setX($posx+117);
            $this->SetFont('Arial','B',7);
            $this->SetFillColor(206, 210, 225);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(45,5,"VALOR TOTAL DE",1,0,'C',true);
            $this->SetFont('Arial','',8);
            $this->SetFillColor(250, 196, 196);            
            $this->SetTextColor(0, 0, 0);
            $this->Cell(33,5,"",1,0,'R',true);            
        }

        if($this->datosComprobante['cdo_tipo'] == "FC"){
            $posy = 242;
            $this->SetLineWidth(0.2);
        }elseif($this->datosComprobante['cdo_tipo'] == "NC"){
            $this->SetDrawColor(0,0,0);
            $posy = $this->GetY()+15;
        }

        ## Código QR y Firma
        if($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != ""){
            $dataURI = "data:image/png;base64, ".base64_encode((string) \QrCode::format('png')->size(85)->margin(0)->generate($this->datosComprobante['qr']));
            $pic = $this->getImage($dataURI);
            if ($pic!==false) $this->Image($pic[0], $posx+2,$posy+0.5,30,null, $pic[1]);

            $this->setXY($posx+50,$posy);
            $this->SetFont('Arial','B',7);
            $this->Cell(60,3,utf8_decode("REPRESENTACIÓN GRAFICA DE LA FACTURA"),0,0,'C');
            $this->Ln(4);
            $this->setX($posx+33);
            $this->SetFont('Arial','',5.5);
            $this->Cell(30,3,utf8_decode("Firma Electrónica:"),0,0,'L');
            $this->Ln(3);
            $this->setX($posx+33);
            $this->SetFont('Arial','',5.5);
            $this->MultiCell(90,2.5,$this->datosComprobante['signaturevalue'],0,'J');
            $this->Ln(1);
            $this->setX($posx+33);
            $this->SetFont('Arial','B',6);
            if ($this->datosComprobante['cdo_tipo'] == "FC") {
                $this->Cell(7, 3, utf8_decode("CUFE:"), 0, 0, 'L');
            } else {
                $this->Cell(7, 3, utf8_decode("CUDE:"), 0, 0, 'L');
            }
            $this->SetFont('Arial','',6);
            $this->MultiCell(90,3,utf8_decode($this->datosComprobante['cufe']),0,'L');
            $this->Ln(1);
            $this->setX($posx+33);
            $this->SetFont('Arial','B',6);
            $this->Cell(41,3,utf8_decode("FECHA Y HORA DE VALIDACIÓN DIAN: "),0,0,'L');
            $this->SetFont('Arial','',6);
            $this->Cell(30,3,substr($this->datosComprobante['fecha_validacion'],0,16),0,0,'L');
        }

        ## Contenedor Contáctenos
        $this->SetFillColor(255, 255, 255);            
        $this->Rect($posx+130, $posy+2,65,28, 'DF');
        $this->SetFont('Arial','B',9);
        $this->setXY($posx+132.5,$posy+6);
        $this->Cell(65,3,utf8_decode("¿Tiene dudas sobre este documento?"),0,0,'L');
        $this->SetFont('Arial','B',7);
        $this->Ln(7);
        foreach ($this->datosComprobante['contactenos'] as $key => $srtContactenos) {
            if(trim($srtContactenos) != ""){
                if ($key == 3)
                    $this->SetFont('Arial','U',7);

                $this->setX($posx+130);
                $this->Cell(50,3,utf8_decode(trim($srtContactenos)),0,0,'L');
                $this->Ln(4);
            }
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

    function SetDash($black=false, $white=false) {
        if($black and $white)
            $s=sprintf('[%.3f %.3f] 0 d', $black*$this->k, $white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
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