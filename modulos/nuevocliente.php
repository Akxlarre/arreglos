


<div class="content">
<div class="alert alert-success oculto alert-dismissible" id="clienteok">
<!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
<h4><i class="icon fa fa-warning"></i>El cliente se ha registrado exitosamente. </h4>
</div>
<div class="alert alert-danger oculto alert-dismissible" id="clienteerror">
<!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
<h4><i class="icon fa fa-warning"></i>Error al registrar cliente. </h4>
</div>
<div class="row top20">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Cliente</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" onsubmit="return validarCLI()">
<input type="hidden" name="operacion" value="nuevocliente"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="form-group">
<div class="col-sm-2 col-lg-2">
<label>Rut cliente</label><br>
<input type="text" name="rut" class="form-control">
</div>
<div class="col-sm-3 col-lg-3">
<label>Cuenta</label><br>
<input type="text" name="cuenta" class="form-control">
</div>
<div class="col-sm-6 col-lg-6 col-offset-2 oculto" id="cliente_existe">

</div>

</div>
<div class="form-group">
<div class="col-sm-6">
<label>Razon Social</label><br>
<input type="text" name="razonsocial" class="form-control" requerid>
</div>
<div class="col-sm-3 col-lg-3">
<label>Representante legal</label><br>
<input type="text" name="rlegal" class="form-control">
</div>
<div class="col-sm-3 col-lg-3">
<label>Rut representante</label><br>
<input type="text" name="rrut" class="form-control">
</div>
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
<div class="col-sm-6 col-lg-6">
<label>Giro</label><br>
<input type="text" name="giro" class="form-control"></div>
</div>
</div>
<div class="form-group">
<div class="col-sm-3">
<label>Nombre</label><br>
<input type="text" name="nombre" class="form-control">
</div>
<div class="col-sm-3" >
<label>Usuario WS</label><br>
<input type="text" name="usuariows" class="form-control" requerid>
</div>
<div class="col-sm-3">
<label>Clave WS</label><br>
<input type="password" name="clavews" class="form-control" requerid>
</div>
<div class="col-sm-3">
<label>Nombre WS</label><br>
<input type="text" name="nombrews" class="form-control" requerid>
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


<div class="row">
    <div class="col-sm-3 col-lg-3 top25">
        <button type="submit" class="btn btn-success btn-rounded">Registrar Cliente</button>
    </div>
    <div class="col-sm-3 col-lg-3 top25" id="btn_agregarcontacto">
        <button type="button" class="btn btn-success btn-rounded" onclick="AgregarContacto();"><i class="fa fa-address-card-o" aria-hidden="true"></i> Agregar Contacto</button>
    </div>
</div>
<!-- <div class="form-group">

</div> -->

</form>
</div>
</div>
</div>
</div>
</div>
<script>
$(function(){
var urlactual = window.location;
//console.log(urlactual["search"]);
var ultimaclavevalor = urlactual["search"].lastIndexOf("&"); 
//console.log(ultimaclavevalor);
estado=urlactual["search"].substring(ultimaclavevalor + 1, ultimaclavevalor.length);
sepestado = estado.split("=");
nuevocliente=sepestado[1];
if(nuevocliente=="OK"){
    setTimeout(function(){ 
    $("#clienteok").fadeIn(2000).fadeOut(2000);
    history.pushState(null, "", "index.php?menu=nuevocliente&idmenu=80");
    }, 100);
}
else if(nuevocliente=="ERROR"){
    setTimeout(function(){ 
    $("#clienteerror").fadeIn(2000).fadeOut(2000);
    history.pushState(null, "", "index.php?menu=nuevocliente&idmenu=80");
    }, 100);
}
});

function validarCLI(){
if($("input[name='razonsocial']").val() == ""){
alert("Error al registrar cliente, falta completar el campo Razón Social");
$("input[name='razonsocial']").addClass("input-error");
return false;
}else{
$("input[name='razonsocial']").removeClass("input-error");	
}

if($("input[name='usuariows']").val()==""){
alert("Error al registrar cliente, falta completar el campo Usuario WS");
$("input[name='usuariows']").addClass("input-error");
return false;	
}else{
$("input[name='usuariows']").removeClass("input-error");		
}

if($("input[name='clavews']").val()==""){
alert("Error al registrar cliente, falta completar el campo Clave WS");
$("input[name='clavews']").addClass("input-error");
return false;	
}else{
$("input[name='clavews']").removeClass("input-error");		
}

if($("input[name='nombrews']").val()==""){
alert("Error al registrar cliente, falta completar el campo Nombre WS");
$("input[name='nombrews']").addClass("input-error");
return false;	
}else{
$("input[name='nombrews']").removeClass("input-error");		
}

var form_data = new FormData();
form_data.append('operacion','ValidarCliente');
form_data.append('razonsocial', $("input[name='razonsocial']").val());
form_data.append('nombrews', $("input[name='nombrews']").val());
form_data.append('retornar','no');

$.ajax({
url: 'operaciones.php', //ruta archivo operaciones
dataType: 'text',  // tipo de datos
async:false,
cache: false,
contentType: false,
processData: false,
data: form_data,
type: 'post',
success: function(respuesta){
if(parseInt(respuesta) > 0){
alert("El cliente "+$("input[name='razonsocial']").val()+" ya se encuentra registrado");
$("input[name='razonsocial']").val("").addClass("input-error");
$("input[name='usuariows'], input[name='clavews'], input[name='nombrews']").val("");
retornar=false;
}else {
retornar=true;
}
}
});
return retornar;
//console.log(retornar);
//return false;
}

$("input[name='rut']").focus(function(){
$("#cliente_existe").hide();
});
// funcion para validar rut de cliente, comentada por ahora 
/* $("input[name='rut']").blur(function(){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'ExisteCliente',rut:''+$(this).val()+'',retornar:'no'},function(data){
if(parseInt(data) > 0){
$("#cliente_existe").html("<div class='callout callout-danger'><h4>El cliente ya existe !</h4><p>El rut ingresado ya se encuentra asociado a un cliente, favor verificar.</p></div>");
$("#cliente_existe").show();
}else{
$("#cliente_existe").hide();
}
});
}); */

function AgregarContacto(){
$("#btn_agregarcontacto button").prop("disabled",true);
$("#form_agregarcontacto").show();
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

function removercontacto(id){
$("#con_fila"+id+", #idcon"+id+"").remove();
}
function noagregarcontacto(){
$("#tb_agregarcontacto tbody, #inp_agregarcontactos").html("");
$("#btn_agregarcontacto button").prop("disabled",false);
$("#form_agregarcontacto").hide();
}




function getComunas(){
idregion=$("#region option:selected").val();
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getComunas',region:''+idregion+'',retornar:'no'},function(data){
console.log(data);
$("#comuna").html(data);
});
}
</script>