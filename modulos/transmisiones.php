<?php 

	$optionclientes = '<option value="">Todos</option>';
	$optionvehiculos = '<option value="">Todas</option>';
	$optiontservicio = '<option value="">Todas</option><option value="0">Básica</option><option value="1">Avanzado</option>';

	$sql2 = "select * from usuarios where usu_id={$_SESSION["cloux_new"]}";
    $res2 = $link->query($sql2);
    $fila2 = mysqli_fetch_array($res2);
    if($fila2['usu_perfil']==null || $fila2['usu_perfil']=='null' || $fila2['usu_perfil']=='' || $fila2['usu_perfil']=='0'){
    	$sql3 = "UPDATE usuarios SET usu_perfil = 2 where usu_id={$_SESSION["cloux_new"]}";
    	$res3 = $link->query($sql3);
    }

    $linkGen = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD');
    if (mysqli_connect_errno()) {
        printf("Falló la conexión: %s\n", mysqli_connect_error());
        exit();
    }
    $devuelve = array();

	if( false ){
		$rs = $linkGen->query('SHOW DATABASES;');
		while($row = mysqli_fetch_array($rs)){
			if($row[0]!='' && $row[0]!=null){
				if(trim($row[0])!='cloux' && trim($row[0])!='mysql' && trim($row[0])!='information_schema' && trim($row[0])!='performance_schema' && trim($row[0])!='prueba_data'){
	
					$linkclient = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD',strtolower($row[0]));
					if (mysqli_connect_errno()) {
						printf("Falló la conexión: %s\n", mysqli_connect_error());
						exit();
					}
	
					/*$sql = "SELECT 
							t1.veh_id, 
							t1.veh_patente, 
							t2.ulp_odometrocan, 
							t2.ulp_odolitroscan, 
							t1.veh_tiposerv, 
							t2.ulp_fechahora,
							TIMESTAMPDIFF(DAY, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) AS dias_transcurridos
						FROM 
							vehiculos t1
						INNER JOIN 
							ultimaposicion t2 ON t2.ulp_idveh = t1.veh_id
						LEFT OUTER JOIN 
							tipo_vehiculo t3 ON t3.tveh_id = t1.veh_tipoveh
						WHERE 
							TIMESTAMPDIFF(HOUR, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) >= 72";
					$res = $linkclient->query($sql);
	 
					if(mysqli_num_rows($res)>0){
						   
						foreach($res as $key=>$ulp){
							$optionvehiculos .= '<option value="'.$ulp['veh_patente'].'">'.$ulp['veh_patente'].'</option>';
						}
					}*/
	
					$optionclientes .= '<option value="'.strtolower($row[0]).'">'.strtolower($row[0]).'</option>';
				}
			}
		}
	}else{
		$link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
		if (mysqli_connect_errno()) {
			printf("Falló la conexión: %s\n", mysqli_connect_error());
			exit();
		}

		$query = "SELECT DISTINCT cuenta FROM clientes as c WHERE c.cli_estadows = 1 ORDER BY c.cuenta ; ";

      	$res = $link->query($query);

		if($res){
			while($row = mysqli_fetch_array($res)){
				if($row[0]!='' && $row[0]!=null){
					$optionclientes .= '<option value="'.strtolower($row[0]).'">'.strtolower($row[0]).'</option>';
				}
			}
		}

	}
    


?>

<style>
.switchcustom {
  display: inline-block;
  height: 28px;
  position: relative;
  width: 50px;
  margin-bottom: 0;
}

.switchcustom input {
  display:none;
}

.slidercustom {
  background-color: #ccc;
  bottom: 0;
  cursor: pointer;
  left: 0;
  position: absolute;
  right: 0;
  top: 0;
  transition: .4s;
  border-radius: 20px;
}

.slidercustom:before {
  background-color: #fff;
  bottom: 3px;
  content: "";
  height: 22px;
  left: 4px;
  position: absolute;
  transition: .4s;
  width: 22px;
  border-radius: 34px;
}

input:checked + .slidercustom {
  background-color: #66bb6a;
}

input:checked + .slidercustom:before {
  transform: translateX(22px);
}

.slidercustom .roundcustom {
  border-radius: 34px;
}

.slidercustom .roundcustom:before {
  border-radius: 50%;
}

.form-check-input-lg {
    transform: scale(1.5); /* Aumenta el tamaño del checkbox */
    margin-right: 10px; /* Agrega un margen a la derecha del checkbox */
}
</style>
<!-- modal -->
<div class="modal" id="mlistclientes">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<table id="tblusu" class="table table-bordered table-striped table-condensed table-sm">
					<thead class="thead-dark">
						<tr align="center">
							<th></th>
							<th>Comentario</th>
							<th>Fecha</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- fin modal -->

