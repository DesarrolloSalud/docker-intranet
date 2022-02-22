<?php 
  date_default_timezone_set("America/Santiago");
  include ("../include/funciones/funciones.php");
  $cnn = ConectarPersonal();
  $query_ot = "SELECT U.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,U.USU_SEXO,U.USU_FEC_NAC,U.USU_NACIONAL,U.USU_CAT,U.USU_NIV,U.USU_PROF,U.USU_CONTRA,U.USU_FEC_INI,U.USU_FEC_ING FROM USUARIO U INNER JOIN ESTABLECIMIENTO E ON U.EST_ID = E.EST_ID WHERE U.USU_ESTA = 'ACTIVO' AND (U.USU_RUT != '15.102.972-8') AND (U.USU_RUT != '16.210.886-7')";
  // $query_ot = "SELECT U.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,U.USU_SEXO,U.USU_FEC_NAC,U.USU_NACIONAL,U.USU_CAT,U.USU_NIV,U.USU_PROF,U.USU_CONTRA,U.USU_FEC_INI,U.USU_FEC_ING FROM USUARIO U INNER JOIN ESTABLECIMIENTO E ON U.EST_ID = E.EST_ID WHERE U.USU_ESTA = 'ACTIVO' AND U.USU_RUT = '13.717.879-6'";
  $respuesta_ot = mysqli_query($cnn, $query_ot);
  while ($row = mysqli_fetch_array($respuesta_ot, MYSQLI_NUM)){
    $MuestroRut=$row[0];
    $MuestroNombre=utf8_encode($row[1]);
    $MuestroApellidoP = utf8_encode($row[2]);
    $MuestroApellidoM = utf8_encode($row[3]);
    $MuestroEstablecimiento = utf8_encode($row[4]);
    $MuestroSexo = utf8_encode($row[5]);
    $MuestroFechaNacimiento = utf8_encode($row[6]);
    $MuestroNacionalidad = utf8_encode($row[7]);
    $MuestroCategoria = $row[8];
    $MuestroNivel = $row[9];
    $MuestroProfesion = $row[10]; 
    $MuestroContrato = $row[11]; 
    $MuestroFechaIngreso = $row[13];
    $MuestroFechaInicio = $row[12];
    $consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$MuestroRut."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
    $resputbie = mysqli_query($cnn, $consultabie);
    while ($row_rs3 = mysqli_fetch_array($resputbie)){    
      if($row_rs3[2] == 1){
        if($row_rs3[0] >= $final2){
          $date1=date_create($final2);
          $date2=date_create($row_rs3[0]);
          $diff=date_diff($date1,$date2);
          $diasno = $diasno + $diff->format('%R%a');
          if($diasno <= 1){
            $diasno = 0;
          }
          $final2= $row_rs3[1];
          if($final2 <= $row_rs3[1]){
            $final2= $row_rs3[1];      
          } 
        }else{
          if($final2 <= $row_rs3[1]){
            $final2 = $row_rs3[1];
          }
        }
        $date1=date_create($row_rs3[0]);
        $date2=date_create($fecha);
        $diff=date_diff($date1,$date2);
        $cuentabienios = $cuentabienios + $diff->format('%Y'); 
        $final2 = $fecha;
        break 1;
      }else{
        if($row_rs3[0] == $inicial){
          $inicial2 = $row_rs3[0];
          $final2 = $row_rs3[1];      
        }else{
          if($row_rs3[0] >= $final2){
            $date1=date_create($final2);
            $date2=date_create($row_rs3[0]);
            $diff=date_diff($date1,$date2);
            $diff2=$diff->format('%R%a');   
            if($diff2 ==1){
              $diff2=0;                                 
            }else{
              $diff2=$diff2-1;
            }
            $diasno = $diasno + $diff2;
            if($diasno <= 1){
              $diasno = 0;
            }                      
            $final2= $row_rs3[1];
            if($final2 <= $row_rs3[1]){
              $final2= $row_rs3[1];    
            }
          }else{
            if($final2 <= $row_rs3[1]){
              $final2 = $row_rs3[1];             
            }
          }                                            
        }                 
      }
    }
    if($final2 > $fecha){
      $final2 = $fecha;
    }
    if($diasno > 0){
      $nuevainicial = date_create($MuestroFechaInicio);
      date_add($nuevainicial, date_interval_create_from_date_string("$diasno days"));
      date_format($nuevainicial, 'Y-m-d');
      $nuevainicial2 = date_format($nuevainicial, 'Y-m-d');
      $date2=date_create($final2);
      $interval=date_diff($nuevainicial,$date2);
      $cuentabienios = $interval->format('%Y');                                        
    }else{                                        
      $date1=date_create($MuestroFechaInicio);
      $date2=date_create($final2);      
      $interval=date_diff($date1,$date2);   
      $cuentabienios =  $interval->format('%Y'); 
    } 
    if($nuevainicial2==""){
      $nuevainicial2 = $MuestroFechaInicio;
    }
    while ($nuevainicial2 <= $fecha){
      $nuevainicial3 = date_create($nuevainicial2);
      date_add($nuevainicial3, date_interval_create_from_date_string('2 years'));
      date_format($nuevainicial3, 'Y-m-d');
      $nuevainicial2 =  date_format($nuevainicial3, 'Y-m-d');
    }  
    if($cuentabienios%2==0){ 
      $valido_bie = $cuentabienios * 1;
    }else{
      $valido_bie= $cuentabienios - 1;
    }
    //muestro todo por pantalla
    // echo $MuestroEstablecimiento.";".$MuestroRut.";".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM.";".$MuestroCategoria.";".$MuestroContrato.";".$MuestroFechaIngreso.";".$MuestroFechaInicio.";".$cuentabienios.";".$valido_bie;
    // if($cuentabienios >= 3 && ($MuestroContrato === 'PLAZO FIJO' || $MuestroContrato === 'CONTRATA')){
    if($cuentabienios >= 3 && ($MuestroContrato === 'PLAZO FIJO' || $MuestroContrato === 'CONTRATA')){
      // echo $MuestroEstablecimiento.";".$MuestroRut.";".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM.";".$MuestroCategoria.";".$MuestroContrato.";".$MuestroFechaIngreso.";".$cuentabienios;
      echo $MuestroEstablecimiento.";".$MuestroRut.";".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM.";".$MuestroCategoria.";".$MuestroContrato.";".$MuestroFechaIngreso;
      echo "<br>";
      $experiencia = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_ESTABLE,CB_TIP_DOC,CB_CALJURI FROM CARRERA_BIENIO WHERE (USU_RUT='".$MuestroRut."') AND (CB_ESTADO = '1') AND (CB_CALJURI != 'REEMPLAZO') ORDER BY CB_FEC_INI";
      echo '<br>';
      $respExp = mysqli_query($cnn, $experiencia);
      // VARIABLES 
      $cont_años  = 0;
      $cont_meses = 0;
      $cont_dias  = 0;
      $total_dias_mes = 0;
      // VARIABLES TEMPORALES
      $temp_fec_ini   = '';
      $temp_fec_fin   = '';
      $temp_cant_dia  = 0;
      $temp_cant_mes  = 0;
      $temp_cant_año  = 0;
      $cont_ite = 0;
      while ($contra = mysqli_fetch_array($respExp)){
        // echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
        // echo '<br>';
        // echo $cont_ite;
        // echo '<br>';
        $cont_ite ++;
        $berta = ($cont_ite == 6 || $cont_ite == 9) && $MuestroRut == '16.766.402-4';
        $patriciaGarrido = ($cont_ite == 1 || $cont_ite == 2 || $cont_ite == 3 || $cont_ite == 4 || $cont_ite == 5 || $cont_ite == 6) && $MuestroRut == '13.717.879-6';
        $constanzaHermosilla = $MuestroRut == '18.040.384-1' && ($cont_ite == 6);
        $constanzaCornejo = $MuestroRut == '18.261.054-2' && ($cont_ite == 2 || $cont_ite == 3);
        $franciscaQuezada = $MuestroRut == '18.262.024-6' && ($cont_ite == 2);
        $veronicaValbuena = $MuestroRut == '25.865.210-K' && ($cont_ite == 7);
        $tatianaDiaz = $MuestroRut == '14.356.994-2' && ($cont_ite == 5 || $cont_ite == 6 || $cont_ite == 7);
        if($berta || $constanzaHermosilla || $constanzaCornejo || $franciscaQuezada || $veronicaValbuena || $tatianaDiaz || $patriciaGarrido){
        // if($patriciaGarrido){
          echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4].'NO SUMA';
          echo "<br>";
          continue;
        }else{
          // echo '<br>';
          // echo 'INICIO TEMPORAL :'.$temp_fec_ini;
          // echo '<br>';
          // echo 'FIN TEMPORAL :'.$temp_fec_fin;
          // echo '<br>';
          if($contra[2] == 'DEPARTAMENTO DE SALUD I.MUNICIPALIDAD DE RENGO' && $contra[4] != 'REEMPLAZO'){
            $arr_ini = explode("-", $contra[0]);
            $arr_fin = explode("-", $contra[1]);
            $inicio = $contra[0] == $arr_ini[0].'-01-01';
            $fin = $contra[1] == $arr_fin[0].'-12-31';
            if(!empty($temp_fec_ini) && !empty($temp_fec_fin)){
              $temp_inicio = $temp_fec_ini == $arr_ini[0].'-01-01';
              $temp_fin = $temp_fec_fin === $arr_fin[0].'-12-31';
              // echo $temp_fec_ini;
              // echo '<br>';
              // echo $arr_ini[0].'-01-01';
              // echo '<br>';
              // echo $temp_fec_ini.' - '.$temp_fec_fin;
              // echo '<br>';
              // echo 'elba';
              // echo '<br>';
              if($temp_inicio){
                $mod_date = date("Y-m-d",strtotime($temp_fec_fin."+ 1 days"));
                echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                echo "<br>";
                if($mod_date == $contra[0]){
                  //continuidad
                  if($fin){
                    if($temp_fec_ini == '2021-01-01' && $contra[1] == '2021-12-31'){
                      $cont_meses = $cont_meses + 9;
                    }else{
                      $cont_años = $cont_años + 1;
                    }
                    //sumo un año y elimino temporales
                    $temp_fec_ini   = '';
                    $temp_fec_fin   = '';
                  }else{
                    $temp_fec_fin = $contra[1];
                    echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                    echo "<br>";
                  }
                }else{
                  //si no es continuidad sumo meses temporales y guardo nuevas fechas en temporal
                  $temp_arr_ini = explode("-", $temp_fec_ini);
                  $temp_arr_fin = explode("-", $temp_fec_fin);
                  $total_dias_mes = date('t',strtotime($temp_fec_fin));
                  if($temp_arr_fin[2] == $total_dias_mes){
                    $diff_meses_temp = $temp_arr_fin[1] - $temp_arr_ini[1];
                    $cont_meses = $cont_meses + ($diff_meses_temp + 1);
                  }else{
                    $cont_dias = $temp_arr_fin[2] + $cont_dias;
                    $temp_cant_mes = $temp_arr_fin[1] - $temp_arr_ini[1];
                    $cont_meses = $cont_meses + $temp_cant_mes;
                    $temp_cant_mes = 0;
                    $temp_cant_dia = 0;
                  }
                  $temp_fec_ini   = $contra[0];
                  $temp_fec_fin   = $contra[1];
                  $diff_meses_temp  = 0;
                }
              }else{
                // fecha inicial temporal no es primero de enero
                echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                echo "<br>";
                $temp_arr_ini = explode("-", $temp_fec_ini);
                $temp_arr_fin = explode("-", $temp_fec_fin);
                if($fin){
                  
                  if($temp_arr_fin[0] == $arr_ini[0]){
                    // calculo dia meses desde temp_fec_ini hasta contra[1]
                    if($temp_arr_ini[2] == '01'){
                      // solo sumo meses
                      $temp_cant_mes = $arr_fin[1] - $temp_arr_ini[1];
                      $cont_meses = ($temp_cant_mes +1) + $cont_meses;
                      $temp_fec_ini   = '';
                      $temp_fec_fin   = '';
                      $temp_cant_mes = 0;
                    }else{
                      // dias y meses
                      // sumo dias primer mes
                      echo 'AQUI';
                      $total_dias_mes = date('t',strtotime($temp_fec_ini));
                      $temp_cant_dia = $total_dias_mes - $temp_arr_ini[2];
                      $temp_cant_dia = $temp_cant_dia + 1;
                      $cont_dias = $temp_cant_dia + $cont_dias;
                      $mod_date = date("Y-m-d",strtotime($temp_fec_fin."+ 1 days"));
                      if($mod_date == $contra[0]){
                        // sumo un mes a temp_fec_ini
                        $mod_mes = date("Y-m-d",strtotime($temp_fec_ini."+ 1 months"));
                        $temp_mes_ini = explode("-", $mod_mes);
                        $temp_cant_mes = $arr_fin[1] - $temp_mes_ini[1];
                        $cont_meses = ($temp_cant_mes +1) + $cont_meses;
                      }else{
                        $total_dias_mes = date('t',strtotime($contra[0]));
                        $temp_cant_dia = $total_dias_mes - $arr_ini[2];
                        $temp_cant_dia = $temp_cant_dia + 1;
                        $cont_dias = $temp_cant_dia + $cont_dias;
                        // sumar meses temporales
                        $mod_mes = date("Y-m-d",strtotime($temp_fec_ini."+ 1 months"));
                        $temp_mes_ini = explode("-", $mod_mes);
                        $temp_cant_mes = $temp_arr_fin[1] - $temp_mes_ini[1];
                        $cont_meses = ($temp_cant_mes +1) + $cont_meses;
                        $mod_mes = date("Y-m-d",strtotime($contra[0]."+ 1 months"));
                        $temp_mes_ini = explode("-", $mod_mes);
                        $temp_cant_mes = $arr_fin[1] - $temp_mes_ini[1];
                        $cont_meses = ($temp_cant_mes +1) + $cont_meses;
                      }
                      $temp_fec_ini   = '';
                      $temp_fec_fin   = '';
                      $temp_cant_mes = 0;
                      $temp_cant_dia = 0;
                    }
                  }else{
                    echo 'AQUI';
                    echo '<br>';
                    // calculo meses y dias temporales
                    // ver si fec de inicio es principio de mes
                    $total_dias_mes = date('t',strtotime($temp_fec_fin));
                    if(($temp_arr_ini[2] == '01') && ($total_dias_mes == $temp_arr_fin[2])){
                      // solo sumo meses
                      $temp_cant_mes = $temp_arr_fin[1] - $temp_arr_ini[1];
                      $cont_meses = $temp_cant_mes + $cont_meses;
                    }else if(($temp_arr_ini[2] != '01') && ($total_dias_mes == $temp_arr_fin[2])){
                      // sumo dias mes inicial y meses
                      $total_dias_ini = date('t',strtotime($temp_fec_ini));
                      $temp_cant_dia = $total_dias_ini - $temp_arr_ini[2];
                      $temp_cant_dia = $temp_cant_dia + 1;
                      $cont_dias = $temp_cant_dia + $cont_dias;
                      $temp_cant_mes = $temp_arr_fin[1] - $temp_arr_ini[1];
                      $cont_meses = $temp_cant_mes + $cont_meses;
                    }else if(($temp_arr_ini[2] != '01') && ($total_dias_mes != $temp_arr_fin[2])){
                      // sumo dias inicial y fin mas meses
                      echo 'AQUI';
                      echo '<br>';
                      if($temp_arr_ini[1] == $temp_arr_fin[1]){
                        $temp_cant_dia = $temp_arr_fin[2] - $temp_arr_ini[2];
                        $temp_cant_dia = $temp_cant_dia + 1;
                        $cont_dias = $temp_cant_dia + $cont_dias;
                      }else{
                        $total_dias_ini = date('t',strtotime($temp_fec_ini));
                        $temp_cant_dia = $total_dias_ini - $temp_arr_ini[2];
                        $temp_cant_dia = $temp_cant_dia + 1;
                        $cont_dias = $temp_cant_dia + $cont_dias + $temp_arr_fin[2];
                        $temp_cant_mes = (($temp_arr_fin[1] - 1) - ($temp_arr_ini[1] + 1)) + 1; 
                        $cont_meses = $temp_cant_mes + $cont_meses;
                      }
                    }
                    // calculo meses y dias actuales
                    // ver que no sea el mismo mes
                    if($arr_ini[1] == $arr_fin[1]){
                      //solo diferencia de dias
                    }else{
                      if($arr_ini[2] == '01'){
                        // solo sumo meses
                        $temp_cant_mes = $arr_fin[1] - $arr_ini[1];
                        $cont_meses = $temp_cant_mes + $cont_meses;
                      }else{
                        //sumo dias y meses
                        $total_dias_ini = date('t',strtotime($contra[0]));
                        $temp_cant_dia = ($total_dias_ini - $arr_ini[2]) + 1;
                        $cont_dias = $temp_cant_dia + $cont_dias;
                        $temp_cant_mes = $arr_fin[1] - $arr_ini[1];
                        $cont_meses = $temp_cant_mes + $cont_meses;
                      }
                    }
                    if($inicio && $fin){
                      echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                      echo "<br>";
                      if($contra[0] == '2021-01-01' && $contra[1] == '2021-12-31'){
                        $cont_meses = $cont_meses + 9;
                      }else{
                        $cont_años = $cont_años + 1;
                      }
                    }else if($inicio){
                      // guardar fechas en temporal
                      $temp_fec_ini   = $contra[0];
                      $temp_fec_fin   = $contra[1];
                      echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                      echo "<br>";
                    }else if(!$inicio && $fin){
                      echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                      echo "<br>";
                      // ver si fecha fin es despues del 09-30 y calcular nueva diferencia
                      if($arr_fin[0] == '2021' && $arr_fin[1] >= '10'){
                        $arr_fin_new = '2021-09-30';
                        $arr_fin = explode("-", $arr_fin_new);
                      }
                      // CALCULAR MESES Y DIAS
                      if($arr_ini[2] == '01'){
                        // SOLO MESES
                        $temp_cant_mes = $arr_fin[1] - $arr_ini[1];
                        $cont_meses = ($temp_cant_mes +1) + $cont_meses;
                        $temp_cant_mes = 0;
                      }else{
                        // DIAS Y MESES
                        $total_dias_inicio = date('t',strtotime($contra[0]));
                        $temp_cant_dia = $total_dias_inicio - $arr_ini[2];
                        $temp_cant_dia = $temp_cant_dia + 1;
                        $cont_dias = $temp_cant_dia + $cont_dias;
                        // MESES
                        $temp_cant_mes = $arr_fin[1] - $arr_ini[1];
                        $cont_meses = ($temp_cant_mes) + $cont_meses;
                        $temp_cant_mes = 0;
                        $temp_cant_dia = 0;
      
                      }
                    }else if(!$inicio && !$fin){
                      $temp_fec_ini   = $contra[0];
                      $temp_fec_fin   = $contra[1];
                      echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4].' PASA A TEMPORAL PARA SU CALCULO';
                      echo "<br>";
                    }
                    $temp_cant_dia = 0;
                    $temp_cant_mes = 0;
                    $temp_fec_ini   = '';
                    $temp_fec_fin   = '';
                  }
                }else{
                  // fin no es 12-31 se debe volver a revisar continuidad
                  $mod_date = date("Y-m-d",strtotime($temp_fec_fin."+ 1 days"));
                  echo $mod_date;
                  echo '<br>';
                  if($mod_date == $contra[0]){
                    $temp_fec_fin = $contra[1];
                    echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                    echo "<br>";
                  }else{
                    // no es continuidad revisar dias y meses 
                    // ver si es temp inicio y fin son el mismo mes
                    if($temp_arr_ini[1] == $temp_arr_fin[1]){
                      // calcular diferencia de dias entre temporal inicio y temporal mes ?
                      $cont_dias = $cont_dias + (($temp_arr_fin[2] - $temp_arr_ini[2]) + 1);
                      $temp_fec_ini   = $contra[0];
                      $temp_fec_fin   = $contra[1];
                      $temp_cant_mes = 0;
                      $temp_cant_dia = 0;
                    }else{
                      $diff_meses_temp = $temp_arr_fin[1] - $temp_arr_ini[1];
                      echo $diff_meses_temp;
                      echo '<br>';
                      if($diff_meses_temp == 1){
                        echo 'AQUI';
                        echo '<br>';
                        // calcular dias de temp inicial y aparte de temporal final
                        $total_dias_inicio = date('t',strtotime($temp_fec_ini));
                        $temp_cant_dia = $total_dias_inicio - $temp_arr_ini[2];
                        $temp_cant_dia = $temp_cant_dia + 1;
                        $cont_dias = $temp_cant_dia + $cont_dias;
                        // calculo dias mes fin
                        $cont_dias = $temp_arr_fin[2] + $cont_dias;
                        $temp_fec_ini   = $contra[0];
                        $temp_fec_fin   = $contra[1];
                        $temp_cant_mes = 0;
                        $temp_cant_dia = 0;
                      }else{
                        // aqui
                        // calcular dias de temp inicial y ver si temp final es ultimo dia de su mes calcular meses y dias
                        // ver si fecha inicial es 01
                        if($temp_arr_ini[2] == '01'){
                          // ver ultimo dia del mes final
                          $total_dias_fin = date('t',strtotime($temp_fec_fin));
                          if($total_dias_fin == $temp_arr_fin[2]){
                            // calcular meses
                            $temp_cant_mes = $temp_arr_fin[1] - $temp_arr_ini[1];
                            $temp_cant_mes = $temp_cant_mes + 1;
                            $cont_meses = $cont_meses + $temp_cant_mes;
                            $temp_fec_ini   = $contra[0];
                            $temp_fec_fin   = $contra[1];
                            $temp_cant_mes = 0;
                          }else{
                            $cont_dias = $temp_arr_fin[2] + $cont_dias;
                            $temp_cant_mes = $temp_arr_fin[1] - $temp_arr_ini[1];
                            $cont_meses = $cont_meses + $temp_cant_mes;
                            $temp_fec_ini   = $contra[0];
                            $temp_fec_fin   = $contra[1];
                            $temp_cant_mes = 0;
                            $temp_cant_dia = 0;
                          }
                        }else{
                          $total_dias_inicio = date('t',strtotime($temp_fec_ini));
                          $temp_cant_dia = $total_dias_inicio - $temp_arr_ini[2];
                          $temp_cant_dia = $temp_cant_dia + 1;
                          $cont_dias = $temp_cant_dia + $cont_dias;
                          // calculo dias mes fin
                          // feo si mes fin es mes completo
                          $total_dias_fin = date('t',strtotime($temp_fec_fin));
                          if($total_dias_fin == $temp_arr_fin[2]){
                            $temp_cant_mes = $temp_arr_fin[1] - $temp_arr_ini[1];
                            $temp_cant_mes = $temp_cant_mes + 1;
                            $cont_meses = $cont_meses + $temp_cant_mes;
                            $temp_fec_ini   = $contra[0];
                            $temp_fec_fin   = $contra[1];
                            $temp_cant_mes = 0;
                            $temp_cant_dia = 0;
                          }else{
                            $cont_dias = $temp_arr_fin[2] + $cont_dias;
                            // calcular meses
                            $nuevo_mes_temp_ini = $temp_arr_ini[1] + 1;
                            $nuevo_mes_temp_fin = $temp_arr_fin[1] - 1;
                            $temp_cant_mes = $nuevo_mes_temp_fin - $nuevo_mes_temp_ini;
                            $temp_cant_mes = $temp_cant_mes + 1;
                            $cont_meses = $cont_meses + $temp_cant_mes;
                            $temp_fec_ini   = $contra[0];
                            $temp_fec_fin   = $contra[1];
                            $temp_cant_mes = 0;
                            $temp_cant_dia = 0;
                          }
                        }
                      }
                    }
                  }
                }
              }
            }else{
              if($inicio && $fin){
                echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                echo "<br>";
                if($contra[0] == '2021-01-01' && $contra[1] == '2021-12-31'){
                  $cont_meses = $cont_meses + 9;
                }else{
                  $cont_años = $cont_años + 1;
                }
              }else if($inicio){
                // guardar fechas en temporal
                $temp_fec_ini   = $contra[0];
                $temp_fec_fin   = $contra[1];
                echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                echo "<br>";
              }else if(!$inicio && $fin){
                echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4];
                echo "<br>";
                // ver si fecha fin es despues del 09-30 y calcular nueva diferencia
                if($arr_fin[0] == '2021' && $arr_fin[1] >= '10'){
                  $arr_fin_new = '2021-09-30';
                  $arr_fin = explode("-", $arr_fin_new);
                }
                // CALCULAR MESES Y DIAS
                if($arr_ini[2] == '01'){
                  // SOLO MESES
                  $temp_cant_mes = $arr_fin[1] - $arr_ini[1];
                  $cont_meses = ($temp_cant_mes +1) + $cont_meses;
                  $temp_cant_mes = 0;
                }else{
                  // DIAS Y MESES
                  $total_dias_inicio = date('t',strtotime($contra[0]));
                  $temp_cant_dia = $total_dias_inicio - $arr_ini[2];
                  $temp_cant_dia = $temp_cant_dia + 1;
                  $cont_dias = $temp_cant_dia + $cont_dias;
                  // MESES
                  $temp_cant_mes = $arr_fin[1] - $arr_ini[1];
                  $cont_meses = ($temp_cant_mes) + $cont_meses;
                  $temp_cant_mes = 0;
                  $temp_cant_dia = 0;

                }
              }else if(!$inicio && !$fin){
                $temp_fec_ini   = $contra[0];
                $temp_fec_fin   = $contra[1];
                echo $contra[0].";".$contra[1].";".$contra[2].";".$contra[3].";".$contra[4].' PASA A TEMPORAL PARA SU CALCULO';
                echo "<br>";
              }
            }
          }
          echo 'AÑOS : '.$cont_años.' - MESES : '.$cont_meses.' - DIAS : '.$cont_dias;
          echo '<br>';
        }
      }
      // revisar que meses tengan 11 0 menos y dias 29 o menos
      if($cont_dias >= 30){
        while($cont_dias >= 30){
          $cont_dias = $cont_dias - 30;
          $cont_meses = $cont_meses + 1;
        }
      }
      if($cont_meses >= 12){
        while($cont_meses >= 12){
          $cont_meses = $cont_meses - 12;
          $cont_años = $cont_años + 1;
        }
      }
      echo 'AÑOS : '.$cont_años.' - MESES : '.$cont_meses.' - DIAS : '.$cont_dias;
      echo '<br>';
      echo '<br>';
      $capacitacion = "SELECT CA_DES,CA_HORA,CA_NIVEL,CA_NOTA,CA_TOTAL FROM CARRERA_ACT WHERE (USU_RUT='".$MuestroRut."' AND  CA_ESTADO != 'Inactivo') ORDER BY CA_FEC";
      $respCap = mysqli_query($cnn, $capacitacion);
      $puntajeTotal = 0;
      // while($capa = mysqli_fetch_array($respCap)){
      //   echo $capa[0].";".$capa[1].";".$capa[2].";".$capa[3].";".$capa[4];
      //   echo "<br>";
      //   // TODO sumar puntaje
      //   $puntajeTotal = $puntajeTotal + $capa[4];
      // }
      // echo "PUNTAJE TOTAL = ".$puntajeTotal;
      // echo "<br>";
    }
  }
?>