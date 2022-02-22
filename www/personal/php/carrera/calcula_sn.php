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
	$Snivel = utf8_encode($_SESSION['USU_NIVEL']);
	//$row_rs[6] = utf8_encode($_SESSION['USU_FEC_INI']);
	$Scategoria = utf8_encode($_SESSION['USU_CAT']);
	date_default_timezone_set("America/Santiago");
	$fecha = date("Y-m-d");
	$ano5 = date("Y");
	$mes = date("m");
	$hora = date("H:i:s");
	$ipcliente = getRealIP();
 // $rut1 = $_GET['rut'];
	include ("../../../include/funciones/funciones.php");
	$cnn = ConectarPersonal();

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
				$insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$row_rs[0]', '$ipcliente', '$fecha', '$hora')";
				mysqli_query($cnn, $insertAcceso);
				header("location: ../../error.php");
			}
		}else{
			//si formulario no activo
			$accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
			$insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$row_rs[0]', '$ipcliente', '$fecha', '$hora')";
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
		<script type="text/javascript" src="../../../include/js/jquery.js"></script>
		<script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
		<script type="text/javascript" src="../../../include/js/materialize.js"></script>
		<script>
			$(document).ready(function () {
				//Animaciones    
				$('select').material_select();
				$(".dropdown-button").dropdown();
				$(".button-collapse").sideNav();  
			});      
			function modificar_usu(id){
				var rut = id;    
				window.open('../../../personal/parametros/editar_usuario.php?rut='+rut); 
			}
			function decreto_nivel(id){
				var rut = id; 
				window.open('../../../personal/parametros/carrera/reco_bienio.php?rut='+rut); 
			}
		</script>
	</head>
	<body onload="cargar();">
		</br>
	</br>
</br>
<div class="container">
	<div class="section">
		<div class="row">
			<div class="col s12 center block" style="background-color: #ffffff">
				<h4 class="light">Subir de Nivel</h4>  
				<div class="row">
					<form class="col s12" method="post" action="" enctype="multipart/form-data">  
						<table id="tab_carrera" class="responsive-table bordered striped">
							<thead>
								<tr>
									<th>RUT</th>
									<th>Nombre</th>   
									<th>Nivel Actual</th>
									<th>Nuevo Nivel</th>  
								</tr>
							<tbody>
								<?php
								$acumu_pun = 0;
								$acumu_pun1 = 0;
								$sal_acu = 0;
								if($mes >= 10){
									$query = "SELECT USU_RUT,USU_NOM,USU_APP,USU_APM,USU_NIV,USU_DEP,USU_FEC_INI,USU_CAT FROM USUARIO WHERE (USU_ESTA='ACTIVO') AND ((USU_CONTRA = 'PLANTA') OR (USU_CONTRA='CONTRATA') OR (USU_CONTRA='PLAZO FIJO'))";
								}else{
									$query = "SELECT USU_RUT,USU_NOM,USU_APP,USU_APM,USU_NIV,USU_DEP,USU_FEC_INI,USU_CAT FROM USUARIO WHERE (USU_ESTA='ACTIVO') AND (USU_CONTRA = 'PLANTA') ";
								}  
								$respuesta = mysqli_query($cnn, $query);
								while ($row_rs = mysqli_fetch_array($respuesta)){
									$acumu_pun = 0;
									$acumu_pun1 = 0;
									$sal_acu = 0;
									$saldo=0;
									$conta_pun=0;
									$total_puntaje=0;
									$valano="";
									$valido_bie=0;
									$nivel_actual =0;
									$cuentabienios=0;
									$cuentabienios2=0;
									$bienios_ptos=0;
									$diasno=0;    
									$MuestroFechaInicio = $row_rs[6]; 
									$MuestroCategoria= $row_rs[7];
									$fecdiasno=0;	
									
									$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$row_rs[0]."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC <='2021-08-31') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";	
									$respuesta2 = mysqli_query($cnn, $query2);
									$row = $respuesta2->fetch_array(MYSQLI_NUM);
									if($row[1] !=''){
										$iniacu=0;
										for ($i = $row[1]; $i <= $ano5; $i++) {
											if($iniacu==0){
												$valano = $row[1];
												$iniacu=1;
											}
											if(isset($i)==''){
												$i=0;
											} 
										$query23="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$row_rs[0]."') AND (CA_ESTADO <> 'Inactivo') AND (YEAR(CA_FEC)=$i) AND (CA_FEC <='2021-08-31') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
									$respuesta3 = mysqli_query($cnn, $query23);
									$row1 = $respuesta3->fetch_array(MYSQLI_NUM);
									//$valano = $i;
									$year = $row1[1];
									$puntaje = $row1[2];
									if($year !='' and $puntaje !=''){
										if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
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
									}
								}
							}
									$acumu_pun31= $acumu_pun;
									//############ HASTA EL 31 DE AGOSTO DE 2021 ##################
									
																		//########### DESDE EL 01 DE SEPTIEMBRE 2021 ##################
									$acumu_pun = 0;
									//$acumu_pun1 = 0;
									//$sal_acu =0;
									$acumu_pun01=0;
									$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC_ING),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$row_rs[0]."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC_ING >='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
									$respuesta21 = mysqli_query($cnn, $query2);
									$row21 = $respuesta21->fetch_array(MYSQLI_NUM);	
									if($row21 != ''){
										for ($i1 = $row21[1]; $i1 <= $ano5; $i1++) {
											$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC_ING),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$row_rs[0]."') AND (CA_ESTADO <> 'Inactivo') AND (YEAR(CA_FEC_ING)=$i1) AND (CA_FEC_ING >='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
											$respuesta31 = mysqli_query($cnn, $query2);
											$row211 = $respuesta31->fetch_array(MYSQLI_NUM);
											//$valano = $i1;
											$year = $row211[1];
											$puntaje = $row211[2];
											If($year>2022){
											if($year !='' and $puntaje !=''){
												if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
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
											}
										}
										}  
									}else{
										$acumu_pun=0;
									}
									$acumu_pun01 = $acumu_pun;
									//########## FIN DESDE EL 01 DE SEPTIEMBRE 2021 ####################
									$acumu_pun=0;
									//#### INICIO SUMA DE PUNTAJES POR CAPACITACIÓN CON AMBOS CÁLCULOS #####
									$acumu_pun = $acumu_pun31 + $acumu_pun01;
									//if($row_rs[0]=='11.398.519-4'){
										//echo $acumu_pun."  Puntaje acumulado";
									//}
									//#### FIN SUMA DE PUNTAJES POR CAPACITACIÓN CON AMBOS CÁLCULOS #####
									
									/*$queryx = "SELECT CA_ID, CA_DES, DATE_FORMAT(CA_FEC,'%Y-%m-%d'), CA_HORA, CA_NOTA, CA_NIVEL, CA_TOTAL, DATE_FORMAT(CA_FEC_ING, '%Y-%m-%d'),CA_ESTADO,
									CA_HORA_PUN,CA_NIVEL_PUN,CA_NOTA_PUN,CA_TOTAL FROM CARRERA_ACT WHERE (USU_RUT = '".$row_rs[0]."') AND (CA_ESTADO <>'Inactivo') ORDER BY CA_FEC"; 
									$respuestax = mysqli_query($cnn, $queryx);  
									$cont = 0;   
									while ($row_rsx = mysqli_fetch_array($respuestax)){     
										if ($row_rsx[8] == "Activo" || $row_rsx[8] == "Decretado"){   
											$conta_pun = $row_rsx[6] + $conta_pun;
											//if($row_rs[0]=='16.528.651-0'){ //OBTENER ACUMULADO POR FUNCIONARIO
											//echo $conta_pun."  ";
											//}	
										}
									}
									if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
										if($conta_pun > 4500){
											$conta_pun= 4500;
										}
									}else{
										if($conta_pun > 3500){
											$conta_pun= 3500;
										}
									}         
									$acumu_pun = 0;
									$acumu_pun1 = 0;
									$sal_acu =0;
									$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$row_rs[0]."') AND (CA_ESTADO <> 'Inactivo') GROUP BY ACU ORDER BY ACU ASC";
									$respuesta2 = mysqli_query($cnn, $query2);
									while ($row_rs2 = mysqli_fetch_array($respuesta2)){  
										$valano = $row_rs2[1];
										if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
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
										if($row_rs[0]=='16.528.651-0'){ //OBTENER ACUMULADO POR FUNCIONARIO
											//echo $conta_pun."  ";
										}
									}             
									$saldo = number_format($conta_pun - $acumu_pun,2,'.', '');
									$valano = $valano + 1;
									while($valano <= $ano5){
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
									
									//BIENIOS
									$consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$row_rs[0]."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
									$resputbie = mysqli_query($cnn, $consultabie);
									if($row = mysqli_fetch_array($resputbie)){
										$inicial = $row[0];
									}        
									/*if($MuestroFechaInicio != $inicial){
									
                                    ?> <script type="text/javascript"> M.toast({html: 'Diferencia en las fechas, favor revisar el ingreso a la Salud Pública'});</script> <?php
                                    break 1;
                                  }       */      
                                    $consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$row_rs[0]."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
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
                                        $fecdiasno=$nuevainicial2;
                                    }else{                                        
                                         $date1=date_create($MuestroFechaInicio);
                                         $date2=date_create($final2);   
                                         $interval=date_diff($date1,$date2);   
                                         $cuentabienios =  $interval->format('%Y');
                                    } 
                                    
                                    if($nuevainicial2==""){
                                      $nuevainicial2 = $MuestroFechaInicio;  
                                    }
                                    if($fecdiasno ==0){
                                      $fecdiasno = $MuestroFechaInicio;
                                    }else{
                                      $fecdiasno = $nuevainicial2;
                                    }
                                    while ($nuevainicial2 <= $fecha){
                                    $nuevainicial3 = date_create($nuevainicial2);
                                    date_add($nuevainicial3, date_interval_create_from_date_string('2 months'));
                                    date_format($nuevainicial3, 'Y-m-d');
                                    $nuevainicial2 =  date_format($nuevainicial3, 'Y-m-d');   
                                    }  	
                                                      
                                    if($cuentabienios%2==0){ // se multiplica o restar para dejar como valor entero para la búsqueda en la Tabla CARRERA_BIENIO_PTOS
                                        $valido_bie1 = $cuentabienios * 1;
                                    }else{
                                        $valido_bie1= $cuentabienios - 1;
                                    }

                                     $date1=date_create($fecdiasno);
                                     $date2=date_create($nuevainicial2);  
                                     $interval=date_diff($date1,$date2);   
                                     $cuentabienios2 =  $interval->format('%Y');
                                    
                                    if($cuentabienios2%2==0){ // se multiplica o restar para dejar como valor entero para la búsqueda en la Tabla CARRERA_BIENIO_PTOS
                                        $valido_bie2 = $cuentabienios2 * 1;
                                    }else{
                                        $valido_bie2= $cuentabienios2 - 1;
                                    }
                                
                                $valido_bie = max($valido_bie1,$valido_bie2);   
                              
                                        
                                    $buscar_bie = "SELECT CBP_PTOS FROM CARRERA_BIENIO_PTOS WHERE CBP_ANOS = '$valido_bie'";
                                    $rs_buscar_bie = mysqli_query($cnn, $buscar_bie);
                                    if($row_bie = mysqli_fetch_array($rs_buscar_bie)){
                                        $bienios_ptos=$row_bie[0];                         
                                    }
                                    if($valido_bie>= 30){
                                      $bienios_ptos= 8000;
                                    }
                                    
									$valido_bie2= $valido_bie /2;
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
                                            if($row_rs4[0] <= $total_puntaje){
                                                if($total_puntaje <= $row_rs4[1]){                  
                                                    $nivel_actual = $row_rs4[2];
                                                    break 1;
                                                }
                                              
                                            }                                
                                        }
                                    }                  

                                    //cuando el puntaje queda entre los puntos cróticos
                                    if($nivel_actual==0){
                                    	$nivel_actual = $row_rs[4];
                                    }
                                    //################
                                          $cont = 0; 
                                          if($nivel_actual < $row_rs[4]){
                                            echo "<tr>";
                                                echo "<td><id='in".$cont."'>".$row_rs[0]."</td>";
                                                echo "<td>".$row_rs[1]." ".$row_rs[2]." " .$row_rs[3]."</td>";
                                                echo "<td>".$row_rs[4]."</td>";
                                                if($diferenciafecha == 1){
                                                  echo "<td>NO CALCULADO</td>";
                                                }else{
                                                  echo "<td>".$nivel_actual."</td>";
                                                }
                                                echo '<td><button class="btn trigger" name="modificar" onclick="modificar_usu('; echo "'".$row_rs[0]."'"; echo');" id="modificar" type="button">Mod. Nivel</button></td>';
                                                if($diferenciafecha == 0){
                                                  echo '<td><button class="btn trigger" name="decreto" onclick="decreto_nivel('; echo "'".$row_rs[0]."'"; echo');" id="decreto" type="button">Reco. Nivel</button></td>';                                                        
                                                }else{
                                                  echo "<td>DIFERENCIA FECHA</td>";  
                                                }                                                                                                         
                                            echo "</tr>"; 
                                                $cont = $cont + 1;

                                         } 
                                          /*if($row_rs[0] == "10.408.545-8"){
                                            break 2;
                                          }*/

                                      }
                                         

                                    ?>

                                            

                                        </tbody>
                                    </thead>                                    
                                </table>
                                <br>
                                <br>                          
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>        

        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones
                
                $(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        Materialize.toast('Rut incorrecto', 4000);
                        $("#btn_usuario").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                        $("#btn_usuario").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });             

            });
        </script>
        
</html>
    </body>
