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
        $Sdireccion = utf8_encode($_SESSION['USU_DIR']);
        $Sfonop = $_SESSION['USU_FONO'];
		$Scargo = utf8_encode($_SESSION['USU_CAR']);
		$Sestablecimiento = $_SESSION['EST_ID'];
        $Sdependencia = $_SESSION['USU_DEP'];
        $Scategoria = $_SESSION['USU_CAT'];
        $Sfingreso = $_SESSION['USU_FEC_ING'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $id_formulario = 4; //id del formulario creado en mantenedor
        $rechaza1= "RECHAZADO DIR";
        $rechaza2= "RECHAZADO J.D.";
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
                    $insertAcceso = "INSERT INTO LOG_ACCION (FOR_ID,LA_ACC,LA_FEC,LA_HORA,LA_IP_USU,USU_RUT) VALUES ('$id_formulario','$accion','$fecha','$hora','$ipcliente','$Srut')";
                    mysqli_query($cnn, $insertAcceso);
                    header("location: ../error.php");
                }
            }else{
                //si formulario no activo
                $accion = utf8_decode("ACCESO A PAGINA DESHABILITADA");
                $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC,FOR_ID,USU_RUT,LA_IP_USU,LA_FEC,LA_HORA,) VALUES ('$accion','$id_formulario','$Srut','$ipcliente','$fecha','$hora')";
                mysqli_query($cnn, $insertAcceso);
                header("location: ../desactivada.php");
            }
        }

        //busqueda de dirección establecimiento
                $queryEs = "SELECT EST_NOM, EST_DIR FROM ESTABLECIMIENTO WHERE (EST_ID =".$Sestablecimiento.")";
                $rs1 = mysqli_query($cnn, $queryEs);
                if (mysqli_num_rows($rs1) !=0){
                    $row=mysqli_fetch_row($rs1);
                    $uni_des = utf8_encode("$row[0]");
                    $ubi_lab = utf8_encode("$row[1]");
                /*else
                    $uni_des= utf8_decode("UNIDAD DE SEMPEÑO NO ENCONTRADA");
                    $ubi_lab= utf8_decode("DIRECCIÓN NO ENCONTRADA");*/
                }   
        /*BUSQUEDA DE PERMISO ANTERIOR
            $queryPSGR = "SELECT SPR_NDIA,SPR_FEC_INI,SPR_FEC_FIN,SPR_FEC FROM SOL_PSGR WHERE (USU_RUT='".$Srut."') AND (SPR_ESTA<>'".$rechaza1."') AND (SPR_ESTA <>'".$rechaza2."') ORDER BY SPR_FEC DESC";

            //echo $queryPSGR = "SELECT SPR_NDIA FROM SOL_PSGR WHERE (USU_RUT='".$Srut."') AND (SPR_ESTA<>'".$rechaza1."') OR (SPR_ESTA <>'".$rechaza2."')";

            $rsP =mysqli_query($cnn, $queryPSGR);
            if(mysqli_num_rows($rsP) !=0){
                $rowR=mysqli_fetch_row($rsP);
                $ndia1 =("$rowR[0]");
                $sprini =("$rowR[1]");
                $sprfin =("$rowR[2]");
            }else{
                $ndia1=0;
            }
            $diasusado=0;
            $respuesta = mysqli_query($cnn, $queryPSGR);
            while ($row_rs = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
                $diasusado = $diasusado+$row_rs[0];
            }              
        */
           
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
        <script type="text/javascript" src="../../include/js/moment.js"></script>
        <script>
            /*$(document).ready(function () {
                //Animaciones 
                $('select').material_select();
                $(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $('.tooltipped').tooltip({delay: 50});
                $('.datepicker').pickadate({firstDay: true});

            });*/
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                //$('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
            function Cargar(){
                //$('select').material_select('destroy');  
                $('select').formSelect('destroy');
                $("#mediodia").attr("disabled","disabled");
                $("#fechamediodia").attr("disabled","disabled");
                $("#fecha_dia").attr("disabled","disabled");                
                $("#dias").attr("disabled","disabled");
                $("#dantes").attr("disabled","disabled");
                $("#saldo").attr("disabled","disabled");
                $("#motivo").attr("disabled","disabled");
                $("#guardar").attr("disabled","disabled");
                //$("#anoper").material_select();
                $('#anoper').formSelect();
            }
            
            function MostrarJefatura(){
                var mtn=0;
                var mt = $("#motivo").val();
                var mtn = mt.length;

                if (mtn > 3){
                    $('#jefatura').formSelect();
                    //$("#jefatura").material_select();
                }else{
                    //$('select').material_select('destroy');
                    $('select').formSelect('destroy');
                    $("#guardar").attr("disabled","disabled");
                }
                
            }

            function MostrarDirigido(){
                $('#dirigido').formSelect();
                //$("#dirigido").material_select();
            }
          
            function Dias(){
                $("#Finicio").removeAttr("disabled");
                $("#Ftermino").attr("disabled","disabled");             
            }
            function ResultadoFechaIni(res_ini){
                if(res_ini.año == "CARGAR"){
                    //Materialize.toast("No se ha realizado la carga del año solicitado, favor solicitar carga a RRHH", 4000);
                    M.toast({html: 'No se ha realizado la carga del año solicitado, favor solicitar carga a RRHH'});
                }else{
                    var estado = res_ini.estado;
                    var sgr = res_ini.sgr;
                    var sgr_u = res_ini.sgr_u;
                    $("#dantes").val(sgr_u);
                    $("#ante1").val(sgr_u);
                    console.log(estado);
                    console.log(sgr);
                    console.log(sgr_u);
                }
            }
            function FecIni(){
                var ValFecIni = $("#Finicio").val();
                var post = $.post("../php/revisar_dia_psgr_fecini.php", { "fecha" : ValFecIni }, ResultadoFechaIni, 'json');
            }
            function ResultadoFecha(rfpsgr){
                var Fecpsgr = rfpsgr.fecha;
                //Materialize.toast(Fecpsgr, 4000);
                if(Fecpsgr == "si"){
                    //tiene que cambiar de dia
                    M.toast({html: 'Día contenido en otra solicitud'});
                    //Materialize.toast('Día contenido en otra solicitud', 4000);
                    $("#Finicio").val("");
                    $("#Ftermino").val("");
                }
            }
            function FIN(){
                var ValFecha = $("#Finicio").val();
                var ValFecha2 = $("#Ftermino").val();
                var post = $.post("../php/revisar_dia_psgr.php", { "fecha1" : ValFecha, "fecha2" : ValFecha2 }, ResultadoFecha, 'json');
            }  
            function validafecha(){    
                var d1 = $("#Finicio").val();
                var d1=moment(d1, 'YYYY/MM/DD', true).format('DD/MM/YYYY');
                var d2 = $("#Ftermino").val();
                var d2=moment(d2, 'YYYY/MM/DD', true).format('DD/MM/YYYY');
                var dia1 = $("#dias2").val();
                var año1 = moment(d1,'DD/MM/YYYY',true).format('YYYY');
                var año2 = moment(d2,'DD/MM/YYYY',true).format('YYYY');
                var ant = $("#dantes").val();
                ant=parseInt(ant);
                var d11=moment(d1, 'DD/MM/YYYY', true).format('YYYY MM DD');
                var d12=moment(d2, 'DD/MM/YYYY', true).format('YYYY MM DD');
                var diferencia= moment(d12).diff(d11,'days');              
                console.log(diferencia);
                diferencia = diferencia+1;

                    if (d12 < d11){
                        M.toast({html: 'Fecha no válida'});
                        //Materialize.toast('Fecha no válida', 4000);
                        $("#Ftermino").val("");
                        $("#motivo").attr("disabled","disabled");                        
                    }else{
                        if(año1==año2){
                                var ant = diferencia + ant;
                            if(ant <= 90){
                                $("#motivo").removeAttr("disabled");
                                $("#dias").val(diferencia);
                                $("#dias2").val(diferencia);
                                                               
                                var psgs1 =0;
                                var sal=0;
                                var sal= 90 - (psgs1+ant);
                                $("#saldo").val(sal);
                                $("#saldo1").val(sal);
                            }else{
                                M.toast({html: 'Excede el máximo de días permitido'});
                                //Materialize.toast('Excede el máximo de días permitido ', 4000);                                
                                $("#Ftermino").val("");
                                $("#motivo").attr("disabled","disabled");                                
                            }
                            
                        }else{
                            M.toast({html: 'Excede el máximo de días permitido'});
                            //Materialize.toast('Fecha fuera de rango', 4000);
                            $("#Ftermino").val("");
                            $("#motivo").attr("disabled","disabled");                            
                        }                                   
                    }                
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
            function anoper(){
                //recargar página enviando el año para consulta en sql (modificar consulta sql para traer permisos utilizados)

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
                        <h1>PERMISO SIN GOCE DE REMUNERACIONES</h1>
                        <form name="form" class="col s12" method="post">
                        <!--<div class="input-field col s2" >
                            <?php
                            $añoAct = date("Y");
                            $añoSiguiente = $añoAct + 1;
                            ?>
                         <select name="anoper" id="anoper" onchange="Listo();">                              
                             <option value="1"><?php echo $añoAct ?></option>
                             <option value="2"><?php echo $añoSiguiente ?></option>
                        </select>
                            </div>-->
                            <div class="input-field col s12" >
                         
                            </div>
                            </br>
                            </br>
                            <div class="input-field col s6">
                                <input type="text" name="rut_usuario" id="rut_usuario" class="validate" placeholder="" value="<?php echo $Srut;?>" disabled>
                                <label for="rut_usuario">RUT</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="fec_ingreso" id="fec_ingreso" class="validate" placeholder="" value="<?php echo $Sfingreso;?>" disabled>
                                <label for="rut_usuario">Fecha Ingreso</label>
                            </div>
                            <div class="input-field col s12">
                                <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $Snombre." ".$SapellidoP." ".$SapellidoM;?>" disabled>
                                <label for="nombre_usuario">Nombre Completo Funcionario</label>
                            </div>
                           <div class="input-field col s6">
                                <input type="text" name="unidad_desempeno" id="unidad_desempeno" class="validate" placeholder="" value="<?php echo $uni_des;?>" disabled>
                                <label for="icon_prefix">Unidad de Desempeño</label>
           
                            </div> 
                            <div class="input-field col s6">
                                <input type="text" name="ubicacion_laboral" id="ubicacion_laboral" class="validate" placeholder="" value="<?php echo $ubi_lab;?>" disabled>
                                <label for="icon_prefix">Ubicación Laboral</label>
                            </div>
                            <div class="input-field col s12">
                                <input type="text" name="direccion" id="direccion" class="validate" placeholder="" value="<?php echo $Sdireccion;?>" disabled>
                                <label for="icon_prefix">Dirección Particular</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="telefono1" id="telefono1" class="validate" placeholder="0"  onkeypress="return soloNumeros(event);">
                                <label for="icon_prefix">Teléfono Oficina</label>
                            </div>


                            <div class="input-field col s6">
                                <input type="text" name="telefono2" id="telefono2" class="validate" placeholder="" value="<?php echo $Sfonop;?>" disabled>
                                <label for="icon_prefix">Teléfono Particular</label>
                            </div>

                            <div class="col s12" align="left"><h6>AGRADECERÉ A LA DIRECCIÓN CONCEDERME:</h6></div>
                            </br>
                            </br>
                            </br>
                            <div class="input-field col s4">
                                <input type="text" name="dias" id="dias" class="validate" placeholder="0" disabled>
                                <label for="icon_prefix">N° de días</label> 
                            </div>
                                                       
                            <div class="input-field col s4">
                                <input type="text" class="datepicker" name="Finicio" id="Finicio" placeholder="Desde"  onchange="FecIni();" required>
                                <label for="icon_prefix">Desde</label>                
                            </div>
                            <div class="input-field col s4">
                                <input type="text" class="datepicker" name="Ftermino" id="Ftermino" placeholder="Hasta"  onchange="FIN();validafecha();" required> 
                                <label for="icon_prefix">Hasta</label>                      
                            </div>                          
                           
                                <div class="input-field col s4">                 
                                    <input type="text" id="dantes" class="validate" placeholder="" disabled>
                                    <label for="icon_prefix">N° de días utilizados antes de esta solicitud:</label>
                                </div>
                         
                            </br>
                            </br>
                            <div class="input-field col s4">                 
                                    <input type="text" id="saldo" class="validate" placeholder="">
                                    <label for="icon_prefix">Saldo considerando esta solicitud:</label>
                                </div>
                            </br>
                            </br>
                            <div class="input-field col s12">
                                <input type="text" name="motivo" id="motivo" class="validate" placeholder=""  onblur="MostrarJefatura();" required="">
                                <label for="icon_prefix">Motivo del Permiso</label>
                            </div>
                            <div class="input-field col s4">
                                <input type="text" name="dias2" id="dias2" class="validate" placeholder="0" style="display: none">
                            </div>
                            <div class="input-field col s4">
                                <input type="text" name="ante1" id="ante1" class="validate" placeholder=""  style="display: none">
                            </div>
                            <div class="input-field col s4">
                                <input type="text" name="saldo1" id="saldo1" class="validate" placeholder="0"  style="display: none">
                            </div>
                            <div class="col s12">
                                <h1>CONSIDERACIONES LEY N°19.378, ESTATUTO DE ATENCIÓN PRIMARIA DE SALUD MUNICIPAL</h1>
                                <p style="text-align:justify;">Artículo 17.- Los funcionarios podrán solicitar permisos para ausentarse de sus labores por motivos particulares hasta por seis días hábiles en el año calendario, con goce de sus remuneraciones. Estos permisos podrán fraccionarse por días o medios días, y serán concedidos o denegados por el Director del establecimiento, según las necesidades del servicio.</p>
                                <p style="text-align:justify;"><strong> Asimismo, podrán solicitar sin goce de remuneraciones, por motivos particulares, hasta tres meses de permiso en cada año calendario.</strong></p>
                                <p style="text-align:justify;">El límite señalado en el inciso anterior, no será aplicable en el caso de funcionarios que obtengan becas otorgadas de acuerdo a la legislación vigente.
                                Los funcionarios regidos por esta ley, que fueren elegidos alcaldes en conformidad a lo dispuesto en la ley N° 18.695, Orgánica Constitucional de Municipalidades, tendrán derecho a que se les conceda permiso sin goce de remuneraciones respecto de las funciones que estuvieren sirviendo en calidad de titulares, por todo el tiempo que comprenda su desempeño alcaldicio.
                                </p>
                            </div>
                            </br>
                            </br>
                            <div class="col s12">
                                <p style="text-align:justify; font-size:80%;">LA JEFATURA TIENE LA FACULTAD DE AUTORIZAR O DENEGAR EL PERMISO POR RAZONES DE SERVICIO</p>
                                <p style="text-align:justify; font-size:80%;">EL PERMISO DEBER SER REQUERIDO CON LA DEBIDA ANTELACIÓN, ANTES DE HACERSE EFECTIVO; DEBE SER FIRMADO POR EL INTERESADO Y LA FEJATURA RESPECTIVA ACREDITANDO CON ELLO SU CONFORMIDAD Y SER PRESENTADOO EN EL DEPARTAMENTO DE SALUD</p>
                            </div>
                            </br>
                            </br>
                            <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura" onchange="Listo();">
                                    <?php
                                        $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND ((USUARIO.USU_CAR = 'Director') OR (USUARIO.USU_CAR = 'Director (S)')) AND (ESTABLECIMIENTO.EST_NOM = '$Sdependencia')";
                                        $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){
                                                $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                            }
                                            echo "<option value='no' selected>Director o Director (S)</option>";
                                    ?>
                                </select>
                                <label for="icon_prefix">Seleccione Director o Director (S):</label>
                            </div>
                            </br>
                            </br>
                            <div class="col s12">
                                <button id="guardar" class="btn trigger" type="submit" name="guardar" value="Guardar">Enviar</button>
                            </div>
                         </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').material_select();
                $(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
            });
        </script>
        <?php
            if($_POST['guardar'] == "Guardar"){
            //primero rescato todos los datos del formulario
                $doc_id = $id_formulario;
                $fec = $fecha;
                $usu_rut = $Srut;
                $toa= $_POST['telefono1'];
                $ndia= $_POST['dias2'];
                $fec_ini = $_POST['Finicio'];
                $fec_fin = $_POST['Ftermino'];
                $fec_ini =  str_replace("/","-",$fec_ini);
                $fec_fin = str_replace("/","-",$fec_fin);
                $motivo = utf8_decode($_POST['motivo']);
                $usu_rut2 = $_POST['jefatura'];
                $estado = "SOLICITADO";
                $comentario ="";
                $diasant = $_POST['ante1'];
                $diasaldo = $_POST['saldo1'];
                $anoAct = date("Y");
                $añoActual = date("Y", strtotime($fec_ini));
								if($Sdependencia == "ILUSTRE MUNICIPALIDAD DE RENGO"){
									$guardar_permisoP = "INSERT INTO SOL_PSGR(DOC_ID,SPR_FEC,USU_RUT,SPR_TOA,SPR_NDIA,SPR_DIANT,SPR_DIASAL,SPR_FEC_INI,SPR_FEC_FIN,SPR_MOT,USU_RUT_DIR,SPR_ESTA,SPR_COM,SPR_ANO,SPR_DECRE) VALUES ($doc_id,'$fec','$usu_rut','$toa',$ndia,$diasant,$diasaldo,'$fec_ini','$fec_fin','$motivo','$usu_rut2','AUTORIZADO DIR','$comentario','$anoAct','NO')";
								}else{
									$guardar_permisoP = "INSERT INTO SOL_PSGR(DOC_ID,SPR_FEC,USU_RUT,SPR_TOA,SPR_NDIA,SPR_DIANT,SPR_DIASAL,SPR_FEC_INI,SPR_FEC_FIN,SPR_MOT,USU_RUT_DIR,SPR_ESTA,SPR_COM,SPR_ANO,SPR_DECRE) VALUES ($doc_id,'$fec','$usu_rut','$toa',$ndia,$diasant,$diasaldo,'$fec_ini','$fec_fin','$motivo','$usu_rut2','$estado','$comentario','$anoAct','NO')";
								}

                mysqli_query($cnn, $guardar_permisoP);
                //actualizo banco dias
                $query_bd = "SELECT BD_ID, BD_SGR, BD_SGR_USADO FROM BANCO_DIAS WHERE (USU_RUT = '$usu_rut') AND (BD_ANO = '$añoActual')";
                //echo $query_bd;
                $respuesta_bd = mysqli_query($cnn,$query_bd);
                if(mysqli_num_rows($respuesta_bd) != 0){
                    $row_bd = mysqli_fetch_row($respuesta_bd);
                    $id_bd = $row_bd[0];
                    $sgr = $row_bd[1];
                    $sgr_u = $row_bd[2];
                }
                $sgr = $sgr - $ndia;
                $sgr_u = $sgr_u + $ndia;
                $update_bd = "UPDATE BANCO_DIAS SET BD_SGR = $sgr,BD_SGR_USADO = $sgr_u WHERE BD_ID = $id_bd";
                //echo $update_bd;
                mysqli_query($cnn,$update_bd);
                
                $consultaIdP = "SELECT SPR_ID FROM SOL_PSGR WHERE(DOC_ID = '$doc_id') AND (USU_RUT = '$usu_rut') AND (SPR_FEC_INI = '$fec_ini') AND (SPR_FEC_FIN = '$fec_fin') AND (SPR_NDIA = '$ndia')";
                //echo $consultaIdP;
                $rsCID = mysqli_query($cnn, $consultaIdP);
                if (mysqli_num_rows($rsCID) != 0){
                    $rowCID = mysqli_fetch_row($rsCID);
                    $Id_for_actual = $rowCID[0];
                    $FecActual = date("Y-m-d");
                    $HorActual = date("H:i:s");
                    $HPAccion = "CREA PERMISO SIN GOCE DE REMUNERACIONES";
                    $guardar_historial = "INSERT INTO HISTO_PERMISO(HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ('$Id_for_actual','$usu_rut','$FecActual','$HorActual',$doc_id, '$HPAccion')";
                    //echo $guardar_historial;
                    mysqli_query($cnn, $guardar_historial);
                    ?>  <script type="text/javascript"> window.location="../index.php";</script> <?php
                }
            }
        ?>
    </body>
</html>


