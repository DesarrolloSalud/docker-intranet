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
    $saf_id = $_POST['id'];
    //$sp_id = 1;
    $selectSAF = "SELECT RSP_ID,USU_RUT,SAF_CANT_DIA,SAF_ANO_ACT FROM SOL_ACU_FER WHERE SAF_ID = $saf_id";
    $rsaf = mysqli_query($cnn, $selectSAF);
    if (mysqli_num_rows($rsaf) != 0){
        $row = mysqli_fetch_row($rsaf);
        $rsp_id       = $row[0];
        $usu_rut      = $row[1];
        $cant_dia     = $row[2];
        $ano_act      = $row[3];
    }
    date_default_timezone_set("America/Santiago");
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    $accionRealizada = utf8_decode("CANCELADO POR USUARIO");
    $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($oe_id,'$Srut','$fecha','$hora',6,'$accionRealizada')";
    //echo $insertAccion;
    //echo "</br>";
    mysqli_query($cnn, $insertAccion);
    $borra_Saf = "DELETE FROM SOL_ACU_FER WHERE SAF_ID = $saf_id AND USU_RUT = '$usu_rut'";
    mysqli_query($cnn, $borra_Saf);  
    //ACTUALIZAR BANCO HORA
    $select_bd = "SELECT BD_ID,BD_FL,BD_FL_USADO FROM BANCO_DIAS WHERE USU_RUT = '$usu_rut' AND BD_ANO = '$ano_act'";
    $rBD = mysqli_query($cnn, $select_bd);
    if (mysqli_num_rows($rBD) != 0){
        $rowBD = mysqli_fetch_row($rBD);
        $bd_id       = $rowBD[0];
        $bd_fl       = $rowBD[1];
        $bd_fl_u     = $rowBD[2];
    }
    $bd_fl = $bd_fl + $cant_dia;
    $bd_fl_u = $bd_fl_u - $cant_dia;
    $actualizar_bd = "UPDATE RES_SOL_PERMI SET RSP_ACC = 'EN ESPERA' WHERE RSP_ID = $rsp_id";
    mysqli_query($cnn,$actualizar_bd);
    $actualizar_rsp = "UPDATE BANCO_DIAS SET BD_FL = $bd_fl, BD_FL_USADO = $bd_fl_u WHERE BD_ID = $bd_id";
    mysqli_query($cnn,$actualizar_rsp);
    $resultado ['estado'] = "Ok";
    sleep(1);
    echo json_encode($resultado);
?>