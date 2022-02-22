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
        $id_formulario = 3;
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
            //funcion al cargar la pagina
            function Guardar(id){
            	//rescato los valores
            	var id_nuevo = id;
            	var nombre_nuevo = $("#nom_formulario").val();
            	var estado = $("#selec_estado").val();
            	if (estado == "no"){
            		var estado_nuevo = "DESACTIVADO"
            		if(nombre_nuevo == ""){
                        Materialize.toast('Nombre de Formulario no valido', 4000);
            			//console.log("nombre vacio");
            		}else if(nombre_nuevo == " "){
            			Materialize.toast('Nombre de Formulario no valido', 4000);
            			//console.log("nombre vacio");
            		}else if(nombre_nuevo == "  "){
            			Materialize.toast('Nombre de Formulario no valido', 4000);
            			//console.log("nombre vacio");
            		}else if(nombre_nuevo == "   "){
            			Materialize.toast('Nombre de Formulario no valido', 4000);
            			//console.log("nombre vacio");
            		}else if(nombre_nuevo == "."){
            			//console.log("nombre vacio");
            		}else{
            			console.log("id " + id_nuevo + " nombre formulario " + nombre_nuevo + " estado " + estado_nuevo);
		                $.post( "../php/nuevo_formulario.php", { "id" : id_nuevo, "nombre" : nombre_nuevo, "estado" : estado_nuevo }, null, "json" )
					    .done(function( data, textStatus, jqXHR ) {
					        if ( console && console.log ) {
					        	//console.log("id " + id_nuevo + " nombre formulario " + nombre_nuevo + " estado " + estado_nuevo);
					            console.log( "La solicitud se ha completado correctamente." );
					        }
					    })
					    .fail(function( jqXHR, textStatus, errorThrown ) {
					        if ( console && console.log ) {
					            console.log( "La solicitud a fallado: " +  textStatus);
					        }
						});
            		}
            	}else{
                    var estado = $("#selec_estado").val();
            		if(nombre_nuevo == ""){
            			Materialize.toast('Nombre de Formulario no valido', 4000);
            			console.log("nombre vacio");
            		}else if(nombre_nuevo == " "){
            			Materialize.toast('Nombre de Formulario no valido', 4000);
            			console.log("nombre vacio");
            		}else if(nombre_nuevo == "  "){
            			Materialize.toast('Nombre de Formulario no valido', 4000);
            			console.log("nombre vacio");
            		}else if(nombre_nuevo == "   "){
            			Materialize.toast('Nombre de Formulario no valido', 4000);
            			console.log("nombre vacio");
            		}else if(nombre_nuevo == "."){
            			console.log("nombre vacio");
            		}else{
            			console.log("id " + id_nuevo + " nombre formulario " + nombre_nuevo + " estado " + estado);
		                $.post( "../php/nuevo_formulario.php", { "id" : id_nuevo, "nombre" : nombre_nuevo, "estado" : estado }, null, "json" )
					    .done(function( data, textStatus, jqXHR ) {
					        if ( console && console.log ) {
					        	//console.log("id " + id_nuevo + " nombre formulario " + nombre_nuevo + " estado " + estado_nuevo);
					            console.log( "La solicitud se ha completado correctamente." );
					        }
					    })
					    .fail(function( jqXHR, textStatus, errorThrown ) {
					        if ( console && console.log ) {
					            console.log( "La solicitud a fallado: " +  textStatus);
					        }
						});
            		}
            	}
            }
            //funcion de select
            function Editar(){
            	var slt_estado = $("#selec_estado").val();
                if(slt_estado == "no"){
                    Materialize.toast('Debe seleccionar un estado', 4000);
                }
            }
            //funcion descativar
            function Desactivar(id){
            	var idDescativar = id;
            	var estado = "DESACTIVADO";
                $.post( "../php/estado_formulario.php", { "id_form" : idDescativar, "esta_enviado" : estado }, null, "json" )
			    .done(function( data, textStatus, jqXHR ) {
			        if ( console && console.log ) {
			        	//console.log("id enviada " + idDescativar + " estado enviado " + estado );
			            console.log( "La solicitud se ha completado correctamente." );
                        window.location = "mant_formularios.php";
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
                $.post( "../php/estado_formulario.php", { "id_form" : idActivar, "esta_enviado" : estado }, null, "json" )
			    .done(function( data, textStatus, jqXHR ) {
			        if ( console && console.log ) {
			        	//console.log("id enviada " + idActivar + " estado enviado " + estado );
			            console.log( "La solicitud se ha completado correctamente." );
                        window.location = "mant_formularios.php";
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
                        <h4 class="light">Formularios</h4>
                        <div class="row">
                         	<form name="form" class="col s12" method="post">
	                            <table class="responsive-table bordered striped">
	                                <thead>
	                                    <tr>
	                                        <th>ID</th>
	                                        <th>FORMULARIO</th>
	                                        <th>ESTADO</th>
	                                        <th>ACCION</th>
	                                    </tr>
	                                    <tbody>
	                                        <!-- cargar la base de datos con php -->
	                                        <?php
	                                            $query = "SELECT FOR_ID,FOR_NOM,FOR_ESTA FROM FORMULARIO";
	                                            $respuesta = mysqli_query($cnn, $query);
	                                            //recorrer los registros
	                                            while ($row_rs = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
		                                            echo "<tr>";
		                                            	echo "<td>".$row_rs[0]."</td>";
		                                            	echo "<td>".$row_rs[1]."</td>";
		                                            	echo "<td>".$row_rs[2]."</td>";
		                                            	if($row_rs[2] == "ACTIVO"){
															echo "<td><button class='btn trigger' name='desactivar' onclick='Desactivar(".$row_rs[0].");' id='desactivar'>DESACTIVAR</button></td>";
		                                            	}else{
		                                            		echo "<td><button class='btn trigger' name='activar' onclick='Activar(".$row_rs[0].");' id='activar'>&nbsp&nbsp&nbsp&nbspACTIVAR&nbsp&nbsp&nbsp</button></td>";
		                                            	}
		                                            	
		                                            echo "</tr>";
	                                            }
	                                        	//rescato cantidad de registros
	                                        	$cantidad = mysqli_num_rows($respuesta);
	                                        	$idactual = $cantidad;
	                                        	echo "<tr>";
	                                        		echo "<td>".$idactual."</td>";
	                                        		echo "<td><div class='input-field col s12'><input id='nom_formulario' type='text' class='validate' name='nom_formulario' onkeypress='return soloLetras(event)'><label for='icon_prefix' id='lb_nom' name='lb_nom'>Nombre Formulario</label></div></td>";
	                                        		echo "<td><select name='selec_estado' id='selec_estado' class='col s12' onchange='Editar();'><option value='no' disabled selected>Seleccione Estado</option><option value='ACTIVO'>ACTIVO</option><option value='DESACTIVADO'>DESACTIVADO</option></select></td>";
	                                        		echo "<td><button class='btn trigger' name='guardar' onclick='Guardar(".$idactual.");' id='guardar'>&nbsp&nbsp&nbspGUARDAR&nbsp&nbsp</button></td>";
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