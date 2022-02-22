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
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $id_formulario = 13;
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
                    $inicio = $_GET['fecha_inicio'];
                    $fin = $_GET['fecha_fin'];
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
        <link type="text/css" rel="stylesheet" href="../../include/css/materialize.clockpicker.min.css" />
        <link href="../../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <style type="text/css">
            body{
                background-image: url("../../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }
						td,th{
								text-align: center;
						}
        </style>
        <script type="text/javascript" src="../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
                $('.tabs').tabs();
                $('.modal').modal();
            });
            function Cargar(){
              $("#fecha_fin").attr("disabled","disabled"); 
              $("#guardar").attr("disabled","disabled"); 
              $("#recibidos_per").css("display", "none");
              $("#recibidos_singose").css("display", "none");
              $("#recibidos_ot").css("display", "none");
              $("#recibidos_cometido").css("display", "none");
              $("#recibidos_acufer").css("display", "none");
              $("#recibidos_otros").css("display", "none");
              $("#permisos").css("display", "none");
              $("#singose").css("display", "none");
              $("#ot").css("display", "none");
              $("#cometido").css("display", "none");
              $("#acufer").css("display", "none");
              $("#otros").css("display", "none");
            }
            function MostrarFechaFin(){
              $("#fecha_fin").removeAttr("disabled");
            }
            function MostrarBuscar(){
              $("#guardar").removeAttr("disabled");
            }
            function Enviados(){
              $("#recibidos_per").css("display", "none");
              $("#recibidos_singose").css("display", "none");
              $("#recibidos_ot").css("display", "none");
              $("#recibidos_cometido").css("display", "none");
              $("#recibidos_acufer").css("display", "none");
              $("#recibidos_otros").css("display", "none");
            }
            function Recibidos(){
              $("#permisos").css("display", "none");
              $("#singose").css("display", "none");
              $("#ot").css("display", "none");
              $("#cometido").css("display", "none");
              $("#acufer").css("display", "none");
              $("#otros").css("display", "none");
            }
            function ImprimirSP(id){
                var idSP = id;
                window.open('http://200.68.34.158/personal/pdf/sol_permi.php?id='+idSP,'_blank');
            }
            function ImprimirOT(id){
                var idOT = id;
                window.open('http://200.68.34.158/personal/pdf/ot_extra.php?id='+idOT,'_blank');
            }
            function ImprimirSAF(id){
                var idSAF = id;
                window.open('http://200.68.34.158/personal/pdf/saf.php?id='+idSAF,'_blank');
            }
            function ImprimirSPR(id){
                var idSPR = id;
                window.open('http://200.68.34.158/personal/pdf/sin_goce.php?id='+idSPR,'_blank');
            }
            function ImprimirCO(id){
                var idCO = id;
                window.open('http://200.68.34.158/personal/pdf/cometido.php?id='+idCO,'_blank');
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
        <!-- inicio contenido pagina -->
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                      <h4 class="light">Mis Formularios</h4>
                      <form action="formularios.php" name="form" class="col s12" method="get">
                        <div class="row">
                          <br><br>
                        </div>
                        <div class="input-field col s4">
                          <?php 
                          if (empty($inicio) && empty($fin)){
                            ?><input type="text" class="datepicker" name="fecha_inicio" id="fecha_inicio" onchange="MostrarFechaFin();" placeholder="" required><?php
                          }else{
                            ?><input type="text" name="fecha_inicio" id="fecha_inicio" placeholder="" value="<?php echo $inicio;?>"> <?php
                          }
                          ?>
                          <label for='fecha_inicio'>Fecha Inicio</label>
                        </div> 
                        <div class="input-field col s4">
                          <?php
                          if (empty($inicio) && empty($fin)){
                            ?><input type="text" class="datepicker" name="fecha_fin" id="fecha_fin" onchange="MostrarBuscar();" placeholder="" value="<?php echo $fin;?>" required><?php
                          }else{
                            ?><input type="text" name="fecha_fin" id="fecha_fin" placeholder="" value="<?php echo $fin;?>"> <?php
                          }
                          ?>
                          <label for='fecha_fin'>Fecha Fin</label>
                        </div>
                        <div class="input-field col s4">
                           <button id="guardar" type="submit" class="btn trigger" name="guardar" value="Guardar" >Buscar</button>
                        </div>
                        <div class="col s12">
                          <ul class="tabs">
                            <li class="tab col s6"><a class="active" href="#enviados" onclick="Enviados();">ENVIADOS</a></li>
                            <li class="tab col s6"><a href="#recibidos" onclick="Recibidos();">RECIBIDOS</a></li>
                          </ul>
                        </div>
                        <div id="enviados" class="col s12">
                          <ul class="tabs">
                            <li class="tab col s2"><a href="#permisos">PERMISOS</a></li>
                            <li class="tab col s2"><a href="#singose">S/ GOSE REMUN</a></li>
                            <li class="tab col s2"><a href="#ot">O.T. EXTRA</a></li>
                            <li class="tab col s2"><a href="#cometido">COMETIDO</a></li>
                            <li class="tab col s2"><a href="#acufer">ACU. FERIADO</a></li>
                            <li class="tab col s2"><a href="#otros">OTROS</a></li>
                          </ul>
                        </div>
                        <div id="permisos" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ESTADO</th>';
                                  echo '<th>ACCIONES</th>';
                                  echo '<th></th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $MisPermisosPedidos = "SELECT SOL_PERMI.SP_ID,DOCUMENTO.DOC_NOM,SOL_PERMI.SP_MOT,SOL_PERMI.SP_ESTA,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_JOR,SOL_PERMI.DOC_ID,SOL_PERMI.SP_COM,SOL_PERMI.SP_CANT_DC,SOL_PERMI.SP_HOR_INI,SOL_PERMI.SP_HOR_FIN,SOL_PERMI.SP_TIPO,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM SOL_PERMI, DOCUMENTO, USUARIO WHERE (SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID) AND (USUARIO.USU_RUT = SOL_PERMI.USU_RUT_JD) AND (SOL_PERMI.USU_RUT = '$Srut') AND (SOL_PERMI.SP_FEC BETWEEN '$inicio' AND '$fin') ORDER BY DOCUMENTO.DOC_NOM,SOL_PERMI.SP_FEC ASC";
                                  $respuesta = mysqli_query($cnn, $MisPermisosPedidos);
                                  while ($row_rs = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
                                    echo "<tr>";
                                      echo "<td>".$row_rs[0]."</td>";
                                      echo "<td>".utf8_encode($row_rs[1])."</td>";
                                      echo "<td>".utf8_encode($row_rs[2])."</td>";
                                      echo "<td>".utf8_encode($row_rs[3])."</td>";
                                      //echo "<td><a class='waves-effect waves-light btn' href='#MIPERMISO".$row_rs[0]."'>Detalle</a></td>";
                                      echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MIPERMISO".$row_rs[0]."'>Detalle</a></td>";
                                      if($row_rs[3] == "AUTORIZADO DIR"){
                                          echo "<td><button class='btn trigger' name='imprimirMiSP".$row_rs[0]."' id='imprimirMiSP".$row_rs[0]."' type='button' onclick='ImprimirSP(".$row_rs[0].");'>Imprimir</button></td>";
                                      }else{
                                          echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                      }
                                    echo "</tr>";
                                    //Modal detalle mispermiso
                                    ?>
                                    <div id="MIPERMISO<?php echo $row_rs[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                          echo '<h4>Detalle de Documento</h4>';
                                          echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rs[0]).' <b>TIPO : </b>'.utf8_encode($row_rs[1]).'</p>';
                                          echo '<p><b>DIRIGIDO A : </b>'.utf8_encode($row_rs[14]).' '.utf8_encode($row_rs[15]).' '.utf8_encode($row_rs[16]).'</p>';
                                          if ($row_rs[13] != "HORAS"){
                                              echo '<p><b>DIAS : </b>'.utf8_encode($row_rs[4]).' <b>DESDE EL : </b>'.$row_rs[5].' <b>HASTA EL : </b>'.$row_rs[6].' <b>JORNADA : </b>'.utf8_encode($row_rs[7]).' </p>';
                                          }else{
                                              echo '<p><b>HORAS : </b>'.$row_rs[10].' <b>EL DIA : </b>'.$row_rs[5].' <b>DESDE LAS : </b>'.$row_rs[11].' <b>HASTA LAS : </b>'.$row_rs[12].' </p>';
                                          }
                                          echo '<p><b>MOTIVO DEL PERMISO : </b>'.utf8_encode($row_rs[2]).'</p>';
                                          //CARGAR HISTO PERMISO
                                          $DetalleMiPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rs[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = ".$row_rs[8].")";
                                          $respuestaDetalleMiPermiso = mysqli_query($cnn, $DetalleMiPermiso);
                                          //recorrer los registros
                                          echo '<h5>SEGUIMIENTO</h5>';
                                          while ($row_rsDMP = mysqli_fetch_array($respuestaDetalleMiPermiso, MYSQLI_NUM)){
                                              echo '<p><b>FECHA : </b>'.$row_rsDMP[0].'     <b>HORA : </b>'.$row_rsDMP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMP[2]).'</p>';
                                          }
                                          if ($row_rs[3] == "RECHAZADO J.D." || $row_rs[3] == "RECHAZADO DIR"){
                                              echo '<h6><b>MOTIVO DEL RECHAZO : </b>'.utf8_encode($row_rs[9]).'</h6>';
                                          }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }
                          ?>
                        </div>
                        <div id="singose" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ESTADO</th>';
                                  echo '<th>ACCIONES</th>';
                                  echo '<th></th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $MIS_SGR = "SELECT S.SPR_ID,D.DOC_NOM,S.SPR_MOT,S.SPR_ESTA,S.SPR_NDIA,DATE_FORMAT(S.SPR_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(S.SPR_FEC_FIN,'%d-%m-%Y'),S.SPR_COM,S.DOC_ID FROM SOL_PSGR S, DOCUMENTO D WHERE (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = '$Srut') AND (S.SPR_FEC BETWEEN '$inicio' AND '$fin') ORDER BY S.SPR_FEC";
                                  $RespuestaSPR = mysqli_query($cnn, $MIS_SGR);
                                  //echo $MIS_SAF;
                                  while ($row_MiSPR = mysqli_fetch_array($RespuestaSPR, MYSQLI_NUM)){
                                    echo "<tr>";
                                      echo "<td>".$row_MiSPR[0]."</td>";
                                      echo "<td>".utf8_encode($row_MiSPR[1])."</td>";
                                      echo "<td>".utf8_encode($row_MiSPR[2])."</td>";
                                      echo "<td>".utf8_encode($row_MiSPR[3])."</td>";
                                      echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MISPR".$row_MiSPR[0]."'>Detalle</a></td>";
                                      if($row_MiSPR[3] == "AUTORIZADO DIR SALUD"){
                                          echo "<td><button class='btn trigger' name='imprimir' id='imprimir' type='button' onclick='ImprimirSPR(".$row_MiSPR[0].");'>Imprimir</button></td>";
                                      }else{
                                          echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                      }
                                    echo "</tr>";
                                    //Modal detalle mispermiso
                                    ?>
                                    <div id="MISPR<?php echo $row_MiSPR[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                          echo '<h4>Detalle de Documento</h4>';
                                          echo '<h5>PERMISO SIN GOCE DE REMUNERACION</h5>';
                                          echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_MiSPR[0]).' <b>TIPO : </b>'.utf8_encode($row_MiSPR[1]).'</p>';
                                          echo '<p><b>DIAS : </b>'.$row_MiSPR[4].' <b>DESDE EL : </b>'.$row_MiSPR[5].' <b>HASTA EL : </b>'.$row_MiSPR[6].'</p>';
                                          echo '<p><b>MOTIVO DE PERMISO SIN GOCE DE REMUNERACION : </b>'.utf8_encode($row_MiSPR[2]).'</p>';
                                          //CARGAR HISTO PERMISO
                                          $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_MiSPR[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = ".$row_MiSPR[8].")";
                                          //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                          $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                          //recorrer los registros
                                          echo '<h5>SEGUIMIENTO</h5>';
                                          while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                              echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                          }
                                          if($row_MiSPR[3] == "RECHAZADO DIR"){
                                              echo '<p><b>MOTIVO RECHAZO : </b>'.utf8_encode($row_MiSPR[7]).'</p>';
                                          }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }
                          ?>
                        </div>
                        <div id="ot" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ESTADO</th>';
                                  echo '<th>ACCIONES</th>';
                                  echo '<th></th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $MisOrdenesdeTrabajo = "SELECT OT_EXTRA.OE_ID,DOCUMENTO.DOC_NOM,OT_EXTRA.OE_TRAB,OT_EXTRA.OE_ESTA,OT_EXTRA.OE_CANT_CANCE,OT_EXTRA.OE_CANT_DC,OT_EXTRA.OE_COM,OTE_PROGRAMA.OP_NOM FROM OT_EXTRA INNER JOIN DOCUMENTO ON OT_EXTRA.DOC_ID = DOCUMENTO.DOC_ID LEFT JOIN OTE_PROGRAMA ON OT_EXTRA.OE_PROGRAMA = OTE_PROGRAMA.OP_ID  WHERE (OT_EXTRA.USU_RUT = '$Srut') AND (OT_EXTRA.OE_FEC BETWEEN '$inicio' AND '$fin') AND (OT_EXTRA.OE_ESTA != 'EN CREACION') ORDER BY OT_EXTRA.OE_FEC";
                                  $respuestaOT = mysqli_query($cnn, $MisOrdenesdeTrabajo);
                                  //recorrer los registros
                                  while ($row_rOT = mysqli_fetch_array($respuestaOT, MYSQLI_NUM)){
                                    echo "<tr>";
                                      echo "<td>".$row_rOT[0]."</td>";
                                      echo "<td>".utf8_encode($row_rOT[1])."</td>";
                                      echo "<td>".utf8_encode($row_rOT[2])."</td>";
                                      echo "<td>".utf8_encode($row_rOT[3])."</td>";
                                      echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MIORDEN".$row_rOT[0]."'>Detalle</a></td>";
                                      if($row_rOT[3] == "V.B. DIR SALUD"){
                                          echo "<td><button class='btn trigger' name='imprimir' id='imprimir' type='button' onclick='ImprimirOT(".$row_rOT[0].");'>Imprimir</button></td>";
                                      }else{
                                          echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                      }
                                    echo "</tr>";
                                    ?>
                                    <div id="MIORDEN<?php echo $row_rOT[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                        echo '<h4>Detalle de Documento</h4>';
                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rOT[0]).' <b>TIPO : </b>'.utf8_encode($row_rOT[1]).'</p>';
                                        echo '<p><b>CUMPLIR EL TRABAJO DE : </b>'.utf8_encode($row_rOT[2]).'</p>';
                                        echo '<p><b>CON CARGO AL PROGRAMA : </b>'.utf8_encode($row_rOT[7]).'</p>';
                                        //CARGO DETALLE DE HORAS
                                        $DetalleMiOrden = "SELECT DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN,OTE_TIPO FROM OTE_DETALLE WHERE (OE_ID = ".$row_rOT[0].") ORDER BY OTE_TIPO,OTE_DIA ASC ";
                                        $RespMiOrden = mysqli_query($cnn,$DetalleMiOrden);
                                        while ($row_rsRMO = mysqli_fetch_array($RespMiOrden, MYSQLI_NUM)){
                                            echo '<p><b>DIA : </b>'.$row_rsRMO[0].'     <b>HORA INICIO : </b>'.$row_rsRMO[1].'      <b>HORA FIN : </b>'.utf8_encode($row_rsRMO[2]).'      <b>TIPO : </b>'.utf8_encode($row_rsRMO[3]).'</p>';
                                        }
                                        echo '<p><b>HORAS CANCELADAS : </b>'.$row_rOT[4].' <b>HORAS DESCANSO COMPLEMENTARIO : </b>'.$row_rOT[5].'</p>';
                                        //CARGAR HISTO PERMISO
                                        $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rOT[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = 5)";
                                        $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                        //recorrer los registros
                                        echo '<h5>SEGUIMIENTO</h5>';
                                        while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                            echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                        }
                                        if ($row_rOT[3] == "RECHAZADO J.D." || $row_rOT[3] == "RECHAZADO DIR" || $row_rOT[3] == "RECHAZADO DIR SALUD"){
                                            echo '<h6><b>MOTIVO DEL RECHAZO : </b>'.utf8_encode($row_rOT[6]).'</h6>';
                                        }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }  
                          ?>
                        </div>
                        <div id="cometido" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ESTADO</th>';
                                  echo '<th>ACCIONES</th>';
                                  echo '<th></th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $MisCometidos = "SELECT COME_PERMI.CO_ID,DOCUMENTO.DOC_NOM,COME_PERMI.CO_MOT,COME_PERMI.CO_ESTA,COME_PERMI.CO_VIA,COME_PERMI.CO_DIA,COME_PERMI.CO_PAS,COME_PERMI.CO_COM,COME_PERMI.CO_PEA,COME_PERMI.CO_PAR,COME_PERMI.CO_DES FROM COME_PERMI INNER JOIN DOCUMENTO ON COME_PERMI.DOC_ID = DOCUMENTO.DOC_ID WHERE (COME_PERMI.USU_RUT = '$Srut') AND (COME_PERMI.CO_FEC BETWEEN '$inicio' AND '$fin') AND (COME_PERMI.CO_ESTA != 'EN CREACION') ORDER BY COME_PERMI.CO_FEC";
                                  $respuestaCO = mysqli_query($cnn, $MisCometidos);
                                  //recorrer los registros
                                  while ($row_CO = mysqli_fetch_array($respuestaCO, MYSQLI_NUM)){
                                    echo "<tr>";
                                      echo "<td>".$row_CO[0]."</td>";
                                      echo "<td>".utf8_encode($row_CO[1])."</td>";
                                      echo "<td>".utf8_encode($row_CO[2])."</td>";
                                      echo "<td>".utf8_encode($row_CO[3])."</td>";
                                      echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MICOME".$row_CO[0]."'>Detalle</a></td>";
                                      if($row_CO[3] == "AUTORIZADO DIR"){
                                          echo "<td><button class='btn trigger' name='imprimirCO' id='imprimirCO' type='button' onclick='ImprimirCO(".$row_CO[0].");'>Imprimir</button></td>";
                                      }else{
                                          echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                      }
                                    echo "</tr>";
                                    ?>
                                    <div id="MICOME<?php echo $row_CO[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                        echo '<h4>Detalle de Documento</h4>';
                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_CO[0]).' <b>TIPO : </b>'.utf8_encode($row_CO[1]).'</p>';
                                        //echo '<p><b>DIAS : </b>'.utf8_encode($row_rs[4]).' <b>DESDE EL : </b>'.$row_rs[5].' <b>HASTA EL : </b>'.$row_rs[6].' <b>JORNADA : </b>'.utf8_encode($row_rs[7]).' </p>';
                                        echo '<p><b>MOTIVO : </b>'.utf8_encode($row_CO[2]).'</p>';
                                        //CARGO DETALLE DE COMETIDO
                                        $DetalleMiCome = "SELECT DATE_FORMAT(CD_DIA,'%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID = ".$row_CO[0].") ORDER BY CD_DIA ASC ";
                                        $RespMiCome = mysqli_query($cnn,$DetalleMiCome);
                                        while ($row_CD = mysqli_fetch_array($RespMiCome, MYSQLI_NUM)){
                                            echo '<p><b>DIA : </b>'.$row_CD[0].'     <b>HORA INICIO : </b>'.$row_CD[1].'      <b>HORA FIN : </b>'.utf8_encode($row_CD[2]).'      <b>PORCENTAJE : </b>'.utf8_encode($row_CD[3]).'</p>';
                                        }
                                        //CARGAR HISTO PERMISO
                                        $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_CO[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = 8)";
                                        $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                        //recorrer los registros
                                        echo '<h5>SEGUIMIENTO</h5>';
                                        while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                            echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                        }
                                        if ($row_CO[3] == "RECHAZADO J.D." || $row_CO[3] == "RECHAZADO DIR"){
                                            echo '<h6><b>MOTIVO DEL RECHAZO : </b>'.utf8_encode($row_CO[6]).'</h6>';
                                        }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }  
                          ?>
                        </div>
                        <div id="acufer" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ESTADO</th>';
                                  echo '<th>ACCIONES</th>';
                                  echo '<th></th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $MIS_SAF = "SELECT S.SAF_ID,D.DOC_NOM,S.SAF_MOT,S.SAF_ESTA,P.SP_CANT_DIA,DATE_FORMAT(P.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(P.SP_FEC_FIN,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_FIN,'%d-%m-%Y'),P.SP_COM,S.SAF_CANT_DIA,S.SAF_ANO_ACT,S.SAF_ANO_SIG,S.SAF_MOT,S.SAF_ESTA,R.RSP_RESOL,S.DOC_ID FROM SOL_ACU_FER S, RES_SOL_PERMI R, SOL_PERMI P, DOCUMENTO D WHERE (S.RSP_ID = R.RSP_ID) AND (S.SP_ID = P.SP_ID) AND (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = '$Srut') AND (S.SAF_FEC BETWEEN '$inicio' AND '$fin') ORDER BY S.SAF_FEC";
                                  $RespuestaSAF = mysqli_query($cnn, $MIS_SAF);
                                  while ($row_MiSAF = mysqli_fetch_array($RespuestaSAF, MYSQLI_NUM)){
                                    echo "<tr>";
                                      echo "<td>".$row_MiSAF[0]."</td>";
                                      echo "<td>".utf8_encode($row_MiSAF[1])."</td>";
                                      echo "<td>".utf8_encode($row_MiSAF[2])."</td>";
                                      echo "<td>".utf8_encode($row_MiSAF[3])."</td>";
                                      echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MISAF".$row_MiSAF[0]."'>Detalle</a></td>";
                                      if($row_MiSAF[3] == "AUTORIZADO DIR"){
                                          echo "<td><button class='btn trigger' name='imprimir' id='imprimir' type='button' onclick='ImprimirSAF(".$row_MiSAF[0].");'>Imprimir</button></td>";
                                      }else{
                                          echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                      }
                                    echo "</tr>";
                                    ?>
                                    <div id="MISAF<?php echo $row_MiSAF[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                        echo '<h4>Detalle de Documento</h4>';
                                        echo '<h5>FERIADO LEGAL SOLICITADO</h5>';
                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_MiSAF[0]).' <b>TIPO : </b>'.utf8_encode($row_MiSAF[1]).'</p>';
                                        echo '<p><b>DIAS : </b>'.$row_MiSAF[4].' <b>DESDE EL : </b>'.$row_MiSAF[5].' <b>HASTA EL : </b>'.$row_MiSAF[6].'</p>';
                                        echo '<h5>RESOLUCION</h5>';
                                        echo '<p><b>RESOLUCION : </b>'.utf8_encode($row_MiSAF[15]).'</p>';
                                        echo '<p><b>FECHA SUGERIDA : </b>'.$row_MiSAF[7].' <b>HASTA EL : </b>'.$row_MiSAF[8].' </p>';
                                        echo '<p><b>MOTIVO DE REAGENDAR FERIADO : </b>'.utf8_encode($row_MiSAF[9]).'</p>';
                                        echo '<h5>ACUMULACION DE FERIADO</h5>';
                                        echo '<p><b>DIAS</b>'.$row_MiSAF[10].' <b>DEL AÑO : </b>'.$row_MiSAF[11].' <b>PARA EL : </b>'.$row_MiSAF[12].' </p>';
                                        echo '<p><b>MOTIVO DE ACUMULACION FERIADO : </b>'.utf8_encode($row_MiSAF[13]).'</p>';
                                        //CARGAR HISTO PERMISO
                                        $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_MiSAF[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = ".$row_MiSAF[16].")";
                                        //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                        $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                        //recorrer los registros
                                        echo '<h5>SEGUIMIENTO</h5>';
                                        while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                            echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                        }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }  
                          ?>
                        </div>
                        <div id="otros" class="col s12">
                        </div>
                        <div id="recibidos" class="col s12">
                          <ul class="tabs">
                            <li class="tab col s2"><a href="#recibidos_per">PERMISOS</a></li>
                            <li class="tab col s2"><a href="#recibidos_singose">S/ GOSE REMUN</a></li>
                            <li class="tab col s2"><a href="#recibidos_ot">O.T. EXTRA</a></li>
                            <li class="tab col s2"><a href="#recibidos_cometido">COMETIDO</a></li>
                            <li class="tab col s2"><a href="#recibidos_acufer">ACU. FERIADO</a></li>
                            <li class="tab col s2"><a href="#recibidos_otros">OTROS</a></li>
                          </ul>
                        </div>
                        <div id="recibidos_per" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>FUNCIONARIO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ACCIONES</th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $MisPermisosSolicitadosJefe = "SELECT SOL_PERMI.SP_ID,DOCUMENTO.DOC_NOM,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,SOL_PERMI.SP_MOT,SOL_PERMI.SP_ESTA,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_JOR,SOL_PERMI.DOC_ID, USUARIO.USU_RUT,SOL_PERMI.SP_CANT_DC,SOL_PERMI.SP_HOR_INI,SOL_PERMI.SP_HOR_FIN,SOL_PERMI.SP_TIPO FROM SOL_PERMI, DOCUMENTO, USUARIO WHERE (SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID) AND (SOL_PERMI.USU_RUT = USUARIO.USU_RUT) AND ((SOL_PERMI.USU_RUT_JD = '$Srut') OR (SOL_PERMI.USU_RUT_DIR = '$Srut')) AND (SOL_PERMI.SP_FEC BETWEEN '$inicio' AND '$fin') ORDER BY DOCUMENTO.DOC_NOM,SOL_PERMI.SP_FEC ASC";
                                  $respuestaPermiJefes = mysqli_query($cnn, $MisPermisosSolicitadosJefe);
                                  //recorrer los registros 
                                  //echo $MisPermisosSolicitadosJefe;
                                  while ($row_rsPJ = mysqli_fetch_array($respuestaPermiJefes, MYSQLI_NUM)){
                                    echo "<tr>";
                                        echo "<td>".$row_rsPJ[0]."</td>";
                                        echo "<td>".utf8_encode($row_rsPJ[1])."</td>";
                                        echo "<td>".utf8_encode($row_rsPJ[2])." ".utf8_encode($row_rsPJ[3])." ".utf8_encode($row_rsPJ[4])."</td>";
                                        echo "<td>".utf8_encode($row_rsPJ[5])."</td>";
                                        echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#VERPERMISOJD".$row_rsPJ[0]."'>Detalle</a></td>";
                                    echo "</tr>";
                                    //Modal detalle mispermiso
                                    ?>
                                    <div id="VERPERMISOJD<?php echo $row_rsPJ[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                        echo '<h4>Detalle de Documento</h4>';
                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rsPJ[0]).' <b>TIPO : </b>'.utf8_encode($row_rsPJ[1]).'</p>';
                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsPJ[2]).' '.utf8_encode($row_rsPJ[3]).' '.utf8_encode($row_rsPJ[4]);
                                        if ($row_rsPJ[16] != "HORAS"){
                                            echo '<p><b>DIAS : </b>'.utf8_encode($row_rsPJ[7]).' <b>DESDE EL : </b>'.$row_rsPJ[8].' <b>HASTA EL : </b>'.$row_rsPJ[9].' <b>JORNADA : </b>'.utf8_encode($row_rsPJ[10]).' </p>';
                                        }else{
                                            echo '<p><b>HORAS : </b>'.$row_rsPJ[13].' <b>EL DIA : </b>'.$row_rsPJ[8].' <b>DESDE LAS : </b>'.$row_rsPJ[14].' <b>HASTA LAS : </b>'.$row_rsPJ[15].' </p>';
                                        }
                                        echo '<p><b>MOTIVO DEL PERMISO : </b>'.utf8_encode($row_rsPJ[5]).'</p>';
                                        //CARGAR HISTO PERMISO
                                        $DetalleSuPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsPJ[0].") AND (USU_RUT = '".$row_rsPJ[12]."') AND (DOC_ID = ".$row_rsPJ[11].")";
                                        $respuestaDetalleSuPermiso = mysqli_query($cnn, $DetalleSuPermiso);
                                        //recorrer los registros
                                        echo '<h5>SEGUIMIENTO</h5>';
                                        while ($row_rsDSP = mysqli_fetch_array($respuestaDetalleSuPermiso, MYSQLI_NUM)){
                                            echo '<p><b>FECHA : </b>'.$row_rsDSP[0].'     <b>HORA : </b>'.$row_rsDSP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDSP[2]).'</p>';
                                        }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }
                          ?>
                        </div>
                        <div id="recibidos_singose" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>FUNCIONARIO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ACCIONES</th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $MIS_SGR_DIR = "SELECT S.SPR_ID,D.DOC_NOM,U.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SPR_MOT,S.SPR_ESTA,S.SPR_NDIA,DATE_FORMAT(S.SPR_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(S.SPR_FEC_FIN,'%d-%m-%Y'),S.SPR_COM,S.DOC_ID FROM SOL_PSGR S, DOCUMENTO D, USUARIO U WHERE (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = U.USU_RUT) AND ((S.USU_RUT_DIR = '$Srut') OR (S.USU_RUT_SALUD = '$Srut')) AND (S.SPR_FEC BETWEEN '$inicio' AND '$fin') ORDER BY S.SPR_FEC ASC";
                                  $RespuestaSPRd = mysqli_query($cnn, $MIS_SGR_DIR);
                                  //echo $MIS_SAF;
                                  while ($row_MiSPRd = mysqli_fetch_array($RespuestaSPRd, MYSQLI_NUM)){
                                    echo "<tr>";
                                      echo "<td>".$row_MiSPRd[0]."</td>";
                                      echo "<td>".utf8_encode($row_MiSPRd[1])."</td>";
                                      echo "<td>".utf8_encode($row_MiSPRd[3])." ".utf8_encode($row_MiSPRd[4])." ".utf8_encode($row_MiSPRd[5])."</td>";
                                      echo "<td>".utf8_encode($row_MiSPRd[6])."</td>";
                                      echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#VERSUSPR".$row_MiSPRd[0]."'>Detalle</a></td>";
                                    echo "</tr>";
                                    //Modal detalle mispermiso
                                    ?>
                                    <div id="VERSUSPR<?php echo $row_MiSPRd[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                        echo '<h4>Detalle de Documento</h4>';
                                        echo '<h5>PERMISO SIN GOCE DE REMUNERACION</h5>';
                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_MiSPRd[0]).' <b>TIPO : </b>'.utf8_encode($row_MiSPRd[1]).'</p>';
                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_MiSPRd[3]).' '.utf8_encode($row_MiSPRd[4]).' '.utf8_encode($row_MiSPRd[5]);
                                        echo '<p><b>DIAS : </b>'.$row_MiSPRd[8].' <b>DESDE EL : </b>'.$row_MiSPRd[9].' <b>HASTA EL : </b>'.$row_MiSPRd[10].'</p>';
                                        echo '<p><b>MOTIVO DE PERMISO SIN GOCE DE REMUNERACION : </b>'.utf8_encode($row_MiSPRd[6]).'</p>';
                                        //CARGAR HISTO PERMISO
                                        $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_MiSPRd[0].") AND (USU_RUT = '".$row_MiSPRd[2]."') AND (DOC_ID = ".$row_MiSPRd[12].")";
                                        //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                        $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                        //recorrer los registros
                                        echo '<h5>SEGUIMIENTO</h5>';
                                        while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                            echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                        }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }
                          ?>
                        </div>
                        <div id="recibidos_ot" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>FUNCIONARIO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ACCIONES</th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $MisOrdenesJefe = "SELECT OT_EXTRA.OE_ID,DOCUMENTO.DOC_NOM,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,OT_EXTRA.OE_TRAB,OT_EXTRA.OE_CANT_CANCE,OT_EXTRA.OE_CANT_DC,USUARIO.USU_RUT,DOCUMENTO.DOC_ID,OT_EXTRA.OE_ESTA,OTE_PROGRAMA.OP_NOM FROM OT_EXTRA,DOCUMENTO,USUARIO,OTE_PROGRAMA WHERE (OT_EXTRA.DOC_ID = DOCUMENTO.DOC_ID) AND (OT_EXTRA.USU_RUT = USUARIO.USU_RUT) AND (OT_EXTRA.OE_PROGRAMA = OTE_PROGRAMA.OP_ID) AND ((OT_EXTRA.USU_RUT_JF = '$Srut') OR (OT_EXTRA.USU_RUT_DIR = '$Srut') OR (OT_EXTRA.USU_RUT_VB = '$Srut')) AND (OT_EXTRA.OE_FEC BETWEEN '$inicio' AND '$fin') ORDER BY OT_EXTRA.OE_FEC ASC";
                                  $respuestaOTJefes = mysqli_query($cnn, $MisOrdenesJefe);
                                  while ($row_rsOJ = mysqli_fetch_array($respuestaOTJefes, MYSQLI_NUM)){
                                    echo "<tr>";
                                      echo "<td>".$row_rsOJ[0]."</td>";
                                      echo "<td>".utf8_encode($row_rsOJ[1])."</td>";
                                      echo "<td>".utf8_encode($row_rsOJ[2])." ".utf8_encode($row_rsOJ[3])." ".utf8_encode($row_rsOJ[4])."</td>";
                                      echo "<td>".utf8_encode($row_rsOJ[5])."</td>";           
                                      echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#VERSUOT".$row_rsOJ[0]."'>Detalle</a></td>";
                                    echo "</tr>";
                                    //Modal detalle mispermiso
                                    ?>
                                    <div id="VERSUOT<?php echo $row_rsOJ[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                        echo '<h4>Detalle de Documento</h4>';
                                        echo '<p><b>DOCUMENTO N° : </b>'.$row_rsOJ[0].' <b>TIPO : </b>'.utf8_encode($row_rsOJ[1]).'</p>';
                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsOJ[2]).' '.utf8_encode($row_rsOJ[3]).' '.utf8_encode($row_rsOJ[4]);
                                        echo '<p><b>PARA CUMPLIR EL TRABAJO DE : </b>'.utf8_encode($row_rsOJ[5]).'</p>';
                                        echo '<p><b>CON CARGO AL PROGRAMA : </b>'.utf8_encode($row_rsOJ
[11]).'</p>';
                                        //CARGO DETALLE DE HORAS
                                        $DetalleSuOrden = "SELECT DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN FROM OTE_DETALLE WHERE (OE_ID = $row_rsOJ[0]) ORDER BY OTE_DIA ASC";
                                        //echo '<p>'.$DetalleSuOrden.'</p>';
                                        $RespSuOrden = mysqli_query($cnn,$DetalleSuOrden);
                                        while ($row_rsRSO = mysqli_fetch_array($RespSuOrden, MYSQLI_NUM)){
                                            echo '<p><b>DIA : </b>'.$row_rsRSO[0].'     <b>HORA INICIO : </b>'.$row_rsRSO[1].'      <b>HORA FIN : </b>'.utf8_encode($row_rsRSO[2]).'</p>';
                                        }
                                        echo '<p><b>HORAS CANCELADAS : </b>'.$row_rsOJ[6].' <b>HORAS DESCANSO COMPLEMENTARIO : </b>'.$row_rsOJ[7].'</p>';
                                        //CARGAR HISTO PERMISO
                                        $DetalleSuOT = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsOJ[0].") AND (USU_RUT = '".$row_rsOJ[8]."') AND (DOC_ID = ".$row_rsOJ[9].")";
                                        $respuestaDetalleSuOT = mysqli_query($cnn, $DetalleSuOT);
                                        //recorrer los registros
                                        echo '<h5>SEGUIMIENTO</h5>';
                                        while ($row_rsDSOT = mysqli_fetch_array($respuestaDetalleSuOT, MYSQLI_NUM)){
                                            echo '<p><b>FECHA : </b>'.$row_rsDSOT[0].'     <b>HORA : </b>'.$row_rsDSOT[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDSOT[2]).'</p>';
                                        }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }
                          ?>
                        </div>
                        <div id="recibidos_cometido" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>FUNCIONARIO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ACCIONES</th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $SusCometidos = "SELECT COME_PERMI.CO_ID,DOCUMENTO.DOC_NOM,COME_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,COME_PERMI.CO_MOT,COME_PERMI.CO_ESTA,COME_PERMI.CO_VIA,COME_PERMI.CO_DIA,COME_PERMI.CO_PAS,COME_PERMI.CO_COM,COME_PERMI.CO_PEA,COME_PERMI.CO_PAR,COME_PERMI.CO_DES FROM COME_PERMI INNER JOIN DOCUMENTO ON COME_PERMI.DOC_ID = DOCUMENTO.DOC_ID INNER JOIN USUARIO ON COME_PERMI.USU_RUT = USUARIO.USU_RUT WHERE ((COME_PERMI.USU_RUT_JD = '$Srut') OR (COME_PERMI.USU_RUT_DIR = '$Srut')) AND (COME_PERMI.CO_FEC BETWEEN '$inicio' AND '$fin') ORDER BY COME_PERMI.CO_FEC ASC";
                                  $respuestaSCO = mysqli_query($cnn, $SusCometidos);
                                  while ($row_SCO = mysqli_fetch_array($respuestaSCO, MYSQLI_NUM)){
                                    echo "<tr>";
                                      echo "<td>".$row_SCO[0]."</td>";
                                      echo "<td>".$row_SCO[1]."</td>";
                                      echo "<td>".utf8_encode($row_SCO[3])." ".utf8_encode($row_SCO[4])." ".utf8_encode($row_SCO[5])."</td>";
                                      echo "<td>".utf8_encode($row_SCO[6])."</td>";
                                      echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#SUCOME".$row_SCO[0]."'>Detalle</a></td>";
                                    echo "</tr>";
                                    //Modal detalle mispermiso
                                    ?>
                                    <div id="SUCOME<?php echo $row_SCO[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                        echo '<h4>Detalle de Documento</h4>';
                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_SCO[0]).' <b>TIPO : </b>'.utf8_encode($row_SCO[1]).'</p>';
                                        //echo '<p><b>DIAS : </b>'.utf8_encode($row_rs[4]).' <b>DESDE EL : </b>'.$row_rs[5].' <b>HASTA EL : </b>'.$row_rs[6].' <b>JORNADA : </b>'.utf8_encode($row_rs[7]).' </p>';
                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_SCO[3]).' '.utf8_encode($row_SCO[4]).' '.utf8_encode($row_SCO[5]);
                                        echo '<p><b>MOTIVO : </b>'.utf8_encode($row_SCO[6]).'</p>';
                                        echo '<p><b>DESTINO : </b>'.utf8_encode($row_SCO[14]).'</p>';
                                        //CARGO DETALLE DE COMETIDO
                                        $DetalleMiCome = "SELECT DATE_FORMAT(CD_DIA,'%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID = ".$row_SCO[0].") ORDER BY CD_DIA ASC ";
                                        $RespMiCome = mysqli_query($cnn,$DetalleMiCome);
                                        while ($row_CD = mysqli_fetch_array($RespMiCome, MYSQLI_NUM)){
                                            echo '<p><b>DIA : </b>'.$row_CD[0].'     <b>HORA INICIO : </b>'.$row_CD[1].'      <b>HORA FIN : </b>'.utf8_encode($row_CD[2]).'      <b>PORCENTAJE : </b>'.utf8_encode($row_CD[3]).'</p>';
                                        }
                                        //echo $DetalleMiCome;
                                        //CARGAR HISTO PERMISO
                                        $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_SCO[0].") AND (USU_RUT = '".$row_SCO[2]."') AND (DOC_ID = 8)";
                                        $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                        //recorrer los registros
                                        echo '<h5>SEGUIMIENTO</h5>';
                                        while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                            echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                        }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }
                          ?>
                        </div>
                        <div id="recibidos_acufer" class="col s12">
                          <?php
                          if (empty($inicio) && empty($fin)){
                          }else{
                            echo '<table class="responsive-table boradered striped">';
                              echo '<thead>';
                                echo '<tr>';
                                  echo '<th>ID</th>';
                                  echo '<th>TIPO</th>';
                                  echo '<th>FUNCIONARIO</th>';
                                  echo '<th>MOTIVO</th>';
                                  echo '<th>ACCIONES</th>';
                                echo '</tr>';
                                echo '<tbody>';
                                  $MisSAFJefe = "SELECT S.SAF_ID,D.DOC_NOM,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SAF_MOT,S.SAF_ESTA,P.SP_CANT_DIA,DATE_FORMAT(P.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(P.SP_FEC_FIN,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_FIN,'%d-%m-%Y'),P.SP_COM,S.SAF_CANT_DIA,S.SAF_ANO_ACT,S.SAF_ANO_SIG,S.SAF_MOT,S.SAF_ESTA,R.RSP_RESOL,S.DOC_ID FROM SOL_ACU_FER S, RES_SOL_PERMI R, SOL_PERMI P, DOCUMENTO D, USUARIO U WHERE (S.RSP_ID = R.RSP_ID) AND (S.SP_ID = P.SP_ID) AND (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = U.USU_RUT) AND ((S.USU_RUT_JD = '$Srut') OR (S.USU_RUT_DIR = '$Srut')) AND (S.SAF_FEC BETWEEN '$inicio' AND '$fin') ORDER BY S.SAF_FEC ASC";
                                  $respuestaSAFJefe = mysqli_query($cnn, $MisSAFJefe);
                                  while ($row_rsSAFJ = mysqli_fetch_array($respuestaSAFJefe, MYSQLI_NUM)){
                                    echo "<tr>";
                                      echo "<td>".$row_rsSAFJ[0]."</td>";
                                      echo "<td>".utf8_encode($row_rsSAFJ[1])."</td>";
                                      echo "<td>".utf8_encode($row_rsSAFJ[3])." ".utf8_encode($row_rsSAFJ[4])." ".utf8_encode($row_rsSAFJ[5])."</td>";
                                      echo "<td>".utf8_encode($row_rsSAFJ[6])."</td>";           
                                      echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#VERSUSAF".$row_rsSAFJ[0]."'>Detalle</a></td>";
                                    echo "</tr>";
                                    //Modal detalle mispermiso
                                    ?>
                                    <div id="VERSUSAF<?php echo $row_rsSAFJ[0]; ?>" class="modal">
                                      <div class="modal-content">
                                        <?php
                                        echo '<h4>Detalle de Documento</h4>';
                                        echo '<h5>FERIADO LEGAL SOLICITADO</h5>';
                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rsSAFJ[0]).' <b>TIPO : </b>'.utf8_encode($row_rsSAFJ[1]).'</p>';
                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsSAFJ[3]).' '.utf8_encode($row_rsSAFJ[4]).' '.utf8_encode($row_rsSAFJ[5]);
                                        echo '<p><b>DIAS : </b>'.$row_rsSAFJ[8].' <b>DESDE EL : </b>'.$row_rsSAFJ[9].' <b>HASTA EL : </b>'.$row_rsSAFJ[10].'</p>';
                                        echo '<h5>RESOLUCION</h5>';
                                        echo '<p><b>RESOLUCION : </b>'.utf8_encode($row_rsSAFJ[19]).'</p>';
                                        echo '<p><b>FECHA SUGERIDA : </b>'.$row_rsSAFJ[11].' <b>HASTA EL : </b>'.$row_rsSAFJ[12].' </p>';
                                        echo '<p><b>MOTIVO DE REAGENDAR FERIADO : </b>'.utf8_encode($row_rsSAFJ[13]).'</p>';
                                        echo '<h5>ACUMULACION DE FERIADO</h5>';
                                        echo '<p><b>DIAS</b>'.$row_rsSAFJ[14].' <b>DEL AÑO : </b>'.$row_rsSAFJ[15].' <b>PARA EL : </b>'.$row_rsSAFJ[16].' </p>';
                                        echo '<p><b>MOTIVO DE ACUMULACION FERIADO : </b>'.utf8_encode($row_rsSAFJ[17]).'</p>';
                                        //CARGAR HISTO PERMISO
                                        $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsSAFJ[0].") AND (USU_RUT = '".$row_rsSAFJ[2]."') AND (DOC_ID = ".$row_rsSAFJ[20].")";
                                        //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                        $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                        //recorrer los registros
                                        echo '<h5>SEGUIMIENTO</h5>';
                                        while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                            echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                        }
                                        ?>
                                      </div>
                                      <div class="modal-footer">
                                        <a href="formularios.php?fecha_inicio=<?php echo $inicio; ?>&fecha_fin=<?php echo $fin; ?>" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                echo '</tbody>';
                              echo '</thead>';
                            echo '</table>';
                          }
                          ?>
                        </div>
                        <div id="recibidos_otros" class="col s12">
                        </div>
                      </form>
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
        <script>
            $(document).ready(function () {
                //Animaciones 
                $('.tabs').tabs()
            });
        </script>
    </body>
</html>