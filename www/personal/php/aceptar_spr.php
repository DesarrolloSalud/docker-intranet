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
    $query ="SELECT SPR_ID,USU_RUT,SPR_NDIA,SPR_FEC_INI FROM SOL_PSGR WHERE (SPR_ID = '$spr_id')";
    $rs = mysqli_query($cnn, $query);
    if (mysqli_num_rows($rs) != 0){
        $rowA = mysqli_fetch_row($rs);
        if ($rowA[0] == $spr_id){
            $usu_rut = $rowA[1];
            $cant_dia = $rowA[2];
            $fec_ini = $rowA[3];
        }
    }
    $actualizarAcu = "UPDATE SOL_PSGR SET SPR_ESTA = 'AUTORIZADO DIR SALUD'  WHERE (SPR_ID = '$spr_id')";
    mysqli_query($cnn, $actualizarAcu);
    date_default_timezone_set("America/Santiago");
    $accionRealizada = utf8_decode("AUTORIZADO COMO DIRECTOR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
    $Actualfecha = date("Y-m-d");
    $Actualhora = date("H:i:s");
    $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($spr_id,'$usu_rut','$Actualfecha','$Actualhora',4,'$accionRealizada')";
    mysqli_query($cnn, $insertAccion);
    $resultado ['estado'] = "Ok";
    sleep(1);
    echo json_encode($resultado);
?>