<style>
    .na{
        background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold
    }
    .si{
        background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold
    }
    .no{
        background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold
    }
    .backtitle{
        background-color: #2f3d4a;color:white;border:1px solid white;border-radius:8px 8px 0px 0px;
    }
</style>
<!-- modal -->
<div class="modal" id="mticket">
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
    <div class="row top20">
        <div class="col-md-12">
            <ul class="nav nav-pills">
                <li role="presentation" class="active"><a class="pointer" onclick="changeViews(1)" id="btnchangeview"><i class="fa fa-user" aria-hidden="true"></i> Contacto clientes</a></li>
            </ul>
        </div>
        <div class="col-md-12 table-responsive oculto" id="view_contactoclientes">
        <table class="table table-bordered table-striped table-condensed" id="tbgcontactoclientes"> 
            <thead>
            <tr><td colspan="2"></td><td colspan="3" align="center" class="backtitle">CONTACTO 1</td><td colspan="3" align="center" class="backtitle">CONTACTO 2</td><td colspan="3" align="center" class="backtitle">CONTACTO 3</td><td colspan="3" align="center" class="backtitle">CONTACTO 4</td><td colspan="3" align="center" class="backtitle">CONTACTO 5</td><td colspan="3" align="center" class="backtitle">CONTACTO 6</td><td colspan="3" align="center" class="backtitle">CONTACTO 7</td><td colspan="3" align="center" class="backtitle">CONTACTO 8</td><td colspan="3" align="center" class="backtitle">CONTACTO 9</td><td colspan="3" align="center" class="backtitle">CONTACTO 10</td><td colspan="3" align="center" class="backtitle">CONTACTO 11</td><td colspan="3" align="center" class="backtitle">CONTACTO 12</td><td colspan="3" align="center" class="backtitle">CONTACTO 13</td><td colspan="3" align="center" class="backtitle">CONTACTO 14</td><td colspan="3" align="center" class="backtitle">CONTACTO 15</td><td colspan="3" align="center" class="backtitle">CONTACTO 16</td><td colspan="3" align="center" class="backtitle">CONTACTO 17</td><td colspan="3" align="center" class="backtitle">CONTACTO 18</td><td colspan="3" align="center" class="backtitle">CONTACTO 19</td><td colspan="3" align="center" class="backtitle">CONTACTO 20</td></tr>
            <tr style="background-color: white;">
                <!-- implementacion inicial -->
                <th nowrap>Cliente</th>
                <th>Cant. Vehículos</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
                <th>Motivo</th>
                <th nowrap>Fecha / Hora</th>
                <th>Contador</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="62" style="color: #2D96F2;padding-left: 20px;"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> Cargando Datos...</td>
                </tr>
            </tbody>
            </table>
        </div>
        <div class="col-md-12 table-responsive" style="margin-top: 10px;" id="view_confclientes">
            <table class="table table-bordered table-striped" id="tbgclientes"> 
            <thead>
            <tr><td colspan="2"></td><td colspan="10" align="center" style="background-color: #2f3d4a;color:white;border:1px solid white;border-radius:8px 8px 0px 0px;">IMPLEMENTACIÓN INICIAL</td><td colspan="8" align="center" style="background-color: #2f3d4a;color:white;border:1px solid white;border-radius:8px 8px 0px 0px;">LOGÍSTICA</td><td colspan="7" align="center" style="background-color: #2f3d4a;color:white;border:1px solid white;border-radius:8px 8px 0px 0px;">SEGURIDAD</td><td colspan="8" align="center" style="background-color: #2f3d4a;color:white;border:1px solid white;border-radius:8px 8px 0px 0px;">GESTIÓN COMBUSTIBLE/CONDUCCIÓN</td><td colspan="5" align="center" style="background-color: #2f3d4a;color:white;border:1px solid white;border-radius:8px 8px 0px 0px;">MANTENIMIENTO Y DOCUMENTOS</td><td colspan="9" align="center" style="background-color: #2f3d4a;color:white;border:1px solid white;border-radius:8px 8px 0px 0px;">ALARMAS</td></tr>
            <tr style="background-color: white;">
                <!-- implementacion inicial -->
                <th>Cliente</th>
                <th>Cant. Vehículos</th>
                <th>Alta Flota</th>
                <th>Instalación</th>
                <th>Conf. Equipo</th>
                <th>Conf. Par Motor</th>
                <th>Analisis Trayecto</th>
                <th>Creación Usuarios</th>
                <th>Carga Datos Maestros</th>
                <th>Otras Configuraciones</th>
                <th>Integracion Terceros</th>
                <th>Correo Bienvenida</th>
                <!-- Logistica -->
                <th>Cartografía e interpretación</th>
                <th>Asignación Vistas y Favoritos</th>
                <th>Rutas por Tramos</th>
                <th>Dashboard Logística</th>
                <th>Activar Sensores</th>
                <th>Estadías Geocercas</th>
                <th>Trayectos</th>
                <th>Cálculo Flotas</th>
                <!-- sEGURIDAD -->
                <th>Velocidades</th>
                <th>Velocidad por Geocerca</th>
                <th>Alerta Velocidad</th>
                <th>Botón_panico y Corta corriente</th>
                <th>Reconstrucción de accidentes</th>
                <th>Somnolencia</th>
                <th>Vehiculo Permitido</th>
                <!-- GESTIÓN COMBUSTIBLE/CONDUCCIÓN -->
                <th>Gráfico nivel estanque</th>
                <th>Informe de Consumo</th>
                <th>Ralentí o Ralentí vs pto</th>
                <th>Gráfico de parámetros</th>
                <th>Analisis de trayectos</th>
                <th>Informe par Motor</th>
                <th>Calificación Condcutor</th>
                <th>Dashboard telemetría</th>
                <!-- MANTENIMIENTO Y DOCUMENTACIONES -->
                <th>Mantenimiento preventivo</th>
                <th>Mantenimiento correctivos</th>
                <th>Fichas técnicas</th>
                <th>Vencimiento documentos(revición técnico, licencias)</th>
                <th>Reporte control de costos</th>
                <!-- alarmas -->
                <th>Alerta Excesos velocidad</th>
                <th>Alertas velocidad geocerca</th>
                <th>Alerta uso no permitido</th>
                <th>Alerta Descenso Estanque</th>
                <th>Alerta ralentí</th>
                <th>Alerta Bajo voltaje batería</th>
                <th>Alerta Exceso temperatura motor</th>
                <th>Alerta Vencimiento mantenimiento y/o Revisión</th>
                <th>Alerta Vencimiento documentos</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="47" style="color: #2D96F2;padding-left: 20px;"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> Cargando Datos...</td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    getDataGeneral();
});

