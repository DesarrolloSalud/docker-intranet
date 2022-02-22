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
		<ul id="liquidaciones" class="dropdown-content">
			<li><a href="http://200.68.34.158/personal/parametros/liquidacion/mi_liquidacion.php">Mi Liquidación</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/parametros/liquidacion/liquidacion2.php">Carga de Liquidación</a></li>
		</ul>
		<ul id="licencias" class="dropdown-content">
			<li><a href="http://200.68.34.158/personal/licencias/mis_licencias.php">Mis Licencias</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/licencias/carga_licencias.php">Carga de Licencias</a></li>
			<li><a href="http://200.68.34.158/personal/licencias/ver_licencias.php">Licencias Medicas</a></li>
      <li><a href="http://200.68.34.158/personal/licencias/licencias_funcionario.php">Licencias por Funcionario</a></li>
      <li><a href="http://200.68.34.158/personal/licencias/licencias_observadas.php">Licencias Observadas</a></li>
		</ul>
		<ul id="carrera" class="dropdown-content">
			<li><a href="http://200.68.34.158/personal/parametros/carrera/mi_carrera.php">Mi Carrera</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/parametros/carrera/carrera_funcionario.php">Carrera por Funcionario</a></li>
			<li><a href="http://200.68.34.158/personal/parametros/carrera/mant_capacitacion.php">Mantenedor Capacitaciones</a></li>
			<li><a href="http://200.68.34.158/personal/parametros/carrera/reco_capacita.php">Reconocimiento Capacitaciones</a></li>
			<li><a href="http://200.68.34.158/personal/parametros/carrera/reco_bienio.php">Reconocimiento Nivel</a></li>
			<li class="divider"></li>
			<li><a href="#!">Estado Carrera Funcionario</a></li>
		</ul>
		<ul id="formularios" class="dropdown-content">
			<?php 
			//echo $Srut;
			if($Sestablecimiento == 3 && $Sprof == "CHOFER AMBULANCIA"){
				echo '<li><a href="http://200.68.34.158/personal/formularios/for_solicitud_permiso_turnos.php">Permisos</a></li>';
			}else{
				echo '<li><a href="http://200.68.34.158/personal/formularios/for_solicitud_permiso.php">Permisos</a></li>';
			}
			?>
			<li><a href="http://200.68.34.158/personal/formularios/for_sin_goce.php">Permiso sin Goce de Rem.</a></li>
			<li><a href="http://200.68.34.158/personal/formularios/for_ot_extra.php">Orden de Trabajo Extra.</a></li>
			<li><a href="http://200.68.34.158/personal/formularios/for_cometido.php">Cometido</a></li>
			<li><a href="http://200.68.34.158/personal/formularios/for_acu_fer.php">Acumulacion de Feriados</a></li>
      <li><a href="http://200.68.34.158/personal/formularios/for_gremial.php">Permisos Gremiales</a></li>
      <li><a href="http://200.68.34.158/personal/formularios/for_capacitaciones.php">Capacitaciones</a></li>
      <li><a href="http://200.68.34.158/personal/formularios/for_otros.php">Otros Permisos</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/formularios/formularios.php">Mis Formularios</a></li>
		</ul>
		<ul id="documentos" class="dropdown-content">
			<li><a href="http://200.68.34.158/personal/formularios/decretos.php">Decretos Masivos</a></li>
			<li><a href="http://200.68.34.158/personal/formularios/historico_decretos.php">Buscador Decretos</a></li>
			<li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/parametros/mant_ot_extra.php">Memo Autorización O.T.</a></li>
			<li><a href="http://200.68.34.158/personal/formularios/decre_autootextra.php">Decreto Autorizacion O.T.</a></li>
      <li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/formularios/edit_ot_extra.php">Visualizador OT</a></li>
			<li><a href="http://200.68.34.158/personal/formularios/visualizador_for.php">Visualizador Formularios</a></li>
		</ul>
		<ul id="configuraciones" class="dropdown-content">
			<li><a href="http://200.68.34.158/personal/parametros/cambio_clave.php">Cambiar Clave</a></li>
			<li><a href="http://200.68.34.158/personal/parametros/firma_usuario.php">Firma</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/parametros/mant_usuarios.php">Mantenedor de Usuarios</a></li>
			<li><a href="http://200.68.34.158/personal/parametros/mant_dias.php">Mantenedor de Dias</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/parametros/mant_ot_extra.php">Autorización Ordenes de Trabajo</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/parametros/mant_establecimientos.php">Mantenedor de Establecimientos</a></li> 
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/parametros/mant_formularios.php">Mantenedor de Formularios</a></li>
			<li><a href="http://200.68.34.158/personal/parametros/mant_programas.php">Mantenedor de Programas</a></li>
			<li class="divider"></li>
			<li><a href="http://200.68.34.158/personal/parametros/registros_sistema.php">Log de Registro</a></li> 
		</ul>
    <ul id="liquidaciones-m" class="dropdown-content">
      <li><a href="http://200.68.34.158/personal/parametros/liquidacion/mi_liquidacion.php">Mi Liquidación</a></li>
      <li class="divider"></li>
      <li><a href="#!">Carga de Liquidación</a></li> 
    </ul>
    <ul id="licencias-m" class="dropdown-content">
      <li><a href="http://200.68.34.158/personal/licencias/mis_licencias.php">Mis Licencias</a></li>
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/licencias/carga_licencias.php">Carga de Licencias</a></li>
      <li><a href="http://200.68.34.158/personal/licencias/ver_licencias.php">Licencias Medicas</a></li>
      <li><a href="http://200.68.34.158/personal/licencias/licencias_funcionario.php">Licencias por Funcionario</a></li>
    </ul>
    <ul id="carrera-m" class="dropdown-content">
      <li><a href="#!">Mi Carrera</a></li>
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/parametros/carrera/carrera_funcionario.php">Carrera por Funcionario</a></li>
      <li><a href="http://200.68.34.158/personal/parametros/carrera/mant_capacitacion.php">Mantenedor Capacitaciones</a></li>
      <li><a href="http://200.68.34.158/personal/parametros/carrera/reco_capacita.php">Reconocimiento Capacitaciones</a></li>
      <li><a href="http://200.68.34.158/personal/parametros/carrera/reco_bienio.php">Reconocimiento Nivel</a></li>
      <li class="divider"></li>
      <li><a href="#!">Estado Carrera Funcionario</a></li>
    </ul>
    <ul id="formularios-m" class="dropdown-content">
      <?php 
      //echo $Srut;
      if($Sestablecimiento == 3 && $Sprof == "CHOFER AMBULANCIA"){
        echo '<li><a href="http://200.68.34.158/personal/formularios/for_solicitud_permiso_turnos.php">Permisos</a></li>';
      }else{
        echo '<li><a href="http://200.68.34.158/personal/formularios/for_solicitud_permiso.php">Permisos</a></li>';
      }
      ?>
      <li><a href="http://200.68.34.158/personal/formularios/for_sin_goce.php">Permiso sin Goce</a></li>
      <li><a href="http://200.68.34.158/personal/formularios/for_ot_extra.php">Orden de Trabajo</a></li>
      <li><a href="http://200.68.34.158/personal/formularios/for_cometido.php">Cometido</a></li>
      <li><a href="http://200.68.34.158/personal/formularios/for_acu_fer.php">A. de Feriados</a></li>
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/formularios/formularios.php">Mis Formularios</a></li>
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/formularios/decretos.php">Decretos Masivos</a></li>
      <li><a href="http://200.68.34.158/personal/formularios/historico_decretos.php">Buscador Decretos</a></li>
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/formularios/edit_ot_extra.php">Visualizador OT</a></li>
    </ul>
    <ul id="documentos-m" class="dropdown-content">
      <li><a href="http://200.68.34.158/personal/formularios/decretos.php">Decretos Masivos</a></li>
      <li><a href="http://200.68.34.158/personal/formularios/historico_decretos.php">Buscador Decretos</a></li>
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/formularios/edit_ot_extra.php">Visualizador OT</a></li>
      <li><a href="http://200.68.34.158/personal/formularios/visualizador_for.php">Visualizador Formularios</a></li>
    </ul>
    <ul id="configuraciones-m" class="dropdown-content">
      <li><a href="http://200.68.34.158/personal/parametros/cambio_clave.php">Cambiar Clave</a></li>
      <li><a href="http://200.68.34.158/personal/parametros/firma_usuario.php">Firma</a></li>
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/parametros/mant_usuarios.php">M. Usuarios</a></li>
      <li><a href="http://200.68.34.158/personal/parametros/mant_dias.php">M. Dias</a></li>
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/parametros/carrera/mant_capacitacion.php">M. Capacita...</a></li> 
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/parametros/mant_ot_extra.php">Autorizacion OT</a></li> 
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/parametros/mant_establecimientos.php">M. Estableci...</a></li> 
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/parametros/mant_formularios.php">M. Formularios</a></li>	
      <li><a href="http://200.68.34.158/personal/parametros/mant_programas.php">M. Programas</a></li>	
      <li class="divider"></li>
      <li><a href="http://200.68.34.158/personal/parametros/registros_sistema.php">Log Registros</a></li>		
    </ul>
    <div class="navbar-fixed">
      <nav class="light-blue accent-3">
        <div class="nav-wrapper">
          <a href="http://200.68.34.158/personal/index.php" class="brand-logo">&nbsp&nbsp&nbsp&nbsp&nbsp&nbspSIPER</a>
          <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
          <ul class="right hide-on-med-and-down">
            <?php
            if($Srut == "17.333.639-K" || $Srut == "15.738.663-8"){
              echo '<li><a data-target="sistema" href="http://200.68.34.158/personal/parametros/otro.php">Ver Sistema</a></li>'; 
            }
            ?>
            <li><a class="dropdown-trigger" href="#!" data-target="liquidaciones">Liquidaciones</a></li>
            <li><a class="dropdown-trigger" href="#!" data-target="licencias">Licencias Medicas</a></li>
            <li><a class="dropdown-trigger" href="#!" data-target="carrera">Carrera Funcionario</a></li>
            <li><a class="dropdown-trigger" href="#!" data-target="formularios">Formularios&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a></li>
            <li><a class="dropdown-trigger" href="#!" data-target="documentos">Documentos&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a></li>
            <li><a class="dropdown-trigger" href="#!" data-target="configuraciones">Parametros&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a></li>
            <li><a href="http://200.68.34.158/index.php" target="_self">Cerrar Sesión</a></li>
          </ul>
        </div>
      </nav>
    </div>

    <ul class="sidenav" id="mobile-demo">
      <?php
      if($Srut == "17.333.639-K" || $Srut == "15.738.663-8"){
        echo '<li><a data-target="sistema-m" href="http://200.68.34.158/personal/parametros/otro.php">Ver Sistema</a></li>'; 
      }
      ?>
      <li><a class="dropdown-trigger" href="#!" data-target="liquidaciones-m">Liquidaciones</a></li>
      <li><a class="dropdown-trigger" href="#!" data-target="licencias-m">Licencias Medicas</a></li>
      <li><a class="dropdown-trigger" href="#!" data-target="carrera-m">Carrera Funcionario</a></li>
      <li><a class="dropdown-trigger" href="#!" data-target="formularios-m">Formularios</a></li>
      <li><a class="dropdown-trigger" href="#!" data-target="documentos-m">Documentos</a></li>
      <li><a class="dropdown-trigger" href="#!" data-target="configuraciones-m">Parametros</a></li>
      <li><a href="http://200.68.34.158/index.php" target="_self">Cerrar Sesión</a></li>
    </ul>
