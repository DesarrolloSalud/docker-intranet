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
  header("location: ../../../index.php");
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
  $ano6 = date("Y");
  $adescu= date("Y");
  $hora = date("H:i:s");
  $ipcliente = getRealIP();
  $rut1 = $_GET['rut'];
  include ("../../../include/funciones/funciones.php");
  $cnn = ConectarPersonal();
  $buscar = "SELECT USUARIO.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_MAIL,USUARIO.USU_DIR,USUARIO.USU_FONO,USUARIO.USU_CAR, ESTABLECIMIENTO.EST_NOM,USUARIO.USU_DEP,USUARIO.USU_ESTA,USUARIO.USU_PAS,USUARIO.USU_CAT,USUARIO.USU_NIV,USUARIO.USU_JEF,USUARIO.USU_FEC_ING,USUARIO.USU_FEC_INI FROM USUARIO INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID = ESTABLECIMIENTO.EST_ID WHERE (USUARIO.USU_RUT = '".$rut1."')";
  $rs = mysqli_query($cnn, $buscar);
  if($row = mysqli_fetch_array($rs)){
    $MuestroRut=$row[0];
    $MuestroNombre=utf8_encode($row[1]);
    $MuestroApellidoP = utf8_encode($row[2]);
    $MuestroApellidoM = utf8_encode($row[3]);
    $MuestroEmail = utf8_encode($row[4]);
    $MuestroDireccion = utf8_encode($row[5]);
    $MuestroFono = utf8_encode($row[6]);
    $MuestroCargo = utf8_encode($row[7]);
    $MuestroEstablecimiento = $row[8];
    $MuestroDependencia = utf8_encode($row[9]);
    $MuestroEstado = $row[10]; 
    $GuardoClave = $row[11]; 
    $MuestroCategoria = $row[12];
    $MuestroNivel = $row[13];
    $MuestroJefatura = $row[14];
    $MuestroFechaIngreso = $row[15];
    $MuestroFechaInicio = $row[16];
  }else{
    $rut1="";
  } 
  $buscar_eyd = "SELECT EST_ID,USU_DEP FROM USUARIO WHERE USU_RUT = '$usu_rut_edit'";
  $rs_buscar_eyd = mysqli_query($cnn, $buscar_eyd);
  if($row_eyd = mysqli_fetch_array($rs_buscar_eyd)){
    $GuardoEstablecimiento=$row_eyd[0];
    $GuardoDependencia=$row_eyd[1];
  }
  $id_formulario = 25;
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
        header("location: ../../error.php");
      }
    }else{
      //si formulario no activo
      $accion = utf8_decode("ACCESO A PAGINA DESABILITADA");
      $insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC, FOR_ID, USU_RUT, LA_IP_USU, LA_FEC, LA_HORA) VALUES ('$accion', '$id_formulario', '$Srut', '$ipcliente', '$fecha', '$hora')";
      mysqli_query($cnn, $insertAcceso);
      header("location: ../../desactivada.php");
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
    <link type="text/css" rel="stylesheet" href="../../../include/css/icon.css" />
    <link type="text/css" rel="stylesheet" href="../../../include/css/materialize.css" media="screen,projection" />
    <link type="text/css" rel="stylesheet" href="../../../include/css/custom.css" />
    <link href="../../../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <style type="text/css">
      body{
        background-image: url("../../../include/img/fondopersonal.jpg");
        background-size: cover;
        background-repeat: no-repeat;
      }
    </style>
    <script type="text/javascript" src="../../../include/js/jquery.js"></script>
    <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
    <script type="text/javascript" src="../../../include/js/materialize.js"></script>
    <script>
      $(document).ready(function () {
        //Animaciones            
        $('select').formSelect();
        $('.sidenav').sidenav();
        $(".dropdown-trigger").dropdown();
        $('.timepicker').timepicker({ twelveHour: false, autoClose: false, defaultTime: 'now'});
        $('.datepicker').datepicker({firstDay: 1,autoClose: true, format: 'yyyy/mm/dd'});
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
      function carga(){
        $("#bienio").attr("disabled","disabled");
        $("#agregar").attr("disabled","disabled");
        $("#eliminar").attr("disabled","disabled");
      }
      function cargarusu(){
        var rut1 = $("#rut_usuario").val(); 
        //console.log(r);
        window.location = "carrera_funcionario.php?rut="+rut1;
      }
      function carga_bienio(){
        var rut2 = "<?php echo $rut1; ?>";
        if (rut2 != ""){
          window.location ="bienio_carrera.php?rut="+rut2;
        }
      } 
      function agregar(){
        var imput_total = $("#total_act").val();
        $("id_act").removeAttr("disabled");
        var idact = $("#id_act").val();
        $("id_act").attr("disabled","disabled");
        var usurut = "<?php echo $rut1; ?>";

        if (imput_total != "" && usurut !=""){
          //var usurut = "<?php echo $rut1; ?>";
          var estadoact = $("#estado_act").val();
          if(estadoact =="1"){
            estadoact="Activo";
          }else if(estadoact =="2"){
            estadoact="Inactivo";
          }
          var nombreact1 = $("#nombre_act").val();
          var fechaact = $("#fecha_act").val();
          var fechaing =$("#fecha_ing").val();
          var horaact = $("#hora_act").val();
          $("#hora_pun").removeAttr("disabled");
          var horapun = $("#hora_pun").val();
          $("#hora_pun").attr("disabled","disabled");
          var nivelact = $("#nivel_act").val();
          if(nivelact=="1"){
              nivelact="Bajo";
          }else if(nivelact=="2"){
              nivelact="Medio";
          }else if(nivelact=="3"){
              nivelact="Alto";
          }                
          $("nivel_pun").removeAttr("disabled");
          var nivelpun = $("#nivel_pun").val();
          $("nivel_pun").attr("disabled","disabled");
          var nota = $("#nota_act").val();
          $("nota_pun1").removeAttr("disabled");
          var notapun = $("#nota_pun1").val();
          $("nota_pun1").attr("disabled","disabled");
          $("total_act").removeAttr("disabled");
          var totalact = $("#total_act").val();
          $("total_act").attr("disabled","disabled");
          $("#fecha_acum1").removeAttr("disabled");
          var fechaacum = $("#fecha_acum1").val();
          $("#fecha_acum1").attr("disabled","disabled");
          //var fechaacum1 = "<?php echo $adescu;?>"; 
          if(idact != ""){
            var estado = 1;
            $.post( "../../php/carrera/actividades_capacitacion2.php", { "estadoact2": estadoact ,"rut_usu" : usurut, "nombreact2" : nombreact1, "fechaact2" : fechaact, "horaact2" : horaact, "horapun2" : horapun, "nivelact2" : nivelact, "nivelpun2" : nivelpun, "nota2": nota, "notapun2" : notapun, "totalact2" : totalact, "fechaing2" : fechaing, "estado2" : estado, "idact2" : idact, "fechaacum2" : fechaacum}, null, "json" )    .done(function( data, textStatus, jqXHR ) {
              if ( console && console.log ) {
                console.log( "La solicitud se ha completado correctamente." );
                window.location = "carrera_funcionario.php?rut="+usurut;
              }
            })
              .fail(function( jqXHR, textStatus, errorThrown ) {
              if ( console && console.log ) {
                console.log( "La solicitud a fallado: " +  textStatus);
                //window.location="mant_capacitacion.php";
              }
            });
          }else{
            var estado = 2;
            $.post( "../../php/carrera/actividades_capacitacion2.php", { "estadoact2": estadoact ,"rut_usu" : usurut, "nombreact2" : nombreact1, "fechaact2" : fechaact, "horaact2" : horaact, "horapun2" : horapun, "nivelact2" : nivelact, "nivelpun2" : nivelpun, "nota2": nota, "notapun2" : notapun, "totalact2" : totalact, "fechaing2" : fechaing, "estado2" : estado, "idact2" : idact,"fecha_acum2" : fechaacum}, null, "json" )          .done(function( data, textStatus, jqXHR ) {
              if ( console && console.log ) {
                console.log( "La solicitud se ha completado correctamente." );
                window.location = "carrera_funcionario.php?rut="+usurut;
              }
            })
              .fail(function( jqXHR, textStatus, errorThrown ) {
              if ( console && console.log ) {
                console.log( "La solicitud a fallado: " +  textStatus);
                //window.location="mant_capacitacion.php";
              }
            });
          }
        }else{
          M.toast({html: 'Datos no válidos'});
        }
      }   
      function Vuelta(v){
        console.log( v.estado); 
      }
      function eliminar(){
        var usurut = "<?php echo $rut1; ?>"; 
        $("id_act").removeAttr("disabled");
        var idact = $("#id_act").val();
        $("id_act").attr("disabled","disabled");
        if(idact != ""){        
          var estado = 3;
          $.post( "../../php/carrera/actividades_capacitacion2.php", { "estado2" : estado, "idact2" : idact }, null, "json" ) 
            .done(function( data, textStatus, jqXHR ) {
            if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
              window.location = "carrera_funcionario.php?rut="+usurut;
            }
          })
            .fail(function( jqXHR, textStatus, errorThrown ) {
            if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
              window.location = "carrera_funcionario.php?rut="+usurut;
            }
          });
        }else{
          M.toast({html:'Registro no eliminado'});
        } 
      }
      function activar_act(idact1){
        var usurut = "<?php echo $rut1; ?>";
        var estado = 4;
        var idact= idact1;
        $.post( "../../php/carrera/actividades_capacitacion2.php", { "estado2" : estado, "idact2" : idact }, null, "json" )  
          .done(function( data, textStatus, jqXHR ) {
          if ( console && console.log ) {
            console.log( "La solicitud se ha completado correctamente." );
            window.location = "carrera_funcionario.php?rut="+usurut;
          }
        })
          .fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
            console.log( "La solicitud a fallado: " +  textStatus);
            window.location = "carrera_funcionario.php?rut="+usurut;
          }       
        });
      }
      function abrir_docu(id){
        var iddocu = id;
        iddocu = iddocu + ".pdf";
        window.open('../../../include/certificado_capacitacion/'+iddocu);
      }
      function modificar(act,fact,fiact,hact,hpun,nact,pnact,noact,nopunact,toact,idact,facum){ 
        $("#nombre_act").val(act);
        $("#fecha_act").val(fact);
        $("#fecha_ing").val(fiact);
        $("#id_act").val(idact);
        $("#id_act1").val(idact);
        $("#fecha_acum1").val(facum);
      }
      function cambiafecha(){
        var f = $("#fecha_acum").val();
        $("#fecha_acum1").val(f);
      } 
      // ingreso de actividades
      function puntajehora(h){
        var pf= $("#hora_act").val();
        if(pf>=1 && pf<=16){
          var pj=25;
          $("#hora_pun").val(pj);
        }else if(pf>=17 && pf<=24){
          var pj=45;
          $("#hora_pun").val(pj);
        }else if(pf>=25 && pf<=32){
          var pj=65;
          $("#hora_pun").val(pj);
        }else if(pf>=33 && pf<=40){
          var pj=80;
          $("#hora_pun").val(pj);
        }else if(pf>=41 && pf<=79){
          var pj=90;
          $("#hora_pun").val(pj);
        }else if(pf>=80){
          var pj=100;
          $("#hora_pun").val(pj);
        }else{
          M.toast({html:'Hora no válida'});
          $("#hora_act").val("");
          $("#hora_pun").val("");
        }
        $("#nota_act").val("");
        $("#nota_pun1").val("");
        $("#total_act").val("");
      }  
      function nota(){
        var imput_nota = "#nota_act";
        var imput_total = "#total_act";
        var imput_not_pun = "#nota_pun1";
        var pn= $(imput_nota).val();
        if(pn>=1 && pn<=4){
            var pjn=0.4;
            $(imput_not_pun).val(pjn);
        }else if(pn>=4.1 && pn<=5.5){
            var pjn=0.7;
            $(imput_not_pun).val(pjn);
        }else if(pn>=5.6 && pn<=7){
            var pjn=1;
            $(imput_not_pun).val(pjn);
        }else{
            M.toast({html: 'Nota no válida'});
            $(imput_nota).val("");
            $(imput_not_pun).val("");
            $("#nota_act").val("");
            $("#nota_pun1").val("");
            $("#total_act").val("");
        }
        var pjh1 = $("#hora_pun").val();
        var pjn1 = $(imput_not_pun).val();
        var pji1 = $("#nivel_pun").val();
        var valn = $(imput_not_pun).val();
        if(pjh1 != 0 && pjn1 != 0 && pji1 != 0){
          var TotalAct =0;
          TotalAct = (pjh1 * valn * pji1);
          n1 = TotalAct.toFixed(2);
          $(imput_total).val(n1);
        }
      }
      function nivel(){
        var pni = $("#nivel_act").val();
        if(pni== "1"){
            var pjni=1;
            $("#nivel_pun").val(pjni);
        }else if(pni== "2"){
            var pjni=1.1;
            $("#nivel_pun").val(pjni);
        }else if(pni== "3"){
            var pjni=1.2;
            $("#nivel_pun").val(pjni);
        }else{
            M.toast({html: 'Nivel no válido'});
            $("#nivel_act").val("");
            $("#nivel_pun").val("");
        }
        $("#nota_act").val("");
        $("#nota_pun1").val("");
        $("#total_act").val("");
      }
      function ImprimirCC(){
        var idcc = "<?php echo $rut1;?>";
        window.open('http://200.68.34.158/personal/pdf/certificado_capa.php?id='+idcc,'_blank');
      }
    </script>
  </head>
  <body onload="cargar();">
    <!-- llamo el nav que tengo almacenado en un archivo -->
    <?php require_once('../../estructura/nav_personal.php');?>
    <!-- inicio contenido pagina -->
    </br>
  </br>
