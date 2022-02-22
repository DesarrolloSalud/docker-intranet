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
		include ("../../../include/funciones/funciones.php");
		$cnn = ConectarPersonal();
		$nd = $_POST['num_dec'];
		$ano= $_POST['ano'];

		//$ano = YEAR($ano);
	    $query = "SELECT DA_ID,DA_DC_NUM, DA_FEC FROM DECRE_ACT WHERE (DA_DC_NUM = '$nd') AND YEAR(DA_FEC) ='$ano'";   
	    //echo $query="SELECT DA_ID,DA_DC_NUM, DA_FEC FROM DECRE_ACT WHERE (DA_DC_NUM = '108') AND YEAR(DA_FEC) ='2019'";
	    $rs = mysqli_query($cnn, $query);
	    if (mysqli_num_rows($rs) == 0){
	    	//decir que si existen registros
	    	//$resultado ['num_dec'] = $nd;
			$resultado ['resultado'] = 0;
			sleep(1);
			echo json_encode($resultado);
	    }else{
	    	//decir que no existen registros con ese rut
	    	//$resultado ['num_enviado'] = $nd;
			$resultado ['resultado'] = 1;
			sleep(1);
			echo json_encode($resultado);
	    }
	}
?>

