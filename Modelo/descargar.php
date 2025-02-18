<?php
session_start();
if(empty($_SESSION["cloux_new"])){
    include("../login.php");
  }else{
    include("../conexion.php");
    include("../funciones.php");
    $id=$_SESSION["cloux_new"];
    $misdatos=usuariologeado($id);
    
    $sql = "select * from usuarios usu left outer join tipo_usuario tusu on tusu.tusu_id=usu.usu_perfil where usu.usu_id={$_SESSION["cloux_new"]}";
    $res = $link->query($sql);
    $dusuario = mysqli_fetch_array($res);
  }
//include_once("../cloux/lib/nusoap.php");
//require_once '../cloux/lib/phpmailer/PHPMailerAutoload.php';
//require_once '../cloux/lib/phpexcel/PHPExcel.php';
include("../cloux/conexion.php");
include("../cloux/funciones.php");
$datos = array();
$fecha = date('Y-m-d');
date_default_timezone_set("America/Santiago");
date_default_timezone_set("America/Santiago");
$fechachile = date("Y-m-d H:i:s");

if (isset($_GET["id"])) {
    $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
    mysqli_set_charset($link, "utf8");

    foreach($res as $key => $data){
        $imagenes = array();
        $sql1= " SELECT * FROM `gti_imagenes_tck`  where img_id_tck = {$data["id"]}";
        $res1 = $link->query($sql1);
        if(mysqli_num_rows($res1)>0){
            foreach($res1 as $key1 => $data1)
            {
                $nombre = $data1["img_nombre"];
                $imagenes[]= array( 
                    "idImg" =>$data1["img_id"],
                    "NombreImg" =>$data1["img_nombre"],                                                      
                    
                );
            }
        }
    }
    // y obtener la ruta del archivo a partir del ID proporcionado
    // Supongamos que obtienes la ruta del archivo en la variable $ruta:

    $ruta = "../../admin/dist/img/tickets/$nombre"; // La ruta completa del archivo

    if (file_exists($ruta)) {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . basename($ruta));
        readfile($ruta);
        exit;
    } else {
        die("El archivo no existe.");
    }
}
?>