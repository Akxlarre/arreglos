<!-- modal -->
<div class="modal" id="mmar">
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
<div class="col-md-8" id="nmarca">
<div class="row">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Servicio</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnuevamar">
<input type="hidden" name="operacion" value="nuevoservicio"/>
<input type="hidden" name="idmar"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="form-group">
<div class="col-sm-6"><label>Servicio</label>
<input type="text" name="nombre" class="form-control"></div>
<div class="col-sm-3 top25"><button type="submit" class="btn btn-success btn-rounded" id="btnunidad">Registrar Servicio</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger oculto btn-rounded" id="btn_CMAR" onclick="CancelarEMAR();"><i class="fa fa-times" aria-hidden="true"></i></button></div>
</div>
</form>

<table class="table table-bordered table-striped table-sm">
<thead class="thead-dark">
<th>N°</th>
<th>Servicio</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sql="select * from servicios order by ser_nombre";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
?>
<tr id="mar<?=$fila["ser_id"];?>">
<td><?=$x;?></td>
<td><?=$fila["ser_nombre"];?></td>
<td class="text-center" width="50"><button class="btn btn-warning btn-circle" onclick="editarMAR('<?=$fila["ser_id"];?>','<?=$fila["ser_nombre"];?>')"><i class="fa fa-edit"></i></button></td>
<td class="text-center" width="50"><button class="btn btn-danger btn-circle" onclick="quitarMAR('<?=$fila["ser_id"];?>','<?=$fila["ser_nombre"];?>')"><i class="fa fa-trash"></i></button></td>
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
function quitarMAR(id,nombre){
$("#mmar .modal-dialog").css({'width':'30%'});
$("#mmar .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mmar .modal-title").html("Eliminar Servicio");
$("#mmar .modal-body").html("Realmente desea eliminar este servicio : <b>"+nombre+"</b>");
$("#mmar .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarMAR(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#mmar").modal("toggle");
}
function eliminarMAR(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarservicio',idmar:''+id+'',retornar:'no'},function(data){
$("#mar"+id+"").remove();
$("#mmar").modal("hide");
});
}

function editarMAR(id,nombre){
$("#fnuevamar").find("input[name='operacion']").val("editarservicio");
$("#fnuevamar").find("input[name='idmar']").val(id);
$("#fnuevamar").find("input[name='nombre']").val(nombre);
$("#fnuevamar").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar");
$("#btn_CMAR").show();
}
function CancelarEMAR(){
$("#fnuevamar").find("input[name='operacion']").val("nuevoservicio");
$("#fnuevamar").find("input[name='idmar']").val("");
$("#fnuevamar").find("input[name='nombre']").val("");
$("#fnuevamar").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Marca");
$("#btn_CMAR").hide();
}

</script>

  