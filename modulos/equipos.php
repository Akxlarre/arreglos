<?php
$sql1       = "SELECT * FROM productos";
$res1       = $link->query($sql1);
$productos  = array();
while($fila = mysqli_fetch_array($res1)){
    $sql = "select t1.ser_codigo as cxp_codigo,t1.ser_estado as cxp_estado
            from serie_guia t1
            where t1.ser_estado = 1 and t1.pro_id = ".$fila['pro_id']."";
    $res    = $link->query($sql);
    $series = '';
    while($filas = mysqli_fetch_array($res)){
        $series .= $filas['cxp_codigo'].',';
    }

    $productos[]=array('pro_id'=>$fila['pro_id'], 'pro_codigo'=>$fila['pro_codigo'], 'pro_serie'=>$fila['pro_serie'], 'pro_familia'=>$fila['pro_familia'], 'pro_subfamilia'=>$fila['pro_subfamilia'], 'pro_marca'=>$fila['pro_marca'], 'pro_nombre'=>$fila['pro_nombre'], 'pro_stockminimo'=>$fila['pro_stockminimo'], 'pro_stock'=>$fila['pro_stock'], 'pro_valor'=>$fila['pro_valor'], 'dataseries'=>$series);
}

$sql3 = "SELECT * FROM familias ORDER BY fam_id";
$res3 = $link->query($sql3);
$opt  = '<select style="width:25%;" onchange="getProductosDetails(1)" tabindex="5" data-placeholder="Seleccione GPS" class="chosen-select" id="selectproducto" name="selectproducto"><option value=""></option>';
$opt1 = '<select style="width:25%;" onchange="getProductosDetails(2)" id="selectaccesorio" name="selectaccesorio" data-placeholder="Seleccione Sim" class="chosen-select" tabindex="4">';
while($fila3 = mysqli_fetch_array($res3)){
    $opt    .= '<optgroup style="color:#338AFF;" label="'.$fila3['fam_nombre'].'">';
    $opt1   .= '<optgroup style="color:#338AFF;" label="'.$fila3['fam_nombre'].'">';
    $sql1    = "SELECT * FROM productos pro LEFT OUTER JOIN familias fam ON pro.pro_familia=fam.fam_id WHERE pro.pro_familia={$fila3['fam_id']} ORDER BY pro.pro_nombre";
    $res1 = $link->query($sql1);
    while($fila1 = mysqli_fetch_array($res1)){
        $opt    .= '<option value="'.$fila1['pro_id'].'">'.$fila1['pro_nombre'].'</option>';
        $opt1   .= '<option value="'.$fila1['pro_id'].'">'.$fila1['pro_nombre'].'</option>';
    }
    $opt  .= '</optgroup>';
    $opt1 .= '</optgroup>';
}
$opt  .= '</select>';
$opt1 .= '</select>';

$sql2        = "SELECT * FROM sensores where sen_estado=1";
$res2        = $link->query($sql2);
$sensores    = array();
$thsensores  = '';
while($fila2 = mysqli_fetch_array($res2)){
    $sensores[] = array(
        'id'     => $fila2['sen_id'],
        'nombre' => $fila2['sen_nombre'],
        'fecha'  => $fila2['sen_create_at'],
        'estado' => $fila2['sen_estado'],
    );
    $thsensores .= '<th scope="col">'.ucfirst(strtolower($fila2['sen_nombre'])).'</th>';
}
?>
<style>
    #contentMap{
      transform:translate(1000px,0);
        -webkit-transform:translate(1000px,0);
        -o-transform:translate(1000px,0);
        -moz-transform:translate(1000px,0);
        transition:all 2s ease-in-out;
        -webkit-transition:all 2s ease-in-out;
        -moz-transition:all 2s ease-in-out;
        -o-transition:all 2s ease-in-out;
    }
