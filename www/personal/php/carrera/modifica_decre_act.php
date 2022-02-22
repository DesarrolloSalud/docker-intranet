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
        $Srut = utf8_encode($_SESSION['USU_RUT']);
    	  include ("../../../include/funciones/funciones.php");
    	  $cnn = ConectarPersonal();
        $usurut = ($_POST['usurut2']);
        $dcnum = ($_POST['dcnum2']);
        $usurut = ($_POST['usurut2']);
        $dcfec = ($_POST['dcfec2']);
        
        $dcvisto = nl2br($_POST['dcvisto2']);       
        $dcconsi = nl2br($_POST['dcconsi2']);        
        $dcdec = nl2br($_POST['dcdec2']);       
        $nomusu = utf8_decode($_POST['nomusu2']);
        $appusu = utf8_decode($_POST['appusu2']);
        $apmusu = utf8_decode($_POST['apmusu2']);
        $alcalde = utf8_decode($_POST['alcalde2']);
        $alcaldesub = utf8_decode($_POST['alcaldesub2']);
        $secretaria = utf8_decode($_POST['secretaria2']);
        $secretariasub = utf8_decode($_POST['secretariasub2']);
        $distribucion = utf8_decode($_POST['distribucion2']);       
        $genalcalde = utf8_decode($_POST['genalcalde2']);
        $gensecre = utf8_decode($_POST['gensecre2']);
        $idmod = $_POST['id2'];
        
        $busca_deta = "SELECT DA_ID,DA_DC_NUM FROM DECRE_ACT WHERE (DA_ID = '$idmod')";    
         $respuesta = mysqli_query($cnn, $busca_deta);
            if($row1 = mysqli_fetch_array($respuesta)){
                $MuestroNum   = $row1[1];            
            }
      
          $modifica_enc = "UPDATE DECRE_ACT SET DA_DC_NUM ='$dcnum', DA_FEC='$dcfec', DA_VISTO = '$dcvisto', DA_CONSI='$dcconsi', DA_DEC='$dcdec', DA_ALCALDE='$alcalde', DA_SECRE='$secretaria', 
          DA_DERIVA='$distribucion', DA_ALCSUB='$alcaldesub', DA_SECSUB='$secretariasub', DA_GENALC='$genalcalde', DA_GENSEC='$gensecre' WHERE (DA_ID = '$idmod') AND (USU_RUT = '$usurut')";    
          mysqli_query($cnn,$modifica_enc);
      
          $modifica_deta ="UPDATE DECRE_ACT_DETA SET DA_DC_NUM = '$dcnum' WHERE (DA_DC_NUM='$MuestroNum') AND (USU_RUT = '$usurut')";
          mysqli_query($cnn,$modifica_deta);
         
            date_default_timezone_set("America/Santiago");
            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            //$id_formulario = 25;
            $ipcliente = getRealIP();
            $accionRealizada = utf8_decode("MODIFICA DECRETO ACTIVIDAD DE CAPACITACIÓN :  ID :".$idmod." ".$nomusu." ".$appusu." ".$apmusu);
            $insertAccion = "INSERT INTO DECRE_ACT_LOG (DAL_ACC, DA_DC_NUM, USU_RUT,LA_IP_USU,DAL_FEC,DAL_HORA) VALUES ('$accionRealizada', '$dcnum', '$Srut', '$ipcliente', '$fecha', '$hora')";
            mysqli_query($cnn, $insertAccion);     

      }   
        
       sleep(1);       
       echo json_encode($resultado);
?> 