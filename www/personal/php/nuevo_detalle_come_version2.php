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
    	include ("../../include/funciones/funciones.php");
    	$cnn = ConectarPersonal();
    	$oe_id = $_POST['id'];
    	$ote_dia = $_POST['dia'];
      $ote_hora_ini = $_POST['hora_ini'];
      $ote_hora_fin = $_POST['hora_fin'];
      $cd_porce = $_POST['porce1'];
      //valido que la fecha y el rango de hora sean validos
      $query_valido = "SELECT C.CO_ID,C.USU_RUT,D.CD_DIA,D.CD_HORA_INI,D.CD_HORA_FIN FROM COME_PERMI C INNER JOIN COME_DETALLE D ON (C.CO_ID = D.CO_ID) WHERE (C.USU_RUT = '$Srut') AND (D.CD_DIA = '$ote_dia') AND ((CO_ESTA <> 'CANCELADO POR USUARIO') AND (CO_ESTA <> 'RECHAZADO J.D.') AND (CO_ESTA <> 'RECHAZADO DIR'))";
      $respuesta = mysqli_query($cnn, $query_valido);
      $num_row = mysqli_num_rows($respuesta);
      $contador = 0;
      if (mysqli_num_rows($respuesta) != 0){
        //debo revisar los registros
        while ($row = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
          $hora_ini = $row[3];
          $hora_fin = $row[4];
          $inicio = "NO";
          $fin = "NO";
          if($ote_hora_ini >= $hora_ini && $ote_hora_ini < $hora_fin){
            //no valida hora inicio
            $resultado = "INICIO";
            sleep(1);
            echo json_encode($resultado);
            break;
          }elseif($ote_hora_fin > $hora_ini && $ote_hora_fin < $hora_fin){
            //no valida hora fin
            $resultado = "FIN";
            sleep(1);
            echo json_encode($resultado);
            break;
          }elseif($ote_hora_ini <= $hora_ini && $ote_hora_fin >= $hora_fin){
            $resultado = "INICIO-FIN";
            sleep(1);
            echo json_encode($resultado);
            break;
					}else{
            //pasa
            $inicio = "SI";
            $fin = "SI";
          }
          $contador = $contador + 1;
        }
        if($inicio == "SI" && $fin == "SI" && $contador == $num_row ){
          $NuevoDetalle = "INSERT INTO COME_DETALLE (CO_ID,CD_DIA,CD_HORA_INI,CD_HORA_FIN,CD_POR) VALUES ($oe_id,'$ote_dia','$ote_hora_ini','$ote_hora_fin','$cd_porce')";
          mysqli_query($cnn, $NuevoDetalle);
          $resultado = "OK";
          sleep(1);
          echo json_encode($resultado);
        }
      }else{
        //se guarda registro nuevo
        $NuevoDetalle = "INSERT INTO COME_DETALLE (CO_ID,CD_DIA,CD_HORA_INI,CD_HORA_FIN,CD_POR) VALUES ($oe_id,'$ote_dia','$ote_hora_ini','$ote_hora_fin','$cd_porce')";
    	  mysqli_query($cnn, $NuevoDetalle);
        $resultado = "OK";
        sleep(1);
        echo json_encode($resultado);
      }
    }
?>