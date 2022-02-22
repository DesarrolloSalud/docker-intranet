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
  //Conecto a la base de datos
  include ("../../include/funciones/funciones.php");
  $cnn = ConectarPersonal();
  $dec_id = $_GET['id'];
  $decreto = "SELECT DATE_FORMAT(DF_FEC,'%d/%m/%Y'),DF_NUM FROM DECRETOS_FOR WHERE DF_ID = $dec_id";
  $resp_decreto = mysqli_query($cnn,$decreto);
  if(mysqli_num_rows($resp_decreto) == 0){
    session_destroy();
    header("location: ../../index.php");
  }
  $row_rd = mysqli_fetch_array($resp_decreto,MYSQLI_NUM);
  $df_fec = $row_rd[0];
  $df_num = $row_rd[1];
  //csv
  $csv_end = "\n";
  $csv_sep = ";";
  $csv="";
  //valores predeterminados
  $A = 5;
  $B = 1;
  $F = 540;
  $G = "MUNICIPALIDAD DE RENGO";
  $H = 113555;
  $I = "DEPARTAMENTO DE SALUD MUNICIPAL";
  $J = "DECRETO MUNICIPAL";
  $M = 3;
  $P = "FERIADO LEGAL";
  $R = "-1";
  $query = "SELECT FOLIO_DOC FROM DECRE_DETALLE WHERE DF_ID = $dec_id ORDER BY FOLIO_DOC ASC";
  $respuestaQuery = mysqli_query($cnn, $query);
  $csv.= "5;0;;;;;;;;;;;;;;;;;;;;;;;;;;".$csv_end;
  $csv.= ";0;RUN;DV;NOMBRE;COD SERVICIO;SERVICIO;COD DEP;DEPENDENCIA;TIPO;NUMERO;FECHA;TIPO DE INFORMACION;FECHA DESDE;FECHA HASTA;COMENTARIO;TOTAL DE DIAS;MOTIVO;OTRO MOTIVO;緾uenta con la Certificacion del Instituto Nacional del Deporte?;'Tiempo (Meses) Efectivo de Permiso Gremial';'Tiempo (Dias) Efectivo de Permiso Gremial';'Tiempo (Horas) Efectivo de Permiso Gremial';'Tiempo (Minutos) Efectivo de Permiso Gremial';'Beneficiario Del Permiso Postnatal';緾on reintegro laboral de 1/2 jornada?;SEMANAS Postnatal;DIAS Postnatal".$csv_end;
  while ($rowDEC = mysqli_fetch_array($respuestaQuery, MYSQLI_NUM)){
    $folio_doc = $rowDEC[0];
    $feriado = "SELECT S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,DATE_FORMAT(S.SP_FEC_INI,'%d/%m/%Y'),DATE_FORMAT(S.SP_FEC_FIN,'%d/%m/%Y') FROM SOL_PERMI S INNER JOIN USUARIO U ON S.USU_RUT = U.USU_RUT WHERE S.SP_ID = $folio_doc";
    $resp_feriado = mysqli_query($cnn,$feriado);
    $row = mysqli_fetch_array($resp_feriado,MYSQLI_NUM);
    $usu_rut = $row[0];
    list($rut, $dv) = split('[-]', $usu_rut);
    $rut = str_replace('.', '', $rut);
    $funcionario = utf8_decode($row[1])." ".utf8_decode($row[2])." ".utf8_decode($row[3]);
    $sp_fec_ini = $row[4];
    $sp_fec_fin = $row[5];
    //comienzo a crear csv
    $csv.=$A.$csv_sep.$B.$csv_sep.$rut.$csv_sep.$dv.$csv_sep.$funcionario.$csv_sep.$F.$csv_sep.$G.$csv_sep.$H.$csv_sep.$I.$csv_sep.$J.$csv_sep.$df_num.$csv_sep.$df_fec.$csv_sep.$M.$csv_sep.$sp_fec_ini.$csv_sep.$sp_fec_fin.$csv_sep.$P.$csv_sep.$csv_sep.$R.$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_end;
  }
header("Content-Description: File Transfer");
header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename=feriado_legal.csv");
echo $csv;
}

?>