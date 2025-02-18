<?php
$sql1 = "SELECT * FROM productos";
$res1=$link->query($sql1);
$productos=array();
while($fila=mysqli_fetch_array($res1)){
    $productos[]=array('pro_id'=>$fila['pro_id'], 'pro_codigo'=>$fila['pro_codigo'], 'pro_serie'=>$fila['pro_serie'], 'pro_familia'=>$fila['pro_familia'], 'pro_subfamilia'=>$fila['pro_subfamilia'], 'pro_marca'=>$fila['pro_marca'], 'pro_nombre'=>$fila['pro_nombre'], 'pro_stockminimo'=>$fila['pro_stockminimo'], 'pro_stock'=>$fila['pro_stock'], 'pro_valor'=>$fila['pro_valor']);
}

$sql = "SELECT * FROM guiaentrada ORDER BY gui_id desc LIMIT 1";
$res=$link->query($sql);
$fila=mysqli_fetch_array($res);
$correlativo = ($fila['gui_id'] + 1);

$sql3 = "SELECT * FROM familias ORDER BY fam_id";
$res3=$link->query($sql3);
$opt = '<select onchange="getProductosDetails()" tabindex="5" data-placeholder="Seleccione producto" class="chosen-select" id="selectproducto" name="selectproducto"><option value=""></option>';
while($fila3=mysqli_fetch_array($res3)){
    $opt .= '<optgroup style="color:#338AFF;" label="'.$fila3['fam_nombre'].'">';
    $sql1 = "SELECT * FROM productos pro LEFT OUTER JOIN familias fam ON pro.pro_familia=fam.fam_id WHERE pro.pro_familia={$fila3['fam_id']} ORDER BY pro.pro_nombre";
    $res1=$link->query($sql1);
    while($fila1=mysqli_fetch_array($res1)){
        $opt .= '<option value="'.$fila1['pro_id'].'" data-serie="'.$fila1['pro_serie'].'" >'.$fila1['pro_nombre'].'</option>';
    }
    $opt .= '</optgroup>';
}
$opt .= '</select>';
?>
<style>
.tooltips {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltips .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  z-index: 1;
  bottom: 125%;
  left: 50%;
  margin-left: -60px;
  opacity: 0;
  transition: opacity 0.3s;
}

/* .tooltips .tooltip-left::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 100%;
    margin-top: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: transparent transparent transparent #555;
}

.tooltips .tooltip-left {
    top: -5px;
    bottom: auto;
    right: 128%;
} */

.tooltips .tooltiptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

.tooltips:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}
</style>

<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<!-- modal -->
<div class="modal" id="malert">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title"></h4>
<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