<div class="content">
<div class="card">
    <!-- <div class="card-header p-2">
        <ul class="nav nav-pills" id="myTab">
            <li class="nav-item"><a id="navguia" class="nav-link active" href="#listaclientes"  data-toggle="tab">Lista de Clientes</a></li>
           <li class="nav-item"><a id="navlistguia" class="nav-link" href="#crearcliente"  data-toggle="tab">Crear Cliente</a></li> 
        </ul>
    </div> -->
    <div class="row p-0">
        <div class="" style="width:25%; padding: 10px; cursor: pointer;" onclick="getTabTickets(2)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Total">
                <span class="info-box-icon elevation-1" style="font-size:1.4rem;background-color:#7100FF;color:white;">
                    <i class="fa fa-plug" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                        Total
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="cardtotal">
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="" style="width:25%; padding: 10px; cursor: pointer;" onclick="getTabTickets(3)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Gestionados">
                <span class="info-box-icon bg-secondary elevation-1" style="font-size:1.4rem;">
                    <i class="fa fa-power-off" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                         Gestionados
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="carddgestionados">
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="" style="width:25%; padding: 10px; cursor: pointer;" onclick="getTabTickets(6)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Pendientes">
                <span class="info-box-icon bg-primary elevation-1" style="font-size:1.4rem;">
                    <i class="fa fa-wrench" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                        Pendientes
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="cardpendientes" >
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>       
    </div>

    <div class="card-body">


    	<div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header p-0" style="background-color:#7058c3;color:white;">
                    <ul class="nav nav-tabs pl-1" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item" style="margin-top:2px;">
                            <a class="nav-link active" style="padding:1px 4px;border-right:1px solid white;" id="transmisiones-tab"
                               data-toggle="pill" href="#transmisiones" role="tab" aria-controls="transmisiones" aria-selected="true">
                                Transmisiones
                            </a>
                        </li>
                        <li class="nav-item" style="margin-top:2px;">
                            <a class="nav-link" style="color:white;padding:1px 4px;border-right:1px solid white;" id="telemetria-tab"
                               data-toggle="pill" href="#telemetria" role="tab" aria-controls="telemetria" aria-selected="true">
                                Telemetría
                            </a>
                        </li>

                        <li class="nav-item" style="margin-top:2px;">
                            <a class="nav-link" style="color:white;padding:1px 4px;border-right:1px solid white;" id="transmision_nuevo-tab"
                               data-toggle="pill" href="#transmision_nuevo" role="tab" aria-controls="transmisiones_nuevo" aria-selected="true">
                                Transmisiones Nuevo
                            </a>
                        </li>

                    </ul>
                </div>
                <div class="card-body">

                    <div class="tab-content" id="custom-tabs-one-tabContent">

                        <div class="tab-pane fade show active" id="transmisiones" role="tabpanel" aria-labelledby="transmisiones-tab">
                            <div class="row">
                            	<div class="col-md-3">
						            <div class="form-group row mb-0">
						                <label class="col-sm-3 col-form-label" for="clientefil">Cliente</label>
						                <div class="col-sm-7 pt-2">
						                    <select id="clientefil" onchange="getTabClientes()"><?php echo $optionclientes?>  </select> 
						                          	
						                </div>
						            </div>
						        </div>
						        <div class="col-md-3">
						            <div class="form-group row mb-0">
						                <label class="col-sm-3 col-form-label" for="patentefil">Patente</label>
						                <div class="col-sm-7 pt-2">
						                   	<select id="patentefil" ><option value="">Seleccione</option></select>
						                   	  
						                </div>
						            </div>
						        </div>
						        <div class="col-md-3">
						            <div class="form-group row mb-0">
						                <label class="col-sm-3 col-form-label" for="tserfil">Tipo Servicio</label>
						                <div class="col-sm-7 pt-2">
						                   	<select id="tserfil" onchange="getTabClientes()"><?php echo $optiontservicio?>  </select>
						                   	 
						                </div>
						            </div>
						        </div>
						        <div class="col-md-2">
						            <div class="form-group row mb-0">
						                <label class="col-sm-3 col-form-label" for="diasfil">Dias</label>
						                <div class="col-sm-7 pt-2">
						                   	<input type="text" name="" id="diasfil" onchange="getTabClientes()" class="form-control form-control-sm">
						                </div>
						            </div>
						        </div>
						        <div class="col-md-2">
						            <div class="form-group row mb-0">
						                <button class="btn btn-danger" onclick="limpia()">Limpiar</button>
						                <button class="btn btn-success" onclick="exportar('tbclientesfiltro2')">Exportar</button>
						            </div>
						        </div>
                            </div>
                            <div class="row">
                            	<table class="table table-bordered table-striped table-condensed table-sm" id="tbclientesfiltro">
									<thead class="thead-dark">
										<th style="text-align:center;">N°</th>
										<th style="text-align:center;">Cliente</th>
										<th style="text-align:center;">Razon Social</th>
										<th style="text-align:center;">Patente</th>
										<th style="text-align:center;">Tipo Servicio</th>
										<th style="text-align:center;">Ult. Transmision</th>
										<th style="text-align:center;">Días</th>
										<!-- <th style="text-align:center;">Ult. Odocan</th>
										<th style="text-align:center;">Ult. Odolitro</th> -->
										<th style="text-align:center;">Comentarios</th>
										<th style="text-align:center;">Fecha/Hora</th>
										<th style="text-align:center;">Ultimo comentario</th>
										<th style="text-align:center;">Días Gestion</th>
										<th style="text-align:center;">Estado</th>
									</thead>
									<tbody id="tab_clientes">
										<tr><td colspan="12" align="center"><i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i></td></tr>
									</tbody>
								</table>

								<table class="table table-bordered table-striped table-condensed table-sm" id="tbclientesfiltro2" style="display: none;">
									<thead class="thead-dark">
										<th style="text-align:center;">N°</th>
										<th style="text-align:center;">Cliente</th>
										<th style="text-align:center;">Razon Social</th>
										<th style="text-align:center;">Patente</th>
										<th style="text-align:center;">Tipo Servicio</th>
										<th style="text-align:center;">Ult. Transmision</th>
										<th style="text-align:center;">Días</th>
										<!-- <th style="text-align:center;">Ult. Odocan</th>
										<th style="text-align:center;">Ult. Odolitro</th> -->
										<th style="text-align:center;">Comentarios</th>
										<th style="text-align:center;">Fecha/Hora</th>
										<th style="text-align:center;">Ultimo comentario</th>
										<th style="text-align:center;">Días Gestion</th>
										<th style="text-align:center;">Estado</th>
									</thead>
									<tbody id="tab_clientes2">
										
									</tbody>
								</table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="telemetria" role="tabpanel" aria-labelledby="telemetria-tab">
                            <div class="row">
                            	
                            		<div class="col-md-4">
							            <div class="form-group row mb-0">
							                <label class="col-sm-3 col-form-label" for="clientefil2">Cliente</label>
							                <div class="col-sm-7 pt-2">
							                    <select id="clientefil2" onchange="getTabtelemetria()"><?php echo $optionclientes?>  </select> 
							                          	
							                </div>
							            </div>
							        </div>
							        <div class="col-md-4">
							            <div class="form-group row mb-0">
							                <label class="col-sm-3 col-form-label" for="patentefil2">Patente</label>
							                <div class="col-sm-7 pt-2">
							                   	<select id="patentefil2" ><option value="">Seleccione</option></select>
							                   	  
							                </div>
							            </div>
							        </div>
							        <div class="col-md-4">
							            <div class="form-group row mb-0">
							                <button class="btn btn-danger" onclick="limpia(2)">Limpiar</button>
							                <button class="btn btn-success" onclick="exportar('tbclientesfiltro2tel')">Exportar</button>
							            </div>
							        </div>
                           
                            	
                            </div>
                            <div class="row">
                            	<div class="col-md-12">
                            		<table class="table table-bordered table-striped table-condensed table-sm" id="tbclientesfiltrotel" style="width: 100%;">
										<thead class="thead-dark">
											<th style="text-align:center;">N°</th>
											<th style="text-align:center;">Cliente</th>
											<th style="text-align:center;">Razon Social</th>
											<th style="text-align:center;">Patente</th>
											<th style="text-align:center;">Odometro</th>
											<th style="text-align:center;">Odolitro</th>
											<th style="text-align:center;">Comentarios</th>
											<th style="text-align:center;">Fecha/Hora</th>
											<th style="text-align:center;">Ultimo comentario</th>
											<th style="text-align:center;">Días Gestion</th>
											<th style="text-align:center;">Estado</th>
										</thead>
										<tbody id="tab_clientestel">
											<tr><td colspan="12" align="center"><i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i></td></tr>
										</tbody>
									</table>

									<table class="table table-bordered table-striped table-condensed table-sm" id="tbclientesfiltro2tel" style="display: none;width: 100%;">
										<thead class="thead-dark">
											<th style="text-align:center;">N°</th>
											<th style="text-align:center;">Cliente</th>
											<th style="text-align:center;">Razon Social</th>
											<th style="text-align:center;">Patente</th>
											<th style="text-align:center;">Odometro</th>
											<th style="text-align:center;">Odolitro</th>
											<th style="text-align:center;">Comentarios</th>
											<th style="text-align:center;">Fecha/Hora</th>
											<th style="text-align:center;">Ultimo comentario</th>
											<th style="text-align:center;">Días Gestion</th>
											<th style="text-align:center;">Estado</th>
										</thead>
										<tbody id="tab_clientes2tel">
											
										</tbody>
									</table>
                            	</div>
                            	
                            </div>
                        </div>

                        <div class="tab-pane fade" id="transmision_nuevo" role="tabpanel" aria-labelledby="transmision_nuevo-tab">
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group row mb-0">
                                        <label class="col-sm-3 col-form-label" for="clientefil3">Cliente</label>
                                        <div class="col-sm-7 pt-2">
                                            <select id="clientefil3" onchange="getTabtelemetriaNuevo()"><?php echo $optionclientes?>  </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group row mb-0">
                                        <label class="col-sm-3 col-form-label" for="patentefil3">Patente</label>
                                        <div class="col-sm-7 pt-2">
                                            <select id="patentefil3" ><option value="">Seleccione</option></select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group row mb-0">
                                        <button class="btn btn-danger" onclick1="limpia(2)">Limpiar</button>
                                        <button class="btn btn-success" onclick1="exportar('tbclientesfiltro2tel')">Exportar</button>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped table-condensed table-sm" id="tbclientesfiltrotel2" style="width: 100%;">

										<thead class="thead-dark">
											<th style="text-align:center;">N°</th>
											<th style="text-align:center;">Cliente</th>
											<th style="text-align:center;">Razon Social</th>
											<th style="text-align:center;">Patente</th>
											<th style="text-align:center;">Tipo Servicio</th>
											<th style="text-align:center;">Ult. Transmision</th>
											<th style="text-align:center;">Días</th>
											<th style="text-align:center;">Ult. Transmision Cliente</th>
											<th style="text-align:center;">Días Cliente</th>
											<!-- <th style="text-align:center;">Ult. Odocan</th>
											<th style="text-align:center;">Ult. Odolitro</th> -->
											<th style="text-align:center;">Comentarios</th>
											<th style="text-align:center;">Fecha/Hora</th>
											<th style="text-align:center;">Ultimo comentario</th>
											<th style="text-align:center;">Días Gestion</th>
											<th style="text-align:center;">Estado</th>
										</thead>
										
                                        <tbody></tbody>

                                        <!-- Este es un comentario HTML -->
                                        <!--<thead class="thead-dark">
                                        <th style="text-align:center;">N°</th>
                                        <th style="text-align:center;">Cliente</th>
                                        <th style="text-align:center;">Razon Social</th>
                                        <th style="text-align:center;">Patente</th>
                                        <th style="text-align:center;">Odometro</th>
                                        <th style="text-align:center;">Odolitro</th>
                                        <th style="text-align:center;">Comentarios</th>
                                        <th style="text-align:center;">Fecha/Hora</th>
                                        <th style="text-align:center;">Ultimo comentario</th>
                                        <th style="text-align:center;">Días Gestion</th>
                                        <th style="text-align:center;">Estado</th>
                                        </thead>
                                        <tbody id="tab_clientestel">
                                        <tr><td colspan="12" align="center"><i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i></td></tr>
                                        </tbody>-->
                                    </table>

                                    <table class="table table-bordered table-striped table-condensed table-sm" id="tbclientesfiltro2tel2" style="display: none;width: 100%;">
                                        <thead class="thead-dark">
                                        <th style="text-align:center;">N°</th>
                                        <th style="text-align:center;">Cliente</th>
                                        <th style="text-align:center;">Razon Social</th>
                                        <th style="text-align:center;">Patente</th>
                                        <th style="text-align:center;">Odometro</th>
                                        <th style="text-align:center;">Odolitro</th>
                                        <th style="text-align:center;">Comentarios</th>
                                        <th style="text-align:center;">Fecha/Hora</th>
                                        <th style="text-align:center;">Ultimo comentario</th>
                                        <th style="text-align:center;">Días Gestion</th>
                                        <th style="text-align:center;">Estado</th>
                                        </thead>
                                        <tbody id="tab_clientes2tel">

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="tab-content">
			<!-- <div class="active tab-pane" id="listaclientes">
			<div class="row top20" id="tb_listadoclientes"> -->
				<!-- <div class="col-md-12">
					<div class="box box-inverse box-solid">
						<div class="box-body">
							

						</div>
					</div>
				</div> -->
			<!-- </div>
			</div> -->
			<div class="tab-pane" id="crearcliente">
			<div class="row top20" id="form_editarcliente">
