<?php	
if(!isset($_SESSION['USU_RUT'])){
		session_destroy();
		header("location: ../index.php");
	}else{
		$Srut = utf8_encode($_SESSION['USU_RUT']);
		$Snombre = utf8_encode($_SESSION['USU_NOM']);
		$SapellidoP = utf8_encode($_SESSION['USU_APP']);
		$SapellidoM = utf8_encode($_SESSION['USU_APM']);
		$Semail = utf8_encode($_SESSION['USU_MAIL']);
		$Scargo = utf8_encode($_SESSION['USU_CAR']);
    $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
		$Sestablecimiento = $_SESSION['EST_ID'];
		$Sprof = utf8_encode($_SESSION['USU_PROF']);
}
?>
<!-- Dropdown Structure -->
		<ul id="configuraciones" class="dropdown-content">
			<li><a href="!#">Cambiar Clave</a></li>
			<li><a href="!#">Firma</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/abastecimiento/parametros/mant_usuario.php">Mantenedor de Usuarios</a></li>
			<li><a href="!#">Mantenedor de Perfiles</a></li>
			<li class="divider"></li>
      <li><a href="!#">Mantenedor de Establecimientos</a></li> 
			<li><a href="!#">Mantenedor de Bodega</a></li>
      <li><a href="!#">Clasificacion de Productos</a></li>
      <li><a href="!#">Mantenedor de Productos</a></li>
			<li class="divider"></li>
      <li><a href="!#">Mantenedor de Proveedores</a></li>
      <li><a href="!#">Mantenedor de Documentos</a></li>
			<li class="divider"></li>
			<li><a href="!#">Log de Registro</a></li> 
		</ul>
    <ul id="configuraciones-m" class="dropdown-content">
			<li><a href="!#">Cambiar Clave</a></li>
			<li><a href="!#">Firma</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/abastecimiento/parametros/mant_usuario.php">Mantenedor de Usuarios</a></li>
			<li><a href="!#">Mantenedor de Perfiles</a></li>
			<li class="divider"></li>
      <li><a href="!#">Mantenedor de Establecimientos</a></li> 
			<li><a href="!#">Mantenedor de Bodega</a></li>
      <li><a href="!#">Clasificacion de Productos</a></li>
      <li><a href="!#">Mantenedor de Productos</a></li>
			<li class="divider"></li>
      <li><a href="!#">Mantenedor de Proveedores</a></li>
      <li><a href="!#">Mantenedor de Documentos</a></li>
			<li class="divider"></li>
			<li><a href="!#">Log de Registro</a></li> 
    </ul>
    <div class="navbar-fixed">
      <nav class="light-blue accent-3">
        <div class="nav-wrapper">
          <a href="http://200.68.34.158/abastecimiento/index.php" class="brand-logo">&nbsp&nbsp&nbsp&nbsp&nbsp&nbspSIABA</a>
          <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
          <ul class="right hide-on-med-and-down">
            <li><a class="dropdown-trigger" href="#!" data-target="configuraciones">Parametros&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a></li>
            <li><a href="http://200.68.34.158/index.php" target="_self">Cerrar Sesión</a></li>
          </ul>
        </div>
      </nav>
    </div>
    <ul class="sidenav" id="mobile-demo">
      <li><a class="dropdown-trigger" href="#!" data-target="configuraciones-m">Parametros</a></li>
      <li><a href="http://200.68.34.158/index.php" target="_self">Cerrar Sesión</a></li>
    </ul>
