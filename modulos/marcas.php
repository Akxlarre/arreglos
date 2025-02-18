<!-- modal -->
<div class="modal" id="mmar">
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
<div class="box-header with-border"><h3 class="box-title">Nueva Marca</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnuevamar">
<input type="hidden" name="operacion" value="nuevamarca"/>
<input type="hidden" name="idmar"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="form-group row">
<div class="col-sm-6"><label>Marca</label>
<input type="text" name="nombre" class="form-control"></div>
<div class="col-sm-3 top25"><button type="submit" style="margin-top: 32px;;" class="btn btn-success btn-rounded" id="btnunidad">Registrar Marca</button>&nbsp;&nbsp;<button type="button" style="margin-top: 32px;" class="btn btn-danger oculto btn-rounded" id="btn_CMAR" onclick="CancelarEMAR();"><i class="fa fa-times" aria-hidden="true"></i></button></div>
</div>
</form>

<table class="table table-bordered table-striped table-sm" id="tblmarcas">
<thead class="thead-dark">
<th>N°</th>
<th>Marca</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sql="select * from marcas where mar_veh = 0";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
?>
<tr id="mar<?=$fila["mar_id"];?>">
<td><?=$x;?></td>
<td><?=$fila["mar_nombre"];?></td>
<td class="text-center" width="50"><button class="btn btn-warning btn-circle" onclick="editarMAR('<?=$fila["mar_id"];?>','<?=$fila["mar_nombre"];?>')"><i class="fa fa-edit"></i></button></td>
<td class="text-center" width="50"><button class="btn btn-danger btn-circle" onclick="quitarMAR('<?=$fila["mar_id"];?>','<?=$fila["mar_nombre"];?>')"><i class="fa fa-trash"></i></button></td>
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
$(document).ready(function(){
    $('#tblmarcas').DataTable({
    "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
    "paging": true,
    // "order": [[0, "desc" ]],
    "lengthChange": true,
    "lengthMenu": [[100,-1], [100,"Todos"]],
    "pageLength":100,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "ordering": false,
    // "drawCallback": function() {
    //     let search = $('#tblvehiculos_length').html();
    //     $('#tblvehiculos_length').html('<button type="button" class="btn btn-info mr-2"><i class="fas fa-file-excel"></i> Exportar Excel</button>');
    //  },
    });
});

function quitarMAR(id,nombre){
$("#mmar .modal-dialog").css({'width':'30%'});
$("#mmar .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mmar .modal-title").html("Eliminar Marca");
$("#mmar .modal-body").html("Realmente desea eliminar esta marca : <b>"+nombre+"</b>");
$("#mmar .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarMAR(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#mmar").modal("toggle");
}
function eliminarMAR(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarmarca',idmar:''+id+'',retornar:'no'},function(data){
    $('#tblmarcas').DataTable().destroy();
    $("#mar"+id+"").remove();
    $("#mmar").modal("hide");
    $('#tblmarcas').DataTable({
    "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
    "paging": true,
    // "order": [[0, "desc" ]],
    "lengthChange": true,
    "lengthMenu": [[100,-1], [100,"Todos"]],
    "pageLength":100,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "ordering": false,
    // "drawCallback": function() {
    //     let search = $('#tblvehiculos_length').html();
    //     $('#tblvehiculos_length').html('<button type="button" class="btn btn-info mr-2"><i class="fas fa-file-excel"></i> Exportar Excel</button>');
    //  },
    });
});
}

function editarMAR(id,nombre){
$("#fnuevamar").find("input[name='operacion']").val("editarmarca");
$("#fnuevamar").find("input[name='idmar']").val(id);
$("#fnuevamar").find("input[name='nombre']").val(nombre);
$("#fnuevamar").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar");
$("#btn_CMAR").show();
}
function CancelarEMAR(){
$("#fnuevamar").find("input[name='operacion']").val("nuevamarca");
$("#fnuevamar").find("input[name='idmar']").val("");
$("#fnuevamar").find("input[name='nombre']").val("");
$("#fnuevamar").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Marca");
$("#btn_CMAR").hide();
}

</script>

  