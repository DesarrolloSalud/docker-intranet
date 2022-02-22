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
        /*if(count($_GET) && !$_SERVER['HTTP_REFERER']){
           header("location: ../error.php");
        }*/
        
        $Srut = utf8_encode($_SESSION['USU_RUT']);
				require('../../include/fpdf/fpdf.php');
				include ("../../include/funciones/funciones.php");
        date_default_timezone_set("America/Santiago");
				$cnn = ConectarPersonal();
				$idrut = $_GET['id'];
			$totalyear=0;
			$totalmes=0;
			$totaldays=0;
			$entra=0;
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
                  /*$secretaria = utf8_encode($row12[9]);
                  $responsables = utf8_encode($row11[12]);
                                    
                  $secretariasub = $row11[10];                  
                  $genalcalde= $row11[8];
                  $gensecre = $row11[11];  */                 
         }
      
        $query1 = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_PROF,USUARIO.USU_DEP,USUARIO.USU_CAT FROM USUARIO WHERE (USU_RUT= '$idrut')";          
        $respuesta1= mysqli_query($cnn, $query1);   
        if($row11 = mysqli_fetch_array($respuesta1)){                          
                  
                  $usurut= $row11[0];
                  $usunom = utf8_decode($row11[1]);
                  $usuapp = utf8_decode($row11[2]);
                  $usuapm = utf8_decode($row11[3]);
                  $usuprof = ($row11[4]);
                  $usudep = utf8_decode($row11[5]);
                  $usucat = $row11[6];                 
                  
         }
        class PDF extends FPDF{
						// Page header
						function Header(){
								// Logo
								$this->Image('../../include/img/header.jpg',1,1,210,20);
								$this->SetY(30);
								//$this->SetX(100);
								$this->SetFont('Times','B',12);
                
								//$dec_alc = utf8_encode("CERTIFICADO",'C');
								global $dcact_id;
								//$this->Write(5,utf8_encode('CERTIFICADO'));   
                $this->Cell(0,10,utf8_decode('CERTIFICADO'),0,0,'C');
								$this->Ln(10);
								$this->SetX(100);
								//global $dc_fec;
								/*$fec_format = obtenerFechaEnLetra($dc_fec);
								$this->Write(5,'Rengo,'.$fec_format);
								$this->Ln(10); */ 
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
				$texto  =utf8_decode('     El Director del Departamento de Salud de la Ilustre Municipalidad de Rengo, certifica en base a los antecedentes que se registran, que Don(a) '.utf8_decode($usunom).' '.utf8_decode($usuapp).' '.utf8_decode($usuapm).' RUN N°: '.$usurut.', '.utf8_decode($usuprof).' del '.utf8_decode($usudep).', resgistra los siguientes nombramientos.' );
        $pdf->MultiCell(165,5,$texto,'J');
				//$pdf->Ln();
				//$pdf->Cell(70,5,'Rengo,'.$fec_format);
				$y = $pdf->GetY();
        
				 $query = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_CALJURI,CB_NUM_DOC,CB_INDEFI,USU_CAT,CB_ESTABLE FROM CARRERA_BIENIO WHERE (USU_RUT = '".$idrut."') AND CB_ESTADO ='1'  AND CB_ESTABLE='DEPARTAMENTO DE SALUD I.MUNICIPALIDAD DE RENGO' ORDER BY CB_FEC_INI";
        
        $respuesta = mysqli_query($cnn, $query);
        //recorrer los registros
        $cont = 0;
        $espacio="";
        
				if($y>280){
            $pdf->SetLeftMargin(30);
				    $pdf->SetRightMargin(20);
						$pdf->AddPage('P',array(215.9,330.2));
            $pdf->SetAutoPageBreak(auto,30);
				    $pdf->SetTopMargin(20);
						$pdf->AliasNbPages();
            $cont= $cont+1;
				}
				$pdf->Ln(5);
				$pdf->SetFillColor(232,232,232);
				$pdf->SetFont('Times','B',10);
        $pdf->SetX(31);
        $pdf->Cell(60,6,utf8_decode('ESTABLECIMIENTO'),1,0,'C',1);
        $pdf->Cell(35,6,utf8_decode('PERÍODO'),1,0,'C',1);
        $pdf->Cell(40,6,utf8_decode('NOMBRAMIENTO'),1,0,'C',1);
        $pdf->Cell(28,6,utf8_decode('CATEGORÍA'),1,0,'C',1);
        $pdf->Ln();
        
				$ultima = "NO";
        while ($row = mysqli_fetch_array($respuesta)){
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
              $pdf->SetX(31);
              $pdf->Cell(60,6,utf8_decode('ESTABLECIMIENTO'),1,0,'C',1);
							$pdf->Cell(35,6,utf8_decode('PERÍODO'),1,0,'C',1);
              $pdf->Cell(40,6,utf8_decode('NOMBRAMIENTO'),1,0,'C',1);
              $pdf->Cell(28,6,utf8_decode('CATEGORÍA'),1,0,'C',1);
							$pdf->Ln();
              //$cont= $cont+1;
						}
            $pdf->SetX(31);
            $pdf->SetFont('Times','',10);
            if($row[4]==1){
              $hasta = "INDEFINIDO";
							$hasta = $dc_fec;//eliminar en producción
            }else{
              $hasta=$row[1];							
            }
            $largo1 = strlen($row[6]);
            if($largo1 < 46){
              while($largo1 < 46){
              $espacio1 = "  " . $espacio1;
              $largo1= $largo1 +1;              
              }
            }
            $y1 = $pdf->GetY();
            $pdf->MultiCell(60,9,utf8_decode($row[6]).$espacio1,1,'C');             
            $y2 = $pdf->GetY();          
            $height = $y2 -$y1;
            if($height >= 27){
              $alto =13.5;
            }else{
              $alto =9;
            }        
            $pdf->SetXY(91,$y1);
            $pdf->MultiCell(35,$alto,'Desde el '.$row[0].' al '.$hasta,1,'C');
					$date1=date_create($row[0]);
					$date2=date_create($hasta);
					//echo $row[0]."--";
					//echo $hasta."///";
					//echo $hasta;
					if($entra==0){
						$diff=date_diff($date1,$date2);
						$year = $diff->format("%y");
						$mes = $diff->format("%m");
						$days = $diff->format("%a");
						//echo $diff."///";
						$totalyear = $totalyear + $year;	
						$totalmes = $totalmes + $mes;
						$totaldays = $totaldays + $days;
						$entra=1;
					}
					
					
					//$pdf->MultiCell(35,$diff,1,'C');
					          
             $largo = strlen($row[2].utf8_decode('  N° :  ').$row[3]);  
            
            if($height<27){
              while($largo < 31){
              $espacio = "  " . $espacio;
              $largo= $largo +1;              
            } 
            }
             
           
               
            
            $pdf->SetXY(126,$y1);
            $pdf->MultiCell(40,$alto,$row[2].utf8_decode('  N° :  ').$row[3].$espacio,1,'C');            
            
            if($row[5]==''){
              $categoria = $usucat;
            }else{
              $categoria = $row[5];
            }
            $pdf->SetXY(166,$y1);
						$pdf->MultiCell(28,$height,$categoria,1,'C');
            $y = $pdf->GetY();
            $espacio="";
            $largo=0;
            $espacio1="";
            $largo1=0;
						
				}        
        if($y+60 >290){
					$pdf->AddPage('P',array(215.9,330.2));
          $pdf->AliasNbPages();	
          $pdf->Ln(10);
          $pdf->SetFont('Times','',12);
          //$pdf->Write(5,utf8_decode('Anotése, Comuníquese, Regístre en su hoja de carrera funcionaria y archivase.'));
          $pdf->MultiCell(165,5,($dc_fin));
					$pdf->Ln(5);
          $pdf->Cell(70,5,utf8_decode('Años,'.$totalyear),'J');
					$pdf->Cell(70,5,utf8_decode('Meses,'.$totalmes),'J');
					$pdf->Cell(70,5,utf8_decode('Años,'.$totaldays),'J');
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
          $pdf->SetFont('Times','',12);
          $pdf->MultiCell(165,5,($dc_fin));
					$pdf->Ln(5);
          $pdf->Cell(70,5,utf8_decode('Años,'.$totalyear),'J');
					$pdf->Cell(70,5,utf8_decode('Meses,'.$totalmes),'J');
					$pdf->Cell(70,5,utf8_decode('Días,'.$totaldays),'J');
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
          $pdf->SetY($y+10);
          $pdf->Write(5,$responsables);
          $pdf->SetFont('Times','',10);
        }				
				
				
				
				}
        //$rutusu ="../../include/img/firmas/".$Srut.".png";
        chmod($firma, 0000);
        chmod($timbre, 0000);
        chmod($rutusu, 0000);
       
				$pdf->Output();			
?>
