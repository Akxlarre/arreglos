<?php
//echo  'jajajajaj';
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
  //require_once '../cloux/lib/phpexcel/PHPExcel.php';
//include_once("../cloux/lib/nusoap.php");
require_once '../lib/phpmailer/PHPMailerAutoload.php';
require_once ("../lib/phpexcel/PHPExcel.php");
include("../cloux/conexion.php");
include("../cloux/funciones.php");
$datos = array();
$fecha = date('Y-m-d');
date_default_timezone_set("America/Santiago");
date_default_timezone_set("America/Santiago");
$fechachile = date("Y-m-d H:i:s");



switch ($_REQUEST['operacion']) {
    case 'listarHistorial_veh':
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
        mysqli_set_charset($link, "utf8");
        date_default_timezone_set("America/Santiago");
        $fechachile = date("Y-m-d H:i:s");
        $devuelve = array();
          
        $sql= " SELECT * FROM `historial_vehiculo`";
        $res = $link->query($sql);
         if(mysqli_num_rows($res)>0){
             foreach($res as $key => $data){
                $devuelve[]= array( 
                    "Patente" =>$data["his_patente"],
                    "Imei" =>$data["his_imei"],
                    "Fecha" =>$data["his_fecha"],
                );

             }
         }
       
        mysqli_close($link);
        echo json_encode($devuelve);
    break;
    
break; 
}

function cellColor($cells,$color){
    global $objPHPExcel;
  
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(
        array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => $color
            ),
        )
    );
}

function celltextcolor($cells,$size=15,$colortext='FF0000'){
    global $objPHPExcel;
  
    $styleArray = array(
      'font'  => array(
          'bold'  => true,
          'color' => array('rgb' => $colortext),
          'size'  => $size,
          'name'  => 'Verdana'
      ));
  
    $objPHPExcel->getActiveSheet()->getStyle($cells)->applyFromArray($styleArray);
}

?>