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
?>
<!DOCTYPE html>
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
        <script type="text/javascript">
        function CargarIndex(){
            $("#btn_usuario").attr("disabled","disabled");
        }
        </script>
        <style type="text/css">
            body{
                background-image: url("../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }

        </style>
    </head>
    <body onLoad="CargarIndex();">
        <nav class="light-blue accent-3" role="navigation" style="position: fixed; top: 0; width: 100%; z-index: 9999; height: 65px;">
            <div class="nav-wrapper">
                <a href="index.php" class="brand-logo right">Personal de Salud</a>
            </div>
        </nav>
        <div class="container">
            <div class="row" style="position: fixed; top: 20%;">
                <div class="espaciado col s12 m8 l8 block" style="background-color:#fafafa; opacity: 0.9;">
                    <div class="icon-block">
                        <p class="light">   
                            <center>
                                <form method="post" autocomplete="off">
                                    <div class="input-field col s12">
                                        <input type="text" name="rut_usuario" id="rut_usuario" style="text-transform: uppercase" placeholder="" requiere autofocus>
                                        <label for="rut">Rut:</label>
                                    </div>
                                    <div class="input-field col s12" autocomplete="off">
                                        <input type="password" id="pwd_usuario" name="pwd_usuario">
                                        <label for="contraseña">Contraseña:</label>
                                    </div>
                                    <br>
                                    <button class="btn waves-effect waves-light" type="submit" name="btn_usuario" id="btn_usuario" value="Entrar">
                                        <i class="mdi-content-send">Entrar</i>
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
                    <div align="right"><h6><a href="../index.php" class="btn trigger">Volver</a></h6></div>
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
        <script type="text/javascript" src="../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $('.tooltipped').tooltip({delay: 50});
                $("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        Materialize.toast('Rut incorrecto', 4000);
                        $("#btn_usuario").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                        $("#btn_usuario").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });
            });
        </script>
        <?php
            //encriptamos la clave ingresada para comparar la clave con la base de datos
            require_once("../include/funciones/funciones.php");
            $cnn = ConectarPersonal();
            date_default_timezone_set("America/Santiago");
            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            $ipcliente = getRealIP();
            $id_formulario = 9;
            if($_POST['btn_usuario'] == "Entrar"){
                $pwd = md5($_POST['pwd_usuario']);
                $query = "SELECT USU_RUT,USU_NOM,USU_APP,USU_APM,USU_MAIL,USU_DIR,USU_FONO,USU_CAR,EST_ID,USU_DEP,USU_ESTA,USU_PAS,USU_CAT,USU_NIV,USU_JEF,USU_FIR,
								DATE_FORMAT(USU_FEC_ING,'%d-%m-%Y'),USU_CONTRA,USU_PROF,DATE_FORMAT(USU_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(USU_FEC_NAC,'%d-%m-%Y'),USU_SEXO,USU_NACIONAL,USU_RECON,USU_TRAMO FROM USUARIO WHERE (USU_RUT ='". $_POST['rut_usuario']."')";
                $rs = mysqli_query($cnn, $query);
                if (mysqli_num_rows($rs) != 0){
                    $row = mysqli_fetch_row($rs);
                    if ($row[10] == "ACTIVO"){
                        if($pwd == $row[11]){
                          $_SESSION['USU_RUT']     = $row[0];
                          $_SESSION['USU_NOM']     = $row[1];
                          $_SESSION['USU_APP']     = $row[2];
                          $_SESSION['USU_APM']     = $row[3];
                          $_SESSION['USU_MAIL']    = $row[4];
                          $_SESSION['USU_DIR']     = $row[5];
                          $_SESSION['USU_FONO']    = $row[6];
                          $_SESSION['USU_CAR']     = $row[7]; //director
                          $_SESSION['EST_ID']      = $row[8]; //id establecimiento
                          $_SESSION['USU_DEP']     = $row[9]; // id establecimiento del que depende
                          $_SESSION['USU_ESTA']    = $row[10]; //estado
                          $_SESSION['USU_PAS']     = $row[11]; //clave
                          $_SESSION['USU_CAT']     = $row[12]; //categoria
                          $_SESSION['USU_NIV']     = $row[13]; //nivel
                          $_SESSION['USU_JEF']     = $row[14]; //si es jefatura
                          $_SESSION['USU_FIR']     = $row[15]; //firma
                          $_SESSION['USU_FEC_ING'] = $row[16]; //fecha ingreso al servicio
                          $_SESSION['USU_CONTRA']  = $row[17]; //tipo de contrato
                          $_SESSION['USU_PROF']    = $row[18]; //profesion
                          $_SESSION['USU_FEC_INI'] = $row[19]; //fecha ingrso salud publica
                          $_SESSION['USU_FEC_NAC'] = $row[20]; //fecha nacimiento
                          $_SESSION['USU_SEXO']		 = $row[21];
                          $_SESSION['USU_NACIONAL'] = $row[22]; //nacionalidad
                          $_SESSION['USU_RECON']		= $row[23]; //número reloj control
                          $_SESSION['USU_TRAMO']		= $row[24];
													$_SESSION['ACTUALIZACIONES']		= "SI"; 
                            //que hacer en caso de contraseña correcta
                            $USU_RUT_AC = $row[0];
                            $accion = "INGRESO";
                            $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$USU_RUT_AC', '$ipcliente', '$fecha', '$hora')";
                            mysqli_query($cnn, $insertAcceso);
                            ?><script type="text/javascript">window.location ="index.php";</script><?php
                        }else{
                            //que hacer en caso de contraseña incorrecta
                            $USU_RUT_AC = $row[0];
                            $accion = utf8_decode("CONTRASEÑA INCORRECTA");
                            $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$USU_RUT_AC', '$ipcliente', '$fecha', '$hora')";
                            mysqli_query($cnn, $insertAcceso);
                            ?>
                            <script type="text/javascript">
                                Materialize.toast('Error de Contraseña', 4000);
                            </script>
                            <?php
                        }
                    }else{
                        $USU_RUT_AC = $row[0];
                        $accion = "USUARIO DESACTIVADO";
                        $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$USU_RUT_AC', '$ipcliente', '$fecha', '$hora')";
                        mysqli_query($cnn, $insertAcceso);
                        ?>
                        <script type="text/javascript">
                            Materialize.toast('Usuario desactivado', 4000);
                        </script>
                        <?php
                    }
                }else{
                    $USU_RUT_AC = "ERROR";
                    $accion = "USUARIO NO EXISTE";
                    $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA)a, hora) VALUES ('$accion', '$id_formulario', '$USU_RUT_AC', '$ipcliente', '$fecha', '$hora')";
                    mysqli_query($cnn, $insertAcceso);
                    ?>
                    <script>
                        Materialize.toast('Usuario no Existe', 4000);
                    </script>
                    <?php
                }
            }
        ?>
    </body>
</html>