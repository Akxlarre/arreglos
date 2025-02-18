<style>

    .is-required:after {
        content: '*';
        margin-left: 3px;
        color: red;
        font-weight: bold;
    }

    .is-mayuscula{
        text-transform:uppercase;
    }

</style>


<div class="content">
<div class="row col-md-12 top20">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Registrar Personal</h3>
</div>
<div class="box-body">
<form action2="operaciones.php" method2="post" enctype2="multipart/form-data">
<input type="hidden" name="operacion" value="registrarpersonal">
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>">

<div class="row">
    <div class="col-md-3"><label class="is-required" >Apellido Paterno</label><input id="apaterno" type="text" name="apaterno" class="form-control is-mayuscula" required></div>
    <div class="col-md-3"><label>Apellido Materno</label><input id="amaterno" type="text" name="amaterno" class="form-control is-mayuscula"></div>
    <div class="col-md-6"><label class="is-required">Nombres</label><input id="nombres" type="text" name="nombres" class="form-control is-mayuscula" required></div>
</div>
<div class="row top10">
    <div class="col-md-3"><label>Celular</label><input id="celular" type="text" name="celular" class="form-control is-mayuscula"></div>
    <div class="col-md-3"><label class="is-required2">E-mail</label><input id="email" type="text" name="email" class="form-control is-mayuscula"></div>
    <div class="col-md-6"><label>Domicilio</label><input id="domicilio" type="text" name="domicilio" class="form-control is-mayuscula"></div>
</div>
<div class="row top10">
    <div class="col-md-3"><label class="is-required2">Usuario</label><input id="usuario" type="text" name="usuario" class="form-control"></div>
    <div class="col-md-3"><label class="is-required2">Clave</label><input id="clave" type="password" name="clave" class="form-control"></div>
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
    <div class="col-md-3">
        <button id="addPersonal" type="button" class="btn btn-success btn-rounded" onclick="AgregarPersonal();">Registrar Personal</button>
    </div>
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
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',
    operacion:'getComunas',
    region:''+idregion+'',
    retornar:'no'},function(data){
console.log(data);
$("#comuna").html(data);
});
}

function AgregarPersonal(){
    $("#addPersonal").prop("disabled",true);
   saveAddPersonal();
}

function saveAddPersonal(){

    toastr.remove();
    toastr.clear();
    let error = 0;
    let message ="Revise camos obligatorios, ";
    var arr = [];

    let apellido = $("#apaterno").val().trim();
    let nombres  = $("#nombres").val().trim();
    let region  = $("#region").val();
    let comuna  = $("#comuna").val();
    let amaterno  = $("#amaterno").val().trim();
    let celular  = $("#celular").val().trim();
    let email  = $("#email").val().trim();
    let domicilio  = $("#domicilio").val().trim();
    let usuario  = $("#usuario").val().trim();
    let clave  = $("#clave").val().trim();

    //validacion de campos obligatorios
    if( apellido == '' ){
        error++;
        message+=' falta ingresar Apellido Paterno';
    }

    if( nombres == '' ){
        if(error == 1){
            message+=',';
        }
        error++;
        message+=' falta ingresar Nombres';
    }

    if( region == '' ){
        region = null;
    }

    if( error ){
        $("#addPersonal").prop("disabled",false);
        toastr.error(message);
        return true;
    }

    arr.push({   'region'  : region,
                'comuna'   : comuna,
                'apaterno' : apellido,
                'amaterno' : amaterno,
                'celular'  : celular,
                'email'    : email,
                'domicilio': domicilio,
                'usuario'  : usuario,
                'clave'    : clave,
                'nombres'  : nombres
            });

    var send  = JSON.stringify(arr);

    $.ajax({
        url     : 'operaciones.php',
        data    : { numero:''+Math.floor(Math.random()*9999999)+'',
                    operacion:'registrarpersonal',
                    retornar:'no',
                    envio:send
                  },
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {
            console.log("beforeSend: ");
        },error   : function(respuesta) {
            $("#addPersonal").prop("disabled",false);
            message = "Error, comunicarse con Soporte";
            toastr.error(message);
        },success : function(data) {
            $("#addPersonal").prop("disabled",false);
            console.log("success: ",data.message);

            if(data.status=='success'){
                toastr.success(data.message);
                clearFeilds();
            }else{
                toastr.error(data.message);
            }
        }
    });
}

function clearFeilds(){
    $("#apaterno").val('');
    $("#nombres").val('');
    $("#region").val('');
    $("#comuna").val('');
    $("#amaterno").val('');
    $("#celular").val('');
    $("#email").val('');
    $("#domicilio").val('');
    $("#usuario").val('');
    $("#clave").val('');
}

</script>