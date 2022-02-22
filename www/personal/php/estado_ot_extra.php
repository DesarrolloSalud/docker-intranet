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
        $iddoc = $_GET['id'];
        $Sdependencia = $_SESSION['USU_DEP'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 20;
        $ipcliente = getRealIP();
        $rutder1=0;
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
        <script>
            $(document).ready(function () {
                //Animaciones
                $('select').material_select(); 
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $('.datepicker').pickadate({firstDay: true});
                $('.tooltipped').tooltip({delay: 50});
                $('.timepicker').pickatime({
                    default: 'now',
                    twelvehour: false, // change to 12 hour AM/PM clock from 24 hour
                    donetext: 'OK',
                    autoclose: false,
                    vibrate: true // vibrate the device when dragging clock hand
                });
            });

            function ImprimirOT(id){
                var idOT = id;
                //window.location = "pdf/sol_permi.php?id="+idSP;
                window.open('http://200.68.34.158/personal/pdf/dto_ot_extra.php?id='+idOT,'_blank')
            }
            function Cargar(){
                $("#derivar").attr("disabled","disabled");
            }
            function Listo(){
                $("#derivar").removeAttr("disabled");
            }
            
        </script>
    </head>
    <body onload="Cargar()">
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
                        <h4 class="light">Detalle Orden de Trabajo Extraordinaria</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                               <!--<div align="right"><h6><a href="nuevo_usuario.php" class="btn trigger">Nuevo</a></h6></div> -->
                            </div>
                        </div>
                        </br>
                        <div class="row">                        
                            <form name="form" class="col s12" method="post">
                                <table class="responsive-table bordered striped" font-family="9px";>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>USUARIO</th>
                                            <th>FECHA</th>
                                            <th>HORA</th>
                                            <th>FOLIO INTERNO</th>
                                            <th>FOLIO MUNICIPAL</th>
                                            <th>ESTADO</th>
                                        </tr>
                                        <?php
                                            $consultaid =  "SELECT OEE_ID,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,OEE_FEC,OEE_HOR,OEE_FOL_INT,OEE_FOL_MUN,OEE_ESTA,USU_RUT_DER FROM OT_EXTRA_ENC INNER JOIN USUARIO ON OT_EXTRA_ENC.USU_RUT=USUARIO.USU_RUT WHERE (OEE_ID='$iddoc')";
                                            $respuesta1 = mysqli_query($cnn, $consultaid);

                                            while ($row_rs = mysqli_fetch_array($respuesta1)){
                                                    echo "<tr>";
                                                        echo "<td><input type='text' id='in".$cont."' class='validate' value='".$row_rs[0]."' style='display: none'>".$row_rs[0]."</td>";                                                   
                                                        echo "<td>".utf8_encode($row_rs[1])." ".utf8_encode($row_rs[2])." ".utf8_encode($row_rs[3])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[4])."</td>";                                                     
                                                        echo "<td>".utf8_encode($row_rs[5])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[6])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[7])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[8])."</td>";
                                                        //echo "<td>".utf8_encode($row_rs[9])."</td>";
                                                        $oeeid= $row_rs[0];
                                                        $fol1= $row_rs[6];
                                                        $fol2= $row_rs[7];
                                                        $rutder1 = $row_rs[9];                                                        
                                                    echo "</tr>";                                                    
                                                }
                                            $queryderiva = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO WHERE (USUARIO.USU_RUT = '$rutder1')";
                                            $rs = mysqli_query($cnn, $queryderiva);
                                            $row = mysqli_fetch_row($rs);
                                            
                                            $Nombreder = utf8_encode($row[1])." ".utf8_encode($row[2])." ".utf8_encode($row[3]);

                                        ?>                                       
                                        <div class="input-field col s3">
                                            <input type="text" name="folint" id="folint" class="validate" placeholder="" value="<?php echo $fol1;?>" required="">
                                            <label for="icon_prefix">Folio Interno</label>
                                        </div>

                                        <div class="input-field col s3">
                                            <input type="text" name="folmun" id="folmun" class="validate" placeholder="" value="<?php echo $fol2;?>" required="">
                                            <label for="icon_prefix">Folio Municipal</label>
                                        </div>

                                        <div class="col s3">
                                            <button id="guardar" class="btn trigger" type="submit" name="guardar" value="Guardar">Guardar</button>
                                        </div>
                                        <div class="col s3">
                                            <button class='btn trigger' name='imprimir' id='imprimir' type='button' onclick='ImprimirOT(<?php echo $oeeid;?>);'>Imprimir</button>
                                        </div>
                                        </br>
                                        </br>
                                        <?php
                                        if($_POST['guardar'] == "Guardar"){                                          
                                            $folint = $_POST['folint'];
                                            $folmun = $_POST['folmun'];
                                            $oeeestado = "INGRESA FOLIO";
                                            $actualizafolio ="UPDATE OT_EXTRA_ENC SET  OEE_FOL_INT= '".$folint."', OEE_FOL_MUN = '".$folmun."' WHERE (OEE_ID = '".$oeeid."')";
                                             mysqli_query($cnn, $actualizafolio);
                                            
                                            $docid= 7;
                                            $FecActual = date("Y-m-d");
                                            $HorActual = date("H:i:s");
                                            $HDAccion = "CREA FOLIO INTERNO: ".$folint." / FOLIO MUNICIPAL: ".$folmun;
                                            $guardar_historial = "INSERT INTO HISTO_DOCU (HD_FOLIO, USU_RUT, HD_FEC, HD_HORA, DOC_ID, HD_ACC) VALUES ('$oeeid','$Srut','$FecActual','$HorActual',$docid, '$HDAccion')";                    
                                            mysqli_query($cnn, $guardar_historial);
                                            ?> <script type="text/javascript"> window.location="../index.php";</script> <?php
                                        }
                                        ?>
                                        <tr>
                                        </br>
                                        </br>
                                            <!-- <th>ID</th> -->
                                            <th>RUT</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <!-- <th>Período</th> -->
                                            <th>Lunes-Jueves</th>
                                            <th>Viernes</th>
                                            <th>Sáb-Dom-Festivo</th>                                            
                                        </tr>
                                        <tbody>
                                            <!-- cargar la base de datos con php --> 
                                            <?php                                                
                                                $query = "SELECT OT_EXTRA_AUT_F.USU_RUT,USUARIO.USU_NOM, USUARIO.USU_APP, USUARIO.USU_APM, USUARIO.USU_CAT, OT_EXTRA_AUT_F.OEA_LJ_HI, OT_EXTRA_AUT_F.OEA_LJ_HF, OT_EXTRA_AUT_F.OEA_VI_HI,OT_EXTRA_AUT_F.OEA_VI_HF, OT_EXTRA_AUT_F.OEA_SDF_HI, OT_EXTRA_AUT_F.OEA_SDF_HF FROM OT_EXTRA_AUT_F INNER JOIN USUARIO ON OT_EXTRA_AUT_F.USU_RUT = USUARIO.USU_RUT WHERE (OT_EXTRA_AUT_F.OEE_ID = '$iddoc')";     
                                                    $respuesta = mysqli_query($cnn, $query);
                                                                                            
                                                while ($row_rs = mysqli_fetch_array($respuesta)){
                                                    echo "<tr>";
                                                        echo "<td><input type='text' id='in".$cont."' class='validate' value='".$row_rs[0]."' style='display: none'>".$row_rs[0]."</td>";                                                   
                                                        echo "<td>".utf8_encode($row_rs[1])." ".utf8_encode($row_rs[2])." ".utf8_encode($row_rs[3])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[4])."</td>";                                                     
                                                        echo "<td>".utf8_encode($row_rs[5])."-".utf8_encode($row_rs[6])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[7])."-".utf8_encode($row_rs[8])."</td>";
                                                        echo "<td>".utf8_encode($row_rs[9])."-".utf8_encode($row_rs[10])."</td>";
                                                    echo "</tr>";                                                    
                                                }
                                            ?>
                                        </tbody>
                                    </thead>                                    
                                </table>
                                </br>
                                <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura" onchange="Listo();">
                                <option disabled selected><?php echo $Nombreder;?></option>
                                    <?php
                                            $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND (ESTABLECIMIENTO.EST_NOM = '$Sdependencia')";
                                            $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){                                                
                                                echo $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];                                            
                                                    printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");                               
                                            }
                                            //echo $nombre;                         
                                    ?>
                                                                       
                                </select>
                                </div>
                                <div class="col s12">
                                    <button id="derivar" class="btn trigger" type="submit" name="derivar" value="Derivar">Autorizar</button>
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
        <script>
            $(document).ready(function () {
                //Animaciones
                $('select').material_select(); 
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $('.modal-trigger').leanModal({);                
            });
        </script>

        <?php                     
            if($_POST['derivar'] == "Derivar"){ 
                
                              
                $OEEESTA ="AUTORIZADO";
                $rutder = $_POST['jefatura'];                

                $actualizar = "UPDATE OT_EXTRA_ENC SET USU_RUT_DER = '$rutder', OEE_ESTA ='$OEEESTA' WHERE (OEE_ID = '$iddoc')";
                mysqli_query($cnn, $actualizar);
                

                $docid= 7;
                $FecActual = date("Y-m-d");
                $HorActual = date("H:i:s");
                $HDAccion = "AUTORIZA Y DERIVA DOCUMENTO A: ".$MuestroJefatura;
                echo $guardar_historial1 = "INSERT INTO HISTO_DOCU (HD_FOLIO, USU_RUT, HD_FEC, HD_HORA, DOC_ID, HD_ACC) VALUES ($doc_id,'$Srut','$FecActual','$HorActual',$iddoc, '$HDAccion')";                    
                mysqli_query($cnn, $guardar_historial1);

            }    
        ?>      

    </body>
</html>