</style>
<div class="modal" id="malert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="card mt-1">
        <div class="card-header p-1">
            <h4>Configurar equipos</h4>
        </div>
        <div class="card-body">
            <div class="row mt-2 pl-3">
                <div class="col-md-3">
                    <div class="form-group row">
                        <label for="fdesde" class="col-sm-3 col-form-label">Desde</label>
                        <div class="col-sm-9">
                        <input type="date" class="form-control form-control-sm" id="fdesde" value="2021-01-01">
                        </div>
                    </div>  
                </div>
                <div class="col-md-3">
                    <div class="form-group row">
                        <label for="fhasta" class="col-sm-3 col-form-label">Hasta</label>
                        <div class="col-sm-9">
                        <input type="date" class="form-control form-control-sm" id="fhasta" value="<?=hoydate()?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" onclick="filtrar()" class="btn btn-primary btn-sm">Filtrar</button>
                    &nbsp;&nbsp;&nbsp;
                    <button type="submit" onclick="cancelarfiltrar()" class="btn btn-danger btn-sm">Resetear</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-inline">
                    &nbsp;&nbsp;&nbsp;
                    <?=$opt?>
                    &nbsp;&nbsp;&nbsp;
                    <select style="width:15%;" id="selectSeries1" onchange="changeSerie(1)" name="selectSeries1" data-placeholder="Seleccione Serie" class="chosen-select" tabindex="2"><option value=""></option></select>
                    <!-- &nbsp;&nbsp;&nbsp;
                    <?=$opt1?> -->
                    &nbsp;&nbsp;&nbsp;
                    <!-- <select style="width:15%;" id="selectSeries2" onchange="changeSerie(2)" name="selectSeries2" data-placeholder="Seleccione Serie" class="chosen-select" tabindex="4"><option value=""></option></select>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                    <button style="width:7%;" id="btnasociaritem" type="button" onclick="asociarItems(0)" class="btn btn-sm btn-primary">Asociar</button>
                    &nbsp;&nbsp;&nbsp;
                    <button style="width:7%;" id="btncancelaritem" onclick="resetForm()" type="button" class="btn btn-sm btn-danger oculto">Cancelar</button>
                    <input type="hidden" name="hdn_id" id="hdn_id" value="0">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table-responsive pt-2" id="tbviajes">
                    <table class="table table-sm table-bordered table-hover" id="tblasociacion">
                        <thead class="thead-dark">
                            <tr>
                            <th scope="col">N°</th>
                            <th scope="col" >GPS</th>
                            <th scope="col" nowrap>Serie GPS</th>
                            <th scope="col" nowrap>SIM</th>
                            <th scope="col" nowrap>Serie SIM</th>
                            <th scope="col" nowrap>Estado SIM</th>
                            <th scope="col" nowrap>Bodega</th>
                            <th scope="col" nowrap>Estado</th>
                            <th scope="col" >&nbsp;Ubicación&nbsp;</th>
                            <th scope="col" nowrap>Fecha/Hora</th>
                            <th scope="col" nowrap>Velocidad</th>
                            <div id="divsensores"><?=$thsensores?></div>
                            <th scope="col" nowrap></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- <tr><td colspan="13" align="center">Sin registros.</td></tr> -->
                        </tbody>
                    </table>
                </div>
                <div class="col-md-0" id="divcontentmapa">
                
                <div style="position: absolute;right:15px;top:50%;z-index: 500;" id="contentButton">
                <span class="pointer tool" onclick="abrirMapa()" data-toggle="tooltip" data-placement="left" title="Abrir mapa" style="position:fixed;font-size:12pt;color:#54BD3C;"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></span>
                </div>
                <div style="position: fixed;right:5px;top:10%;width: 40%;z-index: 500;" id="contentMap">
                <div style="position:fixed;left:0px;top:0px;width:100%;height:22px;background-color:#54BD3C;padding-left:5px;padding-top:0px;border-top-left-radius:8px;border-top-right-radius:8px;">
                    <span class="pointer tool" onclick="changeMap(1)" data-toggle="tooltip" data-placement="top" title="Mapa híbrido." id="btnchangemap" style="color:white;"><i class="fa fa-map" aria-hidden="true"></i></span>
                    <span class="pointer tool" onclick="getGeocercas(1)" data-toggle="tooltip" data-placement="top" title="Geocercas." id="btngetgeocercas" style="color:white;margin-left:5px;width:8px;"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></span>
                    <span class="pointer" onclick="cerrarMapa()" style="color:white;margin-right:3px;position:absolute;right:3px;top:0px;"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                </div>
                <div id="mapaPopup" style="position:fixed;top:22px;left:0px;border-bottom-left-radius:8px;border-bottom-right-radius:8px;"></div>
                </div>
                </div>
            </div>
            
        </div>
    </div>
    <!-- <div style="width: 40%;position: fixed;">
        <div style="width: 100%;height: 30px;"></div>
        <div style="width: 100%;" id="maps"></div>
    </div> -->
</div>
<script>
let productos = [<?php for($i=0; $i<count($productos); $i++){ echo '{"pro_id":"'.$productos[$i]["pro_id"].'", "pro_codigo":"'.$productos[$i]["pro_codigo"].'", "pro_serie":"'.$productos[$i]["pro_serie"].'", "pro_familia":"'.$productos[$i]["pro_familia"].'", "pro_subfamilia":"'.$productos[$i]["pro_subfamilia"].'", "pro_marca":"'.$productos[$i]["pro_marca"].'", "pro_nombre":"'.$productos[$i]["pro_nombre"].'", "pro_stockminimo":"'.$productos[$i]["pro_stockminimo"].'", "pro_stock":"'.$productos[$i]["pro_stock"].'", "pro_valor":"'.$productos[$i]["pro_valor"].'", "series":"'.$productos[$i]["dataseries"].'"},'; } ?>];
$(document).ready(function(){
    $('#selectproducto').chosen({no_results_text: "Sin resultados, el producto ingresado no existe!",allow_single_deselect: true});
    $('#selectaccesorio').val('');
    $('#selectaccesorio').chosen({no_results_text: "Sin resultados, el accesorio ingresado no existe!",allow_single_deselect: true}).trigger("chosen:updated");;
    $('#selectSeries1').chosen({search_contains: true});
    $('#selectSeries1_chosen').css({'width':'150px !important'})
    $('#selectSeries2').chosen({no_results_text: "Sin resultados, la serie ingresada no existe!",allow_single_deselect: true});
    $('#selectSeries2_chosen').css({'width':'150px !important'});
    $('#sensores').addClass('form-control-sm');
    getAllAsociacion('2021-01-01','<?=hoydate()?>',1);
    let alto = $(window).height() - 150;
    $("#mapaPopup").css({"min-height" : alto+"px"});
    $("#contentButton").css({"min-height" : alto+"px"});
    mapaHome();
    
});

