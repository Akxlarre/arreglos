<!-- modal -->
<div class="modal" id="mdev">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">

			</div>
		</div>
	</div>
</div>
<!-- fin modal -->

<style type="text/css">
	.newalb {
	  padding: 50px;
	}

	.alb {
	  display: block;
	  margin-bottom: 15px;
	}

	.alb input {
	  padding: 0;
	  height: initial;
	  width: initial;
	  margin-bottom: 0;
	  display: none;
	  cursor: pointer;
	}

	.alb label {
	  position: relative;
	  cursor: pointer;
	}

	.alb label:before {
	  content:'';
	  -webkit-appearance: none;
	  background-color: transparent;
	  border: 2px solid #0079bf;
	  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05), inset 0px -15px 10px -12px rgba(0, 0, 0, 0.05);
	  padding: 10px;
	  display: inline-block;
	  position: relative;
	  vertical-align: middle;
	  cursor: pointer;
	  margin-right: 5px;
	}

	.alb input:checked + label:after {
	  content: '';
	  display: block;
	  position: absolute;
	  top: 2px;
	  left: 9px;
	  width: 6px;
	  height: 14px;
	  border: solid #0079bf;
	  border-width: 0 2px 2px 0;
	  transform: rotate(45deg);
	}
</style>

<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<section class="content">
	<input type="hidden" name="" id="totaldatos" value="0">
	<div class="row submenu">
		<div class="col-md-12" style="margin-top:10px;">
			<button type='button' class="btn btn-success btn-rounded" id="btn_ndev">
				 <i class="fa fa-plus" aria-hidden="true"></i> Nueva Devolución</button>
		</div>
	</div>
	<!--  nuevo traspaso -->
	<div class="row top20 oculto" id="fnuevadev">
		<div class="col-md-12">
			<div class="box box-inverse box-solid">
				<div class="box-header with-border">
					 <h3 class="box-title">Devolución</h3>
	            </div>
				<div class="box-body">
					<div class="col-md-12">
						<form action="operaciones.php" method="post" class="form-horizontal" enctype="multipart/form-data">
							<input type="hidden" name="operacion" value="nuevadevolucion"/>
							<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-2">
										<label>Fecha</label>
										<br>
										<input type="text" class="form-control fecha" name="fecha" value="<?=hoy();?>">
								    </div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<label>Bodega(Técnico)</label>
										<br><? htmlselect('bodega','bodega','personal','per_id','per_nombrecorto','','','','per_nombrecorto','','','si','no','no');?>
								    </div>
								    <div class="col-sm-4">
										<label>Estado de Envío</label>
										<select id="seltra_0" onchange="sendtracking(0,this.value,0)" class="form-control">
											<option value="0">SELECCIONAR</option>
											<option value="2">En Transito</option>
											<option value="3">Recepcionado</option>
										</select>
								    </div>
								    <div class="col-sm-4" id="tdapp_0">
								    	
								    </div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<table class="table table-bordered table-striped" id="tbprodis">
										<thead class="thead-dark">
											<th>Cantidad</th>
											<th>Devolver</th>
											<th>Producto</th>
											<th>Serie</th>
											<th>Estado</th>
											<th>Detalle</th>
											<th>Devolver</th>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label>Observaciones</label>
									<textarea name='observaciones' class='form-control rznone' rows=5>
										
									</textarea>
								</div>
							</div>
							<div class="form-group" style="margin-bottom: 10px;">
								 <div class="col-sm-6">
								 	<button type="button" class="btn btn-success btn-rounded" onclick="guardarDevolucion()">
								 		Guardar
								 	</button>
								 	&nbsp;&nbsp;
								 	<button type="button" class="btn btn-danger btn-rounded" onclick="location.reload();">
								 		Cancelar
								 	</button>
								 </div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12" style="display:none;" id="formeditdev">
		<input type="hidden" name="" id="valordev" value="">
		<div class="row" style="margin-top:10px;">
			<div class="col-md-12">
				<h3>Editar Devolución</h3>
			</div>
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="col-md-4">
				<div class="row">
					<label>Fecha</label>
					<input type="date" name="" class="form-control" id="datedevform">
				</div>
				<br>
				<div class="row">
					<label>Bodega(Técnico)</label>
					<? htmlselect('bodegadevedit','bodegadevedit','personal','per_id','per_nombrecorto','','','','per_nombrecorto','','','si','no','no');?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="row">
					<div class="col-md-12">
						<label>Estado de Envío</label>
						<select class="form-control" id="envedit" onchange="unoxotro(this.value);">
							<option value="2">En transito</option>
							<option value="3">Recepcionado</option>
						</select>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label>N° Seguimiento</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="" class="form-control" id="numeroc">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label>Nombre Courrier</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="" class="form-control" id="courrierc">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label>Recibe</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="" class="form-control" id="recibec">
					</div>
				</div>
			</div>
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="col-md-12">
				<table class="table table-bordered table-striped" id="listadoeditdev">
					<thead class="thead-dark">
						<th>Cantidad</th>
						<th>Devolver</th>
						<th>Producto</th>
						<th>Serie</th>
						<th>Estado</th>
						<th>Detalle</th>
						<th>Devolver</th>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="col-md-6">
				<label>Observaciones</label>
				<textarea class="form-control" id="obsedit"></textarea>
			</div>
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="col-md-6">
				<button class="btn btn-success btn-rounded" id="btnedit">Actualizar</button>
				&nbsp;
				<button class="btn btn-danger btn-rounded" onclick="location.reload();">Cancelar</button>
			</div>
		</div>
	</div>

	<div class="row top20" id="listadodedevoluciones">
		<div class="col-md-12">
			<div class="box box-inverse box-solid">
				<div class="box-header with-border">
					 <h3 class="box-title">Listado de Devoluciones</h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12" id='tblistdev'>
							<div class="box box-widget">
								<div class="box-header with-border">
									 <h3 class="box-title"></h3>
								</div>
								<div class="box-body table-resposive" style="max-height: 600px;">
									<table class="table table-condensed table-striped table-bordered table-sm" id="tbdevoluciones"> 
										<thead class="thead-dark">
											<th>#</th>
											<th>N° Devolución</th>
											<th>Fecha</th>
											<th>Desde Bodega</th>
											<th>Observaciones</th>
											<th>Estado Envío</th>
											<th>Datos Courrier</th>
											<th class="text-center"></th>
											<!--<th class="text-center"></th>-->
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-sm-6 oculto" id="detdev">
							<div class="box box-widget">
								<div class="box-header with-border">
									<h3 class="box-title"></h3>
										<div class="box-tools">
										<!--<a href="#" class="btn btn-success btn-circle-s" ><i class="fa fa-file-excel-o"></i></a>-->
										<button type="button" class="btn btn-danger btn-circle-s" onclick="cerrarDetDev()">
											<i class="fa fa-times"></i>
										</button>
									</div>
								</div>
								<div class="box-body">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	$(function(){
		$("#btn_ndev").on("click",function(){
			$("#listadodedevoluciones").hide();
			$("#fnuevadev").show();
			$('#formeditdev').hide();
			$(this).attr("disabled",true);
		});

		/*function cancelarDev(){
			$("#fnuevadev").hide();	
			$("#listadodedevoluciones").show();
		}*/

		$("#bodega").change(function(){
			idtec = $(this).val();
			getProTec(idtec);
		});

		getTabDevoluciones();
	});

	window.devoluciones;
	let devolucionesalb = [];

	function unoxotro(valor){
		if(valor==3){
			$('#numeroc').attr('disabled',true);
			$('#courrierc').attr('disabled',true);
			$('#recibec').attr('disabled',true);

			$('#numeroc').val('');
			$('#courrierc').val('');
			$('#recibec').val('');
		}else{
			$('#numeroc').attr('disabled',false);
			$('#courrierc').attr('disabled',false);
			$('#recibec').attr('disabled',false);
			
		}
	}

	function eliminardevolucion (index,iddevolucion){

		Swal.fire({
             title: '\u00BFEstas seguro de eliminarlo?',
             text: "Este ya no aparecer\u00E1 en la lista",
             icon: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Confirmar'
        }).then((result) => {
             if (result.isConfirmed){
                env      = {'index':index,'iddevolucion':iddevolucion};
			    var send = JSON.stringify(env);
			    $.ajax({
			        url     : 'operaciones.php',
			        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'deletedev',retornar:'no',envio:send},
			        type    : 'post',
			        dataType: 'json',
			        beforeSend: function(respuesta) {

			        },error   : function(respuesta) {
			            console.log(respuesta);
			        },success : function(respuesta) {
			        	if(respuesta.logo=='success'){
			        		toastr.success(respuesta.mensaje);
                            location.reload();
			        	}else{
			        		toastr.error(respuesta.mensaje);
			        	}
			        }
			    });
             }
        })
	}

	function editardevolucion (index,iddevolucion){

		$('#formeditdev').show();
		$("#listadodedevoluciones").hide();
		$("#fnuevadev").hide();
		$("#listadoeditdev tbody").html('<tr><td colspan="8" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
		env      = {'index':index,'iddevolucion':iddevolucion};
	    var send = JSON.stringify(env);
	    $.ajax({
	        url     : 'operaciones.php',
	        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getdatdev',retornar:'no',envio:send},
	        type    : 'post',
	        dataType: 'json',
	        beforeSend: function(respuesta) {

	        },error   : function(respuesta) {
	            console.log(respuesta);
	        },success : function(respuesta) {
	        	console.log(respuesta);
	        	var pro = '';
	            if(respuesta.detalle.length>0){
	            	$('#datedevform').val(respuesta.dev_fecha);
	            	$('#bodegadevedit').val(respuesta.usu_id_envia);
	            	$('#bodegadevedit').attr('disabled',true);
	            	$('#obsedit').val(respuesta.dev_observacion);
	            	$('#envedit').val(respuesta.dev_tracking);
	            	$('#numeroc').val(respuesta.dev_tracking_codigo);
	            	$('#courrierc').val(respuesta.dev_tracking_courrier);
	            	$('#recibec').val(respuesta.dev_tracking_recibe);
	            	$('#valordev').val(respuesta.dev_id);     

	            	$.each(respuesta.detalle,function(ind,valor){

						var m = '';
						var b = '';
						var n = '';
						if(valor.ser_condicion==1){
							m = '';
						    b = 'selected';
						    n = '';
						}else if(valor.ser_condicion==0){
							m = 'selected';
						    b = '';
						    n = '';
						}else{
							m = '';
						    b = '';
						    n = 'selected';
						}

						pro+="<tr id='fpxtedit"+ind+"'><td class='text-center'>1</td><td class='text-center'>1</td><td id='proidedit"+ind+"' name='"+valor.pro_id+"'>"+valor.pro_nombre+"</td><td class='tdserie' name='"+valor.ser_id+"'>"+valor.ser_codigo+"</td><td><select name='estadoproedit' class='form-control seltd' id='selectedit"+ind+"' "+n+"><option value='99'>SELECCIONAR</option><option value=1 "+b+">BUENO</option><option value=0 "+m+">MALO</option></select></td><td><textarea class='form-control txtdev' rows=3  id='txtedit"+ind+"'>"+valor.ddev_observacion+"</textarea></td><td class='text-center'><div class='newalbedit'><div class='alb'><span class='pointer btn btn-sm btn-danger btn-circle' onclick='eliminardetdev("+respuesta.dev_id+","+ind+","+valor.ddev_id+","+valor.ser_id+")'><i class='fa fa-trash'></i></span></div></div></td></tr>";
					});

					$("#listadoeditdev tbody").html(pro);
	            }else{
	            	$('#listadoeditdev tbody').html('<tr><td colspan="10" align="center">No hay series asociadas</td></tr>');
	            }
	        }
	    });

	}

	$("#btnedit").on( "click", function() {
        var devid       = $('#valordev').val();
        var observacion = $('#obsedit').val();
        var fec         = convertDateFormat($('#datedevform').val());
        var estadoenvo  = $('#envedit').val();
        var recibec     = $('#recibec').val();
        var courrierc   = $('#courrierc').val();
        var numeroc     = $('#numeroc').val();
        var serid       = [];
        var conid       = [];
        var txtva       = [];
        $(".tdserie").each(function(){
        	 var valser = $(this).attr('name');
        	 serid.push(valser);
        });

        $(".seltd").each(function(){
        	var conser = $(this).val();
        	conid.push(conser);
        });

        $(".txtdev").each(function(){
        	var txtser = $(this).val();
        	txtva.push(txtser);
        });

        var datos       = {'devid':devid,'observacion':observacion,'fecha':fec,'estadoenvo':estadoenvo,'recibec':recibec,'courrierc':courrierc,'numeroc':numeroc,'serid':serid,'conid':conid,'txtva':txtva};
        var send        = JSON.stringify(datos);
        $.ajax({
            url     : 'operaciones.php',
            data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'updatedev',retornar:'no',envio:send},
            type    : 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {

            },error   : function(respuesta) {

            },success : function(respuesta) {
                if(respuesta.logo=='success'){
                    toastr.success(respuesta.mensaje);
                    location.reload();
                }else{
                    toastr.error(respuesta.mensaje);
                }   
            }
        });
    });

	function eliminardetdev(iddev,index,idddev,serid){
		Swal.fire({
	         title: '\u00BFEstas seguro de eliminarlo?',
	         text: "Este ya no aparecer\u00E1 en la lista",
	         icon: 'warning',
	         showCancelButton: true,
	         confirmButtonColor: '#3085d6',
	         cancelButtonColor: '#d33',
	         confirmButtonText: 'Confirmar'
	    }).then((result) => {
	         if (result.isConfirmed){
	            var datos = {'index':index,'iddev':iddev,'iddet':idddev,'serid':serid};
	            var send  = JSON.stringify(datos);
	            $.ajax({
	                url     : 'operaciones.php',
	                data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminardetdev',retornar:'no',envio:send},
	                type    : 'post',
	                dataType: 'json',
	                beforeSend: function(respuesta) {

	                },error   : function(respuesta) {

	                },success : function(respuesta) {
	                     if(respuesta.logo=='success'){
	                     	 toastr.success(respuesta.mensaje);
	                     	 $('#fpxtedit'+index).remove();
	                     	 if(respuesta.sql==0){
	                     	 	location.reload();
	                     	 }
	                     }else{
	                     	 toastr.error(respuesta.mensaje);
	                     }
	                }
	            });
	         }
	    }) 
	}

	function getTabDevoluciones(){
		$("#tbdevoluciones tbody").html('<tr><td colspan="8" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
		$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabDevoluciones',retornar:'no'},function(data){
			console.log(data);
			datos        = $.parseJSON(data);
			if(datos.length>0){
				devoluciones = datos;
				filas        = "";
				x            = 0;

				$.each(datos,function(index,valor){
					devolucionesalb.push({'index':valor.detalle,'sobra':valor.sobra});
					x++;
					if(valor.usumodifica==null || valor.usumodifica=='' || valor.usumodifica==undefined){
						valor.usumodifica = '-';
					}

					var seldev = '';
					var tra    = 'No identificado';
					var txttra = '-';
					if(valor.dev_tracking==2){
						tra    = 'En Transito';
						seldev = '<select id="seltraupd_'+x+'" onchange="sendtrackingupd('+valor.devid+',this.value,'+x+')" class="form-control"><option value="0">SELECCIONAR</option><option value="2" selected>En Transito</option><option value="3">Recepcionado</option></select>';
						txttra = '<label>'+valor.dev_tracking_fecha+'</label><br><label>'+valor.dev_tracking_courrier+'</label><br><label>'+valor.dev_tracking_codigo+'</label><br><label>'+valor.dev_tracking_recibe+'</label>';
					}else if(valor.dev_tracking==3){
						tra    = 'Recepcionado';
						seldev = '<select id="seltraupd_'+x+'" onchange="sendtrackingupd('+valor.devid+',this.value,'+x+')" class="form-control"><option value="0">SELECCIONAR</option><option value="2">En Transito</option><option value="3" selected>Recepcionado</option></select>';
						txttra = '<label>'+valor.dev_tracking_fecha+'</label>';
					}

					filas+="<tr><td>"+x+"</td><td>"+valor.devid+"</td><td>"+valor.dev_fecha+"</td><td>"+valor.usuenvianombre+"</td><td>"+valor.observaciones+"</td><td>"+seldev+"</td><td id='tdappupd_"+x+"'>"+txttra+"</td><td class='text-center' width=150><span class='pointer btn btn-warning btn-circle' onclick='editardevolucion("+index+","+valor.dev_id+")'><i class='fa fa-edit' aria-hidden='true'></i></span>&nbsp;<span class='pointer btn btn-info btn-circle' onclick='verDevolucion("+index+","+valor.dev_id+")'><i class='fa fa-eye' aria-hidden='true'></i></span>&nbsp;<span class='pointer btn btn-danger btn-circle' onclick='eliminardevolucion("+index+","+valor.dev_id+")'><i class='fa fa-trash' aria-hidden='true'></i></span></td></tr>";
				});
			}else{
				filas = '<tr><td colspan="10" align="center">No hay devoluciones</td></tr>';
			}
			
			$("#tbdevoluciones tbody").html(filas);
			$('#tbdevoluciones').DataTable({
				"language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
				"paging": true,
				"lengthChange": true,
				"lengthMenu": [[20,-1], [20,"Todos"]],
				"pageLength":20,
				"searching": true,
				"ordering": true,
				"info": true,
				"autoWidth": false
			});
		});
	}

	window.protec;

	function getProTec(id){
		$("#tbprodis tbody").html('<tr><td colspan="9" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
		$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getProTec',idtec:id,retornar:'no'},function(data){

			protec = $.parseJSON(data);
			if(protec.length>0){
				pro    = "";
				$.each(protec,function(index,valor){
					
					if(valor.tieneserie=="NO"){
						inpcan = "<input type='text' class='form-control' value=0 id='inpcan"+index+"'>";
					}else{
						inpcan = "1";
					}

					var m = '';
					var b = '';
					var n = '';
					if(valor.idcondicion==1){
						m = '';
					    b = 'selected';
					    n = '';
					}else if(valor.idcondicion==0){
						m = 'selected';
					    b = '';
					    n = '';
					}else{
						m = '';
					    b = '';
					    n = 'selected';
					}

					pro+="<tr id='fpxt"+index+"'><td class='text-center'>1</td><td width=50 class='text-center'>"+inpcan+"</td><td id='proid"+index+"' name='"+valor.proid+"'>"+valor.proname+"</td><td>"+valor.serie+"</td><td><select name='estadopro' class='form-control' id='select"+index+"' "+n+"><option value='99'>SELECCIONAR</option><option value=1 "+b+">BUENO</option><option value=0 "+m+">MALO</option></select></td><td><textarea class='form-control form-control-sm rznone' id='txt"+index+"' style='height:40px;'></textarea></td><td class='text-center'><div class='newalb'><div class='alb'><input type='checkbox' name='"+index+"' id='pxt"+index+"' value='"+valor.idserie+"'><label for='pxt"+index+"'></label></div></div></td></tr>";

					$('#totaldatos').val(index);
				});
			}else{
				pro = '<tr><td colspan="9" align="center">No hay datos</td></tr>';
			}
			
			$("#tbprodis tbody").html(pro);
		});
	}

	function sendtracking(ind,valor,index){

		const swalWithBootstrapButtons = Swal.mixin({
	      customClass: {
	        confirmButton: 'btn btn-success',
	        cancelButton: 'btn btn-danger'
	      },
	      buttonsStyling: false
	    })

	    swalWithBootstrapButtons.fire({
	      title : '¿Estas seguro de mandar este estado?',
	      text  : "Este solo se hara efectivo si seleccionas el Check",
	      icon  : 'warning',
	      showCancelButton: true,
	      confirmButtonText: 'Aceptar',
	      cancelButtonText: 'Cancelar',
	      reverseButtons: true
	    }).then((result) => {
	      if (result.isConfirmed) {
	            var valida = 1;
	            var nsegui = '';
	            if(valor==2){
	                Swal.fire({
	                    title             : 'Ingresa datos del Courrier',
	                    html              : '<label>Numero del Courrier</label><input type="text" id="tranum" class="swal2-input" placeholder="Ej:80055005"><label>Nombre del Courrier</label><input type="text" id="tranom" class="swal2-input" placeholder="Ej:Correos de Chile"><label>Nombre persona que recibe</label><input type="text" id="trarec" class="swal2-input" placeholder="Ej:Juan Ramiro">',
	                    confirmButtonText : 'Enviar',
	                    focusConfirm      : false,
	                    preConfirm        : () => {
	                        const nsegui = Swal.getPopup().querySelector('#tranum').value
	                        const csegui = Swal.getPopup().querySelector('#tranom').value
	                        const rsegui = Swal.getPopup().querySelector('#trarec').value
	                        if(!nsegui && !csegui && !rsegui){
	                            Swal.showValidationMessage(`Debes ingresar datos del Courrier`);
	                            $('#seltra_'+index).val(0);
	                            $('#tdapp_'+index).text('');
	                            valida = 0;
	                        }
	                        return {nsegui: nsegui,csegui:csegui,rsegui:rsegui}
	                    }
	                }).then((result) => {
	                    nsegui = result.value.nsegui;
	                    csegui = result.value.csegui;
	                    rsegui = result.value.rsegui;
	                    $('#seltra_'+index).val(2);
	                    $('#tdapp_'+index).html('<label id="cou_'+index+'">'+csegui+'</label><br><label id="num_'+index+'">'+nsegui+'</label><br><label id="rec_'+index+'">'+rsegui+'</label>');
	                })
	            }else{
	                 $('#tdapp_'+index).html('');
	            }   
	       }else if (result.dismiss === Swal.DismissReason.cancel){
	            $('#seltra_'+index).val(0);
	            $('#tdapp_'+index).html('');
	       }
	    }) 
	}

	function sendtrackingupd(iddev,valor,index){

	    const swalWithBootstrapButtons = Swal.mixin({
	      customClass: {
	        confirmButton: 'btn btn-success',
	        cancelButton: 'btn btn-danger'
	      },
	      buttonsStyling: false
	    })

	    swalWithBootstrapButtons.fire({
	      title : '¿Estas seguro de mandar este estado?',
	      text  : "Esto afectara a todas las series que esta conlleva en el traspaso",
	      icon  : 'warning',
	      showCancelButton: true,
	      confirmButtonText: 'Aceptar',
	      cancelButtonText: 'Cancelar',
	      reverseButtons: true
	    }).then((result) => {
	      if (result.isConfirmed) {
	            var valida = 1;
	            var nsegui = '';
	            if(valor==2){
	                Swal.fire({
	                    title             : 'Ingresa datos del Courrier',
	                    html              : '<label>Numero del Courrier</label><input type="text" id="tranum" class="swal2-input" placeholder="Ej:80055005"><label>Nombre del Courrier</label><input type="text" id="tranom" class="swal2-input" placeholder="Ej:Correos de Chile"><label>Nombre persona que recibe</label><input type="text" id="trarec" class="swal2-input" placeholder="Ej:Juan Ramiro">',
	                    confirmButtonText : 'Enviar',
	                    focusConfirm      : false,
	                    preConfirm        : () => {
	                        const nsegui = Swal.getPopup().querySelector('#tranum').value
	                        const csegui = Swal.getPopup().querySelector('#tranom').value
	                        const rsegui = Swal.getPopup().querySelector('#trarec').value
	                        if(!nsegui && !csegui && !rsegui){
	                            Swal.showValidationMessage(`Debes ingresar datos del Courrier`);
	                            $('#seltraupd_'+index).val(0);
	                            valida = 0;
	                        }
	                        return {nsegui: nsegui,csegui:csegui,rsegui:rsegui}
	                    }
	                }).then((result) => {
	                    /* Swal.fire(`
	                        Numero: ${result.value.login}
	                    `.trim())*/
	                    nsegui = result.value.nsegui;
	                    csegui = result.value.csegui;
	                    rsegui = result.value.rsegui;
	                    $('#seltraupd_'+index).val(2);
	                    env = {'iddev':iddev,'valor':valor,'nseguimiento':nsegui,'nombretra':csegui,'recibecou':rsegui};
	                    var send = JSON.stringify(env);
	                    $.ajax({
	                        url     : 'operaciones.php',
	                        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'enviartrackingdev',retornar:'no',envio:send},
	                        type    : 'post',
	                        dataType: 'json',
	                        beforeSend: function(respuesta) {

	                        },error   : function(respuesta) {
	                            console.log(respuesta);
	                        },success : function(respuesta) {
	                            if(respuesta.logo=='success'){
	                                toastr.success(respuesta.mensaje);
	                                $('#tdappupd_'+index).html('<label>'+csegui+'</label><br><label>'+nsegui+'</label><br><label>'+rsegui+'</label>');
	                            }else{
	                                toastr.error(respuesta.mensaje);
	                            }
	                            location.reload();
	                        }
	                    });
	                })
	            }else{
	                nsegui = '';
	                env = {'iddev':iddev,'valor':valor,'nseguimiento':nsegui,'opc':1};
	                var send = JSON.stringify(env);
	                $.ajax({
	                    url     : 'operaciones.php',
	                    data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'enviartrackingdev',retornar:'no',envio:send},
	                    type    : 'post',
	                    dataType: 'json',
	                    beforeSend: function(respuesta) {

	                    },error   : function(respuesta) {
	                        console.log(respuesta);
	                    },success : function(respuesta) {
	                        if(respuesta.logo=='success'){
	                            toastr.success(respuesta.mensaje);
	                            $('#tdappupd_'+index).html('');
	                        }else{
	                            toastr.error(respuesta.mensaje);
	                        }
	                        location.reload();
	                    }
	                });
	            }   
	      } else if (
	        /* Read more about handling dismissals below */
	        result.dismiss === Swal.DismissReason.cancel
	      ) {
	            $('#seltraupd_'+index).val(0);
	      }
	    }) 
	}

	function verDevolucion(index, idevolucion){

		$("#tblistdev").removeClass("col-sm-12").addClass("col-sm-6");
		$("#detdev .box-title").html("Detalle de Devolución");
		var tabla = '';
		console.log(devolucionesalb[index]['index']);
		$.each(devolucionesalb[index]['index'],function(i,valor){

			if(i==0){
	            tabla ="Fecha <b>"+valor.dev_fecha+"</b> De "+valor.usuenvianombre+" a Bodega Principal <hr><table class='table table-sm table-bordered table-striped'><thead class='thead-dark'><th>Producto</th><th>Cantidad</th><th>N° Serie</th><th>Estado</th><th>Observación</th></thead><tbody>"
	        }

	        var txtextra = 'No identificado';
	        var tdextra  = '-';
	        var otrotd   = '';
			if(valor.traid==2){
				txtextra = 'En transito';
				tdextra  = valor.codigotracking;
				otrotd = "<label>"+valor.ddev_tracking_fecha+"</label><br><label>"+valor.ddev_tracking_courrier+"</label><br><label>"+valor.ddev_tracking_codigo+"</label><br><label>"+valor.ddev_tracking_recibe+"</label>";
			}else if(valor.traid==1){
				txtextra = 'Preparación';
				tdextra  = '-';
				otrotd   = '';
			}else if(valor.traid==3){
				txtextra = 'Recepcionado';
				tdextra  = '-';
				otrotd   = '';
			}

			var estado  = '';
			var trcolor = '';
			if(valor.ddev_estado==1){
				trcolor = "";
	            estado  = "<td class='text-center'><span class='text-success'><i class='fa fa-check' aria-hidden='true'></i></span></td>";	
			}else{
				trcolor = "danger";
	            estado  = "<td class='text-center'><span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span></td>";
			}

			/*var seldevser = '';
			$.each(devolucionesalb[index]['sobra'],function(ind,valors){
				if(ind==0){
                	 seldevser +='<select id="selserdev_'+i+'" onchange="enviaractser('+valor.serid+','+i+','+idevolucion+')" class="form-control"><option value="'+valor.serid+'" selected>'+valor.serie+'</option>';
            	}
            seldevser +='<option value="'+valors.ser_id+'">'+valors.ser_codigo+'</option>';
	    	});
	    	seldevser+='</select>';*/

	        tabla+="<tr><td>"+valor.producto+"</td><td>1</td><td>"+valor.serie+"</td>"+estado+"<td>"+valor.observacion+"</td></tr>";
	    });

		tabla += "</tbody></table>";
		$("#detdev .box-body").html(tabla);
		$("#detdev").show();
		$('html, body').animate( { scrollTop : 0 }, 400 );
	}

	function cerrarDetDev(){
		$("#detdev").hide();
		$('#formeditdev').hide();	
		$("#tblistdev").removeClass("col-sm-6").addClass("col-sm-12");
	}

	detalledev = {};

	function devPro(index,idserie){

		idpxt = protec[index]["idpxt"];
		if($("#pxt"+index+"").is(':checked')){
			cantdev = parseInt($("#inpcan"+index).val());
			obs     = $("#txt"+index).val();
			idestado= $("#select"+index).val();
			if(protec[index]["tieneserie"]==="SI"){
				 cantdev=1;
			}

			if(cantdev==0 || isNaN(cantdev)){
				alert("La cantidad a devolver debe ser mayor que 0");
				$("#inpcan"+index).val("").focus();
				$("#pxt"+index+"").prop("checked", false );
				return;
			}

			cantdisp = parseInt(protec[index]["cantidad"]);
			if(cantdev > cantdisp){
				alert("La cantidad a devolver no puede ser mayor a la cantidad disponible");
				$("#inpcan"+index).val("").focus();
				$("#pxt"+index+"").prop("checked", false );
				return;
			}else{
				detalledev[idpxt] = ({"idpro":protec[index]["idpro"],"stock":cantdisp,"cantidad":cantdev,"tieneserie":protec[index]["tieneserie"],"serie":protec[index]["serie"],"obs":obs,"estadooriginal":protec[index]["estado"],"idestado":idestado});
				$("#inpcan"+index+",#select"+index+",#txt"+index).attr("disabled",true);
			}
		}else{
			delete detalledev[idpxt];
			$("#inpcan"+index+",#select"+index+",#txt"+index).attr("disabled",false);
		}
	}

	let senddev = [];
	function guardarDevolucion(){

		if($("#bodega").val() !=""){

			var fecha  = convertDateFormat($("input[name='fecha']").val());
			var bodega  = $("#bodega").val();
			var observacion  = $("textarea[name='observaciones']").val();

			$("input:checkbox:checked").each(function(){

				 var idserie = $(this).val();
				 var nameser = $(this).attr("name");
				 var idselec = $('#select'+nameser).val();
				 var detalle = $('#txt'+nameser).val();
				 var proid   = $('#proid'+nameser).attr("name");
				 var traid   = $('#seltra_0').val();
				 var codcou  = $('#cou_0').text();
				 var numcou  = $('#num_0').text();
				 var reccou  = $('#rec_0').text();

				 senddev.push({'fecha':fecha,'usu_sel':bodega,'observacion':observacion,"idseerie":idserie,"idselect":idselec,"detalle":detalle,"proid":proid,"traid":traid,"codcou":codcou,"numcou":numcou,"reccou":reccou});
	            
	        });
			
			json = JSON.stringify(senddev);

			console.log(json);
			$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'newnuevadevolucion',devolucion:json,retornar:'no'},function(data){

				if(data.logo!='error'){
		            toastr.success(data.mensaje);
		        }else{
		            toastr.error(data.mensaje);
		        }
				location.reload();
			});
		}else{
			Swal.fire(
	            'Error',
	            'Debes seleccionar una bodega',
	            'error'
	        );
			return;
		}
	}

	function convertDateFormat(string) {
	    var info = string.split('/').reverse().join('/');
	    return info;
	}
</script>