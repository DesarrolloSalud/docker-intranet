<?php
    session_start();
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
        $Sestablecimiento = utf8_encode($_SESSION['EST_ID']);
    }   
?>
<html>
    <head>
        <title>Personal Salud</title>
        <meta charset="UTF-8">
        <!-- Le decimos al navegador que nuestra web esta optimizada para moviles -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <!-- Cargamos el CSS --> 
        <link type="text/css" rel="stylesheet" href="../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../include/css/materialize.min.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../include/css/custom.css" />
        <link href="../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    </head>
    <body>
        <!-- llamo el nav que tengo almacenado en un archivo -->
        <?php require_once('estructura/nav_personal.php');?>
        <!-- cuerpo pagina -->
        </br>
        </br>
        </br>
        </br>
        </br>
        </br>
        </br>
        </br>
        <div class="col s12" align="center">
        	<img class="responsive-img" src="../include/img/acceso-denegado.jpg">
        </div>
        <!-- inicia footer -->
        <footer class="page-footer orange col l6 s12" style="position: fixed; bottom: 0; width: 100%; z-index: 9999;">
            <div class="footer-copyright">
                <div class="container">
                    <a class="grey-text text-lighten-4 right">© 2017 Unidad de Informatica - Direccion de Salud Municipal - Rengo.</a>
                </div>
            </div>
        </footer>
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../include/js/jquery.js"></script>
        <script type="text/javascript" src="../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
            });
        </script>
    </body>
</html>