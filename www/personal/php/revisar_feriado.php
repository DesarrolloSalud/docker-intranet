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
        $Sest_id = $_SESSION['EST_ID']; 
        $Sprof = utf8_encode($_SESSION['USU_PROF']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $fecha = $_POST['fecha'];
          $ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$fecha."')";
          $RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
          if (mysqli_num_rows($RespuestaFeriado) == 0){
              //puede pedir ese dia revisar si es sabado o domingo
              if (date('w',strtotime($fecha)) == 0){
                  //no puede dia domingo
                  $resultado ['dia'] = "si";
                  sleep(1);
                  echo json_encode($resultado);
              }else{
                  //ver si es sabado
                  if (date('w',strtotime($fecha)) == 6){
                      //no puede dia sabado
                      $resultado ['dia'] = "si";
                      sleep(1);
                      echo json_encode($resultado);
                  }else{
                      $resultado ['dia'] = "no";
                      sleep(1);
                      echo json_encode($resultado);
                  }
              }
          }else{
              //no puede pedir ese dia porque es feriado
              $resultado ['dia'] = "si";
              sleep(1);
              echo json_encode($resultado);
          }
    }
?>