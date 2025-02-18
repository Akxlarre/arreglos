<?php

?>
<!-- modal -->
<div class="modal" id="mlistveh">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
<!-- fin modal -->

<div class="content">
    <style>
        /* The snackbar - position it at the bottom and in the middle of the screen */
        #snackbar {
            visibility: hidden; /* Hidden by default. Visible on click */
            min-width: 250px; /* Set a default minimum width */
            margin-left: -125px; /* Divide value of min-width by 2 */
            background-color: #333; /* Black background color */
            color: #fff; /* White text color */
            text-align: center; /* Centered text */
            border-radius: 10px; /* Rounded borders */
            padding-left: 16px;
            padding-right: 16px;
            padding-top: 8px;
            padding-bottom: 8px;
            position: fixed; /* Sit on top of the screen */
            z-index: 1; /* Add a z-index if needed */
            left: 50%; /* Center the snackbar */
            bottom: 30px; /* 30px from the bottom */
        }

        /* Show the snackbar when clicking on a button (class added with JavaScript) */
        #snackbar.show {
            visibility: visible; /* Show the snackbar */
            /* Add animation: Take 0.5 seconds to fade in and out the snackbar.
            However, delay the fade out process for 2.5 seconds */
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        /* Animations to fade the snackbar in and out */
        @-webkit-keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }

        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }

        @-webkit-keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }

        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }

    </style>

    <!-- Incluir el archivo CSS de Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<!-- Incluir el archivo JS de Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <div class="row top20" id="tb_listadoveh">
        <div class="col-md-12">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title float-left" style="width:200px;">ICC vehículos</h3>
                    <button type="button" class="btn btn-sm btn-primary float-right" onclick="$('#vehiculoModal').modal('show')">Crear</button>
                    <button type="button" class="btn btn-sm btn-success float-right mr-1" id="btncc" onclick="CC()">Enviar CC</button>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-sm table-bordered table-striped table-condensed" id="tblvehiculosicc">
                    <thead>
                        <th><!--<div class="icheck-primary d-inline"><input type="checkbox" class="" id="vehCheckAll"><label for="vehCheckAll">Todos</label></div>--></th>
                        <th>Empresa</th>
                        <th>Patente</th>
                        <th>ICC</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td  class="text-center" colspan=6>
                                <span class='text-blue'>
                                    <h5>
                                        Cargando ... <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
                                    </h5>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="vehiculoModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="vehiculoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
            <div class="modal-header bg-primary" style="padding: 5px 15px;color:white;">
                <h5 class="modal-title" id="vehiculoModalLabel">Vehículo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="empresa">Empresa</label>
                            <select class="form-control form-control-sm" id="empresa" onchange="loadPatentes()"></select>
                        </div>
                    </div>

                    <!-- <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="patente">Patente</label>
                            <input type="text" class="form-control form-control-sm" id="patente" autocomplete="off">
                        </div>
                    </div> -->

                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="patente">Patente</label>
                            <select class="form-control form-control-sm" id="patente">
                                <!-- Opciones de patente -->
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="icc">ICC</label>
                            <input type="text" class="form-control form-control-sm" id="icc" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select class="form-control form-control-sm" id="tipo"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="resetForm()" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnsave" onclick="setVehiculo()">Guardar</button>
            </div>
            </div>
        </div>
    </div>

    <div id="snackbar">Some text some message..</div>
</div>

