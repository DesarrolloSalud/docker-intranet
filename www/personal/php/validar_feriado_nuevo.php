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
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $año_actual = date("Y");
        $año_siguiente = $año_actual + 1;
        $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FLA FROM BANCO_DIAS WHERE (USU_RUT = '$Srut') AND (BD_ANO = '$año_siguiente')";
        $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
        if (mysqli_num_rows($resultado_banco_fl) != 0){
            while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                $num_fl  = $row_fl[1];
                $num_fla = $row_fl[2];
            }
            $total_feriados = $num_fl + $num_fla;
            $resultado ['doc_id'] = 1;
            $resultado ['pendientes'] = $total_feriados;
            $resultado ['fl'] = $num_fl;
            $resultado ['fla'] = $num_fla;
            $resultado ['motivo'] = "SI";
            sleep(1);
            echo json_encode($resultado);                    
        }else{
            $resultado ['motivo'] = "NO";
            sleep(1);
            echo json_encode($resultado); 
        }
    }
?>