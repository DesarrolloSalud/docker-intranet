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
    function obtenerFechaEnLetra($fecha){
        $dia= conocerDiaSemanaFecha($fecha);
        $num = date("j", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
        return /*$dia.', '.*/$num.' de '.$mes.' del '.$anno;
    }
    function conocerDiaSemanaFecha($fecha) {
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        $dia = $dias[date('w', strtotime($fecha))];
        return $dia;
    }

    session_start();
    if(!isset($_SESSION['USU_RUT'])){
        session_destroy();
        header("location: ../../../index.php");
    }else{
        if(count($_GET) && !$_SERVER['HTTP_REFERER']){
           header("location: ../error.php");
        }
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Snombre = utf8_encode($_SESSION['USU_NOM']);
        $SapellidoP = utf8_encode($_SESSION['USU_APP']);
        $SapellidoM = utf8_encode($_SESSION['USU_APM']);
        $Semail = utf8_encode($_SESSION['USU_MAIL']);
        $Scargo = utf8_encode($_SESSION['USU_CAR']);
        $Sestablecimiento = utf8_encode($_SESSION['EST_ID']);
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $ano5 = date("Y");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $rut1 = $_GET['rut'];
        $iddcre = $_GET['id'];
        $secretaria = "GERALDINE MONTOYA MEDINA";
        $alcalde = "CARLOS SOTO GONZALEZ";
        $responsables = "FLA/PVG/PGC/MPP/mpp";
        include ("../../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $buscar = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_CAR,USUARIO.USU_DEP,USUARIO.USU_ESTA,USUARIO.USU_CAT,USUARIO.USU_NIV,USUARIO.USU_PROF,
        USUARIO.USU_FEC_BIE,USUARIO.USU_FEC_INI FROM USUARIO INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (USUARIO.USU_RUT = '".$rut1."')";
        $rs = mysqli_query($cnn, $buscar);
        if($row = mysqli_fetch_array($rs)){
            $MuestroRut=$row[0];
            $MuestroNombre=utf8_encode($row[1]);
            $MuestroApellidoP = utf8_encode($row[2]);
            $MuestroApellidoM = utf8_encode($row[3]);            
            $MuestroCargo = utf8_encode($row[4]);
            $MuestroDependencia = utf8_encode($row[5]);
            $MuestroEstado = $row[6]; 
            $MuestroCategoria = $row[7];
            $MuestroNivel = $row[8];
            $MuestroProf = $row[9];
            $MuestroFecBie = $row[10];
            $MuestroFechaInicio = $row[11];

        }else{
          $rut1="";
        }
         $busca_deta = "SELECT DB_ID,DB_DC_NUM,DATE_FORMAT(DB_FEC,'%Y-%m-%d'),DB_VISTO,DB_CONSI,DB_DEC,DB_ALCALDE,DB_SECRE,DB_DERIVA,DB_FEC_CONT,DB_ACT_TOTAL,DB_BIE_PUN,DB_ALCSUB,DB_SECSUB,DB_GENALC,DB_GENSEC  FROM DECRE_BIE WHERE (DB_ID = '".$iddcre."')";    
         $respuesta = mysqli_query($cnn, $busca_deta);
            if($row1 = mysqli_fetch_array($respuesta)){
                $MuestroNum   = $row1[1];
                $MuestroFec   = $row1[2];
                $MuestroVisto = $row1[3];
                $MuestroVisto = str_replace("<br />", " ", $MuestroVisto);
                $MuestroConsi = $row1[4];
                $MuestroConsi = str_replace("<br />", " ", $MuestroConsi);
                $MuestroDec   = $row1[5];
                $MuestroDec = str_replace("<br />"," ", $MuestroDec);
                //$MuestroAlca  = utf8_encode($row1[6]);
                $alcalde  = utf8_encode($row1[6]);
                //$MuestroSecre = utf8_encode($row1[7]);
                $secretaria = utf8_encode($row1[7]);
                //$MuestroDeri  = utf8_encode($row1[8]);
                $responsables = utf8_encode($row1[8]);
                $FecCont = $row1[9];
                $acumu_pun = $row1[10];
                $bienios_ptos =$row1[11];
                
                //VERIFICA NIVEL
                $total_puntaje = $bienios_ptos + $acumu_pun;
                round($total_puntaje, 2);
                if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
                    $buscar_criti ="SELECT CPC_AB_INI,CPC_AB_FIN,CPC_NIVEL FROM CARRERA_PTOS_CRITI";
                    $resputcriti = mysqli_query($cnn, $buscar_criti);
                    while ($row_rs4 = mysqli_fetch_array($resputcriti)){
                    //$total_puntaje = 4167.75;
                        if($row_rs4[0] <= $total_puntaje){
                            if($total_puntaje <= $row_rs4[1]){                                                   
                                $nivel_actual = $row_rs4[2];
                                break 1;
                            }

                        }                                
                    }

                }else{
                    $buscar_criti ="SELECT CPC_CF_INI,CPC_CF_FIN,CPC_NIVEL FROM CARRERA_PTOS_CRITI";
                    $resputcriti = mysqli_query($cnn, $buscar_criti);
                    while ($row_rs4 = mysqli_fetch_array($resputcriti)){
                    //$total_puntaje = 4167.75;
                        if($row_rs4[0] <= $total_puntaje){
                            if($total_puntaje <= $row_rs4[1]){                                                   
                                $nivel_actual = $row_rs4[2];
                                break 1;
                            }

                        }                                
                    }
                }
              
                $alcsub= $row1[12];
                $secsub = $row1[13];
                if($alcsub=='(S)'){
                   $alcsub='checked';
               }
                if($secsub =='(S)'){
                    $secsub='checked';                             
                }
                $genalc = $row1[14];
                if($genalc ==""){
                  $genalc="ALCALDE";
                }
                $gensec = $row1[15];
                if($gensec==""){
                  $gensec="SECRETARIA";
                }
            }else{
				$acumu_pun = 0;
$acumu_pun1 = 0;
$sal_acu =0;
//$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC <='2021-08-31') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
$respuesta2 = mysqli_query($cnn, $query2);
$row = $respuesta2->fetch_array(MYSQLI_NUM);
$iniacu=0;
for ($i = $row[1]; $i <= $ano5; $i++) {  
  //echo $i."   antes";
    //print "<p>$i</p>\n"; //solo para ver lista de años
  if($iniacu==0){
    $valano = $row[1];
    $iniacu=1;
  }
  if(isset($i)==''){
    $i=0;
  } 
  $query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (YEAR(CA_FEC)=$i) AND (CA_FEC <='2021-08-31') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
  $respuesta3 = mysqli_query($cnn, $query2);
  $row1 = $respuesta3->fetch_array(MYSQLI_NUM);
  //printf ("%s (%s)\n", $row1[1], $row1[2]);
  //$valano = $i; 
  $year = $row1[1];
  $puntaje = $row1[2];  
    if($year !='' and $puntaje !=''){
      if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
        //$valano = $row_rs2[1];
         $acumu_pun1 = $row1[2] + $sal_acu;
         if($acumu_pun1 < 151){   
          $acumu_pun = ($acumu_pun + $acumu_pun1);
          $sal_acu=0;
        }else{
          $acumu_pun = $acumu_pun + 150;
          $sal_acu = ($acumu_pun1 - 150);
        }       
      }else{  
         $acumu_pun1 = $row1[2] + $sal_acu;
        if($acumu_pun1 < 118){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;      
        }else{
          $acumu_pun = $acumu_pun + 117;
          $sal_acu = ($acumu_pun1 - 117);
        } 
      } 
      //printf ("%s (%s)\n", $i,$acumu_pun);
    }else{
      $puntaje =0;   
      if($MuestroCategoria == "A" || $MuestroCategoria == "B"){      
        $acumu_pun1 = $puntaje + $sal_acu;
        if($acumu_pun1 < 151){   
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;
        }else{
          $acumu_pun = $acumu_pun + 150;
          $sal_acu = ($acumu_pun1 - 150);
        } 
      }else{
        $acumu_pun1 = $puntaje + $sal_acu;
        if($acumu_pun1 < 118){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;      
        }else{
          $acumu_pun = $acumu_pun + 117;
          $sal_acu = ($acumu_pun1 - 117);
        }
      }
       //printf ("%s (%s)\n", $i,$sal_acu);
    }
    
}
$acumu_pun31= $acumu_pun;
//$sal_acu31 = $sal_acu;
/*############ HASTA EL 31 DE AGOSTO DE 2021 ##################*/

/*########### DESDE EL 01 DE SEPTIEMBRE 2021 ##################*/
$acumu_pun = 0;
//$acumu_pun1 = 0;
//$sal_acu =0;
$acumu_pun01=0;
$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC_ING),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC_ING >='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
$respuesta21 = mysqli_query($cnn, $query2);
$row21 = $respuesta21->fetch_array(MYSQLI_NUM);
//printf ("%s (%s)\n", $row21[0], $row21[1]);
//$i= $row21[1];

if($row21 != ''){
  for ($i1 = $row21[1]; $i1 <= $ano5; $i1++) {
    //echo $i1. "   Después";
    //print "<p>$i1</p>\n"; //solo para ver lista de años
    $query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC_ING),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (YEAR(CA_FEC_ING)=$i1) AND (CA_FEC_ING >='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
    $respuesta31 = mysqli_query($cnn, $query2);
    $row211 = $respuesta31->fetch_array(MYSQLI_NUM);
    //printf ("%s (%s)\n", $row211[1], $row211[2]);
    //$valano = $i1;
    $year = $row211[1];
    $puntaje = $row211[2];
    if($year>2022){    
    if($year !='' and $puntaje !=''){
      if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
        //$valano = $row_rs2[1];
        $acumu_pun1 = $row211[2] + $sal_acu;
        if($acumu_pun1 < 151){   
          $acumu_pun = ($acumu_pun + $acumu_pun1);
          $sal_acu=0;
        }else{
          $acumu_pun = $acumu_pun + 150;
          $sal_acu = ($acumu_pun1 - 150);
        }       
      }else{  
        $acumu_pun1 = $row211[2] + $sal_acu;
        if($acumu_pun1 < 118){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;   
        }else{
          $acumu_pun = $acumu_pun + 117;
          $sal_acu = ($acumu_pun1 - 117);
        } 
      } 
      //printf ("%s (%s)\n", $i,$acumu_pun);
    }else{
      $puntaje =0;
      if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
        $acumu_pun1 = $puntaje + $sal_acu;
        if($acumu_pun1 < 151){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;
        }else{
          $acumu_pun = $acumu_pun + 150;
          $sal_acu = ($acumu_pun1 - 150);
        } 
      }else{
        $acumu_pun1 = $puntaje + $sal_acu;
        if($acumu_pun1 < 118){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;      
        }else{
          $acumu_pun = $acumu_pun + 117;
          $sal_acu = ($acumu_pun1 - 117);
        }
      }
      //printf ("%s (%s)\n", $i,$sal_acu);
    }
  }
  }  
  
}else{
  $acumu_pun=0;
}
$acumu_pun01 = $acumu_pun;
//$sal_acu01 = $sal_acu;