<script>
    //$("#cliente").chosen({width:'100%'})
    var vehiculosIcc = [];
    $(function(){
        
        // $('#patente').select2({
        //     placeholder: 'Seleccione una patente',
        //     allowClear: true
        // });

        getVehiculosIcc();
        getEmpresas();
        getTipos();
    });
    
    function getVehiculosIcc(idclie=0){
        vehiculosIcc = [];
        if($.fn.DataTable.isDataTable('#tblvehiculosicc')) {
            $('#tblvehiculosicc').DataTable().destroy();
        }
        var env   = {'idclie':''};
        var send  = JSON.stringify(env);
        $.ajax({
            url     : 'Modelo/operaciones_vehiculosicc.php',
            data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'vehiculosicc',retornar:'no',envio:send},
            type    : 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {
                   
            },error   : function(respuesta) {
                
            },success : function(respuesta) {
                var fila = ``;
                vehiculosIcc = respuesta;
                if(respuesta.length>0){
                    $.each(respuesta, function(i, item) {
                        let acciones = '<button type="button" title="Editar vehículo" onclick="editVehiculo('+i+')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                        acciones += ' <button type="button" title="Copiar datos" onclick="copyData('+i+')" class="btn btn-info btn-sm"><i class="fas fa-copy"></i></button>';
                        acciones += ' <div class="icheck-primary d-inline checkvehiculo"><input type="checkbox" class="" id="vehCheck'+i+'"><label for="vehCheck'+i+'"></label></div>';
                        let corriente = '<span class="badge badge-pill badge-success" style="cursor:pointer;">Encendido</span>';
                        if(item.ccorriente==1){
                            corriente = '<span class="badge badge-pill badge-danger" style="cursor:pointer;">Apagado</span>';
                        }
                        fila += `
                        <tr class="">
                            <td style="width:100px;" nowrap align="center">`+acciones+`</td>
                            <td>`+item.empresa+`</td>
                            <td>`+item.vehiculo+`</td>
                            <td>`+item.icc+`</td>
                            <td>`+(item.codigo==null ? '<span class="badge badge-danger">Sin código</span>' : item.codigo)+`</td>
                            <td style="width:100px;" align="center" id="estcc`+i+`">`+corriente+`</td>
                        </tr>`;
                        item['seleccionado'] = false;
                    });
                }
                $('#tblvehiculosicc tbody').html(fila);
                $('#tblvehiculosicc').DataTable({
                    "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                    "paging": true,
                    "lengthChange": true,
                    "lengthMenu": [[20,-1], [20,"Todos"]],
                    "pageLength":20,
                    "searching": true,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false
                });

                $('.checkvehiculo').on('change',function(){
                    $.each
                })
            }
        });
    }

    $('#vehCheckAll').on('click',function(){
        let checked = this.checked;
        $.each(vehiculosIcc, function(i,item){
            $('#vehCheck'+i).prop('checked',checked)
        })
    })

    function CC(){
        Swal.fire({
            icon: 'question',
            title: '¿Esta seguro de enviar CC?',
            showDenyButton: true,
            confirmButtonText: `Sí`,
            denyButtonText: `No`,
        }).then((result) => {
            if (result.isConfirmed) {
                $('#btncc').addClass('disabled').html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Enviando...').attr('onclick',null)
                $.each(vehiculosIcc, async function(i,item){
                    if($('#vehCheck'+i).prop('checked')){
                        console.log(item.ccorriente,item.idvehiculo,item.empresa,i)
                        await setCorriente(item.ccorriente,item.idvehiculo,item.empresa,i);
                    }
                })
                $('#btncc').removeClass('disabled').html('Enviar CC').attr('onclick','CC()')
            } else if (result.isDenied) {

            }
        });
        
    }

    function setVehiculo(idreg=0){

        let empresa = $('#empresa').val();
        let vehiculo = $('#patente').val();
        let icc = $('#icc').val();
        let tipo = $('#tipo').val();


        console.log("empresa : ",empresa," vehiculo: ", vehiculo," icc: ", icc," tipo: ", tipo," resultado : ",!(empresa || vehiculo || icc || tipo) );
        if( tipo<1 || empresa == 0 || vehiculo == '' ){
            console.log("no se puede enviar: ");
            return false;
        }

        // return false;

        $.ajax({
            url     : 'Modelo/operaciones_vehiculosicc.php',
            data    : {
                numero:''+Math.floor(Math.random()*9999999)+'',
                operacion:'setVehiculo',
                empresa: empresa,
                vehiculo: vehiculo,
                icc: icc,
                tipo: tipo,
                id:idreg,
                retornar:'no'},
            type    : 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {
                $('#btnsave').addClass('disabled').attr('onclick','').html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Guardando...')
            },error   : function(respuesta) {
                $('#btnsave').removeClass('disabled').attr('onclick','setVehiculo()').html('Crear')
                toastr.error(respuesta)
            },success : function(respuesta) {
                $('#btnsave').removeClass('disabled').attr('onclick','setVehiculo()').html('Crear')
                if(respuesta.status=='OK'){
                    resetForm();
                    getVehiculosIcc();
                    toastr.success(respuesta.message)
                }
                else{
                    toastr.error(respuesta.message)
                }
            }
        });
    }

    function loadPatentes(patenteEditando = '') {
        let empresaName = $('#empresa').val(); // Obtén el ID de la empresa seleccionada

        // Verifica que se haya seleccionado una empresa
        if (empresaName) {
            $.ajax({
                url     : 'Modelo/operaciones_vehiculosicc.php',  // Reemplaza con la ruta correcta del servidor
                data    : {
                    numero:''+Math.floor(Math.random()*9999999)+'',
                    operacion:'getPatenteCliente',
                    retornar:'no',
                    empresa: empresaName,
                },
                type    : 'GET',  // Usamos GET para obtener datos
                dataType: 'json',
                beforeSend: function() {
                    // Si quieres mostrar algo mientras se hace la solicitud
                    $('#patente').html('<option>Loading...</option>');
                },
                error   : function(respuesta) {
                    toastr.error('Hubo un error al obtener las patentes');
                },
                success : function(respuesta) {
                    // Limpia las opciones actuales
                    $('#patente').empty();

                    // Si la respuesta tiene patentes, las agregamos al select
                    if (respuesta.status === 'OK' && respuesta.patentes) {
                        // Agrega una opción inicial
                        $('#patente').append('<option value="">Seleccione una patente</option>');
                        
                        // Recorre las patentes y las agrega
                        respuesta.patentes.forEach(function(patente) {
                            $('#patente').append('<option value="' + patente.nombre + '">' + patente.nombre + '</option>');
                        });

                        $('#patente').val(patenteEditando);
                        

                    } else {
                        $('#patente').append('<option value="">Seleccione una patente</option>');
                        toastr.warning('No se encontraron patentes para esta empresa');
                    }

                    $("#patente").trigger('chosen:updated');
                }
            });
        } else {
            // Si no se selecciona empresa, limpiamos el select de patentes
            $('#patente').empty();
            $("#patente").trigger('chosen:updated');
        }
    }


    let patenteEditando = '';

    function editVehiculo(indice){

        let veh = null;
        $.each(vehiculosIcc,function(i,item){
            if(i==indice){
                veh = item;
            }
        });

        $('#empresa').val(veh.empresa).trigger('chosen:updated');
        // $('#empresa').val(veh.empresa).trigger('chosen:updated').prop('disabled', true);

        console.log("veh : ",veh);
        patenteEditando = veh.vehiculo;
        loadPatentes(patenteEditando);

        $('#patente').val(veh.vehiculo);

        $('#icc').val(veh.icc)
        $('#tipo').val(veh.tipo).trigger('chosen:updated');
        $('#btnsave').removeClass('btn-success').addClass('btn-warning').html('Actualizar').attr('onclick','setVehiculo('+veh.id+')')
        $('#vehiculoModal').modal('show');

    }

    function resetForm(){
        $('#empresa').val(0).trigger('chosen:updated');

        $('#patente').empty();
        $('#patente').val('').trigger('chosen:updated');

        $('#icc').val('')
        $('#tipo').val(0).trigger('chosen:updated');

        $('#btnsave').removeClass('btn-warning').addClass('btn-success').html('Guardar').attr('onclick','setVehiculo()')
    }

    function copyData(indice) {
        let veh = null;
        $.each(vehiculosIcc,function(i,item){
            if(i==indice){
                veh = item;
            }
        });
        if(veh!=null){
            let dataCopy = "Emrpesa: "+veh.empresa+'\n';
            dataCopy += "Vehículo: "+veh.vehiculo+'\n';
            dataCopy += "ICC: "+veh.icc;
            navigator.clipboard.writeText(dataCopy);

            showSnackBar("Datos copiados.");
        }
    }

    function showSnackBar(text,time=3000) {
        // Get the snackbar DIV
        var x = document.getElementById("snackbar");

        // Add the "show" class to DIV
        x.className = "show";

        x.innerHTML = text;

        // After 3 seconds, remove the show class from DIV
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, time);
    }

    async function setCorriente(estado,idveh,empresa,indice){
        await $.ajax({
            url: 'https://www.ds-tms.com/api/v5/setCCorriente',
            data: { 
                estado: estado, 
                idveh: idveh, 
                empresa: empresa, 
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(res) {
                $('#estcc'+indice).html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Cargando...');
            },
            error: function(res) {
                let corriente = '<span class="badge badge-pill badge-success" style="cursor:pointer;">Encendido</span>';
                if(estado==1){
                    corriente = '<span class="badge badge-pill badge-danger" style="cursor:pointer;">Apagado</span>';
                }
                $('#estcc'+indice).html(corriente)
            },
            success: function(data) {
                if (data != null && data != '') {
                    //data = $.parseJSON(data);
                    //console.log(data.icc);
                    if (data.message == 'OK') {
                        if(estado==1){
                            estado = 0;
                        }
                        else{
                            estado = 1;
                        }
                        let corriente = '<span class="badge badge-pill badge-success" style="cursor:pointer;">Encendido</span>';
                        if(estado==1){
                            corriente = '<span class="badge badge-pill badge-danger" style="cursor:pointer;">Apagado</span>';
                        }
                        $('#estcc'+indice).html(corriente)
                    } else if (data.message == 'ERROR') {
                        let corriente = '<span class="badge badge-pill badge-success" style="cursor:pointer;">Encendido</span>';
                        if(estado==1){
                            corriente = '<span class="badge badge-pill badge-danger" style="cursor:pointer;">Apagado</span>';
                        }
                    } else if (data.message == 'ERROR SIM') {
                        //console.log(data.sim);
                        let corriente = '<span class="badge badge-pill badge-success" style="cursor:pointer;">Encendido</span>';
                        if(estado==1){
                            corriente = '<span class="badge badge-pill badge-danger" style="cursor:pointer;">Apagado</span>';
                        }
                    } else {
                        let corriente = '<span class="badge badge-pill badge-success" style="cursor:pointer;">Encendido</span>';
                        if(estado==1){
                            corriente = '<span class="badge badge-pill badge-danger" style="cursor:pointer;">Apagado</span>';
                        }
                    }
                } else {
                    //console.log("Sin data.");
                    let corriente = '<span class="badge badge-pill badge-success" style="cursor:pointer;">Encendido</span>';
                    if(estado==1){
                        corriente = '<span class="badge badge-pill badge-danger" style="cursor:pointer;">Apagado</span>';
                    }
                }
            }
        });
    }

    function getEmpresas(){
        $.ajax({
            url     : 'Modelo/operaciones_vehiculosicc.php',
            data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getEmpresas',retornar:'no'},
            type    : 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {
                   
            },error   : function(respuesta) {
                console.error(respuesta)
            },success : function(respuesta) {
                var fila = `<option value="0">Seleccione</option>`;
                if(respuesta.length>0){
                    $.each(respuesta, function(i, item) {
                        fila += '<option value="'+item+'">'+item+'</option>';
                    });
                }
                $('#empresa').html(fila);
                $("#empresa").chosen({width:'100%'})

                $('#patente').empty();
                $("#patente").chosen({width:'100%'});

            }
        });
    }

    function getTipos(){
        $.ajax({
            url     : 'Modelo/operaciones_vehiculosicc.php',
            data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTypes',retornar:'no'},
            type    : 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {
                   
            },error   : function(respuesta) {
                console.error(respuesta)
            },success : function(respuesta) {
                var fila = `<option value="0">Seleccione</option>`;
                if(respuesta.length>0){
                    $.each(respuesta, function(i, item) {
                        fila += '<option value="'+item.id+'">'+item.tipo+'</option>';
                    });
                }
                $('#tipo').html(fila);
                $("#tipo").chosen({width:'100%'})
            }
        });
    }
</script>
