<?php
//cargo conexion
include ("../include/funciones/funciones.php");
$cnn = ConectarPersonal();
//consulto quienes tienen horas compensadas
$df_id = 105;
date_default_timezone_set("America/Santiago");
$fecha = date("Y-m-d");
$hora = date("H:i:s");
$query = "SELECT OT.OE_ID,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,E.EST_NOM,OT.OE_DC_DIU,OT.OE_DC_NOC,OT.OE_CANT_DC,OT.OE_FEC 
FROM OT_EXTRA OT, USUARIO U, ESTABLECIMIENTO E, DECRE_DETALLE D
WHERE (OT.USU_RUT = U.USU_RUT) AND (U.EST_ID = E.EST_ID) AND (OT.OE_ID = D.FOLIO_DOC)
AND ( OT.OE_CANT_DC > 0) AND D.DF_ID = $df_id";
$respuesta = mysqli_query($cnn,$query);
while ($row_fn = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
  $id_imp = $row_fn[0];
  $buscar = "SELECT BH_ID FROM BANCO_HORAS WHERE (BH_TIPO = 'INGRESO') AND (BH_ID_ANT = $id_imp)";
  $respuestaBH = mysqli_query($cnn,$buscar);
  $rows = mysqli_num_rows($respuestaBH);
  if ($rows > 0){
  }else{
    //rescato los otros datos 
    $queryHoras ="SELECT OE_ID,USU_RUT,OE_CANT_DC FROM OT_EXTRA WHERE (OE_ID = $id_imp)";
    $rsH = mysqli_query($cnn, $queryHoras);
    if (mysqli_num_rows($rsH) != 0){
      $rowH = mysqli_fetch_row($rsH);
      if ($rowH[0] == $id_imp){
        $usu_rutOT = $rowH[1];
        $oe_cant_dc = $rowH[2];
      }
      if($oe_cant_dc > 0){
        $ingresoHRS = "INSERT INTO BANCO_HORAS (USU_RUT, BH_FEC, BH_TIPO, BH_CANT, BH_SALDO, BH_ID_ANT) VALUES ('$usu_rutOT','$fecha','INGRESO',$oe_cant_dc,$oe_cant_dc,$id_imp)";
        mysqli_query($cnn, $ingresoHRS);
        $ingresoHRS;
      }
    }
  }
}
?>