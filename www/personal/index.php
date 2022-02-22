<?php 
	session_start(); 
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
	if(!isset($_SESSION['USU_RUT'])){
		session_destroy();
		header("location: ../index.php");
	}else{
		$Srut = utf8_encode($_SESSION['USU_RUT']);
		$Snombre = utf8_encode($_SESSION['USU_NOM']);
		$SapellidoP = utf8_encode($_SESSION['USU_APP']);
		$SapellidoM = utf8_encode($_SESSION['USU_APM']);
		$Semail = utf8_encode($_SESSION['USU_MAIL']);
		$Scargo = utf8_encode($_SESSION['USU_CAR']);
        $Sjefatura = utf8_encode($_SESSION['USU_JEF']);
		$Sestablecimiento = $_SESSION['EST_ID'];
		$Sdependencia = $_SESSION['USU_DEP'];
    $Sdependencia2 = $_SESSION['USU_DEP2'];
		$actualizacion = $_SESSION['ACTUALIZACIONES'];
		include ("../include/funciones/funciones.php");
		$cnn = ConectarPersonal();
    include ("../include/funciones/funciones2.php");
    $enc = ConectarEncuestas();
		date_default_timezone_set("America/Santiago");
		$fecha = date("Y-m-d");
		$hora = date("H:i:s");
		$accion = utf8_decode("INGRESO A INDEX");
		$ipcliente = getRealIP();
		$insertAcceso = "INSERT INTO LOG_ACCION (LA_ACC,FOR_ID,USU_RUT,LA_IP_USU,LA_FEC,LA_HORA) VALUES ('$accion','0','$Srut', '$ipcliente', '$fecha', '$hora')";
		mysqli_query($cnn, $insertAcceso);
    if($Srut == "11.277.235-9" || $Srut=="15.738.663-8"){
      //$Srut == "15.738.663-8" || 
      $sn =1;
    }else{
      $sn=0;
    }
	}
