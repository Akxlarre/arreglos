<?php
$idcliente     = '';
$idpatente     = '';
$iddispositivo = '';
$tservicio     = '';
$usuario_externo = array();
$contacto      = '';
$celular       = '';

$fecha = date('Y-m-d');
date_default_timezone_set("America/Santiago");

$fechachile    = date("Y-m-d");
$fechamenosmes = date("Y-m-d", strtotime($fechachile . "- 30 days"));


/* $sql       = "select * from vehiculos";
    $res       = $link->query($sql);
     while($fila = mysqli_fetch_array($res)){
           if($fila['veh_imei']=!'' && $fila['veh_imei']=!null){
            echo $fila['veh_imei'].'<br>';
            $sql1       = "UPDATE productosxvehiculos SET pxv_nserie='{$fila['veh_imei']}' WHERE pxv_idveh = '{$fila['veh_id']}'";
            $res1       = $link->query($sql1);
            echo $sql1.'<br>';
        }
    }*/
if (isset($_REQUEST['repetido'])) {
    if ($_REQUEST['repetido'] == 1) {
?>
        <script type="text/javascript">
            Swal.fire({
                title: "Error",
                text: "Patente ya tiene un ticket ingresado",
                icon: "error"
            });
        </script>
<?php
    }
}

if (isset($_REQUEST['nuevo'])) {

    $sql       = "SELECT * FROM vehiculos WHERE veh_id=" . $_REQUEST['nuevo'];
    $res       = $link->query($sql);

    $sql       = "SELECT usu_id, usu_nombre FROM usuarios WHERE usu_perfil=3";
    $res2       = $link->query($sql);

    $vehiculos = array();
    while ($fila = mysqli_fetch_array($res)) {
        $vehiculos[] = array(
            "id" => $fila['veh_id'],
            "idflotasnet" => $fila['veh_idflotasnet'],
            "rsocial" => $fila['veh_rsocial'],
            "tipo" => $fila['veh_tipo'],
            "gps" => $fila['veh_gps'],
            "cliente" => $fila['veh_cliente'],
            "grupo" => $fila['veh_grupo'],
            "patente" => $fila['veh_patente'],
            "contacto" => $fila['veh_contacto'],
            "celular" => $fila['veh_celular'],
            "estado" => $fila['veh_estado'],
            "sesion" => $fila['veh_sesion'],
            "observacion" => $fila['veh_observacion'],
            "h2" => $fila['veh_h2'],
            "h12" => $fila['veh_h12'],
            "h24" => $fila['veh_h24'],
            "h48" => $fila['veh_h48'],
            "ultimaposicion" => $fila['veh_ultimaposicion'],
            "localidad" => $fila['veh_localidad'],
            "alerta" => $fila['veh_alerta'],
            "imei" => $fila['veh_seriegps'],
            "dispositivo" => $fila['veh_dispositivo'],
            "tservicio" => $fila['veh_tservicio'],
            "modelo" => $fila['veh_modelo'],
            "marca" => $fila['veh_marca']
        );
    }

    $usuarioExterno = array();
    while ($fila3 = mysqli_fetch_array($res2)) {
        $usuarioExterno[] = array("usu_id" => $fila3['usu_id'], "usu_nombre" => $fila3['usu_nombre']);
    }


    $sql1 = "SELECT * from clientes where id=" . $vehiculos[0]['rsocial'];
    $res1 = $link->query($sql1);
    while ($fila1 = mysqli_fetch_array($res1)) {
        $rsocial = $fila1['cuenta'];
    }

    $idcliente     = $vehiculos[0]['cliente'];
    $idrsocial     = $vehiculos[0]['rsocial'];
    $idpatente     = $vehiculos[0]['id'];
    $iddispositivo = $vehiculos[0]['dispositivo'];
    $tservicio     = $vehiculos[0]['tservicio'];
    $usuario_externo     = $usuarioExterno;
    $contacto      = $vehiculos[0]['contacto'];
    $celular       = $vehiculos[0]['celular'];
    $marca         = $vehiculos[0]['marca'];
    $modelo        = $vehiculos[0]['modelo'];
    $condicion     = "WHERE cuenta LIKE '%{$rsocial}%'";

    //print_r($vehiculos[0]);exit;
}

$sql4 = "select * from subestado_equipos";
$res4 = $link->query($sql4);
//<option value=1>Sin transmisión</option><option value=2>SIM defectuosa</option><option value=3>Equipo apagado</option>
$optsub = "<option value='0'>SELECCIONAR</option>";
while ($fila = mysqli_fetch_array($res4)) {
    $optsub .= "<option value='" . $fila['sub_id'] . "'>" . $fila['sub_nombre'] . "</option>";
}

$sql5 = "SELECT t1.* 
            FROM clientes t1 
            Where t1.cli_estadows = 1
            GROUP by t1.cuenta
            order by t1.cuenta asc";
$res5      = $link->query($sql5);
$ind       = 0;
$optioncli = '';
foreach ($res5 as $key5) {
    if ($ind == 0) {
        $optioncli .= '<option value="">SELECCIONAR</option><option value="' . $key5['id'] . '">' . $key5['cuenta'] . '</option>';
    }
    $optioncli .= '<option value="' . $key5['id'] . '">' . $key5['cuenta'] . '</option>';
    $ind++;
}

$sql5 = "SELECT * FROM clientes GROUP BY cuenta";
$res5 = $link->query($sql5);
$optionsclientes = '';
$indim = 0;
foreach ($res5 as $key5) {
    if ($indim == 0) {
        $optionsclientes .= '<option value="TODOS">TODOS</option><option value="' . $key5['cuenta'] . '">' . $key5['cuenta'] . '</option>';
    }
    $optionsclientes .= '<option value="' . $key5['cuenta'] . '">' . $key5['cuenta'] . '</option>';
    $indim++;
}

$usuariosExternos = array();
$sql6 = "SELECT * FROM usuarios where usu_perfil=3";
$res6 = $link->query($sql6);
while ($fila6 = mysqli_fetch_array($res6)) {
    $usuariosExternos[] = array("usu_id" => $fila6['usu_id'], "usu_nombre" => $fila6['usu_nombre']);
}
?>
<style type="text/css">
    .form-check-input-lg {
        transform: scale(1.5);
        /* Aumenta el tamaño del checkbox */
        margin-right: 10px;
        /* Agrega un margen a la derecha del checkbox */
    }

    /* Estilo para la cabecera de la tabla */
    #tbtickets thead {
        position: sticky;
        top: 0;
        /* Fija la cabecera en la parte superior */
        z-index: 100;
        /* Asegura que la cabecera esté por encima de otros elementos */
        background-color: #f9f9f9;
        /* Opcional: Cambia el color de fondo de la cabecera */
    }
</style>



<!-- Estilos de Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<!-- JS de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


<!-- <button onclick="mailprueba()">email prueba alber</button> -->

<!-- modal -->
<div class="modal" id="mticket">
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

<!-- Modal -->
<div class="modal fade" id="modalSeleccion" data-imei="" tabindex="-1" aria-labelledby="modalSeleccionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSeleccionLabel">Selecciona Comando a enviar</h5>
        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>

      </div>
      <div class="modal-body">

        <div class="form-group">
            <label for="patente_out1">Patente</label>
            <input type="text" class="form-control" id="patente_out1" placeholder="Ingrese patente" disabled>
        </div>

        <div class="form-group">
            <label for="imei_out1">Imei</label>
            <input type="text" class="form-control" id="imei_out1" placeholder="Ingrese imei" disabled>
        </div>

        <label for="TipoComandoOut1">Comando a Enviar</label>
        <select id="TipoComandoOut1" class="form-control mb-3">
            <option value="">Seleccione una opción</option>
            <!-- opciones dinámicas o estáticas -->
        </select>

        <label for="TipoComandoOut1Detalle">Detalle Comando</label>
        <select id="TipoComandoOut1Detalle" class="form-control mb-3">
            <option value="">Seleccione una opción</option>
            <!-- opciones dinámicas o estáticas -->
        </select>

        <!-- <select class="form-control form-control-sm" id="det_cliente_out1">
            <?= $optioncli ?>
        </select> -->

        <div class="form-group">
            <label for="cliente_accesorio">Cliente</label>
            <input type="text" class="form-control" id="cliente_accesorio" placeholder="Ingrese cliente" disabled>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close1" data-dismiss="modal" aria-label="Close">Cerrar</button>
        <button type="button" class="btn btn-primary" id="enviarBtn" onclick="enviarDatos()">Enviar</button>

        <!-- Nuevo botón que aparecerá después de hacer clic en "Enviar" -->
        <button type="button" class="btn btn-success d-none" id="registrarOutBtn" onclick="registrarOut1()">Registrar GPS</button>

      </div>
    </div>
  </div>
</div>


