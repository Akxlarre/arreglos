<!-- modal -->
<div class="modal" id="mmar">
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
<section class="content">
    <div class="row top20">
        <div class="col-md-8" id="nmarca">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-inverse box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Nuevo Tipo de Servicio</h3>
                        </div>
                        <div class="box-body">
                            
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Tipo Servicio</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control">
                                </div>
                                <div class="col-sm-3 top25">
                                    <button type="submit" style="margin-top: 32px;;" class="btn btn-success btn-rounded" id="btnunidad" onclick="addts(0)">
                                            Registrar Tipo Servicio
                                    </button>
                                    &nbsp;&nbsp;
                                    <button type="button" style="margin-top: 32px; display: none;" class="btn btn-danger oculto btn-rounded" id="btn_cancelarts" onclick="carganom(1,0)">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            

                            <table class="table table-bordered table-striped table-sm" id="tblts">
                                <thead class="thead-dark">
                                    <th>N°</th>
                                    <th>Tipo Servicio</th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "select * from servicios where ts_estado = 1";
                                    $res = $link->query($sql);
                                    $x   = 0;
                                    while($fila = mysqli_fetch_array($res)){
                                        $x++;
                                    ?>
                                        <tr id="ts<?=$fila["ser_id"];?>">
                                        <td><?=$x;?></td>
                                        <td id="ts_nombre_<?=$fila["ser_id"];?>"><?=$fila["ser_nombre"];?></td>
                                        <td class="text-center" width="50">
                                            <button class="btn btn-warning btn-circle" onclick="carganom(0,<?=$fila["ser_id"];?>)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                        <td class="text-center" width="50">
                                            <button class="btn btn-danger btn-circle" onclick="delts(2,<?=$fila["ser_id"];?>)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 

<script>
    $(document).ready(function(){
        $('#tblts').DataTable({
        "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
        "paging": true,
        // "order": [[0, "desc" ]],
        "lengthChange": true,
        "lengthMenu": [[100,-1], [100,"Todos"]],
        "pageLength":100,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "ordering": false,
        // "drawCallback": function() {
        //     let search = $('#tblvehiculos_length').html();
        //     $('#tblvehiculos_length').html('<button type="button" class="btn btn-info mr-2"><i class="fas fa-file-excel"></i> Exportar Excel</button>');
        //  },
        });
    });

    function addts (opc,idts = 0){
        var nom = $('#nombre').val();

        if(nom == ''){
            toastr.error('Debes agregar un nombre');
        }else{
            env     = {'opc':opc,'idts':idts,'nom':nom};
            var send = JSON.stringify(env);
            $.ajax({
                url     : 'operaciones.php',
                data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'addts',retornar:'no',envio:send},
                type    : 'post',
                dataType: 'json',
                beforeSend: function(respuesta) {
                     $('#btnunidad').attr('disabled', true);
                     $('#btnunidad').html('Cargando...');
                },error   : function(respuesta) {
                     $('#btnunidad').attr('disabled', false);
                     if(opc==1){
                        $('#btnunidad').html('Actualizar Registro');
                     }else{
                        $('#btnunidad').html('Registrar Tipo Servicio');
                     }
                                     
                },success : function(respuesta) {
                    if(respuesta.logo=='success'){
                         toastr.success(respuesta.mensaje);
                         location.reload();
                    }else{
                         toastr.error(respuesta.mensaje);
                    }
                    $('#btnunidad').attr('disabled', false);
                    if(opc==1){
                        $('#btnunidad').html('Actualizar Registro');
                    }else{
                        $('#btnunidad').html('Registrar Tipo Servicio');
                    }
                }
            }); 
        }
    }

    function carganom(opc,idts = 0){
        if(opc==0){
            var nts = $('#ts_nombre_'+idts).text();
            $('#nombre').val(nts);
            $('#btn_cancelarts').show();
            $('#btnunidad').attr('onclick', 'addts(1,'+idts+')');
            $('#btnunidad').html('Actualizar Registro');
        }else if(opc==1){
            $('#nombre').val('');
            $('#btn_cancelarts').hide();
            $('#btnunidad').attr('onclick', 'addts(0,0)');
            $('#btnunidad').html('Registrar Tipo Servicio');
        }

    }

    function delts (opc,idts = 0){

        Swal.fire({
             title: '\u00BFEstas seguro de cambiar al técnico?',
             text: "Si cambias al técnico automaticamente tomara todas las series del traspaso",
             icon: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Confirmar'
        }).then((result) => {
             if (result.isConfirmed){
                env      = {'opc':opc,'idts':idts};
                var send = JSON.stringify(env);
                $.ajax({
                    url     : 'operaciones.php',
                    data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'addts',retornar:'no',envio:send},
                    type    : 'post',
                    dataType: 'json',
                    beforeSend: function(respuesta) {
                        $('#btnunidad').attr('disabled', true);
                        $('#btnunidad').html('Cargando...');   
                    },error   : function(respuesta) {
                        $('#btnunidad').attr('disabled', false);
                        $('#btnunidad').html('Registrar Tipo Servicio');                      
                    },success : function(respuesta) {
                        if(respuesta.logo=='success'){
                             toastr.success(respuesta.mensaje);
                             location.reload();
                        }else{
                             toastr.error(respuesta.mensaje);
                        }
                        $('#btnunidad').attr('disabled', false);
                        $('#btnunidad').html('Registrar Tipo Servicio');  
                    }
                }); 
             }
        })   
    }

</script>

  