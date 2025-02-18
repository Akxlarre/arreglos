<style>

    .is-required:after {
        content: '*';
        margin-left: 3px;
        color: red;
        font-weight: bold;
    }

</style>

<div class="content">
<div class="row top20">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Registrar Personal</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="operacion" value="registrarpersonal">
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>">

<div class="row">
    <div class="col-md-3"><label class="is-required">Apellido Paterno</label><input type="text" name="apaterno" class="form-control" required></div>
    <div class="col-md-3"><label>Apellido Materno</label><input type="text" name="amaterno" class="form-control"></div>
    <div class="col-md-6"><label class="is-required">Nombres</label><input type="text" name="nombres" class="form-control" required></div>
</div>
<div class="row top10">
    <div class="col-md-3"><label>Celular</label><input type="text" name="celular" class="form-control"></div>
    <div class="col-md-3"><label class="is-required">E-mail</label><input type="text" name="email" class="form-control" required></div>
    <div class="col-md-6"><label>Domicilio</label><input type="text" name="domicilio" class="form-control "></div>
</div>
<div class="row top10">
    <div class="col-md-3"><label class="is-required">Usuario</label><input type="text" name="usuario" class="form-control " required></div>
    <div class="col-md-3"><label class="is-required">Clave</label><input type="password" name="clave" class="form-control" required></div>
    <div class="col-md-3">
        <label>Región</label>
        <?=htmlselect('region','region','regiones','id','region|ordinal','','','','id','getComunas()','','si','no','no');?>
    </div>
    <div class="col-md-3">
        <label>Comuna</label>
        <select name="comuna" id="comuna" class="form-control"></select>
    </div>
</div>
<!--
<div class="row top10">
<div class="col-md-3"><label>Fecha Nacimiento</label><input type="text" name="fnacimiento" class="form-control fecha"></div>
<div class="col-md-3"><label>Rut</label><input type="text" name="rut" class="form-control"></div>
<div class="col-md-3"><label>Profesión</label><input type="text" name="profesion" class="form-control"></div>
</div>-->


<!-- Registrar personal -->
<div class="row"><div class="col-md-12"><hr></div></div>
<div class="row">
<div class="col-md-3"><button type="submit" class="btn btn-success btn-rounded">Registrar Personal</button></div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
<script>
function getComunas(){
idregion=$("#region option:selected").val();
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getComunas',region:''+idregion+'',retornar:'no'},function(data){
console.log(data);
$("#comuna").html(data);
});
}
</script>