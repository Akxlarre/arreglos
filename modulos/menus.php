
<section class="content">
<div class="row submenu">
<div class="col-md-12">
<button type="button" onclick="nuevomenu();" class="btn btn-success btn-rounded" id="btn_nuevomenu"><i class="fa fa-plus" aria-hidden="true"></i> Agregar Menu</button>
</div>
</div>
<div class="row top20" id="listadomenus">
<div class="col-md-10">
<div class="box box-success box-solid">
<div class="box-header with-border"><h3 class="box-title">Listado de menus registrados</h3></div>
<div class="box-body">
<table class="table table-bordered table-striped table-sm">
<thead class="thead-dark">
<th>N°</th>
<th>Nombre</th>
<th>Icono</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sql="select * from menus order by nombre";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
$id=$fila["id"];
$nombre=$fila["nombre"];
?>
<tr id="menu<?=$id;?>">
<td><?=$x;?></td>
<td id="nombre<?=$id;?>"><?=$nombre;?></td>
<td id="icono<?=$id;?>"><?=$fila["icono"];?></td>
<td class="text-center"><span class="btn btn-warning btn-circle" onclick="editarmenu(<?=$id;?>)"><i class="fa fa-edit"></i></span></td>
<td class="text-center"><span class="btn btn-danger btn-circle" onclick="eliminarmenu(<?=$id;?>)"><i class="fa fa-trash"></i></span></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
<div class="row oculto top20" id="nuevomenu">
<div class="col-md-10">
<div class="box box-success box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Menu</h3>
<div class="box-tools pull-right">
<span class="pointer text-red" onclick="updateurl('index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>')"><i class="fa fa-2x fa-times-circle-o"></i></span>
</div>
</div>
<div class="box-body">
<input type="hidden" id="idmenu"/>
<form action="operaciones.php" method="post" class="form-horizontal">
<input type="hidden" name="operacion" value="nuevomenu"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/> 
<div class="form-group">
<label class="col-sm-2 control-label txtleft">Menu</label>
<div class="col-sm-4"><input type="text" name="menu" class="form-control"></div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label txtleft">Icono</label>
<div class="col-sm-4"><input type="text" name="icono" class="form-control"></div>
</div>
<div class="form-group">
<div class="col-sm-offset-2 col-sm-4"><button type="submit" class="btn btn-success btn-rounded">Registrar</button></div>
</div>
</form>
</div>
</div>
</div>
</div>
<div class="row oculto top20" id="editarmenu">
<div class="col-md-10">
<div class="box box-success box-solid">
<div class="box-header with-border"><h3 class="box-title">Editar Menu</h3>
<div class="box-tools pull-right">
<span class="pointer text-red" onclick="updateurl('index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>')"><i class="fa fa-2x fa-times-circle-o"></i></span>
</div>
</div>
<div class="box-body">
<input type="hidden" id="idmenu"/>
<form class="form-horizontal">
<div class="form-group">
<label class="col-sm-2 control-label txtleft">Menu</label>
<div class="col-sm-4"><input type="text" id="menuaeditar" class="form-control"></div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label txtleft">Icono</label>
<div class="col-sm-4"><input type="text" id="iconoaeditar" class="form-control"></div>
</div>
<div class="form-group">
<div class="col-sm-offset-2 col-sm-4"><button type="button" class="btn btn-success btn-rounded" onclick="updatemenu()">Editar</button></div>
</div>
</form>
</div>
</div>
</div>
</div>
</section>

<script>
function nuevomenu(){
$("#listadomenus").hide();
$("#editarmenu").hide();
$("#btn_nuevomenu").attr("disabled",false);
$("#nuevomenu").show();
}

function eliminarmenu(id){
$.get('operaciones.php',{bncnr:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarmenu',idmenu:''+id+'',retornar:'no'}, function(data) {
console.log("borrado");
$("#menu"+id+"").hide();
});	
}

function editarmenu(id){
$("#listadomenus").hide();
$("#nuevomenu").hide();
$("#btn_nuevomenu").attr("disabled",true);
$("#editarmenu").show();
$("#idmenu").val(id);
menu=$("#nombre"+id+"").text();
icono=$("#icono"+id+"").text();
$("#menuaeditar").val(menu);
$("#iconoaeditar").val(icono);
}

function updatemenu(){
menu=$("#menuaeditar").val();
icono=$("#iconoaeditar").val();
idmenu=$("#idmenu").val();
$.get('operaciones.php',{bncnr:''+Math.floor(Math.random()*9999999)+'',operacion:'editarmenu',nombre:''+menu+'',icon:''+icono+'',id:''+idmenu+'',retornar:'no'}, function(data) {
console.log("actualizado");
$("#menuaeditar").val("");
$("#iconoaeditar").val("");
$("#idmenu").val("");
$("#editarmenu").hide();
$("#nombre"+idmenu+"").text(menu);
$("#icono"+idmenu+"").text(icono);
$("#btn_nuevomenu").attr("disabled",false);
$("#listadomenus").show();
});	
	
}
</script>


