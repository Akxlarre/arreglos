<!-- modal -->
<div class="modal" id="mgru">
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
<div class="col-md-6" >
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Grupo</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnuevogru">
<input type="hidden" name="operacion" value="nuevogrupo"/>
<input type="hidden" name="idgru"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>

<div class="form-group">
<label class="col-sm-3 control-label txtleft">Cliente</label>
<div class="col-sm-6"><? htmlselect('cliente','cliente','clientes','id','razonsocial','','','','razonsocial','','','si','no','no');?></div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label txtleft">Grupo</label>
<div class="col-sm-6 control-label txtleft"><input type="text" name="nombre" class="form-control"></div>
</div>
<div class="form-group">
<div class="col-sm-offset-3 col-sm-6"><button type="submit" class="btn btn-success btn-rounded" id="btnunidad">Registrar Grupo</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger oculto btn-rounded" id="btn_CGRU" onclick="CancelarEGRU();"><i class="fa fa-times" aria-hidden="true"></i></button></div>
</div>
</form>

</div>
</div>
</div>
<div class="col-md-6">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Grupos</h3>
</div>
<div class="box-body">
<table class="table table-bordered table-striped">
<thead>
<th>N°</th>
<th>Cliente</th>
<th>Grupo</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?
$sql="select * from grupos";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
?>
<tr id="gru<?=$fila["gru_id"];?>">
<td><?=$x;?></td>
<td><?=obtenervalor("clientes","razonsocial","where id='".$fila["gru_cliente"]."'");?></td>
<td><?=$fila["gru_nombre"];?></td>
<td class="text-center" width="50"><button class="btn btn-warning btn-circle" onclick="editarGRU('<?=$fila["gru_id"];?>','<?=$fila["gru_cliente"];?>','<?=$fila["gru_nombre"];?>')"><i class="fa fa-pencil-square-o"></i></button></td>
<td class="text-center" width="50"><button class="btn btn-danger btn-circle" onclick="quitarGRU('<?=$fila["gru_id"];?>','<?=$fila["gru_nombre"];?>')"><i class="fa fa-trash-o"></i></button></td>
</tr>
<?}?>
</tbody>
</table>
</div>
</div>
</div>

</div>
</section> 

<script>
function quitarGRU(id,nombre){
$("#mgru .modal-dialog").css({'width':'50%'});
$("#mgru .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mgru .modal-title").html("Eliminar Grupo");
$("#mgru .modal-body").html("Realmente desea eliminar este Grupo : <b>"+nombre+"</b>");
$("#mgru .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarGRU(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#mgru").modal("toggle");
}
function eliminarGRU(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminargrupo',idgru:''+id+'',retornar:'no'},function(data){
$("#gru"+id+"").remove();
$("#mgru").modal("hide");
});
}

function editarGRU(id,idcliente,grupo){
$("#fnuevogru").find("input[name='operacion']").val("editargrupo");
$("#fnuevogru #cliente").val(idcliente);
$("#fnuevogru").find("input[name='idgru']").val(id);
$("#fnuevogru").find("input[name='nombre']").val(grupo);
$("#fnuevogru").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar Grupo");
$("#btn_CGRU").show();
}
function CancelarEGRU(){
$("#fnuevogru").find("input[name='operacion']").val("nuevogrupo");
$("#fnuevogru").find("input[name='idgru']").val("");
$("#fnuevogru #cliente").val("");
$("#fnuevogru").find("input[name='nombre']").val("");
$("#fnuevogru").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Grupo");
$("#btn_CGRU").hide();
}

</script>

  