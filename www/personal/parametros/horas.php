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
        //recivo informacion
        $rut_recivido = $_GET['rut'];
        $query_inicial = "SELECT BH_CANT FROM BANCO_HORAS WHERE (USU_RUT = '$rut_recivido') AND (BH_TIPO = 'INICIAL')";
        $resp_inicial = mysqli_query($cnn, $query_inicial);
        if(mysqli_num_rows($resp_inicial) != 0){
            $estado = "EXISTE";

        }else{
            $estado = "NO EXISTE";
        }
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 23;
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
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
            });  
            function Cantidad(){
                var cant = $("#cantidad").val();
                if(cant != ""){
                    //no hacer nada
                    $("#guardar").removeAttr("disabled");
                }else{
                    //decir que el campo no puede quedar vacio
                    Materialize.toast('Favor ingresar la cantidad de Horas, si corresponde dejar valor 0' , 4000);
                    $( "#cantidad" ).focus();
                }
            }
            function Guardar(){
                var rut = $('#rut_oculto').val();
                var fecha = $('#fecha_oculto').val();
                var cant = $('#cantidad').val();
                $.post( "../php/inicial_horas.php", {"rut" : rut, "fecha" : fecha, "cant" : cant}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        Materialize.toast('Horas ingresadas' , 4000);
                        window.location = "horas.php?rut="+rut;
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function soloNumeros(e){
                var key = window.Event ? e.which : e.keyCode
                //console.log( key);
                return (key >= 48 && key <= 57 || key == 127 || key == 08)
            }
        </script>
        <?php
        echo '<script>';
            echo 'function cargar(){';
                echo 'var semaforo ="'.$estado.'";';
                echo 'if (semaforo != "EXISTE"){';
                    echo 'var rut ="'.$rut_recivido.'";';
                    echo 'var fecha = "'.$fecha.'";';
                    echo 'Materialize.toast( "No existe registro carga inicial, favor realizar si corresponde el valor puede ser 0", 4000);';
                    echo '$( "#rut" ).val(rut);';
                    echo '$( "#rut_oculto" ).val(rut);';
                    echo '$( "#fecha" ).val(fecha);';
                    echo '$( "#fecha_oculto" ).val(fecha);';
                    echo '$("#guardar").attr("disabled","disabled");';
                    echo '$( "#contenido" ).hide();';
                    //mostrar campos 
                echo '}else{';
                    echo '$( "#rut" ).hide();';
                    echo '$( "#l_rut" ).hide();';
                    echo '$( "#fecha" ).hide();';
                    //echo '$( "#$l_fec" ).hide();';
                    echo '$( "#l_fec" ).hide();';
                    echo '$( "#cantidad" ).hide();';
                    echo '$( "#l_cant" ).hide();';
                    echo '$( "#guardar" ).hide();';
                echo '}';
            echo '}';
        echo '</script>';
        ?>
    </head>
    <body onload="cargar();">
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
                        <h4 class="light">HORAS COMPENSADAS</h4>
                        <div class="row" style="position: absolute; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                                <div align="right"><h6><a href="mant_dias.php" class="btn trigger">Volver</a></h6></div>
                            </div>
                        </div>
                        </br>
                        </br>
                        </br>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                                <div class="input-field col s3">
                                    <input type="text" name="rut" id="rut" class="validate" placeholder="Rut" disabled>
                                    <input type="text" name="rut_oculto" id="rut_oculto" style="display: none" placeholder="Rut">
                                    <label for="rut" id="l_rut">Rut</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="fecha" id="fecha" class="validate" placeholder="Fecha" disabled>
                                    <input type="text" name="fecha_oculto" id="fecha_oculto" style="display: none" placeholder="Rut">
                                    <label for="fecha" id="l_fec">Fecha</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="cantidad" id="cantidad" maxlength="3" class="validate" placeholder="" onkeypress="return soloNumeros(event)" onblur="Cantidad();">
                                    <label for="cantidad" id="l_cant">Cantidad</label>
                                </div>
                                <div class="input-field col s3">
                                    <button id="guardar" type="button" class="btn trigger" name="guardar" onclick="Guardar();">Guardar</button>
                                </div>
                            </form>
                        </div>
                        <div class="row" id="contenido">
                            <?php
                                //echo '<div class="col s12">hola mundo</div>';
                                //fechas
                                $FecActual = date("Y-m-d");
                                list($año_actual, $mes_actual, $dia_actual) = split('[-]', $FecActual);
                                $FecIni = ($año_actual - 2)."-".$mes_actual."-".$dia_actual;
                                //echo $FecIni;
                                $query_contenido = "SELECT BH_ID, DATE_FORMAT(BH_FEC,'%d-%m-%Y'), BH_TIPO, BH_CANT, BH_SALDO, BH_ID_ANT FROM BANCO_HORAS WHERE (USU_RUT = '$rut_recivido') AND (BH_FEC BETWEEN '$FecIni' AND '$FecActual') ORDER BY BH_FEC ASC";
                                echo '<table class="responsive-table bordered striped">';
                                    echo '<thead>';
                                        echo '<tr>';
                                            echo '<th>ID</th>';
                                            echo '<th>FECHA</th>';
                                            echo '<th>TIPO</th>';
                                            echo '<th>HORAS</th>';
                                            //echo '<th>SALDO</th>';
                                            echo '<th>ID DOC ANT</th>';
                                        echo '</tr>';
                                        //echo $query_contenido;
                                        echo '<tbody>';
                                        $respuesta = mysqli_query($cnn, $query_contenido);
                                        //recorrer los registros
                                        while ($row_rs = mysqli_fetch_array($respuesta)){
                                            echo "<tr>";
                                                echo "<td>".$row_rs[0]."</td>";
                                                echo "<td>".$row_rs[1]."</td>";
                                                echo "<td>".utf8_encode($row_rs[2])."</td>"; 
                                                if($row_rs[3] != 0){echo "<td>".utf8_encode($row_rs[3])."</td>";}else{echo "<td>HORAS CANCELADAS</td>";}
                                                //echo "<td>".utf8_encode($row_rs[4])."</td>";
                                                echo "<td>".$row_rs[5]."</td>";      
                                            echo "</tr>";
                                            $t_saldo = $t_saldo + $row_rs[4];
                                        }
                                        echo "<tr>";
                                            echo "<td></td>";
                                            echo "<td></td>";
                                            echo "<td></td>";
                                            echo "<td><b>SALDO TOTAL</b></td>";
                                            echo "<td><b>".$t_saldo."</b></td>";
                                            echo "<td></td>";
                                        echo "</tr>";
                                        echo '</tbody>';
                                    echo '</thead>';
                                echo '</table>';
                            ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
            });
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
        </script>
    </body>
</html>