function initSensor(){
    if (document.getElementById('tblasociacion_filter')) {
        $('#tblasociacion_filter').append('<button style="float:left;" class="btn btn-sm btn-success"><i class="fas fa plus"></i></button>');
    } else {
        console.log('Res: ','No Existe tblasociacion_filter');
    }
    
}

var mapahome = document.getElementById('mapaPopup');
var bounds = new google.maps.LatLngBounds();
function mapaHome(){
  latlon = new google.maps.LatLng(-34.9809426, -71.2628082);
  var optMapHome = {
    center: latlon,
    zoom:8,
    mapTypeId: 'hybrid',
    disableDefaultUI:true,
    options: {
      zoomControl:false,
      mapTypeControl:false,
      scaleControl: false,
      streetViewControl: false,
      rotateControl: false,
      fullscreenControl: false,
      gestureHandling: 'greedy'
    }
  }
  gMapHome  = new google.maps.Map(mapahome,optMapHome);
}

function abrirMapa(){
    $('.tool').tooltip('dispose')
$('#contentMap').css({
  'transform':'translate(0px,0)',
  '-webkit-transform':'translate(0px,0)',
  '-o-transform':'translate(0px,0)',
  '-moz-transform':'translate(0px,0)',
  'transition':'all 2s ease-in-out',
  '-webkit-transition':'all 2s ease-in-out',
  '-moz-transition':'all 2s ease-in-out',
  '-o-transition':'all 2s ease-in-out',
});
$("#contentButton").hide('slow');
$('#tbviajes').addClass('col-md-7').removeClass('col-md-12');
$('.tool').tooltip()
}

function cerrarMapa(){
  if(listaMarker.length>0){
    quitarMarcadores(listaMarker);
  }
  
  $('#contentMap').css({
    'transform':'translate(1000px,0)',
    '-webkit-transform':'translate(1000px,0)',
    '-o-transform':'translate(1000px,0)',
    '-moz-transform':'translate(1000px,0)',
    'transition':'all 2s ease-in-out',
    '-webkit-transition':'all 2s ease-in-out',
    '-moz-transition':'all 2s ease-in-out',
    '-o-transition':'all 2s ease-in-out',
  });
  $("#contentButton").show('slow');
  $('#tbviajes').addClass('col-md-12').removeClass('col-md-7');
}

function filtrar(){
    let fdesde = $('#fdesde').val().replace('T','');
    let fhasta = $('#fhasta').val().replace('T','');
    let estado = $('#sensores').val();
    getAllAsociacion(fdesde,fhasta,estado);
}

function cancelarfiltrar(){
    let hoy = '<?=hoydate()?>';
    $('#fdesde, #fhasta').val(hoy);
    $('#sensores').val(1);
}