let dataClientes = [];
function getDataGeneral(){
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getDataGeneral',retornar:'no'},function(data){
        if(data!=='' && data!==null && data!=='null'){
            data = $.parseJSON(data);
            dataClientes = data;
            let td = '';
            $.each(data, function(i, item){
                let select = '';
                $.each(item.datacliente, function(index, valor){
                    if(index>=2 && valor.ncampo!=='gc_updatereg' && valor.ncampo!=='gc_estado'){
                        let clas = '';
                        let text = '';
                        if(valor.valor===null || valor.valor==='0'){
                            clas = 'na';
                            text = 'NA';
                        }
                        else if(valor.valor===1 || valor.valor==='1'){
                            clas = 'si';
                            text = 'SI';
                        }
                        else if(valor.valor===2 || valor.valor==='2'){
                            clas = 'no';
                            text = 'NO';
                        }
                        select += '<td id="'+valor.ncampo+'_'+i+'"><span class="pointer '+clas+'" onclick="selectOption('+i+',\''+valor.ncampo+'\');">'+text+'</span></td>';
                    }
                });
                td += '<tr id="fila_'+i+'"><td onclick="marcarFila('+i+')">'+item.cuenta+'</td><td>'+item.cantvehiculos+'</td>'+select+'<!--<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>--></tr>';
            });
            $('#tbgclientes tbody').html(td);
            $('#tbgclientes').DataTable({
                "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                "paging": true,
                "order": [[1, "desc" ]],
                "lengthChange": true,
                "lengthMenu": [[20,-1], [20,"Todos"]],
                "pageLength":100,
                "searching": true,
                "ordering": true,
                "info": true
            });
        }
    });
}

