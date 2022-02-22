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
        $Sestablecimiento = utf8_encode($_SESSION['EST_ID']);
        $Sdependencia = $_SESSION['USU_DEP'];
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 30;
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
                    //rescato valores post
                    $año = $_POST['año'];
                    $query = "SELECT D.DF_ID,D.DF_NUM,DATE_FORMAT(D.DF_FEC, '%d-%m-%Y'),U.USU_NOM,U.USU_APP,U.USU_APM,D.DF_ESTA FROM DECRETOS_FOR D INNER JOIN USUARIO U ON D.USU_RUT = U.USU_RUT WHERE D.DF_ANO = '$año'";
                    $respuesta = mysqli_query($cnn,$query);
                    $num_registros = mysqli_num_rows($respuesta);
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
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('#fecha_inicio').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
                $('#fecha_fin').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });
            function Cargar(){
                $("#guardar").attr("disabled","disabled"); 
            }
            function ValidoAño(){
              $("#guardar").removeAttr("disabled");
            }
            function ImprimirPDF(num){
              var df_id = num.df_id;
              var doc_id = num.doc_id;
              window.open("http://200.68.34.158/personal/pdf/decreto_masivo_for.php?id="+df_id+"&doc_id="+doc_id, "_blank");
            }
            function Decreto(id){
              var df_id = id;
              var post = $.post("../php/buscar_doc_id.php", { "id" : df_id }, ImprimirPDF, 'json');
            }
            function ImprimirAdjunto(adj){
              var df_id = adj.df_id;
              var doc_id = adj.doc_id;
              console.log(doc_id);
              if(doc_id == 1){
                window.open("http://200.68.34.158/personal/pdf/sol_permi_masivo.php?id="+df_id , "_blank");
								window.open("http://200.68.34.158/personal/csv/csv_feriado_legal.php?id="+df_id , "_blank");
              }
              if(doc_id == 2){
                window.open("http://200.68.34.158/personal/pdf/sol_permi_masivo.php?id="+df_id , "_blank");
								window.open("http://200.68.34.158/personal/csv/csv_administrativo.php?id="+df_id , "_blank");
              }
              if(doc_id == 3){
                window.open("http://200.68.34.158/personal/pdf/sol_permi_masivo.php?id="+df_id , "_blank");
								window.open("http://200.68.34.158/personal/csv/csv_descanso_complementario.php?id="+df_id , "_blank");
              }
              if(doc_id == 4){
								var id = adj.id;
                window.open("http://200.68.34.158/personal/pdf/sin_goce.php?id="+id , "_blank");
              }
              if(doc_id == 5){
                window.open("http://200.68.34.158/personal/pdf/ot_masivo.php?id="+df_id , "_blank");
              }
              if(doc_id == 6){
                window.open("http://200.68.34.158/personal/pdf/saf_masivo.php?id="+df_id , "_blank");
              }
              if(doc_id == 8){
                window.open("http://200.68.34.158/personal/pdf/cometido_masivo.php?id="+df_id, "_blank");
              }
            }
            function Adjunto(id){
              console.log(id);
              var df_id = id;
              var post = $.post("../php/buscar_doc_id.php", { "id" : df_id }, ImprimirAdjunto, 'json');
            } 
            function Editar(id){
              var df_id = id;
              window.open("http://200.68.34.158/personal/formularios/editar_decreto.php?id="+df_id, "_self");
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
                        <h4 class="light">Decretos Masivos</h4>
                        <div class="row">
                            <form name="form" class="col s12" method="post" action="historico_decretos.php">
                                </br></br></br>
                                <label>
                                  <input name="tipo" type="radio" value="fecha" id="fecha" onclick="PorFecha();"/>
                                  <span>Filtrar por año</span>
                                </label>  
                                <label>
                                  <input name="tipo" type="radio" value="rut" id="rut" onclick="PorRut();"/>
                                  <span>Filtrar por Rut</span>
                                </label>  
                                <div class='col s4'>Favor indicar año :</div>
                                <div class="input-field col s4">
                                  <input type="text" name="año" id="año" class="validate" placeholder="" maxlength="4" onkeypress="return soloNumeros(event)" onchange="ValidoAño()" required>
                                </div> 
                                <div class="input-field col s4">
                                   <button id="guardar" type="submit" class="btn trigger" name="guardar" value="Guardar" >Buscar</button>
                                </div>
                            </form>
                        </div>
                        <div class="row">
                          <!-- MUESTRO TRABLA CON DECRETOS -->
                          <?php
                          if($num_registros >= 1){
													  echo '<table class="responsive-table boradered">';
															echo '<thead>';
																echo '<tr>';
                                  echo '<th></th>';
																	echo '<th>N° DECRETO</th>';
																	echo '<th>FECHA</th>';
																	echo '<th>TIPO</th>';
																	echo '<th>CREADOR</th>';
                                  echo '<th>ESTADO</th>';
																	echo '<th>ACCIONES</th>';
																echo '</tr>';
																echo '<tbody>';
																  while ($row = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
																	  $funcionario = $row[3]." ".$row[4]." ".$row[5];
																		$queryDOC = "SELECT DO.DOC_NOM FROM DECRE_DETALLE DD INNER JOIN DOCUMENTO DO ON DD.DOC_ID = DO.DOC_ID WHERE DD.DF_ID = $row[0] LIMIT 1";
																		$resultadoDOC = mysqli_query($cnn, $queryDOC);
																		$rowDOC = mysqli_fetch_array($resultadoDOC);
																		$doc_nom = $rowDOC[0];
																		echo '<tr>';
																			echo '<td></td>';
																			echo '<td>'.$row[1].'</td>';
																			echo '<td>'.$row[2].'</td>';
																			echo '<td>'.$doc_nom.'</td>';
																			echo '<td>'.utf8_encode($funcionario).'</td>';
                                      echo '<td>'.$row[6].'</td>';
                                      if($row[6] == "CANCELADO POR RRHH"){
                                        echo "<td><button class='btn trigger' name='decreto' id='decreto' type='button' onclick='Decreto(".$row[0].");' disabled>DECRETO</button></td>";
                                        echo "<td><button class='btn trigger' name='imprimir' id='imprimir' type='button' onclick='Adjunto(".$row[0].");' disabled>ADJUNTOS</button></td>";
                                        echo "<td><button class='btn trigger' name='editar' id='editar' type='button' onclick='Editar(".$row[0].");' disabled>EDITAR</button></td>";
                                      }else{
                                        echo "<td><button class='btn trigger' name='decreto' id='decreto' type='button' onclick='Decreto(".$row[0].");'>DECRETO</button></td>";
                                        echo "<td><button class='btn trigger' name='imprimir' id='imprimir' type='button' onclick='Adjunto(".$row[0].");'>ADJUNTOS</button></td>";
                                        echo "<td><button class='btn trigger' name='editar' id='editar' type='button' onclick='Editar(".$row[0].");' >EDITAR</button></td>";
                                      }
																		echo '</tr>';
                                   }	
																echo '</tbody>';
															echo '</thead>';
														echo '</table>';
                          }elseif($num_registros == 0){
                            
                          }
                          ?>
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

    </body>
</html>