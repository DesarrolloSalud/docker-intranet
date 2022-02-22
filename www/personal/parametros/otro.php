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
        if(count($_GET) && !$_SERVER['HTTP_REFERER']){
           header("location: ../error.php");
        }
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Snombre = utf8_encode($_SESSION['USU_NOM']);
        $SapellidoP = utf8_encode($_SESSION['USU_APP']);
        $SapellidoM = utf8_encode($_SESSION['USU_APM']);
        $Semail = utf8_encode($_SESSION['USU_MAIL']);
        $Scargo = utf8_encode($_SESSION['USU_CAR']);
        $Sestablecimiento = utf8_encode($_SESSION['EST_ID']);
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        if($Srut == "17.333.639-K" || $Srut == "15.738.663-8"){
          if($_POST['buscar'] == "buscar"){
            $query = "SELECT USU_RUT,USU_NOM,USU_APP,USU_APM,USU_MAIL,USU_DIR,USU_FONO,USU_CAR,EST_ID,USU_DEP,USU_ESTA,USU_PAS,USU_CAT,USU_NIV,USU_JEF,USU_FIR,
								DATE_FORMAT(USU_FEC_ING,'%d-%m-%Y'),USU_CONTRA,USU_PROF,DATE_FORMAT(USU_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(USU_FEC_NAC,'%d-%m-%Y'),USU_SEXO,USU_NACIONAL,USU_RECON,USU_TRAMO FROM USUARIO WHERE (USU_RUT ='". $_POST['rut_usuario']."')";
            $respuesta = mysqli_query($cnn,$query);
            $num_registros = mysqli_num_rows($respuesta);
            if (mysqli_num_rows($respuesta) != 0){
              $row = mysqli_fetch_row($respuesta);
              $usu_rut     = $row[0];
              $usu_nom     = utf8_encode($row[1]);
              $usu_app     = utf8_encode($row[2]);
              $usu_apm     = utf8_encode($row[3]);
              $usu_mail    = $row[4];
              $usu_dir     = $row[5];
              $usu_fono    = $row[6];
              $usu_car     = $row[7]; //director
              $est_id      = $row[8]; //id establecimiento
              $usu_dep     = $row[9]; // id establecimiento del que depende
              $usu_esta    = $row[10]; //estado
              $usu_pas     = $row[11]; //clave
              $usu_cat     = $row[12]; //categoria
              $usu_niv     = $row[13]; //nivel
              $usu_jef     = $row[14]; //si es jefatura
              $usu_fir     = $row[15]; //firma
              $usu_fec_ing = $row[16]; //fecha ingreso al servicio
              $usu_contra  = $row[17]; //tipo de contrato
              $usu_prof    = $row[18]; //profesion
              $usu_fec_ini = $row[19]; //fecha ingrso salud publica
              $usu_fec_nac = $row[20]; //fecha nacimiento
              $usu_sexo		 = $row[21];
              $usu_nacional= $row[22]; //nacionalidad
              $usu_recon		= $row[23]; //número reloj control
              $usu_tramo		= $row[24]; 
            }
          }
        }else{
          header("location: ../index.php");
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
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').material_select();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
            });
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
    <body>
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
                        <h4 class="light">Buscar usuario</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post" action="otro.php">
                                <div class="input-field col s6">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" placeholder="" value="<?php echo $usu_rut ?>">
                                    <label for="icon_prefix">RUT</label>
                                </div>
                                <div class="input-field col s6">
                                    <button class="btn trigger" type="submit" name="buscar" id="buscar" value="buscar">Buscar</button>
                                </div>
                                <?php
                                if($num_registros == 1){
                                  echo '<div class="col s12">';
                                    echo $usu_nom." ".$usu_app." ".$usu_apm;
                                  echo '</div>';
                                }
                                ?>
                                <div class="col s12">
                                    <button id="actualizar" class="btn trigger" type="submit" name="actualizar" value="Actualizar">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if($_POST['actualizar'] == "Actualizar"){
          $query = "SELECT USU_RUT,USU_NOM,USU_APP,USU_APM,USU_MAIL,USU_DIR,USU_FONO,USU_CAR,EST_ID,USU_DEP,USU_ESTA,USU_PAS,USU_CAT,USU_NIV,USU_JEF,USU_FIR,DATE_FORMAT(USU_FEC_ING,'%d-%m-%Y'),USU_CONTRA,USU_PROF,DATE_FORMAT(USU_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(USU_FEC_NAC,'%d-%m-%Y'),USU_SEXO,USU_NACIONAL,USU_RECON,USU_TRAMO FROM USUARIO WHERE (USU_RUT ='". $_POST['rut_usuario']."')";
          $respuesta = mysqli_query($cnn,$query);
          $num_registros = mysqli_num_rows($respuesta);
          if($num_registros == 1){
            //session_destroy();
            //session_start();
            if (mysqli_num_rows($respuesta) != 0){
              $row = mysqli_fetch_row($respuesta);
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
              $_SESSION['OTRO']         = 1;
              ?><script type="text/javascript">window.location ="../index.php";</script><?php
            }
          }
        }
        ?>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        Materialize.toast('Rut incorrecto', 4000);
                        $("#buscar").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                        $("#buscar").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });
            });
        </script>
    </body>
</html>