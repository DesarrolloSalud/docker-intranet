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
        $rut_usuario = $_GET['rut'];		
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $id_formulario = 7;
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
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
        function Eliminar(id){
            var idEliminar = id;
            var rut_usr_oculto = $("#rut_oculto").val();
            $.post( "../php/eliminar_acceso.php", { "id_acceso" : idEliminar}, null, "json" )
			   .done(function( data, textStatus, jqXHR ) {
			        if ( console && console.log ) {
			        	//console.log("id enviada " + idEliminar);
			            console.log( "La solicitud se ha completado correctamente." );
                        window.location = "accesos.php?rut="+rut_usr_oculto;
			        }
			    })
			    .fail(function( jqXHR, textStatus, errorThrown ) {
			        if ( console && console.log ) {
			            console.log( "La solicitud a fallado: " +  textStatus);
			        }
				});
			
        }
        function Agregar(id){
            var IdAgregar = id;
        	var rut_usr_oculto = $("#rut_oculto").val();
            $.post( "../php/nuevo_acceso.php", { "rut_usr" : rut_usr_oculto , "id_form" : IdAgregar }, null, "json" )
			   .done(function( data, textStatus, jqXHR ) {
			        if ( console && console.log ) {
			        	//console.log("id enviada " + idEliminar);
			            console.log( "La solicitud se ha completado correctamente." );
                        window.location = "accesos.php?rut="+rut_usr_oculto;
			        }
			    })
			    .fail(function( jqXHR, textStatus, errorThrown ) {
			        if ( console && console.log ) {
			            console.log( "La solicitud a fallado: " +  textStatus);
			        }
				});
			
        }
        function CargarIndex(){
            //$("#agregar").attr("disabled","disabled");
        }
        function Cargar(){
        	var rut_usr_oculto = $("#rut_oculto").val();
        	window.location = "accesos.php?rut="+rut_usr_oculto;
        }
        </script>
    </head>
    <body onload="CargarIndex();">
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
                        <h4 class="light">Accesos de Usuarios</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                                <div align="right"><h6><a href="mant_usuarios.php" class="btn trigger">Volver</a></h6></div>
                            </div>
                        </div>
                        <div class="row">
                            <form name="form" class="col s12" method="POST" action="">
                            	<div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" value="<?php echo $rut_usuario;?>" required>
                                </div>
                                <table class="responsive-table bordered striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>FORMULARIO</th>
                                            <th>ACCION</th>
                                        </tr>
                                        <tbody>
                                            <!-- cargar la base de datos con php -->
                                            <?php
                                                $query = "SELECT ACCESO.AC_ID, FORMULARIO.FOR_NOM, ACCESO.FOR_ID,FORMULARIO.FOR_ID FROM ACCESO INNER JOIN FORMULARIO ON ACCESO.FOR_ID = FORMULARIO.FOR_ID WHERE (ACCESO.USU_RUT = '".$rut_usuario."')";
                                                $respuesta = mysqli_query($cnn, $query);
                                                //recorrer los registros
                                                while ($row_rs = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
                                                    echo "<tr>";
                                                        echo "<td>$row_rs[3]</td>";
                                                        echo "<td>".utf8_encode($row_rs[1])."</td>";
                                                        echo "<td><button class='btn trigger' name='eliminar' onclick='Eliminar(".$row_rs[0].");' id='eliminar'>DESACTIVAR</button></td>";
                                                    echo "</tr>";
                                                    $array[] = $row_rs[2];
                                                }
                                            //echo $query;
                                            ?>
                                        </tbody>
                                    </thead>
                                </table>
                                <table class="responsive-table bordered striped">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tbody>
                                            <?php
                                                $longitud = count($array);
                                                $MostrarFormulariosRestantes = "SELECT FOR_ID, FOR_NOM FROM FORMULARIO";
                                                $resultadoF =mysqli_query($cnn, $MostrarFormulariosRestantes);
                                                while ($row_rsF = mysqli_fetch_array($resultadoF, MYSQLI_NUM)){
                                                    $arrayF[] = $row_rsF[0];
                                                    $arrayN[] = $row_rsF[1];
                                                }
                                                $resultado = array_diff($arrayF, $array);
                                                $longitud = count($arrayF);
                                                for($i=0; $i<$longitud; $i++){
                                                    //echo $resultado[$i];
                                                    if ($resultado[$i] == $arrayF[$i] && $resultado[$i] != 0 && $resultado[$i] != 9){
                                                        //echo $arrayN[$i];
                                                        echo "<tr>";
                                                            echo "<td>$arrayF[$i]</td>";
                                                            echo "<td>".utf8_encode($arrayN[$i])."</td>";
                                                            echo "<td><button class='btn trigger' name='agregar' onclick='Agregar($resultado[$i]);' id='agregar'>&nbsp&nbsp&nbspAGREGAR&nbsp&nbsp&nbsp</button></td>";
                                                        echo "</tr>";
                                                    }
                                                }
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
    </body>
</html>