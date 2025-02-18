<div class="content">
<div class="row submenu">
<div class="col-md-12">
<button type="button" onclick="nuevomodulo();" class="btn btn-success btn-rounded" id="btn_nuevomodulo"><i class="fa fa-plus" aria-hidden="true"></i> Agregar Módulo</button>
</div>
</div>
<div class="row top20" id="listadomodulos">
<div class="col-md-10">
<div class="box box-success box-solid">
<div class="box-header with-border"><h3 class="box-title">Listado de módulos registrados</h3></div>
<div class="box-body">
<table class="table table-bordered table-striped table-sm">
<thead class="thead-dark">
<th>N°</th>
<th>Menu</th>
<th>Módulo</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sql="select * from modulos order by idmenu";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
$id=$fila["id"];
?>
<tr id="modulo<?=$id;?>">
<td><?=$x;?></td>
<td id="menu<?=$id;?>"><?=obtenervalor("menus","nombre","where id='".$fila["idmenu"]."'");?></td>
<td id="nombre<?=$id;?>"><?=$fila["nombre"];?></td>
<td class="text-center"><span class="btn btn-warning btn-circle" onclick="editarmodulo(<?=$id;?>)"><i class="fa fa-edit"></i></span></td>
<td class="text-center"><span class="btn btn-danger btn-circle" onclick="eliminarmodulo('<?=$id;?>')"><i class="fa fa-trash"></i></span></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>
</div>
</div>

<div class="row oculto top20" id="nuevomodulo">
<div class="col-md-10">
<div class="box box-success box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Módulo</h3>
<div class="box-tools pull-right">
<span class="pointer text-red" onclick="updateurl('index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>')"><i class="fa fa-2x fa-times-circle-o"></i></span>
</div>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal">
<input type="hidden" name="operacion" value="nuevomodulo"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="form-group">
<label class="col-sm-3 control-label txtleft">Nombre Modulo</label>
<div class="col-sm-4"><input type="text" name="modulo" class="form-control"/></div>
</div>
<div class="form-group">
<label class="col-sm-3 control-label txtleft">Menu</label>
<div class="col-sm-4"><? htmlselect('idmenu','idmenu','menus','id','nombre','','','','nombre asc','','','si','no','no');?></div>
</div>
<div class="form-group">
<div class="col-sm-offset-3 col-sm-4"><button type="submit" class="btn btn-success btn-rounded">Registrar Módulo</button></div>
</div>
</form>
</div>
</div>
</div>
</div>


<div class="row oculto top20" id="editarmodulo">
<div class="col-md-10">
<div class="box box-success box-solid">
<div class="box-header with-border"><h3 class="box-title">Editar Módulo</h3>
<div class="box-tools pull-right">
<span class="pointer text-red" onclick="updateurl('index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>')"><i class="fa fa-2x fa-times-circle-o"></i></span>
</div>
</div>
<div class="box-body">
<form class="form-horizontal">
<input type="hidden" id="idmodulo"/>
<div class="form-group">
<label class="col-sm-3 control-label txtleft">Nombre Modulo</label>
<div class="col-sm-4"><input type="text" id="moduloaeditar" class="form-control"/></div>
</div>
<div class="form-group">
<label class="col-sm-3 control-label txtleft">Menu</label>
<div class="col-sm-4"><?=htmlselect('menu','menuaeditar','menus','id','nombre','','','','nombre asc','','','si','no','no');?></div>
</div>
<div class="form-group">
<div class="col-sm-offset-3 col-sm-4"><button type="button" class="btn btn-success btn-rounded" onclick="updatemodulo()">Editar Módulo</button></div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<script>
function nuevomodulo(){
$("#listadomodulos").hide();
$("#editarmodulo").hide();
$("#btn_nuevomodulo").attr("disabled",true);
$("#nuevomodulo").show();
}

function eliminarmodulo(a){
var randomNo = Math.floor(Math.random()*9999999);
$.get("operaciones.php", {numero:''+randomNo+'',operacion:'borrarmodulo',idmodulo:''+a+'',retornar:'no'}
,function(data){
$("#modulo"+a+"").hide();
});
}
function editarmodulo(id){
$("#listadomodulos").hide();
$("#nuevomodulo").hide();
$("#btn_nuevomodulo").attr("disabled",true);
$("#editarmodulo").show();
$("#idmodulo").val(id);
modulo=$("#nombre"+id+"").text();
menu=$("#menu"+id+"").text();
$("#moduloaeditar").val(modulo);
$("#menuaeditar option:contains("+menu+")").prop('selected', true);
}

function updatemodulo(){
modulo=$("#moduloaeditar").val();
menu=$("#menuaeditar").val();
menuselect=$("#menuaeditar option:selected").text();
idmodulo=$("#idmodulo").val();
$.get('operaciones.php',{bncnr:''+Math.floor(Math.random()*9999999)+'',operacion:'editarmodulo',idmenu:''+menu+'',nombre:''+modulo+'',id:''+idmodulo+'',retornar:'no'}, function(data) {
$("#editarmodulo").hide();
$("#nombre"+idmodulo+"").text(modulo);
$("#menu"+idmodulo+"").text(menuselect);
$("#listamodulos").show();
$("#btn_nuevomodulo").attr("disabled",false);
});
}


</script>

