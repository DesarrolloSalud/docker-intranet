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
        $spr_id = $_POST['id'];
        //$spr_id = $_GET['id'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $query = "SELECT S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,U.USU_PROF,U.USU_CAT,DATE_FORMAT(S.SPR_FEC_INI, '%d-%m-%Y'),DATE_FORMAT(S.SPR_FEC_FIN, '%d-%m-%Y'),S.SPR_FEC FROM SOL_PSGR S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT LEFT JOIN ESTABLECIMIENTO E ON E.EST_ID = U.EST_ID WHERE (SPR_ID = $spr_id)";
        $resultado = mysqli_query($cnn, $query);
        //echo $query;
          while ($row = mysqli_fetch_array($resultado)){
            //echo "hola";
            $usu_rut  = $row[0];
            $usu_nom  = utf8_encode($row[1]);
            $usu_app  = utf8_encode($row[2]);
            $usu_apm  = utf8_encode($row[3]);
            $est_nom  = utf8_encode($row[4]);
            $usu_prof = utf8_encode($row[5]);
            $usu_cat  = $row[6];
            $fec_ini  = $row[7];
            $fec_fin  = $row[8];
            $spr_fec  = $row[9];
          }
          $respuesta ['id']       = $spr_id;
          $respuesta ['usu_rut']  = $usu_rut;
          $respuesta ['usu_nom']  = $usu_nom;
          $respuesta ['usu_app']  = $usu_app;
          $respuesta ['usu_apm']  = $usu_apm;
          $respuesta ['est_nom']  = $est_nom;
          $respuesta ['usu_prof']  = $usu_prof;
          $respuesta ['usu_cat']  = $usu_cat;
          $respuesta ['fec_ini']  = $fec_ini;
          $respuesta ['fec_fin']  = $fec_fin;
          $respuesta ['spr_fec']  = $spr_fec;
          sleep(1);
          echo json_encode($respuesta);  
    }
?>