let ultimoOption = '';
let ultimoIndex = '';
function selectOption(index,option){
    let select="<select id='select_"+option+"_"+index+"' style='width:70px;' class='form-control' onchange='selectOptionIndex("+index+",\""+option+"\")'>";
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
            style = 'na';
        }
        else if(options==="1"){
            style = 'si';
        }
        else if(options==="2"){
            style = 'no';
        }
        $('#'+ultimoOption+'_'+ultimoIndex).html("<span class='pointer "+style+"'  onclick='selectOption("+ultimoIndex+",\""+ultimoOption+"\");'>"+$('#select_'+ultimoOption+'_'+ultimoIndex+' option:selected').text()+"</span>");
        ultimoOption = option;
        ultimoIndex = index;
    }
}

function selectOptionIndex(index,option){
    let idselect = '#select_'+option+'_'+index;
    let options = $(idselect).val();
    let cuenta = dataClientes[index].cuenta;

    let dataSql = option+'='+options;
    
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'actualizarOptionGestionCliente',cuenta:cuenta,dataSql:dataSql,retornar:'no'},function(data){
        if(data!=='' && data!== null){
            data = $.parseJSON(data);
            if(data.status==='OK'){
                toastr.success('Dato actualizado exitosamente.');
            }
            else{
                toastr.error('Error al actualizar Dato.');
            }
        }
    });
}

let indexAnterior = null;
function marcarFila(index){
    //let color = $('#fila_'+index).css('background-color');
    if(indexAnterior===null){
        $('#fila_'+index).css({'background-color':'#E85C3A'});
        indexAnterior = index;
    }
    else{
        if(index===indexAnterior){
            $('#fila_'+indexAnterior).css({'background-color':'#F9F9F9'});
            indexAnterior = null;
        }
        else{
            $('#fila_'+index).css({'background-color':'#E85C3A'});
            $('#fila_'+indexAnterior).css({'background-color':'#F9F9F9'});
            indexAnterior = index;
        }
    }
}

function changeViews(opc){
    if(parseInt(opc)===1){
        $('#view_contactoclientes').show('slow');
        $('#view_confclientes').hide('slow');
        $('#btnchangeview').html('<i class="fa fa-arrow-left" aria-hidden="true"></i> Volver').attr('onclick','changeViews(2)');
        dataClientes1 = [];
        getDataGeneralContactosClientes();
    }
    else{
        $('#tbgcontactoclientes').DataTable().destroy();
        $('#tbgcontactoclientes tbody').html('<td colspan="61" style="color: #2D96F2;padding-left: 20px;"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> Cargando Datos...</td>');
        $('#view_contactoclientes').hide('slow');
        $('#view_confclientes').show('slow');
        $('#btnchangeview').html('<i class="fa fa-user" aria-hidden="true"></i> Contacto clientes').attr('onclick','changeViews(1)');
        $('#tbgclientes').DataTable().destroy();
        $('#tbgclientes tbody').html('<tr><td colspan="47" style="color: #2D96F2;padding-left: 20px;"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> Cargando Datos...</td></tr>');
        
        dataClientes = [];
        getDataGeneral();
    }
}

