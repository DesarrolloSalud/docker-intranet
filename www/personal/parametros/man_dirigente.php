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
        $SNUEVO =utf8_encode($_SESSION['NUEVO']);
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $usu_rut_edit = $_GET['rut'];
        $SNUEVO = $_GET['es'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $buscar = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO WHERE (USUARIO.USU_RUT = '".$usu_rut_edit."')";
        $rs = mysqli_query($cnn, $buscar);
        if($row = mysqli_fetch_array($rs)){
            $MuestroRut=$row[0];
            $MuestroNombre=utf8_encode($row[1]);
            $MuestroApellidoP = utf8_encode($row[2]);
            $MuestroApellidoM = utf8_encode($row[3]);
        }
        if($SNUEVO == 1){
            //echo "hola";
        }else{
            $buscar1 = "SELECT GD_CARGO,GD_FEC_INI,GD_FEC_FIN,GD_HORA,GD_ESTA,GD_ID,GD_ASOCIACION FROM GREMI_DIR WHERE (USU_RUT = '".$usu_rut_edit."') AND (GD_ID='".$SNUEVO."')";
            $rs1 = mysqli_query($cnn, $buscar1);
            if($row1 = mysqli_fetch_array($rs1)){
                $MuestroCargo=$row1[0];
                $MuestroFechaIni=utf8_encode($row1[1]);
                $MuestroFechaFin = utf8_encode($row1[2]);
                $MuestroHoras = utf8_encode($row1[3]);
                $MuestroEstado = utf8_encode($row1[4]);
                $MuestroID = ($row1[5]);
                $MuestroAsociacion = utf8_encode($row1[6]);
            } 
        }
              

        $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE USU_RUT = '$usu_rut_edit'";
        $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
        if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
            $GuardoEstablecimiento=$row_eyd[0];
            $GuardoDependencia=$row_eyd[1];
        }
        $id_formulario = 5;//CAMBIAR DIRIGENTES
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
        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});                
            });
            
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

            function Limpiar(){
                var RutUsu = "<?php echo $MuestroRut;?>";
                var es = "1";
                window.location = "man_dirigente.php?rut="+RutUsu+'&es='+es; 
            }
            function Editar(e){
                var RutUsu = "<?php echo $MuestroRut;?>";
                var es = e;
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
                        <h4 class="light">Mantenedor Dirigente</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                                <div align="right"><h6><a href="mant_usuarios.php" class="btn trigger">Volver</a></h6></div>
                            </div>
                        </div>
                        <div class="row">
                            <form name="form1" class="col s12" method="post">
                                <div class="input-field col s3">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" placeholder="" value="<?php echo $MuestroRut." ".$MuestroID?>"; disabled>
                                    <label for="icon_prefix">RUT</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $MuestroNombre;?>" onkeypress="return soloLetras(event)" disabled>
                                    <label for="icon_prefix">Nombres</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="apellidoP_usuario" id="apellidoP_usuario" class="validate" placeholder="" value="<?php echo $MuestroApellidoP;?>" onkeypress="return soloLetras(event)" disabled>
                                    <label for="icon_prefix">Apellido Paterno</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="apellidoM_usuario" id="apellidoM_usuario" class="validate" placeholder="" value="<?php echo $MuestroApellidoM;?>" onkeypress="return soloLetras(event)" disabled>
                                    <label for="icon_prefix">Apellido Materno</label>
                                </div>
								<div class="input-field col s4">
                                    <input type="text" class="datepicker" name="fechaini" id="fechaini" placeholder="" value="<?php echo $MuestroFechaIni;?>"> 
                                    <label for="icon_prefix" id="fechanac">Inicio</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" class="datepicker" name="fechafin" id="fechafin" placeholder="" value="<?php echo $MuestroFechaFin;?>" required> 
                                    <label for="icon_prefix" id="fechanac">Fin</label>
                                </div>
								<div class="input-field col s4">
                                    <select name="cargo" id="cargo" required>
                                        <option value="<?php echo $MuestroCargo; ?>"><?php echo $MuestroCargo; ?></option>
                                        <option value="Presidente">Presidente (a)</option>
                                        <option value="Tesorero">Tesorero (a)</option>
								        <option value="Secretaria">Secretaria (o)</option>
                                        <option value="Director">Director (a)</option>
                                        <option value="0"></option>
                                    </select>
                                    <label for="sexo">Cargo</label> 
                                </div>
								<div class="input-field col s4">
                                    <input type="text" name="hora_fuero" id="hora_fuero" class="validate" placeholder="" value="<?php echo $MuestroHoras;?>" required>
                                    <label for="icon_prefix">Horas Fueros</label>
                                </div>
                                <div class="input-field col s4">
                                    <select name="estado" id="estado" required>
                                        <option value="<?php echo $MuestroEstado;?>"><?php echo $MuestroEstado;?></option>
                                        <option value="ACTIVO">ACTIVO</option>
                                        <option value="INACTIVO">INACTIVO</option>
                                    </select>
                                    <label for="estado">ESTADO</label>
                                </div>
                                <div class="input-field col s4">
                                        <select name="asociacion" id="asociacion">
                                            <option value="<?php echo $MuestroAsociacion; ?>"><?php echo $MuestroAsociacion; ?></option>
                                            <option value="NACIONAL">NACIONAL</option>
                                            <option value="REGIONAL">REGIONAL</option>
                                            <option value="PROVINCIAL">PROVINCIAL</option>
                                            <option value="COMUNAL RENGO">COMUNAL RENGO</option>
                                            <option value="COMUNAL ROSARIO">COMUNAL ROSARIO</option>
                                        </select>
                                        <label for="asociacion">Asociación</label> 
                                </div>                                

                                <div class="col s6">
                                    <button id="actualizar" class="btn trigger" type="submit" name="actualizar" value="Actualizar">Guardar</button>
                                </div>
                                <div class="col s6">                                    
                                    <button class="btn trigger" name="nuevoperiodo" id="nuevoperiodo" type="button" onclick="Limpiar('1');">Nuevo Período</button>
                                </div> 
                                <!-- LISTA DE CARGOS GREMIALES -->

                                <table class="responsive-table bordered striped" font-family="9px";>
                                    <thead>
                                        <tr>
                                            <!-- <th>ID</th> -->
                                            <th>ID</th>
                                            <th>Cargo</th>
                                            <th>F. Inicio</th>
                                            <th>F. Fin</th>                                            
                                            <th>Horas Fuero</th>
                                            <th>Estado</th>
                                            <th>Asociación</th>
                                        </tr>
                                        <tbody>
                                            <!-- cargar la base de datos con php --> 
                                            <?php                                                
                                                $query = "SELECT GD_ID,GD_CARGO,GD_FEC_INI,GD_FEC_FIN,GD_HORA,GD_ESTA,GD_ASOCIACION FROM GREMI_DIR WHERE (USU_RUT = '".$usu_rut_edit."')";     
                                                    $respuesta = mysqli_query($cnn, $query);
                                             
                                                //recorrer los registros
                                                $cont = 0;                                                
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    echo "<tr>";
                                                        echo "<td><input type='text' id='in".$cont."' class='validate' value='".$row_rs[0]."' style='display: none'>".$row_rs[0]."</td>";
                                                        echo "<td>".utf8_encode($row_rs[1])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[2])."</td>";                                                     
                                                        echo "<td>".utf8_encode($row_rs[3])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[4])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[5])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[6])."</td>";

                                                        /*if ($row_rs[5] == "ACTIVO"){
                                                            echo '<td><p><input type="checkbox" id="che'.$cont.'" onclick="inactivo('.$cont.');" value="INACTIVO" checked="checked" />  <label for="che'.$cont.'"></label></p></td>';                                                                                                                       
                                                        }else{
                                                            echo '<td><p><input type="checkbox" id="che'.$cont.'" onclick="activa('.$cont.');" value="ACTIVO" />  <label for="che'.$cont.'"></label></p></td>';
                                                        }*/
                                                        
                                                    echo '<td><button class="btn trigger" name="editar" onclick="Editar('; echo "'".$row_rs[0]."'"; echo');" id="editar" type="button">Editar</button></td>';
                                                    //echo "<td><input type='text' id='oea_id".$cont."' class='validate' value='".$row_rs[0]."' style='display: none'>".$row_rs[0]."</td>";
                                                    echo "</tr>";
                                                    $cont = $cont + 1;
                                                }
                                            ?>

                                            

                                        </tbody>
                                    </thead>                                    
                                </table>
                                </br>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();

            });
        </script>
        <?php
            if($_POST['actualizar'] == "Actualizar"){
                //btn actualizar                
				$nuevo_fec_ini = $_POST['fechaini'];
                $nuevo_fec_fin = $_POST['fechafin'];
				$nuevo_cargo = $_POST['cargo'];
				$nuevo_hora_fuero = $_POST['hora_fuero'];
				$nuevo_estado = $_POST['estado'];
                $nuevo_asociacion = $_POST['asociacion'];
                
				if ($MuestroID != ""){
                    $actualizarDir = "UPDATE GREMI_DIR SET GD_CARGO = '".$nuevo_cargo."', GD_FEC_INI = '".$nuevo_fec_ini."', GD_FEC_FIN = '".$nuevo_fec_fin."', GD_HORA = '".$nuevo_hora_fuero."', GD_ESTA = '".$nuevo_estado."', GD_ASOCIACION = '".$nuevo_asociacion."' WHERE (USU_RUT = '".$usu_rut_edit."') AND (GD_ID ='$SNUEVO')";
                    mysqli_query($cnn, $actualizarDir);
                    $accionRealizada = utf8_decode("ACTUALIZO DATOS DIRIGENTE :  ".$nuevo_nombre." ".$nuevo_apellidoP." ".$nuevo_apellidoM);
                }else{
                    //NUEVO PERIODO, ELIMINAR ID Y DESACTIVAR ACTUAL
                    if($MuestroID2 != ""){

                    }
                    $ingresarDir ="INSERT INTO GREMI_DIR(USU_RUT,GD_CARGO,GD_FEC_INI,GD_FEC_FIN,GD_HORA,GD_ESTA,GD_ASOCIACION) VALUES('$usu_rut_edit','$nuevo_cargo','$nuevo_fec_ini','$nuevo_fec_fin','$nuevo_hora_fuero','$nuevo_estado','$nuevo_asociacion')";
                    mysqli_query($cnn, $ingresarDir);
                }
                $accionRealizada = utf8_decode("ACTUALIZO DATOS DIRIGENTE :  ".$nuevo_nombre." ".$nuevo_apellidoP." ".$nuevo_apellidoM);
                $insertAccion = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                mysqli_query($cnn, $insertAccion);
                ?> <script type="text/javascript"> var RutUsu = "<?php echo $MuestroRut;?>";
                    var es = "0";
                    window.location = "man_dirigente.php?rut="+RutUsu+'&es='+es;
                </script><?php
            }
        ?>
    </body>
</html>