<div class="col-md-12">
<div class="box box-warning box-solid">
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal">
<input type="hidden" name="operacion" id="formoperacion" value="nuevocliente"/>
<!-- <input type="hidden" name="operacion" value="editarcliente"/> -->
<input type="hidden" name="idcliente"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="row">
	<div class="col-md-6">
		<div class="form-group row">
			<label for="rut" class="col-sm-4 col-form-label">Rut cliente</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="rut" id="rut" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="cuenta" class="col-sm-4 col-form-label">Cuenta</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="cuenta" id="cuenta" value="">
			</div>
			<div class="col-sm-12 oculto" id="cliente_existe"></div>
		</div>
		<div class="form-group row">
			<label for="razonsocial" class="col-sm-4 col-form-label">Razon Social</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="razonsocial" id="razonsocial" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="rlegal" class="col-sm-4 col-form-label">Representante legal</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="rlegal" id="rlegal" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="rrut" class="col-sm-4 col-form-label">Rut representante</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="rrut" id="rrut" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="rrut" class="col-sm-4 col-form-label">Región</label>
			<div class="col-sm-8">
			<?=htmlselect('region','region','regiones','id','region|ordinal','','','','id','getComunas()','','si','no','no');?>
			</div>
		</div>
		<div class="form-group row">
			<label for="comuna" class="col-sm-4 col-form-label">Comuna</label>
			<div class="col-sm-8">
			<select name="comuna" id="comuna" class="form-control"></select>
			<div class="oculto col-sm-1 text-green padtop7 txtleft" id="loadaop"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i></div>
			</div>
		</div>
		<div class="form-group row">
			<label for="direccion" class="col-sm-4 col-form-label">Dirección</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="direccion" id="direccion" value="">
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group row">
			<label for="telefono" class="col-sm-4 col-form-label">Teléfono</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="telefono" id="telefono" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="correo" class="col-sm-4 col-form-label">Correo</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="correo" id="correo" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="giro" class="col-sm-4 col-form-label">Giro</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="giro" id="giro" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="nombre" class="col-sm-4 col-form-label">Nombre</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="nombre" id="nombre" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="usuariows" class="col-sm-4 col-form-label">Usuario WS</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="usuariows" id="usuariows" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="clavews" class="col-sm-4 col-form-label">Clave WS</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="clavews" id="clavews" value="">
			</div>
		</div>
		<div class="form-group row">
			<label for="nombrews" class="col-sm-4 col-form-label">Nombre WS</label>
			<div class="col-sm-8">
			<input type="text" class="form-control" name="nombrews" id="nombrews" value="">
			</div>
		</div>
	</div>
</div>

<div class="row">
<!-- formulario para agregar clientes -->
<div class="oculto top50" id="form_agregarcontacto">
<h3>Agregar Contacto</h3>
<hr>
<div class="col-md-12 form-inline">
<div class="form-group row">
	<label for="nombrecontacto" class="col-sm-2 col-form-label">Nombre</label>
	<div class="col-sm-10">
	<input type="text" class="form-control form-control-sm" name="nombrecontacto" id="nombrecontacto" value="">
	</div>
</div>
<div class="form-group row">
	<label for="telefonocontacto" class="col-sm-2 col-form-label">Teléfono</label>
	<div class="col-sm-10">
	<input type="text" class="form-control form-control-sm" name="telefonocontacto" id="telefonocontacto" value="">
	</div>
</div>
<div class="form-group row">
	<label for="correocontacto" class="col-sm-2 col-form-label">Correo</label>
	<div class="col-sm-10">
	<input type="text" class="form-control form-control-sm" name="correocontacto" id="correocontacto" value="">
	</div>
</div>
<div class="form-group row">
	<label for="correocontacto" class="col-sm-2 col-form-label">Cargo</label>
	<div class="col-sm-10">
	<input type="text" class="form-control form-control-sm" name="correocontacto" id="correocontacto" value="">
	</div>
</div>
<button type="button" class="btn btn-success btn-circle top25" onclick="addcontacto();"><i class="fa fa-plus" aria-hidden="true"></i></button>
<button type="button" class="btn btn-danger btn-circle top25" onclick="noagregarcontacto();"><i class="fa fa-times" aria-hidden="true"></i></button>
<div  id="inp_agregarcontactos">
</div>
</div>

<div class="col-md-12 mt-2">
	<table class="table table-bordered table-striped table-sm" id="tb_agregarcontacto">
		<thead class="thead-dark">
		<th>#</th>
		<th>Nombre</th>
		<th>Teléfono</th>
		<th>Correo</th>
		<th>Cargo</th>
		<th>&nbsp;</th>
		<tbody>
		</tbody>
	</table>
</div>
</div>

	
	<button type="submit" id="btncrearcliente" class="btn btn-success btn-rounded top25">Crear Cliente</button>&nbsp;&nbsp;&nbsp;&nbsp;
	<button type="button" class="btn btn-danger btn-rounded top25" onclick="CeditCliente()">Cancelar</button>&nbsp;&nbsp;&nbsp;&nbsp;
	<div id="btn_agregarcontacto"><button type="button" class="btn btn-success btn-rounded top25" onclick="AgregarContacto();"><i class="fa fa-address-card-o" aria-hidden="true"></i> Agregar Contacto</button></div>
		<!-- </div>
		<div class="col-sm-3 col-lg-3" id="btn_agregarcontacto">
			<button type="button" class="btn btn-success btn-rounded top25" onclick="AgregarContacto();"><i class="fa fa-address-card-o" aria-hidden="true"></i> Agregar Contacto</button>
		</div>
	</div> -->
</div>







</form>
</div>
</div>
</div>
</div>
			</div>
		</div>
	</div>
</div>




</div>

<script>


var jsonDatatable = {"sProcessing":     "Procesando...",	"sLengthMenu":     "Mostrar _MENU_ registros",	"sZeroRecords":    "No se encontraron resultados",	"sEmptyTable":     "Ningún dato disponible en esta tabla",	"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",	"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",	"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",	"sInfoPostFix":    "",	"sSearch":         "Buscar:",	"sUrl":            "",	"sInfoThousands":  ",",	"sLoadingRecords": "Cargando...",	"oPaginate": {		"sFirst":    "Primero",		"sLast":     "Último",		"sNext":     "Siguiente",		"sPrevious": "Anterior"},	"oAria": {		"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",		"sSortDescending": ": Activar para ordenar la columna de manera descendente"}};


$(function(){
	$("#clientefil").chosen({width:'100%'});
	$("#clientefil2").chosen({width:'100%'});
	$("#clientefil3").chosen({width:'100%'})
	$("#patentefil").chosen({width:'100%'});
	$("#tserfil").chosen({width:'100%'});
	getTabClientes();
	getTabtelemetria();
    getTabtelemetriaNuevo();
});

window.clientes;

var idtabs = ['transmisiones-tab', 'telemetria-tab', 'transmision_nuevo-tab']
$('#transmisiones-tab,#telemetria-tab,#transmision_nuevo-tab').on('click', function(items) {
    let idcap = this.id
    if(idcap=='telemetria-tab'){
    	$("#cardtotal").html(cantidadtotaltel);
		$("#carddgestionados").html(cantidadgestionadostel);
		$("#cardpendientes").html(cantidadpendientestel);
    }else{
    	$("#cardtotal").html(cantidadtotal);
		$("#carddgestionados").html(cantidadgestionados);
		$("#cardpendientes").html(cantidadpendientes);
    }

    $.each(idtabs, function(i, item) {
        if (idcap == item) {
            $('#' + item).css('color', '#495057')
        } else {
            $('#' + item).css('color', 'white')
        }
    });
})

let datos2 = [];
let cantidadtotaltel = 0;
let cantidadgestionadostel = 0;
let cantidadpendientestel = 0;

function getTabtelemetria(){
	if ($.fn.DataTable.isDataTable('#tbclientesfiltrotel')) {
        $('#tbclientesfiltrotel').DataTable().destroy();
    }

    if($("#clientefil2").val()!=''){
    	getvehiculoscliente($("#clientefil2").val(),'2')
    }else{
    	$("#patentefil2").chosen('destroy');
        $("#patentefil2").html('<option value="">Seleccione</option>');
        $("#patentefil2").chosen({width:'100%'});

        $("#patentefil2").attr('onchange','');
    }

    //console.log($("#patentefil2").val());
    //console.log($("#clientefil2").val());

	loadDatatable(); return;

	$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabClientessintele',cliente:$("#clientefil2").val(),datatable:false,patente:$("#patentefil2").val(),retornar:'no'},function(data){

		let fechaActual = new Date();

		// Obtener los componentes de la fecha
		let año = fechaActual.getFullYear();
		let mes = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
		let dia = fechaActual.getDate().toString().padStart(2, '0');
		let hora = fechaActual.getHours().toString().padStart(2, '0');
		let minutos = fechaActual.getMinutes().toString().padStart(2, '0');

		// Formatear la fecha en el formato deseado
		let fechaFormateada = `${año}-${mes}-${dia} ${hora}:${minutos}`;

		//console.log(data);
		datos2 = $.parseJSON(data);
		var filas = '';
		
		$.each(datos2,function(ind,row){
			var indice2 = 1;
			$.each(row.sintransmision,function(i,item){
				var disa = 'disabled';
				if(item.comentarios.length>0){
					disa = '';
					cantidadgestionadostel++;
				}else{
					cantidadpendientestel++;
					disa = 'disabled';
				}

				var dias = 0;
				var comentarioultimo = '';
				if(item.comentarios.length>0){
					var fecha1 = '';
				    fecha1 = item.comentarios[item.comentarios.length - 1].fecha;
				    const fechaProporcionada = new Date(fecha1);
				    const fechaActual = new Date();
				    const diferenciaMilisegundos = fechaActual - fechaProporcionada;
				    dias = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

				    comentarioultimo = item.comentarios[item.comentarios.length - 1].comentario+' / '+item.comentarios[item.comentarios.length - 1].fecha;
				}
				
				var selestado1 = '';
				var selestado2 = '';
				var selestado3 = '';
				if(item.estadoselectr==0){
					selestado1 = 'selected';
					selestado2 = '';
					selestado3 = '';
				}else if(item.estadoselectr==1){
					selestado1 = '';
					selestado2 = 'selected';
					selestado3 = '';
				}else{
					selestado1 = '';
					selestado2 = '';
					selestado3 = 'selected';
				}

				filas += `<tr>`+
							`<td style='text-align:center;'>`+indice2+`</td>`+
							`<td style='text-align:center;'>`+row.cliente+`</td>`+
							`<td style='text-align:center;'>`+item.rs+`</td>`+
							`<td style='text-align:center;' id='patente_`+item.patente+`'>`+item.patente+`</td>`+
							`<td style='text-align:center;'>`+item.maloodo+`</td>`+
							`<td style='text-align:center;'>`+item.malolts+`</td>`+
							`<td><div class="row"><div class="col-md-10"><textarea class='form-control' id='comentario2_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`'></textarea></div><div class="col-md-2"><button type="button" class="btn btn-sm btn-success" onclick='upddetras2("`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+row.cliente+`")'><i class="fas fa-plus"></i></button> <button id="eye2_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`" type="button" class="btn btn-sm btn-info" onclick='mostrarcomentarios2("`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+row.cliente+`")' `+disa+`><i class="fas fa-eye"></i></button></div></div></td>`+
							`<td style='text-align:center;'><input type='datetime-local' id='fecha2_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`' class='form-control-sm' value='`+fechaFormateada+`'></input></td>`+
							`<td style='text-align:center;' id="ult_comentario2`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`">`+comentarioultimo+`</td>`+
							`<td style='text-align:center;' id="ult_dias2`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`">`+dias+`</td>`+
							`<td style='text-align:center;'><select id='estado2_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`' class='estado2' onchange='updestado2("`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+row.cliente+`")'><option value='0' `+selestado1+`>No operativo</option><option value='1' `+selestado2+`>Operativo</option><option value='2' `+selestado3+`>Desinstalar</option></select></td>`+
						 `</tr>`;
				indice2 ++;
				cantidadtotaltel++;
				
			});
		});

		$("#cardtotal").html(cantidadtotaltel);
		$("#carddgestionados").html(cantidadgestionadostel);
		$("#cardpendientes").html(cantidadpendientestel);
		$("#tbclientesfiltrotel tbody").html(filas);
		$("#tbclientesfiltro2tel tbody").html(filas);
		$(".estado2").chosen({width:'100%'});
		
		$('#tbclientesfiltrotel').DataTable({
		    "language": jsonDatatable,//{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json' },
		    "paging": true,
		    "lengthChange": true,
		    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "Todos"]],
		    "pageLength": 25,  // Mostrar 100 registros por página por defecto
		    "searching": true,
		    "ordering": false,  // Desactivar orden al principio
		    "info": true,
		    "autoWidth": false,
		    "order": [],  // No realizar ningún orden al principio
		});

	});
}

