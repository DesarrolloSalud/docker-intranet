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
        $df_id = $_POST['id'];
        //$spr_id = $_GET['id'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $query = "SELECT DOC_ID,FOLIO_DOC FROM DECRE_DETALLE WHERE DF_ID = $df_id LIMIT 1";
        $resultado = mysqli_query($cnn, $query);
        //echo $query;
        $row = mysqli_fetch_array($resultado);
        $doc_id = $row[0];
        if($doc_id == 4){
          $folio_doc = $row[1];
          $respuesta ['id'] = $folio_doc;
        }
        $respuesta ['doc_id'] = $doc_id;
        $respuesta ['df_id']  = $df_id;
        sleep(1);
        echo json_encode($respuesta);  
    }
?>