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
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $ano1= date("Y");
        $id_formulario = 16;
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
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.clockpicker.min.js"></script>
        <script type="text/javascript" src="../../include/js/moment.js"></script>
        <script>
            $(document).ready(function () {
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                //$('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
                $("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        M.toast({html: 'Rut incorrecto'});  
                        $("#buscar").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                     // M.toast({html: 'Rut correcto'});  
                      $("#buscar").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });  
            });  
            //funcion para editar un usuario
            function Editar(r){
                var RutUsu = r;                
                //console.log(r);
                window.location = "editar_ot_extra.php?rut="+RutUsu;
            }
            function activa(cont){
                var cont = cont;                
                var usu_rut = "#in"+cont;
                var oea_id = "#oea_id"+cont;
                var rut = $(usu_rut).val();
                var ide = $(oea_id).val(); 
                var actot1= "#che"+cont;
                var value1 = $(actot1).val();                
                $.post( "../php/actdes_ot_extra.php", { "rut_usu" : rut, "esta_enviado" : value1, "oea_id" : ide }, null, "json" )                
                //$.post( "../php/actdes_ot_extra.php", { "oea_id" : ide, "esta_enviado" : value1 }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idActivar + " estado enviado " + estado );
                        console.log( "La solicitud se ha completado correctamente." );
                        //window.location = "mant_ot_extra.php";
                        window.location="mant_ot_extra.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                        //window.location="mant_ot_extra.php";
                    }
                }); 


            }
            function inactivo(cont){
                var cont = cont;                
                var usu_rut = "#in"+cont;
                var oea_id = "#oea_id"+cont;
                var rut = $(usu_rut).val();
                var ide = $(oea_id).val(); 
                var actot1= "#che"+cont;
                var value2 = $(actot1).val();
                var msng1="desactivado1";                
                            
                $.post( "../php/actdes_ot_extra.php", { "rut_usu" : rut, "esta_enviado" : value2, "oea_id" : ide }, null, "json" )
                //$.post( "../php/actdes_ot_extra.php", { "oea_id" : ide, "esta_enviado" : value1 }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id enviada " + idActivar + " estado enviado " + estado );
                        console.log( "La solicitud se ha completado correctamente." );
                        //window.location = "mant_ot_extra.php";                        
                        window.location="mant_ot_extra.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                        //window.location="mant_ot_extra.php";
                    }
                });               
                
         
            }

            function Accesos(r){
                var RutUsu = r;
                //console.log("accesos.php?id="+idUsr);
                window.location = "accesos.php?rut="+RutUsu;
            }
            
          function validafecha(){   

                var d1 = $("#Finicio").val();
                var d1=moment(d1, 'YYYY/MM/DD', true).format('DD/MM/YYYY');

                var d2 = $("#Ftermino").val();
                var d2=moment(d2, 'YYYY/MM/DD', true).format('DD/MM/YYYY');

                var dia1 = $("#dias").val();
                var año1 = moment(d1,'DD/MM/YYYY',true).format('YYYY');
                var año2 = moment(d2,'DD/MM/YYYY',true).format('YYYY');
                var ant = $("#dantes").val();
                ant=parseInt(ant);

                var d11=moment(d1, 'DD/MM/YYYY', true).format('YYYY MM DD');
                var d12=moment(d2, 'DD/MM/YYYY', true).format('YYYY MM DD');

                var diferencia= moment(d12).diff(d11,'days');              
                console.log(diferencia);

                if (d12 <= d11){
                        M.toast({html: 'Fecha no válida'});
                        $("#Ftermino").val("");                                              
                }else if(año1 != año2){
                        M.toast({html: 'Fecha no válida'});
                        $("#Ftermino").val("");
                }                               
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
                        <h4 class="light">Orden de Trabajo Extraordinaria</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                               <!--<div align="right"><h6><a href="nuevo_usuario.php" class="btn trigger">Nuevo</a></h6></div> -->
                            </div>
                        </div>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                            <div class="input-field col s4">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" style="text-transform: uppercase" placeholder="">
                                    <label for="icon_prefix">RUT</label>
                            </div>
                            <div class="col s4">
                                    <button id="agregar" class="btn trigger" type="submit" name="agregar" value="Agregar">Agregar</button>
                            </div>
                                <table class="responsive-table bordered striped" font-family="9px";>
                                    <thead>
                                        <tr>
                                            <!-- <th>ID</th> -->
                                            <th>RUT</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <!-- <th>Período</th> -->
                                            <th>Lunes-Jueves</th>
                                            <th>Viernes</th>
                                            <th>Sáb-Dom-Festivo</th>
                                            <th>Estado</th>
                                        </tr>
                                        <tbody>                                          

                                            <?php   
                                            if($Srut=='15.922.085-0' || $Srut =='15.738.663-8'){
                                                $query = "SELECT OT_EXTRA_AUT.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT, OT_EXTRA_AUT.OEA_LJ_HI, OT_EXTRA_AUT.OEA_LJ_HF, OT_EXTRA_AUT.OEA_VI_HI,OT_EXTRA_AUT.OEA_VI_HF, OT_EXTRA_AUT.OEA_SDF_HI, OT_EXTRA_AUT.OEA_SDF_HF,OT_EXTRA_AUT.OEA_ESTA, MAX(OT_EXTRA_AUT.OEA_ID)FROM OT_EXTRA_AUT INNER JOIN USUARIO ON OT_EXTRA_AUT.USU_RUT = USUARIO.USU_RUT LEFT JOIN ESTABLECIMIENTO ON ESTABLECIMIENTO.EST_ID = USUARIO.EST_ID WHERE (USUARIO.USU_ESTA = 'ACTIVO') GROUP BY USU_RUT";     
                                                    $respuesta = mysqli_query($cnn, $query);
                                            }else{
                                                                                        
                                                $query = "SELECT OT_EXTRA_AUT.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT, OT_EXTRA_AUT.OEA_LJ_HI, OT_EXTRA_AUT.OEA_LJ_HF, OT_EXTRA_AUT.OEA_VI_HI,OT_EXTRA_AUT.OEA_VI_HF, OT_EXTRA_AUT.OEA_SDF_HI, OT_EXTRA_AUT.OEA_SDF_HF,OT_EXTRA_AUT.OEA_ESTA, MAX(OT_EXTRA_AUT.OEA_ID)FROM OT_EXTRA_AUT INNER JOIN USUARIO ON OT_EXTRA_AUT.USU_RUT = USUARIO.USU_RUT LEFT JOIN ESTABLECIMIENTO ON ESTABLECIMIENTO.EST_ID = USUARIO.EST_ID WHERE (USUARIO.EST_ID = ".$Sestablecimiento.") AND (USUARIO.USU_ESTA = 'ACTIVO') GROUP BY USU_RUT";     
                                                    $respuesta = mysqli_query($cnn, $query);
                                            }  
                                             
                                                //recorrer los registros
                                                $cont = 0;                                                
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    echo "<tr>";
                                                        echo "<td><input type='text' id='in".$cont."' class='validate' value='".$row_rs[0]."' style='display: none'>".$row_rs[0]."</td>";             
                                                        //echo "<td>".$row_rs[1]."</td>";
                                                        echo "<td>".utf8_encode($row_rs[1])." ".utf8_encode($row_rs[2])." ".utf8_encode($row_rs[3])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[4])."</td>";                                                     
                                                        //echo "<td>".utf8_encode($row_rs[5])."/".utf8_encode($row_rs[6])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[5])."-".utf8_encode($row_rs[6])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[7])."-".utf8_encode($row_rs[8])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[9])."-".utf8_encode($row_rs[10])."</td>";
                                                        //echo "<td>".utf8_encode($row_rs[11])."</td>";

                                                        if ($row_rs[11] == "ACTIVA"){
                                                        
                                                           // echo '<td><p><input type="checkbox" id="che'.$cont.'" onclick="inactivo('.$cont.');" value="DESACTIVADO" checked="checked" />  <label for="che'.$cont.'"></label></p></td>';                  
                                                        echo '<td>';
                                                        echo '<label>';
                                                          echo '<input type="checkbox" class="filled-in" id="che'.$cont.'" onclick="inactivo('.$cont.');" value="DESACTIVADO" checked="checked"/>';
                                                          echo '<span></span>';
                                                        echo '</label>';
                                                        echo '</td>';
                                                        }else{
                                                            //echo '<td><p><input type="checkbox" id="che'.$cont.'" onclick="activa('.$cont.');" value="ACTIVA" />  <label for="che'.$cont.'"></label></p></td>';
                                                        
                                                        echo '<td>';
                                                        echo '<label>';
                                                          echo '<input type="checkbox" id="che'.$cont.'" onclick="activa('.$cont.');" value="ACTIVA"/>';
                                                          echo '<span></span>';
                                                        echo '</label>';
                                                        echo '</td>';
                                                        }
                                                        
                                                    echo '<td><button class="btn trigger" name="editar" onclick="Editar('; echo "'".$row_rs[12]."'"; echo');" id="editar" type="button">Editar</button></td>';
                                                    echo "<td><input type='text' id='oea_id".$cont."' class='validate' value='".$row_rs[12]."' style='display: none'>".$row_rs[12]."</td>";
                                                    echo "</tr>";
                                                    $cont = $cont + 1;
                                                }
                                            ?>

                                        </tbody>
                                    </thead>                                    
                                </table>
                                </br>
                                <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura">
                                    <?php
                                        $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = 1) AND (USUARIO.USU_JEF = 'SI') AND (ESTABLECIMIENTO.EST_NOM = 'DEPARTAMENTO DE SALUD')";
                                        $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){
                                                echo $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                            }
                                            echo "<option value='no'>Jefe Directo</option>";
                                    ?>
                                </select>
                                  
                                <div class="input-field col s12">
                                        <input type="text" name="motivo" id="motivo" class="validate" placeholder="" >
                                        <label for="motivo">MOTIVO</label>
                                    </div>
                                </div>                                
                                <div class="input-field col s4">
                                    <input type="text" class="datepicker" name="Finicio" id="Finicio" placeholder="Desde" >
                                    <label for="icon_prefix">Desde</label>                
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" class="datepicker" name="Ftermino" id="Ftermino" placeholder="Hasta" onchange="validafecha();" > 
                                    <label for="icon_prefix">Hasta</label>                      
                                </div>
                                <div class="col s4">
                                    <button id="guardar" class="btn trigger" type="submit" name="guardar" value="Guardar">Guardar</button>
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
        <script type="text/javascript" src="../../include/js/moment.js"></script>
        <?php                     
            if($_POST['guardar'] == "Guardar"){ 
                $docid= 7;
                $usurut2 = $_POST['jefatura'];
                $fol1=0;
                $fol2=0;
                $rutder=0;
                $OEEESTA ="ENVIADO";
                $motivo = $_POST['motivo'];
                $Finicio = $_POST['Finicio'];
                $Ftermino = $_POST['Ftermino'];
                $insertaotextra = "INSERT INTO OT_EXTRA_ENC (USU_RUT,OEE_FEC,OEE_HOR,OEE_FEC_DEC,OEE_FOL_MUN,OEE_ESTA,USU_RUT_DIR,USU_RUT_DEC,OEE_MOTIVO,DOC_ID) VALUES ('$Srut','$fecha','$hora','$fecha','$fol2','$OEEESTA','$usurut2','$rutder','$motivo','$docid')";
                mysqli_query($cnn, $insertaotextra);
                
               $consultaid =  "SELECT OEE_ID FROM OT_EXTRA_ENC WHERE (USU_RUT='$Srut') AND (OEE_FEC = '$fecha') AND (OEE_HOR ='$hora')";
                $respuesta1 = mysqli_query($cnn, $consultaid);
                if (mysqli_num_rows($respuesta1) != 0){
                    $rowA = mysqli_fetch_row($respuesta1);                    
                        $doc_id = $rowA[0];                    
                }else{
                    ?> 
                    <script>M.toast({html: 'Error al generar documento, favor comunicarse con Informática'});</script>
                <?php
                }         
                    if($Srut=='15.922.085-0' || $Srut =='15.738.663-8'){
                        $query1 = "SELECT USU_RUT,OEA_FEC,OEA_LJ,OEA_LJ_HI, OEA_LJ_HF, OEA_VI_HI,OEA_VI_HF,OEA_SDF, OEA_SDF_HI, OEA_SDF_HF,OEA_VI FROM OT_EXTRA_AUT WHERE (OEA_ESTA='ACTIVA')";       
                            $respuesta2 = mysqli_query($cnn, $query1);
                    }else{                                                                                      
                        $query1 = "SELECT OT_EXTRA_AUT.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT, OT_EXTRA_AUT.OEA_LJ_HI, OT_EXTRA_AUT.OEA_LJ_HF, OT_EXTRA_AUT.OEA_VI_HI,OT_EXTRA_AUT.OEA_VI_HF, OT_EXTRA_AUT.OEA_SDF_HI, OT_EXTRA_AUT.OEA_SDF_HF,OT_EXTRA_AUT.OEA_ESTA, MAX(OT_EXTRA_AUT.OEA_ID)FROM OT_EXTRA_AUT INNER JOIN USUARIO ON OT_EXTRA_AUT.USU_RUT = USUARIO.USU_RUT LEFT JOIN ESTABLECIMIENTO ON ESTABLECIMIENTO.EST_ID = USUARIO.EST_ID WHERE (USUARIO.EST_ID = ".$Sestablecimiento.") AND (USUARIO.USU_ESTA = 'ACTIVO') GROUP BY USU_RUT";
                            $respuesta2 = mysqli_query($cnn, $query1);
                    }        
                    //todos iguales
                    $lunesjueves="LU-MA-MI-JU";
                    $sdf="S-D-F";
                    $vie="V";
                    $horaotextra1= "17:00:00";
                    //$horaotextra2= "22:00:00";
                    $horaotextra3= "16:00:00";
                    $horaotextra4= "08:00:00";
                    $horaotextra5= "00:00:00";
                    $inactivoot = "ACTIVA";
                    $fecha1 = $ano1."-01-01";
                    $fecha2= $ano1."-12-31";
                 
                                                                                      
                    while ($row_rs = mysqli_fetch_array($respuesta2)){
                      $guarotxdeta = "INSERT INTO OT_EXTRA_AUT_F (OEE_ID,USU_RUT,OEA_FEC,OEA_FEC_INI,OEA_FEC_FIN,OEA_LJ,OEA_LJ_HI,OEA_LJ_HF,OEA_VI_HI,OEA_VI_HF,OEA_SDF,OEA_SDF_HI,OEA_SDF_HF,OEA_VI) VALUES     ('$doc_id','$row_rs[0]','$row_rs[1]','$fecha1','$fecha2','$lunesjueves','$horaotextra1','$horaotextra5','$horaotextra3','$horaotextra5','$sdf','$horaotextra4','$horaotextra5','$vie')";
                      //con detalle propio
                     //$guarotxdeta = "INSERT INTO OT_EXTRA_AUT_F (OEE_ID,USU_RUT,OEA_FEC,OEA_FEC_INI,OEA_FEC_FIN,OEA_LJ,OEA_LJ_HI,OEA_LJ_HF,OEA_VI_HI,OEA_VI_HF,OEA_SDF,OEA_SDF_HI,OEA_SDF_HF,OEA_VI) VALUES                                         ('$doc_id','$row_rs[0]','$row_rs[1]','$Finicio','$Ftermino','$row_rs[2]','$row_rs[3]','$row_rs[4]','$row_rs[5]','$row_rs[6]','$row_rs[7]','$row_rs[8]','$row_rs[9]','$row_rs[10]')";
                    mysqli_query($cnn, $guarotxdeta); 
                    }
                    ?>
                   <script>   
                        M.toast({html: 'Documento Generado, Número interno: '+<?php echo $doc_id;?>});                  
                   </script>                   
                   <?php
                        $docid= 7;
                        $FecActual = date("Y-m-d");
                        $HorActual = date("H:i:s");
                        $HDAccion = "CREA DOCUMENTO AUTORIZACION ORDER DE TRABAJO EXTRAORDINARIO";
                        $guardar_historial = "INSERT INTO HISTO_DOCU (HD_FOLIO, USU_RUT, HD_FEC, HD_HORA, DOC_ID, HD_ACC) VALUES ($doc_id,'$Srut','$FecActual','$HorActual',$docid, '$HDAccion')";                             mysqli_query($cnn, $guardar_historial);
            }elseif($_POST['agregar'] == "Agregar"){
              $nuevo_rut= $_POST['rut_usuario'];
              $lunesjueves="LU-MA-MI-JU";
              $sdf="S-D-F";
              $vie="V";
              $horaotextra1= "17:00:00";
              //$horaotextra2= "22:00:00";
              $horaotextra3= "16:00:00";
              $horaotextra4= "08:00:00";
              $horaotextra5= "00:00:00";
              $inactivoot = "ACTIVA";
              $fecha1 = $ano1."-01-01";
              $fecha2= $ano1."-12-31";
              
              $consultarut =  "SELECT USU_RUT FROM OT_EXTRA_AUT WHERE (USU_RUT='$nuevo_rut')";
              $respuesta1 = mysqli_query($cnn, $consultarut);
                if (mysqli_num_rows($respuesta1) != 0){
                   ?> 
                    <script>M.toast({html: 'Funcionario ya creado'});</script>
                <?php                    
                }else{
                $insertaotextra = "INSERT INTO OT_EXTRA_AUT(USU_RUT,OEA_FEC,OEA_FEC_INI,OEA_FEC_FIN,OEA_LJ,OEA_LJ_HI,OEA_LJ_HF,OEA_VI,OEA_VI_HI,OEA_VI_HF,OEA_SDF,OEA_SDF_HI,OEA_SDF_HF,OEA_ESTA) VALUES ('$nuevo_rut','$fecha','$fecha1','$fecha2','$lunesjueves','$horaotextra1','$horaotextra5','$vie','$horaotextra3','$horaotextra5','$sdf','$horaotextra4','$horaotextra5','$inactivoot')";
                mysqli_query($cnn, $insertaotextra);
                }         
              
              
            }
        ?>
    </body>
</html>

