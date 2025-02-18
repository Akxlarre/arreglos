<!-- modal -->
<div class="modal" id="mttrab">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title"></h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

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
<div class="box-header with-border"><h3 class="box-title">Nuevo Tipo de Trabajo</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnuevotipo">
<input type="hidden" name="operacion" value="nuevotipodetrabajo"/>
<input type="hidden" name="idttra"/>
<input type="hidden" name="retornar" value="index.php?menu=<?php echo $_REQUEST["menu"];?>&idmenu=<?php echo $_REQUEST["idmenu"];?>"/>
<div class="form-group row">
<div class="col-sm-6"><label>Tipo de Trabajo</label>
<input type="text" name="nombre" class="form-control"></div>
<div class="col-sm-3"><button type="submit" class="btn btn-success btn-rounded" style="margin-top: 32px;">Registrar Tipo</button>&nbsp;&nbsp;
<button type="button" class="btn btn-danger oculto btn-rounded" style="margin-top: 32px;" id="btn_CETT" onclick="CancelarETT();"><i class="fa fa-times" aria-hidden="true"></i></button></div>
</div>
</form>

<table class="table table-bordered table-striped table-sm">
<thead class="thead-dark">
<th>N°</th>
<th>TIPO</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sql="select * from tiposdetrabajos where deleted_at is NULL ;";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
?>
<tr id="tt<?php echo $fila["ttra_id"];?>">
<td><?php echo $x;?></td>
<td><?php echo $fila["ttra_nombre"];?></td>
<td class="text-center" width="50"><button class="btn btn-warning btn-sm btn-circle" onclick="editarTT('<?php echo $fila['ttra_id'];?>','<?php echo $fila['ttra_nombre'];?>')"><i class="fas fa-edit"></i></button></td>
<td class="text-center" width="50"><button class="btn btn-danger btn-sm btn-circle" onclick="quitarTT('<?php echo $fila['ttra_id'];?>','<?php echo $fila['ttra_nombre'];?>')"><i class="fa fa-trash"></i></button></td>
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
function quitarTT(id,nombre){
$("#mttrab .modal-dialog").css({'width':'30%'});
$("#mttrab .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mttrab .modal-title").html("Eliminar Tipo de Trabajo");
$("#mttrab .modal-body").html("Realmente desea eliminar este tipo de trabajo : <b>"+nombre+"</b>");
$("#mttrab .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarTT(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#mttrab").modal("toggle");
}
function eliminarTT(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminartipodetrabajo',idttra:''+id+'',retornar:'no'},function(data){
$("#tt"+id+"").remove();
$("#mttrab").modal("hide");
});
}

function editarTT(id,nombre){
$("#fnuevotipo").find("input[name='operacion']").val("editartipodetrabajo");
$("#fnuevotipo").find("input[name='idttra']").val(id);
$("#fnuevotipo").find("input[name='nombre']").val(nombre);
$("#fnuevotipo").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar");
$("#btn_CETT").show();
}
function CancelarETT(){
$("#fnuevotipo").find("input[name='operacion']").val("nuevotipodetrabajo");
$("#fnuevotipo").find("input[name='idttra']").val("");
$("#fnuevotipo").find("input[name='nombre']").val("");
$("#fnuevotipo").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Tipo");
$("#btn_CETT").hide();
}

</script>

  