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
        $id_formulario = 35;
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");
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
                    $consultaEncreacion = "SELECT OE_ID,OE_COM FROM OT_EXTRA WHERE (USU_RUT = '$Srut') AND (OE_ESTA = 'EN CREACION')";
                    $respuestaEnCreacion = mysqli_query($cnn, $consultaEncreacion);
                    if (mysqli_num_rows($respuestaEnCreacion) == 0){
                        //usuario no tiene ningun folio tomado
                        $consultaNuevoId = "SELECT OE_ID FROM OT_EXTRA ORDER BY OE_ID DESC";
                        $respuestaNuevoId = mysqli_query($cnn, $consultaNuevoId);
                        $AñoActual = date("Y");
                        if (mysqli_num_rows($respuestaNuevoId) == 0){
                            $NuevoID = 1;
                            $FolioUno = "INSERT INTO OT_EXTRA (OE_ID,DOC_ID,USU_RUT,OE_ESTA,OE_ANO,OE_FEC) VALUES ($NuevoID, 5, '$Srut', 'EN CREACION','$AñoActual','$fecha')";
                            $GuardarHistoPermiso = "INSERT INTO HISTO_PERMISO (HP_FOLIO,USU_RUT,HP_FEC,HP_HORA,DOC_ID,HP_ACC) VALUES ($NuevoID,'$Srut','$fecha','$hora',5,'CREA ORDEN DE TRABAJO EXTRAORDINARIO')";
                            mysqli_query($cnn, $GuardarHistoPermiso);
                            mysqli_query($cnn, $FolioUno);
                        }else{
                            $rowNuevoId = mysqli_fetch_row($respuestaNuevoId);
                            $UltimoID = $rowNuevoId[0];
                            $NuevoID = $UltimoID + 1;
                            $FolioNuevo = "INSERT INTO OT_EXTRA (OE_ID,DOC_ID,USU_RUT,OE_ESTA,OE_ANO,OE_FEC) VALUES ($NuevoID, 5, '$Srut', 'EN CREACION','$AñoActual','$fecha')";
                            $GuardarHistoPermiso = "INSERT INTO HISTO_PERMISO (HP_FOLIO,USU_RUT,HP_FEC,HP_HORA,DOC_ID,HP_ACC) VALUES ($NuevoID,'$Srut','$fecha','$hora',5,'CREA ORDEN DE TRABAJO EXTRAORDINARIO')";
                            mysqli_query($cnn, $GuardarHistoPermiso);
                            mysqli_query($cnn, $FolioNuevo);
                        }
                    }else{
                        $rowFolioUsado = mysqli_fetch_row($respuestaEnCreacion);
                        $NuevoID = $rowFolioUsado[0];
                        $OE_COM = $rowFolioUsado[1];
                    }
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
            });
            function Cargar(){
                $('#tipo').formSelect('destroy');
                $('#programa').formSelect('destroy');
                $("#hora_ini").attr("disabled","disabled");
                $("#hora_fin").attr("disabled","disabled");
                $("#guardar").attr("disabled","disabled");
                $("#tdcomplementario").attr("disabled","disabled");
								$("#trabajo").attr("disabled","disabled");
                $("#enviar").attr("disabled","disabled");
            }
            function ValidoDia(){
                //fecha_dia
                var dia = $("#fecha_dia").val();
                console.log( dia );
                 $("#hora_ini").removeAttr("disabled");
            }
            function ValidarHoraIni(){
                //fecha_dia
                var hora_ini = $("#hora_ini").val();
                console.log( hora_ini );
                 $("#hora_fin").removeAttr("disabled");
            }
            function ValidarHoraFin(){
                var hora_ini = $("#hora_ini").val();
                var hora_fin = $("#hora_fin").val();
                //M.toast({html: hora_fin});  
                if (hora_fin == "00:00" ){
                    hora_fin = "24:00"
                    console.log( "mayor" );
                    //var total = hora_fin - hora_ini;
                    inicioMinutos = parseInt(hora_ini.substr(3,2));
                    inicioHoras = parseInt(hora_ini.substr(0,2));
                    finMinutos = parseInt(hora_fin.substr(3,2));
                    finHoras = parseInt(hora_fin.substr(0,2));
                    transcurridoMinutos = finMinutos - inicioMinutos;
                    transcurridoHoras = finHoras - inicioHoras;
                    if (transcurridoMinutos < 0) {
                        transcurridoHoras--;
                        transcurridoMinutos = 60 + transcurridoMinutos;
                    }
                    horas = transcurridoHoras.toString();
                    minutos = transcurridoMinutos.toString();
                    if (horas.length < 2) {
                        horas = "0"+horas;
                    }
                    if (horas.length < 2) {
                        horas = "0"+horas;
                    }
                    //document.getElementById("resta").value = horas+":"+minutos;
                    //console.log( "usted hoy hizo "+horas+":"+minutos+" horas extras" );
                    M.toast({html: 'usted hoy hizo '+horas+':'+minutos+' horas extras'}); 
                    if(hora_fin == "24:00"){
                      $("#hora_fin").val("23:59");
                    }
                    $('#tipo').formSelect();
                    //$("#guardar").removeAttr("disabled");
                }else{
                    if (hora_fin < hora_ini){
                        console.log( "menor" );
                        $("#hora_fin").val("");
                    }else{
                        console.log( "mayor" );
                        //var total = hora_fin - hora_ini;
                        inicioMinutos = parseInt(hora_ini.substr(3,2));
                        inicioHoras = parseInt(hora_ini.substr(0,2));
                        finMinutos = parseInt(hora_fin.substr(3,2));
                        finHoras = parseInt(hora_fin.substr(0,2));
                        transcurridoMinutos = finMinutos - inicioMinutos;
                        transcurridoHoras = finHoras - inicioHoras;
                        if (transcurridoMinutos < 0) {
                            transcurridoHoras--;
                            transcurridoMinutos = 60 + transcurridoMinutos;
                        }
                        horas = transcurridoHoras.toString();
                        minutos = transcurridoMinutos.toString();
                        if (horas.length < 2) {
                            horas = "0"+horas;
                        }
                        if (horas.length < 2) {
                            horas = "0"+horas;
                        }
                        //document.getElementById("resta").value = horas+":"+minutos;
                        //console.log( "usted hoy hizo "+horas+":"+minutos+" horas extras" );
                        M.toast({html: 'usted hoy hizo '+horas+':'+minutos+' horas extras'});
                        if(hora_fin == "00:00"){
                          $("#hora_fin").val("23:59");
                        }
                        $('#tipo').formSelect();
                        //$("#guardar").removeAttr("disabled");
                    }
                }
            }
            function ValidarTipo(){
                $("#guardar").removeAttr("disabled");
                //var tipo = $("#tipo").val();
                //Materialize.toast(tipo, 4000);
            }
            function Cancelar(cont){
                var contador = cont;
                var oe_id = $("#folio").val();
                var id_dia = "#DIA"+contador;
                var id_hi = "#HORA_INI"+contador;
                var id_hf = "#HORA_FIN"+contador;
                var dia = $(id_dia).val();
                var hora_ini = $(id_hi).val();
                var hora_fin = $(id_hf).val();
                console.log(oe_id+" "+dia+" "+hora_ini+" "+hora_fin);
                $.post( "../php/borrar_detalle.php", { "id" : oe_id, "dia" : dia, "hora_ini" : hora_ini, "hora_fin" : hora_fin }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        //console.log("id " + id_nuevo + " nombre formulario " + nombre_nuevo + " estado " + estado_nuevo);
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "for_ot_extra.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        //console.log( "La solicitud a fallado: " +  textStatus);
                        window.location = "for_ot_extra.php";
                    }
                });
            }
						function Respuesta(g){
							var respuesta = g;
							if(respuesta == "OK"){
                window.location = "for_ot_extra.php";
							}else if(respuesta == "INICIO"){
                M.toast({html: 'La hora de inicio ya existe para esta fecha'}); 
								$("#hora_ini").val("");
								$("#hora_fin").val("");
								$("#hora_fin").attr("disabled","disabled");
								$("#guardar").attr("disabled","disabled");
							}else if(respuesta == "FIN"){
                M.toast({html: 'La hora de termino ya existe para esta fecha'}); 
								$("#hora_fin").val("");
								$("#guardar").attr("disabled","disabled");										 
							}else if(respuesta == "INICIO-FIN"){
                M.toast({html: 'El rango de horas ingresado ya existe'}); 
								$("#hora_ini").val("");
								$("#hora_fin").val("");
								$("#hora_fin").attr("disabled","disabled");
								$("#guardar").attr("disabled","disabled");
							}
						}
            function Guardar(){
                var oe_id = $("#folio").val();
                var dia = $("#fecha_dia").val();
                var hora_ini = $("#hora_ini").val();
								var hora_ini = hora_ini+ ":00";
                var hora_fin = $("#hora_fin").val();
								var hora_fin = hora_fin+ ":00";
                var tipo = $("#tipo").val();
                $.post( "../php/nuevo_detalle_version2.php", { "id" : oe_id, "dia" : dia, "hora_ini" : hora_ini, "hora_fin" : hora_fin , "tipo" : tipo}, Respuesta, "json" ); 
            }
						function JefeDirecto(){
              $('#programa').formSelect();
						}
						function Programa(){
							$("#trabajo").removeAttr("disabled");
						}
            function Listo(){
                var f = new Date();
                var dia = f.getDate();
                if(dia <= 15 || dia >= 27){
                   $("#enviar").removeAttr("disabled");
                }else{
                   M.toast({html: 'Las Ordenes de trabajo extraordinario solo se pueden enviar despues del 27 de cada mes hasta el 15 del mes siguiente'});  
                }
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
                        <h4 class="light">Orden de Trabajo Extraordinario</h4>
                         <?php
                          if($OE_COM != ""){
                            echo '<h5 class="light">';
                            echo 'Comentario Jeje Directo : ';
                            echo $OE_COM;
                            echo '</h5>';
                          }  
                         ?>
                         <form name="form" class="col s12" method="post">
                            </br>
                            </br>
                            <div class="input-field col s6">
                                <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $Snombre." ".$SapellidoP." ".$SapellidoM;?>" disabled>
                                <label for="nombre_usuario">Nombre Completo Funcionario</label>
                            </div>
                            <div class="input-field col s4">
                                <input type="text" name="categoria_usuario" id="categoria_usuario" class="validate" placeholder="" value="<?php echo $Scategoria;?>" disabled>
                                <label for="categoria_usuario">Categoria</label>
                            </div>
                            <div class="input-field col s2">
                                <input type="text" name="folio" id="folio" class="validate" placeholder="" value="<?php echo $NuevoID;?>" disabled>
                                <label for="folio">Folio</label>
                            </div>
                            
                            <div class="col s12" align="left"><h6>Según lo siguiente :</h6></div>
                            </br>
                            </br>
                            <table class="responsive-table boradered">
                                <thead>
                                    <tr>
                                        <th>DIA</th>
                                        <th>HORA INICIO</th>
                                        <th>HORA TERMINO</th>
                                        <th>TIPO</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                    <tbody>
                                        <?php
                                            $Detalle_Ot_extra = "SELECT OE_ID,DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN,OTE_DIA,OTE_TIPO FROM OTE_DETALLE WHERE (OE_ID = $NuevoID) ORDER BY OTE_DIA ASC, OTE_TIPO";
                                            $respuesta = mysqli_query($cnn, $Detalle_Ot_extra);
                                            //recorrer los registros
                                            $contador                   = 1;
                                            $hora_diurna                = 0;
                                            $min_diurna                 = 0;
                                            $seg_diurna                 = 0;
                                            $hora_nocturna              = 0;
                                            $min_nocturna               = 0;
                                            $seg_nocturna               = 0;
                                            $hora_diurna_cancelada      = 0;
                                            $min_diurna_cancelada       = 0;
                                            $seg_diurna_cancelada       = 0;
                                            $hora_nocturna_cancelada    = 0;
                                            $min_nocturna_cancelada     = 0;
                                            $seg_nocturna_cancelada     = 0;
                                            while ($row_rs = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
                                                $semaforo_compensadas = "NO";
                                                $semaforo_canceladas = "NO";
                                                echo "<tr>";
                                                    echo '<td><input type="text" id="DIA'.$contador.'" class="validate" placeholder="" value="'.$row_rs[1].'" style="display: none">'.$row_rs[1].'</td>';
                                                    //echo "<td>".$row_rs[1]."</td>";// id="DIA'.$contador.'";
                                                    echo '<td><input type="text" id="HORA_INI'.$contador.'" class="validate" placeholder="" value="'.$row_rs[2].'" style="display: none">'.$row_rs[2].'</td>';
                                                    //echo "<td id='HORA_INI".$contador."'>".$row_rs[2]."</td>";
                                                    echo '<td><input type="text" id="HORA_FIN'.$contador.'" class="validate" placeholder="" value="'.$row_rs[3].'" style="display: none">'.$row_rs[3].'</td>';
                                                    //echo "<td id='HORA_FIN".$contador."'>".$row_rs[3]."</td>";
                                                    echo '<td><input type="text" id="TIPO'.$contador.'" class="validate" placeholder="" value="'.$row_rs[5].'" style="display: none">'.$row_rs[5].'</td>';
                                                    //echo "<td><a class='waves-effect waves-light btn' href='#MIPERMISO".$row_rs[0]."'>Detalle</a></td>";
                                                    echo "<td><button class='btn trigger' name='cancelar' id='cancelar' type='button' onclick='Cancelar(".$contador.");'>Cancelar</button></td>";
                                                echo "</tr>";
                                                if ($row_rs[5] == "COMPENSADAS"){
                                                    if (date('w',strtotime($row_rs[4])) == 0){
                                                        //DIA DOMINGO
                                                        $HorasDomingo = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                        //list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna);
                                                        list($horaDom, $minDom, $segDom) = split('[:]', $HorasDomingo);
                                                        //$hora_nocturna = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
                                                        $hora_nocturna = $hora_nocturna + $horaDom;
                                                        $min_nocturna  = $min_nocturna + $minDom;
                                                        $seg_nocturna  = $seg_nocturna + $segDom;
                                                    }else{
                                                        //NO ES DOMINGO
                                                        if (date('w',strtotime($row_rs[4])) == 6){
                                                            //DIA SABADO
                                                            $HorasSabado = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                            //list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna);
                                                            list($horaSab, $minSab, $segSab) = split('[:]', $HorasSabado);
                                                            //$hora_nocturna = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
                                                            $hora_nocturna = $hora_nocturna + $horaSab;
                                                            $min_nocturna  = $min_nocturna + $minSab;
                                                            $seg_nocturna  = $seg_nocturna + $segSab;
                                                        }else{
                                                            //NO ES SABADO - REVISAR SI ES VERIADO
                                                            $ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$row_rs[4]."')";
                                                            $RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
                                                            if (mysqli_num_rows($RespuestaFeriado) == 0){
                                                                //NO ES FERIADO - REVISAR SI ES ANTES O DESPUES DE LAS 21 HORAS
                                                                if($row_rs[2] < "07:00:00"){
                                                                  if($row_rs[3] > "07:00:00"){
                                                                    $HorasNocturnas = date("H:i:s",strtotime("00:00:00")+strtotime("07:00:00")-strtotime($row_rs[2]));
                                                                    $HorasDiurnas = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime("07:00:00"));
                                                                    //list($hora1, $minut1, $seg1) = split('[:]', $hora_diurna);
                                                                    list($horaDiurno, $minDiurno, $segDiurno) = split('[:]', $HorasDiurnas);
                                                                    //$hora_diurna = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
                                                                    $hora_diurna = $hora_diurna + $horaDiurno;
                                                                    $min_diurna  = $min_diurna + $minDiurno;
                                                                    $seg_diurna  = $seg_diurna + $segDiurno;
                                                                    //$hora_diurna = $hora_diurna + $HorasDiurnas;
                                                                    //list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna);
                                                                    list($horaNoc, $minNoc, $segNoc) = split('[:]', $HorasNocturnas);
                                                                    //$hora_nocturna = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
                                                                    //$hora_nocturna = $hora_nocturna + $HorasNocturnas;
                                                                    $hora_nocturna = $hora_nocturna + $horaNoc;
                                                                    $min_nocturna  = $min_nocturna + $minNoc;
                                                                    $seg_nocturna  = $seg_nocturna + $segNoc;
                                                                  }else{
                                                                    $HorasDia = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                                    list($horaNocturna, $minNocturna, $segNocturna) = split('[:]', $HorasDia);
                                                                    $hora_nocturna = $hora_nocturna + $horaNocturna;
                                                                    $min_nocturna  = $min_nocturna + $minNocturna;
                                                                    $seg_nocturna  = $seg_nocturna + $segNocturna;
                                                                  }
                                                                  $semaforo_compensadas = "SI";
                                                                }
                                                                if($semaforo_compensadas == "NO"){
                                                                  if ($row_rs[2] < "21:00:00"){
                                                                      if ($row_rs[3] > "21:00:00"){
                                                                          $HorasDiurnas = date("H:i:s",strtotime("00:00:00")+strtotime("21:00:00")-strtotime($row_rs[2]));
                                                                          $HorasNocturnas = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime("21:00:00"));
                                                                          //list($hora1, $minut1, $seg1) = split('[:]', $hora_diurna);
                                                                          list($horaDiurno, $minDiurno, $segDiurno) = split('[:]', $HorasDiurnas);
                                                                          //$hora_diurna = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
                                                                          $hora_diurna = $hora_diurna + $horaDiurno;
                                                                          $min_diurna  = $min_diurna + $minDiurno;
                                                                          $seg_diurna  = $seg_diurna + $segDiurno;
                                                                          //$hora_diurna = $hora_diurna + $HorasDiurnas;
                                                                          //list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna);
                                                                          list($horaNoc, $minNoc, $segNoc) = split('[:]', $HorasNocturnas);
                                                                          //$hora_nocturna = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
                                                                          //$hora_nocturna = $hora_nocturna + $HorasNocturnas;
                                                                          $hora_nocturna = $hora_nocturna + $horaNoc;
                                                                          $min_nocturna  = $min_nocturna + $minNoc;
                                                                          $seg_nocturna  = $seg_nocturna + $segNoc;
                                                                      }else{
                                                                          $HorasNormal = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                                          //list($hora1, $minut1, $seg1) = split('[:]', $hora_diurna);
                                                                          list($horaNormal, $minNormal, $segNormal) = split('[:]', $HorasNormal);
                                                                          //$hora_diurna = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
                                                                          $hora_diurna = $hora_diurna + $horaNormal;
                                                                          $min_diurna  = $min_diurna + $minNormal;
                                                                          $seg_diurna  = $seg_diurna + $segNormal;
                                                                      }
                                                                  }else{
                                                                      $HorasDia = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                                      //list($hora1, $minut1, $seg1) = split('[:]', $hora_nocturna);
                                                                      list($horaNocturna, $minNocturna, $segNocturna) = split('[:]', $HorasDia);
                                                                      //$hora_nocturna = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
                                                                      $hora_nocturna = $hora_nocturna + $horaNocturna;
                                                                      $min_nocturna  = $min_nocturna + $minNocturna;
                                                                      $seg_nocturna  = $seg_nocturna + $segNocturna;
                                                                  }
                                                                }
                                                            }else{
                                                                //DIA FERIADO
                                                                $HorasExtras = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                                //list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna);
                                                                list($horaFer, $minFer, $segFer) = split('[:]', $HorasExtras);
                                                                //$hora_nocturna = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
                                                                $hora_nocturna = $hora_nocturna + $horaFer;
                                                                $min_nocturna  = $min_nocturna + $minFer;
                                                                $seg_nocturna  = $seg_nocturna + $segFer;
                                                            }
                                                        }
                                                    }
                                                  if($row_rs[3] == "23:59:00"){
                                                    $min_nocturna  = $min_nocturna + 1;
                                                  }
                                                }else{
                                                    if (date('w',strtotime($row_rs[4])) == 0){
                                                        //DIA DOMINGO
                                                        $HorasDomingo = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                        //list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna_cancelada);
                                                        list($horaDomCa, $minDomCa, $segDomCa) = split('[:]', $HorasDomingo);
                                                        //$hora_nocturna_cancelada = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
                                                        $hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaDomCa;
                                                        $min_nocturna_cancelada = $min_nocturna_cancelada + $minDomCa;
                                                        $seg_nocturna_cancelada = $seg_nocturna_cancelada + $segDomCa; 
                                                    }else{
                                                        //NO ES DOMINGO
                                                        if (date('w',strtotime($row_rs[4])) == 6){
                                                            //DIA SABADO
                                                            $HorasSabado = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                            //list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna_cancelada);
                                                            list($horaSabCa, $minSabCa, $segSabCa) = split('[:]', $HorasSabado);
                                                            //$hora_nocturna_cancelada = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
                                                            $hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaSabCa;
                                                            $min_nocturna_cancelada = $min_nocturna_cancelada + $minSabCa;
                                                            $seg_nocturna_cancelada = $seg_nocturna_cancelada + $segSabCa; 
                                                        }else{
                                                            //NO ES SABADO - REVISAR SI ES VERIADO
                                                            $ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$row_rs[4]."')";
                                                            $RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
                                                            if (mysqli_num_rows($RespuestaFeriado) == 0){
                                                                //NO ES FERIADO - REVISAR SI ES ANTES O DESPUES DE LAS 21 HORAS
                                                                if($row_rs[2] < "07:00:00"){
                                                                  if($row_rs[3] > "07:00:00"){
                                                                    $HorasNocturnas = date("H:i:s",strtotime("00:00:00")+strtotime("07:00:00")-strtotime($row_rs[2]));
                                                                    $HorasDiurnas = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime("07:00:00"));
                                                                    list($horaDiurnoCa, $minDiurnoCa, $segDiurnoCa) = split('[:]', $HorasDiurnas);
                                                                    $hora_diurna_cancelada = $hora_diurna_cancelada + $horaDiurnoCa;
                                                                    $min_diurna_cancelada = $min_diurna_cancelada + $minDiurnoCa;
                                                                    $seg_diurna_cancelada = $seg_diurna_cancelada + $segDiurnoCa;
                                                                    list($horaNocCa, $minNocCa, $segNocCa) = split('[:]', $HorasNocturnas);
                                                                    $hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaNocCa;
                                                                    $min_nocturna_cancelada = $min_nocturna_cancelada + $minNocCa;
                                                                    $seg_nocturna_cancelada = $seg_nocturna_cancelada + $segNocCa; 
                                                                  }else{
                                                                    $HorasNocturnas = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                                    list($horaNocCa, $minNocCa, $segNocCa) = split('[:]', $HorasNocturnas);
                                                                    $hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaNocCa;
                                                                    $min_nocturna_cancelada = $min_nocturna_cancelada + $minNocCa;
                                                                    $seg_nocturna_cancelada = $seg_nocturna_cancelada + $segNocCa; 
                                                                  }
                                                                  $semaforo_canceladas = "SI";
                                                                }
                                                                if($semaforo_canceladas == "NO"){
                                                                  if ($row_rs[2] < "21:00:00"){
                                                                      if ($row_rs[3] > "21:00:00"){
                                                                          $HorasDiurnas = date("H:i:s",strtotime("00:00:00")+strtotime("21:00:00")-strtotime($row_rs[2]));
                                                                          $HorasNocturnas = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime("21:00:00"));
                                                                          //list($hora1, $minut1, $seg1) = split('[:]', $hora_diurna_cancelada);
                                                                          list($horaDiurnoCa, $minDiurnoCa, $segDiurnoCa) = split('[:]', $HorasDiurnas);
                                                                          $hora_diurna_cancelada = $hora_diurna_cancelada + $horaDiurnoCa;
                                                                          $min_diurna_cancelada = $min_diurna_cancelada + $minDiurnoCa;
                                                                          $seg_diurna_cancelada = $seg_diurna_cancelada + $segDiurnoCa;
                                                                          //$hora_diurna_cancelada = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
                                                                          //$hora_diurna_cancelada = $hora_diurna_cancelada + $HorasDiurnas;
                                                                          //list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna_cancelada);
                                                                          list($horaNocCa, $minNocCa, $segNocCa) = split('[:]', $HorasNocturnas);
                                                                          $hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaNocCa;
                                                                          $min_nocturna_cancelada = $min_nocturna_cancelada + $minNocCa;
                                                                          $seg_nocturna_cancelada = $seg_nocturna_cancelada + $segNocCa; 
                                                                          //$hora_nocturna_cancelada = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
                                                                          //$hora_nocturna_cancelada = $hora_nocturna_cancelada + $HorasNocturnas;
                                                                      }else{
                                                                          $HorasNormal = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                                          //list($hora1, $minut1, $seg1) = split('[:]', $hora_diurna_cancelada);
                                                                          list($horaNormalCa, $minNormalCa, $segNormalCa) = split('[:]', $HorasNormal);
                                                                          //$hora_diurna_cancelada = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
                                                                          $hora_diurna_cancelada = $hora_diurna_cancelada + $horaNormalCa;
                                                                          $min_diurna_cancelada = $min_diurna_cancelada + $minNormalCa;
                                                                          $seg_diurna_cancelada = $seg_diurna_cancelada + $segNormalCa;
                                                                      }
                                                                  }else{
                                                                      $HorasDia = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                                      //list($hora1, $minut1, $seg1) = split('[:]', $hora_nocturna_cancelada);
                                                                      list($horaDiaCa, $minDiaCa, $segDiaCa) = split('[:]', $HorasDia);
                                                                      //$hora_nocturna_cancelada = date("H:i:s", mktime($hora1+$hora2,$minut1+$minut2,$seg1+$seg2));
                                                                      $hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaDiaCa;
                                                                      $min_nocturna_cancelada = $min_nocturna_cancelada + $minDiaCa;
                                                                      $seg_nocturna_cancelada = $seg_nocturna_cancelada + $segDiaCa; 
                                                                  }
                                                                }
                                                            }else{
                                                                //DIA FERIADO
                                                                $HorasExtras = date("H:i:s",strtotime("00:00:00")+strtotime($row_rs[3])-strtotime($row_rs[2]));
                                                                //list($hora3, $minut3, $seg3) = split('[:]', $hora_nocturna_cancelada);
                                                                list($horaFerCa, $minFerCa, $segFerCa) = split('[:]', $HorasExtras);
                                                                //$hora_nocturna_cancelada = date("H:i:s", mktime($hora3+$hora4,$minut3+$minut4,$seg3+$seg4));
                                                                $hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaFerCa;
                                                                $min_nocturna_cancelada = $min_nocturna_cancelada + $minFerCa;
                                                                $seg_nocturna_cancelada = $seg_nocturna_cancelada + $segFerCa; 
                                                            }
                                                        }
                                                    }
                                                  if($row_rs[3] == "23:59:00"){
                                                    $min_nocturna_cancelada  = $min_nocturna_cancelada + 1;
                                                  }
                                                }
                                                $contador = $contador + 1;
                                                //echo $hora_diurna_cancelada;
                                            }
                                            //echo $hora_diurna_cancelada;
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="input-field col s12">
                                                    <input type="text" name="fecha_dia" id="fecha_dia" class="datepicker" placeholder="Dia" onchange="ValidoDia();"> 
                                                </div> 
                                            </td>
                                            <td>
                                                <div class="input-field col s12">
                                                    <input id="hora_ini" name="hora_ini" class="timepicker" type="text" placeholder="Hora Inicio" onchange="ValidarHoraIni();" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-field col s12">
                                                    <input id="hora_fin" name="hora_fin" class="timepicker" type="text" placeholder="Hora Termino" onchange="ValidarHoraFin();" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-field col s12">
                                                    <select name="tipo" id="tipo" onchange="ValidarTipo();">
                                                        <option selected value="NO">SELECCIONAR</option>
                                                        <option value="COMPENSADAS">COMPENSADAS</option>
                                                        <option value="CANCELADAS">CANCELADAS</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td><button class="btn trigger" name="guardar" id="guardar" type="button" onclick="Guardar();">Grabar</button></td>
                                        </tr>
                                    </tbody>
                                </thead>
                            </table>
                            </br>
                            <table class="col s6">
                                <thead>
                                    <tr>
                                        <th>HORAS COMPENSADAS</th>
                                    </tr>
                                    <?php
                                    //dar formato a hora compensado
                                    //hora diurna
                                    if($min_diurna >= 60){
                                        $hora_diruna_resultado = $min_diurna / 60;
                                        $min_diurna_resultado = $min_diurna % 60;
                                        if($min_diurna_resultado < 10){
                                            $min_diurna_resultado = "0".$min_diurna_resultado;
                                        }
                                        $hora_diurna = (int)$hora_diruna_resultado + $hora_diurna;
                                        $hora_diurna_compensada = $hora_diurna.":".$min_diurna_resultado.":00";
                                    }else{
                                        $hora_diurna_compensada = $hora_diurna.":".$min_diurna.":00";
                                        
                                    }
                                    //hora nocturna
                                    if($min_nocturna >= 60){
                                        $hora_nocturna_resultado = $min_nocturna / 60;
                                        $min_nocturna_resultado = $min_nocturna % 60;
                                        if($min_nocturna_resultado < 10){
                                            $min_nocturna_resultado = "0".$min_nocturna_resultado;
                                        }
                                        $hora_nocturna = (int)$hora_nocturna_resultado + $hora_nocturna;
                                        $hora_nocturna_compensada = $hora_nocturna.":".$min_nocturna_resultado.":00";
                                    }else{
                                        $hora_nocturna_compensada = $hora_nocturna.":".$min_nocturna.":00";
                                    }
                                    ?>
                                    <tbody>
                                    <tr>
                                        <td>Horas Diurnas</td>
                                        <td><?php echo $hora_diurna_compensada; ?></td>
                                        <td>Al 1.25</td>
                                        <td>
                                            <?php 
                                            //list($horaD, $minutD, $segD) = split('[:]', $hora_diurna);
                                            $hora_diurna_total = $hora_diurna*1.25;
                                            echo (int)$hora_diurna_total." Horas";
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Horas Nocturnas</td>
                                        <td><?php echo $hora_nocturna_compensada; ?></td>
                                        <td>Al 1.5</td>
                                        <td>
                                            <?php 
                                            //ist($horaN, $minutN, $segN) = split('[:]', $hora_nocturna);
                                            $hora_nocturna_total = $hora_nocturna*1.5;
                                            echo (int)$hora_nocturna_total." Horas";
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><b>Horas Compensadas :</b></td>
                                        <td>
                                            <?php
                                                $TotalHoras = (int)$hora_diurna_total + (int)$hora_nocturna_total;
                                                $TotalHoras = (int)$TotalHoras;
                                                echo '<input type="text" id="totalhorascompensadas" name="totalhorascompensadas" class="validate" placeholder="" value="'.$TotalHoras.'" style="display: none">'.$TotalHoras.' Horas';
                                            ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </thead>
                            </table>
                            <table class="col s6">
                                <thead>
                                    <tr>
                                        <th>HORAS CANCELADAS</th>
                                    </tr>
                                    <?php
                                    //dar formato a hora cancelada
                                    //hora diurna
                                    if($min_diurna_cancelada >= 60){
                                        $hora_diurna_cancelada_resultado = $min_diurna_cancelada / 60;
                                        $min_diurna_cancelada_resultado = $min_diurna_cancelada % 60;
                                        if($min_diurna_cancelada_resultado < 10){
                                            $min_diurna_cancelada_resultado = "0".$min_diurna_cancelada_resultado;
                                        }
                                        $hora_diurna_cancelada = (int)$hora_diurna_cancelada_resultado + $hora_diurna_cancelada;
                                        $hora_diurna_cancelada_final = $hora_diurna_cancelada.":".$min_diurna_cancelada_resultado.":00";
                                    }else{
                                        $hora_diurna_cancelada_final = $hora_diurna_cancelada.":".$min_diurna_cancelada.":00";
                                    }
                                    //hora nocturna
                                    if($min_nocturna_cancelada >= 60){
                                        $hora_nocturna_cancelada_resultado = $min_nocturna_cancelada / 60;
                                        $min_nocturna_cancelada_resultado = $min_nocturna_cancelada % 60;
                                        if($min_nocturna_cancelada_resultado < 10){
                                            $min_nocturna_cancelada_resultado = "0".$min_nocturna_cancelada_resultado;
                                        }
                                        $hora_nocturna_cancelada = (int)$hora_nocturna_cancelada_resultado + $hora_nocturna_cancelada;
                                        $hora_nocturna_cancelada_final = $hora_nocturna_cancelada.":".$min_nocturna_cancelada_resultado.":00";
                                    }else{
                                        $hora_nocturna_cancelada_final = $hora_nocturna_cancelada.":".$min_nocturna_cancelada.":00";
                                    }
                                    ?>
                                    <tbody>
                                    <tr>
                                        <td>Horas Diurnas</td>
                                        <td><?php echo $hora_diurna_cancelada_final ?></td>
                                        <td>Al 1.25</td>
                                        <td>
                                            <?php 
                                            //list($horaD, $minutD, $segD) = split('[:]', $hora_diurna_cancelada);
                                            //$hora_diurna_total_cancelada = date("H", mktime($horaD));
                                            echo $hora_diurna_cancelada." Horas";
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Horas Nocturnas</td>
                                        <td><?php echo $hora_nocturna_cancelada_final; ?></td>
                                        <td>Al 1.5</td>
                                        <td>
                                            <?php 
                                            //list($horaN, $minutN, $segN) = split('[:]', $hora_nocturna_cancelada);
                                            //$hora_nocturna_total_cancelada = date("H", mktime($horaN));
                                            echo $hora_nocturna_cancelada." Horas";
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><b>Horas Canceladas :</b></td>
                                        <td>
                                            <?php
                                                $TotalHorasCanceladas = $hora_diurna_cancelada + $hora_nocturna_cancelada;
                                                echo '<input type="text" id="totalhorascanceladas" name="totalhorascanceladas" class="validate" placeholder="" value="'.$TotalHorasCanceladas.'" style="display: none">'.$TotalHorasCanceladas.' Horas';
                                            ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </thead>
                            </table>
                            </br>
                            </br>
                            </br>
                            <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura" onchange="JefeDirecto();">
                                    <?php
                                        $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND ((ESTABLECIMIENTO.EST_NOM = '$Sdependencia') OR (ESTABLECIMIENTO.EST_NOM = 'MULTIESTABLECIMIENTO'))";
                                        $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){
                                                $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                            }
                                            echo "<option value='no' selected disabled>Jefe Directo</option>";
                                    ?>
                                </select>
                                <label for="jefatura">Jefe Directo</label>
                            </div>
                            <div class="input-field col s12" >
                                <select name="programa" id="programa" onchange="Programa();">
                                    <?php
                                        $queryPrograma = "SELECT OTE_PROGRAMA.OP_ID,OTE_PROGRAMA.OP_NOM FROM OTE_PROGRAMA,ESTABLECIMIENTO WHERE (OTE_PROGRAMA.EST_ID = ESTABLECIMIENTO.EST_ID) AND (OP_ESTA = 'ACTIVO') AND (ESTABLECIMIENTO.EST_NOM = '$Sdependencia')";
                                        $resultadoP =mysqli_query($cnn, $queryPrograma);
                                            while($regP =mysqli_fetch_array($resultadoP)){
                                                printf("<option value=\"$regP[0]\">$regP[1]</option>");
                                            }
                                            echo "<option value='0'>Sin Cargo a Programa</option>";
                                            echo "<option value='NO' selected>Seleccione Programa</option>";
                                    ?>
                                </select>
                                <label for="programa">Cargo a algun programa :</label>
                            </div>
                            <div class="input-field col s12">
                                <input type="text" name="trabajo" id="trabajo" class="validate" placeholder="" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="Listo();">
                                <label for="trabajo">Para cumplir el trabajo de :</label>
                            </div>
                           <div class="col s12">
                                <button id="enviar" type="submit" class="btn trigger" name="enviar" value="Guardar" >Guardar</button>
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
        <script>
            $(document).ready(function () {
                /*Animaciones 
                $('select').material_select();
                $(".modal-trigger").leanModal();
                $(".dropdown-button").dropdown();
                $(".button-collapse").sideNav();*/
            });
        </script>
        <?php
            if($_POST['enviar'] == "Guardar"){
                //primero rescato todos los datos del formulario
                $usu_rut_jf = $_POST['jefatura'];
								$programa = $_POST['programa'];
                $oe_trab = utf8_decode($_POST['trabajo']);
								$oe_cc_diu = $hora_diurna_cancelada;
								$oe_cc_noc = $hora_nocturna_cancelada;
                $oe_cant_cance = $_POST['totalhorascanceladas'];
                $oe_dc_diu = $hora_diurna;
								$oe_dc_noc = $hora_nocturna;
								$oe_cant_dc = $_POST['totalhorascompensadas'];
                $oe_esta = 'SOLICITADO';
                $FecActual = date("Y-m-d");
                $HorActual = date("H:i:s");
                if($oe_cant_cance == 0 && $oe_cant_dc == 0){
                  ?> <script type="text/javascript"> M.toast({html: 'Para enviar la orden de trabajo, favor agregar registros.'});;</script>  <?php
                }else{
                  if($Sdependencia == "ILUSTRE MUNICIPALIDAD DE RENGO"){
                      $guardar_orden = "UPDATE OT_EXTRA SET USU_RUT_JF = '$usu_rut_jf',OE_TRAB = '$oe_trab',OE_CC_DIU = $oe_cc_diu,OE_CC_NOC = $oe_cc_noc,OE_CANT_CANCE = $oe_cant_cance,OE_DC_DIU = $oe_dc_diu,OE_DC_NOC = $oe_dc_noc,OE_CANT_DC = $oe_cant_dc,OE_ESTA = 'V.B. DIR SALUD',OE_FEC = '$FecActual',OE_DECRE = 'NO', OE_PROGRAMA = $programa WHERE (OE_ID = $NuevoID)";
                    if($Srut == "15.102.726-1"){
                      //guardar horas compensadas si corresponde
                      $ingreso = "INSERT INTO BANCO_HORAS (USU_RUT, BH_FEC, BH_TIPO, BH_CANT, BH_SALDO, BH_ID_ANT) VALUES ('$Srut','$FecActual','INGRESO',$oe_cant_dc,$oe_cant_dc,$NuevoID)";
                      mysqli_query($cnn, $ingreso);
                    }
                  }else{
                      $guardar_orden = "UPDATE OT_EXTRA SET USU_RUT_JF = '$usu_rut_jf',OE_TRAB = '$oe_trab',OE_CC_DIU = $oe_cc_diu,OE_CC_NOC = $oe_cc_noc,OE_CANT_CANCE = $oe_cant_cance,OE_DC_DIU = $oe_dc_diu,OE_DC_NOC = $oe_dc_noc,OE_CANT_DC = $oe_cant_dc,OE_ESTA = '$oe_esta',OE_FEC = '$FecActual',OE_DECRE = 'NO', OE_PROGRAMA = $programa WHERE (OE_ID = $NuevoID)";
                  }
                  //echo $guardar_orden;
                  $GuaHistoPermiso = "INSERT INTO HISTO_PERMISO (HP_FOLIO,USU_RUT,HP_FEC,HP_HORA,DOC_ID,HP_ACC) VALUES ($NuevoID,'$Srut','$FecActual','$HorActual',5,'ENVIA ORDEN DE TRABAJO EXTRAORDINARIO')";
                  mysqli_query($cnn, $guardar_orden);
                  mysqli_query($cnn, $GuaHistoPermiso);
                  ?> <script type="text/javascript"> window.location="../index.php";</script>  <?php
                }

            }
        ?>
    </body>
</html>
