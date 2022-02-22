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
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 2;
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
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
							
            });
            //funcion para editar un usuario
            function Editar(r){
                var RutUsu = r;
                //console.log(r);
                window.location = "editar_usuario.php?rut="+RutUsu;
            }
            function Firma(r){
                var RutUsu = r;
                //console.log("firma.php?id="+idUsr);
                window.location = "firma_usuario.php?rut="+RutUsu;
            }
            function Accesos(r){
                var RutUsu = r;
                //console.log("accesos.php?id="+idUsr);
                window.location = "accesos.php?rut="+RutUsu;
            }
            //funcion descativar
            function Desactivar(r){
                var rutDesactivar = r;
                var estado = "DESACTIVADO";
                $.post( "../php/estado_usuario.php", { "rut_usu" : rutDesactivar, "esta_enviado" : estado }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idDescativar + " estado enviado " + estado );
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "mant_usuarios.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            //Funcion activar
            function Activar(r){
                var rutActivar = r;
                var estado = "ACTIVO";
                $.post( "../php/estado_usuario.php", { "rut_usu" : rutActivar, "esta_enviado" : estado }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idActivar + " estado enviado " + estado );
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "mant_usuarios.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
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
                        <h4 class="light">Usuarios</h4>
                        <div class="row" style="position: absolute; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                                <div align="right"><h6><a href="nuevo_usuario.php" class="btn trigger">Nuevo</a></h6></div>
                            </div>
                        </div>
                        <div class="row">
                            <form name="form" class="col s12" method="get" action="">
                                <table class="responsive-table bordered striped">
                                    <thead>
                                        <tr>
                                            <!-- <th>ID</th> -->
                                            <th>RUT</th>
                                            <th>NOMBRE COMPLETO</th>
                                            <th>ESTABLECIMIENTO</th>
                                            <th>ESTADO</th>
                                            <!-- <th>FIRMA</th> -->
                                            <th>ACCION</th>
                                        </tr>
                                        <tbody>
                                            <!-- cargar la base de datos con php -->
                                            <?php
                                                $query = "SELECT USUARIO.USU_RUT, USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, ESTABLECIMIENTO.EST_NOM, USUARIO.USU_ESTA FROM USUARIO INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID ORDER BY ESTABLECIMIENTO.EST_NOM DESC";
                                                $respuesta = mysqli_query($cnn, $query);
                                                //recorrer los registros
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    echo "<tr>";
                                                        echo "<td>".$row_rs[0]."</td>";
                                                        //echo "<td>".$row_rs[1]."</td>";
                                                        echo "<td>".utf8_encode($row_rs[1])." ".utf8_encode($row_rs[2])." ".utf8_encode($row_rs[3])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[4])."</td>";
                                                        echo "<td>".$row_rs[5]."</td>";
                                                        //echo "<td><button class='btn trigger' name='firma' onclick='Firma(".$row_rs[0].");' id='firma' type='button'>Firma</button></td>";
                                                        //echo "<td><button class='btn trigger' name='editar' onclick='Editar("; echo "'".$row_rs[0]."'"; echo");' id='editar' type='button'>Editar</button></td>";
                                                        echo '<td><button class="btn trigger" name="editar" onclick="Editar('; echo "'".$row_rs[0]."'"; echo');" id="editar" type="button">Editar</button></td>';
                                                        echo '<td><button class="btn trigger" name="accesos" onclick="Accesos('; echo "'".$row_rs[0]."'"; echo');" id="accesos" type="button">Accesos</button></td>';
                                                            if($row_rs[5] == "ACTIVO"){
                                                                //echo "<td><button class='btn trigger' name='desactivar' onclick='Desactivar($row_rs[0]);' id='desactivar'>DESACTIVAR</button></td>";
                                                                echo '<td><button class="btn trigger" name="desactivar" onclick="Desactivar('; echo "'".$row_rs[0]."'"; echo');" id="desactivar" type="button">DESACTIVAR</button></td>';
                                                            }else{
                                                                //echo "<td><button class='btn trigger' name='activar' onclick='Activar($row_rs[0]);' id='activar'>&nbsp&nbsp&nbsp&nbspACTIVAR&nbsp&nbsp&nbsp</button></td>";
                                                                echo '<td><button class="btn trigger" name="activar" onclick="Activar('; echo "'".$row_rs[0]."'"; echo');" id="activar" type="button">&nbsp&nbsp&nbsp&nbspACTIVAR&nbsp&nbsp&nbsp</button></td>';
                                                            }
                                                    echo "</tr>";
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