</div>
<div class="modal-body"></div>
<div class="modal-footer">
</div>
</div>
</div>
</div>
<!-- fin modal -->
<div class="content">
<div class="alert alert-success oculto alert-dismissible" id="guiaok">
<!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
<h4><i class="icon fa fa-warning"></i>Guía registrada exitosamente. </h4>
</div>
<div class="alert alert-success oculto alert-dismissible" id="guiaup">
<!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
<h4><i class="icon fa fa-warning"></i>Guía actualizada exitosamente. </h4>
</div>
<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-tabs" id="myTab">
            <li class="nav-item"><a id="navguia" class="nav-link active" href="#guia" onclick="resetForm()" data-toggle="tab">Guía de entrada</a></li>
            <li class="nav-item"><a id="navlistguia" class="nav-link" href="#listaguia" onclick="verListadoGuias()" data-toggle="tab">Lista de Guías</a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="active tab-pane" id="guia">
            <div class="row top20">
    <div class="col-md-12">
    <div class="box box-inverse box-solid">
    <div class="box-header with-border"><h3 class="box-title">Guía de Entrada</h3>
    </div>
    <div class="box-body">
    <form action="operaciones.php" method="post" class="form-horizontal" onsubmit="return validarCLI()" id="formid">
    <input type="hidden" name="operacion" id="inputoperacion" value="nuevaguia"/>
    <input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
    <div class="row" style="padding-left: 8px; padding-right: 8px;">
        <div class="col-md-6" style="border-right:1px solid grey;padding: 8px;">
        <table style="width: 100%;">
        <tr>
            <td style="padding-right: 5px;"><span style="font-weight: bold;">Bodega </span></td>
            <td style="padding-right: 5px;"><input type="text" class="form-control form-control-sm" id="inputcorrelativo" style="width: 100%;" disabled placeholder=""></td>
            <td>
                <select class="form-control form-control-sm" id="inputbodega" name="inputbodega" style="width:  295px;">
                    <option value="0">-- Seleccione Bodega --</option>
                    <option value="1">Técnicos</option>
                    <option value="2" selected>Bodega Principal</option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding-right: 5px;"><span style="font-weight: bold;width: 50px;">N° </span></td>
            <td style="padding-top: 5px;" colspan="2"><input type="text" class="form-control form-control-sm" id="inputnumero" name="inputnumero" style="width: 100%;" placeholder="" value="<?=$correlativo?>" disabled></td>
        </tr>
        <tr>
            <td style="padding-right: 5px;"><span style="font-weight: bold;">Concepto </span></td>
            <td style="padding-top: 5px;" colspan="2">
            <select class="form-control form-control-sm" id="inputconcepto" name="inputconcepto" style="width:  100%;">
                <option value="0">-- Seleccione Concepto --</option>
                <option value="1" selected>Compra producto</option>
                <option value="2">Devolución</option>
                <option value="3">Devolución</option>
            </select>
            </td>
        </tr>
        <tr>
            <td style="padding-right: 5px;"><span style="font-weight: bold;">Fecha </span></td>
            <td style="padding-top: 5px;" colspan="2"><input type="date" class="form-control form-control-sm" id="inputfecha" name="inputfecha" style="width: 100%;border-radius:8px;"></td>
        </tr>
        <tr>
            <td style="padding-right: 5px;"><span style="font-weight: bold;">Estado </span></td>
            <td style="padding-top: 5px;" colspan="2">
            <select class="form-control form-control-sm" id="inputestado" name="inputestado" style="width:  100%;">
                <option value="0">-- Seleccione Estado --</option>
                <option value="1">Técnicos</option>
                <option value="2">Bodega Principal</option>
            </select>
            </td>
        </tr>
        <tr>
            <td style="padding-right: 5px;"><span style="font-weight: bold;">Desc. </span></td>
            <td style="padding-top: 5px;" colspan="2"><input type="text" class="form-control form-control-sm" id="inputdesc" name="inputdesc" style="width: 100%;border-radius:8px;"></td>
        </tr>
        </table>
    </div>
    <div class="col-md-6" style="padding: 8px;height: 100%;">
        <table style="width: 100%;height: 100%;">
            <tr>
                <td><span style="font-weight: bold;">Proveedor </span></td>
                <td><?= htmlselect('inputproveedor','inputproveedor','proveedores','id','razonsocial','','','','razonsocial','','','si','no','no');?></td>
            </tr>
            <tr>
                <td><span style="font-weight: bold;width: 50px;">Factura </span></td>
                <td style="padding-top: 5px;"><input type="text" class="form-control form-control-sm" id="inputfactura" name="inputfactura" style="width: 100%;" placeholder=""></td>
            </tr>
        </table>
        <div class="row" style="width: 100%;margin-top: 100px;">
            <div class="col-md-6">
                <button style="color: #fff; font-weight: 700;" type="button" id="btnguardarguia" class="btn btn-sm btn-success btn-rounded" onclick="validarCampos(0)"><i class="fa fa-save"></i> Guardar guía</button>
                <button style="color: #fff; font-weight: 700;" type="button" id="btnupdateguia" class="btn btn-sm btn-success btn-rounded oculto" onclick="validarCampos(1)"><i class="fa fa-save"></i> Actualizar guía</button>
            </div>
            <div class="col-md-6">
                <span style="color: #fff; font-weight: 700;" onclick="resetForm()" class="btn btn-sm btn-danger btn-rounded"><i class="fa fa-close"></i> Cancelar</span>
            </div>
        </div>
    </div>
    <div style="padding-top: 5px;padding-bottom: 5px;border-top: 1px solid grey;width:100%">
        <table style="width: 100%;">
            <tr>
                <td colspan="11" style="text-align: center;">
                    * Las series deben ir separadas por una coma (,) - Ej: 123,543
                </td>
            </tr>
            <tr>
                <td style="width: 22%;padding-left: 5px;"><?=$opt;?></td>
                <!-- <td style="width: 8%;padding-left: 5px;"><input type="number" class="form-control form-control-sm" id="inputcantidad" style="width: 100%;border-radius: 8px;" onkeyup="getCantidad(this)" onchange="getCantidadChange(this)" placeholder="Cant."></td> -->
                <td style="width: 8%;padding-left: 5px;">
                     <input type="number" class="form-control form-control-sm" id="inputcantidad" style="width: 100%;border-radius: 8px;" placeholder="Cant.">
                </td>
                <td style="width: 22%;padding-left: 5px;" id="tdserie">
                     <input type="text" class="form-control form-control-sm" id="inputserie" style="width: 100%;border-radius: 8px;" placeholder="Serie">
                </td>
               <!--  <td style="width: 2%;padding-left: 5px;display: none;" id="tdbtnserie"><span class="btn btn-info btn-circle-s tooltips" onclick="addSerie()"><i class="fa fa-list-ol"></i><span class="tooltiptext tooltip-left">Agregar Serie</span></span></td> -->
                <!-- <td style="width: 2%;padding-left: 5px;display: none;" id="tdbtnserieview"><span class="btn btn-info btn-circle-s tooltips" onclick="verSeries()"><i class="fa fa-eye"></i><span class="tooltiptext tooltip-left">Ver Series agregadas</span></span></td> -->
                <td style="width: 12%;padding-left: 5px;"><input type="text" class="form-control form-control-sm" id="inputneto" style="width: 100%;text-align: end;" onkeyup="getNeto(this)" placeholder="Valor Neto"></td>
                <td style="width: 12%;padding-left: 5px;" class="oculto"><input type="text" class="form-control form-control-sm" id="inputiva" style="width: 100%;text-align: end;" placeholder="IVA" disabled></td>
                <td style="width: 12%;padding-left: 5px;" class="oculto"><input type="text" class="form-control form-control-sm" id="inputtotal" style="width: 100%;text-align: end;" placeholder="Total" disabled></td>
                <td style="width: 2%;padding-left: 5px;"><span class="btn btn-sm btn-success btn-circle-s tooltips" id="btnaddproducto" onclick="addItem()"><i class="fa fa-plus"></i><span class="tooltiptext tooltip-left">Agregar producto</span></span></td>
                <td style="width: 2%;padding-left: 5px;" id="tdbtncancelar" class="oculto"><span class="btn btn-sm btn-danger btn-circle-s tooltips" onclick="cancelarEdicion()"><i class="far fa-window-close"></i><span class="tooltiptext tooltip-left">Cancelar edición</span></span></td>
                <td style="width: 2%;padding-left: 5px;"><span class="btn btn-sm btn-info btn-circle-s tooltips" onclick="getPlantilla()"><i class="fa fa-download"></i><span class="tooltiptext tooltip-left">Descargar plantilla</span></span></td>
                <td style="width: 2%;padding-left: 5px;">
                    <span class="btn btn-sm btn-warning btn-circle-s tooltips text-white" id="btncargarexcelguia"><i class="far fa-file-excel"></i><span class="tooltiptext tooltip-left">Cargar plantilla</span></span>
                    <input type="file" id="excelproductosguia" style="display:none" name='excelproductosguia'/> 
                </td>
            </tr>
        </table>
    </div>
    <div id='cargandoplan' class="col-md-12 oculto "><div class='progress'><div class='progress-bar progress-bar-success progress-bar-striped active' role='progressbar'></div><span class='sr-only'></span></div></div>
    <div class="col-md-12 table-responsive" style="padding-top: 5px;border-top: 1px solid grey;">
        <table class="table table-bordered table-striped table-condensed table-hover table-sm" id="tb_agregarproducto">
            <thead class="thead-dark">
                <th>#</th>
                <th>Proveedor</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Serie</th>
                <th>Valor Neto</th>
                <th class="oculto">IVA</th>
                <th>Valor Total</th>
                <th></th>
            <tbody>
            </tbody>
        </table>
        <div  id="inp_agregarproductos">
        </div>
    </div>
    <div class="col-md-12" style="padding-top: 5px;border-top: 1px solid grey;">
        <table style="width: 100%;">
            <tbody>
                <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="width: 90px;font-weight: bold;">Valor NETO</td>
                <td align="right" style="width: 100px;font-weight: bold;" id="td_neto">$ 0</td>
                </tr>
                <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="width: 90px;font-weight: bold;">IVA</td>
                <td align="right" style="width: 100px;font-weight: bold;" id="td_iva">$ 0</td>
                </tr>
                <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="width: 90px;font-weight: bold;">TOTAL</td>
                <td align="right" style="width: 100px;font-weight: bold;" id="td_total">$ 0</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</form>
</div>
</div>
</div>
</div>
            </div>
            <div class="tab-pane" id="listaguia">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-hover table-sm" id="tbllistaguia">
                        <thead class="thead-dark">
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Factura</th>
                            <th scope="col">Cant.</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Proveedor</th>
                            <th scope="col">Bodega</th>
                            <th scope="col">Concepto</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Acción</th>
                            </tr>
                        </thead>
                            <tbody>
                                <tr><td colspan="10" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

</div>
<script>
let correlativo = <?=$correlativo?>;
let arrser      = [];
$(function(){
    $('#inputproveedor').addClass(' form-control-sm');
    $('#selectproducto').addClass(' form-control-sm');
var urlactual = window.location;
//console.log(urlactual["search"]);
var ultimaclavevalor = urlactual["search"].lastIndexOf("&"); 
//console.log(ultimaclavevalor);
estado=urlactual["search"].substring(ultimaclavevalor + 1, ultimaclavevalor.length);
sepestado = estado.split("=");
nuevocliente=sepestado[1];
if(nuevocliente=="OK"){
setTimeout(function(){ 
$("#guiaok").fadeIn(2000).fadeOut(2000);
history.pushState(null, "", "index.php?menu=nuevocliente&idmenu=80");
}, 100);
}
else if(nuevocliente=='update'){
    setTimeout(function(){ 
    $("#guiaup").fadeIn(2000).fadeOut(2000);
    history.pushState(null, "", "index.php?menu=nuevocliente&idmenu=80");
    }, 100);
}
});

