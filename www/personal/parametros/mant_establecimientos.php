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
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $id_formulario = 1;
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
        <script type="text/javascript">
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $(".dropdown-button").dropdown();
                // $(".button-collapse").sideNav();
                $('.sidenav').sidenav();
                $('.tooltipped').tooltip({delay: 50});
            });
            function Cargar(){
                $("#actualizar").hide( 1 );
                $("#agregar").hide( 1 );
                $("#nom_est").hide(1);
                $("#lb_nom_e").hide(1);
                $("#dir_est").hide(1);
                $("#lb_dir_e").hide(1);
                //$("#nom_est").removeAttr("disabled");
                //$("#comuna").attr("disabled","disabled");
            }
            function Respuesta(r){
                //revivo los datos desde php(datos_establecimiento)
                $("#agregar").hide( 1 );
                $("#id_est").val(r.id_establecimientos);
                $("#nom_est").val(r.nombre_establecimientos);
                $("#dir_est").val(r.direccion_establecimiento);
                $("#nom_est").show("slow");
                $("#lb_nom_e").show("slow");
                $("#dir_est").show("slow");
                $("#lb_dir_e").show("slow");
                $("#actualizar").show("slow");
            }
            function Editar(){
                //variables, que hacen referencia los elementos de la pagina por sus ID.
                var slt_esta = $("#establecimiento").val();
                if(slt_esta == "no"){
                    Materialize.toast('Debe seleccionar un establecimiento o la opcion de agregar uno nuevo', 4000);
                    $("#establecimiento").focus();
                }
                if(slt_esta == "nuevo"){
                    $("#actualizar").hide( 1 );
                    $("#nom_est").val("");
                    $("#nom_est").show("slow");
                    $("#lb_nom_e").show("slow");
                    $("#dir_est").show("slow");
                    $("#lb_dir_e").show("slow");
                    $("#agregar").show("slow");
                }else{
                    var datos = {"id_est":slt_esta};
                    var post = $.post("../php/datos_establecimiento.php", datos, Respuesta, 'json');
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
        </script>
    </head>
    <body onLoad="Cargar();">
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
                        <h4 class="light">Establecimientos</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                                <div class="row">
                                    <div class="col s12 m12 m12 l2">&nbsp&nbsp&nbsp</div>
                                    <div class="col s12 m12 l8">
                                        <div class="row">
                                            <div class="input-field col s9">
                                                <input style="display:none" id="id_est" type="text" class="validate" name="id_est">  
                                            </div>
                                            <div class="input-field">
                                                <select name="establecimiento" id="establecimiento" onchange="Editar();">
                                                <?php
                                                    $mostrarEstablecimientos="SELECT EST_ID, EST_NOM FROM ESTABLECIMIENTO";
                                                    $resultado =mysqli_query($cnn, $mostrarEstablecimientos);
                                                    while($reg=mysqli_fetch_array($resultado)){
                                                        printf("<option value=\"$reg[0]\">$reg[1]</option>");
                                                    }
                                                    echo "<option value='nuevo'>Agregar un Establecimiento</option>";
                                                    echo "<option value='no' disabled selected>Seleccione Establecimiento</option>";
                                                ?>
                                                </select>
                                                <label for="icon_prefix">Establecimiento</label>
                                            </div>
                                            <!-- ACA CARGARA LOS CAPOS PARA EDITAR LOS ESTABLECIMIENTOS -->
                                            <div class="input-field col s12" >
                                                <input id="nom_est" type="text" class="validate" name="nom_est" required onkeypress="return soloLetras(event)" placeholder=""> 
                                                <label for="icon_prefix" id="lb_nom_e" name="lb_nom_e">Nombre Establecimiento</label> 
                                            </div>
                                            <div class="input-field col s12" >
                                                <input id="dir_est" type="text" class="validate" name="dir_est" required placeholder=""> 
                                                <label for="icon_prefix" id="lb_dir_e" name="lb_dir_e">Direccion Establecimiento</label> 
                                            </div>
                                            <button class="btn trigger" type="reset" name="cancelar" value="cancelar" onclick = "Cargar();">Cancelar</button>
                                            <button id="actualizar" class="btn trigger" type="submit" name="actualizar" value="Actualizar">Actualizar</button>
                                            <button id="agregar" class="btn trigger" type="submit" name="agregar" value="Agregar">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
        <!-- fin contenido pagina -->   
        <!--<footer class="page-footer orange col l6 s12" style="position: fixed; bottom: 0; width: 100%; z-index: 9999;">
            <div class="footer-copyright">
                <div class="container">
                    <a class="grey-text text-lighten-4 right">© 2017 Unidad de Informatica - Direccion de Salud Municipal - Rengo.</a>
                </div>
            </div>
        </footer>-->     
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
            });
        </script>
        <?php
            if($_POST['agregar'] == "Agregar"){
                //btn guardar
                $nom_esta = $_POST['nom_est'];
                $dir_esta = $_POST['dir_est'];
                $guardar = "INSERT INTO ESTABLECIMIENTO (EST_NOM,EST_ESTA,EST_DIR) VALUES ('$nom_esta', 'ACTIVO','$dir_esta')";
                $accionRealizada = utf8_decode("CREO NUEVO ESTABLECIMIENTO : ".$nom_esta);
                $insertAccion = "INSERT INTO LOG_ACCION (LA_ACC,FOR_ID,USU_RUT,LA_IP_USU,LA_FEC,LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                mysqli_query($cnn, $insertAccion);
                mysqli_query($cnn, $guardar);
                ?><script type="text/javascript"> window.location="mant_establecimientos.php";</script> <?php
            }
            if($_POST['actualizar'] == "Actualizar"){
                //btn actualizar
                $id_esta = $_POST['id_est'];
                $nom_esta = $_POST['nom_est'];
                $dir_esta = $_POST['dir_est'];
                $actualizar= "UPDATE ESTABLECIMIENTO SET EST_NOM = '$nom_esta', EST_DIR = '$dir_esta'  WHERE (EST_ID = '$id_esta')";
                //rescato nombre del establecimiento
                $queryEstablecimientos = "SELECT EST_ID,EST_NOM FROM ESTABLECIMIENTO WHERE (EST_ID ='".$id_esta."')";
                $resEstablecimientos = mysqli_query($cnn, $queryEstablecimientos);
                if (mysqli_num_rows($resEstablecimientos) != 0){
                    $rowE = mysqli_fetch_row($resEstablecimientos);
                    if ($rowE[0] == $id_esta){
                        $NombreEstablecimientoOld = utf8_encode($rowE[1]);
                    }
                }
                $accionRealizada = utf8_decode("ACTUALIZO NOMBRE ESTABLECIMIENTO : ".$NombreEstablecimientoOld. " A : ".$nom_esta);
                $insertAccion = "INSERT INTO LOG_aCCION (LA_ACC,FOR_ID,USU_RUT,LA_IP_USU,LA_FEC,LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                mysqli_query($cnn, $actualizar);
                mysqli_query($cnn, $insertAccion);
                ?> <script type="text/javascript"> window.location="mant_establecimientos.php";</script>  <?php
            }
        ?>
    </body>
</html>