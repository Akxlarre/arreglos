<!-- modal -->
<div class="modal" id="mlistjor">
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
<div class="row top20">
<div class="col-md-6">
<div class="box box-success box-solid">
<div class="box-header with-border"><h3 class="box-title">Listado de Jornadas Activas</h3>
</div>
<div class="box-body table-responsive">
<table class="table table-bordered table-striped" id="tabjini">
<thead>
<th>Rut</th>
<th>Conductor</th>
<th>Cliente</th>
<th>Patente</th>
<th>Fecha/Hora</th>
<th class="text-center"></th>
</thead>
<tbody >
</tbody>
</table>
</div>
</div>
</div>
<div class="col-md-6">
<div class="box box-danger box-solid">
<div class="box-header with-border"><h3 class="box-title">Listado de Jornadas Terminadas</h3>
</div>
<div class="box-body table-responsive">
<table class="table table-bordered table-striped" id="tabjfin">
<thead>
<th>Rut</th>
<th>Conductor</th>
<th>Cliente</th>
<th>Patente</th>
<th>Fecha/Hora</th>
</thead>
<tbody >
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<script>
$(function(){
getTabJornadasCon();
});
window.jornadas;
function getTabJornadasCon(){
$("#tabjini tbody").html("<tr><td colspan=6 class='text-center'>Cargando ...<i class='fa fa-spinner fa-spin fa-3x fa-fw'></i></td></tr>");
$("#tabjfin tbody").html("<tr><td colspan=5 class='text-center'>Cargando ...<i class='fa fa-spinner fa-spin fa-3x fa-fw'></i></td></tr>");
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabJornadasCon',retornar:'no'},function(data){
// console.log(data);
datos = $.parseJSON(data);
jornadas=datos;
inicios = datos["inicio"];
finales = datos["final"];
// console.log(inicios);
ini="";
$.each(inicios,function(index,valor){
ini+="<tr><td>"+valor.rut+"</td><td>"+valor.conductor+"</td><td>"+valor.cliente+"</td><td>"+valor.patente+"</td><td>"+valor.fechahora+"</td><td class='text-center'><button type='button' class='btn btn-danger btn-rounded' onclick='terminarJornada(\""+index+"\")'>Terminar Jornada</button></td></tr>";
});
$("#tabjini tbody").html(ini);
$('#tabjini').DataTable({
"language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
"paging": true,
"order": [[4, "desc" ]],
"lengthChange": true,
"lengthMenu": [[20,-1], [20,"Todos"]],
"pageLength":20,
"searching": true,
"ordering": true,
"info": true,
"autoWidth": false
});
fin="";
$.each(finales,function(index,valor){
fin+="<tr><td>"+valor.rut+"</td><td>"+valor.conductor+"</td><td>"+valor.cliente+"</td><td>"+valor.patente+"</td><td>"+valor.fechahora+"</td></tr>";
});
$("#tabjfin tbody").html(fin);
$('#tabjfin').DataTable({
"language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
"paging": true,
"order": [[4, "desc" ]],
"lengthChange": true,
"lengthMenu": [[20,-1], [20,"Todos"]],
"pageLength":20,
"searching": true,
"ordering": true,
"info": true,
"autoWidth": false
});
/*
filas="";
x=0;
$.each(datos,function(index,valor){
x++;
filas+="<tr id='conduc"+index+"'><td>"+x+"</td><td>"+valor.cliente+"</td><td>"+valor.pin+"</td><td>"+valor.rut+"</td><td>"+valor.apaterno+"</td><td>"+valor.amaterno+"</td><td>"+valor.nombre+"</td><td class='text-center'><span class='btn btn-warning btn-circle' onclick='EditarConductor(\""+index+"\");'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></span></td><td class='text-center'><span class='btn btn-danger btn-circle' onclick='EliminarConductor(\""+index+"\")'><i class='fa fa-trash-o' aria-hidden='true'></i></span></td></tr>";
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
});*/
});
}
function terminarJornada(index){
inicios = jornadas["inicio"];
datojornada = inicios[index];
$("#mlistjor .modal-dialog").css({'width':'50%'});
$("#mlistjor .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mlistjor .modal-title").html("Finalizar Jornada");
$("#mlistjor .modal-body").html("Realmente desea terminar la jornada  : <br><b>"+datojornada["conductor"]+"<br>"+datojornada["patente"]+"<br>"+datojornada["fechahora"]+"</b>");
$("#mlistjor .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='finalizarJornada(\""+datojornada["idconductor"]+"\",\""+datojornada["idpatente"]+"\");' class='btn btn-success btn-rounded'>Finalizar</button>");
$("#mlistjor").modal("toggle");	
}
function finalizarJornada(conductor,patente){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'finalizarJornadaManual',hdj_conductor:conductor,hdj_patente:patente,retornar:'no'},function(data){
getTabJornadasCon();

});

}
</script>