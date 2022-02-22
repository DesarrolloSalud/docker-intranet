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
        <title>Abastecimiento Salud</title>
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
                <a href="index.php" class="brand-logo right">Abastecimiento de Salud</a>
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
        <!--<div class="col s6">
          <a href="http://200.68.34.158/include/INSTRUCTIVO.pdf" target="_blank"><img class="responsive-img" src="../include/img/index_principal_1.png" style="position: absolute; top: 15%; right: 5%; z-index: 100;opacity: 0.9;"></a>
        </div>-->
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
			$cnn = ConectarAbastecimiento();
			date_default_timezone_set("America/Santiago");
			$fecha = date("Y-m-d");
			$hora = date("H:i:s");
			$ipcliente = getRealIP();
			$id_formulario = 9;			
			if($_POST['btn_usuario'] == "Entrar"){
				$pwd=hash('sha256', $_POST['pwd_usuario']); 
				$rut = ($_POST['rut_usuario']);    
				$pst = $cnn->prepare("select USU_RUT, USU_NOM, USU_APP, USU_APM, USU_MAIL, USU_FONO, USU_CAR, EST_ID, USU_DEP, USU_ESTA, USU_PAS, USU_PERFIL, USU_FIR from USUARIO where USU_RUT=? and USU_PAS=?");
				$pst->execute([$rut, $pwd]);
				$resultado = $pst->fetchAll();
				foreach($resultado as $row){     
					if($row['USU_ESTA'] == 'ACTIVO'){
						$_SESSION['USU_RUT'] = $row['USU_RUT'];
						$_SESSION['USU_NOM'] = $row['USU_NOM'];
						$_SESSION['USU_APP'] = $row['USU_APP'];
						$_SESSION['USU_APM'] = $row['USU_APM'];
						
						$accion = utf8_decode("INGRESO");									
						$pdo1 = $cnn->prepare('insert into LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES (?,?,?,?,?,?)');
						$pdo1->bindParam(1, $accion, PDO::PARAM_STR,255);
						$pdo1->bindParam(2, $id_formulario, PDO::PARAM_INT);
						$pdo1->bindParam(3, $rut, PDO::PARAM_STR,12);
						$pdo1->bindParam(4, $ipcliente, PDO::PARAM_STR,15);
						$pdo1->bindParam(5, $fecha,PDO::PARAM_STR);
						$pdo1->bindParam(6, $hora,PDO::PARAM_STR);
						$pdo1->execute(); 
			?><script>window.location="http://200.68.34.158/abastecimiento/index.php";</script><?php
					}else{
						?>
						<script type="text/javascript">
							M.toast({html: 'Usuario o Contraseña Incorrecta'});
						</script>
						<?php
						$accion = utf8_decode("USUARIO O CONTRASEÑA INCORRECTA");									
						$pdo1 = $cnn->prepare('insert into LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES (?,?,?,?,?,?)');
						$pdo1->bindParam(1, $accion, PDO::PARAM_STR,255);
						$pdo1->bindParam(2, $id_formulario, PDO::PARAM_INT);
						$pdo1->bindParam(3, $rut, PDO::PARAM_STR,12);
						$pdo1->bindParam(4, $ipcliente, PDO::PARAM_STR,15);
						$pdo1->bindParam(5, $fecha,PDO::PARAM_STR);
						$pdo1->bindParam(6, $hora,PDO::PARAM_STR);
						$pdo1->execute(); 			
					}
				}
			}
			?>
	</body>
</html>