function getTabtelemetriaNuevo(){
    if ($.fn.DataTable.isDataTable('#tbclientesfiltrotel2')) {
        $('#tbclientesfiltrotel').DataTable().destroy();
    }

    if($("#clientefil3").val()!=''){
        getvehiculoscliente($("#clientefil3").val(),'3',1)
    }else{
        $("#patentefil3").chosen('destroy');
        $("#patentefil3").html('<option value="">Seleccione</option>');
        $("#patentefil3").chosen({width:'100%'});

        $("#patentefil3").attr('onchange','');
    }

    if($.fn.DataTable.isDataTable('#tbl_bodegas')){
        $('#tbl_bodegas').DataTable().destroy();
    }

	return true;
    $('#tbclientesfiltrotel2').DataTable({
        ajax: 'operaciones.php?numero='+Math.floor(Math.random()*9999999)+'&operacion=listarVehiculosAll&retornar=no',
        processing: true,
        serverSide: true,
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json',
            thousands: '.' // Usar punto como separador de miles
        },
        "paging": true,
        "lengthChange": true,
        "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
        "pageLength": 25,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        columns: [
            { data: 'id_vehiculo', orderable: false,"defaultContent": "" },
			{ data: 'empresa',"defaultContent": "" },
            { data: 'rstransportista',"defaultContent": "" },
            { data: 'patente',"defaultContent": "" },
            { data: 'tiposervicio',"defaultContent": "" },
            { data: 'fh_dato',"defaultContent": "" },
            { data: 'dias_pasados',"defaultContent": "" },
            { data: 'ulttransmision',"defaultContent": "" },
			{ data: 'dias',"defaultContent": "" },
			{ data: '',"defaultContent": "" },
			{ data: '',"defaultContent": "" },
			{ data: '',"defaultContent": "" },
        ],
    });

    //console.log($("#patentefil2").val());
    //console.log($("#clientefil2").val());
    return;
    loadDatatable();

    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabClientessintele',cliente:$("#clientefil2").val(),datatable:false,patente:$("#patentefil2").val(),retornar:'no'},function(data){

        let fechaActual = new Date();

        // Obtener los componentes de la fecha
        let año = fechaActual.getFullYear();
        let mes = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
        let dia = fechaActual.getDate().toString().padStart(2, '0');
        let hora = fechaActual.getHours().toString().padStart(2, '0');
        let minutos = fechaActual.getMinutes().toString().padStart(2, '0');

        // Formatear la fecha en el formato deseado
        let fechaFormateada = `${año}-${mes}-${dia} ${hora}:${minutos}`;

        //console.log(data);
        datos2 = $.parseJSON(data);
        var filas = '';

        $.each(datos2,function(ind,row){
            var indice2 = 1;
            $.each(row.sintransmision,function(i,item){
                var disa = 'disabled';
                if(item.comentarios.length>0){
                    disa = '';
                    cantidadgestionadostel++;
                }else{
                    cantidadpendientestel++;
                    disa = 'disabled';
                }

                var dias = 0;
                var comentarioultimo = '';
                if(item.comentarios.length>0){
                    var fecha1 = '';
                    fecha1 = item.comentarios[item.comentarios.length - 1].fecha;
                    const fechaProporcionada = new Date(fecha1);
                    const fechaActual = new Date();
                    const diferenciaMilisegundos = fechaActual - fechaProporcionada;
                    dias = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

                    comentarioultimo = item.comentarios[item.comentarios.length - 1].comentario+' / '+item.comentarios[item.comentarios.length - 1].fecha;
                }

                var selestado1 = '';
                var selestado2 = '';
                var selestado3 = '';
                if(item.estadoselectr==0){
                    selestado1 = 'selected';
                    selestado2 = '';
                    selestado3 = '';
                }else if(item.estadoselectr==1){
                    selestado1 = '';
                    selestado2 = 'selected';
                    selestado3 = '';
                }else{
                    selestado1 = '';
                    selestado2 = '';
                    selestado3 = 'selected';
                }

                filas += `<tr>`+
                    `<td style='text-align:center;'>`+indice2+`</td>`+
                    `<td style='text-align:center;'>`+row.cliente+`</td>`+
                    `<td style='text-align:center;'>`+item.rs+`</td>`+
                    `<td style='text-align:center;' id='patente_`+item.patente+`'>`+item.patente+`</td>`+
                    `<td style='text-align:center;'>`+item.maloodo+`</td>`+
                    `<td style='text-align:center;'>`+item.malolts+`</td>`+
                    `<td><div class="row"><div class="col-md-10"><textarea class='form-control' id='comentario2_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`'></textarea></div><div class="col-md-2"><button type="button" class="btn btn-sm btn-success" onclick='upddetras2("`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+row.cliente+`")'><i class="fas fa-plus"></i></button> <button id="eye2_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`" type="button" class="btn btn-sm btn-info" onclick='mostrarcomentarios2("`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+row.cliente+`")' `+disa+`><i class="fas fa-eye"></i></button></div></div></td>`+
                    `<td style='text-align:center;'><input type='datetime-local' id='fecha2_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`' class='form-control-sm' value='`+fechaFormateada+`'></input></td>`+
                    `<td style='text-align:center;' id="ult_comentario2`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`">`+comentarioultimo+`</td>`+
                    `<td style='text-align:center;' id="ult_dias2`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`">`+dias+`</td>`+
                    `<td style='text-align:center;'><select id='estado2_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`' class='estado2' onchange='updestado2("`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+row.cliente+`")'><option value='0' `+selestado1+`>No operativo</option><option value='1' `+selestado2+`>Operativo</option><option value='2' `+selestado3+`>Desinstalar</option></select></td>`+
                    `</tr>`;
                indice2 ++;
                cantidadtotaltel++;

            });
        });

        $("#cardtotal").html(cantidadtotaltel);
        $("#carddgestionados").html(cantidadgestionadostel);
        $("#cardpendientes").html(cantidadpendientestel);
        $("#tbclientesfiltrotel tbody").html(filas);
        $("#tbclientesfiltro2tel tbody").html(filas);
        $(".estado2").chosen({width:'100%'});

        $('#tbclientesfiltrotel').DataTable({
            "language": jsonDatatable,//{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json' },
            "paging": true,
            "lengthChange": true,
            "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "Todos"]],
            "pageLength": 25,  // Mostrar 100 registros por página por defecto
            "searching": true,
            "ordering": false,  // Desactivar orden al principio
            "info": true,
            "autoWidth": false,
            "order": [],  // No realizar ningún orden al principio
        });

    });
}

function mostrarcomentarios2(patente='',cliente=''){
	$("#mlistclientes").modal('show');
	var fila = ``;
	let cli = datos2.filter(cl => cl.cliente == cliente)
    if (cli.length > 0) {
	    $.each(cli[0].sintransmision,function(i,row){
	    	if(patente.replaceAll("_","(").replaceAll("-",")").replaceAll("á","/")==row.patente.replaceAll(" ","")){
	    		$.each(row.comentarios,function(ii,rowi){
	    			if(rowi.eliminado==0){
	    				fila += `<tr id="trcom_`+rowi.id+`">`+
			    				`<td><button type="button" class="btn btn-sm btn-danger" onclick='delcoment2("`+rowi.id+`","`+row.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+cli[0].cliente+`")'><i class="fas fa-trash"></i></button> <button type="button" class="btn btn-sm btn-success" onclick='editcoment2("`+rowi.id+`","`+row.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+cli[0].cliente+`")'><i class="fas fa-check"></i></button></td>`+
				    			`<td><textarea id="textarea2_`+rowi.id+`">`+rowi.comentario+`</textarea></td>`+
				    			`<td id="segunfech2_`+rowi.id+`">`+rowi.fecha+`</td>`+
				    		`</tr>`;
	    			}
	    		})
	    	}
	    });
    }
    $("#tblusu tbody").html(fila);
}

