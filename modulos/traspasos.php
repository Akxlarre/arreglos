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
                                    <!--<div class="box-tools">
                                <a href="#" class="btn btn-success btn-roundeds" ><i class="fa fa-file-excel-o"></i> Descargar a Excel</a>
                                </div>-->
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
                                            <!--<th class="text-center"></th>-->
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
                                        <!--<a href="#" class="btn btn-success btn-circle-s" ><i class="fa fa-file-excel-o"></i></a>-->
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
    let temp = [];
    let bod1 = 0;
    let bod2 = 0;

    var dataPerfil = <?php echo $_SESSION['perfil_new']; ?>;

    $(document).ready(function() {

        $("#producto").chosen({
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
    window.traspasos;
    let traspasosalb = [];

    function convertDateFormat(string) {
        var info = string.split('/').reverse().join('/');
        return info;
    }

    $("#btnactualizaredit").on("click", function() {
        var tras = $('#valortras').val();
        var observacion = $('#observacionedit').val();
        var fec = convertDateFormat($('#dateedit').val());
        var datos = {
            'tras': tras,
            'observacion': observacion,
            'fecha': fec
        };
        var send = JSON.stringify(datos);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'updatetra',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {

            },
            error: function(respuesta) {

            },
            success: function(respuesta) {
                if (respuesta.logo == 'success') {
                    toastr.success(respuesta.mensaje);
                } else {
                    toastr.error(respuesta.mensaje);
                }
            }
        });
    });

    function cambioedittecnico(idtecnico, opc) {
        var tras = $('#valortras').val();
        Swal.fire({
            title: '\u00BFEstas seguro de cambiar al técnico?',
            text: "Si cambias al técnico automaticamente tomara todas las series del traspaso",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmar'
        }).then((result) => {
            if (result.isConfirmed) {
                var datos = {
                    'opc': opc,
                    'idtecnico': idtecnico,
                    'idtraspaso': tras
                };
                var send = JSON.stringify(datos);
                $.ajax({
                    url: 'operaciones.php',
                    data: {
                        numero: '' + Math.floor(Math.random() * 9999999) + '',
                        operacion: 'cambiartecnico',
                        retornar: 'no',
                        envio: send
                    },
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function(respuesta) {

                    },
                    error: function(respuesta) {

                    },
                    success: function(respuesta) {
                        if (respuesta.logo == 'success') {
                            toastr.success(respuesta.mensaje);
                        } else {
                            toastr.error(respuesta.mensaje);
                        }
                    }
                });
            }
        })
    }

    function Editartraspaso(index, idtraspaso) {
        $('#formeditar').show();
        $('#valortras').val(idtraspaso);
        $('#listadodetraspasos').hide();
        env = {
            'idtraspaso': idtraspaso
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getdettraspasos',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {

            },
            error: function(respuesta) {
                console.log(respuesta);
            },
            success: function(respuesta) {
                if (respuesta.tablauno.length > 0) {
                    $('#tabletecnicoedit1 tbody').html('<tr><td colspan="10" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
                    $('#tabletecnicoedit2 tbody').html('<tr><td colspan="10" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
                    $('#bodegaedit').val(respuesta.usu_id_envia);
                    $('#bodegaedit').attr('disabled', true);
                    $('#bodegaedit2').val(respuesta.usu_id_recibe);
                    $('#dateedit').val(respuesta.tra_fecha);
                    $('#observacionedit').val(respuesta.tra_observacion);

                    let form = '';
                    $.each(respuesta.tablauno, function(i, item) {
                        let estado = '';
                        let trcolor = '';
                        switch (item.ser_condicion) {
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

                        var btn = "<button type='button' class='btn btn-sm btn-success btn-circle' style='width:65%;color: white;' onclick='traspasoalbedit(1," + i + "," + item.pro_id + "," + item.ser_id + "," + idtraspaso + ")' id='btnpasaredit" + i + "'><i class='fas fa-long-arrow-alt-right'></i></button>";


                        var txttieneserie = 'SI';

                        form += "<tr style='color:black;' id='idedit_table1_" + i + "' class='" + trcolor + "'><td class='text-center'>1</td><td>" + item.pro_nombre + "</td><td>" + txttieneserie + "</td><td id='ser_" + i + "_1'>" + item.ser_codigo + "</td><td></td><td>Producto</td>" + estado + "<td>" + btn + "</td></tr>";
                    });
                    $('#tabletecnicoedit1 tbody').html(form);

                    form = '';
                    $.each(respuesta.tablados, function(i, item) {
                        let estado = '';
                        let trcolor = '';
                        switch (item.ser_condicion) {
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

                        var btn = "<button type='button' class='btn btn-sm btn-danger btn-circle' style='width:65%;color: white;' onclick='traspasoalbedit(2," + i + "," + item.pro_id + "," + item.ser_id + "," + idtraspaso + ")' id='btnpasaredit" + i + "'><i class='fas fa-long-arrow-alt-left'></i></button>";

                        var txttieneserie = 'SI';

                        form += "<tr style='color:black;' id='idedit_table2_" + i + "' class='" + trcolor + "'><td class='text-center'>1</td><td>" + item.pro_nombre + "</td><td>" + txttieneserie + "</td><td id='ser_" + i + "_2'>" + item.ser_codigo + "</td><td></td><td>Producto</td>" + estado + "<td>" + btn + "</td></tr>";
                    });
                    $('#tabletecnicoedit2 tbody').html(form);
                } else {
                    $('#tabletecnicoedit1 tbody').html('<tr><td colspan="10" align="center">No hay series asociadas</td></tr>');
                    $('#tabletecnicoedit2 tbody').html('<tr><td colspan="10" align="center">No hay series asociadas</td></tr>');
                }
            }
        });
    }

    function traspasoalbedit(opciontecnico, index, idproducto, idserie, idtraspaso) {

        var tecotro = $('#bodegaedit2').val();
        var bodega1 = $('#bodegaedit').val();

        if (tecotro == '') {
            alert('debes seleccionar una opcion contraria para el traspaso');
        } else {

            var bodega2 = tecotro;
            var datosj = {
                'bodega2': tecotro,
                'opciontecnico': opciontecnico,
                'index': index,
                'idproducto': idproducto,
                'idserie': idserie,
                'idtraspaso': idtraspaso,
                'bodega1': bodega1
            };
            var sendj = JSON.stringify(datosj);

            $.ajax({
                url: 'operaciones.php',
                data: {
                    numero: '' + Math.floor(Math.random() * 9999999) + '',
                    operacion: 'edittraspasoser',
                    retornar: 'no',
                    envio: sendj
                },
                type: 'post',
                dataType: 'json',
                beforeSend: function(respuesta) {

                },
                error: function(respuesta) {
                    console.log(respuesta);

                },
                success: function(respuesta) {
                    if (respuesta.logo == 'success') {
                        toastr.success(respuesta.mensaje);
                        $('#idedit_table' + opciontecnico + '_' + index + '').remove();
                        Editartraspaso(index, idtraspaso);
                        /*if(opciontecnico==1){
                           productosxternico(tecotro,2);

                        }else{
                           productosxternico(bodega1,1);
                        }*/
                    } else {
                        toastr.error(respuesta.mensaje);
                    }
                }
            });
        }
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

    function sendtracking(idtras, valor, index) {

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
            title: '¿Estas seguro de mandar este estado?',
            text: "Esto afectara a todas las series que esta conlleva en el traspaso",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var valida = 1;
                var nsegui = '';
                if (valor == 2) {
                    Swal.fire({
                        title: 'Ingresa datos del Courrier',
                        html: '<label>Numero del Courrier</label><input type="text" id="tranum" class="swal2-input" placeholder="Ej:80055005"><label>Nombre del Courrier</label><input type="text" id="tranom" class="swal2-input" placeholder="Ej:Correos de Chile"><label>Nombre persona que recibe</label><input type="text" id="trarec" class="swal2-input" placeholder="Ej:Juan Ramiro">',
                        confirmButtonText: 'Enviar',
                        focusConfirm: false,
                        preConfirm: () => {
                            const nsegui = Swal.getPopup().querySelector('#tranum').value
                            const csegui = Swal.getPopup().querySelector('#tranom').value
                            const rsegui = Swal.getPopup().querySelector('#trarec').value
                            if (!nsegui && !csegui && !rsegui) {
                                Swal.showValidationMessage(`Debes ingresar datos del Courrier`);
                                $('#seltra_' + idtras).val(0);
                                valida = 0;
                            }
                            return {
                                nsegui: nsegui,
                                csegui: csegui,
                                rsegui: rsegui
                            }
                        }
                    }).then((result) => {
                        /* Swal.fire(`
                            Numero: ${result.value.login}
                        `.trim())*/
                        nsegui = result.value.nsegui;
                        csegui = result.value.csegui;
                        rsegui = result.value.rsegui;
                        $('#seltra_' + idtras).val(2);
                        env = {
                            'idtraspaso': idtras,
                            'valor': valor,
                            'nseguimiento': nsegui,
                            'nombretra': csegui,
                            'recibecou': rsegui
                        };
                        var send = JSON.stringify(env);
                        $.ajax({
                            url: 'operaciones.php',
                            data: {
                                numero: '' + Math.floor(Math.random() * 9999999) + '',
                                operacion: 'enviartracking',
                                retornar: 'no',
                                envio: send
                            },
                            type: 'post',
                            dataType: 'json',
                            beforeSend: function(respuesta) {

                            },
                            error: function(respuesta) {
                                console.log(respuesta);
                            },
                            success: function(respuesta) {
                                if (respuesta.logo == 'success') {
                                    toastr.success(respuesta.mensaje);
                                    $('#datc_' + index).html('<label>' + csegui + '</label><br><label>' + nsegui + '</label><br><label>' + rsegui + '</label>');
                                } else {
                                    toastr.error(respuesta.mensaje);
                                }
                                location.reload();
                            }
                        });
                    })
                } else {
                    nsegui = '';
                    env = {
                        'idtraspaso': idtras,
                        'valor': valor,
                        'nseguimiento': nsegui,
                        'opc': 1
                    };
                    var send = JSON.stringify(env);
                    $.ajax({
                        url: 'operaciones.php',
                        data: {
                            numero: '' + Math.floor(Math.random() * 9999999) + '',
                            operacion: 'enviartracking',
                            retornar: 'no',
                            envio: send
                        },
                        type: 'post',
                        dataType: 'json',
                        beforeSend: function(respuesta) {

                        },
                        error: function(respuesta) {
                            console.log(respuesta);
                        },
                        success: function(respuesta) {
                            if (respuesta.logo == 'success') {
                                toastr.success(respuesta.mensaje);
                            } else {
                                toastr.error(respuesta.mensaje);
                            }
                            location.reload();
                        }
                    });
                }
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                $('#seltra_' + idtras).val(0);
            }
        })
    }

    function elitras(index, idtras) {

        Swal.fire({
            title: '\u00BFEstas seguro de eliminarlo?',
            text: "Este ya no aparecer\u00E1 en la lista",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmar'
        }).then((result) => {
            if (result.isConfirmed) {
                var datos = {
                    'index': index,
                    'id': idtras
                };
                var send = JSON.stringify(datos);
                $.ajax({
                    url: 'operaciones.php',
                    data: {
                        numero: '' + Math.floor(Math.random() * 9999999) + '',
                        operacion: 'eliminartraspaso',
                        retornar: 'no',
                        envio: send
                    },
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function(respuesta) {

                    },
                    error: function(respuesta) {

                    },
                    success: function(respuesta) {
                        if (respuesta) {
                            Swal.fire(
                                'Correcto',
                                respuesta.mensaje,
                                respuesta.logo
                            );
                            $('#removetr_' + idtras).remove();
                        }
                    }
                });
            }
        })
    }

    window.codigos;
    series = {};

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
        temp = []
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
                    //$("#disponibles").val(datos.length);
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
    }detalletraspaso = [];
    temptras = [];
    // var seriesconcatenadas = []; // SE elimino la variable global seriesconcatenadas

    function agregarProaTras(index, idprod) {
        console.log("agregarProaTras - START");
        console.log("agregarProaTras - detalletraspaso before:", JSON.stringify(detalletraspaso));
        console.log("agregarProaTras - index:", index, "idprod:", idprod);

        let temp2 = [];

        if ($.fn.DataTable.isDataTable('#tb_prodoc')) {
            $('#tb_prodoc').DataTable().destroy();
        }
        if ($("#cod" + index + "").is(':checked')) {
            console.log("agregarProaTras - Checkbox is checked");

            temp.push({
                'id': index,
                'idpro': idprod
            });
            //console.log("agregar");
            idpro = $("#producto").val();
            nombrepro = $("#producto option:selected").text();
            var serieText = $("#ser_" + index + '_' + idprod).text(); // Obtiene el texto de la serie
            console.log("agregarProaTras - serieText:", serieText);


            var productoEnDetalle = detalletraspaso.find(item => item.idproducto === idprod);
            console.log("agregarProaTras - productoEnDetalle found:", productoEnDetalle);

            if (productoEnDetalle) {
                console.log("agregarProaTras - Producto already in detalletraspaso, updating series and quantity");
                productoEnDetalle.series.push({
                    'serie': serieText,
                    'ser_id': index
                });
                productoEnDetalle.cantidad++;
            } else {
                console.log("agregarProaTras - Producto NOT found in detalletraspaso, adding new product");
                detalletraspaso.push({
                    'idproducto': idprod,
                    'cantidad': 1,
                    'nombrepro': nombrepro,
                    'series': [{ 'serie': serieText, 'ser_id': index }],
                    'tieneserie': "SI",
                });
            }


        } else {
            console.log("agregarProaTras - Checkbox is NOT checked (deselected)");
            var serieABorrar = $("#ser_" + index + '_' + idprod).text();
            console.log("agregarProaTras - serieABorrar:", serieABorrar);
            $.each(detalletraspaso, function(i, item) {
                if (item.idproducto == idprod) {
                    console.log("agregarProaTras - Found product in detalletraspaso to remove serie from");
                    item.series = item.series.filter(s => s.serie !== serieABorrar);
                    item.cantidad--;
                    console.log("agregarProaTras - cantidad after decrement:", item.cantidad);

                    if (item.cantidad <= 0) {
                        console.log("agregarProaTras - cantidad <= 0, removing product from detalletraspaso");
                        detalletraspaso.splice(i, 1);
                    }
                    return false;
                }
            });

            $.each(temp, function(i, item) {
                if (index != item.id) {
                    temp2.push({
                        'id': item.id,
                        'idpro': item.idpro
                    });
                }
            });

            temp = temp2;
        }

        dpro = "";
        $.each(detalletraspaso, function(index, valor) {
            var seriesconca = '';
            seriesconca = valor.series.map(s => s.serie).join(', ');

            dpro += "<tr id='fila" + valor.idproducto + "'><td>" + valor.cantidad + "</td><td>" + valor.nombrepro + "</td><td>" + seriesconca + "</td><td class='text-center'><button type='button' class='btn btn-danger bnt-sm btn-circle' onclick='quitarDetalle(\"" + valor.idproducto + "\")'><i class='fa fa-trash' aria-hidden='true'></i></button></td></tr>";
        });
        $("#tb_prodoc tbody").html(dpro); // Reemplazamos con .html en lugar de .append
        $('#tb_prodoc').DataTable({
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
        console.log("agregarProaTras - detalletraspaso after:", JSON.stringify(detalletraspaso));
        console.log("agregarProaTras - END");
    }



    function agregaratrapaso(indice = null, id = 0, idgps = 0, _serie = '') {
        console.log("agregaratrapaso - START");
        console.log("agregaratrapaso - detalletraspaso before:", JSON.stringify(detalletraspaso));
        console.log("agregaratrapaso - indice:", indice, "id:", id, "idgps:", idgps, "_serie:", _serie);

        if ($.fn.DataTable.isDataTable('#tb_prodoc')) {
            $('#tb_prodoc').DataTable().destroy();
        }
        if (opcionTraspaso == 1) {
            console.log("agregaratrapaso - opcionTraspaso == 1");
            // codigo=Math.floor(Math.random()*99999);
            cantidad = parseInt($("input[name='cantidad']").val());
            maximo = parseInt($("#disponibles").val());
            if (cantidad > maximo) {
                alert("Cantidad a traspasar supera el máximo disponible");
                return;
            } else {
                idpro = $("#producto").val();
                nombrepro = $("#producto option:selected").text();
            }

            //series[indice] = ({
            //    "id": indice,
            //    "serie": codigos[indice]
            //});

            detalletraspaso[idpro] = ({
                "cantidad": cantidad,
                "tieneserie": (Object.keys(series).length > 0 ? "SI" : "NO"),
                "temp": temp,
                "idpro": idpro,
                "producto": nombrepro,
                "series": [], // Inicializa series como array vacio para nuevos productos
                'ttraspaso': opcionTraspaso
            });

            // $("#tb_prodoc tbody").append("<tr id='fila"+codigo+"'><td>"+cantidad+"</td><td>"+nombrepro+"</td><td class='text-center'><button type='button' class='btn btn-danger btn-circle' onclick='quitarDetalle(\""+codigo+"\")'><i class='fa fa-trash-o' aria-hidden='true'></i></button></td></tr>");
            let contador = 0;
            dpro = "";
            Object.entries(detalletraspaso).forEach(([idpro, detalle]) => {
                // var seriesconca = ''; // Eliminamos esta variable local redundante
                // $.each(seriesconcatenadas, function(index1, valor1) { // Ya no usamos seriesconcatenadas global
                //     if (detalle.idproducto == valor1.idporducto) {
                //         seriesconca += valor1.serie + ',';
                //     }
                // })
                seriesconca = detalle.series.map(s => s.serie).join(', '); // Ahora las series vienen de detalle.series

                dpro += "<tr id='fila" + contador + "'><td align=\"center\">" + detalle.cantidad + "</td><td>" + detalle.producto + "</td><td id='clases_" + contador + "'>" + (seriesconca == '' ? '<span class="badge badge-danger">N/A</span>' : seriesconca) + "</td><td class='text-center'><button type='button' class='btn btn-sm btn-danger btn-circle' onclick='quitarDetalle(\"" + contador + "\")'><i class='fa fa-trash' aria-hidden='true'></i></button></td></tr>";
                contador++;
            });


            $("#tb_prodoc tbody").html(dpro);

            $("#producto").val("");
            $("input[name='cantidad']").val("");
            $("#disponibles, input[name='disponibles']").val("");
        } else {
            console.log("agregaratrapaso - opcionTraspaso != 1 (KIT GPS)");
            let idpro = idgps;
            var randomNo = Math.floor(Math.random() * 9999999);
            let asyn = $.get("operaciones.php", {
                numero: '' + randomNo + '',
                operacion: 'getStockProducto',
                producto: idpro,
                retornar: 'no'
            }, function(data) {
                //console.log(data);
                datos = $.parseJSON(data);
                if (parseInt(datos.tieneserie) == 1) {
                    codigos = datos["series"];
                }
                $.each(codigos, function(i, item) {
                    if (item == _serie) {
                        series[i] = ({
                            "id": i,
                            "serie": codigos[i]
                        });
                    }
                });
                //series[indice]=({"id":indice,"serie":codigos[indice]});
                _serie = '' + _serie;
                detalletraspaso['KIT' + id] = ({
                    "cantidad": 1,
                    "tieneserie": (Object.keys(series).length > 0 ? "SI" : "NO"),
                    "idpro": id,
                    "producto": 'KIT ' + id,
                    "series": series,
                    'ttraspaso': opcionTraspaso
                });

                dpro = "";
                $.each(detalletraspaso, function(index, valor) {
                    dpro += "<tr id='fila" + index + "'><td>" + valor.cantidad + "</td><td>" + valor.producto + "</td><td class='text-center'><button type='button' class='btn btn-sm btn-danger btn-circle' onclick='quitarDetalle(\"" + index + "\")'><i class='fa fa-trash' aria-hidden='true'></i></button></td></tr>";
                });
                $("#tb_prodoc tbody").html(dpro);
            });
        }
        $('#tb_prodoc').DataTable({
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
        console.log("agregaratrapaso - detalletraspaso after:", JSON.stringify(detalletraspaso));
        console.log("agregaratrapaso - END");
    }

    function quitarDetalle(codigo) {
            console.log("quitarDetalle - START");
            console.log("quitarDetalle - codigo:", codigo, typeof codigo);

            // 'codigo' a número usando parseInt()
            codigo = parseInt(codigo); // CONVERSIÓN A NÚMERO AQUÍ

            console.log("quitarDetalle - codigo (after parseInt):", codigo, typeof codigo); // Log para verificar después de la conversión
            console.log("quitarDetalle - detalletraspaso before:", JSON.stringify(detalletraspaso));

            let temp2 = [];
            $.each(temp, function(i, item) {
                if (item.idpro != codigo) {
                    temp2.push({
                        'id': item.id,
                        'idpro': item.idpro
                    });
                } else {
                    $('#cod' + item.id).prop('checked', false);
                }
            });

            temp = temp2;
            console.log("quitarDetalle - temp after update:", temp);

            // Elimina el producto de detalletraspaso directamente por idproducto
            detalletraspaso = detalletraspaso.filter(item => {
                console.log("quitarDetalle - filter item.idproducto:", item.idproducto, typeof item.idproducto, "codigo:", codigo, typeof codigo);
                return item.idproducto !== codigo; // Ahora 'codigo' es número y 'item.idproducto' también
            });
            console.log("quitarDetalle - detalletraspaso after filter:", JSON.stringify(detalletraspaso));
            actualizarTablaDetalle(); // Re-renderiza la tabla para reflejar los cambios
            console.log("quitarDetalle - END");
    }

    function actualizarTablaDetalle() {
        console.log("actualizarTablaDetalle - START");
        console.log("actualizarTablaDetalle - detalletraspaso before:", JSON.stringify(detalletraspaso));
        if ($.fn.DataTable.isDataTable('#tb_prodoc')) {
            $('#tb_prodoc').DataTable().destroy();
        }
        dpro = "";
        $.each(detalletraspaso, function(index, valor) {
            var seriesconca = '';
            seriesconca = valor.series.map(s => s.serie).join(', ');

            dpro += "<tr id='fila" + valor.idproducto + "'><td>" + valor.cantidad + "</td><td>" + valor.nombrepro + "</td><td>" + seriesconca + "</td><td class='text-center'><button type='button' class='btn btn-danger bnt-sm btn-circle' onclick='quitarDetalle(\"" + valor.idproducto + "\")'><i class='fa fa-trash' aria-hidden='true'></i></button></td></tr>";
        });
        $("#tb_prodoc tbody").html(dpro); // Reemplazamos con .html
        $('#tb_prodoc').DataTable({
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
        console.log("actualizarTablaDetalle - detalletraspaso after:", JSON.stringify(detalletraspaso));
        console.log("actualizarTablaDetalle - END");
    }


    function guardarTrapaso() {
        console.log("guardarTrapaso - START");
        console.log("guardarTrapaso - detalletraspaso:", JSON.stringify(detalletraspaso));
        dataTras = {};
        if ($("#bodega").val() != "") {
            dataTras["usuario"] = <?= $_SESSION['cloux_new'] ?>;
            dataTras["fecha"] = convertDateFormat($("input[name='fecha']").val());
            dataTras["bodega"] = $("#bodega").val();
            dataTras["productos"] = temp;
            dataTras["prods"] = []; // Inicializa prods como un array vacio
            console.log("guardarTrapaso - detalletraspaso before prods processing:", JSON.stringify(detalletraspaso));

            detalletraspaso.forEach(function(productoDetalle) {
                let prodData = {
                    idproducto: productoDetalle.idproducto,
                    cantidad: productoDetalle.cantidad,
                    tieneserie: productoDetalle.tieneserie,
                    seriesconcatenadas: productoDetalle.series.map(s => ({ // <-- Crea 'seriesconcatenadas' AHORA
                        ser_id: s.ser_id,       // <-- Incluye 'ser_id'
                        idporducto: productoDetalle.idproducto, // <-- Incluye 'idporducto'
                        serie: s.serie         // <-- Incluye 'serie'
                    }))
                };
                dataTras["prods"].push(prodData);
            });


            console.log("guardarTrapaso - dataTras.prods:", JSON.stringify(dataTras.prods));


            dataTras["observaciones"] = $("textarea[name='observaciones']").val();
        } else {
            Swal.fire(
                'Error',
                'Debes seleccionar un t\u00E9cnico',
                'error'
            );
            return;
        }

        json = JSON.stringify(dataTras);
        console.log("guardarTrapaso - json to send:", json);
        $.post("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'nuevoTraspasonew',
            traspaso: json,
            retornar: 'no'
        }, function(data) {
            console.log("guardarTrapaso - $.post response data:", data);

            if (data.logo != 'error') {
                toastr.success(data.mensaje);
            } else {
                toastr.error(data.mensaje);
            }

            location.reload();
        });
        console.log("guardarTrapaso - END");
    }

    function convertDateFormat(string) {
        var info = string.split('/').reverse().join('/');
        return info;
    }

    function verTraspaso(index, idtras) {

        $("#tblisttras").removeClass("col-sm-12").addClass("col-sm-6");
        $("#dettras .box-title").html("Detalle de traspaso");
        var tabla = '';
        $.each(traspasosalb[index]['index'], function(i, valor) {
            if (i == 0) {
                var envia = valor.usr_env;
                if (envia == undefined || envia == '') {
                    envia = 'Bodega central';
                }

                var thextra = '';
                var tdextra = '';
                var txttrackin = '';
                if (valor.idtracking != 2) {
                    thextra = '';
                    if (valor.idtracking == 1) {
                        txttrackin = 'Preparación';
                    } else if (valor.idtracking == 3) {
                        txttrackin = 'Recepcionado';
                    } else {
                        txttrackin = 'No identificado';
                    }
                } else {
                    thextra = '<th>Codigo Tracking</th>';
                    txttrackin = 'En transito';
                }
                tabla = "Fecha <b>" + valor.fecha + "</b> De " + envia + " para " + valor.usr_rec + " <hr><table class='table table-sm table-bordered table-striped'><thead class='thead-dark'><th>Producto</th><th>Cantidad</th><th>N° Serie Pro.</th><th>N° Serie SIM.</th><th>Tipo</th><th>Tracking</th>" + thextra + "</thead><tbody>"
            }

            if (valor.idtracking != 2) {
                tdextra = "";
                if (valor.idtracking == 1) {
                    txttrackin = 'Preparación';
                } else if (valor.idtracking == 3) {
                    txttrackin = 'Recepcionado';
                } else {
                    txttrackin = 'No identificado';
                }
            } else {
                tdextra = "<td>" + valor.codigotracking + "</td>";
                txttrackin = 'En transito';
            }

            /*var selser = '';
         $.each(traspasosalb[index]['sobbra'],function(ind,valors){
            if(ind==0){
                selser +='<select id="selser_'+i+'" onchange="enviaractser('+valor.codigoid+','+i+','+idtras+')" class="form-control"><option value="'+valor.codigoid+'" selected>'+valor.codigo+'</option>';
            }
            selser +='<option value="'+valors.ser_id+'">'+valors.ser_codigo+'</option>';
        });
         selser +='</select>';*/

            tabla += "<tr><td>" + valor.proveedor + "</td><td>1</td><td>" + valor.codigo + "</td><td></td><td>Producto</td><td>" + txttrackin + "</td>" + tdextra + "</tr>";

        });

        tabla += "</tbody></table>";
        $("#dettras .box-body").html(tabla);
        $("#dettras").show();
        $('html, body').animate({
            scrollTop: 0
        }, 400);
    }

    function enviaractser(idserantoguo, index, idtra) {

        Swal.fire({
            title: '\u00BFEstas seguro de editarlo?',
            text: "La serie que estas cambiando volvera a la bodega que envio y se borraran los datos de courrier",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmar'
        }).then((result) => {
            if (result.isConfirmed) {
                var valsel = $('#selser_' + index).val();
                env = {
                    'idserantoguo': idserantoguo,
                    'index': index,
                    'valsel': valsel,
                    'idtra': idtra
                };
                var send = JSON.stringify(env);
                $.ajax({
                    url: 'operaciones.php',
                    data: {
                        numero: '' + Math.floor(Math.random() * 9999999) + '',
                        operacion: 'actuser',
                        retornar: 'no',
                        envio: send
                    },
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function(respuesta) {

                    },
                    error: function(respuesta) {
                        console.log(respuesta);
                    },
                    success: function(respuesta) {
                        console.log(respuesta);
                        if (respuesta.logo == 'success') {
                            toastr.success(respuesta.mensaje);
                        } else {
                            toastr.error(respuesta.mensaje);
                        }
                    }
                });
            }
        })
    }

    function cerrarDetTras() {
        $("#dettras").hide();
        $('#formeditar').hide();
        $("#tblisttras").removeClass("col-sm-6").addClass("col-sm-12");
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

                /*let tipo = 'Kit GPS';
                let serie1;
                let serie2;
                if(vpxt.kitdetalle.length>0){
                    serie1 = vpxt.kitdetalle[0].seriegps;
                    serie2 = vpxt.kitdetalle[0].seriesim;
                }*/

                /*if(parseInt(vpxt.tipo)==1){
                    tipo   = 'Producto';
                    serie1 = vpxt.serie;
                    serie2 = '';
                }*/

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
                //"lengthChange": true,
                //"lengthMenu": [[20,-1], [20,"Todos"]],
                "pageLength": 1000,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false
            });
        });
    }

    function traspasoalb(opciontecnico, index, idproducto, idserie) {
        if (opciontecnico == 1) {
            var tecotro = $('#bodega2').val();
        } else {
            var tecotro = $('#bodega').val();
        }

        if (tecotro == '') {
            alert('debes seleccionar una opcion contraria para el traspaso');
        } else {

            var bodega1 = $('#bodega').val();
            var bodega2 = $('#bodega2').val();
            var valida = 0;
            if (opciontecnico == 1) {
                if (bodega1 != bod1) {
                    valida = 0;
                    bod1 = bodega1;
                } else {
                    valida = 1;
                }
            } else {
                if (bodega2 != bod2) {
                    valida = 0;
                    bod2 = bodega2;
                } else {
                    valida = 1;
                }
            }
            let come = $('#observaciones').val();
            let seri = $('#ser_' + index + '_' + opciontecnico).text();
            var datosj = {
                'bodega1': bodega1,
                'bodega2': bodega2,
                'idproducto': idproducto,
                'comen': come,
                'valida': valida,
                'opciontecnico': opciontecnico,
                'idserie': idserie,
                'serie': seri
            };
            var sendj = JSON.stringify(datosj);

            $.ajax({
                url: 'operaciones.php',
                data: {
                    numero: '' + Math.floor(Math.random() * 9999999) + '',
                    operacion: 'newupdatetraspaso',
                    retornar: 'no',
                    envio: sendj
                },
                type: 'post',
                dataType: 'json',
                beforeSend: function(respuesta) {

                },
                error: function(respuesta) {
                    console.log(respuesta);
                },
                success: function(respuesta) {
                    if (respuesta.logo == 'success') {
                        toastr.success(respuesta.mensaje);
                        $('#id_table' + opciontecnico + '_' + index + '').remove();
                        if (opciontecnico == 1) {
                            productosxternico(bodega2, 2);
                        } else {
                            productosxternico(bodega1, 1);
                        }

                    } else {
                        toastr.error(respuesta.mensaje);
                    }
                }
            });
        }
    }

    let productoselect1 = null;
    let productoselect2 = null;

    function selectProducto1(index) {
        if (productoselect1 == null) {
            productoselect1 = productosTecnico1[index];
            $('#id_table1_' + index).css({
                'background-color': '#C70039',
                'color': 'white'
            });
            $('#btnpasar1').attr('disabled', false).attr('onclick', 'pasarProductoTecnico1(' + index + ')');
            $('#btnpasar2').attr('disabled', true).attr('onclick', '');
        } else {
            productoselect1 = null;
            $('#id_table1_' + index).css({
                'background-color': '#FFFFFF',
                'color': 'black'
            });
            $('#btnpasar1').attr('disabled', true).attr('onclick', '');
            $('#btnpasar2').attr('disabled', true).attr('onclick', '');
        }
    }

    function pasarProductoTecnico1(index) {
        if ($.fn.DataTable.isDataTable('#tabletecnico2')) {
            $('#tabletecnico2').DataTable().destroy();
        }
        productoselect1 = productosTecnico1[index];
        let cantTable = $('#tabletecnico2 tbody tr').length;
        let estado = '';
        let trcolor = '';
        let form = '';
        switch (productoselect1.estado) {
            case 'BUENO':
                trcolor = "";
                estado = "<td class='text-center'><span class='text-success'><i class='fa fa-check' aria-hidden='true'></i></span></td>";
                break;
            case 'MALO':
                trcolor = "danger";
                estado = "<td class='text-center'><span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span></td>";
                break;
            case 'NO REGISTRADO':
                trcolor = "warning";
                estado = "<td class='text-center'><span class='text-warning'><i class='fa fa-exclamation' aria-hidden='true'></i></span></td>";
                break;
        }

        let serie1;
        let serie2;
        if (productoselect1.kitdetalle.length > 0) {
            serie1 = productoselect1.kitdetalle[0].seriegps;
            serie2 = productoselect1.kitdetalle[0].seriesim;
        }

        let tipo = 'Kit GPS';
        if (parseInt(productoselect1.tipo) == 1) {
            tipo = 'Producto';
            serie1 = productoselect1.serie;
            serie2 = '';
        }
        form += "<tr style='color:black;' onclick='selectProducto2(" + cantTable + ")' id='id_table2_" + cantTable + "' class='" + trcolor + "'><td class='text-center'>" + productoselect1.cantidad + "</td><td>" + productoselect1.producto + "</td><td>" + productoselect1.tieneserie + "</td><td>" + serie1 + "</td><td>" + serie2 + "</td><td>" + tipo + "</td>" + estado + "</tr>";
        $('#tabletecnico2 tbody').append(form);
        $('#tabletecnico2').DataTable({
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
        $('#id_table1_' + index).remove();
        $('#btnpasar1').attr('disabled', true).attr('onclick', '');
        let proTemp = [];
        $.each(productosTecnico1, function(i, item) {
            if (i != index) {
                proTemp[i] = item;
            }
        });
        productosTecnico1 = proTemp;
        proTemp = [];
        productosTecnico2.push(productoselect1);
        productoselect1 = null;

    }

    function selectProducto2(index) {
        if (productoselect2 == null) {
            productoselect2 = productosTecnico2[index];
            $('#id_table2_' + index).css({
                'background-color': '#C70039',
                'color': 'white'
            });
            $('#btnpasar2').attr('disabled', false).attr('onclick', 'pasarProductoTecnico2(' + index + ')');
            $('#btnpasar1').attr('disabled', true).attr('onclick', '');
        } else {
            productoselect2 = null;
            $('#id_table2_' + index).css({
                'background-color': '#FFFFFF',
                'color': 'black'
            });
            $('#btnpasar2').attr('disabled', true).attr('onclick', '');
            $('#btnpasar1').attr('disabled', true).attr('onclick', '');
        }
    }

    function pasarProductoTecnico2(index) {
        productoselect2 = productosTecnico2[index];
        if ($.fn.DataTable.isDataTable('#tabletecnico1')) {
            $('#tabletecnico1').DataTable().destroy();
        }
        let cantTable = $('#tabletecnico1 tbody tr').length;
        let estado = '';
        let trcolor = '';
        let form = '';
        switch (productoselect2.estado) {
            case 'BUENO':
                trcolor = "";
                estado = "<td class='text-center'><span class='text-success'><i class='fa fa-check' aria-hidden='true'></i></span></td>";
                break;
            case 'MALO':
                trcolor = "danger";
                estado = "<td class='text-center'><span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span></td>";
                break;
            case 'NO REGISTRADO':
                trcolor = "warning";
                estado = "<td class='text-center'><span class='text-warning'><i class='fa fa-exclamation' aria-hidden='true'></i></span></td>";
                break;
        }

        let serie1;
        let serie2;
        if (productoselect2.kitdetalle.length > 0) {
            serie1 = productoselect2.kitdetalle[0].seriegps;
            serie2 = productoselect2.kitdetalle[0].seriesim;
        }

        let tipo = 'Kit GPS';
        if (parseInt(productoselect2.tipo) == 1) {
            tipo = 'Producto';
            serie1 = productoselect2.serie;
            serie2 = '';
        }

        form += "<tr style='color:black;' onclick='selectProducto1(" + cantTable + ")' id='id_table1_" + cantTable + "' class='" + trcolor + "'><td class='text-center'>" + productoselect2.cantidad + "</td><td>" + productoselect2.producto + "</td><td>" + productoselect2.tieneserie + "</td><td>" + serie1 + "</td><td>" + serie2 + "</td><td>" + tipo + "</td>" + estado + "</tr>";

        $('#tabletecnico1 tbody').append(form);
        $('#tabletecnico1').DataTable({
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
        $('#id_table2_' + index).remove();
        $('#btnpasar2').attr('disabled', true).attr('onclick', '');
        // let proTemp = [];
        // $.each(productosTecnico2,function(i,item){
        //     if(i!=index){
        //         proTemp[i] = item;
        //     }
        // });
        // productosTecnico2 = proTemp;
        // proTemp = [];
        productosTecnico1.push(productoselect2);
        productoselect2 = null;


    }

    $('#btnsave').on('click', function() {
        let pro1 = JSON.stringify(productosTecnico1);
        let pro2 = JSON.stringify(productosTecnico2);
        let bod1 = $('#bodega').val();
        let bod2 = $('#bodega2').val();
        let come = $('#observaciones').val();
        $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'updateProductosxTecnico',
                id1: bod1,
                id2: bod2,
                prod1: pro1,
                prod2: pro2,
                observaciones: come,
                retornar: 'no'
            }, function(data) {
                if (data != '' && data != null) {
                    data = $.parseJSON(data);
                    if (data.status == 'OK') {
                        toastr.success('Productos guardados exitosamente.');
                        location.reload();
                    } else {
                        toastr.error('Error al guardar productos.');
                    }
                }
            })
            .fail(function(error) {
                toastr.error(error);
            });
    });



    let listAsoc = [];

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
            if (data != '' && data != null) {
                data = $.parseJSON(data);
                if (data.data.length > 0) {
                    listAsoc = data.data;
                    let fila = '';
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
                        // btn += ' <span onclick="editarItem('+i+','+item.id+')" class="btn btn-sm btn-circle btn-warning tool" data-toggle="tooltip" data-placement="top" title="Editar asociación"><i class="fa fa-edit"></i></span>';
                        // btn += ' <span onclick="borrarItem('+i+','+item.id+')" class="btn btn-sm btn-circle btn-danger tool" data-toggle="tooltip" data-placement="top" title="borrar asociación"><i class="fa fa-trash"></i></span>';

                        let sensores = '';
                        // $.each(item.sensores,function(index, sensor){
                        //     sensores += '<td id="sensor_'+sensor.id+'_'+item.id+'" nowrap style="vertical-align:middle;" align="center">'+(sensor.estado==null?'<span style="cursor:pointer;" class="badge badge-danger">Sin transmisión</span>':sensor.estado==''?'<span style="cursor:pointer;" class="badge badge-danger">Sin transmisión</span>':parseInt(sensor.estado)==1?'<span style="cursor:pointer;" class="badge badge-success">'+sensor.estado1+'</span>':parseInt(sensor.estado)==0?'<span style="cursor:pointer;" class="badge badge-danger">Sin transmisión</span>':'<span style="cursor:pointer;" class="badge badge-danger">'+sensor.estado2+'</span>')+'</td>';
                        // });
                        sensores += '<td nowrap style="vertical-align:middle;">' + btn + '</td>';

                        fila += '<tr id="fila_' + i + '"><td>KIT ' + (item.id) + '</td><td>' + item.gps + '</td><td>' + item.seriegps + '</td><td>' + item.accesorio + '</td><td>' + item.serieaccesorio + '</td><td>' + bodega + '</td>' + sensores + '</tr>';
                    });
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
                } else {
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
                }
            } else {
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
            }
        });
    }

    let opcionTraspaso = 0;

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
</script>