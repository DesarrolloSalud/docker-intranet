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
        $Sestablecimiento = ($_SESSION['EST_ID']);
        $Sdependencia = $_SESSION['USU_DEP'];
        $Scategoria = $_SESSION['USU_CAT'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $id_formulario = 10;
        date_default_timezone_set("America/Santiago");
        $fecha_hoy = date("Y-m-d");
        $hora = date("H:i:s");
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
                    //reviso si tengo algun ot_extra en creacion
                    $consultaEncreacion = "SELECT SP_ID FROM SOL_PERMI WHERE (USU_RUT = '$Srut') AND (SP_ESTA = 'EN CREACION')";
                    $respuestaEnCreacion = mysqli_query($cnn, $consultaEncreacion);
                    if (mysqli_num_rows($respuestaEnCreacion) == 0){
                        //usuario no tiene ningun folio tomado
                        $consultaNuevoId = "SELECT SP_ID FROM SOL_PERMI ORDER BY SP_ID DESC";
                        $respuestaNuevoId = mysqli_query($cnn, $consultaNuevoId);
                        $AñoActual = date("Y");
                        if (mysqli_num_rows($respuestaNuevoId) == 0){
                            $NuevoID = 1;
                            $FolioUno = "INSERT INTO SOL_PERMI (SP_ID,USU_RUT,SP_ESTA,SP_ANO,SP_FEC) VALUES ($NuevoID,'$Srut', 'EN CREACION','$AñoActual','$fecha_hoy')";;
                            mysqli_query($cnn, $FolioUno);
                        }else{
                            $rowNuevoId = mysqli_fetch_row($respuestaNuevoId);
                            $UltimoID = $rowNuevoId[0];
                            $NuevoID = $UltimoID + 1;
                            $FolioUno = "INSERT INTO SOL_PERMI (SP_ID,USU_RUT,SP_ESTA,SP_ANO,SP_FEC) VALUES ($NuevoID,'$Srut', 'EN CREACION','$AñoActual','$fecha_hoy')";;
                            mysqli_query($cnn, $FolioUno);
                        }
                    }else{
                        $rowFolioUsado = mysqli_fetch_row($respuestaEnCreacion);
                        $NuevoID = $rowFolioUsado[0];
                    }  
                }else{
                    //no tengo acceso
                    $accion = utf8_decode("ACCESO DENEGADO");
                    $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha_hoy', '$hora')";
                    mysqli_query($cnn, $insertAcceso);
                    header("location: ../error.php");
                }
            }else{
                //si formulario no activo
                $accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
                $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha_hoy', '$hora')";
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
        <script type="text/javascript" src="../../include/js/moment.js"></script>
				<script type="text/javascript" src="../../include/js/date.format.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
						function sumarDias(fecha, dias){
							fecha.setDate(fecha.getDate() + dias);
							return fecha;
						}
            function Cargar(){
                $('select').formSelect('destroy');
								//check horas 
								$("#horas").attr("disabled","disabled");
								$("#fecha_hora_ini").attr("disabled","disabled");
								$("#hora_ini").attr("disabled","disabled");
								$("#cant_horas").attr("disabled","disabled"); 
								$("#fecha_hora_fin").attr("disabled","disabled");
								$("#hora_fin").attr("disabled","disabled");
								//check medioturno
								$("#medioturno").attr("disabled","disabled");
								$("#fechamediodia_ini").attr("disabled","disabled");
								$("#fechamediodia_fin").attr("disabled","disabled");
								//check dia
								$("#undia").attr("disabled","disabled");
								$("#fecha_dia_ini").attr("disabled","disabled");
								$("#fecha_dia_fin").attr("disabled","disabled");
								//check masdeundia
								$("#masdeuno").attr("disabled","disabled");
                $("#Finicio").attr("disabled","disabled");
                $("#dias").attr("disabled","disabled");
                $("#Ftermino").attr("disabled","disabled");
								//otros campos
                $("#motivo").attr("disabled","disabled");
                $("#guardar").attr("disabled","disabled");
            }
            function Respuesta(r){
                var doc_id = r.doc_id;
                var pendientes = r.pendientes;
                var motivo = r.motivo;
                $("#Tipo_Doc").val(doc_id);
                if (doc_id == 1){ //feriado
									console.log( "respuesta" );
                    if(motivo == "NO"){
											console.log( "NO" );
                        M.toast({html: 'Usted no dispone de dias Feriados Legales'}); 
                        Cargar(); 
                    }else{
											console.log( "SI" );
                        var fl = r.fl; 
                        var fla = r.fla;
                        if(pendientes > 0 && pendientes != null){
                            $("#administrativo").attr("disabled","disabled");
                            $("#descaso").attr("disabled","disabled");
                            $("#AnoSiguiente").attr("disabled","disabled");
                            M.toast({html: 'Usted tiene '+r.pendientes+' dias'}); 
                            $("#dias_pendientes").val(pendientes);
                            $("#fl").val(fl);
                            $("#fla").val(fla);
                            $("#undia").removeAttr("disabled");
                            $("#masdeuno").removeAttr("disabled");
                        }else{
													console.log( "NULL o 0" );
                            M.toast({html: 'Usted no dispone de dias Feriados Legales'}); 
                            Cargar(); 
                        }
                    }
                }else if(doc_id == 2){ //Administrativo
                    if(pendientes == 0 || pendientes == null){
                        M.toast({html: 'Usted no dispone de dias Administrativos'}); 
                        Cargar(); 
                    }else{
                        $("#feriado").attr("disabled","disabled");
                        $("#descaso").attr("disabled","disabled");
                        $("#AnoSiguiente").attr("disabled","disabled");
                        M.toast({html: 'Usted tiene '+r.pendientes+' dias'});
                        $("#dias_pendientes").val(r.pendientes);
                        $("#medioturno").removeAttr("disabled");
                        $("#undia").removeAttr("disabled");
                    }
                }else if(doc_id == 3){ //complementario
                    if(pendientes == 0 || pendientes == null){
                        M.toast({html: 'Usted no dispone de Horas de Descanso Complementario'});
                        Cargar(); 
                    }else if(pendientes >= 1 && pendientes < 12){
                        $("#administrativo").attr("disabled","disabled");
                        $("#feriado").attr("disabled","disabled");
                        $("#AnoSiguiente").attr("disabled","disabled");
                        //mostrar opciones horas - dia - dias
                        $("#horas").removeAttr("disabled");
                    }else if(pendientes >= 12){
                        $("#administrativo").attr("disabled","disabled");
                        $("#feriado").attr("disabled","disabled");
                        $("#AnoSiguiente").attr("disabled","disabled");
												$("#horas").removeAttr("disabled");
												$("#undia").removeAttr("disabled");		 
										}
                }
            }
            function CalcularFeriado(){
                var doc_id = 1;
							console.log( "consulto" );
                var post = $.post("../php/calcular_dias.php", { "doc_id" : doc_id }, Respuesta, 'json');
            }
            function CalcularAnoSiguiente(){
                window.location = "for_feriado_nuevo_ano.php";
            }
            function CalcularAdmin(){
                var doc_id = 2;
                var post = $.post("../php/calcular_dias.php", { "doc_id" : doc_id }, Respuesta, 'json');
            }
            function VerCompezandos(){
                var doc_id = 3;
                var post = $.post("../php/calcular_dias.php", { "doc_id" : doc_id }, Respuesta, 'json');
            }
						//funcion check horas
						function Horas(){
                $("#medioturno").attr("disabled","disabled");
                $("#undia").attr("disabled","disabled");
                $("#masdeuno").attr("disabled","disabled");
                $("#fecha_hora_ini").removeAttr("disabled");
            }
            function ValidarHorasBanco(horBanc){
                var cant_horas_banco = horBanc.horas;
                //var consulta = horBanc.consulta;
                if(cant_horas_banco != 0){
                    M.toast({html: 'Usted tiene '+cant_horas_banco+' horas'});
                    $("#horas_pendientes").val(cant_horas_banco);
                    $("#hora_ini").removeAttr("disabled");
                }else{
                    M.toast({html: 'Usted no dispone de Horas de Descanso Complementario para la fecha seleccionada, cambie la fecha'});
                    $("#hora_ini").attr("disabled","disabled");
                    $("#fecha_hora_ini").val("");
                }
            }
						function MostrarHoraIni(){
								var ValFecha = $("#fecha_hora_ini").val();
                var post = $.post("../php/calcular_horas.php", { "fecha" : ValFecha }, ValidarHorasBanco, 'json');
						}
            function MostrarCantHoras(){
                $("#cant_horas").removeAttr("disabled");
            }
            function CalcularHoraFin(){
                var HorasPendientes = $("#horas_pendientes").val();
                var HorasPedidas = $("#cant_horas").val();
								if(HorasPedidas >= 12){
                  M.toast({html: 'No se puede pedir mas de 11 horas, si necesita 12 seleccione la opcion 1 dia'});
                  $("#cant_horas").val("");
								}else{
									//Materialize.toast(HorasPendientes+' - '+HorasPedidas, 4000);
									if (HorasPedidas > parseInt(HorasPendientes)){
											//borrar cant_horas y mensaje advirtiendo el error
											M.toast({html: 'Las horas solicitadas superan a las acumuladas'});
											$("#cant_horas").val("");
									}else{
											//calcular y mostrar hora fin y mostrar jefatura
											var horaIni = $("#hora_ini").val();
											var ValFecha = $("#fecha_hora_ini").val();
											minutos = horaIni.substr(3,2);
											inicioHoras = parseInt(horaIni.substr(0,2));
											horaFin = inicioHoras + parseInt(HorasPedidas);
											if(horaFin >= 24){
												var d = new Date(ValFecha);
												ValFecha = sumarDias(d, 1);
												ValFecha = dateFormat(ValFecha,"yyyy/mm/dd");
												$("#fecha_hora_fin").val(ValFecha);
												$("#fecha_hora_fin2").val(ValFecha);
											}else{
												$("#fecha_hora_fin").val(ValFecha);
												$("#fecha_hora_fin2").val(ValFecha);
											}
											if(horaFin == 24){
												 horaFin = "00";
											}else if(horaFin > 24){
													horaFin = horaFin - 24;		 
											}
											horas = horaFin.toString();
											MuestroHora = horas+":"+minutos;
											$("#hora_fin").val(MuestroHora);
											$("#hora_fin2").val(MuestroHora);
                      $('#jefatura').formSelect();
									}
								}
            }
						//function check medio turno
            function MedioDia(){
								$("#fechamediodia_ini").removeAttr("disabled");
                $("#horas").attr("disabled","disabled");
                $("#undia").attr("disabled","disabled");
                $("#masdeuno").attr("disabled","disabled");
            }
						function MostrarJornada(){
								var ValFecha = $("#fechamediodia_ini").val();
                var TipoDoc = $("#Tipo_Doc").val();
                if(TipoDoc == 1 || TipoDoc == 2){
                  var año = moment(ValFecha).year();
                  var anoActual = (new Date).getFullYear();
                  if(año == anoActual){
                    $('#jornada').formSelect();
                  }else{
                    M.toast({html: 'La fecha debe ser del año actual'});
                    $("#fechamediodia_ini").val("");
                  }
                }else{
                  $('#jornada').formSelect();
                }
								
						}
						function MostrarFechaFinMedioDia(){
								var jornada = $("#jornada").val();
								var ValFecha = $("#fechamediodia_ini").val();
								if(jornada == "8 AM A 2 PM" || jornada == "2 PM A 8 PM" || jornada == "2 AM A 8 AM"){
									$("#fechamediodia_fin").val(ValFecha);
									$("#fechamediodia_fin2").val(ValFecha);
									$('#jefatura').formSelect();
								}else{
									var d = new Date(ValFecha);
									ValFecha = sumarDias(d, 1);
									ValFecha = dateFormat(ValFecha,"yyyy/mm/dd");
									$("#fechamediodia_fin").val(ValFecha);
									$("#fechamediodia_fin2").val(ValFecha);
									$('#jefatura').formSelect();
								}
						}
						//function check dia
            function UnDia(){
                $("#fecha_dia_ini").removeAttr("disabled");
                $("#horas").attr("disabled","disabled");
                $("#medioturno").attr("disabled","disabled");
                $("#masdeuno").attr("disabled","disabled");
            }
            function ResultadoFechaDia(rfd){
                var FeriadoFecHora = rfd.dia;
                if(FeriadoFecHora == "si"){
                    M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                    $("#fecha_dia_ini").val("");
                }else if(FeriadoFecHora == "no"){
                    $('#jornada_dia').formSelect();
                }
            }
	          function FechaDia(){
								var TipoDoc = $("#Tipo_Doc").val();
								if(TipoDoc == 1){
									var ValFecha = $("#fecha_dia_ini").val();
                  var año = moment(ValFecha).year();
                  var anoActual = (new Date).getFullYear();
                  if(año == anoActual){
                    var post = $.post("../php/revisar_feriado.php", { "fecha" : ValFecha }, ResultadoFechaDia, 'json');
                  }else{
                    M.toast({html: 'La fecha debe ser del año actual'});
                    $("#fecha_dia_ini").val("");
                  }
								}else if(TipoDoc == 2){
									var ValFecha = $("#fecha_dia_ini").val();
                  var año = moment(ValFecha).year();
                  var anoActual = (new Date).getFullYear();
                  if(año == anoActual){
                    $('#jornada_dia').formSelect();
                  }else{
                    M.toast({html: 'La fecha debe ser del año actual'});
                    $("#fecha_dia_ini").val("");
                  }
									
								}else if(TipoDoc == 3){
									$('#jornada_dia').formSelect();
								}
            }
						function MostrarFechaFinDia(){
								var jornada = $("#jornada_dia").val();
								var ValFecha = $("#fecha_dia_ini").val();
								if(jornada == "MAÑANA"){
									$("#fecha_dia_fin").val(ValFecha);
									$("#fecha_dia_fin2").val(ValFecha);
									$('#jefatura').formSelect();
								}else{
									var d = new Date(ValFecha);
									ValFecha = sumarDias(d, 1);
									ValFecha = dateFormat(ValFecha,"yyyy/mm/dd");
									$("#fecha_dia_fin").val(ValFecha);
									$("#fecha_dia_fin2").val(ValFecha);
									$('#jefatura').formSelect();
								}
						}
						//function check mas de un dia
            function MasdeUno(){
                $("#horas").attr("disabled","disabled");
                $("#mediodia").attr("disabled","disabled");
                $("#undia").attr("disabled","disabled");
								var DiasPendientes = $("#dias_pendientes").val();
								if (DiasPendientes > 1){
									$("#dias").removeAttr("disabled");
								}else{
                	//no puede tomarse mas de dos dias
                  M.toast({html: 'Dias insuficientes para tomarse mas de un dias'});
                  $("#formSolPermi").find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
                  $("#feriado").removeAttr("disabled");
                  $("#administrativo").removeAttr("disabled");
                  $("#descaso").removeAttr("disabled");
                  Cargar();
                }
            }
            function MostrarFinicio(){
								var DiasPendientes = $("#dias_pendientes").val();
								var CantDias = $("#dias").val();
                M.toast({html: DiasPendientes+'dias pendientes'});
                M.toast({html: CantDias+'dias pedidos'});
                var dif = DiasPendientes - CantDias;
                if(dif >= 0){
                	//pasar a indicar fecha de inicio
                  $("#Finicio").removeAttr("disabled");
                }else{
                	//no puede tomarse esta cantidad de dias porque el tiene menos
                  var dif = CantDias - DiasPendientes;
                  M.toast({html: dif+' mas de los que puede pedir'});
                  $("#dias").val("");
                }
            }
            function MostrarFechaFIN(mff){
              var fechaFIN = mff.fechaFIN;
              var año = moment(fechaFIN).year();
              var anoActual = (new Date).getFullYear();
              if(año == anoActual){
                $("#Ftermino").val(fechaFIN);
                $("#Ftermino2").val(fechaFIN);
                $('#jefatura').formSelect();
              }else{
                M.toast({html: 'La fecha debe ser del año actual'});
                $("#dias").val("");
                $("#Finicio").attr("disabled","disabled");
                $("#Finicio").val("");
                $("#Ftermino").val("");
                $("#Ftermino2").val("");
              }
						}
	          function SegundaValidacionFechaINI(svfi){
                var FeriadoFecHora = svfi.dia;
                if(FeriadoFecHora == "si"){
                    //tiene que cambiar de dia
                    M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                    $("#Finicio").val("");
                }else if(FeriadoFecHora == "no"){
                	var CantDias = $("#dias").val();
                  var FechaInicio = $("#Finicio").val();
                  var post = $.post("../php/validar_fechaTermino.php", { "TipoDoc" : 1, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN, 'json');
                }
            }
            function ValidoFechaINI(){
                var ValFecha = $("#Finicio").val();
                var post = $.post("../php/revisar_feriado.php", { "fecha" : ValFecha }, SegundaValidacionFechaINI, 'json');
            }
						//funciones 
            function Jefatura(){
                $('#jefatura').formSelect();
            }
            function Motivo(){
                $("#motivo").removeAttr("disabled");
            }
            function Listo(){
                $("#guardar").removeAttr("disabled");
            }
						//otras funciones
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
                        <h4 class="light">Formulario de Solicitud</h4>
                         <form name="form" class="col s12" method="post" id="formSolPermi">
                            </br>
                            </br>
                            <div class="col s3">
                              <label>
                                <input name="seleccion" type="radio" value="1" id="feriado" onclick="CalcularFeriado();"/>
                                <span>Feriado</span>
                              </label>
                            </div>
                            <div class="col s3">
                              <label>
                                <input name="seleccion" type="radio" value="4" id="AnoSiguiente" onclick="CalcularAnoSiguiente();"/>
                                <span>Feriado Año Siguiente</span>
                              </label>
                            </div>
                            <div class="col s3">  
                              <label>
                                <input name="seleccion" type="radio" value="2" id="administrativo" onclick="CalcularAdmin();"/>
                                <span>Permiso Administrativo</span>
                              </label>
                            </div>
                            <div class="col s3">  
                              <label>
                                <input name="seleccion" type="radio" value="3" id="descaso" onclick="VerCompezandos();"/>
                                <span>Descanso Complementario</span>
                              </label>
                            </div>
                            <input type="text" name="Tipo_Doc" id="Tipo_Doc" class="validate" style="display: none">
                            <input type="text" name="fl" id="fl" class="validate" style="display: none">
                            <input type="text" name="fla" id="fla" class="validate" style="display: none">
                            </br>
                            </br>
                            <div class="input-field col s12">
                                <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $Snombre." ".$SapellidoP." ".$SapellidoM;?>" disabled>
                                <label for="nombre_usuario">Nombre Completo Funcionario</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="rut_usuario" id="rut_usuario" class="validate" placeholder="" value="<?php echo $Srut;?>" disabled>
                                <label for="rut_usuario">RUT</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="categoria_usuario" id="categoria_usuario" class="validate" placeholder="" value="<?php echo $Scategoria;?>" disabled>
                                <label for="categoria_usuario">Categoria</label>
                            </div>
                            <div class="col s12" align="left"><h6>Solicita autorización para los siguientes dias:</h6></div>
                            </br>
                            </br>
                            </br>
                            <table class="col 12">
                                <tbody>
                                    <tr>
                                        <td class="cos s3" style="text-align: left;">
                                            <label>
                                              <input name="dia" type="radio" value="horas" id="horas" onclick="Horas();"/>
                                              <span>HORAS</span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" class="datepicker" name="fecha_hora_ini" id="fecha_hora_ini" placeholder="Fecha Inicio" onchange="MostrarHoraIni();" required> 
                                            </div> 
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input id="hora_ini" name="hora_ini" class="timepicker" type="text" placeholder="Hora Inicio"  onchange="MostrarCantHoras()" required>
                                            </div>                                        
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" name="cant_horas" id="cant_horas" class="validate" placeholder=""  onkeypress="return soloNumeros(event)" onchange="CalcularHoraFin()" required>
                                                <input type="text" name="horas_pendientes" id="horas_pendientes" class="validate" style="display: none">
                                                <label for="icon_prefix">Cantidad de Horas</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input id="hora_fin" name="hora_fin" class="timepicker" type="text" placeholder="Hora Termino" required>
                                                <input type="text" name="hora_fin2" id="hora_fin2" class="timepicker" style="display: none">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" class="datepicker" name="fecha_hora_fin" id="fecha_hora_fin" placeholder="Fecha Termino" required> 
																								<input type="text" class="datepicker" name="fecha_hora_fin2" id="fecha_hora_fin2" style="display: none"> 
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;">
                                            <label>
                                              <input name="dia" type="radio" value="1/2" id="medioturno" onclick="MedioDia();"/>
                                              <span>1/2 Turno</span>
                                            </label>
                                        </td>
																				<td>
                                            <div class="input-field col s12">
                                                <input type="text" class="datepicker" name="fechamediodia_ini" id="fechamediodia_ini" placeholder="Fecha Inicio" onchange="MostrarJornada();" required> 
                                            </div>  
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <select name="jornada" id="jornada" onchange="MostrarFechaFinMedioDia();">
                                                    <option value="NO" selected>SELECCIONE TURNO</option>
                                                    <option value="8 AM A 2 PM">8 AM A 2 PM</option>
                                                    <option value="2 PM A 8 PM">2 PM A 8 PM</option>
																										<option value="8 PM A 2 AM">8 PM A 2 AM</option>
                                                    <option value="2 AM A 8 AM">2 AM A 8 AM</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" class="datepicker" name="fechamediodia_fin" id="fechamediodia_fin" placeholder="Fecha Termino" required> 
																								<input type="text" class="datepicker" name="fechamediodia_fin2" id="fechamediodia_fin2" style="display: none"> 
                                            </div>  
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  style="text-align: left;">
                                            <label>
                                              <input name="dia" type="radio" value="1" id="undia" onclick="UnDia();"/>
                                              <span>1 Dia</span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" name="fecha_dia_ini" id="fecha_dia_ini" class="datepicker" placeholder="Fecha Inicio" onchange="FechaDia();" required> 
                                            </div> 
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <select name="jornada_dia" id="jornada_dia" onchange="MostrarFechaFinDia();">
                                                    <option value="NO" selected>SELECCIONE TURNO</option>
                                                    <option value="MAÑANA">8 AM A 8 PM</option>
                                                    <option value="TARDE">8 PM A 8 AM</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" name="fecha_dia_fin" id="fecha_dia_fin" class="datepicker" placeholder="Fecha Termino" required> 
																								<input type="text" name="fecha_dia_fin2" id="fecha_dia_fin2" class="datepicker" style="display: none"> 
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  style="text-align: left;"> 
                                            <label>
                                              <input name="dia" type="radio" value="mas" id="masdeuno" onclick="MasdeUno();"/>
                                              <span>Mas de 1 Dia</span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" name="dias" id="dias" class="validate" placeholder=""  onkeypress="return soloNumeros(event)"  onblur="MostrarFinicio();">
                                                <input type="text" name="dias_pendientes" id="dias_pendientes" class="validate" style="display: none">
                                                <input type="text" name="horas_pedidas" id="horas_pedidas" class="validate" style="display: none">
                                                <label for="icon_prefix">Cantidad de dias</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" class="datepicker" name="Finicio" id="Finicio" placeholder="Fecha de Inicio" onchange="ValidoFechaINI();" required> 
                                            </div> 
                                        </td>
                                        <td>
                                            <div class="input-field col s10">
                                                <input type="text" class="datepicker" name="Ftermino" id="Ftermino" placeholder="Fecha de Termino" required>
                                                <input type="text" class="datepicker" name="Ftermino2" id="Ftermino2" class="validate" style="display: none">
                                            </div>
                                        </td>
                                    </tr>                                                               
                                </tbody>
                            </table>
                            </br>
                            </br>
                            <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura" onchange="Motivo();">
                                    <?php
                                        $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND ((ESTABLECIMIENTO.EST_NOM = '$Sdependencia') OR (ESTABLECIMIENTO.EST_NOM = 'MULTIESTABLECIMIENTO'))";
                                        $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){
                                                $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                            }
                                            echo "<option value='no' selected>Jefe Directo</option>";
                                    ?>
                                </select>
																<label for="jefatura">Jefe Directo</label>
                            </div>
                            <div class="input-field col s12">
                            <!-- <input type="text" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"> -->
                                <input type="text" name="motivo" id="motivo" class="validate" placeholder="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)" onchange="Listo();">
                                <label for="icon_prefix">Motivo</label>
                            </div>
                            <div class="col s12">
                                <button id="guardar" type="submit" class="btn trigger" name="guardar" value="Guardar" >Enviar</button>
                            </div>
                         </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <?php
            if($_POST['guardar'] == "Guardar"){
                //primero rescato todos los datos del formulario
                $doc_id = $_POST['seleccion'];
                $usu_rut = $Srut;
                $usu_rut_jd = $_POST['jefatura'];
                $motivo = utf8_decode($_POST['motivo']);
                $estado = 'SOLICITADO';
                $AñoActual = date("Y");
                if ($doc_id == 1 || $doc_id == 2){
                	//feriados y administrativos
	                if($_POST['dia'] == "1/2"){
                      $tipo = "MEDIODIA";
	                    $cant_dias = "1/2";
	                    $jornada = utf8_decode($_POST['jornada']);
	                    $fec_ini = $_POST['fechamediodia_ini'];
	                    $fec_fin = $_POST['fechamediodia_fin2'];
	                }elseif ($_POST['dia'] == "1"){
                      $tipo = "UNDIA";
	                    $cant_dias = 1;
	                    $jornada = "COMPLETA";
	                    $fec_ini = $_POST['fecha_dia_ini'];
	                    $fec_fin = $_POST['fecha_dia_fin2'];
	                }elseif ($_POST['dia'] == "mas"){
                      $tipo = "MASDEUNDIA";
	                    $cant_dias = $_POST['dias'];
	                    $jornada = "COMPLETA";
	                    $fec_ini = $_POST['Finicio'];
	                    $fec_fin = $_POST['Ftermino2'];
	                }
                    $guardar_permiso = "UPDATE SOL_PERMI SET DOC_ID = $doc_id, USU_RUT_JD = '$usu_rut_jd',SP_CANT_DIA = '$cant_dias',SP_FEC_INI='$fec_ini',SP_FEC_FIN='$fec_fin',SP_JOR='$jornada',SP_MOT='$motivo',SP_ESTA='$estado',SP_TIPO='$tipo',SP_ANO='$AñoActual',SP_FEC='$fecha_hoy',SP_DECRE='NO' WHERE (SP_ID = $NuevoID)";
                    mysqli_query($cnn, $guardar_permiso);
                    $id_sp_actual = $NuevoID;
                    if($doc_id == 1){
                        $fl_actual = $_POST['fl'];
                        $fla_actual = $_POST['fla'];
                        if($fla_actual > 0){
                            //si tiene vacaciones acumulada
                            if($fla_actual == $cant_dias){
                                //solo se usan dias acumulados y fla queda en 0
                                $insert_sp_detalle = "INSERT INTO SP_DETALLE_FL (SP_ID,SPD_FL,SPD_FLA) VALUES ($id_sp_actual,0,$cant_dias)";
                                mysqli_query($cnn,$insert_sp_detalle);
                                $query_banco_fl = "SELECT BD_ID, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$AñoActual')";
                                $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                                if (mysqli_num_rows($resultado_banco_fl) != 0){
                                    while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                                        $num_id  = $row_fl[0];
                                        $num_fl_usado = $row_fl[1];
                                    }
                                }
                                $num_fl_usado = $num_fl_usado + $cant_dias;
                                $update_fl = "UPDATE BANCO_DIAS SET BD_FLA = 0, BD_FL_USADO = $num_fl_usado WHERE BD_ID = $num_id";
                                mysqli_query($cnn, $update_fl);
                            }elseif($fla_actual > $cant_dias){
                                //solo se usan dias acumulados
                                $insert_sp_detalle = "INSERT INTO SP_DETALLE_FL (SP_ID,SPD_FL,SPD_FLA) VALUES ($id_sp_actual,0,$cant_dias)";
                                mysqli_query($cnn,$insert_sp_detalle);
                                $query_banco_fl = "SELECT BD_ID, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$AñoActual')";
                                $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                                if (mysqli_num_rows($resultado_banco_fl) != 0){
                                    while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                                        $num_id  = $row_fl[0];
                                        $num_fl_usado = $row_fl[1];
                                    }
                                }
                                $num_fl_usado = $num_fl_usado + $cant_dias;
                                $update_fl = "UPDATE BANCO_DIAS SET BD_FLA = 0, BD_FL_USADO = $num_fl_usado WHERE BD_ID = $num_id";
                                mysqli_query($cnn, $update_fl);
                            }elseif($fla_actual < $cant_dias){
                                //fla queda en 0 
                                $spd_fla = $fla_actual;
                                $spd_fl = $cant_dias - $fla_actual;
                                $insert_sp_detalle = "INSERT INTO SP_DETALLE_FL (SP_ID,SPD_FL,SPD_FLA) VALUES ($id_sp_actual,$spd_fl,$spd_fla)";
                                echo $insert_sp_detalle;
                                mysqli_query($cnn,$insert_sp_detalle);
                                $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$AñoActual')";
                                $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                                if (mysqli_num_rows($resultado_banco_fl) != 0){
                                    while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                                        $num_id  = $row_fl[0];
                                        $num_fl = $row_fl[1];
                                        $num_fl_usado = $row_fl[2];
                                    }
                                }
                                $num_fl_usado = $num_fl_usado + $cant_dias;
                                $num_fl = $num_fl - $spd_fl;
                                $update_fl = "UPDATE BANCO_DIAS SET BD_FLA = 0, BD_FL = $num_fl, BD_FL_USADO = $num_fl_usado WHERE BD_ID = $num_id";
                                mysqli_query($cnn, $update_fl);
                            }
                        }else{
                            //no tiene vacaciones acumuladas
                            $insert_sp_detalle = "INSERT INTO SP_DETALLE_FL (SP_ID,SPD_FL,SPD_FLA) VALUES ($id_sp_actual,$cant_dias,0)";
                            mysqli_query($cnn,$insert_sp_detalle);
                            $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$AñoActual')";
                            $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                            if (mysqli_num_rows($resultado_banco_fl) != 0){
                                while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                                    $num_id  = $row_fl[0];
                                    $num_fl = $row_fl[1];
                                    $num_fl_usado = $row_fl[2];
                                }
                            }
                            $num_fl_usado = $num_fl_usado + $cant_dias;
                            $num_fl = $num_fl - $cant_dias;
                            $update_fl = "UPDATE BANCO_DIAS SET BD_FLA = 0, BD_FL = $num_fl, BD_FL_USADO = $num_fl_usado WHERE BD_ID = $num_id";
                            mysqli_query($cnn, $update_fl);
                        }
                    }elseif($doc_id == 2){
                        if($tipo == "MEDIODIA"){
                            $cant_dias = 0.5;
                        }
                        //actualiza bd_fl = bd_fl - cant_dias
                        $query_banco_adm = "SELECT BD_ID, BD_ADM, BD_ADM_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$AñoActual')";
                        $resultado_banco_adm = mysqli_query($cnn, $query_banco_adm);
                        if (mysqli_num_rows($resultado_banco_adm) != 0){
                            while ($row_adm = mysqli_fetch_array($resultado_banco_adm)){
                                $num_id  = $row_adm[0];
                                $num_adm  = $row_adm[1];
                                $num_adm_usado = $row_adm[2];
                            }
                        }
                        $num_adm = $num_adm - $cant_dias;
                        $num_adm_usado = $num_adm_usado + $cant_dias;
                        $update_adm = "UPDATE BANCO_DIAS SET BD_ADM = $num_adm, BD_ADM_USADO = $num_adm_usado WHERE BD_ID = $num_id";
                        mysqli_query($cnn, $update_adm);
                    }
                }elseif($doc_id == 3){
	                if($_POST['dia'] == "horas"){
                      $tipo = "HORAS";
	                    $cant_horas = $_POST['cant_horas'];
	                    $jornada = "HORAS";
	                    $fecha = $_POST['fecha_hora_ini'];
                      $fecha_bh = $fecha;
                      $cant_dias = 0;
	                    $fec_ini = $_POST['fecha_hora_ini'];
	                    $fec_fin = $_POST['fecha_hora_fin2'];
	                    $hor_ini = $_POST['hora_ini'];
	                    $hor_fin = $_POST['hora_fin2'];
                      $guardar_permiso = "UPDATE SOL_PERMI SET DOC_ID = $doc_id, USU_RUT_JD = '$usu_rut_jd',SP_CANT_DIA = '$cant_dias',SP_FEC_INI='$fec_ini',SP_FEC_FIN='$fec_fin',SP_CANT_DC=$cant_horas,SP_HOR_INI='$hor_ini',SP_HOR_FIN='$hor_fin',SP_JOR='$jornada',SP_MOT='$motivo',SP_ESTA='$estado',SP_TIPO='$tipo',SP_ANO='$AñoActual',SP_FEC='$fecha_hoy',SP_DECRE='NO' WHERE (SP_ID = $NuevoID)";
                        //echo $guardar_permiso;
                        mysqli_query($cnn, $guardar_permiso);
	                }elseif ($_POST['dia'] == "1"){
                        $tipo = "UNDIA";
                        //ver que dia es y a cuantas horas equivale
                        $fecha = $_POST['fecha_dia_ini'];
                        $fecha_bh = $fecha;
                        $fec_ini = $_POST['fecha_dia_ini'];
                        $fec_fin = $_POST['fecha_dia_fin2'];
												$jornada_dia = $_POST['jornada_dia'];
												$cant_horas = 12;
												if($jornada_dia == "MAÑANA"){
													$hor_ini = "08:00:00";
                          $hor_fin = "20:00:00";
												}else{
													$hor_ini = "20:00:00";
                          $hor_fin = "08:00:00";
												}
	                    $cant_dias = 1;
	                    $jornada = "COMPLETA";
                      $guardar_permiso = "UPDATE SOL_PERMI SET DOC_ID = $doc_id, USU_RUT_JD = '$usu_rut_jd',SP_CANT_DIA = '$cant_dias',SP_FEC_INI='$fec_ini',SP_FEC_FIN='$fec_fin',SP_CANT_DC=$cant_horas,SP_HOR_INI='$hor_ini',SP_HOR_FIN='$hor_fin',SP_JOR='$jornada',SP_MOT='$motivo',SP_ESTA='$estado',SP_TIPO='$tipo',SP_ANO='$AñoActual',SP_FEC='$fecha_hoy',SP_DECRE='NO' WHERE (SP_ID = $NuevoID)";
	                }
                    //Rescato el ultimo id insertado en sol_permi
                    $num_id_sp  = $NuevoID;
                    $cant_horas_inicial = $cant_horas;
                    list($año_bh, $mes_bh, $dia_bh) = split('[/]', $fecha_bh);
                    $FecIni_bh = ($año_bh - 2)."/".$mes_bh."/".$dia_bh;
                    $query_bh = "SELECT BH_ID,BH_SALDO FROM BANCO_HORAS WHERE (USU_RUT = '$usu_rut') AND (BH_SALDO > 0) AND (BH_FEC BETWEEN '$FecIni_bh' AND '$fecha_bh') AND ((BH_TIPO = 'INICIAL') OR (BH_TIPO = 'INGRESO')) ORDER BY BH_FEC ASC";
                    $resultado_bh = mysqli_query($cnn, $query_bh);
                    //CONSULTO EL ID MAS ALTO DE BH_ID
                    $q_bh_id = mysqli_query($cnn, "SELECT MAX(BH_ID) AS BH_ID FROM BANCO_HORAS");
                    if ($row_BH_ID = mysqli_fetch_row($q_bh_id)) {
                        $Ultimo_id = $row_BH_ID[0];
                    }
                    $nuevo_bh_id = $Ultimo_id + 1;
                    while ($row_bh = mysqli_fetch_array($resultado_bh)){
                        $bh_id = $row_bh[0];
                        $bh_saldo  = $row_bh[1];
                        if ($bh_saldo > $cant_horas){
                            //saldo nuevo a guardar
                            $saldo_nuevo = $bh_saldo - $cant_horas;
                            $bh_update = "UPDATE BANCO_HORAS SET BH_SALDO = $saldo_nuevo WHERE BH_ID = $bh_id";
                            mysqli_query($cnn,$bh_update);
                            $bh_insert = "INSERT INTO BANCO_HORAS (BH_ID,USU_RUT,BH_FEC,BH_TIPO,BH_CANT,BH_ID_ANT) VALUES ($nuevo_bh_id,'$usu_rut','$fecha_bh','EGRESO',$cant_horas_inicial,$num_id_sp)";
                            //echo $bh_insert;
                            mysqli_query($cnn,$bh_insert);
                            $bhd_insert = "INSERT INTO BH_DETALLE_EGRESO (BH_ID_EGRESO, BH_ID_INGRESO,BHD_CANT) VALUES ($nuevo_bh_id,$bh_id,$cant_horas)";
                            mysqli_query($cnn,$bhd_insert);
                            break 1;
                        }elseif($bh_saldo == $cant_horas){
                            //saldo nuevo a guardar
                            $saldo_nuevo = 0;
                            $bh_update = "UPDATE BANCO_HORAS SET BH_SALDO = $saldo_nuevo WHERE BH_ID = $bh_id";
                            mysqli_query($cnn,$bh_update);
                            $bh_insert = "INSERT INTO BANCO_HORAS (BH_ID,USU_RUT,BH_FEC,BH_TIPO,BH_CANT,BH_ID_ANT) VALUES ($nuevo_bh_id,'$usu_rut','$fecha_bh','EGRESO',$cant_horas_inicial,$num_id_sp)";
                            mysqli_query($cnn,$bh_insert);
                            $bhd_insert = "INSERT INTO BH_DETALLE_EGRESO (BH_ID_EGRESO, BH_ID_INGRESO,BHD_CANT) VALUES ($nuevo_bh_id,$bh_id,$cant_horas)";
                            mysqli_query($cnn,$bhd_insert);
                            break 1;
                        }elseif($bh_saldo < $cant_horas){
                            //UPDATE SALDO A 0 PARA ID SALDO MENOR
                            $saldo_nuevo = 0;
                            $bh_update = "UPDATE BANCO_HORAS SET BH_SALDO = $saldo_nuevo WHERE BH_ID = $bh_id";
                            mysqli_query($cnn,$bh_update);
                            $bhd_insert = "INSERT INTO BH_DETALLE_EGRESO (BH_ID_EGRESO, BH_ID_INGRESO,BHD_CANT) VALUES ($nuevo_bh_id,$bh_id,$bh_saldo)";
                            mysqli_query($cnn,$bhd_insert);
                            $cant_horas = $cant_horas - $bh_saldo;
                        }
                    }
                }
                //revisar el ID des permiso que corresponde
								
                $Id_for_actual = $NuevoID;
                    $FecActual = date("Y-m-d");
                    $HorActual = date("H:i:s");
                    $HPAccion = "CREA PERMISO";
                    $guardar_historial = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($Id_for_actual,'$usu_rut','$FecActual','$HorActual',$doc_id, '$HPAccion')";
                    //echo $guardar_historial;
                    mysqli_query($cnn, $guardar_historial);
                    ?> <script type="text/javascript"> window.location="../index.php";</script>  <?php
            }
        ?>
    </body>
</html>