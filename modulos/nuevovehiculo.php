<?php
$msjerr = '';
if(isset($_REQUEST['err'])){
    if($_REQUEST['err']==1){
        $msjerr = 'Error al crear Vehiculo';
        ?>
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                let timerInterval
                Swal.fire({
                  title: 'Error!',
                  icon: 'warning',
                  html: 'No se ha podido crear el vechiculo',
                  timer: 1100,
                  timerProgressBar: true,
                  didOpen: () => {
                    Swal.showLoading()
                    const b = Swal.getHtmlContainer().querySelector('b')
                    timerInterval = setInterval(() => {
                      b.textContent = Swal.getTimerLeft()
                    }, 100)
                  },
                  willClose: () => {
                    clearInterval(timerInterval)
                  }
                }).then((result) => {
                  /* Read more about handling dismissals below */
                  if (result.dismiss === Swal.DismissReason.timer) {
                    console.log('I was closed by the timer')
                  }
                })
            </script>
        <?php
    }else if($_REQUEST['err']==2){
        $msjerr = 'Error al crear Vehiculo';
        ?>
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                let timerInterval
                Swal.fire({
                  title: 'Error!',
                  icon: 'warning',
                  html: 'Patente ya existe',
                  timer: 1100,
                  timerProgressBar: true,
                  didOpen: () => {
                    Swal.showLoading()
                    const b = Swal.getHtmlContainer().querySelector('b')
                    timerInterval = setInterval(() => {
                      b.textContent = Swal.getTimerLeft()
                    }, 100)
                  },
                  willClose: () => {
                    clearInterval(timerInterval)
                  }
                }).then((result) => {
                  /* Read more about handling dismissals below */
                  if (result.dismiss === Swal.DismissReason.timer) {
                    console.log('I was closed by the timer')
                  }
                })
            </script>
        <?php
    }
}

$optionmarca = '<option value="0">Seleccione</option>';
$sql = "SELECT * FROM marca";
$res = $link->query($sql);
if(mysqli_num_rows($res)>0){
    foreach($res as $key=>$dat){
        $optionmarca.='<option value="'.$dat['mar_id'].'">'.$dat['mar_nombre'].'</option>';
    }
}


$sql1 = "SELECT * FROM productos";
$res1=$link->query($sql1);
$productos=array();
while($fila=mysqli_fetch_array($res1)){
    $productos[]=array('pro_id'=>$fila['pro_id'], 'pro_codigo'=>$fila['pro_codigo'], 'pro_serie'=>$fila['pro_serie'], 'pro_familia'=>$fila['pro_familia'], 'pro_subfamilia'=>$fila['pro_subfamilia'], 'pro_marca'=>$fila['pro_marca'], 'pro_nombre'=>$fila['pro_nombre'], 'pro_stockminimo'=>$fila['pro_stockminimo'], 'pro_stock'=>$fila['pro_stock'], 'pro_valor'=>$fila['pro_valor']);
}
//$productos = json_encode($productos);
?>
<!-- modal -->
<div class="modal" id="mveh">
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
<section class="content pt-3">
<div class="row top20">
<div class="col-md-12">
    <button class="btn btn-success btn-rounded" id="btncargamasivavehiculos"><i class="fa fa-upload" aria-hidden="true"></i> Carga Masiva</button>
    <button class="btn btn-success btn-rounded" id="btnplantillavehiculos" onclick="getPlantilla()"><i class="fa fa-download" aria-hidden="true"></i> Descargar plantilla</button>
    <input type="file" id="inputcargamasiva" style="display:none" name='inputcargamasiva'/> 
