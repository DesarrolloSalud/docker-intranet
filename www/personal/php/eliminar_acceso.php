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
    	$id = $_POST['id_acceso'];
    	//rescato id usuario e id formulario
        $query ="SELECT AC_ID,USU_RUT,FOR_ID FROM ACCESO WHERE (AC_ID ='".$id."')";
        $rs = mysqli_query($cnn, $query);
        if (mysqli_num_rows($rs) != 0){
        	$rowA = mysqli_fetch_row($rs);
            if ($rowA[0] == $id){
            	$Rut_usr_b = $rowA[1];
                $Id_form_b = $rowA[2];
            }
        }
    	//rescato nombre completo usuario editado
        $queryU ="SELECT USU_RUT,USU_NOM,USU_APP,USU_APM FROM USUARIO WHERE (USU_RUT ='".$Rut_usr_b."')";
        $respuestaU = mysqli_query($cnn, $queryU);
        if (mysqli_num_rows($respuestaU) != 0){
        	$rowU = mysqli_fetch_row($respuestaU);
            if ($rowU[0] == $Rut_usr_b){
            	$Nombre = utf8_encode($rowU[1]);
                $ApellidoP = utf8_encode($rowU[2]);
                $ApellidoM = utf8_encode($rowU[3]);
            }
        }
        //rescato nombre del formulario
        $formulario = "SELECT FOR_ID, FOR_NOM FROM FORMULARIO WHERE (FOR_ID ='".$Id_form_b."')";
        $resFormulario = mysqli_query($cnn, $formulario);
        if (mysqli_num_rows($resFormulario) != 0){
        	$rowF = mysqli_fetch_row($resFormulario);
            if ($rowF[0] == $Id_form_b){
            	$NombreFormulario = utf8_encode($rowF[1]);
            }
        }
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 7;
        $ipcliente = getRealIP();
        $accionRealizada = utf8_decode("QUITAR ACCESO A USUARIO :  ".$Nombre." ".$ApellidoP." ".$ApellidoM." A FORMULARIO : ".$NombreFormulario);
    	$insertAccion = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
    	    mysqli_query($cnn, $insertAccion);
    	$eliminar = "DELETE FROM ACCESO WHERE (AC_ID = $id)";
        mysqli_query($cnn, $eliminar);
        ?><script type="text/javascript"> window.location ="accesos.php";</script><?php
    }
?>