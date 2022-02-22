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
        $id_formulario = 14;
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
                    //reviso si tengo algun cometido en creacion
                    $consultaEncreacion = "SELECT CO_ID FROM COME_PERMI WHERE (USU_RUT = '$Srut') AND (CO_ESTA = 'EN CREACION')";
                    $respuestaEnCreacion = mysqli_query($cnn, $consultaEncreacion);
                    if (mysqli_num_rows($respuestaEnCreacion) == 0){
                        //usuario no tiene ningun folio tomado
                        $consultaNuevoId = "SELECT CO_ID FROM COME_PERMI ORDER BY CO_ID DESC";
                        $respuestaNuevoId = mysqli_query($cnn, $consultaNuevoId);
                        $AñoActual = date("Y");
                        if (mysqli_num_rows($respuestaNuevoId) == 0){
                            $NuevoID = 1;
                            $FolioUno = "INSERT INTO COME_PERMI (CO_ID,DOC_ID,USU_RUT,CO_ESTA,CO_ANO,CO_FEC,CO_DECRE) VALUES ($NuevoID, 8, '$Srut', 'EN CREACION','$AñoActual','$fecha','NO')";
                            $GuardarHistoPermiso = "INSERT INTO HISTO_PERMISO (HP_FOLIO,USU_RUT,HP_FEC,HP_HORA,DOC_ID,HP_ACC) VALUES ($NuevoID,'$Srut','$fecha','$hora',8,'CREA COMETIDO FUNCIONARIO')";
                            mysqli_query($cnn, $GuardarHistoPermiso);
                            mysqli_query($cnn, $FolioUno);
                        }else{
                            $rowNuevoId = mysqli_fetch_row($respuestaNuevoId);
                            $UltimoID = $rowNuevoId[0];
                            $NuevoID = $UltimoID + 1;
                            $FolioNuevo = "INSERT INTO COME_PERMI (CO_ID,DOC_ID,USU_RUT,CO_ESTA,CO_ANO,CO_FEC,CO_DECRE) VALUES ($NuevoID, 8, '$Srut', 'EN CREACION','$AñoActual','$fecha','NO')";
                            $GuardarHistoPermiso = "INSERT INTO HISTO_PERMISO (HP_FOLIO,USU_RUT,HP_FEC,HP_HORA,DOC_ID,HP_ACC) VALUES ($NuevoID,'$Srut','$fecha','$hora',8,'CREA COMETIDO FUNCIONARIO')";
                            mysqli_query($cnn, $GuardarHistoPermiso);
                            mysqli_query($cnn, $FolioNuevo);
                        }
                    }else{
                        $rowFolioUsado = mysqli_fetch_row($respuestaEnCreacion);
                        $NuevoID = $rowFolioUsado[0];
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
        <script type="text/javascript" src="../../include/js/moment.js"></script>
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
                $('#porce').formSelect('destroy');
                /*$("#Finicio").attr("disabled","disabled");
                $("#Ftermino").attr("disabled","disabled");
                $("#horas").attr("disabled","disabled");
                $("#fecha_hora").attr("disabled","disabled"); 
                $("#cant_horas").attr("disabled","disabled");                 
                $("#mediodia").attr("disabled","disabled");
                $("#fecha_dia").attr("disabled","disabled");
                $("#fecha_dia").attr("disabled","disabled");                
                $("#undia").attr("disabled","disabled");
                $("#masdeuno").attr("disabled","disabled");
                $("#dias").attr("disabled","disabled");
                $("#motivo").attr("disabled","disabled");*/
                //$("#jefatura").attr("disabled","disabled");
                //$("#guardar").attr("disabled","disabled");
                //$('select').material_select('destroy');
                //$("#hora_ini").attr("disabled","disabled");
                //$("#hora_fin").attr("disabled","disabled");
                //$("#porce").attr("disabled","disabled");
            }
						function Habilitar(){
							if( $('#viatico').prop('checked') ) {
									$('#porce').formSelect();
							}else{
									$('#porce').formSelect('destroy');
							}
						}
            function soloNumeros(e){
                var key = window.Event ? e.which : e.keyCode;
                return (key >= 48 && key <= 57 || key == 127 || key == 08);
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
            function validahora(){
                var hora_ini = $("#hora_ini").val();
                var hora_fin = $("#hora_fin").val();

                if (hora_fin < hora_ini){
                    $("#hora_ini").val("");
                    $("#hora_fin").val("");
                    M.toast({html: 'Horas no válidas'});  
                }
            }
            function ValidarFechaMedioDia(){
								$("#hora_ini").removeAttr("disabled");
            } 
						function Respuesta(g){
							var respuesta = g;
							if(respuesta == "OK"){
                window.location = "for_cometido.php";
							}else if(respuesta == "INICIO"){
                M.toast({html: 'La hora de inicio ya existe para esta fecha'});  
								$("#hora_ini").val("");
								$("#hora_fin").val("");
							}else if(respuesta == "FIN"){
                M.toast({html: 'La hora de termino ya existe para esta fecha'});
								$("#hora_fin").val("");									 
							}else if(respuesta == "INICIO-FIN"){
                M.toast({html: 'El rango de horas ingresado ya existe'});
								$("#hora_ini").val("");
								$("#hora_fin").val("");
							}
						}
            function Agregar(){
                var oe_id = $("#folio").val();
                var dia = $("#fecha_dia").val();
                var hora_ini = $("#hora_ini").val();
                var hora_fin = $("#hora_fin").val();
                var porce1 = $("#porce").val();
                if(dia != "" && hora_ini != "" && hora_fin != "" && porce1 != ""){
                  $.post( "../php/nuevo_detalle_come_version2.php", { "id" : oe_id, "dia" : dia, "hora_ini" : hora_ini, "hora_fin" : hora_fin, "porce1" : porce1}, Respuesta, "json" );
                }else{
                  M.toast({html: 'Datos incorrectos'});
                } 
            }
            function Cancelar(cont){
                var contador = cont;
                var oe_id = $("#folio").val();
                var id_dia = "#DIA"+contador;
                var id_hi = "#HORA_INI"+contador;
                var id_hf = "#HORA_FIN"+contador;
                var dia = $(id_dia).val();
                var hora_ini = $(id_hi).val();
                var hora_fin = $(id_hf).val();
                var porce1 = $("#porce").val();
                console.log(oe_id+" "+dia+" "+hora_ini+" "+hora_fin);
                $.post( "../php/borrar_detalle_come.php", { "id" : oe_id, "dia" : dia, "hora_ini" : hora_ini, "hora_fin" : hora_fin, "porce1" : porce1}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id " + id_nuevo + " nombre formulario " + nombre_nuevo + " estado " + estado_nuevo);
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "for_cometido.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        //console.log( "La solicitud a fallado: " +  textStatus);
                        window.location = "for_cometido.php";
                    }
                });
            }
            function Listo(){
                var motivo1 = $("#motivo").val();
                var destino1 = $("#destino").val();
                var contadia1 = $("#contadiajs").val();
                if(motivo1 != "" && destino1 !="" && contadia1 > 0){
                    $("#guardar").removeAttr("disabled");
                }else{
                  M.toast({html: 'Ingrese todos los datos'});
                }
                
            }
            function Jefatura(){
              $('#jefatura').formSelect();
            }
            function CargaArc(){
              var oe_id = $("#folio").val();
              var iddoc = 8;              
              //window.open('../php/carga_convo.php?id1=oe_id&id2=iddoc','_blank');
              window.open('../php/carga_convo.php'+"?id1="+escape(document.getElementById("folio").value)+"&id2="+8, "Subir Archivo" , "width=650,height=450,scrollbars=yes,menubar=yes,toolbar=yes,location=no");
              //window.open(this.href+"?combo=" + escape(document.getElementById("idCombo").value), this.target, "width=400,height=300")              
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
                        <h4 class="light">Cometido Funcionario</h4>
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
                            <div class="input-field col s2">
                                <input type="text" name="folio" id="folio" class="validate" placeholder="" value="<?php echo $NuevoID;?>" disabled>
                                <label for="folio">Folio</label>
                            </div>                            
                            <div class="col s2">
                                <label>
                                  <input type="checkbox" name="viatico" id="viatico" onchange="Habilitar();"/>
                                  <span>Viático</span>
                                </label>
                            </div>
                            <div class="col s2">
                                <label>
                                  <input type="checkbox" name="pasaje" id="pasaje"/>
                                  <span>Pasaje</span>
                                </label>
                            </div>
                            <div class="col s2">
                                <label>
                                  <input type="checkbox" name="combustible" id="combustible"/>
                                  <span>Combustible</span>
                                </label>
                            </div>
                            <div class="col s2">
                                <label>
                                  <input type="checkbox" name="peaje" id="peaje"/>
                                  <span>Peaje</span>
                                </label>
                            </div>
                            <div class="col s2">
                                <label>
                                  <input type="checkbox" name="parquimetro" id="parquimetro"/>
                                  <span>Parquimetro</span>
                                </label>
                            </div>
                            <table class="responsive-table boradered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>DIA</th>
                                        <th>HORA INICIO</th>
                                        <th>HORA TERMINO</th>
                                        <th>PORCENTAJE</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                    <tbody>
                                        <?php
                                            $Detalle_cometido = "SELECT CO_ID,DATE_FORMAT(CD_DIA,'%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID = $NuevoID) ORDER BY CD_DIA ASC";
                                            $respuesta = mysqli_query($cnn, $Detalle_cometido);
                                            //recorrer los registros
                                            $contador = 1;
                                            $contadia = 0;
                                            while ($row_rs = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
                                                echo "<tr>";
                                                    echo '<td></td>';
                                                    echo '<td><input type="text" id="DIA'.$contador.'" class="validate" placeholder="" value="'.$row_rs[1].'" style="display: none">'.$row_rs[1].'</td>';
                                                    //echo "<td>".$row_rs[1]."</td>";// id="DIA'.$contador.'";
                                                    echo '<td><input type="text" id="HORA_INI'.$contador.'" class="validate" placeholder="" value="'.$row_rs[2].'" style="display: none">'.$row_rs[2].'</td>';
                                                    //echo "<td id='HORA_INI".$contador."'>".$row_rs[2]."</td>";
                                                    echo '<td><input type="text" id="HORA_FIN'.$contador.'" class="validate" placeholder="" value="'.$row_rs[3].'" style="display: none">'.$row_rs[3].'</td>';
                                                    //echo "<td id='HORA_FIN".$contador."'>".$row_rs[3]."</td>";
                                                    echo '<td><input type="text" id="porce'.$contador.'" class="validate" placeholder="" value="'.$row_rs[5].'" style="display: none">'.$row_rs[4].'</td>';
                                                    //echo "<td><a class='waves-effect waves-light btn' href='#MIPERMISO".$row_rs[0]."'>Detalle</a></td>";
                                                    echo "<td><button class='btn trigger' name='cancelar' id='cancelar' type='button' onclick='Cancelar(".$contador.");'>Cancelar</button></td>";
                                                echo "</tr>";
                                                $contador = $contador + 1;
                                                $contadia = $contador - 1;
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="input-field col s1">
                                                    <input name="contadiajs" id="contadiajs" value="<?php echo $contadia;?>" style="display: none"> 
                                                </div> 
                                            </td>
                                            <td>
                                                <div class="input-field col s12">
                                                    <input type="text" name="fecha_dia" id="fecha_dia" class="datepicker" placeholder="Dia" onchange="ValidarFechaMedioDia();" > 
                                                </div> 
                                            </td>
                                            <td>
                                                <div class="input-field col s12">
                                                    <input id="hora_ini" name="hora_ini" class="timepicker" type="text" placeholder="Hora Inicio">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-field col s12">
                                                    <input id="hora_fin" name="hora_fin" class="timepicker" type="text" placeholder="Hora Termino" onchange="validahora();">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-field col s12">
                                                <select id="porce" name="porce">
                                                    <option value="" disabled selected></option>
                                                    <option value="40%">40%</option>
                                                    <option value="100%">100%</option>
                                                </select>
                                                <label>Porcentaje</label>
                                                </div>
                                            </td>
                                            <td><button class="btn trigger" name="agregar" id="agregar" type="button" onclick="Agregar();">Agregar</button></td>
                                        </tr>
                                    </tbody>
                                </thead>
                            </table>
                            </br>
                            </br>
                          <div class=s12>
                              <td><button class="btn trigger" name="carga_arc" id="carga_arc" type="button" onclick="CargaArc();">Adjuntar Convocatoria</button></td>
                          </div>
                          <div class="right col s12 m8 l8 block">
                                <div align="right"><a href="../php/carga_convo.php?id1=<?php echo $NuevoID;?>&id2=<?php echo "8";?>" target="_blank width='400' height='300'" class="btn trigger">Volver</a></div>
                            </div>
                            <div class="input-field col s12">
                            <!-- <input type="text" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"> -->
                                <input type="text" name="motivo" id="motivo" class="validate" placeholder="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)" required>
                                <label for="icon_prefix">Motivo</label>
                            </div>
                            <div class="input-field col s12">
                            <!-- <input type="text" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"> -->
                                <input type="text" name="destino" id="destino" class="validate" placeholder="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)" required>
                                <label for="icon_prefix">Destino</label>
                            </div>
                            <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura">
                                    <?php
                                        $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND ((ESTABLECIMIENTO.EST_NOM = '$Sdependencia') OR (ESTABLECIMIENTO.EST_NOM = 'MULTIESTABLECIMIENTO'))";
                                        $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){
                                                $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                            }
                                            echo "<option value='no' selected disabled>Jefe Directo</option>";
                                    ?>
                                </select>
                                <label for="jefatura">Jefe Directo</label>
                            </div>
                            </br>
                            </br>  
                            </br>
                            </br>  
                            <div class="col s12">
                                <button id="guardar" type="submit" class="btn trigger" name="guardar" value="Guardar">Enviar</button>
                            </div>
                            <?php
                                if($_POST['guardar'] == "Guardar"){
                                    //primero rescato todos los datos del formulario
                                    $doc_id = 8;
                                    $usu_rut = $Srut;
                                    $usu_rut_jd = $_POST['jefatura'];
                                    $viatico = $_POST['viatico'];
                                    $dia = $_POST['contadiajs'];
                                    $pasaje = $_POST['pasaje'];
                                    $combu = $_POST['combustible'];
                                    $peaje = $_POST['peaje'];
                                    $parqui = $_POST['parquimetro'];
                                    $motivo = utf8_decode($_POST['motivo']);
                                    $destino = utf8_decode($_POST['destino']);
                                    $estado = 'SOLICITADO';
                                    $AnoActual = date("Y");
                                    
                                    $FechaActual = date("Y-m-d");
                                    $HoraActual = date("H:i:s");
                                    
                                    if (($usu_rut_jd != "") && ($motivo != "") && ($destino !="") && ($dia != 0)) {
																				if($Sdependencia == "ILUSTRE MUNICIPALIDAD DE RENGO"){
																					$guardar_come = "UPDATE COME_PERMI SET USU_RUT_JD = '$usu_rut_jd',CO_VIA = '$viatico',CO_DIA = '$dia',CO_PAS ='$pasaje',CO_COM ='$combu',CO_PEA ='$peaje', CO_PAR = '$parqui', CO_MOT = '$motivo',CO_DES ='$destino',CO_ESTA = 'AUTORIZADO DIR',CO_ANO = '$AnoActual', CO_FEC = '$FechaActual' WHERE (CO_ID = $NuevoID)";
																				}else{
																					$guardar_come = "UPDATE COME_PERMI SET USU_RUT_JD = '$usu_rut_jd',CO_VIA = '$viatico',CO_DIA = '$dia',CO_PAS ='$pasaje',CO_COM ='$combu',CO_PEA ='$peaje', CO_PAR = '$parqui', CO_MOT = '$motivo',CO_DES ='$destino',CO_ESTA = '$estado',CO_ANO = '$AnoActual', CO_FEC = '$FechaActual' WHERE (CO_ID = $NuevoID)";
																				}
                                        $GuaHistoPermiso = "INSERT INTO HISTO_PERMISO (HP_FOLIO,USU_RUT,HP_FEC,HP_HORA,DOC_ID,HP_ACC) VALUES ($NuevoID,'$Srut','$FecActual','$HorActual',14,'ENVIA COMETIDO FUNCIONARIO')";
                                        mysqli_query($cnn, $guardar_come);
                                        mysqli_query($cnn, $GuaHistoPermiso);
                                        ?> <script type="text/javascript"> window.location="../index.php";</script>-->  <?php 
                                    }else{
                                        ?> <script type="text/javascript">M.toas({html: 'Ingrese todos los datos'});</script><?php
                                    }
                                    
                                           
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
    </body>
</html>