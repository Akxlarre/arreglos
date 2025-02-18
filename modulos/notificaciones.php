<!-- modal -->
<div class="modal" id="medv">
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
<section class="content">
<div class="row" id="botonesNot">
<div class="col-md-12">
<div class='col-sm-6'><button type="button" class="btn btn-success btn-rounded" onclick="formNotificacion(1)">Inicio Jornada</button>&nbsp;&nbsp;<button type="button" class="btn btn-success btn-rounded" onclick="formNotificacion(2)">Sin Transmisión</button></div>
</div>
</div>

<div  id="finiciojornada" class="oculto">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Configurar Notificacón Inicio de Jornada</h3>
</div>
<div class="box-body">
<form action="#" method="post" class="form-horizontal" id="">
<input type="hidden" name="operacion" value=""/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<div class="col-sm-6">
<label>Cliente</label><br>
<? htmlselect('cliente','cliente','clientes','id','razonsocial','','','','id','getGrupos()','','si','no','no');?>
</div>
<div class="col-sm-6">
<label>Grupo</label><br>
<select name="grupo" id="grupo" class="form-control" onchange="vehxgrupo()"></select>
<div class="oculto col-sm-1 text-green padtop7 txtleft" id="loadaop"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i></div>
</div>
</div>
<div class="form-group" id="listadopatentes">
<div class="col-sm-6">
<table class="table table-bordered table-striped">
<thead><th>Patente</th><th></th></thead>
<tbody>
</tbody>
</table> 
</div>
</div>
</div>

<div class="col-md-6 oculto" id="crearnotificacion">
<div class="form-group">
<label class="col-sm-12">Crear Notificación para estas patentes : </label>
</div>
<div class="form-group">
<div class="col-sm-12">
<textarea name="grupopatentes" class="form-control rznone" rows=5 disabled></textarea>
</div>
</div>
<div class="form-group">
<div class="col-sm-5" >
<label>Nombre</label><br>
<input type="text" name="nombrecontacto" class="form-control">
</div>
<div class="col-sm-5">
<label>Correo</label><br>
<input type="text" name="correocontacto" class="form-control">
</div>
<div class="col-sm-2">
<button type="button" class="btn btn-success btn-circle top25" onclick="addcontacto();"><i class="fa fa-plus" aria-hidden="true"></i></button>
</div>

<div  id="inp_agregarcontactos">
</div>
<div class="col-sm-12 top20">
<table class="table table-bordered table-striped" id="tb_agregarcontacto">
<thead>
<th>#</th>
<th>Nombre</th>
<th>Correo</th>
<th>&nbsp;</th>
</thead>
<tbody>
</tbody>
</table>
</div>
<div class="col-sm-6 top20">
<button type="button" class="btn btn-success btn-rounded" onclick="guardarNotificacion();"><i class="fa fa-plus" aria-hidden="true"></i>Guardar</button>
<button type="button" class="btn btn-danger btn-rounded" onclick="cancelarNotificacion();"><i class="fa fa-times" aria-hidden="true"></i> Cancelar</button>
</div>
</div>
</div>
</div>
</form>
</div>
<div class="box-footer">
<button type='button' class='btn btn-danger btn-rounded pull-right' onclick='cancelarFormNot()'>Cancelar</button>
</div>
</div>
</div>
</div>



<div id="fsintransmision" class="oculto">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Configurar Notificacón Sin Transmisión</h3>
</div>
<div class="box-body">
<form action="#" method="post" class="form-horizontal" id="">
<input type="hidden" name="operacion" value=""/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<div class="col-sm-6">
<label>Cliente</label><br>
<? htmlselect('cliente','cliente','clientes','id','razonsocial','','','','id','getPatentesxCuenta()','','si','no','no');?>
</div>
</div>
<div class="form-group" id="listadopatentes">
<div class="col-sm-6">
<table class="table table-bordered table-striped">
<thead><th>Patente</th><th></th></thead>
<tbody>
</tbody>
</table> 
</div>
</div>
</div>
<div class="col-md-6 oculto" id="crearnotificacion">
<div class="form-group">
<label class="col-sm-12">Crear Notificación para estas patentes : </label>
</div>
<div class="form-group">
<div class="col-sm-12">
<textarea name="grupopatentes" class="form-control rznone" rows=5 disabled></textarea>
</div>
</div>
<div class="form-group">
<div class="col-sm-5" >
<label>Nombre</label><br>
<input type="text" name="nombrecontacto" class="form-control">
</div>
<div class="col-sm-5">
<label>Correo</label><br>
<input type="text" name="correocontacto" class="form-control">
</div>
<div class="col-sm-2">
<button type="button" class="btn btn-success btn-circle top25" onclick="addcontacto();"><i class="fa fa-plus" aria-hidden="true"></i></button>
</div>

