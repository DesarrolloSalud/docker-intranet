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
        header("location: ../../../index.php");
    }else{
        $Srut = utf8_encode($_SESSION['USU_RUT']);
    	include ("../../../include/funciones/funciones.php");
    	$cnn = ConectarPersonal();
        $estadoact = $_POST['estadoact2'];
    	$rut = $_POST['rut_usu'];      
        $nombreact = utf8_decode($_POST['nombreact2']);
        $fechaact = $_POST['fechaact2'];
        $horaact = $_POST['horaact2'];
        $horapun = $_POST['horapun2'];
        $nivelact = $_POST['nivelact2'];
        $nivelpun = $_POST['nivelpun2'];
        $nota = $_POST['nota2'];
        $notapun =$_POST['notapun2'];
        $fechaing = $_POST['fechaing2'];
        $total = $_POST['totalact2'];
        $estado = $_POST['estado2'];     
    	if ($estado == 1){
            $consultact ="SELECT USU_RUT, CA_DES, CA_FEC, CA_HORA, CA_HORA_PUN, CA_NIVEL, CA_NIVEL_PUN, CA_NOTA, CA_NOTA_PUN, CA_FEC_ING, CA_TOTAL FROM CARRERA_ACT WHERE (USU_RUT ='$rut') AND (CA_DES ='$nombreact') AND (CA_FEC = '$fechaact') AND (CA_HORA ='$horaact') AND (CA_HORA_PUN ='$horapun') AND (CA_NIVEL='$nivelact') AND (CA_NIVEL_PUN = '$nivelpun') AND (CA_NOTA = '$nota') AND (CA_NOTA_PUN = '$notapun') AND (CA_FEC_ING = '$fechaing') AND (CA_TOTAL = '$total')";
            $rst =mysqli_query($cnn, $consultact);
            if(mysqli_num_rows($rst) != 0){
                $resultado ['estado'] = "REGISTRO EXISTE";
                sleep(1);
                //echo json_encode($resultado);
            }else{
                $agregaract = "INSERT INTO CARRERA_ACT (USU_RUT, CA_DES, CA_FEC, CA_HORA, CA_HORA_PUN, CA_NIVEL, CA_NIVEL_PUN, CA_NOTA, CA_NOTA_PUN, CA_FEC_ING, CA_TOTAL,CA_ESTADO) VALUES ('$rut', '$nombreact', '$fechaact', '$horaact', '$horapun', '$nivelact', '$nivelpun', '$nota', '$notapun', '$fechaing', '$total','$estadoact')";
                mysqli_query($cnn, $agregaract);  
                $resultadolg = "REGISTRO GUARDADO";
                $resultado ['estado'] = "REGISTRO GUARDADO";                
                sleep(1);
                //echo json_encode($resultado);
            }
        }elseif ($estado == 2) {
            $consultact ="SELECT USU_RUT, CA_DES, CA_FEC, CA_HORA, CA_HORA_PUN, CA_NIVEL, CA_NIVEL_PUN, CA_NOTA, CA_NOTA_PUN, CA_FEC_ING, CA_TOTAL FROM CARRERA_ACT WHERE (USU_RUT ='$rut') AND (CA_DES ='$nombreact') AND (CA_FEC = '$fechaact') AND (CA_HORA ='$horaact') AND (CA_HORA_PUN ='$horapun') AND (CA_NIVEL='$nivelact') AND (CA_NIVEL_PUN = '$nivelpun') AND (CA_NOTA = '$nota') AND (CA_NOTA_PUN = '$notapun') AND (CA_FEC_ING = '$fechaing') AND (CA_TOTAL = '$total')";
            $rst =mysqli_query($cnn, $consultact);
            if(mysqli_num_rows($rst) != 0){         
                $BorrarDetalle = "DELETE FROM CARRERA_ACT WHERE (USU_RUT ='$rut') AND (CA_DES ='$nombreact') AND (CA_FEC = '$fechaact') AND (CA_HORA ='$horaact') AND (CA_HORA_PUN ='$horapun') AND (CA_NIVEL='$nivelact') AND (CA_NIVEL_PUN = '$nivelpun') AND (CA_NOTA = '$nota') AND (CA_NOTA_PUN = '$notapun') AND (CA_FEC_ING = '$fechaing') AND (CA_TOTAL = '$total') ";
                mysqli_query($cnn, $BorrarDetalle);
                $resultadolg = "REGISTRO BORRADO";                 
                $resultado ['estado'] = "REGISTRO BORRADO";
                sleep(1);
                //echo json_encode($resultado);
            }else{
                $resultado ['estado'] = "REGISTRO NO BORRADO";
                sleep(1);
                //echo json_encode($resultado);
            }
        }
        $query ="SELECT USU_RUT, USU_NOM, USU_APP, USU_APM FROM USUARIO WHERE (USU_RUT ='".$rut."')";
        $rs = mysqli_query($cnn, $query);
        if (mysqli_num_rows($rs) != 0){
        	$row = mysqli_fetch_row($rs);
            if ($row[0] == $rut){
            	$Nombre = utf8_encode($row[1]);
                $ApellidoP = utf8_encode($row[2]);
                $ApellidoM = utf8_encode($row[3]);
            }
        }	
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 24;
        $ipcliente = getRealIP();
        $accionRealizada = utf8_decode($resultadolg. " ACTIVIDAD DE CAPACITACIÓN : ".$nombreact."  ".$Nombre." ".$ApellidoP." ".$ApellidoM);
        $insertAccion = "INSERT INTO CARRERA_LOG_ACCION (CA_LA_ACC, FOR_ID, USU_RUT, CA_LA_IP_USU, CA_LA_FEC, CA_LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
        mysqli_query($cnn, $insertAccion);
       
        sleep(1);
        echo json_encode($resultado);
    }
?> 