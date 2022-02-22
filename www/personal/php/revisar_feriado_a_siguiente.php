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
        $Singreso = $_SESSION['USU_FEC_ING'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $fecha = $_POST['fecha'];
        list($año_actual, $mes_actual, $dia_actual) = split('[/]', $fecha);
        list($dia_ingreso, $mes_ingreso, $año_ingreso) = split('[-]', $Singreso);
        $añocumplido = $año_ingreso +1;
        $fechacumplida = $dia_ingreso."/".$mes_ingreso."/".$añocumplido;
        if(($año_actual - $año_ingreso) == 0){
            $semaforo_vacaciones = "NO";
        }elseif(($año_actual - $año_ingreso) == 1){
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
        }elseif(($año_actual - $año_ingreso) >= 2){
            $semaforo_vacaciones = "SI";
        } 
        if($semaforo_vacaciones == "SI"){
            $ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$fecha."')";
            $RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
            if (mysqli_num_rows($RespuestaFeriado) == 0){
                //puede pedir ese dia revisar si es sabado o domingo
                if (date('w',strtotime($fecha)) == 0){
                    //no puede dia domingo
                    $resultado ['vacaciones'] = "si";
                    $resultado ['dia'] = "si";
                    sleep(1);
                    echo json_encode($resultado);
                }else{
                    //ver si es sabado
                    if (date('w',strtotime($fecha)) == 6){
                        //no puede dia sabado
                        $resultado ['vacaciones'] = "si";
                        $resultado ['dia'] = "si";
                        sleep(1);
                        echo json_encode($resultado);
                    }else{
                        $resultado ['vacaciones'] = "si";
                        $resultado ['dia'] = "no";
                        sleep(1);
                        echo json_encode($resultado);
                    }
                }
            }else{
                //no puede pedir ese dia porque es feriado
                $resultado ['vacaciones'] = "si";
                $resultado ['dia'] = "si";
                sleep(1);
                echo json_encode($resultado);
            }
        }else{
            $resultado ['vacaciones'] = "no";
            $resultado ['ingreso'] = $fechacumplida;
            sleep(1);
            echo json_encode($resultado);
        }
    }
?>