let productos = [<?php for($i=0; $i<count($productos); $i++){ echo '{"pro_id":"'.$productos[$i]["pro_id"].'", "pro_codigo":"'.$productos[$i]["pro_codigo"].'", "pro_serie":"'.$productos[$i]["pro_serie"].'", "pro_familia":"'.$productos[$i]["pro_familia"].'", "pro_subfamilia":"'.$productos[$i]["pro_subfamilia"].'", "pro_marca":"'.$productos[$i]["pro_marca"].'", "pro_nombre":"'.trim($productos[$i]["pro_nombre"]).'", "pro_stockminimo":"'.$productos[$i]["pro_stockminimo"].'", "pro_stock":"'.$productos[$i]["pro_stock"].'", "pro_valor":"'.$productos[$i]["pro_valor"].'"},'; } ?>];
let series = [];

$(document).ready(function(){
    //$('#selectproveedor option:eq(0)').text('-- --');
    //$('#selectproducto option:eq(0)').text('-- Seleccione Producto --');
    $('#inputproveedor').chosen({no_results_text: "Sin resultados, el proveedor ingresado no existe!",allow_single_deselect: true});
    $('#selectproducto').chosen({no_results_text: "Sin resultados, el producto ingresado no existe!",allow_single_deselect: true});

});

function validarCampos(opc){

    var nproductos = $("#tb_agregarproducto tbody tr").length;
    if(nproductos==0){
        $('#malert .modal-header').css({'background-color':'#FF5733', 'color':'white'});
        $('#malert .modal-title').text('Información');
        $('#malert .modal-body').text('Debe agregar productos para guardar la guía.');
        $('#malert .modal-footer').html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Aceptar</button>");
        $('#malert').modal('show');
        return false;
    }
    else{

        $('#btnguardarguia').attr('disabled', true);
        $('#btnupdateguia').attr('disabled', true);
        $('#btnguardarguia').html('Cargando...');
        $('#btnupdateguia').html('Cargando...');
        var swipost = 'actualizarguia';
        var idguia  = 0;
        if(opc==1){
            swipost = 'actualizarguia';
            idguia  = $('#idguia').val();
        }else{
            swipost = 'nuevaguia';
            idguia  = 0;
        }
        let _prod     = [];
        let productos = [];
        $('#inp_agregarproductos input[type="hidden"]').each(function() {
            _prod.push($(this).val());
        });
        productos = JSON.stringify(_prod);

        let inputbodega    = $('#inputbodega').val()
        let inputnumero    = $('#inputnumero').val()
        let inputconcepto  = $('#inputconcepto').val()
        let inputfecha     = $('#inputfecha').val()
        let inputestado    = $('#inputestado').val()
        let inputdesc      = $('#inputdesc').val()
        let inputproveedor = $('#inputproveedor').val()
        let inputfactura   = $('#inputfactura').val()

        if(inputproveedor=='' || inputproveedor==undefined || inputproveedor==null){inputproveedor=0;}
        $.post("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:swipost,productos:productos,inputbodega:inputbodega,inputnumero:inputnumero,inputconcepto:inputconcepto,inputfecha:inputfecha,inputestado:inputestado,inputdesc:inputdesc,inputproveedor:inputproveedor,inputfactura:inputfactura,retornar:'no',idguia:idguia},function(data){
            if(data!='' && data!=null){
                data = $.parseJSON(data);
				console.log("Vuelta de Operaciones");
				console.log(data);
                if(data.status=='OK'){
                    
                    $('#btnguardarguia').attr('disabled', false);
                    $('#btnupdateguia').attr('disabled', false);
                    $('#btnguardarguia').html('<i class="fa fa-save"></i> Guardar guía');
                    $('#btnupdateguia').html('<i class="fa fa-save"></i> Actualizar guía');

                    if(data.incorrecto>0){
                        Swal.fire(
                            'Error',
                            'Estas series estan repetidas no se pueden ingresar - '+data.serincorrecto,
                            'error'
                        );
                    }else{
                       
                        Swal.fire(
                          'Correcto',
                          'Exito al guardar guía con ' + data.correcto + ' serie(s).',
                          'success'
                        );

                        setTimeout(function() {
                           window.location.reload();
                        }, 2000);

                       /* toastr.success('Exito al guardar guía con '+data.correcto+' serie(s).');*/
                    }
                }
                else{
                    toastr.error('Error al guardar guía.');
                    $('#btnguardarguia').attr('disabled', false);
                    $('#btnupdateguia').attr('disabled', false);
                    $('#btnguardarguia').html('<i class="fa fa-save"></i> Guardar guía');
                    $('#btnupdateguia').html('<i class="fa fa-save"></i> Actualizar guía');
                }
            }
        });
    }
}
let tieneserieval = '';
function getProductosDetails(){
    let producto = $("#selectproducto").val();
    let valor    = '';
    let serie    = '';
    series       = [];
    $('#inputcantidad').val();
    $('#inputserie').val();
    $.each(productos, function(i, item){
        if(parseInt(item.pro_id) == parseInt(producto)){
            valor         = item.pro_valor;
            serie         = item.pro_serie;
            tieneserieval = item.pro_serie;

            if(tieneserieval == 0){
                $('#inputserie').attr('disabled', true);
                $('#inputserie').val('');
            }else{
                $('#inputserie').attr('disabled', false);
                $('#inputserie').val('');
            }
        }
    });

    if(valor == '0'){
        $('#inputneto').val(0);
        $('#inputneto').val(0);
        $('#inputneto').val(0);
        if(serie == '1'){
            //$('#tdserie').show();
            $('#tdbtnserie').show();
            $('#tdbtnserieview').show();
        }
        else{
            //$('#tdserie').hide();
            $('#tdbtnserie').hide();
            $('#tdbtnserieview').hide();
        }
    }
    else{
        if(serie == '1'){
            //$('#tdserie').show();
            $('#tdbtnserie').show();
        }
        else{
            //$('#tdserie').hide();
            $('#tdbtnserie').hide();
        }
        let cantidad = $("#inputcantidad").val();
        let neto = valor;
        let iva = parseFloat(valor) * 0.19;
        let total = '';

        if(cantidad===0 || cantidad==='NaN' || cantidad===''){
            total = parseInt(neto);
        }
        else{
            total = parseInt(neto) * parseInt(cantidad);
        }
        

        $('#inputneto').val(neto);
        $('#inputiva').val(iva);
        $('#inputtotal').val(total);
    }
}

function getNeto(number){
    let cantidad = $("#inputneto").val();
    if(cantidad !== ''){
        if(cantidad === '0'){
            $('#inputtotal').val('0');
            $('#inputiva').val('0');
        }
        else{
            $('#inputtotal').val((parseInt($("#inputcantidad").val()) * parseInt($('#inputneto').val())));
            $('#inputiva').val(parseFloat(cantidad) * 0.19);
        }
    }
    else{
        $('#inputtotal').val(parseInt($('#inputneto').val()));
    }
}

