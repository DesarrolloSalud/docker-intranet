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
				include ("../../include/funciones/funciones.php");
        date_default_timezone_set("America/Santiago");
				$cnn = ConectarPersonal();
				$idrut = $_GET['id'];
        $dc_fin = utf8_decode('     Se extiende el presente certificado a petición del o la interesado(a), para los fines que estime conveniente.');
        $dc_fec = date("Y-m-d");
       	$fec_format = obtenerFechaEnLetra($dc_fec);
        $responsables="PVG/PGC/mpp";
        
        $director ="SELECT USU_RUT,USU_NOM,USU_APP,USU_APM,USU_CAT,USU_DEP FROM USUARIO WHERE (EST_ID= '1' AND USU_CAR='Director')";
        $respuesta2= mysqli_query($cnn, $director);   
        if($row12 = mysqli_fetch_array($respuesta2)){
                  $rutdir = $row12[0];
                  $alcalde = utf8_encode($row12[1]." ".$row12[2]." ".$row12[3]);
                  $alcaldesub = "DIRECTOR DE SALUD";                                  
         }
      
        $query1 = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_PROF,USUARIO.USU_DEP, USU_CAT FROM USUARIO WHERE (USU_RUT= '$idrut')";          
        $respuesta1= mysqli_query($cnn, $query1);   
        if($row11 = mysqli_fetch_array($respuesta1)){                          
                  
                  $usurut= $row11[0];
                  $usunom = utf8_decode($row11[1]);
                  $usuapp = utf8_decode($row11[2]);
                  $usuapm = utf8_decode($row11[3]);
                  $usuprof = ($row11[4]);
                  $usudep = utf8_decode($row11[5]);
					$MuestroCategoria=$row11[6];
         }
        class PDF extends FPDF{
						
						function Header(){								
								$this->Image('../../include/img/header.jpg',1,1,210,20);
								$this->SetY(30);
								$this->SetFont('Times','B',12);
								global $dcact_id;  
                $this->Cell(0,10,utf8_decode('CERTIFICADO'),0,0,'C');
								$this->Ln(10);
								$this->SetX(100);								 
						}
						function Footer(){
								$this->Image('../../include/img/footer.jpg',3,305,205,20);								
								$this->SetY(-15);
								$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
						}
				}
				
				$pdf = new PDF();
				$pdf->AddFont('GothamBooK','','GothamBook.php');
				$pdf->AddFont('GothamBold','','GOTHAM-BOLD.php');
				$pdf->SetLeftMargin(30);
				$pdf->SetRightMargin(20);				
				$pdf->AddPage('P',array(215.9,330.2));				
				$pdf->SetAutoPageBreak(auto,30);
				$pdf->SetTopMargin(20);
				$pdf->AliasNbPages();							               
        $pdf->Ln(5);				
				$pdf->SetFont('Times','',12);
				$texto  =utf8_decode('     El Director del Departamento de Salud de la Ilustre Municipalidad de Rengo, certifica en base a los antecedentes que se registran, que Don(a) '.utf8_decode($usunom).' '.utf8_decode($usuapp).' '.utf8_decode($usuapm).' RUN N°: '.$usurut.', '.utf8_decode($usuprof).' del '.utf8_decode($usudep).', resgistra las siguientes actividades de capacitación.' );
        $pdf->MultiCell(165,5,$texto,'J');
				$y = $pdf->GetY();
				$query = "SELECT CA_ID, CA_DES, DATE_FORMAT(CA_FEC,'%d-%m-%Y'),CA_TOTAL FROM CARRERA_ACT WHERE (USU_RUT = '".$idrut."') AND (CA_ESTADO<>'Inactivo')  ORDER BY CA_FEC";
        $respuesta = mysqli_query($cnn, $query);
			
			/*########### CÁLCULO DE PUNTAJE VALIDO HASTA EL AÑO ACTUAL #################*/
			$ano5 = date("Y");
			$acumu_pun = 0;
			$acumu_pun1 = 0;
			$sal_acu =0;
			$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$idrut."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC <='2021-08-31') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
			$respuesta2 = mysqli_query($cnn, $query2);
			$row = $respuesta2->fetch_array(MYSQLI_NUM);
			for ($i = $row[1]; $i <= $ano5; $i++) {
				$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$idrut."') AND (CA_ESTADO <> 'Inactivo') AND (YEAR(CA_FEC)=$i) AND (CA_FEC <='2021-08-31') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
				$respuesta3 = mysqli_query($cnn, $query2);
				$row1 = $respuesta3->fetch_array(MYSQLI_NUM);
				$valano = $i;
				$year = $row1[1];
				$puntaje = $row1[2];
				if($year !='' and $puntaje !=''){
					if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
						$acumu_pun1 = $row1[2] + $sal_acu;
						if($acumu_pun1 < 151){   
							$acumu_pun = ($acumu_pun + $acumu_pun1);
							$sal_acu=0;
						}else{
							$acumu_pun = $acumu_pun + 150;
							$sal_acu = ($acumu_pun1 - 150);
						}       
					}else{  
						$acumu_pun1 = $row1[2] + $sal_acu;
						if($acumu_pun1 < 118){  
							$acumu_pun = $acumu_pun + $acumu_pun1;
							$sal_acu=0;      
						}else{
							$acumu_pun = $acumu_pun + 117;
							$sal_acu = ($acumu_pun1 - 117);
						} 
					} 
				}else{
					$puntaje =0;  
					if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
						$acumu_pun1 = $puntaje + $sal_acu;
						if($acumu_pun1 < 151){   
							$acumu_pun = $acumu_pun + $acumu_pun1;
							$sal_acu=0;
						}else{
							$acumu_pun = $acumu_pun + 150;
							$sal_acu = ($acumu_pun1 - 150);
						} 
					}else{
						$acumu_pun1 = $puntaje + $sal_acu;
						if($acumu_pun1 < 118){  
							$acumu_pun = $acumu_pun + $acumu_pun1;
							$sal_acu=0;      
						}else{
							$acumu_pun = $acumu_pun + 117;
							$sal_acu = ($acumu_pun1 - 117);
						}
					}
				} 
			}
			$acumu_pun31= $acumu_pun;
			//############ HASTA EL 31 DE AGOSTO DE 2021 ##################
			//########### DESDE EL 01 DE SEPTIEMBRE 2021 ##################
			$acumu_pun01=0;
			$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC_ING),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$idrut."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC_ING >='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
			$respuesta21 = mysqli_query($cnn, $query2);
			$row21 = $respuesta21->fetch_array(MYSQLI_NUM);
			if($row21 != ''){
				for ($i1 = $row21[1]; $i1 <= $ano5; $i1++) {
					$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC_ING),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$idrut."') AND (CA_ESTADO <> 'Inactivo') AND (YEAR(CA_FEC_ING)=$i1) AND (CA_FEC_ING >='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
					$respuesta31 = mysqli_query($cnn, $query2);
					$row211 = $respuesta31->fetch_array(MYSQLI_NUM);
					$valano = $i1;
					$year = $row211[1];
					$puntaje = $row211[2];
					if($year !='' and $puntaje !=''){
						if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
							$acumu_pun1 = $row211[2] + $sal_acu;
							if($acumu_pun1 < 151){   
								$acumu_pun = ($acumu_pun + $acumu_pun1);
								$sal_acu=0;
							}else{
								$acumu_pun = $acumu_pun + 150;
								$sal_acu = ($acumu_pun1 - 150);
							}   
						}else{  
							$acumu_pun1 = $row211[2] + $sal_acu;
							if($acumu_pun1 < 118){
								$acumu_pun = $acumu_pun + $acumu_pun1;
								$sal_acu=0;  
							}else{
								$acumu_pun = $acumu_pun + 117;
								$sal_acu = ($acumu_pun1 - 117);
							} 
						} 
					}else{
						$puntaje =0;
						if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
							$acumu_pun1 = $puntaje + $sal_acu;
							if($acumu_pun1 < 151){  
								$acumu_pun = $acumu_pun + $acumu_pun1;
								$sal_acu=0;
							}else{
								$acumu_pun = $acumu_pun + 150;
								$sal_acu = ($acumu_pun1 - 150);
							} 
						}else{
							$acumu_pun1 = $puntaje + $sal_acu;
							if($acumu_pun1 < 118){  
								$acumu_pun = $acumu_pun + $acumu_pun1;
								$sal_acu=0; 
							}else{
								$acumu_pun = $acumu_pun + 117;
								$sal_acu = ($acumu_pun1 - 117);
							}
						}
					}
				}  
			}else{
				$acumu_pun=0;
			}
			$acumu_pun01 = $acumu_pun;
			//########## FIN DESDE EL 01 DE SEPTIEMBRE 2021 ####################
			$acumu_pun=0;
			//#### INICIO SUMA DE PUNTAJES POR CAPACITACIÓN CON AMBOS CÁLCULOS #####
			$acumu_pun = $acumu_pun31 + $acumu_pun01;
			//#### FIN SUMA DE PUNTAJES POR CAPACITACIÓN CON AMBOS CÁLCULOS #####    
			$saldo = number_format($conta_pun - $acumu_pun,2,'.', '');
			$valano = $valano + 1;
			while($valano <= $ano5){  
				$valano = $valano + 1;
				if($MuestroCategoria == "A" || $MuestroCategoria == "B"){      
					if($saldo >= 150){
						$acumu_pun= $acumu_pun +150;
						$saldo = $saldo -150;
					}else{
						$acumu_pun = $acumu_pun + $saldo;
						$saldo=0;
					}
				}else{
					if($saldo >= 117){ 
						$acumu_pun= $acumu_pun + 117;
						$saldo = $saldo -117;
					}else{
						$acumu_pun = $acumu_pun + $saldo;
						$saldo=0;
					}
				}
			}
			if($saldo<0){
				$saldo=0;
			}
			if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
				if($acumu_pun > 4500){
					$acumu_pun= 4500;
				}
			}else{
				if($acumu_pun > 3500){
					$acumu_pun= 3500;
				}
			}
			//########## FIN CÁLCULO DE PUNTAJE AÑO ACTUAL ###############################
        
        $cont = 0;
        $espacio="";        
				if($y>280){												
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->AliasNbPages();
            $cont= $cont+1;
				}
				$pdf->Ln();
				$pdf->SetFillColor(232,232,232);
				$pdf->SetFont('Times','B',10);        
        $pdf->Cell(118,6,'NOMBRE',1,0,'C',1);
        $pdf->Cell(12,6,utf8_decode('Pts.'),1,0,'C',1);
        $pdf->Cell(35,6,'FECHA ACTIVIDAD',1,0,'C',1);
        $pdf->Ln();
        
				$ultima = "NO";
        while ($row = mysqli_fetch_array($respuesta)){
						if($y > 280){
							if($ultima == "SI"){
								$cont = 0;
							}else{
								$ultima = "SI";
							}
							$pdf->AddPage('P',array(215.9,330.2));
							$pdf->AliasNbPages();	
							$pdf->Ln(10);
							$pdf->SetFillColor(232,232,232);
							$pdf->SetFont('Times','B',10);
							$pdf->Cell(118,6,'NOMBRE',1,0,'C',1);
              $pdf->Cell(12,6,utf8_decode('Pts.'),1,0,'C',1);
              $pdf->Cell(35,6,'FECHA ACTIVIDAD',1,0,'C',1);
							$pdf->Ln();
						}
            $pdf->SetFont('Times','',10);            
            $y1 = $pdf->GetY();          
            $pdf->MultiCell(118,9,$row[1],1,'C');
						$y2 = $pdf->GetY();
            $height = $y2 - $y1;
            $pdf->SetXY(148,$y1);
            $pdf->MultiCell(12,$height,$row[3],1,'C');           
            $pdf->SetXY(160,$y1);
						$pdf->MultiCell(35,$height,$row[2],1,'C');
            $y = $pdf->GetY();  
					$total_puntaje= $total_puntaje+ $row[3];
				}        
        if($y+60 >290){
					$pdf->AddPage('P',array(215.9,330.2));
          $pdf->AliasNbPages();	
					$pdf->Ln(10);
          $pdf->SetFont('Times','B',12);
          $pdf->MultiCell(165,5,utf8_decode('Puntaje Válido Año en Curso: ').$acumu_pun);
					$pdf->SetFont('Times','',12);
					$pdf->MultiCell(165,5,utf8_decode('Puntaje Total Acumulado: ').$total_puntaje);
          $pdf->Ln(5);
          $pdf->SetFont('Times','',12);
          $pdf->MultiCell(165,5,($dc_fin));
          $pdf->Ln(10);
          $pdf->Cell(70,5,'Rengo,'.$fec_format,'J');
          $y = $pdf->GetY();          
          $pdf->SetY($y+30);
          $pdf->SetX(105);
          $rutusu ='../../include/img/firmas/'.$Srut.'.png';
          chmod($rutusu, 0755);
          $firma= '../../include/img/firmas/'.$rutdir.'.png';
          chmod($firma, 0755);
            if (is_readable($firma)) {
                $pdf->Image($firma,118,$y,60,30);
            }          
          $timbre= '../../include/img/firmas/timbre_dir.png';
          chmod($timbre, 0755); 
            if (is_readable($timbre)) {
                $pdf->Image($timbre,80,$y+5,32,32);
            }
          $pdf->Line(105,$y+29,191,$y+29);          
          $pdf->SetFont('Times','B',12);
          $pdf->Cell(70,5,$alcalde);
          $pdf->Ln();
          $pdf->SetX(125);
          $pdf->SetFont('Times','B',12);
          $pdf->Cell(70,5,utf8_decode($genalcalde.' '.$alcaldesub));
          $y = $pdf->GetY();
          $pdf->SetY($y+20);
          $pdf->Write(5,$responsables);
          $pdf->SetFont('Times','',10);
				}else{
          $pdf->Ln(10);
          $pdf->SetFont('Times','B',12);
          $pdf->MultiCell(165,5,utf8_decode('Puntaje Válido Año en Curso: ').$acumu_pun);
					$pdf->SetFont('Times','',12);
					$pdf->MultiCell(165,5,utf8_decode('Puntaje Total Acumulado: ').$total_puntaje);
          $pdf->Ln(5);
          $pdf->SetFont('Times','',12);
          $pdf->MultiCell(165,5,($dc_fin));
          $pdf->Ln(10);
          $pdf->Cell(70,5,'Rengo,'.$fec_format,'J');
          $y = $pdf->GetY();          
          $pdf->SetY($y+30);
          $pdf->SetX(105);
          $rutusu ='../../include/img/firmas/'.$Srut.'.png';
          chmod($rutusu, 0755);
          $firma= '../../include/img/firmas/'.$rutdir.'.png';
          chmod($firma, 0755);
            if (is_readable($firma)) {
                $pdf->Image($firma,118,$y,60,30);           
            }
          
          $timbre= '../../include/img/firmas/timbre_dir.png';
          chmod($timbre, 0755);
            if (is_readable($timbre)) {
                $pdf->Image($timbre,80,$y+5,32,32);
            }
          $pdf->Line(105,$y+29,192,$y+29);          
          $pdf->SetFont('Times','B',12);
          $pdf->Cell(70,5,$alcalde);
          $pdf->Ln();
          $pdf->SetX(125);
          $pdf->SetFont('Times','B',12);
          $pdf->Cell(70,5,utf8_decode($genalcalde.' '.$alcaldesub));
          $y = $pdf->GetY();
          $pdf->SetY($y+20);
          $pdf->Write(5,$responsables);
          $pdf->SetFont('Times','',10);
        }				
	      				
				}
        chmod($rutusu, 0000);
        chmod($firma, 0000);
        chmod($timbre, 0000);
				$pdf->Output();			
?>
