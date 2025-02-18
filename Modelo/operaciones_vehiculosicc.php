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

    case 'vehiculosicc':
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
          
        $sql= "SELECT   t1.id, t1.empresa, t1.vehiculo, 
                        t1.tipo, 
                        t2.nombre as codigo, 
                        -- t2.codigo, 
                        t2.activa, t2.desactiva 
                        FROM vehiculoscc t1 
                        INNER JOIN tipo_icc t2 ON t2.id=t1.tipo 
                        ORDER BY t1.empresa, t1.vehiculo";
        $res = $link->query($sql);
        if($res){
            $empresa = "";
            $connect = null;
            foreach($res as $key => $data){
                $idveh = 0;
                $icc = 0;
                $ccorriente = 0;
                if($empresa == ""){
                    $connect = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD',$data["empresa"]);
                    $sql1= "SELECT veh_id, veh_iccgps, veh_ccorriente FROM vehiculos WHERE veh_patente='{$data["vehiculo"]}'";
                    $res1 = $connect->query($sql1);
                    if($res1){
                        while( $row1 = mysqli_fetch_array($res1) ){
                            $idveh = $row1["veh_id"];
                            $icc = $row1["veh_iccgps"];
                            $ccorriente = $row1["veh_ccorriente"];
                        }
                        mysqli_free_result($res1);
                    }
                    $empresa = $data["empresa"];
                }
                else{
                    if($empresa == $data["empresa"]){
                        $sql1= "SELECT veh_id, veh_iccgps, veh_ccorriente FROM vehiculos WHERE veh_patente='{$data["vehiculo"]}'";
                        $res1 = $connect->query($sql1);
                        if($res1){
                            while( $row1 = mysqli_fetch_array($res1) ){
                                $idveh = $row1["veh_id"];
                                $icc = $row1["veh_iccgps"];
                                $ccorriente = $row1["veh_ccorriente"];
                            }
                            mysqli_free_result($res1);
                        }
                    }
                    else{
                        mysqli_close($connect);
                        $connect = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD',$data["empresa"]);
                        $sql1= "SELECT veh_id, veh_iccgps, veh_ccorriente FROM vehiculos WHERE veh_patente='{$data["vehiculo"]}'";
                        $res1 = $connect->query($sql1);
                        if($res1){
                            while( $row1 = mysqli_fetch_array($res1) ){
                                $idveh = $row1["veh_id"];
                                $icc = $row1["veh_iccgps"];
                                $ccorriente = $row1["veh_ccorriente"];
                            }
                            mysqli_free_result($res1);
                        }
                        $empresa = $data["empresa"];
                    }
                }
                $devuelve[]= array( 
                    "id" =>$data["id"],
                    "empresa" =>$data["empresa"],
                    "idvehiculo" =>$idveh,
                    "vehiculo" =>$data["vehiculo"],
                    "icc" =>$icc,
                    "ccorriente" =>$ccorriente,
                    "tipo" =>$data["tipo"],
                    "codigo" =>$data["codigo"],
                    "activa" =>$data["activa"],
                    "desactiva" =>$data["desactiva"],
                );
            }
        }
       
        mysqli_close($link);
        echo json_encode($devuelve);
    break;

    case 'setVehiculo':

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
        $devuelve = array('status'=>'','message'=>'');
        
        if(isset($_REQUEST['empresa'])){


            if( isset($_REQUEST['id']) && $_REQUEST['id']!='undefined' && $_REQUEST['id']!=null && $_REQUEST['id']!=0){
                $sql= "UPDATE vehiculoscc SET empresa='{$_REQUEST['empresa']}', vehiculo='{$_REQUEST['vehiculo']}', tipo='{$_REQUEST['tipo']}' WHERE id='{$_REQUEST['id']}'";
                $res = $link->query($sql);

                $sql = "UPDATE vehiculos set veh_ccorriente = 1 
                            WHERE veh_id in ( SELECT v.veh_id
                                                FROM vehiculos AS v 
                                                LEFT JOIN clientes AS c ON v.veh_cliente = c.id
                                                WHERE c.cuenta = '{$_REQUEST['empresa']}' AND v.veh_patente = '{$_REQUEST['vehiculo']}'
                                                AND v.deleted_at is null)
                            ";
                $res = $link->query($sql);

            }
            else{

                $sql= "SELECT * FROM vehiculoscc where empresa='{$_REQUEST['empresa']}' and vehiculo='{$_REQUEST['vehiculo']}'";
                $res = $link->query($sql);
                if($res){
                    $sql= "UPDATE vehiculoscc SET tipo='{$_REQUEST['tipo']}' WHERE empresa='{$_REQUEST['empresa']}' and vehiculo='{$_REQUEST['vehiculo']}' ";
                    $res = $link->query($sql);

                    $sql = "UPDATE vehiculos set veh_ccorriente = 1 
                            WHERE veh_id in ( SELECT v.veh_id
                                                FROM vehiculos AS v 
                                                LEFT JOIN clientes AS c ON v.veh_cliente = c.id
                                                WHERE c.cuenta = '{$_REQUEST['empresa']}' AND v.veh_patente = '{$_REQUEST['vehiculo']}'
                                                AND v.deleted_at is null)
                            ";
                    $res = $link->query($sql);

                }else{

                    $sql = "UPDATE vehiculos set veh_ccorriente = 1 
                            WHERE veh_id in ( SELECT v.veh_id
                                                FROM vehiculos AS v 
                                                LEFT JOIN clientes AS c ON v.veh_cliente = c.id
                                                WHERE c.cuenta = '{$_REQUEST['empresa']}' AND v.veh_patente = '{$_REQUEST['vehiculo']}'
                                                AND v.deleted_at is null)
                            ";
                    $res = $link->query($sql);

                    $sql= "INSERT INTO vehiculoscc(empresa, vehiculo, tipo) VALUES ('{$_REQUEST['empresa']}','{$_REQUEST['vehiculo']}','{$_REQUEST['tipo']}')";
                    $res = $link->query($sql);
                }
            }
            
            if($res){
                $conectar = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD',$_REQUEST['empresa']);

                if(str_replace(' ','',$_REQUEST['icc'])!=''){
                    $sql1 = "UPDATE vehiculos SET veh_iccgps='{$_REQUEST['icc']}' WHERE veh_patente='{$_REQUEST['vehiculo']}'";
                    $res1 = $conectar->query($sql1);
                }
                mysqli_close($conectar);
                
                $devuelve["status"] = 'OK';
                $devuelve["message"] = 'Exito al guardar registro.';
            }
            else{
                $devuelve["status"] = 'ERROR';
                $devuelve["message"] = 'Error al guardar registro.';
            }
        }
        else{
            $devuelve["status"] = 'ERROR';
            $devuelve["message"] = 'Error al guardar registro.';
        }
       
        mysqli_close($link);
        echo json_encode($devuelve);
    break;

    case 'getEmpresas':
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        date_default_timezone_set("America/Santiago");
        $fechachile = date("Y-m-d H:i:s");
        $devuelve = array();

        $conexion = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD');
        mysqli_set_charset($link, "utf8");
        $rs = $conexion->query('SHOW DATABASES;');
        
        while ($row = mysqli_fetch_array($rs)) {
            if (trim($row[0]) != 'cloux' && trim($row[0]) != '') {
                $devuelve[] = $row[0];
            }
        }
       
        mysqli_close($conexion);
        echo json_encode($devuelve);
    break;

    case 'getPatenteCliente':

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
          
        

        $sql= "SELECT v.veh_id, v.veh_patente 
                FROM vehiculos AS v 
                LEFT JOIN clientes AS c ON v.veh_cliente = c.id
                WHERE c.cuenta = '".$_REQUEST['empresa']."'
                AND v.deleted_at is null
                ORDER BY v.veh_patente DESC";

                // echo $sql;return true;
        $res = $link->query($sql);
        if($res){
            foreach($res as $key => $data){
                $devuelve[]= array( 
                    "id"     => $data["veh_id"],
                    "nombre" => $data["veh_patente"],
                );
            }
        }

        mysqli_close($link);

        if ($devuelve) {
            echo json_encode(['status' => 'OK', 'patentes' => $devuelve]);
        } else {
            echo json_encode(['status' => 'ERROR', 'message' => 'No se encontraron patentes']);
        }
        
        // echo json_encode($devuelve);
    break;

    case 'getTypes':
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
          
        $sql= "SELECT id,CONCAT(nombre,' ',COALESCE(codigo,''))opcion FROM tipo_icc ORDER BY id";
        $res = $link->query($sql);
        if($res){
            foreach($res as $key => $data){
                $devuelve[]= array( 
                    "id" =>$data["id"],
                    "tipo" =>$data["opcion"],
                );
            }
        }
       
        mysqli_close($link);
        echo json_encode($devuelve);
    break;
    
    default:
        echo json_encode('ERROR');
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