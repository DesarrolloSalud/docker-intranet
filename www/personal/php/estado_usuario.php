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
    	$rut = $_POST['rut_usu'];
    	$estado = $_POST['esta_enviado'];
    	$actualizar = "UPDATE USUARIO SET USU_ESTA = '$estado' WHERE (USU_RUT = '$rut')";
    	//rescato nombre completo usuario editado
        $query ="SELECT USU_RUT, USU_NOM, USU_APP, USU_APM FROM USUARIO WHERE (USU_RUT ='".$rut."')";
        $rs = mysqli_query($cnn, $query);
        if (mysqli_num_rows($rs) != 0){
        	$row = mysqli_fetch_row($rs);
            if ($row[0] == $rut){
            	$Nombre = utf8_encode($row[1]);
                $ApellidoP = utf8_encode($row[2]);
                $ApellidoM = utf8_encode($row[3]);
            }
        }	
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 2;
        $ipcliente = getRealIP();
        $accionRealizada = utf8_decode($estado." A :  ".$Nombre." ".$ApellidoP." ".$ApellidoM);
        $insertAccion = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
        mysqli_query($cnn, $insertAccion);
        mysqli_query($cnn, $actualizar);
    }
    ?>