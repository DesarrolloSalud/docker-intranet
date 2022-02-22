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
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $id_formulario = 8;
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
                $('select').material_select();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $('.tooltipped').tooltip({delay: 50});
                $('.datepicker').pickadate({
                    firstDay: true
                });
            });
            function Cargar(){
                $("#ver").hide( 1 );
                $("#Ftermino").hide( 1 );
                $("#Finicio").hide(1);
                $('select').material_select('destroy');
                $("#lbusuarios").hide( 1 );
                $("#lbfinicio").hide( 1 );
                $("#lbftermino").hide(1);
                //$("#nom_est").removeAttr("disabled");
                //$("#comuna").attr("disabled","disabled");
            }
            function MFechas(){
                $('select').material_select('destroy');
                $("#lbusuarios").hide( 1 );
                $("#ver").show("slow");
                $("#Ftermino").show("slow");
                $("#Finicio").show("slow");
                $("#lbfinicio").show("slow");
                $("#lbftermino").show("slow");
            }
            function MNombres(){
                $('select').material_select();
                $("#lbusuarios").show("slow");
                $("#ver").show("slow");
                $("#Ftermino").hide( 1 );
                $("#Finicio").hide( 1 );
                $("#lbfinicio").hide( 1 );
                $("#lbftermino").hide( 1 );
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
                        <h4 class="light">Log de Registros</h4>
                        </br>
                        </br>
                        </br>
                        <form name="form" class="col s12" method="post" action="visor_registros.php">
                            <div class="col s4">
                                <input class="with-gap col s4" name="seleccion" value="fecha" type="radio" id="BFecha" onclick="MFechas();"/>
                                <label for="BFecha">Busqueda por fecha</label>
                            </div>
                            <div class="col s4">
                            </div>
                            <div class="col s4">  
                                <input class="with-gap col s4" name="seleccion" value="id_usuario" type="radio" id="BNombre" onclick="MNombres();"/>
                                <label for="BNombre">Busqueda por Nombre</label>
                            </div>
                            </br>
                            </br>
                            <div class="input-field col s12">
                                <select name="usuario" id="usuario">
                                <?php
                                    $mostrarUsuarios="SELECT USU_RUT,USU_NOM,USU_APP,USU_APM FROM USUARIO";
                                    $resultado =mysqli_query($cnn, $mostrarUsuarios);
                                    while($reg=mysqli_fetch_array($resultado)){
                                        $MostrarNombre = utf8_encode($reg[1]);
                                        $MostrarApellidoP = utf8_encode($reg[2]);
                                        $MostrarApellidoM = utf8_encode($reg[3]);
                                        printf("<option value=\"$reg[0]\">$MostrarNombre $MostrarApellidoP $MostrarApellidoM</option>");
                                    }
                                    echo "<option value='no' disabled selected>Seleccione Usuario</option>";
                                ?>
                                </select>
                                <label for="icon_prefix" id="lbusuarios">Usuarios</label>
                            </div>    
                            <div class="input-field col s6">
                                <input type="date" class="datepicker" name="Finicio" id="Finicio" placeholder="Fecha de Inicio" required> 
                                <label for="icon_prefix" id="lbfinicio"></label>
                            </div> 
                            <div class="input-field col s6">
                                <input type="date" class="datepicker" name="Ftermino" id="Ftermino" placeholder="Fecha de Termino" required>
                                <label for="icon_prefix" id="lbftermino"></label>
                            </div>
                            <div align="center">
                                <button id="ver" class="btn trigger" type="submit" name="ver" value="Ver">Mostrar</button>
                            </div>                    
                        </form>
                    </div>
                </div>
            </div>
        </div>   
        <!-- fin contenido pagina 
        <footer class="page-footer orange col l6 s12" style="position: fixed; bottom: 0; width: 100%; z-index: 9999;">
            <div class="footer-copyright">
                <div class="container">
                    <a class="grey-text text-lighten-4 right">© 2017 Unidad de Informatica - Direccion de Salud Municipal - Rengo.</a>
                </div>
            </div>
        </footer> -->   
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
    </body>
</html>