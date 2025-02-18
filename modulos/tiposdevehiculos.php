<!-- modal -->
<div class="modal" id="mtveh">
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
<div class="row top20">
<div class="col-md-8" id="ntveh">
<div class="row">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Tipo</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnuevotveh">
<input type="hidden" name="operacion" value="nuevotveh"/>
<input type="hidden" name="idtveh"/>
<input type="hidden" name="retornar" value="index.php?menu=<?php echo $_REQUEST["menu"];?>&idmenu=<?php echo $_REQUEST["idmenu"];?>"/>
<div class="form-group row">
<div class="col-sm-6"><label>Tipo</label>
<input type="text" name="nombre" class="form-control"></div>
<div class="col-sm-3"><button type="submit" class="btn btn-success btn-rounded" style="margin-top: 32px;" id="btntipo">Registrar Tipo</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger oculto btn-rounded" id="btn_CTVEH" onclick="CancelarETVEH();"><i class="fa fa-times" aria-hidden="true"></i></button></div>
</div>
</form>

<table class="table table-bordered table-striped table-sm">
<thead class="thead-dark">
<th>N°</th>
<th>Tipo</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sql="select * from tiposdevehiculos";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
?>
<tr id="tveh<?php echo $fila["tveh_id"];?>">
<td><?php echo $x;?></td>
<td><?php echo $fila["tveh_nombre"];?></td>
<td class="text-center" width="50"><button class="btn btn-warning btn-sm btn-circle" onclick="editarTVEH('<?php echo $fila['tveh_id'];?>','<?php echo $fila['tveh_nombre'];?>')"><i class="fa fa-edit"></i></button></td>
<td class="text-center" width="50"><button class="btn btn-danger btn-sm btn-circle" onclick="quitarTVEH('<?php echo $fila['tveh_id'];?>','<?php echo $fila['tveh_nombre'];?>')"><i class="fa fa-trash"></i></button></td>
</tr>
<?php
}
?>
</tbody>
</table>

</div>
</div>
</div>
</div>

</div>

</div>

</section> 

<script>
function quitarTVEH(id,nombre){
$("#mtveh .modal-dialog").css({'width':'50%'});
$("#mtveh .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mtveh .modal-title").html("Eliminar Tipo de Vehículo");
$("#mtveh .modal-body").html("Realmente desea eliminar este tipo : <b>"+nombre+"</b>");
$("#mtveh .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarTVEH(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#mtveh").modal("toggle");
}
function eliminarTVEH(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminartveh',idtveh:''+id+'',retornar:'no'},function(data){
$("#tveh"+id+"").remove();
$("#mtveh").modal("hide");
});
}

function editarTVEH(id,nombre){
$("#fnuevotveh").find("input[name='operacion']").val("editartveh");
$("#fnuevotveh").find("input[name='idtveh']").val(id);
$("#fnuevotveh").find("input[name='nombre']").val(nombre);
$("#fnuevotveh").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar");
$("#btn_CTVEH").show();
}
function CancelarETVEH(){
$("#fnuevotveh").find("input[name='operacion']").val("nuevotveh");
$("#fnuevotveh").find("input[name='idtveh']").val("");
$("#fnuevotveh").find("input[name='nombre']").val("");
$("#fnuevotveh").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Tipo");
$("#btn_CTVEH").hide();
}

</script>

  