let listAsoc = [];
function getAllAsociacion(fdesde,fhasta,estado){
    let cantSensores = <?=count($sensores)?>;
    let _Sensores = [<?php for($i=0; $i<count($sensores); $i++){ echo '{"id":"'.$sensores[$i]["id"].'", "nombre":"'.$sensores[$i]["nombre"].'", "fecha":"'.$sensores[$i]["fecha"].'", "estado":"'.$sensores[$i]["estado"].'"},'; } ?>];
    if($.fn.DataTable.isDataTable('#tblasociacion')){
        $('#tblasociacion').DataTable().destroy();
    }

    $("#tblasociacion tbody").html('<tr><td colspan="21" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');

    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'listarAsociacion',fdesde:fdesde,fhasta:fhasta,estado:estado,idgps:_prod1,serie1:_serie,idacc:_prod2,serie2:_serie1,retornar:'no'},function(data){
        if(data!='' && data!=null){
            data = $.parseJSON(data);
            if(data.data.length>0){
                listAsoc = data.data;
                let fila = '';
                $('.tool').tooltip('dispose')
                $('#tblasociacion tbody').html('');
                $.each(data.data,function(i,item){
                    let num = $('#tblasociacion tbody tr').length;

                    let bodega = '';
                    if(parseInt(item.idbodega)==26){
                        bodega = 'Bodega principal';
                    }
                    else{
                        bodega = 'Bodega técnico';
                    }

                    let estado = '';
                    if(parseInt(item.estado)==1){
                        estado = '<span class="badge badge-warning">Espera Transmisión</span>';
                    }
                    else if(parseInt(item.estado)==2){
                        estado = '<span class="badge badge-info">Transmisión OK</span>';
                    }
                    else if(parseInt(item.estado)==3){
                        estado = '<span class="badge badge-primary">Asignado Técnico</span>';
                    }
                    else if(parseInt(item.estado)==4){
                        estado = '<span class="badge badge-success">Instalado</span>';
                    }
                    else if(parseInt(item.estado)==3){
                        estado = '<span class="badge badge-danger">Retirado</span>';
                    }

                    let estadosim = '';
                    if(parseInt(item.estadosim)==1){
                        estadosim = `
                            <div class="custom-control custom-switch" >
                                <input type="checkbox" onclick="estadoSIM(`+item.id+`)" class="custom-control-input" id="switch_`+item.id+`">
                                <label class="custom-control-label" id="lblswitch_`+item.id+`" for="switch_`+item.id+`">OFF</label>
                            </div>`;
                    }
                    else if(parseInt(item.estadosim)==2){
                        estadosim = `
                            <div class="custom-control custom-switch">
                                <input type="checkbox" onclick="estadoSIM(`+item.id+`)" class="custom-control-input" checked id="switch_`+item.id+`">
                                <label class="custom-control-label" id="lblswitch_`+item.id+`" for="switch_`+item.id+`">ON</label>
                            </div>`;
                    }

                    let btn = '<span onclick="verItem('+i+','+item.id+')" class="btn btn-sm btn-circle btn-primary tool" data-toggle="tooltip" data-placement="top" title="Ver detalle asociación"><i class="fa fa-eye"></i></span>';
                    btn += ' <span id="span_'+i+'" onclick="checkearapi('+i+','+item.id+','+item.pro_id+','+item.seriegps+')" class="btn btn-sm btn-circle btn-info tool" data-toggle="tooltip" data-placement="top" title="Checkear sensores"><i class="fa fa-check"></i></span>';
                    btn += ' <span onclick="editarItem('+i+','+item.id+')" class="btn btn-sm btn-circle btn-warning tool" data-toggle="tooltip" data-placement="top" title="Editar asociación"><i class="fa fa-edit"></i></span>';
                    btn += ' <span onclick="borrarItem('+i+','+item.id+')" class="btn btn-sm btn-circle btn-danger tool" data-toggle="tooltip" data-placement="top" title="borrar asociación"><i class="fa fa-trash"></i></span>';

                    let sensores = '';
                    $.each(item.sensores,function(index, sensor){
                        sensores += '<td id="sensor_'+sensor.id+'_'+item.id+'" nowrap style="vertical-align:middle;" align="center">'+(sensor.estado==null?'<span style="cursor:pointer;" class="badge badge-danger">Sin transmisión</span>':sensor.estado==''?'<span style="cursor:pointer;" class="badge badge-danger">Sin transmisión</span>':parseInt(sensor.estado)==1?'<span style="cursor:pointer;" class="badge badge-success">'+sensor.estado1+'</span>':parseInt(sensor.estado)==0?'<span style="cursor:pointer;" class="badge badge-danger">Sin transmisión</span>':'<span style="cursor:pointer;" class="badge badge-danger">'+sensor.estado2+'</span>')+'</td>';
                    });
                    sensores += '<td nowrap style="vertical-align:middle;">'+btn+'</td>';

                    var ubi = '';
                    var vel = '';
                    var fec = '';
                    /*if (item.api.basica!='Sin Datos.') {
                        $.each(item.api.basica,function(ind, val){
                            if(typeof val.item !== undefined){
                                ubi = val.localidad;
                                vel = val.velocidad;
                                fec = val.fechahora;
                            }
                        });
                    }*/
                    fila += '<tr id="fila_'+i+'"><td>'+(i+1)+'</td><td>'+item.gps+'</td><td>'+item.seriegps+'</td><td>'+item.accesorio+'</td><td>'+item.serieaccesorio+'</td><td style="vertical-align:middle;" align="center">'+estadosim+'</td><td>'+bodega+'</td><td align="center" style="vertical-align:middle;" nowrap>'+estado+'</td><td id="ubi_'+i+'">'+ubi+'</td><td id="fec_'+i+'">'+fec+'</td><td id="vel_'+i+'">'+vel+'</td>'+sensores+'</tr>';
                });
                $('#tblasociacion tbody').append(fila);
                $('#tblasociacion').DataTable({
                    "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                    "paging": true,
                    "lengthChange": true,
                    "lengthMenu": [[20,-1], [20,"Todos"]],
                    "pageLength":20,
                    columnDefs:[{
                        targets: "_all",
                        sortable: false
                    }],
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false
                });

                $('.tool').tooltip()
            }
            else{
                $('#tblasociacion').DataTable({
                    "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                    "paging": true,
                    "lengthChange": true,
                    "lengthMenu": [[20,-1], [20,"Todos"]],
                    "pageLength":20,
                    columnDefs:[{
                        targets: "_all",
                        sortable: false
                    }],
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false
                });
            }
        }
        else{
            $('#tblasociacion').DataTable({
                "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                "paging": true,
                "lengthChange": true,
                "lengthMenu": [[20,-1], [20,"Todos"]],
                "pageLength":20,
                columnDefs:[{
                    targets: "_all",
                    sortable: false
                }],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        }

        initSensor();
    });
}

