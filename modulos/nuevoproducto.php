<!-- modal -->
<div class="modal" id="mnpro">
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
<div class="col-md-10" id="nuevoproducto">
<div class="row">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Producto</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal top20" id="fnpro">
<input type="hidden" name="operacion" value="nuevoproducto"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-4 control-label txtleft">Código Interno</label>
            <div class="col-sm-3"><input type="text" name="codigo" class="form-control"></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 control-label txtleft">Tiene Serie</label>
            <div class="col-sm-2">
                <input type="checkbox" id="serie" name="serie" value="1">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 control-label txtleft">Familia</label>
            <div class="col-sm-8"><? htmlselect('familia','familia','familias','fam_id','fam_nombre','','','','fam_nombre','getSubfamilias()','','si','no','no');?></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 control-label txtleft">Subfamilia</label>
            <div class="col-sm-8"><select name="subfamilia" id="subfamilia" class="form-control"></select></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 control-label txtleft">Marca</label>
            <div class="col-sm-8"><? htmlselect('marca','marca','marcas','mar_id','mar_nombre','','','where mar_veh = 0','mar_nombre','','','si','no','no');?></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 control-label txtleft">Producto</label>
            <div class="col-sm-8"><textarea name="nombre" class="form-control rznone" rows=3 ></textarea></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 control-label txtleft">Stock Mínimo</label>
            <div class="col-sm-8"><input type="text" name="sminimo" class="form-control"></div>
        </div>
        <div class="form-group">
        <div class="col-sm-4"><button type="submit" class="btn btn-success btn-rounded">Registrar Producto</button></div>
        </div>
    </div>
</div>









</form>


</div>
</div>
</div>
</div>

</div>

</div>

</section> 
<script>
$(function(){
getCodigo();
});

function getCodigo(){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'generarCodigo',retornar:'no'},function(data){
$("input[name='codigo']").val(data);
});	
}
function getSubfamilias(){
idfam=$("#familia option:selected").val();
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getSubfamilias',familia:''+idfam+'',retornar:'no'},function(data){
$("#subfamilia").html(data);
});
}
</script>
