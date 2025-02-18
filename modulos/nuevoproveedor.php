<div class="content">
<div class="row top20">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Proveedor</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal">
<input type="hidden" name="operacion" value="nuevoproveedor"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Rut</label>
            <div class="col-sm-9">
                <input type="text" name="rut" class="form-control" onblur="validarProveedor(this)">
                <div class="col-sm-6 col-lg-6 col-offset-2 oculto" id="proveedor_existe"></div>
            </div>
            
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Razon Social</label>
            <div class="col-sm-9">
                <input type="text" name="razonsocial" class="form-control">
            </div>
            
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Giro</label>
            <div class="col-sm-9">
                <input type="text" name="giro" class="form-control">
            </div>
            
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Región</label>
            <div class="col-sm-9">
                <? htmlselect('region','region','regiones','id','region|ordinal','','','','id','getComunas()','','si','no','no');?>
            </div>
           
        </div>
        
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Comuna</label>
            <div class="col-sm-9">
                <select name="comuna" id="comuna" class="form-control"></select>
                <div class="oculto col-sm-1 text-green padtop7 txtleft" id="loadaop"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i></div>
            </div>
            
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Dirección</label>
            <div class="col-sm-9">
                <input type="text" name="direccion" class="form-control">
            </div>
            
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Teléfono</label>
            <div class="col-sm-9">
                <input type="text" name="telefono" class="form-control">
            </div>
            
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Correo</label>
            <div class="col-sm-9">
                <input type="text" name="correo" class="form-control">
            </div>
            
        </div>
    </div>
    <div class="col-md-12 oculto" id="form_agregarcontacto">
        <h3>Agregar Contacto</h3>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <label for="nombrecontacto" class="sr-only">Nombre</label>
                    <input type="text" class="form-control" id="nombrecontacto" placeholder="Nombre" value="">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-2">
                <label for="telefonocontacto" class="sr-only">Teléfono</label>
                <input type="text" class="form-control" id="telefonocontacto" placeholder="Teléfono" value="">
            </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-2">
                <label for="correocontacto" class="sr-only">Correo</label>
                <input type="text" class="form-control" id="correocontacto" placeholder="Correo" value="">
            </div>
            </div>
            <div class="col-md-2">
                
            <div class="form-group mb-2">
                <label for="cargocontacto" class="sr-only">Cargo</label>
                <input type="text" class="form-control" id="cargocontacto" placeholder="Nombre" value="">
            </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-success btn-circle top25" onclick="addcontacto();"><i class="fa fa-plus" aria-hidden="true"></i></button>
            <button type="button" class="btn btn-danger btn-circle top25" onclick="noagregarcontacto();"><i class="fa fa-times" aria-hidden="true"></i></button>

            </div>
        </div>
        
        <hr>
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
</div>

<button type="submit" id="btnregprov" class="btn btn-success btn-rounded">Registrar Proveedor</button>
<button type="button" class="btn btn-success btn-rounded top25" onclick="AgregarContacto();"><i class="fa fa-address-card-o" aria-hidden="true"></i> Agregar Contacto</button>


</form>
</div>
</div>
</div>
</div>
</div>
<script>
$("input[name='rut']").focus(function(){
$("#cliente_existe").hide();
});

function validarProveedor(e){
console.log($(e).val());
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'ExisteProveedor',rut:''+$(e).val()+'',retornar:'no'},function(data){
if(parseInt(data) > 0){
$("#proveedor_existe").html("<div class='callout callout-danger'><h4>El proveedor ya existe !</h4><p>El rut ingresado ya se encuentra asociado a un proveedor, favor verificar.</p></div>");
$("#proveedor_existe").show();
$("#btnregprov").attr("disabled",true);
}else{
$("#proveedor_existe").hide();
$("#btnregprov").attr("disabled",false);
}
});	
}

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