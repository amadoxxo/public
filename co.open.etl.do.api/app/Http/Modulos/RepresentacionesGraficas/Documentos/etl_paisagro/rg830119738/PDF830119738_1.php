<?php 
/**
 * Created by PhpStorm.
 * User: Juan Jose Trujillo Ch.
 * Date: 20/11/19
 * Time: 11:30 AM
 */

namespace App\Http\Modulos\RepresentacionesGraficas\Documentos\etl_paisagro\rg830119738;

use App\Http\Modulos\RepresentacionesGraficas\Core\PDFBase;

class PDF830119738_1 extends PDFBase{

	function Header() {

        if($this->datosComprobante['signaturevalue'] == '' && $this->datosComprobante['qr'] ==''){
            $this->Image($this->datosComprobante['no_valido'], 20, 50, 180, 180);
        }

        $this->SetFont('Arial','',6);
        $this->TextWithDirection(209,70,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): ".$this->datosComprobante['razon_social_pt']." NIT: ".$this->datosComprobante['nit_pt']." NOMBRE DEL SOFTWARE: ".$this->datosComprobante['nombre_software']),'D');

        if($this->datosComprobante['cdo_tipo'] == "FC"){

            // Logo marca de agua
            $this->Image($this->datosComprobante['marca_agua'], 60, 110, 50);

            $posx = 10;
            $posy = 8;

            $this->posx = $posx;
            $this->posy = $posy;

            $this->Image($this->imageHeader, $posx, $posy-2, 100);

            //Datos OFE 
            $this->SetFont('Arial','',8);
            $this->setXY($posx-1,$posy);  
            $this->TextWithDirection(6,235,utf8_decode($this->datosComprobante['resolucion']),'U');
            $this->setXY($posx+80,$posy+2);     
            $this->SetFont('Arial','B',7);       
            $this->Cell(85,5, "NIT: " . number_format($this->datosComprobante['ofe_identificacion'],0,'.','.') . "-" . $this->datosComprobante['ofe_dv'],0,0,"C");
            $this->Ln(3);
            $this->setX($posx+80);
            $this->Cell(85,5, $this->datosComprobante['impuesto_iva'],0,0,"C");
            $this->Ln(4);
            $this->setX($posx+102);
            $this->MultiCell(40, 3.5, $this->datosComprobante['actividad_economica'],0,"C");
            $this->Ln(4);
            foreach ($this->datosComprobante['cuatro_x_mil'] as $strCuatro_x_mil) {
                $this->setX($posx+110);
                $this->Cell(40,5,utf8_decode($strCuatro_x_mil),0,0,'L');
                $this->Ln(3);
            }

            $this->Ln(1);
            $this->setX($posx+110);
            $this->Cell(40,5,utf8_decode($this->datosComprobante['ofe_web']),0,0,'L');
            $this->Ln(3);
            $this->setX($posx+110);
            $this->Cell(40,5,utf8_decode($this->datosComprobante['ofe_correo']),0,0,'L');

            $this->SetFont('Arial','B',6);
            $this->setXY($posx+10,$posy+18);
            $this->Cell(85,5,utf8_decode($this->datosComprobante['texto_cabecera_1']),0,0,'C');
            $this->Ln(3);
            $this->setX($posx+10);
            $this->Cell(85,5, utf8_decode($this->datosComprobante['ofe_dir'] . " " . $this->datosComprobante['pbx'] . " TEL " . $this->datosComprobante['ofe_tel'] . " " . $this->datosComprobante['ofe_mun']),0,0,'C');
            $this->Ln(3);
            $this->setX($posx+10);
            $this->Cell(85,5,utf8_decode($this->datosComprobante['direccion_2']),0,0,'C');
            $this->Ln(3);
            $this->setX($posx+10);
            $this->Cell(85,5,utf8_decode($this->datosComprobante['ofe_pais']),0,0,'C');

            // Datos Factura
            $this->setXY($posx+148,$posy+7);
            $this->SetFont('Arial','B',10);
            $this->MultiCell(51,3.5, utf8_decode("FACTURA ELECTRÓNICA DE VENTA"),0,'C');
            $this->Ln(5);
            $this->setX($posx+151);
            $this->SetFont('Arial','',10);
            $this->Cell(45,7,utf8_decode("No. ").$this->datosComprobante['numero_documento'],1,0,'C');
            $this->Ln(7);
                
            // Datos Cliente
            // Primera Fila
            $posy += 38;
            $posx = 8;
            
            $this->SetFont('Arial','',7);
            $this->setXY($posx,$posy+1);
            $this->Cell(20,2, utf8_decode("SEÑORES"),0,0,'L');
            $this->SetFont('Arial','',8);
            $this->setXY($posx,$posy+4);
            $this->MultiCell(56,3.5, utf8_decode($this->datosComprobante['adquirente']),0,'L');

            $this->SetFont('Arial','',7);
            $this->setXY($posx+85,$posy+1);
            $this->Cell(20,2, utf8_decode("NIT"),0,0,'L');
            $this->SetFont('Arial','',8);
            $this->setXY($posx+85,$posy+4);
            $this->MultiCell(70,3, number_format($this->datosComprobante['adq_nit'], 0, '', '.').'-'.$this->datosComprobante['adq_nit_consecutivo'],0,'L');

            $this->setXY($posx+127,$posy+1);
            $this->SetFont('Arial','',7);
            $this->Cell(20,2, utf8_decode("VENDEDOR"),0,0,'L');
            $this->setXY($posx+127,$posy+4);
            $this->SetFont('Arial','',8);
            $this->MultiCell(40,3.5, utf8_decode($this->datosComprobante['vendedor']),0,'L');

            $this->SetFont('Arial','',7);
            $this->setXY($posx+175,$posy+1);
            $this->Cell(20,2, utf8_decode("FECHA DE FACTURA"),0,0,'C');
            $this->Line($posx+169,$this->GetY()+3,$posx+200,$this->GetY()+3);
            $this->setXY($posx+167,$posy+4.5);
            $this->SetFont('Arial','',6);
            $this->Cell(10,2,"DIA",0,0,'C');
            $this->Cell(10,2,"MES",0,0,'C');
            $this->Cell(10,2, utf8_decode("AÑO"),0,0,'C');
            $this->setXY($posx+170,$posy+8);
            $this->SetFont('Arial','',9);
            $this->Cell(70,4, utf8_decode($this->datosComprobante['fecha_hora_documento']),0,0,'L');
            $this->Ln(4);

            $pyyfin = $this->GetY();
            $this->RoundedRect($posx, $posy, 85, 15, 1);
            $this->RoundedRect($posx+85, $posy, 42, 15, 1);
            $this->RoundedRect($posx+127, $posy, 42, 15, 1);
            $this->Line($posx+177,$posy+4,$posx+177, $posy+15);
            $this->Line($posx+188,$posy+4,$posx+188, $posy+15);
            $this->RoundedRect($posx+169, $posy, 31, 15, 1);

            //Segunda Fila
            $posy = $pyyfin+3;
            $posx = 8;
            
            $this->setXY($posx,$posy+1);
            $this->SetFont('Arial','',7);
            $this->Cell(20,2,"DIRECCION Y CIUDAD",0,0,'L');
            $this->setXY($posx,$posy+4);
            $this->SetFont('Arial','',8);
            $this->MultiCell(55,3.5, utf8_decode($this->datosComprobante['adq_dir']),0,'L');
            $this->setXY($posx+55,$posy+4);            
            $this->SetFont('Arial','',8);
            $this->MultiCell(30,3, utf8_decode($this->datosComprobante['adq_mun']),0,'L');

            $this->setXY($posx+85,$posy+1);
            $this->SetFont('Arial','',7);
            $this->Cell(20,2, utf8_decode("TELÉFONO"),0,0,'L');
            $this->SetFont('Arial','',8);
            $this->setXY($posx+85,$posy+4);
            $this->MultiCell(70,3, utf8_decode($this->datosComprobante['adq_tel']),0,'L');

            $this->setXY($posx+127,$posy+1);
            $this->SetFont('Arial','',7);
            $this->Cell(20,2, utf8_decode("PLAZO DE PAGO"),0,0,'L');
            $this->SetFont('Arial','',8);
            $this->setXY($posx+127,$posy+4);
            $this->MultiCell(70,4, utf8_decode($this->datosComprobante['diferencia']),0,'L');

            $this->setXY($posx+175,$posy+1);
            $this->SetFont('Arial','',7);
            $this->Cell(20,2,utf8_decode("HORA FACTURA"),0,0,'C');
            $this->Line($posx+169,$this->GetY()+3,$posx+200,$this->GetY()+3);
            $this->setXY($posx+177,$posy+8);
            $this->SetFont('Arial','',9);
            $this->Cell(70,4, $this->datosComprobante['hora_documento'],0,0,'L');
            $this->Ln(4);

            $pyyfin = $this->GetY();
            $this->RoundedRect($posx, $posy, 85, 14, 1);
            $this->RoundedRect($posx+85, $posy, 42, 14, 1);
            $this->RoundedRect($posx+127, $posy, 42, 14, 1);
            $this->RoundedRect($posx+169, $posy, 31, 14, 1);

            //Tercera Fila
            $posy = $pyyfin+2;
            $posx = 8;
            
            $this->setXY($posx,$posy+1);
            $this->SetFont('Arial','',7);
            $this->Cell(20,2,"FORMA DE PAGO",0,0,'L');
            $this->setXY($posx,$posy+4);
            $this->SetFont('Arial','',8);
            $this->MultiCell(55,3.5,utf8_decode($this->datosComprobante['forma_pago']),0,'L');

            $this->setXY($posx+85,$posy+1);
            $this->SetFont('Arial','',7);
            $this->Cell(20,2,utf8_decode("MEDIO DE PAGO"),0,0,'L');
            $this->SetFont('Arial','',8);
            $this->setXY($posx+85,$posy+4);
            $this->MultiCell(70,3,utf8_decode($this->datosComprobante['medio_pago']),0,'L');

            $this->setXY($posx+175,$posy+1);
            $this->SetFont('Arial','',7);
            $this->Cell(20,2,utf8_decode("FECHA VENCIMIENTO"),0,0,'C');
            $this->Line($posx+169,$this->GetY()+3,$posx+200,$this->GetY()+3);
            $this->setXY($posx+167,$posy+4.5);
            $this->SetFont('Arial','',6);
            $this->Cell(10,2,"DIA",0,0,'C');
            $this->Cell(10,2,"MES",0,0,'C');
            $this->Cell(10,2,utf8_decode("AÑO"),0,0,'C');
            $this->setXY($posx+170,$posy+8);
            $this->SetFont('Arial','',9);
            $this->Cell(70,4,utf8_decode($this->datosComprobante['fecha_vencimiento']),0,0,'L');
            $this->Ln(4);

            $this->RoundedRect($posx, $posy, 85, 12, 1);
            $this->RoundedRect($posx+85, $posy, 84, 12, 1);
            $this->Line($posx+177,$posy+4,$posx+177,$posy+12);           	
            $this->Line($posx+188,$posy+4,$posx+188,$posy+12);            
            $this->RoundedRect($posx+169, $posy, 31, 12, 1);

            //Inicializo las posiciones xy para dibujar los titulos del detalle factura
            $posx = 8;
            $posy =  $this->GetY();
            $this->SetFont('Arial','I',7);
            $this->setFillColor(220,220,220); 
            $this->setXY($posx,$posy);
            $this->Cell(10,6,"ITEM",0,0,'C',true);
            $this->Cell(21,6,"CODIGO",0,0,'C',true);
            $this->Cell(46,6,"PRODUCTOS",0,0,'C',true);
            $this->Cell(10,6,"UNIDAD",0,0,'C',true);
            $this->Cell(10,6,"LOTE",0,0,'R',true);
            $this->Cell(10,6,"IVA",0,0,'C',true);
            $this->SetFont('Arial','I',6);
            $this->Cell(11,6,"CANTIDAD",0,0,'C',true);
            $this->SetFont('Arial','I',7);
            $this->Cell(25,6,utf8_decode("PRECIO PÚBLICO"),0,0,'C',true);
            $this->Cell(12,6,"DCTO.",0,0,'C',true);
            $this->Cell(24,6,"VALOR UNITARIO",0,0,'C',true);
            $this->Cell(22,6,"VALOR TOTAL",0,0,'C',true);
            $this->ln(6);
            $this->Line($posx,$this->GetY(),$posx+200,$this->GetY());

            $this->nPosYFin = $posy+8;

            $this->RoundedRect($posx, $posy, 200, 107, 1);
            $this->Line($posx+10,$posy,$posx+10,$posy+107);
            $this->Line($posx+31,$posy,$posx+31,$posy+107);
            $this->Line($posx+76,$posy,$posx+76,$posy+107);
            $this->Line($posx+88.5,$posy,$posx+88.5,$posy+107);
            $this->Line($posx+98,$posy,$posx+98,$posy+107);
            $this->Line($posx+105,$posy,$posx+105,$posy+107);
            $this->Line($posx+119,$posy,$posx+119,$posy+107);
            $this->Line($posx+142,$posy,$posx+142,$posy+107);
            $this->Line($posx+153,$posy,$posx+153,$posy+107);
            $this->Line($posx+178,$posy,$posx+178,$posy+107);

        }else if ($this->datosComprobante['cdo_tipo'] == "NC") {
            $posx = 10;
            $posy = 5;

            $this->posx = $posx;
            $this->posy = $posy;

            $this->Image($this->datosComprobante['logonc'], $posx+8, $posy, 25);

            // Datos OFE
            $this->SetFont('Arial','',9);
            $this->setXY($posx+10,$posy+15);
            $this->Cell(130,5, utf8_decode($this->datosComprobante['razon_social']),0,0,'C');
            $this->Ln(5);
            $this->setX($posx+10);
            $this->Cell(110,5, $this->datosComprobante['ofe_dir'],0,0,'C');
            $this->setX($posx+10);
            $this->Cell(185,5, utf8_decode($this->datosComprobante['ofe_tel']),0,0,'C');
            $this->Ln(5);
            $this->setX($posx+10);
            $this->Cell(117,5, number_format($this->datosComprobante['ofe_identificacion'], 0, ',', '.'). "-" .$this->datosComprobante['ofe_dv'],0,0,'C');
            $this->setX($posx+10);
            $this->Cell(170,5, utf8_decode($this->datosComprobante['ofe_mun']),0,0,'C');
            $this->setX($posx+72);
            $this->Cell(117,5, utf8_decode($this->datosComprobante['ofe_web']),0,0,'C');
            $this->Line($posx,$this->GetY()+8,$posx+190,$this->GetY()+8);

            $this->SetFont('Arial','',8);
            $this->setXY($posx+125, $posy+15);
            $this->Cell(85,5, $this->datosComprobante['impuesto_iva'],0,0,"C");
            $this->Ln(4);
            $this->setX($posx+147);
            $this->MultiCell(40, 3.5, $this->datosComprobante['actividad_economica'],0,"C");
            $this->Ln(4);
            
            // Datos Cliente
            $posy = $this->GetY()+10;
            $this->setXY($posx+3,$posy);
            $this->SetFont('Arial','',8);
            $this->Cell(27,10, utf8_decode("SEÑORES"),0,0,'L');
            $this->Ln(7);
            $this->setX($posx+3);
            $this->SetFont('Arial','',8);
            $this->MultiCell(68,4, utf8_decode($this->datosComprobante['adquirente']),0,'L');
            $this->setX($posx+3);
            $this->SetFont('Arial','',8);
            $this->MultiCell(50,4, utf8_decode($this->datosComprobante['adq_dir']),0,'L');
       		$pyfin = $this->GetY();

            $this->setXY($posx+74,$posy);
            $this->SetFont('Arial','',8);
            $this->Cell(27,10, utf8_decode("NIT."),0,0,'L');
            $this->Ln(4);
            $this->setX($posx+74);
            $this->SetFont('Arial','',8);
            $this->Cell(25,10, $this->datosComprobante['adq_nit'],0,0,'L');
            $this->Ln(7);
            $this->setX($posx+74);
            $this->SetFont('Arial','',8);
            $this->MultiCell(45,4,"Vendedor : ". utf8_decode($this->datosComprobante['vendedor']),0,'L');
       		$pyyfin = $this->GetY();

            $this->setXY($posx+147,$posy);
            $this->SetFont('Arial','',8);
            $this->Cell(23,10,utf8_decode("NOTA CREDITO"),0,0,'L');
            $this->SetFont('Arial','',7);
            $this->Cell(20,10,$this->datosComprobante['numero_documento'],0,0,'L');
            $this->Line($posx+147,$this->GetY()+7,$posx+190,$this->GetY()+7);
            
            $this->Ln(4);
            $this->setX($posx+158);
            $this->SetFont('Arial','',8);
            $this->Cell(20,10,"FECHA  :   ".$this->datosComprobante['fecha_hora_documento'],0,0,'L');
            $this->Ln(4);
            $this->setX($posx+133);
            $this->SetFont('Arial','',8);
            $this->Cell(15,10,"Concepto : ",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Ln(4);
            $this->setX($posx+150);
            $concepto = mb_convert_encoding($this->datosComprobante['cdo_conceptos_correccion'][0]->cdo_observacion_correccion, 'ISO-8859-1', 'UTF-8');
            $this->MultiCell(37,3,$this->datosComprobante['cdo_conceptos_correccion'][0]->cco_codigo." ".$concepto,0,'L');
            
            if($pyfin > $pyyfin){
                $pyyfin = $pyfin;
            }

            $posy = $pyyfin + 5;
            $this->setXY($posx+3,$posy);
            $this->SetFont('Arial','',8);
            $this->Cell(63,6, utf8_decode("FACTURA REFERENCIA"),0,0,'L');
            $this->Cell(63,6, utf8_decode("FECHA FACTURA REFERENCIA"),0,0,'L');
            $this->Cell(63,6, utf8_decode("CUFE FACTURA REFERENCIA"),0,0,'L');
            $this->Ln(5);
            $this->setX($posx);
            $this->SetFont('Arial','',7);
            $this->Cell(30,6, $this->datosComprobante['consecutivo_ref'],0,0,'C');
            $this->setX($posx+72);
            $this->Cell(30,6, $this->datosComprobante['fecha_emision'],0,0,'C');
            $this->SetFont('Arial','',6);
            $this->Ln(2);
            $this->setX($posx+122);
            $this->MultiCell(60, 3, $this->datosComprobante['cufe_ref'],0,'L');
            
            $pyyfin = $this->getY();
            $this->Line($posx,$pyyfin+2,$posx+190,$pyyfin+2);

            //Inicializo las posiciones xy para dibujar los titulos del detalle factura
            $posy = $pyyfin+2;
            $posx = 7;
            $this->SetFont('Arial','I',7);
            $this->setXY($posx+4,$posy);
            $this->Cell(10,6,"ITEM",0,0,'L');
            $this->Cell(10,6,"CANTIDAD",0,0,'C');
            $this->Cell(20,6,"CODIGO",0,0,'C');
            $this->Cell(65,6,"DESCRIPCION",0,0,'L');
            $this->Cell(20,6,"UNIDAD",0,0,'L');
            $this->Cell(10,6,"DTO",0,0,'C');
            $this->Cell(25,6,"VALOR UNIDAD",0,0,'C');
            $this->Cell(25,6,"T O T A L.",0,0,'L');
            $this->ln(6);
            $this->Line($posx+3,$this->GetY(),$posx+193,$this->GetY());

            $this->nPosYFin = $posy;
        }
    }

	function Footer() {
        
        if($this->datosComprobante['cdo_tipo'] == "FC"){
            $posx = $this->posx - 2;
            $posy = $this->posy + 186;

            $this->setXY($posx,$posy);
            $this->SetFont('Arial','B',7);
            $this->Cell(30,4,"OBSERVACIONES:",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Cell(40,5, $this->datosComprobante['base_1']."   ",0,0,'L');
            $this->SetFont('Arial','',8);
            $this->Cell(20,5, $this->datosComprobante['base_2']."   ",0,0,'L');

            $this->setXY($posx,$posy+5);
            $this->SetFont('Arial','',7);
            $this->MultiCell(155, 3.5, utf8_decode(implode("\n", $this->datosComprobante['observacion_decode'])), 0, 'L');

            $this->setXY($posx,$posy+18);
            $this->SetFont('Arial','',6);
            $this->Cell(30,5, utf8_decode($this->datosComprobante['nota_final_1']),0,0,'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->Cell(70,5, utf8_decode($this->datosComprobante['nota_final_2']),0,0,'L');
            $this->Ln(4);
            $this->setX($posx);
            $this->MultiCell(90,3.5, utf8_decode($this->datosComprobante['nota_final_3']),0,'L');
           
            $this->setXY($posx,$posy+36);
            foreach ($this->datosComprobante['consignar'] as $strConsignar) {
                $this->setX($posx+1);
                $this->SetFont('Arial','B',6);
                $this->Cell(50,4,$strConsignar,0,0,'L');
                $this->Ln(3);
            }

            /*** Totales ***/
            $this->SetFont('Arial','B',7);
            $this->setXY($posx+144,$posy);
            $this->Cell(26,5,"SUBTOTAL",0,0,'L');
            $this->Ln(5);
            $this->setX($posx+144);
            $this->Cell(26,5,"IVA 5%",0,0,'L');
            $this->Ln(5);
            $this->setX($posx+144);
            $this->Cell(26,5,"IVA 19%",0,0,'L');
            $this->Ln(5);
            $this->setX($posx+144);
            $this->Cell(26,5,"RETENCION IVA",0,0,'L');
            $this->Ln(5);
            $this->setX($posx+144);
            $this->Cell(26,5,"RETENCION ICA",0,0,'L');
            $this->Ln(5);
            $this->SetFont('Arial','B',6);
            $this->setX($posx+144);
            $this->Cell(26,5,"RETENCION FUENTE",0,0,'L');
            $this->Ln(5);
            $this->SetFont('Arial','B',8);
            $this->setX($posx+144);
            $this->Cell(26,5,"TOTAL $",0,0,'C');
   
            ## Valores Totales
            $this->SetFillColor(220,220,220);
            $this->setXY($posx+169,$posy);
            $this->SetFont('Arial','',7);
            $this->Cell(31,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intSubtotal'], 0,'.',','),'L',0,'R',true);
            $this->Ln(5);

            $this->setX($posx+169);
            $this->Cell(31,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : $this->datosComprobante['nValorIva5FCCOP'],'T',0,'R');
            $this->Ln(5);

            $this->setX($posx+169);
            $this->Cell(31,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : $this->datosComprobante['nValorIva19FCCOP'],'T',0,'R',true);
            $this->Ln(5);

            $this->setX($posx+169);
            $this->Cell(31,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intValorReteiva'], 0, ',', '.'),1,0,'R');
            $this->Ln(5);

            $this->setX($posx+169);
            $this->Cell(31,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intValorReteica'], 0, ',', '.'),'T',0,'R',true);
            $this->Ln(5);

            $this->setX($posx+169);
            $this->Cell(31,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intValorRetefuente'], 0, ',', '.'),'T',0,'R');
            $this->Ln(5);

            $this->setX($posx+169);
            $this->Cell(31,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intTotalPagar'], 0,'.',','),'T',0,'R',true);
            $this->Ln(5);

            $this->Line($posx+169,$posy,$posx+169,$posy+35);
            $this->RoundedRect($posx, $posy, 200, 35, 1);
            $this->Line($posx,$posy+18,$posx+144,$posy+18);
            $this->Line($posx+144,$posy,$posx+144,$posy+35);
            
            //Datos QR y Firma
            $posx = 5;
            $posy = 252;

            $this->setXY($posx+5,$posy-3);
            $this->SetFont('Arial','',6);
            $this->Cell(50,3, utf8_decode("FECHA Y HORA VALIDACIÓN DIAN: ").$this->datosComprobante['validacion_dian'],0,0,'L');
            
            if($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != ""){
                $dataURI = "data:image/png;base64, ".base64_encode((string) \QrCode::format('png')->size(82)->margin(0)->generate($this->datosComprobante['qr']));
                $pic = $this->getImage($dataURI);
                if ($pic!==false) $this->Image($pic[0], $posx+167,$posy,0,0, $pic[1]);
                
                $this->setXY($posx+5,$posy);
                $this->SetFont('Arial','',8);
                $this->Cell(110,3,utf8_decode("Firma Electrónica:"),0,0,'L');
                $this->Ln(4);
                $this->setX($posx+5);
                $this->SetFont('Arial','',6);
                $this->MultiCell(140,4,$this->datosComprobante['signaturevalue'],0,'J');
                $this->Ln(1);
                $this->setXY($posx+160,$posy-17);
                $this->SetFont('Arial','B',7);
                $this->Cell(100,3,utf8_decode("CUFE:"),0,0,'L');
                $this->Ln(4);
                $this->setX($posx+160,$posy-13);
                $this->SetFont('Arial','',6);
                $this->MultiCell(45,3,utf8_decode($this->datosComprobante['cufe']),0,'L');

                //Paginacion
                $posy = 271;
                $this->SetFont('Arial','B',6);
                $this->setXY($posx,$posy);
                $this->Cell(150,4,$this->datosComprobante['pie_pagina'],0,0,'C');
                $this->SetFont('Arial','B',7);
                $this->setXY($posx,$posy);
                $this->Cell(270,4,'Pag '.$this->PageNo().'/{nb}',0,0,'C');
            }

            
        }else if($this->datosComprobante['cdo_tipo'] == "NC"){

            //Datos QR y Firma
            $posx = 14.5;
            $posy = 210;

            /*** Totales ***/
            $this->SetFont('Arial','B',7);
            $this->setXY($posx,$posy);
            $this->Cell(26,5,"SUBTOTAL: " ,0,0,'L');
            $this->setX($posx + 33);
            $this->Cell(26,5,"I.V.A: " ,0,0,'L');
            $this->setX($posx + 58);
            $this->Cell(26,5,"RETEFUENTE: " ,0,0,'L');
            $this->setX($posx + 94);
            $this->Cell(26,5,"RETEIVA: " ,0,0,'L');
            $this->setX($posx + 125);
            $this->Cell(26,5,"RETEICA: " ,0,0,'L');
            $this->setX($posx + 150);
            $this->Cell(26,5,"TOTAL: " ,0,0,'C');

            $this->SetFont('Arial','',7);
            $this->setXY($posx + 15, $posy);
            $this->Cell(26,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intSubtotal'], 0,'.',',') ,0,0,'L');
            $this->setX($posx + 41);
            $this->Cell(26,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intIva'], 0,'.',','),0,0,'L');
            $this->setX($posx + 76);
            $this->Cell(26,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intValorRetefuente'], 0,'.',','),0,0,'L');
            $this->setX($posx + 107);
            $this->Cell(26,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intValorReteiva'], 0,'.',','),0,0,'L');
            $this->setX($posx + 138);
            $this->Cell(26,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intValorReteica'], 0,'.',','),0,0,'L');
            $this->setX($posx + 161);
            $this->Cell(26,5, ($this->datosComprobante['bandera_fondo'] == "SI") ? "" : number_format($this->datosComprobante['intTotalPagar'], 0,'.',','),0,0,'C');
            /***Fin Totales ***/

            $posx -= 2.5;
            $posy += 20;

            if($this->datosComprobante['signaturevalue'] != "" && $this->datosComprobante['qr'] != ""){
                $dataURI = "data:image/png;base64, ".base64_encode((string) \QrCode::format('png')->size(85)->margin(0)->generate($this->datosComprobante['qr']));
                $pic = $this->getImage($dataURI);
                if ($pic!==false) $this->Image($pic[0], $posx+155,$posx+232,0,0, $pic[1]);
                
                $this->setXY($posx+5,$posy+15);
                $this->SetFont('Arial','',8);
                $this->Cell(110,3,utf8_decode("Firma Electrónica:"),0,0,'L');
                $this->Ln(4);
                $this->setX($posx+5);
                $this->SetFont('Arial','',6);
                $this->MultiCell(135,4,$this->datosComprobante['signaturevalue'],0,'J');
                $this->Ln(1);
                $this->setXY($posx+147,$posy-5);
                $this->SetFont('Arial','B',7);
                $this->Cell(100,3,utf8_decode("CUDE:"),0,0,'L');
                $this->Ln(4);
                $this->setX($posx+147,$posy-5);
                $this->SetFont('Arial','',6);
                $this->MultiCell(43,3,utf8_decode($this->datosComprobante['cufe']),0,'L');
            }

            //Paginacion
            $this->SetFont('Arial','B',7);
            $this->setXY($posx,$posy+38);
            $this->Cell(190,4,'Pag '.$this->PageNo().'/{nb}',0,0,'C');
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
