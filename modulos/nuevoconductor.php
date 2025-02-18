<div class="content">
<div class="row top20">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Conductor</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal">
<input type="hidden" name="operacion" value="nuevoconductor"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">PIN</label>
<div class="col-sm-2"><input type="text" name="pin" class="form-control">
</div>
</div>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">Cliente</label>
<div class="col-sm-4">
<? htmlselect('cliente','cliente','clientes','id','razonsocial','','','','razonsocial','','','si','no','no');?>
</div>
</div>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">Rut</label>
<div class="col-sm-2"><input type="text" name="rut" class="form-control">
</div>
</div>
<div class="form-group">
<label class="contro-label col-sm-2 txtleft">Apellido Paterno</label>
<div class="col-sm-4"><input type="text" name="apaterno" class="form-control">
</div>
</div>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">Apellido Materno</label>
<div class="col-sm-4"><input type="text" name="amaterno" class="form-control">
</div>
</div>
<div class="form-group">
<label class="control-label col-sm-2 txtleft">Nombre</label>
<div class="col-sm-4"><input type="text" name="nombre" class="form-control">
</div>
</div>

<div class="form-group">
<div class="col-sm-3 col-sm-offset-2"><button type="submit" class="btn btn-success btn-rounded">Registrar Conductor</button></div>
</div>

</form>
</div>
</div>
</div>
</div>
</div>