$(document).ready(function() {

    listarTickets();
    

});
var per = $('#perf').val();
var colorhome = $('#colorhome').val();
var colorroj = $('#colorroj').val();
var colorver = $('#colorver').val();
var colorazu = $('#colorazu').val();
var colorama = $('#colorama').val();

var tickets = [];
var tickets1 = [];


function cearTikects(id=0){
    $('#formTicket').find(':input:disabled').prop('disabled', false);
    $('#btncreartck').html('Crear Ticket').attr('onclick','cearTikects()')
    var formulario = new FormData($('#formTicket')[0]);
    formulario.append('operacion', 'cearTikects')
    
    
    if(id != 0){
        formulario.append('id', id)       
                      
    }
    if ($("#tck_descripcion").val() == '' ){
        toastr.error('El campo descripcion debe ir de forma obligartoria')
        return;
    }
    if ($("#tck_asunto").val() == '' ){
        toastr.error('El campo asunto debe ir de forma obligartoria')
        return;
    }if ($("#tck_comentario").val() == '' ){
        $("#tck_comentario").val('Sin comentarios');
        cearTikects();
        return;
    }
    $('#formTicket').find(':input:disabled').prop('disabled', true);
    $('#btncreartck').prop('disabled',true);
    $.ajax({
        method: "POST",
        url: "../cloux/Modelo/operaciones_tickets.php",
        type: 'POST',
        data: formulario,
        dataType: 'json',
        contentType: false,
        processData: false,

    }).done(function(data) {
        if (data != null && data != '') {
            if(data.status == 'OK'){
                cerrticket()
                
                toastr.success('Creado excitosamente')
                $('#btncreartck').prop('disabled',false);
            }
        } else {
            toastr.error('Error al crear ticket')
            $('#btncreartck').prop('disabled',false);
        }
    }).fail(function(error) {
       console.log(error)
    });

    
}
function listarTickets() {
    
    $('.tools').tooltip('dispose')
    if ($.fn.DataTable.isDataTable('#tblTicket')) {
        $('#tblTicket').DataTable().destroy();
    }
    $('#tblTicket tbody').html('<tr><td colspan="8"><div class="d-flex justify-content-center align-items-center h-100" ><div class="spinner-border" style="width: 3rem; height: 3rem;vertical-align:middle;color:' + colorhome + '" role="status"><span class="sr-only">Cargando...</span></div></div></td></tr>');

    var env   = {'valor':0};
    var send  = JSON.stringify(env);
      
    $.ajax({
        url     : '../cloux/Modelo/operaciones_tickets.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'listarTickets',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {
               
        },error   : function(respuesta) {
            console.log(respuesta)
        },success : function(data) {
            var proceso = '';
            var tipoVar = '';
            var fila = ``;
            
            tickets = data;
            $.each(data, function(i, items) {
                if(items.Tipo==1){
                   tipoVar = "Incidencia";
                }
                if(items.Tipo==2){
                    tipoVar = "Requerimiento";
                 }
                 if(items.Tipo==3){
                    tipoVar = "Consulta";
                 }
                 if(items.Estado_proceso==1){
                    proceso = "Enviado a Soporte TI";
                 }
                 if(items.Estado_proceso==2){
                    proceso = "En Ejecución";
                  }
                  if(items.Estado_proceso==3){
                    proceso = "Finalizado";
                  }
                fila += `<tr class="clickableRow" style="cursor:pointer" onclick="editarTicket(` + items.id + `,`+items.Estado_proceso+`); listarComentarios(` + items.id + `)">
                
                <td>`+items.id+`</td>
                <td>`+items.usuario+`</td>
                <td>`+items.Fecha_Hora+`</td>                
                <td>`+tipoVar+`</td>
                <td>`+items.Asunto+`</td>
                <td>`+items.Descripcion+`</td>
                <td>`+proceso+`</td>
                <td>`+items.Tiempo_transcurrido+`</td>
               
                <td  onclick="event.cancelBubble=true; return false;"><button data-toggle="tooltip" data-placement="left" title="Borrar Usuario" type="button" onclick="borrarTicket( `+ items.id + `,`+ items.Estado_proceso + `)" class="btn  btn-sm tools" style="color:white;background-color:` + colorroj + `"><i class="fa fa-trash"></i></td>
                </tr>`;
                                
            })
            $('#tblTicket tbody').html(fila);
            $('#tblTicket').DataTable({
                'destroy': true,
                "language": { url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json' },
                "paging": true,
                "order": [[0, "desc" ]],
                "lengthChange": true,
                "lengthMenu": [
                    [10, -1],
                    [10, "Todos"]
                ],
                "pageLength": 100,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [[ 0, "desc" ]],
            });

        }
        
    });
}
function cerrticket(){
    
    $('#ticketModal').modal("hide");
    limpiarTicket();
    listarTickets();
      
}
function borrarTicket(id, Estado_proceso){
if(Estado_proceso == 1){
    Swal.fire({
        title: "¿Seguro desea borrar el ticket?",
        text: "Este registro no aparecerá en el listado",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: colorver,
        cancelButtonColor: colorroj,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarTicket(id);
        }
    })
}
else{
    Swal.fire(
        {
            title: "!LO SENTIMOS!",
            text: "Este ticket ya esta siendo procesado",
            icon: "info",
            confirmButtonText: 'OK',
        }
    )
}

}
function eliminarTicket(id){
    $.get("../cloux/Modelo/operaciones_tickets.php", { numero: '' + Math.floor(Math.random() * 9999999) + '', operacion: 'eliminarTicket', id: id, retornar: 'no' }, function(data) {
        if (data != null && data != '') {
            data = $.parseJSON(data);
            if (data.status == 'OK') {
                listarTickets();
                Swal.fire('Exito al borrar ticket.', '', 'success')
            } else {
                Swal.fire('Error al borrar ticket, intente nuevamente.', '', 'error')
            }
        }
    });
}
function editarTicket(id){
    

    $('.tools').tooltip('dispose')
    
  
    let ticket = tickets.filter(tck => parseInt(tck.id) == parseInt(id));   
      if(ticket.length > 0){
        $('#btncreartck').prop('disabled',false);
        $('#tck_tipo').val(ticket[0].Tipo);
        $('#tck_asunto').val(ticket[0].Asunto);
        $('#tck_solicitante').val(ticket[0].usuario);
        $('#tck_estado').val(ticket[0].Estado);
        $('#tck_descripcion').val(ticket[0].Descripcion);  
        $('#tomarTickets').val(ticket[0].Estado_proceso); 
        $('#valorid').val(ticket[0].id);
        
        $('#btncreartck').html('Actualizar Ticket').attr('onclick','cearTikects('+id+')')
       
        
        $('#ticketModal').modal("show");

        if(ticket[0].Estado_proceso == 1){
            $('#tomarTickets').html('Tomar').prop('style','background-color: #82d2bf').attr('onclick','confirmarticket()')
            $('#tomarTickets').prop('disabled',false);
            $('#tck_comentario').prop('disabled',false);
        }
        if(ticket[0].Estado_proceso == 2){
            $('#tomarTickets').html('Finalizar').prop('style','background-color: orange').attr('onclick','finalizarTicket()')
            $('#tomarTickets').prop('disabled',false);
            $('#tck_comentario').prop('disabled',false);
        }
        if(ticket[0].Estado_proceso == 3){
            $('#tomarTickets').html('Finalizado').prop('disabled',true);
            $('#btncreartck').html('Actualizar Ticket').prop('disabled',true);
            $('#tomarTickets').html('Finalizado').prop('style','background-color: red');
            $('#tck_comentario').prop('disabled',true);
        }
        
    }
    
    
}
function limpiarTicket() {
    $('#tck_asunto').val('')
    $('#tck_descripcion').val('')
    $('#tck_tipo').val('')
    $('#tck_comentario').val('')
    listarTickets();
    
}


