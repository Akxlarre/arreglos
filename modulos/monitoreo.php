
<!-- modal -->
<div class="modal" id="mlistveh">
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

<div class="content">
<div class="row top20" id="tb_listadoveh">
<div class="col-md-12">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Listado de Vehículos</h3>
</div>
<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <button type="button" class="btn btn-success btn-rounded"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Generar Excel</button>
        </div>
        <div class="col-md-12 table-responsive" style="margin-top: 10px;">
            <table class="table table-bordered table-striped table-condensed" id="tabveh">
            <thead>
            <tr><td colspan=18></td><td colspan=3 class="text-center">TRANSMISIONES</td><td class="text-center">TEMP.</td><td colspan=2 class="text-center">CANBUS</td></tr>
            <tr>
            <td>N°</td>
            <td>Cliente</td>
            <td>Patente</td>
            <td>Frecuencia 1 min.</td>
            <td>U. Versión</td>
            <td>Trama 3</td>
            <td>Sensores</td>
            <td>Alerta accidente</td>
            <td>Jamming</td>
            <td>Par Motor</td>
            <td>A. Trayectos</td>
            <td>Geocercas</td>
            <td>WS</td>
            <td>Ultima Posición</td>
            <td>Localidad</td>
            <td>Grupo</td>
            <td>Estado</td>
            <td>Fecha filtro</td>
            <td class="text-center">2</td>
            <td class="text-center">5</td>
            <td class="text-center">10</td>
            <td class="text-center">Tº</td>
            <td class="text-center">Odometro</td>
            <td class="text-center">Litros</td>
            </tr>
            </thead>
            <tbody>
            <tr><td  class="text-center" colspan=17>
            <span class='text-blue'><h4>Procesando información ... <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></h4></span>
            </td></tr>
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
window.userid=$("#usuarioidjs").val();
$(function(){
getTabVehiculos();
});
window.vehiculos;
function getTabVehiculos(){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabVehiculosMonitor',retornar:'no'},function(data){
console.log(data);
datos = $.parseJSON(data);
vehiculos=datos;
filas="";
x=0;
$.each(datos,function(index,valor){
x++;
/*observaciones = vehiculos[index]["observaciones"];
if(Object.keys(observaciones).length > 0){
//alerta="<span class='heartbit pointer' onclick='agregarOBS(\""+index+"\")'></span>";
}else{
//alerta="";
}*/
if(valor["transmisiones"]["ultima"]!='0000-00-00 00:00:00'){
if(parseInt(valor["transmisiones"]["48"]) > 24 ){
h48="<span class='btnh btnh-danger pointer'>"+valor["transmisiones"]["48"]+"</span>";	
h24="";
h12="";
h2="";
}else if(parseInt(valor["transmisiones"]["24"]) > 12 && valor["transmisiones"]["48"]=="--"){
h24="<span class='btnh btnh-danger pointer'>"+valor["transmisiones"]["24"]+"</span>";	
h48="";
h12="";
h2="";
}else if(parseInt(valor["transmisiones"]["12"]) >= 2 && valor["transmisiones"]["24"]=="--"){
h12="<span class='btnh btnh-danger pointer'>"+valor["transmisiones"]["12"]+"</span>";	
h24="";
h48="";
h2="";	
}else if(parseInt(valor["transmisiones"]["2"]) > 0 && valor["transmisiones"]["12"]=="--"){
h2="<span class='btnh btnh-danger pointer'>"+valor["transmisiones"]["2"]+"</span>";
h24="";
h12="";
h48="";		
}else if(parseInt(valor["transmisiones"]["2"]) == 0){
h2="<span class='btnh btnh-success pointer'><i class='fa fa-check' aria-hidden='true'></i></span>";
h24="";
h12="";
h48="";
}else{

h2="<span class='btnh btnh-warning pointer'><i class='fa fa-exclamation' aria-hidden='true'></i></span>";	
h24="";
h12="";
h48="";
}	
}else{
h24="";
h12="";
h48="";	
h2="<span class='btnh btnh-warning pointer'><i class='fa fa-exclamation' aria-hidden='true'></i></span>";
}

let dias2 = '';
let dias5 = '';
let dias10 = '';

if(parseInt(valor["transmisiones"]["dias2"]) != 0){
    dias2 = "<span class='btnh btnh-success pointer'>"+valor["transmisiones"]["dias2"]+"</span>";
    dias5 = '';
    dias10 = '';
}
if(parseInt(valor["transmisiones"]["dias5"]) != 0){
    dias2 = '';
    dias5 = "<span class='btnh btnh-warning pointer'>"+valor["transmisiones"]["dias5"]+"</span>";
    dias10 = '';
}
if(parseInt(valor["transmisiones"]["dias10"]) != 0){
    dias2 = '';
    dias5 = '';
    dias10 = "<span class='btnh btnh-danger pointer'>"+valor["transmisiones"]["dias10"]+"</span>";
}

/*
if(valor["transmisiones"]["2"]=="--" && valor["transmisiones"]["12"]=="--" && valor["transmisiones"]["24"]=="--" && valor["transmisiones"]["48"]=="--"){
h2="<span class='btnh btnh-warning'><i class='fa fa-exclamation' aria-hidden='true'></i></span>";	
}else if(parseInt(valor["transmisiones"]["2"]) == 0){
h2="<span class='btnh btnh-success'><i class='fa fa-check' aria-hidden='true'></i></span>";	
}
else{
h2="<span class='btnh btnh-danger'>"+valor["transmisiones"]["2"]+"</span>";	
}

if(parseInt(valor["transmisiones"]["12"]) > 0){
h12="<span class='btnh btnh-danger'>"+valor["transmisiones"]["12"]+"</span>";
}else {
h12="";
h2="";	
}
*/
let fechaObs = '';
if(valor["transmisiones"]["fechaobservacion"]===null || valor["transmisiones"]["fechaobservacion"]==='null' || valor["transmisiones"]["fechaobservacion"]===undefined || valor["transmisiones"]["fechaobservacion"]===''){
    fechaObs = 'Sin registro';
}else{
    let ano = valor["transmisiones"]["fechaobservacion"].split(' ')[0].replaceAll('/','-').split('-')[0];
    let mes = valor["transmisiones"]["fechaobservacion"].split(' ')[0].replaceAll('/','-').split('-')[1];
    let dia = valor["transmisiones"]["fechaobservacion"].split(' ')[0].replaceAll('/','-').split('-')[2]

    fechaObs = dia+'-'+mes+'-'+ano+' '+valor["transmisiones"]["fechaobservacion"].split(' ')[1];
}

filas+="<tr id='veh"+index+"'><td width=30>"+x+"</td><td>"+valor.cuenta+"</td><td nowrap>"+valor.patente.trim()+"</td><td id='frecuencia_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color1']+"' onclick='selectOption("+index+",\"frecuencia\");'>"+valor["transmisiones"]['frecuencia']+"</span></td><td id='ultimaversion_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color2']+"' onclick='selectOption("+index+",\"ultimaversion\");'>"+valor["transmisiones"]['ultimaversion']+"</span></td><td id='trama3_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color3']+"' onclick='selectOption("+index+",\"trama3\");'>"+valor["transmisiones"]['trama3']+"</span></td><td id='sensores_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color4']+"' onclick='selectOption("+index+",\"sensores\");'>"+valor["transmisiones"]['sensores']+"</span></td><td id='alertaaccidente_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color9']+"' onclick='selectOption("+index+",\"alertaaccidente\");'>"+valor["transmisiones"]['alertaaccidente']+"</span></td><td id='jamming_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color10']+"' onclick='selectOption("+index+",\"jamming\");'>"+valor["transmisiones"]['jamming']+"</span></td><td id='parmotor_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color5']+"' onclick='selectOption("+index+",\"parmotor\");'>"+valor["transmisiones"]['parmotor']+"</span></td><td id='atrayectos_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color6']+"' onclick='selectOption("+index+",\"atrayectos\");'>"+valor["transmisiones"]['atrayectos']+"</span></td><td id='geocercas_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color7']+"' onclick='selectOption("+index+",\"geocercas\");'>"+valor["transmisiones"]['geocercas']+"</span></td><td id='ws_"+index+"'><span class='pointer' style='"+valor['transmisiones']['color8']+"' onclick='selectOption("+index+",\"ws\");'>"+valor["transmisiones"]['ws']+"</span></td><td>"+valor["transmisiones"]["ultima"]+"</td><td>"+valor["transmisiones"]["localidad"]+"</td><td>"+valor.grupo+"</td><td id='vehestado"+index+"'><span class='pointer' onclick='selectEstado(\""+index+"\");'>"+valor.observacion+"</span></td><td id='vehfecha"+index+"'>"+fechaObs+"</td><td align=center>"+dias2+"</td><td align=center>"+dias5+"</td><td align=center>"+dias10+"</td><td></td><td></td><td></td></tr>";
});
$("#tabveh tbody").html(filas);
$('#tabveh').DataTable({
"language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
"paging": true,
"order": [[9, "desc" ],[ 8, "desc" ],[7, "desc" ]],
"lengthChange": true,
"lengthMenu": [[20,-1], [20,"Todos"]],
"pageLength":100,
"searching": true,
"ordering": true,
"info": true,
// "columns": [
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     { "width": "25%" },
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//     null,
//   ]
});
});
}
function selectEstado(index){

select="<select id='observacion"+index+"' class='form-control' onchange='selectobs(\""+index+"\")'>";
select+="<option value='0'>SELECCIONAR</option>";
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getSelectODV',retornar:'no'},function(data){
datos = $.parseJSON(data);
$.each(datos,function(index,valor){
select+="<option value='"+valor.id+"'>"+valor.observacion+"</option>";
});
select+="</select>";
$("#vehestado"+index+"").html(select);
});
}
function selectobs(index){
idveh=vehiculos[index]["idveh"];
id=$("#observacion"+index+"").val();
obs=$("#observacion"+index+" option:selected").text();
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'ActualizaObservacion',veh_id:idveh,odv_id:id,retornar:'no'},function(data){
console.log(data);
nestado = "<span class='pointer' onclick='selectEstado(\""+index+"\");'>"+obs+"</span>";
$("#vehestado"+index+"").html(nestado);
let date = new Date()
let day = date.getDate()
let month = date.getMonth() + 1
let year = date.getFullYear()
let hour = date.getHours()
let minute = date.getMinutes()
let second = date.getSeconds()
$("#vehfecha"+index).text(day+'-'+month+'-'+year+' '+hour+':'+minute+':'+second);
});
}

let ultimoOption = '';
let ultimoIndex = '';
function selectOption(index,option){
    let select="<select id='select_"+option+"_"+index+"' class='form-control' onchange='selectOptionIndex("+index+",\""+option+"\")'>";
    select+="<option value='0'>NA</option>";
    select+="<option value='1'>SI</option>";
    select+="<option value='2'>NO</option>";
    select+="</select>";
    $('#'+option+'_'+index).html(select);

    if(ultimoOption===''){
        ultimoOption = option;
        ultimoIndex = index;
    }
    else{
        let idselect = '#select_'+ultimoOption+'_'+ultimoIndex;
        let options = $(idselect).val();
        let style = '';
        if(options==="0"){
            style = 'style="background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold"';
        }
        else if(options==="1"){
            style = 'style="background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold"';
        }
        else if(options==="2"){
            style = 'style="background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold"';
        }
        $('#'+ultimoOption+'_'+ultimoIndex).html("<span class='pointer' "+style+" onclick='selectOption("+ultimoIndex+",\""+ultimoOption+"\");'>"+$('#select_'+ultimoOption+'_'+ultimoIndex+' option:selected').text()+"</span>");
        ultimoOption = option;
        ultimoIndex = index;
    }
}

// function resetSelect(){
//     let idselect = '#select_'+ultimoOption+'_'+ultimoIndex;
//     let options = $(idselect).val();
//     let style = '';
//     if(options==="0"){
//         style = 'style="background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold"';
//     }
//     else if(options==="1"){
//         style = 'style="background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold"';
//     }
//     else if(options==="2"){
//         style = 'style="background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold"';
//     }
//     let name = $('#select_'+ultimoOption+'_'+ultimoIndex+' option:selected').text();
//     $('#'+ultimoOption+'_'+ultimoIndex).html("<span class='pointer' "+style+" onclick='selectOption("+ultimoIndex+",\""+ultimoOption+"\");'>"+name+"</span>");
// }

function selectOptionIndex(index,option){
    let idselect = '#select_'+option+'_'+index;
    let options = $(idselect).val();
    let veh_id = vehiculos[index]["idveh"];
    let dataSql = '';
    if(option==='frecuencia'){
        dataSql = 'veh_frecuencia1=\''+options+"\'";
    }
    else if(option==='ultimaversion'){
        dataSql = 'veh_ultimaversion=\''+options+"\'";
    }
    else if(option==='trama3'){
        dataSql = 'veh_trama3=\''+options+"\'";
    }
    else if(option==='sensores'){
        dataSql = 'veh_sensores=\''+options+"\'";
    }
    else if(option==='alertaaccidente'){
        dataSql = 'veh_alertaaccidente=\''+options+"\'";
    }
    else if(option==='jamming'){
        dataSql = 'veh_jamming=\''+options+"\'";
    }
    else if(option==='parmotor'){
        dataSql = 'veh_parmotor=\''+options+"\'";
    }
    else if(option==='atrayectos'){
        dataSql = 'veh_atrayectos=\''+options+"\'";
    }
    else if(option==='geocercas'){
        dataSql = 'veh_geocercas=\''+options+"\'";
    }
    else if(option==='ws'){
        dataSql = 'veh_ws=\''+options+"\'";
    }
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'actualizarOption',veh_id:veh_id,dataSql:dataSql,retornar:'no'},function(data){
        if(data!=='' && data!== null){
            data = $.parseJSON(data);
            if(data.status==='OK'){
                toastr.success(option.toUpperCase()+' actualizado exitosamente.');
            }
            else{
                toastr.error('Error al actualizar '+option.toUpperCase());
            }
        }
    });
}

// funcion para agregar observaciones al vehiculo
function agregarOBS(index){
observaciones = vehiculos[index]["observaciones"];
id = vehiculos[index]["idveh"];
console.log(Object.keys(observaciones).length);
// si tiene observaciones  
fobs="<div class='col-md-10'>";
if(Object.keys(observaciones).length > 0){
$.each(observaciones, function(index2,valor2){
console.log(valor2);
fobs+="<div class='col-sm-12'><div class='direct-chat-msg'><div class='direct-chat-info clearfix'><span class='direct-chat-name pull-left'>"+valor2.usuario+"</span><span class='direct-chat-timestamp pull-right'>"+valor2.fecha+"</span></div><img class='direct-chat-img' src='img/"+valor2.foto+"' alt='Message User Image'><div class='direct-chat-text'>"+valor2.observacion+"</div></div></div>";
});	
}

fobs+="<div class='col-sm-12'><div class='form-group'><label class='col-sm-12 label-control'>Observación</label><div class='col-sm-12'><textarea class='form-control rznone' name='obs"+index+"' rows='5'></textarea></div></div><div class='col-sm-12 top10 text-green'><span class='oculto' id='enviandoobs'><i class='fa fa-spinner fa-spin fa-lg fa-fw'></i></span><button type='button' id='btnobs' class='btn btn-success btn-rounded ' onclick='registrarObservacion(\""+index+"\",\""+id+"\")'>Agregar Observación</button></div></div>";
fobs+="</div>";

$("#mlistveh .modal-dialog").css({'width':'50%'});
$("#mlistveh .modal-header").removeClass("header-rojo").addClass("header-verde");
$("#mlistveh .modal-title").html("Observaciones");
$("#mlistveh .modal-body").html(fobs);
$("#mlistcon .modal-footer").css({"display":"none"});
$("#mlistveh").modal("toggle");
}

function registrarObservacion(index,veh_id){
//console.log(userid);
obs=$("textarea[name='obs"+index+"']").val();
$("#enviandoobs").show();
$("#btnobs").attr("disabled",true);
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'postObservacion',vehiculo:veh_id,usuario:''+userid+'',observacion:''+obs+'',retornar:'no'},function(data){
$("textarea[name='obs"+index+"']").val("");
$("#enviandoobs").hide();
$(btnobs).attr("disabled",false);
});
}



</script>
