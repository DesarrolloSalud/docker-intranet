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
                let preguntas = 13;
                let opciones = 5;
                for(let i = 2; i <= preguntas; i++){
                    for(let o = 1; o <= opciones; o++){
                        let opcion = `#${i}-${o}`;
                        $(opcion).attr("disabled","disabled");
                    }
                }
                $("#aceptar").attr("disabled","disabled");
            }

            const pasar = (pregunta) => {
                const siguiente = (pregunta < 13) ? pregunta + 1 : 'aceptar';
                if(siguiente === 'aceptar'){
                    $("#aceptar").removeAttr("disabled");
                    return
                }
                for(let o = 1; o <= 5; o++){
                    let opcion = `#${siguiente}-${o}`;
                    $(opcion).removeAttr("disabled");
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
                        <h4 class="light">ENCUESTA IMPACTO COVID 19 - CESFAM RIENZI VALENCIA</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                            </br></br></br>
                            <?php 
                            $preguntas = array(
                                "¿Se siente en general angustiada/(o) a raíz de contingencia COVID -19?",
                                "¿Siente miedo, angustia o impotencia al pensar en la posibilidad de contagiarse con la enfermedad y/o contagiar a familiares?",
                                "¿Se siente debidamente protegido para trabajar con los elementos de protección personal?",
                                "¿Existe espíritu de equipo? ¿Se funciona como equipo en su unidad?",
                                "¿Siente que se toma en cuenta su opinión?",
                                "¿Siente que ha sido efectiva la entrega y manejo de la información por parte de la institución?",
                                "¿Considera que ha mejorado la disponibilidad de los aparatos de protección?",
                                "¿Siente frustración o tristeza debido al período de distanciamiento físico?",
                                "¿Tiene problemas para dormir?",
                                "¿Existe una persona en la institución con la cual pueda conversar sus cosas personales?",
                                "¿Participaría de alguna actividad lúdica tipo pausa activa u otra durante su jornada laboral?",
                                "¿Participaría de alguna actividad tipo conversación grupal reflexiva durante su jornada laboral con su unidad?",
                                "¿Consideraría apropiado la implementación de un buzón de sugerencias respecto a esta contingencia?",
                            );
                            for($i = 0; $i < count($preguntas); $i++){
                                $num_pregunta = $i + 1;
                                echo '<div class="col s12" align="left"><h6>'.$num_pregunta.' - '.$preguntas[$i].'</h6></div>';
                                echo '<div class="col s12">';
                                  echo '<label>';
                                  echo '<input name="'.$num_pregunta.'" type="radio" value="1" id="'.$num_pregunta.'-1" onclick="pasar('.$num_pregunta.');"/>';
                                  echo '<span>1 - Nunca</span>';
                                  echo '</label>';
                                  echo '<label>';
                                  echo '<input name="'.$num_pregunta.'" type="radio" value="2" id="'.$num_pregunta.'-2" onclick="pasar('.$num_pregunta.');"/>';
                                  echo '<span>2 - Escasamente</span>';
                                  echo '</label>';
                                  echo '<label>';
                                  echo '<input name="'.$num_pregunta.'" type="radio" value="3" id="'.$num_pregunta.'-3" onclick="pasar('.$num_pregunta.');"/>';
                                  echo '<span>3 - Moderadamente</span>';
                                  echo '</label>';
                                  echo '<label>';
                                  echo '<input name="'.$num_pregunta.'" type="radio" value="4" id="'.$num_pregunta.'-4" onclick="pasar('.$num_pregunta.');"/>';
                                    echo '<span>4 - Casi Siempre</span>';
                                    echo '</label>';
                                    echo '<label>';
                                    echo '<input name="'.$num_pregunta.'" type="radio" value="5" id="'.$num_pregunta.'-5" onclick="pasar('.$num_pregunta.');"/>';
                                    echo '<span>5 - Siempre</span>';
                                  echo '</label>';
                                echo '</div>';

                            }
                            ?>
                            
                            </br></br>
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
                $respuesta = array();
                for($i = 0; $i < count($preguntas); $i++){
                    $num_pregunta = $i + 1;
                    $resp = $_POST[$num_pregunta];
                    array_push($respuesta, $resp);
                }
            $insertAccion = "INSERT INTO RIENZI_2021 (USU_RUT, P1, P2, P3, P4, P5, P6, P7, P8, P9,P10,P11,P12,P13) VALUES ('$Srut',$respuesta[0],$respuesta[1],$respuesta[2],$respuesta[3],$respuesta[4],$respuesta[5],$respuesta[6],$respuesta[7],$respuesta[8],$respuesta[9],$respuesta[10],$respuesta[11],$respuesta[12])";
            echo $insertAccion;
            mysqli_query($enc, $insertAccion);
            ?> <script type="text/javascript"> window.location="index.php";</script> <?php
            }
        ?>
    </body>
</html>