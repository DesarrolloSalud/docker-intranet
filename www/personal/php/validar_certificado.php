<?php 
    session_start();
    if(!isset($_SESSION['USU_RUT'])){
        session_destroy();
        header("location: ../../index.php");
    }else{
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Sest_id = $_SESSION['EST_ID']; 
        $Sprof = utf8_encode($_SESSION['USU_PROF']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $folio = $_POST['id'];
        $tipo = $_POST['tipo'];
	    //rescato valor si se subio archivo
	    $formulario = "SELECT FO_ID,FO_ADJUNTO FROM FOR_OTROS WHERE (FO_ID = $folio) and (FO_TIPO = $tipo)";
	    $resFormulario = mysqli_query($cnn, $formulario);
	    if (mysqli_num_rows($resFormulario) != 0){
	    	$rowF = mysqli_fetch_row($resFormulario);
	        if ($rowF[0] == $folio){
	        	$Estado = $rowF[1];

	        }
	    }else{
        $Estado = "NO";
      }
      $resultado ['Mensaje'] = $Estado;
      sleep(1);
      echo json_encode($resultado);
    }
?>