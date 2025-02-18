<!-- modal -->
<div class="modal" id="mlistcon">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title"></h4>
</div>
<div class="modal-body"></div>
<div class="modal-footer">
</div>
</div>
</div>
</div>
<!-- fin modal -->

<div class="content">
<div class="row top20" id="tb_listadoconductores">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Listado de Conductores</h3>
</div>
<div class="box-body">
<table class="table table-bordered table-striped" id="tabcon">
<thead>
<th>#</th>
<th>Cliente</th>
<th>Pin</th>
<th>Rut</th>
<th>Nombre</th>
<th>A.Paterno</th>
<th>A.Materno</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody >
</tbody>
</table>
</div>
</div>
</div>
</div>

<div class="row top20 oculto" id="cf_editarcon">
<div class="col-md-12">
<div class="box box-warning box-solid">
<div class="box-header with-border"><h3 class="box-title">Editar Conductor</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal">
<input type="hidden" name="operacion" value="editarconductor"/>
<input type="hidden" name="idconductor"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">PIN</label>
<div class="col-sm-2"><input type="text" name="pin" class="form-control">
</div>
</div>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">Cliente</label>
<div class="col-sm-4">
<? htmlselect('cliente','cliente','clientes','id','razonsocial','','','','razonsocial','','','si','no','no');?>
</div>
</div>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">Rut</label>
<div class="col-sm-2"><input type="text" name="rut" class="form-control">
</div>
</div>
<div class="form-group">
<label class="contro-label col-sm-2 txtleft">Apellido Paterno</label>
<div class="col-sm-4"><input type="text" name="apaterno" class="form-control">
</div>
</div>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">Apellido Materno</label>
<div class="col-sm-4"><input type="text" name="amaterno" class="form-control">
</div>
</div>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">Nombre</label>
<div class="col-sm-4"><input type="text" name="nombre" class="form-control">
</div>
</div>

<div class="form-group">
<div class="col-sm-3 col-sm-offset-2"><button type="submit" class="btn btn-success btn-rounded">Editar Conductor</button></div>
</div>

</form>
</div>
</div>
</div>
</div>

</div>

<script>
$(function(){
getTabConductores();
});
window.conductores;
function getTabConductores(){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabConductores',retornar:'no'},function(data){
console.log(data);
datos = $.parseJSON(data);
conductores=datos;
filas="";
x=0;
$.each(datos,function(index,valor){
x++;
filas+="<tr id='conduc"+index+"'><td>"+x+"</td><td>"+valor.cliente+"</td><td>"+valor.pin+"</td><td>"+valor.rut+"</td><td>"+valor.nombre+"</td><td>"+valor.apaterno+"</td><td>"+valor.amaterno+"</td><td class='text-center'><span class='btn btn-warning btn-circle' onclick='EditarConductor(\""+index+"\");'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></span></td><td class='text-center'><span class='btn btn-danger btn-circle' onclick='EliminarConductor(\""+index+"\")'><i class='fa fa-trash-o' aria-hidden='true'></i></span></td></tr>";
});
$("#tabcon tbody").html(filas);
$('#tabcon').DataTable({
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



function EditarConductor(index){
conductor=conductores[index];
$("input[name='idconductor']").val(conductor.idcon);
$("input[name='pin']").val(conductor.pin);
$("#cliente").val(conductor.idcliente);
$("input[name='rut']").val(conductor.rut);
$("input[name='apaterno']").val(conductor.apaterno);
$("input[name='amaterno']").val(conductor.amaterno);
$("input[name='nombre']").val(conductor.nombre);
$("#tb_listadoconductores").hide();
$("#cf_editarcon").show();
}
function EliminarConductor(index){
conductor=conductores[index];
$("#mlistcon .modal-dialog").css({'width':'50%'});
$("#mlistcon .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mlistcon .modal-title").html("Eliminar Conductor");
$("#mlistcon .modal-body").html("Realmente desea eliminar este Conductor : <b>"+conductor["nombre"]+" "+conductor["apaterno"]+""+conductor["amaterno"]+"</b>");
$("#mlistcon .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarCON(\""+index+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#mlistcon").modal("toggle");
}

function eliminarCON(index){
conductor=conductores[index];	
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarconductor',idcon:''+conductor["idcon"]+'',retornar:'no'},function(data){
$("#conduc"+index+"").remove();
$("#mlistcon").modal("hide");
});
}



</script>
