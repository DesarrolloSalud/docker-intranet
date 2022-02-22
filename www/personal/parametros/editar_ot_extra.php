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
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $usu_rut_edit = $_GET['rut'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $buscar = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_CAT,USUARIO.USU_NIV,OT_EXTRA_AUT.OEA_LJ,OT_EXTRA_AUT.OEA_LJ_HI,OT_EXTRA_AUT.OEA_LJ_HF,OT_EXTRA_AUT.OEA_VI_HI,OT_EXTRA_AUT.OEA_VI_HF,OT_EXTRA_AUT.OEA_SDF,OT_EXTRA_AUT.OEA_SDF_HI,OT_EXTRA_AUT.OEA_SDF_HF,OT_EXTRA_AUT.OEA_FEC_INI,OT_EXTRA_AUT.OEA_FEC_FIN,USUARIO.USU_FEC_ING FROM USUARIO INNER JOIN OT_EXTRA_AUT ON USUARIO.USU_RUT = OT_EXTRA_AUT.USU_RUT WHERE (OT_EXTRA_AUT.OEA_ID= $usu_rut_edit) AND (USUARIO.USU_ESTA= 'ACTIVO')";
        $rs = mysqli_query($cnn, $buscar);
        if($row = mysqli_fetch_array($rs)){
            $MuestroRut=$row[0];
            $MuestroNombre=utf8_encode($row[1]);
            $MuestroApellidoP = utf8_encode($row[2]);
            $MuestroApellidoM = utf8_encode($row[3]);
            $MuestroCategoria = $row[4];
            $MuestroNivel = $row[5];            
            $MuestroLJ = utf8_encode($row[6]);
            $MuestroLJHI = ($row[7]);
            $MuestroLJHF = ($row[8]);
            $MuestroVINI = ($row[9]);
            $MuestroVFIN = ($row[10]); 
            $MuestroSDF = ($row[11]);                    
            $MuestroSDFHI = ($row[12]);
            $MuestroSDFHF = ($row[13]);
            $MuestroFechaInicio = utf8_encode($row[14]);
            $MuestroFechaTermino = utf8_encode($row[15]);
            $Muestrofechausuario= utf8_encode($row[16]);
            
            //Lunes a Jueves
            $token = strtok($MuestroLJ, "-"); // Primer token
              
                while($token !== false) {
                // En los tokens subsecuentes no se include el string $cadena

                    if($token =='LU'){                       
                        $lunes1="checked";
                         $token = strtok("-");                   
                    }

                    if($token =='MA'){                       
                        $martes1="checked";
                         $token = strtok("-");
                    }
                    if($token =='MI'){                       
                        $miercoles1="checked";
                         $token = strtok("-");
                    }

                    if($token =='JU'){                       
                        $jueves1="checked";
                         $token = strtok("-");
                    }
                      //  $token = strtok("-");
                }  

            //Viernes
            if($MuestroVINI!=='00:00:00'){
                $viernes1="checked";
            }

            //Sábado, Domingo y Festivo
            $token1 = strtok($MuestroSDF, "-"); // Primer token
              
                while($token1 !== false) {
                // En los tokens subsecuentes no se include el string $cadena

                    if($token1 =='S'){                       
                        $sabado1="checked";
                         $token1 = strtok("-");                   
                    }

                    if($token1 =='D'){                       
                        $domingo1="checked";
                         $token1 = strtok("-");
                    }
                    if($token1 =='F'){                       
                        $festivo1="checked";
                        $token1 = strtok("-");
                    }
                }   
        }  

        $id_formulario = 18;
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
        <script type="text/javascript" src="../../include/js/materialize.clockpicker.min.js"></script>
        <script type="text/javascript" src="../../include/js/moment.js"></script>
        <script>
            $(document).ready(function () {
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy-mm-dd'});
            });         


           function validafecha(){   

                var d1 = $("#Finicio").val();
                var d1=moment(d1, 'YYYY-MM-DD', true).format('DD-MM-YYYY');

                var d2 = $("#Ftermino").val();
                var d2=moment(d2, 'YYYY-MM-DD', true).format('DD-MM-YYYY');

                var dia1 = $("#dias").val();
                var año1 = moment(d1,'DD-MM-YYYY',true).format('YYYY');
                var año2 = moment(d2,'DD-MM-YYYY',true).format('YYYY');
                var ant = $("#dantes").val();
                ant=parseInt(ant);

                var d11=moment(d1, 'DD-MM-YYYY', true).format('YYYY MM DD');
                var d12=moment(d2, 'DD-MM-YYYY', true).format('YYYY MM DD');

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
                        <h4 class="light">Editar Trabajos Extraordinarios</h4>
                        <div class="row" style="position: fixed; top: 15%; right: 20%">
                            <div class="right col s12 m8 l8 block">
                                <div align="right"><h6><a href="mant_ot_extra.php" class="btn trigger">Volver</a></h6></div>
                            </div>
                        </div>
                        <div class="row">
                            <form name="form" class="col s12" method="post">
                                <div class="input-field col s6">
                                    <i class="mdi-action-account-circle prefix"></i>
                                    <input id="rut_usuario" type="text" class="validate" name="rut_usuario" placeholder="" disabled value="<?php echo $MuestroRut;?>">
                                    <label for="icon_prefix">RUT</label>
                                </div>
                                <div class="input-field col s12">
                                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $MuestroNombre ." ". $MuestroApellidoP ." ". $MuestroApellidoM;?>" disabled>
                                    <label for="icon_prefix">Nombres</label>
                                </div>
                              
                                <div class="input-field col s6">
                                    <input type="text" name="text" name="categoria_usuario" id="categoria_usuario" placeholder="" value="<?php echo $MuestroCategoria;?>" disabled>
                                    <label for="categoria_usuario">Categoria</label>
                                </div>
                                 <div class="input-field col s6">
                                    <input type="text" name="nivel_usuario" id="nivel_usuario" class="validate" placeholder="" value="<?php echo $MuestroNivel;?>" disabled>
                                    <label for="nivel_usuario">Nivel</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" class="datepicker" name="Finicio" id="Finicio" placeholder="Desde" value="<?php echo $MuestroFechaInicio;?>"  required>
                                    <label for="icon_prefix">Desde</label>                
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" class="datepicker" name="Ftermino" id="Ftermino" placeholder="Hasta" value="<?php echo $MuestroFechaTermino;?>" onchange="validafecha();" required> 
                                    <label for="icon_prefix">Hasta</label>                      
                                </div>                            
                                 
                                
                                <div class="input-field col s1">
                                <label>
                                    <input type="checkbox" class="filled-in" id="lunes" name="lunes" <?php echo $lunes1;?> />
                                <span>Lunes</span>
                                </label>                             
                                </div>  

                                <div class="input-field col s1">
                                <label>
                                  <input type="checkbox" class="filled-in" id="martes" name="martes" <?php echo $martes1;?> />
                                <span>Martes</span>
                                </label>  
                                </div>
                                <div class="input-field col s1">
                                <label>
                                  <input type="checkbox" class="filled-in" id="miercoles" name="miercoles" <?php echo $miercoles1;?> />
                                <span>Miércoles</span>
                                </label>  
                                </div>
                                <div class="input-field col s1">
                                <label>
                                  <input type="checkbox" class="filled-in" id="jueves" name="jueves" <?php echo $jueves1;?> />
                                <span>Jueves</span>
                                </label>  
                                </div>
                                <div class="input-field col s4">
                                    <input id="hora_inilj" name="hora_inilj" class="timepicker" type="time" placeholder="Hora Inicio" value="<?php echo $MuestroLJHI;?>" required>
                                </div>
                                <div class="input-field col s4">
                                    <input id="hora_finlj" name="hora_finlj" class="timepicker" type="time" placeholder="Hora Termino" value="<?php echo $MuestroLJHF;?>"  required>
                                </div>
                                <div class="input-field col s4">
                                <label>
                                  <input type="checkbox" class="filled-in" id="viernes" name="viernes" <?php echo $viernes1;?> />
                                <span>Viernes</span>
                                </label>  
                                </div>
                                <div class="input-field col s4">
                                    <input id="hora_iniv" name="hora_iniv" class="timepicker" type="time" placeholder="Hora Inicio" value="<?php echo $MuestroVINI;?>" required>
                                </div>
                                <div class="input-field col s4">
                                    <input id="hora_finv" name="hora_finv" class="timepicker" type="time" placeholder="Hora Termino" value="<?php echo $MuestroVFIN;?>" required>
                                </div>
                                <div class="input-field col s1">
                                <label>
                                  <input type="checkbox" class="filled-in" id="sabado" name="sabado" <?php echo $sabado1;?> />
                                <span>Sábado</span>
                                </label>  
                                </div>
                                
                                <div class="input-field col s1">
                                <label>
                                  <input type="checkbox" class="filled-in" id="domingo" name="domingo" <?php echo $domingo1;?> />
                                <span>Domingo</span>
                                </label>  
                                </div>
                                
                                <div class="input-field col s2">
                                <label>
                                    <input type="checkbox" class="filled-in" id="festivo" name="festivo" <?php echo $festivo1;?> />
                                <span>Festivo</span>
                                </label>                             
                                </div>
                                
                                <div class="input-field col s4">
                                    <input id="hora_inisdf" name="hora_inisdf" class="timepicker" type="time" placeholder="Hora Inicio" value="<?php echo $MuestroSDFHI ;?>" required>
                                </div>
                                <div class="input-field col s4">
                                    <input id="hora_finsdf" name="hora_finsdf" class="timepicker" type="time" placeholder="Hora Termino" value="<?php echo $MuestroSDFHF ;?>" required>
                                </div>                      

                                <div class="col s12">
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
        <script type="text/javascript" src="../../include/js/materialize.clockpicker.min.js"></script>
        
        <?php
            if($_POST['guardar'] == "Guardar"){
                $lun1= $_POST['lunes'];
                $mar1= $_POST['martes'];
                $mir1= $_POST['miercoles'];
                $jue1= $_POST['jueves'];
                $vie1= $_POST['viernes'];
                $sab1= $_POST['sabado'];
                $dom1= $_POST['domingo'];
                $fes1= $_POST['festivo'];
               
                if ($lun1  == 'on'){
                    $LMMJ="LU";
                }elseif ($lun1=='') {
                    $LMMJ="";
                }               

                if($LMMJ==""){
                    if($mar1=='on'){
                        $LMMJ="MA";
                    }
                }elseif($LMMJ!=""){
                    if($mar1=='on'){
                        $LMMJ=$LMMJ . "-MA";
                    }
                }    

                if($LMMJ==""){
                    if($mir1=='on'){
                        $LMMJ= "MI";
                    }
                }elseif($LMMJ!=""){
                    if($mir1=='on'){
                        $LMMJ= $LMMJ . "-MI";
                    }
                }              

                if($LMMJ==""){
                    if($jue1=='on'){
                        $LMMJ="JU";
                    }
                }elseif($LMMJ!=""){
                    if($jue1=='on'){
                        $LMMJ= $LMMJ . "-JU";
                    }
                }               

                if($vie1=='on'){
                    $vieini= $_POST['hora_iniv'];
                    $viefin= $_POST['hora_finv'];
                }else{
                    $vieini = "00:00:00";
                    $viefin ="00:00:00";
                }

                $fecini= $_POST['Finicio'];
                $fecter= $_POST['Ftermino'];
                $horinilj = $_POST['hora_inilj'];
                $horfinlj = $_POST['hora_finlj'];
                $hora_inisdf = $_POST['hora_inisdf'];
                $hora_finsdf = $_POST['hora_finsdf'];


                if ($sab1  == 'on'){
                    $SDF="S";
                }elseif ($sab1=='') {
                    $SDF="";
                }               

                if($SDF==""){
                    if($dom1=='on'){
                        $SDF="D";
                    }
                }elseif($SDF!=""){
                    if($dom1=='on'){
                        $SDF=$SDF . "-D";
                    }
                }    

                if($SDF==""){
                    if($fes1=='on'){
                        $SDF= "F";
                    }
                }elseif($SDF!=""){
                    if($fes1=='on'){
                        $SDF= $SDF . "-F";
                    }
                }              

                if($SDF==""){
                    $hora_inisdf="00:00:00";
                    $hora_finsdf="00:00:00";
                }
                $estadoota ="ACTIVA";

                

                 $actualizarotextra ="UPDATE OT_EXTRA_AUT SET  OEA_FEC ='".$fecha."', OEA_FEC_INI ='".$fecini."', OEA_FEC_FIN ='".$fecter."', OEA_LJ ='".$LMMJ."', OEA_LJ_HI ='".$horinilj."', OEA_LJ_HF ='".$horfinlj."', OEA_VI_HI='".$vieini."', OEA_VI_HF='".$viefin."', OEA_SDF='".$SDF."', OEA_SDF_HI='".$hora_inisdf."', OEA_SDF_HF='".$hora_finsdf."' WHERE (USU_RUT = '".$MuestroRut."')";                
                mysqli_query($cnn, $actualizarotextra);
                ?> <script type="text/javascript"> window.location="mant_ot_extra.php";</script>  <?php

               
            }
        ?>
    </body>
</html>



