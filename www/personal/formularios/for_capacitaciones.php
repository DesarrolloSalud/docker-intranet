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
        $id_formulario = 48;
        date_default_timezone_set("America/Santiago");
        $fecha_hoy = date("Y-m-d");
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
                    //reviso si tengo algun ot_extra en creacion
                    $consultaEncreacion = "SELECT FC_ID FROM FOR_CAPA WHERE (USU_RUT = '$Srut') AND (FC_ESTA = 'EN CREACION')";
                    $respuestaEnCreacion = mysqli_query($cnn, $consultaEncreacion);
                      if (mysqli_num_rows($respuestaEnCreacion) == 0){
                          //usuario no tiene ningun folio tomado
                          $consultaNuevoId = "SELECT FC_ID FROM FOR_CAPA ORDER BY FC_ID DESC";
                          $respuestaNuevoId = mysqli_query($cnn, $consultaNuevoId);
                          $AñoActual = date("Y");
                          if (mysqli_num_rows($respuestaNuevoId) == 0){
                              $NuevoID = 1;
                              $FolioUno = "INSERT INTO FOR_CAPA (FC_ID,DOC_ID,USU_RUT,FC_ESTA,FC_ANO,FC_FEC,FC_ADJUNTO) VALUES ($NuevoID,13,'$Srut', 'EN CREACION','$AñoActual','$fecha_hoy','NO')";
                              mysqli_query($cnn, $FolioUno);
                          }else{
                              $rowNuevoId = mysqli_fetch_row($respuestaNuevoId);
                              $UltimoID = $rowNuevoId[0];
                              $NuevoID = $UltimoID + 1;
                              $FolioUno = "INSERT INTO FOR_CAPA (FC_ID,DOC_ID,USU_RUT,FC_ESTA,FC_ANO,FC_FEC,FC_ADJUNTO) VALUES ($NuevoID,13,'$Srut', 'EN CREACION','$AñoActual','$fecha_hoy','NO')";
                              mysqli_query($cnn, $FolioUno);
                          }
                      }else{
                          $rowFolioUsado = mysqli_fetch_row($respuestaEnCreacion);
                          $NuevoID = $rowFolioUsado[0];
                      }  
                  }else{
                      //no tengo acceso
                      $accion = utf8_decode("ACCESO DENEGADO");
                      $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha_hoy', '$hora')";
                      mysqli_query($cnn, $insertAcceso);
                      header("location: ../error.php");
                  }
              }else{
                  //si formulario no activo
                  $accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
                  $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha_hoy', '$hora')";
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
        <script type="text/javascript" src="../../include/js/moment.js"></script>
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
            function Cargar(){
                $("#fecha_dia").attr("disabled","disabled");
                $("#Finicio").attr("disabled","disabled");
                $("#Ftermino").attr("disabled","disabled");
                $("#dias").attr("disabled","disabled");
            }
            function UnDia(){
                $("#fecha_dia").removeAttr("disabled");
                $("#masdeuno").attr("disabled","disabled");
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
      </br>
      </br>
      </br>
      <div class="container">
          <div class="section">
              <div class="row">
                  <div class="col s12 center block" style="background-color: #ffffff">
                      <h4 class="light">Permiso Capacitaciones</h4>
                       <form name="form" class="col s12" method="post" id="formSolPermi">  
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
                            <div class="col s12" align="left"><h6>Solicita autorización para los siguientes dias:</h6></div>
                            </br>
                            </br>
                            </br>
                            <table class="col 12">
                                <tbody>
                                   <tr>
                                        <td  style="text-align: left;">
                                          <label>
                                            <input name="dia" type="radio" value="1" id="undia" onclick="UnDia();"/>
                                            <span>1 Dia</span>
                                          </label>        
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" name="fecha_dia" id="fecha_dia" class="datepicker" onchange="FechaDia();" placeholder="Fecha" required> 
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  style="text-align: left;">
                                          <label>
                                            <input name="dia" type="radio" value="mas" id="masdeuno" onclick="MasdeUno();"/>
                                            <span>Mas de 1 Dia</span>
                                          </label>       
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" name="dias" id="dias" class="validate" placeholder=""  onkeypress="return soloNumeros(event)" onblur="MostrarFinicio();" required>
                                                <input type="text" name="dias_pendientes" id="dias_pendientes" class="validate" style="display: none">
                                                <label for="icon_prefix">Cantidad de dias</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-field col s12">
                                                <input type="text" class="datepicker" name="Finicio" id="Finicio" placeholder="Fecha de Inicio" required onchange="ValidoFechaINI();" required> 
                                            </div> 
                                        </td>
                                        <td>
                                            <div class="input-field col s10">
                                                <input type="text" class="datepicker" name="Ftermino" id="Ftermino" placeholder="Fecha de Termino" required>
                                                <input type="text" class="datepicker" name="Ftermino2" id="Ftermino2" class="validate" style="display: none">
                                            </div>
                                        </td>
                                    </tr>                                                               
                                </tbody>
                       </form>
                  </div>
              </div>
          </div>
      </div>        
  </body>
</html>