</br>
<div class="container">
  <div class="section">
    <div class="row">
      <div class="col s12 center block" style="background-color: #ffffff">
        <h4 class="light">Estado Carrera Funcionaria</h4>
        <div class="row">
          <form class="col s12" method="post" action="" enctype="multipart/form-data">
            <div class="input-field col s6">
              <i class="mdi-action-account-circle prefix"></i>
              <input id="rut_usuario" type="text" class="validate" name="rut_usuario" style="text-transform: uppercase" placeholder="" value="<?php echo $rut1;?>" required>
              <label for="icon_prefix">RUT</label>
            </div>
            <div class="input-field col s6">
              <button class="btn trigger" type="button" name="buscar" id="buscar" value="buscar"  onclick = "cargarusu();">Buscar</button>
            </div>
            <div class="input-field col s12">
              <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
            </div>
            <div class="input-field col s4">
              <input type="text" name="nombre_usuario" id="nombre_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroNombre;?>" onkeypress="return soloLetras(event)">
              <label for="icon_prefix">Nombres</label>
            </div>
            <div class="input-field col s4">
              <input type="text" name="apellidoP_usuario" id="apellidoP_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroApellidoP;?>" onkeypress="return soloLetras(event)">
              <label for="icon_prefix">Apellido Paterno</label>
            </div>
            <div class="input-field col s4">
              <input type="text" name="apellidoM_usuario" id="apellidoM_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroApellidoM;?>" onkeypress="return soloLetras(event)">
              <label for="icon_prefix">Apellido Materno</label>
            </div>
            <div class="input-field col s4">
              <input type="text" name="email_usuario" id="email_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroEmail;?>" onblur="ValidoEmail()";>
              <label for="icon_prefix">Correo</label>
            </div>
            <div class="input-field col s4">
              <input type="text" name="direccion_usuario" id="direccion_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroDireccion;?>" onblur="ValidoEmail()";>
              <label for="icon_prefix">Direccion</label>
            </div>
            <div class="input-field col s4">
              <input type="text" name="fono_usuario" id="fono_usuario" class="validate" placeholder="" disabled value="<?php echo $MuestroFono;?>" onblur="ValidoEmail()";>
              <label for="icon_prefix">Telefono</label>
            </div>         
            <div class="input-field col s4">
              <input type="text" name="categoria_usuario" id="categoria_usuario" value="<?php echo $MuestroCategoria;?>" disabled>
              <label for="categoria_usuario">Categoria</label>
            </div>
            <div class="input-field col s4">
              <input type="text" name="nivel_usuario" id="nivel_usuario" class="validate" placeholder="" value="<?php echo $MuestroNivel;?>" disabled>
              <label for="nivel_usuario">Nivel</label>
            </div>
            <div class="input-field col s4">
              <input type="text" name="jefatura_usuario" id="jefatura_usuario" class="validate" placeholder="" value="<?php echo $MuestroJefatura;?>" disabled>
              <label for="jefatura_usuario">Jefatura</label>
            </div>                                
            <div class="input-field col s4">
              <input type="text" name="establecimiento_usuario" id="establecimiento_usuario" class="validate" placeholder="" value="<?php echo $MuestroEstablecimiento;?>" disabled>
              <label for="icon_prefix">Lugar de Trabajo</label>
            </div>
            <div class="input-field col s4">
              <input type="text" name="dependencia_usuario" id="dependencia_usuario" class="validate" placeholder="" value="<?php echo $MuestroDependencia;?>" disabled>
              <label for="icon_prefix">De quien depende</label>
            </div>                                
            <div class="input-field col s2">
              <input type="text" class="datepicker" name="fechaIngreso" id="fechaIngreso" value="<?php echo $MuestroFechaIngreso;?>"placeholder="Fecha Ingreso Dpto. Salud de Rengo" disabled> 
              <label for="icon_prefix" id="fechaIngreso">Fecha Ingreso</label>
            </div>
            <div class="input-field col s2">
              <input type="text" class="datepicker" name="fechaInicio" id="fechaInicioo" value="<?php echo $MuestroFechaInicio;?>"placeholder="Fecha Ingreso Salud Pública" disabled> 
              <label for="icon_prefix" id="fechaIngreso">Fecha Inicio Carrera F.</label>
            </div>
            <br>
            <br>
            <br>
            <div class="input-field col s2">                                    
            <select name="estado_act" id="estado_act" >
              <!--<option value="" disabled selected></option>-->
              <option value="1">Activo</option>
              <option value="2">Inactivo</option>           
            </select>
              <label>Estado Actividad</label>
            </div>
            <div class="input-field col s4">
              <input type="text" name="nombre_act" id="nombre_act" class="validate" maxlength="250" placeholder="" required>
              <label for="autocomplete-input">Actividad de Capacitación</label>
            </div>
            <div class="input-field col s2">
              <input type="text" class="datepicker" name="fecha_act" id="fecha_act" placeholder="" required>
              <label for="icon_prefix">Fecha Actividad</label>
            </div>
            <div class="input-field col s2">
              <input type="text" class="datepicker" name="fecha_ing" id="fecha_ing" placeholder="" required>
              <label for="icon_prefix">Fecha Ingreso</label>
            </div>
            <div class="input-field col s1">
              <input type="text" name="fecha_acum1" id="fecha_acum1" class="validate" placeholder="" value="<?php echo $ano5;?>" required disabled>
              <label for="fecha_acum1">Acumula</label>
            </div>
            <div class="input-field col s1">
              <select name="fecha_acum" id="fecha_acum" type="text" onchange="cambiafecha(event)">
                <?php
                $i = 0;
                $ano6 = $ano6;
                while(30 > $i){
                  $adescu = $ano6 - $i;
                  printf("<option value=\"$adescu\">$adescu</option>");
                  $i= $i+1;
                }
                //echo "<option value='".$adescu."' selected>$ano5</option>";
                //echo $adescu;
                ?> 
              </select>
              <label>Fecha Acumula</label>
            </div>
            <div class="input-field col s12">
              <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
            </div>
            <div class="input-field col s1">
              <input type="text" name="hora_act" id="hora_act" class="validate" placeholder="" onchange="puntajehora(event)">
              <label for="icon_prefix">Horas</label>
            </div>
            <div class="input-field col s1">
              <input type="text" name="hora_pun" id="hora_pun" class="validate" placeholder="" disabled>
              <label for="hora_pun">Puntaje Horas</label>
            </div>       
            <div class="input-field col s2"> 
              <select name="nivel_act" id="nivel_act" onchange="nivel()">
              <option value="" disabled selected></option>                                       
              <option value="1">Bajo</option>
              <option value="2">Medio</option>
              <option value="3">Alto</option>
            </select>
              <label class="active">Nivel Técnico</label>
            </div>
            <div class="input-field col s1">
              <input type="text" name="nivel_pun" id="nivel_pun" class="validate" disabled placeholder="">
              <label for="icon_prefix">Puntaje Nivel</label>
            </div>
            <div class="input-field col s1">
              <input type="text" name="nota_act" id="nota_act" class="validate" placeholder=""  onblur="nota()">
              <label for="icon_prefix">Nota</label>
            </div>
            <div class="input-field col s2">
              <input type="text" name="nota_pun1" id="nota_pun1" class="validate" placeholder="" disabled>
              <label for="hora_pun">Puntaje Nota</label>
            </div>  
            <div class="input-field col s2">
                <input type="text" name="total_act" id="total_act" class="validate" placeholder="" disabled>
                <label for="hora_pun">Puntaje Total</label>
            </div>
            <div class="input-field col s2">
              <input type="text" name="id_act" id="id_act" class="validate" placeholder="" required disabled>
              <label for="id_act">ID</label>
            </div>
            <div class="input-field col s12">
              <input style="display:none" id="id_act1" type="text" class="validate" name="id_act1" required >
            </div>
            <div class="col s6">
              <div name ="agregar" id="agregar" onclick="agregar()" class="btn trigger">Agre/Modi</a></h6></div>
        </div> 
        <div class="col s6">
          <div name ="eliminar" id="eliminar" onclick="eliminar()" class="btn trigger">Eliminar</a></h6></div>
    </div>
    <div class="input-field col s12">
      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
    </div>
    <div class="input-field col s12">
      <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
    </div>
    <!-- subir archivos al servidor -->
    <?php
    $nombreArchivoId = utf8_decode($_POST['id_act1']);
    if($nombreArchivoId != ""){  
      $formatos   = array('.pdf');
      $directorio = '../../../include/certificado_capacitacion'; 
      if (isset($_POST['boton'])){
        $nombreArchivo    = $_FILES['archivo']['name'];
        $nombreArchivo1    = $_FILES['archivo']['name'];
        $nombreTmpArchivo = $_FILES['archivo']['tmp_name'];
        $ext         = substr($nombreArchivo, strrpos($nombreArchivo, '.'));
        $nombreArchivoId = "$nombreArchivoId"."$ext";
        if (in_array($ext, $formatos)){
          if (move_uploaded_file($nombreTmpArchivo, "$directorio/$nombreArchivoId")){
            //echo "Felicitaciones, archivo $nombreArchivo subido exitosamente";
    ?><script> M.toast({html: 'Archivo Cargado'});</script><?php
          }else{
            //echo 'Ocurrió un error subiendo el archivo, valida los permisos de la carpeta "archivos"';
    ?><script> M.toast({html:'Archivo No Cargado, error de permisos'});</script><?php
          }
        }else{
          //echo 'Aquí va el mensaje que quieres mostrar cuando un usuario suba un archivo con una extensión diferente';
    ?><script> M.toast({html:'Formato no aceptado'});</script><?php
        }
      }
    }
    ?>
    <!-- <div class="form-group">
