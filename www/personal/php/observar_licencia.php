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
    $Srut = utf8_encode($_SESSION['USU_RUT']);
    $Snombre = utf8_encode($_SESSION['USU_NOM']);
    $SapellidoP = utf8_encode($_SESSION['USU_APP']);
    $SapellidoM = utf8_encode($_SESSION['USU_APM']);
    $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
    $Scargo = utf8_encode($_SESSION['USU_CAR']);
    include ("../../include/funciones/funciones.php");
    $cnn = ConectarPersonal();
    $lm_id = $_POST['id'];
    $query ="SELECT LM_ID,USU_RUT FROM LICENCIAS_MEDICAS WHERE (LM_ID = $lm_id)";
    $rs = mysqli_query($cnn, $query);
    if (mysqli_num_rows($rs) != 0){
    	$rowA = mysqli_fetch_row($rs);
        if ($rowA[0] == $lm_id){
          $usu_rut = $rowA[1];
        }
    }
    date_default_timezone_set("America/Santiago");
    $FecActual = date("Y-m-d");
    $HorActual = date("H:i:s");
    $ipcliente = getRealIP();
    $actualizarauot = "UPDATE LICENCIAS_MEDICAS SET  LM_ESTA = 'PAGADA', LM_OBSERVACION = 'SI' WHERE (LM_ID= $lm_id)";
    mysqli_query($cnn,$actualizarauot);

    $accionRealizada = utf8_decode("OBSERVO LICENCIA DE RUT :  ".$usu_rut." ID LICENCIA : ".$lm_id);
    $guardar_historial = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accionRealizada', 44, '$Srut', '$ipcliente', '$FecActual', '$HorActual')";                                           mysqli_query($cnn, $guardar_historial);
    $resultado ['estado'] = "Ok";
    sleep(1);
    echo json_encode($resultado);
?>