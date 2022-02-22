<?php 
    session_start();
    if(!isset($_SESSION['USU_RUT'])){
        session_destroy();
        header("location: ../../index.php");
    }else{
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $fecha = $_POST['fecha'];
        $añoIni = date("Y", strtotime($fecha));
        $fecha =  str_replace("/","-",$fecha);
        $query_bd = "SELECT BD_SGR, BD_SGR_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$Srut') AND (BD_ANO = '$añoIni')";
        $respuesta_bd = mysqli_query($cnn,$query_bd);
        if(mysqli_num_rows($respuesta_bd) != 0){
            $row_bd = mysqli_fetch_row($respuesta_bd);
            $sgr = $row_bd[0];
            $sgr_u = $row_bd[1];
            $ConsultaFecha = "SELECT SPR_FEC_INI,SPR_FEC_FIN FROM SOL_PSGR WHERE (USU_RUT =  '$Srut')";
            $RespuestaFecha = mysqli_query($cnn, $ConsultaFecha);
            $estado = "NO";
            if (mysqli_num_rows($RespuestaFecha) != 0){
                while ($row_rs = mysqli_fetch_array($RespuestaFecha, MYSQLI_NUM)){
                    if($estado == "NO"){
                        if( $fecha >= $row_rs[0] && $fecha <= $row_rs[1]){
                            $estado = "SI";
                            break 1;
                        }else{
                            $estado = "NO";
                        }
                    }else{
                        $resultado ['estado'] = $estado;
                        break 3;
                    }
                }
            }else{
                $estado = "NO";
            }
            $resultado ['sgr'] = $sgr;
            $resultado ['sgr_u'] = $sgr_u;
            $resultado ['estado'] = $estado; 
            $resultado ['año'] = "CARGADO";  
            sleep(1);
            echo json_encode($resultado); 
        }else{
            $resultado ['año'] = "CARGAR";  
            sleep(1);
            echo json_encode($resultado); 
        }
    }
?>