function checkearapi (index,id,idpro,serie){
    $('#span_'+index).attr('disabled', true);
    $('#span_'+index).html('Cargando');
    env = {'idpro':idpro,'serie':serie};
    var send = JSON.stringify(env);
    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'detallevivo',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {

        },error   : function(respuesta) {
                           
        },success : function(respuesta) {
            var ubi = '';
            var vel = '';
            var fec = '';
            if(respuesta){
                if (respuesta.api.basica!='Sin Datos.') {
                    $.each(respuesta.api.basica,function(ind, val){
                        if(typeof val.item !== undefined){
                            ubi = val.localidad;
                            vel = val.velocidad;
                            fec = val.fechahora;
                        }
                    });
                    $('#ubi_'+index).text(ubi);
                    $('#vel_'+index).text(vel);
                    $('#fec_'+index).text(fec);
                }else{
                    toastr.error('No hay comunicación');
                }
                $('#span_'+index).attr('disabled', false);
                $('#span_'+index).html('<i class="fa fa-check"></i>');
                
            }
        }
    });
}

function estadoSIM(id){
    let nestado = 'OFF';
    let active = 1;
    if($('#switch_'+id).prop('checked')){
        nestado = 'ON';
        active = 2;
    }
    $('#lblswitch_'+id).text(nestado);
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'activeSim',id:id,estado:active,retornar:'no'},function(data){
        if(data!='' && data!=null){
            data = $.parseJSON(data);
            if(data.status=='OK'){
                toastr.success('Exito al activar SIM.');
            }
            else{
                $("#switch_"+id).prop("checked", false);
                $('#lblswitch_'+id).text('OFF');
                toastr.error('Error al activar SIM.');
            }
        }
        else{
            $("#switch_"+id).prop("checked", false);
            $('#lblswitch_'+id).text('OFF');
            toastr.error('Error al activar SIM.');
        }
    });
    console.log(id,$('#switch_'+id).prop('checked'));
}

function cambiarEstado(idsensor,estado, idasoc){
    let selected0 = '';
    let selected1 = '';
    let selected2 = '';
    if(estado==null || estado==''){
        selected0 = 'selected';
        selected1 = '';
        selected2 = '';
    }
    else{
        if(estado=='1'){
            selected0 = '';
            selected1 = 'selected';
            selected2 = '';
        }
        else{
            selected0 = '';
            selected1 = '';
            selected2 = 'selected';
        }
        
    }
    let select = '<select id="select_'+idsensor+'_'+idasoc+'" style="width:100px;" onchange="seleccionarEstado('+idsensor+','+estado+', '+idasoc+')" class="form-control formcontrol-sm"><option value="0" '+selected1+'>Sin estado</option><option value="1" '+selected1+'>Encendido</option><option value="2" '+selected2+'>Apagado</option></select>';
    $('#sensor_'+idsensor+'_'+idasoc).html(select);
}

function sinFocus(idsensor,estado, idasoc){
    let selected ='';
    let badge = '';
    if(estado==null || estado=='0' || estado=='2'){
        badge = 'badge-danger';
        selected ='Apagado';
    }
    else{
        badge = 'badge-success';
        selected ='Encendido';
    }
    $('#sensor_'+idsensor+'_'+idasoc).html('<span onclick="cambiarEstado('+idsensor+','+estado+','+idasoc+')" style="cursor:pointer;" class="badge '+badge+'">'+selected+'</span>');
}

function seleccionarEstado(idsensor,estado, idasoc){
    let selected = $('#select_'+idsensor+'_'+idasoc+' option:selected').text();
    let idselect = $('#select_'+idsensor+'_'+idasoc).val();
    let badge = '';
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'setTipoEstado',idsensor:idsensor,idasoc:idasoc,estado:idselect,retornar:'no'},function(data){
        if(data!=null && data!=''){
            data = $.parseJSON(data);
            if(data.status=='OK'){
                let estadoSelect = '';
                if(idselect==null || idselect=='0' || idselect=='2'){
                    badge = 'badge-danger';
                    estadoSelect = 'Apagado';
                }
                else{
                    badge = 'badge-success';
                    estadoSelect = 'Encendido';
                }
                $('#sensor_'+idsensor+'_'+idasoc).html('<span onclick="cambiarEstado('+idsensor+','+estado+','+idasoc+')" style="cursor:pointer;" class="badge '+badge+'">'+estadoSelect+'</span>');
                toastr.success('Estado '+selected+' Actualizado exitosamente.');
            }
            else{
                toastr.error('Error actualizando estado '+selected+'.');
            }
        }
        else{
            toastr.error('Error actualizando estado '+selected+'.');
        }
    });
}