function editcoment2(id,patente='',cliente=''){
	if(($("#comentario2_"+id).val()=='' || $("#textarea2_"+id).val()==undefined)){
		toastr.error('Debes ingresar un comentario para editar');
		return;
	}

	let fechaActual = new Date();

	// Obtener los componentes de la fecha
	let año = fechaActual.getFullYear();
	let mes = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
	let dia = fechaActual.getDate().toString().padStart(2, '0');
	let hora = fechaActual.getHours().toString().padStart(2, '0');
	let minutos = fechaActual.getMinutes().toString().padStart(2, '0');

	// Formatear la fecha en el formato deseado
	let fechaFormateada = `${año}-${mes}-${dia} ${hora}:${minutos}`;

	var env   = {'id':id,'patente':patente,'comentario':$("#textarea2_"+id).val(),'fecha':fechaFormateada};
    var send  = JSON.stringify(env);

    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'editcoment2',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(res) {
               
        },error   : function(res) {
            
        },success : function(res) {
        	if(res.respuesta=='success'){
        		toastr.success(res.mensaje);

        		
        		let cli = datos2.filter(cl => cl.cliente == cliente)
				if (cli.length > 0) {
					$.each(cli[0].sintransmision,function(i,row){
						if(patente.replaceAll("_","(").replaceAll("-",")").replaceAll("á","/")==row.patente.replaceAll(" ","")){
						   	$.each(row.comentarios,function(ii,rowi){
						    	if(rowi.id==id){
						    		rowi.comentario = $("#textarea2_"+id).val();
						    		rowi.fecha = fechaFormateada;

						    		var fecha1 = '';
								    fecha1 = fechaFormateada;
								    const fechaProporcionada = new Date(fecha1);
								    const fechaActual = new Date();
								    const diferenciaMilisegundos = fechaActual - fechaProporcionada;
								    var dias = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

								    $("#segunfech2_"+id).text(fechaFormateada)
								   
						    		$("#ult_comentario2"+patente).text($("#textarea2_"+id).val()+' / '+fechaFormateada)
						    		$("#ult_dias2"+patente).text(dias)
						    	}
						   	})
						}
					});
				}
        	}else{
        		toastr.error(res.mensaje);
        	}
        }
    });
}

function delcoment2 (id,patente='',cliente=''){
    Swal.fire({
        title: '\u00BFEstas seguro de eliminarlo?',
        text: "Este desaparecera de la lista",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar'
    }).then((result) => {
        if (result.isConfirmed){
            env       = {'id':id};
            var send  = JSON.stringify(env);
            $.ajax({
                url       : 'operaciones.php',
                data      : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'delcoment2',retornar:'no',envio:send},
                type      : 'post',
                dataType  : 'json',
                beforeSend: function(res) {

                },error   : function(res) {
                        console.log(res);
                },success : function(res) {
                    console.log(res);
                    if(res.respuesta=='success'){
                        toastr.success(res.mensaje);
                        $('#trcom_'+id).remove();
                 		
                 		let cli = datos2.filter(cl => cl.cliente == cliente)
					    if (cli.length > 0) {
						    $.each(cli[0].sintransmision,function(i,row){
						    	if(patente==row.patente){
						    		$.each(row.comentarios,function(ii,rowi){
						    			if(rowi.id==id){
						    				rowi.eliminado = 1;
						    			}
						    		})

						    		var nuecome = '';
						    		var nuefecha = '';
						    		$.each(row.comentarios,function(ii,rowi){
						    			if(rowi.eliminado==0){
						    				nuecome = rowi.comentario;
						    				nuefecha = rowi.fecha;
						    			}
						    		})
						    		$("#ult_comentario2"+patente).text(nuecome+' / '+nuefecha)

						    		var fecha1 = '';
								    fecha1 = nuefecha;
								    const fechaProporcionada = new Date(fecha1);
								    const fechaActual = new Date();
								    const diferenciaMilisegundos = fechaActual - fechaProporcionada;
								    var dias = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24));
								    $("#ult_dias2"+patente).text(dias)
						    	}
						    });
					    }

                    }else{
                        toastr.error(res.mensaje);
                    }
                }
            });
        }
    }) 
}

function upddetras2(patente='',cliente=''){
	if(($("#comentario2_"+patente).val()=='' || $("#comentario2_"+patente).val()==undefined) || ($("#fecha2_"+patente).val()=='' || $("#fecha2_"+patente).val()==undefined)){
		toastr.error('Debes ingresar un comentario y fecha para guardar');
		return;
	}
	var env   = {'patente':patente,'comentario':$("#comentario2_"+patente).val(),'fecha':$("#fecha2_"+patente).val()};
    var send  = JSON.stringify(env);

    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'upddetras2',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(res) {
               
        },error   : function(res) {
            
        },success : function(res) {
        	if(res.respuesta=='success'){
        		toastr.success(res.mensaje);
        		var fech = '';
        		let cli = datos2.filter(cl => cl.cliente == cliente)
        		if (cli.length > 0) {
        			$.each(cli[0].sintransmision,function(i,row){
        				if(row.patente.replaceAll(" ","")==patente.replaceAll("_","(").replaceAll("-",")").replaceAll("á","/")){

        					const fechaObjeto = new Date($("#fecha2_"+patente).val());
							const año = fechaObjeto.getFullYear();
							const mes = (fechaObjeto.getMonth() + 1).toString().padStart(2, '0'); // Meses van de 0 a 11
							const dia = fechaObjeto.getDate().toString().padStart(2, '0');
							const hora = fechaObjeto.getHours().toString().padStart(2, '0');
							const minutos = fechaObjeto.getMinutes().toString().padStart(2, '0');
							const segundos = fechaObjeto.getSeconds().toString().padStart(2, '0');
							const fechaFormateada = `${año}-${mes}-${dia} ${hora}:${minutos}:${segundos}`;
							fech = fechaFormateada;
        					row.comentarios.push({'comentario':$("#comentario2_"+patente).val(),'fecha':fechaFormateada,'eliminado':0,'id':res.idnuevo})
        				}
        			});
        		}

        		
        		$("#ult_comentario2"+patente).text($("#comentario2_"+patente).val()+' / '+fech)

        		var fecha1 = '';
				fecha1 = fech;
				const fechaProporcionada = new Date(fecha1);
				const fechaActual = new Date();
				const diferenciaMilisegundos = fechaActual - fechaProporcionada;
				var dias = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

        		$("#ult_dias2"+patente).text(dias)
        		$("#eye2_"+patente).attr('disabled',false)
        		$("#comentario2_"+patente).val('')
        		
        		/*$("#fecha_"+patente).val('')*/
        	}else{
        		toastr.error(res.mensaje);
        	}
        }
    });
}

function updestado2 (patente='',cliente=''){
    Swal.fire({
        title: '\u00BFEstas seguro de cambiar el estado?',
        text: "Si pones operativo este creara un ticket automatico, pero si pones no operativos el ticket seguira creado",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar'
    }).then((result) => {
        if (result.isConfirmed){

        	var tservicio = 0;
        	var tservicioticket = 0;
        	var rut = '';
        	var rs = '';
        	var imei = '';
        	let cli = datos2.filter(cl => cl.cliente == cliente)
			if (cli.length > 0) {
				$.each(cli[0].sintransmision,function(i,row){
					if(patente==row.patente){
						if(row.tservicio=='Avanzado'){
							tservicio = 1;
							tservicioticket = 2;
						}else if(row.tservicio=='Thermo'){
							tservicio = 3;
							tservicioticket = 3;
						}else{
							tservicio = 0;
							tservicioticket = 1;
						}

						rut = row.ruttransportista;
        				rs = row.rstransportista;
        				imei = row.imei;
					}
				});
			}

            env       = {'imei':imei,'rut':rut,'rs':rs,'tservicio':tservicio,'tservicioticket':tservicioticket,'patente':patente,'cliente':cliente,'estado':$("#estado2_"+patente).val()};
			console.log(env);
            var send  = JSON.stringify(env);
            $.ajax({
                url       : 'operaciones.php',
                data      : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'updestado2',retornar:'no',envio:send},
                type      : 'post',
                dataType  : 'json',
                beforeSend: function(res) {

                },error   : function(res) {
                        console.log(res);
                },success : function(res) {
                    console.log(res);
                    if(res.respuesta=='success'){
                        toastr.success(res.mensaje);
                    }else{
                        toastr.error(res.mensaje);
                    }
                }
            });
        }
    }) 
}

function limpia(opc=''){
	$("#clientefil"+opc).val('').trigger('chosen:updated')
	$("#patentefil"+opc).val('').trigger('chosen:updated')
	$("#tserfil"+opc).val('').trigger('chosen:updated')
	$("#diasfil"+opc).val('')
	if(opc==''){
		getTabClientes()
	}else{
		getTabtelemetria();
	}
	
}

