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



    #tblhis > thead tr:nth-child(1) th{background: <?php echo $_SESSION['colorprin']?>; position: sticky;top: 0;z-index: 20;}
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
                    <h3 class="card-title font-weight-bold" style="padding:3px 1px;">Historial Imei Patentes</h3>
                </div>
                
            </div>
        </div>
        
        <div class="card-body" style="padding-right: 10px;">
              
                <div class="row">
                    <div class="col-md-12 mt-2 table-responsive" id="divtblhis">
                        <table class="table table-sm table-bordered " id="tblhis">
                             <thead class="bg-secondary text-white">
                                 <tr>
                               
                                     <th nowrap scope="col" style="text-align:center;">Patente</th>
                                     <th nowrap scope="col" style="text-align:center;">Imei</th>
                                     <th nowrap scope="col" style="text-align:center;">Fecha Ingreso</th>
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

<script src="../cloux/jsmodulos/historial_vehV.1.0.1.js"></script>
<script>
    var iduser = <?php echo $_SESSION['cloux_new']; ?>;
    var iframe = <?php if(isset($_REQUEST['iframe'])){ if($_REQUEST['iframe']=='yes'){echo 1;}else{echo 0;}}else{echo 0;} ?>;
</script>


<?php
//}
//else{
    //  include('../denegado.php');
//}
}
?>