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
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Snombre = utf8_encode($_SESSION['USU_NOM']);
        $SapellidoP = utf8_encode($_SESSION['USU_APP']);
        $SapellidoM = utf8_encode($_SESSION['USU_APM']);
        $Semail = utf8_encode($_SESSION['USU_MAIL']);
        $Sdireccion = utf8_encode($_SESSION['USU_DIR']);
        $Sfono = utf8_encode($_SESSION['USU_FONO']);
        $Scargo = utf8_encode($_SESSION['USU_CAR']);
        $Sestablecimiento = utf8_encode($_SESSION['EST_ID']);
        $Sdependencia = utf8_encode($_SESSION['USU_DEP']);
        $Scategoria = utf8_encode($_SESSION['USU_CAT']);
        $Snivel = utf8_encode($_SESSION['USU_NIV']);       
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
        $Sfecing1 = $_SESSION['USU_FEC_ING'];
        $Sfecini1 = $_SESSION['USU_FEC_INI'];
				
				date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
				$ano5 = date("Y");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $usu_rut_edit = $_GET['rut'];
        include ("../../../include/funciones/funciones.php");
				$Sliqdir = utf8_encode($_SESSION['LIQDIR']);
        $cnn = ConectarPersonal();
        $buscar = "SELECT ESTABLECIMIENTO.EST_NOM FROM USUARIO INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (USUARIO.USU_RUT = '".$Srut."')";
        $rs = mysqli_query($cnn, $buscar);
        if($row = mysqli_fetch_array($rs)){
            $MuestroEstablecimiento = $row[0];            
        }        
        $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE USU_RUT = '$usu_rut_edit'";
        $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
        if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
            $GuardoEstablecimiento=$row_eyd[0];
            $GuardoDependencia=$row_eyd[1];
        }
        $id_formulario = 33;
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
        </script>
    </head>
    <body>
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
                        <h4 class="light">Liquidación de Sueldo</h4>
                        <?php
											//echo $Sliqdir;
													if($Sliqdir != ""){
														$total = "";
														for ($segundos = 1; $segundos <= 2; $segundos++)
														{
                              sleep($segundos);
                              $total = $segundos;
														}
                              unlink($Sliqdir);
                              $Sliqdir ="";
													}
											  ?>
                        <div class="row">
                            <form class="col s12" method="post" action="" enctype="multipart/form-data">
                                  <div class="col s12">
                                <h1>CARGA MASIVA DE LIQUIDACIONES DE SUELDO</h1>                                                  
                                <p style="text-align:center;"><strong> En caso de ser "PLANILLA SUPLEMENTARIA",favor seleccionar en "Tipo Liquidación"</strong></p>                                    
                            </div>
                            <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                            <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>  
                            <div class="input-field col s4 aling=center">                                    
                                <select name="liqui_estable" id="liqui_estable" >
                                  <!--<option value="" disabled selected></option>-->
                                  <!--<option value="RE1">CESFAM RENGO</option>-->
                                  <option value="RO1">CESFAM ROSARIO Y DEPARTAMENTO</option>
                                  <option value="RO2">CESFAM RENGO Y CESFAM ORIENTE</option>             
                                </select>
                                <label>Establecimiento</label>
                              </div>
														<div class="input-field col s4">
                                    <input type="text" class="datepicker" name="fechaIngreso" id="fechaIngreso" placeholder=""> 
                                    <label for="icon_prefix" id="fechaIngreso">Ingrese Fecha</label>
                              </div>
                            <div class="input-field col s4 aling=center">                                    
                                <select name="liqui_suple" id="liqui_suple" >
                                  <!--<option value="" disabled selected></option>-->
                                  <option value="1"></option>
                                  <option value="2">Suplementaria</option>           
                                </select>
                                <label>Tipo Liquidación</label>
                              </div>
															<div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>  
															<div class="input-field col s12">
																			<input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
															</div>
                         
															<div class="file-field input-field col s10">
                                  <div class="btn">
                                    <span>Archivo</span>
                                    <input type="file" name="archivo">
                                  </div>
                                  <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" name="archivo" id="archivo" />                                   
                                  </div>                                  
                                </div>
                                <button class="btn waves-effect waves-light col s2" type="submit" name="boton">Cargar Liquidación</button>
                                                                <?php
                                                                if (isset($_POST['boton'])){     
                                                                    $estable = $_POST['liqui_estable'];
                                                                    $tipo1 = $_POST['liqui_suple'];                                            
																						$restfec = substr($fechabus, 0,7);
                                            if($tipo1 == 2){
                                              $sup = "2-";
                                            }else{
                                              $sup ="";
                                            }
																				$formatos   = array('.txt');
																				$fechabus = $_POST['fechaIngreso'];																				
																			  $mescarp = substr($fechabus, -5,2);
																				$anocarp = substr($fechabus,0,4);
																				$directorio = '../../../include/liquidacion_txt';
																				$carpeta = $directorio."/".$anocarp."/".$mescarp;
																					if (!file_exists($carpeta)) {
																							mkdir($carpeta, 0777, true);		
																					}
                                      		
																					if($fechabus != ""){
																							$nombreArchivo    = $_FILES['archivo']['name'];
																							//$nombreArchivo1    = $_FILES['archivo']['name'];
																							$nombreTmpArchivo = $_FILES['archivo']['tmp_name'];
																							$ext         = substr($nombreArchivo, strrpos($nombreArchivo, '.'));
																							$nombreArchivoId = $sup.$estable.$ext;
																							$nombreArchivoId = "$nombreArchivoId";
                                                                                            //da acceso al archivo
                                                                                            $sinpermi= $carpeta."/".$nombreArchivoId;
                                                                                            chmod($sinpermi, 0777);

																							if (in_array($ext, $formatos)){
																								if (move_uploaded_file($nombreTmpArchivo, "$carpeta/$nombreArchivoId")){
																										?><script> M.toast({html: 'Archivo Cargado'});</script><?php
                                                    $sinpermi= $carpeta."/".$nombreArchivoId;
                                                  chmod($sinpermi, 0000);
                                                  
																								}else{
																									?><script> M.toast({html: 'Archivo No Cargado, error de permisos'});</script><?php
																								}
																							}else{
																								?><script> M.toast({html: 'Formato no aceptado'});</script><?php
																							}
																					}else{
 																						?><script> M.toast({html: 'Ingrese Fecha Liquidación de Sueldo'});</script><?php
																					}
																			 }
																			 ?>
															<div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>  
															<div class="input-field col s12">
																			<input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
															</div>
															<div class="input-field col s6">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" placeholder="" value="">
                                    <label for="icon_prefix">RUT</label>
                               </div>	
															<div class="col s4">
																<button id="buscar1" class="btn trigger" type="submit" name="buscar1" value="Buscar1">Buscar</button>
															</div>															
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

				<?php
            if($_POST['buscar1'] == "Buscar1"){              
							$cnn = ConectarPersonal();
							$rutbus = $_POST['rut_usuario'];
							$buscar = "SELECT USUARIO.USU_RUT FROM USUARIO  WHERE (USUARIO.USU_RUT = '".$rutbus."')";
							$rs = mysqli_query($cnn, $buscar);
							$largo = strlen ($rutbus);
							if($largo<12){
								 $rutbus = "0".$rutbus;
							}
								 if($row = mysqli_fetch_array($rs)){									
												$estable = $_POST['liqui_estable'];
												$tipo1 = $_POST['liqui_suple'];                                            
												$restfec = substr($fechabus, 0,7);
														if($tipo1 == 2){
															$sup = "2-";
														}else{
															$sup ="";
														}							
												$formatos   = array('.txt');
												$fechabus = $_POST['fechaIngreso'];																				
												$mescarp = substr($fechabus, -5,2);
												$anocarp = substr($fechabus,0,4);
												$directorio = '../../../include/liquidacion_txt';
												$nombreArchivoId = $sup.$estable.".txt";
												$carpeta = $directorio."/".$anocarp."/".$mescarp;
												$_SESSION['LIQDIR'] = $carpeta.'/'.$rutbus.'.txt';
												$rut1=1;
												$inicio=0;
												$inibus=0;
                        $sinpermi= $carpeta."/".$nombreArchivoId;
                        
                        chmod($sinpermi, 0755);
															if (is_readable($carpeta)) {                        
																$fp = fopen($carpeta.'/'.$nombreArchivoId, "r");
																$contador=0;
																while(!feof($fp)) {
																		$linea = fgets($fp);
																		$linea = str_replace('"','',$linea);   
                                    $linea;
																		if($inibus==0){
																			$bus = $linea;            
																			$inibus=1;
																		} 
																	if($linea != $bus){                                         
																				$file = fopen($carpeta.'/'.$rut1.'.txt', "a");       
																				fwrite($file, $linea);
																						$contador= $contador+1;
																						if ($contador == 8){
																							$rest = substr($linea, -7,5);      
																							$rest1 = substr($linea, -10, 3);                
																							$rest2 = substr($linea,-12,2);                         
																							$rest3 = $rest2.".".$rest1.".".$rest;
																							$dir = $carpeta."/".$rut1.".txt";
																							$dirfin = $carpeta."/".$rest3.".txt";
																						}
                                      if($linea == "   LIQUIDACION"){                                
                                        echo $linea;
                                      }
																		}else{              
																			fclose($file);                            
																			rename($dir, $dirfin);
																			$contador=0;
																			if($rest3 == $rutbus){
																				echo '<script type="text/javascript"> window.open("'.$dirfin.'" , "Liquidación" , "width=550,height=650,scrollbars=yes,menubar=yes,toolbar=yes,location=no")</script>';															
																				break 1;
																			}else{
																				unlink($dirfin);
																			}
																		}      
																} 
																fclose($fp);      
																$dir =  $carpeta."/"."1".$sup.".txt";
																$dirfin1 =  $carpeta."/".$rest3.$sup.".txt";
																copy($dir, $dirfin1);
																if($rest3 == $rutbus){
																				echo '<script type="text/javascript"> window.open("'.$dirfin.'" , "Liquidación" , "width=550,height=650,scrollbars=yes,menubar=yes,toolbar=yes,location=no")</script>';										
																}else{
																				unlink($dirfin);
																				unlink($dir);
																}											
															}else{
																	echo  '<script type="text/javascript"> M.toast({html: "Liquidación no encontrada"});</script>';
																/*echo "<script> ver(); </script>";*/
															}	
												echo '<script type="text/javascript"> window.location="liquidacion2.php"; </script>';
								}else{
										echo  '<script type="text/javascript"> M.toast({html: "RUT no encontrado"});</script>';
								} 
              $sinpermi= $carpeta."/".$nombreArchivoId;
              chmod($sinpermi, 0000);
            }

echo $Sliqdir;
        ?>
</html>
</body>


 

