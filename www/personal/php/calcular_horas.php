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
        $fecha = $_POST['fecha'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        list($año_actual, $mes_actual, $dia_actual) = split('[/]', $fecha);
        $FecIni = ($año_actual - 2)."/".$mes_actual."/".$dia_actual;
        $query_banco_hora = "SELECT BH_SALDO FROM BANCO_HORAS WHERE (USU_RUT = '$Srut') AND (BH_SALDO > 0) AND (BH_FEC BETWEEN '$FecIni' AND '$fecha') AND ((BH_TIPO = 'INICIAL') OR (BH_TIPO = 'INGRESO')) ORDER BY BH_FEC ASC";
        $resultado_hora = mysqli_query($cnn, $query_banco_hora);
        if (mysqli_num_rows($resultado_hora) != 0){
            while ($row_hora = mysqli_fetch_array($resultado_hora)){
                $horas  = $row_hora[0] + $horas;
            }
            $resultado ['doc_id'] = 3;
            $resultado ['horas'] = $horas;
            $resultado ['consulta'] = $query_banco_hora;
            sleep(1);
            echo json_encode($resultado);  
        }else{
            $resultado ['doc_id'] = 3;
            $resultado ['horas'] = 0;
            $resultado ['consulta'] = $query_banco_hora;
            sleep(1);
            echo json_encode($resultado);   
        }
    }
?>