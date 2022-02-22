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
        $id_formulario = 31;
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
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
        <link href="https://fonts.googleapis.com/icon?family=MAterial+Icons" rel="stylesheet">
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
            });
            function Cargar(){
                $("#guardar").attr("disabled","disabled");
						}
            //funcion de select
            function Editar(){
            	var slt_estado = $("#select_est").val();
                if(slt_estado == "no"){
                    Materialize.toast('Debe seleccionar un Establecimiento', 4000);
                }else{
										$("#guardar").removeAttr("disabled");
								}
            }
            //funcion descativar
            function Desactivar(id){
            	var idDescativar = id;
            	var estado = "DESACTIVADO";
                $.post( "../php/estado_programa.php", { "id_form" : idDescativar, "esta_enviado" : estado }, null, "json" )
			    .done(function( data, textStatus, jqXHR ) {
			        if ( console && console.log ) {
			        	//console.log("id enviada " + idDescativar + " estado enviado " + estado );
			            console.log( "La solicitud se ha completado correctamente." );
                        window.location = "mant_programas.php";
			        }
			    })
			    .fail(function( jqXHR, textStatus, errorThrown ) {
			        if ( console && console.log ) {
			            console.log( "La solicitud a fallado: " +  textStatus);
			        }
				});
            }
            //Funcion activar
            function Activar(id){
            	var idActivar = id;
            	var estado = "ACTIVO";
                $.post( "../php/estado_programa.php", { "id_form" : idActivar, "esta_enviado" : estado }, null, "json" )
			    .done(function( data, textStatus, jqXHR ) {
			        if ( console && console.log ) {
			        	//console.log("id enviada " + idActivar + " estado enviado " + estado );
			            console.log( "La solicitud se ha completado correctamente." );
                        window.location = "mant_programas.php";
			        }
			    })
			    .fail(function( jqXHR, textStatus, errorThrown ) {
			        if ( console && console.log ) {
			            console.log( "La solicitud a fallado: " +  textStatus);
			        }
				});
            }
            function soloLetras(e){
               key = e.keyCode || e.which;
               tecla = String.fromCharCode(key).toLowerCase();
               letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
               especiales = "8-37-39-45-46";
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
                        <h4 class="light">Programas</h4>
                        <div class="row">
                         	<form name="form" class="col s12" method="post">
	                            <table class="responsive-table bordered striped">
	                                <thead>
	                                    <tr>
	                                        <th>ID</th>
	                                        <th>PROGRAMA</th>
																					<th>ESTABLECIMIENTO</th>
	                                        <th>ESTADO</th>
	                                        <th>ACCION</th>
	                                    </tr>
	                                    <tbody>
	                                        <!-- cargar la base de datos con php -->
	                                        <?php
	                                            $query = "SELECT OTE_PROGRAMA.OP_ID,OTE_PROGRAMA.OP_NOM,ESTABLECIMIENTO.EST_NOM,OTE_PROGRAMA.OP_ESTA FROM OTE_PROGRAMA INNER JOIN ESTABLECIMIENTO ON OTE_PROGRAMA.EST_ID = ESTABLECIMIENTO.EST_ID ORDER BY ESTABLECIMIENTO.EST_ID ASC";
	                                            $respuesta = mysqli_query($cnn, $query);
	                                            //recorrer los registros
	                                            while ($row_rs = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
		                                            echo "<tr>";
		                                            	echo "<td>".$row_rs[0]."</td>";
		                                            	echo "<td>".$row_rs[1]."</td>";
		                                            	echo "<td>".$row_rs[2]."</td>";
																									echo "<td>".$row_rs[3]."</td>";
		                                            	if($row_rs[3] == "ACTIVO"){
															echo "<td><button class='btn trigger' name='desactivar' onclick='Desactivar(".$row_rs[0].");' id='desactivar'>DESACTIVAR</button></td>";
		                                            	}else{
		                                            		echo "<td><button class='btn trigger' name='activar' onclick='Activar(".$row_rs[0].");' id='activar'>&nbsp&nbsp&nbsp&nbspACTIVAR&nbsp&nbsp&nbsp</button></td>";
		                                            	}
		                                            	
		                                            echo "</tr>";
	                                            }
	                                        	echo "<tr>";
	                                        		echo "<td></td>";
	                                        		echo "<td><div class='input-field col s12'><input id='nom_programa' type='text' class='validate' name='nom_programa' onkeypress='return soloLetras(event)'><label for='icon_prefix' id='lb_nom' name='lb_nom'>Nombre Programa</label></div></td>";
	                                        		echo "<td><select name='select_est' id='select_est' class='col s12' onchange='Editar();'><option value='no' disabled selected>Seleccione Establecimiento</option><option value='2'>CESFAM RENGO</option><option value='3'>CESFAM ROSARIO</option></select></td>";
																							echo "<td></td>";
	                                        		echo "<td><button class='btn trigger' type='submit' name='guardar' value='Guardar' id='guardar'>&nbsp&nbsp&nbspGUARDAR&nbsp&nbsp</button></td>";
	                                        	echo "</tr>";
	                                        ?>
	                                    </tbody>
	                                </thead>
	                            </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
				<?php
            if($_POST['guardar'] == "Guardar"){
                //primero rescato todos los datos del formulario
								$nom_programa = utf8_decode($_POST['nom_programa']);
                $id_est = $_POST['select_est'];
								$op_esta = 'ACTIVO';
                $FecActual = date("Y-m-d");
                $HorActual = date("H:i:s");
                $guardar_pro = "INSERT INTO OTE_PROGRAMA (EST_ID,OP_NOM,OP_ESTA) VALUES ($id_est,'$nom_programa','$op_esta')";
                $accion = utf8_decode("CREA NUEVO PROGRAMA");
                $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$FecActual', '$HorActual')";
                mysqli_query($cnn, $guardar_pro);
                mysqli_query($cnn, $insertAcceso);
              	//echo $guardar_pro;
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
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
            });
        </script>
    </body>
</html>