function getCantidad(number){
    let cantidad = $("#inputcantidad").val();
    if(cantidad !== ''){
        if(cantidad === '0'){
            $('#inputtotal').val('0');
        }
        else{
            $('#inputtotal').val((parseInt(cantidad) * parseInt($('#inputneto').val())));
        }
    }
    else{
        $('#inputtotal').val(parseInt($('#inputneto').val()));
    }
}

function getCantidadChange(number){
    let cantidad = $("#inputcantidad").val();
    if(cantidad !== ''){
        if(cantidad === '0'){
            $('#inputtotal').val('0');
        }
        else{
            $('#inputtotal').val((parseInt(cantidad) * parseInt($('#inputneto').val())));
        }
    }
    else{
        $('#inputtotal').val(parseInt($('#inputneto').val()));
    }
}

function addSerie(){
    let seresplit = '';
    let serieTemp = $('#inputserie').val();
    let neto      = $('#inputneto').val();
    seresplit     = serieTemp.split(',');
    let cantidad  = parseInt($("#inputcantidad").val());
    if(seresplit.length == cantidad){
        $.each(seresplit,function(i,item){
            var valorsel  = $('#selectproducto').val();
            var valorpro  = $('#inputproveedor').val();
            arrser.push({'select':valorsel,'serie':item});

           /* if($('#can_'+valorpro+'-'+valorsel).text()!='' && i==0){
                var precneto = $('#net_'+valorpro+'-'+valorsel).text();
                var sumnet   = (parseInt(neto)+parseInt(precneto));
                $('#net_'+valorpro+'-'+valorsel).text(sumcan);

                var txtpro = $('#can_'+valorpro+'-'+valorsel).text();
                var sumcan = (parseInt(cantidad)+parseInt(txtpro));
                $('#can_'+valorpro+'-'+valorsel).text(sumcan);
                
            }

            if($('#ser_'+valorpro+'-'+valorsel).text()!=''){
                var txtser = $('#ser_'+valorpro+'-'+valorsel).text()+','+item;
                $('#ser_'+valorpro+'-'+valorsel).text(txtser);
            }*/
        });
    }else{
        toastr.info('No puede agregar mas serie que la cantidad ingresada ('+$("#inputcantidad").val()+')');
    }
    
}

function verSeries(idtemp){
    if(ideditar!=null){
        let producto = $('#idcon'+ideditar).val();
        let serie = producto.split('|')[7];
        let series = [];
        if(serie.split(',').length>0){
            series = serie.split(',');
            let td = '<table class="table table-sm table-striped table-bordered table-hover"><thead class="thead-dark"><th scope="col">#</th><th scope="col">Serie</th><th></th></thead><tbody>';
            $.each(series,function(i,item){
                td += '<tr id="rowsermod_'+i+'"><td scope="row">'+i+'</td><td><input type="text" class="form-control form-control-sm modrowserval" name="tdid_'+i+'" id="tdid_'+i+'" value="'+item+'"></td><td><span class="btn btn-sm btn-circle-s btn-success text-white" onclick="updateSerie('+i+','+idtemp+')"><i class="fa fa-save"></i></span><span class="btn btn-sm btn-circle-s btn-danger text-white" onclick="delserie('+i+','+idtemp+')"><i class="fa fa-trash"></i></span></td></tr>';
                /*td += '<tr id="rowsermod_'+i+'"><td scope="row">'+i+'</td><td><input type="text" class="form-control form-control-sm" name="tdid_'+i+'" id="tdid_'+i+'" value="'+item+'"></td><td><span class="btn btn-sm btn-circle-s btn-success text-white" onclick="updateSerie('+i+')"><i class="fa fa-save"></i></span> <span class="btn btn-sm btn-circle-s btn-danger text-white" onclick="deleteSerie('+i+')"><i class="fa fa-trash"></i></span></td></tr>';*/
            });
            td += '</tbody></table>';
            $('#malert .modal-header').css({'background-color':'#33A2FF', 'color':'white'});
            $('#malert .modal-title').text('Series');
            $('#malert .modal-body').html(td);
            $('#malert .modal-footer').html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cerrar</button>");
            $('#malert').modal('show');
        }
    }else{
        /*let _series = arrser.split(',');*/
        let td = '<table class="table table-sm table-striped table-bordered table-hover"><thead class="thead-dark"><th scope="col">#</th><th scope="col">Serie</th><th scope="col"></th></thead><tbody>';

        var ser     = $('#ser_'+idtemp).text();
        var spliser = ser.split(','); 

        $.each(spliser,function(i,item){
            /*td += '<tr id="rowsermod_'+i+'"><td scope="row">'+(i+1)+'</td><td><input type="text" class="form-control form-control-sm" name="tdid_'+i+'" id="tdid_'+i+'" value="'+item+'"></td><td><span class="btn btn-sm btn-circle-s btn-success text-white" onclick="updateSerie('+i+')"><i class="fa fa-save"></i></span> <span class="btn btn-sm btn-circle-s btn-danger text-white" onclick="deleteSerie('+i+','+idtemp+')"><i class="fa fa-trash"></i></span></td></tr>';*/
            td += '<tr id="rowsermod_'+i+'"><td scope="row">'+(i+1)+'</td><td><input type="text" class="form-control form-control-sm modrowserval" name="tdid_'+i+'" id="tdid_'+i+'" value="'+item+'"></td><td><span class="btn btn-sm btn-circle-s btn-success text-white" onclick="updateSerie('+i+','+idtemp+')"><i class="fa fa-save"></i></span><span class="btn btn-sm btn-circle-s btn-danger text-white" onclick="delserie('+i+','+idtemp+')"><i class="fa fa-trash"></i></span></td></tr>';
           
        });

        /*$.each(arrser,function(i,item){
            var selalb  = $('#selectproducto').val();
            if(item.select == selalb){
                 td += '<tr><td scope="row">'+(i+1)+'</td><td><input type="text" class="form-control form-control-sm" name="tdid_'+i+'" id="tdid_'+i+'" value="'+item.serie+'"></td><td><span class="btn btn-sm btn-circle-s btn-success text-white" onclick="updateSerie('+i+')"><i class="fa fa-save"></i></span> <span class="btn btn-sm btn-circle-s btn-danger text-white" onclick="deleteSerie('+i+')"><i class="fa fa-trash"></i></span></td></tr>';
            } 
        });  */
        td += '</tbody></table>';

        $('#malert .modal-header').css({'background-color':'#33A2FF', 'color':'white'});
        $('#malert .modal-title').text('Series');
        $('#malert .modal-body').html(td);
        $('#malert .modal-footer').html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cerrar</button>");
        $('#malert').modal('show');
    }
}

