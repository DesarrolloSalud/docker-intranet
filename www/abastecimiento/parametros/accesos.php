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
		header("location: ../../error.php");
	}
	include ("../../include/funciones/funciones.php");
	$rut1 = $_GET['rut'];
	$cnn = ConectarAbastecimiento();
	$id_formulario = 2;
	$pst = $cnn->prepare("select USU_RUT,USU_NOM,USU_APP,USU_APM from USUARIO where USU_RUT=?");
  $pst->execute([$rut1]);
  $resultado = $pst->fetchAll();
  foreach($resultado as $row1){
    $rut1 = $row1['USU_RUT'];
    $nom  = utf8_encode($row1['USU_NOM']);
    $app = utf8_encode($row1['USU_APP']);
    $apm = utf8_encode($row1['USU_APM']);
  }
 // $cnn=null;  
	/*$Srut = utf8_encode($_SESSION['USU_RUT']);
	$Snombre = utf8_encode($_SESSION['USU_NOM']);
	$SapellidoP = utf8_encode($_SESSION['USU_APP']);
	$SapellidoM = utf8_encode($_SESSION['USU_APM']);
	$Semail = utf8_encode($_SESSION['USU_MAIL']);
	$Scargo = utf8_encode($_SESSION['USU_CAR']);
	$Sestablecimiento = utf8_encode($_SESSION['EST_ID']);


	date_default_timezone_set("America/Santiago");
	$fecha = date("Y-m-d");
	$ano1 =date("Y");
	$hora = date("H:i:s");
	$ipcliente = getRealIP();
	
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
	}*/
}	
?>
<html>
	<head>
		<title>Version desarrollo - Abastecimiento Salud</title>
		<meta charset="UTF-8">
		<!-- Le decimos al navegador que nuestra web esta optimizada para moviles -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<!-- Cargamos el CSS --> 
		<link type="text/css" rel="stylesheet" href="../../include/css/icon.css" />
		<link type="text/css" rel="stylesheet" href="../../include/css/materialize.css" media="screen,projection" />
		<link type="text/css" rel="stylesheet" href="../../include/css/custom.css" />
		<link href="../../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
		<style type="text/css">
			body{
				background-image: url("../../include/img/fondopersonal.jpg");
				background-size: cover;
				background-repeat: no-repeat;
			}
		</style>
		<script type="text/javascript" src="../../include/js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="../../include/js/jquery.min.js"></script> <!--PARA AJAX -->
		<script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
		<script type="text/javascript" src="../../include/js/materialize.js"></script>
		<script>
			function Activar(id){
				var rut1 = "<?php echo $rut1;?>";
        var formid = id;
        var estado ="ACT";  
        $.post( "../controller/actdes_acceso.php", { "rutusu" : rut1, "des" : formid, "est" : estado}, null, "json" )
        window.location="acceso_pag.php?rut="+rut1;
      }
      function Desactivar(id){        
        var rut2 = "<?php echo $rut1;?>";
        var formid = id;
        var estado="DES";
        $.post( "../controller/actdes_acceso.php", { "rutusu" : rut2, "des" : formid, "est" : estado}, null, "json" )
        window.location="acceso_pag.php?rut="+rut2;
      }
      function Volver(){
        window.location="usuario.php";
      }
		</script>
	</head>
	<body onload="Cargo();CambioEstablecimiento();CambioDependencia();">
		<!-- llamo el nav que tengo almacenado en un archivo -->
		<?php require_once('../estructura/nav_abastecimiento.php');?>
		<!-- inicio contenido pagina -->
		</br>
	</br>
</br>
<div class="container">
	<div class="section">
		<div class="row">
			<div class="col s12 center block" style="background-color: #ffffff">
				<h4 class="light">Accesos</h4>
				<div class="row" style="position: fixed; top: 15%; right: 20%">
					<div class="right col s12">
						<div align="right"><h6><a href="mant_usuario.php" class="btn trigger">Volver</a></h6></div>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $rut1."    ".$nom."  ".$app."  ".$apm;?>" disabled>
						<label for="icon_prefix">Funcionario</label>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<table class="table table-bordered">
		<thead> 
			<tr>
				<!-- definimos cabeceras de la tabla -->
				<th scope="col">ID</th>
				<th scope="col">Nombre</th>
				<th scope="col">Estado</th>
				<th scope="col">Acción</th>
			</tr>
		</thead>
		<tbody>
			<?php
			//include ("../../include/funciones/funciones.php");
			//$cnn = ConectarAbastecimiento(); 
			$sql = "SELECT FOR_ID, FOR_NOM FROM FORMULARIO WHERE FOR_ESTA='ACTIVO'";
			foreach ($cnn->query($sql) as $row){
				if($row['usu_esta']=="INACTIVO"){
					echo '<td>'.$row['FOR_ID'].'</td>';
					echo '<td>'.$row['FOR_NOM'].'</td>';
					echo '<td>'.$row['FOR_ESTA'].'</td>';
					echo '<td><button type="button" class="btn btn-success" onclick="Editar('; echo "'".$row['FOR_ID']."'"; echo');">Editar</button> <button type="button" class="btn btn-warning">Accesos</button> <button type="button" class="btn btn-primary" onclick="Activar('; echo "'".$row['FOR_ID']."'"; echo');">Activar</button> <button type="button" class="btn btn-danger" onclick="Resetear('; echo "'".$row['FOR_ID']."'"; echo');">Reset Clave</button></td>';
				}else{
					echo '<td>'.$row['FOR_ID'].'</td>';
					echo '<td>'.$row['FOR_NOM']." ".$row['usu_app']." ".$row['usu_apm'].'</td>';
					echo '<td>'.$row['FOR_ESTA'].'</td>';
					echo '<td><button type="button" class="btn btn-success" onclick="Editar('; echo "'".$row['FOR_ID']."'"; echo');">Editar</button> <button type="button" class="btn btn-warning" onclick="accesos('; echo "'".$row['FOR_ID']."'"; echo');">Accesos</button> <button type="button" class="btn btn-primary" onclick="Desactivar('; echo "'".$row['FOR_ID']."'"; echo');">Desactivar</button> <button type="button" class="btn btn-danger" onclick="Resetear('; echo "'".$row['FOR_ID']."'"; echo');">Reset Clave</button></td>';
				}
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
</div>
<!-- fin contenido pagina --> 
<!-- Cargamos jQuery y materialize js -->
<script type="text/javascript" src="../../include/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../include/js/jquery.min.js"></script> <!--PARA AJAX -->
<script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
<script type="text/javascript" src="../../include/js/materialize.js"></script>
<?php
if($_POST['guardar'] == "Guardar"){
?> <script type="text/javascript"> window.location="mant_usuarios.php";</script><?php
}
?>
</body>
</html>



