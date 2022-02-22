<?php
    session_start();
    $Srut = utf8_encode($_SESSION['USU_RUT']);
    $Snombre = utf8_encode($_SESSION['USU_NOM']);
    $SapellidoP = utf8_encode($_SESSION['USU_APP']);
    $SapellidoM = utf8_encode($_SESSION['USU_APM']);
    $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
    $Scargo = utf8_encode($_SESSION['USU_CAR']);
    $Sid_est = $_SESSION['EST_ID'] ;
		include ("../../include/funciones/funciones.php");
		$cnn = ConectarPersonal();
		$oe_id = $_POST['id'];
    //$sp_id = 1;
		//rescato id usuario e id formulario
    $query ="SELECT OE_ID,DOC_ID,USU_RUT,OE_CANT_DC FROM OT_EXTRA WHERE (OE_ID = '$oe_id')";
    $rs = mysqli_query($cnn, $query);
    if (mysqli_num_rows($rs) != 0){
    	$rowA = mysqli_fetch_row($rs);
        if ($rowA[0] == $oe_id){
        	$doc_id = $rowA[1];
            $usu_rut = $rowA[2];
            $oe_cant_dc = $rowA[3];
        }
    }
    date_default_timezone_set("America/Santiago");
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
		$buscar = "SELECT BH_ID FROM BANCO_HORAS WHERE (BH_TIPO = 'INGRESO') AND (BH_ID_ANT = $oe_id)";
		$respuesta = mysqli_query($cnn,$buscar);
		if (mysqli_num_rows($respuesta) != 0){
			
		}else{
			//$ingreso = "INSERT INTO BANCO_HORAS (USU_RUT, BH_FEC, BH_TIPO, BH_CANT, BH_SALDO, BH_ID_ANT) VALUES ('$usu_rut','$fecha','INGRESO',$oe_cant_dc,$oe_cant_dc,$oe_id)";
			//mysqli_query($cnn, $ingreso);
			$accionRealizada = utf8_decode("VISTO BUENO POR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
			$insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($oe_id,'$usu_rut','$fecha','$hora',$doc_id,'$accionRealizada')";
			mysqli_query($cnn, $insertAccion);
			$actualizarSolPermi = "UPDATE OT_EXTRA SET OE_ESTA = 'V.B. DIR SALUD' WHERE (OE_ID = $oe_id)";
			mysqli_query($cnn, $actualizarSolPermi); 
		}
?> 