function delserie(i,idtemp){
    $('#rowsermod_'+i).remove();
    var cant = (parseInt($('#can_'+idtemp).text())-1);
    $('#can_'+idtemp).text(cant);

    var seriesnueva = '';
    var seriesnuevafin = '';
    $(".modrowserval").each(function(){
        seriesnueva += $(this).val()+',';
    });

    if(seriesnueva!=''){
        seriesnuevafin = seriesnueva.slice(0, -1);
        $('#ser_'+idtemp).text(seriesnuevafin);
    }
    
    var neto = $('#net_'+idtemp).text().replaceAll("$","").replaceAll(" ","").replaceAll(".","").replaceAll(",","");
    var tot = 0;
    if(neto!='' && cant!=''){
        tot = parseInt(neto)*parseInt(cant);
        $('#tot_'+idtemp).text('$'+tot);
    }
        
    var totdetot = 0;
    if(tot!=0){
        $(".totalalb").each(function(){
            totdetot = (parseInt($(this).text().replaceAll("$","").replaceAll(" ","").replaceAll(".","").replaceAll(",",""))+totdetot);
        });

        var iva = totdetot*0.19;
        var suma = parseInt(iva)+parseInt(totdetot);
        $('#td_neto').text('$'+new Intl.NumberFormat('es-CL').format(totdetot))
        $('#td_iva').text('$'+new Intl.NumberFormat('es-CL').format(iva))
        $('#td_total').text('$'+new Intl.NumberFormat('es-CL').format(suma))

        var datosguarda = $('#idcon'+idtemp).val();
        var datguar = datosguarda.split('|');

        var nuevostring = '';
        $.each(datguar, function(i, item){

            if(i==2 || i==6){
                if(i==2){
                    nuevostring += cant+'|';
                }

                if(i==6){
                    nuevostring += seriesnuevafin+'|';
                }
            }else{
                nuevostring += datguar[i]+'|';
            }   
        });
        nuevostring = nuevostring.slice(0, -1);
        $('#idcon'+idtemp).val(nuevostring)
    }
}

function updateSerie(index,idtem){
    
    var valorunmodser = $('#tdid_'+index).val();
    var seriactutemp  = $('#ser_'+idtem).text();
    var spliseractemp = seriactutemp.split(',');
    var rep           = 0;
    var nameants      = '';
    $.each(spliseractemp, function(i, item){
         if(valorunmodser==item){
             rep++;
         }
    });

    if(rep>0){
        toastr.error('No se pueden ingresar series repetidas');
    }else{
        $('.modrowserval').each(function() {
             nameants += $(this).val()+',';
           
        });
        const str2 = nameants.slice(0, -1);
        //var str2 = nameants.substr(0, nameants.length - 1);
        $('#ser_'+idtem).text(str2);

        var hiddenenvio = $('#idcon'+idtem).val();
        var splihidden  = hiddenenvio.split('|');
        var indeach     = 1;
        hiddenenvio = '';
        $.each(splihidden, function(i, item){
            if(indeach==7){
                hiddenenvio += $('#ser_'+idtem).text();
            }else{
                 hiddenenvio += item+'|';
            }
            indeach++; 
        });

        $('#idcon'+idtem).val(hiddenenvio);
    }
    
    /*if(series.length>0){
        let serieTemp = [];
        $.each(series,function(i,item){
            let temp = '';
            if(i==index){
                temp = $('#tdid_'+index).val();
            }
            else{
                temp = item;
            }
            serieTemp[i] = temp;
        });
        series = serieTemp;
        console.log(series);
    }*/
}

function deleteSerie(index,idtem){
    /*let seriesTemp = [];
    let indice = 0;
    $.each(series,function(i,item){
        if(i!=parseInt(index)){
            seriesTemp[indice] = item;
            indice++;
        }
    });*/
    var ser           = $('#ser_'+idtem).text();
    var spliser       = ser.split(','); 

    if(spliser.length<=1){
         $('#con_fila'+idtem).remove();
         $('#rowsermod_'+index).remove();
          $('#malert .modal-body').html('<tr><td colspan="3">Sin series para mostrar</td></tr>');
    }else{
         var sacar = $('#tdid_'+index).val();
         var txt   = $('#ser_'+idtem).text();

         if(index>0){
             $('#ser_'+idtem).text(txt.replace(','+sacar, ''));
         }else{
             $('#ser_'+idtem).text(txt.replace(sacar+',', ''));
         }
         
         $('#rowsermod_'+index).remove();

         /*restamientras = spliser.length-1; 
         if(restamientras<=0){
            td += '<tr><td colspan="3">Sin series para mostrar</td></tr>';
            $('#malert .modal-body').html(td);
         }*/
    }

    

    // series = seriesTemp;
    // let td = '<table class="table table-sm table-striped table-bordered table-hover"><thead class="thead-dark"><th scope="col">#</th><th scope="col">Serie</th><th scope="col"></th></thead><tbody>';
    // $.each(series,function(i,item){
    //     td += '<tr><td scope="row">'+(i+1)+'</td><td>'+item+'</td><td><span class="btn btn-sm btn-circle-s btn-danger text-white" onclick="deleteSerie('+i+')"><i class="fa fa-trash"></i></span></td></tr>';
    // });
    // if(td=='<table class="table table-sm table-striped table-bordered table-hover"><thead class="thead-dark"><th scope="col">#</th><th scope="col">Serie</th><th scope="col"></th></thead><tbody>'){
    //     td += '<tr><td colspan="3">Sin series para mostrar</td></tr>';
    // }
    // td += '</tbody></table>';
    // $('#malert .modal-body').html(td);
}

