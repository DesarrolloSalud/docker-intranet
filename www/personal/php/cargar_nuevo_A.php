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
        date_default_timezone_set("America/Santiago");
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $ipcliente = getRealIP();
        $año = $_POST['año'];
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 22;
        $accion = utf8_decode("CREA AÑO ".$año." PARA TODOS LOS USUARIOS, HORAS = 0 - DIAS ADM = 6 - FERIADOS = 15 - FER ACUMULADOS = 0 - SGR = 90");
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $query_usuario = "SELECT USU_RUT FROM USUARIO";
        $respuesta_usuario = mysqli_query($cnn, $query_usuario);
        $BD_ADM     = 6;
        $BD_FL      = 15;
        $BD_FLA     = 0;
        $BD_ADM_U   = 0;
        $BD_FL_U    = 0;
        $BD_SGR     = 90;
        $BD_SGR_U   = 0;
        while ($row = mysqli_fetch_array($respuesta_usuario, MYSQLI_NUM)){
            $usu_rut = $row[0];
            $GuardarNuevo = "INSERT INTO BANCO_DIAS (USU_RUT,BD_ADM,BD_FL,BD_FLA,BD_ANO,BD_ADM_USADO,BD_FL_USADO,BD_SGR,BD_SGR_USADO) VALUES ('$usu_rut','$BD_ADM',$BD_FL,$BD_FLA,'$año','$BD_ADM_U',$BD_FL_U,$BD_SGR,$BD_SGR_U)";
            mysqli_query($cnn, $GuardarNuevo);
        }
        $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
        mysqli_query($cnn, $insertAcceso);
        $resultado ['estado'] = "Ok";
        sleep(1);
        echo json_encode($resultado);
    }
?>