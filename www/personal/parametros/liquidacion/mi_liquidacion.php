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
		header("location: ../../../index.php");
	}else{
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Snombre = utf8_encode($_SESSION['USU_NOM']);
        $SapellidoP = utf8_encode($_SESSION['USU_APP']);
        $SapellidoM = utf8_encode($_SESSION['USU_APM']);
        $Semail = utf8_encode($_SESSION['USU_MAIL']);
        $Sdireccion = utf8_encode($_SESSION['USU_DIR']);
        $Sfono = utf8_encode($_SESSION['USU_FONO']);
        $Scargo = utf8_encode($_SESSION['USU_CAR']);
        $Sestablecimiento = utf8_encode($_SESSION['EST_ID']);
        $Sdependencia = utf8_encode($_SESSION['USU_DEP']);
        $Scategoria = utf8_encode($_SESSION['USU_CAT']);
        $Snivel = utf8_encode($_SESSION['USU_NIV']);       
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
        $Sfecing1 = $_SESSION['USU_FEC_ING'];
        $Sfecini1 = $_SESSION['USU_FEC_INI'];				
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $ano5 = date("Y");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();        
        include ("../../../include/funciones/funciones.php");
				$Sliqdir = utf8_encode($_SESSION['LIQDIR']);
        
				
        $cnn = ConectarPersonal();
        $buscar = "SELECT ESTABLECIMIENTO.EST_NOM FROM USUARIO INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (USUARIO.USU_RUT = '".$Srut."')";
        $rs = mysqli_query($cnn, $buscar);
        if($row = mysqli_fetch_array($rs)){
            $MuestroEstablecimiento = $row[0];            
        }        
        $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE USU_RUT = '$usu_rut_edit'";
        $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
        if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
            $GuardoEstablecimiento=$row_eyd[0];
            $GuardoDependencia=$row_eyd[1];
        }
        $id_formulario = 32;
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
                    header("location: ../../error.php");
                }
            }else{
                //si formulario no activo
                $accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
                $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                mysqli_query($cnn, $insertAcceso);
                header("location: ../../desactivada.php");
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
		<link type="text/css" rel="stylesheet" href="../../../include/css/icon.css" />
		<link type="text/css" rel="stylesheet" href="../../../include/css/materialize.css" media="screen,projection" />
		<link type="text/css" rel="stylesheet" href="../../../include/css/custom.css" />
		<link href="../../../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
		<style type="text/css">
			body{
				background-image: url("../../../include/img/fondopersonal.jpg");
				background-size: cover;
				background-repeat: no-repeat;
			}
		</style>
		<script type="text/javascript" src="../../../include/js/jquery-2.1.4.js"></script>
		<script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
		<script type="text/javascript" src="../../../include/js/materialize.js"></script>        
		<script>
			$(document).ready(function () {                
				$('select').formSelect();
				$('.sidenav').sidenav();
				$(".dropdown-trigger").dropdown();
				$('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
				$('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
			});
		</script>
	</head>
	<body>
		<!-- llamo el nav que tengo almacenado en un archivo -->
		<?php require_once('../../estructura/nav_personal.php');?>			
		<!-- inicio contenido pagina -->
		</br>
	</br>
</br>
<div class="container">
	<div class="section">
		<div class="row">
			<div class="col s12 center block" style="background-color: #ffffff">
				<h4 class="light">Mí Liquidación de Sueldo</h4>
				<?php
				//echo $Sliqdir;
				if($Sliqdir != ""){
					$total = "";
					for ($segundos = 1; $segundos <= 2; $segundos++){
						sleep($segundos);
						$total = $segundos;
					}                            
					//unlink($Sliqdir);                            
					$Sliqdir ="";
				}
				$fecha5 = date("Y");
				$mes=1;
				while($mes < 13){
					if($mes <=9){
						$mcuenta = "0".$mes;
					}else{
						$mcuenta = $mes;
					}          
					$dir5 = "../../../include/liquidacion_txt/".$fecha5."/".$mcuenta."/".$Srut.".txt";
					$dir6 = "../../../include/liquidacion_txt/".$fecha5."/".$mcuenta."/1.txt";
					$dir7 = "../../../include/liquidacion_txt/".$fecha5."/".$mcuenta."/RE1.txt";
					$dir8 = "../../../include/liquidacion_txt/".$fecha5."/".$mcuenta."/R01.txt";
					$dir9 = "../../../include/liquidacion_txt/".$fecha5."/".$mcuenta."/2-RE1.txt";
					$dir10 = "../../../include/liquidacion_txt/".$fecha5."/".$mcuenta."/2-RO1.txt";
					$dir11 = "../../../include/liquidacion_txt/".$fecha5."/".$mcuenta."/R02.txt";
					$dir12 = "../../../include/liquidacion_txt/".$fecha5."/".$mcuenta."/2-RO2.txt";
					chmod($dir7, 0000);
					chmod($dir8, 0000);
					chmod($dir9, 0000);
					chmod($dir10, 0000);
					chmod($dir11, 0000);
					chmod($dir12, 0000);
					unlink($dir5);
					unlink($dir6);
					$mes = $mes+1;
				}
				?>
				<div class="row">
					<form class="col s12" method="post" action="" enctype="multipart/form-data">
						<div class="input-field col s12">
							<i class="mdi-action-account-circle prefix"></i>
							<input id="rut_usuario" type="text" class="validate" name="rut_usuario" placeholder="" disabled value="<?php echo $Srut;?>">
							<label for="icon_prefix">RUT</label>
						</div>															
						<div class="input-field col s4">
							<input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" disabled value="<?php echo $Snombre;?>" onkeypress="return soloLetras(event)">
							<label for="icon_prefix">Nombres</label>
						</div>
						<div class="input-field col s4">
							<input type="text" name="apellidoP_usuario" id="apellidoP_usuario" class="validate" placeholder="" disabled value="<?php echo $SapellidoP;?>" onkeypress="return soloLetras(event)">
							<label for="icon_prefix">Apellido Paterno</label>
						</div>
						<div class="input-field col s4">
							<input type="text" name="apellidoM_usuario" id="apellidoM_usuario" class="validate" placeholder="" disabled value="<?php echo $SapellidoM;?>" onkeypress="return soloLetras(event)">
							<label for="icon_prefix">Apellido Materno</label>
						</div>															
						<div class="input-field col s4">                                    
							<select name="liqui_estable" id="liqui_estable">
								<!--<option value="RE1">CESFAM RENGO</option>-->
								<option value="RO1">CESFAM ROSARIO Y DEPARTAMENTO</option>
								<option value="RO2">CESFAM RENGO Y CESFAM ORIENTE</option>     
							</select>
							<label>Establecimiento</label>
						</div>
						<div class="input-field col s4">
							<input type="text" class="datepicker" name="fechaIngreso" id="fechaIngreso" placeholder="" required> 
							<label for="icon_prefix" id="fechaIngreso">Ingrese Fecha</label>
						</div>
						<div class="input-field col s4">                                    
							<select name="liqui_suple" id="liqui_suple" >
								<!--<option value="" disabled selected></option>-->
								<option value="1"></option>
								<option value="2">Suplementaria</option>           
							</select>
							<label>Tipo Liquidación</label>
						</div> 
						<div class="col s12">
							<button id="buscar1" class="btn trigger" type="submit" name="buscar1" value="Buscar">Buscar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>        
<!-- fin contenido pagina -->        
<!-- Cargamos jQuery y materialize js -->
<script type="text/javascript" src="../../../include/js/jquery.js"></script>
<script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
<script type="text/javascript" src="../../../include/js/materialize.js"></script>
<script>
	$(document).ready(function () {
		//Animaciones
		$('select').formSelect();
		$('.sidenav').sidenav();
		$(".dropdown-trigger").dropdown();
		$('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
		$('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
	});
</script>
<?php
if($_POST['buscar1'] == "Buscar"){
	$rutbus = $Srut;							
	$largo = strlen ($rutbus);
	if($largo<12){
		$Srut = "0".$rutbus;
	}              
	$estable = $_POST['liqui_estable'];
	$tipo1 = $_POST['liqui_suple'];       
	$restfec = substr($fechabus, 0,7);
	if($tipo1 == 2){
		$sup = "2-";
	}else{
		$sup ="";
	}							
	$formatos   = array('.txt');
	$fechabus = $_POST['fechaIngreso'];																				
	$mescarp = substr($fechabus, -5,2);
	$anocarp = substr($fechabus,0,4);
	$directorio = '../../../include/liquidacion_txt';
	$nombreArchivoId = $sup.$estable.".txt";
	$carpeta = $directorio."/".$anocarp."/".$mescarp;
	$_SESSION['LIQDIR'] = $carpeta.'/'.$rutbus.'.txt';
	//$txtusu = $carpeta.'/'.$rutbus.'.txt';
	$rut1=1;
	$inicio=0;
	$inibus=0;
	$sinpermi= $carpeta."/".$nombreArchivoId;
	chmod($sinpermi, 0755);
	if (is_readable($carpeta)) { 
		$fp = fopen($carpeta.'/'.$nombreArchivoId, "r"); 
		$contador=0;
		while(!feof($fp)) {
			$linea = fgets($fp);
			$linea = str_replace('"','',$linea);          
			if($inibus==0){
				$bus = $linea;            
				$inibus=1;
			} 
			if($linea != $bus){            
				$file = fopen($carpeta.'/'.$rut1.'.txt', "a"); 
				fwrite($file, $linea);
				$contador= $contador+1;
				if ($contador == 8){
					$rest = substr($linea, -7,5);                    
					$rest1 = substr($linea, -10, 3);                
					$rest2 = substr($linea,-12,2);                         
					$rest3 = $rest2.".".$rest1.".".$rest;
					//$dir = $carpeta."/".$rut1.$sup.".txt";   //se saca $sup
					$dir = $carpeta."/".$rut1.".txt";   
					//$dirfin = $carpeta."/".$rest3.$sup.".txt";
					$dirfin = $carpeta."/".$rest3.".txt";
				}
				if($linea == "   LIQUIDACION"){
					echo $linea;
				}
			}else{              
				fclose($file);                            
				rename($dir, $dirfin);
				$contador=0;
				//echo "Dirección final"." ".$dirfin;
				$largo1 = strlen ($rutbus);
				if($largo1<12){
					$rutbus = "0".$rutbus;
				}  
				if($rest3 == $rutbus){
					echo '<script type="text/javascript"> window.open("'.$dirfin.'" , "Liquidación" , "width=550,height=650,scrollbars=yes,menubar=yes,toolbar=yes,location=no")</script>';											  
					break 1;
				}else{
					unlink($dirfin);
					unlink($dir);
				}
			}      
		} 
		fclose($fp);      
		$dir =  $carpeta."/"."1".$sup.".txt";
		$dirfin1 =  $carpeta."/".$rest3.$sup.".txt";
		copy($dir, $dirfin1);
		if($rest3 == $rutbus){
			//echo $dirfin;
			echo '<script type="text/javascript"> window.open("'.$dirfin.'" , "Liquidación" , "width=550,height=650,scrollbars=yes,menubar=yes,toolbar=yes,location=no")</script>';
		}else{
			unlink($dirfin);
			unlink($dir);
		}								
	}else{
		echo  '<script type="text/javascript"> M.toast({html: "Liquidación no encontrada"});</script>';											
	}              
	echo '<script type="text/javascript"> window.location="mi_liquidacion.php"; </script>';
}	
$sinpermi= $carpeta."/".$nombreArchivoId;
chmod($sinpermi, 0000);
?>
</html>
</body>