let dataClientes1 = [];
function getDataGeneralContactosClientes(){
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getDataGeneralContactos',retornar:'no'},function(data){
        if(data!=='' && data!==null && data!=='null'){
            data = $.parseJSON(data);
            dataClientes1 = data;
            let td = '';
            $.each(data, function(i, item){
                let select = '';
                let contador = 0;
                $.each(item.datacliente, function(index, valor){
                    if(contador === 0){
                        let opt1 = '';
                        let opt2 = '';
                        let opt3 = '';
                        if(valor.valor==0 || valor.valor==null){opt1='selected';}else if(valor.valor==1){opt2='selected';}else if(valor.valor==2){opt3='selected';}
                        select +=   '<td><select id="slct_'+valor.ncampo+'_'+item.cuenta+'_'+i+'" onchange="changeMotivo(\''+item.cuenta+'\','+i+',\''+valor.ncampo+'\')" class="form-control"><option value="0" '+opt1+'>SELECCIONAR</option><option value="1" '+opt2+'>CAPACITACIÓN</option><option value="2" '+opt3+'>ESTADO GENERAL</option></select></td>';
                        contador++;
                    }
                    else if(contador === 1){
                        if(valor.valor==null || valor.valor=='0' || valor.valor==0){
                            select +=  '<td nowrap id="fechahora_'+valor.ncampo+'_'+item.cuenta+'">0000-00-00 00:00:00</td>';
                        }
                        else{
                            select +=  '<td nowrap id="fechahora_'+valor.ncampo+'_'+item.cuenta+'">'+valor.valor+'</td>';
                        }   
                        
                        contador++;
                    }
                    else if(contador === 2){
                        let cont = valor.valor;
                        let clas = '';
                        if(cont==null){
                            clas = 'si';
                        }
                        else if(parseInt(cont)>=0 && parseInt(cont)<=30){
                            clas = 'si';
                        }
                        else if(parseInt(cont)>=31 && parseInt(cont)<=59){
                            clas = 'na';
                        }
                        else if(parseInt(cont)>=60){
                            clas = 'no';
                        }
                        select +=   '<td align="center"><span class="'+clas+'">'+(valor.valor==null?0:valor.valor)+'</span></td>';
                        contador = 0;
                    }
                });
                td += '<tr id="fila_'+i+'"><td onclick="marcarFila1('+i+')">'+item.cuenta+'</td><td>'+item.cantvehiculos+'</td>'+select+'</tr>';
            });
            $('#tbgcontactoclientes tbody').html(td);
            $('#tbgcontactoclientes').DataTable({
                "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                "paging": true,
                "order": [[1, "desc" ]],
                "lengthChange": true,
                "lengthMenu": [[20,-1], [20,"Todos"]],
                "pageLength":100,
                "searching": true,
                "ordering": true,
                "info": true
            });
        }
    });
}

function changeMotivo(cuentacli, index, ncampo){
    //console.log(index,ncampo);
    let idselect = '#slct_'+ncampo+'_'+cuentacli+'_'+index;
    let options = $(idselect).val();
    let d = new Date();
    let fechahora = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
    let dataSql = ncampo+'='+options+', gc_fechahora'+(ncampo.charAt(ncampo.length-1))+'=now()';
    let valor1 = options;
    let campo1 = ncampo;
    let campo2 = 'gc_fechahora'+ncampo.charAt(ncampo.length-1);
    let cuenta = cuentacli;
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'actualizarOptionGestionCliente1',cuenta:cuenta,dataSql:dataSql,campo1:campo1,campo2:campo2,valor1:valor1,retornar:'no'},function(data){
        if(data!=='' && data!== null){
            data = $.parseJSON(data);
            if(data.status==='OK'){
                $('#fechahora_'+campo2+'_'+cuentacli).text(fechahora);
                toastr.success('Dato actualizado exitosamente.');
            }
            else{
                toastr.error('Error al actualizar Dato.');
            }
        }
    });
}

let indexAnterior1 = null;
function marcarFila1(index){
    //let color = $('#fila_'+index).css('background-color');
    if(indexAnterior1===null){
        $('#fila_'+index).css({'background-color':'#E85C3A'});
        indexAnterior1 = index;
    }
    else{
        if(index===indexAnterior1){
            $('#fila_'+indexAnterior1).css({'background-color':'#F9F9F9'});
            indexAnterior1 = null;
        }
        else{
            $('#fila_'+index).css({'background-color':'#E85C3A'});
            $('#fila_'+indexAnterior1).css({'background-color':'#F9F9F9'});
            indexAnterior1 = index;
        }
    }
}
</script>