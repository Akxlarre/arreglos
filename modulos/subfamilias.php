<!-- modal -->
<div class="modal" id="msfam">
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
<div class="col-md-8" id="subfamilias">
<div class="row">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Subfamilias</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnuevasfam" >
<input type="hidden" name="operacion" value="nuevasubfamilia"/>
<input type="hidden" name="idsfam"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="row">
    <div class="col-md-4">
        <div class="form-group row">
        <label class="col-sm-3 control-label txtleft">Familia</label>
        <div class="col-sm-9"><?= htmlselect('familia','familia','familias','fam_id','fam_nombre','','','','fam_nombre','','','si','no','no');?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group row">
        <label class="col-sm-3 control-label txtleft">Subfamilia</label>
        <div class="col-sm-9"><input type="text" name="nombre" class="form-control"></div>
        </div>
    </div>
    <div class="col-md-4">
        <button type="submit" class="btn btn-success btn-rounded" id="btncargo">Registrar Subfamilia</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger oculto btn-rounded" id="btn_CESFAM" onclick="CancelarESFAM();"><i class="fa fa-times" aria-hidden="true"></i></button>
    </div>
</div>

</form>
<table class="table table-bordered table-striped table-sm" id="tblsubfamilia">
<thead class="thead-dark">
<th>N°</th>
<th>Familia</th>
<th>Subfamilia</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sql="select sfam.*,fam.* from subfamilias sfam left outer join familias fam on sfam.sfam_familia = fam.fam_id ";
$res=$link->query($sql);
$x=0;
while($fila=mysqli_fetch_array($res)){
$x++;
?>
<tr id="sfam<?=$fila["sfam_id"];?>">
<td><?=$x;?></td>
<td><?=$fila["fam_nombre"];?></td>
<td><?=$fila["sfam_nombre"];?></td>
<td class="text-center" width="50"><button class="btn btn-warning btn-circle " onclick="editarSFAM('<?=$fila["sfam_id"];?>','<?=$fila["sfam_familia"];?>','<?=$fila["sfam_nombre"];?>')"><i class="fa fa-edit"></i></button></td>
<td class="text-center" width="50"><button class="btn btn-danger btn-circle" onclick="quitarSFAM('<?=$fila["sfam_id"];?>','<?=$fila["sfam_nombre"];?>')"><i class="fa fa-trash"></i></button></td>
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
        $('#tblsubfamilia').DataTable({
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
/**** cargos *****/
function quitarSFAM(id,nombre){
$("#msfam .modal-dialog").css({'width':'50%'});
$("#msfam .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#msfam .modal-title").html("Eliminar Subfamilia");
$("#msfam .modal-body").html("Realmente desea eliminar esta subfamilia : <b>"+nombre+"</b>");
$("#msfam .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarSFAM(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
$("#msfam").modal("toggle");
}
function eliminarSFAM(id){
    $('#tblsubfamilia').DataTable().destroy();
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarsubfamilia',idsfam:''+id+'',retornar:'no'},function(data){
    $("#sfam"+id+"").remove();  
    $("#msfam").modal("hide");
    $('#tblsubfamilia').DataTable({
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

function editarSFAM(id,idfam,nombre){
	
$("#fnuevasfam").find("input[name='operacion']").val("editarsubfamilia");
$("#fnuevasfam").find("input[name='idsfam']").val(id);
$("#fnuevasfam").find("#familia").val(idfam);
$("#fnuevasfam").find("input[name='nombre']").val(nombre);
$("#fnuevasfam").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar");
$("#btn_CESFAM").show();
}
function CancelarESFAM(){
$("#fnuevasfam").find("input[name='operacion']").val("nuevasubfamilia");
$("#fnuevasfam").find("input[name='idsfam']").val("");
$("#fnuevasfam").find("#familia").val("");
$("#fnuevasfam").find("input[name='nombre']").val("");
$("#fnuevasfam").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Subfamilia");
$("#btn_CESFAM").hide();
}



</script>

  