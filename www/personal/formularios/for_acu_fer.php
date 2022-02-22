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
        $Scontra = $_SESSION['USU_CONTRA'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $id_formulario = 19;
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
                    //BUSCAR RES_SOL_PERMI
                    $MISRES_SOLPERMI = "SELECT RES_SOL_PERMI.RSP_ID,SOL_PERMI.SP_ID,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),DATE_FORMAT(RES_SOL_PERMI.RSP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(RES_SOL_PERMI.RSP_FEC_FIN,'%d-%m-%Y'),RES_SOL_PERMI.RSP_RESOL,SOL_PERMI.SP_FEC_INI,SOL_PERMI.SP_FEC_FIN,RES_SOL_PERMI.RSP_FEC_INI,RES_SOL_PERMI.RSP_FEC_FIN,RES_SOL_PERMI.RSP_ACC,SOL_PERMI.SP_ANO  FROM RES_SOL_PERMI, SOL_PERMI WHERE (RES_SOL_PERMI.SP_ID = SOL_PERMI.SP_ID) AND (SOL_PERMI.USU_RUT = '$Srut') AND (RES_SOL_PERMI.RSP_ACC = 'ACUMULA')";
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
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.modal').modal();
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
            function Cargar(){
                $("#motivo").attr("disabled","disabled");
                $("#enviar").attr("disabled","disabled");
            }
            function Motivo(){
                $("#motivo").removeAttr("disabled");
            }
            function Listo(){
                $("#enviar").removeAttr("disabled");
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
            $rRSP = mysqli_query($cnn, $MISRES_SOLPERMI);
            if (mysqli_num_rows($rRSP) != 0){
                $rowRSP = mysqli_fetch_row($rRSP);
                $G_rsp_id       = $rowRSP[0];
                $G_sp_id        = $rowRSP[1];
                $M_cant_dia     = $rowRSP[2];
                $M_sp_fec_ini   = $rowRSP[3];
                $M_sp_fec_fin   = $rowRSP[4];
                $M_rsp_fec_ini  = $rowRSP[5];
                $M_rsp_fec_fin  = $rowRSP[6];
                $M_rsp_resol    = $rowRSP[7];
                $G_sp_fec_ini   = $rowRSP[8];
                $G_sp_fec_fin   = $rowRSP[9];
                $G_rsp_fec_ini  = $rowRSP[10];
                $G_rsp_fec_fin  = $rowRSP[11];
                $G_rsp_acc      = $rowRSP[12];
                $G_rsp_año      = $rowRSP[13];
                //revisar banco dias
                $select_bd = "SELECT BD_ID,BD_FL,BD_FL_USADO FROM BANCO_DIAS WHERE USU_RUT = '$Srut' AND BD_ANO = '$G_rsp_año'";
                $respuesta_sbd = mysqli_query($cnn, $select_bd);
                if(mysqli_num_rows($respuesta_sbd) != 0){
                    $r_bd = mysqli_fetch_row($respuesta_sbd);
                    $bd_id = $r_bd[0];
                    $bd_fl = $r_bd[1];
                    $bd_fl_usado = $r_bd[2];
                }
                $año_proximo = $G_rsp_año + 1;
                $select_bd_pa = "SELECT BD_ID,BD_FLA FROM BANCO_DIAS WHERE USU_RUT = '$Srut' AND BD_ANO = '$año_proximo'";
                $respuesta_sbdpa = mysqli_query($cnn, $select_bd_pa);
                if(mysqli_num_rows($respuesta_sbdpa) == 0){
                    ?>
                    <script type="text/javascript">
                        M.toast({html: 'No se ha realizado la carga del banco de dias del año '+ <?php echo $año_proximo?>+ ' ,favor solicitar carga a RRHH'});
                        window.setTimeout(" window.location = '../index.php'",6000);   
                    </script> 
                    <?php 
                }/*else{
                    $r_proximo = mysqli_fetch_row($respuesta_sbdpa);
                    $id_bd_act = $r_proximo[0];
                    $fla_act   = $r_proximo[1];
                }*/
            }else{
                //no tiene ningun resolucion que acumular
                //mensaje en javascript
                ?>
                <script type="text/javascript">
                    M.toast({html: 'No existen registros de acumulacion de feriado, revise sus documentos pendientes'});
                </script> 
                <?php
                //echo $MISRES_SOLPERMI;
            }
        ?>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">Solicitud de acumulacion de Feriado</h4>
                         <form name="form" class="col s12" method="post">
                            </br>
                            </br>
                            <div class="input-field col s12">
                                <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $Snombre." ".$SapellidoP." ".$SapellidoM;?>" disabled>
                                <label for="nombre_usuario">Nombre del Funcionario</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="rut_usuario" id="rut_usuario" class="validate" placeholder="" value="<?php echo $Srut;?>" disabled>
                                <input type="text" name="rut_usuario2" id="rut_usuario2" class="validate" value="<?php echo $Srut;?>" style="display: none">
                                <label for="categoria_usuario">RUT :</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="folio" id="folio" class="validate" placeholder="" value="<?php echo $Scontra;?>" disabled >
                                <label for="folio">Planta</label>
                            </div>
                            <?php
                                if($G_rsp_acc == "ACUMULA"){
                                    echo '<div class="input-field col s12" >';
                                        echo '<select name="jefatura" id="jefatura" onchange ="Motivo();">';
                                                //$queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND (ESTABLECIMIENTO.EST_NOM = '$Sdependencia')";
                                                $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND ((ESTABLECIMIENTO.EST_NOM = '$Sdependencia') OR (ESTABLECIMIENTO.EST_NOM = 'MULTIESTABLECIMIENTO'))";
                                                $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                                    while($regJ =mysqli_fetch_array($resultadoJ)){
                                                        $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                        printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                                    }
                                                    echo "<option value='no' selected disabled>Jefe Directo</option>";
                                        echo '</select>';
                                    echo '</div>';
                                }
                            ?>
                            <div class="col s12">
                                <p>Expone y Solicita lo Siguiente:</p>
                                <p>He tomado conocimiento de su resolución en que <b><?php if($G_rsp_acc == "ACUMULA"){ echo " ".$M_rsp_resol." ";}else{ echo " ________________ ";}?></b> por razones de buen servicio, mi feriado legal de <b><?php if($G_rsp_acc == "ACUMULA"){ echo " ".$M_cant_dia." ";}else{ echo " _________ ";}?></b> días habiles solicitados para hacer uso a contar del <b><?php if($G_rsp_acc == "ACUMULA"){ echo " ".$M_sp_fec_ini." ";}else{ echo " ________________ ";}?></b> hasta el <b><?php if($G_rsp_acc == "ACUMULA"){ echo " ".$M_sp_fec_fin." ";}else{ echo " ________________ ";}?></b>.</p>
                                <p>La fecha indicada por usted, entre el <b><?php if($G_rsp_acc == "ACUMULA"){ echo " ".$M_rsp_fec_ini." ";}else{ echo " ________________ ";}?></b> hasta el <b><?php if($G_rsp_acc == "ACUMULA"){ echo " ".$M_rsp_fec_fin." ";}else{ echo " ________________ ";}?></b> no me es conveniente por : </p>
                            </div>
                            <div class="input-field col s12">
                                <input type="text" name="motivo" id="motivo" class="validate" placeholder="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)" onchange="Listo();">
                                <label for="motivo">Motivo</label>
                            </div>
                            <!-- ver si la cantidad de dias es igual a fl o mayor -->
                            <?php
                            //$M_cant_dia -> dias pedidos
                            //$bd_fl -> feriados legales restantes
                            if($M_cant_dia > $bd_fl){
                                //cant dia a acumular pasa a ser fl
                                $M_cant_dia = $bd_fl;
                            }else{
                                //restar cant_dia a fl año actual
                                $M_cant_dia = $M_cant_dia;
                            }
                            ?>
                            <div class="col s12">
                                <p>Por lo anterior, solicito a usted, acumular el feriado de <b><?php if($G_rsp_acc == "ACUMULA"){ echo " ".$M_cant_dia." ";}else{ echo " _________ ";}?></b> días hábiles, correspondientes al año <b><?php echo $G_rsp_año;?></b> para hacer uso conjuntamente con el periodo del feriado legal del año <b><?php $Aactual = date("Y"); $Asiguiente = $G_rsp_año + 1; echo $Asiguiente; ?></b>.</p>
                                </br>
                                </br>
                            </div>
                            <?php
                            if($G_rsp_acc == "ACUMULA"){
                                echo '<div class="col s12">';
                                    echo '<button id="enviar" type="submit" class="btn trigger" name="enviar" value="Guardar" >Guardar</button>';
                                echo '</div>';
                            }
                            ?>
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
            if($_POST['enviar'] == "Guardar"){
                //primero rescato todos los datos del formulario
                $usu_rut2 = $_POST['jefatura'];
                $saf_mot = utf8_decode($_POST['motivo']);
                $saf_esta = 'SOLICITADO';
                $FecActual = date("Y-m-d");
                $HorActual = date("H:i:s");
                $guardar_saf = "INSERT INTO SOL_ACU_FER (DOC_ID,USU_RUT,USU_RUT_JD,RSP_ID,SP_ID,SAF_CANT_DIA,SAF_ANO_ACT,SAF_ANO_SIG,SAF_MOT,SAF_ESTA,SAF_FEC)VALUES(6,'$Srut','$usu_rut2',$G_rsp_id,$G_sp_id,$M_cant_dia,'$Aactual','$Asiguiente','$saf_mot','$saf_esta','$FecActual')";
                //BUSCAR FOLIO
                mysqli_query($cnn, $guardar_saf);
                //actualizar bando dias año actual
                $bd_fl = $bd_fl - $M_cant_dia;
                $bd_fl_usado = $bd_fl_usado +$M_cant_dia;
                $actualizar_bd = "UPDATE BANCO_DIAS SET BD_FL = $bd_fl, BD_FL_USADO = $bd_fl_usado WHERE BD_ID = $bd_id";
                mysqli_query($cnn,$actualizar_bd);
                $ConsultaFolio = "SELECT SAF_ID FROM SOL_ACU_FER WHERE (RSP_ID = $G_rsp_id) AND (SP_ID = $G_sp_id)";
                $rcf = mysqli_query($cnn, $ConsultaFolio);
                if (mysqli_num_rows($rcf) != 0){
                    $rowcf = mysqli_fetch_row($rcf);
                    $saf_id       = $rowcf[0];
                }
                $GuaHistoPermiso = "INSERT INTO HISTO_PERMISO (HP_FOLIO,USU_RUT,HP_FEC,HP_HORA,DOC_ID,HP_ACC) VALUES ($saf_id,'$Srut','$FecActual','$HorActual',6,'ENVIA SOLICITUD DE ACUMULACION DE FERIADO')";
                $actualiza_rsp = "UPDATE RES_SOL_PERMI SET RSP_ACC = 'ACUMULADO' WHERE RSP_ID = $G_rsp_id";
                //echo $GuaHistoPermiso;
                mysqli_query($cnn, $actualiza_rsp);
                mysqli_query($cnn, $GuaHistoPermiso);
                ?> <script type="text/javascript"> window.location="../index.php";</script>  <?php 
            }
        ?>
    </body>
</html>