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
      $ote_tipo = $_POST['tipo'];
      //valido que la fecha y el rango de hora sean validos
      $query_valido = "SELECT O.OE_ID,O.USU_RUT,D.OTE_DIA,D.OTE_HORA_INI,D.OTE_HORA_FIN FROM OT_EXTRA O INNER JOIN OTE_DETALLE D ON (O.OE_ID = D.OE_ID) WHERE (O.USU_RUT = '$Srut') AND (D.OTE_DIA = '$ote_dia') AND (OTE_ESTA = 'ACTIVO')";
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
          $NuevoDetalle = "INSERT INTO OTE_DETALLE (OE_ID,OTE_DIA,OTE_HORA_INI,OTE_HORA_FIN,OTE_TIPO,OTE_ESTA) VALUES ($oe_id,'$ote_dia','$ote_hora_ini','$ote_hora_fin','$ote_tipo','ACTIVO')";
          mysqli_query($cnn, $NuevoDetalle);
          $resultado = "OK";
          sleep(1);
          echo json_encode($resultado);
        }
      }else{
        //se guarda registro nuevo
        $NuevoDetalle = "INSERT INTO OTE_DETALLE (OE_ID,OTE_DIA,OTE_HORA_INI,OTE_HORA_FIN,OTE_TIPO,OTE_ESTA) VALUES ($oe_id,'$ote_dia','$ote_hora_ini','$ote_hora_fin','$ote_tipo','ACTIVO')";
    	  mysqli_query($cnn, $NuevoDetalle);
        $resultado = "OK";
        sleep(1);
        echo json_encode($resultado);
      }
    }
?>