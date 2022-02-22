<?php
date_default_timezone_set("America/Santiago");
$fecha = date("Y-m-d");
$fecha15 = date("2021-09-15");
require '../include/PHPExcel/Classes/PHPExcel/IOFactory.php';  
include ("../include/funciones/funciones.php");
$cnn = ConectarPersonal();
// Funciones
function AnosServicio($rut,$inicio){
    $MuestroFechaInicio = $inicio;
    $MuestroRut = $rut;
    $consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$MuestroRut."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
    $resputbie = mysqli_query($cnn, $consultabie);
    while ($row_rs3 = mysqli_fetch_array($resputbie)){    
      if($row_rs3[2] == 1){
        if($row_rs3[0] >= $final2){
          $date1=date_create($final2);
          $date2=date_create($row_rs3[0]);
          $diff=date_diff($date1,$date2);
          $diasno = $diasno + $diff->format('%R%a');
          if($diasno <= 1){
            $diasno = 0;
          }
          $final2= $row_rs3[1];
          if($final2 <= $row_rs3[1]){
            $final2= $row_rs3[1];      
          } 
        }else{
          if($final2 <= $row_rs3[1]){
            $final2 = $row_rs3[1];
          }
        }
        $date1=date_create($row_rs3[0]);
        $date2=date_create($fecha);
        $diff=date_diff($date1,$date2);
        $cuentabienios = $cuentabienios + $diff->format('%Y'); 
        $final2 = $fecha;
        break 1;
      }else{
        if($row_rs3[0] == $inicial){
          $inicial2 = $row_rs3[0];
          $final2 = $row_rs3[1];      
        }else{
          if($row_rs3[0] >= $final2){
            $date1=date_create($final2);
            $date2=date_create($row_rs3[0]);
            $diff=date_diff($date1,$date2);
            $diff2=$diff->format('%R%a');   
            if($diff2 ==1){
              $diff2=0;                                 
            }else{
              $diff2=$diff2-1;
            }
            $diasno = $diasno + $diff2;
            if($diasno <= 1){
              $diasno = 0;
            }                      
            $final2= $row_rs3[1];
            if($final2 <= $row_rs3[1]){
              $final2= $row_rs3[1];    
            }
          }else{
            if($final2 <= $row_rs3[1]){
              $final2 = $row_rs3[1];             
            }
          }                                            
        }                 
      }
    }
    if($final2 > $fecha){
      $final2 = $fecha;
    }
    if($diasno > 0){
      $nuevainicial = date_create($MuestroFechaInicio);
      date_add($nuevainicial, date_interval_create_from_date_string("$diasno days"));
      date_format($nuevainicial, 'Y-m-d');
      $nuevainicial2 = date_format($nuevainicial, 'Y-m-d');
      $date2=date_create($final2);
      $interval=date_diff($nuevainicial,$date2);
      $cuentabienios = $interval->format('%Y');                                        
    }else{                                        
      $date1=date_create($MuestroFechaInicio);
      $date2=date_create($final2);      
      $interval=date_diff($date1,$date2);   
      $cuentabienios =  $interval->format('%Y'); 
    } 
    if($nuevainicial2==""){
      $nuevainicial2 = $MuestroFechaInicio;
    }
    while ($nuevainicial2 <= $fecha){
      $nuevainicial3 = date_create($nuevainicial2);
      date_add($nuevainicial3, date_interval_create_from_date_string('2 years'));
      date_format($nuevainicial3, 'Y-m-d');
      $nuevainicial2 =  date_format($nuevainicial3, 'Y-m-d');
    }  
    if($cuentabienios%2==0){ 
      $valido_bie = $cuentabienios * 1;
    }else{
      $valido_bie= $cuentabienios - 1;
    }
    $valido_bie = $valido_bie / 2;
    return array($cuentabienios, $valido_bie);
}
function Antiguedad($rut, $ingreso){
  $MuestroFechaInicio = $ingreso;
  $MuestroRut = $rut;
  $consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$MuestroRut."') AND (CB_ESTADO = '1')  AND (CB_ESTABLE = 'DEPARTAMENTO DE SALUD I.MUNICIPALIDAD DE RENGO')  ORDER BY CB_FEC_INI";
  $resputbie = mysqli_query($cnn, $consultabie);
  while ($row_rs3 = mysqli_fetch_array($resputbie)){    
    if($row_rs3[2] == 1){
      if($row_rs3[0] >= $final2){
        $date1=date_create($final2);
        $date2=date_create($row_rs3[0]);
        $diff=date_diff($date1,$date2);
        $diasno = $diasno + $diff->format('%R%a');
        if($diasno <= 1){
          $diasno = 0;
        }
        $final2= $row_rs3[1];
        if($final2 <= $row_rs3[1]){
          $final2= $row_rs3[1];      
        } 
      }else{
        if($final2 <= $row_rs3[1]){
          $final2 = $row_rs3[1];
        }
      }
      $date1=date_create($row_rs3[0]);
      $date2=date_create($fecha15);
      $diff=date_diff($date1,$date2);
      $cuentabienios = $cuentabienios + $diff->format('%Y'); 
      $final2 = $fecha15;
      break 1;
    }else{
      if($row_rs3[0] == $inicial){
        $inicial2 = $row_rs3[0];
        $final2 = $row_rs3[1];      
      }else{
        if($row_rs3[0] >= $final2){
          $date1=date_create($final2);
          $date2=date_create($row_rs3[0]);
          $diff=date_diff($date1,$date2);
          $diff2=$diff->format('%R%a');   
          if($diff2 ==1){
            $diff2=0;                                 
          }else{
            $diff2=$diff2-1;
          }
          $diasno = $diasno + $diff2;
          if($diasno <= 1){
            $diasno = 0;
          }                      
          $final2= $row_rs3[1];
          if($final2 <= $row_rs3[1]){
            $final2= $row_rs3[1];    
          }
        }else{
          if($final2 <= $row_rs3[1]){
            $final2 = $row_rs3[1];             
          }
        }                                            
      }                 
    }
  }
  if($final2 > $fecha15){
    $final2 = $fecha15;
  }
  if($diasno > 0){
    $nuevainicial = date_create($MuestroFechaInicio);
    date_add($nuevainicial, date_interval_create_from_date_string("$diasno days"));
    date_format($nuevainicial, 'Y-m-d');
    $nuevainicial2 = date_format($nuevainicial, 'Y-m-d');
    $date2=date_create($final2);
    $interval=date_diff($nuevainicial,$date2);
    $cuentabienios = $interval->format('%Y');                                        
  }else{                                        
    $date1=date_create($MuestroFechaInicio);
    $date2=date_create($final2);      
    $interval=date_diff($date1,$date2);   
    $cuentabienios =  $interval->format('%Y'); 
  } 
  if($nuevainicial2==""){
    $nuevainicial2 = $MuestroFechaInicio;
  }
  while ($nuevainicial2 <= $fecha15){
    $nuevainicial3 = date_create($nuevainicial2);
    date_add($nuevainicial3, date_interval_create_from_date_string('2 years'));
    date_format($nuevainicial3, 'Y-m-d');
    $nuevainicial2 =  date_format($nuevainicial3, 'Y-m-d');
  }  
  if($cuentabienios%2==0){ 
    $valido_bie = $cuentabienios * 1;
  }else{
    $valido_bie= $cuentabienios - 1;
  }
  return $cuentabienios;
}
//cargar excel
$directorio = './listado.xlsx';
$sinpermi = $directorio;
chmod($sinpermi, 0777);
if (is_readable($sinpermi)) {
  $objPHPExcel = PHPExcel_IOFactory::load($sinpermi);
  $objPHPExcel->setActiveSheetIndex(0);
  $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
}
for ($x = 2; $x <= $numRows; $x++) {
    $Rut = $objPHPExcel->getActiveSheet()->getCell('A'.$x)->getCalculatedValue();
    //recupero fecha inicio salud municipal
    $query = "SELECT USU_FEC_ING,USU_FEC_INI FROM USUARIO WHERE USU_RUT = '$Rut'";
    $respuesta = mysqli_query($cnn,$query);
    $row = mysqli_fetch_row($respuesta);
    $usu_fec_ing = $row[0];
    $usu_fec_ini = $row[1];
    // Calcular antiguedad hasta el 15 de septiembre
    $antiguedad = Antiguedad($Rut,$usu_fec_ing);
    // Años de Servicio y Bienios
    $arrAnosServicioBienios = AnosServicio($Rut,$usu_fec_ini);
    //Muestro informacion en pantalla
    echo $Rut.';'.$usu_fec_ing.';'.$antiguedad.';'.$arrAnosServicioBienios[0].';'.$arrAnosServicioBienios[1];
    echo '<br>';
}
?>