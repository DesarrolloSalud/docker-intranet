<?php
  session_start(); 
	if(!isset($_SESSION['USU_RUT'])){
		session_destroy();
		header("location: ../index.php");
	}else{
    $_SESSION['ACTUALIZACIONES']		= "NO"; 
    $resultado ['resultado'] = "OK";
    sleep(1);
    echo json_encode($resultado);
  }
?>