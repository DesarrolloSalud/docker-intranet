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
        $Sestablecimiento = $_SESSION['EST_ID'];
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 28;
        $ipcliente = getRealIP();
        $spr_id = $_GET['id'];
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
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
            function CargarIndex(){
                $("#rechazar").attr("disabled","disabled");
            } 
            function Motivo(){
                $("#rechazar").removeAttr("disabled");
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
                        <h4 class="light">Rechazo de Permiso sin Goce de Remuneracion</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                                <div class="input-field col s12">
                                    <input type="text" name="motivo" id="motivo" class="validate" onchange="Motivo();" placeholder="" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Ingrese Motivo de Rechezo</label>
                                </div>
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
        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <?php
            if($_POST['rechazar'] == "Rechazar"){
                $spr_com = $_POST['motivo'];
                //rescato id usuario e id formulario
                $query ="SELECT SPR_ID,DOC_ID,USU_RUT,SPR_NDIA,SPR_FEC_INI FROM SOL_PSGR WHERE (SPR_ID = $spr_id)";
                $rs = mysqli_query($cnn, $query);
                if (mysqli_num_rows($rs) != 0){
                    $rowA = mysqli_fetch_row($rs);
                    if ($rowA[0] == $spr_id){
                        $doc_id = $rowA[1];
                        $usu_rut = $rowA[2];
                        $cant_dia = $rowA[3];
                        $fec_ini = $rowA[4];
                    }
                }
                $Actualfecha = date("Y-m-d");
                $Actualhora = date("H:i:s");
                $accionRealizada = utf8_decode("RECHAZADO POR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
                $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($spr_id,'$usu_rut','$Actualfecha','$Actualhora',$doc_id,'$accionRealizada')";
                mysqli_query($cnn, $insertAccion);
                $actualizarSolPermi = "UPDATE SOL_PSGR SET SPR_ESTA = 'RECHAZADO DIR SALUD', SPR_COM = '$spr_com' WHERE (SPR_ID = '$spr_id')";
                mysqli_query($cnn, $actualizarSolPermi);
                $añoActual = date("Y", strtotime($fec_ini));
                //echo $query;
                $select_bd = "SELECT BD_ID,BD_SGR,BD_SGR_USADO FROM BANCO_DIAS WHERE USU_RUT = '$usu_rut' AND BD_ANO = '$añoActual'";
                //echo $select_bd;
                $rBD = mysqli_query($cnn, $select_bd);
                if (mysqli_num_rows($rBD) != 0){
                    $rowBD = mysqli_fetch_row($rBD);
                    $bd_id       = $rowBD[0];
                    $bd_sgr       = $rowBD[1];
                    $bd_sgr_u     = $rowBD[2];
                }
                $bd_sgr = $bd_sgr + $cant_dia;
                $bd_sgr_u = $bd_sgr_u - $cant_dia;
                $actualizar_spr = "UPDATE BANCO_DIAS SET BD_SGR = $bd_sgr, BD_SGR_USADO = $bd_sgr_u WHERE BD_ID = $bd_id";
                //echo $actualizar_spr;
                mysqli_query($cnn,$actualizar_spr); 
                ?> <script type="text/javascript"> window.location="../index.php";</script>  <?php
            }
        ?>
    </body>
</html>