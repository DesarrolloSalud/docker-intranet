<?php 
	session_start(); 
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
	if(!isset($_SESSION['USU_RUT'])){
		session_destroy();
		header("location: ../index.php");
	}else{
		$Srut = utf8_encode($_SESSION['USU_RUT']);
		$Snombre = utf8_encode($_SESSION['USU_NOM']);
		$SapellidoP = utf8_encode($_SESSION['USU_APP']);
		$SapellidoM = utf8_encode($_SESSION['USU_APM']);
		$Semail = utf8_encode($_SESSION['USU_MAIL']);
		$Scargo = utf8_encode($_SESSION['USU_CAR']);
    $Sperfil = utf8_encode($_SESSION['USU_PER']);
		$Sestablecimiento = $_SESSION['EST_ID'];
		$Sdependencia = $_SESSION['USU_DEP'];
    $Sdependencia2 = $_SESSION['USU_DEP2'];
		$actualizacion = $_SESSION['ACTUALIZACIONES'];
		include ("../include/funciones/funciones.php");
		$cnn = ConectarAbastecimiento();
		date_default_timezone_set("America/Santiago");
		$fecha = date("Y-m-d");
		$hora = date("H:i:s");
		$accion = utf8_decode("INGRESO A INDEX");
		$ipcliente = getRealIP();
		$insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC,FOR_ID,USU_RUT,LA_IP_USU,LA_FEC,LA_HORA) VALUES ('$accion','0','$Srut', '$ipcliente', '$fecha', '$hora')";
		mysqli_query($cnn, $insertAcceso);
	}
?>
<html>
    <head>
        <title>Abastecimiento Salud</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link type="text/css" rel="stylesheet" href="../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../include/css/materialize.min.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../include/css/custom.css" />
        <link href="../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <style type="text/css">
            body{
                background-image: url("../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }
						.mi_informacion{
								font-size: 18px;
								font-weight: bold;
						}
        </style>
        <!--<script type="text/javascript" src="../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../include/js/materialize.js"></script>-->

    </head>
	<!--<body onload="Actualizacion();SubNivel();">-->
	<body>
		<!-- llamo el nav que tengo almacenado en un archivo -->
		<?php require_once('estructura/nav_abastecimiento.php');?>
        <!-- mostrar hora en php -->
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <div class="row">
                            <div class="col s12">
                                <ul class="tabs">
                                    <li class="tab col s4"><a href="#mensajes">ALERTAS DE BODEGA</a></li>
                                    <li class="tab col s4"><a class="active" href="#permisos">MIS DOCUMENTOS</a></li>
                                    <li class="tab col s4"><a href="#yo"></a></li>
                                </ul>
                            </div>
                            <div id="mensajes" class="col s12">
                            </div>
                            <div id="permisos" class="col s12">
                            </div>
                            <div id="yo" class="col s12">	
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <!-- footer -->
        <footer class="page-footer orange col l6 s12" style="position: fixed; bottom: 0; width: 100%; z-index: 9999;">
            <div class="footer-copyright">
                <div class="container">
                    <a class="grey-text text-lighten-4 right">© 2021 Unidad de Informatica - Direccion de Salud Municipal - Rengo.</a>
                </div>
            </div>
        </footer>
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../include/js/jquery.js"></script>
        <script type="text/javascript" src="../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
              $('.sidenav').sidenav();
              $(".dropdown-trigger").dropdown();
              $('.modal').modal();
              $('.tabs').tabs();
              $('select').formSelect();
            });
        </script>
    </body>
</html>
