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
				class PDF extends FPDF{
					// Page header
					function Header(){
						// Logo
						$this->Image('../../include/img/header.jpg',1,1,210,20);
						$this->Ln(5);
					}
					function Footer(){
						$this->Image('../../include/img/footer.jpg',3,305,205,20);
						// Posición: a 1,5 cm del final
						$this->SetY(-15);
						// Arial italic 8
						$this->SetFont('Arial','I',8);
						// Número de página
						$this->Cell(0,10,'Pag '.$this->PageNo(),0,0,'R');
					}
				}
				date_default_timezone_set("America/Santiago");
				$pdf = new PDF();			
				$pdf->AddFont('GothamBooK','','GothamBook.php');
				$pdf->AddFont('GothamBold','','GOTHAM-BOLD.php');
				$dec_id = $_GET['id'];
				$query = "SELECT FOLIO_DOC FROM DECRE_DETALLE WHERE DF_ID = $dec_id ORDER BY FOLIO_DOC ASC";
				$respuestaQuery = mysqli_query($cnn, $query);
	      if (mysqli_num_rows($respuestaQuery) != 0){
        	while ($rowDEC = mysqli_fetch_array($respuestaQuery, MYSQLI_NUM)){
						$folio_doc = $rowDEC[0];
						//"SELECT CO_ID,DOC_ID,COME_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_CAT,USU_RUT_JD,CO_VIA,CO_PAS,CO_PEA,CO_PAR,CO_MOT,CO_DES,CO_DIA,CO_COM,USUARIO.USU_FIR,USU_RUT_DIR,USUARIO.USU_CAR,USUARIO.EST_ID FROM COME_PERMI INNER JOIN USUARIO ON COME_PERMI.USU_RUT=USUARIO.USU_RUT WHERE (CO_ID ='$coid')";
						$consulta = "SELECT CO_ID,DOC_ID,COME_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_CAT,USU_RUT_JD,CO_VIA,CO_PAS,CO_PEA,CO_PAR,CO_MOT,CO_DES,CO_DIA,CO_COM,USUARIO.USU_FIR,USU_RUT_DIR,USUARIO.USU_CAR,USUARIO.EST_ID  FROM COME_PERMI INNER JOIN USUARIO ON COME_PERMI.USU_RUT=USUARIO.USU_RUT WHERE (CO_ID ='$folio_doc')";
						$respuesta = mysqli_query($cnn, $consulta);
						//echo $consulta;
						if (mysqli_num_rows($respuesta) == 1){
						 //while ($rowOE = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
							$numreg = mysqli_num_rows($respuesta);
							$rowOED = mysqli_fetch_row($respuesta);
							$str = $rowOED[1];
							$usu_rut = $rowOED[2];
							$usu_nom = $rowOED[3];
							$usu_app = $rowOED[4];
							$usu_apm = $rowOED[5];
							$usu_cat = $rowOED[6];
							$usu_rut_jd = $rowOED[7];
							if($rowOED[8] == 'on'){
								$viatico="X";
							}else{
								$viatico="";
							}
							if($rowOED[9] == 'on'){
								$pasaje="X";
							}else{
								$pasaje="";
							}
							if($rowOED[10] == 'on'){
								$peaje="X";
							}else{
								$peaje="";
							}
							if($rowOED[11] == 'on'){
								$parquimetro="X";
							}else{
								$parquimetro="";
							}
							$motivo = utf8_decode($rowOED[12]);
							$destino = utf8_decode($rowOED[13]);
							$dia = $rowOED[14];
							if($rowOED[15] == 'on'){
								$combustible="X";
							}else{
								$combustible="";
							}
							$firma = $rowOED[16];
							$usu_rut_dir = $rowOED[17];
							$usu_car = $rowOED[18];
							$est_id = $rowOED[19];
							$pdf->AddPage('P',array(215.9,330.2));
							$pdf->SetFont('Arial','B',12);
              $pdf->Header();
              $pdf->SetX(10);
              $pdf->Ln(5);
							$pdf->Cell(0,6,'COMETIDO FUNCIONARIO',0,0,'C');
              $pdf->Ln(5);
  						$pdf->SetFont('Arial','B',12);
              $pdf->Header();
              $pdf->SetX(10);
              //$pdf->Ln(2);
              $pdf->Ln(2);
              $pdf->SetFontSize(10);
              $pdf->Write(5,'NOMBRE FUNCIONARIO : '.$usu_nom." ".$usu_app." ".$usu_apm);
              $pdf->Ln(5);
              $pdf->Write(5,'RUT: '. $usu_rut ."  ". utf8_decode('CATEGORÍA: ').$usu_cat);
              $pdf->Ln(15);
              $pdf->Write(5,utf8_decode('VIÁTICO:  '));
              $pdf->Cell(10,5,$viatico,1,0,'C');
              $pdf->Write(5,utf8_decode(' DÍAS:  '));
              $pdf->Cell(10,5,$dia,1,0,'C');
              $pdf->Write(5,utf8_decode(' PASAJES:  '));
              $pdf->Cell(10,5,$pasaje,1,0,'C');
              $pdf->Write(5,utf8_decode(' COMBUSTIBLE:  '));
              $pdf->Cell(10,5,$combustible,1,0,'C');
              $pdf->Write(5,utf8_decode(' PEAJE:  '));
              $pdf->Cell(10,5,$peaje,1,0,'C');
              $pdf->Write(5,utf8_decode(' PARQUIMETRO:  '));
              $pdf->Cell(10,5,$parquimetro,1,0,'C');
							//$DetalleCometido = "SELECT DATE_FORMAT(CD_DIA, '%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID ='$folio_doc')";
							$pdf->Ln(10);
							$pdf->Write(5,'MOTIVO : '.$motivo,1,'L'); 
							$pdf->Ln(10);
							$pdf->Write(5,'DESTINO : '.$destino,1,'L'); 
							$pdf->Ln(10);
							$DetalleCometido = "SELECT DATE_FORMAT(CD_DIA, '%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID ='$folio_doc')";
							$RespMiCome = mysqli_query($cnn,$DetalleCometido);                
							$conta=92;
							$contax=22;
							$cuenta=0; 
							$pdf->SetFillColor(232,232,232);
							$pdf->SetFont('Arial','B',10);
							$pdf->SetY(87);
							$pdf->SetX($contax);
							$pdf->Cell(25,6,'FECHA',1,0,'C',1);
							$pdf->Cell(55,6,'HORA INICIO',1,0,'C',1);                
							$pdf->Cell(55,6,utf8_decode('HORA FIN'),1,0,'C',1);
							$pdf->Cell(30,6,utf8_decode('PORCENTAJE'),1,0,'C',1);
							$pdf->Ln();
							while ($row = mysqli_fetch_array($RespMiCome)){
								if($cuenta <= 4){
									$pdf->SetY($conta);
									$pdf->SetY($conta+1); 
									$pdf->SetX($contax);
									$pdf->Cell(25,10,$row[0],1,0,'C');
									$pdf->Cell(55,10,$row[1],1,'C');                    
									$pdf->Cell(55,10,$row[2],1,'C');
									$pdf->Cell(30,10,$row[3],1,'C'); 
									$conta=$conta+10;
									$cuenta = $cuenta+1;
									$pdf->Ln(6);  
								}                   
								if ($cuenta == 5 || $cuenta == $numreg){
									$pdf->SetY(185);
									$pdf->SetX(17);
									$pdf->Line(15,184,55,184);
									$pdf->Write(5,utf8_decode('V° B° JEFE DIRECTO'),1,'L');
									if((($usu_car == "Director") || ($usu_car == "Director (S)")) && ($est_id == 1)){
										$pdf->SetX(95);
										$pdf->Line(85,184,125,184);
										$pdf->Write(5,'ALCALDE',1,'R');
									}else{
										$pdf->SetX(78);
										$pdf->Line(85,184,125,184);
										$pdf->Write(5,'DIRECTOR ESTABLECIMIENTO',1,'R');
									}
									$pdf->SetX(155);
									$pdf->Line(154,184,194,184);
									$pdf->Write(5,'FIRMA FUNCIONARIO ',1,'R');
									$firma= '../../include/img/firmas/'.$usu_rut.'.png';
                  $firmausu = $firma;
                  chmod($firmausu, 0755);
									if (is_readable($firma)) {
										$pdf->Image($firma,154,163,40,20);
									} 
									$firma= '../../include/img/firmas/'.$usu_rut_jd.'.png'; 
                  $firmajd = $firma;
                  chmod($firmajd, 0755);
									if (is_readable($firma)) {
										$pdf->Image($firma,15,163,40,20);
									} 
									$firma= '../../include/img/firmas/'.$usu_rut_dir.'.png';
                  $firmadir = $firma;
                  chmod($firmadir, 0755);
									if (is_readable($firma)) {
										$pdf->Image($firma,85,163,40,20);
									} 
									$pdf->Ln(10);
									$pdf->SetX(90);
									$pdf->Write(5,utf8_decode('DECRETO N°: '),1,'L'); //AGREGAR CUANDO SE CREE MANTENEDOR DECRETOS
									$pdf->Ln(5);
									$pdf->SetX(10);
									$pdf->Write(5,utf8_decode('VISTOS :'));
									$pdf->Ln(10);
									$pdf->Write(5,utf8_decode('Ley 19.378, Estatuto de Atención Primaria de Salud Municipal; Resolución N° 520, del 15711/96 de la Contraloría General de la República, la Ley 18.695 Orgánica Constitucional de Municipalidades, y su textp refundido fijado por el DFL 1-19.704, del 27/01/01 del Ministerio del Interior, Código del Trabajo y Contratos Prestacion de Servicios a Honorarios suscritos.'));
									$pdf->Ln(10);
									$pdf->Write(5,utf8_decode('DECRETO :'));
									$pdf->Ln(10);
									$pdf->Write(5,utf8_decode('Autorizase a la persona individualizado (a) en la presente solicitud, para hacer uso del derecho indicado en las condiciones y fechas señaladas.'));
									$pdf->Ln(8);
									$pdf->Write(5,utf8_decode('ANOTESE, TRANSCRIBASE, COMUNIQUESE Y ARCHIVESE'));
									$pdf->Ln(34);
									$pdf->Line(20,291,80,291);
									$pdf->SetX(28);
									$pdf->Write(5,utf8_decode('SECRETARIA MUNICIPAL'));
									$pdf->Line(120,291,180,291);
									$pdf->SetX(123);                    
									$pdf->Write(5,utf8_decode('DIRECTOR SALUD MUNICIPAL'));
									$pdf->Ln(30); 
								}
								if($cuenta == 5){
									$conta=30;
									$contax=22;
									$pdf->SetY($conta);
									$pdf->SetX($contax);
									$pdf->AddPage('P',array(215.9,330.2));
									$pdf->SetFont('Arial','B',8);
									$pdf->SetY($conta-10);
									$pdf->SetX(22);
									$pdf->Cell(0,5,'CONTINUACION DETALLE COMETIDO FUNCIONARIO',0,1,'L');
									$pdf->SetY($conta);
									$pdf->SetFontSize(10);
									$pdf->SetX($contax);
								}
								if($cuenta >= 4){
									$pdf->SetY($conta);
									$pdf->SetY($conta+1); 
									$pdf->SetX($contax);
									$pdf->Cell(25,10,$row[0],1,0,'C');
									$pdf->Cell(55,10,$row[1],1,'C');                    
									$pdf->Cell(55,10,$row[2],1,'C');
									$pdf->Cell(30,10,$row[3],1,'C'); 
									$conta=$conta+10;
									$cuenta = $cuenta+1;
									$pdf->Ln(6);   
								}                      
							}   
						}
					}
				}
        chmod($firmausu, 0000);
        chmod($firmajd, 0000);
        chmod($firmadir, 0000);
				$pdf->Output();
    }
?>
