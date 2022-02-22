<?php
	include ("../../include/funciones/funciones.php");
	$cnn = ConectarPersonal();
	$id = $_POST['id_est'];
	$buscar = "SELECT EST_ID,EST_NOM,EST_ESTA,EST_DIR FROM ESTABLECIMIENTO WHERE (EST_ID
	 = $id)";
	$rs = mysqli_query($cnn, $buscar);
	if (mysqli_num_rows($rs) != 0){
		//RESCATO CAMPOS DESDE LA BD 
		if($row = mysqli_fetch_array($rs)){
			$Muestroid=$row[0];
			$MuestroNombre=$row[1];
			$MuestroEstado=$row[2];
			$MuestroDireccion=$row[3];
		}
		//ENVIO LOS DATOS RESCATADOS DESDE LA BD PARA EDITAR REGISTRO
		$resultado ['id_establecimientos'] = $Muestroid;
		$resultado ['nombre_establecimientos'] = utf8_encode($MuestroNombre);
		$resultado ['estado_establecimientos'] = $MuestroEstado;
		$resultado ['direccion_establecimiento'] = utf8_encode($MuestroDireccion);
		sleep(1);
		echo json_encode($resultado);	
	}//Fin if 
?>