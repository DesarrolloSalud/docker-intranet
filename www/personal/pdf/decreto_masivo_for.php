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
    $df_id = $_GET['id']; 
		$doc_id = $_GET['doc_id'];
    $consulta = "SELECT DF_FEC,DF_NUM,DATE_FORMAT(DF_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(DF_FEC_FIN,'%d-%m-%Y'),DF_TEXT_VISTOS,DF_TEXT_CONSIDERANDO,DF_TEXT_DECRETO,DF_TEXT_FIN,DF_NOM_DIR,DF_NOM_SEC,DF_ESTA,DF_RESPONSABLES,DF_DIR_SUB,DF_SEC_SUB,DF_DIR_GEN,DF_SEC_GEN FROM DECRETOS_FOR WHERE (DF_ID = '$df_id')";
    $respuesta = mysqli_query($cnn, $consulta);
    //echo $consulta;
    if (mysqli_num_rows($respuesta) == 1){
      $rowDF = mysqli_fetch_row($respuesta);
      if ($rowDF[10] == "CREADO"){
        $df_fec        = $rowDF[0];
        $df_num        = $rowDF[1];
        $fec_ini       = $rowDF[2];
        $fec_fin       = $rowDF[3];
        $text_visto    = $rowDF[4];
				$text_consi    = $rowDF[5];
				$text_decreto  = $rowDF[6];
        $text_fin      = $rowDF[7];
        $nom_dir       = $rowDF[8];
        $nom_sec       = $rowDF[9];
        $df_esta       = $rowDF[10];
				$df_respo			 = $rowDF[11];
				$dir_sub			 = $rowDF[12];
				$sec_sub			 = $rowDF[13];
				$dir_gen			 = $rowDF[14];
				$sec_gen			 = $rowDF[15];
        class PDF extends FPDF{
          // Page header
          function Header(){
            // Logo
            $this->Image('../../include/img/header.jpg',1,1,210,20);
            $this->SetY(30);
            $this->SetX(100);
            $this->SetFont('Times','B',12);
            $dec_alc = utf8_encode("DECRETO ALCALDICIO N");
            global $df_num;
            $this->Write(5,utf8_encode($dec_alc.' :  __/'.$df_num.' (SALUD)'));   
            $this->Ln();
            $this->SetX(100);
            global $df_fec;
						$fec_format = obtenerFechaEnLetra($df_fec);
						$this->Write(5,'Rengo,'.$fec_format);
						$this->Ln(10);         
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
				$text_visto = utf8_decode($text_visto);
				$txt_visto = str_replace("<br />", "\n", $text_visto);			          
				$txt_visto_mostrar = str_replace("?", "*", $txt_visto);
				nl2br($txt_visto_mostrar);
				$pdf->SetFont('Times','B',12);
				$pdf->Write(5, "VISTOS :");
				$pdf->Ln(10);
				$pdf->SetFont('Times','',12);
				$pdf->MultiCell(165,5,$txt_visto_mostrar,'J');
				$pdf->Ln();
				$pdf->SetFont('Times','B',12);
				$pdf->Write(5, "CONSIDERANDO :");
				$pdf->Ln(10);
				$text_consi = utf8_decode($text_consi);
				$txt_consi = str_replace("<br />", "\n", $text_consi);			          
				$txt_consi_mostrar = str_replace("?", "*", $txt_consi);
				nl2br($txt_consi_mostrar);
				$pdf->SetFont('Times','',12);
				$pdf->MultiCell(165,5,$txt_consi_mostrar,'J');
				$pdf->Ln();
				$pdf->SetFont('Times','B',12);
				$pdf->Write(5, "DECRETO :");
				$pdf->Ln(10);
				$text_decreto = utf8_decode($text_decreto);
				$txt_decreto = str_replace("<br />", "\n", $text_decreto);			          
				$txt_decreto_mostrar = str_replace("?", "*", $txt_decreto);
				nl2br($txt_decreto_mostrar);
				$pdf->SetFont('Times','',12);
				$pdf->MultiCell(165,5,$txt_decreto_mostrar,'J');
				$y = $pdf->GetY();	
				$y = $y + 10;
				$pdf->SetY($y);
				//$pdf->Write(5,$y);
        //hacer tabla segun tipo de documento
				if($doc_id == 1){
					$pdf->SetFont('Times','B',12);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(165,6,'PERMISO FERIADO LEGAL',1,0,'C',1);
					$pdf->Ln();
					$pdf->SetFont('Times','B',10);
					$pdf->Cell(25,12,'RUT',1,0,'C',1);
					$pdf->MultiCell(65,12,'NOMBRE COMPLETO',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(120);
					$pdf->Cell(25,12,'DIAS',1,0,'C',1);
					$pdf->MultiCell(25,6,'FECHA INICIO',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(170);
					$pdf->MultiCell(25,12,'FECHA FIN',1,'C',1);
					//$pdf->Ln();
					$query_sp = "SELECT S.SP_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SP_CANT_DIA,DATE_FORMAT(S.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(S.SP_FEC_FIN,'%d-%m-%Y')  FROM DECRE_DETALLE DD INNER JOIN SOL_PERMI S ON DD.FOLIO_DOC = S.SP_ID INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE DD.DF_ID = $df_id ORDER BY U.EST_ID,S.SP_FEC ASC";
					$resultado_sp = mysqli_query($cnn, $query_sp);
					while($row_sp = mysqli_fetch_array($resultado_sp)){
						$pdf->SetFont('Times','',7);
						$pdf->Cell(25,6,$row_sp[1],1,0,'C',0);
						$pdf->Cell(65,6,$row_sp[2].' '.$row_sp[3].' '.$row_sp[4],1,0,'L',0);
						$pdf->Cell(25,6,$row_sp[5],1,0,'C',0);
						$pdf->SetX(145);
						$pdf->Cell(25,6,$row_sp[6],1,0,'C',0);
						$pdf->Cell(25,6,$row_sp[7],1,0,'C',0);
						$pdf->Ln(6);
					}
					$y = $pdf->GetY();
					if($y <= 200){
						$pdf->SetY(210);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,260,90,260);
						$pdf->Line(120,260,180,260);
						$pdf->SetY(260);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
            $pdf->Ln(20);
            $pdf->Write(5,$df_respo);
					}elseif($y > 200){
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->SetY(50);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,100,90,100);
						$pdf->Line(120,100,180,100);
						$pdf->SetY(100);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
					}
				}
				if($doc_id == 2){
					$pdf->SetFont('Times','B',12);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(165,6,'PERMISO ADMINISTRATIVO',1,0,'C',1);
					$pdf->Ln();
					$pdf->SetFont('Times','B',10);
					$pdf->Cell(25,12,'RUT',1,0,'C',1);
					$pdf->MultiCell(65,12,'NOMBRE COMPLETO',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(120);
					$pdf->Cell(25,12,'DIAS',1,0,'C',1);
					$pdf->MultiCell(25,6,'FECHA INICIO',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(170);
					$pdf->MultiCell(25,12,'FECHA FIN',1,'C',1);
					//$pdf->Ln();
					$query_sp = "SELECT S.SP_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SP_CANT_DIA,DATE_FORMAT(S.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(S.SP_FEC_FIN,'%d-%m-%Y')  FROM DECRE_DETALLE DD INNER JOIN SOL_PERMI S ON DD.FOLIO_DOC = S.SP_ID INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE DD.DF_ID = $df_id ORDER BY U.EST_ID,S.SP_FEC ASC";
					$resultado_sp = mysqli_query($cnn, $query_sp);
					while($row_sp = mysqli_fetch_array($resultado_sp)){
						$pdf->SetFont('Times','',7);
						$pdf->Cell(25,6,$row_sp[1],1,0,'C',0);
						$pdf->Cell(65,6,$row_sp[2].' '.$row_sp[3].' '.$row_sp[4],1,0,'L',0);
						$pdf->Cell(25,6,$row_sp[5],1,0,'C',0);
						$pdf->SetX(145);
						$pdf->Cell(25,6,$row_sp[6],1,0,'C',0);
						$pdf->Cell(25,6,$row_sp[7],1,0,'C',0);
						$pdf->Ln(6);
					}
					$y = $pdf->GetY();
					if($y <= 200){
						$pdf->SetY(210);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,260,90,260);
						$pdf->Line(120,260,180,260);
						$pdf->SetY(260);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
            $pdf->Ln(20);
            $pdf->Write(5,$df_respo);
					}elseif($y > 200){
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->SetY(50);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,100,90,100);
						$pdf->Line(120,100,180,100);
						$pdf->SetY(100);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
					}
				}
				if($doc_id == 3){
					$pdf->SetFont('Times','B',12);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(165,6,'DESCANSO COMPLEMENTARIO',1,0,'C',1);
					$pdf->Ln();
					$pdf->SetFont('Times','B',10);
					$pdf->Cell(25,12,'RUT',1,0,'C',1);
					$pdf->MultiCell(65,12,'NOMBRE COMPLETO',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(120);
					$pdf->Cell(25,12,'HORAS',1,0,'C',1);
					$pdf->MultiCell(25,6,'FECHA INICIO',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(170);
					$pdf->MultiCell(25,12,'FECHA FIN',1,'C',1);
					//$pdf->Ln();
					$query_sp = "SELECT S.SP_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SP_CANT_DC,DATE_FORMAT(S.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(S.SP_FEC_FIN,'%d-%m-%Y')  FROM DECRE_DETALLE DD INNER JOIN SOL_PERMI S ON DD.FOLIO_DOC = S.SP_ID INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE DD.DF_ID = $df_id ORDER BY U.EST_ID,S.SP_FEC ASC";
					$resultado_sp = mysqli_query($cnn, $query_sp);
					while($row_sp = mysqli_fetch_array($resultado_sp)){
						$pdf->SetFont('Times','',7);
						$pdf->Cell(25,6,$row_sp[1],1,0,'C',0);
						$pdf->Cell(65,6,$row_sp[2].' '.$row_sp[3].' '.$row_sp[4],1,0,'L',0);
						$pdf->Cell(25,6,$row_sp[5],1,0,'C',0);
						$pdf->SetX(145);
						$pdf->Cell(25,6,$row_sp[6],1,0,'C',0);
						$pdf->Cell(25,6,$row_sp[7],1,0,'C',0);
						$pdf->Ln(6);
					}
					$y = $pdf->GetY();
					if($y <= 200){
						$pdf->SetY(210);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,260,90,260);
						$pdf->Line(120,260,180,260);
						$pdf->SetY(260);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
            $pdf->Ln(20);
            $pdf->Write(5,$df_respo);
					}elseif($y > 200){
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->SetY(50);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,100,90,100);
						$pdf->Line(120,100,180,100);
						$pdf->SetY(100);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
					}
				}
				if($doc_id == 4){
						$pdf->SetY($y + 20);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(165,5,$txt_final_mostrar,'J');
						$pdf->Line(35,260,95,260);
						$pdf->Line(120,240,180,240);
						$pdf->SetY(260);
						$pdf->SetX(30);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
						$pdf->SetY(240);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->SetY(260);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(30);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetY(240);
						$pdf->Ln();
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' '.$dir_sub,0,0,'C',false);
						$pdf->SetY(260);
						$pdf->Ln(35);
						$pdf->Write(5,$df_respo);
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->SetFont('Times','B',12);
						$pdf->Write(5, "VISTOS :");
						$pdf->Ln(10);
						$pdf->SetFont('Times','',12);
						$pdf->MultiCell(165,5,$txt_visto_mostrar,'J');
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->Write(5, "CONSIDERANDO :");
						$pdf->Ln(10);
						$pdf->SetFont('Times','',12);
						$pdf->MultiCell(165,5,$txt_consi_mostrar,'J');
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->Write(5, "DECRETO :");
						$pdf->Ln(10);
						$pdf->SetFont('Times','',12);
						$pdf->MultiCell(165,5,$txt_decreto_mostrar,'J');
						$y = $pdf->GetY();	
						$y = $y + 30;
						$pdf->SetY($y);
						$pdf->SetFont('Times','B',12);
						$pdf->MultiCell(165,5,$txt_final_mostrar,'J');
						$pdf->Ln(20);
						$pdf->SetX(50);
						$pdf->Write(5,'FDOS: '.$nom_dir.', '.$dir_gen.''.$dir_sub.'.');
						$pdf->Ln();
						$pdf->SetX(64);
						$pdf->Write(5,$nom_sec.', '.$sec_gen.' MUNICIPAL'.$sec_sub.'.');
						$pdf->Line(120,260,180,260);
						$pdf->SetY(260);
            $pdf->SetX(115);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->Ln(30);
						$pdf->Write(5,$df_respo);
						
				}
				if($doc_id == 5){
					$pdf->SetFont('Times','B',12);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(165,6,'DESCANSO COMPLEMENTARIO',1,0,'C',1);
					$pdf->Ln();
					$pdf->SetFont('Times','B',10);
					$pdf->Cell(25,12,'RUT',1,0,'C',1);
					$pdf->MultiCell(65,12,'NOMBRE COMPLETO',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(120);
					$pdf->Cell(25,12,'EST.',1,0,'C',1);
					$pdf->MultiCell(15,6,'HRS. DIUR.',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(160);
					$pdf->MultiCell(20,6,'SAB-DOM Y FEST',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(180);
					$pdf->Cell(15,12,'TOTAL',1,0,'C',1);
					$pdf->Ln();
					//$pdf->SetFont('GothamBooK');
					$query_dc = "SELECT OT.OE_ID,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,OT.OE_DC_DIU,OT.OE_DC_NOC,OT.OE_CANT_DC,OT.OE_FEC FROM DECRE_DETALLE DD INNER JOIN OT_EXTRA OT ON DD.FOLIO_DOC = OT.OE_ID INNER JOIN USUARIO U ON OT.USU_RUT = U.USU_RUT INNER JOIN ESTABLECIMIENTO E ON U.EST_ID = E.EST_ID WHERE DD.DF_ID = $df_id AND ( OT.OE_CANT_DC > 0) ORDER BY U.EST_ID,OT.OE_FEC ASC";
					$resultado_dc = mysqli_query($cnn, $query_dc);
					while($row_dc = mysqli_fetch_array($resultado_dc)){
						$pdf->SetFont('Times','',7);
						$pdf->Cell(25,6,$row_dc[1],1,0,'C',0);
						$pdf->Cell(65,6,$row_dc[2].' '.$row_dc[3].' '.$row_dc[4],1,0,'L',0);
						$establecimiento = $row_dc[5];
						if($establecimiento == "DEPARTAMENTO DE SALUD"){$establecimiento = "DPTO. SALUD";}
						if($establecimiento == "MULTIESTABLECIMIENTO"){$establecimiento = "DPTO. SALUD";}
						$pdf->Cell(25,6,$establecimiento,1,0,'C',0);
						//$pdf->MultiCell(25,6,$establecimiento,1,'C',0);
						//$y = $pdf->GetY();	
						//$y = $y-6;
						//$pdf->SetY($y);
						$pdf->SetX(145);
						$pdf->Cell(15,6,$row_dc[6],1,0,'C',0);
						$pdf->Cell(20,6,$row_dc[7],1,0,'C',0);
						$pdf->SetFont('Times','B',7);
						$pdf->Cell(15,6,$row_dc[8],1,0,'C',0);
						$pdf->Ln(6);
					}
					$y = $pdf->GetY();
					$y = $y + 20;
					$pdf->SetY($y);
					if($y >= 280){
						$pdf->AddPage('P',array(215.9,330.2));
						$y = $pdf->GetY();
					}
					$pdf->SetFont('Times','B',12);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(165,6,'HORAS CANCELADAS',1,0,'C',1);
					$pdf->Ln();
					$pdf->SetFont('Times','B',10);
					$pdf->Cell(25,12,'RUT',1,0,'C',1);
					$pdf->MultiCell(65,12,'NOMBRE COMPLETO',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(120);
					$pdf->Cell(25,12,'EST.',1,0,'C',1);
					$pdf->MultiCell(15,6,'HRS. DIUR.',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(160);
					$pdf->MultiCell(20,6,'SAB-DOM Y FEST',1,'C',1);
					$pdf->SetY($y + 6);
					$pdf->SetX(180);
					$pdf->Cell(15,12,'TOTAL',1,0,'C',1);
					$pdf->Ln();
					//$pdf->SetFont('GothamBooK');
					$query_cc = "SELECT OT.OE_ID,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,OT.OE_CC_DIU,OT.OE_CC_NOC,OT.OE_CANT_CANCE,OT.OE_FEC FROM DECRE_DETALLE DD INNER JOIN OT_EXTRA OT ON DD.FOLIO_DOC = OT.OE_ID INNER JOIN USUARIO U ON OT.USU_RUT = U.USU_RUT INNER JOIN ESTABLECIMIENTO E ON U.EST_ID = E.EST_ID WHERE DD.DF_ID = $df_id AND (OT.OE_CANT_CANCE > 0) AND (OT.OE_PROGRAMA = 0) ORDER BY U.EST_ID,OT.OE_FEC ASC";
					$resultado_cc = mysqli_query($cnn, $query_cc);
					while($row_cc = mysqli_fetch_array($resultado_cc)){
						$pdf->SetFont('Times','',7);
						$pdf->Cell(25,6,$row_cc[1],1,0,'C',0);
						$pdf->Cell(65,6,$row_cc[2].' '.$row_cc[3].' '.$row_cc[4],1,0,'L',0);
						$establecimiento = $row_cc[5];
						if($establecimiento == "DEPARTAMENTO DE SALUD"){$establecimiento = "DPTO. SALUD";}
						if($establecimiento == "MULTIESTABLECIMIENTO"){$establecimiento = "DPTO. SALUD";}
						$pdf->Cell(25,6,$establecimiento,1,0,'C',0);
						$pdf->SetX(145);
						$pdf->Cell(15,6,$row_cc[6],1,0,'C',0);
						$pdf->Cell(20,6,$row_cc[7],1,0,'C',0);
						$pdf->SetFont('Times','B',7);
						$pdf->Cell(15,6,$row_cc[8],1,0,'C',0);
						$pdf->Ln(6);
					}
					$y = $pdf->GetY();	
					$y = $y + 20;
					$pdf->SetY($y);
					$query_programa = "SELECT P.OP_ID,P.OP_NOM,E.EST_NOM FROM OTE_PROGRAMA P INNER JOIN ESTABLECIMIENTO E ON P.EST_ID = E.EST_ID WHERE (P.OP_ID != 0) AND (P.OP_ESTA = 'ACTIVO') ORDER BY E.EST_ID ASC";
					$respuesta_programa = mysqli_query($cnn,$query_programa);
					while ($row_pro = mysqli_fetch_array($respuesta_programa, MYSQLI_NUM)){
						$queryPRO = "SELECT OT.OE_ID,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,OT.OE_CC_DIU,OT.OE_CC_NOC,OT.OE_CANT_CANCE,OT.OE_FEC FROM DECRE_DETALLE DD INNER JOIN OT_EXTRA OT ON DD.FOLIO_DOC = OT.OE_ID INNER JOIN USUARIO U ON OT.USU_RUT = U.USU_RUT INNER JOIN ESTABLECIMIENTO E ON U.EST_ID = E.EST_ID WHERE DD.DF_ID = $df_id AND (OT.OE_CANT_CANCE > 0) AND (OT.OE_PROGRAMA = $row_pro[0]) ORDER BY U.EST_ID,OT.OE_FEC ASC";
						$respuesta_pro = mysqli_query($cnn,$queryPRO);
						if(mysqli_num_rows($respuesta_pro) != 0){
							$y = $pdf->GetY();	
							//$pdf->Write(5,$y);
							if($y >= 280){
								$pdf->AddPage('P',array(215.9,330.2));
								$y = $pdf->GetY();
							}
							$pdf->SetFont('Times','B',12);
							$pdf->SetFillColor(232,232,232);
							$pdf->Cell(165,6,'PROGRAMA : '.$row_pro[1].' - '.$row_pro[2],1,0,'C',1);
							$pdf->Ln();
							$pdf->SetFont('Times','B',10);
							$pdf->Cell(25,12,'RUT',1,0,'C',1);
							$pdf->MultiCell(65,12,'NOMBRE COMPLETO',1,'C',1);
							$pdf->SetY($y + 6);
							$pdf->SetX(120);
							$pdf->Cell(25,12,'EST.',1,0,'C',1);
							$pdf->MultiCell(15,6,'HRS. DIUR.',1,'C',1);
							$pdf->SetY($y + 6);
							$pdf->SetX(160);
							$pdf->MultiCell(20,6,'SAB-DOM Y FEST',1,'C',1);
							$pdf->SetY($y + 6);
							$pdf->SetX(180);
							$pdf->Cell(15,12,'TOTAL',1,0,'C',1);
							$pdf->Ln();
							while ($rowPG = mysqli_fetch_array($respuesta_pro, MYSQLI_NUM)){
								$pdf->SetFont('Times','',7);
								$pdf->Cell(25,6,$rowPG[1],1,0,'C',0);
								$pdf->Cell(65,6,$rowPG[2].' '.$rowPG[3].' '.$rowPG[4],1,0,'L',0);
								$establecimiento = $rowPG[5];
								if($establecimiento == "DEPARTAMENTO DE SALUD"){$establecimiento = "DPTO. SALUD";}
								if($establecimiento == "MULTIESTABLECIMIENTO"){$establecimiento = "DPTO. SALUD";}
								$pdf->Cell(25,6,$establecimiento,1,0,'C',0);
								$pdf->SetX(145);
								$pdf->Cell(15,6,$rowPG[6],1,0,'C',0);
								$pdf->Cell(20,6,$rowPG[7],1,0,'C',0);
								$pdf->SetFont('Times','B',7);
								$pdf->Cell(15,6,$rowPG[8],1,0,'C',0);
								$pdf->Ln(6);
							}
						}
					}
					$y = $pdf->GetY();
          //$pdf->Write(5,$y);
					if($y <= 200){
						$pdf->SetY(210);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						//$text_inicio = "     ".$text_inicio;
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,260,90,260);
						$pdf->Line(120,260,180,260);
						$pdf->SetY(260);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						//$pdf->Write(5,$nom_dir);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
            $pdf->Ln(20);
            //$y = $pdf->GetY();	
					  //$pdf->Write(5,$y);
            $pdf->Write(5,$df_respo);
					}elseif($y > 200){
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->SetY(50);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						//$text_inicio = "     ".$text_inicio;
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,100,90,100);
						$pdf->Line(120,100,180,100);
						$pdf->SetY(100);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
					}
				}
			if($doc_id == 6){
					$pdf->SetFont('Times','B',12);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(165,6,'ACUMULACION DE FERIADOS',1,0,'C',1);
					$pdf->Ln();
					$pdf->SetFont('Times','B',10);
					$pdf->Cell(30,6,'RUT',1,0,'C',1);
					$pdf->MultiCell(95,6,'NOMBRE COMPLETO',1,'C',1);
					$pdf->SetY($y + 6);
          $pdf->SetX(155);
					$pdf->Cell(25,6,'CATEGORIA',1,0,'C',1);
					$pdf->MultiCell(15,6,'DIAS.',1,'C',1);;
					//$pdf->Ln();
          $query_ac = "SELECT SA.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CAT,SA.SAF_CANT_DIA FROM DECRE_DETALLE DD INNER JOIN SOL_ACU_FER SA ON DD.FOLIO_DOC = SA.SAF_ID INNER JOIN USUARIO U ON SA.USU_RUT = U.USU_RUT WHERE DD.DF_ID = $df_id ORDER BY U.USU_CAT,SA.USU_RUT ASC";
					$resultado_ac = mysqli_query($cnn, $query_ac);
					while($row_ac = mysqli_fetch_array($resultado_ac)){
            $pdf->SetFillColor(254,254,254);
            $pdf->Cell(30,6,$row_ac[0],1,0,'C',1);
            $pdf->Cell(95,6,$row_ac[1].' '.$row_ac[2].' '.$row_ac[3],1,0,'L',1);
            $pdf->Cell(25,6,$row_ac[4],1,0,'C',1);
            $pdf->Cell(15,6,$row_ac[5],1,0,'C',1);
            $pdf->Ln(6);
          }
          $pdf->Ln(6);
          $y = $pdf->GetY();	
          //$pdf->Write(5,$y);
          if($y >= 180){
            $pdf->AddPage('P',array(215.9,330.2));
            $pdf->Ln(20);
          }
          $text_fin = utf8_decode($text_fin);
          $text_fin = str_replace("<br />", "\n", $text_fin);			          
          $txt_final_mostrar = str_replace("?", "*", $text_fin);
          nl2br($txt_final_mostrar);
          $pdf->SetFont('Times','',12);
          $pdf->MultiCell(165,5,$txt_final_mostrar,'J');
          $y = $pdf->GetY();
          $y = $y + 35;
          $pdf->Line(120,$y,180,$y);
          $pdf->SetY($y);
          $pdf->SetX(115);
          $pdf->SetFont('Times','',12);
          $pdf->Cell(70,5,$nom_dir,0,0,'C',false);
          $pdf->Ln();
          $pdf->SetFont('Times','B',12);
          $pdf->SetX(115);
          $pdf->Cell(70,5,$dir_gen.' '.$dir_sub,0,0,'C',false);
          $pdf->Ln();
          $y = $pdf->GetY();	
          $y = $y + 15;
          $pdf->Line(30,$y,90,$y);
          $pdf->SetY($y);
          $pdf->SetX(25);
          $pdf->SetFont('Times','',12);
          $pdf->Cell(70,5,$nom_sec,0,0,'C',false);
          $pdf->Ln();
          $pdf->SetFont('Times','B',12);
          $pdf->SetX(25);
          $pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
          $pdf->Ln(20);
          $pdf->Write(5,$df_respo);
          /*segunda copia*/
          $pdf->AddPage('P',array(215.9,330.2));
          $pdf->SetFont('Times','B',12);
          $pdf->Write(5, "VISTOS :");
          $pdf->Ln(10);
          $pdf->SetFont('Times','',12);
          $pdf->MultiCell(165,5,$txt_visto_mostrar,'J');
          $pdf->Ln();
          $pdf->SetFont('Times','B',12);
          $pdf->Write(5, "CONSIDERANDO :");
          $pdf->Ln(10);
          $pdf->SetFont('Times','',12);
          $pdf->MultiCell(165,5,$txt_consi_mostrar,'J');
          $pdf->Ln();
          $pdf->SetFont('Times','B',12);
          $pdf->Write(5, "DECRETO :");
          $pdf->Ln(10);
          $pdf->SetFont('Times','',12);
          $pdf->MultiCell(165,5,$txt_decreto_mostrar,'J');
          $y = $pdf->GetY();	
          $y = $y + 10;
          $pdf->SetY($y);
					$pdf->SetFont('Times','B',12);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(165,6,'ACUMULACION DE FERIADOS',1,0,'C',1);
					$pdf->Ln();
					$pdf->SetFont('Times','B',10);
					$pdf->Cell(30,6,'RUT',1,0,'C',1);
					$pdf->MultiCell(95,6,'NOMBRE COMPLETO',1,'C',1);
					$pdf->SetY($y + 6);
          $pdf->SetX(155);
					$pdf->Cell(25,6,'CATEGORIA',1,0,'C',1);
					$pdf->MultiCell(15,6,'DIAS.',1,'C',1);;
					//$pdf->Ln();
          $query_ac = "SELECT SA.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CAT,SA.SAF_CANT_DIA FROM DECRE_DETALLE DD INNER JOIN SOL_ACU_FER SA ON DD.FOLIO_DOC = SA.SAF_ID INNER JOIN USUARIO U ON SA.USU_RUT = U.USU_RUT WHERE DD.DF_ID = $df_id ORDER BY U.USU_CAT,SA.USU_RUT ASC";
					$resultado_ac = mysqli_query($cnn, $query_ac);
					while($row_ac = mysqli_fetch_array($resultado_ac)){
            $pdf->SetFillColor(254,254,254);
            $pdf->Cell(30,6,$row_ac[0],1,0,'C',1);
            $pdf->Cell(95,6,$row_ac[1].' '.$row_ac[2].' '.$row_ac[3],1,0,'L',1);
            $pdf->Cell(25,6,$row_ac[4],1,0,'C',1);
            $pdf->Cell(15,6,$row_ac[5],1,0,'C',1);
            $pdf->Ln(6);
          }
          $pdf->Ln(6);
          $y = $pdf->GetY();	
          //$pdf->Write(5,$y);
          if($y >= 180){
            $pdf->AddPage('P',array(215.9,330.2));
            $pdf->Ln(20);
          };
          $pdf->SetFont('Times','',12);
          $pdf->MultiCell(165,5,$txt_final_mostrar,'J');
          $pdf->Ln(6);
          $pdf->SetX(50);
          $pdf->Write(5, "FDOS:. ".$nom_dir.", ".$dir_gen." ".$dir_sub.".");
          $pdf->Ln();
          $pdf->SetX(64);
          $pdf->Write(5, $nom_sec.", ".$sec_gen." MUNICIPAL ".$sec_sub.".");
          $y = $pdf->GetY();
          $y = $y + 35;
          $pdf->Line(120,$y,180,$y);
          $pdf->SetY($y);
          $pdf->SetX(115);
          $pdf->SetFont('Times','',12);
          $pdf->Cell(70,5,$nom_sec,0,0,'C',false);
          $pdf->Ln();
          $pdf->SetFont('Times','B',12);
          $pdf->SetX(115);
          $pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
          $pdf->Ln(20);
          $pdf->Write(5,$df_respo);
				}
				if($doc_id == 8){
					$pdf->SetFont('Times','B',12);
					$pdf->SetFillColor(232,232,232);
					$pdf->Cell(165,6,'COMETIDOS FUNCIONARIOS',1,0,'C',1);
					$pdf->Ln();
					$query_co = "SELECT CP.CO_ID,CP.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CAT,E.EST_NOM,CP.CO_DIA,CP.CO_VIA,CP.CO_PAS,CP.CO_COM,CP.CO_PEA,CP.CO_PAR,CP.CO_MOT,CP.CO_DES FROM DECRE_DETALLE DD INNER JOIN COME_PERMI CP ON DD.FOLIO_DOC = CP.CO_ID INNER JOIN USUARIO U ON CP.USU_RUT = U.USU_RUT INNER JOIN ESTABLECIMIENTO E ON U.EST_ID = E.EST_ID WHERE DD.DF_ID = $df_id ORDER BY U.EST_ID,CP.CO_FEC ASC";
					$resultado_co = mysqli_query($cnn, $query_co);
					while($row_co = mysqli_fetch_array($resultado_co)){
						if($y > 270){
							$pdf->AddPage('P',array(215.9,330.2));
						}
						$pdf->SetFont('Times','B',10);
						$pdf->SetFillColor(232,232,232);
						$pdf->Cell(10,6,'RUT',1,0,'C',1);
						$pdf->SetFillColor(254,254,254);
						$pdf->SetFont('Times','',8);
						$pdf->Cell(20,6,$row_co[1],1,0,'L',1);
						$pdf->SetFillColor(232,232,232);
						$pdf->SetFont('Times','B',10);
						$pdf->Cell(20,6,'NOMBRE',1,0,'C',1);
						$pdf->SetFillColor(254,254,254);
						$pdf->SetFont('Times','',8);
						$pdf->Cell(70,6,$row_co[2].' '.$row_co[3].' '.$row_co[4],1,0,'L',0);
						$pdf->SetFont('Times','B',10);
						$pdf->SetFillColor(232,232,232);
						$pdf->Cell(10,6,'CAT',1,0,'C',1);
						$pdf->SetFillColor(254,254,254);
						$pdf->SetFont('Times','',8);
						$pdf->Cell(5,6,$row_co[5],1,0,'C',1);
						$establecimiento = $row_co[6];
						if($establecimiento == "DEPARTAMENTO DE SALUD"){$establecimiento = "DPTO. SALUD";}
						if($establecimiento == "MULTIESTABLECIMIENTO"){$establecimiento = "DPTO. SALUD";}
						$pdf->SetFont('Times','B',8);
						$pdf->Cell(30,6,$establecimiento,1,0,'C',1);
						$pdf->Ln(6);
						if(mb_strlen($row_co[13],'UTF-8') > 70){
							$pdf->SetFont('Times','B',10);
							$pdf->SetFillColor(232,232,232);
							$pdf->Cell(20,12,'MOTIVO',1,0,'L',1);
							//$pdf->Cell(10,6,mb_strlen($row_co[13],'UTF-8'),1,0,'L',1);
							$pdf->SetFillColor(254,254,254);
							$pdf->SetFont('Times','',8);
							$pdf->MultiCell(145,6,strtoupper($row_co[13]),1,'C',1);
						}else{
							$pdf->SetFont('Times','B',10);
							$pdf->SetFillColor(232,232,232);
							$pdf->Cell(20,6,'MOTIVO',1,0,'L',1);
							//$pdf->Cell(10,6,mb_strlen($row_co[13],'UTF-8'),1,0,'L',1);
							$pdf->SetFillColor(254,254,254);
							$pdf->SetFont('Times','',8);
							$pdf->Cell(145,6,strtoupper($row_co[13]),1,0,'L',1);
							$pdf->Ln(6);
						}
						if(mb_strlen($row_co[14],'UTF-8') > 70){
							$pdf->SetFont('Times','B',10);
							$pdf->SetFillColor(232,232,232);
							$pdf->Cell(20,12,'DESTINO',1,0,'L',1);
							//$pdf->Cell(10,6,mb_strlen($row_co[14],'UTF-8'),1,0,'L',1);
							$pdf->SetFillColor(254,254,254);
							$pdf->SetFont('Times','',8);
							$pdf->MultiCell(145,6,strtoupper($row_co[14]),1,'L',1);
						}else{
							$pdf->SetFont('Times','B',10);
							$pdf->SetFillColor(232,232,232);
							$pdf->Cell(20,6,'DESTINO',1,0,'L',1);
							//$pdf->Cell(10,6,mb_strlen($row_co[14],'UTF-8'),1,0,'L',1);
							$pdf->SetFillColor(254,254,254);
							$pdf->SetFont('Times','',8);
							$pdf->Cell(145,6,strtoupper($row_co[14]),1,0,'L',1);
							$pdf->Ln(6);
						}
						$pdf->SetFont('Times','B',10);
						$pdf->SetFillColor(232,232,232);
						$pdf->Cell(10,6,'DIAS',1,0,'L',1);
						$pdf->SetFillColor(254,254,254);
						$pdf->Cell(8,6,$row_co[7],1,0,'C',1);
						$pdf->SetFillColor(232,232,232);
						$pdf->Cell(20,6,'VIATICO',1,0,'L',1);
						$pdf->SetFillColor(254,254,254);
						if($row_co[8] == "on"){$viatico = "X";}else{$viatico = "";}
						$pdf->Cell(5,6,$viatico,1,0,'C',1);
						$pdf->SetFillColor(232,232,232);
						$pdf->Cell(17,6,'PASAJE',1,0,'L',1);
						$pdf->SetFillColor(254,254,254);
						if($row_co[9] == "on"){$pasaje = "X";}else{$pasaje = "";}
						$pdf->Cell(5,6,$pasaje,1,0,'C',1);
						$pdf->SetFillColor(232,232,232);
						$pdf->Cell(30,6,'COMBUSTIBLE',1,0,'L',1);
						$pdf->SetFillColor(254,254,254);
						if($row_co[10] == "on"){$combustible = "X";}else{$combustible = "";}
						$pdf->Cell(5,6,$combustible,1,0,'C',1);
						$pdf->SetFillColor(232,232,232);
						$pdf->Cell(20,6,'PEAJE',1,0,'L',1);
						$pdf->SetFillColor(254,254,254);
						if($row_co[11] == "on"){$peaje = "X";}else{$peaje = "";}
						$pdf->Cell(5,6,$peaje,1,0,'C',1);
						$pdf->SetFillColor(232,232,232);
						$pdf->Cell(35,6,'ESTACINAMIENTO',1,0,'L',1);
						$pdf->SetFillColor(254,254,254);
						if($row_co[12] == "on"){$estacionamiento = "X";}else{$estacionamiento = "";}
						$pdf->Cell(5,6,$estacionamiento,1,0,'C',1);
						$pdf->Ln(6);
						$DetalleCometido = "SELECT DATE_FORMAT(CD_DIA, '%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID =$row_co[0])";
           	$RespuestaDetalle = mysqli_query($cnn,$DetalleCometido);
						while($row_dco = mysqli_fetch_array($RespuestaDetalle)){
							$pdf->SetFont('Times','B',10);
							$pdf->SetFillColor(232,232,232);
							$pdf->Cell(10,6,'DIAS',1,0,'L',1);
							$pdf->SetFillColor(254,254,254);
							$pdf->SetFont('Times','',10);
							$pdf->Cell(20,6,$row_dco[0],1,0,'L',1);
							$pdf->SetFont('Times','B',10);
							$pdf->SetFillColor(232,232,232);
							$pdf->Cell(30,6,'HORA INICIO',1,0,'L',1);
							$pdf->SetFillColor(254,254,254);
							$pdf->SetFont('Times','',10);
							$pdf->Cell(15,6,$row_dco[1],1,0,'L',1);
							$pdf->SetFont('Times','B',10);
							$pdf->SetFillColor(232,232,232);
							$pdf->Cell(25,6,'HORA FIN',1,0,'L',1);
							$pdf->SetFillColor(254,254,254);
							$pdf->SetFont('Times','',10);
							$pdf->Cell(15,6,$row_dco[2],1,0,'L',1);
							$pdf->SetFont('Times','B',10);
							$pdf->SetFillColor(232,232,232);
							$pdf->Cell(30,6,'PORCENTAJE',1,0,'L',1);
							$pdf->SetFillColor(254,254,254);
							$pdf->SetFont('Times','',10);
							$pdf->Cell(20,6,$row_dco[3],1,0,'L',1);
							$pdf->Ln(6);
						}
						$pdf->Ln(6);
						$y = $pdf->GetY();	
					  //$pdf->Write(5,$y);
					}
					$pdf->Ln(30);
					$y = $pdf->GetY();
					if($y <= 200){
						//$pdf->SetY(210);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						//$text_inicio = "     ".$text_inicio;
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,260,90,260);
						$pdf->Line(120,260,180,260);
						$pdf->SetY(260);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
            $pdf->Ln(20);
            $pdf->Write(5,$df_respo);
					}elseif($y > 200){
						$pdf->AddPage('P',array(215.9,330.2));
						$pdf->SetY(50);
						$pdf->SetFont('Times','B',12);
						$text_final = utf8_decode($text_fin);
						//$text_inicio = "     ".$text_inicio;
						$txt_final = str_replace("<br />", "\n", $text_final);			          
						$txt_final_mostrar = str_replace("?", "*", $txt_final);
						nl2br($txt_final_mostrar);
						$pdf->MultiCell(160,5,$txt_final_mostrar,'J');
						$pdf->Line(30,100,90,100);
						$pdf->Line(120,100,180,100);
						$pdf->SetY(100);
						$pdf->SetX(25);
						$pdf->SetFont('Times','',12);
						$pdf->Cell(70,5,$nom_sec,0,0,'C',false);
            $pdf->SetX(115);
						$pdf->Cell(70,5,$nom_dir,0,0,'C',false);
						$pdf->Ln();
						$pdf->SetFont('Times','B',12);
						$pdf->SetX(25);
						$pdf->Cell(70,5,$sec_gen.' MUNICIPAL '.$sec_sub,0,0,'C',false);
						$pdf->SetX(115);
						$pdf->Cell(70,5,$dir_gen.' DPTO. SALUD '.$dir_sub,0,0,'C',false);
					}
				}
        $pdf->Output();
      }
    }
}
?>