/*########## FIN DESDE EL 01 DE SEPTIEMBRE 2021 ####################*/
$acumu_pun=0;
//$sal_acu=0;

/*#### INICIO SUMA DE PUNTAJES POR CAPACITACIÓN CON AMBOS CÁLCULOS #####*/
$acumu_pun = $acumu_pun31 + $acumu_pun01;
//$sal_acu = $sal_acu01+$sal_acu31;
//$acumu_pun = $acumu_pun - $sal_acu;
//echo $sal_acu;
/*#### FIN SUMA DE PUNTAJES POR CAPACITACIÓN CON AMBOS CÁLCULOS #####*/ 

  /*while ($row_rs2 = mysqli_fetch_array($respuesta2)){                                            
   $valano = $row_rs2[1];
    if($valini==0){
      echo $valini= $valano;
    }
      if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
          //$valano = $row_rs2[1];
          $acumu_pun1 = $row_rs2[2] + $sal_acu;
          if($acumu_pun1 < 151){                                            
              $acumu_pun = $acumu_pun + $acumu_pun1;
              $sal_acu=0;
          }else{
              $acumu_pun = $acumu_pun + 150;
              $sal_acu = ($acumu_pun1 - 150);
          }                                                
      }else{                                                
          $acumu_pun1 = $row_rs2[2] + $sal_acu;
          if($acumu_pun1 < 118){                                            
              $acumu_pun = $acumu_pun + $acumu_pun1;
              $sal_acu=0;                                                    
          }else{
              $acumu_pun = $acumu_pun + 117;
              $sal_acu = ($acumu_pun1 - 117);
          }                                                
      }                                                                                     
  } */                                     
