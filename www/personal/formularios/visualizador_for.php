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
        $id_formulario = 39;
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
                    //rescato valores post
                    $año = $_POST['año'];
                    $rut = $_POST['rut'];
                    $documento = $_POST['documento'];
                    switch($documento){
                      case 1:
                        //feriado legal
                        $query = "SELECT SOL_PERMI.SP_ID,SOL_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_ESTA,DOCUMENTO.DOC_NOM,SOL_PERMI.DOC_ID FROM SOL_PERMI INNER JOIN USUARIO ON SOL_PERMI.USU_RUT = USUARIO.USU_RUT INNER JOIN DOCUMENTO ON SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID WHERE (SOL_PERMI.USU_RUT = '$rut') AND (SOL_PERMI.DOC_ID = $documento) AND (SOL_PERMI.SP_ESTA = 'AUTORIZADO DIR') AND (SOL_PERMI.SP_ANO = $año) ORDER BY SOL_PERMI.SP_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
                        $num_registros = mysqli_num_rows($respuesta);
                        break;
                      case 2:
                        //dia administrativo
                        $query = "SELECT SOL_PERMI.SP_ID,SOL_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_ESTA,DOCUMENTO.DOC_NOM,SOL_PERMI.DOC_ID FROM SOL_PERMI INNER JOIN USUARIO ON SOL_PERMI.USU_RUT = USUARIO.USU_RUT INNER JOIN DOCUMENTO ON SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID WHERE (SOL_PERMI.USU_RUT = '$rut') AND (SOL_PERMI.DOC_ID = $documento) AND (SOL_PERMI.SP_ESTA = 'AUTORIZADO DIR') AND (SOL_PERMI.SP_ANO = $año) ORDER BY SOL_PERMI.SP_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
                        $num_registros = mysqli_num_rows($respuesta);
                        break;
                      case 3:
                        //descanso complementario
                        $query = "SELECT SOL_PERMI.SP_ID,SOL_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_ESTA,DOCUMENTO.DOC_NOM,SOL_PERMI.DOC_ID FROM SOL_PERMI INNER JOIN USUARIO ON SOL_PERMI.USU_RUT = USUARIO.USU_RUT INNER JOIN DOCUMENTO ON SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID WHERE (SOL_PERMI.USU_RUT = '$rut') AND (SOL_PERMI.DOC_ID = $documento) AND (SOL_PERMI.SP_ESTA = 'AUTORIZADO DIR') AND (SOL_PERMI.SP_ANO = $año) ORDER BY SOL_PERMI.SP_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
                        $num_registros = mysqli_num_rows($respuesta);
                        break;
											case 4:
												$query = "SELECT S.SPR_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,DATE_FORMAT(S.SPR_FEC_INI, '%d-%m-%Y'),DATE_FORMAT(S.SPR_FEC_FIN, '%d-%m-%Y'),S.SPR_ESTA,DOCUMENTO.DOC_NOM,S.DOC_ID FROM SOL_PSGR S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT INNER JOIN DOCUMENTO ON S.DOC_ID = DOCUMENTO.DOC_ID WHERE (S.USU_RUT = '$rut') AND (S.SPR_ESTA = 'AUTORIZADO DIR SALUD') AND (S.DOC_ID = $documento) AND (S.SPR_ANO = $año) ORDER BY S.SPR_FEC ASC";
												$respuesta = mysqli_query($cnn,$query);
                        $num_registros = mysqli_num_rows($respuesta);
												break;
                      case 5:
                        //orden de trabajo extraordinario
												$query = "SELECT OT.OE_ID,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,DATE_FORMAT(OT.OE_FEC, '%d-%m-%Y'),DATE_FORMAT(OT.OE_FEC, '%d-%m-%Y'),OT.OE_ESTA,D.DOC_NOM,OT.DOC_ID FROM OT_EXTRA OT, USUARIO U, DOCUMENTO D WHERE (OT.USU_RUT = U.USU_RUT) AND (OT.DOC_ID = D.DOC_ID) AND (OT.USU_RUT = '$rut') AND (OT.DOC_ID = $documento) AND (OT.OE_ESTA = 'V.B. DIR SALUD') AND (OT.OE_ANO = $año) ORDER BY U.EST_ID,OT.OE_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
												$num_registros = mysqli_num_rows($respuesta);
                        break;
                      case 6:
                        //acumulacion de feriado legal
                        $query = "SELECT S.SAF_ID,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,DATE_FORMAT(S.SAF_FEC,'%d-%m-%Y'),DATE_FORMAT(S.SAF_FEC,'%d-%m-%Y'),S.SAF_ESTA,D.DOC_NOM,S.DOC_ID FROM SOL_ACU_FER S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT INNER JOIN DOCUMENTO D ON S.DOC_ID = D.DOC_ID WHERE (S.USU_RUT = '$rut') AND (S.DOC_ID = $documento) AND (S.SAF_ANO_ACT = $año) AND (S.SAF_ESTA = 'AUTORIZADO DIR') ORDER BY S.SAF_FEC ASC";
                        $respuesta = mysqli_query($cnn,$query);
												$num_registros = mysqli_num_rows($respuesta);
                        break;
                      case 8:
                        //cometido funcionario
												$query = "SELECT CP.CO_ID,CP.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,DATE_FORMAT(CP.CO_FEC, '%d-%m-%Y'),DATE_FORMAT(CP.CO_FEC, '%d-%m-%Y'),CP.CO_ESTA,D.DOC_NOM,CP.DOC_ID FROM COME_PERMI CP INNER JOIN USUARIO U ON CP.USU_RUT = U.USU_RUT INNER JOIN DOCUMENTO D ON CP.DOC_ID = D.DOC_ID WHERE (CP.USU_RUT = '$rut') AND (CP.DOC_ID = $documento) AND (CP.CO_ANO = $año) AND (CP.CO_ESTA = 'AUTORIZADO DIR') ORDER BY U.EST_ID,CP.CO_FEC ASC";
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
        <script type="text/javascript" src="../../include/js/materialize.clockpicker.min.js"></script>
        <script>
            $(document).ready(function () {
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('#fecha_inicio').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
                $('#fecha_fin').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
            function Cargar(){
                $("#rut").attr("disabled","disabled"); 
                $("#documento").formSelect('destroy');
                $("#buscar").attr("disabled","disabled"); 
            }
            function ValidoAño(){
              $("#rut").removeAttr("disabled");
            }
            function RespuestaUsuario(u){
              var respuesta = u.resultado;
              var rut = u.rut_enviado;
              if(respuesta == 1){
                $("#documento").formSelect();
              }else{
                //usuario no existe
                M.toast({html: 'El usuario Rut :"+rut+" no existe en el sistema'});  
                $("#rut").val("");
                $("#documento").formSelect('destroy');
              }
            }
            function ValidoUsuario(){
              var rut = $("#rut").val();
              //Materialize.toast(rut, 4000);
              var post = $.post("../php/buscar_usuario.php", { "rut_nuevo" : rut }, RespuestaUsuario, 'json');
            }
            function Documento(){
              $("#buscar").removeAttr("disabled");
            }
            function Imprimir(id,doc_id){
              var for_id = id;
              var doc_id = doc_id;
              if(doc_id == 1 || doc_id == 2 || doc_id ==3){
                window.open("http://200.68.34.158/personal/pdf/sol_permi.php?id="+id , "_blank");
              }
              if(doc_id == 4){
                window.open("http://200.68.34.158/personal/pdf/sin_goce.php?id="+id , "_blank");
              }
              if(doc_id == 5){
                window.open("http://200.68.34.158/personal/pdf/ot_extra.php?id="+id , "_blank");
              }
              if(doc_id == 6){
                window.open("http://200.68.34.158/personal/pdf/saf.php?id="+id, "_blank");
              }
              if(doc_id == 8){
                window.open("http://200.68.34.158/personal/pdf/cometido.php?id="+id, "_blank");
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
                        <h4 class="light">Visualizador de Documentos</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post" action="visualizador_for.php">
                                </br></br></br>
                                <div class="input-field col s4">
                                  <input type="text" name="año" id="año" class="validate" placeholder="" maxlength="4" onkeypress="return soloNumeros(event)" onchange="ValidoAño()" required>
                                  <label for='año'>Año</label>
                                </div> 
                                <div class="input-field col s4">
                                  <input type="text" name="rut" id="rut" class="validate" style="text-transform: uppercase" onblur="ValidoUsuario();" required>
                                  <label for='rut'>Rut Funcionario</label>
                                </div> 
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
                                <div class="input-field col s12">
                                   <button id="buscar" type="submit" class="btn trigger" name="buscar" value="Buscar" >Buscar</button>
                                </div>
                            </form>
                        </div>
                        <div class="row">
                          <!-- MUESTRO TRABLA CON DOCUMENTOS-->
                          <?php
                          if($num_registros >= 1){
													  echo '<table class="responsive-table boradered">';
															echo '<thead>';
																echo '<tr>';
                                  echo '<th></th>';
																	echo '<th>FUNCIONARIO</th>';
																	echo '<th>FECHA INICIO</th>';
																	echo '<th>FECHA FIN</th>';
                                  echo '<th>TIPO</th>';
																	echo '<th>ESTADO</th>';
																	echo '<th>ACCIONES</th>';
																echo '</tr>';
																echo '<tbody>';
																  while ($row = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
																	  $funcionario = $row[2]." ".$row[3]." ".$row[4];
																		echo '<tr>';
																			echo '<td></td>';
																			echo '<td>'.utf8_encode($funcionario).'</td>';
																			echo '<td>'.$row[5].'</td>';
																			echo '<td>'.$row[6].'</td>';
                                      echo '<td>'.$row[8].'</td>';
                                      echo '<td>'.$row[7].'</td>';
																			echo "<td><button class='btn trigger' name='decreto' id='decreto' type='button' onclick='Imprimir(".$row[0].",".$row[9].");'>IMPRIMIR</button></td>";
																		echo '</tr>';
                                   }	
																echo '</tbody>';
															echo '</thead>';
														echo '</table>';
                          }elseif($num_registros == 0){
                          }
                          ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $("#rut").Rut({ 
                    on_error: function(){ 
                        M.toast({html: 'Rut incorrecto'});  
                        $("#rut").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                        $("#rut").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });
            });
        </script>
    </body>
</html>