let _prod1 = null;
let _prod2 = null;
let _serie = null;
let _serie1 = null;
let series = [];
let series1 = [];
function getProductosDetails(opcion){
    

    if(parseInt(opcion)==1){
        let producto = $("#selectproducto").val();
        let valor = '';
        let serie = '';
        let dataserie = '';
        $.each(productos, function(i, item){
            if(parseInt(item.pro_id) == parseInt(producto)){
                valor = item.pro_valor;
                serie = item.pro_serie;
                dataserie = item.series;
            }
        });
        if(serie == '1'){
            let ser = '';
            let _series = dataserie.split(',');
            series = _series;
            $.each(_series,function(i,item){
                if(item!='' && item!=null){
                    ser += '<option value="'+item+'">'+item+'</option>';
                }
            });
            $('#selectSeries1').chosen('destroy');
            $('#selectSeries1').html(ser).attr({'disabled':false});
            $('#selectSeries1').val('');
            $('#selectSeries1').chosen({search_contains: true}).trigger("chosen:updated");
            $('#selectSeries1_chosen').css({'width':'150px !important'})
        }
        else{
            $('#selectSeries1').chosen('destroy');
            $('#selectSeries1').html('').attr({'disabled':true});
            $('#selectSeries1').chosen({search_contains: true}).trigger("chosen:updated");
            $('#selectSeries1_chosen').css({'width':'150px !important'})
        }
    }
    else{
        let producto = $("#selectaccesorio").val();
        let valor = '';
        let serie = '';
        let dataserie = '';
        $.each(productos, function(i, item){
            if(parseInt(item.pro_id) == parseInt(producto)){
                valor = item.pro_valor;
                serie = item.pro_serie;
                dataserie = item.series;
            }
        });
        if(serie == '1'){
            let ser = '';
            let _series = dataserie.split(',');
            series1 = _series;
            $.each(_series,function(i,item){
                if(item!='' && item!=null){
                    ser += '<option value="'+item+'">'+item+'</option>';
                }
            });
            $('#selectSeries2').chosen('destroy');
            $('#selectSeries2').html(ser).attr({'disabled':false});
            $('#selectSeries2').val('');
            $('#selectSeries2').chosen({}).trigger("chosen:updated");
            $('#selectSeries2_chosen').css({'width':'150px !important'})
        }
        else{
            $('#selectSeries2').chosen('destroy');
            $('#selectSeries2').html('').attr({'disabled':true});
            $('#selectSeries2').chosen({}).trigger("chosen:updated");
            $('#selectSeries2_chosen').css({'width':'150px !important'})
        }
    }
}

function changeSerie(opcion){
    if(parseInt(opcion)==1){
        let ser = $('#selectSeries1').val();
        _serie = ser;
    }
    else{
        let ser = $('#selectSeries2').val();
        _serie1 = ser;
    }
}

function asociarItems(opt, opcion=0){

    var produ = $('#selectproducto').val();
    var serie = $('#selectSeries1').val();

    /*_prod1 = $('#selectproducto').val();
    _prod2 = $('#selectaccesorio').val();*/
    /*if(_prod1==''){
        toastr.info('Debe seleccionar un GPS.');
        return;
    }

    if(_prod2==''){
        toastr.info('Debe seleccionar un Accesorio.');
        return;
    }

    if(series.length>0){
        if(_serie=='' || _serie==null){
            toastr.info('Debe seleccionar una serie para continuar.');
            return;
        }
    }

    if(series1.length>0){
        if(_serie1=='' || _serie1==null){
            toastr.info('Debe seleccionar una serie para continuar.');
            return;
        }
    }

    let gps = '';
    let acc = '';
    $.each(productos,function(i,item){
        if(parseInt(_prod1)==parseInt(item.pro_id)){
            gps = item.pro_nombre;
        }
        if(parseInt(_prod2)==parseInt(item.pro_id)){
            acc = item.pro_nombre;
        }
    });*/

    //console.log('1','prod: '+_prod1+' acce:'+_prod2+' serie:'+_serie);
    let bodega    = 26;
    let operacion = '';
    let idupdate  = 0;

    if(opcion==0){
        operacion = 'asignarAsociacion';
    }else{
        operacion = 'actualizarAsociacion';
        idupdate  = $('#hdn_id').val();
    }

    if((produ=='' || produ==null || produ==undefined) || (serie=='' || serie==null || serie==undefined)){
        Swal.fire(
            'Error',
            'Debes Ingresar el porducto y la serie',
            'error'
        );
    }else{
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:operacion,idgps:produ,serie1:serie,idacc:_prod2,serie2:'',bodega:bodega,idup:idupdate,retornar:'no'},function(data){
            if(data!='' && data!=null){
                data = $.parseJSON(data);
                if(data.status=='OK'){
                    $('.tool').tooltip('dispose')
                    getAllAsociacion('<?=hoydate()?>','<?=hoydate()?>',1);

                    $('#selectproducto').val('').trigger('chosen:updated');
                    $('#selectaccesorio').val('').trigger('chosen:updated');
                    $('#selectSeries1').html('').trigger('chosen:updated');
                    $('#selectSeries2').html('').trigger('chosen:updated');
                    series = [];
                    series1 = [];
                    _serie = '';
                    _serie1 = '';

                    $('#hdn_id').val('0');
                    $('#btncancelaritem').hide();
                    $('#btnasociaritem').attr('onclick','asociarItems(0)').removeClass('btn-warning').addClass('btn-primary').text('Asociar');
                }
                else{
                    toastr.error('Error al asociar gps y accesorio.');
                }
            }
            else{
                toastr.error('Error al asociar gps y accesorio.');
            }
        })
        .fail(function(error){
            toastr.error(error);
        });
    }

    
}

function borrarItem(index, id){
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'borrarAsociacion',idasoc:id,retornar:'no'},function(data){
        if(data!='' && data!=null){
            data = $.parseJSON(data);
            if(data.status=='OK'){
                getAllAsociacion('<?=hoydate()?>','<?=hoydate()?>',1);
                toastr.success('Asociación borrada con exito');
            }
            else{
                toastr.error('Error al asociar gps y accesorio.');
            }
        }
        else{
            toastr.error('Error al asociar gps y accesorio.');
        }
    });
}

