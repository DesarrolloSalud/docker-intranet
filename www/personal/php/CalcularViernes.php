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
        $fecha = $_POST['fecha_dia'];
        $cantidadActual = $_POST['horas_actuales'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        if (date('w',strtotime($fecha)) == 5){ //ver si es viernes
        	if($cantidadActual == 8){
        		//puede tomarse el viernes
        		$resultado ['horas'] = "si";
        		$resultado ['dia'] = "si";
	            sleep(1);
	            echo json_encode($resultado);
        	}elseif ($cantidadActual < 8) {
        		//solo puede pedir horas
        		$resultado ['horas'] = "si";
        		$resultado ['dia'] = "no";
	            sleep(1);
	            echo json_encode($resultado);
        	}elseif ($cantidadActual > 8) {
        		//puede pedir horas y dia
        		$resultado ['horas'] = "si";
        		$resultado ['dia'] = "si";
	            sleep(1);
	            echo json_encode($resultado);
        	}
        }else{
        	if($cantidadActual < 9){
        		//solo puede horas
        		$resultado ['horas'] = "si";
        		$resultado ['dia'] = "no";
	            sleep(1);
	            echo json_encode($resultado);
        	}elseif ($cantidadActual >= 9){
        		//puede pedir dia y horas
        		//puede pedir horas y dia
        		$resultado ['horas'] = "si";
        		$resultado ['dia'] = "si";
	            sleep(1);
	            echo json_encode($resultado);
        	}
        }
    }
?>