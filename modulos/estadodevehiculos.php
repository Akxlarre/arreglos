<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
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
<div class="row top20">
<div class="col-md-6" id="nedv">
<div class="row">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Estado</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnuevoestado">
<input type="hidden" name="operacion" value="nuevoodv"/>
<input type="hidden" name="idodv"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="form-group row">
<div class="col-sm-6"><label>Estado</label>
<input type="text" name="nombre" class="form-control"></div>
<div class="col-sm-4"><button type="submit" style="margin-top: 32px;" class="btn btn-success btn-rounded" id="btnunidad">Registrar Estado</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger oculto btn-rounded" id="btn_CODV" onclick="CancelarEODV();"><i class="fa fa-times" aria-hidden="true"></i></button></div>
</div>
</form>

<table class="table table-bordered table-striped table-sm">
<thead class="thead-dark">
<th>N°</th>
<th>Estado</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sql="select * from observacionesvehiculos";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
?>
<tr id="odv<?=$fila["odv_id"];?>">
<td><?=$x;?></td>
<td><?=$fila["odv_observacion"];?></td>
<td class="text-center" width="50"><button class="btn btn-sm btn-warning btn-circle" onclick="editarODV('<?=$fila["odv_id"];?>','<?=$fila["odv_observacion"];?>')"><i class="fa fa-edit"></i></button></td>
<td class="text-center" width="50"><button class="btn btn-sm btn-danger btn-circle" onclick="quitarODV('<?=$fila["odv_id"];?>','<?=$fila["odv_observacion"];?>')"><i class="fa fa-trash"></i></button></td>
</tr>
<?php } ?>
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
function quitarODV(id,nombre){
$("#medv .modal-dialog").css({'width':'30%'});
$("#medv .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#medv .modal-title").html("Eliminar Estado");
$("#medv .modal-body").html("Realmente desea eliminar este estado : <b>"+nombre+"</b>");
$("#medv .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarODV(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#medv").modal("toggle");
}
function eliminarODV(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarodv',idodv:''+id+'',retornar:'no'},function(data){
$("#odv"+id+"").remove();
$("#medv").modal("hide");
});
}

function editarODV(id,nombre){
$("#fnuevoestado").find("input[name='operacion']").val("editarodv");
$("#fnuevoestado").find("input[name='idodv']").val(id);
$("#fnuevoestado").find("input[name='nombre']").val(nombre);
$("#fnuevoestado").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar");
$("#btn_CODV").show();
}
function CancelarEODV(){
$("#fnuevoestado").find("input[name='operacion']").val("nuevoodv");
$("#fnuevoestado").find("input[name='idodv']").val("");
$("#fnuevoestado").find("input[name='nombre']").val("");
$("#fnuevoestado").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Estado");
$("#btn_CODV").hide();
}

</script>

  