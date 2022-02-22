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
        $usu_rut_edit = $_GET['rut'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $buscar = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_MAIL,USUARIO.USU_DIR,USUARIO.USU_FONO,USUARIO.USU_CAR,ESTABLECIMIENTO.EST_NOM,
				USUARIO.USU_DEP,USUARIO.USU_ESTA,USUARIO.USU_PAS,USUARIO.USU_CAT,USUARIO.USU_NIV,USUARIO.USU_JEF,USUARIO.USU_FEC_ING,USUARIO.USU_CONTRA,USUARIO.USU_PROF,USUARIO.USU_FEC_INI,USUARIO.USU_FEC_NAC,
				USUARIO.USU_SEXO,USUARIO.USU_NACIONAL,USUARIO.USU_RECON,USUARIO.USU_TRAMO
				FROM USUARIO INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (USUARIO.USU_RUT = '".$usu_rut_edit."')";
        $rs = mysqli_query($cnn, $buscar);
        if($row = mysqli_fetch_array($rs)){
            $MuestroRut=$row[0];
            $MuestroNombre=utf8_encode($row[1]);
            $MuestroApellidoP = utf8_encode($row[2]);
            $MuestroApellidoM = utf8_encode($row[3]);
            $MuestroEmail = utf8_encode($row[4]);
            $MuestroDireccion = utf8_encode($row[5]);
            $MuestroFono = utf8_encode($row[6]);
            $MuestroCargo = utf8_encode($row[7]);
            $MuestroEstablecimiento = $row[8];
            $MuestroDependencia = utf8_encode($row[9]);
            $MuestroEstado = $row[10]; 
            $GuardoClave = $row[11]; 
            $MuestroCategoria = $row[12];
            $MuestroNivel = $row[13];
            $MuestroJefatura = $row[14];
            $MuestroFechaIngreso = $row[15];
            $MuestroTipoContrato = $row[16];
            $MuestroProfesion = utf8_encode($row[17]);
            $MuestroFechaInicio = $row[18];
						$MuestroFechaNac = $row[19];
						$MuestroSexo = $row[20];
						$MuestroNacion = $row[21];
						$MuestroReloj	= $row[22];
						$MuestroTramo = $row[23];
        }        
        $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE USU_RUT = '$usu_rut_edit'";
        $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
        if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
            $GuardoEstablecimiento=$row_eyd[0];
            $GuardoDependencia=$row_eyd[1];
        }
        $id_formulario = 5;
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
        <link type="text/css" rel="stylesheet" href="../../include/css/materialize.min.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../../include/css/custom.css" />
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
                $$('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
            function ValidoEmail(){
                var email = $('#email_usuario').val();
                expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if ( !expr.test(email) ){
                    Materialize.toast('La dirección de correo ' + email + ' es incorrecta.', 4000);
                    $('#email_usuario').val("");
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
          
            function ImprimirCC(){
              var idcc = "<?php echo $MuestroRut;?>";
              window.open('http://200.68.34.158/personal/pdf/certificado_capa.php?id='+idcc,'_blank');               
            }
            function ImprimirCE(){
              var idcc = "<?php echo $MuestroRut;?>";
              window.open('http://200.68.34.158/personal/pdf/certificado_expe.php?id='+idcc,'_blank');
            }

            function Dirigente(){
             var RutUsu = "<?php echo $MuestroRut;?>";
             var es = "0";
             window.location = "man_dirigente.php?rut="+RutUsu+'&es='+es;
            }
        </script>
    </head>
    <body>
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
                        <h4 class="light">Editar datos de Usuario</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                                <div align="right"><h6><a href="mant_usuarios.php" class="btn trigger">Volver</a></h6></div>
                            </div>
                        </div>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                                <div class="input-field col s6">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" placeholder="" disabled value="<?php echo $MuestroRut;?>">
                                    <label for="icon_prefix">RUT</label>
                                </div>
                                <div class="input-field col s12">
                                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $MuestroNombre;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Nombres</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="apellidoP_usuario" id="apellidoP_usuario" class="validate" placeholder="" value="<?php echo $MuestroApellidoP;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Paterno</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="apellidoM_usuario" id="apellidoM_usuario" class="validate" placeholder="" value="<?php echo $MuestroApellidoM;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Materno</label>
                                </div>
																<div class="input-field col s4">
                                    <input type="text" class="datepicker" name="fechanac" id="fechanac" placeholder="Fecha Nacimiento" value="<?php echo $MuestroFechaNac;?>" required> 
                                    <label>Fecha Nacimiento</label>
                                </div>
															  <div class="input-field col s4">
                                    <select name="sexo" id="sexo">
                                        <option value="<?php echo $MuestroSexo; ?>"><?php echo $MuestroSexo; ?></option>
                                        <option value="F">Femenino</option>
                                        <option value="M">Masculino</option>
																			  <option value="O">Otro</option>
                                    </select>
                                    <label for="sexo">Sexo</label> 
                                </div>
																<div class="input-field col s4">
                                    <input type="text" name="nacionalidad" id="nacionalidad" class="validate" placeholder="" value="<?php echo $MuestroNacion;?>" required>
                                    <label for="icon_prefix">Nacionalidad</label>
                                </div>
																<div class="input-field col s3">
                                    <input type="text" name="reloj" id="reloj" class="validate" placeholder="" value="<?php echo $MuestroReloj;?>" required>
                                    <label for="icon_prefix">N° Reloj Control</label>
                                </div>
																<div class="input-field col s3">
                                    <select name="tramo" id="tramo" value="<?php echo $MuestroTramo;?>">
                                        <option value="<?php echo $MuestroTramo; ?>"><?php echo $MuestroTramo; ?></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
																			  <option value="3">3</option>
																				<option value="4">4</option>
                                    </select>
                                    <label for="tramo">Tramo</label> 
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="email_usuario" id="email_usuario" class="validate" placeholder="" value="<?php echo $MuestroEmail;?>" onblur="ValidoEmail()";>
                                    <label for="icon_prefix">Correo</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="direccion_usuario" id="direccion_usuario" class="validate" placeholder="" value="<?php echo $MuestroDireccion;?>" onblur="ValidoEmail()";>
                                    <label for="icon_prefix">Direccion</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="fono_usuario" id="fono_usuario" class="validate" placeholder="" value="<?php echo $MuestroFono;?>" onblur="ValidoEmail()";>
                                    <label for="icon_prefix">Telefono</label>
                                </div>
                                <div class="input-field col s4">
                                    <select name="cargo_usuario" id="cargo_usuario">
                                        <option value="<?php echo $MuestroCargo;?>" selected><?php echo $MuestroCargo;?></option>
                                        <option value="Director">Director</option>
                                        <option value="Director (S)">Director (S)</option>
                                        <option value="">Sin Cargo</option>
                                    </select>
                                    <label for="cargo_usuario">Cargo</label>
                                </div>
                                <div class="input-field col s4">
                                    <select name="categoria_usuario" id="categoria_usuario">
                                        <option value="<?php echo $MuestroCategoria;?>" selected><?php echo $MuestroCategoria;?></option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                        <option value="F">F</option>
                                    </select>
                                    <label for="categoria_usuario">Categoria</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="nivel_usuario" id="nivel_usuario" class="validate" placeholder="" value="<?php echo $MuestroNivel;?>">
                                    <label for="nivel_usuario">Nivel</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="jefatura_usuario" id="jefatura_usuario" class="validate" placeholder="" value="<?php echo $MuestroJefatura;?>" disabled>
                                    <label for="jefatura_usuario">Jefatura</label>
                                </div>
                                <div class="input-field col s6">
                                    <select name="jefatura" id="jefatura">
                                        <option value="<?php echo $MuestroJefatura;?>" selected>Pertenece a Jefatura</option>
                                        <option value="SI">SI</option>
                                        <option value="NO">NO</option>
                                    </select>
                                    <label for="jefatura">Jefatura</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="establecimiento_usuario" id="establecimiento_usuario" class="validate" placeholder="" value="<?php echo $MuestroEstablecimiento;?>" disabled>
                                    <label for="icon_prefix">Lugar de Trabajo</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="dependencia_usuario" id="dependencia_usuario" class="validate" placeholder="" value="<?php echo $MuestroDependencia;?>" disabled>
                                    <label for="icon_prefix">De quien depende</label>
                                </div>
                                <div class="input-field col s6" >
                                    <select name="establecimiento" id="establecimiento">
                                        <?php
                                            $Establecimientos = "SELECT EST_ID,EST_NOM FROM ESTABLECIMIENTO WHERE EST_ESTA = 'ACTIVO'";
                                            $resultadoE =mysqli_query($cnn, $Establecimientos);
                                                while($regE =mysqli_fetch_array($resultadoE)){
                                                    printf("<option value=\"$regE[0]\">$regE[1]</option>");
                                                }
                                            echo "<option value='".$GuardoEstablecimiento."' selected>Cambio de Establecimiento</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6">
                                    <select name="dependecia" id="dependecia">
                                        <?php
                                            $Dependencia="SELECT EST_NOM FROM ESTABLECIMIENTO WHERE EST_ESTA = 'ACTIVO'";
                                            $resultadoD =mysqli_query($cnn, $Dependencia);
                                                while($regD=mysqli_fetch_array($resultadoD)){
                                                    printf("<option value=\"$regD[0]\">$regD[0]</option>");
                                                }
                                            echo "<option value='".$GuardoDependencia."' selected>Cambio de Dependencia</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" class="datepicker" name="fechaIngreso" id="fechaIngreso" value="<?php echo $MuestroFechaIngreso;?>" placeholder="" required> 
                                    <label for="icon_prefix" id="fechaIngreso">Fecha Ingreso Dpto. Salud de Rengo</label>
                                </div> 
                                <div class="input-field col s6">
                                    <input type="text" class="datepicker" name="fechaInicio" id="fechaInicio" value="<?php echo $MuestroFechaInicio;?>" placeholder="" required> 
                                    <label for="icon_prefix" id="fechaInicio">Fecha Ingreso Salud Publica</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="contrato_usuario" id="contrato_usuario" class="validate" placeholder="" value="<?php echo $MuestroTipoContrato;?>" disabled>
                                    <label for="icon_prefix">Tipo de Contrato</label>
                                </div>
                                <div class="input-field col s6">
                                    <select name="contrato" id="contrato" onchange="CambioContrato();">
                                        <?php echo "<option value='".$MuestroTipoContrato."' selected>Cambio Tipo de Contrato</option>"; ?>
                                        <option value='PLANTA'>PLANTA</option>
                                        <option value='CONTRATA'>CONTRATA</option>
                                        <option value='SUPLENCIA'>SUPLENCIA</option>
                                        <option value='HONORARIO'>HONORARIO</option>
                                    </select>
                                    <label for="icon_prefix">Tipo Contrato</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="profesion_usuario" id="profesion_usuario" class="validate" placeholder="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)" value="<?php echo $MuestroProfesion;?>" required>
                                    <label for="icon_prefix">Profesion</label>
                                </div>
                                <div class="col s6" align="center">
                                    <p>
                                      <label>
                                        <input type="checkbox" id="pwd_usuario" name="pwd_usuario" value="reset" />
                                        <span>Reestablecer contraseña<span>
                                      </label>
                                    </p>
                                </div>
                                <div class="col s12"></div>
                                <div class="col s6">
                                    <td><button class='btn trigger' type='button' onclick='ImprimirCC();'>Certificado Capacitación</button></td>
                                </div>
                                <div class="col s6">
                                    <td><button class='btn trigger' type='button' onclick='ImprimirCE();'>Certificado Antigüedad</button></td>
                                </div>           
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto">
                                </div>
                                <div class="col s12">
                                    <button class="btn trigger" name="dirigente" onclick="Dirigente();" id="dirigente" type="button">Dirigente</button>
                                </div>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto">
                                </div>
                                <div class="col s12">
                                    <button id="actualizar" class="btn trigger" type="submit" name="actualizar" value="Actualizar">Actualizar</button>
                                </div>
                            </form>
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
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
        </script>
        <?php
            if($_POST['actualizar'] == "Actualizar"){
                //btn actualizar
                $nuevo_nombre = utf8_decode($_POST['nombre_usuario']);
                $nuevo_apellidoP = utf8_decode($_POST['apellidoP_usuario']);
                $nuevo_apellidoM = utf8_decode($_POST['apellidoM_usuario']);
								$nuevo_fec_nac = $_POST['fechanac'];
								$nuevo_sexo = $_POST['sexo'];
								$nuevo_nacion = $_POST['nacionalidad'];
								$nuevo_reloj = $_POST['reloj'];
							  $nuevo_tramo = $_POST['tramo'];
								if($nuevo_tramo ==""){
									echo $nuevo_tramo = $MuestroTramo;
								}
                $nuevo_email = utf8_decode($_POST['email_usuario']);
                $nuevo_direccion = utf8_decode($_POST['direccion_usuario']);
                $nuevo_fono = $_POST['fono_usuario'];
                $nuevo_cargo = utf8_decode($_POST['cargo_usuario']);
                $nuevo_establecimiento = $_POST['establecimiento'];
                $nuevo_dependencia = utf8_decode($_POST['dependecia']);
                //valido si reset la clave de usuario
                $valida_clave = $_POST['pwd_usuario'];
                if($valida_clave == "reset"){
                    $nuevo_clave = md5("saludrengo");
                }else{
                    $nuevo_clave = $GuardoClave;
                }
                $nuevo_categoria = $_POST['categoria_usuario'];
								$nuevo_nivel	= $_POST['nivel_usuario'];
                $nuevo_jefatura = $_POST['jefatura'];
                if ($MuestroFechaIngreso != $_POST['fechaIngreso']){
                    $nuevo_fec_ing = $_POST['fechaIngreso'];
                }else{
                    $nuevo_fec_ing = $MuestroFechaIngreso;
                }
                if($MuestroFechaInicio != $_POST['fechaInicio']){
                    $nuevo_fec_ini = $_POST['fechaInicio'];
                }else{
                    $nuevo_fec_ini = $MuestroFechaInicio;
                }
                $nuevo_contrato = $_POST['contrato'];
                $nuevo_profesion = $_POST['profesion_usuario'];
                $actualizarUsuario = "UPDATE USUARIO SET USU_NOM = '".$nuevo_nombre."', USU_APP = '".$nuevo_apellidoP."', USU_APM = '".$nuevo_apellidoM."', USU_MAIL = '".$nuevo_email."', 
								USU_DIR = '".$nuevo_direccion."', USU_FONO = '".$nuevo_fono."', USU_CAR = '".$nuevo_cargo."', EST_ID = '".$nuevo_establecimiento."', USU_DEP = '".$nuevo_dependencia."', 
								USU_PAS = '".$nuevo_clave."', USU_CAT = '".$nuevo_categoria."', USU_NIV='".$nuevo_nivel."', USU_JEF = '".$nuevo_jefatura."', USU_FEC_ING = '".$nuevo_fec_ing."', USU_CONTRA = '".$nuevo_contrato."', 
								USU_PROF = '".$nuevo_profesion."', USU_FEC_INI = '".$nuevo_fec_ini."', USU_FEC_NAC ='".$nuevo_fec_nac."', USU_SEXO ='".$nuevo_sexo."', USU_NACIONAL='".$nuevo_nacion."', 
								USU_RECON='".$nuevo_reloj."', USU_TRAMO ='".$nuevo_tramo."' WHERE (USU_RUT = '".$usu_rut_edit."')";
                mysqli_query($cnn, $actualizarUsuario);
                $accionRealizada = utf8_decode("ACTUALIZO DATOS DE :  ".$nuevo_nombre." ".$nuevo_apellidoP." ".$nuevo_apellidoM);
                $insertAccion = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                mysqli_query($cnn, $insertAccion);
                ?> <script type="text/javascript"> window.location="mant_usuarios.php";</script><?php
            }
        ?>
    </body>
</html>