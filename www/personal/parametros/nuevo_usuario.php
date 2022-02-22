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
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $ano1 =date("Y");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $id_formulario = 6;
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
        <title>Version desarrollo - Personal Salud</title>
        <meta charset="UTF-8">
        <!-- Le decimos al navegador que nuestra web esta optimizada para moviles -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <!-- Cargamos el CSS --> 
        <link type="text/css" rel="stylesheet" href="../../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../../include/css/materialize.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../../../include/css/custom.css" />
        <link href="../../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <style type="text/css">
            body{
                background-image: url("../../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }

        </style>
        <script type="text/javascript" src="../../include/js/jquery-3.3.1.min.js"></script>
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
								$("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        M.toast({html: 'Rut incorrecto'});  
                        $("#buscar").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                      M.toast({html: 'Rut correcto'});  
                      $("#buscar").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });  
            });
            function CargarIndex(){
                $("#buscar").attr("disabled","disabled");
                $("#nombre_usuario").attr("disabled","disabled");
                $("#apellidoP_usuario").attr("disabled","disabled");
                $("#apellidoM_usuario").attr("disabled","disabled");
								$("#fechanac").attr("disabled","disabled");
								$("#nacionalidad").attr("disabled","disabled");
								$("#reloj").attr("disabled","disabled");
								$("#email_usuario").attr("disabled","disabled");
                $("#fono_usuario").attr("disabled","disabled");
                $("#direccion_usuario").attr("disabled","disabled");
								$("#nivel_usuario").attr("disabled","disabled");
                $("#fechaIngreso").attr("disabled","disabled");
                $("#fechaInicio").attr("disabled","disabled");
                $("#profesion_usuario").attr("disabled","disabled");
                $('select').formSelect('destroy');
                $("#guardar").attr("disabled","disabled");
            }
            function Respuesta(r){
                if(r.resultado == 1){
                    //usuario existe
                    $("#rut_usuario").val("");
                    $("#buscar").attr("disabled","disabled");
                    M.toast({html: 'Usuario RUT: ' + r.rut_enviado + ' existe'}); 
                }else{
                    //usuario no existe y debemos mostrar campos para agregar nuevos usuarios
                    $("#rut_usuario").attr("disabled","disabled");
                    $("#buscar").attr("disabled","disabled");
                    $("#rut_oculto").val(r.rut_enviado);
                    $("#nombre_usuario").removeAttr("disabled");
                    $("#apellidoP_usuario").removeAttr("disabled");
                    $("#apellidoM_usuario").removeAttr("disabled");
										$("#fechanac").removeAttr("disabled");										
										$("#nacionalidad").removeAttr("disabled");	
										$("#reloj").removeAttr("disabled");	
										$("#email_usuario").removeAttr("disabled");
                    $("#fono_usuario").removeAttr("disabled");
                    $("#direccion_usuario").removeAttr("disabled");
										$("#nivel_usuario").removeAttr("disabled");
                    //$("#guardar").removeAttr("disabled");
                    //$("#establecimiento").material_select();
                    $('#cargo_usuario').formSelect();
                    $('#categoria_usuario').formSelect();
                    $('#jefatura').formSelect();
                    $('#contrato').formSelect();
                    $('#sexo').formSelect();
                    $('#tramo').formSelect();
                }
            }
            function ValidoEmail(){
                var email = $('#email_usuario').val();
                expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if ( !expr.test(email) ){
                    M.toast({html: 'La dirección de correo ' + email + ' es incorrecta.'}); 
                    $('#email_usuario').val("");
                }  
            }
            function CambioCategoria(){
                var value_categoria = $("#categoria_usuario").val();
                if(value_categoria == "no"){
                    M.toast({html: 'Debe seleccionar una Categoria'});
                }else{
                    $("#establecimiento").formSelect();
                }
            }
            function CambioEstablecimiento(){
                var value_establecimiento = $("#establecimiento").val();
                if(value_establecimiento == "no"){
                    M.toast({html: 'Debe seleccionar un Establecimiento'});
                }else{
                    $("#dependecia").formSelect();
                }
            }
            function CambioDependencia(){
                var value_dependencia = $("#dependecia").val();
                if(value_dependencia == "no"){
                    M.toast({html: 'Debe asignar una Dependencia'});
                }else{
                    $("#fechaIngreso").removeAttr("disabled");
                }
            }
            function CambioContrato(){
                $("#profesion_usuario").removeAttr("disabled");
            }
            function CambioProfesion(){
                $("#fechaInicio").removeAttr("disabled");
            }
            function CambioFechaInicio(){
                $("#guardar").removeAttr("disabled");
            }
            function Buscar(){
                //variables, que hacen referencia los elementos de la pagina por sus ID.
                var rut = $("#rut_usuario").val();
                var post = $.post("../php/buscar_usuario.php", { "rut_nuevo" : rut }, Respuesta, 'json');
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
                return (key >= 48 && key <= 57)
            }
        </script>
    </head>
    <body onload="CargarIndex();">
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
                        <h4 class="light">Nuevo Usuario</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                                <div align="right"><h6><a href="mant_usuarios.php" class="btn trigger">Volver</a></h6></div>
                            </div>
                        </div>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                                <div class="input-field col s6">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" style="text-transform: uppercase" placeholder="">
                                    <label for="icon_prefix">RUT</label>
                                </div>
                                <div class="input-field col s6">
                                    <button class="btn trigger" type="button" name="buscar" id="buscar" value="buscar"  onclick = "Buscar();">Buscar</button>
                                </div>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" required>
                                </div>
                                <div class="input-field col s12">
                                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" required onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Nombres</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="apellidoP_usuario" id="apellidoP_usuario" class="validate" placeholder="" required onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Paterno</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="apellidoM_usuario" id="apellidoM_usuario" class="validate" placeholder="" required onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Materno</label>
                                </div>
																<div class="input-field col s4">
                                    <input type="text" class="datepicker" name="fechanac" id="fechanac" placeholder="" required> 
                                    <label for="icon_prefix" id="fechanac">Fecha Nacimiento</label>
                                </div>
															  <div class="input-field col s3">
                                    <select name="sexo" id="sexo">
                                        <option value="no" selected disabled>Seleccione Sexo</option>
                                        <option value="F">Femenino</option>
                                        <option value="M">Masculino</option>
																			  <option value="O">Otro</option>
                                    </select>
                                    <label for="sexo">Sexo</label> 
                                </div>
																<div class="input-field col s3">
                                    <input type="text" name="nacionalidad" id="nacionalidad" class="validate" placeholder="" required onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Nacionalidad</label>
                                </div>
																<div class="input-field col s3">
                                    <input type="text" name="reloj" id="reloj" class="validate" placeholder="" required>
                                    <label for="icon_prefix">N° Reloj Control</label>
                                </div>
																<div class="input-field col s3">
                                    <select name="tramo" id="tramo">
                                        <option value="no" selected disabled>Seleccione Tramo</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
																			  <option value="3">3</option>
																				<option value="4">4</option>
                                    </select>
                                    <label for="tramo">Tramo</label> 
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="email_usuario" id="email_usuario" class="validate" placeholder="" onblur="ValidoEmail();" required>
                                    <label for="icon_prefix">Correo</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="fono_usuario" id="fono_usuario" class="validate" placeholder="" onkeypress="return soloNumeros(event)" required>
                                    <label for="icon_prefix">Telefono</label>
                                </div>
                                <div class="input-field col s12">
                                    <input type="text" name="direccion_usuario" id="direccion_usuario" class="validate" placeholder="" required>
                                    <label for="icon_prefix">Direccion</label>
                                </div>
                                <div class="input-field col s6">
                                    <select name="jefatura" id="jefatura">
                                        <option value="NO" selected disabled>Pertenece a Jefatura</option>
                                        <option value="SI">SI</option>
                                        <option value="NO">NO</option>
                                    </select>
                                    <label for="jefatura">Jefatura</label> 
                                </div>
                                <div class="input-field col s6">
                                    <select name="cargo_usuario" id="cargo_usuario">
                                        <option value="no" selected disabled>Seleccione Cargo</option>
                                        <option value="Director">Director</option>
                                        <option value="Director (S)">Director (S)</option>
                                        <option value="">Sin Cargo</option>
                                    </select>
                                </div>
                                <div class="input-field col s6">
                                    <select name="categoria_usuario" id="categoria_usuario" onchange="CambioCategoria();">
                                        <option value="no" disabled selected required>Seleccione Categoria</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                        <option value="F">F</option>
                                    </select>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="nivel_usuario" id="nivel_usuario" class="validate" placeholder="" value="15">
                                    <label for="nivel_usuario">Nivel</label>
                                </div>
                                <div class="input-field col s6" >
                                    <select name="establecimiento" id="establecimiento" onchange="CambioEstablecimiento();">
                                        <?php
                                            $Establecimientos = "SELECT EST_ID, EST_NOM FROM ESTABLECIMIENTO WHERE (EST_ESTA = 'ACTIVO')";
                                            $resultadoE =mysqli_query($cnn, $Establecimientos);
                                                while($regE =mysqli_fetch_array($resultadoE)){
                                                    printf("<option value=\"$regE[0]\">$regE[1]</option>");
                                                }
                                            echo "<option value='no' disabled selected>Seleccione Establecimiento</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6">
                                    <select name="dependecia" id="dependecia" onchange="CambioDependencia();">
                                        <?php
                                            $Dependencia="SELECT EST_NOM FROM ESTABLECIMIENTO WHERE (EST_ESTA = 'ACTIVO')";
                                            $resultadoD =mysqli_query($cnn, $Dependencia);
                                                while($regD=mysqli_fetch_array($resultadoD)){
                                                    printf("<option value=\"$regD[0]\">$regD[0]</option>");
                                                }
                                            echo "<option value='no' disabled selected>Indique Dependencia</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" class="datepicker" name="fechaIngreso" id="fechaIngreso" placeholder="" required> 
                                    <label for="icon_prefix" id="fechaIngreso">Fecha Ingreso Dpto. Salud de Rengo</label>
                                </div> 
                                <div class="input-field col s6">
                                    <select name="contrato" id="contrato" onchange="CambioContrato();">
                                        <option value='no' disabled selected>Indique Tipo Contrato</option>
                                        <option value='PLAZO INDIFINIDO'>PLAZO INDEFINIDO</option>
                                        <option value='PLAZO FIJO'>PLAZO FIJO</option>
                                        <option value='REEMPLAZO'>REEMPLAZO</option>
																				<option value='HONORARIO'>HONORARIO</option>
                                    </select>
                                    <label for="icon_prefix">Calidad Jurídica</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="profesion_usuario" id="profesion_usuario" class="validate" placeholder="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)" onchange="CambioProfesion();" required>
                                    <label for="icon_prefix">Profesion</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" class="datepicker" name="fechaInicio" id="fechaInicio" onchange="CambioFechaInicio();" placeholder="" required> 
                                    <label for="icon_prefix" id="fechaInicio">Fecha Ingreso Salud Publica</label>
                                </div> 
                                <div class="col s12">
                                    <button id="guardar" class="btn trigger" type="submit" name="guardar" value="Guardar">Agregar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <?php
            if($_POST['guardar'] == "Guardar"){
                //btn actualizar
                $nuevo_rut = $_POST['rut_oculto'];
                $nuevo_nombre = utf8_decode($_POST['nombre_usuario']);
                $nuevo_apellidoP = utf8_decode($_POST['apellidoP_usuario']);
                $nuevo_apellidoM = utf8_decode($_POST['apellidoM_usuario']);
								$nuevo_fecnac = utf8_decode($_POST['fechanac']);
								$nuevo_sexo = $_POST['sexo'];
								$nuevo_nacionalidad = utf8_decode($_POST['nacionalidad']);
								$nuevo_reloj = $_POST['reloj'];
								$nuevo_tramo = ($_POST['tramo']);
                $nuevo_email = utf8_decode($_POST['email_usuario']);
                $nuevo_fono = $_POST['fono_usuario'];
                $nuevo_direccion = $_POST['direccion_usuario'];
                $nuevo_cargo = utf8_decode($_POST['cargo_usuario']);
                $nuevo_establecimiento = $_POST['establecimiento'];
                $nuevo_dependencia = utf8_decode($_POST['dependecia']);
                $nuevo_estado = "DESACTIVADO";
                $nuevo_clave = md5("saludrengo");
                $nuevo_categoria = $_POST['categoria_usuario'];
                $nuevo_nivel = 15;
                $nuevo_jefatura = $_POST['jefatura'];
                $nuevo_fec_ing = $_POST['fechaIngreso'];
                $nuevo_fec_ini = $_POST['fechaInicio'];
                $nuevo_contrato = $_POST['contrato'];
                $nuevo_profesion = $_POST['profesion_usuario'];
                $lunesjueves="LU-MA-MI-JU";
                $sdf="S-D-F";
                $vie="V";
                $horaotextra1= "17:00:00";                    
                $horaotextra3= "16:00:00";
                $horaotextra4= "08:00:00";
                $horaotextra5= "00:00:00";
                $inactivoot = "ACTIVA";
                $fecha1 = $ano1."-01-01";
                $fecha2= $ano1."-12-31";
                //datos banco de usuario
                $AñoActual = date("Y");
                $BD_SGR = 90;
                $BD_ADM = 6;
                $BD_FL  = 15;
                $BD_FLA = 0;
                echo $insertarUsuario = "INSERT INTO USUARIO (USU_RUT,USU_NOM,USU_APP,USU_APM,USU_MAIL,USU_DIR,USU_FONO,USU_CAR,EST_ID,USU_DEP,USU_ESTA,USU_PAS,USU_CAT,USU_NIV,USU_JEF,USU_FEC_ING,
								USU_CONTRA,USU_PROF,USU_FEC_INI,USU_FEC_NAC,USU_SEXO,USU_NACIONAL,USU_RECON,USU_TRAMO) VALUES ('$nuevo_rut','$nuevo_nombre','$nuevo_apellidoP','$nuevo_apellidoM','$nuevo_email','$nuevo_direccion','$nuevo_fono','$nuevo_cargo',
								'$nuevo_establecimiento','$nuevo_dependencia','$nuevo_estado','$nuevo_clave','$nuevo_categoria','$nuevo_nivel','$nuevo_jefatura','$nuevo_fec_ing','$nuevo_contrato','$nuevo_profesion',
								'$nuevo_fec_ini','$nuevo_fecnac','$nuevo_sexo','$nuevo_nacionalidad','$nuevo_reloj','$nuevo_tramo')";
                mysqli_query($cnn, $insertarUsuario);
                //cargar banco dias para año actual
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',4)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',9)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',10)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',11)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',13)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',14)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',15)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',35)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',17)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',19)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',21)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',26)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',28)");
								mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',32)");
                mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',38)");
                mysqli_query($cnn, "INSERT INTO ACCESO (USU_RUT,FOR_ID) VALUES ('$nuevo_rut',43)");
                $GuardarNuevo = "INSERT INTO BANCO_DIAS (USU_RUT,BD_ADM,BD_FL,BD_FLA,BD_ANO,BD_SGR) VALUES ('$nuevo_rut',$BD_ADM,$BD_FL,$BD_FLA,'$AñoActual',$BD_SGR)";
                mysqli_query($cnn, $GuardarNuevo);
                $accionRealizada = utf8_decode("CREO NUEVO USUARIO :  ".$nuevo_nombre." ".$nuevo_apellidoP." ".$nuevo_apellidoM);
                $insertAccion = "INSERT INTO LOG_ACCION (LA_ACC,FOR_ID,USU_RUT,LA_IP_USU,LA_FEC,LA_HORA) VALUES ('$accionRealizada','$id_formulario','$Srut','$ipcliente','$fecha','$hora')";
                mysqli_query($cnn, $insertAccion);
                $insertaotextra = "INSERT INTO OT_EXTRA_AUT(USU_RUT, OEA_FEC, OEA_FEC_INI, OEA_FIN, OEA_LJ, OEA_LJ_HI, OEA_LJ_HF, OEA_VI, OEA_VI_HI, OEA_VI_HF, OEA_SDF, OEA_SDF_HI,OEA_SDF_HF) VALUES ('$nuevo_rut','$fecha','$fecha1','$fecha2','$lunesjueves','$horaotextra1','$horaotextra5','$vie','$horaotextra3','$horaotextra5','$sdf','$horaotextra4','$horaotextra5','$inactivoot')";
                mysqli_query($cnn, $insertaotextra);
                ?> <script type="text/javascript"> window.location="mant_usuarios.php";</script><?php
            }
        ?>
    </body>
</html>

                    
                                                                                      
                    