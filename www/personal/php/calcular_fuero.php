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
        function inicio($fecha){
          $fechaini = new DateTime();
          $fechaini->modify( 'first day of this month' );
          return $fechaini->format( 'Y-m-d' );
          $anoini = new DateTime();
          $anoini->modify( 'first day of this year' );
          return $anoini->format( 'Y-m-d' );
        }
        function fin($fecha){          
          $fechafin = new DateTime();
          $fechafin->modify( 'last day of this month' );
          return $fechafin->format( 'Y-m-d' );
          $anofin = new DateTime();
          $anofin->modify( 'first day of this year' );
          return $anofin->format( 'Y-m-d' );
        }
        date_default_timezone_set("America/Santiago");
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        $doc_id = $_POST['doc_id']; 
        $asocia = $_POST['asocia'];       
        include ("../../include/funciones/funciones.php");
        $dc_fec = date("Y-m-d");
        $fecano =date("Y");
       	$fechaini1 = inicio($dc_fec);
        $anoini1 = inicio($dc_fec);
        $fechafin1 = fin($dc_fec);
        $anofin1 = fin($dec_fec);
        $cnn = ConectarPersonal();
        
        //$consulta_dir ="SELECT GD_HORA FROM GREMI_DIR WHERE (USU_RUT='$Srut') AND (GD_ASOCIACION ='$asocia')";
        $consulta_dir ="SELECT GD_HORA,YEAR(GD_FEC_INI),GD_FEC_FIN,GD_ESTA,GD_ASOCIACION FROM GREMI_DIR WHERE (USU_RUT='$Srut') AND (GD_ESTA='ACTIVO') AND (GD_FEC_FIN > '$dc_fec') AND (GD_ASOCIACION='$asocia')";
        $rcdir = mysqli_query($cnn, $consulta_dir);        
        if (mysqli_num_rows($rcdir) != 0){
            $rcdir1 = mysqli_fetch_row($rcdir);
            $horasdir = $rcdir1[0]; 
        }else{
          $horasdir = 0;
        }
      
        /*$consulta_fuero ="SELECT FG_HOR_UTIL,FG_MIN_UTIL FROM GREMI_FOR WHERE (USU_RUT = '$Srut') AND (FG_FEC BETWEEN '$fechaini1' and '$fechafin1') AND (FG_TIP='1') OR (FG_TIP='2')";
        $resultado_fuero = mysqli_query($cnn, $consulta_fuero);
        if(mysqli_num_rows($resultado_fuero) != 0){
          while ($row_sum = mysqli_fetch_array($resultado_fuero)){
                $hora1  = $hora1+$row_sum[0];
                $minuto1 = $minuto1 + $row_sum[1];
            }
        }
        $horamin = intval($minuto1/60);
        if ($horamin < 1){
          $horamin=0;
        }
        $minmin = $minuto1%60;
        $sumtodo = $hora1 + $horamin;
        $suma1 = $sumtodo.":".$minmin;// horas y minutos utilizados  */    
      
        if ($doc_id == 1){//FUERO           
            $consulta_fuero ="SELECT FG_HOR_UTIL,FG_MIN_UTIL,FG_TIP,USU_RUT_DG FROM GREMI_FOR WHERE (USU_RUT = '$Srut') AND (FG_FEC BETWEEN '$fechaini1' and '$fechafin1') AND (FG_TIP < 3) AND (FG_ASOCIACION = '$asocia') AND (FG_ESTADO <>'EN CREACION') AND (FG_ESTADO <>'CANCELADO POR USUARIO')";
            $resultado_fuero = mysqli_query($cnn, $consulta_fuero);
            if(mysqli_num_rows($resultado_fuero) != 0){
              while ($row_sum = mysqli_fetch_array($resultado_fuero)){
                if($row_sum[2]==2 && $row_sum[3]== $Srut){
                    $horasdir = $horasdir+$row_sum[0];
                }else{
                  $hora1  = $hora1+$row_sum[0];
                  $minuto1 = $minuto1 + $row_sum[1];
                }                
             }
            
                       
              $horamin = intval($minuto1/60);
              if ($horamin < 1){
                $horamin=0;
              }
              $minmin = $minuto1%60;
              if ($minmin < 1){
                $minmin="00";
              }
              $sumtodo = $hora1 + $horamin;
              $suma1 = $sumtodo.":".$minmin;// horas y minutos utilizados
              
              $resultado ['doc_id'] = 1;
              $resultado ['gdhora'] = $horasdir;
              $resultado ['usado'] = $suma1;
              sleep(1);
              echo json_encode($resultado);                    
                
            }else{
              $resultado ['doc_id'] = 1;
              $resultado ['gdhora'] = $horasdir;
              $resultado ['usado'] = "00:00";
              sleep(1);
              echo json_encode($resultado);
            }
        }elseif($doc_id ==2){//traspaso
            $consulta_fuero ="SELECT SUM(FG_CANT) FROM GREMI_FOR WHERE (USU_RUT = '$Srut') AND (FG_FEC BETWEEN '$fechaini1' and '$fechafin1') AND (FG_TIP='2') AND (FG_ASOCIACION = '$asocia') AND (FG_ESTADO='SOLICITADO')";
            $resultado_fuero = mysqli_query($cnn, $consulta_fuero);
            if(mysqli_num_rows($resultado_fuero) != 0){
              while ($row_sum = mysqli_fetch_array($resultado_fuero)){
                    $suma1  = $row_sum[0];
                }
                            
              $resultado ['doc_id'] = 2;
              $resultado ['usado'] = $suma1;
              $resultado ['horasdir'] = $horasdir;
              
              sleep(1);
              echo json_encode($resultado);                    
                
            }else{
              $resultado ['doc_id'] = 2;
              $resultado ['usado'] = 0;
              $resultado ['horasdir'] = $horasdir;
              sleep(1);
              echo json_encode($resultado);
            }
        }elseif($doc_id ==4 || $doc_id==5){//PERFECCIONAMIENTO y ESPECIAL
            $consulta_fuero ="SELECT FG_DIA_UTIL FROM GREMI_FOR WHERE (USU_RUT = '$Srut') AND (FG_TIP ='$doc_id') AND (YEAR(FG_FEC)= '$fecano') AND (FG_ESTADO <> 'CANCELADO POR USUARIO') AND(FG_ASOCIACION = '$asocia')";
            $resultado_fuero = mysqli_query($cnn, $consulta_fuero);
            if(mysqli_num_rows($resultado_fuero) != 0){
              while ($row_sum = mysqli_fetch_array($resultado_fuero)){
                    $suma1  = $suma1+$row_sum[0];
                }
            }
            $consulta_fuerotras ="SELECT FG_DIA_UTIL FROM GREMI_FOR WHERE (USU_RUT_DG = '$Srut') AND (FG_TIP ='$doc_id') AND (YEAR(FG_FEC)= '$fecano') AND (FG_ESTADO <> 'CANCELADO POR USUARIO') AND (FG_ASOCIACION = '$asocia')";
            $resultado_fuerotras = mysqli_query($cnn, $consulta_fuerotras);
            if(mysqli_num_rows($resultado_fuerotras) != 0){
              while ($row_sumtras = mysqli_fetch_array($resultado_fuerotras)){
                    $sumatras1  = $sumatras1+$row_sumtras[0];
                }
            }
            $sumadis= $suma1 - $sumatras1;  
            $resultado ['doc_id'] = $doc_id;
            $resultado ['usado'] = $sumadis;
            sleep(1);
            echo json_encode($resultado); 


        }/*elseif($doc_id ==5){//ESPECIAL
            $consulta_fuero ="SELECT FG_DIA_UTIL FROM GREMI_FOR WHERE (USU_RUT = '$Srut') AND (FG_TIP ='$doc_id') AND (YEAR(FG_FEC_PER)= '$fecano') AND (FG_ASOCIACION = '$asocia')";
            $resultado_fuero = mysqli_query($cnn, $consulta_fuero);
            if(mysqli_num_rows($resultado_fuero) != 0){
              while ($row_sum = mysqli_fetch_array($resultado_fuero)){
                    $suma1  = $suma1+$row_sum[0];
                }
              $resultado ['doc_id'] = 5;
              $resultado ['usado'] = $suma1;
              sleep(1);
              echo json_encode($resultado); 
            }else{
              $resultado ['doc_id'] = 5;
              $resultado ['usado'] = 0;
              sleep(1);
              echo json_encode($resultado);
            }   
        }*/
    }    
?>

