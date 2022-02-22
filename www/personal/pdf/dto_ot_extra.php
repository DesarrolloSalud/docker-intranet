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
				$iddcre = $_GET['id'];
      
        $query1 = "SELECT DF_FEC,DF_NUM,DF_TEXT_VISTOS,DF_TEXT_CONSIDERANDO,DF_TEXT_DECRETO,DF_TEXT_FIN,DF_NOM_DIR,DF_DIR_SUB,DF_DIR_GEN,DF_NOM_SEC,DF_SEC_SUB,DF_SEC_GEN,DF_RESPONSABLES, USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM DECRETOS_FOR INNER JOIN USUARIO ON DECRETOS_FOR.DF_NOM_DIR=USUARIO.USU_RUT WHERE (DF_ID= ".$iddcre.")";          
        $respuesta1= mysqli_query($cnn, $query1);   
        if($row11 = mysqli_fetch_array($respuesta1)){                          
                  
                  $dc_fec = $row11[0];
                  $dcact_id = $row11[1];                  
                  $dc_visto = $row11[2];
                  $dc_visto = str_replace("<br />", " ", $dc_visto);
                  $dc_consi = $row11[3];
                  $dc_consi = str_replace("<br />", " ", $dc_consi);
                  $dc_dec = $row11[4];
                  $dc_dec = str_replace("<br />"," ", $dc_dec);
                  $dc_fin = $row11[5];
                  $dc_fin = str_replace("<br />"," ", $dc_fin);
                  $alcalde = utf8_encode($row11[6]);
                  $secretaria = utf8_encode($row11[9]);
                  $responsables = utf8_encode($row11[12]);
                  $alcaldesub = $row11[7];                  
                  $secretariasub = $row11[10];                  
                  $genalcalde= $row11[8];
                  $gensecre = $row11[11];
                  $alcalde1 = utf8_encode($row11[13]." ".$row11[14]." ".$row11[15]);
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
				//$pdf->Ln();
				$y = $pdf->GetY();
        
				$query = "SELECT OT_EXTRA_AUT_F.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT,OT_EXTRA_AUT_F.OEA_FEC_INI,OT_EXTRA_AUT_F.OEA_FEC_FIN,OT_EXTRA_AUT_F.OEA_LJ,OT_EXTRA_AUT_F.OEA_LJ_HI, OT_EXTRA_AUT_F.OEA_LJ_HF, OT_EXTRA_AUT_F.OEA_VI_HI,OT_EXTRA_AUT_F.OEA_VI_HF, OT_EXTRA_AUT_F.OEA_SDF_HI, OT_EXTRA_AUT_F.OEA_SDF_HF, OT_EXTRA_AUT_F.OEA_VI, OT_EXTRA_AUT_F.OEA_SDF FROM OT_EXTRA_ENC INNER JOIN OT_EXTRA_AUT_F ON OT_EXTRA_ENC.OEE_ID=OT_EXTRA_AUT_F.OEE_ID INNER JOIN USUARIO ON OT_EXTRA_AUT_F.USU_RUT = USUARIO.USU_RUT WHERE (OT_EXTRA_ENC.OEE_FOL_MUN= '$dcact_id' AND USU_ESTA='ACTIVO') ORDER BY USUARIO.USU_CAT";
        //$query = "SELECT OT_EXTRA_AUT_F.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT, OT_EXTRA_AUT_F.OEA_LJ_HI, OT_EXTRA_AUT_F.OEA_LJ_HF, OT_EXTRA_AUT_F.OEA_VI_HI,OT_EXTRA_AUT_F.OEA_VI_HF, OT_EXTRA_AUT_F.OEA_SDF_HI, OT_EXTRA_AUT_F.OEA_SDF_HF,OT_EXTRA_AUT_F.OEA_LJ,OT_EXTRA_AUT_F.OEA_VI,OT_EXTRA_AUT_F.OEA_SDF,OT_EXTRA_AUT_F.OEA_FEC_INI, FROM OT_EXTRA_ENC INNER JOIN OT_EXTRA_AUT_F ON OT_EXTRA_ENC.OEE_ID=OT_EXTRA_AUT_F.OEE_ID INNER JOIN USUARIO ON OT_EXTRA_AUT_F.USU_RUT = USUARIO.USU_RUT WHERE (OT_EXTRA_ENC.OEE_FOL_MUN= '$dcact_id')";
        $respuesta = mysqli_query($cnn, $query);
        //recorrer los registros
        $cont = 0;
        $espacio="";
        
				if($y>290){												
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->AliasNbPages();
            $cont= $cont+1;
				}
				$pdf->Ln();
				$pdf->SetFillColor(232,232,232);
				$pdf->SetFont('Times','B',10);        
        $pdf->Cell(52,6,'NOMBRE',1,0,'C',1);
        $pdf->Cell(21,6,'RUT',1,0,'C',1);                
        $pdf->Cell(15,6,utf8_decode('CATE.'),1,0,'C',1);
        $pdf->Cell(23,6,utf8_decode('PERÍODO'),1,0,'C',1);
        $pdf->Cell(53,6,'HORARIO',1,0,'C',1);
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
							$pdf->Cell(52,6,'NOMBRE',1,0,'C',1);
              $pdf->Cell(21,6,'RUT',1,0,'C',1);                
              $pdf->Cell(15,6,utf8_decode('CATE.'),1,0,'C',1);
              $pdf->Cell(23,6,utf8_decode('PERÍODO'),1,0,'C',1);
              $pdf->Cell(53,6,'HORARIO',1,0,'C',1);
							$pdf->Ln();
              //$cont= $cont+1;
						}
            $pdf->SetFont('Times','',10);
            $largo = strlen(utf8_decode($row[1])." ".utf8_decode($row[2])." ".utf8_decode($row[3]));  
            $nombre = utf8_decode($row[1])." ".utf8_decode($row[2])." ".utf8_decode($row[3]);
            while($largo < 30){
              $espacio = "  " . $espacio;
              $largo= $largo +1;              
            }
            /*if($row[0]=='15.738.621-2'){
              $largolj =strlen($row[7]);
              echo $largo;
              break 1;
            }*/
            $y1 = $pdf->GetY();
            $pdf->MultiCell(52,9,utf8_decode($row[1])." ".utf8_decode($row[2])." ".utf8_decode($row[3].$espacio),1,'C');
            
            
						$y2 = $pdf->GetY();            
            $height = $y2 - $y1;
            $pdf->SetXY(82,$y1);
						$pdf->Cell(21,$height,$row[0],1,'C');
						$pdf->SetXY(103,$y1);
						$pdf->Cell(15,$height,$row[4],1,0,'C');
						$pdf->SetXY(118,$y1);
						$pdf->MultiCell(23,6,$row[5]." hasta ".$row[6],1,'C');
						$pdf->SetXY(141,$y1);
            
           
            $lj= $row[7];
            $largolj =strlen($row[7]);
            if($largolj <12){
              while($largolj <11){
                $lj=$lj ."  ";
                $largolj = $largolj +1;
              }
            }
            if($row[14]=='V'){
              $viernes= "        Viernes";
              $vinicio = $row[10];
              $vfin = $row[11];
            }else{
              $viernes="                 ";
              $vinicio="               ";
              $vfin="               ";
            }
            
            $sdf = $row[15];
            $sdfini = $row[12];
            $sdffin = $row[13];
            $largosdf = strlen($row[15]);
            if($sdf==''){
              $sdf="                 ";
              $sdfini = "                 ";
              $sdffin = "                 ";              
            }else{             
               if($largosdf <6){
                while($largosdf <7){
                  $sdf="  ".$sdf;
                  $largosdf = $largosdf +1;
                }
              }
             }
          
						$pdf->MultiCell(53,6,$lj." ".$row[8]." ".$row[9]. " ".$viernes." ".$vinicio." ".$vfin."    ".$sdf." ".$sdfini." ".$sdffin,1,'C');
						
            $y = $pdf->GetY();
            $espacio="";
            $largo=0;
				}
        $pdf->Ln(5);
        $pdf->Write(5,'LU = LUNES, MA = MARTES, MI = MIERCOLES, JU = JUEVES, S = SABADO, D = DOMINGO, F = FERIADO');
        $pdf->Ln(10);

        if($y+80 >290){
					$pdf->AddPage('P',array(215.9,330.2));
          $pdf->AliasNbPages();	
          $pdf->SetFont('Times','B',12);
          $pdf->Cell(70,5,utf8_decode($dc_fin));
          $pdf->Ln(10);
          $y = $pdf->GetY();
          $pdf->SetY($y+35);
          $pdf->SetX(110);
          $pdf->Line(110,$y+34,194,$y+34);          
          $pdf->SetFont('Times','',12);
          $pdf->Cell(70,5,$alcalde1);
          $pdf->Ln();
          $pdf->SetX(145);
          $firma= '../../include/img/firmas/'.$alcalde.'.png';
          chmod($firma, 0755);
            if (is_readable($firma)) {
                #$pdf->Image($firma,118,$y,60,30);
            }          
          $timbre= '../../include/img/firmas/timbre_dir.png';
          chmod($timbre, 0755); 
            if (is_readable($timbre)) {
                #$pdf->Image($timbre,77,$y,32,32);
            }
          $pdf->SetFont('Times','B',12);
          $pdf->Cell(60,5,utf8_decode($genalcalde.' '.$alcaldesub));
          $y = $pdf->GetY();
          $pdf->SetY($y+25);
          $pdf->SetFont('Times','',12);          
          $pdf->Line(30,$y+19,105,$y+19);
          $pdf->SetX(30);
          $pdf->Cell(75,5,$secretaria,0,0,'C',false);
          $pdf->Ln();
          $pdf->SetFont('Times','B',12);
          $pdf->SetX(30);
          $pdf->Cell(75,5,$gensecre.' MUNICIPAL '.$secretariasub,0,0,'C',false);
          $y = $pdf->GetY();
          $pdf->SetY($y+10);
          $pdf->Write(5,$responsables);
          $pdf->SetFont('Times','',10);
				}else{
          //$pdf->Ln(10);
          $pdf->SetFont('Times','B',12);
          //$pdf->Write(5,utf8_decode('Anotése, Comuníquese, Regístre en su hoja de carrera funcionaria y archivase.'));
          $pdf->Cell(70,5,utf8_decode($dc_fin));
          $pdf->Ln(10);
          $y = $pdf->GetY();
          $pdf->SetY($y+35);
          $pdf->SetX(110);
          $pdf->Line(110,$y+34,194,$y+34);          
          $pdf->SetFont('Times','',12);
          $pdf->Cell(70,5,$alcalde1);
          $pdf->Ln();
          $pdf->SetX(145);
          $firma= '../../include/img/firmas/'.$alcalde.'.png';
          chmod($firma, 0755);
            if (is_readable($firma)) {
               # $pdf->Image($firma,118,$y,60,30);
            }
          
          $timbre= '../../include/img/firmas/timbre_dir.png';
          chmod($timbre, 0755); 
            if (is_readable($timbre)) {
                #$pdf->Image($timbre,77,$y,32,32);
            }
          $pdf->SetFont('Times','B',12);
          $pdf->Cell(60,5,utf8_decode($genalcalde.' '.$alcaldesub));
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
          $pdf->SetY($y+10);
          $pdf->Write(5,$responsables);
          $pdf->SetFont('Times','',10);
        }				

				}
        chmod($firma, 0000);
        chmod($timbre, 0000);
				$pdf->Output();			
?>
