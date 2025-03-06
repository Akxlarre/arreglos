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
<link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<style>
    .expand-btn {
        width:65%;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2em; /* Aumenta la visibilidad del icono */
    }

    /* Ajusta el tamaño del icono dentro del botón */
    .expand-btn i {
        font-size: 1.2em;
    }
    /* Se asegura que las tablas sean desplazables en pantallas pequeñas */
    .table-responsive {
        overflow-x: auto;
    }

    /* Ajustes de margen en dispositivos pequeños */
    @media (max-width: 768px) {
        .mt-2 {
            margin-top: 0.75rem !important;
        }
        .mt-3 {
            margin-top: 1rem !important;
        }
    }

</style>
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
<!-- modal de busqueda historial de traspasos por serie -->
<div id="modal_busqueda_serie" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buscar traspasos por Serie</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="inputSerieBusqueda">Serie</label>
          <input type="text" class="form-control" id="inputSerieBusqueda" placeholder="Ingrese la serie">
        </div>
        <button type="button" class="btn btn-primary" onclick="buscarTraspasosPorSerie()">Buscar</button>
        <hr>
        <div id="resultadoBusquedaSerie">
          <!-- Aquí se mostrarán los resultados -->
        </div>
      </div>
    </div>
  </div>
</div>
<!-- fin modal de busqueda historial de traspasos por serie -->
<section class="content">
    <div class="row submenu">
        <div class="col-md-12" style="padding: 10px;">
            <button type='button' class="btn btn-success btn-rounded" id="btn_ntraspaso"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Traspaso</button>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal_busqueda_serie">
                Buscar traspasos por Serie
            </button>
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
                                    <button type="button" id="btnVerPro" onclick="verPro()" class="btn btn-sm btn-primary" disabled>Traspaso Productos</button>
                                    <!-- desactivado por el momento -->
                                    <!-- <button type="button" onclick="verKit()" class="btn btn-sm btn-primary">Traspaso Kits GPS</button> -->
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
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label for="disponibles">Disponibles</label>
                                            <input type="text" id="disponibles" name="disponibles" class="form-control form-control-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-2">
                                            <label for = "serieBuscar">Buscar Serie</label>                                                                 <!--  Evita el envío del formulario al presionar Enter -->
                                            <input type="text" id="serieBuscar" class="form-control" placeholder="Buscar serie..." disabled oninput="filtrarEnVivo()" onkeydown="if(event.keyCode === 13){ return false; }" style="height: calc(2.25rem - 5px);" /> 
                                        </div>
                                    </div>

                                    <!-- Se utilizara otra interfaz para agregar productos -->
                                <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cantidad">Cantidad</label>
                                            <input type="text" id="cantidad" name="cantidad" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" style="margin-top: 32px;" class="btn btn-sm btn-success btn-circle" onclick="agregaratrapaso()" id="btnaddcan"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                    </div>-->
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
                                    <!-- Tabla para productos CON serie -->
                                    <div class="col-sm-6 oculto" id="tblistcod">
                                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                            <table class="table table-bordered table-sm">
                                                <thead class="thead-dark">
                                                    <th>N° Serie Disponible</th>
                                                    <th></th>
                                                </thead>
                                                <tbody id="tablaSeriesBody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Tabla para productos SIN serie -->
                                    <div class="col-sm-6 oculto" id="tblistcod_sin_serie">
                                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                            <table class="table table-bordered table-sm">
                                                <thead class="thead-dark">
                                                    <th>Descripción</th>
                                                    <th>Cantidad</th>
                                                    <th>Acción</th>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 oculto d-flex flex-column" id="tablatem" style="height: 500px; display: none !important;">
                                        <!-- Contenedor de la tabla -->
                                        <div class="table-responsive flex-grow-1" style="overflow-y: auto;">
                                            <table class="table table-bordered table-sm" id="tb_prodoc">
                                            <thead class="thead-dark">
                                                <tr>
                                                <th class="text-center" width="50">Cantidad</th>
                                                <th>Producto</th>
                                                <th>Series</th>
                                                <th width="50">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Filas generadas dinámicamente -->
                                            </tbody>
                                            </table>
                                        </div>

                                        <!-- Sección Observaciones y Botones, anclada al final con flex -->
                                        <div class="row mt-auto" style="background: #fff; border-top: 1px solid #ddd;">
                                            <div class="col-12" id="formobservaciones">
                                            <div class="form-group">
                                                <label>Observaciones</label>
                                                <textarea id="observaciones" name="observaciones" class="form-control rznone" rows="3"></textarea>
                                            </div>
                                            </div>
                                            <div class="col-12 text-center" id="formbutton" style="margin-bottom: 10px;">
                                            <button type="button" class="btn btn-success btn-rounded" onclick="guardarTrapaso()">Guardar</button>
                                            &nbsp;&nbsp;
                                            <button type="button" class="btn btn-danger btn-rounded" onclick="cancelar()">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
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
<div class="container-fluid">
  <div class="row" id="formeditar" style="display: none;">
    
    <div class="col-12">
      <input type="hidden" id="valortras" value="">
    </div>

    <div class="col-12 mt-2">
      <h3 class="box-title">Editar Traspaso</h3>
    </div>

    <!-- Fila: Fecha y Bodegas -->
    <div class="col-md-4 col-12 mt-2">
      <label>Fecha</label>
      <input type="date" class="form-control form-control-sm" id="dateedit">
    </div>
    <div class="col-md-4 col-12 mt-2">
      <label>Bodega(Técnico)</label>
      <?= htmlselect('bodegaedit', 'bodegaedit', 'personal', 'per_id', 'per_nombrecorto', '', '', '', 'per_nombrecorto', 'activeTraspaso(1)', '', 'si', 'no', 'no'); ?>
    </div>
    <div class="col-md-4 col-12 mt-2">
      <label>Bodega(Técnico)</label>
      <?= htmlselect('bodegaedit2', 'bodegaedit2', 'personal', 'per_id', 'per_nombrecorto', '', '', 'where per_id <> 26', 'per_nombrecorto', 'cambioedittecnico(this.value,2)', '', 'si', 'no', 'no'); ?>
    </div>

    <!-- Sección Técnico A  -->
    <div class="col-lg-6 col-md-12 col-12 mt-3">
      <h3 class="box-title">Técnico A</h3>
      <div class="table-responsive" style="max-height: 600px;">
        <table class="table table-condensed table-striped table-bordered table-sm" id="tabletecnicoedit1">
          <thead class="thead-dark">
            <tr>
              <th>Cant.</th>
              <th>Producto</th>
              <th>Serie</th>
              <th>Tipo</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <!-- Sección Técnico B  -->
    <div class="col-lg-6 col-md-12 col-12 mt-3 mt-lg-3">
      <h3 class="box-title">Técnico B</h3>
      <!-- Corrige la clase a .table-responsive -->
      <div class="table-responsive" style="max-height: 600px;">
        <table class="table table-condensed table-striped table-bordered table-sm" id="tabletecnicoedit2">
          <thead class="thead-dark">
            <tr>
              <th>Cant.</th>
              <th>Producto</th>
              <th>Serie</th>
              <th>Tipo</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <!-- Observaciones y Botones (en la parte inferior, col-12 para ocupar todo el ancho) -->
    <div class="col-12 mt-3">
      <label>Observaciones</label>
      <textarea class="form-control" id="observacionedit"></textarea>
    </div>

    <div class="col-12 mt-3 text-center">
      <button class="btn btn-sm btn-rounded btn-success" id="btnactualizaredit">Actualizar</button>
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

    /**
     * Genera la fila resumen de cada grupo (producto).
     * @param {String} product - Nombre del producto.
     * @param {Number} count - Cantidad de items.
     * @param {String} serieDisplay - Serie a mostrar en la columna "Serie".
     * @param {String} actionBtn - Botón de acción.
     * @param {String} expandBtn - Botón de expansión (si aplica).
     * @param {Number} idx - Índice del grupo.
     */
    function createSummaryRow(product, count, serieDisplay, actionBtn, expandBtn, idx) {
        console.log('createSummaryRow', product, count, serieDisplay, actionBtn, expandBtn, idx);
        return (
            `<tr id="group_${idx}">
                <td class="text-center">${count}</td>
                <td>${product}</td>
                <td>${serieDisplay}</td>
                <td>Producto</td>
                <td>${expandBtn} ${actionBtn}</td>
            </tr>`
        );
    }

