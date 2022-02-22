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
        header("location: ../../../index.php");
    }else{
        if(count($_GET) && !$_SERVER['HTTP_REFERER']){
           header("location: ../error.php");
        }
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $Snombre = utf8_encode($_SESSION['USU_NOM']);
        $SapellidoP = utf8_encode($_SESSION['USU_APP']);
        $SapellidoM = utf8_encode($_SESSION['USU_APM']);
        $Semail = utf8_encode($_SESSION['USU_MAIL']);
        $Sdireccion = utf8_encode($_SESSION['USU_DIR']);
        $Sfono = utf8_encode($_SESSION['USU_FONO']);
        $Scargo = utf8_encode($_SESSION['USU_CAR']);
        $Sestablecimiento = utf8_encode($_SESSION['EST_ID']);
        $Sdependencia = utf8_encode($_SESSION['USU_DEP']);
        $Scategoria = utf8_encode($_SESSION['USU_CAT']);
        $Snivel = utf8_encode($_SESSION['USU_NIV']);       
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
        $Sfecing1 = $_SESSION['USU_FEC_ING'];
        $Sfecini1 = $_SESSION['USU_FEC_INI'];

        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $ano5 = date("Y");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $usurut = $_GET['rut'];
        include ("../../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $buscar = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_MAIL,USUARIO.USU_DIR,USUARIO.USU_FONO,USUARIO.USU_CAR, 
				ESTABLECIMIENTO.EST_NOM,USUARIO.USU_DEP,USUARIO.USU_ESTA,USUARIO.USU_PAS,USUARIO.USU_CAT,USUARIO.USU_NIV,USUARIO.USU_JEF,USUARIO.USU_FEC_ING,USUARIO.USU_FEC_INI 
				FROM USUARIO INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (USUARIO.USU_RUT = '".$usurut."')";
        $rs = mysqli_query($cnn, $buscar);
        if($row = mysqli_fetch_array($rs)){
            $MuestroRut=$row[0];
            $MuestroNombre=utf8_encode($row[1]);
            $MuestroApellidoP = utf8_encode($row[2]);
            $MuestroApellidoM = utf8_encode($row[3]);
            $MuestroEmail = utf8_encode($row[4]);
            $MuestroDireccion = utf8_encode($row[5]);
            $MuestroFono = utf8_encode($row[6]);
            $MuestroCargo = utf8_encode($row[7]);
            $MuestroEstablecimiento = $row[8];
            $MuestroDependencia = utf8_encode($row[9]);
            $MuestroEstado = $row[10]; 
            $GuardoClave = $row[11]; 
            $MuestroCategoria = $row[12];
            $MuestroNivel = $row[13];
            $MuestroJefatura = $row[14];
            $MuestroFechaIngreso = $row[15];
            $MuestroFechaInicio = $row[16];
        }else{
          header("location: ../error.php");
        }           
        $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE USU_RUT = '$usu_rut_edit'";
        $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
        if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
            $GuardoEstablecimiento=$row_eyd[0];
            $GuardoDependencia=$row_eyd[1];
        }
        $id_formulario = 27;
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
                    header("location: ../../error.php");
                }
            }else{
                //si formulario no activo
                $accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
                $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                mysqli_query($cnn, $insertAcceso);
                header("location: ../../desactivada.php");
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
        <link type="text/css" rel="stylesheet" href="../../../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../../../include/css/materialize.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../../../include/css/custom.css" />
        <link href="../../../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <style type="text/css">
            body{
                background-image: url("../../../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }

        </style>
         <script type="text/javascript" src="../../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../../include/js/materialize.js"></script>
        <script>
          
          $(document).ready(function () {
                
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});      
                $("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        M.toast({html: 'Rut incorrecto'});
                        $("#btn_usuario").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                        $("#btn_usuario").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });              
                
          });           
            
            function Desactivar(ic,ec,tc,nc,fci,fcf){
                var idcb = ic;
                var rut1= $("#rut_usuario").val();
                var estacb = ec;
                var tipdocb = tc;
                var numbc = nc;
                var feini = fci;
                var fefin = fcf;
                var nombrecb = $("#rut_usuario").val() + " " + $("#nombre_usuario").val() + " " + $("#apellidoP_usuario").val() + " " + $("#apellidoM_usuario").val();
                var opcion = 2;
                var descri_cb =  estacb + " " + tipdocb + " " + numbc +" "+feini+" "+fefin;

                $.post( "../../php/carrera/actdes_carrera.php", { "usunom" : nombrecb, "id_cb" : idcb, "op_cb" : opcion, "descripcion" : descri_cb }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idDescativar + " estado enviado " + estado );
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "bienio_carrera.php?rut="+rut1;
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function Activar(ic,ec,tc,nc,fci,fcf){
                var idcb = ic;
                var rut1= $("#rut_usuario").val();
                var estacb = ec;
                var tipdocb = tc;
                var numbc = nc;
                var feini = fci;
                var fefin = fcf;
                var nombrecb = $("#rut_usuario").val() + " " + $("#nombre_usuario").val() + " " + $("#apellidoP_usuario").val() + " " + $("#apellidoM_usuario").val();
                var opcion = 1;
                var descri_cb =  estacb + " " + tipdocb + " " + numbc +" "+feini+" "+fefin;

                $.post( "../../php/carrera/actdes_carrera.php", { "usunom" : nombrecb, "id_cb" : idcb, "op_cb" : opcion, "descripcion" : descri_cb }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idDescativar + " estado enviado " + estado );
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "bienio_carrera.php?rut="+rut1;
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function Eliminar(ic,ec,tc,nc,fci,fcf){
                var idcb = ic;
                var rut1= $("#rut_usuario").val();
                var estacb = ec;
                var tipdocb = tc;
                var numbc = nc;
                var feini = fci;
                var fefin = fcf;
                var nombrecb = $("#rut_usuario").val() + " " + $("#nombre_usuario").val() + " " + $("#apellidoP_usuario").val() + " " + $("#apellidoM_usuario").val();
                var opcion = 3;
                var descri_cb =  estacb + " " + tipdocb + " " + numbc +" "+feini+" "+fefin;

                $.post( "../../php/carrera/actdes_carrera.php", { "usunom" : nombrecb, "id_cb" : idcb, "op_cb" : opcion, "descripcion" : descri_cb }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idDescativar + " estado enviado " + estado );
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "bienio_carrera.php?rut="+rut1;
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
						
						function Modificar(ic,ec,tc,nc,fci,fcf){
                $("#id_doc").val(ic);
                $("#estable_cb").val(ec);
								$("#tip_doc").val(tc);
								$("#num_doc").val(nc);
								$("#fecha_ini").val(fci);
								$("#fecha_fin").val(fcf);
							
						}
            function nivel(){
                
                   
            }
            function cargarusu(){
                var rut1 = $("#rut_usuario").val();              
                //console.log(r);
            window.location = "carrera_funcionario.php?rut="+rut1;
            }
					function ImprimirCE(){
              var idcc = "<?php echo $usurut;?>";
              window.open('http://200.68.34.158/personal/pdf/certificado_expe.php?id='+idcc,'_blank');
              //window.open('http://200.68.34.158/personal/pdf/ftb.php?id='+idcc,'_blank');
            }

        </script>
    </head>
    <body>
        <!-- llamo el nav que tengo almacenado en un archivo -->
        <?php require_once('../../estructura/nav_personal.php');?>
        <!-- inicio contenido pagina -->
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">Bienios Carrera Funcionaria</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                                <div align="right"><h6><a class="btn trigger" onclick="cargarusu()">Volver</a></h6></div>
                            </div>
                        </div>                        
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                                <div class="input-field col s2">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" style="text-transform: uppercase" disabled  placeholder="" value="<?php echo $usurut;?>">
                                    <label for="icon_prefix">RUT</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroNombre;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Nombres</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="apellidoP_usuario" id="apellidoP_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroApellidoP;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Paterno</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="apellidoM_usuario" id="apellidoM_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroApellidoM;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Materno</label>
                                </div>                               
                                
                                <div class="input-field col s4">
                                    <input type="text" name="categoria_usuario" id="categoria_usuario" value="<?php echo $MuestroCategoria;?>" disabled>
                                    <label for="categoria_usuario">Categoria</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="nivel_usuario" id="nivel_usuario" class="validate" placeholder="" value="<?php echo $MuestroNivel;?>" disabled>
                                    <label for="nivel_usuario">Nivel</label>
                                </div>
                                                             
                                <div class="input-field col s4">
                                    <input type="text" name="establecimiento_usuario" id="establecimiento_usuario" class="validate" placeholder="" value="<?php echo $MuestroEstablecimiento;?>" disabled>
                                    <label for="icon_prefix">Lugar de Trabajo</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="dependencia_usuario" id="dependencia_usuario" class="validate" placeholder="" value="<?php echo $MuestroDependencia;?>" disabled>
                                    <label for="icon_prefix">De quien depende</label>
                                </div>                                
                                <div class="input-field col s4">
                                    <input type="text" class="datepicker" name="fechaIngreso" id="fechaIngreso" value="<?php echo $MuestroFechaIngreso;?>"placeholder="Fecha Ingreso Dpto. Salud de Rengo" disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Fecha ingreso</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" class="datepicker" name="fechaInicio" id="fechaInicio" value="<?php echo $MuestroFechaInicio;?>"placeholder="Fecha Ingreso Dpto. Salud de Rengo" disabled> 
                                    <label for="icon_prefix" id="fechaInicio">Fecha Incicio Carrera</label>
                                </div>
                                

                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>                                
                                <div class="input-field col s6">
                                    <input type="text" name="estable_cb" id="estable_cb" class="validate" placeholder="DEPARTAMENTO DE SALUD I.MUNICIPALIDAD DE RENGO"  onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Establecimiento</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="tip_doc" id="tip_doc" class="validate" placeholder="" required>
                                    <label for="hora_pun">Tipo Documento</label>
                                </div>
                                  <div class="input-field col s3">
                                    <input type="text" name="num_doc" id="num_doc" class="validate" placeholder="" required>
                                    <label for="hora_pun">Número Documento</label>
                                </div> 

                                <div class="input-field col s2">
                                    <input type="text" class="datepicker" name="fecha_ini" id="fecha_ini" placeholder="" required>
                                    <label for="icon_prefix">Fecha Inicio</label>
                                </div>
                                <div class="input-field col s2">
                                    <input type="text" class="datepicker" name="fecha_fin" id="fecha_fin" placeholder="" required>
                                    <label for="icon_prefix">Fecha Fin</label>
                                </div>                                                             
                                <div class="input-field col s1">                                    
                                    <select name="estado_cb" id="estado_cb" required>
                                      <option value="" disabled selected></option>                                       
                                      <option value="1">Activo</option>
                                      <option value="2">Inactivo</option>                                      
                                    </select>
                                    <label>Estado</label>
                                </div>
                               <div class="input-field col s1">                                    
                                    <select name="indefini_cb" id="indefini_cb" required>
                                      <option value="0"></option>                                       
                                      <option value="1">SI</option>                                                                 
                                    </select>
                                    <label>Indefinido</label>
                                </div>
															<div class="input-field col s2">
																<select name="contrato" id="contrato" required>
																	<option value='no' disabled selected>Tipo Contrato</option>
																	<option value='PLAZO INDIFINIDO'>PLAZO INDEFINIDO</option>
																	<option value='PLAZO FIJO'>PLAZO FIJO</option>
																	<option value='REEMPLAZO'>REEMPLAZO</option>
																	<option value='HONORARIO'>HONORARIO</option>
																	<option value='TERMINO'>TÉRMINO</option>
																</select>
																<label for="icon_prefix">Calidad Jurídica</label>
															</div>
															<div class="input-field col s1">
																<select name="categoria_usuario" id="categoria_usuario" required>
																	<option value="no" disabled selected required>S. Cat.</option>
																	<option value="A">A</option>
																	<option value="B">B</option>
																	<option value="C">C</option>
																	<option value="D">D</option>
																	<option value="E">E</option>
																	<option value="F">F</option>
																</select>
																<label for="icon_prefix">Categoría</label>
															</div>
															<div class="input-field col s1">
																<select name="nivel_usuario" id="nivel_usuario" required>
																	<option value="no" disabled selected required>Nivel</option>
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																	<option value="4">4</option>
																	<option value="5">5</option>
																	<option value="6">6</option>
																	<option value="7">7</option>
																	<option value="8">8</option>
																	<option value="9">9</option>
																	<option value="10">10</option>
																	<option value="11">11</option>
																	<option value="12">12</option>
																	<option value="13">13</option>
																	<option value="14">14</option>
																	<option value="15">15</option>
																</select>
																<label for="icon_prefix">Nivel</label>
															</div>
															<div class="col s2">
																<button id="guardar" class="btn trigger" type="submit" name="guardar" value="Guardar">Agre/Modi</button>
															</div>
															<div class="input-field col s3">
																<input style="display:none" type="text" name="id_doc" id="id_doc" class="validate" placeholder="">   
															</div> 
															<div class="input-field col s12">
																<input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
															</div>
															<div class="input-field col s12">
																<input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
															</div>    
															<table id="tab_carrera" class="responsive-table bordered striped">
																<thead>
																	<tr>
																		<th>ID</th>
																		<th>Establecimiento</th>
																		<th>Tipo Docu.</th>
																		<th>Número Docu.</th>
																		<th>Calidad J.</th>
																		<th>Categoría</th>
																		<th>Nivel</th>
																		<th width="100">Inicio</th> 
																		<th width="100">Fin</th>
																	</tr>
																<tbody>
																	<!-- cargar la base de datos con php --> 
																	<?php 
																	$query = "SELECT CB_ID, CB_ESTABLE,CB_TIP_DOC,CB_NUM_DOC,CB_FEC_INI,CB_FEC_FIN, CB_ESTADO, CB_INDEFI, CB_CALJURI,USU_CAT, USU_NIV FROM CARRERA_BIENIO WHERE (USU_RUT = '".$usurut."')  ORDER BY CB_FEC_INI";  
																	$respuesta = mysqli_query($cnn, $query);
																		//recorrer los registros
																		$cont = 0;  
																		while ($row_rs = mysqli_fetch_array($respuesta)){
																			echo "<tr>";
																			echo "<td><id='in".$cont."'>".$row_rs[0]."</td>";
																			echo "<td><class='col s4'>".utf8_encode($row_rs[1])."</td>";
																			echo "<td><class='col s4'>".$row_rs[2]."</td>";
																			echo "<td>".$row_rs[3]."</td>";
																			echo "<td>".$row_rs[8]."</td>";
																			echo "<td>".$row_rs[9]."</td>";
																			echo "<td>".$row_rs[10]."</td>";
																			echo "<td>".$row_rs[4]."</td>";
																			echo "<td>".$row_rs[5]."</td>";
																			if($row_rs[6] == 1){ 
																				echo '<td><button class="btn trigger" name="desactivar" onclick="Desactivar('; echo "'".$row_rs[0]."','".$row_rs[1]."','".$row_rs[2]."','".$row_rs[3]."','".$row_rs[4]."','".$row_rs[5]."'"; echo');" id="desactivar" type="button">DESACTIVAR</button></td>';
																			}else{
																				echo '<td><button class="btn trigger" name="activar" onclick="Activar('; echo "'".$row_rs[0]."','".$row_rs[1]."','".$row_rs[2]."','".$row_rs[3]."','".$row_rs[4]."','".$row_rs[5]."'"; echo');" id="activar" type="button">&nbsp&nbsp&nbsp&nbspACTIVAR&nbsp&nbsp&nbsp</button></td>';
																			}        
																			echo '<td><button class="btn trigger" name="accesos" onclick="Eliminar('; echo "'".$row_rs[0]."','".$row_rs[1]."','".$row_rs[2]."',
																																'".$row_rs[3]."','".$row_rs[4]."','".$row_rs[5]."'"; echo');" id="accesos" type="button">Eliminar</button></td>';
																			echo '<td><button class="btn trigger" name="modificar" onclick="Modificar('; echo "'".$row_rs[0]."','".$row_rs[1]."','".$row_rs[2]."',
																																'".$row_rs[3]."','".$row_rs[4]."','".$row_rs[5]."'"; echo');" id="modifcar" type="button">Modificar</button></td>';
																			echo "</tr>";   
																			$cont = $cont + 1;
																		}   
																		?>
																	</tbody>
																	</thead>                           
                                </table>
                                <br>
                                <br>                                
                                <?php                                          
                                    $consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$usurut."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
                                    $resputbie = mysqli_query($cnn, $consultabie);
                                    while ($row_rs3 = mysqli_fetch_array($resputbie)){                          
                                       
                                        if($row_rs3[2] == 1){
                                            if($row_rs3[0] >= $final2){
                                                                                                                
                                                    $date1=date_create($final2);
                                                    $date2=date_create($row_rs3[0]);
                                                    $diff=date_diff($date1,$date2);
                                                    $diasno = $diasno + $diff->format('%R%a');
                                                    if($diasno <= 1){
                                                        $diasno = 0;
                                                    }

                                                   $final2= $row_rs3[1];
                                                if($final2 <= $row_rs3[1]){
                                                    $final2= $row_rs3[1];                                                        
                                                }
                                            }else{
                                                if($final2 <= $row_rs3[1]){
                                                    $final2 = $row_rs3[1];

                                                }
                                            }
                                          
                                            $date1=date_create($row_rs3[0]);
                                            $date2=date_create($fecha);
                                            $diff=date_diff($date1,$date2);
                                            $cuentabienios = $cuentabienios + $diff->format('%Y'); //$diff->format('%R%a');
                                            $final2 = $fecha;
                                            break 1;
                                        }else{
                                            
                                            if($row_rs3[0] == $inicial){
                                                $inicial2 = $row_rs3[0];
                                                $final2 = $row_rs3[1];
                                                //$date1=date_create($inicial2);
                                                //$date2=date_create($final2);
                                                //$diff=date_diff($date1,$date2);
                                                
                                            }else{
                                                if($row_rs3[0] >= $final2){
                                                                                                                
                                                        $date1=date_create($final2);
                                                        $date2=date_create($row_rs3[0]);
                                                        $diff=date_diff($date1,$date2);
                                                        $diff2=$diff->format('%R%a');
                                                        
                                                        if($diff2 ==1){
                                                          $diff2=0;                                 
                                                        }else{
                                                          $diff2=$diff2-1;
                                                        }
                                                        $diasno = $diasno + $diff2;
                                                        if($diasno <= 1){
                                                            $diasno = 0;
                                                        }
                                                        
                                                       $final2= $row_rs3[1];
                                                    if($final2 <= $row_rs3[1]){
                                                        $final2= $row_rs3[1];                                                        
                                                    }
                                                }else{
                                                    if($final2 <= $row_rs3[1]){
                                                        $final2 = $row_rs3[1];
                                                                                          
                                                    }
                                                }                                                
                                                                                            
                                            }                                        
                                            
                                        }
                                        
                             
                                    }
                                    
                                    if($final2 > $fecha){
                                        $final2 = $fecha;
                                    }
                                        

                                    if($diasno > 0){
                                        $nuevainicial = date_create($MuestroFechaInicio);
                                        date_add($nuevainicial, date_interval_create_from_date_string("$diasno days"));
                                        date_format($nuevainicial, 'Y-m-d');
																				$nuevainicial2 = date_format($nuevainicial, 'Y-m-d');
                                        $date2=date_create($final2);
                                        $interval=date_diff($nuevainicial,$date2);
                                        $cuentabienios = $interval->format('%Y');                                        
                                    }else{                                        
                                         $date1=date_create($MuestroFechaInicio);
                                         $date2=date_create($final2);                                        
                                         $interval=date_diff($date1,$date2);                                         
                                         $cuentabienios =  $interval->format('%Y'); 
                                    } 
                                    
                                    if($nuevainicial2==""){
                                        $nuevainicial2 = $MuestroFechaInicio;
                                    }
                                    while ($nuevainicial2 <= $fecha){
                                        $nuevainicial3 = date_create($nuevainicial2);
                                        date_add($nuevainicial3, date_interval_create_from_date_string('2 years'));
                                        date_format($nuevainicial3, 'Y-m-d');
                                        $nuevainicial2 =  date_format($nuevainicial3, 'Y-m-d');
                                    }  																	
                                    //GUARDA FECHA DE CUMPLIMIENTO BIENIO
                                    //$actualizarUsuario = "UPDATE USUARIO SET USU_FEC_BIE = '".$nuevainicial2."' WHERE (USU_RUT = '".$usurut."')";
                                    //mysqli_query($cnn, $actualizarUsuario);
                                    //FIN
                                    
                                    if($cuentabienios%2==0){ // se multiplica o restar para dejar como valor entro para la búsqueda en la Tabla CARRERA_BIENIO_PTOS
                                        $valido_bie = $cuentabienios * 1;
                                    }else{
                                        $valido_bie= $cuentabienios - 1;
                                    }
                                    //echo "Dividido  ". $cuentabienios%2; 
                                    //Cuando la división da uno, deja en años como válido para carrera
                                    if($cuentabienios==1){
                                       $cuentabienios=2;
                                       $valido_bie=2;
                                    }
                                   
                                    $buscar_bie = "SELECT CBP_PTOS FROM CARRERA_BIENIO_PTOS WHERE CBP_ANOS = '$valido_bie'";
                                    $rs_buscar_bie = mysqli_query($cnn, $buscar_bie);
                                    if($row_bie = mysqli_fetch_array($rs_buscar_bie)){
                                        $bienios_ptos=$row_bie[0];                         
                                    }
                                    if($valido_bie>= 30){
                                      $bienios_ptos= 8000;
                                    }
                          
                                    $total_puntaje = $bienios_ptos + $acumu_pun;
                                    round($total_puntaje, 2);
                                    
                                    if($Scategoria == "A" || $Scategoria == "B"){
                                        $buscar_criti ="SELECT CPC_AB_INI,CPC_AB_FIN,CPC_NIVEL FROM CARRERA_PTOS_CRITI";
                                        $resputcriti = mysqli_query($cnn, $buscar_criti);
                                        while ($row_rs4 = mysqli_fetch_array($resputcriti)){
                                        //$total_puntaje = 4167.75;
                                            if($row_rs4[0] <= $total_puntaje){
                                                if($total_puntaje <= $row_rs4[1]){                                                   
                                                    $nivel_actual = $row_rs4[2];
                                                    break 1;
                                                }
                                              
                                            }                                
                                        }                                    
                                    }else{
                                        $buscar_criti ="SELECT CPC_CF_INI,CPC_CF_FIN,CPC_NIVEL FROM CARRERA_PTOS_CRITI";
                                        $resputcriti = mysqli_query($cnn, $buscar_criti);
                                        while ($row_rs4 = mysqli_fetch_array($resputcriti)){
                                        //$total_puntaje = 4167.75;
                                            if($row_rs4[0] <= $total_puntaje){
                                                if($total_puntaje <= $row_rs4[1]){                                                   
                                                    $nivel_actual = $row_rs4[2];
                                                    break 1;
                                                }
                                              
                                            }                                
                                        }
                                    }
                                ?>

                                <div class="input-field col s3">
                                    <input type="text" align='right' name="bienios_cf" id="bienios_cf" value="<?php echo $cuentabienios;?>" placeholder="Años en Salud Publica"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Años Salud Pública</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" align='right' name="validobie" id="validosact" value="<?php echo $valido_bie;?>" placeholder="Años Válidos Salud Pública"  disabled> 
                                    <label for="icon_prefix" id="validobie">Años Válidos Salud Pública</label>
                                </div>                             
                                <div class="input-field col s3">
                                    <input type="text" align='right' name="ptos_bie" id="pto_bie" value="<?php echo $bienios_ptos;?>" placeholder="Total Puntaje Bienios"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Total Puntos Bienios</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" align='right' name="dias_no" id="dias_no" value="<?php echo $diasno;?>" placeholder="Días no Trabajados Salud Pública"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Días no Trabajados Salud Pública</label>
                                </div> 
													<div class="input-field col s3">
														<button class='btn trigger' type='button' onclick='ImprimirCE();'>Certificado Antigüedad</button>
													</div> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- fin contenido pagina -->        
        <!-- Cargamos jQuery y materialize js -->
        <script type="text/javascript" src="../../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../../include/js/materialize.js"></script>
        
<?php
if($_POST['guardar'] == "Guardar"){
	//btn actualizar
	//echo $nuevo_rut = utf8_decode($_POST['rut_usuario']);
	$idcb = $_POST['id_doc'];
	$fecini = ($_POST['fecha_ini']);
	$fecfin = ($_POST['fecha_fin']);
	$estable = ($_POST['estable_cb']);
	$estable = str_replace("'", '', $estable);
	if ($estable==""){
		$estable= "DEPARTAMENTO DE SALUD I.MUNICIPALIDAD DE RENGO";
	}
	$tipdoc = $_POST['tip_doc'];
	$numdoc = utf8_decode($_POST['num_doc']);
	$estdoc = utf8_decode($_POST['estado_cb']);
	$indefi = utf8_decode($_POST['indefini_cb']);
	$caljuri = utf8_decode($_POST['contrato']);
	$catego = $_POST['categoria_usuario'];
	$nivel_nombramiento = $_POST['nivel_usuario'];

	//valido si reset la clave de usuario
	//$consulta1 = "SELECT * FROM CARRERA_BIENIO WHERE (USU_RUT='".$usurut."') AND (CB_FEC_INI='".$fecini."') AND (CB_FEC_FIN='".$fecfin."') AND (CB_ESTABLE ='".$estable."') 
	//AND (CB_TIP_DOC ='".$tipdoc."') AND (CB_NUM_DOC='".$numdoc."') AND (CB_ESTADO='".$estdoc."') AND (CB_INDEFI='".$indefi."')";
	 $consulta1 = "SELECT * FROM CARRERA_BIENIO WHERE (USU_RUT='".$usurut."') AND (CB_ID='".$idcb."')";
	$rsconsu = mysqli_query($cnn, $consulta1);
	if (mysqli_num_rows($rsconsu) != 0){
	 $actualiza ="UPDATE CARRERA_BIENIO SET CB_FEC_INI ='".$fecini."', CB_FEC_FIN='".$fecfin."', CB_ESTABLE ='".$estable."',CB_TIP_DOC ='".$tipdoc."',CB_NUM_DOC='".$numdoc."',CB_ESTADO='".$estdoc."'
	,CB_INDEFI='".$indefi."', CB_CALJURI='".$caljuri."', USU_CAT='".$catego."', USU_NIV='".$nivel_nombramiento."' WHERE (CB_ID ='".$idcb."')";
	 mysqli_query($cnn, $actualiza);
	$accionRealizada ="ACTUALIZA";

	}else{
			$insertareg = "INSERT INTO CARRERA_BIENIO (USU_RUT,CB_FEC_INI,CB_FEC_FIN,CB_ESTABLE,CB_TIP_DOC,CB_NUM_DOC,CB_ESTADO,CB_INDEFI,CB_CALJURI,USU_CAT,USU_NIV) VALUES('$usurut','$fecini','$fecfin','$estable','$tipdoc','$numdoc','$estdoc','$indefi','$caljuri','$catego','$nivel_nombramiento')";
			mysqli_query($cnn, $insertareg);
	$accionRealizada ="AGREGA";
	}
	//SOLICITADO POR MARCELA PARA INGRESO DE NUEVO NIVEL LEY DE ALIVIO.
    //SE AGREGA CALIDAD JURIDICA EL 24-01-2022 NO SOLICITADO ANTES.
	$actualiza_nivel ="UPDATE USUARIO SET USU_NIV='".$nivel_nombramiento."', USU_CONTRA='".$caljuri."' WHERE (USU_RUT='".$usurut."')";
	mysqli_query($cnn, $actualiza_nivel);
//##########################################
	$insertAccion = "INSERT INTO CARRERA_LOG_ACCION (CA_LA_ACC, FOR_ID, USU_RUT, CA_LA_IP_USU, CA_LA_FEC, CA_LA_HORA) VALUES ('$accionRealizada', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
	mysqli_query($cnn, $insertAccion);
	?> <script type="text/javascript"> 
			var rut1 = $("#rut_usuario").val();
			window.location="bienio_carrera.php?rut="+rut1;
			</script>
	<?php
}
?>
    </body>
</html>