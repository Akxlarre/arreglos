<!-- modal -->
<div class="modal" id="mlistproveedores">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title"></h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

</div>
<div class="modal-body"></div>
<div class="modal-footer">
</div>
</div>
</div>
</div>
<!-- fin modal -->

<div class="content">
<div class="row top20" id="tb_listadoproveedores">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Listado de Proveedores</h3>
</div>
<div class="box-body">
<table class="table table-bordered table-striped" id="tbproveedoresfiltro">
<thead>
<th>#</th>
<th>Rut</th>
<th>Razón Social</th>
<th>Giro</th>
<th>Dirección</th>
<th>Teléfono</th>
<th>Correo</th>
<th class="text-center">Contactos</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody id="tab_proveedores">
</tbody>
</table>
</div>
</div>
</div>
</div>

<div class="row top20 oculto" id="form_editarproveedor">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Editar Proveedor</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal">
<input type="hidden" name="operacion" value="editarproveedor"/>
<input type="hidden" name="idproveedor"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="form-group">
<div class="col-sm-2 col-lg-2">
<label>Rut</label><br>
<input type="text" name="rut" class="form-control">
</div>
<div class="col-sm-6 col-lg-6 col-offset-2 oculto" id="cliente_existe">

</div>

</div>
<div class="form-group">
<div class="col-sm-6 col-lg-6">
<label>Razon Social</label><br>
<input type="text" name="razonsocial" class="form-control">
</div>
<div class="col-sm-6 col-lg-6">
<label>Giro</label><br>
<input type="text" name="giro" class="form-control"></div>
</div>
<div class="form-group">
<div class="col-sm-3 col-lg-3">
<label>Región</label><br>
<? htmlselect('region','region','regiones','id','region|ordinal','','','','id','getComunas()','','si','no','no');?>
</div>
<div class="col-sm-3 col-lg-3">
<label>Comuna</label><br>
<select name="comuna" id="comuna" class="form-control"></select>
<div class="oculto col-sm-1 text-green padtop7 txtleft" id="loadaop"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i></div>
</div>
<div class="col-sm-6 col-lg-6">
<label>Dirección</label><br>
<input type="text" name="direccion" class="form-control"></div>
</div>
<div class="form-group">
<div class="col-sm-3 col-lg-3" >
<label>Teléfono</label><br>
<input type="text" name="telefono" class="form-control">
</div>
<div class="col-sm-3 col-lg-3">
<label>Correo</label><br>
<input type="text" name="correo" class="form-control">
</div>
<div class="col-sm-3 col-lg-3" id="btn_agregarcontacto">
<button type="button" class="btn btn-success btn-rounded top25" onclick="AgregarContacto();"><i class="fa fa-address-card-o" aria-hidden="true"></i> Agregar Contacto</button>
</div>
</div>
<!-- formulario para agregar clientes -->
<div class="oculto top50" id="form_agregarcontacto">
<h3>Agregar Contacto</h3>
<hr>
<div class="form-group">
<div class="col-sm-3 col-lg-3" >
<label>Nombre</label><br>
<input type="text" name="nombrecontacto" class="form-control">
</div>
<div class="col-sm-2 col-lg-2">
<label>Teléfono</label><br>
<input type="text" name="telefonocontacto" class="form-control">
</div>
<div class="col-sm-2 col-lg-2">
<label>Correo</label><br>
<input type="text" name="correocontacto" class="form-control">
</div>
<div class="col-sm-3 col-lg-3">
<label>Cargo</label><br>
<input type="text" name="cargocontacto" class="form-control">
</div>
<div class="col-sm-2 col-lg-2">
<button type="button" class="btn btn-success btn-circle top25" onclick="addcontacto();"><i class="fa fa-plus" aria-hidden="true"></i></button>
<button type="button" class="btn btn-danger btn-circle top25" onclick="noagregarcontacto();"><i class="fa fa-times" aria-hidden="true"></i></button>
</div>
</div>
<div  id="inp_agregarcontactos">
</div>
<!-- listado para ver contactos agregagos -->
<table class="table table-bordered table-striped" id="tb_agregarcontacto">
<thead>
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



<div class="form-group">
<div class="col-sm-3 col-lg-3"><button type="submit" class="btn btn-success btn-rounded">Editar Proveedor</button></div>
</div>

</form>
</div>
</div>
</div>
</div>

</div>

