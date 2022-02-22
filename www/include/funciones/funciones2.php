<?php
function ConectarEncuestas() {

  //datos del servidor
	$servername = "172.10.10.2";
	$usuario = "root";
	$password = "CKXjhu84qe7";
	$dbname = "ENCUESTAS";
	//crear conexion
	$conn = new mysqli ($servername, $usuario, $password, $dbname);
	//imprimir error
		if($conn->connect_error){
			die("Error en la conexion : ".$conn -> connect_errno.
									 "-".$conn -> connect_error);
		}
	//si todo esta bien retornamos la conexion
	return $conn;
  
}
?>
