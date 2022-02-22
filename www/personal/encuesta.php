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
        include ("../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        include ("../include/funciones/funciones2.php");
        $enc = ConectarEncuestas();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $reponder = "SELECT RUT,CAT,EST FROM HC2021 WHERE (RUT = '$Srut' AND ESTADO = 'PENDIENTE')";
        $ver = mysqli_num_rows(mysqli_query($enc, $reponder));
        $res = mysqli_query($enc, $reponder);
        if($ver != 1){
            echo $reponder;
        //   header("location: index.php");
        }else{
          while ($row = mysqli_fetch_array($res)){
              $rut  = $row[0];
              $cat = $row[1];
              $est  = $row[2];
          }
          if($est == 1){
            // if($cat == "A" || $cat == "B"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 1 AND (CAT = 'A' OR CAT = 'B'))";
            // }elseif($cat == "C"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 1 AND CAT = 'C')";
            // }elseif($cat == "E" || $cat == "F"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 1 AND (CAT = 'E' OR CAT = 'F'))";
            // }
            if($cat == "C"){
                $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 1 AND CAT = 'C' AND (RUT = '11.529.128-9' OR RUT = '12.725.377-3' OR RUT = '13.944.114-1' OR RUT = '15.526.128-5'))"
            }
          }elseif($est == 2){      
            // if($cat == "A"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 2 AND CAT = 'A')";
            // }elseif($cat == "B"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 2 AND CAT = 'B')";
            // }elseif($cat == "C"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 2 AND CAT = 'C')";
            // }elseif($cat == "E"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 2 AND CAT = 'E')";
            // }elseif($cat == "F"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 2 AND CAT = 'F')";
            // }
            if($cat == "A"){
                $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 2 AND CAT = 'A' AND (RUT = '15.746.987-8' OR RUT = '16.766.402-4' OR RUT = '25.648.251-7'))";
            }elseif($cat == "C"){
                $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 2 AND CAT = 'C' AND (RUT = '13.345.144-7' OR RUT = '16.528.727-4'))";
            }elseif($cat == "E"){
                $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 2 AND CAT = 'E' AND (RUT = '10.644.582-6' OR RUT = '14.612.283-3' OR RUT = '15.922.141-5'))";
            }elseif($cat == "F"){
                $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 2 AND CAT = 'F' AND (RUT = '7.591.084-3' OR RUT = '9.949.713-0' OR RUT = '12.725.793-0' OR RUT = '14.356.368-5'))";
            }  
          }elseif($est == 3){
            // if($cat == "A"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 3 AND CAT = 'A')";
            // }elseif($cat == "B"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 3 AND CAT = 'B')";
            // }elseif($cat == "C" || $cat == "D"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 3 AND (CAT = 'C' OR CAT = 'D'))";
            // }elseif($cat == "E"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 3 AND CAT = 'E')";
            // }elseif($cat == "F"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 3 AND CAT = 'F')";
            // }
          }elseif($est == 10001){
            // if($cat == "A"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 10001 AND CAT = 'A')";
            // }elseif($cat == "B"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 10001 AND CAT = 'B')";
            // }elseif($cat == "C" || $cat == "D"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 10001 AND (CAT = 'C' OR CAT = 'D'))";
            // }elseif($cat == "E" || $cat == "F"){
            //     $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 10001 AND (CAT = 'E' OR CAT = 'F'))";
            // }
            if($cat == "A"){
                $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 10001 AND CAT = 'A' AND (RUT = '16.558.087-7' OR RUT = '18.021.970-6'))";
            }elseif($cat == "C"){
                $consulta = "SELECT RUT,NOM FROM HC2021 WHERE (EST = 10001 AND (CAT = 'E' OR CAT = 'F') AND (RUT = '14.356.930-6' OR RUT = '17.793.945-5'))";
            }
          }
        }
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
                $("#aceptar").attr("disabled","disabled");
            }
            function enviar(){
                let voto1 = $("#voto1").val();
                let voto2 = $("#voto2").val();
                if(voto1 != "no" && voto2 != "no"){
                    $("#aceptar").removeAttr("disabled");
                }else{
                    M.toast({html: 'debe seleccionar ambos botos'});
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
                        <h4 class="light">VOTACION CALIFICACIONES 2021</h4>
                        <p>
                        Votacion para seleccionar pares proceso calificaciones 2021.
                        </p>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                            </br></br></br>
                            <?php
                            echo "<div class='col s12'>Favor seleccionar eleccion</div>";
                            echo "</br>";
                            echo '<div class="input-field col s12">';
                                echo '<select name="voto1" id="voto1">';
                                    $resultado =mysqli_query($enc, $consulta);
                                    echo '<option selected value="no">SELECCIONAR</option>';
                                    while($reg =mysqli_fetch_array($resultado)){
                                        printf("<option value=\"$reg[0]\">$reg[1]</option>");
                                    }
                                echo '</select>';
                            echo '<label for="voto1">Voto 1</label>';
                            echo '</div>';
                            echo "</br>";
                            echo '<div class="input-field col s12">';
                                echo '<select name="voto2" id="voto2" onchange="enviar();">';
                                    $resultado =mysqli_query($enc, $consulta);
                                    echo '<option selected value="no">SELECCIONAR</option>';
                                    while($reg =mysqli_fetch_array($resultado)){
                                        printf("<option value=\"$reg[0]\">$reg[1]</option>");
                                    }
                                echo '</select>';
                            echo '<label for="voto2">Voto 2</label>';
                            echo '</div>';
                            ?>
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
                $voto1 = $_POST['voto1'];
                $voto2 = $_POST['voto2'];
                $insertAccion = "UPDATE HC2021 SET ESTADO = 'LISTO', VOTO1 = '$voto1', VOTO2 = '$voto2' WHERE RUT = '$Srut'";
                mysqli_query($enc, $insertAccion);
                ?> <script type="text/javascript"> window.location="index.php";</script>  <?php

            }
        ?>
    </body>
</html>