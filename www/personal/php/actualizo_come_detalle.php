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
        $co_id = $_POST['id'];
        $porcentaje = $_POST['porcentaje'];
        $dia = $_POST['dia'];
        $hi = $_POST['hi'];
        $hf = $_POST['hf'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $update = "UPDATE COME_DETALLE SET CD_POR = '$porcentaje' WHERE CO_ID = $co_id AND CD_DIA = '$dia' AND CD_HORA_INI = '$hi' AND 	CD_HORA_FIN = '$hf'";
        $resultado = mysqli_query($cnn, $update);
        sleep(1);
        echo json_encode($respuesta);  
    }
?>