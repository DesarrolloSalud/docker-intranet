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
  $id = $_GET['folio'];
  $doc = $_GET['doc'];
  include ("../include/funciones/funciones.php");
  $cnn = ConectarPersonal();
  $directorio = '../include/convocatoria';
  $sinpermi = $directorio."/".$doc."-".$id.".pdf";
  chmod($sinpermi, 0755);
  if (is_readable($sinpermi)) {
    //chmod($sinpermi, 0755);                                
    //echo '<script type="text/javascript"> window.open("'.$sinpermi.'","_self")</script>'; 
    //sleep(1);
    //chmod($sinpermi, 0000);  
    ?><script> window.close();</script><?php
  }
  ?><script> window.close();</script><?php
}
?>