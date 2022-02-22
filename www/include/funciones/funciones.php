<?php
function ConectarPersonal() {
  //datos del servidor
	// $servername = "localhost";
	$servername = "172.10.10.2";
	$usuario = "root";
	$password = "CKXjhu84qe7";
	$dbname = "PERSONAL";
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
function ConectarAbastecimiento() {
  //datos del servidor
	/*$servername = "localhost";
	$usuario = "root";
	$password = "CKXjhu84qe7";
	$dbname = "ABASTECIMIENTO";
	//crear conexion
	$conn = new mysqli ($servername, $usuario, $password, $dbname);
	//imprimir error
		if($conn->connect_error){
			die("Error en la conexion : ".$conn -> connect_errno.
									 "-".$conn -> connect_error);
		}
	//si todo esta bien retornamos la conexion
	return $conn;*/
	
	//PDO
	$dsn = 'mysql:host=localhost;dbname=ABASTECIMIENTO';
  $us = 'root';
  $ps = 'CKXjhu84qe7';
  try {
    $conn = new PDO($dsn, $us, $ps);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo 'Falló la conexión: ' . $e->getMessage();
  }
  return $conn;
	
}
?>
