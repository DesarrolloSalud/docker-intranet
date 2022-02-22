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
        $Sestablecimiento = ($_SESSION['EST_ID']);
        $Sdependencia = $_SESSION['USU_DEP'];
        $Scategoria = $_SESSION['USU_CAT'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $id_formulario = 10;
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
        <link type="text/css" rel="stylesheet" href="../../include/css/materialize.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../../include/css/custom.css" />
        <link type="text/css" rel="stylesheet" href="../../include/css/materialize.clockpicker.min.css" />
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
        <script type="text/javascript" src="../../include/js/moment.js"></script>
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
            function Respuesta(r){
                var motivo = r.motivo;
                var acumulados = r.fla;
                var feriados = r.fl;
                var tfl = r.pendientes;
                if(motivo == "SI"){
                    if(tfl > 0){
                        M.toast({html: "Total Feriados disponibles : "+tfl});
                        M.toast({html: "Dias feriados año siguiente : "+feriados});
                        M.toast({html: "Dias feriados acumulados : "+acumulados});
                        $("#dias").removeAttr("disabled");
                        $("#dias_pendientes").val(tfl);
                        $("#fl").val(feriados);
                        $("#fla").val(acumulados);
                    }else{
                        M.toast({html: "No tiene dias disponibles"});
                    }
                }else{
                    M.toast({html: "No se ha realizado la carga del año solicitado"});
                    window.setTimeout(" window.location = 'for_solicitud_permiso.php'",3000);
                }
            }
            function ValidarFeriado(){
                var TipoDoc = 1;
                var post = $.post("../php/validar_feriado_nuevo.php", { "TipoDoc" : TipoDoc }, Respuesta, 'json');
            }
            function Cargar(){
                ValidarFeriado();
                $('select').formSelect('destroy');
                $("#Finicio").attr("disabled","disabled");
                $("#Ftermino").attr("disabled","disabled");
                $("#dias").attr("disabled","disabled");
                $("#motivo").attr("disabled","disabled");
                $("#guardar").attr("disabled","disabled");
            }

            function MostrarFinicio(){ 
                var DiasPendientes = $("#dias_pendientes").val();
                var CantDias = $("#dias").val();
                var dif = DiasPendientes - CantDias;
                if(dif >= 0){
                    //pasar a indicar fecha de inicio
                    $("#Finicio").removeAttr("disabled");
                }else{
                    //no puede tomarse esta cantidad de dias porque el tiene menos
                    var dif = DiasPendientes - CantDias;
                    M.toast({html: dif+' mas de los que puede pedir'});
                    $("#dias").val("");
                }
            }
            function MostrarFechaFIN(mff){
                var DocTipo = mff.TipoDoc;
                if(DocTipo == 1 || DocTipo == 2){
                    var fechaFIN = mff.fechaFIN;
                    var año = moment(fechaFIN).year();
                    var anoActual = (new Date).getFullYear();
                    anoActual = anoActual + 1;
                    if(año == anoActual){
                      $("#Ftermino").val(fechaFIN);
                      $("#Ftermino2").val(fechaFIN);
                      $('#jefatura').formSelect();
                    }else{
                      M.toast({html: 'La fecha de termino debe ser del año '+anoActual});
                      $("#Finicio").val("");
                      $("#Ftermino").val("");
                      $("#Ftermino2").val("");
                    }
                }
            }
            function SegundaValidacionFechaINI(svfi){
                var vacaciones = svfi.vacaciones;
                //Materialize.toast(vacaciones, 4000);
                if(vacaciones == "si" ){
                    var FeriadoFecHora = svfi.dia;
                    if(FeriadoFecHora == "si"){
                        //tiene que cambiar de dia
                        M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                        $("#Finicio").val("");
                    }else if(FeriadoFecHora == "no"){
                        //pasar a siguiente validancion
                        var TipoDoc = 1;
                        var CantDias = $("#dias").val();
                        var FechaInicio = $("#Finicio").val();
                        var post = $.post("../php/validar_fechaTermino.php", { "TipoDoc" : TipoDoc, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN, 'json');
                    }
                }else{
                    var ingreso = svfi.ingreso;
                    M.toast({html: 'A la fecha ingresada aun no cumple un año, puede pedir desde : '+ingreso+' cualquier error comunicarse a RRHH', displayLength: 8000});
                    $("#Finicio").val("");                }
            }
            function ValidoFechaINI(){
                var ValFecha = $("#Finicio").val();
                var año = moment(ValFecha).year();
                var anoActual = (new Date).getFullYear();
                anoActual = anoActual + 1;
                if(año == anoActual){
                  var post = $.post("../php/revisar_feriado_a_siguiente.php", { "fecha" : ValFecha }, SegundaValidacionFechaINI, 'json');
                }else{
                  M.toast({html: 'La fecha debe ser del año '+anoActual})
                  $("#Finicio").val("");
                }
            }
            function Jefatura(){
                $('#jefatura').formSelect();
            }
            function Motivo(){
                $("#motivo").removeAttr("disabled");
            }
            function Listo(){
                $("#guardar").removeAttr("disabled");
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
        </script>
    </head>
    <body onload="Cargar();">
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
                        <h4 class="light">Formulario de Solicitud</h4>
                         <form name="form" class="col s12" method="post" id="formSolPermi">
                            </br>
                            </br>
                            <div class="input-field col s12">
                                <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $Snombre." ".$SapellidoP." ".$SapellidoM;?>" disabled>
                                <label for="nombre_usuario">Nombre Completo Funcionario</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="rut_usuario" id="rut_usuario" class="validate" placeholder="" value="<?php echo $Srut;?>" disabled>
                                <label for="rut_usuario">RUT</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="categoria_usuario" id="categoria_usuario" class="validate" placeholder="" value="<?php echo $Scategoria;?>" disabled>
                                <label for="categoria_usuario">Categoria</label>
                            </div>
                            <div class="col s12" align="left"><h6>Solicita autorización para los siguientes dias:</h6></div>
                            </br>
                            </br>
                            </br>
                            <table class="col 12">
                                <tbody>
                                    <tr>
                                        <td  style="text-align: left;">
                                            <h6>Mas de 1 Dia</h6>          
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" name="dias" id="dias" class="validate" placeholder=""  onkeypress="return soloNumeros(event)" onblur="MostrarFinicio();">
                                                <input type="text" name="dias_pendientes" id="dias_pendientes" class="validate" style="display: none">
                                                <input type="text" name="fl" id="fl" class="validate" style="display: none">
                                                <input type="text" name="fla" id="fla" class="validate" style="display: none">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" class="datepicker" name="Finicio" id="Finicio" placeholder="Fecha de Inicio" required onchange="ValidoFechaINI();"> 
                                            </div> 
                                        </td>
                                        <td>
                                            <div class="input-field col s10">
                                                <input type="text" class="datepicker" name="Ftermino" id="Ftermino" placeholder="Fecha de Termino" required>
                                                <input type="text" class="datepicker" name="Ftermino2" id="Ftermino2" style="display: none">
                                            </div>
                                        </td>
                                    </tr>                                                               
                                </tbody>
                            </table>
                            </br>
                            </br>
                            <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura" onchange ="Motivo();">
                                    <?php
                                        $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND ((ESTABLECIMIENTO.EST_NOM = '$Sdependencia') OR (ESTABLECIMIENTO.EST_NOM = 'MULTIESTABLECIMIENTO'))";
                                        $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){
                                                $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                            }
                                            echo "<option value='no' selected>Jefe Directo</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="input-field col s12">
                            <!-- <input type="text" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"> -->
                                <input type="text" name="motivo" id="motivo" class="validate" placeholder="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)" onchange="Listo();">
                                <label for="icon_prefix">Motivo</label>
                            </div>
                            <div class="col s12">
                                <button id="guardar" type="submit" class="btn trigger" name="guardar" value="Guardar" >Enviar</button>
                            </div>
                         </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.clockpicker.min.js"></script>
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
            if($_POST['guardar'] == "Guardar"){
                //primero rescato todos los datos del formulario
                $usu_rut = $Srut;
                $usu_rut_jd = $_POST['jefatura'];
                $motivo = utf8_decode($_POST['motivo']);
                $estado = 'SOLICITADO';
                $AñoActual = date("Y");
                $AñoSiguiente = $AñoActual + 1;
                $tipo = "MASDEUNDIA";
                $cant_dias = $_POST['dias'];
                $jornada = "COMPLETA";
                $fec_ini = $_POST['Finicio'];
                $fec_fin = $_POST['Ftermino2'];
								if($Sdependencia == "ILUSTRE MUNICIPALIDAD DE RENGO"){
									$guardar_permiso = "INSERT INTO SOL_PERMI (DOC_ID,USU_RUT,USU_RUT_JD,USU_RUT_DIR,SP_CANT_DIA,SP_FEC_INI,SP_FEC_FIN,SP_JOR,SP_MOT,SP_ESTA,SP_TIPO,SP_ANO,SP_FEC) VALUES (1,'$usu_rut','$usu_rut_jd','$usu_rut_jd','$cant_dias','$fec_ini','$fec_fin','$jornada','$motivo','AUTORIZADO DIR','$tipo','$AñoSiguiente','$fecha')";
								}else{
									$guardar_permiso = "INSERT INTO SOL_PERMI (DOC_ID,USU_RUT,USU_RUT_JD,SP_CANT_DIA,SP_FEC_INI,SP_FEC_FIN,SP_JOR,SP_MOT,SP_ESTA,SP_TIPO,SP_ANO,SP_FEC) VALUES (1,'$usu_rut','$usu_rut_jd','$cant_dias','$fec_ini','$fec_fin','$jornada','$motivo','$estado','$tipo','$AñoSiguiente','$fecha')";
								}
                mysqli_query($cnn, $guardar_permiso);
                //echo $guardar_permiso;
                $query_sp_actual = "SELECT SP_ID FROM SOL_PERMI WHERE (DOC_ID = 1) AND (USU_RUT = '$usu_rut') AND (SP_FEC_INI = '$fec_ini') AND (SP_FEC_FIN = '$fec_fin') AND (SP_JOR = '$jornada')";
                
                $rsISA = mysqli_query($cnn, $query_sp_actual);
                if (mysqli_num_rows($rsISA) != 0){
                    while($rowISA = mysqli_fetch_row($rsISA)){
                        $id_sp_actual = $rowISA[0];
                    }
                }
                $fl_actual = $_POST['fl'];
                $fla_actual = $_POST['fla'];
                if($fla_actual > 0){
                    //si tiene vacaciones acumulada
                    if($fla_actual == $cant_dias){
                        //solo se usan dias acumulados y fla queda en 0
                        $insert_sp_detalle = "INSERT INTO SP_DETALLE_FL (SP_ID,SPD_FL,SPD_FLA) VALUES ($id_sp_actual,0,$cant_dias)";
                        mysqli_query($cnn,$insert_sp_detalle);
                        $query_banco_fl = "SELECT BD_ID, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$AñoSiguiente')";
                        $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                        if (mysqli_num_rows($resultado_banco_fl) != 0){
                            while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                                $num_id  = $row_fl[0];
                                $num_fl_usado = $row_fl[1];
                            }
                        }
                        $num_fl_usado = $num_fl_usado + $cant_dias;
                        $update_fl = "UPDATE BANCO_DIAS SET BD_FLA = 0, BD_FL_USADO = $num_fl_usado WHERE BD_ID = $num_id";
                        mysqli_query($cnn, $update_fl);
                    }elseif($fla_actual > $cant_dias){
                        //solo se usan dias acumulados
                        $insert_sp_detalle = "INSERT INTO SP_DETALLE_FL (SP_ID,SPD_FL,SPD_FLA) VALUES ($id_sp_actual,0,$cant_dias)";
                        mysqli_query($cnn,$insert_sp_detalle);
                        $query_banco_fl = "SELECT BD_ID, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$AñoSiguiente')";
                        $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                        if (mysqli_num_rows($resultado_banco_fl) != 0){
                            while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                                $num_id  = $row_fl[0];
                                $num_fl_usado = $row_fl[1];
                            }
                        }
                        $num_fl_usado = $num_fl_usado + $cant_dias;
                        $update_fl = "UPDATE BANCO_DIAS SET BD_FLA = 0, BD_FL_USADO = $num_fl_usado WHERE BD_ID = $num_id";
                        mysqli_query($cnn, $update_fl);
                    }elseif($fla_actual < $cant_dias){
                        //fla queda en 0 
                        $spd_fla = $fla_actual;
                        $spd_fl = $cant_dias - $fla_actual;
                        $insert_sp_detalle = "INSERT INTO SP_DETALLE_FL (SP_ID,SPD_FL,SPD_FLA) VALUES ($id_sp_actual,$spd_fl,$spd_fla)";
                        echo $insert_sp_detalle;
                        mysqli_query($cnn,$insert_sp_detalle);
                        $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$AñoSiguiente')";
                        $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                        if (mysqli_num_rows($resultado_banco_fl) != 0){
                            while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                                $num_id  = $row_fl[0];
                                $num_fl = $row_fl[1];
                                $num_fl_usado = $row_fl[2];
                            }
                        }
                        $num_fl_usado = $num_fl_usado + $cant_dias;
                        $num_fl = $num_fl - $spd_fl;
                        $update_fl = "UPDATE BANCO_DIAS SET BD_FLA = 0, BD_FL = $num_fl, BD_FL_USADO = $num_fl_usado WHERE BD_ID = $num_id";
                        mysqli_query($cnn, $update_fl);
                    }
                }else{
                    //no tiene vacaciones acumuladas
                    $insert_sp_detalle = "INSERT INTO SP_DETALLE_FL (SP_ID,SPD_FL,SPD_FLA) VALUES ($id_sp_actual,$cant_dias,0)";
                    mysqli_query($cnn, $insert_sp_detalle);
                    //echo $insert_sp_detalle; 
                    $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$AñoSiguiente')";
                    $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                    if (mysqli_num_rows($resultado_banco_fl) != 0){
                        while ($row_fl = mysqli_fetch_array($resultado_banco_fl)){
                            $num_id  = $row_fl[0];
                            $num_fl = $row_fl[1];
                            $num_fl_usado = $row_fl[2];
                        }
                    }
                    $num_fl_usado = $num_fl_usado + $cant_dias;
                    $num_fl = $num_fl - $cant_dias;
                    $update_fl = "UPDATE BANCO_DIAS SET BD_FLA = 0, BD_FL = $num_fl, BD_FL_USADO = $num_fl_usado WHERE BD_ID = $num_id";
                    mysqli_query($cnn, $update_fl);
                }
                //revisar el ID des permiso que corresponde
                $consultaId = "SELECT SP_ID FROM SOL_PERMI WHERE (DOC_ID = 1) AND (USU_RUT = '$usu_rut') AND (SP_FEC_INI = '$fec_ini') AND (SP_FEC_FIN = '$fec_fin') AND (SP_JOR = '$jornada')";
                $rsCID = mysqli_query($cnn, $consultaId);
                if (mysqli_num_rows($rsCID) != 0){
                    $rowCID = mysqli_fetch_row($rsCID);
                    $Id_for_actual = $rowCID[0];
                    $FecActual = date("Y-m-d");
                    $HorActual = date("H:i:s");
                    $HPAccion = "CREA PERMISO";
                    $guardar_historial = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($Id_for_actual,'$usu_rut','$FecActual','$HorActual',$doc_id, '$HPAccion')";
                    //echo $guardar_historial;
                    mysqli_query($cnn, $guardar_historial);
                    ?> <script type="text/javascript"> window.location="../index.php";</script>  <?php
                }
            }
        ?>
    </body>
</html>