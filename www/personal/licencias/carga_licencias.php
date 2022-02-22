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
        header("location: ../../index.php");
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
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $id_formulario = 41;
        $ext = $_GET['ext'];
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
                    header("location: ../error.php");
                }
            }else{
                //si formulario no activo
                $accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
                $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                mysqli_query($cnn, $insertAcceso);
                header("location: ../desactivada.php");
            }
        }
    }   
?>
<html>
    <head>
        <title>Personal Salud</title>
        <meta charset="UTF-8">
        <!-- Le decimos al navegador que nuestra web esta optimizada para moviles -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <!-- Cargamos el CSS --> 
        <link type="text/css" rel="stylesheet" href="../../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../../include/css/materialize.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../../include/css/custom.css" />
        <link href="../../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <style type="text/css">
            body{
                background-image: url("../../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }

        </style>
        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () { 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
            }); 
        </script>
    </head>
    <body onload="cargar();">
         <!-- llamo el nav que tengo almacenado en un archivo -->
        <?php require_once('../estructura/nav_personal.php');?>
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">Carga de Licencias Medicas</h4>
                        <?php													
														$directorio = '../../include/licencias';
                            $sinpermi = $directorio."/".$id2."-".$id1.".pdf";     
                            chmod($sinpermi, 0000);																							
												?>
                        <div class="row">
                            <form class="col s12" method="post" action="" enctype="multipart/form-data">                                                      
                             <?php                                  
                                
                               if (isset($_POST['boton'])){
                                  $nombreArchivoId = "licencias";
                                if($nombreArchivoId != ""){                                                             
                                  $formatos   = array('.xlsx','.xls');
                                  $directorio = '../../include/licencias';  
                                  $nombreArchivo    = $_FILES['archivo']['name'];
                                  $nombreTmpArchivo = $_FILES['archivo']['tmp_name'];
                                  $ext         = substr($nombreArchivo, strrpos($nombreArchivo, '.'));
                                  $nombreArchivoId = "$nombreArchivoId"."$ext";
                                  $directorio1 = $directorio."/".$nombreArchivoId;
                                    if(in_array($ext, $formatos)){
                                      //chmod($directorio1, 0777,true);
                                      if (move_uploaded_file($nombreTmpArchivo, "$directorio/$nombreArchivoId")){
                                        chmod($directorio1, 0000);
                                        ?><script>M.toast({html: 'Archivo Cargado'});</script>  <?php                                                        
                                        echo '<script type="text/javascript"> window.location=("carga_licencias.php?ext='.$ext.'")</script>';    
                                      }else{
                                        chmod($directorio1, 0000);
                                        ?><script>M.toast({html: 'Archivo No Cargado, error de permisos'});</script><?php
                                      }
                                    }else{
                                      chmod($directorio1, 0000);
                                      ?><script>M.toast({html: 'Formato no aceptado'});</script><?php
                                    }
                                }
                             }
                              if(isset($_POST['boton1'])){
                                $directorio = '../../include/licencias';
                                $sinpermi = $directorio."/licencias".$ext;
                                chmod($sinpermi, 0777);
                                if (is_readable($sinpermi)) {
                                  require '../../include/PHPExcel/Classes/PHPExcel/IOFactory.php';  
                                  $objPHPExcel = PHPExcel_IOFactory::load($sinpermi);
                                  $objPHPExcel->setActiveSheetIndex(0);
                                  $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                                 
                                }else{
                                  ?><script>M.toast({html: 'Archivo no encontrado'});</script><?php
                                }
                              }
                            ?>                             
                             
                              <div class="file-field input-field col s8">
                                  <div class="btn">
                                    <span>Archivo</span>
                                    <input type="file" name="archivo">
                                  </div>
                                  <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" name="archivo" id="archivo" />                                   
                                  </div>                                  
                                </div>
                              <div class="col s2">
                                <button class="btn trigger" id="boton" type="submit" name="boton" value="Boton">Cargar</button> 
                              </div>
                              <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                              <div class="input-field col s4">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>                             
                              <div class="col s4">
                                <button class="btn trigger" type="submit" id="boton1" name="boton1" value="Bonto1">Ver Archivo</button>    
                              </div>
                            </form>
                            <?php
                            function rut( $rut ) {
                                return number_format( substr ( $rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $rut, strlen($rut) -1 , 1 );
                            }      
                            $Rut = $objPHPExcel->getActiveSheet()->getCell('A1')->getCalculatedValue();
                            $i = 2;
                            while($Rut != "Rut"){
                              $Rut = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                              $i = $i + 1;
                            }
														echo '<table class="responsive-table boradered striped">';
															echo '<thead>';
																echo '<tr>';
																	echo '<th>RUT</th>';
																	echo '<th>FUNCIONARIO</th>';
																	echo '<th>NUM LICENCIA</th>';
                                  echo '<th>TIPO</th>';
																	echo '<th>INICIO</th>';
																	echo '<th>TERMINO</th>';
																	echo '<th>DIAS</th>';
																	echo '<th>T ESTIMADO</th>';
																	echo '<th>T RECUPERADO</th>';
																echo '</tr>';
																echo '<tbody>';
																for ($x = $i; $x <= $numRows; $x++) {
																	//imprimo y guardo registro
																	$Rut = $objPHPExcel->getActiveSheet()->getCell('A'.$x)->getCalculatedValue();
																	$usu_rut = rut($Rut);
																	$Nombre = $objPHPExcel->getActiveSheet()->getCell('B'.$x)->getCalculatedValue();
																	$NumLice = $objPHPExcel->getActiveSheet()->getCell('D'.$x)->getCalculatedValue();
																	$Rec = $objPHPExcel->getActiveSheet()->getCell('E'.$x)->getFormattedValue();
																	list($mes_rec, $dia_rec, $ano_rec) = split('[/]', $Rec);
																	if($dia_rec < 10){$dia_rec = "0".$dia_rec;}
																	if($mes_rec < 10){$mes_rec = "0".$mes_rec;}
																	$FecRecepcion = $ano_rec."/".$mes_rec."/".$dia_rec;
																	$Ini = $objPHPExcel->getActiveSheet()->getCell('F'.$x)->getFormattedValue();
																	list($mes_ini, $dia_ini, $ano_ini) = split('[/]', $Ini);
																	if($dia_ini < 10){$dia_ini = "0".$dia_ini;}
																	if($mes_ini < 10){$mes_ini = "0".$mes_ini;}
																	$FecIni = $ano_ini."/".$mes_ini."/".$dia_ini;
																	$Fin = $objPHPExcel->getActiveSheet()->getCell('G'.$x)->getFormattedValue();
																	list($mes_fin, $dia_fin, $ano_fin) = split('[/]', $Fin);
																	if($dia_fin < 10){$dia_fin = "0".$dia_fin;}
																	if($mes_fin < 10){$mes_fin = "0".$mes_fin;}
																	$FecFin = $ano_fin."/".$mes_fin."/".$dia_fin;
																	$Dias = $objPHPExcel->getActiveSheet()->getCell('I'.$x)->getCalculatedValue();
																	$TotalEstimado = $objPHPExcel->getActiveSheet()->getCell('K'.$x)->getCalculatedValue();
																	$TotalRecuperado = $objPHPExcel->getActiveSheet()->getCell('L'.$x)->getCalculatedValue();
                                  $Tipo = $objPHPExcel->getActiveSheet()->getCell('N'.$x)->getCalculatedValue();
																	//reviso que licencia no exista en la base de datos
																	$query = "SELECT LM_NUM,LM_ID,LM_TR FROM LICENCIAS_MEDICAS WHERE (USU_RUT = '$usu_rut') AND (LM_NUM = $NumLice)";
																	$respuesta = mysqli_query($cnn,$query);
																	$row = mysqli_fetch_row($respuesta);
																	$lm_num = $row[0];
																	if($lm_num == $NumLice){
																		$lm_id = $row[1];
																		$lm_tr = $row[2];
																		if($lm_tr != $TotalRecuperado){
																			if($TotalRecuperado > 0){
																				$Actualizar = "UPDATE LICENCIAS_MEDICAS SET LM_TR = '$TotalRecuperado', LM_ESTA = 'PAGADA', LM_OBSERVACION = ''  WHERE LM_ID = $lm_id";
																				mysqli_query($cnn, $Actualizar);
																				echo "SE ACTUALIZA MONTO RECUPERADO A ".$TotalRecuperado." DE LA LICENCIA N° ".$lm_num." Y SU ESTADO SE ACTUALIZA A 'PAGADA'";
																				echo "<br>";
																			}
																		}
																	}else{
																		echo "<tr>";
																			echo "<td>".$usu_rut."</td>";
																			echo "<td>".$Nombre."</td>";
																			echo "<td>".$NumLice."</td>";
                                      echo "<td>".$Tipo."</td>";
																			echo "<td>".$FecIni."</td>";
																			echo "<td>".$FecFin."</td>";
																			echo "<td>".$Dias."</td>";
																			echo "<td>".$TotalEstimado."</td>";
																			echo "<td>".$TotalRecuperado."</td>";
																		echo "</tr>";
																		if($TotalRecuperado > 0){
																			$lm_esta = "PAGADA";
																		}else{
																			$lm_esta = "PAGO PENDIENTE";
																		}
																		$insert = "INSERT INTO LICENCIAS_MEDICAS (LM_NUM,USU_RUT,LM_FEC,LM_FEC_INI,LM_FEC_FIN,LM_DIAS,LM_TE,LM_TR,LM_ESTA,LM_TIPO) VALUES ('$NumLice', '$usu_rut', '$FecRecepcion', '$FecIni', '$FecFin',$Dias,'$TotalEstimado','$TotalRecuperado','$lm_esta','$Tipo')";
               											mysqli_query($cnn, $insert);
																	}
																	$Rut = "";
																	$usu_rut = "";
																	$Nombre = "";
																	$NumLice = "";
																	$FecRecepcion = "";
																	$FecIni = "";
																	$FecFin = "";
																	$Dias = "";
																	$TotalEstimado = "";
																	$TotalRecuperado = "";
																	$Rec = "";
																	$Ini = "";
																	$Fin = "";
                                  $Tipo = "";
																}
																echo '</tbody>';
															echo '</thead>';
														echo '</table>';
                            //alimino archivo
                            unlink($sinpermi);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>       

        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        
</html>
</body>