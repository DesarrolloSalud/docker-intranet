<?php
  session_start();
  //unset($_SESSION["nombre_usuario"]); 
  //unset($_SESSION["nombre_cliente"]);
  $_SESSION = array();
  session_destroy();
?>
  <!DOCTYPE html>
  <html>
  <!-- prueba  -->
  <head>
    <title>Intranet Departamento de Salud de Rengo</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Le decimos al navegador que nuestra web esta optimizada para moviles -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <!-- Cargamos el CSS -->
    <link type="text/css" rel="stylesheet" href="include/css/icon.css" />
    <link type="text/css" rel="stylesheet" href="include/css/materialize.min.css" media="screen,projection" />
    <link type="text/css" rel="stylesheet" href="include/css/custom.css" />
    <link href="include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection" />
  </head>

  <body>
		<ul id="organizacion" class="dropdown-content">
			<li><a href="https://rengo-fono-salud.web.app/dashboard" target="_blank">FONO SALUD</a></li>
			<li class="divider"></li>
			<li><a href="https://rengo-rosario-web.web.app/dashboard" target="_blank">CESFAM ROSARIO</a></li>
            <li class="divider"></li>
			<li><a href="https://rengo-rienzi-valencia-gonzalez.web.app/dashboard" target="_blank">CESFAM DR RIENZI VALENCIA</a></li>
            <li class="divider"></li>
			<li><a href="https://rengo-urbano-oriente.web.app/dashboard" target="_blank">CESFAM RENGO URBANO ORIENTE</a></li>
		</ul>
		<ul id="organizacion-m" class="dropdown-content">
			<li><a href="https://rengo-fono-salud.web.app/dashboard" target="_blank">FONO SALUD</a></li>
			<li class="divider"></li>
			<li><a href="https://rengo-rosario-web.web.app/dashboard" target="_blank">CESFAM ROSARIO</a></li>
            <li class="divider"></li>
			<li><a href="https://rengo-rienzi-valencia-gonzalez.web.app/dashboard" target="_blank">CESFAM DR RIENZI VALENCIA</a></li>
            <li class="divider"></li>
			<li><a href="https://rengo-urbano-oriente.web.app/dashboard" target="_blank">CESFAM RENGO URBANO ORIENTE</a></li>
		</ul>
		<ul id="farmacia" class="dropdown-content">
			<li><a href="http://www.erp-software.cl/rengo/" target="_blank">SISTEMA FARMACIA</a></li>
			<li class="divider"></li>
			<li><a href="https://rengo-web-farmacia.web.app/dashboard" target="_blank">PRE INSCRIPCION</a></li>
		</ul>
		<ul id="farmacia-m" class="dropdown-content">
            <li><a href="http://www.erp-software.cl/rengo/" target="_blank">SISTEMA FARMACIA</a></li>
			<li class="divider"></li>
			<li><a href="https://rengo-web-farmacia.web.app/dashboard" target="_blank">PRE INSCRIPCION</a></li>
		</ul>
		<ul id="interes" class="dropdown-content">
			<li><a href="https://wlme.medipass.cl/WebPublic/" target="_blank">L.M.E.</a></li>
			<li class="divider"></li>
			<li><a href="http://131.221.166.134:7781/webresultados/" target="_blank">BioMaas</a></li>
			<li class="divider"></li>
			<li><a href="bienestar/index.php">Bienestar</a></li>
		</ul>
		<ul id="interes-m" class="dropdown-content">
      <li><a href="https://wlme.medipass.cl/WebPublic/" target="_blank">L.M.E.</a></li>
			<li class="divider"></li>
			<li><a href="http://131.221.166.134:7781/webresultados/" target="_blank">BioMaas</a></li>
			<li class="divider"></li>
			<li><a href="bienestar/index.php">Bienestar</a></li>
		</ul>
    <div class="navbar-fixed">
      <nav class="light-blue accent-3">
        <div class="nav-wrapper">
          <a href="index.php" class="brand-logo right">Intranet Salud</a>
          <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
          <ul class="left hide-on-med-and-down">
            <li><a href="#!" data-activates="portal"><b>PORTAL DE SALUD</b></a></li>
            <li><a class="dropdown-trigger" href="#!" data-target="organizacion" data-activates="organizacion"><b>SOME - FONOSALUD</b></a></li>
            <li><a href="personal/login.php" data-activates="personal"><b>PERSONAL</b></a></li>
            <li><a href="abastecimiento/login.php" data-activates="abastecimiento"><b>ABASTECIMIENTO</b></a></li>
            <li><a href="http://200.68.34.158:8080/" data-activates="control" target="_blank"><b>RELOJ CONTROL</b></a></li>
            <li><a class="dropdown-trigger" href="#!" data-target="farmacia" data-activates="farmacia"><b>F. POPULAR</b></a></li>
            <li><a href="https://correo.munirengo.cl/owa" data-activates="control" target="_blank"><b>WEBMAIL</b></a></li>
			      <!-- <li><a href="https://wlme.medipass.cl/WebPublic/" data-activates="control" target="_blank"><b>L.M.E.</b></a></li> -->
			      <li><a class="dropdown-trigger" href="#!" data-activates="interes" data-target="interes"><b>LINK DE INTERÉS</b></a></li>
          </ul>
        </div>
      </nav>
    </div>
    <ul class="sidenav" id="mobile-demo">
      <li><a class="dropdown-trigger" href="#!" data-target="organizacion-m"><b>ORGANIZACION</b></a></li>
      <li><a href="personal/login.php" data-activates="personal"><b>PERSONAL</b></a></li>
      <li><a href="abastecimiento/login.php" data-activates="abastecimiento"><b>ABASTECIMIENTO</b></a></li>
      <li><a href="http://200.68.34.158:8080/" data-activates="control" target="_blank"><b>RELOJ CONTROL</b></a></li>
      <li><a class="dropdown-trigger" href="#!" data-target="farmacia-m"><b>F. POPULAR</b></a></li>
      <li><a href="https://correo.munirengo.cl/owa" data-activates="control" target="_blank"><b>WEBMAIL</b></a></li>
      <li><a class="dropdown-trigger" href="#!" data-target="interes-m"><b>LINK DE INTERÉS</b></a></li>
      <!-- <li><a href="https://wlme.medipass.cl/WebPublic/" data-activates="control" target="_blank"><b>L.M.E.</b></a></li> -->
    </ul>
    <div class="row"></div>
    <div class="row"></div>
    <div class="row"></div>
    <div class="row"></div>
    <div class="row">
      <div class="col s4"></div>
      <div class="col s4">
        <iframe width="560" height="315" src="https://www.youtube.com/embed/Mvs895Ex0Zg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      </div>
      <div class="col s4"></div>
      <!--
        <iframe width="560" height="315" src="https://www.youtube.com/embed/hVudquihL24" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        <div class="col s4" >
        <iframe width="400" height="315" src="https://www.youtube.com/embed/E2nuzCuPUGo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br/>
        Si tienes problemas para ver el video,<a href="https://drive.google.com/file/d/1kqk7lJuAF1Bk0qrJpbLhEOQ8DZyQ9Myb/view?usp=sharing" target="_blank">descarga aquí</a> 
      </div>
       <div class="col s4">
        <iframe width="400" height="315" src="https://www.youtube.com/embed/zuLTjsOhE_U" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br/>
        Si tienes problemas para ver el video,<a href="https://drive.google.com/file/d/1Ramliu0CIpUaPCzMxx3aPkeJpn9zPQpg/view?usp=sharing" target="_blank">descarga aquí</a> 
      </div>
       <div class="col s4">
        <iframe width="400" height="315" src="https://www.youtube.com/embed/tyd90C8kxXo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br/>
        Si tienes problemas para ver el video,<a href="https://drive.google.com/file/d/1t3E_w5BlXCVyzUPlznx7vUbUFdvCrPIK/view?usp=sharing" target="_blank">descarga aquí</a> 
      </div>-->

      <div class="row"></div>
      <div class="row"></div>
      <div class="col s12 m4 l4">
        <center>
          <a href="http://200.68.34.158/include/reglamento-interno.pdf" target="_blank"><img src="include/img/index_principal_12.png" width="278" height="64"></a>
        </center>
      </div>
      <div class="col s12 m4 l4">
        <center>
          <a href="http://200.68.34.158/include/decreto_1649.pdf" target="_blank"><img src="include/img/index_principal_13.png" width="278" height="64"></a>
        </center>
      </div>
      <div class="col s12 m4 l4">
        <center>
          <a href="http://200.68.34.158/include/afiche.pdf" target="_blank"><img src="include/img/index_principal_14.png" width="278" height="64"></a>
        </center>
      </div>
    </div>
    <div class="row"></div>
    <div class="row"></div>

    <div class="row"></div>
    <div class="row"></div>
    <div class="row"></div>
    <div class="row">
      <!--<div class="col s6 offset-s3"><img class="responsive-img" src="include/img/LOGO_CESFAM_salud.png" style=" z-index: 90;"></div> -->
      
    </div>
    <!-- <div class="row">
        <div class="col s6">
        <a href="http://200.68.34.158/include/decreto_v3.pdf" target="_blank"><img class="responsive-img" src="../include/img/index_principal_7.png" style="position: absolute; top: 15%; right: 5%; z-index: 100;opacity: 0.9;"></a>
        </div>
        <div class="col s6">
        <a href="http://200.68.34.158/include/bases_v3.pdf" target="_blank"><img class="responsive-img" src="../include/img/index_principal_8.png" style="position: absolute; top: 25%; right: 5%; z-index: 100;opacity: 0.9;"></a>
        </div>
        <div class="col s6">
        <a href="http://200.68.34.158/include/anexos.docx" target="_blank"><img class="responsive-img" src="../include/img/index_principal_9.png" style="position: absolute; top: 35%; right: 5%; z-index: 100;opacity: 0.9;"></a>
        </div>
        <div class="col s6">
        <a href="http://200.68.34.158/include/DECLARACION_JURADA_SIMPLE.doc" target="_blank"><img class="responsive-img" src="../include/img/index_principal_10.png" style="position: absolute; top: 45%; right: 5%; z-index: 100;opacity: 0.9;"></a>
        </div>
      </div> -->
    <footer class="page-footer orange col l6 s12" style="position: fixed; bottom: 0; width: 100%; z-index: 9999;">
      <div class="footer-copyright">
        <div class="container">
          <a class="grey-text text-lighten-4 right">© 2017 Unidad de Informatica - Direccion de Salud Municipal - Rengo.</a>
        </div>
      </div>
    </footer>
    <!-- Cargamos jQuery y materialize js -->
    <script type="text/javascript" src="include/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="include/js/materialize.min.js"></script>
    <script>
      $(document).ready(function() {
          $('.sidenav').sidenav();
          $(".dropdown-trigger").dropdown();
      })
    </script>
  </body>

  </html>