</div>
<div id='cargandoplan' class="col-md-12 oculto" style="margin-top: 5px;"><div class='progress'><div class='progress-bar progress-bar-success progress-bar-striped active' role='progressbar'></div><span class='sr-only'></span></div></div>
<div class="col-md-12" style="margin-top: 5px;">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Nuevo Vehículo</h3>
</div>
<div class="box-body">
<form action="operaciones.php" method="post" class="form-horizontal" id="fnueveh" onsubmit="return validarVEH()">
<input type="hidden" name="operacion" value="nuevovehiculo"/>
<input type="hidden" name="idveh"/>
<input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Tipo</label>
            <div class="col-sm-6"><? htmlselect('tipo','tipo','tiposdevehiculos','tveh_id','tveh_nombre','','','','tveh_nombre','','','si','no','no');?></div>
        </div>
        <div class="form-group row" style="display: none;">
            <label class="col-sm-3 control-label txtleft">GPS</label>
            <div class="col-sm-6">
                <select id="gps" name="gps" class="form-control">
                <option value="0">SELECCIONAR</option>
                <option value="1">BÁSICO</option>
                <option value="2">CANBUS</option>
                <option value="3">TEMPERATURA</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Cuenta</label>
            <div class="col-sm-6"><? htmlselect('cliente','cliente','clientes','id','cuenta','','','WHERE cuenta!="" group by cuenta','cuenta','getGruposCliente()','','si','no','no');?></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Razón social</label>
            <div class="col-sm-6"><select name="rsocial" id="rsocial" class="form-control"></select></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Grupo</label>
            <div class="col-sm-6"><select name="grupo" id="grupo" class="form-control"></select></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Región</label>
            <div class="col-sm-6"><? htmlselect('region','region','regiones','id','region|ordinal','','','','id','getComunas()','','si','no','no');?></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Instalar</label>
            <div class="col-sm-4"><input type="checkbox" name="instalar" checked></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Comuna</label>
            <div class="col-sm-6"><select name="comuna" id="comuna" class="form-control"></select></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Patente</label>
            <div class="col-sm-3"><input type="text" name="patente" class="form-control"></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Marca</label>
            <div class="col-sm-6">
                <select id="marca" name="marca">
                    <?php echo $optionmarca?>
                </select>
            </div>

            <!-- <div class="col-sm-3"><input type="text" name="marca" class="form-control"></div> -->
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Modelo</label>
            <div class="col-sm-6">
                <select id="modelo" name="modelo">
                    <option value="0">Seleccione</option>
                </select>
            </div>
            <!-- <div class="col-sm-3"><input type="text" name="modelo" class="form-control"></div> -->
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Dispositivo</label>
            <div class="col-sm-6"><? htmlselect('dispositivo','dispositivo','tiposdedispositivos','tdi_id','tdi_nombre','','','','tdi_nombre','','','si','no','no');?></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Tipo de servicio</label>
            <div class="col-sm-6"><? htmlselect('tservicio','tservicio','servicios','ser_id','ser_nombre','','','','ser_nombre','','','si','no','no');?></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Contacto</label>
            <div class="col-sm-6"><input type="text" name="contacto" class="form-control"></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">Celular</label>
            <div class="col-sm-4"><input type="text" name="celular" class="form-control"></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label txtleft">SIM</label>
            <div class="col-sm-4"><input type="number" name="sim" class="form-control"></div>
        </div>
    </div>
</div>

<div class="col-md-6 oculto" id="view_equipamiento">
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-sm-4 col-lg-6">
            <label>Producto</label><br>
            <? htmlselect('selectproducto','selectproducto','productos','pro_id','pro_nombre','','','','pro_nombre','getProductosDetails()','','si','no','no');?>
        </div>
        <div class="col-sm-4 col-lg-6">
            <label>Cantidad</label><br>
            <input type="number" name="cantidad" id="cantidad" class="form-control form-control-sm">
        </div>
        <div class="col-sm-4 col-lg-5 oculto divserie">
            <label>Serie</label><br>
            <input type="text" name="serie" id="serie" class="form-control">
        </div>
        <div class="col-sm-4 col-lg-1 oculto divserie">
            <span class="btn btn-info btn-circle tooltips top20" onclick="addSerie()"><i class="fa fa-list-ol"></i><span class="tooltiptext tooltip-left"></span></span>
        </div>
        <div class="col-sm-4 col-lg-6">
            <button type="button" style="margin-top:23px;margin-left:20px;" class="btn btn-success btn-rounded" onclick="addProducto()" id=""><i class="fa fa-plus"></i> Agregar Producto</button>
        </div>
        <div class="col-sm-12 col-lg-12 table-responsive top10 oculto" id="divtableproducto">
            <table class="table table-striped table-bordered table-hover table-condensed" id="tableproductosxveh">
                <thead>
                    <th>#</th>
                    <th nowrap>Producto</th>
                    <th nowrap>Cantidad</th>
                    <th nowrap>Serie</th>
                    <th></th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="inp_agregarproductos" class="oculto">
            </div>
        </div>
    </div>