function addItem(){

    let seresplit = '';
    let cantidad  = 0;
    let serieTemp = $('#inputserie').val();
    let neto      = $('#inputneto').val();
    seresplit     = serieTemp.split(',');
    cantidad  = parseInt($("#inputcantidad").val());

    var versies = serieTemp.split(',')[1];
    var serienocumple = '';

    var productosel = $("#selectproducto").val();
    var valorSeleccionado = $('#selectproducto option:selected').data('serie');

    var minsel = 5;
    var maxsel = 20;
    //if(productosel=='167'){
    //    minsel = 8;
    //    maxsel = 8;
    //}

    $band = true;
    if( !valorSeleccionado ){
        $band = false;
    }

    console.log(" productosel : ",productosel, " valor seleccionado : ", valorSeleccionado);

    if( (versies!=undefined && versies!='') ){
        $.each(serieTemp.split(','), function(i, item) {
            if(item.length>maxsel || item.length<minsel){
                serienocumple += item+"<br>";
            }
        })

        if(serienocumple!=''){
            Swal.fire(
              'Estas series no cumplen con el largo permitido',
               serienocumple,
              'warning'
            )
            return;
        }
    }else{
        if($band){
            if(serieTemp.length>maxsel || serieTemp.length<minsel){
                Swal.fire(
                'Estas series no cumplen con el largo permitido',
                serieTemp,
                'warning'
                )
                return;
            }
        }
        
    } 

    if(seresplit.length == cantidad || tieneserieval==0){

        if(tieneserieval==0){
            if($("#inputneto").val()<=0 || $("#selectproducto").val()=='' || $("#inputcantidad").val()==''){
                $('#malert .modal-header').css({'background-color':'#FF5733', 'color':'white'});
                $('#malert .modal-title').text('Información');
                $('#malert .modal-body').text('Debe agregar una cantidad o precio neto para agregar el producto.');
                $('#malert .modal-footer').html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Aceptar</button>");
                $('#malert').modal('show');
                $("#inputcantidad").val(0);
                $('#inputneto').val('');
                return;
            }
        }else{
            if(($("#inputcantidad").val()=='' || $("#inputcantidad").val()=='0') || ($("#inputserie").val()=='') || ($("#inputneto").val()<=0) || ($("#selectproducto").val()=='')){
                $('#malert .modal-header').css({'background-color':'#FF5733', 'color':'white'});
                $('#malert .modal-title').text('Información');
                $('#malert .modal-body').text('Debe agregar una cantidad,serie, o precio neto para agregar el producto.');
                $('#malert .modal-footer').html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Aceptar</button>");
                $('#malert').modal('show');
                return;
            }
        }
        
        if($("#inputproveedor").val() === ''){
            $('#malert .modal-header').css({'background-color':'#FF5733', 'color':'white'});
            $('#malert .modal-title').text('Información');
            $('#malert .modal-body').text('Debe seleccionar un proveedor para agregar un producto.');
            $('#malert .modal-footer').html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Aceptar</button>");
            $('#malert').modal('show');
            return;
        }

        var nproductos = $("#tb_agregarproducto tbody tr").length;
        nproductos     = nproductos+1;
        let proveedor  = $("#inputproveedor").val();
        let producto   = $("#selectproducto").val();
        let cantidad   = $("#inputcantidad").val();
        let serie      = '';
        let serieSend  = '';
        let neto       = $("#inputneto").val();
        let iva        = $("#inputiva").val();
        let total      = $("#inputtotal").val();
        let valor      = '';

        $.each(productos, function(i, item){
            if(parseInt(item.pro_id) === parseInt(producto)){
                valor = item.pro_valor;
            }
        });
        seriejun     = $('#inputserie').val();
        var spliser  = seriejun.split(','); 
        var arrrep   = [];
        var repetido = 0;
        if(spliser.length>0){
            $.each(spliser, function(i, item){
                 $.each(arrrep, function(e, iteme){
                     if(item==iteme){
                         repetido ++;
                     } 
                });
                 arrrep.push(item);
            }); 
        }else{
            seriejun = '';
        }
        
        var nproductos  = 0;
        var indexmuesra = 1;
        $('.recindex').each(function() {
             var nameants = $(this).attr('name');
             var soliname = nameants.split('_');
             nproductos   = parseInt(soliname[1])+1;
             indexmuesra++;
        });

        if(nproductos==0){
             nproductos  = 1;
             indexmuesra = 1;
        }

        if(repetido>=1){
             toastr.error('No se pueden ingresar series repetidas');
        }else{
            let rows = "<tr id='con_fila"+nproductos+"'><td class='recindex' name='index_"+nproductos+"'>"+indexmuesra+"</td><td>"+$("#inputproveedor option:selected").text()+"</td><td>"+$("#selectproducto option:selected").text()+"</td><td id='can_"+nproductos+"' .cantidad>"+cantidad+"</td><td id='ser_"+nproductos+"'>"+seriejun+"</td><td id='net_"+nproductos+"' .neto>$ "+neto+"</td><td class='oculto'>$ "+iva+"</td><td id='tot_"+nproductos+"' class='totalalb' .total>$ "+total+"</td><td><span class='btn btn-sm btn-danger btn-circle-s' onclick='removeItem("+nproductos+")'><i class='fa fa-trash'></i></span>&nbsp;<span class='btn btn-sm btn-warning btn-circle-s' onclick='verSeries("+nproductos+")'><i class='fa fa-edit'></i></span></td></tr>";
            $('#tb_agregarproducto tbody').append(rows);
            autoSuma();
            arrser = [];


            $("#inp_agregarproductos").append("<input type='hidden' id='idcon"+nproductos+"' name='productos[]' value=\""+proveedor+"|"+producto+"|"+cantidad+"|"+neto+"|0|0|"+seriejun+"|"+tieneserieval+"\">");

            // $("#selectproducto").val('');
            $("#inputcantidad").val('');
            $("#inputneto").val('0');
            $("#inputiva").val('');
            $("#inputtotal").val('');
            series = [];
            $('#inputserie').val('');
            $('#tdbtnserie').hide();
            $('#tdbtnserieview').hide();
        }
   
    }else{
        toastr.info('No puede agregar mas serie que la cantidad ingresada ('+$("#inputcantidad").val()+')');
    }
}

function autoSuma(){
    let total = '';
    $("#tb_agregarproducto tbody tr").each(function(index) {
        //console.log($(this).find("td").eq(7).text());
        if(total !== ''){
            let temp = $(this).find("td").eq(7).text().replace('$ ','').replace(' ', '');
            total = (parseInt(total) + parseInt(temp));
        }
        else{
            total = $(this).find("td").eq(7).text().replaceAll('$ ','').replaceAll(' ', '');
        }
    });
    if(total==''){
        total = '0';
    }
    //$('#td_neto').text("$ "+(parseInt(total)-parseInt(parseFloat(total) * 0.19)));
    $('#td_neto').text("$ "+new Intl.NumberFormat('es-CL').format(total));
    $('#td_iva').text("$ "+new Intl.NumberFormat('es-CL').format(Math.round(parseInt(total) * 0.19)));
    $('#td_total').text("$ "+new Intl.NumberFormat('es-CL').format(Math.round(parseInt(total)+parseInt(total) * 0.19)));
}

function modificarItem(id){
    let serie = '';
    let serieSend = '';
    let prove = $("#inputproveedor").val();
    let produ = $("#selectproducto").val();
    let canti = $("#inputcantidad").val();
    let valor = ($('#idcon'+id).val()).split('|')[3];
    let neto  = $("#inputneto").val();
    let iva = parseInt(parseFloat(neto) * 0.19);
    let total = (parseInt(canti)*parseInt(neto));
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
    $('#idcon'+id).val(prove+'|'+produ+'|'+canti+'|'+valor+'|'+neto+'|'+iva+'|'+total+'|'+serie);
    $("#tb_agregarproducto tbody tr").each(function(i,index) {
        if(parseInt(id)==(i+1)){
            $(this).find("td").eq(1).text($("#inputproveedor option:selected").text());
            $(this).find("td").eq(2).text($("#selectproducto option:selected").text());
            $(this).find("td").eq(3).text(canti);
            $(this).find("td").eq(4).text(serieSend);
            $(this).find("td").eq(5).text('$ '+neto);
            $(this).find("td").eq(7).text('$ '+total);
        }
    });
    autoSuma();
    $("#selectproducto").val('');
    $("#inputcantidad").val('');
    $("#inputneto").val('0');
    $("#inputiva").val('');
    $("#inputtotal").val('');
    $('#btnaddproducto').html('<i class="fa fa-plus"></i><span class="tooltiptext tooltip-left">Agregar producto</span>')
                .removeClass('btn-warning').addClass('btn-success').attr('onclick','addItem()');
    $('#tdbtncancelar').hide();
    $('#tdserie').hide();
    $('#tdbtnserie').hide();
    $('#tdbtnserieview').hide();
    ideditar = null;
}

