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
        header("location: ../../../index.php");
    }else{
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        include ("../../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $nombre = utf8_decode($_POST['usunom']); //trae nombre completo desde bienio_carrera.php
        $idcb1 = $_POST['id_cb'];
        $opcion1 = $_POST['op_cb'];
        $descripcion1 = utf8_decode($_POST['descripcion']);

        if($opcion1 == 2){

            $desactivar = "UPDATE CARRERA_BIENIO SET CB_ESTADO = '$opcion1' WHERE (CB_ID ='$idcb1')";            
            date_default_timezone_set("America/Santiago");
            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            $id_formulario = 27;
            $ipcliente = getRealIP(); 
            $accionRealizada = ("Desactiva ".$descripcion1." :  De: ".$nombre);

            mysqli_query($cnn, $desactivar);
            $resultado ['estado']= "OK";           
        }
        if($opcion1 == 1){

            $activa = "UPDATE CARRERA_BIENIO SET CB_ESTADO = '$opcion1' WHERE (CB_ID ='$idcb1')";            
            date_default_timezone_set("America/Santiago");
            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            $id_formulario = 27;
            $ipcliente = getRealIP();  
            $accionRealizada = ("Activa".$descripcion1." :  De: ".$nombre);

            mysqli_query($cnn, $activa);
            $resultado ['estado']= "OK";           
        }

        if($opcion1 == 3){

            $elimina1 = "DELETE FROM CARRERA_BIENIO WHERE (CB_ID ='$idcb1')";            
            date_default_timezone_set("America/Santiago");
            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            $id_formulario = 27;
            $ipcliente = getRealIP(); 
            $accionRealizada = ("Elimina ".$descripcion1." :  De: ".$nombre);

            mysqli_query($cnn, $elimina1);
            $resultado ['estado']= "OK";
        }
        

        $insertAccion = "INSERT INTO CARRERA_LOG_ACCION (CA_LA_ACC, FOR_ID, USU_RUT, CA_LA_IP_USU, CA_LA_FEC, CA_LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
        mysqli_query($cnn, $insertAccion);

            
            //mysqli_query($cnn, $insertAccion);
            //mysqli_query($cnn, $actualizar);

        sleep(1);
        echo json_encode($resultado);
        
        
    }
    ?>