?>
<html>
    <head>
        <title>Personal Salud</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link type="text/css" rel="stylesheet" href="../include/css/icon.css" />
        <link type="text/css" rel="stylesheet" href="../include/css/materialize.min.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="../include/css/custom.css" />
        <link href="../include/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <style type="text/css">
            body{
                background-image: url("../include/img/fondopersonal.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }
						.mi_informacion{
								font-size: 18px;
								font-weight: bold;
						}
        </style>
        <script type="text/javascript" src="../include/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="../include/js/materialize.js"></script>
        <script>
            $(document).ready(function () {
              $('.sidenav').sidenav();
              $(".dropdown-trigger").dropdown();
              $('.modal').modal();
              $('.tabs').tabs();
              $('select').formSelect();
            });
            function VerSP(id){
                var idSP = id;
                $.post( "php/ver_histopermiso.php", { "id" : idSP}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function AceptarSP(id,doc_id){
                var idSP = id;
                var idDoc = doc_id;
                window.location = "formularios/aceptar.php?id="+idSP+"&docid="+idDoc;
            }
            function AceptarSPDIR(id){
                var idSP = id;
                $.post( "php/aceptar_solpermi.php", { "id" : idSP }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "index.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                        window.location = "index.php";
                    }
                });
            }
            function RechazarSP(id){
                var idSP = id;
                window.location = "formularios/cancelarsp.php?id="+idSP;
            }
            function ImprimirSP(id){
                var idSP = id;
                window.open('http://200.68.34.158/personal/pdf/sol_permi.php?id='+idSP,'_blank');
            }
            function CancelarSP(id){
								var btn = "#imprimirMiSP"+id;
								$(btn).attr("disabled","disabled");
                var idSP = id;
                $.post( "php/cancelar_mi_sp.php", { "id" : idSP }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "index.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                        window.location = "index.php";
                    }
                });
            }
            function Iraorden(id){
                window.location = "formularios/for_ot_extra.php";
            }
            function ImprimirOT(id){
                var idOT = id;
                window.open('http://200.68.34.158/personal/pdf/ot_extra.php?id='+idOT,'_blank');
            }
            function CancelarOT(id){
								var btn = "#cancelarMiOT"+id;
								$(btn).attr("disabled","disabled");
                var idOT = id;
                $.post( "php/cancelar_mi_ot.php", { "id" : idOT }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "index.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                        window.location = "index.php";
                    }
                });
            }
            function VerOT(id){
                var idOT = id;
                $.post( "php/verOT_histopermiso.php", { "id" : idOT}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function AceptarOT(id,doc_id){
                var idOT = id;
                var idDoc = doc_id;
                window.location = "formularios/aceptar.php?id="+idOT+"&docid="+idDoc;
            }
            function VistoBuenoOT(id){
                var idOT = id;
                $.post( "php/aceptar_otextra.php", { "id" : idOT }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "index.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                        window.location = "index.php";
                    }
                });
            }
            function RechazarOT(id,a){
                // RECHAZAR OT QUIEN -> 1 : JD - 2 : DIR - 3 : DIR DPTO
                var idOT = id;
                var quien = a;
                window.location = "formularios/cancelarot.php?id="+idOT+"&quien="+a;
            }
            function AceptaRSP(id){
								var btn = "acepta"+id;
								$(btn).attr("disabled","disabled");
                var idRSP = id;
                var rsp_acc = "SOLICITA NUEVO FERIADO";
                $.post( "php/respuesta_sp.php", { "id" : idRSP, "acc" : rsp_acc }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "formularios/for_sol_permi_res.php?id="+idRSP;
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function AcumulaRSP(id){
								var btn = "acumula"+id;
								$(btn).attr("disabled","disabled");
                var idRSP = id;
                var rsp_acc = "ACUMULA";
                $.post( "php/respuesta_sp.php", { "id" : idRSP, "acc" : rsp_acc }, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "formularios/for_acu_fer.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function VistoBuenoSAF(id){
                var idSAF = id;
                var idDoc = 6;
                window.location = "formularios/aceptar_acu.php?id="+idSAF+"&docid="+idDoc;
            }
            function AceptarSAF(id){
                var idSAF = id;
                $.post( "php/aceptar_saf.php", { "id" : idSAF}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "index.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function ImprimirSAF(id){
                var idSAF = id;
                window.open('http://200.68.34.158/personal/pdf/saf.php?id='+idSAF,'_blank');
            }
            function CancelarSAF(id){
								var btn = "#cancelarMiSAF"+id;
								$(btn).attr("disabled","disabled");
                var idSAF = id;
                $.post( "php/cancelar_mi_saf.php", { "id" : idSAF}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "index.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function ImprimirSPR(id){
                var idSPR = id;
                window.open('http://200.68.34.158/personal/pdf/sin_goce.php?id='+idSPR,'_blank');
            }
            function CancelarSPR(id){
                var idSPR = id;
                $.post( "php/cancelar_mi_spr.php", { "id" : idSPR}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "index.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function VerSPR(id){
                var idSPR = id;
                $.post( "php/verSPR_histopermiso.php", { "id" : idSPR}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
            function AceptarSPR(id){
                var idSPR = id;
                var idDoc = 4;
                window.location = "formularios/aceptar_dir_psgr.php?id="+idSPR+"&docid="+idDoc;
            }
						function AutorizarSPR(id){
                var idSPR = id;
                $.post( "php/aceptar_spr.php", { "id" : idSPR}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
												window.location = "index.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
						}
            function RechazarSPR(id){
                var idSPR = id;
                window.location = "formularios/cancelarspr.php?id="+idSPR;
            }
						function RechazarDIRSPR(id){
                var idSPR = id;
                window.location = "formularios/cancelar_dir_spr.php?id="+idSPR;
            }
            function IraCome(id){
                window.location = "formularios/for_cometido.php";
            }
            function ImprimirCO(id){
                var idCO = id;
                window.open('http://200.68.34.158/personal/pdf/cometido.php?id='+idCO,'_blank');
            }
            function CancelarCO(id){
								var btn = "#cancelarMiSPR"+id;
								$(btn).attr("disabled","disabled");
                var idCO = id;
                $.post( "php/cancelar_mi_come.php", { "id" : idCO}, null, "json" )
                .done(function( data, textStatus, jqXHR ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud se ha completado correctamente." );
                        window.location = "index.php";
                    }
                })
                .fail(function( jqXHR, textStatus, errorThrown ) {
                    if ( console && console.log ) {
                        console.log( "La solicitud a fallado: " +  textStatus);
                    }
                });
            }
						function VerSCO(id){
								var idSCO = id;
								$.post( "php/verSCO_histopermiso.php", { "id" : idSCO}, null, "json" )
								.done(function( data, textStatus, jqXHR ) {
										if ( console && console.log ) {
												console.log( "La solicitud se ha completado correctamente." );
										}
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {
										if ( console && console.log ) {
												console.log( "La solicitud a fallado: " +  textStatus);
										}
								});
						}
						function AceptarSCO(id){
								var idSCO = id;
								var idDoc = 8;
								window.location = "formularios/aceptar_jd_come.php?id="+idSCO+"&docid="+idDoc;
						}
						function RechazarSCO(id){
								var idSCO = id;
								$.post( "php/rechazar_cometido.php", { "id" : idSCO}, null, "json" )
								.done(function( data, textStatus, jqXHR ) {
										if ( console && console.log ) {
												console.log( "La solicitud se ha completado correctamente." );
												window.location = "index.php";
										}
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {
										if ( console && console.log ) {
												console.log( "La solicitud a fallado: " +  textStatus);
										}
								});
						}
						function VerSCOD(id){
								var idSCO = id;
								$.post( "php/verSCO_histopermiso.php", { "id" : idSCO}, null, "json" )
								.done(function( data, textStatus, jqXHR ) {
										if ( console && console.log ) {
												console.log( "La solicitud se ha completado correctamente." );
										}
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {
										if ( console && console.log ) {
												console.log( "La solicitud a fallado: " +  textStatus);
										}
								});
						}
						function AceptarSCOD(id){
								var idSCO = id;
								$.post( "php/aceptar_cometido.php", { "id" : idSCO}, null, "json" )
								.done(function( data, textStatus, jqXHR ) {
										if ( console && console.log ) {
												console.log( "La solicitud se ha completado correctamente." );
												window.location = "index.php";
										}
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {
										if ( console && console.log ) {
												console.log( "La solicitud a fallado: " +  textStatus);
										}
								});
						}
						function RechazarSCOD(id){
								var idSCO = id;
								$.post( "php/rechazar_cometido_dir.php", { "id" : idSCO}, null, "json" )
								.done(function( data, textStatus, jqXHR ) {
										if ( console && console.log ) {
												console.log( "La solicitud se ha completado correctamente." );
												window.location = "index.php";
										}
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {
										if ( console && console.log ) {
												console.log( "La solicitud a fallado: " +  textStatus);
										}
								});
						}
						function CancelarMOT(id){
							var idMOT = id;
							$.post( "php/cancelar_mot.php", { "id" : idMOT}, null, "json" )
								.done(function( data, textStatus, jqXHR ) {
										if ( console && console.log ) {
												console.log( "La solicitud se ha completado correctamente." );
												window.location = "index.php";
										}
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {
										if ( console && console.log ) {
												console.log( "La solicitud a fallado: " +  textStatus);
										}
								});
						}
						function VerMOT(id){
							var idMOT = id;
							$.post( "php/ver_mot.php", { "id" : idMOT}, null, "json" )
								.done(function( data, textStatus, jqXHR ) {
										if ( console && console.log ) {
												console.log( "La solicitud se ha completado correctamente." );
										}
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {
										if ( console && console.log ) {
												console.log( "La solicitud a fallado: " +  textStatus);
										}
								});
						}
						function AceptarMOT(id){
							var idMOT = id;
							$.post( "php/aceptar_mot.php", { "id" : idMOT}, null, "json" )
								.done(function( data, textStatus, jqXHR ) {
										if ( console && console.log ) {
												console.log( "La solicitud se ha completado correctamente." );
												window.location = "index.php";
										}
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {
										if ( console && console.log ) {
												console.log( "La solicitud a fallado: " +  textStatus);
										}
								});
						}
						function RechazarMOT(id){
							var idMOT = id;
							$.post( "php/rechazar_mot.php", { "id" : idMOT}, null, "json" )
								.done(function( data, textStatus, jqXHR ) {
										if ( console && console.log ) {
												console.log( "La solicitud se ha completado correctamente." );
												window.location = "index.php";
										}
								})
								.fail(function( jqXHR, textStatus, errorThrown ) {
										if ( console && console.log ) {
												console.log( "La solicitud a fallado: " +  textStatus);
										}
								});
						}
						function Actualizacion(){
							var act = $("#act").val();
							if(act == "SI"){
								var value = "NO";
								$.post( "php/actualizacion.php", { "value" : value}, null, "json" );
								$('#ACTUALIZACIONES').modal('open');
							}
						}
						function Adjunto(doc,id){
							var doc_id = doc;
							var folio = id;
							var ruta = "adjunto.php?doc="+doc_id+"&folio="+folio;
              var ruta1 = "adjunto1.php?doc="+doc_id+"&folio="+folio;
							window.open(ruta1);
              window.open(ruta);
						}
            function ImprimirCC(){
              var idcc = "<?php echo $Srut;?>";
              window.open('http://200.68.34.158/personal/pdf/certificado_capa.php?id='+idcc,'_blank');               
            }
            function ImprimirCE(){
              var idcc = "<?php echo $Srut;?>";
              window.open('http://200.68.34.158/personal/pdf/certificado_expe.php?id='+idcc,'_blank');
              //window.open('http://200.68.34.158/personal/pdf/ftb.php?id='+idcc,'_blank');
            }
            function SubNivel(){
              var act = $("#act").val();
              var idsn = "<?php echo $sn;?>";
              if(act == "SI"){
                if(idsn == 1){
                window.open('http://200.68.34.158/personal/php/carrera/calcula_sn.php','_blank',"width=650,height=450");
                }
              }    
            }
            function CambioDependencia(){
              var value_dependencia = $("#dependecia").val();
              $.post( "php/cambio_dependencia.php", { "nombre" : value_dependencia}, null, "json" )
              .done(function( data, textStatus, jqXHR ) {
                  if ( console && console.log ) {
                      console.log( "La solicitud se ha completado correctamente." );
                      window.location = "index.php";
                  }
              })
              .fail(function( jqXHR, textStatus, errorThrown ) {
                  if ( console && console.log ) {
                      console.log( "La solicitud a fallado: " +  textStatus);
                  }
              });
            }
        </script>
    </head>
    <body onload="Actualizacion();SubNivel();">
				<!-- llamo el nav que tengo almacenado en un archivo -->
        <?php require_once('estructura/nav_personal.php');?>
        <!-- mostrar hora en php -->
				<?php
        if($Sdependencia2 == "MULTIESTABLECIMIENTO"){
          echo '<div class="input-field col s12">';
              echo '<select name="dependecia" id="dependecia" onchange="CambioDependencia();">';
                $Dependencia="SELECT EST_NOM FROM ESTABLECIMIENTO WHERE (EST_ESTA = 'ACTIVO')";
                $resultadoD =mysqli_query($cnn, $Dependencia);
                  while($regD=mysqli_fetch_array($resultadoD)){
                    printf("<option value=\"$regD[0]\">$regD[0]</option>");
                  }
                echo "<option value='no' disabled selected>$Sdependencia</option>";
              echo '</select>';
          echo '</div>';
        }
				echo '<input type="text" id="act" name="act" class="validate" style="display: none" value="'.$actualizacion.'">';
				echo '<div id="ACTUALIZACIONES" class="modal">';
						echo '<div class="modal-content">';
								echo '<h4>Detalle de Documento</h4>';
								$query_act = "SELECT VE_VERSION,DATE_FORMAT(VE_FEC,'%d-%m-%Y'),VE_HORA,VE_DESCRI FROM VERSION WHERE ESTADO ='S' ORDER BY VE_FEC DESC LIMIT 3 ";
								$RespACT = mysqli_query($cnn,$query_act);
								while ($row_ACT = mysqli_fetch_array($RespACT, MYSQLI_NUM)){
									echo '<p><b>ACTUALIZACION SISTEMA</b> - FECHA : '.$row_ACT[1].' - HORA : '.$row_ACT[2].'</p>';	
									echo '<p><b>SIPER VERSION N° '.$row_ACT[0].'</p>';	
									echo '<p><b>DESCRIPCION : '.utf8_encode($row_ACT[3]).'</p>';
								}
						echo '</div>';
						echo '<div class="modal-footer">';
								echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
						echo '</div>';
				echo '</div>';
				?>
        </br>
        </br>
        </br>
      <div class="row">
        <div class="col s6">
          <?php 
              $limite = "2021-11-26";
              if($fecha <= $limite){      
                // ver si ya respondio
                $query_encuesta =  "SELECT RUT FROM HC2021 WHERE RUT = '$Srut' AND ESTADO_2 = 'PENDIENTE'";
                // echo $query_encuesta;
                $resp_encuesta = mysqli_query($enc, $query_encuesta);
                $encuensta_ok = mysqli_num_rows($resp_encuesta);
                if(($encuensta_ok != 0)){ // || $Srut == '15.738.663-8'  || ($Srut == '17.333.639-K')
                    ?><a href="encuesta2.php" target="_self"><img class="responsive-img" src="../include/img/index_principal_11.png" style="position: absolute; top: 11%; right: 2%; z-index: 100;opacity: 0.9;"></a><?php
                }
                // else{
                    ?><!--<a href="encuesta.php" target="_top"><img class="responsive-img" src="../include/img/index_principal_4.png" style="position: absolute; top: 25%; right: 2%; z-index: 100;opacity: 0.9;"></a>--><?php
                // }
              } 
          ?>
          <!--<a href="encuesta.php" target="_top"><img class="responsive-img" src="../include/img/index_principal_4.png" style="position: absolute; top: 25%; right: 2%; z-index: 100;opacity: 0.9;"></a>
          <a href="https://docs.google.com/forms/d/e/1FAIpQLSczVnzrktWsohdLCItdJ3tONPvcgnoynHpcuA5Jd6WyscFsNg/viewform?vc=0&c=0&w=1" target="_self"><img class="responsive-img" src="../include/img/index_principal_3.png" style="position: absolute; top: 25%; right: 2%; z-index: 100;opacity: 0.9;"></a> -->
        </div>
      </div>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center block" style="background-color: #ffffff">
                        <div class="row">
                            <div class="col s12">
                              <?php
                              list($ano_actual, $mes_actual, $dia_actual) = split('[-]', $fecha);
        											$FecIni = ($ano_actual - 2)."-".$mes_actual."-".$dia_actual;
                              $licencias = "SELECT LM_FEC_INI,LM_FEC_FIN,LM_DIAS,LM_NUM,LM_TE,LM_TR,LM_ESTA,LM_TIPO,DATE_FORMAT(LM_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(LM_FEC_FIN,'%d-%m-%Y') FROM LICENCIAS_MEDICAS WHERE (USU_RUT = '$Srut') AND (LM_FEC_FIN >= '$FecIni') AND ((LM_TIPO NOT LIKE '%Enfermedad Grave Hijo Menor%') AND (LM_TIPO != 'Prenatal - Postnatal') AND (LM_TIPO NOT LIKE '%Del Embarazo%')) ORDER BY LM_FEC_INI ASC";
                              $respuesta = mysqli_query($cnn, $licencias);
                              $TotalDias = 0;
                              if (mysqli_num_rows($respuesta) == 0){
                                $TotalDias = 0;
                              }elseif(mysqli_num_rows($respuesta) == 1){
                                $rowlm = mysqli_fetch_row($respuesta);
                                $TotalDias = $rowlm[2];
                              }elseif(mysqli_num_rows($respuesta) > 1){
                                while ($rowlm = mysqli_fetch_array($respuesta)){
                                  $fec_ini = $rowlm[0];
                                  $fec_fin = $rowlm[1];
                                  $dias = $rowlm[2];
                                  if($fec_ini < $FecIni && $fec_fin > $FecIni){
                                    $date1 = new DateTime($FecIni);
                                    $date2 = new DateTime($fec_fin);
                                    $diff = $date1->diff($date2);
                                    $diferencia = $diff->days;
                                    $TotalDias = $TotalDias + $diferencia;
                                  }elseif($fec_ini >= $FecIni && $fec_fin <= $fecha){
                                    $TotalDias = $TotalDias + $dias;                                   
                                  }elseif($fec_fin > $fecha){
                                    $date1 = new DateTime($fec_ini);
                                    $date2 = new DateTime($fecha);
                                    $diff = $date1->diff($date2);
                                    $diferencia = $diff->days;
                                    $TotalDias = $TotalDias + $diferencia;
                                  }
                                }
                              }
                              $pendientes = "SELECT LM_ID FROM LICENCIAS_MEDICAS WHERE (USU_RUT = '$Srut') AND (LM_ESTA = 'PAGO PENDIENTE')";
                              $cantidad = mysqli_num_rows(mysqli_query($cnn, $pendientes));
                              ?>
                              <div class="col s3"></div>
                              <div class="col s6">Dias Licencias ultimos 2 años: <?php echo "  ".$TotalDias; ?></div>
                              <div class="col s3">Licencias pendientes de pago: <?php echo "  ".$cantidad; ?></div>
                              <div class="col s3"></div>
                            </div>
                            <?php
                            if($Srut == "15.922.085-0"){//
                              $usuario = "SELECT USU_RUT,USU_NOM,USU_APM,USU_APP FROM USUARIO ORDER BY EST_ID ASC";
                              $rs = mysqli_query($cnn, $usuario);
                              list($ano_actual, $mes_actual, $dia_actual) = split('[-]', $fecha);
        											$FecIni = ($ano_actual - 2)."-".$mes_actual."-".$dia_actual;
                              while ($row = mysqli_fetch_array($rs)){
                                  $nombreCompleto = utf8_encode($row[1])." ".utf8_encode($row[2])." ".utf8_encode($row[3]); 
                                  $licencias = "SELECT LM_FEC_INI,LM_FEC_FIN,LM_DIAS,LM_NUM,LM_TE,LM_TR,LM_ESTA,LM_TIPO,DATE_FORMAT(LM_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(LM_FEC_FIN,'%d-%m-%Y') FROM LICENCIAS_MEDICAS WHERE (USU_RUT = '$row[0]') AND (LM_FEC_FIN >= '$FecIni') AND ((LM_TIPO NOT LIKE '%Enfermedad Grave Hijo Menor%') AND (LM_TIPO != 'Prenatal - Postnatal') AND (LM_TIPO NOT LIKE '%Del Embarazo%')) ORDER BY LM_FEC_INI ASC";
                                  $respuesta = mysqli_query($cnn, $licencias);
                                  $TotalDias = 0;
                                  if (mysqli_num_rows($respuesta) == 0){
                                    $TotalDias = 0;
                                  }elseif(mysqli_num_rows($respuesta) == 1){
                                    $rowlm = mysqli_fetch_row($respuesta);
                                    $TotalDias = $rowlm[2];
                                  }elseif(mysqli_num_rows($respuesta) > 1){
                                    while ($rowlm = mysqli_fetch_array($respuesta)){
                                      $fec_ini = $rowlm[0];
                                      $fec_fin = $rowlm[1];
                                      $dias = $rowlm[2];
                                      if($fec_ini < $FecIni && $fec_fin > $FecIni){
                                        $date1 = new DateTime($FecIni);
                                        $date2 = new DateTime($fec_fin);
                                        $diff = $date1->diff($date2);
                                        $diferencia = $diff->days;
                                        $TotalDias = $TotalDias + $diferencia;
                                      }elseif($fec_ini >= $FecIni && $fec_fin <= $fecha){
                                        $TotalDias = $TotalDias + $dias;                                   
                                      }elseif($fec_fin > $fecha){
                                        $date1 = new DateTime($fec_ini);
                                        $date2 = new DateTime($fecha);
                                        $diff = $date1->diff($date2);
                                        $diferencia = $diff->days;
                                        $TotalDias = $TotalDias + $diferencia;
                                      }
                                    }
                                  }
                                  if($TotalDias >= 100){
                                    echo '<div class="col s6">Funcionario: '.$nombreCompleto.' </div>';
                                    echo '<div class="col s6">Dias: '.$TotalDias.' </div>';
                                  }
                              }
                            }
                            ?>
                            <br><br>
                            <div class="col s12">
                                <ul class="tabs">
                                    <li class="tab col s4"><a href="#mensajes">AVISOS</a></li>
                                    <li class="tab col s4"><a class="active" href="#permisos">Documentos Pendientes</a></li>
                                    <li class="tab col s4"><a href="#yo">Mi Informacion</a></li>
                                </ul>
                            </div>
                            <div id="mensajes" class="col s12">
                                <!-- RESERVADO PARA LECTURA DE MENSAJES MASIVOSS -->
															<table class="responsive-table boradered striped">
                                        <thead>
                                            <tr>
                                                <th>FUNCIONARIO</th>
																								<th>TIPO</th>
																							  <th>JORNADA</th>
                                                <th>DESDE</th>
                                                <th>HASTA</th>
                                                <th></th>
                                            </tr>
                                            <tbody>
																							<?php
																								if($Srut =="11.277.235-9"){
                                                  $permisos ="SELECT USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,DOCUMENTO.DOC_NOM,SOL_PERMI.SP_JOR,SOL_PERMI.SP_FEC_INI,SOL_PERMI.SP_FEC_FIN 
																								  FROM SOL_PERMI INNER JOIN DOCUMENTO ON SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID INNER JOIN USUARIO ON SOL_PERMI.USU_RUT = USUARIO.USU_RUT 
																								  WHERE (SOL_PERMI.SP_FEC_FIN >= '$fecha') AND (SOL_PERMI.SP_ESTA='AUTORIZADO DIR')
																								  ORDER BY SOL_PERMI.SP_FEC_INI ASC";
                                                }else{
                                                  $permisos ="SELECT USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,DOCUMENTO.DOC_NOM,SOL_PERMI.SP_JOR,SOL_PERMI.SP_FEC_INI,SOL_PERMI.SP_FEC_FIN 
																								  FROM SOL_PERMI INNER JOIN DOCUMENTO ON SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID INNER JOIN USUARIO ON SOL_PERMI.USU_RUT = USUARIO.USU_RUT 
																								  WHERE (SOL_PERMI.SP_FEC_FIN >= '$fecha') AND ((USUARIO.EST_ID = '$Sestablecimiento') OR (USUARIO.EST_ID = '9999'))  AND (SOL_PERMI.SP_ESTA='AUTORIZADO DIR')
																								  ORDER BY SOL_PERMI.SP_FEC_INI ASC";
                                                }
																								
																								$respuesta1 = mysqli_query($cnn, $permisos);
																								while ($row_rs1 = mysqli_fetch_array($respuesta1, MYSQLI_NUM)){
																									echo "<tr>";
																										echo "<td>".utf8_encode($row_rs1[0])." ".utf8_encode($row_rs1[1])." ".utf8_encode($row_rs1[2])."</td>";
																										echo "<td>".utf8_encode($row_rs1[3])."</td>";
																										echo "<td>".utf8_encode($row_rs1[4])."</td>";
																										echo "<td>".$row_rs1[5]."</td>";
																									  echo "<td>".$row_rs1[6]."</td>";
																									echo "</tr>";
																								}
																								
																							
																							?>
																						</tbody>
																		</thead>
																</table>
																</br></br></br>
																<?php
																if($Scargo == "Director" || $Scargo == "Director (S)"){
																	echo "HORAS COMPENSADAS";
																	$query_nom = "SELECT U.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM FROM USUARIO U WHERE U.USU_DEP = (SELECT EST_NOM FROM ESTABLECIMIENTO WHERE EST_ID = $Sestablecimiento)";
																	$respuesta_nom = mysqli_query($cnn,$query_nom);
																	echo '<table class="responsive-table boradered striped">';
																		echo '<thead>';
																			echo '<tr>';
																				echo '<th>FUNCIONARIO</th>';
																				echo '<th> TOTAL HORAS</th>';
																			echo '</tr>';
																				echo '<tbody>';
																					while ($row_nom = mysqli_fetch_array($respuesta_nom, MYSQLI_NUM)){
																						echo "<tr>";
																							echo "<td>".utf8_encode($row_nom[1])." ".utf8_encode($row_nom[2])." ".utf8_encode($row_nom[3])."</td>";
																							list($ano_actual, $mes_actual, $dia_actual) = split('[/]', $fecha);
																							$FecIni = ($ano_actual - 2)."/".$mes_actual."/".$dia_actual;
																							$query_banco_hora = "SELECT BH_SALDO FROM BANCO_HORAS WHERE (USU_RUT = '$row_nom[0]') AND (BH_SALDO > 0) AND (BH_FEC BETWEEN '$FecIni' AND '$fecha') AND ((BH_TIPO = 'INICIAL') OR (BH_TIPO = 'INGRESO')) ORDER BY BH_FEC ASC";
																							$resultado_hora = mysqli_query($cnn, $query_banco_hora);
																							if (mysqli_num_rows($resultado_hora) != 0){
																									while ($row_hora = mysqli_fetch_array($resultado_hora)){
																											$muestro_horas  = $row_hora[0] + $muestro_horas;
																									}
																							}else{
																									$muestro_horas = 0;
																							}
																							echo "<td>".$muestro_horas."</td>";
																						echo "</tr>";
																						$muestro_horas = 0;
																					}
																				echo '</tbody>';
																		echo '</thead>';
																	echo '</table>';
																}
																?>
                            </div>
                            <div id="permisos" class="col s12">
                                <form method="post" name="form_permisos">
                                    <br><br><br>
                                    <!--ELIMINA LIQUIDACIONES -->
                                    <?php
                                      $fecha5 = date("Y");
                                      $mes=1;
                                      while($mes < 13){
                                        if($mes <=9){
                                          $mcuenta = "0".$mes;
                                        }else{
                                          $mcuenta = $mes;
                                        }          
                                        $dir5 = "../include/liquidacion_txt/".$fecha5."/".$mcuenta."/".$Srut.".txt";
                                        unlink($dir5);
                                        $mes = $mes+1;
                                      }
                                    ?>
                                    <!--primero sol_permi-->
                                    <?php
                                        $AñoActual = date("Y");
                                        $FecActual = date("d-m-Y");
                                        $AñoSiguiente = $AñoActual + 1;
                                    ?>
                                    <table class="responsive-table boradered striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>TIPO</th>
                                                <th>MOTIVO</th>
                                                <th>ESTADO</th>
                                                <th>ACCIONES</th>
                                                <th></th>
                                            </tr>
                                            <tbody>
                                                <!-- MOSTRAR MIS PERMISOS -->
                                                    <?php
                                                        list($dia_fin, $mes_fin, $año_fin) = split('[-]', $FecActual);
                                                        $fec_fin = date("Y-m-d");
                                                        $fec_ini = strtotime ( '-7 day' , strtotime ( $fec_fin ) ) ;
                                                        $fec_ini = date ( 'Y-m-d' , $fec_ini );
                                                        $MisPermisosPedidos = "SELECT SOL_PERMI.SP_ID,DOCUMENTO.DOC_NOM,SOL_PERMI.SP_MOT,SOL_PERMI.SP_ESTA,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_JOR,SOL_PERMI.DOC_ID,SOL_PERMI.SP_COM,SOL_PERMI.SP_CANT_DC,SOL_PERMI.SP_HOR_INI,SOL_PERMI.SP_HOR_FIN,SOL_PERMI.SP_TIPO,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM FROM SOL_PERMI, DOCUMENTO, USUARIO WHERE (SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID) AND (USUARIO.USU_RUT = SOL_PERMI.USU_RUT_JD) AND (SOL_PERMI.USU_RUT = '$Srut') AND ((SOL_PERMI.SP_FEC BETWEEN '$fec_ini' AND '$fec_fin') OR ((SOL_PERMI.SP_ESTA = 'SOLICITADO') OR (SOL_PERMI.SP_ESTA = 'AUTORIZADO J.D.'))) ORDER BY DOCUMENTO.DOC_NOM ASC";
                                                        $respuesta = mysqli_query($cnn, $MisPermisosPedidos);
                                                        while ($row_rs = mysqli_fetch_array($respuesta, MYSQLI_NUM)){
                                                            echo "<tr>";
                                                                echo "<td>".$row_rs[0]."</td>";
                                                                echo "<td>".utf8_encode($row_rs[1])."</td>";
                                                                echo "<td>".utf8_encode($row_rs[2])."</td>";
                                                                echo "<td>".utf8_encode($row_rs[3])."</td>";
                                                                echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MIPERMISO".$row_rs[0]."'>Detalle</a></td>";
                                                                if($row_rs[3] == "AUTORIZADO DIR"){
                                                                    echo "<td><button class='btn trigger' name='imprimirMiSP".$row_rs[0]."' id='imprimirMiSP".$row_rs[0]."' type='button' onclick='ImprimirSP(".$row_rs[0].");'>Imprimir</button></td>";
                                                                }else{
                                                                    echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                                                }
                                                                //descomponer fecha para validar boton
                                                                list($dia_actual, $mes_actual, $ano_actual) = split('[-]', $FecActual);
                                                                list($dia_permiso, $mes_permiso, $año_permiso) = split('[-]', $row_rs[5]);
                                                                if($row_rs[3] == "RECHAZADO J.D." || $row_rs[3] == "RECHAZADO DIR"){
                                                                    echo "<td><button class='btn trigger' name='cancelarSP' id='cancelarSP' disabled>Cancelar</button></td>";
                                                                }else{
                                                                    if($año_permiso == $ano_actual){
                                                                        //revisar mes
                                                                        if($mes_permiso == $mes_actual){
                                                                            //revisar dia
                                                                            if($dia_permiso == $dia_actual){
                                                                                //mostrar boton
                                                                                if($row_rs[3] == "CANCELADO POR USUARIO"){
                                                                                    echo "<td><button class='btn trigger' name='cancelarSP' id='cancelarSP' disabled>Cancelar</button></td>";
                                                                                }else{
                                                                                    echo "<td><button class='btn trigger' name='cancelarMiSP".$row_rs[0]."' id='cancelarMiSP".$row_rs[0]."' type='button' onclick='CancelarSP(".$row_rs[0].");'>Cancelar</button></td>";
                                                                                }
                                                                            }elseif($dia_permiso > $dia_actual){
                                                                                //mostrar boton
                                                                                if($row_rs[3] == "CANCELADO POR USUARIO"){
                                                                                    echo "<td><button class='btn trigger' name='cancelarSP' id='cancelarSP' disabled>Cancelar</button></td>";
                                                                                }else{
                                                                                    echo "<td><button class='btn trigger' name='cancelarMiSP".$row_rs[0]."' id='cancelarMiSP".$row_rs[0]."' type='button' onclick='CancelarSP(".$row_rs[0].");'>Cancelar</button></td>";
                                                                                }
                                                                            }elseif($dia_permiso < $dia_actual){
                                                                                //no mostrar boton
                                                                                echo "<td><button class='btn trigger' name='cancelarSP' id='cancelarSP' disabled>Cancelar</button></td>";
                                                                            }
                                                                        }elseif($mes_permiso > $mes_actual){
                                                                            //mostrar boton
                                                                            if($row_rs[3] == "CANCELADO POR USUARIO"){
                                                                                echo "<td><button class='btn trigger' name='cancelarSP' id='cancelarSP' disabled>Cancelar</button></td>";
                                                                            }else{
                                                                                echo "<td><button class='btn trigger' name='cancelarMiSP".$row_rs[0]."' id='cancelarMiSP".$row_rs[0]."' type='button' onclick='CancelarSP(".$row_rs[0].");'>Cancelar</button></td>";
                                                                            }
                                                                        }elseif($mes_permiso < $mes_actual){
                                                                            //no mostrar boton
                                                                            echo "<td><button class='btn trigger' name='cancelarSP' id='cancelarSP' disabled>Cancelar</button></td>";
                                                                        }
                                                                    }elseif($año_permiso > $ano_actual){
                                                                        //muestro boton
                                                                        if($row_rs[3] == "CANCELADO POR USUARIO"){
                                                                            echo "<td><button class='btn trigger' name='cancelarSP' id='cancelarSP' disabled>Cancelar</button></td>";
                                                                        }else{
                                                                            echo "<td><button class='btn trigger' name='cancelarMiSP".$row_rs[0]."' id='cancelarMiSP".$row_rs[0]."' type='button' onclick='CancelarSP(".$row_rs[0].");'>Cancelar</button></td>";
                                                                        }
                                                                    }
                                                                }
                                                                //echo $row_rs[5]."- actual - ".$FecActual;
                                                            echo "</tr>";
                                                            //Modal detalle mispermiso
                                                            echo '<div id="MIPERMISO'.$row_rs[0].'" class="modal">';
                                                                echo '<div class="modal-content">';
                                                                    echo '<h4>Detalle de Documento</h4>';
                                                                    echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rs[0]).' <b>TIPO : </b>'.utf8_encode($row_rs[1]).'</p>';
                                                                    echo '<p><b>DIRIGIDO A : </b>'.utf8_encode($row_rs[14]).' '.utf8_encode($row_rs[15]).' '.utf8_encode($row_rs[16]).'</p>';
                                                                    if ($row_rs[13] != "HORAS"){
                                                                        echo '<p><b>DIAS : </b>'.utf8_encode($row_rs[4]).' <b>DESDE EL : </b>'.$row_rs[5].' <b>HASTA EL : </b>'.$row_rs[6].' <b>JORNADA : </b>'.utf8_encode($row_rs[7]).' </p>';
                                                                    }else{
                                                                        echo '<p><b>HORAS : </b>'.$row_rs[10].' <b>EL DIA : </b>'.$row_rs[5].' <b>DESDE LAS : </b>'.$row_rs[11].' <b>HASTA LAS : </b>'.$row_rs[12].' </p>';
                                                                    }
                                                                    echo '<p><b>MOTIVO DEL PERMISO : </b>'.utf8_encode($row_rs[2]).'</p>';
                                                                    //CARGAR HISTO PERMISO
                                                                    $DetalleMiPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rs[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = ".$row_rs[8].")";
                                                                    $respuestaDetalleMiPermiso = mysqli_query($cnn, $DetalleMiPermiso);
                                                                    //recorrer los registros
                                                                    echo '<h5>SEGUIMIENTO</h5>';
                                                                    while ($row_rsDMP = mysqli_fetch_array($respuestaDetalleMiPermiso, MYSQLI_NUM)){
                                                                        echo '<p><b>FECHA : </b>'.$row_rsDMP[0].'     <b>HORA : </b>'.$row_rsDMP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMP[2]).'</p>';
                                                                    }
                                                                    if ($row_rs[3] == "RECHAZADO J.D." || $row_rs[3] == "RECHAZADO DIR"){
                                                                        echo '<h6><b>MOTIVO DEL RECHAZO : </b>'.utf8_encode($row_rs[9]).'</h6>';
                                                                    }
                                                                echo '</div>';
                                                                echo '<div class="modal-footer">';
                                                                    echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                echo '</div>';
                                                            echo '</div>';
                                                        }
                                                    ?>
                                                <!-- MOSTRAR MIS OT EXTRA -->
                                                    <?php
                                                    //MUESTRO MIS ORDENES DE TRABAJO
                                                        $MisOrdenesdeTrabajo = "SELECT OT_EXTRA.OE_ID,DOCUMENTO.DOC_NOM,OT_EXTRA.OE_TRAB,OT_EXTRA.OE_ESTA,OT_EXTRA.OE_CANT_CANCE,OT_EXTRA.OE_CANT_DC,OT_EXTRA.OE_COM,OTE_PROGRAMA.OP_NOM FROM OT_EXTRA INNER JOIN DOCUMENTO ON OT_EXTRA.DOC_ID = DOCUMENTO.DOC_ID LEFT JOIN OTE_PROGRAMA ON OT_EXTRA.OE_PROGRAMA = OTE_PROGRAMA.OP_ID  WHERE (OT_EXTRA.USU_RUT = '$Srut') AND (OT_EXTRA.OE_FEC BETWEEN '$fec_ini' AND '$fec_fin')";
                                                        //echo $MisOrdenesdeTrabajo;
                                                        $respuestaOT = mysqli_query($cnn, $MisOrdenesdeTrabajo);
                                                        //recorrer los registros
                                                        while ($row_rOT = mysqli_fetch_array($respuestaOT, MYSQLI_NUM)){
                                                            echo "<tr>";
                                                                echo "<td>".$row_rOT[0]."</td>";
                                                                echo "<td>".utf8_encode($row_rOT[1])."</td>";
                                                                echo "<td>".utf8_encode($row_rOT[2])."</td>";
                                                                echo "<td>".utf8_encode($row_rOT[3])."</td>";
                                                                //echo "<td><a class='waves-effect waves-light btn' href='#MIPERMISO".$row_rs[0]."'>Detalle</a></td>";
                                                                if($row_rOT[3] == "EN CREACION"){
                                                                    echo "<td><button class='btn trigger' name='IrOT' id='IrOT' type='button' onclick='Iraorden(".$row_rOT[0].");'>&nbsp&nbsp&nbsp&nbspVer&nbsp&nbsp&nbsp&nbsp&nbsp</button></td>";
                                                                }else{
                                                                    echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MIORDEN".$row_rOT[0]."'>Detalle</a></td>";
                                                                }
                                                                if($row_rOT[3] == "V.B. DIR SALUD"){
                                                                    echo "<td><button class='btn trigger' name='imprimir' id='imprimir' type='button' onclick='ImprimirOT(".$row_rOT[0].");'>Imprimir</button></td>";
                                                                }else{
                                                                    echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                                                }
                                                                if($row_rOT[3] == "SOLICITADO"){
                                                                    echo "<td><button class='btn trigger' name='cancelarMiOT".$row_rOT[0]."' id='cancelarMiOT".$row_rOT[0]."' type='button' onclick='CancelarOT(".$row_rOT[0].");'>Cancelar</button></td>";
                                                                }else{
                                                                    echo "<td><button class='btn trigger' name='cancelarOT' id='cancelarOT' type='button' disabled>Cancelar</button></td>";
                                                                }
                                                            echo "</tr>";
                                                            //Modal detalle mispermiso
                                                            echo '<div id="MIORDEN'.$row_rOT[0].'" class="modal">';
                                                                echo '<div class="modal-content">';
                                                                    echo '<h4>Detalle de Documento</h4>';
                                                                    echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rOT[0]).' <b>TIPO : </b>'.utf8_encode($row_rOT[1]).'</p>';
                                                                    //echo '<p><b>DIAS : </b>'.utf8_encode($row_rs[4]).' <b>DESDE EL : </b>'.$row_rs[5].' <b>HASTA EL : </b>'.$row_rs[6].' <b>JORNADA : </b>'.utf8_encode($row_rs[7]).' </p>';
                                                                    if($row_rOT[3] == "EN CREACION"){
                                                                        echo '<p><b>ESTADO DE ORDEN DE TRABAJO : </b>'.utf8_encode($row_rOT[3]).'</p>';
                                                                        //CARGO DETALLE DE HORAS
                                                                        $DetalleMiOrden = "SELECT DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN,OTE_TIPO FROM OTE_DETALLE WHERE (OE_ID = ".$row_rOT[0].") ORDER BY OTE_TIPO,OTE_DIA ASC ";
                                                                        $RespMiOrden = mysqli_query($cnn,$DetalleMiOrden);
                                                                        while ($row_rsRMO = mysqli_fetch_array($RespMiOrden, MYSQLI_NUM)){
                                                                            echo '<p><b>DIA : </b>'.$row_rsRMO[0].'     <b>HORA INICIO : </b>'.$row_rsRMO[1].'      <b>HORA FIN : </b>'.utf8_encode($row_rsRMO[2]).'      <b>TIPO : </b>'.utf8_encode($row_rsRMO[3]).'</p>';
                                                                        }
                                                                    }else{
                                                                        echo '<p><b>CUMPLIR EL TRABAJO DE : </b>'.utf8_encode($row_rOT[2]).'</p>';
																																				echo '<p><b>CON CARGO AL PROGRAMA : </b>'.utf8_encode($row_rOT[7]).'</p>';
                                                                        //CARGO DETALLE DE HORAS
                                                                        $DetalleMiOrden = "SELECT DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN,OTE_TIPO FROM OTE_DETALLE WHERE (OE_ID = ".$row_rOT[0].") ORDER BY OTE_TIPO,OTE_DIA ASC ";
                                                                        $RespMiOrden = mysqli_query($cnn,$DetalleMiOrden);
                                                                        while ($row_rsRMO = mysqli_fetch_array($RespMiOrden, MYSQLI_NUM)){
                                                                            echo '<p><b>DIA : </b>'.$row_rsRMO[0].'     <b>HORA INICIO : </b>'.$row_rsRMO[1].'      <b>HORA FIN : </b>'.utf8_encode($row_rsRMO[2]).'      <b>TIPO : </b>'.utf8_encode($row_rsRMO[3]).'</p>';
                                                                        }
                                                                        echo '<p><b>HORAS CANCELADAS : </b>'.$row_rOT[4].' <b>HORAS DESCANSO COMPLEMENTARIO : </b>'.$row_rOT[5].'</p>';
                                                                    }
                                                                    //CARGAR HISTO PERMISO
                                                                    $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rOT[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = 5)";
                                                                    $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                    //recorrer los registros
                                                                    echo '<h5>SEGUIMIENTO</h5>';
                                                                    while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                        echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                    }
                                                                    if ($row_rOT[3] == "RECHAZADO J.D." || $row_rOT[3] == "RECHAZADO DIR" || $row_rOT[3] == "RECHAZADO DIR SALUD"){
                                                                        echo '<h6><b>MOTIVO DEL RECHAZO : </b>'.utf8_encode($row_rOT[6]).'</h6>';
                                                                    }
                                                                echo '</div>';
                                                                echo '<div class="modal-footer">';
                                                                    echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                echo '</div>';
                                                            echo '</div>';
                                                        }
                                                    ?>
                                                <!-- MOSTRAR MIS RESOLUCIONES DE VACACIONES -->
                                                    <?php
                                                    //MUESTRO RESOLUCION DE MIS VACACIONES
                                                        $MISRES_SOLPERMI = "SELECT RES_SOL_PERMI.RSP_ID,SOL_PERMI.SP_COM,RES_SOL_PERMI.RSP_ACC,SOL_PERMI.SP_ID,DOCUMENTO.DOC_NOM,SOL_PERMI.SP_MOT,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),RES_SOL_PERMI.RSP_RESOL,DATE_FORMAT(RES_SOL_PERMI.RSP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(RES_SOL_PERMI.RSP_FEC_FIN,'%d-%m-%Y') FROM RES_SOL_PERMI, SOL_PERMI, DOCUMENTO WHERE (RES_SOL_PERMI.SP_ID = SOL_PERMI.SP_ID) AND (SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID) AND (SOL_PERMI.USU_RUT = '$Srut') AND (RES_SOL_PERMI.RSP_ACC = 'EN ESPERA')";
                                                        $respuestaRSP = mysqli_query($cnn, $MISRES_SOLPERMI);
                                                        while ($row_rRSP = mysqli_fetch_array($respuestaRSP, MYSQLI_NUM)){
                                                            echo "<tr>";
                                                                echo "<td>".$row_rRSP[0]."</td>";
                                                                echo "<td>RESUESTA FERERIADO LEGAL</td>";
                                                                echo "<td>".utf8_encode($row_rRSP[1])."</td>";
                                                                echo "<td>".utf8_encode($row_rRSP[2])."</td>";
                                                                //echo "<td><a class='waves-effect waves-light btn' href='#MIPERMISO".$row_rs[0]."'>Detalle</a></td>";
                                                                echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MIRESOLUCION".$row_rRSP[0]."'>Detalle</a></td>";
                                                                echo "<td><button class='btn trigger' name='acepta".$row_rRSP[0]."' id='acepta".$row_rRSP[0]."' type='button' onclick='AceptaRSP(".$row_rRSP[0].");'>SOLICITAR</button></td>";
                                                                echo "<td><button class='btn trigger' name='acumula".$row_rRSP[0]."' id='acumula".$row_rRSP[0]."' type='button' onclick='AcumulaRSP(".$row_rRSP[0].");'>ACUMULAR</button></td>";
                                                            echo "</tr>";
                                                            //Modal detalle mi resolucion
                                                            echo '<div id="MIRESOLUCION'.$row_rRSP[0].'" class="modal">';
                                                                echo '<div class="modal-content">';
                                                                    echo '<h4>Detalle de Documento</h4>';
                                                                    echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rRSP[3]).' <b>TIPO : </b>'.utf8_encode($row_rRSP[4]).'</p>';
                                                                    //echo '<p><b>DIAS : </b>'.utf8_encode($row_rs[4]).' <b>DESDE EL : </b>'.$row_rs[5].' <b>HASTA EL : </b>'.$row_rs[6].' <b>JORNADA : </b>'.utf8_encode($row_rs[7]).' </p>';
                                                                    echo '<p><b>MOTIVO DEL PERMISO : </b>'.utf8_encode($row_rRSP[5]).'</p>';
                                                                    echo '<p><b>DIAS : </b>'.utf8_encode($row_rRSP[6]).' <b>DESDE EL : </b>'.$row_rRSP[7].' <b>HASTA EL : </b>'.$row_rRSP[8].' </p>';
                                                                    echo '<p><b>MOTIVO DE REAGENDAR FERIADO : </b>'.utf8_encode($row_rRSP[1]).'</p>';
                                                                    echo '<p><b>RESOLUCION : </b>'.utf8_encode($row_rRSP[9]).' <b>DESDE EL : </b>'.$row_rRSP[10].' <b>HASTA EL : </b>'.$row_rRSP[11].' </p>';
                                                                    //CARGAR HISTO PERMISO
                                                                    $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rRSP[3].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = 1)";
                                                                    //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                                                    $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                    //recorrer los registros
                                                                    echo '<h5>SEGUIMIENTO</h5>';
                                                                    while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                        echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                    }
                                                                echo '</div>';
                                                                echo '<div class="modal-footer">';
                                                                    echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                echo '</div>';
                                                            echo '</div>';
                                                        }
                                                    ?>
                                                <!-- MOSTRAR MIS SOLICITUDES DE ACUMULACION DE FERIADO -->
                                                    <?php
                                                        //MIS SOLICITUDES DE ACUMULACION DE FERIADOS
                                                        $MIS_SAF = "SELECT S.SAF_ID,D.DOC_NOM,S.SAF_MOT,S.SAF_ESTA,P.SP_CANT_DIA,DATE_FORMAT(P.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(P.SP_FEC_FIN,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_FIN,'%d-%m-%Y'),P.SP_COM,S.SAF_CANT_DIA,S.SAF_ANO_ACT,S.SAF_ANO_SIG,S.SAF_MOT,S.SAF_ESTA,R.RSP_RESOL,S.DOC_ID FROM SOL_ACU_FER S, RES_SOL_PERMI R, SOL_PERMI P, DOCUMENTO D WHERE (S.RSP_ID = R.RSP_ID) AND (S.SP_ID = P.SP_ID) AND (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = '$Srut') AND ((S.SAF_FEC BETWEEN '$fec_ini' AND '$fec_fin') OR ((S.SAF_ESTA = 'SOLICITADO') OR (S.SAF_ESTA = 'V.B. J.D.')))";
                                                        $RespuestaSAF = mysqli_query($cnn, $MIS_SAF);
                                                        //echo $MIS_SAF;
                                                        while ($row_MiSAF = mysqli_fetch_array($RespuestaSAF, MYSQLI_NUM)){
                                                            echo "<tr>";
                                                                echo "<td>".$row_MiSAF[0]."</td>";
                                                                echo "<td>".utf8_encode($row_MiSAF[1])."</td>";
                                                                echo "<td>".utf8_encode($row_MiSAF[2])."</td>";
                                                                echo "<td>".utf8_encode($row_MiSAF[3])."</td>";
                                                                echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MISAF".$row_MiSAF[0]."'>Detalle</a></td>";
                                                                if($row_MiSAF[3] == "AUTORIZADO DIR"){
                                                                    echo "<td><button class='btn trigger' name='imprimir' id='imprimir' type='button' onclick='ImprimirSAF(".$row_MiSAF[0].");'>Imprimir</button></td>";
                                                                }else{
                                                                    echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                                                }
                                                                if($row_MiSAF[3] == "SOLICITADO"){
                                                                    echo "<td><button class='btn trigger' name='cancelarMiSAF".$row_MiSAF[0]."' id='cancelarMiSAF".$row_MiSAF[0]."' type='button' onclick='CancelarSAF(".$row_MiSAF[0].");'>Cancelar</button></td>";
                                                                }else{
                                                                    echo "<td><button class='btn trigger' name='cancelarSAF' id='cancelarSAF' type='button' disabled>Cancelar</button></td>";
                                                                }
                                                                
                                                            echo "</tr>";
                                                            //modal detalle SAF
                                                            echo '<div id="MISAF'.$row_MiSAF[0].'" class="modal">';
                                                                echo '<div class="modal-content">';
                                                                    echo '<h4>Detalle de Documento</h4>';
                                                                    echo '<h5>FERIADO LEGAL SOLICITADO</h5>';
                                                                    echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_MiSAF[0]).' <b>TIPO : </b>'.utf8_encode($row_MiSAF[1]).'</p>';
                                                                    echo '<p><b>DIAS : </b>'.$row_MiSAF[4].' <b>DESDE EL : </b>'.$row_MiSAF[5].' <b>HASTA EL : </b>'.$row_MiSAF[6].'</p>';
                                                                    echo '<h5>RESOLUCION</h5>';
                                                                    echo '<p><b>RESOLUCION : </b>'.utf8_encode($row_MiSAF[15]).'</p>';
                                                                    echo '<p><b>FECHA SUGERIDA : </b>'.$row_MiSAF[7].' <b>HASTA EL : </b>'.$row_MiSAF[8].' </p>';
                                                                    echo '<p><b>MOTIVO DE REAGENDAR FERIADO : </b>'.utf8_encode($row_MiSAF[9]).'</p>';
                                                                    echo '<h5>ACUMULACION DE FERIADO</h5>';
                                                                    echo '<p><b>DIAS</b>'.$row_MiSAF[10].' <b>DEL AÑO : </b>'.$row_MiSAF[11].' <b>PARA EL : </b>'.$row_MiSAF[12].' </p>';
                                                                    echo '<p><b>MOTIVO DE ACUMULACION FERIADO : </b>'.utf8_encode($row_MiSAF[13]).'</p>';
                                                                    //CARGAR HISTO PERMISO
                                                                    $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_MiSAF[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = ".$row_MiSAF[16].")";
                                                                    //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                                                    $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                    //recorrer los registros
                                                                    echo '<h5>SEGUIMIENTO</h5>';
                                                                    while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                        echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                    }
                                                                echo '</div>';
                                                                echo '<div class="modal-footer">';
                                                                    echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                echo '</div>';
                                                            echo '</div>';
                                                        }
                                                    ?>
                                                <!-- MOSTRAR SOLICITUDES DE PERMISO SIN GOCE DE SUELDO  SOLICITADO AUTORIZADOR DIR-->
                                                    <?php
                                                        $MIS_SGR = "SELECT S.SPR_ID,D.DOC_NOM,S.SPR_MOT,S.SPR_ESTA,S.SPR_NDIA,DATE_FORMAT(S.SPR_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(S.SPR_FEC_FIN,'%d-%m-%Y'),S.SPR_COM,S.DOC_ID FROM SOL_PSGR S, DOCUMENTO D WHERE (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = '$Srut') AND ((S.SPR_FEC BETWEEN '$fec_ini' AND '$fec_fin') OR ((S.SPR_ESTA = 'SOLICITADO) OR (S.SPR_ESTA = AUTORIZADO DIR)))";
                                                        $RespuestaSPR = mysqli_query($cnn, $MIS_SGR);
                                                        //echo $MIS_SAF;
                                                        while ($row_MiSPR = mysqli_fetch_array($RespuestaSPR, MYSQLI_NUM)){
                                                            echo "<tr>";
                                                                echo "<td>".$row_MiSPR[0]."</td>";
                                                                echo "<td>".utf8_encode($row_MiSPR[1])."</td>";
                                                                echo "<td>".utf8_encode($row_MiSPR[2])."</td>";
                                                                echo "<td>".utf8_encode($row_MiSPR[3])."</td>";
                                                                echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MISPR".$row_MiSPR[0]."'>Detalle</a></td>";
                                                                if($row_MiSPR[3] == "AUTORIZADO DIR SALUD"){
                                                                    echo "<td><button class='btn trigger' name='imprimir' id='imprimir' type='button' onclick='ImprimirSPR(".$row_MiSPR[0].");'>Imprimir</button></td>";
                                                                }else{
                                                                    echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                                                }
                                                                //descomponer fecha para validar boton
                                                                list($dia_actual, $mes_actual, $ano_actual) = split('[-]', $FecActual);
                                                                list($dia_permiso, $mes_permiso, $año_permiso) = split('[-]', $row_MiSPR[5]);
                                                                if($row_MiSPR[3] == "SOLICITADO"){
                                                                    if($row_MiSPR[3] == "RECHAZADO DIR"){
                                                                        echo "<td><button class='btn trigger' name='cancelarSPR' id='cancelarSPR' disabled>Cancelar</button></td>";
                                                                    }else{
                                                                        if($año_permiso == $ano_actual){
                                                                            //revisar mes
                                                                            if($mes_permiso == $mes_actual){
                                                                                //revisar dia
                                                                                if($dia_permiso == $dia_actual){
                                                                                    //mostrar boton
                                                                                    if($row_MiSPR[3] == "CANCELADO POR USUARIO"){
                                                                                        echo "<td><button class='btn trigger' name='cancelarSPR' id='cancelarSPR' disabled>Cancelar</button></td>";
                                                                                    }else{
                                                                                        echo "<td><button class='btn trigger' name='cancelarMiSPR".$row_MiSPR[0]."' id='cancelarMiSPR".$row_MiSPR[0]."' type='button' onclick='CancelarSPR(".$row_MiSPR[0].");'>Cancelar</button></td>";
                                                                                    }
                                                                                }elseif($dia_permiso > $dia_actual){
                                                                                    //mostrar boton
                                                                                    if($row_MiSPR[3] == "CANCELADO POR USUARIO"){
                                                                                        echo "<td><button class='btn trigger' name='cancelarSPR' id='cancelarSPR' disabled>Cancelar</button></td>";
                                                                                    }else{
                                                                                        echo "<td><button class='btn trigger' name='cancelarMiSPR".$row_MiSPR[0]."' id='cancelarMiSPR".$row_MiSPR[0]."' type='button' onclick='CancelarSPR(".$row_MiSPR[0].");'>Cancelar</button></td>";
                                                                                    }
                                                                                }elseif($dia_permiso < $dia_actual){
                                                                                    //no mostrar boton
                                                                                    echo "<td><button class='btn trigger' name='cancelarSPR' id='cancelarSPR' disabled>Cancelar</button></td>";
                                                                                }
                                                                            }elseif($mes_permiso > $mes_actual){
                                                                                //mostrar boton
                                                                                if($row_MiSPR[3] == "CANCELADO POR USUARIO"){
                                                                                    echo "<td><button class='btn trigger' name='cancelarSPR' id='cancelarSPR' disabled>Cancelar</button></td>";
                                                                                }else{
                                                                                    echo "<td><button class='btn trigger' name='cancelarMiSPR".$row_MiSPR[0]."' id='cancelarMiSPR".$row_MiSPR[0]."' type='button' onclick='CancelarSPR(".$row_MiSPR[0].");'>Cancelar</button></td>";
                                                                                }
                                                                            }elseif($mes_permiso < $mes_actual){
                                                                                //no mostrar boton
                                                                                echo "<td><button class='btn trigger' name='cancelarSPR' id='cancelarSPR' disabled>Cancelar</button></td>";
                                                                            }
                                                                        }elseif($año_permiso > $ano_actual){
                                                                            //muestro boton
                                                                            if($row_MiSPR[3] == "CANCELADO POR USUARIO"){
                                                                                echo "<td><button class='btn trigger' name='cancelarSPR' id='cancelarSPR' disabled>Cancelar</button></td>";
                                                                            }else{
                                                                                echo "<td><button class='btn trigger' name='cancelarMiSPR".$row_MiSPR[0]."' id='cancelarMiSPR".$row_MiSPR[0]."' type='button' onclick='CancelarSPR(".$row_MiSPR[0].");'>Cancelar</button></td>";
                                                                            }
                                                                        }
                                                                    }
                                                                }else{
                                                                    echo "<td><button class='btn trigger' name='cancelarSAF' id='cancelarSAF' type='button' disabled>Cancelar</button></td>";
                                                                }
                                                            echo "</tr>";
                                                            //modal detalle SAF
                                                            echo '<div id="MISPR'.$row_MiSPR[0].'" class="modal">';
                                                                echo '<div class="modal-content">';
                                                                    echo '<h4>Detalle de Documento</h4>';
                                                                    echo '<h5>PERMISO SIN GOCE DE REMUNERACION</h5>';
                                                                    echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_MiSPR[0]).' <b>TIPO : </b>'.utf8_encode($row_MiSPR[1]).'</p>';
                                                                    echo '<p><b>DIAS : </b>'.$row_MiSPR[4].' <b>DESDE EL : </b>'.$row_MiSPR[5].' <b>HASTA EL : </b>'.$row_MiSPR[6].'</p>';
                                                                    echo '<p><b>MOTIVO DE PERMISO SIN GOCE DE REMUNERACION : </b>'.utf8_encode($row_MiSPR[2]).'</p>';
                                                                    //CARGAR HISTO PERMISO
                                                                    $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_MiSPR[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = ".$row_MiSPR[8].")";
                                                                    //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                                                    $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                    //recorrer los registros
                                                                    echo '<h5>SEGUIMIENTO</h5>';
                                                                    while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                        echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                    }
                                                                    if($row_MiSPR[3] == "RECHAZADO DIR"){
                                                                        echo '<p><b>MOTIVO RECHAZO : </b>'.utf8_encode($row_MiSPR[7]).'</p>';
                                                                    }
                                                                echo '</div>';
                                                                echo '<div class="modal-footer">';
                                                                    echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                echo '</div>';
                                                            echo '</div>';
                                                        }
                                                    ?>
                                                <!-- MOSTRAR COMETIDOS -->
                                                    <?php
                                                    //MUESTRO MIS COMETIDOS
                                                        $MisCometidos = "SELECT COME_PERMI.CO_ID,DOCUMENTO.DOC_NOM,COME_PERMI.CO_MOT,COME_PERMI.CO_ESTA,COME_PERMI.CO_VIA,COME_PERMI.CO_DIA,COME_PERMI.CO_PAS,COME_PERMI.CO_COM,COME_PERMI.CO_PEA,COME_PERMI.CO_PAR,COME_PERMI.CO_DES FROM COME_PERMI INNER JOIN DOCUMENTO ON COME_PERMI.DOC_ID = DOCUMENTO.DOC_ID WHERE (COME_PERMI.USU_RUT = '$Srut') AND ((COME_PERMI.CO_FEC BETWEEN '$fec_ini' AND '$fec_fin') OR ((COME_PERMI.CO_ESTA = 'EN CREACION') OR (COME_PERMI.CO_ESTA = 'SOLICITADO') OR (COME_PERMI.CO_ESTA = 'V.B. J.D.')))";
                                                        //echo $MisOrdenesdeTrabajo;
                                                        $respuestaCO = mysqli_query($cnn, $MisCometidos);
                                                        //recorrer los registros
                                                        while ($row_CO = mysqli_fetch_array($respuestaCO, MYSQLI_NUM)){
                                                            echo "<tr>";
                                                                echo "<td>".$row_CO[0]."</td>";
                                                                echo "<td>".utf8_encode($row_CO[1])."</td>";
                                                                echo "<td>".utf8_encode($row_CO[2])."</td>";
                                                                echo "<td>".utf8_encode($row_CO[3])."</td>";
                                                                //echo "<td><a class='waves-effect waves-light btn' href='#MIPERMISO".$row_rs[0]."'>Detalle</a></td>";
                                                                if($row_CO[3] == "EN CREACION"){
                                                                    echo "<td><button class='btn trigger' name='IrCO' id='IrCO' type='button' onclick='IraCome(".$row_CO[0].");'>&nbsp&nbsp&nbsp&nbspVer&nbsp&nbsp&nbsp&nbsp&nbsp</button></td>";
                                                                }else{
                                                                    echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MICOME".$row_CO[0]."'>Detalle</a></td>";
                                                                }
                                                                if($row_CO[3] == "AUTORIZADO DIR"){
                                                                    echo "<td><button class='btn trigger' name='imprimirCO' id='imprimirCO' type='button' onclick='ImprimirCO(".$row_CO[0].");'>Imprimir</button></td>";
                                                                }else{
                                                                    echo "<td><button class='btn trigger' name='imprimir' id='imprimir' disabled>Imprimir</button></td>";
                                                                }
                                                                if($row_CO[3] == "SOLICITADO" ){
                                                                    echo "<td><button class='btn trigger' name='cancelarCO' id='cancelarCO' type='button' onclick='CancelarCO(".$row_CO[0].");'>Cancelar</button></td>";
                                                                }else{
                                                                    echo "<td><button class='btn trigger' name='cancelarCO' id='cancelarCO' type='button' disabled>Cancelar</button></td>";
                                                                }
                                                            echo "</tr>";
                                                            //Modal detalle mispermiso
                                                            echo '<div id="MICOME'.$row_CO[0].'" class="modal">';
                                                                echo '<div class="modal-content">';
                                                                    echo '<h4>Detalle de Documento</h4>';
                                                                    echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_CO[0]).' <b>TIPO : </b>'.utf8_encode($row_CO[1]).'</p>';
                                                                    //echo '<p><b>DIAS : </b>'.utf8_encode($row_rs[4]).' <b>DESDE EL : </b>'.$row_rs[5].' <b>HASTA EL : </b>'.$row_rs[6].' <b>JORNADA : </b>'.utf8_encode($row_rs[7]).' </p>';
                                                                    if($row_CO[3] == "EN CREACION"){
                                                                        echo '<p><b>ESTADO DEL COMETIDO : </b>'.utf8_encode($row_CO[3]).'</p>';
                                                                        //CARGO DETALLE DE COMETIDO
                                                                        $DetalleMiCome = "SELECT DATE_FORMAT(CD_DIA,'%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID = ".$row_CO[0].") ORDER BY CD_DIA ASC ";
                                                                        $RespMiCome = mysqli_query($cnn,$DetalleMiCome);
                                                                        while ($row_CD = mysqli_fetch_array($RespMiCome, MYSQLI_NUM)){
                                                                            echo '<p><b>DIA : </b>'.$row_CD[0].'     <b>HORA INICIO : </b>'.$row_CD[1].'      <b>HORA FIN : </b>'.utf8_encode($row_CD[2]).'      <b>PORCENTAJE : </b>'.utf8_encode($row_CD[3]).'</p>';
                                                                        }
                                                                    }else{
                                                                        echo '<p><b>MOTIVO : </b>'.utf8_encode($row_CO[2]).'</p>';
                                                                        //CARGO DETALLE DE COMETIDO
                                                                        $DetalleMiCome = "SELECT DATE_FORMAT(CD_DIA,'%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID = ".$row_CO[0].") ORDER BY CD_DIA ASC ";
                                                                        $RespMiCome = mysqli_query($cnn,$DetalleMiCome);
                                                                        while ($row_CD = mysqli_fetch_array($RespMiCome, MYSQLI_NUM)){
                                                                            echo '<p><b>DIA : </b>'.$row_CD[0].'     <b>HORA INICIO : </b>'.$row_CD[1].'      <b>HORA FIN : </b>'.utf8_encode($row_CD[2]).'      <b>PORCENTAJE : </b>'.utf8_encode($row_CD[3]).'</p>';
                                                                        }
                                                                    }
                                                                    $directorio = '../include/convocatoria';
                                                                    $sinpermi = $directorio."/8-".$row_CO[0].".pdf";
                                                                    chmod($sinpermi, 0755);
                                                                    if (is_readable($sinpermi)) {
                                                                        echo '<p><a onclick="Adjunto(8,'.$row_CO[0].');" href="http://200.68.34.158/include/convocatoria/8-'.$row_CO[0].'.pdf" target="_blank">Ver Adjunto</a></p>';
                                                                    chmod($sinpermi, 0000);
                                                                    }
                                                                    //CARGAR HISTO PERMISO
                                                                    $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_CO[0].") AND (USU_RUT = '".$Srut."') AND (DOC_ID = 8)";
                                                                    $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                    //recorrer los registros
                                                                    echo '<h5>SEGUIMIENTO</h5>';
                                                                    while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                        echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                    }
                                                                    if ($row_CO[3] == "RECHAZADO J.D." || $row_CO[3] == "RECHAZADO DIR"){
                                                                        echo '<h6><b>MOTIVO DEL RECHAZO : </b>'.utf8_encode($row_CO[6]).'</h6>';
                                                                    }
                                                                echo '</div>';
                                                                echo '<div class="modal-footer">';
                                                                    echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                echo '</div>';
                                                            echo '</div>';
                                                        }
                                                    ?>
                                                    <?php
                                                    //muestro memo horas extras
                                                    $MemoHorasExtras = "SELECT OT.OEE_ID,D.DOC_NOM,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,OT.OEE_MOTIVO,OT.OEE_ESTA FROM OT_EXTRA_ENC OT INNER JOIN USUARIO U ON OT.USU_RUT = U.USU_RUT INNER JOIN DOCUMENTO D ON OT.DOC_ID = D.DOC_ID WHERE (OT.USU_RUT = '$Srut') AND (OT.OEE_ESTA = 'ENVIADO')";
                                                    //echo $MemoHorasExtras;
                                                    $respuestaMOT = mysqli_query($cnn, $MemoHorasExtras);

                                                    while ($row_MOT = mysqli_fetch_array($respuestaMOT, MYSQLI_NUM)){
                                                        echo "<tr>";
                                                                echo "<td>".$row_MOT[0]."</td>";
                                                                echo "<td>".utf8_encode($row_MOT[1])."</td>";
                                                                echo "<td>".utf8_encode($row_MOT[6])."</td>";
                                                                echo "<td>".utf8_encode($row_MOT[7])."</td>";
                                                                echo "<td><a class='waves-effect waves-light btn modal-trigger' href='#MOT".$row_MOT[0]."'>Detalle</a></td>";
                                                                echo "<td><button class='btn trigger' name='aceptarMOT' id='aceptarMOT' type='button' disabled>&nbsp&nbspAceptar&nbsp&nbsp</button></td>";
                                                                echo "<td><button class='btn trigger' name='cancelarMOT' id='cancelarMOT' type='button' onclick='CancelarMOT(".$row_MOT[0].");'>Cancelar</button></td>";
                                                        echo "</tr>";
                                                        echo '<div id="MOT'.$row_MOT[0].'" class="modal">';
                                                                echo '<div class="modal-content">';
                                                                        echo '<h4>Detalle de Documento</h4>';
                                                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_MOT[0]).' <b>TIPO : </b>'.utf8_encode($row_MOT[1]).'</p>';
                                                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_MOT[3]).' '.utf8_encode($row_MOT[4]).' '.utf8_encode($row_MOT[5]);
                                                                        echo '<p><b>MOTIVO : </b>'.utf8_encode($row_MOT[6]).'</p>';
                                                                        //CARGO DETALLE
                                                                        echo '<p><a href="pdf/dto_ot_extra_deta.php?id='.$row_MOT[0].'" target = "_blank">Ver detalle</a></p>';
                                                                        //CARGAR HISTO PERMISO
                                                                        $DetalleMiHistoDocumento = "SELECT DATE_FORMAT(HD_FEC,'%d-%m-%Y'),HD_HORA,HD_ACC FROM HISTO_DOCU WHERE (HD_FOLIO = ".$row_MOT[0].") AND (USU_RUT = '".$row_MOT[2]."') AND (DOC_ID = 7)";
                                                                        $respuestaDetalleMiHistoDocu = mysqli_query($cnn, $DetalleMiHistoDocumento);
                                                                        //recorrer los registros
                                                                        echo '<h5>SEGUIMIENTO</h5>';
                                                                        while ($row_rsDMHD = mysqli_fetch_array($respuestaDetalleMiHistoDocu, MYSQLI_NUM)){
                                                                                echo '<p><b>FECHA : </b>'.$row_rsDMHD[0].'     <b>HORA : </b>'.$row_rsDMHD[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHD[2]).'</p>';
                                                                        }
                                                                echo '</div>';
                                                                echo '<div class="modal-footer">';
                                                                        echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                echo '</div>';
                                                        echo '</div>';
                                                    }

                                                    ?>
                                            </tbody>
                                        </thead>
                                    </table>
                                    <!-- VALIDAR USUARIO SEA JEFE - DIRECTOR -->
                                    <?php 
                                        if ($Scargo == "Director" || $Scargo == "Director (S)" || $Sjefatura == "SI"){
                                            echo '<table class="responsive-table boradered striped">';
                                                echo '<thead>';
                                                    echo '<tr>';
                                                        echo '<th>ID</th>';
                                                        echo '<th>TIPO</th>';
                                                        echo '<th>FUNCIONARIO</th>';
                                                        echo '<th>MOTIVO</th>';
                                                        echo '<th>ACCIONES</th>';
                                                        echo '<th></th>';
                                                    echo '</tr>';
                                                    echo '<tbody>';
                                                        ?> 
                                                        <!-- MOSTRAR PERMISOS ENVIADOS A JEFE DIRECTO -->
                                                            <?php
                                                            $MisPermisosSolicitadosJefe = "SELECT SOL_PERMI.SP_ID,DOCUMENTO.DOC_NOM,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,SOL_PERMI.SP_MOT,SOL_PERMI.SP_ESTA,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_JOR,SOL_PERMI.DOC_ID, USUARIO.USU_RUT,SOL_PERMI.SP_CANT_DC,SOL_PERMI.SP_HOR_INI,SOL_PERMI.SP_HOR_FIN,SOL_PERMI.SP_TIPO FROM SOL_PERMI, DOCUMENTO, USUARIO WHERE (SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID) AND (SOL_PERMI.USU_RUT = USUARIO.USU_RUT) AND (SOL_PERMI.USU_RUT_JD = '$Srut') AND (SOL_PERMI.SP_ESTA = 'SOLICITADO')";
                                                            $respuestaPermiJefes = mysqli_query($cnn, $MisPermisosSolicitadosJefe);
                                                            //recorrer los registros 
                                                            //echo $MisPermisosSolicitadosJefe;
                                                            while ($row_rsPJ = mysqli_fetch_array($respuestaPermiJefes, MYSQLI_NUM)){
                                                                echo "<tr>";
                                                                    echo "<td>".$row_rsPJ[0]."</td>";
                                                                    echo "<td>".utf8_encode($row_rsPJ[1])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsPJ[2])." ".utf8_encode($row_rsPJ[3])." ".utf8_encode($row_rsPJ[4])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsPJ[5])."</td>";
                                                                    echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerSP(".$row_rsPJ[0].");' href='#VERPERMISOJD".$row_rsPJ[0]."'>Detalle</a></td>";
                                                                    echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='AceptarSP(".$row_rsPJ[0].",".$row_rsPJ[11].");'>&nbsp&nbspAceptar&nbsp&nbsp</button></td>";
                                                                    echo "<td><button class='btn trigger' name='rechazar' id='rechazar' type='button' onclick='RechazarSP(".$row_rsPJ[0].");'>Rechazar</button></td>";
                                                                echo "</tr>";
                                                                echo '<div id="VERPERMISOJD'.$row_rsPJ[0].'" class="modal">';
                                                                    echo '<div class="modal-content">';
                                                                        echo '<h4>Detalle de Documento</h4>';
                                                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rsPJ[0]).' <b>TIPO : </b>'.utf8_encode($row_rsPJ[1]).'</p>';
                                                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsPJ[2]).' '.utf8_encode($row_rsPJ[3]).' '.utf8_encode($row_rsPJ[4]);
                                                                        if ($row_rsPJ[16] != "HORAS"){
                                                                            echo '<p><b>DIAS : </b>'.utf8_encode($row_rsPJ[7]).' <b>DESDE EL : </b>'.$row_rsPJ[8].' <b>HASTA EL : </b>'.$row_rsPJ[9].' <b>JORNADA : </b>'.utf8_encode($row_rsPJ[10]).' </p>';
                                                                        }else{
                                                                            echo '<p><b>HORAS : </b>'.$row_rsPJ[13].' <b>EL DIA : </b>'.$row_rsPJ[8].' <b>DESDE LAS : </b>'.$row_rsPJ[14].' <b>HASTA LAS : </b>'.$row_rsPJ[15].' </p>';
                                                                        }
                                                                        echo '<p><b>MOTIVO DEL PERMISO : </b>'.utf8_encode($row_rsPJ[5]).'</p>';
                                                                        //CARGAR HISTO PERMISO
                                                                        $DetalleSuPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsPJ[0].") AND (USU_RUT = '".$row_rsPJ[12]."') AND (DOC_ID = ".$row_rsPJ[11].")";
                                                                        $respuestaDetalleSuPermiso = mysqli_query($cnn, $DetalleSuPermiso);
                                                                        //recorrer los registros
                                                                        echo '<h5>SEGUIMIENTO</h5>';
                                                                        while ($row_rsDSP = mysqli_fetch_array($respuestaDetalleSuPermiso, MYSQLI_NUM)){
                                                                            echo '<p><b>FECHA : </b>'.$row_rsDSP[0].'     <b>HORA : </b>'.$row_rsDSP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDSP[2]).'</p>';
                                                                        }
                                                                    echo '</div>';
                                                                    echo '<div class="modal-footer">';
                                                                        echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                    echo '</div>';
                                                                echo '</div>';
                                                            }
                                                            ?>
                                                        <!-- MOSTRAR PERMISOS ENVIADOS A DIRECTOR -->
                                                            <?php
                                                            $MisPermisosSolicitadosDirector = "SELECT SOL_PERMI.SP_ID,DOCUMENTO.DOC_NOM,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,SOL_PERMI.SP_MOT,SOL_PERMI.SP_ESTA,SOL_PERMI.SP_CANT_DIA,DATE_FORMAT(SOL_PERMI.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(SOL_PERMI.SP_FEC_FIN,'%d-%m-%Y'),SOL_PERMI.SP_JOR,SOL_PERMI.DOC_ID, USUARIO.USU_RUT,SOL_PERMI.SP_CANT_DC,SOL_PERMI.SP_HOR_INI,SOL_PERMI.SP_HOR_FIN,SOL_PERMI.SP_TIPO FROM SOL_PERMI, DOCUMENTO, USUARIO WHERE (SOL_PERMI.DOC_ID = DOCUMENTO.DOC_ID) AND (SOL_PERMI.USU_RUT = USUARIO.USU_RUT) AND (SOL_PERMI.USU_RUT_DIR = '$Srut') AND (SOL_PERMI.SP_ESTA = 'AUTORIZADO J.D.')";
                                                            $respuestaPermiDir = mysqli_query($cnn, $MisPermisosSolicitadosDirector);
                                                            //recorrer los registros 
                                                            while ($row_rsPD = mysqli_fetch_array($respuestaPermiDir, MYSQLI_NUM)){
                                                                echo "<tr>";
                                                                    echo "<td>".$row_rsPD[0]."</td>";
                                                                    echo "<td>".utf8_encode($row_rsPD[1])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsPD[2])." ".utf8_encode($row_rsPD[3])." ".utf8_encode($row_rsPD[4])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsPD[5])."</td>";
                                                                    echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerSP(".$row_rsPD[0].");' href='#VERPERMISODIR".$row_rsPD[0]."'>Detalle</a></td>";
                                                                    echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='AceptarSPDIR(".$row_rsPD[0].");'>Autorizar</button></td>";
                                                                    echo "<td><button class='btn trigger' name='rechazar' id='rechazar' type='button' onclick='RechazarSP(".$row_rsPD[0].");'>Rechazar</button></td>";
                                                                echo "</tr>";
                                                                echo '<div id="VERPERMISODIR'.$row_rsPD[0].'" class="modal">';
                                                                    echo '<div class="modal-content">';
                                                                        echo '<h4>Detalle de Documento</h4>';
                                                                        echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rsPD[0]).' <b>TIPO : </b>'.utf8_encode($row_rsPD[1]).'</p>';
                                                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsPD[2]).' '.utf8_encode($row_rsPD[3]).' '.utf8_encode($row_rsPD[4]);
                                                                        if ($row_rsPD[16] != "HORAS"){
                                                                            echo '<p><b>DIAS : </b>'.utf8_encode($row_rsPD[7]).' <b>DESDE EL : </b>'.$row_rsPD[8].' <b>HASTA EL : </b>'.$row_rsPD[9].' <b>JORNADA : </b>'.utf8_encode($row_rsPD[10]).' </p>';
                                                                        }else{
                                                                            echo '<p><b>HORAS : </b>'.$row_rsPD[13].' <b>EL DIA : </b>'.$row_rsPD[8].' <b>DESDE LAS : </b>'.$row_rsPD[14].' <b>HASTA LAS : </b>'.$row_rsPD[15].' </p>';
                                                                        }
                                                                        echo '<p><b>MOTIVO DEL PERMISO : </b>'.utf8_encode($row_rsPD[5]).'</p>';
                                                                        //CARGAR HISTO PERMISO
                                                                        $DetalleSuPermisoD = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsPD[0].") AND (USU_RUT = '".$row_rsPD[12]."') AND (DOC_ID = ".$row_rsPD[11].")";
                                                                        $respuestaDetalleSuPermisoD = mysqli_query($cnn, $DetalleSuPermisoD);
                                                                        //recorrer los registros
                                                                        echo '<h5>SEGUIMIENTO</h5>';
                                                                        while ($row_rsDSPD = mysqli_fetch_array($respuestaDetalleSuPermisoD, MYSQLI_NUM)){
                                                                            echo '<p><b>FECHA : </b>'.$row_rsDSPD[0].'     <b>HORA : </b>'.$row_rsDSPD[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDSPD[2]).'</p>';
                                                                        }
                                                                    echo '</div>';
                                                                    echo '<div class="modal-footer">';
                                                                        echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                    echo '</div>';
                                                                echo '</div>';
                                                            }
                                                            ?>
                                                        <!-- MOSTRAR OT_EXTRA ENVIADOS A JEFE DIRECTO -->
                                                            <?php
                                                            $MisOrdenesJefe = "SELECT OT_EXTRA.OE_ID,DOCUMENTO.DOC_NOM,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,OT_EXTRA.OE_TRAB,OT_EXTRA.OE_CANT_CANCE,OT_EXTRA.OE_CANT_DC,USUARIO.USU_RUT,DOCUMENTO.DOC_ID,OT_EXTRA.OE_ESTA,OTE_PROGRAMA.OP_NOM FROM OT_EXTRA,DOCUMENTO,USUARIO,OTE_PROGRAMA WHERE (OT_EXTRA.DOC_ID = DOCUMENTO.DOC_ID) AND (OT_EXTRA.USU_RUT = USUARIO.USU_RUT) AND (OT_EXTRA.OE_PROGRAMA = OTE_PROGRAMA.OP_ID) AND (OT_EXTRA.USU_RUT_JF = '$Srut') AND (OT_EXTRA.OE_ESTA = 'SOLICITADO')";
                                                            $respuestaOTJefes = mysqli_query($cnn, $MisOrdenesJefe);
                                                            while ($row_rsOJ = mysqli_fetch_array($respuestaOTJefes, MYSQLI_NUM)){
                                                                echo "<tr>";
                                                                    echo "<td>".$row_rsOJ[0]."</td>";
                                                                    echo "<td>".utf8_encode($row_rsOJ[1])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsOJ[2])." ".utf8_encode($row_rsOJ[3])." ".utf8_encode($row_rsOJ[4])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsOJ[5])."</td>";           
                                                                    echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerOT(".$row_rsOJ[0].");' href='#VERSUOTJD".$row_rsOJ[0]."'>Detalle</a></td>";
                                                                    echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='AceptarOT(".$row_rsOJ[0].",".$row_rsOJ[9].");'>&nbsp&nbspAceptar&nbsp&nbsp</button></td>";
                                                                    echo "<td><button class='btn trigger' name='rechazar' id='rechazar' type='button' onclick='RechazarOT(".$row_rsOJ[0].",1);'>Rechazar</button></td>";
                                                                echo "</tr>";
                                                                echo '<div id="VERSUOTJD'.$row_rsOJ[0].'" class="modal">';
                                                                    echo '<div class="modal-content">';
                                                                        echo '<h4>Detalle de Documento</h4>';
                                                                        echo '<p><b>DOCUMENTO N° : </b>'.$row_rsOJ[0].' <b>TIPO : </b>'.utf8_encode($row_rsOJ[1]).'</p>';
                                                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsOJ[2]).' '.utf8_encode($row_rsOJ[3]).' '.utf8_encode($row_rsOJ[4]);
                                                                        echo '<p><b>PARA CUMPLIR EL TRABAJO DE : </b>'.utf8_encode($row_rsOJ[5]).'</p>';
                                                                        echo '<p><b>CON CARGO AL PROGRAMA : </b>'.utf8_encode($row_rsOJ[11]).'</p>';
                                                                        //CARGO DETALLE DE HORAS
                                                                        $DetalleSuOrden = "SELECT DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN FROM OTE_DETALLE WHERE (OE_ID = $row_rsOJ[0]) ORDER BY OTE_DIA ASC";
                                                                        //echo '<p>'.$DetalleSuOrden.'</p>';
                                                                        $RespSuOrden = mysqli_query($cnn,$DetalleSuOrden);
                                                                        while ($row_rsRSO = mysqli_fetch_array($RespSuOrden, MYSQLI_NUM)){
                                                                            echo '<p><b>DIA : </b>'.$row_rsRSO[0].'     <b>HORA INICIO : </b>'.$row_rsRSO[1].'      <b>HORA FIN : </b>'.utf8_encode($row_rsRSO[2]).'</p>';
                                                                        }
                                                                        echo '<p><b>HORAS CANCELADAS : </b>'.$row_rsOJ[6].' <b>HORAS DESCANSO COMPLEMENTARIO : </b>'.$row_rsOJ[7].'</p>';
                                                                        //CARGAR HISTO PERMISO
                                                                        $DetalleSuOT = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsOJ[0].") AND (USU_RUT = '".$row_rsOJ[8]."') AND (DOC_ID = ".$row_rsOJ[9].")";
                                                                        $respuestaDetalleSuOT = mysqli_query($cnn, $DetalleSuOT);
                                                                        //recorrer los registros
                                                                        echo '<h5>SEGUIMIENTO</h5>';
                                                                        while ($row_rsDSOT = mysqli_fetch_array($respuestaDetalleSuOT, MYSQLI_NUM)){
                                                                            echo '<p><b>FECHA : </b>'.$row_rsDSOT[0].'     <b>HORA : </b>'.$row_rsDSOT[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDSOT[2]).'</p>';
                                                                        }
                                                                    echo '</div>';
                                                                    echo '<div class="modal-footer">';
                                                                        echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                    echo '</div>';
                                                                echo '</div>';
                                                            }
                                                            ?>
                                                        <!-- MOSTRAR OT_EXTRA ENVIADOS A DIRECTOR -->
                                                            <?php
                                                            $MisOrdenesDir = "SELECT OT_EXTRA.OE_ID,DOCUMENTO.DOC_NOM,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,OT_EXTRA.OE_TRAB,OT_EXTRA.OE_CANT_CANCE,OT_EXTRA.OE_CANT_DC,USUARIO.USU_RUT,DOCUMENTO.DOC_ID,OT_EXTRA.OE_ESTA,OTE_PROGRAMA.OP_NOM FROM OT_EXTRA,DOCUMENTO,USUARIO,OTE_PROGRAMA WHERE (OT_EXTRA.DOC_ID = DOCUMENTO.DOC_ID) AND (OT_EXTRA.USU_RUT = USUARIO.USU_RUT) AND (OT_EXTRA.OE_PROGRAMA = OTE_PROGRAMA.OP_ID) AND (OT_EXTRA.USU_RUT_DIR = '$Srut') AND (OT_EXTRA.OE_ESTA = 'AUTORIZADO J.D.')";
                                                            $respuestaOTDir = mysqli_query($cnn, $MisOrdenesDir);
                                                            while ($row_rsOD = mysqli_fetch_array($respuestaOTDir, MYSQLI_NUM)){
                                                                echo "<tr>";
                                                                    echo "<td>".$row_rsOD[0]."</td>";
                                                                    echo "<td>".utf8_encode($row_rsOD[1])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsOD[2])." ".utf8_encode($row_rsOD[3])." ".utf8_encode($row_rsOD[4])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsOD[5])."</td>";           
                                                                    echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerOT(".$row_rsOD[0].");' href='#VERSUOTDIR".$row_rsOD[0]."'>Detalle</a></td>";
                                                                    //echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='AceptarOT(".$row_rsOD[0].",".$row_rsOD[9].");'>Aceptar</button></td>";
                                                                    echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='AceptarOT(".$row_rsOD[0].",".$row_rsOD[9].");'>&nbsp&nbspAceptar&nbsp&nbsp</button></td>";
                                                                    echo "<td><button class='btn trigger' name='rechazar' id='rechazar' type='button' onclick='RechazarOT(".$row_rsOD[0].",2);'>Rechazar</button></td>";
                                                                echo "</tr>";
                                                                echo '<div id="VERSUOTDIR'.$row_rsOD[0].'" class="modal">';
                                                                    echo '<div class="modal-content">';
                                                                        echo '<h4>Detalle de Documento</h4>';
                                                                        echo '<p><b>DOCUMENTO N° : </b>'.$row_rsOD[0].' <b>TIPO : </b>'.utf8_encode($row_rsOD[1]).'</p>';
                                                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsOD[2]).' '.utf8_encode($row_rsOD[3]).' '.utf8_encode($row_rsOD[4]);
                                                                        echo '<p><b>PARA CUMPLIR EL TRABAJO DE : </b>'.utf8_encode($row_rsOD[5]).'</p>';
                                                                        echo '<p><b>CON CARGO AL PROGRAMA : </b>'.utf8_encode($row_rsOD[11]).'</p>';
                                                                        //CARGO DETALLE DE HORAS
                                                                        $DetalleSuOrden = "SELECT DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN FROM OTE_DETALLE WHERE (OE_ID = ".$row_rsOD[0].")";
                                                                        $RespSuOrden = mysqli_query($cnn,$DetalleSuOrden);
                                                                        while ($row_rsRSO = mysqli_fetch_array($RespSuOrden, MYSQLI_NUM)){
                                                                            echo '<p><b>DIA : </b>'.$row_rsRSO[0].'     <b>HORA INICIO : </b>'.$row_rsRSO[1].'      <b>HORA FIN : </b>'.utf8_encode($row_rsRSO[2]).'</p>';
                                                                        }
                                                                        echo '<p><b>HORAS CANCELADAS : </b>'.$row_rsOD[6].' <b>HORAS DESCANSO COMPLEMENTARIO : </b>'.$row_rsOD[7].'</p>';
                                                                        //CARGAR HISTO PERMISO
                                                                        $DetalleSuOT = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsOD[0].") AND (USU_RUT = '".$row_rsOD[8]."') AND (DOC_ID = ".$row_rsOD[9].")";
                                                                        $respuestaDetalleSuOT = mysqli_query($cnn, $DetalleSuOT);
                                                                        //recorrer los registros
                                                                        echo '<h5>SEGUIMIENTO</h5>';
                                                                        while ($row_rsDSOT = mysqli_fetch_array($respuestaDetalleSuOT, MYSQLI_NUM)){
                                                                            echo '<p><b>FECHA : </b>'.$row_rsDSOT[0].'     <b>HORA : </b>'.$row_rsDSOT[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDSOT[2]).'</p>';
                                                                        }
                                                                    echo '</div>';
                                                                    echo '<div class="modal-footer">';
                                                                        echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                    echo '</div>';
                                                                echo '</div>';
                                                            }
                                                            ?>
                                                        <!-- MOSTRAR OT_EXTRA ENVIADOS PARA VISTO BUENO -->
                                                            <?php
                                                            $MisOrdenesVB = "SELECT OT_EXTRA.OE_ID,DOCUMENTO.DOC_NOM,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,OT_EXTRA.OE_TRAB,OT_EXTRA.OE_CANT_CANCE,OT_EXTRA.OE_CANT_DC,USUARIO.USU_RUT,DOCUMENTO.DOC_ID,OT_EXTRA.OE_ESTA,OTE_PROGRAMA.OP_NOM FROM OT_EXTRA,DOCUMENTO,USUARIO,OTE_PROGRAMA WHERE (OT_EXTRA.DOC_ID = DOCUMENTO.DOC_ID) AND (OT_EXTRA.USU_RUT = USUARIO.USU_RUT) AND (OT_EXTRA.OE_PROGRAMA = OTE_PROGRAMA.OP_ID) AND (OT_EXTRA.USU_RUT_VB = '$Srut') AND (OT_EXTRA.OE_ESTA = 'AUTORIZADO DIR')";
                                                            $respuestaOTVB = mysqli_query($cnn, $MisOrdenesVB);
                                                            while ($row_rsOVB = mysqli_fetch_array($respuestaOTVB, MYSQLI_NUM)){
                                                                echo "<tr>";
                                                                    echo "<td>".$row_rsOVB[0]."</td>";
                                                                    echo "<td>".utf8_encode($row_rsOVB[1])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsOVB[2])." ".utf8_encode($row_rsOVB[3])." ".utf8_encode($row_rsOVB[4])."</td>";
                                                                    echo "<td>".utf8_encode($row_rsOVB[5])."</td>";           
                                                                    echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerOT(".$row_rsOVB[0].");' href='#VERSUOTVB".$row_rsOVB[0]."'>Detalle</a></td>";
                                                                    echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='VistoBuenoOT(".$row_rsOVB[0].");'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspV.B.&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</button></td>";
                                                                    echo "<td><button class='btn trigger' name='rechazar' id='rechazar' type='button' onclick='RechazarOT(".$row_rsOVB[0].",3);'>Rechazar</button></td>";
                                                                echo "</tr>";
                                                                echo '<div id="VERSUOTVB'.$row_rsOVB[0].'" class="modal">';
                                                                    echo '<div class="modal-content">';
                                                                        echo '<h4>Detalle de Documento</h4>';
                                                                        echo '<p><b>DOCUMENTO N° : </b>'.$row_rsOVB[0].' <b>TIPO : </b>'.utf8_encode($row_rsOVB[1]).'</p>';
                                                                        echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsOVB[2]).' '.utf8_encode($row_rsOVB[3]).' '.utf8_encode($row_rsOVB[4]);
                                                                        echo '<p><b>PARA CUMPLIR EL TRABAJO DE : </b>'.utf8_encode($row_rsOVB[5]).'</p>';
                                                                        echo '<p><b>CON CARGO AL PROGRAMA : </b>'.utf8_encode($row_rsOVB[11]).'</p>';
                                                                        //CARGO DETALLE DE HORAS
                                                                        $DetalleSuOrden = "SELECT DATE_FORMAT(OTE_DIA,'%d-%m-%Y'),OTE_HORA_INI,OTE_HORA_FIN FROM OTE_DETALLE WHERE (OE_ID = ".$row_rsOVB[0].")";
                                                                        $RespSuOrden = mysqli_query($cnn,$DetalleSuOrden);
                                                                        while ($row_rsRSO = mysqli_fetch_array($RespSuOrden, MYSQLI_NUM)){
                                                                            echo '<p><b>DIA : </b>'.$row_rsRSO[0].'     <b>HORA INICIO : </b>'.$row_rsRSO[1].'      <b>HORA FIN : </b>'.utf8_encode($row_rsRSO[2]).'</p>';
                                                                        }
                                                                        echo '<p><b>HORAS CANCELADAS : </b>'.$row_rsOVB[6].' <b>HORAS DESCANSO COMPLEMENTARIO : </b>'.$row_rsOVB[7].'</p>';
                                                                        //CARGAR HISTO PERMISO
                                                                        $DetalleSuOT = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsOVB[0].") AND (USU_RUT = '".$row_rsOVB[8]."') AND (DOC_ID = ".$row_rsOVB[9].")";
                                                                        $respuestaDetalleSuOT = mysqli_query($cnn, $DetalleSuOT);
                                                                        //recorrer los registros
                                                                        echo '<h5>SEGUIMIENTO</h5>';
                                                                        while ($row_rsDSOT = mysqli_fetch_array($respuestaDetalleSuOT, MYSQLI_NUM)){
                                                                            echo '<p><b>FECHA : </b>'.$row_rsDSOT[0].'     <b>HORA : </b>'.$row_rsDSOT[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDSOT[2]).'</p>';
                                                                        }
                                                                    echo '</div>';
                                                                    echo '<div class="modal-footer">';
                                                                        echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                    echo '</div>';
                                                                echo '</div>';
                                                            }
                                                            ?>
                                                        <!-- MOSTRAR SOLICITUD DE ACUMULACION DE FERIADOS ENVIADOS A JEFE DIRECTO -->
                                                            <?php
                                                                $MisSAFJefe = "SELECT S.SAF_ID,D.DOC_NOM,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SAF_MOT,S.SAF_ESTA,P.SP_CANT_DIA,DATE_FORMAT(P.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(P.SP_FEC_FIN,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_FIN,'%d-%m-%Y'),P.SP_COM,S.SAF_CANT_DIA,S.SAF_ANO_ACT,S.SAF_ANO_SIG,S.SAF_MOT,S.SAF_ESTA,R.RSP_RESOL,S.DOC_ID FROM SOL_ACU_FER S, RES_SOL_PERMI R, SOL_PERMI P, DOCUMENTO D, USUARIO U WHERE (S.RSP_ID = R.RSP_ID) AND (S.SP_ID = P.SP_ID) AND (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = U.USU_RUT) AND (S.SAF_ESTA = 'SOLICITADO') AND (S.USU_RUT_JD = '$Srut')";
                                                                $respuestaSAFJefe = mysqli_query($cnn, $MisSAFJefe);
                                                                while ($row_rsSAFJ = mysqli_fetch_array($respuestaSAFJefe, MYSQLI_NUM)){
                                                                    echo "<tr>";
                                                                        echo "<td>".$row_rsSAFJ[0]."</td>";
                                                                        echo "<td>".utf8_encode($row_rsSAFJ[1])."</td>";
                                                                        echo "<td>".utf8_encode($row_rsSAFJ[3])." ".utf8_encode($row_rsSAFJ[4])." ".utf8_encode($row_rsSAFJ[5])."</td>";
                                                                        echo "<td>".utf8_encode($row_rsSAFJ[6])."</td>";           
                                                                        echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerSAF(".$row_rsSAFJ[0].");' href='#VERSUSAF".$row_rsSAFJ[0]."'>Detalle</a></td>";
                                                                        //echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='AceptarSAF(".$row_rsSAFJ[0].");'>Aceptar</button></td>";
                                                                        echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='VistoBuenoSAF(".$row_rsSAFJ[0].");'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspV.B.&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</button></td>";
                                                                        echo "<td><button class='btn trigger' name='rechazar' id='rechazar' type='button' disabled>Rechazar</button></td>";
                                                                    echo "</tr>";
                                                                    //modal detalle SAF
                                                                    echo '<div id="VERSUSAF'.$row_rsSAFJ[0].'" class="modal">';
                                                                        echo '<div class="modal-content">';
                                                                            echo '<h4>Detalle de Documento</h4>';
                                                                            echo '<h5>FERIADO LEGAL SOLICITADO</h5>';
                                                                            echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rsSAFJ[0]).' <b>TIPO : </b>'.utf8_encode($row_rsSAFJ[1]).'</p>';
                                                                            echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsSAFJ[3]).' '.utf8_encode($row_rsSAFJ[4]).' '.utf8_encode($row_rsSAFJ[5]);
                                                                            echo '<p><b>DIAS : </b>'.$row_rsSAFJ[8].' <b>DESDE EL : </b>'.$row_rsSAFJ[9].' <b>HASTA EL : </b>'.$row_rsSAFJ[10].'</p>';
                                                                            echo '<h5>RESOLUCION</h5>';
                                                                            echo '<p><b>RESOLUCION : </b>'.utf8_encode($row_rsSAFJ[19]).'</p>';
                                                                            echo '<p><b>FECHA SUGERIDA : </b>'.$row_rsSAFJ[11].' <b>HASTA EL : </b>'.$row_rsSAFJ[12].' </p>';
                                                                            echo '<p><b>MOTIVO DE REAGENDAR FERIADO : </b>'.utf8_encode($row_rsSAFJ[13]).'</p>';
                                                                            echo '<h5>ACUMULACION DE FERIADO</h5>';
                                                                            echo '<p><b>DIAS</b>'.$row_rsSAFJ[14].' <b>DEL AÑO : </b>'.$row_rsSAFJ[15].' <b>PARA EL : </b>'.$row_rsSAFJ[16].' </p>';
                                                                            echo '<p><b>MOTIVO DE ACUMULACION FERIADO : </b>'.utf8_encode($row_rsSAFJ[17]).'</p>';
                                                                            //CARGAR HISTO PERMISO
                                                                            $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsSAFJ[0].") AND (USU_RUT = '".$row_rsSAFJ[2]."') AND (DOC_ID = ".$row_rsSAFJ[20].")";
                                                                            //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                                                            $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                            //recorrer los registros
                                                                            echo '<h5>SEGUIMIENTO</h5>';
                                                                            while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                                echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                            }
                                                                        echo '</div>';
                                                                        echo '<div class="modal-footer">';
                                                                            echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                        echo '</div>';
                                                                    echo '</div>';
                                                                }
                                                            ?>
                                                        <!-- MOSTRAR SOLICITUD DE ACUMULACION DE FERIADOS ENVIADOS A DIRECTOR -->
                                                            <?php
                                                                $MisSAFJefe = "SELECT S.SAF_ID,D.DOC_NOM,S.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SAF_MOT,S.SAF_ESTA,P.SP_CANT_DIA,DATE_FORMAT(P.SP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(P.SP_FEC_FIN,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(R.RSP_FEC_FIN,'%d-%m-%Y'),P.SP_COM,S.SAF_CANT_DIA,S.SAF_ANO_ACT,S.SAF_ANO_SIG,S.SAF_MOT,S.SAF_ESTA,R.RSP_RESOL,S.DOC_ID FROM SOL_ACU_FER S, RES_SOL_PERMI R, SOL_PERMI P, DOCUMENTO D, USUARIO U WHERE (S.RSP_ID = R.RSP_ID) AND (S.SP_ID = P.SP_ID) AND (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = U.USU_RUT) AND ((S.SAF_ESTA = 'SOLICITADO') OR (S.SAF_ESTA = 'V.B. J.D.')) AND (S.USU_RUT_DIR = '$Srut')";
                                                                $respuestaSAFJefe = mysqli_query($cnn, $MisSAFJefe);
                                                                while ($row_rsSAFJ = mysqli_fetch_array($respuestaSAFJefe, MYSQLI_NUM)){
                                                                    echo "<tr>";
                                                                        echo "<td>".$row_rsSAFJ[0]."</td>";
                                                                        echo "<td>".utf8_encode($row_rsSAFJ[1])."</td>";
                                                                        echo "<td>".utf8_encode($row_rsSAFJ[3])." ".utf8_encode($row_rsSAFJ[4])." ".utf8_encode($row_rsSAFJ[5])."</td>";
                                                                        echo "<td>".utf8_encode($row_rsSAFJ[6])."</td>";           
                                                                        echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerSAF(".$row_rsSAFJ[0].");' href='#VERSUSAF".$row_rsSAFJ[0]."'>Detalle</a></td>";
                                                                        echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='AceptarSAF(".$row_rsSAFJ[0].");'>Aceptar</button></td>";
                                                                        //echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='VistoBuenoSAF(".$row_rsSAFJ[0].");'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspV.B.&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</button></td>";
                                                                        echo "<td><button class='btn trigger' name='rechazar' id='rechazar' type='button' disabled>Rechazar</button></td>";
                                                                    echo "</tr>";
                                                                    //modal detalle SAF
                                                                    echo '<div id="VERSUSAF'.$row_rsSAFJ[0].'" class="modal">';
                                                                        echo '<div class="modal-content">';
                                                                            echo '<h4>Detalle de Documento</h4>';
                                                                            echo '<h5>FERIADO LEGAL SOLICITADO</h5>';
                                                                            echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_rsSAFJ[0]).' <b>TIPO : </b>'.utf8_encode($row_rsSAFJ[1]).'</p>';
                                                                            echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_rsSAFJ[3]).' '.utf8_encode($row_rsSAFJ[4]).' '.utf8_encode($row_rsSAFJ[5]);
                                                                            echo '<p><b>DIAS : </b>'.$row_rsSAFJ[8].' <b>DESDE EL : </b>'.$row_rsSAFJ[9].' <b>HASTA EL : </b>'.$row_rsSAFJ[10].'</p>';
                                                                            echo '<h5>RESOLUCION</h5>';
                                                                            echo '<p><b>RESOLUCION : </b>'.utf8_encode($row_rsSAFJ[19]).'</p>';
                                                                            echo '<p><b>FECHA SUGERIDA : </b>'.$row_rsSAFJ[11].' <b>HASTA EL : </b>'.$row_rsSAFJ[12].' </p>';
                                                                            echo '<p><b>MOTIVO DE REAGENDAR FERIADO : </b>'.utf8_encode($row_rsSAFJ[13]).'</p>';
                                                                            echo '<h5>ACUMULACION DE FERIADO</h5>';
                                                                            echo '<p><b>DIAS</b>'.$row_rsSAFJ[14].' <b>DEL AÑO : </b>'.$row_rsSAFJ[15].' <b>PARA EL : </b>'.$row_rsSAFJ[16].' </p>';
                                                                            echo '<p><b>MOTIVO DE ACUMULACION FERIADO : </b>'.utf8_encode($row_rsSAFJ[17]).'</p>';
                                                                            //CARGAR HISTO PERMISO
                                                                            $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_rsSAFJ[0].") AND (USU_RUT = '".$row_rsSAFJ[2]."') AND (DOC_ID = ".$row_rsSAFJ[20].")";
                                                                            //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                                                            $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                            //recorrer los registros
                                                                            echo '<h5>SEGUIMIENTO</h5>';
                                                                            while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                                echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                            }
                                                                        echo '</div>';
                                                                        echo '<div class="modal-footer">';
                                                                            echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                        echo '</div>';
                                                                    echo '</div>';
                                                                }
                                                            ?>
                                                        <!-- MOSTRAR PERMISOS SIN GOCE DE REMUNERACIONES ENVIADOS A DIRECTOR -->
                                                            <?php
                                                                $MIS_SGR_DIR = "SELECT S.SPR_ID,D.DOC_NOM,U.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SPR_MOT,S.SPR_ESTA,S.SPR_NDIA,DATE_FORMAT(S.SPR_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(S.SPR_FEC_FIN,'%d-%m-%Y'),S.SPR_COM,S.DOC_ID FROM SOL_PSGR S, DOCUMENTO D, USUARIO U WHERE (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = U.USU_RUT) AND (S.USU_RUT_DIR = '$Srut') AND (S.SPR_ESTA = 'SOLICITADO')";
                                                                $RespuestaSPRd = mysqli_query($cnn, $MIS_SGR_DIR);
                                                                //echo $MIS_SAF;
                                                                while ($row_MiSPRd = mysqli_fetch_array($RespuestaSPRd, MYSQLI_NUM)){
                                                                    echo "<tr>";
                                                                        echo "<td>".$row_MiSPRd[0]."</td>";
                                                                        echo "<td>".utf8_encode($row_MiSPRd[1])."</td>";
                                                                        echo "<td>".utf8_encode($row_MiSPRd[3])." ".utf8_encode($row_MiSPRd[4])." ".utf8_encode($row_MiSPRd[5])."</td>";
                                                                        echo "<td>".utf8_encode($row_MiSPRd[6])."</td>";
                                                                        echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerSPR(".$row_MiSPRd[0].");' href='#VERSUSPR".$row_MiSPRd[0]."'>Detalle</a></td>";
                                                                        echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='AceptarSPR(".$row_MiSPRd[0].");'>&nbsp&nbspAceptar&nbsp&nbsp</button></td>";
                                                                        echo "<td><button class='btn trigger' name='rechazar' id='rechazar' type='button' onclick='RechazarSPR(".$row_MiSPRd[0].");'>Rechazar</button></td>";
                                                                    echo "</tr>";
                                                                    //modal detalle SAF
                                                                    echo '<div id="VERSUSPR'.$row_MiSPRd[0].'" class="modal">';
                                                                        echo '<div class="modal-content">';
                                                                            echo '<h4>Detalle de Documento</h4>';
                                                                            echo '<h5>PERMISO SIN GOCE DE REMUNERACION</h5>';
                                                                            echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_MiSPRd[0]).' <b>TIPO : </b>'.utf8_encode($row_MiSPRd[1]).'</p>';
                                                                            echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_MiSPRd[3]).' '.utf8_encode($row_MiSPRd[4]).' '.utf8_encode($row_MiSPRd[5]);
                                                                            echo '<p><b>DIAS : </b>'.$row_MiSPRd[8].' <b>DESDE EL : </b>'.$row_MiSPRd[9].' <b>HASTA EL : </b>'.$row_MiSPRd[10].'</p>';
                                                                            echo '<p><b>MOTIVO DE PERMISO SIN GOCE DE REMUNERACION : </b>'.utf8_encode($row_MiSPRd[6]).'</p>';
                                                                            //CARGAR HISTO PERMISO
                                                                            $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_MiSPRd[0].") AND (USU_RUT = '".$row_MiSPRd[2]."') AND (DOC_ID = ".$row_MiSPRd[12].")";
                                                                            //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                                                            $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                            //recorrer los registros
                                                                            echo '<h5>SEGUIMIENTO</h5>';
                                                                            while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                                echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                            }
                                                                        echo '</div>';
                                                                        echo '<div class="modal-footer">';
                                                                            echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                        echo '</div>';
                                                                    echo '</div>';
                                                                }
                                                            ?>
																														<!-- PERMISO SIN GOCE DE SUELDO A DIRECTOR DE SALUD -->
                                                            <?php
                                                                $MIS_SGR_DIR = "SELECT S.SPR_ID,D.DOC_NOM,U.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,S.SPR_MOT,S.SPR_ESTA,S.SPR_NDIA,DATE_FORMAT(S.SPR_FEC_INI,'%d-%m-%Y'),DATE_FORMAT(S.SPR_FEC_FIN,'%d-%m-%Y'),S.SPR_COM,S.DOC_ID FROM SOL_PSGR S, DOCUMENTO D, USUARIO U WHERE (S.DOC_ID = D.DOC_ID) AND (S.USU_RUT = U.USU_RUT) AND (S.USU_RUT_SALUD = '$Srut') AND (S.SPR_ESTA = 'AUTORIZADO DIR')";
                                                                $RespuestaSPRd = mysqli_query($cnn, $MIS_SGR_DIR);
                                                                //echo $MIS_SAF;
                                                                while ($row_MiSPRd = mysqli_fetch_array($RespuestaSPRd, MYSQLI_NUM)){
                                                                    echo "<tr>";
                                                                        echo "<td>".$row_MiSPRd[0]."</td>";
                                                                        echo "<td>".utf8_encode($row_MiSPRd[1])."</td>";
                                                                        echo "<td>".utf8_encode($row_MiSPRd[3])." ".utf8_encode($row_MiSPRd[4])." ".utf8_encode($row_MiSPRd[5])."</td>";
                                                                        echo "<td>".utf8_encode($row_MiSPRd[6])."</td>";
                                                                        echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerSPR(".$row_MiSPRd[0].");' href='#VERSUSPR".$row_MiSPRd[0]."'>Detalle</a></td>";
                                                                        echo "<td><button class='btn trigger' name='aceptar' id='aceptar' type='button' onclick='AutorizarSPR(".$row_MiSPRd[0].");'>&nbsp&nbspAceptar&nbsp&nbsp</button></td>";
                                                                        echo "<td><button class='btn trigger' name='rechazar' id='rechazar' type='button' onclick='RechazarDIRSPR(".$row_MiSPRd[0].");'>Rechazar</button></td>";
                                                                    echo "</tr>";
                                                                    //modal detalle SAF
                                                                    echo '<div id="VERSUSPR'.$row_MiSPRd[0].'" class="modal">';
                                                                        echo '<div class="modal-content">';
                                                                            echo '<h4>Detalle de Documento</h4>';
                                                                            echo '<h5>PERMISO SIN GOCE DE REMUNERACION</h5>';
                                                                            echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_MiSPRd[0]).' <b>TIPO : </b>'.utf8_encode($row_MiSPRd[1]).'</p>';
                                                                            echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_MiSPRd[3]).' '.utf8_encode($row_MiSPRd[4]).' '.utf8_encode($row_MiSPRd[5]);
                                                                            echo '<p><b>DIAS : </b>'.$row_MiSPRd[8].' <b>DESDE EL : </b>'.$row_MiSPRd[9].' <b>HASTA EL : </b>'.$row_MiSPRd[10].'</p>';
                                                                            echo '<p><b>MOTIVO DE PERMISO SIN GOCE DE REMUNERACION : </b>'.utf8_encode($row_MiSPRd[6]).'</p>';
                                                                            //CARGAR HISTO PERMISO
                                                                            $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_MiSPRd[0].") AND (USU_RUT = '".$row_MiSPRd[2]."') AND (DOC_ID = ".$row_MiSPRd[12].")";
                                                                            //echo '<p>'.$DetalleMiHistoPermiso.'</p>';
                                                                            $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                            //recorrer los registros
                                                                            echo '<h5>SEGUIMIENTO</h5>';
                                                                            while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                                echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                            }
                                                                        echo '</div>';
                                                                        echo '<div class="modal-footer">';
                                                                            echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                        echo '</div>';
                                                                    echo '</div>';
                                                                }
                                                            ?>
                                                    <!-- MOSTRAR COMETIDOS -->
                                                            <?php
                                                            //MUESTRO COMETIDOS A JEFE DIRECTO
                                                                    $SusCometidos = "SELECT COME_PERMI.CO_ID,DOCUMENTO.DOC_NOM,COME_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,COME_PERMI.CO_MOT,COME_PERMI.CO_ESTA,COME_PERMI.CO_VIA,COME_PERMI.CO_DIA,COME_PERMI.CO_PAS,COME_PERMI.CO_COM,COME_PERMI.CO_PEA,COME_PERMI.CO_PAR,COME_PERMI.CO_DES FROM COME_PERMI INNER JOIN DOCUMENTO ON COME_PERMI.DOC_ID = DOCUMENTO.DOC_ID INNER JOIN USUARIO ON COME_PERMI.USU_RUT = USUARIO.USU_RUT WHERE (COME_PERMI.USU_RUT_JD = '$Srut') AND (COME_PERMI.CO_ESTA = 'SOLICITADO')";
                                                                    //echo $SusCometidos;
                                                                    $respuestaSCO = mysqli_query($cnn, $SusCometidos);
                                                                    //recorrer los registros
                                                                    while ($row_SCO = mysqli_fetch_array($respuestaSCO, MYSQLI_NUM)){
                                                                            echo "<tr>";
                                                                                    echo "<td>".$row_SCO[0]."</td>";
                                                                                    echo "<td>".$row_SCO[1]."</td>";
                                                                                    echo "<td>".utf8_encode($row_SCO[3])." ".utf8_encode($row_SCO[4])." ".utf8_encode($row_SCO[5])."</td>";
                                                                                    echo "<td>".utf8_encode($row_SCO[6])."</td>";
                                                                                    echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerSCO(".$row_SCO[0].");' href='#SUCOME".$row_SCO[0]."'>Detalle</a></td>";
                                                                                    echo "<td><button class='btn trigger' name='aceptarSCO' id='aceptarSCO' type='button' onclick='AceptarSCO(".$row_SCO[0].");'>&nbsp&nbspAceptar&nbsp&nbsp</button></td>";
                                                                                    echo "<td><button class='btn trigger' name='cancelarSCO' id='cancelarSCO' type='button' onclick='RechazarSCO(".$row_SCO[0].");'>Cancelar</button></td>";
                                                                            echo "</tr>";
                                                                            //Modal detalle mispermiso
                                                                            echo '<div id="SUCOME'.$row_SCO[0].'" class="modal">';
                                                                                    echo '<div class="modal-content">';
                                                                                            echo '<h4>Detalle de Documento</h4>';
                                                                                            echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_SCO[0]).' <b>TIPO : </b>'.utf8_encode($row_SCO[1]).'</p>';
                                                                                            //echo '<p><b>DIAS : </b>'.utf8_encode($row_rs[4]).' <b>DESDE EL : </b>'.$row_rs[5].' <b>HASTA EL : </b>'.$row_rs[6].' <b>JORNADA : </b>'.utf8_encode($row_rs[7]).' </p>';
                                                                                            echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_SCO[3]).' '.utf8_encode($row_SCO[4]).' '.utf8_encode($row_SCO[5]);
                                                                                            echo '<p><b>MOTIVO : </b>'.utf8_encode($row_SCO[6]).'</p>';
                                                                                            echo '<p><b>DESTINO : </b>'.utf8_encode($row_SCO[14]).'</p>';
                                                                                            //CARGO DETALLE DE COMETIDO
                                                                                            $DetalleMiCome = "SELECT DATE_FORMAT(CD_DIA,'%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID = ".$row_SCO[0].") ORDER BY CD_DIA ASC ";
                                                                                            $RespMiCome = mysqli_query($cnn,$DetalleMiCome);
                                                                                            while ($row_CD = mysqli_fetch_array($RespMiCome, MYSQLI_NUM)){
                                                                                                    echo '<p><b>DIA : </b>'.$row_CD[0].'     <b>HORA INICIO : </b>'.$row_CD[1].'      <b>HORA FIN : </b>'.utf8_encode($row_CD[2]).'      <b>PORCENTAJE : </b>'.utf8_encode($row_CD[3]).'</p>';
                                                                                            }
                                                                                            $directorio = '../include/convocatoria';
                                                                                            $sinpermi = $directorio."/8-".$row_SCO[0].".pdf";
                                                                                            chmod($sinpermi, 0755);
                                                                                            if (is_readable($sinpermi)) {
                                                                                                echo '<p><a onclick="Adjunto(8,'.$row_SCO[0].');" href="http://200.68.34.158/include/convocatoria/8-'.$row_SCO[0].'.pdf" target="_blank">Ver Adjunto</a></p>';
                                                                                            chmod($sinpermi, 0000);
                                                                                            }
                                                                                            //CARGAR HISTO PERMISO
                                                                                            $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_SCO[0].") AND (USU_RUT = '".$row_SCO[2]."') AND (DOC_ID = 8)";
                                                                                            $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                                            //recorrer los registros
                                                                                            echo '<h5>SEGUIMIENTO</h5>';
                                                                                            while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                                                    echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                                            }
                                                                                    echo '</div>';
                                                                                    echo '<div class="modal-footer">';
                                                                                            echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                                    echo '</div>';
                                                                            echo '</div>';
                                                                    }
                                                            ?>
                                                            <?php
                                                            //MUESTRO COMETIDOS A DIRECTOR
                                                                    $SusCometidosDir = "SELECT COME_PERMI.CO_ID,DOCUMENTO.DOC_NOM,COME_PERMI.USU_RUT,USUARIO.USU_NOM,USUARIO.USU_APP,USUARIO.USU_APM,COME_PERMI.CO_MOT,COME_PERMI.CO_ESTA,COME_PERMI.CO_VIA,COME_PERMI.CO_DIA,COME_PERMI.CO_PAS,COME_PERMI.CO_COM,COME_PERMI.CO_PEA,COME_PERMI.CO_PAR,COME_PERMI.CO_DES FROM COME_PERMI INNER JOIN DOCUMENTO ON COME_PERMI.DOC_ID = DOCUMENTO.DOC_ID INNER JOIN USUARIO ON COME_PERMI.USU_RUT = USUARIO.USU_RUT WHERE (COME_PERMI.USU_RUT_DIR = '$Srut') AND (COME_PERMI.CO_ESTA = 'V.B. J.D.')";
                                                                    //echo $SusCometidos;
                                                                    $respuestaSCOD = mysqli_query($cnn, $SusCometidosDir);
                                                                    //recorrer los registros
                                                                    while ($row_SCOD = mysqli_fetch_array($respuestaSCOD, MYSQLI_NUM)){
                                                                            echo "<tr>";
                                                                                    echo "<td>".$row_SCOD[0]."</td>";
                                                                                    echo "<td>".$row_SCOD[1]."</td>";
                                                                                    echo "<td>".utf8_encode($row_SCOD[3])." ".utf8_encode($row_SCOD[4])." ".utf8_encode($row_SCOD[5])."</td>";
                                                                                    echo "<td>".utf8_encode($row_SCOD[6])."</td>";
                                                                                    echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerSCOD(".$row_SCOD[0].");' href='#SUCOMED".$row_SCOD[0]."'>Detalle</a></td>";
                                                                                    echo "<td><button class='btn trigger' name='aceptarSCO' id='aceptarSCO' type='button' onclick='AceptarSCOD(".$row_SCOD[0].");'>&nbsp&nbspAceptar&nbsp&nbsp</button></td>";
                                                                                    echo "<td><button class='btn trigger' name='cancelarSCO' id='cancelarSCO' type='button' onclick='RechazarSCOD(".$row_SCOD[0].");'>Cancelar</button></td>";
                                                                            echo "</tr>";
                                                                            //Modal detalle mispermiso
                                                                            echo '<div id="SUCOMED'.$row_SCOD[0].'" class="modal">';
                                                                                    echo '<div class="modal-content">';
                                                                                            echo '<h4>Detalle de Documento</h4>';
                                                                                            echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_SCOD[0]).' <b>TIPO : </b>'.utf8_encode($row_SCOD[1]).'</p>';
                                                                                            //echo '<p><b>DIAS : </b>'.utf8_encode($row_rs[4]).' <b>DESDE EL : </b>'.$row_rs[5].' <b>HASTA EL : </b>'.$row_rs[6].' <b>JORNADA : </b>'.utf8_encode($row_rs[7]).' </p>';
                                                                                            echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_SCOD[3]).' '.utf8_encode($row_SCOD[4]).' '.utf8_encode($row_SCOD[5]);
                                                                                            echo '<p><b>MOTIVO : </b>'.utf8_encode($row_SCOD[6]).'</p>';
                                                                                            echo '<p><b>DESTINO : </b>'.utf8_encode($row_SCOD[14]).'</p>';
                                                                                            //CARGO DETALLE DE COMETIDO
                                                                                            $DetalleMiCome = "SELECT DATE_FORMAT(CD_DIA,'%d-%m-%Y'),CD_HORA_INI,CD_HORA_FIN,CD_POR FROM COME_DETALLE WHERE (CO_ID = ".$row_SCOD[0].") ORDER BY CD_DIA ASC ";
                                                                                            $RespMiCome = mysqli_query($cnn,$DetalleMiCome);
                                                                                            while ($row_CDD = mysqli_fetch_array($RespMiCome, MYSQLI_NUM)){
                                                                                                    echo '<p><b>DIA : </b>'.$row_CDD[0].'     <b>HORA INICIO : </b>'.$row_CDD[1].'      <b>HORA FIN : </b>'.utf8_encode($row_CDD[2]).'      <b>PORCENTAJE : </b>'.utf8_encode($row_CDD[3]).'</p>';
                                                                                            }
                                                                                            $directorio = '../include/convocatoria';
                                                                                            $sinpermi = $directorio."/8-".$row_SCOD[0].".pdf";
                                                                                            chmod($sinpermi, 0755);
                                                                                            if (is_readable($sinpermi)) {
                                                                                                echo '<p><a onclick="Adjunto(8,'.$row_SCOD[0].');" href="http://200.68.34.158/include/convocatoria/8-'.$row_SCOD[0].'.pdf" target="_blank">Ver Adjunto</a></p>';
                                                                                            chmod($sinpermi, 0000);
                                                                                            }
                                                                                            //CARGAR HISTO PERMISO
                                                                                            $DetalleMiHistoPermiso = "SELECT DATE_FORMAT(HP_FEC,'%d-%m-%Y'),HP_HORA,HP_ACC FROM HISTO_PERMISO WHERE (HP_FOLIO = ".$row_SCOD[0].") AND (USU_RUT = '".$row_SCOD[2]."') AND (DOC_ID = 8)";
                                                                                            $respuestaDetalleMiHistoPermiso = mysqli_query($cnn, $DetalleMiHistoPermiso);
                                                                                            //recorrer los registros
                                                                                            echo '<h5>SEGUIMIENTO</h5>';
                                                                                            while ($row_rsDMHP = mysqli_fetch_array($respuestaDetalleMiHistoPermiso, MYSQLI_NUM)){
                                                                                                    echo '<p><b>FECHA : </b>'.$row_rsDMHP[0].'     <b>HORA : </b>'.$row_rsDMHP[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHP[2]).'</p>';
                                                                                            }
                                                                                    echo '</div>';
                                                                                    echo '<div class="modal-footer">';
                                                                                            echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                                    echo '</div>';
                                                                            echo '</div>';
                                                                    }
                                                            ?>
                                                            <?php
                                                            //muestro memo horas extras
                                                            $MemoHorasExtras = "SELECT OT.OEE_ID,D.DOC_NOM,OT.USU_RUT,U.USU_NOM,U.USU_APP,U.USU_APM,OT.OEE_MOTIVO,OT.OEE_ESTA FROM OT_EXTRA_ENC OT INNER JOIN USUARIO U ON OT.USU_RUT = U.USU_RUT INNER JOIN DOCUMENTO D ON OT.DOC_ID = D.DOC_ID WHERE (OT.USU_RUT_DIR = '$Srut') AND (OT.OEE_ESTA = 'ENVIADO')";
                                                            //echo $MemoHorasExtras;
                                                            $respuestaMOT = mysqli_query($cnn, $MemoHorasExtras);
                                                            
                                                            while ($row_MOT = mysqli_fetch_array($respuestaMOT, MYSQLI_NUM)){
                                                                echo "<tr>";
                                                                        echo "<td>".$row_MOT[0]."</td>";
                                                                        echo "<td>".utf8_encode($row_MOT[1])."</td>";
                                                                        echo "<td>".utf8_encode($row_MOT[3])." ".utf8_encode($row_MOT[4])." ".utf8_encode($row_MOT[5])."</td>";
                                                                        echo "<td>".utf8_encode($row_MOT[6])."</td>";
                                                                        echo "<td><a class='waves-effect waves-light btn modal-trigger' onclick='VerMOT(".$row_MOT[0].");' href='#MOT".$row_MOT[0]."'>Detalle</a></td>";
                                                                        echo "<td><button class='btn trigger' name='aceptarMOT' id='aceptarMOT' type='button' onclick='AceptarMOT(".$row_MOT[0].");'>&nbsp&nbspAceptar&nbsp&nbsp</button></td>";
                                                                        echo "<td><button class='btn trigger' name='cancelarMOT' id='cancelarMOT' type='button' onclick='RechazarMOT(".$row_MOT[0].");'>Cancelar</button></td>";
                                                                echo "</tr>";
                                                                echo '<div id="MOT'.$row_MOT[0].'" class="modal">';
                                                                        echo '<div class="modal-content">';
                                                                                echo '<h4>Detalle de Documento</h4>';
                                                                                echo '<p><b>DOCUMENTO N° : </b>'.utf8_encode($row_MOT[0]).' <b>TIPO : </b>'.utf8_encode($row_MOT[1]).'</p>';
                                                                                echo '<p><b>FUNCIONARIO : </b>'.utf8_encode($row_MOT[3]).' '.utf8_encode($row_MOT[4]).' '.utf8_encode($row_MOT[5]);
                                                                                echo '<p><b>MOTIVO : </b>'.utf8_encode($row_MOT[6]).'</p>';
                                                                                echo '<p><a href="pdf/dto_ot_extra_deta.php?id='.$row_MOT[0].'" target = "_blank">Ver detalle</a></p>';
                                                                                //CARGAR HISTO PERMISO
                                                                                $DetalleMiHistoDocumento = "SELECT DATE_FORMAT(HD_FEC,'%d-%m-%Y'),HD_HORA,HD_ACC FROM HISTO_DOCU WHERE (HD_FOLIO = ".$row_MOT[0].") AND (USU_RUT = '".$row_MOT[2]."') AND (DOC_ID = 7)";
                                                                                $respuestaDetalleMiHistoDocu = mysqli_query($cnn, $DetalleMiHistoDocumento);
                                                                                //recorrer los registros
                                                                                echo '<h5>SEGUIMIENTO</h5>';
                                                                                while ($row_rsDMHD = mysqli_fetch_array($respuestaDetalleMiHistoDocu, MYSQLI_NUM)){
                                                                                        echo '<p><b>FECHA : </b>'.$row_rsDMHD[0].'     <b>HORA : </b>'.$row_rsDMHD[1].'      <b>ACCION : </b>'.utf8_encode($row_rsDMHD[2]).'</p>';
                                                                                }
                                                                        echo '</div>';
                                                                        echo '<div class="modal-footer">';
                                                                                echo '<a href="index.php" class="modal-action modal-close waves-effect waves-red btn-flat">CERRAR</a>';
                                                                        echo '</div>';
                                                                echo '</div>';
                                                            }
                                                            
                                                            ?>
                                                        <?php
                                                    echo '</tbody>';
                                                echo '</thead>';
                                            echo '</table>';
                                            //echo $MisPermisosSolicitadosJefe;
                                        }
                                    ?>
                                </form>
                            </div>
                            <div id="yo" class="col s12">
                                <?php
                                        //rescato valores banco hora
                                list($ano_actual, $mes_actual, $dia_actual) = split('[-]', $fecha);
                                $FecIni = ($ano_actual - 2)."/".$mes_actual."/".$dia_actual;
                                //nuevo fecha corte dos meses mas que mes actual
                                $nuevo_ano_actual = $ano_actual - 2;
                                if($mes_actual == 11){
                                    $nuevo_mes_actual = 1;
                                    $nuevo_ano_actual = ($nuevo_ano_actual + 1);
                                }elseif($mes_actual == 12){
                                    $nuevo_mes_actual = 2;
                                    $nuevo_ano_actual = ($nuevo_ano_actual + 1);
                                }else{
                                    $nuevo_mes_actual = ($mes_actual + 2);
                                }
                                $FecFin = $nuevo_ano_actual."/".($nuevo_mes_actual )."/".$dia_actual;
                                //echo $fecha;
                                $query_banco_hora = "SELECT BH_SALDO,BH_FEC FROM BANCO_HORAS WHERE (USU_RUT = '$Srut') AND (BH_SALDO > 0) AND (BH_FEC BETWEEN '$FecIni' AND '$fecha') AND ((BH_TIPO = 'INICIAL') OR (BH_TIPO = 'INGRESO')) ORDER BY BH_FEC ASC";
                                $resultado_hora = mysqli_query($cnn, $query_banco_hora);
                                $cont = 1;
                                $primera_fecha = '';
                                if (mysqli_num_rows($resultado_hora) != 0){
                                    while ($row_hora = mysqli_fetch_array($resultado_hora)){
                                        if($primera_fecha == $row_hora[1]){
                                            $muestro_horas_proxima = $row_hora[0] + $muestro_horas_proxima;  
                                        }
                                        if($cont == 1){
                                            $primera_fecha = $row_hora[1];
                                            $muestro_horas_proxima = $row_hora[0];
                                            $muestro_fecha_proxima = $row_hora[1];
                                            list($ano_venc, $mes_venc, $dia_venc) = split('[-]', $muestro_fecha_proxima);
                                            $muestro_fecha_proxima = $dia_venc."/".$mes_venc."/".($ano_venc + 2);
                                        }
                                        $muestro_horas  = $row_hora[0] + $muestro_horas;
                                        $cont = $cont + 1;
                                    }
                                }else{
                                        $muestro_horas = 0;
                                }
                                // $query_banco_hora_proxima = "SELECT BH_SALDO FROM BANCO_HORAS WHERE (USU_RUT = '$Srut') AND (BH_SALDO > 0) AND (BH_FEC BETWEEN '$FecIni' AND '$FecFin') AND ((BH_TIPO = 'INICIAL') OR (BH_TIPO = 'INGRESO')) ORDER BY BH_FEC ASC";
                                // $resultado_hora_proxima = mysqli_query($cnn, $query_banco_hora_proxima);
                                // if (mysqli_num_rows($resultado_hora_proxima) != 0){
                                //     while ($row_hora_proxima = mysqli_fetch_array($resultado_hora_proxima)){
                                //         $muestro_horas_proxima  = $row_hora_proxima[0] + $muestro_horas_proxima;
                                //     }
                                // }else{
                                //     $muestro_horas_proxima = 0;
                                // }
                                //rescato valores banco dia
                                $ano_actual = date("Y");
                                $query_banco_fl = "SELECT BD_ADM,BD_ADM_USADO,BD_FL,BD_FLA,BD_FL_USADO,BD_SGR,BD_SGR_USADO  FROM BANCO_DIAS WHERE (USU_RUT = '$Srut') AND (BD_ANO = '$ano_actual')";
                                $resultado_banco_fl = mysqli_query($cnn, $query_banco_fl);
                                if (mysqli_num_rows($resultado_banco_fl) != 0){
                                    while ($row_bd = mysqli_fetch_array($resultado_banco_fl)){
                                        $muestro_adm  	= $row_bd[0];
                                        $muestro_adm_u 	= $row_bd[1];
                                        $muestro_fl			= $row_bd[2];
                                        $muestro_fla		= $row_bd[3];
                                        $muestro_fl_u		= $row_bd[4];
                                        $muestro_sgr		= $row_bd[5];
                                        $muestro_sgr_u	= $row_bd[6];
                                    }              
                                }
                                //muestro la informacion
                                echo '<table class="responsive-table boradered striped">';
                                    echo '<thead>';
                                        echo '<tr>';
                                            echo '<th></th>';
                                            echo '<th></th>';
                                            echo '<th></th>';
                                            echo '<th></th>';
                                        echo '</tr>';
                                        echo '<tbody class="mi_informacion">';
                                            echo '<tr>';
                                                echo '<td>Dias Administrativos :</td>';
                                                echo '<td>'.$muestro_adm.'</td>';
                                                echo '<td>Dias Adm. Usados : </td>';
                                                echo '<td>'.$muestro_adm_u.'</td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                                echo '<td>Feriados Legales :</td>';
                                                echo '<td>'.$muestro_fl.'</td>';
                                                echo '<td>Feriados Legales Acumulados : </td>';
                                                echo '<td>'.$muestro_fla.'</td>';
                                            echo '</tr>';
                                            $fl_total = $muestro_fl + $muestro_fla;
                                            echo '<tr>';
                                                echo '<td>Feriados Legales Disponibles :</td>';
                                                echo '<td>'.$fl_total.'</td>';
                                                echo '<td>Feriados Legales Usados : </td>';
                                                echo '<td>'.$muestro_fl_u.'</td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                                echo '<td>Horas Descanso Complementario :</td>';
                                                echo '<td>'.$muestro_horas.'</td>';
                                                echo '<td> Proximo vencimiento: ' .$muestro_fecha_proxima.':</td>';
                                                echo '<td>'.$muestro_horas_proxima.'</td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                                echo '<td>Dias sin Goce de Remuneracion :</td>';
                                                echo '<td> '.$muestro_sgr.' </td>';
                                                echo '<td>Dias S/Goce Usados : </td>';
                                                echo '<td> '.$muestro_sgr_u.' </td>';
                                            echo '</tr>';
                                            echo '<tr>';																									
                                                echo "<td><button class='btn trigger' type='button' onclick='ImprimirCC();'>Certificado Capacitación</button></td>";
                                                echo "<td><button class='btn trigger' type='button' onclick='ImprimirCE();'>Certificado Antigüedad</button></td>";
                                            echo '</tr>';
                                        echo '</tbody>';
                                    echo '</thead>';
                                  echo '</table>';
								?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <!-- footer -->
        <footer class="page-footer orange col l6 s12" style="position: fixed; bottom: 0; width: 100%; z-index: 9999;">
            <div class="footer-copyright">
                <div class="container">
                    <a class="grey-text text-lighten-4 right">© 2017 Unidad de Informatica - Direccion de Salud Municipal - Rengo.</a>
                </div>
            </div>
        </footer>
        <!-- Cargamos jQuery y materialize js -->
        <!-- <script type="text/javascript" src="../include/js/jquery.js"></script> -->
        <script type="text/javascript" src="../include/js/jquery.Rut.js"></script>
        <script type="text/javascript" src="../include/js/materialize.js"></script>
    </body>
</html>
