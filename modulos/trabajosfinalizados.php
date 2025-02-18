<?php
session_start();
$optioncliente = '<option value="0">Seleccione</option>';
$sql = "SELECT * FROM clientes GROUP by cuenta";
$res = $link->query($sql);
if (mysqli_num_rows($res) > 0) {
    foreach ($res as $key => $data) {
        $optioncliente .= '<option value="' . $data['cuenta'] . '">' . $data['cuenta'] . '</option>';
    }
}

$sqle = "SHOW COLUMNS FROM tickets WHERE Field = 'tic_kmsdist'";
$rese = $link->query($sqle);
if (mysqli_num_rows($rese) == 0) {
    $sqlr = "ALTER TABLE tickets ADD tic_kmsdist VARCHAR(20) DEFAULT 0";
    $resr = $link->query($sqlr);
}

$comunas = array();
$sql = "SELECT * FROM comunas";
$res = $link->query($sql);
if (mysqli_num_rows($res) > 0) {
    foreach ($res as $key => $data) {
        $comunas[] = array(
            'comid' => $data['comuna_id'],
            'comnombre' => $data['comuna_nombre']
        );
    }
}

$sql = "SELECT * FROM configuracion_columnas WHERE coco_pestana = 1 and usu_id = {$_SESSION['cloux_new']}";
$res = $link->query($sql);
if (mysqli_num_rows($res) == 0) {
    for ($i = 1; $i <= 27; $i++) {
        $sql1 = "INSERT INTO configuracion_columnas (usu_id,coco_ncolumna,coco_visible,coco_pestana) VALUES ({$_SESSION['cloux_new']},{$i},1,1);";
        $res1 = $link->query($sql1);
    }
}
?>
<style>
    /* Contenedor para los iconos */
    .icon-container {
        display: flex;
        /* Usar flexbox para alinear los iconos horizontalmente */
        justify-content: center;
        /* Centrar los iconos en el contenedor */
        gap: 5px;
        /* Espacio entre los iconos */
    }

    /* Estilo de los enlaces de los iconos */
    .icon-link {
        display: inline-block;
        /* Asegura que los enlaces no ocupen más espacio del necesario */
        /*color: #000;            Color de los iconos (puedes ajustar según sea necesario) */
        font-size: 16px;
        /* Tamaño de los iconos */
        text-decoration: none;
        /* Quitar subrayado de los enlaces */
    }

    .icon-link:hover {
        color: #007bff;
        /* Cambia el color del icono al pasar el ratón */
    }

    #tbtickets thead th {
        text-align: center;
        color: #fff;
        background-color: #212529;
        border-color: #383f45;
    }

    .aligned-spans {
        display: flex;
        justify-content: space-between;
        /* o flex-start, flex-end según lo que necesites */
        align-items: center;
        /* Para alinear verticalmente */
        width: 100%;
    }

    .aligned-spans span {
        flex: 1;
        /* Esto asegurará que ambos spans ocupen el mismo espacio */
        text-align: center;
        /* Opcional: Centra el texto dentro de cada span */
    }

    .fila-activo {
        background-color: #F5F182 !important;
    }

    .fila-inactivo {
        background-color: #82F585 !important;
    }

    .columna-ancha {
        width: 150px !important;
    }

    .imgticket:hover {
        color: #424242;
        -webkit-transition: all .3s ease-in;
        -moz-transition: all .3s ease-in;
        -ms-transition: all .3s ease-in;
        -o-transition: all .3s ease-in;
        transition: all .3s ease-in;
        opacity: 1;
        transform: scale(1.25);
        -ms-transform: scale(1.25);
        /* IE 9 */
        -webkit-transform: scale(1.25);
        /* Safari and Chrome */

    }

    #tbtickets td {
        text-align: center;
    }

    .text-start {
        text-align: start !important;
    }
</style>
<!-- modal -->

<div class="modal fade" id="columnasModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="columnasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#4E63AA;padding: 5px;color:white;">
                <h5 class="modal-title" id="columnasModalLabel">Columnas a Visualizar</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-sm table-bordered table-striped" id="tblcolumnas">
                            <thead class="bg-secondary text-white">
                                <tr>
                                    <th nowrap scope="col" style="text-align:center;">Columna</th>
                                    <th nowrap scope="col" style="text-align:center;">Ver/No Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center">Botones</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-0" value="0"><label for="columna-0"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Estado APP</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-1" value="1"><label for="columna-1"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">N° OT</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-2" value="2"><label for="columna-2"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Fecha registro</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-3" value="3"><label for="columna-3"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Fecha Termino</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-4" value="4"><label for="columna-4"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Días solicitud</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-5" value="5"><label for="columna-5"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Técnico</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-6" value="6"><label for="columna-6"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Cliente</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-7" value="7"><label for="columna-7"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Razon social</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-8" value="8"><label for="columna-8"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Patente</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-9" value="9"><label for="columna-9"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Tipo servicio</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-10" value="10"><label for="columna-10"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Dispositivo</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-11" value="11"><label for="columna-11"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Tipo trabajo</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-12" value="12"><label for="columna-12"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Imei</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-13" value="13"><label for="columna-13"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Accesorios</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-14" value="14"><label for="columna-14"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Lugar</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-15" value="15"><label for="columna-15"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Comuna orig</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-16" value="16"><label for="columna-16"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Comuna dest</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-17" value="17"><label for="columna-17"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Kms dist</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-18" value="18"><label for="columna-18"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Descripción</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-19" value="19"><label for="columna-19"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Comentario</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-20" value="20"><label for="columna-20"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">U.M</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-21" value="21"><label for="columna-21"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Valor trabajo</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-22" value="22"><label for="columna-22"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Valor KM</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-23" value="23"><label for="columna-23"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Total KM</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-24" value="24"><label for="columna-24"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Costo labor</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-25" value="25"><label for="columna-25"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Centro costo</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-26" value="26"><label for="columna-26"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Estado facturación</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-27" value="27"><label for="columna-27"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Pago técnico</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-28" value="28"><label for="columna-28"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Imágenes</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-29" value="29"><label for="columna-29"></label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">Estado</td>
                                    <td align="center">
                                        <div class="icheck-warning d-inline checkcol"><input type="checkbox" checked id="columna-30" value="30"><label for="columna-30"></label></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 5px;">
                <button type="button" class="btn btn-sm" onclick="" style="background-color:#4E63AA;color:white" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mticket">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- fin modal -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Large Modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>One fine body&hellip;</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="alert alert-success oculto alert-dismissible" id="ticketok">
        <!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
        <h4><i class="icon fa fa-warning"></i>El Ticket se ha registrado exitosamente. </h4>
    </div>
    <div class="row submenu ml-2 pt-2">
        <!-- <div class="col-md-12">
<button type='button' class="btn btn-info btn-rounded" id="btn_reportticket"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Generar reporte Histórico</button>
</div> -->
        <div class="col-md-2 top10">
            <div class="form-group row">
                <label for="fil_select_ccosto">Centro Costo</label>
                <select class="form-control form-control-sm" name="fil_select_ccosto" id="fil_select_ccosto">
                    <option value="0">N/A</option>
                    <option value="1">CLIENTE</option>
                    <option value="2">INTERNO</option>
                </select>
            </div>
        </div>
        <div class="col-md-2 top10">
            <div class="form-group row">
                <label for="fil_select_efacturacion">Estado Facturación</label>
                <select class="form-control form-control-sm" name="fil_select_efacturacion" id="fil_select_efacturacion">
                    <option value="">TODOS</option>
                    <option value="0">PENDIENTE</option>
                    <option value="1">OK</option>
                </select>
            </div>
        </div>



        <div class="col-md-2 top10">
            <div class="form-group row">
                <label for="fil_select_pagot">Pago Técnico</label>
                <select class="form-control form-control-sm" name="fil_select_pagot" id="fil_select_pagot">
                    <option value="0">N/A</option>
                    <option value="1">SI</option>
                    <option value="2">NO</option>
                </select>
            </div>
        </div>
        <div class="col-md-2 top10">
            <div class="form-group row">
                <label for="fil_select_cliente">Cliente</label>
                <select id="fil_select_cliente" name="fil_select_cliente" class="form-control form-control-sm">
                    <?php echo $optioncliente ?>
                </select>
                <!-- <?php htmlselect('fil_select_cliente', 'fil_select_cliente', 'clientes', 'id', 'razonsocial', '', '', '', 'razonsocial', '', '', 'si', 'no', 'no'); ?> -->
            </div>
        </div>
        <div class="col-md-2 top10">
            <div class="form-group row">
                <label for="fil_select_tecnico">Técnico</label>
                <?php htmlselect('fil_select_tecnico', 'fil_select_tecnico', 'personal', 'per_id', 'per_nombrecorto', '', '', 'where per_id!=26', 'per_nombrecorto', '', '', 'si', 'no', 'no'); ?>
            </div>
        </div>
        <!-- <div class="col-md-3">
