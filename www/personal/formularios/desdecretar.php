<?php
	session_start();
	if(!isset($_SESSION['USU_RUT'])){
		session_destroy();
		header("location: ../../index.php");
	}else{
        if(count($_GET) && !$_SERVER['HTTP_REFERER']){
           header("location: ../error.php");
        }
        $Srut = utf8_encode($_SESSION['USU_RUT']);
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $df_id = $_GET['id'];
        //con el id del decreto recorro el detalle y actualizo documento a estado no decretado
        $query_doc = "SELECT DOC_ID FROM DECRE_DETALLE WHERE DF_ID = $df_id LIMIT 1";
        $respuesta_doc = mysqli_query($cnn,$query_doc);
        $r_doc = mysqli_fetch_array($respuesta_doc, MYSQLI_NUM);
        $doc_id = $r_doc[0];
        $query_folio = "SELECT FOLIO_DOC FROM DECRE_DETALLE WHERE DF_ID = $df_id";
        $respuesta_folio = mysqli_query($cnn,$query_folio);
        while ($row_fn = mysqli_fetch_array($respuesta_folio, MYSQLI_NUM)){
          $id_imp = $row_fn[0];
          echo $id_imp;
          echo "<br>";
          if($doc_id == 1 || $doc_id == 2){
            $respuesta_esta = mysqli_query($cnn,"SELECT SP_DECRE FROM SOL_PERMI WHERE SP_ID = $id_imp");
            $sp = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $sp_esta = $sp[0];
            echo $sp_esta;
            echo "<br>";
            mysqli_query($cnn,"UPDATE SOL_PERMI SET SP_DECRE = 'NO' WHERE SP_ID = $id_imp");
            $respuesta_esta = mysqli_query($cnn,"SELECT SP_DECRE FROM SOL_PERMI WHERE SP_ID = $id_imp");
            $sp = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $sp_esta = $sp[0];
            echo $sp_esta;
            echo "<br><br>";
          }
          if($doc_id == 3){
            $respuesta_esta = mysqli_query($cnn,"SELECT SP_DECRE FROM SOL_PERMI WHERE SP_ID = $id_imp");
            $sp = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $sp_esta = $sp[0];
            echo $sp_esta;
            echo "<br>";
            mysqli_query($cnn,"UPDATE SOL_PERMI SET SP_DECRE = 'NO' WHERE SP_ID = $id_imp");
            $respuesta_esta = mysqli_query($cnn,"SELECT SP_DECRE FROM SOL_PERMI WHERE SP_ID = $id_imp");
            $sp = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $sp_esta = $sp[0];
            echo $sp_esta;
            echo "<br><br>";
          }
          if($doc_id == 5){
            $respuesta_esta = mysqli_query($cnn,"SELECT OE_DECRE FROM OT_EXTRA WHERE OE_ID = $id_imp");
            $ot = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $ot_esta = $ot[0];
            echo $ot_esta;
            echo "<br>";
            mysqli_query($cnn,"UPDATE OT_EXTRA SET OE_DECRE = 'NO' WHERE OE_ID = $id_imp");
            $respuesta_esta = mysqli_query($cnn,"SELECT OE_DECRE FROM OT_EXTRA WHERE OE_ID = $id_imp");
            $ot = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $ot_esta = $ot[0];
            echo $ot_esta;
            echo "<br><br>";
          }
          if($doc_id == 6){
            $respuesta_esta = mysqli_query($cnn,"SELECT SAF_DECRE FROM SOL_ACU_FER WHERE SAF_ID = $id_imp");
            $saf = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $saf_esta = $saf[0];
            echo $saf_esta;
            echo "<br>";
            mysqli_query($cnn,"UPDATE SOL_ACU_FER SET SAF_DECRE = 'NO' WHERE SAF_ID = $id_imp");
            $respuesta_esta = mysqli_query($cnn,"SELECT SAF_DECRE FROM SOL_ACU_FER WHERE SAF_ID = $id_imp");
            $saf = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $saf_esta = $saf[0];
            echo $saf_esta;
            echo "<br><br>";
          }
          if($doc_id == 8){
            $respuesta_esta = mysqli_query($cnn,"SELECT CO_DECRE FROM COME_PERMI WHERE CO_ID = $id_imp");
            $co = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $co_esta = $co[0];
            echo $co_esta;
            echo "<br>";
            mysqli_query($cnn,"UPDATE COME_PERMI SET CO_DECRE = 'NO' WHERE CO_ID = $id_imp");
            $respuesta_esta = mysqli_query($cnn,"SELECT CO_DECRE FROM COME_PERMI WHERE CO_ID = $id_imp");
            $co = mysqli_fetch_array($respuesta_esta, MYSQLI_NUM);
            $co_esta = $co[0];
            echo $co_esta;
            echo "<br><br>";
          }
        }
        //actualizo estado de decreto a cancelado
        mysqli_query($cnn,"UPDATE DECRETOS_FOR SET DF_ESTA = 'CANCELADO POR RRHH' WHERE DF_ID = $df_id");
  }
?>