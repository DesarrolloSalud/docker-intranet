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
        $value = $_POST['value'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        if($value == "SI"){
          $update = "UPDATE COME_PERMI SET CO_VIA='on' WHERE CO_ID = $co_id";
        }else{
          $update = "UPDATE COME_PERMI SET CO_VIA='' WHERE CO_ID = $co_id";
        }
        $resultado = mysqli_query($cnn, $update);
        sleep(1);
        echo json_encode($respuesta);  
    }
?>