</div>


<button type="submit" class="btn btn-success btn-rounded" id="btnunidad">Registrar Vehículo</button>
<button type="button" class="btn btn-warning btn-rounded" onclick="addEquipamiento(1)" id="btnequipamiento">Equipamiento</button>

</form>

</div>
</div>
</div>
</div>
</section> 
<script>
$(document).ready(function(){
    $('#selectproducto').chosen({width: "95%"});
    $('#marca').chosen({width: "100%"});
    $('#modelo').chosen({width: "100%"});
    $('#tipo').chosen({width: "100%"});
    $('#gps').chosen({width: "100%"});
    $('#cliente').chosen({width: "100%"});
    $('#comuna').chosen({width: "100%"});
    $('#rsocial').chosen({width: "100%"});
    $('#grupo').chosen({width: "100%"});
    $('#region').chosen({width: "100%"});
    $('#dispositivo').chosen({width: "100%"});
    $('#tservicio').chosen({width: "100%"});

});

$(function(){
    
});

$('#marca').on('change', function() {
    if($(this).val()==0){
        $('#modelo').html('<option value="0">Seleccione</option>').trigger('chosen:updated')
    }else{
        cargaselect ('modelo',$(this).val())
    }     
})

function cargaselect (id,valor,fijvalor=0){
    var env   = {'id':id,'valor':valor};
    var send  = JSON.stringify(env);
    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'cargaselect',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {
               
        },error   : function(respuesta) {
            
        },success : function(data) {
            if(data){
                $('#'+id).html('')
                $.each(data.option, function(i, item) {
                    $('#'+id).append(item).trigger('chosen:updated')
                })

                if(fijvalor!=0){
                    $('#'+id).val(fijvalor).trigger('chosen:updated')
                }
            }
        }
    });
}
function validarVEH(){

    if($("#tipo").val() == ""){
        alert("Tipo de vehículo no seleccionado");
        return false;
    }

    /*if(parseInt($("#gps").val()) == 0){
        alert("GPS no seleccionado");
        return false;
    }*/
    if($("#cliente").val() == ""){
        alert("Cliente no seleccionado");
        return false;
    }

    if($("input[name='patente']").val() !=""){
        var form_data = new FormData();
        form_data.append('operacion','ValidarPatente');
        form_data.append('patente', $("input[name='patente']").val());
        form_data.append('retornar','no');

        $.ajax({
            url: 'operaciones.php', //ruta archivo operaciones
            dataType: 'text',  // tipo de datos
            async:false,
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(respuesta){
                if(parseInt(respuesta) == 1){
                    let timerInterval
                    Swal.fire({
                      title: 'Error!',
                      icon: 'warning',
                      html: 'Patente '+$("input[name='patente']").val()+' ya registrada',
                      timer: 1100,
                      timerProgressBar: true,
                      didOpen: () => {
                        Swal.showLoading()
                        const b = Swal.getHtmlContainer().querySelector('b')
                        timerInterval = setInterval(() => {
                          b.textContent = Swal.getTimerLeft()
                        }, 100)
                      },
                      willClose: () => {
                        clearInterval(timerInterval)
                      }
                    }).then((result) => {
                      /* Read more about handling dismissals below */
                        if (result.dismiss === Swal.DismissReason.timer) {
                            console.log('I was closed by the timer')
                        }
                    })
                    /*alert("La patente "+$("input[name='patente']").val()+" ya se encuentra registrada");*/
                    $("input[name='patente']").val("").focus();
                    retornar = false;
                }else{
                    retornar = true;
                }
            }
        });

        /*
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'ValidarPatente',patente:''+$("input[name='patente']").val()+'',retornar:'no'},function(data){
        console.log(data);
        if(parseInt(data) == 1){
        alert("La patente "+$("input[name='patente']").val()+" ya se encuentra registrada");
        $("input[name='patente']").val("").focus();
        return false;
        }else {
        return true;
        }

        });*/

        return retornar;
    }else{
        alert("Patante no ingresada");
        return false;
    }


    //return false;
}

