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
        $id1 = $_GET['tipo'];
        $id2 = $_GET['id'];
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();        
        $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE USU_RUT = '$usu_rut_edit'";
        $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
        if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
            $GuardoEstablecimiento=$row_eyd[0];
            $GuardoDependencia=$row_eyd[1];
        }
        $id_formulario = 47;
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
        <title>Personal Salud</title>
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
        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () { 
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
            });
                 
                
         
        </script>
    </head>
    <body onload="cargar();">
        
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">Subir Archivo</h4>
                        <?php													
														$directorio = '../../include/certificados';
                            $sinpermi = $directorio."/".$id2."-".$id1.".pdf";     
                            chmod($sinpermi, 0000);																							
												?>
                        <div class="row">
                            <form class="col s12" method="post" action="" enctype="multipart/form-data">
                              
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>                                                          
                             <?php                                  
                                
                               if (isset($_POST['boton'])){
                                  $nombreArchivoId = $id2."-".$id1;
                                if($nombreArchivoId != ""){                                                             
                                  $formatos   = array('.pdf');
                                  $directorio = '../../include/certificados';  
                                  $nombreArchivo    = $_FILES['archivo']['name'];
                                  $nombreArchivo1    = $_FILES['archivo']['name'];
                                  $nombreTmpArchivo = $_FILES['archivo']['tmp_name'];
                                  $ext         = substr($nombreArchivo, strrpos($nombreArchivo, '.'));
                                  $nombreArchivoId = "$nombreArchivoId"."$ext";
                                  $directorio1 = $directorio."/".$nombreArchivoId;
                                    if(in_array($ext, $formatos)){
                                      //chmod($directorio1, 0777,true);
                                      if (move_uploaded_file($nombreTmpArchivo, "$directorio/$nombreArchivoId")){
                                        chmod($directorio1, 0000);
                                        //actualizo tabla indicando si archivo cargado
                                        $update = "UPDATE FOR_OTROS SET FO_TIPO = $id1, FO_ADJUNTO = 'SI' WHERE FO_ID = $id2";
                                        mysqli_query($cnn, $update);
                                        ?><script> M.toast({html: 'Archivo Cargado'});</script><?php                                        
                                      }else{
                                        chmod($directorio1, 0000);
                                        ?><script> M.toast({html: 'Archivo No Cargado, error de permisos'});</script><?php
                                      }
                                    }else{
                                      chmod($directorio1, 0000);
                                      ?><script> M.toast({html: 'Formato no aceptado'});</script><?php
                                    }
                                }
                             }
                              if(isset($_POST['boton1'])){
                                $directorio = '../../include/certificados';
                                $sinpermi = $directorio."/".$id2."-".$id1.".pdf";
                                chmod($sinpermi, 0755);
                                if (is_readable($sinpermi)) {
                                  $recarga = "../php/carga_certificado.php?tipo=".$id1."&id=".$id2;
                                  chmod($sinpermi, 0755);                                
                                  echo '<script type="text/javascript"> window.open("'.$sinpermi.'")</script>';                                
                                  echo '<script type="text/javascript"> window.location=("'.$recarga.'")</script>';                                
                                }else{
                                  ?><script> M.toast({html: 'Archivo no encontrado'});</script><?php
                                }
                                //chmod($sinpermi, 0000);
                              }
                            ?>                             
                             
                              <div class="file-field input-field col s8">
                                  <div class="btn">
                                    <span>Archivo</span>
                                    <input type="file" name="archivo">
                                  </div>
                                  <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" name="archivo" id="archivo" />                                   
                                  </div>                                  
                                </div>
                              <div class="col s2">
                                <button class="btn trigger" id="boton" type="submit" name="boton" value="Boton">Cargar</button> 
                              </div>
                              <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                              <div class="input-field col s4">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>                             
                              <div class="col s4">
                                <button class="btn trigger" type="submit" id="boton1" name="boton1" value="Bonto1">Ver Archivo</button>    
                              </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>       

        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
             /*   //Animaciones
                
                $(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();
                $("#rut_usuario").Rut({ 
                    on_error: function(){ 
                        Materialize.toast('Rut incorrecto', 4000);
                        $("#btn_usuario").attr("disabled","disabled");
                    },
                    on_success: function(){ 
                        $("#btn_usuario").removeAttr("disabled");
                    },
                    format_on: 'keyup'
                });             
*/
            });
        </script>
        
</html>
    </body>