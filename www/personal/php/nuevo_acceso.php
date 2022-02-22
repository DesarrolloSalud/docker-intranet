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
    	$rut_usr = $_POST['rut_usr'];
    	$id_form = $_POST['id_form'];
    	//rescato nombre completo usuario editado
        $query ="SELECT USU_RUT,USU_NOM,USU_APP,USU_APM FROM USUARIO WHERE (USU_RUT ='".$rut_usr."')";
        $respuesta = mysqli_query($cnn, $query);
        if (mysqli_num_rows($respuesta) != 0){
        	$rowU = mysqli_fetch_row($respuesta);
            if ($rowU[0] == $rut_usr){
            	$Nombre = utf8_encode($rowU[1]);
                $ApellidoP = utf8_encode($rowU[2]);
                $ApellidoM = utf8_encode($rowU[3]);
            }
        }
        //rescato nombre del formulario
        $formulario = "SELECT FOR_ID,FOR_NOM FROM FORMULARIO WHERE (FOR_ID ='".$id_form."')";
        $resFormulario = mysqli_query($cnn, $formulario);
        if (mysqli_num_rows($resFormulario) != 0){
        	$rowF = mysqli_fetch_row($resFormulario);
            if ($rowF[0] == $id_form){
            	$NombreFormulario = utf8_encode($rowF[1]);
            }
        }
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 7;
        $ipcliente = getRealIP();
        $accionRealizada = utf8_decode("DAR ACCESO A USUARIO :  ".$Nombre." ".$ApellidoP." ".$ApellidoM." A FORMULARIO : ".$NombreFormulario);
    	$buscar = "SELECT * FROM ACCESO WHERE (USU_RUT = '".$rut_usr."') AND (FOR_ID = ".$id_form.")";
    	$rs = mysqli_query($cnn, $buscar);
        if (mysqli_num_rows($rs) == 0){
        	//decir que no existen registros con ese rut
    		$agregar = "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$rut_usr', '$id_form')";
    	    mysqli_query($cnn, $agregar);
    	    $insertAccion = "INSERT INTO LOG_ACCION (LA_ACC,FOR_ID,USU_RUT,LA_IP_USU,LA_FEC,LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
    	    mysqli_query($cnn, $insertAccion);
        }else{
  	     //decir que si existen registros
        }
    }
?>

