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
        function soloNumeros(e){
          var key = window.Event ? e.which : e.keyCode
          return ((key >= 48 && key <= 57) || key == 75 || key == 107 )
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
                                        <input type="text" name="rut_usuario" id="rut_usuario" style="text-transform: uppercase" onkeypress="return soloNumeros(event)" placeholder="" requiere autofocus>
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
      <div class="row">
        <div class="col s6">
          <a href="http://200.68.34.158/include/INSTRUCTIVO.pdf" target="_blank"><img class="responsive-img" src="../include/img/index_principal_1.png" style="position: absolute; top: 15%; right: 5%; z-index: 100;opacity: 0.9;"></a>
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
                //$(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                //$(".button-collapse").sideNav();
                $('.tooltipped').tooltip({delay: 50});
                $("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        M.toast({html: 'Rut incorrecto'});
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
            require_once("../include/funciones/funciones2.php");
            $cnn = ConectarPersonal();
            $cnn_e = ConectarEncuestas();
            date_default_timezone_set("America/Santiago");
            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            $ipcliente = getRealIP();
            $id_formulario = 9;
            if($_POST['btn_usuario'] == "Entrar"){
                $pwd = md5($_POST['pwd_usuario']);
                $rut_usu1 = ($_POST['rut_usuario']);        
               
                /* crear una sentencia preparada */
              if ($stmt = mysqli_prepare($cnn, "SELECT USU_RUT, USU_NOM, USU_APP, USU_APM, USU_MAIL, USU_DIR, USU_FONO, USU_CAR, EST_ID, USU_DEP, USU_ESTA, USU_PAS, USU_CAT,USU_NIV,USU_JEF, USU_FIR, DATE_FORMAT(USU_FEC_ING,'%d-%m-%Y'), USU_CONTRA, USU_PROF, DATE_FORMAT(USU_FEC_INI,'%d-%m-%Y'), DATE_FORMAT(USU_FEC_NAC,'%d-%m-%Y'), USU_SEXO,USU_NACIONAL, USU_RECON, USU_TRAMO FROM USUARIO WHERE USU_RUT =?")) {
                  /* parámetro para consulta */
                  mysqli_stmt_bind_param($stmt, "s", $rut_usu1);
                  /* ejecutar la consulta */
                  mysqli_stmt_execute($stmt);
                  /* asigna resultados, variables pueden ser cualquiera, pero el orden lo da la consulta */
                  mysqli_stmt_bind_result($stmt, $rut, $nom, $app, $apm, $mail, $dir, $fono, $car, $estid, $dep, $esta, $pas, $cat, $niv, $jef, $fir, $fecing, $contra, $prof, $fecini, $fecnac, $sexo, $nacional, $recon, $tramo);
                  /* obtener valor */
                 while (mysqli_stmt_fetch($stmt)) {                    
                    $rut = $rut;
                    $nom = $nom;
                    $app = $app;
                    $apm = $apm;
                    $mail = $mail;
                    $dir = $dir;
                    $fono = $fono;
                    $car = $car; //director
                    $estid = $estid; //id establecimiento
                    $dep = $dep;// id establecimiento del que depende
                    $esta = $esta; //estado
                    $pas = $pas; //clave
                    $cat = $cat; //categoria
                    $niv = $niv; //nivel
                    $jef = $jef; //si es jefatura
                    $fir = $fir; //firma
                    $fecing = $fecing; //fecha ingreso al servicio
                    $contra  = $contra; //tipo de contrato
                    $prof    = $prof; //profesion
                    $fecini = $fecini; //fecha ingrso salud publica
                    $fecnac = $fecnac; //fecha nacimiento
                    $sexo	= $sexo;
                    $nacional = $nacional; //nacionalidad
                    $recon		= $recon; //número reloj control
                    $tramo		= $tramo;
                  }
                /* cerrar consulta */
                  mysqli_stmt_close($stmt);
              }
   
                    if ($esta == "ACTIVO"){
                        if($pwd == $pas){  
                            //que hacer en caso de contraseña correcta
                            $USU_RUT_AC = $rut;
                            $accion = "INGRESO";
                            $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$USU_RUT_AC', '$ipcliente', '$fecha', '$hora')";
                            mysqli_query($cnn, $insertAcceso);
                            $_SESSION['USU_RUT'] = $rut;
                            $_SESSION['USU_NOM'] = $nom;
                            $_SESSION['USU_APP'] = $app;
                            $_SESSION['USU_APM'] = $apm;
                            $_SESSION['USU_MAIL'] = $mail;
                            $_SESSION['USU_DIR'] = $dir;
                            $_SESSION['USU_FONO'] = $fono;
                            $_SESSION['USU_CAR'] = $car; //director
                            $_SESSION['EST_ID'] = $estid; //id establecimiento
                            $_SESSION['USU_DEP'] = $dep;// id establecimiento del que depende
                            $_SESSION['USU_DEP2'] = $dep;
                            $_SESSION['USU_ESTA'] = $esta; //estado
                            $_SESSION['USU_PAS'] = $pas; //clave
                            $_SESSION['USU_CAT'] = $cat; //categoria
                            $_SESSION['USU_NIV'] = $niv; //nivel
                            $_SESSION['USU_JEF'] = $jef; //si es jefatura
                            $_SESSION['USU_FIR'] = $fir; //firma
                            $_SESSION['USU_FEC_ING'] = $fecing; //fecha ingreso al servicio
                            $_SESSION['USU_CONTRA']  = $contra; //tipo de contrato
                            $_SESSION['USU_PROF']    = $prof; //profesion
                            $_SESSION['USU_FEC_INI'] = $fecini; //fecha ingrso salud publica
                            $_SESSION['USU_FEC_NAC'] = $fecnac; //fecha nacimiento
                            $_SESSION['USU_SEXO']	= $sexo;
                            $_SESSION['USU_NACIONAL'] = $nacional; //nacionalidad
                            $_SESSION['USU_RECON']		= $recon; //número reloj control
                            $_SESSION['USU_TRAMO']		= $tramo;
                            $_SESSION['ACTUALIZACIONES']		= "SI";
                            ?><!--<script type="text/javascript">window.location ="index.php";</script>-->
      
                              <script type="text/javascript">window.location ="index.php";</script><?php
                        }else{
                            //que hacer en caso de contraseña incorrecta
                            $USU_RUT_AC = $rut;
                            $accion = utf8_decode("USUARIO O CONTRASEÑA INCORRECTA");
                            $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$USU_RUT_AC', '$ipcliente', '$fecha', '$hora')";
                            mysqli_query($cnn, $insertAcceso);
                            ?>
                            <script type="text/javascript">
                                M.toast({html: 'Usuario o Contraseña Incorrecta'});
                            </script>
                            <?php
                        }
                    }else{
                        
                        $USU_RUT_AC = "ERROR";
                        $accion = "USUARIO DESACTIVADO";
                        $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$USU_RUT_AC', '$ipcliente', '$fecha', '$hora')";
                        mysqli_query($cnn, $insertAcceso);
                        ?>
                        <script type="text/javascript">
                            M.toast({html: 'Favor comuníquese con Personal'});
                        </script>
                        <?php
                    }
                }                
        ?>
    </body>
</html>