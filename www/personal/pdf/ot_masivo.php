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
				class PDF extends FPDF{
					// Page header
					function Header(){
						// Logo
						$this->Image('../../include/img/header.jpg',1,1,210,20);
						$this->Ln(5);
					}
					function Footer(){
						$this->Image('../../include/img/footer.jpg',3,305,205,20);
						// Posición: a 1,5 cm del final
						$this->SetY(-15);
						// Arial italic 8
						$this->SetFont('Arial','I',8);
						// Número de página
						$this->Cell(0,10,'Pag '.$this->PageNo(),0,0,'R');
					}
				}
				date_default_timezone_set("America/Santiago");
				$pdf = new PDF();			
				$pdf->AddFont('GothamBooK','','GothamBook.php');
				$pdf->AddFont('GothamBold','','GOTHAM-BOLD.php');
				$dec_id = $_GET['id'];
				$query = "SELECT FOLIO_DOC FROM DECRE_DETALLE WHERE DF_ID = $dec_id ORDER BY FOLIO_DOC ASC";
				$respuestaQuery = mysqli_query($cnn, $query);
	      if (mysqli_num_rows($respuestaQuery) != 0){
        	while ($rowDEC = mysqli_fetch_array($respuestaQuery, MYSQLI_NUM)){
						$folio_doc = $rowDEC[0];
						$consulta = "SELECT USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,USUARIO.USU_CAT,OT_EXTRA.OE_TRAB,OT_EXTRA.USU_RUT_JF,OT_EXTRA.USU_RUT_DIR,OT_EXTRA.USU_RUT_VB,OT_EXTRA.OE_CANT_CANCE,OT_EXTRA.OE_CANT_DC,OT_EXTRA.OE_ESTA,USUARIO.USU_DEP,OT_EXTRA.OE_ID FROM OT_EXTRA,USUARIO WHERE (OT_EXTRA.USU_RUT = USUARIO.USU_RUT) AND (OT_EXTRA.OE_ID = $folio_doc)";
						$respuesta = mysqli_query($cnn, $consulta);
						//echo $consulta;
						if (mysqli_num_rows($respuesta) == 1){
						 //while ($rowOE = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
												$rowOE = mysqli_fetch_array($respuesta, MYSQLI_NUM);
												$usu_nom       = $rowOE[0];
												$usu_app       = $rowOE[1];
												$usu_apm       = $rowOE[2];
												$usu_cat       = $rowOE[3];
												$oe_trab       = $rowOE[4];
												$usu_rut_jd    = $rowOE[5];
												$usu_rut_dir   = $rowOE[6];
												$usu_rut_vb   = $rowOE[7];
												$oe_cant_cance = $rowOE[8];
												$oe_cant_dc    = $rowOE[9];
												$oe_esta       = $rowOE[10];
												$usu_dep       = $rowOE[11];
												$oe_id									= $rowOE[12];
												//Agregamos la primera pagina al documento pdf
												$pdf->AddPage('P',array(215.9,330.2));
												//Seteamos el tiupo de letra y creamos el titulo de la pagina. No es un encabezado no se repetira
												$pdf->SetFont('GothamBold');
												$pdf->SetFontSize(10);
												$pdf->Header();
												$pdf->SetX(0);
												$pdf->Cell(210,5,'ORDEN DE TRABAJOS EXTRAORDINARIOS ',0,1,'C');
												$pdf->Ln(10);
												$pdf->SetFont('GothamBooK');
												$pdf->Write(5,utf8_decode('Se ordena los siguientes trabajos extraordinarios :                                                                          Folio : '.$oe_id));
												$pdf->Ln(10);
												$pdf->Write(5,'Para el Funcionario : '.$usu_nom." ".$usu_app." ".$usu_apm);
												$pdf->Ln(5);
												$pdf->Write(5,utf8_decode('Categoría : ').$usu_cat);
												$pdf->Ln(10);
												$pdf->Write(5,'Para cumplir el trabajo de : '.$oe_trab/*$consulta*/);
												$pdf->Ln(15);
												$pdf->Write(5,utf8_decode('Según lo siguiente : '));
												$pdf->Ln(10);
												$DetalleMiOrden = "SELECT DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN,OTE_DIA,OTE_TIPO,OTE_ESTA FROM OTE_DETALLE WHERE (OE_ID = $oe_id) AND OTE_ESTA = 'ACTIVO' ORDER BY OTE_DIA ASC, OTE_TIPO";
												$RespMiOrden = mysqli_query($cnn,$DetalleMiOrden);
												$RespMiOrden2 = mysqli_query($cnn,$DetalleMiOrden);
												$numreg = mysqli_num_rows($RespMiOrden);
												$contador = 0;
												$prueba = "";
												$hora_diurna                = 0;
												$min_diurna                 = 0;
												$seg_diurna                 = 0;
												$hora_nocturna              = 0;
												$min_nocturna               = 0;
												$seg_nocturna               = 0;
												$hora_diurna_cancelada      = 0;
												$min_diurna_cancelada       = 0;
												$seg_diurna_cancelada       = 0;
												$hora_nocturna_cancelada    = 0;
												$min_nocturna_cancelada     = 0;
												$seg_nocturna_cancelada     = 0;
												while ($row_c = mysqli_fetch_array($RespMiOrden2, MYSQLI_NUM)){
													if($row_c[5] == "ACTIVO"){
														if($row_c[4] == "COMPENSADAS"){
															if (date('w',strtotime($row_c[3])) == 0){
																//DIA DOMINGO
																$HorasDomingo = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																list($horaDom, $minDom, $segDom) = split('[:]', $HorasDomingo);
																$hora_nocturna = $hora_nocturna + $horaDom;
																$min_nocturna  = $min_nocturna + $minDom;
																$seg_nocturna  = $seg_nocturna + $segDom;
															}else{
																//NO ES DOMINGO
																if (date('w',strtotime($row_c[3])) == 6){
																 //DIA SABADO
																	$HorasSabado = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																	list($horaSab, $minSab, $segSab) = split('[:]', $HorasSabado);
																	$hora_nocturna = $hora_nocturna + $horaSab;
																	$min_nocturna  = $min_nocturna + $minSab;
																	$seg_nocturna  = $seg_nocturna + $segSab;
																}else{
																	//NO ES SABADO - REVISAR SI ES VERIADO
																	$ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$row_c[3]."')";
																	$RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
																	if (mysqli_num_rows($RespuestaFeriado) == 0){
																		//NO ES FERIADO - REVISAR SI ES ANTES O DESPUES DE LAS 21 HORAS
																		if ($row_c[1] < "21:00:00"){
																			if ($row_c[2] > "21:00:00"){
																				$HorasDiurnas = date("H:i:s",strtotime("00:00:00")+strtotime("21:00:00")-strtotime($row_c[1]));
																				$HorasNocturnas = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime("21:00:00"));
																				list($horaDiurno, $minDiurno, $segDiurno) = split('[:]', $HorasDiurnas);
																				$hora_diurna = $hora_diurna + $horaDiurno;
																				$min_diurna  = $min_diurna + $minDiurno;
																				$seg_diurna  = $seg_diurna + $segDiurno;
																				list($horaNoc, $minNoc, $segNoc) = split('[:]', $HorasNocturnas);
																				$hora_nocturna = $hora_nocturna + $horaNoc;
																				$min_nocturna  = $min_nocturna + $minNoc;
																				$seg_nocturna  = $seg_nocturna + $segNoc;
																			}else{
																				$HorasNormal = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																				list($horaNormal, $minNormal, $segNormal) = split('[:]', $HorasNormal);
																				$hora_diurna = $hora_diurna + $horaNormal;
																				$min_diurna  = $min_diurna + $minNormal;
																				$seg_diurna  = $seg_diurna + $segNormal;
																			}
																		}else{
																			$HorasDia = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																			list($horaNocturna, $minNocturna, $segNocturna) = split('[:]', $HorasDia);
																			$hora_nocturna = $hora_nocturna + $horaNocturna;
																			$min_nocturna  = $min_nocturna + $minNocturna;
																			$seg_nocturna  = $seg_nocturna + $segNocturna;
																		}
																	}else{
																		//DIA FERIADO
																		$HorasExtras = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																		list($horaFer, $minFer, $segFer) = split('[:]', $HorasExtras);
																		$hora_nocturna = $hora_nocturna + $horaFer;
																		$min_nocturna  = $min_nocturna + $minFer;
																		$seg_nocturna  = $seg_nocturna + $segFer;
																	}
																}
															}
														}else{
															if (date('w',strtotime($row_c[3])) == 0){
																//DIA DOMINGO
																$HorasDomingo = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																list($horaDomCa, $minDomCa, $segDomCa) = split('[:]', $HorasDomingo);
																$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaDomCa;
																$min_nocturna_cancelada = $min_nocturna_cancelada + $minDomCa;
																$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segDomCa; 
															}else{
																//NO ES DOMINGO
																if (date('w',strtotime($row_c[3])) == 6){
																	//DIA SABADO
																	$HorasSabado = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																	list($horaSabCa, $minSabCa, $segSabCa) = split('[:]', $HorasSabado);
																	$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaSabCa;
																	$min_nocturna_cancelada = $min_nocturna_cancelada + $minSabCa;
																	$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segSabCa; 
																}else{
																	//NO ES SABADO - REVISAR SI ES VERIADO
																	$ConsultaFeriado = "SELECT FN_FEC FROM FER_NACIONALES WHERE (FN_FEC =  '".$row_c[3]."')";
																	$RespuestaFeriado = mysqli_query($cnn, $ConsultaFeriado);
																	if (mysqli_num_rows($RespuestaFeriado) == 0){
																		//NO ES FERIADO - REVISAR SI ES ANTES O DESPUES DE LAS 21 HORAS
																		if ($row_c[1] < "21:00:00"){
																			if ($row_c[2] > "21:00:00"){
																				$HorasDiurnas = date("H:i:s",strtotime("00:00:00")+strtotime("21:00:00")-strtotime($row_c[1]));
																				$HorasNocturnas = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime("21:00:00"));
																				list($horaDiurnoCa, $minDiurnoCa, $segDiurnoCa) = split('[:]', $HorasDiurnas);
																				$hora_diurna_cancelada = $hora_diurna_cancelada + $horaDiurnoCa;
																				$min_diurna_cancelada = $min_diurna_cancelada + $minDiurnoCa;
																				$seg_diurna_cancelada = $seg_diurna_cancelada + $segDiurnoCa;
																				list($horaNocCa, $minNocCa, $segNocCa) = split('[:]', $HorasNocturnas);
																				$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaNocCa;
																				$min_nocturna_cancelada = $min_nocturna_cancelada + $minNocCa;
																				$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segNocCa; 
																			}else{
																				$HorasNormal = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																				list($horaNormalCa, $minNormalCa, $segNormalCa) = split('[:]', $HorasNormal);
																				$hora_diurna_cancelada = $hora_diurna_cancelada + $horaNormalCa;
																				$min_diurna_cancelada = $min_diurna_cancelada + $minNormalCa;
																				$seg_diurna_cancelada = $seg_diurna_cancelada + $segNormalCa;
																			}
																		}else{
																			$HorasDia = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																			list($horaDiaCa, $minDiaCa, $segDiaCa) = split('[:]', $HorasDia);
																			$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaDiaCa;
																			$min_nocturna_cancelada = $min_nocturna_cancelada + $minDiaCa;
																			$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segDiaCa; 
																		}
																	}else{
																		//DIA FERIADO
																		$HorasExtras = date("H:i:s",strtotime("00:00:00")+strtotime($row_c[2])-strtotime($row_c[1]));
																		list($horaFerCa, $minFerCa, $segFerCa) = split('[:]', $HorasExtras);
																		$hora_nocturna_cancelada = $hora_nocturna_cancelada + $horaFerCa;
																		$min_nocturna_cancelada = $min_nocturna_cancelada + $minFerCa;
																		$seg_nocturna_cancelada = $seg_nocturna_cancelada + $segFerCa; 
																	}
																}
															}
														}
													}
												}
												while ($row = mysqli_fetch_array($RespMiOrden, MYSQLI_NUM)){
													
													if($row[5] == "ACTIVO"){
														$contador = $contador + 1;
														if ($contador<= 10){
															$pdf->SetX(20);
															$pdf->Write(5,'DIA : '.$row[0].'       HORA INICIO : '.$row[1].'      HORA TERMINO : '.$row[2].'         '.$row[4]);
															$pdf->Ln(7);
														}
														if($contador == 10 || $contador == $numreg){
															if ($prueba != "IMPRESO"){
																$pdf->Ln(10);
																//dar formato a hora compensado
																//hora diurna
																if($min_diurna >= 60){
																	$hora_diruna_resultado = $min_diurna / 60;
																	$min_diurna_resultado = $min_diurna % 60;
																	if($min_diurna_resultado < 10){
																		$min_diurna_resultado = "0".$min_diurna_resultado;
																	}
																	$hora_diurna = (int)$hora_diruna_resultado + $hora_diurna;
																	$hora_diurna_compensada = $hora_diurna.":".$min_diurna_resultado.":00";
																}else{
																	$hora_diurna_compensada = $hora_diurna.":".$min_diurna.":00";          
																}
																//hora nocturna
																if($min_nocturna >= 60){
																	$hora_nocturna_resultado = $min_nocturna / 60;
																	$min_nocturna_resultado = $min_nocturna % 60;
																	if($min_nocturna_resultado < 10){
																		$min_nocturna_resultado = "0".$min_nocturna_resultado;
																	}
																	$hora_nocturna = (int)$hora_nocturna_resultado + $hora_nocturna;
																	$hora_nocturna_compensada = $hora_nocturna.":".$min_nocturna_resultado.":00";
																}else{
																	$hora_nocturna_compensada = $hora_nocturna.":".$min_nocturna.":00";
																}
																//dar formato a hora cancelada
																//hora diurna
																if($min_diurna_cancelada >= 60){
																	$hora_diurna_cancelada_resultado = $min_diurna_cancelada / 60;
																	$min_diurna_cancelada_resultado = $min_diurna_cancelada % 60;
																	if($min_diurna_cancelada_resultado < 10){
																		$min_diurna_cancelada_resultado = "0".$min_diurna_cancelada_resultado;
																	}
																	$hora_diurna_cancelada = (int)$hora_diurna_cancelada_resultado + $hora_diurna_cancelada;
																	$hora_diurna_cancelada_final = $hora_diurna_cancelada.":".$min_diurna_cancelada_resultado.":00";
																}else{
																	$hora_diurna_cancelada_final = $hora_diurna_cancelada.":".$min_diurna_cancelada.":00";
																}
																//hora nocturna
																if($min_nocturna_cancelada >= 60){
																	$hora_nocturna_cancelada_resultado = $min_nocturna_cancelada / 60;
																	$min_nocturna_cancelada_resultado = $min_nocturna_cancelada % 60;
																	if($min_nocturna_cancelada_resultado < 10){
																		$min_nocturna_cancelada_resultado = "0".$min_nocturna_cancelada_resultado;
																	}
																	$hora_nocturna_cancelada = (int)$hora_nocturna_cancelada_resultado + $hora_nocturna_cancelada;
																	$hora_nocturna_cancelada_final = $hora_nocturna_cancelada.":".$min_nocturna_cancelada_resultado.":00";
																}else{
																	$hora_nocturna_cancelada_final = $hora_nocturna_cancelada.":".$min_nocturna_cancelada.":00";
																}
																$hora_diurna_total = $hora_diurna*1.25;
																$hora_diurna_total = (int)$hora_diurna_total;
																$pdf->SetX(30);
																$pdf->Write(5,'HORAS COMPENSADAS                                              HORAS CANCELADAS');
																$pdf->Ln();
																$pdf->SetX(10);
																$pdf->Write(5,'Horas diurnas      : '.$hora_diurna_compensada.' al 1.25 - TOTAL : '.$hora_diurna_total);
																$pdf->SetX(105);
																$pdf->Write(5,'Horas diurnas :                                        '.$hora_diurna_cancelada);
																$pdf->Ln();
																$hora_nocturna_total = $hora_nocturna*1.5;
																$hora_nocturna_total = (int)$hora_nocturna_total;
																$pdf->SetX(10);
																$pdf->Write(5,'Horas nocturnas  : '.$hora_nocturna_compensada.' al 1.50 - TOTAL : '.$hora_nocturna_total);
																$pdf->SetX(105);
																$pdf->Write(5,'Horas nocturnas :                                    '.$hora_nocturna_cancelada);
																$pdf->Ln();
																$pdf->SetX(42);
																$TotalHoras = $hora_diurna_total + $hora_nocturna_total;
																$TotalHoras = (int)$TotalHoras;
																$TotalHorasCance = $hora_diurna_cancelada + $hora_nocturna_cancelada;
																$pdf->Write(5,'TOTAL COMPENSADAS : '.$TotalHoras);
																$pdf->SetX(130);
																$pdf->Write(5,'TOTAL CANCELADAS : '.$TotalHorasCance);
																$pdf->SetY(220);
																$pdf->SetX(37);
																$pdf->Write(5,utf8_decode('INTERESADO'));
																$pdf->Line(20,220,80,220);
																$pdf->SetX(136);
																$pdf->Write(5,utf8_decode('JEFE DIRECTO'));
																$pdf->Line(120,220,180,220);
																$pdf->Ln(20);
																$pdf->SetX(110);
																$pdf->Write(5,'CANCELADOS : '.$oe_cant_cance);
																$pdf->Ln();
																$pdf->SetX(110);
																$pdf->Write(5,'DESCANSO COMPLEMENTARIO : '.$oe_cant_dc);
																$pdf->Ln();
																$pdf->PageNo(1);
																$pdf->Line(20,299,70,299);
																$pdf->Line(80,299,130,299);
																$pdf->Line(140,299,190,299);
																$pdf->SetY(300);
																$pdf->SetX(35);
																$pdf->Write(5,utf8_decode('DIRECTOR'));
																$pdf->SetX(83);
																$pdf->Write(5,utf8_decode('V° B° JEFE DPTO. SALUD'));
																$pdf->SetX(143);
																$pdf->Write(5,utf8_decode('SECRETARIA MUNICIPAL'));
																$prueba = "IMPRESO";
															}
														}
														if($contador==11){
															$pdf->SetY(155);
															$pdf->SetX(130);
															$pdf->SetFontSize(6);
															$pdf->Write(5,utf8_decode('Detalle continua en la siguiente hoja'));
															//agregar nueva pagina
															$pdf->AddPage('P',array(215.9,330.2));
															$pdf->SetFontSize(12);
															$pdf->SetY(30);
															$pdf->SetX(0);
															$pdf->SetFont('GothamBold');
															$pdf->Cell(210,5,'CONTINUACION DETALLE DE ORDEN DE TRABAJO EXTRAORDINARIOS',0,1,'C');
															$pdf->SetY(50);
															$pdf->SetFont('GothamBook');
															$pdf->SetFontSize(10);
														}
														if($contador>=11){
															//registros en la pagina nueva
															$pdf->SetX(30);
															$pdf->Write(5,'DIA : '.$row[0].'       HORA INICIO : '.$row[1].'      HORA TERMINO : '.$row[2].'         '.$row[4]);
															$pdf->Ln(7);
														}
													}
												}
										}		
					}
				}	
			$pdf->Output();
    }
?>
