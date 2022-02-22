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
        $id_formulario = 42;
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
            function Detalle(rut,fec){
                var rut = rut;
                var FecIni = fec;
                window.open('http://200.68.34.158/personal/pdf/detalle_licencias.php?rut='+rut+'&inicio='+FecIni,'_blank');
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
                        <h4 class="light">Licencias Medicas</h4>
                        <div class="row">
												  <table class="responsive-table boradered striped">
													  <thead>
														  <tr>
															  <th>RUT</th>
																<th>FUNCIONARIO</th>
																<th>TOTAL DIAS</th>
                                <th>ACCIONES</th>
															</tr>
															<tbody>
                              <?php
                              $usuario = "SELECT USU_RUT,USU_NOM,USU_APM,USU_APP FROM USUARIO ORDER BY EST_ID ASC";
                              $rs = mysqli_query($cnn, $usuario);
                              list($año_actual, $mes_actual, $dia_actual) = split('[-]', $fecha);
        											$FecIni = ($año_actual - 2)."-".$mes_actual."-".$dia_actual;
                              while ($row = mysqli_fetch_array($rs)){
                                echo "<tr>";
                                  echo "<td>".$row[0]."</td>";
                                  echo "<td>".utf8_encode($row[1])." ".utf8_encode($row[2])." ".utf8_encode($row[3])."</td>"; 
                                  $licencias = "SELECT LM_FEC_INI,LM_FEC_FIN,LM_DIAS,LM_NUM,LM_TE,LM_TR,LM_ESTA,LM_TIPO,DATE_FORMAT(LM_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(LM_FEC_FIN,'%d-%m-%Y') FROM LICENCIAS_MEDICAS WHERE (USU_RUT = '$row[0]') AND (LM_FEC_FIN >= '$FecIni') AND ((LM_TIPO NOT LIKE '%Enfermedad Grave Hijo Menor%') AND (LM_TIPO != 'Prenatal - Postnatal') AND (LM_TIPO NOT LIKE '%Del Embarazo%')) ORDER BY LM_FEC_INI ASC";
                                  $respuesta = mysqli_query($cnn, $licencias);
                                  $TotalDias = 0;
                                  if (mysqli_num_rows($respuesta) == 0){
                                    $TotalDias = 0;
                                  }elseif(mysqli_num_rows($respuesta) == 1){
                                    $rowlm = mysqli_fetch_row($respuesta);
                                    $TotalDias = $rowlm[2];
                                  }elseif(mysqli_num_rows($respuesta) > 1){
                                    while ($rowlm = mysqli_fetch_array($respuesta)){
                                      $fec_ini = $rowlm[0];
                                      $fec_fin = $rowlm[1];
                                      $dias = $rowlm[2];
                                      if($fec_ini < $FecIni && $fec_fin > $FecIni){
                                        $date1 = new DateTime($FecIni);
                                        $date2 = new DateTime($fec_fin);
                                        $diff = $date1->diff($date2);
                                        $diferencia = $diff->days;
                                        $TotalDias = $TotalDias + $diferencia;
                                      }elseif($fec_ini >= $FecIni && $fec_fin <= $fecha){
                                        $TotalDias = $TotalDias + $dias;                                   
                                      }elseif($fec_fin > $fecha){
                                        $date1 = new DateTime($fec_ini);
                                        $date2 = new DateTime($fecha);
                                        $diff = $date1->diff($date2);
                                        $diferencia = $diff->days;
                                        $TotalDias = $TotalDias + $diferencia;
                                      }
                                    }
                                  }
                                  echo "<td>".$TotalDias."</td>"; ?>
                                  <td><button class='btn trigger' name='detalle$row[0]' id='detalle$row[0]' type='button' onclick='Detalle("<?php echo $row[0]; ?>","<?php echo $FecIni; ?> ");'>Detalle</button></td>
                                <?php echo "</tr>";
                              }
                              ?>
															</tbody>
														</thead>
													</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>       

        <script type="text/javascript" src="../../include/js/jquery.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>        
</html>
</body>