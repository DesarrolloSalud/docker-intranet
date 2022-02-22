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
    $oe_id = $_POST['id'];
    //$sp_id = 1;
    date_default_timezone_set("America/Santiago");
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    $accionRealizada = utf8_decode("CANCELADO POR USUARIO");
    $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($oe_id,'$Srut','$fecha','$hora',5,'$accionRealizada')";
    //echo $insertAccion;
    //echo "</br>";
    mysqli_query($cnn, $insertAccion);
    $actualizarOT = "UPDATE OT_EXTRA SET OE_ESTA = 'CANCELADO POR USUARIO' WHERE (OE_ID = $oe_id)";
    mysqli_query($cnn, $actualizarOT);  
    $actualizarOTE = "UPDATE OTE_DETALLE SET OTE_ESTA = 'INACTIVO' WHERE OE_ID = $oe_id";
    mysqli_query($cnn,$actualizarOTE);
    //echo $actualizarOT;
    $resultado ['estado'] = "OK";
    sleep(1);
    echo json_encode($resultado);   
?>