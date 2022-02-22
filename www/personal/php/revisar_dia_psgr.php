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
        $fecha = $_POST['fecha1'];
        $fecha =  str_replace("/","-",$fecha);
        $fecha2 = $_POST['fecha2'];
        $fecha2 =  str_replace("/","-",$fecha2);
        $ano1 = date_time_set('Y');
        echo $enero = "01-01-".$ano1;
        echo $diciembre ="12-31-".$ano1;


        $ConsultaFecha = "SELECT SPR_FEC_INI,SPR_FEC_FIN FROM SOL_PSGR WHERE (USU_RUT =  '$Srut')";
        $RespuestaFecha = mysqli_query($cnn, $ConsultaFecha);

            while ($row_rs = mysqli_fetch_array($RespuestaFecha, MYSQLI_NUM)){
                if( $fecha >= $row_rs[0] && $fecha <= $row_rs[1]){
                    $resultado ['fecha'] = "si";
                    sleep(1);
                    echo json_encode($resultado);
                    break;
                }
            }    

        /*if (mysqli_num_rows($RespuestaFecha) == 0){
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
        }*/
    }
?>