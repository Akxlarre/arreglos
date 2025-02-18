<?php
session_start();
include_once("lib/nusoap.php");
require_once 'lib/phpmailer/PHPMailerAutoload.php';
require_once 'lib/phpexcel/PHPExcel.php';
include("conexion.php");
include("funciones.php");
$datos = array();
$fecha = date('Y-m-d');
date_default_timezone_set("America/Santiago");
date_default_timezone_set("America/Santiago");
$fechachile = date("Y-m-d H:i:s");

if($_REQUEST['proceso']=='cuveh'){
	$archivoMaestra = "carga data intranet.xlsx";

	try {
		$objPHPExcel = PHPExcel_IOFactory::load($archivoMaestra);
	} catch(Exception $e){
		die('Error en la carga de archivo "'.pathinfo($archivoMaestra,PATHINFO_BASENAME));
	}

	$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	$arrayCount     = count($allDataInSheet);
	$cell           = 2;
	$ObjHoja        = $objPHPExcel->getActiveSheet()->getCell('A'.$cell)->getValue();
	$validacion     = $objPHPExcel->getActiveSheet()->rangeToArray('A1:M1');
	$errorExcel     = 0;
	$aCabeceras     = array (
		 strtoupper('PATENTE'),
		 strtoupper('IMEI'),
		 strtoupper('TIPO SERVICIO'),
		 strtoupper('TIPO VEHICULO')
	);

	$aDiferencias   = array_diff($validacion[0], $aCabeceras);
	$aDiferencias   = array_filter($aDiferencias);
	$estado         = false;
	$mensaje        = 'Los encabezados del documento no son válidos';
	$noValidos      = array();
	$totalNoNalidos = 0;
	$aDiferencias   = array_diff($validacion[0], $aCabeceras);
	$aDiferencias   = array_filter($aDiferencias);
	$estado         = false;
	$mensaje        = 'Los encabezados del documento no son válidos';
	$noValidos      = array();
	$totalNoNalidos = 0;
				
		$estado          		 = true;
		$mensaje         		 = 'Documento valido';	
		$hoja            		 = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$actualizaciones 		 = array();
		$indices         		 = 1;
		$cabecerasparaactualizar = array();
		$filasparaactualizar     = array();
		$letras                  = array('A','B','C','D','E','F','G','H','I','J','K','L','M');
		$i                       = 0;
		foreach ($hoja as $indice=>$celda){
			if($indices==1){
				foreach($letras as $let){
					if(isset($celda[$let])){
						array_push($cabecerasparaactualizar,$celda[$let]);
					}
				}
			}else{
				$arrdat = array();
				if($celda['A']!='' && $celda['A']!=null){
					$indicecabecera = 0;
					$patente        = '';
					$imei           = '';
					$tiposer        = 0;
					$tipoveh        = 0;
					$idveh          = 0;
					$cliente        = '';
					$rs             = '';
					$rut            = '';
					$cuenta         = '';
					$dispo          = '';
					foreach($letras as $let){
						if(isset($celda[$let])){
							
							if($cabecerasparaactualizar[$indicecabecera]=='PATENTE'){
								$patente = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='RUT'){
								$rut = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='CUENTA'){
								$cuenta = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='IMEI'){
								$imei = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='TIPO SERVICIO'){
								$tiposer = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='DISPOSITIVO'){
								$dispo = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='VEHICULO'){
								$tipoveh = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='CUENTA'){
								$cliente = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='RAZON SOCIAL'){
								$rs = $celda[$let];
							}

							if(strtoupper($tiposer)!='AVANZADO'){
	                            $tiposer = 'BASICO';
	                        }else{
	                            $tiposer = 'AVANZADO';
	                        }

	                        $sql2 = "SELECT * FROM servicios where ser_nombre = '".$tiposer."'";
	                        $res2 = $link->query($sql2);

	                        $idtiposer = 0;
	                        if($res2){
	                            foreach ($res2 as $key2){
		                            $idtiposer = $key2['ser_id'];
		                        }
	                        }

							$sql   = "select * from vehiculos where veh_patente = '".$patente."'";
                            $res   = $link->query($sql);
                            $fila4  = mysqli_fetch_array($res);
                            $idveh = '';
                             echo $patente.'<br>';
                            if($fila4['veh_id']>0 || $fila4['veh_id']!=null){
                            	foreach ($res as $key){
	                            	$idveh    = $key['veh_id'];
	                            	$sql1     = "SELECT * FROM tiposdevehiculos where tveh_nombre = '".$tipoveh."'";
	                                $res1     = $link->query($sql1);
	                                $idtipveh = 0;
	                                if($res1){
	                                    foreach ($res1 as $key1){
		                                    $idtipveh = $key1['tveh_id'];
		                                }
	                                }
	                            }
                            }else{
                        
	                            $sql1     = "SELECT * FROM tiposdevehiculos where tveh_nombre = '".$tipoveh."'";
	                            $res1     = $link->query($sql1);
	                            $idtipveh = 0;
	                            echo $sql1.'<br>';
	                            if($res1){
	                                foreach ($res1 as $key1){
		                                $idtipveh = $key1['tveh_id'];
		                            }
	                            }
                            }
							$indicecabecera ++;
						}
					}

					echo $idveh.'<---idvehiculo'.'<br>';
					if($idveh!=''){
						$sql3 = "update vehiculos set veh_imei = '".$imei."', veh_tipo = ".$idtipveh.", veh_tservicio = ".$idtiposer." where veh_id = ".$idveh." and veh_patente = '".$patente."'";
                        $res3 = $link->query($sql3);

                        $sql10  = "select * from productos where pro_nombre like '%{$dispo}%'";
				        $res10  = $link->query($sql10);
				        $fila10 = mysqli_fetch_array($res10);

				        $sql8 = "update serie_guia set pro_id = {$fila10['pro_id']} where ser_codigo = '{$imei}'";
				        $res8 = $link->query($sql8);

				        echo $sql8.'<br>';
                      
                        echo $sql3.'<br>';
                        echo '------------------------------------------------<br>';
					}else{
						$sql4 = "SELECT * FROM clientes where cli_nombrews = '".$cliente."' and razonsocial = '".$rs."'";
                        $res4 = $link->query($sql4);
                        $fila = mysqli_fetch_array($res4);

                        echo $sql4.'<br>';

                        if($fila['id']!='' || $fila['id']!=0){
                        	echo $sql4.'<br>';
	                        $indins = 0;
	                        foreach ($res4 as $key4) {
	                        	if ($indins==0) {
	                        		$sql4 = "INSERT INTO vehiculos(veh_tipo, veh_cliente, veh_patente, veh_imei, veh_tservicio) VALUES (".$idtipveh.",".$key4['id'].",'".$patente."','".$imei."',".$idtiposer.")";
	                                $res4 = $link->query($sql4);
	                                $idveh = $link->insert_id;

	                                $sql9  = "select * from serie_guia where ser_codigo = '{$imei}' and ser_estado = 1";
				                    $res9  = $link->query($sql9);
				                    $fila9 = mysqli_fetch_array($res9);
				                    $serid = $fila9['ser_id'];
				                    if($fila9['ser_id']==0 || $fila9['ser_id']==''){

				                    	$sql10  = "select * from productos where pro_nombre like '%{$dispo}%'";
				                        $res10  = $link->query($sql10);
				                        $fila10 = mysqli_fetch_array($res10);

				                    	$sql8 = "INSERT INTO serie_guia(gui_id, pro_id, ser_neto, ser_fecha, ser_estado,usu_id_ingresa,ser_codigo,prov_id,ser_instalado) VALUES (0,".$fila10['pro_id'].",0,'".$fechachile."',1,1,'".$imei."',0,1)";
				                        $res8 = $link->query($sql8);
				                        $serid = $link->insert_id;

				                        echo $sql8.'<br>';
	  
				                    }else{
				                    	$sql10  = "select * from productos where pro_nombre like '%{$dispo}%'";
				                        $res10  = $link->query($sql10);
				                        $fila10 = mysqli_fetch_array($res10);

				                        $sql8 = "update serie_guia set pro_id = {$fila10['pro_id']} where ser_codigo = '{$imei}'";
				                        $res8 = $link->query($sql8);

				                        echo $sql8.'<br>';
				                    }	

			                        $sql7 = "INSERT INTO productosxvehiculos(pxv_idveh, pxv_cantidad, pxv_nserie, pxv_ideasi, pxv_tipo, pxv_estado) VALUES (".$idveh.",1,'".$imei."',".$serid.",0,1)";
				                    $res7 = $link->query($sql7);

				                    echo $sql7.'<br>';
	                              
	                                echo $sql4.'<br>';
	                                echo '------------------------------------------------<br>';
	                        	}
	                        	$indins++;
	                        }
                        }else{

                        	$sql7  = "SELECT * FROM clientes where cli_nombrews = '".$cliente."'";
                        	$res7  = $link->query($sql7);
                        	$fila7 = mysqli_fetch_array($res7);

                        	echo $sql7.'<br>';
                        	echo $fila7['id'].'<-----fila7id'.'<br>';
                        	if($fila7['id']!=''){
                        		$sql6 = "INSERT INTO vehiculos(veh_tipo, veh_cliente, veh_patente, veh_imei, veh_tservicio) VALUES (".$idtipveh.",".$fila7['id'].",'".$patente."','".$imei."',".$idtiposer.")";
		                        $res6 = $link->query($sql6);
		                        $idveh = $link->insert_id;

		                        $sql9  = "select * from serie_guia where ser_codigo = '{$imei}' and ser_estado = 1";
			                    $res9  = $link->query($sql9);
			                    $fila9 = mysqli_fetch_array($res9);
			                    $serid = $fila9['ser_id'];
			                    if($fila9['ser_id']==0 || $fila9['ser_id']==''){

			                    	$sql10  = "select * from productos where pro_nombre like '%{$dispo}%'";
				                    $res10  = $link->query($sql10);
				                    $fila10 = mysqli_fetch_array($res10);

			                    	$sql8 = "INSERT INTO serie_guia(gui_id, pro_id, ser_neto, ser_fecha, ser_estado,usu_id_ingresa,ser_codigo,prov_id,ser_instalado) VALUES (0,".$fila10['pro_id'].",0,'".$fechachile."',1,1,'".$imei."',0,1)";
			                        $res8 = $link->query($sql8);
			                        $serid = $link->insert_id;

			                        echo $sql8.'<br>';
  
			                    }else{
				                    $sql10  = "select * from productos where pro_nombre like '%{$dispo}%'";
				                    $res10  = $link->query($sql10);
				                    $fila10 = mysqli_fetch_array($res10);

				                    $sql8 = "update serie_guia set pro_id = {$fila10['pro_id']} where ser_codigo = '{$imei}'";
				                    $res8 = $link->query($sql8);

				                     echo $sql8.'<br>';
				                }	

		                        $sql7 = "INSERT INTO productosxvehiculos(pxv_idveh, pxv_cantidad, pxv_nserie, pxv_ideasi, pxv_tipo, pxv_estado) VALUES (".$idveh.",1,'".$imei."',".$serid.",0,1)";
			                    $res7 = $link->query($sql7);

			                    echo $sql7.'<br>';

		                        echo $sql6.'<br>';
                        	}else{
                        		$sql5 = "INSERT INTO clientes(rut, razonsocial, cli_usuariows, cli_clavews, cli_nombrews,cli_estadows,cuenta) VALUES ('{$rut}','{$rs}','ws','ws','{$cuenta}',1,'{$cuenta}')";
		                        $res5      = $link->query($sql5);
		                        $idcliente = $link->insert_id;
		                        echo $sql5.'<br>';
		                        if($idcliente!='' || $idcliente!=0 || $idcliente!=null){
		                        	$sql6 = "INSERT INTO vehiculos(veh_tipo, veh_cliente, veh_patente, veh_imei, veh_tservicio) VALUES (".$idtipveh.",".$idcliente.",'".$patente."','".$imei."',".$idtiposer.")";
			                        $res6 = $link->query($sql6);
			                        $idveh = $link->insert_id;

			                        $sql9  = "select * from serie_guia where ser_codigo = '{$imei}' and ser_estado = 1";
				                    $res9  = $link->query($sql9);
				                    $fila9 = mysqli_fetch_array($res9);
				                    $serid = $fila9['ser_id'];
				                    if($fila9['ser_id']==0 || $fila9['ser_id']==''){
				                    	$sql10  = "select * from productos where pro_nombre like '%{$dispo}%'";
				                    	$res10  = $link->query($sql10);
				                    	$fila10 = mysqli_fetch_array($res10);

				                    	$sql8 = "INSERT INTO serie_guia(gui_id, pro_id, ser_neto, ser_fecha, ser_estado,usu_id_ingresa,ser_codigo,prov_id,ser_instalado) VALUES (0,".$fila10['pro_id'].",0,'".$fechachile."',1,1,'".$imei."',0,1)";
				                        $res8 = $link->query($sql8);
				                        $serid = $link->insert_id;

				                        echo $sql8.'<br>';
	  
				                    }else{
					                    $sql10  = "select * from productos where pro_nombre like '%{$dispo}%'";
					                    $res10  = $link->query($sql10);
					                    $fila10 = mysqli_fetch_array($res10);

					                    $sql8 = "update serie_guia set pro_id = {$fila10['pro_id']} where ser_codigo = '{$imei}'";
					                    $res8 = $link->query($sql8);

					                     echo $sql8.'<br>';
					                }

			                        $sql7 = "INSERT INTO productosxvehiculos(pxv_idveh, pxv_cantidad, pxv_nserie, pxv_ideasi, pxv_tipo, pxv_estado) VALUES (".$idveh.",1,'".$imei."',".$serid.",0,1)";
				                    $res7 = $link->query($sql7);

				                    echo $sql8.'<br>';
			                              
			                        echo $sql6.'<br>';
			                        echo '------------------------------------------------<br>';
		                        }
                        	}
                        } 
					}	
				}						
			}
			$indices++;				
		}
}else if($_REQUEST['proceso']=='cargaticket'){
	$archivoMaestra = "TRABAJOS TECNICOS.xlsx";

	try {
		$objPHPExcel = PHPExcel_IOFactory::load($archivoMaestra);
	} catch(Exception $e){
		die('Error en la carga de archivo "'.pathinfo($archivoMaestra,PATHINFO_BASENAME));
	}

	$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	$arrayCount     = count($allDataInSheet);
	$cell           = 2;
	$ObjHoja        = $objPHPExcel->getActiveSheet()->getCell('A'.$cell)->getValue();
	$validacion     = $objPHPExcel->getActiveSheet()->rangeToArray('A1:P1');
	$errorExcel     = 0;
	$aCabeceras     = array (
		 strtoupper('FECHA REGISTRO'),
		 strtoupper('CUENTA'),
		 strtoupper('RAZON SOCIAL'),
		 strtoupper('PATENTE'),
		 strtoupper('DISPOSITIVO'),
		 strtoupper('TIPO DE SERVICIO'),
		 strtoupper('TIPO DE TRABAJO'),
		 strtoupper('CONTACTO'),
		 strtoupper('CELULAR'),
		 strtoupper('CIUDAD'),
		 strtoupper('OBSERVACION'),
		 strtoupper('TIPO VEHICULO'),
		 strtoupper('MARCA'),
		 strtoupper('MODELO')
	);

	$aDiferencias   = array_diff($validacion[0], $aCabeceras);
	$aDiferencias   = array_filter($aDiferencias);
	$estado         = false;
	$mensaje        = 'Los encabezados del documento no son válidos';
	$noValidos      = array();
	$totalNoNalidos = 0;
	$aDiferencias   = array_diff($validacion[0], $aCabeceras);
	$aDiferencias   = array_filter($aDiferencias);
	$estado         = false;
	$mensaje        = 'Los encabezados del documento no son válidos';
	$noValidos      = array();
	$totalNoNalidos = 0;
				
		$estado          		 = true;
		$mensaje         		 = 'Documento valido';	
		$hoja            		 = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$actualizaciones 		 = array();
		$indices         		 = 1;
		$cabecerasparaactualizar = array();
		$filasparaactualizar     = array();
		$letras                  = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P');
		$i                       = 0;
		foreach ($hoja as $indice=>$celda){
			if($indices==1){
				foreach($letras as $let){
					if(isset($celda[$let])){
						array_push($cabecerasparaactualizar,$celda[$let]);
					}
				}
			}else{
				$arrdat = array();
				if($celda['A']!='' && $celda['A']!=null){
					$indicecabecera = 0;
					$fechar         = '';
					$cuenta         = '';
					$rs             = '';
					$patente        = '';
					$dispositivo    = '';
					$tipos          = '';
					$tipot          = '';
					$contacto       = '';
					$celular        = '';
					$ciudad         = '';
					$obs            = '';
					$tipoveh        = '';
					$marca          = '';
					$modelo         = '';
					$veh_id         = '';
					$ncuenta        = '';
					foreach($letras as $let){
						if(isset($celda[$let])){
							if($cabecerasparaactualizar[$indicecabecera]=='FECHA REGISTRO'){
								$fechar = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='CUENTA'){
								$ncuenta = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='RAZON SOCIAL'){
								$rs = $celda[$let];
							}
							
							if($cabecerasparaactualizar[$indicecabecera]=='PATENTE'){
								$patente = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='DISPOSITIVO'){
								$dispositivo = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='TIPO DE SERVICIO'){
								$tipos = $celda[$let];
							}
	
							if($cabecerasparaactualizar[$indicecabecera]=='TIPO DE TRABAJO'){
								$tipot = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='CONTACTO'){
								$contacto = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='CELULAR'){
								$celular = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='CIUDAD'){
								$ciudad = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='OBSERVACION'){
								$obs = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='TIPO VEHICULO'){
								$tipoveh = $celda[$let];
							}

						
							if($cabecerasparaactualizar[$indicecabecera]=='MARCA'){
								$marca = $celda[$let];
							}

							if($cabecerasparaactualizar[$indicecabecera]=='MODELO'){
								$modelo = $celda[$let];
							}   
							$indicecabecera ++;
						}
					}	
					
					$newDate = date("Y-m-d", strtotime($fechar));

						$sql     = "select * from clientes where cuenta like '%{$ncuenta}%' order by 1 desc limit 1";
	                    $res     = $link->query($sql);
	                    echo $sql.'<br>';
	                    $fila    = mysqli_fetch_array($res);
	                    if($fila['id']!=''){
	                        $cuenta = $fila['id'];
	                    }else{
	                        $cuenta = 0;
	                    }

	                    if($cuenta==0){
	                        $sql1 = "insert into clientes (razonsocial,cli_usuariows,cli_clavews,cli_nombrews,cli_estadows,cuenta) values ('{$rs}','ws','ws','{$ncuenta}',1,'{$ncuenta}')";
	                        $res1   = $link->query($sql1);
	                        echo $sql1.'<br>';
	                        $cuenta = $link->insert_id;
	                    }

	                    $sql3   = "select * from tiposdedispositivos where tdi_nombre = '{$dispositivo}' order by 1 desc limit 1";
	                    $res3   = $link->query($sql3);
	                    echo $sql3.'<br>';
	                    $fila3  = mysqli_fetch_array($res3);

	                    if($fila3['tdi_id']!=''){
	                        $dispositivo = $fila3['tdi_id'];
	                    }else{
	                        $dispositivo = 0;
	                    }

	                    $sql6   = "select * from tiposdevehiculos where tveh_nombre = '{$tipoveh}' order by 1 desc limit 1";
	                    $res6   = $link->query($sql6);
	                    echo $sql6.'<br>';
	                    $fila6  = mysqli_fetch_array($res6);

	                    if($fila6['tveh_id']!=''){
	                        $tipoveh = $fila6['tveh_id'];
	                    }else{
	                        $tipoveh = 0;
	                    }

	                    $sql4   = "select * from servicios where ser_nombre = '{$tipos}' order by 1 desc limit 1";
	                    $res4   = $link->query($sql4);
	                    echo $sql4.'<br>';
	                    $fila4  = mysqli_fetch_array($res4);

	                    if($fila4['ser_id']!=''){
	                        $tipos = $fila4['ser_id'];
	                    }else{
	                        $tipos = 0;
	                    }

	                    $sql5   = "select * from tiposdetrabajos where ttra_nombre = '{$tipot}' order by 1 desc limit 1";
	                    $res5   = $link->query($sql5);
	                    echo $sql5.'<br>';
	                    $fila5  = mysqli_fetch_array($res5);

	                    if($fila5['ttra_id']!=''){
	                        $tipot = $fila5['ttra_id'];
	                    }else{
	                        $tipot = 0;
	                    }

	                    $sql2   = "select * from vehiculos where veh_patente = '{$patente}' order by 1 desc limit 1";
	                    $res2   = $link->query($sql2);
	                    echo $sql2.'<br>';
	                    $fila2  = mysqli_fetch_array($res2);

	                    if($fila2['veh_id']!=''){
	                        $veh_id  = $fila2['veh_id'];
	                    }else{
	                    	if($patente!=''){
	                    		 $sql7   = "INSERT INTO vehiculos(veh_tipo, veh_cliente, veh_patente, veh_imei, veh_tservicio, veh_marca, veh_modelo) VALUES (".$tipoveh.",".$cuenta.",'".$patente."','',".$tipos.",'".$marca."','".$modelo."')";
	                            	$res7    = $link->query($sql7);
		                        echo $sql7.'<br>';
		                        $fila7   = mysqli_fetch_array($res7);
		                        $veh_id  = $link->insert_id;
	                    	}else{
	                    		$veh_id  = 0;
	                    	}
	                    }

	                    if($newDate=='1969-12-31'){
	                    	$newDate='2022-07-14';
	                    }

	                    $sql   = "insert into tickets (tic_fechahorareg,tic_cliente,tic_patente,tic_dispositivo,tic_tipotrabajo,tic_tiposervicio,tic_contacto,tic_celular,tic_descripcion,tic_estado) values ('{$newDate}',{$cuenta},{$veh_id},{$dispositivo},{$tipot},{$tipos},'{$contacto}','{$celular}','{$obs}',1)";
                        $res   = $link->query($sql);
                        echo $sql.'<br>';
                        $fila  = mysqli_fetch_array($res);
                        echo '-----------------------------------------------------'.'<br>';
				}						
			}
			$indices++;				
		}
}else{
	echo 'Debes enviar el proceso';
}

?>