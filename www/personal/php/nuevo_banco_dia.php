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
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $ipcliente = getRealIP();
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 22;
        $rut = $_POST['rut'];
        $adm = $_POST['adm'];
        $fl = $_POST['fl'];
        $fla = $_POST['fla'];
        $sgr = $_POST['sgr'];
        $año = $_POST['año'];
        $BD_ADM_U   = 0;
        $BD_FL_U    = 0;
        $BD_SGR_U   = 0;
        $query_rut = "SELECT USU_NOM, USU_APP, USU_APM FROM USUARIO WHERE USU_RUT = $rut";
        $respuestarut = mysqli_query($cnn, $query_rut);
        if($row = mysqli_fetch_array($respuestarut)){
            $MuestroNombre=utf8_encode($row[0]);
            $MuestroApellidoP = utf8_encode($row[1]);
            $MuestroApellidoM = utf8_encode($row[2]);
        }
        $accion = utf8_decode("CARGA NUEVO USUARIO : ".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM." DEL AÑO ".$año." CON LOS SIGUIENTES DATOS : ADMINISTRATIVOS = ".$adm." FERIADOS LEGALES = ".$fl." - FER ACUMULADOS = ".$fla." - SIN GOSE REM = ".$sgr);
        //$Actualizar = "UPDATE BANCO_DIAS SET BD_HR = $horas,BD_ADM = $adm,BD_FL = $fl,BD_FLA = $fla WHERE BD_ID = $id";
        $Nuevo = "INSERT INTO BANCO_DIAS (USU_RUT, BD_ADM, BD_FL, BD_FLA, BD_ANO, BD_ADM_USADO, BD_FL_USADO, BD_SGR, BD_SGR_USADO) VALUES ('$rut','$adm',$fl,$fla,'$año','$BD_ADM_U',$BD_FL_U,$sgr,$BD_SGR_U)";
        mysqli_query($cnn, $Nuevo);
        $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
        mysqli_query($cnn, $insertAcceso);
        $resultado ['estado'] = "Ok";
        sleep(1);
        echo json_encode($resultado);
    }
?>