<div  id="inp_agregarcontactos">
</div>
<div class="col-sm-12 top20">
<table class="table table-bordered table-striped" id="tb_agregarcontacto">
<thead>
<th>#</th>
<th>Nombre</th>
<th>Correo</th>
<th>&nbsp;</th>
<tbody>
</tbody>
</table>
</div>
<div class="col-sm-6 top20">
<button type="button" class="btn btn-success btn-rounded" onclick="guardarNotificacion(2);"><i class="fa fa-plus" aria-hidden="true"></i>Guardar</button>
<button type="button" class="btn btn-danger btn-rounded" onclick="cancelarNotificacion(2);"><i class="fa fa-times" aria-hidden="true"></i> Cancelar</button>
</div>
</div>

</div>
</div>
</form>
</div>
<div class="box-footer">
<button type='button' class='btn btn-danger btn-rounded pull-right' onclick='cancelarFormNot()'>Cancelar</button>
</div>
</div>
</div>

</div>

<div class="row top20 oculto" id="formNot">

</div>

<!-- listado de alertas -->
<div class="row top20" id="listadoalertas">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Notificaciones Configuradas</h3>
</div>
<div class="box-body">
<table class='table table-bordered table-striped' id="tbalertas">
<thead>
<th>Tipo</th>
<th>Cuenta</th>
<th>Grupo</th>
<th>Patentes</th>
<th>Contactos</th>
<th>&nbsp;</th>
</thead>
<tbody>
</tbody>
</table>
</div>
</div>
</div>

</div>
</section> 
<script>
$(function(){
getTabAlertas();


});
window.alertas;
function getTabAlertas(){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabAlertas',retornar:'no'},function(data){
alertas = $.parseJSON(data);
faler="";
$.each(alertas,function(index,valor){
alertapatentes=valor["patentes"];
alertacontactos=valor["contactos"];
switch(parseInt(valor.tipo)){
case 1:
tipo="INICIO DE JORNADA";
break;
case 2:
tipo="SIN TRANSMISIÓN";
break;

}
faler+="<tr><td>"+tipo+"</td><td>"+valor.cliente+"</td><td>"+valor.grupo+"</td><td><textarea class='form-control rznone' rows=5 disabled>";
muestrapat="";
$.each(alertapatentes,function(index,valor){
muestrapat+=","+valor;
});
muestrapat = muestrapat.substring(1);
faler+=muestrapat+"</textarea></td><td>";
muestracon="<table class='table table-bordered table-striped'><thead><th>Nombre</th><th>Correo</th></thead><tbody>";
$.each(alertacontactos,function(index,valor){
muestracon+="<tr><td>"+valor.nombre+"</td><td>"+valor.correo+"</td></tr>";
});
muestracon+="</tbody></table></td>";

faler+=muestracon+"<td><button type='button' class='btn btn-danger  btn-rounded' onclick='quitarConfiguracion(\""+index+"\")'>Eliminar Notificación</button></td></tr>";
// console.log(alertacontactos);

});
$("#tbalertas tbody").html(faler);
$('#tbalertas').DataTable({
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

function formNotificacion(tipo){
if(parseInt(tipo)==1){
form=$("#finiciojornada").html();
}else{
form=$("#fsintransmision").html();
}
$("#listadoalertas").hide();
$("#formNot").html(form).show();
}


function cancelarFormNot(){
$("#formNot").html("").hide();
$("#listadoalertas").show();

}
 
window.patentes;
function getPatentesxCuenta(){
id =$("#formNot #cliente").val();
console.log(id);
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getVehxCuenta',idcliente:''+id+'',retornar:'no'},function(data){
console.log(data);
datos=$.parseJSON(data);
patentes=datos["vehiculos"];
pxg="<tr><td>Seleccionar Todas</td><td class='text-center'><input type='checkbox' onchange='marcarTodas(this)'></td></tr>";
$.each(patentes,function(index,valor){
pxg+="<tr><td>"+valor.patente+"</td><td class='text-center'><input type='checkbox'  class='checkpatente' value="+index+"></td></tr>";
});
pxg+="<tr><td colspan=2 class='text-right'><button type='button' class='btn btn-success btn-rounded' onclick='formnotificaciones()'>Agregar Notificadores</button></td></tr>";
$("#formNot #listadopatentes tbody").html(pxg);
});

}
function vehxgrupo(){
id=$("#formNot #grupo").val();
console.log(id);
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getVehxGrupo',idgrupo:''+id+'',retornar:'no'},function(data){
datos=$.parseJSON(data);
patentes=datos["vehiculos"];
pxg="<tr><td>Seleccionar Todas</td><td class='text-center'><input type='checkbox' onchange='marcarTodas(this)'></td></tr>";
$.each(patentes,function(index,valor){
pxg+="<tr><td>"+valor.patente+"</td><td class='text-center'><input type='checkbox'  class='checkpatente' value="+index+"></td></tr>";
});
pxg+="<tr><td colspan=2 class='text-right'><button type='button' class='btn btn-success btn-rounded' onclick='formnotificaciones()'>Agregar Notificadores</button></td></tr>";
$("#formNot  #listadopatentes tbody").html(pxg);
/*
alertas=datos["alertas"];
calert="";
$.each(alertas,function(index,valor){
alertapatentes=valor["patentes"];
alertacontactos=valor["contactos"];
calert+="<div class='col-sm-10 top20'><div class='box'><div class='row'><div class='col-sm-12'><textarea class='form-control rznone' rows=5 disabled>";
muestrapat="";
$.each(alertapatentes,function(index,valor){
muestrapat+=","+valor;
});
muestrapat = muestrapat.substring(1);
calert+=muestrapat+"</textarea></div></div>";
muestracon="<div class='row top20'><div class='col-sm-12'><table class='table table-bordered table-striped'><thead><th>Nombre</th><th>Correo</th><th></th></thead><tbody>";
$.each(alertacontactos,function(index,valor){
muestracon+="<tr><td>"+valor.nombre+"</td><td>"+valor.correo+"</td><td></td></tr>";
});
muestracon+="</tbody></table></div></div>";

calert+=muestracon+"<div class='row top20'><div class='col-sm-12'><button type='button' class='btn btn-danger  btn-rounded' onclick='quitarConfiguracion(\""+index+"\")'>Eliminar Configuración de Notificación</button></div></div></div></div>";
// console.log(alertacontactos);
});
$("#formNot #listadoalertas").html(calert);
*/
});

}


