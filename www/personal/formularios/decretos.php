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
       
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Snombre = utf8_encode($_SESSION['USU_NOM']);
        $SapellidoP = utf8_encode($_SESSION['USU_APP']);
        $SapellidoM = utf8_encode($_SESSION['USU_APM']);
        $Semail = utf8_encode($_SESSION['USU_MAIL']);
        $Scargo = utf8_encode($_SESSION['USU_CAR']);
        $Sestablecimiento = utf8_encode($_SESSION['EST_ID']);
        $Sdependencia = $_SESSION['USU_DEP'];
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 29;
        $ipcliente = getRealIP();
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
                    //VALIDO QUE NO EXISTA DECRETO EN CREACION
                    $consultaEncreacion = "SELECT DF_ID FROM DECRETOS_FOR WHERE (DF_ESTA = 'EN CREACION')";
                    $respuestaEnCreacion = mysqli_query($cnn, $consultaEncreacion);
                    if (mysqli_num_rows($respuestaEnCreacion) == 0){
                        //usuario no tiene ningun folio tomado
                        $consultaNuevoId = "SELECT DF_ID FROM DECRETOS_FOR ORDER BY DF_ID DESC";
                        $respuestaNuevoId = mysqli_query($cnn, $consultaNuevoId);
                        $AñoActual = date("Y");
                        if (mysqli_num_rows($respuestaNuevoId) == 0){
                            $NuevoID = 1;
                            $FolioUno = "INSERT INTO DECRETOS_FOR (DF_ID,DF_ESTA) VALUES ($NuevoID,'EN CREACION')";
                            mysqli_query($cnn, $FolioUno);
                        }else{
                            $rowNuevoId = mysqli_fetch_row($respuestaNuevoId);
                            $UltimoID = $rowNuevoId[0];
                            $NuevoID = $UltimoID + 1;
                            $FolioUno = "INSERT INTO DECRETOS_FOR (DF_ID,DF_ESTA) VALUES ($NuevoID,'EN CREACION')";
                            mysqli_query($cnn, $FolioUno);
                        }
                    }else{
                        $rowFolioUsado = mysqli_fetch_row($respuestaEnCreacion);
                        $NuevoID = $rowFolioUsado[0];
                    }
                    //rescato valores post
                    $doc_id = $_POST['documento'];
                    $fecha_ini = $_POST['fecha_inicio'];
                    $fecha_fin = $_POST['fecha_fin'];
                    $dependencia = $_POST['lugar_trabajo'];
                    if($dependencia == 0){
                      $dependencia = $_SESSION['DEP'];  
                    }
                    //veo a cual tabla se hara la consulta
                    switch($doc_id){
                      case 1:
                        //feriado legal
                        $query = "SELECT SOL_PERMI.SP_ID,SOL_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC,'%d-%m-%Y') FROM SOL_PERMI INNER JOIN USUARIO ON SOL_PERMI.USU_RUT = USUARIO.USU_RUT WHERE (SOL_PERMI.SP_FEC_INI BETWEEN '$fecha_ini' AND '$fecha_fin') AND (SOL_PERMI.SP_DECRE = 'NO') AND (SOL_PERMI.DOC_ID = $doc_id) AND (SOL_PERMI.SP_ESTA = 'AUTORIZADO DIR') ORDER BY SOL_PERMI.SP_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
                        $num_registros = mysqli_num_rows($respuesta);
                        break;
                      case 2:
                        //dia administrativo
                        $query = "SELECT SOL_PERMI.SP_ID,SOL_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC,'%d-%m-%Y') FROM SOL_PERMI INNER JOIN USUARIO ON SOL_PERMI.USU_RUT = USUARIO.USU_RUT WHERE (SOL_PERMI.SP_FEC_INI BETWEEN '$fecha_ini' AND '$fecha_fin') AND (SOL_PERMI.SP_DECRE = 'NO') AND (SOL_PERMI.DOC_ID = $doc_id) AND (SOL_PERMI.SP_ESTA = 'AUTORIZADO DIR') ORDER BY SOL_PERMI.SP_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
                        $num_registros = mysqli_num_rows($respuesta);
                        break;
                      case 3:
                        //descanso complementario
                        $query = "SELECT SOL_PERMI.SP_ID,SOL_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,SOL_PERMI.SP_CANT_DIA,SOL_PERMI.SP_CANT_DC,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),SOL_PERMI.SP_HOR_INI,DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_HOR_FIN FROM SOL_PERMI INNER JOIN USUARIO ON SOL_PERMI.USU_RUT = USUARIO.USU_RUT WHERE (SOL_PERMI.SP_FEC_INI BETWEEN '$fecha_ini' AND '$fecha_fin') AND (SOL_PERMI.SP_DECRE = 'NO') AND (SOL_PERMI.DOC_ID = $doc_id) AND (SOL_PERMI.SP_ESTA = 'AUTORIZADO DIR') ORDER BY SOL_PERMI.SP_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
                        $num_registros = mysqli_num_rows($respuesta);
                        break;
											case 4:
												$query = "SELECT S.SPR_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SPR_NDIA,DATE_FORMAT(S.SPR_FEC_INI, '%d-%m-%Y'),DATE_FORMAT(S.SPR_FEC_FIN, '%d-%m-%Y'),DATE_FORMAT(S.SPR_FEC, '%d-%m-%Y') FROM SOL_PSGR S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE (S.SPR_DECRE = 'NO') AND (S.SPR_ESTA = 'AUTORIZADO DIR SALUD') AND (S.DOC_ID = $doc_id) ORDER BY S.SPR_FEC ASC";
												$respuesta = mysqli_query($cnn,$query);
                        $num_registros = mysqli_num_rows($respuesta);
												break;
                      case 5:
                        //orden de trabajo extraordinario
												$query = "SELECT OT.OE_ID,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,OT.OE_DC_DIU,OT.OE_DC_NOC,OT.OE_CANT_DC,OT.OE_FEC FROM OT_EXTRA OT, USUARIO U, ESTABLECIMIENTO E WHERE (OT.USU_RUT = U.USU_RUT) AND (U.EST_ID = E.EST_ID) AND (OT.OE_FEC BETWEEN '$fecha_ini' AND '$fecha_fin') AND (OT.OE_DECRE = 'NO') AND (OT.DOC_ID = 5) AND (OT.OE_ESTA = 'V.B. DIR SALUD') ORDER BY U.EST_ID,OT.OE_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
												$num_registros = mysqli_num_rows($respuesta);
                        break;
                      case 6:
                        //acumulacion de feriado legal
                        $Actual = date("Y");
                        $Actual = $Actual - 1;
                        if($dependencia == 1){
                          //ACA ENTRA LOS MULTI ESTABLECIMIENTO - FARMACIA POPULAR - DPTO
                          $query = "SELECT S.SAF_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CAT,S.SAF_CANT_DIA FROM SOL_ACU_FER S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE S.SAF_ESTA = 'AUTORIZADO DIR' AND ((U.EST_ID = 1) OR (U.EST_ID = 10) OR (U.EST_ID = 9999)) AND S.SAF_ANO_ACT = $Actual AND S.SAF_DECRE != 'SI' ORDER BY U.USU_CAT ASC";
                          $respuesta = mysqli_query($cnn,$query);
												  $num_registros = mysqli_num_rows($respuesta);
                          $_SESSION['DEP'] = $dependencia;
                        }elseif($dependencia == 2){
                          $query = "SELECT S.SAF_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CAT,S.SAF_CANT_DIA FROM SOL_ACU_FER S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE S.SAF_ESTA = 'AUTORIZADO DIR' AND U.EST_ID = 2 AND S.SAF_ANO_ACT = $Actual AND S.SAF_DECRE != 'SI' ORDER BY U.USU_CAT ASC";
                          //$query = "SELECT S.SAF_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CAT,S.SAF_CANT_DIA FROM SOL_ACU_FER S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE S.SAF_ESTA = 'AUTORIZADO DIR' AND ((U.EST_ID = 2) OR (U.EST_ID = 1) OR (U.EST_ID = 3)) AND S.SAF_ANO_ACT = $Actual AND S.SAF_DECRE != 'SI' ORDER BY U.USU_CAT ASC";
                          $respuesta = mysqli_query($cnn,$query);
												  $num_registros = mysqli_num_rows($respuesta);
                          $_SESSION['DEP'] = $dependencia;
                        }elseif($dependencia == 3){
                          $query = "SELECT S.SAF_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CAT,S.SAF_CANT_DIA FROM SOL_ACU_FER S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE S.SAF_ESTA = 'AUTORIZADO DIR' AND ((U.EST_ID = 3) OR (U.EST_ID = 4) OR (U.EST_ID = 5) OR (U.EST_ID = 6) OR (U.EST_ID = 7) OR (U.EST_ID = 8) OR (U.EST_ID = 9)) AND S.SAF_ANO_ACT = $Actual AND S.SAF_DECRE != 'SI' ORDER BY U.USU_CAT ASC";
                          $respuesta = mysqli_query($cnn,$query);
						  $num_registros = mysqli_num_rows($respuesta);
                          $_SESSION['DEP'] = $dependencia;
                        }elseif($dependencia == 4){
                          $query = "SELECT S.SAF_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CAT,S.SAF_CANT_DIA FROM SOL_ACU_FER S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE S.SAF_ESTA = 'AUTORIZADO DIR' AND U.EST_ID = 10001 AND S.SAF_ANO_ACT = $Actual AND S.SAF_DECRE != 'SI' ORDER BY U.USU_CAT ASC";
						  echo '<script>';
						  echo 'console.log('. json_encode( $query ) .')';
						  echo '</script>'; 
						  $respuesta = mysqli_query($cnn,$query);
						  $num_registros = mysqli_num_rows($respuesta);
                          $_SESSION['DEP'] = $dependencia;
                        }
                        break;
                      case 8:
                        //cometido funcionario
												$query = "SELECT CP.CO_ID,CP.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,U.USU_CAT,E.EST_NOM,CP.CO_DIA,CP.CO_VIA,CP.CO_PAS,CP.CO_COM,CP.CO_PEA,CP.CO_PAR,CP.CO_MOT,CP.CO_DES FROM COME_PERMI CP INNER JOIN USUARIO U ON CP.USU_RUT = U.USU_RUT LEFT JOIN ESTABLECIMIENTO E ON U.EST_ID = E.EST_ID WHERE (CP.CO_FEC BETWEEN '$fecha_ini' AND '$fecha_fin') AND (CP.CO_DECRE = 'NO') AND (CP.DOC_ID = 8) AND (CP.CO_ESTA = 'AUTORIZADO DIR') ORDER BY U.EST_ID,CP.CO_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
												$num_registros = mysqli_num_rows($respuesta);
                        break;
												
                    }
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
        <title>Version desarrollo - Personal Salud</title>
        <meta charset="UTF-8">
        <!-- Le decimos al navegador que nuestra web esta optimizada para moviles -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <!-- Cargamos el CSS --> 
        <link type="text/css" rel="stylesheet" href="../../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../../include/css/materialize.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../../include/css/custom.css" />
        <link type="text/css" rel="stylesheet" href="../../include/css/materialize.clockpicker.min.css" />
        <link href="../../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <style type="text/css">
            body{
                background-image: url("../../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }
						td,th{
								text-align: center;
						}
        </style>
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            })
            function Cargar(){
                $("#fecha_inicio").attr("disabled","disabled");
                $("#fecha_fin").attr("disabled","disabled"); 
                $("#guardar").attr("disabled","disabled"); 
                $('#lugar_trabajo').formSelect('destroy');
            }
						function Documento(){
							var tipo = $("#documento").val();
							if(tipo == 4){
								$("#fecha_inicio").attr("disabled","disabled");
                $("#fecha_fin").attr("disabled","disabled"); 
								$("#guardar").removeAttr("disabled");
              }else if(tipo == 6){
                $('#lugar_trabajo').formSelect();
                $("#fecha_inicio").attr("disabled","disabled");
								$("#fecha_fin").attr("disabled","disabled"); 
                $("#guardar").removeAttr("disabled");
							}else{
								$("#fecha_inicio").removeAttr("disabled");
								$("#fecha_fin").attr("disabled","disabled"); 
                $("#guardar").attr("disabled","disabled"); 
							}
						}
						function MostrarFechaFin(){
							$("#fecha_fin").removeAttr("disabled");
						}
						function MostrarBuscar(){
							$("#guardar").removeAttr("disabled");
						}
						function ModificoViatico(id){
							var id = id;
							var input = "#viatico"+id;
							var value = $(input).val();
							if(value == "SI"){
								//quito el viatico y cambio value del imput a no
								var nuevo = "NO";
								var post = $.post("../php/actualizo_come_permi.php", { "id" : id, "value" : nuevo }, null, 'json');
								$(input).val(nuevo);
							}else{
								//agrego el viatico y cambio value del imput a si
								var nuevo = "SI";
								var post = $.post("../php/actualizo_come_permi.php", { "id" : id, "value" : nuevo }, null, 'json');
								$(input).val(nuevo);
							}
						}
						function ModificoPorcentaje(id,cont){
							var id = id;
							var cont = cont;
							var ident = id+"-"+cont;
							var dia = "#dia"+ident;
							var hi = "#hi"+ident;
							var hf = "#hf"+ident;
							var porcentaje = "#porcentaje"+ident;
							var value_porc = $(porcentaje).val();
							var value_dia = $(dia).val(); 
							var value_hi = $(hi).val();
							var value_hf = $(hf).val();
							if(value_porc == "40%"){
								//cambiar a 100%
								var nuevo = "100%";
								var post = $.post("../php/actualizo_come_detalle.php", { "id" : id, "dia" : value_dia , "hi" : value_hi, "hf" : value_hf ,"porcentaje" : nuevo }, null, 'json');
								$(porcentaje).val(nuevo);
							}else if(value_porc == "100%"){
								//cambiar a nada
								var nuevo = "";
								var post = $.post("../php/actualizo_come_detalle.php", { "id" : id, "dia" : value_dia , "hi" : value_hi, "hf" : value_hf ,"porcentaje" : nuevo }, null, 'json');
								$(porcentaje).val(nuevo);
							}else{
								//cambiar a %40
								var nuevo = "40%";
								var post = $.post("../php/actualizo_come_detalle.php", { "id" : id, "dia" : value_dia , "hi" : value_hi, "hf" : value_hf ,"porcentaje" : nuevo }, null, 'json');
								$(porcentaje).val(nuevo);
							}
						}
						function MostrarDecreto(s){
							var vistos = "El D.F.L N° 1-3063 de 1980 del Ministerio del Interior;  El Articulo Nº 17 de La Ley 19.378, Estatuto de Atención Primaria de Salud Municipal; la Resolución N° 520 de 1996, de la Contraloría General de la República; y en uso de las facultades que me confiere la Ley N° 18.695, Orgánica Constitucional de Municipalidades, texto refundido por el D.F.L  N° 1, del 09 de Mayo de 2006, del Ministerio del Interior, publicado en el Diario Oficial del 26 de Julio de 2006; y sus posteriores modificaciones.";
							var considerando = "Que,don o doña : "+s.usu_nom+" "+s.usu_app+" "+s.usu_apm+",   RUT N° "+s.usu_rut+", "+s.usu_prof+" del "+s.est_nom+", ha presentado solicitud  para hacer uso de permiso sin goce de remuneraciones desde el "+s.fec_ini+"  al "+s.fec_fin+",    autorizada por la Directora  del "+s.est_nom+" y el Director del Depto. de Salud Municipal.";
							var decreto = "Autorizase a la funcionaria doña: "+s.usu_nom+" "+s.usu_app+" "+s.usu_apm+", RUT N° "+s.usu_rut+", "+s.usu_prof+" del "+s.est_nom+", Categoría "+s.usu_cat+",    para hacer uso de permiso sin goce de remuneraciones desde el "+s.fec_ini+"  al "+s.fec_fin+".-";
							$("textarea#vistos").html(vistos);
							$("textarea#considerando").html(considerando);
							$("textarea#decreto").html(decreto);
							$("#spr_id").val(s.id);
							$("#spr_fec").val(s.spr_fec);
						}
            function BuscarDatos(id){
                var id = id;
								//Materialize.toast(id, 4000);
								var post = $.post("../php/buscar_spr.php", { "id" : id }, MostrarDecreto, 'json');
            }
						function SubDirector(){
							if( $('#dic_sub').prop('checked') ) {
									//SI ES SUBROGANTE
									var dic = "(S)";
									$("#df_dir_sub").val(dic);
							}else{
									var dic = "";
									$("#df_dir_sub").val(dic);
							}
						}
						function SubSecretaria(){
							if( $('#sec_sub').prop('checked') ) {
									//SI ES SUBROGANTE
									var sec = "(S)";
									$("#df_sec_sub").val(sec);
							}else{
									var sec = "";
									$("#df_sec_sub").val(sec);
							}
						}
            function soloLetras(e){
               key = e.keyCode || e.which;
               tecla = String.fromCharCode(key).toLowerCase();
               letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
               especiales = "8-37-39-46";
               tecla_especial = false
               for(var i in especiales){
                    if(key == especiales[i]){
                        tecla_especial = true;
                        break;
                    }
                }
                if(letras.indexOf(tecla)==-1 && !tecla_especial){
                    return false;
                }
            }
            function soloNumeros(e){
                var key = window.Event ? e.which : e.keyCode
                return (key >= 48 && key <= 57 || key == 127 || key == 08)
            }
        </script>
    </head>
    <body onload="Cargar();">
        <!-- llamo el nav que tengo almacenado en un archivo -->
        <?php require_once('../estructura/nav_personal.php');?>
        <!-- inicio contenido pagina -->
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">Decretos Masivos</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post" action="decretos.php">
                                </br></br></br>
                                <div class='col s4'>Favor indicar el tipo de documento que se decretara : </div>
                                </br>
                                <div class="input-field col s12">
                                  <select name="documento" id="documento" onchange="Documento();"><!--  onchange="MostrarFechaInicio();" -->
                                    <option value='no' selected disabled>Tipo de Documento</option>
                                    <option value='1'>Permiso Feriado Legal</option>
                                    <option value='2'>Permiso Administrativo</option>
                                    <option value='3'>Permiso Descanso Complementario</option>
																		<option value='4'>Permiso Sin Goce de Sueldo</option>
                                    <option value='5'>Orden de Trabajo Extraordinario</option>
                                    <option value='8'>Cometido Funcionario</option>
                                    <option value='6'>Acumulacion de Feriado Legal</option>
                                  </select>
                                </div>
                                </br></br></br>
                                <div class="input-field col s12" >
                                  <select name="lugar_trabajo" id="lugar_trabajo">
                                    <option value="NO" selected>SELECCIONE ESTABLECIMIENTO</option>
                                    <option value="1">DPTO DE SALUD</option>
                                    <option value="2">CESFAM RENGO</option>
                                    <option value="3">CESFAM ROSARIO</option>
                                    <option value="4">CESFAM ORIENTE</option>
                                  </select>
                                  <!-- <label for="lugar_trabajo">Dependencia</label> -->
                                </div>
                                </br></br></br>
                                <div class='col s3'>Favor indicar rango de Fecha : </div>
                                <div class="input-field col s3">
                                  <input type="text" class="datepicker" name="fecha_inicio" id="fecha_inicio" onchange="MostrarFechaFin();" placeholder="Fecha Inicio" value="<?php echo date_format($fecha_ini,'Y-m-d');?>" required> 
                                </div> 
                                <div class="input-field col s3">
                                  <input type="text" class="datepicker" name="fecha_fin" id="fecha_fin" onchange="MostrarBuscar();" placeholder="Fecha Fin" value="<?php echo date_format($fecha_fin,'Y-m-d');?>" required> 
                                </div>
                                <div class="input-field col s3">
                                   <button id="guardar" type="submit" class="btn trigger" name="guardar" value="Guardar" >Buscar</button>
                                </div>
                            </form>
                        </div>
                        <div class="row">
                          <?php
														//echo $query;
                            if($num_registros != 0){
                              $registros = "si";
                              if($doc_id == 1 || $doc_id == 2){
                                echo "<form name='form_reg' class='col s12' method='post'>";
                                  echo "<div class='col s12'>Periodo a decretar : $fecha_ini - $fecha_fin</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" name="num_decre" id="num_decre" class="validate" size="10" maxlength="4" required onkeypress="return soloNumeros(event)">';
																			echo "<label for='num_decre'>Numero Decreto :</label>";
																	echo "</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" class="datepicker" name="fec_decre" id="fec_decre" required>';
																			echo "<label for='fec_decre'>Fecha Decreto</label>";
																	echo "</div>";
																	$considerando = "•	 Las presentes solicitudes autorizada por el Jefe Directo y/o Director.";
																	$vistos = "•	 Ley 19.378, Estatuto de Atención Primaria de Salud Municipal; Resolución N° 520, del 15711/96 de la Contraloría General de la República, la Ley 18.695 Orgánica Constitucional de Municipalidades, y su textp refundido fijado por el DFL 1-19.704, del 27/01/01 del Ministerio del Interior, Código del Trabajo y Contratos Prestacion de Servicios a Honorarios suscritos."; 
																	$decreto = "•	 Autorizase a la persona individualizado (a) en la presente solicitud, para hacer uso del derecho indicado en las condiciones y fechas señaladas.";
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="vistos" name="vistos" class="materialize-textarea">'.$vistos.'</textarea>';
          														echo '<label for="vistos">VISTOS</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="considerando" name="considerando" class="materialize-textarea">'.$considerando.'</textarea>';
          														echo '<label for="considerando">Considerando</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="decreto" name="decreto" class="materialize-textarea">'.$decreto.'</textarea>';
          														echo '<label for="decreto">Decreto</label>';
        													echo '</div>';
																	echo "<div class='col s4'>Listado de documentos a decretar : </div>";
																	echo '<table class="responsive-table boradered">';
																		echo '<thead>';
																			echo '<tr>';
																				echo '<th>CREADO</th>';
																				echo '<th>RUT</th>';
																				echo '<th>FUNCIONARIO</th>';
																				echo '<th>CANT. DIAS</th>';
																				echo '<th>INICIO</th>';
																				echo '<th>TERMINO</th>';
																			echo '</tr>';
																			echo '<tbody>';
																				while ($row = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
																					$funcionario = $row[2]." ".$row[3]." ".$row[4];
																					echo '<tr>';
																						echo '<td>'.$row[8].'</td>';
																						echo '<td>'.$row[1].'</td>';
																						echo '<td>'.utf8_encode($funcionario).'</td>';
																						echo '<td>'.$row[5].'</td>';
																						echo '<td>'.$row[6].'</td>';
																						echo '<td>'.$row[7].'</td>';
																					echo '</tr>';
																				}
																			echo '</tbody>';
																		echo '</thead>';
																	echo '</table>';
																	echo "</br>";
																	echo "</br>";
																	$fin = "ANOTESE, TRANSCRIBASE, COMUNIQUESE Y ARCHIVESE";
																	echo '<div class="input-field col s12">';
																		echo '<textarea id="fin_decreto" name="fin_decreto" class="materialize-textarea">'.$fin.'</textarea>';
																		echo '<label for="fin_decreto">Fin Decreto</label>';
																	echo '</div>';
																	echo "</br>";
                                  echo '<div class="input-field col s7">';
																			$director = "PABLO VILLANUEVA GALAZ";
																			echo '<input value="'.$director.'" id="director" type="text" class="validate" name="director" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                      echo '<label for="director">Favor indicar Director de Salud o Subrogante si corresponde (NOMBRE COMPLETO) :</label>';
                                  echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" onchange="SubDirector();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_alc" id="gen_alc" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="DIRECTOR">DIRECTOR</option>';
																			echo '<option value="DIRECTORA">DIRECTORA</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" style="display: none">';
																	$secretaria = "GERALDINE MONTOYA MEDINA";
																	echo '<div class="input-field col s7">';
                                		echo '<input type="text" name="secretaria" id="secretaria" class="validate" value="'.$secretaria.'" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                		echo '<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>';
                            			echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub" onchange="SubSecretaria();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_sec" id="gen_sec" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="SECRETARIA">SECRETARIA</option>';
																			echo '<option value="SECRETARIO">SECRETARIO</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_sec_sub" name="df_sec_sub" class="validate" style="display: none">';
																	echo '<div class="input-field col s12">';
																		$responsables = "ARG/PVG/PGC/MPP/mpp";
																		echo '<input id="responsables" type="text" name="responsables" class="validate" value="'.$responsables.'" required>';
                                		echo '<label for="secretaria">Indique Responbles del decreto(INICIALES) :</label>';
                            			echo '</div>';
																	echo '<input type="text" id="fecha_inicio" name="fecha_inicio" class="validate" value="'.$fecha_ini.'" style="display: none">';
																	echo '<input type="text" id="fecha_fin" name="fecha_fin" class="validate" value="'.$fecha_fin.'" style="display: none">';
																	echo '<input type="text" id="documento" name="documento" class="validate" value="'.$doc_id.'" style="display: none">';
																	echo '<div class="col s12">';
																		echo '<button id="enviar" type="submit" class="btn trigger" name="enviar" value="Guardar" >Guardar</button>';
																	echo '</div>';
																echo "</form>";
															}
                              if($doc_id == 3){
                                //tabla para descanso complementario
                                echo "<form name='form_reg' class='col s12' method='post'>";
                                  echo "<div class='col s12'>Periodo a decretar : $fecha_ini - $fecha_fin</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" name="num_decre" id="num_decre" class="validate" placeholder="Numero Decreto" size="10" maxlength="4" required onkeypress="return soloNumeros(event)">';
																			echo "<label for='num_decre'>Numero Decreto :</label>";
																	echo "</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" class="datepicker" name="fec_decre" id="fec_decre" required>';
																			echo "<label for='fec_decre'>Fecha Decreto</label>";
																	echo "</div>";
																	$considerando = "•	 Las presentes solicitudes autorizada por el Jefe Directo y/o Director.";
																	$vistos = "•	 Ley 19.378, Estatuto de Atención Primaria de Salud Municipal; Resolución N° 520, del 15711/96 de la Contraloría General de la República, la Ley 18.695 Orgánica Constitucional de Municipalidades, y su textp refundido fijado por el DFL 1-19.704, del 27/01/01 del Ministerio del Interior, Código del Trabajo y Contratos Prestacion de Servicios a Honorarios suscritos."; 
																	$decreto = "•	 Autorizase a la persona individualizado (a) en la presente solicitud, para hacer uso del derecho indicado en las condiciones y fechas señaladas.";
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="vistos" name="vistos" class="materialize-textarea">'.$vistos.'</textarea>';
          														echo '<label for="vistos">VISTOS</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="considerando" name="considerando" class="materialize-textarea">'.$considerando.'</textarea>';
          														echo '<label for="considerando">Considerando</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="decreto" name="decreto" class="materialize-textarea">'.$decreto.'</textarea>';
          														echo '<label for="decreto">Decreto</label>';
        													echo '</div>';
                                  echo "<div class='col s4'>Listado de documentos a decretar : </div>";
                                  echo '<table class="responsive-table boradered">';
                                    echo '<thead>';
                                        echo '<tr>';
                                          echo '<th>RUT</th>';
                                          echo '<th>FUNCIONARIO</th>';
                                          echo '<th>DIAS</th>';
                                          echo '<th>HORAS</th>';
                                          echo '<th>FEC. INI.</th>';
                                          echo '<th>HORA</th>';
                                          echo '<th>FEC. FIN.</th>';
                                          echo '<th>HORA</th>';
                                        echo '</tr>';
                                        echo '<tbody>';
                                          while ($row = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
                                            $funcionario = $row[2]." ".$row[3]." ".$row[4];
                                            echo '<tr>';
                                              echo '<td>'.$row[1].'</td>';
                                              echo '<td>'.utf8_encode($funcionario).'</td>';
                                              echo '<td>'.$row[5].'</td>';
                                              echo '<td>'.$row[6].'</td>';
                                              echo '<td>'.$row[7].'</td>';
                                              echo '<td>'.$row[8].'</td>';
                                              echo '<td>'.$row[9].'</td>';
                                              echo '<td>'.$row[10].'</td>';
                                            echo '</tr>';
                                          }
                                        echo '</tbody>';
                                    echo '</thead>';
                                  echo '</table>';
                                  echo "</br>";
                                  echo "</br>";
																	$fin = "ANOTESE, TRANSCRIBASE, COMUNIQUESE Y ARCHIVESE";
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="fin_decreto" name="fin_decreto" class="materialize-textarea">'.$fin.'</textarea>';
          														echo '<label for="fin_decreto">Fin Decreto</label>';
        													echo '</div>';
                                  echo "</br>";
                                  echo '<div class="input-field col s7">';
																			$director = "PABLO VILLANUEVA GALAZ";
																			echo '<input value="'.$director.'" id="director" type="text" class="validate" name="director" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                      echo '<label for="director">Favor indicar Director de Salud o Subrogante si corresponde (NOMBRE COMPLETO) :</label>';
                                  echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" onchange="SubDirector();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_alc" id="gen_alc" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="DIRECTOR">DIRECTOR</option>';
																			echo '<option value="DIRECTORA">DIRECTORA</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" style="display: none">';
																	$secretaria = "GERALDINE MONTOYA MEDINA";
																	echo '<div class="input-field col s7">';
                                		echo '<input type="text" name="secretaria" id="secretaria" class="validate" value="'.$secretaria.'" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                		echo '<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>';
                            			echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub" onchange="SubSecretaria();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_sec" id="gen_sec" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="SECRETARIA">SECRETARIA</option>';
																			echo '<option value="SECRETARIO">SECRETARIO</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_sec_sub" name="df_sec_sub" class="validate" style="display: none">';
																	echo '<div class="input-field col s12">';
																		$responsables = "ARG/PVG/PGC/MPP/mpp";
																		echo '<input id="responsables" type="text" name="responsables" class="validate" value="'.$responsables.'" required>';
                                		echo '<label for="secretaria">Indique Responbles del decreto(INICIALES) :</label>';
                            			echo '</div>';
																	echo '<input type="text" id="fecha_inicio" name="fecha_inicio" class="validate" value="'.$fecha_ini.'" style="display: none">';
																	echo '<input type="text" id="fecha_fin" name="fecha_fin" class="validate" value="'.$fecha_fin.'" style="display: none">';
																	echo '<input type="text" id="documento" name="documento" class="validate" value="'.$doc_id.'" style="display: none">';
                                  echo '<div class="col s12">';
                                    echo '<button id="enviar" type="submit" class="btn trigger" name="enviar" value="Guardar" >Guardar</button>';
                                  echo '</div>';
                                echo "</form>";
                              }
															if($doc_id == 4){
                                //tabla para sin gose de remuneracion
                                echo "<form name='form_reg' class='col s12' method='post'>";
                                  echo "<div class='col s12'>Cantidad sin decretar a la fecha: $num_registros</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" name="num_decre" id="num_decre" class="validate" size="10" maxlength="4" required onkeypress="return soloNumeros(event)">';
																			echo "<label for='num_decre'>Numero Decreto :</label>";
																	echo "</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" class="datepicker" name="fec_decre" id="fec_decre" required>';
																			echo "<label for='fec_decre'>Fecha Decreto</label>";
																	echo "</div>";
                                  echo "<div class='col s4'>Seleccione un permiso para decretar : </div>";
                                  echo '<table class="responsive-table boradered">';
                                    echo '<thead>';
                                        echo '<tr>';
																					echo '<th></th>';
                                          echo '<th>RUT</th>';
                                          echo '<th>FUNCIONARIO</th>';
                                          echo '<th>DIAS</th>';
                                          echo '<th>FEC. INI.</th>';
                                          echo '<th>FEC. FIN.</th>';
																					echo '<th>FEC SOLICITADO</th>';
                                        echo '</tr>';
                                        echo '<tbody>';
                                          while ($row = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
                                            $funcionario = $row[2]." ".$row[3]." ".$row[4];
                                            echo '<tr>';
																							//echo '<td><input class="with-gap" name="permiso" value="'.$row[0].'" type="radio" id="per'.$row[0].'" onclick="BuscarDatos('.$row[0].');"/><label for="per'.$row[0].'">'.$row[0].'</label></td>';
                                              echo '<td><label><input name="permiso" value="'.$row[0].'" type="radio" id="per'.$row[0].'" onclick="BuscarDatos('.$row[0].');"/><span>'.$row[0].'</span></label></td>';
                                              echo '<td>'.$row[1].'</td>';
                                              echo '<td>'.utf8_encode($funcionario).'</td>';
                                              echo '<td>'.$row[5].'</td>';
                                              echo '<td>'.$row[6].'</td>';
                                              echo '<td>'.$row[7].'</td>';
                                              echo '<td>'.$row[8].'</td>';
                                            echo '</tr>';
                                          }
                                        echo '</tbody>';
                                    echo '</thead>';
                                  echo '</table>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="vistos" name="vistos" class="materialize-textarea"></textarea>';
          														echo '<label for="vistos">VISTOS</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="considerando" name="considerando" class="materialize-textarea"></textarea>';
          														echo '<label for="considerando">Considerando</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="decreto" name="decreto" class="materialize-textarea"></textarea>';
          														echo '<label for="decreto">Decreto</label>';
        													echo '</div>';
                                  echo "</br>";
                                  echo "</br>";
																	$fin = "ANOTESE, TRANSCRIBASE, COMUNIQUESE Y REGISTRESE EN LA PAGINA SIAPER DE LA CONTRALORIA GENERAL DE LA REPUBLICA.";
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="fin_decreto" name="fin_decreto" class="materialize-textarea">'.$fin.'</textarea>';
          														echo '<label for="fin_decreto">Fin Decreto</label>';
        													echo '</div>';
                                  echo "</br>";
                                  echo '<div class="input-field col s7">';
																			$director = "CARLOS SOTO GONZALEZ";
																			echo '<input value="'.$director.'" id="director" type="text" class="validate" name="director" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                      echo '<label for="director">Favor indicar Director de Salud o Subrogante si corresponde (NOMBRE COMPLETO) :</label>';
                                  echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" onchange="SubDirector();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_alc" id="gen_alc" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="ALCALDE">ALCALDE</option>';
																			echo '<option value="ALCALDESA">ALCALDESA</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" style="display: none">';
																	$secretaria = "GERALDINE MONTOYA MEDINA";
																	echo '<div class="input-field col s7">';
                                		echo '<input type="text" name="secretaria" id="secretaria" class="validate" value="'.$secretaria.'" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                		echo '<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>';
                            			echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub" onchange="SubSecretaria();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_sec" id="gen_sec" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="SECRETARIA">SECRETARIA</option>';
																			echo '<option value="SECRETARIO">SECRETARIO</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_sec_sub" name="df_sec_sub" class="validate" style="display: none">';
																	echo '<div class="input-field col s12">';
																		$responsables = "ARG/PVG/PGC/MPP/mpp";
																		echo '<input id="responsables" type="text" name="responsables" class="validate" value="'.$responsables.'" required>';
                                		echo '<label for="secretaria">Indique Responbles del decreto(INICIALES) :</label>';
                            			echo '</div>';
																	echo '<input type="text" id="spr_id" name="spr_id" class="validate" style="display: none">';
																	echo '<input type="text" id="spr_fec" name="spr_fec" class="validate" style="display: none">';
																	echo '<input type="text" id="documento" name="documento" class="validate" value="'.$doc_id.'" style="display: none">';
                                  echo '<div class="col s12">';
                                    echo '<button id="enviar" type="submit" class="btn trigger" name="enviar" value="Guardar" >Guardar</button>';
                                  echo '</div>';
                                echo "</form>";
															}
                              if($doc_id == 5){
                                //tabla para descanso complementario
                                echo "<form name='form_reg' class='col s12' method='post'>";
                                  echo "<div class='col s12'>Periodo a decretar : $fecha_ini - $fecha_fin</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" name="num_decre" id="num_decre" class="validate" size="10" maxlength="4" required onkeypress="return soloNumeros(event)">';
																			echo "<label for='num_decre'>Numero Decreto :</label>";
																	echo "</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" class="datepicker" name="fec_decre" id="fec_decre" required>';
																			echo "<label for='fec_decre'>Fecha Decreto</label>";
																	echo "</div>";
																	$vistos = "•	El  D.F.L. Nº 1-3063 de 1981;  Lo establecido en la Ley Nº 19.378 del 15 de Abril de 1995, Artículo 4º inciso 1º, Estatuto de Atención Primaria de Salud Municipal y;  lo establecido en los Art.  63º al 66º y 70º de la Ley Nº 18.883 de 1989, sobre Estatuto Administrativo  para funcionarios Municipales.\n•	El Decreto Nº 662, del 16 de Junio de 1992 publicado en el diario oficial del 27 de Agosto de 1992, que fijó el Texto Refundido de la Ley Nº 18.695, Orgánica Constitucional de Municipalidades;";
																	$considerando = "•	Decreto Alcaldicio Nº 320 de fecha 06 Octubre 1998, que delega en los Directores Municipales la facultad para autorizar trabajos extraordinarios al personal de su dependencia.\n•	Decreto Alcaldicio N° 02 y 03 de fecha 03 Enero 2017, que autoriza la ejecución de trabajos extraordinarios del personal del Cesfam Rosario y Departamento de Salud.\n•	Órdenes de trabajos extraordinarios  de los funcionarios del Cesfam Rosario y Departamento de Salud.";
																	$decreto = "\n1.	AUTORIZANSE Y CANCÉLENSE,  horas extraordinarias efectuadas en el  mes Mayo, Junio, Julio,  Agosto y Septiembre 2017 a los funcionarios que a continuación se individualiza, dependientes  del Centro de Salud Familiar de  Rosario y  Departamento de Salud Municipal de la Comuna de Rengo.\n2.	AUTORIZANSE a los siguientes funcionarios para hacer uso de “Descanso Complementario”, según el siguiente detalle:";
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="vistos" name="vistos" class="materialize-textarea">'.$vistos.'</textarea>';
          														echo '<label for="vistos">VISTOS</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="considerando" name="considerando" class="materialize-textarea">'.$considerando.'</textarea>';
          														echo '<label for="considerando">Considerando</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="decreto" name="decreto" class="materialize-textarea">'.$decreto.'</textarea>';
          														echo '<label for="decreto">Decreto</label>';
        													echo '</div>';
																	//echo $query;
                                  echo "<div class='col s4'>Listado de documentos a decretar : </div>";
																	//consultas segun orden
																	//horas compensadas
																	$queryHC = "SELECT OT.OE_ID,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,OT.OE_DC_DIU,OT.OE_DC_NOC,OT.OE_CANT_DC,OT.OE_FEC FROM OT_EXTRA OT, USUARIO U, ESTABLECIMIENTO E WHERE (OT.USU_RUT = U.USU_RUT) AND (U.EST_ID = E.EST_ID) AND (OT.OE_FEC BETWEEN '$fecha_ini' AND '$fecha_fin') AND (OT.OE_DECRE = 'NO') AND (OT.DOC_ID = 5) AND (OT.OE_ESTA = 'V.B. DIR SALUD') AND ( OT.OE_CANT_DC > 0) ORDER BY U.EST_ID,OT.OE_FEC ASC";
																	$respuestaHC = mysqli_query($cnn,$queryHC);
                                  echo '<table class="responsive-table boradered">';
                                    echo '<thead>';
                                        echo '<tr>';
																					echo '<th>ID</th>';
                                          echo '<th>RUT</th>';
                                          echo '<th>FUNCIONARIO</th>';
                                          echo '<th>ESTABLECIMIENTO</th>';
                                          echo '<th>HRS.DIURNA DES.COMPLE</th>';
																					echo '<th>HRS. SAB - DOM - FEST DES.COMPLE.</th>';
                                          echo '<th>TOTAL HRS. DES.COMPLE.</th>';
                                        echo '</tr>';
                                        echo '<tbody>';
                                          while ($rowHC = mysqli_fetch_array($respuestaHC, MYSQLI_NUM)){
                                            $funcionarioHC = $rowHC[2]." ".$rowHC[3]." ".$rowHC[4];
                                            echo '<tr>';
																							echo '<td>'.$rowHC[0].'</td>';
                                              echo '<td>'.$rowHC[1].'</td>';
                                              echo '<td>'.utf8_encode($funcionarioHC).'</td>';
                                              echo '<td>'.utf8_encode($rowHC[5]).'</td>';
																							echo '<td>'.$rowHC[6].'</td>';
                                              echo '<td>'.$rowHC[7].'</td>';
                                              echo '<td><b>'.$rowHC[8].'</b></td>';
                                            echo '</tr>';
                                          }
                                        echo '</tbody>';
                                    echo '</thead>';
                                  echo '</table>';
																	//horas canceladas sin cargo a programa
																	$queryCSP = "SELECT OT.OE_ID,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,OT.OE_CC_DIU,OT.OE_CC_NOC,OT.OE_CANT_CANCE,OT.OE_FEC FROM OT_EXTRA OT, USUARIO U,ESTABLECIMIENTO E WHERE (OT.USU_RUT = U.USU_RUT) AND (U.EST_ID = E.EST_ID) AND (OT.OE_FEC BETWEEN '$fecha_ini' AND '$fecha_fin') AND (OT.OE_DECRE = 'NO') AND (OT.DOC_ID = 5) AND (OT.OE_ESTA = 'V.B. DIR SALUD') AND (OT.OE_CANT_CANCE > 0) AND (OE_PROGRAMA = 0) ORDER BY U.EST_ID,OT.OE_FEC ASC";
																	$respuestaCSP = mysqli_query($cnn,$queryCSP);
                                  echo '<table class="responsive-table boradered">';
                                    echo '<thead>';
                                        echo '<tr>';
																					echo '<th>ID</th>';
                                          echo '<th>RUT</th>';
                                          echo '<th>FUNCIONARIO</th>';
                                          echo '<th>ESTABLECIMIENTO</th>';
                                          echo '<th>HRS.DIURNA CANCELADAS</th>';
																					echo '<th>HRS. SAB - DOM - FEST CANCELADAS</th>';
                                          echo '<th>TOTAL HRS. CANCELADAS</th>';
                                        echo '</tr>';
                                        echo '<tbody>';
                                          while ($rowCSP = mysqli_fetch_array($respuestaCSP, MYSQLI_NUM)){
                                            $funcionarioCSP = $rowCSP[2]." ".$rowCSP[3]." ".$rowCSP[4];
                                            echo '<tr>';
																							echo '<td>'.$rowCSP[0].'</td>';
                                              echo '<td>'.$rowCSP[1].'</td>';
                                              echo '<td>'.utf8_encode($funcionarioCSP).'</td>';
                                              echo '<td>'.utf8_encode($rowCSP[5]).'</td>';
																							echo '<td>'.$rowCSP[6].'</td>';
                                              echo '<td>'.$rowCSP[7].'</td>';
                                              echo '<td><b>'.$rowCSP[8].'</b></td>';
                                            echo '</tr>';
                                          }
                                        echo '</tbody>';
                                    echo '</thead>';
                                  echo '</table>';
																	//RECORRO TABLA OTE_PROGRAMA MENOS ID 0
																	$query_programa = "SELECT OP_ID,OP_NOM FROM OTE_PROGRAMA P WHERE (OP_ID != 0) AND (OP_ESTA = 'ACTIVO') ORDER BY EST_ID ASC";
																	$respuesta_programa = mysqli_query($cnn,$query_programa);
																	while ($row_pro = mysqli_fetch_array($respuesta_programa, MYSQLI_NUM)){
																		$queryPRO = "SELECT OT.OE_ID,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,OT.OE_CC_DIU,OT.OE_CC_NOC,OT.OE_CANT_CANCE,OT.OE_FEC FROM OT_EXTRA OT, USUARIO U,ESTABLECIMIENTO E WHERE (OT.USU_RUT = U.USU_RUT) AND (U.EST_ID = E.EST_ID) AND (OT.OE_FEC BETWEEN '$fecha_ini' AND '$fecha_fin') AND (OT.OE_DECRE = 'NO') AND (OT.DOC_ID = 5) AND (OT.OE_ESTA = 'V.B. DIR SALUD') AND (OT.OE_CANT_CANCE > 0) AND (OE_PROGRAMA = $row_pro[0]) ORDER BY U.EST_ID,OT.OE_FEC ASC";
																		$respuesta_pro = mysqli_query($cnn,$queryPRO);
																		if(mysqli_num_rows($respuesta_pro) != 0){
																			echo '<table class="responsive-table boradered">';
																				echo '<thead>';
																					echo '<tr>';
																						echo '<th>'.utf8_encode($row_pro[1]).'</th>';
																					echo '</tr>';
																					echo '<tr>';
																						echo '<th>ID</th>';
																						echo '<th>RUT</th>';
																						echo '<th>FUNCIONARIO</th>';
																						echo '<th>ESTABLECIMIENTO</th>';
																						echo '<th>HRS.DIURNA CANCELADAS</th>';
																						echo '<th>HRS. SAB - DOM - FEST CANCELADAS</th>';
																						echo '<th>TOTAL HRS. CANCELADAS</th>';
																					echo '</tr>';
																					echo '<tbody>';
																						while ($rowPG = mysqli_fetch_array($respuesta_pro, MYSQLI_NUM)){
																							$funcionarioPG = $rowPG[2]." ".$rowPG[3]." ".$rowPG[4];
																							echo '<tr>';
																								echo '<td>'.$rowPG[0].'</td>';
																								echo '<td>'.$rowPG[1].'</td>';
																								echo '<td>'.utf8_encode($funcionarioPG).'</td>';
																								echo '<td>'.utf8_encode($rowPG[5]).'</td>';
																								echo '<td>'.$rowPG[6].'</td>';
																								echo '<td>'.$rowPG[7].'</td>';
																								echo '<td><b>'.$rowPG[8].'</b></td>';
																							echo '</tr>';
																						}
																					echo '</tbody>';
																				echo '</thead>';
																			echo '</table>';
																		}
																	}
                                  echo "</br>";
                                  echo "</br>";
																	$fin = "Impútese la cuenta 2101004005-2102004005 “Trabajos extraordinarios” de los funcionarios 	incorporados a la gestión Municipal.-\n\n	ANÓTESE, TRANSCRÍBASE Y ARCHÍVESE.";
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="fin_decreto" name="fin_decreto" class="materialize-textarea">'.$fin.'</textarea>';
          														echo '<label for="fin_decreto">Fin Decreto</label>';
        													echo '</div>';
                                  echo "</br>";
                                  echo '<div class="input-field col s7">';
																			$director = "PABLO VILLANUEVA GALAZ";
																			echo '<input value="'.$director.'" id="director" type="text" class="validate" name="director" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                      echo '<label for="director">Favor indicar Director de Salud o Subrogante si corresponde (NOMBRE COMPLETO) :</label>';
                                  echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" onchange="SubDirector();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_alc" id="gen_alc" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="DIRECTOR">DIRECTOR</option>';
																			echo '<option value="DIRECTORA">DIRECTORA</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" style="display: none">';
																	$secretaria = "GERALDINE MONTOYA MEDINA";
																	echo '<div class="input-field col s7">';
                                		echo '<input type="text" name="secretaria" id="secretaria" class="validate" value="'.$secretaria.'" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                		echo '<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>';
                            			echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub" onchange="SubSecretaria();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_sec" id="gen_sec" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="SECRETARIA">SECRETARIA</option>';
																			echo '<option value="SECRETARIO">SECRETARIO</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_sec_sub" name="df_sec_sub" class="validate" style="display: none">';
																	echo '<div class="input-field col s12">';
																		$responsables = "ARG/PVG/PGC/MPP/mpp";
																		echo '<input id="responsables" type="text" name="responsables" class="validate" value="'.$responsables.'" required>';
                                		echo '<label for="secretaria">Indique Responbles del decreto(INICIALES) :</label>';
                            			echo '</div>';
																	echo '<input type="text" id="fecha_inicio" name="fecha_inicio" class="validate" value="'.$fecha_ini.'" style="display: none">';
																	echo '<input type="text" id="fecha_fin" name="fecha_fin" class="validate" value="'.$fecha_fin.'" style="display: none">';
																	echo '<input type="text" id="documento" name="documento" class="validate" value="'.$doc_id.'" style="display: none">';
                                  echo '<div class="col s12">';
                                    echo '<button id="enviar" type="submit" class="btn trigger" name="enviar" value="Guardar" >Guardar</button>';
                                  echo '</div>';
                                echo "</form>";
                              }
                              if($doc_id == 6){
                                echo "<form name='form_reg' class='col s12' method='post'>";
                                  if($dependencia == 1){
                                    echo "<div class='col s12'>Periodo a decretar : $Actual - Dependencia : DPTO DE SALUD</div>";
                                  }elseif($dependencia == 2){
                                    echo "<div class='col s12'>Periodo a decretar : $Actual - Dependencia : CESFAM RENGO</div>";
                                  }elseif($dependencia == 3){
                                    echo "<div class='col s12'>Periodo a decretar : $Actual - Dependencia : CESFAM ROSARIO</div>";
                                  }
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" name="num_decre" id="num_decre" class="validate" placeholder="Numero Decreto" size="10" maxlength="4" required onkeypress="return soloNumeros(event)">';
																			echo "<label for='num_decre'>Numero Decreto :</label>";
																	echo "</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" class="datepicker" name="fec_decre" id="fec_decre" placeholder="Fecha Decreto" size="10" required>';
																			echo "<label for='fec_decre'>Fecha Decreto</label>";
																	echo "</div>";
                                  $Siguiente = $Actual + 1;
                                  if($dependencia == 1){
                                    $considerando = "•	 Solicitud de acumulación de Feriado Legal presentada por los funcionarios del Departamento  de Salud de Rengo que a continuación se detallan.";
                                    $decreto = "•	 Autorícese la acumulación para el año $Siguiente del Feriado Legal de los funcionarios que a continuación se detalla, dependientes del Depto. Salud.";
                                  }elseif($dependencia == 2){
                                    $considerando = "•	 Solicitud de acumulación de Feriado Legal presentada por los funcionarios del Centro de Salud Familiar de Rengo que a continuación se detallan.";
                                    $decreto = "•	 Autorícese la acumulación para el año $Siguiente del Feriado Legal de los funcionarios que a continuación se detalla, dependientes del Centro de Salud Familiar de Rengo";
                                  }elseif($dependencia == 3){
                                    $considerando = "•	 Solicitud de acumulación de Feriado Legal presentada por los funcionarios del Centro de Salud Familiar de Rosario que a continuación se detallan.";
                                    $decreto = "•	 Autorícese la acumulación para el año $Siguiente del Feriado Legal de los funcionarios que a continuación se detalla, dependientes del Centro de Salud Familiar de Rosario";
                                  }
																	$vistos = "•	 El artículo Nº 18 de la Ley Nº 19.378, Estatuto de Atención Primaria de Salud Municipalizada.
•	 La Resolución Nº 1600 de 2008, de la Contraloría  General de la República y, en uso de las facultades que me confiere la Ley 18.695, Orgánica Constitucional de Municipalidades y sus modificaciones, texto refundido por el D.F. L. Nº 1 del 08 de Mayo de 2006, del Ministerio del Interior, Publicado en el Diario Oficial del 26 de Julio de 2006.-"; 
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="vistos" name="vistos" class="materialize-textarea">'.$vistos.'</textarea>';
          														echo '<label for="vistos">VISTOS</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="considerando" name="considerando" class="materialize-textarea">'.$considerando.'</textarea>';
          														echo '<label for="considerando">Considerando</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="decreto" name="decreto" class="materialize-textarea">'.$decreto.'</textarea>';
          														echo '<label for="decreto">Decreto</label>';
        													echo '</div>';
																	echo "<div class='col s4'>Listado de documentos a decretar : </div>";
																	echo '<table class="responsive-table boradered">';
																		echo '<thead>';
																			echo '<tr>';
																				echo '<th>ID</th>';
																				echo '<th>RUT</th>';
																				echo '<th>FUNCIONARIO</th>';
																				echo '<th>CATEGORIA</th>';
																				echo '<th>DIAS</th>';
																			echo '</tr>';
																			echo '<tbody>';
																				while ($row = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
																					$funcionario = $row[2]." ".$row[3]." ".$row[4];
																					echo '<tr>';
																						echo '<td>'.$row[0].'</td>';
																						echo '<td>'.$row[1].'</td>';
																						echo '<td>'.utf8_encode($funcionario).'</td>';
																						echo '<td>'.$row[5].'</td>';
																						echo '<td>'.$row[6].'</td>';
																					echo '</tr>';
																				}
																			echo '</tbody>';
																		echo '</thead>';
																	echo '</table>';
																	echo "</br>";
																	echo "</br>";
																	$fin = "El feriado legal podrá ser solicitado en otra fecha del año $Siguiente conforme necesidad funcionaria y funcionamiento del servicio.
                                  
                                  ANOTESE, TRANSCRIBASE, COMUNIQUESE Y ARCHIVESE";
																	echo '<div class="input-field col s12">';
																		echo '<textarea id="fin_decreto" name="fin_decreto" class="materialize-textarea">'.$fin.'</textarea>';
																		echo '<label for="fin_decreto">Fin Decreto</label>';
																	echo '</div>';
																	echo "</br>";
                                  echo '<div class="input-field col s7">';
																			$director = "CARLOS SOTO GONZALEZ";
																			echo '<input value="'.$director.'" id="director" type="text" class="validate" name="director" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                      echo '<label for="director">Favor indicar Director de Salud o Subrogante si corresponde (NOMBRE COMPLETO) :</label>';
                                  echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" onchange="SubDirector();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_alc" id="gen_alc" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="ALCALDE">ALCALDE</option>';
																			echo '<option value="ALCALDESA">ALCALDESA</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" style="display: none">';
																	$secretaria = "GERALDINE MONTOYA MEDINA";
																	echo '<div class="input-field col s7">';
                                		echo '<input type="text" name="secretaria" id="secretaria" class="validate" value="'.$secretaria.'" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                		echo '<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>';
                            			echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub" onchange="SubSecretaria();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_sec" id="gen_sec" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="SECRETARIA">SECRETARIA</option>';
																			echo '<option value="SECRETARIO">SECRETARIO</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_sec_sub" name="df_sec_sub" class="validate" style="display: none">';
																	echo '<div class="input-field col s12">';
																		$responsables = "ARG/PVG/PGC/MPP/mpp";
																		echo '<input id="responsables" type="text" name="responsables" class="validate" value="'.$responsables.'" required>';
                                		echo '<label for="secretaria">Indique Responbles del decreto(INICIALES) :</label>';
                            			echo '</div>';
																	echo '<input type="text" id="fecha_inicio" name="fecha_inicio" class="validate" value="'.$fecha_ini.'" style="display: none">';
																	echo '<input type="text" id="fecha_fin" name="fecha_fin" class="validate" value="'.$fecha_fin.'" style="display: none">';
																	echo '<input type="text" id="documento" name="documento" class="validate" value="'.$doc_id.'" style="display: none">';
																	echo '<div class="col s12">';
																		echo '<button id="enviar" type="submit" class="btn trigger" name="enviar" value="Guardar" >Guardar</button>';
																	echo '</div>';
																echo "</form>";
                              }
                              if($doc_id == 8){
                                //tabla para cometido
                                echo "<form name='form_reg' class='col s12' method='post'>";
                                  echo "<div class='col s12'>Periodo a decretar : $fecha_ini - $fecha_fin</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" name="num_decre" id="num_decre" class="validate" size="10" maxlength="4" required onkeypress="return soloNumeros(event)">';
																			echo "<label for='num_decre'>Numero Decreto :</label>";
																	echo "</div>";
																	echo "<div class='input-field col s6'>";
																			echo '<input type="text" class="datepicker" name="fec_decre" id="fec_decre" required>';
																			echo "<label for='fec_decre'>Fecha Decreto</label>";
																	echo "</div>";
																	$vistos = "•	Lo establecido en e el inciso primero del artículo 4°, de la ley N° 19.378,  el artículo 97 de la ley N° 18.883, Estatuto Administrativo para Funcionarios Municipales, es aplicable al personal de atención primaria de salud municipal.\n•	Lo establecido en el Decreto Alcaldicio N° 845 del 24 de Marzo de 2016,  que delega las facultad en el Director del Departamento de Salud de la I. Municipalidad de Rengo, la  facultad para firmar bajo la fórmula “Por Orden del Sr. Alcalde“, los decreto alcaldicios, relacionados con la autorización de permisos administrativos, feriados Legales y Cometidos funcionarios, del personal del Departamento de Salud.";
																	$considerando = "•	Que el personal de Salud Municipal, ha  realizado  labores inherentes al cargo que ocupa, como  asistencia a reuniones,  capacitaciones,  traslado de personal y/o usuarios,  por un tiempo delimitado, según se informa y ha debido desplazarse dentro o fuera del lugar habitual de trabajo.";
																	$decreto = "•	Autorizase a las personas individualizado (a) en las siguientes solicitudes, para hacer uso del derecho indicado en las condiciones y fechas señaladas, según el siguiente detalle:";
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="vistos" name="vistos" class="materialize-textarea">'.$vistos.'</textarea>';
          														echo '<label for="vistos">VISTOS</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="considerando" name="considerando" class="materialize-textarea">'.$considerando.'</textarea>';
          														echo '<label for="considerando">Considerando</label>';
        													echo '</div>';
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="decreto" name="decreto" class="materialize-textarea">'.$decreto.'</textarea>';
          														echo '<label for="decreto">Decreto</label>';
        													echo '</div>';
																	//echo $query;
                                  echo "<div class='col s4'>Listado de documentos a decretar : </div>";
																	//consultas segun orden
																	//horas compensadas
                                  echo '<table class="responsive-table highlight">';
                                    echo '<thead>';
                                        echo '<tr>';
																					echo '<th>ID</th>';
                                          echo '<th>RUT</th>';
                                          echo '<th>FUNCIONARIO</th>';
																					echo '<th>CATEGORIA</th>';
                                          echo '<th>ESTABLECIMIENTO</th>';
                                          echo '<th>DIAS</th>';
																					echo '<th>VIATICO</th>';
                                          echo '<th>OTROS GASTOS</th>';
                                        echo '</tr>';
                                        echo '<tbody>';
                                          while ($row = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
                                            $funcionario = $row[2]." ".$row[3]." ".$row[4];
                                            echo '<tr bgcolor="#4fc3f7">';
																							$otros_gastos = "";
																							echo '<td>'.$row[0].'</td>';
                                              echo '<td>'.$row[1].'</td>';
                                              echo '<td>'.utf8_encode($funcionario).'</td>';
                                              echo '<td>'.$row[5].'</td>';
																							echo '<td>'.utf8_encode($row[6]).'</td>';
                                              echo '<td>'.$row[7].'</td>';
																							if($row[8] == "on"){ $viatico = "SI";}else{ $viatico = "NO";}
																							echo '<td><input type="text" class="validate" name="viatico'.$row[0].'" id="viatico'.$row[0].'" value="'.$viatico.'"  onclick="ModificoViatico('.$row[0].');" readonly></td>';
																							if($row[9] == "on"){ $otros_gastos = $otros_gastos."-PASAJES";}
																							if($row[10] == "on"){ $otros_gastos = $otros_gastos."-COMBUSTIBLE";}
																							if($row[11] == "on"){ $otros_gastos = $otros_gastos."-PEAJE";}
																							if($row[12] == "on"){ $otros_gastos = $otros_gastos."-PARQUIMETRO";}
																							echo '<td><b>'.$otros_gastos.'</b></td>';
                                            echo '</tr>';
																						echo '<tr bgcolor="#81d4fa">';
																							echo '<td><b>MOTIVO</b></td>';
																							echo '<td>'.utf8_encode($row[13]).'</td>';
																							echo '<td><b>DESTINO :</b></td>';
																							echo '<td>'.utf8_encode($row[14]).'</td>';
																							echo '<td></td>';
																							echo '<td></td>';
																							echo '<td></td>';
																							echo '<td></td>';
																						echo '</tr>';
																						$queryDetalle = "SELECT DATE_FORMAT(CD_DIA, '%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR,CD_DIA FROM COME_DETALLE WHERE CO_ID = $row[0]";
																						$respuesta_detalle = mysqli_query($cnn,$queryDetalle);
																						$num_registros = mysqli_num_rows($respuesta_detalle);
																						if($num_registros != 0){
																							$contador = 1;
																							while ($rowD = mysqli_fetch_array($respuesta_detalle, MYSQLI_NUM)){
																								$identificador = $row[0]."-".$contador;
																								echo '<input type="text" id="dia'.$identificador.'" name="dia'.$identificador.'" class="validate" placeholder="" value="'.$rowD[4].'" style="display: none">';
																								echo '<input type="text" id="hi'.$identificador.'" name="hi'.$identificador.'" class="validate" placeholder="" value="'.$rowD[1].'" style="display: none">';
																								echo '<input type="text" id="hf'.$identificador.'" name="hf'.$identificador.'" class="validate" placeholder="" value="'.$rowD[2].'" style="display: none">';
																								echo '<tr bgcolor="#b3e5fc">';
																									echo '<td><b>DIA</b></td>';
																									echo '<td>'.$rowD[0].'</td>';
																									echo '<td><b>HORA INICIO :</b></td>';
																									echo '<td>'.$rowD[1].'</td>';
																									echo '<td><b>HORA FIN :</b></td>';
																									echo '<td>'.$rowD[2].'</td>';
																									echo '<td><b>PORCENTAJE :</b></td>';
																									echo '<td><input type="text" class="validate" name="porcentaje'.$identificador.'" id="porcentaje'.$identificador.'" value="'.$rowD[3].'"  onclick="ModificoPorcentaje('.$row[0].','.$contador.');" readonly></td>';
																									//echo '<td>'.$rowD[3].'</td>';".$row_rsPJ[0].",".$row_rsPJ[11]."
																								echo '</tr>';
																								$contador = $contador +1;
																							}
																						}
                                          }
                                        echo '</tbody>';
                                    echo '</thead>';
                                  echo '</table>';
                                 echo "</br>";
                                  echo "</br>";
																	$fin = "ANÓTESE, TRANSCRÍBASE Y ARCHÍVESE.";
																	echo '<div class="input-field col s12">';
          														echo '<textarea id="fin_decreto" name="fin_decreto" class="materialize-textarea">'.$fin.'</textarea>';
          														echo '<label for="fin_decreto">Fin Decreto</label>';
        													echo '</div>';
                                  echo "</br>";
                                  echo '<div class="input-field col s7">';
																			$director = "PABLO VILLANUEVA GALAZ";
																			echo '<input value="'.$director.'" id="director" type="text" class="validate" name="director" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                      echo '<label for="director">Favor indicar Director de Salud o Subrogante si corresponde (NOMBRE COMPLETO) :</label>';
                                  echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" onchange="SubDirector();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_alc" id="gen_alc" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="DIRECTOR">DIRECTOR</option>';
																			echo '<option value="DIRECTORA">DIRECTORA</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" style="display: none">';
																	$secretaria = "GERALDINE MONTOYA MEDINA";
																	echo '<div class="input-field col s7">';
                                		echo '<input type="text" name="secretaria" id="secretaria" class="validate" value="'.$secretaria.'" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                		echo '<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>';
                            			echo '</div>';
																	echo '<div class="input-field col s3">';
                                      echo '<label>';
                                        echo '<input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub" onchange="SubSecretaria();"/>';
                                        echo '<span>Subrogante</span>';
                                      echo '</label>';
																	echo '</div>';
																	echo '<div class="input-field col s2">';
																		echo '<select name="gen_sec" id="gen_sec" >';
                                      echo '<option value="no" selected>SELECCIONE</option>';
																			echo '<option value="SECRETARIA">SECRETARIA</option>';
																			echo '<option value="SECRETARIO">SECRETARIO</option>';
																		echo '</select>';
																		echo '<label>Genero</label>';
																	echo '</div>';
																	echo '<input type="text" id="df_sec_sub" name="df_sec_sub" class="validate" style="display: none">';
																	echo '<div class="input-field col s12">';
																		$responsables = "ARG/PVG/PGC/MPP/mpp";
																		echo '<input id="responsables" type="text" name="responsables" class="validate" value="'.$responsables.'" required>';
                                		echo '<label for="secretaria">Indique Responbles del decreto(INICIALES) :</label>';
                            			echo '</div>';
																	echo '<input type="text" id="fecha_inicio" name="fecha_inicio" class="validate" value="'.$fecha_ini.'" style="display: none">';
																	echo '<input type="text" id="fecha_fin" name="fecha_fin" class="validate" value="'.$fecha_fin.'" style="display: none">';
																	echo '<input type="text" id="documento" name="documento" class="validate" value="'.$doc_id.'" style="display: none">';
                                  echo '<div class="col s12">';
                                    echo '<button id="enviar" type="submit" class="btn trigger" name="enviar" value="Guardar" >Guardar</button>';
                                  echo '</div>';
                                echo "</form>";
                              }
                            }
                          ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
							if($_POST['enviar'] == "Guardar"){
								//variables
								if($doc_id == 4){
									$fec_ini = $_POST['spr_fec'];
									$fec_fin = $_POST['spr_fec'];
                }elseif($doc_id == 6){
                  $fec_ini = $fecha;
                  $fec_fin = $fecha;
                }else{
									$fec_ini = $_POST['fecha_inicio'];
									$fec_fin = $_POST['fecha_fin'];
								}
								$df_fec = $_POST['fec_decre'];
								$ano = date("Y");
								$df_num = $_POST['num_decre'];
								$texto_vistos = nl2br($_POST['vistos']);
								$texto_considerando = nl2br($_POST['considerando']);
								$texto_decreto = nl2br($_POST['decreto']);
								$texto_fin = nl2br($_POST['fin_decreto']);
								$usu_rut = $Srut;
								$nom_dir_salud = $_POST['director'];
								$df_dir_sub = $_POST['df_dir_sub'];
								$genero_dir = $_POST['gen_alc'];
								$nom_sec = $_POST['secretaria'];
								$df_sec_sub = $_POST['df_sec_sub'];
								$genero_sec = $_POST['gen_sec'];
								$responsables = nl2br($_POST['responsables']);
								$cnn = ConectarPersonal();
                $Update = "UPDATE DECRETOS_FOR SET DOC_ID = 9,USU_RUT = '$usu_rut',DF_FEC='$df_fec',DF_NUM=$df_num,DF_FEC_INI='$fec_ini',DF_FEC_FIN='$fec_fin',DF_ESTA='CREADO',DF_ANO='$ano',DF_TEXT_VISTOS='$texto_vistos',DF_TEXT_CONSIDERANDO='$texto_considerando',DF_TEXT_DECRETO='$texto_decreto',DF_TEXT_FIN='$texto_fin',DF_NOM_DIR='$nom_dir_salud',DF_DIR_SUB='$df_dir_sub',DF_DIR_GEN='$genero_dir',DF_NOM_SEC='$nom_sec',DF_SEC_SUB='$df_sec_sub',DF_SEC_GEN='$genero_sec',DF_RESPONSABLES='$responsables' WHERE DF_ID = $NuevoID";
								//echo $InsertInto;
								mysqli_query($cnn,$Update);
								//consulto para ver el id agregado
								$df_id = $NuevoID;	
								if($doc_id == 4){
									$id_imp = $_POST['spr_id'];
									mysqli_query($cnn,"INSERT INTO DECRE_DETALLE (DF_ID,DOC_ID,FOLIO_DOC) VALUES ($df_id,$doc_id,$id_imp)");
									mysqli_query($cnn,"UPDATE SOL_PSGR SET 	SPR_DECRE = 'SI' WHERE SPR_ID = $id_imp");
								}else{
									$respuesta = mysqli_query($cnn,$query);
									while ($row_fn = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
										$id_imp = $row_fn[0];
										//echo $id_imp;
										//insert into decre_detalle
										mysqli_query($cnn,"INSERT INTO DECRE_DETALLE (DF_ID,DOC_ID,FOLIO_DOC) VALUES ($df_id,$doc_id,$id_imp)");
										if($doc_id == 1 || $doc_id == 2){
											mysqli_query($cnn,"UPDATE SOL_PERMI SET SP_DECRE = 'SI' WHERE SP_ID = $id_imp");
										}
										if($doc_id == 3){
											mysqli_query($cnn,"UPDATE SOL_PERMI SET SP_DECRE = 'SI' WHERE SP_ID = $id_imp");
										}
										if($doc_id == 5){
											mysqli_query($cnn,"UPDATE OT_EXTRA SET OE_DECRE = 'SI' WHERE OE_ID = $id_imp");
                      //pregunto si id existe en banco de horas
                      $buscar = "SELECT BH_ID FROM BANCO_HORAS WHERE (BH_TIPO = 'INGRESO') AND (BH_ID_ANT = $id_imp)";
                      $respuestaBH = mysqli_query($cnn,$buscar);
                      $rows = mysqli_num_rows($respuestaBH);
                      if ($rows > 0){
                      }else{
                        //rescato los otros datos 
                        $queryHoras ="SELECT OE_ID,USU_RUT,OE_CANT_DC FROM OT_EXTRA WHERE (OE_ID = $id_imp)";
                        $rsH = mysqli_query($cnn, $queryHoras);
                        if (mysqli_num_rows($rsH) != 0){
                          $rowH = mysqli_fetch_row($rsH);
                          if ($rowH[0] == $id_imp){
                            $usu_rutOT = $rowH[1];
                            $oe_cant_dc = $rowH[2];
                          }
                          if($oe_cant_dc > 0){
                            $ingresoHRS = "INSERT INTO BANCO_HORAS (USU_RUT, BH_FEC, BH_TIPO, BH_CANT, BH_SALDO, BH_ID_ANT) VALUES ('$usu_rutOT','$fecha','INGRESO',$oe_cant_dc,$oe_cant_dc,$id_imp)";
                            mysqli_query($cnn, $ingresoHRS);
                            $ingresoHRS;
                          }
                        }
                      }
										}
										if($doc_id == 6){
                      mysqli_query($cnn,"UPDATE SOL_ACU_FER SET SAF_DECRE = 'SI' WHERE SAF_ID = $id_imp");
										}
										if($doc_id == 8){
											mysqli_query($cnn,"UPDATE COME_PERMI SET CO_DECRE = 'SI' WHERE CO_ID = $id_imp");
										}
									}
								}

								//ENVIAR A PDF
								//header("location: ../pdf/decreto_masivo_for.php?id=$df_id");
								echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/pdf/decreto_masivo_for.php?id='.$df_id.'&doc_id='.$doc_id.'" , "_blank")</script>';	
								if($doc_id == 8){
									//pdf para tabla de documentos para pago
								}
								//ENVIO A PDF MASIVO SEGUN TIPO DE DOCUMENTO
								if($doc_id == 1){
									echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/pdf/sol_permi_masivo.php?id='.$df_id.'" , "_blank")</script>';
									echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/csv/csv_feriado_legal.php?id='.$df_id.'" , "_blank")</script>';
								}
								if($doc_id == 2){
									echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/pdf/sol_permi_masivo.php?id='.$df_id.'" , "_blank")</script>';
									echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/csv/csv_administrativo.php?id='.$df_id.'" , "_blank")</script>';
								}
								if($doc_id == 3){
									echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/pdf/sol_permi_masivo.php?id='.$df_id.'" , "_blank")</script>';	
									echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/csv/csv_descanso_complementario.php?id='.$df_id.'" , "_blank")</script>';
								}
								if($doc_id == 4){
									echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/pdf/sin_goce.php?id='.$id_imp.'" , "_blank")</script>';									
								}
								if($doc_id == 5){
									echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/pdf/ot_masivo.php?id='.$df_id.'" , "_blank")</script>';
								}
								if($doc_id == 6){
                  echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/pdf/saf_masivo.php?id='.$df_id.'" , "_blank")</script>';
								}
								if($doc_id == 8){
									echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/pdf/cometido_masivo.php?id='.$df_id.'" , "_blank")</script>';
								}
								?> <script type="text/javascript"> window.location="decretos.php";</script>  <?php
							}
							?>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
    </body>
</html>