function verItem(index, id){
    let table = '<table class="table table-sm table-bordered">';
    table += '<thead class="thead-dark">';
    table += '<th scope="col">Tipo</th>';
    table += '<th scope="col">Estado</th>';
    table += '</thead>';
    table += '<tbody>';
    table += '<tr>';
    table += '<td scope="row">Odometro</td><td></td>';
    table += '</tr>';
    table += '<tr>';
    table += '<td scope="row">Litros</td><td></td>';
    table += '</tr>';
    table += '<tr>';
    table += '<td scope="row">Velocidad</td><td></td>';
    table += '</tr>';
    table += '<tr>';
    table += '<td scope="row">Pedal Aceleración</td><td></td>';
    table += '</tr>';
    table += '<tr>';
    table += '<td scope="row">RPM</td><td></td>';
    table += '</tr>';
    table += '<tr>';
    table += '<td scope="row">Nivel Estanque</td><td></td>';
    table += '</tr>';
    table += '<tr>';
    table += '<td scope="row">T° Motor</td><td></td>';
    table += '</tr>';
    table += '<tr>';
    table += '<td scope="row">Torque</td><td></td>';
    table += '</tr>';
    table += '<tr>';
    table += '<td scope="row">AD Blue</td><td></td>';
    table += '</tr>';
    table += '<tr>';
    table += '<td scope="row">Peso Eje</td><td></td>';
    table += '</tr>';
    table += '</tbody>';
    table += '</table>';
    $('#malert .modal-header').css({'background-color':'#338AFF','color':'white'});
    $('#malert .modal-title').text('Info CAN');
    $('#malert .modal-body').html(table);
    $('#malert .modal-footer').html("<button type='button' class='btn btn-primary btn-rounded' data-dismiss='modal'>Aceptar</button>");
    $('#malert').modal('show');
}

function resetForm(){
    $('#hdn_id').val('0');
    $('#btncancelaritem').hide();
    $('#btnasociaritem').attr('onclick','asociarItems(0)').removeClass('btn-warning').addClass('btn-primary').text('Asociar');
    $('#selectproducto').val('').trigger('chosen:updated');
    $('#selectaccesorio').val('').trigger('chosen:updated');
    $('#selectSeries1').html('').chosen({search_contains: true}).trigger("chosen:updated");
    $('#selectSeries2').html('').chosen({no_results_text: "Sin resultados, la serie ingresada no existe!",allow_single_deselect: true}).trigger("chosen:updated");
}

function editarItem(index, id){
    $('#hdn_id').val(id);
    let idprod1 = listAsoc[index].idgps;
    let idprod2 = listAsoc[index].idaccesorio;
    let series1 = listAsoc[index].seriegps;
    let series2 = listAsoc[index].serieaccesorio;

    if(series1!=null && series1!=''){
        let _serie = [];
        let datas = '';
        $.each(productos, function(i, item){
            if(parseInt(item.pro_id) == parseInt(idprod1)){
                datas = item.series;
            }
        });
        if(datas!=''){
            _serie = datas.split(',');
            let option = '';
            $.each(_serie,function(i,item){
                let selec = '';
                if(series1==item){selec = 'selected';_serie=item;}
                option += '<option value="'+item+'" '+selec+'>'+item+'</option>';
            });
            $('#selectSeries1').html(option).chosen('destroy').chosen({search_contains: true}).trigger("chosen:updated");
        }
    }

    if(series2!=null && series2!=''){
        let _serie = [];
        let datas = '';
        $.each(productos, function(i, item){
            if(parseInt(item.pro_id) == parseInt(idprod2)){
                datas = item.series;
            }
        });
        if(datas!=''){
            _serie = datas.split(',');
            let option = '';
            $.each(_serie,function(i,item){
                let selec = '';
                if(series1==item){selec = 'selected';_serie1=item;}
                option += '<option value="'+item+'" '+selec+'>'+item+'</option>';
            });
            $('#selectSeries2').html(option).chosen('destroy').chosen({no_results_text: "Sin resultados, la serie ingresada no existe!",allow_single_deselect: true}).trigger("chosen:updated");
        }
    }
    
    $('#btncancelaritem').show();
    $('#btnasociaritem').attr('onclick','asociarItems(0,1)').removeClass('btn-primary').addClass('btn-warning').text('Actualizar');
    $('#selectproducto').val(idprod1).trigger('chosen:updated');
    $('#selectaccesorio').val(idprod2).trigger('chosen:updated');
}

function getChangeEstado(){
    // let estado = $('#sensores option:selected').text();
    // $('#tdestado').text(estado);
}

var marker = null;
window.listaMarker = [];
window.listaPopup = [];

