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
        if($Tipo == 1 || $Tipo == 2){
            //trabajar con dias
            $cantidad = $_POST['cantDIAS'];
            $fechaINI = $_POST['fechaINI'];
            $cantDMES = date('t', strtotime($fechaINI)); //dias que tiene el mes
            $diaSemana = date('N',strtotime($fechaINI)); //6 sabado - 7 domingo
            $dia = date('d',strtotime($fechaINI)); //dia actual
            $mes = date('m',strtotime($fechaINI)); //mes actual
            $año = date('Y',strtotime($fechaINI)); //año actual
            //si chofer ambulancia y tipo 2 corren los sabado y domingos para administrativos
            if($Sest_id == 3 && $Sprof == "CHOFER AMBULANCIA" && $Tipo == 2){
              $cantidad = $cantidad - 1;
              $fechaFIN = strtotime ( $cantidad." day" , strtotime ( $fechaINI ) ); 
              $fechaFIN = date ( 'Y-m-d' , $fechaFIN );
              $resultado ['TipoDoc'] = $Tipo;
              $resultado ['fechaFIN'] = $fechaFIN;
              sleep(1);
              echo json_encode($resultado);
            }else{
              //echo "hola";
              if($cantidad == 1){
                $fechaFIN = $_POST['fechaINI'];
                $resultado ['TipoDoc'] = $Tipo;
                $resultado ['fechaFIN'] = $fechaFIN;
                sleep(1);
                echo json_encode($resultado);
              }else{
                $ContadorDiasHabil=1;
                while ($cantidad > $ContadorDiasHabil) {
                    //echo $dia;
                    $nuevoDia = $dia + 1;
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
                            $primerdiaNMES = "01";
                            $fechaNMES = $año."-".$nuevoMes."-".$primerdiaNMES;
                            $cantDMES = date('t', strtotime($fechaNMES)); //dias que tiene el mes
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
            }
        }elseif($Tipo == 3){
            $cantidadDias = $_POST['cantDIAS'];
            $cantidadHoras = $cantidadDias * 9;
            $fechaINI = $_POST['fechaINI'];
            //$horasPendientes = $_POST['HorasPendi'];
            //calculo horas segun fecha inicio
            list($año_actual, $mes_actual, $dia_actual) = split('[/]', $fechaINI);
            $FecIniQ = ($año_actual - 2)."/".$mes_actual."/".$dia_actual;
            $query_banco_hora = "SELECT BH_SALDO FROM BANCO_HORAS WHERE (USU_RUT = '$Srut') AND (BH_SALDO > 0) AND (BH_FEC BETWEEN '$FecIniQ' AND '$fechaINI') AND ((BH_TIPO = 'INICIAL') OR (BH_TIPO = 'INGRESO')) ORDER BY BH_FEC ASC";
            $resultado_hora = mysqli_query($cnn, $query_banco_hora);
            if (mysqli_num_rows($resultado_hora) != 0){
                while ($row_hora = mysqli_fetch_array($resultado_hora)){
                    $horas  = $row_hora[0] + $horas;
                }
                $horasPendientes = $horas;
                $cantDMES = date('t', strtotime($fechaINI)); //dias que tiene el mes
                $diaSemana = date('N',strtotime($fechaINI)); //6 sabado - 7 domingo
                $dia = date('d',strtotime($fechaINI)); //dia actual
                $mes = date('m',strtotime($fechaINI)); //mes actual
                $año = date('Y',strtotime($fechaINI)); //año actual
                //echo "hola";
                //variable descuento una hora por si viernes
                $cantidadHoras2 = $cantidadHoras - 1;
                if ($cantidadHoras <= $horasPendientes || $cantidadHoras2 <= $horasPendientes){
                    if(date('N',strtotime($fechaINI)) == 5){
                        $ContadorHoras=8;
                    }else{
                        $ContadorHoras=9;
                    }
                    //echo $ContadorHoras."<br>";
                    $dif = $cantidadHoras - $ContadorHoras;
                    //echo $dif;
                    while ($dif >= 9) {
                        //echo $dia;
                        $nuevoDia = $dia + 1;
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
                                    //ver si el dia es viernes
                                    if(date('N',strtotime($fechaNDIA)) == 5){
                                        //es viernes
                                        //termina el ciclo
                                        $ContadorHoras = $ContadorHoras + 8;
                                        $dia = $nuevoDia;
                                        $dif = $cantidadHoras - $ContadorHoras;
                                        $fechaFIN = $fechaNDIA;
                                        //echo $fechaNDIA."</br>";
                                    }else{
                                        //lunes a jueves
                                        //termina el ciclo
                                        $ContadorHoras = $ContadorHoras + 9;
                                        $dia = $nuevoDia;
                                        $dif = $cantidadHoras - $ContadorHoras;
                                        $fechaFIN = $fechaNDIA;
                                    }
                                }
                            }else{
                                $dia = $nuevoDia;
                            }
                        }elseif ($nuevoDia > $cantDMES) {
                            $nuevoMes = $mes + 1;
                            if ($nuevoMes <= 12){
                                $primerdiaNMES = "01";
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
                                        //ver si el dia es viernes
                                        if(date('N',strtotime($fechaNMES)) == 5){
                                            //es viernes
                                            //termina el ciclo
                                            $ContadorHoras = $ContadorHoras + 8;
                                            $dia = $primerdiaNMES;
                                            $mes = $nuevoMes;
                                            $dif = $cantidadHoras - $ContadorHoras;
                                            $fechaFIN = $fechaNMES;
                                        }else{
                                            //lunes a jueves
                                            //termina el ciclo
                                            $ContadorHoras = $ContadorHoras + 9;
                                            $dia = $primerdiaNMES;
                                            $mes = $nuevoMes;
                                            $dif = $cantidadHoras - $ContadorHoras;
                                            $fechaFIN = $fechaNMES;
                                        }
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
                                        //ver si el dia es viernes
                                        if(date('N',strtotime($fechaNAÑO)) == 5){
                                            //es viernes
                                            //termina el ciclo
                                            $ContadorHoras = $ContadorHoras + 8;
                                            $dia = $diaNAÑO;
                                            $mes = $mesNAÑO;
                                            $año = $nuevoAño;
                                            $dif = $cantidadHoras - $ContadorHoras;
                                            $fechaFIN = $fechaNAÑO;
                                        }else{
                                            //lunes a jueves
                                            //termina el ciclo
                                            $ContadorHoras = $ContadorHoras + 9;
                                            $dia = $diaNAÑO;
                                            $mes = $mesNAÑO;
                                            $año = $nuevoAño;
                                            $dif = $cantidadHoras - $ContadorHoras;
                                            $fechaFIN = $fechaNAÑO;
                                        }
                                    }
                                }else{
                                    $dia = $diaNAÑO;
                                    $mes = $mesNAÑO;
                                    $año = $nuevoAño;
                                }
                            }
                        }
                    }
                    //horas restantes por pedir
                    $HorasRestantes = $horasPendientes - $ContadorHoras;
                    $resultado ['TipoDoc'] = $Tipo;
                    $resultado ['Mensaje'] = "NO";
                    $resultado ['HorasPedidas'] = $ContadorHoras;
                    $resultado ['HorasPendientes'] = $HorasRestantes;
                    $resultado ['fechaFIN'] = $fechaFIN;
                    $resultado ['saldo'] = $horasPendientes;
                    sleep(1);
                    echo json_encode($resultado);
                }else{
                    $resultado ['TipoDoc'] = $Tipo;
                    $resultado ['Mensaje'] = "SI";
                    $resultado ['saldo'] = $horasPendientes;
                    sleep(1);
                    echo json_encode($resultado);
                }  
            }else{
                //no tiene horas
                $resultado ['TipoDoc'] = $Tipo;
                $resultado ['Mensaje'] = "SI";
                $resultado ['saldo'] = 0;
                sleep(1);
                echo json_encode($resultado);
            }
        }
    }
?>