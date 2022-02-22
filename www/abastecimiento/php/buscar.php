<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
        header("location: ../index.php");
    }else{
      require_once("../../include/funciones/funciones.php");
      $cnn = ConectarAbastecimiento();

      $request = 0;

      if(isset($_POST['request'])){
         $request = $_POST['request'];
      }
      if($request == 1){
        $rut = $_POST['rut'];               
        $stmt = $cnn->prepare("SELECT USU_RUT, USU_NOM,USU_APP,USU_APM,USU_MAIL,USU_FONO,USU_CAR,EST_ID,USU_DEP,USU_ESTA FROM USUARIO WHERE USU_RUT=?");
        $stmt->bindParam(1, $rut, PDO::PARAM_STR, 12);
        $stmt->execute();
        $statesList = $stmt->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "rut" => $state['USU_RUT'],
            "nom" => utf8_encode($state['USU_NOM']),
            "app" => utf8_encode($state['USU_APP']),
            "apm" => utf8_encode($state['USU_APM']),
            "correo" => utf8_encode($state['USU_MAIL']),
            "fono" => utf8_encode($state['USU_FONO']),
            "cargo" => utf8_encode($state['USU_CAR']),
            "estable" => utf8_encode($state['EST_ID']),
            "depende" => utf8_encode($state['USU_DEP']),
            "estado" => utf8_encode($state['USU_ESTA']),
          ); 
        }
        echo json_encode($response);
        exit;
      }
      if($request == 2){   
        try{          
          if($_POST['id'] ==0){
            $stmt = $cnn->prepare("select CAR_ID, CAR_DES from CARGOS ORDER BY CAR_DES");            
            $stmt->execute();
          }else{
            $stmt = $cnn->prepare("select CAR_ID, CAR_DES from CARGOS where CAR_ID=? ORDER BY CAR_DES");
            $stmt->bindParam(1, $_POST['id'], PDO::PARAM_INT);
            $stmt->execute();
          }          
          $statesList = $stmt->fetchAll();
          $response = array();
          foreach($statesList as $state){
            $response[] = array(
              "id" => $state['CAR_ID'],
              "name" => utf8_encode($state['CAR_DES']),
            );
          }  
          echo json_encode($response);
          exit;
        } catch (PDOException $e) { 
          $response ='Error al buscar.php 2 insert: ' . $e->getMessage() . ' ' .error_reporting(E_ALL);
          echo json_encode($response);
          exit;
        }  
      }
      if($request == 3){
        try{          
          if($_POST['id'] ==0){
            $stmt = $cnn->prepare("select EST_ID, EST_NOM from ESTABLECIMIENTO ORDER BY EST_NOM");            
            $stmt->execute();
          }else{
            $stmt = $cnn->prepare("select EST_ID, EST_NOM from ESTABLECIMIENTO where EST_ID=? ORDER BY EST_NOM");
            $stmt->bindParam(1, $_POST['id'], PDO::PARAM_INT);
            $stmt->execute();
          }          
          $statesList = $stmt->fetchAll();
          $response = array();
          foreach($statesList as $state){
            $response[] = array(
              "id" => $state['EST_ID'],
              "name" => utf8_encode($state['EST_NOM']),
            );
          }  
          echo json_encode($response);
          exit;
        } catch (PDOException $e) { 
          $response ='Error al buscar.php 3 insert: ' . $e->getMessage() . ' ' .error_reporting(E_ALL);
          echo json_encode($response);
          exit;
        }  
      }
      if($request == 4){
        try{          
          if($_POST['id'] ==0){
            $stmt = $cnn->prepare("select EST_ID, EST_NOM from ESTABLECIMIENTO ORDER BY EST_NOM");            
            $stmt->execute();
          }else{
            $stmt = $cnn->prepare("select EST_ID, EST_NOM from ESTABLECIMIENTO where EST_ID=? ORDER BY EST_NOM");
            $stmt->bindParam(1, $_POST['id'], PDO::PARAM_INT);
            $stmt->execute();
          }          
          $statesList = $stmt->fetchAll();
          $response = array();
          foreach($statesList as $state){
            $response[] = array(
              "id" => $state['EST_ID'],
              "name" => utf8_encode($state['EST_NOM']),
            );
          }  
          echo json_encode($response);
          exit;
        } catch (PDOException $e) { 
          $response ='Error al buscar.php 3 insert: ' . $e->getMessage() . ' ' .error_reporting(E_ALL);
          echo json_encode($response);
          exit;
        }  
      }
      /*if($request == 1){
        $codigo = $_POST['id'];
        $stmt = $cnn->prepare("SELECT prod_cod, prod_des, prod_stocka, prod_stockm, prod_uni, bodega.bode_cod, bodega.bode_des, bodega_estante.bode_estan_cod, bodega_estante.bode_estan_num, 
bodega_seccion.bode_seccion_cod ,bodega_seccion.bode_seccion_des, categoria.cp_id, categoria.cp_des, prod_marca, prod_serv, prod_estado FROM producto left join bodega on producto.bode_cod=bodega.bode_cod 
left join bodega_estante on producto.bode_estan_cod=bodega_estante.bode_estan_cod left join bodega_seccion on producto.bode_seccion_cod=bodega_seccion.bode_seccion_cod left join categoria on producto.cp_id=categoria.cp_id where prod_cod=?");
        $stmt->bindParam(1, $codigo, PDO::PARAM_STR, 100);
        $stmt->execute();
        $statesList = $stmt->fetchAll();
         $response = array();
         foreach($statesList as $state){
           $response[] = array(
             "cod" => $state['prod_cod'],
             "des" => $state['prod_des'],
             "stocka" => $state['prod_stocka'],
             "stockm" => $state['prod_stockm'],
             "uni" => $state['prod_uni'],
             "bodega" => $state['bode_cod'],
             "bodegades" => $state['bode_des'],
             "estante" => $state['bode_estan_cod'],
             "estantenum" => $state['bode_estan_num'],
             "seccion" => $state['bode_seccion_cod'],
             "secciondes" => $state['bode_seccion_des'],
             "clasificacion" => $state['cp_id'],
             "clasificaciondes" => $state['cp_des'],
             "marca" => $state['prod_marca'],
             "servicio" => $state['prod_serv'],
             "estado" => $state['bode_estado']             
           );
         }
        echo json_encode($response);
        exit;
      }
      if($request == 2){
        $stateid = $_POST['stateid'];
        $stmt = $cnn->prepare("select bode_seccion_cod, bode_seccion_des from bodega_seccion where bode_estan_cod =? order by bode_seccion_cod");
        $stmt->execute([$stateid]);
        $statesList = $stmt->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "id" => $state['bode_seccion_cod'],
            "name" => $state['bode_seccion_des']
          );
        }  
         echo json_encode($response);
         exit;
      }
      if($request == 3){
        $stmt = $cnn->prepare("select bode_cod, bode_des from bodega order by bode_cod");
        $stmt->execute();
        $statesList = $stmt->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "id" => $state['bode_cod'],
            "name" => $state['bode_des']
          );
        }  
        echo json_encode($response);
        exit;
      }
      if($request == 4){
        $pst = $cnn->prepare("select cp_id, cp_des from categoria where cp_estado='ACTIVO'");
        $pst->execute();
        $statesList = $pst->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "id" => $state['cp_id'],
            "name" => $state['cp_des']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request == 5){
        $codigo = $_POST['id'];
        $stmt = $cnn->prepare("select * from proveedor where prov_rut=?");
        $stmt->bindParam(1, $codigo, PDO::PARAM_STR, 12);
        $stmt->execute();
        $statesList = $stmt->fetchAll();
         $response = array();
         foreach($statesList as $state){
           $response[] = array(
             "rutp" => $state['prov_rut'],
             "nom" => $state['prov_nom'],
             "giro" => $state['prov_giro'],
             "dir" => $state['prov_dir'],
             "comuna" => $state['comuna'],
             "ciudad" => $state['ciudad'],
             "correo" => $state['prov_correo'],
             "fono" => $state['prov_fono'],
             "con" => $state['prov_con'],
             "fonocon" => $state['prov_fonocon'],
             "estado" => $state['prov_esta']           
           );
         }
        echo json_encode($response);
        exit;
      }
      if($request == 6){
        $pst = $cnn->prepare("select Nombre from comunas");
        $pst->execute();
        $statesList = $pst->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(            
            "name" => $state['Nombre']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request==7){
        $lastID = $_POST['id'];
        $todos = $_POST['todos'];
           
        $cnn = ConectarBoleta(); 
      if($todos != ''){
        $stmt= $cnn->prepare( "SELECT  prod_cod, prod_des, prod_stocka, prod_stockm, prod_uni, bodega.bode_des, bodega_estante.bode_estan_num, bodega_seccion.bode_seccion_des, categoria.cp_des, prod_marca, prod_serv, prod_estado FROM producto left join bodega on producto.bode_cod=bodega.bode_cod left join bodega_estante on producto.bode_estan_cod=bodega_estante.bode_estan_cod left join bodega_seccion on producto.bode_seccion_cod=bodega_seccion.bode_seccion_cod left join categoria on producto.cp_id=categoria.cp_id ORDER BY prod_cod desc");
        $stmt->execute();
      }elseif($lastID==''){
        $stmt= $cnn->prepare( "SELECT  prod_cod, prod_des, prod_stocka, prod_stockm, prod_uni, bodega.bode_des, bodega_estante.bode_estan_num, bodega_seccion.bode_seccion_des, categoria.cp_des, prod_marca, prod_serv, prod_estado FROM producto left join bodega on producto.bode_cod=bodega.bode_cod left join bodega_estante on producto.bode_estan_cod=bodega_estante.bode_estan_cod left join bodega_seccion on producto.bode_seccion_cod=bodega_seccion.bode_seccion_cod left join categoria on producto.cp_id=categoria.cp_id ORDER BY prod_cod desc limit 10 ");
        $stmt->execute();
      }elseif($lastID){
        $stmt = $cnn->prepare( "SELECT prod_cod, prod_des, prod_stocka, prod_stockm, prod_uni, bodega.bode_des, bodega_estante.bode_estan_num, bodega_seccion.bode_seccion_des, categoria.cp_des, prod_marca, prod_serv, prod_estado FROM producto left join bodega on producto.bode_cod=bodega.bode_cod left join bodega_estante on producto.bode_estan_cod=bodega_estante.bode_estan_cod left join bodega_seccion on producto.bode_seccion_cod=bodega_seccion.bode_seccion_cod left join categoria on producto.cp_id=categoria.cp_id where prod_cod < ? ORDER BY prod_cod desc limit 10 ");
        $stmt->bindParam(1, $lastID);
        $stmt->execute();          
      }
        $statesList = $stmt->fetchAll();
        $response = array();
        foreach($statesList as $state){        
          $response[] = array(
          "cod"=>$state['prod_cod'],
          "des"=>$state['prod_des'],
          "stocka"=>$state['prod_stocka'],
          "stockm"=>$state['prod_stockm'],
          "uni"=>$state['prod_uni'],
          "bodega"=>$state['bode_des'],
          "estante"=>$state['bode_estan_num'],
          "seccion"=>$state['bode_seccion_des'],
          "clasificacion"=>$state['cp_des'],
          "marca"=>$state['prod_marca'],
          "servicio"=>$state['prod_serv'],
          "estado"=>$state['prod_estado']
        );
      }
      echo json_encode($response);
      exit;
      }
      if($request == 8){
        $id= $_POST['id'];
        $pst = $cnn->prepare("select bt_doc, bt_des from bode_tipdoc where bt_esta='ACTIVO' order by bt_des");
        $pst->execute();
        $statesList = $pst->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "id" => $state['bt_doc'],
            "name" => $state['bt_des']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request == 9){
        $cli_rut= $_POST['rut_cli'];
        $pst = $cnn->prepare("select cli_rut, cli_nom, cli_apepat, cli_apemat from cliente where cli_rut=?");
        $pst->execute([$cli_rut]);
        $statesList = $pst->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "id" => $state['cli_rut'],
            "name" => $state['cli_nom'],
            "apepat" => $state['cli_apepat'],
            "apemat" => $state['cli_apemat']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request == 10){
        $fecha = date("Y-m-d");
        $cli_rut= $_POST['rut_cli'];
        $pst = $cnn->prepare("select cliente_convenio.cv_id, convenio.cv_nombre, convenio.cv_descuento, convenio.cp_id from cliente_convenio inner join convenio on cliente_convenio.cv_id=convenio.cv_id 
where cli_rut =? and cv_termino > ?");
        $pst->execute([$cli_rut, $fecha]);
        $statesList = $pst->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "id" => $state['cv_id'],
            "name" => $state['cv_nombre'],
            "descuento" => $state['cv_descuento'],
            "categoria" => $state['cp_id']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request == 11){
        $fecha = date("Y-m-d");
        //$cli_rut= $_POST['rut_cli'];
        $id= $_POST['id'];
        $pst = $cnn->prepare("select cv_descuento from convenio where cv_id =? and cv_termino > ?");
        $pst->execute([$id, $fecha]);
        $statesList = $pst->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "descuento" => $state['cv_descuento']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request == 12){
        $des= $_POST['des'];
        $des= "%$des%";
        $estado='ACTIVO';
        $pst = $cnn->prepare("select prod_cod, prod_des from producto where prod_des like ? and prod_estado=?");
        $pst->execute([$des, $estado]);
        $statesList = $pst->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "id" => $state['prod_cod'],
            "name" => $state['prod_des']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request == 13){
        $codigo = $_POST['id'];
        $stmt = $cnn->prepare("SELECT producto.prod_cod, prod_des, prod_stocka, prod_uni, categoria.cp_id, categoria.cp_des, producto_venta.pv_id, producto_venta.pv_venta FROM producto inner join producto_venta on producto.prod_cod=producto_venta.prod_cod inner join categoria on producto.cp_id=categoria.cp_id where producto.prod_cod=? order by pv_id desc limit 1");
        $stmt->bindParam(1, $codigo, PDO::PARAM_STR, 100);
        $stmt->execute();
        $statesList = $stmt->fetchAll();
         $response = array();
         foreach($statesList as $state){
           $response[] = array(
             "cod" => $state['prod_cod'],
             "des" => $state['prod_des'],
             "stocka" => $state['prod_stocka'],
             "stockm" => $state['prod_stockm'],
             "uni" => $state['prod_uni'],
             "clasificacion" => $state['cp_id'],
             "clasificaciondes" => $state['cp_des'],
             "precio" => $state['pv_venta'],
             "idprecio" => $state['pv_id']
           );
         }
        echo json_encode($response);
        exit;
      }
      if($request==14){
        // //venta: venta, cod: cod, idprecio: idprecio, precio: precio, descuento: descuento, cantidad: cantidad, iva: iva
        $id= $_POST['id'];
        $pst = $cnn->prepare("select venta_detalle.prod_cod, producto.prod_des, pv_id, pv_venta, vd_descu, vd_prod_cant, vd_iva from venta_detalle left join producto on venta_detalle.prod_cod=producto.prod_cod where ve_id=?");      
        $pst->execute([$id]);
        $resultado = $pst->fetchAll();
        $response = array();
        foreach($resultado as $row1){
          $response[] = array(
          "cod" => $row1['prod_cod'],
          "des"=>$row1['prod_des'],
          "idprecio" => $row1['pv_id'],
          "precio" => $row1['pv_venta'],
          "descuento" => $row1['vd_descu'],
          "cantidad" => $row1['vd_prod_cant'],
          "iva" => $row1['vd_iva']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request == 15){
        $pst = $cnn->prepare("select fp_id, fp_des from pago where fp_esta='ACTIVO' order by fp_des");
        $pst->execute();
        $statesList = $pst->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "id" => $state['fp_id'],
            "name" => $state['fp_des']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request == 16){
        $doc = $_POST['doc'];
        $estado ='ACTIVO';
        $pst = $cnn->prepare("select folio_detalle.folio_id,fd_num from folio inner join folio_detalle on folio.folio_id=folio_detalle.folio_id where folio.bt_doc =? and fd_estado=? order by fd_num limit 1");
        $pst->execute([$doc, $estado]);
        $statesList = $pst->fetchAll();
        $response = array();
        foreach($statesList as $state){
          $response[] = array(
            "id" => $state['folio_id'],
            "num" => $state['fd_num']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request==17){
        try{
          $rut = $_POST['rut'];
          $razon = $_POST['razon'];
          $doc = $_POST['doc'];
          $autoriza = $_POST['autoriza'];
          $ini = $_POST['ini'];
          $fin = $_POST['fin'];
          $idk = $_POST['idk'];
          $firma = $_POST['firma'];
          $RSASK = $_POST['RSASK'];
          $RSAPUBK = $_POST['RSAPUBK'];
          $RSAPK = $_POST['RSAPK'];
          $pst = $cnn->prepare("select folio_id, folio_fecing from folio where emp_rut=? and emp_nom=? and bt_doc=? and folio_ini=? and folio_fin=? and folio_fecha=? and folio_IDK=? and folio_firma=? and folio_RSASK=? and folio_RSAPUBK=? and folio_RSAPK=?");
          $pst->execute([$rut, $razon, $doc, $ini, $fin, $autoriza, $idk, $firma, $RSASK, $RSAPUBK, $RSAPK]);
          $resultado = $pst->fetchAll();
          $response= array();
          foreach($resultado as $row){
            $response[] = array(
              "id"=> $row['folio_id'],
              "fecha" => $row['folio_fecing']        
            );
          }
          echo json_encode($response);
          exit;
        } catch (PDOException $e) { 
          $response ='Error al buscar buscar.php 17 insert: ' . $e->getMessage() . ' ' .error_reporting(E_ALL);
          echo json_encode($response);
          exit;
        } 
      }
      if($request==18){
        $pst = $cnn->prepare("select DISTINCT (folio_detalle.folio_id),folio.emp_rut , folio.emp_nom, bode_tipdoc.bt_des, folio_ini, folio_fin, folio_fecha,folio.folio_fecing, folio_detalle.fd_estado, count(folio_detalle.fd_num) as conteo from folio_detalle inner join folio on folio_detalle.folio_id= folio.folio_id left join bode_tipdoc on bode_tipdoc.bt_doc=folio.bt_doc where folio_detalle.fd_estado ='ACTIVO'group by folio.folio_id ");
        $pst->execute();
        $resultado = $pst->fetchAll();
        $response= array();
        foreach($resultado as $row){
          $response[] = array(
            "id"=> $row['folio_id'],
            "rut"=> $row['emp_rut'],
            "razon"=> $row['emp_nom'],
            "doc"=> $row['bt_des'],
            "ini"=> $row['folio_ini'],
            "fin"=> $row['folio_fin'],
            "fecha"=> $row['folio_fecha'],            
            "fechaing" => $row['folio_fecing'],
            "folio"=> $row['conteo']
          );
        }
        echo json_encode($response);
        exit;
      }
      if($request==19){
        $doc = $_POST['doc'];
        $rut = $_POST['rut'];
        $ndoc = $_POST['ndoc'];        
        try {
          $pdo = $cnn->prepare('select bod_doc_id, n_doc, bod_doc_enc.bt_doc, bode_tipdoc.bt_des,de_fec1, de_fec2, n_ord, de_esta, de_sr from bod_doc_enc inner join bode_tipdoc on bod_doc_enc.bt_doc = bode_tipdoc.bt_doc where prov_rut=? and bod_doc_enc.bt_doc=? and n_doc=?');
          $pdo->execute([$rut,$doc,$ndoc]);
          $resultado = $pdo->fetchAll();        
          $response= array();
          foreach($resultado as $row){
            $response[] = array(
              "id" => $row['bod_doc_id'],
              "doc" => $row['bt_doc'],
              "ndoc" => $row['n_doc'],
              "docdes" => $row['bt_des'],
              "fdoc" => $row['de_fec1'],
              "fven" => $row['de_fec2'],
              "norden" => $row['n_ord'],
              "estado" => $row['de_esta'],
              "sr"=> $row['de_sr']
            );
          }
          echo json_encode($response);
          //exit;
        } catch (PDOException $e) { 
          $response ='Error al buscar 19: ' . $e->getMessage() . ' ' .error_reporting(E_ALL). '  '.$_SERVER['PHP_SELF'];
          echo json_encode($response);
        }
      }
      if($request==20){
        try {
          $id= $_POST['id'];
          $estado = $_POST['estado'];
          if($estado == 'BORRADOR'){
            $pst = $cnn->prepare("select bod_doc_detalle.bod_doc_id, bod_doc_detalle.prod_cod, producto.prod_des, producto.prod_uni, bod_doc_detalle.prod_cant, bod_doc_detalle.prod_val, bod_doc_detalle.prod_lote, bod_doc_detalle.prod_vcto, bod_doc_detalle.bod_de_descu from bod_doc_detalle left join producto on bod_doc_detalle.prod_cod=producto.prod_cod where bod_doc_id=?");      
          $pst->execute([$id]);
          }elseif($estado=='GUARDADO'){
            $pst = $cnn->prepare("select bod_doc_detallef.bod_doc_id, bod_doc_detallef.prod_cod, producto.prod_des, producto.prod_uni, bod_doc_detallef.prod_cant, bod_doc_detallef.prod_val, bod_doc_detallef.prod_lote, bod_doc_detallef.prod_vcto, bod_doc_detallef.bod_de_descu from bod_doc_detallef left join producto on bod_doc_detallef.prod_cod=producto.prod_cod where bod_doc_id=?");      
          $pst->execute([$id]);
          }
          
          $resultado = $pst->fetchAll();
          $response = array();
          foreach($resultado as $row1){
            $response[] = array(
              "id" => $row1['bod_doc_id'],
              "cod" => $row1['prod_cod'],
              "des"=>$row1['prod_des'],
              "unidad" => $row1['prod_uni'],
              "cantidad" => $row1['prod_cant'],
              "precio" => $row1['prod_val'],
              "lote" => $row1['prod_lote'],
              "vencimiento" => $row1['prod_vcto'],
              "descuento" => $row1['bod_de_descu']
            );
          }
          echo json_encode($response);
          exit;
        } catch (PDOException $e) { 
          $response ='Error al buscar 20: ' . $e->getMessage() . ' ' .error_reporting(E_ALL);
          echo json_encode($response);
        }  
      }
      if($request==21){
        try {
          $rut = $_POST['rut'];
          $turno =$_POST['turno'];
          $fecha= $_POST['fecha'];
          //$id = $_POST['id'];
          //$fp_id=1;
          $estado ='REALIZADA';
          $id=0;
          if($turno=='tu'){
            $pdo = $cnn->prepare('select ve_id, bode_tipdoc.bt_des, ve_fec, ve_hora, ve_total, venta_enc.fp_id, pago.fp_des from venta_enc inner join bode_tipdoc on venta_enc.bt_doc= bode_tipdoc.bt_doc inner join pago on venta_enc.fp_id=pago.fp_id where usu_rut=? and cc_id=? and ve_fec=? and ve_esta=? ORDER BY ve_fec desc');
            $pdo->execute([$rut,$id, $fecha, $estado]);
          }elseif($turno =='to'){
            $pdo = $cnn->prepare('select ve_id, bode_tipdoc.bt_des, ve_fec, ve_hora, ve_total, venta_enc.fp_id, pago.fp_des from venta_enc inner join bode_tipdoc on venta_enc.bt_doc= bode_tipdoc.bt_doc inner join pago on venta_enc.fp_id=pago.fp_id where cc_id=? and ve_fec <=? and ve_esta=? ORDER BY ve_fec desc');
            $pdo->execute([$id, $fecha, $estado]);
          }
          $resultado = $pdo->fetchAll();
          foreach($resultado as $row1){
            $response[] = array(
              "id" => $row1['ve_id'],
              "documento" => $row1['bt_des'],
              "fecha" => $row1['ve_fec'],
              "hora" => $row1['ve_hora'],
              "total" => $row1['ve_total'],
              "tipo" => $row1['fp_id'],
              "fpago" => $row1['fp_des']
            ); 
          }
          echo json_encode($response);
          exit;
        } catch (PDOException $e) { 
          $response ='Error al buscar 21: ' . $e->getMessage() . ' ' .error_reporting(E_ALL);
          echo json_encode($response);
        }  
                 
      }
      if($request==22){
        try {
          $rut = $_POST['rut'];
          $clave= $_POST['clave'];
          $observacion = $_POST['observacion'];
          $id = $_POST['id'];
          $dc = $_POST['descuadre'];
          $pwd=hash('sha256', $clave); 
          $pst = $cnn->prepare("select usu_rut, usu_esta,usu_admin from usuario where usu_rut=? and usu_clave=?");
          $pst->execute([$rut, $pwd]);
          $resultado = $pst->fetchAll();
          foreach($resultado as $row){     
            if($row['usu_esta'] == 'ACTIVO' and $row['usu_admin'] == 'checked'){              
              //se actualiza cierre, al verificar que el usuario es administrador
              $sqlf = $cnn->prepare("UPDATE cierre_caja_diario SET cc_dc=?, cc_usu_rut=?, cc_motivo=? WHERE cc_id=?");
              $sqlf->bindParam(1, $dc, PDO::PARAM_INT);           
              $sqlf->bindParam(2, $rut, PDO::PARAM_STR, 12);
              $sqlf->bindParam(3, $observacion, PDO::PARAM_STR, 200);
              $sqlf->bindParam(4, $id, PDO::PARAM_INT);
              $sqlf->execute();
              $response ='OK';
              echo json_encode($response);
              exit;
            }else{
              $response ='NO';
              echo json_encode($response);
              exit;
            }
          }
          if($response==''){
            $response ='NO';
            echo json_encode($response);
            exit;
          }
        } catch (PDOException $e) { 
          $response ='Error al buscar 22: ' . $e->getMessage() . ' ' .error_reporting(E_ALL);
          echo json_encode($response);
        }  
                 
      }
      if($request==23){
        $codigo = $_POST['id'];
        $stmt = $cnn->prepare("select cli_rut, cli_nom, cli_apepat, cli_apemat, cli_fec_nac, cli_giro, comuna, ciudad, cli_dir, cli_fono, cli_fono1, cli_correo, cli_correo, cli_estado from cliente where cli_rut=?");
        $stmt->bindParam(1, $codigo, PDO::PARAM_STR, 12);
        $stmt->execute();
        $statesList = $stmt->fetchAll();
         $response = array();
         foreach($statesList as $state){
           $response[] = array(
             "rutc" => $state['cli_rut'],
             "nomc" => $state['cli_nom'],
             "apepc" =>$state['cli_apepat'],
             "apemc" =>$state['cli_apemat'],
             "fechanc" =>$state['cli_fec_nac'],
             "giroc" => $state['cli_giro'],
             "comuna" => $state['comuna'],
             "ciudadc" => $state['ciudad'],
             "dirc" => $state['cli_dir'],
             "fonoc" => $state['cli_fono'],
             "fono1c" => $state['cli_fono1'],
             "correoc" => $state['cli_correo'],
             "estadoc" => $state['cli_estado']           
           );
         }
        echo json_encode($response);
        exit;
      }*/
      
    }
?>


 