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
        date_default_timezone_set("America/Santiago");
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Sfec_ingreso = $_SESSION['USU_FEC_ING'];
        $Sfec_inicio = $_SESSION['USU_FEC_INI'];
        $doc_id = $_POST['doc_id'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        if ($doc_id == 1){//feriado
            $FecActual = date("Y-m-d");
            if($Sfec_inicio == $Sfec_ingreso){
              //validacion normal
              //$Sfec_ingreso = ("01-02-2001");
              list($año_actual, $mes_actual, $dia_actual) = split('[-]', $FecActual);
              list($dia_ingreso, $mes_ingreso, $año_ingreso) = split('[-]', $Sfec_ingreso);
              if($año_actual - $año_ingreso == 0){
                  $semaforo_vacaciones = "NO";
              }elseif($año_actual - $año_ingreso == 1){
                  if($mes_actual == $mes_ingreso){
                      if($dia_actual == $dia_ingreso){
                          $semaforo_vacaciones = "SI";
                      }elseif($dia_actual > $dia_ingreso){
                          $semaforo_vacaciones = "SI";
                      }elseif($dia_actual < $dia_ingreso){
                          $semaforo_vacaciones = "NO";
                      }
                  }elseif($mes_actual > $mes_ingreso){
                      $semaforo_vacaciones = "SI";
                  }elseif($mes_actual < $mes_ingreso){
                      $semaforo_vacaciones = "NO";
                  }
              }elseif($año_actual - $año_ingreso >= 2){
                  $semaforo_vacaciones = "SI";
              }
            }else{
              //calcular nueva fecha
              $query_ano_inicio = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$Srut."') AND (CB_ESTADO > '0') ORDER BY CB_FEC_INI";
              $res_ai = mysqli_query($cnn, $query_ano_inicio);
              while ($row_ai = mysqli_fetch_array($res_ai)){
                  if($row_ai[2] == 1){
                      $date1=date_create($row_ai[0]);
                      $date2=date_create($FecActual);
                      $diff=date_diff($date1,$date2);
                      $cuentabienios = $cuentabienios + $diff->format('%Y'); //$diff->format('%R%a');
                      $final2 = $FecActual;
                      break 1;
                  }else{ 
                      if($row_ai[0] == $inicial){
                          $inicial2 = $row_ai[0];
                          $final2 = $row_ai[1];
                      }else{
                          if($row_ai[0] >= $final2){
                              $date1=date_create($final2);
                              $date2=date_create($row_ai[0]);
                              $diff=date_diff($date1,$date2);
                              $diasno = $diasno + $diff->format('%R%a');
                              if($diasno <= 1){
                                  $diasno = 0;
                              }
                              $final2= $row_ai[1];
                              if($final2 <= $row_ai[1]){
                                  $final2= $row_ai[1];                                                        
                              }
                          }else{
                              if($final2 <= $row_ai[1]){
                                  $final2 = $row_ai[1];
                              }
                          }                                                                                                                   
                      }                                        
                  }
              }
              if($final2 > $FecActual){
                  $final2 = $FecActual;
              }
              if($diasno > 0){
                  $nuevainicial = date_create($Sfec_inicio);
                  date_add($nuevainicial, date_interval_create_from_date_string("$diasno days"));
                  date_format($nuevainicial, 'Y-m-d');
																		$nuevainicial2 =  date_format($nuevainicial, 'Y-m-d');   // $nuevainicial2 para saber próximo cumplimiento de bienio 
                  $date2=date_create($final2);
                  $diff=date_diff($nuevainicial,$date2);
                  $cuentabienios = $diff->format('%Y');
              }else{
                  $date1=date_create($Sfec_inicio);
                  $date2=date_create($final2);                                        
                  $diff=date_diff($date1,$date2);
                  $cuentabienios =  $diff->format('%Y');
              }
														if($nuevainicial2==""){
                  $nuevainicial2 = $Sfec_inicio;
              }
              list($año_actual, $mes_actual, $dia_actual) = split('[-]', $FecActual);
              list($dia_ingreso, $mes_ingreso, $año_ingreso) = split('[-]', $nuevainicial2);
              if($año_actual - $año_ingreso == 0){
                  $semaforo_vacaciones = "NO";
              }elseif($año_actual - $año_ingreso == 1){
                  if($mes_actual == $mes_ingreso){
                      if($dia_actual == $dia_ingreso){
                          $semaforo_vacaciones = "SI";
                      }elseif($dia_actual > $dia_ingreso){
                          $semaforo_vacaciones = "SI";
                      }elseif($dia_actual < $dia_ingreso){
                          $semaforo_vacaciones = "NO";
                      }
                  }elseif($mes_actual > $mes_ingreso){
                      $semaforo_vacaciones = "SI";
                  }elseif($mes_actual < $mes_ingreso){
                      $semaforo_vacaciones = "NO";
                  }
              }elseif($año_actual - $año_ingreso >= 2){
                  $semaforo_vacaciones = "SI";
              }
            }
            if ($semaforo_vacaciones == "SI"){
                $año_actual = date("Y");
                $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FLA FROM BANCO_DIAS WHERE (USU_RUT = '$Srut') AND (BD_ANO = '$año_actual')";
                $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                if (mysqli_num_rows($resultado_banco_fl) != 0){
                    while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                        $num_fl  = $row_fl[1];
                        $num_fla = $row_fl[2];
                    }
                    $total_feriados = $num_fl + $num_fla;
                    $resultado ['doc_id'] = 1;
                    $resultado ['pendientes'] = $total_feriados;
                    $resultado ['fl'] = $num_fl;
                    $resultado ['fla'] = $num_fla;
                    $resultado ['motivo'] = "SI";
                    $resultado ['fec_ini'] = $nuevainicial2;
                    sleep(1);
                    echo json_encode($resultado);                    
                }
            }else{
               $resultado ['doc_id'] = 1;
                $resultado ['motivo'] = "NO";
                sleep(1);
                echo json_encode($resultado);
            }
        }elseif($doc_id == 2){//administrativo
            $año_actual = date("Y");
            $query_banco_adm = "SELECT BD_ID, BD_ADM FROM BANCO_DIAS WHERE (USU_RUT = '$Srut') AND (BD_ANO = '$año_actual')";
            $resultado_banco_adm = mysqli_query($cnn, $query_banco_adm);
            if (mysqli_num_rows($resultado_banco_adm) != 0){
                while ($row_adm = mysqli_fetch_array($resultado_banco_adm)){
                    $num_adm  = $row_adm[1];
                }
            }
            $resultado ['doc_id'] = 2;
            $resultado ['pendientes'] = $num_adm;
            sleep(1);
            echo json_encode($resultado);
        }elseif($doc_id == 3){//complementario 
            $FecActual = date("Y-m-d");
            list($año_actual, $mes_actual, $dia_actual) = split('[-]', $FecActual);
            $FecIni = ($año_actual - 2)."-".$mes_actual."-".$dia_actual;
            $query_banco_hora = "SELECT BH_SALDO FROM BANCO_HORAS WHERE (USU_RUT = '$Srut') AND (BH_SALDO > 0) AND (BH_FEC BETWEEN '$FecIni' AND '$FecActual') AND ((BH_TIPO = 'INICIAL') OR (BH_TIPO = 'INGRESO')) ORDER BY BH_FEC ASC";
            $resultado_hora = mysqli_query($cnn, $query_banco_hora);
            if (mysqli_num_rows($resultado_hora) != 0){
                while ($row_hora = mysqli_fetch_array($resultado_hora)){
                    $horas  = $row_hora[0] + $horas;
                }
                $resultado ['doc_id'] = 3;
                $resultado ['pendientes'] = $horas;
                sleep(1);
                echo json_encode($resultado);  
            }else{
                $resultado ['doc_id'] = 3;
                $resultado ['pendientes'] = 0;
                sleep(1);
                echo json_encode($resultado);   
            }
        }
    }
?>