function exportar(idtabla='') {

	var nopmarchivo = 'Sin Transmisiones';
	if(idtabla=='tbclientesfiltro2tel'){
		nopmarchivo = 'Sin Telemetria';
	}
    $('#btnexportarexcel').addClass('disabled').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>').attr('onclick', null);
    let formulario = new FormData();

    var dataArray = [];
	var headersArray = [];

	// Obtener los encabezados de la tabla oculta
	$('#'+idtabla+' thead tr:eq(0) th').each(function(index) {
	    if (index !== 7) {
	        headersArray.push($(this).text());
	    }
	});
	dataArray.push(headersArray);

	// Obtener los datos de las filas de la tabla oculta
	$('#'+idtabla+' tbody tr').each(function(row, tr){
	    var rowArray = [];

	    // Obtener los datos de las celdas
	    $(tr).find('td').each(function(index, td){
	        if (index !== 7) {
	            var select = $(td).find('select');
	            var input = $(td).find('input');

	            if (select.length > 0) {
	                rowArray.push(select.find('option:selected').text());
	            } else if (input.length > 0) {
	                var inputValue = input.val().replace('T', ' ');

	                // Verificar si el contenido del input es una fecha y hora
	                var dateTimeRegex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;
	                if (dateTimeRegex.test(inputValue)) {
	                    // Formatear la fecha y hora sin usar Moment.js
	                    var date = inputValue.split(' ')[0].split('-').reverse().join('-');
	                    var time = inputValue.split(' ')[1];
	                    var formattedDateTime = date + ' ' + time;
	                    rowArray.push(formattedDateTime);
	                } else {
	                    rowArray.push(inputValue);
	                }
	            } else {
	                rowArray.push($(td).text());
	            }
	        }
	    });

	    dataArray.push(rowArray);
	});

	console.log(dataArray);

    formulario.append('operacion', 'exportarexcel')
    formulario.append('retornar', 'no')
    formulario.append('datos', JSON.stringify(dataArray))
    $.ajax({
        method: "POST",
        url: "operaciones.php",
        data: formulario,
        processData: false,
        contentType: false
    }).done(function(data) {
       /* $('#btnexportarexcel').removeClass('disabled').html('<i class="fas fa-file-excel"></i>').attr('onclick', 'exportEXCEL()');*/
        if (isJson(data)) {
            data = $.parseJSON(data);
            var $a = $("<a>");
            $a.attr("href", data.file);
            $("body").append($a);
            $a.attr("download", nopmarchivo+".xlsx");
            $a[0].click();
            $a.remove();
        } else {
            toastr.error('Error al generar Excel.')
        }
    }).fail(function(error) {
        /*$('#btnexportarexcel').removeClass('disabled').html('<i class="fas fa-file-excel"></i>').attr('onclick', 'exportEXCEL()');*/
    });
}

function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

let cantidadtotal = 0;
let cantidadgestionados = 0;
let cantidadpendientes = 0;

function getTabClientes(){
	if ($.fn.DataTable.isDataTable('#tbclientesfiltro')) {
        $('#tbclientesfiltro').DataTable().destroy();
    }

    if($("#clientefil").val()!=''){
    	getvehiculoscliente($("#clientefil").val())
    }else{
    	$("#patentefil").chosen('destroy');
        $("#patentefil").html('<option value="">Seleccione</option>');
        $("#patentefil").chosen({width:'100%'});

        $("#patentefil").attr('onchange','');
    }
	
	$.get("operaciones.php", {
				numero:''+Math.floor(Math.random()*9999999)+'',
				operacion:'getTabClientessintrans',
				cliente:$("#clientefil").val(),
				patente:$("#patentefil").val(),
				tservicio:$("#tserfil").val(),
				dias:$("#diasfil").val(),
				retornar:'no'},function(data)
				{
					
		let fechaActual = new Date();

		// Obtener los componentes de la fecha
		let año = fechaActual.getFullYear();
		let mes = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
		let dia = fechaActual.getDate().toString().padStart(2, '0');
		let hora = fechaActual.getHours().toString().padStart(2, '0');
		let minutos = fechaActual.getMinutes().toString().padStart(2, '0');

		// Formatear la fecha en el formato deseado
		let fechaFormateada = `${año}-${mes}-${dia} ${hora}:${minutos}`;

		//console.log(data);
		datos = $.parseJSON(data);
		var filas = '';
		
		$.each(datos,function(ind,row){
			var indice = 1;
			$.each(row.sintransmision,function(i,item){
				var disa = 'disabled';
				if(item.comentarios.length>0){
					disa = '';
					cantidadgestionados++;
				}else{
					cantidadpendientes++;
					disa = 'disabled';
				}

				var dias = 0;
				var comentarioultimo = '';
				if(item.comentarios.length>0){
					var fecha1 = '';
				    fecha1 = item.comentarios[item.comentarios.length - 1].fecha;
				    const fechaProporcionada = new Date(fecha1);
				    const fechaActual = new Date();
				    const diferenciaMilisegundos = fechaActual - fechaProporcionada;
				    dias = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

				    comentarioultimo = item.comentarios[item.comentarios.length - 1].comentario+' / '+item.comentarios[item.comentarios.length - 1].fecha;
				}
				
				var selestado1 = '';
				var selestado2 = '';
				var selestado3 = '';
				if(item.estadoselectr==0){
					selestado1 = 'selected';
					selestado2 = '';
					selestado3 = '';
				}else if(item.estadoselectr==1){
					selestado1 = '';
					selestado2 = 'selected';
					selestado3 = '';
				}else{
					selestado1 = '';
					selestado2 = '';
					selestado3 = 'selected';
				}

				filas += `<tr>`+
							`<td style='text-align:center;'>`+indice+`</td>`+
							`<td style='text-align:center;'>`+row.cliente+`</td>`+
							`<td style='text-align:center;'>`+item.rstransportista+`</td>`+
							`<td style='text-align:center;' id='patente_`+item.patente+`'>`+item.patente+`</td>`+
							`<td style='text-align:center;'>`+item.tiposervicio+`</td>`+
							`<td style='text-align:center;'>`+item.ulttransmision+`</td>`+
							`<td style='text-align:center;'>`+item.dias+`</td>`+
							/*`<td style='text-align:center;'>`+item.ultodocan+`</td>`+
							`<td style='text-align:center;'>`+item.ultodolitro+`</td>`+*/
							`<td><div class="row"><div class="col-md-10"><textarea class='form-control' id='comentario_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`'></textarea></div><div class="col-md-2"><button type="button" class="btn btn-sm btn-success" onclick='upddetras("`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+row.cliente+`")'><i class="fas fa-plus"></i></button> <button id="eye_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`" type="button" class="btn btn-sm btn-info" onclick='mostrarcomentarios("`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+row.cliente+`")' `+disa+`><i class="fas fa-eye"></i></button></div></div></td>`+
							`<td style='text-align:center;'><input type='datetime-local' id='fecha_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`' class='form-control-sm' value='`+fechaFormateada+`'></input></td>`+
							`<td style='text-align:center;' id="ult_comentario`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`">`+comentarioultimo+`</td>`+
							`<td style='text-align:center;' id="ult_dias`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`">`+dias+`</td>`+
							`<td style='text-align:center;'><select id='estado_`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`' class='estado' onchange='updestado("`+item.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+row.cliente+`")'><option value='0' `+selestado1+`>No operativo</option><option value='1' `+selestado2+`>Operativo</option><option value='2' `+selestado3+`>Desinstalar</option></select></td>`+
						 `</tr>`;
				indice ++;
				cantidadtotal++;
				
			});
		});

		$("#cardtotal").html(cantidadtotal);
		$("#carddgestionados").html(cantidadgestionados);
		$("#cardpendientes").html(cantidadpendientes);
		$("#tbclientesfiltro tbody").html(filas);
		$("#tbclientesfiltro2 tbody").html(filas);

		

		$(".estado").chosen({width:'100%'});
		
		$('#tbclientesfiltro').DataTable({
		    "language": jsonDatatable,//{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json' },
		    "paging": true,
		    "lengthChange": true,
		    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "Todos"]],
		    "pageLength": 25,  // Mostrar 100 registros por página por defecto
		    "searching": true,
		    "ordering": false,  // Desactivar orden al principio
		    "info": true,
		    "autoWidth": false,
		    "order": [],  // No realizar ningún orden al principio
		});

	});
}

function loadDatatable(){

	$('#tbclientesfiltrotel').DataTable({
		//ajax: 'operaciones.php?numero='+Math.floor(Math.random()*9999999)+'&operacion=getTabClientessintele&retornar=no&cliente='+$("#clientefil2").val()+'&patente='+$("#patentefil2").val()+'',
		ajax: {
			url: 'operaciones.php',
			type: 'GET',
			data: function(d) {
				// Agregar parámetros de consulta según tus necesidades
				d.numero = Math.floor(Math.random() * 9999999);
				d.operacion = 'getTabClientessintele';
				d.retornar = 'no';
				d.cliente = $("#clientefil2").val();
				d.patente = $("#patentefil2").val();
				d.datatable = true;
			},
			/*success: function(response) {
				console.log("response : ",response); // Mostrar la respuesta completa en la consola
				console.log("response.recordsTotal : ",response.recordsTotal); // Mostrar la respuesta completa en la consola

				$("#cardtotal").html(response.recordsTotal);
				$("#carddgestionados").html(response.gestionados);
				$("#cardpendientes").html(response.recordsTotal - response.gestionados);

				// Aquí puedes acceder y manipular la respuesta según tus necesidades
			}*/
		},
		processing: true,
		serverSide: true,
		"language": {
			url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json',
			thousands: '.' // Usar punto como separador de miles
		},
		"paging": true,
		"lengthChange": true,
		"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
		"pageLength": 25,
		"searching": true,
		"ordering": true,
		"info": true,
		"autoWidth": false,
		columns: [
			{ data: 'contador', orderable: false },
			{ data: 'cliente' },
			{ data: 'rs' },
			{ data: 'patente' },
			{ data: 'maloodo' },
			{ data: 'malolts' },
			{ data: 'comentario' },
			{ data: 'fecha_hora' },
			{ data: 'ultimo_comentario', orderable: false },
			{ data: 'dias_gestion', orderable: false },
			{ data: 'estado', orderable: false },
		],
		// Evento para acceder a los datos en cada refresco
		//drawCallback: function(settings) {
			//var api = this.api();
			//var data = api.rows().data(); // Obtener todos los datos de la tabla
			//console.log("data : ",data); // Mostrar los datos en la consola
		//}
	});

	//$("#cardtotal").html(cantidadtotaltel);
	//$("#carddgestionados").html(cantidadgestionadostel);
	//$("#cardpendientes").html(cantidadpendientestel);

	/*
	$('#tbclientesfiltro').DataTable({
		"language": jsonDatatable,//{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json' },
		"paging": true,
		"lengthChange": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "Todos"]],
		"pageLength": 25,  // Mostrar 100 registros por página por defecto
		"searching": true,
		"ordering": false,  // Desactivar orden al principio
		"info": true,
		"autoWidth": false,
		"order": [],  // No realizar ningún orden al principio
	});*/

}

