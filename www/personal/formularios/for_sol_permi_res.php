
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
        //RESCATO DATOS POR GET ENVIADOS
        $rsp_id = $_GET['id'];
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
                    $select_rsp = "SELECT SP.SP_CANT_DIA,RSP.RSP_FEC_INI,DATE_FORMAT(RSP.RSP_FEC_INI,'%d-%m-%Y'),RSP.RSP_FEC_FIN,DATE_FORMAT(RSP.RSP_FEC_FIN,'%d-%m-%Y'),SP.SP_JOR,SP.SP_MOT,SP.SP_TIPO,SP.SP_ANO FROM RES_SOL_PERMI RSP INNER JOIN SOL_PERMI SP ON RSP.SP_ID = SP.SP_ID WHERE RSP.RSP_ID = $rsp_id";
                    //reviso si tengo algun ot_extra en creacion
                    $consultaEncreacion = "SELECT SP_ID FROM SOL_PERMI WHERE (USU_RUT = '$Srut') AND (SP_ESTA = 'EN CREACION')";
                    $respuestaEnCreacion = mysqli_query($cnn, $consultaEncreacion);
                    if (mysqli_num_rows($respuestaEnCreacion) == 0){
                        //usuario no tiene ningun folio tomado
                        $consultaNuevoId = "SELECT SP_ID FROM SOL_PERMI ORDER BY SP_ID DESC";
                        $respuestaNuevoId = mysqli_query($cnn, $consultaNuevoId);
                        $AñoActual = date("Y");
                        if (mysqli_num_rows($respuestaNuevoId) == 0){
                            $NuevoID = 1;
                            $FolioUno = "INSERT INTO SOL_PERMI (SP_ID,USU_RUT,SP_ESTA,SP_ANO,SP_FEC) VALUES ($NuevoID,'$Srut', 'EN CREACION','$AñoActual','$fecha')";;
                            mysqli_query($cnn, $FolioUno);
                        }else{
                            $rowNuevoId = mysqli_fetch_row($respuestaNuevoId);
                            $UltimoID = $rowNuevoId[0];
                            $NuevoID = $UltimoID + 1;
                            $FolioUno = "INSERT INTO SOL_PERMI (SP_ID,USU_RUT,SP_ESTA,SP_ANO,SP_FEC) VALUES ($NuevoID,'$Srut', 'EN CREACION','$AñoActual','$fecha')";;
                            mysqli_query($cnn, $FolioUno);
                        }
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
            function Cargar(){
                $("#guardar").attr("disabled","disabled");
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
        <?php
            $res_spr = mysqli_query($cnn, $select_rsp);
            if (mysqli_num_rows($res_spr) != 0){
                $rowRSP = mysqli_fetch_row($res_spr);
                //SP.SP_CANT_DIA,RSP.RSP_FEC_INI,DATE_FORMAT(RSP.RSP_FEC_INI,'%d-%m-%Y'),RSP.RSP_FEC_FIN,DATE_FORMAT(RSP.RSP_FEC_FIN,'%d-%m-%Y'),SP.SP_JOR,SP.SP_MOT,SP.SP_TIPO,SP.SP_ANO
                $cant_dia       = $rowRSP[0];
                $fec_ini        = $rowRSP[1];
                $mos_fec_ini    = $rowRSP[2];
                $fec_fin        = $rowRSP[3];
                $mos_fec_fin    = $rowRSP[4];
                $jor            = $rowRSP[5];
                $mot            = $rowRSP[6];
                $tipo           = $rowRSP[7];
                $año            = $rowRSP[8];
                //revisar banco dias
                $select_bd = "SELECT BD_ID,BD_FL,BD_FLA,BD_FL_USADO FROM BANCO_DIAS WHERE USU_RUT = '$Srut' AND BD_ANO = '$año'";
                $respuesta_sbd = mysqli_query($cnn, $select_bd);
                if(mysqli_num_rows($respuesta_sbd) != 0){
                    $r_bd        = mysqli_fetch_row($respuesta_sbd);
                    $bd_id       = $r_bd[0];
                    $bd_fl       = $r_bd[1];
                    $bd_fla      = $r_bd[2];
                    $bd_fl_usado = $r_bd[3];
                    //$bd_fl_usado = $r_bd[2];
                }
                //echo $mos_fec_ini;
            }
        ?>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">Formulario de Solicitud Feriado Legal</h4>
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
                            <div class="input-field col s2">
                                <input type="text" name="dias" id="dias" class="validate" placeholder="" value="<?php echo $cant_dia; ?>" disabled>
                                <label for="dias">Dias</label>
                            </div>
                            <div class="input-field col s5">
                                <input type="text" class="validate" name="Finicio" id="Finicio" placeholder="" value="<?php echo $mos_fec_ini; ?>" disabled>
                                <label for="Finicio">Fecha Inicio</label>
                            </div>
                            <div class="input-field col s5">
                                <input type="text" class="validate" name="Ftermino" id="Ftermino" placeholder="" value="<?php echo $mos_fec_fin; ?>" disabled>
                                <label for="Ftermino">Fecha Termino</label>
                            </div>
                            </br>
                            <div class="input-field col s12">
                            <!-- <input type="text" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"> -->
                                <input type="text" name="motivo" id="motivo" class="validate" placeholder="" value="<?php echo $mot ?>" disabled>
                                <label for="icon_prefix">Motivo</label>
                            </div>
                            </br>
                            <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura" onchange="Listo();">
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

        <?php
            if($_POST['guardar'] == "Guardar"){
                //primero rescato todos los datos del formulario
                $usu_rut = $Srut;
                $usu_rut_jd = $_POST['jefatura'];
                $motivo = $mot;
                $estado = 'SOLICITADO';
                $cant_dias =$cant_dia;
                $jornada = "COMPLETA";
								if($Sdependencia == "ILUSTRE MUNICIPALIDAD DE RENGO"){
                  $guardar_permiso = "UPDATE SOL_PERMI SET DOC_ID = 1, USU_RUT_JD = '$usu_rut_jd',USU_RUT_DIR = '$usu_rut_jd',SP_CANT_DIA = '$cant_dias',SP_FEC_INI='$fec_ini',SP_FEC_FIN='$fec_fin',SP_JOR='$jornada',SP_MOT='$motivo',SP_ESTA='AUTORIZADO DIR',SP_TIPO='$tipo',SP_ANO='$año',SP_FEC='$fecha',SP_DECRE='NO' WHERE (SP_ID = $NuevoID)";
								}else{
                  $guardar_permiso = "UPDATE SOL_PERMI SET DOC_ID = 1, USU_RUT_JD = '$usu_rut_jd',SP_CANT_DIA = '$cant_dias',SP_FEC_INI='$fec_ini',SP_FEC_FIN='$fec_fin',SP_JOR='$jornada',SP_MOT='$motivo',SP_ESTA='$estado',SP_TIPO='$tipo',SP_ANO='$año',SP_FEC='$fecha',SP_DECRE='NO' WHERE (SP_ID = $NuevoID)";
								}
                mysqli_query($cnn, $guardar_permiso);
                $id_sp_actual = $NuevoID;
                $fl_actual = $bd_fl;
                $fla_actual = $bd_fla;
                if($fla_actual > 0){
                    //si tiene vacaciones acumulada
                    if($fla_actual == $cant_dias){
                        //solo se usan dias acumulados y fla queda en 0
                        $insert_sp_detalle = "INSERT INTO SP_DETALLE_FL (SP_ID,SPD_FL,SPD_FLA) VALUES ($id_sp_actual,0,$cant_dias)";
                        mysqli_query($cnn,$insert_sp_detalle);
                        $query_banco_fl = "SELECT BD_ID, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$año')";
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
                        $query_banco_fl = "SELECT BD_ID, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$año')";
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
                        //echo $insert_sp_detalle;
                        mysqli_query($cnn,$insert_sp_detalle);
                        $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$año')";
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
                    $query_banco_fl = "SELECT BD_ID, BD_FL, BD_FL_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$año')";
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
                $Id_for_actual = $NuevoID;
                    $FecActual = date("Y-m-d");
                    $HorActual = date("H:i:s");
                    $HPAccion = "CREA PERMISO";
                    $guardar_historial = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($Id_for_actual,'$usu_rut','$FecActual','$HorActual',1, '$HPAccion')";
                    //echo $guardar_historial;
                    mysqli_query($cnn, $guardar_historial);
                    ?> <script type="text/javascript"> window.location="../index.php";</script>  <?php
                }
            }
        ?>
    </body>
</html>