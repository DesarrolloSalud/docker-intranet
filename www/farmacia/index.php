<?php
	session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>SistemaGestionEducacional</title>
        <meta charset="UTF-8">
        <!-- Le decimos al navegador que nuestra web esta optimizada para moviles -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <!-- Cargamos el CSS --> 
        <link type="text/css" rel="stylesheet" href="../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../include/css/materialize.min.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../include/css/custom.css" />
        <link href="../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <script type="text/javascript">
        function CargarIndex(){
            $("#btn_usuario").attr("disabled","disabled");
        }
        </script>
        <style type="text/css">
            body{
                background-image: url("../include/img/fondologinfarmacia.jpg");
            }
        </style>
    </head>
    <body onLoad="CargarIndex();">
        <nav class="light-blue accent-3" role="navigation" style="position: fixed; top: 0; width: 100%; z-index: 9999; height: 65px;">
            <div class="nav-wrapper">
                <a href="index.html" class="brand-logo right">Farmacia Popular</a>
            </div>
        </nav>
        <div class="container">
            <div class="row" style="position: fixed; top: 20%;">
                <div class="espaciado col s12 m8 l8 block" style="background-color:#fafafa; opacity: 0.9;">
                    <div class="icon-block">
                        <p class="light">   
                            <center>
                                <form method="post">
                                    <div class="input-field col s12">
                                        <input type="text" name="rut_usuario" id="rut_usuario" style="text-transform: uppercase" requiere autofocus>
                                        <label for="rut">Rut:</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <input type="password" id="pwd_usuario" name="pwd_usuario">
                                        <label for="contraseña">Contraseña:</label>
                                    </div>
                                    <br>
                                    <button class="btn waves-effect waves-light" type="submit" name="btn_usuario" id="btn_usuario" value="Ingresar">
                                        <i class="mdi-content-send">Ingresar</i>
                                    </button>
                                </form>
                            </center>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row" style="position: fixed; bottom: 10%; right: 10%">
                <div class="right col s12 m8 l8 block">
                    <div align="right"><h6><a href="../index.html" class="btn trigger">Volver</a></h6></div>
                </div>
            </div>
        </div>
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
        <script type="text/javascript" src="../include/js/materialize.min.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        alert('Rut incorrecto');
                        $("#btn_usuario").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                        $("#btn_usuario").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });
            });
        </script>
    </body>
</html>