let ideditar = null;
function editarItem(id){
    //"+proveedor+"|"+producto+"|"+cantidad+"|"+valor+"|"+neto+"|"+iva+"|"+total+"|"+serie+"
    
    // if(opc==1){
    //     id = (parseInt(id)+1);
    // }
    ideditar = id;
    let producto = $('#idcon'+id).val();
    $("#inputproveedor").val(producto.split('|')[0]);
    $("#selectproducto").val(producto.split('|')[1]);
    $("#inputcantidad").val(producto.split('|')[2]);
    $("#inputneto").val(producto.split('|')[4]);
    let serieTemp = producto.split('|')[7];
    if(serieTemp!=''){
        $.each(serieTemp.split(','),function(i,item){
            series[i] = item;
        });
    }
    if(series.length>0){
        $('#tdserie').show();
        $('#tdbtnserie').show();
        $('#tdbtnserieview').show();
    }
    $('#btnaddproducto').html('<i class="fa fa-edit"></i><span class="tooltiptext tooltip-left">Modificar producto</span>')
                .removeClass('btn-success').addClass('btn-warning').attr('onclick','modificarItem('+id+',1)');
    $('#tdbtncancelar').show();
    console.log(producto);
}

function cancelarEdicion(){
    $('#btnaddproducto').html('<i class="fa fa-plus"></i><span class="tooltiptext tooltip-left">Agregar producto</span>')
                .removeClass('btn-warning').addClass('btn-success').attr('onclick','addItem()');
    $("#selectproducto").val('');
    $("#inputcantidad").val('');
    $("#inputneto").val('0');
    $('#tdbtncancelar').hide();
    $('#tdserie').hide();
    $('#tdbtnserie').hide();
    $('#tdbtnserieview').hide();
    ideditar = null;
}

function removeItem(id,iditem='',idguia=''){

    swal.fire({
         title: '\u00BFEstas seguro de eliminarlo?',
         text: "Este ya no aparecer\u00E1 en la lista",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: 'Confirmar'
    }).then((result) => {
         if (result.isConfirmed){
             if(iditem==''){
                $("#con_fila"+id+", #idcon"+id+"").remove();
                autoSuma();
                var indeselim = 1;
                $('.recindex').each(function() {
                     $(this).text(indeselim);
                     indeselim ++;
                });   
            }else{
                $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'borrarItemGuia',iditemguia:iditem,retornar:'no',idgui:idguia},function(data){
                    if(data!='' && data!=null){
                        data = $.parseJSON(data);
                        if(data.status=='OK'){
                            $("#con_fila"+id+", #idcon"+id+"").remove();
                            autoSuma();
                        }
                        else{
                            toastr.error('Error al borrar item de guía.');
                        }
                    }
                    
                });
            }
         }
    })

    
}

function getPlantilla(){

    var send = '';
    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getPlantillaGuiaEntrada',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {
               
        },error   : function(respuesta) {
                
        },success : function(data) {
            var $a = $("<a>");
            $a.attr("href", data.file);
            $("body").append($a);
            $a.attr("download", "Plantilla.xlsx");
            $a[0].click();
            $a.remove();
        }
    });
    
    // $.get('operaciones.php',{bncnr:''+Math.floor(Math.random()*9999999)+'',operacion:'getPlantillaGuiaEntrada',retornar:'no'}, function(data) {
    //     window.location.href = data;
    // });
    /*let url = "operaciones.php?operacion=getPlantillaGuiaEntrada";
    let a = document.createElement('a');
    a.target="_blank";
    a.href=url;
    a.click();*/
}

$('#btncargarexcelguia').click(function(){ 
    $('#excelproductosguia').trigger('click'); 
});

$("input[name='excelproductosguia']").change(function(e){
    doc = $(this).prop('files')[0];
    if(typeof doc !=="undefined"){
    CargarDocumentoGuiaEntrada(doc);
    this.value = null;
    }else{
    }
});

function CargarDocumentoGuiaEntrada(documento){
    
    var file_data = documento;
    var form_data = new FormData();
    fecha = $('input[name="fecha"]').val();
    form_data.append('operacion','importProductosGuia');
    form_data.append('fecha',fecha);
    form_data.append('archivoexcel', file_data);
    form_data.append('retornar','no');

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

            if (e.lengthComputable) {

            }
            };
            xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
            porcentaje=parseInt((e.loaded / e.total)*100);
            $("#cargandoplan .progress-bar").css({width:""+porcentaje+"%"});
            $("#cargandoplan .progress-bar").html(porcentaje+"%");
            $("#cargandoplan .sr-only").html(porcentaje+"%");

            }};
            return xhr;
        },
        success: function(respuesta){
            setTimeout(function(){ 
				$("#cargandoplan").hide();
			}, 2000);
            if(respuesta!=='' && respuesta!==null){
                respuesta = $.parseJSON(respuesta);
                console.log(respuesta);
                
                if(respuesta.length>0){
                    //$("#inputproveedor").chosen();
                    $.each(respuesta, function(i, item){
                        var nproductos = $("#tb_agregarproducto tbody tr").length;
                        nproductos=nproductos+1;

                        var str         = item.serie.toString();
                        var spliserdoc  = str.split('.');
                        var txtserfin   = ''; 
                        $.each(spliserdoc, function(ie, iteme){
                             txtserfin += iteme+',';
                        });

                        const str2 = txtserfin.slice(0, -1);

                        let rows = "<tr id='con_fila"+nproductos+"'><td class='recindex' name='index_"+nproductos+"'>"+nproductos+"</td><td>"+item.proveedor+"</td><td>"+item.producto+"</td><td id='can_"+nproductos+"' .cantidad>"+item.cantidad+"</td><td id='ser_"+nproductos+"'>"+str2+"</td><td id='net_"+nproductos+"' .neto>$ "+item.neto+"</td><td  class='oculto'>$ "+item.iva+"</td><td id='tot_"+nproductos+"' class='totalalb' .total>$ "+item.total+"</td><td><span class='btn btn-sm btn-danger btn-circle-s' onclick='removeItem("+nproductos+")'><i class='fa fa-trash'></i></span>&nbsp;<span class='btn btn-sm btn-warning btn-circle-s' onclick='verSeries("+nproductos+")'><i class='fa fa-edit'></i></span></td></tr>";

                        $('#tb_agregarproducto tbody').append(rows);
                        autoSuma();
                        $("#inp_agregarproductos").append("<input type='hidden' id='idcon"+nproductos+"' name='productos[]' value=\""+item.idproveedor+"|"+item.idproducto+"|"+item.cantidad+"|"+item.neto+"|0|0|"+str2+"|"+tieneserieval+"\">");
                        
                        $('#inputproveedor').val(item.proveedor).trigger("chosen:updated");
                        $('#selectproducto').val(item.proveedor).trigger("chosen:updated");
                        
                    });
                    //$("#inputproveedor").chosen();
                    //$('#inputproveedor').trigger("liszt:updated");
                }
            }
        }
    });
}

