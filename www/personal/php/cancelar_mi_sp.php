<?php
    session_start();
    $Srut = utf8_encode($_SESSION['USU_RUT']);
    $Snombre = utf8_encode($_SESSION['USU_NOM']);
    $SapellidoP = utf8_encode($_SESSION['USU_APP']);
    $SapellidoM = utf8_encode($_SESSION['USU_APM']);
    $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
    $Scargo = utf8_encode($_SESSION['USU_CAR']);
    include ("../../include/funciones/funciones.php");
    $cnn = ConectarPersonal();
    $sp_id = $_POST['id'];
    //$sp_id = 1;
    date_default_timezone_set("America/Santiago");
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    $accionRealizada = utf8_decode("CANCELADO POR USUARIO");
    $query ="SELECT SP_ID,DOC_ID,SP_ANO,SP_CANT_DIA FROM SOL_PERMI WHERE (SP_ID = '$sp_id')";
    $rs = mysqli_query($cnn, $query);
    if (mysqli_num_rows($rs) != 0){
        $rowA = mysqli_fetch_row($rs);
        if ($rowA[0] == $sp_id){
            $doc_id = $rowA[1];
            $sp_ano = $rowA[2];
            $sp_dia = $rowA[3];
        }
    }
    $insertAccion = "INSERT INTO HISTO_PERMISO (HP_FOLIO, USU_RUT, HP_FEC, HP_HORA, DOC_ID, HP_ACC) VALUES ($sp_id,'$Srut','$fecha','$hora', $doc_id,'$accionRealizada')";
    //echo $insertAccion;
    //echo "</br>";
    mysqli_query($cnn, $insertAccion);
    $actualizarOT = "UPDATE SOL_PERMI SET SP_ESTA = 'CANCELADO POR USUARIO' WHERE (SP_ID = $sp_id)";
    mysqli_query($cnn, $actualizarOT);  
    //echo $actualizarOT;
    if($doc_id == 1){
        //busco sp_detalle_fl
        $select_spd = "SELECT SPD_FL,SPD_FLA FROM SP_DETALLE_FL WHERE SP_ID = $sp_id";
        $respuesta_spd = mysqli_query($cnn,$select_spd);
        if(mysqli_num_rows($respuesta_spd) != 0){
            $row_spd = mysqli_fetch_row($respuesta_spd);
            $spd_fl = $row_spd[0];
            $spd_fla = $row_spd[1];
        }
        //busco datos banco_dias
        $select_bd = "SELECT BD_ID,BD_FL,BD_FLA,BD_FL_USADO FROM BANCO_DIAS WHERE USU_RUT = '$Srut' AND BD_ANO = '$sp_ano'";
        $respuesta_bd = mysqli_query($cnn,$select_bd);
        if(mysqli_num_rows($respuesta_bd) != 0){
            $row_bd = mysqli_fetch_row($respuesta_bd);
            $bd_id = $row_bd[0];
            $bd_fl = $row_bd[1];
            $bd_fla = $row_bd[2];
            $bd_usado = $row_bd[3];
        }
        //calculo totales
        $total_fl = $spd_fl + $spd_fla; //total dias pedidos
        $bd_fl = $bd_fl + $spd_fl; //dias feriado legal mas los pedidos
        $bd_fla = $bd_fla + $spd_fla; //dias feriado legal acumulado mas los pedidos
        $bd_usado = $bd_usado - $total_fl; //dias feriado legal usados menos los pedidos
        //actualizar banco dias
        $update_bd = "UPDATE BANCO_DIAS SET BD_FL = $bd_fl, BD_FLA = $bd_fla, BD_FL_USADO = $bd_usado WHERE BD_ID = $bd_id";
        mysqli_query($cnn,$update_bd); 
        //borro registros
        $delete_spd = "DELETE FROM SP_DETALLE_FL WHERE SP_ID = $sp_id";
        mysqli_query($cnn,$delete_spd);
    }elseif($doc_id == 2){
        //convierto a decimal numero fraccion
        if($sp_dia == "1/2"){
            $sp_dia = 0.5;
        }
        //busco datos banco dias
        $select_bd = "SELECT BD_ID,BD_ADM,BD_ADM_USADO FROM BANCO_DIAS WHERE USU_RUT = '$Srut' AND BD_ANO = '$sp_ano'";
        $respuesta_bd = mysqli_query($cnn,$select_bd);
        if(mysqli_num_rows($respuesta_bd) != 0){
            $row_bd = mysqli_fetch_row($respuesta_bd);
            $bd_id = $row_bd[0];
            $bd_adm = $row_bd[1];
            $bd_usado = $row_bd[2];
        }
        //calculo totales
        $bd_adm = $bd_adm + $sp_dia; //dia adm mas los pedidos
        $bd_usado = $bd_usado - $sp_dia; //dias adm usados menos los pedidos
        //actualizo banco dias
        $update_bd = "UPDATE BANCO_DIAS SET BD_ADM = '$bd_adm', BD_ADM_USADO = '$bd_usado' WHERE BD_ID = $bd_id";
        mysqli_query($cnn,$update_bd);
    }elseif($doc_id == 3){
        //buscar el id del banco hora
        $select_bh = "SELECT BH_ID FROM BANCO_HORAS WHERE BH_TIPO = 'EGRESO' AND BH_ID_ANT = $sp_id";
        $respuesta_bh = mysqli_query($cnn, $select_bh);
        if(mysqli_num_rows($respuesta_bh) != 0){
            $r_rbh = mysqli_fetch_row($respuesta_bh);
            $bh_id = $r_rbh[0];
        }
        //rescato id bh ingreso y cantidad para hacer update por cada uno
        $select_bh_detalle = "SELECT BH_ID_INGRESO, BHD_CANT FROM BH_DETALLE_EGRESO WHERE BH_ID_EGRESO = $bh_id";
        $respuesta_bhd = mysqli_query($cnn,$select_bh_detalle);
        //por cada registro debo consultar banco horas
        if (mysqli_num_rows($respuesta_bhd) != 0){
            while ($row_bhd = mysqli_fetch_row($respuesta_bhd)){
                $bh_id_ingreso = $row_bhd[0];
                $bhd_cant      = $row_bhd[1];
                //rescato saldo de bh_id_ingreso
                $select_bh_ingreso = "SELECT BH_SALDO FROM BANCO_HORAS WHERE BH_ID = $bh_id_ingreso";
                $respuesta_bhi = mysqli_query($cnn,$select_bh_ingreso);
                $row_rbhi = mysqli_fetch_row($respuesta_bhi);
                $bh_saldo = $row_rbhi[0];
                //calculo nuevo saldo y hago update
                $bh_saldo = $bh_saldo + $bhd_cant;
                $update_bh = "UPDATE BANCO_HORAS SET BH_SALDO = $bh_saldo WHERE BH_ID = $bh_id_ingreso";
                mysqli_query($cnn,$update_bh);
            }
            //borrar todos los registros de bh_detalle_egreso
            $delete_bhd = "DELETE FROM BH_DETALLE_EGRESO WHERE BH_ID_EGRESO = $bh_id";
            mysqli_query($cnn,$delete_bhd);
            //borrar egreso de banco_horas
            $delete_bh = "DELETE FROM BANCO_HORAS WHERE BH_ID = $bh_id";
            mysqli_query($cnn,$delete_bh);
        }
    }
?>