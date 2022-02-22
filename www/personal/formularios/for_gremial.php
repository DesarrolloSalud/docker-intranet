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
        $Sestablecimiento = ($_SESSION['EST_ID']);
        $Sdependencia = $_SESSION['USU_DEP']; 
        $hoy = date("Y-m-d");
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $id_formulario = 45;
        $dc_fec = date("Y-m-d");
        $consultaborrador ="SELECT FG_ID FROM GREMI_FOR WHERE (USU_RUT='$Srut') AND (FG_ESTADO='EN CREACION')";
        $rsb = mysqli_query($cnn, $consultaborrador);
        if(mysqli_num_rows($rsb) !=0){
          $rsb1 = mysqli_fetch_row($rsb);
          $idpermi = $rsb1[0];
        }else{
          $insertborrador = "INSERT INTO GREMI_FOR (USU_RUT, FG_ESTADO,DOC_ID) VALUES ('$Srut', 'EN CREACION','11')";
          mysqli_query($cnn, $insertborrador);
          ?> <script type="text/javascript"> window.location="for_gremial.php";</script>  <?php
          //header("location:../for_gremial.php");
        }        
                       
        date_default_timezone_set("America/Santiago");
        function inicio($fecha1){
          $fechaini = new DateTime();
          $fechaini->modify( 'first day of this month' );
          return $fechaini->format( 'Y-m-d' );
        }
        function fin($fecha1){          
          $fechafin = new DateTime();
          $fechafin->modify( 'last day of this month' );
          return $fechafin->format( 'Y-m-d' );          
        }
        
        
        $fecano =date("Y");
       	$fechaini1 = inicio($hoy);
        $fechafin1 = fin($hoy);
        
        if($stmt =mysqli_prepare($cnn, "SELECT FG_HOR_UTIL, FG_MIN_UTIL, FG_TIP, USU_RUT_DG FROM GREMI_FOR WHERE (USU_RUT=?) AND (FG_FEC BETWEEN '$fechaini1' AND '$fechafin1') AND (FG_TIP < 3) AND (FG_ASOCIACION = '$MuestroAsociacion') AND (FG_ESTADO='SOLICITADO')")){
          mysqli_stmt_bind_param($stmt, "s", $Srut);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $fghorutil, $fgminutil, $fgtip, $usurutdg);
          while(mysqli_stmt_fetch($stmt)){
            if($fgtip == 2 && $usurutdg == $Srut){
              $horasdir = $horasdir + $fghorutil;
            }else{
              $hora1 = $hora1+ $fghorutil;
              $minuto1 = $minuto1 + $fgminutil;
            }
          }                          
        }
        $horamin = intval($minuto1/60);
        if ($horamin < 1){
          $horamin=0;
        }
        $minmin = $minuto1%60;
        $sumtodo = $hora1 + $horamin;
        if($minmin =='0'){
          $minmin='00';
        }
        $suma1 = $sumtodo.":".$minmin;// horas y minutos utilizados      
        //horas disponibles
        $horasdisponibles = intval((($horasdir*60)-(($hora1*60)+$minuto1))/60);
        if($stmt = mysqli_prepare($cnn, "SELECT SUM(FG_HOR_UTIL) FROM GREMI_FOR WHERE (USU_RUT_DG=?) AND (FG_FEC_PER BETWEEN '$fechaini1' AND '$fechafin1')")){
          mysqli_stmt_bind_param($stmt, "s", $Srut);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $sumaht);
          mysqli_stmt_fetch($stmt);
          mysqli_stmt_close($stmt);          
        }
        $horasdisponibles = $horasdisponibles + $sumaht;         
        $fecha_hoy = date("Y-m-d");                  
        $hora = date("H:i:s");
        $ipcliente = getRealIP();
        $id_formulario = 45;//CAMBIAR DIRIGENTES
        $queryForm = "SELECT FOR_ESTA FROM FORMULARIO WHERE (FOR_ID = ".$id_formulario.")";
        $rsqF = mysqli_query($cnn, $queryForm);
        if (mysqli_num_rows($rsqF) != 0){
            $rowqF = mysqli_fetch_row($rsqF);
            if ($rowqF[0] == "ACTIVO"){
                //si formulario activo
                $queryAcceso = "SELECT AC_ID FROM ACCESO WHERE (USU_RUT = '".$Srut."') AND (FOR_ID = ".$id_formulario.")";
                $rsqA = mysqli_query($cnn, $queryAcceso);
                if (mysqli_num_rows($rsqA) != 0){                    
                }else{             
                    $accion = utf8_decode("ACCESO DENEGADO");
                    $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
                    mysqli_query($cnn, $insertAcceso);
                    header("location: ../error.php");
                }
            }else{
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
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
			<script type="text/javascript" src="../../include/js/jquery.min.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
                $('select').formSelect();
                $('.sidenav').sidenav();
                $(".dropdown-trigger").dropdown();
                $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
                $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
            });

            function Cargar1(){
                $('select').formSelect('destroy');
                $('#asociacion').formSelect();
            }
            function Cargar(){
                $("#horas").attr("disabled","disabled");
                $("#fecha_hora").attr("disabled","disabled");                
                $("#cant_horas").attr("disabled","disabled"); 
                $("#cant_minutos").attr("disabled","disabled");
                $("#hora_ini").attr("disabled","disabled");
                $("#hora_fin").attr("disabled","disabled");
                $("#horas_tras").attr("disabled","disabled");
                $("#horas_dis").attr("disabled","disabled");
                $("#dpusado").attr("disabled","disabled");
                $("#perfdias").attr("disabled","disabled");
                $("#fecha_perini").attr("disabled","disabled");
                $("#fecha_perfin").attr("disabled","disabled");
                $("#espedias").attr("disabled","disabled");
                $("#fecha_espeini").attr("disabled","disabled");
                $("#fecha_espefin").attr("disabled","disabled");
                $("#fecha_hora_cita").attr("disabled","disabled");                
                $("#cant_horas_cita").attr("disabled","disabled"); 
                $("#cant_minutos_cita").attr("disabled","disabled");
                $("#hora_ini_cita").attr("disabled","disabled");
                $("#hora_fin_cita").attr("disabled","disabled");
                $("#carga_arc").attr("disabled","disabled");
                $("#motivo").attr("disabled","disabled");
                $("#guardar").attr("disabled","disabled");
                $("#dirigente").attr("disabled","disabled");
                $("#dirigente").formSelect('');
                $("#dirigente2").attr("disabled","disabled");
                $("#dirigente2").formSelect(''); 
                $("#dirigente3").attr("disabled","disabled");
                $("#dirigente3").formSelect('');  
            }

            function Respuesta(r){
                var doc_id = r.doc_id;
                var horasdir = r.gdhora+":00";
                var usado = r.usado;
                                             
                $("#Tipo_Doc").val(doc_id);
                //M.toast({html: 'ID: '+doc_id});
                if (doc_id == 1){ //fuero
                    //HORAS DISPONIBLES
                    $("#horas_dir").val(horasdir);
                    $("#hora_usado").val(usado); 

                    inicioMinutos = parseInt(horasdir.substr(3,2));
                    inicioHoras = parseInt(horasdir.substr(0,2));
                    
                    finMinutos = parseInt(usado.substr(3,2));
                    finHoras = parseInt(usado.substr(0,2));

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
                    horas = horas * -1;
                    
                    var hd = horas+":"+minutos;
                    $("#horas_dis").val(hd);

                   
                    //#################


                    if(usado== null){
                      usado =0;
                    }
                      M.toast({html: 'Horas utilizadas '+usado}); 
                    if(document.getElementById("fuero").checked) {
                      $("#fecha_hora").removeAttr("disabled");                        
                    }
                        
                }else if(doc_id ==4){
                    if(usado== null){
                       usado =0;
                    }
                    if(usado <0){
                      usado1 = usado * -1;
                      M.toast({html: 'Días Adicionales '+usado1});
                      //usado=0;
                    }else{
                      M.toast({html: 'Días utilizados '+usado}); 
                    }
                    
                    $("#dpusado").val(usado);
                    if(usado>=5){
                      M.toast({html: 'No dispone de días de perfeccionamiento '});
                    }else{
                      $("#perfdias").removeAttr("disabled");                          
                    }
                }else if(doc_id == 5){
                    if(usado== null){
                       usado =0;
                    }
                    if(usado <0){
                      usado1 = usado * -1;
                      M.toast({html: 'Días Adicionales '+usado1});
                      //usado=0;
                    }else{
                      M.toast({html: 'Días utilizados '+usado}); 
                    }
                    
                    $("#esusado").val(usado);
                    if(usado>=5){
                      M.toast({html: 'No dispone de días especiales '});
                    }else{
                      $("#espedias").removeAttr("disabled");                          
                    }                    
                }
                               
            }

            function CalcularPerfe(){                            
                var doc_id = 4;
                var asocia = $('#asociacion').val();
                if(asocia !=''){
                  var post = $.post("../php/calcular_fuero.php", { "doc_id" : doc_id,"asocia" : asocia }, Respuesta, 'json');
                }else{
                  M.toast({html: 'Favor seleccione una organización'});
                }
            }           

            function PermisoEspecial(){
              var doc_id = 5;
              var asocia = $('#asociacion').val();
              if(asocia !=''){
                var post = $.post("../php/calcular_fuero.php", { "doc_id" : doc_id,"asocia" : asocia }, Respuesta, 'json');
              }else{
                M.toast({html: 'Favor seleccione una organización'});                
              }                   
            }
            
            function Limpiar(){
              var t = document.getElementById("formSolPermi").getElementsByTagName("input");
              for (var i=0; i<t.length; i++) {
                  if(i>10){                    
                   t[i].value = "";                     
                  }                  
              }
            }
            function CalcularFuero(){                
                var doc_id = 1;
                var asocia = $('#asociacion').val();
                if(asocia !=''){
                  var post = $.post("../php/calcular_fuero.php", { "doc_id" : doc_id,"asocia" : asocia }, Respuesta, 'json');
                }else{
                  M.toast({html: 'Favor seleccione una organización'});
                  document.getElementById("asociacion").focus();
                }                                
            }
					function CalcularTraspaso(){
						var doc_id = 2;
						var asoc = $('#asociacion').val();
						var rut = "<?php echo $Srut;?>";
						if(asoc != ''){
							if(asoc != 'NACIONAL'){
								if(asociacion != 'REGIONAL'){
									$("#dirigente").removeAttr("disabled");
									$("#dirigente").formSelect('');                       
									$("#Tipo_Doc").val(doc_id);
								}
							}    
						}else{
							M.toast({html: 'Favor seleccione una organización'});
							document.getElementById("asociacion").focus();
						}                                   
					}
            

            function ValidarHoraIni(rfr){
                var FeriadoFecHora = rfr.dia;
                if(FeriadoFecHora == "si"){
                    M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                    $("#fecha_hora").val("");
                }else if(FeriadoFecHora == "no"){                   
                    $("#hora_ini").removeAttr("disabled");
                    $("#hora_fin").removeAttr("disabled");
                }
            }
            function MostrarHoraIni(){
                var ValFecha = $("#fecha_hora").val();
                var post = $.post("../php/revisar_feriado.php", { "fecha" : ValFecha }, ValidarHoraIni, 'json');
            }
            function MostrarCantHoras(){
                $("#cant_horas").removeAttr("disabled");
            }
            function CalcularHoraFin(){
                var hini = $("#hora_ini").val();
                var hfin = $("#hora_fin").val();
                if(hini < hfin){
                      var minutos_inicio = hini.split(':')
                      .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
                      var minutos_final = hfin.split(':')
                        .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
                      if (minutos_final < minutos_inicio) return;
                      var diferencia = minutos_final - minutos_inicio;

                      // Cálculo de horas y minutos de la diferencia
                      var horas = Math.floor(diferencia / 60);
                      var minutos = diferencia % 60;
                  
                      if (horas ==""){
                        $("#cant_horas").val("0");
                      }else{
                        $("#cant_horas").val(horas);
                        Motivo();
                      }
                      if(minutos ==""){
                        $("#cant_minutos").val("0");
                      }else{
                        //$("#cant_minutos").val(minutos);
                        $("#cant_minutos").val("0");
                        horas = horas +1;
                        $("#cant_horas").val(horas);
                      }

                }else{
                  $("#cant_horas").val("");
                  $("#hora_ini").val("");
                  $("#hora_fin").val("");
                }
                
            }
            function HorasTras(){
              //$("#dirigente").removeAttr("disabled");
              //$("#dirigente").formSelect('');
              $("#horas_tras").removeAttr("disabled");
            }
            function ValidaSaldo(){
                var ht = parseInt($('#horas_tras').val());               
                var hd = parseInt($('#horas_dis').val());
                if(ht != '' && ht > 0){
                  if(hd>=ht){
                    $("#motivo").removeAttr("disabled");
                  }else{
                     M.toast({html: 'Traspaso no disponible'});
                     $("#horas_tras").val("");
                     $("#motivo").val("");
                     $("#motivo").attr("disabled","disabled");
                     $('#jefatura').formSelect('destroy');
                     $("#guardar").attr("disabled","disabled");
                  }
                }else{
                     $("#horas_tras").val("");
                     $("#motivo").val("");
                     $("#motivo").attr("disabled","disabled"); 
                     $('#jefatura').formSelect('destroy');
                     $("#guardar").attr("disabled","disabled");
                }
            }
            function FecPerIni(){
                var ValFecha = $("#fecha_perini").val();
                var post = $.post("../php/revisar_feriado.php", { "fecha" : ValFecha }, ValidarPerIni, 'json');
            }
            function FecPerFin(){
                var ValFecha = $("#fecha_perfin").val();
                var post = $.post("../php/revisar_feriado.php", { "fecha" : ValFecha }, ValidarPerFin, 'json');
            }
            function ValidarPerIni(rfr){
                var FeriadoFecHora = rfr.dia;
                if(FeriadoFecHora == "si"){
                    //tiene que cambiar de dia
                    M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                    $("#fecha_perini").val("");
                    $("#fecha_perfin").val("");
                    $("#fecha_perfin").attr("disabled","disabled");
                }
            }
            function ValidarPerFin(rfr){
                var FeriadoFecHora = rfr.dia;
                if(FeriadoFecHora == "si"){
                    M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                    $("#fecha_perini").val("");
                    $("#fecha_perfin").val("");
                    $("#fecha_perfin").attr("disabled","disabled");
                }
            }
            
            function MostrarFinicio(){
                var DiasPendientes = $("#dpusado").val();
                var CantDias = $("#perfdias").val();
                DiasPendientes = 5 - DiasPendientes;
                var dif = DiasPendientes - CantDias;
                if(dif >= 0){
                    $("#fecha_perini").removeAttr("disabled");
                }else{
                    var dif = CantDias - DiasPendientes;
                    M.toast({html: 'Excede en: '+dif});
                    $("#fecha_perini").val("");
                    $("#fecha_perfin").val("");
                    $("#fecha_perfin2").val("");
                    $("#fecha_perini").attr("disabled","disabled");
                    $("#perfdias").val("");
                    $("#motivo").attr("disabled","disabled");
                }
            }
          
            function MostrarFechaFIN(mff){
            	var fechaFIN = mff.fechaFIN;
              var finano = '<?php echo $fecano;?>';              
              var fechaano = new Date(mff.fechaFIN);
              var ano = fechaano.getFullYear();
            
              if(finano < ano){
                $("#fecha_perini").val("");
                $("#fecha_perfin").val("");
                $("#fecha_perfin2").val("");
                $("#motivo").val("");
                $("#motivo").attr("disabled","disabled");
                M.toast({html: 'Fecha no válida'});
              }else{                
                $("#fecha_perfin").val(fechaFIN);
                $("#fecha_perfin2").val(fechaFIN);
                $("#motivo").removeAttr("disabled");
                if(document.getElementById("perfetras").checked) {
                  $("#dirigente2").removeAttr("disabled");
                  $("#dirigente2").formSelect('');                       
                }                
              }              
						}
	          function SegundaValidacionFechaINI(svfi){
                var FeriadoFecHora = svfi.dia;
                if(FeriadoFecHora == "si"){     
                    M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                    $("#fecha_perini").val("");
                }else if(FeriadoFecHora == "no"){
                	var CantDias = $("#perfdias").val();
                  var FechaInicio = $("#fecha_perini").val();									
                  var post = $.post("../php/validar_fechaTermino.php", { "TipoDoc" : 1, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN, 'json');
                }
            }
            function ValidoFechaINI(){
                var ValFecha = $("#fecha_perini").val();
                var post = $.post("../php/revisar_feriado.php", { "fecha" : ValFecha }, SegundaValidacionFechaINI, 'json');
            }
          //DIAS ESPECIALES ###########
            function MostrarFinicio2(){
                var DiasPendientes = $("#esusado").val();
                var CantDias = $("#espedias").val();
                DiasPendientes = 5 - DiasPendientes;
                var dif = DiasPendientes - CantDias;
                if(dif >= 0){
                    $("#fecha_espeini").removeAttr("disabled");
                }else{
                    var dif = CantDias - DiasPendientes;
                    M.toast({html: 'Excede en: '+dif});
                    $("#fecha_espeini").val("");
                    $("#fecha_espefin").val("");
                    $("#fecha_espefin2").val("");
                    $("#fecha_espeini").attr("disabled","disabled");
                    $("#espedias").val("");
                    $("#motivo").attr("disabled","disabled");
                }
            }
          
            function MostrarFechaFIN2(mff){
            	var fechaFIN = mff.fechaFIN;
              var finano = '<?php echo $fecano;?>';              
              var fechaano = new Date(mff.fechaFIN);
              var ano = fechaano.getFullYear();
            
              if(finano < ano){
                $("#fecha_espeini").val("");
                $("#fecha_espefin").val("");
                $("#fecha_espefin2").val("");
                $("#motivo").val("");
                $("#motivo").attr("disabled","disabled");
                M.toast({html: 'Fecha no válida'});
              }else{                
                $("#fecha_espefin").val(fechaFIN);
                $("#fecha_espefin2").val(fechaFIN);
                $("#motivo").removeAttr("disabled");
                if(document.getElementById("espetras").checked) {
                  $("#dirigente3").removeAttr("disabled");
                  $("#dirigente3").formSelect('');                       
                }                   
              }              
						}
            function SegundaValidacionFechaINI2(svfi){
                var FeriadoFecHora = svfi.dia;
                if(FeriadoFecHora == "si"){  
                    M.toast({html: 'Dia no habil, ingresar fecha de nuevo'});
                    $("#fecha_espeini").val("");
                }else if(FeriadoFecHora == "no"){
                	var CantDias = $("#espedias").val();
                  var FechaInicio = $("#fecha_espeini").val();									
                  var post = $.post("../php/validar_fechaTermino.php", { "TipoDoc" : 1, "cantDIAS" : CantDias, "fechaINI" : FechaInicio }, MostrarFechaFIN2, 'json');
                }
            }
            function ValidoFechaINI2(){
                var ValFecha = $("#fecha_espeini").val();
                var post = $.post("../php/revisar_feriado.php", { "fecha" : ValFecha }, SegundaValidacionFechaINI2, 'json');
            }
         //########################################
            function VerCita(){
              $("#Tipo_Doc").val(3);              
              var asocia = $('#asociacion').val();
                if(asocia !=''){
                  //var post = $.post("../php/calcular_fuero.php", { "doc_id" : doc_id,"asocia" : asocia }, Respuesta, 'json');
                  $("#fecha_hora_cita").removeAttr("disabled");
                }else{
                  M.toast({html: 'Favor seleccione una organización'});
                  document.getElementById("asociacion").focus();
                }                                  
            }
            function MostrarHoraIniCita(){
              $("#hora_ini_cita").removeAttr("disabled");
              $("#hora_fin_cita").removeAttr("disabled");
            }
            function CalcularHoraIniCita(){
              $("#hora_fin_cita").val("");
              $("#cant_horas_cita").val("");
              $("#cant_minutos_cita").val("");
            }
             function CalcularHoraFinCita(){
                var hini = $("#hora_ini_cita").val();
                var hfin = $("#hora_fin_cita").val();
                if(hini < hfin){
                  var minutos_inicio = hini.split(':')
                  .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
                  var minutos_final = hfin.split(':')
                    .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
                  if (minutos_final < minutos_inicio) return;
                  var diferencia = minutos_final - minutos_inicio;
                  var horas = Math.floor(diferencia / 60);
                  var minutos = diferencia % 60;
                  if (horas ==""){
                    $("#cant_horas_cita").val("0");
                  }else{
                    $("#cant_horas_cita").val(horas);
                    Motivo();
                  }
                  if(minutos ==""){
                    $("#cant_minutos_cita").val("0");
                  }else{
                    //$("#cant_minutos_cita").val(minutos);
                    $("#cant_minutos_cita").val("0");
                    horas= horas +1;
                    $("#cant_horas_cita").val(horas);
                  }
                 
                }else{
                  $("#cant_horas_cita").val("");
                  $("#cant_minutos_cita").val("");
                  $("#hora_ini_cita").val("");
                  $("#hora_fin_cita").val("");
                  $
                }
                
            }
                      
            function Motivo(){
                $("#motivo").removeAttr("disabled");
            }
            function Jefatura(){
                var motivo2 = $("#motivo").val();
                if(motivo2 == ''){                    
                     $('#jefatura').formSelect('destroy');
                     $("#guardar").attr("disabled","disabled");
                }else{
                  $("#carga_arc").removeAttr("disabled");
                  $('#jefatura').formSelect();
                }
            }
            function Listo(){
                $("carga_arc").removeAttr("disabled");
                $("#guardar").removeAttr("disabled");
            }
            function Habilita(){
                $("#cant_horas").removeAttr("disabled");
                $("#cant_minutos").removeAttr("disabled");
                $("#cant_horas_cita").removeAttr("disabled");
                $("#cant_minutos_cita").removeAttr("disabled");
            }
            function soloNumeros(e){
                var key = window.Event ? e.which : e.keyCode
                return (key >= 48 && key <= 57 || key == 127 || key == 08)
            }
            function CargaArc(){
              window.open('../php/cargar_gremial.php'+"?id1="+"<?php echo $idpermi;?>"+"&id2="+"11", "Subir Archivo" , "width=650,height=450,scrollbars=yes,menubar=yes,toolbar=yes,location=no");  
            }

        </script>
    </head>
    <body onload="Cargar();Cargar1();">
        <?php require_once('../estructura/nav_personal.php');?>
        </br>
        </br>
        </br>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <h4 class="light">Formulario Fuero Gremial</h4>
                      <!-- <?php echo $idpermi; ?> -->
                         <form name="formSolPermi" class="col s12" method="post" id="formSolPermi">                           
                            </br>
                            </br>    
                            <input type="text" name="Tipo_Doc" id="Tipo_Doc" class="validate" style="display: none">
                            <input type="text" name="fl" id="fl" class="validate" style="display: none">
                            <input type="text" name="fla" id="fla" class="validate" style="display: none">
                            </br>
                            </br>
                            <div class="input-field col s2">
                                <input type="text" name="rut_usuario" id="rut_usuario" class="validate" placeholder="" value="<?php echo $Srut;?>" disabled>
                                <label for="rut_usuario">RUT</label>
                            </div>
                            <div class="input-field col s4">
                                <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" value="<?php echo $Snombre." ".$SapellidoP." ".$SapellidoM;?>" disabled>
                                <label for="nombre_usuario">Nombre Completo Dirigente</label>
                            </div>
                             <div class="input-field col s4" >
                                    <select name="asociacion" id="asociacion" onchange="Cargar();CalcularFuero();">
                                      <?php                                       
                                         $queryDir = "SELECT GD_ASOCIACION, GD_HORA FROM GREMI_DIR WHERE (GD_ESTA = 'ACTIVO' AND USU_RUT = '$Srut' AND GD_FEC_FIN >= '$hoy')";
                                         $resultadoD =mysqli_query($cnn, $queryDir);
                                         printf("<option></option>");    
                                         while($regD =mysqli_fetch_array($resultadoD)){
                                          $MuestroAsociacion = $regD[0];                                          
                                          printf("<option value=\"$regD[0]\">$MuestroAsociacion</option>");
                                         }  

                                      ?>
                                     </select>
                                     <label for="asociacion">Seleccione Organización</label>
                                 </div>      
                            <div class="input-field col s12">
                                          <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                            </div>                 
                            <div class="input-field col s3">
                                <input type="text" name="horas_dir" id="horas_dir" class="validate" placeholder="" value="<?php echo $horasdir;?>" disabled>
                                <label for="horas_dir">Horas de Fuero</label>
                            </div>
                            <div class="input-field col s3">
                                <input type="text" name="hora_usado" id="hora_usado" class="validate" placeholder="" value="<?php echo $suma1;?>" disabled>
                                <label for="hora_usado">Horas Utilizada</label>
                            </div>
                            <div class="input-field col s3">
                                <input type="text" name="dpusado" id="dpusado" class="validate" placeholder="" value="<?php echo $anofin1;?>" disabled>
                                <label for="dpusado">Días Perf. Utilizada</label>
                            </div>
                            <div class="input-field col s3">
                                <input type="text" name="esusado" id="esusado" class="validate" placeholder="" disabled>
                                <label for="esusado">Permiso Espcial</label>
                            </div>
                            <div class="col s12" align="left"><h6>Uso Fuero Ley N° 19.296 :</h6></div>
                            <div class="input-field col s12">
                                          <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                            </div>                            
                                <div class="col s12" align="left">
                                  <label>
                                    <input class="with-gap col s3" name="seleccion" value="1" type="radio" id="fuero" onclick="Limpiar();Cargar();CalcularFuero();">
                                    <span>Fuero Gremial</span>
                                  </label>
                                </div>
                                </br>
                                </br>
                                <div class="input-field col s12">
                                          <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s2">
                                    <input type="text" class="datepicker" name="fecha_hora" id="fecha_hora" onchange="MostrarHoraIni();" placeholder="Fecha"> 
                                </div> 

                                <div class="input-field col s2">
                                    <input id="hora_ini" name="hora_ini" class="timepicker" type="text"  placeholder="Hora Inicio">
                                </div>                                        

                                <div class="input-field col s2">
                                    <input id="hora_fin" name="hora_fin" class="timepicker" type="text" onchange="CalcularHoraFin();"  placeholder="Hora Fin">
                                </div>                                        

                                <div class="input-field col s2">
                                    <input type="text" name="cant_horas" id="cant_horas" class="validate" placeholder="">
                                    <label for="icon_prefix">Horas</label>
                                </div>

                                <div class="input-field col s2">
                                    <input style="display:none" type="text" name="cant_minutos" id="cant_minutos" class="validate" placeholder="">
                                   <!-- <label for="icon_prefix">Minutos</label>-->
                                </div>
                                <div class="input-field col s2">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="input-field col s12">
                                          <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <div class="col s12" align="left">
                                  <label>
                                    <input class="with-gap col s12" name="seleccion" value="2" type="radio" id="traspaso" onclick="Limpiar();Cargar();CalcularTraspaso();CalcularFuero();" />
                                    <span>Traspaso Hora</span>
                                  </label>
                                </div>
                                <div class="input-field col s12">
                                          <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>                               

                                 <div class="input-field col s4" >
                                    <select name="dirigente" id="dirigente" onchange="HorasTras();">
                                         <?php                                                                                
                                          $queryDir = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,GREMI_DIR.GD_CARGO FROM USUARIO INNER JOIN GREMI_DIR ON USUARIO.USU_RUT=GREMI_DIR.USU_RUT WHERE (GREMI_DIR.GD_ESTA = 'ACTIVO' AND GREMI_DIR.USU_RUT <> '$Srut' AND GD_FEC_FIN >= '$hoy' AND GD_ASOCIACION <>'NACIONAL' AND GD_ASOCIACION <>'REGIONAL')";
                                         $resultadoD =mysqli_query($cnn, $queryDir);
                                         printf("<option></option>"); 
                                         while($regD =mysqli_fetch_array($resultadoD)){
                                          $MuestroDire = $regD[1]." ".$regD[2]." ".$regD[3];
                                          printf("<option value=\"$regD[0]\">$MuestroDire</option>");
                                         }
                                      ?>
                                     </select>
                                     <label for="dirigente">Seleccione Dirigente</label>
                                 </div>                                
                                  <div class="input-field col s4">
                                      <input type="text" name="horas_tras" id="horas_tras" class="validate" placeholder="" onkeypress="return soloNumeros(event)" onchange="ValidaSaldo();">                               
                                      <label for="icon_prefix">Horas a Traspasar</label>
                                  </div>                                  
                                  <div class="input-field col s4">
                                      <input type="text" name="horas_dis" id="horas_dis" class="validate" placeholder="" value="<?php echo $horasdisponibles; ?>" onkeypress="return soloNumeros(event)">          
                                      <label for="icon_prefix">Horas Disponibles</label>
                                  </div>
                                  <div class="input-field col s12">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  <div class="col s12" align="left"> 
                                  <label>
                                    <input class="with-gap col s3" name="seleccion" value="3" type="radio" id="cita" onclick="Limpiar();Cargar();VerCita();"/>
                                    <span>Cita Autoridad Pública</span>
                                  </label>
                                  </div>
                                  <div class="input-field col s12">
                                          <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  <div class="input-field col s2">
                                      <input type="text" class="datepicker" name="fecha_hora_cita" id="fecha_hora_cita" onchange="MostrarHoraIniCita();" placeholder="Fecha"> 
                                  </div> 

                                  <div class="input-field col s2">
                                      <input id="hora_ini_cita" name="hora_ini_cita" class="timepicker" type="text"  placeholder="Hora Inicio" onchange="CalcularHoraIniCita();">
                                  </div>                                        

                                  <div class="input-field col s2">
                                      <input id="hora_fin_cita" name="hora_fin_cita" class="timepicker" type="text" onchange="CalcularHoraFinCita();"  placeholder="Hora Fin">
                                  </div>                                        

                                  <div class="input-field col s2">
                                      <input type="text" name="cant_horas_cita" id="cant_horas_cita" class="validate" placeholder="">
                                     <!--<input type="text" name="horas_pendientes" id="horas_pendientes" class="validate" style="display: none">-->
                                      <label for="icon_prefix">Horas</label>
                                  </div>

                                  <div class="input-field col s2">
                                      <input style="display:none" type="text" name="cant_minutos_cita" id="cant_minutos_cita" class="validate" placeholder="">
                                      <!--<label for="icon_prefix">Minutos</label>-->
                                  </div>
                                  <div class="input-field col s2">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  <div class="col s6" align="left">  
                                  <label>
                                    <input class="with-gap col s6" name="seleccion" value="4" type="radio" id="perfe" onclick="Limpiar();Cargar();CalcularPerfe();"/>
                                    <span>Permiso Actividades o Perfeccionamiento</span>
                                  </label>
                                  </div>
                                  <div class="col s6" align="left">  
                                  <label>
                                    <input class="with-gap col s6" name="seleccion" value="4" type="radio" id="perfetras" onclick="Limpiar();Cargar();CalcularPerfe();"/>
                                    <span>Traspaso</span>
                                  </label>
                                  </div>
                                  <div class="input-field col s12">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  <div class="input-field col s2">
                                      <input type="text" name="perfdias" id="perfdias" class="validate" placeholder="" onkeyup="MostrarFinicio();" onkeypress="return soloNumeros(event)"/>
                                      <label for="categoria_usuario">Días a Utilizar</label>
                                  </div>
                                  <div class="input-field col s2">
                                      <input type="text" class="datepicker" name="fecha_perini" id="fecha_perini" onchange="ValidoFechaINI();" placeholder="Fecha"> 
                                  </div>
                                  <div class="input-field col s2">
                                      <input type="text" class="datepicker" name="fecha_perfin" id="fecha_perfin" placeholder="Fecha de Termino" disabled> 
                                  </div> 
                                  <div class="input-field col s4" >
                                    <select name="dirigente2" id="dirigente2">
                                         <?php                                                                                
                                          $queryDir = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,GREMI_DIR.GD_CARGO FROM USUARIO INNER JOIN GREMI_DIR ON USUARIO.USU_RUT=GREMI_DIR.USU_RUT WHERE (GREMI_DIR.GD_ESTA = 'ACTIVO' AND GREMI_DIR.USU_RUT <> '$Srut' AND GD_FEC_FIN >= '$hoy' AND GD_ASOCIACION <>'NACIONAL' AND GD_ASOCIACION <>'REGIONAL')";
                                         $resultadoD =mysqli_query($cnn, $queryDir);
                                         printf("<option></option>"); 
                                         while($regD =mysqli_fetch_array($resultadoD)){
                                          $MuestroDire2 = $regD[1]." ".$regD[2]." ".$regD[3];
                                          printf("<option value=\"$regD[0]\">$MuestroDire2</option>");
                                         }
                                      ?>
                                     </select>
                                     <label for="dirigente2">Seleccione Dirigente</label>
                                 </div>                                                                      
                                  <div class="input-field col s2">
                                      <input style="display:none" type="text" class="datepicker" name="fecha_perfin2" id="fecha_perfin2"> 
                                  </div> 
                                  <div class="input-field col s12">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div> 
                                  <div class="col s6" align="left">  
                                    <label>
                                      <input class="with-gap col s6" name="seleccion" value="5" type="radio" id="espe" onclick="Limpiar();Cargar();PermisoEspecial();"/>
                                      <span>Permiso Especial Adicional</span>
                                    </label>
                                  </div>
                                  <div class="col s6" align="left">  
                                  <label>
                                    <input class="with-gap col s6" name="seleccion" value="5" type="radio" id="espetras" onclick="Limpiar();Cargar();PermisoEspecial();"/>
                                    <span>Traspaso</span>
                                  </label>
                                  </div>
                                  <div class="input-field col s12">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  <div class="input-field col s2">
                                      <input type="text" name="espedias" id="espedias" class="validate" placeholder="" onkeyup="MostrarFinicio2();" onkeypress="return soloNumeros(event)"/>
                                      <label for="categoria_usuario">Días a Utilizar</label>
                                  </div>
                                  <div class="input-field col s3">
                                      <input type="text" class="datepicker" name="fecha_espeini" id="fecha_espeini" onchange="ValidoFechaINI2();" placeholder="Fecha"> 
                                  </div>
                                  <div class="input-field col s3">
                                      <input type="text" class="datepicker" name="fecha_espefin" id="fecha_espefin" placeholder="Fecha de Termino" disabled> 
                                  </div>
                                  <div class="input-field col s4" >
                                    <select name="dirigente3" id="dirigente3">
                                         <?php                                                                                
                                          $queryDir = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,GREMI_DIR.GD_CARGO FROM USUARIO INNER JOIN GREMI_DIR ON USUARIO.USU_RUT=GREMI_DIR.USU_RUT WHERE (GREMI_DIR.GD_ESTA = 'ACTIVO' AND GREMI_DIR.USU_RUT <> '$Srut' AND GD_FEC_FIN >= '$hoy' AND GD_ASOCIACION <>'NACIONAL' AND GD_ASOCIACION <>'REGIONAL')";
                                         $resultadoD =mysqli_query($cnn, $queryDir);
                                         printf("<option></option>"); 
                                         while($regD =mysqli_fetch_array($resultadoD)){
                                          $MuestroDire2 = $regD[1]." ".$regD[2]." ".$regD[3];
                                          printf("<option value=\"$regD[0]\">$MuestroDire2</option>");
                                         }
                                      ?>
                                     </select>
                                     <label for="dirigente2">Seleccione Dirigente</label>
                                 </div>                     
                                  <div class="input-field col s0">
                                      <input style="display:none" type="text" class="datepicker" name="fecha_espefin2" id="fecha_espefin2"> 
                                  </div> 
                                  <div class="input-field col s12">
                                      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  
                                  <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                                  <div class="input-field col s12">
                                          <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                  </div>
                            <div class="input-field col s12">
                            <!-- <input type="text" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"> -->
                                <input type="text" name="motivo" id="motivo" class="validate" placeholder="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloLetras(event)" onchange="Jefatura();" required>
                                <label for="icon_prefix">Motivo</label>
                            </div>
                             <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                            </div>
                            <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                            </div>
                            <div class="input-field col s12" >
                                <select name="jefatura" id="jefatura" onchange ="Listo();">
                                    <?php
                                        $queryJefatura = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM USUARIO, ESTABLECIMIENTO WHERE (USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID) AND (USUARIO.USU_JEF = 'SI') AND ((ESTABLECIMIENTO.EST_NOM = '$Sdependencia') OR (ESTABLECIMIENTO.EST_NOM = 'MULTIESTABLECIMIENTO'))";
                                        $resultadoJ =mysqli_query($cnn, $queryJefatura);
                                            while($regJ =mysqli_fetch_array($resultadoJ)){
                                                $MuestroJefatura = $regJ[1]." ".$regJ[2]." ".$regJ[3];
                                                printf("<option value=\"$regJ[0]\">$MuestroJefatura</option>");
                                            }
                                            //echo "<option value='no' selected>Jefe Directo</option>";
                                    ?>
                                </select>
																<label for="jefatura">Jefe Directo</label>
                            </div>
                            <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                            </div>
                            <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                            </div>
                            <div class="input-field col s12">
                              <button class="btn trigger" name="carga_arc" id="carga_arc" type="button" onclick="CargaArc();">Adjuntar Convocatoria</button>
                            </div>                           
                            <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                            </div>
                            <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                            </div>
                            <div class="col s12">
                                <button id="guardar" type="submit" class="btn trigger" name="guardar" value="Guardar" onclick="Habilita();">Enviar</button>
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
<script type="text/javascript" src="../../include/js/jquery.min.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.js"></script>
        <script type="text/javascript" src="../../include/js/materialize.clockpicker.min.js"></script>
        <script>
            $(document).ready(function () {
                //Animaciones 
              $('.sidenav').sidenav();
              $(".dropdown-trigger").dropdown();
            });
        </script>
        <?php
          if($_POST['guardar'] == "Guardar"){
              $fgtip = $_POST['Tipo_Doc'];
              $usurut = $Srut;
              $usurutjd = $_POST['jefatura'];
              $fecper = $dc_fec;
              $fegmotivo = $_POST['motivo'];
              $fgasocia = $_POST['asociacion'];            
              if($fgtip == 1){
                  $fec = $_POST['fecha_hora'];
                  $feghorini = $_POST['hora_ini'];
                  $feghorfin = $_POST['hora_fin'];                   
                  $feghorutil = $_POST['cant_horas'];
                  $fegminutil = $_POST['cant_minutos'];
                  $guarda_fuero = "UPDATE GREMI_FOR SET FG_TIP ='$fgtip', USU_RUT = '$Srut', USU_RUT_JD='$usurutjd', FG_FEC_PER='$fec', FG_FEC='$dc_fec', FG_HOR_INI='$feghorini', FG_HOR_FIN='$feghorfin', FG_HOR_UTIL='$feghorutil', FG_MIN_UTIL='$fegminutil', FG_MOTIVO='$fegmotivo', FG_ESTADO='SOLICITADO', FG_ASOCIACION = '$fgasocia'  WHERE (FG_ID = $idpermi) AND (FG_ESTADO ='EN CREACION')";
                  mysqli_query($cnn, $guarda_fuero);
              }elseif($fgtip == 2){
                  $usurutdg = $_POST['dirigente'];  
                  $feghorutil = $_POST['horas_tras'];
                  $feghordis = $horasdisponibles;
                  $guarda_traspaso="UPDATE GREMI_FOR SET FG_TIP ='$fgtip', USU_RUT = '$Srut', USU_RUT_JD='$usurutjd', FG_FEC_PER='$dc_fec', FG_FEC='$dc_fec', FG_HOR_UTIL='$feghorutil', USU_RUT_DG='$usurutdg', FG_HOR_DIS= '$feghordis', FG_MOTIVO= '$fegmotivo', FG_ESTADO='SOLICITADO', FG_ASOCIACION = '$fgasocia' WHERE (FG_ID = $idpermi) AND (FG_ESTADO ='EN CREACION')";
                  mysqli_query($cnn, $guarda_traspaso);
              }elseif($fgtip == 3){
                  $fegfec = $_POST['fecha_hora_cita'];
                  $feghorini = $_POST['hora_ini_cita'];
                  $feghorfin = $_POST['hora_fin_cita'];
                  $feghorutil = $_POST['cant_horas_cita'];
                  $fegminutil = $_POST['cant_minutos_cita'];                   
                  $guarda_cita="UPDATE GREMI_FOR SET FG_TIP ='$fgtip', USU_RUT = '$Srut', USU_RUT_JD='$usurutjd', FG_FEC_PER='$fegfec', FG_FEC='$dc_fec', FG_HOR_INI='$feghorini', FG_HOR_FIN='$feghorfin', FG_HOR_UTIL='$feghorutil', FG_MIN_UTIL='$fegminutil', FG_MOTIVO='$fegmotivo', FG_ESTADO='SOLICITADO' WHERE (FG_ID = $idpermi) AND (FG_ESTADO ='EN CREACION')";
                  mysqli_query($cnn, $guarda_cita);
                  
              }elseif($fgtip == 4){
                  $usurutdg2 = $_POST['dirigente2'];
                  $fegdiautil = $_POST['perfdias'];
                  $fegfecini = $_POST['fecha_perini'];             
                  $fegfecfin = $_POST['fecha_perfin2'];
                  $guarda_perf="UPDATE GREMI_FOR SET FG_TIP='$fgtip', USU_RUT='$usurut', USU_RUT_JD='$usurutjd', FG_FEC_PER='$fecper', FG_FEC='$fecper', USU_RUT_DG='$usurutdg2' ,FG_DIA_UTIL='$fegdiautil', FG_FEC_INI='$fegfecini', FG_FEC_FIN= '$fegfecfin', FG_MOTIVO='$fegmotivo', FG_ESTADO='SOLICITADO', FG_ASOCIACION = '$fgasocia' WHERE (FG_ID = $idpermi) AND (FG_ESTADO ='EN CREACION')";
                  mysqli_query($cnn, $guarda_perf);   
              }elseif($fgtip == 5){
                  $fegdiautil = $_POST['espedias'];
                  $fegfecini = $_POST['fecha_espeini'];             
                  $fegfecfin = $_POST['fecha_espefin2'];                  
                  $guarda_espe="UPDATE GREMI_FOR SET FG_TIP='$fgtip', USU_RUT='$usurut', USU_RUT_JD='$usurutjd', FG_FEC_PER='$fecper', FG_FEC='$fecper', FG_DIA_UTIL='$fegdiautil', FG_FEC_INI='$fegfecini', FG_FEC_FIN= '$fegfecfin', FG_MOTIVO='$fegmotivo', FG_ESTADO='SOLICITADO', FG_ASOCIACION = '$fgasocia' WHERE (FG_ID = $idpermi) AND (FG_ESTADO ='EN CREACION')";
                 mysqli_query($cnn, $guarda_espe);

              }

                  $rowCID = mysqli_fetch_row($rsCID);
                  $Id_for_actual = $rowCID[0];
                  $FecActual = date("Y-m-d");
                  $HorActual = date("H:i:s");
                  $HPAccion = "CREA FUERO GREMIAL";
                  $guardar_historial = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($idpermi,'$Srut','$FecActual','$HorActual',11, '$HPAccion')";
                  $guardar_historial;
                  mysqli_query($cnn, $guardar_historial);
                  ?> <script type="text/javascript"> window.location="../index.php";</script>  <?php

            }
        ?>
    </body>
</html>