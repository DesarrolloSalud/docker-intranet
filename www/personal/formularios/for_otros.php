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
        $id_formulario = 47;
        date_default_timezone_set("America/Santiago");
        $fecha_hoy = date("Y-m-d");
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
                    //reviso si tengo algun ot_extra en creacion
                    $consultaEncreacion = "SELECT FO_ID FROM FOR_OTROS WHERE (USU_RUT = '$Srut') AND (FO_ESTA = 'EN CREACION')";
                    $respuestaEnCreacion = mysqli_query($cnn, $consultaEncreacion);
                    if (mysqli_num_rows($respuestaEnCreacion) == 0){
                        //usuario no tiene ningun folio tomado
                        $consultaNuevoId = "SELECT FO_ID FROM FOR_OTROS ORDER BY FO_ID DESC";
                        $respuestaNuevoId = mysqli_query($cnn, $consultaNuevoId);
                        $AñoActual = date("Y");
                        if (mysqli_num_rows($respuestaNuevoId) == 0){
                            $NuevoID = 1;
                            $FolioUno = "INSERT INTO FOR_OTROS (FO_ID,DOC_ID,USU_RUT,FO_ESTA,FO_ANO,FO_FEC,FO_ADJUNTO) VALUES ($NuevoID,12,'$Srut', 'EN CREACION','$AñoActual','$fecha_hoy','NO')";
                            mysqli_query($cnn, $FolioUno);
                        }else{
                            $rowNuevoId = mysqli_fetch_row($respuestaNuevoId);
                            $UltimoID = $rowNuevoId[0];
                            $NuevoID = $UltimoID + 1;
                            $FolioUno = "INSERT INTO FOR_OTROS (FO_ID,DOC_ID,USU_RUT,FO_ESTA,FO_ANO,FO_FEC,FO_ADJUNTO) VALUES ($NuevoID,12,'$Srut', 'EN CREACION','$AñoActual','$fecha_hoy','NO')";
                            mysqli_query($cnn, $FolioUno);
                        }
                    }else{
                        $rowFolioUsado = mysqli_fetch_row($respuestaEnCreacion);
                        $NuevoID = $rowFolioUsado[0];
                    }  
                }else{
                    //no tengo acceso
                    $accion = utf8_decode("ACCESO DENEGADO");
                    $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha_hoy', '$hora')";
                    mysqli_query($cnn, $insertAcceso);
                    header("location: ../error.php");
                }
            }else{
                //si formulario no activo
                $accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
                $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha_hoy', '$hora')";
                mysqli_query($cnn, $insertAcceso);
                header("location: ../desactivada.php");
            }
        }
	}	
