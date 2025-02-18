<style>
.switchcustom {
  display: inline-block;
  height: 28px;
  position: relative;
  width: 50px;
  margin-bottom: 0;
}
.red-row, .red-row-cliente {
    background-color: red !important;
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
							<th>N°</th>
							<th>Usuario</th>
							<th align="center">
								<div class='form-check'>
									<input class='form-check-input form-check-input-lg' type='checkbox' value='0' id='todos'>
									<label class='form-check-label' for='todos'></label>
            					</div>
							</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">

			</div>
		</div>
	</div>
</div>
<!-- fin modal -->

<div class="content">
<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-pills" id="myTab">
            <li class="nav-item"><a id="navguia" class="nav-link active" href="#listaclientes"  data-toggle="tab">Lista de Clientes</a></li>
           <!--  <li class="nav-item"><a id="navlistguia" class="nav-link" href="#crearcliente"  data-toggle="tab">Crear Cliente</a></li> -->
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
			<div class="active tab-pane" id="listaclientes">
			<div class="row top20" id="tb_listadoclientes">
	<div class="col-md-12">
	<div class="box box-inverse box-solid">
		<div class="box-body">
				<table class="table table-bordered table-striped table-condensed table-sm" id="tbclientesfiltro">
					<thead class="thead-dark">
						<th>N°</th>
						<th>Cuenta</th>
						<th>Razón Social</th>
						<th>Usuarios bloqueados</th>
						<th class="text-center">Editar</th>
					</thead>
					<tbody id="tab_clientes">
						<tr><td colspan="4" align="center"><i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
			</div>
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
$(function(){
getTabClientes();

});
window.clientes;
function getTabClientes(){
	if ($.fn.DataTable.isDataTable('#tbclientesfiltro')) {
        $('#tbclientesfiltro').DataTable().destroy();
    }
	$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabClientesbloq',retornar:'no'},function(data){
		console.log(data);
		datos = $.parseJSON(data);
		clientes=datos;
		filas="";
		x=0;
		$.each(datos,function(i,item){
			let colorClase;
			if (item.moroso === 1) {
                colorClase = 'red';
             }
                     
         
			 filas+="<tr id='cliente_"+i+"' style='background-color: " + colorClase + ";'>" +
                        "<td align='center'>"+item.numero+"</td>" +
                        "<td class='font-weight-lighter' nowrap align='center'>"+item.cliente+"</td>" +
                        "<td class='font-weight-lighter' nowrap align='center'>"+item.razonsocial+"</td>" +
                        "<td class='font-weight-lighter' align='center'>"+item.numerobloqueados+"</td>" +
                        "<td align='center' class='text-center'><span class='btn btn-sm btn-warning btn-sm btn-circle' onclick='EditarClienteusu(\""+i+"\");'><i class='fa fa-edit' aria-hidden='true'></i></span></td>" +
                    "</tr>";
		});
		$("#tbclientesfiltro tbody").html(filas);
		$('#tbclientesfiltro').DataTable({
			"language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
			"paging": true,
			"lengthChange": true,
			"lengthMenu": [[20,-1], [20,"Todos"]],
			"pageLength":20,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"order": [[ 0, "asc" ]]
		});
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
	var checkbox = document.getElementById('defaultCheck' + idusu);
    var row = checkbox.closest('tr');
    
    if (!checkbox.checked) {
        row.classList.add('red-row');
        localStorage.setItem('row_' + idusu, 'red');
    } else {
        row.classList.remove('red-row');
        localStorage.removeItem('row_' + idusu);
    }
    
    
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
			let colorClase = '';
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
