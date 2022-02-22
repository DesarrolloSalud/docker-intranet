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
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 15;
        $ipcliente = getRealIP();
        $sp_id = $_GET['id'];
        $MiSP = "SELECT DOC_ID,SP_CANT_DIA,SP_CANT_DC,DATE_FORMAT(SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SP_FEC_FIN,'%d-%m-%Y') FROM SOL_PERMI WHERE (SP_ID = $sp_id)";
        $rowTSP = mysqli_fetch_row(mysqli_query($cnn,$MiSP));
        $TIPOSP = $rowTSP[0];
        $CANTDIA = $rowTSP[1];
        $CANTDC = $rowTSP[2];
        $FECINI = $rowTSP[3];
        $FECFIN = $rowTSP[4];
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
            function CargarIndex(){
                $("#rechazar").attr("disabled","disabled");
                $('select').formSelect('destroy');
                $("#fecha_ini").attr("disabled","disabled");
                $("#fecha_fin").attr("disabled","disabled");
            } 
            function Motivo(){
                var TIPODOC = $("#tipo_doc").val();
                if (TIPODOC != 1){
                    $("#rechazar").removeAttr("disabled");
                }else{
                    $("#resolucion").formSelect();
                }
            }
            function MostrarFechaINI(){
                $("#fecha_ini").removeAttr("disabled");
            }
            function MostrarFechaFIN(mff){
                var fechaFIN = mff.fechaFIN;
                var año = moment(fechaFIN).year();
                var anoActual = (new Date).getFullYear();
                //Materialize.toast(fechaFIN, 4000);
                //Materialize.toast(anoActual, 4000);
                if(año == anoActual){
                  $("#fecha_fin").val(fechaFIN);
                  $("#fecha_fin2").val(fechaFIN);
                  $("#rechazar").removeAttr("disabled");
                }else{
                  M.toast({html: 'La fecha debe ser del año actual'});
                  $("#fecha_ini").val("");
                  $("#fecha_fin").val("");
                  $("#fecha_fin2").val("");
                }
            }
            function SegundaValidacionFechaINI(svfi){
                var FeriadoFecHora = svfi.dia;
                if(FeriadoFecHora == "si"){
                    //tiene que cambiar de dia
                    M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                    $("#fecha_ini").val("");
                }else if(FeriadoFecHora == "no"){
                    var TIPODOC = $("#tipo_doc").val();
                    var CantDias = $("#dias_pedidos").val();
                    var FechaInicio = $("#fecha_ini").val();
                    var post = $.post("../php/validar_fechaTermino.php", { "TipoDoc" : TIPODOC, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN, 'json');
                }
            }
            function ValidoFechaINI(){
                var ValFecha = $("#fecha_ini").val();
                var post = $.post("../php/revisar_feriado.php", { "fecha" : ValFecha }, SegundaValidacionFechaINI, 'json');
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
    <body onload="CargarIndex();">
        <?php echo $TIPOSP; ?>
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
                        <h4 class="light">Rechazo de Permiso</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                                <div class="input-field col s12">
                                    <input type="text" name="motivo" id="motivo" class="validate" onchange="Motivo();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)" placeholder="" required>
                                    <input type="text" name="tipo_doc" id="tipo_doc" value="<?php echo $TIPOSP; ?>" class="validate" style="display: none">
                                    <label for="icon_prefix">Ingrese Motivo de Rechezo</label>
                                </div>
                                <?php
                                if($TIPOSP == 1){
                                    echo '<div class="col s12">';
                                    echo '<p>'."DIAS SOLICITADOS : ".$CANTDIA.'<br>'." FECHA INICIO SOLICITADA : ".$FECINI." FECHA FIN SOLICITADA : ".$FECFIN.'</p>';
                                    echo '</div>';
                                    echo '<div class="input-field col s4">';
                                        echo '<input type="text" name="dias_pedidos" id="dias_pedidos" value="'.$CANTDIA.'" class="validate" style="display: none">';
                                        echo '<select name="resolucion" id="resolucion" onchange="MostrarFechaINI();">';
                                            echo '<option value="no" selected>SELECCIONE</option>';
                                            echo '<option value="ANTICIPA">ANTICIPA</option>';
                                            echo '<option value="POSTERGA">POSTERGA</option>';
                                        echo '</select>';
                                    echo '</div>';
                                    echo '<div class="input-field col s4">';
                                        echo '<input id="fecha_ini" name="fecha_ini" class="datepicker" type="text" placeholder="Fecha Inicio" onchange="ValidoFechaINI();" required>';
                                    echo '</div>';
                                    echo '<div class="input-field col s4">';
                                        echo '<input id="fecha_fin" name="fecha_fin" class="datepicker" type="text" placeholder="Fecha Termino" required>';
                                        echo '<input id="fecha_fin2" name="fecha_fin2" class="datepicker" type="text" placeholder="Fecha Termino" style="display: none" required>';
                                    echo '</div>';
                                }
                                ?>
                                <div class="input-field col s12">
                                    <button class="btn trigger" type="submit" name="rechazar" id="rechazar" value="Rechazar" >Rechazar</button>
                                </div>
                            </form>
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
        <?php
            if($_POST['rechazar'] == "Rechazar"){
                $sp_com = $_POST['motivo'];
                //rescato id usuario e id formulario
                $query ="SELECT SP_ID,DOC_ID,USU_RUT,SP_ESTA,SP_CANT_DIA,SP_CANT_DC,SP_FEC_INI,SP_ANO FROM SOL_PERMI WHERE (SP_ID = '$sp_id')";
                $rs = mysqli_query($cnn, $query);
                if (mysqli_num_rows($rs) != 0){
                    $rowA = mysqli_fetch_row($rs);
                    if ($rowA[0] == $sp_id){
                        $doc_id = $rowA[1];
                        $usu_rut = $rowA[2];
                        $sp_estado = $rowA[3];
                        $sp_cant_dia = $rowA[4];
                        $sp_cant_dc = $rowA[5];
                        $sp_fec_ini = $rowA[6];
                        $sp_ano = $rowA[7];
                    }
                }
                $Actualfecha = date("Y-m-d");
                $Actualhora = date("H:i:s");
                $accionRealizada = utf8_decode("RECHAZADO POR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
                $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($sp_id,'$usu_rut','$Actualfecha','$Actualhora',$doc_id,'$accionRealizada')";
                mysqli_query($cnn, $insertAccion);
                if ($sp_estado == "SOLICITADO"){
                    $actualizarSolPermi = "UPDATE SOL_PERMI SET SP_ESTA = 'RECHAZADO J.D.', SP_COM = '$sp_com' WHERE (SP_ID = '$sp_id')";
                    //mysqli_query($cnn, $actualizarSolPermi); 
                }else{
                    $actualizarSolPermi = "UPDATE SOL_PERMI SET SP_ESTA = 'RECHAZADO DIR', SP_COM = '$sp_com' WHERE (SP_ID = '$sp_id')";
                    //mysqli_query($cnn, $actualizarSolPermi); 
                    //echo $actualizarSolPermi;
                }
                mysqli_query($cnn, $actualizarSolPermi); 
                //ver que tipo de documento es
                if($doc_id == 1){
                    //busco sp_detalle_fl
                    $select_spd = "SELECT SPD_FL,SPD_FLA FROM SP_DETALLE_FL WHERE SP_ID = $sp_id";
                    $respuesta_spd = mysqli_query($cnn,$select_spd);
                    if(mysqli_num_rows($respuesta_spd) != 0){
                        $row_spd = mysqli_fetch_row($respuesta_spd);
                        $spd_fl = $row_spd[0];
                        $spd_fla = $row_spd[1];
                    }
                    //busco datos banco_dias
                    $select_bd = "SELECT BD_ID,BD_FL,BD_FLA,BD_FL_USADO FROM BANCO_DIAS WHERE USU_RUT = '$usu_rut' AND BD_ANO = '$sp_ano'";
                    $respuesta_bd = mysqli_query($cnn,$select_bd);
                    if(mysqli_num_rows($respuesta_bd) != 0){
                        $row_bd = mysqli_fetch_row($respuesta_bd);
                        $bd_id = $row_bd[0];
                        $bd_fl = $row_bd[1];
                        $bd_fla = $row_bd[2];
                        $bd_usado = $row_bd[3];
                    }
                    //calculo totales
                    $total_fl = $spd_fl + $spd_fla; //total dias pedidos
                    $bd_fl = $bd_fl + $spd_fl; //dias feriado legal mas los pedidos
                    $bd_fla = $bd_fla + $spd_fla; //dias feriado legal acumulado mas los pedidos
                    $bd_usado = $bd_usado - $total_fl; //dias feriado legal usados menos los pedidos
                    //actualizar banco dias
                    $update_bd = "UPDATE BANCO_DIAS SET BD_FL = $bd_fl, BD_FLA = $bd_fla, BD_FL_USADO = $bd_usado WHERE BD_ID = $bd_id";
                    mysqli_query($cnn,$update_bd); 
                    //borro registros
                    $delete_spd = "DELETE FROM SP_DETALLE_FL WHERE SP_ID = $sp_id";
                    mysqli_query($cnn,$delete_spd);
                }elseif($doc_id == 2){
                    //convierto a decimal numero fraccion
                    if($sp_cant_dia == "1/2"){
                        $sp_cant_dia = 0.5;
                    }
                    //busco datos banco dias
                    $select_bd = "SELECT BD_ID,BD_ADM,BD_ADM_USADO FROM BANCO_DIAS WHERE USU_RUT = '$usu_rut' AND BD_ANO = '$sp_ano'";
                    $respuesta_bd = mysqli_query($cnn,$select_bd);
                    if(mysqli_num_rows($respuesta_bd) != 0){
                        $row_bd = mysqli_fetch_row($respuesta_bd);
                        $bd_id = $row_bd[0];
                        $bd_adm = $row_bd[1];
                        $bd_usado = $row_bd[2];
                    }
                    //calculo totales
                    $bd_adm = $bd_adm + $sp_cant_dia; //dia adm mas los pedidos
                    $bd_usado = $bd_usado - $sp_cant_dia; //dias adm usados menos los pedidos
                    //actualizo banco dias
                    $update_bd = "UPDATE BANCO_DIAS SET BD_ADM = '$bd_adm', BD_ADM_USADO = '$bd_usado' WHERE BD_ID = $bd_id";
                    mysqli_query($cnn,$update_bd);
                }elseif($doc_id == 3){
                    //buscar el id del banco hora
                    $select_bh = "SELECT BH_ID FROM BANCO_HORAS WHERE BH_TIPO = 'EGRESO' AND BH_ID_ANT = $sp_id";
                    $respuesta_bh = mysqli_query($cnn, $select_bh);
                    if(mysqli_num_rows($respuesta_bh) != 0){
                        $r_rbh = mysqli_fetch_row($respuesta_bh);
                        $bh_id = $r_rbh[0];
                    }
                    //rescato id bh ingreso y cantidad para hacer update por cada uno
                    $select_bh_detalle = "SELECT BH_ID_INGRESO, BHD_CANT FROM BH_DETALLE_EGRESO WHERE BH_ID_EGRESO = $bh_id";
                    $respuesta_bhd = mysqli_query($cnn,$select_bh_detalle);
                    //por cada registro debo consultar banco horas
                    if (mysqli_num_rows($respuesta_bhd) != 0){
                        while ($row_bhd = mysqli_fetch_row($respuesta_bhd)){
                            $bh_id_ingreso = $row_bhd[0];
                            $bhd_cant      = $row_bhd[1];
                            //rescato saldo de bh_id_ingreso
                            $select_bh_ingreso = "SELECT BH_SALDO FROM BANCO_HORAS WHERE BH_ID = $bh_id_ingreso";
                            $respuesta_bhi = mysqli_query($cnn,$select_bh_ingreso);
                            $row_rbhi = mysqli_fetch_row($respuesta_bhi);
                            $bh_saldo = $row_rbhi[0];
                            //calculo nuevo saldo y hago update
                            $bh_saldo = $bh_saldo + $bhd_cant;
                            $update_bh = "UPDATE BANCO_HORAS SET BH_SALDO = $bh_saldo WHERE BH_ID = $bh_id_ingreso";
                            mysqli_query($cnn,$update_bh);
                        }
                        //borrar todos los registros de bh_detalle_egreso
                        $delete_bhd = "DELETE FROM BH_DETALLE_EGRESO WHERE BH_ID_EGRESO = $bh_id";
                        mysqli_query($cnn,$delete_bhd);
                        //borrar egreso de banco_horas
                        $delete_bh = "DELETE FROM BANCO_HORAS WHERE BH_ID = $bh_id";
                        mysqli_query($cnn,$delete_bh);
                    }
                }
                if($TIPOSP == 1){
                    $rsp_resol = $_POST['resolucion'];
                    $rsp_fec_ini = $_POST['fecha_ini'];
                    $rsp_fec_fin = $_POST['fecha_fin2'];
                    $rsp_acc = "EN ESPERA";
                    $insert_rsp = "INSERT INTO RES_SOL_PERMI (SP_ID,RSP_RESOL,RSP_FEC_INI,RSP_FEC_FIN,RSP_ACC) VALUES ($sp_id,'$rsp_resol','$rsp_fec_ini','$rsp_fec_fin','$rsp_acc')";
                    mysqli_query($cnn,$insert_rsp);
                }
                ?> <script type="text/javascript"> window.location="../index.php";</script>  <?php
            }
        ?>
    </body>
</html>