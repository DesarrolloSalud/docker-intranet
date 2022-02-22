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
        $Sestablecimiento = ($_SESSION['EST_ID']);
        $Sdependencia = $_SESSION['USU_DEP'];
        $Scategoria = $_SESSION['USU_CAT'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $id_formulario = 38;
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
				$folio = $_GET['folio'];
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
										$query = "SELECT O.OE_ID,O.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,O.OE_TRAB FROM OT_EXTRA O INNER JOIN USUARIO U ON O.USU_RUT = U.USU_RUT WHERE O.OE_ID = $folio AND O.OE_DECRE = 'SI'";
										$respuesta = mysqli_query($cnn,$query);
										$registro = mysqli_num_rows($respuesta);
										$row = mysqli_fetch_row($respuesta);
										$oe_id 		= $row[0];
										$usu_rut 	= utf8_encode($row[1]);
										$usu_nom	= utf8_encode($row[2]);
										$usu_app	= utf8_encode($row[3]);
										$usu_apm	= utf8_encode($row[4]);
										$funcionario = $usu_nom." ".$usu_app." ".$usu_app;
										$oe_trab	= utf8_encode($row[5]);
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
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
            function Cargar(){
                $("#hora_ini").attr("disabled","disabled");
                $("#hora_fin").attr("disabled","disabled");
                $("#guardar").attr("disabled","disabled");
                $("#tdcomplementario").attr("disabled","disabled");
								$("#trabajo").attr("disabled","disabled");
                $("#enviar").attr("disabled","disabled");
            }
						function Activar(cont){
                var contador = cont;
                var oe_id = $("#oe_id").val();
                var id_dia = "#DIA"+contador;
                var id_hi = "#HORA_INI"+contador;
                var id_hf = "#HORA_FIN"+contador;
                var dia = $(id_dia).val();
                var hora_ini = $(id_hi).val();
                var hora_fin = $(id_hf).val();
								var estado = "ACTIVO";
                $.post( "../php/actualizar_detalle.php", { "id" : oe_id, "dia" : dia, "hora_ini" : hora_ini, "hora_fin" : hora_fin, "estado" : estado }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id " + id_nuevo + " nombre formulario " + nombre_nuevo + " estado " + estado_nuevo);
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "edit_ot_extra.php?folio="+oe_id+"&.=";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        //console.log( "La solicitud a fallado: " +  textStatus);
                        window.location = "edit_ot_extra.php?folio="+oe_id+"&.=";
                    }
                });
						}
            function Desactivar(cont){
                var contador = cont;
                var oe_id = $("#oe_id").val();
                var id_dia = "#DIA"+contador;
                var id_hi = "#HORA_INI"+contador;
                var id_hf = "#HORA_FIN"+contador;
                var dia = $(id_dia).val();
                var hora_ini = $(id_hi).val();
                var hora_fin = $(id_hf).val();
								var estado = "INACTIVO";
								console.log("id " + oe_id + " fecha " + dia + " estado " + estado);
                $.post( "../php/actualizar_detalle.php", { "id" : oe_id, "dia" : dia, "hora_ini" : hora_ini, "hora_fin" : hora_fin, "estado" : estado }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id " + id_nuevo + " nombre formulario " + nombre_nuevo + " estado " + estado_nuevo);
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "edit_ot_extra.php?folio="+oe_id+"&.=";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        //console.log( "La solicitud a fallado: " +  textStatus);
                        //window.location = "edit_ot_extra.php?folio="+oe_id+"&.=";
                    }
                });
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
                        <h4 class="light">Visualizador Orden de Trabajo Extraordinario</h4>
												<form name="form" class="col s12" method="GET" action="edit_ot_extra.php">
													  <div class="input-field col s6">
                                <input type="text" name="folio" id="folio" class="validate" placeholder="">
                                <label for="folio">Folio Orden de Trabajo Extraordinario</label>
                            </div> 
														<div class="input-field col s6">
																<button class="btn trigger" name="." id="." type="submit">Buscar</button></td>
														</div>
												</form>
												<?php
													if($registro == 1){
                            echo '<div class="input-field col s6">';
                                echo '<input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" value="'.$funcionario.'" disabled>';
                                echo '<label for="nombre_usuario">Nombre Completo Funcionario</label>';
                            echo '</div>';
                            echo '<div class="input-field col s6">';
                                echo '<input type="text" name="oe_id" id="oe_id" class="validate" value="'.$oe_id.'" disabled>';
                                echo '<label for="oe_id">Folio Documento</label>';
                            echo '</div>';
														echo '<div class="input-field col s12">';
                                echo '<input type="text" name="motivo" id="motivo" class="validate" value="'.$oe_trab.'" disabled>';
                                echo '<label for="motivo">Cumplir el trabajo de</label>';
                            echo '</div>';
                            echo '</br>';
                            echo '</br>';
                            echo '<table class="responsive-table boradered">';
                            		echo '<thead>';
                              	    echo '<tr>';
                                        echo '<th>DIA</th>';
                                        echo '<th>HORA INICIO</th>';
                                        echo '<th>HORA TERMINO</th>';
                                        echo '<th>TIPO</th>';
                                        echo '<th>ACCIONES</th>';
                                    echo '</tr>';
                                    echo '<tbody>';
                                    $Detalle_Ot_extra = "SELECT OE_ID,DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN,OTE_DIA,OTE_TIPO,OTE_ESTA FROM OTE_DETALLE WHERE (OE_ID = $oe_id) ORDER BY OTE_DIA ASC, OTE_TIPO";
                                    $respuesta = mysqli_query($cnn, $Detalle_Ot_extra);
                                    //recorrer los registros
                                    $contador                   = 1;
                                    $hora_diurna                = 0;
                                    $min_diurna                 = 0;
                                    $seg_diurna                 = 0;
                                    $hora_nocturna              = 0;
                                    $min_nocturna               = 0;
                                    $seg_nocturna               = 0;
                                    $hora_diurna_cancelada      = 0;
                                    $min_diurna_cancelada       = 0;
                                    $seg_diurna_cancelada       = 0;
                                    $hora_nocturna_cancelada    = 0;
                                    $min_nocturna_cancelada     = 0;
                                    $seg_nocturna_cancelada     = 0;
                                    while ($row_rs = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
																				echo "<tr>";
																						echo '<td><input type="text" id="DIA'.$contador.'" class="validate" placeholder="" value="'.$row_rs[1].'" style="display: none">'.$row_rs[1].'</td>';
																						echo '<td><input type="text" id="HORA_INI'.$contador.'" class="validate" placeholder="" value="'.$row_rs[2].'" style="display: none">'.$row_rs[2].'</td>';
																						echo '<td><input type="text" id="HORA_FIN'.$contador.'" class="validate" placeholder="" value="'.$row_rs[3].'" style="display: none">'.$row_rs[3].'</td>';
																						echo '<td><input type="text" id="TIPO'.$contador.'" class="validate" placeholder="" value="'.$row_rs[5].'" style="display: none">'.$row_rs[5].'</td>';
																						if($row_rs[6] == "ACTIVO" ){
																							echo "<td><button class='btn trigger' name='desactivar' id='desactivar' type='button' onclick='Desactivar(".$contador.");'>Desactivar</button></td>";
																						}else{
																							echo "<td><button class='btn trigger' name='activar' id='activar' type='button' onclick='Activar(".$contador.");'>Activar</button></td>";
																						}
																				echo "</tr>";
																				if($row_rs[6] == "ACTIVO"){
																						if ($row_rs[5] == "COMPENSADAS"){
																								if (date('w',strtotime($row_rs[4])) == 0){
																										//DIA DOMINGO
																										$HorasDomingo = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																										//list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna);
																										list($horaDom, $minDom, $segDom) = split('[:]', $HorasDomingo);
																										//$hora_nocturna = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
																										$hora_nocturna = $hora_nocturna + $horaDom;
																										$min_nocturna  = $min_nocturna + $minDom;
																										$seg_nocturna  = $seg_nocturna + $segDom;
																								}else{
																										//NO ES DOMINGO
																										if (date('w',strtotime($row_rs[4])) == 6){
																												//DIA SABADO
																												$HorasSabado = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																												//list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna);
																												list($horaSab, $minSab, $segSab) = split('[:]', $HorasSabado);
																												//$hora_nocturna = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
																												$hora_nocturna = $hora_nocturna + $horaSab;
																												$min_nocturna  = $min_nocturna + $minSab;
																												$seg_nocturna  = $seg_nocturna + $segSab;
																										}else{
																												//NO ES SABADO - REVISAR SI ES VERIADO
																												$ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$row_rs[4]."')";
																												$RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
																												if (mysqli_num_rows($RespuestaFeriado) == 0){
																														//NO ES FERIADO - REVISAR SI ES ANTES O DESPUES DE LAS 21 HORAS
																														if ($row_rs[2] < "21:00:00"){
																																if ($row_rs[3] > "21:00:00"){
																																		$HorasDiurnas = date("H:i:s",strtotime("00:00:00")+strtotime("21:00:00")-strtotime($row_rs[2]));
																																		$HorasNocturnas = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime("21:00:00"));
																																		//list($hora1, $minut1, $seg1) = split('[:]', $hora_diurna);
																																		list($horaDiurno, $minDiurno, $segDiurno) = split('[:]', $HorasDiurnas);
																																		//$hora_diurna = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
																																		$hora_diurna = $hora_diurna + $horaDiurno;
																																		$min_diurna  = $min_diurna + $minDiurno;
																																		$seg_diurna  = $seg_diurna + $segDiurno;
																																		//$hora_diurna = $hora_diurna + $HorasDiurnas;
																																		//list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna);
																																		list($horaNoc, $minNoc, $segNoc) = split('[:]', $HorasNocturnas);
																																		//$hora_nocturna = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
																																		//$hora_nocturna = $hora_nocturna + $HorasNocturnas;
																																		$hora_nocturna = $hora_nocturna + $horaNoc;
																																		$min_nocturna  = $min_nocturna + $minNoc;
																																		$seg_nocturna  = $seg_nocturna + $segNoc;
																																}else{
																																		$HorasNormal = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																																		//list($hora1, $minut1, $seg1) = split('[:]', $hora_diurna);
																																		list($horaNormal, $minNormal, $segNormal) = split('[:]', $HorasNormal);
																																		//$hora_diurna = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
																																		$hora_diurna = $hora_diurna + $horaNormal;
																																		$min_diurna  = $min_diurna + $minNormal;
																																		$seg_diurna  = $seg_diurna + $segNormal;
																																}
																														}else{
																																$HorasDia = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																																//list($hora1, $minut1, $seg1) = split('[:]', $hora_nocturna);
																																list($horaNocturna, $minNocturna, $segNocturna) = split('[:]', $HorasDia);
																																//$hora_nocturna = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
																																$hora_nocturna = $hora_nocturna + $horaNocturna;
																																$min_nocturna  = $min_nocturna + $minNocturna;
																																$seg_nocturna  = $seg_nocturna + $segNocturna;
																														}
																												}else{
																														//DIA FERIADO
																														$HorasExtras = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																														//list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna);
																														list($horaFer, $minFer, $segFer) = split('[:]', $HorasExtras);
																														//$hora_nocturna = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
																														$hora_nocturna = $hora_nocturna + $horaFer;
																														$min_nocturna  = $min_nocturna + $minFer;
																														$seg_nocturna  = $seg_nocturna + $segFer;
																												}
																									 }
																								}
																						}else{
																								if (date('w',strtotime($row_rs[4])) == 0){
																										//DIA DOMINGO
																										$HorasDomingo = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																										//list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna_cancelada);
																										list($horaDomCa, $minDomCa, $segDomCa) = split('[:]', $HorasDomingo);
																										//$hora_nocturna_cancelada = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
																										$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaDomCa;
																										$min_nocturna_cancelada = $min_nocturna_cancelada + $minDomCa;
																										$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segDomCa; 
																								}else{
																										//NO ES DOMINGO
																										if (date('w',strtotime($row_rs[4])) == 6){
																												//DIA SABADO
																												$HorasSabado = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																												//list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna_cancelada);
																												list($horaSabCa, $minSabCa, $segSabCa) = split('[:]', $HorasSabado);
																												//$hora_nocturna_cancelada = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
																												$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaSabCa;
																												$min_nocturna_cancelada = $min_nocturna_cancelada + $minSabCa;
																												$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segSabCa; 
																										}else{
																												//NO ES SABADO - REVISAR SI ES VERIADO
																												$ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$row_rs[4]."')";
																												$RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
																												if (mysqli_num_rows($RespuestaFeriado) == 0){
																														//NO ES FERIADO - REVISAR SI ES ANTES O DESPUES DE LAS 21 HORAS
																														if ($row_rs[2] < "21:00:00"){
																																if ($row_rs[3] > "21:00:00"){
																																		$HorasDiurnas = date("H:i:s",strtotime("00:00:00")+strtotime("21:00:00")-strtotime($row_rs[2]));
																																		$HorasNocturnas = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime("21:00:00"));
																																		//list($hora1, $minut1, $seg1) = split('[:]', $hora_diurna_cancelada);
																																		list($horaDiurnoCa, $minDiurnoCa, $segDiurnoCa) = split('[:]', $HorasDiurnas);
																																		$hora_diurna_cancelada = $hora_diurna_cancelada + $horaDiurnoCa;
																																		$min_diurna_cancelada = $min_diurna_cancelada + $minDiurnoCa;
																																		$seg_diurna_cancelada = $seg_diurna_cancelada + $segDiurnoCa;
																																		//$hora_diurna_cancelada = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
																																		//$hora_diurna_cancelada = $hora_diurna_cancelada + $HorasDiurnas;
																																		//list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna_cancelada);
																																		list($horaNocCa, $minNocCa, $segNocCa) = split('[:]', $HorasNocturnas);
																																		$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaNocCa;
																																		$min_nocturna_cancelada = $min_nocturna_cancelada + $minNocCa;
																																		$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segNocCa; 
																																		//$hora_nocturna_cancelada = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
																																		//$hora_nocturna_cancelada = $hora_nocturna_cancelada + $HorasNocturnas;
																																}else{
																																		$HorasNormal = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																																		//list($hora1, $minut1, $seg1) = split('[:]', $hora_diurna_cancelada);
																																		list($horaNormalCa, $minNormalCa, $segNormalCa) = split('[:]', $HorasNormal);
																																		//$hora_diurna_cancelada = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
																																		$hora_diurna_cancelada = $hora_diurna_cancelada + $horaNormalCa;
																																		$min_diurna_cancelada = $min_diurna_cancelada + $minNormalCa;
																																		$seg_diurna_cancelada = $seg_diurna_cancelada + $segNormalCa;
																																}
																														}else{
																																$HorasDia = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																																//list($hora1, $minut1, $seg1) = split('[:]', $hora_nocturna_cancelada);
																																list($horaDiaCa, $minDiaCa, $segDiaCa) = split('[:]', $HorasDia);
																																//$hora_nocturna_cancelada = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
																																$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaDiaCa;
																																$min_nocturna_cancelada = $min_nocturna_cancelada + $minDiaCa;
																																$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segDiaCa; 
																														}
																												}else{
																														//DIA FERIADO
																														$HorasExtras = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
																														//list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna_cancelada);
																														list($horaFerCa, $minFerCa, $segFerCa) = split('[:]', $HorasExtras);
																														//$hora_nocturna_cancelada = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
																														$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaFerCa;
																														$min_nocturna_cancelada = $min_nocturna_cancelada + $minFerCa;
																														$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segFerCa; 
																												}
																										}
																								}
																						}
																				}
																				$contador = $contador + 1;
																		}
																		echo '</tbody>';
																echo '</thead>';
														echo '</table>';
                            echo '</br>';
                            echo '<table class="col s6">';
                                echo '<thead>';
                                    echo '<tr>';
                                        echo '<th>HORAS COMPENSADAS</th>';
                                    echo '</tr>';
                                    //dar formato a hora compensado
                                    //hora diurna
                                    if($min_diurna >= 60){
                                        $hora_diruna_resultado = $min_diurna / 60;
                                        $min_diurna_resultado = $min_diurna % 60;
                                        if($min_diurna_resultado < 10){
                                            $min_diurna_resultado = "0".$min_diurna_resultado;
                                        }
                                        $hora_diurna = (int)$hora_diruna_resultado + $hora_diurna;
                                        $hora_diurna_compensada = $hora_diurna.":".$min_diurna_resultado.":00";
                                    }else{
                                        $hora_diurna_compensada = $hora_diurna.":".$min_diurna.":00";
                                        
                                    }
                                    //hora nocturna
                                    if($min_nocturna >= 60){
                                        $hora_nocturna_resultado = $min_nocturna / 60;
                                        $min_nocturna_resultado = $min_nocturna % 60;
                                        if($min_nocturna_resultado < 10){
                                            $min_nocturna_resultado = "0".$min_nocturna_resultado;
                                        }
                                        $hora_nocturna = (int)$hora_nocturna_resultado + $hora_nocturna;
                                        $hora_nocturna_compensada = $hora_nocturna.":".$min_nocturna_resultado.":00";
                                    }else{
                                        $hora_nocturna_compensada = $hora_nocturna.":".$min_nocturna.":00";
                                    }
                                    echo '<tbody>';
                                    		echo '<tr>';
                                        		echo '<td>Horas Diurnas</td>';
                                        		echo '<td>'.$hora_diurna_compensada.'</td>';
                                        		echo '<td>Al 1.25</td>';
                                        		echo '<td>';
																								$hora_diurna_total = $hora_diurna*1.25;
																								echo (int)$hora_diurna_total." Horas";
                                        		echo '</td>';
                                    		echo '</tr>';
                                    		echo '<tr>';
                                            echo '<td>Horas Nocturnas</td>';
                                        		echo '<td>'.$hora_nocturna_compensada.'</td>';
                                        		echo '<td>Al 1.5</td>';
                                        		echo '<td>';
																								$hora_nocturna_total = $hora_nocturna*1.5;
																								echo (int)$hora_nocturna_total." Horas";
                                        		echo '</td>';
                                    		echo '</tr>';
                                    		echo '<tr>';
                                        		echo '<td></td>';
                                        		echo '<td></td>';
                                        		echo '<td><b>Horas Compensadas :</b></td>';
                                        		echo '<td>';
                                                $TotalHoras = (int)$hora_diurna_total + (int)$hora_nocturna_total;
                                                $TotalHoras = (int)$TotalHoras;
                                                echo '<input type="text" id="totalhorascompensadas" name="totalhorascompensadas" class="validate" placeholder="" value="'.$TotalHoras.'" style="display: none">'.$TotalHoras.' Horas';
                                            echo '</td>';
                                   	 		echo '</tr>';
                                    echo '</tbody>';
                                echo '</thead>';
                            echo '</table>';
                            echo '<table class="col s6">';
                                echo '<thead>';
                                    echo '<tr>';
                                        echo '<th>HORAS CANCELADAS</th>';
                                    echo '</tr>';
                                    //dar formato a hora cancelada
                                    //hora diurna
                                    if($min_diurna_cancelada >= 60){
                                        $hora_diurna_cancelada_resultado = $min_diurna_cancelada / 60;
                                        $min_diurna_cancelada_resultado = $min_diurna_cancelada % 60;
                                        if($min_diurna_cancelada_resultado < 10){
                                            $min_diurna_cancelada_resultado = "0".$min_diurna_cancelada_resultado;
                                        }
                                        $hora_diurna_cancelada = (int)$hora_diurna_cancelada_resultado + $hora_diurna_cancelada;
                                        $hora_diurna_cancelada_final = $hora_diurna_cancelada.":".$min_diurna_cancelada_resultado.":00";
                                    }else{
                                        $hora_diurna_cancelada_final = $hora_diurna_cancelada.":".$min_diurna_cancelada.":00";
                                    }
                                    //hora nocturna
                                    if($min_nocturna_cancelada >= 60){
                                        $hora_nocturna_cancelada_resultado = $min_nocturna_cancelada / 60;
                                        $min_nocturna_cancelada_resultado = $min_nocturna_cancelada % 60;
                                        if($min_nocturna_cancelada_resultado < 10){
                                            $min_nocturna_cancelada_resultado = "0".$min_nocturna_cancelada_resultado;
                                        }
                                        $hora_nocturna_cancelada = (int)$hora_nocturna_cancelada_resultado + $hora_nocturna_cancelada;
                                        $hora_nocturna_cancelada_final = $hora_nocturna_cancelada.":".$min_nocturna_cancelada_resultado.":00";
                                    }else{
                                        $hora_nocturna_cancelada_final = $hora_nocturna_cancelada.":".$min_nocturna_cancelada.":00";
                                    }
                                    echo '<tbody>';
																				echo '<tr>';
																						echo '<td>Horas Diurnas</td>';
																						echo '<td>'.$hora_diurna_cancelada_final.'</td>';
																						echo '<td>Al 1.25</td>';
																						echo '<td>';
																								echo $hora_diurna_cancelada." Horas";
																						echo '</td>';
																				echo '</tr>';
																				echo '<tr>';
																						echo '<td>Horas Nocturnas</td>';
																						echo '<td>'.$hora_nocturna_cancelada_final.'</td>';
																						echo '<td>Al 1.5</td>';
																						echo '<td>';
																								echo $hora_nocturna_cancelada." Horas";
																						echo '</td>';
																				echo '</tr>';
																				echo '<tr>';
																						echo '<td></td>';
																						echo '<td></td>';
																						echo '<td><b>Horas Canceladas :</b></td>';
																						echo '<td>';
																								$TotalHorasCanceladas = $hora_diurna_cancelada + $hora_nocturna_cancelada;
																								echo '<input type="text" id="totalhorascanceladas" name="totalhorascanceladas" class="validate" placeholder="" value="'.$TotalHorasCanceladas.'" style="display: none">'.$TotalHorasCanceladas.' Horas';
																						echo '</td>';
																				echo '</tr>';
                                    echo '</tbody>';
                                echo '</thead>';
                            echo '</table>';
														//BOTON ACTUALIZAR
														echo '<form name="form_actualizar" class="col s12" method="post">';
															echo '<div class="col s12">';
																	echo '<button id="actualizar" type="submit" class="btn trigger" name="actualizar" value="Actualizar" >Actualizar</button>';
															echo '</div>';
														echo '</form>';
													}
												?>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin contenido pagina --> 
				<?php
						if($_POST['actualizar'] == "Actualizar"){
								$oe_cc_diu = $hora_diurna_cancelada;
								$oe_cc_noc = $hora_nocturna_cancelada;
                $oe_cant_cance = $TotalHorasCanceladas;
                $oe_dc_diu = $hora_diurna;
								$oe_dc_noc = $hora_nocturna;
								$oe_cant_dc = $TotalHoras;
								$select = "SELECT OE_CANT_DC FROM OT_EXTRA WHERE OE_ID = $oe_id";
								$query_select = mysqli_query($cnn,$select);
								$row_qs = mysqli_fetch_array($query_select);
								$hr_dc_anterior = $row_qs[0];
								$actualizo_oc = "UPDATE OT_EXTRA SET OE_CC_DIU = $oe_cc_diu,OE_CC_NOC = $oe_cc_noc,OE_CANT_CANCE = $oe_cant_cance,OE_DC_DIU = $oe_dc_diu,OE_DC_NOC = $oe_dc_noc,OE_CANT_DC = $oe_cant_dc WHERE (OE_ID = $oe_id)";
								mysqli_query($cnn, $actualizo_oc);
								$FecActual = date("Y-m-d");
                $HorActual = date("H:i:s");
								$GuaHistoPermiso = "INSERT INTO HISTO_PERMISO (HP_FOLIO,USU_RUT,HP_FEC,HP_HORA,DOC_ID,HP_ACC) VALUES ($oe_id,'$usu_rut','$FecActual','$HorActual',5,'RR.HH MODIFICA POR HORAS MAL INGRESADAS')";
                mysqli_query($cnn, $GuaHistoPermiso);
								//actualizo banco de horas segun modificacion de horas compenzadas
								if($hr_dc_anterior != $oe_cant_dc){
									$query_bh = "SELECT BH_CANT,BH_SALDO,BH_ID FROM BANCO_HORAS WHERE BH_TIPO = 'INGRESO' AND BH_ID_ANT = $oe_id AND USU_RUT = '$usu_rut'";
									$respuesta_bh = mysqli_query($cnn,$query_bh);
									$row_bh = mysqli_fetch_array($respuesta_bh, MYSQLI_NUM);
									$bh_cant 	= $row_bh[0];
									$bh_saldo = $row_bh[1];
									$bh_id		= $row_bh[2];
									$diferencia = $bh_cant - $TotalHoras;
									if($diferencia > $bh_saldo){
										//rescato diferencia entre diferencia menos saldo, actualizo ingreso con nuevo valores y busco otros ingresos para restar la diferencia, en caso que no existan mas registros notificar en correo que sera descontado en proximo orden de trabajo
										$cant_egreso = $diferencia - $bh_saldo;
										$bh_saldo = 0;
										$actualizo_bh = "UPDATE BANCO_HORAS SET BH_CANT = $oe_cant_dc, BH_SALDO = $bh_saldo WHERE BH_ID = $bh_id";
										mysqli_query($cnn,$actualizo_bh);
										list($año_bh, $mes_bh, $dia_bh) = split('[-]', $FecActual);
										$FecIni_bh = ($año_bh - 2)."/".$mes_bh."/".$dia_bh;
										$query_bh = "SELECT BH_ID,BH_SALDO FROM BANCO_HORAS WHERE (USU_RUT = '$usu_rut') AND (BH_SALDO > 0) AND (BH_FEC BETWEEN '$FecIni_bh' AND '$fecha_bh') AND ((BH_TIPO = 'INICIAL') OR (BH_TIPO = 'INGRESO')) ORDER BY BH_FEC ASC";
										$resultado_bh = mysqli_query($cnn, $query_bh);
										if (mysqli_num_rows($resultado_bh) != 0){
											while ($row_bh = mysqli_fetch_array($resultado_bh)){
													$bh_id = $row_bh[0];
													$bh_saldo  = $row_bh[1];
													if ($bh_saldo > $cant_egreso){
															//saldo nuevo a guardar
															$saldo_nuevo = $bh_saldo - $cant_egreso;
															$bh_update = "UPDATE BANCO_HORAS SET BH_SALDO = $saldo_nuevo WHERE BH_ID = $bh_id";
															mysqli_query($cnn,$bh_update);
															$cant_egreso = 0;
															break 1;
													}elseif($bh_saldo == $cant_egreso){
															//saldo nuevo a guardar
															$saldo_nuevo = 0;
															$bh_update = "UPDATE BANCO_HORAS SET BH_SALDO = $saldo_nuevo WHERE BH_ID = $bh_id";
															mysqli_query($cnn,$bh_update);
															$cant_egreso = 0;
															break 1;
													}elseif($bh_saldo < $cant_egreso){
															//UPDATE SALDO A 0 PARA ID SALDO MENOR
															$saldo_nuevo = 0;
															$bh_update = "UPDATE BANCO_HORAS SET BH_SALDO = $saldo_nuevo WHERE BH_ID = $bh_id";
															mysqli_query($cnn,$bh_update);
															$cant_egreso = $cant_egreso - $bh_saldo;
													}
											}
										}
									}elseif($diferencia == $bh_saldo){
										//saldo queda en 0 y se actualiza la nueva cantidad
										$bh_saldo = 0;
										$actualizo_bh = "UPDATE BANCO_HORAS SET BH_CANT = $oe_cant_dc, BH_SALDO = $bh_saldo WHERE BH_ID = $bh_id";
										mysqli_query($cnn,$actualizo_bh);
									}elseif($diferencia < $bh_saldo){
										//se resta la diferencia al saldo y se actualiza nuevo saldo y nueva cantidad
										$bh_saldo = $bh_saldo - $diferencia;
										$actualizo_bh = "UPDATE BANCO_HORAS SET BH_CANT = $oe_cant_dc, BH_SALDO = $bh_saldo WHERE BH_ID = $bh_id";
										mysqli_query($cnn,$actualizo_bh);
									}
								}
								//mando correo avisando cuales son los dias a desactivar
								$DetalleInactivo = "SELECT OE_ID,DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN,OTE_DIA,OTE_TIPO FROM OTE_DETALLE WHERE (OE_ID = $oe_id) AND (OTE_ESTA = 'INACTIVO') ORDER BY OTE_DIA ASC, OTE_TIPO";
                $respuestaInactivo = mysqli_query($cnn, $DetalleInactivo);
								while ($row_ri = mysqli_fetch_array($respuestaInactivo, MYSQLI_NUM)){
									$dia 	 = $row_ri[1];
									$h_ini = $row_ri[2];
									$h_fin = $row_ri[3];
									$h_tip = $row_ri[5];
									$dato = "FECHA : ".$dia."| HORA INICIO : ".$h_ini."| HORA TERMINO : ".$h_fin."| TIPO : ".$h_tip."\r\n\r\n";
									$detalle .= $dato;
								}
								/*$mymail = "soporte.salud.rengo@gmail.com";
								$header = 'From: ' . $mymail . " \r\n";
								$header .= "X-Mailer: PHP/" . phpversion() . " \r\n";
								$header .= "Mime-Version: 1.0 \r\n";
								$header .= "Content-Type: text/plain";
								$mensaje = "Estimad@ ".$funcionario. ":\r\n\r\nSe informa que debido a diferencias con los registros del reloj control se han descontado de sus Formulario de Trabajos Extraordinarios el o los siguientes días :\r\n\r\n\r\n";
								$mensaje .= $detalle;
								$mensaje .= "\r\n\r\nEl nuevo detalle de horas de la Orden de Trabajo Extraordinario Folio ".$oe_id.", es el siguiente :\r\n\r\n";
								$mensaje .= "Horas Diurnas Canceladas : ".$oe_cc_diu." | Horas Nocturnas Canceladas : ".$oe_cc_noc." | Total de Horas Canceladas : ".$oe_cant_cance."\r\n\r\n";
								$mensaje .= "Horas Diurnas Compensadas : ".$oe_dc_diu." | Horas Nocturnas Compensadas : ".$oe_dc_noc." | Total de Horas Compensadas : ".$oe_cant_dc."\r\n\r\n";
								if($cant_egreso > 0){
									//agregar en el mensaje que el proximo orden de trabajo extraordinario se restaran $cant_egreso horas 
									$mensaje .= "Debido a que ud ya ocupó su saldo de horas compensadas y el descuento no se efectuo completamente, se descontarán por sistema : ".$cant_egreso." horas de su siguiente orden de trabajo extraordinario.\r\n\r\n";
								}
								$mensaje .= "Según lo anterior se deberá realizar una nueva Orden de trabajo Extraordinario con las fechas antes mencionadas y se actualizará el saldo de horas compensadas en caso de ser necesario.rnrnPara aclarar cualquier duda comuníquese con RR.HH. del Departamento de Salud al correo: pgomez@munirengo.cl\r\n\r\nSalud a usted atentamente.\r\n";
								$para = 'pgomez@munirengo.cl';
								$asunto = 'FOLIO : '.$oe_id.' | FUNCIONARIO : '.$funcionario;
								mail($para, $asunto, utf8_decode($mensaje), $header);*/
								$file = fopen("archivo.txt", "w");
								fwrite($file, "Estimad@ ".$funcionario. ":Se informa que debido a diferencias con los registros del reloj control se han descontado de sus Formulario de Trabajos Extraordinarios el o los siguientes días :" . PHP_EOL);
								fwrite($file, $detalle . PHP_EOL);
								fwrite($file, "El nuevo detalle de horas de la Orden de Trabajo Extraordinario Folio ".$oe_id.", es el siguiente :" . PHP_EOL);
								fwrite($file, "Horas Diurnas Canceladas : ".$oe_cc_diu." | Horas Nocturnas Canceladas : ".$oe_cc_noc." | Total de Horas Canceladas : ".$oe_cant_cance . PHP_EOL);
								fwrite($file, "Horas Diurnas Compensadas : ".$oe_dc_diu." | Horas Nocturnas Compensadas : ".$oe_dc_noc." | Total de Horas Compensadas : ".$oe_cant_dc . PHP_EOL);
								if($cant_egreso > 0){
									fwrite($file, "Debido a que ud ya ocupó su saldo de horas compensadas y el descuento no se efectuo completamente, se descontarán por sistema : ".$cant_egreso." horas de su siguiente orden de trabajo extraordinario." . PHP_EOL);
								}
								fwrite($file, "Según lo anterior se deberá realizar una nueva Orden de trabajo Extraordinario con las fechas antes mencionadas y se actualizará el saldo de horas compensadas en caso de ser necesario.rnrnPara aclarar cualquier duda comuníquese con RR.HH. del Departamento de Salud al correo: pgomez@munirengo.cl Salud a usted atentamente." . PHP_EOL);
								fclose($file);
								?> <script type="text/javascript">window.location="edit_ot_extra.php";</script>  <?php
						}
				?>
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
    </body>
</html>
