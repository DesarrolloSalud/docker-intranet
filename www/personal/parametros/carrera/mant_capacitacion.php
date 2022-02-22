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
        if(count($_GET) && !$_SERVER['HTTP_REFERER']){
           header("location: ../error.php");
        }
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Snombre = utf8_encode($_SESSION['USU_NOM']);
        $SapellidoP = utf8_encode($_SESSION['USU_APP']);
        $SapellidoM = utf8_encode($_SESSION['USU_APM']);
        $Semail = utf8_encode($_SESSION['USU_MAIL']);
        $Scargo = utf8_encode($_SESSION['USU_CAR']);
        //$Sdependencia1 = utf8_encode($_SESSION['USU_DEP']);
        //$Sestablecimiento = utf8_encode($_SESSION['EST_ID']);
        $estable = $_GET['stb'];
        include ("../../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $id_formulario = 24;
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
                    header("location: ../../../error.php");
                }
            }else{
                //si formulario no activo
                $accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
                $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                mysqli_query($cnn, $insertAcceso);
                header("location: ../../../desactivada.php");
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
        <!--<script type="text/javascript" src="../../../include/js/materialize.clockpicker.min.js">--></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                /*$('select').material_select();
                $(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $('.tooltipped').tooltip({delay: 50});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
							  $('input.autocomplete').autocomplete({
								data: {
									"DEPARTAMENTO DE SALUDA I.MUNICIPALIDAD DE RENGO": null,									
								},
								limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
								onAutocomplete: function(val) {
								// Callback function when value is autcompleted.
								},
								minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
							});*/
							$('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });

            //funcion para editar un usuario
            function cargarusu(){
                var estable1 = $("#establecimiento").val();   
                
                //console.log(r);
            window.location = "mant_capacitacion.php?stb="+estable1;
            }
            function Vuelta(v){
                var estado = v.estado;
                console.log( estado );
                M.toast({html: 'estado'});

            }
            function agre_modi(cont){
                
                    var cont =cont;
                    var usurut = "#in"+cont;
                    var rut = $(usurut).val();
                    var estadoact = $("#estado_act").val();
                    if(estadoact =="1"){
                        estadoact="Activo";
                    }
                    if(estadoact =="2"){
                        estadoact="Inactivo";
                    }                                
                    var nombreact1 = $("#nombre_act").val();
                    var fechaact = $("#fecha_act").val();
                    var horaact = $("#hora_act").val();
                    $("#hora_pun").removeAttr("disabled");
                    var horapun = $("#hora_pun").val();
                    $("#hora_pun").attr("disabled","disabled"); 
                    
                    var nivelact = $("#nivel_act").val();
                    if(nivelact=="1"){
                        nivelact="Bajo";
                    }
                    if(nivelact=="2"){
                        nivelact="Medio";
                    }
                    if(nivelact=="3"){
                        nivelact="Alto";
                    }
                    $("nivel_pun").removeAttr("disabled");
                    var nivelpun = $("#nivel_pun").val();
                    $("#nivel_pun").attr("disabled","disabled");
                    var notaact = "#nota_act"+cont;
                    var nota = $(notaact).val();
                    $("#nota_pun1").removeAttr("disabled");
                    var notapun ="#nota_pun1"+cont;
                    var notapunt = $(notapun).val();
                    $("#nota_pun1").attr("disabled","disabled");
                    $("#total_act").removeAttr("disabled");
                    var totalact = "#total_act"+cont;
                    var totalact1 = $(totalact).val();
                    var fechaing = $("#fecha_ing").val();

                    if(estadoact != 'null' && nombreact1 != "" && fechaact != "" && horaact != "" && horapun != "" && nivelact !="" && nivelpun != "" && nota != "" && notapunt != "" && totalact1 != "" && fechaing != ""){
                        var condiciones = $("#che"+cont).is(":checked");
                        if (condiciones === true) {
                            var estado = 1;
                            $.post( "../../php/carrera/actividades_capacitacion.php", { "estadoact2": estadoact ,"rut_usu" : rut, "nombreact2" : nombreact1, "fechaact2" : fechaact, "horaact2" : horaact, "horapun2" : horapun, "nivelact2" : nivelact, "nivelpun2" : nivelpun, "nota2": nota, "notapun2" : notapunt, "totalact2" : totalact1, "fechaing2" : fechaing, "estado2" : estado}, Vuelta, "json" )                
                        
                            .done(function( data, textStatus, jqXHR ) {
                            if ( console && console.log ) {
                                //console.log("id enviada " + idActivar + " estado enviado " + estado );
                                console.log( "La solicitud se ha completado correctamente." );
                                //window.location = "mant_ot_extra.php";
                               // window.location="mant_capacitacion.php";
                            }
                            })
                            .fail(function( jqXHR, textStatus, errorThrown ) {
                            if ( console && console.log ) {
                                console.log( "La solicitud a fallado: " +  textStatus);
                               //window.location="mant_capacitacion.php";
                            }
                            });
                        }
                        if (condiciones === false){
                            var estado = 2;
                            $.post( "../../php/carrera/actividades_capacitacion.php", { "rut_usu" : rut, "nombreact2" : nombreact1, "fechaact2" : fechaact, "horaact2" : horaact, "horapun2" : horapun, "nivelact2" : nivelact, "nivelpun2" : nivelpun, "nota2": nota, "notapun2" : notapunt, "totalact2" : totalact1, "fechaing2" : fechaing, "estado2" : estado}, Vuelta, "json" )                
                        
                            .done(function( data, textStatus, jqXHR ) {
                            if ( console && console.log ) {
                                //console.log("id enviada " + idActivar + " estado enviado " + estado );
                                console.log( "La solicitud se ha completado correctamente." );
                                //window.location = "mant_ot_extra.php";
                               // window.location="mant_capacitacion.php";
                            }
                            })
                            .fail(function( jqXHR, textStatus, errorThrown ) {
                            if ( console && console.log ) {
                                console.log( "La solicitud a fallado: " +  textStatus);
                               //window.location="mant_capacitacion.php";
                            }
                            });
                        }                    
                    }else{
                        M.toast({html: 'DATOS NO VÁLIDOS'});
                    }
            }

            

            function Accesos(r){
                var RutUsu = r;
                //console.log("accesos.php?id="+idUsr);
                window.location = "accesos.php?rut="+RutUsu;
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
                return (key >= 48 && key <= 57 || key == 127 || key == 08)

            }
            function puntajehora(h){
                var pf= $("#hora_act").val();
                if(pf>=1 && pf<=16){
                    var pj=25;
                    $("#hora_pun").val(pj);
                }else if(pf>=17 && pf<=24){
                    var pj=45;
                    $("#hora_pun").val(pj);
                }else if(pf>=25 && pf<=32){
                    var pj=65;
                    $("#hora_pun").val(pj);
                }else if(pf>=33 && pf<=40){
                    var pj=80;
                    $("#hora_pun").val(pj);
                }else if(pf>=41 && pf<=79){
                    var pj=90;
                    $("#hora_pun").val(pj);
                }else if(pf>=80){
                    var pj=100;
                    $("#hora_pun").val(pj);
                }else{
                    M.toast({html: 'Hora no válida'});
                    $("#hora_act").val("");
                    $("#hora_pun").val("");
                }
            }

            function nota(){
                var imput_nota = "#nota_act"+h;
                var imput_total = "#total_act"+h;
                var imput_not_pun = "#nota_pun1"+h;
                var pn= $(imput_nota).val();
                if(pn>=1 && pn<=4){
                    var pjn=0.4;
                    $(imput_not_pun).val(pjn);
                }else if(pn>=4.1 && pn<=5.5){
                    var pjn=0.7;
                    $(imput_not_pun).val(pjn);
                }else if(pn>=5.6 && pn<=7){
                    var pjn=1;
                    $(imput_not_pun).val(pjn);
                }else{
                    M.toast({html:'Nota no válida'});
                    $(imput_nota).val("");
                    $(imput_not_pun).val("");
                }

                var pjh1 = $("#hora_pun").val();
                var pjn1 = $(imput_not_pun).val();
                var pji1 = $("#nivel_pun").val();

                //var n = "#nota_pun1"+h;
                var valn = $(imput_not_pun).val();

                if(pjh1 != 0 && pjn1 != 0 && pji1 != 0){
                    var TotalAct =0;
                    TotalAct = (pjh1 * valn * pji1);
                    n1 = TotalAct.toFixed(2);
                    $(imput_total).val(n1);
                }
            }

            function nivel(){
                var pni = $("#nivel_act").val();
                if(pni== "1" ){
                    var pjni=1;
                    $("#nivel_pun").val(pjni);
                }else if(pni== "2"){
                    var pjni=1.1;
                    $("#nivel_pun").val(pjni);
                }else if(pni== "3"){
                    var pjni=1.2;
                    $("#nivel_pun").val(pjni);
                }else{
                    M.toast({html:'Nivel no válida'});
                    $("#nivel_act").val("");
                    $("#nivel_pun").val("");
                }            
            }

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
                        <h4 class="light">Ingreso Actividades de capacitación</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                               <!--<div align="right"><h6><a href="nuevo_usuario.php" class="btn trigger">Nuevo</a></h6></div> -->
                            </div>
                        </div>
                        <div class="col s6 input-field">
                            <select name="establecimiento" id="establecimiento" onchange="cargarusu();">
                            <?php
                                $mostrarEstablecimientos="SELECT EST_ID, EST_NOM FROM ESTABLECIMIENTO";
                                $resultado =mysqli_query($cnn, $mostrarEstablecimientos);
                                while($reg=mysqli_fetch_array($resultado)){
                                    printf("<option value=\"$reg[1]\">$reg[1]</option>");
                                }                                                    ;
                                echo "<option value='no' disabled selected>Seleccione Establecimiento</option>";
                            ?>
                            </select>
                            <label for="icon_prefix">Establecimiento</label>
                        </div>
                        <div class="col s2 input-field">
                        </div>
                        <div class="input-field col s4">                                    
                            <select name="estado_act" id="estado_act" required>
                              <!--<option value="" disabled selected></option>-->
                              <option value="1">Activo</option>
                              <option value="2">Inactivo</option>           
                            </select>
                            <label>Estado Actividad</label>
                        </div>   
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                                <table class="responsive-table bordered striped" font-family="9px";>

                                <div class="input-field col s12">
                                    <input type="text" name="nombre_act" id="nombre_act" class="autocomplete" maxlength="250" placeholder="" required onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Actividad de Capacitación</label>
                                </div>
                                <div class="input-field col s2">
                                    <input type="text" class="datepicker" name="fecha_act" id="fecha_act" placeholder="">
                                    <label for="icon_prefix">Fecha Actividad</label>
                                </div>
                                <div class="input-field col s2">
                                    <input type="text" name="hora_act" id="hora_act" class="validate" placeholder="" required onchange="puntajehora(event)">
                                    <label for="icon_prefix">Horas</label>
                                </div>
                                <div class="input-field col s2">
                                    <input type="text" name="hora_pun" id="hora_pun" class="validate" placeholder="" disabled>
                                    <label for="hora_pun">Puntaje Horas</label>
                                </div>                                
                                <div class="input-field col s2">                                    
                                    <select name="nivel_act" id="nivel_act" onchange="nivel()">
                                      <option value="" disabled selected></option>                                       
                                      <option value="1">Bajo</option>
                                      <option value="2">Medio</option>
                                      <option value="3">Alto</option>
                                    </select>
                                    <label>Nivel Técnico</label>
                                </div>
                                <div class="input-field col s2">
                                    <input type="text" name="nivel_pun" id="nivel_pun" class="validate" disabled placeholder="">
                                    <label for="icon_prefix">Puntaje Nivel</label>
                                </div>
                                <div class="input-field col s2">
                                    <input type="text" class="datepicker" name="fecha_ing" id="fecha_ing" placeholder="">
                                    <label for="icon_prefix">Fecha Ingreso</label>
                                </div>

                                

                        </div>
                        <table class="responsive-table bordered striped">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    <th width="150">RUT</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>                                    
                                    <th>Nota</th>
                                    <th>Puntaje Nota</th>
                                    <th>Puntaje Total</th>
                                    <th>Agregar</th>
                                </tr>
                                        <tbody>
                                            <!-- cargar la base de datos con php --> 

                                            <?php                                                
                                                 $query = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT FROM USUARIO INNER JOIN ESTABLECIMIENTO ON ESTABLECIMIENTO.EST_ID = USUARIO.EST_ID WHERE (USUARIO.USU_DEP = '$estable') AND (USUARIO.USU_ESTA = 'ACTIVO') GROUP BY USU_RUT ORDER BY USUARIO.USU_APP";     
                                                    $respuesta = mysqli_query($cnn, $query);

                                                $cont = 0;                                                
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    echo "<tr>";
                                                        echo "<td><input type='text' id='in".$cont."' class='validate col s4' value='".$row_rs[0]."' style='display: none'>".$row_rs[0]."</td>";             
                                                        //echo "<td>".$row_rs[1]."</td>";
                                                        echo "<td>".utf8_encode($row_rs[2])." ".utf8_encode($row_rs[3])." ".utf8_encode($row_rs[1])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[4])."</td>";                                                     
                                                        echo '<td><input type="text" name="nota_act" id="nota_act'.$cont.'" class="validate col s3" maxlength="3" onblur="nota('.$cont.')"></td>';
                                                        echo '<td><input type="text" name="nota_pun1" id="nota_pun1'.$cont.'" class="validate col s3" disabled></td>';
                                                        echo '<td><input type="text" name="total_act" id="total_act'.$cont.'" class="validate col s5" disabled></td>';

                                                       
                                                            echo '<td><p><input type="checkbox" id="che'.$cont.'" onclick="agre_modi('.$cont.');"  class="validate col s3" />  <label for="che'.$cont.'"></label></p></td>';   
                                                        /* if ($row_rs[11] == "ACTIVA"){                                                                                                                    
                                                        }else{
                                                            echo '<td><p><input type="checkbox" id="che'.$cont.'" class="validate col s3" onclick="activa('.$cont.');" value="ACTIVA" />  <label for="che'.$cont.'"></label></p></td>';
                                                        }*/
                                                    echo "</tr>";
                                                    $cont = $cont + 1;
                                                }
                                            ?>

                                        </tbody>
                                    </thead>                                    
                                </table>
                                </br>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../../include/js/materialize.js"></script>
        
        

    </body>
</html>

