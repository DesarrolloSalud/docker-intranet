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
        $id_formulario = 23;
        $usu_rut = $_POST['rut'];
        $bh_fec = $_POST['fecha'];
        $bh_cant = $_POST['cant'];
        $bh_saldo = $bh_cant;
        $bh_tipo = "INICIAL";
        $query_rut = "SELECT USU_NOM, USU_APP, USU_APM FROM USUARIO WHERE USU_RUT = $rut";
        $respuestarut = mysqli_query($cnn, $query_rut);
        if($row = mysqli_fetch_array($respuestarut)){
            $MuestroNombre=utf8_encode($row[0]);
            $MuestroApellidoP = utf8_encode($row[1]);
            $MuestroApellidoM = utf8_encode($row[2]);
        }
        $accion = utf8_decode("CARGA INICIAL HORAS A : ".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM." CON LOS SIGUIENTES DATOS : FECHA = ".$fec." HORAS = ".$cant." - TIPO = INICIAL");
        //$Actualizar = "UPDATE BANCO_DIAS SET BD_HR = $horas,BD_ADM = $adm,BD_FL = $fl,BD_FLA = $fla WHERE BD_ID = $id";
        $Nuevo = "INSERT INTO BANCO_HORAS (USU_RUT, BH_FEC, BH_TIPO, BH_CANT, BH_SALDO) VALUES ('$usu_rut','$bh_fec','$bh_tipo',$bh_cant,$bh_saldo)";
        mysqli_query($cnn, $Nuevo);
        $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
        mysqli_query($cnn, $insertAcceso);
        $resultado ['estado'] = "Ok";
        sleep(1);
        echo json_encode($resultado);
    }
?>