function setLocationMap(vv, id, patente){
  quitarMarcadores(listaMarker);
  quitarMarcadores(listaPopup);
  var i;
  sentido = parseInt(vv.sentido);
  console.log(sentido);
  if(sentido  >=0 && sentido <=90 ){
  imgMarker='img/camion90.png';	
  }else if(sentido >= 91 && sentido <= 180){
  imgMarker='img/camion180.png';		
  }else if(sentido >= 181 && sentido <= 270){
  imgMarker='img/camion270.png';
  }else if(sentido >= 271 && sentido <= 360){
  imgMarker='img/camion360.png';	
  }else{
  imgMarker='img/camion360.png';
  }

  var popup, Popup;
  marker = new google.maps.Marker({
  position: new google.maps.LatLng(vv["latitud"], vv["longitud"]),
  icon:imgMarker,
  map: gMapHome
  });

  listaMarker.push(marker);
      
  $("#contenpop").append("<div id='"+id+"'>"+patente+"</div>");
  Popup = createPopupClass();
  popup = new Popup(
  new google.maps.LatLng(vv["latitud"], vv["longitud"]), document.getElementById(id));
  popup.setMap(gMapHome);
  listaPopup.push(popup);	 

  gMapHome.setCenter(new google.maps.LatLng(vv["latitud"], vv["longitud"]));
  gMapHome.setZoom(15);

  abrirMapa();
}

window.locaciones;
window.poligonos={};
window.polygons = [];
function getLocaciones(){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getLocaciones',retornar:'no'},function(data){
locaciones = $.parseJSON(data);
color= "#85bc25";
if(Object.keys(locaciones).length > 0){
$.each(locaciones,function(il,vl){
if(Object.keys(vl.coordenadas).length > 0){
switch(parseInt(vl.tdl_id)){
case 1:
color= "#85bc25";// plantas
break;
case 4:
color="#ea185e";// terminal portuario
break;
case 7:
color="#ffbc34";// depositos
break;
case 10:
color="#1676d2";// puerto embarque
break;
}
cuadrante=[];
$.each(vl.coordenadas, function(ic,vc){
cuadrante.push({lat:parseFloat(vc.latitud),lng:parseFloat(vc.longitud)});
});
poligonos[vl.pun_nombre]={"cuadrante":cuadrante,"color":color,"tipo":vl.tdl_nombre,'nombre':vl.pun_nombre};
}
});
}
dibujarPoligonos();
$('#btngetgeocercas').html('<i class="fa fa-map-marker" aria-hidden="true"></i><span class="tooltiptext">Geocercas.</span>');
});
}

function dibujarPoligonos(){
  $.each(poligonos, function(ip,vp){
    var locaciones = new google.maps.Polygon({
      paths: vp["cuadrante"],
      strokeColor: vp["color"],
      strokeOpacity: 1,
      strokeWeight: 3,
      fillColor: vp["color"]
    });
    locaciones.setMap(gMapHome);
    polygons.push(locaciones);
    var infoWindow = new google.maps.InfoWindow();
    google.maps.event.addListener(locaciones, 'mouseover', function(e) {
        infoWindow.setPosition(vp["cuadrante"][0]);
        infoWindow.setContent(vp.nombre);
        infoWindow.open(gMapHome);
    });
    google.maps.event.addListener(locaciones, 'mouseout', function() {
        infoWindow.close();
    });
  });
}

function quitarMarcadores(lista){for (i in lista){lista[i].setMap(null);}}

function createPopupClass() {
function Popup(position, content) {
this.position = position;
content.classList.add('popup-bubble');

// This zero-height div is positioned at the bottom of the bubble.
var bubbleAnchor = document.createElement('div');
bubbleAnchor.classList.add('popup-bubble-anchor');
bubbleAnchor.appendChild(content);

// This zero-height div is positioned at the bottom of the tip.
this.containerDiv = document.createElement('div');
this.containerDiv.classList.add('popup-container');
this.containerDiv.appendChild(bubbleAnchor);

// Optionally stop clicks, etc., from bubbling up to the map.
google.maps.OverlayView.preventMapHitsAndGesturesFrom(this.containerDiv);
}
// ES5 magic to extend google.maps.OverlayView.
Popup.prototype = Object.create(google.maps.OverlayView.prototype);

/** Called when the popup is added to the map. */
Popup.prototype.onAdd = function() {
this.getPanes().floatPane.appendChild(this.containerDiv);
};

/** Called when the popup is removed from the map. */
Popup.prototype.onRemove = function() {
if (this.containerDiv.parentElement) {
this.containerDiv.parentElement.removeChild(this.containerDiv);
}
};

/** Called each frame when the popup needs to draw itself. */
Popup.prototype.draw = function() {
var divPosition = this.getProjection().fromLatLngToDivPixel(this.position);

// Hide the popup when it is far out of view.
var display =
Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000 ?
'block' :
'none';

if (display === 'block') {
this.containerDiv.style.left = divPosition.x + 'px';
this.containerDiv.style.top = divPosition.y + 'px';
}
if (this.containerDiv.style.display !== display) {
this.containerDiv.style.display = display;
}
};
return Popup;
}

function pruebaapi (imei){
    var env  = {'imei':imei};
    var send = JSON.stringify(env);
    $.ajax({
        url     : 'operaciones.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'pruebaapi',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {

        },error   : function(respuesta) {
            console.log(respuesta);
        },success : function(respuesta) {
            console.log(respuesta);
        }
    });
}

</script>