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
        $rsp_id = $_POST['id'];
        $rsp_acc = $_POST['acc'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $actualiza_rsp = "UPDATE RES_SOL_PERMI SET RSP_ACC = '$rsp_acc' WHERE RSP_ID = $rsp_id";
        mysqli_query($cnn, $actualiza_rsp); 
        $resultado ['estado'] = "OK";
        sleep(1);
        echo json_encode($resultado);
    }
?>