function updestado (patente='',cliente=''){
    Swal.fire({
        title: '\u00BFEstas seguro de cambiar el estado?',
        text: "Si pones operativo este creara un ticket automatico, pero si pones no operativos el ticket seguira creado",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar'
    }).then((result) => {
        if (result.isConfirmed){

        	var tservicio = 0;
        	var tservicioticket = 0;
        	var rut = '';
        	var rs = '';
        	var imei = '';
        	let cli = datos.filter(cl => cl.cliente == cliente)
			if (cli.length > 0) {
				$.each(cli[0].sintransmision,function(i,row){
					if(patente==row.patente){
						if(row.tservicio=='Avanzado'){
							tservicio = 1;
							tservicioticket = 2;
						}else if(row.tservicio=='Thermo'){
							tservicio = 3;
							tservicioticket = 3;
						}else{
							tservicio = 0;
							tservicioticket = 1;
						}

						rut = row.ruttransportista;
        				rs = row.rstransportista;
        				imei = row.imei;
					}
				});
			}

            env       = {'imei':imei,'rut':rut,'rs':rs,'tservicio':tservicio,'tservicioticket':tservicioticket,'patente':patente,'cliente':cliente,'estado':$("#estado_"+patente).val()};
            var send  = JSON.stringify(env);
            $.ajax({
                url       : 'operaciones.php',
                data      : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'updestado',retornar:'no',envio:send},
                type      : 'post',
                dataType  : 'json',
                beforeSend: function(res) {

                },error   : function(res) {
                        console.log(res);
                },success : function(res) {
                    console.log(res);
                    if(res.respuesta=='success'){
                        toastr.success(res.mensaje);
                    }else{
                        toastr.error(res.mensaje);
                    }
                }
            });
        }
    }) 
}

function getvehiculoscliente(cliente='',opc='',servicio=0){

	var env   = {'cliente':cliente,'opc':opc,'servicio':servicio};
    var send  = JSON.stringify(env);

    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getvehiculoscliente',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(res) {
               
        },error   : function(res) {
            
        },success : function(res) {
        	if(res.options!=''){
        		$("#patentefil"+opc).chosen('destroy');
        		$("#patentefil"+opc).html(res.options);
        		$("#patentefil"+opc).chosen({width:'100%'});

        		if(opc!=''){
        			$("#patentefil"+opc).attr('onchange','getTabtelemetria()');
        		}else{
        			$("#patentefil"+opc).attr('onchange','getTabClientes()');
        		}
        		
        	}
        }
    });
}

function mostrarcomentarios(patente='',cliente=''){
	$("#mlistclientes").modal('show');
	var fila = ``;
	let cli = datos.filter(cl => cl.cliente == cliente)
    if (cli.length > 0) {
	    $.each(cli[0].sintransmision,function(i,row){
	    	if(patente.replaceAll("_","(").replaceAll("-",")").replaceAll("á","/")==row.patente.replaceAll(" ","")){
	    		$.each(row.comentarios,function(ii,rowi){
	    			if(rowi.eliminado==0){
	    				fila += `<tr id="trcom_`+rowi.id+`">`+
			    				`<td><button type="button" class="btn btn-sm btn-danger" onclick='delcoment("`+rowi.id+`","`+row.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+cli[0].cliente+`")'><i class="fas fa-trash"></i></button> <button type="button" class="btn btn-sm btn-success" onclick='editcoment("`+rowi.id+`","`+row.patente.replaceAll("(","_").replaceAll(")","-").replaceAll("/","á").replaceAll(" ","")+`","`+cli[0].cliente+`")'><i class="fas fa-check"></i></button></td>`+
				    			`<td><textarea id="textarea_`+rowi.id+`">`+rowi.comentario+`</textarea></td>`+
				    			`<td id="segunfech_`+rowi.id+`">`+rowi.fecha+`</td>`+
				    		`</tr>`;
	    			}
	    		})
	    	}
	    });
    }
    $("#tblusu tbody").html(fila);
}

function editcoment(id,patente='',cliente=''){
	if(($("#comentario_"+id).val()=='' || $("#textarea_"+id).val()==undefined)){
		toastr.error('Debes ingresar un comentario para editar');
		return;
	}

	let fechaActual = new Date();

	// Obtener los componentes de la fecha
	let año = fechaActual.getFullYear();
	let mes = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
	let dia = fechaActual.getDate().toString().padStart(2, '0');
	let hora = fechaActual.getHours().toString().padStart(2, '0');
	let minutos = fechaActual.getMinutes().toString().padStart(2, '0');

	// Formatear la fecha en el formato deseado
	let fechaFormateada = `${año}-${mes}-${dia} ${hora}:${minutos}`;

	var env   = {'id':id,'patente':patente,'comentario':$("#textarea_"+id).val(),'fecha':fechaFormateada};
    var send  = JSON.stringify(env);

    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'editcoment',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(res) {
               
        },error   : function(res) {
            
        },success : function(res) {
        	if(res.respuesta=='success'){
        		toastr.success(res.mensaje);

        		
        		let cli = datos.filter(cl => cl.cliente == cliente)
				if (cli.length > 0) {
					$.each(cli[0].sintransmision,function(i,row){
						if(patente.replaceAll("_","(").replaceAll("-",")").replaceAll("á","/")==row.patente.replaceAll(" ","")){
						   	$.each(row.comentarios,function(ii,rowi){
						    	if(rowi.id==id){
						    		rowi.comentario = $("#textarea_"+id).val();
						    		rowi.fecha = fechaFormateada;

						    		var fecha1 = '';
								    fecha1 = fechaFormateada;
								    const fechaProporcionada = new Date(fecha1);
								    const fechaActual = new Date();
								    const diferenciaMilisegundos = fechaActual - fechaProporcionada;
								    var dias = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

								    $("#segunfech_"+id).text(fechaFormateada)
								   
						    		$("#ult_comentario"+patente).text($("#textarea_"+id).val()+' / '+fechaFormateada)
						    		$("#ult_dias"+patente).text(dias)
						    	}
						   	})
						}
					});
				}
        	}else{
        		toastr.error(res.mensaje);
        	}
        }
    });
}

function delcoment (id,patente='',cliente=''){
    Swal.fire({
        title: '\u00BFEstas seguro de eliminarlo?',
        text: "Este desaparecera de la lista",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar'
    }).then((result) => {
        if (result.isConfirmed){
            env       = {'id':id};
            var send  = JSON.stringify(env);
            $.ajax({
                url       : 'operaciones.php',
                data      : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'delcoment',retornar:'no',envio:send},
                type      : 'post',
                dataType  : 'json',
                beforeSend: function(res) {

                },error   : function(res) {
                        console.log(res);
                },success : function(res) {
                    console.log(res);
                    if(res.respuesta=='success'){
                        toastr.success(res.mensaje);
                        $('#trcom_'+id).remove();
                 		
                 		let cli = datos.filter(cl => cl.cliente == cliente)
					    if (cli.length > 0) {
						    $.each(cli[0].sintransmision,function(i,row){
						    	if(patente==row.patente){
						    		$.each(row.comentarios,function(ii,rowi){
						    			if(rowi.id==id){
						    				rowi.eliminado = 1;
						    			}
						    		})

						    		var nuecome = '';
						    		var nuefecha = '';
						    		$.each(row.comentarios,function(ii,rowi){
						    			if(rowi.eliminado==0){
						    				nuecome = rowi.comentario;
						    				nuefecha = rowi.fecha;
						    			}
						    		})
						    		$("#ult_comentario"+patente).text(nuecome+' / '+nuefecha)

						    		var fecha1 = '';
								    fecha1 = nuefecha;
								    const fechaProporcionada = new Date(fecha1);
								    const fechaActual = new Date();
								    const diferenciaMilisegundos = fechaActual - fechaProporcionada;
								    var dias = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24));
								    $("#ult_dias"+patente).text(dias)
						    	}
						    });
					    }

                    }else{
                        toastr.error(res.mensaje);
                    }
                }
            });
        }
    }) 
}

function upddetras(patente='',cliente=''){
	if(($("#comentario_"+patente).val()=='' || $("#comentario_"+patente).val()==undefined) || ($("#fecha_"+patente).val()=='' || $("#fecha_"+patente).val()==undefined)){
		toastr.error('Debes ingresar un comentario y fecha para guardar');
		return;
	}
	var env   = {'patente':patente,'comentario':$("#comentario_"+patente).val(),'fecha':$("#fecha_"+patente).val()};
    var send  = JSON.stringify(env);

    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'upddetras',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(res) {
               
        },error   : function(res) {
            
        },success : function(res) {
        	if(res.respuesta=='success'){
        		toastr.success(res.mensaje);
        		var fech = '';
        		let cli = datos.filter(cl => cl.cliente == cliente)
        		if (cli.length > 0) {
        			$.each(cli[0].sintransmision,function(i,row){
        				if(row.patente.replaceAll(" ","")==patente.replaceAll("_","(").replaceAll("-",")").replaceAll("á","/")){

        					const fechaObjeto = new Date($("#fecha_"+patente).val());
							const año = fechaObjeto.getFullYear();
							const mes = (fechaObjeto.getMonth() + 1).toString().padStart(2, '0'); // Meses van de 0 a 11
							const dia = fechaObjeto.getDate().toString().padStart(2, '0');
							const hora = fechaObjeto.getHours().toString().padStart(2, '0');
							const minutos = fechaObjeto.getMinutes().toString().padStart(2, '0');
							const segundos = fechaObjeto.getSeconds().toString().padStart(2, '0');
							const fechaFormateada = `${año}-${mes}-${dia} ${hora}:${minutos}:${segundos}`;
							fech = fechaFormateada;
        					row.comentarios.push({'comentario':$("#comentario_"+patente).val(),'fecha':fechaFormateada,'eliminado':0,'id':res.idnuevo})
        				}
        			});
        		}

        		
        		$("#ult_comentario"+patente).text($("#comentario_"+patente).val()+' / '+fech)

        		var fecha1 = '';
				fecha1 = fech;
				const fechaProporcionada = new Date(fecha1);
				const fechaActual = new Date();
				const diferenciaMilisegundos = fechaActual - fechaProporcionada;
				var dias = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

        		$("#ult_dias"+patente).text(dias)
        		$("#eye_"+patente).attr('disabled',false)
        		$("#comentario_"+patente).val('')
        		
        		/*$("#fecha_"+patente).val('')*/
        	}else{
        		toastr.error(res.mensaje);
        	}
        }
    });
}

