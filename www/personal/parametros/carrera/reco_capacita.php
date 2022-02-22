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
        $alcalde = "CARLOS SOTO GONZALEZ";
        $responsables = "FLA/PVG/PGC/MPP/";
        include ("../../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $buscar = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_CAR,USUARIO.USU_DEP,USUARIO.USU_ESTA,USUARIO.USU_CAT,USUARIO.USU_NIV,USUARIO.USU_PROF 
        FROM USUARIO INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (USUARIO.USU_RUT = '".$rut1."')";
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

        }else{
          $rut1="";
        }
         $busca_deta = "SELECT DA_ID,DA_DC_NUM,DATE_FORMAT(DA_FEC,'%Y-%m-%d'),DA_VISTO,DA_CONSI,DA_DEC,DA_ALCALDE,DA_SECRE,DA_DERIVA,DA_ALCSUB,DA_SECSUB,DA_GENALC,DA_GENSEC FROM DECRE_ACT WHERE (DA_ID = '".$iddcre."')";    
         $respuesta = mysqli_query($cnn, $busca_deta);
            if($row1 = mysqli_fetch_array($respuesta)){
                $MuestroNum   = $row1[1];
                $MuestroFec   = $row1[2];
                $MuestroVisto = $row1[3];
                $MuestroVisto = str_replace("<br />", " ", $MuestroVisto);
                $MuestroConsi = $row1[4];
                $MuestroConsi = str_replace("<br />", " ", $MuestroConsi);
                $MuestroDec   = $row1[5];
                $MuestroDec = str_replace("<br />"," ", $MuestroDec);
                $alcalde  = utf8_encode($row1[6]);
                $secretaria = utf8_encode($row1[7]);
                $responsables  = utf8_encode($row1[8]);
                $alcsub= $row1[9];
                $secsub = $row1[10];
                if($alcsub=='(S)'){
                   $alcsub='checked';
                                 }
                if($secsub =='(S)'){
                    $secsub='checked';                             
                }
                $genalc = $row1[11];
                if($genalc ==""){
                  $genalc="ALCALDE";
                }
                $gensec = $row1[12];
                if($gensec==""){
                  $gensec="SECRETARIA";
                }
            }else{
              $MuestroVisto = "Lo Dispuesto en el artículo N°42 de la ley N°19.378 de 1995; los artículos 37 y siguientes del Reglamento de Carrera Funcionaria del Personal regido por el Estatuto Administrativo regido por el Estatuto de Atención Primaria de Salud Municipal.   La resolución N° 1600 de 2008, de la Contraloría General de la República; y, en uso de las facultades que me confiere la Ley N° 18.695, Orgánica Constitucional de Municipalidades, cuyo texto  fue fijado por el Decreto Supremo N° 662, del 16 de Junio de 1992, publicado en el Diario Oficial.";
              $MuestroConsi = "Capacitaciones realizadas por el personal de Salud Municipal, insertas en el programa de Capacitación Comunal";
              $MuestroDec   = "Reconózcase los cursos de capacitación efectuados por el o la funcionario(a) ".$MuestroNombre." ".$MuestroApellidoP." ".$MuestroApellidoM."  Rut N°: ".$MuestroRut."  Categoría ".$MuestroCategoria.", ".$MuestroProf.", ".$MuestroDependencia." y asígnese los siguientes puntajes:";
            }
      
      
        $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE USU_RUT = '$usu_rut_edit'";
        $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
        if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
            $GuardoEstablecimiento=$row_eyd[0];
            $GuardoDependencia=$row_eyd[1];
        }
        $id_formulario = 34;
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
                //Animaciones
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
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
             
           function cargarusu(id){
                var rut1 = $("#rut_usuario").val();
                var id1 = $("#idmodifica").val();
                if (id1 >0){
                  var rut1 = "<?php echo $rut1;?>";
                  window.location = "reco_capacita.php?rut="+rut1+"&id="+id1;
                }else{
                  window.location = "reco_capacita.php?rut="+rut1;
                }
                
            }
             
            function Respuesta(r){
                if(r.resultado == 1){                    
                    //$("#num_doc").val(""); 
                    //$("#fecha_dc").val("");
                    M.toast({html: 'Número de Decreto Utilizado'}); 
                }
            }

            function buscanum(){
                var nd = $("#num_doc").val();
                var year = $("#fecha_dc").val();                
                var ano = year.substring(0,4);
                var post = $.post("../../php/carrera/buscar_num.php", { "num_dec" : nd, "ano" : ano }, Respuesta, 'json');    
            }
          


            function decretar(){
              
              var suma = "<?php echo $suma;?>";
              var dcnum = $("#num_doc").val();
              var usurut = "<?php echo $rut1;?>";
              var dcfec = $("#fecha_dc").val();
              var dcvisto = $("#text_visto").val();
              var dcconsi = $("#text_consi").val();
              var dcdec = $("#text_decre").val();
              var nomusu = "<?php echo $MuestroNombre;?>";
              var appusu = "<?php echo $MuestroApellidoP;?>";
              var apmusu = "<?php echo $MuestroApellidoM;?>";
              
              var alcalde = $("#alcalde").val();              
              var alcaldesub = document.getElementById('dic_sub').checked;
              if(alcaldesub ==true){
                alcaldesub="(S)";
              }else{
                alcaldesub="";
              }
              var genalc = $("#gen_alc").val();
              if(genalc == ""){
                genalc = "ALCALDE";   
              }
              
              
              var secretaria = $("#secretaria").val();
              var secretariasub = document.getElementById('sec_sub').checked;
              if(secretariasub == true){
                secretariasub ="(S)";
              }else{
                secretariasub="";
              }
              var gensec = $("#gen_sec").val();
              if(gensec == ""){
                 gensec = "SECRETARIA";
              }
              
              var distribucion =$("#distribucion").val(); 
              var idmod = $("#idmodifica").val();
              
              if (idmod ==""){
                if(dcnum != "" && dcfec != "" && alcalde != "" && secretaria != "" && distribucion != ""){           

                 $.post( "../../php/carrera/decre_act.php", { "dcnum2" : dcnum, "usurut2" : usurut, "dcfec2" : dcfec, "dcvisto2" : dcvisto, "dcconsi2" : dcconsi, "dcdec2" : dcdec, "nomusu2" : nomusu, "appusu2" : appusu,
                                                                "apmusu2" : apmusu, "alcalde2" : alcalde, "secretaria2" : secretaria, "distribucion2" : distribucion, "alcaldesub2" : alcaldesub, "secretariasub2" : secretariasub, "genalcalde2" : genalc, "gensecre2" : gensec}, null, "json")  

                   .done(function( data, textStatus, jqXHR ) {
                   if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                         window.location = "reco_capacita.php?rut="+usurut;
                       }
                   })
                   .fail(function( jqXHR, textStatus, errorThrown ) {
                              if ( console && console.log ) {
                                  console.log( "La solicitud a fallado: " +  textStatus);
                                 window.location = "reco_capacita.php?rut="+usurut;
                              }

                    });
                 }else{
                    M.toast({html: 'Datos no válidos'});

                 } 
              }else{

              if(dcnum != "" && dcfec != "" && alcalde != "" && secretaria != "" && distribucion != ""){           

                 $.post( "../../php/carrera/modifica_decre_act.php", { "dcnum2" : dcnum, "usurut2" : usurut, "dcfec2" : dcfec, "dcvisto2" : dcvisto, "dcconsi2" : dcconsi, "dcdec2" : dcdec, "nomusu2" : nomusu, "appusu2" :                                                              appusu, "apmusu2" : apmusu, "alcalde2" : alcalde, "secretaria2" : secretaria, "distribucion2" : distribucion, "id2" : idmod, "alcaldesub2" : alcaldesub, 
                                                             "secretariasub2" : secretariasub, "genalcalde2" : genalc, "gensecre2" : gensec}, null, "json" )  

                   .done(function( data, textStatus, jqXHR ) {
                   if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                         window.location = "reco_capacita.php?rut="+usurut;
                       }
                   })
                   .fail(function( jqXHR, textStatus, errorThrown ) {
                              if ( console && console.log ) {
                                  console.log( "La solicitud a fallado: " +  textStatus);
                                 window.location = "reco_capacita.php?rut="+usurut;
                              }

                    });
                 }else{
                    M.toast({html: 'Datos no válidos'});

                 }
              }
            }
          function Imprimir(id1){
                var idDC1 = id1;
                //window.location = "pdf/sol_permi.php?id="+idSP;
              //Materialize.toast(idDC, 4000);
                window.open('http://200.68.34.158/personal/pdf/decreto_act.php?id='+idDC1,'_blank');
            } 
          function Editar(id,ndc){
                var idDC = id;               
               $("#idmodifica").val(id);
               
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
                        <h4 class="light">RECONOCIMIENTO CAPACITACIONES</h4>
                        
                        <div class="row">
                            <form class="col s12" method="post" action="" enctype="multipart/form-data">
                                <div class="input-field col s6">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" style="text-transform: uppercase" placeholder="" value="">
                                    <label for="icon_prefix">RUT</label>
                                </div>
                                                                
                                <div class="input-field col s6">
                                    <button class="btn trigger" type="button" name="buscar" id="buscar" value="buscar"  onclick = "cargarusu();">Buscar</button>
                                </div>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>

                                <div class="input-field col s4">
                                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroNombre;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Nombres</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="apellidoP_usuario" id="apellidoP_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroApellidoP;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Paterno</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="apellidoM_usuario" id="apellidoM_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroApellidoM;?>" onkeypress="return soloLetras(event)">
                                    <label for="icon_prefix">Apellido Materno</label>
                                </div>                                
                                <div class="input-field col s3">
                                    <input type="text" name="prof_usuario" id="prof_usuario" class="validate" placeholder="" value="<?php echo $MuestroProf;?>" disabled>
                                    <label for="icon_prefix">Profesión</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="categoria_usuario" id="categoria_usuario" class="validate" placeholder="" value="<?php echo $MuestroCategoria;?>" disabled>
                                    <label for="icon_prefix">Categoría</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" name="nivel_usuario" id="nivel_usuario" class="validate" placeholder="" value="<?php echo $MuestroNivel;?>" disabled>
                                    <label for="nivel_usuario">Nivel</label>
                                </div>                                                             
                               <div class="input-field col s3">
                                    <input type="text" name="dependencia_usuario" id="dependencia_usuario" class="validate" placeholder="" value="<?php echo $MuestroDependencia;?>" disabled>
                                    <label for="icon_prefix">De quien depende</label>
                               </div>
                               <div class="input-field col s4">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s4">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" name="num_doc" id="num_doc" class="validate" placeholder="" value="<?php echo $MuestroNum;?>">
                                    <label for="hora_pun">DECRETO ALCALDICIO N°:  </label>
                                </div>
                                <div class="input-field col s4">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  <div class="input-field col s4">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                <div class="input-field col s4">
                                        <input type="text" class="datepicker" name="fecha_dc" id="fecha_dc" placeholder="" value="<?php echo $MuestroFec;?>" onchange="buscanum()">
                                      <label for="icon_prefix">Fecha Decreto</label>
                                    </div>
                               <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="row">
                                  <form class="col s12">
                                    <div class="row">
                                      <div class="input-field col s12">
                                        <textarea id="text_visto" class="materialize-textarea"><?php echo $MuestroVisto;?></textarea>
                                        <label for="text_visto">VISTOS</label>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                                <div class="row">
                                  <form class="col s12">
                                    <div class="row">
                                      <div class="input-field col s12">
                                        <textarea id="text_consi" class="materialize-textarea" maxlength="2500"><?php echo $MuestroConsi;?></textarea>
                                        <label for="text_consi">CONSIDERANDO</label>
                                      </div>
                                    </div>
                                  </form>
                                </div>                            
                              <br>
                              <br><div class="row">
                                  <form class="col s12">
                                    <div class="row">
                                      <div class="input-field col s12">
                                        <textarea id="text_decre" class="materialize-textarea"><?php echo $MuestroDec;?></textarea>
                                        <label for="text_consi">DECRETO</label>
                                      </div>
                                    </div>
                                  </form>
                                </div>      
                              <br>
                            <table id="tab_carrera" class="responsive-table bordered striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Actividad</th>
                                    <th width="100">Fecha</th>                                    
                                    <th>Horas</th>
                                    <th>Nota</th>
                                    <th>Nivel</th>
                                    <th>Puntaje</th>
                                    <th width="100">Fecha Ingreso</th>
                                </tr>
                                        <tbody>
                                            <!-- cargar la base de datos con php --> 

                                            <?php
                                              if($iddcre ==''){
                                                 $query = "SELECT CA_ID, CA_DES, DATE_FORMAT(CA_FEC,'%Y-%m-%d'), CA_HORA, CA_NOTA, CA_NIVEL, CA_TOTAL, DATE_FORMAT(CA_FEC_ING, '%Y-%m-%d'),
                                                 CA_ESTADO,CA_HORA_PUN,CA_NIVEL_PUN,CA_NOTA_PUN,CA_TOTAL FROM CARRERA_ACT WHERE (USU_RUT = '".$rut1."') AND (CA_ESTADO ='Inactivo')  
                                                 ORDER BY CA_FEC_ING";    
                                                 $respuesta = mysqli_query($cnn, $query); 
                                              }else{                                                
                                              $query = "SELECT CARRERA_ACT.CA_ID, CARRERA_ACT.CA_DES, DATE_FORMAT(CARRERA_ACT.CA_FEC,'%Y-%m-%d'), CARRERA_ACT.CA_HORA, CARRERA_ACT.CA_NOTA, CARRERA_ACT.CA_NIVEL,
                                                CARRERA_ACT.CA_TOTAL, DATE_FORMAT(CARRERA_ACT.CA_FEC_ING, '%Y-%m-%d'), CARRERA_ACT.CA_ESTADO,CARRERA_ACT.CA_HORA_PUN,CARRERA_ACT.CA_NIVEL_PUN,CARRERA_ACT.CA_NOTA_PUN,
                                                CARRERA_ACT.CA_TOTAL FROM DECRE_ACT_DETA INNER JOIN CARRERA_ACT ON DECRE_ACT_DETA.CA_ID = CARRERA_ACT.CA_ID WHERE (DECRE_ACT_DETA.USU_RUT = '".$rut1."') 
                                                AND (DECRE_ACT_DETA.DA_DC_NUM = '".$MuestroNum."') ORDER BY CA_FEC_ING";
                                                $respuesta = mysqli_query($cnn, $query);
                                              }
                                                 
                                             
                                                //recorrer los registros
                                                $cont = 0;                                                
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    echo "<tr>";
                                                        echo "<td><id='in".$cont."'>".$row_rs[0]."</td>";
                                                        echo "<td><class='col s6'>".utf8_encode($row_rs[1])."</td>";
                                                        echo "<td>".$row_rs[2]."</td>";
                                                        echo "<td>".$row_rs[3]."</td>";
                                                        echo "<td>".$row_rs[4]."</td>";
                                                        echo "<td>".$row_rs[5]."</td>";
                                                        echo "<td>".$row_rs[6]."</td>";
                                                        echo "<td>".$row_rs[7]."</td>";                                                    
                                                    echo "</tr>";                                                    
                                                        $suma = $suma + $row_rs[6];
                                                }
                                                        echo "<td></td>";
                                                        echo "<td></td>";
                                                        echo "<td></td>";
                                                        echo "<td></td>";
                                                        echo "<td></td>";
                                                        echo "<td>".'Total Puntos'."</td>";
                                                        echo "<td>".$suma."</td>";
                                            ?>

                                        </tbody>
                                    </thead>                                    
                                </table>
                                <br>
                                <br>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div> 
                                 <div class="input-field col s6">	
																	<input value="<?php echo $alcalde;?>" id="alcalde" type="text" class="validate" name="alcalde" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event);">
                                   <label for="alcalde">Favor indicar Alcalde o Subrogante si corresponde :</label>
                                  </div>
																  <div class="input-field col s4">
                                    <label>
																			<input type="checkbox" class="filled-in" id="dic_sub" name="dic_sub" <?php echo $alcsub;?>/>
      																<!--<label for="dic_sub">Subrogante</label>-->
                                      <span>Subrogante</span>
                                    </label>
																	</div>
                                  <div class="input-field col s2">                                   
                                    <select name="gen_alc" id="gen_alc" >
                                      <!--<option value="" disabled selected></option>-->
                                      <option value="<?php echo $genalc;?>"><?php echo $genalc;?></option>
                                      <option value="ALCALDE">ALCALDE</option>
                                      <option value="ALCALDESA">ALCALDESA</option>           
                                    </select>
                                    <label>Genero</label>
                                  </div> 
                          				<input type="text" id="df_dir_sub" name="df_dir_sub" class="validate" style="display: none">																
																	<div class="input-field col s6">
                                	<input type="text" name="secretaria" id="secretaria" class="validate" value="<?php echo $secretaria;?>" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)">
                                	<label for="secretaria">Indique Secretaria Municipal o subrogante (NOMBRE COMPLETO) :</label>
                            			</div>
																	<div class="input-field col s4">
                                  <label>
																	<input type="checkbox" class="filled-in" id="sec_sub" name="sec_sub"  <?php echo $secsub; ?>/>
                                    <span>Subrogante</span>
                                    </label>
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
                                    <div name ="decre" id="decre" type="button" onclick="decretar();" class="btn trigger">Decretar</div>
                                </div> 
                                <div class="input-field col s12">
                                    <input style="display:none" id="idmodifica" type="text" class="validate" name="idmodifica" value="<?php echo $iddcre;?>">
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
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Número Decreto</th>
                                    <th>Fecha</th>                            
                                </tr>
                                        <tbody>
                                            <?php                                                
                                                  $query = "SELECT DA_ID,DA_DC_NUM, DATE_FORMAT(DA_FEC,'%Y-%m-%d') FROM DECRE_ACT WHERE (USU_RUT = '".$rut1."')  AND (DA_ESTADO !='I') ORDER BY DA_FEC DESC";    
                                                    $respuesta = mysqli_query($cnn, $query);                                                
                                                $cont = 0;                                                
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    echo "<tr>";
                                                        echo "<td>  </td>";
                                                        echo "<td>  </td>";
                                                        echo "<td></td>";
                                                        echo "<td><id='in".$cont."'>".$row_rs[0]."</td>";
                                                        echo "<td><class='col s6'>".utf8_encode($row_rs[1])."</td>";
                                                        echo "<td>".$row_rs[2]."</td>";
                                                        echo '<td><button class="btn trigger" name="imprimir" onclick="Imprimir('; echo "'".utf8_encode($row_rs[0])."'"; echo');" id="imprimir" 
                                                            type="button">Imprimir</button></td>';
                                                        echo '<td><button class="btn trigger" name="editar" onclick="Editar('; echo "'".utf8_encode($row_rs[0])."'"; echo');cargarusu();" id="editar" 
                                                            type="button">Editar</button></td>';
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
        <script type="text/javascript" src="../../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../../include/js/materialize.js"></script>  
</html>
    </body>