function getGruposCliente(){
    let idcli=$("#cliente").val();
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getGruposCliente',id:''+idcli+'',retornar:'no'},function(data){
    console.log(data);
    datos = $.parseJSON(data);
    sgrup="<opion value=0>SELECCIONAR</option>";
    $.each(datos,function(index,valor){
    sgrup+="<option value="+valor.id+">"+valor.nombre+"</option>";
    });
    $("#grupo").html(sgrup);
    });

    let text=$("#cliente option:selected").text().trim();
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getRazonSocial',text:text,retornar:'no'},function(data){
        //console.log(data);
        if(data!=='' && data!==null){
            data = $.parseJSON(data);
            if(data.length>0){
                let option = "<option value='0'>-- SELECCIONAR --</option>";
                $.each(data,function(i,item){
                    option += "<option value='"+item.id+"'>"+item.rsocial+"</option>";
                });
                $('#rsocial').html(option);
            }
        }
    });
}

function getComunas(){
idregion=$("#region option:selected").val();
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getComunas',region:''+idregion+'',retornar:'no'},function(data){
console.log(data);
$("#comuna").html(data);
});
}

function getPlantilla(){
    let url = "operaciones.php?operacion=getPlantillaVehiculos";
    let a = document.createElement('a');
    a.target="_blank";
    a.href=url;
    a.click();
}

$('#btncargamasivavehiculos').click(function(){ $('#inputcargamasiva').trigger('click'); });
$("input[name='inputcargamasiva']").change(function(e){
    doc = $(this).prop('files')[0];
    if(typeof doc !=="undefined"){
        cargaMasivaVehiculos(doc);
    this.value = null;
    }else{
    }
});

function cargaMasivaVehiculos(documento){
// console.log(documento);
var file_data = documento;
usuario = $("#useridjs").val();
var form_data = new FormData();
let d = new Date();
let fechahora = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
fecha = fechahora;
form_data.append('operacion','postVehiculos');
form_data.append('fecha',fecha);
form_data.append('usuario',usuario);
form_data.append('archivo', file_data);
form_data.append('retornar','no');
// $("#btnImportPlan").hide();
$("#cargandoplan").show();
$.ajax({
url: 'operaciones.php', //ruta archivo operaciones
dataType: 'text',  // tipo de datos
cache: false,
contentType: false,
processData: false,
data: form_data,
type: 'post',
xhr: function () {
var xhr = $.ajaxSettings.xhr();
xhr.onprogress = function e() {
// For downloads
if (e.lengthComputable) {
 //console.log(e.loaded / e.total);
}
};
xhr.upload.onprogress = function (e) {
if (e.lengthComputable) {
porcentaje=parseInt((e.loaded / e.total)*100);
$("#cargandoplan .progress-bar").css({width:""+porcentaje+"%"});
$("#cargandoplan .progress-bar").html(porcentaje+"%");
$("#cargandoplan .sr-only").html(porcentaje+"%");
// console.log(porcentaje);
}};
return xhr;
},
success: function(respuesta){
// console.log(respuesta);
$("#cargandoplan").hide();
// $("#divImportarViajes").hide();
//location.reload();
// procesarPlanificacion(respuesta);
}
});
}

