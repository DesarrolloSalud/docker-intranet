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
      $ote_esta = $_POST['estado'];
      if($ote_esta == "ACTIVO"){
        $actualizar = "UPDATE OTE_DETALLE SET OTE_ESTA = '$ote_esta' WHERE (OE_ID = $oe_id) AND (DATE_FORMAT(OTE_DIA,'%d-%m-%Y') = '$ote_dia') AND (OTE_HORA_INI = '$ote_hora_ini') AND (OTE_HORA_FIN = '$ote_hora_fin')";
        mysqli_query($cnn, $actualizar);
      }elseif($ote_esta == "INACTIVO"){
        $actualizar = "UPDATE OTE_DETALLE SET OTE_ESTA = '$ote_esta' WHERE (OE_ID = $oe_id) AND (DATE_FORMAT(OTE_DIA,'%d-%m-%Y') = '$ote_dia') AND (OTE_HORA_INI = '$ote_hora_ini') AND (OTE_HORA_FIN = '$ote_hora_fin')";
        mysqli_query($cnn, $actualizar);
      }
      sleep(1);
    echo json_encode($resultado);
    }
?>