<?php
    function getRealIP(){
        if (isset($_SERVER["HTTP_CLIENT_IP"])){
            return $_SERVER["HTTP_CLIENT_IP"];
        }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){
            return $_SERVER["HTTP_X_FORWARDED"];
        }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){
            return $_SERVER["HTTP_FORWARDED_FOR"];
        }elseif (isset($_SERVER["HTTP_FORWARDED"])){
            return $_SERVER["HTTP_FORWARDED"];
        }else{
            return $_SERVER["REMOTE_ADDR"];
        }
    }
    session_start();
    if(!isset($_SESSION['USU_RUT'])){
        session_destroy();
        header("location: ../../index.php");
    }else{
        $Srut = utf8_encode($_SESSION['USU_RUT']);
    	include ("../../include/funciones/funciones.php");
    	$cnn = ConectarPersonal();
    	$oe_id = $_POST['id'];
    	$ote_dia = $_POST['dia'];
        $ote_hora_ini = $_POST['hora_ini'];
        $ote_hora_fin = $_POST['hora_fin'];
        $cd_porce = $_POST['porce1'];
        //$ote_tipo = $_POST['tipo'];
    	$NuevoDetalle = "INSERT INTO COME_DETALLE (CO_ID,CD_DIA,CD_HORA_INI,CD_HORA_FIN,CD_POR) VALUES ($oe_id,'$ote_dia','$ote_hora_ini','$ote_hora_fin','$cd_porce')";
    	mysqli_query($cnn, $NuevoDetalle);
    }
?>