// echo $conta_pun ."   ". $acumu_pun;
 $saldo = number_format($conta_pun - $acumu_pun,2,'.', '');
//$valano = $valano + 1;
 

 //echo $valano. "valid". "  ". $ano5."Año5";
 /*while($valano <= $ano5){
  $valano = $valano + 1;
  if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
    if($saldo >= 150){
      $acumu_pun= $acumu_pun +150;
      $saldo = $saldo -150;
    }else{
      $acumu_pun = $acumu_pun + $saldo;
      $saldo=0;
    }
  }else{
    if($saldo >= 117){
      $acumu_pun= $acumu_pun + 117;
      $saldo = $saldo -117;
    }else{
      $acumu_pun = $acumu_pun + $saldo;
      $saldo=0;
    }
  }
}

  if($saldo<0){
    $saldo=0;
  }
  if($rut1 ='11.277.235-9'){
    //echo $saldo. "  Saldo";
    //echo $acumu_pun."  Acumulado";
  }
if($MuestroCategoria == "A" || $MuestroCategoria == "B"){                          
if($acumu_pun > 4500){
  $acumu_pun= 4500;
}
}else{
if($acumu_pun > 3500){
  $acumu_pun= 3500;
}
}*/
if($acumu_pun==0){
    $acumu_pun=$conta_pun;
    $saldo=0;
}
              
              //#########
                $consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$rut1."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
                                 $resputbie = mysqli_query($cnn, $consultabie);
                                 if($row = mysqli_fetch_array($resputbie)){
                                    $inicial = $row[0];                                    
                                      
                                 }                     

                                  if($MuestroFechaInicio != $inicial){
                                                             
                                    ?> <script type="text/javascript"> M.toast({html:'Diferencia en las fechas, favor revisar el ingreso a la Salud Pública'});</script> <?php
                                    break 1;
                                  }             
                                    $consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$rut1."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
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
                                            $cuentabienios = $cuentabienios + $diff->format('%Y'); //$diff->format('%R%a');
                                            $final2 = $fecha;
                                            break 1;
                                        }else{
                                            
                                            if($row_rs3[0] == $inicial){
                                                $inicial2 = $row_rs3[0];
                                                $final2 = $row_rs3[1];
                                                //$date1=date_create($inicial2);
                                                //$date2=date_create($final2);
                                                //$diff=date_diff($date1,$date2);
                                                
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
                                        $fechacumplebienio = $nuevainicial2;
																				$nuevainicial3 = date_create($nuevainicial2);
																				date_add($nuevainicial3, date_interval_create_from_date_string('2 years'));
																				date_format($nuevainicial3, 'Y-m-d');
																				$nuevainicial2 =  date_format($nuevainicial3, 'Y-m-d');
																		}  																	
                                    

                                    if($cuentabienios%2==0){ // se multiplica o restar para dejar como valor entro para la búsqueda en la Tabla CARRERA_BIENIO_PTOS
                                        $valido_bie = $cuentabienios * 1;
                                    }else{
                                        $valido_bie= $cuentabienios - 1;
                                    }
                                    //Cuando la división da uno, deja en años como válido para carrera
                                    if($cuentabienios==1){
                                       $cuentabienios=2;
                                       $valido_bie=2;
                                    }
                                    $buscar_bie = "SELECT CBP_PTOS FROM CARRERA_BIENIO_PTOS WHERE CBP_ANOS = '$valido_bie'";
                                    $rs_buscar_bie = mysqli_query($cnn, $buscar_bie);
                                    if($row_bie = mysqli_fetch_array($rs_buscar_bie)){
                                        $bienios_ptos=$row_bie[0];                         
                                    }
                                    if($valido_bie>= 30){
                                      $bienios_ptos= 8000;
                                    }
                                    
                                    $total_puntaje = $bienios_ptos + $acumu_pun;
                                    round($total_puntaje, 2);
                                    
                                    if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
                                        $buscar_criti ="SELECT CPC_AB_INI,CPC_AB_FIN,CPC_NIVEL FROM CARRERA_PTOS_CRITI";
                                        $resputcriti = mysqli_query($cnn, $buscar_criti);
                                        while ($row_rs4 = mysqli_fetch_array($resputcriti)){
                                        //$total_puntaje = 4167.75;
                                            if($row_rs4[0] <= $total_puntaje){
                                                if($total_puntaje <= $row_rs4[1]){                                                   
                                                    $nivel_actual = $row_rs4[2];
                                                    break 1;
                                                }
                                              
                                            }                                
                                        }
                                    
                                    }else{
                                        $buscar_criti ="SELECT CPC_CF_INI,CPC_CF_FIN,CPC_NIVEL FROM CARRERA_PTOS_CRITI";
                                        $resputcriti = mysqli_query($cnn, $buscar_criti);
                                        while ($row_rs4 = mysqli_fetch_array($resputcriti)){
                                        //$total_puntaje = 4167.75;
                                            if($row_rs4[0] <= $total_puntaje){
                                                if($total_puntaje <= $row_rs4[1]){                                                   
                                                    $nivel_actual = $row_rs4[2];
                                                    break 1;
                                                }
                                              
                                            }                                
                                        }
                                    }
              //#########
              $fecumbie = obtenerFechaEnLetra($fechacumplebienio);//($MuestroFecBie);
              
              $MuestroVisto = "El DFL N° 1-3063, de 1980, del Ministerio del Interior; la Ley N° 19.378, de 1995; los Artículos 26° al 29° del Decreto Supremo N° 1.889, de 1995, Reglamento de Atención Primaria de Salud;   La Resolución N° 1.600, del 30 de Octubre de 2008, de la Contraloría General de la República; El D.F.L. N° 1, del 09 de Mayo de 2006, que fija Texto Refundido, Coordinado y Sistematizado de la Ley N° 18.695, publicado en el D.O. del 26.07.2006.";
              $MuestroConsi = "Que, don(a) ".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM.", ".$MuestroProf. ", el ".$fecumbie.", ha completado ".$cuentabienios." años de experiencia en el sector Salud, acumulando por experencia un total de ".$bienios_ptos." puntos, y ".$acumu_pun." puntos por capacitación, sumando un total de ".$total_puntaje." puntos, alcanzando el puntaje requerido para subir al nivel ".$nivel_actual. " de la Categoría ".$MuestroCategoria." a contar de la misma fecha.";
              $MuestroDec   = "RECONÓCESE a don(a) ".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM.", Rut N°".$MuestroRut.", ".$MuestroProf." del ".$MuestroDependencia.", el haber obtenido ".$bienios_ptos." puntos acumulados por los ".$cuentabienios." años reconocidos, a contar del ".$fecumbie.", y ".$acumu_pun." puntos por capacitación sumando un total de ".$total_puntaje." lo que significa ubicarlo(a) en el nivel ".$nivel_actual." de la Categoría ".$MuestroCategoria.", de la carrera funcionaria local a contar de la misma fecha.
              
                  Cancélese sus remuneraciones correspondientes a su nivel y categoría a contar del ".$fecumbie.".-
                
                  Impútese el gasto a la cuenta 2101 \"Personal de Planta\" del presupuesto del Departamento de Salud Municipal para el año ".$ano5.".-";
            }
      
     
        $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE (USU_RUT = '".$usu_rut_edit."')";
        $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
        if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
            $GuardoEstablecimiento=$row_eyd[0];
            $GuardoDependencia=$row_eyd[1];
        }
        $id_formulario = 37;
        $queryForm = "SELECT FOR_ESTA FROM FORMULARIO WHERE (FOR_ID = ".$id_formulario.")";
        $rsqF = mysqli_query($cnn, $queryForm);
        if (mysqli_num_rows($rsqF) != 0){
            $rowqF = mysqli_fetch_row($rsqF);
            if ($rowqF[0] == "ACTIVO"){
                //si formulario activo
                $queryAcceso = "SELECT AC_ID FROM ACCESO WHERE (USU_RUT = '".$Srut."') AND (FOR_ID = ".$id_formulario.")";
                $rsqA = mysqli_query($cnn, $queryAcceso);
                if (mysqli_num_rows($rsqA) != 0){
                    //tengo acceso
                }else{
                    //no tengo acceso
                    $accion = utf8_decode("ACCESO DENEGADO");
                    $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                    mysqli_query($cnn, $insertAcceso);
                    header("location: ../../error.php");
                }
            }else{
                //si formulario no activo
                $accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
                $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                mysqli_query($cnn, $insertAcceso);
                header("location: ../../desactivada.php");
            }
        }
    //}
  }
