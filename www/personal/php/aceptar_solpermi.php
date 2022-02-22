<?php
    session_start();
    $Srut = utf8_encode($_SESSION['USU_RUT']);
    $Snombre = utf8_encode($_SESSION['USU_NOM']);
    $SapellidoP = utf8_encode($_SESSION['USU_APP']);
    $SapellidoM = utf8_encode($_SESSION['USU_APM']);
    $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
    $Scargo = utf8_encode($_SESSION['USU_CAR']);
	include ("../../include/funciones/funciones.php");
	$cnn = ConectarPersonal();
	$sp_id = $_POST['id'];
    //$sp_id = 1;
	//rescato id usuario e id formulario
    $query ="SELECT SP_ID,DOC_ID,USU_RUT,SP_ANO FROM SOL_PERMI WHERE (SP_ID = '$sp_id')";
    $rs = mysqli_query($cnn, $query);
    if (mysqli_num_rows($rs) != 0){
    	$rowA = mysqli_fetch_row($rs);
        if ($rowA[0] == $sp_id){
        	$doc_id = $rowA[1];
            $usu_rut = $rowA[2];
            $año = $rowA[3];
        }
    }
    $actualizarSolPermi = "UPDATE SOL_PERMI SET SP_ESTA = 'AUTORIZADO DIR' WHERE (SP_ID = '$sp_id')";
    mysqli_query($cnn, $actualizarSolPermi); 
    date_default_timezone_set("America/Santiago");
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    $accionRealizada = utf8_decode("AUTORIZADO POR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
	$insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($sp_id,'$usu_rut','$fecha','$hora',$doc_id,'$accionRealizada')";
    mysqli_query($cnn, $insertAccion);
    //header("Location: index.php"); 
?>