<label for="archvio">Archivo</label>
<input type="file" class="form-control-file" id="archvio" aria-describedby="fileHelp" name="archivo">
<small id="fileHelp" class="form-text text-muted">Archivos permitidos (.jpg .png .gif)</small>
</div>
<button type="submit" class="btn btn-primary" name="boton">Subir archivo</button>-->
    <div class="file-field input-field col s10">
      <div class="btn">
        <span>Archivo</span>
        <input type="file" name="archivo">
      </div>
      <div class="file-path-wrapper">
        <input class="file-path validate" type="text" name="archivo" id="archivo" /> 
      </div>        
    </div>
    <button class="btn waves-effect waves-light col s2" type="submit" name="boton">Cargar</button>  
    <br>
    <br />
    <br>
    <br>
    <table id="tab_carrera" class="responsive-table bordered striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre Actividad</th>
          <th width="100">Fecha</th>
          <th>Horas</th>
          <th>Nota</th>
          <th>Nivel</th>
          <th>Puntaje</th>
          <th width="100">Fecha Ingreso</th>
          <th>Año Acumula</th>
          <!--<th>Estado</th>-->
        </tr>
      <tbody>
        <!-- cargar la base de datos con php --> 
        <?php        
        $query = "SELECT CA_ID, CA_DES, DATE_FORMAT(CA_FEC,'%Y-%m-%d'), CA_HORA, CA_NOTA, CA_NIVEL, CA_TOTAL, DATE_FORMAT(CA_FEC_ING, '%Y-%m-%d'),CA_ESTADO,                                  CA_HORA_PUN,CA_NIVEL_PUN,CA_NOTA_PUN,CA_TOTAL,if(CA_FEC_ACU='0',YEAR(CA_FEC_ING),CA_FEC_ACU) AS ACU FROM CARRERA_ACT WHERE (USU_RUT = '".$rut1."')  ORDER BY CA_FEC_ING";                                                      $respuesta = mysqli_query($cnn, $query);   
        //recorrer los registros
        $cont = 0;               
        while ($row_rs = mysqli_fetch_array($respuesta)){
          $actividad = str_replace('"',' ',$row_rs[1]);
          echo "<tr>";
          echo "<td><id='in".$cont."'>".$row_rs[0]."</td>";
          echo "<td><class='col s6'>".utf8_encode($actividad)."</td>";
          echo "<td>".$row_rs[2]."</td>";
          echo "<td>".$row_rs[3]."</td>";
          echo "<td>".$row_rs[4]."</td>";
          echo "<td>".$row_rs[5]."</td>";
          echo "<td>".$row_rs[6]."</td>";                                                        
          echo "<td>".$row_rs[7]."</td>";
          echo "<td>".$row_rs[13]."<td>";
         
            if ($row_rs[8] == "Activo" || $row_rs[8] == "Decretado"){
                    echo '<td><button class="btn trigger" name="modifica" onclick="modificar('; echo "'".utf8_encode($actividad)."','".$row_rs[2]."','".$row_rs[7]."','".$row_rs[3]."','".$row_rs[9]."','".$row_rs[5]."','".$row_rs[10]."','".$row_rs[4]."','".$row_rs[11]."','".$row_rs[12]."','".$row_rs[0]."','".$row_rs[13]."'"; echo');" id="modifica" type="button">Modificar</button></td>';
                     $conta_pun = $row_rs[6] + $conta_pun;                                                                                                                      
            }elseif ($row_rs[8] == "Inactivo"){
                    echo '<td><button class="btn trigger" name="activar" onclick="activar_act('; echo "'".$row_rs[0]."'"; echo');" id="activar" type="button">ACTIVAR</button></td>';
            }
            
            $docum= '../../../include/certificado_capacitacion/'.$row_rs[0].'.pdf'; //MUESTRA ARCHIVO CUANDO EXISTE
            if (is_readable($docum)) {
                //$pdf->Image($firma,130,279,40,20);
                echo '<td><button class="btn trigger" name="documento" onclick="abrir_docu('; echo "'".$row_rs[0]."'"; echo');" id="documento" type="button">ARCHIVO</button></td>';
            } 
            echo "</tr>";                                                    
                $cont = $cont + 1;
        }
        if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
          if($conta_pun > 4500){
            $conta_pun= 4500;
          }
        }else{
          if($conta_pun > 3500){
            $conta_pun= 3500;
          }
        }
        ?>

        </tbody>
    </thead>                                    
