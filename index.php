<?php
session_start();
$Date = date("Y"); 
if(empty($_SESSION["cloux"])){
  // print_r( $_SESSION );
  // echo 'session vacia'."<br>";
  include("login.php");
}else{
  include("conexion.php");
  include("funciones.php");
  $id=$_SESSION["cloux_new"];
  $misdatos=usuariologeado($id);
  //$indicadores=getIndicatorHeader();
  if($misdatos[$id]["idtransportista"] > 0){
    $btnperfil="<button class='btn btn-rounded btn-danger btn-sm' onclick='tabMisDatos();'>Mi Perfil</button>";
  }else{
    $btnperfil="<a href=index.php?menu=perfil&usuario=".$id." class='btn btn-rounded btn-danger btn-sm'>Mi Perfil</a>";
  }

  $sql = "select * from usuarios usu left outer join tipo_usuario tusu on tusu.tusu_id=usu.usu_perfil where usu.usu_id={$_SESSION["cloux_new"]}";
  $res = $link->query($sql);
  $dusuario = mysqli_fetch_array($res);


  $linkGen = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD');
  if (mysqli_connect_errno()) {
    printf("Falló la conexión: %s\n", mysqli_connect_error());
    exit();
  }
  $rs = $linkGen->query('SHOW DATABASES;');
  while($row = mysqli_fetch_array($rs)){
      if($row[0]!='' && $row[0]!=null){
          if(trim($row[0])!='cloux' && trim($row[0])!='mysql' && trim($row[0])!='information_schema' && trim($row[0])!='performance_schema' && trim($row[0])!='prueba_data'){
                $sql1 = "SELECT * FROM clientes where cuenta = LOWER('{$row[0]}')";
                $res1 = $link->query($sql1);
                if(mysqli_num_rows($res1)==0){
                    $sql2 = "INSERT INTO clientes (rut, razonsocial, giro, region, comuna, direccion, telefono, correo, cli_usuariows, cli_clavews, cli_nombrews, cli_estadows, cuenta, rlegal, rrut)
                    VALUES ('', '', 0, 0, 0, '', '', '', 'ws', 'ws', LOWER('{$row[0]}'), 1, LOWER('{$row[0]}'), '', '');";
                    $res2 = $link->query($sql2);
                }
          }
      }
  }

  $sqlusuario = "SELECT * FROM `usuarios` where usu_id = {$_SESSION['cloux']}";
  $resusuario = $link->query($sqlusuario);
  $filausuario = mysqli_fetch_array($resusuario);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Solutions</title>
  <!-- Para un favicon en formato PNG -->
  <link rel="icon" type="image/png" href="dist/img/favicon.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="dist/css/prism.css">
  <link rel="stylesheet" href="dist/css/chosen.css">
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="plugins/ekko-lightbox/ekko-lightbox.css">
  <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <style>
    .oculto{
      display: none;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini  sidebar-collapse">
<input type="hidden" id="nomusu" value="<?php echo $filausuario['usu_usuario']?>">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <!-- <li class="nav-item d-none d-sm-inline-block">
        <a href="index.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li> -->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Buscar" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- <a href="#" class="dropdown-item">
            <div class="media">
              <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <div class="media">
              <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <div class="media">
              <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
          </a> -->
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-header">0 Notifications</span>
          <div class="dropdown-divider"></div>
          <!-- <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a> -->
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" onclick="salir()" style="cursor: pointer;" role="button">
          <i class="fas fa-power-off"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
      <img src="dist/img/logo-app-blanco.png" alt="DataSolutions" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><strong>Data Solutions<strong></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?=($dusuario['usu_foto']==null ? 'dist/img/user2-160x160.jpg' : $dusuario['usu_foto'] )?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="index.php?idmenu=profile" class="d-block"><?=$dusuario['usu_nombre']?><?=($dusuario['tusu_nombre']==null ? '' : '<br>'.$dusuario['tusu_nombre'])?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <?php
          $permisosusuario=crear_array('permisosusuarios','idusuario','idmodulo','where idusuario="'.$_SESSION["cloux_new"].'"');
          $sqlm="select * from menus where estado = 1 order by orden";
          $resf=$link->query($sqlm);
          while($fila=mysqli_fetch_array($resf)) {
            
            $sqlfma="select * from modulos where idmenu='".$fila["id"]."' AND estado=1";
            $resfma=$link->query($sqlfma);
            ?>
            <li class="nav-item <?php $idmenu=obtenervalor("modulos","idmenu","where id='".$_REQUEST["idmenu"]."' AND estado=1");if($idmenu==$fila["id"]){echo 'menu-open';}?>">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas <?=$fila["icono"];?>"></i>
                  <p>
                    <?php echo $fila["nombre"];?>
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
            <?php
            while($filama=mysqli_fetch_array($resfma)){
              $mostrar=0;
              if($permisosusuario[$filama["id"]][0] > 0){
                $mostrar=$mostrar+1;
              }
              if($mostrar>0){
              $nombremodulo=$filama["nombre"];
              $menu=quitarAcentosEspacios($nombremodulo,'si','si');
              ?>
                  <li class="nav-item">
                    <a href="index.php?menu=<?=$menu;?>&idmenu=<?=$filama["id"];?>" class="nav-link <?php $menusel=$_REQUEST["idmenu"]; if($menusel == $filama["id"]){echo "active";}?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p><?php echo $filama["nombre"];?></p>
                    </a>
                  </li>
              <?php
              }
            }
            ?>
              </ul>
            </li>
            <?php
          }
          ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="funciones.js"></script>
<script src="dist/js/chosen.jquery.js"></script>
<script src="dist/js/prism.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyCyCCcgI4IGRw8wXqx8VlMRyBIWOp4TMh8&libraries=geometry&libraries=places&libraries=drawing"></script>
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
<script src="plugins/filterizr/jquery.filterizr.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
    $(document).ready(function() {
    var nomusu = $("#nomusu").val();
    
    // Verifica si ya se redirigió agregando una marca a la URL
    if (nomusu === 'sotrap2' && !window.location.search.includes('redirected=true')) {
        window.location.href = 'index.php?menu=trabajosfinalizados&idmenu=112&redirected=true';
    }
});


  function salir(){
    Swal.fire({
      title: '¿Desea cerrar sesión?',
      showDenyButton: true,
      confirmButtonText: `Sí`,
      denyButtonText: `No`,
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'salir.php';
      } else if (result.isDenied) {

      }
    })
    
  }
</script>
<!-- <div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <div class="nav navbar navbar-expand navbar-white navbar-light border-bottom p-0">
      <div class="nav-item dropdown">
        <a class="nav-link bg-danger dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Close</a>
        <div class="dropdown-menu mt-0">
          <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all">Close All</a>
          <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all-other">Close All Other</a>
        </div>
      </div>
      <a class="nav-link bg-light" href="#" data-widget="iframe-scrollleft"><i class="fas fa-angle-double-left"></i></a>
      <ul class="navbar-nav overflow-hidden" role="tablist"></ul>
      <a class="nav-link bg-light" href="#" data-widget="iframe-scrollright"><i class="fas fa-angle-double-right"></i></a>
      <a class="nav-link bg-light" href="#" data-widget="iframe-fullscreen"><i class="fas fa-expand"></i></a>
    </div>
    <div class="tab-content">
      <div class="tab-empty">
        <h2 class="display-4">No tab selected!</h2>
      </div>
      <div class="tab-loading">
        <div>
          <h2 class="display-4">Tab is loading <i class="fa fa-sync fa-spin"></i></h2>
        </div>
      </div>
    </div>
  </div> -->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php
    if(isset($_REQUEST["idmenu"])){
      if($_REQUEST["idmenu"]!='profile'){
        $menuselect=obtenervalor("modulos","nombre","where id='".$_REQUEST["idmenu"]."'");
        $idmenu=obtenervalor("modulos","idmenu","where id='".$_REQUEST["idmenu"]."'");
        $nom_menu=obtenervalor("menus","nombre","where id='".$idmenu."'");
      }
      
    }

      if(isset($_REQUEST["menu"])){
        if(file_exists("modulos/".$_REQUEST["menu"].".php")){
          include("modulos/".$_REQUEST["menu"].".php");
        }
        else{
          include("404.php");
        }
      }else{
        if($_REQUEST["idmenu"]=='profile'){
          include("profile.php");
        }
        else{
          if(file_exists("home.php")){
            include("home.php");
          }
          else{
            include("404.php");
          }
        }
        
      }
    ?>
  </div>
  <!-- /.content-wrapper -->
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Opciones</h5>
      <a href="salir.php" class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
        <i class="fa fa-power-off"></i> Salir
      </a>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?=$Date?> <a href="http://cloux.cl">DSOLUTIONS</a>.</strong> Todos los derechos reservados.
  </footer>
</div>
<!-- ./wrapper -->


</body>
</html>
<?php
}

?>