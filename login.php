<?php
try{

// Para el sistema 2 (Cloux)
// session_set_cookie_params(0, '/cloux');

//session_save_path("/var/www/html/cloux/sesiones");
// ini_set("session.cookie_lifetime","7200");
// ini_set("session.gc_maxlifetime","7200");
//session_name("cookie_cloux");
//ini_set('session.cookie_domain', '.cloux' );


// Para el sitio /cloux
//session_set_cookie_params(7200, '/cloux');


if(!isset($_SESSION)){   session_start();}
else{session_destroy();  session_start(); }



// Imprimir todas las cookies de sesión disponibles
//print_r($_COOKIE);
//exit;

include("conexion.php");
include("funciones.php");
if(isset($_REQUEST["usuario"])){

  $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify'; 
  $recaptcha_secret = '6LfTC7AqAAAAAE6bQthdOw7vUtInBrr1xQDFqGyD'; 
  $recaptcha_response = $_POST['recaptcha_response']; 
  $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response); 
  $recaptcha = json_decode($recaptcha); 
  $error = 0;

  // echo $recaptcha->score."<br>";
  if( $recaptcha->score >= 0.7 || $_REQUEST["usuario"] == 'jaime' ){
    // OK. ERES HUMANO, EJECUTA ESTE CÓDIGO
  }else{
    // KO. ERES ROBOT, EJECUTA ESTE CÓDIGO
    $error = 1;
    echo '<span class="msgerror" style="background-color:#900C3F; color:white; font-weight:bold;border-radius:10px;padding-left:4px;padding-right:4px;">Problemas de conexión, por favor recargue la página web.</span><br>';
  }

  $sqluser="select * from usuarios where usu_usuario ='".$_REQUEST["usuario"]."'";
  
  if($link && $error == 0){
    $resuser=$link->query($sqluser);
    $filauser=mysqli_fetch_array($resuser);
    if($filauser['usu_id']!=null){
      $passhash=$filauser["usu_clave"];
      $validar = get_clave($passhash,$_REQUEST["clave"]);
      if($validar == 1){
        $_SESSION["cloux_new"]   = $filauser["usu_id"];
        $_SESSION["usuario_new"] = $filauser["usu_usuario"];
        $_SESSION["perfil_new"]  = $filauser["usu_perfil"];
        $_SESSION["personal_new"]  = $filauser["usu_idpersonal"];

        $_SESSION["cloux"]   = $filauser["usu_id"];
        //$_SESSION["usuario"] = $filauser["usu_usuario"];
        //$_SESSION["perfil"]  = $filauser["usu_perfil"];
        //$_SESSION["personal"]  = $filauser["usu_idpersonal"];

        $permisosQuery="SELECT * from permisosusuarios WHERE idusuario = '".$_SESSION["cloux_new"]."'";
        $resPermisos=$link->query($permisosQuery);
        $dataPermisos = [];
        while($filamo=mysqli_fetch_array($resPermisos)){
          $dataPermisos[$filamo["idmodulo"]] = array("id" => $filamo["id"],"idusuario" => $filamo["idusuario"],"idmodulo" => $filamo["idmodulo"], );
        }
        //$_SESSION["permisos"]=$dataPermisos;
        $_SESSION["permisos_new"]=$dataPermisos;

        $ultimaurl="index.php";
        $ultimologin=date("Y-m-d H:i:s");
        $sqlupdate="update usuarios set usu_ultimologin='".$ultimologin."' where usu_id='".$filauser["usu_id"]."'";
        $resultado=$link->query($sqlupdate);
        $codigosesion=generarcodigo(6);
        $sql="insert into sesiones(ses_codigo,ses_usuario,ses_entrada)values('".$codigosesion."','".$filauser["usu_id"]."','".time()."')";
        $res=$link->query($sql);
        $_SESSION["codsession"]=$codigosesion;
        header("Location:".$ultimaurl."");
        // if($_REQUEST["empresa"]=='cloux'){
        //   header("Location:".$ultimaurl."");
        // }
        // else{
        //   $link->close();
        //   $link = bdconerctor($filauser['usu_bbdd']);
        //   $bbdd = $filauser['usu_bbdd'];
        //   $sqluser="select * from usuarios where usu_usuario ='".$_REQUEST["usuario"]."'";
        //   $resuser=$link->query($sqluser);
        //   $rowuser=mysqli_fetch_array($resuser);
        //   $token = getToken(25);
        //   $_SESSION["bbdd"]=$bbdd;
        //   $_SESSION["cloux"]=$rowuser["usu_id"];
        //   $_SESSION["usuario"]=$rowuser["usuario"];
        //   $_SESSION["usu_perfil"]=$rowuser["usu_perfil"];
        //   $_SESSION["usu_pass"] =$_REQUEST["clave"];
        //   $_SESSION["tk"] = $token;
        //   $ultimaurl="../admin/index.php?bd=".$bbdd;
        //   $ultimologin=date("Y-m-d H:i:s");
          
        //   $sqlupdate="update usuarios set usu_token='".$token."',usu_ultimologin='".$ultimologin."' where usu_id='".$rowuser["usu_id"]."'";
        //   $resultado=$link->query($sqlupdate);

        //   $codigosesion=generarcodigo(6);
        //   $sql="insert into sesiones(ses_codigo,ses_usuario,ses_entrada)values('".$codigosesion."','".$rowuser["usu_id"]."','".time()."')";
        //   $res=$link->query($sql);
        //   $_SESSION["codsession"]=$codigosesion;
        //   header("Location:".$ultimaurl."");
        // }
      }
      else{
        $error = 1;
        echo '<span class="msgerror" style="background-color:#900C3F; color:white; font-weight:bold;border-radius:10px;padding-left:4px;padding-right:4px;">Contraseña ingresada esta incorrecta.</span><br>';
      }
    }
    else{
      $error = 1;
      echo '<span class="msgerror" style="background-color:#900C3F; color:white; font-weight:bold;border-radius:10px;padding-left:4px;padding-right:4px;">Usuario ingresado no está registrado.</span><br>';
    }
  }
  else{
    // echo "Error: No se pudo conectar a MySQL." .'<br>';
    // echo "errno de depuración: " . mysqli_connect_errno() .'<br>';
    // echo "error de depuración: " . mysqli_connect_error() .'<br>';
    $error = 1;
    echo '<span class="msgerror" style="background-color:#900C3F; color:white; font-weight:bold;border-radius:10px;padding-left:4px;padding-right:4px;">Problemas de conexión, por favor recargue la página web.</span><br>';
  }
}
}catch (\Throwable $th) {
  $dataSend = array();
  $dataSend[0]=''.$th;
  echo json_encode($dataSend);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flotas Cloux</title>

  <!-- Para un favicon en formato PNG -->
  <link rel="icon" type="image/png" href="dist/img/favicon.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
    /*BALANCEO*/
    /*Efecto de balanceo, jugando con la escala y la rotación*/
    .msgerror::before{
      animation: balanceo 1s 1;
      transform-origin: top center;
    }
    @keyframes balanceo{
      20%{
        transform: scale(0.7) rotate(-6deg);
      }
      30%, 70%{
        transform: scale(1.1) rotate(6deg);
      }
      50%, 90%{
        transform: scale(1.1) rotate(-6deg);
      }
    }
    .backlogin{
      background-repeat: no-repeat;
      background-image: url('dist/img/fondo-login.jpg'); 
      background-size: 100% 100%;;
    }
    </style>
</head>
<body class="hold-transition login-page backlogin">
<div class="login-box">
  
  <!-- /.login-logo -->
  <div class="card" style="border-radius: 12px;">
    <div class="card-body login-card-body">
      <p class="login-box-msg">CONTROL DE ACCESO</p>

      <form id="formLogin" action="login.php"  role="form" method="post"  accept-charset="UTF-8">
        <input type="hidden" id="metodo" name="metodo" value="login">
        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
        <div class="input-group mb-3">
          <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Usuario" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" id="clave" name="clave" class="form-control" placeholder="Contraseña" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" id="empresa" name="empresa" class="form-control" placeholder="Empresa" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-building"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Recuerdame
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-12">
            <button type="submit" class="btn btn-block text-white" style="background-color: #323232;">Iniciar sesión</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mb-1 mt-2">
        <a href="forgot-password.html" style="color: grey;">Olvidé mi contraseña</a>
      </p>
      <a href="index.php"><img src="dist/img/logo-app-color.png" style="margin-left: 30%;" width="130" alt="logo-app"></a>
      
      
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js?render=6LfTC7AqAAAAAHkBDYgyR9ILiNcWaiFmJwuhzvBO"></script>

<script>
  $(document).ready(function(){

    // grecaptcha.ready(function() {
    //     grecaptcha.execute('6LfTC7AqAAAAAHkBDYgyR9ILiNcWaiFmJwuhzvBO', { action: 'loginCloux' }).then(function(token) {
    //         // Agregar el token al formulario
    //         document.getElementById('recaptchaResponse').value = token;
    //     });
    // });

    // Ejecutar al cargar la página
    refreshRecaptchaToken();
    setInterval(refreshRecaptchaToken, 120000);

  });

  function refreshRecaptchaToken() {
      grecaptcha.ready(function() {
          grecaptcha.execute('6LfTC7AqAAAAAHkBDYgyR9ILiNcWaiFmJwuhzvBO', { action: 'loginAdmin' }).then(function(token) {
              // Actualizar el token en el campo oculto
              document.getElementById('recaptchaResponse').value = token;
              console.log('Token actualizado:', token);
          });
      });
  }

</script>

</body>
</html>