function resetForm(){
    $('#idguia').remove();
    $('#inputbodega').val(2);
    $('#inputnumero').val(correlativo);
    $('#inputconcepto').val(1);
    $('#inputfecha').val('');
    $('#inputestado').val(0);
    $('#inputdesc').val('');
    $('#inputproveedor').val('').trigger("chosen:updated");
    $('#selectproducto').val('').trigger("chosen:updated");
    $('#inputfactura').val('');
    $('#btnguardarguia').show();
    $('#btnupdateguia').hide();
    $('#inputoperacion').val('nuevaguia');
    $('#selectproducto').val('').trigger("chosen:updated");
    $('#td_neto , #td_iva , #td_total').text('');
    $('#tb_agregarproducto tbody').html('');
    $('#inp_agregarproductos').html('');
    $('#tbllistaguia tbody').html('');
    $('#tbllistaguia').DataTable().destroy();
    listaGuias = [];
    series = [];
    ideditar = null;
}

/////////////////////////////    funciones para listado de guias ////////////////////////////

let listaGuias = [];
function verListadoGuias(){
    $('.tool').tooltip('dispose')
    $('#inp_agregarproductos').html('');
    if ($.fn.DataTable.isDataTable('#tbllistaguia')) {
        $('#tbllistaguia').DataTable().destroy();
    }

    $('#tbllistaguia tbody').html('<tr><td colspan="10" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getListGuia',retornar:'no'},function(data){
        if(data!='' && data!=null){
            data = $.parseJSON(data);
            //console.log(data.tbody);
            if(data.tbody.length>0){
                listaGuias = data.data;
                $('#tbllistaguia tbody').html(data.tbody);
                $('#tbllistaguia').DataTable({
                    "language": {
                        url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'
                    },
                    "paging": true,
                    "lengthChange": true,
                    "lengthMenu": [[20,-1], [20,"Todos"]],
                    "pageLength": 20,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "order": [[3, "desc"]]
                });
                $('.tool').tooltip()
            }
            else{
                $('#tbllistaguia tbody').html('');
                $('#tbllistaguia').DataTable({
                    "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                    "paging": true,
                    "lengthChange": true,
                    "lengthMenu": [[20,-1], [20,"Todos"]],
                    "pageLength":20,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "order": [[3, "desc"]]
                });
            }
        }
    });
}

function cerrarListadoGuias(){
    $('#inp_agregarproductos').html('');
    $('#inputbodega').val(0);
    $('#inputnumero').val(<?=$correlativo?>);
    $('#inputconcepto').val(1);
    const hoy = new Date();
    var fech  = formatoFecha(hoy, 'dd-mm-yyyy');
    $('#inputfecha').val(fech);
    $('#inputestado').val(0);
    $('#inputdesc').val();
    $("#inputproveedor").trigger("chosen:updated");
    $("#selectproducto").trigger("chosen:updated");
    $('#inputfactura').val();
    $('#btnguardarguia').hide();
    $('#btnupdateguia').show();
    $('#inputoperacion').val('actualizarguia');
    $('#tbllistaguia tbody').html('');
    $('#tbllistaguia').DataTable().destroy();
    $('#tb_agregarproducto tbody').html('');
}

function formatoFecha(fecha, formato) {
    //
}

function borrarGuia(id){

    Swal.fire({
         title: '\u00BFEstas seguro de eliminarlo?',
         text: "Este ya no aparecer\u00E1 en la lista",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: 'Confirmar'
    }).then((result) => {
         if (result.isConfirmed){
            $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'borrarGuiaEntrada',idguia:id,retornar:'no'},function(data){
                if(data!='' && data!=null){
                    data = $.parseJSON(data);
                    if(data.status=='OK'){
                        toastr.success('Guía borrada con exito.');
                        $('#tbllistaguia').DataTable().destroy();
                        verListadoGuias();
                    }
                    else{
                        toastr.success('Error al borrar guía.');
                    }
                }
                else{
                    toastr.success('Error inesperado, intentelo nuevamente.');
                }
            });
         }
    })
    
}

function editarGuia(id){
    $('.tool').tooltip('dispose')
    $('#editgui_'+id).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
    $('#editgui_'+id).attr('disabled', true);
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'verGuiaEntrada',idguia:id,retornar:'no'},function(data){
        console.log(data);
        console.log(data.length);
        if(data!='' && data!=null){
            $('#editgui_'+id).html('<i class="fa fa-edit"></i>');
            $('#editgui_'+id).attr('disabled', false);
            data = $.parseJSON(data);
            if(data.idguia!= undefined && data.idguia!=0){
                let guia = data.idguia;
                $('#formid').append('<input type="hidden" name="idguia" id="idguia" value="'+id+'">');
                $('#inputbodega').val(data.bodega);
                $('#inputnumero').val(data.idguia);
                $('#inputconcepto').val(data.concepto);
                $('#inputfecha').val(data.fecha);
                $('#inputestado').val(data.estado);
                $('#inputdesc').val(data.descuento);
               /* $('#inputproveedor').val(guia.proveedor);*/
                $("#inputproveedor").trigger("chosen:updated");
                $("#selectproducto").trigger("chosen:updated");
                $('#inputfactura').val(data.factura);
                $('#btnguardarguia').hide();
                $('#btnupdateguia').show();
                $('#inputoperacion').val('actualizarguia');
                let td    = '';
                productos = [];
                $.each(data.lista,function(i,item){  
                    var onclick = 'removeItem('+(i+1)+',"'+item.idseries+'",'+data.idguia+')';
                    td += '<tr id="con_fila'+(i+1)+'" >';
                    td += '<td class="recindex" name="index_'+(i+1)+'">'+(i+1)+'</td>';
                    td += '<td>'+item.nombre_proveedor+'</td>';
                    td += '<td>'+item.nombre_prod+'</td>';
                    td += '<td id="can_'+(i+1)+'">'+item.cantidad+'</td>';
                    td += '<td id="ser_'+(i+1)+'">'+item.series+'</td>';
                    td += '<td id="net_'+(i+1)+'">$ '+item.neto+'</td>';
                    td += '<td class="oculto"></td>';
                    td += '<td>$ '+(parseInt(item.cantidad)*parseInt(item.neto))+'</td>';
                    td += "<td><span class='btn btn-sm btn-danger btn-circle-s' onclick='"+onclick+"'><i class='fa fa-trash'></i></span>&nbsp;<span class='btn btn-sm btn-warning btn-circle-s' onclick='verSeries("+(i+1)+")'><i class='fa fa-edit'></i></span></td>";
                    td += '</tr>';
                    $('#tb_agregarproducto tbody').append(td);
                    $("#inp_agregarproductos").append("<input type='hidden' id='idcon"+(i+1)+"' name='productos[]' value=\""+item.idproveedor+"|"+item.idproducto+"|"+item.cantidad+"|"+item.neto+"|"+item.idseries+"|"+data.idguia+"|"+item.series+"|"+tieneserieval+"\">");

                });

                $('#tb_agregarproducto tbody').html(td);
                autoSuma();
                $('#tbllistaguia tbody').html('');
                $('#tbllistaguia').DataTable().destroy();
                $('#myTab a[href="#guia"]').tab('show');
                $('.tool').tooltip()
            }else{
                $('#editgui_'+id).html('<i class="fa fa-edit"></i>');
                $('#editgui_'+id).attr('disabled', false);
            }
        }else{
            $('#editgui_'+id).html('<i class="fa fa-edit"></i>');
            $('#editgui_'+id).attr('disabled', false);
        }
    });
}

</script>