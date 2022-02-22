<?php 
date_default_timezone_set("America/Santiago"); 
$año = 2021;
$fecha = date("Y-m-d");
$hora = date("H:i:s");
include ("../include/funciones/funciones.php");
$cnn = ConectarPersonal();
$query_usuario = "SELECT USU_RUT,USU_FEC_INI,USU_NOM,USU_APP,USU_APM FROM USUARIO WHERE USU_ESTA = 'ACTIVO' AND USU_CONTRA != 'HONORARIO'";
$respuesta_usuario = mysqli_query($cnn, $query_usuario);
$BD_ADM     = 6;
$BD_FLA     = 0;
$BD_ADM_U   = 0;
$BD_FL_U    = 0;
$BD_SGR     = 90;
$BD_SGR_U   = 0;
while ($row = mysqli_fetch_array($respuesta_usuario, MYSQLI_NUM)){
    $usu_rut = $row[0];
    $fec_ini_sp = $row[1];
    $funcionario = $row[2]." ".$row[3]." ".$row[4];
    $query_ano_inicio = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$usu_rut."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
    $res_ai = mysqli_query($cnn, $query_ano_inicio);
    while ($row_ai = mysqli_fetch_array($res_ai)){
        if($row_ai[2] == 1){
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
            $date1=date_create($row_ai[0]);
            $date2=date_create($fecha);
            $diff=date_diff($date1,$date2);
            $cuentabienios = $cuentabienios + $diff->format('%Y'); //$diff->format('%R%a');
            $final2 = $fecha;
            break 1;
        }else{ 
            if($row_ai[0] == $fec_ini_sp){
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
    if($final2 > $fecha){
      $final2 = $fecha;
    }
    if($diasno > 0){
        $nuevainicial = date_create($fec_ini_sp);
        date_add($nuevainicial, date_interval_create_from_date_string("$diasno days"));
        date_format($nuevainicial, 'Y-m-d');
        $nuevainicial2 =  date_format($nuevainicial, 'Y-m-d');   // $nuevainicial2 para saber próximo cumplimiento de bienio 
        $date2=date_create($final2);
        $diff=date_diff($nuevainicial,$date2);
        $cuentabienios = $diff->format('%Y');
    }else{
        $date1=date_create($fec_ini_sp);
        $date2=date_create($final2);                                        
        $diff=date_diff($date1,$date2);
        $cuentabienios =  $diff->format('%Y');
    }
    if($nuevainicial2==""){
        $nuevainicial2 = $fec_ini_sp;
    }
    $cuentabienios = $cuentabienios * 1;
    if($cuentabienios < 15){
        $BD_FL      = 15;
    }elseif($cuentabienios >= 15 && $cuentabienios < 20){
        $BD_FL      = 20;  
    }elseif($cuentabienios >= 20){
        $BD_FL      = 25;
    }
    $GuardarNuevo = "INSERT INTO BANCO_DIAS (USU_RUT,BD_ADM,BD_FL,BD_FLA,BD_ANO,BD_ADM_USADO,BD_FL_USADO,BD_SGR,BD_SGR_USADO) VALUES ('$usu_rut','$BD_ADM',$BD_FL,$BD_FLA,'$año','$BD_ADM_U',$BD_FL_U,$BD_SGR,$BD_SGR_U)";
    //mysqli_query($cnn, $GuardarNuevo);
    echo $usu_rut.";".$funcionario.";".$BD_FL."</br>";
    $cuentabienios = 0;
    $diasno = 0;
    $nuevainicial2 = "";
} 
?>