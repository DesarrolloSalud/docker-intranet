<?php 
    function getRealIP(){
        if (isset($_SERVER["HTTP_CLIENT_IP"])){
            return $_SERVER["HTTP_CLIENT_IP"];
        }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){
            return $_SERVER["HTTP_X_FORWARDED"];
        }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){
            return $_SERVER["HTTP_FORWARDED_FOR"];
        }elseif (isset($_SERVER["HTTP_FORWARDED"])){
            return $_SERVER["HTTP_FORWARDED"];
        }else{
            return $_SERVER["REMOTE_ADDR"];
        }
    }
    function obtenerFechaEnLetra($fecha){
        $dia= conocerDiaSemanaFecha($fecha);
        $num = date("j", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
        return /*$dia.', '.*/$num.' de '.$mes.' del '.$anno;
    }
    function conocerDiaSemanaFecha($fecha) {
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        $dia = $dias[date('w', strtotime($fecha))];
        return $dia;
    }
    session_start();
    if(!isset($_SESSION['USU_RUT'])){
        session_destroy();
        header("location: ../../index.php");
    }else{
        if(count($_GET) && !$_SERVER['HTTP_REFERER']){
           header("location: ../error.php");
        }
        $Srut = utf8_encode($_SESSION['USU_RUT']);
				require('../../include/fpdf/fpdf.php');
				//Conecto a la base de datos
				include ("../../include/funciones/funciones.php");
				$cnn = ConectarPersonal();
				$dcact_id = $_GET['id'];
        $consulta = "SELECT DECRE_ACT.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_CAT,USUARIO.USU_PROF,ESTABLECIMIENTO.EST_NOM,DECRE_ACT.DA_VISTO,DECRE_ACT.DA_CONSI,DECRE_ACT.DA_DEC,
				DECRE_ACT.DA_FEC,DECRE_ACT.DA_ALCALDE,DECRE_ACT.DA_SECRE,DECRE_ACT.DA_DERIVA FROM DECRE_ACT INNER JOIN USUARIO ON DECRE_ACT.USU_RUT=USUARIO.USU_RUT 
				LEFT JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (DA_DC_NUM= $dcact_id)";
        $respuesta = mysqli_query($cnn, $consulta);			
				/*echo $consulta;*/       
        $rowDC = mysqli_fetch_row($respuesta);
        $usu_rut       = $rowDC[0];
				$usu_nom       = $rowDC[1];
        $usu_app       = $rowDC[2];
        $usu_apm       = $rowDC[3];
        $usu_cat       = $rowDC[4];
				$usu_prof      = $rowDC[5];
        $usu_dep       = $rowDC[6];
				$dc_visto			 = $rowDC[7];
				$dc_consi			 = $rowDC[8];
				$dc_dec				 = $rowDC[9];
				$dc_fec				 = $rowDC[10];
				$alcalde				= $rowDC[11];
				$secretaria		=$rowDC[12];
				$distribucion = $rowDC[13];								
        class PDF extends FPDF{
						// Page header
						function Header(){
								// Logo
								$this->Image('../../include/img/header.jpg',1,1,210,20);
								$this->SetY(30);
								$this->SetX(100);
								$this->SetFont('Times','B',12);
								$dec_alc = utf8_encode("DECRETO ALCALDICIO N");
								global $dcact_id;
								$this->Write(5,utf8_encode($dec_alc.' :  __/'.$dcact_id.' (SALUD)'));   
								$this->Ln();
								$this->SetX(100);
								global $dc_fec;
								$fec_format = obtenerFechaEnLetra($dc_fec);
								$this->Write(5,'Rengo,'.$fec_format);
								$this->Ln(10);   
						}
						function Footer(){
								$this->Image('../../include/img/footer.jpg',3,305,205,20);
								// Go to 1.5 cm from bottom
								$this->SetY(-15);
								// Select Arial italic 8
								//$this->SetFont('Arial','I',8);
								// Print current and total page numbers
								$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
						}
				}
				//Instaciamos la clase para genrear el documento pdf
				$pdf = new PDF();
				$pdf->AddFont('GothamBooK','','GothamBook.php');
				$pdf->AddFont('GothamBold','','GOTHAM-BOLD.php');
				$pdf->SetLeftMargin(30);
				$pdf->SetRightMargin(20);
				//Agregamos la primera pagina al documento pdf
				$pdf->AddPage('P',array(215.9,330.2));
				//Seteamos el tiupo de letra y creamos el titulo de la pagina. No es un encabezado no se repetira
				$pdf->SetAutoPageBreak(auto,30);
				$pdf->SetTopMargin(20);
				$pdf->AliasNbPages();							               
        $pdf->Ln(10);
				$pdf->SetFont('Times','B',12);
        $pdf->Write(5,'VISTOS : ');
        $pdf->Ln(5);
				$pdf->SetFont('Times','',12);
				$text_inicio = utf8_decode($dc_visto);
				$text_inicio = "     ".$text_inicio;
				$txt_inicio = str_replace("<br />", "\n       ", $text_inicio);			          
				$txt_inicio_mostrar = str_replace("?", "*", $txt_inicio);
				nl2br($txt_inicio_mostrar);
				$pdf->MultiCell(165,5,$txt_inicio_mostrar,'J');
				$pdf->Ln(5);
				$pdf->SetFont('Times','B',12);
				$pdf->Write(5,'CONSIDERANDO : ');
				$pdf->Ln(5);
				$pdf->SetFont('Times','',12);
				$text_inicio = utf8_decode($dc_consi);
				$text_inicio = "     ".$text_inicio;
				$txt_inicio = str_replace("<br />", "\n       ", $text_inicio);			          
				$txt_inicio_mostrar = str_replace("?", "*", $txt_inicio);
				nl2br($txt_inicio_mostrar);
				$pdf->MultiCell(165,5,$txt_inicio_mostrar,'J');
				$pdf->Ln(5);
				$pdf->SetFont('Times','B',12);
				$pdf->Write(5,'DECRETO : ');
				$pdf->Ln(5);
				$pdf->SetFont('Times','',12);
				$text_inicio = utf8_decode($dc_dec);
				$text_inicio = "     ".$text_inicio;
				$txt_inicio = str_replace("<br />", "\n       ", $text_inicio);			          
				$txt_inicio_mostrar = str_replace("?", "*", $txt_inicio);
				nl2br($txt_inicio_mostrar);
				$pdf->MultiCell(165,5,$txt_inicio_mostrar,'J');
				$pdf->Ln(5);
				$Y = $pdf->GetY();								
				$DetalleMiOrden = "SELECT CARRERA_ACT.CA_ID, CARRERA_ACT.CA_DES, DATE_FORMAT(CARRERA_ACT.CA_FEC,'%Y-%m-%d'), CARRERA_ACT.CA_HORA, CARRERA_ACT.CA_NOTA, CARRERA_ACT.CA_NIVEL, CARRERA_ACT.CA_TOTAL, 
				DATE_FORMAT(CARRERA_ACT.CA_FEC_ING, '%Y-%m-%d') FROM DECRE_ACT_DETA INNER JOIN CARRERA_ACT ON DECRE_ACT_DETA.CA_ID=CARRERA_ACT.CA_ID WHERE (DA_DC_NUM = '$dcact_id') ORDER BY CA_FEC";
        $RespMiOrden = mysqli_query($cnn,$DetalleMiOrden);                
        if($Y>280){												
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->AliasNbPages();	
						$pdf->Ln(5);	
						$pdf->SetFillColor(232,232,232);
						$pdf->SetFont('Times','B',10);
						$pdf->Cell(65,6,'Nombre Actividad',1,0,'C',1);
						$pdf->Cell(20,6,'Fecha Act.',1,0,'C',1);                
						$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
						$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
						$pdf->cell(25,6,utf8_decode('Nivel Técnico'),1,0,'C',1);
						$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
						$pdf->Cell(20,6,'Fecha Ing.',1,0,'C',1);
						$Y= 41;										
						$cta=0;
				}else{
						$pdf->SetFillColor(232,232,232);
						$pdf->SetFont('Times','B',10);
						$pdf->Cell(65,6,'Nombre Actividad',1,0,'C',1);
						$pdf->Cell(20,6,'Fecha Act.',1,0,'C',1);                
						$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
						$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
						$pdf->cell(25,6,utf8_decode('Nivel Técnico'),1,0,'C',1);
						$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
						$pdf->Cell(20,6,'Fecha Ing.',1,0,'C',1);
						$conta=155;
						$contax=146;             
						$cta =0;
						$Y =$Y+6;
				}
        $cont1=0;
      	$cuentawhile=0;              
        while ($row = mysqli_fetch_array($RespMiOrden)){
        		$cuentawhile = $cuentawhile +1;
            $pdf->SetFont('Times','',10);
						$pdf->SetXY(30,$Y);
						$pdf->MultiCell(65,6,$row[1],1,'L');
						$H = $pdf->GetY();
						$height = $H - $Y;
						$pdf->SetXY(95,$Y);
						$pdf->Cell(20,$height,utf8_decode($row[2]),1,'C');
						$pdf->SetXY(115,$Y);
						$pdf->Cell(10,$height,$row[3],1,'R');
						$pdf->SetXY(125,$Y);
						$pdf->Cell(10,$height,$row[4],1,'R');
						$pdf->SetXY(135,$Y);
						$pdf->Cell(25,$height,$row[5],1,'R');
						$pdf->SetXY(160,$Y);
						$pdf->Cell(20,$height,$row[6],1,'R');
						$pdf->SetXY(180,$Y);
						$pdf->Cell(20,$height,$row[7],1,'R');
						$nombre = $row[1];
						$fecha_act =$row[2];
						$hrs = $row[3];
						$eval = $row[4];
						$nt =$row[5];
						$po = $row[6];
						$fi = $row[7];
						$suma = $suma+ $row[6];
						$Y = $H;  
						$NY = $pdf->GetY();
						if($cont1 == 1){
								$E = $pdf->GetY();
								//echo $E;
								//break 1;
						}else{
								$cont1=$cont1+1;
						}
						if($NY>245){												
								$pdf->AddPage('P',array(215.9,330.2));	
								$pdf->SetTopMargin(20);
								$pdf->AliasNbPages();
								$pdf->Ln(10);	/*
								$pdf->SetFillColor(232,232,232);
								$pdf->SetFont('Times','B',10);
								$pdf->Cell(65,6,'Nombre Actividad',1,0,'C',1);
								$pdf->Cell(20,6,'Fecha Act.',1,0,'C',1);                
								$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
								$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
								$pdf->cell(25,6,utf8_decode('Nivel Técnico'),1,0,'C',1);
								$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
								$pdf->Cell(20,6,'Fecha Ing.',1,0,'C',1);*/
								$Y= 41;
								$cta=0;
								$SEGUN =0;
                $cuentawhil2 = $cuentawhile;
						}else{
								$SEGUN=1;
						}								      
				}
				$NY = $pdf->GetY();
				if($NY>280){
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->AliasNbPages();
						$pdf->SetFont('GothamBold');
						$pdf->Header();
						$pdf->SetX(115);
						$pdf->Cell(210,5,utf8_decode('DECRETO ALCALDICIO N°: ').$dcact_id,0,1);
						$pdf->SetX(115);
						$pdf->Cell(197,5,utf8_decode('RENGO, ').$fecha,0,1); 
						$pdf->Ln(5);	
						$pdf->SetFillColor(232,232,232);
						$pdf->SetFont('Arial','B',10);
						$pdf->Cell(75,6,'Nombre Actividad',1,0,'C',1);
						$pdf->Cell(25,6,'Fecha Act.',1,0,'C',1);                
						$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
						$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
						$pdf->cell(25,6,utf8_decode('Nivel Técnico1'),1,0,'C',1);
						$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
						$pdf->Cell(25,6,'Fecha Ing.',1,0,'C',1);
						$Y= 41;
						$cta=0;
						$pdf->SetFont('GothamBold');
						$pdf->SetXY(10,$Y);
						$pdf->MultiCell(75,6,$row[1],1,'L');
						$H = $pdf->GetY();
						$height = $H - $Y;
						$pdf->SetXY(85,$Y);
						$pdf->Cell(25,$height,utf8_decode($row[2]),1,'C');
						$pdf->SetXY(110,$Y);
						$pdf->Cell(20,$height,$row[3],1,'R');
						$pdf->SetXY(120,$Y);
						$pdf->Cell(35,$height,$row[4],1,'R');
						$pdf->SetXY(130,$Y);
						$pdf->Cell(25,$height,$row[5],1,'R');
						$pdf->SetXY(155,$Y);
						$pdf->Cell(20,$height,$row[6],1,'R');
						$pdf->SetXY(175,$Y);
						$pdf->Cell(25,$height,$row[7],1,'R');
						$pdf->SetFont('GothamBold');
						$pdf->SetXY(10,$Y);
						$pdf->Cell(75,6,'TOTAL PUNTOS',1,'L');
						$pdf->SetXY(155,$Y);
						$pdf->Cell(20,$height,$suma,1,'R');
						$pdf->Ln(15);
						$pdf->SetX(40);
						$pdf->SetFont('GothamBook');
						$pdf->Write(5,utf8_decode('Anotése, Comuníquese, Regístre en su hoja de carrera funcionaria y archivase.'));
						$pdf->Ln(30);
						//$pdf->SetX(140);
						$pdf->SetFont('GothamBoold');
					  $pdf->Cell(170,5,$alcalde,0,1,'R');
						$pdf->SetX(145);
            $pdf->Write(5,utf8_decode('ALCALDE'));
						$pdf->Ln(20);
						$pdf->Cell(75,5,$secretaria,0,1,'R');
           	$pdf->SetX(32);
            $pdf->Write(5,utf8_decode('SECRETARIA MUNICIPAL'));
						$pdf->Ln(20);
						$pdf->Cell(47,5,$distribucion,0,1,'R');
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->AliasNbPages();
						$pdf->SetFont('GothamBold');
						$pdf->Header();
						$pdf->SetX(115);
						$pdf->Cell(210,5,utf8_decode('DECRETO ALCALDICIO N°: ').$dcact_id,0,1);
						$pdf->SetX(115);
						$pdf->Cell(197,5,utf8_decode('RENGO, ').$fecha,0,1); 
						$pdf->Ln(5);	
		        $pdf->SetFillColor(232,232,232);
						$pdf->SetFont('Arial','B',10);
						$pdf->Cell(75,6,'Nombre Actividad',1,0,'C',1);
						$pdf->Cell(25,6,'Fecha Act.',1,0,'C',1);                
						$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
						$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
						$pdf->cell(25,6,utf8_decode('Nivel Técnico'),1,0,'C',1);
						$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
						$pdf->Cell(25,6,'Fecha Ing.',1,0,'C',1);
						//$Y= 41;
						$cta=0;
						$pdf->SetFont('GothamBold');
						$pdf->SetXY(10,$Y);
						$pdf->MultiCell(75,6,$row[1],1,'L');
						$H = $pdf->GetY();
						$height = $H - $Y;
						$pdf->SetXY(85,$Y);
						$pdf->Cell(25,$height,utf8_decode($row[2]),1,'C');
						$pdf->SetXY(110,$Y);
						$pdf->Cell(20,$height,$row[3],1,'R');
						$pdf->SetXY(120,$Y);
						$pdf->Cell(35,$height,$row[4],1,'R');
						$pdf->SetXY(130,$Y);
						$pdf->Cell(25,$height,$row[5],1,'R');
						$pdf->SetXY(155,$Y);
						$pdf->Cell(20,$height,$row[6],1,'R');
						$pdf->SetXY(175,$Y);
						$pdf->Cell(25,$height,$row[7],1,'R');
						$pdf->SetFont('Arial','B',13);
						$pdf->SetXY(10,$Y);
						$pdf->Cell(75,6,'TOTAL PUNTOS',1,'L');
						$pdf->SetXY(155,$Y);
						$pdf->Cell(20,6,$suma,1,'R');
						$pdf->Ln(10);
						$pdf->SetFont('GothamBook');
						$pdf->SetX(40);
						$pdf->Write(5,utf8_decode('Anotése, Comuníquese, Regístre en su hoja de carrera funcionaria y archivase.'));
						$pdf->Ln(10);
						//$pdf->SetX(140);
						$pdf->SetFont('GothamBold');
						$pdf->SetX(40);
						$pdf->Write(5,('FDOS.:  '.$alcalde.', ALCALDE.'));
						$pdf->Ln(5);
						$pdf->SetX(57);
						$pdf->Write(5,$secretaria.', SECRETARIA MUNICIPAL.');
					  //$pdf->Cell(170,5,utf8_decode($alcalde),0,1,'R');
						$pdf->Ln(40);
						$pdf->SetX(125);                  
						$pdf->Cell(75,5,$secretaria,0,1,'R');
           	$pdf->SetX(140);
            $pdf->Write(5,utf8_decode('SECRETARIA MUNICIPAL'));
						$pdf->Ln(20);
						$pdf->Cell(47,5,$distribucion,0,1,'R');
				}else{
						$pdf->Ln(10);
						$pdf->SetFont('GothamBold');
						$pdf->SetXY(10,$Y);
						$pdf->Cell(75,6,'TOTAL PUNTOS',1,'L');
						$pdf->SetXY(155,$Y);
						$pdf->Cell(20,6,$suma,1,'R');
						$pdf->Ln(10);
						$pdf->SetX(40);
						$pdf->SetFont('GothamBook');
						$pdf->Write(5,utf8_decode('Anotése, Comuníquese, Regístre en su hoja de carrera funcionaria y archivase.'));
						$F = $pdf->GetY();
						if($F+60<280){
								$pt = 1;
								$pdf->Ln(30);
								$pdf->SetFont('GothamBold');
								$pdf->Cell(170,5,$alcalde,0,1,'R');
								$pdf->SetX(145);
								$pdf->Write(5,utf8_decode('ALCALDE'));
								$pdf->Ln(20);
								$pdf->Cell(75,5,$secretaria,0,1,'R');
								$pdf->SetX(32);
								$pdf->Write(5,utf8_decode('SECRETARIA MUNICIPAL'));
								$pdf->Ln(20);
								$pdf->Cell(47,5,$distribucion,0,1,'R');	
						}else{
								$pdf->AddPage('P',array(215.9,330.2));
								$pdf->AliasNbPages();
								$pdf->SetFont('GothamBold');
								$pdf->Header();
								$pdf->SetX(115);
								$pdf->Cell(210,5,utf8_decode('DECRETO ALCALDICIO N°: ').$dcact_id,0,1);
								$pdf->SetX(115);
								$pdf->Cell(197,5,utf8_decode('RENGO, ').$fecha,0,1); 
								$pdf->Ln(30);
								$pdf->SetFont('GothamBold');
								$pdf->Cell(170,5,$alcalde,0,1,'R');
								$pdf->SetX(145);
								$pdf->Write(5,utf8_decode('ALCALDE'));
								$pdf->Ln(20);
								$pdf->Cell(75,5,$secretaria,0,1,'R');
								$pdf->SetX(32);
								$pdf->Write(5,utf8_decode('SECRETARIA MUNICIPAL'));
								$pdf->Ln(20);
								$pdf->Cell(47,5,$distribucion,0,1,'R');	
						}
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->AliasNbPages();
						$pdf->SetFont('GothamBold');
						$pdf->Header();
						$pdf->SetX(115);
						$pdf->Cell(210,5,utf8_decode('DECRETO ALCALDICIO N°: ').$dcact_id,0,1);
						$pdf->SetX(115);
						$pdf->Cell(197,5,utf8_decode('RENGO, ').$fecha,0,1); 
						$pdf->Ln(5);	
						if ($pt==1){
								$pdf->SetFillColor(232,232,232);
								$pdf->SetFont('Arial','B',10);
								$pdf->Cell(75,6,'Nombre Actividad',1,0,'C',1);
								$pdf->Cell(25,6,'Fecha Act.',1,0,'C',1);                
								$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
								$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
								$pdf->cell(25,6,utf8_decode('Nivel Técnico'),1,0,'C',1);
								$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
								$pdf->Cell(25,6,'Fecha Ing.',1,0,'C',1);
								if($SEGUN ==1){
										/*$Y= 41;
										$cta=0;
										$pdf->SetFont('GothamBook');
										$pdf->SetXY(10,$Y);
										$pdf->MultiCell(75,6,$nombre,1,'L');
										$H = $pdf->GetY();
										$height = $H - $Y;
										$pdf->SetXY(85,$Y);
										$pdf->Cell(25,$height,utf8_decode($fecha_act),1,'C');
										$pdf->SetXY(110,$Y);
										$pdf->Cell(20,$height,$hrs,1,'R');
										$pdf->SetXY(120,$Y);
										$pdf->Cell(35,$height,$eval,1,'R');
										$pdf->SetXY(130,$Y);
										$pdf->Cell(25,$height,$nt,1,'R');
										$pdf->SetXY(155,$Y);
										$pdf->Cell(20,$height,$po,1,'R');
										$pdf->SetXY(175,$Y);
										$pdf->Cell(25,$height,$fi,1,'R');
										$Y= $H;*/
								}
                $DetalleMiOrden2 = "SELECT CARRERA_ACT.CA_ID, CARRERA_ACT.CA_DES, DATE_FORMAT(CARRERA_ACT.CA_FEC,'%Y-%m-%d'), CARRERA_ACT.CA_HORA, CARRERA_ACT.CA_NOTA, CARRERA_ACT.CA_NIVEL, CARRERA_ACT.CA_TOTAL, 
								DATE_FORMAT(CARRERA_ACT.CA_FEC_ING, '%Y-%m-%d') FROM DECRE_ACT_DETA INNER JOIN CARRERA_ACT ON DECRE_ACT_DETA.CA_ID=CARRERA_ACT.CA_ID WHERE (DA_DC_NUM = '$dcact_id') ORDER BY CA_FEC";
                $RespMiOrden2 = mysqli_query($cnn,$DetalleMiOrden2); 
                //
                $cuentawhile3=0;
                $Y=41;
                //echo $cuentawhil2;
                // echo $cuentawhile3;
                while ($row = mysqli_fetch_array($RespMiOrden2)){
                		if($cuentawhil2 <= $cuentawhile3){
                  			$cuentawhile3;
                    		$pdf->SetFont('GothamBook');
                        $pdf->SetXY(10,$Y);
                        $pdf->MultiCell(75,6,$row[1],1,'L');
                        $H = $pdf->GetY();
                        $height = $H - $Y;
                        $pdf->SetXY(85,$Y);
                        $pdf->Cell(25,$height,utf8_decode($row[2]),1,'C');
                        $pdf->SetXY(110,$Y);
                        $pdf->Cell(20,$height,$row[3],1,'R');
                        $pdf->SetXY(120,$Y);
                        $pdf->Cell(35,$height,$row[4],1,'R');
                        $pdf->SetXY(130,$Y);
                        $pdf->Cell(25,$height,$row[5],1,'R');
                        $pdf->SetXY(155,$Y);
                        $pdf->Cell(20,$height,$row[6],1,'R');
                        $pdf->SetXY(175,$Y);
                        $pdf->Cell(25,$height,$row[7],1,'R');
                        $Y = $H;  
                    }else{
                        $cuentawhile3 = $cuentawhile3 + 1;
                    }
                    //$NY = $pdf->GetY();
                }
                $pdf->SetFont('GothamBold');
								$pdf->SetXY(10,$Y);
								$pdf->Cell(75,6,'TOTAL PUNTOS',1,'L');
								$pdf->SetXY(155,$Y);
								$pdf->Cell(20,6,$suma,1,'R');
								$pdf->Ln(10);
						}
						$pdf->SetFont('GothamBook');
						$pdf->SetX(40);
						$pdf->Write(5,utf8_decode('Anotése, Comuníquese, Regístre en su hoja de carrera funcionaria y archivase.'));
						$pdf->Ln(10);
						//$pdf->SetX(140);
						$pdf->SetFont('GothamBold');
						$pdf->SetX(40);
						$pdf->Write(5,('FDOS.:  '.$alcalde.', ALCALDE.'));
						$pdf->Ln(5);
						$pdf->SetX(57);
						$pdf->Write(5,$secretaria.', SECRETARIA MUNICIPAL.');
						//$pdf->Cell(170,5,utf8_decode($alcalde),0,1,'R');
						$pdf->Ln(40);
						$pdf->SetX(117);                  
						$pdf->Cell(75,5,$secretaria,0,1,'R');
            $pdf->SetX(140);
            $pdf->Write(5,utf8_decode('SECRETARIA MUNICIPAL'));
						$pdf->Ln(20);
						$pdf->Cell(47,5,$distribucion,0,1,'R');
				}
				$pdf->Output();													
		} 
?>
