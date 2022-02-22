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
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $ano5 = date("Y");
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $rut1 = $_GET['rut'];
        $iddcre = $_GET['id'];
        $secretaria = "GERALDINE MONTOYA MEDINA";
        $alcalde = "PABLO VILLANUEVA GALAZ";
        $responsables = "PGC";
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $buscar = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_CAR,USUARIO.USU_DEP,USUARIO.USU_ESTA,USUARIO.USU_CAT,USUARIO.USU_NIV,USUARIO.USU_PROF,ESTABLECIMIENTO.EST_NOM 
        FROM USUARIO INNER JOIN OT_EXTRA_ENC INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (USUARIO.USU_RUT = '".$rut1."')";
        $rs = mysqli_query($cnn, $buscar);
        if($row = mysqli_fetch_array($rs)){
            $MuestroRut=$row[0];
            $MuestroNombre=utf8_encode($row[1]);
            $MuestroApellidoP = utf8_encode($row[2]);
            $MuestroApellidoM = utf8_encode($row[3]);            
            $MuestroCargo = utf8_encode($row[4]);
            $MuestroDependencia = utf8_encode($row[5]);
            $MuestroEstado = $row[6]; 
            $MuestroCategoria = $row[7];
            $MuestroNivel = $row[8];
            $MuestroProf = $row[9];
            $MuestroEstablecimiento = $row[10];

        }else{
          $rut1="";
        }
        $query1 = "SELECT DF_FEC,DF_NUM,DF_TEXT_VISTOS,DF_TEXT_CONSIDERANDO,DF_TEXT_DECRETO,DF_TEXT_FIN,DF_NOM_DIR,DF_DIR_SUB,DF_DIR_GEN,DF_NOM_SEC,DF_SEC_SUB,DF_SEC_GEN,DF_RESPONSABLES FROM DECRETOS_FOR WHERE (DF_ID= ".$iddcre.")";    
        $respuesta1= mysqli_query($cnn, $query1);   
      if($row11 = mysqli_fetch_array($respuesta1)){
                $MuestroFec = $row11[0];
                $MuestroNum = $row11[1];                
                $MuestroVisto = $row11[2];
                $MuestroVisto = str_replace("<br />", " ", $MuestroVisto);
                $MuestroConsi = $row11[3];
                $MuestroConsi = str_replace("<br />", " ", $MuestroConsi);
                $MuestroDec = $row11[4];
                $MuestroDec = str_replace("<br />"," ", $MuestroDec);
                $MuestroFin = $row11[5];
                $MuestroFin = str_replace("<br />"," ", $MuestroFin);
                $alcalde = utf8_encode($row11[6]);
                $secretaria = utf8_encode($row11[9]);
                $responsables = utf8_encode($row11[12]);
                $alcsub = $row11[7];
                if($alcsub=='(S)'){
                  $alcsub='checked';
                }
                $secsub = $row11[10];
                if($secsub == '(S)'){
                  $secsub='checked';
                }
                $genalc= $row11[8];
                $gensec = $row11[11];              
                
       }else{
              $MuestroVisto = "Lo establecido en los art. 63° al 66° de la Ley N° 18.883 de 1989, sobre Estatuto Administrativo para funcionarios municipales;
              Las disposiciones establecidas por la Ley N° 18.695, Orgánica Constitucional de Municipalidades y sus modificaciones";
              $MuestroConsi = "Decreto Alcaldicio N° 320 del 06 de Octubre de 1998; que delega facultades en el Director del Departamento de Salud para autorizar ejecución de trabajos extraordinarios.
              Solcitud N° ".$iddcre." de ".$MuestroCargo." ".$MuestroEstablecimiento.", a través del cual se solcita autorización de horarios en el cual los funcionarios realizarán trabajos extraordinarios para ".$motivo;
              $MuestroDec   = "Autorizase la ejecución de trabajos extraordinarios con \"Recargo en las Remuneraciones\" y/o \"Descanso complementarios\" de los siguientes funcionarios:";
      }     
      if($MuestroVisto==""){
          $MuestroVisto = "Lo establecido en los art. 63° al 66° de la Ley N° 18.883 de 1989, sobre Estatuto Administrativo para funcionarios municipales;
              Las disposiciones establecidas por la Ley N° 18.695, Orgánica Constitucional de Municipalidades y sus modificaciones";
      }
      if($MuestroConsi ==""){
          $MuestroConsi = "Decreto Alcaldicio N° 320 del 06 de Octubre de 1998; que delega facultades en el Director del Departamento de Salud para autorizar ejecución de trabajos extraordinarios.
          Solcitud N° ".$iddcre." de ".$MuestroCargo." ".$MuestroEstablecimiento.", a través del cual se solcita autorización de horarios en el cual los funcionarios realizarán trabajos extraordinarios para ".$motivo;
      }
      if($MuestroDec=="") {
        $MuestroDec   = "Autorizase la ejecución de trabajos extraordinarios con \"Recargo en las Remuneraciones\" y/o \"Descanso complementarios\" de los siguientes funcionarios:";
      }
      if($secretaria==""){
        $secretaria = "GERALDINE MONTOYA MEDINA";
      }
      if($alcalde==""){
        $alcalde = "PABLO VILLANUEVA GALAZ";
      }
      if($responsables==""){
        $responsables = "PGC";
      }
      if($MuestroFin==""){
        $MuestroFin="ANÓTESE, TRANSCRÍBAE, COMUNÍQUESE Y ARCHÍVESE";
      }
        
      
      
        $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE USU_RUT = '$usu_rut_edit'";
        $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
        if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
            $GuardoEstablecimiento=$row_eyd[0];
            $GuardoDependencia=$row_eyd[1];
        }
        $id_formulario = 40;
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
        <script type="text/javascript" src="../../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../../include/js/materialize.js"></script>
        <script>
          
          $(document).ready(function () {
                
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'}); 
          });        
                 

           function cargarusu(id,rut1){              
                var rut1 = $("#rut_usuario").val();
                var id1 = $("#idmodifica").val();
                if (id1 >0){
                  
                  window.location = "decre_autootextra.php?id="+id1;
                }else{
                  window.location = "decre_autootextra.php?rut="+rut1;
                }
             
            }

          function Imprimir(id){
                var idDC = id;                
                window.open('http://200.68.34.158/personal/pdf/dto_ot_extra.php?id='+idDC,'_blank');
            } 
          function Editar(id,rut1){
                var idDC = id;
                //var rut = rut1;               
               $("#idmodifica").val(id);
               //$("#rut_usuario").val(rut);               
          }
          
        </script>
    </head>
    <body>
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
                        <h4 class="light">DECRETO AUTORIZACIÓN ORDEN DE TRABAJO EXTRAORDINARIOS</h4>
                        
                        <div class="row">
                        <form name="form" class="col s12" method="post">
                              <div class="input-field col s6">                                    
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" style="display:none" placeholder="" value="">                                    
                                </div>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                               <div class="input-field col s4">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s4">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="num_doc" id="num_doc" class="validate" placeholder="" value="<?php echo $MuestroNum;?>" required>
                                    <label for="hora_pun">DECRETO ALCALDICIO N°:  </label>
                                </div>
                                <div class="input-field col s4">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  <div class="input-field col s4">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                <div class="input-field col s4">
                                        <input type="text" class="datepicker" name="fecha_dc" id="fecha_dc" placeholder="" value="<?php echo $MuestroFec;?>" required>
                                      <label for="icon_prefix">Fecha Decreto</label>
                                    </div>
                               <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                             
                                      <div class="input-field col s12">
                                        <textarea name='text_visto' id="text_visto" class="materialize-textarea"><?php echo $MuestroVisto;?></textarea>
                                        <label for="text_visto">VISTOS</label>
                                      </div>
                        
                                      <div class="input-field col s12">
                                        <textarea name='text_consi' id="text_consi" class="materialize-textarea" maxlength="2500"><?php echo $MuestroConsi;?></textarea>
                                        <label for="text_consi">CONSIDERANDO</label>
                                      </div>
                           
                                      <div class="input-field col s12">
                                        <textarea name='text_decre' id="text_decre" class="materialize-textarea"><?php echo $MuestroDec;?></textarea>
                                        <label for="text_consi">DECRETO</label>
                                      </div>
                          
                                      <div class="input-field col s12">
                                        <textarea name='text_fin' id="text_fin" class="materialize-textarea"><?php echo $MuestroFin;?></textarea>
                                        <label for="text_fin">FIN TEXTO</label>
                                      </div>  
                                      
                              <br>
                            <table id="tab_carrera" class="responsive-table bordered striped">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    <th>RUT</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <!-- <th>Período</th> -->
                                    <th>Lunes-Jueves</th>
                                    <th>Horario</th>
                                    <th>Viernes</th>
                                    <th>Horario</th>
                                    <th>Sáb-Dom-Festivo</th>                                    
                                    <th>Horario</th>
                                </tr>
                                        <tbody>                                            

                                            <?php
                                            if($MuestroNum == ''){
                                              
                                             $query = "SELECT OT_EXTRA_AUT_F.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT, OT_EXTRA_AUT_F.OEA_LJ_HI, OT_EXTRA_AUT_F.OEA_LJ_HF, OT_EXTRA_AUT_F.OEA_VI_HI,OT_EXTRA_AUT_F.OEA_VI_HF, OT_EXTRA_AUT_F.OEA_SDF_HI, OT_EXTRA_AUT_F.OEA_SDF_HF,OT_EXTRA_AUT_F.OEA_LJ,OT_EXTRA_AUT_F.OEA_VI,OT_EXTRA_AUT_F.OEA_SDF FROM OT_EXTRA_ENC INNER JOIN OT_EXTRA_AUT_F ON OT_EXTRA_ENC.OEE_ID=OT_EXTRA_AUT_F.OEE_ID INNER JOIN USUARIO ON OT_EXTRA_AUT_F.USU_RUT = USUARIO.USU_RUT WHERE (OT_EXTRA_ENC.OEE_ESTA= 'AUTORIZADO DIR' AND USU_ESTA='ACTIVO')";     
                                                    
                                            }else{
                                             $query = "SELECT OT_EXTRA_AUT_F.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT, OT_EXTRA_AUT_F.OEA_LJ_HI, OT_EXTRA_AUT_F.OEA_LJ_HF, OT_EXTRA_AUT_F.OEA_VI_HI,OT_EXTRA_AUT_F.OEA_VI_HF, OT_EXTRA_AUT_F.OEA_SDF_HI, OT_EXTRA_AUT_F.OEA_SDF_HF,OT_EXTRA_AUT_F.OEA_LJ,OT_EXTRA_AUT_F.OEA_VI,OT_EXTRA_AUT_F.OEA_SDF FROM OT_EXTRA_ENC INNER JOIN OT_EXTRA_AUT_F ON OT_EXTRA_ENC.OEE_ID=OT_EXTRA_AUT_F.OEE_ID INNER JOIN USUARIO ON OT_EXTRA_AUT_F.USU_RUT = USUARIO.USU_RUT WHERE (OT_EXTRA_ENC.OEE_FOL_MUN= '$MuestroNum' AND USU_ESTA='ACTIVO')";
                                            }
                                               $respuesta = mysqli_query($cnn, $query);

                                             
                                                //recorrer los registros
                                                $cont = 0;                                                
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    if($row_rs[11]==''){
                                                      $lj="";
                                                      $ljini = "";
                                                      $ljfin = "";
                                                    }else{                                                      
                                                      $lj=$row_rs[11];
                                                      $ljini =$row_rs[5];
                                                      $ljfin=$row_rs[6];
                                                    }
                                                   
                                                   if($row_rs[12]=='V'){
                                                      $viernes= "Viernes";
                                                      $vinicio = $row_rs[7];
                                                      $vfin = $row_rs[8];
                                                    }else{
                                                      $viernes="";
                                                      $vinicio="";
                                                      $vfin="";
                                                    }
                                                  
                                                   if($row_rs[13]==''){
                                                      $sdf = "";
                                                      $sdfini = "";
                                                      $sdffin = "";
                                                    }else{
                                                      $sdf = $row_rs[13];
                                                      $sdfini = $row_rs[9];
                                                      $sdffin = $row_rs[10];
                                                    }
                                                  
                                                    echo "<tr>";
                                                        echo "<td><input type='text' id='in".$cont."' class='validate' value='".$row_rs[0]."' style='display: none'>".$row_rs[0]."</td>";
                                                        echo "<td>".utf8_encode($row_rs[1])." ".utf8_encode($row_rs[2])." ".utf8_encode($row_rs[3])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[4])."</td>";
                                                        echo "<td>".utf8_encode($lj)."</td>";
                                                        echo "<td>".$ljini."-".$ljfin."</td>";
                                                        echo "<td>".utf8_encode($viernes)."</td>";
                                                        echo "<td>".$vinicio."-".$vfin."</td>";
                                                         echo "<td>".utf8_encode($sdf)."</td>";
                                                        echo "<td>".$sdfini."-".$sdffin."</td>";                                                        
                                                    $cont = $cont + 1;
                                                }
                                            ?>
                                        </tbody>
                                    </thead>                                    
                                </table>
                                <br>
                                <br>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s6" >
                                <select name="alcalde" id="alcalde">
                                    <?php
                                        $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND (ESTABLECIMIENTO.EST_NOM = 'DEPARTAMENTO DE SALUD')";
                                        $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){
                                                $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                            }
                                            echo "<option value='no' selected disabled>Director</option>";
                                    ?>
                                </select>
                                <label for="alcalde">Director</label>
                            </div>
                                 <!--<div class="input-field col s6">															
																	<input value="<?php echo $alcalde;?>" id="alcalde" type="text" class="validate" name="alcalde" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event);">
                                   <label for="alcalde">Favor indicar Director Departamento o subrogante :</label>
                                  </div>-->
																  <div class="input-field col s4">
																			<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" <?php echo $alcsub;?>/>
      																<label for="dic_sub">Subrogante</label>
																	</div>
                                  <div class="input-field col s2">                                    
                                    <select name="gen_alc" id="gen_alc" >
                                      <!--<option value="" disabled selected></option>-->
                                      <option value="<?php echo $genalc;?>"><?php echo $genalc;?></option>
                                      <option value="DIRECTOR">DIRECTOR</option>
                                      <option value="DIRECTORA">DIRECTORA</option>           
                                    </select>
                                    <label>Genero</label>
                                  </div> 
                          				<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" style="display: none">																
																	<div class="input-field col s6">
                                	<input type="text" name="secretaria" id="secretaria" class="validate" value="<?php echo $secretaria;?>" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">
                                	<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>
                            			</div>
																	<div class="input-field col s4">
																	<input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub"  <?php echo $secsub; ?>/>
      														<label for="sec_sub">Subrogante</label>                                
																	</div>
                                  <div class="input-field col s2">                                    
                                    <select name="gen_sec" id="gen_sec" value="<?php echo $gensec;?>">
                                      <!--<option value="" disabled selected></option>-->
                                      <option value="<?php echo $gensec;?>"><?php echo $gensec;?></option>
                                      <option value="SECRETARIA">SECRETARIA</option>
                                      <option value="SECRETARIO">SECRETARIO</option>           
                                    </select>
                                    <label></label>
                                  </div>                                  
                                  <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  
																	<div class="input-field col s4">																		
																	<input id="distribucion" type="text" name="distribucion" class="validate" value="<?php echo $responsables;?>" required>
                                	<label for="distribucion">Indique Responbles del decreto(INICIALES) :</label>
                            			</div>
                                <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>                                
                                <div class="col s12">
                                    <button id="guardar" class="btn trigger" type="submit" name="guardar" value="Guardar">Decretar</button>
                                </div>
                                <div class="input-field col s12">
                                    <input style="display:none" id="idmodifica" type="text" class="validate" name="idmodifica" value="<?php echo $iddcre;?>">
                                </div>                              
                            </div>
                              <br>
                               <br>
                            <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                          <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                              <table id="tab_decretos" class="responsive-table bordered striped col s12">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Solicita</th>
                                    <th>Fecha</th>
                                    <th>N° Decreto</th>
                                                                
                                </tr>
                                        <tbody>
                                            <?php                                                
                                                 $query = "SELECT DF_ID,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,DATE_FORMAT(DF_FEC,'%Y-%m-%d'),DF_NUM FROM DECRETOS_FOR INNER JOIN USUARIO ON DECRETOS_FOR.USU_RUT = USUARIO.USU_RUT WHERE (DOC_ID = 7)  ORDER BY DF_FEC DESC";
                                                //"INSERT INTO DECRETOS_FOR (DOC_ID,USU_RUT,DF_FEC,DF_NUM,DF_FEC_INI,DF_FEC_FIN,DF_ESTA,DF_ANO,DF_TEXT_VISTOS,DF_TEXT_CONSIDERANDO,DF_TEXT_DECRETO,DF_TEXT_FIN,DF_NOM_DIR,	DF_DIR_SUB,DF_NOM_SEC,DF_SEC_SUB,DF_RESPONSABLES) VALUES (7,'$MuestroRut','$dcfec',$dcnum,'$dcfec','$dcfec','CREADO','$ano5','$dcvisto','$dcconsi','$dcdec','$texto_fin','$dire','$diresub','$secre','$secgen','$deriva')";
                                                    $respuesta = mysqli_query($cnn, $query);                                                
                                                $cont = 0;                                                
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    echo "<tr>";
                                                        echo "<td>".$row_rs[0]."</td>";
                                                        echo "<td>".utf8_encode($row_rs[1])." ".utf8_encode($row_rs[2])." ".utf8_encode($row_rs[3])."</td>";
                                                        echo "<td>".$row_rs[4]."</td>";
                                                        echo "<td><id='in".$cont."'>".$row_rs[5]."</td>";
                                                        echo '<td><button class="btn trigger" name="editar" onclick="Editar('; echo $row_rs[0].",'".$row_rs[7]."'"; echo');cargarusu();" id="editar" 
                                                            type="button">Ver</button></td>'; 
                                                        
                                                          echo '<td><button class="btn trigger" name="imprimir" onclick="Imprimir
                                                            ('; echo "'".utf8_encode($row_rs[0])."'"; echo');" id="imprimir" 
                                                            type="button">Imprimir</button></td>';
                                                                                         
                                                }                                                        
                                            ?>

                                        </tbody>
                                    </thead>                                    
                                </table>
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
        <script>
            $(document).ready(function () {
                //Animaciones
                
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
        </script>
        <?php
                    
     
            if($_POST['guardar'] == "Guardar"){
              
              $idenc = $_POST['idmodifica'];
              $dcnum = ($_POST['num_doc']);
              $dcfec = ($_POST['fecha_dc']);              
              $dcvisto = nl2br($_POST['text_visto']);
              $dcconsi = nl2br($_POST['text_consi']);
              $dcdec = nl2br($_POST['text_decre']);
              $dcfin = nl2br($_POST['text_fin']);
              $dire = $_POST['alcalde']; 
              $diresub = $_POST['dic_sub']; 
              if($diresub == "on"){
                 $diresub ="(S)";
              }
              $dirgen = $_POST['gen_alc'];
              if($dirgen == ""){
                $dirgen = "DIRECTOR";
              }
              $secre = utf8_decode($_POST['secretaria']);
              $secsub = $_POST['sec_sub'];
              if($secsub == "on"){
                 $secsub="(S)";
              }
              $secgen = $_POST['gen_sec'];
              if($secgen == ""){
                $secgen ="SECRETARIA";
              }
              $deriva = utf8_decode($_POST['distribucion']);
              if($dcnum =="" || $dcfec == ""){
                ?>
                  <script>   
                       M.toast({html: 'Documento No Generado'});                  
                  </script>                  
                   <?php
                break 1;
              
             }
              if($idenc ==''){
                $InsertInto = "INSERT INTO DECRETOS_FOR (DOC_ID,USU_RUT,DF_FEC,DF_NUM,DF_FEC_INI,DF_FEC_FIN,DF_ESTA,DF_ANO,DF_TEXT_VISTOS,DF_TEXT_CONSIDERANDO,DF_TEXT_DECRETO,DF_TEXT_FIN,DF_NOM_DIR,	DF_DIR_SUB,DF_DIR_GEN,DF_NOM_SEC,DF_SEC_SUB,DF_SEC_GEN,DF_RESPONSABLES) VALUES (7,'$Srut','$dcfec',$dcnum,'$dcfec','$dcfec','CREADO','$ano5','$dcvisto','$dcconsi','$dcdec','$dcfin','$dire','$diresub','$dirgen','$secre','$secsub','$secgen','$deriva')";
								$InsertInto;
								mysqli_query($cnn,$InsertInto);
                $actualizarauot = "UPDATE OT_EXTRA_ENC SET OEE_FEC_DEC ='$dcfec', OEE_FOL_MUN = '$dcnum', OEE_ESTA = 'DECRETADO' WHERE (OEE_ESTA= 'AUTORIZADO DIR')";
                mysqli_query($cnn, $actualizarauot);
                $docid= 7;
                $FecActual = date("Y-m-d");
                $HorActual = date("H:i:s");
                $HDAccion = ("DECRETA DOCUMENTO AUTORIZACIÓN ORDEN DE TRABAJO EXTRAORDINARIO ID: ".$idenc." ".$MuestroEstablecimiento ." ".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM);
                $guardar_historial = "INSERT INTO HISTO_DOCU (HD_FOLIO, USU_RUT, HD_FEC, HD_HORA, DOC_ID, HD_ACC) VALUES ('$idenc','$Srut','$FecActual','$HorActual','$docid', '$HDAccion')";                                                 mysqli_query($cnn, $guardar_historial);
                ?> 
                   <script type="text/javascript"> 
                   window.location="decre_autootextra.php";
                   </script>
                <?php
              }else{
                $UpDate ="UPDATE DECRETOS_FOR SET DF_FEC='$dcfec',DF_NUM='$dcnum',DF_TEXT_VISTOS='$dcvisto',DF_TEXT_CONSIDERANDO='$dcconsi',DF_TEXT_DECRETO='$dcdec',DF_TEXT_FIN='$dcfin',DF_NOM_DIR='$dire',DF_DIR_SUB='$diresub',DF_DIR_GEN='$dirgen',DF_NOM_SEC='$secre',DF_SEC_SUB='$secsub',DF_SEC_GEN='$secgen',DF_RESPONSABLES='$deriva' WHERE DF_ID ='$iddcre'";
                mysqli_query($cnn, $UpDate);
                $actualizarauot = "UPDATE OT_EXTRA_ENC SET OEE_FEC_DEC ='$dcfec', OEE_FOL_MUN = '$dcnum', OEE_ESTA = 'DECRETADO' WHERE (OEE_FOL_MUN= '$MuestroNum')";
                mysqli_query($cnn, $actualizarauot);
                $docid= 7;
                $FecActual = date("Y-m-d");
                $HorActual = date("H:i:s");
                $HDAccion = ("MODIFICA DOCUMENTO AUTORIZACIÓN ORDEN DE TRABAJO EXTRAORDINARIO ID: ".$idenc." ".$MuestroEstablecimiento ." ".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM);
                $guardar_historial = "INSERT INTO HISTO_DOCU (HD_FOLIO, USU_RUT, HD_FEC, HD_HORA, DOC_ID, HD_ACC) VALUES ('$idenc','$Srut','$FecActual','$HorActual','$docid', '$HDAccion')";                                                 mysqli_query($cnn, $guardar_historial);
                ?> 
                   <script type="text/javascript"> 
                   window.location="decre_autootextra.php";
                   </script>
                <?php
              }
            }
        ?>
            
</html>
    </body>