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
        $id_formulario = 29;
        $id_decreto = $_GET['id'];
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
                    $queryDecreto = "SELECT DF_FEC,DF_NUM,DF_TEXT_VISTOS,DF_TEXT_CONSIDERANDO,DF_TEXT_DECRETO,DF_TEXT_FIN,DF_NOM_DIR,DF_DIR_SUB,DF_DIR_GEN,DF_NOM_SEC,DF_SEC_SUB,DF_SEC_GEN,DF_RESPONSABLES FROM DECRETOS_FOR WHERE DF_ID = $id_decreto";
                    $respuestaQD = mysqli_query($cnn,$queryDecreto);
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
						td,th{
								text-align: center;
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
            });

						function SubDirector(){
							if( $('#dic_sub').prop('checked') ) {
									//SI ES SUBROGANTE
									var dic = "(S)";
									$("#df_dir_sub").val(dic);
							}else{
									var dic = "";
									$("#df_dir_sub").val(dic);
							}
						}
						function SubSecretaria(){
							if( $('#sec_sub').prop('checked') ) {
									//SI ES SUBROGANTE
									var sec = "(S)";
									$("#df_sec_sub").val(sec);
							}else{
									var sec = "";
									$("#df_sec_sub").val(sec);
							}
						}
            function Invalidar(id){
                window.open('http://200.68.34.158/personal/formularios/desdecretar.php?id='+id,'_blank');  
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
                        <h4 class="light">Editor de Decretos</h4>
                        <div class="row">
                        <?php
                          $row_d = mysqli_fetch_array($respuestaQD);
                          echo "<form name='form_dec class='col s12' method='post'>";
                            echo "<div class='input-field col s6'>";
                                echo '<input type="text" name="num_decre" id="num_decre" class="validate" size="10" maxlength="4" required onkeypress="return soloNumeros(event)" value="'.$row_d[1].'">';
                                echo "<label for='num_decre'>Numero Decreto :</label>"; 
                            echo "</div>";
                            echo "<div class='input-field col s6'>";
                                echo '<input type="text" class="datepicker" name="fec_decre" id="fec_decre" required value="'.$row_d[0].'">';
                                echo "<label for='fec_decre'>Fecha Decreto :</label>"; 
                            echo "</div>";
                            $text_visto = $row_d[2];
                            $txt_visto = str_replace("<br />", "\n", $text_visto);			          
                            nl2br($txt_visto);
                            echo '<div class="input-field col s12">';
                                echo '<textarea id="vistos" name="vistos" class="materialize-textarea">'.$txt_visto.'</textarea>';
                                echo '<label for="vistos">VISTOS</label>';
                            echo '</div>';
                            $text_consi = $row_d[3];
                            $txt_consi = str_replace("<br />", "\n", $text_consi);			          
                            nl2br($txt_consi);
                            echo '<div class="input-field col s12">';
                                echo '<textarea id="considerando" name="considerando" class="materialize-textarea">'.$txt_consi.'</textarea>';
                                echo '<label for="considerando">Considerando</label>';
                            echo '</div>';
                            $text_decreto = $row_d[4];
                            $txt_decreto = str_replace("<br />", "\n", $text_decreto);			          
                            nl2br($txt_decreto);
                            echo '<div class="input-field col s12">';
                                echo '<textarea id="decreto" name="decreto" class="materialize-textarea">'.$txt_decreto.'</textarea>';
                                echo '<label for="decreto">Decreto</label>';
                            echo '</div>';
                            $text_final = $row_d[5];
                            $txt_final = str_replace("<br />", "\n", $text_final);			          
                            nl2br($txt_final);
                            echo '<div class="input-field col s12">';
                              echo '<textarea id="fin_decreto" name="fin_decreto" class="materialize-textarea">'.$txt_final.'</textarea>';
                              echo '<label for="fin_decreto">Fin Decreto</label>';
                            echo '</div>';
                            echo "</br>";
                            echo '<div class="input-field col s6">';
                                echo '<input value="'.$row_d[6].'" id="director" type="text" class="validate" name="director" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                                echo '<label for="director">Favor indicar Director de Salud o Subrogante si corresponde (NOMBRE COMPLETO) :</label>';
                            echo '</div>';
                            echo '<div class="input-field col s4">';
                              echo '<label>';
                                if($row_d[7] == "(S)"){
                                  echo '<input type="checkbox" checked="checked" class="filled-in" id="dic_sub" name="dic_sub" onchange="SubDirector();"/>';
                                }else{
                                  echo '<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" onchange="SubDirector();"/>';
                                }
                                echo '<span>Subrogante</span>';
                              echo '</label>';
                            echo '</div>';
                            echo '<div class="input-field col s2">';
                              echo '<select name="gen_alc" id="gen_alc" >';
                                echo '<option value="'.$row_d[8].'">'.$row_d[8].'</option>';
                                echo '<option value="DIRECTOR">DIRECTOR</option>';
                                echo '<option value="DIRECTORA">DIRECTORA</option>';
                                echo '<option value="ALCALDE">ALCALDE</option>';
                                echo '<option value="ALCALDESA">ALCALDESA</option>';
                              echo '</select>';
                              echo '<label>Genero</label>';
                            echo '</div>';
                            echo '<div class="input-field col s6">';
                              echo '<input type="text" name="secretaria" id="secretaria" class="validate" value="'.$row_d[9].'" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">';
                              echo '<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>';
                            echo '</div>';
                            echo '<div class="input-field col s4">';
                              echo '<label>';
                                if($row_d[10] == "(S)"){
                                  echo '<input type="checkbox" checked="checked" class="filled-in" id="sec_sub" name="sec_sub" onchange="SubSecretaria();"/>';
                                }else{
                                  echo '<input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub" onchange="SubSecretaria();"/>';
                                }
                                echo '<span>Subrogante</span>';
                              echo '</label>';
                            echo '</div>';
                            echo '<div class="input-field col s2">';
                              echo '<select name="gen_sec" id="gen_sec" >';
                                echo '<option value="'.$row_d[11].'">'.$row_d[11].'</option>';
                                echo '<option value="SECRETARIA">SECRETARIA</option>';
                                echo '<option value="SECRETARIO">SECRETARIO</option>';
                              echo '</select>';
                              echo '<label>Genero</label>';
                            echo '</div>';
                            echo '<div class="input-field col s12">';
                              echo '<input id="responsables" type="text" name="responsables" class="validate" value="'.$row_d[12].'" required>';
                              echo '<label for="responsables">Indique Responbles del decreto(INICIALES) :</label>';
                            echo '</div>';
                            echo '<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" value="'.$row_d[7].'" style="display: none" >';
                            echo '<input type="text" id="df_sec_sub" name="df_sec_sub" class="validate" value="'.$row_d[10].'" style="display: none">';
                            echo "</br>";
                            echo "</br>";
                            echo '<div class="col s6">';
                              echo '<button id="actualizar" type="submit" class="btn trigger" name="actualizar" value="Actualizar" >Actualizar</button>';
                            echo '</div>';
                            echo '<div class="col s6">';
                              echo "<button class='btn trigger' name='invalidar' id='invalidar' type='button' onclick='Invalidar(".$id_decreto.");'>Sin Efecto</button>";
                            echo '</div>';
                          echo "</form>";
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
          if($_POST['actualizar'] == "Actualizar"){
            $query = "SELECT DOC_ID,FOLIO_DOC FROM DECRE_DETALLE WHERE DF_ID = $id_decreto LIMIT 1";
            $resultado = mysqli_query($cnn, $query);
            //echo $query;
            $row = mysqli_fetch_array($resultado);
            $doc_id = $row[0];
            $df_fec = $_POST['fec_decre'];
            $ano = date("Y");
            $df_num = $_POST['num_decre'];
            $texto_vistos = nl2br($_POST['vistos']);
            $texto_considerando = nl2br($_POST['considerando']);
            $texto_decreto = nl2br($_POST['decreto']);
            $texto_fin = nl2br($_POST['fin_decreto']);
            $usu_rut = $Srut;
            $nom_dir_salud = $_POST['director'];
            $dir_sub = $_POST['df_dir_sub'];
            $genero_dir = $_POST['gen_alc'];
            $nom_sec = $_POST['secretaria'];
            $sec_sub = $_POST['df_sec_sub'];
            $genero_sec = $_POST['gen_sec'];
            $responsables = nl2br($_POST['responsables']);
            $update = "UPDATE DECRETOS_FOR SET DF_FEC = '$df_fec',DF_NUM = $df_num,DF_ANO = '$ano',DF_TEXT_VISTOS = '$texto_vistos',DF_TEXT_CONSIDERANDO = '$texto_considerando',DF_TEXT_DECRETO = '$texto_decreto',DF_TEXT_FIN = '$texto_fin',DF_NOM_DIR = '$nom_dir_salud',DF_DIR_SUB = '$dir_sub',DF_DIR_GEN = '$genero_dir',DF_NOM_SEC = '$nom_sec',DF_SEC_SUB = '$sec_sub',DF_SEC_GEN = '$genero_sec',DF_RESPONSABLES = '$responsables' WHERE DF_ID = $id_decreto";
            mysqli_query($cnn,$update);
            echo '<script type="text/javascript"> window.open("http://200.68.34.158/personal/pdf/decreto_masivo_for.php?id='.$id_decreto.'&doc_id='.$doc_id.'" , "_blank")</script>';	
            ?> <script type="text/javascript"> window.location="historico_decretos.php";</script>  <?php
          }
        ?>
        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
    </body>
</html>