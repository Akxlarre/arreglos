$(document).ready(function() {
    listarHistorial_veh();
  
});
var per = $('#perf').val();
var colorhome = $('#colorhome').val();
var colorroj = $('#colorroj').val();
var colorver = $('#colorver').val();
var colorazu = $('#colorazu').val();
var colorama = $('#colorama').val();
var colornar = $('#colornar').val();
var colorplomo = $('#colorplomo').val();
var colorrojcla = $('#colorrojcla').val();

function listarHistorial_veh() {
    $('.tools').tooltip('dispose')
    if ($.fn.DataTable.isDataTable('#tblhis')) {
        $('#tblhis').DataTable().destroy();
    }
    $('#tblhis tbody').html('<tr><td colspan="8"><div class="d-flex justify-content-center align-items-center h-100" ><div class="spinner-border" style="width: 3rem; height: 3rem;vertical-align:middle;color:' + colorhome + '" role="status"><span class="sr-only">Cargando...</span></div></div></td></tr>');

    var env   = {'valor':0};
    var send  = JSON.stringify(env);
      
    $.ajax({
        url     : '../cloux/Modelo/operaciones_historial.php',
        data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'listarHistorial_veh',retornar:'no',envio:send},
        type    : 'post',
        dataType: 'json',
        beforeSend: function(respuesta) {
               
        },error   : function(respuesta) {
            console.log(respuesta)
        },success : function(data) {
            var fila = ``;         
            $.each(data, function(i, items) {
                fila += `<tr id="trhis" name="trhis" for="trhis">
                
                <td id="tdhis" name="tdhis" for="tdhis">`+items.Patente+`</td>
                <td id="tdhis" name="tdhis" for="tdhis">`+items.Imei+`</td>
                <td id="tdhis" name="tdhis" for="tdhis">`+items.Fecha+`</td>
                </tr>`;
                
            })
            $('#tblhis tbody').html(fila);
            $('#tblhis td').css('text-align', 'center');
            $('#tblhis').DataTable({
                'destroy': true,
                "language": { url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json' },
                "paging": true,
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
                "order": [[0, "desc" ]],
                
            });

        }
        
    });
    
}