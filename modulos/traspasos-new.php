<?php
$sql3 = "SELECT * FROM familias ORDER BY fam_id";
$res3 = $link->query($sql3);
$opt = '<select onchange="buscarProducto()" tabindex="5" data-placeholder="Seleccione producto" class="chosen-select" id="producto" name="producto"><option value=""></option>';
while ($fila3 = mysqli_fetch_array($res3)) {
    $opt .= '<optgroup style="color:#338AFF;" label="' . $fila3['fam_nombre'] . '">';
    $sql1 = "SELECT * FROM productos pro LEFT OUTER JOIN familias fam ON pro.pro_familia=fam.fam_id WHERE pro.pro_familia={$fila3['fam_id']} ORDER BY pro.pro_nombre";
    $res1 = $link->query($sql1);
    while ($fila1 = mysqli_fetch_array($res1)) {
        $opt .= '<option value="' . $fila1['pro_id'] . '">' . $fila1['pro_nombre'] . '</option>';
    }
    $opt .= '</optgroup>';
}
$opt .= '</select>';
?>

<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<!-- modal -->
<div class="modal" id="mtras">
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
<section class="content">
    <div class="row submenu">
        <div class="col-md-12" style="padding: 10px;">
            <button type='button' class="btn btn-success btn-rounded" id="btn_ntraspaso"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Traspaso</button>
        </div>
    </div>
    <div class="row top20 oculto" id="fnuevotraspaso">
        <div class="col-md-12">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Nuevo Traspaso</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <form action="operaciones.php" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <input type="hidden" name="operacion" value="nuevotraspaso" />
                            <input type="hidden" name="retornar" value="index.php?menu=<?= $_REQUEST["menu"]; ?>&idmenu=<?= $_REQUEST["idmenu"]; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="fecha">Fecha</label>
                                                <input type="text" class="form-control form-control-sm" name="fecha" id="fecha" value="<?= hoy(); ?>">
                                            </div>
                                        </div>


                                        <?php if ($_SESSION['perfil_new'] == 3) { ?>
                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label for="bodega">Bodega(Técnico)</label>
                                                    <!-- per_id <> 26 and -->
                                                    <?= htmlselect('bodega', 'bodega', 'personal', 'per_id', 'per_nombrecorto', '', '', 'where  (per_id= ' . $_SESSION['personal_new'] . ' or per_id in(select usu_idpersonal from usuarios where usu_perfil=3) ) and deleted_at is NULL', 'per_nombrecorto', 'activeTraspaso()', '', 'si', 'no', 'no'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3 oculto" id="divbodega2">
                                                <div class="form-group row">
                                                    <label for="bodega2">Bodega(Técnico)</label>
                                                    <!-- per_id <> 26 and -->
                                                    <?= htmlselect('bodega2', 'bodega2', 'personal', 'per_id', 'per_nombrecorto', '', '', 'where per_id in(select usu_idpersonal from usuarios where usu_perfil=3) and deleted_at is NULL', 'per_nombrecorto', 'productosxternico(this.value,2)', '', 'si', 'no', 'no'); ?>
                                                </div>
                                            </div>

                                        <?php } else {
                                        ?>
                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label for="bodega">Bodega(Técnico)</label>
                                                    <!-- per_id <> 26 and -->
                                                    <?= htmlselect('bodega', 'bodega', 'personal', 'per_id', 'per_nombrecorto', '', '', 'where  deleted_at is NULL', 'per_nombrecorto', 'activeTraspaso()', '', 'si', 'no', 'no'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3 oculto" id="divbodega2">
                                                <div class="form-group row">
                                                    <label for="bodega2">Bodega(Técnico)</label>
                                                    <!-- per_id <> 26 and -->
                                                    <?= htmlselect('bodega2', 'bodega2', 'personal', 'per_id', 'per_nombrecorto', '', '', 'where  deleted_at is NULL', 'per_nombrecorto', 'productosxternico(this.value,2)', '', 'si', 'no', 'no'); ?>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="col-md-3 oculto" id="divtraspaso">
                                            <button class="btn btn-sm btn-success btn-rounded" style="margin-top: 32px;" id="btntraspasotecnico" onclick="traspasoTecnico(event)"><i class="fa fa-exchange" aria-hidden="true"></i> Traspaso entre técnicos</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row oculto" id="divproductos">
                                <div class="row">
                                    <div style="width: 47.5%;">
                                        <div class="box box-inverse box-solid">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Técnico A</h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="col-md-12 table-responsive" style="max-height: 400px;">
                                                    <table class="table table-condensed table-striped table-bordered table-sm" id="tabletecnico1">
                                                        <thead class="thead-dark">
                                                            <th>Cant.</th>
                                                            <th>Producto</th>
                                                            <th>Serie</th>
                                                            <th>N° Serie Pro.</th>
                                                            <th>N° Serie SIM.</th>
                                                            <th>Tipo</th>
                                                            <th>Estado</th>
                                                            <th>Acci&oacute;n</th>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="width: 5%;padding-left: 10px;padding-top: 10%;">
                                        <!-- <button type="button" class="btn btn-sm btn-success btn-circle" style="width:65%;color: white;" disabled id="btnpasar1">
                                    <i class="fas fa-long-arrow-alt-right"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-circle" style="width:65%;color: white;margin-top: 10px;" disabled id="btnpasar2">
                                    <i class="fas fa-long-arrow-alt-left"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning btn-circle" style="width:65%;color: white;margin-top: 10px;" id="btnsave">
                                    <i class="fa fa-save"></i>
                                </button> -->
                                    </div>
                                    <div style="width: 47.5%;">
                                        <div class="box box-inverse box-solid">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Técnico B</h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="col-md-12 table-responsive" style="max-height: 400px;">
                                                    <table class="table table-condensed table-striped table-bordered table-sm" id="tabletecnico2">
                                                        <thead class="thead-dark">
                                                            <th>Cant.</th>
                                                            <th>Producto</th>
                                                            <th>Serie</th>
                                                            <th>N° Serie Pro.</th>
                                                            <th>N° Serie SIM.</th>
                                                            <th>Tipo</th>
                                                            <th>Estado</th>
                                                            <th>Acci&oacute;n</th>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row pb-2" id="divbuttons">
                                <div class="col-md-12">
                                    <button type="button" onclick="verPro()" class="btn btn-sm btn-primary">Traspaso Productos</button>
                                    <button type="button" onclick="verKit()" class="btn btn-sm btn-primary">Traspaso Kits GPS</button>
                                </div>
                            </div>
                            <div class="row oculto" id="divpro">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="producto">Producto</label>
                                            <?php echo $opt; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="disponibles">Disponibles</label>
                                            <input type="text" id="disponibles" name="disponibles" class="form-control form-control-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cantidad">Cantidad</label>
                                            <input type="text" id="cantidad" name="cantidad" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" style="margin-top: 32px;" class="btn btn-sm btn-success btn-circle" onclick="agregaratrapaso()" id="btnaddcan"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                    </div>
                                </div>

                            </div>
                            <div class="row oculto" id="divkit">
                                <div class="col-md-12">
                                    <table class="table table-sm table-bordered table-hover" id="tblasociacion">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">N°</th>
                                                <th scope="col">GPS</th>
                                                <th scope="col" nowrap>Serie GPS</th>
                                                <th scope="col" nowrap>SIM</th>
                                                <th scope="col" nowrap>Serie SIM</th>
                                                <th scope="col" nowrap>Bodega</th>
                                                <div id="divsensores"><?= $thsensores ?></div>
                                                <th scope="col" nowrap></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- <tr><td colspan="13" align="center">Sin registros.</td></tr> -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6 oculto" id="tblistcod">
                                        <table class="table table-bordered table-sm">
                                            <thead class="thead-dark">
                                                <th>N° Serie Disponible</th>
                                                <th></th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-sm-6 oculto" id="tablatem">
                                        <table class="table table-bordered table-sm" id="tb_prodoc">
                                            <thead class="thead-dark">
                                                <th class="text-center" width=50>Cantidad</th>
                                                <th>Producto</th>
                                                <th>Series</th>
                                                <th width=50></th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6" style="margin-bottom: 20px;">
                                    <label>Observaciones</label>
                                    <textarea id='observaciones' name='observaciones' class='form-control rznone' rows=5></textarea>
                                </div>
                            </div>
                            <div class="form-group" id="formbutton">
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-success btn-rounded" onclick="guardarTrapaso()">Guardar</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-rounded" onclick="cancelar()">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- fin box-body -->
            </div>
        </div>
    </div>
    <div class="row top20" id="listadodetraspasos">
        <div class="col-md-12">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Listado de Traspasos</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12" id='tblisttras'>
                            <div class="box box-widget">
                                <div class="box-header with-border">
                                    <h3 class="box-title"></h3>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered table-striped table-sm" id="tbtraspasos">
                                        <thead class="thead-dark">
                                            <th>#</th>
                                            <th>Fecha</th>
                                            <th>Bodega</th>
                                            <th>Observaciones</th>
                                            <th>Usuario</th>
                                            <th>Estado Envío</th>
                                            <th>Datos de Courrier</th>
                                            <th class="text-center"></th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 oculto" id="dettras">
                            <div class="box box-widget">
                                <div class="box-header with-border">
                                    <h3 class="box-title"></h3>
                                    <div class="box-tools">
                                        <button type="button" class="btn btn-danger btn-circle-s" onclick="cerrarDetTras()">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="col-md-12" id="formeditar" style="display: none;">
    <input type="hidden" name="" id="valortras" value="">
    <div class="row" style="margin-top:10px;">
        <div class="col-md-12">
            <h3 class="box-title">Editar Traspaso</h3>
        </div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-md-4">
            <label>Fecha</label>
            <input type="date" class="form-control form-control-sm" id="dateedit">
        </div>
        <div class="col-md-4">
            <label>Bodega(Técnico)</label>
            <?= htmlselect('bodegaedit', 'bodegaedit', 'personal', 'per_id', 'per_nombrecorto', '', '', '', 'per_nombrecorto', 'activeTraspaso(1)', '', 'si', 'no', 'no'); ?>
        </div>
        <div class="col-md-4">
            <label>Bodega(Técnico)</label>
            <?= htmlselect('bodegaedit2', 'bodegaedit2', 'personal', 'per_id', 'per_nombrecorto', '', '', 'where per_id <> 26', 'per_nombrecorto', 'cambioedittecnico(this.value,2)', '', 'si', 'no', 'no'); ?>
        </div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-md-6">
            <h3 class="box-title">Técnico A</h3>
        </div>
        <div class="col-md-6">
            <h3 class="box-title">Técnico B</h3>
        </div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-md-6">
            <div class="col-md-12 table-resposive" style="max-height: 600px;">
                <table class="table table-condensed table-striped table-bordered table-sm" id="tabletecnicoedit1">
                    <thead class="thead-dark">
                        <th>Cant.</th>
                        <th>Producto</th>
                        <th>Serie</th>
                        <th>N° Serie Pro.</th>
                        <th>N° Serie SIM.</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acci&oacute;n</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-12 table-resposive" style="max-height: 600px;">
                <table class="table table-condensed table-striped table-bordered table-sm" id="tabletecnicoedit2">
                    <thead class="thead-dark">
                        <th>Cant.</th>
                        <th>Producto</th>
                        <th>Serie</th>
                        <th>N° Serie Pro.</th>
                        <th>N° Serie SIM.</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acci&oacute;n</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-md-6">
            <label>Observaciones</label>
            <textarea class="form-control" id="observacionedit"></textarea>
        </div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-md-12">
            <button class="btn btn-sm btn-rounded btn-success" id="btnactualizaredit">Actualizar</button>
            &nbsp;
            <button class="btn btn-sm btn-rounded btn-danger" onclick="location.reload();">Cancelar</button>
        </div>
    </div>
</div>
<script>
    var temp = [];
    var bod1 = 0;
    var bod2 = 0;

    var dataPerfil = <?php echo $_SESSION['perfil_new']; ?>;
    var traspasosalb = [];
    var opcionTraspaso = 0;
    var listAsoc = [];

    $(document).ready(function() {
        $("#producto,#bodega,#bodega2").chosen({
            width: "100%",
            height: "45px"
        });
        $('#bodega, #bodega2').addClass('form-control-sm');
        $("#btn_ntraspaso").on("click", function() {
            $("#listadodetraspasos").hide();
            $('#formeditar').hide();
            $("#fnuevotraspaso").show();
            $(this).attr("disabled", true);
        });

        if (dataPerfil != 3) {
            //deben ser cualquier menos los externos quienes pueden ver los transpasos
            getTabTraspasos();
            getAllAsociacion();
        }
    });

    function verPro() {
        $('#divpro').show();
        $('#divkit').hide();
        opcionTraspaso = 1;
    }

    function verKit() {
        $('#divkit').show();
        $('#divpro').hide();
        opcionTraspaso = 2;
        getAllAsociacion();
    }

    function getAllAsociacion() {
        if ($.fn.DataTable.isDataTable('#tblasociacion')) {
            $('#tblasociacion').DataTable().destroy();
        }
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'listarAsociacion',
            estado: 2,
            retornar: 'no'
        }, function(data) {
            let fila = '';
            if (data != '' && data != null) {
                data = $.parseJSON(data);
                if (data.data.length > 0) {
                    listAsoc = data.data;

                    $('.tool').tooltip('dispose')
                    $('#tblasociacion tbody').html('');
                    $.each(data.data, function(i, item) {
                        let num = $('#tblasociacion tbody tr').length;

                        let bodega = '';
                        if (parseInt(item.idbodega) == 26) {
                            bodega = 'Bodega principal';
                        } else {
                            bodega = 'Bodega técnico';
                        }

                        let btn = '<span onclick="agregaratrapaso(' + i + ',' + item.id + ',' + item.idgps + ',' + item.seriegps + ')" class="btn btn-sm btn-circle btn-success tool" data-toggle="tooltip" data-placement="top" title="Asignar Kit"><i class="fas fa-plus-circle"></i></span>';
                        let sensores = '';
                        sensores += '<td nowrap style="vertical-align:middle;">' + btn + '</td>';

                        fila += '<tr id="fila_' + i + '"><td>KIT ' + (item.id) + '</td><td>' + item.gps + '</td><td>' + item.seriegps + '</td><td>' + item.accesorio + '</td><td>' + item.serieaccesorio + '</td><td>' + bodega + '</td>' + sensores + '</tr>';
                    });
                }
            }
            $('#tblasociacion tbody').append(fila);
            $('#tblasociacion').DataTable({
                "language": {
                    url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'
                },
                "paging": true,
                "lengthChange": true,
                "lengthMenu": [
                    [20, -1],
                    [20, "Todos"]
                ],
                "pageLength": 20,
                columnDefs: [{
                    targets: "_all",
                    sortable: false
                }],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            $('.tool').tooltip()
        });
    }

    function getTabTraspasos() {

        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getTabTraspasos',
            retornar: 'no'
        }, function(data) {
            //console.log(data);
            datos = $.parseJSON(data);
            filas = "";
            x = 0;

            $.each(datos, function(index, valor) {
                traspasosalb.push({
                    'index': valor.detalle,
                    'sobbra': valor.sobra
                });
                x++;

                var uno = '';
                var dos = '';
                var tres = '';
                var cero = '';
                var txt = '';
                var ftxt = '';
                if (valor.fechatracking == null || valor.fechatracking == '' || valor.fechatracking == undefined) {
                    ftxt = '-';
                } else {
                    ftxt = valor.fechatracking;
                }

                if (valor.idtracking == 0) {
                    uno = '';
                    dos = '';
                    tres = '';
                    cero = 'selected';
                    txt = "<label>" + ftxt + "</label>";
                } else if (valor.idtracking == 1) {
                    uno = 'selected';
                    dos = '';
                    tres = '';
                    cero = '';
                    txt = "<label>" + ftxt + "</label>";
                } else if (valor.idtracking == 2) {
                    uno = '';
                    dos = 'selected';
                    tres = '';
                    cero = '';
                    txt = "<label>" + ftxt + "</label><br><label>" + valor.courrier + "</label><br><label>" + valor.trackingcodigo + "</label><br><label>" + valor.recibetracking + "</label>"
                } else if (valor.idtracking == 3) {
                    uno = '';
                    dos = '';
                    tres = 'selected';
                    cero = '';
                    txt = "<label>" + ftxt + "</label>";
                }

                filas += "<tr id='removetr_" + valor.idtraspaso + "'><td>" + x + "</td><td>" + valor.fecha + "</td><td>" + valor.usr_rec + "</td><td>" + valor.observaciones + "</td><td>" + valor.usr_mod + "</td><td><select id='seltra_" + valor.idtraspaso + "' onchange='sendtracking(" + valor.idtraspaso + ",this.value," + index + ")' class='form-control' ><option value='0' " + cero + ">SELECCIONAR</option><option value='1' " + uno + ">Preparaci&oacute;n</option><option value='2' " + dos + ">En Transito</option><option value='3' " + tres + ">Recepcionado</option></select></td><td id='datc_" + index + "'>" + txt + "</td><td class='text-center' width=150><span class='pointer btn btn-sm btn-warning btn-circle' onclick='Editartraspaso(" + index + "," + valor.idtraspaso + ")'><i class='fa fa-edit' aria-hidden='true'></i></span>&nbsp;<span class='pointer btn btn-sm btn-info btn-circle' onclick='verTraspaso(" + index + "," + valor.idtraspaso + ")'><i class='fa fa-eye' aria-hidden='true'></i></span>&nbsp;<span class='pointer btn btn-sm btn-danger btn-circle' onclick='elitras(" + index + "," + valor.idtraspaso + ")'><i class='fa fa-trash'></i></span></td></tr>";
            });

            $("#tbtraspasos tbody").html(filas);
            $('#tbtraspasos').DataTable({
                "language": {
                    url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'
                },
                "paging": true,
                "lengthChange": true,
                "lengthMenu": [
                    [20, -1],
                    [20, "Todos"]
                ],
                "pageLength": 20,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    }

    function activeTraspaso(opc = 0) {
        if (opc == 0) {
            let tecnico = $('#bodega').val();
            if (tecnico != '') {
                $('#divtraspaso').show();
                productosxternico(tecnico, 1);
            } else {
                $('#divtraspaso').hide();
                $('#formeditar').hide();
            }
        } else {
            let tecnico = $('#bodegaedit').val();
            if (tecnico != '') {
                $('#divtraspaso').show();
                productosxternico(tecnico, 1);
            } else {
                $('#divtraspaso').hide();
                $('#formeditar').hide();
            }
        }
    }

    let productosTecnico1 = [];
    let productosTecnico2 = [];

    function productosxternico(id, opc) {
        if ($.fn.DataTable.isDataTable('#tabletecnico' + opc)) {
            try {
                $('#tabletecnico' + opc).DataTable().destroy();
            } catch (error) {
                console.log(error);
            }
        }
        $('#tabletecnico' + opc + ' tbody').html('<tr><td colspan="10" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
        $('#tabletecnicoedit' + opc + ' tbody').html('<tr><td colspan="10" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'productoxTecniconew',
            idtecnico: id,
            retornar: 'no'
        }, function(data) {
            data = $.parseJSON(data);
            if (opc == 1) {
                productosTecnico1 = data;
            } else {
                productosTecnico2 = data;
            }

            let form = '';
            $.each(data, function(i, item) {
                let estado = '';
                let trcolor = '';
                switch (item.condicion) {
                    case 'BUENO':
                        trcolor = "";
                        estado = "<td class='text-center'><span class='text-success'><i class='fa fa-check' aria-hidden='true'></i></span></td>";
                        break;
                    case 'MALO':
                        trcolor = "danger";
                        estado = "<td class='text-center'><span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span></td>";
                        break;
                    case 'NO IDENTIFICADO':
                        trcolor = "warning";
                        estado = "<td class='text-center'><span class='text-warning'><i class='fa fa-exclamation' aria-hidden='true'></i></span></td>";
                        break;
                }

                var btn = "";
                if (opc == 1) {
                    btn = "<button type='button' class='btn btn-sm btn-success btn-circle' style='width:65%;color: white;' onclick='traspasoalb(" + opc + "," + i + "," + item.idpro + "," + item.idserie + ")' id='btnpasar" + i + "'><i class='fas fa-long-arrow-alt-right'></i></button>";
                } else {
                    btn = "<button type='button' class='btn btn-sm btn-danger btn-circle' style='width:65%;color: white;' onclick='traspasoalb(" + opc + "," + i + "," + item.idpro + "," + item.idserie + ")' id='btndev" + i + "'><i class='fas fa-long-arrow-alt-left'></i></button>";
                }

                var txttieneserie = 'SI';
                if (item.tieneserie == 1) {
                    txttieneserie = 'SI';
                } else {
                    txttieneserie = 'NO';
                }

                form += "<tr style='color:black;' id='id_table" + opc + "_" + i + "' class='" + trcolor + "'><td class='text-center'>1</td><td>" + item.pro_nombre + "</td><td>" + txttieneserie + "</td><td id='ser_" + i + "_" + opc + "'>" + item.serie + "</td><td></td><td>" + item.tipo + "</td>" + estado + "<td>" + btn + "</td></tr>";
            });

            $('#tabletecnico' + opc + ' tbody').html(form);
            $('#tabletecnicoedit' + opc + ' tbody').html(form);

            $('#tabletecnico' + opc).DataTable({
                "language": {
                    url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'
                },
                "paging": false,
                "pageLength": 1000,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false
            });
        });
    }

    function traspasoTecnico(event) {

        event.preventDefault();
        $('#divbodega2').show();
        $('#divpro').hide();
        $('#tblistcod').hide();
        $('#btntraspasotecnico').html('<i class="fa fa-times-circle-o" aria-hidden="true"></i> Cancelar').removeClass('btn-success').addClass('btn-danger').attr('onclick', 'cancelarTraspasoTecnico(event)');
        $('#divproductos').show();
        $('#divbuttons').hide();
        $('#tb_prodoc').hide();
        $('#formbutton').hide();
    }

    function cancelarTraspasoTecnico(event) {
        event.preventDefault();
        $('#divbodega2').hide();
        $('#btntraspasotecnico').html('<i class="fa fa-exchange" aria-hidden="true"></i> Traspaso entre técnicos').removeClass('btn-danger').addClass('btn-success').attr('onclick', 'traspasoTecnico(event)');
        $('#divproductos').hide();
        $('#formeditar').hide();
        $('#bodega').val('');
        $('#divbuttons').show();
        $('#tb_prodoc').show();
        $('#formbutton').show();
    }

    function cancelar() {
        location.reload();
    }

    function buscarProducto() {
        series = {};
        idpro = $("#producto").val();
        var randomNo = Math.floor(Math.random() * 9999999);
        if ($.fn.DataTable.isDataTable('#tblistcod table')) {
            $('#tblistcod table').DataTable().destroy();
        }
        $.get("operaciones.php", {
            numero: '' + randomNo + '',
            operacion: 'getStockProducto',
            producto: idpro,
            retornar: 'no'
        }, function(data) {
            var datos = $.parseJSON(data);
            if (datos.length > 0) {

                $("#disponibles, input[name='disponibles']").val(datos[0]["stock"]);
                if (parseInt(datos[0]['valida']) == 1) {
                    $("input[name='cantidad']").attr("disabled", true);
                    $("#btnaddcan").attr("disabled", true);
                    /* codigos = datos["series"];*/
                    fs = "";
                    $.each(datos, function(index, valor) {
                        var dis = '';
                        $.each(temp, function(i, item) {
                            if (item.id == valor.idserie) {
                                dis = 'checked';
                            }
                        });
                        fs += "<tr><td id='ser_" + valor.idserie + "_" + idpro + "'>" + (valor.codigoserie == undefined ? 'Sin Serie' : valor.codigoserie) + "</td><td width=50 class='text-center'><input type='checkbox' class='cla_" + idpro + "' id='cod" + valor.idserie + "' onclick='agregarProaTras(" + valor.idserie + "," + idpro + ")' " + dis + "></td></tr>";
                    });
                    $("#tblistcod table tbody").html(fs);
                    $('#tblistcod table').DataTable({
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'
                        },
                        "paging": false,
                        "lengthChange": true,
                        /*"lengthMenu": [[20,-1], [20,"Todos"]],
                        "pageLength":20,*/
                        "searching": true,
                        "ordering": true,
                        "info": false,
                        "autoWidth": false
                    });
                    $("#tblistcod").show();
                    $("#tablatem").show();
                } else {

                    $.each(datos, function(index, valor) {
                        temp.push({
                            'id': valor.idserie,
                            'idpro': $("#producto").val()
                        })
                    });

                    $("input[name='cantidad']").attr("disabled", false);
                    $("#btnaddcan").attr("disabled", false);
                    $("#tblistcod").hide();
                    $('#formeditar').hide();
                    $("#disponibles").val(datos.length);
                    $("#cantidad").val(0);
                    $("#tablatem").show();
                }
            } else {
                $("input[name='cantidad']").attr("disabled", false);
                $("#btnaddcan").attr("disabled", false);
                $("#tblistcod").hide();
                $('#formeditar').hide();
                $("#disponibles").val(0);
            }

        });
    }
</script>