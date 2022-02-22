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
    $spr_id = $_POST['id'];
    //$sp_id = 1;
    $selectspr = "SELECT SPR_ID,USU_RUT,SPR_NDIA,SPR_FEC_INI FROM SOL_PSGR WHERE SPR_ID = $spr_id";
    $rSPR = mysqli_query($cnn, $selectspr);
    if (mysqli_num_rows($rSPR) != 0){
        $row = mysqli_fetch_row($rSPR);
        $spr_id       = $row[0];
        $usu_rut      = $row[1];
        $cant_dia     = $row[2];
        $fec_ini      = $row[3];
    }
    date_default_timezone_set("America/Santiago");
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    $accionRealizada = utf8_decode("CANCELADO POR USUARIO");
    $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($oe_id,'$Srut','$fecha','$hora',4,'$accionRealizada')";
    //echo $insertAccion;
    //echo "</br>";
    mysqli_query($cnn, $insertAccion);
    $borra_spr = "DELETE FROM SOL_PSGR WHERE SPR_ID = $spr_id AND USU_RUT = '$usu_rut'";
    mysqli_query($cnn, $borra_spr);  
    //ACTUALIZAR BANCO HORA
    $añoActual = date("Y", strtotime($fec_ini));
    $select_bd = "SELECT BD_ID,BD_SGR,BD_SGR_USADO FROM BANCO_DIAS WHERE USU_RUT = '$usu_rut' AND BD_ANO = '$añoActual'";
    $rBD = mysqli_query($cnn, $select_bd);
    if (mysqli_num_rows($rBD) != 0){
        $rowBD = mysqli_fetch_row($rBD);
        $bd_id       = $rowBD[0];
        $bd_sgr       = $rowBD[1];
        $bd_sgr_u     = $rowBD[2];
    }
    $bd_sgr = $bd_sgr + $cant_dia;
    $bd_sgr_u = $bd_sgr_u - $cant_dia;
    $actualizar_spr = "UPDATE BANCO_DIAS SET BD_SGR = $bd_sgr, BD_SGR_USADO = $bd_sgr_u WHERE BD_ID = $bd_id";
    mysqli_query($cnn,$actualizar_spr);
    $resultado ['estado'] = "Ok";
    sleep(1);
    echo json_encode($resultado);
?>