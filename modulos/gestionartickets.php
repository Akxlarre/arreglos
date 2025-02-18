<?php
session_start();
$Date = date("Y"); 
if(empty($_SESSION["cloux_new"])){
  include("../login.php");
}else{
  include("../conexion.php");
  include("../funciones.php");
  $id=$_SESSION["cloux_new"];
  $misdatos=usuariologeado($id);

$link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
$sql = "SELECT * FROM usuarios WHERE usu_id='{$_SESSION['cloux_new']}'";
$res = $link->query($sql);
$usua = [];
if(mysqli_num_rows($res)){
    foreach($res as $key => $data){
       $usua []= array(
        "usu_nom" =>$data["usu_nombre"],
       );
    }
}
$usr = mysqli_fetch_array($res);

$link1 = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', 'cloux');
$sql1 = "SELECT * FROM usuarios";
$res1 = $link1->query($sql1);
$optionsUsr = '';

if (mysqli_num_rows($res1)) {
    foreach ($res1 as $key => $data1) {
        $usua1 = $data1["usu_nombre"];
        $optionsUsr .= '<option value="' . $usua1 . '">' . $usua1 . '</option>';
    }
}
$linkGen = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD');
if (mysqli_connect_errno()) {
	printf("Falló la conexión: %s\n", mysqli_connect_error());
	exit();
}

$empresas = array();
$rs = $linkGen->query('SHOW DATABASES;');
$indim = 0;

while ($row = mysqli_fetch_array($rs)) {
    if ($row[0] != '' && $row[0] != null) {
        if (trim($row[0]) != 'cloux' && trim($row[0]) != 'mysql' && trim($row[0]) != 'information_schema' && trim($row[0]) != 'performance_schema') {
            $empresas[] = $row[0];
            if ($indim == 0) {
                $optionsclientes .= '<option value="TODOS">TODOS</option>';
            }
            $optionsclientes .= '<option value="' . $empresas[count($empresas) - 1] . '">' . $empresas[count($empresas) - 1] . '</option>';
            $indim++;
        }
        if (trim($row[0]) != 'mysql' && trim($row[0]) != 'information_schema' && trim($row[0]) != 'performance_schema') {
            $empresas[] = $row[0];
            $optionsclientesform .= '<option value="' . $empresas[count($empresas) - 1] . '">' . $empresas[count($empresas) - 1] . '</option>';
            $indim++;
        }
    }
}
  
 /* $sql = "select * from usuarios usu left outer join tipo_usuario tusu on tusu.tusu_id=usu.usu_perfil where usu.usu_id={$_SESSION["cloux"]}";
  $res = $link->query($sql);
  $dusuario = mysqli_fetch_array($res);

  $sql5 = "SELECT * FROM clientes GROUP BY cuenta";
    $res5 = $link->query($sql5);
    $optionsclientes = '';
    $indim = 0;
    foreach($res5 as $key5){
        if($indim==0){
            $optionsclientes .= '<option value="TODOS">TODOS</option><option value="'.$key5['cuenta'].'">'.$key5['cuenta'].'</option>';
        }
        $optionsclientes .= '<option value="'.$key5['cuenta'].'">'.$key5['cuenta'].'</option>';
        $indim++;
    }*/

$_SESSION["colorprin"] = '#7058c3';
$_SESSION["colorver"]  = '#82d2bf';
$_SESSION["colorroj"]  = '#de5053';
$_SESSION["colorama"]  = '#efcc44';
$_SESSION["colorazu"]  = '#5f6a9f';
$_SESSION["colornar"]  = '#F5C175';
$_SESSION["colorplomo"]  = '#C0C0C0';
$_SESSION["colorrojcla"]  = '#FF9999';
//$nmodulo = str_replace('.php','',basename($_SERVER['PHP_SELF']));
//$sql1 = "SELECT pxu.idmodulo FROM modulos modu LEFT OUTER JOIN permisosusuarios pxu ON pxu.idmodulo=modu.id WHERE replace(modu.nombre,' ','')='{$nmodulo}'";
//$res1 = $link->query($sql1);
//$idmod = mysqli_fetch_array($res1);
//$sql = "SELECT * FROM usuarios WHERE usu_token='{$_SESSION['tk']}' AND usu_id={$_SESSION['cloux']}";
//$res = $link->query($sql);
//$usr = mysqli_fetch_array($res);
//if($usr['usu_id']!=null && $idmod['idmodulo']!=null){
?>
<!-- Google Font: Source Sans Pro -->
  
  <!-- jQuery -->


<input type="hidden" name="" id="perf" value="<?php echo $usr['usu_perfil']?>">
<input type="hidden" name="" id="colorhome" value="<?php echo $_SESSION["colorprin"] ?>">
<input type="hidden" name="" id="colorver" value="<?php echo $_SESSION["colorver"] ?>">
<input type="hidden" name="" id="colorroj" value="<?php echo $_SESSION["colorroj"] ?>">
<input type="hidden" name="" id="colorazu" value="<?php echo $_SESSION["colorazu"] ?>">
<input type="hidden" name="" id="colorama" value="<?php echo $_SESSION["colorama"] ?>">
<style>
    .oculto{
        display: none;
    }
    .table tbody tr.highlight td {
        background-color: #ddd;
    }

    .alert-success{
        background-color: <?php echo $_SESSION['colorver']?>;
        border : <?php echo $_SESSION['colorver']?>;
    }

    .alert-error{
        background-color: <?php echo $_SESSION['colorroj']?>;
        border : <?php echo $_SESSION['colorroj']?>;
    }

    .table tbody tr.highlight td {
     background-color: #ddd;
    }
    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: <?php echo $_SESSION['colorprin']?>;
        border-color: <?php echo $_SESSION['colorprin']?>;
    }
    .modal {
       display: none;
       position: fixed;
       top: 0;
       left: 0;
       width: 100%;
       height: 100%;
       background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-content {
       background-color: #fefefe;
       margin: 10% auto; /* Incrementamos el margen superior para que el modal sea más largo */
       padding: 20px;
       border: 1px solid #888;
       width: 80%;
    }
    .pointer{
        cursor:pointer;
    }

    .chatbox-holder {

align-items: flex-end;
height: 0;
}

.chatbox {
width: 100%;
height: 300px;
margin: 0 20px 0 0;
position: relative;
box-shadow: 0 0 5px 0 rgba(0, 0, 0, .2);
display: flex;
flex-flow: column;
border-radius: 10px 10px 0 0;
background: white;
bottom: 0;
transition: .1s ease-out;
}

.chatbox-top {
position: relative;
display: flex;
padding: 10px 0;
border-radius: 10px 10px 0 0;
background: rgba(0, 0, 0, .05);
}

.chatbox-icons {
padding: 0 10px 0 0;
display: flex;
position: relative;
}

.chatbox-icons .fa {
background: rgba(220, 0, 0, .6);
padding: 3px 5px;
margin: 0 0 0 3px;
color: white;
border-radius: 0 5px 0 5px;
transition: 0.3s;
}

.chatbox-icons .fa:hover {
border-radius: 5px 0 5px 0;
background: rgba(220, 0, 0, 1);
}

.chatbox-icons a, .chatbox-icons a:link, .chatbox-icons a:visited {
color: white;
}

.chat-partner-name, .chat-group-name {
flex: 1;
padding: 0 0 0 95px;
font-size: 15px;
font-weight: bold;
color: #30649c;
text-shadow: 1px 1px 0 white;
transition: .1s ease-out;
}

.status {
width: 12px;
height: 12px;
border-radius: 50%;
display: inline-block;
box-shadow: inset 0 0 3px 0 rgba(0, 0, 0, 0.2);
border: 1px solid rgba(0, 0, 0, 0.15);
background: #cacaca;
margin: 0 3px 0 0;
}

.online {
background: #b7fb00;
}

.away {
background: #ffae00;
}

.donot-disturb {
background: #ff4343;
}

.chatbox-avatar {
width: 80px;
height: 80px;
overflow: hidden;
border-radius: 50%;
background: white;
padding: 3px;
box-shadow: 0 0 5px 0 rgba(0, 0, 0, .2);
position: absolute;
transition: .1s ease-out;
bottom: 0;
left: 6px;
}

.chatbox-avatar img {
width: 100%;
height: 100%;
border-radius: 50%;
}

.chat-messages {
border-top: 1px solid rgba(0, 0, 0, .05);
padding: 10px;
width: 100%;
overflow: auto;
display: flex;
flex-flow: row wrap;
align-content: flex-start;
flex: 1;
align-items: flex-end;
}

.message-box-holder {
width: 100%;
margin: 0 0 15px;
display: flex;
flex-flow: column;
align-items: flex-end;
}

.message-sender {
font-size: 12px;
margin: 0 0 15px;
color: #30649c;
align-self: flex-start;
}
.message-sendersop {
font-size: 12px;
margin: 0 0 15px;
color: #30649c;
align-self: flex-end;
}

.message-sender a, .message-sender a:link, .message-sender a:visited, .chat-partner-name a, .chat-partner-name a:link, .chat-partner-name a:visited {
color: #30649c;
text-decoration: none;
}

.message-box {
padding: 6px 10px;
border-radius: 6px 0 6px 0;
position: relative;
background: rgba(100, 170, 0, .1);
border: 2px solid rgba(100, 170, 0, .1);
color: #6c6c6c;
font-size: 12px;
}

.message-box:after {
content: "";
position: absolute;
border: 10px solid transparent;
border-top: 10px solid rgba(100, 170, 0, .2);
border-right: none;
bottom: -22px;
right: 10px;
}

.message-partner {
background: rgba(0, 114, 135, .1);
border: 2px solid rgba(0, 114, 135, .1);
align-self: flex-start;
}

.message-partner:after {
right: auto;
bottom: auto;
top: -22px;
left: 9px;
border: 10px solid transparent;
border-bottom: 10px solid rgba(0, 114, 135, .2);
border-left: none;
}

.chat-input-holder {
display: flex;
border-top: 1px solid rgba(0, 0, 0, .1);
}

.chat-input {
resize: none;
padding: 5px 10px;
height: 40px;
font-family: 'Lato', sans-serif;
font-size: 14px;
color: #999999;
flex: 1;
border: none;
background: rgba(0, 0, 0, .05);
 border-bottom: 1px solid rgba(0, 0, 0, .05);
}

.chat-input:focus, .message-send:focus {
outline: none;
}

.message-send::-moz-focus-inner {
border:0;
padding:0;
}

.message-send {
-webkit-appearance: none;
background: #9cc900;
background: -moz-linear-gradient(180deg, #00d8ff, #00b5d6);
background: -webkit-linear-gradient(180deg, #00d8ff, #00b5d6);
background: -o-linear-gradient(180deg, #00d8ff, #00b5d6);
background: -ms-linear-gradient(180deg, #00d8ff, #00b5d6);
background: linear-gradient(180deg, #00d8ff, #00b5d6);
color: white;
font-size: 12px;
padding: 0 15px;
border: none;
text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.3);
}

.attachment-panel {
padding: 3px 10px;
text-align: right;
}

.attachment-panel a, .attachment-panel a:link, .attachment-panel a:visited {
margin: 0 0 0 7px;
text-decoration: none;
color: rgba(0, 0, 0, 0.5);
}

.chatbox-min {
margin-bottom: -362px;
/*   height: 46px; */
}

.chatbox-min .chatbox-avatar {
width: 60px;
height: 60px;
}

.chatbox-min .chat-partner-name, .chatbox-min .chat-group-name {
padding: 0 0 0 75px;
}

.settings-popup {
background: white;
border-radius: 20px/10px;
box-shadow: 0 3px 5px 0 rgba(0, 0, 0, .2);
font-size: 13px;
opacity: 0;
padding: 10px 0;
position: absolute;
right: 0;
text-align: left;
top: 33px;
transition: .15s;
transform: scale(1, 0);
transform-origin: 50% 0;
width: 120px;
z-index: 2;
border-top: 1px solid rgba(0, 0, 0, .2);
border-bottom: 2px solid rgba(0, 0, 0, .3);
}

.settings-popup:after, .settings-popup:before {
border: 7px solid transparent;
border-bottom: 7px solid white;
border-top: none; 
content: "";
position: absolute;
left: 45px;
top: -10px;
border-top: 3px solid rgba(0, 0, 0, .2);
}

.settings-popup:before {
border-bottom: 7px solid rgba(0, 0, 0, .25);
top: -11px;
}

.settings-popup:after {
border-top-color: transparent;
}

#chkSettings {
display: none;
}

#chkSettings:checked + .settings-popup {
opacity: 1;
transform: scale(1, 1);
}

.settings-popup ul li a, .settings-popup ul li a:link, .settings-popup ul li a:visited {
color: #999;
text-decoration: none;
display: block;
padding: 5px 10px;
}

.settings-popup ul li a:hover {
background: rgba(0, 0, 0, .05);
}

    #tblTicket > thead tr:nth-child(1) th{background: <?php echo $_SESSION['colorprin']?>; position: sticky;top: 0;z-index: 20;}
    [class*=icheck-].icheck-primary>input:first-child:not(:checked):not(:disabled):hover+input[type=hidden]+label::before,.icheck-primary>input:first-child:not(:checked):not(:disabled):hover+label::before{border-color:<?php echo $_SESSION['colorver']?>}.icheck-primary>input:first-child:checked+input[type=hidden]+label::before,.icheck-primary>input:first-child:checked+label::before{background-color:<?php echo $_SESSION['colorver']?>;border-color:<?php echo $_SESSION['colorver']?>}.icheck-success>input:first-child:not(:checked):not(:disabled):hover+input[type=hidden]+label::before,.icheck-success>input:first-child:not(:checked):not(:disabled):hover+label::before{border-color:#4cae4c}.icheck-success>input:first-child:checked+input[type=hidden]+label::before,.icheck-success>input:first-child:checked+label::before{background-color:#5cb85c;border-color:#4cae4c}.icheck-info>input:first-child:not(:checked):not(:disabled):hover+input[type=hidden]+label::before,.icheck-info>input:first-child:not(:checked):not(:disabled):hover+label::before{border-color:#46b8da}.icheck-info>input:first-child:checked+input[type=hidden]+label::before,.icheck-info>input:first-child:checked+label::before{background-color:#5bc0de;border-color:#46b8da}.icheck-warning>input:first-child:not(:checked):not(:disabled):hover+input[type=hidden]+label::before,.icheck-warning>input:first-child:not(:checked):not(:disabled):hover+label::before{border-color:<?php echo $_SESSION['colorroj']?>}.icheck-warning>input:first-child:checked+input[type=hidden]+label::before,.icheck-warning>input:first-child:checked+label::before{background-color:<?php echo $_SESSION['colorroj']?>;border-color:<?php echo $_SESSION['colorroj']?>}
    
</style>
<input type="hidden" name="" id="perf" value="<?php echo $usr['usu_perfil']?>">
<input type="hidden" name="" id="colorhome" value="<?php echo $_SESSION["colorprin"] ?>">
<input type="hidden" name="" id="colorver" value="<?php echo $_SESSION["colorver"] ?>">
<input type="hidden" name="" id="colorroj" value="<?php echo $_SESSION["colorroj"] ?>">
<input type="hidden" name="" id="colorazu" value="<?php echo $_SESSION["colorazu"] ?>">
<input type="hidden" name="" id="colorama" value="<?php echo $_SESSION["colorama"] ?>">
<input type="hidden" name="" id="colornar" value="<?php echo $_SESSION["colornar"] ?>">
<input type="hidden" name="" id="colorplomo" value="<?php echo $_SESSION["colorplomo"] ?>">
<input type="hidden" name="" id="colorrojcla" value="<?php echo $_SESSION["colorrojcla"] ?>">

<div class="modal fade" id="mloading" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:0.5rem;">
            <div class="modal-body" style="background-color:<?php echo $_SESSION["colorprin"]; ?>;">
                <div class="d-flex justify-content-center align-items-center h-100" ><div class="spinner-border" style="width: 4rem; height: 4rem;vertical-align:middle;color:white;" role="status"><span class="sr-only">Cargando...</span></div></div>
                <div class="d-flex justify-content-center align-items-center h-100" style="color:white;font-size:17pt;font-weight:bold;">Recopilando Información...</div>
            </div>
        </div>
    </div>
</div>

<div class="content" style="margin: 0 10px;padding-top: 5px;">
    <div class="card ">
        <div class="card-header p-0" style="background-color:<?php echo $_SESSION['colorprin']?>;color:white;">
        <div class="row">
                
                <div class="col-md-2 pl-3">
                    <h3 class="card-title font-weight-bold" style="padding:3px 1px;">Tickets</h3>
                </div>
                
            </div>
        </div>
        <div class="row p-0">
               <div class="" style="width:20%; padding: 10px; cursor: pointer;" onclick="listarTickets(1)">
                  <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Total Vehículos">
                     <span class="info-box-icon bg-warning elevation-1" style="font-size:1.4rem;">
                        <i class="fa fa-phone" aria-hidden="true"></i>
                     </span>
                     <div class="info-box-content">
                          <span class="info-box-text" style="font-weight:bold;">
                              Enviado Soporte TI
                          </span>
                          <span class="info-box-number" style="font-weight:bold;" id="fltEnviado">
                              <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                          </span>
                     </div>
                  </div>
               </div>
               <div class="" style="width:20%; padding: 10px; cursor: pointer;" onclick="listarTickets(2)">
                  <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Operativos">
                            <span class="info-box-icon bg-orange elevation-1" style="font-size:1.4rem;background-color:#7100FF;color:white;">
                            <i style="font-size:24px" class="fa">&#xf085;</i>
                            </span>
                      <div class="info-box-content">
                            <span class="info-box-text" style="font-weight:bold;">
                                En Ejecucion
                            </span>
                            <span class="info-box-number" style="font-weight:bold;" id="fltEjecucion">
                                <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                            </span>
                      </div>
                   </div>
               </div>
               <div class="" style="width:20%; padding: 10px; cursor: pointer;" onclick="listarTickets(3)">
                   <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="En Taller">
                            <span class="info-box-icon bg-secondary elevation-1" style="font-size:1.4rem;">
                            <i style="font-size:24px" class="fa">&#xf252;</i>
                            </span>
                       <div class="info-box-content">
                            <span class="info-box-text" style="font-weight:bold;">
                                 Esperando Confirmacion
                            </span>
                            <span class="info-box-number" style="font-weight:bold;" id="ftlConfirmacion">
                                <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                            </span>
                       </div>
                    </div>
               </div>
               <div class="" style="width:20%; padding: 10px; cursor: pointer;" onclick="listarTickets(4)">
                    <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Programados">
                            <span class="info-box-icon bg-success elevation-1" style="font-size:1.4rem;">
                              <i style="font-size:24px" class="fa">&#xf058;</i>
                            </span>
                         <div class="info-box-content">
                             <span class="info-box-text" style="font-weight:bold;">
                                 Finalizado
                             </span>
                             <span class="info-box-number" style="font-weight:bold;" id="fltFinalizado" >
                                 <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                             </span>
                          </div>
                    </div>
               </div>
               <div class="" style="width:20%; padding: 10px; cursor: pointer;" onclick="listarTickets(5)">
                    <div class="tools info-box mb-3" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Operativos">
                        <span class="info-box-icon bg-red-light elevation-1" style="font-size:1.4rem;background-color:#FF9999;color:white;">
                            <i style="font-size:24px" class="fa">&#xf00d;</i> <!-- Icono de "x" -->
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text" style="font-weight:bold;">
                                Anulados
                            </span>
                            <span class="info-box-number" style="font-weight:bold;" id="fltAnulados">
                                <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                            </span>
                        </div>
                    </div>
               </div>
        </div>
        <div class="card-body" style="padding-right: 10px;">
               <div>
                    <button type="button" class="btn btn-sm ml-1" data-toggle="modal" data-target="#ticketModal" style="color:white; background-color: <?php echo $_SESSION['colorver']?>;" id="btnCrearTickets" onclick="dosFunciones()">
                        <i class="fas fa-plus"></i></i> Crear Ticket
                    </button>
                    <button type="button" class="btn btn-sm ml-1" style="color:white; background-color: <?php echo $_SESSION['colorprin']?>;" id="btnexportartickets" onclick="exportarTicket()">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </button>
    
                    <button type="button" class="btn btn-sm ml-1" style="color:white; background-color: <?php echo $_SESSION['colorprin']?>;" id="btnRefresh" onclick="reloadPagina()">
                        <i style="font-size:21px;" class="fa">&#xf021;</i>
                    </button>
    
                    <label for="clientealb" style="margin-left: 20px;">Cliente</label>
    
                    <select id="clientealb" onchange="listarTickets()" style=" margin-left: 20px; background-color: <?php echo $_SESSION['colorprin']?>; border: 1px solid black; padding: 5px; color: white; border-radius: 5px;">
                         <?php echo $optionsclientes?>
                    </select>
                    <br>
                    
                </div>
                <br>
                <div>
                        <label for="fechaInicio">Desde:</label>
                        <input type="date" id="fechaInicio">

                        <label for="fechaFin">Hasta:</label>
                        <input type="date" id="fechaFin">

                        <button type="button" class="btn btn-sm ml-1" style="color:white; background-color: <?php echo $_SESSION['colorprin']?>;" id="btnAplicarFiltro" onclick="listarTickets()">
                            Aplicar Filtro
                        </button>
                </div>
            
           <div class="row">
           <div class="col-md-12 mt-2 table-responsive" id="divtblcchica">
                    <table class="table table-sm table-bordered " id="tblTicket">
                        <thead class="bg-secondary text-white">
                            <tr>
                               
                                <th nowrap scope="col" style="text-align:center;">Id</th>
                                <th nowrap scope="col" style="text-align:center;">Cliente</th>
                                <th nowrap scope="col" style="text-align:center;">Cliente Asig.</th>
                                <th nowrap scope="col" style="text-align:center;">Solicitante</th>
                                <th nowrap scope="col" style="text-align:center;">Asignado</th>
                                <th nowrap scope="col" style="text-align:center;">Fecha Hora</th>                              
                                <th nowrap scope="col" style="text-align:center;">Tipo</th>
                                <th nowrap scope="col" style="text-align:center;">Asunto</th>
                                <th nowrap scope="col" style="text-align:center;">Descripcion</th>
                                <th nowrap scope="col" style="text-align:center;">Estado Proceso</th>
                                <th nowrap scope="col" style="text-align:center;">Tiempo desde Envio</th>
                                <th nowrap scope="col" style="text-align:center;">Tiempo en Ejecucion</th>
                                <th nowrap scope="col" style="text-align:center;">Respuestas</th>
                                <th nowrap scope="col" style="text-align:center;"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
           </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ticketModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content p-0">
            <div class="modal-header" style="background-color:<?php echo $_SESSION['colorprin']?>;padding: 5px;color:white;">
                <h5 class="modal-title" id="ticketModalLabel">Gestionar Ticket</h5>
                <div class="col-md-1" id="btnconftck" name="btnconftck" for="btnconftck">
                </div>
            </div>
            <div class="modal-body">
                <form id="formTicket" method="POST" >
                <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row mb-0">
                                <label class="col-sm-3 col-form-label" for="tck_solicitante">Solicitante</label>
                                <div class="col-sm-9 pt-2">
                                    <input type="text" readonly class="form-control form-control-sm" id="tck_solicitante" name="tck_solicitante" style="height:25px;" value="<?php echo ucfirst($data["usu_nombre"]) ?>">
                                </div>
                                
                            </div>
                            <div class="form-group row mb-0">
                                <label for="tck_asig" class="col-sm-3 col-form-label">Asignado</label>
                                <div class="col-sm-9 pt-2">
                                    <select name="tck_asig" id="tck_asig" class="form-control form-control-sm" data-placeholder="Seleccione" <?php if ($data["usu_nombre"] !== "Jaime" && $data["usu_nombre"] !== "John") echo 'disabled'; ?>>
                                    <?php echo $optionsUsr?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label for="tck_asig" class="col-sm-3 col-form-label">Cliente Asig.</label>
                                <div class="col-sm-9 pt-2">
                                    <select name="tck_asig_cli" id="tck_asig_cli" class="form-control form-control-sm" data-placeholder="Seleccione" <?php if ($data["usu_nombre"] !== "Jaime" && $data["usu_nombre"] !== "John") echo 'disabled'; ?>>
                                    <?php echo $optionsclientesform?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label for="asig_tipo" class="col-sm-3 col-form-label">Tipo</label>
                                <div class="col-sm-9 pt-2">
                                    <select name="tck_tipo" id="tck_tipo" class="form-control form-control-sm" data-placeholder="Seleccione_Tipo">
                                        <option value="1">Incidencia</option>
                                        <option value="2">Requerimiento</option>
                                        <option value="3">Consulta</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-sm-3 col-form-label" for="imgveh">Archivo</label>
                                <div class="col-sm-9 pt-2" >
                                    <input type="file" class="inputfile form-control form-control-sm" multiple id="fileImg" name="fileImg">
                                </div>
                            </div> 
                            <div class="form-group mb-0">
                                <div class="col-sm-7 pt-2" id="preview">
                               
                                </div> 
                            </div> 
                            <input type="hidden" value="0" id="valorultimotemporal">
                            <input type="hidden" value="0" id="hayonoimg"> 
                            <div class="form-group row mb-0">
                                <label class="col-sm-3 col-form-label" for="tck_asunto">Asunto</label>
                                <div class="col-sm-9 pt-2 pl-2" style="padding-left:40px;">
                                    <input type="text" class="form-control form-control-sm" id="tck_asunto" name="tck_asunto" style="height: 26px;">
                                </div>
                            </div>  

                            <div class="form-group row mb-0">
                                <label class="col-sm-3 col-form-label" for="tck_descripcion">Descripción</label>
                                <div class="col-sm-9 pt-2 pl-2" style="padding-left:40px;">
                                    <textarea  type="text" class="form-control form-control-sm" id="tck_descripcion" name="tck_descripcion" rows="4" cols="50" maxlength="250"></textarea>
                                </div>
                            </div>   
                        </div>
                        <input id="valorid" name="valorid" for="valorId" style="display: none;">
                        <div class="col-md-6">
                            <div class="chatbox-holder">
                                <div class="chatbox">
                                    
                                    <div class="chat-messages" id="chat" name="chat">
                                                                              
                                        
                                    </div>
                                    <div class="chat-input-holder">
                                    <textarea class="chat-input" placeholder="ESCRIBE UN COMENTARIO..." id="tck_comentario" name="tck_comentario" for="tck_comentario"></textarea>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--<div class="col-md-12">
                            
                        </div>
                        <div class="col-md-12">
                            
                        </div>
                        <br>
                        <div class="modal-dialog modal-dialog-centered modal-lg " >
                            <div >
                                <div class="modal-header" style="background-color:<?php echo $_SESSION['colorprin']?>;padding: 5px;color:white;">
                                    <h5 class="modal-title" id="ticketModalLabel" >Historial de comentarios</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row mb-0">
                                <label class="col-sm-2 col-form-label" for="tck_comentario"></label>
                                <div class="col-sm-10 pt-2 pl-2" style="padding-left:40px;" id="divCom" name="divCom" for="divCom">
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group row mb-0">
                                <label class="col-sm-2 col-form-label" for="tck_comentario">Comentario</label>
                                <div class="col-sm-10 pt-2 pl-2" style="padding-left:40px;">
                                    <textarea type="text" class="form-control form-control-sm" id="tck_comentario" name="tck_comentario" style="height:25px;height: 70px;" placeholder="Agrega un comentario" rows="4" cols="50"></textarea>
                                </div>
                            </div>
                        </div>-->
                        
                    </div>
                </form>
                <div class="tiemposite">
                     <ul class="nav nav-tabs" >
                    <li class="nav-item" id="tiemposite" name="tiemposite" for="tiemposite" >
                        <a class="nav-link" id="tiemposs" name="tiemposs" for="tiemposs" data-toggle="tab">Tiempos</a>
                    </li>
                     </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade" id="tiempos">
                        <!-- Contenido de la tabla con datos de tiempos -->
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>INICIO</th>
                                            <th>FIN </th>
                                            <th>TIEMPO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Enviado a Soporte TI</td>
                                            <td>En Ejecucion</td>
                                            <td id="sop_eje" for="sop_eje" name="sop_eje"></td>
                                        </tr>
                                        <tr>
                                            <td>En Ejecucion</td>
                                            <td>Envio Validacion</td>
                                            <td id="eje_vali" for="eje_vali" name="eje_vali"></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Envio Validacion</td>
                                            <td>Confirmacion/Finalizado</td>
                                            <td id="vali_confi" for="vali_confi" name="vali_confi"></td>
                                        </tr>
                                        <tr>
                                            <td>Enviado a Soporte TI</td>
                                            <td>Confirmacion/Finalizado</td>
                                            <td id="sop_confi" for="sop_confi" name="sop_confi"></td>
                                        </tr>
                                        <!-- Agrega más filas según tus datos de tiempos -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 5px;">
                <button type="button" class="btn btn-sm" onclick="cerrticket()"  style="background-color:<?php echo $_SESSION['colorama']?>;" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm" id="btncreartck" onclick="cearTikects()" style="background-color:<?php echo $_SESSION['colorver']?>;">Guardar Ticket</button>
            </div>
        </div>
    </div>
</div>

<script src="../cloux/jsmodulos/ticketsV.1.0.62.js"></script>
<script>
    var iduser = <?php echo $_SESSION['cloux']; ?>;
    var iframe = <?php if(isset($_REQUEST['iframe'])){ if($_REQUEST['iframe']=='yes'){echo 1;}else{echo 0;}}else{echo 0;} ?>;
</script>


<?php
//}
//else{
    //  include('../denegado.php');
//}
}
?>