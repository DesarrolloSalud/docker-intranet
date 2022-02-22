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
		header("location: ../index.php");
	}else{
        if(count($_GET) && !$_SERVER['HTTP_REFERER']){
           header("location: ../error.php");
        }
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Sest_id = $_SESSION['EST_ID'];
        include ("../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        include ("../include/funciones/funciones2.php");
        $enc = ConectarEncuestas();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
	}	
?>
<html>
    <head>
        <title>Encuesta - Personal Salud</title>
        <meta charset="UTF-8">
        <!-- Le decimos al navegador que nuestra web esta optimizada para moviles -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <!-- Cargamos el CSS --> 
        <link type="text/css" rel="stylesheet" href="../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../include/css/materialize.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../include/css/custom.css" />
        <link href="../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <style type="text/css">
            body{
                background-image: url("../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }

        </style>
        <script type="text/javascript" src="../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
            function Cargar(){
                $("#2a").attr("disabled","disabled");
                $("#2b").attr("disabled","disabled");
                $("#2c").attr("disabled","disabled");
                $("#2d").attr("disabled","disabled");
                $("#2e").attr("disabled","disabled");
                $("#3a").attr("disabled","disabled"); 
                $("#3b").attr("disabled","disabled"); 
                $("#3c").attr("disabled","disabled"); 
                $("#aceptar").attr("disabled","disabled");
            }
            function pasar2(){
                $("#2a").removeAttr("disabled");
                $("#2b").removeAttr("disabled");
                $("#2c").removeAttr("disabled");
                $("#2d").removeAttr("disabled");
                $("#2e").removeAttr("disabled");
            }
            function pasar3(){
                $("#3a").removeAttr("disabled");
                $("#3b").removeAttr("disabled");
                $("#3c").removeAttr("disabled");
            }
            function guardar(){
                $("#aceptar").removeAttr("disabled");
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
    <body onload="Cargar();">
        <!-- llamo el nav que tengo almacenado en un archivo -->
        <?php require_once('estructura/nav_personal.php');?>
        <!-- inicio contenido pagina -->
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">ENCUESTA AUTOCUIDADO 2021</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                            </br></br></br>
                            <div class="col s12" align="left"><h6>1.	En relación a las actividades realizadas de autocuidado durante la pandemia (pausas activas, taller Mindfulness, medicina complementaria, un minuto para respirar, chikung, capsulas, etc); ¿consideras que te ayudaron a disminuir tu cansancio emocional o fueron de tu agrado para enfrentar la contingencia sanitaria?</h6></div>
                            <div class="col s12">
                              <label>
                                <input name="1" type="radio" value="a" id="1a" onclick="pasar2();"/>
                                <span>A) Totalmente de acuerdo</span>
                              </label>
                              <label>
                                <input name="1" type="radio" value="b" id="1b" onclick="pasar2();"/>
                                <span>B) Medianamente de acuerdo</span>
                              </label>
                              <label>
                                <input name="1" type="radio" value="c" id="1c" onclick="pasar2();"/>
                                <span>C) En desacuerdo</span>
                              </label>
                            </div>
                            </br></br>
                            <div class="col s12" align="left"><h6>2.	¿Consideras que existieron obstáculos o situaciones que limitaron tu participación en estos espacios?</h6></div>
                            <div class="col s12">
                              <label>
                                <input name="2" type="radio" value="a" id="2a" onclick="pasar3();"/>
                                <span>A) No existieron espacio protegidos para la actividad</span>
                              </label>
                              <label>
                                <input name="2" type="radio" value="b" id="2b" onclick="pasar3();"/>
                                <span>B) No contamos con espacio fisico</span>
                              </label>
                              <label>
                                <input name="2" type="radio" value="c" id="2c" onclick="pasar3();"/>
                                <span>C) Priorizaron actividades sanitarias por sobre el autocuidado</span>
                              </label>
                              <label>
                                <input name="2" type="radio" value="d" id="2d" onclick="pasar3();"/>
                                <span>D) No existieron obstáculos</span>
                              </label>
                              <label>
                                <input name="2" type="radio" value="e" id="2e" onclick="pasar3();"/>
                                <span>E) Solo abc</span>
                              </label>
                            </div>
                            </br></br>
                            <div class="col s12" align="left"><h6>3.	¿Consideras que los espacios de autocuidado son necesarios dentro de las labores diarias?</h6></div>
                            <div class="col s12">
                              <label>
                                <input name="3" type="radio" value="a" id="3a" onclick="guardar();"/>
                                <span>A) Siempre</span>
                              </label>
                              <label>
                                <input name="3" type="radio" value="b" id="3b" onclick="guardar();"/>
                                <span>B) Nunca</span>
                              </label>
                              <label>
                                <input name="3" type="radio" value="c" id="3c" onclick="guardar();"/>
                                <span>C) A veces</span>
                              </label>
                            </div>
                            <div class="input-field col s12">
                                <button class="btn trigger" type="submit" name="aceptar" id="aceptar" value="Autorizar">Enviar</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../include/js/materialize.js"></script>
        <?php
            if($_POST['aceptar'] == "Autorizar"){
              $voto1 = $_POST['1'];
              $voto2 = $_POST['2'];
              $voto3 = $_POST['3'];
              $insertAccion = "INSERT INTO AUTOCUIDADO_2021 (USU_RUT, EST_ID,primera, segunda, tercera) VALUES ('$Srut',$Sest_id,'$voto1','$voto2','$voto3')";
              mysqli_query($enc, $insertAccion);
              ?> <script type="text/javascript"> window.location="index.php";</script>  <?php
            }
        ?>
    </body>
</html>