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
    date_default_timezone_set("America/Santiago");
    $fecha = date("Y-m-d");
		include ("../../include/funciones/funciones.php");
		$cnn = ConectarPersonal();
		$rut = $_GET['rut'];
		$FecIni = $_GET['inicio'];
    $usuario = "SELECT USU_RUT,USU_NOM,USU_APM,USU_APP FROM USUARIO WHERE USU_RUT = '$rut'";
    $rs = mysqli_query($cnn, $usuario);
    $row = mysqli_fetch_array($rs);
		$licencias = "SELECT LM_FEC_INI,LM_FEC_FIN,LM_DIAS,LM_NUM,LM_TE,LM_TR,LM_ESTA,LM_TIPO,DATE_FORMAT(LM_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(LM_FEC_FIN,'%d-%m-%Y') FROM LICENCIAS_MEDICAS WHERE (USU_RUT = '$rut') AND (LM_FEC_FIN >= '$FecIni') AND ((LM_TIPO NOT LIKE '%Enfermedad Grave Hijo Menor%') AND (LM_TIPO != 'Prenatal - Postnatal') AND (LM_TIPO NOT LIKE '%Del Embarazo%')) ORDER BY LM_FEC_INI ASC";
		class PDF extends FPDF{
		// Page header
			function Header(){
			    // Logo
			    $this->Image('../../include/img/header.jpg',1,1,280,30);
				$this->Ln(10);
			}
			function Footer(){
			    $this->Image('../../include/img/footer.jpg',3,180,290,30);
			}
		}
		//Instaciamos la clase para genrear el documento pdf
		$pdf = new PDF();
		//Agregamos la primera pagina al documento pdf
		$pdf->AddPage('L');
		//Seteamos el tiupo de letra y creamos el titulo de la pagina. No es un encabezado no se repetira
		$pdf->SetFont('Times','B',12);
		$pdf->Header();
		$pdf->Cell(280,6,'LICENCIAS MEDICAS',1,0,'C');
		$pdf->Ln(10);
		$pdf->Cell(280,6,'Registros de Usuario : '.utf8_encode($row[1]).' '.utf8_encode($row[2]).' '.utf8_encode($row[3]),0,0,'L');
	  $pdf->Ln(15);
			
		//Creamos las celdas para los titulo de cada columna y le asignamos un fondo gris y el tipo de letra
		$pdf->SetFillColor(232,232,232);
		$pdf->SetFont('Times','B',10);
		$pdf->Cell(40,6,'LICENCIA',1,0,'C',1);
		$pdf->Cell(20,6,'INICIO',1,0,'C',1);
		$pdf->Cell(20,6,'TERMINO',1,0,'C',1);
		$pdf->Cell(15,6,'DIAS',1,0,'C',1);
		$pdf->Cell(35,6,'ESTADO',1,0,'C',1);
    $pdf->Cell(90,6,'TIPO',1,0,'C',1);
		$pdf->Cell(30,6,'ESTIMADO',1,0,'C',1);
    $pdf->Cell(30,6,'RECUPERADO',1,0,'C',1);
		$pdf->Ln();
		//Comienzo a crear las fiulas de productos según la consulta mysql
    $respuesta = mysqli_query($cnn, $licencias);
    $TotalDias = 0;
    $pdf->SetFillColor(254,254,254);
    $pdf->SetFont('Times','',10);
    //LM_FEC_INI,LM_FEC_FIN,LM_DIAS,LM_NUM,LM_TE,LM_TR,LM_ESTA,LM_TIPO,DATE_FORMAT(LM_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(LM_FEC_FIN,'%d-%m-%Y')
    if (mysqli_num_rows($respuesta) == 0){
      $pdf->Cell(280,6,'NO REGISTRA LICENCIAS MEDICAS',1,0,'C');
      $TotalDias = 0;
    }elseif(mysqli_num_rows($respuesta) == 1){
      $rowlm = mysqli_fetch_row($respuesta);
      $TotalDias = $rowlm[2];
      $pdf->Cell(40,6,$rowlm[3],1,0,'C',0);
      $pdf->Cell(20,6,$rowlm[8],1,0,'C',0);
      $pdf->Cell(20,6,$rowlm[9],1,0,'C',0);
      $pdf->Cell(15,6,$rowlm[2],1,0,'C',0);
      $pdf->Cell(35,6,$rowlm[6],1,0,'C',0);
      $pdf->Cell(90,6,$rowlm[7],1,0,'C',0);
      $pdf->Cell(30,6,'$ '.$rowlm[4],1,0,'C',0);
      $pdf->Cell(30,6,'$ '.$rowlm[5],1,0,'C',0);
      $pdf->Ln();
    }elseif(mysqli_num_rows($respuesta) > 1){
      while ($rowlm = mysqli_fetch_array($respuesta)){
        $pdf->Cell(40,6,$rowlm[3],1,0,'C',0);
        $pdf->Cell(20,6,$rowlm[8],1,0,'C',0);
        $pdf->Cell(20,6,$rowlm[9],1,0,'C',0);
        $pdf->Cell(15,6,$rowlm[2],1,0,'C',0);
        $pdf->Cell(35,6,$rowlm[6],1,0,'C',0);
        $pdf->Cell(90,6,$rowlm[7],1,0,'C',0);
        $pdf->Cell(30,6,'$ '.$rowlm[4],1,0,'C',0);
        $pdf->Cell(30,6,'$ '.$rowlm[5],1,0,'C',0);
        $pdf->Ln();
        $fec_ini = $rowlm[0];
        $fec_fin = $rowlm[1];
        $dias = $rowlm[2];
        if($fec_ini < $FecIni && $fec_fin > $FecIni){
          $date1 = new DateTime($FecIni);
          $date2 = new DateTime($fec_fin);
          $diff = $date1->diff($date2);
          $diferencia = $diff->days;
          $TotalDias = $TotalDias + $diferencia;
        }elseif($fec_ini >= $FecIni && $fec_fin <= $fecha){
          $TotalDias = $TotalDias + $dias;                                   
        }elseif($fec_fin > $fecha){
          $date1 = new DateTime($fec_ini);
          $date2 = new DateTime($fecha);
          $diff = $date1->diff($date2);
          $diferencia = $diff->days;
          $TotalDias = $TotalDias + $diferencia;
        }
      }
    }
    $pdf->Cell(220,6,' ',0,0,'C',0);
    $pdf->SetFillColor(232,232,232);
    $pdf->SetFont('Times','B',10);
    $pdf->Cell(30,6,'TOTAL DIAS',1,0,'R',0);
    $pdf->Cell(30,6,$TotalDias,1,0,'C',0);
		$pdf->Output();
}
?>