?>
<html>
    <head>
        <title>Personal Salud</title>
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
            function Cargar(){
                $( "#fallecimientoT" ).hide();
                $("#Finicio").attr("disabled","disabled");
                $("#Ftermino").attr("disabled","disabled");
                $("#guardar").attr("disabled","disabled");
                $('select').formSelect('destroy');
            }
            function Nacimiento(){
              //5 dias habiles
              var doc_id = $('input[name="seleccion"]:checked').val();
              $("#Tipo_Doc").val(doc_id);
              $("#matrimonio").attr("disabled","disabled");
              $("#fallecimiento").attr("disabled","disabled");
              //habilitar boton para seleccionar fecha inicio
              $("#Finicio").removeAttr("disabled");
            }
            function Matrimonio(){
              //5 dias habiles un mes de anticipacion
              var doc_id = $('input[name="seleccion"]:checked').val();
              $("#Tipo_Doc").val(doc_id);
              $("#nacimiento").attr("disabled","disabled");
              $("#fallecimiento").attr("disabled","disabled");
              //habilitar boton para seleccionar fecha inicio
              $("#Finicio").removeAttr("disabled");
            }
            function Fallecimiento(){
              $("#nacimiento").attr("disabled","disabled");
              $("#matrimonio").attr("disabled","disabled");
              //habilitar opciones
              $( "#fallecimientoT" ).show();
            }
            function FallecimientoPM(){
              //3 dias habiles
              var doc_id = $('input[name="selec"]:checked').val();
              $("#Tipo_Doc").val(doc_id);
              $("#fallecimientoH").attr("disabled","disabled");
              $("#fallecimientoG").attr("disabled","disabled");
              //habilitar boton para seleccionar fecha inicio
              $("#Finicio").removeAttr("disabled");
            }
            function FallecimientoH(){
              //7 dias corridos
              var doc_id = $('input[name="selec"]:checked').val();
              $("#Tipo_Doc").val(doc_id);
              $("#fallecimientoPM").attr("disabled","disabled");
              $("#fallecimientoG").attr("disabled","disabled");
              //habilitar boton para seleccionar fecha inicio
              $("#Finicio").removeAttr("disabled");
            }
            function FallecimientoG(){
              //3 dias habiles
              var doc_id = $('input[name="selec"]:checked').val();
              $("#Tipo_Doc").val(doc_id);
              $("#fallecimientoPM").attr("disabled","disabled");
              $("#fallecimientoH").attr("disabled","disabled");
              //habilitar boton para seleccionar fecha inicio
              $("#Finicio").removeAttr("disabled");
            }
            function MostrarFechaFIN(mff){
                var DocTipo = mff.TipoDoc;
                var fechaFIN = mff.fechaFIN;
                $("#Ftermino").val(fechaFIN);
                $("#Ftermino2").val(fechaFIN);
                $("#jefatura").formSelect();
            }
            function SegundaValidacionFechaINI(svfi){
                var FeriadoFecHora = svfi.dia;
                if(FeriadoFecHora == "si"){
                    //tiene que cambiar de dia
                    M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                    $("#Finicio").val("");
                }else if(FeriadoFecHora == "no"){
                    //pasar a siguiente validancion
                    var TipoDoc = $("#Tipo_Doc").val();
                    if (TipoDoc == 1){
                      // nacimiento 5 dias habiles
                      var CantDias = 5;
                      var FechaInicio = $("#Finicio").val();
                      var post = $.post("../php/validar_fechaTerminoOP.php", { "TipoDoc" : TipoDoc, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN, 'json');
                    }else if(TipoDoc == 2){
                        //matrimonio 5 dias habiles anticipacion de un mes (segunda validacion)     
                      var FechaInicio = $("#Finicio").val();
                      var fecha1 = moment(FechaInicio);
                      var hoy = $("#hoy").val();
                      var fecha2 = moment(hoy);
                      var diferencia = fecha1.diff(fecha2, 'days');
                      if(diferencia >= 30){
                        var CantDias = 5;
                        var FechaInicio = $("#Finicio").val();
                        var post = $.post("../php/validar_fechaTerminoOP.php", { "TipoDoc" : TipoDoc, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN, 'json');
                      }else{
                        M.toast({html: 'La fecha debe ser con un mes de anticipacion'});
                        $("#Finicio").val("");
                      }
                    }else if(TipoDoc == 4){
                        // fallecimiento padre o madre 3 dias habiles
                      var CantDias = 3;
                      var FechaInicio = $("#Finicio").val();
                      var post = $.post("../php/validar_fechaTerminoOP.php", { "TipoDoc" : TipoDoc, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN, 'json');
                    }else if(TipoDoc == 5){
                        //fallecimiento hijo 7 dias corridos
                      var CantDias = 7;
                      var FechaInicio = $("#Finicio").val();
                      var post = $.post("../php/validar_fechaTerminoOP.php", { "TipoDoc" : TipoDoc, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN, 'json');
                    }else if(TipoDoc == 6){
                        //fallecimiento hijo en gestacion 3 dias habiles
                      var CantDias = 3;
                      var FechaInicio = $("#Finicio").val();
                      var post = $.post("../php/validar_fechaTerminoOP.php", { "TipoDoc" : TipoDoc, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN, 'json');
                    }
                }
            }
            function ValidoFechaINI(){
                var TipoDoc = $("#Tipo_Doc").val();
                if(TipoDoc == 2){

                }
                var ValFecha = $("#Finicio").val();
                var post = $.post("../php/revisar_feriado.php", { "fecha" : ValFecha }, SegundaValidacionFechaINI, 'json');
            }
            function CargaArc(){
              var fo_id = $("#folio").val();
              var iddoc = $("#Tipo_Doc").val();            
              window.open('../php/carga_certificado.php'+"?tipo="+iddoc+"&id="+fo_id, "Subir Archivo" , "width=650,height=450,scrollbars=yes,menubar=yes,toolbar=yes,location=no");           
            }
            function RespuestaGuardar(m){
                var mensaje = m.Mensaje;
                if(mensaje == "NO"){
                    M.toast({html: "Para poder enviar, se debe adjuntar el certificado correspondiente"});
                }else{
                    $("#guardar").removeAttr("disabled");
                }
                
            }
            function Guardar(){
                var fo_id = $("#folio").val();
                var tipo = $("#Tipo_Doc").val(); 
                var post = $.post("../php/validar_certificado.php", { "id" : fo_id, "tipo" : tipo }, RespuestaGuardar, 'json');
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
                        <h4 class="light">Otros Permisos</h4>
                         <form name="form" class="col s12" method="post" id="formSolPermi">                           
                            </br>
                            </br>
                            <div class="col s4">
                              <label>
                                  <input class="with-gap col s4" name="seleccion" value="1" type="radio" id="nacimiento" onclick="Nacimiento();"/>
                                  <span>Nacimiento</span>
                              </label>
                            </div>
                            <div class="col s4">
                              <label>  
                                <input class="with-gap col s4" name="seleccion" value="2" type="radio" id="matrimonio" onclick="Matrimonio();"/>
                                <span>Matrimonio</span>
                               </label>
                            </div>
                            <div class="col s4">
                              <label>
                                <input class="with-gap col s4" name="seleccion" value="3" type="radio" id="fallecimiento" onclick="Fallecimiento();"/>
                                <span>Fallecimiento</span>
                              </label>
                            </div>
                            <!-- si el motivo es fallecimiento debe seleccionar una de las siguientes opciones -->
                            <div class="col s12" name="fallecimientoT" id=fallecimientoT>
                              <div class="col s4">
                                  <label>
                                    <input class="with-gap col s4" name="selec" value="4" type="radio" id="fallecimientoPM" onclick="FallecimientoPM();"/>
                                    <span>Fallecimiento Padre o Madre</span>
                                  </label>
                              </div>
                              <div class="col s4">
                                  <label>
                                    <input class="with-gap col s4" name="selec" value="5" type="radio" id="fallecimientoH" onclick="FallecimientoH();"/>
                                    <span>Fellecimiento Hijo</span>
                                  </label>
                              </div>
                              <div class="col s4">  
                                  <label>
                                    <input class="with-gap col s4" name="selec" value="6" type="radio" id="fallecimientoG" onclick="FallecimientoG();"/>
                                    <span>Fallecimiento Hijo en Gestacion</span>
                                  </label>
                              </div>
                            </div>
                            <input type="text" name="Tipo_Doc" id="Tipo_Doc" class="validate" style="display:none">
                            </br>
                            </br>
                            <div class="input-field col s2">
                                <input type="text" name="folio" id="folio" class="validate" placeholder="" value="<?php echo $NuevoID;?>" disabled>
                                <label for="folio">Folio</label>
                            </div> 
                            <div class="input-field col s10">
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
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" class="datepicker" name="Finicio" id="Finicio" placeholder="Fecha de Inicio" required onchange="ValidoFechaINI();" required> 
                                            </div> 
                                        </td>
                                        <td>
                                            <div class="input-field col s10">
                                                <input type="text" class="datepicker" name="Ftermino" id="Ftermino" placeholder="Fecha de Termino" required>
                                                <input type="text" class="datepicker" name="Ftermino2" id="Ftermino2" class="validate" style="display: none">
                                                <input type="text" class="datepicker" name="hoy" id="hoy" class="validate" value="<?php echo $fecha_hoy;?>" style="display: none">
                                            </div>
                                        </td>
                                    </tr>                                                               
                                </tbody>
                            </table>
                            </br>
                            <div class=s12>
                                <td><button class="btn trigger" name="carga_arc" id="carga_arc" type="button" onclick="CargaArc();">Adjuntar Certificado</button></td>
                            </div>
                            <!--<div class="right col s12 m8 l8 block">
                                <div align="right"><a href="../php/carga_convo.php?id1=<?php echo $NuevoID;?>&id2=<?php echo "8";?>" target="_blank width='400' height='300'" class="btn trigger">Volver</a></div>
                            </div>-->       
                            </br>
                            <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura" onchange ="Guardar();">
                                    <?php
                                        $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND ((ESTABLECIMIENTO.EST_NOM = '$Sdependencia') OR (ESTABLECIMIENTO.EST_NOM = 'MULTIESTABLECIMIENTO'))";
                                        $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){
                                                $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                            }
                                            //echo "<option value='no' selected>Jefe Directo</option>";
                                    ?>
                                </select>
							    <label for="jefatura">Jefe Directo</label>
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
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
            });
        </script>
        <?php
            if($_POST['guardar'] == "Guardar"){
                //primero rescato todos los datos del formulario
                //USU_JD, FO_CANT, FO_FEC_INI, FO_FEC_FIN, FO_ESTA, FO_ANO, FO_DEC
                $tipo = $_POST['Tipo_Doc'];
                //segun tipo es la catidad de dias: 1->5; 2->5 ; 4->3 ; 5->7 ; 6->3
                if($tipo == 1 || $tipo == 2){
                    $cant = 5;
                }elseif($tipo == 4 || $tipo == 6){
                    $cant = 3;
                }elseif($tipo == 5){
                    $cant = 7;
                }
                $usu_rut_jd = $_POST['jefatura'];
                $estado = 'SOLICITADO';
                $AñoActual = date("Y");
                $fec_ini = $_POST['Finicio'];
                $fec_fin = $_POST['Ftermino2'];
                $update = "UPDATE FOR_OTROS SET USU_RUT_JD = '$usu_rut_jd', FO_CANT = $cant, FO_FEC_INI = '$fec_ini', FO_FEC_FIN = '$fec_fin', FO_ESTA = '$estado', FO_ANO = '$AñoActual', FO_FEC = '$fecha_hoy' WHERE FO_ID = $NuevoID";
                mysqli_query($cnn, $update);
                //revisar el ID des permiso que corresponde
                $Id_for_actual = $NuevoID;
                $FecActual = date("Y-m-d");
                $HorActual = date("H:i:s");
                $HPAccion = "CREA PERMISO";
                $guardar_historial = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($Id_for_actual,'$Srut','$FecActual','$HorActual',12, '$HPAccion')";
                //echo $guardar_historial;
                mysqli_query($cnn, $guardar_historial);
                ?> <script type="text/javascript"> window.location="../index.php";</script> <?php
            }
        ?>
    </body>
</html>