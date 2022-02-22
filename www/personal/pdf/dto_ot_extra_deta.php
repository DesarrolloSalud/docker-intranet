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
      
        $query1 = "SELECT DF_FEC,DF_NUM,DF_TEXT_VISTOS,DF_TEXT_CONSIDERANDO,DF_TEXT_DECRETO,DF_TEXT_FIN,DF_NOM_DIR,DF_DIR_SUB,DF_DIR_GEN,DF_NOM_SEC,DF_SEC_SUB,DF_SEC_GEN,DF_RESPONSABLES FROM DECRETOS_FOR WHERE (DF_ID= ".$iddcre.")";          
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
         }
        class PDF extends FPDF{
						function Header(){
								$this->Image('../../include/img/header.jpg',1,1,210,20);
								$this->SetY(30);
								$this->SetX(100);
								$this->SetFont('Times','B',12);
								global $dcact_id; 
								$this->Ln();
								$this->SetX(100);
								global $dc_fec;
								$fec_format = obtenerFechaEnLetra($dc_fec);
								$this->Ln(10);   
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
        $pdf->Ln(10);
				$pdf->SetFont('Times','B',12);
        $pdf->Ln(5);
				$pdf->SetFont('Times','',12);
				$y = $pdf->GetY();
        
				$query = "SELECT OT_EXTRA_AUT_F.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT,OT_EXTRA_AUT_F.OEA_FEC_INI,OT_EXTRA_AUT_F.OEA_FEC_FIN,OT_EXTRA_AUT_F.OEA_LJ,OT_EXTRA_AUT_F.OEA_LJ_HI, OT_EXTRA_AUT_F.OEA_LJ_HF, OT_EXTRA_AUT_F.OEA_VI_HI,OT_EXTRA_AUT_F.OEA_VI_HF, OT_EXTRA_AUT_F.OEA_SDF_HI, OT_EXTRA_AUT_F.OEA_SDF_HF, OT_EXTRA_AUT_F.OEA_VI, OT_EXTRA_AUT_F.OEA_SDF FROM OT_EXTRA_ENC INNER JOIN OT_EXTRA_AUT_F ON OT_EXTRA_ENC.OEE_ID=OT_EXTRA_AUT_F.OEE_ID INNER JOIN USUARIO ON OT_EXTRA_AUT_F.USU_RUT = USUARIO.USU_RUT WHERE (OT_EXTRA_ENC.OEE_ID= '$iddcre')";
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
        $pdf->Cell(50,6,'NOMBRE',1,0,'C',1);
        $pdf->Cell(22,6,'RUT',1,0,'C',1);                
        $pdf->Cell(15,6,utf8_decode('CATE.'),1,0,'C',1);
        $pdf->Cell(23,6,utf8_decode('PERÍODO'),1,0,'C',1);
        $pdf->Cell(53,6,'HORARIO',1,0,'C',1);
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
							$pdf->Cell(50,6,'NOMBRE',1,0,'C',1);
              $pdf->Cell(22,6,'RUT',1,0,'C',1);                
              $pdf->Cell(15,6,utf8_decode('CATE.'),1,0,'C',1);
              $pdf->Cell(23,6,utf8_decode('PERÍODO'),1,0,'C',1);
              $pdf->Cell(53,6,'HORARIO',1,0,'C',1);
							$pdf->Ln();
						}
            $pdf->SetFont('Times','',10);
            $largo = strlen(utf8_decode($row[1])." ".utf8_decode($row[2])." ".utf8_decode($row[3]));  
            
            while($largo < 30){
              $espacio = "  " . $espacio;
              $largo= $largo +1;
            } 
            $y1 = $pdf->GetY();          
            $pdf->MultiCell(50,9,utf8_decode($row[1])." ".utf8_decode($row[2])." ".utf8_decode($row[3]).$espacio,1,'C');
						$y2 = $pdf->GetY();            
            $height = $y2 - $y1;
            $pdf->SetXY(80,$y1);
						$pdf->Cell(22,$height,$row[0],1,'C');
						$pdf->SetXY(102,$y1);
						$pdf->Cell(15,$height,$row[4],1,0,'C');
						$pdf->SetXY(117,$y1);
						$pdf->MultiCell(23,6,$row[5]." hasta ".$row[6],1,'C');
						$pdf->SetXY(140,$y1);
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
				}
				$pdf->Output();			
?>
