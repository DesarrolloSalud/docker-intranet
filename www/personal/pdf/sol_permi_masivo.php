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
            $consulta = "SELECT DOCUMENTO.DOC_NOM,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_RUT,SOL_PERMI.USU_RUT_JD,SOL_PERMI.USU_RUT_DIR,USUARIO.USU_CAT,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_JOR,SOL_PERMI.SP_MOT,SOL_PERMI.SP_ESTA,DOCUMENTO.DOC_ID,SOL_PERMI.SP_CANT_DC,SOL_PERMI.SP_HOR_INI,SOL_PERMI.SP_HOR_FIN,SOL_PERMI.SP_TIPO,SOL_PERMI.SP_ANO,DATE_FORMAT(SOL_PERMI.SP_FEC,'%d-%m-%Y') FROM SOL_PERMI, DOCUMENTO, USUARIO WHERE (SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID) AND (SOL_PERMI.USU_RUT = USUARIO.USU_RUT) AND (SOL_PERMI.SP_ID = '$folio_doc')";
						$respuesta = mysqli_query($cnn, $consulta);
						//echo $consulta;
              while ($rowSP = mysqli_fetch_row($respuesta)){
                if ($rowSP[13] == "AUTORIZADO DIR"){
                    $doc_nom        = $rowSP[0];
                    $usu_nom        = $rowSP[1];
                    $usu_app        = $rowSP[2];
                    $usu_apm        = $rowSP[3];
                    $usu_rut        = $rowSP[4];
                    $usu_rut_jd     = $rowSP[5];
                    $usu_rut_dir    = $rowSP[6];
                    $usu_cat        = $rowSP[7];
                    $sp_cant_dia    = $rowSP[8];
                    $sp_fec_ini     = $rowSP[9];
                    $sp_fec_fin     = $rowSP[10];
                    $sp_jor         = $rowSP[11];
                    $sp_mot         = $rowSP[12];
                    $sp_esta        = $rowSP[13];
                    $doc_id         = $rowSP[14];
                    $sp_cant_dc     = $rowSP[15];
                    $sp_hor_ini     = $rowSP[16];
                    $sp_hor_fin     = $rowSP[17];
                    $sp_tipo        = $rowSP[18];
                    $sp_año         = $rowSP[19];
                    $sp_fec         = $rowSP[20];
                    $consultaUSUR2 = "SELECT USU_JEF,USU_NOM,USU_APP,USU_APM FROM USUARIO WHERE USU_RUT = '$usu_rut_jd'";
                    $respuestaUSUR2 = mysqli_query($cnn, $consultaUSUR2);
                    $rowUSUR2 = mysqli_fetch_row($respuestaUSUR2);
                    //echo $consultaUSUR2;
                    if ($rowUSUR2[0] == "SI"){
                        $usu2_nom = $rowUSUR2[1];
                        $usu2_app = $rowUSUR2[2];
                        $usu2_apm = $rowUSUR2[3];
                        //calcular dias administrativos
                        date_default_timezone_set("America/Santiago");
                        $año_actual = date("Y");
                        $query_banco_adm = "SELECT BD_ID, BD_ADM,BD_ADM_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$año_actual')";
                        $resultado_banco_adm = mysqli_query($cnn, $query_banco_adm);
                        if (mysqli_num_rows($resultado_banco_adm) != 0){
                            while ($row_adm = mysqli_fetch_array($resultado_banco_adm)){
                                $num_adm        = $row_adm[1];
                                $num_adm_usado  = $row_adm[2];
                            }
                            $num_dias_aactual_admin = 6;
                            $num_dias_total_admin = 6;
                            $num_dias_pendientes_admin = $num_adm;
                            $num_dias_usados_admin = $num_adm_usado;
                        }
                        //calcular dias feriados
                        //ver si el permiso es de este año o el proximo
                        $año_siguiente = $año_actual + 1;
                        if($sp_año == $año_actual){
                            $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FLA, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$año_actual')";
                            $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                            if (mysqli_num_rows($resultado_banco_fl) != 0){
                                while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                                    $num_fl         = $row_fl[1];
                                    $num_fla        = $row_fl[2];
                                    $num_fl_usado   = $row_fl[3];
                                }
                                $num_dias_acumulados = $num_fla;
                                $total_dias = $num_fl + $num_fla;
                                $dias_usados = $num_fl_usado;
                                $num_dias_actual = $num_fl;
                                $dias_pendientes = $num_fl + $num_fla;
                            }
                        }elseif($sp_año == $año_siguiente){
                            $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FLA, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$año_siguiente')";
                            $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                            if (mysqli_num_rows($resultado_banco_fl) != 0){
                                while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                                    $num_fl         = $row_fl[1];
                                    $num_fla        = $row_fl[2];
                                    $num_fl_usado   = $row_fl[3];
                                }
                                $num_dias_acumulados = $num_fla;
                                $total_dias = $num_fl + $num_fla;
                                $dias_usados = $num_fl_usado;
                                $num_dias_actual = $num_fl;
                                $dias_pendientes = $num_fl + $num_fla;
                            }
                        }
                        //calculo horas
                        $FecActual = date("Y-m-d");
                        list($año_actual, $mes_actual, $dia_actual) = split('[-]', $FecActual);
                        $FecIni = ($año_actual - 2)."-".$mes_actual."-".$dia_actual;
                        $query_banco_hora = "SELECT BH_SALDO,BH_CANT FROM BANCO_HORAS WHERE (USU_RUT = '$usu_rut') AND (BH_SALDO > 0) AND (BH_FEC BETWEEN '$FecIni' AND '$FecActual') AND ((BH_TIPO = 'INICIAL') OR (BH_TIPO = 'INGRESO')) ORDER BY BH_FEC ASC";
                        $resultado_hora = mysqli_query($cnn, $query_banco_hora);

                        if (mysqli_num_rows($resultado_hora) != 0){
                            while ($row_hora = mysqli_fetch_array($resultado_hora)){
                                $horas  = $row_hora[0] + $horas;
                                $total = $row_hora[1] + $total;
                            }
                            $horas_informadas = $total;
                            $horas_solicitadas = $total - $horas;
                            $horas_disponibles = $horas;
                        }

                        //Instaciamos la clase para genrear el documento pdf
                        $pdf->AddPage('P',array(215.9,330.2));
                        //Seteamos el tiupo de letra y creamos el titulo de la pagina. No es un encabezado no se repetira
                        $pdf->SetFont('Arial','B',10);
                        $pdf->Header();
                        $pdf->SetX(140);
                        $pdf->Write(5,'FECHA :');
                        $pdf->SetX(160);
                        $pdf->Cell(30,5,$sp_fec,1,1,'C');
                        $pdf->Ln(1);
                        $pdf->SetX(140);
                        $pdf->Write(5,'FOLIO  :');
                        $pdf->SetX(160);
                        $pdf->Cell(30,5,$folio_doc,1,1,'C');
                        $pdf->Ln(10);
                        $pdf->Write(5,'SOLICITUD DE : '.$doc_nom);
                        $pdf->Ln(10);
                        $pdf->Write(5,'DE : '.$usu_nom." ".$usu_app." ".$usu_apm);
                        $pdf->Ln(5);
                        $pdf->Write(5,'A : '.$usu2_nom." ".$usu2_app." ".$usu2_apm);
                        $pdf->Ln(5);
                        $pdf->Write(5,'RUT : '.$usu_rut);
                        $pdf->SetX(100);
                        $pdf->Write(5,'CATEGORIA : '.$usu_cat);
                        $pdf->Ln(10);
                        $pdf->Write(5,utf8_decode('Solicita autorización para los siguientes dias :'));
                        $pdf->Ln(10);
                        //$pdf->Write(5,$año_siguiente);
                        if ($sp_tipo != "HORAS"){
                            $pdf->SetX(15);
                            $pdf->Cell(25,5,$sp_cant_dia,1,0,'C');
                            $pdf->SetX(40);
                            $pdf->Write(5,utf8_decode('N° DE DIAS'));
                            $pdf->SetX(62);
                            $pdf->Cell(25,5,$sp_fec_ini,1,0,'C');
                            $pdf->SetX(87);
                            $pdf->Write(5,utf8_decode('DESDE'));
                            $pdf->SetX(102);
                            $pdf->Cell(25,5,$sp_fec_fin,1,0,'C');
                            $pdf->SetX(127);
                            $pdf->Write(5,utf8_decode('HASTA'));
                            $pdf->SetX(142);
                            $pdf->Cell(25,5,$sp_jor,1,0,'C');
                            $pdf->SetX(167);
                            $pdf->Write(5,utf8_decode('JORNADA'));
                        }else{
                            $pdf->SetX(15);
                            $pdf->Cell(25,5,$sp_cant_dc,1,0,'C');
                            $pdf->SetX(40);
                            $pdf->Write(5,utf8_decode('HORAS'));
                            $pdf->SetX(62);
                            $pdf->Cell(25,5,$sp_fec_ini,1,0,'C');
                            $pdf->SetX(87);
                            $pdf->Write(5,utf8_decode('FECHA'));
                            $pdf->SetX(102);
                            $pdf->Cell(25,5,$sp_hor_ini,1,0,'C');
                            $pdf->SetX(127);
                            $pdf->Write(5,utf8_decode('INICIO'));
                            $pdf->SetX(142);
                            $pdf->Cell(25,5,$sp_hor_fin,1,0,'C');
                            $pdf->SetX(167);
                            $pdf->Write(5,utf8_decode('TERMINO'));
                        }
                        if($doc_id == 1 || $doc_id == 2){
                            $pdf->Ln(10);
                            $pdf->Cell(180,20,'',1,1);
                            $pdf->SetY(93);
                        }elseif ($doc_id == 3){
                            $pdf->Ln(15);
                            $pdf->Cell(180,10,'',1,1); 
                            $pdf->SetY(98);
                        }
                        $pdf->SetX(15);
                        //$pdf->SetFont('Arial',,8);
                        if ($doc_id == 1){
                            //feriado
                            $pdf->Cell(10,5,$num_dias_acumulados,1,0,'C');
                            $pdf->Write(5,utf8_decode('N° DIAS AÑO ANTERIOR'));
                            $pdf->SetX(75);
                        }elseif ($doc_id == 2){
                            //administrativo
                            $pdf->Cell(10,5,'N.A.',1,0,'C');
                            $pdf->Write(5,utf8_decode('N° DIAS AÑO ANTERIOR'));
                            $pdf->SetX(75);
                        }
                        if ($doc_id == 1){
                            //feriado
                            $pdf->Cell(10,5,$num_dias_actual,1,0,'C');
                            $pdf->Write(5,utf8_decode('N° DIAS AÑO ACTUAL'));
                            $pdf->SetX(135);
                        }elseif ($doc_id == 2){
                            //administrativo
                            $pdf->Cell(10,5,$num_dias_aactual_admin,1,0,'C');
                            $pdf->Write(5,utf8_decode('N° DIAS AÑO ACTUAL'));
                            $pdf->SetX(135);
                        }
                        if ($doc_id == 1){
                            //feriado
                            $pdf->Cell(10,5,$total_dias,1,0,'C');
                            $pdf->Write(5,utf8_decode('TOTAL DE DIAS'));
                            $pdf->SetY(102);
                            $pdf->SetX(50);
                        }elseif ($doc_id == 2){
                            //administrativo
                            $pdf->Cell(10,5,$num_dias_total_admin,1,0,'C');
                            $pdf->Write(5,utf8_decode('TOTAL DE DIAS'));
                            $pdf->SetY(102);
                            $pdf->SetX(50);
                        }
                        if ($doc_id == 1){
                            //feriado
                            $pdf->Cell(10,5,$dias_usados,1,0,'C');
                            $pdf->Write(5,utf8_decode('DIAS USADOS'));
                            $pdf->SetX(100);
                        }elseif ($doc_id == 2){
                            //administrativo
                            $pdf->Cell(10,5,$num_dias_usados_admin,1,0,'C');
                            $pdf->Write(5,utf8_decode('DIAS USADOS'));
                            $pdf->SetX(100);
                        }
                        if ($doc_id == 1){
                            //feriado
                            $pdf->Cell(10,5,$dias_pendientes,1,0,'C');
                            $pdf->Write(5,utf8_decode('N° DIAS PENDIENTES'));
                        }elseif ($doc_id == 2){
                            //administrativo
                            $pdf->Cell(10,5,$num_dias_pendientes_admin,1,0,'C');
                            $pdf->Write(5,utf8_decode('N° DIAS PENDIENTES'));
                        }
                        if($doc_id == 3){
                            //complementario
                            $pdf->Cell(10,5,$horas_solicitadas,1,0,'C');
                            $pdf->Write(5,utf8_decode('HORAS USADAS'));
                            $pdf->SetX(75);
                            $pdf->Cell(10,5,$horas_informadas,1,0,'C');
                            $pdf->Write(5,utf8_decode('HORAS INFORMADAS'));
                            $pdf->SetX(135);
                            $pdf->Cell(10,5,$horas_disponibles,1,0,'C');
                            $pdf->Write(5,utf8_decode('HORAS DISPONIBLES'));
                        }
                        $pdf->SetY(120);
                        $pdf->Write(5,'MOTIVO : '.$sp_mot);
                        $pdf->Line(130,135,180,135);
                        $pdf->Ln(15);
                        $pdf->SetX(128);
                        $firma= '../../include/img/firmas/'.$usu_rut.'.png';
                        if (is_readable($firma)) {
                            $pdf->Image($firma,135,112,40,20);
                        } 
                        $pdf->Write(5,'FIRMA FUNCIONARIO');
                        $pdf->Ln(2);
                        if($usu_rut_jd == '15.103.393-8' || $usu_rut_jd == '8.437.701-5' || $usu_rut_jd == '8.934.321-6'){
                          $pdf->Write(5,'AUTORIZADO :');
                        }else{
                          $pdf->Write(5,'AUTORIZADO : SI');
                        }
                        $pdf->Line(20,164,80,164);
                        $pdf->Ln(27);
                        $pdf->SetX(21);
                        $firma= '../../include/img/firmas/'.$usu_rut_jd.'.png'; 
                        if (is_readable($firma)) {
                            $pdf->Image($firma,30,142,40,20);
                        } 
                        $pdf->Write(5,'FIRMA Y TIMBRE JEFE DIRECTO');
                        $pdf->Line(120,164,180,164);
                        $pdf->SetX(127);
                        $firma= '../../include/img/firmas/'.$usu_rut_dir.'.png'; 
                        if (is_readable($firma)) {
                            $pdf->Image($firma,130,142,40,20);
                        } 
                        $pdf->Write(5,'FIRMA Y TIMBRE DIRECTOR');
                        $pdf->Ln(10);
                        $pdf->SetX(100);
                        $pdf->Write(5,utf8_decode('DECRETO ALCALDICIO N° _______________/(MP)'));
                        $pdf->Ln(5);
                        $pdf->SetX(100);
                        $pdf->Write(5,utf8_decode('FECHA : ___________________'));
                        $pdf->Ln(10);
                        $pdf->Write(5,utf8_decode('CONSIDERANDO :'));
                        $pdf->Ln(5);
                        $pdf->SetX(45);
                        $pdf->Write(5,utf8_decode('La presente solicitud autorizada por el Jefe Directo y/o Director.'));
                        $pdf->Ln(10);
                        $pdf->Write(5,utf8_decode('VISTOS :'));
                        $pdf->Ln(10);
                        //$pdf->SetX(15);
                        $pdf->Write(5,utf8_decode('Ley 19.378, Estatuto de Atención Primaria de Salud Municipal; Resolución N° 520, del 15711/96 de la Contraloría General de la República, la Ley 18.695 Orgánica Constitucional de Municipalidades, y su textp refundido fijado por el DFL 1-19.704, del 27/01/01 del Ministerio del Interior, Código del Trabajo y Contratos Prestacion de Servicios a Honorarios suscritos.'));
                        $pdf->Ln(10);
                        $pdf->Write(5,utf8_decode('DECRETO :'));
                        $pdf->Ln(10);
                        //$pdf->SetX(15);
                        $pdf->Write(5,utf8_decode('Autorizase a la persona individualizado (a) en la presente solicitud, para hacer uso del derecho indicado en las condiciones y fechas señaladas.'));
                        $pdf->Ln(8);
                        //$pdf->SetX(15);
                        $pdf->Write(5,utf8_decode('ANOTESE, TRANSCRIBASE, COMUNIQUESE Y ARCHIVESE'));
                        $pdf->Ln(35);

                        $pdf->Line(20,300,80,300);                    
                        $pdf->SetY(300);
                        $pdf->SetX(28);
                        $pdf->Write(5,utf8_decode('SECRETARIA MUNICIPAL'));
                        $pdf->Line(120,300,180,300);
                        /*$firma= '../../include/img/firmas/'.$usu_rut_dir.'.png'; //AGREGAR FIRMA CUANDO ESTÉ DECRETADO
                            if (is_readable($firma)) {
                                $pdf->Image($firma,130,279,40,20);
                            }*/ 
                        $pdf->SetX(123); 
                        if($usu_rut_jd == '15.103.393-8' || $usu_rut_jd == '8.437.701-5' || $usu_rut_jd == '8.934.321-6'){
                          $pdf->Write(5,utf8_decode('ALCALDE'));
                        }else{
                          $pdf->Write(5,utf8_decode('DIRECTOR SALUD MUNICIPAL'));
                        }
                          $pdf->Ln(30);
                        //$pdf->Write(5,$cal_dias_fer_aact);
                    }
                }
              }
              $horas_informadas = 0;
              $horas_solicitadas = 0;
              $horas_disponibles = 0;
              $horas = 0;
              $total = 0;
					}
				}	
			$pdf->Output();
    }
?>