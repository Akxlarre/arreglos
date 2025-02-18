<?php
$msjerr = '';
if(isset($_REQUEST['deveh'])){
    if($_REQUEST['deveh']==1){
        ?>
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                let timerInterval
                Swal.fire({
                  title: 'Correcto!',
                  icon: 'success',
                  html: 'Vehiculo editado correctamente',
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
    <div class="row top20" id="tb_listadoveh">
        <div class="col-md-12">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Listado de dispositivos</h3>
                </div>
                <div class="box-body table-responsive">

                    <button id="exportButton">Export Data</button>

                    <table class="table table-bordered table-striped table-condensed" id="tabveh">
                    <thead>
                        <th>Empresa</th>
                        <th>Patente (GPS)</th>
                        <th>IMEI (GPS)</th>
                        <th>IMEI (CLIENTE)</th>
                        <th>FECHA (GPS)</th>
                        <th>FECHA (CLIENTE)</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td  class="text-center" colspan=12>
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

    <div class="row top20 oculto" id="cfe_veh">
        <div class="col-md-8" >
            <div class="box box-warning box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Editar Vehículo</h3>
                    <span style="float: right;" class="pointer" onclick="cerrarEdicion()">
                        <i class="fa fa-times"></i>
                    </span>
                </div>
                <div class="box-body">
                    <form action="operaciones.php" method="post" class="form-horizontal" id="fe_veh">
                        <input type="hidden" name="operacion" value="editarvehiculo"/>
                        <input type="hidden" name="idveh" id="idveh" value="">
                        <input type="hidden" name="deveh" id="deveh" value="1">
                        <!-- <input type="hidden" name="vehid" id="vehid" value=""> -->
                        <input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>

                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Tipo</label>
                        <div class="col-sm-6"><?= htmlselect('tipo','tipo','tiposdevehiculos','tveh_id','tveh_nombre','','','','tveh_nombre','','','si','no','no');?></div>
                        </div>

                        <div class="form-group">
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

                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Transportista</label>
                        <div class="col-sm-6"><?= htmlselect('cliente','cliente','clientes','id','razonsocial','','','','razonsocial','','','si','no','no');?></div>
                        </div>
                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Grupo</label>
                        <div class="col-sm-6"><select name="grupo" id="grupo" class="form-control"></select></div>
                        </div>
                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Región</label>
                        <div class="col-sm-6"><?= htmlselect('region','region','regiones','id','region|ordinal','','','','id','getComunas()','','si','no','no');?></div>
                        </div>
                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Comuna</label>
                        <div class="col-sm-6"><?= htmlselect('comuna','comuna','comunas','comuna_id','comuna_nombre','','','','comuna_id','','','si','no','no');?></div>
                        </div>
                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Patente</label>
                        <div class="col-sm-3"><input type="text" name="patente" class="form-control"></div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label txtleft">Imei</label>
                            <div class="col-sm-3"><input type="text" name="veh_imei" class="form-control"></div>
                        </div>

                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Dispositivo</label>
                        <div class="col-sm-6"><?= htmlselect('dispositivo','dispositivo','tiposdedispositivos','tdi_id','tdi_nombre','','','','tdi_nombre','','','si','no','no');?></div>
                        </div>
                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Tipo de servicio</label>
                        <div class="col-sm-6">
                            <select id="tservicio" name="tservicio" class="form-control">
                                <option value="0">Basico</option>
                                <option value="1">Avanzado</option>
                            </select>
                            <!-- <?= htmlselect('tservicio','tservicio','tiposdetrabajos','ttra_id','ttra_nombre','','','','ttra_nombre','','','si','no','no');?> -->
                            
                        </div>
                        </div>
                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Contacto</label>
                        <div class="col-sm-6"><input type="text" name="contacto" id="contacto" class="form-control"></div>
                        </div>
                        <div class="form-group">
                        <label class="col-sm-3 control-label txtleft">Celular</label>
                        <div class="col-sm-4"><input type="text" name="celular" id="celular" class="form-control"></div>
                        </div>
                        <div class="" id="view_equipamiento">
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-sm-4 col-lg-6">
                                    <label>Producto</label><br>
                                    <?= htmlselect('selectproducto','selectproducto','productos','pro_id','pro_nombre','','','','pro_nombre','getProductosDetails()','','si','no','no');?>
                                </div>
                                <div class="col-sm-4 col-lg-6">
                                    <label>Cantidad</label><br>
                                    <input type="number" name="cantidad" id="cantidad" class="form-control">
                                </div>
                                <div class="col-sm-4 col-lg-5 oculto divserie">
                                    <label>Serie</label><br>
                                    <input type="text" name="serie" id="serie" class="form-control">
                                </div>
                                <div class="col-sm-4 col-lg-1 oculto divserie">
                                    <span class="btn btn-info btn-circle tooltips top20" onclick="addSerie()"><i class="fa fa-list-ol"></i><span class="tooltiptext tooltip-left"></span></span>
                                </div>
                                <div class="col-sm-4 col-lg-6">
                                    <button type="button" style="margin-top:23px;margin-left:20px;" class="btn btnh-success btn-rounded" onclick="addProducto()" id=""><i class="fa fa-plus"></i> Agregar Producto</button>
                                </div>
                                <div class="col-sm-12 col-lg-12 table-responsive top10 oculto" id="divtableproducto">
                                    <table class="table table-sm table-striped table-bordered table-hover table-condensed" id="tableproductosxveh">
                                        <thead class="thead-dark">
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
                        <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-6">
                            <button type="submit" class="btn btn-success btn-rounded" id="btnunidad">Editar Vehículo</button>&nbsp;&nbsp;
                            <button type="button" class="btn btn-danger btn-rounded" id="btn_ceLinea" onclick="CancelarEV();">Cancelar</button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    window.vehiculos;

    $(function(){
        getTabVehiculos();

        /*
        $('#tabveh').on('draw.dt', function() {
            console.log( "$('#tabveh').on('draw.dt', function() " );
            var parametros = $('#tabveh').DataTable().ajax.params(); // Obtener parámetros de la solicitud AJAX
            $.ajax({
                url: 'operaciones.php?numero='+Math.floor(Math.random()*9999999)+'&operacion=listarVehiculosAll&retornar=no',
                method: 'GET',
                data: parametros,
                dataType: 'json',
                success: function(response) {

                    //     datos     = $.parseJSON(data);
                    vehiculos = response.data;
                    // Manejar la respuesta del servidor con los datos filtrados
                    console.log("response data : ",response.data );
                    // Asignar los datos filtrados a una variable, por ejemplo:
                    var datosFiltrados = response.data;
                    console.log("datosFiltrados : ", datosFiltrados);
                },
                error: function(xhr, status, error) {
                    // Manejar errores de la solicitud AJAX
                    console.error("error : ",error);
                }
            });
        });
        */

    });

    var table;

    function getTabVehiculos(){

        table = $('#tabveh').DataTable({
            ajax: 'operaciones.php?numero='+Math.floor(Math.random()*9999999)+'&operacion=listarVehiculosAll&retornar=no',
            processing: true,
            serverSide: true,
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json',
                thousands: '.' // Usar punto como separador de miles
            },
            "dom": '<"top"flp>rt<"bottom"ip><"clear">',
            "paging": true,
            "lengthChange": true,
            "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
            "pageLength": 25,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            columns: [
                //{ data: 'id_cont', orderable: false, "defaultContent":'' },
                //{ data: 'badg', "defaultContent":'' },
                //{ data: 'tipo', "defaultContent":'' },
                //{ data: 'tservicio', "defaultContent":'' },
                { data: 'empresa', "defaultContent":'' },
                { data: 'patente', "defaultContent":'' },
                { data: 'imei', "defaultContent":'' },
                { data: 'imei_cliente', orderable: false, "defaultContent":'' },
                { data: 'fh_gps', "defaultContent":'' },
                { data: 'ulttransmision', orderable: false, "defaultContent":'' },
                //{ data: 'span3', orderable: false, "defaultContent":'' },
            ],
            "columnDefs": [
                {
                    "targets": 3, // Target the third column for rendering
                    "render": function(data, type, row) {
                        data = (data == undefined ? '': data); 
                        if (row['imei'] === row['imei_cliente']) {
                            return data+'<span style="color: green;">✔️</span>'; // Add your "ok" icon here
                        } else {
                            return data+'<span style="color: red;">❌</span>'; // Optional: Add an "error" icon
                        }
                    }

                },
                {
                    "targets": 5, // Target the third column for rendering
                    "render": function(data, type, row) {
                        data = (data == undefined ? '': data); 
                        if (row['fh_gps'] === row['ulttransmision']) {
                            return data+'<span style="color: green;">✔️</span>'; // Add your "ok" icon here
                        } else {
                            return data+'<span style="color: red;">❌</span>'; // Optional: Add an "error" icon
                        }
                    }
                }
            ]
        });

        //AllVehiculo();
        // vehiculos = datos;
    }

    function AllVehiculo(){

        // Obtén los valores de los filtros manualmente
        var searchValue = table.search();
        var order = table.order();
        var length = 5000;//table.page.len();
        var start = table.page.info().start;
        var customFilters = {}; // Añadir cualquier filtro adicional que puedas tener

        // Puedes añadir más parámetros personalizados si los usas
        $('#customFilterForm').serializeArray().forEach(function(filter) {
            customFilters[filter.name] = filter.value;
        });

        // Configura los parámetros para la solicitud Ajax
        var params = {
            search: { value: searchValue },
            order: order,
            length: length,
            start: start,
            customFilters: customFilters
        };

        // Desactiva el botón mientras se descarga el archivo
        $('#exportButton').prop('disabled', true);

        // Mostrar SweetAlert
        Swal.fire({
                    title: 'Descargando Excel...',
                    html: 'Por favor, espera un momento.',
                    timerProgressBar: true,
                    showConfirmButton: false, // Quita el botón OK
                    allowOutsideClick: false, // Evita que el SweetAlert se cierre al hacer clic fuera
                    willOpen: () => {
                        Swal.showLoading();
                    }
                }).then((result) => {
                    // La función then se ejecutará cuando Swal.close() sea llamado
                    if (result.dismiss === Swal.DismissReason.timer) {
                        console.log('Descarga completada o cancelada');
                    }
                });


        console.log("params: ",params);//return;
        $.ajax({
            url: 'operaciones.php?numero='+Math.floor(Math.random()*9999999)+'&operacion=listarVehiculosAll&retornar=no',
            method: 'POST',
            data: params,
            success: function(data) {
                // Procesamos los datos y llamamos a la función de exportación
                json = JSON.parse(data);
                //console.log("data", json.data);
                exportAllData(json.data);
                
            },
            error: function() {
                // Habilita el botón si ocurre un error
                $('#exportButton').prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al generar el archivo Excel.',
                    showConfirmButton: true // Muestra el botón OK en el error
                });
            }
        });

    }

    function exportAllData(data) {
        // Convertimos los datos en un formato adecuado para la descarga (por ejemplo, CSV)

        var filteredData = data.map(function(item) {
                    return {
                        Empresa: item.empresa,
                        Patente: item.patente,
                        'IMEI (GPS)': item.imei,
                        'IMEI (CLIENTE)': item.imei_cliente,
                        'Fecha (GPS)': item.fh_gps,
                        'Fecha (Cliente)': item.ulttransmision,
                    };
                });

        var ws = XLSX.utils.json_to_sheet(filteredData);

         // Ajusta el ancho de las columnas
         var maxWidths = [];
        filteredData.forEach(function(row) {
            Object.keys(row).forEach(function(key, colIndex) {
                const value = String(row[key]);
                maxWidths[colIndex] = Math.max(maxWidths[colIndex] || 0, value.length);
            });
        });

        ws['!cols'] = maxWidths.map(function(width) {
            return { width: width + 2 }; // Añade un margen para mejor visualización
        });

        /*
        // Estilo de cabecera
        var header = ws['!rows'][0]; // Primera fila
        for (let cell in header) {
            header[cell].s = {
                fill: { patternType: 'solid', fgColor: { rgb: 'FFFF00' } }, // Color de fondo amarillo
                font: { color: { rgb: '000000' }, bold: true } // Fuente negra y negrita
            };
        }

        // Estilo de contenido
        ws['!rows'].forEach((row, rowIndex) => {
            if (rowIndex !== 0) { // Excluye la cabecera
                for (let cell in row) {
                    row[cell].s = {
                        font: { color: { rgb: '000000' } } // Texto en negro
                    };
                }
            }
        });*/


        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "GPS");

        // Obtener la fecha y hora actuales
        var now = new Date();
        var dateString = now.toISOString().slice(0, 10);
        var timeString = now.toTimeString().slice(0, 8).replace(/:/g, '');
        var filename = `GPS_${dateString}_${timeString}.xlsx`;

        // Especifica el nombre del archivo
        XLSX.writeFile(wb, filename);

        // Habilita el botón después de la descarga
        $('#exportButton').prop('disabled', false);

        // Desaparecer SweetAlert
        Swal.close();
    }

    // Llamamos a la función cuando sea necesario (por ejemplo, al hacer clic en un botón)
    document.getElementById('exportButton').addEventListener('click', AllVehiculo);

    function ProductosVehiculo(index,idpatente){

        env = {'index':index,'idpatente':idpatente};
        var send = JSON.stringify(env);
        $.ajax({
            url     : 'operaciones.php',
            data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getdetallevehiculo',retornar:'no',envio:send},
            type    : 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {

            },error   : function(respuesta) {
                console.log(respuesta);
            },success : function(respuesta) {
                var tbproductos = '';
                var patente     = '';
                if(respuesta.length>0){
                    $.each(respuesta, function(i,valor){
                        if(i==0){
                            tbproductos +="<table class='table table-bordered table-striped'><thead><th>N° Vehiculo</th><th>Serie</th></thead><tbody>";
                            patente = valor.patente;

                             tbproductos+="<tr><td>"+valor.id+"</td><td>"+valor.imei+"</td></tr>";
                        }else{
                             tbproductos+="<tr><td>"+valor.id+"</td><td>"+valor.imei+"</td></tr>";
                        }
                        
                    });
                    tbproductos+="</tbody></table>";
                    $("#mlistveh .modal-dialog").css({'width':'50%'});
                    $("#mlistveh .modal-header").removeClass("header-rojo").addClass("header-verde");
                    $("#mlistveh .modal-title").html("Productos Patente : "+patente+"");
                    $("#mlistveh .modal-body").html(tbproductos);
                    $("#mlistveh .modal-footer").css("<button type='button' class='btn btn-info btn-rounded' data-dismiss='modal'>OK</button>");
                    $("#mlistveh").modal("toggle");
                }else{

                }
            }
        });

        /*pxv = vehiculos[index];
        tbproductos ="<table class='table table-bordered table-striped'><thead><th>Producto</th><th>Serie</th><th>N° Serie</th></thead><tbody>";
        $.each(pxv["productos"],function (index,valor){
        tbproductos+="<tr><td>"+valor.producto+"</td><td>"+valor.tieneserie+"</td><td>"+valor.serie+"</td></tr>";	
        });*/	
    }

    let productosXVeh = [];
    function EditarVehiculo(index,vehid = 0){
        console.log("vehiculos : ",index, vehid, vehiculos[index]);
        vehiculo = vehiculos[index];

        idcli    = vehiculo["idcliente"]
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getGruposCliente',id:''+idcli+'',retornar:'no'},function(data){
            console.log(data);
            datos = $.parseJSON(data);
            sgrup="<option value=0>SELECCIONAR</option>";
            $.each(datos,function(index,valor){
                sgrup+="<option value="+valor.id+">"+valor.nombre+"</option>";
            });
            $("#grupo").html(sgrup);
            $("#grupo").val(vehiculo["idgrupo"]);
           /* $("input[name='idveh']").val(vehiculo["idveh"]);*/
            $("#idveh").val(vehid);
            $("#gps").val(vehiculo["idgps"]);
            $("#tipo").val(vehiculo["idtipo"]);
            $("#cliente").val(vehiculo["idcliente"]);
            $("input[name='patente']").val(vehiculo["patente"]);
            $("input[name='veh_imei']").val(vehiculo["veh_imei"]);
            $("#dispositivo").val(vehiculo["dispositivo"]);
            $("#tservicio").val((vehiculo["tservicio"]==0 || vehiculo["tservicio"]==2?0:1));
            $("#region").val(vehiculo["region"]);
            $("#comuna").val(vehiculo["comuna"]);
            $("#contacto").val(vehiculo["contacto"]);
            $("#celular").val(vehiculo["celular"]);
            $("#tb_listadoveh").hide();
            $("#cfe_veh").show();

            $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getProductosxVehiculos',id:vehiculo["idveh"],retornar:'no'},function(proxveh){
                if(proxveh!='' && proxveh!=null){
                    proxveh = $.parseJSON(proxveh);
                    if(proxveh.length>0){
                        console.log('proxveh',proxveh);
                        $.each(proxveh, function(i, item){
                            var nproductos = $("#tb_agregarproducto tbody tr").length;
                            nproductos=nproductos+1;
                            let rows = "<tr id='con_fila"+nproductos+"'><td>"+nproductos+"</td><td>"+item.producto+"</td><td .cantidad>"+item.cantidad+"</td><td>"+item.serie+"</td><td><span class='btn btn-danger btn-circle-s' onclick='removeItem("+nproductos+")'><i class='fa fa-trash'></i></span></td></tr>";
                            $('#tableproductosxveh tbody').append(rows);
                            $("#inp_agregarproductos").append("<input type='hidden' id='idcon"+nproductos+"' name='productosSend[]' value=\""+item.cantidad+"|"+item.idproducto+"|"+item.serie+"|"+item.id+"\">");
                        });
                        $('#divtableproducto').show('slow');
                        productosXVeh = proxveh;
                    }
                }
            });
        });
    }

    function CancelarEV(){
        $("input[name='idveh']").val("");
        $("#cliente").val("");
        $("input[name='patente']").val("");
        $("#cfe_veh").hide();
        $("#tb_listadoveh").show();
    }

    function EliminarVehiculo(index){
        vehiculo = vehiculos[index];
        $("#mlistveh .modal-dialog").css({'width':'50%'});
        $("#mlistveh .modal-header").removeClass("header-verde").addClass("header-rojo");
        $("#mlistveh .modal-title").html("Eliminar Vehículo");
        $("#mlistveh .modal-body").html("Realmente desea eliminar este vehículo : <b>"+vehiculo["patente"]+"</b>");
        $("#mlistveh .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarVEH(\""+index+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
        $("#mlistveh").modal("toggle");
    }

    function eliminarVEH(index){
        vehiculo = vehiculos[index];	
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarvehiculo',idveh:''+vehiculo["idveh"]+'',retornar:'no'},function(data){
            $("#veh"+index+"").remove();
            $("#mlistveh").modal("hide");
        });
    }

    function cerrarEdicion(){
        window.location.reload();
    }

    function getComunas(){
        idregion = $("#region option:selected").val();
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getComunas',region:''+idregion+'',retornar:'no'},function(data){
            console.log(data);
            $("#comuna").html(data);
        });
    }

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
        $("#inp_agregarproductos").append("<input type='hidden' id='idcon"+nproductos+"' name='productosSend[]' value=\""+cantidad+"|"+producto+"|"+serie+"|0\">");
        $('#divtableproducto').show('slow');
        $("#selectproducto").val('');
        $("#cantidad").val('');
        $('.divserie').hide('slow');
        $('#serie').val('');
    }

    function removeItem(id){
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'delProductoxVehiculo',id:productosXVeh[(id-1)].idproducto,retornar:'no'},function(data){
            if(data!='' && data!=null){
                data = $.parseJSON(data);
                if(data.status=='OK'){
                    toastr.success('Producto eliminado exitosamente.');
                    $("#con_fila"+id+", #idcon"+id+"").remove();
                }
                else{
                    toastr.error('Error al eliminar producto.');
                }
            }
        });
    }

    
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<style>
        

        h2 {
            margin-bottom: 20px;
        }

        #exportButton {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 12px;
            transition-duration: 0.4s;
        }

        #exportButton:hover {
            background-color: white;
            color: black;
            border: 2px solid #4CAF50;
        }

        #example {
            margin-top: 20px;
        }
    </style>