function listarComentarios(id) {
    $('.tools').tooltip('dispose')
    var env   = {'valor':0};
    var send  = JSON.stringify(env);
      
    $.ajax({
        url     : '../cloux/Modelo/operaciones_tickets.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'listarComentarios',id: id,retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {
               
        },error   : function(respuesta) {
            console.log(respuesta)
        },success : function(data) {
            var proceso = '';
            var tipoVar = '';
            var fila = ``;
            tickets1 = data;
            
            $.each(data, function(i, items) {
              
                fila += 
                `
                <label id="lblusu" name="lblusu" for="lblusu">`+items.Usuario+` </label><label style="padding: 10px" id="lblfecha" name="lblfecha" for="lblfecha">(`+items.Fecha_com+`) : </label><label name="lblcom" for="lblcom" id="lblcom" style="padding: 10px; font-weight: normal;color: #808080;"> ` +items.Comentarios+`</label> <br>
                `;
                
            })
            $('#divCom').html(fila);
        

        }
        
    });
}
function confirmarticket(){
    if($('#tomarTickets').val() == 2){
        
        Swal.fire({
            title: "¿Finalizar ticket?",
            
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: colorver,
            cancelButtonColor: colorroj,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                finalizarTicket();
            }
        })
    }
     else  if($('#tomarTickets').val() == 1){
        
        Swal.fire({
            title: "¿Comenzar proceso ticket?",
            
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: colorver,
            cancelButtonColor: colorroj,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                tomarTicket();
            }
        })
    }
    else{
        Swal.fire(
            {
                title: "GRACIAS",
                text: "Este ticket ya esta siendo procesado",
                icon: "info",
                confirmButtonText: 'OK',
            }
        )
    }
    
    
}

function tomarTicket(){
    let id = $('#valorid').val();
    $.get("../cloux/Modelo/operaciones_tickets.php", { numero: '' + Math.floor(Math.random() * 9999999) + '', operacion: 'tomarTicket', id: id, retornar: 'no' }, function(data) {
        if (data != null && data != '') {
            data = $.parseJSON(data);
            if (data.status == 'OK') {
                $('#tomarTickets').html('Finalizar').prop('style','background-color: orange').attr('onclick','finalizarTicket()')
                $('#tck_comentario').prop('disabled',true);
                Swal.fire('Ticket tomado ', '', 'success')
                
            } else {
                Swal.fire('Error al tomar ticket, intente nuevamente.', '', 'error')
            }
            
        }
        
    });
    
}
function finalizarTicket(){
    let id = $('#valorid').val();
    $.get("../cloux/Modelo/operaciones_tickets.php", { numero: '' + Math.floor(Math.random() * 9999999) + '', operacion: 'finalizarTicket', id: id, retornar: 'no' }, function(data) {
        if (data != null && data != '') {
            data = $.parseJSON(data);
            if (data.status == 'OK') {
                $('#tomarTickets').html('Finalizado').prop('style','background-color: red');
                
                Swal.fire('Ticket finalizado ', '', 'success')
                
            } else {
                Swal.fire('Error al finalizar ticket, intente nuevamente.', '', 'error')
            }
            
        }
        
    });
    $('#tomarTickets').html('Finalizado').prop('disabled',true);

}
