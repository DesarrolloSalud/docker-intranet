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
            $consulta = "SELECT SAF.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CONTRA,SAF.USU_RUT_JD,SAF.USU_RUT_DIR,RSP.RSP_RESOL,SP.SP_CANT_DIA,DATE_FORMAT(SP.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SP.SP_FEC_FIN,'%d-%m-%Y'),DATE_FORMAT(RSP.RSP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(RSP.RSP_FEC_FIN,'%d-%m-%Y'),SAF.SAF_MOT,SAF.SAF_CANT_DIA,SAF.SAF_ANO_ACT,SAF.SAF_ANO_SIG,SAF.SAF_ESTA FROM SOL_PERMI SP,RES_SOL_PERMI RSP,SOL_ACU_FER SAF,USUARIO U WHERE (SP.SP_ID = RSP.SP_ID) AND (SAF.RSP_ID = RSP.RSP_ID) AND (SP.USU_RUT = U.USU_RUT) AND (SAF.SAF_ID = $folio_doc)";
						$respuesta = mysqli_query($cnn, $consulta);
            while ($rowSAF = mysqli_fetch_row($respuesta)){
                if ($rowSAF[17] == "AUTORIZADO DIR"){
                  $usu_rut        = $rowSAF[0];
                  $usu_nom        = $rowSAF[1];
                  $usu_app        = $rowSAF[2];
                  $usu_apm        = $rowSAF[3];
                  $usu_contra     = $rowSAF[4];
                  $usu_rut_jd     = $rowSAF[5];
                  $usu_rut_dir    = $rowSAF[6];
                  $rsp_resol      = $rowSAF[7];
                  $sp_cant_dia    = $rowSAF[8];
                  $sp_fec_ini     = $rowSAF[9];
                  $sp_fec_fin     = $rowSAF[10];
                  $rsp_fec_ini    = $rowSAF[11];
                  $rsp_fec_fin    = $rowSAF[12];
                  $saf_mot        = $rowSAF[13];
                  $saf_cant_dia   = $rowSAF[14];
                  $saf_ano_act    = $rowSAF[15];
                  $saf_ano_sig    = $rowSAF[16];
                  $saf_esta       = $rowSAF[17];
                  $consultaUSURDIR = "SELECT USU_CAR,USU_NOM,USU_APP,USU_APM FROM USUARIO WHERE USU_RUT = '$usu_rut_dir'";
                  $respuestaUSURDIR = mysqli_query($cnn, $consultaUSURDIR);
                  $rowUSURDIR = mysqli_fetch_row($respuestaUSURDIR);
                  //echo $consultaUSUR2;
                  if ($rowUSURDIR[0] == "Director" || $rowUSURDIR[0] == "Director (S)"){
                      $usu2_nom = $rowUSURDIR[1];
                      $usu2_app = $rowUSURDIR[2];
                      $usu2_apm = $rowUSURDIR[3];
                      //Agregamos la primera pagina al documento pdf
                      $pdf->AddPage('P',array(215.9,330.2));
                      //Seteamos el tiupo de letra y creamos el titulo de la pagina. No es un encabezado no se repetira
                      $pdf->SetFont('Arial','B',10);
                      $pdf->Header();
                      $pdf->SetX(0);
                      $pdf->Cell(210,5,'SOLICITUD DE ACUMULACION DE FERIADO',0,1,'C');
                      $pdf->SetX(160);
                      $pdf->Ln(20);
                      $pdf->Write(5,'Nombre de Funcionario : '.$usu_nom." ".$usu_app." ".$usu_apm);
                      $pdf->Ln(10);
                      $pdf->Write(5,'RUT : '.$usu_rut);
                      $pdf->SetX(100);
                      $pdf->Write(5,'Planta : '.$usu_contra);
                      $pdf->Ln(10);
                      $pdf->Write(5,'A Director : '.$usu2_nom." ".$usu2_app." ".$usu2_apm);
                      $pdf->Ln(15);
                      $pdf->Write(5,utf8_decode('Expone y Solicita lo siguiente :'));
                      $pdf->Ln(10);
                      $pdf->Write(5,utf8_decode('He tomado conocimiento de su relución en que '.$rsp_resol.' por razones de buen servicio, mi feriado legal de '.$sp_cant_dia.' días habiles solicitados para hacer uso a contrar del '.$sp_fec_ini.' hasta el '.$sp_fec_fin.'.'));
                      $pdf->Ln(10);
                      $pdf->Write(5,utf8_decode('La fecha indicada por usted, entre el '.$rsp_fec_ini.' hasta el '.$rsp_fec_fin.' no me es conveniente por : '.$saf_mot.'.'));
                      $pdf->Ln(10);
                      $pdf->Write(5,utf8_decode('Por lo anterior, solicito a usted, acumular el feriado de '.$saf_cant_dia.' días hábiles, correspondientes al año '.$saf_ano_act.' para hacer uso conjuntamente con el periodo del feriado legal del año '.$saf_ano_sig.'.'));
                      $pdf->Line(30,165,80,165);//firma jd
                      $pdf->Line(130,165,180,165); //firma usuario
                      $pdf->SetY(166);
                      $pdf->SetX(38);
                      $pdf->Write(5,utf8_decode('V° B° Jefe Directo'));
                      $pdf->SetX(140);
                      $pdf->Write(5,'Firma Interesado');
                      $pdf->SetY(190);
                      $pdf->SetX(20);
                      $pdf->Write(5,'AUTORIZADO : SI');
                      $pdf->Line(130,210,180,210); //firma director
                      $pdf->SetY(211);
                      $pdf->SetX(142);
                      $pdf->Write(5,'Firma Director');
                  }
                }
					  }
          }
        }
			$pdf->Output();
    }
?>