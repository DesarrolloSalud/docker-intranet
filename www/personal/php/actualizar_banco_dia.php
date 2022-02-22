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
        $id = $_POST['id'];
        $adm = $_POST['adm'];
        $fl = $_POST['fl'];
        $fla = $_POST['fla'];
        $sgr = $_POST['sgr'];
        $query_rut = "SELECT USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, BANCO_DIAS.BD_ANO, USUARIO.USU_RUT FROM BANCO_DIAS, USUARIO WHERE (USUARIO.USU_RUT = BANCO_DIAS.USU_RUT) AND (BANCO_DIAS.BD_ID = $id)";
        $respuestarut = mysqli_query($cnn, $query_rut);
        if($row = mysqli_fetch_array($respuestarut)){
            $MuestroNombre=utf8_encode($row[0]);
            $MuestroApellidoP = utf8_encode($row[1]);
            $MuestroApellidoM = utf8_encode($row[2]);
            $MuestroAño = $row[3];
            $MuestroRUT = $row[4];
        }
        $accion = utf8_decode("ACTUALIZA DIAS DE USUARIO : ".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM." DEL AÑO ".$MuestroAño." CON LOS SIGUIENTES DATOS : ADMINISTRATIVOS = ".$adm." FERIADOS LEGALES = ".$fl." - FER ACUMULADOS = ".$fla." - SIN GOSE REM = ".$sgr);
        $marce = strpos($accion, 'MARCELA ANGELINA'); // 11.277.235-9
        if($marce == true && $Srut == '11.277.235-9'){
            //guarda en tabla secundaria
            $insertedias = "INSERT INTO BANCO_DIAS_MARCE (USU_RUT, BD_ADM, BD_FL, BD_FLA, BD_SGR) VALUES ('$MuestroRUT', '$adm', $fl, $fla, $sgr)";
            mysqli_query($cnn, $insertedias);
            $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
            mysqli_query($cnn, $insertAcceso);
            $resultado ['estado'] = "Ok";
            //$resultado ['query'] = "UPDATE BANCO_DIAS SET BD_ADM = '$adm',BD_FL = $fl,BD_FLA = $fla,BD_SGR = $sgr WHERE BD_ID = $id";
            sleep(1);
            echo json_encode($resultado);
        }else{
            $Actualizar = "UPDATE BANCO_DIAS SET BD_ADM = '$adm',BD_FL = $fl,BD_FLA = $fla,BD_SGR = $sgr WHERE BD_ID = $id";
            mysqli_query($cnn, $Actualizar);
            $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
            mysqli_query($cnn, $insertAcceso);
            $resultado ['estado'] = "Ok";
            //$resultado ['query'] = "UPDATE BANCO_DIAS SET BD_ADM = '$adm',BD_FL = $fl,BD_FLA = $fla,BD_SGR = $sgr WHERE BD_ID = $id";
            sleep(1);
            echo json_encode($resultado);
        }
    }
?>