function marcarTodas(e){
if($(e).is(":checked")){
$("#formNot .checkpatente").attr("checked",true);
}else{
$("#formNot .checkpatente").attr("checked",false);
}

// var prod = [];
// $(".checpro:checked").each(function () {
// indexpro=$(this).val();
// prod.push({"id":productos[indexpro]["id"],"idtipo":productos[indexpro]["idtipo"],"idpro":productos[indexpro]["idproducto"],"cantidad":productos[indexpro]["cantidad"],"detalle":productos[indexpro]["detalle"],"valor":productos[indexpro]["valorunitario"],"total":productos[indexpro]["total"]});	

// });
	
}


window.patnot;
function formnotificaciones(){
patnot=[];
grupopatentes="";
$("#formNot .checkpatente:checked").each(function () {
index=$(this).val();
idpatente=patentes[index]["idveh"];
patente = patentes[index]["patente"];
grupopatentes+=","+patente ;
patnot.push({"id":idpatente,"patente":patente});	
});
grupopatentes = grupopatentes.substring(1);
$("#formNot textarea[name='grupopatentes']").val(grupopatentes);
$("#formNot #listadopatentes").hide();
$("#formNot #crearnotificacion").show();
}


function getGrupos(){
idcliente=$("#formNot #cliente").val();
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getGruposCliente',id:''+idcliente+'',retornar:'no'},function(data){
datos=$.parseJSON(data);
sgrupo="<option value=0>SELECCIONAR</option>";
$.each(datos,function(index,valor){
sgrupo+="<option value="+valor.id+">"+valor.nombre+"</option>";
});

$("#formNot #grupo").html(sgrupo);
});
}

function addcontacto(){
var ncontactos = $("#formNot #tb_agregarcontacto tbody tr").length;
ncontactos=ncontactos+1;
con_nombre=$("#formNot input[name='nombrecontacto']").val();
con_correo=$("#formNot input[name='correocontacto']").val();

$("#formNot #tb_agregarcontacto tbody").append("<tr id='con_fila"+ncontactos+"'><td>"+ncontactos+"</td><td>"+con_nombre+"</td><td>"+con_correo+"</td><td class='text-center text-red'><span class='pointer' onclick='removercontacto("+ncontactos+")'><i class='fa fa-trash-o' aria-hidden='true'></i></span></td></tr>");
$("#formNot #inp_agregarcontactos").append("<input type='hidden' id='idcon"+ncontactos+"' name='contactos[]' value=\""+con_nombre+"|"+con_correo+"\">");
$("#formNot input[name='nombrecontacto']").val("");
$("#formNot input[name='correocontacto']").val("");
}
function removercontacto(id){
$("#formNot #con_fila"+id+", #formNot #idcon"+id+"").remove();
}
function cancelarNotificacion(tipo){
$("#formNot #tb_agregarcontacto tbody, #inp_agregarcontactos").html("");
$("#formNot .checkpatente").attr("checked",false);
$("#formNot textarea[name='grupopatentes']").val("");
$("#formNot #crearnotificacion").hide();
$("#formNot #listadopatentes").show();
}

function guardarNotificacion(tipo){
datagn={};
datagn["cliente"]=$("#formNot #cliente").val();
if(parseInt(tipo)==1){
datagn["grupo"]=$("#grupo").val();
datagn["tipo"]=1;
}else{
datagn["grupo"]=0;
datagn["tipo"]=2;	
}
contactos=[];
$("#formNot input[name='contactos[]']").each(function(e){
datoscontacto = $(this).val();
sepcontacto = datoscontacto.split("|");
nombrec=sepcontacto[0];
correoc=sepcontacto[1];
contactos.push({nombre:nombrec,correo:correoc});
});
datagn["contactos"]=contactos;
datagn["patentes"]=patnot;
json = JSON.stringify(datagn);
// console.log(json);
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'registrarConfigAlertaIJ',alerta:json,retornar:'no'},function(data){
//console.log(data);
 location.reload();
}); 

}

function quitarConfiguracion(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarConfigAlertaIJ',alerta:id,retornar:'no'},function(data){
location.reload();	
});	
}

</script>