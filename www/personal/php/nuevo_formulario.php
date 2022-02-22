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
		$id = $_POST['id'];
		$nombre = $_POST['nombre'];
		$estado = $_POST['estado'];
        $id_formulario = 3;
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
	    $accionRealizada = utf8_decode("CREO NUEVO FORMULARIO : ".$nombre);
		$insertAccion = "INSERT INTO LOG_ACCION (LA_ACC,FOR_ID,USU_RUT,LA_IP_USU,LA_FEC,LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
		mysqli_query($cnn, $insertAccion);
	    $guardar = "INSERT INTO FORMULARIO (FOR_ID,FOR_NOM,FOR_ESTA) VALUES ('$id','$nombre', 'estado')";
	    mysqli_query($cnn, $guardar);
	}
?>