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
    $query ="SELECT SAF_ID,USU_RUT,SAF_CANT_DIA,SAF_ANO_SIG FROM SOL_ACU_FER WHERE (SAF_ID = '$saf_id')";
    $rs = mysqli_query($cnn, $query);
    if (mysqli_num_rows($rs) != 0){
        $rowA = mysqli_fetch_row($rs);
        if ($rowA[0] == $saf_id){
            $usu_rut = $rowA[1];
            $saf_cant_dia = $rowA[2];
            $saf_ano_sig = $rowA[3];
        }
    }
    $actualizarAcu = "UPDATE SOL_ACU_FER SET SAF_ESTA = 'AUTORIZADO DIR'  WHERE (SAF_ID = '$saf_id')";
    mysqli_query($cnn, $actualizarAcu);
    date_default_timezone_set("America/Santiago");
    //busco dias año sig
    $selectBD = "SELECT BD_ID,BD_FLA FROM BANCO_DIAS WHERE USU_RUT = '$usu_rut' AND BD_ANO = '$saf_ano_sig'";
    $resBD = mysqli_query($cnn,$selectBD);
    if (mysqli_num_rows($resBD) != 0){
        $rowBD = mysqli_fetch_row($resBD);
        $bd_id = $rowBD[0];
        $bd_fla = $rowBD[1];
    }
    $bd_fla = $bd_fla + $saf_cant_dia;
    $actualizar_bd = "UPDATE BANCO_DIAS SET BD_FLA = $bd_fla WHERE BD_ID = $bd_id";
    mysqli_query($cnn,$actualizar_bd);
    $accionRealizada = utf8_decode("AUTORIZADO COMO DIRECTOR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
    $Actualfecha = date("Y-m-d");
    $Actualhora = date("H:i:s");
    $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($saf_id,'$usu_rut','$Actualfecha','$Actualhora',$doc_id,'$accionRealizada')";
    mysqli_query($cnn, $insertAccion);
    $resultado ['estado'] = "Ok";
    sleep(1);
    echo json_encode($resultado);
?>