// Función para generar el botón de expansión 
    function createExpandButton(idx, direction) {
            console.log("createExpandButton => idx:", idx, "direction:", direction);
            let colorClass = direction === 1 ? "btn-success" : "btn-danger";
            const btnHTML = `<button type="button" class="btn btn-sm ${colorClass} expand-btn expand w-100 mx-0.5"
                                data-target="detail_${direction}_${idx}" 
                                data-state="collapsed" 
                                aria-label="Expandir detalles"
                                title="Mostrar detalles">
                                <i class="fas fa-list"></i>
                            </button>`;
            console.log("Button HTML generated:", btnHTML);
            return btnHTML;
        }

    $(document).on('click', '.expand', function(e) {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        const target = $btn.data('target');
        const detailRow = $('#' + target);
        
        // Usamos el estado almacenado en el botón; por defecto, es 'collapsed'
        const currentState = $btn.data('state') || 'collapsed';
        console.log("Estado actual del botón:", currentState, "para target:", target);

        if (currentState === 'expanded') {
            // Si ya está expandido, lo colapsamos
            detailRow.css('display', 'none');
            $btn.data('state', 'collapsed')
                .html('<i class="fas fa-list"></i>')
                .attr('aria-label', 'Expandir detalles');
            console.log("Detail row hidden, display ahora:", detailRow.css('display'));
        } else {
            // Si está colapsado, lo mostramos como table-row
            detailRow.css('display', 'table-row');
            $btn.data('state', 'expanded')
                .html('<i class="fas fa-list-alt"></i>')
                .attr('aria-label', 'Ocultar detalles');
            console.log("Detail row shown, display ahora:", detailRow.css('display'));
        }
    });



    // Función para generar la fila de detalle para productos con serie y más de un registro
    function createDetailRow(idx, items, idtraspaso, direction) {
        console.log("createDetailRow => idx:", idx, "direction:", direction, "items:", items);
        let rows = items.map(item => {
            let btn = `<button type="button" class="btn btn-sm btn-${direction === 1 ? 'success' : 'danger'} btn-circle"
                            style="width:65%; color: white;"
                            onclick="traspasoalbedit(${direction}, ${idx}, ${item.pro_id}, ${item.ser_id}, ${idtraspaso})"
                            aria-label="Transferir">
                        <i class="fas fa-long-arrow-alt-${direction === 1 ? 'right' : 'left'}"></i>
                        </button>`;
            return `<tr>
                    <td>${item.ser_codigo || "Sin serie"}</td>
                    <td>Producto</td>
                    <td>${btn}</td>
                    </tr>`;
        }).join('');
        
        const detailRowHTML = `<tr class="detail-row" id="detail_${direction}_${idx}" style="display:none;">
                                <td colspan="5">
                                    <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                        <th>Serie</th>
                                        <th>Tipo</th>
                                        <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${rows}
                                    </tbody>
                                    </table>
                                </td>
                                </tr>`;
        console.log("Detail row HTML generated:", detailRowHTML);
        return detailRowHTML;
    }

    
    // Función para procesar los datos y generar la estructura de cada tabla
    function procesarDatos(data, idtraspaso, direction) {
        let groups = {};

        // Agrupar productos por nombre
        $.each(data, function(i, item) {
            let prod = item.pro_nombre;
            if (!groups[prod]) {
                groups[prod] = [];
            }
            groups[prod].push(item);
        });

        // Ordenar productos en el orden especificado
        let sortedGroups = Object.entries(groups).map(([product, items]) => ({
            product: product,
            items: items,
            tiene_serie: items[0].tiene_serie, // "SI" o "NO"
            count: items.length // Cantidad de artículos del mismo producto
        })).sort((a, b) => {
            // 1️⃣ Primero los productos con serie que tienen varios artículos (se desglosan)
            if (a.tiene_serie === "SI" && a.count > 1) return -1;
            if (b.tiene_serie === "SI" && b.count > 1) return 1;

            // 2️⃣ productos con serie que solo tienen un artículo
            if (a.tiene_serie === "SI" && a.count === 1) return -1;
            if (b.tiene_serie === "SI" && b.count === 1) return 1;

            // 3️⃣  productos sin serie
            return 0;
        });

        let form = '';
        let idx = 0;

        sortedGroups.forEach(group => {
            let product = group.product;
            let items = group.items;
            let count = group.count;
            let first = items[0];

            // Determinar el contenido de la columna "Serie"
            let serieDisplay = first.tiene_serie === "NO" ? "Sin serie" : (count > 1 ? "-" : first.ser_codigo);

            let expandBtn = "";
            let actionBtn = "";

            if (first.tiene_serie === "NO") {
                // Para productos sin serie, solo se muestra el botón de traspaso
                actionBtn = `
                    <button type="button" class="btn btn-sm btn-${direction === 1 ? 'success' : 'danger'} btn-circle"
                            style="width:65%; color: white;"
                            onclick="traspasoalbedit(${direction}, ${idx}, ${first.pro_id}, ${first.ser_id}, ${idtraspaso})"
                            aria-label="Transferir producto">
                        <i class="fas fa-long-arrow-alt-${direction === 1 ? 'right' : 'left'}"></i>
                    </button>`;
            } else {
                // Para productos con serie
                if (count > 1) {
                    // Si hay más de una serie, mostramos el botón de detalles (expandir)
                    expandBtn = createExpandButton(idx, direction);
                } else {
                    // Si hay solo una serie, mostramos el botón de traspaso
                    actionBtn = `
                        <button type="button" class="btn btn-sm btn-${direction === 1 ? 'success' : 'danger'} btn-circle"
                                style="width:65%; color: white;"
                                onclick="traspasoalbedit(${direction}, ${idx}, ${first.pro_id}, ${first.ser_id}, ${idtraspaso})"
                                aria-label="Transferir producto">
                            <i class="fas fa-long-arrow-alt-${direction === 1 ? 'right' : 'left'}"></i>
                        </button>`;
                }
            }

            form += createSummaryRow(product, count, serieDisplay, actionBtn, expandBtn, idx);

            // Si el producto tiene serie y hay más de uno, generamos la fila de detalle
            if (first.tiene_serie === "SI" && count > 1) {
                form += createDetailRow(idx, items, idtraspaso, direction);
            }
            idx++;
        });

        return form;
    }

    // Función para cargar los traspasos y generar las tablas
    function Editartraspaso(index, idtraspaso) {
        $('#formeditar').show();
        $('#valortras').val(idtraspaso);
        $('#listadodetraspasos').hide();
        let env = { 'idtraspaso': idtraspaso };
        let send = JSON.stringify(env);

        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999),
                operacion: 'getdettraspasos',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function() {},
            error: function(respuesta) {
                console.log(respuesta);
            },
            success: function(respuesta) {
                if (respuesta.tablauno.length > 0) {
                    $('#tabletecnicoedit1 tbody, #tabletecnicoedit2 tbody').html(
                        '<tr><td colspan="5" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>'
                    );

                    $('#bodegaedit').val(respuesta.usu_id_envia).attr('disabled', true);
                    $('#bodegaedit2').val(respuesta.usu_id_recibe);
                    $('#dateedit').val(respuesta.tra_fecha);
                    $('#observacionedit').val(respuesta.tra_observacion);

                    // Procesar datos para Técnico A
                    let form1 = procesarDatos(respuesta.tablauno, idtraspaso, 1);
                    $('#tabletecnicoedit1 tbody').html(form1);

                    // Procesar datos para Técnico B
                    let form2 = procesarDatos(respuesta.tablados, idtraspaso, 2);
                    $('#tabletecnicoedit2 tbody').html(form2);
                } else {
                    $('#tabletecnicoedit1 tbody, #tabletecnicoedit2 tbody').html(
                        '<tr><td colspan="5" align="center">No hay series asociadas</td></tr>'
                    );
                }
            }
        });
    }

    function traspasoalbedit(opciontecnico, index, idproducto, idserie, idtraspaso) {
        console.log('traspasoalbedit', opciontecnico, index, idproducto, idserie, idtraspaso);
        let tecotro = $('#bodegaedit2').val();
        console.log('tecotro', tecotro);
        let bodega1 = $('#bodegaedit').val();
        console.log('bodega1', bodega1);
        if (tecotro === '') {
            alert('Debes seleccionar una opción contraria para el traspaso');
        } else {
            let datosj = {
                'bodega2': tecotro,
                'opciontecnico': opciontecnico,
                'index': index,
                'idproducto': idproducto,
                'idserie': idserie,
                'idtraspaso': idtraspaso,
                'bodega1': bodega1
            };
            let sendj = JSON.stringify(datosj);
            $.ajax({
                url: 'operaciones.php',
                data: {
                    numero: '' + Math.floor(Math.random() * 9999999),
                    operacion: 'edittraspasoser',
                    retornar: 'no',
                    envio: sendj
                },
                type: 'post',
                dataType: 'json',
                beforeSend: function() {},
                error: function(respuesta) {
                    console.log(respuesta);
                },
                success: function(respuesta) {
                    if (respuesta.logo === 'success') {
                        toastr.success(respuesta.mensaje);
                        // Se refresca la tabla actualizando el detalle
                        Editartraspaso(index, idtraspaso);
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

    function mostrarTablaTemporal() {
        $("#tablatem").removeClass("oculto").show();
    }
    function ocultarTablaTemporal() {
        $("#tablatem").addClass("oculto").hide();
    }

    let productosConSerie = [];
    // Función que renderiza la tabla de series a partir de una lista de productos
    function renderizarTabla(lista,idpro) {
        const tbody = $("#tablaSeriesBody");
        tbody.empty();
        
        if (lista.length === 0) {
            tbody.append("<tr><td colspan='2' class='text-center'>No se encontraron resultados</td></tr>");
            return;
        }
        
        lista.forEach(producto => {
            let fila = `
                <tr id="fila_${producto.idserie}">
                    <td>${producto.codigoserie}</td>
                    <td>
                        <button type="button" class="btn btn-success w-100" onclick="agregarProaTras(${producto.idserie}, ${idpro}, '${producto.codigoserie}')">
                            <i class="fas fa-long-arrow-alt-right"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(fila);
        });
    }

    // Función de filtrado en vivo: se llama cada vez que se escribe en el input de búsqueda
    function filtrarEnVivo() {
        let texto = $("#serieBuscar").val().trim().toLowerCase();
        let idpro = $("#producto").val();
        // Obtén los disponibles, excluyendo los ya agregados (que están en traspasados)
        let disponibles = productosConSerie.filter(item =>
            traspasados.indexOf(item.codigoserie) === -1
        );
        if (!texto) {
            renderizarTabla(disponibles, idpro);
            return;
        }
        let filtrados = disponibles.filter(item =>
            item.codigoserie.toLowerCase().includes(texto)
        );
        renderizarTabla(filtrados, idpro);
    }

    let productosCache = null; // Aquí se guardará la respuesta del servidor 
    let traspasados = []; // Aquí se guardarán los productos ya traspasados

    function buscarProducto() {
        let idpro = $("#producto").val();

         // Verifica si el botón "Traspaso entre técnicos" ha sido presionado
        let btnTraspaso = $("#btntraspasotecnico");
        let bodega = $("#bodega").val(); 

        // Si el botón no ha sido presionado Y la bodega de destino NO está vacía, usar bodega 26 que es la bodega principal
        if (!btnTraspaso.hasClass("active")) {
            
            bodega = 26;
        } 
        

        if ($.fn.DataTable.isDataTable('#tblistcod')) {
            $('#tblistcod').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#tblistcod_sin_serie')) {
            $('#tblistcod_sin_serie').DataTable().destroy();
        }   

        // Si ya tenemos cache para este producto, usamos esa información
        if (productosCache && productosCache.idpro === idpro) {
            // Filtrar los productos excluyendo los que ya están en 'traspasados'
            let datosFiltrados = productosCache.data.filter(producto =>
                traspasados.indexOf(producto.codigoserie) === -1
            );
            productosConSerie = datosFiltrados.slice(); // Guardar copia de los productos con serie para filtrado en vivo
            renderizarTabla(datosFiltrados, idpro);
            $("#tblistcod").show();
            $("#tblistcod_sin_serie").hide();
        }else {
            let randomNo = Math.floor(Math.random() * 9999999);
            $.get("operaciones.php", {
                numero: '' + randomNo + '',
                operacion: 'getStockProducto',
                bodega : bodega,
                producto: idpro,
                retornar: 'no'
                }
                , function(data) {
            
                let datos = $.parseJSON(data);
                // Guarda en caché la respuesta
                productosCache = { idpro: idpro, data: datos };
                // Filtra excluyendo los códigos ya traspasados
                let datosFiltrados = datos.filter(producto =>
                    traspasados.indexOf(producto.codigoserie) === -1
                );
                //tabla de productos con serie
                let tabla = $("#tblistcod tbody");
                tabla.empty(); // Limpiar tabla antes de agregar nuevos datos
                //tabla de productos sin serie
                let tablaSinSerie = $("#tblistcod_sin_serie tbody");
                tablaSinSerie.empty();

                if (datos.length > 0) {
                    let stockDisponible = parseInt(datos[0]["stock"]) || 0;   
                    let requiereSerie = parseInt(datos[0]['valida']) === 1;
                    $("#disponibles").val(stockDisponible);

                    if (requiereSerie && stockDisponible > 0) {
                        $("#serieBuscar").prop("disabled", false);
                    } else {
                        $("#serieBuscar").prop("disabled", true).val("");
                    }
  

                    if (requiereSerie) {

                        datosFiltrados.forEach((producto) => {
                            temp.push({
                                id: producto.idserie,
                                idpro: idpro,
                                codigoserie: producto.codigoserie
                            });
                        });
                        productosConSerie = datosFiltrados.slice(); // Guardar copia de los productos con serie para filtrado en vivo
                        // Renderiza la tabla completa (todos los datos)
                        renderizarTabla(datosFiltrados,idpro);

                        $("#tblistcod").show();
                        $("#tblistcod_sin_serie").hide();

                    } else {
                        let fila = `
                            <tr>
                                <td class="text-center"><strong>Producto sin serie</strong></td>
                                <td>
                                    <input type="number" id="cantidad" class="form-control" min="1" max="${stockDisponible}" value="1"
                                        oninput="validarCantidad(this, ${stockDisponible})">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success" onclick="agregarSinSerie(${idpro})">➕ Agregar</button>
                                </td>
                            </tr>
                        `;

                        tablaSinSerie.append(fila);

                        $("#tblistcod_sin_serie").show();
                        $("#tblistcod").hide(); // Ocultar tabla de productos con serie
                        $("#serieBuscar").prop("disabled", true).val("");
                    }
                } else {
                    // Si no hay stock disponible para el producto se da el valor 0 al input de cantidad
                    $("#disponibles").val(0);
                    $("#serieBuscar").prop("disabled", true).val("");
                    tabla.append(`
                        <tr>
                            <td colspan="3" class="text-center">No hay stock disponible</td>
                        </tr>
                    `);
                    tablaSinSerie.append(`
                        <tr>
                            <td colspan="3" class="text-center">No hay stock disponible</td>
                        </tr>
                    `);
                }
            });
        }
    }


    // Validar la cantidad ingresada por el usuario para productos sin serie 
    function validarCantidad(input, max) {
        if (input.value < 1) {
            input.value = 1;
        }
        if (input.value >= max) {
            input.value = max;
        }
    }



    function actualizarTablaTraspaso() {
    // Agrupar ítems por producto
        let groups = agruparDetalleTraspaso();
        let groupArray = transformarGrupos(groups);
        
        // Ordenar grupos 
        groupArray.sort(ordenarGrupos);
        
        // Determinar si se debe mostrar la columna tracking
        let hasTrackingCode = detalletraspaso.some(it => it.idtracking == 2);
        
        let dpro = "";
        let idx = 0;
        groupArray.forEach(group => {
            if (group.tieneSerie === "SI") {
                dpro += construirFilaConSerie(group, idx, hasTrackingCode);
            } else {
                dpro += construirFilaSinSerie(group, idx);
            }
            idx++;
        });
        
        $("#tb_prodoc tbody").html(dpro);
        asignarEventoExpand();
    }

    function agruparDetalleTraspaso() {
        let groups = {};
        detalletraspaso.forEach(item => {
            let key = item.nombre; // se agrupa por nombre
            if (!groups[key]) groups[key] = [];
            groups[key].push(item);
        });
        return groups;
    }

    function transformarGrupos(groups) {
        return Object.entries(groups).map(([product, items]) => {
            // Si el primer ítem tiene codigoserie no vacía se considera que tiene serie
            let tieneSerie = (items[0].codigoserie && items[0].codigoserie.trim() !== "") ? "SI" : "NO";
            let totalQuantity = items.reduce((sum, item) => sum + (item.cantidad || 1), 0);
            return { product, items, count: items.length, totalQuantity, tieneSerie };
        });
    }

    function ordenarGrupos(a, b) {
        if (a.tieneSerie === "SI" && a.count > 1 && !(b.tieneSerie === "SI" && b.count > 1)) return -1;
        if (b.tieneSerie === "SI" && b.count > 1 && !(a.tieneSerie === "SI" && a.count > 1)) return 1;
        if (a.tieneSerie === "SI" && a.count === 1 && b.tieneSerie === "NO") return -1;
        if (b.tieneSerie === "SI" && b.count === 1 && a.tieneSerie === "NO") return 1;
        return 0;
    }

    function construirFilaConSerie(group, idx, hasTrackingCode) {
        let product = group.product,
            items = group.items,
            count = group.count,
            totalQuantity = group.totalQuantity;
        let first = items[0];
        let serieDisplay = (count > 1) ? "-" : (first.codigoserie || "Sin serie");
        let row = "";
        
        if (count > 1) {
            // Si hay más de una serie, se muestra un botón de expansión
            let expandBtn = createExpandButton(idx, 2); // 2 para color rojo danger
            row += `
                <tr id="group_${idx}">
                    <td class="text-center">${totalQuantity}</td>
                    <td>${product}</td>
                    <td>${serieDisplay}</td>
                    <td>${expandBtn}</td>
                </tr>`;
            // Fila de detalle con cada ítem y su botón de eliminar
            let detailId = "detail_2_" + idx;
            row += `<tr class="detail-row" id="${detailId}" style="display:none;">
                        <td colspan="4">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>N° Serie</th>
                                        <th>Cantidad</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>`;
            items.forEach(item => {
                row += `
                    <tr>
                        <td>${item.codigoserie || "Sin serie"}</td>
                        <td class="text-center">${item.cantidad || 1}</td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm w-100 mx-0.5" 
                                    onclick="eliminarFila('${item.idproducto}', '${item.codigoserie}', '${item.id}')">
                                    <i class="fas fa-long-arrow-alt-left"></i>️
                            </button>                        
                        </td>
                    </tr>`;
            });
            row += `
                                </tbody>
                            </table>
                        </td>
                    </tr>`;
        } else {
            // Solo un ítem: se muestra el botón de eliminar en la fila resumen
            let actionBtn = `<button class="btn btn-danger btn-sm w-100 mx-0.5" onclick="eliminarFila('${first.idproducto}', '${first.codigoserie}', '${first.id}')">
                                <i class="fas fa-long-arrow-alt-left"></i>️
                            </button>`;
            row += `
                <tr id="group_${idx}">
                    <td class="text-center">${totalQuantity}</td>
                    <td>${product}</td>
                    <td>${serieDisplay}</td>
                    <td>${actionBtn}</td>
                </tr>`;
        }
        return row;
    }

    function construirFilaSinSerie(group, idx) {
        let product = group.product,
            totalQuantity = group.totalQuantity,
            first = group.items[0];
        console.log("first", first);
        console.log("first idproducto", first.idproducto);
        let actionBtn = `<button class="btn btn-danger btn-sm w-100 mx-0.5" onclick="eliminarFilaSinSerie('${first.idproducto}')">
                            <i class="fas fa-long-arrow-alt-left"></i>️   
                        </button>`;
        return `
            <tr id="group_${idx}">
                <td class="text-center">${totalQuantity}</td>
                <td>${product}</td>
                <td>SIN SERIE</td>
                <td>${actionBtn}</td>
            </tr>`;
    }

    function asignarEventoExpand() {
        $(".expand-btn").off("click").on("click", function () {
            const target = $(this).data('target');
            const detailRow = $('#' + target);
            if (detailRow.is(':visible')) {
                detailRow.slideUp();
                $(this).html('<i class="fas fa-list"></i>').data('state', 'collapsed').attr('aria-label', 'Expandir detalles');
            } else {
                detailRow.slideDown();
                $(this).html('<i class="fas fa-list-alt"></i>').data('state', 'expanded').attr('aria-label', 'Ocultar detalles');
            }
        });
    }

    detalletraspaso = [];
    temptras = [];
    var seriesconcatenadas = [];

    function agregarProaTras(index, idpro, codigoserie) {
        console.log("Llamada a agregarProaTras:", { index, idpro, codigoserie });
        console.log("Contenido de temp antes de buscar:", temp);
        
        // Se busca el producto en temp usando idpro y codigoserie
        let producto = temp.find(item => String(item.idpro) === String(idpro) && item.codigoserie === codigoserie);
        console.log("Producto encontrado:", producto);

        if (detalletraspaso.some(item => item.idproducto === producto.idpro && item.codigoserie === producto.codigoserie)) {
            alert("⚠️ Este número de serie ya fue agregado al traspaso.");
            return;
        }

        // Obtener el nombre del producto desde el select
        let nombreProducto = $("#producto option:selected").text() || "Producto desconocido";
        console.log("Nombre del producto seleccionado:", nombreProducto);
        
        detalletraspaso.push({
            idproducto: producto.idpro, 
            codigoserie: producto.codigoserie,
            nombre: nombreProducto
        });
        console.log("detalletraspaso actualizado:", detalletraspaso);
        

        // Agregar en seriesconcatenadas (si aún no está)
        if (!Array.isArray(seriesconcatenadas)) {
            seriesconcatenadas = [];
        }
        if (!seriesconcatenadas.some(item => item.idproducto === idpro && item.serie === codigoserie)) {
            seriesconcatenadas.push({
                idproducto: idpro,
                serie: codigoserie,
                ser_id: producto.id  
            });
        }
        console.log("seriesconcatenadas actualizado:", seriesconcatenadas);

        // Agregar la fila en la tabla temporal (derecha)
        $("#tb_prodoc tbody").append(`
            <tr id="fila_temporal_${producto.idpro}_${producto.codigoserie}">
                <td class="text-center">1</td>
                <td>${nombreProducto}</td>
                <td>${producto.codigoserie}</td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm w-100 mx-0.5" onclick="eliminarFila('${producto.idpro}', '${producto.codigoserie}', '${producto.id}')">
                        <i class="fas fa-long-arrow-alt-left"></i>️
                    </button>
                </td>
            </tr>
        `);
        console.log("Fila temporal agregada para:", producto);

        // Remover la fila correspondiente en la tabla izquierda (de productos con serie)
        $("#fila_" + producto.id).remove();

        // Agrega el código de serie al array traspasados
        traspasados.push(producto.codigoserie);
        console.log("traspasados actualizado:", traspasados);
        console.log("productosCache actualizado:", productosCache);


        mostrarTablaTemporal();
        actualizarTablaTraspaso();
    }


    
    function eliminarFila(idpro, codigoserie, idserie) {
        console.log("🗑️ Eliminando producto con ID:", idpro, "y Código de serie:", codigoserie);

        // 1. Eliminarlo del arreglo de detalle (lado derecho)
        detalletraspaso = detalletraspaso.filter(item =>
            !(String(item.idproducto) === String(idpro) && item.codigoserie === codigoserie)
        );

        // 2. Eliminarlo de seriesconcatenadas
        seriesconcatenadas = seriesconcatenadas.filter(item =>
            !(String(item.idproducto) === String(idpro) && item.serie === codigoserie)
        );

        // 3. Borrar la fila temporal de la derecha
        $("#fila_temporal_" + idpro + "_" + codigoserie).remove();

        // 4. Recuperar el objeto "producto" de tu array global `temp` (o como lo llames).
        let producto = temp.find(item =>
            String(item.idpro) === String(idpro) && item.codigoserie === codigoserie
        );

        // 5. Comprobar si coincide con el producto actualmente seleccionado en #producto
        let productoSeleccionado = $("#producto").val();
        if (producto && String(producto.idpro) === String(productoSeleccionado)) {
            
            // (A) AÑADIRLO de nuevo al array de la izquierda que uses para pintar (por ej. `productosConSerie`).
            // OJO: Debes asegurarte de que `productosConSerie` contenga los mismos campos que usas en filtrarEnVivo().
            if (!productosConSerie.some(p => p.idserie == producto.id)) {
                productosConSerie.push({
                    idserie: producto.id,
                    codigoserie: producto.codigoserie,
                    // cualquier otro campo que uses en filtrarEnVivo
                });
            }

            // (B) Insertar la fila en el DOM (ya lo hacías):
            let filaIzquierda = `
                <tr id="fila_${producto.id}">
                    <td>${producto.codigoserie}</td>
                    <td>
                        <button type="button" class="btn btn-success w-100 mx-0.5" 
                            onclick="agregarProaTras(${producto.id}, ${idpro}, '${producto.codigoserie}')">
                            <i class="fas fa-long-arrow-alt-right"></i>    
                        </button>
                    </td>
                </tr>
            `;
            $("#tblistcod tbody").append(filaIzquierda);

            // (C) Llamar a filtrarEnVivo() para que, si #serieBuscar tiene texto, se oculte si no coincide
            filtrarEnVivo();
        }

        // 6. Quitar el código de serie de `traspasados` para que pueda volver a aparecer si corresponde
        traspasados = traspasados.filter(codigo => String(codigo) !== String(codigoserie));

        // 7. Refrescar la tabla de la derecha
        actualizarTablaTraspaso();
    }

    function agregarSinSerie(idpro) {
        console.log("✅ Agregando producto sin serie con ID:", idpro);

        // Verificar que `detalletraspaso` existe
        if (!Array.isArray(detalletraspaso)) {
            return;
        }

        let stockDisponible = parseInt($("#disponibles").val().trim()) || 0;

        // Verificar si el input `cantidad` existe en el DOM
        let cantidadInput = document.getElementById("cantidad");

        if (!cantidadInput) {
           
            alert("⚠️ Error interno: No se encontró el campo de cantidad.");
            return;
        }

        // Obtener y convertir la cantidad a número entero
        let cantidadTexto = cantidadInput.value.trim();
        

        let cantidad = parseInt(cantidadTexto, 10);
       
        if (isNaN(cantidad) || cantidad <= 0) {
            
            alert("⚠️ Ingrese una cantidad válida.");
            return;
        }

        // Obtener el nombre del producto seleccionado
        let nombreProducto = $("#producto option:selected").text() || "Producto desconocido";

        // Verificar si el producto ya está en `detalletraspaso`
        let productoExistente = detalletraspaso.find(item => item.idproducto === idpro && !item.codigoserie);

        if (productoExistente) {
            let nuevaCantidad = productoExistente.cantidad + cantidad;

            // 🔥 Restricción: No permitir más cantidad de la que hay en stock
            if (nuevaCantidad > stockDisponible) {
                alert("⚠️ No puedes agregar más de " + stockDisponible + " unidades en total.");
                return;
            }

            productoExistente.cantidad = nuevaCantidad;
            
            
            // Actualizar la cantidad en la tabla
            $(`#fila_${idpro} .cantidad-col`).text(productoExistente.cantidad);
            } 
            else {
            // 🔥 Restricción: No permitir agregar más cantidad de la que hay en stock
                if (cantidad > stockDisponible) {
                    
                    alert("⚠️ No puedes agregar más de " + stockDisponible + " unidades.");
                    return;
                }

                // Agregar un nuevo producto sin serie
                let nuevoProducto = {
                    idproducto: idpro,
                    cantidad: cantidad,
                    nombre: nombreProducto
            };

            detalletraspaso.push(nuevoProducto);
            console.log("📌 Producto sin serie agregado al detalle del traspaso:", nuevoProducto);

            // Agregar la fila a la tabla `tb_prodoc`
            $("#tb_prodoc tbody").append(`
                <tr id="fila_${idpro}">
                    <td class="text-center cantidad-col">${cantidad}</td>
                    <td>${nombreProducto}</td>
                    <td class="text-center">SIN SERIE</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm w-100 mx-0.5" onclick="eliminarFilaSinSerie(${idpro})">
                            <i class="fas fa-long-arrow-alt-left"></i>️
                        </button>
                    </td>
                </tr>
            `);
        }
        mostrarTablaTemporal();
        actualizarTablaTraspaso();
    }
    function eliminarFilaSinSerie(idpro) {
        console.log("🗑️ Eliminando producto sin serie con ID:", idpro);
        // Buscar el índice del producto sin serie en detalletraspaso
        let index = detalletraspaso.findIndex(item => 
            String(item.idproducto) === String(idpro) && !item.codigoserie
        );
        
        if (index >= 0) {
            let item = detalletraspaso[index];
            if (item.cantidad > 1) {
                // Si hay más de una unidad, se disminuye en 1
                item.cantidad--;
                // Actualiza la cantidad en la fila temporal
                $(`#fila_${idpro} .cantidad-col`).text(item.cantidad);
            } else {
                // Si la cantidad es 1, se elimina el registro
                detalletraspaso.splice(index, 1);
                $(`#fila_${idpro}`).remove();
            }
        }
        actualizarTablaTraspaso();
    }


    function validarTraspasoAntesDeEnviar() {
        let productosInvalidos = detalletraspaso.filter(p => 
            !seriesconcatenadas.some(s => 
                String(s.idproducto) === String(p.idproducto) &&  // 🔥 Ahora usa `idproducto`
                String(s.serie) === String(p.codigoserie)
            )
        );

        if (productosInvalidos.length > 0) {
            
            alert("Error: Algunos productos no tienen la información completa en `seriesconcatenadas`. Revisa la consola para más detalles.");
            return false;
        }

        return true;
    }


    // Deprecado!!!!!!!!
    // function agregaratrapaso() {
    //     let cantidad = parseInt($("input[name='cantidad']").val().trim());
    //     let maximo = parseInt($("#disponibles").val().trim());

    //     if (isNaN(cantidad) || cantidad <= 0) {
    //         console.warn("⚠️ Cantidad inválida:", cantidad);
    //         alert("Debe ingresar una cantidad válida.");
    //         return;
    //     }

    //     if (cantidad > maximo) {
    //         console.warn("⚠️ Cantidad supera el stock disponible. Máximo permitido:", maximo);
    //         alert("Cantidad a traspasar supera el máximo disponible.");
    //         return;
    //     }

    //     console.log("Producto a traspasar:", $("#producto option:selected").text(), "Cantidad:", cantidad);
    //     console.log("Temp antes del filtrado:", temp);

    //     if (temp.length < cantidad) {
    //         console.error("❌ Error: No hay suficientes productos en temp.");
    //         alert("No hay suficientes unidades disponibles.");
    //         return;
    //     }

    //     let tempSeleccionado = temp.slice(0, cantidad);
    //     console.log("🔍 Productos seleccionados:", tempSeleccionado);

    //     let idpro = $("#producto").val();
    //     let nombrepro = $("#producto option:selected").text();

    //     let productoExistente = detalletraspaso.find(item => item.idproducto === idpro);
    //     if (productoExistente) {
    //         productoExistente.cantidad += cantidad;
    //         productoExistente.temp.push(...tempSeleccionado);
    //         console.log("🔄 Producto ya existía, nueva cantidad:", productoExistente.cantidad);
    //     } else {
    //         detalletraspaso.push({
    //             "idproducto": idpro,
    //             "cantidad": cantidad,
    //             "nombrepro": nombrepro,
    //             "temp": tempSeleccionado,
    //             "tieneserie": "NO"
    //         });
    //         console.log("✅ Producto agregado a detalletraspaso:", detalletraspaso);
    //     }

    //     actualizarTablaTraspaso();
    // }



    function quitarDetalle(idproducto) {
      
        $(`#fila_${idproducto}`).remove();

        // Filtrar para eliminar el producto específico
        detalletraspaso = detalletraspaso.filter(item => item.idproducto !== idproducto);
       
    }

    function guardarTrapaso() {
        let dataTras = {};

        if ($("#bodega").val() != "") {
            dataTras["usuario"] = "<?= $_SESSION['cloux_new'] ?>";
            dataTras["fecha"] = convertDateFormat($("input[name='fecha']").val());
            dataTras["bodega"] = $("#bodega").val(); // esta seccion probablemente requiera una verificacion cuando se active el envio entre tecnicos
            dataTras["bodega2"] = $("#bodega2").val() || 26; // Si no se selecciona, se envía a la bodega principal
            dataTras["productos"] = detalletraspaso;
            dataTras["observaciones"] = $("textarea[name='observaciones']").val();

            
            
            // ✅ Validar solo los productos con serie
            let productosConSerie = detalletraspaso.filter(p => p.codigoserie);
            if (productosConSerie.length > 0 && !validarTraspasoAntesDeEnviar()) {
                return;
            }
        } else {
            Swal.fire('Error', 'Debes seleccionar un técnico', 'error');
            
            return;
        }

        let json = JSON.stringify(dataTras);
       

        $.post("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'nuevoTraspasonew',
            traspaso: json,
            retornar: 'no'
        }, function(data) {
            
            if (data.logo !== 'error') {
                toastr.success(data.mensaje);
            } else {
                toastr.error(data.mensaje);
            }
            location.reload();
        });
    }


    function convertDateFormat(string) {
        var info = string.split('/').reverse().join('/');
        return info;
    }

    function verTraspaso(index, idtras) {
       
        
        $("#tblisttras").removeClass("col-sm-12").addClass("col-sm-6");
        $("#dettras .box-title").html("Detalle de traspaso");

        let items = traspasosalb[index]['index'];
        if (!items || !items.length) {
           
            $("#dettras .box-body").html("<p>No hay ítems en este traspaso.</p>");
            $("#dettras").show();
            $('html, body').animate({ scrollTop: 0 }, 400);
            return;
        }
        
        let envia = items[0].usr_env || 'Bodega central';
        let recibe = items[0].usr_rec || 'No definido';
        let fecha = items[0].fecha || 'Sin fecha';
        
        
        // Agrupar ítems por producto (ajusta el campo si es necesario)
        let groups = {};
        $.each(items, function(i, item) {
            let prodName = item.proveedor; // Ajusta este campo si es distinto
            if (!groups[prodName]) {
                groups[prodName] = [];
            }
            groups[prodName].push(item);
        });
       
        
        // Ordenar grupos:
        // - Primero los productos con serie y varios ítems (desplegables)
        // - Luego los productos con serie y un solo ítem
        // - Finalmente, los productos sin serie
        let sortedGroups = Object.entries(groups).map(([product, groupItems]) => ({
            product: product,
            items: groupItems,
            // Usamos la existencia de 'codigo' para determinar si tiene serie.
            tiene_serie: (groupItems[0].codigo && groupItems[0].codigo.trim() !== "") ? "SI" : "NO",
            count: groupItems.length
        })).sort((a, b) => {
            // Primero los que tienen serie y varios ítems
            if (a.tiene_serie === "SI" && a.count > 1 && !(b.tiene_serie === "SI" && b.count > 1)) return -1;
            if (b.tiene_serie === "SI" && b.count > 1 && !(a.tiene_serie === "SI" && a.count > 1)) return 1;
            // Luego, si tienen serie pero solo uno, se ponen antes de los sin serie
            if (a.tiene_serie === "SI" && a.count === 1 && b.tiene_serie === "NO") return -1;
            if (b.tiene_serie === "SI" && b.count === 1 && a.tiene_serie === "NO") return 1;
            return 0;
        });
        
        
        // Verificar si se debe mostrar la columna "Codigo Tracking"
        let hasTrackingCode = items.some(it => it.idtracking == 2);
       
        
        // Construir la cabecera de la tabla (sin la columna "N° Serie SIM.")
        let table = "Fecha <b>" + fecha + "</b> De " + envia + " para " + recibe + " <hr>";
        table += "<table class='table table-sm table-bordered table-striped'><thead class='thead-dark'><tr>";
        table += "<th>Producto</th><th>Cantidad</th><th>N° Serie Pro.</th><th>Tipo</th><th>Tracking</th>";
        if (hasTrackingCode) { table += "<th>Codigo Tracking</th>"; }
        table += "<th>Acción</th></tr></thead><tbody>";
        
        let idx = 0;
        $.each(sortedGroups, function(i, group) {
            let product = group.product;
            let groupItems = group.items;
            let count = group.count;
            let first = groupItems[0];
            
            
            let expandBtn = "";
            let actionBtn = "";
            let serieDisplay = "";
            
            // Si el producto es sin serie, mostramos "Sin serie"
            if (group.tiene_serie === "NO") {
                serieDisplay = "Sin serie";
            } else {
                // Con serie: si hay más de un ítem, mostramos "-" en resumen; si es 1, mostramos el código
                serieDisplay = (count > 1) ? "-" : (first.codigo || "Sin serie");
            }
            
            // Determinar si se debe mostrar botón de acción:
            // Solo se muestra si el grupo tiene serie ("SI") y más de 1 ítem.
            let hasSerie = (group.tiene_serie === "SI");
            if (hasSerie && count > 1) {
                // Si no existe first.direction, se asume 1 (Técnico A)
                let direction = first.direction || 1;
                expandBtn = createExpandButton(idx, direction);
            } else {
                expandBtn = "";
                actionBtn = "";
            }
            
            table += "<tr>";
            table += "<td>" + product + "</td>";
            table += "<td>" + count + "</td>";
            table += "<td>" + serieDisplay + "</td>";
            table += "<td>Producto</td>";
            table += "<td>" + getTrackingText(first.idtracking) + "</td>";
            if (hasTrackingCode) {
                table += (first.idtracking == 2 ? "<td>" + first.codigotracking + "</td>" : "<td></td>");
            }
            table += "<td>" + expandBtn + " " + actionBtn + "</td>";
            table += "</tr>";
            
            // Agregar fila de detalle solo para grupos con serie y más de un ítem
            if (hasSerie && count > 1) {
                // Aseguramos que la fila de detalle tenga el mismo id que el data-target del botón
                let direction = first.direction || 1;
                table += "<tr class='detail-row' id='detail_" + direction + "_" + idx + "' style='display:none;'><td colspan='" + (hasTrackingCode ? 7 : 6) + "'>";
                table += "<table class='table table-sm table-bordered'><thead><tr>";
                table += "<th>N° Serie Pro.</th><th>Tipo</th><th>Tracking</th>";
                if (hasTrackingCode) { 
                    table += "<th>Codigo Tracking</th>"; 
                }
                table += "</tr></thead><tbody>";
                $.each(groupItems, function(j, item) {
                    table += "<tr>";
                    table += "<td>" + (item.codigo || "Sin serie") + "</td>";
                    table += "<td>Producto</td>";
                    table += "<td>" + getTrackingText(item.idtracking) + "</td>";
                    if (hasTrackingCode) {
                        table += (item.idtracking == 2 ? "<td>" + item.codigotracking + "</td>" : "<td></td>");
                    }
                    table += "</tr>";
                });
                table += "</tbody></table></td></tr>";
            }

            idx++;
        });
        
        table += "</tbody></table>";
        
       
        $("#dettras .box-body").html(table);
        $("#dettras").show();
        $('html, body').animate({ scrollTop: 0 }, 400);
        
        // Evento para expandir/contraer las filas de detalle
        $(".expand-btn").off("click").on("click", function () {
            const target = $(this).data('target');
            
            const detailRow = $('#' + target);
            if (detailRow.is(':visible')) {
                detailRow.slideUp();
                $(this).html('<i class="fas fa-list"></i>').data('state', 'collapsed').attr('aria-label', 'Expandir detalles');
               
            } else {
                detailRow.slideDown();
                $(this).html('<i class="fas fa-list-alt"></i>').data('state', 'expanded').attr('aria-label', 'Ocultar detalles');
                
            }
        });
    }

    /**
     * Devuelve el texto para el campo Tracking según el idtracking.
     */
    function getTrackingText(idtracking) {
        if (idtracking == 1) {
            return 'Preparación';
        } else if (idtracking == 2) {
            return 'En tránsito';
        } else if (idtracking == 3) {
            return 'Recepcionado';
        } else {
            return 'No identificado';
        }
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
        if (opc === 0) {
            let tecnico = $('#bodega').val();
            if (tecnico != '') {
                console.log('entro');
                console.log(tecnico);
                console.log('estado del boton');
                console.log($("#btnVerPro").prop('disabled'));

                $("#btnVerPro").prop('disabled', false); // Habilitar el botón 
                console.log('estado del boton');
                console.log($("#btnVerPro").prop('disabled'));
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

        let btnTraspaso = $("#btntraspasotecnico");

        if (!btnTraspaso.hasClass("active")) {
            btnTraspaso.addClass("active"); // Agregar clase si no la tiene
        }

        $('#divbodega2').show();
        $('#divpro').hide();
        $('#tblistcod').hide();
        btnTraspaso.html('<i class="fa fa-times-circle-o" aria-hidden="true"></i> Cancelar')
                .removeClass('btn-success')
                .addClass('btn-danger')
                .attr('onclick', 'cancelarTraspasoTecnico(event)');
        $('#divproductos').show();
        $('#divbuttons').hide();
        $('#tb_prodoc').hide();
        $('#formbutton').hide();
    }

    function cancelarTraspasoTecnico(event) {
        event.preventDefault();

        let btnTraspaso = $("#btntraspasotecnico");

        if (btnTraspaso.hasClass("active")) {
            btnTraspaso.removeClass("active"); // Remover clase si está activa
        }

        $('#divbodega2').hide();
        btnTraspaso.html('<i class="fa fa-exchange" aria-hidden="true"></i> Traspaso entre técnicos')
                .removeClass('btn-danger')
                .addClass('btn-success')
                .attr('onclick', 'traspasoTecnico(event)');
        $('#divproductos').hide();
        $('#formeditar').hide();
        $('#bodega').val('');
        $('#divbuttons').show();
        $('#tb_prodoc').show();
        $('#formbutton').show();
    }

    function formatEstado(estado) {
        if (!estado) return "";
        estado = estado.toString().toUpperCase();
        switch (estado) {
            case "BUENO":
            return '<span class="badge badge-success">Bueno</span>';
            case "MALO":
            return '<span class="badge badge-danger">Malo</span>';
            case "PENDIENTE":
            return '<span class="badge badge-warning">Pendiente</span>';
            default:
            return estado;
        }
    }
    function createExpandButtonTT(idx, opc) {
        let colorClass = opc === 1 ? "btn-success" : "btn-danger";
        const btnHTML = `<button type="button" class="btn btn-sm ${colorClass} expand-btn expandTT" 
                                data-target="detail_${opc}_${idx}" 
                                data-opc="${opc}"
                                data-group-index="${idx}"
                                data-state="collapsed" 
                                aria-label="Expandir detalles" 
                                title="Mostrar detalles">
                                <i class="fas fa-list"></i>
                            </button>`;
        console.log("createExpandButtonTT: Button HTML generated:", btnHTML);
        return btnHTML;
    }

    $(document).on('click', '.expandTT', function(e) {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        const target = $btn.attr('data-target');  // Ej: "detail_1_2"
        const opc = $btn.attr('data-opc');         // Ej: "1"
        const groupIndexStr = $btn.attr('data-group-index');
        const groupIndex = groupIndexStr ? parseInt(groupIndexStr, 10) : null;
        
        console.log("expand-btn (sin DataTables): Antes de clic, estado =", $btn.attr('data-state') || 'collapsed',
                    "para target =", target, "opc =", opc, "groupIndex =", groupIndex);
        
        // Seleccionamos la agrupación según opc
        let currentGrouped = (opc === "1" || opc == 1) ? window.grouped1 : window.grouped2;
        if (groupIndex === null || typeof currentGrouped === "undefined" || !currentGrouped[groupIndex]) {
            console.log("expand-btn (sin DataTables): grouped o grouped[groupIndex] no definido.");
            return;
        }
        
        // Buscamos la fila actual
        const $tr = $btn.closest('tr');
        
        // Verificamos si la fila siguiente es el child row que queremos insertar
        if ($tr.next().attr('id') === target) {
            // Si ya existe, la quitamos (colapsamos)
            $tr.next().remove();
            $btn.attr('data-state', 'collapsed').html('<i class="fas fa-list"></i>');
            console.log("expand-btn (sin DataTables): Se colapsó el detalle para", target);
        } else {
            // Generamos el HTML para el detalle usando la función formatChildRow
            const childHtml = formatChildRow(currentGrouped[groupIndex].items, groupIndex, opc);
            // Creamos una nueva fila <tr> con el id adecuado y una sola celda que ocupe todas las columnas
            const $childRow = $("<tr id='" + target + "' class='child-row'><td colspan='5'>" + childHtml + "</td></tr>");
            $tr.after($childRow);
            $btn.attr('data-state', 'expanded').html('<i class="fas fa-list-alt"></i>');
            console.log("expand-btn (sin DataTables): Se expandió el detalle para", target);
        }
    });



    // Función para crear la fila resumen TT
    function createSummaryRowTT(group, rowIndex, opc) {  // "opc" será 1 o 2
        const firstItem = group.items[0];
        const count = group.count;
        
        console.log("createSummaryRowTT: Procesando grupo:", group.product, "con", count, "items");
        console.log("createSummaryRowTT: Primer item:", firstItem);
        
        let serieDisplay = (group.tiene_serie === "NO")
            ? "Sin serie"
            : (count > 1 ? "-" : (firstItem.serie || ""));
        
        let estadoDisplay = (count > 1)
            ? "-"
            : formatEstado(firstItem.condicion);
        
        let actionBtn = "";
        if (group.tiene_serie === "SI" && count > 1) {
            actionBtn = createExpandButtonTT(rowIndex, opc);
            console.log("createSummaryRowTT: Grupo con serie y varios items, se generó botón expandible:", actionBtn);
        } else {
            actionBtn = `
                <button type="button"
                        class="btn btn-sm btn-${opc === 1 ? 'success' : 'danger'} btn-circle"
                        style="width:65%; color:white;"
                        onclick="traspasoalb(${opc}, ${rowIndex}, ${firstItem.idpro || firstItem.pro_id}, ${firstItem.idserie || 0}, '${firstItem.serie}')">
                    <i class="fas fa-long-arrow-alt-${opc === 1 ? 'right' : 'left'}"></i>
                </button>
            `;
            console.log("createSummaryRowTT: Grupo sin botón expandible, se generó botón de traspaso:", actionBtn);
        }
        
        const rowHTML = `
            <tr id="group_TT_${opc}_${rowIndex}">
                <td class="text-center">${count}</td>
                <td>${group.product}</td>
                <td>${serieDisplay}</td>
                <td>${estadoDisplay}</td>
                <td>${actionBtn}</td>
            </tr>
        `;
        
        console.log("createSummaryRowTT: HTML generado:", rowHTML);
        
        return rowHTML;
    }


    // Función para crear la fila detalle TT para productos con serie y >1 ítem
    function createDetailRowTT(idx, items, idtraspaso, opc) {
        console.log("createDetailRowTT: Procesando detalle para grupo índice:", idx);
        console.log("createDetailRowTT: Items del grupo:", items);
        
        let rows = items.map(item => {
            let btn = `
                <button type="button"
                        class="btn btn-sm btn-${opc === 1 ? 'success' : 'danger'} btn-circle"
                        style="width:65%; color:white;"
                        onclick="traspasoalb(${opc}, ${idx}, ${item.idpro}, ${item.idserie}, '${item.serie}', ${idtraspaso})">
                    <i class="fas fa-long-arrow-alt-${opc === 1 ? 'right' : 'left'}"></i>
                </button>
            `;
            return `
                <tr>
                    <td>${item.serie || "Sin serie"}</td>
                    <td>${formatEstado(item.condicion)}</td>
                    <td>${btn}</td>
                </tr>
            `;
        }).join("");
        
        const detailHTML = `
            <tr class="detail-row" id="detail_${opc}_${idx}" style="display:none;">
                <td colspan="5">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rows}
                        </tbody>
                    </table>
                </td>
            </tr>
        `;
        
        console.log("createDetailRowTT: HTML detalle generado:", detailHTML);
        
        return detailHTML;
    }

    let productosTecnico1 = [];
    let productosTecnico2 = [];

    function productosxternico(id, opc) {
        if ($.fn.DataTable.isDataTable('#tabletecnico' + opc)) {
            $('#tabletecnico' + opc).DataTable().destroy();
        }
        $('#tabletecnico' + opc + ' tbody').html('<tr><td colspan="5" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
        
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999),
            operacion: 'productoxTecniconew',
            idtecnico: id,
            retornar: 'no'
        }, function(data) {
            const datos = $.parseJSON(data);
            console.log("Se recibieron " + datos.length + " productos para técnico con ID:", id);
            // Guarda la data sin agrupar para el modo detalle:
            if(opc === 1) {
                window.allProductosTecnico1 = datos;
            } else if(opc === 2) {
                window.allProductosTecnico2 = datos;
            }
            loadProductosTecnico(datos, opc);
        });
    }

    function traspasoalb(opciontecnico, index, idproducto, idserie, serie) {
        let tecotro = (opciontecnico === 1) ? $('#bodega2').val() : $('#bodega').val();
        console.log("traspasoalb: Valor de tecotro =", tecotro);
        if (tecotro == '') {
            alert('Debes seleccionar una opcion contraria para el traspaso');
            return;
        }
        
        let bodega1 = $('#bodega').val();
        let bodega2 = $('#bodega2').val();
        console.log("traspasoalb: bodega1 =", bodega1, "bodega2 =", bodega2);
        console.log("traspasoalb: Valores globales previos: bod1 =", bod1, "bod2 =", bod2);
        
        let valida = 0;
        if (opciontecnico === 1) {
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
        
        console.log("traspasoalb: Valor de valida =", valida);
        
        let come = $('#observaciones').val();
        var datosj = {
            'bodega1': bodega1,
            'bodega2': bodega2,
            'idproducto': idproducto,
            'comen': come,
            'valida': valida,
            'opciontecnico': opciontecnico,
            'idserie': idserie,
            'serie': serie
        };
        var sendj = JSON.stringify(datosj);
        console.log("traspasoalb: Datos de sendj:", sendj);
        console.log("traspasoalb: Producto a traspasar:", { idproducto, idserie, serie });
        
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
            beforeSend: function() {
                console.log("traspasoalb: Enviando datos:", sendj);
            },
            error: function(respuesta) {
                console.log("traspasoalb: Error en la petición:", respuesta);
            },
            success: function(respuesta) {
                console.log("traspasoalb: Respuesta recibida:", respuesta);
                if (respuesta.logo == 'success') {
                    toastr.success(respuesta.mensaje);
                    console.log("traspasoalb: Actualizando tabla técnico receptor (bodega2):", bodega2);
                    productosxternico(bodega2, 2); // Actualiza la tabla del técnico receptor
                    console.log("traspasoalb: Actualizando tabla técnico emisor (bodega1):", bodega1);
                    productosxternico(bodega1, 1); // Actualiza la tabla del técnico emisor
                } else {
                    toastr.error(respuesta.mensaje);
                }
            }
        });
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
        let pro1 = JSON.stringify(productosTecnico1);$(document).on('click', '.expand-btn', function(e) {
    e.preventDefault();
    const $btn = $(e.currentTarget);
    // Usar .attr para obtener directamente el atributo
    const target = $btn.attr('data-target');
    const opc = $btn.attr('data-opc'); // Extraemos opc desde el botón
    console.log("expand-btn: Antes de clic, estado =", $btn.attr('data-state') || 'collapsed', "para target =", target, "opc =", opc);
    
    // Determinar qué tabla utilizar según opc
    let table;
    if (opc === "1" || opc === 1) {
       table = $('#tabletecnico1').DataTable();
    } else if (opc === "2" || opc === 2) {
       table = $('#tabletecnico2').DataTable();
    } else {
       console.log("expand-btn: No se pudo determinar 'opc', abortando.");
       return;
    }
    
    const $tr = $btn.closest('tr');
    const row = table.row($tr);
    
    if (row.child.isShown()) {
        row.child.hide();
        $tr.removeClass('shown');
        $btn.attr('data-state', 'collapsed').html('<i class="fas fa-list"></i>');
        console.log("expand-btn: Se colapsó el detalle para", target);
    } else {
        // Asegúrate de que "grouped" esté en un ámbito accesible aquí. 
        // Si no lo está, tendrás que pasarlo o almacenarlo en una variable global.
        const groupIndex = $tr.data('group-index');
        if (typeof grouped === "undefined" || !grouped[groupIndex]) {
            console.log("expand-btn: grouped o grouped[groupIndex] no está definido.");
            return;
        }
        const childHtml = formatChildRow(grouped[groupIndex].items, groupIndex, opc);
        row.child(childHtml).show();
        // No es necesario llamar a .node(), así que lo omitimos
        $tr.addClass('shown');
        $btn.attr('data-state', 'expanded').html('<i class="fas fa-list-alt"></i>');
        console.log("expand-btn: Se expandió el detalle para", target);
    }
});
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

    function formatChildRow(items, idx, opc) {
        console.log("formatChildRow - idx:", idx, "opc:", opc);
        console.log("formatChildRow - items:", items);

        let html = '<table class="table table-sm table-bordered">';
        html += '<thead><tr><th>Serie</th><th>Estado</th><th>Acción</th></tr></thead><tbody>';
        items.forEach(item => {
            // Generar el botón usando el parámetro opc para la dirección y idx para contextualizar
            html += '<tr>';
            html += '<td>' + (item.serie || "Sin serie") + '</td>';
            html += '<td>' + formatEstado(item.condicion) + '</td>';
            // Aquí se usa opc y idx para construir la llamada correctamente
            html += '<td><button type="button" class="btn btn-sm btn-' + (opc == 1 ? 'success' : 'danger') + ' btn-circle" onclick="traspasoalb(' + opc + ', ' + idx + ', ' + item.idpro + ', ' + item.idserie + ', \'' + item.serie + '\')"><i class="fas fa-long-arrow-alt-' + (opc == 1 ? 'right' : 'left') + '"></i></button></td>';            html += '</tr>';
        });
        html += '</tbody></table>';
        return html;
    }

    function loadProductosTecnico(data, opc) {
        console.log("se hizo un loadProductosTecnico");
        console.log("loadProductosTecnico - datos recibidos:", data);
        const grouped = groupProductos(data);
        
        // Asigna la agrupación en función de opc
        if (opc === 1) {
            window.grouped1 = grouped;
        } else if (opc === 2) {
            window.grouped2 = grouped;
        }
        
        const $tbody = $('#tabletecnico' + opc + ' tbody');
        $tbody.empty();
        
        grouped.forEach((group, idx) => {
            const firstItem = group.items[0];
            const serieDisplay = (group.tiene_serie === "NO")
                ? "Sin serie"
                : (group.count > 1 ? "-" : firstItem.serie);
            
            const estadoDisplay = (group.count > 1)
                ? "-"
                : formatEstado(firstItem.condicion);
            
            let btnAccion = "";
            if (group.tiene_serie === "SI" && group.count > 1) {
                // El botón ahora incluye data-group-index
                btnAccion = createExpandButtonTT(idx, opc);
            } else {
                btnAccion = `<button type="button" class="btn btn-sm btn-${opc === 1 ? 'success' : 'danger'} btn-circle" 
                                    onclick="traspasoalb(${opc}, ${idx}, ${firstItem.idpro}, ${firstItem.idserie}, '${firstItem.serie}')" 
                                    aria-label="Transferir producto">
                                <i class="fas fa-long-arrow-alt-${opc === 1 ? 'right' : 'left'}"></i>
                            </button>`;
            }
            
            const rowHtml = `
                <tr data-group-index="${idx}">
                    <td class="text-center">${group.count}</td>
                    <td>${group.product}</td>
                    <td>${serieDisplay}</td>
                    <td>${estadoDisplay}</td>
                    <td>${btnAccion}</td>
                </tr>`;
            $tbody.append(rowHtml);
        });
        
        if ($.fn.DataTable.isDataTable('#tabletecnico' + opc)) {
            $('#tabletecnico' + opc).DataTable().destroy();
        }
        $('#tabletecnico' + opc).DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json' },
            paging: false,
            searching: true,
            ordering: false,
            info: false,
            autoWidth: false
        });
    }

    function groupProductos(data) {
        console.log("groupProductos se ejecuta con data.length =", data.length);
        let groups = {};
        data.forEach(function(item) {
            let productName = item.pro_nombre;
            if (!groups[productName]) {
            groups[productName] = [];
            }
            groups[productName].push(item);
        });
        let groupArray = Object.keys(groups).map(function(key) {
            let items = groups[key];
            let tieneSerie = (items[0].tieneserie == 1) ? "SI" : "NO";
            return {
            product: key,
            items: items,
            tiene_serie: tieneSerie,
            count: items.length
            };
        });
        // Ordenar según un criterio
        groupArray.sort(function(a, b) {
            let weightA = (a.tiene_serie === "SI" ? (a.count > 1 ? 1 : 2) : 3);
            let weightB = (b.tiene_serie === "SI" ? (b.count > 1 ? 1 : 2) : 3);
            if (weightA !== weightB) {
            return weightA - weightB;
            }
            return a.product.localeCompare(b.product);
        });
        console.log("groupProductos devuelve:", groupArray);
        return groupArray;
    }

    function renderDetalleIndividual(opc, searchQuery) {
        // Obtén la data sin agrupar previamente almacenada
        let allData = (opc === 1) ? window.allProductosTecnico1 : window.allProductosTecnico2;
        if (!allData) {
            console.log("renderDetalleIndividual: No se encontró la data para opc", opc);
            return;
        }
        
        searchQuery = searchQuery.trim();
        // Usamos una expresión regular: si el término tiene 3 o más caracteres, exige que la serie comience con el término; de lo contrario, se usa de forma flexible
        let regex = (searchQuery.length >= 3) 
                    ? new RegExp('^' + searchQuery, 'i') 
                    : new RegExp(searchQuery, 'i');
        
        // Filtra los ítems cuyo campo 'serie' cumpla con el criterio
        let filtrados = allData.filter(function(item) {
            return regex.test(item.serie || "");
        });
        
        console.log("renderDetalleIndividual: Se encontraron", filtrados.length, "ítems para el término:", searchQuery);
        
        // Construir un arreglo de arrays, donde cada subarray representa los datos de una fila
        let newRows = filtrados.map(function(item) {
            return [
                item.cantidad || 1,
                item.pro_nombre,
                item.serie || "Sin serie",
                formatEstado(item.condicion),
                `<button type="button" class="btn btn-sm btn-${opc === 1 ? 'success' : 'danger'} btn-circle" 
                    onclick="traspasoalb(${opc}, 0, ${item.idpro}, ${item.idserie}, '${item.serie}')">
                    <i class="fas fa-long-arrow-alt-${opc === 1 ? 'right' : 'left'}"></i>
                </button>`
            ];
        });
        
        let tableId = '#tabletecnico' + opc;
        // Si ya está inicializado DataTable, actualiza sus datos; de lo contrario, inicialízalo
        if ($.fn.DataTable.isDataTable(tableId)) {
            let dt = $(tableId).DataTable();
            dt.clear();
            dt.rows.add(newRows);
            dt.draw();
        } else {
            $(tableId).DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json' },
                paging: false,
                searching: true, // Dejamos activo el buscador
                ordering: false,
                info: false,
                autoWidth: false,
                data: newRows,
                columns: [
                    { title: "Cant." },
                    { title: "Producto" },
                    { title: "Serie" },
                    { title: "Estado" },
                    { title: "Acción" }
                ]
            });
        }
    }

    // Para la tabla 1:
    $(document).on('keyup', '#tabletecnico1_filter input', function() {
        var query = $(this).val().trim();
        if(query !== "") {
            // En modo búsqueda, re-renderiza en detalle (fila por ítem)
            renderDetalleIndividual(1, query);
        } else {
            // Si se borra el término, vuelve a la vista agrupada
            var tecnico = $('#bodega').val();
            if(tecnico) {
                productosxternico(tecnico, 1);
            }
        }
    });

    // Para la tabla 2:
    $(document).on('keyup', '#tabletecnico2_filter input', function() {
        var query = $(this).val().trim();
        if(query !== "") {
            renderDetalleIndividual(2, query);
        } else {
            var tecnico = $('#bodega2').val();
            if(tecnico) {
                productosxternico(tecnico, 2);
            }
        }
    });

    function buscarTraspasosPorSerie() {
        var serie = $("#inputSerieBusqueda").val().trim();
        if (serie === "") {
            $("#resultadoBusquedaSerie").html("<p class='text-danger'>Por favor ingrese una serie</p>");
            return;
        }
        // Limpiar mensajes previos antes de iniciar la búsqueda
        $("#resultadoBusquedaSerie").html("");
        $.ajax({
        url: 'operaciones.php',
        type: 'get',
        dataType: 'json',
        data: { 
            operacion: 'buscarTraspasoPorSerie',
            serie: serie,
            retornar: 'no' 
        },
        success: function(response) {
            var html = "";
            if (response.error) {
                html = "<p class='text-danger'>" + response.error + "</p>";
            } else {
                // Muestra el pro_nombre y usu_id_cargo_nombre (en vez de usu_id_cargo)
                html += "<h4>Producto: " + response.serieGuia.pro_nombre + "</h4>";
                html += "<p>Usuario actual (A cargo de): " + response.serieGuia.usu_id_cargo_nombre + "</p>";

                html += "<table class='table table-bordered'id='tablaTraspasosPorSerie'>";
                html += "<thead><tr><th>ID Traspaso</th><th>Fecha</th><th>Enviado por</th><th>Recibido por</th></tr></thead>";
                html += "<tbody>";

                if (response.traspasos.length > 0) {
                    $.each(response.traspasos, function(i, traspaso) {
                        html += "<tr>";
                        html += "<td>" + traspaso.tra_id + "</td>";
                        html += "<td>" + traspaso.tra_fecha + "</td>";
                        // En vez de usu_id_envia / usu_id_recibe, usamos usu_envia_nombre / usu_recibe_nombre
                        html += "<td>" + traspaso.usu_envia_nombre + "</td>";
                        html += "<td>" + traspaso.usu_recibe_nombre + "</td>";
                        html += "</tr>";
                    });
                } else {
                    html += "<tr><td colspan='6' class='text-center'>No se encontraron traspasos</td></tr>";
                }
                html += "</tbody></table>";
            }
            $("#resultadoBusquedaSerie").html(html);
           // Agregar scroll si hay más de 9 filas
            var $tabla = $("#tablaTraspasosPorSerie");
            if ($tabla.find("tbody tr").length > 9) {
                // Envuelve la tabla en un contenedor con scroll
                $tabla.wrap('<div class="table-responsive" style="max-height: 550px; overflow-y: auto;"></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error("buscarTraspasosPorSerie: Error en la petición AJAX:", xhr.status, status, error);
            console.log("Response Text:", xhr.responseText);
            $("#resultadoBusquedaSerie").html("<p>Error en la búsqueda.</p>");
        }
        });
    }
    // Limpiar el campo de búsqueda
    $('#modal_busqueda_serie').on('hidden.bs.modal', function () {
        // Limpiar el campo de búsqueda
        $(this).find('#inputSerieBusqueda').val('');
        // Limpiar el contenido de resultados
        $(this).find('#resultadoBusquedaSerie').html('');
    });




</script>