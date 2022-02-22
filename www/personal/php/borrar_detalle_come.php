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
    	//$NuevoDetalle = "INSERT INTO OTE_DETALLE (OE_ID,OTE_DIA,OTE_HORA_INI,OTE_HORA_FIN) VALUES ($oe_id,'$ote_dia','$ote_hora_ini','$ote_hora_fin')";
    	$BorrarDetalle = "DELETE FROM COME_DETALLE WHERE (CO_ID = $oe_id) AND (DATE_FORMAT(CD_DIA,'%d-%m-%Y') = '$ote_dia') AND (CD_HORA_INI = '$ote_hora_ini') AND (CD_HORA_FIN = '$ote_hora_fin')";
        mysqli_query($cnn, $BorrarDetalle);
    }
?>