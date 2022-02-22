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
        //$texto_ini = nl2br($_POST['inicio_decreto']);
       // $dcvisto = utf8_decode($_POST['dcvisto2']);
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
        $distribucionsub = utf8_decode($_POST['distribucionsub2']);
        $genalcalde = $_POST['genalcalde2'];
        $gensecre = $_POST['gensecre2'];
        $fechasub = $_POST['fechasub2'];
        $puncap = $_POST['puncap2'];
        $biepun = $_POST['biepun2'];
        $nuevonivel = $_POST['nuevonivel2'];
        
                    
        $buscar = "SELECT CB_ID FROM CARRERA_BIENIO WHERE (USU_RUT = '".$usurut."') AND (CB_ESTADO ='1')  ORDER BY CB_FEC_INI";
        $rs = mysqli_query($cnn, $buscar);
        if($row = mysqli_fetch_array($rs)){
            $insencdecre = "INSERT INTO DECRE_BIE (DB_DC_NUM,USU_RUT,DB_FEC,DB_VISTO,DB_CONSI,DB_DEC,DB_ALCALDE,DB_SECRE,DB_DERIVA,DB_FEC_CONT,DB_ACT_TOTAL,DB_BIE_PUN,DB_ALCSUB,DB_SECSUB,DB_GENALC,DB_GENSEC) 
            VALUES('$dcnum','$usurut','$dcfec','$dcvisto','$dcconsi','$dcdec','$alcalde','$secretaria','$distribucion','$fechasub','$puncap','$biepun','$alcaldesub','$secretariasub','$genalcalde','$gensecre')";
            mysqli_query($cnn, $insencdecre);
          
            $actualizanivel = "UPDATE USUARIO SET USU_NIV = '$nuevonivel' WHERE (USU_RUT= '".$usurut."')";
            mysqli_query($cnn, $actualizanivel);

            $query = "SELECT CB_ID FROM CARRERA_BIENIO WHERE (USU_RUT = '".$usurut."') AND (CB_ESTADO ='1')  ORDER BY CB_FEC_INI";    
            $respuesta = mysqli_query($cnn, $query);

            while ($row_rs = mysqli_fetch_array($respuesta)){
                $insencdecre = "INSERT INTO DECRE_BIE_DETA (DB_DC_NUM,CB_ID,USU_RUT) VALUES('$dcnum','$row_rs[0]','$usurut')";
                mysqli_query($cnn, $insencdecre);

                //$actualizaract = "UPDATE CARRERA_BIENIO SET CB_ESTADO = '3' WHERE (CA_ID = '$row_rs[0]')"; COMENTADO PARA NO ACTULIZAR EL ESTADO DEL BIENIO
                //mysqli_query($cnn, $actualizaract);
            }

            date_default_timezone_set("America/Santiago");
            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            //$id_formulario = 25;
            $ipcliente = getRealIP();
            $accionRealizada = utf8_decode("DECRETA NIVEL :  ".$nomusu." ".$appusu." ".$apmusu);
            $insertAccion = "INSERT INTO DECRE_BIE_LOG (DBL_ACC, DB_DC_NUM, USU_RUT,LA_IP_USU,DBL_FEC,DBL_HORA) VALUES ('$accionRealizada', '$dcnum', '$Srut', '$ipcliente', '$fecha', '$hora')";
            mysqli_query($cnn, $insertAccion);
       

        }else{
            
            $resultadolg = "ERROR";
            $resultado ['estado'] = "ERROR";                
            sleep(1);
        }
    
       
    }
       sleep(1);       
       echo json_encode($resultado);
        
    
?> 