</table>
<br>
<br>
<div class="col s12">
    <div name ="bienio" id="bienio" onclick="carga_bienio()" class="btn trigger">Bienio</a></h6></div>
</div>
<div class="input-field col s12">
    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
</div>
<br>
<br>
<div class="input-field col s4">
    <input type="text" align='right' name="totalact" id="totalact" value="<?php echo $conta_pun;?>"placeholder="Total Puntaje Capacitación"  disabled> 
    <label for="icon_prefix" id="fechaIngreso">Total Puntos Capacitación</label>
</div>
<?php
$acumu_pun = 0;
$acumu_pun1 = 0;
$sal_acu =0;
$iniacu=0;
//$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC <='2021-08-31') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
 $query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
$respuesta2 = mysqli_query($cnn, $query2);
$row = $respuesta2->fetch_array(MYSQLI_NUM);
//printf ("%s (%s)\n", $row[0], $row[1]);
//$i= 0;
//$valano = $row1[1];
for ($i = $row[1]; $i <= $ano5; $i++) {  
  //echo $i."   antes";
    //print "<p>$i</p>\n"; //solo para ver lista de años
  if($iniacu==0){
    $valano = $row[1];
    $iniacu=1;
  }
  //echo $i. " acá el i";
  if(isset($i)==''){
    $i=0;
  }  
   $query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (YEAR(CA_FEC)=$i) AND (CA_FEC <='2021-08-31') AND (CA_FEC_ING <='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
  $respuesta3 = mysqli_query($cnn, $query2);
  $row1 = $respuesta3->fetch_array(MYSQLI_NUM);
  //printf ("%s (%s)\n", $row1[1], $row1[2]);
  //$valano = $i; 
  $year = $row1[1];
  $puntaje = $row1[2];  
    if($year !='' and $puntaje !=''){
      if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
        //$valano = $row_rs2[1];
         $acumu_pun1 = $row1[2] + $sal_acu;
         if($acumu_pun1 < 151){   
          $acumu_pun = ($acumu_pun + $acumu_pun1);
          $sal_acu=0;
        }else{
          $acumu_pun = $acumu_pun + 150;
          $sal_acu = ($acumu_pun1 - 150);
        }       
      }else{  
         $acumu_pun1 = $row1[2] + $sal_acu;
        if($acumu_pun1 < 118){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;      
        }else{
          $acumu_pun = $acumu_pun + 117;
          $sal_acu = ($acumu_pun1 - 117);
        } 
      } 
      //printf ("%s (%s)\n", $i,$acumu_pun);
    }else{
      $puntaje =0;   
      if($MuestroCategoria == "A" || $MuestroCategoria == "B"){      
        $acumu_pun1 = $puntaje + $sal_acu;
        if($acumu_pun1 < 151){   
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;
        }else{
          $acumu_pun = $acumu_pun + 150;
          $sal_acu = ($acumu_pun1 - 150);
        } 
      }else{
        $acumu_pun1 = $puntaje + $sal_acu;
        if($acumu_pun1 < 118){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;      
        }else{
          $acumu_pun = $acumu_pun + 117;
          $sal_acu = ($acumu_pun1 - 117);
        }
      }
       //printf ("%s (%s)\n", $i,$sal_acu);
    }
    
}
$acumu_pun31= $acumu_pun;
//$sal_acu31 = $sal_acu;
/*############ HASTA EL 31 DE AGOSTO DE 2021 ##################*/

