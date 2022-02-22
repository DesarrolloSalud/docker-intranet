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
		$id = $_POST['id_form'];
		$estado = $_POST['esta_enviado'];
	    //rescato nombre del formulario
	    $formulario = "SELECT FOR_ID,FOR_NOM FROM FORMULARIO WHERE (FOR_ID = $id)";
	    $resFormulario = mysqli_query($cnn, $formulario);
	    if (mysqli_num_rows($resFormulario) != 0){
	    	$rowF = mysqli_fetch_row($resFormulario);
	        if ($rowF[0] == $id){
	        	$NombreFormulario = utf8_encode($rowF[1]);
	        }
	    }
	    date_default_timezone_set("America/Santiago");
	    $fecha = date("Y-m-d");
	    $hora = date("H:i:s");
	    $id_formulario = 3;
	    $ipcliente = getRealIP();
	    $accionRealizada = utf8_decode($estado." Formulario : ".$NombreFormulario);
		$insertAccion = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
		mysqli_query($cnn, $insertAccion);
		$actualizar = "UPDATE FORMULARIO SET FOR_ESTA = '$estado' WHERE (FOR_ID = $id)";
	    mysqli_query($cnn, $actualizar);
		$resultado ['estado'] = "OK";
    sleep(1);
    echo json_encode($resultado);  
	}
?>