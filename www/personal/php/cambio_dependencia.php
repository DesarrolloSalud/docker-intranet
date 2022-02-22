<?php
  session_start(); 
  $dep = $_POST['nombre'];
  $_SESSION['USU_DEP']		= $dep; 
  $resultado ['resultado'] = "OK";
  sleep(1);
  echo json_encode($resultado);
?>