$(document).ready(function() {

    listarTickets();
    

});
var per = $('#perf').val();
var colorhome = $('#colorhome').val();
var colorroj = $('#colorroj').val();
var colorver = $('#colorver').val();
var colorazu = $('#colorazu').val();
var colorama = $('#colorama').val();
var colornar = $('#colornar').val();
var colorplomo = $('#colorplomo').val();

var tickets = [];
var tickets1 = [];
var ticketsTiempos = [];


function cearTikects(id=0){
    $('#formTicket').find(':input:disabled').prop('disabled', false);
    $('#btncreartck').html('Crear Ticket').attr('onclick','cearTikects()')
    var formulario = new FormData($('#formTicket')[0]);
    
    for (let i = 0; i < $('#fileImg').get(0).files.length; i++) {
        formulario.append("files[]", $('#fileImg').get(0).files[i]);
        
    }
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
function listarTickets(estado=0) {
    
    $('.tools').tooltip('dispose')
    if ($.fn.DataTable.isDataTable('#tblTicket')) {
        $('#tblTicket').DataTable().destroy();
    }
    $('#tblTicket tbody').html('<tr><td colspan="8"><div class="d-flex justify-content-center align-items-center h-100" ><div class="spinner-border" style="width: 3rem; height: 3rem;vertical-align:middle;color:' + colorhome + '" role="status"><span class="sr-only">Cargando...</span></div></div></td></tr>');

    var env   = {'valor':0,'estado':estado};
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
            var enviado= 0;
            var  ejecucion =0;
            var confir =0 ;
            var final = 0;
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
                    proceso = "Esperando Confirmación";
                  }
                  if(items.Estado_proceso==4){
                    proceso = "Finalizado";
                  }
                  if(items.Interaccion == 1){
                    inter = "NUEVO";
                   
                  }if(items.Interaccion == 2){
                    inter = "ACTUALIZACION ENVIADA";
                   
                  }
                  if(items.Interaccion == 3){
                    inter = "ACTUALIZACION RECIBIDA";
                   
                  }
                    let colorClase;
                    switch (proceso) {
                      case "Enviado a Soporte TI":
                        colorClase = colorama;
                        enviado++;
                        break;
                      case "En Ejecución":
                        colorClase = colornar;
                        ejecucion++;
                        break;
                      case "Esperando Confirmación":
                        colorClase = colorplomo;
                        confir++;
                        break;
                      case "Finalizado":
                        colorClase = colorver;
                        final++;
                        break;
                      // Agrega más casos según los valores y colores que necesites
                      default:
                        colorClase = ""; // Si no hay un color específico para este valor, deja la clase vacía
                        break;
                    }
                $('#fltEnviado').text(enviado);
                $('#fltEjecucion').text(ejecucion);
                $('#ftlConfirmacion').text(confir);
                $('#fltFinalizado').text(final);
                
                  
                    fila += `<tr id="trtick" name="trtick" for="trtick" class="clickableRow" style="cursor:pointer; background-color:` + colorClase + ` " onclick="editarTicket(` + items.id + `,`+items.Estado_proceso+`); listarComentarios(` + items.id + `);listarTiempos(` + items.id + `)">
                
                <td id="tdtick" name="tdtick" for="tdtick">`+items.id+`</td>
                <td id="tdtick" name="tdtick" for="tdtick">`+items.usuario+`</td>
                <td id="tdtick" name="tdtick" for="tdtick">`+items.Fecha_Hora+`</td>                
                <td id="tdtick" name="tdtick" for="tdtick">`+tipoVar+`</td>
                <td id="tdtick" name="tdtick" for="tdtick">`+items.Asunto+`</td>
                <td id="tdtick" name="tdtick" for="tdtick">`+items.Descripcion+`</td>
                <td id="tdtick" name="tdtick" for="tdtick">`+proceso+`</td>
                <td id="tdtick" name="tdtick" for="tdtick">`+items.Tiempo_transcurrido+`</td>
                <td id="tdtick" name="tdtick" for="tdtick">`+items.Fecha_Actualiza+`</td>
                <td id="tdtick" name="tdtick" for="tdtick">`+inter+`</td>
                <td  onclick="event.cancelBubble=true; return false;"><button data-toggle="tooltip" data-placement="left" title="Borrar Usuario" type="button" onclick="borrarTicket( `+ items.id + `,`+ items.Estado_proceso + `)" class="btn  btn-sm tools" style="color:white;background-color:` + colorroj + `"><i class="fa fa-trash"></i></td>
                </tr>`;
                
                $('#sop_eje').html(items.Tiempo_sop_tomado);
                $('#eje_vali').html(items.Tiempo_ejecu_envio);
                $('#vali_confi').html(items.Tiempo_envio_confirmo);
                $('#sop_confi').html(items.Tiempo_soporte_confirmo);
            })
            $('#tblTicket tbody').html(fila);
            $('#tblTicket').DataTable({
                'destroy': true,
                "language": { url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json' },
                "paging": true,
                "order": [[0, "asc" ]],
                "lengthChange": true,
                "lengthMenu": [
                    [100, -1],
                    [100, "Todos"]
                ],
                "pageLength": 100,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [[ 0, "asc" ]],
            });

        }
        
    });
}
function listarTiempos(id) {
    
    $('.tools').tooltip('dispose')
    var env   = {'valor':0};
    var send  = JSON.stringify(env);
      
    $.ajax({
        url     : '../cloux/Modelo/operaciones_tickets.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'listarTiempos',id: id,retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {
               
        },error   : function(respuesta) {
            console.log(respuesta)
        },success : function(data) {
                       
            ticketsTiempos = data;
            $.each(data, function(i, items) {
                $('#sop_eje').html(items.Tiempo_sop_tomado);
                $('#eje_vali').html(items.Tiempo_ejecu_envio);
                $('#vali_confi').html(items.Tiempo_envio_confirmo);
                $('#sop_confi').html(items.Tiempo_soporte_confirmo);
            })
        }
    });
}
function cerrticket(){
    
    $('#ticketModal').modal("hide");
    limpiarTicket();
    
    $('#preview').html('<img src="">');
    $('#sop_eje').html('-----');
    $('#eje_vali').html('-----');
    $('#vali_confi').html('-----');
    $('#sop_confi').html('-----');
    listarTickets();
    location.reload();
}
function borrarTicket(id, Estado_proceso){
if(Estado_proceso == 1 || Estado_proceso == 4){
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
function bloquear (){
    $('#tck_tipo').prop('disabled',true);
    $('#tck_asunto').prop('disabled',true);
    $('#tck_descripcion').prop('disabled',true);
}
function editarTicket(id){
    $('.tools').tooltip('dispose')
    listarTiempos(id);
    bloquear();
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
        if(ticket[0].Imagenes.length > 0){
            $.each(ticket[0].Imagenes, function(i, items) {
                $("#preview").append('<li><a href="../admin/dist/img/tickets/'+items.NombreImg+'" download>'+items.NombreImg+'</a></li>');
                console.log(items.NombreImg);
            })
        }

        if(ticket[0].Estado_proceso == 1){
            $('#tomarTickets').html('<i class="fas fa-check">Tomar</i>').prop('style','background-color: yellow').attr('onclick','confirmarticket()')
            $('#tomarTickets').prop('disabled',false);
            $('#tck_comentario').prop('disabled',false);
        }
        if(ticket[0].Estado_proceso == 2){
            $('#tomarTickets').html('<i class="fas fa-check">Enviar</i>').prop('style','background-color: orange').attr('onclick','finalizarTicket()')
            $('#tomarTickets').prop('disabled',false);
            $('#tck_comentario').prop('disabled',false);
        }
        if(ticket[0].Estado_proceso == 3){
            $('#tomarTickets').html('<i class="fas fa-check">Espera Confirmacion</i>').prop('disabled',true);
            $('#btncreartck').html('Actualizar Ticket').prop('disabled',true);
            $('#tomarTickets').prop('style','background-color: grey');
            $('#tck_comentario').prop('disabled',true);
        }if(ticket[0].Estado_proceso == 4){
            $('#tomarTickets').html('<i class="fas fa-check">Finalizado</i>').prop('disabled',true);
            $('#btncreartck').html('Actualizar Ticket').prop('disabled',true);
            $('#tomarTickets').prop('style','background-color: #82d2bf');
            $('#tck_comentario').prop('disabled',true);v
        }
      
        
    }
        
    }
    
    

function limpiarTicket() {
    $('#tck_asunto').val('')
    $('#tck_descripcion').val('')
    $('#tck_tipo').val('')
    $('#tck_comentario').val('')
  
    $('#divCom').html("");
    
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
            
            
            let filaSop = '';
                for (const item of data) {
                    
                    if (item.Usuario !== $('#tck_solicitante').val() && item.Comentarios.trim() !== "" && item.EnlacesImg.trim() !== "<a href='../dist/img/tickets/' download'></a><br>" ) {
                        filaSop +=
                           `<div class="message-box-holder">
                              <div class="message-sendersop">
                                  <p> (`+item.Fecha_com+`):</p>
                              </div>
                              <div class="message-box" >
                                  <p>`+item.Comentarios+` `+item.EnlacesImg+` </p>
                                  
                              </div>
                            </div>`;
                            $('#chat ').html(filaSop);
                           }
                    else if (item.Usuario !== $('#tck_solicitante').val() && item.Comentarios.trim() !== "") {
                            filaSop +=
                               `<div class="message-box-holder">
                                  <div class="message-sendersop">
                                      <p> (`+item.Fecha_com+`):</p>
                                  </div>
                                  <div class="message-box" >
                                      <p>`+item.Comentarios+`</p>
                                      
                                  </div>
                                </div>`;
                                $('#chat ').html(filaSop);
                               }
                    else if (item.Usuario !== $('#tck_solicitante').val() && item.EnlacesImg.trim() !== "<a href='../dist/img/tickets/' download'></a><br>") {
                                filaSop +=
                                   `<div class="message-box-holder">
                                      <div class="message-sendersop">
                                          <p> (`+item.Fecha_com+`):</p>
                                      </div>
                                      <div class="message-box" >
                                          <p>`+item.EnlacesImg+`</p>
                                          
                                      </div>
                                    </div>`;
                                    $('#chat ').html(filaSop);
                                   }        
                    else if(item.Usuario === $('#tck_solicitante').val() && item.Comentarios.trim() !== "") {
                              filaSop +=
                                `<div class="message-box-holder" name="msSop" for="msSop" id="msSop">
                                   <div class="message-sender">
                                       <p>`+item.Usuario+` (`+item.Fecha_com+`):</p>
                                   </div>
                                    <div class="message-box message-partner" name="msCliente" for="msCliente" id="msCliente">
                                        <p>`+item.Comentarios+` `+item.EnlacesImg+`</p>
                                        
                                    </div>
                                </div>`;
                                $('#chat').html(filaSop);
                                }
                            }
                        

        }
        
    });
}
function confirmarticket(){
    if($('#tomarTickets').val() == 2){
        
        Swal.fire({
            title: "¿Enviar ticket?",
            
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
                $('#tomarTickets').html('<i class="fas fa-check">Enviar</i>').prop('style','background-color: orange').attr('onclick','finalizarTicket()')
                

                Swal.fire('Ticket tomado ', '', 'success')
                setTimeout(cerrticket, 3000);
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
                $('#tomarTickets').html('<i class="fas fa-check">Espera Confirmacion</i>').prop('style','background-color: grey');
                $('#tck_comentario').prop('disabled',true);
                $('#btncreartck').html('Actualizar Ticket').prop('disabled',true);
                Swal.fire('Ticket Enviado, espere confirmación para que finalize este ticket  ', '', 'success')
                
                
            } else {
                Swal.fire('Error al finalizar ticket, intente nuevamente.', '', 'error')
            }
            
        }
        
    });
    $('#tomarTickets').html('<i class="fas fa-check">Finalizado</i>').prop('disabled',true);

}
$("#btnexportartickets").on("click",function(){
    
    
    Swal.fire({
        title             : 'Ingresa Rango de Fecha',
        html              : '<label>Desde</label><input type="date" id="datedesde" class="swal2-input" value=""><label>Hasta</label><input type="date" id="datehasta" class="swal2-input" value="">',
        confirmButtonText : 'Enviar',
        focusConfirm      : false,
        preConfirm        : () => {
            const desde = Swal.getPopup().querySelector('#datedesde').value
            const hasta = Swal.getPopup().querySelector('#datehasta').value
            if(!desde && !hasta){
                Swal.showValidationMessage(`Debes ingresar las fecha`);
            }
            return {desde: desde,hasta:hasta}
        }
    }).then((result) => { 
    desde = result.value.desde;
    hasta = result.value.hasta;  
    env   = {'desde':desde,'hasta':hasta}; 
    var send = JSON.stringify(env);
    $('#btnexportartickets').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Generando Excel...').attr('disabled', true)
    
    $.ajax({
        method: "POST",
        url: "../cloux/Modelo/operaciones_tickets.php",
        data: {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTicketsExcel',retornar:'no',envio:send},
        type: 'post',
        dataType: 'json',
    }).done(function(data) {
        console.log(data);
        $('#btnexportartickets').html('<i class="fas fa-file-excel"></i> Exportar Excel').attr('disabled', false)
        if (data != null && data != '') {
            if (data) {
                //data = $.parseJSON(data);
                var $a = $("<a>");
                $a.attr("href", data.file);
                $("body").append($a);
                $a.attr("download", "Tickets.xlsx");
                $a[0].click();
                $a.remove();
            } else {
                toastr.error('Error al exportar excel.')
            }
        }
    }).fail(function(error) {
        console.log(error);

    });
    }) 
});
/*function exportarTicket() {
    $('#btnexportartickets').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Generando Excel...').attr('disabled', true)
    let formulario = new FormData();
    formulario.append('operacion', 'getTicketsExcel');
    formulario.append('retornar', 'no');
    $.ajax({
        method: "POST",
        url: "../cloux/Modelo/operaciones_tickets.php",
        data: formulario,
        processData: false,
        contentType: false
    }).done(function(data) {
        $('#btnexportartickets').html('<i class="fas fa-file-excel"></i> Exportar Excel').attr('disabled', false)
        if (data != null && data != '') {
            if (isJson(data)) {
                data = $.parseJSON(data);
                var $a = $("<a>");
                $a.attr("href", data.file);
                $("body").append($a);
                $a.attr("download", "Tickets.xlsx");
                $a[0].click();
                $a.remove();
            } else {
                toastr.error('Error al exportar excel.')
            }
        }
    }).fail(function(error) {
        console.log(error);

    });
}*/
function reloadPagina() {
    
    window.location.reload(); 
}
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

