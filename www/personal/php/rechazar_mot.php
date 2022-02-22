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
    $id = $_POST['id'];
    $query ="SELECT OEE_ID,DOC_ID,USU_RUT FROM OT_EXTRA_ENC WHERE (OEE_ID = '$id')";
    $rs = mysqli_query($cnn, $query);
    if (mysqli_num_rows($rs) != 0){
    	$rowA = mysqli_fetch_row($rs);
        if ($rowA[0] == $id){
        	$doc_id = $rowA[1];
          $usu_rut = $rowA[2];
        }
    }
    date_default_timezone_set("America/Santiago");
    $FecActual = date("Y-m-d");
    $HorActual = date("H:i:s");
    
    $actualizarauot = "UPDATE OT_EXTRA_ENC SET  OEE_ESTA = 'RECHAZADO DIR' WHERE (OEE_ID= '$id')";
    mysqli_query($cnn,$actualizarauot);
    $accionRealizada = utf8_decode("RECHAZADO COMO DIRECTOR POR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
    $guardar_historial = "INSERT INTO HISTO_DOCU (HD_FOLIO, USU_RUT, HD_FEC, HD_HORA, DOC_ID, HD_ACC) VALUES ('$id','$usu_rut','$FecActual','$HorActual','$doc_id', '$accionRealizada')";                                                 mysqli_query($cnn, $guardar_historial);
    $resultado ['estado'] = "Ok";
    sleep(1);
    echo json_encode($resultado);
?>