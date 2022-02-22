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
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $id_formulario = 44;
        $rut = $_POST['rut_usuario'];
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
        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () { 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
            }); 
            function Observar(id){
                var lm_id = id;
                //Materialize.toast('PERMISO VISTO ID: ' + lm_id , 4000);
                $.post( "../php/observar_licencia.php", { "id" : lm_id }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "licencias_funcionario.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
        </script>
    </head>
    <body>
         <!-- llamo el nav que tengo almacenado en un archivo -->
        <?php require_once('../estructura/nav_personal.php');?>
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">Licencias Medicas por Funcionario</h4>
                        <div class="row">
                          <form name="form" class="col s12" method="post" action="licencias_funcionario.php">
                              <div class="input-field col s6">
                                  <i class="mdi-action-account-circle prefix"></i>
                                  <input id="rut_usuario" type="text" class="validate" name="rut_usuario" placeholder="" value="<?php echo $usu_rut ?>">
                                  <label for="icon_prefix">RUT</label>
                              </div>
                              <div class="input-field col s6">
                                  <button class="btn trigger" type="submit" name="buscar" id="buscar" value="buscar">Buscar</button>
                              </div>
                          </form>
                          <?php
                            if($_POST['buscar'] == "buscar"){
                              $licencias = "SELECT LM_NUM,LM_ESTA,LM_TIPO,DATE_FORMAT(LM_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(LM_FEC_FIN,'%d-%m-%Y'),LM_DIAS,LM_TE,LM_TR,LM_ID FROM LICENCIAS_MEDICAS WHERE (USU_RUT = '$rut') ORDER BY LM_FEC_INI ASC";
                              $respuesta = mysqli_query($cnn, $licencias);
                              echo '<table class="responsive-table boradered striped">';
													      echo '<thead>';
														      echo '<tr>';
															      echo '<th>LICENCIA</th>';
                                    echo '<th>TIPO</th>';
                                    echo '<th>DIAS</th>';
																    echo '<th>FECHA INICIO</th>';
																    echo '<th>FECHA TERMINO</th>';
                                    echo '<th>ESTADO</th>';
                                    echo '<th>ESTIMADO</th>';
                                    echo '<th>RECUPERADO</th>';
                                    echo '<th>ACCIONES</th>';
															    echo '</tr>';
															    echo '<tbody>';
                                  while ($rowlm = mysqli_fetch_array($respuesta)){
                                    echo "<tr>";
                                      echo "<td>".$rowlm[0]."</td>";
                                      echo "<td>".$rowlm[2]."</td>";
                                      echo "<td>".$rowlm[5]."</td>";
                                      echo "<td>".$rowlm[3]."</td>";
                                      echo "<td>".$rowlm[4]."</td>";
                                      echo "<td>".$rowlm[1]."</td>";
                                      echo "<td>$ ".$rowlm[6]."</td>";
                                      echo "<td>$ ".$rowlm[7]."</td>";
                                      if($rowlm[1] == "PAGO PENDIENTE" ){
                                        echo '<td><button class="btn trigger" name="observacion" onclick="Observar('.$rowlm[8].');" id="observacion" type="button">OBSERVAR</button></td>';
                                      }else{
                                        echo '<td></td>';
                                      }
                                    echo "</tr>";
                                  }
                              		echo '</tbody>';
														    echo '</thead>';
													    echo '</table>';
                            }
                          ?>
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
                //Animaciones
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
        
</html>
</body>