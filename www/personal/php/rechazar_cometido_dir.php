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
    $sco_id = $_POST['id'];
      //$sp_id = 1;
    //rescato id usuario e id formulario
    $query ="SELECT CO_ID,DOC_ID,USU_RUT FROM COME_PERMI WHERE (CO_ID = '$sco_id')";
    $rs = mysqli_query($cnn, $query);
    if (mysqli_num_rows($rs) != 0){
    	$rowA = mysqli_fetch_row($rs);
        if ($rowA[0] == $sco_id){
        	  $doc_id = $rowA[1];
            $usu_rut = $rowA[2];
        }
    }
    $actualizarSolPermi = "UPDATE COME_PERMI SET CO_ESTA = 'RECHAZADO DIR' WHERE (CO_ID = '$sco_id')";
    mysqli_query($cnn, $actualizarSolPermi); 
    date_default_timezone_set("America/Santiago");
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    $accionRealizada = utf8_decode("RECHAZADO POR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
	  $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($sp_id,'$usu_rut','$fecha','$hora',$doc_id,'$accionRealizada')";
    mysqli_query($cnn, $insertAccion);
    sleep(1);
    echo json_encode($resultado);
    //header("Location: index.php"); 
?>