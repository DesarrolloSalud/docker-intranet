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
        $Tipo = $_POST['TipoDoc'];
        
            //trabajar con dias
            $cantidad = $_POST['cantDIAS'];
            $fechaINI = $_POST['fechaINI'];
            $cantDMES = date('t', strtotime($fechaINI)); //dias que tiene el mes
            $diaSemana = date('N',strtotime($fechaINI)); //6 sabado - 7 domingo
            $dia = date('d',strtotime($fechaINI)); //dia actual
            $mes = date('m',strtotime($fechaINI)); //mes actual
            $año = date('Y',strtotime($fechaINI)); //año actual
            
            $ContadorDiasHabil = 1;
            while($cantidad >$ContadorDiasHabil){
              $nuevoDia = $dia + 1;
              if($nuevoDia <= $cantDMES){
                $fechaNDIA = $año."-".$mes."-".$nuevoDia;
                $ConsultaFeriado ="SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC ='".$fechaNDIA."')";
                $RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
                if(mysqli_num_rows($RespuestaFeriado) == 0){
                  if(date('N',strtotime($fechaNDIA)) == 6 || date('N', strtotime($fechaNDIA)) == 7){
                    $dia = $nuevoDia;
                  }else{
                    $ContadorDiasHabil = $ContadorDiasHabil +1;
                    $dia = $nuevoDia;
                    $fechaFIN = $fechaNDIA;
                  }
                }
              }elseif($nuevoDia > $cantDMES){
                $nuevoMes = $mes + 1;
                if($nuevoMes <=12){
                  $primerdiaNMES ="01";
                  $fechaNMES = $año."-".$nuevoMes."-".$primerdiaNMES;
                  $ConsultaFeriado ="SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC ='".$fechaNDIA."')";
                  $RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
                    if(mysqli_num_rows($RespuestaFeriado) == 0){
                      if(date('N',strtotime($fechaNDIA)) == 6 || date('N', strtotime($fechaNDIA)) == 7){
                        $dia = $primerdiaNMES;
                        $mes = $nuevoMes;
                      }else{
                        $ContadorDiasHabil = $ContadorDiasHabil + 1;
                        $dia = $primerdiaNMES;
                        $mes = $nuevoMes;
                        $fechaFIN = $fechaNMES;
                      }                    
                    }else{
                        $dia = $primerdiaNMES;
                        $mes = $nuevoMes;
                    }
                }
              }
            }
              $ContadorDiasHabil=1;
              while ($cantidad > $ContadorDiasHabil) {
                  //echo $dia;
                  $nuevoDia = $dia + $cantidad-1;
                  if ($nuevoDia <= $cantDMES){
                      $fechaNDIA = $año."-".$mes."-".$nuevoDia;
                      //revisar si dia es feriado 
                      $ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$fechaNDIA."')";
                      $RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
                      if (mysqli_num_rows($RespuestaFeriado) == 0){
                          //revisar si es sabado o domingo
                          if (date('N',strtotime($fechaNDIA)) == 6 || date('N',strtotime($fechaNDIA)) == 7){
                              $dia = $nuevoDia;
                          }else{
                              //termina el ciclo
                              $ContadorDiasHabil = $ContadorDiasHabil + 1;
                              $dia = $nuevoDia;
                              $fechaFIN = $fechaNDIA;
                              //echo $fechaNDIA."</br>";
                          }
                      }else{
                          $dia = $nuevoDia;
                      }
                  }elseif ($nuevoDia > $cantDMES) {
                      $nuevoMes = $mes + 1;
                      if ($nuevoMes <= 12){
                          $primerdiaNMES = 1+;
                          $fechaNMES = $año."-".$nuevoMes."-".$primerdiaNMES;
                          //revisar si dia es feriado 
                          $ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$fechaNMES."')";
                          $RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
                          if (mysqli_num_rows($RespuestaFeriado) == 0){
                              //revisar si es sabado o domingo
                              if (date('N',strtotime($fechaNMES)) == 6 || date('N',strtotime($fechaNMES)) == 7){
                                  $dia = $primerdiaNMES;
                                  $mes = $nuevoMes;
                              }else{
                                  //termina el ciclo
                                  $ContadorDiasHabil = $ContadorDiasHabil + 1;
                                  $dia = $primerdiaNMES;
                                  $mes = $nuevoMes;
                                  $fechaFIN = $fechaNMES;
                                  //echo $fechaNMES."</br>";
                              }
                          }else{
                              $dia = $primerdiaNMES;
                              $mes = $nuevoMes;
                          }
                      }elseif ($nuevoMes > 12) {
                          $nuevoAño = $año + 1;
                          $mesNAÑO = "01";
                          $diaNAÑO = "01";
                          $fechaNAÑO = $nuevoAño."-".$mesNAÑO."-".$diaNAÑO;
                          //revisar si dia es feriado 
                          $ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$fechaNAÑO."')";
                          $RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
                          if (mysqli_num_rows($RespuestaFeriado) == 0){
                              //revisar si es sabado o domingo
                              if (date('N',strtotime($fechaNAÑO)) == 6 || date('N',strtotime($fechaNAÑO)) == 7){
                                  $dia = $diaNAÑO;
                                  $mes = $mesNAÑO;
                                  $año = $nuevoAño;
                              }else{
                                  //termina el ciclo
                                  $ContadorDiasHabil = $ContadorDiasHabil + 1;
                                  $dia = $diaNAÑO;
                                  $mes = $mesNAÑO;
                                  $año = $nuevoAño;
                                  $fechaFIN = $fechaNAÑO;
                                  //echo $fechaNAÑO."</br>";
                              }
                          }else{
                              $dia = $diaNAÑO;
                              $mes = $mesNAÑO;
                              $año = $nuevoAño;
                          }
                      }
                  }
              }
              
              $resultado ['TipoDoc'] = $Tipo;
              $resultado ['fechaFIN'] = $fechaFIN;
              sleep(1);
              echo json_encode($resultado);
            }
?>