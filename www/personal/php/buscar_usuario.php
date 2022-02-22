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
        if(count($_GET) && !$_SERVER['HTTP_REFERER']){
           header("location: ../error.php");
        }
		include ("../../include/funciones/funciones.php");
		$cnn = ConectarPersonal();
		$rut = $_POST['rut_nuevo'];
		$buscar = "SELECT USU_RUT FROM USUARIO WHERE (USU_RUT = '".$rut."')";
	    $rs = mysqli_query($cnn, $buscar);
	    if (mysqli_num_rows($rs) != 0){
	    	//decir que si existen registros
	    	$resultado ['rut_enviado'] = $rut;
			$resultado ['resultado'] = 1;
			sleep(1);
			echo json_encode($resultado);
	    }else{
	    	//decir que no existen registros con ese rut
	    	$resultado ['rut_enviado'] = $rut;
			$resultado ['resultado'] = 0;
			sleep(1);
			echo json_encode($resultado);
	    }
	}
?>