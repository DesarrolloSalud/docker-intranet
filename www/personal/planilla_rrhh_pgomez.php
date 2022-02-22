<?php
date_default_timezone_set("America/Santiago");
$fecha = date("Y-m-d");
$fecha15 = date("2021-09-15");
require '../include/PHPExcel/Classes/PHPExcel/IOFactory.php';  
include ("../include/funciones/funciones.php");
$cnn = ConectarPersonal();
//cargar excel
$directorio = './planilla.xlsx';
$sinpermi = $directorio;
chmod($sinpermi, 0777);
if (is_readable($sinpermi)) {
  $objPHPExcel = PHPExcel_IOFactory::load($sinpermi);
  $objPHPExcel->setActiveSheetIndex(0);
  $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
}
for ($x = 1; $x <= $numRows; $x++) {
    $Rut = $objPHPExcel->getActiveSheet()->getCell('A'.$x)->getCalculatedValue();
    //recupero fecha inicio salud municipal
    $query = "SELECT USU_FEC_NAC FROM USUARIO WHERE USU_RUT = '$Rut'";
    $respuesta = mysqli_query($cnn,$query);
    $row = mysqli_fetch_row($respuesta);
    $usu_fec_nac = $row[0];
    // $usu_fec_ini = $row[1];
    // Calcular antiguedad hasta el 15 de septiembre
    // $antiguedad = Antiguedad($Rut,$usu_fec_ing);
    // Años de Servicio y Bienios
    // $arrAnosServicioBienios = AnosServicio($Rut,$usu_fec_ini);
    //Muestro informacion en pantalla
    echo $Rut.';'.$usu_fec_nac;
    echo '<br>';
}
?>