/*########### DESDE EL 01 DE SEPTIEMBRE 2021 ##################*/
$acumu_pun = 0;
//$acumu_pun1 = 0;
//$sal_acu =0;
$acumu_pun01=0;
$query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC_ING),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (CA_FEC_ING >='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
$respuesta21 = mysqli_query($cnn, $query2);
$row21 = $respuesta21->fetch_array(MYSQLI_NUM);
//printf ("%s (%s)\n", $row21[0], $row21[1]);
//$i= $row21[1];

if($row21 != ''){
  for ($i1 = $row21[1]; $i1 <= $ano5; $i1++) {
    //echo $i1. "   Después";
    //print "<p>$i1</p>\n"; //solo para ver lista de años
    $query2="SELECT USU_RUT,if(CA_FEC_ACU='0',YEAR(CA_FEC_ING),CA_FEC_ACU) AS ACU, SUM(CA_TOTAL) AS SUMA,  YEAR(CA_FEC) AS ANO  FROM CARRERA_ACT WHERE (USU_RUT='".$rut1."') AND (CA_ESTADO <> 'Inactivo') AND (YEAR(CA_FEC_ING)=$i1) AND (CA_FEC_ING >='2021-08-31') GROUP BY ACU ORDER BY ACU ASC";
    $respuesta31 = mysqli_query($cnn, $query2);
    $row211 = $respuesta31->fetch_array(MYSQLI_NUM);
    //printf ("%s (%s)\n", $row211[1], $row211[2]);
    //$valano = $i1;
    $year = $row211[1];
    $puntaje = $row211[2];
    if($year>2022 && $iniacu=0){    
    if($year !='' and $puntaje !=''){
      if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
        //$valano = $row_rs2[1];
        $acumu_pun1 = $row211[2] + $sal_acu;
        if($acumu_pun1 < 151){   
          $acumu_pun = ($acumu_pun + $acumu_pun1);
          $sal_acu=0;
        }else{
          $acumu_pun = $acumu_pun + 150;
          $sal_acu = ($acumu_pun1 - 150);
        }       
      }else{  
        $acumu_pun1 = $row211[2] + $sal_acu;
        if($acumu_pun1 < 118){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;   
        }else{
          $acumu_pun = $acumu_pun + 117;
          $sal_acu = ($acumu_pun1 - 117);
        } 
      } 
      //printf ("%s (%s)\n", $i,$acumu_pun);
    }else{
      $puntaje =0;
      if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
        $acumu_pun1 = $puntaje + $sal_acu;
        if($acumu_pun1 < 151){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;
        }else{
          $acumu_pun = $acumu_pun + 150;
          $sal_acu = ($acumu_pun1 - 150);
        } 
      }else{
        $acumu_pun1 = $puntaje + $sal_acu;
        if($acumu_pun1 < 118){  
          $acumu_pun = $acumu_pun + $acumu_pun1;
          $sal_acu=0;      
        }else{
          $acumu_pun = $acumu_pun + 117;
          $sal_acu = ($acumu_pun1 - 117);
        }
      }
      //printf ("%s (%s)\n", $i,$sal_acu);
    }
  }
  }  
  
}else{
  $acumu_pun=0;
}
$acumu_pun01 = $acumu_pun;
//$sal_acu01 = $sal_acu;

