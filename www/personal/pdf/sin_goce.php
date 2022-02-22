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
        require('../../include/fpdf/fpdf.php');
        //Conecto a la base de datos
        include ("../../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $sp_id = $_GET['id'];
        $consulta = "SELECT DOCUMENTO.DOC_NOM,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_RUT,SOL_PSGR.USU_RUT_DIR,USUARIO.USU_CAT,SOL_PSGR.SPR_TOA,DATE_FORMAT(SOL_PSGR.SPR_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PSGR.SPR_FEC_FIN,'%d-%m-%Y'),SOL_PSGR.SPR_NDIA,SOL_PSGR.SPR_DIANT,SOL_PSGR.SPR_DIASAL,SOL_PSGR.SPR_MOT,SOL_PSGR.SPR_ESTA,SOL_PSGR.SPR_COM,USUARIO.USU_DIR,USUARIO.USU_DEP,ESTABLECIMIENTO.EST_DIR,USUARIO.USU_FONO,USUARIO.USU_FEC_ING FROM SOL_PSGR INNER JOIN USUARIO ON SOL_PSGR.USU_RUT=USUARIO.USU_RUT INNER JOIN DOCUMENTO ON SOL_PSGR.DOC_ID=DOCUMENTO.DOC_ID INNER JOIN ESTABLECIMIENTO ON USUARIO.EST_ID=ESTABLECIMIENTO.EST_ID WHERE SOL_PSGR.SPR_ID = '$sp_id'";
        $respuesta = mysqli_query($cnn, $consulta);

        //echo $consulta;
        if (mysqli_num_rows($respuesta) == 1){

            $rowSP = mysqli_fetch_row($respuesta);
            if ($rowSP[14] == "AUTORIZADO DIR SALUD"){
                $doc_nom        = $rowSP[0];
                $usu_nom        = $rowSP[1];
                $usu_app        = $rowSP[2];
                $usu_apm        = $rowSP[3];
                $usu_rut        = $rowSP[4];
                //$usu_rut_jd     = $rowSP[5];
                $usu_rut_dir    = $rowSP[5];
                $usu_cat        = $rowSP[6];
                $sg_toa         = $rowSP[7];
                $sg_fec_ini     = $rowSP[8];
                $sg_fec_fin     = $rowSP[9];
                $sg_ndia        = $rowSP[10];
                $sg_ante        = $rowSP[11];
                $sg_saldo       = $rowSP[12];
                $sg_motivo      = $rowSP[13];
                $sg_estado      = $rowSP[14];
                $sg_comen       = $rowSP[15];
                $usu_dir        = $rowSP[16];
                $usu_dep        = $rowSP[17];
                $est_dir        = $rowSP[18];
                $usu_fono        = $rowSP[19];
                $usu_fec_ing        = $rowSP[20];              
                $consultaUSUR2 = "SELECT USU_JEF,USU_NOM,USU_APP,USU_APM FROM USUARIO WHERE (USU_RUT = '$usu_rut_dir')";
                $respuestaUSUR2 = mysqli_query($cnn, $consultaUSUR2);
                $rowUSUR2 = mysqli_fetch_row($respuestaUSUR2);
                if ($rowUSUR2[0] == "SI"){
                    $usu2_nom = $rowUSUR2[1];
                    $usu2_app = $rowUSUR2[2];
                    $usu2_apm = $rowUSUR2[3];
                    class PDF extends FPDF{
                    // Page header
                        function Header(){
                            // Logo
                            $this->Image('../../include/img/header.jpg',1,1,210,20);
                            $this->Ln(5);
                        }
                        function Footer(){
                            $this->Image('../../include/img/footer.jpg',3,305,205,20);
                        }
                    }    
                }

                $pdf = new PDF();
                //Agregamos la primera pagina al documento pdf
                $pdf->AddPage('P',array(215.9,330.2));
                //Seteamos el tiupo de letra y creamos el titulo de la pagina. No es un encabezado no se repetira
                $pdf->SetFont('Arial','B',10);
                $pdf->Header();
                $pdf->SetX(140);
                $pdf->Write(5,'FECHA :');
                $pdf->SetX(160);
                $pdf->Cell(30,5,'',1,1,'C');
                $pdf->Ln(1);
                $pdf->SetX(140);
                $pdf->Write(5,'FOLIO  :');
                $pdf->SetX(160);
                $pdf->Cell(30,5,'',1,1,'C');
                $pdf->Ln(10);
                $pdf->Write(5,'SOLICITUD DE :  '.$doc_nom);
                $pdf->Ln(10);
                $pdf->Write(5,'Rut :  '.$usu_rut);
                $pdf->SetX(110);
                $pdf->Write(5,'Fecha Ingreso :  '.$usu_fec_ing);
                $pdf->Ln(5);
                $pdf->Write(5,'De :  '.$usu_nom." ".$usu_app." ".$usu_apm);
                $pdf->Ln(5);
                $pdf->Write(5,'A :  '.$usu2_nom." ".$usu2_app." ".$usu2_apm);
                $pdf->SetX(110);
                $pdf->Write(5,utf8_decode('Categoría :  '.$usu_cat));
                $pdf->Ln(5);
                $pdf->Write(5,utf8_decode('Unidad de Desempeño :  '.$usu_dep));
                $pdf->Ln(5);
                $pdf->Write(5,utf8_decode('Ubicación Laboral:  ' ). utf8_decode($est_dir));
                $pdf->Ln(5);
                $pdf->Write(5,utf8_decode('Dirección Particular : ' .$usu_dir));
                $pdf->Ln(5);
                $pdf->Write(5,utf8_decode('Teléfono Laboral : ' .$sg_toa));
                $pdf->SetX(110);
                $pdf->Write(5,utf8_decode('Teléfono Particular : ' .$usu_fono));
                $pdf->Ln(10);
                $pdf->Write(5,utf8_decode('Agradeceré a la Dirección Concederme: '));
                $pdf->Ln(10);
                $pdf->Write(5,utf8_decode('Número de Días: '.$sg_ndia));
                $pdf->SetX(70);                   
                $pdf->Write(5,utf8_decode('Desde: '.$sg_fec_ini));
                $pdf->SetX(140);
                $pdf->Write(5,utf8_decode('Hasta: '.$sg_fec_fin));
                $pdf->Ln(10);
                $pdf->Write(5,utf8_decode('Días Utilizados Antes de Esta Solicitud: '.$sg_ante));
                $pdf->Ln(5);
                $pdf->Write(5,utf8_decode('Saldo Considerando Esta Solicitud: '.$sg_saldo));
                $pdf->SetY(125);
                $pdf->Write(5,'Motivo : '.$sg_motivo);
                //$pdf->Line(130,135,180,135);
                $pdf->Ln(15);
                $pdf->Line(20,164,80,164);
                $pdf->Ln(29);
                $pdf->SetX(21);
                $firma= '../../include/img/firmas/'.$usu_rut.'.png'; 
                    if (is_readable($firma)) {
                        $pdf->Image($firma,30,142,40,20);
                    } 
                $pdf->Write(5,'FIRMA FUNCIONARIO');
                $pdf->Line(120,164,180,164);
                $pdf->SetX(127);
                $firma= '../../include/img/firmas/'.$usu_rut_dir.'.png'; 
                    if (is_readable($firma)) {
                        $pdf->Image($firma,130,142,40,20);
                    } 
                $pdf->Write(5,'FIRMA Y TIMBRE DIRECTOR');
                $pdf->Ln(10);
                $pdf->SetX(100);
                $pdf->Write(5,utf8_decode('DECRETO ALCALDICIO N° _______________/(MP)'));
                $pdf->Ln(5);
                $pdf->SetX(100);
                $pdf->Write(5,utf8_decode('FECHA : ___________________'));
                $pdf->Ln(10);
                $pdf->Write(5,utf8_decode('CONSIDERACIONES LEY No 19.738, ESTATUTO DE ATENCIÓN PRIMARIA DE SALUD MUNICIPAL:'));
                $pdf->Ln(5);
                $pdf->SetFont('Arial','B',8);
                $pdf->Write(5,utf8_decode('Artículo 17.- Los funcionarios podrán solicitar permisos para ausentarse de sus labores por motivos particulares hasta por seis días hábiles en el año calendario, con goce de sus remuneraciones. Estos permisos podrán fraccionarse por días o medios días, y serán concedidos o denegados por el Director del establecimiento, según las necesidades del servicio.'),'L');
                $pdf->Ln(5);
                $pdf->Write(5,utf8_decode('     Asimismo, podrán solicitar sin goce de remuneraciones, por motivos particulares, hasta tres meses de permiso en cada año calendario.'),'L');
                $pdf->Ln(5);
                $pdf->Write(5,utf8_decode('     El límite señalado en el inciso anterior, no será aplicable en el caso de funcionarios que obtengan becas otorgadas de acuerdo a la legislación vigente.'),'L');
                $pdf->Ln(5);
                $pdf->Write(5,utf8_decode('     Los funcionarios regidos por esta ley, que fueren elegidos alcaldes en conformidad a lo dispuesto en la ley N° 18.695, Orgánica Constitucional de Municipalidades, tendrán derecho a que se les conceda permiso sin goce de remuneraciones respecto de las funciones que estuvieren sirviendo en calidad de titulares, por todo el tiempo que comprenda su desempeño alcaldicio.'),'L');
                $pdf->Ln(10);
                $pdf->Write(5,utf8_decode(' LA JEFATURA TIENE LA FACULTAD DE AUTORIZAR O DENEGAR EL PERMISO POR RAZONES DE SERVICIO. EL PERMISO DEBE SER REQUERIDO CON LA DEBIDA ANTELACIÓN, ANTES DE HACERSE EFECTIVO; DEBE SER FIRMADO POR EL INTERESADO Y LA JEFATURA RESPECTIVA ACREDITANDO CON ELLO SU CONFORMIDAD Y SER PRESENTADO EN EL DEPARTAMENTO DE SALUD.'),'L');
                $pdf->Ln(10);
                $pdf->SetFont('Arial','B',10);  
                $pdf->Line(20,300,80,300);                    
                $pdf->SetY(300);
                $pdf->SetX(28);
                $pdf->Write(5,utf8_decode('SECRETARIA MUNICIPAL'));
                $pdf->Line(120,300,180,300);
                $firma= '../../include/img/firmas/'.$usu_rut_dir.'.png'; //AGREGAR FIRMA CUANDO ESTÉ DECRETADO
                if (is_readable($firma)) {
                    $pdf->Image($firma,130,279,40,20);
                } 
                $pdf->SetX(123);                    
                $pdf->Write(5,utf8_decode('DIRECTOR SALUD MUNICIPAL'));
                $pdf->Ln(30);
                //$pdf->Write(5,$cal_dias_fer_aact);
                $pdf->Output();
            }
        }
    }
?>

