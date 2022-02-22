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
  require_once("../../include/funciones/conectar.php");
  $cnn = ConectarPersonal();
  $hoy = date("Y-m-d");
  $request = 0;

  if(isset($_POST['request'])){
     $request = $_POST['request'];
  }
  if($request == 1){
    /*$rut = $_POST['rut'];
    $asoc = $_POST['asoc'];    
    $queryDir = "SELECT distinct(USUARIO.USU_RUT),USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,GREMI_DIR.GD_CARGO FROM USUARIO INNER JOIN GREMI_DIR ON USUARIO.USU_RUT=GREMI_DIR.USU_RUT WHERE (GREMI_DIR.GD_ESTA = 'ACTIVO' AND GREMI_DIR.USU_RUT <> '$rut' AND GD_FEC_FIN >= '$hoy' AND GG_ASOCIACION = $asoc)";
    $resultadoD =mysqli_query($cnn, $queryDir);
    $statesList = $resultadoD->fetchAll();
    $response = array();
    foreach($statesList as $state){
      $response[] = array(
        "rut" => $state['usu_rut'],
        "nom" => $state['usu_nom'],
        "app" => $state['usu_app'],
        "apm" => $state['usu_apm']
      );
        }  
        echo json_encode($response);
        exit;*/
  }
}
?>


 