?>
<html>
    <head>
        <title>Version desarrollo - Personal Salud</title>
        <meta charset="UTF-8">
        <!-- Le decimos al navegador que nuestra web esta optimizada para moviles -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <!-- Cargamos el CSS --> 
        <link type="text/css" rel="stylesheet" href="../../../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../../../include/css/materialize.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../../../include/css/custom.css" />
        <link href="../../../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <style type="text/css">
            body{
                background-image: url("../../../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }

        </style>
        <script type="text/javascript" src="../../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            
                $("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        M.toast({html: 'Rut incorrecto'});
                        $("#btn_usuario").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                        $("#btn_usuario").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });              
                
            });
             
           function cargarusu(id){
                var rut1 = $("#rut_usuario").val();
                var id1 = $("#idmodifica").val();
                if (id1 >0){
                  var rut1 = "<?php echo $rut1;?>";
                  window.location = "reco_bienio.php?rut="+rut1+"&id="+id1
                }else{
                  window.location = "reco_bienio.php?rut="+rut1
                }
                
            }
             
        
          
            function decretar(){
              var fechasub = "<?php echo $MuestroFecBie;?>";
              var puncap = "<?php echo $acumu_pun;?>";
              var biepun = "<?php echo $bienios_ptos;?>";
              var dcnum = $("#num_doc").val();
              var usurut = "<?php echo $rut1;?>";
              var dcfec = $("#fecha_dc").val();
              var dcvisto = $("#text_visto").val();
              var dcconsi = $("#text_consi").val();
              var dcdec = $("#text_decre").val();
              var nomusu = "<?php echo $MuestroNombre;?>";
              var appusu = "<?php echo $MuestroApellidoP;?>";
              var apmusu = "<?php echo $MuestroApellidoM;?>";
              var nuevonivel = "<?php echo $nivel_actual;?>";
              
              var alcalde = $("#alcalde").val();              
              var alcaldesub = document.getElementById('dic_sub').checked;
              if(alcaldesub ==true){
                alcaldesub="(S)";
              }else{
                alcaldesub="";
              }
              var genalc = $("#gen_alc").val();
              if(genalc == ""){
                genalc = "ALCALDE";   
              }              
              
              var secretaria = $("#secretaria").val();
              var secretariasub = document.getElementById('sec_sub').checked;
              if(secretariasub == true){
                secretariasub ="(S)";
              }else{
                secretariasub="";
              }
              var gensec = $("#gen_sec").val();
              if(gensec == ""){
                 gensec = "SECRETARIA";
              }
              
              var distribucion =$("#distribucion").val(); 
              var idmod = $("#idmodifica").val();
              
              if (idmod ==""){
                if(dcnum != "" && dcfec != "" && alcalde != "" && secretaria != "" && distribucion != ""){           

                 $.post( "../../php/carrera/decre_bie.php", { "dcnum2" : dcnum, "usurut2" : usurut, "dcfec2" : dcfec, "dcvisto2" : dcvisto, "dcconsi2" : dcconsi, "dcdec2" : dcdec, "nomusu2" : nomusu, "appusu2" : appusu,
                                                                "apmusu2" : apmusu, "alcalde2" : alcalde, "secretaria2" : secretaria, "distribucion2" : distribucion, "alcaldesub2" : alcaldesub, "secretariasub2" : secretariasub, "genalcalde2" : genalc, "gensecre2" : gensec, "fechasub2" : fechasub, "puncap2" : puncap, "biepun2" : biepun, "nuevonivel2": nuevonivel}, null, "json")  

                   .done(function( data, textStatus, jqXHR ) {
                   if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                         window.location = "reco_bienio.php?rut="+usurut;
                       }
                   })
                   .fail(function( jqXHR, textStatus, errorThrown ) {
                              if ( console && console.log ) {
                                  console.log( "La solicitud a fallado: " +  textStatus);
                                 window.location = "reco_bienio.php?rut="+usurut;
                              }

                    });
                 }else{
                    M.toast({html:'Datos no válidos'});

                 } 
              }else{

              if(dcnum != "" && dcfec != "" && alcalde != "" && secretaria != "" && distribucion != ""){           

                $.post( "../../php/carrera/modifica_decre_bie.php", { "dcnum2" : dcnum, "usurut2" : usurut, "dcfec2" : dcfec, "dcvisto2" : dcvisto, "dcconsi2" : dcconsi, "dcdec2" : dcdec, "nomusu2" : nomusu, "appusu2" :                                                              appusu, "apmusu2" : apmusu, "alcalde2" : alcalde, "secretaria2" : secretaria, "distribucion2" : distribucion, "id2" : idmod, "alcaldesub2" : alcaldesub, 
                                                            "secretariasub2" : secretariasub, "genalcalde2" : genalc, "gensecre2" : gensec, "fechasub2" : fechasub, "puncap2" : puncap, "biepun2" : biepun, "nuevonivel2": nuevonivel}, null, "json" )  

                   .done(function( data, textStatus, jqXHR ) {
                   if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                         window.location = "reco_bienio.php?rut="+usurut;
                       }
                   })
                   .fail(function( jqXHR, textStatus, errorThrown ) {
                              if ( console && console.log ) {
                                  console.log( "La solicitud a fallado: " +  textStatus);
                                 window.location = "reco_bienio.php?rut="+usurut;
                              }

                    });
                 }else{
                    M.toast({html: 'Datos no válidos'});

                 }
              }
       
            }
          function Imprimir(id){
                var idDC = id;
                //window.location = "pdf/sol_permi.php?id="+idSP;
                window.open('http://200.68.34.158/personal/pdf/decreto_bie.php?id='+idDC,'_blank');
            } 
          function Editar(id,ndc){
                var idDC = id;
               
               $("#idmodifica").val(id);
               
          }
          
        </script>
    </head>
    <body onload="cargar();">
        <!-- llamo el nav que tengo almacenado en un archivo -->
        <?php require_once('../../estructura/nav_personal.php');?>
        <!-- inicio contenido pagina -->
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">RECONOCIMIENTO NIVEL</h4>
                        
                        <div class="row">
                            <form class="col s12" method="post" action="" enctype="multipart/form-data">
                                <div class="input-field col s6">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" style="text-transform: uppercase" placeholder="" value="">
                                    <label for="icon_prefix">RUT</label>
                                </div>
                                                                
                                <div class="input-field col s6">
                                    <button class="btn trigger" type="button" name="buscar" id="buscar" value="buscar"  onclick = "cargarusu();">Buscar</button>
                                </div>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>

                                <div class="input-field col s4">
                                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroNombre;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Nombres</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="apellidoP_usuario" id="apellidoP_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroApellidoP;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Paterno</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="apellidoM_usuario" id="apellidoM_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroApellidoM;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Materno</label>
                                </div>                                
                                <div class="input-field col s3">
                                    <input type="text" name="prof_usuario" id="prof_usuario" class="validate" placeholder="" value="<?php echo $MuestroProf;?>" disabled>
                                    <label for="icon_prefix">Profesión</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="categoria_usuario" id="categoria_usuario" class="validate" placeholder="" value="<?php echo $MuestroCategoria;?>" disabled>
                                    <label for="icon_prefix">Categoría</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="nivel_usuario" id="nivel_usuario" class="validate" placeholder="" value="<?php echo $MuestroNivel;?>" disabled>
                                    <label for="nivel_usuario">Nivel</label>
                                </div>                                                             
                               <div class="input-field col s3">
                                    <input type="text" name="dependencia_usuario" id="dependencia_usuario" class="validate" placeholder="" value="<?php echo $MuestroDependencia;?>" disabled>
                                    <label for="icon_prefix">De quien depende</label>
                               </div>
                               <div class="input-field col s4">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s4">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="num_doc" id="num_doc" class="validate" maxlength="50" placeholder="" value="<?php echo $MuestroNum;?>">
                                    <label for="hora_pun">DECRETO ALCALDICIO N°:  </label>
                                </div>
                                <div class="input-field col s4">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  <div class="input-field col s4">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                <div class="input-field col s4">
                                        <input type="text" class="datepicker" name="fecha_dc" id="fecha_dc" placeholder="" value="<?php echo $MuestroFec;?>">
                                      <label for="icon_prefix">Fecha Decreto</label>
                                    </div>
                               <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="row">
                                  <form class="col s12">
                                    <div class="row">
                                      <div class="input-field col s12">
                                        <textarea id="text_visto" class="materialize-textarea"><?php echo $MuestroVisto;?></textarea>
                                        <label for="text_visto">VISTOS</label>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                                <div class="row">
                                  <form class="col s12">
                                    <div class="row">
                                      <div class="input-field col s12">
                                        <textarea id="text_consi" class="materialize-textarea" maxlength="2500"><?php echo $MuestroConsi;?></textarea>
                                        <label for="text_consi">CONSIDERANDO</label>
                                      </div>
                                    </div>
                                  </form>
                                </div>                            
                              <br>
                              <br>
                              <div class="row">
                                  <form class="col s12">
                                    <div class="row">
                                      <div class="input-field col s12">
                                        <textarea id="text_decre" class="materialize-textarea"><?php echo $MuestroDec;?></textarea>
                                        <label for="text_consi">DECRETO</label>
                                      </div>
                                    </div>
                                  </form>
                                </div>      
                              <br>
                            
                                <br>
                                <br>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>                              
                                <div class="input-field col s6">		
																			
																	<input value="<?php echo $alcalde;?>" id="alcalde" type="text" class="validate" name="alcalde" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event);">
                                   <label for="alcalde">Favor indicar Alcalde o Subrogante si corresponde :</label>
                                  </div>
																  <div class="input-field col s4">
                                    <label>
																			<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" <?php echo $alcsub;?>/>
                                      <span>Subrogante</span>
      															</label>
																	</div>
                                  <div class="input-field col s2">                                    
                                    <select name="gen_alc" id="gen_alc" >
                                      <!--<option value="" disabled selected></option>-->
                                      <option value="<?php echo $genalc;?>"><?php echo $genalc;?></option>
                                      <option value="ALCALDE">ALCALDE</option>
                                      <option value="ALCALDESA">ALCALDESA</option>           
                                    </select>
                                    <label>Genero</label>
                                  </div> 
                          				<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" style="display: none">																
																	<div class="input-field col s6">
                                	<input type="text" name="secretaria" id="secretaria" class="validate" value="<?php echo $secretaria;?>" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">
                                	<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>
                            			</div>
																	<div class="input-field col s4">
                                    <label>
																	    <input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub"  <?php echo $secsub; ?>/>
                                      <span>Subrogante</span>
      														  </label>                                
																	</div>
                                  <div class="input-field col s2">                                    
                                    <select name="gen_sec" id="gen_sec" value="<?php echo $gensec;?>">
                                      <!--<option value="" disabled selected></option>-->
                                      <option value="<?php echo $gensec;?>"><?php echo $gensec;?></option>
                                      <option value="SECRETARIA">SECRETARIA</option>
                                      <option value="SECRETARIO">SECRETARIO</option>           
                                    </select>
                                    <label></label>
                                  </div>                                  
                                  <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  
																	<div class="input-field col s4">																		
																	<input id="distribucion" type="text" name="distribucion" class="validate" value="<?php echo $responsables;?>" required>
                                	<label for="distribucion">Indique Responbles del decreto(INICIALES) :</label>
                            			</div>
                              
                                <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="col s12">
                                    <div name ="decre" id="decre" type="button" onclick="decretar();" class="btn trigger">Decretar</div>
                                </div> 
                                <div class="input-field col s12">
                                    <input style="display:none" id="idmodifica" type="text" class="validate" name="idmodifica" value="<?php echo $iddcre;?>">
                                </div>
                                
                              <br>
                               <br>
                            <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                          <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                              <table id="tab_decretos" class="responsive-table bordered striped col s12">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Número Decreto</th>
                                    <th>Fecha</th>                            
                                </tr>
                                        <tbody>
                                            <?php                                                
                                               $query = "SELECT DB_ID,DB_DC_NUM, DATE_FORMAT(DB_FEC,'%Y-%m-%d') FROM DECRE_BIE WHERE (USU_RUT = '".$rut1."') ORDER BY DB_FEC DESC";    
                                                    $respuesta = mysqli_query($cnn, $query);                                                
                                                $cont = 0;                                                
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    echo "<tr>";
                                                        echo "<td>  </td>";
                                                        echo "<td>  </td>";
                                                        echo "<td></td>";
                                                        echo "<td><id='in".$cont."'>".$row_rs[0]."</td>";
                                                        echo "<td><class='col s6'>".utf8_encode($row_rs[1])."</td>";
                                                        echo "<td>".$row_rs[2]."</td>";
                                                        echo '<td><button class="btn trigger" name="imprimir" onclick="Imprimir
                                                            ('; echo "'".utf8_encode($row_rs[0])."'"; echo');" id="imprimir" 
                                                            type="button">Imprimir</button></td>';
                                                        echo '<td><button class="btn trigger" name="editar" onclick="Editar('; echo "'".utf8_encode($row_rs[0])."'"; echo');cargarusu();" id="editar" 
                                                            type="button">Editar</button></td>';
                                                }    
                                            ?>
                                        </tbody>
                                    </thead>                                    
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../../include/js/materialize.js"></script>                 
        
</html>
    </body>