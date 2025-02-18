<!-- modal -->
<div class="modal" id="mfam">
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
<div class="col-md-8" id="nfamilia">
<div class="row">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nueva Familia</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnuevafam">
<input type="hidden" name="operacion" value="nuevafamilia"/>
<input type="hidden" name="idfam"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="form-group">
<div class="col-sm-6"><label>Familia</label>
<input type="text" name="nombre" class="form-control"></div>
<div class="col-sm-3 top25"><button type="submit" class="btn btn-success btn-rounded" id="btnunidad">Registrar Familia</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger oculto btn-rounded" id="btn_CFAM" onclick="CancelarEFAM();"><i class="fa fa-times" aria-hidden="true"></i></button></div>
</div>
</form>

<table class="table table-bordered table-striped table-sm">
<thead class="thead-dark">
<th>N°</th>
<th>Familia</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sql="select * from familias";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
?>
<tr id="fam<?=$fila["fam_id"];?>">
<td><?=$x;?></td>
<td><?=$fila["fam_nombre"];?></td>
<td class="text-center" width="50"><button class="btn btn-warning btn-circle" onclick="editarFAM('<?=$fila["fam_id"];?>','<?=$fila["fam_nombre"];?>')"><i class="fa fa-edit"></i></button></td>
<td class="text-center" width="50"><button class="btn btn-danger btn-circle" onclick="quitarFAM('<?=$fila["fam_id"];?>','<?=$fila["fam_nombre"];?>')"><i class="fa fa-trash"></i></button></td>
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
function quitarFAM(id,nombre){
$("#mfam .modal-dialog").css({'width':'30%'});
$("#mfam .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mfam .modal-title").html("Eliminar Familia");
$("#mfam .modal-body").html("Realmente desea eliminar esta familia : <b>"+nombre+"</b>");
$("#mfam .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarFAM(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#mfam").modal("toggle");
}
function eliminarFAM(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarFAM',idfam:''+id+'',retornar:'no'},function(data){
$("#fam"+id+"").remove();
$("#mfam").modal("hide");
});
}

function editarFAM(id,nombre){
$("#fnuevafam").find("input[name='operacion']").val("editarFAM");
$("#fnuevafam").find("input[name='idfam']").val(id);
$("#fnuevafam").find("input[name='nombre']").val(nombre);
$("#fnuevafam").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar");
$("#btn_CFAM").show();
}
function CancelarEFAM(){
$("#fnuevafam").find("input[name='operacion']").val("nuevafamilia");
$("#fnuevafam").find("input[name='idfam']").val("");
$("#fnuevafam").find("input[name='nombre']").val("");
$("#fnuevafam").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Familia");
$("#btn_CFAM").hide();
}

</script>

  