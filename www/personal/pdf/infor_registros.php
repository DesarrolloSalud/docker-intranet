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
        $Srut = utf8_encode($_SESSION['USU_RUT']);
		require('../../include/fpdf/fpdf.php');
		//Conecto a la base de datos
		include ("../../include/funciones/funciones.php");
		$cnn = ConectarPersonal();
		$seleccion = $_POST['seleccion_pdf'];
		$rut_usuario_recivida = $_POST['rut_usr_pdf'];
		$fecha_inicio = $_POST['fec_inicio_pdf'];
		$fecha_termino = $_POST['fec_termino_pdf'];
		if ($seleccion == "id_usuario"){
		    $query = "SELECT DATE_FORMAT(LOG_ACCION.LA_FEC,'%d-%m-%Y'),LOG_ACCION.LA_HORA, FORMULARIO.FOR_NOM, LOG_ACCION.LA_ACC, USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, LOG_ACCION.LA_IP_USU FROM FORMULARIO, LOG_ACCION, USUARIO WHERE (LOG_ACCION.FOR_ID = FORMULARIO.FOR_ID) AND (LOG_ACCION.USU_RUT = USUARIO.USU_RUT) AND (USUARIO.USU_RUT = '$rut_usuario_recivida')";
		}else{
		    $query = "SELECT DATE_FORMAT(LOG_ACCION.LA_FEC,'%d-%m-%Y'),LOG_ACCION.LA_HORA, FORMULARIO.FOR_NOM, LOG_ACCION.LA_ACC, USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, LOG_ACCION.LA_IP_USU FROM FORMULARIO, LOG_ACCION, USUARIO WHERE (LOG_ACCION.FOR_ID = FORMULARIO.FOR_ID) AND (LOG_ACCION.USU_RUT = USUARIO.USU_RUT) AND LOG_ACCION.LA_FEC BETWEEN '$fecha_inicio' AND '$fecha_termino'";
		}
		$resultado = mysqli_query($cnn, $query);
		class PDF extends FPDF{
		// Page header
			function Header(){
			    // Logo
			    $this->Image('../../include/img/header.jpg',1,1,280,30);
				$this->Ln(20);
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
		$pdf->SetFont('Arial','B',12);
		$pdf->Header();
		$pdf->Cell(280,6,'INFORME REGISTROS DE SISTEMA',1,0,'C');
		$pdf->Ln(10);
		if($seleccion == "id_usuario"){
			$query_usr = "SELECT USU_NOM,USU_APP,USU_APM FROM USUARIO WHERE USU_RUT = '$rut_usuario_recivida'";
			$resultado_usr = mysqli_query($cnn, $query_usr);
			while ($row = mysqli_fetch_array($resultado_usr)){
				$MuestroNombreCompleto = $row[0]." ".$row[1]." ".$row[2];
			}
			$pdf->Cell(280,6,'Registros de Usuario : '.$MuestroNombreCompleto,0,0,'L');
			$pdf->Ln(15);
		}else{
			$pdf->Cell(280,6,'Fecha Inicio : '.$fecha_inicio. ' - Fecha Termino : '.$fecha_termino,0,0,'L');
			$pdf->Ln(15);
		}
			
		//Creamos las celdas para los titulo de cada columna y le asignamos un fondo gris y el tipo de letra
		$pdf->SetFillColor(232,232,232);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(25,6,'FECHA',1,0,'C',1);
		$pdf->Cell(25,6,'HORA',1,0,'C',1);
		$pdf->Cell(60,6,'FORMULARIO',1,0,'C',1);
		$pdf->Cell(80,6,'ACCION',1,0,'C',1);
		$pdf->Cell(60,6,'NOMBRE COMPLETO',1,0,'C',1);
		$pdf->Cell(30,6,'IP USUARIO',1,0,'C',1);
		$pdf->Ln(10);
		//Comienzo a crear las fiulas de productos según la consulta mysql
		while($fila = mysqli_fetch_array($resultado)){
		    $Ifecha = $fila[0];
		    $IHora = $fila[1];
		    $IFormulario = substr($fila[2], 0, 25);
		    $IAccion = substr($fila[3], 0, 35);
		    $INombre = $fila[4]." ".$fila[5]." ".$fila[6];
		    $IIp = $fila[7];
			$pdf->Cell(25,15,$Ifecha,1,0,'C',0);
		 	$pdf->Cell(25,15,$IHora,1,0,'C',1);
		 	$pdf->Cell(60,15,$IFormulario,1,0,'C',0);
		 	$pdf->Cell(80,15,$IAccion,1,0,'C',1);
		 	$pdf->Cell(60,15,$INombre,1,0,'C',0);
		 	$pdf->Cell(30,15,$IIp,1,0,'C',1);
			$pdf->Ln(15);
		}
		//Mostramos el documento pdf
		$pdf->Cell(280,6,'Sistema de Personal - Dpto. de Salud 2017',0,0,'C');
		$pdf->Ln(10);
		$pdf->Output();
}
?>