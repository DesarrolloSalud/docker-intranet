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
    $docid= 7;
    date_default_timezone_set("America/Santiago");
    $FecActual = date("Y-m-d");
    $HorActual = date("H:i:s");
    $id = $_POST['id'];
    $actualizarauot = "UPDATE OT_EXTRA_ENC SET  OEE_ESTA = 'CANCELADO POR USUARIO' WHERE (OEE_ID= '$id')";
    mysqli_query($cnn,$actualizarauot);
    $HDAccion = ("CANCELADO POR USUARIO");
    $guardar_historial = "INSERT INTO HISTO_DOCU (HD_FOLIO, USU_RUT, HD_FEC, HD_HORA, DOC_ID, HD_ACC) VALUES ('$id','$Srut','$FecActual','$HorActual','$docid', '$HDAccion')";                                                 mysqli_query($cnn, $guardar_historial);
    $resultado ['estado'] = "Ok";
    sleep(1);
    echo json_encode($resultado);
?>