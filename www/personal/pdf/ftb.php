<?php
session_start();
if(!isset($_SESSION['USU_RUT'])){
  session_destroy();
  header("location: ../index.php");
}else{
  if(count($_GET) && !$_SERVER['HTTP_REFERER']){
     header("location: error.php");
  }
  $Srut = utf8_encode($_SESSION['USU_RUT']);
  date_default_timezone_set("America/Santiago");
  $idrut = $_GET['id'];
 
  include ("../../include/funciones/funciones.php");
  $cnn = ConectarPersonal();
  $firma= '../../include/img/firmas/'.$idrut.'.png';
  $timbre= '../../include/img/firmas/timbre_dir.png';  
  if (is_readable($firma)) {
    sleep(2);
    chmod($firma, 0000);
    chmod($timbre, 0000);
   /*?><script> window.close();</script><?php*/
  }
}
?>