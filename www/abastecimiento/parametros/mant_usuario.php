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
	include ("../../include/funciones/funciones.php");
	$cnn = ConectarAbastecimiento();
	$id_formulario = 1;
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
			$(document).ready(function () {
				//Animaciones 
				$('select').formSelect();
				$('.sidenav').sidenav();
				$(".dropdown-trigger").dropdown();
				$('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
				$('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
				$("#rut_usuario").Rut({
					on_error: function(){
						M.toast({html: 'Rut incorrecto'});
						$("#buscar").attr("disabled","disabled");
					},
					on_success: function(){
						M.toast({html: 'Rut correcto'});
						$("#buscar").removeAttr("disabled");
					},
					format_on: 'keyup'
				});
				$('#cargo_usuario').change(function(){
					Cargo();
				});
			});
			function ValidoEmail(){
				var email = $('#email_usuario').val();
				expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				if ( !expr.test(email) ){
					M.toast({html: 'La dirección de correo ' + email + ' es incorrecta.'});
					$('#email_usuario').val("");
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
			function soloNumeros(e){
				var key = window.Event ? e.which : e.keyCode
				return (key >= 48 && key <= 57)
			}
			function Accesos(r){
        var rut1 = r;   
        window.location="accesos.php?rut="+rut1;
      }
			function Editar(r){
				var r = r;
				$.ajax({
					url: '../php/buscar.php',
					type: 'post',
					data: {request: 1, rut: r},
					dataType: 'json',
					success: function(response){
						var len = response.length;
						for( var i = 0; i<len; i++){
							//var rut = response[i]['rut'];
							$('#rut_usuario').val(response[i]['rut']);
							$('#nombre_usuario').val(response[i]['nom']);
							$('#apellidoP_usuario').val(response[i]['app']);
							$('#apellidoM_usuario').val(response[i]['apm']);
							$('#email_usuario').val(response[i]['correo']);
							$('#fono_usuario').val(response[i]['fono']);
							Cargo(response[i]['cargo']);
							CambioEstablecimiento(response[i]['estable']);
							CambioDependencia(response[i]['depende']);
						}
					}
				});
			}
			function Cargo(r){
				if(typeof r === "undefined"){
					r=0;
				}
				$('#cargo_usuario').find('option').remove();   
				$.ajax({
					url: '../php/buscar.php',
					type: 'post',
					data: {request: 2, id: r},
					dataType: 'json',
					success: function(response){
						var str= response;
						var n1 = str.indexOf("Error");
						if(n1 > -1){
							M.toast({html: str});
						}
						var len = response.length;
						for( var i = 0; i<len; i++){
							var id = response[i]['id'];
							var name = response[i]['name'];
							$("#cargo_usuario").append("<option attr='"+id+"'>"+name+"</option>");
							if(r != 0){
								$("#cargo_usuario").append("<option attr='0'>"+'Cambiar'+"</option>");
							}
						}
						$('#cargo_usuario').formSelect();
					}
				});
			}
			function CambioEstablecimiento(r){
				if(typeof r === "undefined"){
					r=0;
				}
				$('#establecimiento').find('option').remove();   
				$.ajax({
					url: '../php/buscar.php',
					type: 'post',
					data: {request:3, id: r},
					dataType: 'json',
					success: function(response){
						var str= response;
						var n1 = str.indexOf("Error");
						if(n1 > -1){
							M.toast({html: str});
						}
						var len = response.length;
						for( var i = 0; i<len; i++){
							var id = response[i]['id'];
							var name = response[i]['name'];
							$("#establecimiento").append("<option attr='"+id+"'>"+name+"</option>");
							if(r != 0){
								$("#establecimiento").append("<option attr='0'>"+'Cambiar'+"</option>");
							}
						}
						$('#establecimiento').formSelect();
					}
				});
			}
			function CambioDependencia(r){
				if(typeof r === "undefined"){
					r=0;
				}
				$('#dependencia').find('option').remove();   
				$.ajax({
					url: '../php/buscar.php',
					type: 'post',
					data: {request:4, id: r},
					dataType: 'json',
					success: function(response){
						var str= response;
						var n1 = str.indexOf("Error");
						if(n1 > -1){
							M.toast({html: str});
						}
						var len = response.length;
						for( var i = 0; i<len; i++){
							var id = response[i]['id'];
							var name = response[i]['name'];
							$("#dependencia").append("<option attr='"+id+"'>"+name+"</option>");
							if(r != 0){
								$("#dependencia").append("<option attr='0'>"+'Cambiar'+"</option>");
							}
						}
						$('#dependencia').formSelect();
					}
				});
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
				<h4 class="light">Nuevo Usuario</h4>
				<div class="row" style="position: fixed; top: 15%; right: 20%">
					<div class="right col s12 m8 l8 block">
						<div align="right"><h6><a href="../index.php" class="btn trigger">Volver</a></h6></div>
					</div>
				</div>
				<div class="row">
					<form name="form" class="col s12" method="post">
						<div class="input-field col s3">
							<i class="mdi-action-account-circle prefix"></i>
							<input id="rut_usuario" type="text" class="validate" name="rut_usuario" style="text-transform: uppercase" placeholder="">
							<label for="icon_prefix">RUT</label>
						</div>
						<div class="input-field col s3">
							<input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" required onkeypress="return soloLetras(event)">
							<label for="icon_prefix">Nombres</label>
						</div>
						<div class="input-field col s3">
							<input type="text" name="apellidoP_usuario" id="apellidoP_usuario" class="validate" placeholder="" required onkeypress="return soloLetras(event)">
							<label for="icon_prefix">Apellido Paterno</label>
						</div>
						<div class="input-field col s3">
							<input type="text" name="apellidoM_usuario" id="apellidoM_usuario" class="validate" placeholder="" required onkeypress="return soloLetras(event)">
							<label for="icon_prefix">Apellido Materno</label>
						</div>
						<div class="input-field col s3">
							<input type="text" name="email_usuario" id="email_usuario" class="validate" placeholder="" onblur="ValidoEmail();" required>
							<label for="icon_prefix">Correo</label>
						</div>
						<div class="input-field col s3">
							<input type="text" name="fono_usuario" id="fono_usuario" class="validate" placeholder="" onkeypress="return soloNumeros(event)" required>
							<label for="icon_prefix">Telefono</label>
						</div>
						<div class="input-field col s4">
							<select class="custom-select" name="cargo_usuario" id="cargo_usuario" onclick=Cargo();></select>
						</div>
						<div class="input-field col s3" >
							<select name="establecimiento" id="establecimiento" onchange="CambioEstablecimiento();"></select>
						</div>
						<div class="input-field col s3">
							<select name="dependencia" id="dependencia" onchange="CambioDependencia();"></select>
						</div>
						<div class="input-field col s3">
							<select name="estado" id="estado">
								<option value="NO" selected disabled>Estado Usuario</option>
								<option value="ACTIVO">ACTIVO</option>
								<option value="INACTIVO">INACTIVO</option>
							</select>
							<label for="estado">Estado</label>
						</div>
						<div class="form-group col s3">
							<button type="submit" class="btn btn-secondary" value="Guardar" id="btn_guardar" name="btn_guardar">Guardar</button>
						</div>
					</form>
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
				<th scope="col">Rut</th>
				<th scope="col">Nombre</th>
				<th scope="col">Estado</th>
				<th scope="col">Acción</th>
			</tr>
		</thead>
		<tbody>
			<?php
			//include ("../../include/funciones/funciones.php");
			//$cnn = ConectarAbastecimiento(); 
			$sql = "SELECT usu_rut, usu_nom, usu_app, usu_apm, usu_esta FROM USUARIO ORDER BY usu_nom";
			foreach ($cnn->query($sql) as $row){
				if($row['usu_esta']=="INACTIVO"){
					echo '<td>'.$row['usu_rut'].'</td>';
					echo '<td>'.$row['usu_nom']." ".$row['usu_app']." ".$row['usu_apm'].'</td>';
					echo '<td>'.$row['usu_esta'].'</td>';
					echo '<td><button type="button" class="btn btn-success" onclick="Editar('; echo "'".$row['usu_rut']."'"; echo');">Editar</button> <button type="button" class="btn btn-warning">Accesos</button> <button type="button" class="btn btn-primary" onclick="Activar('; echo "'".$row['usu_rut']."'"; echo');">Activar</button> <button type="button" class="btn btn-danger" onclick="Resetear('; echo "'".$row['usu_rut']."'"; echo');">Reset Clave</button></td>';
				}else{
					echo '<td>'.$row['usu_rut'].'</td>';
					echo '<td>'.$row['usu_nom']." ".$row['usu_app']." ".$row['usu_apm'].'</td>';
					echo '<td>'.$row['usu_esta'].'</td>';
					echo '<td><button type="button" class="btn btn-success" onclick="Editar('; echo "'".$row['usu_rut']."'"; echo');">Editar</button> <button type="button" class="btn btn-warning" onclick="Accesos('; echo "'".$row['usu_rut']."'"; echo');">Accesos</button> <button type="button" class="btn btn-primary" onclick="Desactivar('; echo "'".$row['usu_rut']."'"; echo');">Desactivar</button> <button type="button" class="btn btn-danger" onclick="Resetear('; echo "'".$row['usu_rut']."'"; echo');">Reset Clave</button></td>';
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



