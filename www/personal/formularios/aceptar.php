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
        $Sdependencia = $_SESSION['USU_DEP'];
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 21;
        $ipcliente = getRealIP();
        $sp_id = $_GET['id'];
        $doc_id = $_GET['docid'];
        if($doc_id == 5){
            $queryEsta ="SELECT OE_ID,OE_ESTA FROM OT_EXTRA WHERE (OE_ID = '$sp_id')";
            $rsE = mysqli_query($cnn, $queryEsta);
            if (mysqli_num_rows($rsE) != 0){
                $rowE = mysqli_fetch_row($rsE);
                if ($rowE[0] == $sp_id){
                    $oe_esta = $rowE[1];
                }
            }else{  
              session_destroy();
              header("location: ../../index.php");
            }
        }
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
        }else{  
          session_destroy();
          header("location: ../../index.php");
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
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
            function CargarIndex(){
                $("#aceptar").attr("disabled","disabled");
                $( "#div_text" ).hide();
                $( "#div_select" ).hide();
                //$('select').material_select('destroy');
            } 
            function Autoriza(){
                var doc_id = $("#codigo_doc").val();
                var select = $("#director").val();
									if (doc_id == 5){
											if(select == "JDyDIR"){
													$( "#div_text" ).show();
													$( "#div_select" ).show();
											}else{
													$("#aceptar").removeAttr("disabled");
											}
									}else{
											$("#aceptar").removeAttr("disabled");
									}
            }
            function AutorizaJefe(){
								$("#aceptar").removeAttr("disabled"); 
            }
						function Autorizar(){
							//$("#aceptar").attr("disabled","disabled");
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
                        <h4 class="light">Aceptar Permiso</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                            </br></br></br>
                            <div class="input-field col s6">
                                <input type="text" name="codigo_doc" id="codigo_doc" class="validate" style="display: none" value="<?php echo $doc_id;?>">
                            </div>
                            <?php
                                if($oe_esta == "AUTORIZADO J.D." && $doc_id == 5){
                                    echo "<div class='col s12'>Favor indicar Director Departamento de Salud o Subrogante si corresponde para Visto Bueno del Formulario de Horas Extras</div>";
                                    echo "</br>";
                                    echo '<div class="input-field col s12"onclick="AutorizaJefe();">';
                                        echo '<select name="visto_bueno" id="visto_bueno" >';
                                            $queryVB = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND ((USUARIO.USU_CAR = 'Director') OR (USUARIO.USU_CAR = 'Director (S)')) AND (ESTABLECIMIENTO.EST_NOM = 'DEPARTAMENTO DE SALUD')";
                                            $resultadoVB =mysqli_query($cnn, $queryVB);  
                                            while($regVB =mysqli_fetch_array($resultadoVB)){
                                                $MuestroVB = $regVB[1]." ".$regVB[2]." ".$regVB[3];
                                                printf("<option value=\"$regVB[0]\">$MuestroVB</option>");
                                            }
                                           //echo "<option value='no' selected disabled>Director Departamento de Salud</option>";
                                        echo '</select>';
                                    echo '<label for="visto_bueno">Director Departamento de Salud</label>';
                                    echo '</div>';
                                    $VB = "OK";
                                }else{
                                    if($Scargo == "Director"){
                                        echo "<div class='col s12'>Favor seleccionar AUTORIZAR COMO JEFE DIRECTO Y DIRECTOR</div>";
                                        echo "</br>";
                                        echo '<div class="input-field col s12" onclick="Autoriza();">';
                                            echo '<select name="director" id="director">';
                                                $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND ((USUARIO.USU_CAR = 'Director') OR (USUARIO.USU_CAR = 'Director (S)')) AND (ESTABLECIMIENTO.EST_NOM = '$Sdependencia')";
                                                $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                                echo "<option value='JDyDIR'>AUTORIZAR COMO JEFE DIRECTO Y DIRECTOR</option>";      
                                                while($regJ =mysqli_fetch_array($resultadoJ)){
                                                    $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                    printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                                }
                                                //echo "<option value='no' selected disabled>Director</option>";
                                            echo '</select>';
                                        echo '<label for="director">Director</label>';
                                        echo '</div>';
                                    }elseif($Scargo == "Director (S)"){
                                        echo "<div class='col s12'>Si usted esta subrogando al director en este momento, puede autorizar inmediatamente como Jefe Directo y como Director (S) o puede derivar el permiso </div>";
                                        echo "</br>";
                                        echo '<div class="input-field col s12" >';
                                            echo '<select name="director" id="director" onchange="Autoriza();">';
                                                $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND ((USUARIO.USU_CAR = 'Director') OR (USUARIO.USU_CAR = 'Director (S)')) AND (ESTABLECIMIENTO.EST_NOM = '$Sdependencia')";
                                                $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                                echo "<option value='JDyDIR'>AUTORIZAR COMO JEFE DIRECTO Y DIRECTOR (S)</option>";      
                                                while($regJ =mysqli_fetch_array($resultadoJ)){
                                                    $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                    printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                                }
                                                //echo "<option value='no' selected disabled>Director</option>";
                                            echo '</select>';
                                        echo '<label for="director">Director</label>';
                                        echo '</div>';
                                    }else{
																				if($Sestablecimiento == 9999){
																					echo "<div class='col s12'>Favor indicar Director para derivar el permiso</div>";
																					echo "</br>";
																					echo '<div class="input-field col s12" onclick="Autoriza();">';
																							echo '<select name="director" id="director">';
																									$queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND ((USUARIO.USU_CAR = 'Director') OR (USUARIO.USU_CAR = 'Director (S)'))";
																									$resultadoJ =mysqli_query($cnn, $queryJefatura);  
																									while($regJ =mysqli_fetch_array($resultadoJ)){
																											$MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
																											printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
																									}
																									//echo "<option value='no' selected disabled>Director</option>";
																							echo '</select>';
																					echo '<label for="director">Director</label>'; 
																					echo '</div>';
																				}else{
																					echo "<div class='col s12'>Favor indicar Director para derivar el permiso</div>";
																					echo "</br>";
																					echo '<div class="input-field col s12" onclick="Autoriza();">';
																							echo '<select name="director" id="director">';
																									$queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND ((USUARIO.USU_CAR = 'Director') OR (USUARIO.USU_CAR = 'Director (S)')) AND (ESTABLECIMIENTO.EST_NOM = '$Sdependencia')";
																									$resultadoJ =mysqli_query($cnn, $queryJefatura);
																									while($regJ =mysqli_fetch_array($resultadoJ)){
																											$MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
																											printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
																									}
																									//echo "<option value='no' selected disabled>Director</option>";
																							echo '</select>';
																					echo '<label for="director">Director</label>'; 
																					echo '</div>';	
																				}
                                    }
                                    if($doc_id == 5 && ($Scargo == "Director" || $Scargo == "Director (S)")){
                                        echo "<div class='col s12' id='div_text' name='div_text'>Favor indicar Director Departamento de Salud o Subrogante si corresponde para Visto Bueno del Formulario de Horas Extras</div>";
                                        echo "</br>";
                                        echo '<div class="input-field col s12" id="div_select" name="div_select" onclick="AutorizaJefe();">';
                                            echo '<select name="visto_bueno" id="visto_bueno" >';
                                                $queryVB = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND ((USUARIO.USU_CAR = 'Director') OR (USUARIO.USU_CAR = 'Director (S)')) AND (ESTABLECIMIENTO.EST_NOM = 'DEPARTAMENTO DE SALUD')";
                                                $resultadoVB =mysqli_query($cnn, $queryVB);  
                                                while($regVB =mysqli_fetch_array($resultadoVB)){
                                                    $MuestroVB = $regVB[1]." ".$regVB[2]." ".$regVB[3];
                                                    printf("<option value=\"$regVB[0]\">$MuestroVB</option>");
                                                }
                                               // echo "<option value='no' selected disabled>Director Departamento de Salud</option>";
                                            echo '</select>';
                                        echo '<label for="visto_bueno">Director Departamento de Salud</label>';
                                        echo '</div>';
                                    }
                                }
                            ?>
                            <div class="input-field col s12">
                                <button class="btn trigger" type="submit" name="aceptar" id="aceptar" value="Autorizar" onclick="Autorizar();">Autorizar</button>
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
            if($_POST['aceptar'] == "Autorizar"){
                if($VB == "OK"){
                    //rescato id usuario e id formulario
                    $query ="SELECT OE_ID,USU_RUT FROM OT_EXTRA WHERE (OE_ID = '$sp_id')";
                    $rs = mysqli_query($cnn, $query);
                    if (mysqli_num_rows($rs) != 0){
                        $rowA = mysqli_fetch_row($rs);
                        if ($rowA[0] == $sp_id){
                            $usu_rut = $rowA[1];
                        }
                    }
                    $Actualfecha = date("Y-m-d");
                    $Actualhora = date("H:i:s");
                    $usu_rut_vb = $_POST['visto_bueno'];
                    $accionRealizada = utf8_decode("AUTORIZADO COMO DIRECTOR POR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM."  Y DERIVADO PARA VISTO BUENO DEL DIRECTOR DE DEPARTAMENTO.");
                    $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($sp_id,'$usu_rut','$Actualfecha','$Actualhora',$doc_id,'$accionRealizada')";
                    mysqli_query($cnn, $insertAccion);
                    $actualizarSolPermi = "UPDATE OT_EXTRA SET OE_ESTA = 'AUTORIZADO DIR', USU_RUT_VB = '$usu_rut_vb'  WHERE (OE_ID = '$sp_id')";
                    mysqli_query($cnn, $actualizarSolPermi);
                }else{
                    $usu_rut_dir = $_POST['director'];
                    $Actualfecha = date("Y-m-d");
                    $Actualhora = date("H:i:s");
                    if ($doc_id == 1 || $doc_id == 2 || $doc_id == 3){
                        //guardar en sol_permi
                        $TABLA = "SOL_PERMI";
                        $ID = "SP_ID";
                        $ESTADO = "SP_ESTA";
                    }elseif($doc_id == 5){
                        $TABLA = "OT_EXTRA";
                        $ID = "OE_ID";
                        $ESTADO = "OE_ESTA";
                    }
                    //rescato id usuario e id formulario
                    $query ="SELECT $ID,USU_RUT FROM $TABLA WHERE ($ID = '$sp_id')";
                    $rs = mysqli_query($cnn, $query);
                    if (mysqli_num_rows($rs) != 0){
                        $rowA = mysqli_fetch_row($rs);
                        if ($rowA[0] == $sp_id){
                            $usu_rut = $rowA[1];
                        }
                    }
                    if ($usu_rut_dir == "JDyDIR"){
                        //guardar rut_jf en rut_dir
                        $usu_rut_jf = $Srut;
                        $usu_rut_dir = $Srut;
                        $accionRealizada = utf8_decode("AUTORIZADO COMO DIRECTOR Y JEFE DIRECTO POR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
                        $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($sp_id,'$usu_rut','$Actualfecha','$Actualhora',$doc_id,'$accionRealizada')";
                        mysqli_query($cnn, $insertAccion);
                        if($doc_id == 5){
                            $usu_rut_vb = $_POST['visto_bueno'];
                            $actualizarSolPermi = "UPDATE $TABLA SET $ESTADO = 'AUTORIZADO DIR',USU_RUT_DIR = '$usu_rut_dir',USU_RUT_VB = '$usu_rut_vb'  WHERE ($ID = '$sp_id')";
                            mysqli_query($cnn, $actualizarSolPermi);
                        }else{
                            $actualizarSolPermi = "UPDATE $TABLA SET $ESTADO = 'AUTORIZADO DIR',USU_RUT_DIR = '$usu_rut_dir'  WHERE ($ID = '$sp_id')";
                            mysqli_query($cnn, $actualizarSolPermi);
                        }
                    }else{
                        $accionRealizada = utf8_decode("AUTORIZADO JEFE DIRECTO POR :  ".$Snombre." ".$SapellidoP." ".$SapellidoM);
                        $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($sp_id,'$usu_rut','$Actualfecha','$Actualhora',$doc_id,'$accionRealizada')";
                        mysqli_query($cnn, $insertAccion);
                        $actualizarSolPermi = "UPDATE $TABLA SET $ESTADO = 'AUTORIZADO J.D.',USU_RUT_DIR = '$usu_rut_dir'  WHERE ($ID = '$sp_id')";
                        mysqli_query($cnn, $actualizarSolPermi);
                    }
                }
                ?> <script type="text/javascript"> window.location="../index.php";</script>  <?php
            }
        ?>
    </body>
</html>