<button class="btn btnh-success btn-rounded" onclick="aplicarFiltros()" style="margin-top: 32px;"><i class="fa fa-check-circle-o"></i> Aplicar filtros</button>
</div> -->
    </div>
    <div class="row ml-2">
        <div class="col-md-2 top10">
            <div class="form-group row">
                <label for="fil_select_ccosto">Desde</label>
                <input type="date" name="fdesde" id="fdesde" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-2 top10">
            <div class="form-group row">
                <label for="fil_select_ccosto">Hasta</label>
                <input type="date" name="fhasta" id="fhasta" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-2 top10">
            <div class="form-group row">
                <label for="fil_select_estado">Técnico</label>
                <select class="form-control form-control-sm" id="fil_select_estado" name="fil_select_estado">
                    <option value="0">Todos</option>
                    <option value="7">Anulados</option>
                </select>
            </div>
        </div>

        <div class="col-md-2 top10">
            <div class="form-group row">
                <label for="fil_select_estado">Estado APP</label>
                <select class="form-control form-control-sm" name="fil_select_estado" id="fil_select_estado">
                    <option value="">TODOS</option>
                    <option value="3">CERRADO</option>
                    <option value="6">Finalizado APP</option>
                </select>
            </div>
        </div>

        <div class="col-md-1">
            <button class="btn btn-sm btn-warning btn-rounded" onclick="resetearFiltros()" style="margin-top: 32px;"><i class="fa fa-refresh"></i> Resetear filtros</button>
        </div>
        <div class="col-md-1">
            <button class="btn btn-sm btn-success btn-rounded" onclick="aplicarFiltros()" style="margin-top: 32px;"><i class="fa fa-check-circle-o"></i> Aplicar filtros</button>
        </div>
        <div class="col-md-1">
            <button type='button' style="margin-top: 32px;" class="btn btn-sm btn-success btn-rounded" id="btnexcexp"><i class="fa fa-download" aria-hidden="true"></i> Exportar Excel</button>
        </div>
        <div class="col-md-1">
            <button type="button" id="" onclick="" title="Filtrar Columnas" data-toggle="modal" data-target="#columnasModal" class="btn btn-sm btn-info btn-rounded" style="margin-top: 32px;"><i class="fas fa-columns"></i></button>
        </div>
    </div>
    <div class="row top20" id="fnuevoticket" style="<?php if (isset($_REQUEST['nuevo'])) {
                                                        echo "display:block;";
                                                    } else {
                                                        echo "display:none;";
                                                    } ?>">
        <div class="col-md-12">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Nuevo Ticket</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <form action="operaciones.php" method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validarTIC()">
                            <input type="hidden" name="operacion" value="nuevoticket" />
                            <input type="hidden" name="retornar" value="index.php?menu=<? echo $_REQUEST["menu"]; ?>&idmenu=<? echo $_REQUEST["idmenu"]; ?>" />
                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Cuenta</label>
                                <?php
                                $idcliente = '';
                                $idpatente = '';
                                $iddispositivo = '';
                                $tservicio = '';
                                $contacto = '';
                                $celular = '';

                                if (isset($_REQUEST['nuevo'])) {

                                    $sql = "select * from vehiculos where veh_id=" . $_REQUEST['nuevo'];
                                    $res = $link->query($sql);
                                    $vehiculos = array();
                                    while ($fila = mysqli_fetch_array($res)) {
                                        $vehiculos[] = array("id" => $fila['veh_id'], "idflotasnet" => $fila['veh_idflotasnet'], "rsocial" => $fila['veh_rsocial'], "tipo" => $fila['veh_tipo'], "gps" => $fila['veh_gps'], "cliente" => $fila['veh_cliente'], "grupo" => $fila['veh_grupo'], "patente" => $fila['veh_patente'], "contacto" => $fila['veh_contacto'], "celular" => $fila['veh_celular'], "estado" => $fila['veh_estado'], "sesion" => $fila['veh_sesion'], "observacion" => $fila['veh_observacion'], "h2" => $fila['veh_h2'], "h12" => $fila['veh_h12'], "h24" => $fila['veh_h24'], "h48" => $fila['veh_h48'], "ultimaposicion" => $fila['veh_ultimaposicion'], "localidad" => $fila['veh_localidad'], "alerta" => $fila['veh_alerta'], "imei" => $fila['veh_imei'], "dispositivo" => $fila['veh_dispositivo'], "tservicio" => $fila['veh_tservicio']);
                                    }


                                    $sql1 = "select * from clientes where id=" . $vehiculos[0]['rsocial'];

                                    $res1 = $link->query($sql1);
                                    while ($fila1 = mysqli_fetch_array($res1)) {
                                        $rsocial = $fila1['cuenta'];
                                    }

                                    $idcliente = $vehiculos[0]['cliente'];
                                    $idrsocial = $vehiculos[0]['rsocial'];
                                    $idpatente = $vehiculos[0]['id'];
                                    $iddispositivo = $vehiculos[0]['dispositivo'];
                                    $tservicio = $vehiculos[0]['tservicio'];
                                    $contacto = $vehiculos[0]['contacto'];
                                    $celular = $vehiculos[0]['celular'];

                                    $condicion = "WHERE cuenta LIKE '%{$rsocial}%'";
                                }
                                ?>
                                <div class="col-sm-4"><?php htmlselect('cliente', 'cliente', 'clientes', 'id', 'cuenta', '', $idcliente, 'WHERE cuenta!="" group by cuenta', 'cuenta', 'getVehCli()', '', 'si', 'no', 'no'); ?></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Razón social</label>
                                <div class="col-sm-4"><?php if (!isset($_REQUEST['nuevo'])) { ?><select name="rsocial" id="rsocial" class="form-control"></select><?php } else {
                                                                                                                                                                    htmlselect('rsocial', 'rsocial', 'clientes', 'id', 'razonsocial', '', $idrsocial, $condicion, 'razonsocial', '', '', 'no', 'no', 'no');
                                                                                                                                                                } ?></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Patente</label>
                                <div class="col-sm-4"><?php if (!isset($_REQUEST['nuevo'])) { ?><select name="patente" id="patente" class="form-control" onchange="selectpatente(this);"></select><?php } else {
                                                                                                                                                                                                    htmlselect('patente', 'patente', 'vehiculos', 'veh_id', 'veh_patente', '', $idpatente, '', 'veh_id', 'selectpatente(this)', '', 'si', 'no', 'no');
                                                                                                                                                                                                } ?></div>
                            </div>
                            <!-- el tipo de vehiculo depende de la patente seleccionda -->
                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Tipo</label>
                                <div class="col-sm-4"><input type="text" id="tipoveh" class="form-control" disabled><input type="hidden" name="tipoveh"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Dispositivo</label>
                                <div class="col-sm-4"><?php htmlselect('dispositivo', 'dispositivo', 'tiposdedispositivos', 'tdi_id', 'tdi_nombre', '', $iddispositivo, '', 'tdi_nombre', '', '', 'si', 'no', 'no'); ?></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Tipo de Servicio</label>
                                <div class="col-sm-4"><?php htmlselect('tipodtrab', 'tipodtrab', 'servicios', 'ser_id', 'ser_nombre', '', $tservicio, '', 'ser_nombre', '', '', 'si', 'no', 'no'); ?></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Contacto</label>
                                <div class="col-sm-4"><input type="text" class="form-control " name="contacto" value="<?php if (isset($_REQUEST['nuevo'])) {
                                                                                                                            echo $contacto;
                                                                                                                        } ?>"></div>
                                <div class="col-sm-3" id="msg_contacto" style="display: none;"><span style="color:red;">* Contacto es obligatorio.</span></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Celular</label>
                                <div class="col-sm-4"><input type="text" class="form-control " name="celular" value="<?php if (isset($_REQUEST['nuevo'])) {
                                                                                                                            echo $celular;
                                                                                                                        } ?>"></div>
                                <div class="col-sm-3" id="msg_celular" style="display: none;"><span style="color:red;">* Celular es obligatorio.</span></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Lugar</label>
                                <div class="col-sm-6"><input type="text" class="form-control " name="lugar"></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label txtleft">Descripción</label>
                                <div class="col-sm-6"><textarea name='descripcion' class='form-control rznone' rows=5></textarea></div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4"><button type="submit" class="btn btn-success btn-rounded" onclick="return ValidarCampos()">Guardar</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-rounded" onclick="cNuevoTicket()">Cancelar</button></div>


                            </div>
                        </form>
                    </div>
                </div><!-- fin box-body -->
            </div>
        </div>
    </div>
    <!-- formulario de asignacion -->
    <div class="oculto" id="fagendar">
        <form class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-4">
                    <label>Fecha</label><br>
                    <input type="text" class="form-control form-control-sm fechaagenda" name="fecha" value="<? echo hoy(); ?>">
                </div>
                <div class="col-sm-4">
                    <label>Hora</label><br>
                    <input type="time" class="form-control form-control-sm" name="hora">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-7">
                    <label>Técnico</label><br>
                    <?php htmlselect('tecnico', 'tecnico', 'personal', 'per_id', 'per_nombrecorto', '', '', '', 'per_apaterno', 'getCiudad()', '', 'si', 'no', 'no'); ?>
                </div>
                <div class="col-sm-4">
                    <label>Ciudad</label><br>
                    <input type="text" class="form-control" id="ciudad" name="ciudad" disabled value="">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <label>Descripción</label><br>
                    <textarea name='descripcion' class='form-control rznone' rows=5></textarea>
                </div>
            </div>

        </form>
    </div>


    <div class="row top20" id="listadodetickets" style="<?php if (isset($_REQUEST['nuevo'])) {
                                                            echo "display:none;";
                                                        } ?>">
        <div class="col-md-12" id="tblistaticket" style="padding-top: 10px;">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Listado de trabajos finalizados</h3>
                </div>
                <div class="box-body table-responsive">

                    <table class="table table-bordered table-striped table-sm" id="tbtickets">
                    </table>


                    <!-- <thead class="thead-dark">
        <th class="columna-ancha colum-0" nowrap></th>
        <th class="colum-1" nowrap>N° OT</th>
        <th class="colum-2" nowrap>Fecha Registro</th>
        <th class="colum-3" nowrap>Fecha Termino</th>
        <th class="colum-4" nowrap>Días Solicitud</th>
        <th nowrap>Dias</th>
        <th class="colum-5" nowrap>Técnico</th>
        <th class="colum-6" nowrap>Cliente</th>
        <th class="colum-7" nowrap>Razon Social</th>
        <th class="colum-8" nowrap>Patente</th>
        <th class="colum-9" nowrap>Tipo Servicio</th>
        <th class="colum-10" nowrap>Dispositivo</th>
        <th class="colum-11" nowrap>Tipo Trabajo</th>
        <th class="colum-12" nowrap>Imei</th>
        <th nowrap>Sello Seguridad</th>
        <th nowrap>Ch 1</th>
        <th nowrap>Ch 2</th>
        <th nowrap>Ch 3</th>
        <th nowrap>Contacto</th>
        <th nowrap>Celular</th>
        <th class="colum-13" nowrap>Lugar</th>
        <th class="colum-14" nowrap>Comuna Orig.</th>
        <th class="colum-15" nowrap>Comuna Dest.</th>
        <th class="colum-16" nowrap>Kms Dist.</th>
        <th class="colum-17" nowrap>Descripción</th>
        <th class="colum-18" nowrap>Comentario</th>
        <th nowrap>Fecha labor</th>
        <th class="colum-19" nowrap>U.M.</th>
        <th class="colum-20" nowrap>Valor trabajo</th>
        <th class="colum-21" nowrap>Valor KM</th>
        <th class="colum-22" nowrap>Total KM</th>
        <th class="colum-23" nowrap>Costo Labor</th>
        <th class="colum-24" nowrap>Centro costo</th>
        <th class="colum-25" nowrap>Estado Facturación</th>
        <th class="colum-26" nowrap>Pago Técnico</th>
        <th class="colum-27" nowrap>Imágenes</th>
        <th class="colum-28" nowrap class="text-center">Estado</th>
    </thead>
    <tbody>
        <tr>
            <td class="text-center" colspan="18"><span class='text-blue'><h4>Cargando Tickets ... <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i></h4></span></td>
        </tr>
    </tbody> -->

                    <table class="table table-bordered table-striped table-sm" id="tbtickets2" style="display:none;">
                        <thead class="thead-dark">
                            <th nowrap></th>
                            <th nowrap>N° OT</th>
                            <th nowrap>Fecha Registro</th>
                            <th nowrap>Fecha Termino</th>
                            <th nowrap>Días Solicitud</th>
                            <!-- <th nowrap>Dias</th> -->
                            <th nowrap>Técnico</th>
                            <th nowrap>Cliente</th>
                            <th nowrap>Razon Social</th>
                            <th nowrap>Patente</th>
                            <th nowrap>Tipo Servicio</th>
                            <th nowrap>Dispositivo</th>
                            <th nowrap>Tipo Trabajo</th>
                            <th nowrap>Imei</th>
                            <!-- <th nowrap>Sello Seguridad</th>
        <th nowrap>Ch 1</th>
        <th nowrap>Ch 2</th>
        <th nowrap>Ch 3</th> -->
                            <!-- <th nowrap>Contacto</th>
        <th nowrap>Celular</th> -->
                            <th nowrap>Lugar</th>
                            <th nowrap>Comuna Orig.</th>
                            <th nowrap>Comuna Dest.</th>
                            <th nowrap>Kms Dist.</th>
                            <th nowrap>Descripción</th>
                            <th nowrap>Comentario</th>
                            <!-- <th nowrap>Fecha labor</th> -->
                            <th nowrap>U.M.</th>
                            <th nowrap>Valor trabajo</th>
                            <th nowrap>Valor KM</th>
                            <th nowrap>Total KM</th>
                            <th nowrap>Costo Labor</th>
                            <th nowrap>Centro costo</th>
                            <th nowrap>Estado Facturación</th>
                            <th nowrap>Pago Técnico</th>
                            <th nowrap>Imágenes</th>
                            <th nowrap class="text-center">Estado</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="18"><span class='text-blue'>
                                        <h4>Cargando Tickets ... <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i></h4>
                                    </span></td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <!-- form para finalizar ticket -->
        <div class="col-md-12 oculto" id="fcerrarticket">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Finalizar Ticket</h3>
                    <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" onclick="cancelarCierre();"><i class="fa fa-lg fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<script>
    $(document).ready(function() {

        $('#fil_select_cliente').addClass('form-control-sm');
        $('#fil_select_tecnico').addClass('form-control-sm');
        //$('#cliente').chosen();
        <?php
        if (isset($_REQUEST['nuevo'])) {
            //echo '$("#patente").chosen();';
        }

        ?>
    });



    $(function() {
        var urlactual = window.location;
        var ultimaclavevalor = urlactual["search"].lastIndexOf("&");

        estado = urlactual["search"].substring(ultimaclavevalor + 1, ultimaclavevalor.length);
        sepestado = estado.split("=");
        estadoticket = sepestado[1];
        if (estadoticket == "OK") {
            setTimeout(function() {
                $("#ticketok").fadeIn(2000).fadeOut(2000);
                history.pushState(null, "", "index.php?menu=tickets&idmenu=100");
            }, 100);
        }


        $("#btn_nticket").on("click", function() {
            $("#listadodetickets").hide();
            $("#fnuevoticket").show();
            $("#btn_nticket").attr("disabled", true);
        });
        getTabTickets('todos');
    });

    function validarTIC() {
        if ($("#cliente").val() == "") {
            alert("Error al registrar ticket, falta seleccionar un cliente");
            $("#cliente").addClass("input-error");
            return false;
        } else {
            $("#cliente").removeClass("input-error");
        }

        if ($("#patente").val() == 0) {
            alert("Error al registrar ticket, falta seleccionar una patante");
            $("#patente").addClass("input-error");
            return false;
        } else {
            $("#patente").removeClass("input-error");
        }

        if ($("#dispositivo").val() == "") {
            alert("Error al registrar ticket, falta seleccionar un dispositivo");
            $("#dispositivo").addClass("input-error");
            return false;
        } else {
            $("#dispositivo").removeClass("input-error");
        }

        if ($("#tipodtrab").val() == "") {
            alert("Error al registrar ticket, falta seleccionar el tipo de trabajo");
            $("#tipodtrab").addClass("input-error");
            return false;
        } else {
            $("#tipodtrab").removeClass("input-error");
        }
        if ($("input[name='contacto']").val() == "") {
            alert("Error al registrar ticket, falta completar el campo contacto");
            $("input[name='contacto']").addClass("input-error");
            return false;
        } else {
            $("input[name='contacto']").removeClass("input-error");
        }
        if ($("input[name='celular']").val() == "") {
            alert("Error al registrar ticket, falta completar el campo celular");
            $("input[name='celular']").addClass("input-error");
            return false;
        } else {
            $("input[name='celular']").removeClass("input-error");
        }
        if ($("input[name='lugar']").val() == "") {
            alert("Error al registrar ticket, falta completar el campo lugar");
            $("input[name='lugar']").addClass("input-error");
            return false;
        } else {
            $("input[name='lugar']").removeClass("input-error");
        }
    }


    $('.checkcol').on('change', function() {

        var id = this.children[0].id;
        var ch = 0;
        if ($("#" + id).prop("checked")) {
            ch = 1;
        }
        var datos = {
            'id': id.split("-")[1],
            'ch': ch
        };
        var send = JSON.stringify(datos);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'setConfColum',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(res) {

            },
            error: function(res) {
                console.log(res);
            },
            success: function(res) {
                if (res.respuesta == "success") {
                    toastr.success(res.mensaje);
                    if ($("#" + id).prop("checked")) {
                        $valueHide = true;
                        //$('.colum-' + id.split("-")[1]).show(); 
                    } else {
                        $valueHide = false;
                        //$('.colum-' + id.split("-")[1]).hide();
                    }

                    //console.log("valor ocultar : ", id.split("-")[1], $valueHide);
                    tableData.column(id.split("-")[1]).visible($valueHide);

                    //aplicarFiltros();
                } else {
                    toastr.error(res.mensaje);
                    if ($('#' + id).prop('checked')) {
                        $('#' + id).prop('checked', false)
                        $('.colum-' + id.split("-")[1]).hide();
                    } else {
                        $('#' + id).prop('checked', true)
                        $('.colum-' + id.split("-")[1]).show();
                    }
                }

            }
        });
    })

    function getcolumnas() {

        var datos = {
            'id': 1
        };
        var send = JSON.stringify(datos);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getcolumnas',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(res) {

            },
            error: function(res) {
                console.log(res);
            },
            success: function(res) {
                $columnasHide = [];
                $.each(res, function(i, item) {
                    if (item.valor == 1) {
                        valueHide = true;
                        $('#columna-' + item.columna).prop('checked', true);
                        $('.colum-' + item.columna).show();
                    } else {
                        valueHide = false;
                        $columnasHide.push(item.columna);
                        //$columnasHide[item.columna]= item.valor;
                        $('#columna-' + item.columna).prop('checked', false);
                        $('.colum-' + item.columna).hide();
                    }
                });

                Swal.close();
            }
        });

    }

    window.tickets;
    var tableData;
    var viajesSistema = [];
    var rowDataArray = {}; // Array para almacenar los datos de cada fila
    var $columnasHide;
    var iduser = <?php echo $_SESSION['cloux_new'] ?>

    function exportarDatatable(orderby, filccosto, filefact, filpagot, filcliente, filtecnico, fildesde, filhasta, filestado) {

        //console.log("carga de datatable");

        var searchValue = tableData.search();
        var order = tableData.order();
        var length = tableData.page.info().recordsTotal; //tableData.page.len();
        var start = tableData.page.info().start;
        var customFilters = {}; // Añadir cualquier filtro adicional que puedas tener

        // Puedes añadir más parámetros personalizados si los usas
        $('#customFilterForm').serializeArray().forEach(function(filter) {
            customFilters[filter.name] = filter.value;
        });

        var params = {
            search: {
                value: searchValue
            },
            order: order,
            length: length,
            start: start,
            customFilters: customFilters,
            orderby: orderby,
            filestado: filestado,
            filccosto: filccosto,
            filefact: filefact,
            filpagot: filpagot,
            filcliente: filcliente,
            filtecnico: filtecnico,
            fildesde: fildesde,
            filhasta: filhasta,
        };

        //return true;
        //ajax: 'operaciones.php?numero='+Math.floor(Math.random()*9999999)+'&operacion=listarVehiculosAll&retornar=no',
        //console.log("getTablaViajes : ");
        $.ajax({
            url: 'operaciones.php?numero=' + Math.floor(Math.random() * 9999999) + '&operacion=getTabTicketsFinalizadosDatatable&retornar=no',
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

        //return true;
        /*
        
        { data: 'tic_comuna_ori', 
            render: function(data, type, row) {
                // Reevaluar y sobrescribir el valor de 'columna1'
                // Aquí puedes agregar la lógica que necesitas

                var disa = 'disabled';
                if(iduser==1 || iduser==16){
                    disa = '';
                }

                var comunas = <?php echo json_encode($comunas) ?>;
                var data = '<option value="0">Seleccione</option>';
                if(comunas){
                    $.each(comunas, function(i, rowcomuna) {
                        var selected = '';
                        if(rowcomuna.comid == row['tic_comuna_ori']){
                            selected = 'selected';
                        }
                        data += '<option value="'+rowcomuna.comid+'" '+selected+'>'+rowcomuna.comnombre+'</option>';
                    });
                }
                

                let comorigen = '<select '+disa+' onchange="verkms('+row['id']+','+row['id']+')" id="comorigen_'+row['id']+'" class="form-control">'+(data)+'</select>';

                return comorigen; // Devuelve el valor original si no se debe cambiar
            },

                
                */




        var filteredData = data.map(function(item) {


            var comunas = <?php echo json_encode($comunas) ?>;
            let comdestino = 'Seleccione';
            var comorigen = 'Seleccione';

            if (comunas) {
                $.each(comunas, function(i, rowcomuna) {
                    if (rowcomuna.comid == item['tic_comuna_des']) {
                        comdestino = rowcomuna.comnombre;
                    }
                    if (rowcomuna.comid == item['tic_comuna_ori']) {
                        comorigen = rowcomuna.comnombre;
                    }

                });
            }

            let tic_id = item['tic_id'];
            let fechahorareg = item['fechahorareg'];
            let fechaagenda = item['fechaagenda'];
            let diferencia_dias = item['diferencia_dias'];
            let tecnico = item['tecnico'];
            let cliente = item['cliente'];
            let rs = item['rs'];
            let patente = item['patente'];
            let tipo_servicio = item['tipo_servicio'];
            let tipotrabajo = item['tipotrabajo'];
            let series = item['series'];
            let lugar = item['lugar'];

            var valounouotrokms = (item['mcom_kms'] == 'null' || item['mcom_kms'] == null || item['mcom_kms'] == '' ? 0 : item['mcom_kms']);
            if (item['tic_kmsdist'] != '0' && item['tic_kmsdist'] != 'null' && item['tic_kmsdist'] != '' && item['tic_kmsdist'] != null) {
                valounouotrokms = (item['tic_kmsdist'] == 'null' || item['tic_kmsdist'] == null || item['tic_kmsdist'] == '' ? 0 : item['tic_kmsdist'])
            }

            let kmdis = valounouotrokms;


            let dispositivo = item['dispositivo'];


            let descripcion = item['descripcion'];
            let comentario = item['comentario'];

            let um = ''
            if (item['tic_um'] == 1) {
                um = "CLP";
            } else if (item['tic_um'] == 2) {
                um = "UF";
            } else {
                um = "Seleccionar";
            }

            let vvtrabajo = item['vtrabajo'] == null ? '' : item['vtrabajo'];

            let valorkm = item['tic_kmsdist'];

            let tic_totalkm = item['tic_totalkm'];

            let tic_costolabor = item['tic_costolabor'];
            if (item['tic_valorkm'] == 0 && (item['vtrabajo'] == '0' || item['vtrabajo'] == '' || item['vtrabajo'] == null)) {
                tic_costolabor = 0;
            }

            let cCosto = "";
            if (parseInt(item['ccosto']) === 0) {
                cCosto = 'N/A';
            } else if (parseInt(item['ccosto']) === 1) {
                cCosto = 'Cliente';
            } else if (parseInt(item['ccosto']) === 2) {
                cCosto = 'Interno';
            } else if (parseInt(item['ccosto']) === 3) {
                cCosto = 'Interno';
            }
            //let cCosto = '<option value="0" '+opt1+'>N/A</option><option value="1" '+opt2+'>Cliente</option><option value="2" '+opt3+'>Interno</option></select>';


            let estadofact = "";
            if (parseInt(item['estadofact']) === 0) {
                estadofact = 'PENDIENTE';
            } else if (parseInt(item['estadofact']) === 1) {
                estadofact = 'OK';
            }

            let pagot = '';
            if (parseInt(item['pagot']) === 0) {
                pagot = 'N/A';
            } else if (parseInt(item['pagot']) === 1) {
                pagot = 'SI';
            } else if (parseInt(item['pagot']) === 2) {
                pagot = 'NO';
            }

            let estado = "";
            switch (parseInt(item.idestado)) {
                case 1:
                    estado = "PENDIENTE";
                    break;
                case 2:
                    estado = "AGENDADO";
                    break;
                case 3:
                    estado = "CERRADO";
                    break;
            }

            return {

                'N°': tic_id,
                'Fecha Registro': fechahorareg,
                'Fecha Termino': fechaagenda,
                'Dias Solicitud': diferencia_dias,
                'Tecnico': tecnico,
                'Cliente': cliente,
                'Razon Social': rs,
                'Patente': patente,
                'Tipo Servicio': tipo_servicio,
                'Dispositivo': dispositivo,
                'Tipo Trabajo': tipotrabajo,
                'Imei': series,
                'Lugar': lugar,
                'Comuna Orig.': comorigen,
                'Comuna Dest.': comdestino,
                'Kms Dist.': kmdis,
                'Descripcion': descripcion,
                'Comentario': comentario,
                'U.M.': um,
                'Valor Trabajo': vvtrabajo,
                'Valor KM': valorkm,
                'Total KM': tic_totalkm,
                'Costo Labor': tic_costolabor,
                'Centro costo': cCosto,
                'Estado Facturacion': estadofact,
                'Pago Tecnico': pagot,
                'Estado': estado,
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
            return {
                width: width + 20
            }; // Añade un margen para mejor visualización
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

        // Definir el estilo para el fondo de la fila
        const fillColor = {
            fgColor: {
                rgb: "FFFF00"
            }
        }; // Color de fondo amarillo

        // Aplicar el estilo a la fila 2 (índice 1 en base 0)
        const rowIndex = 1; // Índice de la fila que quieres colorear (segunda fila)
        const numberOfColumns = Object.keys(filteredData[0]).length;
        //console.log( "count : ",Object.keys(filteredData[0]).length );

        for (let col = 0; col < numberOfColumns; col++) {
            const cell_address = {
                c: col,
                r: rowIndex
            }; // {c: columna, r: fila}
            const cell_ref = XLSX.utils.encode_cell(cell_address);
            if (!ws[cell_ref]) ws[cell_ref] = {};
            ws[cell_ref].s = {
                fill: fillColor
            };
        }


        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Trabajos");

        // Obtener la fecha y hora actuales
        var now = new Date();
        var dateString = now.toISOString().slice(0, 10);
        var timeString = now.toTimeString().slice(0, 8).replace(/:/g, '');
        var filename = `TrabajosFinalizados_${dateString}_${timeString}.xlsx`;

        // Especifica el nombre del archivo
        XLSX.writeFile(wb, filename);

        // Habilita el botón después de la descarga
        $('#exportButton').prop('disabled', false);

        // Desaparecer SweetAlert
        Swal.close();
    }

    function cargarDatatable() {

        //console.log("columnas",$columnasHide);

        //getTabTickets('',ccosto,estadofact,pagot,cliente,tecnico,desde,hasta,estadofil);

        tableData = $('#tbtickets').DataTable({
            ajax: {
                url: '/cloux/operaciones.php',
                type: 'POST',
                data: function(d) {
                    // Parámetros adicionales que quieres enviar al servidor
                    d.operacion = 'getTabTicketsFinalizadosDatatable';
                    d.numero = Math.floor(Math.random() * 9999999);
                    d.retornar = 'no';
                    // Agregar aquí más parámetros si es necesario
                    //d.orderby    = orderby;

                    let filccosto = $('#fil_select_ccosto').val();
                    let filefact = $('#fil_select_efacturacion').val();

                    let fileestado = $('#fil_select_estado').val();



                    let filpagot = $('#fil_select_pagot').val();
                    let filcliente = $('#fil_select_cliente').val();
                    let filtecnico = $('#fil_select_tecnico').val();
                    let filestado = $('#fil_select_estado').val();
                    let fildesde = $('#fdesde').val();
                    let filhasta = $('#fhasta').val();

                    if (parseInt(filccosto) === 0) {
                        filccosto = '';
                    }
                    //if(parseInt(filefact)===0){
                    //    filefact = 0;
                    //}
                    if (parseInt(filefact) === 0) {
                        filefact = '';
                    }
                    if (parseInt(filcliente) === 0) {
                        filcliente = '';
                    }
                    if (parseInt(filtecnico) === 0) {
                        filtecnico = '';
                    }
                    if (fildesde === '0000-00-00') {
                        fildesde = '';
                    }
                    if (filhasta === '0000-00-00') {
                        filhasta = '';
                    }

                    d.filestado = filestado;
                    d.filccosto = filccosto;
                    d.filefact = $('#fil_select_efacturacion').val();
                    d.filpagot = filpagot;
                    d.filcliente = filcliente;
                    d.filtecnico = filtecnico;
                    d.fildesde = fildesde;
                    d.filhasta = filhasta;
                    d.fileestado = fileestado;


                    // console.log("filefact : ",filefact,$('#fil_select_efacturacion').val() );

                }
            },
            //ajax: 'operaciones.php?numero='+Math.floor(Math.random()*9999999)+'&operacion=getTabTicketsFinalizadosDatatable&retornar=no',
            processing: true,
            serverSide: true,
            // "scrollX": true,
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json',
                //thousands: '.' // Usar punto como separador de miles
                "decimal": ".",
                "thousands": "."
            },
            //"dom": '<"top"flp>rt<"bottom"ip><"clear">',
            "paging": true,
            "lengthChange": true,
            "lengthMenu": [
                [25, 50, 100, -1],
                [25, 50, 100, "Todos"]
            ],
            "pageLength": 25,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            //stateSave: true, // Habilitar el guardado del estado
            //stateDuration: -1, // Guardar el estado indefinidamente
            order: [
                [3, 'desc']
            ],
            "columnDefs": [

                {
                    "targets": 0,
                    "className": 'colum-0',
                    width: '100px'
                },
                {
                    "targets": 1,
                    "className": 'colum-1'
                },
                {
                    "targets": 2,
                    "className": 'colum-2'
                },
                {
                    "targets": 3,
                    "className": 'colum-3'
                },
                {
                    "targets": 4,
                    "className": 'colum-4'
                },
                {
                    "targets": 5,
                    "className": 'colum-5'
                },
                {
                    "targets": 6,
                    "className": 'colum-6'
                },
                {
                    "targets": 7,
                    "className": 'colum-7'
                },
                {
                    "targets": 8,
                    "className": 'colum-8'
                },
                {
                    "targets": 9,
                    "className": 'colum-9'
                },
                {
                    "targets": 10,
                    "className": 'colum-10'
                },
                {
                    "targets": 11,
                    "className": 'colum-11'
                },
                {
                    "targets": 12,
                    "className": 'colum-12'
                },
                {
                    "targets": 13,
                    "className": 'colum-13'
                },
                {
                    "targets": 14,
                    "className": 'colum-14 text-start'
                },
                {
                    "targets": 15,
                    "className": 'colum-15'
                },
                {
                    "targets": 16,
                    "className": 'colum-16'
                },
                {
                    "targets": 17,
                    "className": 'colum-17'
                },
                {
                    "targets": 18,
                    "className": 'colum-18'
                },
                {
                    "targets": 19,
                    "className": 'colum-19'
                },
                {
                    "targets": 20,
                    "className": 'colum-20'
                },
                {
                    "targets": 21,
                    "className": 'colum-21'
                },
                {
                    "targets": 22,
                    "className": 'colum-22'
                },
                {
                    "targets": 23,
                    "className": 'colum-23'
                },
                {
                    "targets": 24,
                    "className": 'colum-24'
                },
                {
                    "targets": 25,
                    "className": 'colum-25'
                },
                {
                    "targets": 26,
                    "className": 'colum-26'
                },
                {
                    "targets": 27,
                    "className": 'colum-27'
                },
                {
                    "targets": 28,
                    "className": 'colum-28'
                },
                {
                    "targets": 29,
                    "className": 'colum-29'
                },
                {
                    "targets": 30,
                    "className": 'colum-30'
                },
                {
                    "targets": $columnasHide, // Índices de las columnas que quieres ocultar
                    "visible": false, // Hacerlas invisibles
                    "searchable": false // Opcional: Desactivar la búsqueda en estas columnas
                },
                {
                    "targets": 0, // Índice de la columna que deseas modificar
                    "createdCell": function(td, cellData, rowData, row, col) {
                        $(td).addClass('aligned-spans'); // Agrega la clase a la celda
                    }
                },
                {
                    "targets": 2, // La columna que contiene las fechas
                    "render": function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            // Devuelve el dato tal cual está para mostrarlo en la tabla
                            return data;
                        } else if (type === 'sort') {
                            // Convertir fecha al formato "año-mes-día hora:minuto:segundo" para ordenamiento
                            return moment(data, "DD/MM/YYYY").format("YYYY-MM-DD");
                        }
                        return data;
                    }
                }
            ],
            "headerCallback": function(thead, data, start, end, display) {
                //$(thead).find('th').eq(0).addClass('aligned-spans'); // Agrega la clase al encabezado de la columna
            },
            "drawCallback": function(settings) {

                $('#tbtickets tbody tr').each(function() {

                    var data = tableData.row(this).data();

                    if (data && data.hasOwnProperty('tic_id')) {
                        // Asignar ID a la fila basado en la primera columna (Nombre)
                        var id = data['tic_id'];

                        $clase = 'fila-inactivo';
                        var colortr = '#82F585';
                        if (data['estadofact'] == 0) {
                            colortr = '#F5F182';
                            $clase = 'fila-activo';
                        }

                        $(this).addClass($clase);

                        $(this).attr('id', 'ftick' + id);
                    }

                });
            },

            "createdRow": function(row, data, dataIndex) {
                //$(row).addClass('fila-activo');
                //console.log('colum-'+dataIndex);
                //$(row).find('td:eq('+dataIndex+')').addClass('colum-'+dataIndex);  // Segunda columna
            },
            rowCallback: function(row, data) {
                if (data.tic_id) {
                    //console.log("se guarda data",data);
                    rowDataArray[data.tic_id] = data;

                    /*
                    // Agregar atributo 'data-id' con el valor de la columna 'id' (ajusta según tu estructura)
                    $(row).attr('id', 'tr2_'+data.tic_id);
                    $(row).attr('data-id', data.tic_id);
                    $(row).attr('class', '.filter-row');

                    // Abrir modal al hacer clic en la fila
                    $(row).on('click', function() {
                        var id = $(this).data('id');
                        // Lógica para abrir el modal con el ID específico
                        asocPedidosNew(1,id);
                    });*/
                }

            },
            columns: [{
                    title: '',
                    width: '100px',
                    data: 'acciones',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        data1 = '<div class="icon-container">' +
                            '<a href="#" class="icon-link" title="Eliminar">' +
                            '<i class="fa fa-trash"></i>' +
                            '</a>' +
                            '<a href="#" class="icon-link" title="Descarga PDF">' +
                            '<i class="fas fa-file-pdf"></i>' +
                            '</a>' +
                            '</div>';

                        data1 = "<div class='icon-container'>";
                        data1 += "    <a href='#' class='btn btn-sm btn-danger btn-circle icon-link' title='Eliminar' onclick='anularticket(\"" + row['id'] + "\")'>";
                        data1 += "        <i class='fa fa-trash'></i>";
                        data1 += "    </a>";

                        if (row['count_img'] > 0) {
                            data1 += "    <a href='#' class='btn btn-sm btn-info btn-circle icon-link' title='Descarga PDF' id='btnpdf" + row['id'] + "' onclick='descargarOT(\"" + row['id'] + "\")'>";
                            data1 += "        <i class='fas fa-file-pdf'></i>";
                            data1 += "    </a>";
                            data1 += "</div>";
                        }

                        data1 += "";
                        data1 += "";

                        $data = "";
                        data = "<span class='btn btn-sm btn-danger btn-circle' onclick='anularticket(\"" + row['id'] + "\")'>";
                        data += "<i class='fa fa-trash' aria-hidden='true'></i>";
                        data += "</span>";
                        data += "<span class='btn btn-sm btn-info btn-circle' id='btnpdf" + row['id'] + "' onclick='descargarOT(\"" + row['id'] + "\")'>";
                        data += " <i class='fas fa-file-pdf'></i>";
                        data += "</span>";
                        return data1; // Devuelve el valor original si no se debe cambiar
                    },
                    "defaultContent": ''
                },


                {
                    title: 'Estado APP',
                    data: 'idestadoapp',
                    "defaultContent": '',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        var estado = "";
                        var accion = "";

                        switch (parseInt(row['idestadoapp'])) {
                            case 3:
                                estado = "<span class='label label-success btn-rounded pointer'>CERRADO</span>";
                                break;

                            default:
                                estado = "<span class='label label-success btn-rounded pointer'>Finalizado APP</span>";
                                break;

                        }

                        return estado; // Devuelve el valor original si no se debe cambiar
                    }
                },
                {
                    title: 'N° OT',
                    data: 'tic_id',
                    "defaultContent": ''
                },
                {
                    title: 'Fecha Registro',
                    data: 'fechahorareg',
                    "defaultContent": ''
                },
                {
                    title: 'Fecha Término',
                    data: 'fechaagenda',
                    "defaultContent": ''
                },
                {
                    title: 'Días Solicitud',
                    data: 'diferencia_dias',
                    orderable: true,
                    "defaultContent": ''
                },
                {
                    title: 'Técnico',
                    data: 'tecnico',
                    "defaultContent": ''
                },
                {
                    title: 'Cliente',
                    data: 'cliente',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Razón Social',
                    data: 'rs',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Patente',
                    data: 'patente',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Tipo Servicio',
                    data: 'tipo_servicio',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Dispositivo',
                    data: 'dispositivo',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Tipo Trabajo',
                    data: 'tipotrabajo',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Imei',
                    data: 'series',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Accesorios',
                    data: 'accesorios',
                    render: function(data, type, row) {
                        let acc = '';
                        $.each(data, function(i, item) {
                            acc += '<span class="badge badge-pill badge-primary">(' + (i + 1) + ') ' + item.pro_nombre + '</span><br>';
                        });
                        return acc; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Lugar',
                    data: 'lugar',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Comuna Orig.',
                    data: 'tic_comuna_ori',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }

                        var comunas = <?php echo json_encode($comunas) ?>;
                        var data = '<option value="0">Seleccione</option>';
                        if (comunas) {
                            $.each(comunas, function(i, rowcomuna) {
                                var selected = '';
                                if (rowcomuna.comid == row['tic_comuna_ori']) {
                                    selected = 'selected';
                                }
                                data += '<option value="' + rowcomuna.comid + '" ' + selected + '>' + rowcomuna.comnombre + '</option>';
                            });
                        }


                        let comorigen = '<select style="width: 150px;" ' + disa + ' onchange="verkms(' + row['id'] + ',' + row['id'] + ')" id="comorigen_' + row['id'] + '" class="form-control">' + (data) + '</select>';

                        return comorigen; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },

                {
                    title: 'Comuna Dest.',
                    data: 'tic_comuna_des',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }

                        var comunas = <?php echo json_encode($comunas) ?>;
                        var data = '<option value="0">Seleccione</option>';
                        if (comunas) {
                            $.each(comunas, function(i, rowcomuna) {
                                var selected = '';
                                if (rowcomuna.comid == row['tic_comuna_des']) {
                                    selected = 'selected';
                                }
                                data += '<option value="' + rowcomuna.comid + '" ' + selected + '>' + rowcomuna.comnombre + '</option>';
                            });
                        }

                        let comdestino = '<select style="width: 150px;" ' + disa + ' onchange="verkms(' + row['id'] + ',' + row['id'] + ')" id="comdestino_' + row['id'] + '"  class="form-control">' + data + '</select>';

                        return comdestino; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Kms Dist.',
                    data: 'tic_kmsdist',
                    //tic_valorkm
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }

                        var valounouotrokms = (row['mcom_kms'] == 'null' || row['mcom_kms'] == null || row['mcom_kms'] == '' ? 0 : row['mcom_kms']);
                        if (row['tic_kmsdist'] != '0' && row['tic_kmsdist'] != 'null' && row['tic_kmsdist'] != '' && row['tic_kmsdist'] != null) {
                            valounouotrokms = (row['tic_kmsdist'] == 'null' || row['tic_kmsdist'] == null || row['tic_kmsdist'] == '' ? 0 : row['tic_kmsdist'])
                        }

                        let kmdis = '<input ' + disa + ' onblur="updatealb(' + row['id'] + ',' + row['id'] + ')" type="text" class="form-control" style="width:120px;text-align:right;" id="kmdistin_' + row['id'] + '" value="' + valounouotrokms + '">';

                        //let valorkm = '<input '+disa+' type="text" class="form-control" name="'+row['tic_kmsdist']+'" onblur="updatevalor(0,'+row['id']+','+row['id']+')" style="width:120px;text-align:right;" id="valorkm_'+row['id']+'" value="'+row['tic_kmsdist']+'">';

                        return kmdis; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },




                {
                    title: 'Descripción',
                    data: 'descripcion',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Comentario',
                    data: 'comentario',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'U.M.',
                    data: 'tic_um',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }

                        var u = '';
                        var c = '';
                        var n = '';
                        if (row['tic_um'] == 1) {
                            u = '';
                            c = 'selected';
                            n = '';
                        } else if (row['tic_um'] == 2) {
                            u = 'selected';
                            c = '';
                            n = '';
                        } else {
                            u = 'selected';
                            c = '';
                            n = '';
                        }

                        //let kmdis = '<input '+disa+' onblur="updatealb('+row['id']+','+row['id']+')" type="text" class="form-control" style="width:120px;text-align:right;" id="kmdistin_'+row['id']+'" value="'+valounouotrokms+'">';
                        let um = '<select style="width: 70px;" ' + disa + ' id="um_' + row['id'] + '" style="width:100px;" class="form-control"  onchange="updatevalor(1,' + row['id'] + ',' + row['id'] + ')"><option value="0" ' + n + '>Seleccionar</option><option value="1" ' + c + '>CLP</option><option value="2" ' + u + '>UF</option></select>';

                        return um; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },


                {
                    title: 'Valor trabajo',
                    data: 'vtrabajo',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas
                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }
                        vvtrabajo = row['vtrabajo'] == null ? '' : row['vtrabajo'];
                        let vtrabajo = '<input ' + disa + ' type="text" class="form-control" name="' + vvtrabajo + '" id-tr="jaime" onkeypress="validarTecla(event)" onblur="updatevalor(0,' + row['id'] + ',' + row['id'] + ')" style="width:120px;text-align:right;" id="vtrabajo_' + row['id'] + '" value="' + vvtrabajo + '">';

                        return vtrabajo; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },



                {
                    title: 'Valor KM',
                    data: 'valorkm',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas
                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }
                        let valorkm = '<input ' + disa + ' type="text" class="form-control" name="' + row['tic_kmsdist'] + '" onblur="updatevalor(0,' + row['id'] + ',' + row['id'] + ')" style="width:120px;text-align:right;" id="valorkm_' + row['id'] + '" value="' + row['tic_kmsdist'] + '">';

                        return valorkm; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Total KM',
                    data: 'tic_totalkm',
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Costo Labor',
                    data: 'tic_costolabor',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        if (row['tic_valorkm'] == 0 && (row['vtrabajo'] == '0' || row['vtrabajo'] == '' || row['vtrabajo'] == null)) {
                            row['tic_costolabor'] = 0;
                        }

                        return row['tic_costolabor']; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Centro costo',
                    data: 'ccosto',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }

                        let opt1 = '';
                        let opt2 = '';
                        let opt3 = '';
                        let opt4 = '';
                        if (parseInt(row['ccosto']) === 0) {
                            opt1 = 'selected'
                        } else if (parseInt(row['ccosto']) === 1) {
                            opt2 = 'selected'
                        } else if (parseInt(row['ccosto']) === 2) {
                            opt3 = 'selected'
                        } else if (parseInt(row['ccosto']) === 3) {
                            opt4 = 'selected'
                        }
                        let cCosto = '<select style="width: 130px;" ' + disa + ' id="ccosto_' + row['id'] + '" class="form-control" onchange="selectccosto(' + row['id'] + ')"><option value="0" ' + opt1 + '>N/A</option><option value="1" ' + opt2 + '>Cliente</option><option value="2" ' + opt3 + '>Interno</option></select>';

                        return cCosto; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Estado Facturación',
                    data: 'estadofact',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }

                        opt1 = '';
                        opt2 = '';
                        opt3 = '';
                        if (parseInt(row['estadofact']) === 0) {
                            opt1 = 'selected'
                        } else if (parseInt(row['estadofact']) === 1) {
                            opt2 = 'selected'
                        }
                        let estadofact = '<select style="width: 140px;" ' + disa + ' id="estadofact_' + row['id'] + '" onchange="selectEstadoFact(' + row['id'] + ')" class="form-control"><option value="0" ' + opt1 + '>PENDIENTE</option><option value="1" ' + opt2 + '>OK</option></select>';

                        return estadofact; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Pago Técnico',
                    data: 'pagot',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }

                        opt1 = '';
                        opt2 = '';
                        opt3 = '';
                        if (parseInt(row['pagot']) === 0) {
                            opt1 = 'selected'
                        } else if (parseInt(row['pagot']) === 1) {
                            opt2 = 'selected'
                        } else if (parseInt(row['pagot']) === 2) {
                            opt3 = 'selected'
                        }
                        let pagot = '<select style="width: 120px;" ' + disa + ' id="pagot_' + row['id'] + '" onchange="selectPagoTecnico(' + row['id'] + ')" class="form-control"><option value="0" ' + opt1 + '>N/A</option><option value="1" ' + opt2 + '>SI</option><option value="2" ' + opt3 + '>NO</option></select>';

                        return pagot; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Imágenes',
                    data: 'img1',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas
                        let verImg = '<span class="no">Sin Imagen</span>';
                        let existeImg = false;
                        if (row['img1'] != '' || row['img1'] != null) {
                            existeImg = true;
                        }
                        if (row['img2'] != '' || row['img2'] != null) {
                            existeImg = true;
                        }
                        if (row['img3'] != '' || row['img3'] != null) {
                            existeImg = true;
                        }
                        if (row['img4'] != '' || row['img4'] != null) {
                            existeImg = true;
                        }
                        if (row['img5'] != '' || row['img5'] != null) {
                            existeImg = true;
                        }

                        if (existeImg) {
                            verImg = '<span class="pointer" style="color:#0182C0;cursor:pointer" onclick="verImagenes(' + row['id'] + ')"><i class="fa fa-eye" aria-hidden="true"></i></span>';
                        }


                        return verImg; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },
                {
                    title: 'Estado',
                    data: 'idestado',
                    render: function(data, type, row) {
                        // Reevaluar y sobrescribir el valor de 'columna1'
                        // Aquí puedes agregar la lógica que necesitas

                        var estado = "";
                        var accion = "";

                        switch (parseInt(row['idestado'])) {

                            case 1:
                                estado = "<span class='label label-danger btn-rounded'>PENDIENTE</span>";
                                accion = "<span class='label label-default btn-rounded pointer' onclick='agendarTicket(\"" + row['id'] + "\")'>AGENDAR</span>";
                                break;

                            case 2:
                                estado = "<span class='label label-warning btn-rounded pointer' onclick='ModificarAgenda(\"" + row['id'] + "\")'>AGENDADO</span>";
                                accion = "<span class='label label-success btn-rounded pointer' onclick='terminarTicket(\"" + row['id'] + "\")'>FINALIZAR</span>";
                                break;

                            case 3:
                                estado = "<span class='label label-success btn-rounded pointer'>CERRADO</span>";
                                accion = "<span class='label label-info btn-rounded pointer' onclick='DetalleTicket(\"" + row['id'] + "\")'>DETALLE</span>";
                                break;

                        }

                        return estado; // Devuelve el valor original si no se debe cambiar
                    },
                    orderable: false,
                    "defaultContent": ''
                },

                //{ data: 'span3', orderable: false, "defaultContent":'' },
            ],
        });

        // Ocultar las columnas basadas en el array
        if ($columnasHide) {
            $columnasHide.forEach(function(index) {
                tableData.column(index).visible(false);
            });
        }


    }

    var ticketsAll = [];

    function getTabTickets(orderby = 'todos', filccosto = '', filefact = '', filpagot = '', filcliente = '', filtecnico = '', fildesde = '', filhasta = '', filestado = '') {

        getcolumnas();
        // Ejemplo de clearTimeout
        var timeoutId = setTimeout(function() {
            cargarDatatable();
        }, 500);

        return true;
        Swal.fire({
            title: 'Cargando...',
            html: 'Por favor, espere.',
            allowOutsideClick: false,
            showConfirmButton: false,
            showLoaderOnConfirm: true,
            timer: 50000,
            timerProgressBar: true,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });


        $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getTabTicketsFinalizados',
                orderby: orderby,
                filestado: filestado,
                filccosto: filccosto,
                filefact: filefact,
                filpagot: filpagot,
                filcliente: filcliente,
                filtecnico: filtecnico,
                fildesde: fildesde,
                filhasta: filhasta,
                retornar: 'no'
            },
            function(data) {
                ticketsAll = tickets = $.parseJSON(data);
                console.log(tickets[0]);
                //console.log(tickets);
                ftickets = "";
                $.each(tickets, function(index, valor) {
                    estado = "";
                    accion = "";
                    switch (parseInt(valor.idestado)) {
                        case 1:
                            estado = "<span class='label label-danger btn-rounded'>PENDIENTE</span>";
                            accion = "<span class='label label-default btn-rounded pointer' onclick='agendarTicket(\"" + valor.id + "\")'>AGENDAR</span>";
                            break;
                        case 2:
                            estado = "<span class='label label-warning btn-rounded pointer' onclick='ModificarAgenda(\"" + index + "\")'>AGENDADO</span>";
                            accion = "<span class='label label-success btn-rounded pointer' onclick='terminarTicket(\"" + index + "\")'>FINALIZAR</span>";
                            break;
                        case 3:
                            estado = "<span class='label label-success btn-rounded pointer'>CERRADO</span>";
                            accion = "<span class='label label-info btn-rounded pointer' onclick='DetalleTicket(\"" + index + "\")'>DETALLE</span>";
                            break;
                    }

                    if (parseInt(valor.idestado) === 3 || parseInt(valor.idestado) === 6) {

                        // dias transcurridos
                        /*if(parseInt(valor.diastranscurridos) > 10){
                        dias="<span class='label label-danger btn-rounded pointer'>"+valor.diastranscurridos+"</span>";
                        }else if(parseInt(valor.diastranscurridos) > 5 && parseInt(valor.diastranscurridos)  <=10){
                        dias="<span class='label label-warning btn-rounded pointer'>"+valor.diastranscurridos+"</span>";	
                        }else{
                        dias="<span class='label label-success btn-rounded pointer'>"+valor.diastranscurridos+"</span>";	
                        }*/
                        var disa = 'disabled';
                        if (iduser == 1 || iduser == 16) {
                            disa = '';
                        }

                        var u = '';
                        var c = '';
                        var n = '';
                        if (valor.tic_um == 1) {
                            u = '';
                            c = 'selected';
                            n = '';
                        } else if (valor.tic_um == 2) {
                            u = 'selected';
                            c = '';
                            n = '';
                        } else {
                            u = 'selected';
                            c = '';
                            n = '';
                        }

                        let um = '<select ' + disa + ' id="um_' + index + '" style="width:100px;" class="form-control" onchange="updatevalor(1,' + index + ',' + valor.id + ')"><option value="0" ' + n + '>Seleccionar</option><option value="1" ' + c + '>CLP</option><option value="2" ' + u + '>UF</option></select>';

                        let opt1 = '';
                        let opt2 = '';
                        let opt3 = '';
                        let opt4 = '';
                        if (parseInt(valor.ccosto) === 0) {
                            opt1 = 'selected'
                        } else if (parseInt(valor.ccosto) === 1) {
                            opt2 = 'selected'
                        } else if (parseInt(valor.ccosto) === 2) {
                            opt3 = 'selected'
                        } else if (parseInt(valor.ccosto) === 3) {
                            opt4 = 'selected'
                        }
                        let cCosto = '<select ' + disa + ' id="ccosto_' + index + '" class="form-control" onchange="selectccosto(' + index + ')"><option value="0" ' + opt1 + '>N/A</option><option value="1" ' + opt2 + '>Cliente</option><option value="2" ' + opt3 + '>Interno</option></select>';

                        opt1 = '';
                        opt2 = '';
                        opt3 = '';
                        if (parseInt(valor.pagot) === 0) {
                            opt1 = 'selected'
                        } else if (parseInt(valor.pagot) === 1) {
                            opt2 = 'selected'
                        } else if (parseInt(valor.pagot) === 2) {
                            opt3 = 'selected'
                        }
                        let pagot = '<select ' + disa + ' id="pagot_' + index + '" onchange="selectPagoTecnico(' + index + ')" class="form-control"><option value="0" ' + opt1 + '>N/A</option><option value="1" ' + opt2 + '>SI</option><option value="2" ' + opt3 + '>NO</option></select>';

                        opt1 = '';
                        opt2 = '';
                        opt3 = '';
                        if (parseInt(valor.estadofact) === 0) {
                            opt1 = 'selected'
                        } else if (parseInt(valor.estadofact) === 1) {
                            opt2 = 'selected'
                        }
                        let estadofact = '<select ' + disa + ' id="estadofact_' + index + '" onchange="selectEstadoFact(' + index + ')" class="form-control"><option value="0" ' + opt1 + '>PENDIENTE</option><option value="1" ' + opt2 + '>OK</option></select>';

                        let vvtrabajo = '';
                        let vkmsdist = '';
                        if (parseInt(valor.vtrabajo) !== 0) {
                            /*const formatterPeso = new Intl.NumberFormat('es-CL', {
                                style: 'currency',
                                currency: 'CLP',
                                minimumFractionDigits: 0
                            });
                            vvtrabajo = formatterPeso.format(parseInt(valor.vtrabajo));*/
                            vvtrabajo = valor.vtrabajo;
                        } else {
                            if (valor.vtrabajo == '0') {
                                vvtrabajo = '0';
                            } else {
                                vvtrabajo = valor.vtrabajo;
                            }

                        }

                        if (vvtrabajo == null || vvtrabajo == undefined) {
                            vvtrabajo = '';
                        }

                        /*let vtrabajo = '<input type="text" class="form-control" onblur="resetData('+index+')" onkeyup="activarButton('+index+')" style="width:120px;text-align:right;" id="vtrabajo_'+index+'" value="'+vvtrabajo+'"><span id="btn_reloadvalor_'+index+'" onclick="enviarValorNuevo('+index+')" class="btn btn-warning btn-cirle-s oculto" style="color:white;"><i class="fa fa-refresh"></i></span>';*/

                        let vtrabajo = '<input ' + disa + ' type="text" class="form-control" name="' + vvtrabajo + '"  onblur="updatevalor(0,' + index + ',' + valor.id + ')" style="width:120px;text-align:right;" id="vtrabajo_' + index + '" value="' + vvtrabajo + '">';

                        var valounouotrokms = (valor.mcom_kms == 'null' || valor.mcom_kms == null || valor.mcom_kms == '' ? 0 : valor.mcom_kms);
                        if (valor.tic_kmsdist != '0' && valor.tic_kmsdist != 'null' && valor.tic_kmsdist != '' && valor.tic_kmsdist != null) {
                            valounouotrokms = (valor.tic_kmsdist == 'null' || valor.tic_kmsdist == null || valor.tic_kmsdist == '' ? 0 : valor.tic_kmsdist)
                        }

                        let kmdis = '<input ' + disa + ' onblur="updatealb(' + valor.id + ',' + index + ')" type="text" class="form-control" style="width:120px;text-align:right;" id="kmdistin_' + index + '" value="' + valounouotrokms + '">';

                        var comunas = <?php echo json_encode($comunas) ?>;
                        var optioncomunasori = '<option value="0">Seleccione</option>';
                        $.each(comunas, function(i, rowcomuna) {
                            var selected = '';
                            if (rowcomuna.comid == valor.tic_comuna_ori) {
                                selected = 'selected';
                            }
                            optioncomunasori += '<option value="' + rowcomuna.comid + '" ' + selected + '>' + rowcomuna.comnombre + '</option>';
                        });

                        var optioncomunasdes = '<option value="0">Seleccione</option>';
                        $.each(comunas, function(i, rowcomuna) {
                            var selected = '';
                            if (rowcomuna.comid == valor.tic_comuna_des) {
                                selected = 'selected';
                            }
                            optioncomunasdes += '<option value="' + rowcomuna.comid + '" ' + selected + '>' + rowcomuna.comnombre + '</option>';
                        });

                        let comorigen = '<select ' + disa + ' onchange="verkms(' + index + ',' + valor.id + ')" id="comorigen_' + index + '" class="form-control">' + optioncomunasori + '</select>';

                        let comdestino = '<select ' + disa + ' onchange="verkms(' + index + ',' + valor.id + ')" id="comdestino_' + index + '"  class="form-control">' + optioncomunasdes + '</select>';

                        let valorkm = '<input ' + disa + ' type="text" class="form-control" name="' + valor.tic_valorkm + '" onblur="updatevalor(0,' + index + ',' + valor.id + ')" style="width:120px;text-align:right;" id="valorkm_' + index + '" value="' + valor.tic_valorkm + '">';

                        let verImg = '<span class="no">Sin Imagen</span>';
                        let existeImg = false;
                        if (valor.img1 != '' || valor.img1 != null) {
                            existeImg = true;
                        }
                        if (valor.img2 != '' || valor.img2 != null) {
                            existeImg = true;
                        }
                        if (valor.img3 != '' || valor.img3 != null) {
                            existeImg = true;
                        }
                        if (valor.img4 != '' || valor.img4 != null) {
                            existeImg = true;
                        }
                        if (valor.img5 != '' || valor.img5 != null) {
                            existeImg = true;
                        }

                        if (existeImg) {
                            verImg = '<span class="pointer" style="color:#0182C0;cursor:pointer" onclick="verImagenes(' + index + ')"><i class="fa fa-eye" aria-hidden="true"></i></span>';
                        }

                        var colortr = '#82F585';
                        if (valor.estadofact == 0) {
                            colortr = '#F5F182';
                        }
                        /* <td>"+valor.diastranscurridos+"</td>*/

                        /*ftickets+="<tr style='background-color:"+colortr+"' id='ftick"+valor.id+"'><td><span class='btn btn-sm btn-danger btn-circle' onclick='anularticket(\""+valor.id+"\")'><i class='fa fa-trash' aria-hidden='true'></i></span></td><td>"+valor.fechahorareg+"</td><td>"+valor.tecnico+"</td><td>"+valor.cliente+"</td><td>"+valor.patente+"</td><td>"+valor.dispositivo+"</td><td>"+valor.tipotrabajo+"</td><td>"+valor.series+"</td><td>"+valor.tic_sseguridad+"</td><td>"+valor.ch_1+"</td><td>"+valor.ch_2+"</td><td>"+valor.ch_3+"</td><td>"+valor.lugar+"</td><td>"+valor.comunaorigen+"</td><td>"+valor.comunades+"</td><td style='font-weight:bold;'>"+valor.mcom_kms+"</td><td style='font-weight:bold;'>"+valor.descripcion+"</td><td style='font-weight:bold;'>"+valor.comentario+"</td><td nowrap style='font-weight:bold;'>"+valor.agenda+"</td><td style='font-weight:bold;'>"+um+"</td><td id='tdvalor_"+index+"' style='font-weight:bold;'>"+vtrabajo+"</td><td style='font-weight:bold;'>"+cCosto+"</td><td style='font-weight:bold;'>"+estadofact+"</td><td style='font-weight:bold;'>"+pagot+"</td><td class='text-center' style='font-weight:bold;'>"+verImg+"</td><td class='text-center' style='font-weight:bold;'>"+estado+"</td></tr>";*/

                        if (valor.tic_valorkm == 0 && (valor.vtrabajo == '0' || valor.vtrabajo == '' || valor.vtrabajo == null)) {
                            valor.tic_costolabor = 0;
                        }

                        /*ftickets+="<tr style='background-color:"+colortr+"' id='ftick"+valor.id+"'><td><span class='btn btn-sm btn-danger btn-circle' onclick='anularticket(\""+valor.id+"\")'><i class='fa fa-trash' aria-hidden='true'></i></span></td><td>"+valor.fechahorareg+"</td><td>"+valor.fechaagenda+"</td><td>"+valor.diferencia_dias+"</td><td>"+valor.tecnico+"</td><td>"+valor.cliente+"</td><td>"+valor.rs+"</td><td>"+valor.patente+"</td><td>"+valor.tipo_servicio+"</td><td>"+valor.dispositivo+"</td><td>"+valor.tipotrabajo+"</td><td>"+valor.series+"</td><td>"+valor.tic_sseguridad+"</td><td>"+valor.ch_1+"</td><td>"+valor.ch_2+"</td><td>"+valor.ch_3+"</td><td>"+valor.lugar+"</td><td>"+valor.comunaorigen+"</td><td>"+valor.comunades+"</td><td style='font-weight:bold;' id='kmdist_"+index+"'>"+(valor.mcom_kms=='null' || valor.mcom_kms==null || valor.mcom_kms==''?0:valor.mcom_kms)+"</td><td style='font-weight:bold;'>"+valor.descripcion+"</td><td style='font-weight:bold;'>"+valor.comentario+"</td><td style='font-weight:bold;'>"+um+"</td><td id='tdvalor_"+index+"' style='font-weight:bold;'>"+vtrabajo+"</td><td id='tdvalorkm_"+index+"' style='font-weight:bold;'>"+valorkm+"</td><td id='totalkm_"+index+"' style='font-weight:bold;'>"+valor.tic_totalkm+"</td><td id='costolabor_"+index+"' style='font-weight:bold;'>"+valor.tic_costolabor+"</td><td style='font-weight:bold;'>"+cCosto+"</td><td style='font-weight:bold;'>"+estadofact+"</td><td style='font-weight:bold;'>"+pagot+"</td><td class='text-center' style='font-weight:bold;'>"+verImg+"</td><td class='text-center' style='font-weight:bold;'>"+estado+"</td></tr>";*/
                        /*ftickets+="<tr style='background-color:"+colortr+"' id='ftick"+valor.id+"'><td><span class='btn btn-sm btn-danger btn-circle' onclick='anularticket(\""+valor.id+"\")'><i class='fa fa-trash' aria-hidden='true'></i></span></td><td class='colum-1'>"+valor.fechahorareg+"</td><td class='colum-2'>"+valor.fechaagenda+"</td><td class='colum-3'>"+valor.diferencia_dias+"</td><td class='colum-4'>"+valor.tecnico+"</td><td class='colum-5'>"+valor.cliente+"</td><td class='colum-6'>"+valor.rs+"</td><td class='colum-7'>"+valor.patente+"</td><td class='colum-8'>"+valor.tipo_servicio+"</td><td class='colum-9'>"+valor.dispositivo+"</td><td class='colum-10'>"+valor.tipotrabajo+"</td><td class='colum-11'>"+valor.series+"</td><td class='colum-12'>"+valor.lugar+"</td><td class='colum-13'>"+valor.comunaorigen+"</td><td class='colum-14'>"+valor.comunades+"</td><td  class='colum-15' style='font-weight:bold;' id='kmdist_"+index+"'>"+(valor.mcom_kms=='null' || valor.mcom_kms==null || valor.mcom_kms==''?0:valor.mcom_kms)+"</td><td class='colum-16' style='font-weight:bold;'>"+valor.descripcion+"</td><td class='colum-17' style='font-weight:bold;'>"+valor.comentario+"</td><td class='colum-18' style='font-weight:bold;'>"+um+"</td><td class='colum-19' id='tdvalor_"+index+"' style='font-weight:bold;'>"+vtrabajo+"</td><td class='colum-20' id='tdvalorkm_"+index+"' style='font-weight:bold;'>"+valorkm+"</td><td class='colum-21' id='totalkm_"+index+"' style='font-weight:bold;'>"+valor.tic_totalkm+"</td><td class='colum-22' id='costolabor_"+index+"' style='font-weight:bold;'>"+valor.tic_costolabor+"</td><td class='colum-23' style='font-weight:bold;'>"+cCosto+"</td><td class='colum-24' style='font-weight:bold;'>"+estadofact+"</td><td style='font-weight:bold;' class='colum-25'>"+pagot+"</td><td class='colum-26' text-center' style='font-weight:bold;'>"+verImg+"</td><td class='colum-27'text-center' style='font-weight:bold;'>"+estado+"</td></tr>";*/
                        ftickets += "<tr style='background-color:" + colortr + "' id='ftick" + valor.id + "'>" +
                            "<td nowrap><span class='btn btn-sm btn-danger btn-circle' onclick='anularticket(\"" + valor.id + "\")'>" +
                            "<i class='fa fa-trash' aria-hidden='true'></i></span> <span class='btn btn-sm btn-info btn-circle' id='btnpdf" + valor.id + "' onclick='descargarOT(\"" + valor.id + "\")'>" +
                            "<i class='fas fa-file-pdf'></i></span></td><td class='colum-1'>" + valor.tic_id + "" +
                            "</td>" +
                            "<td class='colum-1'>" + valor.fechahorareg + "</td><td class='colum-2'>" + valor.fechaagenda + "</td>" +
                            "<td class='colum-3'>" + valor.diferencia_dias + "</td><td class='colum-4'>" + valor.tecnico + "</td>" +
                            "<td class='colum-5'>" + valor.cliente + "</td><td class='colum-6'>" + valor.rs + "</td>" +
                            "<td class='colum-7'>" + valor.patente + "</td><td class='colum-8'>" + valor.tipo_servicio + "</td>" +
                            "<td class='colum-9'>" + valor.dispositivo + "</td><td class='colum-10'>" + valor.tipotrabajo + "</td>" +
                            "<td class='colum-11'>" + valor.series + "</td><td class='colum-12'>" + valor.lugar + "</td>" +
                            "<td class='colum-13'>" + comorigen + "</td><td class='colum-14'>" + comdestino + "</td>" +
                            "<td  class='colum-15' style='font-weight:bold;' id='kmdist_" + index + "'>" + kmdis + "</td>" +
                            "<td class='colum-16' style='font-weight:bold;'>" + valor.descripcion + "</td>" +
                            "<td class='colum-17' style='font-weight:bold;'>" + valor.comentario + "</td>" +
                            "<td class='colum-18' style='font-weight:bold;'>" + um + "</td>" +
                            "<td class='colum-19' id='tdvalor_" + index + "' style='font-weight:bold;'>" + vtrabajo + "</td>" +
                            "<td class='colum-20' id='tdvalorkm_" + index + "' style='font-weight:bold;'>" + valorkm + "</td>" +
                            "<td class='colum-21' id='totalkm_" + index + "' style='font-weight:bold;'>" + valor.tic_totalkm + "</td>" +
                            "<td class='colum-22' id='costolabor_" + index + "' style='font-weight:bold;'>" + valor.tic_costolabor + "</td>" +
                            "<td class='colum-23' style='font-weight:bold;'>" + cCosto + "</td>" +
                            "<td class='colum-24' style='font-weight:bold;'>" + estadofact + "</td>" +
                            "<td style='font-weight:bold;' class='colum-25'>" + pagot + "</td>" +
                            "<td class='colum-26' text-center' style='font-weight:bold;'>" + verImg + "</td>" +
                            "<td class='colum-27'text-center' style='font-weight:bold;'>" + estado + "</td>" +
                            "</tr>";
                    }
                });

                $("#tbtickets tbody").html(ftickets);
                $("#tbtickets2 tbody").html(ftickets);

                tableData = $('#tbtickets').DataTable({
                    "language": {
                        url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'
                    },
                    "paging": true,
                    "columnDefs": [{
                        "targets": 2, // La columna que contiene las fechas
                        "render": function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                // Devuelve el dato tal cual está para mostrarlo en la tabla
                                return data;
                            } else if (type === 'sort') {
                                // Convertir fecha al formato "año-mes-día hora:minuto:segundo" para ordenamiento
                                return moment(data, "DD/MM/YYYY").format("YYYY-MM-DD");
                            }
                            return data;
                        }
                    }],
                    "order": [
                        [2, "desc"]
                    ],
                    "lengthChange": true,
                    "lengthMenu": [
                        [100, -1],
                        [100, "Todos"]
                    ],
                    "pageLength": 100,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false
                });

                getcolumnas();


            });


    }

    function descargarOT(idticket) {

        $("#btnpdf" + idticket).attr('disabled', true).html("<i class='fa fa-spinner fa-pulse fa-1x fa-fw' aria-hidden='true'></i>");
        let formulario = new FormData();
        formulario.append('operacion', 'getOTPDF');
        formulario.append('idticket', idticket);
        formulario.append('retornar', 'no');
        $.ajax({
            method: "POST",
            url: "operaciones.php",
            data: formulario,
            processData: false,
            contentType: false
        }).done(function(data) {
            $("#btnpdf" + idticket).attr('disabled', false).html("<i class='fas fa-file-pdf'></i>");
            var url = "data:application/pdf;base64," + data;
            fetch(url)
                .then(function(response) {
                    return response.blob();
                })
                .then(function(myblob) {
                    var urlblob = URL.createObjectURL(myblob);
                    var link = document.createElement('a');
                    link.href = urlblob;
                    link.download = "orden_trabajo_n" + idticket + ".pdf";
                    link.dispatchEvent(new MouseEvent('click'));
                });
        }).fail(function(error) {
            //$('#btnsetExcellibro').html('<i class="fas fa-book-reader"></i>').attr({'disabled': false})
        });
    }

    function verkms(i = 0, id = 0) {
        if ($("#comorigen_" + i).val() != 0 && $("#comdestino_" + i).val() != 0) {
            traematriz($("#comorigen_" + i).val(), $("#comdestino_" + i).val(), id, i)
        } else {
            $("#kmdistin_" + i).val('0')
            updatealb(id, i)
        }
    }

    function traematriz(idorigen = 0, iddestino = 0, id = 0, i = 0) {
        env = {
            'idorigen': idorigen,
            'iddestino': iddestino
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'traematriz',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(res) {},
            error: function(res) {},
            success: function(res) {
                if (res.respuesta == 'success') {
                    $("#kmdistin_" + i).val(res.km)
                } else {
                    $("#kmdistin_" + i).val(res.km)
                }
                updatealb(id, i);
            }
        })
    }

    function updatealb(id = 0, i = 0) {
        env = {
            'id': id,
            'origen': $("#comorigen_" + i).val(),
            'destino': $("#comdestino_" + i).val(),
            'kms': $("#kmdistin_" + i).val()
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'updatealb',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(res) {},
            error: function(res) {},
            success: function(res) {
                if (res.respuesta == 'success') {
                    toastr.success(res.mensaje);
                } else {
                    toastr.error(res.mensaje);
                }
            }
        })
    }

    function anularticket(i) {
        Swal.fire({
            title: '\u00BFEstas seguro de anular el Registro?',
            text: "Este desaparecera de la lista a menos que filtres por anulados",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: "green",
            cancelButtonColor: "red",
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                annular(i);
            }
        })
    }

    function annular(i) {
        env = {
            'idtick': i
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'annular',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(res) {},
            error: function(res) {},
            success: function(res) {
                if (res.respuesta == 'success') {
                    toastr.success(res.mensaje);
                    aplicarFiltros()
                } else {
                    toastr.error(res.mensaje);
                }
            }
        })
    }

    function verImagenes(index) {
        let img1 = '';
        let img2 = '';
        let img3 = '';
        let img4 = '';
        let img5 = '';
        let img6 = '';
        let img7 = '';
        let img8 = '';

        if (rowDataArray[index].img1 != '' && rowDataArray[index].img1 != null) {
            //img1 = '<img class="imgticket" style="width:100%;margin:0px;" src="'+tickets[index].img1+'">';
            img1 = `<div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="` + rowDataArray[index].img1 + `" data-toggle="lightbox" data-title="Imagen 1">
                        <img src="` + rowDataArray[index].img1 + `" class="img-fluid mb-2" alt="Imagen 1"/>
                      </a>
                    </div>`;
        }
        if (rowDataArray[index].img2 != '' && rowDataArray[index].img2 != null) {
            //img2 = '<img class="imgticket" style="width:100%;margin:0px;" src="'+tickets[index].img2+'">';
            img2 = `<div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="` + rowDataArray[index].img2 + `" data-toggle="lightbox" data-title="Imagen 2">
                        <img src="` + rowDataArray[index].img2 + `" class="img-fluid mb-2" alt="Imagen 1"/>
                      </a>
                    </div>`;
        }
        if (rowDataArray[index].img3 != '' && rowDataArray[index].img3 != null) {
            //img3 = '<img class="imgticket" style="width:100%;margin:0px;" src="'+tickets[index].img3+'">';
            img3 = `<div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="` + rowDataArray[index].img3 + `" data-toggle="lightbox" data-title="Imagen 3">
                        <img src="` + rowDataArray[index].img3 + `" class="img-fluid mb-2" alt="Imagen 1"/>
                      </a>
                    </div>`;
        }
        if (rowDataArray[index].img4 != '' && rowDataArray[index].img4 != null) {
            //img4 = '<img class="imgticket" style="width:100%;margin:0px;" src="'+tickets[index].img4+'">';
            img4 = `<div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="` + rowDataArray[index].img4 + `" data-toggle="lightbox" data-title="Imagen 4">
                        <img src="` + rowDataArray[index].img4 + `" class="img-fluid mb-2" alt="Imagen 1"/>
                      </a>
                    </div>`;
        }
        if (rowDataArray[index].img5 != '' && rowDataArray[index].img5 != null) {
            //img5 = '<img class="imgticket" style="width:100%;margin:0px;" src="'+tickets[index].img5+'">';
            img5 = `<div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="` + rowDataArray[index].img5 + `" data-toggle="lightbox" data-title="Imagen 5">
                        <img src="` + rowDataArray[index].img5 + `" class="img-fluid mb-2" alt="Imagen 1"/>
                      </a>
                    </div>`;
        }
        if (rowDataArray[index].img6 != '' && rowDataArray[index].img6 != null) {
            //img6 = '<img class="imgticket" style="width:100%;margin:0px;" src="'+tickets[index].img6+'">';
            img6 = `<div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="` + rowDataArray[index].img6 + `" data-toggle="lightbox" data-title="Imagen 6">
                        <img src="` + rowDataArray[index].img6 + `" class="img-fluid mb-2" alt="Imagen 1"/>
                      </a>
                    </div>`;
        }
        if (rowDataArray[index].img7 != '' && rowDataArray[index].img7 != null) {
            //img7 = '<img class="imgticket" style="width:100%;margin:0px;" src="'+tickets[index].img7+'">';
            img7 = `<div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="` + rowDataArray[index].img7 + `" data-toggle="lightbox" data-title="Imagen 7">
                        <img src="` + rowDataArray[index].img7 + `" class="img-fluid mb-2" alt="Imagen 1"/>
                      </a>
                    </div>`;
        }
        if (rowDataArray[index].img8 != '' && rowDataArray[index].img8 != null) {
            //img8 = '<img class="imgticket" style="width:100%;margin:0px;" src="'+tickets[index].img8+'">';
            img8 = `<div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="` + rowDataArray[index].img8 + `" data-toggle="lightbox" data-title="Imagen 7">
                        <img src="` + rowDataArray[index].img8 + `" class="img-fluid mb-2" alt="Imagen 1"/>
                      </a>
                    </div>`;
        }
        let body = '';

        body = `<div class="card card-primary">
              <div class="card-header">
                <h4 class="card-title">Imágenes adjuntas al finalizar ticket</h4>
              </div>
              <div class="card-body">
                <!--<div>
                  <div class="btn-group w-100 mb-2">
                    <a class="btn btn-info active" href="javascript:void(0)" data-filter="all"> All items </a>
                    <a class="btn btn-info" href="javascript:void(0)" data-filter="1"> Category 1 (WHITE) </a>
                    <a class="btn btn-info" href="javascript:void(0)" data-filter="2"> Category 2 (BLACK) </a>
                    <a class="btn btn-info" href="javascript:void(0)" data-filter="3"> Category 3 (COLORED) </a>
                    <a class="btn btn-info" href="javascript:void(0)" data-filter="4"> Category 4 (COLORED, BLACK) </a>
                  </div>
                  <div class="mb-2">
                    <a class="btn btn-secondary" href="javascript:void(0)" data-shuffle> Shuffle items </a>
                    <div class="float-right">
                      <select class="custom-select" style="width: auto;" data-sortOrder>
                        <option value="index"> Sort by Position </option>
                        <option value="sortData"> Sort by Custom Data </option>
                      </select>
                      <div class="btn-group">
                        <a class="btn btn-default" href="javascript:void(0)" data-sortAsc> Ascending </a>
                        <a class="btn btn-default" href="javascript:void(0)" data-sortDesc> Descending </a>
                      </div>
                    </div>
                  </div>
                </div>-->
                <div>
                  <div class="filter-container p-0 row">
                    ` + img1 + img2 + img3 + img4 + img5 + img6 + img7 + img8 + `
                    <!--<div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="https://via.placeholder.com/1200/FFFFFF.png?text=1" data-toggle="lightbox" data-title="sample 1 - white">
                        <img src="https://via.placeholder.com/300/FFFFFF?text=1" class="img-fluid mb-2" alt="white sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="2, 4" data-sort="black sample">
                      <a href="https://via.placeholder.com/1200/000000.png?text=2" data-toggle="lightbox" data-title="sample 2 - black">
                        <img src="https://via.placeholder.com/300/000000?text=2" class="img-fluid mb-2" alt="black sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="3, 4" data-sort="red sample">
                      <a href="https://via.placeholder.com/1200/FF0000/FFFFFF.png?text=3" data-toggle="lightbox" data-title="sample 3 - red">
                        <img src="https://via.placeholder.com/300/FF0000/FFFFFF?text=3" class="img-fluid mb-2" alt="red sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="3, 4" data-sort="red sample">
                      <a href="https://via.placeholder.com/1200/FF0000/FFFFFF.png?text=4" data-toggle="lightbox" data-title="sample 4 - red">
                        <img src="https://via.placeholder.com/300/FF0000/FFFFFF?text=4" class="img-fluid mb-2" alt="red sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="2, 4" data-sort="black sample">
                      <a href="https://via.placeholder.com/1200/000000.png?text=5" data-toggle="lightbox" data-title="sample 5 - black">
                        <img src="https://via.placeholder.com/300/000000?text=5" class="img-fluid mb-2" alt="black sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="https://via.placeholder.com/1200/FFFFFF.png?text=6" data-toggle="lightbox" data-title="sample 6 - white">
                        <img src="https://via.placeholder.com/300/FFFFFF?text=6" class="img-fluid mb-2" alt="white sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="https://via.placeholder.com/1200/FFFFFF.png?text=7" data-toggle="lightbox" data-title="sample 7 - white">
                        <img src="https://via.placeholder.com/300/FFFFFF?text=7" class="img-fluid mb-2" alt="white sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="2, 4" data-sort="black sample">
                      <a href="https://via.placeholder.com/1200/000000.png?text=8" data-toggle="lightbox" data-title="sample 8 - black">
                        <img src="https://via.placeholder.com/300/000000?text=8" class="img-fluid mb-2" alt="black sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="3, 4" data-sort="red sample">
                      <a href="https://via.placeholder.com/1200/FF0000/FFFFFF.png?text=9" data-toggle="lightbox" data-title="sample 9 - red">
                        <img src="https://via.placeholder.com/300/FF0000/FFFFFF?text=9" class="img-fluid mb-2" alt="red sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="https://via.placeholder.com/1200/FFFFFF.png?text=10" data-toggle="lightbox" data-title="sample 10 - white">
                        <img src="https://via.placeholder.com/300/FFFFFF?text=10" class="img-fluid mb-2" alt="white sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <a href="https://via.placeholder.com/1200/FFFFFF.png?text=11" data-toggle="lightbox" data-title="sample 11 - white">
                        <img src="https://via.placeholder.com/300/FFFFFF?text=11" class="img-fluid mb-2" alt="white sample"/>
                      </a>
                    </div>
                    <div class="filtr-item col-sm-2" data-category="2, 4" data-sort="black sample">
                      <a href="https://via.placeholder.com/1200/000000.png?text=12" data-toggle="lightbox" data-title="sample 12 - black">
                        <img src="https://via.placeholder.com/300/000000?text=12" class="img-fluid mb-2" alt="black sample"/>
                      </a>
                    </div>-->
                  </div>
                </div>
              </div>
            </div>`;

        $('#modal-lg .modal-title').text('Imágenes del ticket');
        $('#modal-lg .modal-body').html(body);
        $('#modal-lg .modal-footer').html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cerrar</button>");
        $('#modal-lg').modal('show');

        $(function() {
            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox({
                    alwaysShowClose: true
                });
            });

            $('.filter-container').filterizr({
                gutterPixels: 3
            });
            $('.btn[data-filter]').on('click', function() {
                $('.btn[data-filter]').removeClass('active');
                $(this).addClass('active');
            });
        })
    }

    function aplicarFiltros() {

        tableData.ajax.reload(); // Recargar la tabla con los nuevos filtros
        return true;

        let ccosto = $('#fil_select_ccosto').val();
        let estadofact = $('#fil_select_efacturacion').val();
        let pagot = $('#fil_select_pagot').val();
        let cliente = $('#fil_select_cliente').val();
        let tecnico = $('#fil_select_tecnico').val();
        let estadofil = $('#fil_select_estado').val();
        let desde = $('#fdesde').val();
        let hasta = $('#fhasta').val();

        if (parseInt(ccosto) === 0) {
            ccosto = '';
        }
        if (parseInt(estadofact) === 0) {
            estadofact = '';
        }
        if (parseInt(pagot) === 0) {
            pagot = '';
        }
        if (parseInt(cliente) === 0) {
            cliente = '';
        }
        if (parseInt(tecnico) === 0) {
            tecnico = '';
        }
        if (desde === '0000-00-00') {
            desde = '';
        }
        if (hasta === '0000-00-00') {
            hasta = '';
        }

        if (ccosto === '' && estadofact === '' && pagot === '' && cliente === '' && tecnico === '' && desde === '' && hasta === '' && estadofil === '') {
            toastr.info('Seleccione alguna opción para filtrar.');
            return;
        }
        $("#tbtickets tbody").html('<tr><td class="text-center" colspan="18"><span class="text-blue"><h4>Aplicando filtros... <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i></h4></span></td></tr>');
        $('#tbtickets').DataTable().destroy();
        getTabTickets('', ccosto, estadofact, pagot, cliente, tecnico, desde, hasta, estadofil);
    }

    function resetearFiltros() {


        $("#tbtickets tbody").html('<tr><td class="text-center" colspan="18"><span class="text-blue"><h4>Quitando filtros... <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i></h4></span></td></tr>');
        $('#fil_select_ccosto').val(0);
        $('#fil_select_efacturacion').val(0);
        $('#fil_select_pagot').val(0);
        $('#fil_select_cliente').val(0);
        $('#fil_select_tecnico').val(0);
        $('#fil_select_estado').val(0);
        $('#fdesde').val('0000-00-00');
        $('#fhasta').val('0000-00-00');

        tableData.ajax.reload(); // Recargar la tabla con los nuevos filtros
        return true;

        $('#tbtickets').DataTable().destroy();
        getTabTickets('todos');
    }

    function cambiarValor(index, idticket) {
        /*let valor = parseInt(tickets[index].vtrabajo);*/
        let valor = $('#vtrabajo_' + index).val();
        /*var valorsinsigno = valor.split('$');*/
        if (valor != 0) {
            /*$('#vtrabajo_'+index).attr('disabled',false);*/
            let slctvalor = $('#um_' + index).val();
            if (parseInt(slctvalor) == 2) {
                $.get("https://mindicador.cl/api", function(data) {
                    let uf = data.uf.valor;
                    var newvalor = uf * valor;
                    /*let valornew = ((1*valor) / uf);*/
                    $('#vtrabajo_' + index).val(newvalor.replaceAll(",", "."));
                    updatevalor(1, index, idticket);
                });
            } else {
                /*const formatterPeso = new Intl.NumberFormat('es-CL', {
                    style: 'currency',
                    currency: 'CLP',
                    minimumFractionDigits: 0
                });*/
                var newvalor = $('#vtrabajo_' + index).attr('name');
                $('#vtrabajo_' + index).val(newvalor);
                updatevalor(1, index, idticket);
            }
        } else {
            /*$('#vtrabajo_'+index).attr('disabled',true);*/
            /*$('#um_'+index).val(1);*/
            toastr.info('No se puede convertir valor en 0.');
        }
    }

    function activarButton(index) {
        if ($('#vtrabajo_' + index).val() === '') {
            $('#btn_reloadvalor_' + index).hide('slow');
            $('#vtrabajo_' + index).css({
                'width': '120px'
            });
            if (tickets[index].vtrabajo !== '0') {
                const formatterPeso = new Intl.NumberFormat('es-CL', {
                    style: 'currency',
                    currency: 'CLP',
                    minimumFractionDigits: 0
                });
                $('#vtrabajo_' + index).val(tickets[index].vtrabajo.replaceAll(",", "."))
            }
        } else {
            $('#btn_reloadvalor_' + index).show('slow');
            $('#vtrabajo_' + index).css({
                'width': '80px'
            });
        }
    }

    function enviarValorNuevo(index) {
        let valor = $('#vtrabajo_' + index).val();
        let id = tickets[index].id;
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'setValorTrabajo',
            valor: valor,
            id: id,
            retornar: 'no'
        }, function(data) {
            data = $.parseJSON(data);
            if (data.status === 'OK') {
                const formatterPeso = new Intl.NumberFormat('es-CL', {
                    style: 'currency',
                    currency: 'CLP',
                    minimumFractionDigits: 0
                });
                $('#btn_reloadvalor_' + index).hide('slow');
                $('#vtrabajo_' + index).css({
                    'width': '120px'
                });
                $('#vtrabajo_' + index).val(valor.replaceAll(",", "."));
                toastr.success('Valor actualizado con exito.');
            } else {
                toastr.error('Error al actualizar valor.');
            }
        });
    }

    function resetData(index) {
        if ($('#vtrabajo_' + index).val() === '') {
            let valorOri = tickets[index].vtrabajo;
            const formatterPeso = new Intl.NumberFormat('es-CL', {
                style: 'currency',
                currency: 'CLP',
                minimumFractionDigits: 0
            });
            $('#btn_reloadvalor_' + index).hide('slow');
            $('#vtrabajo_' + index).css({
                'width': '120px'
            });
            $('#vtrabajo_' + index).val(valorOri.replaceAll(",", "."));
        }

    }

    function selectPagoTecnico(index) {
        let pagot = $('#pagot_' + index).val();
        let id = tickets[index].id;
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'setPagoTecnico',
            pagot: pagot,
            id: id,
            retornar: 'no'
        }, function(data) {
            data = $.parseJSON(data);
            if (data.status === 'OK') {
                toastr.success('Pago técnico actualizado con exito.');
            } else {
                toastr.error('Error al actualizar pago técnico.');
            }
        });
    }

    function selectEstadoFact(index) {
        let estadofact = $('#estadofact_' + index).val();
        let id = rowDataArray[index].id;
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'setEstadoFact',
            estadofact: estadofact,
            id: id,
            retornar: 'no'
        }, function(data) {
            data = $.parseJSON(data);
            if (data.status === 'OK') {
                toastr.success('Estado factura actualizado con exito.');
            } else {
                toastr.error('Error al actualizar estado factura.');
            }
        });
    }

    function selectccosto(index) {
        let ccosto = $('#ccosto_' + index).val();
        let id = rowDataArray[index].id;
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'setCentroCosto',
            ccosto: ccosto,
            id: id,
            retornar: 'no'
        }, function(data) {
            data = $.parseJSON(data);
            if (data.status === 'OK') {
                toastr.success('Centro costo actualizado con exito.');
            } else {
                toastr.error('Error al actualizar centro costo.');
            }
        });
    }

    function round(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num);
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }

    function agendarTicket(id) {
        form = $("#fagendar").html();
        $("#mticket .modal-header").removeClass("header-rojo").addClass("header-verde");
        $("#mticket .modal-title").html("Agendar Ticket");
        $("#mticket .modal-body").html(form);
        $("#mticket .modal-footer").html("<button type='button' class='btn btn-success btn-rounded' onclick='RegistrarAgenda(\"" + id + "\")'>Confirmar</button>");
        $("#mticket").modal("toggle");
        $('.fechaagenda').datepicker();
    }

    function ModificarAgenda(index) {
        ticket = tickets[index];
        form = $("#fagendar").html();
        $("#mticket .modal-header").removeClass("header-rojo header-verde").addClass("header-warning");
        $("#mticket .modal-title").html("Editar Agenda Ticket");
        $("#mticket .modal-body").html(form);
        $("#mticket .modal-footer").html("<button type='button' class='btn btn-success btn-rounded' onclick='RegistrarAgenda(\"" + ticket.id + "\")'>Editar</button>");
        $("#mticket").modal("toggle");
        $('.fechaagenda').datepicker();
        $("#mticket input[name='fecha']").val(ticket["fechaagenda"]);
        $("#mticket #tecnico").val(ticket["idtecnico"]);
        $("#mticket input[name='hora']").val(ticket["hora"]);
        $("#mticket textarea[name='descripcion']").val(ticket["descripcion"]);

    }

    function RegistrarAgenda(id) {
        fecha = $("#mticket input[name='fecha']").val();
        hora = $("#mticket input[name='hora']").val();
        tecnico = $("#mticket #tecnico").val();
        descripcion = $("#mticket textarea[name='descripcion']").val();
        if (tecnico != "") {
            $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'agendarTicket',
                tic_id: id,
                tic_fechaagenda: fecha,
                tic_horaagenda: hora,
                tic_tecnico: tecnico,
                tic_descagenda: descripcion,
                retornar: 'no'
            }, function(data) {
                location.reload();
            });
        } else {
            alert("Error al agendar, debes seleccionar un Técnico");
        }

    }

    $("#btnexcexp").on("click", function() {
        //exportar();

        let ccosto = $('#fil_select_ccosto').val();
        let estadofact = $('#fil_select_efacturacion').val();
        let pagot = $('#fil_select_pagot').val();
        let cliente = $('#fil_select_cliente').val();
        let tecnico = $('#fil_select_tecnico').val();
        let estadofil = $('#fil_select_estado').val();
        let desde = $('#fdesde').val();
        let hasta = $('#fhasta').val();

        if (parseInt(ccosto) === 0) {
            ccosto = '';
        }
        if (parseInt(estadofact) === 0) {
            estadofact = '';
        }
        if (parseInt(pagot) === 0) {
            pagot = '';
        }
        if (parseInt(cliente) === 0) {
            cliente = '';
        }
        if (parseInt(tecnico) === 0) {
            tecnico = '';
        }
        if (desde === '0000-00-00') {
            desde = '';
        }
        if (hasta === '0000-00-00') {
            hasta = '';
        }

        if (ccosto === '' && estadofact === '' && pagot === '' && cliente === '' && tecnico === '' && desde === '' && hasta === '' && estadofil === '') {
            toastr.info('Seleccione alguna opción para filtrar.');
            return;
        }

        exportarDatatable('', ccosto, estadofact, pagot, cliente, tecnico, desde, hasta, estadofil);
        return true;

        /*let ccosto     = $('#fil_select_ccosto').val();
        let estadofact = $('#fil_select_efacturacion').val();
        let pagot      = $('#fil_select_pagot').val();
        let cliente    = $('#fil_select_cliente').val();
        let tecnico    = $('#fil_select_tecnico').val();
        let desde      = $('#fdesde').val();
        let hasta      = $('#fhasta').val();
        if(parseInt(ccosto)===0){
            ccosto = '';
        }
        if(parseInt(estadofact)===0){
            estadofact = '';
        }
        if(parseInt(pagot)===0){
            pagot = '';
        }
        if(parseInt(cliente)===0){
            cliente = '';
        }
        if(parseInt(tecnico)===0){
            tecnico = '';
        }
        if(desde==='0000-00-00'){
            desde = '';
        }
        if(hasta==='0000-00-00'){
            hasta = '';
        }

        env      = {'ccosto':ccosto,'estadofact':estadofact,'pagot':pagot,'cliente':cliente,'tecnico':tecnico,'desde':desde,'hasta':hasta};
        var send = JSON.stringify(env);
        $.ajax({
            url     : 'operaciones.php',
            data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'generaexcelfin',retornar:'no',envio:send},
            type    : 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {
                $('#btnexcexp').attr('disabled', true);
                $('#btnexcexp').html('cargando...');
            },error   : function(respuesta) {
                $('#btnexcexp').attr('disabled', false);
                $('#btnexcexp').html('<i class="fa fa-download" aria-hidden="true"></i> Exportar Excel');                  
            },success : function(respuesta) {
                console.log(respuesta);
                var $a = $("<a>");
                $a.attr("href", respuesta.file);
                $("body").append($a);
                $a.attr("download", "Trabajos finalizados.xlsx");
                $a[0].click();
                $a.remove();
                $('#btnexcexp').attr('disabled', false);
                $('#btnexcexp').html('<i class="fa fa-download" aria-hidden="true"></i> Exportar Excel');
            }           
        }) */
    });

    function exportar() {

        $('#btnexcexp').attr('disabled', true);
        $('#btnexcexp').html('cargando...');
        let formulario = new FormData();

        var dataArray = [];
        var headersArray = [];

        // Obtener los encabezados de la tabla oculta
        $('#tbtickets2 thead tr:eq(0) th').each(function(index) {
            if (index !== 0 && index !== 27) {
                headersArray.push($(this).text());
            }
        });
        dataArray.push(headersArray);

        // Obtener los datos de las filas de la tabla oculta
        $('#tbtickets2 tbody tr').each(function(row, tr) {
            var rowArray = [];

            // Obtener los datos de las celdas
            $(tr).find('td').each(function(index, td) {
                if (index !== 0 && index !== 27) {
                    var select = $(td).find('select');
                    var input = $(td).find('input');

                    if (select.length > 0) {
                        rowArray.push(select.find('option:selected').text());
                    } else if (input.length > 0) {
                        var inputValue = input.val().replace('T', ' ');

                        // Verificar si el contenido del input es una fecha y hora
                        var dateTimeRegex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;
                        if (dateTimeRegex.test(inputValue)) {
                            // Formatear la fecha y hora sin usar Moment.js
                            var date = inputValue.split(' ')[0].split('-').reverse().join('-');
                            var time = inputValue.split(' ')[1];
                            var formattedDateTime = date + ' ' + time;
                            rowArray.push(formattedDateTime);
                        } else {
                            rowArray.push(inputValue);
                        }
                    } else {
                        rowArray.push($(td).text());
                    }
                }
            });

            dataArray.push(rowArray);
        });

        //console.log(dataArray);

        formulario.append('operacion', 'generaexcelfin')
        formulario.append('retornar', 'no')
        formulario.append('datos', JSON.stringify(dataArray))
        $.ajax({
            method: "POST",
            url: "operaciones.php",
            data: formulario,
            processData: false,
            contentType: false
        }).done(function(data) {
            $('#btnexcexp').attr('disabled', false);
            $('#btnexcexp').html('<i class="fa fa-download" aria-hidden="true"></i> Exportar Excel');
            if (isJson(data)) {
                data = $.parseJSON(data);
                var $a = $("<a>");
                $a.attr("href", data.file);
                $("body").append($a);
                $a.attr("download", "Trabajos Finalizados.xlsx");
                $a[0].click();
                $a.remove();
            } else {
                toastr.error('Error al generar Excel.')
            }
        }).fail(function(error) {
            $('#btnexcexp').attr('disabled', false);
            $('#btnexcexp').html('<i class="fa fa-download" aria-hidden="true"></i> Exportar Excel');
        });
    }

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    window.ProxVEH;
    window.ProxTEC;

    function terminarTicket(index) {
        ticket = tickets[index];
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getProxTiquet',
            idveh: ticket["idpatente"],
            idtec: ticket["idtecnico"],
            retornar: 'no'
        }, function(data) {
            datos = $.parseJSON(data);
            ProxVEH = datos["pxv"];
            ProxTEC = datos["pxt"];
            form = "<input type='hidden' id='idtecnico' value=" + ticket.idtecnico + "><input type='hidden' id='idpatente' value=" + ticket.idpatente + "><div class='col-md-12'>Detalle Ticket<hr><div class='col-sm-6'><table class='table table-bordered table-striped'><tr><td>Cliente</td><td>" + ticket.cliente + "</td></tr><tr><td>Contacto</td><td>" + ticket.contacto + "</td></tr><tr><td>Celular</td><td>" + ticket.celular + "</td></tr><tr><td>Patente</td><td>" + ticket.patente + "</td></tr></table></div><div class='col-sm-6'><table class='table table-bordered table-striped'><tr><td>Lugar</td><td>" + ticket.lugar + "</td></tr><tr><td>Fecha Agendada</td><td>" + ticket.fechaagenda + "</td></tr><tr><td>Técnico Asignado</td><td>" + ticket.tecnico + "</td></tr><tr><td>Tipo Trabajo</td><td>" + ticket.tipotrabajo + "</td></tr></table></div></div><div class='col-md-12'>Descripción<hr><textarea class='form-control rznone' rows=3 disabled>" + ticket.descripcion + "</textarea></div>";

            form += "<div class='col-md-12 top20'><div class='col-sm-6'><b>Productos Instalados</b><hr><div id='agregarproaveh'></div><table class='table table-bordered table-striped' id='tbpxv'><thead><th>Cantidad</th><th>Producto</th><th>Serie</th><th>N° Serie</th><th></th></thead><tbody>";
            $.each(ProxVEH, function(index2, valor) {
                form += "<tr><td>" + valor.cantidad + "</td><td>" + valor.producto + "</td><td>" + valor.tieneserie + "</td><td>" + valor.serie + "</td><td class='text-center'><span class='text-red pointer' onclick='quitarProducto(\"" + index2 + "\")'><i class='fa fa-arrow-right' aria-hidden='true'></i></span></td></tr>";
            });

            form += "</tbody></table></div>";
            form += "<div class='col-sm-6' id='productosxtecnico'><b>Productos en Bodega Técnico</b><hr><div id='agregarproatec'></div><table class='table table-bordered table-striped' id='tbpxt'><thead><tr><th></th><th>Cantidad</th><th>Producto</th><th>Serie</th><th>N° Serie</th><th>Estado</th></tr></thead><tbody>";

            $.each(ProxTEC, function(ipxt, vpxt) {
                switch (vpxt.estado) {
                    case 'BUENO':
                        trcolor = "";
                        add = "<td class='text-center'><span class='text-green pointer' onclick='agregarProducto(\"" + ipxt + "\")'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
                        estado = "<td class='text-center'><span class='text-success'><i class='fa fa-check' aria-hidden='true'></i></span></td>";
                        break;
                    case 'MALO':
                        trcolor = "danger";
                        add = "<td class='text-center'><span class='text-muted'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
                        estado = "<td class='text-center'><span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span></td>";
                        break;
                    case 'NO REGISTRADO':
                        trcolor = "warning";
                        add = "<td class='text-center'><span class='text-muted'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
                        break;
                }
                form += "<tr class='" + trcolor + "'>" + add + "<td class='text-center'>" + vpxt.cantidad + "</td><td>" + vpxt.producto + "</td><td>" + vpxt.tieneserie + "</td><td>" + vpxt.serie + "</td>" + estado + "</tr>";
            });
            form += "</tbody></table></div></div>";

            form += "<div class='col-md-12 top20'>Información de Cierre <hr><div class='form-horizontal'><div class='form-group'><div class='col-sm-6'>Observaciones<br><textarea name='observacionfin' class='form-control rznone' rows=5></textarea></div></div><div class='form-group'><div class='col-sm-6'><button type='button' class='btn btn-success btn-rounded' onclick='FinalizarTicket(\"" + ticket.id + "\")'>Finalizar Ticket</button></div></div></div></div>";
            //$("#tblistaticket").removeClass("col-md-12").addClass("col-md-4");
            $("#tblistaticket").hide();
            $("#fcerrarticket .box-body").html(form);
            $("#fcerrarticket").show();
            $('html, body').animate({
                scrollTop: 0
            }, 400);
        });

    }

    function cancelarCierre() {
        $("#fcerrarticket").hide();
        //$("#tblistaticket").removeClass("col-md-4").addClass("col-md-12");
        $("#tblistaticket").show();
    }

    function quitarProducto(idxpro) {
        pxv = ProxVEH[idxpro];
        console.log(pxv);
        if (pxv["tieneserie"] == "NO") {
            inp = "<div class='form-group'><label class='col-sm-3'>Cantidad</label><div class='col-sm-4'><input type='text' class='form-control' name='cantidad' value='" + pxv.cantidad + "'></div></div>";
        } else {
            inp = "<div class='form-group'><label class='col-sm-3'>Nº Serie</label><div class='col-sm-4'><input type='text' class='form-control' name='nserie' disabled value='" + pxv.serie + "'></div></div>";
        }
        form = "<span class='label label-info'>Desinstalar producto y agregar a bodega técnico</span><hr><form class='form-horizontal'><div class='form-group'><label class='col-sm-3'>Producto</label><div class='col-sm-8'><input type='text' class='form-control' name='producto' disabled value='" + pxv["producto"] + "'></div></div>" + inp + "<div class='form-group'><label class='col-sm-3'>Estado</label><div class='col-sm-6'><select name='estadopro' class='form-control'><option value='0'>SELECCIONAR</option><option value=1>BUENO</option><option value=2>MALO</option></select></div></div><div class='form-group'><label class='col-sm-3'>Observaciones</label><div class='col-sm-8'><textarea name='observaciones' class='form-control rznone' rows=5></textarea></div></div><div class='form-group'><div class='col-sm-6 col-sm-offset-3'><button type='button' class='btn btn-info btn-rounded' onclick='sacarPro(\"" + idxpro + "\")'>Desinstalar</button>&nbsp;<button type='button' class='btn btn-danger btn-rounded' onclick='noSacarPro()'>Cancelar</button></div></div></form>";
        $("#agregarproatec").html(form);
    }

    function sacarPro(index) {
        pxv = ProxVEH[index];
        idpxv = pxv["idpxv"];
        idestado = $("select[name='estadopro']").val();
        obs = $("textarea[name='observaciones']").val();
        idtecnico = $("#idtecnico").val();
        if (pxv["tieneserie"] == "NO") {
            cantidad = parseInt($("#agregarproatec input[name='cantidad']").val());
            if (cantidad > parseInt(pxv["cantidad"])) {
                alert("La cantidad a desinstalar no puede ser mayor a la cantidad instalada en el vehículo");
                return;
            }
        } else {
            cantidad = 1;
        }
        // console.log(idestado+" "+obs);
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'desinstalarProducto',
            pxv_id: idpxv,
            pxv_cantidad: cantidad,
            stockactual: pxv["cantidad"],
            "tieneserie": pxv["tieneserie"],
            tecnico: idtecnico,
            estado: idestado,
            observaciones: obs,
            retornar: 'no'
        }, function(data) {
            console.log(data);
            datos = $.parseJSON(data);
            ProxVEH = datos["pxv"];
            ProxTEC = datos["pxt"];
            fpxv = "";
            $.each(ProxVEH, function(ipxv, vpxv) {
                fpxv += "<tr><td>" + vpxv.cantidad + "</td><td>" + vpxv.producto + "</td><td>" + vpxv.tieneserie + "</td><td>" + vpxv.serie + "</td><td class='text-center'><span class='text-red pointer' onclick='quitarProducto(\"" + ipxv + "\")'><i class='fa fa-arrow-right' aria-hidden='true'></i></span></td></tr>";
            });
            $("#tbpxv tbody").html(fpxv);

            fpxt = "";
            $.each(ProxTEC, function(ipxt, vpxt) {
                switch (vpxt.estado) {
                    case 'BUENO':
                        trcolor = "";
                        add = "<td class='text-center'><span class='text-green pointer' onclick='agregarProducto(\"" + ipxt + "\")'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
                        estado = "<td class='text-center'><span class='text-success'><i class='fa fa-check' aria-hidden='true'></i></span></td>";
                        break;
                    case 'MALO':
                        trcolor = "danger";
                        add = "<td class='text-center'><span class='text-muted'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
                        estado = "<td class='text-center'><span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span></td>";
                        break;
                    case 'NO REGISTRADO':
                        trcolor = "warning";
                        add = "<td class='text-center'><span class='text-muted'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
                        break;
                }

                fpxt += "<tr class='" + trcolor + "'>" + add + "<td class='text-center'>" + vpxt.cantidad + "</td><td>" + vpxt.producto + "</td><td>" + vpxt.tieneserie + "</td><td>" + vpxt.serie + "</td>" + estado + "</tr>";
                //fpxt+="<tr><td class='text-center'><span class='text-green pointer' onclick='agregarProducto(\""+ipxt+"\")'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td><td>"+vpxt.cantidad+"</td><td>"+vpxt.producto+"</td><td>"+vpxt.tieneserie+"</td><td>"+vpxt.serie+"</td><td>"+vpxt.estado+"</td></tr>";	
            });
            $("#tbpxt tbody").html(fpxt);
        });
        $("#agregarproatec").html("");
    }

    function noSacarPro() {
        $("#agregarproatec").html("");
    }

    // agregar productos al vehiculo

    function agregarProducto(idxtec) {
        pxt = ProxTEC[idxtec];
        console.log(pxt);
        if (pxt["tieneserie"] == "NO") {
            inp = "<div class='form-group'><label class='col-sm-3'>Cantidad</label><div class='col-sm-4'><input type='text' class='form-control' name='cantidad'></div></div>";
        } else {
            inp = "<div class='form-group'><label class='col-sm-3'>Nº Serie</label><div class='col-sm-4'><input type='text' class='form-control' name='nserie' value=" + pxt.serie + "></div></div>";
        }
        form = "<span class='label label-success'>Instalar producto en el Vehículo</span><hr><form class='form-horizontal'><div class='form-group'><label class='col-sm-3'>Producto</label><div class='col-sm-8'><input type='text' class='form-control' name='producto' disabled value='" + pxt.producto + "'></div></div>" + inp + "<div class='form-group'><div class='col-sm-6 col-sm-offset-3'><button type='button' class='btn btn-success btn-rounded' onclick='instalarPro(\"" + idxtec + "\")'>Instalar</button>&nbsp;<button type='button' class='btn btn-danger btn-rounded' onclick='noInstalarPro()'>Cancelar</button></div></div></form>";
        $("#agregarproaveh").html(form);
    }

    function instalarPro(index) {
        pxt = ProxTEC[index];
        idpxt = pxt["idpxt"];
        idtecnico = $("#idtecnico").val();
        idpatente = $("#idpatente").val();
        if (pxt["tieneserie"] == "NO") {
            nserie = "";
            cantidad = parseInt($("#agregarproaveh input[name='cantidad']").val());
            if (cantidad > parseInt(pxt["cantidad"])) {
                alert("La cantidad a instalar no puede ser mayor a la cantidad disponible en bodega tecnico");
                return;
            }
        } else {
            nserie = $("#agregarproaveh input[name='nserie']").val();
            cantidad = 1;
        }

        // console.log(idestado+" "+obs);
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'instalarProducto',
            "tieneserie": pxt["tieneserie"],
            pxt_id: idpxt,
            pxt_cantidad: cantidad,
            stockactual: pxt["cantidad"],
            tecnico: idtecnico,
            vehiculo: idpatente,
            serie: nserie,
            retornar: 'no'
        }, function(data) {
            console.log(data);
            datos = $.parseJSON(data);
            ProxVEH = datos["pxv"];
            ProxTEC = datos["pxt"];
            fpxv = "";
            $.each(ProxVEH, function(ipxv, vpxv) {
                fpxv += "<tr><td>" + vpxv.cantidad + "</td><td>" + vpxv.producto + "</td><td>" + vpxv.tieneserie + "</td><td>" + vpxv.serie + "</td><td class='text-center'><span class='text-red pointer' onclick='quitarProducto(\"" + ipxv + "\")'><i class='fa fa-arrow-right' aria-hidden='true'></i></span></td></tr>";
            });
            $("#tbpxv tbody").html(fpxv);

            // actualizar listado de productos por tecnico
            fpxt = "";
            $.each(ProxTEC, function(ipxt, vpxt) {
                switch (vpxt.estado) {
                    case 'BUENO':
                        trcolor = "";
                        add = "<td class='text-center'><span class='text-green pointer' onclick='agregarProducto(\"" + ipxt + "\")'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
                        estado = "<td class='text-center'><span class='text-success'><i class='fa fa-check' aria-hidden='true'></i></span></td>";
                        break;
                    case 'MALO':
                        trcolor = "danger";
                        add = "<td class='text-center'><span class='text-muted'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
                        estado = "<td class='text-center'><span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span></td>";
                        break;
                    case 'NO REGISTRADO':
                        trcolor = "warning";
                        add = "<td class='text-center'><span class='text-muted'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
                        break;
                }

                fpxt += "<tr class='" + trcolor + "'>" + add + "<td class='text-center'>" + vpxt.cantidad + "</td><td>" + vpxt.producto + "</td><td>" + vpxt.tieneserie + "</td><td>" + vpxt.serie + "</td>" + estado + "</tr>";
                //fpxt+="<tr><td class='text-center'><span class='text-green pointer' onclick='agregarProducto(\""+ipxt+"\")'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td><td>"+vpxt.cantidad+"</td><td>"+vpxt.producto+"</td><td>"+vpxt.tieneserie+"</td><td>"+vpxt.serie+"</td><td>"+vpxt.estado+"</td></tr>";	
            });
            $("#tbpxt tbody").html(fpxt);


        });
        $("#agregarproaveh").html("");
    }

    function noInstalarPro() {
        $("#agregarproaveh").html("");
    }

    function FinalizarTicket(id) {
        descripcion = $("#fcerrarticket textarea[name='observacionfin']").val();
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'cerrarTicket',
            tic_id: id,
            tic_desccierre: descripcion,
            retornar: 'no'
        }, function(data) {
            location.reload();
        });
    }

    window.patentes;

    function getVehCli() {
        idcliente = parseInt($("#cliente").val());
        //console.log(parseInt(idcliente));
        if (isNaN(idcliente)) {
            alert("El cliente seleccionado no es válido");
            $("#cliente").focus();
            return;
        } else {
            $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getVehCli',
                veh_cliente: '' + idcliente + '',
                retornar: 'no'
            }, function(data) {
                datos = $.parseJSON(data);
                patentes = datos;
                selectvehiculos = "<option value=0>SELECCIONAR</option>";
                $.each(datos, function(index, valor) {
                    selectvehiculos += "<option value=" + valor.idveh + " id=" + index + ">" + valor.patente + "</option>";
                });
                $("#patente").html(selectvehiculos);
                $('#patente').chosen();
            });

            let text = $("#cliente option:selected").text().trim();
            $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getRazonSocial',
                text: text,
                retornar: 'no'
            }, function(data) {
                //console.log(data);
                if (data !== '' && data !== null) {
                    data = $.parseJSON(data);
                    if (data.length > 0) {
                        let option = "<option value='0'>-- SELECCIONAR --</option>";
                        $.each(data, function(i, item) {
                            option += "<option value='" + item.id + "'>" + item.rsocial + "</option>";
                        });
                        $('#rsocial').html(option);
                    }
                }
            });
        }
    }

    function selectpatente(e) {
        index = e[e.selectedIndex].id;
        //console.log(index);
        //console.log(patentes[index]);
        idtipo = parseInt(patentes[index]["idtipo"]);
        if (idtipo == 0) {
            tipo = "--";
        } else {
            tipo = tipo;
        }
        $("#tipoveh").val(tipo);
        $('#tipodtrab').val(patentes[index]["tservicio"]);
        $('#dispositivo').val(patentes[index]["dispositivo"]);
        $('input[name="contacto"]').val(patentes[index]["contacto"]);
        $('input[name="celular"]').val(patentes[index]["celular"]);
    }

    function cNuevoTicket() {
        $("#fnuevoticket").hide();
        $("#listadodetickets").show();
        $("#btn_nticket").attr("disabled", false);
    }

    function EliminarTicket(index) {
        ticket = tickets[index];
        $("#mticket .modal-dialog").css({
            'width': '50%'
        });
        $("#mticket .modal-header").removeClass("header-verde").addClass("header-rojo");
        $("#mticket .modal-title").html("Eliminar Ticket");
        $("#mticket .modal-body").html("Realmente desea eliminar este ticket : <br><table class='table table-bordered table-striped'><tr><td>Cliente</td><td>" + ticket.cliente + "</td></tr><tr><td>Patente</td><td>" + ticket.patente + "</td></tr><tr><td>Tipo Trabajo</td><td>" + ticket.tipotrabajo + "</td></tr></table>");
        $("#mticket .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarTIC(\"" + ticket.id + "\");' class='btn btn-success btn-rounded'>Eliminar</button>");
        $("#mticket").modal("toggle");
    }

    function eliminarTIC(id) {
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'eliminarTicket',
            tic_id: '' + id + '',
            retornar: 'no'
        }, function(data) {
            $("#ftick" + id + "").remove();
            $("#mticket").modal("hide");
        });
    }

    function ValidarCampos() {

        if ($("input[name='contacto']").val() === '' && $("input[name='celular']").val() === '') {
            $("input[name='contacto']").css({
                'border': '1px solid red'
            });
            $('#msg_contacto').show();
            $("input[name='celular']").css({
                'border': '1px solid red'
            });
            $('#msg_celular').show();
            return false;
        }

        if ($("input[name='contacto']").val() === '') {
            $("input[name='contacto']").css({
                'border': '1px solid red'
            });
            $('#msg_contacto').show();
            return false;
        } else {
            $("input[name='contacto']").css({
                'border': '1px solid #ccc'
            });
            $('#msg_contacto').hide();
        }

        if ($("input[name='celular']").val() === '') {
            $("input[name='celular']").css({
                'border': '1px solid red'
            });
            $('#msg_celular').show();
            return false;
        } else {
            $("input[name='celular']").css({
                'border': '1px solid #ccc'
            });
            $('#msg_celular').hide();
        }


    }

    function getCiudad() {
        let idtecnico = $('#tecnico').val();
        if (idtecnico !== '') {
            $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getComunasData',
                usuario: idtecnico,
                retornar: 'no'
            }, function(data) {
                if (data !== '' && data !== null) {
                    data = $.parseJSON(data);
                    if (data.length > 0) {
                        $('#ciudad').val(data[0].comuna);
                    } else {
                        $('#ciudad').val('');
                    }
                } else {
                    $('#ciudad').val('');
                }
            });
        } else {
            $('#ciudad').val('');
        }

    }


    $('#btn_reportticket').click(function() {
        let url = "reports/reporte-historico-ticket-finalizados.php";
        let a = document.createElement('a');
        a.target = "_blank";
        a.href = url;
        a.click();
    });

    function validarTecla(event) {

        const char = String.fromCharCode(event.which);
        const input = event.target;

        console.log("char input: ", char, input);
        // Si el caracter es una coma y ya hay una coma en el valor del input, prevenir la entrada
        if (char === ',' && input.value.includes(',')) {
            event.preventDefault();
        } else if (char !== ',' && (isNaN(char) || char === '.')) {
            // La lógica original para evitar letras, símbolos y puntos
            event.preventDefault();
        }
    }

    function updatevalor2(indice, id, id2) {
        console.log("updatevalor");
        // Obtén el valor actual del input
        const input = document.getElementById('vtrabajo_' + id);
        let valor = input.value;

        // Elimina todos los caracteres que no sean números o la coma
        valor = valor.replace(/[^0-9,]/g, '');

        // Asegura que solo haya una coma como separador decimal
        valor = valor.replace(/,/g, '.').replace(/\./g, ',');

        // Verifica si hay más de un decimal y lo corrige
        const partes = valor.split(',');
        if (partes.length > 2) {
            valor = partes.slice(0, 2).join(',');
        }

        // Asigna el valor formateado al input
        input.value = valor;

        console.log(" indice, id, id2 ", indice, id, id2);
        // Resto de tu código para actualizar otros valores (si es necesario)
        // ...
    }

    function updatevalor(opc, index, ticket) {
        console.log("updatevalor");
        var valor = '';
        if (opc == 0) {
            valor = ($('#vtrabajo_' + index).val() == '' || $('#vtrabajo_' + index).val() == undefined ? 0 : $('#vtrabajo_' + index).val());

            // Eliminar todos los caracteres que no sean números o la coma
            const valorLimpio = valor.replace(/[^0-9,]/g, '');
            // Asegurar solo una coma
            const partes = valorLimpio.split(',');
            if (partes.length > 2) {
                // Si hay más de una coma, mostrar un mensaje de error o corregir el valor
                // alert('Solo se permite una coma.');
                valueEmpty = partes.slice(0, 2).join(',');
            } else {
                // Asignar el valor limpio al input
                valueEmpty = valorLimpio;
            }

            valor = valueEmpty;
            $('#vtrabajo_' + index).val(valueEmpty);

        } else {
            valor = $('#um_' + index).val();
        }

        console.log("valor : ", opc, index, ticket, valor);

        var valorkm = ($('#valorkm_' + index).val() == '' || $('#valorkm_' + index).val() == undefined ? 0 : $('#valorkm_' + index).val());
        var kmdis = $('#kmdistin_' + index).val(); /*($('#kmdist_'+index).text()==null || $('#kmdist_'+index).text()=="null" || $('#kmdist_'+index).text()==""?0:$('#kmdist_'+index).text())*/

        var totalkm = parseFloat(kmdis) * parseFloat(valorkm);
        $('#totalkm_' + index).text(totalkm);
        $('#costolabor_' + index).text(parseFloat(valor) + parseFloat(totalkm));

        var costolabor = parseFloat(valor) + parseFloat(totalkm);

        if (valorkm == '' || valorkm == NaN) {
            valorkm = 0;
        }

        if (totalkm == '' || totalkm == NaN) {
            totalkm = 0;
        }

        if (costolabor == '' || costolabor == NaN) {
            costolabor = 0;
        }

        var datos = {
            'opc': opc,
            'index': index,
            'ticket': ticket,
            'valor': valor,
            'valorkm': valorkm,
            'totalkm': totalkm,
            'costolabor': costolabor
        };
        var send = JSON.stringify(datos);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'updvalorticket',
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
                    if (opc == 0) {
                        $('#vtrabajo_' + index).attr('name', valor);
                    }
                } else {
                    toastr.error(respuesta.mensaje);
                }
            }
        });
    }
</script>