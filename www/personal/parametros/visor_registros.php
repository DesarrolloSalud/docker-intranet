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
                    $seleccion = $_POST['seleccion'];
                    $rut_usuario_recivida = $_POST['usuario'];
                    $fecha_inicio = $_POST['Finicio'];
                    $fecha_termino = $_POST['Ftermino'];
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
                        <h4 class="light">Visor de Registros</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 5%">
                            <div class="right col s12 m8 l8 block">
                                <div align="right"><h6><a href="registros_sistema.php" class="btn trigger">Volver</a></h6></div>
                            </div>
                        </div>                        
                        <table class="responsive-table bordered striped">
                            <thead>
                                <tr>
                                    <th>FECHA</th>
                                    <th>HORA</th>
                                    <th>PAGINA</th>
                                    <th>ACCION REALIZADA</th>
                                    <th>USUARIO</th>
                                    <th>IP USUARIO</th>
                                </tr>
                                <tbody>
                                    <!-- cargar la base de datos con php -->
                                    <?php
                                        if ($seleccion == "id_usuario"){
                                            $query = "SELECT DATE_FORMAT(LOG_ACCION.LA_FEC,'%d-%m-%Y'),LOG_ACCION.LA_HORA, FORMULARIO.FOR_NOM, LOG_ACCION.LA_ACC, USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, LOG_ACCION.LA_IP_USU FROM FORMULARIO, LOG_ACCION, USUARIO WHERE (LOG_ACCION.FOR_ID = FORMULARIO.FOR_ID) AND (LOG_ACCION.USU_RUT = USUARIO.USU_RUT) AND (USUARIO.USU_RUT = '$rut_usuario_recivida')";
                                        }else{
                                            $query = "SELECT DATE_FORMAT(LOG_ACCION.LA_FEC,'%d-%m-%Y'),LOG_ACCION.LA_HORA, FORMULARIO.FOR_NOM, LOG_ACCION.LA_ACC, USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, LOG_ACCION.LA_IP_USU FROM FORMULARIO, LOG_ACCION, USUARIO WHERE (LOG_ACCION.FOR_ID = FORMULARIO.FOR_ID) AND (LOG_ACCION.USU_RUT = USUARIO.USU_RUT) AND LOG_ACCION.LA_FEC BETWEEN '$fecha_inicio' AND '$fecha_termino'";
                                        }
                                        $respuesta = mysqli_query($cnn, $query);
                                        //recorrer los registros
                                        while ($row_rs = mysqli_fetch_array($respuesta)){
                                            echo "<tr>";
                                                echo "<td>".$row_rs[0]."</td>";
                                                echo "<td>".$row_rs[1]."</td>";
                                                echo "<td>".utf8_encode($row_rs[2])."</td>"; 
                                                echo "<td>".utf8_encode($row_rs[3])."</td>";
                                                echo "<td>".utf8_encode($row_rs[4])." ".utf8_encode($row_rs[5])." ".utf8_encode($row_rs[6])."</td>";
                                                echo "<td>".$row_rs[7]."</td>";      
                                            echo "</tr>";
                                        }
                                    ?>
                                </tbody>
                            </thead>
                        </table>
                        <!-- form oculto para guardar datos he imprimir informe de log en pdf -->
                        <form name="form" class="col s12" method="post" target="_blank" action="../pdf/infor_registros.php">
                            <input style="display:none" id="seleccion_pdf" type="text" class="validate" name="seleccion_pdf" value="<?php echo $seleccion; ?>">
                            <input style="display:none" id="rut_usr_pdf" type="text" class="validate" name="rut_usr_pdf" value="<?php echo $rut_usuario_recivida; ?>"> 
                            <input style="display:none" id="fec_inicio_pdf" type="text" class="validate" name="fec_inicio_pdf" value="<?php echo $fecha_inicio; ?>">
                            <input style="display:none" id="fec_termino_pdf" type="text" class="validate" name="fec_termino_pdf" value="<?php echo $fecha_termino; ?>">  
                            <div class="row" style="position: fixed; right: 5%; bottom: 5%">
                                <div class="right col s12 m8 l8 block">
                                    <button id="imprimir" class="btn trigger" type="submit" name="imprimir" value="Imprimir">PDF</button>
                                </div>
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