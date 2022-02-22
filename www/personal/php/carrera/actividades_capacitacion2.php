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
        $idact  = $_POST['idact2'];
        $fechaacum = $_POST['fechaacum2'];
        if($estado == 1){ 

            $modificaact = "UPDATE CARRERA_ACT SET CA_DES ='$nombreact', CA_FEC = '$fechaact', CA_HORA = '$horaact', CA_HORA_PUN = '$horapun', CA_NIVEL = '$nivelact', CA_NIVEL_PUN = '$nivelpun', CA_NOTA = '$nota', CA_NOTA_PUN = '$notapun', CA_FEC_ING = '$fechaing', CA_TOTAL = '$total', CA_ESTADO = '$estadoact', CA_FEC_ACU ='$fechaacum' WHERE (CA_ID = '$idact')";
            mysqli_query($cnn, $modificaact);  
            $resultadolg = "REGISTRO ACTUALIZADO";
            $resultado ['estado'] = "REGISTRO ACTUALIZADO";                
            sleep(1);

       }
      if($estado == 2){
        $agregaract = "INSERT INTO CARRERA_ACT (USU_RUT, CA_DES, CA_FEC, CA_HORA, CA_HORA_PUN, CA_NIVEL, CA_NIVEL_PUN, CA_NOTA, CA_NOTA_PUN, CA_FEC_ING, CA_TOTAL,CA_ESTADO,CA_FEC_ACU) VALUES ('$rut', '$nombreact', '$fechaact', '$horaact', '$horapun', '$nivelact', '$nivelpun', '$nota', '$notapun', '$fechaing', '$total','$estadoact','$fechaacum')";
                mysqli_query($cnn, $agregaract);  
                $resultadolg = "REGISTRO GUARDADO";
                $resultado ['estado'] = "REGISTRO GUARDADO";                
                sleep(1);
        
       }
      if($estado == 3){
        $queryd ="SELECT CA_DES FROM CARRERA_ACT WHERE (CA_ID ='$idact')";
        $rs = mysqli_query($cnn, $queryd);
        if (mysqli_num_rows($rs) != 0){
            $row = mysqli_fetch_row($rs);
            if ($row[0] != ""){
                $nombreact = utf8_encode($row[0]);                
            }
        }   
                $BorrarDetalle = "DELETE FROM CARRERA_ACT WHERE (CA_ID ='$idact') ";
                mysqli_query($cnn, $BorrarDetalle);
                $resultadolg = "REGISTRO BORRADO";                 
                $resultado ['estado'] = "REGISTRO BORRADO";
                $directorio = '../../../include/certificado_capacitacion';
                $directorio = $directorio."/".$idact.".pdf"; 
                unlink($directorio);//elimina el archivo del directorio
                sleep(1);
                echo json_encode($resultado);           
        }
      if ($estado == 4){
        $querya ="SELECT CA_DES FROM CARRERA_ACT WHERE (CA_ID ='$idact')";
        $rs = mysqli_query($cnn, $querya);
        if (mysqli_num_rows($rs) != 0){
            $row = mysqli_fetch_row($rs);
            if ($row[0] != ""){
                $nombreact = utf8_encode($row[0]);                
            }
        }   
        $estadoact = "Activo";
        $activaact = "UPDATE CARRERA_ACT SET CA_ESTADO = '$estadoact' WHERE (CA_ID = '$idact')";
            mysqli_query($cnn, $activaact);  
            $resultadolg = "REGISTRO ACTIVADO";
            $resultado ['estado'] = "REGISTRO ACTIVADO";                
            sleep(1);
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
        $id_formulario = 25;
        $ipcliente = getRealIP();
        $accionRealizada = utf8_decode($resultadolg. " ACTIVIDAD DE CAPACITACIÓN ID:".$idact." ".$nombreact." ".$Nombre." ".$ApellidoP." ".$ApellidoM);
        $insertAccion = "INSERT INTO CARRERA_LOG_ACCION (CA_LA_ACC, FOR_ID, USU_RUT, CA_LA_IP_USU, CA_LA_FEC, CA_LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
        mysqli_query($cnn, $insertAccion);
       
        sleep(1);
        echo json_encode($resultado);
    }
?> 