/*########## FIN DESDE EL 01 DE SEPTIEMBRE 2021 ####################*/
$acumu_pun=0;
//$sal_acu=0;
/*#### INICIO SUMA DE PUNTAJES POR CAPACITACIÓN CON AMBOS CÁLCULOS #####*/
$acumu_pun = $acumu_pun31 + $acumu_pun01;
//$sal_acu = $sal_acu01+$sal_acu31;
//$acumu_pun = $acumu_pun - $sal_acu;
//echo $sal_acu;
/*#### FIN SUMA DE PUNTAJES POR CAPACITACIÓN CON AMBOS CÁLCULOS #####*/ 

                                        /*while ($row_rs2 = mysqli_fetch_array($respuesta2)){                                            
                                         $valano = $row_rs2[1];
                                          if($valini==0){
                                            echo $valini= $valano;
                                          }
                                            if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
                                                //$valano = $row_rs2[1];
                                                $acumu_pun1 = $row_rs2[2] + $sal_acu;
                                                if($acumu_pun1 < 151){                                            
                                                    $acumu_pun = $acumu_pun + $acumu_pun1;
                                                    $sal_acu=0;
                                                }else{
                                                    $acumu_pun = $acumu_pun + 150;
                                                    $sal_acu = ($acumu_pun1 - 150);
                                                }                                                
                                            }else{                                                
                                                $acumu_pun1 = $row_rs2[2] + $sal_acu;
                                                if($acumu_pun1 < 118){                                            
                                                    $acumu_pun = $acumu_pun + $acumu_pun1;
                                                    $sal_acu=0;                                                    
                                                }else{
                                                    $acumu_pun = $acumu_pun + 117;
                                                    $sal_acu = ($acumu_pun1 - 117);
                                                }                                                
                                            }                                                                                     
                                        } */                                     
                                      // echo $conta_pun ."   ". $acumu_pun;
                                       $saldo = number_format($conta_pun - $acumu_pun,2,'.', '');
                                      //$valano = $valano + 1;
                                       

                                       //echo $valano. "valid". "  ". $ano5."Año5";
                                       /*while($valano <= $ano5){
                                        $valano = $valano + 1;
                                        if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
                                          if($saldo >= 150){
                                            $acumu_pun= $acumu_pun +150;
                                            $saldo = $saldo -150;
                                          }else{
                                            $acumu_pun = $acumu_pun + $saldo;
                                            $saldo=0;
                                          }
                                        }else{
                                          if($saldo >= 117){
                                            $acumu_pun= $acumu_pun + 117;
                                            $saldo = $saldo -117;
                                          }else{
                                            $acumu_pun = $acumu_pun + $saldo;
                                            $saldo=0;
                                          }
                                        }
                                      }
 
                                        if($saldo<0){
                                          $saldo=0;
                                        }
                                        if($rut1 ='11.277.235-9'){
                                          //echo $saldo. "  Saldo";
                                          //echo $acumu_pun."  Acumulado";
                                        }
                                    if($MuestroCategoria == "A" || $MuestroCategoria == "B"){                          
                                      if($acumu_pun > 4500){
                                        $acumu_pun= 4500;
                                      }
                                    }else{
                                      if($acumu_pun > 3500){
                                        $acumu_pun= 3500;
                                      }
                                    }*/
                                    if($acumu_pun==0){
                                      $acumu_pun=$conta_pun;
                                      $saldo=0;
                                    }
                                ?>

                                <div class="input-field col s4">
                                    <input type="text" align='right' name="validosact" id="validosact" value="<?php echo $acumu_pun;?>"placeholder="Total Puntaje Capacitación"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Puntos Válidos Año en Curso</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" align='right' name="saldoact" id="saldosact" value="<?php echo $saldo;?>"placeholder="Saldo Puntaje Capacitación"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Saldo</label>
                                </div>
                                <div class="input-field col s12">
                                    <input style="display:none" id="rut_oculto" type="text" class="validate" name="rut_oculto" >
                                </div>
                                <?php
                                 $consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$rut1."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
                                 $resputbie = mysqli_query($cnn, $consultabie);
                                 if($row = mysqli_fetch_array($resputbie)){
                                    $inicial = $row[0];                                    
                                      
                                 }                     

                                  if($MuestroFechaInicio != $inicial){
                                                             
                                    ?> <script type="text/javascript"> M.toast({html: 'Diferencia en las fechas, favor revisar el ingreso a la Salud Pública'});</script> <?php
                                    break 1;
                                  }             
                                    $consultabie = "SELECT CB_FEC_INI,CB_FEC_FIN,CB_INDEFI FROM CARRERA_BIENIO WHERE (USU_RUT='".$rut1."') AND (CB_ESTADO = '1') ORDER BY CB_FEC_INI";
                                    $resputbie = mysqli_query($cnn, $consultabie);
                                    while ($row_rs3 = mysqli_fetch_array($resputbie)){                          
                                       
                                        if($row_rs3[2] == 1){
                                            if($row_rs3[0] >= $final2){
                                                                                                                
                                                    $date1=date_create($final2);
                                                    $date2=date_create($row_rs3[0]);
                                                    $diff=date_diff($date1,$date2);
                                                    $diasno = $diasno + $diff->format('%R%a');
                                                    if($diasno <= 1){
                                                        $diasno = 0;
                                                    }

                                                   $final2= $row_rs3[1];
                                                if($final2 <= $row_rs3[1]){
                                                    $final2= $row_rs3[1];                                                        
                                                }
                                            }else{
                                                if($final2 <= $row_rs3[1]){
                                                    $final2 = $row_rs3[1];

                                                }
                                            }
                                          
                                            $date1=date_create($row_rs3[0]);
                                            $date2=date_create($fecha);
                                            $diff=date_diff($date1,$date2);
                                            $cuentabienios = $cuentabienios + $diff->format('%Y'); //$diff->format('%R%a');
                                            $final2 = $fecha;
                                            break 1;
                                        }else{
                                            
                                            if($row_rs3[0] == $inicial){
                                                $inicial2 = $row_rs3[0];
                                                $final2 = $row_rs3[1];
                                                //$date1=date_create($inicial2);
                                                //$date2=date_create($final2);
                                                //$diff=date_diff($date1,$date2);
                                                
                                            }else{
                                                if($row_rs3[0] >= $final2){
                                                                                                                
                                                        $date1=date_create($final2);
                                                        $date2=date_create($row_rs3[0]);
                                                        $diff=date_diff($date1,$date2);
                                                        $diff2=$diff->format('%R%a');
                                                        
                                                        if($diff2 ==1){
                                                          $diff2=0;                                 
                                                        }else{
                                                          $diff2=$diff2-1;
                                                        }
                                                        $diasno = $diasno + $diff2;
                                                        if($diasno <= 1){
                                                            $diasno = 0;
                                                        }                             
                                                    
                                                        
                                                        $final2= $row_rs3[1];
                                                    if($final2 <= $row_rs3[1]){
                                                        $final2= $row_rs3[1];                                                        
                                                    }
                                                }else{
                                                    if($final2 <= $row_rs3[1]){
                                                        $final2 = $row_rs3[1];
                                                                                          
                                                    }
                                                }                                                
                                                                                            
                                            }                                        
                                            
                                        }
                                        
                             
                                    }
                                    if($final2 > $fecha){
                                        $final2 = $fecha;
                                    }
                                              
                                    if($diasno > 0){
                                        $nuevainicial = date_create($MuestroFechaInicio);
                                        date_add($nuevainicial, date_interval_create_from_date_string("$diasno days"));
                                        date_format($nuevainicial, 'Y-m-d');
                                        $nuevainicial2 = date_format($nuevainicial, 'Y-m-d');
                                        $date2=date_create($final2);
                                        $interval=date_diff($nuevainicial,$date2);
                                        $cuentabienios = $interval->format('%Y');                                        
                                    }else{                                        
                                         $date1=date_create($MuestroFechaInicio);
                                         $date2=date_create($final2);                                        
                                         $interval=date_diff($date1,$date2);                                         
                                         $cuentabienios =  $interval->format('%Y');                                                                           
                                    } 
                                    
                                    if($nuevainicial2==""){
                                      $nuevainicial2 = $MuestroFechaInicio;
                                    }
                  
                                    while ($nuevainicial2 <= $fecha){
                                        $nuevainicial3 = date_create($nuevainicial2);
                                        date_add($nuevainicial3, date_interval_create_from_date_string('2 years'));
                                        date_format($nuevainicial3, 'Y-m-d');
                                        $nuevainicial2 =  date_format($nuevainicial3, 'Y-m-d');
                                    }                                   
                                      
                                    if($cuentabienios%2==0){ // se multiplica o restar para dejar como valor entro para la búsqueda en la Tabla CARRERA_BIENIO_PTOS
                                        $valido_bie = $cuentabienios * 1;
                                    }else{
                                        $valido_bie= $cuentabienios - 1;
                                    }
                                    //Cuando la división da uno, deja en años como válido para carrera 10-01-2022spo
                                    if($cuentabienios==1){
                                       $cuentabienios=2;
                                       $valido_bie=2;
                                    }
                                    $buscar_bie = "SELECT CBP_PTOS FROM CARRERA_BIENIO_PTOS WHERE CBP_ANOS = '$valido_bie'";
                                    $rs_buscar_bie = mysqli_query($cnn, $buscar_bie);
                                    if($row_bie = mysqli_fetch_array($rs_buscar_bie)){
                                        $bienios_ptos=$row_bie[0];                         
                                    }
                                    if($valido_bie>= 30){
                                      $bienios_ptos= 8000;
                                    }
                                    $valido_bie2= $valido_bie /2;
                                    $total_puntaje = $bienios_ptos + $acumu_pun;
                                    round($total_puntaje, 2);
                                    
                                    if($MuestroCategoria == "A" || $MuestroCategoria == "B"){
                                        $buscar_criti ="SELECT CPC_AB_INI,CPC_AB_FIN,CPC_NIVEL FROM CARRERA_PTOS_CRITI";
                                        $resputcriti = mysqli_query($cnn, $buscar_criti);
                                        while ($row_rs4 = mysqli_fetch_array($resputcriti)){
                                        //$total_puntaje = 4167.75;
                                            if($row_rs4[0] <= $total_puntaje){
                                              $nivel_actual = $row_rs4[2];                                              
                                                if($total_puntaje <= $row_rs4[1]){ 
                                                    $nivel_actual = $row_rs4[2];                                                  
                                                    break 1;
                                                }
                                              
                                            }                                
                                        }
                                    
                                    }else{
                                        $buscar_criti ="SELECT CPC_CF_INI,CPC_CF_FIN,CPC_NIVEL FROM CARRERA_PTOS_CRITI";
                                        $resputcriti = mysqli_query($cnn, $buscar_criti);
                                        while ($row_rs4 = mysqli_fetch_array($resputcriti)){
                                        //$total_puntaje = 4167.75;
                                            if($row_rs4[0] <= $total_puntaje){
                                              $nivel_actual = $row_rs4[2];
                                                if($total_puntaje <= $row_rs4[1]){                                                   
                                                    $nivel_actual = $row_rs4[2];
                                                    break 1;
                                                }
                                              
                                            }                                
                                        }
                                    }
                                
                                ?>

                                <div class="input-field col s3">
                                    <input type="text" align='right' name="bienios_cf" id="bienios_cf" value="<?php echo $cuentabienios;?>" placeholder="Años en Salud Publica"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Años Salud Pública</label>
                                </div>

                                <div class="input-field col s3">
                                    <input type="text" align='right' name="validobie" id="validosact" value="<?php echo $valido_bie2;?>" placeholder="Años Válidos Salud Pública"  disabled> 
                                    <label for="icon_prefix" id="validobie">Bienios Válidos Salud Pública</label>
                                </div>                             
                                <div class="input-field col s2">
                                    <input type="text" align='right' name="ptos_bie" id="pto_bie" value="<?php echo $bienios_ptos;?>" placeholder="Total Puntaje Bienios"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Total Puntos Bienios</label>
                                </div>
                                <div class="input-field col s2">
                                    <input type="text" align='right' name="dias_no" id="dias_no" value="<?php echo $diasno;?>" placeholder="Días no Trabajados Salud Pública"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Días no Trabajados Salud Pública</label>
                                </div>
                                <div class="input-field col s2">
                                    <input type="text" align='right'  name="nuevo_cumple_bie" id="nuevo_cumple_bie" value="<?php echo $nuevainicial2;?>" placeholder="Cumple bienios"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Cumple Bienios</label>
                                </div>
                                <div class="input-field col s3">
                                    <input type="text" align='right' name="total_carrera" id="total_carrera" value="<?php echo $total_puntaje;?>" placeholder="Total Puntaje Bienios"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Total Puntos Carrera F.</label>
                                </div>
                                <div class="input-field col s4">
                                    <input type="text" align='right' name="nivel_carrera" id="nivel_carrera" value="<?php echo $nivel_actual;?>" placeholder="Nivel Actual"  disabled> 
                                    <label for="icon_prefix" id="fechaIngreso">Nivel Actual</label>
                                </div>  
                              <div class="input-field col s4">
                                <button class='btn trigger' type='button' onclick='ImprimirCC();'>Certificado Capacitación</button>
                              </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <script type="text/javascript" src="../../../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../../../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../../../include/js/materialize.js"></script>
        
</html>
    </body>