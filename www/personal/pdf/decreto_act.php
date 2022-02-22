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
				DECRE_ACT.DA_FEC,DECRE_ACT.DA_ALCALDE,DECRE_ACT.DA_SECRE,DECRE_ACT.DA_DERIVA, DECRE_ACT.DA_ALCSUB,DECRE_ACT.DA_SECSUB,DECRE_ACT.DA_GENALC, DECRE_ACT.DA_GENSEC, DECRE_ACT.DA_DC_NUM FROM DECRE_ACT INNER JOIN USUARIO ON DECRE_ACT.USU_RUT=USUARIO.USU_RUT 
				LEFT JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (DA_ID= $dcact_id)";
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
        $alcaldesub =  $rowDC[14];
        $secretariasub =  $rowDC[15];       
        $genalcalde = $rowDC[16];
        $gensecre = $rowDC[17];
        $dadcnum= $rowDC[18];   
        if($genalcalde==""){
           $genalcalde="ALCALDE";
        }
        if($gensecre==""){
            $gensecre="SECRETARIA";
        }
        class PDF extends FPDF{
						// Page header
						function Header(){
								// Logo
								$this->Image('../../include/img/header.jpg',1,1,210,20);
								$this->SetY(30);
								$this->SetX(100);
								$this->SetFont('Times','B',12);
								$dec_alc = utf8_encode("DECRETO ALCALDICIO N");
                global $usu_rut;
                global $dadcnum;
								global $dcact_id;
								$this->Write(5,utf8_encode($dec_alc.' :  __/'.$dadcnum.' (SALUD)'));   
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
				//$pdf->Ln(5);
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
				//$pdf->Ln();
				$y = $pdf->GetY();		
				$anodecre = date("Y", strtotime($dc_fec));
				$DetalleMiOrden = "SELECT CARRERA_ACT.CA_ID, CARRERA_ACT.CA_DES, DATE_FORMAT(CARRERA_ACT.CA_FEC,'%Y-%m-%d'), CARRERA_ACT.CA_HORA, CARRERA_ACT.CA_NOTA, CARRERA_ACT.CA_NIVEL, CARRERA_ACT.CA_TOTAL, 
				DATE_FORMAT(CARRERA_ACT.CA_FEC_ING, '%Y-%m-%d') FROM DECRE_ACT_DETA INNER JOIN CARRERA_ACT ON DECRE_ACT_DETA.CA_ID=CARRERA_ACT.CA_ID WHERE (DA_DC_NUM = '$dadcnum') AND (DECRE_ACT_DETA.USU_RUT = '".$usu_rut."') AND (DATE_FORMAT(CARRERA_ACT.CA_FEC_ING, '%Y') =$anodecre) ORDER BY CA_FEC";
        $RespMiOrden = mysqli_query($cnn,$DetalleMiOrden);
				$RespMiOrden2 = mysqli_query($cnn,$DetalleMiOrden);
				$num_registros = mysqli_num_rows($RespMiOrden);
				if($num_registros==0){
					$DetalleMiOrden = "SELECT CARRERA_ACT.CA_ID, CARRERA_ACT.CA_DES, DATE_FORMAT(CARRERA_ACT.CA_FEC,'%Y-%m-%d'), CARRERA_ACT.CA_HORA, CARRERA_ACT.CA_NOTA, CARRERA_ACT.CA_NIVEL, CARRERA_ACT.CA_TOTAL, 
				DATE_FORMAT(CARRERA_ACT.CA_FEC_ING, '%Y-%m-%d') FROM DECRE_ACT_DETA INNER JOIN CARRERA_ACT ON DECRE_ACT_DETA.CA_ID=CARRERA_ACT.CA_ID WHERE (DA_DC_NUM = '$dadcnum') AND (DECRE_ACT_DETA.USU_RUT = '".$usu_rut."') ORDER BY CA_FEC";
        $RespMiOrden = mysqli_query($cnn,$DetalleMiOrden);
				$RespMiOrden2 = mysqli_query($cnn,$DetalleMiOrden);
				$num_registros = mysqli_num_rows($RespMiOrden);
				}
				if($y>=290){												
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->AliasNbPages();
            $cont= $cont+1;
				}
				$pdf->Ln();
				$pdf->SetFillColor(232,232,232);
				$pdf->SetFont('Times','B',10);
				$pdf->Cell(60,6,'Nombre Actividad',1,0,'C',1);
				$pdf->Cell(20,6,'Fecha Act.',1,0,'C',1);                
				$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
				$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
				$pdf->cell(25,6,utf8_decode('Nivel Técnico'),1,0,'C',1);
				$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
				$pdf->Cell(20,6,'Fecha Ing.',1,0,'C',1);
				$pdf->Ln();
				$ultima = "NO";
        while ($row = mysqli_fetch_array($RespMiOrden)){
						if($y > 280){
							if($ultima == "SI"){
								//volver contador a 0
								$cont = 0;
							}else{
								$ultima = "SI";
							}
							$pdf->AddPage('P',array(215.9,330.2));
							$pdf->AliasNbPages();	
							$pdf->Ln(10);
							$pdf->SetFillColor(232,232,232);
							$pdf->SetFont('Times','B',10);
							$pdf->Cell(60,6,'Nombre Actividad',1,0,'C',1);
							$pdf->Cell(20,6,'Fecha Act.',1,0,'C',1);                
							$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
							$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
							$pdf->cell(25,6,utf8_decode('Nivel Técnico'),1,0,'C',1);
							$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
							$pdf->Cell(20,6,'Fecha Ing.',1,0,'C',1);
							$pdf->Ln();
              //$cont= $cont+1;
						}
            $pdf->SetFont('Times','',10);
						$y1 = $pdf->GetY();	
						$pdf->MultiCell(60,6,$row[1],1,'J');
						$y2 = $pdf->GetY();
						$height = $y2 - $y1;
						$pdf->SetXY(90,$y1);
						$pdf->Cell(20,$height,utf8_decode($row[2]),1,'C');
						$pdf->SetXY(110,$y1);
						$pdf->Cell(10,$height,$row[3],1,'C');
						$pdf->SetXY(120,$y1);
						$pdf->Cell(10,$height,$row[4],1,'R');
						$pdf->SetXY(130,$y1);
						$pdf->Cell(25,$height,$row[5],1,'R');
						$pdf->SetXY(155,$y1);
						$pdf->Cell(20,$height,$row[6],1,'R');
						$total = $total + $row[6];
						$pdf->SetXY(175,$y1);
						$pdf->Cell(20,$height,$row[7],1,'R');
						$pdf->Ln();
						if($ultima == "SI"){
							$cont = $cont + 1;
						}
						$y = $pdf->GetY();	
				}
        
				if($y + 80 >= 290){
					//nueva hoja          
					$pdf->AddPage('P',array(215.9,330.2));
					$pdf->AliasNbPages();	
					$cont = 0;
					$pdf->Ln(10);
          $cont= $cont+1;
				}
				$pdf->SetFont('Times','B',10);
				$pdf->SetFillColor(232,232,232);
				$pdf->Cell(125,6,'TOTAL PUNTOS',1,0,'L',1);
				$pdf->Cell(20,6,$total,1,0,'C',1);
				$pdf->Cell(20,6,'',1,0,'L',1);
				//$pdf->Ln();
				$y = $pdf->GetY();
				$pdf->SetY($y+10);
				$pdf->SetFont('Times','B',12);
				$pdf->Write(5,utf8_decode('Anótese, Comuníquese, Regístrese en su hoja de carrera funcionaria y archivase.'));
				//$pdf->Ln();
				$y = $pdf->GetY();
				$pdf->SetY($y+30);
				$pdf->SetX(130);
				$pdf->Line(125,$y+29,188,$y+29);
        //$pdf->Line(130,100,90,100);
				//$pdf->Line(120,100,180,100);
				$pdf->SetFont('Times','',12);
				$pdf->Cell(70,5,$alcalde);
				$pdf->Ln();
				$pdf->SetX(145);
				$pdf->SetFont('Times','B',12);
        $pdf->Cell(70,5,utf8_decode($genalcalde.' '.$alcaldesub));
				$y = $pdf->GetY();
				$pdf->SetY($y+20);
				$pdf->SetFont('Times','',12);
				$pdf->Line(30,$y+19,105,$y+19);
        $pdf->SetX(30);
				$pdf->Cell(75,5,$secretaria,0,0,'C',false);
        $pdf->Ln();
				$pdf->SetFont('Times','B',12);
				$pdf->SetX(30);
        $pdf->Cell(75,5,$gensecre.' MUNICIPAL '.$secretariasub,0,0,'C',false);
				$y = $pdf->GetY();
				$pdf->SetY($y+15);//antes en 20
				$pdf->Write(5,$distribucion);
				$pdf->SetFont('Times','',10);
				//$pdf->Write(5,$y);
				$pdf->AddPage('P',array(215.9,330.2));
				$pdf->AliasNbPages();	
				$pdf->Ln(10);
        //$cont= $cont+1;
				if($cont == 0){
          //AGREGADO CUANDO NO ES MÁS DE UNA HOJA
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
				//$pdf->Ln(5);
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
				$pdf->Ln();
				$y = $pdf->GetY();
					$anodecre = date("Y", strtotime($dc_fec));
					$DetalleMiOrden = "SELECT CARRERA_ACT.CA_ID, CARRERA_ACT.CA_DES, DATE_FORMAT(CARRERA_ACT.CA_FEC,'%Y-%m-%d'), CARRERA_ACT.CA_HORA, CARRERA_ACT.CA_NOTA, CARRERA_ACT.CA_NIVEL, CARRERA_ACT.CA_TOTAL, 
				DATE_FORMAT(CARRERA_ACT.CA_FEC_ING, '%Y-%m-%d') FROM DECRE_ACT_DETA INNER JOIN CARRERA_ACT ON DECRE_ACT_DETA.CA_ID=CARRERA_ACT.CA_ID WHERE (DA_DC_NUM = '$dadcnum') AND (DECRE_ACT_DETA.USU_RUT ='".$usu_rut."') AND (DATE_FORMAT(CARRERA_ACT.CA_FEC_ING, '%Y') =$anodecre) ORDER BY CA_FEC";
        $RespMiOrden = mysqli_query($cnn,$DetalleMiOrden);
				$RespMiOrden2 = mysqli_query($cnn,$DetalleMiOrden);
				$num_registros = mysqli_num_rows($RespMiOrden);
				if($num_registros==0){
					$DetalleMiOrden = "SELECT CARRERA_ACT.CA_ID, CARRERA_ACT.CA_DES, DATE_FORMAT(CARRERA_ACT.CA_FEC,'%Y-%m-%d'), CARRERA_ACT.CA_HORA, CARRERA_ACT.CA_NOTA, CARRERA_ACT.CA_NIVEL, CARRERA_ACT.CA_TOTAL, 
				DATE_FORMAT(CARRERA_ACT.CA_FEC_ING, '%Y-%m-%d') FROM DECRE_ACT_DETA INNER JOIN CARRERA_ACT ON DECRE_ACT_DETA.CA_ID=CARRERA_ACT.CA_ID WHERE (DA_DC_NUM = '$dadcnum') AND (DECRE_ACT_DETA.USU_RUT = '".$usu_rut."') ORDER BY CA_FEC";
        $RespMiOrden = mysqli_query($cnn,$DetalleMiOrden);
				$RespMiOrden2 = mysqli_query($cnn,$DetalleMiOrden);
				$num_registros = mysqli_num_rows($RespMiOrden);
				}	
        if($y >= 290){												
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->AliasNbPages();
            $cont= $cont+1;
				}
        $pdf->Ln();
				$pdf->SetFillColor(232,232,232);
				$pdf->SetFont('Times','B',10);
				$pdf->Cell(60,6,'Nombre Actividad',1,0,'C',1);
				$pdf->Cell(20,6,'Fecha Act.',1,0,'C',1);                
				$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
				$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
				$pdf->cell(25,6,utf8_decode('Nivel Técnico'),1,0,'C',1);
				$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
				$pdf->Cell(20,6,'Fecha Ing.',1,0,'C',1);
				$pdf->Ln();
				$ultima = "NO";
        //$total= 0;
         while ($row = mysqli_fetch_array($RespMiOrden)){
						if($cont2 >= $diferencia){
							$pdf->SetFont('Times','',10);
							$y1 = $pdf->GetY();	
							$pdf->MultiCell(60,6,$row[1],1,'J');
							$y2 = $pdf->GetY();
							$height = $y2 - $y1;
							$pdf->SetXY(90,$y1);
							$pdf->Cell(20,$height,utf8_decode($row[2]),1,'C');
							$pdf->SetXY(110,$y1);
							$pdf->Cell(10,$height,$row[3],1,'C');
							$pdf->SetXY(120,$y1);
							$pdf->Cell(10,$height,$row[4],1,'R');
							$pdf->SetXY(130,$y1);
							$pdf->Cell(25,$height,$row[5],1,'R');
							$pdf->SetXY(155,$y1);
							$pdf->Cell(20,$height,$row[6],1,'R');
							$pdf->SetXY(175,$y1);
							$pdf->Cell(20,$height,$row[7],1,'R');
							$pdf->Ln();
						}
						$cont2 = $cont2 + 1;
				}
          
          
          //###################################
					$pdf->SetFont('Times','B',10);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(125,6,'TOTAL PUNTOS',1,0,'L',1);
					$pdf->Cell(20,6,$total,1,0,'C',1);
					$pdf->Cell(20,6,'',1,0,'L',1);
					//$pdf->Ln();
					$y = $pdf->GetY();
          $pdf->SetY($y+10);
          $pdf->SetFont('Times','B',12);
          $pdf->Write(5,utf8_decode('Anótese, Comuníquese, Regístrese en su hoja de carrera funcionaria y archivase.'));
          $pdf->Ln();
					$y = $pdf->GetY();
					$pdf->SetY($y+10);
					$pdf->SetX(45);
					$pdf->Write(5,('FDOS.: '.$alcalde.', '.$genalcalde.' '.$alcaldesub));
					$pdf->Ln();
					$pdf->SetX(60);
					$pdf->Write(5,$secretaria.', '.$gensecre.' MUNICIPAL '.$secretariasub);
					$y = $pdf->GetY();
					$pdf->SetY($y+30);
					$pdf->SetFont('Times','',12);
					$pdf->Line(120,$y+29,190,$y+29);
         
					$pdf->SetX(120);
					$pdf->Cell(70,5,$secretaria,0,0,'C',false);
					$pdf->Ln();
					$pdf->SetFont('Times','B',12);
					$pdf->SetX(126);
					$pdf->Cell(55,5,$gensecre.' MUNICIPAL '.$secretariasub,0,0,'C',false);
					$y = $pdf->GetY();
					$pdf->SetY($y+15);//antes en 20
					$pdf->Write(5,$distribucion);
					$pdf->SetFont('Times','',10);
				}else{
          if($cont>1){
					$diferencia = $num_registros - $cont;
					$pdf->SetFillColor(232,232,232);
					$pdf->SetFont('Times','B',10);
					$pdf->Cell(60,6,'Nombre Actividad',1,0,'C',1);
					$pdf->Cell(20,6,'Fecha Act.',1,0,'C',1);                
					$pdf->Cell(10,6,utf8_decode('Hrs.'),1,0,'C',1);
					$pdf->Cell(10,6,utf8_decode('Eval.'),1,0,'C',1);
					$pdf->cell(25,6,utf8_decode('Nivel Técnico'),1,0,'C',1);
					$pdf->Cell(20,6,'Punt. Obt.',1,0,'C',1);
					$pdf->Cell(20,6,'Fecha Ing.',1,0,'C',1);
					$pdf->Ln();
          
            while ($row = mysqli_fetch_array($RespMiOrden2)){
              if($cont2 >= $diferencia){
                $pdf->SetFont('Times','',10);
                $y1 = $pdf->GetY();	
                $pdf->MultiCell(60,6,$row[1],1,'J');
                $y2 = $pdf->GetY();
                $height = $y2 - $y1;
                $pdf->SetXY(90,$y1);
                $pdf->Cell(20,$height,utf8_decode($row[2]),1,'C');
                $pdf->SetXY(110,$y1);
                $pdf->Cell(10,$height,$row[3],1,'C');
                $pdf->SetXY(120,$y1);
                $pdf->Cell(10,$height,$row[4],1,'R');
                $pdf->SetXY(130,$y1);
                $pdf->Cell(25,$height,$row[5],1,'R');
                $pdf->SetXY(155,$y1);
                $pdf->Cell(20,$height,$row[6],1,'R');
                $pdf->SetXY(175,$y1);
                $pdf->Cell(20,$height,$row[7],1,'R');
                $pdf->Ln();
              }
              $cont2 = $cont2 + 1;
            }
          }
					$pdf->SetFont('Times','B',10);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(125,6,'TOTAL PUNTOS',1,0,'L',1);
					$pdf->Cell(20,6,$total,1,0,'C',1);
					$pdf->Cell(20,6,'',1,0,'L',1);
					//$pdf->Ln();
					$y = $pdf->GetY();
					$pdf->SetY($y+10);
					$pdf->SetFont('Times','B',12);
					$pdf->Write(5,utf8_decode('Anótese, Comuníquese, Regístrese en su hoja de carrera funcionaria y archivase.'));
					$pdf->Ln();
					$y = $pdf->GetY();
					$pdf->SetY($y+10);
					$pdf->SetX(45);
					$pdf->Write(5,('FDOS.: '.$alcalde.', '.$genalcalde.' '.$alcaldesub));
					$pdf->Ln();
					$pdf->SetX(60);
					$pdf->Write(5,$secretaria.', '.$gensecre.' MUNICIPAL.'.$secretariasub);
					$y = $pdf->GetY();
					$pdf->SetY($y+30);//antes en 30
					$pdf->SetFont('Times','',12);
					$pdf->Line(120,$y+29,185,$y+29);
					$pdf->SetX(120);
					$pdf->Write(5,$secretaria);
					$pdf->Ln();
					$pdf->SetFont('Times','B',12);
					$pdf->SetX(126);
					$pdf->Cell(55,5,$gensecre.' MUNICIPAL '.$secretariasub,0,0,'C',false);
					$y = $pdf->GetY();
					$pdf->SetY($y+15);//antes en 20
					$pdf->Write(5,$distribucion);
					$pdf->SetFont('Times','',10);
				}
				$pdf->Output();	
		} 
?>