$("#todos").on( "click", function() {
  	if($(this).prop('checked')){
  		$(".chmasivo").prop('checked', true)
  	}else{
  		$(".chmasivo").prop('checked', false)
  	}
  	guardach(0,clientes[indicecli].cliente,'')
});

function guardach(idusu=0, clien='', nombre=''){

	var arr = [];
	if(idusu==0 && nombre==''){
		$(".chmasivo").each(function(){
			if($(this).prop('checked')){
				arr.push({'idusu':$(this).val(),'clien':clien,'nombreusu':$(this).attr('name'),'estado':1});
			}else{
				arr.push({'idusu':$(this).val(),'clien':clien,'nombreusu':$(this).attr('name'),'estado':0});
			}
	    });
	}else{
		$(".chmasivo").each(function(){
			if($(this).prop('checked')){
				arr.push({'idusu':$(this).val(),'clien':clien,'nombreusu':$(this).attr('name'),'estado':1});
			}else{
				arr.push({'idusu':$(this).val(),'clien':clien,'nombreusu':$(this).attr('name'),'estado':0});
			}
	    });
	}
	
	/*var env   = {'cliente':clien,'index':index};*/
    var send  = JSON.stringify(arr);

    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'guardach',cliente:clien,retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {
               
        },error   : function(respuesta) {
            
        },success : function(data) {
        	if(data.respuesta=='success'){
        		toastr.success(data.mensaje);
        		getTabClientes();
        	}else{
        		toastr.error(data.mensaje);
        	}
        }
    });
}

function activarMonitoreo(index){
	let active = $('#checkbox_'+index).prop('checked');
	let id = clientes[index].id;
	$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'activarMonitoreo', id:id, active:active, retornar:'no'},function(data){
		data = $.parseJSON(data);
		if(data.status=='OK'){
			if($('#checkbox_'+index).prop('checked')){
				toastr.success('Monitoreo activado exitosamente.');
			}
			else{
				toastr.warning('Monitoreo desactivado exitosamente.');
			}
		}
		else{
			toastr.error('Error al activar monitoreo.');
			$('#checkbox_'+index).prop('checked',false);
		}
		console.log(data);
	});
}

function verContactos(id){
$.each(clientes[id],function(index,valor){
contac="<div class='row'>";
contac+="<div class='col-md-10'>";
contac+="<table class='table table-bordered table-striped'>";
contac+="<thead><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Cargo</th></thead>";
contac+="<tbody>";	
if(index=="contactos"){
$.each(valor, function(index2,valor2){
contac+="<tr><td>"+valor2.nombre+"</td><td>"+valor2.telefono+"</td><td>"+valor2.correo+"</td><td>"+valor2.cargo+"</td></tr>";
});
contac+="</tbody></table>";
}
});
contac+="</div>";
$("#mlistclientes .modal-header").removeClass("header-rojo").addClass("header-verde");
$("#mlistclientes .modal-title").html("Contactos de Cliente : <b>"+clientes[id]["razonsocial"]+"</b>");
$("#mlistclientes .modal-body").html(contac);
$("#mlistclientes .modal-footer").css({display:"none"})
$("#mlistclientes").modal("toggle");
}

let indicecli = 0;
function EditarClienteusu(index){
	$("#tblusu tbody").html('');
	indicecli = index;
	var clien = clientes[index].cliente;
	var env   = {'cliente':clien,'index':index};
    var send  = JSON.stringify(env);
    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'cargacliente',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {
               
        },error   : function(respuesta) {
            
        },success : function(data) {
        	var fila = ``;
        	var estado = '';
            if(data.length>0){
            	$.each(data, function(i,item){
            		
            		if(item.todos==1){
	            		$('#todos').prop('checked', true);
	            	}else{
	            		$('#todos').prop('checked', false);
	            	}
            		

            		if(item.estado==1){
            			estado = 'checked';
            		}else{
            			estado = '';
            		}

					fila += `
            			<tr>
            				<td align='center'>`+(i+1)+`</td>
            				<td align='center'>`+item.usu_usuario+`</td>
            				<td align='center'>
            					<div class='form-check'><input onclick='guardach(`+item.usu_id+`,"`+item.cliente+`","`+item.usu_usuario+`")' class='form-check-input form-check-input-lg chmasivo' type='checkbox' name='`+item.usu_usuario+`' value='`+item.usu_id+`' id='defaultCheck`+item.usu_id+`' `+estado+`><label class='form-check-label' for='defaultCheck`+item.usu_id+`'></label>
            					</div>
            				</td>
            			</tr>`;
				});
            }
            $("#tblusu tbody").html(fila);
            $("#mlistclientes").modal('show');
        }
    });
}

function CeditCliente(){
// $("#form_editarcliente").hide();
// $("#tb_listadoclientes").show();
$('#btncrearcliente').removeClass('btn-warning').addClass('btn-success').text('Crear Cliente');
$('#formoperacion').val('nuevocliente');
$('#myTab a[href="#listaclientes"]').tab('show');
}

function addcontacto(){
var ncontactos = $("#tb_agregarcontacto tbody tr").length;
ncontactos=ncontactos+1;
con_nombre=$("input[name='nombrecontacto']").val();
con_telefono=$("input[name='telefonocontacto']").val();
con_correo=$("input[name='correocontacto']").val();
con_cargo=$("input[name='cargocontacto']").val();

$("#tb_agregarcontacto tbody").append("<tr id='con_fila"+ncontactos+"'><td>"+ncontactos+"</td><td>"+con_nombre+"</td><td>"+con_telefono+"</td><td>"+con_correo+"</td><td>"+con_cargo+"</td><td class='text-center text-red'><span class='pointer' onclick='removercontacto("+ncontactos+")'><i class='fa fa-trash-o' aria-hidden='true'></i></span></td></tr>");
$("#inp_agregarcontactos").append("<input type='hidden' id='idcon"+ncontactos+"' name='contactos[]' value=\""+con_nombre+"|"+con_telefono+"|"+con_correo+"|"+con_cargo+"\">");
$("input[name='nombrecontacto']").val("");
$("input[name='telefonocontacto']").val("");
$("input[name='correocontacto']").val("");
$("input[name='cargocontacto']").val("");
}

function noagregarcontacto(){
$("#tb_agregarcontacto tbody, #inp_agregarcontactos").html("");
$("#btn_agregarcontacto button").prop("disabled",false);
$("#form_agregarcontacto").hide();
}

function AgregarContacto(){
$("#btn_agregarcontacto button").prop("disabled",true);
$("#form_agregarcontacto").show();
}
function removercontacto(id){
$("#con_fila"+id+", #idcon"+id+"").remove();
}
function eliminarcontacto(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'BorrarContacto',contacto:''+id+'',retornar:'no'},function(data){
$("#con_fila"+id+"").remove();
ncontactos = $("#tb_agregarcontacto tbody tr").length;
if(ncontactos==0){
$("#btn_agregarcontacto button").prop("disabled",false);
$("#form_agregarcontacto").hide();
}
});
}

function eliminarcliente(id){
$.each(clientes,function(index,valor){
if(index==id){
det_cli="<div class='row'><div class='col-md-12'><table class='table tbinfoser'><tr><td class='bgtd' width='200'>Rut</td><td >"+valor.rut+"</td><td class='bgtd' width='200'>Razon Social</td><td>"+valor.razonsocial+"</td></tr>";
det_cli+="<tr><td class='bgtd' width='200'>Giro</td><td>"+valor.giro+"</td><td class='bgtd' width='200'>Dirección</td><td >"+valor.direccion+"</td></tr>";
det_cli+="<tr><td class='bgtd' width='200'>Teléfono</td><td>"+valor.telefono+"</td><td class='bgtd' width='200'>Correo</td><td>"+valor.correo+"</td></tr></table></div>";

if(valor.ncontactos > 0){
det_cli+="<div class='col-md-12'><h3>Contactos</h3><hr></div><div class='col-md-12'><table class='table table-bordered table-striped'><thead><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Cargo</th></thead><tbody>";
$.each(valor.contactos,function(index2,valor2){
det_cli+="<tr><td>"+valor2.nombre+"</td><td>"+valor2.telefono+"</td><td>"+valor2.correo+"</td><td>"+valor2.cargo+"</td></tr>";
});
det_cli+="</tbody></table></div>";
}
det_cli+="</div>";
$("#mlistclientes .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mlistveh .modal-dialog").css({'width':'50%'});
$("#mlistclientes .modal-title").html("Eliminar Cliente");
$("#mlistclientes .modal-body").html(det_cli);
$("#mlistclientes .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' onclick='BorrarCliente(\""+id+"\")'>Confirmar</button>")
$("#mlistclientes").modal("toggle");
}
});
}

function BorrarCliente(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'BorrarCliente',cliente:''+id+'',retornar:'no'},function(data){
$("#mlistclientes").modal("hide");
location.reload();
});
}

function getComunas(){
idregion=$("#region option:selected").val();
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getComunas',region:''+idregion+'',retornar:'no'},function(data){
console.log(data);
$("#comuna").html(data);
});
}


</script>