<script>
$(function(){
getTabProveedores();
});
window.proveedores;
function getTabProveedores(){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabProveedores',retornar:'no'},function(data){
datos = $.parseJSON(data);
proveedores=datos;
filas="";
x=0;
$.each(datos,function(index,valor){
x++;
filas+="<tr id='proveedor"+index+"'><td>"+x+"</td><td nowrap>"+valor.rut+"</td><td>"+valor.razonsocial+"</td><td>"+valor.giro+"</td><td>"+valor.direccion+"</td><td>"+valor.telefono+"</td><td>"+valor.correo+"</td><td class='text-center'><span class='btn btn-info btn-sm btn-circle' onclick='verContactos(\""+index+"\");'><i class='fa fa-address-book' aria-hidden='true'></i></span></td><td class='text-center'><span class='btn btn-warning btn-sm btn-circle' onclick='EditarProveedor(\""+index+"\");'><i class='fa fa-edit' aria-hidden='true'></i></span></td><td class='text-center'><span class='btn btn-danger btn-sm btn-circle' onclick='eliminarproveedor(\""+index+"\")'><i class='fa fa-trash' aria-hidden='true'></i></span></td></tr>";
});
$("#tab_proveedores").html(filas);
$('#tbproveedoresfiltro').DataTable({
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

function verContactos(id){
$.each(proveedores[id],function(index,valor){
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
$("#mlistproveedores .modal-header").removeClass("header-rojo").addClass("header-inverse");
$("#mlistproveedores .modal-title").html("Contactos de Proveedor : <b>"+proveedores[id]["razonsocial"]+"</b>");
$("#mlistproveedores .modal-body").html(contac);
$("#mlistproveedores .modal-footer").css({display:"none"})
$("#mlistproveedores").modal("toggle");
}

function EditarProveedor(id){
$("input[name='idproveedor']").val(id);
$.each(proveedores,function(index,valor){
if(index==id){
$("input[name='rut']").val(valor.rut);
$("input[name='razonsocial']").val(valor.razonsocial);
$("input[name='giro']").val(valor.giro);
$("input[name='direccion']").val(valor.direccion);
$("#region").val(valor.region);
var form_data = new FormData();
form_data.append('operacion','getComunas');
form_data.append('region', valor.region);
form_data.append('retornar','no');
$.ajax({
url: 'operaciones.php', //ruta archivo operaciones
dataType: 'text',  // tipo de datos
cache: false,
contentType: false,
processData: false,
data: form_data,
type: 'post',
async: false,// esperar que se ejecute y continua con la ejecucion del script
success: function(respuesta){
$("#comuna").html(respuesta);
}
});
$("#comuna").val(valor.comuna);
$("input[name='telefono']").val(valor.telefono);
$("input[name='correo']").val(valor.correo);
if(valor.ncontactos > 0){
$("#btn_agregarcontacto button").prop("disabled",true);
$("#form_agregarcontacto").show();
ncontactos=0;
$.each(valor.contactos,function(index2,valor2){
ncontactos++;
$("#tb_agregarcontacto tbody").append("<tr id='con_fila"+index2+"'><td>"+ncontactos+"</td><td>"+valor2.nombre+"</td><td>"+valor2.telefono+"</td><td>"+valor2.correo+"</td><td>"+valor2.cargo+"</td><td class='text-center text-red'><span class='pointer' onclick='eliminarcontacto("+index2+")'><i class='fa fa-trash-o' aria-hidden='true'></i></span></td></tr>");
});

}
$("#tb_listadoproveedores").hide();
$("#form_editarproveedor").show();
}
});
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

function AgregarContacto(){
$("#btn_agregarcontacto button").prop("disabled",true);
$("#form_agregarcontacto").show();
}
function removercontacto(id){
$("#con_fila"+id+", #idcon"+id+"").remove();
}
function eliminarcontacto(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'BorrarContactoProveedor',contacto:''+id+'',retornar:'no'},function(data){
$("#con_fila"+id+"").remove();
ncontactos = $("#tb_agregarcontacto tbody tr").length;
if(ncontactos==0){
$("#btn_agregarcontacto button").prop("disabled",false);
$("#form_agregarcontacto").hide();
}
});
}

function eliminarproveedor(id){
$.each(proveedores,function(index,valor){
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
$("#mlistproveedores .modal-header").removeClass("header-inverse").addClass("header-rojo");
$("#mlistproveedores .modal-title").html("Eliminar Proveedor");
$("#mlistproveedores .modal-body").html(det_cli);
$("#mlistproveedores .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' onclick='BorrarProveedor(\""+id+"\")'>Confirmar</button>")
$("#mlistproveedores").modal("toggle");
}
});
}

function BorrarProveedor(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'BorrarProveedor',proveedor:''+id+'',retornar:'no'},function(data){
$("#mlistproveedores").modal("hide");
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
