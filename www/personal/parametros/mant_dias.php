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
        //recivo informacion
        $año_recivido = $_POST['año'];
        $BD_ADM = 6;
        $BD_FL  = 15;
        $BD_FLA = 0;
        $BD_SGR = 90;
        if($año_recivido != ""){
            $query_total_año = "SELECT BD_ID FROM BANCO_DIAS WHERE BD_ANO = '$año_recivido'";
            $respuesta_qta = mysqli_query($cnn, $query_total_año);
            if (mysqli_num_rows($respuesta_qta) != 0){
                $semaforo_año = $año_recivido;
            }else{
                $semaforo_año = "no";
            }
        }else{
            $semaforo_año = "nada";
        }
        if($_POST['todos'] == "Todos"){
            $query_bd = "SELECT BANCO_DIAS.BD_ID,BANCO_DIAS.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,BANCO_DIAS.BD_ADM,BANCO_DIAS.BD_FL,BANCO_DIAS.BD_FLA,BANCO_DIAS.BD_SGR FROM BANCO_DIAS, USUARIO WHERE (BANCO_DIAS.USU_RUT = USUARIO.USU_RUT) AND (BANCO_DIAS.BD_ANO = '$año_recivido') ORDER BY USU_RUT ASC";
            $mostrar = "todos";
        }
        if($_POST['buscar'] == "Buscar"){
            $rut_usuario = $_POST['rut_usuario'];
            $query_bd = "SELECT BANCO_DIAS.BD_ID,BANCO_DIAS.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,BANCO_DIAS.BD_ADM,BANCO_DIAS.BD_FL,BANCO_DIAS.BD_FLA,BANCO_DIAS.BD_SGR FROM BANCO_DIAS, USUARIO WHERE (BANCO_DIAS.USU_RUT = USUARIO.USU_RUT) AND (BANCO_DIAS.USU_RUT = '$rut_usuario') AND (BANCO_DIAS.BD_ANO = '$año_recivido')";
            $mostrar = "uno";
        }
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 22;
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
                $(".dropdown-trigger").dropdown();
            });
            function ValidoAño(){
                $( "#año" ).show();
                $( "#año_post" ).hide();
                $( "#nuevo" ).hide();
                $( "#validar" ).show();
            }
           /* function Respuesta(res){
                var query = res.query;
                console.log( query);
            }*/
            function Nuevo(){
                //variables, que hacen referencia los elementos de la pagina por sus ID.
                var año = $("#año_post").val();
                M.toast({html: 'Se estan cargado los datos de los Funcionarios'});  
                $("#nuevo").attr("disabled","disabled");
                $.post( "../php/cargar_nuevo_A2.php", { "año" : año}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idActivar + " estado enviado " + estado );
                        console.log( "La solicitud se ha completado correctamente." );
                        M.toast({html: 'Año cargado con exito'});
                        //$("#todos").removeAttr("disabled");
                        //window.location = "mant_dias.php";
                        window.setTimeout(" window.location = 'mant_dias.php'",6000); 
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function Horas(rut){
                var Rut = rut;
                //Materialize.toast(Rut , 4000);
                window.location = "horas.php?rut="+Rut;
            }
            function Adm(id){
                var IdAdm = "#ADM"+id;
                var ValAdm = $(IdAdm).val();
                if(ValAdm != ""){
                    if(ValAdm > 6){
                        //no se pueden mas de 6 dias
                        M.toast({html: 'No se puede agregar mas de 6 dias'});
                        $( IdAdm ).focus();
                    }else{
                        //no hacer nada
                    }
                }else{
                    //decir que el campo no puede quedar vacio
                    M.toast({html: 'Favor ingresar la cantidad de dias, si corresponde dejar valor 0'});
                    $( IdAdm ).focus();
                }
            }
            function FerL(id){
                var IdFL = "#FL"+id;
                var ValFL = $(IdFL).val();
                if(ValFL != ""){
                    //no hacer nada
                }else{
                    //decir que el campo no puede quedar vacio
                    M.toast({html: 'Favor ingresar la cantidad de Feriados Legales, si corresponde dejar valor 0'});
                    $( IdFL ).focus();
                }
            }
            function FerLA(id){
                var IdFLA = "#FLA"+id;
                var ValFLA = $(IdFLA).val();
                if(ValFLA != ""){
                    //no hacer nada
                }else{
                    //decir que el campo no puede quedar vacio
                    M.toast({html: 'Favor ingresar la cantidad de Feriados Legales Acumulados, si corresponde dejar valor 0'});

                    $( IdFLA ).focus();
                }
            }
            function Sgr(id){
                var IdSGR = "#SGR"+id;
                var ValSGR = $(IdSGR).val();
                if(ValSGR != ""){
                    if(ValSGR > 90){
                        //no se pueden mas de 6 dias
                        M.toast({html: 'No se puede agregar mas de 90 dias'});
                        $( IdSGR ).focus();
                    }else{
                        //no hacer nada
                    }
                }else{
                    //decir que el campo no puede quedar vacio
                    M.toast({html: 'Favor infresar la cantidad de Dias Sin Gose de Remuneraciones, si corresponde dejar valor 0'});
                    $( IdSGR).focus();
                }
            }
            // function Actualiza2(id){
            //     var Id_bd = id;
            //     var IdAdm = "#ADM"+id;
            //     var ValAdm = $(IdAdm).val();
            //     var IdFL = "#FL"+id;
            //     var ValFL = $(IdFL).val();
            //     var IdFLA = "#FLA"+id;
            //     var ValFLA = $(IdFLA).val();
            //     var IdSGR = "#SGR"+id;
            //     var ValSGR = $(IdSGR).val();
            //     $.post( "../php/actualizar_banco_dia2.php", { "id" : Id_bd, "adm" : ValAdm, "fl" : ValFL, "fla" : ValFLA, "sgr" : ValSGR}, null, "json" )
            //     .done(function( data, textStatus, jqXHR ) {
            //         if ( console && console.log ) {
            //             M.toast({html: 'Usuario actualizado'});
            //             //window.location = "mant_dias.php";
            //         }
            //     })
            //     .fail(function( jqXHR, textStatus, errorThrown ) {
            //         if ( console && console.log ) {
            //             console.log( "La solicitud a fallado: " +  textStatus);
            //         }
            //     });
            // }
            function Actualizar(id){
                var Id_bd = id;
                var IdAdm = "#ADM"+id;
                var ValAdm = $(IdAdm).val();
                var IdFL = "#FL"+id;
                var ValFL = $(IdFL).val();
                var IdFLA = "#FLA"+id;
                var ValFLA = $(IdFLA).val();
                var IdSGR = "#SGR"+id;
                var ValSGR = $(IdSGR).val();
                $.post( "../php/actualizar_banco_dia.php", { "id" : Id_bd, "adm" : ValAdm, "fl" : ValFL, "fla" : ValFLA, "sgr" : ValSGR}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        M.toast({html: 'Usuario actualizado'});
                        //window.location = "mant_dias.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }   
            function ActualizarTodos(id){
                var Id_bd = id;
                var IdAdm = "#ADM"+id;
                var ValAdm = $(IdAdm).val();
                var IdFL = "#FL"+id;
                var ValFL = $(IdFL).val();
                var IdFLA = "#FLA"+id;
                var ValFLA = $(IdFLA).val();
                var IdSGR = "#SGR"+id;
                var ValSGR = $(IdSGR).val();
                $.post( "../php/actualizar_banco_dia.php", { "id" : Id_bd, "adm" : ValAdm, "fl" : ValFL, "fla" : ValFLA, "sgr" : ValSGR}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idActivar + " estado enviado " + estado );
                        //console.log( "La solicitud se ha completado correctamente." );
                        M.toast({html: 'Usuario actualizado'});
                        //window.location = "mant_dias.php";
                        $(IdHoras).val(ValHoras);
                        $(IdAdm).val(ValAdm);
                        $(IdFL).val(ValFL);
                        $(IdFLA).val(ValFLA);
                        $(IdSGR).val(ValSGR);
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }  
            function NuevoRegistro(id){
                var Id_bd = id;
                var IdRut = "#RUT"+id;
                var ValRut = $(IdRut).val();
                var IdAdm = "#ADM"+id;
                var ValAdm = $(IdAdm).val();
                var IdFL = "#FL"+id;
                var ValFL = $(IdFL).val();
                var IdFLA = "#FLA"+id;
                var ValFLA = $(IdFLA).val();
                var IdSGR = "#SGR"+id;
                var ValSGR = $(IdSGR).val();
                var año = $("#año").val();
                $.post( "../php/nuevo_banco_dia.php", {"rut" : ValRut, "adm" : ValAdm, "fl" : ValFL, "fla" : ValFLA, "sgr" : ValSGR, "año" : año}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idActivar + " estado enviado " + estado );
                        //console.log( "La solicitud se ha completado correctamente." );
                        M.toast({html: 'Usuario actualizado'});
                        window.location = "mant_dias.php";
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
                return (key >= 48 && key <= 57 || key == 127 || key == 08 || key == 46 || key == 44)
            }
            function habilitar(){
                $( "#buscar" ).show();
                $( "#todos" ).hide();
            }
        </script>

        <?php
        echo '<script>';
            echo 'function cargar(){';
                echo 'var semaforo_año ="'.$semaforo_año.'";';
                echo 'if(semaforo_año == "nada"){';
                    //echo 'Materialize.toast( "primera carga", 4000);';
                    echo '$( "#validar" ).hide();';
                    echo '$( "#nuevo" ).hide();';
                    echo '$( "#año_post" ).hide();';
                    echo '$("#rut_usuario").attr("disabled","disabled");';
                    echo '$("#todos").attr("disabled","disabled");';
                    echo '$( "#buscar" ).hide();';
                echo '}else if(semaforo_año == "no"){';
                    echo 'var año_recivido ="'.$año_recivido.'";';
                    echo 'M.toast({html:  "no existen registros el año ingresado"});';
                    echo '$( "#año_post" ).val(año_recivido);';
                    echo '$( "#año" ).hide();';
                    echo '$( "#año_post" ).show();';
                    echo '$( "#validar" ).hide();';
                    echo '$( "#nuevo" ).show();';
                    echo '$( "#buscar" ).hide();';
                    echo '$("#rut_usuario").attr("disabled","disabled");';
                    echo '$("#todos").attr("disabled","disabled");';
                echo '}else{';
                    echo 'var año_recivido ="'.$año_recivido.'";';
                    echo '$( "#año" ).val(año_recivido);';
                    echo '$( "#año_post" ).hide();';
                    echo '$( "#validar" ).hide();';
                    echo '$( "#nuevo" ).hide();';
                    echo '$( "#buscar" ).hide();';
                    echo '$("#rut_usuario").removeAttr("disabled");';
                    echo '$("#todos").removeAttr("disabled");';
                    //cargo los filtros
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
                        <h4 class="light">Mantenedor de Dias</h4>
                        </br>
                        </br>
                        </br>
                        <div class="row">
                            <form name="form" class="col s12" method="post" action="mant_dias.php">
                                <div class="input-field col s1">
                                    <input type="text" name="año" id="año" class="validate" placeholder="" maxlength="4" onkeypress="return soloNumeros(event)" onclick="ValidoAño()" required>
                                    <input type="text" name="año_post" id="año_post" placeholder="" onclick="ValidoAño();">
                                    <label for="año">Ingrese Año</label>
                                </div>
                                <div class="input-field col s2">
                                    <button id="validar" type="submit" class="btn trigger" name="validar" value="Validar" >Validar</button>
                                    <!-- este boton funcionara con ajax -->
                                    <button class="btn trigger" name="nuevo" onclick="Nuevo();" id="nuevo" type="button">Nuevo Año</button>
                                </div>
                                <div class="input-field col s6">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" style="text-transform: uppercase" placeholder="" onclick="habilitar();">
                                    <label for="icon_prefix">RUT</label>
                                </div>
                                <div class="input-field col s2">
                                    <button id="todos" type="submit" class="btn trigger" name="todos" value="Todos" >Todos</button>
                                    <button id="buscar" type="submit" class="btn trigger" name="buscar" value="Buscar" >Buscar</button>
                                </div>
                            </form>
                        </div>
                        <?php
                            //echo $mostrar;
                            if($mostrar == "uno"){
                                //validar que el rut sea de un usuario de lo contrario dar mensaje que debe crear al usuario
                                $query_usuario = "SELECT USU_NOM,USU_APP,USU_APM FROM USUARIO WHERE (USU_RUT = '$rut_usuario')";
                                $resultado_usu = mysqli_query($cnn, $query_usuario);
                                //echo $query_usuario;
                                if (mysqli_num_rows($resultado_usu) != 0){
                                    //usuario existe validar que esta en banco dia
                                    $resultado_bd = mysqli_query($cnn, $query_bd);
                                    if (mysqli_num_rows($resultado_bd) != 0){
                                        //usuario tiene registros los muestros
                                        echo '<div class="row">';
                                            echo '<table class="responsive-table boradered">';
                                                echo '<thead>';
                                                    echo '<tr>';
                                                        echo '<th>RUT</th>';
                                                        echo '<th>NOMBRE</th>';
                                                        echo '<th>ADMIN</th>';
                                                        echo '<th>FER. L</th>';
                                                        echo '<th>F.L. ACU</th>';
                                                        echo '<th>S.G.R.</th>';
                                                        echo '<th>ACCIONES</th>';
                                                    echo '</tr>';
                                                    echo '<tbody>';
                                                        //while
                                                        while ($row_usu = mysqli_fetch_array($resultado_bd, MYSQLI_NUM)){
                                                            echo '<tr>';
                                                                echo '<td><input type="text" id="RUT'.$row_usu[0].'" class="validate" placeholder="" value="'.$row_usu[1].'" style="display: none">'.$row_usu[1].'</td>';
                                                                $NombreCompleto = utf8_encode($row_usu[2])." ".utf8_encode($row_usu[3])." ".utf8_encode($row_usu[4]);
                                                                echo '<td><input type="text" id="NOMBRE'.$row_usu[0].'" class="validate" placeholder="" value="'.$NombreCompleto.'" style="display: none">'.$NombreCompleto.'</td>';
                                                                //echo '<td><input size="3" class="col s5 validate" type="text" id="HORAS'.$row_usu[0].'" maxlength="3" placeholder="" value="'.$row_usu[5].'" onkeypress="return soloNumeros(event)" onblur="Horas('.$row_usu[0].');"></td>';
                                                                echo '<td><input size="6" class="col s5 validate" type="text" id="ADM'.$row_usu[0].'" maxlength="3" placeholder="" value="'.$row_usu[5].'" onkeypress="return soloNumeros(event)" onblur="Adm('.$row_usu[0].');"></td>';
                                                                echo '<td><input size="6" class="col s5 validate" type="text" id="FL'.$row_usu[0].'" maxlength="2" placeholder="" value="'.$row_usu[6].'" onkeypress="return soloNumeros(event)" onblur="FerL('.$row_usu[0].');"></td>';
                                                                echo '<td><input size="6" class="col s5 validate" type="text" id="FLA'.$row_usu[0].'" maxlength="2" placeholder="" value="'.$row_usu[7].'" onkeypress="return soloNumeros(event)" onblur="FerLA('.$row_usu[0].');"></td>';
                                                                /* ------------------------------------------------------------------------------------------------------ */
                                                                echo '<td><input size="6" class="col s5 validate" type="text" id="SGR'.$row_usu[0].'" maxlength="2" placeholder="" value="'.$row_usu[8].'" onkeypress="return soloNumeros(event)" onblur="Sgr('.$row_usu[0].');"></td>';
                                                                /* ------------------------------------------------------------------------------------------------------ */
                                                                echo '<td><button class="btn trigger" name="actualizar" onclick="Actualizar('.$row_usu[0].');" id="actualizar" type="button">Actualizar</button></td>';
                                                                // if($Srut == "11.277.235-9"){
                                                                //   echo '<td><button class="btn trigger" name="actualizar" onclick="Actualiza2('.$row_usu[0].');" id="actualizar" type="button">Actualizar</button></td>';
                                                                // }else{
                                                                // }
                                                                echo '<td><button class="btn trigger" name="horas" onclick="Horas('; echo "'".$row_usu[1]."'"; echo');" id="horas" type="button">Horas</button></td>';
                                                            echo '</tr>';
                                                        }
                                                    echo '</tbody>';
                                                echo '</thead>';
                                            echo '</table>';
                                        echo '</div>';
                                    }else{
                                        //usuario no tiene registros
                                        echo '<script>';
                                            echo 'Materialize.toast( "El rut ingresado no registros, favor revisar la informacion y guardar", 4000);';
                                        echo '</script>';
                                        echo '<div class="row">';
                                            echo '<table class="responsive-table boradered">';
                                                echo '<thead>';
                                                    echo '<tr>';
                                                        echo '<th>RUT</th>';
                                                        echo '<th>NOMBRE</th>';
                                                        echo '<th>ADMIN</th>';
                                                        echo '<th>FER. L</th>';
                                                        echo '<th>F.L. ACU</th>';
                                                        echo '<th>S.G.R.</th>';
                                                        echo '<th>ACCIONES</th>';
                                                    echo '</tr>';
                                                    echo '<tbody>';
                                                        echo '<tr>';
                                                            //rescatar nombre del rut
                                                            if($row_datos = mysqli_fetch_array($resultado_usu)){
                                                                $MuestroNombre=utf8_encode($row_datos[0]);
                                                                $MuestroApellidoP = utf8_encode($row_datos[1]);
                                                                $MuestroApellidoM = utf8_encode($row_datos[2]);
                                                            }
                                                            $id_nuevoregistro = 999999999;
                                                            echo '<td><input type="text" id="RUT'.$id_nuevoregistro.'" class="validate" placeholder="" value="'.$rut_usuario.'" style="display: none">'.$rut_usuario.'</td>';
                                                                $NombreCompleto = $MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM;
                                                            echo '<td><input type="text" id="NOMBRE'.$id_nuevoregistro.'" class="validate" placeholder="" value="'.$NombreCompleto.'" style="display: none">'.$NombreCompleto.'</td>';
                                                            echo '<td><input size="6" class="col s5 validate" type="text" id="ADM'.$id_nuevoregistro.'" maxlength="3" placeholder="" value="'.$BD_ADM.'" onkeypress="return soloNumeros(event)" onblur="Adm('.$id_nuevoregistro.');"></td>';
                                                            echo '<td><input size="6" class="col s5 validate" type="text" id="FL'.$id_nuevoregistro.'" maxlength="2" placeholder="" value="'.$BD_FL.'" onkeypress="return soloNumeros(event)" onblur="FerL('.$id_nuevoregistro.');"></td>';
                                                            echo '<td><input size="6" class="col s5 validate" type="text" id="FLA'.$id_nuevoregistro.'" maxlength="2" placeholder="" value="'.$BD_FLA.'" onkeypress="return soloNumeros(event)" onblur="FerLA('.$id_nuevoregistro.');"></td>';
                                                            /* ----------------------------------------------------------------------------------------------------- */
                                                            echo '<td><input size="6" class="col s5 validate" type="text" id="SGR'.$id_nuevoregistro.'" maxlength="2" placeholder="" value="'.$BD_SGR.'" onkeypress="return soloNumeros(event)" onblur="Sgr('.$id_nuevoregistro.');"></td>';
                                                            /* ----------------------------------------------------------------------------------------------------- */
                                                            echo '<td><button class="btn trigger" name="nuevo_registro" onclick="NuevoRegistro('.$id_nuevoregistro.');" id="nuevo_regstro" type="button">Guardar</button></td>';
                                                            echo '<td><button class="btn trigger" name="horas" onclick="Horas('; echo "'".$rut_usuario."'"; echo');" id="horas" type="button">Horas</button></td>';
                                                        echo '</tr>';
                                                    echo '</tbody>';
                                                echo '</thead>';
                                            echo '</table>';
                                        echo '</div>';
                                    }
                                }else{
                                    //usuario no existe mensaje de que primero se debe crear en el mantenedor de usuario
                                    echo '<script>';
                                        echo 'Materialize.toast( "Rut no existe, favor crear usuario si corresponde", 4000);';
                                    echo '</script>';
                                }
                            }elseif($mostrar == "todos"){
                                echo '<div class="row">';
                                    echo '<table class="responsive-table boradered">';
                                        echo '<thead>';
                                            echo '<tr>';
                                                echo '<th>RUT</th>';
                                                echo '<th>NOMBRE</th>';
                                                echo '<th>ADMIN</th>';
                                                echo '<th>FER. L</th>';
                                                echo '<th>F.L. ACU</th>';
                                                echo '<th>S.G.R.</th>';
                                                echo '<th>ACCIONES</th>';
                                            echo '</tr>';
                                            echo '<tbody>';
                                            //while
                                            $resultado_bd = mysqli_query($cnn, $query_bd);
                                            while ($row_usu = mysqli_fetch_array($resultado_bd, MYSQLI_NUM)){
                                                echo '<tr>';
                                                    echo '<td><input type="text" id="RUT'.$row_usu[0].'" class="validate" placeholder="" value="'.$row_usu[1].'" style="display: none">'.$row_usu[1].'</td>';
                                                    $NombreCompleto = utf8_encode($row_usu[2])." ".utf8_encode($row_usu[3])." ".utf8_encode($row_usu[4]);
                                                    echo '<td><input type="text" id="NOMBRE'.$row_usu[0].'" class="validate" placeholder="" value="'.$NombreCompleto.'" style="display: none">'.$NombreCompleto.'</td>';
                                                    echo '<td><input size="6" class="col s5 validate" type="text" id="ADM'.$row_usu[0].'" maxlength="3" placeholder="" value="'.$row_usu[5].'" onkeypress="return soloNumeros(event)" onblur="Adm('.$row_usu[0].');"></td>';
                                                    echo '<td><input size="6" class="col s5 validate" type="text" id="FL'.$row_usu[0].'" maxlength="2" placeholder="" value="'.$row_usu[6].'" onkeypress="return soloNumeros(event)" onblur="FerL('.$row_usu[0].');"></td>';
                                                    echo '<td><input size="6" class="col s5 validate" type="text" id="FLA'.$row_usu[0].'" maxlength="2" placeholder="" value="'.$row_usu[7].'" onkeypress="return soloNumeros(event)" onblur="FerLA('.$row_usu[0].');"></td>';
                                                    /* ------------------------------------------------------------------------------------------------------ */
                                                    echo '<td><input size="6" class="col s5 validate" type="text" id="SGR'.$row_usu[0].'" maxlength="2" placeholder="" value="'.$row_usu[8].'" onkeypress="return soloNumeros(event)" onblur="Sgr('.$row_usu[0].');"></td>';
                                                    /* ------------------------------------------------------------------------------------------------------ */
                                                    echo '<td><button class="btn trigger" name="actualizar" onclick="ActualizarTodos('.$row_usu[0].');" id="actualizar" type="button">Actualizar</button></td>';
                                                    echo '<td><button class="btn trigger" name="horas" onclick="Horas('; echo "'".$row_usu[1]."'"; echo');" id="horas" type="button">Horas</button></td>';
                                                echo '</tr>';
                                                }
                                            echo '</tbody>';
                                        echo '</thead>';
                                    echo '</table>';
                                echo '</div>';
                            }
                        ?>
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
              $('.sidenav').sidenav();
              $(".dropdown-trigger").dropdown();
            });
            $("#rut_usuario").Rut({ 
                on_error: function(){ 
                    M.toast({html: 'Rut incorrecto'});
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