function addEquipamiento(opc){
    if(parseInt(opc)==1){
        $('#view_equipamiento').show('slow');
        $('#btnequipamiento').attr('onclick', 'addEquipamiento(2)').html('Cancelar Equipamiento').removeClass('btn-warning').addClass('btn-danger');
    }
    else{
        $('#view_equipamiento').hide('slow');
        $('#btnequipamiento').attr('onclick', 'addEquipamiento(1)').html('Equipamiento').removeClass('btn-danger').addClass('btn-warning');
    }
}

let productos = [<?php for($i=0; $i<count($productos); $i++){ echo '{"pro_id":"'.$productos[$i]["pro_id"].'", "pro_codigo":"'.$productos[$i]["pro_codigo"].'", "pro_serie":"'.$productos[$i]["pro_serie"].'", "pro_familia":"'.$productos[$i]["pro_familia"].'", "pro_subfamilia":"'.$productos[$i]["pro_subfamilia"].'", "pro_marca":"'.$productos[$i]["pro_marca"].'", "pro_nombre":"'.$productos[$i]["pro_nombre"].'", "pro_stockminimo":"'.$productos[$i]["pro_stockminimo"].'", "pro_stock":"'.$productos[$i]["pro_stock"].'", "pro_valor":"'.$productos[$i]["pro_valor"].'"},'; } ?>];
let series = [];

function getProductosDetails(){
    let producto = $("#selectproducto").val();
    let valor = '';
    let serie = '';
    series = [];
    $.each(productos, function(i, item){
        if(parseInt(item.pro_id) === parseInt(producto)){
            valor = item.pro_valor;
            serie = item.pro_serie;
        }
    });

    if(parseInt(serie) == 1){
        $('.divserie').show('slow');
        //$('#tdbtnserie').show('slow');
    }
    else{
        $('.divserie').hide('slow');
        //$('#tdbtnserie').hide('slow');
    }
}

function addSerie(){
    let serieTemp = $('#serie').val();
    let cantidad = parseInt($("#cantidad").val());
    if($("#cantidad").val()!=''){
        if(series.length===0){
            series[0] = serieTemp;
            toastr.success('Serie ingresada con exito (1 serie(s)).');
            //$('.divserie').hide('slow');
            //$("#cantidad").val('');
        }
        else{
            if(series.length < cantidad){
                let numb = series.length;
                series[numb] = serieTemp;
                toastr.success('Serie ingresada con exito ('+(numb+1)+' serie(s)).');
                //$('.divserie').hide('slow');
                //$("#cantidad").val('');
            }
            else{
                toastr.info('No puede agregar mas serie que la cantidad ingresada ('+$("#cantidad").val()+')');
            }
        }
        $('#serie').val('');
    }
    else{
        toastr.error('Debe ingresar una cantidad.');
    }
}

function addProducto(){
    var nproductos = $("#tb_agregarproducto tbody tr").length;
    nproductos=nproductos+1;
    let producto = $("#selectproducto").val();
    let cantidad = $("#cantidad").val();
    let serie = '';
    let serieSend = '';

    $.each(series, function(i, item){
        if(i===series.length-1){
            serie += item;
            serieSend += item;
        }
        else{
            serie += item + ",";
            serieSend += item + ", ";
        }
    });

    
    let rows = "<tr id='con_fila"+nproductos+"'><td>"+nproductos+"</td><td>"+$("#selectproducto option:selected").text()+"</td><td .cantidad>"+cantidad+"</td><td>"+serieSend+"</td><td><span class='btn btn-danger btn-circle-s' onclick='removeItem("+nproductos+")'><i class='fa fa-trash'></i></span></td></tr>";
    $('#tableproductosxveh tbody').append(rows);
    $("#inp_agregarproductos").append("<input type='hidden' id='idcon"+nproductos+"' name='productosSend[]' value=\""+cantidad+"|"+producto+"|"+serie+"\">");
    $('#divtableproducto').show('slow');
    $("#selectproducto").val('');
    $("#cantidad").val('');
    $('.divserie').hide('slow');
    $('#serie').val('');
}

function removeItem(id){
    $("#con_fila"+id+", #idcon"+id+"").remove();
}
</script>


  