<section class="content pt-2">
    <div class="alert alert-success oculto alert-dismissible" id="ticketok">
        <!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
        <h4>
            <i class="icon fa fa-warning"></i>El Ticket se ha registrado exitosamente.
        </h4>
    </div>
    <div class="row p-0">
        <div class="" style="width:14.2%; padding: 10px; cursor: pointer;" onclick="getTabTickets(1)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Total Vehículos">
                <span class="info-box-icon bg-info elevation-1" style="font-size:1.4rem;">
                    <i class="fa fa-phone" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                        Soporte
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="cardsoporte">
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="" style="width:14.2%; padding: 10px; cursor: pointer;" onclick="getTabTickets(2)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Operativos">
                <span class="info-box-icon elevation-1" style="font-size:1.4rem;background-color:#7100FF;color:white;">
                    <i class="fa fa-plug" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                        Instalación
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="cardinstalacion">
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="" style="width:14.2%; padding: 10px; cursor: pointer;" onclick="getTabTickets(3)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="En Taller">
                <span class="info-box-icon bg-secondary elevation-1" style="font-size:1.4rem;">
                    <i class="fa fa-power-off" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                        Desinstalación
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="carddesinstalacion">
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="" style="width:14.2%; padding: 10px; cursor: pointer;" onclick="getTabTickets(4)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Programados">
                <span class="info-box-icon bg-primary elevation-1" style="font-size:1.4rem;">
                    <i class="fa fa-wrench" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                        Intalación Demo
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="cardinstalaciondemo">
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="" style="width:14.2%; padding: 10px; cursor: pointer;" onclick="getTabTickets(5)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Accesorios">
                <span class="info-box-icon bg-info elevation-1" style="font-size:1.4rem;">
                    <i class="fa fa-phone" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                        Inst. Accesorios
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="cardinstalacionaccesorio">
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="" style="width:14.2%; padding: 10px; cursor: pointer;" onclick="getTabTickets(6)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="desaccesorio">
                <span class="info-box-icon bg-primary elevation-1" style="font-size:1.4rem;background-color:#7100FF;color:white;">
                    <i class="fa fa-plug" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                        Desi. Accesorio
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="carddesintalacionaccesorio">
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="" style="width:14.2%; padding: 10px; cursor: pointer;" onclick="getTabTickets(7)">
            <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="inspeccion">
                <span class="info-box-icon bg-secondary elevation-1" style="font-size:1.4rem;">
                    <i class="fa fa-power-off" aria-hidden="true"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-weight:bold;">
                        Inspección
                    </span>
                    <span class="info-box-number" style="font-weight:bold;" id="cardinspeccion">
                        <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>

    </div>

    <div class="row submenu">
        <div class="col-md-12">


            <?php if (in_array($_SESSION['perfil_new'], [1, 2])) { ?>
                <button type='button' class="btn btn-sm btn-success btn-rounded" id="btn_nticket">
                    <i class="fa fa-plus" aria-hidden="true"></i> Nuevo Ticket de Soporte
                </button>
                <!-- <button type='button' class="btn btn-sm btn-info btn-rounded" id="btn_reportticket">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Generar reporte Histórico
            </button> -->
                <button type='button' class="btn btn-sm btn-info btn-rounded" id="btn_resumenticket" onclick="verResumen(1)">
                    <i class="fa fa-cube" aria-hidden="true"></i> Resumen
                </button>
                <button type='button' class="btn btn-sm btn-warning btn-rounded" onclick="getPlantilla()">
                    <i class="fa fa-save" aria-hidden="true"></i> Descargar Plantilla
                </button>
                <button type='button' class="btn btn-sm btn-primary btn-rounded" id="btnsubirtickets">
                    <i class="fa fa-upload" aria-hidden="true"></i> Carga Masiva
                </button>
                <button type='button' class="btn btn-sm btn-success btn-rounded" id="btnexcexp">
                    <i class="fa fa-download" aria-hidden="true"></i> Exportar Excel
                </button>
                <button type='button' class="btn btn-sm btn-success btn-rounded" id="veropertarivos">
                    <i class="fas fa-eye"></i> Ver operativos
                </button>

                <button type='button' class="btn btn-sm btn-success btn-rounded" id="agemdarmasivo" onclick="agendarTicket()" disabled>
                    Agendar masivo
                </button>
            <?php } ?>


            <button type='button' class="btn btn-sm btn-danger btn-rounded oculto" onclick="cancelarFinalizarTicket()" id="btnvolverfromfin">
                <i class="fas fa-arrow-circle-left" aria-hidden="true"></i> Volver
            </button>
            <input type="file" id="ticketsmasivos" style="display:none" name='ticketsmasivos' />
            <div id='cargandoplan' class="col-md-12 oculto top20">
                <div class='progress'>
                    <div class='progress-bar progress-bar-success progress-bar-striped active' role='progressbar'>
                    </div>
                    <span class='sr-only'></span>
                </div>
            </div>
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
                    <h3 class="box-title">Nuevo Ticket</h3><span class="pointer" onclick="resetPage()" style="float: right;"><i class="fa fa-times"></i></span>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <form action="operaciones.php" method="post" class="form-horizontal" id="formticket" enctype="multipart/form-data" onsubmit="return validarTIC()">
                            <input type="hidden" name="operacion" value="nuevoticket" />
                            <input type="hidden" name="retornar" value="index.php?menu=<?php echo $_REQUEST["menu"]; ?>&idmenu=<?php echo $_REQUEST["idmenu"]; ?>" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="cliente" class="col-sm-3 col-form-label">Cuenta</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <?php htmlselect('cliente', 'cliente', 'clientes', 'id', 'cuenta', '', $idcliente, 'WHERE cuenta!="" group by cuenta', 'cuenta', 'getVehCli()', '', 'si', 'no', 'no'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="patente" class="col-sm-3 col-form-label">Patente</label>
                                        <div class="col-sm-8" style="padding-top: 3px;">
                                            <?php htmlselect('patente', 'patente', 'vehiculos', 'veh_id', 'veh_patente', '', $idpatente, '', 'veh_id', 'selectpatente(this)', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                        </div>
                                        <a class="btn-success btn-sm" onclick="modalabrir()" style="height:30px;">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <!-- <label for="patente" class="col-sm-3 col-form-label">Patente</label>
                    <div class="col-sm-9" style="padding-top: 3px;">
                        <?php if (!isset($_REQUEST['nuevo'])) { ?>
                        <select name="patente" id="patente" class="form-control form-control-sm" onchange="selectpatente(this);"></select>
                        <?php } else {
                            htmlselect('patente', 'patente', 'vehiculos', 'veh_id', 'veh_patente', '', $idpatente, '', 'veh_id', 'selectpatente(this)', '', 'si', 'no', 'no', 'form-control-sm');
                        } ?>
                    </div> -->
                                    </div>
                                    <div class="form-group row">
                                        <label for="celular" class="col-sm-3 col-form-label">Marca</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <input type="text" class="form-control form-control-sm" id="marca" name="marca" value="<?php if (isset($_REQUEST['nuevo'])) {
                                                                                                                                        echo $marca;
                                                                                                                                    } ?>">
                                            <!-- <div id="msg_marca" style="display: none;"><span style="color:red;"></span></div> -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="celular" class="col-sm-3 col-form-label">Modelo</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <input type="text" class="form-control form-control-sm" id="modelo" name="modelo" value="<?php if (isset($_REQUEST['nuevo'])) {
                                                                                                                                            echo $modelo;
                                                                                                                                        } ?>">
                                            <!-- <div id="msg_modelo" style="display: none;"><span style="color:red;">* Celular es obligatorio.</span></div> -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="rsocial" class="col-sm-3 col-form-label">Razón Social</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <?php if (!isset($_REQUEST['nuevo'])) { ?>
                                                <select name="rsocial" id="rsocial" class="form-control form-control-sm"></select>
                                            <?php } else {
                                                htmlselect('rsocial', 'rsocial', 'clientes', 'id', 'razonsocial', '', $idrsocial, $condicion, 'razonsocial', '', '', 'no', 'no', 'no', 'form-control-sm');
                                            } ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="tipoveh" class="col-sm-3 col-form-label">Tipo</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <input type="text" id="tipoveh" class="form-control form-control-sm" disabled><input type="hidden" name="tipoveh">
                                        </div>
                                    </div>
                                    <div class="form-group row" style="display:none;">
                                        <label for="dispositivo" class="col-sm-3 col-form-label">Dispositivo</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <?php htmlselect('dispositivo', 'dispositivo', 'tiposdedispositivos', 'tdi_id', 'tdi_nombre', '', $iddispositivo, '', 'tdi_nombre', '', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group row">
                                        <label for="tipodserv" class="col-sm-3 col-form-label">Tipo de Prestador</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">

                                            <select name="tic_tipo_prestador" id="tic_tipo_prestador" class="form-control form-control-sm">
                                                <option value="" disabled>Seleccione prestador</option>
                                                <option value="interno">Interno</option>
                                                <option value="externo" selected>Externo</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tic_usuario_externo" class="col-sm-3 col-form-label">Usuario Externo</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <?php echo htmlselect('tic_usuario_externo', 'tic_usuario_externo', 'usuarios', 'usu_id', 'usu_nombre', '', '', 'where usu_perfil=3', 'usu_nombre', '', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tipodserv" class="col-sm-3 col-form-label">Tipo de Servicio</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <?php htmlselect('tipodserv', 'tipodserv', 'servicios', 'ser_id', 'ser_nombre', '', $tservicio, '', 'ser_nombre', '', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="tipodtrab" class="col-sm-3 col-form-label">Tipo de Trabajo</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <?php htmlselect(
                                                'tipodtrab',
                                                'tipodtrab',
                                                'tiposdetrabajos',
                                                'ttra_id',
                                                'ttra_nombre',
                                                '',
                                                '',
                                                'where deleted_at is NULL',
                                                'ttra_nombre',
                                                '',
                                                '',
                                                'si',
                                                'no',
                                                'no',
                                                'form-control-sm'
                                            ); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="contacto" class="col-sm-3 col-form-label">Contacto</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <input type="text" class="form-control form-control-sm" id="contacto" name="contacto" value="<?php if (isset($_REQUEST['nuevo'])) {
                                                                                                                                                echo $contacto;
                                                                                                                                            } ?>">
                                            <div id="msg_contacto" style="display: none;"><span style="color:red;">* Contacto es obligatorio.</span></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="celular" class="col-sm-3 col-form-label">Celular</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <input type="text" class="form-control form-control-sm" id="celular" name="celular" value="<?php if (isset($_REQUEST['nuevo'])) {
                                                                                                                                            echo $celular;
                                                                                                                                        } ?>">
                                            <div id="msg_celular" style="display: none;"><span style="color:red;">* Celular es obligatorio.</span></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="lugar" class="col-sm-3 col-form-label">Lugar</label>
                                        <div class="col-sm-9" style="padding-top: 3px;">
                                            <input type="text" class="form-control form-control-sm" name="lugar" id="lugar">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="descripcion">Descripción</label>
                                        <textarea name='descripcion' id='descripcion' class='form-control rznone' rows=5></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-rounded" id="btnguardarticket" onclick="return ValidarCampos()">Guardar</button>&nbsp;&nbsp;<button type="button" id="btncancelarticket" class="btn btn-danger btn-rounded" onclick="resetPage()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div><!-- fin box-body -->
            </div>
        </div>
    </div>
    <div class="row top20 oculto" id="resumenticket">
        <div class="col-md-12">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Resumen de ticket</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped table-condensed table-sm" id="tbticketsresumen">
                        <thead class="thead-dark">
                            <th>Region y comuna</th>
                            <th>Cantidad</th>
                            <th>Patente</th>
                            <th>Tipo Trabajo</th>
                            <th>Dispositivo</th>
                            <th>Cuenta</th>
                            <th>Razón Social</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Dias</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan=11>
                                    <span class='text-blue'>
                                        <h4>
                                            Cargando ...
                                            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                                        </h4>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- formulario de asignacion -->
    <div class="oculto" id="fagendar">
        <form class="form-horizontal">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fechaasi" class="col col-sm-3">Fecha</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control fechaagenda form-control-sm" id="fechaasi" name="fechaasi" value="<?php echo hoydate(); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tecnico" class="col col-sm-3">Técnico</label>
                        <div class="col-sm-9">
                            <?php htmlselect('tecnico', 'tecnico', 'personal', 'per_id', 'per_nombrecorto', '', '', ' where deleted_at is null', 'per_nombrecorto,per_apaterno', 'getCiudad()', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="hora" class="col col-sm-3">Hora</label>
                        <div class="col-sm-9">
                            <input type="time" class="form-control form-control-sm" name="hora" id="hora">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ciudad" class="col col-sm-3">Ciudad</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="ciudad" name="ciudad" disabled value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="descripcionasi" class="col col-sm-3">Descripción</label>
                        <div class="col-sm-9">
                            <textarea name='descripcionasi' id='descripcionasi' class='form-control rznone' rows=5></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row top20" id="listadodetickets" style="<?php if (isset($_REQUEST['nuevo'])) {
                                                            echo "display:none;";
                                                        } ?>">
        <div class="col-md-12" id="tblistaticket" style="margin-top:10px;">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Listado de tickets de Soporte</h3><button id="btnlimpiar" onclick="getTabTickets(0)" class="btn btn-sm btn-danger btn-rounded" style="display: none; margin-top: 10px; margin-bottom: 10px;"><i class="fa fa-eraser" aria-hidden="true"></i> Limpiar filtro</button>
                </div>

                <div class="box-header with-border row">
                    <div class="col-md-4">
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 col-form-label" for="clientealb">Cliente</label>
                            <div class="col-sm-7 pt-2">
                                <select id="clientealb" onchange="getTabTickets()"><?php echo $optionsclientes ?></select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 col-form-label" for="estadofl">Estado</label>
                            <div class="col-sm-7 pt-2">
                                <select id="estadofl" onchange="getTabTickets()">
                                    <option value="TODOS">TODOS</option>
                                    <option value="1">PENDIENTE</option>
                                    <option value="2">AGENDADO</option>
                                    <option value="4">ANULADO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="box-body ">
                    <div class="row table-responsive" id="tbl1">
                        <table class="table table-bordered table-striped table-sm" style="width:100%" id="tbtickets">
                            <thead class="thead-dark">
                                <th>Agendar</th>
                                <th>N° Ticket</th>
                                <th nowrap>Fecha Registro</th>
                                <th>Dias</th>
                                <th>Cliente</th>
                                <th>Prestador</th>
                                <th>Usuario Externo</th>
                                <th>Patente</th>
                                <th>Dispositivo</th>
                                <th>Tipo Servicio</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Tipo Trabajo</th>
                                <th>Contacto</th>
                                <th>Celular</th>
                                <th>Lugar</th>
                                <th>Descripción</th>
                                <th>Agenda</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acción</th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="row table-responsive" id="tbl2" style="display:none">
                        <table class="table table-bordered table-striped table-sm" style="width:100%" id="operativos">
                            <thead class="thead-dark">
                                <th style="width: 5%;"><button class="btn-danger btn-sm" onclick="vovler()">Volver</button></th>
                                <th style="width: 5%;">N°</th>
                                <th style="width: 10%;">Tipo</th>
                                <th nowrap style="width: 10%;">Patente</th>
                                <th style="width: 20%;">Fecha</th>
                                <th style="width: 20%;">Cliente</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Ticket</th>
                                <th style="width: 10%;">Crear</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- form para finalizar ticket -->
        <div class="col-md-12 oculto" id="fcerrarticket">
            <div class="card card-primary mt-3">
                <div class="card-header">
                    <h3 class="card-title">Finalizar Ticket</h3>

                    <button type="button" class="btn btn-danger btn-sm text-white float-right" title="Cerrar" onclick="cancelarCierre();"><i class="fas fa-times-circle"></i></button>
                    <button type="button" class="btn btn-warning btn-sm text-white float-right mr-1" title="Recargar Datos del ticket" onclick="recargarData()" id="btnreloadticket"><i class="fas fa-redo-alt"></i></button>
                </div>
                <div class="card-body">
                </div>
            </div>
        </div>

        <div class="col-md-12 oculto mt-3" id="finalizarTicket">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Detalle de Cuenta y Vehículo</h3>
                            <!-- <button type="button" class="btn btn-sm text-white" style="position: absolute;right: 0;"><i class="fas fa-times-circle"></i></button> -->
                        </div>
                        <div class="card-body" style="position: relative;">
                            <div id="loading1" style="position: absolute;left:0;top:0;width:100%;height:100%;background-color:rgba(185,184,185,.5);text-align: center;z-index:500"><i style="margin: 10% auto;" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> Cargando datos...</div>
                            <form action="operaciones.php" method="post" class="form-horizontal" id="fe_veh" style="z-index:100">
                                <!-- <input type="hidden" name="operacion" value="editarvehiculo"/>
                                <input type="hidden" name="idveh"/>
                                <input type="hidden" name="retornar" value="index.php?menu=<?= $_REQUEST["menu"]; ?>&idmenu=<?= $_REQUEST["idmenu"]; ?>"/> -->

                                <div class="form-group row">
                                    <label class="col-sm-4 control-label txtleft">Tipo</label>
                                    <div class="col-sm-8"><?= htmlselect('det_tipo', 'det_tipo', 'tiposdevehiculos', 'tveh_id', 'tveh_nombre', '', '', '', 'tveh_nombre', '', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                    </div>
                                </div>

                                <div class="form-group row" style="display: none;">
                                    <label class="col-sm-4 control-label txtleft">GPS</label>
                                    <div class="col-sm-8">
                                        <select id="gpsdet_" name="det_gps" class="form-control form-control-sm">
                                            <option value="0">SELECCIONAR</option>
                                            <option value="1">BÁSICO</option>
                                            <option value="2">CANBUS</option>
                                            <option value="3">TEMPERATURA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label txtleft">Cuenta</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm" id="det_cliente">
                                            <?= $optioncli ?>
                                        </select>
                                        <!-- <?= htmlselect('det_cliente', 'det_cliente', 'clientes', 'id', 'cuenta', '', '', '', 'cuenta', '', '', 'si', 'no', 'no', 'form-control-sm'); ?> -->
                                    </div>
                                </div>
                                <div class="form-group row" style="display:none;">
                                    <label class="col-sm-4 control-label txtleft">Grupo</label>
                                    <div class="col-sm-8"><select name="grupo" id="det_grupo" class="form-control form-control-sm"></select></div>
                                </div>
                                <div class="form-group row" style="display:none;">
                                    <label class="col-sm-4 control-label txtleft">Región</label>
                                    <div class="col-sm-8"><?= htmlselect('det_region', 'det_region', 'regiones', 'id', 'region|ordinal', '', '', '', 'id', 'getComunas()', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                    </div>
                                </div>
                                <div class="form-group row" style="display:none;">
                                    <label class="col-sm-4 control-label txtleft">Comuna</label>
                                    <div class="col-sm-8"><?= htmlselect('det_comuna', 'det_comuna', 'comunas', 'comuna_id', 'comuna_nombre', '', '', '', 'comuna_id', '', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label txtleft">Patente</label>
                                    <div class="col-sm-4">
                                        <input type="text" id="det_patente" name="det_patente" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label txtleft">Dispositivo</label>
                                    <div class="col-sm-8"><?= htmlselect('det_dispositivo', 'det_dispositivo', 'productos', 'pro_id', 'pro_nombre', '', '', '', 'pro_nombre', '', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label txtleft">Tipo de servicio</label>
                                    <div class="col-sm-8"><?= htmlselect('det_tservicio', 'det_tservicio', 'servicios', 'ser_id', 'ser_nombre', '', '', '', 'ser_nombre', '', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label txtleft">Tipo de trabajo</label>
                                    <div class="col-sm-8"><?= htmlselect('det_ttrabajo', 'det_ttrabajo', 'tiposdetrabajos', 'ttra_id', 'ttra_nombre', '', '', '', 'ttra_nombre', '', '', 'si', 'no', 'no', 'form-control-sm'); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 control-label txtleft">Accesorios</label>

                                    <!-- <div class="col-sm-8"><?= htmlselect('det_accesorios[]', 'det_accesorios', 'subfamilias', 'sfam_id', 'sfam_nombre', '', '', 'where sfam_familia = 23', 'sfam_nombre', '', '', 'si', 'no', 'no', 'form-control-sm','multiple','multiple'); ?>
                                    </div> -->

                                    <div class="col-sm-8">
                                        <select name="det_accesorios[]" id="det_accesorios" class="form-control form-control-sm" multiple>
                                            <?php
                                            // Conexión a la BD y consulta
                                            $query = "SELECT sfam_id, sfam_nombre FROM subfamilias WHERE sfam_familia = 23";
                                            $result = $link->query($query); 
                                            
                                            echo '<option >--</option>';
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo '<option value="'.$row['sfam_id'].'">'.$row['sfam_nombre'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div> 

                                </div>



                                <div class="form-group row" style="display:none;">
                                    <label class="col-sm-4 control-label txtleft">Contacto</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="det_contacto" id="det_contacto" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="form-group row" style="display:none;">
                                    <label class="col-sm-4 control-label txtleft">Celular</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="det_celular" id="det_celular" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <!-- <div class="" id="view_equipamiento">
                                    <div class="row" style="margin-bottom: 15px;">
                                        <div class="col-sm-4 col-lg-6">
                                            <label>Producto</label><br>
                                            <?= htmlselect('selectproducto', 'selectproducto', 'productos', 'pro_id', 'pro_nombre', '', '', '', 'pro_nombre', 'getProductosDetails()', '', 'si', 'no', 'no'); ?>
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
                                </div> -->
                                <!-- <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6"><button type="submit" class="btn btn-success btn-rounded" id="btnunidad">Editar Vehículo</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-rounded" id="btn_ceLinea" onclick="CancelarEV();">Cancelar</button></div>
                                </div> -->
                            </form>
                            <button class="btn btn-warning" id="btnupdateveh" onclick="updateVehiculo()">Actualizar Datos</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Productos equipados en Vehículo</h3>
                        </div>
                        <div class="card-body">
                            <div id="loading2" style="position: absolute;left:0;top:0;width:100%;height:100%;background-color:rgba(185,184,185,.5);text-align: center;z-index:500"><i style="margin: 10% auto;" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> Cargando datos...</div>
                            <table class="table table-sm table-bordered table-hover" id="tbllistproxveh">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Cant.</th>
                                        <th scope="col">Producto</th>
                                        <th scope="col">Serie Pro.</th>
                                        <th scope="col">SIM</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Din1</th>
                                        <th scope="col">Din2</th>
                                        <th scope="col">Din3</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--<div class="col-md-6">
                    <embed id="embedot" style="width: 100%;height: 500;" type="application/pdf" src="">
                </div>-->
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <button type="button" id="btnfinmigrar" class="btn btn-success">Finalizar y Migrar</button>
                    <button type="button" id="btncerrart" class="btn btn-success">Cerrar Ticket</button>
                    <button type="button" onclick="cancelarFinalizarTicket()" class="btn btn-danger"><i class="fas fa-arrow-circle-left" aria-hidden="true"></i> Volver</button>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modal2" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#0EB015;padding: 5px;color:white;">
                <h5 class="modal-title">Agregar patente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="" class="form-control form-control-sm" id="txtpatente">
                    </div>
                    <div>
                        <button class="btn-success btn-sm" id="btnbusniv" onclick="agregarpatente()">Agregar</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 5px;">
                <button type="button" class="btn btn-sm" style="background-color:<?php echo $_SESSION['colorprin'] ?>;color:white;" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm" id="btnseriescrear" onclick="guardarniv()" style="background-color:<?php echo $_SESSION['colorver'] ?>;color:white;">Crear</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="previewImg" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Imágenes Previa y Posterior</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <img id="previewImagen" style="width:100%;cursor:pointer;min-width: 400px;" onclick="" />
                    </div>
                    <div class="col-md-12">
                        <a id="imgdownload" href="" download="">Descargar <i class="fas fa-download"></i></a>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-sm table-bordered table-striped">
                            <tr>
                                <td align="center" colspan="4">Previa</td>
                            </tr>
                            <tr>
                                <td>F. Patente</td>
                                <td>T. Instrumento</td>
                                <td>P. Tablero</td>
                                <td>D. Daños</td>
                            </tr>
                            <tr>
                                <td align="center"><img id="0_1" style="width:100px;cursor:pointer;" onclick="" /></td>
                                <td align="center"><img id="0_2" style="width:100px;cursor:pointer;" onclick="" /></td>
                                <td align="center"><img id="0_3" style="width:100px;cursor:pointer;" onclick="" /></td>
                                <td align="center"><img id="0_4" style="width:100px;cursor:pointer;" onclick="" /></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-sm table-bordered table-striped">
                            <tr>
                                <td align="center" colspan="4">Posterior</td>
                            </tr>
                            <tr>
                                <td>T. Instrumento</td>
                                <td>Puntos Conexión</td>
                                <td>V. Panorámica</td>
                                <td>U. Equipo</td>
                            </tr>
                            <tr>
                                <td align="center"><img id="1_1" style="width:100px;cursor:pointer;" onclick="" /></td>
                                <td align="center"><img id="1_2" style="width:100px;cursor:pointer;" onclick="" /></td>
                                <td align="center"><img id="1_3" style="width:100px;cursor:pointer;" onclick="" /></td>
                                <td align="center"><img id="1_4" style="width:100px;cursor:pointer;" onclick="" /></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mloading" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:0.5rem;">
            <div class="modal-body" style="background-color:#7058c3;">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="spinner-border" style="width: 4rem; height: 4rem;vertical-align:middle;color:white;" role="status"><span class="sr-only">Cargando...</span></div>
                </div>
                <div class="d-flex justify-content-center align-items-center h-100" style="color:white;font-size:17pt;font-weight:bold;" id="loading_desc">Recopilando Información...</div>
            </div>
        </div>
    </div>
</div>

<script>

    var comandos = [];
    var dataPerfil = <?php echo $_SESSION['perfil_new']; ?>;
    var usuariosExternos = <?php echo json_encode($usuariosExternos); ?>;
    var dataTecnico = $("#tecnico").html();
    $(document).ready(function() {

        $('#det_accesorios').select2({
            placeholder: "Selecciona accesorios",
            allowClear: true
        });

        //$('#tic_usuario_externo').chosen('destroy').chosen({ width: "100%", search_contains: true });
        $('#cliente').chosen({
            width: "100%",
            search_contains: true
        });
        $('#patente').chosen({
            width: "100%",
            search_contains: true
        });
        $('#clientealb').chosen({
            width: "100%",
            search_contains: true
        });
        $('#estadofl').chosen({
            width: "100%",
            search_contains: true
        });

        //$('#tecnico').chosen({width: "100%",search_contains: true});


        let tipotrabajo = 0;
        let tiposervicio = 0;
        traeroperativos()
        <?php
        if (isset($_REQUEST['nuevo'])) {
            echo '$("#patente").chosen();';
        }

        ?>



        // Evento para detectar cambio en el select TipoComandoOut1
        $("#TipoComandoOut1").on("change", function () {

            //se reinician variables para registrar vehiculo con nuevo tipo de comando
            band1Out1 = false;
            band1Out2 = false;
            document.getElementById("registrarOutBtn").classList.add("d-none");


            let selectedId = $(this).val();
            let $selectDetalle = $("#TipoComandoOut1Detalle");

            $selectDetalle.empty().append('<option value="">Seleccione un detalle</option>');

            if (selectedId) {
                let comandoSeleccionado = comandos.find(comando => comando.id === selectedId);

                if (comandoSeleccionado) {
                    $selectDetalle.append('<option value="'+comandoSeleccionado.activa+'">Activa: ' + comandoSeleccionado.activa + '</option>');
                    $selectDetalle.append('<option value="'+comandoSeleccionado.desactiva+'">Desactiva: ' + comandoSeleccionado.desactiva + '</option>');
                }
            }
        });

    });

    let arramasivos = [];
    let sercehialb = [];
    let sercehialb1 = [];
    let dinsalb = [];
    let gpsf = [];

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
        getTabTickets(0);
    });

    function mailprueba() {
        env = {
            'id': 0
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'mailprueba',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(res) {

            },
            error: function(res) {

            },
            success: function(res) {

            }
        });
    }

    $("#btnexcexp").on("click", function() {
        var d = "<?php echo $fechachile ?>";
        var h = "<?php echo $fechamenosmes ?>";

        env = {}; //{'desde':NULL,'hasta':NULL};
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'generaexcel',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {
                $('#btnexcexp').attr('disabled', true);
                $('#btnexcexp').html('cargando...');
            },
            error: function(respuesta) {
                $('#btnexcexp').attr('disabled', false);
                $('#btnexcexp').html('<i class="fa fa-download" aria-hidden="true"></i> Exportar Excel');
            },
            success: function(respuesta) {
                // console.log(respuesta);
                var $a = $("<a>");
                $a.attr("href", respuesta.file);
                $("body").append($a);
                $a.attr("download", "Resumen.xlsx");
                $a[0].click();
                $a.remove();
                $('#btnexcexp').attr('disabled', false);
                $('#btnexcexp').html('<i class="fa fa-download" aria-hidden="true"></i> Exportar Excel');
            }
        });

        return true;
        Swal.fire({
            title: 'Ingresa Rango de Fecha',
            html: '<label>Desde</label><input type="date" id="datedesde" class="swal2-input" value="' + h + '"><label>Hasta</label><input type="date" id="datehasta" class="swal2-input" value="' + d + '">',
            confirmButtonText: 'Enviar',
            focusConfirm: false,
            preConfirm: () => {
                const desde = Swal.getPopup().querySelector('#datedesde').value
                const hasta = Swal.getPopup().querySelector('#datehasta').value
                if (!desde && !hasta) {
                    Swal.showValidationMessage(`Debes ingresar las fecha`);
                }
                return {
                    desde: desde,
                    hasta: hasta
                }
            }
        }).then((result) => {
            desde = result.value.desde;
            hasta = result.value.hasta;
            env = {
                'desde': desde,
                'hasta': hasta
            };
            var send = JSON.stringify(env);
            $.ajax({
                url: 'operaciones.php',
                data: {
                    numero: '' + Math.floor(Math.random() * 9999999) + '',
                    operacion: 'generaexcel',
                    retornar: 'no',
                    envio: send
                },
                type: 'post',
                dataType: 'json',
                beforeSend: function(respuesta) {
                    $('#btnexcexp').attr('disabled', true);
                    $('#btnexcexp').html('cargando...');
                },
                error: function(respuesta) {
                    $('#btnexcexp').attr('disabled', false);
                    $('#btnexcexp').html('<i class="fa fa-download" aria-hidden="true"></i> Exportar Excel');
                },
                success: function(respuesta) {
                    // console.log(respuesta);
                    var $a = $("<a>");
                    $a.attr("href", respuesta.file);
                    $("body").append($a);
                    $a.attr("download", "Resumen.xlsx");
                    $a[0].click();
                    $a.remove();
                    $('#btnexcexp').attr('disabled', false);
                    $('#btnexcexp').html('<i class="fa fa-download" aria-hidden="true"></i> Exportar Excel');
                }
            });
        })
    });

    function agregarpatente() {
        if ($('#txtpatente').val() == '') {
            toastr.info('Debes ingresar algun nombre');
            return;
        }

        if ($('#cliente').val() == '') {
            toastr.info('Debes seleccionar un cliente primero');
            return;
        }

        env = {
            'patente': $('#txtpatente').val(),
            'cliente': $('#cliente').val()
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'agregarpatente',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(res) {
                $('#btnbusniv').attr('onclick', 'event.stopPropagation();');
                $('#btnbusniv').text('Cargando...');
            },
            error: function(res) {

            },
            success: function(res) {
                if (res.repetido == 0) {
                    if (res.respuesta == 'error') {
                        toastr.error(res.respuesta.mensaje);
                    } else {

                        var numOpciones = $("#patente option").length - 1;
                        $('#patente').append('<option value="' + res.options + '" id="' + numOpciones + '" selected>' + $('#txtpatente').val() + '</option>').trigger('chosen:updated');

                        toastr.success(res.respuesta.mensaje);
                        /*$('#patente').html('')*/
                        /*$.each(res.options, function(i, item) {*/

                        /* })*/
                        $('#txtpatente').val('');
                        $('#modal2').modal('hide')
                    }

                    $('#btnbusniv').attr('onclick', 'agregarpatente()');
                    $('#btnbusniv').text('Agregar');
                } else {
                    toastr.info('Patente ya existe');
                    $('#btnbusniv').attr('onclick', 'agregarpatente()');
                    $('#btnbusniv').text('Agregar');
                }
                $('#tipodserv').val('').attr('disabled', false);
            }
        });
    }

    function modalabrir() {
        $('#modal2').modal('show');
    }

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

        /*if($("#dispositivo").val()==""){
            alert("Error al registrar ticket, falta seleccionar un dispositivo");
            $("#dispositivo").addClass("input-error");
            return false;	
        }else{
            $("#dispositivo").removeClass("input-error");		
        }*/

        if ($("#tipodtrab").val() == "") {
            alert("Error al registrar ticket, falta seleccionar el tipo de trabajo");
            $("#tipodtrab").addClass("input-error");
            return false;
        } else {
            $("#tipodtrab").removeClass("input-error");
        }

        /*if($("input[name='contacto']").val()==""){
            alert("Error al registrar ticket, falta completar el campo contacto");
            $("input[name='contacto']").addClass("input-error");
            return false;	
        }else{
            $("input[name='contacto']").removeClass("input-error");		
        }

        if($("input[name='celular']").val()==""){
            alert("Error al registrar ticket, falta completar el campo celular");
            $("input[name='celular']").addClass("input-error");
            return false;	
        }else{
            $("input[name='celular']").removeClass("input-error");		
        }

        if($("input[name='lugar']").val()==""){
            alert("Error al registrar ticket, falta completar el campo lugar");
            $("input[name='lugar']").addClass("input-error");
            return false;	
        }else{
            $("input[name='lugar']").removeClass("input-error");		
        }*/
    }

    window.tickets;

    function getTabTickets(tipo_trabajo = null) {
        var filclie = $("#clientealb").val();
        var filestado = $("#estadofl").val();
        if (filclie != '' || filclie != '0') {
            traeroperativos(filclie)
        }

        if ($.fn.DataTable.isDataTable('#tbtickets')) {
            $('#tbtickets').DataTable().destroy();
        }
        $("#tbtickets tbody").html('<tr><td colspan="16" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getTabTickets',
            clientealb: filclie,
            filestado: filestado,
            retornar: 'no',
            tipo_trabajo: tipo_trabajo
        }, function(data) {
            window.tickets = tickets = $.parseJSON(data);
            // console.log(tickets);
            ftickets = "";
            if (tipo_trabajo == 0 || tipo_trabajo == null) {
                $('#btnlimpiar').hide();
            } else {
                $('#btnlimpiar').show();
            }
            countsop = 0;
            countins = 0;
            countdes = 0;
            countdem = 0;

            countinsacc = 0;
            countdesacc = 0;
            countinspec = 0;
            if (tickets.length > 0) {
                $.each(tickets, function(index, valor) {
                    estado = "";
                    accion = "";
                    /*if(index==0 && (tipo_trabajo == 0 || tipo_trabajo==null)){
                        $('#cardsoporte').html(valor.countsop);
                        $('#cardinstalacion').html(valor.countins);
                        $('#carddesinstalacion').html(valor.countdes);
                        $('#cardinstalaciondemo').html(valor.countdem);
                    }*/
                    var chono = '';
                    switch (parseInt(valor.idestado)) {
                        case 1:
                            chono = "<div class='form-check'><input onclick='chmasivo(this.value," + valor.id + ")' class='form-check-input form-check-input-lg chmasivo' type='checkbox' value='" + valor.id + "' id='defaultCheck" + valor.id + "'><label class='form-check-label' for='defaultCheck" + valor.id + "'></label></div>"
                            estado = "<span class='badge badge-danger'>PENDIENTE</span>";
                            accion = "<span class='badge badge-secondary' style='cursor:pointer;' onclick='agendarTicket(\"" + valor.id + "\")'>AGENDAR</span>";
                            break;
                        case 2:
                            estado = "<span class='badge badge-warning' style='cursor:pointer;' onclick='ModificarAgenda(\"" + index + "\")'>AGENDADO</span>";
                            accion = "<span class='badge badge-success' style='cursor:pointer;' onclick='terminarTicket(\"" + index + "\")'>FINALIZAR</span>";
                            break;
                        case 3:
                            estado = "<span class='badge badge-success' style='cursor:pointer;'>CERRADO</span>";
                            accion = "<span class='badge badge-info' style='cursor:pointer;' onclick='DetalleTicket(\"" + index + "\")'>DETALLE</span>";
                            break;
                        case 4:
                            estado = "<span class='badge badge-warning' style='cursor:pointer;'>ANULADO</span>";
                            accion = "<span class='badge badge-info' style='cursor:pointer;' onclick='DetalleTicket(\"" + index + "\")'>DETALLE</span>";
                            break;
                        case 5:
                            estado = "<span class='badge badge-warning' style='cursor:pointer;' onclick='ModificarAgenda(\"" + index + "\")'>AGENDADO</span>";
                            accion = "<span class='badge badge-success' style='cursor:pointer;' onclick='terminarTicket(\"" + index + "\")'>FINALIZAR</span>";
                            break;
                        case 6:
                            estado = "<span class='badge badge-warning' style='cursor:pointer;'>AGENDADO</span>";
                            accion = "<span class='badge badge-success' style='cursor:pointer;' onclick='terminarTicket(\"" + index + "\")'>FINALIZADO APP</span>";
                            break;
                    }

                    // dias transcurridos
                    if (parseInt(valor.diastranscurridos) > 10) {
                        dias = "<span class='label label-danger btn-rounded pointer'>" + valor.diastranscurridos + "</span>";
                    } else if (parseInt(valor.diastranscurridos) > 5 && parseInt(valor.diastranscurridos) <= 10) {
                        dias = "<span class='label label-warning btn-rounded pointer'>" + valor.diastranscurridos + "</span>";
                    } else {
                        dias = "<span class='label label-success btn-rounded pointer'>" + valor.diastranscurridos + "</span>";
                    }

                    if (valor.veh_marca == null || valor.veh_marca == '') {
                        valor.veh_marca = '';
                    }

                    if (valor.veh_modelo == null || valor.veh_modelo == '') {
                        valor.veh_modelo = '';
                    }

                    if (valor.idtiposervicio == 1) {
                        var tser = 'Avanzado';
                    } else if (valor.idtiposervicio == 2) {
                        var tser = 'Básico';
                    } else if (valor.idtiposervicio == 3) {
                        var tser = 'Thermo';
                    } else {
                        var tser = 'No defindo';
                    }

                    if (valor.tipotrabajo == 'INSTALACIÓN') {
                        countins++;
                    } else if (valor.tipotrabajo == 'DESINSTALACIÓN') {
                        countdes++;
                    } else if (valor.tipotrabajo == 'SOPORTE') {
                        countsop++;
                    } else if (valor.tipotrabajo == 'INSTALACIÓN ACCESORIO') {
                        countinsacc++;
                    } else if (valor.tipotrabajo == 'INSPECCIÓN') {
                        countinspec++;
                    } else if (valor.tipotrabajo == 'DESINSTALACIÓN ACCESORIO') {
                        countdesacc++;
                    } else {
                        countdem++;
                    }

                    if (dataPerfil == 1) {
                        ftickets += "<tr id='ftick" + valor.id + "'><td align='center'>" + chono + "</td><td>" + valor.id + "</td><td>" + valor.fechahorareg + "</td><td>" + dias + "</td>" +
                            "<td>" + valor.cuenta + "</td><td>" + valor.tic_tipo_prestador + "</td><td>" + valor.nombre_usuario_externo + "</td><td>" + valor.patente + "</td><td>" + valor.dispositivo + "</td><td>" + valor.sernom + "</td>" +
                            "<td>" + valor.veh_marca + "</td><td>" + valor.veh_modelo + "</td><td>" + valor.tipotrabajo + "</td>" +
                            "<td>" + valor.contacto + "</td><td>" + valor.celular + "</td><td>" + valor.lugar + "</td><td>" + valor.descripcion + "</td>";

                        ftickets += "<td>" + valor.agenda + "</td><td class='text-center'>" + estado + "</td><td class='text-center'>" + accion + "</td>" +
                            "<td><span class='btn btn-sm btn-warning btn-circle' onclick='EditarTicket(\"" + index + "\")'><i class='fa fa-edit' aria-hidden='true'></i></span></td>" +
                            "<td><span class='btn btn-sm btn-danger btn-circle' onclick='EliminarTicket(\"" + index + "\")'><i class='fa fa-trash' aria-hidden='true'></i></span></td>";

                        ftickets += "</tr>";
                    } else if (dataPerfil == 2) {
                        //"+chono+"
                        ftickets += "<tr id='ftick" + valor.id + "'><td align='center'></td><td>" + valor.id + "</td><td>" + valor.fechahorareg + "</td><td>" + dias + "</td>" +
                            "<td>" + valor.cuenta + "</td><td>" + valor.tic_tipo_prestador + "</td><td>" + valor.nombre_usuario_externo + "</td><td>" + valor.patente + "</td><td>" + valor.dispositivo + "</td><td>" + valor.sernom + "</td>" +
                            "<td>" + valor.veh_marca + "</td><td>" + valor.veh_modelo + "</td><td>" + valor.tipotrabajo + "</td>" +
                            "<td>" + valor.contacto + "</td><td>" + valor.celular + "</td><td>" + valor.lugar + "</td><td>" + valor.descripcion + "</td>";
                        if (valor.tic_tipo_prestador == 'interno') {
                            ftickets += "<td>" + valor.agenda + "</td><td class='text-center'>" + estado + "</td><td class='text-center'>" + accion + "</td>" +
                                "<td><span class='btn btn-sm btn-warning btn-circle' onclick='EditarTicket(\"" + index + "\")'><i class='fa fa-edit' aria-hidden='true'></i></span></td>" +
                                "<td><span class='btn btn-sm btn-danger btn-circle' onclick='EliminarTicket(\"" + index + "\")'><i class='fa fa-trash' aria-hidden='true'></i></span></td>";
                        } else {
                            ftickets += "<td>" + valor.agenda + "</td><td class='text-center'></td><td class='text-center'></td>" +
                                "<td></td>" +
                                "<td></td>";
                        }
                        ftickets += "</tr>";
                    } else if (dataPerfil == 3) {

                        let idestado = parseInt(valor.idestado);
                        if (idestado != 1) {
                            accion = "";
                        }

                        if (!(idestado == 2 || idestado == 5)) {
                            estado = "";
                        }

                        //sin opciones de agendar
                        //"+chono+"
                        if (valor.tic_tipo_prestador == 'externo') {
                            ftickets += "<tr id='ftick" + valor.id + "'><td align='center'></td><td>" + valor.id + "</td><td>" + valor.fechahorareg + "</td><td>" + dias + "</td>" +
                                "<td>" + valor.cuenta + "</td><td>" + valor.tic_tipo_prestador + "</td><td>" + valor.nombre_usuario_externo + "</td><td>" + valor.patente + "</td><td>" + valor.dispositivo + "</td><td>" + valor.sernom + "</td>" +
                                "<td>" + valor.veh_marca + "</td><td>" + valor.veh_modelo + "</td><td>" + valor.tipotrabajo + "</td>" +
                                "<td>" + valor.contacto + "</td><td>" + valor.celular + "</td><td>" + valor.lugar + "</td><td>" + valor.descripcion + "</td>";

                            ftickets += "<td>" + valor.agenda + "</td><td class='text-center'>" + estado + "</td><td class='text-center'>" + accion + "</td>" +
                                "<td></td>" +
                                "<td></td>";
                            ftickets += "</tr>";
                        }

                    } else {

                    }


                    //ftickets+="<tr id='ftick"+valor.id+"'><td align='center'>"+chono+"</td><td>"+valor.id+"</td><td>"+valor.fechahorareg+"</td><td>"+dias+"</td><td>"+valor.cuenta+"</td><td>"+valor.patente+"</td><td>"+valor.dispositivo+"</td><td>"+valor.sernom+"</td><td>"+valor.veh_marca+"</td><td>"+valor.veh_modelo+"</td><td>"+valor.tipotrabajo+"</td><td>"+valor.contacto+"</td><td>"+valor.celular+"</td><td>"+valor.lugar+"</td><td>"+valor.descripcion+"</td><td>"+valor.agenda+"</td><td class='text-center'>"+estado+"</td><td class='text-center'>"+accion+"</td><td><span class='btn btn-sm btn-warning btn-circle' onclick='EditarTicket(\""+index+"\")'><i class='fa fa-edit' aria-hidden='true'></i></span></td><td><span class='btn btn-sm btn-danger btn-circle' onclick='EliminarTicket(\""+index+"\")'><i class='fa fa-trash' aria-hidden='true'></i></span></td></tr>";
                });
            }

            $('#cardsoporte').html(countsop);
            $('#cardinstalacion').html(countins);
            $('#carddesinstalacion').html(countdes);
            $('#cardinstalaciondemo').html(countdem);

            $('#cardinstalacionaccesorio').html(countinsacc);
            $('#carddesintalacionaccesorio').html(countdesacc);
            $('#cardinspeccion').html(countinspec);

            $("#tbtickets tbody").html(ftickets);
            if (tipo_trabajo == null || tipo_trabajo == 0) {
                $('#tbtickets').DataTable({
                    "language": {
                        url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'
                    },
                    "paging": true,
                    // "order": [[0, "desc" ]],
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
                    "order": [
                        [1, "asc"]
                    ]
                });

            }
        });
    }

    function chmasivo(value, id) {
        arramasivos = [];
        var hayono = 0;
        $(".chmasivo").each(function() {
            if ($(this).prop('checked')) {
                arramasivos.push($(this).val())
                hayono++;
            }
        });

        if (hayono > 0) {
            $('#agemdarmasivo').attr('disabled', false);
        } else {
            $('#agemdarmasivo').attr('disabled', true);
        }
    }

    function traeroperativos(idclie = 0) {
        var env = {
            'idclie': idclie
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'traeroperativos',
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
                var fila = ``;
                if (respuesta.data.length > 0) {

                    $.each(respuesta.data, function(i, item) {
                        fila += `
                            <tr id="trmov_` + i + `" class="trnuevos">
                                <td></td>
                                <td>` + (i + 1) + `</td>
                                <td>` + item.tservicio + `</td>
                                <td>` + item.patente + `</td>
                                <td>` + item.fecha + `</td>
                                <td>` + item.cliente + `</td>
                                <td>` + item.estado + `</td>
                                <td>` + item.tieneticket + `</td>
                                <td>
                                    <button class="btn-success btn-sm" id="btncrear_` + i + `" onclick="crearticketpat(` + i + `,'` + item.patente + `')" ` + item.disa + `>Crear ticket</button>
                                </td>
                            </tr>`;
                    });

                }
                $('#operativos tbody').html(fila);
            }
        });
    }

    function crearticketpat(indice = 0, patente = '') {

        Swal.fire({
            title: '\u00BFEstas seguro de crear el registro?',
            text: "Este aparecera en la lista de tickets",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#77D47B',
            cancelButtonColor: '#D13E3E',
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var env = {
                    'indice': indice,
                    'patente': patente
                };
                var send = JSON.stringify(env);
                $.ajax({
                    url: 'operaciones.php',
                    data: {
                        numero: '' + Math.floor(Math.random() * 9999999) + '',
                        operacion: 'crearticketpat',
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
                        if (respuesta.resp == 'error') {
                            toastr.error(respuesta.mensaje);
                        } else {
                            toastr.success(respuesta.mensaje);
                            location.reload();
                        }
                    }
                });
            }
        })
    }

    function EditarTicket(index) {
        let _ticket = tickets[index];
        //console.log("ticket: ", _ticket);
        $('#cliente').chosen('destroy');
        $('#cliente').val(_ticket.idcliente);
        $('#cliente').chosen();

        //$('#tic_usuario_externo').chosen('destroy');
        let dataSelectTecnico = '';
        $.each(usuariosExternos, function(i, item) {
            dataSelectTecnico += "<option value='" + item.usu_id + "'>" + item.usu_nombre + "</option>";
        });
        $('#tic_usuario_externo').html(dataSelectTecnico).chosen('destroy').chosen({
            width: "100%",
            search_contains: true
        });



        if (_ticket.idtecnico == '0') {
            $('#btnguardarticket').attr('onclick', 'ActualizarTicket(' + index + ')').addClass('btn-warning').removeClass('btn-success').text('Actualizar Ticket').removeAttr('type').attr('type', 'button');
        } else {
            $('#btnguardarticket').hide();
            $('#btncancelarticket').hide();
        }


        let cliente_cuenta = $("#cliente option:selected").text().trim();
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getVehCli',
            veh_cliente: cliente_cuenta,
            retornar: 'no'
        }, function(data) {
            datos = $.parseJSON(data);
            patentes = datos;
            selectvehiculos = "<option value=0>SELECCIONAR</option>";
            $.each(datos, function(index, valor) {
                selectvehiculos += "<option value=" + valor.idveh + " id=" + index + ">" + valor.patente + "</option>";
            });

            $('#patente').chosen('destroy');
            $('#patente').val(_ticket.idpatente);
            $('#patente').chosen();

            /*  $("#patente").html(selectvehiculos);
              $('#patente').val(_ticket.idpatente);*/

            //$('#patente').chosen();
        });

        let text = $("#cliente option:selected").text().trim();
        $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getRazonSocial',
                text: text,
                retornar: 'no'
            },
            function(data) {
                //console.log(data);
                if (data !== '' && data !== null) {
                    data = $.parseJSON(data);
                    if (data.length > 0) {
                        let option = "<option value='0'>-- SELECCIONAR --</option>";
                        $.each(data, function(i, item) {
                            option += "<option value='" + item.id + "'>" + item.rsocial + "</option>";
                        });
                        $('#rsocial').html(option);

                        //leera la razon social asignada en ticket
                        // console.log("idCliente : ", _ticket.idcliente);
                        if (_ticket.id_rsocial === null) {
                            $('#rsocial').val(_ticket.idcliente);
                        } else {
                            $('#rsocial').val(_ticket.id_rsocial);
                        }

                        //$('#rsocial').chosen();
                    }
                }
            }
        );

        $('#dispositivo').val(_ticket.iddispositivo);
        $('#tipodserv').val(_ticket.idtiposervicio);

        //$('#tic_usuario_externo').chosen('destroy');
        $('#tic_usuario_externo').val(_ticket.tic_usuario_externo).trigger('chosen:updated');
        //$('#tic_usuario_externo').chosen();

        $('#tic_tipo_prestador').val(_ticket.tic_tipo_prestador);

        $('#marca').val(_ticket.veh_marca);
        $('#marca').attr('disabled', true);
        $('#modelo').val(_ticket.veh_modelo);
        $('#modelo').attr('disabled', true);

        $('#tipodtrab').val(_ticket.idtipotrabajo);
        $('input[name="contacto"]').val(_ticket.contacto);
        $('input[name="celular"]').val(_ticket.celular);
        $('input[name="lugar"]').val(_ticket.lugar);
        $('textarea[name="descripcion"]').val(_ticket.descripcion);

        if (_ticket.idtecnico != '0') {
            let slctTec = '<select id="tecnico" class="form-control form-control-sm" onchange="getCiudad()">';
            $.each(usuariosExternos, function(i, item) {
                slctTec += "<option value='" + item.usu_id + "'>" + item.usu_nombre + "</option>";
            });
            slctTec += '</select>';
            let selectTecnico = slctTec;
            let form = '<div class="form-group"><div class="col-sm-12" style="font-size:15pt;"><u><strong>Datos Agenda</strong></u></div><br><div class="col-sm-4"><label for="">Fecha</label><input type="text" id="fechaagenda" class="form-control fechaagenda" value="' + _ticket.fechaagenda + '"></div>' +
                '<div class="col-sm-4"><label for="">Hora</label><input type="time" name="txttiempo" id="txttiempo" class="form-control" value="' + _ticket.hora + '"></div></div>' +
                '<div class="form-group"><div class="col-sm-4"><label for="">Técnico</label>' + selectTecnico + '</div>' +
                '<div class="col-sm-4"><label for="">Ciudad</label><input type="text" class="form-control" id="ciudad" name="ciudad" disabled="" value=""></div>' +
                '<div class="col-sm-12"><label for="descripcionagenda">Descripción</label><textarea name="descripcionagenda" id="descripcionagenda" class="form-control rznone" rows="5">' + _ticket.descagenda + '</textarea></div>' +
                '<div class="col-sm-12 top10"><button type="button" class="btn btn-warning btn-rounded" id="btnguardartickets" onclick="ActualizarTicket(' + index + ')">Actualizar Ticket</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-rounded" onclick="resetPage()">Cancelar</button></div></div>';
            $('#formticket').append(form);
            $('.fechaagenda').datetimepicker({
                format: 'L'
            });
            $('#tecnico').val(_ticket.tic_usuario_externo).chosen('destroy').chosen({
                width: "100%",
                search_contains: true
            });
        }

        $('.fechaagenda').datetimepicker({
            format: 'L'
        });
        $("#listadodetickets").hide();
        $("#fnuevoticket").show();
        $("#btn_nticket").attr("disabled", true);
        $('#cliente').chosen("destroy");
        $('#cliente').chosen({
            width: "100%"
        });
        $('#patente').chosen("destroy");
        $('#patente').chosen({
            width: "100%",
            search_contains: true
        });
    }

    function resetPage() {
        window.location.reload();
    }

    function ActualizarTicket(index) {

        let data = tickets[index];
        let cliente = $('#cliente').val();
        let patente = $('#patente').val();
        let rsocial = $('#rsocial').val();
        let dispositivo = $('#dispositivo').val();
        let tservicio = $('#tipodserv').val();
        let tic_usuario_externo = $('#tic_usuario_externo').val();
        let ttrabajo = $('#tipodtrab').val();
        let contacto = $('input[name="contacto"]').val();
        let celular = $('input[name="celular"]').val();
        let lugar = $('input[name="lugar"]').val();
        let desc = $('textarea[name="descripcion"]').val();
        let tic_tipo_prestador = $('#tic_tipo_prestador').val();

        let fecha = '';
        let hora = '';
        let tecnico = '0';
        let desctecnico = '';
        if (data.idtecnico != '0') {
            fecha = $('#fechaagenda').val();
            hora = $('#txttiempo').val();
            tecnico = $('#tecnico').val();
            desctecnico = $('textarea[name="descripcionagenda"]').val();
        }

        $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'updateTicket',
                id: data.id,
                tic_usuario_externo: tic_usuario_externo,
                tic_tipo_prestador: tic_tipo_prestador,
                cliente: cliente,
                patente: patente,
                rsocial: rsocial,
                dispositivo: dispositivo,
                tservicio: tservicio,
                ttrabajo: ttrabajo,
                contacto: contacto,
                celular: celular,
                lugar: lugar,
                descp: desc,
                fecha: fecha,
                hora: hora,
                tecnico: tecnico,
                desctecnico: desctecnico,
                retornar: 'no'
            },
            function(data) {
                if (data !== '' && data !== null) {
                    data = $.parseJSON(data);
                    if (data.status == 'OK') {
                        toastr.success('Ticket Actualizado exitosamente.');
                        window.location.reload();
                    } else {
                        toastr.error('Error al actualizar\nIntente nuevamente.');
                    }
                }
            });
    }

    function agendarTicket(id = 0) {
        let idTecnico = 0;
        let tic = window.tickets.filter(t => t.id == id);
        let dataSelectTecnico = '';
        if (tic.length > 0) {
            idTecnico = tic[0].tic_usuario_externo;
            $.each(usuariosExternos, function(i, item) {
                dataSelectTecnico += "<option value='" + item.usu_id + "'>" + item.usu_nombre + "</option>";
            });
        }
        form = $("#fagendar").html();
        $("#mticket .modal-header").removeClass("header-rojo").addClass("header-verde");
        $("#mticket .modal-title").html("Agendar Ticket");
        $("#mticket .modal-body").html(form);
        $("#mticket .modal-footer").html("<button type='button' class='btn btn-success btn-rounded' onclick='RegistrarAgenda(\"" + id + "\")'>Confirmar</button>");
        $("#mticket").modal("toggle");
        $('#tecnico').html(dataSelectTecnico).val(idTecnico).chosen('destroy').chosen({
            width: "100%",
            search_contains: true
        });


        //$('#fechaasi').datetimepicker({format: 'L'});	
    }

    function ModificarAgenda(index) {
        ticket = tickets[index];
        let idTecnico = 0;
        let dataSelectTecnico = '';
        idTecnico = ticket.tic_usuario_externo;
        $.each(usuariosExternos, function(i, item) {
            dataSelectTecnico += "<option value='" + item.usu_id + "'>" + item.usu_nombre + "</option>";
        });
        form = $("#fagendar").html();
        $("#mticket .modal-header").removeClass("header-rojo header-verde").addClass("header-warning");
        $("#mticket .modal-title").html("Editar Agenda Ticket");
        $("#mticket .modal-body").html(form);
        $("#mticket .modal-footer").html("<button type='button' class='btn btn-success btn-rounded' onclick='RegistrarAgenda(\"" + ticket.id + "\")'>Editar</button>");
        $("#mticket").modal("toggle");
        $('.fechaagenda').datetimepicker({
            format: 'L'
        });
        $("#mticket input[name='fechaasi']").val(ticket["fechaagenda"]);
        $("#mticket #tecnico").val(ticket["idtecnico"]);
        $("#mticket input[name='hora']").val(ticket["hora"]);
        $("#mticket textarea[name='descripcionasi']").val(ticket["descripcion"]);
        $('#tecnico').html(dataSelectTecnico).val(idTecnico).chosen('destroy').chosen({
            width: "100%",
            search_contains: true
        });
    }

    function RegistrarAgenda(id = 0) {

        if (id != 0) {
            arramasivos = [];
        }

        masivos = JSON.stringify(arramasivos);

        fecha = $("#mticket input[name='fechaasi']").val();
        hora = $("#mticket input[name='hora']").val();
        tecnico = $("#mticket #tecnico").val();
        descripcion = $("#mticket textarea[name='descripcionasi']").val();
        if (tecnico != "") {
            $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'agendarTicket',
                tic_id: id,
                tic_fechaagenda: fecha,
                tic_horaagenda: hora,
                tic_tecnico: tecnico,
                tic_descagenda: descripcion,
                masivos: masivos,
                retornar: 'no'
            }, function(data) {
                /*return;*/
                //console.log(data);
                location.reload();
            });
        } else {
            alert("Error al agendar, debes seleccionar un Técnico");
        }

    }

    window.vehiculos;

    function detailsVeh(idpatente) {
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getTabVehiculos',
            idpatente: idpatente,
            retornar: 'no'
        }, function(data) {
            //vehiculos = $.parseJSON(data);
            vehiculos = $.parseJSON(data).data;
            //console.log('detailsVeh',vehiculos);
        });
    }

    function actualizaapp(idticket = 0) {
        env = {
            'idticket': idticket
        };
        var estadoapp = '';
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'actualizaapp',
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
                if (respuesta.res.length > 0) {
                    estadoapp = 'Sin asignar <button type="button" class="btn btn-sm btn-warning btn-circle" style="color: white;"onclick="actualizaapp(' + idticket + ')"><i class="fas fa-sync-alt"></i></button>';
                    if (respuesta.res[0].estadoapp == 2) {
                        estadoapp = '<span class="badge badge-danger">Pendiente</span> <button type="button" class="btn btn-sm btn-warning btn-circle" style="color: white;"onclick="actualizaapp(' + idticket + ')"><i class="fas fa-sync-alt"></i></button>';
                    } else if (respuesta.res[0].estadoapp == 5) {
                        estadoapp = '<span class="badge badge-success">Finalizado</span> <button type="button" class="btn btn-sm btn-warning btn-circle" style="color: white;"onclick="actualizaapp(' + idticket + ')"><i class="fas fa-sync-alt"></i></button>';
                    } else if (respuesta.res[0].estadoapp == 6) {
                        estadoapp = '<span class="badge badge-success">Finalizado App</span> <button type="button" class="btn btn-sm btn-warning btn-circle" style="color: white;"onclick="actualizaapp(' + idticket + ')"><i class="fas fa-sync-alt"></i></button>';
                    }
                    $("#estado_app").html(estadoapp);
                }
            }
        });
    }

    function getPDFOT(idticket) {
        let formulario = new FormData();
        formulario.append('operacion', 'getOTPDF');
        formulario.append('idticket', idticket);
        formulario.append('retornar', 'no');
        $.ajax({
            method: "POST",
            url: "Modelo/operaciones_ticket_cli.php",
            data: formulario,
            processData: false,
            contentType: false
        }).done(function(data) {
            //$('#btnsetExcellibro').html('<i class="fas fa-book-reader"></i>').attr({'disabled': false})
            var url = "data:application/pdf;base64," + data;
            //$('#embedot').attr('src',url).css('width','100%')
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

    function recargarData(idticket, idveh) {
        $('#loading_desc').text('Cargando datos...')
        $('#mloading').modal('show')
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getDataTicket',
            idticket: idticket,
            idveh: idveh,
            retornar: 'no'
        }, function(data) {
            try {
                datos = $.parseJSON(data);
                // console.log(idticket, idveh, datos)

                let img0_1 = "";
                let img0_2 = "";
                let img0_3 = "";
                let img0_4 = "";
                let img1_1 = "";
                let img1_2 = "";
                let img1_3 = "";
                let img1_4 = "";

                $.each(datos.img, function(i, img) {
                    if (img.idtipo == 0 && img.idsubtipo == 0) {
                        img0_1 = img.img;
                    }
                    if (img.idtipo == 0 && img.idsubtipo == 1) {
                        img0_2 = img.img;
                    }
                    if (img.idtipo == 0 && img.idsubtipo == 2) {
                        img0_3 = img.img;
                    }
                    if (img.idtipo == 0 && img.idsubtipo == 3) {
                        img0_4 = img.img;
                    }
                    if (img.idtipo == 1 && img.idsubtipo == 0) {
                        img1_1 = img.img;
                    }
                    if (img.idtipo == 1 && img.idsubtipo == 1) {
                        img1_2 = img.img;
                    }
                    if (img.idtipo == 1 && img.idsubtipo == 2) {
                        img1_3 = img.img;
                    }
                    if (img.idtipo == 1 && img.idsubtipo == 3) {
                        img1_4 = img.img;
                    }
                });

                let accesorios = "";
                $.each(datos.accesorios, function(i, acc) {
                    accesorios += acc.pro_nombre + (acc.ser_codigo != "" ? "(" + acc.ser_codigo + ")" : "") + ",";
                });

                let firmaTec = datos.firmatec;
                let firmaCli = datos.firmacli;
                $('#firmaTec').html((firmaTec == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' src='archivos/tickets/" + firmaTec + "'/>")))
                $('#firmaCli').html((firmaCli == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' src='archivos/tickets/" + firmaCli + "'/>")))

                $('#img0_1').html((img0_1 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"0_1\")' src='archivos/tickets/" + img0_1 + "'/>")))
                $('#img0_2').html((img0_2 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"0_2\")' src='archivos/tickets/" + img0_2 + "'/>")))
                $('#img0_3').html((img0_3 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"0_3\")' src='archivos/tickets/" + img0_3 + "'/>")))
                $('#img0_4').html((img0_4 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"0_4\")' src='archivos/tickets/" + img0_4 + "'/>")))
                $('#img1_1').html((img1_1 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"1_1\")' src='archivos/tickets/" + img1_1 + "'/>")))
                $('#img1_2').html((img1_2 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"1_2\")' src='archivos/tickets/" + img1_2 + "'/>")))
                $('#img1_3').html((img1_3 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"1_3\")' src='archivos/tickets/" + img1_3 + "'/>")))
                $('#img1_4').html((img1_4 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"1_4\")' src='archivos/tickets/" + img1_4 + "'/>")))

                $('#dimeigps').text(datos.nserie)
                $('#dimeican').text(datos.nserieCan)
                $('#dseriesim').text(datos.seriesim)
                $('#daccesorios').text(accesorios)
                $('#origen').text(datos.origen)
                $('#destino').text(datos.destino)
                $('#kms').text(datos.kms)
                $('#nombrefirmaCli').text(datos.nombrefirma)
            } catch (error) {

            }

            setTimeout(() => {
                $('#mloading').modal('hide')
            }, 1000);
        });
    }

    window.ProxVEH;
    window.ProxTEC;
    window.idpatenteSelec;
    let sensores = '';
    var ticketSelect = null;

    function terminarTicket(index) {
        ticket = tickets[index];
        ticketSelect = ticket;
        // console.log('ticket', ticket);
        // console.log('holaaaaaa');
        tipotrabajo = $('#tipo_trab').text();
        idpatenteSelec = ticket["idpatente"];
        tiposervicio = ticket["idtiposervicio"];
        $('#btnreloadticket').attr('onclick', 'recargarData(' + ticket.id + ',' + ticket.idpatente + ')')
        var nametipo = '-';
        detailsVeh(idpatenteSelec);
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getProxTiquet',
            idveh: ticket["idpatente"],
            idtec: ticket["idtecnico"],
            retornar: 'no'
        }, function(data) {
            datos = $.parseJSON(data);
            ProxVEH = datos["pxv"];

            $.each(datos["pxv"], function(i, item) {
                sercehialb.push({
                    'ser_id': item.ser_id,
                    'ser_codigo': item.serie,
                    'condicion': item.ser_condicion,
                    'pro_nombre': item.producto,
                    'din1d': item.din1,
                    'din2d': item.din2,
                    'din3d': item.din3,
                    'proid': item.idpro
                });
            });

            $.each(datos["pxt"], function(i, item) {
                sercehialb1.push({
                    'ser_id': item.idpxt,
                    'ser_codigo': item.serie,
                    'pro_nombre': item.producto,
                    'proid': item.idpro,
                    'familia': item.familia
                });
            });

            $.each(datos["gpsf"], function(i, item) {
                gpsf.push({
                    'proid': item.proid,
                    'proserie': item.pro_serie
                });
            });

            ProxTEC = datos["pxt"];
            sensoressel = datos['sensores'];

            let img0_1 = "";
            let img0_2 = "";
            let img0_3 = "";
            let img0_4 = "";
            let img1_1 = "";
            let img1_2 = "";
            let img1_3 = "";
            let img1_4 = "";

            let accesorios = "";
            $.each(ticket.img, function(i, img) {
                if (img.idtipo == 0 && img.idsubtipo == 1) {
                    img0_1 = img.img;
                }
                if (img.idtipo == 0 && img.idsubtipo == 2) {
                    img0_2 = img.img;
                }
                if (img.idtipo == 0 && img.idsubtipo == 3) {
                    img0_3 = img.img;
                }
                if (img.idtipo == 0 && img.idsubtipo == 4) {
                    img0_4 = img.img;
                }
                if (img.idtipo == 1 && img.idsubtipo == 1) {
                    img1_1 = img.img;
                }
                if (img.idtipo == 1 && img.idsubtipo == 2) {
                    img1_2 = img.img;
                }
                if (img.idtipo == 1 && img.idsubtipo == 3) {
                    img1_3 = img.img;
                }
                if (img.idtipo == 1 && img.idsubtipo == 4) {
                    img1_4 = img.img;
                }
            });

            $.each(ticket.accesorios, function(i, acc) {
                accesorios += acc.pro_nombre + (acc.ser_codigo != "" ? "(" + acc.ser_codigo + ")" : "") + ",";
            });

            let firmaTec = ticket.firmatec;
            let firmaCli = ticket.firmacli;

            form = "<input type='hidden' id='idtecnico' value=" + ticket.idtecnico + ">" +
                "<input type='hidden' id='idpatente' value=" + ticket.idpatente + ">" +
                "<input type='hidden' id='idpatentetxt' value=" + ticket.patente + ">" +
                "<div class='col-md-12'>Detalle Ticket #" + ticket.id + "<hr>" +
                "<div class='row'><div class='col-sm-6'>" +
                "<table class='table table-sm table-bordered table-striped'>" +
                "<tr>" +
                "<td>Cliente</td>" +
                "<td>" + ticket.cuenta + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td>Contacto</td>" +
                "<td>" + ticket.contacto + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td>Celular</td>" +
                "<td>" + ticket.celular + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td>Patente</td>" +
                "<td>" + ticket.patente + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td>Tipo Vehiculo</td>" +
                "<td>" + ticket.tveh_nombre + "</td>" +
                "</tr>" +
                "</table>" +
                "</div>" +
                "<div class='col-sm-6'>" +
                "<table class='table table-sm table-bordered table-striped'>" +
                "<tr>" +
                "<td>Lugar</td>" +
                "<td>" + ticket.lugar + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td>Fecha Agendada</td>" +
                "<td>" + ticket.fechaagenda + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td>Técnico Asignado</td>" +
                "<td>" + ticket.tecnico + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td>Tipo Trabajo</td>" +
                "<td id='tipo_trab'>" + ticket.tipotrabajo + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td>Estado APP</td>" +
                "<td id='estado_app'>" + ticket.estadoapp + " " +
                "<button type='button' class='btn btn-sm btn-warning btn-circle' style='color: white;'onclick='actualizaapp(" + ticket.id + ")'><i class='fas fa-sync-alt'></i></button>" +
                "</td>" +
                "</tr>" +
                "</table>" +
                "</div>" +
                "<div class='col-sm-12'>" +
                "<table class='table table-sm table-bordered table-striped'>" +
                "<tr>" +
                "<th style='text-align:center;'>Imei GPS</th>" +
                "<th style='text-align:center;'>Imei CAN</th>" +
                "<th style='text-align:center;'>Serie SIM</th>" +
                "<th style='text-align:center;'>Origen</th>" +
                "<th style='text-align:center;'>Destino</th>" +
                "<th style='text-align:center;'>Kms</th>" +
                "</tr>" +
                "<tr>" +
                "<td align='center' id='dimeigps'>" + ticket.nserie + "</td>" +
                "<td align='center' id='dimeican'>" + ticket.nserieCan + "</td>" +
                "<td align='center' id='dseriesim'>" + ticket.seriesim + "</td>" +
                "<td align='center' id='dorigen'>" + ticket.origen + "</td>" +
                "<td align='center' id='ddestino'>" + ticket.destino + "</td>" +
                "<td align='center' id='dkms'>" + ticket.kms + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td align='center' style='width:180px;'>Accesorios</td>" +
                "<td align='left' colspan='6' id='daccesorios'>" + accesorios + "</td>" +
                "</tr>" +
                "</table>" +
                "</div>" +
                "<div class='col-sm-6'>" +
                "<table class='table table-sm table-bordered table-striped'>" +
                "<tr>" +
                "<td align='center' colspan='4'>Previa</td>" +
                "</tr>" +
                "<tr>" +
                "<td align='center'>F. Patente</td>" +
                "<td align='center'>T. Instrumento</td>" +
                "<td align='center'>P. Tablero</td>" +
                "<td align='center'>D. Daños</td>" +
                "</tr>" +
                "<tr>" +
                "<td align='center' id='img0_1'>" + (img0_1 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"0_1\")' src='archivos/tickets/" + img0_1 + "'/>")) + "</td>" +
                "<td align='center' id='img0_2'>" + (img0_2 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"0_2\")' src='archivos/tickets/" + img0_2 + "'/>")) + "</td>" +
                "<td align='center' id='img0_3'>" + (img0_3 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"0_3\")' src='archivos/tickets/" + img0_3 + "'/>")) + "</td>" +
                "<td align='center' id='img0_4'>" + (img0_4 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"0_4\")' src='archivos/tickets/" + img0_4 + "'/>")) + "</td>" +
                "</tr>" +
                "</table>" +
                "</div>" +
                "<div class='col-sm-6'>" +
                "<table class='table table-sm table-bordered table-striped'>" +
                "<tr>" +
                "<td align='center' colspan='4'>Posterior</td>" +
                "</tr>" +
                "<tr>" +
                "<td align='center'>T. Instrumento</td>" +
                "<td align='center'>Puntos Conexión</td>" +
                "<td align='center'>V. Panorámica</td>" +
                "<td align='center'>U. Equipo</td>" +
                "</tr>" +
                "<tr>" +
                "<td align='center' id='img1_1'>" + (img1_1 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"1_1\")' src='archivos/tickets/" + img1_1 + "'/>")) + "</td>" +
                "<td align='center' id='img1_2'>" + (img1_2 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"1_2\")' src='archivos/tickets/" + img1_2 + "'/>")) + "</td>" +
                "<td align='center' id='img1_3'>" + (img1_3 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"1_3\")' src='archivos/tickets/" + img1_3 + "'/>")) + "</td>" +
                "<td align='center' id='img1_4'>" + (img1_4 == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' onclick='previewImg(\"1_4\")' src='archivos/tickets/" + img1_4 + "'/>")) + "</td>" +
                "</tr>" +
                "</table>" +
                "</div>" +
                "<div class='col-sm-6'>" +
                "<table class='table table-sm table-bordered table-striped'>" +
                "<tr>" +
                "<td align='center' colspan='4'>Firmas OT</td>" +
                "</tr>" +
                "<tr>" +
                "<td align='center'>Técnico</td>" +
                "<td align='center'>Cliente</td>" +
                "</tr>" +
                "<tr>" +
                "<td align='center' id='firmaTec'>" + (firmaTec == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' src='archivos/firmas/" + firmaTec + "'/>")) + "</td>" +
                "<td align='center' id='firmaCli'>" + (firmaCli == "" ? "<div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div>" : ("<img style='width:110px;cursor:pointer;' src='archivos/firmas/" + firmaCli + "'/>")) + "</td>" +
                "</tr>" +
                "<tr>" +
                "<td></td>" +
                "<td align='center' id='nombrefirmaCli'>" + ticket.nombreFirma + "</td>" +
                "</tr>" +
                "</table>" +
                "</div>" +
                "</div><div class='col-md-12'>Descripción<hr><textarea class='form-control rznone' rows=3 disabled>" + ticket.descripcion + "</textarea></div>";

            form += "<div class='row mt-3'><div style='width:47.5%;' class='table-responsive'><b>Productos Instalados</b><hr><div id='agregarproaveh'></div><table class='table table-sm table-bordered table-striped' id='tbpxv'><thead class='thead-dark'><th nowrap>Cant.</th><th nowrap>Producto</th><th nowrap>Serie</th><th nowrap>N° Serie Pro.</th><th nowrap>N° Serie SIM.</th><th nowrap>Tipo</th><th nowrap>Din1</th><th nowrap>Din2</th><th nowrap>Din3</th></thead><tbody>";
            $.each(ProxVEH, function(index2, valor) {
                let tipo = 'Kit GPS';

                /* nametipo = valor.ideasi;*/
                let seriesim;
                let seriegps;
                if (valor.kitdetalle.length > 0) {
                    seriesim = (valor.kitdetalle[0].seriesim == undefined ? '' : valor.kitdetalle[0].seriesim);
                    seriegps = (valor.kitdetalle[0].seriegps == undefined ? '' : valor.kitdetalle[0].seriegps);
                }
                if (parseInt(valor.tipo) == 1) {
                    tipo = 'Producto';
                    nametipo = valor.producto;
                    seriegps = valor.serie
                    seriesim = '';
                }

                if (valor.din1 == null) {
                    valor.din1 = '-';
                }

                if (valor.din2 == null) {
                    valor.din2 = '-';
                }

                if (valor.din3 == null) {
                    valor.din3 = '-';
                }

                if (valor.producto == '' || valor.producto == null) {
                    nametipo = '-';
                } else {
                    nametipo = valor.producto;
                }

                form += "<tr id='fila_id2_" + index2 + "' onclick='pasarProducto(\"" + index2 + "\",2,\"" + valor.serie + "\",0," + valor.ser_id + ")'><td>" + valor.cantidad + "</td><td>" + nametipo + "</td><td>" + valor.tieneserie + "</td><td>" + valor.serie + "</td><td>" + seriesim + "</td><td>" + tipo + "</td><td align='center'>" + valor.din1 + "</td><td align='center'>" + valor.din2 + "</td><td align='center'>" + valor.din3 + "</td><td class='text-center oculto'><span class='text-red pointer' onclick='quitarProducto(\"" + index2 + "\")'><i class='fa fa-arrow-right' aria-hidden='true'></i></span></td></tr>";
            });

            form += "</tbody></table></div>";
            form += '<div style="width: 5%;padding-left: 10px;padding-top: 10%;">';
            form += '    <button type="button" class="btn btn-sm btn-success btn-circle" style="width:65%;color: white;" disabled id="btnpasar1" onclick="prodAveh(1)"><i class="fas fa-long-arrow-alt-left"></i></button>';
            form += '    <button type="button" class="btn btn-sm btn-danger btn-circle" style="width:65%;color: white;margin-top: 10px;" disabled id="btnpasar2"onclick="prodAveh(2)"><i class="fas fa-long-arrow-alt-right"></i></button>';
            form += '</div>';
            form += "<div style='width:47.5%;' id='productosxtecnico'>"
                        +"<b>Productos en Bodega Técnico: <span id='spannombrecorto'></span></b>"
                        +"<hr>"
                        +"<div id='agregarproatec'></div>"
                        +"<table class='table table-sm table-bordered table-striped' id='tbpxt'>"
                        +"    <thead class='thead-dark'><tr>"
                        +"        <th nowrap>Cant.</th>"
                            +"        <th nowrap></th>"
                            +"        <th nowrap>Producto</th>"
                            +"        <th nowrap>Serie</th>"
                            +"        <th nowrap>N° Serie Pro.</th>"
                            +"        <th nowrap>N° Serie SIM.</th>"
                            +"        <th nowrap>Tipo</th>"
                            +"        <th nowrap>Estado</th>"
                            +"        </tr>"
                            +"    </thead>"
                        +"<tbody>";
            
            
            var ntec = '';
            $.each(ProxTEC, function(ipxt, vpxt) {
                if (ipxt == 0) {
                    ntec = vpxt.nomtecnico;
                }

                let tipo = 'Kit GPS';
                let nametipo = vpxt.ideasi;
                let seriesim;
                let seriegps;
                if (vpxt.kitdetalle.length > 0) {
                    seriesim = (vpxt.kitdetalle[0].seriesim == undefined ? '' : vpxt.kitdetalle[0].seriesim);
                    seriegps = (vpxt.kitdetalle[0].seriegps == undefined ? '' : vpxt.kitdetalle[0].seriegps);
                }

                if (parseInt(vpxt.tipo) == 1) {
                    tipo = 'Producto';
                    nametipo = vpxt.producto;
                    seriegps = vpxt.serie;
                    seriesim = '';
                } else {
                    tipo = 'Producto';
                    nametipo = vpxt.producto;
                    seriegps = vpxt.serie;
                    seriesim = '';
                }
                trcolor = "";

                switch (vpxt.estado) {
                    case 'BUENO':
                        trcolor = "";
                        add = "<td class='text-center'><span class='text-green' style='cursor:pointer;' onclick='agregarProducto(\"" + ipxt + "\")'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
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
                        estado = "<td class='text-center'><span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span></td>";
                        break;
                }
                // console.log("trcolor", trcolor);

                var btnchvivio = "";
                if (vpxt.familia == 'GPS') {
                    btnchvivio = "<button type='button' class='btn btn-sm btn-success btn-circle' style='color: white;' onclick='checkedvivo(" + vpxt.serie + "," + vpxt.idpro + ")'><i class='fas fa-server'></i></button>";
                    btnchvivio += "<button type='button' class='btn btn-sm btn-info btn-circle' style='color: white;' title='Corta Corriente' onclick='validarOut1(" + vpxt.serie + "," + vpxt.idpro + ")'><i class='fas fa-server'></i></button>";
                }
                form += "<tr id='fila_id_" + ipxt + "'  class='" + trcolor + "'><td class='text-center'>" + vpxt.cantidad + "</td><td>" + btnchvivio + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + vpxt.serie + "\"," + vpxt.idpro + "," + vpxt.idpxt + ")'>" + nametipo + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + vpxt.serie + "\"," + vpxt.idpro + "," + vpxt.idpxt + ")'>" + vpxt.tieneserie + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + vpxt.serie + "\"," + vpxt.idpro + "," + vpxt.idpxt + ")' id='idser_" + ipxt + "' name='" + vpxt.idpxt + "'>" + vpxt.serie + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + vpxt.serie + "\"," + vpxt.idpro + "," + vpxt.idpxt + ")'>" + seriesim + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + vpxt.serie + "\"," + vpxt.idpro + "," + vpxt.idpxt + ")'>" + tipo + "</td>" + estado + "</tr>";
            });
            form += "</tbody></table></div></div>";
            form += "<div class='col-md-12 top20'>Información de Cierre <hr><div class='form-horizontal'><div class='form-group'><div class='row'><div class='col-sm-6'>Observaciones<br><textarea name='observacionfin' class='form-control rznone' rows=5></textarea></div><div class='col-sm-6' id='tablavivo'></div></div></div><div class='form-group' style='margin-left:10px;'><label for='btnadjuntarimagenes'>Adjuntar Imágenes</label><button type='button' id='btnadjuntarimagenes' onclick='addImagenes()' class='pointer btn btn-sm bt-primary' style='font-size:17pt;color:#1676D2;'><i class='fas fa-upload' aria-hidden='true'></i></button> <span id='contImg'>0 Imágenes</span><input type='file' id='archivosadjuntos' style='display:none'  accept='.jpeg,.jpg,.png' required name='archivosadjuntos' multiple/> </div><div class='form-group'><div class='col-sm-6'><button type='button' class='btn btn-info btn-rounded' onclick='FinalizarTicket(\"" + ticket.id + "\"," + ticket.idtipotrabajo + ")'>Confirmar Ticket</button></div></div></div></div>";
            //$("#tblistaticket").removeClass("col-md-12").addClass("col-md-4");
            $("#tblistaticket").hide();
            $("#fcerrarticket .card-body").html(form);
            $('#spannombrecorto').text(ntec);
            $("#fcerrarticket").show();
            $('html, body').animate({
                scrollTop: 0
            }, 400);
        });

    }

    function previewImg(idImg) {
        let img0_1 = "";
        let img0_2 = "";
        let img0_3 = "";
        let img0_4 = "";
        let img1_1 = "";
        let img1_2 = "";
        let img1_3 = "";
        let img1_4 = "";
        $.each(ticketSelect.img, function(i, img) {
            if (idImg == (img.idtipo + "_" + (parseInt(img.idsubtipo) + 1))) {
                $('#previewImagen').attr("src", "archivos/tickets/" + img.img)
                $('#imgdownload').attr({
                    "href": "archivos/tickets/" + img.img,
                    "download": "archivos/tickets/" + img.img
                })
            }
            if (img.idtipo == 0 && img.idsubtipo == 1) {
                img0_1 = img.img;
            }
            if (img.idtipo == 0 && img.idsubtipo == 2) {
                img0_2 = img.img;
            }
            if (img.idtipo == 0 && img.idsubtipo == 3) {
                img0_3 = img.img;
            }
            if (img.idtipo == 0 && img.idsubtipo == 4) {
                img0_4 = img.img;
            }
            if (img.idtipo == 1 && img.idsubtipo == 1) {
                img1_1 = img.img;
            }
            if (img.idtipo == 1 && img.idsubtipo == 2) {
                img1_2 = img.img;
            }
            if (img.idtipo == 1 && img.idsubtipo == 3) {
                img1_3 = img.img;
            }
            if (img.idtipo == 1 && img.idsubtipo == 4) {
                img1_4 = img.img;
            }
        });

        $('#0_1').attr({
            "src": "archivos/tickets/" + img0_1,
            "onclick": (img0_1 == "" ? null : "viewPreview('" + img0_1 + "')")
        });
        $('#0_2').attr({
            "src": "archivos/tickets/" + img0_2,
            "onclick": (img0_2 == "" ? null : "viewPreview('" + img0_2 + "')")
        });
        $('#0_3').attr({
            "src": "archivos/tickets/" + img0_3,
            "onclick": (img0_3 == "" ? null : "viewPreview('" + img0_3 + "')")
        });
        $('#0_4').attr({
            "src": "archivos/tickets/" + img0_4,
            "onclick": (img0_4 == "" ? null : "viewPreview('" + img0_4 + "')")
        });
        $('#1_1').attr({
            "src": "archivos/tickets/" + img1_1,
            "onclick": (img1_1 == "" ? null : "viewPreview('" + img1_1 + "')")
        });
        $('#1_2').attr({
            "src": "archivos/tickets/" + img1_2,
            "onclick": (img1_2 == "" ? null : "viewPreview('" + img1_2 + "')")
        });
        $('#1_3').attr({
            "src": "archivos/tickets/" + img1_3,
            "onclick": (img1_3 == "" ? null : "viewPreview('" + img1_3 + "')")
        });
        $('#1_4').attr({
            "src": "archivos/tickets/" + img1_4,
            "onclick": (img1_4 == "" ? null : "viewPreview('" + img1_4 + "')")
        });
        $('#previewImg').modal('show')
    }

    function viewPreview(imagen) {
        $('#previewImagen').attr("src", "archivos/tickets/" + imagen)
        $('#imgdownload').attr({
            "href": "archivos/tickets/" + imagen,
            "download": "archivos/tickets/" + imagen
        })
    }

    let proSelect = null;
    let indSelect = null;
    let proSelect2 = null;
    let indSelect2 = null;

    function pasarProducto(index, opc, serie = 0, idpor = 0, idserie = 0, color = 0) {
        if (parseInt(opc) == 1) {
            $('#btnpasar1').attr({
                'disabled': false
            });
            $('#btnpasar1').attr('onclick', 'prodAveh(1,' + idserie + ',' + index + ',' + idpor + ')');
            var valida = 0;
            $.each(gpsf, function(ind, itema) {
                if (itema.proid == idpor) {
                    valida = 1;
                }
            });

            if (valida == 1) {
                /*checkedvivo(serie,idpor);*/
            } else {
                $('#tablavivo').html('');
            }


            $('#tbpxt tbody tr').each(function(i) {
                if (i == parseInt(index)) {
                    $('#fila_id_' + i).css({
                        'background-color': '#C70039',
                        'color': 'white'
                    });
                } else {
                    $('#fila_id_' + i).css({
                        'background-color': 'rgba(0,0,0,.05)',
                        'color': 'black'
                    });
                    $('#btnpasar1').attr({
                        'disabled': false
                    });
                }
            });
            /*if(proSelect==null){
                proSelect = ProxTEC[index];
                indSelect = index;
                $('#tbpxt tbody tr').each(function(i){
                    if(i==parseInt(index)){
                        $('#fila_id_'+i).css({'background-color':'#C70039','color':'white'});
                    }
                    else{
                        $('#fila_id_'+i).css({'background-color':'rgba(0,0,0,.05)','color':'black'});
                    }
                });
            }*/
            /*else{
                if(proSelect.idpxt==ProxTEC[index].idpxt){
                    $('#tbpxt tbody tr').each(function(i){
                        $('#fila_id_'+i).css({'background-color':'rgba(0,0,0,.05)','color':'black'});
                    });
                    proSelect = null;
                    indSelect = null;
                    $('#btnpasar1').attr({'disabled':true});
                }
                else{
                    proSelect = ProxTEC[index];
                    indSelect = index;
                    $('#btnpasar1').attr({'disabled':false});
                    $('#tbpxt tbody tr').each(function(i){
                        if(i==parseInt(index)){
                            $('#fila_id_'+i).css({'background-color':'#C70039','color':'white'});
                        }
                        else{
                            $('#fila_id_'+i).css({'background-color':'rgba(0,0,0,.05)','color':'black'});
                        }
                    });
                }
            }*/
        } else {
            $('#btnpasar2').attr('onclick', 'prodAveh(2,' + idserie + ',' + index + ',' + idpor + ')');
            $('#btnpasar2').attr({
                'disabled': false
            });

            $('#tbpxv tbody tr').each(function(i) {
                if (i == parseInt(index)) {
                    $('#fila_id2_' + i).css({
                        'background-color': '#C70039',
                        'color': 'white'
                    });
                } else {
                    $('#fila_id2_' + i).css({
                        'background-color': 'rgba(0,0,0,.05)',
                        'color': 'black'
                    });
                    $('#btnpasar2').attr({
                        'disabled': false
                    });
                }
            });
            /*if(proSelect2==null){
                proSelect2 = ProxVEH[index];
                indSelect2 = index;
                $('#tbpxv tbody tr').each(function(i){
                    if(i==parseInt(index)){
                        $('#fila_id2_'+i).css({'background-color':'#C70039','color':'white'});
                    }
                    else{
                        $('#fila_id2_'+i).css({'background-color':'rgba(0,0,0,.05)','color':'black'});
                    }
                });
            }*/
            /*else{
                if(proSelect2.idpxt==ProxVEH[index].idpxt){
                    $('#tbpxv tbody tr').each(function(i){
                        $('#fila_id2_'+i).css({'background-color':'rgba(0,0,0,.05)','color':'black'});
                    });
                    proSelect2 = null;
                    indSelect2 = null;
                    $('#btnpasar2').attr({'disabled':true});
                }
                else{
                    proSelect2 = ProxVEH[index];
                    indSelect2 = index;
                    $('#btnpasar2').attr({'disabled':false});
                    $('#tbpxv tbody tr').each(function(i){
                        if(i==parseInt(index)){
                            $('#fila_id2_'+i).css({'background-color':'#C70039','color':'white'});
                        }
                        else{
                            $('#fila_id2_'+i).css({'background-color':'rgba(0,0,0,.05)','color':'black'});
                        }
                    });
                }
            }*/
        }
    }

    function cambioinv(sensor, serie, din) {
        if (sensor == 0) {
            if (din == 1) {
                $('#din1inbadge').html('');
                $('#btnrep1').attr('onclick', '');
            } else if (din == 2) {
                $('#din2inbadge').html('');
                $('#btnrep2').attr('onclick', '');
            } else if (din == 3) {
                $('#din3inbadge').html('');
                $('#btnrep2').attr('onclick', '');
            }
        } else {
            if (din == 1) {
                $('#din1inbadge').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                $('#btnrep1').attr('onclick', 'cambioinv (' + sensor + ',' + serie + ',' + din + ')');
            } else if (din == 2) {
                $('#din2inbadge').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                $('#btnrep2').attr('onclick', 'cambioinv (' + sensor + ',' + serie + ',' + din + ')');
            } else if (din == 3) {
                $('#din3inbadge').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
                $('#btnrep3').attr('onclick', 'cambioinv (' + sensor + ',' + serie + ',' + din + ')');
            }
            env = {
                'sensor': sensor,
                'serie': serie
            };
            var send = JSON.stringify(env);
            $.ajax({
                url: 'operaciones.php',
                data: {
                    numero: '' + Math.floor(Math.random() * 9999999) + '',
                    operacion: 'detallevivoind',
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
                    if (respuesta.api.gps != 'Sin Datos.') {
                        var si = '';
                        var no = '';
                        $.each(sensoressel, function(ind, valor) {
                            if (valor.senid == sensor) {
                                si = valor.estado1;
                                no = valor.estado2;
                            }
                        });

                        $.each(respuesta.api.gps, function(ind, valor) {
                            if (din == 1) {
                                if (valor.din1 == 1) {
                                    $('#din1inbadge').html('<span style="cursor:pointer;" class="badge badge-success">' + si + '</span>');
                                } else {
                                    $('#din1inbadge').html('<span style="cursor:pointer;" class="badge badge-danger">' + no + '</span>');
                                }
                            } else if (din == 2) {
                                if (valor.din2 == 1) {
                                    $('#din2inbadge').html('<span style="cursor:pointer;" class="badge badge-success">' + si + '</span>');
                                } else {
                                    $('#din2inbadge').html('<span style="cursor:pointer;" class="badge badge-danger">' + no + '</span>');
                                }
                            } else if (din == 3) {
                                if (valor.din3 == 1) {
                                    $('#din3inbadge').html('<span style="cursor:pointer;" class="badge badge-success">' + si + '</span>');
                                } else {
                                    $('#din3inbadge').html('<span style="cursor:pointer;" class="badge badge-danger">' + no + '</span>');
                                }
                            }
                        });
                    }
                }
            });
        }
    }

    function checkedvivo(serie, idpro) {
        env = {
            'idpro': idpro,
            'serie': serie
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'detallevivo',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {
                $('#tablavivo').html('<table style="width:100%"><tr><td colspan="10" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr></table>');
            },
            error: function(respuesta) {

            },
            success: function(respuesta) {
                // console.log(respuesta);
                if (respuesta) {
                    var options = '-';
                    var egps = '-';
                    var bodegaapi = '-';
                    var fecha = '-';
                    var localidad = '-';
                    var velocidad = '-';
                    var din1 = '-';
                    var din2 = '-';
                    var din3 = '-';
                    var torqueb = '-';
                    var temp1b = '-';
                    var temp2b = '-';

                    var odometro = '-';
                    var odolitro = '-';
                    var hrsnmotor = '-';
                    var velcidada = '-';
                    var rpm = '-';
                    var nestanque = '-';
                    var tmotoa = '-';
                    var torquea = '-';
                    var adblue = '-';


                    $.each(sensoressel, function(ind, valor) {
                        if (ind == 0) {
                            options += '<option value="0">Seleccionar</option>';
                            options += '<option value="' + valor.senid + '">' + valor.sen_nombre + '</option>';
                        } else {
                            options += '<option value="' + valor.senid + '">' + valor.sen_nombre + '</option>';
                        }
                    });

                    if (respuesta.api.gps != 'Sin Datos.') {
                        $.each(respuesta.api.gps, function(ind, valor) {
                            localidad = valor.localidad;
                            velocidad = valor.velocidad;
                            fecha = valor.fechahora;
                            din1 = valor.din1;
                            din2 = valor.din2;
                            din3 = valor.din3;
                            odometro = valor.odometro;
                            egps = '';
                            bodegaapi = '';
                            torqueb = '';
                            temp1b = valor.temperatura1;
                            temp2b = valor.temperatura2;
                        });
                    }

                    if (respuesta.api.gps != 'Sin Datos.') {
                        $.each(respuesta.api.gps, function(ind, valor) {
                            odometro_can = valor.odometro_can;
                            odolitro = valor.odolitro;
                            hrsnmotor = valor.horometro;
                            velcidada = valor.velocidad_can;
                            rpm = valor.rpm;
                            nestanque = valor.estanque;
                            tmotoa = valor.temperatura_can;
                            torquea = valor.torque;
                            adblue = valor.ad_blue;
                        });
                    }

                    if (respuesta.api.gps != 'Sin Datos.') {
                        $.each(respuesta.api.gps, function(ind, valor) {
                            odometro = valor.odometro;
                            odolitro = valor.odolitro;
                            hrsnmotor = valor.horas_motor;
                            velcidada = valor.velocidad;
                            rpm = valor.rpm;
                            nestanque = valor.estanque;
                            testanque = valor.estanque_thermo;
                            tmotoa = valor.temperatura_can;
                            torquea = valor.torque;
                            adblue = valor.ad_blue;
                        });
                    }


                    $('#tablavivo').html('<table style="width: 100%;" cellspacing="0"><tbody><tr><td rowspan="7" align="center" style="width:25%; padding:5px;">&nbsp;</td><td style="width: 20%; border: black solid 1px; background-color:#212529; color:white;" colspan="2" align="center">GPS</td><td style="width: 5%;" align="center">&nbsp;</td><td style="width: 25%; border: black solid 1px; background-color:#212529; color:white;" colspan="2" align="center">TELEMETRIA</td></tr><tr style=""><td style="border: black solid 1px; background-color:#F1F3F4;" align="left">Equipo GPS</td><td style="border: black solid 1px;" align="center">' + egps + '</td><td style="width: 10%;" align="center">&nbsp;</td><td style="border: black solid 1px; background-color:#F1F3F4;" align="left">Odometro</td><td style="border: black solid 1px;" align="center">' + odometro + '</td></tr><tr><td style="border: black solid 1px; background-color:#F1F3F4;" align="left">Serie</td><td style="border: black solid 1px;" align="center">' + respuesta.serie + '</td><td style="width: 10%;" align="center">&nbsp;</td><td style="border: black solid 1px; background-color:#F1F3F4;" align="left">Odolitro</td><td style="border: black solid 1px;" align="center">' + odolitro + '</td></tr><tr><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">Bodega</td><td style="border: black solid 1px;" align="center">' + bodegaapi + '</td><td style="width: 10%;" align="center">&nbsp;</td><td style="border: black solid 1px; background-color:#F1F3F4;" align="left">HRS Motor</td><td style="border: black solid 1px;" align="center">' + hrsnmotor + '</td></tr><tr><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">Fecha/Hora</td><td style="border: black solid 1px;" align="center">' + fecha + '</td><td style="width: 10%;" align="center">&nbsp;</td><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">Velocidad</td><td style="border: black solid 1px;" align="center">' + velcidada + '</td></tr><tr><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">Ubicaci&oacute;n</td><td style="border: black solid 1px;" align="center">' + localidad + '</td><td style="width: 10%;" align="center">&nbsp;</td><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">RPM</td><td style="border: black solid 1px;" align="center">' + rpm + '</td></tr><tr><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">Velocidad</td><td style="border: black solid 1px;" align="center">' + velocidad + '</td><td style="width: 10%;" align="center">&nbsp;</td><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">Nivel Estanque</td><td style="border: black solid 1px;" align="center">' + nestanque + '</td></tr><tr><td align="left"><select id="din1" class="form-control" onchange="cambioinv(this.value,' + serie + ',1)">' + options + '</select></td><td style="border: black solid 1px; background-color:#F1F3F4;" align="left" id="din1in" name="' + din1 + '">Din1</td><td style="border: black solid 1px;" align="center" id="din1inbadge"></td><td style="width: 10%;" align="center"><button type="button" class="btn btn-warning" id="btnrep1" onclick=""><i class="fa fa-history" aria-hidden="true"></i></button></td><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">T&deg; Motor</td><td style="border: black solid 1px;" align="center">' + tmotoa + '</td></tr><tr><td align="left"><select id="din2" class="form-control" onchange="cambioinv(this.value,' + serie + ',2)">' + options + '</select></td><td style="border: black solid 1px;background-color:#F1F3F4;" align="left" id="din2in" name="' + din2 + '">Din2</td><td style="border: black solid 1px;" align="center" id="din2inbadge"></td><td style="width: 10%;" align="center"><button type="button" class="btn btn-warning" id="btnrep2" onclick=""><i class="fa fa-history" aria-hidden="true"></i></button></td><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">Torque</td><td style="border: black solid 1px;" align="center">' + torquea + '</td></tr><tr><td align="left"><select id="din3" class="form-control" onchange="cambioinv(this.value,' + serie + ',3)">' + options + '</select></td><td style="border: black solid 1px;background-color:#F1F3F4;" align="left" id="din3in" name="' + din3 + '">Din3</td><td style="border: black solid 1px;" align="center" id="din3inbadge"></td><td style="width: 10%;" align="center"><button type="button" class="btn btn-warning" id="btnrep3" onclick=""><i class="fa fa-history" aria-hidden="true"></i></button></td><td style="border: black solid 1px;background-color:#F1F3F4;" align="left">AD Blue</td><td style="border: black solid 1px;" align="center">' + adblue + '</td></tr><tr><td rowspan="7"></td><td style="border: black solid 1px;" align="left">Temp1</td><td style="border: black solid 1px;" align="center">' + temp1b + '</td><td style="width: 10%;" align="center">&nbsp;</td></tr><tr><td style="border: black solid 1px;" align="left">Temp2</td><td style="border: black solid 1px;" align="center">' + temp2b + '</td></tr></tbody></table>');
                }

                cambioinv(1, serie, 1);
                $('#din1').val(1);
            }
        });
    }

    function validarOut1(serie, idpro) {

        let idModal ="#modalSeleccion";
        let cliente_cuenta = window.vehiculos[0].cuenta;

        $(idModal).attr("data-imei", serie);
    

        $("#imei_out1").val(serie);
        $("#patente_out1").val(window.vehiculos[0].patente);
        


        $(idModal).attr("data-cliente", cliente_cuenta);
        $(idModal).modal('show');
        
        $("#cliente_accesorio").val(cliente_cuenta);
        // console.log("cliente actual : ",cliente_cuenta );

        env = {
            'idpro': idpro,
            'serie': serie
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'tipo_comando',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {
                // $('#tablavivo').html('<table style="width:100%"><tr><td colspan="10" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr></table>');
            },
            error: function(respuesta) {

            },
            success: function(respuesta) {
                // console.log(respuesta);
                if (respuesta && respuesta.tipo_comandos) {

                    comandos = respuesta.tipo_comandos; // Guardamos los datos en un array

                    let $select = $("#TipoComandoOut1"); // Capturamos el select
                    $select.empty(); // Limpiamos opciones anteriores
                    $select.append('<option value="">Seleccione una opción</option>'); // Opción por defecto

                    // Iteramos sobre los datos recibidos y agregamos opciones al select
                    $.each(respuesta.tipo_comandos, function(ind, valor) {
                        // console.log("data:", ind, valor);
                        $select.append('<option value="' + valor.id + '">' + valor.nombre + '</option>');
                    });
                }

                // cambioinv(1, serie, 1);
                // $('#din1').val(1);
            }
        });

        return true;

    }

    var band1Out1 = false;
    var band1Out2 = false;

    // Función para enviar los datos del modal via AJAX
    function enviarDatos() {

        //se bloquea boton para no apretar mas de 1 vez
        var btn = document.getElementById("enviarBtn");
        btn.disabled = true;

        let imei           = $("#modalSeleccion").attr("data-imei");
        let cliente        = $("#modalSeleccion").attr("data-cliente");
        let comandoId      = $("#TipoComandoOut1").val();
        let detalleComando = $("#TipoComandoOut1Detalle").val();
        let patente        = $("#patente_out1").val();

        // console.log( "imei : ", (imei==860896050263847) );
        // || !(imei==860896050263847)

        if ( (!comandoId || !detalleComando || !imei || !cliente) ) {
            // alert("Por favor, selecciona un comando y un detalle antes de enviar.");
            // console.log("comando no enviado");
            Swal.fire({
                icon: 'warning',
                // title: 'Respuesta recibida',
                text: 'Por favor, selecciona un comando y un detalle antes de enviar.',
                showConfirmButton: true, // Muestra el botón de confirmación
                confirmButtonText: 'Aceptar', // Texto personalizado para el botón
            }).then((result) => {
                btn.disabled = false;
                if (result.isConfirmed) {
                    // console.log('Usuario confirmó');
                    // Aquí puedes agregar lo que quieras hacer después de que el usuario confirme
                }
            });

            btn.disabled = false;
            return;
        }

        // console.log("comando se enviara");

        

        // btn.disabled = false;
        // return true;

        

         // Mostrar un Swal de carga mientras se procesa el comando
        Swal.fire({
            title: 'Enviando comando...',
            text: 'Por favor espere...',
            allowOutsideClick: false,  // No permitirá cerrar el Swal al hacer clic fuera
            didOpen: () => {
                Swal.showLoading();  // Muestra el ícono de carga
            }
        });

        env = {
                comando: comandoId,
                detalle: detalleComando,
                imei:    imei,
                cliente: cliente,
                patente: patente,
            };
        var send = JSON.stringify(env);

        $.ajax({
            url: 'operaciones.php',
            type: 'POST',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'ejecutar_comando',
                retornar: 'no',
                envio: send
            },
            dataType: 'json',
            beforeSend: function () {
                // console.log("Enviando comando...");
            },
            success: function (respuesta) {
                // console.log("Respuesta del servidor:", respuesta);
                // alert("Comando enviado con éxito.");

                let timeTest = 60000;
                // timeTest = 6000;
                if(!respuesta.error){

                    $msgComando = "";
                    if (detalleComando === "0" || detalleComando === "00") {
                        estado = "0";  // Si es "0" o "00", cambiarlo a "1"
                        $msgComando = "Comando a Desactivar";
                    } else if (detalleComando === "1" || detalleComando === "01") {
                        estado = "1";  // Si es "1" o "01", cambiarlo a "0"
                        $msgComando = "Comando a Activar";
                    }

                    //1️⃣ Muestra la alerta inicial (esperando respuesta)
                    let swalInstance = Swal.fire({
                        icon: 'info',
                        title: 'Procesando...',
                        html: 'Esperando respuesta del vehículo...<br><br>'+$msgComando,
                        allowOutsideClick: false, // Evita que se cierre al hacer clic afuera
                        showConfirmButton: false, // Oculta el botón de "OK"
                        timer: timeTest, // Mantiene la alerta abierta 30 segundos
                        didOpen: () => {
                            Swal.showLoading(); // Muestra animación de carga
                        }
                    });

                    

                    let maxTime = timeTest; // Máximo tiempo de espera (30 segundos)
                    let startTime = Date.now(); // Tiempo de inicio

                    // 2️⃣ Llamar al servicio cada 3 segundos hasta recibir respuesta o llegar a 30s
                    let checkResponseInterval = setInterval(() => {
                        let elapsedTime = Date.now() - startTime; // Tiempo transcurrido

                        $.ajax({
                            url: "https://www.ds-tms.com/api/v5/statusCCorrienteCloux", // URL del endpoint
                            type: "POST",
                            data: { imei: imei, empresa:cliente, estado:estado, patente:patente, tipo:comandoId }, // Parámetro IMEI
                            success: function(response) {
                                if (response.success) { // ✅ Si la API devuelve la respuesta esperada
                                    clearInterval(checkResponseInterval); // 3️⃣ Detiene las llamadas AJAX
                                    swalInstance.close(); // Cierra la alerta de carga

                                    if(estado == 0){
                                        band1Out1=true;
                                    }

                                    if(estado == 1){
                                        band1Out2=true;
                                    }

                                    if( band1Out1 && band1Out2 ){
                                        document.getElementById("registrarOutBtn").classList.remove("d-none");
                                    }
                                    

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Respuesta recibida',
                                        text: response.message,
                                        showConfirmButton: true, // Muestra el botón de confirmación
                                        confirmButtonText: 'Aceptar', // Texto personalizado para el botón
                                    }).then((result) => {
                                        btn.disabled = false;
                                        if (result.isConfirmed) {
                                            // console.log('Usuario confirmó');
                                            // Aquí puedes agregar lo que quieras hacer después de que el usuario confirme
                                        }
                                    });

                                }
                            },
                            error: function(xhr) {
                                console.error("Error en la petición:", error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Hubo un error al enviar el comando.',
                                    showConfirmButton: true,
                                }).then((result) => {
                                    btn.disabled = false;  // Habilitar el botón en caso de error
                                });
                            }
                        });

                        // 5️⃣ Si pasan 30 segundos y no hay respuesta, se cierra el proceso
                        if (elapsedTime >= maxTime) {
                            clearInterval(checkResponseInterval);
                            swalInstance.close();

                            Swal.fire({
                                icon: 'error',
                                title: 'Tiempo agotado',
                                text: 'No se recibió respuesta del vehículo en el tiempo esperado.',
                                showConfirmButton: true, // Muestra el botón de confirmación
                                confirmButtonText: 'Aceptar', // Texto personalizado para el botón
                            }).then((result) => {
                                btn.disabled = false;
                                if (result.isConfirmed) {
                                    // console.log('Usuario confirmó');
                                    // Aquí puedes agregar lo que quieras hacer después de que el usuario confirme
                                }
                            });

                            // Swal.fire({
                            //     icon: 'error',
                            //     title: 'Tiempo agotado',
                            //     text: 'No se recibió respuesta del vehículo en el tiempo esperado.',
                            //     showConfirmButton: true
                            // });
                        }
                    }, 5000); // Se ejecuta cada 3 segundos

                }else{
                    alert("error");
                }
                

                // $('#modalSeleccion').modal('hide'); // Cierra el modal
            },
            error: function (xhr, status, error) {
                console.error("Error en la petición:", error);
                alert("Hubo un error al enviar el comando.");
            }
        });

    }

    function registrarOut1() {

        var btnOut1        = document.getElementById("registrarOutBtn") ;
        btnOut1.disabled   = true ;

        let imei           = $("#modalSeleccion").attr("data-imei");
        let cliente        = $("#modalSeleccion").attr("data-cliente");
        let comandoId      = $("#TipoComandoOut1").val();
        let patente        = $("#patente_out1").val();
        let detalleComando = $("#TipoComandoOut1Detalle").val();

        if ( !comandoId || !detalleComando || !imei || !cliente || !patente ) {
            alert("Por favor, selecciona un comando y un detalle antes de enviar.");
            btnOut1.disabled = false;
            return;
        }

         // Mostrar un Swal de carga mientras se procesa el comando
        Swal.fire({
            title: 'Registrando vehículo...',
            text: 'Por favor espere...',
            allowOutsideClick: false,  // No permitirá cerrar el Swal al hacer clic fuera
            didOpen: () => {
                Swal.showLoading();  // Muestra el ícono de carga
            }
        });
        

        env = {
                comando : comandoId,
                detalle : detalleComando,
                imei    : imei,
                cliente : cliente,
                patente : patente,
            };
        var send = JSON.stringify(env);

        // Swal.close();
        // btnOut1.disabled = false;
        // console.log("send : ",send);
        // return true;

        // console.log("registrando GPS: ");
        
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'agregar_vehiculo_icc',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {
                // $('#tablavivo').html('<table style="width:100%"><tr><td colspan="10" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr></table>');
            },
            error: function(respuesta) {
                btnOut1.disabled = false;
            },
            success: function(respuesta) {

                btnOut1.disabled = false;

                if (respuesta && !respuesta.error) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Respuesta recibida',
                        text: respuesta.message,
                        showConfirmButton: true, // Muestra el botón de confirmación
                        confirmButtonText: 'Aceptar', // Texto personalizado para el botón
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // console.log('Usuario confirmó');
                            // Aquí puedes agregar lo que quieras hacer después de que el usuario confirme
                        }
                    });

                }else{
                    alert("error");
                }

            }
        });

    }

    function prodAveh(opc, idserie = 0, index, idpro = 0) {
        if (parseInt(opc) == 1) {
            var msjalert = 0;
            if (sercehialb1[0].ser_id != null && sercehialb1[0].ser_id != 0 && sercehialb1[0].ser_id != '') {
                // console.log(proSelect);
                var html1 = '';
                var productonombre = '';
                var valida = 1;
                if (sercehialb.length > 0) {
                    $.each(sercehialb, function(ind, item) {
                        $.each(gpsf, function(inda, itema) {
                            if (item.proid == itema.proid) {
                                $.each(gpsf, function(indaa, itemaa) {
                                    if (idpro == itemaa.proid) {
                                        valida = 0;
                                    }
                                });
                            }
                        });
                    });
                }
                $.each(sercehialb1, function(i, item) {
                    if (valida == 1) {
                        if (idserie == item.ser_id) {
                            if ($('#din1').val() == 0) {
                                toastr.info('Debes al menos seleccionar el DIN1');
                            } else {
                                html1 += "<div class='form-group'><label class='col-sm-3'>Nº Serie</label><div class='col-sm-4'><input type='text' class='form-control' id='nserie' name='nserie' value=" + item.ser_codigo + " disabled></div></div>";
                                productonombre = item.pro_nombre;
                                productonombre = item.pro_nombre;
                                // console.log(item.pro_nombre);

                                html = "<span class='label label-success'>Instalar producto en el Vehículo</span><hr><form class='form-horizontal'><div class='form-group'><label class='col-sm-3'>Producto</label><div class='col-sm-9'><input type='text' class='form-control' name='producto' disabled value='" + productonombre + "'></div></div>" + html1 + "<div class='form-group'></div></form>";
                                let button = "<button type='button' id='instalar' class='btn btn-success btn-rounded' onclick='instalarPronew(\"" + index + "\"," + idserie + ",1)'>Instalar</button>&nbsp;<button type='button' data-dismiss='modal' class='btn btn-danger btn-rounded' onclick='noInstalarPro()'>Cancelar</button>";
                                $("#mticket .modal-header").addClass('bg-info');
                                $('#mticket .modal-title').text('Opciones de Producto');
                                $('#mticket .modal-body').html(html);
                                $('#mticket .modal-footer').html(button);
                                $('#mticket').modal('show');
                                $('#btnpasar1').attr({
                                    'disabled': true
                                });
                            }
                        }
                    } else {
                        if (msjalert == 0) {
                            toastr.info('No puedes seleccionar mas de GPS para un vehiculo');
                            msjalert = 1;
                        }
                    }
                });
            } else {
                toastr.info('Debe seleccionar un producto a traspasar.')
            }
        } else {
            // console.log(sercehialb);
            if (sercehialb[0].ser_id != null && sercehialb[0].ser_id != 0 && sercehialb[0].ser_id != '') {
                let html = '';
                let html1 = '';
                var productonombre = '';
                var b = '';
                var m = '';
                $.each(sercehialb, function(i, item) {
                    if (idserie == item.ser_id) {
                        html1 += "<div class='form-group'><label class='col-sm-3'>Nº Serie</label><div class='col-sm-4'><input type='text' class='form-control' id='nserie' name='nserie' value=" + item.ser_codigo + " disabled></div></div>";
                        productonombre = item.pro_nombre;
                        // console.log(item.pro_nombre);
                        // console.log(productonombre);
                        if (item.condicion == 1) {
                            b = 'selected';
                            m = '';
                        } else {
                            b = '';
                            m = 'selected';
                        }
                    }
                });
                html = "<span class='label label-success'>Desinstalar producto y agregar a bodega técnico</span><hr><form class='form-horizontal'><div class='form-group'><label class='col-sm-3'>Producto</label><div class='col-sm-9'><input type='text' class='form-control' name='producto' disabled value='" + productonombre + "'></div><div class='form-group'><label class='col-sm-3'>Estado</label><div class='col-sm-6'><select id='estadopro' name='estadopro' class='form-control'><option value=''>SELECCIONAR</option><option value=1 " + b + ">BUENO</option><option value=0 " + m + ">MALO</option><option value=2 " + m + ">ROBADO</option><option value=3 " + m + ">SINIESTRADO</option></select></div><div class='form-group'><label class='col-sm-3'>SubEstado</label><div class='col-sm-6'><select id='subestadopro' name='subestadopro' class='form-control'><?= $optsub ?></select></div><div class='form-group'><label class='col-sm-3'>Observaciones</label><div class='col-sm-8'><textarea id='observaciones' name='observaciones' class='form-control rznone' rows=5></textarea></div></div>" + html1 + "</form>";
                let button = "<button type='button' class='btn btn-success btn-rounded' id='desinstalar' onclick='instalarPronew(\"" + index + "\"," + idserie + ",2)'>Desinstalar</button>&nbsp;<button type='button' data-dismiss='modal' class='btn btn-danger btn-rounded' onclick='noInstalarPro()'>Cancelar</button>";
                $("#mticket .modal-header").addClass('bg-danger');
                $('#mticket .modal-title').text('Opciones de Producto');
                $('#mticket .modal-body').html(html);
                $('#mticket .modal-footer').html(button);
                $('#mticket').modal('show');
                $('#btnpasar2').attr({
                    'disabled': true
                });
                sercehialb = [];
            } else {
                toastr.info('Debe seleccionar un producto a traspasar.')
            }
        }
    }

    function desintalarlaimei(idimei) {
        env = {
            'idimei': idimei
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'desintalarlaimei',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(data) {

            },
            error: function(data) {

            },
            success: function(data) {


            }
        });
    }

    function cancelarCierre() {
        $("#fcerrarticket").hide();
        //$("#tblistaticket").removeClass("col-md-4").addClass("col-md-12");
        $("#tblistaticket").show();
        $('#btnreloadticket').attr('onclick', null)
    }

    function quitarProducto(idxpro) {
        pxv = ProxVEH[idxpro];
        // console.log(pxv);
        if (pxv["tieneserie"] == "NO") {
            inp = "<div class='form-group'><label class='col-sm-3'>Cantidad</label><div class='col-sm-4'><input type='text' class='form-control' name='cantidad' value='" + pxv.cantidad + "'></div></div>";
        } else {
            inp = "<div class='form-group'><label class='col-sm-3'>Nº Serie</label><div class='col-sm-4'><input type='text' class='form-control' name='nserie' disabled value='" + pxv.serie + "'></div></div>";
        }
        form = "<span class='label label-info'>Desinstalar producto y agregar a bodega técnico</span><hr><form class='form-horizontal'><div class='form-group'><label class='col-sm-3'>Producto</label><div class='col-sm-8'><input type='text' class='form-control' name='producto' disabled value='" + pxv["producto"] + "'></div></div>" + inp + "<div class='form-group'><label class='col-sm-3'>Estado</label><div class='col-sm-6'><select name='estadopro' class='form-control'><option value=''>SELECCIONAR</option><option value=1>BUENO</option><option value=0>MALO</option></select></div></div><div class='form-group'><label class='col-sm-3'>Observaciones</label><div class='col-sm-8'><textarea name='observaciones' class='form-control rznone' rows=5></textarea></div></div><div class='form-group'><div class='col-sm-6 col-sm-offset-3'><button type='button' class='btn btn-info btn-rounded' onclick='sacarPro(\"" + idxpro + "\")'>Desinstalar</button>&nbsp;<button type='button' class='btn btn-danger btn-rounded' onclick='noSacarPro()'>Cancelar</button></div></div></form>";
        $("#agregarproatec").html(form);
    }

    function sacarPro(index) {
        pxv = ProxVEH[index];
        idpxv = pxv["idpxv"];
        idestado = $("#estadopro").val();
        subestado = $('#subestadopro').val();
        obs = $("#observaciones").val();
        idtecnico = $("#idtecnico").val();
        if (pxv["tieneserie"] == "NO") {
            cantidad = parseInt($("#cantidad").val());
            if (cantidad > parseInt(pxv["cantidad"])) {
                alert("La cantidad a desinstalar no puede ser mayor a la cantidad instalada en el vehículo");
                return;
            }
        } else {
            cantidad = 1;
        }
        // console.log(pxv);
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'desinstalarProducto',
            pxv_id: idpxv,
            pxv_cantidad: cantidad,
            stockactual: pxv["cantidad"],
            "tieneserie": pxv["tieneserie"],
            tecnico: idtecnico,
            estado: idestado,
            subestado: subestado,
            observaciones: obs,
            tipo: pxv["tipo"],
            ideasi: pxv["ideasi"],
            retornar: 'no'
        }, function(data) {
            // console.log(data);
            datos = $.parseJSON(data);
            ProxVEH = datos["pxv"];
            ProxTEC = datos["pxt"];
            fpxv = "";
            $('#mticket').modal('hide');
            $.each(ProxVEH, function(ipxv, vpxv) {
                let tipo = 'Kit GPS';
                let nametipo = vpxv.ideasi;
                let seriesim;
                let seriegps;
                if (vpxv.kitdetalle.length > 0) {
                    seriesim = vpxv.kitdetalle[0].seriesim;
                    seriegps = vpxv.kitdetalle[0].seriegps;
                }
                if (parseInt(vpxv.tipo) == 1) {
                    tipo = 'Producto';
                    nametipo = vpxv.producto;
                    seriegps = vpxv.serie;
                    seriesim = '';
                }
                fpxv += "<tr id='fila_id2_" + ipxv + "' onclick='pasarProducto(\"" + ipxv + "\",2)'><td>" + vpxv.cantidad + "</td><td>" + nametipo + "</td><td>" + vpxv.tieneserie + "</td><td>" + seriegps + "</td><td>" + seriesim + "</td><td>" + tipo + "</td><td class='text-center oculto'><span class='text-red pointer' onclick='quitarProducto(\"" + ipxv + "\")'><i class='fa fa-arrow-right' aria-hidden='true'></i></span></td></tr>";
            });
            $("#tbpxv tbody").html(fpxv);

            fpxt = "";
            $.each(ProxTEC, function(ipxt, vpxt) {
                let tipo = 'Kit GPS';
                let nametipo = vpxt.ideasi;
                let seriesim;
                if (vpxt.kitdetalle.length > 0) {
                    seriesim = vpxt.kitdetalle[0].seriesim;
                }

                if (parseInt(vpxt.tipo) == 1) {
                    tipo = 'Producto';
                    nametipo = vpxt.producto;
                    seriesim = '';
                }
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

                var btnchvivio = "";
                if (vpxt.familia == 'GPS') {
                    btnchvivio = "<button type='button' class='btn btn-sm btn-success btn-circle' style='color: white;' onclick='checkedvivo(" + vpxt.serie + ",0)'><i class='fas fa-server'></i></button>";
                }

                fpxt += "<tr id='fila_id_" + ipxt + "' ><td class='text-center'>" + vpxt.cantidad + "</td><td>" + btnchvivio + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1)' class='" + trcolor + "'>" + nametipo + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1)' class='" + trcolor + "'>" + vpxt.tieneserie + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1)' class='" + trcolor + "'>" + vpxt.serie + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1)' class='" + trcolor + "'>" + tipo + "</td>" + estado + "</tr>";
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
        // console.log(pxt);
        if (pxt["tieneserie"] == "NO") {
            inp = "<div class='form-group'><label class='col-sm-3'>Cantidad</label><div class='col-sm-4'><input type='text' class='form-control' name='cantidad'></div></div>";
        } else {
            inp = "<div class='form-group'><label class='col-sm-3'>Nº Serie</label><div class='col-sm-4'><input type='text' class='form-control' name='nserie' value=" + pxt.serie + "></div></div>";
        }
        form = "<span class='label label-success'>Instalar producto en el Vehículo</span><hr><form class='form-horizontal'><div class='form-group'><label class='col-sm-3'>Producto</label><div class='col-sm-8'><input type='text' class='form-control' name='producto' disabled value='" + pxt.producto + "'></div></div>" + inp + "<div class='form-group'><div class='col-sm-6 col-sm-offset-3'><button type='button' class='btn btn-success btn-rounded' onclick='instalarPro(\"" + idxtec + "\")'>Instalar</button>&nbsp;<button type='button' data-dismiss='modal' class='btn btn-danger btn-rounded' onclick='noInstalarPro()'>Cancelar</button></div></div></form>";
        $("#agregarproaveh").html(form);
    }

    function instalarPronew(index, idserie = 0, tabla = 0) {
        idtecnico = $("#idtecnico").val();
        idpatente = $("#idpatente").val();
        let _pantente = $("#idpatentetxt").val();

        var condicion = '';
        var subestado = '';
        var obser = '';
        var ds1 = 0;
        var ds2 = 0;
        var ds3 = 0;

        if (tabla == 2) {
            condicion = $('#estadopro').val();
            subestado = $('#subestadopro').val();
            obser = $('#observaciones').val();

            Swal.fire({
                title: '\u00BF¿Estás seguro de desinstalar esta imei?',
                text: "Este irá a la bodega del técnico y se borrará, eso significa que ya no seguirá transmitiendo ese vehículo.",
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: 'Aceptar',
                denyButtonText: `Cancelar`,
            }).then((result) => {
                if (result.isConfirmed) {
                    desintalarlaimei(idserie);
                    let env = {
                        'idpatente': idpatente,
                        'idtecnico': idtecnico,
                        'idserie': idserie,
                        'tabla': tabla,
                        'condicion': condicion,
                        'subestado': subestado,
                        'obser': obser,
                        'din1': ds1,
                        'din2': ds2,
                        'din3': ds3,
                        'patente': _pantente
                    };
                    var send = JSON.stringify(env);
                    $.ajax({
                        url: 'operaciones.php',
                        data: {
                            numero: '' + Math.floor(Math.random() * 9999999) + '',
                            operacion: 'insertarinstalacionnew',
                            retornar: 'no',
                            envio: send
                        },
                        type: 'post',
                        dataType: 'json',
                        beforeSend: function(respuesta) {
                            if (tabla == 1) {
                                $('#instalar').attr('disabled', true);
                                $('#instalar').text('Cargando...');
                            } else {
                                $('#desinstalar').attr('disabled', true);
                                $('#desinstalar').text('Cargando...');
                            }
                        },
                        error: function(respuesta) {
                            if (tabla == 1) {
                                $('#instalar').attr('disabled', false);
                                $('#instalar').text('Instalar');
                            } else {
                                $('#desinstalar').attr('disabled', false);
                                $('#desinstalar').text('Desinstalar');
                            }
                        },
                        success: function(respuesta) {
                            if (respuesta.logo == 'success') {
                                toastr.success(respuesta.mensaje);
                                if (tabla == 1) {
                                    $('#fila_id_' + index).remove();
                                    dinsalb.push({
                                        'idserie': idserie,
                                        'idvehiculo': idpatente,
                                        'idtecnico': idtecnico,
                                        'din1': ds1,
                                        'din2': ds2,
                                        'din3': ds3
                                    });
                                } else {
                                    var temdin = [];
                                    $.each(dinsalb, function(ind, valor) {
                                        if (idserie != valor.idserie) {
                                            temdin.push({
                                                'idserie': valor.idserie,
                                                'idvehiculo': valor.idvehiculo,
                                                'idtecnico': valor.idtecnico,
                                                'din1': valor.din1,
                                                'din2': valor.din2,
                                                'din3': valor.din3
                                            });
                                        }
                                    });
                                    dinsalb = temdin;
                                    $('#fila_id2_' + index).remove();
                                }
                                $('.close').click();
                                $('#din1').val(0);
                                $('#din2').val(0);
                                $('#din3').val(0);
                                noInstalarPro();
                                mandatablados(index, idserie, 1);
                                mandatablados(index, idserie, 2);

                            } else {
                                toastr.error(respuesta.mensaje);
                            }

                            if (tabla == 1) {
                                $('#instalar').attr('disabled', false);
                                $('#instalar').text('Instalar');
                            } else {
                                $('#desinstalar').attr('disabled', false);
                                $('#desinstalar').text('Desinstalar');
                            }
                        }
                    });
                } else if (result.isDenied) {
                    return;
                }
            })


        } else {
            ds1 = $('#din1').val();
            ds2 = $('#din2').val();
            ds3 = $('#din3').val();
            Swal.fire({
                title: '\u00BF¿Estás seguro de instalar esta imei?',
                text: "Este imei se instalará en vehículo seleccionado, eso significa que ya no seguirá en la bodega.",
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: 'Aceptar',
                denyButtonText: `Cancelar`,
            }).then((result) => {
                if (result.isConfirmed) {
                    desintalarlaimei(idserie);
                    let env = {
                        'idpatente': idpatente,
                        'idtecnico': idtecnico,
                        'idserie': idserie,
                        'tabla': tabla,
                        'condicion': condicion,
                        'subestado': subestado,
                        'obser': obser,
                        'din1': ds1,
                        'din2': ds2,
                        'din3': ds3,
                        'patente': _pantente
                    };
                    var send = JSON.stringify(env);
                    $.ajax({
                        url: 'operaciones.php',
                        data: {
                            numero: '' + Math.floor(Math.random() * 9999999) + '',
                            operacion: 'insertarinstalacionnew',
                            retornar: 'no',
                            envio: send
                        },
                        type: 'post',
                        dataType: 'json',
                        beforeSend: function(respuesta) {
                            if (tabla == 1) {
                                $('#instalar').attr('disabled', true);
                                $('#instalar').text('Cargando...');
                            } else {
                                $('#desinstalar').attr('disabled', true);
                                $('#desinstalar').text('Cargando...');
                            }
                        },
                        error: function(respuesta) {
                            if (tabla == 1) {
                                $('#instalar').attr('disabled', false);
                                $('#instalar').text('Instalar');
                            } else {
                                $('#desinstalar').attr('disabled', false);
                                $('#desinstalar').text('Desinstalar');
                            }
                        },
                        success: function(respuesta) {
                            if (respuesta.logo == 'success') {
                                toastr.success(respuesta.mensaje);
                                // console.log("Exito");
                                if (tabla == 1) {
                                    $('#fila_id_' + index).remove();
                                    dinsalb.push({
                                        'idserie': idserie,
                                        'idvehiculo': idpatente,
                                        'idtecnico': idtecnico,
                                        'din1': ds1,
                                        'din2': ds2,
                                        'din3': ds3
                                    });
                                } else {
                                    var temdin = [];
                                    $.each(dinsalb, function(ind, valor) {
                                        if (idserie != valor.idserie) {
                                            temdin.push({
                                                'idserie': valor.idserie,
                                                'idvehiculo': valor.idvehiculo,
                                                'idtecnico': valor.idtecnico,
                                                'din1': valor.din1,
                                                'din2': valor.din2,
                                                'din3': valor.din3
                                            });
                                        }
                                    });
                                    dinsalb = temdin;
                                    $('#fila_id2_' + index).remove();
                                }
                                $('.close').click();
                                $('#din1').val(0);
                                $('#din2').val(0);
                                $('#din3').val(0);
                                noInstalarPro();
                                mandatablados(index, idserie, 1);
                                mandatablados(index, idserie, 2);

                            } else {
                                console.log("Error");
                                toastr.error(respuesta.mensaje);
                            }

                            if (tabla == 1) {
                                $('#instalar').attr('disabled', false);
                                $('#instalar').text('Instalar');
                            } else {
                                $('#desinstalar').attr('disabled', false);
                                $('#desinstalar').text('Desinstalar');
                            }
                        }
                    });
                } else if (result.isDenied) {
                    return;
                }
            })


        }

    }

    function mandatablados(index, idserie = 0, tabla = 0) {
        if (tabla == 1) {
            $("#tbpxv tbody").html('<tr><td colspan="9" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
        } else {
            $("#tbpxt tbody").html('<tr><td colspan="7" align="center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></td></tr>');
        }

        idtecnico = $("#idtecnico").val();
        idpatente = $("#idpatente").val();
        env = {
            'idpatente': idpatente,
            'idtecnico': idtecnico,
            'idserie': idserie,
            'tabla': tabla
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'obtenerinstalaciones',
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
                var fpxt = '';
                if (tabla == 1) {
                    sercehialb = [];
                } else {
                    sercehialb1 = [];
                }

                if (respuesta.length > 0) {
                    if (respuesta[0].ser_id != null && respuesta[0].ser_id != undefined && respuesta[0].ser_id != '') {
                        $.each(respuesta, function(ipxt, item) {
                            switch (item.condicion) {
                                case 'BUENO':
                                    trcolor = "";
                                    add = "<td class='text-center'><span class='text-green' style='cursor:pointer;' onclick='agregarProducto(\"" + ipxt + "\")'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
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

                            if (tabla == 1) {
                                sercehialb.push({
                                    'ser_id': item.ser_id,
                                    'ser_codigo': item.ser_codigo,
                                    'condicion': item.ser_condicion,
                                    'pro_nombre': item.pro_nombre,
                                    'din1d': item.din1,
                                    'din2d': item.din2,
                                    'din3d': item.din3,
                                    'proid': item.pro_id
                                });

                                if (item.din1 == null) {
                                    item.din1 = '-';
                                }

                                if (item.din2 == null) {
                                    item.din2 = '-';
                                }

                                if (item.din3 == null) {
                                    item.din3 = '-';
                                }

                                if (item.ser_codigo == '') {
                                    var sercod = 0;
                                } else {
                                    var sercod = item.ser_codigo;
                                }

                                if (item.pro_nombre == '' || item.pro_nombre == null) {
                                    item.pro_nombre = '-';
                                }
                                fpxt += "<tr id='fila_id2_" + ipxt + "' onclick='pasarProducto(\"" + ipxt + "\",2,\"" + sercod + "\"," + item.pro_id + "," + item.ser_id + ")' class='" + trcolor + "'><td class='text-center'>1</td><td>" + item.pro_nombre + "</td><td>SI</td><td>" + item.ser_codigo + "</td><td></td><td>Producto</td><td align='center'>" + item.din1 + "</td><td align='center'>" + item.din2 + "</td><td align='center'>" + item.din3 + "</td></tr>";
                            } else {
                                sercehialb1.push({
                                    'ser_id': item.ser_id,
                                    'ser_codigo': item.ser_codigo,
                                    'pro_nombre': item.pro_nombre,
                                    'proid': item.pro_id,
                                    'familia': item.familia
                                });

                                if (item.ser_codigo == '') {
                                    var sercod = 0;
                                } else {
                                    var sercod = item.ser_codigo;
                                }

                                if (item.pro_nombre == '' || item.pro_nombre == null) {
                                    item.pro_nombre = '-';
                                }

                                var btnchvivio = "";
                                if (item.familia == 'GPS') {
                                    btnchvivio = "<button type='button' class='btn btn-sm btn-success btn-circle' style='color: white;' onclick='checkedvivo(" + item.ser_codigo + "," + item.pro_id + ")'><i class='fas fa-server'></i></button>";
                                }

                                fpxt += "<tr id='fila_id_" + ipxt + "' class='" + trcolor + "'><td class='text-center'>1</td>" + btnchvivio + "<td></td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + sercod + "\"," + item.pro_id + "," + item.ser_id + ")'>" + item.pro_nombre + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + sercod + "\"," + item.pro_id + "," + item.ser_id + ")'>SI</td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + sercod + "\"," + item.pro_id + "," + item.ser_id + ")'>" + item.ser_codigo + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + sercod + "\"," + item.pro_id + "," + item.ser_id + ")'></td><td onclick='pasarProducto(\"" + ipxt + "\",1,\"" + sercod + "\"," + item.pro_id + "," + item.ser_id + ")'>Producto</td>" + estado + "</tr>";
                            }

                        });
                        if (tabla == 1) {
                            $("#tbpxv tbody").html(fpxt);
                        } else {
                            $("#tbpxt tbody").html(fpxt);
                        }
                    }
                } else {
                    if (tabla == 1) {
                        $("#tbpxv tbody").html('<tr><td colspan="9" align="center">No hay datos</td></tr>');
                    } else {
                        $("#tbpxt tbody").html('<tr><td colspan="7" align="center">No hay datos</td></tr>');
                    }
                }

            }

        });
    }

    function instalarPro(index) {
        pxt = ProxTEC[index];
        idpxt = pxt["idpxt"];
        idtecnico = $("#idtecnico").val();
        idpatente = $("#idpatente").val();
        if (pxt["tieneserie"] == "NO") {
            nserie = "";
            cantidad = parseInt($("#cantidad").val());
            if (cantidad > parseInt(pxt["cantidad"])) {
                alert("La cantidad a instalar no puede ser mayor a la cantidad disponible en bodega tecnico");
                return;
            }
        } else {
            nserie = $("#nserie").val();
            cantidad = 1;
        }

        // console.log(pxt);
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
            tipo: pxt['tipo'],
            ideasi: pxt["ideasi"],
            retornar: 'no'
        }, function(data) {
            // console.log(data);
            datos = $.parseJSON(data);
            ProxVEH = datos["pxv"];
            ProxTEC = datos["pxt"];
            fpxv = "";
            $('#mticket').modal('hide');
            $.each(ProxVEH, function(ipxv, vpxv) {
                let tipo = 'Kit GPS';
                let nametipo = vpxv.ideasi;
                let seriesim;
                if (vpxv.kitdetalle.length > 0) {
                    seriesim = vpxv.kitdetalle[0].seriesim;
                }

                if (parseInt(vpxv.tipo) == 1) {
                    tipo = 'Producto';
                    nametipo = vpxv.producto;
                    seriesim = '';
                    idproducto = vpxv.ideasi;
                }

                fpxv += "<tr id='fila_id2_" + ipxv + "' onclick='pasarProducto(\"" + ipxv + "\",2)'><td>" + vpxv.cantidad + "</td><td>" + nametipo + "</td><td>" + vpxv.tieneserie + "</td><td>" + vpxv.serie + "</td><td>" + seriesim + "</td><td>" + tipo + "</td><td style='display:none;' class='text-center'><span class='text-red pointer' onclick='quitarProducto(\"" + ipxv + "\")'><i class='fa fa-arrow-right' aria-hidden='true'></i></span></td></tr>";
            });
            $("#tbpxv tbody").html(fpxv);

            // actualizar listado de productos por tecnico
            fpxt = "";
            $.each(ProxTEC, function(ipxt, vpxt) {
                let tipo = 'Kit GPS';
                let nametipo = vpxt.ideasi;
                let seriesim;
                if (vpxt.kitdetalle.length > 0) {
                    seriesim = vpxt.kitdetalle[0].seriesim;
                }

                if (parseInt(vpxt.tipo) == 1) {
                    tipo = 'Producto';
                    nametipo = vpxt.producto;
                    seriesim = '';
                }
                switch (vpxt.estado) {
                    case 'BUENO':
                        trcolor = "";
                        add = "<td class='text-center'><span class='text-green' style='cursor:pointer;' onclick='agregarProducto(\"" + ipxt + "\")'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td>";
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

                var btnchvivio = "";
                if (vpxt.familia == 'GPS') {
                    btnchvivio = "<button type='button' class='btn btn-sm btn-success btn-circle' style='color: white;' onclick='checkedvivo(" + vpxt.serie + ",0)'><i class='fas fa-server'></i></button>";
                }

                fpxt += "<tr id='fila_id_" + ipxt + "' class='" + trcolor + "'><td class='text-center'>" + vpxt.cantidad + "</td><td>" + btnchvivio + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1)'>" + nametipo + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1)'>" + vpxt.tieneserie + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1)'>" + vpxt.serie + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1)'>" + seriesim + "</td><td onclick='pasarProducto(\"" + ipxt + "\",1)'>" + tipo + "</td></tr>";
                //fpxt+="<tr><td class='text-center'><span class='text-green pointer' onclick='agregarProducto(\""+ipxt+"\")'><i class='fa fa-arrow-left' aria-hidden='true'></i></span></td><td>"+vpxt.cantidad+"</td><td>"+vpxt.producto+"</td><td>"+vpxt.tieneserie+"</td><td>"+vpxt.serie+"</td><td>"+vpxt.estado+"</td></tr>";	
            });
            $("#tbpxt tbody").html(fpxt);
        });
        $("#agregarproaveh").html("");
    }

    function noInstalarPro() {
        $('#tbpxt tbody tr').each(function(i) {
            $('#fila_id_' + i).css({
                'background-color': 'rgba(0,0,0,.05)',
                'color': 'black'
            });
        });
        $('#tbpxv tbody tr').each(function(i) {
            $('#fila_id_' + i).css({
                'background-color': 'rgba(0,0,0,.05)',
                'color': 'black'
            });
        });
        proSelect = null;
        indSelect = null;
        proSelect2 = null;
        indSelect2 = null;

        $("#agregarproaveh").html("");
        $('#mticket .modal-body').html("");
    }


    function FinalizarTicket(id, tipotrabajo = 0) {
        //fcerrarticket,finalizarTicket
        //det_dispositivo
        var dataArray = [];

        // console.log(tipotrabajo)
        /*    tiposervicio = 0;*/
        $("#det_ttrabajo").val(tipotrabajo)
        $('#tbpxv tbody tr').each(function(row, tr) {
            var rowArray = [];
            $(tr).find('td:visible').each(function(col, td) {
                rowArray.push($(td).text());
            });
            dataArray.push(rowArray);
        });

        var tipo_trab = $('#tipo_trab').text().toLowerCase();
        if (tipo_trab == 'soporte') {
            if (dataArray.length == 0) {
                toastr.info('Si es soporte el vehiculo debe tener al menos una imei instalada');
                return;
            }
        } else if (tipo_trab == 'instalación') {
            if (dataArray.length == 0) {
                toastr.info('Si es instalación el vehiculo debe tener al menos una imei instalada');
                return;
            }
        } else if (tipo_trab == 'desinstalación') {
            if (dataArray.length > 0) {
                if (dataArray[0][0] != 'No hay datos') {
                    toastr.info('Si es desinstalación el vehiculo no debe tener una imei instalada');
                    return;
                }
            }
        }

        var valida = 1;
        if (tipotrabajo == 2) {
            $('#btncerrart').hide();
            if (sercehialb.length == 0) {
                valida = 0;
            } else {
                valida = 1;
            }
        } else {
            valida = 1;
            $('#btnfinmigrar').hide();
        }

        if (valida == 1) {
            $('#fcerrarticket').hide();
            $('#finalizarTicket').show();
            $('#btnvolverfromfin').show();
            $('#btnfinmigrar').attr('onclick', 'finTicketyMigrar(' + id + ',0,' + tipotrabajo + ')')
            $('#btncerrart').attr('onclick', 'finTicketyMigrar(' + id + ',1,' + tipotrabajo + ')')
            $('#btnupdateveh').attr('onclick', 'updateVehiculo(' + id + ')')
            EditarVehiculo(id, tipotrabajo);
        } else {
            toastr.error('Debes tener al menos una serie para finalizar el ticket');
        }
    }

    function cancelarFinalizarTicket() {
        $('#fcerrarticket').show();
        $('#finalizarTicket').hide();
        $('#btnvolverfromfin').hide();
        $('#btnfinmigrar').attr('onclick', '')
    }

    function finTicketyMigrar(id, opc = 0, tipotrabajo = 0) {
        let doc = $('input[name="archivosadjuntos"]').prop('files');
        descripcion = $("#fcerrarticket textarea[name='observacionfin']").val();
        var form_data = new FormData();
        form_data.append('operacion', 'cerrarTicket');
        $.each(doc, function(i, img) {
            form_data.append('imglist' + (i + 1), img);
        });
        form_data.append('cantidad', doc.length);
        form_data.append('tic_id', id);
        form_data.append('tic_desccierre', descripcion);
        form_data.append('retornar', 'no');
        form_data.append('opc', opc);
        form_data.append('tipotrabajo', tipotrabajo);
        $('#btnfinmigrar').attr('disabled', true);
        $('#btnfinmigrar').text('Cargando...');

        form_data.append('tservicio', parseInt($("#det_tservicio").val()));
        // form_data.append('taccesorios', parseInt($("#det_accesorios").val()));
        form_data.append('taccesorios', JSON.stringify($("#det_accesorios").val()));



        $.ajax({
            url: 'operaciones.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                xhr.onprogress = function e() {
                    if (e.lengthComputable) {}
                };
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        porcentaje = parseInt((e.loaded / e.total) * 100);
                    }
                };
                return xhr;
            },
            success: function(data) {
                respuesta = $.parseJSON(data);

                // console.log("SQL: " + respuesta.sql);
                // console.log("LOGO: " + respuesta.logo);
                // console.log("MENSAJE: " + respuesta.mensaje);
                if (respuesta.logo == 'success') {
                    //getPDFOT(id);
                    toastr.success(respuesta.mensaje);
                    // console.log( location.reload() );
                    location.reload();
                    // Redirige a la URL específica
                    // window.location.href = "https://www.ds-tms.com/cloux/index.php?menu=tickets&idmenu=100";

                } else {
                    toastr.error(respuesta.mensaje)
                }
                $('#btnfinmigrar').attr('disabled', false);
                $('#btnfinmigrar').text('Finalizar y Migrar');
            },
            error: function(error) {
                console.log('error', error);
                toastr.error('Error al finalizar ticket.')
                $('#btnfinmigrar').attr('disabled', false);
                $('#btnfinmigrar').text('Finalizar y Migrar');
            }
        });
    }

    let productosXVeh = [];

    function EditarVehiculo(idticket = 0, tipotrabajo = 0) {
        let vehiculo = vehiculos[0];
        idcli = vehiculo["idcliente"];
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getGruposCliente',
            id: '' + idcli + '',
            retornar: 'no'
        }, function(data) {
            //console.log(data);
            datos = $.parseJSON(data);
            sgrup = "<option value=0>SELECCIONAR</option>";
            $.each(datos, function(index, valor) {
                sgrup += "<option value=" + valor.id + ">" + valor.nombre + "</option>";
            });
            $("#det_grupo").html(sgrup);
            $("#det_grupo").val(vehiculo["idgrupo"]);
            $("input[name='det_idveh']").val(vehiculo["idveh"]);
            $("#det_gps").val(vehiculo["idgps"]);
            $("#det_tipo").val(vehiculo["idtipo"]);
            $("#det_cliente").val(vehiculo["idcliente"]);
            $("input[name='det_patente']").val(vehiculo["patente"]);
            $("#det_dispositivo").val(vehiculo["dispositivo"]);
            if (vehiculo["tic_tiposervicio"] != '0' && vehiculo["tic_tiposervicio"] != '') {
                $("#det_tservicio").val(vehiculo["tic_tiposervicio"]);
            } else {
                $("#det_tservicio").val((vehiculo["tservicio"] == 1 ? 2 : (vehiculo["tservicio"] == 3 ? 3 : 1)));
            }

            if (tiposervicio != 0) {
                $("#det_tservicio").val(tiposervicio);
            }
            $("#det_ttrabajo").val(tipotrabajo);
            $("#det_region").val(vehiculo["region"]);
            $("#det_comuna").val(vehiculo["comuna"]);
            $("#det_contacto").val(vehiculo["contacto"]);
            $("#det_celular").val(vehiculo["celular"]);

            /*if(idticket!='0' && idticket!=''){
                var idtd = 0;
                let tic = tickets.filter(tic => tic.id == idticket)
                if (tic.length > 0) {
                    idtd = tic[0].iddispositivo;     
                }
                $("#det_dispositivo").val(idtd);
            }*/


            $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getProductosxVehiculos',
                id: vehiculo["idveh"],
                idticket: idticket,
                retornar: 'no'
            }, function(proxveh) {
                if (proxveh != '' && proxveh != null) {
                    var sim = (proxveh.seriesim == undefined ? '-' : proxveh.seriesim);
                    proxveh = $.parseJSON(proxveh);
                    if (proxveh.prosveh.length > 0) {
                        // console.log('proxveh', proxveh.prosveh);
                        let fila = '';
                        $.each(proxveh.prosveh, function(i, item) {
                            let tipo = 'Kit GPS';
                            let ntipo = item.ideasi;
                            let cant = '1';
                            let serie = (item.kitdetalle.length > 0 ? item.kitdetalle[0].seriegps : '') ? (item.kitdetalle.length > 0 ? item.kitdetalle[0].seriegps : '') : item.serie;
                            let serie1 = (item.kitdetalle.length > 0 ? item.kitdetalle[0].seriesim : '');
                            if (parseInt(item.tipo) == 1) {
                                tipo = 'Producto';
                                ntipo = item.producto;
                                cant = item.cantidad;
                                serie = item.serie;
                                serie1 = '';
                                idproducto = item.ideasi;
                            }
                            if (item.din1 == null) {
                                item.din1 = '-';
                            }
                            if (item.din2 == null) {
                                item.din2 = '-';
                            }
                            if (item.din3 == null) {
                                item.din3 = '-';
                            }

                            // $("#det_dispositivo").val(item.ideasi);
                            // det_cliente

                            $("#det_dispositivo").val(item.proid);

                            fila += '<tr><td>' + (i + 1) + '</td><td>' + cant + '</td><td>' + item.producto + '</td><td>' + serie + '</td><td>' + sim + '</td><td>' + item.familia + '</td><td align="center">' + item.din1 + '</td><td align="center">' + item.din2 + '</td><td align="center">' + item.din3 + '</td></tr>';
                        });
                        $('#tbllistproxveh tbody').html(fila);
                    } else {
                        $("#det_dispositivo").val(vehiculo["idproser"]);
                    }
                    if(proxveh.accesorios.length > 0){
                        let fila = '';
                        $.each(proxveh.accesorios, function(i, item) {
                            fila += '<tr><td>' + (i + 1) + '</td><td>1</td><td>' + item + '</td><td></td><td></td><td>Accesorio</td><td align="center"></td><td align="center"></td><td align="center"></td></tr>';
                        });
                        $('#tbllistproxveh tbody').append(fila);

                    }
                }
            });
            $('#loading1').hide();
            $('#loading2').hide();
        });
    }

    function updateVehiculo(idticket = 0) {
        let vehiculo = vehiculos[0];
        let tipo = ($('#det_tipo').val() == null || $('#det_tipo').val() == undefined ? 0 : $('#det_tipo').val());
        let region = ($('#det_region').val() == null || $('#det_region').val() == undefined ? 0 : $('#det_region').val());
        let comuna = ($('#det_comuna').val() == null || $('#det_comuna').val() == undefined ? 0 : $('#det_comuna').val());
        let gps = ($('#det_gps').val() == undefined ? 0 : $('#det_gps').val());
        let cliente = ($('#det_cliente').val() == null || $('#det_cliente').val() == undefined ? 0 : $('#det_cliente').val());
        let grupo = ($('#det_grupo').val() == null || $('#det_grupo').val() == undefined ? 0 : $('#det_grupo').val());
        let patente = $('#det_patente').val();
        let contacto = $('#det_contacto').val();
        let celular = $('#det_celular').val();
        let dispositivo = ($('#det_dispositivo').val() == null || $('#det_dispositivo').val() == undefined ? 0 : $('#det_dispositivo').val());
        let tservicio = ($('#det_tservicio').val() == null || $('#det_tservicio').val() == undefined ? 0 : $('#det_tservicio').val());
        let idveh = vehiculo["idveh"];
        let ttrabajo = $('#det_ttrabajo').val();
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'editarvehiculo',
            tipo: tipo,
            region: region,
            comuna: comuna,
            gps: gps,
            cliente: cliente,
            grupo: grupo,
            patente: patente,
            contacto: contacto,
            celular: celular,
            dispositivo: dispositivo,
            tservicio: tservicio,
            idveh: idveh,
            tickalb: 1,
            idticket: idticket,
            ttrabajo: ttrabajo,
            retornar: 'no'
        }, function(data) {
            if (data != '' && data != null) {
                data = $.parseJSON(data);
                if (data.status == 'OK') {
                    toastr.success('Exito al actualizar datos de vehículo.');
                    if (ttrabajo == 2) {
                        $('#btncerrart').hide();
                        $('#btnfinmigrar').show();
                    } else {
                        $('#btnfinmigrar').hide();
                        $('#btncerrart').show();
                    }

                    $('#btnfinmigrar').attr('onclick', 'finTicketyMigrar(' + idticket + ',0,' + ttrabajo + ')')
                    $('#btncerrart').attr('onclick', 'finTicketyMigrar(' + idticket + ',1,' + ttrabajo + ')')

                } else {
                    toastr.error('Error al actualizar datos de vehículo.')
                }
            } else {
                toastr.error('Error al actualizar datos de vehículo.')
            }
        });
    }

    function getComunas() {
        idregion = $("#det_region option:selected").val();
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getComunas',
            region: '' + idregion + '',
            retornar: 'no'
        }, function(data) {
            // console.log(data);
            $("#det_comuna").html(data);
        });
    }

    window.patentes;

    function getVehCli() {
        $("#tipoveh").val("");
        idcliente = parseInt($("#cliente").val());
        //console.log(parseInt(idcliente));
        if (isNaN(idcliente)) {
            alert("El cliente seleccionado no es válido");
            $("#cliente").focus();
            $('#marca').val('');
            $('#modelo').val('');
            return;
        } else {
            $('#marca').val('');
            $('#modelo').val('');
            let cliente_cuenta = $("#cliente option:selected").text();
            $.get("operaciones.php", {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getVehCli',
                veh_cliente: cliente_cuenta,
                retornar: 'no'
            }, function(data) {
                datos = $.parseJSON(data);
                patentes = datos;
                selectvehiculos = "<option value=0>SELECCIONAR</option>";
                $.each(datos, function(index, valor) {
                    selectvehiculos += "<option value=" + valor.idveh + " id=" + index + ">" + valor.patente + "</option>";
                });
                $('#patente').chosen('destroy');
                $('#patente').html(selectvehiculos);
                $('#patente').chosen();
                /*  $("#patente").html(selectvehiculos);
                  $('#patente').chosen();*/
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
        idtipo = patentes[index]["tipo"];
        /*alert(idtipo);
        if(idtipo==0)
        {
            tipo="--";
        }
        else
        {
            tipo=idtipo;
        }*/

        $("#tipoveh").val(idtipo);
        $('#tipodtrab').val(patentes[index]["tservicio"]);
        $('#dispositivo').val(patentes[index]["dispositivo"]);
        $('input[name="contacto"]').val(patentes[index]["contacto"]);
        $('input[name="celular"]').val(patentes[index]["celular"]);

        let vehid = $('#patente').val();
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getRsocialxPatente',
            vehid: vehid,
            retornar: 'no'
        }, function(data) {
            data = $.parseJSON(data);
            if (data.status == 'OK') {
                $('#rsocial').val(data.id);
                $('#marca').val(data.marca);
                $('#modelo').val(data.modelo);
                $('#marca').attr('disabled', true);
                $('#modelo').attr('disabled', true);
                $('#tipodserv').val((data.tiposervicio == 1 ? 2 : 1)); //.attr('disabled', true);
            }
        });
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
        $("#mticket .modal-title").html("Anular Ticket");
        $("#mticket .modal-body").html("¿Realmente desea anular este ticket? : <br><table class='table table-bordered table-striped'><tr><td>Cliente</td><td>" + ticket.cliente + "</td></tr><tr><td>Patente</td><td>" + ticket.patente + "</td></tr><tr><td>Tipo Trabajo</td><td>" + ticket.tipotrabajo + "</td></tr></table>");
        $("#mticket .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarTIC(\"" + ticket.id + "\");' class='btn btn-success btn-rounded'>Anular</button>");
        $("#mticket").modal("toggle");
    }

    function eliminarTIC(id) {
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'eliminarTicket',
            tic_id: '' + id + '',
            retornar: 'no'
        }, function(data) {
            //$("#ftick"+id+"").remove();
            $("#mticket").modal("hide");
            resetPage()
        });

    }

    function ValidarCampos() {
        return true;
        /*if($("input[name='contacto']").val() === '' && $("input[name='celular']").val() === ''){
            $("input[name='contacto']").css({'border':'1px solid red'});
            $('#msg_contacto').show();
            $("input[name='celular']").css({'border':'1px solid red'});
            $('#msg_celular').show();
            return false;
        }

        if($("input[name='contacto']").val() === ''){
            $("input[name='contacto']").css({'border':'1px solid red'});
            $('#msg_contacto').show();
            return false;
        }else{
            $("input[name='contacto']").css({'border':'1px solid #ccc'});
            $('#msg_contacto').hide();
        }

        if($("input[name='celular']").val() === ''){
            $("input[name='celular']").css({'border':'1px solid red'});
            $('#msg_celular').show();
            return false;
        }else{
            $("input[name='celular']").css({'border':'1px solid #ccc'});
            $('#msg_celular').hide();
        }*/
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

    function verResumen(opc) {
        if (opc === 1) {
            $('#resumenticket').show('slow');
            $('#fnuevoticket').hide('slow');
            $('#listadodetickets').hide('slow');
            $('#btn_resumenticket').attr('onclick', 'verResumen(' + 2 + ')').html('<i class="fa fa-arrow-left"> Volver</i>');
            getTicketResumen();
        } else {
            $('#resumenticket').hide('slow');
            $('#listadodetickets').show('slow');
            $('#btn_resumenticket').attr('onclick', 'verResumen(' + 1 + ')').html('<i class="fa fa-cube"> Resumen</i>');
        }
    }

    function getTicketResumen() {
        $.get("operaciones.php", {
            numero: '' + Math.floor(Math.random() * 9999999) + '',
            operacion: 'getTicketResumen',
            retornar: 'no'
        }, function(data) {
            if (data !== '' && data !== null) {
                data = $.parseJSON(data);
                if (data.html !== '') {
                    //console.log(data);
                    $('#tbticketsresumen tbody').html(data.html);
                } else {
                    $('#tbticketsresumen tbody').html('<tr><td align="center" colspan="8" style="padding:4px">No exiten registros para mostrar.</td></tr>');
                }
            } else {
                $('#tbticketsresumen tbody').html('<tr><td align="center" colspan="8" style="padding:4px">No exiten registros para mostrar.</td></tr>');
            }
        });
    }

    $('#btn_reportticket').click(function() {
        // let desde=$("#fdesde").val();
        // let hasta=$("#fhasta").val();
        // let idtra=$("#transportista").val();
        // let filtro="";
        // let tipo = tipofil;
        //let url = "reports/download-listhistoricosexcel.php?desde="+desde+"&hasta="+hasta+"&idtra="+idtra+"&optfiltro="+tipo;
        let url = "reports/reporte-historico-ticket.php";
        let a = document.createElement('a');
        a.target = "_blank";
        a.href = url;
        a.click();
    });

    $('#veropertarivos').click(function() {
        $('#tbl1').hide()
        $('#tbl2').show()
    });

    function vovler() {
        $('#tbl1').show()
        $('#tbl2').hide()
    }

    function addImagenes() {
        $('#archivosadjuntos').trigger('click');
    }

    $("body").on('change', 'input[name="archivosadjuntos"]', function() {
        let doc = $(this).prop('files');
        if (typeof doc !== "undefined") {
            // console.log(doc);
            let extValida = 0;
            $.each(doc, function(i, file) {
                var extension = file.name.replace(/^.*\./, '');
                if (extension == file.name) {
                    extension = '';
                } else {
                    extension = extension.toLowerCase();
                    if ((extension == 'jpg') || (extension == 'png') || (extension == 'jpeg')) {
                        extValida++;
                    }
                }
            });
            $('#contImg').text(extValida + ' Imágenes');
        } else {}
    });


    function getPlantilla() {
        env = {
            'opc': 1
        };
        var send = JSON.stringify(env);
        $.ajax({
            url: 'operaciones.php',
            data: {
                numero: '' + Math.floor(Math.random() * 9999999) + '',
                operacion: 'getPlantillaTickets',
                retornar: 'no',
                envio: send
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {},
            error: function(respuesta) {},
            success: function(respuesta) {
                // console.log(respuesta);
                var $a = $("<a>");
                $a.attr("href", respuesta.file);
                $("body").append($a);
                $a.attr("download", "Plantilla.xlsx");
                $a[0].click();
                $a.remove();
            }
        });

    }

    /*function getPlantilla(){
    	let url  = "operaciones.php?operacion=getPlantillaTickets";
        let a    = document.createElement('a');
        a.target = "_blank";
        a.href   = url;
        a.click();	
    }*/

    $('#btnsubirtickets').on('click', function() {
        $('#ticketsmasivos').trigger('click');
    });

    $("input[name='ticketsmasivos']").change(function(e) {
        doc = $(this).prop('files')[0];
        if (typeof doc !== "undefined") {
            CargaMasiva(doc);
            this.value = null;
        } else {

        }
    });

    function CargaMasiva(documento) {
        var file_data = documento;
        usuario = $("#useridjs").val();
        var form_data = new FormData();
        form_data.append('operacion', 'postCargaTickets');
        form_data.append('usuario', usuario);
        form_data.append('archivo', file_data);
        form_data.append('retornar', 'no');
        $("#cargandoplan").show();
        $.ajax({
            url: 'operaciones.php', //ruta archivo operaciones
            dataType: 'text', // tipo de datos
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                xhr.onprogress = function e() {
                    // For downloads
                    if (e.lengthComputable) {
                        //console.log(e.loaded / e.total);
                    }
                };
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        porcentaje = parseInt((e.loaded / e.total) * 100);
                        $("#cargandoplan .progress-bar").css({
                            width: "" + porcentaje + "%"
                        });
                        $("#cargandoplan .progress-bar").html(porcentaje + "%");
                        $("#cargandoplan .sr-only").html(porcentaje + "%");
                    }
                };
                return xhr;
            },
            success: function(respuesta) {
                //console.log($.parseJSON(respuesta));
                $("#cargandoplan").hide();
                // $("#divImportarViajes").hide();
                /*location.reload();*/
                // procesarPlanificacion(respuesta);
            }
        });
    }
</script>