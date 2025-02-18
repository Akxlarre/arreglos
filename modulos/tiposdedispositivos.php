<!-- modal -->
<div class="modal" id="mtdi">
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
<div class="box-header with-border"><h3 class="box-title">Nuevo Tipo de Dispositivo</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnuevotipo">
    <input type="hidden" id="operacion" name="operacion" value="nuevotdi"/>
    <input type="hidden" id="idtdi" name="idtdi"/>
    <input type="hidden" id="retornar" name="retornar" value="index.php?menu=<?php echo $_REQUEST["menu"];?>&idmenu=<?php echo $_REQUEST["idmenu"];?>"/>
    <div class="form-group row">
        <div class="col-sm-6"><label>Tipo de Dispositivo</label>
            <input type="text" id="nombre" name="nombre" class="form-control">
        </div>
    <div class="col-sm-3"><button type="submit" class="btn btn-success btn-rounded" style="margin-top: 32px;">Registrar Tipo</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger oculto btn-rounded" id="btn_CETDI" onclick="CancelarETDI();"><i class="fa fa-times" aria-hidden="true"></i></button></div>
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
$sql="select * from tiposdedispositivos";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
?>
<tr id="tdi<?php echo $fila["tdi_id"];?>">
<td><?php echo $x;?></td>
<td><?php echo $fila["tdi_nombre"];?></td>
<td class="text-center" width="50"><button class="btn btn-warning btn-sm btn-circle" onclick="editarTDI('<?php echo $fila['tdi_id'];?>','<?php echo $fila['tdi_nombre'];?>')"><i class="fa fa-edit"></i></button></td>
<td class="text-center" width="50"><button class="btn btn-danger btn-sm btn-circle" onclick="quitarTDI('<?php echo $fila['tdi_id'];?>','<?php echo $fila['tdi_nombre'];?>')"><i class="fa fa-trash"></i></button></td>
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

function quitarTDI(id,nombre)
{
$("#mtdi .modal-dialog").css({'width':'30%'});
$("#mtdi .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mtdi .modal-title").html("Eliminar Tipo de Dispositivo");
$("#mtdi .modal-body").html("Realmente desea eliminar este tipo de Dispositivo : <b>"+nombre+"</b>");
$("#mtdi .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarTDI(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#mtdi").modal("toggle");
}
function eliminarTDI(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminartdi',idtdi:''+id+'',retornar:'no'},function(data){
$("#tdi"+id+"").remove();
$("#mtdi").modal("hide");
});
}

function editarTDI(id,nombre){
$("#fnuevotipo").find("input[name='operacion']").val("editartdi");
$("#fnuevotipo").find("input[name='idtdi']").val(id);
$("#fnuevotipo").find("input[name='nombre']").val(nombre);
$("#fnuevotipo").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar");
$("#btn_CETDI").show();
}
function CancelarETDI(){
$("#fnuevotipo").find("input[name='operacion']").val("nuevotdi");
$("#fnuevotipo").find("input[name='idtdi']").val("");
$("#fnuevotipo").find("input[name='nombre']").val("");
$("#fnuevotipo").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Tipo");
$("#btn_CETDI").hide();
}

</script>

  