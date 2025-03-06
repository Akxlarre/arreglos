<?php
session_start();
include_once("lib/nusoap.php");
//require_once '../lib/phpmailer/PHPMailerAutoload.php';
require_once 'lib/phpmailer/PHPMailerAutoload.php';
require_once 'lib/phpexcel/PHPExcel.php';
include("conexion.php");
include("funciones.php");

require_once "../admin/dompdf2-0/lib/html5lib/Parser.php";
require_once "../admin/dompdf2-0/lib/php-font-lib/src/FontLib/Autoloader.php";
require_once "../admin/dompdf2-0/lib/php-svg-lib/src/autoload.php";
require_once "../admin/dompdf2-0/src/Autoloader.php";
require_once '../admin/dompdf2-0/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$datos = array();
$fecha = date('Y-m-d');
date_default_timezone_set("America/Santiago");
date_default_timezone_set("America/Santiago");
$fechachile = date("Y-m-d H:i:s");

function cellColor($cells, $color)
{
  global $objPHPExcel;
  $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => $color)));
}
//debug 
 /* $log_dir = "/var/www/html/cloux/logs"; // Ruta del log
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

$log_file = $log_dir . "/debug_peticiones.txt";

file_put_contents(__DIR__ . "/logs/debug_peticiones.txt", 
  "[" . date('Y-m-d H:i:s') . "] 📩 Datos recibidos:\n" . print_r($_REQUEST, true) . "\n\n",
  FILE_APPEND
);

if (isset($link) && mysqli_connect_errno()) {
  file_put_contents(__DIR__ . "/logs/debug_peticiones.txt", 
      "❌ Error de conexión a MySQL: " . mysqli_connect_error() . "\n", 
      FILE_APPEND
  );
}  */

function ejecutarConsulta($sql, $link) {
  $resultado = $link->query($sql);
  if (!$resultado) {
      file_put_contents(__DIR__ . "/logs/debug_peticiones.txt", 
          "⚠️ Error en SQL: " . mysqli_error($link) . "\nConsulta: " . $sql . "\n\n", 
          FILE_APPEND
      );
  }
  return $resultado;
}



switch ($_REQUEST["operacion"]) {
    /*****************************************
OPERACIONES ESTANDAR
   *******************************************/
  case 'getComunas':
    $opciones = "";
    $sql2 = "SELECT comuna_nombre AS comuna, comuna_id AS id FROM comunas 
        INNER JOIN provincias ON comunas.provincia_id = provincias.provincia_id 
        INNER JOIN regiones ON provincias.region_id = regiones.id 
        WHERE id ='" . $_REQUEST["region"] . "'";
    $res2 = $link->query($sql2);
    while ($fila2 = mysqli_fetch_array($res2)) {
      $opciones .= "<option value='" . $fila2["id"] . "'>" . $fila2["comuna"] . "</option>";
    }
    echo $opciones;
    break;


  case 'setConfColum':
    $recibe = json_decode($_REQUEST['envio'], true);

    $sql = "UPDATE configuracion_columnas SET coco_visible ='{$recibe['ch']}' WHERE usu_id = {$_SESSION['cloux_new']} and coco_ncolumna = {$recibe['id']} and coco_pestana = 1";
    $res = $link->query($sql);
    if ($res) {
      $devuelve = array('respuesta' => 'success', 'mensaje' => 'Configuracion guardada correctamente', 'sql' => $sql);
    } else {
      $devuelve = array('respuesta' => 'error', 'mensaje' => 'Se ha producido un error', 'sql' => $sql);
    }
    echo json_encode($devuelve);

    break;

  case 'getOTPDF':

    $idveh = 0;
    $idorigen = 0;
    $iddestino = 0;
    $seriesim = '';
    $firmaTec = '';
    $firmaCli = '';
    $nombrefirma = '';
    $tecnico = '';
    $patente = '';
    $descripcion = '';
    $fhlabor = '';
    $ttrabajo = '';
    $tservicio = '';
    $cliente = '';
    $idcliente = 0;
    $sql = "SELECT tic_seriesim, tic_cliente, tic_fhinicio,tic_patente,tic_descripcion,tic_desccierre,tic_comuna_ori,tic_comuna_des,tic_firmaTec,tic_firmaCli,tic_nombrefirma, per.per_nombrecorto, veh.veh_patente, ttra.ttra_nombre, ser.ser_nombre, cli.cli_nombrews FROM tickets tic LEFT OUTER JOIN personal per ON per.per_id=tic.tic_tecnico LEFT OUTER JOIN vehiculos veh ON veh.veh_id=tic.tic_patente LEFT OUTER JOIN tiposdetrabajos ttra ON ttra.ttra_id=tic.tic_tipotrabajo LEFT OUTER JOIN servicios ser ON ser.ser_id=tic.tic_tiposervicio LEFT OUTER JOIN clientes cli On cli.id=tic.tic_cliente WHERE tic_id='{$_REQUEST['idticket']}'";
    $res = $link->query($sql);
    while ($fila = mysqli_fetch_array($res)) {
      $idcliente = $fila['tic_cliente'];
      $fhlabor = $fila['tic_fhinicio'];
      $idveh = $fila['tic_patente'];
      $seriesim = $fila['tic_seriesim'];
      $idorigen = $fila['tic_comuna_ori'];
      $iddestino = $fila['tic_comuna_des'];
      $firmaTec = $fila['tic_firmaTec'];
      $firmaCli = $fila['tic_firmaCli'];
      $nombrefirma = $fila['tic_nombrefirma'];
      $tecnico = $fila['per_nombrecorto'];
      $patente = $fila['veh_patente'];
      $descripcion = $fila['tic_descripcion'];
      $comentario = $fila['tic_desccierre'];
      $ttrabajo = $fila['ttra_nombre'];
      $tservicio = $fila['ser_nombre'];
      $cliente = $fila['cli_nombrews'];
    }

    $imgTrab = array();
    $sql1 = "SELECT timg_id, timg_tipo, timg_subtipo, timg_name FROM tickets_img WHERE timg_idticket='{$_REQUEST['idticket']}' ORDER BY timg_tipo";
    $res1 = $link->query($sql1);
    while ($fila1 = mysqli_fetch_array($res1)) {
      $tipo = '';
      $ntipo = '';
      if ($fila1['timg_tipo'] == 0) {
        $ntipo = 'Previa';
        if ($fila1['timg_subtipo'] == 1) {
          $tipo = 'F. Patente';
        }
        if ($fila1['timg_subtipo'] == 2) {
          $tipo = 'T. Instrumento';
        }
        if ($fila1['timg_subtipo'] == 3) {
          $tipo = 'P. Tablero';
        }
        if ($fila1['timg_subtipo'] == 4) {
          $tipo = 'D. Daños';
        }
      }
      if ($fila1['timg_tipo'] == 1) {
        $ntipo = 'Posterior';
        if ($fila1['timg_subtipo'] == 1) {
          $tipo = 'T. Instrumento';
        }
        if ($fila1['timg_subtipo'] == 2) {
          $tipo = 'Puntos Conexión';
        }
        if ($fila1['timg_subtipo'] == 3) {
          $tipo = 'V. Panorámica';
        }
        if ($fila1['timg_subtipo'] == 4) {
          $tipo = 'U. Equipo';
        }
      }
      $imgTrab[] = array(
        'id' => $fila1['timg_id'],
        'tipo' => $ntipo,
        'ntipo' => $tipo,
        'idtipo' => $fila1['timg_tipo'],
        'idsubtipo' => $fila1['timg_subtipo'],
        'img' => $fila1['timg_name']
      );
    }

    $accesorios = array();
    $sql1 = "SELECT ava_id, ava_idveh, ava_idguia, ava_serie, ava_estado FROM asociacion_vehiculos_accesorios WHERE ava_estado=1 AND ava_idveh='{$idveh}'";
    $res1 = $link->query($sql1);
    while ($fila1 = mysqli_fetch_array($res1)) {
      $npro = "";
      $sql2 = "SELECT pro.pro_nombre FROM serie_guia ser INNER JOIN productos pro ON pro.pro_id=ser.pro_id WHERE ser_id='{$fila1['ava_idguia']}'";
      $res2 = $link->query($sql2);
      while ($fila2 = mysqli_fetch_array($res2)) {
        $npro = $fila2['pro_nombre'];
      }
      $accesorios[] = array(
        'ser_id' => $fila1['ava_idguia'],
        'ser_codigo' => $fila1['ava_serie'],
        'pro_nombre' => $npro,
      );
    }

    $nserieCan = '';
    if ($idveh != '' && $idveh != null && $idveh != 0) {
      $sql1 = "SELECT ser_idcan FROM asociacion_vehiculos_sensores WHERE veh_id='{$idveh}'";
      $res1 = $link->query($sql1);
      while ($fila1 = mysqli_fetch_array($res1)) {
        $sql2 = "SELECT ser_codigo FROM serie_guia WHERE ser_id='{$fila1['ser_idcan']}'";
        $res2 = $link->query($sql2);
        if (mysqli_num_rows($res2) > 0) {
          $ser_id1 = mysqli_fetch_array($res2);
          $nserieCan = $ser_id1['ser_codigo'];
        }
      }
    }

    $nserie = '';
    $nseriecan = '';
    $sql = "SELECT pxv_nserie, t2.pro_familia FROM productosxvehiculos t1 LEFT OUTER JOIN productos t2 ON t2.pro_id=t1.pxv_idpro WHERE pxv_estado=1 AND pxv_idveh='{$idveh}'";
    $res = $link->query($sql);
    while ($fila = mysqli_fetch_array($res)) {
      if($fila['pro_familia']==19){ // GPS
        $nserie = $fila['pxv_nserie'];
      }
      if($fila['pro_familia']==22){ // CAN
        $nseriecan = $fila['pxv_nserie'];
      }
    }

    $kms = 0;
    $origen = "";
    $destino = "";
    $sql1 = "SELECT mcom_kms,mcom_comorigen,mcom_comdestino FROM matriz_comunas WHERE mcom_idorigen='{$idorigen}' AND mcom_iddestino='{$iddestino}'";
    $res1 = $link->query($sql1);
    while ($fila1 = mysqli_fetch_array($res1)) {
      $kms = $fila1["mcom_kms"];
      $origen = $fila1["mcom_comorigen"];
      $destino = $fila1["mcom_comdestino"];
    }
    if ($kms == null || $kms == '' || $kms == '0') {
      $kms = "N/A";
    }

    $acc = '';
    foreach ($accesorios as $key => $value) {
      $acc .= $value['pro_nombre'] . ($value['ser_codigo'] != "" ? "(" + $value['ser_codigo'] . ")" : "") . ", ";
    }

    if($acc!=''){
      $acc = substr($acc, 0, -2);
    }
    

    $fecha = date('d-m-Y H:i:s');
    $fields = array(
      'fecha' => $fecha,
      'nticket' => $_REQUEST['idticket'],
      'tecnico' => $tecnico,
      'nombre' => $nombrefirma,
      'patente' => $patente,
      'fhlabor' => $fhlabor,
      'ttrabajo' => $ttrabajo,
      'tservicio' => $tservicio,
      'img' => $imgTrab,
      'accesorios' => $acc,
      'nserie' => ($nserie == null ? 'N/A' : $nserie),
      'nserieCan' => ($nseriecan == null ? 'N/A' : $nseriecan),
      'origen' => ($origen == null ? 'N/A' : $origen),
      'destino' => ($destino == null ? 'N/A' : $destino),
      'kms' => ($kms == null ? 'N/A' : $kms),
      'firmaTec' => $firmaTec,
      'firmaCli' => $firmaCli,
      'descripcion' => $descripcion,
      'comentario' => $comentario,
      'cliente' => $cliente,
    );
    $fields_string = http_build_query($fields);
    $url = 'http://34.226.130.122/cloux/pdfOt.php';
    $varcrear = curl_init();
    curl_setopt($varcrear, CURLOPT_URL, $url);
    curl_setopt($varcrear, CURLOPT_POST, 1);
    curl_setopt($varcrear, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($varcrear, CURLOPT_RETURNTRANSFER, 1);
    $final_html = curl_exec($varcrear);
    curl_close($varcrear);

    $dompdf = new DOMPDF($options);
    $dompdf->set_paper('A4', 'portrait');
    $dompdf->load_html($final_html);
    $dompdf->render();
    $pdf = $dompdf->output();
    $base64pdf = base64_encode($pdf);
    $target_file = '../archivos/';
    $name = strtotime(date('Y-m-d H:i:s')) . '_' . $cliente . '.pdf';
    $ruta = $target_file . $name;
    if (file_put_contents($ruta, base64_decode($base64pdf))) {
    }

    echo $base64pdf;
    break;

  case 'getcolumnas':
    $recibe = json_decode($_REQUEST['envio'], true);

    $sql = "select * from configuracion_columnas WHERE usu_id = {$_SESSION['cloux_new']} and coco_pestana = 1";
    $res = $link->query($sql);
    /*echo $sql.'<br>';*/
    $devuelve = array();
    if (mysqli_num_rows($res) > 0) {
      foreach ($res as $r) {
        array_push($devuelve, array('columna' => $r['coco_ncolumna'], 'valor' => $r['coco_visible']));
      }
    }
    echo json_encode($devuelve);

    break;

  case 'getComunasData':
    $opciones = array();
    $sql2 = "SELECT * FROM personal per LEFT OUTER JOIN comunas com ON com.comuna_id=per.per_comuna WHERE per.per_id='" . $_REQUEST["usuario"] . "'";
    $res2 = $link->query($sql2);

    while ($fila2 = mysqli_fetch_array($res2)) {
      $opciones[] = array("comuna" => $fila2['comuna_nombre']);
    }
    echo json_encode($opciones);
    break;

  case 'desintalarlaimei':

    $recibe = json_decode($_REQUEST['envio'], true);
    $devuelve = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');
    $sql = "SELECT * FROM serie_guia where ser_id = {$recibe['idimei']}";
    $res = $link->query($sql);
    if (mysqli_num_rows($res) > 0) {
      $fila = mysqli_fetch_array($res);
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://54.158.85.208/api/v1/deleteimei',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('imei' => $fila['ser_codigo']),
        CURLOPT_HTTPHEADER => array(
          'Authorization: 202cb962ac59075b964b07152d234b70'
        ),
      ));

      /*$fila['ser_codigo']*/
      $respalba = curl_exec($curl);
      curl_close($curl);

      $varveh = json_decode($respalba, true);
      if ($varveh['imei'] != 'Error al borrar imei de data gps.') {
        $empexis = json_decode($varveh['clientes'], true);
        if (count($empexis) > 0) {
          foreach ($empexis as $keyexi => $dataexis) {
            $_bbddclient3 = strtolower($dataexis['cliente']);
            if ($bbddclient3 != '') {
              $_bbddclient3 = $bbddclient3;
            }

            $linkclient3 = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', $_bbddclient3);

            if (mysqli_connect_errno()) {
              printf("Falló la conexión: %s\n", mysqli_connect_error());
              exit();
            }

            mysqli_set_charset($linkclient3, "utf8");

            $sql3    = "update vehiculos set veh_seriegps = '' WHERE veh_patente = '{$dataexis['patente']}' and deleted_at is NULL";
            $res3 = $linkclient3->query($sql3);

            mysqli_close($linkclient3);
          }
        }
        $devuelve = array('respuesta' => 'success', 'mensaje' => 'Enviado correcto');
      }
    }
    echo json_encode($devuelve);
    break;
    /************************************
OPERACIONES CLIENTES
     **************************************/
  case 'ValidarCliente':
    $sql = "select * from clientes where razonsocial='" . $_REQUEST["razonsocial"] . "' || cli_nombrews='" . $_REQUEST["nombrews"] . "'";
    $res = $link->query($sql);
    $cuenta = mysqli_num_rows($res);
    echo $cuenta;
    break;

  case 'exportarexcel':
    $_SESSION['colorprin'] = '#7058c3';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('max_execution_time', '360');
    ini_set('memory_limit', '128M');
    setlocale(LC_MONETARY, 'en_US');

    $fecha = date('d-m-Y H:i:s');
    $via = json_decode($_REQUEST['datos'], true);

    $letras = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK");

    try {
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("D-Solutions");
      $objPHPExcel->getProperties()->setLastModifiedBy("D-Solutions");
      $objPHPExcel->getProperties()->setTitle("Sin Transmisiones");
      $objPHPExcel->getProperties()->setSubject("Sin Transmisiones");
      $objPHPExcel->getProperties()->setDescription("Sin Transmisiones");
      $objPHPExcel->setActiveSheetIndex(0);

      $headers = $via[0];
      $letrafinal = '';
      $indice = 0;
      $style = array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '7058c3'),
        ),
        'font' => array(
          'color' => array('rgb' => 'FFFFFF'),
        ),
      );

      for ($i = 0; $i < count($headers); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue($letras[$indice] . '1', $headers[$indice]);
        $letrafinal = $letras[$indice];
        $cell = $objPHPExcel->getActiveSheet()->getCell($letras[$indice] . '1');
        $objPHPExcel->getActiveSheet()->getStyle($cell->getCoordinate())->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getColumnDimension($letras[$indice])->setWidth(15);
        $indice++;
      }

      $objPHPExcel->getActiveSheet()->getStyle('A1:' . $letrafinal . '1')->getFont()->setBold(true);
      $style = array(
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
      );

      $objPHPExcel->getActiveSheet()->getStyle("A1:" . $letrafinal . "1")->applyFromArray($style);

      $indice = 0;
      for ($i = 1; $i <= count($via); $i++) {
        $indice2 = 0;
        for ($o = 0; $o < count($headers); $o++) {

          $objPHPExcel->getActiveSheet()->SetCellValue($letras[$indice2] . $i, $via[$indice][$o]);
          $indice2++;
        }
        $indice++;
      }

      $objPHPExcel->getActiveSheet()->setTitle('Sin Transmisiones');
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      ob_start();
      $objWriter->save("php://output");
      $xlsData = ob_get_contents();
      ob_end_clean();
      $response =  array(
        'op' => 'ok',
        'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
      );
      echo json_encode($response);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }
    break;

  case 'upddetras':
    $recibe = json_decode($_REQUEST['envio'], true);
    $idnuevo = 0;
    /*$sql = "SELECT * FROM historial_vehiculo WHERE his_patente = '{$recibe['patente']}'";
    $res = $link->query($sql);*/
    $patente1 = str_replace("_", "(", $recibe['patente']);
    $patente = str_replace("-", ")", $patente1);
    $recibe['patente'] = str_replace("á", "/", $patente);

    if ($recibe['fecha'] == '' || $recibe['fecha'] == null) {
      $recibe['fecha'] = 'null';
    } else {
      $recibe['fecha'] = "'" . $recibe['fecha'] . "'";
    }
    /*if(mysqli_num_rows($res)>0){
        $sql1 = "UPDATE historial_vehiculo set his_comentario = '{$recibe['comentario']}' , his_fechaupd = {$recibe['fecha']} WHERE his_patente = '{$recibe['patente']}'";
        $res1 = $link->query($sql1);
    }else{*/
    $sql1 = "INSERT INTO historial_vehiculo (his_patente,his_fecha,his_comentario,his_fechaupd) VALUES ('{$recibe['patente']}','{$fechachile}','{$recibe['comentario']}',{$recibe['fecha']})";
    $res1 = $link->query($sql1);
    /* if($_SESSION['cloux']==16){
            echo $sql1.'<br>';
        }*/

    $idnuevo = $link->insert_id;
    /*}*/

    $devuelve = array('mensaje' => 'Ha ocurrido un error', 'respuesta' => 'error', 'idnuevo' => $idnuevo);
    if ($res1) {
      $devuelve = array('mensaje' => 'Actualizado correctamente', 'respuesta' => 'success', 'idnuevo' => $idnuevo);
    }
    echo json_encode($devuelve);
    break;

  case 'upddetras2':
    $recibe = json_decode($_REQUEST['envio'], true);
    $idnuevo = 0;
    /*$sql = "SELECT * FROM historial_vehiculo WHERE his_patente = '{$recibe['patente']}'";
    $res = $link->query($sql);*/
    $patente1 = str_replace("_", "(", $recibe['patente']);
    $patente = str_replace("-", ")", $patente1);
    $recibe['patente'] = str_replace("á", "/", $patente);

    if ($recibe['fecha'] == '' || $recibe['fecha'] == null) {
      $recibe['fecha'] = 'null';
    } else {
      $recibe['fecha'] = "'" . $recibe['fecha'] . "'";
    }
    /*if(mysqli_num_rows($res)>0){
        $sql1 = "UPDATE historial_vehiculo set his_comentario = '{$recibe['comentario']}' , his_fechaupd = {$recibe['fecha']} WHERE his_patente = '{$recibe['patente']}'";
        $res1 = $link->query($sql1);
    }else{*/
    $sql1 = "INSERT INTO comentarios_veh_sintelemetria (cvst_patente,cvst_fecha,cvst_comentario,cvst_fechaupd) VALUES ('{$recibe['patente']}','{$fechachile}','{$recibe['comentario']}',{$recibe['fecha']})";
    $res1 = $link->query($sql1);
    $idnuevo = $link->insert_id;
    /*}*/

    $devuelve = array('mensaje' => 'Ha ocurrido un error', 'respuesta' => 'error', 'idnuevo' => $idnuevo);
    if ($res1) {
      $devuelve = array('mensaje' => 'Actualizado correctamente', 'respuesta' => 'success', 'idnuevo' => $idnuevo);
    }
    echo json_encode($devuelve);
    break;

  case 'traeroperativos':
    $sql = "SELECT * FROM estado_vehiculos WHERE eve_fun = 1";
    $res = $link->query($sql);
    $response = array();
    $data = array();
    $cuenta = mysqli_num_rows($res);
    if (mysqli_num_rows($res) > 0) {
      foreach ($res as $key => $r) {
        $tieneticket = 'No';
        $disabtn = '';
        $sql2 = "SELECT *
            FROM vehiculos
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(veh_patente), ')', ''), '(', ''), ' ', ''), '|', ''), '-', ''), '_', '') LIKE '%{$r['eve_patente']}%' and deleted_at is NULL";
        $res2 = $link->query($sql2);
        if (mysqli_num_rows($res2) > 0) {
          $fila2 = mysqli_fetch_array($res2);
          $sql3 = "SELECT * FROM tickets where tic_patente = {$fila2['veh_id']} and tic_tipotrabajo = 1";
          $res3 = $link->query($sql3);
          if (mysqli_num_rows($res3) > 0) {
            $tieneticket = 'Si';
            $disabtn = 'disabled';
          } else {
            $tieneticket = 'No';
          }
        } else {
          $tieneticket = 'No';
        }

        $data[] = array(
          'patente' => $r['eve_patente'],
          'id' => $r['eve_id'],
          'fecha' => $r['eve_fh'],
          'usuario' => $r['eve_nombreusu'],
          'tservicio' => ($r['eve_tservicio'] == 1 ? 'Canbs' : 'Telemetría'),
          'cliente' => $r['eve_cliente'],
          'tieneticket' => $tieneticket,
          'disa' => $disabtn,
          'estado' => ($r['eve_fun'] == 1 ? 'Operativo' : '-'),
        );
      }
    }

    $response['data'] = $data;
    echo json_encode($response);
    break;

  case 'agregarpatente':

    $recibe = json_decode($_REQUEST['envio'], true);
    $valor  = str_replace("_", "", $recibe['patente']);
    $valor  = str_replace("_", "", $valor);
    $valor  = str_replace("|", "", $valor);
    //$valor  = str_replace("(","",$valor);
    //$valor  = str_replace(")","",$valor);
    // $valor  = str_replace("/","",$valor);
    //$valor  = str_replace(" ","",$valor);
    $valor  = strtoupper(trim($valor));
    $repetido = 0;
    //WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(veh_patente), ')', ''), '(', ''), ' ', ''), '|', ''), '-', ''), '_', '') 
    $sql = "SELECT *
            FROM vehiculos
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(TRIM(veh_patente), '_', ''), '|', ''), '_', ''), '_', '') 
            LIKE '%{$valor}%' and deleted_at is NULL";
    $res = $link->query($sql);
    $response = array();
    $devuelve = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error', 'query' => $sql);
    if ($res && mysqli_num_rows($res) == 0) {
      $sql = "INSERT INTO vehiculos (veh_cliente,veh_rsocial,veh_patente) VALUES ({$recibe['cliente']},{$recibe['cliente']},'{$valor}')";
      $res = $link->query($sql);

      if ($res) {
        $devuelve = array('respuesta' => 'success', 'mensaje' => 'Agregado correctamente');
        $sql1 = "SELECT * FROM vehiculos 
                    where deleted_at is NULL";
        $res1 = $link->query($sql1);
        if (mysqli_num_rows($res1) > 0) {
          foreach ($res1 as $key1 => $data1) {
            $selected = '';
            if ($data1['veh_patente'] == $valor) {
              $selected = 'selected';
            }
            $response['options'][] = array('<option value="' . $data1['veh_id'] . '" ' . $selected . '>' . $data1['veh_patente'] . '</option>');
          }
        }
      }
    } else {
      if (strtoupper($valor) == "NUEVO") {

        $pasa = true;
        foreach ($res as $keyda => $fila) {
          if ($recibe['cliente'] == $fila['veh_rsocial'] && $recibe['cliente'] == $fila['veh_cliente']) {
            $pasa =  false;
          }
        }

        if ($pasa) {
          $sql = "INSERT INTO vehiculos (veh_cliente,veh_rsocial,veh_patente) VALUES ({$recibe['cliente']},{$recibe['cliente']},'{$valor}')";
          $res = $link->query($sql);
          if ($res) {
            $devuelve = array('respuesta' => 'success', 'mensaje' => 'Agregado correctamente');
            $sql1 = "SELECT * FROM `vehiculos` where veh_cliente = {$recibe['cliente']} and veh_rsocial = {$recibe['cliente']} 
                    and deleted_at is NULL";
            $res1 = $link->query($sql1);
            if (mysqli_num_rows($res1) > 0) {
              foreach ($res1 as $key1 => $data1) {
                $selected = '';
                if ($data1['veh_patente'] == $valor) {
                  $selected = 'selected';
                }
                $response['options'][] = array('<option value="' . $data1['veh_id'] . '" ' . $selected . '>' . $data1['veh_patente'] . '</option>');
              }
            }
          }
        } else {
          $repetido = 1;
        }
      } else {
        $repetido = 1;
      }
    }

    $response['repetido'] = $repetido;
    $response['respuesta'] = $devuelve;
    echo json_encode($response);

    break;


  case 'nuevocliente':
    $region = 0;
    $comuna = 0;
    if (isset($_REQUEST["region"])) {
      if ($_REQUEST["region"] == '') {
        $region = 0;
      } else {
        $region = $_REQUEST["region"];
      }
    }
    if (isset($_REQUEST["comuna"])) {
      if ($_REQUEST["comuna"] == '') {
        $comuna = 0;
      } else {
        $comuna = $_REQUEST["comuna"];
      }
    }

    if (isset($_REQUEST["tipo_prestador"])) {
      if ($_REQUEST["tipo_prestador"] == '') {
        $prestador = "''";
      } else {
        $prestador = "'" . strtolower($_REQUEST["tipo_prestador"]) . "'";
      }
    } else {
      $prestador = "''";
    }

    $sql = "INSERT INTO clientes (rut,razonsocial,giro,region,comuna,direccion,telefono,correo,cli_usuariows,cli_clavews,cli_nombrews,cli_estadows,cuenta,rlegal,rrut, tipo_prestador)
        VALUES('" . $_REQUEST["rut"] . "','" . $_REQUEST["razonsocial"] . "','" . $_REQUEST["giro"] . "'," . $region . "," . $comuna . ",'" . $_REQUEST["direccion"] . "','" . $_REQUEST["telefono"] . "','" . $_REQUEST["correo"] . "','" . $_REQUEST["usuariows"] . "','" . $_REQUEST["clavews"] . "','" . $_REQUEST["nombrews"] . "',1,'" . $_REQUEST["cuenta"] . "','" . $_REQUEST["rlegal"] . "','" . $_REQUEST["rrut"] . "'," . $prestador . ")";
    $res = $link->query($sql);
    $idcliente = $link->insert_id;
    if (isset($_REQUEST["contactos"])) {
      $contactos = $_REQUEST["contactos"];
      foreach ($contactos as $valor) {
        $sep = explode("|", $valor);
        $sql1 = "insert into contactoclientes(cliente,nombre,telefono,correo,cargo)values('" . $idcliente . "','" . $sep[0] . "','" . $sep[1] . "','" . $sep[2] . "','" . $sep[3] . "')";
        $res1 = $link->query($sql1);
      }
    }

    $response = '';
    if ($res) {
      $response = 'OK';
    } else {
      $response = 'ERROR';
    }

    $sale_a = $_REQUEST["retornar"] . "&estado=" . $response;
    // $res['sql'] = $sql;
    // echo json_encode($res);
    break;

  case 'editarcliente':
    $region = 0;
    $comuna = 0;
    if ($_REQUEST['region'] == '') {
      $region = 0;
    } else {
      $region = $_REQUEST['region'];
    }
    if ($_REQUEST['comuna'] == '') {
      $comuna = 0;
    } else {
      $comuna = $_REQUEST['comuna'];
    }

    if (isset($_REQUEST["tipo_prestador"])) {
      if ($_REQUEST["tipo_prestador"] == '') {
        $prestador = "''";
      } else {
        $prestador = "'" . strtolower($_REQUEST["tipo_prestador"]) . "'";
      }
    } else {
      $prestador = "''";
    }

    $sql = "UPDATE clientes 
        SET rut='" . $_REQUEST["rut"] . "', cuenta='" . $_REQUEST["cuenta"] . "',
            razonsocial='" . $_REQUEST["razonsocial"] . "',giro='" . $_REQUEST["giro"] . "',region=" . $region . ",
            comuna=" . $comuna . ",direccion='" . $_REQUEST["direccion"] . "',telefono='" . $_REQUEST["telefono"] . "',
            correo='" . $_REQUEST["correo"] . "',cli_usuariows='" . $_REQUEST["usuariows"] . "',
            cli_clavews='" . $_REQUEST["clavews"] . "', cli_nombrews='" . $_REQUEST["nombrews"] . "', 
            rlegal='" . $_REQUEST["rlegal"] . "', rrut='" . $_REQUEST["rrut"] . "' , tipo_prestador=" . $prestador . "
        WHERE id=" . $_REQUEST["idcliente"] . "";
    $res = $link->query($sql);
    $contactos = $_REQUEST["contactos"];
    foreach ($contactos as $valor) {
      $sep = explode("|", $valor);
      $sql1 = "insert into contactoclientes(cliente,nombre,telefono,correo,cargo)values('" . $_REQUEST["idcliente"] . "','" . $sep[0] . "','" . $sep[1] . "','" . $sep[2] . "','" . $sep[3] . "')";
      $res1 = $link->query($sql1);
    }
    // echo $sql;
    // $_REQUEST["retornar"]='no';
    $sale_a = $_REQUEST["retornar"];
    break;

  case 'BorrarCliente':
    $sql0 = "UPDATE vehiculos set deleted_at=now() where veh_cliente='" . $_REQUEST["cliente"] . "' and deleted_at is NULL";
    //$sql0="delete from vehiculos where veh_cliente='".$_REQUEST["cliente"]."' and deleted_at is NULL";
    $res0 = $link->query($sql0);
    $sql = "delete from contactoclientes where cliente='" . $_REQUEST["cliente"] . "'";
    $res = $link->query($sql);
    //$sql1="delete from clientes where id='".$_REQUEST["cliente"]."'";
    $sql1 = "UPDATE clientes set deleted_at=now() where id='" . $_REQUEST["cliente"] . "' and deleted_at is NULL";
    $res1 = $link->query($sql1);
    echo "cliente eliminado";
    break;

  case 'ExisteCliente':
    $sql = "select COUNT(*) as total from clientes where rut='" . $_REQUEST["rut"] . "' and deleted_at is NULL ";
    $res = $link->query($sql);
    $fila = mysqli_fetch_array($res);
    echo $fila["total"];
    break;

  case 'getTabClientes':
    $sql = "SELECT * FROM clientes 
      WHERE deleted_at is NULL
      ORDER BY razonsocial,cuenta";
    $res = $link->query($sql);
    $datos = array();
    while ($fila = mysqli_fetch_array($res)) {
      $ncontactos = 0;
      $contactos = array();
      $sql1 = "select * from contactoclientes where cliente='" . $fila["id"] . "'";
      $res1 = $link->query($sql1);
      while ($fila1 = mysqli_fetch_array($res1)) {
        $ncontactos++;
        $contactos[$fila1["id"]] = array("nombre" => $fila1["nombre"], "telefono" => $fila1["telefono"], "correo" => $fila1["correo"], "cargo" => $fila1["cargo"]);
      }
      $datos[$fila["id"]] = array(
        "id" => $fila["id"],
        "rut" => $fila["rut"],
        "rrut" => $fila["rrut"],
        "rlegal" => $fila["rlegal"],
        "cuenta" => $fila["cuenta"],
        "razonsocial" => $fila["razonsocial"],
        "giro" => $fila["giro"],
        "region" => $fila["region"],
        "comuna" => $fila["comuna"],
        "direccion" => $fila["direccion"],
        "telefono" => $fila["telefono"],
        "correo" => $fila["correo"],
        "usuariows" => $fila["cli_usuariows"],
        "clavews" => $fila["cli_clavews"],
        "nombrews" => $fila["cli_nombrews"],
        "estadows" => $fila["cli_estadows"],
        "ncontactos" => $ncontactos,
        "tipo_prestador" => strtoupper($fila["tipo_prestador"]),
        "contactos" => $contactos
      );
    }
    echo json_encode($datos);
    break;

  case 'getvehiculoscliente':

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $recibe = json_decode($_REQUEST['envio'], true);

    $linkGen = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD');
    if (mysqli_connect_errno()) {
      printf("Falló la conexión: %s\n", mysqli_connect_error());
      exit();
    }
    $devuelve = array();
    $rs = $linkGen->query('SHOW DATABASES;');
    $optionvehiculos = '<option value="">Seleccione<option>';
    $devuelve = array();

    /*echo $recibe['opc'].'<br>';*/
    if ($recibe['opc'] == '') {
      while ($row = mysqli_fetch_array($rs)) {
        if ($row[0] != '' && $row[0] != null) {
          if (trim($row[0]) == strtolower($recibe['cliente'])) {

            $linkclient = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', strtolower($row[0]));
            if (mysqli_connect_errno()) {
              printf("Falló la conexión: %s\n", mysqli_connect_error());
              exit();
            }

            $sql = "SELECT 
                            t1.veh_id, 
                            t1.veh_patente, 
                            t2.ulp_odometrocan, 
                            t2.ulp_odolitroscan, 
                            t1.veh_tiposerv, 
                            t2.ulp_fechahora,
                            TIMESTAMPDIFF(DAY, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) AS dias_transcurridos
                        FROM 
                            vehiculos t1
                        INNER JOIN 
                            ultimaposicion t2 ON t2.ulp_idveh = t1.veh_id
                        LEFT OUTER JOIN 
                            tipo_vehiculo t3 ON t3.tveh_id = t1.veh_tipoveh
                        WHERE 
                            TIMESTAMPDIFF(HOUR, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) >= 96 and t1.deleted_at is NULL";
            $res = $linkclient->query($sql);

            if (mysqli_num_rows($res) > 0) {

              foreach ($res as $key => $ulp) {
                $optionvehiculos .= '<option value="' . $ulp['veh_patente'] . '">' . $ulp['veh_patente'] . '</option>';
              }
            }
          }
        }
      }
    } else if ($recibe['opc'] == 3) {

      $linkclient = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', strtolower($recibe['cliente']));
      if (mysqli_connect_errno()) {
        printf("Falló la conexión: %s\n", mysqli_connect_error());
        exit();
      }

      $sql = "SELECT * FROM  vehiculos WHERE veh_estado = 1 and veh_tiposerv != 0";
      $VehiculosBD = $linkclient->query($sql);

      $optionvehiculos = '<option value="">Seleccione Vehículos ' . $recibe['cliente'] . '<option>';

      $traza = array();

      $band = true;
      if ($VehiculosBD && mysqli_num_rows($VehiculosBD) > 0) {
        foreach ($VehiculosBD as $key => $ulp) {
          $optionvehiculos .= '<option value="' . $ulp['veh_patente'] . '">' . $ulp['veh_patente'] . '</option>';

          if ($band) {
            $band = false;

            // Hora y fecha de inicio del proceso
            $inicio = new DateTime();

            $rendimientoQuery = "-- Crear tabla temporal para almacenar los registros filtrados por fecha y vehículo
                  CREATE TEMPORARY TABLE temp_data AS
                  SELECT 
                      dgw_patente, 
                      dgw_odolitroscan, 
                      dgw_odometrocan, 
                      odocandiff,
                      ROW_NUMBER() OVER (ORDER BY dgw_fechahora ASC) AS row_asc,
                      ROW_NUMBER() OVER (ORDER BY dgw_fechahora DESC) AS row_desc
                  FROM 
                      datagetws
                  INNER JOIN 
                      vehiculos ON dgw_idveh = veh_id
                  WHERE 
                      dgw_idveh = " . $ulp['veh_id'] . "
                      AND dgw_fechahora BETWEEN '2024-04-25 00:00:00' AND '2024-04-25 23:59:59'
                      AND dgw_odometrocan > 0
                      AND dgw_odolitroscan > 0;
                  
                  -- Obtener el primer y último registro de la tabla temporal
                  SELECT 
                      dgw_patente, 
                      dgw_odolitroscan, 
                      dgw_odometrocan, 
                      odocandiff
                  FROM 
                      temp_data
                  WHERE
                      row_asc = 1 OR row_desc = 1;
                  
                  -- Eliminar la tabla temporal después de su uso
                  DROP TEMPORARY TABLE IF EXISTS temp_data;";
            $VehiculosRendBD = $linkclient->query($rendimientoQuery);

            $difodo = 0;
            $odomin = 0;
            $odomax = 0;
            $litrosmin = 0;
            $litrosmax = 0;
            $pat = '';
            $diffcan = 0;
            $fila = array();


            if ($VehiculosRendBD) {
              while ($row = mysqli_fetch_assoc($VehiculosRendBD)) {
                $fila[] = $row;
              }
              mysqli_free_result($VehiculosRendBD);
            }

            $grupo = '';
            foreach ($fila as $key => $value) {
              if ($key == 0) {
                $pat = $value["dgw_patente"];
                $odomin = $value["dgw_odometrocan"];
                $litrosmin = $value["dgw_odolitroscan"];
                $diffcan = $value["odocandiff"] < 1 ? 0 : $value["odocandiff"];
                $diffcan = preg_replace('/[^0-9]/', '', $diffcan);
              } elseif ($key == 1) {
                $odomax = $value["dgw_odometrocan"];
                $litrosmax = $value["dgw_odolitroscan"];
              }
            }

            $odomin = ((float)$odomin + (float)$diffcan);
            $odomax = ((float)$odomax + (float)$diffcan);

            /*$sql1="SELECT t2.gveh_nombre FROM vehiculos t1 INNER JOIN grupo_vehiculo t2 on t2.gveh_id = t1.veh_grupoveh where t1.veh_id = {$_REQUEST['npatente']};";
                  //$response['sql'][] = $sql1;
                  $res1=$link->query($sql1);
                  
                  if($res1 && mysqli_num_rows($res1)>0){
                      $fila1=mysqli_fetch_array($res1);
                      $grupo = $fila1['gveh_nombre'];
                      mysqli_free_result($res1);
                  }*/

            $difodo = ((float)$odomax - (float)$odomin);
            $diflts = ((float)$litrosmax - (float)$litrosmin);
            $rendimiento = 0;
            if ($difodo > 0 && $diflts > 0) {
              $rendimiento = round($difodo / $diflts, 2);
            }
            $grupo = '';

            if ($pat != '' && $pat != null) {
              $traza[] = array(
                'patente' => $pat,
                'grupo' => utf8_encode($grupo),
                'odometroi' => (float)$odomin,
                'odometrof' => (float)$odomax,
                'diferencia' => $difodo,
                'litros' => round($diflts, 1),
                'rendimiento' => $rendimiento,
              );
            }

            // Hora y fecha de finalización del proceso
            $fin = new DateTime();

            // Calcula la diferencia entre la hora de inicio y la hora de finalización
            $duracion = $inicio->diff($fin);

            if ($duracion !== null) {
              //echo $duracion->format('Y-m-d H:i:s');
              // Imprime la duración en un formato legible
              $tiempo =  "Duración del proceso: " . $duracion->format('%H horas, %i minutos y %s segundos');
            } else {
              //echo "La fecha es nula";
              // Imprime la duración en un formato legible
              $tiempo =  "La fecha es nula";
            }
          }
        }
      }



      $linkclient->close();

      //$optionvehiculos .= '<option value="'.$recibe['cliente'].'">'.$recibe['cliente'].'</option>';
      $devuelve['options'] = $optionvehiculos;
      $devuelve['traza']   = $traza;
      $devuelve['tiempo']   = $tiempo;
    } else {

      $sql = "SELECT * FROM sintranmision_can";
      $res = $link->query($sql);

      if (mysqli_num_rows($res) > 0) {
        foreach ($res as $key => $ulp) {
          $optionvehiculos .= '<option value="' . $ulp['stca_patente'] . '">' . $ulp['stca_patente'] . '</option>';
        }
      }
    }


    $devuelve['options'] = $optionvehiculos;
    echo json_encode($devuelve);
    break;

  case 'getTabClientessintrans':

    ini_set('max_execution_time', '420');
    ini_set('memory_limit', '512M');

    $linkGen = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD');
    if (mysqli_connect_errno()) {
      printf("Falló la conexión: %s\n", mysqli_connect_error());
      exit();
    }
    $devuelve = array();
    //echo json_encode($devuelve);return;
    $rs = $linkGen->query('SHOW DATABASES;');
    while ($row = mysqli_fetch_array($rs)) {
      if ($row[0] != '' && $row[0] != null) {
        if (trim($row[0]) != 'cloux' && trim($row[0]) != 'mysql' && trim($row[0]) != 'information_schema' && trim($row[0]) != 'performance_schema' && trim($row[0]) != 'prueba_data' && trim($row[0]) != 'copefrut') {

          $pasa = true;

          if ($_REQUEST['cliente'] != '') {
            if (strtolower($_REQUEST['cliente']) == strtolower($row[0])) {
              $pasa = true;
            } else {
              $pasa = false;
            }
          }

          if ($pasa) {
            $cantidad = 0;
            $linkclient = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', strtolower($row[0]));
            if (mysqli_connect_errno()) {
              printf("Falló la conexión: %s\n", mysqli_connect_error());
              exit();
            }

            $arravehsintran = array();

            $filtro = '';


            if ($_REQUEST['patente'] != '') {
              $filtro = ' and t1.veh_patente = "' . $_REQUEST['patente'] . '"';
            }

            if ($_REQUEST['tservicio'] != '') {
              if ($_REQUEST['tservicio'] == 1) {
                $filtro = ' and t1.veh_tiposerv = "1"';
              } else {
                $filtro = ' and t1.veh_tiposerv in (0,2)';
              }
            }

            if ($_REQUEST['dias'] != '') {
              $filtro = " AND TIMESTAMPDIFF(DAY, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) >= '" . $_REQUEST['dias'] . "' and t1.deleted_at is NULL";
            }

            $sql = "SELECT 
                                t1.veh_id, 
                                t1.veh_patente, 
                                t2.ulp_odometrocan, 
                                t2.ulp_odolitroscan, 
                                t1.veh_tiposerv, 
                                t2.ulp_fechahora,
                                t4.tra_rsocial,
                                t4.tra_alias,
                                t4.tra_rut,
                                t1.veh_seriegps,
                                TIMESTAMPDIFF(DAY, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) AS dias_transcurridos
                            FROM 
                                vehiculos t1
                            INNER JOIN 
                                ultimaposicion t2 ON t2.ulp_idveh = t1.veh_id
                            LEFT OUTER JOIN 
                                tipo_vehiculo t3 ON t3.tveh_id = t1.veh_tipoveh
                            LEFT OUTER JOIN 
                                transportistas t4 on t4.tra_id = t1.veh_rsocial
                            WHERE 
                                TIMESTAMPDIFF(HOUR, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) >= 96 {$filtro}
                                ";
            $res = $linkclient->query($sql);

            if ($res && mysqli_num_rows($res) > 0) {

              foreach ($res as $key => $ulp) {

                $sql2 = "SELECT * FROM historial_vehiculo WHERE his_patente = '{$ulp['veh_patente']}' ORDER BY his_fecha ASC";
                $res2 = $link->query($sql2);
                $comentarios = array();

                if (mysqli_num_rows($res2) > 0) {
                  foreach ($res2 as $key2 => $data2) {
                    $comentarios[] = array(
                      'comentario' => ($data2['his_comentario'] == '' || $data2['his_comentario'] == null ? '' : $data2['his_comentario']),
                      'fecha' => ($data2['his_fechaupd'] == '' || $data2['his_fechaupd'] == null ? '-' : $data2['his_fechaupd']),
                      'id' => $data2['id_his'],
                      'eliminado' => 0,
                    );
                  }
                }


                $sql2 = "SELECT * FROM estado_vehiculos where eve_patente = '{$ulp['veh_patente']}'";
                $res2 = $link->query($sql2);
                $filaestado = mysqli_fetch_array($res2);
                $estadoselectr = 0;
                if ($filaestado['eve_fun'] == 1) {
                  $estadoselectr = 1;
                } else if ($filaestado['eve_fun'] == 2) {
                  $estadoselectr = 2;
                }

                /*echo $ulp['tra_alias'].'<br>';*/
                $arravehsintran[] = array(
                  'idveh' => $ulp['veh_id'],
                  'patente' => $ulp['veh_patente'],
                  'dias' => $ulp['dias_transcurridos'],
                  'ulttransmision' => $ulp['ulp_fechahora'],
                  'ultodocan' => ($ulp['veh_tiposerv'] == 1 ? $ulp['ulp_odometrocan'] : 0),
                  'ultodolitro' => ($ulp['veh_tiposerv'] == 1 ? $ulp['ulp_odolitroscan'] : 0),
                  'tiposervicio' => ($ulp['veh_tiposerv'] == 1 ? 'Avanzado' : 'Básico'),
                  'transportistaalias' => ($ulp['tra_alias'] == null ||  $ulp['tra_alias'] == '' ? '' : utf8_encode($ulp['tra_alias'])),
                  'rstransportista' => ($ulp['tra_rsocial'] == null ||  $ulp['tra_rsocial'] == '' ? '' : utf8_encode($ulp['tra_rsocial'])),
                  'ruttransportista' => ($ulp['tra_rut'] == null ||  $ulp['tra_rut'] == '' ? '' : $ulp['tra_rut']),
                  'imei' => ($ulp['veh_seriegps'] == null ||  $ulp['veh_seriegps'] == '' ? '' : $ulp['veh_seriegps']),
                  'comentarios' => $comentarios,
                  'estadoselectr' => $estadoselectr,
                );
                $cantidad++;
              }
            }

            $devuelve[] = array(
              'cliente' => strtolower($row[0]),
              'cantidad' => $cantidad,
              'sintransmision' => $arravehsintran,
            );
          }
        }
      }
    }

    array_multisort(array_map(function ($element) {
      return $element['cantidad'];
    }, $devuelve), SORT_DESC, $devuelve);

    echo json_encode($devuelve);
    break;

  case 'getTabClientessintele':

    ini_set('max_execution_time', '420');
    ini_set('memory_limit', '512M');


    $fil1 = '  group by stca_cliente';
    if ($_REQUEST['cliente'] != '') {
      $fil1 = ' WHERE stca_cliente = "' . $_REQUEST['cliente'] . '"';
    }

    $fil2 = '';
    if ($_REQUEST['patente'] != '') {
      $fil2 = ' AND stca_patente = "' . $_REQUEST['patente'] . '"';
    }

    /*echo $recibe['patente'].'<---<br>';*/
    $sqlcli = "SELECT * FROM sintranmision_can {$fil1} {$fil2}";
    $rescli = $link->query($sqlcli);
    $devuelve["query"][] =  $sqlcli;
    //echo $sqlcli.'<br>';exit;

    if ($_REQUEST['datatable'] == false) {

      foreach ($rescli as $keyc => $cli) {
        $sql = "SELECT sc.* FROM sintranmision_can sc
                LEFT JOIN clientes as c on (sc.stca_cliente = c.cuenta) 
                WHERE stca_cliente='{$cli['stca_cliente']}' {$fil2}
                AND c.cli_estadows = 1
                ";
        $res = $link->query($sql);
        /* echo $sql.'<br>';*/
        $devuelve["query"][] =  $sql;
        $cantidad = 0;
        $arravehsintran = array();
        if ($res && mysqli_num_rows($res) > 0) {
          foreach ($res as $key => $ulp) {

            $sql2 = "SELECT * FROM comentarios_veh_sintelemetria WHERE cvst_patente = '{$ulp['stca_patente']}' ORDER BY cvst_fecha ASC";
            $res2 = $link->query($sql2);
            $comentarios = array();

            if (mysqli_num_rows($res2) > 0) {
              foreach ($res2 as $key2 => $data2) {
                $comentarios[] = array(
                  'comentario' => ($data2['cvst_comentario'] == '' || $data2['cvst_comentario'] == null ? '' : $data2['cvst_comentario']),
                  'fecha' => ($data2['cvst_fechaupd'] == '' || $data2['cvst_fechaupd'] == null ? '-' : $data2['cvst_fechaupd']),
                  'id' => $data2['cvst_id'],
                  'eliminado' => 0,
                );
              }
            }


            $sql2 = "SELECT * FROM estado_vehiculos where eve_patente = '{$ulp['stca_patente']}'";
            $res2 = $link->query($sql2);
            $filaestado = mysqli_fetch_array($res2);
            $estadoselectr = 0;
            if ($filaestado['eve_estadotelemetria'] == 1) {
              $estadoselectr = 1;
            } else if ($filaestado['eve_estadotelemetria'] == 2) {
              $estadoselectr = 2;
            }


            $arravehsintran[] = array(
              'patente' => $ulp['stca_patente'],
              'cliente' => $ulp['stca_cliente'],
              'rs' => ($ulp['stca_rs'] == '' || $ulp['stca_rs'] == 'null' || $ulp['stca_rs'] == null ? '-' : $ulp['stca_rs']),
              'odo' => ($ulp['stca_odometro'] == '' || $ulp['stca_odometro'] == 'null' || $ulp['stca_odometro'] == null ? '-' : $ulp['stca_odometro']),
              'lts' => ($ulp['stca_odolitro'] == '' || $ulp['stca_odolitro'] == 'null' || $ulp['stca_odolitro'] == null ? '-' : $ulp['stca_odolitro']),
              'maloodo' => ($ulp['stca_problemaodo'] == 0 ? '' : 'X'),
              'malolts' => ($ulp['stca_problemaodolts'] == 0 ? '' : 'X'),
              'kms' => $ulp['stca_kms'],
              'comentarios' => $comentarios,
              'estadoselectr' => $estadoselectr,
            );
            $cantidad++;
          }
        }

        $devuelve[] = array(
          'cliente' => $cli['stca_cliente'],
          'cantidad' => $cantidad,
          'sintransmision' => $arravehsintran,
        );
      }

      array_multisort(array_map(function ($element) {
        return $element['cantidad'];
      }, $devuelve), SORT_DESC, $devuelve);
    } else {

      $data   = [];
      $draw   = $_REQUEST["draw"];
      $start  = $_REQUEST["start"];
      $length = $_REQUEST["length"];
      $search = trim($_REQUEST["search"]["value"]);

      //$columnOrder   = $_REQUEST['order'][0]['column']; // Suponiendo que 'column' es el parámetro que contiene el nombre de la columna por la cual quieres ordenar
      //$dirOrden      = $_REQUEST['order'][0]['dir']; // Suponiendo que 'dir' es el parámetro que contiene la dirección del ordenamiento (ASC o DESC)
      //$columna_orden = $columnas[$columnOrder];

      // Filtramos la consulta por la búsqueda
      if ($search) {
        $sqlFiltroSearch = " AND (stca_cliente LIKE '%$search%' OR stca_patente LIKE '%$search%' OR stca_rs LIKE '%$search%') ";
      }

      $fil1 = ' ';
      if ($_REQUEST['cliente'] != '') {
        //$fil1 = ' WHERE stca_cliente = "'.$_REQUEST['cliente'].'"';
        $fil1 = ' AND c.cuenta = "' . $_REQUEST['cliente'] . '"';
      }

      $fil2 = '';
      if ($_REQUEST['patente'] != '') {
        $fil2 = ' AND stca_patente = "' . $_REQUEST['patente'] . '"';
      }

      $queryBase = "SELECT sc.* FROM sintranmision_can sc 
                    LEFT JOIN clientes AS c ON (sc.stca_cliente = c.cli_nombrews) 
                    -- LEFT JOIN vehiculos AS v ON (sc.stca_patente = v.veh_patente) 
                    WHERE c.cli_estadows = 1 
                    -- AND v.deleted_at is NULL 
                    {$fil1} {$fil2} {$sqlFiltroSearch}
                    ORDER BY stca_cliente 
                ";



      $query = $queryBase;
      // Limitamos la consulta
      $query .= " LIMIT $start, $length";

      $res = $link->query($query);
      /* echo $sql.'<br>';*/
      $cantidad = $start;
      $arrayData = [];

      $cantidadgestionados = 0;
      $cantidadpendientes  = 0;

      $sql[] = $query;
      if ($res && mysqli_num_rows($res) > 0) {
        foreach ($res as $key => $ulp) {

          $sql2 = "SELECT * FROM comentarios_veh_sintelemetria WHERE cvst_patente = '{$ulp['stca_patente']}' ORDER BY cvst_fecha ASC";
          $res2 = $link->query($sql2);
          $comentarios = array();

          if (mysqli_num_rows($res2) > 0) {
            foreach ($res2 as $key2 => $data2) {
              $comentarios[] = array(
                'comentario' => ($data2['cvst_comentario'] == '' || $data2['cvst_comentario'] == null ? '' : $data2['cvst_comentario']),
                'fecha' => ($data2['cvst_fechaupd'] == '' || $data2['cvst_fechaupd'] == null ? '-' : $data2['cvst_fechaupd']),
                'id' => $data2['cvst_id'],
                'eliminado' => 0,
              );
            }
          }


          $sql2 = "SELECT * FROM estado_vehiculos where eve_patente = '{$ulp['stca_patente']}'";
          $res2 = $link->query($sql2);
          $filaestado = mysqli_fetch_array($res2);
          $estadoselectr = 0;
          if ($filaestado['eve_estadotelemetria'] == 1) {
            $estadoselectr = 1;
          } else if ($filaestado['eve_estadotelemetria'] == 2) {
            $estadoselectr = 2;
          }

          $arravehsintran[$ulp['stca_cliente']][] = array(
            'patente' => $ulp['stca_patente'],
            'cliente' => $ulp['stca_cliente'],
            'rs' => ($ulp['stca_rs'] == '' || $ulp['stca_rs'] == 'null' || $ulp['stca_rs'] == null ? '-' : $ulp['stca_rs']),
            'odo' => ($ulp['stca_odometro'] == '' || $ulp['stca_odometro'] == 'null' || $ulp['stca_odometro'] == null ? '-' : $ulp['stca_odometro']),
            'lts' => ($ulp['stca_odolitro'] == '' || $ulp['stca_odolitro'] == 'null' || $ulp['stca_odolitro'] == null ? '-' : $ulp['stca_odolitro']),
            'maloodo' => ($ulp['stca_problemaodo'] == 0 ? '' : 'X'),
            'malolts' => ($ulp['stca_problemaodolts'] == 0 ? '' : 'X'),
            'kms' => $ulp['stca_kms'],
            'comentarios' => $comentarios,
            'estadoselectr' => $estadoselectr,
          );

          $stca_patente = $ulp['stca_patente'];
          $stca_patente = str_replace("(", "_", $stca_patente);
          $stca_patente = str_replace(")", "-", $stca_patente);
          $stca_patente = str_replace("/", "á", $stca_patente);
          $patenteEditada = str_replace(" ", "", $stca_patente);

          $cliente = $ulp['stca_cliente'];

          $disa = 'disabled';
          if (count($comentarios)) {
            $disa = '';
            $cantidadgestionados++;
          } else {
            $cantidadpendientes++;
            $disa = 'disabled';
          }



          $comentarioSpan = "<td>
                                <div class='row'>
                                  <div class='col-md-10'>
                                    <textarea class='form-control' id='comentario2_" . $patenteEditada . "'></textarea>
                                  </div>
                                  <div class='col-md-2'>
                                    <button type='button' class='btn btn-sm btn-success' onclick='upddetras2(\"" . $patenteEditada . "\",\"" . $cliente . "\")'><i class='fas fa-plus'></i></button> 
                                    <button id='eye2_" . $patenteEditada . "' type='button' class='btn btn-sm btn-info' onclick='mostrarcomentarios2(\"" . $patenteEditada . "\",\"" . $cliente . "\")' " . $disa . "><i class='fas fa-eye'></i></button>
                                  </div>
                                </div>
                              </td>";


          $fechaActual = new DateTime();

          // Obtener los componentes de la fecha
          $año = $fechaActual->format('Y');
          $mes = $fechaActual->format('m');
          $dia = $fechaActual->format('d');
          $hora = $fechaActual->format('H');
          $minutos = $fechaActual->format('i');

          // Formatear la fecha en el formato deseado
          $fechaFormateada = $año . '-' . $mes . '-' . $dia . ' ' . $hora . ':' . $minutos;


          $fechaSpan = "<td style='text-align:center;'>
                            <input type='datetime-local' id='fecha2_" . $patenteEditada . "' 
                              class='form-control-sm' value='" . $fechaFormateada . "'>
                            </input>
                          </td>";


          $dias = 0;
          $comentarioultimo = '';

          // Verificar si hay comentarios
          if (count($comentarios) > 0) {
            // Obtener la fecha del último comentario
            $fecha1 = $comentarios[count($comentarios) - 1]['fecha'];

            // Convertir la fecha proporcionada a un objeto DateTime
            $fechaProporcionada = new DateTime($fecha1);

            // Obtener la fecha actual
            $fechaActual = new DateTime();

            // Calcular la diferencia en días
            $diferenciaDias = $fechaActual->diff($fechaProporcionada)->days;

            // Redondear la diferencia de días
            $dias = round($diferenciaDias);

            // Obtener el último comentario
            $ultimoComentario = $comentarios[count($comentarios) - 1];
            $comentarioultimo = $ultimoComentario['comentario'] . ' / ' . $ultimoComentario['fecha'];
          }
          $ultComentario = "<td style='text-align:center;' 
                                id='ult_comentario2" . $patenteEditada . "'>" . $comentarioultimo . "
                              </td>";


          $diasGestion = "<td style='text-align:center;' id='ult_dias2" . $patenteEditada . "'>" . $dias . "</td>";


          $selestado1 = '';
          $selestado2 = '';
          $selestado3 = '';

          if ($estadoselectr == 0) {
            $selestado1 = 'selected';
            $selestado2 = '';
            $selestado3 = '';
          } else if (item . estadoselectr == 1) {
            $selestado1 = '';
            $selestado2 = 'selected';
            $selestado3 = '';
          } else {
            $selestado1 = '';
            $selestado2 = '';
            $selestado3 = 'selected';
          }

          $estado = "<td style='text-align:center;'>
                        <select id='estado2_" . $patenteEditada . "' class='estado2' 
                          onchange='updestado2(\"" . $patenteEditada . "\",\"" . $cliente . "\")'>
                          <option value='0' `" . $selestado1 . "`>No operativo</option>
                          <option value='1' `" . $selestado2 . "`>Operativo</option>
                          <option value='2' `" . $selestado3 . "`>Desinstalar</option>
                        </select>
                      </td>";



          $arrayData[] = array(
            'contador' => ($cantidad + 1),
            'patente' => $ulp['stca_patente'],
            'cliente' => $ulp['stca_cliente'],
            'rs' => ($ulp['stca_rs'] == '' || $ulp['stca_rs'] == 'null' || $ulp['stca_rs'] == null ? '-' : $ulp['stca_rs']),
            'odo' => ($ulp['stca_odometro'] == '' || $ulp['stca_odometro'] == 'null' || $ulp['stca_odometro'] == null ? '-' : $ulp['stca_odometro']),
            'lts' => ($ulp['stca_odolitro'] == '' || $ulp['stca_odolitro'] == 'null' || $ulp['stca_odolitro'] == null ? '-' : $ulp['stca_odolitro']),
            'maloodo' => ($ulp['stca_problemaodo'] == 0 ? '' : 'X'),
            'malolts' => ($ulp['stca_problemaodolts'] == 0 ? '' : 'X'),
            'kms' => $ulp['stca_kms'],
            'comentarios' => $comentarios,
            'estadoselectr' => $estadoselectr,
            'comentario' => $comentarioSpan,
            'fecha_hora' => $fechaSpan,
            'ultimo_comentario' => $ultComentario,
            'dias_gestion' => $diasGestion,
            'estado' => $estado,
            'sql' => $sql


          );


          $cantidad++;
        }

        foreach ($arravehsintran as $key => $value) {
          $devuelve[] = array(
            'cliente' => $key,
            'cantidad' => count($arravehsintran[$key]),
            'sintransmision' => $arravehsintran[$key],
          );
        }

        array_multisort(array_map(function ($element) {
          return $element['cantidad'];
        }, $devuelve), SORT_DESC, $devuelve);


        $totalData = "SELECT COUNT(*) as contador FROM sintranmision_can sc 
                    LEFT JOIN clientes AS c ON (sc.stca_cliente = c.cli_nombrews) 
                    WHERE c.cli_estadows = 1 {$fil1}
                    ORDER BY stca_cliente ";

        $resData = $link->query($totalData);
        $total = mysqli_fetch_array($resData)['contador'];
        $dataReturn['recordsTotal'] = $total;
        $dataReturn['recordsFiltered'] = $total;

        // Formatear los datos para que DataTables los entienda
        $devuelve = array(
          "draw" => $draw,
          "recordsTotal" => $total,
          "recordsFiltered" => $total,
          "data" => $arrayData,
          "gestionados" => $cantidadgestionados,
          "pendientes" => $cantidadpendientes,
        );
      }
    }

    echo json_encode($devuelve);
    break;

  case 'delcoment':
    $recibe = json_decode($_REQUEST['envio'], true);

    $sql = "DELETE FROM historial_vehiculo WHERE id_his = '{$recibe['id']}'";
    $res = $link->query($sql);
    $datos = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');
    if ($res) {
      $datos = array('respuesta' => 'success', 'mensaje' => 'Borrado correctamente');
    }

    echo json_encode($datos);
    break;

  case 'delcoment2':
    $recibe = json_decode($_REQUEST['envio'], true);

    $sql = "DELETE FROM comentarios_veh_sintelemetria WHERE cvst_id = '{$recibe['id']}'";
    $res = $link->query($sql);
    $datos = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');
    if ($res) {
      $datos = array('respuesta' => 'success', 'mensaje' => 'Borrado correctamente');
    }

    echo json_encode($datos);
    break;

  case 'updestado':

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $recibe = json_decode($_REQUEST['envio'], true);

    $patente1 = str_replace("_", "(", $recibe['patente']);
    $patente = str_replace("-", ")", $patente1);
    $recibe['patente'] = str_replace("á", "/", $patente);

    $sql2 = "SELECT * from usuarios usu 
            left outer join tipo_usuario tusu on tusu.tusu_id=usu.usu_perfil 
            where usu.usu_id={$_SESSION["cloux_new"]}";
    $res2 = $link->query($sql2);
    $dusuario = mysqli_fetch_array($res2);

    $sql = "SELECT * FROM estado_vehiculos WHERE eve_patente = '" . $recibe['patente'] . "' AND eve_cliente='" . $recibe['cliente'] . "'";
    $res = $link->query($sql);
    if (mysqli_num_rows($res) > 0) {
      $sql1 = "UPDATE estado_vehiculos 
                SET eve_fun='{$recibe['estado']}' 
                WHERE eve_patente = '{$recibe['patente']}' and eve_cliente = '{$recibe['cliente']}'";
      $res1 = $link->query($sql1);
    } else {
      $sql1 = "INSERT INTO estado_vehiculos (eve_patente,eve_cliente,eve_fun,eve_fh,eve_nombreusu,eve_estado,eve_tservicio) 
                  VALUES ('{$recibe['patente']}','{$recibe['cliente']}','{$recibe['estado']}','{$fechachile}','{$dusuario['usu_nombre']}',1,'{$recibe['tservicio']}')";
      $res1 = $link->query($sql1);
    }

    $datos = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');
    if ($res) {

      $datos = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error  1');
      $idcliente = 0;
      $idvehiculo = 0;
      $sql3 = "SELECT * FROM clientes 
                  where cuenta = '" . strtolower($recibe['cliente']) . "' and razonsocial = '{$recibe['rs']}' limit 1";
      $res3 = $link->query($sql3);
      if (mysqli_num_rows($res3) > 0) {
        $fila3 = mysqli_fetch_array($res3);
        $idcliente = $fila3['id'];
      } else {
        $sql4 = "INSERT INTO clientes (rut,razonsocial,giro,region,comuna,direccion,telefono,correo,cli_usuariows,cli_clavews,cli_nombrews,cli_estadows,cuenta,rlegal,rrut) 
                      VALUES ('{$recibe['rut']}','{$recibe['rs']}','0','0','0','','','','ws','ws','" . strtolower($recibe['cliente']) . "',1,'" . strtolower($recibe['cliente']) . "','','')";
        $res4 = $link->query($sql4);
        $idcliente = $link->insert_id;
      }

      $sql3 = "SELECT * FROM vehiculos 
                  where veh_patente = '{$recibe['patente']}' and deleted_at is NULL";
      $res3 = $link->query($sql3);
      if ($res3 && mysqli_num_rows($res3) > 0) {
        $fila3 = mysqli_fetch_array($res3);
        $idvehiculo = $fila3['veh_id'];
      } else {
        $sql4 = "INSERT INTO vehiculos(veh_idflotasnet, veh_tipo, veh_gps, veh_cliente, veh_rsocial, veh_grupo, veh_patente, veh_imei, veh_dispositivo, veh_tservicio) 
                      VALUES (0,0,0,'{$idcliente}','{$idcliente}',0,'{$recibe['patente']}','{$recibe['imei']}',0,'{$recibe['tservicio']}')";
        $res4 = $link->query($sql4);
        $idvehiculo = $link->insert_id;
      }

      $datos = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error  ' . $idvehiculo);
      if ($idvehiculo > 0 && $idcliente > 0) {
        $tiposerv = 0;
        if ($recibe['tservicio'] == 1) {
          $tiposerv = 2;
        } else if ($recibe['tservicio'] == 0) {
          $tiposerv = 1;
        } else {
          $tiposerv = 3;
        }

        $descripcion = '';
        $sql5 = "SELECT * FROM historial_vehiculo where his_patente = '{$recibe['patente']}' order by 1 desc limit 1";
        $res5 = $link->query($sql5);
        if ($res5 && mysqli_num_rows($res5) > 0) {
          $fila5 = mysqli_fetch_array($res5);
          $descripcion = $fila5['his_comentario'];
        }

        if ($recibe['estado'] == 1) {
          $sql5 = "INSERT INTO tickets(tic_fechahorareg, tic_cliente, tic_patente, tic_tipotrabajo, tic_tiposervicio, tic_descripcion, tic_estado) 
                          VALUES ('{$fechachile}','{$idcliente}','{$idvehiculo}','1','{$tiposerv}','{$descripcion}',1)";
          $res5 = $link->query($sql5);
          $datos = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error  ' . $idvehiculo, 'query' => $sql5);
        } else if ($recibe['estado'] == 2) {
          $sql5 = "INSERT INTO tickets(tic_fechahorareg, tic_cliente, tic_patente, tic_tipotrabajo, tic_tiposervicio, tic_descripcion, tic_estado) 
                          VALUES ('{$fechachile}','{$idcliente}','{$idvehiculo}','3','{$tiposerv}','{$descripcion}',1)";
          $res5 = $link->query($sql5);
        }

        if ($res5) {
          $datos = array('respuesta' => 'success', 'mensaje' => 'Ticket creado correctamente');
        }
      }
    }

    echo json_encode($datos);
    break;

  case 'updestado2':
    $recibe = json_decode($_REQUEST['envio'], true);

    $sql2 = "select * from usuarios usu left outer join tipo_usuario tusu on tusu.tusu_id=usu.usu_perfil where usu.usu_id={$_SESSION["cloux_new"]}";
    $res2 = $link->query($sql2);
    $dusuario = mysqli_fetch_array($res2);

    $sql = "SELECT * FROM estado_vehiculos WHERE eve_patente = '" . $recibe['patente'] . "' AND eve_cliente='" . $recibe['cliente'] . "'";
    $res = $link->query($sql);
    if (mysqli_num_rows($res) > 0) {
      $sql1 = "UPDATE estado_vehiculos SET eve_estadotelemetria='{$recibe['estado']}' WHERE eve_patente = '{$recibe['patente']}' and eve_cliente = '{$recibe['cliente']}'";
      $res1 = $link->query($sql1);
    } else {
      $sql1 = "INSERT INTO estado_vehiculos (eve_patente,eve_cliente,eve_estadotelemetria,eve_fh,eve_nombreusu,eve_estado,eve_tservicio) VALUES ('{$recibe['patente']}','{$recibe['cliente']}','{$recibe['estado']}','{$fechachile}','{$dusuario['usu_nombre']}',1,'{$recibe['tservicio']}')";
      $res1 = $link->query($sql1);
    }

    $datos = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');
    if ($res) {

      $idcliente = 0;
      $idvehiculo = 0;
      $sql3 = "SELECT * FROM clientes where cuenta = '" . strtolower($recibe['cliente']) . "' and razonsocial = '{$recibe['rs']}' limit 1";
      $res3 = $link->query($sql3);
      if (mysqli_num_rows($res3) > 0) {
        $fila3 = mysqli_fetch_array($res3);
        $idcliente = $fila3['id'];
      } else {
        $sql4 = "INSERT INTO clientes (rut,razonsocial,giro,region,comuna,direccion,telefono,correo,cli_usuariows,cli_clavews,cli_nombrews,cli_estadows,cuenta,rlegal,rrut) VALUES ('{$recibe['rut']}','{$recibe['rs']}','0','0','0','','','','ws','ws','" . strtolower($recibe['cliente']) . "',1,'" . strtolower($recibe['cliente']) . "','','')";
        $res4 = $link->query($sql4);
        $idcliente = $link->insert_id;
      }

      $sql3 = "SELECT * FROM vehiculos where veh_patente = '{$recibe['patente']}' and deleted_at is NULL";
      $res3 = $link->query($sql3);
      if (mysqli_num_rows($res3) > 0) {
        $fila3 = mysqli_fetch_array($res3);
        $idvehiculo = $fila3['veh_id'];
      } else {
        $sql4 = "INSERT INTO vehiculos(veh_idflotasnet, veh_tipo, veh_gps, veh_cliente, veh_rsocial, veh_grupo, veh_patente, veh_imei, veh_dispositivo, veh_tservicio) VALUES (0,0,0,'{$idcliente}','{$idcliente}',0,'{$recibe['patente']}','{$recibe['imei']}',0,'{$recibe['tservicio']}')";
        $res4 = $link->query($sql4);
        $idvehiculo = $link->insert_id;
      }

      if ($idvehiculo > 0 && $idcliente > 0) {
        $tiposerv = 0;
        if ($recibe['tservicio'] == 1) {
          $tiposerv = 2;
        } else if ($recibe['tservicio'] == 0) {
          $tiposerv = 1;
        } else {
          $tiposerv = 3;
        }

        $descripcion = '';
        $sql5 = "SELECT * FROM comentarios_veh_sintelemetria where cvst_patente = '{$recibe['patente']}' order by 1 desc limit 1";
        $res5 = $link->query($sql5);
        if (mysqli_num_rows($res5) > 0) {
          $fila5 = mysqli_fetch_array($res5);
          $descripcion = $fila5['cvst_comentario'];
        }

        if ($recibe['estado'] == 1) {
          $sql5 = "INSERT INTO tickets(tic_fechahorareg, tic_cliente, tic_patente, tic_tipotrabajo, tic_tiposervicio, tic_descripcion, tic_estado) VALUES ('{$fechachile}','{$idcliente}','{$idvehiculo}','1',2,'SIN TRANSMISION CAN',1)";
          $res5 = $link->query($sql5);
        } else if ($recibe['estado'] == 2) {
          $sql5 = "INSERT INTO tickets(tic_fechahorareg, tic_cliente, tic_patente, tic_tipotrabajo, tic_tiposervicio, tic_descripcion, tic_estado) VALUES ('{$fechachile}','{$idcliente}','{$idvehiculo}','3',2,'SIN TRANSMISION CAN',1)";
          $res5 = $link->query($sql5);
        }

        if ($res5) {
          $datos = array('respuesta' => 'success', 'mensaje' => 'Ticket creado correctamente');
        }
      }
    }

    echo json_encode($datos);
    break;

  case 'editcoment':
    $recibe = json_decode($_REQUEST['envio'], true);

    $sql = "UPDATE historial_vehiculo SET his_comentario = '{$recibe['comentario']}', his_fecha = '{$recibe['fecha']}' WHERE id_his = '{$recibe['id']}'";
    $res = $link->query($sql);
    /*echo $sql.'<br>';*/
    $datos = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');
    if ($res) {
      $datos = array('respuesta' => 'success', 'mensaje' => 'Actualizado correctamente');
    }

    echo json_encode($datos);
    break;

  case 'editcoment2':
    $recibe = json_decode($_REQUEST['envio'], true);

    $sql = "UPDATE comentarios_veh_sintelemetria SET cvst_comentario = '{$recibe['comentario']}', cvst_fecha = '{$recibe['fecha']}' WHERE cvst_id = '{$recibe['id']}'";
    $res = $link->query($sql);
    /*echo $sql.'<br>';*/
    $datos = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');
    if ($res) {
      $datos = array('respuesta' => 'success', 'mensaje' => 'Actualizado correctamente');
    }

    echo json_encode($datos);
    break;

  case 'getTabClientesbloq':
    $sql = "SELECT * from clientes GROUP BY cuenta";
    $res = $link->query($sql);
    $datos = array();
    if ($res && mysqli_num_rows($res) > 0) {
      foreach ($res as $key => $clien) {

        $sql1 = "SELECT * FROM usuarios_bloqueado 
                      WHERE usbl_estado = 0 
                        and usbl_cuenta = '" . strtolower($clien['cuenta']) . "'";
        $res1 = $link->query($sql1);
        $estado = 0;

        $moroso = 0; // Valor por defecto
        if ($res1) {
          while ($row1 = mysqli_fetch_assoc($res1)) {
            if ($row1['usbl_moroso'] == 1) {
              $moroso = 1; // Cambiar a 1 si al menos un usuario es moroso
              break; // Salir del bucle si encontramos un usuario moroso
            }
          }

          $datos[] = array(
            'numero' => ($key + 1),
            'cliente' => $clien['cuenta'],
            'razonsocial' => $clien['razonsocial'],
            'numerobloqueados' => mysqli_num_rows($res1),
            'estado' => $estado,
            'moroso' => $moroso
          );
        }
      }
    }
    echo json_encode($datos);
    break;

  case 'cargacliente':
    $recibe = json_decode($_REQUEST['envio'], true);
    $datos  = array();
    $linkcliente = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', strtolower($recibe['cliente']));
    mysqli_set_charset($linkcliente, "utf8");
    $sql = "SELECT * FROM usuarios where usu_estado = 1";
    $res = $linkcliente->query($sql);
    $todos = 1;

    if ($res && mysqli_num_rows($res) > 0) {
      foreach ($res as $key => $usu) {
        $estado = 1;
        $sql1 = "SELECT * FROM usuarios_bloqueado 
                      WHERE usbl_nombre = '{$usu['usu_usuario']}' 
                      and usbl_iduser = {$usu['usu_id']} 
                      and usbl_cuenta = '" . strtolower($recibe['cliente']) . "'";
        $res1 = $link->query($sql1);
        //echo $sql1;


        if ($res1 && mysqli_num_rows($res1) > 0) {
          $fila1 = mysqli_fetch_array($res1);
          $estado = (int)$fila1['usbl_estado'];
          if ($estado == 0) {
            $todos = 0;
          }
        }


        $datos[] = array(
          'usu_id' => $usu['usu_id'],
          'usu_usuario' => $usu['usu_usuario'],
          'cliente' => $recibe['cliente'],
          'estado' => $estado,
          'todos' => $todos
        );
      }
    }
    echo json_encode($datos);

    break;

  case 'guardach':
    $recibe = json_decode($_REQUEST['envio'], true);
    //print_r($recibe);
    $datos  = array();
    $devuelve = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');

    $linkcliente = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', strtolower($_REQUEST['cliente']));
    mysqli_set_charset($linkcliente, "utf8");

    foreach ($recibe as $key => $data) {
      $sql = "SELECT * FROM usuarios_bloqueado 
                WHERE usbl_cuenta = '" . strtolower($data['clien']) . "' and usbl_iduser = {$data['idusu']} and usbl_nombre = '" . $data['nombreusu'] . "'";
      $res = $link->query($sql);

      $fila1 = mysqli_fetch_array($res);
      $claveoriginal = $fila1['usbl_clave'];
      if ($fila1['usbl_clave'] == null || $fila1['usbl_clave'] == "") {
        $sql2 = "SELECT * FROM usuarios where usu_id = {$data['idusu']}";
        $res2 = $linkcliente->query($sql2);
        foreach ($res2 as $key2 => $us2) {
          $claveoriginal = $us2['usu_claveoriginal'];
        }
      }

      if (mysqli_num_rows($res) > 0) {

        $sql1 = "UPDATE usuarios_bloqueado SET usbl_estado = {$data['estado']}, usbl_clave='{$claveoriginal}', usbl_moroso='1' WHERE usbl_iduser = {$data['idusu']} and usbl_cuenta = '" . strtolower($data['clien']) . "' and usbl_nombre = '" . $data['nombreusu'] . "'";
        $res1 = $link->query($sql1);
      } else {
        $sql1 = "INSERT INTO usuarios_bloqueado (usbl_cuenta,usbl_iduser,usbl_nombre,usbl_estado,usbl_clave,usbl_moroso) VALUES ('{$data['clien']}','{$data['idusu']}','{$data['nombreusu']}','{$data['estado']}','{$claveoriginal}','1')";
        $res1 = $link->query($sql1);
      }

      if ($data['estado'] == 0) {
        $sql2 = "UPDATE usuarios SET usu_claveoriginal = 'XX1XXBLOQ' where usu_id = {$data['idusu']}";
        $res2 = $linkcliente->query($sql2);

        $sqlmoroso = "SELECT * from usuarios where usu_id = {$data['idusu']}";
        $resmoroso = $linkcliente->query($sqlmoroso);
        $correomoroso = '';
        $nombreMor = '';
        foreach ($resmoroso as $keymor => $cormor) {
          $correomoroso = $cormor['usu_correo'];
          $nombreMor = $cormor['usu_nombre'];
        }
        if ($correomoroso != '') {
          enviaremailMoroso($correomoroso, $nombreMor);
        }
      } else {
        $sql2 = "UPDATE usuarios SET usu_claveoriginal = '{$claveoriginal}' where usu_id = {$data['idusu']}";
        $res2 = $linkcliente->query($sql2);
        //echo $sql2;
        $sql1 = "UPDATE usuarios_bloqueado SET usbl_clave = '',usbl_moroso = '0' WHERE usbl_iduser = {$data['idusu']} and usbl_cuenta = '" . strtolower($data['clien']) . "' and usbl_nombre = '" . $data['nombreusu'] . "'";
        $res1 = $link->query($sql1);
        //echo $sql1;

      }
    }

    if ($res1) {
      $devuelve = array('respuesta' => 'success', 'mensaje' => 'Guardado correctamente');
    }
    echo json_encode($devuelve);

    break;

  case 'nuevocontactocliente':
    $sql = "insert into contactoclientes(cliente,nombre,telefono,correo,cargo)values('" . $_REQUEST["cliente"] . "','" . $_REQUEST["nombre"] . "','" . $_REQUEST["telefono"] . "','" . $_REQUEST["correo"] . "','" . $_REQUEST["cargo"] . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;

  case 'getContactos':
    $sql = "select * from contactoclientes where cliente='" . $_REQUEST["cliente"] . "'";
    $res = $link->query($sql);
    $opciones = "";
    while ($fila = mysqli_fetch_array($res)) {
      $opciones .= "<option value='" . $fila["id"] . "'>" . $fila["nombre"] . "</option>";
    }
    echo $opciones;
    break;

  case 'getTabContactos':
    $sql = "select * from contactoclientes";
    $res = $link->query($sql);
    $datos = array();
    while ($fila = mysqli_fetch_array($res)) {
      $datos[$fila["id"]] = array("idcliente" => $fila["cliente"], "id" => $fila["id"], "nombre" => $fila["nombre"], "telefono" => $fila["telefono"], "correo" => $fila["correo"], "cargo" => $fila["cargo"], "cliente" => obtenervalor("clientes", "razonsocial", "where id=" . $fila["cliente"] . ""));
    }
    echo json_encode($datos);
    break;

  case 'BorrarContacto':
    $sql = "delete from contactoclientes where id='" . $_REQUEST["contacto"] . "'";
    $res = $link->query($sql);
    echo "contacto eliminado";
    break;

    /***************************************
OPERACIONES VEHICULOS
     ****************************************/

  case 'getTabVehiculos':

    $start = 0;
    if (isset($_REQUEST['idpatente'])) {
      $sql = "select veh.*,cli.*,gru.*,tveh.*, CONCAT(reg.ordinal,' ',reg.region)region, com.comuna_nombre comuna , t7.tic_tipotrabajo, t7.tic_tiposervicio
      from vehiculos veh 
      left outer join clientes cli on cli.id = veh.veh_cliente 
      left outer join grupos gru on veh.veh_grupo = gru.gru_id 
      left outer join tiposdevehiculos tveh on veh.veh_tipo = tveh.tveh_id 
      LEFT OUTER JOIN regiones reg ON veh.veh_region = reg.id 
      LEFT OUTER JOIN comunas com ON com.comuna_id = veh.veh_comuna 
      LEFT OUTER JOIN tickets t7 ON t7.tic_patente = veh.veh_id and t7.tic_estado = 2
      WHERE veh.veh_id = {$_REQUEST['idpatente']} ";

      //        if(isset($_REQUEST['idpatente'])){
      //            $sql.=" where veh.veh_id = {$_REQUEST['idpatente']}";
      //        }
      //        WHERE veh.veh_id = {$_REQUEST['idpatente']}
    } else {
      $columnas = array('id_cont', 'veh_estado', 'tveh_nombre', 'veh_tservicio', 'cuenta', 'veh_patente', 'razonsocial', 'veh_imei', 'veh_sim');

      $data   = [];
      $draw   = $_REQUEST["draw"];
      $start  = $_REQUEST["start"];
      $length = $_REQUEST["length"];
      $search = trim($_REQUEST["search"]["value"]);

      $columnOrder   = $_REQUEST['order'][0]['column']; // Suponiendo que 'column' es el parámetro que contiene el nombre de la columna por la cual quieres ordenar
      $dirOrden      = $_REQUEST['order'][0]['dir']; // Suponiendo que 'dir' es el parámetro que contiene la dirección del ordenamiento (ASC o DESC)
      $columna_orden = $columnas[$columnOrder];

      $sql = "select veh.*,cli.*,gru.*,tveh.*, CONCAT(reg.ordinal,' ',reg.region)region, com.comuna_nombre 
      comuna from vehiculos veh left outer join clientes cli on cli.id = veh.veh_cliente 
          left outer join grupos gru on veh.veh_grupo = gru.gru_id 
          left outer join tiposdevehiculos tveh on veh.veh_tipo=tveh.tveh_id 
          LEFT OUTER JOIN regiones reg ON veh.veh_region=reg.id 
          LEFT OUTER JOIN comunas com ON com.comuna_id= veh.veh_comuna
          ";

      // Filtramos la consulta por la búsqueda
      if ($search) {
        $filtroAvanzado = '';
        $filtroEstado = '';

        //filtros para tipo de servicio avanzado o basico
        $tipoServicio = array("avanzado", "a", "av", "ava", "avan", "avanz", "avanza", "avanzad", "basico", "b", "ba", "bas", "basi", "basic");
        $searchTServicio = strtolower($search);
        if (in_array($searchTServicio, $tipoServicio)) {

          $tipoServicioB = array("basico", "b", "ba", "bas", "basi", "basic");
          $tipoServicioA = array("avanzado", "a", "av", "ava", "avan", "avanz", "avanza", "avanzad");
          if (in_array($searchTServicio, $tipoServicioA)) {
            $filtroAvanzado = ' OR veh_tservicio = 2 ';
          } else if (in_array($searchTServicio, $tipoServicioB)) {
            $filtroAvanzado = ' OR veh_tservicio = 1 ';
          }
        }

        //filtro para estado activo o inactivo
        $tipoEstado = array("activo", "a", "ac", "act", "acti", "activ", "inactivo", "i", "in", "ina", "inac", "inact", "inacti", "inactiv");
        $searchEstado = strtolower($search);
        if (in_array($searchEstado, $tipoEstado)) {
          $tipoEstadoA = array("activo", "a", "ac", "act", "acti", "activ");
          $tipoEstadoI = array("inactivo", "i", "in", "ina", "inac", "inact", "inacti", "inactiv");
          if (in_array($searchEstado, $tipoEstadoA)) {
            $filtroAvanzado = ' OR veh_estado = 0 ';
          } else if (in_array($searchEstado, $tipoEstadoI)) {
            $filtroAvanzado = ' OR veh_estado = 1 ';
          }
        }

        $sql .= " WHERE (veh_imei LIKE '%$search%' 
                  OR veh_patente LIKE '%$search%' 
                  OR cuenta LIKE '%$search%' 
                  OR tveh_nombre LIKE '%$search%' " . $filtroAvanzado . $filtroEstado . ' ) and veh.deleted_at is NULL';
      }

      //ordenamiento por columna
      if ($columnOrder > 0) {
        $order =  " ORDER BY " . $columna_orden . " " . $dirOrden;
        $sql .= $order;
      }


      // Limitamos la consulta
      $sql .= " LIMIT $start, $length";
    }

    //echo $sql;
    // Ejecutamos la consulta
    $res = $link->query($sql);
    $vehiculos = array();
    //        echo '<pre>';
    //        print_r($serie);
    //        echo '</pre>';
    //        return true;

    $sql1 = "SELECT pro_id, ser_codigo FROM serie_guia;";
    $dataSerie = [];
    $res1 = $link->query($sql1);

    if ($res1) {
      while ($serie = mysqli_fetch_array($res1)) {
        $dataSerie[$serie['ser_codigo']] = $serie['pro_id'];
      }
    }

    $contadorRegistro = $start;
    if ($res) {
      while ($fila = mysqli_fetch_array($res)) {
        /*$productosxvehiculo=getProxVeh($fila["veh_id"]);*/

        $productosxvehiculo = array();
        if ($fila["veh_sim"] == null || $fila["veh_sim"] == '') {
          $sim = '-';
        } else {
          $sim = $fila["veh_sim"];
        }

        if (isset($fila['tic_tipotrabajo'])) {
          $ttra = $fila['tic_tipotrabajo'];
        } else {
          $ttra = 0;
        }

        $idproser = 0;
        //        $sql1 = "SELECT * FROM serie_guia where ser_codigo = '{$fila['veh_imei']}'";
        //        $res1 = $link->query($sql1);
        if (mysqli_num_rows($res) > 0) {
          //            $fila1 = mysqli_fetch_array($res1);
          //            $idproser = $fila1['pro_id'];
          if (isset($dataSerie[$fila['veh_imei']])) {
            $idproser = $dataSerie[$fila['veh_imei']];
          } else {
            //                echo $fila['veh_imei'].'.\n';
          }
        }

        $badg = '<span class="badge badge-danger">Inactivo</span>';
        if ($fila["veh_estado"] == 0) {
          $badg = '<span class="badge badge-success">Activo</span>';
        }

        $tipo = "";
        if ($fila["veh_tipo"] == 0) {
          $tipo = "--";
        } else {
          $tipo = $fila["veh_tipo"];
        }

        //      echo $fila["id"];
        //      echo $fila["veh_id"];

        //      $func1 = 'ProductosVehiculo("'+$fila["veh_id"]+'","'+$fila["veh_id"]+'")';
        $func1 = 'ProductosVehiculo(' . count($vehiculos) . ',' . $fila["veh_id"] . ')';
        $span1 = "<span class='btn btn-sm btn-info btn-circle-s' onclick='$func1;'><i class='fa fa-list-alt' aria-hidden='true'></i></span>";
        $span2 = "<span class='btn btn-sm btn-warning btn-circle-s' onclick='EditarVehiculo(" . count($vehiculos) . "," . $fila["veh_id"] . ");'><i class='fa fa-edit' aria-hidden='true'></i></span>";
        $span3 = "<span class='btn btn-sm btn-danger btn-circle-s' onclick='EliminarVehiculo(" . count($vehiculos) . "," . $fila["veh_id"] . ")'><i class='fa fa-trash' aria-hidden='true'></i></span>";

        //$span3 = "<span class='btn btn-sm btn-danger btn-circle-s' onclick='EliminarVehiculo(".$fila["id"].",".$fila["veh_id"].")'><i class='fa fa-trash' aria-hidden='true'></i></span>";


        $vehiculos[] = array(
          "id_cont" => ++$contadorRegistro,
          "badg" => $badg,
          "tipo" => $tipo,

          "idproser" => $idproser,
          "veh_imei" => $fila["veh_imei"],
          "veh_can" => $fila["veh_can"]==null?'N/A':$fila["veh_can"],
          "tic_tiposervicio" => (isset($fila["tic_tiposervicio"]) ? $fila["tic_tiposervicio"] : ''),
          "veh_estado" => $fila["veh_estado"],
          "idveh" => $fila["veh_id"],
          "ttrabajo" => $ttra,
          "tservicio" => ($fila["veh_tservicio"] == 2 ? 'Avanzado' : ($fila["veh_tservicio"] == 3 ? 'Thermo' : 'Basico')),
          "dispositivo" => $fila["veh_dispositivo"],
          "idtipo" => $fila["veh_tipo"],
          "tipo" => $fila["tveh_nombre"],
          "idgps" => $fila["veh_gps"],
          "idcliente" => $fila["id"],
          "cliente" => $fila["razonsocial"],
          "cuenta" => $fila["cuenta"],
          "idgrupo" => $fila["veh_grupo"],
          "grupo" => $fila["gru_nombre"],
          "patente" => $fila["veh_patente"],
          "contacto" => $fila["veh_contacto"],
          "celular" => $fila["veh_celular"],
          "region" => $fila["veh_region"],
          "comuna" => $fila["veh_comuna"],
          "nregion" => $fila["region"],
          "ncomuna" => $fila["comuna"],
          "productos" => $productosxvehiculo,
          "sim" => $sim,
          "span1" => $span1,
          "span2" => $span2,
          "span3" => $span3,
        );
      }
    }

    if (isset($_REQUEST['idpatente'])) {
      $dataReturn['data'] = $vehiculos;
    } else {
      $totalData = "SELECT COUNT(*) as contador
          from vehiculos veh 
          left outer join clientes cli on cli.id = veh.veh_cliente 
          left outer join grupos gru on veh.veh_grupo = gru.gru_id 
          left outer join tiposdevehiculos tveh on veh.veh_tipo=tveh.tveh_id 
          LEFT OUTER JOIN regiones reg ON veh.veh_region=reg.id 
          LEFT OUTER JOIN comunas com ON com.comuna_id= veh.veh_comuna ";

      $resData = $link->query($totalData);
      $total = mysqli_fetch_array($resData)['contador'];
      $dataReturn['recordsTotal'] = $total;
      $dataReturn['recordsFiltered'] = $total;

      $response = array(
        "draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $vehiculos
      );

      mysqli_close($link);
      echo json_encode($response);
      return;
    }


    mysqli_close($link);

    //    $response = array(
    //        "draw" => $draw,
    //        "recordsTotal" => $totalRecords,
    //        "recordsFiltered" => $filteredRecords,
    //        "data" => $data
    //    );

    // Enviamos la respuesta a DataTables
    echo json_encode($dataReturn);
    return;


    if (isset($_REQUEST['idpatente'])) {
      $sql = "select veh.*,cli.*,gru.*,tveh.*, CONCAT(reg.ordinal,' ',reg.region)region, com.comuna_nombre comuna , t7.tic_tipotrabajo, t7.tic_tiposervicio
        from vehiculos veh 
        left outer join clientes cli on cli.id = veh.veh_cliente 
        left outer join grupos gru on veh.veh_grupo = gru.gru_id 
        left outer join tiposdevehiculos tveh on veh.veh_tipo = tveh.tveh_id 
        LEFT OUTER JOIN regiones reg ON veh.veh_region = reg.id 
        LEFT OUTER JOIN comunas com ON com.comuna_id = veh.veh_comuna 
        LEFT OUTER JOIN tickets t7 ON t7.tic_patente = veh.veh_id and t7.tic_estado = 2
        WHERE veh.veh_id = {$_REQUEST['idpatente']} and veh.deleted_At is null ";
    } else {
      $sql = "select veh.*,cli.*,gru.*,tveh.*, CONCAT(reg.ordinal,' ',reg.region)region, com.comuna_nombre comuna from vehiculos veh left outer join clientes cli on cli.id = veh.veh_cliente left outer join grupos gru on veh.veh_grupo = gru.gru_id left outer join tiposdevehiculos tveh on veh.veh_tipo=tveh.tveh_id LEFT OUTER JOIN regiones reg ON veh.veh_region=reg.id 
      LEFT OUTER JOIN comunas com ON com.comuna_id= veh.veh_comuna
      WHERE veh.deleted_At is null
      ";
    }

    $res = $link->query($sql);
    $vehiculos = array();



    $bandTest = false;
    if (!$bandTest) {
      $sql1 = "SELECT pro_id, ser_codigo FROM serie_guia;";
      $dataSerie = [];
      $res1 = $link->query($sql1);
      while ($serie = mysqli_fetch_array($res1)) {
        $dataSerie[$serie['ser_codigo']] = $serie['pro_id'];
      }
    }

    while ($fila = mysqli_fetch_array($res)) {
      /*$productosxvehiculo=getProxVeh($fila["veh_id"]);*/

      $productosxvehiculo = array();
      if ($fila["veh_sim"] == null || $fila["veh_sim"] == '') {
        $sim = '-';
      } else {
        $sim = $fila["veh_sim"];
      }

      if (isset($fila['tic_tipotrabajo'])) {
        $ttra = $fila['tic_tipotrabajo'];
      } else {
        $ttra = 0;
      }

      $idproser = 0;
      if ($bandTest) {
        $sql1 = "SELECT * FROM serie_guia where ser_codigo = '{$fila['veh_imei']}'";
        $res1 = $link->query($sql1);
      }

      if (mysqli_num_rows($res) > 0) {
        if ($bandTest) {
          $fila1 = mysqli_fetch_array($res1);
          $idproser = $fila1['pro_id'];
        } else {
          if (isset($dataSerie[$fila['veh_imei']])) {
            $idproser = $dataSerie[$fila['veh_imei']];
          } else {
            //                echo $fila['veh_imei'].'.\n';
          }
        }
      }

      $vehiculos[] = array("idproser" => $idproser, "veh_imei" => $fila["veh_imei"], "tic_tiposervicio" => $fila["tic_tiposervicio"], "veh_estado" => $fila["veh_estado"], "idveh" => $fila["veh_id"], "ttrabajo" => $ttra, "tservicio" => $fila["veh_tservicio"], "dispositivo" => $fila["veh_dispositivo"], "idtipo" => $fila["veh_tipo"], "tipo" => $fila["tveh_nombre"], "idgps" => $fila["veh_gps"], "idcliente" => $fila["id"], "cliente" => $fila["razonsocial"], "cuenta" => $fila["cuenta"], "idgrupo" => $fila["veh_grupo"], "grupo" => $fila["gru_nombre"], "patente" => $fila["veh_patente"], "contacto" => $fila["veh_contacto"], "celular" => $fila["veh_celular"], "region" => $fila["veh_region"], "comuna" => $fila["veh_comuna"], "nregion" => $fila["region"], "ncomuna" => $fila["comuna"], "productos" => $productosxvehiculo, "sim" => $sim);
    }
    mysqli_close($link);
    echo json_encode($vehiculos);
    break;

  case 'getdetallevehiculo':
    $recibe   = json_decode($_REQUEST['envio'], true);
    $sql      = "select * from vehiculos where veh_id=" . $recibe["idpatente"] . " and deleted_at is NULL";
    $res      = $link->query($sql);
    $devuelve = array();
    foreach ($res as $key) {
      array_push($devuelve, array('imei' => $key['veh_imei'], 'id' => $key['veh_id'], 'patente' => $key['veh_patente']));
    }
    echo json_encode($devuelve);

    break;

  case 'ValidarPatente':
    $sql    = "select veh_patente from vehiculos where veh_patente='" . $_REQUEST["patente"] . "' and deleted_at is NULL";
    $res    = $link->query($sql);
    $existe = 0;
    if (mysqli_num_rows($res) > 0) {
      $existe = 1;
    }
    echo $existe;
    break;

  case 'nuevovehiculo':
    if ($_REQUEST["grupo"] == "") {
      $_REQUEST["grupo"] = 0;
    }

    if ($_REQUEST['sim']) {
      $varsim = str_replace(array('e', 'E'), '', $_REQUEST['sim']);
    }

    $sql2  = "select * from vehiculos where veh_patente = '" . $_REQUEST["patente"] . "' and deleted_at is NULL";
    $res2  = $link->query($sql2);
    $fila2 = mysqli_fetch_array($res2);

    if ($fila2['veh_id'] != '') {
      header("Location: http://18.234.82.208/cloux/index.php?menu=nuevovehiculo&idmenu=82&err=2");
      exit;
    } else {

      $comuna = 0;
      $region = 0;

      if ($_REQUEST['comuna'] != '') {
        (int)$comuna = $_REQUEST['comuna'];
      }

      if ($_REQUEST['region'] != '') {
        (int)$region = $_REQUEST['region'];
      }

      if ($_REQUEST["dispositivo"] == '') {
        $_REQUEST["dispositivo"] = 0;
      }

      if ($_REQUEST["tservicio"] == '') {
        $_REQUEST["tservicio"] = 0;
      }

      if ($_REQUEST["rsocial"] == '') {
        $_REQUEST["rsocial"] = 0;
      }

      $sql        = "insert into vehiculos(veh_idflotasnet,veh_tipo,veh_gps,veh_cliente,veh_rsocial,veh_grupo,veh_patente,veh_contacto,veh_celular,veh_dispositivo,veh_tservicio,veh_estado, veh_observacion,veh_ultimaposicion, veh_localidad, veh_alerta, veh_seriegps, veh_region, veh_comuna,veh_sim, veh_modelo, veh_marca)values(0," . $_REQUEST["tipo"] . "," . $_REQUEST["gps"] . "," . $_REQUEST["cliente"] . "," . $_REQUEST["rsocial"] . "," . $_REQUEST["grupo"] . ",'" . $_REQUEST["patente"] . "','" . $_REQUEST["contacto"] . "','" . $_REQUEST["celular"] . "'," . $_REQUEST["dispositivo"] . "," . $_REQUEST["tservicio"] . ",0,0,now(),'', 0,''," . $region . "," . $comuna . ",'" . $_REQUEST['sim'] . "','" . $_REQUEST['modelo'] . "','" . $_REQUEST['marca'] . "')";
      $res        = $link->query($sql);

      /* echo $sql.'<br>';
    die();*/
      $idvehiculo = $link->insert_id;

      if ($idvehiculo != '') {
        /*if(isset($_REQUEST["productosSend"])){
         $productos = $_REQUEST["productosSend"];
         $contador  = 0;
         foreach($productos as $valor){
           $sep  = explode("|",$valor);
           $sql1 = "INSERT INTO productosxvehiculos( pxv_idveh, pxv_cantidad, pxv_idpro, pxv_nserie) VALUES ({$idvehiculo}, {$sep[0]}, {$sep[1]},'{$sep[2]}')";
           $res1 = $link->query($sql1);
           $contador++;
           $response['sqlveh'.$contador] = $sql1;
        }
      }*/

        $response['sql'] = $sql;
        if ($_REQUEST['instalar'] == "on") {
          //  $sale_a='index.php?menu=tickets&idmenu=100&nuevo='.$idvehiculo;
        } else {
          $sale_a = $_REQUEST["retornar"];
        }
      } else {
        header("Location: http://18.234.82.208/cloux/index.php?menu=nuevovehiculo&idmenu=82&err=1");
        exit;
      }
    }

    break;

  case 'editarvehiculo':

    $tipovehiculo = 0;
    $region = 0;
    $comuna = 0;

    if (isset($_REQUEST['tickalb']) == 1) {

      $tservicio = $_REQUEST["tservicio"];

      if ($tservicio == 1) {
        $idtiposer = 2; //basico
      } else if ($tservicio == 2) {
        $idtiposer = 1; //avanzado
      } else if ($tservicio == 3) {
        $idtiposer = 3; //thermo
      } else {
        echo 'Sin servicio.';
        return false;
      }

      $sql2  = "select * from tickets where tic_id = {$_REQUEST['idticket']}";
      $res2  = $link->query($sql2);
      $fila2 = mysqli_fetch_array($res);

      $sql3  = "update tickets set tic_cliente = {$_REQUEST["cliente"]}, tic_tipotrabajo = {$_REQUEST['ttrabajo']}, tic_tiposervicio = {$tservicio} where tic_id = {$_REQUEST['idticket']}";
      $res3  = $link->query($sql3);
    }

    if (isset($_REQUEST['cliente']) && $_REQUEST['cliente'] != '') {
      $sqlupdtick  = "update tickets set tic_cliente = {$_REQUEST["cliente"]} where tic_patente = {$_REQUEST['idveh']}";
      $resupdtick  = $link->query($sqlupdtick);
      /*echo $sqlupdtick.'<br>';*/

      if ($resupdtick) {
        /*if(true){*/
        $sqlsel  = "SELECT * FROM clientes WHERE id = {$_REQUEST["cliente"]}";
        $ressel  = $link->query($sqlsel);
        if (mysqli_num_rows($ressel) > 0) {
          $filacli =  mysqli_fetch_array($ressel);
          if ($filacli['razonsocial'] == null) {
            $filacli['razonsocial'] == '';
          }

          if ($filacli['rrut'] == null) {
            $filacli['rrut'] == '';
          }
          $clipat = strtolower($filacli['cuenta']);

          $linkclient = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', $clipat);
          if (mysqli_connect_errno()) {
            printf("Falló la conexión: %s\n", mysqli_connect_error());
            exit();
          }

          $sqlveh  = "SELECT * FROM vehiculos where veh_patente = '{$_REQUEST["patente"]}'";
          $resveh  = $linkclient->query($sqlveh);
          if (mysqli_num_rows($resveh) > 0) {
            $filaveh = mysqli_fetch_array($resveh);
            if ($filacli['razonsocial'] == '') {
              $sqlupdveh  = "UPDATE vehiculos SET veh_rsocial = 0 WHERE veh_id = '{$filaveh["veh_id"]}'";
              /*echo $sqlupdveh.'<br>';*/
              $resupdveh  = $linkclient->query($sqlupdveh);
            } else {
              $idnuevars = 0;
              $sqlrs  = "SELECT * FROM transportistas where tra_rsocial like '%{$filacli['razonsocial']}%' order by 1 limit 1";
              $resrs  = $linkclient->query($sqlrs);
              if (mysqli_num_rows($resrs) > 0) {
                $filatra = mysqli_fetch_array($resrs);
                $idrsocial = $filatra['tra_id'];
              } else {
                $sqlins  = "INSERT INTO transportistas (tra_rut,tra_rsocial,tra_alias,tra_giro,tra_correo,tra_proter,tra_create_at) VALUES ('{$filacli["rrut"]}','{$filacli["razonsocial"]}','{$filacli["razonsocial"]}',0,'',0,'{$fechachile}')";
                $resins  = $linkclient->query($sqlins);
                /*echo $sqlins.'<br>';*/
                $idrsocial = $linkclient->insert_id;
              }

              $sqlupdveh  = "UPDATE vehiculos SET veh_rsocial = {$idrsocial} WHERE veh_id = '{$filaveh["veh_id"]}'";
              $resupdveh  = $linkclient->query($sqlupdveh);
              /*echo $sqlupdveh.'<br>';*/
            }
          }
        }

        /*die();*/
      }
    }

    if ($_REQUEST["tipo"] == '') {
      $tipovehiculo = 0;
    } else {
      $tipovehiculo = (int)$_REQUEST["tipo"];
    }
    if ($_REQUEST["region"] == '') {
      $region = 0;
    } else {
      $region = (int)$_REQUEST["region"];
    }
    if ($_REQUEST["comuna"] == '') {
      $comuna = 0;
    } else {
      $comuna = (int)$_REQUEST["comuna"];
    }
    if ($_REQUEST["dispositivo"] == '') {
      $dispo = 0;
    } else {
      $dispo = (int)$_REQUEST["dispositivo"];
    }
    if ($_REQUEST["tservicio"] == '') {
      $tser = 0;
    } else {
      $tser = (int)$_REQUEST["tservicio"];
    }


    // if($tser==2){
    //     $tser==1;
    // }else if($tser==1){
    //     $tser==2;
    // }else if($tser==3){
    //     $tser==3;
    // }

    $sql = "update vehiculos 
                set veh_tipo=" . $tipovehiculo . ",
                veh_gps=" . $_REQUEST["gps"] . ",veh_cliente=" . $_REQUEST["cliente"] . ",
                veh_grupo=" . $_REQUEST["grupo"] . ",veh_patente='" . $_REQUEST["patente"] . "', veh_imei='" . $_REQUEST["veh_imei"] . "',
                veh_contacto='" . $_REQUEST["contacto"] . "', veh_celular='" . $_REQUEST["celular"] . "', 
                veh_dispositivo=" . $dispo . ", veh_tservicio=" . $tser . ", veh_region=" . $region . ", 
                veh_comuna=" . $comuna . " where veh_id=" . $_REQUEST["idveh"] . " and deleted_at is NULL";
    $res = $link->query($sql);
    // echo  $sql;


    if (isset($_REQUEST["productosSend"])) {
      $productos = $_REQUEST["productosSend"];
      $contador = 0;
      foreach ($productos as $valor) {
        $sep = explode("|", $valor);
        if ($sep[3] == 0) {
          $sql1 = "INSERT INTO productosxvehiculos( pxv_idveh, pxv_cantidad, pxv_idpro, pxv_nserie) VALUES ({$_REQUEST["idveh"]}, {$sep[0]}, {$sep[1]},'{$sep[2]}')";
          $res1 = $link->query($sql1);
          if ($sep[2] != '' && $sep[2] != null) {
            $sql2 = "UPDATE serie_guia SET pro_id = '{$sep[1]}' WHERE ser_codigo = '{$sep[2]}'";
            $res2 = $link->query($sql2);
          }
          //$contador++;
          //$response['sqlveh'.$contador] = $sql1;
        }
      }
    }

    if ($_REQUEST["retornar"] == 'no') {
      $sale_a = $_REQUEST["retornar"];
      $response['status'] = 'OK';
      echo json_encode($response);
    } else {
      $response['status'] = 'OK';
      $response['sql'] = $sql;

      echo json_encode($response);
      if (isset($_REQUEST["deveh"]) == 1) {
        header("Location: https://www.ds-tms.com/cloux/index.php?menu=listarvehiculos&idmenu=83");
        exit;
      }
    }

    break;

  case 'eliminarvehiculo':
    //$sql="delete from vehiculos where veh_id='".$_REQUEST["idveh"]."'";
    //eliminado suave
    $sql = "UPDATE vehiculos set deleted_at=now() where veh_id='" . $_REQUEST["idveh"] . "' and deleted_at is NULL";
    $res = $link->query($sql);

    $sql = "UPDATE productosxvehiculos set pxv_estado=0 where pxv_idveh='" . $_REQUEST["idveh"] . "' and pxv_estado=1";
    $res = $link->query($sql);

    $sql = "UPDATE serie_guia set ser_instalado=0 
  where ser_codigo=(select veh_imei from vehiculos where veh_id='" . $_REQUEST["idveh"] . " and deleted_at is NULL')";
    $res = $link->query($sql);

    break;

  case 'getTabVehiculosMonitor':
    $sql = "select veh.*,cli.*,gru.gru_nombre from  vehiculos veh left outer join clientes cli on cli.id = veh.veh_cliente left outer join grupos gru on veh.veh_grupo = gru.gru_id where veh.veh_h48 >0";
    $res = $link->query($sql);
    $vehiculos = array();
    while ($fila = mysqli_fetch_array($res)) {
      //{"2":0,"12":"--","24":"--","48":"--","localidad":"Acceso A Carr Panam 1523, Rancagua, Regi\u00f3n del Libertador Gral. Bernardo O?Higgins, Chile","ultima":"01\/04\/2019 09:22:13"}
      //$transmisiones=getTransmisiones($fila["veh_id"]);
      $datetime1 = new DateTime(date("Y-m-d H:i:s"));
      $datetime2 = new DateTime($fila["veh_ultimaposicion"]);
      $interval = $datetime1->diff($datetime2);
      $dias = (int)$interval->format('%a');
      $dias2 = 0;
      $dias5 = 0;
      $dias10 = 0;
      if ($dias >= 0 && $dias <= 2) {
        $dias2 = $dias;
        $dias5 = 0;
        $dias10 = 0;
      } else if ($dias >= 3 && $dias <= 5) {
        $dias2 = 0;
        $dias5 = $dias;
        $dias10 = 0;
      } else if ($dias >= 6) {
        $dias2 = 0;
        $dias5 = 0;
        $dias10 = $dias;
      }

      if ($fila['veh_frecuencia1'] > 0) {
        if ($fila['veh_frecuencia1'] == 1) {
          $frecuencia = "SI";
          $color1 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $frecuencia = "NO";
          $color1 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $frecuencia = "NA";
        $color1 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      if ($fila['veh_ultimaversion'] > 0) {
        if ($fila['veh_ultimaversion'] == 1) {
          $ultimaversion = "SI";
          $color2 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $ultimaversion = "NO";
          $color2 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $ultimaversion = "NA";
        $color2 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      if ($fila['veh_trama3'] > 0) {
        if ($fila['veh_trama3'] == 1) {
          $trama3 = "SI";
          $color3 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $trama3 = "NO";
          $color3 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $trama3 = "NA";
        $color3 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      if ($fila['veh_sensores'] > 0) {
        if ($fila['veh_sensores'] == 1) {
          $sensores = "SI";
          $color4 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $sensores = "NO";
          $color4 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $sensores = "NA";
        $color4 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      if ($fila['veh_parmotor'] > 0) {
        if ($fila['veh_parmotor'] == 1) {
          $parmotor = "SI";
          $color5 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $parmotor = "NO";
          $color5 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $parmotor = "NA";
        $color5 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      if ($fila['veh_atrayectos'] > 0) {
        if ($fila['veh_atrayectos'] == 1) {
          $atrayectos = "SI";
          $color6 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $atrayectos = "NO";
          $color6 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $atrayectos = "NA";
        $color6 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      if ($fila['veh_geocercas'] > 0) {
        if ($fila['veh_geocercas'] == 1) {
          $geocercas = "SI";
          $color7 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $geocercas = "NO";
          $color7 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $geocercas = "NA";
        $color7 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      if ($fila['veh_ws'] > 0) {
        if ($fila['veh_ws'] == 1) {
          $ws = "SI";
          $color8 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $ws = "NO";
          $color8 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $ws = "NA";
        $color8 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      if ($fila['veh_alertaaccidente'] > 0) {
        if ($fila['veh_alertaaccidente'] == 1) {
          $alerta = "SI";
          $color9 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $alerta = "NO";
          $color9 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $alerta = "NA";
        $color9 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      if ($fila['veh_jamming'] > 0) {
        if ($fila['veh_jamming'] == 1) {
          $jamming = "SI";
          $color10 = "background-color:#2AE36D;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        } else {
          $jamming = "NO";
          $color10 = "background-color:#E33E2A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
        }
      } else {
        $jamming = "NA";
        $color10 = "background-color:#E3D82A;color:white;border-radius:8px;padding:3px 7px 3px 7px;font-weight:bold";
      }

      $transmisiones = array("2" => $fila["veh_h2"], "12" => $fila["veh_h12"], "24" => $fila["veh_h24"], "48" => $fila["veh_h48"], "localidad" => $fila["veh_localidad"], "ultima" => $fila["veh_ultimaposicion"], "dias2" => $dias2, "dias5" => $dias5, "dias10" => $dias10, "frecuencia" => $frecuencia, "ultimaversion" => $ultimaversion, "trama3" => $trama3, "sensores" => $sensores, "alertaaccidente" => $alerta, "jamming" => $jamming, "parmotor" => $parmotor, "atrayectos" => $atrayectos, "geocercas" => $geocercas, "ws" => $ws, "fechaobservacion" => $fila['veh_fechaobservacion'], "color1" => $color1, "color2" => $color2, "color3" => $color3, "color4" => $color4, "color5" => $color5, "color6" => $color6, "color7" => $color7, "color8" => $color8, "color9" => $color9, "color10" => $color10);
      //$observaciones = getObservacionesVeh($fila["veh_id"]);
      if ($fila["veh_observacion"] > 0) {
        $observacion = obtenervalor("observacionesvehiculos", "odv_observacion", "where odv_id='" . $fila["veh_observacion"] . "'");
      } else {
        $observacion = "ACTIVO";
      }



      if (is_null($fila["gru_nombre"])) {
        $grupo = "--";
      } else {
        $grupo = $fila["gru_nombre"];
      }
      // $patente = formatpatente($fila["veh_patente"]);
      $patente = $fila["veh_patente"];
      $vehiculos[] = array("idveh" => $fila["veh_id"], "idtipo" => $fila["veh_tipo"], "idcliente" => $fila["id"], "cliente" => $fila["razonsocial"], "cuenta" => $fila["cuenta"], "patente" => $patente, "grupo" => $grupo, "transmisiones" => $transmisiones, "observacion" => $observacion, "ws" => $fila["cli_clavews"]);
    }
    mysqli_close($link);
    echo json_encode($vehiculos);
    break;

  case 'getSelectODV':
    $estados = array();
    $sql = "select * from observacionesvehiculos";
    $res = $link->query($sql);
    while ($fila = mysqli_fetch_array($res)) {
      $estados[] = array("id" => $fila["odv_id"], "observacion" => $fila["odv_observacion"]);
    }
    mysqli_close($link);
    echo json_encode($estados);
    break;

  case 'ActualizaObservacion':
    $fechaupdate = date("Y-m-d H:i:s");
    $sql = "update vehiculos set veh_observacion='" . $_REQUEST["odv_id"] . "', veh_fechaobservacion='{$fechaupdate}' where veh_id='" . $_REQUEST["veh_id"] . "' and deleted_at is NULL";
    $res = $link->query($sql);
    echo "OK";
    break;

  case 'nuevoodv':
    $sql = "insert into observacionesvehiculos(odv_observacion)values('" . $_REQUEST["nombre"] . "')";
    $res = $link->query($sql);
    echo $sql . '<br>';
    $sale_a = $_REQUEST["retornar"];
    break;

  case 'editarodv':
    $sql = "update observacionesvehiculos set odv_observacion='" . $_REQUEST["nombre"] . "' where odv_id='" . $_REQUEST["idodv"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminarodv':
    $sql = "delete from observacionesvehiculos where odv_id='" . $_REQUEST["idodv"] . "'";
    $res = $link->query($sql);
    break;


  case 'nuevogrupo':
    $sql    = "insert into grupos(gru_cliente,gru_nombre)values('" . $_REQUEST["cliente"] . "','" . $_REQUEST["nombre"] . "')";
    $res    = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'editargrupo':
    $sql    = "update grupos set gru_cliente='" . $_REQUEST["cliente"] . "',gru_nombre =  '" . $_REQUEST["nombre"] . "' where gru_id='" . $_REQUEST["idgru"] . "'";
    $res    = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;

  case 'eliminargrupo':
    $sql = "delete from grupos where gru_id='" . $_REQUEST["idgru"] . "'";
    $res = $link->query($sql);
    break;

  case 'getGruposCliente':
    $sql    = "select * from grupos where gru_cliente ='" . $_REQUEST["id"] . "'";
    $res    = $link->query($sql);
    $grupos = array();
    if (mysqli_num_rows($res) > 0) {
      while ($fila  = mysqli_fetch_array($res)) {
        $grupos[] = array("id" => $fila["gru_id"], "nombre" => $fila["gru_nombre"]);
      }
    }
    mysqli_close($link);
    echo json_encode($grupos);
    break;

  case 'getProductosxVehiculos':
    // $sql = "SELECT * FROM `productosxvehiculos` pxv LEFT OUTER JOIN productos pro ON pxv.pxv_idpro=pro.pro_id WHERE pxv.pxv_idveh={$_REQUEST['id']}";
    // $res=$link->query($sql);
    // $proxveh = array();
    // while($fila=mysqli_fetch_array($res)){
    //   $proxveh[]=array("id"=>$fila["pxv_id"],"idproducto"=>$fila["pxv_idpro"],"producto"=>$fila["pro_nombre"],"cantidad"=>$fila["pxv_cantidad"],"serie"=>$fila["pxv_nserie"]);
    // }
    $proxveh = getProxVeh($_REQUEST['id']);
    $devuelve = array();
    $sql = "SELECT * FROM `tickets` where tic_id = {$_REQUEST['idticket']}";
    $res = $link->query($sql);
    if (mysqli_num_rows($res) > 0) {
      $fila = mysqli_fetch_array($res);
      $devuelve['seriesim'] = $fila['tic_seriesim'];
      $devuelve['cliente_id'] = $fila['tic_seriesim'];
    }
    $sql = "SELECT t3.pro_nombre FROM asociacion_vehiculos_accesorios t1 JOIN serie_guia t2 ON t2.ser_id=t1.ava_idguia JOIN productos t3 ON t3.pro_id=t2.pro_id WHERE ava_idveh='{$_REQUEST['id']}'";
    $res = $link->query($sql);
    $accesorios = array();
    while ($fila = mysqli_fetch_array($res)) {
      $accesorios[] = $fila['pro_nombre'];
    }
    $devuelve['prosveh'] = $proxveh;
    $devuelve['accesorios'] = $accesorios;
    echo json_encode($devuelve);
    break;

  case 'getRazonSocial':
    $sql = "SELECT * FROM clientes WHERE cuenta LIKE '%{$_REQUEST["text"]}%'";
    $res = $link->query($sql);
    $grupos = array();
    if (mysqli_num_rows($res) > 0) {
      while ($fila = mysqli_fetch_array($res)) {
        $grupos[] = array("id" => $fila["id"], "rsocial" => $fila["razonsocial"], "rlegal" => $fila["rlegal"], "rrut" => $fila["rrut"]);
      }
    }
    mysqli_close($link);
    echo json_encode($grupos);
    break;

    // TIPOS DE VEHICULOS
  case 'nuevotveh':
    $sql = "insert into tiposdevehiculos(tveh_nombre)values('" . $_REQUEST["nombre"] . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'editartveh':
    $sql = "update tiposdevehiculos set tveh_nombre='" . $_REQUEST["nombre"] . "' where tveh_id='" . $_REQUEST["idtveh"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminartveh':
    $sql = "delete from tiposdevehiculos where tveh_id='" . $_REQUEST["idtveh"] . "'";
    $res = $link->query($sql);
    break;
    /************************************
OPERACIONES CONDUCTORES
     *************************************/
  case 'getTabConductores':
    $sql = "select con.*,cli.* from conductores con left outer join clientes cli on con.con_cliente = cli.id";
    $res = $link->query($sql);
    $conductores = array();
    while ($fila = mysqli_fetch_array($res)) {
      $conductores[] = array("idcon" => $fila["con_id"], "idcliente" => $fila["con_cliente"], "cliente" => $fila["razonsocial"], "pin" => $fila["con_pin"], "rut" => $fila["con_rut"], "apaterno" => $fila["con_apaterno"], "amaterno" => $fila["con_amaterno"], "nombre" => $fila["con_nombre"]);
    }
    mysqli_close($link);
    echo json_encode($conductores);
    break;

  case 'editarconductor':
    $sql = "update conductores set con_cliente = '" . $_REQUEST["cliente"] . "',con_pin='" . $_REQUEST["pin"] . "',con_nombre='" . $_REQUEST["nombre"] . "',con_apaterno='" . $_REQUEST["apaterno"] . "',con_amaterno='" . $_REQUEST["amaterno"] . "',con_rut='" . $_REQUEST["rut"] . "',con_clave='" . md5($_REQUEST["pin"]) . "' where con_id='" . $_REQUEST["idconductor"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'nuevoconductor':
    $sql = "insert into conductores (con_cliente,con_pin,con_nombre,con_apaterno,con_amaterno,con_rut,con_clave)values('" . $_REQUEST["cliente"] . "','" . $_REQUEST["pin"] . "','" . $_REQUEST["nombre"] . "','" . $_REQUEST["apaterno"] . "','" . $_REQUEST["amaterno"] . "','" . $_REQUEST["rut"] . "','" . md5($_REQUEST["pin"]) . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminarconductor':
    $sql = "delete from conductores where con_id='" . $_REQUEST["idcon"] . "'";
    $res = $link->query($sql);
    break;


  case 'getTabJornadasCon':
    $sql = "select con.*,cli.* from conductores con left outer join clientes cli on con.con_cliente = cli.id";
    $res = $link->query($sql);
    $inicios = array();
    $finales = array();
    $sinjornadas = array();
    while ($fila = mysqli_fetch_array($res)) {
      $conductor = $fila["con_nombre"] . " " . $fila["con_apaterno"] . " " . $fila["con_amaterno"];
      $rut = $fila["con_rut"];
      $cliente = $fila["razonsocial"];
      $sql2 = "select * from historialdejornadas where hdj_conductor='" . $fila["con_id"] . "' order by hdj_id desc limit 0,1 ";
      $res2 = $link->query($sql2);
      $cuenta2 = mysqli_num_rows($res2);
      if ($cuenta2 > 0) {
        $fila2 = mysqli_fetch_array($res2);
        $patente = obtenervalor("vehiculos", "veh_patente", "where veh_id='" . $fila2["hdj_patente"] . "'");
        $fechahora = devfechahora($fila2["hdj_fechahora"]);
        if ($fila2["hdj_tipo"] == 1) {
          $inicios[] = array("id" => $fila2["hdj_id"], "rut" => $rut, "idconductor" => $fila2["hdj_conductor"], "conductor" => $conductor, "cliente" => $cliente, "idpatente" => $fila2["hdj_patente"], "patente" => $patente, "fechahora" => $fechahora);
        } else {
          $finales[] = array("id" => $fila2["hdj_id"], "rut" => $rut, "idconductor" => $fila2["hdj_conductor"], "conductor" => $conductor, "cliente" => $cliente, "idpatente" => $fila2["hdj_patente"], "patente" => $patente, "fechahora" => $fechahora);
        }
      } else {
        $sinjornadas[] = array("id" => "", "rut" => $rut, "idconductor" => "", "conductor" => $conductor, "cliente" => $cliente, "patente" => "000000", "idpatente" => "", "fechahora" => "0000000");
      }
    }
    $jornadas["inicio"] = $inicios;
    $jornadas["final"] = $finales;
    $jornadas["sinjornada"] = $sinjornadas;


    // $sql="select con.*,cli.* from conductores con left outer join clientes cli on con.con_cliente = cli.id";
    // $res=$link->query($sql);
    // $conductores=array();
    // while($fila=mysqli_fetch_array($res)){
    // $conductores[]=array("idcon"=>$fila["con_id"],"idcliente"=>$fila["con_cliente"],"cliente"=>$fila["razonsocial"],"pin"=>$fila["con_pin"],"rut"=>$fila["con_rut"],"apaterno"=>$fila["con_apaterno"],"amaterno"=>$fila["con_amaterno"],"nombre"=>$fila["con_nombre"]);
    // }
    echo json_encode($jornadas);
    break;

  case 'finalizarJornadaManual':
    $hoy = getdate();
    $hora = $hoy["hours"] . ":" . $hoy["minutes"];
    $idtipo = 2;
    $idconductor = $_REQUEST["hdj_conductor"];
    $idpatente = $_REQUEST["hdj_patente"];
    // buscar si otro conductor ya inicio jornada con la patente
    $sql = "insert into historialdejornadas(hdj_conductor,hdj_patente,hdj_tipo,hdj_app)values('" . $idconductor . "','" . $idpatente . "','" . $idtipo . "',1)";
    $res = $link->query($sql);
    //cambiar estado a patente 
    $sql2 = "update vehiculos set veh_estado=0, veh_sesion=0  where veh_id='" . $idpatente . "' and deleted_at is NULL";
    $res2 = $link->query($sql2);
    break;


    /******************************
OPERACIONES USUARIOS
     ********************************/
  case 'agregarusuario':

    $sql1 = "SELECT * FROM usuarios where usu_usuario = '{$_REQUEST["usuario"]}'";
    $res1 = $link->query($sql1);
    if (mysqli_num_rows($res1) > 0 && ($_REQUEST['idusuario'] == '0' || $_REQUEST['idusuario'] == '')) {
      echo 'existe';
      header("Location: http://18.234.82.208/cloux/index.php?menu=usuarios&idmenu=1&repetido=1");
      exit;
    } else {
      $archivo = $_FILES['foto']['name']; // nombre archivo a cargar
      $temporal = $_FILES['foto']['tmp_name']; //nombre temporal en equipo cliente
      $codigo = generarCodigo(6);
      $foto = $codigo . "_" . $archivo;
      if ($temporal != "") {
        $permitidos =  array('gif', 'png', 'jpg');
        $ext = pathinfo($archivo, PATHINFO_EXTENSION);
        if (in_array($ext, $permitidos)) {
          move_uploaded_file($temporal, "img/" . $foto);
        }
      } else {
        $foto = "";
      }
      $nombre = $_REQUEST["nombre"];
      $usuario = $_REQUEST["usuario"];
      $correo = $_REQUEST["correo"];
      $empresa = $_REQUEST["cliente"];
      $bd = "NULL";;
      $sql = "SELECT * FROM `clientes` WHERE id = '{$empresa}'";
      $res = $link->query($sql);
      if (mysqli_num_rows($res) > 0) {
        $fila = mysqli_fetch_array($res);
        $bd = "'" . strtolower($fila['cuenta']) . "'";
      }

      if ($empresa == '') {
        $empresa = 0;
      }

      $clave = post_clave($_REQUEST["clave"]);
      /*echo $_REQUEST["clave"].'<br>';
        echo $clave.'<br>';
        die();*/

      if ($_REQUEST["usu_idpersonal"]) {
        $idpersonal = $_REQUEST["usu_idpersonal"];
      } else {
        $idpersonal = 0;
      }

      if ($_REQUEST["tusuario"]) {
        $perfil = $_REQUEST["tusuario"];
      } else {
        $perfil = 0;
      }

      if ($_REQUEST['idusuario'] != '0' && $_REQUEST['idusuario'] != '') {
        $sql = "UPDATE usuarios 
                  SET 
                    usu_nombre='" . $nombre . "',usu_usuario='" . $usuario . "',
                    usu_foto='" . $foto . "',usu_clave='" . $clave . "',
                    usu_correo='" . $correo . "',usu_empresa='" . $empresa . "',
                    usu_bbdd=" . $bd . ",usu_claveoriginal='" . $_REQUEST["clave"] . "' ,usu_perfil='" . $perfil . "' 
                    ,usu_idpersonal='" . $idpersonal . "'
                  WHERE usu_id='{$_REQUEST['idusuario']}'";
        $res = $link->query($sql);
        /*echo $sql.'<br>';
            die();*/
      } else {
        $sql = "INSERT INTO usuarios(usu_nombre,usu_usuario,usu_foto,usu_clave,usu_correo,usu_empresa,usu_bbdd,usu_claveoriginal, usu_perfil,usu_idpersonal)
                  VALUES('" . $nombre . "','" . $usuario . "','" . $foto . "','" . $clave . "','" . $correo . "','" . $empresa . "'," . $bd . ",'" . $_REQUEST["clave"] . "'," . $perfil . "," . $idpersonal . ")";
        //return json_encode($sql);
        //exit;
        $res = $link->query($sql);
      }

      /* echo $sql.'<br>';
        die();*/
      $sale_a = $_REQUEST["retornar"];

      header("Location: http://18.234.82.208/cloux/index.php?menu=usuarios&idmenu=1");
      /*exit;*/
    }

    break;

  case 'validaruser':
    $sql1 = "SELECT * FROM usuarios where usu_usuario = '{$_REQUEST["usuario"]}'";
    $res1 = $link->query($sql1);
    if (mysqli_num_rows($res1) > 0) {
      echo 'existe';
    } else {
      $archivo = $_FILES['foto']['name']; // nombre archivo a cargar
      $temporal = $_FILES['foto']['tmp_name']; //nombre temporal en equipo cliente
      $codigo = generarCodigo(6);
      $foto = $codigo . "_" . $archivo;
      if ($temporal != "") {
        $permitidos =  array('gif', 'png', 'jpg');
        $ext = pathinfo($archivo, PATHINFO_EXTENSION);
        if (in_array($ext, $permitidos)) {
          move_uploaded_file($temporal, "img/" . $foto);
        }
      } else {
        $foto = "";
      }
      $nombre = $_REQUEST["nombre"];
      $usuario = $_REQUEST["usuario"];
      $correo = $_REQUEST["correo"];
      $clave = $pass = post_clave($_REQUEST["clave"]);

      $sql = "insert into usuarios(usu_nombre,usu_usuario,usu_foto,usu_clave,usu_correo)values('" . $nombre . "','" . $usuario . "','" . $foto . "','" . $clave . "','" . $correo . "')";
      $res = $link->query($sql);
      $sale_a = $_REQUEST["retornar"];

      echo 'ok';
    }
    break;

  case 'permisos':
    $tipo = $_REQUEST["accion"];
    if ($tipo == '1') {
      $sql1 = "insert into permisosusuarios(idmodulo,idusuario)values('" . $_REQUEST["idmodulo"] . "','" . $_REQUEST["idusuario"] . "')";
      $res1 = $link->query($sql1);
    } else {
      $sql2 = "delete from permisosusuarios where idusuario='" . $_REQUEST["idusuario"] . "' && idmodulo='" . $_REQUEST["idmodulo"] . "'";
      $res2 = $link->query($sql2);
    }
    echo $tipo;
    break;
    /*****************************
Operaciones menu
     *****************************/
  case 'nuevomenu':
    $sql = "insert into menus(nombre,icono)values('" . $_REQUEST["menu"] . "','" . $_REQUEST["icono"] . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminarmenu':
    $sql0 = "select * from modulos where idmenu='" . $_REQUEST["idmenu"] . "'";
    $res0 = $link->query($sql0);
    while ($fila0 = mysqli_fetch_array($res0)) {
      $sql1 = "delete from permisosusuarios where idmodulo='" . $fila0["id"] . "'";
      $res1 = $link->query($sql1);
    }
    $sql2 = "delete from modulos where idmenu='" . $_REQUEST["idmenu"] . "'";
    $res2 = $link->query($sql2);
    $sql = "delete from menus where id='" . $_REQUEST["idmenu"] . "'";
    $res = $link->query($sql);
    break;
  case 'editarmenu':
    $sql = "update menus set nombre='" . $_REQUEST["nombre"] . "', icono='" . $_REQUEST["icon"] . "' where id='" . $_REQUEST["id"] . "'";
    $res = $link->query($sql);
    break;

    /******************************
Operaciones modulos
     ******************************/
  case 'nuevomodulo':
    $sql = "insert into modulos(idmenu,nombre)values('" . $_REQUEST["idmenu"] . "','" . $_REQUEST["modulo"] . "')";
    $res = $link->query($sql);
    try {
      $nameFile = quitarAcentosEspacios($_REQUEST["modulo"]);
      $archivo = fopen("modulos/{$nameFile}.php", "w+b");
      if ($archivo == false) {
        echo "Error al crear el archivo";
      } else {
        // Escribir en el archivo:
        fwrite($archivo, "<?php");
        fwrite($archivo, "echo {$_REQUEST["modulo"]};");
        fwrite($archivo, "?>");
        // Fuerza a que se escriban los datos pendientes en el buffer:
        fflush($archivo);
      }
      fclose($archivo);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }

    $sale_a = $_REQUEST["retornar"];
    break;

  case 'borrarmodulo':
    $sql = "delete from permisosusuarios where idmodulo='" . $_REQUEST["idmodulo"] . "'";
    $res = $link->query($sql);
    $sql2 = "delete from modulos where id='" . $_REQUEST["idmodulo"] . "'";
    $res2 = $link->query($sql2);
    break;

  case 'editarmodulo':
    $sql = "update modulos set nombre='" . $_REQUEST["nombre"] . "',idmenu='" . $_REQUEST["idmenu"] . "' where id='" . $_REQUEST["id"] . "'";
    $res = $link->query($sql);
    break;

    /*************************************************************
OPERACIONES APP ESTANDAR
     *************************************************************/
  case 'login':
    $sql = "select * from conductores where con_rut='" . $_REQUEST["usuario"] . "' && con_clave='" . md5($_REQUEST["clave"]) . "' && con_cliente != 1";
    $res = $link->query($sql);
    $cuenta = mysqli_num_rows($res);
    if ($cuenta > 0) {
      while ($fila = mysqli_fetch_array($res)) {
        $patentes = getPatentes($fila["con_cliente"]);
        $jornada = getInforJornada($fila["con_id"]);
        $datos["success"] = true;
        $datos["idusuario"] = $fila["con_id"];
        $datos["nombre"] = $fila["con_nombre"];
        $datos["rut"] = $fila["con_rut"];
        $datos["empresa"] = obtenervalor("clientes", "razonsocial", "where id='" . $fila["con_cliente"] . "'");
        $datos["patentes"] = $patentes;
        $datos["jornada"] = $jornada;
      }
    } else {
      $datos["success"] = false;
      $datos["error"] = "Usuario no identificado";
    }
    echo json_encode($datos);
    break;



  case 'RegistrarJornada':
    $hoy = getdate();
    $hora = $hoy["hours"] . ":" . $hoy["minutes"];
    $tipo = $_REQUEST["tipo"];
    $conductor = obtenervalor("conductores", "con_nombre", "where con_id='" . $_REQUEST["conductor"] . "'");
    if ($tipo == "inicio") {
      $idtipo = 1;
      $vehestado = 1;
      $info = "El conductor " . $conductor . " ha iniciado su jornada con fecha " . date("d/m/Y") . " a las " . $hora . " , patente " . $_REQUEST["patente"] . "";
      $asunto = "inicio de jornada";
      $pin = obtenervalor("conductores", "con_pin", "where con_id='" . $_REQUEST["conductor"] . "'");
    } else {
      $idtipo = 2;
      $vehestado = 0;
      $info = "El conductor " . $conductor . " ha finalizado su jornada con fecha " . date("d/m/Y") . " a las " . $hora . " , patente " . $_REQUEST["patente"] . "";
      $asunto = "fin de jornada";
      $pin = "00000";
    }
    $idpatente = obtenervalor("vehiculos", "veh_id", "where veh_patente='" . $_REQUEST["patente"] . "'");
    // buscar si otro conductor ya inicio jornada con la patente
    $sql = "insert into historialdejornadas(hdj_conductor,hdj_patente,hdj_tipo,hdj_app)values('" . $_REQUEST["conductor"] . "','" . $idpatente . "','" . $idtipo . "',1)";
    $res = $link->query($sql);
    //cambiar estado a patente 
    $sql2 = "update vehiculos set veh_estado=" . $vehestado . ", veh_sesion=0  where veh_id='" . $idpatente . "' and deleted_at is NULL";
    $res2 = $link->query($sql2);
    // $sql3="update vehiculos set  where veh_patente='".$_REQUEST["patente"]."'";
    // $res3=$link->query($sql3);

    /*
//$correos=array("jarayam@cloux.cl","sac@cloux.cl","luisberbesi@pipau.cl","fabianfuentesg@gmail.com");
$correos=array("sac@cloux.cl","luisberbesi@pipau.cl");
//$correos=array("jarayam@cloux.cl");
$plantilla = PlantillaNotificacionJornada("Usuario Notifiaciones",$info);
$correo = new PHPMailer();
$correo->SetFrom("jarayam@cloux.cl", "Asistencia Cloux");
//Creamos una instancia en lugar usar mail()
foreach ($correos as $index=>$valor){
$correo->AddAddress($valor, "Cloux");
$correo->Subject = $asunto;
$correo->MsgHTML($plantilla);
$correo->CharSet = 'UTF-8';
if(!$correo->Send()) {
//echo "Hubo un error: " . $correo->ErrorInfo;
}
else{
//echo "Correo enviado";
}
}
*/
    $dataempresa = getDataEmpresa($_REQUEST["conductor"]);
    $cliente = new nusoap_client("http://www.flotasnet.com/servicios/ConfiguracionVehiculos.asmx?wsdl", true);
    $cliente->soap_defencoding = 'UTF-8';
    $cliente->timeout = 1800; // 10 minutos para resolver
    $cliente->response_timeout = 1800; // 10 minutos para esperar la 
    $cabecera = '<AuthHeader xmlns="http://212.8.96.37/webservices/">
      <Username>' . $dataempresa["usuario"] . '</Username>
      <Password>' . $dataempresa["clave"] . '</Password>
      <Empresa>' . $dataempresa["nombre"] . '</Empresa>
    </AuthHeader>';
    $cliente->setHeaders($cabecera);
    //$letras = substr($_REQUEST["patente"],0,4);
    //$digitos = substr($_REQUEST["patente"],4,2);
    //$formatpatente = formatpatente($_REQUEST["patente"]);
    $datos = '<AsignacionConductor xmlns="http://212.8.96.37/webservices/">
      <vehicle>' . $_REQUEST["patente"] . '</vehicle>
      <conductor>' . $pin . '</conductor>
    </AsignacionConductor>';
    $consultar = $cliente->call("AsignacionConductor", $datos);
    $data = utf8_encode($consultar["AsignacionConductorResult"]);
    $sql2 = "insert into datapostws(dpws_patente,dpws_conductor,dpws_pin,dpws_resultado)values('" . $_REQUEST["patente"] . "','" . $conductor . "','" . $pin . "','" . $data . "')";
    $res2 = $link->query($sql2);

    $resultado["success"] = true;


    echo json_encode($resultado);
    break;

  case 'getInfoJornadas':
    //$sql="select * from historialdejornadas where date(hdj_fechahora)='".date("Y-m-d")."' && hdj_conductor='".$_REQUEST["conductor"]."'";
    $sql = "select * from historialdejornadas where  hdj_conductor='" . $_REQUEST["conductor"] . "'  order by hdj_id desc limit 0,1";
    $res = $link->query($sql);
    $cuenta = mysqli_num_rows($res);
    $datos["inicio"] = true;
    $datos["fin"] = false;
    if ($cuenta > 0) {
      $fila = mysqli_fetch_array($res);
      if ($fila["hdj_tipo"] == 1) {
        $datos["inicio"] = false;
        $datos["fin"] = true;
      }
      if ($fila["hdj_tipo"] == 2) {
        $datos["inicio"] = true;
        $datos["fin"] = false;
      }
    }

    echo json_encode($datos);
    break;

  case 'getSessionPatente':
    $sql = "select * from vehiculos where deleted_at is NULL and veh_patente ='" . $_REQUEST["patente"] . "' && veh_sesion=1";
    $res = $link->query($sql);
    $cuenta = mysqli_num_rows($res);
    if ($cuenta > 0) {
      $retornar["inicio"] = false;
    } else {
      $sql = "update vehiculos set veh_sesion=1 where veh_patente='" . $_REQUEST["patente"] . "' and deleted_at is NULL";
      $res = $link->query($sql);
      $retornar["inicio"] = true;
    }
    echo json_encode($retornar);
    break;

  case 'getTabAlertas':
    $sql = "select * from alertas";
    $res = $link->query($sql);
    $alertas = array();
    while ($fila = mysqli_fetch_array($res)) {
      $patentes = array();
      $sql2 = "select * from patentesxalerta where pxa_alerta='" . $fila["ale_id"] . "'";
      $res2 = $link->query($sql2);
      while ($fila2 = mysqli_fetch_array($res2)) {
        $patentes[] = $fila2["pxa_patente"];
      }
      $contactos = array();
      $sql3 = "select * from contactosxalerta where cxa_alerta='" . $fila["ale_id"] . "'";
      $res3 = $link->query($sql3);
      while ($fila3 = mysqli_fetch_array($res3)) {
        $contactos[] = array("nombre" => $fila3["cxa_nombre"], "correo" => $fila3["cxa_correo"]);
      }
      $alertas[$fila["ale_id"]]["tipo"] = $fila["ale_tipo"];
      $cliente = obtenervalor("clientes", "razonsocial", "where id='" . $fila["ale_cliente"] . "'");
      if ($fila["ale_grupo"] != 0) {
        $grupo = obtenervalor("grupos", "gru_nombre", "where gru_id='" . $fila["ale_grupo"] . "'");
      } else {
        $grupo = "SIN GRUPO";
      }
      $alertas[$fila["ale_id"]]["cliente"] = $cliente;
      $alertas[$fila["ale_id"]]["grupo"] = $grupo;
      $alertas[$fila["ale_id"]]["patentes"] = $patentes;
      $alertas[$fila["ale_id"]]["contactos"] = $contactos;
    }
    echo json_encode($alertas);
    break;

  case 'getVehxGrupo':
    //$sql="select veh_id,veh_patente from  vehiculos where veh_grupo = '".$_REQUEST["idgrupo"]."' && veh_alerta=0";
    $sql = "select veh_id,veh_patente from  vehiculos where veh_grupo = '" . $_REQUEST["idgrupo"] . "' and deleted_at is NULL";
    $res = $link->query($sql);
    $vehiculos = array();
    while ($fila = mysqli_fetch_array($res)) {
      $vehiculos[] = array("idveh" => $fila["veh_id"], "patente" => $fila["veh_patente"]);
    }
    $alertas = getAlertasxG($_REQUEST["idgrupo"]);
    $json = array("vehiculos" => $vehiculos, "alertas" => $alertas);
    echo json_encode($json);
    break;

  case 'getVehxCuenta':
    //$sql="select veh_id,veh_patente from  vehiculos where veh_cliente = '".$_REQUEST["idcliente"]."' && veh_alerta=0";
    $sql = "select veh_id,veh_patente from  vehiculos where veh_cliente = '" . $_REQUEST["idcliente"] . "' and deleted_at is NULL";
    $res = $link->query($sql);
    $vehiculos = array();
    while ($fila = mysqli_fetch_array($res)) {
      $vehiculos[] = array("idveh" => $fila["veh_id"], "patente" => $fila["veh_patente"]);
    }
    //$alertas = getAlertasxG($_REQUEST["idgrupo"]);
    $json = array("vehiculos" => $vehiculos);
    echo json_encode($json);
    break;


  case 'registrarConfigAlertaIJ':
    $datos = json_decode($_REQUEST["alerta"], true);
    $sql = "insert into alertas(ale_tipo,ale_cliente,ale_grupo)values('" . $datos["tipo"] . "','" . $datos["cliente"] . "','" . $datos["grupo"] . "')";
    $res = $link->query($sql);
    $id = $link->insert_id;
    $patentes = isset($datos["patentes"]) ? $datos["patentes"] : [];
    // echo "<pre>";
    // print_r($patentes);
    // echo "<pre>";
    foreach ($patentes as $index => $valor) {
      $sql1 = "insert into patentesxalerta(pxa_alerta,pxa_idpatente,pxa_patente)values('" . $id . "','" . $valor["id"] . "','" . $valor["patente"] . "')";
      $res1 = $link->query($sql1);
      $sql3 = "update vehiculos set veh_alerta = '" . $id . "' where veh_id='" . $valor["id"] . "' and deleted_at is NULL";
      $res3 = $link->query($sql3);
    }
    $contactos = $datos["contactos"];
    foreach ($contactos as $index => $valor) {
      $sql2 = "insert into contactosxalerta(cxa_alerta,cxa_nombre,cxa_correo)values('" . $id . "','" . $valor["nombre"] . "','" . $valor["correo"] . "')";
      $res2 = $link->query($sql2);
    }

    break;
  case 'eliminarConfigAlertaIJ':
    $sql = "delete from contactosxalerta where cxa_alerta='" . $_REQUEST["alerta"] . "'";
    $res = $link->query($sql);
    $sql1 = "select pxa_idpatente from patentesxalerta where pxa_alerta='" . $_REQUEST["alerta"] . "'";
    $res1 = $link->query($sql1);
    while ($fila1 = mysqli_fetch_array($res1)) {
      $sql2 = "update vehiculos set veh_alerta=0 where veh_id='" . $fila1["pxa_idpatente"] . "' and deleted_at is NULL";
      $res2 = $link->query($sql2);
    }
    $sql3 = "delete from patentesxalerta where pxa_alerta='" . $_REQUEST["alerta"] . "'";
    $res3 = $link->query($sql3);
    $sql4 = "delete from alertas where ale_id='" . $_REQUEST["alerta"] . "'";
    $res4 = $link->query($sql4);

    break;

    /************************************************************
OPERACIONES INVENTARIO
     *************************************************************/
  case 'getSubfamilias':
    $opciones = "";
    $sql = "select * from subfamilias where sfam_familia='" . $_REQUEST["familia"] . "'";
    $res = $link->query($sql);
    while ($fila = mysqli_fetch_array($res)) {
      $opciones .= "<option value='" . $fila["sfam_id"] . "'>" . $fila["sfam_nombre"] . "</option>";
    }
    echo $opciones;
    break;
  case 'generarCodigo':
    $sql = "select count(*) as total from productos";
    $res = $link->query($sql);
    $fila = mysqli_fetch_array($res);
    if ($fila["total"] > 0) {
      $codigo = $fila["total"] + 1;
      echo str_pad($codigo, 6, "0", STR_PAD_LEFT);
    } else {
      echo "000001";
    }
    break;
    // SENSORES
  case 'nuevoSensor':
    $sql    = "insert into sensores (sen_nombre,sen_estado1,sen_estado2,sen_create_at) values ('{$_REQUEST['nombre']}','{$_REQUEST['estado1']}','{$_REQUEST['estado2']}','{$fechachile}')";
    $res    = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];

    break;

  case 'editarSensor':
    $sql = "update sensores set sen_nombre='" . $_REQUEST["nombre"] . "',sen_estado1='" . $_REQUEST["estado1"] . "',sen_estado2='" . $_REQUEST["estado2"] . "' where sen_id='" . $_REQUEST["idsen"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminarSensor':
    $sql = "delete from sensores where sen_id='" . $_REQUEST["idsen"] . "'";
    $res = $link->query($sql);
    break;



    // FAMILIAS
  case 'nuevafamilia':
    $sql = "insert into familias(fam_nombre)values('" . $_REQUEST["nombre"] . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'editarFAM':
    $sql = "update familias set fam_nombre='" . $_REQUEST["nombre"] . "' where fam_id='" . $_REQUEST["idfam"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminarFAM':
    $sql = "delete from familias where fam_id='" . $_REQUEST["idfam"] . "'";
    $res = $link->query($sql);
    break;
    // SUBFAMILIAS
  case 'nuevasubfamilia':
    if ($_REQUEST["familia"] == '' || $_REQUEST["familia"] == null) {
      $_REQUEST["familia"] = 0;
    }
    $sql = "insert into subfamilias(sfam_familia,sfam_nombre)values(" . $_REQUEST["familia"] . ",'" . $_REQUEST["nombre"] . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    // echo $sql;
    break;

  case 'editarsubfamilia':
    $sql = "update subfamilias set sfam_familia='" . $_REQUEST["familia"] . "', sfam_nombre='" . $_REQUEST["nombre"] . "' where sfam_id='" . $_REQUEST["idsfam"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;

  case 'eliminarsubfamilia':
    $sql = "delete from subfamilias where sfam_id='" . $_REQUEST["idsfam"] . "'";
    $res = $link->query($sql);
    break;
    // SUBESTADO EQUIPOS
  case 'nuevosubestado':
    $sql = "insert into subestado_equipos(sub_nombre)values('" . $_REQUEST["nombre"] . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'editarsubestado':
    $sql = "update subestado_equipos set sub_nombre='" . $_REQUEST["nombre"] . "' where sub_id='" . $_REQUEST["idmar"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminarsubestado':
    $sql = "delete from subestado_equipos where sub_id='" . $_REQUEST["idmar"] . "'";
    $res = $link->query($sql);
    break;
    // MARCAS
  case 'nuevamarca':
    $sql = "insert into marcas(mar_nombre)values('" . $_REQUEST["nombre"] . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'editarmarca':
    $sql = "update marcas set mar_nombre='" . $_REQUEST["nombre"] . "' where mar_id='" . $_REQUEST["idmar"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminarmarca':
    $sql = "delete from marcas where mar_id='" . $_REQUEST["idmar"] . "'";
    $res = $link->query($sql);
    break;
    // SERVICIOS
  case 'nuevoservicio':
    $sql = "insert into servicios(ser_nombre)values('" . $_REQUEST["nombre"] . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'editarservicio':
    $sql = "update servicios set ser_nombre='" . $_REQUEST["nombre"] . "' where ser_id='" . $_REQUEST["idmar"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminarservicio':
    $sql = "delete from servicios where ser_id='" . $_REQUEST["idmar"] . "'";
    $res = $link->query($sql);
    break;

  case 'nuevoproducto':
    $serie = 0;
    if ($_REQUEST["serie"] == '') {
      $serie = 0;
    } else {
      $serie = $_REQUEST["serie"];
    }

    if ($_REQUEST["marca"] == '' || $_REQUEST["marca"] == null) {
      $_REQUEST["marca"] = 0;
    }

    if ($_REQUEST["sminimo"] == '' || $_REQUEST["sminimo"] == null) {
      $_REQUEST["sminimo"] = 0;
    }

    if ($_REQUEST["codigo"] == '' || $_REQUEST["codigo"] == null) {
      $_REQUEST["codigo"] = 0;
    }
    $sql = "insert into productos(pro_codigo,pro_serie,pro_familia,pro_subfamilia,pro_marca,pro_nombre,pro_stockminimo)values('" . $_REQUEST["codigo"] . "'," . $serie . "," . $_REQUEST["familia"] . "," . $_REQUEST["subfamilia"] . "," . $_REQUEST["marca"] . ",'" . $_REQUEST["nombre"] . "'," . $_REQUEST["sminimo"] . ")";
    /*echo $sql;
die();*/
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;

  case 'getTabProductos':
    $sql = "select pro.*,fam.fam_nombre as familia, sfam.sfam_nombre as subfamilia, mar.mar_nombre as marca, (select count(ser_id) from serie_guia where pro_id = pro.pro_id and ser_instalado = 0 and ser_estado = 1) as cantidad 
      from productos pro 
      left outer join familias fam on pro.pro_familia = fam.fam_id 
      left outer join subfamilias sfam on pro.pro_subfamilia = sfam.sfam_id 
      left outer join marcas mar on pro.pro_marca = mar.mar_id 
      order by pro.pro_nombre";
    $res        = $link->query($sql);
    $productos  = array();
    while ($fila = mysqli_fetch_array($res)) {

      $detallestock = getStockxProducto($fila["pro_id"], $fila["pro_serie"]);
      $productos[]  = array("idpro" => $fila["pro_id"], "codigo" => $fila["pro_codigo"], "proserie" => $fila["pro_serie"], "idfam" => $fila["pro_familia"], "familia" => $fila["familia"], "idsfam" => $fila["pro_subfamilia"], "subfamilia" => $fila["subfamilia"], "idmar" => $fila["pro_marca"], "marca" => $fila["marca"], "nombre" => $fila["pro_nombre"], "sminimo" => $fila["pro_stockminimo"], "stock" => $detallestock["stock"], "cantidad" => $fila["cantidad"], "precio" => $fila["pro_valor"], "detallestock" => $detallestock);
    }
    mysqli_close($link);
    echo json_encode($productos);
    break;

  case 'addts':
    $recibe     = json_decode($_REQUEST['envio'], true);
    $sql        = "select * from servicios where ts_estado = 1 and ser_nombre = '{$recibe['nom']}'";
    $res        = $link->query($sql);
    $fila       = mysqli_fetch_array($res);

    if ($recibe['opc'] == 0) {
      if ($fila['ser_id'] != '') {
        $devuelve = array('logo' => 'success', 'mensaje' => 'ingresado correctamente', 'repetido' => 1);
      } else {

        $sql0 = "select max(ser_id)+1 as con from servicios";
        $res0 = $link->query($sql0);
        $fila0 = mysqli_fetch_array($res0);

        $sql1 = "insert into servicios (ser_id,ser_nombre,ts_estado) values ({$fila0['con']},'{$recibe['nom']}',1)";
        $res1 = $link->query($sql1);
        if ($res) {
          $devuelve = array('logo' => 'success', 'mensaje' => 'Ingresado correctamente', 'repetido' => $sql1);
        } else {
          $devuelve = array('logo' => 'error', 'mensaje' => 'No se ha podido ingresar el tipo de servicio', 'repetido' => $sql1);
        }
      }
    } else if ($recibe['opc'] == 1) {
      $sql1 = "update servicios set ser_nombre = '{$recibe['nom']}' where ser_id = {$recibe['idts']}";
      $res1 = $link->query($sql1);
      if ($res) {
        $devuelve = array('logo' => 'success', 'mensaje' => 'Actualizado correctamente', 'repetido' => 0);
      } else {
        $devuelve = array('logo' => 'error', 'mensaje' => 'No se ha podido actualizar el tipo de servicio', 'repetido' => 0);
      }
    } else if ($recibe['opc'] == 2) {
      $sql1 = "DELETE FROM `servicios` WHERE ser_id =  {$recibe['idts']}";
      $res1 = $link->query($sql1);
      if ($res) {
        $devuelve = array('logo' => 'success', 'mensaje' => 'Eliminado correctamente', 'repetido' => 0);
      } else {
        $devuelve = array('logo' => 'error', 'mensaje' => 'No se ha podido eliminar el tipo de servicio', 'repetido' => 0);
      }
    }

    echo json_encode($devuelve);
    break;

  case 'newgetInventarioxProducto':

    $sql = "select t1.*, t2.per_nombrecorto, t3.pro_nombre
            from serie_guia t1
            left outer join personal t2 on t2.per_id = t1.usu_id_cargo
            left outer join productos t3 on t3.pro_id = t1.pro_id
            where t1.ser_estado = 1 and t1.pro_id = " . $_REQUEST['id'] . " and ser_instalado = 0";
    $res        = $link->query($sql);
    $productos  = array();
    while ($fila = mysqli_fetch_array($res)) {
      array_push($productos, array("ser_id" => $fila["ser_id"], "idpro" => $fila["pro_id"], "ser_codigo" => $fila["ser_codigo"], "proserie" => $fila["pro_serie"], "bodega" => $fila["per_nombrecorto"], "ser_condicion" => $fila["ser_condicion"], "pro_nombre" => $fila["pro_nombre"], "cargo" => $fila["usu_id_cargo"]));
    }
    mysqli_close($link);
    echo json_encode($productos);

    break;

  case 'updateestado':

    $recibe = json_decode($_REQUEST['envio'], true);
    $sql    = "update serie_guia set ser_estado = {$recibe['valor']} where ser_id = {$recibe['idserie']}";
    $res    = $link->query($sql);

    if ($res) {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Estado actualizado correctamente', 'sta' => $sql);
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error', 'sta' => $sql);
    }

    echo json_encode($devuelve);

    break;

  case 'getInventarioxProducto':
    $idpro = $_REQUEST["id"];
    $proserie = $_REQUEST["serie"];
    $npro = 0;
    $prostock = 0;
    //$stockpro=obtenervalor("productos","pro_stock","where pro_id='".$idpro."'");
    $series = array();
    $prosinseriemalos = array();
    if ($proserie == 1) {
      $sql = "select cxp_codigo,cxp_estado,cxp_info from codigosxproducto where cxp_producto='" . $idpro . "' && (cxp_estado=1 || cxp_estado=3)";
      $res = $link->query($sql);
      while ($fila = mysqli_fetch_array($res)) {
        if (intval($fila["cxp_estado"]) == 1) {
          $estado = "<span class='text-success'><i class='fa fa-check' aria-hidden='true'></i></span>";
          $info = "";
        } else {
          $estado = "<span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span>";
          $info = $fila["cxp_info"];
        }
        $series[$fila["cxp_codigo"]] = array("estado" => $estado, "info" => $info);
      }
      $npro = count($series);
    } else {
      // stock de productos buenos 
      $npro = obtenervalor("productos", "pro_stock", "where pro_id='" . $idpro . "'");
      $prostock = $npro;
      // buscar productos malos
      $sql = "select cxp_cantidad,cxp_info from codigosxproducto where cxp_producto='" . $idpro . "' &&  cxp_estado = 3";
      $res = $link->query($sql);
      if (mysqli_num_rows($res) > 0) {
        $fila = mysqli_fetch_array($res);
        $prosinseriemalos = array("cantidad" => $fila["cxp_cantidad"], "estado" => "<span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span>", "info" => $fila["cxp_info"]);
        $npro = $npro + $fila["cxp_cantidad"];
      }
    }
    // productos x tecnico
    $sql1 = "select * from productosxtecnico  where pxt_idpro='" . $idpro . "'";
    $res1 = $link->query($sql1);
    $pxt = array();
    if ($proserie == 1) {
      $pxtcantidad = mysqli_num_rows($res1);
      while ($fila1 = mysqli_fetch_array($res1)) {
        $tecnico = obtenervalor("personal", "per_nombrecorto", "where per_id='" . $fila1["pxt_idtecnico"] . "'");
        if ($tecnico != "") {
          $pxt[] = array("tecnico" => $tecnico, "cantidad" => 1, "serie" => $fila1["pxt_nserie"], "estado" => $fila1["pxt_estado"]);
        }
      }
    } else {
      $pxtcantidad = 0;
      while ($fila1 = mysqli_fetch_array($res1)) {
        $tecnico = obtenervalor("personal", "per_nombrecorto", "where per_id='" . $fila1["pxt_idtecnico"] . "'");
        if ($tecnico != "") {
          $pxt[] = array("tecnico" => $tecnico, "cantidad" => $fila1["pxt_cantidad"], "serie" => "", "estado" => $fila1["pxt_estado"]);
        }
      }
    }

    // productos instalados en vehiculos
    $sql2 = "select * from productosxvehiculos where pxv_idpro='" . $idpro . "'";
    $res2 = $link->query($sql2);
    $pxv = array();
    while ($fila2 = mysqli_fetch_array($res2)) {
      $patente = obtenervalor("vehiculos", "veh_patente", "where veh_id='" . $fila2["pxv_idveh"] . "'");
      if ($fila2["pxv_cantidad"] == 0) {
        $cantidad = 1;
      } else {
        $cantidad = $fila2["pxv_cantidad"];
      }
      $pxv[] = array("patente" => $patente, "cantidad" => $cantidad, "serie" => $fila2["pxv_nserie"]);
    }
    $spro = $npro + $pxtcantidad + count($pxv) + intval($prosinseriemalos[0]["cantidad"]);
    $inventario["stock"] = $spro;
    $inventario["npro"] = $prostock;
    $inventario["series"] = $series;
    $inventario["pxtcantidad"] = $pxtcantidad;
    $inventario["pxtcantidadmalo"] = $prosinseriemalos;
    $inventario["pxt"] = $pxt;
    $inventario["pxv"] = $pxv;

    /*$inventario["idpro"]=$idpro;
$inventario["stock"]=$stockpro;
$inventario["series"]=$series;
$inventario["pxtcantidad"]=$pxtcantidad;
$inventario["pxt"]=$pxt;
$inventario["pxv"]=$pxv;*/
    mysqli_close($link);
    echo json_encode($inventario);
    break;

  case 'getProTec':

    $sql = "SELECT t1.*, t2.pro_nombre, t2.pro_serie, t3.razonsocial, if(t2.pro_serie=1, 'SI','NO') as tieneserie
          FROM serie_guia t1
          LEFT OUTER join productos t2 on t2.pro_id = t1.pro_id
          LEFT OUTER join proveedores t3 on t3.id = t1.prov_id
          where t1.ser_estado = 1 and ser_instalado = 0 and t1.usu_id_cargo = {$_REQUEST["idtec"]}";
    $res   = $link->query($sql);
    $envia = array();

    foreach ($res as $key) {
      array_push($envia, array('serie' => $key['ser_codigo'], 'idserie' => $key['ser_id'], 'proid' => $key['pro_id'], 'idcondicion' => $key['ser_condicion'], 'proname' => $key['pro_nombre'], 'tieneserie' => $key['tieneserie']));
    }
    mysqli_close($link);
    echo json_encode($envia);

    break;

  case 'enviartracking':

    $recibe = json_decode($_REQUEST["envio"], true);

    $sql   = "SELECT * FROM traspasos_series where tra_id = {$recibe['idtraspaso']}";
    $res   = $link->query($sql);
    if ($recibe['nseguimiento'] == '' || $recibe['nseguimiento'] == null) {
      $nseg .= ' , tra_tracking_codigo = ""';
    } else {
      $nseg .= ' , tra_tracking_codigo = "' . $recibe['nseguimiento'] . '"';
    }

    if ($recibe['nombretra'] == '' || $recibe['nombretra'] == null) {
      $nseg .= ' , tra_tracking_courrier = ""';
    } else {
      $nseg .= ' , tra_tracking_courrier = "' . $recibe['nombretra'] . '"';
    }

    if ($recibe['recibecou'] == '' || $recibe['recibecou'] == null) {
      $nseg .= ' , tra_tracking_recibe = ""';
    } else {
      $nseg .= ' , tra_tracking_recibe = "' . $recibe['recibecou'] . '"';
    }

    $sql3   = "update traspasos_series set tra_tracking = {$recibe['valor']}, tra_tracking_fecha = '{$fecha}' {$nseg} where tra_id = {$recibe['idtraspaso']}";
    $res3   = $link->query($sql3);

    $nseg = '';
    foreach ($res as $key) {
      $varpas = json_decode($key['tra_detalle'], true);
      if ($recibe['nseguimiento'] == '' || $recibe['nseguimiento'] == null) {
        $nseg .= ' , ser_tracking_codigo = ""';
      } else {
        $nseg .= ' , ser_tracking_codigo = "' . $recibe['nseguimiento'] . '"';
      }

      if ($recibe['nombretra'] == '' || $recibe['nombretra'] == null) {
        $nseg .= ' , ser_tracking_courrier = ""';
      } else {
        $nseg .= ' , ser_tracking_courrier = "' . $recibe['nombretra'] . '"';
      }

      if ($recibe['recibecou'] == '' || $recibe['recibecou'] == null) {
        $nseg .= ' , ser_tracking_recibe = ""';
      } else {
        $nseg .= ' , ser_tracking_recibe = "' . $recibe['recibecou'] . '"';
      }
      foreach ($varpas as $key2) {
        $sql2 = "update serie_guia set ser_tracking = {$recibe['valor']}, ser_tracking_fecha = '{$fecha}' {$nseg} where pro_id = {$key2['idpro']} and ser_id = {$key2['id']}";
        $res2 = $link->query($sql2);
      }
    }
    $devuelve = array();
    if ($res2) {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Tracking enviado correctamente', 'pri' => $sql3);
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error al enviar el tracking', 'pri' => $sql3);
    }
    mysqli_close($link);
    echo json_encode($devuelve);

    break;

  case 'enviartrackingdev':

    $recibe = json_decode($_REQUEST["envio"], true);

    $sql   = "SELECT * FROM detalledevolucion where dev_id = {$recibe['iddev']}";
    $res   = $link->query($sql);

    if ($recibe['nseguimiento'] == '' || $recibe['nseguimiento'] == null) {
      /* $nseg .= ' , dev_tracking_codigo = ""';*/
      $nseg .= '';
    } else {
      $nseg .= ' , dev_tracking_codigo = "' . $recibe['nseguimiento'] . '"';
    }

    if ($recibe['nombretra'] == '' || $recibe['nombretra'] == null) {
      /*$nseg .= ' , dev_tracking_courrier = ""';*/
      $nseg .= '';
    } else {
      $nseg .= ' , dev_tracking_courrier = "' . $recibe['nombretra'] . '"';
    }

    if ($recibe['recibecou'] == '' || $recibe['recibecou'] == null) {
      /*$nseg .= ' , dev_tracking_recibe = ""';*/
      $nseg .= '';
    } else {
      $nseg .= ' , dev_tracking_recibe = "' . $recibe['recibecou'] . '"';
    }

    $sql3   = "update devoluciones set dev_tracking = {$recibe['valor']}, dev_tracking_fecha = '{$fecha}' {$nseg} where dev_id = {$recibe['iddev']}";
    $res3   = $link->query($sql3);


    foreach ($res as $key) {
      $nseg = '';
      if ($recibe['nseguimiento'] == '' || $recibe['nseguimiento'] == null) {
        /*$nseg .= ' , ddev_tracking_codigo = ""';*/
        $nseg .= '';
      } else {
        $nseg .= ' , ddev_tracking_codigo = "' . $recibe['nseguimiento'] . '"';
      }

      if ($recibe['nombretra'] == '' || $recibe['nombretra'] == null) {
        /*$nseg .= ' , ddev_tracking_courrier = ""';*/
        $nseg .= '';
      } else {
        $nseg .= ' , ddev_tracking_courrier = "' . $recibe['nombretra'] . '"';
      }

      if ($recibe['recibecou'] == '' || $recibe['recibecou'] == null) {
        /*$nseg .= ' , ddev_tracking_recibe = ""';*/
        $nseg .= '';
      } else {
        $nseg .= ' , ddev_tracking_recibe = "' . $recibe['recibecou'] . '"';
      }

      $sql3 = "update detalledevolucion set ddev_tracking = {$recibe['valor']}, ddev_tracking_fecha = '{$fecha}' {$nseg} where ddev_id = {$key['ddev_id']} and dev_id = {$key['dev_id']}";
      $res3 = $link->query($sql3);

      $nseg = '';
      if ($recibe['nseguimiento'] == '' || $recibe['nseguimiento'] == null) {
        /*$nseg .= ' , ser_tracking_codigo = ""';*/
        $nseg .= '';
      } else {
        $nseg .= ' , ser_tracking_codigo = "' . $recibe['nseguimiento'] . '"';
      }

      if ($recibe['nombretra'] == '' || $recibe['nombretra'] == null) {
        /*$nseg .= ' , ser_tracking_courrier = ""';*/
        $nseg .= '';
      } else {
        $nseg .= ' , ser_tracking_courrier = "' . $recibe['nombretra'] . '"';
      }

      if ($recibe['recibecou'] == '' || $recibe['recibecou'] == null) {
        /*$nseg .= ' , ser_tracking_recibe = ""';*/
        $nseg .= '';
      } else {
        $nseg .= ' , ser_tracking_recibe = "' . $recibe['recibecou'] . '"';
      }

      $sql2 = "update serie_guia set ser_tracking = {$recibe['valor']}, ser_tracking_fecha = '{$fecha}' {$nseg} where pro_id = {$key['pro_id']} and ser_id = {$key['ser_id']}";
      $res2 = $link->query($sql2);
    }
    $devuelve = array();
    if ($res2) {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Tracking enviado correctamente', 'pri' => $sql3);
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error al enviar el tracking', 'pri' => $sql3);
    }
    mysqli_close($link);
    echo json_encode($devuelve);

    break;

  case 'getTabProductosxSUC':
    $sql = "select pxs.*,pro.pro_codigo,pro.pro_nombre,pro.pro_valor from productosxsucursal pxs left outer join productos pro on pxs.pxs_producto = pro.pro_id";
    $res = $link->query($sql);
    $cuenta = mysqli_num_rows($res);
    $productos = array();
    if ($cuenta > 0) {
      while ($fila = mysqli_fetch_array($res)) {
        $sucursal = obtenervalor("sucursales", "suc_nombre", "where suc_id='" . $fila["pxs_sucursal"] . "'");
        $productos[] = array("idpxs" => $fila["pxs_id"], "idsuc" => $fila["pxs_sucursal"], "sucursal" => $sucursal, "idpro" => $fila["pxs_producto"], "codigo" => $fila["pro_codigo"], "producto" => $fila["pro_nombre"], "stock" => $fila["pxs_stock"], "valor" => $fila["pro_valor"]);
      }
    }
    echo json_encode($productos);
    break;
  case 'editarproducto':
    $serie = 0;
    $marca = 0;
    $familia = 0;
    $subfamilia = 0;
    $sminimo = 0;
    if ($_REQUEST["serie"] == '') {
      $serie = 0;
    } else {
      $serie = $_REQUEST["serie"];
    }
    if ($_REQUEST["marca"] == '') {
      $marca = 0;
    } else {
      $marca = $_REQUEST["marca"];
    }
    if ($_REQUEST["familia"] == '') {
      $familia = 0;
    } else {
      $familia = $_REQUEST["familia"];
    }
    if ($_REQUEST["subfamilia"] == '') {
      $subfamilia = 0;
    } else {
      $subfamilia = $_REQUEST["subfamilia"];
    }
    if ($_REQUEST["sminimo"] == '') {
      $sminimo = 0;
    } else {
      $sminimo = $_REQUEST["sminimo"];
    }
    $sql = "update productos set pro_codigo='" . $_REQUEST["codigo"] . "',pro_serie=" . $serie . ",pro_familia=" . $familia . ",pro_subfamilia=" . $subfamilia . ",pro_marca=" . $marca . ",pro_nombre='" . $_REQUEST["nombre"] . "',pro_stockminimo=" . $sminimo . " where pro_id=" . $_REQUEST["idpro"] . "";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    // echo $sql;
    break;

  case 'eliminarproducto':
    $sql = "delete from productos where pro_id='" . $_REQUEST["id"] . "'";
    $res = $link->query($sql);
    break;

  case 'actualizarStock':
    $datos = json_decode($_REQUEST["datos"], true);
    $sql = "update productos set pro_stock='" . $datos["stock"] . "' where pro_id='" . $datos["idpro"] . "'";
    $res = $link->query($sql);
    $tipo = 1; // comentarios para productos modificacion de stock
    InsertComentario($tipo, $datos["idpro"], $datos["usuario"], $datos["comentario"]);
    if ($datos["serie"] == 1) {
      $sql3 = "delete from codigosxproducto where cxp_producto='" . $datos["idpro"] . "'";
      $res3 = $link->query($sql3);

      if (isset($datos["codigos"])) {
        foreach ($datos["codigos"] as $index => $valor) {
          $sql2 = "insert into codigosxproducto(cxp_producto,cxp_codigo,cxp_estado)values('" . $datos["idpro"] . "','" . $valor["nombre"] . "',1)";
          $res2 = $link->query($sql2);
        }
      }
    }

    break;
  case 'actualizarPrecio':
    $sql = "update productos set pro_valor='" . $_REQUEST["precio"] . "' where pro_id='" . $_REQUEST["idpro"] . "'";
    $res = $link->query($sql);
    $tipo = 1; // comentarios para productos modificacion de stock
    InsertComentario($tipo, $_REQUEST["idpro"], $_REQUEST["usuario"], $_REQUEST["comentario"]);
    break;

  case 'getStockProducto':

    $sql = "SELECT DISTINCT t1.ser_id, t1.ser_codigo, t1.gui_id, t1.pro_id, t1.usu_id_cargo, 
                t2.pro_stock, t2.pro_serie, t1.prov_id, 
                (SELECT COUNT(DISTINCT ser_id) 
                 FROM serie_guia 
                 WHERE ser_estado = 1 
                   AND ser_condicion = 1 
                   AND pro_id = {$_REQUEST['producto']} 
                   AND ser_instalado = 0 
                   AND usu_id_cargo = {$_REQUEST['bodega']}) AS stock  
        FROM serie_guia t1 
        INNER JOIN productos t2 ON t1.pro_id = t2.pro_id
        WHERE t1.pro_id = {$_REQUEST['producto']} 
          AND t1.ser_estado = 1 
          AND t1.ser_condicion = 1 
          AND t1.ser_instalado = 0
          AND t1.usu_id_cargo = {$_REQUEST['bodega']}";
    $res    = $link->query($sql);
    $series = array();

    foreach ($res as $key) {
      if ($key['pro_serie'] == 1) {
          array_push($series, array(
            'keyserie' => $key['pro_serie'], 
            'valida' => 1, 
            'idserie' => $key['ser_id'], 
            'idguia' => $key['gui_id'], 
            'codigoserie' => $key['ser_codigo'], 
            'idproveedor' => $key['prov_id'], 
            'stock' => $key['stock'] // usa el stock calculado
          ));
      } else {
          array_push($series, array(
            'keyserie' => $key['pro_serie'], 
            'valida' => 0, 
            'idserie' => $key['ser_id'], 
            'idguia' => $key['gui_id'], 
            'codigoserie' => $key['ser_codigo'], 
            'idproveedor' => $key['prov_id'], 
            'stock' => $key['stock'] // usa el stock calculado
          ));
      }
    }
    mysqli_close($link);
    echo json_encode($series);
    break;

  case 'getTabTraspasos':

    $sql       = "SELECT t1.*, t2.usu_nombre as usr_modifica, t3.per_nombrecorto as usr_envia, t4.per_nombrecorto as usr_recibe
                  FROM traspasos_series t1
                  left outer join usuarios t2 on t2.usu_id = t1.usu_modifica
                  left outer join personal t3 on t3.per_id = t1.usu_id_envia
                  left outer join personal t4 on t4.per_id = t1.usu_id_recibe
                  where t1.tra_estado = 1 
                  order by t1.tra_id desc
                  limit 5
                  ";
    $res       = $link->query($sql);
    $traspasos = array();

    foreach ($res as $key) {
      $detalle   = array();
      $sobra     = array();
      $varpas    = json_decode($key['tra_detalle'], true);
      foreach ($varpas as $keyres2) {
        $sql2 = "SELECT t1.ser_codigo, t2.pro_nombre, t1.ser_id
                    FROM serie_guia t1
                    inner join productos t2 on t2.pro_id = t1.pro_id
                    where t1.ser_id = {$keyres2['id']}";
        $res2 = $link->query($sql2);

        foreach ($res2 as $key2) {
          array_push($detalle, array('fechatracking' => $key['tra_tracking_fecha'], 'courrier' => $key['tra_tracking_courrier'], 'recibetracking' => $key['tra_tracking_recibe'], 'codigotracking' => $key['tra_tracking_codigo'], 'idtracking' => $key['tra_tracking'], 'codigo' => $key2['ser_codigo'], 'proveedor' => $key2['pro_nombre'], 'fecha' => $key['tra_fecha'], 'usr_mod' => $key['usr_modifica'], 'usr_env' => $key['usr_envia'], 'usr_rec' => $key['usr_recibe'], 'codigoid' => $key2['ser_id']));
        }
      }

      $sql3 = "SELECT ser_id,ser_codigo FROM serie_guia where usu_id_cargo = (SELECT IF(usu_id_envia=0, '26', usu_id_envia) as envio FROM traspasos_series where tra_id = {$key['tra_id']})";
      $res3 = $link->query($sql3);

      foreach ($res3 as $key3) {
        array_push($sobra, array('ser_id' => $key3['ser_id'], 'ser_codigo' => $key3['ser_codigo'], 'tra_id' => $key['tra_id']));
      }

      array_push($traspasos, array('fechatracking' => $key['tra_tracking_fecha'], 'courrier' => $key['tra_tracking_courrier'], 'recibetracking' => $key['tra_tracking_recibe'], 'idtracking' => $key['tra_tracking'], 'trackingcodigo' => $key['tra_tracking_codigo'], 'idtraspaso' => $key['tra_id'], 'observaciones' => $key['tra_observacion'], 'fecha' => $key['tra_fecha'], 'usr_mod' => $key['usr_modifica'], 'usr_env' => $key['usr_envia'], 'usr_rec' => $key['usr_recibe'], 'detalle' => $detalle, 'sobra' => $sobra, 'sql' => $sql3));
    }

    mysqli_close($link);
    echo json_encode($traspasos);

    break;

    case 'nuevoTraspasonew':

      $datos = json_decode($_REQUEST["traspaso"], true);
    
      if (!$datos) {
          exit(json_encode(["mensaje" => "Error en el formato JSON", "logo" => "error"]));
      }
    
      $idtecnico    = $datos["bodega"];
      $idtecnico2  = $datos["bodega2"];
      $fecharequest = $datos["fecha"];
      $productos    = isset($datos['productos']) ? $datos['productos'] : [];
      $observaciones = $datos['observaciones'];
      $usumod       = $datos['usuario'];
    
      if (empty($productos)) {
          exit(json_encode(["mensaje" => "No hay productos para ingresar", "logo" => "error"]));
      }
    
      $prods = array();
    
      foreach ($productos as $key) {
          $idProducto = $key['idproducto'];
          $codigoserie = isset($key['codigoserie']) ? $key['codigoserie'] : null;
          $cantidad = isset($key['cantidad']) ? intval($key['cantidad']) : 1;
    
          if ($codigoserie) {
              $query_ser_id = "SELECT ser_id FROM serie_guia WHERE TRIM(ser_codigo) = '{$codigoserie}' AND pro_id = '{$idProducto}'";
              $res_ser_id = $link->query($query_ser_id);
              $row_ser_id = $res_ser_id ? $res_ser_id->fetch_assoc() : null;
              $ser_id = $row_ser_id ? $row_ser_id['ser_id'] : null;
    
              if (!$ser_id) {
                  exit(json_encode(["mensaje" => "Error: Código de serie no encontrado", "logo" => "error"]));
              }
    
              $prods[] = array('id' => $ser_id, 'idpro' => $idProducto);
    
              $update_query = "UPDATE serie_guia 
                               SET usu_id_cargo = {$idtecnico}, ser_instalado = 0 
                               WHERE ser_id = {$ser_id} AND pro_id = {$idProducto} AND ser_estado = 1";
              $link->query($update_query);
  
              $stock_update_query = "UPDATE productos SET pro_stock = pro_stock - 1 WHERE pro_id = '{$idProducto}'";
              $link->query($stock_update_query);
    
          } else {
              $query_stock = "SELECT pro_stock FROM productos WHERE pro_id = '{$idProducto}'";
              $res_stock = $link->query($query_stock);
              $stock = $res_stock ? $res_stock->fetch_assoc()['pro_stock'] : 0;
    
              if ($stock < $cantidad) {
                  exit(json_encode(["mensaje" => "Stock insuficiente para ID producto: $idProducto", "logo" => "error"]));
              }
    
              $query_sin_serie = "SELECT ser_id FROM serie_guia WHERE pro_id = '{$idProducto}' AND ser_estado = 1 AND usu_id_cargo != {$idtecnico} LIMIT {$cantidad}";
              $res_sin_serie = $link->query($query_sin_serie);
    
              if ($res_sin_serie->num_rows < $cantidad) {
                  exit(json_encode(["mensaje" => "Stock insuficiente para ID producto: $idProducto", "logo" => "error"]));
              }
    
              while ($row = $res_sin_serie->fetch_assoc()) {
                  $ser_id = $row['ser_id'];
                  $prods[] = array('id' => $ser_id, 'idpro' => $idProducto);
    
                  $update_query = "UPDATE serie_guia 
                                   SET usu_id_cargo = {$idtecnico}, ser_instalado = 0  
                                   WHERE ser_id = {$ser_id} AND pro_id = {$idProducto} AND ser_estado = 1";
                  $link->query($update_query);
              }
    
              $stock_update_query = "UPDATE productos SET pro_stock = pro_stock - {$cantidad} WHERE pro_id = '{$idProducto}'";
              $link->query($stock_update_query);
          }
      }
    
      if (empty($prods)) {
          exit(json_encode(["mensaje" => "No hay productos válidos para traspasar", "logo" => "error"]));
      }
    
      $sql = "INSERT INTO traspasos_series (tra_fecha, usu_id_envia, usu_id_recibe, tra_observacion, usu_modifica, tra_detalle) 
            VALUES ('{$fecharequest}', {$idtecnico2}, {$idtecnico}, '{$observaciones}', {$usumod}, '" . json_encode($prods) . "')";
    
      if (!$link->query($sql)) {
          exit(json_encode(["mensaje" => "Error en la base de datos", "logo" => "error"]));
      }
  
      echo json_encode(["mensaje" => "Traspaso realizado", "logo" => "success"]);
    break;
  
  
  

  case 'nuevoTraspaso':

    $datos        = json_decode($_REQUEST["traspaso"], true);
    $tipo         = 1; // traspaso de bodega principal a bodega tecnico
    $idtecnico    = $datos["bodega"];
    $fecharequest =  $datos["fecha"];
    try {
      $sql = "insert into traspasos(tras_fecha,tras_bodega,tras_observaciones,tras_usuario,tras_tipo)values('{$fecha}'," . $datos["bodega"] . ",'" . $datos["observaciones"] . "'," . $datos["usuario"] . "," . $tipo . ")";
      $res = $link->query($sql);
      $idtras = $link->insert_id;
      $productos = $datos["productos"];
      if (count($productos) >  0) {
        foreach ($productos as $index => $valor) {
          $idbod = $datos["bodega"];
          $idpro = $valor["idpro"];
          $cantidad = $valor["cantidad"];
          if ((int)$valor["ttraspaso"] == 1) {
            foreach ($valor["series"] as $index1 => $valor1) {
              $_serie1 = $valor1['serie'];
            }
            $_serie2 = '';
            $idprodd = $idpro;
          } else {
            $sql2 = "SELECT * FROM equipos_asociados WHERE easi_id={$idpro}";
            $res2 = $link->query($sql2);
            $val2 = mysqli_fetch_array($res2);
            $idprodd = $val2['easi_id'];
            $_serie1 = $val2['easi_seriegps'];
            $_serie2 = $val2['easi_seriesim'];
          }

          if ((int)$valor["ttraspaso"] == 1) {

            $sql3 = "insert into detalletraspaso(dtras_traspaso,dtras_bodega,dtras_producto,dtras_cantidad,dtras_tipo,dtras_seriegps,dtras_seriesim,dtras_idasi)values(" . $idtras . "," . $idbod . "," . $idpro . "," . $cantidad . ",1,'','',0)";
            $res3 = $link->query($sql3);
            $iddetalle = $link->insert_id;
            $stockactual = obtenervalor("productos", "pro_stock", "where pro_id=" . $idpro . "");
            $nuevostock = $stockactual - $cantidad;
            $sql4 = "update productos set pro_stock=" . $nuevostock . " where pro_id=" . $idpro . "";
            $res4 = $link->query($sql4);
          } else {
            $sql = "SELECT * FROM equipos_asociados WHERE easi_id={$idpro}";
            $res = $link->query($sql);
            $val = mysqli_fetch_array($res);
            $idprod = $val['easi_idgps'];
            $response['sql_1_idgps_' . $index] = $idprod;
            $sql3 = "insert into detalletraspaso(dtras_traspaso,dtras_bodega,dtras_producto,dtras_cantidad,dtras_tipo,dtras_seriegps,dtras_seriesim,dtras_idasi)values(" . $idtras . "," . $idbod . "," . $idprod . "," . $cantidad . ",2,'" . $_serie1 . "','" . $_serie2 . "'," . $idprodd . ")";
            $res3 = $link->query($sql3);
            $iddetalle = $link->insert_id;
            $response['sql_1_' . $index] = $sql3;
            $sql4 = "update equipos_asociados set easi_estado=3 where easi_id=" . $idpro . "";
            $res4 = $link->query($sql4);

            $response['sql_1__update_' . $index] = $sql4;
          }

          if ($valor["tieneserie"] == "SI") {
            foreach ($valor["series"] as $index1 => $valor1) {
              $sql2 = "update codigosxproducto set cxp_estado= 2 where cxp_id=" . $index1 . "";
              $res2 = $link->query($sql2);
              $sql3 = "insert into productosxtecnico(pxt_idtecnico,pxt_cantidad,pxt_idpro,pxt_nserie,pxt_estado,pxt_observaciones,pxt_ideasi,pxt_tipo,pxt_subestado)values(" . $datos["bodega"] . ",1," . $valor["idpro"] . ",'" . $valor1["serie"] . "',1,'Traspaso desde bodega principal'," . $idpro . "," . $valor["ttraspaso"] . ",0)";
              $res3 = $link->query($sql3);
              $sql4 = "insert into codigosxtraspaso(cxt_detalletraspaso,cxt_serie)values(" . $iddetalle . ",'" . $valor1["serie"] . "')";
              $res4 = $link->query($sql4);
            }
          } else {
            $tipo = '';
            if ((int)$valor["ttraspaso"] == 1) {
              $tipo = 'pxt_tipo=1';
              $idprod = $valor["idpro"];
              $ideasi = 0;
            } else {
              $tipo = 'pxt_tipo=2';
              $sql = "SELECT * FROM equipos_asociados WHERE easi_id={$idpro}";
              $res = $link->query($sql);
              $val = mysqli_fetch_array($res);
              $idprod = $val['easi_idgps'];
              $ideasi = $val['easi_id'];
            }
            $sql31 = "select * from productosxtecnico where pxt_idpro=" . $idprod . " AND pxt_idtecnico=" . $idtecnico . " AND pxt_estado=1 AND " . $tipo;
            $res31 = $link->query($sql31);
            $cuenta = mysqli_num_rows($res31);
            $response['sql_select_1_' . ($valor["ttraspaso"])] = $sql31;
            if ($cuenta > 0) {
              // producto existe
              $stoctec = obtenervalor("productosxtecnico", "pxt_cantidad", "where pxt_idpro=" . $idprod . " AND pxt_idtecnico=" . $idtecnico . " AND pxt_estado=1 AND pxt_tipo=" . $valor["ttraspaso"]);
              $nuevostec = intval($stoctec) + intval($valor["cantidad"]);
              $sql1 = "update productosxtecnico set pxt_cantidad=" . $nuevostec . " where pxt_idpro=" . $idprod . " AND pxt_idtecnico=" . $idtecnico . " && pxt_estado=1 AND pxt_tipo=" . $valor["ttraspaso"];
              //echo "cuenta => ".$cuenta." nuevo stock => ".$nuevostec." consulta =>".$sql1 ;
              // return;
              $res1 = $link->query($sql1);
              $response['sql_update_1_' . $index] = $sql1;
            } else {
              $sql3 = "insert into productosxtecnico(pxt_idtecnico,pxt_cantidad,pxt_idpro,pxt_estado,pxt_observaciones,pxt_ideasi,pxt_tipo,pxt_subestado)values(" . $datos["bodega"] . "," . $valor["cantidad"] . "," . $idprod . ",1,'Traspaso desde bodega principal'," . $ideasi . "," . $valor["ttraspaso"] . ",0)";
              $res = $link->query($sql3);
              $response['sql_insert_1_' . $index] = $sql3;
            }
          }
        }
      }
      echo json_encode($response);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }
    //echo json_encode($devoluciones);
    break;

  case 'newnuevadevolucion':
    $datos = json_decode($_REQUEST["devolucion"], true);
    $ind   = 0;
    foreach ($datos as $key) {
      if ($ind == 0) {
        $sql = "insert into devoluciones(dev_fecha,usu_id_envia,dev_observacion,usu_id_modifica,dev_tipo,dev_estado,dev_tracking,dev_tracking_codigo,dev_tracking_courrier,dev_tracking_recibe,dev_tracking_fecha)values('" . $key["fecha"] . "','" . $key["usu_sel"] . "','" . $key["observacion"] . "'," . $_SESSION['cloux_new'] . ",1,1," . $key["traid"] . ",'" . $key["numcou"] . "','" . $key["codcou"] . "','" . $key["reccou"] . "','" . $key["fecha"] . "')";
        $res    = $link->query($sql);
        $id_dev = $link->insert_id;

        $sql1 = "insert into detalledevolucion(dev_id,ser_id,dev_observacion,pro_id,ddev_tracking,ddev_tracking_codigo,ddev_tracking_courrier,ddev_tracking_recibe,ddev_tracking_fecha,ddev_estado)values(" . $id_dev . "," . $key["idseerie"] . ",'" . $key["detalle"] . "'," . $key["proid"] . "," . $key["traid"] . ",'" . $key["numcou"] . "','" . $key["codcou"] . "','" . $key["reccou"] . "','" . $key["fecha"] . "'," . $key["idselect"] . ")";
        $res1 = $link->query($sql1);

        $sql3 = "UPDATE productos SET pro_stock = (select pro_stock from productos where pro_id = {$key["proid"]}) + 1 WHERE pro_id = {$key["proid"]}";
        $res3 = $link->query($sql3);

        $sql4 = "UPDATE serie_guia SET usu_id_cargo = 26, ser_condicion = {$key['idselect']}, ser_tracking_codigo = '{$key["numcou"]}', ser_tracking = {$key["traid"]}, ser_tracking_courrier = '{$key["codcou"]}', ser_tracking_fecha = '{$key["fecha"]}', ser_tracking_recibe = '{$key['reccou']}' WHERE pro_id = {$key["proid"]} and ser_id = {$key["idseerie"]}";
        $res4 = $link->query($sql4);
      } else {

        $sql2 = "insert into detalledevolucion(dev_id,ser_id,dev_observacion,pro_id,ddev_tracking,ddev_tracking_codigo,ddev_tracking_courrier,ddev_tracking_recibe,ddev_tracking_fecha,ddev_estado)values(" . $id_dev . "," . $key["idseerie"] . ",'" . $key["detalle"] . "'," . $key["proid"] . "," . $key["traid"] . ",'" . $key["numcou"] . "','" . $key["codcou"] . "','" . $key["reccou"] . "','" . $key["fecha"] . "'," . $key["idselect"] . ")";
        $res2 = $link->query($sql2);

        $sql3 = "UPDATE productos SET pro_stock = (select pro_stock from productos where pro_id = {$key["proid"]}) + 1 WHERE pro_id = {$key["proid"]}";
        $res3 = $link->query($sql3);

        $sql4 = "UPDATE serie_guia SET usu_id_cargo = 26, ser_condicion = {$key['idselect']}, ser_tracking_codigo = '{$key["numcou"]}', ser_tracking = {$key["traid"]}, ser_tracking_courrier = '{$key["codcou"]}', ser_tracking_fecha = '{$key["fecha"]}', ser_tracking_recibe = '{$key['reccou']}' WHERE pro_id = {$key["proid"]} and ser_id = {$key["idseerie"]}";
        $res4 = $link->query($sql4);
      }
      $ind++;
    }

    if ($id_dev > 0 || $id_dev != '') {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Devolucion ingresada correctamente', 'pru' => $sql1);
    } else {
      $devuelve = array('logo' => 'danger', 'mensaje' => 'Ha ocurrido un error', 'pru' => $sql);
    }
    mysqli_close($link);
    echo json_encode($devuelve);

    break;

  case 'nuevadevolucion':
    $datos     = json_decode($_REQUEST["devolucion"], true);
    $tipo      = 1; // devolucion de productos desde bodega tecnico a bodega principal
    $idtecnico = $datos["bodega"];
    $sql       = "insert into devoluciones(dev_fecha,dev_bodega,dev_observaciones,dev_usuario,dev_tipo)values('" . convfecha($datos["fecha"]) . "','" . $datos["bodega"] . "','" . $datos["observaciones"] . "','" . $_SESSION['cloux_new'] . "','" . $tipo . "')";
    $res       = $link->query($sql);
    $iddev     = $link->insert_id;
    $productos = $datos["productos"];

    if (count($productos) >  0) {
      foreach ($productos as $index => $valor) {
        $idbod      = $datos["bodega"];
        $idpro      = $valor["idpro"];
        $cantidad   = $valor["cantidad"];
        $obs        = $valor["obs"];
        $idestado   = $valor["idestado"];
        $tieneserie = $valor["tieneserie"];
        $sql3       = "insert into detalledevolucion(ddev_devolucion,ddev_bodega,ddev_producto,ddev_cantidad,ddev_estado,ddev_observaciones)values('" . $iddev . "','" . $idbod . "','" . $idpro . "','" . $cantidad . "','" . $idestado . "','" . $obs . "')";
        $res3      = $link->query($sql3);
        $iddetalle = $link->insert_id;
        $sa        = obtenervalor("productos", "pro_stock", "where pro_id='" . $idpro . "'"); // stock actual del producto con serie
        // estado del producto
        $cxp_estado = 0;
        if (intval($idestado) == 1) {
          // si el producto esta bueno
          $cxp_estado = 1; // producto en buen estado y en bodega principal
        } else {
          // si el producto esta malo
          $cxp_estado = 3; // producto en mal estado pero en bodega principal
        }
        if ($tieneserie === "SI") {
          // producto con serie => cantidad siempre es 1
          $sql5   = "update codigosxproducto set cxp_estado='" . $cxp_estado . "', cxp_info='" . $obs . "' where cxp_codigo='" . $valor["serie"] . "'";
          $res5   = $link->query($sql5);
          $sql7   = "insert into codigosxdevolucion(cxd_detalledevolucion,cxd_serie)values('" . $iddetalle . "','" . $valor["serie"] . "')";
          $res7   = $link->query($sql7);

          $sql6   = "update productosxtecnico set pxt_estado = 0, pxt_idtecnico = 26 where pxt_id='" . $index . "'";
          $res6   = $link->query($sql6);
          $nuevoS = $sa + 1;
        } else {
          // producto sin serie, cantidad variable
          if ($valor["estadooriginal"] === "BUENO") {
            $epxt = 1;
          } else {
            $epxt = 2;
          }

          $sbt = obtenervalor("productosxtecnico", "pxt_cantidad", "where pxt_idpro='" . $valor["idpro"] . "' && pxt_estado='" . $epxt . "'");
          $dif = $sbt - $cantidad;

          if ($dif == 0) {
            $sql6 = "update productosxtecnico set pxt_estado = 0 where pxt_id='" . $index . "'";
            $res6 = $link->query($sql6);
          } else {
            $sql6 = "update productosxtecnico set pxt_cantidad='" . $dif . "' where pxt_id='" . $index . "'";
            $res6 = $link->query($sql6);
          }

          if (intval($idestado) == 2) {
            $sql7 = "select * from codigosxproducto where cxp_estado=3 && cxp_producto='" . $valor["idpro"] . "'";
            $res7 = $link->query($sql7);
            if (mysqli_num_rows($res7) > 0) {

              $fila7      = mysqli_fetch_array($res7);
              $nuevostock = intval($fila7["cxp_cantidad"]) + $cantidad;
              $sql5       = "update codigosxproducto set cxp_cantidad='" . $nuevostock . "',cxp_estado='" . $cxp_estado . "', cxp_info='" . $obs . "' where cxp_codigo='" . $valor["serie"] . "'";
              $nuevoS = $sa;
            } else {
              $sql5 = "insert into codigosxproducto(cxp_producto,cxp_cantidad,cxp_estado,cxp_info)values('" . $valor["idpro"] . "','" . $cantidad . "','" . $cxp_estado . "','" . $obs . "')";
            }
            $res5 = $link->query($sql5);
          } else {
            $nuevoS = $sa + $cantidad;
          }
        }

        $sql4 = "update productos set pro_stock='" . $nuevoS . "' where pro_id='" . $valor["idpro"] . "'";
        $res4 = $link->query($sql4);
      }
    } else {
    }
    break;

  case 'actuser':
    $recibe = json_decode($_REQUEST['envio'], true);
    $sql    = "select * from traspasos_series where tra_id = {$recibe['idtra']}";
    $res    = $link->query($sql);
    $arract = array();
    foreach ($res as $key) {
      $varpas = json_decode($key['tra_detalle'], true);
      foreach ($varpas as $keyres2) {
        if ($keyres2['id'] == $recibe['idserantoguo']) {
          $sql1  = "select * from serie_guia where ser_id = {$recibe['valsel']}";
          $res1  = $link->query($sql1);
          $fila1 = mysqli_fetch_array($res1);
          array_push($arract, array('id' => $recibe['valsel'], 'idpro' => $fila1['pro_id']));
          $usuenvia  = $key['usu_id_envia'];
          $usurecibe = $key['usu_id_recibe'];
          if ($usuenvia == 0) {
            $usuenvia = 26;
          }

          if ($usurecibe == 0) {
            $usurecibe = 26;
          }

          $fecser = '';
          if ($key['tra_tracking_fecha'] == null || $key['tra_tracking_fecha'] == 0 || $key['tra_tracking_fecha'] == '') {
            $fecser = '';
          } else {
            $fecser = ", ser_tracking_fecha = '{$key['tra_tracking_fecha']}'";
          }

          $sql3 = "update serie_guia set usu_id_cargo = {$usurecibe}, ser_tracking = {$key['tra_tracking']},ser_tracking_codigo = '{$key['tra_tracking_codigo']}', ser_tracking_recibe = '{$key['tra_tracking_recibe']}', ser_tracking_courrier = '{$key['tra_tracking_courrier']}' {$fecser} where ser_id = {$recibe['valsel']}";
          $res3 = $link->query($sql3);

          $sql4 = "update serie_guia set usu_id_cargo = {$usuenvia}, ser_tracking = 0,ser_tracking_codigo = '', ser_tracking_recibe = '', ser_tracking_courrier = '', ser_tracking_fecha = 0 where ser_id = {$recibe['idserantoguo']}";
          $res4 = $link->query($sql4);
        } else {
          array_push($arract, array('id' => $keyres2['id'], 'idpro' => $keyres2['idpro']));
        }
      }

      $sql2 = "update traspasos_series set tra_detalle = '" . str_replace("\\", '', json_encode($arract)) . "' where tra_id = " . $recibe['idtra'] . "";
      $res2 = $link->query($sql2);
      $devuelve = array();
      if ($res2) {
        $devuelve = array('logo' => 'success', 'mensaje' => 'Serie actualizada correctamente', 'sql' => '');
      } else {
        $devuelve = array('logo' => 'danger', 'mensaje' => 'Ha ocurrido un error', 'sql' => '');
      }

      echo json_encode($devuelve);
    }

    break;

  case 'getTabDevoluciones':

    $sql          = "select t1.*, t2.per_nombrecorto, t3.usu_nombre
                    from devoluciones t1
                    left outer join personal t2 on t2.per_id = t1.usu_id_envia
                    left outer join usuarios t3 on t3.usu_id = t1.usu_id_modifica
                    where t1.dev_estado = 1 order by t1.dev_fecha desc";
    $res          = $link->query($sql);
    $devoluciones = array();

    foreach ($res as $key) {

      $detalle = array();
      $sobra   = array();
      $sql1    = "select t1.*, t2.pro_nombre, t3.ser_codigo
                  from detalledevolucion t1 
                  left outer join productos t2 on t1.pro_id = t2.pro_id 
                  inner join serie_guia t3 on t3.ser_id = t1.ser_id
                  where t1.dev_id = {$key['dev_id']}";
      $res1 = $link->query($sql1);

      foreach ($res1 as $key1) {
        array_push($detalle, array("observacion" => $key1["dev_observacion"], "ddev_tracking_fecha" => $key1["ddev_tracking_fecha"], "ddev_tracking_recibe" => $key1["ddev_tracking_recibe"], "ddev_tracking_codigo" => $key1["ddev_tracking_codigo"], "ddev_tracking_courrier" => $key1["ddev_tracking_courrier"], "ddev_estado" => $key1["ddev_estado"], "iddev" => $key1["ddev_id"], "producto" => $key1["pro_nombre"], "serie" => $key1["ser_codigo"], "pro_id" => $key1["pro_id"], "serid" => $key1["ser_id"], "dev_fecha" => $key["dev_fecha"], "usuenvianombre" => $key["per_nombrecorto"], "traid" => $key1["ddev_tracking"], "codigotracking" => $key1["ddev_tracking_codigo"]));
      }

      $sql2 = "SELECT * FROM serie_guia where usu_id_cargo = (SELECT usu_id_envia FROM devoluciones where dev_id = {$key['dev_id']}) and ser_estado = 1";
      $res2 = $link->query($sql2);

      foreach ($res2 as $key2) {
        array_push($sobra, array("ser_id" => $key2["ser_id"], "pro_id" => $key2["pro_id"], "ser_codigo" => $key2["ser_codigo"]));
      }

      array_push($devoluciones, array("devid" => $key["dev_id"], "dev_tracking" => $key["dev_tracking"], "dev_tracking_fecha" => $key["dev_tracking_fecha"], "dev_tracking_recibe" => $key["dev_tracking_recibe"], "dev_tracking_codigo" => $key["dev_tracking_codigo"], "dev_tracking_courrier" => $key["dev_tracking_courrier"], "dev_id" => $key["dev_id"], "dev_fecha" => $key["dev_fecha"], "idusuenvia" => $key["usu_id_envia"], "usuenvianombre" => $key["per_nombrecorto"], "observaciones" => $key["dev_observacion"], "usu_id_mod" => $key["usu_id_modifica"], "usumodifica" => $key["usu_nombre"], "detalle" => $detalle, "sobra" => $sobra));
    }
    mysqli_close($link);
    echo json_encode($devoluciones);

    break;

    /***************************
OPERACIONES PROVEEDORES
     ****************************/
  case 'nuevoproveedor':
    if ($_REQUEST["comuna"] == '') {
      $_REQUEST["comuna"] = 0;
    }

    if ($_REQUEST["region"] == '') {
      $_REQUEST["region"] = 0;
    }

    if ($_REQUEST["giro"] == '') {
      $_REQUEST["giro"] = 0;
    }
    $sql = "insert into proveedores(rut,razonsocial,giro,region,comuna,direccion,telefono,correo)values('" . $_REQUEST["rut"] . "','" . $_REQUEST["razonsocial"] . "','" . $_REQUEST["giro"] . "','" . $_REQUEST["region"] . "','" . $_REQUEST["comuna"] . "','" . $_REQUEST["direccion"] . "','" . $_REQUEST["telefono"] . "','" . $_REQUEST["correo"] . "')";
    /*echo $sql;
die();*/
    $res = $link->query($sql);
    $idproveedor = $link->insert_id;
    if ($_REQUEST["desde"] == "nuevaordendecompra") {
      //$proveedores = getProveedores();
      $proveedores = array();
      echo json_encode($proveedores);
    } else {
      $contactos = $_REQUEST["contactos"];
      foreach ($contactos as $valor) {
        $sep = explode("|", $valor);
        $sql1 = "insert into contactoproveedores(proveedor,nombre,telefono,correo,cargo)values('" . $idproveedor . "','" . $sep[0] . "','" . $sep[1] . "','" . $sep[2] . "','" . $sep[3] . "')";
        $res1 = $link->query($sql1);
      }
      $sale_a = $_REQUEST["retornar"];
    }


    break;
  case 'editarproveedor':
    $sql = "update proveedores set rut='" . $_REQUEST["rut"] . "',razonsocial='" . $_REQUEST["razonsocial"] . "',giro='" . $_REQUEST["giro"] . "',region='" . $_REQUEST["region"] . "',comuna='" . $_REQUEST["comuna"] . "',direccion='" . $_REQUEST["direccion"] . "',telefono='" . $_REQUEST["telefono"] . "',correo='" . $_REQUEST["correo"] . "' where id='" . $_REQUEST["idproveedor"] . "'";
    $res = $link->query($sql);
    $contactos = $_REQUEST["contactos"];
    foreach ($contactos as $valor) {
      $sep = explode("|", $valor);
      $sql1 = "insert into contactoproveedores(proveedor,nombre,telefono,correo,cargo)values('" . $_REQUEST["idproveedor"] . "','" . $sep[0] . "','" . $sep[1] . "','" . $sep[2] . "','" . $sep[3] . "')";
      $res1 = $link->query($sql1);
    }
    $sale_a = $_REQUEST["retornar"];
    break;

  case 'BorrarProveedor':
    $sql  = "delete from contactoproveedores where proveedor='" . $_REQUEST["proveedor"] . "'";
    $res  = $link->query($sql);
    $sql1 = "delete from proveedores where id='" . $_REQUEST["proveedor"] . "'";
    $res1 = $link->query($sql1);
    echo "proveedor eliminado";
    break;

    case 'getdettraspasos':

      $recibe   = json_decode($_REQUEST['envio'], true);
      $sql      = "SELECT * FROM traspasos_series where tra_id = " . $recibe["idtraspaso"];
      $res      = $link->query($sql);
      $fila     = mysqli_fetch_array($res);
      $vardeta  = json_decode($fila['tra_detalle'], true);
      $tablauno = array();
      $tablados = array();
  
      foreach ($vardeta as $key) {
          $sql1  = "SELECT t1.*, t2.pro_nombre, IF(t1.ser_condicion=1,'BUENO','MALO') as condicion
                    FROM serie_guia t1
                    LEFT OUTER JOIN productos t2 on t2.pro_id = t1.pro_id
                    WHERE ser_id = {$key['id']}";
          $res1  = $link->query($sql1);
          $fila1 = mysqli_fetch_array($res1);
          // Determinar si tiene serie (se usa ser_codigo)
          $tiene_serie = (isset($fila1['ser_codigo']) && trim($fila1['ser_codigo']) != '') ? "SI" : "NO";
          array_push($tablados, array(
              'ser_codigo'    => $fila1['ser_codigo'], 
              'pro_nombre'    => $fila1['pro_nombre'], 
              'pro_id'        => $fila1['pro_id'], 
              'ser_id'        => $fila1['ser_id'], 
              'ser_condicion' => $fila1['condicion'],
              'tiene_serie'   => $tiene_serie
          ));
      }
  
      if ($fila['usu_id_envia'] == 0) {
          $envia = 26;
      } else {
          $envia = $fila['usu_id_envia'];
      }
  
      $sql2  = "SELECT t1.*, t2.pro_nombre, IF(t1.ser_condicion=1,'BUENO','MALO') as condicion
          FROM serie_guia t1
          LEFT OUTER JOIN productos t2 on t2.pro_id = t1.pro_id
          WHERE t1.usu_id_cargo = {$envia}
            AND t1.ser_estado = 1
            AND t1.ser_condicion = 1
            AND t1.ser_instalado = 0";
      $res2  = $link->query($sql2);
  
      foreach ($res2 as $key2) {
          $tiene_serie = (isset($key2['ser_codigo']) && trim($key2['ser_codigo']) != '') ? "SI" : "NO";
          array_push($tablauno, array(
              'ser_codigo'    => $key2['ser_codigo'], 
              'pro_nombre'    => $key2['pro_nombre'], 
              'pro_id'        => $key2['pro_id'], 
              'ser_id'        => $key2['ser_id'], 
              'ser_condicion' => $key2['condicion'],
              'tiene_serie'   => $tiene_serie
          ));
      }
  
      $devuelve = array(
          'usu_id_envia'   => $envia, 
          'usu_id_recibe'  => $fila['usu_id_recibe'], 
          'tra_observacion'=> $fila['tra_observacion'], 
          'tra_fecha'      => $fila['tra_fecha'], 
          'tablauno'       => $tablauno, 
          'tablados'       => $tablados
      );
  
      echo json_encode($devuelve);
  
      break;
  
      
      case 'edittraspasoser':

    
        // Log de datos recibidos
        $recibe = json_decode($_REQUEST['envio'], true);
        file_put_contents($log_file,
            "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] Datos recibidos:\n" . print_r($recibe, true) . "\n\n",
            FILE_APPEND
        );
    
        // Obtener el registro de traspasos_series
        $sql = "SELECT * FROM traspasos_series where tra_id = {$recibe['idtraspaso']}";
        file_put_contents($log_file,
            "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] SQL para obtener traspasos_series:\n$sql\n\n",
            FILE_APPEND
        );
        $res = $link->query($sql);
        if (!$res) {
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] Error en query: " . $link->error . "\n\n",
                FILE_APPEND
            );
        }
        $fila = mysqli_fetch_array($res);
        file_put_contents($log_file,
            "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] Registro traspasos_series obtenido:\n" . print_r($fila, true) . "\n\n",
            FILE_APPEND
        );
    
        if ($recibe['opciontecnico'] == 1) {
            // Log: mostrar el valor de usu_id_recibe
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] Opción técnico 1: usu_id_recibe = " . $fila['usu_id_recibe'] . "\n\n",
                FILE_APPEND
            );
            
            // Procesar fecha para tracking
            if ($fila['tra_fecha'] == '' || $fila['tra_fecha'] == null || $fila['tra_fecha'] == 0) {
                $fechan = "";
            } else {
                $fechan = ", ser_tracking_fecha = '{$fila['tra_fecha']}'";
            }
            
            // Construir SQL de actualización en serie_guia
            $sql1 = "update serie_guia set usu_id_cargo = {$fila['usu_id_recibe']}, ser_tracking = {$fila['tra_tracking']}, ser_tracking_codigo = '{$fila['tra_tracking_codigo']}', ser_tracking_courrier = '{$fila['tra_tracking_courrier']}', ser_tracking_recibe = '{$fila['tra_tracking_recibe']}' {$fechan} where ser_id = {$recibe['idserie']}";
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] SQL1 (opción 1):\n$sql1\n\n",
                FILE_APPEND
            );
            $res1 = $link->query($sql1);
            if (!$res1) {
                file_put_contents($log_file,
                    "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] Error en SQL1: " . $link->error . "\n\n",
                    FILE_APPEND
                );
            }
    
            // Procesar y actualizar el detalle
            $vardeta = json_decode($fila['tra_detalle'], true);
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] tra_detalle original:\n" . print_r($vardeta, true) . "\n\n",
                FILE_APPEND
            );
            $deta = array();
            foreach ($vardeta as $key) {
                array_push($deta, array('id' => $key['id'], 'idpro' => $key['idpro']));
            }
            array_push($deta, array('id' => $recibe['idserie'], 'idpro' => $recibe['idproducto']));
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] tra_detalle modificado:\n" . print_r($deta, true) . "\n\n",
                FILE_APPEND
            );
            $sql2 = "update traspasos_series set tra_detalle = '" . str_replace("\\", '', json_encode($deta)) . "' where tra_id = " . $recibe['idtraspaso'];
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] SQL2 (opción 1):\n$sql2\n\n",
                FILE_APPEND
            );
            $res2 = $link->query($sql2);
            if (!$res2) {
                file_put_contents($log_file,
                    "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] Error en SQL2: " . $link->error . "\n\n",
                    FILE_APPEND
                );
            }
    
            if ($res2) {
                $devuelve = array('logo' => 'success', 'mensaje' => 'Cambio de serie realizado', 'sql' => $sql1);
            } else {
                $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error', 'sql' => $sql1);
            }
            echo json_encode($devuelve);
        } else if ($recibe['opciontecnico'] == 2) {
            // Para opción 2, loggear el valor de usu_id_envia
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] Opción técnico 2: usu_id_envia = " . $fila['usu_id_envia'] . "\n\n",
                FILE_APPEND
            );
            $sql1 = "update serie_guia set usu_id_cargo = {$fila['usu_id_envia']}, ser_tracking = 0, ser_tracking_codigo = '', ser_tracking_courrier = '', ser_tracking_recibe = '', ser_tracking_fecha = 0 where ser_id = {$recibe['idserie']}";
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] SQL1 (opción 2):\n$sql1\n\n",
                FILE_APPEND
            );
            $res1 = $link->query($sql1);
            if (!$res1) {
                file_put_contents($log_file,
                    "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] Error en SQL1 (opción 2): " . $link->error . "\n\n",
                    FILE_APPEND
                );
            }
            $vardeta = json_decode($fila['tra_detalle'], true);
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] tra_detalle original (opción 2):\n" . print_r($vardeta, true) . "\n\n",
                FILE_APPEND
            );
            $deta = array();
            foreach ($vardeta as $key) {
                if ($key['id'] != $recibe['idserie']) {
                    array_push($deta, array('id' => $key['id'], 'idpro' => $key['idpro']));
                }
            }
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] tra_detalle modificado (opción 2):\n" . print_r($deta, true) . "\n\n",
                FILE_APPEND
            );
            $sql2 = "update traspasos_series set tra_detalle = '" . str_replace("\\", '', json_encode($deta)) . "' where tra_id = " . $recibe['idtraspaso'];
            file_put_contents($log_file,
                "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] SQL2 (opción 2):\n$sql2\n\n",
                FILE_APPEND
            );
            $res2 = $link->query($sql2);
            if (!$res2) {
                file_put_contents($log_file,
                    "[" . date('Y-m-d H:i:s') . "] [edittraspasoser] Error en SQL2 (opción 2): " . $link->error . "\n\n",
                    FILE_APPEND
                );
            }
    
            if ($res2) {
                $devuelve = array('logo' => 'success', 'mensaje' => 'Cambio de serie realizado', 'sql' => $sql1);
            } else {
                $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error', 'sql' => $sql1);
            }
            echo json_encode($devuelve);
        }
      break;
    

  case 'deletedev':

    $recibe = json_decode($_REQUEST['envio'], true);
    $sql2   = "update devoluciones set dev_estado = 0 where dev_id = " . $recibe['iddevolucion'] . "";
    $res2   = $link->query($sql2);

    if ($res2) {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Devolución eliminada correctamente');
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error');
    }

    echo json_encode($devuelve);
    break;

  case 'updatetra':
    $recibe = json_decode($_REQUEST['envio'], true);
    $sql    = "update traspasos_series set tra_observacion = '{$recibe["observacion"]}',tra_fecha = '{$recibe["fecha"]}' where tra_id = {$recibe["tras"]}";
    $res    = $link->query($sql);

    if ($res) {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Actualizado correctamente');
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error');
    }

    echo json_encode($devuelve);

    break;

  case 'updatedev':
    $recibe = json_decode($_REQUEST['envio'], true);
    $sql    = "update devoluciones set dev_observacion = '{$recibe["observacion"]}',dev_fecha = '{$recibe["fecha"]}',dev_tracking = {$recibe["estadoenvo"]},dev_tracking_codigo = '{$recibe["numeroc"]}',dev_tracking_courrier = '{$recibe["courrierc"]}',dev_tracking_recibe = '{$recibe["recibec"]}',dev_tracking_fecha = '{$fecha}' where dev_id = {$recibe["devid"]}";
    $res    = $link->query($sql);

    $sql1   = "update detalledevolucion set ddev_tracking_fecha = '{$fecha}',ddev_tracking = {$recibe["estadoenvo"]},ddev_tracking_codigo = '{$recibe["numeroc"]}',ddev_tracking_courrier = '{$recibe["courrierc"]}',ddev_tracking_recibe = '{$recibe["recibec"]}' where dev_id = {$recibe["devid"]}";
    $res1    = $link->query($sql1);

    $sql2   = "select * from detalledevolucion where dev_id = {$recibe["devid"]} and ddev_visible = 1";
    $res2   = $link->query($sql2);

    foreach ($res2 as $key2) {
      $sql3 = "update serie_guia set ser_tracking = {$recibe["estadoenvo"]}, ser_tracking_codigo = '{$recibe["numeroc"]}', ser_tracking_courrier = '{$recibe["courrierc"]}', ser_tracking_recibe = '{$recibe["recibec"]}', ser_tracking_fecha = '{$fecha}' where ser_id = {$key2['ser_id']}";
      $res3 = $link->query($sql3);
    }

    $indavanza = 0;
    foreach ($recibe['serid'] as $ser) {

      $sql4 = "update detalledevolucion set dev_observacion = '{$recibe['txtva'][$indavanza]}', ddev_estado = {$recibe['conid'][$indavanza]} where ser_id = {$ser} and dev_id = {$recibe["devid"]}";
      $res4 = $link->query($sql4);

      $sql5 = "update serie_guia set ser_condicion = {$recibe['conid'][$indavanza]} where ser_id = {$ser}";
      $res5 = $link->query($sql5);

      $indavanza++;
    }

    if ($res5) {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Actualizado correctamente');
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error');
    }

    echo json_encode($devuelve);

    break;

  case 'cambiartecnico':
    $recibe  = json_decode($_REQUEST['envio'], true);
    $sql     = "SELECT * FROM traspasos_series where tra_id = " . $recibe["idtraspaso"] . "";
    $res     = $link->query($sql);
    $fila    = mysqli_fetch_array($res);
    $vardeta = json_decode($fila['tra_detalle'], true);
    $sql1    = "update traspasos_series set usu_id_recibe = {$recibe["idtecnico"]} where tra_id = {$recibe["idtraspaso"]}";
    $res1    = $link->query($sql1);

    foreach ($vardeta as $key) {
      $sql2 = "update serie_guia set usu_id_cargo = {$recibe["idtecnico"]} where ser_id = {$key["id"]}";
      $res2 = $link->query($sql2);
    }

    if ($res2) {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Cambio de técnico correcto');
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Error al cambiar el técnico');
    }

    echo json_encode($devuelve);

    break;

  case 'ExisteProveedor':
    $sql  = "select COUNT(*) as total from proveedores where rut='" . $_REQUEST["rut"] . "'";
    $res  = $link->query($sql);
    $fila = mysqli_fetch_array($res);
    echo $fila["total"];
    break;

  case 'getTabProveedores':
    $sql = "select * from proveedores";
    $res = $link->query($sql);
    $datos = array();
    while ($fila = mysqli_fetch_array($res)) {
      $ncontactos = 0;
      $contactos = array();
      $sql1 = "select * from contactoproveedores where proveedor='" . $fila["id"] . "'";
      $res1 = $link->query($sql1);
      while ($fila1 = mysqli_fetch_array($res1)) {
        $ncontactos++;
        $contactos[$fila1["id"]] = array("nombre" => $fila1["nombre"], "telefono" => $fila1["telefono"], "correo" => $fila1["correo"], "cargo" => $fila1["cargo"]);
      }
      $datos[$fila["id"]] = array("rut" => $fila["rut"], "razonsocial" => $fila["razonsocial"], "giro" => $fila["giro"], "region" => $fila["region"], "comuna" => $fila["comuna"], "direccion" => $fila["direccion"], "telefono" => $fila["telefono"], "correo" => $fila["correo"], "ncontactos" => $ncontactos, "contactos" => $contactos);
    }
    echo json_encode($datos);
    break;

  case 'nuevocontactoproveedor':
    $sql = "insert into contactoproveedores(proveedor,nombre,telefono,correo,cargo)values('" . $_REQUEST["proveedor"] . "','" . $_REQUEST["nombre"] . "','" . $_REQUEST["telefono"] . "','" . $_REQUEST["correo"] . "','" . $_REQUEST["cargo"] . "')";
    $res = $link->query($sql);
    if ($_REQUEST["desde"] == "solicituddecompra") {
      //$contactos = getContactosProv($_REQUEST["proveedor"]);
      $contactos = array();
      echo json_encode($contactos);
    } else {
      $sale_a = $_REQUEST["retornar"];
    }
    break;

  case 'getContactosProveedor':
    $sql = "select * from contactoproveedores where proveedor='" . $_REQUEST["proveedor"] . "'";
    $res = $link->query($sql);
    $opciones = "";
    while ($fila = mysqli_fetch_array($res)) {
      $opciones .= "<option value='" . $fila["id"] . "'>" . $fila["nombre"] . "</option>";
    }
    echo $opciones;
    break;

  case 'getTabContactosProveedor':
    $sql = "select * from contactoproveedores";
    $res = $link->query($sql);
    $datos = array();
    while ($fila = mysqli_fetch_array($res)) {
      $datos[$fila["id"]] = array("nombre" => $fila["nombre"], "telefono" => $fila["telefono"], "correo" => $fila["correo"], "cargo" => $fila["cargo"], "proveedor" => obtenervalor("proveedores", "razonsocial", "where id='" . $fila["proveedor"] . "'"));
    }
    echo json_encode($datos);
    break;

  case 'BorrarContactoProveedor':
    $sql = "delete from contactoproveedores where id='" . $_REQUEST["contacto"] . "'";
    $res = $link->query($sql);
    echo "contacto eliminado";
    break;

  case 'EditarContactoProveedor':
    $sql = "update contactoproveedores set nombre='" . $_REQUEST["nombre"] . "',telefono='" . $_REQUEST["telefono"] . "',correo='" . $_REQUEST["correo"] . "',cargo='" . $_REQUEST["cargo"] . "' where id='" . $_REQUEST["contacto"] . "'";
    $res = $link->query($sql);
    echo "contacto actualizado";
    break;
    /*******************************************************************
OPERACIONES TICKETS
     ********************************************************************/
  case 'getVehCli':
    //$sql="select veh.*,tveh.* from  vehiculos veh  left outer join tiposdevehiculos tveh on veh.veh_tipo=tveh.tveh_id where veh.veh_cliente='".$_REQUEST["veh_cliente"]."'";
    $sql = "select veh.*,tveh.* from vehiculos veh left outer join tiposdevehiculos tveh on veh.veh_tipo=tveh.tveh_id LEFT OUTER JOIN clientes cli ON cli.id=veh.veh_cliente where cli.cuenta='{$_REQUEST["veh_cliente"]}' and veh.veh_estado = '0' and veh.deleted_at is NULL";
    $res = $link->query($sql);
    $vehiculos = array();
    while ($fila = mysqli_fetch_array($res)) {
      $vehiculos[] = array("idveh" => $fila["veh_id"], "contacto" => $fila["veh_contacto"], "celular" => $fila["veh_celular"], "tservicio" => $fila["veh_tservicio"], "dispositivo" => $fila["veh_dispositivo"], "idtipo" => $fila["veh_tipo"], "tipo" => $fila["tveh_nombre"], "idgps" => $fila["veh_gps"], "patente" => $fila["veh_patente"], "veh_marca" => $fila["veh_marca"], "veh_modelo" => $fila["veh_modelo"]);
    }
    echo json_encode($vehiculos);
    break;

  case 'cargaselect':
    $recibe = json_decode($_REQUEST['envio'], true);
    $devuelve = array();
    if ($recibe['id'] == 'modelo') {
      $sql = "SELECT * FROM modelo where mod_idmarca = " . $recibe['valor'] . "";
      $res = $link->query($sql);

      if (mysqli_num_rows($res) > 0) {
        foreach ($res as $key => $dat) {
          if ($key == 0) {
            $devuelve['option'][] = array('<option value="0">Seleccione</option>');
          }
          $devuelve['option'][] = array('<option value="' . $dat['mod_id'] . '">' . $dat['mod_nombre'] . '</option>');
        }
      }
    }

    echo json_encode($devuelve);
    break;

  case 'nuevotipodetrabajo':

    $sql = "SELECT MAX(ttra_id) as max FROM tiposdetrabajos";
    $res = $link->query($sql);
    $fila = mysqli_fetch_array($res);

    $sql = "insert into tiposdetrabajos(ttra_id,ttra_nombre)values(" . ($fila['max'] + 1) . ",'" . $_REQUEST["nombre"] . "')";
    $res = $link->query($sql);
    /* echo $sql;
    die();*/
    $sale_a = $_REQUEST["retornar"];
    break;

  case 'editartipodetrabajo':
    $sql = "update tiposdetrabajos set ttra_nombre='" . $_REQUEST["nombre"] . "' where ttra_id='" . $_REQUEST["idttra"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminartipodetrabajo':
    $sql = "update tiposdetrabajos set deleted_at=now() where ttra_id='" . $_REQUEST["idttra"] . "'";
    //$sql="delete from tiposdetrabajos where ttra_id='".$_REQUEST["idttra"]."'";
    $res = $link->query($sql);
    break;


  case 'nuevotdi':
    $sql = "insert into tiposdedispositivos(tdi_nombre)values('" . $_REQUEST["nombre"] . "')";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'editartdi':
    $sql = "update tiposdedispositivos set tdi_nombre='" . $_REQUEST["nombre"] . "' where tdi_id='" . $_REQUEST["idtdi"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;
  case 'eliminartdi':
    $sql = "delete from tiposdedispositivos where tdi_id='" . $_REQUEST["idtdi"] . "'";
    $res = $link->query($sql);
    break;

  case 'nuevoticket':
    (int)$dis = 0;
    (int)$tra = 0;
    (int)$ser = 0;
    if ($_REQUEST['dispositivo'] != '' || $_REQUEST['dispositivo'] != null || $_REQUEST['dispositivo'] != 0) {
      (int)$dis = $_REQUEST['dispositivo'];
    }

    if ($_REQUEST['tipodtrab'] != '' || $_REQUEST['tipodtrab'] != null || $_REQUEST['tipodtrab'] != 0) {
      (int)$tra = $_REQUEST['tipodtrab'];
    }

    if ($_REQUEST['tipodserv'] != '' || $_REQUEST['tipodserv'] != null || $_REQUEST['tipodserv'] != 0) {
      (int)$ser = $_REQUEST['tipodserv'];
    } else {
      $ser = 1;
    }

    if ($_REQUEST['tic_tipo_prestador'] != '' || $_REQUEST['tic_tipo_prestador'] != null || $_REQUEST['tic_tipo_prestador'] != 0) {
      $prestador = "'" . $_REQUEST['tic_tipo_prestador'] . "'";
    } else {
      $prestador = "''";
    }

    if ($_REQUEST['tic_usuario_externo'] != '' || $_REQUEST['tic_usuario_externo'] != null || $_REQUEST['tic_usuario_externo'] != 0) {
      $usuarioExterno = "'" . $_REQUEST['tic_usuario_externo'] . "'";
    } else {
      $usuarioExterno = "0";
    }


    $sql = "SELECT * FROM `tickets` where tic_patente = 3647 and tic_estado != 3 and tic_estado != 4 and tic_estado != 7";
    $res = $link->query($sql);
    if (mysqli_num_rows($res) > 0) {
      header("Location: http://18.234.82.208/cloux/index.php?menu=tickets&idmenu=100&repetido=1");
      /* exit;*/
      die();
    }

    //rsocial
    $idpersonal = 0;
    $estadoTic = 1;
    if ($usuarioExterno != "0") {
      $estadoTic = 2;
      $sql = "SELECT usu_idpersonal FROM usuarios where usu_id = " . $usuarioExterno . "";
      $res = $link->query($sql);
      if ($res) {
        $fila = mysqli_fetch_array($res);
        $idpersonal = $fila['usu_idpersonal'] == null || $fila['usu_idpersonal'] == '' ? 0 : $fila['usu_idpersonal'];
      }
    }


    //tic_usuario_externo

    // estados 1=> PENDIENTE 2=> AGENDADO 3=>EJECUTADO 4=>FINALIZADO
    $sql = "INSERT INTO tickets(tic_cliente,tic_rsocial,tic_fechahorareg,tic_patente,tic_dispositivo,tic_tipotrabajo,
                                tic_tiposervicio,tic_contacto,tic_celular,tic_lugar,tic_descripcion,
                                tic_estado,tic_fechaagenda,tic_horaagenda, tic_tecnico, tic_descagenda, 
                                tic_fechacierre, tic_nserie, tic_desccierre, tic_tipo_prestador, tic_usuario_externo)
            values('" . $_REQUEST["cliente"] . "','" . $_REQUEST["rsocial"] . "','" . $fechachile . "','" . $_REQUEST["patente"] . "'," . $dis . "," . $tra . ",
                    " . $ser . ",'" . $_REQUEST["contacto"] . "','" . $_REQUEST["celular"] . "','" . $_REQUEST["lugar"] . "',
                    '" . $_REQUEST["descripcion"] . "','{$estadoTic}',now(),now(),'{$idpersonal}','',now(),'',''," . $prestador . "," . $usuarioExterno . ")";
    $res = $link->query($sql);
    /*echo $sql ;
    die() ;*/
    $id  = $link->insert_id;
    if (isset($_REQUEST['contacto'])) {
      $sqlupdate = "UPDATE vehiculos SET veh_contacto='{$_REQUEST["contacto"]}',veh_celular='{$_REQUEST["celular"]}' WHERE veh_id={$_REQUEST["patente"]} and deleted_at is NULL";
      $link->query($sqlupdate);
    }

    $h_tipo   = 1; // tipo 1: tickets
    $h_estado = 1; // pendiente
    Historial($h_tipo, $id, $h_estado);
    $sale_a = $_REQUEST["retornar"] . "&ticket=OK";
    mysqli_close($link);
    break;

  case 'actualizaapp':
    $recibe = json_decode($_REQUEST['envio'], true);
    $sql = "SELECT tic_id, tic_estado, tic_estadofact FROM tickets where tic_id = '{$recibe['idticket']}'";
    $res = $link->query($sql);
    $response = array();
    $data = array();
    if (mysqli_num_rows($res) > 0) {
      $fila = mysqli_fetch_array($res);
      $data[] = array(
        'estadoapp' => $fila['tic_estado'],
      );
    }
    $response['res'] = $data;
    echo json_encode($response);
    break;

  case 'annular':
    $recibe = json_decode($_REQUEST['envio'], true);
    $sql = "UPDATE tickets SET tic_estado=7 where tic_id = '{$recibe['idtick']}'";
    $res = $link->query($sql);
    $response = array();
    $response = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');
    if ($res) {
      $response = array('respuesta' => 'success', 'mensaje' => 'Actualizado correctamente');
    }

    echo json_encode($response);
    break;

  case 'updatealb':
    $recibe = json_decode($_REQUEST['envio'], true);
    $sql = "UPDATE tickets SET tic_comuna_ori='{$recibe['origen']}',tic_comuna_des='{$recibe['destino']}',tic_kmsdist='{$recibe['kms']}' where tic_id = '{$recibe['id']}'";
    $res = $link->query($sql);
    $response = array();
    $response = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error');
    if ($res) {
      $response = array('respuesta' => 'success', 'mensaje' => 'Actualizado correctamente');
    }

    echo json_encode($response);
    break;

  case 'traematriz':
    $recibe = json_decode($_REQUEST['envio'], true);
    $sql = "SELECT * FROM matriz_comunas where mcom_idorigen = {$recibe['idorigen']} and mcom_iddestino = {$recibe['iddestino']}";
    $res = $link->query($sql);
    /*echo $sql.'<br>';*/
    $valor = 0;
    if (mysqli_num_rows($res) > 0) {
      $fila = mysqli_fetch_array($res);
      $valor = $fila['mcom_kms'];
    }
    $response = array();
    $response = array('respuesta' => 'error', 'mensaje' => 'Ha ocurrido un error', 'km' => $valor);
    if ($res) {
      $response = array('respuesta' => 'success', 'mensaje' => 'Actualizado correctamente', 'km' => $valor);
    }

    echo json_encode($response);
    break;

  case 'getTabTickets':


    $consql = '';
    if ($_REQUEST['tipo_trabajo'] == 0) {
      $consql = '';
    } else {
      $consql = ' and tic.tic_tipotrabajo = ' . $_REQUEST["tipo_trabajo"] . '';
    }

    if ($_SESSION['perfil_new'] == 3) {
      if ($_SESSION['cloux_new'] != 62) {
        $consql = ' and tic.tic_usuario_externo = ' . $_SESSION["cloux_new"] . '';
      }
    }

    if ($_REQUEST['clientealb'] != 'TODOS') {
      $in = '';
      $sql = "select * from clientes where cuenta = '{$_REQUEST['clientealb']}'";
      $res = $link->query($sql);
      if (mysqli_num_rows($res) > 0) {
        foreach ($res as $key => $cli) {
          $in .= $cli['id'] . ',';
        }
        $in = substr($in, 0, -1);
      }
      $consql = ' and tic.tic_cliente in (' . $in . ')';
    }
    $consqlcli = '';
    $dato = '';
    if ($_REQUEST['filestado'] != 'TODOS') {
      $dato = "'{$_REQUEST['filestado']}'";
      $consqlcli = ' and tic.tic_estado =' . $dato;
    } else {
      $consqlcli = ' and tic.tic_estado !=3 and tic.tic_estado !=4';
    } //pxv

    $filtroporusu = '';
    if ($_SESSION['cloux_new'] == 62) {
      $filtroporusu = ' and tic.tic_usuario_externo in (44,45,43,62)';
    }
    $sql = "SELECT 
              tic.*,cli.razonsocial,cli.cuenta,pxv.pxv_nserie,
              veh.veh_tipo,veh.veh_id,veh.veh_marca,veh.veh_modelo,veh.veh_patente,
              tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, 
              (select COUNT(tic_id) from tickets where tic_estado != 3 and tic_tipotrabajo = 1) as soporte, 
              (select COUNT(tic_id) from tickets where tic_estado != 3 and tic_tipotrabajo = 2) as instalacion, 
              (select COUNT(tic_id) from tickets where tic_estado != 3 and tic_tipotrabajo = 3) as desinstalacion, 
              (select COUNT(tic_id) from tickets where tic_estado != 3 and tic_tipotrabajo = 6) as demo, 
              ser.ser_nombre, com1.comuna_nombre origen, com2.comuna_nombre destino
              ,u.usu_nombre as nombre_usuario_externo
            from tickets tic 
            left outer join comunas com1 on com1.comuna_id = tic.tic_comuna_ori
            left outer join comunas com2 on com2.comuna_id = tic.tic_comuna_des
            left outer join clientes cli on tic.tic_cliente = id 
            left outer join vehiculos veh on tic.tic_patente = veh.veh_id 
            left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
            left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id 
            left outer join personal per on tic.tic_tecnico = per.per_id 
            LEFT OUTER JOIN productosxvehiculos pxv ON pxv.pxv_idveh=veh.veh_id AND pxv.pxv_estado=1
            inner join servicios ser on ser.ser_id = tic.tic_tiposervicio
            left outer join usuarios u on tic.tic_usuario_externo = u.usu_id 
            where tic_id>0 {$consql} {$consqlcli} {$filtroporusu}
            and cli.deleted_at is NULL 
            -- and veh.deleted_at is NULL 
            group by tic.tic_id -- and pxv_estado = 1
            order by 2 desc ";
    $res     = $link->query($sql);

    // echo $sql;
    $tickets = array();

    while ($fila = mysqli_fetch_array($res)) {
      $tecnicoNombre = '';
      $agenda = "--()";
      if ($fila["tic_estado"] == 2 || $fila["tic_estado"] == 3 || $fila["tic_estado"] == 5) {
        if ($fila["tic_tecnico"] == 0) {
          $tecnicoNombre = "NO ASIGNADO";
        } else {
          $tecnicoNombre = $fila["per_nombrecorto"];
        }

        $agenda = "<b>" . devfecha($fila["tic_fechaagenda"]) . " " . hhmm($fila["tic_horaagenda"]) . "</b> <br>(" . $tecnicoNombre . ")";
      }

      $firstDate  = new DateTime($fila["tic_fechahorareg"]);
      $secondDate = new DateTime($fechachile);
      $intvl = $firstDate->diff($secondDate);
      $series = '';
      $sql1 = "SELECT * FROM productosxvehiculos where pxv_idveh = {$fila['tic_patente']}";
      $res1 = $link->query($sql1);
      if (mysqli_num_rows($res1) > 0) {
        foreach ($res1 as $key1 => $data1) {
          $series .= $data1['pxv_nserie'] . ',';
        }
        $series = substr($series, 0, -1);
      }

      if ($fila['veh_tipo'] != 0) {
        $sqlv = "SELECT * FROM tiposdevehiculos where tveh_id = '{$fila['veh_tipo']}'";
        $resv = $link->query($sqlv);
        $filav = mysqli_fetch_array($resv);
      } else {
        $fila['veh_tipo'] = 'Sin Asignar';

        $filav['tveh_nombre'] = $fila['veh_tipo'];
      }


      /*echo $intvl->y . " year, " . $intvl->m." months and ".$intvl->d." day"; 
        echo "\n";
        echo $intvl->days . " days ";*/
      //$productos = getProxVeh($fila["veh_id"]);
      /*$dateinicio = new DateTime($fila["tic_fechahorareg"]);
        $ndias      = getDiasMes($fila["tic_fechahorareg"]);
        $datenow    = new DateTime(date("Y-m-d H:i:s"));
        $tiempo     = $dateinicio->diff($datenow);
        $meses      = $tiempo->m;
        $dias       = $tiempo->d;
        $nhoras     = $tiempo->h;
        $dmes       = $meses * $ndias;
        //$hdias=  $dias * 24;
        $diastotales = $dmes + $dias;*/

      $imgTrab = array();
      $sql1 = "SELECT timg_id, timg_tipo, timg_subtipo, timg_name FROM tickets_img WHERE timg_idticket='{$fila["tic_id"]}' ORDER BY timg_tipo";
      $res1 = $link->query($sql1);
      while ($fila1 = mysqli_fetch_array($res1)) {
        $tipo = '';
        $ntipo = '';
        if ($fila1['timg_tipo'] == 0) {
          $ntipo = 'Previa';
          if ($fila1['timg_subtipo'] == 1) {
            $tipo = 'F. Patente';
          }
          if ($fila1['timg_subtipo'] == 2) {
            $tipo = 'T. Instrumento';
          }
          if ($fila1['timg_subtipo'] == 3) {
            $tipo = 'P. Tablero';
          }
          if ($fila1['timg_subtipo'] == 4) {
            $tipo = 'D. Daños';
          }
        }
        if ($fila1['timg_tipo'] == 1) {
          $ntipo = 'Posterior';
          if ($fila1['timg_subtipo'] == 1) {
            $tipo = 'T. Instrumento';
          }
          if ($fila1['timg_subtipo'] == 2) {
            $tipo = 'Puntos Conexión';
          }
          if ($fila1['timg_subtipo'] == 3) {
            $tipo = 'V. Panorámica';
          }
          if ($fila1['timg_subtipo'] == 4) {
            $tipo = 'U. Equipo';
          }
        }
        $imgTrab[] = array(
          'id' => $fila1['timg_id'],
          'tipo' => $ntipo,
          'ntipo' => $tipo,
          'idtipo' => $fila1['timg_tipo'],
          'idsubtipo' => $fila1['timg_subtipo'],
          'img' => $fila1['timg_name']
        );
      }

      $accesorios = array();
      $sql1 = "SELECT ava_id, ava_idveh, ava_idguia, ava_serie, ava_estado FROM asociacion_vehiculos_accesorios WHERE ava_estado=1 AND ava_idveh='{$fila['tic_patente']}'";
      $res1 = $link->query($sql1);
      while ($fila1 = mysqli_fetch_array($res1)) {
        $npro = "";
        $sql2 = "SELECT pro.pro_nombre FROM serie_guia ser INNER JOIN productos pro ON pro.pro_id=ser.pro_id WHERE ser_id='{$fila1['ava_idguia']}'";
        $res2 = $link->query($sql2);
        while ($fila2 = mysqli_fetch_array($res2)) {
          $npro = $fila2['pro_nombre'];
        }
        $accesorios[] = array(
          'ser_id' => $fila1['ava_idguia'],
          'ser_codigo' => $fila1['ava_serie'],
          'pro_nombre' => $npro,
        );
      }

      $nserieCan = '';
      if ($fila['tic_patente'] != '' && $fila['tic_patente'] != null) {
        $sql1 = "SELECT ser_idcan FROM asociacion_vehiculos_sensores WHERE veh_id='{$fila['tic_patente']}'";
        $res1 = $link->query($sql1);
        while ($fila1 = mysqli_fetch_array($res1)) {
          $sql2 = "SELECT ser_codigo FROM serie_guia WHERE ser_id='{$fila1['ser_idcan']}'";
          $res2 = $link->query($sql2);
          if (mysqli_num_rows($res2) > 0) {
            $ser_id1 = mysqli_fetch_array($res2);
            $nserieCan = $ser_id1['ser_codigo'];
          }
        }
      }

      $estado = 'Sin asignar';
      if ($fila["tic_estado"] == 2) {
        $estado = '<span class="badge badge-danger">Pendiente</span>';
      } else if ($fila["tic_estado"] == 5) {
        $estado = '<span class="badge badge-success">Finalizado</span>';
      } else if ($fila["tic_estado"] == 6) {
        $estado = '<span class="badge badge-success">Finalizado App</span>';
      }

      $kms = 0;
      $sql1 = "SELECT mcom_kms FROM matriz_comunas WHERE mcom_idorigen=170 AND mcom_iddestino=61";
      $res1 = $link->query($sql1);
      while ($fila1 = mysqli_fetch_array($res1)) {
        $kms = $fila1["mcom_kms"];
      }
      if ($kms == null || $kms == '' || $kms == '0') {
        $kms = "N/A";
      }

      //tic_tiposervicio
      $tickets[] = array( //tic_seriesim
        "series" => $series,
        "countsop" => $fila["soporte"],
        "sernom" => $fila["ser_nombre"],
        "countins" => $fila["instalacion"],
        "countdes" => $fila["desinstalacion"],
        "countdem" => $fila["demo"],
        "id" => $fila["tic_id"],
        "tveh_nombre" => $filav['tveh_nombre'],
        "veh_marca" => $fila["veh_marca"],
        "veh_modelo" => $fila["veh_modelo"],
        "cuenta" => $fila["cuenta"],
        "tic_tipo_prestador" => $fila["tic_tipo_prestador"],
        "tic_usuario_externo" => $fila["tic_usuario_externo"],
        "nombre_usuario_externo" => $tecnicoNombre,
        "fechahorareg" => devfechahora($fila["tic_fechahorareg"]),
        "diastranscurridos" => $intvl->days,
        "idcliente"  => $fila["tic_cliente"],
        "cliente"    => $fila["razonsocial"],
        "id_rsocial" => $fila["tic_rsocial"],
        "idpatente" => $fila["tic_patente"],
        "patente" => $fila["veh_patente"],
        "iddispositivo" => $fila["tic_dispositivo"],
        "dispositivo" => $fila["tdi_nombre"],
        "idtiposervicio" => $fila["tic_tiposervicio"],
        'descagenda' => $fila['tic_descagenda'],
        "idtipotrabajo" => $fila["tic_tipotrabajo"],
        "tipotrabajo" => $fila["ttra_nombre"],
        "contacto" => $fila["tic_contacto"],
        "celular" => $fila["tic_celular"],
        "lugar" => $fila["tic_lugar"],
        "descripcion" => $fila["tic_descripcion"],
        "fechaagenda" => devfecha($fila["tic_fechaagenda"]),
        "origen" => $fila["origen"],
        "destino" => $fila["destino"],
        "kms" => $kms,
        "firmatec" => $fila["tic_firmaTec"],
        "firmacli" => $fila["tic_firmaCli"],
        "nombreFirma" => $fila["tic_nombrefirma"],
        "tecnico" => $tecnico,
        "idtecnico" => $fila["tic_tecnico"],
        "agenda" => $agenda,
        "hora" => hhmm($fila["tic_horaagenda"]),
        "idestado" => $fila["tic_estado"],
        "estadoapp" => $estado,
        "seriesim" => $fila["tic_seriesim"],
        'nserie' => ($fila['pxv_nserie'] == null ? "N/A" : $fila['pxv_nserie']),
        "nserieCan" => ($nserieCan == null ? "N/A" : $nserieCan),
        "img" => $imgTrab,
        "accesorios" => $accesorios
      );
    }

    echo json_encode($tickets);
    mysqli_close($link);

    break;

  case 'getDataTicket':

    $imgTrab = array();
    $sql1 = "SELECT timg_id, timg_tipo, timg_subtipo, timg_name FROM tickets_img WHERE timg_idticket='{$_REQUEST['idticket']}' ORDER BY timg_tipo";
    $res1 = $link->query($sql1);
    while ($fila1 = mysqli_fetch_array($res1)) {
      $tipo = '';
      $ntipo = '';
      if ($fila1['timg_tipo'] == 0) {
        $ntipo = 'Previa';
        if ($fila1['timg_subtipo'] == 1) {
          $tipo = 'F. Patente';
        }
        if ($fila1['timg_subtipo'] == 2) {
          $tipo = 'T. Instrumento';
        }
        if ($fila1['timg_subtipo'] == 3) {
          $tipo = 'P. Tablero';
        }
        if ($fila1['timg_subtipo'] == 4) {
          $tipo = 'D. Daños';
        }
      }
      if ($fila1['timg_tipo'] == 1) {
        $ntipo = 'Posterior';
        if ($fila1['timg_subtipo'] == 1) {
          $tipo = 'T. Instrumento';
        }
        if ($fila1['timg_subtipo'] == 2) {
          $tipo = 'Puntos Conexión';
        }
        if ($fila1['timg_subtipo'] == 3) {
          $tipo = 'V. Panorámica';
        }
        if ($fila1['timg_subtipo'] == 4) {
          $tipo = 'U. Equipo';
        }
      }
      $imgTrab[] = array(
        'id' => $fila1['timg_id'],
        'tipo' => $ntipo,
        'ntipo' => $tipo,
        'idtipo' => $fila1['timg_tipo'],
        'idsubtipo' => $fila1['timg_subtipo'],
        'img' => $fila1['timg_name']
      );
    }

    $accesorios = array();
    $sql1 = "SELECT ava_id, ava_idveh, ava_idguia, ava_serie, ava_estado FROM asociacion_vehiculos_accesorios WHERE ava_estado=1 AND ava_idveh='{$_REQUEST['idveh']}'";
    $res1 = $link->query($sql1);
    while ($fila1 = mysqli_fetch_array($res1)) {
      $npro = "";
      $sql2 = "SELECT pro.pro_nombre FROM serie_guia ser INNER JOIN productos pro ON pro.pro_id=ser.pro_id WHERE ser_id='{$fila1['ava_idguia']}'";
      $res2 = $link->query($sql2);
      while ($fila2 = mysqli_fetch_array($res2)) {
        $npro = $fila2['pro_nombre'];
      }
      $accesorios[] = array(
        'ser_id' => $fila1['ava_idguia'],
        'ser_codigo' => $fila1['ava_serie'],
        'pro_nombre' => $npro,
      );
    }

    $nserieCan = '';
    if ($_REQUEST['idveh'] != '' && $_REQUEST['idveh'] != null) {
      $sql1 = "SELECT ser_idcan FROM asociacion_vehiculos_sensores WHERE veh_id='{$_REQUEST['idveh']}'";
      $res1 = $link->query($sql1);
      while ($fila1 = mysqli_fetch_array($res1)) {
        $sql2 = "SELECT ser_codigo FROM serie_guia WHERE ser_id='{$fila1['ser_idcan']}'";
        $res2 = $link->query($sql2);
        if (mysqli_num_rows($res2) > 0) {
          $ser_id1 = mysqli_fetch_array($res2);
          $nserieCan = $ser_id1['ser_codigo'];
        }
      }
    }

    $nserie = '';
    $sql = "SELECT pxv_nserie FROM productosxvehiculos WHERE pxv_estado=1 AND pxv_idveh='{$_REQUEST['idveh']}'";
    $res = $link->query($sql);
    while ($fila = mysqli_fetch_array($res)) {
      $nserie = $fila['pxv_nserie'];
    }

    $idorigen = 0;
    $iddestino = 0;
    $seriesim = '';
    $firmaTec = '';
    $firmaCli = '';
    $nombrefirma = '';
    $sql = "SELECT tic_seriesim,tic_comuna_ori,tic_comuna_des,tic_firmaTec,tic_firmaCli,tic_nombrefirma FROM tickets WHERE tic_id='{$_REQUEST['idticket']}'";
    $res = $link->query($sql);
    while ($fila = mysqli_fetch_array($res)) {
      $seriesim = $fila['tic_seriesim'];
      $idorigen = $fila['tic_comuna_ori'];
      $iddestino = $fila['tic_comuna_des'];
      $firmaTec = $fila['tic_firmaTec'];
      $firmaCli = $fila['tic_firmaCli'];
      $nombrefirma = $fila['tic_nombrefirma'];
    }

    $kms = 0;
    $origen = "";
    $destino = "";
    $sql1 = "SELECT mcom_kms,mcom_comorigen,mcom_comdestino FROM matriz_comunas WHERE mcom_idorigen='{$idorigen}' AND mcom_iddestino='{$iddestino}'";
    $res1 = $link->query($sql1);
    while ($fila1 = mysqli_fetch_array($res1)) {
      $kms = $fila1["mcom_kms"];
      $origen = $fila1["mcom_comorigen"];
      $destino = $fila1["mcom_comdestino"];
    }
    if ($kms == null || $kms == '' || $kms == '0') {
      $kms = "N/A";
    }

    $response['img'] = $imgTrab;
    $response['accesorios'] = $accesorios;
    $response['nserie'] = ($nserie == null ? 'N/A' : $nserie);
    $response['nserieCan'] = ($nserieCan == null ? 'N/A' : $nserieCan);
    $response['origen'] = ($origen == null ? 'N/A' : $origen);
    $response['destino'] = ($destino == null ? 'N/A' : $destino);
    $response['kms'] = ($kms == null ? 'N/A' : $kms);
    $response['firmaTec'] = $firmaTec;
    $response['firmaCli'] = $firmaCli;
    $response['nombrefirma'] = $nombrefirma;
    echo json_encode($response);
    mysqli_close($link);
    break;

  case 'getTicketResumen':
    $sql1 = "select (case when veh.veh_region=0 then 'Sin asignar' else reg.region end)group_region,(case when veh.veh_comuna=0 then 'Sin asignar' else com.comuna_nombre end)group_comuna, tic.*,cli.razonsocial,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, veh.veh_region, veh.veh_comuna from tickets tic left outer join clientes cli on tic.tic_cliente = id left outer join vehiculos veh on tic.tic_patente = veh.veh_id left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id left outer join personal per on tic.tic_tecnico = per.per_id LEFT OUTER JOIN regiones reg ON reg.id=veh.veh_region LEFT OUTER JOIN comunas com ON com.comuna_id=veh.veh_comuna where tic.tic_estado !=3 GROUP BY veh.veh_region,veh.veh_comuna order by tic_id desc, veh.veh_region desc, veh.veh_comuna desc";
    //$sql="select (case when veh.veh_region=0 then 'Sin asignar' else reg.region end)group_region,(case when veh.veh_comuna=0 then 'Sin asignar' else com.comuna_nombre end)group_comuna, tic.*,cli.razonsocial,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, veh.veh_region, veh.veh_comuna from tickets tic left outer join clientes cli on tic.tic_cliente = id left outer join vehiculos veh on tic.tic_patente = veh.veh_id left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id left outer join personal per on tic.tic_tecnico = per.per_id LEFT OUTER JOIN regiones reg ON reg.id=veh.veh_region LEFT OUTER JOIN comunas com ON com.comuna_id=veh.veh_comuna where tic.tic_estado !=3 order by tic_id desc, veh.veh_region desc, veh.veh_comuna desc";
    $res1 = $link->query($sql1);
    $tickets1 = array();
    $html = '';

    $opcColor = 1;
    while ($fila1 = mysqli_fetch_array($res1)) {
      $sql = "select (case when veh.veh_region=0 then 'Sin asignar' else reg.region end)group_region,(case when veh.veh_comuna=0 then 'Sin asignar' else com.comuna_nombre end)group_comuna, tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, veh.veh_region, veh.veh_comuna from tickets tic left outer join clientes cli on tic.tic_cliente = id left outer join vehiculos veh on tic.tic_patente = veh.veh_id left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id left outer join personal per on tic.tic_tecnico = per.per_id LEFT OUTER JOIN regiones reg ON reg.id=veh.veh_region LEFT OUTER JOIN comunas com ON com.comuna_id=veh.veh_comuna where tic.tic_estado !=3 AND veh.veh_region={$fila1['veh_region']} AND veh.veh_comuna={$fila1['veh_comuna']} order by tic_id desc, veh.veh_region desc, veh.veh_comuna desc";
      $res = $link->query($sql);
      $colspan = 0;
      $td = '';
      $tickets = array();

      while ($fila = mysqli_fetch_array($res)) {
        $dateinicio = new DateTime($fila["tic_fechahorareg"]);
        $ndias = getDiasMes($fila["tic_fechahorareg"]);
        $datenow = new DateTime(date("Y-m-d H:i:s"));
        $tiempo = $dateinicio->diff($datenow);
        $meses = $tiempo->m;
        $dias = $tiempo->d;
        $nhoras = $tiempo->h;
        $dmes = $meses * $ndias;
        //$hdias=  $dias * 24;
        $diastotales = $dmes + $dias;
        $estado = '';
        if ($fila['tic_estado'] == 1) {
          $estado = "<span class='label label-danger btn-rounded'>PENDIENTE</span>";
        } else if ($fila['tic_estado'] == 2) {
          $estado = "<span class='label label-warning btn-rounded'>AGENDADO</span>";
        } else if ($fila['tic_estado'] == 3) {
          $estado = "<span class='label label-success btn-rounded'>CERRADO</span>";
        } else if ($fila['tic_estado'] == 5) {
          $estado = "<span class='label label-success btn-rounded'>FINALIZADO</span>";
        }

        if ($diastotales > 10) {
          $dias = "<span class='label label-danger btn-rounded pointer'>" . $diastotales . "</span>";
        } else if ($diastotales > 5 && $diastotales  <= 10) {
          $dias = "<span class='label label-warning btn-rounded pointer'>" . $diastotales . "</span>";
        } else {
          $dias = "<span class='label label-success btn-rounded pointer'>" . $diastotales . "</span>";
        }

        $tickets[] = array("dias" => $dias, "region" => $fila["veh_region"], "comuna" => $fila["veh_comuna"], "group_region" => $fila["group_region"], "group_comuna" => $fila["group_comuna"], "id" => $fila["tic_id"], "fechahorareg" => devfechahora($fila["tic_fechahorareg"]), "diastranscurridos" => $diastotales, "idcliente" => $fila["tic_cliente"], "cliente" => $fila["razonsocial"], "cuenta" => $fila["cuenta"], "idpatente" => $fila["tic_patente"], "patente" => $fila["veh_patente"], "iddispositivo" => $fila["tic_dispositivo"], "dispositivo" => $fila["tdi_nombre"], "idtipotrabajo" => $fila["tic_tipotrabajo"], "tipotrabajo" => $fila["ttra_nombre"], "contacto" => $fila["tic_contacto"], "celular" => $fila["tic_celular"], "lugar" => $fila["tic_lugar"], "descripcion" => $fila["tic_descripcion"], "fechaagenda" => devfecha($fila["tic_fechaagenda"]), "tecnico" => $tecnico, "idtecnico" => $fila["tic_tecnico"], "agenda" => $agenda, "hora" => hhmm($fila["tic_horaagenda"]), "idestado" => $fila["tic_estado"], "estado" => $estado);

        $colspan++;
      }

      $enter = true;
      foreach ($tickets as $key => $value) {
        if ($enter) {
          $color = "";
          if ($opcColor == 1) {
            $color = "#BDD7EE";
            $opcColor = 0;
          } else {
            $color = "#94D04F";
            $opcColor = 1;
          }
          $td .= '<tr><td style="background-color:' . $color . ';" rowspan="' . $colspan . '">' . $fila1['group_region'] . '-' . $fila1['group_comuna'] . '</td><td rowspan="' . $colspan . '">' . $colspan . '</td><td>' . $value['patente'] . '</td><td>' . $value['tipotrabajo'] . '</td><td>' . $value['dispositivo'] . '</td><td>' . $value['cuenta'] . '</td><td>' . $value['cliente'] . '</td><td>' . $value['descripcion'] . '</td><td>' . $value['estado'] . '</td><td>' . $value['dias'] . '</td></tr>';
          $enter = false;
        } else {
          $td .= '<tr><td>' . $value['patente'] . '</td><td>' . $value['tipotrabajo'] . '</td><td>' . $value['dispositivo'] . '</td><td>' . $value['cuenta'] . '</td><td>' . $value['cliente'] . '</td><td>' . $value['descripcion'] . '</td><td>' . $value['estado'] . '</td><td>' . $value['dias'] . '</td></tr>';
        }
      }

      $html .= $td;
    }

    $response['html'] = $html;
    echo json_encode($response);
    break;

  case 'getTabTicketsFinalizados':

    $extrasql = '';

    if ($_REQUEST['filestado'] == 7) {
      $extrasql = ' and tic.tic_estado=7';
    } else {
      $extrasql = ' and tic.tic_estado=3';
    }

    if ($_REQUEST['orderby'] == 'todos') {
      $sql = "select tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, com.comuna_nombre, avs.sen_id_1, avs.sen_id_2, avs.sen_id_3, com2.comuna_nombre as comunaorigen, mco.mcom_kms
            from tickets tic 
            left outer join clientes cli on tic.tic_cliente = id 
            left outer join vehiculos veh on tic.tic_patente = veh.veh_id 
            left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
            left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id
            left outer join personal per on tic.tic_tecnico = per.per_id 
            LEFT OUTER JOIN comunas com on com.comuna_id = tic.tic_comuna_des 
            LEFT OUTER JOIN comunas com2 on com2.comuna_id = tic.tic_comuna_ori
            left outer join asociacion_vehiculos_sensores avs on avs.veh_id = veh.veh_id and avs.avx_tecnico = tic.tic_tecnico 
            LEFT OUTER JOIN matriz_comunas mco on mco.mcom_idorigen = tic.tic_comuna_ori and mco.mcom_iddestino = tic.tic_comuna_des 
            where tic.tic_id>0 " . $extrasql . " group by tic_id order by tic_id desc 
            limit 50
            ";
    } else {
      $filtros = '';
      if ($_REQUEST['filccosto'] != '') {
        $filtros .= " and tic.tic_centrocosto={$_REQUEST['filccosto']} ";
      }
      if ($_REQUEST['filefact'] != '') {
        $filtros .= " and tic.tic_estadofact={$_REQUEST['filefact']} ";
      }
      if ($_REQUEST['filpagot'] != '') {
        $filtros .= " and tic.tic_pagot={$_REQUEST['filpagot']} ";
      }
      if ($_REQUEST['filcliente'] != '') {
        $filtros .= " and cli.cuenta='{$_REQUEST['filcliente']}'";
      }
      if ($_REQUEST['filtecnico'] != '') {
        $filtros .= " and tic.tic_tecnico={$_REQUEST['filtecnico']} ";
      }
      if ($_REQUEST['fildesde'] != '' && $_REQUEST['filhasta'] == '') {
        $filtros .= " and tic.tic_fechahorareg between '{$_REQUEST['fildesde']} 00:00:00' and '{$_REQUEST['fildesde']} 23:59:59'";
      } else if ($_REQUEST['fildesde'] != '' && $_REQUEST['filhasta'] != '') {
        $filtros .= " and tic.tic_fechahorareg between '{$_REQUEST['fildesde']} 00:00:00' and '{$_REQUEST['filhasta']} 23:59:59'";
      } else if ($_REQUEST['fildesde'] == '' && $_REQUEST['filhasta'] != '') {
        $filtros .= " and tic.tic_fechahorareg between '{$_REQUEST['filhasta']} 00:00:00' and '{$_REQUEST['filhasta']} 23:59:59'";
      }
      // if($_REQUEST['fildesde']!=''){
      //   $filtros .= " and tic.tic_fechahorareg='{$_REQUEST['fildesde']} 00:00:00' ";
      // }
      // if($_REQUEST['filhasta']!=''){
      //   $filtros .= " and tic.tic_fechahorareg='{$_REQUEST['filhasta']} 23:59:59' ";
      // }
      $sql = "SELECT 
                tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,
                tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, com.comuna_nombre, 
                avs.sen_id_1, avs.sen_id_2, avs.sen_id_3, com2.comuna_nombre as comunaorigen, mco.mcom_kms 
            from tickets tic 
            left outer join clientes cli on tic.tic_cliente = id 
            left outer join vehiculos veh on tic.tic_patente = veh.veh_id 
            left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
            left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id
            left outer join personal per on tic.tic_tecnico = per.per_id 
            LEFT OUTER JOIN comunas com on com.comuna_id = tic.tic_comuna_des 
            LEFT OUTER JOIN comunas com2 on com2.comuna_id = tic.tic_comuna_ori
            left outer join asociacion_vehiculos_sensores avs on avs.veh_id = veh.veh_id and avs.avx_tecnico = tic.tic_tecnico 
            LEFT OUTER JOIN matriz_comunas mco on mco.mcom_idorigen = tic.tic_comuna_ori and mco.mcom_iddestino = tic.tic_comuna_des
            where tic.tic_id>0 " . $extrasql . " {$filtros} group by tic_id order by tic_id desc 
            limit 50
            ";
    }

    $res        = $link->query($sql);
    $tickets    = array();
    // echo $sql.'<br>';
    while ($fila = mysqli_fetch_array($res)) {
      $agenda  = "--()";
      $tecnico = "";
      // if($fila["tic_estado"]==2 || $fila["tic_estado"]==3){
      // if($fila["tic_tecnico"]==0){
      // $tecnico="NO ASIGNADO";	
      // }else{
      // $tecnico=$fila["per_nombrecorto"];
      // }
      // $agenda="<b>".devfecha($fila["tic_fechaagenda"])." ".hhmm($fila["tic_horaagenda"])."</b> <br>(".$tecnico.")";	
      // }
      if ($fila["tic_horaagenda"] == '' || $fila["tic_horaagenda"] == null) {
        $fila["tic_horaagenda"] = '00:00:00';
      }
      $agenda      = "<b>" . devfecha($fila["tic_fechaagenda"]) . " " . hhmm($fila["tic_horaagenda"]);
      //$productos = getProxVeh($fila["veh_id"]);
      $dateinicio  = new DateTime($fila["tic_fechahorareg"]);
      $ndias       = getDiasMes($fila["tic_fechahorareg"]);
      $datenow     = new DateTime(date("Y-m-d H:i:s"));
      $tiempo      = $dateinicio->diff($datenow);
      $meses       = $tiempo->m;
      $dias        = $tiempo->d;
      $nhoras      = $tiempo->h;
      $dmes        = $meses * $ndias;
      //$hdias=  $dias * 24;
      $diastotales = $dmes + $dias;

      $firstDate  = new DateTime($fila['tic_fechahorareg']);
      $secondDate = new DateTime($fechachile);
      $intvl = $firstDate->diff($secondDate);

      $finalizosDate = new DateTime($fila['tic_fechacierre']);
      $intvlDiff = $firstDate->diff($finalizosDate);

      $nombredispositivo = '-';
      $series = '';
      $sql1 = "SELECT t1.*, t2.pro_nombre 
                FROM productosxvehiculos t1 
                LEFT OUTER JOIN productos t2 on t2.pro_id = t1.pxv_idpro
                where t1.pxv_idveh = {$fila['tic_patente']} order by 1 desc limit 1";
      $res1 = $link->query($sql1);
      $fila1 = mysqli_fetch_array($res1);
      if (mysqli_num_rows($res1) > 0) {
        /*foreach($res1 as $key1=>$data1){
                $series .= $data1['pxv_nserie'].',';
            }
            $series = substr($series, 0, -1);*/
        $series            = $fila1['pxv_nserie'];
        $nombredispositivo = $fila1['pro_nombre'];
      }

      if ($fila["tic_tipotrabajo"] == 3) {
        $series = $fila['tic_imeis'];
      }

      $accesorios = array();

      $sql1 = "SELECT ava_id, ava_idveh, ava_idguia, ava_serie, ava_estado 
                FROM asociacion_vehiculos_accesorios 
                WHERE ava_estado=1 AND ava_idveh='{$fila["tic_patente"]}'";
      // $res1 = $link->query($sql1);

      if($res1 && true){
        while ($fila1 = mysqli_fetch_array($res1)) {
          $npro = "";
          $sql2 = "SELECT pro.pro_nombre FROM serie_guia ser 
                    INNER JOIN productos pro ON pro.pro_id=ser.pro_id 
                    WHERE ser_id='{$fila1['ava_idguia']}'";
          $res2 = $link->query($sql2);
          while ($fila2 = mysqli_fetch_array($res2)) {
            $npro = $fila2['pro_nombre'];
          }
          $accesorios[] = array(
            'ser_id' => $fila1['ava_idguia'],
            'ser_codigo' => $fila1['ava_serie'],
            'pro_nombre' => $npro,
          );
        }
      }
      

      $tickets[] = array(
        "tic_valorkm" => $fila["tic_valorkm"],
        "tic_totalkm" => $fila["tic_totalkm"],
        "tic_costolabor" => $fila["tic_costolabor"],
        "tic_sseguridad" => ($fila["tic_sseguridad"] == null || $fila["tic_sseguridad"] == '' ? '-' : $fila["tic_sseguridad"]),
        "tic_id" => $fila["tic_id"],
        "mcom_kms" => $fila["mcom_kms"],
        "comunades" => $fila["comuna_nombre"],
        "comunaorigen" => $fila["comunaorigen"],
        "tic_kmsdist" => $fila["tic_kmsdist"],
        "comentario" => $fila["tic_desccierre"],
        "tic_comuna_ori" => $fila["tic_comuna_ori"],
        "tic_comuna_des" => $fila["tic_comuna_des"],
        "tic_um" => $fila["tic_um"],
        "series" => $series,
        "id" => $fila["tic_id"],
        "fechahorareg" => devfechahora($fila["tic_fechahorareg"]),
        "diastranscurridos" => $intvl->days,
        "idcliente" => $fila["tic_cliente"],
        "rs" => $fila["razonsocial"],
        "cliente" => $fila["cuenta"],
        "idpatente" => $fila["tic_patente"],
        "patente" => $fila["veh_patente"],
        "iddispositivo" => $fila["tic_dispositivo"],
        "dispositivo" => $nombredispositivo,
        "idtipotrabajo" => $fila["tic_tipotrabajo"],
        "tipotrabajo" => $fila["ttra_nombre"],
        "contacto" => $fila["tic_contacto"],
        "celular" => $fila["tic_celular"],
        "lugar" => $fila["tic_lugar"],
        "descripcion" => $fila["tic_descripcion"],
        "fechaagenda" => devfecha($fila["tic_fechacierre"]),
        "diferencia_dias" => ($intvlDiff->days),
        "tipo_servicio" => ($fila["tic_tiposervicio"] == 1 ? 'Avanzado' : 'Básico'),
        "tecnico" => $tecnico,
        "idtecnico" => $fila["tic_tecnico"],
        "agenda" => $agenda,
        "hora" => hhmm($fila["tic_horaagenda"]),
        "idestado" => $fila["tic_estado"],
        'tecnico' => $fila['per_nombrecorto'],
        'vtrabajo' => $fila['tic_valortrabajo'],
        'ccosto' => $fila['tic_centrocosto'],
        'estadofact' => $fila['tic_estadofact'],
        'pagot' => $fila['tic_pagot'],
        'img1' => $fila['tic_img1'],
        'img2' => $fila['tic_img2'],
        'img3' => $fila['tic_img3'],
        'img4' => $fila['tic_img4'],
        'img5' => $fila['tic_img5'],
        'ch_1' => ($fila['sen_id_1'] == 0 ? 'No' : 'Si'),
        'ch_2' => ($fila['sen_id_2'] == 0 ? 'No' : 'Si'),
        'ch_3' => ($fila['sen_id_3'] == 0 ? 'No' : 'Si'),
        'accesorios' => $accesorios,
        //,'sql'=>$sql
      );
    }

    echo json_encode($tickets);
    break;

  case 'cargadatos':
    $data = array();
    $labels = array();
    $valores = array();

    $recibe = json_decode($_REQUEST['envio'], true);

    if ($recibe['opc'] == 1) {
      $meses = array(['numero' => 1, 'nombre' => 'Enero'], ['numero' => 2, 'nombre' => 'Febrero'], ['numero' => 3, 'nombre' => 'Marzo'], ['numero' => 4, 'nombre' => 'Abril'], ['numero' => 5, 'nombre' => 'Mayo'], ['numero' => 6, 'nombre' => 'junio'], ['numero' => 7, 'nombre' => 'Julio'], ['numero' => 8, 'nombre' => 'Agosto'], ['numero' => 9, 'nombre' => 'Septiembre'], ['numero' => 10, 'nombre' => 'Octubre'], ['numero' => 11, 'nombre' => 'Noviembre'], ['numero' => 12, 'nombre' => 'Diciembre']);

      $ano_actual = date("Y");

      foreach ($meses as $keym => $mes) {
        $sql = "SELECT
                        SUM(CASE WHEN tic_tipotrabajo = 1 THEN 1 ELSE 0 END) AS soporte,
                        SUM(CASE WHEN tic_tipotrabajo = 2 THEN 1 ELSE 0 END) AS instalacion,
                        SUM(CASE WHEN tic_tipotrabajo = 3 THEN 1 ELSE 0 END) AS desinstalacion,
                        SUM(CASE WHEN tic_tipotrabajo = 6 THEN 1 ELSE 0 END) AS demo
                    FROM tickets
                    WHERE
                        (MONTH(tic_fechahorareg) = {$mes['numero']} AND YEAR(tic_fechahorareg) = {$ano_actual})";
        $res = $link->query($sql);
        $fila = mysqli_fetch_array($res);
        $ins = 0;
        $des = 0;
        $sop = 0;
        $dem = 0;
        if (mysqli_num_rows($res) > 0) {
          $ins = ($fila['instalacion'] == null || $fila['instalacion'] == '' ? 0 : $fila['instalacion']);
          $des = ($fila['desinstalacion'] == null || $fila['desinstalacion'] == '' ? 0 : $fila['desinstalacion']);
          $sop = ($fila['soporte'] == null || $fila['soporte'] == '' ? 0 : $fila['soporte']);
          $dem = ($fila['demo'] == null || $fila['demo'] == '' ? 0 : $fila['demo']);
        }
        $labels[] = array(
          'mes' => $mes['nombre'],
          'col1' => $sop,
          'col2' => $ins,
          'col3' => $des,
          'col4' => $dem,
        );
      }
    } else if ($recibe['opc'] == 2) {
      $meses = array(['numero' => 1, 'nombre' => 'Enero'], ['numero' => 2, 'nombre' => 'Febrero'], ['numero' => 3, 'nombre' => 'Marzo'], ['numero' => 4, 'nombre' => 'Abril'], ['numero' => 5, 'nombre' => 'Mayo'], ['numero' => 6, 'nombre' => 'junio'], ['numero' => 7, 'nombre' => 'Julio'], ['numero' => 8, 'nombre' => 'Agosto'], ['numero' => 9, 'nombre' => 'Septiembre'], ['numero' => 10, 'nombre' => 'Octubre'], ['numero' => 11, 'nombre' => 'Noviembre'], ['numero' => 12, 'nombre' => 'Diciembre']);

      $ano_actual = date("Y");

      $sql = "SELECT * FROM personal where per_estado = 1 and per_id!=26";
      $res = $link->query($sql);

      foreach ($meses as $keym => $mes) {
        $datapersonal = array();
        foreach ($res as $keyper => $personal) {
          $sql1 = "SELECT *
                        FROM tickets
                        WHERE (MONTH(tic_fechahorareg) = {$mes['numero']} AND YEAR(tic_fechahorareg) = {$ano_actual}) and tic_tecnico = {$personal['per_id']}";
          $res1 = $link->query($sql1);
          /* echo $sql1.'<br>';
                    echo $mes['nombre'].'<br>';*/
          $datapersonal[] = array(
            'tecnico' => $personal['per_nombres'],
            'trabajos' => mysqli_num_rows($res1),
          );
        }

        $pasa =  false;
        $conta = 0;
        foreach ($datapersonal as $key => $dat) {
          $conta = $conta + (int)$dat['trabajos'];
        }

        if ($conta > 0) {
          $pasa = true;
        }

        if ($pasa) {


          $labels[] = array(
            'mes' => $mes['nombre'],
            'lineas' => $datapersonal,
          );
        }
      }
    } else if ($recibe['opc'] == 3) {
      $meses = array(['numero' => 1, 'nombre' => 'Enero'], ['numero' => 2, 'nombre' => 'Febrero'], ['numero' => 3, 'nombre' => 'Marzo'], ['numero' => 4, 'nombre' => 'Abril'], ['numero' => 5, 'nombre' => 'Mayo'], ['numero' => 6, 'nombre' => 'junio'], ['numero' => 7, 'nombre' => 'Julio'], ['numero' => 8, 'nombre' => 'Agosto'], ['numero' => 9, 'nombre' => 'Septiembre'], ['numero' => 10, 'nombre' => 'Octubre'], ['numero' => 11, 'nombre' => 'Noviembre'], ['numero' => 12, 'nombre' => 'Diciembre']);

      $ano_actual = date("Y");

      foreach ($meses as $keym => $mes) {
        $sql = "SELECT
                        SUM(CASE WHEN tic_centrocosto = 2 THEN tic_costolabor ELSE 0 END) AS interno,
                        SUM(CASE WHEN tic_centrocosto = 1 THEN tic_costolabor ELSE 0 END) AS cliente
                    FROM tickets
                    WHERE
                        (MONTH(tic_fechahorareg) = {$mes['numero']} AND YEAR(tic_fechahorareg) = {$ano_actual}) and tic_estado = 3";
        $res = $link->query($sql);
        $fila = mysqli_fetch_array($res);
        $interno = 0;
        $cliente = 0;

        if (mysqli_num_rows($res) > 0) {
          $interno = ($fila['interno'] == null || $fila['interno'] == '' ? 0 : $fila['interno']);
          $cliente = ($fila['cliente'] == null || $fila['cliente'] == '' ? 0 : $fila['cliente']);
        }
        $labels[] = array(
          'mes' => $mes['nombre'],
          'inerno' => $interno,
          'cliente' => $cliente,
        );
      }
    }

    $data['labels'] = $labels;
    echo json_encode($data);
    break;


  case 'generaexcel':
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $recibe      = json_decode($_REQUEST['envio'], true);
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("D-Solutions");
    $objPHPExcel->getProperties()->setLastModifiedBy("D-Solutions");
    $objPHPExcel->getProperties()->setTitle("Resumen de tickets");
    $objPHPExcel->getProperties()->setSubject("Resumen de tickets");
    $objPHPExcel->getProperties()->setDescription("Resumen de tickets");
    $objPHPExcel->setActiveSheetIndex(0);

    $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
    /*$objPHPExcel->getActiveSheet()->mergeCells("A1:N1");*/
    $style = array(
      'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
      )
    );

    $letcabeceras   = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L');
    $datoscabeceras = array('Fecha Registro', 'Días', 'Cliente', 'Patente', 'Dispositivo', 'Tipo Trabajo', 'Contacto', 'Celular', 'Lugar', 'Descripción', 'Agenda', 'Estado');

    $objPHPExcel->getActiveSheet()->getStyle("A1:L1")->applyFromArray($style);
    $objPHPExcel->getActiveSheet()->getStyle("A1:L1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $ind = 0;
    foreach ($letcabeceras as $let) {
      $objPHPExcel->getActiveSheet()->SetCellValue($let . '1', $datoscabeceras[$ind]);
      cellColor($let . '1', 'EAEAEA');
      $objPHPExcel->getActiveSheet()->getColumnDimension($let)->setWidth(20);
      $ind++;
    }

    $sql = "SELECT tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto
        from tickets tic 
        left outer join clientes cli on tic.tic_cliente = id 
        left outer join vehiculos veh on tic.tic_patente = veh.veh_id 
        left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
        left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id 
        left outer join personal per on tic.tic_tecnico = per.per_id 
        where tic.tic_estado !=3 and tic.tic_estado !=4 and tic.tic_estado !=7
        order by 2 desc";
    // and tic.tic_fechahorareg BETWEEN '{$recibe['desde']}' and '{$recibe['hasta']}'
    $res   = $link->query($sql);
    $index = 2;

    foreach ($res as $key) {
      $firstDate  = new DateTime($key['tic_fechahorareg']);
      $secondDate = new DateTime($fechachile);
      $intvl = $firstDate->diff($secondDate);

      if ($key['tic_estado'] == 1) {
        $estado = 'Pendiente'; //rojo
        cellColor('L' . ($index), 'F34E4E');
      } else if ($key['tic_estado'] == 2) {
        $estado = 'Agendado'; //amarillo
        cellColor('L' . ($index), 'E6EC63');
      } else {
        $estado = 'Cerrado';
      }

      $agenda = "--()";
      if ($key["tic_estado"] == 2 || $key["tic_estado"] == 3 || $key["tic_estado"] == 5) {
        if ($key["tic_tecnico"] == 0) {
          $tecnico = "NO ASIGNADO";
        } else {
          $tecnico = $key["per_nombrecorto"];
        }
        $agenda = $key["tic_fechaagenda"];
        if ($key["tic_fechaagenda"] != '') {
          $agenda = devfecha($key["tic_fechaagenda"]);
        }
        /*echo $agenda.'<br>';*/
        $agenda = $agenda . " " . hhmm($key["tic_horaagenda"]) . "(" . $tecnico . ")";
      }

      $objPHPExcel->getActiveSheet()->SetCellValue('A' . ($index), $key['tic_fechahorareg']);
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . ($index), $intvl->days);
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . ($index), strtolower($key['cuenta']));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . ($index), $key['veh_patente']);
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . ($index), $key['tdi_nombre']);
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . ($index), $key['ttra_nombre']);
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . ($index), $key['tic_contacto']);
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . ($index), $key['tic_celular']);
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . ($index), $key['tic_lugar']);
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . ($index), $key['tic_descripcion']);
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . ($index), $agenda);
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . ($index), $estado);
      $index++;
    }

    $objPHPExcel->getActiveSheet()->setTitle('Resumen de tickets');
    $objPHPExcel->setActiveSheetIndex(0);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_start();
    $objWriter->save("php://output");
    $xlsData = ob_get_contents();
    ob_end_clean();
    $response =  array(
      'op' => 'ok',
      'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
    );
    echo json_encode($response);
    break;

  case 'generaexcelfin':


    $_SESSION['colorprin'] = '#7058c3';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('max_execution_time', '360');
    ini_set('memory_limit', '128M');
    setlocale(LC_MONETARY, 'en_US');

    $fecha = date('d-m-Y H:i:s');
    $via = json_decode($_REQUEST['datos'], true);

    $letras = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK");

    try {
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("D-Solutions");
      $objPHPExcel->getProperties()->setLastModifiedBy("D-Solutions");
      $objPHPExcel->getProperties()->setTitle("Trabajos Finalizados");
      $objPHPExcel->getProperties()->setSubject("Trabajos Finalizados");
      $objPHPExcel->getProperties()->setDescription("Trabajos Finalizados");
      $objPHPExcel->setActiveSheetIndex(0);

      $headers = $via[0];
      $letrafinal = '';
      $indice = 0;
      $style = array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '7058c3'),
        ),
        'font' => array(
          'color' => array('rgb' => 'FFFFFF'),
        ),
      );

      for ($i = 0; $i < count($headers); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue($letras[$indice] . '1', $headers[$indice]);
        $letrafinal = $letras[$indice];
        $cell = $objPHPExcel->getActiveSheet()->getCell($letras[$indice] . '1');
        $objPHPExcel->getActiveSheet()->getStyle($cell->getCoordinate())->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getColumnDimension($letras[$indice])->setWidth(15);
        $indice++;
      }

      $objPHPExcel->getActiveSheet()->getStyle('A1:' . $letrafinal . '1')->getFont()->setBold(true);
      $style = array(
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
      );

      $objPHPExcel->getActiveSheet()->getStyle("A1:" . $letrafinal . "1")->applyFromArray($style);

      $indice = 0;
      for ($i = 1; $i <= count($via); $i++) {
        $indice2 = 0;
        for ($o = 0; $o < count($headers); $o++) {

          $objPHPExcel->getActiveSheet()->SetCellValue($letras[$indice2] . $i, $via[$indice][$o]);
          $indice2++;
        }
        $indice++;
      }

      $objPHPExcel->getActiveSheet()->setTitle('Trabajos Finalizados');
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      ob_start();
      $objWriter->save("php://output");
      $xlsData = ob_get_contents();
      ob_end_clean();
      $response =  array(
        'op' => 'ok',
        'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
      );
      echo json_encode($response);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }

    /*error_reporting(E_ALL);
      ini_set('display_errors', '1');

      $recibe      = json_decode($_REQUEST['envio'],true); 
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("D-Solutions");
      $objPHPExcel->getProperties()->setLastModifiedBy("D-Solutions");
      $objPHPExcel->getProperties()->setTitle("Trabajajos Finalizados");
      $objPHPExcel->getProperties()->setSubject("Trabajajos Finalizados");
      $objPHPExcel->getProperties()->setDescription("Trabajajos Finalizados");
      $objPHPExcel->setActiveSheetIndex(0);
  
      $objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getFont()->setBold(true);

      $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );

      $letcabeceras   = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
      $datoscabeceras = array('Fecha Labor','Técnico','Cliente','Razon Social','Patente','Dispositivo','Tipo Trabajo','Imei','Sello Seguridad','Ch1','Ch2','Ch3','Lugar','Comuna Orig.','Comuna Dest.','Kms Dest.','Descripción','Comentario','U.M','Valor trabajo','Valor KM','Total KM','Costo Labor','Centro Costo','Estado Facturación','Pago Técnico','Estado');

      $objPHPExcel->getActiveSheet()->getStyle("A1:Z1")->applyFromArray($style);
      $objPHPExcel->getActiveSheet()->getStyle("A1:Z1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
      $ind = 0;
      foreach($letcabeceras as $let){
         $objPHPExcel->getActiveSheet()->SetCellValue($let.'1', $datoscabeceras[$ind]);
         cellColor($let.'1', 'EAEAEA');
         $objPHPExcel->getActiveSheet()->getColumnDimension($let)->setWidth(20);
         $ind++;
      }
      
     
      $filtros = '';
      if($recibe['ccosto']!=''){
        $filtros .= " and tic.tic_centrocosto={$recibe['ccosto']} ";
      }

      if($recibe['estadofact']!=''){
        $filtros .= " and tic.tic_estadofact={$recibe['estadofact']} ";
      }

      if($recibe['pagot']!=''){
        $filtros .= " and tic.tic_pagot={$recibe['pagot']} ";
      }

      if($recibe['cliente']!=''){
        $filtros .= " and tic.tic_cliente={$recibe['cliente']} ";
      }

      if($recibe['tecnico']!=''){
        $filtros .= " and tic.tic_tecnico={$recibe['tecnico']} ";
      }

      if($recibe['desde']!='' && $recibe['hasta']==''){
        $filtros .= " and tic.tic_fechahorareg between '{$recibe['desde']} 00:00:00' and '{$recibe['desde']} 23:59:59'";
      }else if($recibe['desde']!='' && $recibe['hasta']!=''){
        $filtros .= " and tic.tic_fechahorareg between '{$recibe['desde']} 00:00:00' and '{$recibe['hasta']} 23:59:59'";
      }else if($recibe['desde']=='' && $recibe['hasta']!=''){
        $filtros .= " and tic.tic_fechahorareg between '{$recibe['hasta']} 00:00:00' and '{$recibe['hasta']} 23:59:59'";
      }

      $sql = "select tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, com.comuna_nombre, avs.sen_id_1, avs.sen_id_2, avs.sen_id_3, com2.comuna_nombre as comunaorigen, mco.mcom_kms
            from tickets tic 
            left outer join clientes cli on tic.tic_cliente = id 
            left outer join vehiculos veh on tic.tic_patente = veh.veh_id 
            left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
            left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id
            left outer join personal per on tic.tic_tecnico = per.per_id 
            LEFT OUTER JOIN comunas com on com.comuna_id = tic.tic_comuna_des 
            LEFT OUTER JOIN comunas com2 on com2.comuna_id = tic.tic_comuna_ori
            left outer join asociacion_vehiculos_sensores avs on avs.veh_id = veh.veh_id and avs.avx_tecnico = tic.tic_tecnico 
            LEFT OUTER JOIN matriz_comunas mco on mco.mcom_idorigen = tic.tic_comuna_ori and mco.mcom_iddestino = tic.tic_comuna_des 
            where tic.tic_estado=3 {$filtros} group by tic_id order by tic_id desc";
      $res   = $link->query($sql);
      $index = 2;

      foreach($res as $key){
          $firstDate  = new DateTime($key['tic_fechahorareg']);
          $secondDate = new DateTime($fechachile);
          $intvl      = $firstDate->diff($secondDate);

          if($key["tic_pagot"]==1){
             $pagot = 'Si';
          }else if($key["tic_pagot"]==2){
             $pagot = 'No';
          }else{
             $pagot = 'N/A';
          }

          if($key["tic_estadofact"]==1){
             $estadof = 'OK';
          }else{
             $estadof = 'Pendiente';
          }

          if($key["tic_centrocosto"]==1){
             $centroc = 'Cliente';
          }else if($key["tic_centrocosto"]==2){
             $centroc = 'Interno';
          }else{
             $centroc = 'N/A';
          }

            $series = '';
            $sql1 = "SELECT * FROM productosxvehiculos where pxv_idveh = {$key['tic_patente']} order by 1 desc limit 1";
            $res1 = $link->query($sql1);
            $fila1 = mysqli_fetch_array($res1);
            if(mysqli_num_rows($res1)>0){
                $series = $fila1['pxv_nserie'];
            }

          $objPHPExcel->getActiveSheet()->SetCellValue('A'.($index), $key['tic_fechahorareg']);
          $objPHPExcel->getActiveSheet()->SetCellValue('B'.($index), $key['per_nombrecorto']);
          $objPHPExcel->getActiveSheet()->SetCellValue('C'.($index), strtolower($key['cuenta']));
          $objPHPExcel->getActiveSheet()->SetCellValue('D'.($index), $key['razonsocial']);
          $objPHPExcel->getActiveSheet()->SetCellValue('E'.($index), $key['veh_patente']);
          $objPHPExcel->getActiveSheet()->SetCellValue('F'.($index), $key['tdi_nombre']);
          $objPHPExcel->getActiveSheet()->SetCellValue('G'.($index), $key['ttra_nombre']);
          $objPHPExcel->getActiveSheet()->setCellValue('H'.($index), $series);
          $objPHPExcel->getActiveSheet()->getStyle('H'.($index))->getNumberFormat()->setFormatCode('0');

          $objPHPExcel->getActiveSheet()->SetCellValue('I'.($index), ($key['tic_sseguridad']=='' || $key['tic_sseguridad']=='null'?'-':$key['tic_sseguridad']));
          $objPHPExcel->getActiveSheet()->SetCellValue('J'.($index), ($key['sen_id_1']==0?'No':'Si'));
          $objPHPExcel->getActiveSheet()->SetCellValue('K'.($index), ($key['sen_id_1']==0?'No':'Si'));
          $objPHPExcel->getActiveSheet()->SetCellValue('L'.($index), ($key['sen_id_1']==0?'No':'Si'));
          $objPHPExcel->getActiveSheet()->SetCellValue('M'.($index), $key["tic_lugar"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('N'.($index), $key["comunaorigen"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('O'.($index), $key["comuna_nombre"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('P'.($index), $key["mcom_kms"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('Q'.($index), $key["tic_descripcion"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('R'.($index), $key["tic_desccierre"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('S'.($index), $key['tic_valortrabajo']);
          $objPHPExcel->getActiveSheet()->SetCellValue('T'.($index), ($key['tic_um']==1?'CLP':'UF'));
          $objPHPExcel->getActiveSheet()->SetCellValue('U'.($index), $key["tic_valortrabajo"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('V'.($index), $key["tic_valorkm"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('W'.($index), $key["tic_totalkm"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('X'.($index), $key["tic_costolabor"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('Y'.($index), $centroc);
          $objPHPExcel->getActiveSheet()->SetCellValue('Z'.($index), $estadof);
          $objPHPExcel->getActiveSheet()->SetCellValue('AA'.($index), $pagot);
          
          $index++;
      }

      $objPHPExcel->getActiveSheet()->setTitle('Trabajos Finalizados');
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      ob_start();
      $objWriter->save("php://output");
      $xlsData = ob_get_contents();
      ob_end_clean();
      $response =  array(
          'op' => 'ok',
          'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($xlsData)
      );
      echo json_encode($response);*/
    break;

  case 'getNtickets':
    $sql       = "select * from tickets where tic_estado !=3 ";
    $res       = $link->query($sql);
    $nverde    = 0;
    $namarillo = 0;
    $nrojo     = 0;
    while ($fila = mysqli_fetch_array($res)) {
      $dateinicio = new DateTime($fila["tic_fechahorareg"]);
      $ndias      = getDiasMes($fila["tic_fechahorareg"]);
      $datenow    = new DateTime(date("Y-m-d H:i:s"));
      $tiempo     = $dateinicio->diff($datenow);
      $meses      = $tiempo->m;
      $dias       = $tiempo->d;
      $nhoras     = $tiempo->h;
      $dmes       = $meses * $ndias;
      //$hdias=  $dias * 24;
      $diastotales = $dmes + $dias;

      if ($diastotales > 10) {
        $nrojo++;
      } else if ($diastotales > 5 && $diastotales <= 10) {
        $namarillo++;
      } else {
        $nverde++;
      }
    }
    //$cuenta = mysqli_num_rows($res);
    $data["rojo"] = $nrojo;
    $data["amarillo"] = $namarillo;
    $data["verde"] = $nverde;
    echo json_encode($data);
    break;


  case 'crearticketpat':
    $recibe = json_decode($_REQUEST['envio'], true);
    $devuelve = array('resp' => 'error', 'mensaje' => 'Ha ocurrido un error');
    $sql = "SELECT * FROM estado_vehiculos where eve_patente = '{$recibe['patente']}'";
    $res = $link->query($sql);

    if (mysqli_num_rows($res) > 0) {
      $fila = mysqli_fetch_array($res);
      $sql1 = "SELECT t1.* 
            FROM clientes t1 
            Where t1.cli_estadows = 1 and t1.cli_nombrews = '{$fila['eve_cliente']}'
            GROUP by t1.cuenta
            order by t1.cuenta asc";
      $res1 = $link->query($sql1);

      if (mysqli_num_rows($res1) > 0) {
        $fila1 = mysqli_fetch_array($res1);
        $sql2 = "SELECT *
            FROM vehiculos
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(veh_patente), ')', ''), '(', ''), ' ', ''), '|', ''), '-', ''), '_', '') LIKE '%{$recibe['patente']}%' and deleted_at is NULL ORDER BY veh_tcreacion desc limit 1";
        $res2 = $link->query($sql2);
        if (mysqli_num_rows($res2) > 0) {
          $fila2 = mysqli_fetch_array($res2);
          $idveh = $fila2['veh_id'];
          $tiposer = $fila2['veh_tservicio'];
        } else {
          $sql3 = "INSERT INTO vehiculos (veh_cliente,veh_patente,veh_seriegps) VALUES ({$fila1['id']},'{$recibe['patente']}','')";
          $res3 = $link->query($sql3);
          $idveh = $link->insert_id;
          $tiposer = 1;
        }

        $sql4 = "INSERT INTO tickets (tic_fechahorareg,tic_cliente,tic_patente,tic_tipotrabajo,tic_tiposervicio,tic_estado) VALUES ('{$fechachile}','{$fila1['id']}','{$idveh}','1','{$tiposer}','1')";
        $res4 = $link->query($sql4);

        if ($res4) {
          $devuelve = array('resp' => 'success', 'mensaje' => 'Creado correctamente');
        }
      }
    }

    echo json_encode($devuelve);
    break;

  case 'getTabTicketsFinalizadosDatatable':

    // Parámetros de DataTables
    $draw        = $_REQUEST['draw'];
    $start       = $_REQUEST['start'];
    $length      = $_REQUEST['length'];
    $orderColumn = $_REQUEST['order'][0]['column'] ? $_REQUEST['order'][0]['column'] : 1;
    $orderDir    = $_REQUEST['order'][0]['dir'];
    $searchValue = trim($_REQUEST['search']['value']);

    //echo '<pre>';
    //print_r($_REQUEST);
    //echo '</pre>';


    // Mapear columnas de DataTables a columnas de la base de datos
    $columns = array('', 'tic_id', 'tic_fechahorareg', 'tic_fechacierre', 'diferencia_dias'); // Ajusta según tus columnas

    $filtros = 'WHERE true ';

    $filtros .= ' AND tic.tic_id>0 ';
    if (!empty($searchValue)) {

      $filtros .= " AND (
                  tic.tic_id LIKE '%$searchValue%' OR 
                  tic.tic_fechahorareg LIKE '%$searchValue%' OR 
                  tic.tic_cliente LIKE '%$searchValue%' OR 
                  tic.tic_patente LIKE '%$searchValue%' OR 
                  tic.tic_usuario_externo LIKE '%$searchValue%' OR 
                  tic.tic_dispositivo LIKE '%$searchValue%' OR 
                  tic.tic_tipotrabajo LIKE '%$searchValue%' OR 
                  tic.tic_tiposervicio LIKE '%$searchValue%' OR 
                  tic.tic_fechaagenda LIKE '%$searchValue%' OR 
                  tic.tic_horaagenda LIKE '%$searchValue%' OR 
                  tic.tic_tecnico LIKE '%$searchValue%' OR 
                  tic.tic_fhinicio LIKE '%$searchValue%' OR 
                  tic.tic_fhfin LIKE '%$searchValue%' OR 
                  tic.tic_fechacierre LIKE '%$searchValue%' OR 
                  tic.tic_seriesim LIKE '%$searchValue%' OR
                  tic.tic_imeis LIKE '%$searchValue%' OR
                  tic.tic_descripcion LIKE '%$searchValue%' OR
                

                  cli.razonsocial LIKE '%$searchValue%' OR
                  cli.cuenta LIKE '%$searchValue%' OR
                  veh.veh_id LIKE '%$searchValue%' OR
                  veh.veh_patente LIKE '%$searchValue%' OR
                  tdi.tdi_nombre LIKE '%$searchValue%' OR
                  ttra.ttra_nombre LIKE '%$searchValue%' OR
                  per.per_nombrecorto LIKE '%$searchValue%' OR
                  com.comuna_nombre LIKE '%$searchValue%' OR
                  com2.comuna_nombre LIKE '%$searchValue%' 
                  )";
    }

    $extrasql = '';



    if ($_REQUEST['fileestado'] == 6) {
      $filtros .= ' AND tic.tic_estado_app=6'; // Finalizados APP
    } else if ($_REQUEST['fileestado'] == 3) {
      $filtros .= ' AND tic.tic_estado_app=3'; // Cerrado
    }


    if ($_REQUEST['filestado'] == 7) {
      $filtros .= ' AND tic.tic_estado=7'; // Anulados se debe dejar el 7 por que choca con el estado 6 de finalizados por APP
    } else {
      $filtros .= ' AND tic.tic_estado=3'; // Finalizados
    }

    //$filtros = '';
    if ($_REQUEST['filccosto'] != '') {
      $filtros .= " AND tic.tic_centrocosto={$_REQUEST['filccosto']} ";
    }
    if ($_REQUEST['filefact'] != '') {
      $filtros .= " AND tic.tic_estadofact={$_REQUEST['filefact']} ";
    }
    if ($_REQUEST['filpagot'] != '') {
      $filtros .= " AND tic.tic_pagot={$_REQUEST['filpagot']} ";
    }
    if ($_REQUEST['filcliente'] != '') {
      $filtros .= " AND cli.cuenta='{$_REQUEST['filcliente']}'";
    }
    if ($_REQUEST['filtecnico'] != '') {
      $filtros .= " AND tic.tic_tecnico={$_REQUEST['filtecnico']} ";
    }
    if ($_REQUEST['fildesde'] != '' && $_REQUEST['filhasta'] == '') {
      $filtros .= " AND (tic.tic_fechahorareg between '{$_REQUEST['fildesde']} 00:00:00' AND '{$_REQUEST['fildesde']} 23:59:59' OR tic.tic_fechacierre between '{$_REQUEST['fildesde']} 00:00:00' AND '{$_REQUEST['fildesde']} 23:59:59' )";
    } else if ($_REQUEST['fildesde'] != '' && $_REQUEST['filhasta'] != '') {
      $filtros .= " AND (tic.tic_fechahorareg between '{$_REQUEST['fildesde']} 00:00:00' AND '{$_REQUEST['filhasta']} 23:59:59' OR tic.tic_fechacierre between '{$_REQUEST['fildesde']} 00:00:00' AND '{$_REQUEST['filhasta']} 23:59:59') ";
    } else if ($_REQUEST['fildesde'] == '' && $_REQUEST['filhasta'] != '') {
      $filtros .= " AND (tic.tic_fechahorareg between '{$_REQUEST['filhasta']} 00:00:00' AND '{$_REQUEST['filhasta']} 23:59:59' OR tic.tic_fechacierre between '{$_REQUEST['filhasta']} 00:00:00' AND '{$_REQUEST['filhasta']} 23:59:59')";
    }

    $filtros .= " GROUP BY tic_id";

    $sqlBase = "SELECT 
              tic.*,
              cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,
              tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, com.comuna_nombre, 
              avs.sen_id_1, avs.sen_id_2, avs.sen_id_3, com2.comuna_nombre as comunaorigen, mco.mcom_kms,
              ( select razonsocial from clientes where id = tic_rsocial) as razonsocialnew,
              -- Diferencia en días entre dos fechas
              DATEDIFF(tic.tic_fechacierre, tic.tic_fechahorareg) AS diferencia_dias,
              (
              SELECT count(timg_id) 
              FROM tickets_img 
              WHERE timg_idticket = tic_id
              ) AS count_img
          from tickets tic 
          LEFT JOIN clientes cli on tic.tic_cliente = id 
          LEFT JOIN vehiculos veh on tic.tic_patente = veh.veh_id 
          LEFT JOIN tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
          LEFT JOIN tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id
          LEFT JOIN personal per on tic.tic_tecnico = per.per_id 
          LEFT JOIN comunas com on com.comuna_id = tic.tic_comuna_des 
          LEFT JOIN comunas com2 on com2.comuna_id = tic.tic_comuna_ori
          LEFT JOIN asociacion_vehiculos_sensores avs on avs.veh_id = veh.veh_id and avs.avx_tecnico = tic.tic_tecnico 
          LEFT JOIN matriz_comunas mco on mco.mcom_idorigen = tic.tic_comuna_ori and mco.mcom_iddestino = tic.tic_comuna_des
          " . $filtros . " ";
    // Obtener el total de registros sin filtrar
    $totalRecordsQuery = $link->query($sqlBase);
    $totalRecords = $totalRecordsQuery->num_rows;


    if (false) {
      if ($_REQUEST['orderby'] == 'todos') {
        $sql = "select 
            tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, com.comuna_nombre, avs.sen_id_1, avs.sen_id_2, avs.sen_id_3, com2.comuna_nombre as comunaorigen, mco.mcom_kms
            from tickets tic 
            LEFT JOIN clientes cli on tic.tic_cliente = id 
            LEFT JOIN vehiculos veh on tic.tic_patente = veh.veh_id 
            LEFT JOIN tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
            LEFT JOIN tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id
            LEFT JOIN personal per on tic.tic_tecnico = per.per_id 
            LEFT JOIN comunas com on com.comuna_id = tic.tic_comuna_des 
            LEFT JOIN comunas com2 on com2.comuna_id = tic.tic_comuna_ori
            LEFT JOIN asociacion_vehiculos_sensores avs on avs.veh_id = veh.veh_id and avs.avx_tecnico = tic.tic_tecnico 
            LEFT JOIN matriz_comunas mco on mco.mcom_idorigen = tic.tic_comuna_ori and mco.mcom_iddestino = tic.tic_comuna_des 
            where tic.tic_id>0 " . $extrasql . " group by tic_id order by tic_id desc limit 100 ";
      } else {
        $filtros = '';
        if ($_REQUEST['filccosto'] != '') {
          $filtros .= " and tic.tic_centrocosto={$_REQUEST['filccosto']} ";
        }
        if ($_REQUEST['filefact'] != '') {
          $filtros .= " and tic.tic_estadofact={$_REQUEST['filefact']} ";
        }
        if ($_REQUEST['filpagot'] != '') {
          $filtros .= " and tic.tic_pagot={$_REQUEST['filpagot']} ";
        }
        if ($_REQUEST['filcliente'] != '') {
          $filtros .= " and cli.cuenta='{$_REQUEST['filcliente']}'";
        }
        if ($_REQUEST['filtecnico'] != '') {
          $filtros .= " and tic.tic_tecnico={$_REQUEST['filtecnico']} ";
        }
        if ($_REQUEST['fildesde'] != '' && $_REQUEST['filhasta'] == '') {
          $filtros .= " and tic.tic_fechahorareg between '{$_REQUEST['fildesde']} 00:00:00' and '{$_REQUEST['fildesde']} 23:59:59'";
        } else if ($_REQUEST['fildesde'] != '' && $_REQUEST['filhasta'] != '') {
          $filtros .= " and tic.tic_fechahorareg between '{$_REQUEST['fildesde']} 00:00:00' and '{$_REQUEST['filhasta']} 23:59:59'";
        } else if ($_REQUEST['fildesde'] == '' && $_REQUEST['filhasta'] != '') {
          $filtros .= " and tic.tic_fechahorareg between '{$_REQUEST['filhasta']} 00:00:00' and '{$_REQUEST['filhasta']} 23:59:59'";
        }
        // if($_REQUEST['fildesde']!=''){
        //   $filtros .= " and tic.tic_fechahorareg='{$_REQUEST['fildesde']} 00:00:00' ";
        // }
        // if($_REQUEST['filhasta']!=''){
        //   $filtros .= " and tic.tic_fechahorareg='{$_REQUEST['filhasta']} 23:59:59' ";
        // }
        $sql = "SELECT 
                tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,
                tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, com.comuna_nombre, 
                avs.sen_id_1, avs.sen_id_2, avs.sen_id_3, com2.comuna_nombre as comunaorigen, mco.mcom_kms,
                ( select razonsocial from clientes where id = tic_rsocial) as razonsocialnew,
                -- Diferencia en días entre dos fechas
              DATEDIFF(tic.tic_fechacierre, tic.tic_fechahorareg) AS diferencia_dias
            from tickets tic 
            LEFT JOIN clientes cli on tic.tic_cliente = id 
            LEFT JOIN vehiculos veh on tic.tic_patente = veh.veh_id 
            LEFT JOIN tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
            LEFT JOIN tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id
            LEFT JOIN personal per on tic.tic_tecnico = per.per_id 
            LEFT JOIN comunas com on com.comuna_id = tic.tic_comuna_des 
            LEFT JOIN comunas com2 on com2.comuna_id = tic.tic_comuna_ori
            LEFT JOIN asociacion_vehiculos_sensores avs on avs.veh_id = veh.veh_id and avs.avx_tecnico = tic.tic_tecnico 
            LEFT JOIN matriz_comunas mco on mco.mcom_idorigen = tic.tic_comuna_ori and mco.mcom_iddestino = tic.tic_comuna_des
            where tic.tic_id>0 " . $extrasql . " {$filtros} group by tic_id order by tic_id desc limit 100";

        // echo     $sql."</br>";  

      }
    }

    // Agregar orden y límites para paginación
    $sql = $sqlBase . " ORDER BY " . $columns[$orderColumn] . " $orderDir LIMIT $start, $length";

    // echo $sql;
    // Ejecutar la consulta con límites
    $res = $link->query($sql);

    // echo $sql;
    //$res        = $link->query($sql);
    $tickets    = array();
    // echo $sql.'<br>';
    while ($fila = mysqli_fetch_array($res)) {
      $agenda  = "--()";
      $tecnico = "";
      // if($fila["tic_estado"]==2 || $fila["tic_estado"]==3){
      // if($fila["tic_tecnico"]==0){
      // $tecnico="NO ASIGNADO";	
      // }else{
      // $tecnico=$fila["per_nombrecorto"];
      // }
      // $agenda="<b>".devfecha($fila["tic_fechaagenda"])." ".hhmm($fila["tic_horaagenda"])."</b> <br>(".$tecnico.")";	
      // }
      if ($fila["tic_horaagenda"] == '' || $fila["tic_horaagenda"] == null) {
        $fila["tic_horaagenda"] = '00:00:00';
      }
      $agenda      = "<b>" . devfecha($fila["tic_fechaagenda"]) . " " . hhmm($fila["tic_horaagenda"]);
      //$productos = getProxVeh($fila["veh_id"]);
      $dateinicio  = new DateTime($fila["tic_fechahorareg"]);
      $ndias       = getDiasMes($fila["tic_fechahorareg"]);
      $datenow     = new DateTime(date("Y-m-d H:i:s"));
      $tiempo      = $dateinicio->diff($datenow);
      $meses       = $tiempo->m;
      $dias        = $tiempo->d;
      $nhoras      = $tiempo->h;
      $dmes        = $meses * $ndias;
      //$hdias=  $dias * 24;
      $diastotales = $dmes + $dias;

      $firstDate  = new DateTime($fila['tic_fechahorareg']);
      $secondDate = new DateTime($fechachile);
      $intvl = $firstDate->diff($secondDate);

      $finalizosDate = new DateTime($fila['tic_fechacierre']);
      $intvlDiff = $firstDate->diff($finalizosDate);



      $nombredispositivo = '-';
      $series = '';
      $sql1 = "SELECT t1.*, t2.pro_nombre 
              FROM productosxvehiculos t1 
              LEFT OUTER JOIN productos t2 on t2.pro_id = t1.pxv_idpro
              where t1.pxv_idveh = {$fila['tic_patente']} order by 1 desc limit 1";
      $res1 = $link->query($sql1);
      $fila1 = mysqli_fetch_array($res1);
      if (mysqli_num_rows($res1) > 0) {
        /*foreach($res1 as $key1=>$data1){
              $series .= $data1['pxv_nserie'].',';
          }
          $series = substr($series, 0, -1);*/
        $series = $fila1['pxv_nserie'];
        $nombredispositivo = $fila1['pro_nombre'];
      }

      if ($fila["tic_tipotrabajo"] == 3) {
        $series = $fila['tic_imeis'];
      }

      $img1 = '';
      $img2 = '';
      $img3 = '';
      $img4 = '';
      $img5 = '';
      $img6 = '';
      $img7 = '';
      $img8 = '';

      $sql1 = "SELECT timg_tipo, timg_subtipo, timg_name FROM tickets_img WHERE timg_idticket='{$fila["tic_id"]}'";
      $res1 = $link->query($sql1);
      if ($res1) {
        while ($fila1 = mysqli_fetch_array($res1)) {
          if ($fila1["timg_tipo"] == 0 && $fila1["timg_subtipo"] == 0) {
            $img1 = $fila1["timg_name"];
          }
          if ($fila1["timg_tipo"] == 0 && $fila1["timg_subtipo"] == 1) {
            $img2 = $fila1["timg_name"];
          }
          if ($fila1["timg_tipo"] == 0 && $fila1["timg_subtipo"] == 2) {
            $img3 = $fila1["timg_name"];
          }
          if ($fila1["timg_tipo"] == 0 && $fila1["timg_subtipo"] == 3) {
            $img4 = $fila1["timg_name"];
          }
          if ($fila1["timg_tipo"] == 1 && $fila1["timg_subtipo"] == 0) {
            $img5 = $fila1["timg_name"];
          }
          if ($fila1["timg_tipo"] == 1 && $fila1["timg_subtipo"] == 1) {
            $img6 = $fila1["timg_name"];
          }
          if ($fila1["timg_tipo"] == 1 && $fila1["timg_subtipo"] == 2) {
            $img7 = $fila1["timg_name"];
          }
          if ($fila1["timg_tipo"] == 1 && $fila1["timg_subtipo"] == 3) {
            $img8 = $fila1["timg_name"];
          }
        }
      }

      $accesorios = array();
      $sql1 = "SELECT ava_id, ava_idveh, ava_idguia, ava_serie, ava_estado FROM asociacion_vehiculos_accesorios WHERE ava_estado=1 AND ava_idveh='{$fila["tic_patente"]}'";
      $res1 = $link->query($sql1);
      while ($fila1 = mysqli_fetch_array($res1)) {
        $npro = "";
        $sql2 = "SELECT pro.pro_nombre FROM serie_guia ser INNER JOIN productos pro ON pro.pro_id=ser.pro_id WHERE ser_id='{$fila1['ava_idguia']}'";
        $res2 = $link->query($sql2);
        while ($fila2 = mysqli_fetch_array($res2)) {
          $npro = $fila2['pro_nombre'];
        }
        $accesorios[] = array(
          'ser_id' => $fila1['ava_idguia'],
          'ser_codigo' => $fila1['ava_serie'],
          'pro_nombre' => $npro,
        );
      }

      $tickets[] = array(
        "tic_valorkm" => $fila["tic_valorkm"],
        "tic_totalkm" => $fila["tic_totalkm"],
        "tic_costolabor" => $fila["tic_costolabor"],
        "tic_sseguridad" => ($fila["tic_sseguridad"] == null || $fila["tic_sseguridad"] == '' ? '-' : $fila["tic_sseguridad"]),
        "tic_id" => $fila["tic_id"],
        "mcom_kms" => $fila["mcom_kms"],
        "comunades" => $fila["comuna_nombre"],
        "comunaorigen" => $fila["comunaorigen"],
        "tic_kmsdist" => $fila["tic_kmsdist"],
        "comentario" => $fila["tic_desccierre"],
        "tic_comuna_ori" => $fila["tic_comuna_ori"],
        "tic_comuna_des" => $fila["tic_comuna_des"],
        "tic_um" => $fila["tic_um"],
        "series" => $series,
        "id" => $fila["tic_id"],
        "fechahorareg" => devfechahora($fila["tic_fechahorareg"]),
        "diastranscurridos" => $intvl->days,
        "idcliente" => $fila["tic_cliente"],
        "rs"   => ($fila["tic_rsocial"] == null || $fila["tic_rsocial"] == 0 ? $fila["razonsocial"] : $fila["razonsocialnew"]), //$fila["razonsocial"],
        "cliente" => $fila["cuenta"],
        "idpatente" => $fila["tic_patente"],
        "patente" => $fila["veh_patente"],
        "iddispositivo" => $fila["tic_dispositivo"],
        "dispositivo" => $nombredispositivo,
        "idtipotrabajo" => $fila["tic_tipotrabajo"],
        "tipotrabajo" => $fila["ttra_nombre"],
        "contacto" => $fila["tic_contacto"],
        "celular" => $fila["tic_celular"],
        "lugar" => $fila["tic_lugar"],
        "descripcion" => $fila["tic_descripcion"],
        "fechaagenda" => devfecha($fila["tic_fechacierre"]),
        "diferencia_dias" => $fila["diferencia_dias"], //( $intvlDiff->days ), //
        "tipo_servicio" => ($fila["tic_tiposervicio"] == 2 ? 'Avanzado' : ($fila["tic_tiposervicio"] == 3 ? 'Thermo' : 'Básico')), //( $fila["tic_tiposervicio"] == 1 ? 'Avanzado':'Básico' ),
        "tecnico" => $tecnico,
        "idtecnico" => $fila["tic_tecnico"],
        "agenda" => $agenda,
        "hora" => hhmm($fila["tic_horaagenda"]),
        "idestado" => $fila["tic_estado"],
        "idestadoapp" => $fila["tic_estado_app"],
        'tecnico' => $fila['per_nombrecorto'],
        'vtrabajo' => $fila['tic_valortrabajo'],
        'ccosto' => $fila['tic_centrocosto'],
        'estadofact' => $fila['tic_estadofact'],
        'pagot' => $fila['tic_pagot'],
        'img1' => 'archivos/tickets/' . $img1,
        'img2' => 'archivos/tickets/' . $img2,
        'img3' => 'archivos/tickets/' . $img3,
        'img4' => 'archivos/tickets/' . $img4,
        'img5' => 'archivos/tickets/' . $img5,
        'img6' => 'archivos/tickets/' . $img6,
        'img7' => 'archivos/tickets/' . $img7,
        'img8' => 'archivos/tickets/' . $img8,
        'ch_1' => ($fila['sen_id_1'] == 0 ? 'No' : 'Si'),
        'ch_2' => ($fila['sen_id_2'] == 0 ? 'No' : 'Si'),
        'ch_3' => ($fila['sen_id_3'] == 0 ? 'No' : 'Si'),
        'count_img' => $fila['count_img'],
        'accesorios' => $accesorios,
        //,'sql'=>$sql
      );
    }

    // Formatear los datos para DataTables
    $response = array(
      "draw" => intval($draw),
      "recordsTotal" => $totalRecords,
      "recordsFiltered" => $totalRecords,
      "data" => $tickets
    );

    echo json_encode($response);

    //echo json_encode($tickets);
    break;

  case 'getTabTicketsFinalizados':

    $extrasql = '';

    if ($_REQUEST['filestado'] == 7) {
      $extrasql = ' and tic.tic_estado=7';
    } else {
      $extrasql = ' and tic.tic_estado=3';
    }

    if ($_REQUEST['orderby'] == 'todos') {
      $sql = "SELECT 
              tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,
              ttra.ttra_nombre, per.per_nombrecorto, com.comuna_nombre, avs.sen_id_1, 
              avs.sen_id_2, avs.sen_id_3, com2.comuna_nombre AS comunaorigen, mco.mcom_kms,
              (
              SELECT count(timg_id) 
              FROM tickets_img 
              WHERE timg_idticket = tic_id
              ) AS count_img
            from tickets tic 
            LEFT JOIN clientes cli ON tic.tic_cliente = id 
            LEFT JOIN vehiculos veh ON tic.tic_patente = veh.veh_id 
            LEFT JOIN tiposdedispositivos tdi ON tic.tic_dispositivo = tdi.tdi_id 
            LEFT JOIN tiposdetrabajos ttra ON tic.tic_tipotrabajo = ttra.ttra_id
            LEFT JOIN personal per ON tic.tic_tecnico = per.per_id 
            LEFT JOIN comunas com ON com.comuna_id = tic.tic_comuna_des 
            LEFT JOIN comunas com2 ON com2.comuna_id = tic.tic_comuna_ori
            LEFT JOIN asociacion_vehiculos_sensores avs ON avs.veh_id = veh.veh_id AND avs.avx_tecnico = tic.tic_tecnico 
            LEFT JOIN matriz_comunas mco ON mco.mcom_idorigen = tic.tic_comuna_ori AND mco.mcom_iddestino = tic.tic_comuna_des 
            WHERE tic.tic_id>0 " . $extrasql . " group by tic_id order by tic_id desc limit 100 ";
    } else {
      $filtros = '';
      if ($_REQUEST['filccosto'] != '') {
        $filtros .= " and tic.tic_centrocosto={$_REQUEST['filccosto']} ";
      }
      if ($_REQUEST['filefact'] != '') {
        $filtros .= " and tic.tic_estadofact={$_REQUEST['filefact']} ";
      }
      if ($_REQUEST['filpagot'] != '') {
        $filtros .= " and tic.tic_pagot={$_REQUEST['filpagot']} ";
      }
      if ($_REQUEST['filcliente'] != '') {
        $filtros .= " and cli.cuenta='{$_REQUEST['filcliente']}'";
      }
      if ($_REQUEST['filtecnico'] != '') {
        $filtros .= " and tic.tic_tecnico={$_REQUEST['filtecnico']} ";
      }
      if ($_REQUEST['fildesde'] != '' && $_REQUEST['filhasta'] == '') {
        $filtros .= " and tic.tic_fechahorareg between '{$_REQUEST['fildesde']} 00:00:00' and '{$_REQUEST['fildesde']} 23:59:59'";
      } else if ($_REQUEST['fildesde'] != '' && $_REQUEST['filhasta'] != '') {
        $filtros .= " and tic.tic_fechahorareg between '{$_REQUEST['fildesde']} 00:00:00' and '{$_REQUEST['filhasta']} 23:59:59'";
      } else if ($_REQUEST['fildesde'] == '' && $_REQUEST['filhasta'] != '') {
        $filtros .= " and tic.tic_fechahorareg between '{$_REQUEST['filhasta']} 00:00:00' and '{$_REQUEST['filhasta']} 23:59:59'";
      }
      // if($_REQUEST['fildesde']!=''){
      //   $filtros .= " and tic.tic_fechahorareg='{$_REQUEST['fildesde']} 00:00:00' ";
      // }
      // if($_REQUEST['filhasta']!=''){
      //   $filtros .= " and tic.tic_fechahorareg='{$_REQUEST['filhasta']} 23:59:59' ";
      // }
      $sql = "SELECT 
                tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,
                tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, com.comuna_nombre, 
                avs.sen_id_1, avs.sen_id_2, avs.sen_id_3, com2.comuna_nombre AS comunaorigen, mco.mcom_kms,
                (
                SELECT count(timg_id) 
                FROM tickets_img 
                WHERE timg_idticket = tic_id
                ) AS count_img 
            FROM tickets tic 
            LEFT JOIN clientes cli ON tic.tic_cliente = id 
            LEFT JOIN vehiculos veh ON tic.tic_patente = veh.veh_id 
            LEFT JOIN tiposdedispositivos tdi ON tic.tic_dispositivo = tdi.tdi_id 
            LEFT JOIN tiposdetrabajos ttra ON tic.tic_tipotrabajo = ttra.ttra_id
            LEFT JOIN personal per ON tic.tic_tecnico = per.per_id 
            LEFT JOIN comunas com ON com.comuna_id = tic.tic_comuna_des 
            LEFT JOIN comunas com2 ON com2.comuna_id = tic.tic_comuna_ori
            LEFT JOIN asociacion_vehiculos_sensores avs ON avs.veh_id = veh.veh_id AND avs.avx_tecnico = tic.tic_tecnico 
            LEFT JOIN matriz_comunas mco ON mco.mcom_idorigen = tic.tic_comuna_ori AND mco.mcom_iddestino = tic.tic_comuna_des
            WHERE tic.tic_id>0 " . $extrasql . " {$filtros} group by tic_id order by tic_id desc limit 100";
    }

    $res        = $link->query($sql);
    $tickets    = array();
    // echo $sql.'<br>';
    while ($fila = mysqli_fetch_array($res)) {
      $agenda  = "--()";
      $tecnico = "";
      // if($fila["tic_estado"]==2 || $fila["tic_estado"]==3){
      // if($fila["tic_tecnico"]==0){
      // $tecnico="NO ASIGNADO";	
      // }else{
      // $tecnico=$fila["per_nombrecorto"];
      // }
      // $agenda="<b>".devfecha($fila["tic_fechaagenda"])." ".hhmm($fila["tic_horaagenda"])."</b> <br>(".$tecnico.")";	
      // }
      if ($fila["tic_horaagenda"] == '' || $fila["tic_horaagenda"] == null) {
        $fila["tic_horaagenda"] = '00:00:00';
      }
      $agenda      = "<b>" . devfecha($fila["tic_fechaagenda"]) . " " . hhmm($fila["tic_horaagenda"]);
      //$productos = getProxVeh($fila["veh_id"]);
      $dateinicio  = new DateTime($fila["tic_fechahorareg"]);
      $ndias       = getDiasMes($fila["tic_fechahorareg"]);
      $datenow     = new DateTime(date("Y-m-d H:i:s"));
      $tiempo      = $dateinicio->diff($datenow);
      $meses       = $tiempo->m;
      $dias        = $tiempo->d;
      $nhoras      = $tiempo->h;
      $dmes        = $meses * $ndias;
      //$hdias=  $dias * 24;
      $diastotales = $dmes + $dias;

      $firstDate  = new DateTime($fila['tic_fechahorareg']);
      $secondDate = new DateTime($fechachile);
      $intvl = $firstDate->diff($secondDate);

      $finalizosDate = new DateTime($fila['tic_fechacierre']);
      $intvlDiff = $firstDate->diff($finalizosDate);

      $nombredispositivo = '-';
      $series = '';
      $sql1 = "SELECT t1.*, t2.pro_nombre 
                FROM productosxvehiculos t1 
                LEFT OUTER JOIN productos t2 on t2.pro_id = t1.pxv_idpro
                where t1.pxv_idveh = {$fila['tic_patente']} order by 1 desc limit 1";
      $res1 = $link->query($sql1);
      $fila1 = mysqli_fetch_array($res1);
      if (mysqli_num_rows($res1) > 0) {
        /*foreach($res1 as $key1=>$data1){
                $series .= $data1['pxv_nserie'].',';
            }
            $series = substr($series, 0, -1);*/
        $series = $fila1['pxv_nserie'];
        $nombredispositivo = $fila1['pro_nombre'];
      }

      if ($fila["tic_tipotrabajo"] == 3) {
        $series = $fila['tic_imeis'];
      }



      $tickets[] = array(
        "tic_valorkm" => $fila["tic_valorkm"],
        "tic_totalkm" => $fila["tic_totalkm"],
        "tic_costolabor" => $fila["tic_costolabor"],
        "tic_sseguridad" => ($fila["tic_sseguridad"] == null || $fila["tic_sseguridad"] == '' ? '-' : $fila["tic_sseguridad"]),
        "tic_id" => $fila["tic_id"],
        "mcom_kms" => $fila["mcom_kms"],
        "comunades" => $fila["comuna_nombre"],
        "comunaorigen" => $fila["comunaorigen"],
        "tic_kmsdist" => $fila["tic_kmsdist"],
        "comentario" => $fila["tic_desccierre"],
        "tic_comuna_ori" => $fila["tic_comuna_ori"],
        "tic_comuna_des" => $fila["tic_comuna_des"],
        "tic_um" => $fila["tic_um"],
        "series" => $series,
        "id" => $fila["tic_id"],
        "fechahorareg" => devfechahora($fila["tic_fechahorareg"]),
        "diastranscurridos" => $intvl->days,
        "idcliente" => $fila["tic_cliente"],
        "rs" => $fila["razonsocial"],
        "cliente" => $fila["cuenta"],
        "idpatente" => $fila["tic_patente"],
        "patente" => $fila["veh_patente"],
        "iddispositivo" => $fila["tic_dispositivo"],
        "dispositivo" => $nombredispositivo,
        "idtipotrabajo" => $fila["tic_tipotrabajo"],
        "tipotrabajo" => $fila["ttra_nombre"],
        "contacto" => $fila["tic_contacto"],
        "celular" => $fila["tic_celular"],
        "lugar" => $fila["tic_lugar"],
        "descripcion" => $fila["tic_descripcion"],
        "fechaagenda" => devfecha($fila["tic_fechacierre"]),
        "diferencia_dias" => ($intvlDiff->days),
        "tipo_servicio" => ($fila["tic_tiposervicio"] == 1 ? 'Avanzado' : 'Básico'),
        "tecnico" => $tecnico,
        "idtecnico" => $fila["tic_tecnico"],
        "agenda" => $agenda,
        "hora" => hhmm($fila["tic_horaagenda"]),
        "idestado" => $fila["tic_estado"],
        'tecnico' => $fila['per_nombrecorto'],
        'vtrabajo' => $fila['tic_valortrabajo'],
        'ccosto' => $fila['tic_centrocosto'],
        'estadofact' => $fila['tic_estadofact'],
        'pagot' => $fila['tic_pagot'],
        'img1' => $fila['tic_img1'],
        'img2' => $fila['tic_img2'],
        'img3' => $fila['tic_img3'],
        'img4' => $fila['tic_img4'],
        'img5' => $fila['tic_img5'],
        'ch_1' => ($fila['sen_id_1'] == 0 ? 'No' : 'Si'),
        'ch_2' => ($fila['sen_id_2'] == 0 ? 'No' : 'Si'),
        'ch_3' => ($fila['sen_id_3'] == 0 ? 'No' : 'Si'),

        'count_img' => $fila['count_img']

        //,'sql'=>$sql
      );
    }

    echo json_encode($tickets);
    break;

  case 'cargadatos':
    $data = array();
    $labels = array();
    $valores = array();

    $recibe = json_decode($_REQUEST['envio'], true);

    if ($recibe['opc'] == 1) {
      $meses = array(['numero' => 1, 'nombre' => 'Enero'], ['numero' => 2, 'nombre' => 'Febrero'], ['numero' => 3, 'nombre' => 'Marzo'], ['numero' => 4, 'nombre' => 'Abril'], ['numero' => 5, 'nombre' => 'Mayo'], ['numero' => 6, 'nombre' => 'junio'], ['numero' => 7, 'nombre' => 'Julio'], ['numero' => 8, 'nombre' => 'Agosto'], ['numero' => 9, 'nombre' => 'Septiembre'], ['numero' => 10, 'nombre' => 'Octubre'], ['numero' => 11, 'nombre' => 'Noviembre'], ['numero' => 12, 'nombre' => 'Diciembre']);

      $ano_actual = date("Y");

      foreach ($meses as $keym => $mes) {
        $sql = "SELECT
                        SUM(CASE WHEN tic_tipotrabajo = 1 THEN 1 ELSE 0 END) AS soporte,
                        SUM(CASE WHEN tic_tipotrabajo = 2 THEN 1 ELSE 0 END) AS instalacion,
                        SUM(CASE WHEN tic_tipotrabajo = 3 THEN 1 ELSE 0 END) AS desinstalacion,
                        SUM(CASE WHEN tic_tipotrabajo = 6 THEN 1 ELSE 0 END) AS demo
                    FROM tickets
                    WHERE
                        (MONTH(tic_fechahorareg) = {$mes['numero']} AND YEAR(tic_fechahorareg) = {$ano_actual})";
        $res = $link->query($sql);
        $fila = mysqli_fetch_array($res);
        $ins = 0;
        $des = 0;
        $sop = 0;
        $dem = 0;
        if (mysqli_num_rows($res) > 0) {
          $ins = ($fila['instalacion'] == null || $fila['instalacion'] == '' ? 0 : $fila['instalacion']);
          $des = ($fila['desinstalacion'] == null || $fila['desinstalacion'] == '' ? 0 : $fila['desinstalacion']);
          $sop = ($fila['soporte'] == null || $fila['soporte'] == '' ? 0 : $fila['soporte']);
          $dem = ($fila['demo'] == null || $fila['demo'] == '' ? 0 : $fila['demo']);
        }
        $labels[] = array(
          'mes' => $mes['nombre'],
          'col1' => $sop,
          'col2' => $ins,
          'col3' => $des,
          'col4' => $dem,
        );
      }
    } else if ($recibe['opc'] == 2) {
      $meses = array(['numero' => 1, 'nombre' => 'Enero'], ['numero' => 2, 'nombre' => 'Febrero'], ['numero' => 3, 'nombre' => 'Marzo'], ['numero' => 4, 'nombre' => 'Abril'], ['numero' => 5, 'nombre' => 'Mayo'], ['numero' => 6, 'nombre' => 'junio'], ['numero' => 7, 'nombre' => 'Julio'], ['numero' => 8, 'nombre' => 'Agosto'], ['numero' => 9, 'nombre' => 'Septiembre'], ['numero' => 10, 'nombre' => 'Octubre'], ['numero' => 11, 'nombre' => 'Noviembre'], ['numero' => 12, 'nombre' => 'Diciembre']);

      $ano_actual = date("Y");

      $sql = "SELECT * FROM personal where per_estado = 1 and per_id!=26";
      $res = $link->query($sql);

      foreach ($meses as $keym => $mes) {
        $datapersonal = array();
        foreach ($res as $keyper => $personal) {
          $sql1 = "SELECT *
                        FROM tickets
                        WHERE (MONTH(tic_fechahorareg) = {$mes['numero']} AND YEAR(tic_fechahorareg) = {$ano_actual}) and tic_tecnico = {$personal['per_id']}";
          $res1 = $link->query($sql1);
          /* echo $sql1.'<br>';
                    echo $mes['nombre'].'<br>';*/
          $datapersonal[] = array(
            'tecnico' => $personal['per_nombres'],
            'trabajos' => mysqli_num_rows($res1),
          );
        }

        $pasa =  false;
        $conta = 0;
        foreach ($datapersonal as $key => $dat) {
          $conta = $conta + (int)$dat['trabajos'];
        }

        if ($conta > 0) {
          $pasa = true;
        }

        if ($pasa) {


          $labels[] = array(
            'mes' => $mes['nombre'],
            'lineas' => $datapersonal,
          );
        }
      }
    } else if ($recibe['opc'] == 3) {
      $meses = array(['numero' => 1, 'nombre' => 'Enero'], ['numero' => 2, 'nombre' => 'Febrero'], ['numero' => 3, 'nombre' => 'Marzo'], ['numero' => 4, 'nombre' => 'Abril'], ['numero' => 5, 'nombre' => 'Mayo'], ['numero' => 6, 'nombre' => 'junio'], ['numero' => 7, 'nombre' => 'Julio'], ['numero' => 8, 'nombre' => 'Agosto'], ['numero' => 9, 'nombre' => 'Septiembre'], ['numero' => 10, 'nombre' => 'Octubre'], ['numero' => 11, 'nombre' => 'Noviembre'], ['numero' => 12, 'nombre' => 'Diciembre']);

      $ano_actual = date("Y");

      foreach ($meses as $keym => $mes) {
        $sql = "SELECT
                        SUM(CASE WHEN tic_centrocosto = 2 THEN tic_costolabor ELSE 0 END) AS interno,
                        SUM(CASE WHEN tic_centrocosto = 1 THEN tic_costolabor ELSE 0 END) AS cliente
                    FROM tickets
                    WHERE
                        (MONTH(tic_fechahorareg) = {$mes['numero']} AND YEAR(tic_fechahorareg) = {$ano_actual}) and tic_estado = 3";
        $res = $link->query($sql);
        $fila = mysqli_fetch_array($res);
        $interno = 0;
        $cliente = 0;

        if (mysqli_num_rows($res) > 0) {
          $interno = ($fila['interno'] == null || $fila['interno'] == '' ? 0 : $fila['interno']);
          $cliente = ($fila['cliente'] == null || $fila['cliente'] == '' ? 0 : $fila['cliente']);
        }
        $labels[] = array(
          'mes' => $mes['nombre'],
          'inerno' => $interno,
          'cliente' => $cliente,
        );
      }
    }

    $data['labels'] = $labels;
    echo json_encode($data);
    break;


  case 'generaexcel':
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $recibe      = json_decode($_REQUEST['envio'], true);
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("D-Solutions");
    $objPHPExcel->getProperties()->setLastModifiedBy("D-Solutions");
    $objPHPExcel->getProperties()->setTitle("Resumen de tickets");
    $objPHPExcel->getProperties()->setSubject("Resumen de tickets");
    $objPHPExcel->getProperties()->setDescription("Resumen de tickets");
    $objPHPExcel->setActiveSheetIndex(0);

    $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
    /*$objPHPExcel->getActiveSheet()->mergeCells("A1:N1");*/
    $style = array(
      'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
      )
    );

    $letcabeceras   = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L');
    $datoscabeceras = array('Fecha Registro', 'Días', 'Cliente', 'Patente', 'Dispositivo', 'Tipo Trabajo', 'Contacto', 'Celular', 'Lugar', 'Descripción', 'Agenda', 'Estado');

    $objPHPExcel->getActiveSheet()->getStyle("A1:L1")->applyFromArray($style);
    $objPHPExcel->getActiveSheet()->getStyle("A1:L1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $ind = 0;
    foreach ($letcabeceras as $let) {
      $objPHPExcel->getActiveSheet()->SetCellValue($let . '1', $datoscabeceras[$ind]);
      cellColor($let . '1', 'EAEAEA');
      $objPHPExcel->getActiveSheet()->getColumnDimension($let)->setWidth(20);
      $ind++;
    }

    $sql = "SELECT tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto
        from tickets tic 
        left outer join clientes cli on tic.tic_cliente = id 
        left outer join vehiculos veh on tic.tic_patente = veh.veh_id 
        left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
        left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id 
        left outer join personal per on tic.tic_tecnico = per.per_id 
        where tic.tic_estado !=3 and tic.tic_estado !=4 and tic.tic_estado !=7
        order by 2 desc";
    // and tic.tic_fechahorareg BETWEEN '{$recibe['desde']}' and '{$recibe['hasta']}'
    $res   = $link->query($sql);
    $index = 2;

    foreach ($res as $key) {
      $firstDate  = new DateTime($key['tic_fechahorareg']);
      $secondDate = new DateTime($fechachile);
      $intvl = $firstDate->diff($secondDate);

      if ($key['tic_estado'] == 1) {
        $estado = 'Pendiente'; //rojo
        cellColor('L' . ($index), 'F34E4E');
      } else if ($key['tic_estado'] == 2) {
        $estado = 'Agendado'; //amarillo
        cellColor('L' . ($index), 'E6EC63');
      } else {
        $estado = 'Cerrado';
      }

      $agenda = "--()";
      if ($key["tic_estado"] == 2 || $key["tic_estado"] == 3 || $key["tic_estado"] == 5) {
        if ($key["tic_tecnico"] == 0) {
          $tecnico = "NO ASIGNADO";
        } else {
          $tecnico = $key["per_nombrecorto"];
        }
        $agenda = $key["tic_fechaagenda"];
        if ($key["tic_fechaagenda"] != '') {
          $agenda = devfecha($key["tic_fechaagenda"]);
        }
        /*echo $agenda.'<br>';*/
        $agenda = $agenda . " " . hhmm($key["tic_horaagenda"]) . "(" . $tecnico . ")";
      }

      $objPHPExcel->getActiveSheet()->SetCellValue('A' . ($index), $key['tic_fechahorareg']);
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . ($index), $intvl->days);
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . ($index), strtolower($key['cuenta']));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . ($index), $key['veh_patente']);
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . ($index), $key['tdi_nombre']);
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . ($index), $key['ttra_nombre']);
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . ($index), $key['tic_contacto']);
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . ($index), $key['tic_celular']);
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . ($index), $key['tic_lugar']);
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . ($index), $key['tic_descripcion']);
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . ($index), $agenda);
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . ($index), $estado);
      $index++;
    }

    $objPHPExcel->getActiveSheet()->setTitle('Resumen de tickets');
    $objPHPExcel->setActiveSheetIndex(0);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_start();
    $objWriter->save("php://output");
    $xlsData = ob_get_contents();
    ob_end_clean();
    $response =  array(
      'op' => 'ok',
      'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
    );
    echo json_encode($response);
    break;

  case 'generaexcelfin':


    $_SESSION['colorprin'] = '#7058c3';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('max_execution_time', '360');
    ini_set('memory_limit', '128M');
    setlocale(LC_MONETARY, 'en_US');

    $fecha = date('d-m-Y H:i:s');
    $via = json_decode($_REQUEST['datos'], true);

    $letras = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK");

    try {
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("D-Solutions");
      $objPHPExcel->getProperties()->setLastModifiedBy("D-Solutions");
      $objPHPExcel->getProperties()->setTitle("Trabajos Finalizados");
      $objPHPExcel->getProperties()->setSubject("Trabajos Finalizados");
      $objPHPExcel->getProperties()->setDescription("Trabajos Finalizados");
      $objPHPExcel->setActiveSheetIndex(0);

      $headers = $via[0];
      $letrafinal = '';
      $indice = 0;
      $style = array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '7058c3'),
        ),
        'font' => array(
          'color' => array('rgb' => 'FFFFFF'),
        ),
      );

      for ($i = 0; $i < count($headers); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue($letras[$indice] . '1', $headers[$indice]);
        $letrafinal = $letras[$indice];
        $cell = $objPHPExcel->getActiveSheet()->getCell($letras[$indice] . '1');
        $objPHPExcel->getActiveSheet()->getStyle($cell->getCoordinate())->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getColumnDimension($letras[$indice])->setWidth(15);
        $indice++;
      }

      $objPHPExcel->getActiveSheet()->getStyle('A1:' . $letrafinal . '1')->getFont()->setBold(true);
      $style = array(
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
      );

      $objPHPExcel->getActiveSheet()->getStyle("A1:" . $letrafinal . "1")->applyFromArray($style);

      $indice = 0;
      for ($i = 1; $i <= count($via); $i++) {
        $indice2 = 0;
        for ($o = 0; $o < count($headers); $o++) {

          $objPHPExcel->getActiveSheet()->SetCellValue($letras[$indice2] . $i, $via[$indice][$o]);
          $indice2++;
        }
        $indice++;
      }

      $objPHPExcel->getActiveSheet()->setTitle('Trabajos Finalizados');
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      ob_start();
      $objWriter->save("php://output");
      $xlsData = ob_get_contents();
      ob_end_clean();
      $response =  array(
        'op' => 'ok',
        'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
      );
      echo json_encode($response);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }

    /*error_reporting(E_ALL);
      ini_set('display_errors', '1');

      $recibe      = json_decode($_REQUEST['envio'],true); 
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("D-Solutions");
      $objPHPExcel->getProperties()->setLastModifiedBy("D-Solutions");
      $objPHPExcel->getProperties()->setTitle("Trabajajos Finalizados");
      $objPHPExcel->getProperties()->setSubject("Trabajajos Finalizados");
      $objPHPExcel->getProperties()->setDescription("Trabajajos Finalizados");
      $objPHPExcel->setActiveSheetIndex(0);
  
      $objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getFont()->setBold(true);

      $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
      );

      $letcabeceras   = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
      $datoscabeceras = array('Fecha Labor','Técnico','Cliente','Razon Social','Patente','Dispositivo','Tipo Trabajo','Imei','Sello Seguridad','Ch1','Ch2','Ch3','Lugar','Comuna Orig.','Comuna Dest.','Kms Dest.','Descripción','Comentario','U.M','Valor trabajo','Valor KM','Total KM','Costo Labor','Centro Costo','Estado Facturación','Pago Técnico','Estado');

      $objPHPExcel->getActiveSheet()->getStyle("A1:Z1")->applyFromArray($style);
      $objPHPExcel->getActiveSheet()->getStyle("A1:Z1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
      $ind = 0;
      foreach($letcabeceras as $let){
         $objPHPExcel->getActiveSheet()->SetCellValue($let.'1', $datoscabeceras[$ind]);
         cellColor($let.'1', 'EAEAEA');
         $objPHPExcel->getActiveSheet()->getColumnDimension($let)->setWidth(20);
         $ind++;
      }
      
     
      $filtros = '';
      if($recibe['ccosto']!=''){
        $filtros .= " and tic.tic_centrocosto={$recibe['ccosto']} ";
      }

      if($recibe['estadofact']!=''){
        $filtros .= " and tic.tic_estadofact={$recibe['estadofact']} ";
      }

      if($recibe['pagot']!=''){
        $filtros .= " and tic.tic_pagot={$recibe['pagot']} ";
      }

      if($recibe['cliente']!=''){
        $filtros .= " and tic.tic_cliente={$recibe['cliente']} ";
      }

      if($recibe['tecnico']!=''){
        $filtros .= " and tic.tic_tecnico={$recibe['tecnico']} ";
      }

      if($recibe['desde']!='' && $recibe['hasta']==''){
        $filtros .= " and tic.tic_fechahorareg between '{$recibe['desde']} 00:00:00' and '{$recibe['desde']} 23:59:59'";
      }else if($recibe['desde']!='' && $recibe['hasta']!=''){
        $filtros .= " and tic.tic_fechahorareg between '{$recibe['desde']} 00:00:00' and '{$recibe['hasta']} 23:59:59'";
      }else if($recibe['desde']=='' && $recibe['hasta']!=''){
        $filtros .= " and tic.tic_fechahorareg between '{$recibe['hasta']} 00:00:00' and '{$recibe['hasta']} 23:59:59'";
      }

      $sql = "select tic.*,cli.razonsocial,cli.cuenta,veh.veh_id,veh.veh_patente,tdi.tdi_nombre,ttra.ttra_nombre, per.per_nombrecorto, com.comuna_nombre, avs.sen_id_1, avs.sen_id_2, avs.sen_id_3, com2.comuna_nombre as comunaorigen, mco.mcom_kms
            from tickets tic 
            left outer join clientes cli on tic.tic_cliente = id 
            left outer join vehiculos veh on tic.tic_patente = veh.veh_id 
            left outer join tiposdedispositivos tdi on tic.tic_dispositivo = tdi.tdi_id 
            left outer join tiposdetrabajos ttra on tic.tic_tipotrabajo = ttra.ttra_id
            left outer join personal per on tic.tic_tecnico = per.per_id 
            LEFT OUTER JOIN comunas com on com.comuna_id = tic.tic_comuna_des 
            LEFT OUTER JOIN comunas com2 on com2.comuna_id = tic.tic_comuna_ori
            left outer join asociacion_vehiculos_sensores avs on avs.veh_id = veh.veh_id and avs.avx_tecnico = tic.tic_tecnico 
            LEFT OUTER JOIN matriz_comunas mco on mco.mcom_idorigen = tic.tic_comuna_ori and mco.mcom_iddestino = tic.tic_comuna_des 
            where tic.tic_estado=3 {$filtros} group by tic_id order by tic_id desc";
      $res   = $link->query($sql);
      $index = 2;

      foreach($res as $key){
          $firstDate  = new DateTime($key['tic_fechahorareg']);
          $secondDate = new DateTime($fechachile);
          $intvl      = $firstDate->diff($secondDate);

          if($key["tic_pagot"]==1){
             $pagot = 'Si';
          }else if($key["tic_pagot"]==2){
             $pagot = 'No';
          }else{
             $pagot = 'N/A';
          }

          if($key["tic_estadofact"]==1){
             $estadof = 'OK';
          }else{
             $estadof = 'Pendiente';
          }

          if($key["tic_centrocosto"]==1){
             $centroc = 'Cliente';
          }else if($key["tic_centrocosto"]==2){
             $centroc = 'Interno';
          }else{
             $centroc = 'N/A';
          }

            $series = '';
            $sql1 = "SELECT * FROM productosxvehiculos where pxv_idveh = {$key['tic_patente']} order by 1 desc limit 1";
            $res1 = $link->query($sql1);
            $fila1 = mysqli_fetch_array($res1);
            if(mysqli_num_rows($res1)>0){
                $series = $fila1['pxv_nserie'];
            }

          $objPHPExcel->getActiveSheet()->SetCellValue('A'.($index), $key['tic_fechahorareg']);
          $objPHPExcel->getActiveSheet()->SetCellValue('B'.($index), $key['per_nombrecorto']);
          $objPHPExcel->getActiveSheet()->SetCellValue('C'.($index), strtolower($key['cuenta']));
          $objPHPExcel->getActiveSheet()->SetCellValue('D'.($index), $key['razonsocial']);
          $objPHPExcel->getActiveSheet()->SetCellValue('E'.($index), $key['veh_patente']);
          $objPHPExcel->getActiveSheet()->SetCellValue('F'.($index), $key['tdi_nombre']);
          $objPHPExcel->getActiveSheet()->SetCellValue('G'.($index), $key['ttra_nombre']);
          $objPHPExcel->getActiveSheet()->setCellValue('H'.($index), $series);
          $objPHPExcel->getActiveSheet()->getStyle('H'.($index))->getNumberFormat()->setFormatCode('0');

          $objPHPExcel->getActiveSheet()->SetCellValue('I'.($index), ($key['tic_sseguridad']=='' || $key['tic_sseguridad']=='null'?'-':$key['tic_sseguridad']));
          $objPHPExcel->getActiveSheet()->SetCellValue('J'.($index), ($key['sen_id_1']==0?'No':'Si'));
          $objPHPExcel->getActiveSheet()->SetCellValue('K'.($index), ($key['sen_id_1']==0?'No':'Si'));
          $objPHPExcel->getActiveSheet()->SetCellValue('L'.($index), ($key['sen_id_1']==0?'No':'Si'));
          $objPHPExcel->getActiveSheet()->SetCellValue('M'.($index), $key["tic_lugar"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('N'.($index), $key["comunaorigen"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('O'.($index), $key["comuna_nombre"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('P'.($index), $key["mcom_kms"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('Q'.($index), $key["tic_descripcion"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('R'.($index), $key["tic_desccierre"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('S'.($index), $key['tic_valortrabajo']);
          $objPHPExcel->getActiveSheet()->SetCellValue('T'.($index), ($key['tic_um']==1?'CLP':'UF'));
          $objPHPExcel->getActiveSheet()->SetCellValue('U'.($index), $key["tic_valortrabajo"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('V'.($index), $key["tic_valorkm"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('W'.($index), $key["tic_totalkm"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('X'.($index), $key["tic_costolabor"]);
          $objPHPExcel->getActiveSheet()->SetCellValue('Y'.($index), $centroc);
          $objPHPExcel->getActiveSheet()->SetCellValue('Z'.($index), $estadof);
          $objPHPExcel->getActiveSheet()->SetCellValue('AA'.($index), $pagot);
          
          $index++;
      }

      $objPHPExcel->getActiveSheet()->setTitle('Trabajos Finalizados');
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      ob_start();
      $objWriter->save("php://output");
      $xlsData = ob_get_contents();
      ob_end_clean();
      $response =  array(
          'op' => 'ok',
          'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($xlsData)
      );
      echo json_encode($response);*/
    break;

  case 'getNtickets':
    $sql       = "select * from tickets where tic_estado !=3 ";
    $res       = $link->query($sql);
    $nverde    = 0;
    $namarillo = 0;
    $nrojo     = 0;
    while ($fila = mysqli_fetch_array($res)) {
      $dateinicio = new DateTime($fila["tic_fechahorareg"]);
      $ndias      = getDiasMes($fila["tic_fechahorareg"]);
      $datenow    = new DateTime(date("Y-m-d H:i:s"));
      $tiempo     = $dateinicio->diff($datenow);
      $meses      = $tiempo->m;
      $dias       = $tiempo->d;
      $nhoras     = $tiempo->h;
      $dmes       = $meses * $ndias;
      //$hdias=  $dias * 24;
      $diastotales = $dmes + $dias;

      if ($diastotales > 10) {
        $nrojo++;
      } else if ($diastotales > 5 && $diastotales <= 10) {
        $namarillo++;
      } else {
        $nverde++;
      }
    }
    //$cuenta = mysqli_num_rows($res);
    $data["rojo"] = $nrojo;
    $data["amarillo"] = $namarillo;
    $data["verde"] = $nverde;
    echo json_encode($data);
    break;


  case 'crearticketpat':
    $recibe = json_decode($_REQUEST['envio'], true);
    $devuelve = array('resp' => 'error', 'mensaje' => 'Ha ocurrido un error');
    $sql = "SELECT * FROM estado_vehiculos where eve_patente = '{$recibe['patente']}'";
    $res = $link->query($sql);

    if (mysqli_num_rows($res) > 0) {
      $fila = mysqli_fetch_array($res);
      $sql1 = "SELECT t1.* 
            FROM clientes t1 
            Where t1.cli_estadows = 1 and t1.cli_nombrews = '{$fila['eve_cliente']}'
            GROUP by t1.cuenta
            order by t1.cuenta asc";
      $res1 = $link->query($sql1);

      if (mysqli_num_rows($res1) > 0) {
        $fila1 = mysqli_fetch_array($res1);
        $sql2 = "SELECT *
            FROM vehiculos
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(veh_patente), ')', ''), '(', ''), ' ', ''), '|', ''), '-', ''), '_', '') LIKE '%{$recibe['patente']}%' and deleted_at is NULL ORDER BY veh_tcreacion desc limit 1";
        $res2 = $link->query($sql2);
        if (mysqli_num_rows($res2) > 0) {
          $fila2 = mysqli_fetch_array($res2);
          $idveh = $fila2['veh_id'];
          $tiposer = $fila2['veh_tservicio'];
        } else {
          $sql3 = "INSERT INTO vehiculos (veh_cliente,veh_patente,veh_seriegps) VALUES ({$fila1['id']},'{$recibe['patente']}','')";
          $res3 = $link->query($sql3);
          $idveh = $link->insert_id;
          $tiposer = 1;
        }

        $sql4 = "INSERT INTO tickets (tic_fechahorareg,tic_cliente,tic_patente,tic_tipotrabajo,tic_tiposervicio,tic_estado) VALUES ('{$fechachile}','{$fila1['id']}','{$idveh}','1','{$tiposer}','1')";
        $res4 = $link->query($sql4);

        if ($res4) {
          $devuelve = array('resp' => 'success', 'mensaje' => 'Creado correctamente');
        }
      }
    }

    echo json_encode($devuelve);
    break;


  case 'getProxTiquet':
    $pxv = getProxVeh($_REQUEST["idveh"]);
    //echo $pxv;
    $sql = "SELECT t1.*, t2.pro_nombre, t2.pro_serie, t2.pro_id, t3.per_nombrecorto, t2.pro_codigo, t4.fam_nombre
        FROM serie_guia t1
        left outer join productos t2 on t2.pro_id = t1.pro_id
        left outer join personal t3 on t3.per_id = t1.usu_id_cargo
        LEFT OUTER JOIN familias t4 on t4.fam_id = t2.pro_familia
        where t1.ser_estado = 1 and t1.ser_instalado = 0 and t1.usu_id_cargo = {$_REQUEST["idtec"]}";
    $res       = $link->query($sql);
    //echo $sql;
    $productos = array();

    while ($fila = mysqli_fetch_array($res)) {
      if ($fila["pro_serie"] == 1) {
        $tieneserie = "SI";
        $cantidad   = 1;
      } else {
        $tieneserie = "NO";
        $cantidad   = 1;
      }
      if ($fila["ser_condicion"] == 1) {
        $estado = "BUENO";
      }
      if ($fila["ser_condicion"] == 0) {
        $estado = "MALO";
      }

      /* if($fila["ser_condicion"]==0){$estado="NO REGISTRADO";}*/

      $detalleKits = array();
      /*$sql1        = "SELECT easi.*, pro1.pro_nombre namegps, pro2.pro_nombre namesim FROM equipos_asociados easi LEFT OUTER JOIN productos pro1 ON pro1.pro_id=easi.easi_idgps LEFT OUTER JOIN productos pro2 ON pro2.pro_id=easi.easi_accesorio WHERE easi.easi_id={$fila['pxt_ideasi']}";

        $res1 = $link->query($sql1);

        while($fila1 = mysqli_fetch_array($res1)){
            $detalleKits[] = array(
                'id'      =>$fila1['easi_id'],
                'idgps'   =>$fila1['easi_idgps'],
                'ngps'    =>$fila1['namegps'],
                'seriegps'=>$fila1['easi_seriegps'],
                'idsim'   =>$fila1['easi_accesorio'],
                'nsim'    =>$fila1['namesim'],
                'seriesim'=>$fila1['easi_seriesim'],
                'bodega'  =>$fila1['easi_bodega'],
                'usercrea'=>$fila1['easi_user_create'],
                'fcrea'   =>$fila1['easi_create_at'],
            );
        }*/
      if ($fila["ser_codigo"] == "0" || $fila["ser_codigo"] == "") {
        $sercod = '-';
      } else {
        $sercod = $fila["ser_codigo"];
      }
      //if(!is_null($fila["pro_nombre"])){
      $productos[] = array("familia" => $fila["fam_nombre"], "nomtecnico" => $fila["per_nombrecorto"], "idpxt" => $fila["ser_id"], "idpro" => $fila["pro_id"], "codigo" => $fila["pro_codigo"], "cantidad" => $cantidad, "producto" => $fila["pro_nombre"], "tieneserie" => $tieneserie, "serie" => $sercod, "estado" => $estado, "observaciones" => '', 'tipo' => $tieneserie, 'ideasi' => $fila['pro_id'], "kitdetalle" => $detalleKits);
      //}
    }

    $sql2      = "SELECT * FROM sensores where sen_estado = 1";
    $res2      = $link->query($sql2);
    $sensores  = array();

    foreach ($res2 as $key2) {
      array_push($sensores, array('senid' => $key2['sen_id'], 'sen_nombre' => $key2['sen_nombre'], 'estado1' => $key2['sen_estado1'], 'estado2' => $key2['sen_estado2']));
    }

    $sql5      = "SELECT * FROM productos where pro_familia = 19";
    $res5      = $link->query($sql5);
    $family    = array();

    foreach ($res5 as $key5) {
      array_push($family, array('proid' => $key5['pro_id'], 'pro_serie' => $key5['pro_serie']));
    }

    $pxt                   = $productos;/*getProxTec($_REQUEST["idtec"]);*/
    $productos["pxv"]      = $pxv;
    $productos["pxt"]      = $pxt;
    $productos["sensores"] = $sensores;
    $productos["gpsf"]     = $family;
    echo json_encode($productos);
    break;

  case 'detallevivo':

    $recibe = json_decode($_REQUEST['envio'], true);
    /*$sql      = "SELECT * FROM equipos_asociados WHERE easi_idgps = {} and easi_seriegps = '{$_REQUEST['imei']}' limit 1";
   $res      = $link->query($sql);
   $fila     = mysqli_fetch_array($res);
   $sql1     = "SELECT * FROM sensores WHERE sen_estado=1 ORDER BY sen_id";
   $res1     = $link->query($sql1);
   $sensores = array();
   foreach($res1 as $key){

     $sql2 = "SELECT * FROM sensores_estado WHERE sene_idsensor={$key['sen_id']} AND sene_idasoc={$fila['easi_id']} LIMIT 1";
     $res2  = $link->query($sql2);
     $fila2 = mysqli_fetch_array($res2);

     array_push($sensores, array('nsensor'=>$key['sen_nombre'],'id'=>$key['sen_id'],'estado'=>$fila2['sene_senestado'],'estado1'=>$key['sen_estado1'],'estado2'=>$fila2['sen_estado2']));
   }*/

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://54.158.85.208/api/v1/trackup',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('imei' => $recibe['serie']),
      CURLOPT_HTTPHEADER => array(
        'Authorization: 202cb962ac59075b964b07152d234b70'
      ),
    ));

    $respalba = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($respalba);
    $dev = array('id' => 1, 'api' => $var, 'serie' => $recibe['serie']);
    echo json_encode($dev);

    break;

  case 'tipo_comando':

      // $recibe = json_decode($_REQUEST['envio'], true);

      $sql    = "SELECT * FROM tipo_icc ; ";
      $res    = $link->query($sql);

      $tipoIccArray = [];

      if ($res) {
          while ($row = $res->fetch_assoc()) {
              $tipoIccArray[] = $row;
          }
      }

      $dev = [
          'tipo_comandos' => $tipoIccArray, // Aquí almacenamos el array con los resultados
      ];
      
      // $curl = curl_init();
      // curl_setopt_array($curl, array(
      //   CURLOPT_URL => 'http://54.158.85.208/api/v1/trackup',
      //   CURLOPT_RETURNTRANSFER => true,
      //   CURLOPT_ENCODING => '',
      //   CURLOPT_MAXREDIRS => 10,
      //   CURLOPT_TIMEOUT => 0,
      //   CURLOPT_FOLLOWLOCATION => true,
      //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      //   CURLOPT_CUSTOMREQUEST => 'POST',
      //   CURLOPT_POSTFIELDS => array('imei' => $recibe['serie']),
      //   CURLOPT_HTTPHEADER => array(
      //     'Authorization: 202cb962ac59075b964b07152d234b70'
      //   ),
      // ));
  
      // $respalba = curl_exec($curl);
      // curl_close($curl);
      // $var = json_decode($respalba);

      // $var ="";
      // $dev = array('id' => 1, 'api' => $var, 'serie' => $recibe['serie']);
      echo json_encode($dev);
  
  break;

  case 'ejecutar_comando':

    $recibe = json_decode($_REQUEST['envio'], true);

    $estado     = $recibe['detalle'];
    $imei       = $recibe['imei'];

    $cliente    = $recibe['cliente'];
    $tipo_icc   = $recibe['comando'];
    $patente    = $recibe['patente'];

    $comando = 'setdigout ' . $estado;

    // Convertir cada carácter a su valor ASCII
    $asciiArray = [];
    for ($i = 0; $i < strlen($comando); $i++) {
      $asciiArray[] = ord($comando[$i]);
    }

    // Convertir el array de ASCII a una cadena (puedes usar json_encode o implode)
    $asciiString = implode(' ', $asciiArray); // Se convierte en una cadena separada por comas

    // return $comando;

    $status = 0; //0 comando no enviado //1 comando enviado a GW NEW para enviar a GPS //2 comando ha sido respondido desde GPS y se ha ejecutado

    // Verificar si el imei ya existe
    $query = "SELECT COUNT(*) AS total FROM vehicle_panic WHERE imei = '$imei'";
    $result = $link->query($query);
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
      // El IMEI ya existe, actualizar el registro
      $sql = "UPDATE vehicle_panic 
          SET protocol = 2, status = $status, message_origin = '$comando', message = '$asciiString', response =null , response_at=null 
          WHERE imei = '$imei'";
    } else {
      // El IMEI no existe, insertar un nuevo registro
      $sql = "INSERT INTO vehicle_panic (imei, protocol, status, message_origin, message) 
          VALUES ('$imei', 2, $status, '$comando', '$asciiString')";
    }

    
    $resPanic = $link->query($sql);

    $sqlHistorial = "INSERT INTO tickets_comandos (empresa, patente, imei ,tipo_icc, comando, respuesta_comando, ts_respuesta_comando) 
    VALUES ('$cliente', '$patente','$imei', $tipo_icc, '$estado', (select response from vehicle_panic where imei ='$imei' limit 1), (select response_at from vehicle_panic where imei ='$imei' limit 1) )";
    $resHistorial = $link->query($sqlHistorial);

    if($resPanic){
      //esto es para el historial de lo que se esta enviando, debido a que podrian enviar comandos seguidos y necesitamos saber si eso se ha realizado
      $sql = "INSERT INTO vehicle_panic_history (imei, protocol, status, message_origin, message) VALUES ('$imei', 2, $status, '$comando','$asciiString');";
      $resPanicHistory = $link->query($sql);
    
      $response_data["message"] = 'OK';
      $response_data['sim'] = "mensaje guardado : ". $activa." ".$desactiva;
      $response_data["icc"] = $sqlHistorial;
      $response_data["error"] = false;
    }else{
      $response_data["message"] = 'ERROR';
      $response_data['status'] = -4; //-4 error ingreso de registro de alerta en cloux bd
      $response_data['icc'] = 123;
      $response_data['sim'] = "reintente error ".$sql;
      $response_data["error"] = true;
    }
    
    echo json_encode( $response_data );

  break;

  case 'agregar_vehiculo_icc':

    $recibe = json_decode($_REQUEST['envio'], true);

    $comando   = $recibe['detalle'];
    $imei      = $recibe['imei'];
    $patente   = $recibe['patente'];
    $cliente   = $recibe['cliente'];
    $tipo_icc  = $recibe['comando'];

    
    $query = "SELECT COUNT(*) AS total FROM vehiculoscc WHERE empresa = '$cliente' and vehiculo= '$patente' ";
    $result = $link->query($query);
    $row = $result->fetch_assoc();

    if ($row['total'] == 1) {
      // La PATENTE ya existe, actualizar el registro

      $sql = "UPDATE vehiculoscc 
              SET tipo = $tipo_icc
              WHERE empresa = '$cliente' and vehiculo = '$patente' ";
    } else if ($row['total'] > 1){
      // El Patente y cliente existen, solo debe haber un registro para enviar comando al IMEI
      $resPanic = false;
    }else{

      

      $sql = "UPDATE vehiculos set veh_ccorriente = 1 
              WHERE veh_id in ( SELECT v.veh_id
                                FROM vehiculos AS v 
                                LEFT JOIN clientes AS c ON v.veh_cliente = c.id
                                WHERE c.cuenta = '$cliente' AND v.veh_patente = '$patente'
                                AND v.deleted_at is null)
              ";
      $res = $link->query($sql);

      //registro se debe crear
      $sql = "INSERT INTO vehiculoscc (empresa, vehiculo, tipo) 
              VALUES ('$cliente', '$patente', $tipo_icc)";
    }

    $sqlHistorial = "INSERT INTO tickets_comandos (empresa, patente, imei ,tipo_icc, comando, respuesta_comando, ts_respuesta_comando) VALUES ('$cliente', '$patente','$imei', $tipo_icc, '$comando', (select response from vehicle_panic where imei ='$imei' limit 1), (select response_at from vehicle_panic where imei ='$imei' limit 1) )";
    $resHistorial = $link->query($sqlHistorial);

    $resPanic = $link->query($sql);

    if($resPanic){
      //esto es para el historial de lo que se esta enviando, debido a que podrian enviar comandos seguidos y necesitamos saber si eso se ha realizado
      // $sql = "INSERT INTO vehicle_panic_history (imei, protocol, status, message_origin, message) VALUES ('$imei', 2, $status, '$comando','$asciiString');";
      // $resPanicHistory = $link->query($sql);
    
      $response_data["message"] = 'Registro creado correctamente o actualizado.';
      $response_data['sim'] = "mensaje guardado : ".$sqlHistorial;
      $response_data["error"] = false;
    }else{
      $response_data["message"] = 'Registro no ha sido posible crear.';
      $response_data['status'] = -4; //-4 error ingreso de registro de alerta en cloux bd
      $response_data['sim'] = "reintente error ".$sql;
      $response_data["error"] = true;
    }
    
    echo json_encode( $response_data );

  break;

  case 'insertarinstalacionnew':

    $recibe = json_decode($_REQUEST['envio'], true);
    $sql    = "SELECT (select pro_familia from productos where pro_id = serie_guia.pro_id ) as gps, serie_guia.* 
              FROM serie_guia WHERE ser_id = {$recibe['idserie']}";
    $res    = $link->query($sql);
    $fila   = mysqli_fetch_array($res);

    $sql2   = "SELECT * from productosxvehiculos where pxv_ideasi = {$recibe['idserie']}";
    $res2   = $link->query($sql2);

    if (mysqli_num_rows($res2) > 0) {
      $sql3 = "UPDATE productosxvehiculos set pxv_estado = 0 where pxv_ideasi = {$recibe['idserie']}";
      $res3 = $link->query($sql3);
    }

    if ($recibe['tabla'] == 1) {
      $sql1 = "INSERT into productosxvehiculos (pxv_idveh,pxv_cantidad,pxv_idpro,pxv_nserie,pxv_ideasi,pxv_tipo) 
              values ({$recibe['idpatente']},1,{$fila['pro_id']},'{$fila['ser_codigo']}',{$recibe['idserie']},1)";
      $res1 = $link->query($sql1);

      if ($recibe['din1'] == '') {
        $din1 = 0;
      } else {
        $din1 = $recibe['din1'];
      }

      if ($recibe['din2'] == '') {
        $din2 = 0;
      } else {
        $din2 = $recibe['din2'];
      }

      if ($recibe['din3'] == '') {
        $din3 = 0;
      } else {
        $din3 = $recibe['din3'];
      }

      $sql5 = "INSERT into asociacion_vehiculos_sensores (veh_id,sen_id_1,sen_id_2,sen_id_3,avx_tecnico,ser_id) 
              values ({$recibe['idpatente']},{$din1},{$din2},{$din3},{$recibe['idtecnico']},{$recibe['idserie']})";
      $res5 = $link->query($sql5);

      $msj = 'Serie ingresada correctamente';

      $sql4 = "UPDATE serie_guia set ser_instalado = 1 where ser_id = {$recibe['idserie']}";
      $res4 = $link->query($sql4);

      $sql7  = "SELECT * from serie_guia where ser_id = {$recibe['idserie']} and ser_estado = 1";
      $res7  = $link->query($sql7);
      $fila7 = mysqli_fetch_array($res7);

      $sql10  = "SELECT veh_cliente from vehiculos where veh_id = {$recibe['idpatente']} and deleted_at is NULL";
      $res10  = $link->query($sql10);
      $fila10 = mysqli_fetch_array($res10);

      if ($res10 && mysqli_num_rows($res10) > 0) {

        if ($fila['gps'] == 19) {
          $sql11  = "SELECT cli_nombrews from clientes where id = {$fila10['veh_cliente']}";
          $res11  = $link->query($sql11);
          $fila11 = mysqli_fetch_array($res11);
          if ($fila11['cli_nombrews'] != '' || $fila11['cli_nombrews'] != null) {
            $_bbddclient = strtolower($fila11['cli_nombrews']);
            if ($bbddclient != '') {
              $_bbddclient = $bbddclient;
            }

            $linkclient10 = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', $_bbddclient);

            if (mysqli_connect_errno()) {
              printf("Falló la conexión: %s\n", mysqli_connect_error());
              exit();
            }

            mysqli_set_charset($linkclient10, "utf8");

            $sql6 = "UPDATE vehiculos set veh_estado= 1, veh_seriegps = '{$fila7['ser_codigo']}' 
                    WHERE veh_patente = '{$recibe['patente']}' ; ";
            $res6 = $linkclient10->query($sql6);
          }
        }
      }
    } else {

      $sql1 = "UPDATE serie_guia 
                SET ser_instalado = 0, 
                    ser_condicion = {$recibe['condicion']}, 
                    ser_subestado = {$recibe['subestado']}, 
                    ser_observacion = '{$recibe['obser']}', 
                    usu_id_cargo = {$recibe['idtecnico']} 
              WHERE ser_id = {$recibe['idserie']}";
      $res1 = $link->query($sql1);
      $sql10  = " SELECT veh_cliente 
                  FROM vehiculos 
                  WHERE veh_id = {$recibe['idpatente']} 
                    and deleted_at is NULL";
      $res10  = $link->query($sql10);
      $fila10 = mysqli_fetch_array($res10);
      if (mysqli_num_rows($res10) > 0) {
        $sql11  = "SELECT cli_nombrews from clientes where id = {$fila10['veh_cliente']}";
        $res11  = $link->query($sql11);
        $fila11 = mysqli_fetch_array($res11);

        if ($fila11['cli_nombrews'] != '' || $fila11['cli_nombrews'] != null) {

          if ($fila['gps'] == 19) {
            $_bbddclient = strtolower($fila11['cli_nombrews']);
            if ($bbddclient != '') {
              $_bbddclient = $bbddclient;
            }

            $linkclient10 = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', $_bbddclient);

            if (mysqli_connect_errno()) {
              printf("Falló la conexión: %s\n", mysqli_connect_error());
              exit();
            }

            mysqli_set_charset($linkclient10, "utf8");

            //revisar, por que al eliminar imei eliminaba el vehiculo
            $sqlbn = "UPDATE vehiculos SET veh_seriegps = '', veh_estado = 0 
            WHERE veh_patente = '{$recibe['patente']}' and veh_estado = 1";
            $res_1 = $linkclient10->query($sqlbn);

            /*$sql_1 = "SELECT veh_id FROM vehiculos 
                    WHERE veh_patente='".$recibe['patente']."' and veh_estado = 1;";
            $res_1 = $linkclient10->query($sql_1);
            while($fila_1 = mysqli_fetch_array($res_1)){
            $sql_2 = "delete from vehiculos_asig where veha_idveh='".$fila_1["veh_id"]."';";
            $res_2 = $linkclient10->query($sql_2);
            }*/

            //$sqlbn = "delete from vehiculos where veh_patente='".$recibe['patente']."' and deleted_at is NULL;";
            //$resbn = $linkclient10->query($sqlbn);

            //$sqlbn = "delete from ultimaposicion where ulp_patente='".$recibe['patente']."';";
            //$resbn = $linkclient10->query($sqlbn);

          }
        }

        $sql5 = "UPDATE asociacion_vehiculos_sensores SET avx_estado = 0 WHERE ser_id = {$recibe['idserie']}";
        $res5 = $link->query($sql5);

        $msj = 'Vehículo borrado correctamente.';
      }
    }


    if ($res1) {
      $devuelve = array('logo' => 'success', 'mensaje' => $msj, 'sql' => $sql6, 'res' => $res6);
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => $msj, 'sql' => '', 'sql1' => $sql1);
    }
    mysqli_close($link);
    echo json_encode($devuelve);

    break;

  case 'obtenerinstalaciones':

    $recibe = json_decode($_REQUEST['envio'], true);

    if ($recibe['tabla'] == 1) {
      $sql    = "SELECT t1.*,t3.*, t2.pro_nombre,t2.pro_id, if(t3.ser_condicion=1,'BUENO','MALO') as condicion, t5.sen_nombre as din1, t6.sen_nombre as din2, t7.sen_nombre as din3
              FROM productosxvehiculos t1
              LEFT OUTER JOIN productos t2 on t2.pro_id = t1.pxv_idpro
              inner join serie_guia t3 on t3.ser_id = t1.pxv_ideasi
              left outer join asociacion_vehiculos_sensores t4 on t4.veh_id = t1.pxv_idveh and t4.avx_estado = 1 and t4.ser_id = t3.ser_id
              left outer join sensores t5 on t5.sen_id = t4.sen_id_1
              left outer join sensores t6 on t6.sen_id = t4.sen_id_2
              left outer join sensores t7 on t7.sen_id = t4.sen_id_3
              where t1.pxv_idveh = {$recibe['idpatente']} and t1.pxv_estado = 1 GROUP by 1";
      $res      = $link->query($sql);
      $devuelve = array();
      foreach ($res as $key) {
        array_push($devuelve, array('din1' => $key['din1'], 'din2' => $key['din2'], 'din3' => $key['din3'], 'pro_nombre' => $key['pro_nombre'], 'pro_id' => $key['pro_id'], 'ser_id' => $key['ser_id'], 'ser_codigo' => $key['ser_codigo'], 'condicion' => $key['condicion'], 'ser_codigo' => $key['ser_codigo'], 'ser_condicion' => $key['ser_condicion']));
      }
    } else {
      $sql    = "SELECT t1.*, t2.pro_nombre,t2.pro_id, if(t1.ser_condicion=1,'BUENO','MALO') as condicion
              FROM serie_guia t1
              LEFT OUTER JOIN productos t2 on t2.pro_id = t1.pro_id
              where t1.usu_id_cargo = {$recibe['idtecnico']} and ser_instalado = 0 and ser_estado = 1";
      $res      = $link->query($sql);
      $devuelve = array();
      foreach ($res as $key) {
        array_push($devuelve, array('pro_nombre' => $key['pro_nombre'], 'pro_id' => $key['pro_id'], 'ser_id' => $key['ser_id'], 'ser_codigo' => $key['ser_codigo'], 'condicion' => $key['condicion'], 'ser_codigo' => $key['ser_codigo'], 'ser_condicion' => $key['ser_condicion']));
      }
    }
    echo json_encode($devuelve);

    break;

  case 'detallevivoind':

    $recibe = json_decode($_REQUEST['envio'], true);
    /*$sql      = "SELECT * FROM equipos_asociados WHERE easi_idgps = {} and easi_seriegps = '{$_REQUEST['imei']}' limit 1";
   $res      = $link->query($sql);
   $fila     = mysqli_fetch_array($res);
   $sql1     = "SELECT * FROM sensores WHERE sen_estado=1 ORDER BY sen_id";
   $res1     = $link->query($sql1);
   $sensores = array();
   foreach($res1 as $key){

     $sql2 = "SELECT * FROM sensores_estado WHERE sene_idsensor={$key['sen_id']} AND sene_idasoc={$fila['easi_id']} LIMIT 1";
     $res2  = $link->query($sql2);
     $fila2 = mysqli_fetch_array($res2);

     array_push($sensores, array('nsensor'=>$key['sen_nombre'],'id'=>$key['sen_id'],'estado'=>$fila2['sene_senestado'],'estado1'=>$key['sen_estado1'],'estado2'=>$fila2['sen_estado2']));
   }*/

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://54.158.85.208/api/v1/trackup',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('imei' => $recibe['serie']),
      CURLOPT_HTTPHEADER => array(
        'Authorization: 202cb962ac59075b964b07152d234b70'
      ),
    ));

    $respalba = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($respalba);
    $dev = array('id' => 1, 'api' => $var, 'serie' => $recibe['serie']);
    echo json_encode($dev);

    break;

  case 'productoxTecniconew':

    $sql = "SELECT 
                t1.*, 
                t2.pro_serie,t2.pro_serie, t2.pro_nombre, 
                if(t1.ser_condicion=1, 'BUENO',
                if(t1.ser_condicion=0, 'MALO','NO IDENTIFICADO')) as nom_condicion, 
                if(t1.ser_tracking=1, 'Preparación',if(t1.ser_tracking=2, 'En Transito',
                if(t1.ser_tracking=3, 'Recepcionado','No Identificado'))) as Tracking
          FROM serie_guia t1
          LEFT JOIN productos t2 on t2.pro_id = t1.pro_id
          WHERE t1.usu_id_cargo = {$_REQUEST['idtecnico']} AND t1.ser_estado = 1 AND t1.ser_instalado = 0";

    $prod = array();

    $res  = $link->query($sql);
    mysqli_close($link);

    //$prod['idtecnico']= $_REQUEST['idtecnico'];
    //$prod['sql']= $sql;

    if ($res) {
      foreach ($res as $key) {
        array_push(
          $prod,
          array(
            'idpro' => $key['pro_id'],
            'ser_instalado' => $key['ser_instalado'],
            'serie' => $key['ser_codigo'],
            'idserie' => $key['ser_id'],
            'condicion' => $key['nom_condicion'],
            'pro_nombre' => $key['pro_nombre'],
            'proveedor' => $key['razonsocial'],
            'tieneserie' => $key['pro_serie'],
            'tipo' => 'Producto',
            'observacion' => ($key['ser_observacion'] == null ? '-' : $key['ser_observacion'])
          )
        );
      }
    }


    echo json_encode($prod);
    break;
    case 'productoxTecnico':

      $productos = array();
      $sql = "SELECT t1.*, t2.pro_serie, t2.pro_nombre, 
              IF(t1.ser_condicion=1, 'BUENO', IF(t1.ser_condicion=0, 'MALO','NO REGISTRADO')) AS condicion, 
              IF(t1.ser_tracking=1, 'Preparación', 
                 IF(t1.ser_tracking=2, 'En Transito', 
                    IF(t1.ser_tracking=3, 'Recepcionado','No Identificado'))) AS Tracking
              FROM serie_guia t1
              LEFT OUTER JOIN productos t2 ON t2.pro_id = t1.pro_id
              WHERE t1.usu_id_cargo = {$_REQUEST['idtecnico']} 
              AND t1.ser_estado = 1";
  
      $res = $link->query($sql);
  
      while ($fila = mysqli_fetch_array($res)) {
          $tieneserie = ((int)$fila['pro_serie'] == 1) ? 'SI' : 'NO';
          $serie = ($tieneserie == 'SI' && !empty($fila['ser_codigo'])) ? $fila['ser_codigo'] : '';
  
          $producto = array(
              'nomtracking' => $fila['ser_tracking_courrier'], 
              'ser_instalado' => $fila['ser_instalado'], 
              'rectracking' => $fila['ser_tracking_recibe'], 
              'fechatracking' => $fila['ser_tracking_fecha'], 
              'codtracking' => $fila['ser_tracking_codigo'], 
              'tracking' => $fila['Tracking'], 
              'tipo' => 1, 
              'estado' => $fila['condicion'], 
              'cantidad' => 1, 
              'producto' => $fila['pro_nombre'], 
              'tieneserie' => $tieneserie, 
              'serie' => $serie, 
              'observacion' => ($fila['ser_observacion'] == null ? '-' : $fila['ser_observacion']), 
              "kitdetalle" => array()
          );
  
          array_push($productos, $producto);
      }
  
      echo json_encode($productos);
      break;
  

  case 'agendarTicket':

    $masivos = json_decode($_REQUEST['masivos'], true);

    if ($_REQUEST["tic_horaagenda"] == '') {
      $_REQUEST["tic_horaagenda"] = "null";
    } else {
      $_REQUEST["tic_horaagenda"] = "'" . $_REQUEST["tic_horaagenda"] . "'";
    }

    if ($_REQUEST["tic_fechaagenda"] == '') {
      $_REQUEST["tic_fechaagenda"] = "null";
    } else {
      $_REQUEST["tic_fechaagenda"] = "'" . $_REQUEST["tic_fechaagenda"] . "'";
    }

    $idpersonal = 0;
    $sql = "SELECT usu_idpersonal FROM usuarios WHERE usu_id='{$_REQUEST["tic_tecnico"]}'";
    $res = $link->query($sql);
    if ($res) {
      $fila = mysqli_fetch_array($res);
      $idpersonal = $fila['usu_idpersonal'] == null || $fila['usu_idpersonal'] == '' ? 0 : $fila['usu_idpersonal'];
    }

    if (count($masivos) > 0) {
      foreach ($masivos as $data) {
        $sql = "update tickets set tic_fechaagenda = " . $_REQUEST["tic_fechaagenda"] . ",tic_horaagenda=" . $_REQUEST["tic_horaagenda"] . ", tic_usuario_externo='{$_REQUEST["tic_tecnico"]}', tic_tecnico='" . $idpersonal . "',tic_descagenda='" . $_REQUEST["tic_descagenda"] . "',tic_estado=2  where tic_id='" . $data . "'";
        $res = $link->query($sql);
      }
    } else {
      $sql = "update tickets set tic_fechaagenda = " . $_REQUEST["tic_fechaagenda"] . ",tic_horaagenda=" . $_REQUEST["tic_horaagenda"] . ", tic_usuario_externo='{$_REQUEST["tic_tecnico"]}', tic_tecnico='" . $idpersonal . "',tic_descagenda='" . $_REQUEST["tic_descagenda"] . "',tic_estado=2  where tic_id='" . $_REQUEST["tic_id"] . "'";
      $res = $link->query($sql);
      /*echo $sql.'<br>';*/
    }

    $h_tipo = 1; // tipo 1: tickets
    $h_estado = 2; // agendada
    Historial($h_tipo, $_REQUEST["tic_id"], $h_estado);
    $response['update'] = $sql;
    echo json_encode($response);
    break;

  case 'cerrarTicket':

    $ipPrivada = "http://54.158.85.208";
    $ipPrivada = "http://172.31.30.97";

    $sql = "SELECT (select pro_familia from productos where pro_id = ( select pro_id from serie_guia where ser_codigo = t4.pxv_nserie limit 1 )) as gps, 
              t1.*, t2.*, t3.razonsocial, t3.cli_clavews, t3.cli_nombrews, t4.pxv_nserie, t4.pxv_ideasi
            FROM tickets t1
            LEFT OUTER JOIN vehiculos t2 on t2.veh_id = t1.tic_patente
            LEFT OUTER JOIN clientes t3 on t3.id = t1.tic_cliente
            LEFT OUTER JOIN productosxvehiculos t4 on t4.pxv_idveh = t1.tic_patente and t4.pxv_estado = 1 and t4.pxv_nserie <> '' and t4.pxv_nserie <> '0' and t4.pxv_nserie <> 'null' 
              and (t4.pxv_idpro = 170 or t4.pxv_idpro = 208 or t4.pxv_idpro = 161 or t4.pxv_idpro = 183 or t4.pxv_idpro = 180 or t4.pxv_idpro = 215 ) 
            WHERE t1.tic_id = " . $_REQUEST["tic_id"] . " order by t4.pxv_nserie desc limit 1";

    if ($_REQUEST["tic_id"] == 1122) {
      //echo $sql;
      //exit;
    }
    $res  = $link->query($sql);
    $fila = mysqli_fetch_array($res);


    if($_REQUEST["tic_id"] == 3151){
      
    }


    //codigo nuevo para definir especificamente que accesorio tiene el ticket, boton de panico, cerradura electrica, corta corriente, esto para que desde cliente se pueda saber que vehiculos tienen estos elementos debido a que hoy se desconoce 20250207 1733
    $ticketID = $_REQUEST["tic_id"]; // o el valor que corresponda
    $accesorios = json_decode($_POST['taccesorios'], true); // Obtiene el array de accesorios
    // Llamar a la función para manejar los accesorios
    actualizarAccesorios($ticketID, $accesorios, $link);


    if ($fila['cli_nombrews'] != '' || $fila['cli_nombrews'] != null) {
      $_bbddclient = strtolower($fila['cli_nombrews']);
      if ($bbddclient != '') {
        $_bbddclient = $bbddclient;
      }

      $linkclient = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', $_bbddclient);

      if (mysqli_connect_errno()) {
        printf("Falló la conexión: %s\n", mysqli_connect_error());
        exit();
      }

      mysqli_set_charset($linkclient, "utf8");


      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $ipPrivada . '/api/v1/searchvehicle',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('patente' => $fila['veh_patente']),
        CURLOPT_HTTPHEADER => array(
          'Authorization: 202cb962ac59075b964b07152d234b70'
        ),
      ));

      $respvehiculo = curl_exec($curl);
      curl_close($curl);
      $varveh = json_decode($respvehiculo, true);

      $existepantet = false;
      $empresapatenteexiste = '';
      if ($varveh['patente'] == 'Sin Datos') {
        $existepantet = false;
      } else {
        foreach ($varveh['patente'] as $key => $data) {
          $existepantet = true;
          $empresapatenteexiste = $data['empresa'];
        }
      }


      /*if($empresapatenteexiste!=strtolower($fila['cli_nombrews'])){
            $devuelve = array('logo'=>'error', 'mensaje'=>'Este vehiculo ya existe en empresa '.$empresapatenteexiste,'sql'=>$sql);
            echo json_encode($devuelve);
            die();
        }
        echo $empresapatenteexiste.'<---------<br>';
        echo $existepantet.'<---------<br>';
        echo $_REQUEST['opc'].'<---------<br>';
        die();*/

      if ($_REQUEST['opc'] == 0 || $_REQUEST['opc'] == 1) {

        //si lo que viene en el ticket pxv_nserie es GPS se actualiza el imei en cliente
        if (in_array($fila['gps'], array(19))) {
          $curl = curl_init();
          $fila['pxv_nserie'] = trim($fila['pxv_nserie']);
          $dataPost = array('patente' => $fila['veh_patente'], 'imei' => $fila['pxv_nserie'], 'cliente' => strtolower($fila['cli_nombrews']));
          curl_setopt_array($curl, array(
            CURLOPT_URL => $ipPrivada . '/api/v1/insertgpsalb',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dataPost,
            CURLOPT_HTTPHEADER => array(
              'Authorization: 202cb962ac59075b964b07152d234b70'
            ),
          ));

          $respalba = curl_exec($curl);
          curl_close($curl);
          $var = json_decode($respalba, true);
        } else {
          $res = array('gps' => 'Otro Trabajo');
          $var = json_encode($res, true);
        }
      } else {
        $res = array('gps' => 'Otro Trabajo');
        $var = json_encode($res, true);
      }

      if (true) {
        $sqle = "SHOW COLUMNS FROM vehiculos WHERE Field = 'veh_iccgps'";
        $rese = $linkclient->query($sqle);
        if ($rese && mysqli_num_rows($rese) == 0) {
          $sqlr = "ALTER TABLE vehiculos ADD veh_iccgps VARCHAR(20) NULL DEFAULT 0 AFTER veh_seriegps";
          $resr = $linkclient->query($sqlr);
        }

        $sqle1 = "SHOW COLUMNS FROM vehiculos WHERE Field = 'veh_horometroCAN'";
        $rese1 = $linkclient->query($sqle1);
        if ($rese1 && mysqli_num_rows($rese1) == 0) {
          $sqlr1 = "ALTER TABLE vehiculos ADD veh_horometroCAN VARCHAR(20) NULL DEFAULT 0 AFTER veh_seriegps";
          $resr1 = $linkclient->query($sqlr1);
        }

        $sqle2 = "SHOW COLUMNS FROM vehiculos WHERE Field = 'veh_fonogps'";
        $rese2 = $linkclient->query($sqle2);
        if ($rese2 && mysqli_num_rows($rese2) == 0) {
          $sqlr2 = "ALTER TABLE vehiculos ADD veh_fonogps VARCHAR(20) NULL DEFAULT 0 AFTER veh_seriegps";
          $resr2 = $linkclient->query($sqlr2);
        }
      }

      //definimos tipo de servicio
      //$fila['tic_tiposervicio']==1 basico
      //$fila['tic_tiposervicio']==2 avanzado
      //$fila['tic_tiposervicio']==3 thermo
      $idtiposer = 2;
      if ($fila['tic_tiposervicio'] == 2) {
        $idtiposer = 1; //avanzado
      } else if ($fila['tic_tiposervicio'] != 2 && $fila['tic_tiposervicio'] != 1) {
        $idtiposer = 2; //basico
      } else {
        $idtiposer = 2;
      }


      $tservicio = $_REQUEST["tservicio"];

      if ($tservicio == 1) {
        $idtiposer = 2; //basico
      } else if ($tservicio == 2) {
        $idtiposer = 1; //avanzado
      } else if ($tservicio == 3) {
        $idtiposer = 3; //thermo
      } else {
        echo 'Sin servicio.';
        // return false;
      }

      // echo '<pre>';
      // print_r ( $tservicio ) ;
      // print_r (  $idtiposer ) ;
      // echo '</pre>';
      // exit;

      $fila3 = array();
      if ($idtiposer == 1) {
        $sql2 = "SELECT t1.pxv_nserie FROM productosxvehiculos t1 
                  LEFT OUTER JOIN productos t2 ON t2.pro_id=t1.pxv_idpro 
                  WHERE t2.pro_familia=22 
                  AND t1.pxv_estado=1 
                  AND t1.pxv_idveh='{$fila['tic_patente']}'";
        $res2 = $link->query($sql2);
        $fila3 = mysqli_fetch_array($res2);
      }


      if ($var['gps'] == 'Vehiculo actualizado satisfactoriamente.' || $var['gps'] == 'Vehiculo guardado satisfactoriamente.') {

        if ($_REQUEST['tipotrabajo'] == 3) {
          $sql1 = "UPDATE vehiculos set veh_imei = '',veh_estado = '0' , veh_tservicio = '{$tservicio}' where veh_patente = '{$fila['veh_patente']}' and deleted_at is NULL";
          $res1 = $link->query($sql1);

          $sql1 = "UPDATE productosxvehiculos set pxv_estado = 0 where pxv_nserie = '{$fila['pxv_nserie']}'";
          $res1 = $link->query($sql1);

          $sql11 = "UPDATE serie_guia set ser_instalado = 0 where ser_codigo = '{$fila['pxv_nserie']}'";
          $res11 = $link->query($sql11);
        } else {
          if (isset($fila3['pxv_nserie'])) {
            $sql1 = "UPDATE vehiculos set veh_imei = '{$fila['pxv_nserie']}', veh_can='{$fila3['pxv_nserie']}', veh_tservicio = '{$tservicio}' where veh_patente = '{$fila['veh_patente']}'  and deleted_at is NULL";
            $res1 = $link->query($sql1);
          } else {
            $sql1 = "UPDATE vehiculos set veh_imei = '{$fila['pxv_nserie']}', veh_tservicio = '{$tservicio}' where veh_patente = '{$fila['veh_patente']}'  and deleted_at is NULL";
            $res1 = $link->query($sql1);
          }
          //$sql1 = "UPDATE vehiculos set veh_imei = '{$fila['pxv_nserie']}', veh_tservicio = '{$tservicio}' where veh_patente = '{$fila['veh_patente']}'  and deleted_at is NULL";
          //$res1 = $link->query($sql1);

          $sql1 = "UPDATE productosxvehiculos set pxv_idveh = '{$fila['veh_patente']}' where pxv_nserie = '{$fila['pxv_nserie']}'";
          $res1 = $link->query($sql1);

          $sql11 = "UPDATE serie_guia set ser_instalado = 1 where ser_codigo = '{$fila['pxv_nserie']}'";
          $res11 = $link->query($sql11);
        }


        $sqlal   = "SELECT * from vehiculos where veh_patente = '{$fila['veh_patente']}'";
        $res2a   = $linkclient->query($sqlal);
        $filaalb = mysqli_fetch_array($res2a);



        if ($filaalb['veh_id'] != '') {

          //tipo de trabajo 3 es desisntalacion
          if ($_REQUEST['tipotrabajo'] == 3) {
            $sql2 = "UPDATE vehiculos 
                          SET veh_seriegps = '', veh_tiposerv='{$idtiposer}' , veh_estado = 0
                          WHERE veh_patente = '{$fila['veh_patente']}'";
            $res2 = $linkclient->query($sql2);
          } else {

            //si lo que viene en el ticket pxv_nserie es GPS se actualiza el imei en cliente
            if (in_array($fila['gps'], array(19))) {
              $sql2 = "UPDATE vehiculos 
                            SET veh_seriegps = '{$fila['pxv_nserie']}', veh_tiposerv='{$idtiposer}' , veh_estado = 1
                            WHERE veh_patente = '{$fila['veh_patente']}'";
              $res2 = $linkclient->query($sql2);
            }
          }

          /* echo $sql2.'<br>';*/

          //si registro de ultima posicion no existe, se crea para que reporte en plataforma y se visualice
          $sqlal   = "SELECT ulp_patente FROM ultimaposicion WHERE ulp_patente = '{$fila['veh_patente']}'";
          $res2a   = $linkclient->query($sqlal);
          $existUltiPosition = mysqli_fetch_array($res2a);
          if (!($existUltiPosition['ulp_patente'] != '')) {
            if ($_REQUEST['tipotrabajo'] != 3) {
              $sql2      = "INSERT INTO ultimaposition(ulp_patente, ulp_idveh) values ('{$fila['veh_patente']}','{$filaalb['veh_id']}')";
              $resUltPos = $linkclient->query($sql2);
            }
          }
        } else {

          $sql2    = "INSERT INTO vehiculos(veh_rsocial, veh_propietario, veh_tipoveh, veh_grupoveh, veh_codigo, veh_patente, veh_seriegps, veh_seriesim, veh_otraseries, veh_tiposerv, veh_estadofun) 
                            values ('0','0','{$fila['veh_tipo']}','0','','{$fila['veh_patente']}','{$fila['pxv_nserie']}','','','{$idtiposer}','1')";
          $res2 = $linkclient->query($sql2);
          /* echo $sql2.'<br>';*/
        }

        if ($res2 || $_REQUEST['tipotrabajo'] == 3) {

          $empexis = json_decode($var['clientes'], true);
          if (count($empexis) > 0) {
            foreach ($empexis as $keyexi => $dataexis) {
              $_bbddclient3 = strtolower($dataexis['cliente']);
              if ($bbddclient3 != '') {
                $_bbddclient3 = $bbddclient3;
              }

              $linkclient3 = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', $_bbddclient3);

              if (mysqli_connect_errno()) {
                printf("Falló la conexión: %s\n", mysqli_connect_error());
                exit();
              }

              mysqli_set_charset($linkclient3, "utf8");

              /* $sql3    = "DELETE FROM vehiculos WHERE veh_patente = '{$fila['veh_patente']}'";
                       $res3 = $linkclient3->query($sql3);
                       
                       $sql3    = "DELETE FROM ultimaposicion WHERE ulp_patente = '{$fila['veh_patente']}'";
                       $res3 = $linkclient3->query($sql3);
                       mysqli_close($linkclient3);*/
            }
          }

          if ($empresapatenteexiste != '') {
            $_bbddclient2 = strtolower($empresapatenteexiste);
            if ($bbddclient2 != '') {
              $_bbddclient2 = $bbddclient2;
            }

            $linkclient2 = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', $_bbddclient2);

            if (mysqli_connect_errno()) {
              printf("Falló la conexión: %s\n", mysqli_connect_error());
              exit();
            }

            mysqli_set_charset($linkclient2, "utf8");

            /* $sql3    = "DELETE FROM vehiculos WHERE veh_patente = '{$fila['veh_patente']}'";
                   $res3 = $linkclient2->query($sql3);

                   $sql3    = "DELETE FROM ultimaposicion WHERE ulp_patente = '{$fila['veh_patente']}'";
                   $res3 = $linkclient2->query($sql3);*/
          }


          $update = '';
          for ($i = 0; $i < (int)$_REQUEST['cantidad']; $i++) {
            $archivo  = $_FILES['imglist' . ($i + 1)]['name'];
            $type     = $_FILES['imglist' . ($i + 1)]['type'];
            $temporal = file_get_contents($_FILES['imglist' . ($i + 1)]['tmp_name']);
            $img      = 'data:' . $type . ';base64,' . base64_encode($temporal);
            $update  .= ', tic_img' . ($i + 1) . '=\'' . $img . '\'';
          }

          $fecha = date("Y-m-d");
          $sql   = "UPDATE tickets 
                          SET 
                            tic_fechacierre = '" . $fecha . "',
                            tic_desccierre='" . $_REQUEST["tic_desccierre"] . "',
                            tic_estado=3 " . $update . "  
                            WHERE tic_id='" . $_REQUEST["tic_id"] . "'";
          $res      = $link->query($sql);
          /*echo $sql.'<br>';*/
          $h_tipo   = 1; // tipo 1: tickets
          $h_estado = 3; // ticket cerrado
          Historial($h_tipo, $_REQUEST["tic_id"], $h_estado);
          if ($res) {

            $sqldet   = "SELECT 
                                  t1.* , 
                                  t2.veh_patente, t2.veh_cliente, t2.veh_rsocial, 
                                  t3.cuenta, t3.razonsocial, t3.correo, 
                                  concat(t4.per_nombrecorto,' ',t4.per_apaterno) as nombretecnico, 
                                  t5.ttra_nombre, t3.direccion
                                FROM tickets t1
                                INNER JOIN vehiculos t2 on t2.veh_id = t1.tic_patente
                                LEFT OUTER JOIN clientes t3 on t3.id = t2.veh_cliente
                                LEFT OUTER JOIN personal t4 on t4.per_id = t1.tic_tecnico
                                LEFT OUTER JOIN tiposdetrabajos t5 on t5.ttra_id = t1.tic_tipotrabajo
                                where t1.tic_id = {$_REQUEST["tic_id"]}";
            $resdet    = $link->query($sqldet);
            $datos = array();

            if ($resdet && mysqli_num_rows($resdet) > 0) {
              $filacli = mysqli_fetch_array($resdet);

              if ($filacli['correo'] != '' && $filacli['correo'] != null) {
                $fechachilebuen = date("d-m-Y H:i:s");
                $imeitic = "";
                $myString = "";
                $sqlpv = "SELECT * FROM productosxvehiculos where pxv_idveh = {$filacli['tic_patente']}";
                $respv = $link->query($sqlpv);
                if ($respv && mysqli_num_rows($respv) > 0) {
                  foreach ($respv as $keypv => $datapv) {
                    $imeitic .= $datapv['pxv_nserie'] . ',';
                  }
                  $myString = substr($imeitic, 0, -1);
                }

                $datos[] = array(
                  'fechatrabajo' => $fechachilebuen,
                  'tecnico'      => ($filacli['nombretecnico'] == null || $filacli['nombretecnico'] == '' ? '-' : $filacli['nombretecnico']),
                  'tipotrabajo'  => ($filacli['ttra_nombre'] == null || $filacli['ttra_nombre'] == '' ? '-' : $filacli['ttra_nombre']),
                  'cliente'      => ($filacli['cuenta'] == null || $filacli['cuenta'] == '' ? '-' : $filacli['cuenta']),
                  'ndispositivo' => $myString,
                  'direccion'    => ($filacli['tic_lugar'] == null || $filacli['tic_lugar'] == '' ? 'N/A' : $filacli['tic_lugar']),
                  'correo'       => $filacli['correo'],
                  'patente'      => $filacli['veh_patente'],
                );

                enviaemail($datos);
              }
            }
            $devuelve = array('logo' => 'success', 'mensaje' => 'Migrado correctamente', 'sql' => $sql2);
          } else {
            $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error al migrar la información', 'sql' => $sql, 'msg' => mysqli_error($link));
          }
        } else {
          $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error al migrar la información', 'sql' => $sql2, 'msg' => mysqli_error($linkclient));
        }
      } else {


        if ($_REQUEST['opc'] == 1) {

          $sql2  = "SELECT * from vehiculos WHERE veh_patente = '{$fila['veh_patente']}' order by 1 desc limit 1";
          $res2  = $linkclient->query($sql2);
          $fila2 = mysqli_fetch_array($res2);

          $devuelve = array('logo' => 'error', 'mensaje' => 'paso 1 instalacion', 'sql' => $sql2);
          /*  if($fila2['veh_id']>0){*/
          if ($_REQUEST['tipotrabajo'] == 3) {
            /*$sql3 = "update vehiculos  set veh_estado='0', veh_seriegps = '' where veh_id = {$fila2['veh_id']}";
                      $res3 = $linkclient->query($sql3);*/
            $sqlvc = "UPDATE vehiculos  
                                SET veh_estado='0', veh_imei='' 
                                WHERE veh_patente = '{$fila['veh_patente']}'";
            $resvc = $link->query($sqlvc);
            /*echo $sqlvc;*/
            $update = '';
            for ($i = 0; $i < (int)$_REQUEST['cantidad']; $i++) {
              $archivo  = $_FILES['imglist' . ($i + 1)]['name'];
              $type     = $_FILES['imglist' . ($i + 1)]['type'];
              $temporal = file_get_contents($_FILES['imglist' . ($i + 1)]['tmp_name']);
              $img      = 'data:' . $type . ';base64,' . base64_encode($temporal);
              $update  .= ', tic_img' . ($i + 1) . '=\'' . $img . '\'';
            }
            $fecha = date("Y-m-d");
            $sql   = "UPDATE tickets 
                                SET 
                                  tic_fechacierre = '" . $fecha . "',
                                  tic_desccierre='" . $_REQUEST["tic_desccierre"] . "',
                                  tic_estado = 3 " . $update . "  
                                WHERE 
                                  tic_id='" . $_REQUEST["tic_id"] . "'";
            $res      = $link->query($sql);
            $h_tipo   = 1; // tipo 1: tickets
            $h_estado = 3; // ticket cerrado
            Historial($h_tipo, $_REQUEST["tic_id"], $h_estado);
            if ($res) {
              $sqldet   = "SELECT t1.* , t2.veh_patente, t2.veh_cliente, t2.veh_rsocial, t3.cuenta, t3.razonsocial, t3.correo, concat(t4.per_nombrecorto,' ',t4.per_apaterno) as nombretecnico, t5.ttra_nombre, t3.direccion
                                FROM tickets t1
                                INNER JOIN vehiculos t2 on t2.veh_id = t1.tic_patente
                                LEFT OUTER JOIN clientes t3 on t3.id = t2.veh_cliente
                                LEFT OUTER JOIN personal t4 on t4.per_id = t1.tic_tecnico
                                LEFT OUTER JOIN tiposdetrabajos t5 on t5.ttra_id = t1.tic_tipotrabajo
                                where t1.tic_id = {$_REQUEST["tic_id"]}";
              $resdet    = $link->query($sqldet);
              $datos = array();
              if (mysqli_num_rows($resdet) > 0) {
                $filacli = mysqli_fetch_array($resdet);

                $imeitic = "";
                $myString = "";
                $sqlpv = "SELECT * FROM productosxvehiculos where pxv_idveh = {$filacli['tic_patente']} group by pxv_nserie";
                $respv = $link->query($sqlpv);
                if (mysqli_num_rows($respv) > 0) {
                  foreach ($respv as $keypv => $datapv) {
                    $imeitic .= $datapv['pxv_nserie'] . ',';
                  }
                  $myString = substr($imeitic, 0, -1);
                }

                $sql2alb   = "update tickets set tic_imeis='" . $myString . "' where tic_id='" . $_REQUEST["tic_id"] . "'";
                $res2alb      = $link->query($sql2alb);

                if ($myString != '') {
                  $explo = explode(",", $myString);
                  foreach ($explo as $keyex) {
                    $sql11 = "UPDATE serie_guia set ser_instalado = 0 where ser_codigo = '{$keyex}'";
                    $res11 = $link->query($sql11);
                  }
                }


                if ($filacli['correo'] != '' && $filacli['correo'] != null) {
                  $fechachilebuen = date("d-m-Y H:i:s");

                  $datos[] = array(
                    'fechatrabajo' => $fechachilebuen,
                    'tecnico'      => ($filacli['nombretecnico'] == null || $filacli['nombretecnico'] == '' ? '-' : $filacli['nombretecnico']),
                    'tipotrabajo'  => ($filacli['ttra_nombre'] == null || $filacli['ttra_nombre'] == '' ? '-' : $filacli['ttra_nombre']),
                    'cliente'      => ($filacli['cuenta'] == null || $filacli['cuenta'] == '' ? '-' : $filacli['cuenta']),
                    'ndispositivo' => $myString,
                    'direccion'    => ($filacli['tic_lugar'] == null || $filacli['tic_lugar'] == '' ? 'N/A' : $filacli['tic_lugar']),
                    'correo'       => $filacli['correo'],
                    'patente'      => $filacli['veh_patente'],
                  );

                  enviaemail($datos);
                }
              }
              $devuelve = array('logo' => 'success', 'mensaje' => 'Migrado correctamente');
            } else {
              $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error al migrar la información', 'msg' => mysqli_error($link));
            }
          } else {
            $update = '';
            for ($i = 0; $i < (int)$_REQUEST['cantidad']; $i++) {
              $archivo  = $_FILES['imglist' . ($i + 1)]['name'];
              $type     = $_FILES['imglist' . ($i + 1)]['type'];
              $temporal = file_get_contents($_FILES['imglist' . ($i + 1)]['tmp_name']);
              $img      = 'data:' . $type . ';base64,' . base64_encode($temporal);
              $update  .= ', tic_img' . ($i + 1) . '=\'' . $img . '\'';
            }
            $fecha = date("Y-m-d");
            $sql   = "update tickets set tic_fechacierre = '" . $fecha . "',tic_desccierre='" . $_REQUEST["tic_desccierre"] . "',tic_estado = 3 " . $update . "  where tic_id='" . $_REQUEST["tic_id"] . "'";
            $res      = $link->query($sql);
            $h_tipo   = 1; // tipo 1: tickets
            $h_estado = 3; // ticket cerrado
            Historial($h_tipo, $_REQUEST["tic_id"], $h_estado);

            if ($_REQUEST['tipotrabajo'] == 1) {
              //soporte

              /*
                            $idtiposer = 1;
                            if($fila['veh_tiposerv']==1){
                                $idtiposer = 1;
                            }else if($fila['veh_tiposerv']!=2 && $fila['veh_tiposerv']!=1){
                                $idtiposer = 2;
                            }*/


              //si lo que viene en el ticket pxv_nserie es GPS se actualiza el imei en cliente
              if (in_array($fila['gps'], array(19))) {

                if ($fila2['veh_id'] != '') {

                  $sql2 = "UPDATE vehiculos set veh_estado='1', veh_seriegps = '{$fila['pxv_nserie']}', 
                                          veh_tiposerv='{$idtiposer}' where veh_patente = '{$fila['veh_patente']}'";
                  $res2 = $linkclient->query($sql2);

                  $sqlal   = "SELECT ulp_patente from ultimaposicion where ulp_patente = '{$fila['veh_patente']}'";
                  $res2a   = $linkclient->query($sqlal);
                  $existUltiPosition = mysqli_fetch_array($res2a);
                  if (!($existUltiPosition['ulp_patente'] != '')) {
                    $sql2    = "INSERT INTO ultimaposition(ulp_patente, ulp_idveh) values ('{$fila['veh_patente']}','{$fila2['veh_id']}')";
                    $resUltPos = $linkclient->query($sql2);
                  }
                } else {
                  $sql2    = "INSERT INTO vehiculos(veh_rsocial, veh_propietario, veh_tipoveh, veh_grupoveh, veh_codigo, veh_patente, veh_seriegps, veh_seriesim, veh_otraseries, veh_tiposerv, veh_estadofun) 
                                              values ('0','0','{$fila['veh_tipo']}','0','','{$fila['veh_patente']}','{$fila['pxv_nserie']}','','','{$idtiposer}','1')";
                  $res2 = $linkclient->query($sql2);
                }

                $sql5 = "UPDATE vehiculos set veh_imei = '{$fila['pxv_nserie']}' where veh_id = '{$fila2['veh_id']}'";
                $res5 = $link->query($sql5);
              }

              //bd cloux
              $sql11 = "UPDATE serie_guia set ser_instalado = 1 where ser_codigo = '{$fila['pxv_nserie']}'";
              $res11 = $link->query($sql11);
            }

            if ($res) {

              $sqldet   = "SELECT t1.* , t2.veh_patente, t2.veh_cliente, t2.veh_rsocial, t3.cuenta, t3.razonsocial, t3.correo, concat(t4.per_nombrecorto,' ',t4.per_apaterno) as nombretecnico, t5.ttra_nombre, t3.direccion
                                FROM tickets t1
                                INNER JOIN vehiculos t2 on t2.veh_id = t1.tic_patente
                                LEFT OUTER JOIN clientes t3 on t3.id = t2.veh_cliente
                                LEFT OUTER JOIN personal t4 on t4.per_id = t1.tic_tecnico
                                LEFT OUTER JOIN tiposdetrabajos t5 on t5.ttra_id = t1.tic_tipotrabajo
                                where t1.tic_id = {$_REQUEST["tic_id"]}";
              $resdet    = $link->query($sqldet);
              $datos = array();
              if (mysqli_num_rows($resdet) > 0) {
                $filacli = mysqli_fetch_array($resdet);
                if ($filacli['correo'] != '' && $filacli['correo'] != null) {
                  $fechachilebuen = date("d-m-Y H:i:s");
                  $imeitic = "";
                  $myString = "";
                  $sqlpv = "SELECT * FROM productosxvehiculos where pxv_idveh = {$filacli['tic_patente']}";
                  $respv = $link->query($sqlpv);
                  if (mysqli_num_rows($respv) > 0) {
                    foreach ($respv as $keypv => $datapv) {
                      $imeitic .= $datapv['pxv_nserie'] . ',';
                    }
                    $myString = substr($imeitic, 0, -1);
                  }

                  $datos[] = array(
                    'fechatrabajo' => $fechachilebuen,
                    'tecnico'      => ($filacli['nombretecnico'] == null || $filacli['nombretecnico'] == '' ? '-' : $filacli['nombretecnico']),
                    'tipotrabajo'  => ($filacli['ttra_nombre'] == null || $filacli['ttra_nombre'] == '' ? '-' : $filacli['ttra_nombre']),
                    'cliente'      => ($filacli['cuenta'] == null || $filacli['cuenta'] == '' ? '-' : $filacli['cuenta']),
                    'ndispositivo' => $myString,
                    'direccion'    => ($filacli['tic_lugar'] == null || $filacli['tic_lugar'] == '' ? 'N/A' : $filacli['tic_lugar']),
                    'correo'       => $filacli['correo'],
                    'patente'      => $filacli['veh_patente'],
                  );

                  enviaemail($datos);
                }
              }

              $devuelve = array('logo' => 'success', 'mensaje' => 'Migrado correctamente');
            } else {
              $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error al migrar la información...');
            }
          }
          /*}else{
                   $devuelve = array('logo'=>'error', 'mensaje'=>'Ha ocurrido un error al migrar la información!','sql'=>$sql2);
               }*/
        } else {
          $devuelve = array('logo' => 'error', 'mensaje' => json_encode($var), 'sql' => "Este error. 1", 'sql2' => json_encode($dataPost), 'opc' => $_REQUEST['opc'], 'otro' => $res, 'query' => $sql);
        }
      }
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error al migrar la información. 1', 'sql' => $sql);
    }

    $sale_a = isset($_REQUEST["retornar"]) ? $_REQUEST["retornar"] : 'no';

    mysqli_close($link);
    mysqli_close($linkclient);
    echo json_encode($devuelve);
    break;

  case 'eliminarTicket':
    /*$sql = "delete from tickets where tic_id='".$_REQUEST["tic_id"]."'";
   $res = $link->query($sql);*/

    $sql = "UPDATE tickets set tic_estado = 4, deleted_at = now(), user_deleted_at = {$_SESSION['cloux_new']} 
            WHERE tic_id = '{$_REQUEST["tic_id"]}'";
    $res = $link->query($sql);
    /*header("Location: http://18.234.82.208/cloux/index.php?menu=tickets&idmenu=100");
   exit;*/
    break;

  case 'updvalorticket':
    $recibe = json_decode($_REQUEST['envio'], true);

    if ($recibe['opc'] == 0) {
      $sql = "UPDATE tickets 
                SET 
                  tic_valortrabajo = '{$recibe['valor']}',
                  tic_valorkm = '{$recibe['valorkm']}',
                  tic_totalkm = '{$recibe['totalkm']}',
                  tic_costolabor = '{$recibe['costolabor']}' 
                WHERE tic_id = {$recibe['ticket']}";
      $res = $link->query($sql);
    } else {
      $sql = "UPDATE tickets 
                SET 
                  tic_um = {$recibe['valor']},
                  tic_valorkm = '{$recibe['valorkm']}',
                  tic_totalkm = '{$recibe['totalkm']}',
                  tic_costolabor = '{$recibe['costolabor']}' 
                WHERE tic_id = {$recibe['ticket']}";
      $res = $link->query($sql);
    }

    if ($res) {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Se ha ingreado el valor correctamente');
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error');
    }

    echo json_encode($devuelve);

    break;

  case 'desinstalarProducto':
    try {
      $dpxv = getPXV($_REQUEST["pxv_id"]);
      $idtecnico = $_REQUEST["tecnico"];

      if ($_REQUEST["tieneserie"] == "SI") {
        $sql = "insert into productosxtecnico(pxt_idtecnico,pxt_cantidad,pxt_idpro,pxt_nserie,pxt_estado,pxt_observaciones,pxt_ideasi,pxt_tipo,pxt_subestado)values(" . $_REQUEST["tecnico"] . "," . $_REQUEST["pxv_cantidad"] . "," . $dpxv["idpro"] . ",'" . $dpxv["serie"] . "'," . $_REQUEST["estado"] . ",'" . $_REQUEST["observaciones"] . "'," . $_REQUEST["ideasi"] . "," . $_REQUEST["tipo"] . "," . $_REQUEST["subestado"] . ")";
        $res = $link->query($sql);
        $sql1 = "delete from productosxvehiculos where pxv_id=" . $_REQUEST["pxv_id"] . "";
        $res1 = $link->query($sql1);
      } else {
        $nspxv = intval($_REQUEST["stockactual"]) - intval($_REQUEST["pxv_cantidad"]);
        if ($nspxv == 0) {
          $sql1 = "delete from productosxvehiculos where pxv_id=" . $_REQUEST["pxv_id"] . "";
          $res1 = $link->query($sql1);
        } else {
          $sql1 = "update productosxvehiculos  set pxv_cantidad = " . $nspxv . " where pxv_id=" . $_REQUEST["pxv_id"] . "";
          $res1 = $link->query($sql1);
        }
        $sql = "select * from productosxtecnico where pxt_idpro=" . $dpxv["idpro"] . " && pxt_estado=" . $_REQUEST["estado"] . " && pxt_idtecnico=" . $idtecnico;
        $res = $link->query($sql);
        $cuenta = mysqli_num_rows($res);
        // echo "nuevo stock de producto en vehiculo => ".$nspxv." => ".$sql1." producto en bodega tecnico => ".$cuenta;
        // return;
        if ($cuenta > 0) {
          // producto existe en bodega tecnico
          $stoctec = obtenervalor("productosxtecnico", "pxt_cantidad", "where pxt_idpro=" . $dpxv["idpro"] . " && pxt_estado=" . $_REQUEST["estado"] . " && pxt_idtecnico=" . $idtecnico . "");
          $nuevostec = intval($stoctec) + intval($_REQUEST["pxv_cantidad"]);
          $sql1 = "update productosxtecnico set pxt_cantidad=" . $nuevostec . " where pxt_idpro=" . $dpxv["idpro"] . " && pxt_estado=" . $_REQUEST["estado"] . " && pxt_idtecnico=" . $idtecnico . "";
          // echo "nuevo stock de producto en vehiculo => ".$nspxv." => ".$sql1." producto en bodega tecnico => ".$cuenta. " => ".$sql1;
          // return;

          $res1 = $link->query($sql1);
        } else {
          // producto no existe en bodega tecnico
          $sql = "insert into productosxtecnico(pxt_idtecnico,pxt_cantidad,pxt_idpro,pxt_nserie,pxt_estado,pxt_observaciones,pxt_ideasi,pxt_tipo,pxt_subestado)values(" . $_REQUEST["tecnico"] . "," . $_REQUEST["pxv_cantidad"] . "," . $dpxv["idpro"] . ",'" . $dpxv["serie"] . "'," . $_REQUEST["estado"] . ",'" . $_REQUEST["observaciones"] . "'," . $_REQUEST["ideasi"] . "," . $_REQUEST["tipo"] . "," . $_REQUEST["subestado"] . ")";
          $res = $link->query($sql);
        }
      }
      $pxv = getProxVeh($dpxv["idveh"]);
      $pxt = getProxTec($_REQUEST["tecnico"]);
      $productos["pxv"] = $pxv;
      $productos["pxt"] = $pxt;
      echo json_encode($productos);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }
    break;

  case 'instalarProducto':
    try {
      $dpxt = getPXT($_REQUEST["pxt_id"]);
      $idtecnico = $_REQUEST["tecnico"];

      if ($_REQUEST["serie"] == "" || $_REQUEST["serie"] == "null" || $_REQUEST["serie"] == null) {
        $serie = $dpxt["serie"];
      } else {
        $serie = $_REQUEST["serie"];
      }
      $cantidad = $_REQUEST["pxt_cantidad"];
      $stock = $_REQUEST["stockactual"] - $cantidad;
      if ($_REQUEST["tieneserie"] == "NO") {
        // si el producto no tiene serie
        // valido si el producto a instalar ya se encuentra instalado
        $sql = "select * from productosxvehiculos where pxv_idveh='" . $_REQUEST["vehiculo"] . "' && pxv_idpro='" . $dpxt["idpro"] . "'";

        $res = $link->query($sql);
        $cuenta = mysqli_num_rows($res);

        if ($cuenta > 0) {
          // producto esta instalado en vehiculo
          $stocveh = obtenervalor("productosxvehiculos", "pxv_cantidad", "where pxv_idpro='" . $dpxt["idpro"] . "' && pxv_idveh='" . $_REQUEST["vehiculo"] . "'");
          $nuevosveh = intval($stocveh) + intval($cantidad);
          $sql1 = "update productosxvehiculos set pxv_cantidad=" . $nuevosveh . " where pxv_idpro='" . $dpxt["idpro"] . "' && pxv_idveh='" . $_REQUEST["vehiculo"] . "'";
          $res1 = $link->query($sql1);
        } else {
          // producto no instalado en vehiculo
          $sql = "insert into productosxvehiculos(pxv_idveh,pxv_cantidad,pxv_idpro,pxv_nserie,pxv_ideasi,pxv_tipo)values('" . $_REQUEST["vehiculo"] . "','" . $cantidad . "','" . $dpxt["idpro"] . "','" . $serie . "'," . $_REQUEST["ideasi"] . "," . $_REQUEST["tipo"] . ")";
          // echo "producto instalado => ".$cuenta."  consulta => ".$sql;
          // return;
          $res = $link->query($sql);
        }

        if ($stock == 0) {
          $sql1 = "delete from productosxtecnico where pxt_id='" . $_REQUEST["pxt_id"] . "'";
          $res1 = $link->query($sql1);
        } else {
          $sql1 = "update productosxtecnico set pxt_cantidad='" . $stock . "' where  pxt_id='" . $_REQUEST["pxt_id"] . "'";
          // echo "stock => ".$stock." => ".$sql1;
          // return;
          $res1 = $link->query($sql1);
        }
      } else {
        // si el producto tiene serie 
        $cantidad = 1;
        $sql1 = "delete from productosxtecnico where pxt_id='" . $_REQUEST["pxt_id"] . "'";
        $res1 = $link->query($sql1);
        $sql = "insert into productosxvehiculos(pxv_idveh,pxv_cantidad,pxv_idpro,pxv_nserie,pxv_ideasi,pxv_tipo)values(" . $_REQUEST["vehiculo"] . "," . $cantidad . "," . $dpxt["idpro"] . ",'" . $serie . "'," . $_REQUEST["ideasi"] . "," . $_REQUEST["tipo"] . ")";
        $res = $link->query($sql);
        //$productos['error']=mysqli_error($link);
      }

      $pxv = getProxVeh($_REQUEST["vehiculo"]);
      $pxt = getProxTec($_REQUEST["tecnico"]);
      $productos["pxv"] = $pxv;
      $productos["pxt"] = $pxt;
      echo json_encode($productos);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }
    break;


    /*****************************************************************************************************
OPERACIONES RR.HH
     ********************************************************************************************************/
  case 'getPersonal':
    $sql = "SELECT * FROM personal where deleted_at is null order by per_apaterno";
    $res = $link->query($sql);
    $personal = array();
    $x = 0;
    while ($fila = mysqli_fetch_array($res)) {
      $x++;
      $personal[] = array("id" => $fila["per_id"], "apaterno" => $fila["per_apaterno"], "amaterno" => $fila["per_amaterno"], "nombres" => $fila["per_nombres"], "nombrecorto" => $fila["per_nombrecorto"], "domicilio" => $fila["per_domicilio"], "celular" => $fila["per_celular"], "correo" => $fila["per_email"], "usuario" => $fila["per_usuario"], "clave" => $fila["per_clave"], "estado" => $fila["per_estado"], "region" => $fila["per_region"], "comuna" => $fila["per_comuna"]);
    }
    echo json_encode($personal);
    break;

  case 'registrarpersonal_bk':
    $sepnombre = explode(" ", $_REQUEST["nombres"]);
    $nombrecorto = $sepnombre[0] . " " . $_REQUEST["apaterno"];
    $sql = "insert into personal(per_apaterno,per_amaterno,per_nombres,per_nombrecorto,per_celular,per_email,per_domicilio,per_usuario,per_clave,per_estado,per_region,per_comuna)values('" . $_REQUEST["apaterno"] . "','" . $_REQUEST["amaterno"] . "','" . $_REQUEST["nombres"] . "','" . $nombrecorto . "','" . $_REQUEST["celular"] . "','" . $_REQUEST["email"] . "','" . $_REQUEST["domicilio"] . "','" . $_REQUEST["usuario"] . "','" . $_REQUEST["clave"] . "',1," . $_REQUEST["region"] . "," . $_REQUEST["comuna"] . ")";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    // if($res){
    //   echo 'Inserción exitosa.';
    // }
    // else{
    //   echo 'Inserción fallida. <br>'.$sql;
    // }
    //echo $sql;
    break;

  case 'registrarpersonal':

    $returnView = array(
      'status' => 'error',
      'title' => 'Verifique campos',
      'message' => 'Datos no han sido posible guardar'
    );

    $recibe = json_decode($_REQUEST['envio'], true);

    if (isset($recibe[0])) {
      $region    = isset($recibe[0]["region"]) ? (trim($recibe[0]["region"]) == '' ? 0 : trim($recibe[0]["region"])) : 0;
      $comuna    = isset($recibe[0]["comuna"]) ? trim($recibe[0]["comuna"]) : 0;
      $apellidoP = isset($recibe[0]["apaterno"]) ? strtoupper(trim($recibe[0]["apaterno"])) : NULL;
      $apellidoM = isset($recibe[0]["amaterno"]) ? strtoupper(trim($recibe[0]["amaterno"])) : NULL;
      $celular   = isset($recibe[0]["celular"]) ? strtoupper(trim($recibe[0]["celular"])) : NULL;
      $email     = isset($recibe[0]["email"]) ? strtoupper(trim($recibe[0]["email"])) : NULL;
      $domicilio = isset($recibe[0]["domicilio"]) ? strtoupper(trim($recibe[0]["domicilio"])) : NULL;
      $usuario   = isset($recibe[0]["usuario"]) ? trim($recibe[0]["usuario"]) : NULL;
      $clave     = isset($recibe[0]["clave"]) ? trim($recibe[0]["clave"]) : NULL;

      $nombres = isset($recibe[0]["nombres"]) ? strtoupper(trim($recibe[0]["nombres"])) : NULL;
      $sepnombre = explode(" ", $nombres);
      $nombrecorto = (isset($sepnombre[0]) ? $sepnombre[0] : NULL) . " " . $apellidoP;

      //si los campos apellidos paternos y nombres vienen vacio enviar error de campos minimos faltantes

      if ($nombres == null && $apellidoP == null) {
        $returnView = array(
          'status' => 'error',
          'title' => 'Verifique campos',
          'message' => 'Datos no han sido posible guardar',
          'nombres' => $nombres,
          'apellidos' => $apellidoP,
          'booleanNombres' => $nombres == null,
          'booleanApellidos' => $apellidoP == null,
        );
      }

      $sql = "insert into 
        personal(per_apaterno,per_amaterno,per_nombres,per_nombrecorto,per_celular,
                per_email,per_domicilio,per_usuario,per_clave,per_estado,
                per_region,per_comuna)
        values('" . $apellidoP . "','" . $apellidoM . "','" . $nombres . "','" . $nombrecorto . "','" . $celular . "'
            ,'" . $email . "','" . $domicilio . "','" . $usuario . "','" . $clave . "',1
            ," . $region . "," . $comuna . ")";

      $res = $link->query($sql);
      $sale_a = $_REQUEST["retornar"];
      if ($res) {
        $returnView = array(
          'status' => 'success',
          'title' => 'Personal registrado',
          'message' => 'Personal registrado. Guardado correctamente ' . $nombres
        );
        //echo 'Inserción exitosa.';
      } else {
        //echo 'Inserción fallida. <br>'.$sql;
        $returnView = array(
          'status' => 'error',
          'title' => 'Verifique campos',
          'message' => 'Datos no han sido posible guardar, error bd : ' . $sql
        );
      }
      //    echo $sql;

    } else {
    }


    echo json_encode($returnView);
    return;

    break;


  case 'editarpersonal':
    //$sepnombre=explode(" ",$_REQUEST["nombres"]);
    //$nombrecorto=$sepnombre[0]." ".$_REQUEST["apaterno"];

    $region    = isset($_REQUEST["region"]) ? (trim($_REQUEST["region"]) == '' ? 0 : trim($_REQUEST["region"])) : 0;
    $comuna    = isset($_REQUEST["comuna"]) ? trim($_REQUEST["comuna"]) : 0;
    $apellidoP = isset($_REQUEST["apaterno"]) ? strtoupper(trim($_REQUEST["apaterno"])) : NULL;
    $apellidoM = isset($_REQUEST["amaterno"]) ? strtoupper(trim($_REQUEST["amaterno"])) : NULL;
    $celular   = isset($_REQUEST["celular"]) ? strtoupper(trim($_REQUEST["celular"])) : NULL;
    $email     = isset($_REQUEST["email"]) ? strtoupper(trim($_REQUEST["email"])) : NULL;
    $domicilio = isset($_REQUEST["domicilio"]) ? strtoupper(trim($_REQUEST["domicilio"])) : NULL;
    $usuario   = isset($_REQUEST["usuario"]) ? trim($_REQUEST["usuario"]) : NULL;
    $clave     = isset($_REQUEST["clave"]) ? trim($_REQUEST["clave"]) : NULL;

    $nombres = isset($_REQUEST["nombres"]) ? strtoupper(trim($_REQUEST["nombres"])) : NULL;
    $sepnombre = explode(" ", $nombres);
    $nombrecorto = (isset($sepnombre[0]) ? $sepnombre[0] : NULL) . " " . $apellidoP;

    $sql2 = "insert into 
  personal(per_apaterno,per_amaterno,per_nombres,per_nombrecorto,per_celular,
          per_email,per_domicilio,per_usuario,per_clave,per_estado,
          per_region,per_comuna)
  values('" . $apellidoP . "','" . $apellidoM . "','" . $nombres . "','" . $nombrecorto . "','" . $celular . "'
      ,'" . $email . "','" . $domicilio . "','" . $usuario . "','" . $clave . "',1
      ," . $region . "," . $comuna . ")";

    $sql = "UPDATE personal SET 
          per_apaterno='" . $apellidoP . "',
          per_amaterno='" . $apellidoM . "',
          per_nombres='" . $nombres . "',
          per_nombrecorto='" . $nombrecorto . "',
          per_celular='" . $celular . "',
          per_email='" . $email . "',
          per_domicilio='" . $domicilio . "',
          per_usuario='" . $usuario . "',
          per_clave='" . $clave . "',
          per_region=" . $region . ",
          per_comuna=" . $comuna . " 
        WHERE per_id='" . $_REQUEST["idpersonal"] . "'";
    $res = $link->query($sql);
    $sale_a = $_REQUEST["retornar"];
    break;

  case 'cambiarestadoPersonal':
    $sql = "update personal set per_estado ='" . $_REQUEST["idestado"] . "' where per_id='" . $_REQUEST["id"] . "'";
    $res = $link->query($sql);
    break;
  case 'eliminarpersonal':
    //$sql="delete from personal where per_id='".$_REQUEST["idper"]."'";

    //se aplica eliminado suave
    $sql = "update personal set deleted_at=now() where per_id='" . $_REQUEST["idper"] . "'";
    $res = $link->query($sql);
    break;

  case 'eliminarusuario':
    $sql = "delete from usuarios where usu_id=" . $_REQUEST["idusuario"];
    $res = $link->query($sql);
    if ($res) {
      $response['status'] = "OK";
    } else {
      $response['status'] = "ERROR";
    }
    echo json_encode($response);
    break;

  case 'getPlantillaGuiaEntrada':
    $sql1 = "SELECT * FROM proveedores";
    $res1 = $link->query($sql1);
    $proveedores = array();
    $contador = 0;
    while ($fila1 = mysqli_fetch_array($res1)) {
      $proveedores[$contador] = $fila1['razonsocial'];
      $contador++;
    }

    $sql2 = "SELECT * FROM productos";
    $res2 = $link->query($sql2);
    $productos = array();
    $contador = 0;
    while ($fila2 = mysqli_fetch_array($res2)) {
      //$productos[$contador] = "(".$fila['pro_codigo'].") ".$fila['pro_nombre'];
      $productos[$contador] = "(" . $fila2['pro_codigo'] . ") " . $fila2['pro_nombre'];
      $contador++;
    }
    try {
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("Cloux");
      $objPHPExcel->getProperties()->setLastModifiedBy("CLOUX");
      $objPHPExcel->getProperties()->setTitle("Plantilla_guia");
      $objPHPExcel->getProperties()->setSubject("Plantilla_guia");
      $objPHPExcel->getProperties()->setDescription("Plantilla_guia");
      $objPHPExcel->setActiveSheetIndex(0);


      $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Proveedor");
      $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Producto");
      $objPHPExcel->getActiveSheet()->SetCellValue('C1', "Serie");
      $objPHPExcel->getActiveSheet()->SetCellValue('D1', "Cantidad");
      $objPHPExcel->getActiveSheet()->SetCellValue('E1', "Valor");
      $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
      $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);

      $index = 1;
      for ($i = 0; $i < count($productos); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('X' . ($index), $productos[$i]);
        $index++;
      }

      for ($i = 2; $i <= 100; $i++) {

        $objValidation = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getDataValidation();
        $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setPromptTitle('Seleccione proveedor');
        $objValidation->setPrompt('Seleccione un proveedor a usar.');
        $objValidation->setErrorTitle('Error de entrada');
        $objValidation->setError('El proveedor no está en la lista');
        $objValidation->setFormula1('"' . implode(',', $proveedores) . '"');
        unset($objValidation);

        $objValidation1 = $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getDataValidation();
        $objValidation1->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
        $objValidation1->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation1->setAllowBlank(false);
        $objValidation1->setShowInputMessage(true);
        $objValidation1->setShowDropDown(true);
        $objValidation1->setPromptTitle('Seleccione producto');
        $objValidation1->setPrompt('Seleccione un producto a usar.');
        $objValidation1->setErrorTitle('Error de entrada');
        $objValidation1->setError('El producto no está en la lista');
        $objValidation1->setFormula1('=\'Plantilla_guia\'!$X$1:$X$' . count($productos));
        unset($objValidation1);
      }

      $objPHPExcel->getActiveSheet()->setTitle('Plantilla_guia');
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      ob_start();
      $objWriter->save("php://output");
      $xlsData = ob_get_contents();
      ob_end_clean();
      $response =  array(
        'op' => 'ok',
        'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
      );
      echo json_encode($response);

      /*$objPHPExcel->getActiveSheet()->setTitle('Plantilla_guia');
    $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Plantilla-guia-entrada.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
    header ('Cache-Control: cache, must-revalidate'); 
    header ('Pragma: public');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');*/
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }
    // $rutadescarga = "archivos/plantillaguiaentrada.xlsx?".date("U")."";
    // echo $rutadescarga;
    break;

  case 'nuevaguia':

    $total = 0;
    $productos = json_decode($_REQUEST["productos"], true);
    foreach ($productos as $key => $valor) {
      $sep = explode("|", $valor);
      $total += $sep[3];
    }

    if ($_REQUEST['inputfecha'] == '') {
      $_REQUEST['inputfecha'] = 'NULL';
    } else {
      $_REQUEST['inputfecha'] = "'" . $_REQUEST['inputfecha'] . "'";
    }

    $sql = "INSERT INTO guiaentrada(gui_bodega, gui_numero, gui_concepto, gui_fecha, gui_estadoguia, gui_desc, gui_proveedor, gui_factura, gui_total, gui_bodegausr) 
            VALUES ({$_REQUEST['inputbodega']}, '{$_REQUEST['inputnumero']}', '{$_REQUEST['inputconcepto']}', {$_REQUEST['inputfecha']}, '{$_REQUEST['inputestado']}', 
                    '{$_REQUEST['inputdesc']}', '{$_REQUEST['inputproveedor']}', '{$_REQUEST['inputfactura']}', {$total}, 26)";
    //echo $sql;exit;
    $res    = $link->query($sql);
    $idguia = $link->insert_id;
    $cont   = 0;
    $fecha  = date("Y-m-d");
    $correcto   = 0;
    $incorrecto = 0;
    $serincorre = '';
    if ($res) {
      $pro = array();
      $ind = 0;
      foreach ($productos as $key => $valor) {
        $sep    = explode("|", $valor);
        $series = explode(',', $sep[6]);
        $precioProd = $sep[3]==null||$sep[3]=='undefined'?0:$sep[3];
        $sql5   = "UPDATE productos SET pro_valor='{$precioProd}',  pro_stock = (select pro_stock from productos where pro_id = {$sep[1]}) + {$sep[2]} WHERE pro_id = {$sep[1]}";
        $res5   = $link->query($sql5);


        $paratraer = '';
        foreach ($series as $keyexplo => $datexplo) {
          $paratraer .= "'" . $datexplo . "'" . ',';
        }
        $myString = substr($paratraer, 0, -1);

        /*$curl = curl_init();
                  curl_setopt_array($curl, array(
                     CURLOPT_URL => 'http://54.90.162.240/api/v1/searchimei',
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_ENCODING => '',
                     CURLOPT_MAXREDIRS => 10,
                     CURLOPT_TIMEOUT => 0,
                     CURLOPT_FOLLOWLOCATION => true,
                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                     CURLOPT_CUSTOMREQUEST => 'POST',
                     CURLOPT_POSTFIELDS => array('imei' => "{$myString}"),
                     CURLOPT_HTTPHEADER => array(
                        'Authorization: 202cb962ac59075b964b07152d234b70'
                 ),
          ));

          $respvehiculo = curl_exec($curl);
          curl_close($curl);
          $varveh = json_decode($respvehiculo,true);*/

        if ($sep[7] == 1) {


          foreach ($series as $keys) {

            $pasa = true;
            /*if($varveh['imei']!='Sin datos.'){
                foreach($varveh['imei'] as $keydatime=>$dataimei){
                    if($dataimei['imei']==$keys){
                        $pasa = false;
                    }
                }
             }*/

            if ($pasa) {
              $sql4  = "select * from serie_guia where ser_codigo = '{$keys}' and ser_estado = 1";
              $res4  = $link->query($sql4);
              $fila4 = mysqli_fetch_array($res4);

              if ($fila4['ser_id'] != '') {
                $incorrecto++;
                $serincorre .= $fila4['ser_codigo'] . ',';

                $sql222 = "DELETE FROM guiaentrada WHERE gui_id = {$idguia}";
                $res222 = $link->query($sql222);

                $sql223 = "DELETE FROM serie_guia WHERE gui_id = {$idguia}";
                $res223 = $link->query($sql223);
              } else {
                $sql2 = "INSERT INTO serie_guia(gui_id, pro_id, ser_neto, ser_fecha,ser_estado,usu_id_ingresa,ser_codigo, prov_id,usu_id_cargo, ser_condicion) VALUES ({$idguia},{$sep[1]},{$sep[3]},'{$fecha}',1,{$_SESSION['cloux_new']},'{$keys}',{$sep[0]},26,1)";
                $res2 = $link->query($sql2);
                $correcto++;
              }
            } else {
              $incorrecto++;
              $serincorre .= $keys . ',';

              $sql222 = "DELETE FROM guiaentrada WHERE gui_id = {$idguia}";
              $res222 = $link->query($sql222);

              $sql223 = "DELETE FROM serie_guia WHERE gui_id = {$idguia}";
              $res223 = $link->query($sql223);
            }
          }
        } else {

          if ($sep[1] > 0) {
            $sql2  = "select * from productos where pro_id = {$sep[1]}";
            $res2  = $link->query($sql2);
            $fila2 = mysqli_fetch_array($res2);
            $series = explode(',', $sep[6]);
            if ($fila2['pro_serie'] == 1) {

              foreach ($series as $keys) {

                $pasa = true;
                /*if($varveh['imei']!='Sin datos.'){
                        foreach($varveh['imei'] as $keydatime=>$dataimei){
                            if($dataimei['imei']==$keys){
                                $pasa = false;
                            }
                        }
                     }*/

                if ($pasa) {
                  $sql5  = "select * from serie_guia where ser_codigo = '{$keys}' and ser_estado = 1";
                  $res5  = $link->query($sql5);
                  $fila5 = mysqli_fetch_array($res5);


                  if ($fila4['ser_id'] != '') {
                    $incorrecto++;
                    $serincorre .= $fila4['ser_codigo'] . ',';

                    $sql222 = "DELETE FROM guiaentrada WHERE gui_id = {$idguia}";
                    $res222 = $link->query($sql222);

                    $sql223 = "DELETE FROM serie_guia WHERE gui_id = {$idguia}";
                    $res223 = $link->query($sql223);
                  } else {
                    $sql5 = "INSERT INTO serie_guia(gui_id, pro_id, ser_neto, ser_fecha,ser_estado,usu_id_ingresa,ser_codigo, prov_id,usu_id_cargo, ser_condicion) VALUES ({$idguia},{$sep[1]},{$sep[3]},'{$fecha}',1,{$_SESSION['cloux_new']},'{$keys}',{$sep[0]},26,1)";
                    $res5 = $link->query($sql5);
                    $correcto++;
                  }
                } else {
                  $incorrecto++;
                  $serincorre .= $keys . ',';

                  $sql222 = "DELETE FROM guiaentrada WHERE gui_id = {$idguia}";
                  $res222 = $link->query($sql222);

                  $sql223 = "DELETE FROM serie_guia WHERE gui_id = {$idguia}";
                  $res223 = $link->query($sql223);
                }
              }
            } else {
              for ($i = 0; $i < $sep[2]; $i++) {

                $sql2 = "INSERT INTO serie_guia(gui_id, pro_id, ser_neto, ser_fecha,ser_estado,usu_id_ingresa,ser_codigo, prov_id,usu_id_cargo, ser_condicion) VALUES ({$idguia},{$sep[1]},{$sep[3]},'{$fecha}',1,{$_SESSION['cloux_new']},'',{$sep[0]},26,1)";
                $res2 = $link->query($sql2);
                if ($res2) {
                  $correcto++;
                } else {
                  $incorrecto++;
                  $sql222 = "DELETE FROM guiaentrada WHERE gui_id = {$idguia}";
                  $res222 = $link->query($sql222);

                  $sql223 = "DELETE FROM serie_guia WHERE gui_id = {$idguia}";
                  $res223 = $link->query($sql223);
                }
              }
            }
          } else {
            for ($i = 0; $i < $sep[2]; $i++) {
              $sql2 = "INSERT INTO serie_guia(gui_id, pro_id, ser_neto, ser_fecha,ser_estado,usu_id_ingresa,ser_codigo, prov_id,usu_id_cargo, ser_condicion) VALUES ({$idguia},{$sep[1]},{$sep[3]},'{$fecha}',1,{$_SESSION['cloux_new']},'',{$sep[0]},26,1)";
              $res2 = $link->query($sql2);
              if ($res2) {
                $correcto++;
              } else {
                $incorrecto++;
                $sql222 = "DELETE FROM guiaentrada WHERE gui_id = {$idguia}";
                $res222 = $link->query($sql222);

                $sql222 = "DELETE FROM serie_guia WHERE gui_id = {$idguia}";
                $res222 = $link->query($sql222);
              }
            }
          }
        }

        $pro[$ind] = (int)$sep[1];
        $ind++;
      }
      array_unique($pro, SORT_NUMERIC);

      $sql3 = "SELECT round( sum(t1.ser_neto*1)*0.19 + sum(t1.ser_neto * 1),0) totalconiva, sum(t1.ser_estado) cantidadtotal
      FROM serie_guia t1 
      WHERE t1.gui_id = {$idguia} and t1.ser_estado = 1";
      $res3 = $link->query($sql3);

      foreach ($res3 as $key3) {
        $sql4 = "UPDATE guiaentrada SET gui_total={$key3['totalconiva']} WHERE gui_id={$idguia}";
        $res4 = $link->query($sql4);
      }
    }

    $sql5  = "select * from serie_guia where gui_id = {$idguia}";
    $res5  = $link->query($sql5);
    $fila5 = mysqli_num_rows($res5);

    if ($fila5 == 0) {
      $sql5  = "delete from guiaentrada where gui_id = {$idguia}";
      $res5  = $link->query($sql5);
      $fila5 = mysqli_fetch_array($res5);
    }

    $mystring = substr($serincorre, 0, -1);

    if ($incorrecto > 0) {
      $sql222 = "DELETE FROM guiaentrada WHERE gui_id = {$idguia}";
      $res222 = $link->query($sql222);

      $sql223 = "DELETE FROM serie_guia WHERE gui_id = {$idguia}";
      $res223 = $link->query($sql223);
    }
    $response['sql']             = $sql5;
    if ($res) {
      $response['status']        = 'OK';
      $response['correcto']      = $correcto;
      $response['incorrecto']    = $incorrecto;
      $response['serincorrecto'] = $mystring;
    } else {
      $response['status']        = 'ERROR';
      $response['correcto']      = $correcto;
      $response['incorrecto']    = $incorrecto;
      $response['serincorrecto'] = $mystring;
    }
    mysqli_close($link);
    echo json_encode($response);

    break;

  case 'importProductosGuia':
    $pdv_fecha     = date("Y-m-d");
    $archivo       = $_FILES['archivoexcel']['name']; // nombre archivo a cargar
    $temporal      = $_FILES['archivoexcel']['tmp_name']; //nombre temporal en equipo cliente
    $guiaproductos = array();

    if ($temporal != "") {
      $codigo = generarCodigo(6);

      $adjunto = $codigo . "_" . $archivo;
      $ruta    = "archivos/guiaentrada/" . $archivo;
      $subido  = move_uploaded_file($temporal, $ruta);
      $excel   = PHPExcel_IOFactory::load($temporal);
      $hoja    = $excel->getActiveSheet()->toArray(null, true, true, true);
      $filas   = 0;
      $i       = 0;

      foreach ($hoja as $indice => $celda) {
        $filas++;
        if ($filas > 1) {

          $campo1 = $celda["A"];
          $campo2 = $celda["B"];
          $campo3 = $celda["C"];
          $campo4 = $celda["D"];
          $campo5 = $celda["E"];

          $nombreproov = explode(')', $campo2);

          $strpro = $nombreproov[1];
          $strpro = ltrim($strpro, ' ');

          if ($campo1 != null) {
            $sql1 = "select id from proveedores where razonsocial like '%{$campo1}%'";
            $res1 = $link->query($sql1);
            $idproveedor = '';
            while ($fila1 = mysqli_fetch_array($res1)) {
              $idproveedor = $fila1['id'];
            }

            //$pro = explode(') ',$campo2)[1];
            $sql2 = "select pro_id from productos where pro_nombre like '%{$strpro}%'";
            $res2 = $link->query($sql2);

            /* echo $sql2;*/
            $idproducto = '';
            while ($fila2 = mysqli_fetch_array($res2)) {
              $idproducto = $fila2['pro_id'];
            }

            //Proveedor,Producto,Serie,Cantidad,Valor

            $iva = 0;
            $total = 0;
            if ($campo5 != null && $campo5 != '') {
              $total = (int)$campo5 * (int)$campo4;
              $iva = (int)((int)$total * 0.19);
            }

            $guiaproductos[] = array('proveedor' => $campo1, 'idproveedor' => $idproveedor, 'producto' => $campo2, 'idproducto' => $idproducto, 'serie' => $campo3, 'cantidad' => $campo4, 'neto' => $campo5, 'iva' => $iva, 'total' => $total);
            $i++;
          }
        }
      }
      echo json_encode($guiaproductos);
    }
    break;

  case 'actualizarOption':
    $sql = "update vehiculos set {$_REQUEST['dataSql']} where veh_id={$_REQUEST['veh_id']} and deleted_at is NULL";
    $res = $link->query($sql);
    if ($res) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
    }

    echo json_encode($response);
    break;

  case 'getDataGeneral':
    $sql = "SELECT * FROM clientes cli LEFT OUTER JOIN gestionclientes gc ON cli.cuenta=gc.gc_cuenta WHERE cli.cuenta != 'copefrut' GROUP BY cli.cuenta ORDER BY cli.cuenta";
    $res = $link->query($sql);
    $data = array();
    $indice = 0;
    while ($fila1 = mysqli_fetch_array($res)) {
      $cant_veh = 0;
      $sql2 = "SELECT * FROM clientes WHERE cuenta='{$fila1['cuenta']}'";
      $res2 = $link->query($sql2);
      while ($fila2 = mysqli_fetch_array($res2)) {
        $sql3 = "SELECT count(*)cant FROM vehiculos WHERE veh_cliente={$fila2['id']} and deleted_at is NULL";
        $res3 = $link->query($sql3);
        while ($fila3 = mysqli_fetch_array($res3)) {
          $cant_veh += $fila3['cant'];
        }
      }
      $datacliente = array();
      $datacliente[] = array("ncampo" => "gc_id", "valor" => $fila1['gc_id'], "html" => '<td id=""><span class="pointer "></span></td>');
      $datacliente[] = array("ncampo" => "gc_idcliente", "valor" => $fila1['gc_idcliente'], "html" => '<td id=""><span class="pointer "></span></td>');
      $datacliente[] = array("ncampo" => "gc_altaflota", "valor" => $fila1['gc_altaflota'], "html" => "<td id='gc_altaflota_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_altaflota')' class='pointer " . ($fila1['gc_altaflota'] === null ? 'na' : ($fila1['gc_altaflota'] === 0 ? 'na' : ($fila1['gc_altaflota'] === 1 ? 'si' : ($fila1['gc_altaflota'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_altaflota'] === null ? 'NA' : ($fila1['gc_altaflota'] === 0 ? 'NA' : ($fila1['gc_altaflota'] === 1 ? 'SI' : ($fila1['gc_altaflota'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_instalacion", "valor" => $fila1['gc_instalacion'], "html" => "<td id='gc_instalacion_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_instalacion')' class='pointer " . ($fila1['gc_instalacion'] === null ? 'na' : ($fila1['gc_instalacion'] === 0 ? 'na' : ($fila1['gc_instalacion'] === 1 ? 'si' : ($fila1['gc_instalacion'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_instalacion'] === null ? 'NA' : ($fila1['gc_instalacion'] === 0 ? 'NA' : ($fila1['gc_instalacion'] === 1 ? 'SI' : ($fila1['gc_instalacion'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_confequipo", "valor" => $fila1['gc_confequipo'], "html" => "<td id='gc_confequipo_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_confequipo')' class='pointer " . ($fila1['gc_confequipo'] === null ? 'na' : ($fila1['gc_confequipo'] === 0 ? 'na' : ($fila1['gc_confequipo'] === 1 ? 'si' : ($fila1['gc_confequipo'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_confequipo'] === null ? 'NA' : ($fila1['gc_confequipo'] === 0 ? 'NA' : ($fila1['gc_confequipo'] === 1 ? 'SI' : ($fila1['gc_confequipo'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_confparmotor", "valor" => $fila1['gc_confparmotor'], "html" => "<td id='gc_confparmotor_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_confparmotor')' class='pointer " . ($fila1['gc_confparmotor'] === null ? 'na' : ($fila1['gc_confparmotor'] === 0 ? 'na' : ($fila1['gc_confparmotor'] === 1 ? 'si' : ($fila1['gc_confparmotor'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_confparmotor'] === null ? 'NA' : ($fila1['gc_confparmotor'] === 0 ? 'NA' : ($fila1['gc_confparmotor'] === 1 ? 'SI' : ($fila1['gc_confparmotor'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_atrayecto", "valor" => $fila1['gc_atrayecto'], "html" => "<td id='gc_atrayecto_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_atrayecto')' class='pointer " . ($fila1['gc_atrayecto'] === null ? 'na' : ($fila1['gc_atrayecto'] === 0 ? 'na' : ($fila1['gc_atrayecto'] === 1 ? 'si' : ($fila1['gc_atrayecto'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_atrayecto'] === null ? 'NA' : ($fila1['gc_atrayecto'] === 0 ? 'NA' : ($fila1['gc_atrayecto'] === 1 ? 'SI' : ($fila1['gc_atrayecto'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_creausuarios", "valor" => $fila1['gc_creausuarios'], "html" => "<td id='gc_creausuarios_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_creausuarios')' class='pointer " . ($fila1['gc_creausuarios'] === null ? 'na' : ($fila1['gc_creausuarios'] === 0 ? 'na' : ($fila1['gc_creausuarios'] === 1 ? 'si' : ($fila1['gc_creausuarios'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_creausuarios'] === null ? 'NA' : ($fila1['gc_creausuarios'] === 0 ? 'NA' : ($fila1['gc_creausuarios'] === 1 ? 'SI' : ($fila1['gc_creausuarios'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_datosmaestros", "valor" => $fila1['gc_datosmaestros'], "html" => "<td id='gc_datosmaestros_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_datosmaestros')' class='pointer " . ($fila1['gc_datosmaestros'] === null ? 'na' : ($fila1['gc_datosmaestros'] === 0 ? 'na' : ($fila1['gc_datosmaestros'] === 1 ? 'si' : ($fila1['gc_datosmaestros'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_datosmaestros'] === null ? 'NA' : ($fila1['gc_datosmaestros'] === 0 ? 'NA' : ($fila1['gc_datosmaestros'] === 1 ? 'SI' : ($fila1['gc_datosmaestros'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_otrasconf", "valor" => $fila1['gc_otrasconf'], "html" => "<td id='gc_otrasconf_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_otrasconf')' class='pointer " . ($fila1['gc_otrasconf'] === null ? 'na' : ($fila1['gc_otrasconf'] === 0 ? 'na' : ($fila1['gc_otrasconf'] === 1 ? 'si' : ($fila1['gc_otrasconf'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_otrasconf'] === null ? 'NA' : ($fila1['gc_otrasconf'] === 0 ? 'NA' : ($fila1['gc_otrasconf'] === 1 ? 'SI' : ($fila1['gc_otrasconf'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_integraciont", "valor" => $fila1['gc_integraciont'], "html" => "<td id='gc_integraciont_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_integraciont')' class='pointer " . ($fila1['gc_integraciont'] === null ? 'na' : ($fila1['gc_integraciont'] === 0 ? 'na' : ($fila1['gc_integraciont'] === 1 ? 'si' : ($fila1['gc_integraciont'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_integraciont'] === null ? 'NA' : ($fila1['gc_integraciont'] === 0 ? 'NA' : ($fila1['gc_integraciont'] === 1 ? 'SI' : ($fila1['gc_integraciont'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_correobienvenida", "valor" => $fila1['gc_correobienvenida'], "html" => "<td id='gc_correobienvenida_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_correobienvenida')' class='pointer " . ($fila1['gc_correobienvenida'] === null ? 'na' : ($fila1['gc_correobienvenida'] === 0 ? 'na' : ($fila1['gc_correobienvenida'] === 1 ? 'si' : ($fila1['gc_correobienvenida'] === 2 ? 'no' : '')))) . "'>" . ($fila1['gc_correobienvenida'] === null ? 'NA' : ($fila1['gc_correobienvenida'] === 0 ? 'NA' : ($fila1['gc_correobienvenida'] === 1 ? 'SI' : ($fila1['gc_correobienvenida'] === 2 ? 'NO' : '')))) . "</span></td>");
      $datacliente[] = array("ncampo" => "gc_cartointerpretacion", "valor" => $fila1['gc_cartointerpretacion'], "html" => "<td id='gc_cartointerpretacion_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_cartointerpretacion')' class='pointer " . ($fila1['gc_cartointerpretacion'] === null ? 'na' : ($fila1['gc_cartointerpretacion'] === 0 ? 'na' : ($fila1['gc_cartointerpretacion'] === 1 ? 'si' : ($fila1['gc_cartointerpretacion'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_vistasfavoritos", "valor" => $fila1['gc_vistasfavoritos'], "html" => "<td id='gc_vistasfavoritos_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_vistasfavoritos')' class='pointer " . ($fila1['gc_vistasfavoritos'] === null ? 'na' : ($fila1['gc_vistasfavoritos'] === 0 ? 'na' : ($fila1['gc_vistasfavoritos'] === 1 ? 'si' : ($fila1['gc_vistasfavoritos'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_rutasxtramos", "valor" => $fila1['gc_rutasxtramos'], "html" => "<td id='gc_rutasxtramos_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_rutasxtramos')' class='pointer " . ($fila1['gc_rutasxtramos'] === null ? 'na' : ($fila1['gc_rutasxtramos'] === 0 ? 'na' : ($fila1['gc_rutasxtramos'] === 1 ? 'si' : ($fila1['gc_rutasxtramos'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_dashboardl", "valor" => $fila1['gc_dashboardl'], "html" => "<td id='gc_dashboardl_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_dashboardl')' class='pointer " . ($fila1['gc_dashboardl'] === null ? 'na' : ($fila1['gc_dashboardl'] === 0 ? 'na' : ($fila1['gc_dashboardl'] === 1 ? 'si' : ($fila1['gc_dashboardl'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_asensores", "valor" => $fila1['gc_asensores'], "html" => "<td id='gc_asensores_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_asensores')' class='pointer " . ($fila1['gc_asensores'] === null ? 'na' : ($fila1['gc_asensores'] === 0 ? 'na' : ($fila1['gc_asensores'] === 1 ? 'si' : ($fila1['gc_asensores'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_egeocercas", "valor" => $fila1['gc_egeocercas'], "html" => "<td id='gc_egeocercas_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_egeocercas')' class='pointer " . ($fila1['gc_egeocercas'] === null ? 'na' : ($fila1['gc_egeocercas'] === 0 ? 'na' : ($fila1['gc_egeocercas'] === 1 ? 'si' : ($fila1['gc_egeocercas'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_trayectos", "valor" => $fila1['gc_trayectos'], "html" => "<td id='gc_trayectos_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_trayectos')' class='pointer " . ($fila1['gc_trayectos'] === null ? 'na' : ($fila1['gc_trayectos'] === 0 ? 'na' : ($fila1['gc_trayectos'] === 1 ? 'si' : ($fila1['gc_trayectos'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_calculoflota", "valor" => $fila1['gc_calculoflota'], "html" => "<td id='gc_calculoflota_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_calculoflota')' class='pointer " . ($fila1['gc_calculoflota'] === null ? 'na' : ($fila1['gc_calculoflota'] === 0 ? 'na' : ($fila1['gc_calculoflota'] === 1 ? 'si' : ($fila1['gc_calculoflota'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_velocidades", "valor" => $fila1['gc_velocidades'], "html" => "<td id='gc_velocidades_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_velocidades')' class='pointer " . ($fila1['gc_velocidades'] === null ? 'na' : ($fila1['gc_velocidades'] === 0 ? 'na' : ($fila1['gc_velocidades'] === 1 ? 'si' : ($fila1['gc_velocidades'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_vxgeocercas", "valor" => $fila1['gc_vxgeocercas'], "html" => "<td id='gc_vxgeocercas_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_vxgeocercas')' class='pointer " . ($fila1['gc_vxgeocercas'] === null ? 'na' : ($fila1['gc_vxgeocercas'] === 0 ? 'na' : ($fila1['gc_vxgeocercas'] === 1 ? 'si' : ($fila1['gc_vxgeocercas'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_avelocidad", "valor" => $fila1['gc_avelocidad'], "html" => "<td id='gc_avelocidad_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_avelocidad')' class='pointer " . ($fila1['gc_avelocidad'] === null ? 'na' : ($fila1['gc_avelocidad'] === 0 ? 'na' : ($fila1['gc_avelocidad'] === 1 ? 'si' : ($fila1['gc_avelocidad'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_panicocorriente", "valor" => $fila1['gc_panicocorriente'], "html" => "<td id='gc_panicocorriente_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_panicocorriente')' class='pointer " . ($fila1['gc_panicocorriente'] === null ? 'na' : ($fila1['gc_panicocorriente'] === 0 ? 'na' : ($fila1['gc_panicocorriente'] === 1 ? 'si' : ($fila1['gc_panicocorriente'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_raccidentes", "valor" => $fila1['gc_raccidentes'], "html" => "<td id='gc_raccidentes_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_raccidentes')' class='pointer " . ($fila1['gc_raccidentes'] === null ? 'na' : ($fila1['gc_raccidentes'] === 0 ? 'na' : ($fila1['gc_raccidentes'] === 1 ? 'si' : ($fila1['gc_raccidentes'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_somnolencia", "valor" => $fila1['gc_somnolencia'], "html" => "<td id='gc_somnolencia_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_somnolencia')' class='pointer " . ($fila1['gc_somnolencia'] === null ? 'na' : ($fila1['gc_somnolencia'] === 0 ? 'na' : ($fila1['gc_somnolencia'] === 1 ? 'si' : ($fila1['gc_somnolencia'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_vpermitido", "valor" => $fila1['gc_vpermitido'], "html" => "<td id='gc_vpermitido_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_vpermitido')' class='pointer " . ($fila1['gc_vpermitido'] === null ? 'na' : ($fila1['gc_vpermitido'] === 0 ? 'na' : ($fila1['gc_vpermitido'] === 1 ? 'si' : ($fila1['gc_vpermitido'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_gestanque", "valor" => $fila1['gc_gestanque'], "html" => "<td id='gc_gestanque_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_gestanque')' class='pointer " . ($fila1['gc_gestanque'] === null ? 'na' : ($fila1['gc_gestanque'] === 0 ? 'na' : ($fila1['gc_gestanque'] === 1 ? 'si' : ($fila1['gc_gestanque'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_iconsumo", "valor" => $fila1['gc_iconsumo'], "html" => "<td id='gc_iconsumo_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_iconsumo')' class='pointer " . ($fila1['gc_iconsumo'] === null ? 'na' : ($fila1['gc_iconsumo'] === 0 ? 'na' : ($fila1['gc_iconsumo'] === 1 ? 'si' : ($fila1['gc_iconsumo'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_ralenti", "valor" => $fila1['gc_ralenti'], "html" => "<td id='gc_ralenti_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_ralenti')' class='pointer " . ($fila1['gc_ralenti'] === null ? 'na' : ($fila1['gc_ralenti'] === 0 ? 'na' : ($fila1['gc_ralenti'] === 1 ? 'si' : ($fila1['gc_ralenti'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_gparametros", "valor" => $fila1['gc_gparametros'], "html" => "<td id='gc_gparametros_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_gparametros')' class='pointer " . ($fila1['gc_gparametros'] === null ? 'na' : ($fila1['gc_gparametros'] === 0 ? 'na' : ($fila1['gc_gparametros'] === 1 ? 'si' : ($fila1['gc_gparametros'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_atrayectos", "valor" => $fila1['gc_atrayectos'], "html" => "<td id='gc_atrayectos_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_atrayectos')' class='pointer " . ($fila1['gc_atrayectos'] === null ? 'na' : ($fila1['gc_atrayectos'] === 0 ? 'na' : ($fila1['gc_atrayectos'] === 1 ? 'si' : ($fila1['gc_atrayectos'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_iparmotor", "valor" => $fila1['gc_iparmotor'], "html" => "<td id='gc_iparmotor_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_iparmotor')' class='pointer " . ($fila1['gc_iparmotor'] === null ? 'na' : ($fila1['gc_iparmotor'] === 0 ? 'na' : ($fila1['gc_iparmotor'] === 1 ? 'si' : ($fila1['gc_iparmotor'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_cconductor", "valor" => $fila1['gc_cconductor'], "html" => "<td id='gc_cconductor_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_cconductor')' class='pointer " . ($fila1['gc_cconductor'] === null ? 'na' : ($fila1['gc_cconductor'] === 0 ? 'na' : ($fila1['gc_cconductor'] === 1 ? 'si' : ($fila1['gc_cconductor'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_dtelemetria", "valor" => $fila1['gc_dtelemetria'], "html" => "<td id='gc_dtelemetria_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_dtelemetria')' class='pointer " . ($fila1['gc_dtelemetria'] === null ? 'na' : ($fila1['gc_dtelemetria'] === 0 ? 'na' : ($fila1['gc_dtelemetria'] === 1 ? 'si' : ($fila1['gc_dtelemetria'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_mpreventivo", "valor" => $fila1['gc_mpreventivo'], "html" => "<td id='gc_mpreventivo_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_mpreventivo')' class='pointer " . ($fila1['gc_mpreventivo'] === null ? 'na' : ($fila1['gc_mpreventivo'] === 0 ? 'na' : ($fila1['gc_mpreventivo'] === 1 ? 'si' : ($fila1['gc_mpreventivo'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_mcorrectivo", "valor" => $fila1['gc_mcorrectivo'], "html" => "<td id='gc_mcorrectivo_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_mcorrectivo')' class='pointer " . ($fila1['gc_mcorrectivo'] === null ? 'na' : ($fila1['gc_mcorrectivo'] === 0 ? 'na' : ($fila1['gc_mcorrectivo'] === 1 ? 'si' : ($fila1['gc_mcorrectivo'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_ftecnicas", "valor" => $fila1['gc_ftecnicas'], "html" => "<td id='gc_ftecnicas_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_ftecnicas')' class='pointer " . ($fila1['gc_ftecnicas'] === null ? 'na' : ($fila1['gc_ftecnicas'] === 0 ? 'na' : ($fila1['gc_ftecnicas'] === 1 ? 'si' : ($fila1['gc_ftecnicas'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_vdocumentos", "valor" => $fila1['gc_vdocumentos'], "html" => "<td id='gc_vdocumentos_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_vdocumentos')' class='pointer " . ($fila1['gc_vdocumentos'] === null ? 'na' : ($fila1['gc_vdocumentos'] === 0 ? 'na' : ($fila1['gc_vdocumentos'] === 1 ? 'si' : ($fila1['gc_vdocumentos'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_rcostos", "valor" => $fila1['gc_rcostos'], "html" => "<td id='gc_rcostos_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_rcostos')' class='pointer " . ($fila1['gc_rcostos'] === null ? 'na' : ($fila1['gc_rcostos'] === 0 ? 'na' : ($fila1['gc_rcostos'] === 1 ? 'si' : ($fila1['gc_rcostos'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_aevelocidad", "valor" => $fila1['gc_aevelocidad'], "html" => "<td id='gc_aevelocidad_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_aevelocidad')' class='pointer " . ($fila1['gc_aevelocidad'] === null ? 'na' : ($fila1['gc_aevelocidad'] === 0 ? 'na' : ($fila1['gc_aevelocidad'] === 1 ? 'si' : ($fila1['gc_aevelocidad'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_avgeocerca", "valor" => $fila1['gc_avgeocerca'], "html" => "<td id='gc_avgeocerca_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_avgeocerca')' class='pointer " . ($fila1['gc_avgeocerca'] === null ? 'na' : ($fila1['gc_avgeocerca'] === 0 ? 'na' : ($fila1['gc_avgeocerca'] === 1 ? 'si' : ($fila1['gc_avgeocerca'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_anpermitido", "valor" => $fila1['gc_anpermitido'], "html" => "<td id='gc_anpermitido_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_anpermitido')' class='pointer " . ($fila1['gc_anpermitido'] === null ? 'na' : ($fila1['gc_anpermitido'] === 0 ? 'na' : ($fila1['gc_anpermitido'] === 1 ? 'si' : ($fila1['gc_anpermitido'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_adestanque", "valor" => $fila1['gc_adestanque'], "html" => "<td id='gc_adestanque_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_adestanque')' class='pointer " . ($fila1['gc_adestanque'] === null ? 'na' : ($fila1['gc_adestanque'] === 0 ? 'na' : ($fila1['gc_adestanque'] === 1 ? 'si' : ($fila1['gc_adestanque'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_aralenti", "valor" => $fila1['gc_aralenti'], "html" => "<td id='gc_aralenti_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_aralenti')' class='pointer " . ($fila1['gc_aralenti'] === null ? 'na' : ($fila1['gc_aralenti'] === 0 ? 'na' : ($fila1['gc_aralenti'] === 1 ? 'si' : ($fila1['gc_aralenti'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_abvbateria", "valor" => $fila1['gc_abvbateria'], "html" => "<td id='gc_abvbateria_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_abvbateria')' class='pointer " . ($fila1['gc_abvbateria'] === null ? 'na' : ($fila1['gc_abvbateria'] === 0 ? 'na' : ($fila1['gc_abvbateria'] === 1 ? 'si' : ($fila1['gc_abvbateria'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_aetmotor", "valor" => $fila1['gc_aetmotor'], "html" => "<td id='gc_aetmotor_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_aetmotor')' class='pointer " . ($fila1['gc_aetmotor'] === null ? 'na' : ($fila1['gc_aetmotor'] === 0 ? 'na' : ($fila1['gc_aetmotor'] === 1 ? 'si' : ($fila1['gc_aetmotor'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_avmrevision", "valor" => $fila1['gc_avmrevision'], "html" => "<td id='gc_avmrevision_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_avmrevision')' class='pointer " . ($fila1['gc_avmrevision'] === null ? 'na' : ($fila1['gc_avmrevision'] === 0 ? 'na' : ($fila1['gc_avmrevision'] === 1 ? 'si' : ($fila1['gc_avmrevision'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_avdocumentos", "valor" => $fila1['gc_avdocumentos'], "html" => "<td id='gc_avdocumentos_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_avdocumentos')' class='pointer " . ($fila1['gc_avdocumentos'] === null ? 'na' : ($fila1['gc_avdocumentos'] === 0 ? 'na' : ($fila1['gc_avdocumentos'] === 1 ? 'si' : ($fila1['gc_avdocumentos'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_updatereg", "valor" => $fila1['gc_updatereg'], "html" => "<td id='gc_updatereg_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_updatereg')' class='pointer " . ($fila1['gc_updatereg'] === null ? 'na' : ($fila1['gc_updatereg'] === 0 ? 'na' : ($fila1['gc_updatereg'] === 1 ? 'si' : ($fila1['gc_updatereg'] === 2 ? 'no' : '')))) . "'></span></td>");
      $datacliente[] = array("ncampo" => "gc_estado", "valor" => $fila1['gc_estado'], "html" => "<td id='gc_estado_" . $indice . "'><span onclick='selectOption(" . $indice . ",'gc_estado')' class='pointer " . ($fila1['gc_estado'] === null ? 'na' : ($fila1['gc_estado'] === 0 ? 'na' : ($fila1['gc_estado'] === 1 ? 'si' : ($fila1['gc_estado'] === 2 ? 'no' : '')))) . "'></span></td>");
      $indice++;
      $data[] = array('idcliente' => $fila1['id'], 'razonsocial' => $fila1['razonsocial'], 'cuenta' => $fila1['cuenta'], 'cantvehiculos' => $cant_veh, 'datacliente' => $datacliente);
    }
    echo json_encode($data);
    break;

  case 'setValorTrabajo':
    $sql = "update tickets set tic_valortrabajo={$_REQUEST['valor']} where tic_id={$_REQUEST['id']}";
    $res = $link->query($sql);
    if ($res) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
    }
    echo json_encode($response);
    break;

  case 'setCentroCosto':
    $sql = "update tickets set tic_centrocosto={$_REQUEST['ccosto']} where tic_id={$_REQUEST['id']}";
    $res = $link->query($sql);
    if ($res) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
    }
    echo json_encode($response);
    break;

  case 'setEstadoFact':
    $sql = "update tickets set tic_estadofact={$_REQUEST['estadofact']} where tic_id={$_REQUEST['id']}";
    $res = $link->query($sql);
    if ($res) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
    }
    echo json_encode($response);
    break;

  case 'setPagoTecnico':
    $sql = "update tickets set 	tic_pagot={$_REQUEST['pagot']} where tic_id={$_REQUEST['id']}";
    $res = $link->query($sql);
    if ($res) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
    }
    echo json_encode($response);
    break;

  case 'actualizarOptionGestionCliente':
    $sqlexiste = "select * from gestionclientes where gc_cuenta='{$_REQUEST['cuenta']}'";
    $resexiste = $link->query($sqlexiste);
    $filaexiste = mysqli_fetch_array($resexiste);

    if ($filaexiste['gc_cuenta'] == null) {
      $campo = explode('=', $_REQUEST['dataSql'])[0];
      $valor = explode('=', $_REQUEST['dataSql'])[1];
      $sql = "insert into gestionclientes(gc_cuenta, {$campo}) values('{$_REQUEST['cuenta']}',{$valor})";
      $res = $link->query($sql);
      if ($res) {
        $response['status'] = 'OK';
      } else {
        $response['status'] = 'ERROR';
        $response['sql'] = $sql;
      }
    } else {
      $sql = "update gestionclientes set {$_REQUEST['dataSql']} where gc_cuenta='{$_REQUEST['cuenta']}'";
      $res = $link->query($sql);
      if ($res) {
        $response['status'] = 'OK';
      } else {
        $response['status'] = 'ERROR';
        $response['sql'] = $sql;
      }
    }


    echo json_encode($response);
    break;

  case 'actualizarOptionGestionCliente1':
    $sqlexiste = "select * from gestionclientes where gc_cuenta='{$_REQUEST['cuenta']}'";
    $resexiste = $link->query($sqlexiste);
    $filaexiste = mysqli_fetch_array($resexiste);

    if ($filaexiste['gc_cuenta'] == null) {
      $campo1 = $_REQUEST['campo1'];
      $campo2 = $_REQUEST['campo2'];
      $valor = $_REQUEST['valor1'];

      $sql = "insert into gestionclientes(gc_cuenta, {$campo1}, {$campo2}) values('{$_REQUEST['cuenta']}',{$valor},now())";
      $res = $link->query($sql);
      if ($res) {
        $response['status'] = 'OK';
      } else {
        $response['status'] = 'ERROR';
        $response['sql'] = $sql;
      }
    } else {
      $sql = "update gestionclientes set {$_REQUEST['dataSql']} where gc_cuenta='{$_REQUEST['cuenta']}'";
      $res = $link->query($sql);
      if ($res) {
        $response['status'] = 'OK';
      } else {
        $response['status'] = 'ERROR';
        $response['sql'] = $sql;
      }
    }


    echo json_encode($response);
    break;

  case 'getDataGeneralContactos':
    $sql = "SELECT * FROM clientes cli LEFT OUTER JOIN gestionclientes gc ON cli.cuenta=gc.gc_cuenta GROUP BY cli.cuenta ORDER BY cli.cuenta";
    $res = $link->query($sql);
    $data = array();
    $indice = 0;
    while ($fila1 = mysqli_fetch_array($res)) {
      $cant_veh = 0;
      $sql2 = "SELECT * FROM clientes WHERE cuenta='{$fila1['cuenta']}'";
      $res2 = $link->query($sql2);
      while ($fila2 = mysqli_fetch_array($res2)) {
        $sql3 = "SELECT count(*)cant FROM vehiculos WHERE veh_cliente={$fila2['id']} and deleted_at is NULL";
        $res3 = $link->query($sql3);
        while ($fila3 = mysqli_fetch_array($res3)) {
          $cant_veh += $fila3['cant'];
        }
      }
      $datacliente = array();
      $datacliente[] = array('ncampo' => 'gc_motivo1', 'valor' => $fila1['gc_motivo1']);
      $datacliente[] = array('ncampo' => 'gc_fechahora1', 'valor' => $fila1['gc_fechahora1']);
      $datacliente[] = array('ncampo' => 'gc_contador1', 'valor' => ($fila1['gc_fechahora1'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora1'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo2', 'valor' => $fila1['gc_motivo2']);
      $datacliente[] = array('ncampo' => 'gc_fechahora2', 'valor' => $fila1['gc_fechahora2']);
      $datacliente[] = array('ncampo' => 'gc_contador2', 'valor' => ($fila1['gc_fechahora2'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora2'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo3', 'valor' => $fila1['gc_motivo3']);
      $datacliente[] = array('ncampo' => 'gc_fechahora3', 'valor' => $fila1['gc_fechahora3']);
      $datacliente[] = array('ncampo' => 'gc_contador3', 'valor' => ($fila1['gc_fechahora3'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora3'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo4', 'valor' => $fila1['gc_motivo4']);
      $datacliente[] = array('ncampo' => 'gc_fechahora4', 'valor' => $fila1['gc_fechahora4']);
      $datacliente[] = array('ncampo' => 'gc_contador4', 'valor' => ($fila1['gc_fechahora4'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora4'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo5', 'valor' => $fila1['gc_motivo5']);
      $datacliente[] = array('ncampo' => 'gc_fechahora5', 'valor' => $fila1['gc_fechahora5']);
      $datacliente[] = array('ncampo' => 'gc_contador5', 'valor' => ($fila1['gc_fechahora5'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora5'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo6', 'valor' => $fila1['gc_motivo6']);
      $datacliente[] = array('ncampo' => 'gc_fechahora6', 'valor' => $fila1['gc_fechahora6']);
      $datacliente[] = array('ncampo' => 'gc_contador6', 'valor' => ($fila1['gc_fechahora6'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora6'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo7', 'valor' => $fila1['gc_motivo7']);
      $datacliente[] = array('ncampo' => 'gc_fechahora7', 'valor' => $fila1['gc_fechahora7']);
      $datacliente[] = array('ncampo' => 'gc_contador7', 'valor' => ($fila1['gc_fechahora7'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora7'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo8', 'valor' => $fila1['gc_motivo8']);
      $datacliente[] = array('ncampo' => 'gc_fechahora8', 'valor' => $fila1['gc_fechahora8']);
      $datacliente[] = array('ncampo' => 'gc_contador8', 'valor' => ($fila1['gc_fechahora8'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora8'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo9', 'valor' => $fila1['gc_motivo9']);
      $datacliente[] = array('ncampo' => 'gc_fechahora9', 'valor' => $fila1['gc_fechahora9']);
      $datacliente[] = array('ncampo' => 'gc_contador9', 'valor' => ($fila1['gc_fechahora9'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora9'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo10', 'valor' => $fila1['gc_motivo10']);
      $datacliente[] = array('ncampo' => 'gc_fechahora10', 'valor' => $fila1['gc_fechahora10']);
      $datacliente[] = array('ncampo' => 'gc_contador10', 'valor' => ($fila1['gc_fechahora10'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora10'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo11', 'valor' => $fila1['gc_motivo11']);
      $datacliente[] = array('ncampo' => 'gc_fechahora11', 'valor' => $fila1['gc_fechahora11']);
      $datacliente[] = array('ncampo' => 'gc_contador11', 'valor' => ($fila1['gc_fechahora11'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora11'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo12', 'valor' => $fila1['gc_motivo12']);
      $datacliente[] = array('ncampo' => 'gc_fechahora12', 'valor' => $fila1['gc_fechahora12']);
      $datacliente[] = array('ncampo' => 'gc_contador12', 'valor' => ($fila1['gc_fechahora12'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora12'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo13', 'valor' => $fila1['gc_motivo13']);
      $datacliente[] = array('ncampo' => 'gc_fechahora13', 'valor' => $fila1['gc_fechahora13']);
      $datacliente[] = array('ncampo' => 'gc_contador13', 'valor' => ($fila1['gc_fechahora13'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora13'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo14', 'valor' => $fila1['gc_motivo14']);
      $datacliente[] = array('ncampo' => 'gc_fechahora14', 'valor' => $fila1['gc_fechahora14']);
      $datacliente[] = array('ncampo' => 'gc_contador14', 'valor' => ($fila1['gc_fechahora14'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora14'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo15', 'valor' => $fila1['gc_motivo15']);
      $datacliente[] = array('ncampo' => 'gc_fechahora15', 'valor' => $fila1['gc_fechahora15']);
      $datacliente[] = array('ncampo' => 'gc_contador15', 'valor' => ($fila1['gc_fechahora15'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora15'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo16', 'valor' => $fila1['gc_motivo16']);
      $datacliente[] = array('ncampo' => 'gc_fechahora16', 'valor' => $fila1['gc_fechahora16']);
      $datacliente[] = array('ncampo' => 'gc_contador16', 'valor' => ($fila1['gc_fechahora16'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora16'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo17', 'valor' => $fila1['gc_motivo17']);
      $datacliente[] = array('ncampo' => 'gc_fechahora17', 'valor' => $fila1['gc_fechahora17']);
      $datacliente[] = array('ncampo' => 'gc_contador17', 'valor' => ($fila1['gc_fechahora17'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora17'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo18', 'valor' => $fila1['gc_motivo18']);
      $datacliente[] = array('ncampo' => 'gc_fechahora18', 'valor' => $fila1['gc_fechahora18']);
      $datacliente[] = array('ncampo' => 'gc_contador18', 'valor' => ($fila1['gc_fechahora18'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora18'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo19', 'valor' => $fila1['gc_motivo19']);
      $datacliente[] = array('ncampo' => 'gc_fechahora19', 'valor' => $fila1['gc_fechahora19']);
      $datacliente[] = array('ncampo' => 'gc_contador19', 'valor' => ($fila1['gc_fechahora19'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora19'])))->format('%a')));
      $datacliente[] = array('ncampo' => 'gc_motivo20', 'valor' => $fila1['gc_motivo20']);
      $datacliente[] = array('ncampo' => 'gc_fechahora20', 'valor' => $fila1['gc_fechahora20']);
      $datacliente[] = array('ncampo' => 'gc_contador20', 'valor' => ($fila1['gc_fechahora20'] == null ? 0 : (new DateTime(date("Y-m-d H:i:s")))->diff((new DateTime($fila1['gc_fechahora20'])))->format('%a')));
      $indice++;
      $data[] = array('idcliente' => $fila1['id'], 'razonsocial' => $fila1['razonsocial'], 'cuenta' => $fila1['cuenta'], 'cantvehiculos' => $cant_veh, 'datacliente' => $datacliente);
    }
    echo json_encode($data);
    break;

  case 'postVehiculos':
    try {
      $data = array();
      $pdv_fecha = date("Y-m-d");
      $archivo = $_FILES['archivo']['name']; // nombre archivo a cargar
      $temporal = $_FILES['archivo']['tmp_name']; //nombre temporal en equipo cliente
      $vehiculos = array();
      if ($temporal != "") {
        $codigo = generarCodigo(6);
        //$adjunto=$_REQUEST["codigo"]."_".$archivo;
        $adjunto = $codigo . "_" . $archivo;
        $ruta = "archivos/" . $archivo;
        move_uploaded_file($temporal, $ruta); //arhivo válido, es cargado en el servidor
        $excel = PHPExcel_IOFactory::load($temporal);
        $hoja = $excel->getActiveSheet()->toArray(null, true, true, true);
        $filas = 0;
        $i = 0;

        foreach ($hoja as $indice => $celda) {
          if ($filas > 0) {
            $tipo = $celda["A"];
            $gps = $celda["B"];
            $cuenta = $celda["C"];
            $rsocial = $celda["D"];
            $grupo = $celda["E"];
            $region = $celda["F"];
            $comuna = $celda["G"];
            $patente = $celda["H"];
            $dispositivo = $celda["I"];
            $tservicio = $celda["J"];
            $contacto = $celda["K"];
            $celular = $celda["L"];
            if ($patente != null && $patente != '') {
              $sql = "select veh_id from vehiculos where veh_patente like '%{$patente}%' and deleted_at is NULL";
              $res = $link->query($sql);
              $fila = mysqli_fetch_array($res);
              if ($fila['veh_id'] == null) {
                $idtipo = 0;
                $idgps = 0;
                $idcliente = 0;
                $idgrupo = 0;
                $idregion = 0;
                $idcomuna = 0;
                $iddispositivo = 0;
                $idtservicio = 0;

                $sql1 = "SELECT * FROM tiposdevehiculos where tveh_nombre='{$tipo}' LIMIT 1";
                $res1 = $link->query($sql1);
                while ($fila1 = mysqli_fetch_array($res1)) {
                  $idtipo = $fila1['tveh_id'];
                }

                if ($gps == 'BÁSICO' || $gps == 'BASICO' || $gps == 'basico') {
                  $idgps = 1;
                } else if ($gps == 'CANBUS' || $gps == 'canbus') {
                  $idgps = 2;
                } else if ($gps == 'temperatura' || $gps == 'TEMPERATURA') {
                  $idgps = 3;
                }

                $sql2 = "SELECT * FROM `clientes` WHERE razonsocial='{$rsocial}' LIMIT 1";
                $res2 = $link->query($sql2);
                while ($fila2 = mysqli_fetch_array($res2)) {
                  $idcliente = $fila2['id'];
                }

                $sql3 = "SELECT * FROM `grupos` WHERE gru_nombre='{$grupo}' LIMIT 1";
                $res3 = $link->query($sql3);
                while ($fila3 = mysqli_fetch_array($res3)) {
                  $idgrupo = $fila3['gru_id'];
                }

                $sql4 = "SELECT * FROM regiones reg LEFT OUTER JOIN provincias pro ON pro.region_id=reg.id LEFT OUTER JOIN comunas com ON com.provincia_id=pro.provincia_id where com.comuna_nombre='{$comuna}' LIMIT 1";
                $res4 = $link->query($sql4);
                while ($fila4 = mysqli_fetch_array($res4)) {
                  $idregion = $fila4['id'];
                  $idcomuna = $fila4['comuna_id'];
                }

                $sql5 = "SELECT * FROM tiposdedispositivos WHERE tdi_nombre='{$dispositivo}' LIMIT 1";
                $res5 = $link->query($sql5);
                while ($fila5 = mysqli_fetch_array($res5)) {
                  $iddispositivo = $fila5['tdi_id'];
                }

                if ($tservicio == 'ESTANDAR' || $tservicio == 'estandar') {
                  $idtservicio = 3;
                } else if ($tservicio == 'basico' || $tservicio == 'básico' || $tservicio == 'BÁSICO') {
                  $idtservicio = 1;
                } else if ($tservicio == 'avanzado' || $tservicio == 'AVANZADO') {
                  $idtservicio = 2;
                }

                $sqlInsert = "insert into vehiculos(veh_idflotasnet,veh_tipo,veh_gps,veh_cliente,veh_rsocial,veh_grupo,veh_patente,veh_contacto,veh_celular,veh_dispositivo,veh_tservicio,veh_estado, veh_observacion,veh_ultimaposicion, veh_localidad, veh_alerta, veh_seriegps, veh_region, veh_comuna)values(0,{$idtipo},{$idgps},{$idcliente},{$idcliente},{$idgrupo},'{$patente}','{$contacto}','{$celular}',{$iddispositivo},{$idtservicio},0,0,now(),'', 0,'',{$idregion},{$idcomuna})";
                $resInsert = $link->query($sqlInsert);
                $idveh = $link->insert_id;

                $sqlInsert1 = "INSERT INTO tickets(tic_cliente, tic_patente, tic_dispositivo, tic_tipotrabajo, tic_tiposervicio, tic_contacto, tic_celular, tic_lugar, tic_descripcion, tic_nserie, tic_estado) VALUES ({$idcliente},{$fila['veh_id']},{$iddispositivo},0,{$idtservicio},'{$contacto}','{$celular}','','','',1)";
                $resInsert1 = $link->query($sqlInsert1);
                $idticket = $link->insert_id;

                $vehiculos[] = array('id' => $idveh, 'idticket' => $idticket, 'tipo' => $tipo, 'gps' => $gps, 'cuenta' => $cuenta, 'rsocial' => $rsocial, 'grupo' => $grupo, 'region' => $region, 'comuna' => $comuna, 'patente' => $patente, 'dispositivo' => $dispositivo, 'tservicio' => $tservicio, 'contacto' => $contacto, 'celular' => $celular);
              }
            }
          }
          $filas++;
        }
      }
      echo json_encode($vehiculos);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }
    break;

  case 'getPlantillaVehiculos':
    $sql1 = "SELECT * FROM regiones ORDER by ordinal";
    $res1 = $link->query($sql1);
    $regiones = array();
    $contador = 0;
    while ($fila1 = mysqli_fetch_array($res1)) {
      //$productos[$contador] = "(".$fila['pro_codigo'].") ".$fila['pro_nombre'];
      $regiones[$contador] = $fila1['ordinal'] . ' ' . $fila1['region'];
      $contador++;
    }

    $sql2 = "SELECT * FROM `comunas` ORDER by provincia_id";
    $res2 = $link->query($sql2);
    $comunas = array();
    $contador = 0;
    while ($fila2 = mysqli_fetch_array($res2)) {
      //$productos[$contador] = "(".$fila['pro_codigo'].") ".$fila['pro_nombre'];
      $comunas[$contador] = $fila2['comuna_nombre'];
      $contador++;
    }

    try {
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("Cloux");
      $objPHPExcel->getProperties()->setLastModifiedBy("CLOUX");
      $objPHPExcel->getProperties()->setTitle("Plantilla_Vehiculos");
      $objPHPExcel->getProperties()->setSubject("Plantilla Vehiculos");
      $objPHPExcel->getProperties()->setDescription("Plantilla Vehiculos");
      $objPHPExcel->setActiveSheetIndex(0);


      $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Tipo");
      $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Gps");
      $objPHPExcel->getActiveSheet()->SetCellValue('C1', "Cuenta");
      $objPHPExcel->getActiveSheet()->SetCellValue('D1', "Razón social");
      $objPHPExcel->getActiveSheet()->SetCellValue('E1', "Grupo");
      $objPHPExcel->getActiveSheet()->SetCellValue('F1', "Región");
      $objPHPExcel->getActiveSheet()->SetCellValue('G1', "Comuna");
      $objPHPExcel->getActiveSheet()->SetCellValue('H1', "Patente");
      $objPHPExcel->getActiveSheet()->SetCellValue('I1', "Dispositivo");
      $objPHPExcel->getActiveSheet()->SetCellValue('J1', "Tipo servicio");
      $objPHPExcel->getActiveSheet()->SetCellValue('K1', "Contacto");
      $objPHPExcel->getActiveSheet()->SetCellValue('L1', "Celular");
      $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
      $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

      $index = 1;
      for ($i = 0; $i < count($comunas); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('Z' . ($index + 1), $comunas[$i]);
        $index++;
      }

      $index = 1;
      for ($i = 0; $i < count($regiones); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('AA' . ($index + 1), $regiones[$i]);
        $index++;
      }

      for ($i = 2; $i <= 100; $i++) {

        $objValidation = $objPHPExcel->getActiveSheet()->getCell('F' . $i)->getDataValidation();
        $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setPromptTitle('Seleccione región');
        $objValidation->setPrompt('Seleccione una región a usar.');
        $objValidation->setErrorTitle('Error de entrada');
        $objValidation->setError('La región no está en la lista');
        //$objValidation->setFormula1('"'.implode(',', $regiones).'"');
        $objValidation->setFormula1('=\'Plantilla_Vehiculos\'!$AA$2:$AA$' . count($regiones));
        unset($objValidation);

        $objValidation1 = $objPHPExcel->getActiveSheet()->getCell('G' . $i)->getDataValidation();
        $objValidation1->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
        $objValidation1->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation1->setAllowBlank(false);
        $objValidation1->setShowInputMessage(true);
        $objValidation1->setShowDropDown(true);
        $objValidation1->setPromptTitle('Seleccione comuna');
        $objValidation1->setPrompt('Seleccione un comuna a usar.');
        $objValidation1->setErrorTitle('Error de entrada');
        $objValidation1->setError('El comuna no está en la lista');
        $objValidation1->setFormula1('=\'Plantilla_Vehiculos\'!$Z$2:$Z$' . count($comunas));
        unset($objValidation1);
      }
      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle('Plantilla_Vehiculos');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="Plantilla-Vehiculos.xlsx"');
      header('Cache-Control: max-age=0');
      header('Cache-Control: max-age=1');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
      header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header('Pragma: public'); // HTTP/1.0
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }
    // $rutadescarga = "archivos/plantillavehiculos.xlsx?".date("U")."";
    // echo $rutadescarga;
    break;

  case 'getRsocialxPatente':
    $sql = "SELECT cli.id,cli.razonsocial,veh.* FROM vehiculos veh LEFT OUTER JOIN clientes cli ON cli.id=veh.veh_cliente WHERE veh_id={$_REQUEST['vehid']} LIMIT 1";
    $res = $link->query($sql);
    $fila = mysqli_fetch_array($res);
    $response['status'] = 'OK';
    $response['id'] = $fila['id'];
    $response['marca'] = $fila['veh_marca'];
    $response['modelo'] = $fila['veh_modelo'];
    $response['tiposervicio'] = $fila['veh_tiposerv'];
    echo json_encode($response);
    break;

  case 'updateProductosxTecnico':
    //try{
    $id1   = $_REQUEST['id1'];
    $id2   = $_REQUEST['id2'];
    $prod1 = json_decode($_REQUEST['prod1']);
    $prod2 = json_decode($_REQUEST['prod2']);
    $comen = $_REQUEST['observaciones'];
    $sql   = "BEGIN TRAN";
    $link->query($sql);
    $fecha = date("Y-m-d");

    $sql   = "INSERT INTO traspasos(tras_fecha,tras_bodega,tras_observaciones,tras_usuario,tras_tipo) VALUES('{$fecha}',{$id2},'{$comen}',{$_SESSION['cloux_new']},2)";

    $res        = $link->query($sql);
    $idtraspaso = $link->insert_id;
    $validar1   = 0;
    $validar2   = 0;
    $count1     = 0;
    $count2     = 0;

    if (count($prod1) > 0) {
      if ($id1 != '' && $id1 != 0) {
        for ($i = 0; $i < count($prod1); $i++) {

          $id         = $prod1[$i]->idpxt;
          $idproducto = $prod1[$i]->idpro;
          $cantidad   = $prod1[$i]->cantidad;
          $tipo       = $prod1[$i]->tipo;

          if ($idproducto != '') {

            $count1++;
            $sql    = "SELECT * FROM productosxtecnico WHERE pxt_id={$id}";
            $res    = $link->query($sql);
            $existe = mysqli_fetch_array($res);

            if ($existe['pxt_idtecnico'] != $id1) {
              $sqlinsert = "INSERT INTO detalletraspaso(dtras_traspaso,dtras_bodega,dtras_producto,dtras_cantidad,dtras_tipo) VALUES(" . $idtraspaso . "," . $id1 . "," . $idproducto . "," . $cantidad . "," . $tipo . ")";
              //$response['sqlinsert_1_'.($i)] = $sqlinsert;
              $res = $link->query($sqlinsert);
            }


            $sql  = "UPDATE productosxtecnico SET pxt_idtecnico={$id1} WHERE pxt_id={$id}";
            //$response['update_1_'.($i)] = $sql;
            $res1 = $link->query($sql);
            if ($res1) {
              $validar1++;
            }
          }
        }
      }
    }

    if (count($prod2) > 0) {
      if ($id2 != '' && $id2 != 0) {
        for ($i = 0; $i < count($prod2); $i++) {
          $id = $prod2[$i]->idpxt;
          $idproducto = $prod2[$i]->idpro;
          $cantidad = $prod2[$i]->cantidad;
          $tipo = $prod2[$i]->tipo;
          if ($idproducto != '') {
            $count2++;
            $sql = "SELECT * FROM productosxtecnico WHERE pxt_id={$id}";
            $res = $link->query($sql);
            $existe = mysqli_fetch_array($res);
            if ($existe['pxt_idtecnico'] != $id2) {
              $sqlinsert = "INSERT INTO detalletraspaso(dtras_traspaso,dtras_bodega,dtras_producto,dtras_cantidad,dtras_tipo) VALUES(" . $idtraspaso . "," . $id2 . "," . $idproducto . "," . $cantidad . "," . $tipo . ")";
              //$response['sqlinsert_2_'.($i)] = $sqlinsert;
              $res = $link->query($sqlinsert);
            }

            $sql = "UPDATE productosxtecnico SET pxt_idtecnico={$id2} WHERE pxt_id={$id}";
            //$response['update_2_'.($i)] = $sql;
            $res2 = $link->query($sql);
            if ($res2) {
              $validar2++;
            }
          }
        }
      }
    }

    $response['status1'] = 'ERROR';
    $response['status2'] = 'ERROR';
    if ($count1 == $validar1) {
      $response['status1'] = 'OK';
    }
    if ($count2 == $validar2) {
      $response['status2'] = 'OK';
    }

    if ($response['status1'] == 'OK' && $response['status2'] == 'OK') {

      unset($response['status1']);
      unset($response['status2']);
      $response['status'] = 'OK';
      $sql                = "COMMIT TRAN";
      $res                = $link->query($sql);
      if ($res) {
        $response['COMMIT'] = true;
      }
    } else {
      $sql = "ROLLBACK TRAN";
      $res = $link->query($sql);
      if ($res) {
        $response['rollback'] = true;
      }
      $response['status'] = 'ERROR';
    }
    //}catch (\Throwable $th) {
    //$response['status'] = ''.$th;
    //}
    echo json_encode($response);
    break;

  case 'newupdatetraspaso':
    $recibe = json_decode($_REQUEST["envio"], true);

    if ($recibe['opciontecnico'] == 1) {
      $envia   = $recibe['bodega1'];
      $recibe1 = $recibe['bodega2'];
    } else {
      $envia   = $recibe['bodega2'];
      $recibe1 = $recibe['bodega1'];
    }
    $idtra = 0;
    if ($recibe['valida'] == 0) {

      $prod  = array(array('id' => $recibe['idserie'], 'idpro' => $recibe['idproducto']));
      $sql   = "INSERT INTO traspasos_series(tra_fecha,usu_id_envia,usu_id_recibe,tra_observacion,usu_modifica,tra_detalle) VALUES('{$fecha}',{$envia},{$recibe1},'{$recibe['comen']}',{$_SESSION['cloux_new']},'" . str_replace("\\", '', json_encode($prod)) . "')";
      $res   = $link->query($sql);
      $idtra = $link->insert_id;
    } else {
      $sql2  = "SELECT * FROM traspasos_series where usu_id_envia = {$envia} and usu_id_recibe = {$recibe1} and tra_estado = 1 order by 1 desc limit 1";
      $res2  = $link->query($sql2);
      $prod  = array(array('id' => $recibe['idserie'], 'idpro' => $recibe['idproducto']));
      foreach ($res2 as $key2) {
        $idtra  = $key2['tra_id'];
        $varpas = json_decode($key2['tra_detalle'], true);
        foreach ($varpas as $keyres2) {
          array_push($prod, array('id' => $keyres2['id'], 'idpro' => $keyres2['idpro']));
        }
      }

      $sql3  = "UPDATE traspasos_series SET tra_detalle = '" . str_replace("\\", '', json_encode($prod)) . "' WHERE tra_id = " . $idtra . "";
      $res3  = $link->query($sql3);
    }

    $sql1  = "UPDATE serie_guia SET usu_id_cargo = {$recibe1} WHERE ser_id = {$recibe['idserie']} and pro_id = {$recibe['idproducto']}";
    $res1  = $link->query($sql1);

    if ($idtra != 0) {
      $devuelve = array('mensaje' => 'Traspaso concretado', 'logo' => 'success', 'pru' => $sql2);
    } else {
      $devuelve = array('mensaje' => 'Erro al traspasar', 'logo' => 'danger', 'pru' => $sql2);
    }

    echo json_encode($devuelve);

    break;

    case 'buscarTraspasoPorSerie':
      header('Content-Type: application/json; charset=UTF-8');
  
      $serieBuscada = isset($_REQUEST['serie']) ? $_REQUEST['serie'] : '';
      if (trim($serieBuscada) == "") {
          echo json_encode(array('error' => 'Debe ingresar una serie'));
          exit;
      }
  
      $serieBuscadaEsc = $link->real_escape_string($serieBuscada);
      $sql = "
      SELECT 
          sg.*, 
          p.pro_nombre,
          per.per_nombrecorto AS usu_id_cargo_nombre
      FROM serie_guia sg
      LEFT JOIN productos p  ON p.pro_id   = sg.pro_id
      LEFT JOIN personal per ON per.per_id = sg.usu_id_cargo
      WHERE sg.ser_codigo = '$serieBuscadaEsc'
      ORDER BY sg.ser_id DESC 
      LIMIT 1
      ";
      $res = $link->query($sql);
  
      if ($res === false) {
          echo json_encode(array('error' => 'Error en la consulta de serie'));
          exit;
      }
  
      if ($res->num_rows > 0) {
          $serieGuia = $res->fetch_assoc();
          $ser_id = $serieGuia['ser_id'];
  
          $sql2 = "
          SELECT 
              ts.*,
              pe1.per_nombrecorto AS usu_envia_nombre,
              pe2.per_nombrecorto AS usu_recibe_nombre
          FROM traspasos_series ts
          LEFT JOIN personal pe1 ON pe1.per_id = ts.usu_id_envia
          LEFT JOIN personal pe2 ON pe2.per_id = ts.usu_id_recibe
          WHERE ts.tra_detalle LIKE '%\"id\":$ser_id%' 
          ORDER BY ts.tra_id DESC
          ";
          $res2 = $link->query($sql2);
  
          if ($res2 === false) {
              echo json_encode(array('error' => 'Error en la consulta de traspasos'));
              exit;
          }
  
          $traspasos = array();
          while ($row2 = $res2->fetch_assoc()) {
              $traspasos[] = $row2;
          }
  
          // 3. Devolvemos la serieGuia (con pro_nombre y usu_id_cargo_nombre) y los traspasos
          echo json_encode(array(
              'serieGuia' => $serieGuia,
              'traspasos' => $traspasos
          ));
      } else {
          // No se encontró la serie
          echo json_encode(array('error' => 'No se encontró esa serie en la base de datos.'));
      }
    break;
  

  case 'updateTicket':

    $fecha = '';
    $hora = '';
    if ($_REQUEST['fecha'] != '' || $_REQUEST['fecha'] != null) {
      $fecha = explode('/', $_REQUEST['fecha'])[2] . '-' . explode('/', $_REQUEST['fecha'])[1] . '-' . explode('/', $_REQUEST['fecha'])[0];
    } else {
      $fecha = '0000-00-00';
    }
    if ($_REQUEST['hora'] == '') {
      $hora = '00:00:00';
    } else {
      $hora = $_REQUEST['hora'];
    }

    if ($_REQUEST['dispositivo'] == '' || $_REQUEST['dispositivo'] == null) {
      $_REQUEST['dispositivo'] = 0;
    }

    if ($_REQUEST['ttrabajo'] == '' || $_REQUEST['ttrabajo'] == null) {
      $_REQUEST['ttrabajo'] = 0;
    }

    if ($_REQUEST['tic_usuario_externo'] == '' || $_REQUEST['tic_usuario_externo'] == null) {
      $_REQUEST['tic_usuario_externo'] = 0;
    }

    $idpersonal = 0;
    $sql = "SELECT usu_idpersonal FROM usuarios WHERE usu_id='{$_REQUEST["tecnico"]}'";
    $res = $link->query($sql);
    if ($res) {
      $fila = mysqli_fetch_array($res);
      $idpersonal = $fila['usu_idpersonal'] == null || $fila['usu_idpersonal'] == '' ? 0 : $fila['usu_idpersonal'];
    }

    //, tic_id_rsocial={$_REQUEST['rsocial']}

    $sql = "UPDATE tickets 
          SET tic_cliente={$_REQUEST['cliente']}, tic_rsocial={$_REQUEST['rsocial']}, tic_patente={$_REQUEST['patente']}, tic_dispositivo={$_REQUEST['dispositivo']}, 
          tic_tipotrabajo={$_REQUEST['ttrabajo']}, tic_tiposervicio='{$_REQUEST['tservicio']}', 
          tic_contacto='{$_REQUEST['contacto']}', tic_celular='{$_REQUEST['celular']}', 
          tic_lugar='{$_REQUEST['lugar']}', tic_descripcion='{$_REQUEST['descp']}', 
          tic_fechaagenda='{$fecha}', tic_horaagenda='{$hora}', tic_tecnico={$idpersonal}, 
          tic_descagenda='{$_REQUEST['desctecnico']}',
          tic_tipo_prestador = '{$_REQUEST['tic_tipo_prestador']}', tic_usuario_externo = '{$_REQUEST['tecnico']}'  
          WHERE tic_id={$_REQUEST['id']}";
    $res = $link->query($sql);
    if ($res) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
      $response['error'] = mysqli_error($link);
      $response['sql'] = $sql;
    }
    echo json_encode($response);
    break;

  case 'activarMonitoreo':
    $sql = "UPDATE clientes 
          SET cli_estadows={$_REQUEST['active']} 
          WHERE id={$_REQUEST['id']}";
    $res = $link->query($sql);
    if ($res) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
    }
    echo json_encode($response);
    break;

  case 'getPlantillaTickets':

    $sql1 = "SELECT cuenta FROM clientes GROUP BY cuenta ORDER BY cuenta";
    $res1 = $link->query($sql1);
    $cuentas = array();
    $contador = 0;
    while ($fila1 = mysqli_fetch_array($res1)) {
      $cuentas[$contador] = $fila1['cuenta'];
      $contador++;
    }

    $sql2 = "SELECT * 
            FROM clientes 
            WHERE razonsocial <> ''
            ORDER BY cuenta";
    $res2 = $link->query($sql2);
    $razon = array();
    $contador = 0;
    while ($fila2 = mysqli_fetch_array($res2)) {
      $razon[$contador] = $fila2['razonsocial'];
      $contador++;
    }

    $sql3 = "SELECT * FROM `vehiculos` ORDER BY veh_patente";
    $res3 = $link->query($sql3);
    $patente = array();
    $contador = 0;
    while ($fila3 = mysqli_fetch_array($res3)) {
      $patente[$contador] = $fila3['veh_patente'];
      $contador++;
    }

    $sql4 = "SELECT * FROM `tiposdedispositivos` ORDER BY tdi_nombre";
    $res4 = $link->query($sql4);
    $dispositivo = array();
    $contador = 0;
    while ($fila4 = mysqli_fetch_array($res4)) {
      $dispositivo[$contador] = $fila4['tdi_nombre'];
      $contador++;
    }

    $sql5 = "SELECT * FROM `servicios` ";
    $res5 = $link->query($sql5);
    $tservicio = array();
    $contador = 0;
    while ($fila5 = mysqli_fetch_array($res5)) {
      $tservicio[$contador] = $fila5['ser_nombre'];
      $contador++;
    }

    $sql6 = "SELECT * FROM `tiposdetrabajos` ORDER BY ttra_nombre";
    $res6 = $link->query($sql6);
    $ttrabajo = array();
    $contador = 0;
    while ($fila6 = mysqli_fetch_array($res6)) {
      $ttrabajo[$contador] = $fila6['ttra_nombre'];
      $contador++;
    }

    $sql7 = "SELECT * FROM `personal` WHERE per_estado=1 AND per_id!=17 AND per_id!=18 ORDER BY per_nombrecorto";
    $res7 = $link->query($sql7);
    $personal = array();
    $contador = 0;
    while ($fila7 = mysqli_fetch_array($res7)) {
      $personal[$contador] = $fila7['per_nombrecorto'];
      $contador++;
    }


    try {
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("DS");
      $objPHPExcel->getProperties()->setLastModifiedBy("DS");
      $objPHPExcel->getProperties()->setTitle("Plantilla");
      $objPHPExcel->getProperties()->setSubject("Plantilla");
      $objPHPExcel->getProperties()->setDescription("Plantilla");
      $objPHPExcel->setActiveSheetIndex(0);

      $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Cuenta");
      $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Razón Social");
      $objPHPExcel->getActiveSheet()->SetCellValue('C1', "Patente");
      /*$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Dispositivo");*/
      $objPHPExcel->getActiveSheet()->SetCellValue('D1', "Tipo de Servicio");
      $objPHPExcel->getActiveSheet()->SetCellValue('E1', "Tipo de trabajo");
      /*$objPHPExcel->getActiveSheet()->SetCellValue('G1', "Contacto");
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', "Celular");
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', "Lugar");
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', "Descripción");
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', "Agenda");
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', "Fecha Hora Agenda");*/
      $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(21);
      $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
      $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(21);
      $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
      $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(21);
      /*$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(27);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);*/

      $index = 2;
      for ($i = 0; $i < count($cuentas); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('AA' . $index, $cuentas[$i]);
        $index++;
      }

      $index = 2;
      for ($i = 0; $i < count($razon); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('AB' . $index, $razon[$i]);
        $index++;
      }

      $index = 2;
      for ($i = 0; $i < count($patente); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('AC' . $index, $patente[$i]);
        $index++;
      }

      $index = 2;
      for ($i = 0; $i < count($dispositivo); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('AD' . $index, $dispositivo[$i]);
        $index++;
      }

      $index = 2;
      for ($i = 0; $i < count($tservicio); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('AE' . $index, $tservicio[$i]);
        $index++;
      }

      $index = 2;
      for ($i = 0; $i < count($ttrabajo); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('AF' . $index, $ttrabajo[$i]);
        $index++;
      }

      $index = 2;
      for ($i = 0; $i < count($personal); $i++) {
        $objPHPExcel->getActiveSheet()->SetCellValue('AG' . $index, $personal[$i]);
        $index++;
      }


      if (count($cuentas) > 0) {
        for ($i = 2; $i <= 100; $i++) {

          $objValidation = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getDataValidation();
          $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
          $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
          $objValidation->setAllowBlank(false);
          $objValidation->setShowInputMessage(true);
          $objValidation->setShowDropDown(true);
          $objValidation->setPromptTitle('Seleccione cuenta');
          $objValidation->setPrompt('Seleccione un cuenta a usar.');
          $objValidation->setErrorTitle('Error de entrada');
          $objValidation->setError('La cuenta no está en la lista');
          $objValidation->setFormula1('=\'Plantilla\'!$AA$2:$AA$' . (count($cuentas) + 1));
          unset($objValidation);

          $objValidation = $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getDataValidation();
          $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
          $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
          $objValidation->setAllowBlank(false);
          $objValidation->setShowInputMessage(true);
          $objValidation->setShowDropDown(true);
          $objValidation->setPromptTitle('Seleccione razon social');
          $objValidation->setPrompt('Seleccione un razon social a usar.');
          $objValidation->setErrorTitle('Error de entrada');
          $objValidation->setError('La razon social no está en la lista');
          $objValidation->setFormula1('=\'Plantilla\'!$AB$2:$AB$' . (count($razon) + 1));
          unset($objValidation);

          $objValidation = $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getDataValidation();
          $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
          $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
          $objValidation->setAllowBlank(false);
          $objValidation->setShowInputMessage(true);
          $objValidation->setShowDropDown(true);
          $objValidation->setPromptTitle('Seleccione patente');
          $objValidation->setPrompt('Seleccione un patente.');
          $objValidation->setErrorTitle('Error de entrada');
          $objValidation->setError('La patente no está en la lista');
          $objValidation->setFormula1('=\'Plantilla\'!$AC$2:$AC$' . (count($patente) + 1));
          unset($objValidation);

          $objValidation = $objPHPExcel->getActiveSheet()->getCell('D' . $i)->getDataValidation();
          $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
          $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
          $objValidation->setAllowBlank(false);
          $objValidation->setShowInputMessage(true);
          $objValidation->setShowDropDown(true);
          $objValidation->setPromptTitle('Seleccione tipo servicio');
          $objValidation->setPrompt('Seleccione un tipo servicio.');
          $objValidation->setErrorTitle('Error de entrada');
          $objValidation->setError('El tipo servicio no está en la lista');
          $objValidation->setFormula1('=\'Plantilla\'!$AE$2:$AE$' . (count($tservicio) + 2));
          unset($objValidation);

          $objValidation = $objPHPExcel->getActiveSheet()->getCell('E' . $i)->getDataValidation();
          $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
          $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
          $objValidation->setAllowBlank(false);
          $objValidation->setShowInputMessage(true);
          $objValidation->setShowDropDown(true);
          $objValidation->setPromptTitle('Seleccione tipo de trabajo');
          $objValidation->setPrompt('Seleccione un tipo de trabajo.');
          $objValidation->setErrorTitle('Error de entrada');
          $objValidation->setError('El tipo de trabajo no está en la lista');
          $objValidation->setFormula1('=\'Plantilla\'!$AF$2:$AF$' . (count($ttrabajo) + 1));
          unset($objValidation);

          /*$objValidation = $objPHPExcel->getActiveSheet()->getCell('L' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setPromptTitle('Seleccione tipo unidad de vehículo');
                $objValidation->setPrompt('Seleccione un tipo unidad.');
                $objValidation->setErrorTitle('Error de entrada');
                $objValidation->setError('El tipo unidad de vehículo no está en la lista');
                $objValidation->setFormula1('=\'Plantilla\'!$AF$2:$AF$'.(count($tipounidad)+1));
                unset($objValidation);

                $objValidation = $objPHPExcel->getActiveSheet()->getCell('N' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setPromptTitle('Seleccione coordinador de vehículo');
                $objValidation->setPrompt('Seleccione un coordinador.');
                $objValidation->setErrorTitle('Error de entrada');
                $objValidation->setError('El coordinador de vehículo no está en la lista');
                $objValidation->setFormula1('=\'Plantilla\'!$AG$2:$AG$'.(count($coordinadores)+1));
                unset($objValidation);

                $objValidation = $objPHPExcel->getActiveSheet()->getCell('Q' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setPromptTitle('Seleccione tipo carga de vehículo');
                $objValidation->setPrompt('Seleccione un tipo carga.');
                $objValidation->setErrorTitle('Error de entrada');
                $objValidation->setError('El tipo carga de vehículo no está en la lista');
                $objValidation->setFormula1('=\'Plantilla\'!$AH$2:$AH$'.(count($tipocarga)+1));
                unset($objValidation);

                $objValidation = $objPHPExcel->getActiveSheet()->getCell('S' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setPromptTitle('Seleccione conductores');
                $objValidation->setPrompt('Seleccione una conductores.');
                $objValidation->setErrorTitle('Error de entrada');
                $objValidation->setError('La conductores no está en la lista');
                $objValidation->setFormula1('=\'Plantilla\'!$AI$2:$AI$'.(count($conductores)+1));
                unset($objValidation);

                $objValidation = $objPHPExcel->getActiveSheet()->getCell('T' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setPromptTitle('Seleccione transportista');
                $objValidation->setPrompt('Seleccione un transportista.');
                $objValidation->setErrorTitle('Error de transportista');
                $objValidation->setError('El transportista no está en la lista');
                $objValidation->setFormula1('=\'Plantilla\'!$AJ$2:$AJ$'.(count($transportista)+1));
                unset($objValidation);

                $objValidation = $objPHPExcel->getActiveSheet()->getCell('V' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setPromptTitle('Seleccione semiremolque');
                $objValidation->setPrompt('Seleccione un semiremolque.');
                $objValidation->setErrorTitle('Error de semiremolque');
                $objValidation->setError('El semiremolque no está en la lista');
                $objValidation->setFormula1('=\'Plantilla\'!$AK$2:$AK$'.(count($semremolque)+1));
                unset($objValidation);

                $objValidation = $objPHPExcel->getActiveSheet()->getCell('W' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setPromptTitle('Seleccione tipo viaje');
                $objValidation->setPrompt('Seleccione un tipo viaje.');
                $objValidation->setErrorTitle('Error de tipo viaje');
                $objValidation->setError('El tipo viaje no está en la lista');
                $objValidation->setFormula1('=\'Plantilla\'!$AL$2:$AL$'.(count($tipoviajes)+1));
                unset($objValidation);

                $objValidation = $objPHPExcel->getActiveSheet()->getCell('U' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setPromptTitle('Seleccione vehiculo');
                $objValidation->setPrompt('Seleccione un vehiculo.');
                $objValidation->setErrorTitle('Error de vehiculo');
                $objValidation->setError('El vehiculo no está en la lista');
                $objValidation->setFormula1('=\'Plantilla\'!$AM$2:$AM$'.(count($vehiculos)+1));
                unset($objValidation);*/
        }
      }

      $objPHPExcel->getActiveSheet()->setTitle('Plantilla');
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      ob_start();
      $objWriter->save("php://output");
      $xlsData = ob_get_contents();
      ob_end_clean();
      $response =  array(
        'op' => 'ok',
        'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
      );
      echo json_encode($response);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }
    break;

  case 'postCargaTickets':
    try {

      $data = array();
      $pdv_fecha = date("Y-m-d");
      $archivo = $_FILES['archivo']['name']; // nombre archivo a cargar
      $temporal = $_FILES['archivo']['tmp_name']; //nombre temporal en equipo cliente
      $tickets = array();
      if ($temporal != "") {
        $codigo = generarCodigo(6);

        $adjunto = $codigo . "_" . $archivo;
        $ruta = "./archivos/proformas/" . $archivo;

        $excel = PHPExcel_IOFactory::load($temporal);
        $hoja = $excel->getActiveSheet()->toArray(null, true, true, true);
        $hojaExcel = $hoja;
        $data = array();
        $indice2 = 1;
        $indice1 = 0;
        $filas = 0;
        /*foreach($hojaExcel as $indice=>$celda){
                    $filas++;
                    if($filas > 1){
                        if($celda['A'] || $celda["A"]!=null && $celda["E"]!="" || $celda["E"]!=null){
                            $cuenta  = $excel->getActiveSheet()->getCell('A'.($indice2+1))->getOldCalculatedValue();
                            $rsocial = $excel->getActiveSheet()->getCell('B'.($indice2+1))->getOldCalculatedValue();
                            $patente = $excel->getActiveSheet()->getCell('C'.($indice2+1))->getOldCalculatedValue();
                            $data[$indice1] = array('cuenta'=>$cuenta, 'rsocial'=>$rsocial, 'patente'=>$patente);
                            $indice2++;
                            $indice1++;
                        }
                    }
                }*/

        $filas = 0;
        $i = 0;
        $indice1 = 0;
        foreach ($hoja as $indice => $celda) {
          $filas++;
          if ($filas > 1) {
            $i++;
            if ($celda["A"] != "" || $celda["A"] != null && $celda["E"] != "" || $celda["E"] != null) {

              /*$cuenta = $data[$indice1]["cuenta"];
                            $rsocial = $data[$indice1]["rsocial"];
                            $patente = $data[$indice1]["patente"];*/

              $cuenta  = $celda["A"];
              $rsocial = $celda["B"];
              $patente = $celda["C"];

              /*$disposi = $celda["G"];*/
              $tservic = $celda["D"];
              $ttrabaj = $celda["E"];
              /* $contact = $celda["J"];
                            $celular = $celda["K"];
                            $lugar   = $celda["L"];
                            $descrip = $celda["M"];
                            $agenda  = $celda["N"];
                            $FecHora = $celda["O"];*/
              $fechRegistro = date("Y-m-d H:m:s");

              if ($rsocial != '' && $cuenta != '') {
                $sqlcliente = "SELECT * FROM clientes WHERE razonsocial='{$rsocial}' and cuenta = '{$cuenta}'";
                $rescliente = $link->query($sqlcliente);
                $filacliente = mysqli_fetch_array($rescliente);
                $idcliente = $filacliente['id'];
              } else if ($rsocial != '' && $cuenta == '') {
                $sqlcliente = "SELECT * FROM clientes WHERE razonsocial='{$rsocial}'";
                $rescliente = $link->query($sqlcliente);
                $filacliente = mysqli_fetch_array($rescliente);
                $idcliente = $filacliente['id'];
              } else if ($rsocial == '' && $cuenta != '') {
                $sqlcliente = "SELECT * FROM clientes WHERE cuenta='{$cuenta}' order by 1 desc limit 1";
                $rescliente = $link->query($sqlcliente);
                $filacliente = mysqli_fetch_array($rescliente);
                $idcliente = $filacliente['id'];
              }


              $sqlpatente = "SELECT * FROM vehiculos WHERE veh_patente='{$patente}' and deleted_at is NULL";
              $respatente = $link->query($sqlpatente);
              $filapatente = mysqli_fetch_array($respatente);
              $idpatente = $filapatente['veh_id'];

              /*$sqldispo = "SELECT * FROM tiposdedispositivos WHERE tdi_nombre='{$disposi}'";
                            $resdispo=$link->query($sqldispo);
                            $filadispo=mysqli_fetch_array($resdispo);
                            $iddispo = $filadispo['tdi_id'];*/

              $sqlttrab = "SELECT * FROM tiposdetrabajos WHERE ttra_nombre='{$ttrabaj}'";
              $resttrab = $link->query($sqlttrab);
              $filattrab = mysqli_fetch_array($resttrab);
              $idttrab = $filattrab['ttra_id'];

              $sqltservi = "SELECT * FROM servicios WHERE ser_nombre='{$tservic}'";
              $restservi = $link->query($sqltservi);
              $filatservi = mysqli_fetch_array($restservi);
              $idtservi = $filatservi['ser_id'];

              $sql = "insert into tickets(tic_fechahorareg,tic_cliente,tic_patente,tic_dispositivo,tic_tipotrabajo,tic_tiposervicio,tic_contacto,tic_celular,tic_lugar,tic_descripcion,tic_estado,tic_fechaagenda,tic_horaagenda, tic_tecnico, tic_descagenda, tic_fechacierre, tic_nserie, tic_desccierre)values('" . $fechRegistro . "'," . $idcliente . "," . $idpatente . ",0," . $idttrab . "," . $idtservi . ",'','','','',1,'" . $fechRegistro . "','" . $fechRegistro . "',0,'','" . $fechRegistro . "','','')";
              $res = $link->query($sql);
              /*echo $sql.'<br>';
                            die();*/
              $id = $link->insert_id;
              /*if($contact!=''){
                              $sqlupdate = "UPDATE vehiculos SET veh_contacto='{$contact}',veh_celular='{$celular}' WHERE veh_id={$patente}";
                              $link->query($sqlupdate);
                            }*/
              $h_tipo = 1;
              $h_estado = 1;
              Historial($h_tipo, $id, $h_estado);
              /* $tickets[$indice1]=array('idticket'=>$id,'cuenta'=>$cuenta, 'rsocial'=>$rsocial,'patente'=>$patente, 'disposi'=>$disposi ,'tservic'=>$tservic, 'ttrabaj'=>$ttrabaj ,'contact'=>$contact ,'celular'=>$celular ,'lugar'=>$lugar   ,'descrip'=>$descrip ,'agenda'=>$agenda  ,'FecHora'=>$FecHora, 'fechareg'=>$fechRegistro);	*/
              $indice1++;
            }
          }
        }
      }
      echo json_encode($tickets);
    } catch (\Throwable $th) {
      $dataSend = array();
      $dataSend[0] = '' . $th;
      echo json_encode($dataSend);
    }
    break;

  case 'getListGuia':
    $sql = "SELECT *,
            (SELECT sum(ser_estado) FROM serie_guia WHERE ser_estado=1 AND gui_id=gui.gui_id) cant_pro,
            (SELECT sum(ser_neto) FROM serie_guia WHERE ser_estado=1 AND gui_id=gui.gui_id) neto 
            FROM guiaentrada gui
            LEFT OUTER JOIN proveedores prov ON prov.id=gui.gui_proveedor
            WHERE gui.gui_estado=1";
    $res = $link->query($sql);
    $table = '';
    $guias = array();
    $i = 1;
    while ($fila = mysqli_fetch_array($res)) {
      $table .= '<tr>';
      $table .= '<td class="font-weight-light">' . $i . '</td>';
      $table .= '<td style="text-align:center;"><span class="badge badge-primary">' . ($fila['gui_factura'] == '' ? 'Sin N°' : ($fila['gui_factura'] == null ? 'Sin N°' : $fila['gui_factura'])) . '</span></td>';
      $table .= '<td class="font-weight-light" align="center">' . ($fila['cant_pro'] == '' ? '0' : ($fila['cant_pro'] == null ? '0' : $fila['cant_pro'])) . '</td>';
      $table .= '<td nowrap class="font-weight-light">' . $fila['gui_fecha'] . '</td>';
      $table .= '<td class="font-weight-light">' . $fila['razonsocial'] . '</td>';
      $table .= '<td class="font-weight-light">' . ($fila['gui_bodega'] == 1 ? 'Técnicos' : 'Bodega Principal') . '</td>';
      $table .= '<td class="font-weight-light">' . ($fila['gui_concepto'] == 1 ? 'Compra Producto' : ($fila['gui_concepto'] == 2 ? 'Devolución' : ($fila['gui_concepto'] == 3 ? 'Devolución' : ''))) . '</td>';
      $table .= '<td class="font-weight-light">' . ($fila['gui_bodega'] == 1 ? 'Técnicos' : 'Bodega Principal') . '</td>';
      $table .= '<td class="font-weight-light">' . $fila['gui_desc'] . '</td>';
      $table .= '<td nowrap><button class="btn btn-warning btn-sm btn-circle tool" data-toggle="tooltip" data-placement="top" title="Editar Guía" id="editgui_' . $fila['gui_id'] . '" onclick="editarGuia(' . $fila['gui_id'] . ')"><i class="fa fa-edit"></i></button> <span class="btn btn-danger btn-sm btn-circle tool" data-toggle="tooltip" data-placement="top" title="Borrar Guía" onclick="borrarGuia(' . $fila['gui_id'] . ')"><i class="fa fa-trash"></i></span></td>';
      $table .= '</tr>';
      $i++;
      $guias[('' . $fila['gui_id'])] = array(
        'idguia' => $fila['gui_id'],
        'bodega' => $fila['gui_bodega'],
        'numero' => $fila['gui_numero'],
        'concepto' => $fila['gui_concepto'],
        'fecha' => $fila['gui_fecha'],
        'estado' => $fila['gui_estadoguia'],
        'desc' => $fila['gui_desc'],
        'proveedor' => $fila['gui_proveedor'],
        'factura' => $fila['gui_factura'],
      );
    }
    $response['tbody'] = $table;
    $response['data'] = $guias;
    echo json_encode($response);
    break;

  case 'borrarGuiaEntrada':

    $sql                = "UPDATE guiaentrada SET gui_estado = 0 WHERE gui_id={$_REQUEST['idguia']}";
    $res                = $link->query($sql);
    $response['status'] = 'ERROR';

    if ($res) {

      $sql3 = "UPDATE serie_guia SET ser_estado = 0 WHERE gui_id={$_REQUEST['idguia']}";
      $res3 = $link->query($sql3);

      $response['status'] = 'ERROR';
      if ($res3) {
        $response['status'] = 'OK';
      }
    }

    echo json_encode($response);
    break;

  case 'getdatdev':

    $recibe  = json_decode($_REQUEST['envio'], true);
    $sql     = "SELECT * FROM devoluciones where dev_id = {$recibe['iddevolucion']}";
    $res     = $link->query($sql);
    $fila    = mysqli_fetch_array($res);
    $detalle = array();
    $sql1    = "SELECT t1.*, t2.ser_codigo, t2.ser_condicion, t3.pro_nombre 
                  FROM detalledevolucion t1 
                  left outer join serie_guia t2 on t2.ser_id = t1.ser_id
                  left outer join productos t3 on t3.pro_id =  t1.pro_id
                  where t1.dev_id = {$recibe['iddevolucion']} and ddev_visible = 1";
    $res1    = $link->query($sql1);

    foreach ($res1 as $key1) {
      array_push($detalle, array('ddev_id' => $key1['ddev_id'], 'ser_id' => $key1['ser_id'], 'ddev_observacion' => $key1['dev_observacion'], 'pro_id' => $key1['pro_id'], 'ddev_estado' => $key1['ddev_estado'], 'ser_codigo' => $key1['ser_codigo'], 'ser_condicion' => $key1['ser_condicion'], 'pro_nombre' => $key1['pro_nombre']));
    }

    $devuelve = array('usu_id_envia' => $fila['usu_id_envia'], 'dev_fecha' => $fila['dev_fecha'], 'dev_id' => $fila['dev_id'], 'dev_observacion' => $fila['dev_observacion'], 'dev_tracking' => $fila['dev_tracking'], 'dev_tracking_codigo' => $fila['dev_tracking_codigo'], 'dev_tracking_courrier' => $fila['dev_tracking_courrier'], 'dev_tracking_recibe' => $fila['dev_tracking_recibe'], 'dev_tracking_fecha' => $fila['dev_tracking_fecha'], 'detalle' => $detalle);

    echo json_encode($devuelve);

    break;

  case 'eliminardetdev':

    $recibe  = json_decode($_REQUEST['envio'], true);
    $sql     = "update detalledevolucion set ddev_visible = 0 where ddev_id = {$recibe['iddet']}";
    $res     = $link->query($sql);

    $sql1    = "select * from detalledevolucion where dev_id = {$recibe['iddev']} and ddev_visible = 1";
    $res1    = $link->query($sql1);
    $conta   = mysqli_num_rows($res1);

    if ($conta == 0) {
      $sql2 = "update devoluciones set dev_estado = 0 where dev_id = {$recibe['iddev']}";
      $res2 = $link->query($sql2);
    }

    $sql3  = "select * from devoluciones where dev_id = {$recibe['iddev']}";
    $res3  = $link->query($sql3);
    $fila3 = mysqli_fetch_array($res3);
    $sql4  = "update serie_guia set usu_id_cargo = {$fila3['usu_id_envia']} where ser_id = {$recibe['serid']}";
    $res4  = $link->query($sql4);

    if ($res4) {
      $devuelve = array('logo' => 'success', 'mensaje' => 'Serie eliminada correctamente', 'sql' => $conta);
    } else {
      $devuelve = array('logo' => 'error', 'mensaje' => 'Ha ocurrido un error', 'sql' => $conta);
    }

    echo json_encode($devuelve);

    break;

  case 'verGuiaEntrada':

    $sql = "SELECT t1.*,t2.*, t3.razonsocial, t4.pro_nombre 
            FROM guiaentrada t1 
            inner join serie_guia t2 on t2.gui_id = t1.gui_id
            inner join proveedores t3 on t2.prov_id = t3.id
            inner join productos t4 on t4.pro_id = t2.pro_id
            where t2.ser_estado = 1 and t1.gui_id = {$_REQUEST['idguia']} 
            order by t2.pro_id,t2.ser_neto asc";
    $res      = $link->query($sql);
    $contador = mysqli_num_rows($res) - 1;
    $detalle  = array();
    $prim     = 0;
    $series   = '';
    $idserie  = '';
    $arrlista = array();
    foreach ($res as $fila) {
      /*$detalle[] = array(
        'id'=>$fila['ged_id'],
        'idguia'=>$fila['gui_id'],
        'idproveedor'=>$fila['ged_idproveedor'],
        'nproveedor'=>$fila['razonsocial'],
        'idproducto'=>$fila['ged_idproducto'],
        'nproducto'=>$fila['pro_nombre'],
        'series'=>($fila['ged_series']==null?'':$fila['ged_series']),
        'cantidad'=>$fila['ged_cantidad'],
        'neto'=>$fila['ged_neto'],
        'estado'=>$fila['ged_estado'],
      );*/

      if ($prim == 0) {
        $primer_valor_neto      = $fila['ser_neto'];
        $primer_valor_producto  = $fila['pro_id'];
        $primer_valor_proveedor = $fila['prov_id'];
        $neto                   = $fila['ser_neto'];
        $cantidad               = 1;
        $fecha                  = $fila['gui_fecha'];
        $concepto               = $fila['gui_concepto'];
        $estado                 = $fila['gui_estadoguia'];
        $descuento              = $fila['gui_desc'];
        $factura                = $fila['gui_factura'];
        $series                 = $fila['ser_codigo'] . ',';
        $idseries               = $fila['ser_id'] . '*';
        $idguia                 = $fila['gui_id'];
        $bodega                 = $fila['gui_bodega'];
        $nombreproducto         = $fila['pro_nombre'];
        $nombreproveedor        = $fila['razonsocial'];
        $idproducto             = $fila['pro_id'];
        $idproveedor            = $fila['prov_id'];

        array_push($arrlista, array('idseries' => $idseries, 'cantidad' => $cantidad, 'neto' => $neto, 'series' => $series, 'nombre_prod' => $nombreproducto, 'nombre_proveedor' => $nombreproveedor, 'idproducto' => $idproducto, 'idproveedor' => $idproveedor));
      } else {

        if ($prim == 1) {
          $arrlista = [];
        }

        if ($primer_valor_neto == $fila['ser_neto'] && $primer_valor_producto == $fila['pro_id'] && $primer_valor_proveedor == $fila['prov_id']) {
          $series         .= $fila['ser_codigo'] . ',';
          $idseries       .= $fila['ser_id'] . '*';
          $cantidad        = $cantidad + 1;
          $neto            = $fila['ser_neto'];


          if ($contador == $prim) {
            $mystringid  = substr($idseries, 0, -1);
            $mystringcod = substr($series, 0, -1);
            array_push($arrlista, array('idseries' => $mystringid, 'cantidad' => $cantidad, 'neto' => $neto, 'series' => $mystringcod, 'nombre_prod' => $nombreproducto, 'nombre_proveedor' => $nombreproveedor, 'idproducto' => $idproducto, 'idproveedor' => $idproveedor));
          }
        } else {
          $mystringid  = substr($idseries, 0, -1);
          $mystringcod = substr($series, 0, -1);

          array_push($arrlista, array('idseries' => $mystringid, 'cantidad' => $cantidad, 'neto' => $neto, 'series' => $mystringcod, 'nombre_prod' => $nombreproducto, 'nombre_proveedor' => $nombreproveedor, 'idproducto' => $idproducto, 'idproveedor' => $idproveedor));

          $primer_valor_neto      = $fila['ser_neto'];
          $primer_valor_producto  = $fila['pro_id'];
          $primer_valor_proveedor = $fila['prov_id'];
          $neto                   = $fila['ser_neto'];
          $nombreproducto         = $fila['pro_nombre'];
          $nombreproveedor        = $fila['razonsocial'];
          $idproducto             = $fila['pro_id'];
          $idproveedor            = $fila['prov_id'];
          $cantidad               = 1;
          $series   = $fila['ser_codigo'] . ',';
          $idseries = $fila['ser_id'] . '*';

          if ($contador == $prim) {
            $mystringid  = substr($idseries, 0, -1);
            $mystringcod = substr($series, 0, -1);
            array_push($arrlista, array('idseries' => $mystringid, 'cantidad' => $cantidad, 'neto' => $neto, 'series' => $mystringcod, 'nombre_prod' => $nombreproducto, 'nombre_proveedor' => $nombreproveedor, 'idproducto' => $idproducto, 'idproveedor' => $idproveedor));
          }
        }
      }

      $prim++;

      /*$detalle[] = array(
        'idserie'=>$fila['ser_id'],
        'idguia'=>$fila['gui_id'],
        'series'=>($fila['ged_series']==null?'':$fila['ged_series']),
        'cantidad'=>$fila['ged_cantidad'],
        'neto'=>$fila['ged_neto'],
      );*/
    }

    if ($concepto == '') {
      $concepto = 0;
    }
    $detalle = array(
      "idguia"    => $idguia,
      "bodega"    => $bodega,
      "fecha"     => $fecha,
      "concepto"  => $concepto,
      "estado"    => $estado,
      "descuento" => $descuento,
      "factura"   => $factura,
      "lista"     => $arrlista
    );
    echo json_encode($detalle);

    break;

  case 'actualizarguia':

    $total      = 0;
    $correcto   = 0;
    $incorrecto = 0;
    $serincorre = '';
    $contactos  = json_decode($_REQUEST["productos"], true);
    $sernoactu = array();
    $sql = "UPDATE guiaentrada SET gui_bodega={$_REQUEST['inputbodega']}, gui_numero='{$_REQUEST['inputnumero']}', gui_concepto='{$_REQUEST['inputconcepto']}', gui_fecha='{$_REQUEST['inputfecha']}', gui_estadoguia='{$_REQUEST['inputestado']}', gui_desc='{$_REQUEST['inputdesc']}', gui_proveedor='{$_REQUEST['inputproveedor']}', gui_factura='{$_REQUEST['inputfactura']}', gui_bodegausr = 26 WHERE gui_id={$_REQUEST['idguia']}";
    $res  = $link->query($sql);
    $data = array();

    foreach ($contactos as $valor) {

      $sep = explode("|", $valor);

      $varexploeditserexplo   = explode(',', $sep[6]);
      $paratraer = '';
      foreach ($varexploeditserexplo as $keyexplo => $datexplo) {
        $paratraer .= "'" . $datexplo . "'" . ',';
      }
      $myString = substr($paratraer, 0, -1);

      /*$curl = curl_init();
                  curl_setopt_array($curl, array(
                     CURLOPT_URL => 'http://54.90.162.240/api/v1/searchimei',
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_ENCODING => '',
                     CURLOPT_MAXREDIRS => 10,
                     CURLOPT_TIMEOUT => 0,
                     CURLOPT_FOLLOWLOCATION => true,
                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                     CURLOPT_CUSTOMREQUEST => 'POST',
                     CURLOPT_POSTFIELDS => array('imei' => "{$myString}"),
                     CURLOPT_HTTPHEADER => array(
                        'Authorization: 202cb962ac59075b964b07152d234b70'
                 ),
          ));

          $respvehiculo = curl_exec($curl);
          curl_close($curl);
          $varveh = json_decode($respvehiculo,true);*/

      if ($sep[4] != '0') {

        $varexploeditidser = explode('*', $sep[4]);
        $varexploeditser   = explode(',', $sep[6]);
        $ind               = 0;

        foreach ($varexploeditidser as $keyidser) {

          $pasa = true;
          /*if($varveh['imei']!='Sin datos.'){
                foreach($varveh['imei'] as $keydatime=>$dataimei){
                    if($dataimei['imei']==$varexploeditser[$ind]){
                        $pasa = false;
                    }
                }
             }*/

          if ($pasa) {
            $sql1 = "UPDATE serie_guia SET ser_codigo = {$varexploeditser[$ind]} WHERE ser_id = {$keyidser}";
            $res1 = $link->query($sql1);
          }
          $ind++;
        }
      } else {

        $varexploeditser = explode(',', $sep[6]);
        foreach ($varexploeditser as $keyser) {
          $sql4  = "select * from serie_guia where ser_codigo = '{$keyser}' and ser_estado = 1";
          $res4  = $link->query($sql4);
          $fila4 = mysqli_fetch_array($res4);

          $pasa = true;
          /*if($varveh['imei']!='Sin datos.'){
                foreach($varveh['imei'] as $keydatime=>$dataimei){
                    if($dataimei['imei']==$keyser){
                        $pasa = false;
                    }
                }
             }*/

          if ($pasa) {
            if ($fila4['ser_id'] > 0) {
              $incorrecto++;
              $serincorre .= $fila4['ser_codigo'] . ',';
            } else {
              $sql1 = "INSERT INTO serie_guia(gui_id, pro_id, ser_neto, ser_fecha, ser_estado, usu_id_ingresa, ser_codigo, prov_id) VALUES ({$_REQUEST['idguia']}, {$sep[1]}, {$sep[3]},'$fecha',1, {$_SESSION['cloux_new']}, '{$keyser}',{$sep[0]})";
              $res1 = $link->query($sql1);

              $sql5 = "UPDATE productos SET pro_stock = (select pro_stock from productos where pro_id = {$sep[1]}) + 1 WHERE pro_id = {$sep[1]}";
              $res5 = $link->query($sql5);
            }
          } else {
            $incorrecto++;
            $serincorre .= $fila4['ser_codigo'] . ',';
          }
        }
      }
    }

    $sql3 = "SELECT round( sum(t1.ser_neto*1)*0.19 + sum(t1.ser_neto * 1),0) totalconiva, sum(t1.ser_estado) cantidadtotal
      FROM serie_guia t1 
      WHERE t1.gui_id = {$_REQUEST['idguia']} and t1.ser_estado = 1";
    $res3 = $link->query($sql3);

    foreach ($res3 as $key3) {
      $sql4 = "UPDATE guiaentrada SET gui_total={$key3['totalconiva']} WHERE gui_id={$_REQUEST['idguia']}";
      $res4 = $link->query($sql4);
    }

    $mystring = substr($serincorre, 0, -1);
    if ($res) {
      $response['status']        = 'OK';
      $response['correcto']      = $correcto;
      $response['incorrecto']    = $incorrecto;
      $response['serincorrecto'] = $mystring;
      $response['seriesnoactualizadas'] = $sernoactu;
    } else {
      $response['status']        = 'ERROR';
      $response['correcto']      = $correcto;
      $response['incorrecto']    = $incorrecto;
      $response['serincorrecto'] = $mystring;
      $response['seriesnoactualizadas'] = $sernoactu;
    }

    echo json_encode($response);

    break;

  case 'borrarItemGuia':

    if ($_REQUEST['iditemguia'] != '') {

      $exploidser = explode('*', $_REQUEST['iditemguia']);
      foreach ($exploidser as $key) {
        $sql1 = "update serie_guia set ser_estado = 0 WHERE ser_id = {$key} and gui_id = {$_REQUEST['idgui']}";
        $res1 = $link->query($sql1);
      }
    }

    $sql2 = "SELECT round( sum(t1.ser_neto*1)*0.19 + sum(t1.ser_neto * 1),0) totalconiva, sum(t1.ser_estado) cantidadtotal
      FROM serie_guia t1 
      WHERE t1.gui_id = {$_REQUEST['idgui']} and t1.ser_estado = 1";
    $res2 = $link->query($sql2);

    foreach ($res2 as $key2) {
      $sql3 = "UPDATE guiaentrada SET gui_total = {$key2['totalconiva']} WHERE gui_id = {$_REQUEST['idgui']}";
      $res3 = $link->query($sql3);
    }

    if ($res3) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
    }

    echo json_encode($response);

    break;

  case 'listarAsociacion':
    if (!isset($_REQUEST['fdesde'])) {
      if (!isset($_REQUEST['estado'])) {
        $sql = "SELECT easi.*,pro1.pro_nombre as gps, pro2.pro_nombre as accesorio,pro1.pro_id FROM equipos_asociados easi LEFT OUTER JOIN productos pro1 ON pro1.pro_id=easi.easi_idgps LEFT OUTER JOIN productos pro2 ON pro2.pro_id=easi.easi_accesorio ORDER BY easi_id";
      } else {
        $sql = "SELECT easi.*,pro1.pro_nombre as gps, pro2.pro_nombre as accesorio,pro1.pro_id FROM equipos_asociados easi LEFT OUTER JOIN productos pro1 ON pro1.pro_id=easi.easi_idgps LEFT OUTER JOIN productos pro2 ON pro2.pro_id=easi.easi_accesorio WHERE easi.easi_estado={$_REQUEST['estado']} ORDER BY easi_id";
      }
    } else {
      $sql = "SELECT easi.*,pro1.pro_nombre as gps, pro2.pro_nombre as accesorio,pro1.pro_id FROM equipos_asociados easi LEFT OUTER JOIN productos pro1 ON pro1.pro_id=easi.easi_idgps LEFT OUTER JOIN productos pro2 ON pro2.pro_id=easi.easi_accesorio WHERE easi.easi_create_at BETWEEN '{$_REQUEST['fdesde']} 00:00:00' AND '{$_REQUEST['fhasta']} 23:59:59' ORDER BY easi_id";
    }

    $res = $link->query($sql);
    $list = array();
    while ($fila = mysqli_fetch_array($res)) {
      $sql1 = "SELECT * FROM sensores WHERE sen_estado=1 ORDER BY sen_id";
      $res1 = $link->query($sql1);
      $sensores = array();
      while ($fila1 = mysqli_fetch_array($res1)) {
        $sql2 = "SELECT * FROM sensores_estado WHERE sene_idsensor={$fila1['sen_id']} AND sene_idasoc={$fila['easi_id']} LIMIT 1";
        $res2 = $link->query($sql2);
        $fila2 = mysqli_fetch_array($res2);
        $sensores[] = array(
          'id' => $fila1['sen_id'],
          'nombre' => $fila1['sen_nombre'],
          'estado' => $fila2['sene_senestado'],
          'estado1' => $fila1['sen_estado1'],
          'estado2' => $fila1['sen_estado2'],
        );
      }

      /*$curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://54.90.162.240/api/v1/trackup',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('imei' => $fila['easi_seriegps']),
          CURLOPT_HTTPHEADER => array(
            'Authorization: 202cb962ac59075b964b07152d234b70'
          ),
        ));

        $respalba = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($respalba);*/

      /*  var_dump($var);*/

      $list[] = array(
        'id' => $fila['easi_id'],
        'idgps' => $fila['easi_idgps'],
        'gps' => $fila['gps'],
        'seriegps' => $fila['easi_seriegps'],
        'idaccesorio' => $fila['easi_accesorio'],
        'estadosim' => $fila['easi_estadosim'],
        'accesorio' => $fila['accesorio'],
        'serieaccesorio' => $fila['easi_seriesim'],
        'idbodega' => $fila['easi_bodega'],
        'fechareg' => $fila['easi_create_at'],
        'estado' => $fila['easi_estado'],
        'pro_id' => $fila['pro_id'],
        'sensores' => $sensores/*,
        'api'=> $var*/
      );
    }
    $response['sql'] = $sql;
    $response['data'] = $list;
    echo json_encode($response);
    break;

  case 'listarBodega':

    $data   = [];
    $draw   = $_REQUEST["draw"];
    $start  = $_REQUEST["start"];
    $length = $_REQUEST["length"];
    $search = trim($_REQUEST["search"]["value"]);

    //$columnOrder   = $_REQUEST['order'][0]['column']; // Suponiendo que 'column' es el parámetro que contiene el nombre de la columna por la cual quieres ordenar
    //$dirOrden      = $_REQUEST['order'][0]['dir']; // Suponiendo que 'dir' es el parámetro que contiene la dirección del ordenamiento (ASC o DESC)
    //$columna_orden = $columnas[$columnOrder];

    // Filtramos la consulta por la búsqueda
    $sqlFiltroSearch = '';
    if ($search) {

      $filtroAvanzado = '';
      $filtroEstado = '';

      //filtros para tipo de servicio avanzado o basico
      $tipoServicio = array("activo", "a", "ac", "act", "acti", "activ", "no activo", "n", "no", "no ", "no a", "no ac", "no act", "no acti", "no activ");
      $searchTServicio = strtolower($search);
      if (in_array($searchTServicio, $tipoServicio)) {

        $tipoServicioB = array("activo", "a", "ac", "act", "acti", "activ");
        $tipoServicioA = array("no activo", "n", "no", "no ", "no a", "no ac", "no act", "no acti", "no activ");
        if (in_array($searchTServicio, $tipoServicioA)) {
          $filtroAvanzado .= ' OR sg.ser_estado = 1 ';
        } else if (in_array($searchTServicio, $tipoServicioB)) {
          $filtroAvanzado .= ' OR sg.ser_estado = 0 ';
        }
      }

      //filtro para estado activo o inactivo
      $tipoEstado = array("malo", "m", "ma", "mal", "bueno", "b", "bu", "bue", "buen");
      $searchEstado = strtolower($search);
      if (in_array($searchEstado, $tipoEstado)) {
        $tipoEstadoA = array("malo", "m", "ma", "mal");
        $tipoEstadoI = array("bueno", "b", "bu", "bue", "buen");
        if (in_array($searchEstado, $tipoEstadoA)) {
          $filtroAvanzado .= ' OR sg.ser_condicion = 0 ';
        } else if (in_array($searchEstado, $tipoEstadoI)) {
          $filtroAvanzado .= ' OR sg.ser_condicion = 1 ';
        }
      }

      $sqlFiltroSearch = " where (sg.ser_codigo LIKE '%$search%' OR b.per_nombrecorto LIKE '%$search%' OR p.pro_nombre LIKE '%$search%' OR sg.ser_id LIKE '%$search%' " . $filtroAvanzado . ") ";
    }

    $fil1 = ' ';
    $fil2 = '';
    /*if($_REQUEST['cliente']!=''){
          //$fil1 = ' WHERE stca_cliente = "'.$_REQUEST['cliente'].'"';
          $fil1 = ' AND c.cuenta = "'.$_REQUEST['cliente'].'"';
      }


      if($_REQUEST['patente']!=''){
          $fil2 = ' AND stca_patente = "'.$_REQUEST['patente'].'"';
      }*/

    $personal  = $_SESSION['personal_new'];
    $perfil    = $_SESSION['perfil_new'];
    if ($personal > 0 && $perfil == 3) {
      //perfil = 3 es externo y solo podran ver su bodega
      $fil2 = ' AND sg.usu_id_cargo = "' . $personal . '"';
    }

    $queryBase = "SELECT 
                    sg.ser_id as id, 
                    sg.ser_codigo as seriegps ,
                    p.pro_nombre as gps,
                    sg.ser_condicion as estado
                ,CASE
                        WHEN sg.ser_condicion = 1 THEN 'Bueno'
                        WHEN sg.ser_condicion = 0 THEN 'Malo'
                        ELSE 'consultar'
                    END AS condicion
                   ,CASE
                        WHEN sg.ser_estado = 1 THEN 'Activo'
                        WHEN sg.ser_estado = 0 THEN 'No Activo'
                        ELSE 'consultar'
                    END AS estado
                    , b.per_nombrecorto as bodega
                    , sg.* 
                    FROM serie_guia as sg
                    LEFT JOIN productos as p on ( sg.pro_id = p.pro_id)
                    LEFT JOIN personal as b on ( sg.usu_id_cargo = b.per_id)
                    -- where sg.usu_id_cargo = 26 
                    -- and ser_instalado = 0 -- and ser_estado = 1
                    {$fil1} {$fil2} {$sqlFiltroSearch}
                    -- ORDER BY sg.usu_id_cargo
                ";



    $query = $queryBase;
    // Limitamos la consulta
    $query .= " LIMIT $start, $length";

    $res = $link->query($query);
    /* echo $sql.'<br>';*/
    $cantidad = $start;
    $arrayData = [];

    $cantidadgestionados = 0;
    $cantidadpendientes  = 0;

    $sql[] = $query;
    $list = array();

    if ($res && $res->num_rows > 0) {

      $list = array();

      // Recorrer los resultados y almacenarlos en el array
      while ($row = $res->fetch_assoc()) {
        // Almacenar cada fila en el array
        $list[] = $row;
      }

      // Liberar la memoria del resultado
      $res->free();

      $totalData = "SELECT COUNT(*) as contador  
                    FROM serie_guia as sg
                    LEFT JOIN productos as p on ( sg.pro_id = p.pro_id)
                    LEFT JOIN personal as b on ( sg.usu_id_cargo = b.per_id)
                    -- where sg.usu_id_cargo = 26 
                    -- and ser_instalado = 0 -- and ser_estado = 1
                    {$fil1} {$fil2} {$sqlFiltroSearch}
                    ";

      $total = 0;
      $resData = $link->query($totalData);
      if ($resData) {
        $total = mysqli_fetch_array($resData)['contador'];
      }


      // Formatear los datos para que DataTables los entienda
      $response = array(
        "draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $list
      );
    }

    //$list = [];
    // Cerrar la conexión
    $link->close();

    $response['sql'] = $sql;
    $response['data'] = $list;
    echo json_encode($response);
    break;

  case 'listarVehiculosAll':

    $data   = [];
    $draw   = $_REQUEST["draw"];
    $start  = $_REQUEST["start"];
    $length = $_REQUEST["length"];
    $search = trim($_REQUEST["search"]["value"]);


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://54.158.85.208/api-dev/v1/getVehiculosAll',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => 'start=' . $start . '&length=' . $length . '&search=' . $search . '&draw=' . $draw . '',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $dataTemporal = json_decode($response, true);
    //print_r( $dataTemporal->data );
    //exit;
    if ($dataTemporal['data']) {

      foreach ($dataTemporal['data'] as $keyFirst => $valor) {
        /*Array (
                      [id_vehiculo] => 1
                      [patente] => CCRP39
                      [imei] => 867553053396776
                      [empresa] => pipau
                      [fh_gps] => 0000-00-00 00:00:00
                      [fh_dato] => 0000-00-00 00:00:00
                      [dias_pasados] =>
                  ) 0*/
        //print_r( $valor );
        //echo $key.'<br>';

        if ($dataTemporal['data'][$keyFirst]['fh_gps'] > '2024-01-01') {
          $fecha = date_create($dataTemporal['data'][$keyFirst]['fh_gps'], timezone_open('UTC'));
          date_timezone_set($fecha, timezone_open('America/Santiago'));
          $fechanueva = date_format($fecha, 'Y-m-d H:i:s');

          $dataTemporal['data'][$keyFirst]['fh_gps'] =  $fechanueva;
        }



        if ($valor["empresa"] != '' && $valor["empresa"] != null) {
          $empresasNO = array('cloux', 'mysql', 'information_schema', 'performance_schema', 'prueba_data');
          if (!in_array($valor["empresa"],  $empresasNO)) {

            $pasa = true;
            if ($pasa) {
              $cantidad = 0;
              $linkclient = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', strtolower($valor["empresa"]));
              if (mysqli_connect_errno()) {
                //printf("Falló la conexión: %s\n", mysqli_connect_error());
                //exit();
                continue;
              }

              $arravehsintran = array();

              $filtro = '';

              if ($valor["patente"] != '') {
                $filtro = ' and t1.veh_patente = "' . trim($valor["patente"]) . '"';
              }

              if ($_REQUEST['tservicio'] != '') {
                if ($_REQUEST['tservicio'] == 1) {
                  $filtro = ' and t1.veh_tiposerv = "1"';
                } else {
                  $filtro = ' and t1.veh_tiposerv in (0,2)';
                }
              }

              if ($_REQUEST['dias'] != '') {
                $filtro = " AND TIMESTAMPDIFF(DAY, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) >= '" . $_REQUEST['dias'] . "' and t1.deleted_at is NULL";
              }

              $sql = "SELECT 
                                      t1.veh_id, 
                                      t1.veh_patente, 
                                      t2.ulp_odometrocan, 
                                      t2.ulp_odolitroscan, 
                                      t1.veh_tiposerv, 
                                      t2.ulp_fechahora,
                                      t4.tra_rsocial,
                                      t4.tra_alias,
                                      t4.tra_rut,
                                      t1.veh_seriegps,
                                      TIMESTAMPDIFF(DAY, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) AS dias_transcurridos
                                  FROM 
                                      vehiculos t1
                                  INNER JOIN 
                                      ultimaposicion t2 ON t2.ulp_idveh = t1.veh_id
                                  LEFT OUTER JOIN 
                                      tipo_vehiculo t3 ON t3.tveh_id = t1.veh_tipoveh
                                  LEFT OUTER JOIN 
                                      transportistas t4 on t4.tra_id = t1.veh_rsocial
                                  WHERE 
                                      -- TIMESTAMPDIFF(HOUR, CONVERT_TZ(t2.ulp_fechahora, '+00:00', '-03:00'), CONVERT_TZ(NOW(), '+00:00', '-03:00')) >= 96 
                                      true {$filtro}
                                      ";
              $res = $linkclient->query($sql);

              $dataTemporal['data'][$keyFirst]['sql'] = $sql;

              if ($res && mysqli_num_rows($res) > 0) {

                foreach ($res as $key => $ulp) {

                  $sql2 = "SELECT * FROM historial_vehiculo WHERE his_patente = '{$ulp['veh_patente']}' ORDER BY his_fecha ASC";
                  $res2 = $link->query($sql2);
                  $comentarios = array();

                  if (mysqli_num_rows($res2) > 0) {
                    foreach ($res2 as $key2 => $data2) {
                      $comentarios[] = array(
                        'comentario' => ($data2['his_comentario'] == '' || $data2['his_comentario'] == null ? '' : $data2['his_comentario']),
                        'fecha' => ($data2['his_fechaupd'] == '' || $data2['his_fechaupd'] == null ? '-' : $data2['his_fechaupd']),
                        'id' => $data2['id_his'],
                        'eliminado' => 0,
                      );
                    }
                  }


                  $sql2 = "SELECT * FROM estado_vehiculos where eve_patente = '{$ulp['veh_patente']}'";
                  $res2 = $link->query($sql2);
                  $filaestado = mysqli_fetch_array($res2);
                  $estadoselectr = 0;
                  if ($filaestado['eve_fun'] == 1) {
                    $estadoselectr = 1;
                  } else if ($filaestado['eve_fun'] == 2) {
                    $estadoselectr = 2;
                  }

                  /*echo $ulp['tra_alias'].'<br>';*/
                  $arravehsintran[] = array(
                    //'idveh' => $ulp['veh_id'],
                    //'patente' => $ulp['veh_patente'],
                    //'dias' => $ulp['dias_transcurridos'],
                    'ulttransmision' => $ulp['ulp_fechahora'],
                    //'ultodocan' => ($ulp['veh_tiposerv'] == 1 ? $ulp['ulp_odometrocan'] : 0),
                    //'ultodolitro' => ($ulp['veh_tiposerv'] == 1 ? $ulp['ulp_odolitroscan'] : 0),
                    //'tiposervicio' => ($ulp['veh_tiposerv'] == 1 ? 'Avanzado' : 'Básico'),
                    //'transportistaalias' => ($ulp['tra_alias'] == null ||  $ulp['tra_alias'] == ''? '' : utf8_encode($ulp['tra_alias'])),
                    //'rstransportista' => ($ulp['tra_rsocial'] == null ||  $ulp['tra_rsocial'] == ''? '' : utf8_encode($ulp['tra_rsocial'])),
                    //'ruttransportista' => ($ulp['tra_rut'] == null ||  $ulp['tra_rut'] == ''? '' : $ulp['tra_rut']),
                    'imei' => ($ulp['veh_seriegps'] == null ||  $ulp['veh_seriegps'] == '' ? '' : $ulp['veh_seriegps']),
                    //'comentarios' => $comentarios,
                    //'estadoselectr' => $estadoselectr,
                  );
                  $cantidad++;

                  //$dataTemporal['data'][$keyFirst]['tiposervicio']  = ($ulp['veh_tiposerv'] == 1 ? 'Avanzado' : 'Básico');
                  //$dataTemporal['data'][$keyFirst]['comentarios']   = $comentarios;
                  $dataTemporal['data'][$keyFirst]['imei_cliente']  = ($ulp['veh_seriegps'] == null ||  $ulp['veh_seriegps'] == '' ? '' : $ulp['veh_seriegps']);
                  //$dataTemporal['data'][$keyFirst]['estadoselectr'] = $estadoselectr;

                  //$dataTemporal['data'][$keyFirst]['sql'] = $sql;

                  //$dataTemporal['data'][$keyFirst]['dias'] = $ulp['dias_transcurridos'];
                  $dataTemporal['data'][$keyFirst]['ulttransmision'] = $ulp['ulp_fechahora'];
                  //$dataTemporal['data'][$keyFirst]['ultodocan'] = ($ulp['veh_tiposerv'] == 1 ? $ulp['ulp_odometrocan'] : 0);
                  //$dataTemporal['data'][$keyFirst]['ultodolitro'] = ($ulp['veh_tiposerv'] == 1 ? $ulp['ulp_odolitroscan'] : 0);
                  //$dataTemporal['data'][$keyFirst]['transportistaalias'] = ($ulp['tra_alias'] == null ||  $ulp['tra_alias'] == ''? '' : utf8_encode($ulp['tra_alias']));
                  //$dataTemporal['data'][$keyFirst]['rstransportista'] = ($ulp['tra_rsocial'] == null ||  $ulp['tra_rsocial'] == ''? '' : utf8_encode($ulp['tra_rsocial']));
                  //$dataTemporal['data'][$keyFirst]['ruttransportista'] = ($ulp['tra_rut'] == null ||  $ulp['tra_rut'] == ''? '' : $ulp['tra_rut']);

                }
              }
            }
          }
        }
      }
    }

    $response = json_encode($dataTemporal);
    //exit;


    echo $response;
    break;



  case 'activeSim':
    $sql = "UPDATE equipos_asociados SET easi_estadosim={$_REQUEST['estado']} WHERE easi_id={$_REQUEST['id']}";
    $res = $link->query($sql);

    $response['status'] = 'ERROR';
    if ($res) {
      $response['status'] = 'OK';
    }
    echo json_encode($response);
    break;

  case 'asignarAsociacion':

    $fecha = date("Y-m-d H:i:s");
    /*$sql   = "INSERT INTO equipos_asociados(easi_idgps, easi_seriegps, easi_accesorio, easi_seriesim, easi_estadosim, easi_bodega, easi_user_create, easi_create_at) VALUES ({$_REQUEST['idgps']},'{$_REQUEST['serie1']}',{$_REQUEST['idacc']},'{$_REQUEST['serie2']}',1,{$_REQUEST['bodega']},{$_SESSION["cloux"]},'{$fecha}')";*/
    $sql   = "INSERT INTO equipos_asociados(easi_idgps, easi_seriegps, easi_accesorio, easi_seriesim, easi_estadosim, easi_bodega, easi_user_create, easi_create_at) VALUES ({$_REQUEST['idgps']},'{$_REQUEST['serie1']}',0,'',1,{$_REQUEST['bodega']},{$_SESSION["cloux_new"]},'{$fecha}')";
    $res = $link->query($sql);
    $id  = $link->insert_id;

    if ($res) {
      $response['status'] = 'OK';
      $response['id'] = $id;
    } else {
      $response['status'] = 'ERROR';
      $response['sql'] = $sql;
    }
    echo json_encode($response);
    break;

  case 'actualizarAsociacion':
    $fecha = date("Y-m-d H:i:s");
    $sql = "UPDATE equipos_asociados SET easi_idgps={$_REQUEST['idgps']}, easi_seriegps='{$_REQUEST['serie1']}', easi_accesorio={$_REQUEST['idacc']}, easi_seriesim='{$_REQUEST['serie2']}', easi_bodega={$_REQUEST['bodega']} WHERE easi_id={$_REQUEST['idup']}";
    $res = $link->query($sql);
    if ($res) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
    }
    echo json_encode($response);
    break;

  case 'borrarAsociacion':
    $sql = "DELETE FROM equipos_asociados WHERE easi_id=" . $_REQUEST['idasoc'];
    $res = $link->query($sql);

    if ($res) {
      $response['status'] = 'OK';
    } else {
      $response['status'] = 'ERROR';
    }
    echo json_encode($response);
    break;

  case 'setTipoEstado':
    $sql = "SELECT * from sensores_estado WHERE sene_idasoc={$_REQUEST['idasoc']}";
    $res = $link->query($sql);
    $cant = mysqli_num_rows($res);
    if ($cant > 0) {
      while ($fila = mysqli_fetch_array($res)) {
        $sql = "UPDATE sensores_estado SET sene_senestado={$_REQUEST['estado']} WHERE sene_idasoc={$_REQUEST['idasoc']} AND sene_idsensor={$_REQUEST['idsensor']}";
        $res = $link->query($sql);
        if ($res) {
          $response['status'] = 'OK';
        } else {
          $response['status'] = 'ERROR';
        }
      }
    } else {
      $fecha = date("Y-m-d H:i:s");
      $sql = "INSERT INTO sensores_estado(sene_idasoc, sene_idsensor, sene_senestado, sene_create_at) VALUES({$_REQUEST['idasoc']},{$_REQUEST['idsensor']},{$_REQUEST['estado']},'{$fecha}')";
      $res = $link->query($sql);
      if ($res) {
        $response['status'] = 'OK';
      } else {
        $response['status'] = 'ERROR';
      }
    }
    mysqli_close($link);
    echo json_encode($response);
    break;

    //Ordenados por AKSM
  case 'eliminartraspaso':

    $recibe = json_decode($_REQUEST["envio"], true);
    $sql    = "UPDATE traspasos_series SET tra_estado = 0 WHERE tra_id = {$recibe['id']}";
    $res    = $link->query($sql);
    if ($res) {
      $devuelve = array('mensaje' => 'Traspaso eliminado', 'logo' => 'success');
    } else {
      $devuelve = array('mensaje' => 'Error al eliminar traspaso', 'logo' => 'danger');
    }
    mysqli_close($link);
    echo json_encode($devuelve);

    break;

  case 'mailprueba':

    $recibe = json_decode($_REQUEST["envio"], true);

    $sqldet   = "SELECT t1.* , t2.veh_patente, t2.veh_cliente, t2.veh_rsocial, t3.cuenta, t3.razonsocial, t3.correo, concat(t4.per_nombrecorto,' ',t4.per_apaterno) as nombretecnico, t5.ttra_nombre, t3.direccion
            FROM tickets t1
            INNER JOIN vehiculos t2 on t2.veh_id = t1.tic_patente
            LEFT OUTER JOIN clientes t3 on t3.id = t2.veh_cliente
            LEFT OUTER JOIN personal t4 on t4.per_id = t1.tic_tecnico
            LEFT OUTER JOIN tiposdetrabajos t5 on t5.ttra_id = t1.tic_tipotrabajo
            where t1.tic_id = 591 and t2.deleted_at is NULL";
    $resdet    = $link->query($sqldet);
    $datos = array();
    if ($resdet && mysqli_num_rows($resdet) > 0) {
      $filacli = mysqli_fetch_array($resdet);
      if ($filacli['correo'] != '' && $filacli['correo'] != null) {
        $fechachilebuen = date("d-m-Y H:i:s");
        $imeitic = "";
        $myString = "";
        $sqlpv = "SELECT * FROM productosxvehiculos where pxv_idveh = {$filacli['tic_patente']}";
        $respv = $link->query($sqlpv);
        if ($respv && mysqli_num_rows($respv) > 0) {
          foreach ($respv as $keypv => $datapv) {
            $imeitic .= $datapv['pxv_nserie'] . ',';
          }
          $myString = substr($imeitic, 0, -1);
        }

        $datos[] = array(
          'fechatrabajo' => $fechachilebuen,
          'tecnico'      => ($filacli['nombretecnico'] == null || $filacli['nombretecnico'] == '' ? '-' : $filacli['nombretecnico']),
          'tipotrabajo'  => ($filacli['ttra_nombre'] == null || $filacli['ttra_nombre'] == '' ? '-' : $filacli['ttra_nombre']),
          'cliente'      => ($filacli['cuenta'] == null || $filacli['cuenta'] == '' ? '-' : $filacli['cuenta']),
          'ndispositivo' => $myString,
          'direccion'    => ($filacli['tic_lugar'] == null || $filacli['tic_lugar'] == '' ? 'N/A' : $filacli['tic_lugar']),
          'correo'       => $filacli['correo'],
          'patente'      => $filacli['veh_patente'],
        );

        enviaemail($datos);
      }
    }

    break;
}


function actualizarAccesorios($ticketID, $accesorios, $link) {
  // Verificar si los accesorios no están vacíos
  if (!empty($accesorios)) {
      $tiene_accesorios = count($accesorios);

      // Eliminar los accesorios anteriores asociados al ticket (eliminación suave)
      $updateAccesorios = "UPDATE `ticket_accesorios` 
                           SET `deleted_at` = NOW() 
                           WHERE `ticket_id` = '$ticketID' AND `deleted_at` IS NULL";
      $link->query($updateAccesorios);

      // Insertar nuevos accesorios para el ticket
      foreach ($accesorios as $accesorio) {    
          $idAccesorio = htmlspecialchars($accesorio);
          $insertAccesorio = "INSERT INTO `ticket_accesorios`(`ticket_id`, `accesorio_id`) 
                              VALUES ('$ticketID','$idAccesorio')";
          $link->query($insertAccesorio);
      }
  } else {
      // Si no hay accesorios, se marca como 0
      $tiene_accesorios = 0;
  }

  // Actualizar el campo tiene_accesorios en la tabla tickets
  $updateTicket = "UPDATE `tickets`
                   SET `tiene_accesorios` = $tiene_accesorios 
                   WHERE `tic_id` = '$ticketID'";
  $link->query($updateTicket);
}


function enviaemail($datos)
{

  $fecha        = $datos[0]['fechatrabajo'];
  $tecnico      = $datos[0]['tecnico'];
  $tipotrabajo  = $datos[0]['tipotrabajo'];
  $cliente      = $datos[0]['cliente'];
  $ndispositivo = $datos[0]['ndispositivo'];
  $direccion    = $datos[0]['direccion'];
  $correo       = $datos[0]['correo'];
  $patente      = $datos[0]['patente'];

  $plantilla = '<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta charset="utf-8"/>
    <title></title>
    <style>
        table, td, div, h1, p {font-family: Arial, sans-serif;}
        .badge{display:inline-block;padding:.25em .4em;font-size:75%;font-weight:700;line-height:1;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25rem;transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out}@media (prefers-reduced-motion:reduce){.badge{transition:none}}a.badge:focus,a.badge:hover{text-decoration:none}.badge:empty{display:none}.btn .badge{position:relative;top:-1px}.badge-pill{padding-right:.6em;padding-left:.6em;border-radius:10rem}.badge-primary{color:#fff;background-color:#007bff}a.badge-primary:focus,a.badge-primary:hover{color:#fff;background-color:#0062cc}a.badge-primary.focus,a.badge-primary:focus{outline:0;box-shadow:0 0 0 .2rem rgba(0,123,255,.5)}.badge-secondary{color:#fff;background-color:#6c757d}a.badge-secondary:focus,a.badge-secondary:hover{color:#fff;background-color:#545b62}a.badge-secondary.focus,a.badge-secondary:focus{outline:0;box-shadow:0 0 0 .2rem rgba(108,117,125,.5)}.badge-success{color:#fff;background-color:#28a745}a.badge-success:focus,a.badge-success:hover{color:#fff;background-color:#1e7e34}a.badge-success.focus,a.badge-success:focus{outline:0;box-shadow:0 0 0 .2rem rgba(40,167,69,.5)}.badge-info{color:#fff;background-color:#17a2b8}a.badge-info:focus,a.badge-info:hover{color:#fff;background-color:#117a8b}a.badge-info.focus,a.badge-info:focus{outline:0;box-shadow:0 0 0 .2rem rgba(23,162,184,.5)}.badge-warning{color:#212529;background-color:#ffc107}a.badge-warning:focus,a.badge-warning:hover{color:#212529;background-color:#d39e00}a.badge-warning.focus,a.badge-warning:focus{outline:0;box-shadow:0 0 0 .2rem rgba(255,193,7,.5)}.badge-danger{color:#fff;background-color:#dc3545}a.badge-danger:focus,a.badge-danger:hover{color:#fff;background-color:#bd2130}a.badge-danger.focus,a.badge-danger:focus{outline:0;box-shadow:0 0 0 .2rem rgba(220,53,69,.5)}.badge-light{color:#212529;background-color:#f8f9fa}a.badge-light:focus,a.badge-light:hover{color:#212529;background-color:#dae0e5}a.badge-light.focus,a.badge-light:focus{outline:0;box-shadow:0 0 0 .2rem rgba(248,249,250,.5)}.badge-dark{color:#fff;background-color:#343a40}a.badge-dark:focus,a.badge-dark:hover{color:#fff;background-color:#1d2124}a.badge-dark.focus,a.badge-dark:focus{outline:0;box-shadow:0 0 0 .2rem rgba(52,58,64,.5)}
        table{border-collapse:collapse}.table{width:100%;margin-bottom:1rem;color:#212529}.table td,.table th{padding:.75rem;vertical-align:top;border-top:1px solid #dee2e6}.table thead th{vertical-align:bottom;border-bottom:2px solid #dee2e6}.table tbody+tbody{border-top:2px solid #dee2e6}.table-sm td,.table-sm th{padding:.3rem}.table-bordered{border:1px solid #dee2e6}.table-bordered td,.table-bordered th{border:1px solid #dee2e6}.table-bordered thead td,.table-bordered thead th{border-bottom-width:2px}.table-borderless tbody+tbody,.table-borderless td,.table-borderless th,.table-borderless thead th{border:0}.table-striped tbody tr:nth-of-type(odd){background-color:rgba(0,0,0,.05)}.table-hover tbody tr:hover{color:#212529;background-color:rgba(0,0,0,.075)}.table-primary,.table-primary>td,.table-primary>th{background-color:#b8daff}.table-primary tbody+tbody,.table-primary td,.table-primary th,.table-primary thead th{border-color:#7abaff}.table-hover .table-primary:hover{background-color:#9fcdff}.table-hover .table-primary:hover>td,.table-hover .table-primary:hover>th{background-color:#9fcdff}.table-secondary,.table-secondary>td,.table-secondary>th{background-color:#d6d8db}.table-secondary tbody+tbody,.table-secondary td,.table-secondary th,.table-secondary thead th{border-color:#b3b7bb}.table-hover .table-secondary:hover{background-color:#c8cbcf}.table-hover .table-secondary:hover>td,.table-hover .table-secondary:hover>th{background-color:#c8cbcf}.table-success,.table-success>td,.table-success>th{background-color:#c3e6cb}.table-success tbody+tbody,.table-success td,.table-success th,.table-success thead th{border-color:#8fd19e}.table-hover .table-success:hover{background-color:#b1dfbb}.table-hover .table-success:hover>td,.table-hover .table-success:hover>th{background-color:#b1dfbb}.table-info,.table-info>td,.table-info>th{background-color:#bee5eb}.table-info tbody+tbody,.table-info td,.table-info th,.table-info thead th{border-color:#86cfda}.table-hover .table-info:hover{background-color:#abdde5}.table-hover .table-info:hover>td,.table-hover .table-info:hover>th{background-color:#abdde5}.table-warning,.table-warning>td,.table-warning>th{background-color:#ffeeba}.table-warning tbody+tbody,.table-warning td,.table-warning th,.table-warning thead th{border-color:#ffdf7e}.table-hover .table-warning:hover{background-color:#ffe8a1}.table-hover .table-warning:hover>td,.table-hover .table-warning:hover>th{background-color:#ffe8a1}.table-danger,.table-danger>td,.table-danger>th{background-color:#f5c6cb}.table-danger tbody+tbody,.table-danger td,.table-danger th,.table-danger thead th{border-color:#ed969e}.table-hover .table-danger:hover{background-color:#f1b0b7}.table-hover .table-danger:hover>td,.table-hover .table-danger:hover>th{background-color:#f1b0b7}.table-light,.table-light>td,.table-light>th{background-color:#fdfdfe}.table-light tbody+tbody,.table-light td,.table-light th,.table-light thead th{border-color:#fbfcfc}.table-hover .table-light:hover{background-color:#ececf6}.table-hover .table-light:hover>td,.table-hover .table-light:hover>th{background-color:#ececf6}.table-dark,.table-dark>td,.table-dark>th{background-color:#c6c8ca}.table-dark tbody+tbody,.table-dark td,.table-dark th,.table-dark thead th{border-color:#95999c}.table-hover .table-dark:hover{background-color:#b9bbbe}.table-hover .table-dark:hover>td,.table-hover .table-dark:hover>th{background-color:#b9bbbe}.table-active,.table-active>td,.table-active>th{background-color:rgba(0,0,0,.075)}.table-hover .table-active:hover{background-color:rgba(0,0,0,.075)}.table-hover .table-active:hover>td,.table-hover .table-active:hover>th{background-color:rgba(0,0,0,.075)}.table .thead-dark th{color:#fff;background-color:#343a40;border-color:#454d55}.table .thead-light th{color:#495057;background-color:#e9ecef;border-color:#dee2e6}.table-dark{color:#fff;background-color:#343a40}.table-dark td,.table-dark th,.table-dark thead th{border-color:#454d55}.table-dark.table-bordered{border:0}.table-dark.table-striped tbody tr:nth-of-type(odd){background-color:rgba(255,255,255,.05)}.table-dark.table-hover tbody tr:hover{color:#fff;background-color:rgba(255,255,255,.075)}@media (max-width:575.98px){.table-responsive-sm{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-sm>.table-bordered{border:0}}@media (max-width:767.98px){.table-responsive-md{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-md>.table-bordered{border:0}}@media (max-width:991.98px){.table-responsive-lg{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-lg>.table-bordered{border:0}}@media (max-width:1199.98px){.table-responsive-xl{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-xl>.table-bordered{border:0}}.table-responsive{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive>.table-bordered{border:0}
        .d-table{display:table!important}.d-table-row{display:table-row!important}.d-table-cell{display:table-cell!important}.d-flex{display:-ms-flexbox!important;display:flex!important}.d-inline-flex{display:-ms-inline-flexbox!important;display:inline-flex!important}@media (min-width:576px){.d-sm-none{display:none!important}.d-sm-inline{display:inline!important}.d-sm-inline-block{display:inline-block!important}.d-sm-block{display:block!important}.d-sm-table{display:table!important}.d-sm-table-row{display:table-row!important}.d-sm-table-cell{display:table-cell!important}.d-sm-flex{display:-ms-flexbox!important;display:flex!important}.d-sm-inline-flex{display:-ms-inline-flexbox!important;display:inline-flex!important}}@media (min-width:768px){.d-md-none{display:none!important}.d-md-inline{display:inline!important}.d-md-inline-block{display:inline-block!important}.d-md-block{display:block!important}.d-md-table{display:table!important}.d-md-table-row{display:table-row!important}.d-md-table-cell{display:table-cell!important}.d-md-flex{display:-ms-flexbox!important;display:flex!important}.d-md-inline-flex{display:-ms-inline-flexbox!important;display:inline-flex!important}}@media (min-width:992px){.d-lg-none{display:none!important}.d-lg-inline{display:inline!important}.d-lg-inline-block{display:inline-block!important}.d-lg-block{display:block!important}.d-lg-table{display:table!important}.d-lg-table-row{display:table-row!important}.d-lg-table-cell{display:table-cell!important}.d-lg-flex{display:-ms-flexbox!important;display:flex!important}.d-lg-inline-flex{display:-ms-inline-flexbox!important;display:inline-flex!important}}@media (min-width:1200px){.d-xl-none{display:none!important}.d-xl-inline{display:inline!important}.d-xl-inline-block{display:inline-block!important}.d-xl-block{display:block!important}.d-xl-table{display:table!important}.d-xl-table-row{display:table-row!important}.d-xl-table-cell{display:table-cell!important}.d-xl-flex{display:-ms-flexbox!important;display:flex!important}.d-xl-inline-flex{display:-ms-inline-flexbox!important;display:inline-flex!important}}@media print{.d-print-none{display:none!important}.d-print-inline{display:inline!important}.d-print-inline-block{display:inline-block!important}.d-print-block{display:block!important}.d-print-table{display:table!important}.d-print-table-row{display:table-row!important}.d-print-table-cell{display:table-cell!important}}
        .bg-primary{
            background-color: #0275d8;
        }
        .bg-success{
            background-color: #5cb85c;
        }
        .bg-info{
            background-color: #5bc0de;
        }
        .bg-warning{
            background-color: #f0ad4e;
        }
        .bg-danger{
            background-color: #d9534f;
        }
        .bg-secondary{
            background-color: #292b2c;
        }
        .bg-white{
            background-color: #f7f7f7;
        }
        .text-white{
            color: #f7f7f7;
        }
        .w-100{
            width: 100% !important;
        }
        .m-0{
            margin: 0px;
        }
        .p-1{
            padding: 4px;
        }
        body{
            font-family:SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
        }
        .card {
            /* Add shadows to create the "card" effect */
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            transition: 0.3s;
            border-radius: 4px;
        }
        /* On mouse-over, add a deeper shadow */
        .card:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
        }
        .card-header{
            border-radius: 4px 4px 0px 0px;
        }
        /* Add some padding inside the card container */
        .card-body {
        padding: 2px 16px;
        }
        @media screen and (max-width: 530px) {
          .unsub {
            display: block;
            padding: 8px;
            margin-top: 14px;
            border-radius: 6px;
            background-color: #555555;
            text-decoration: none !important;
            font-weight: bold;
          }
          .col-lge {
            max-width: 100% !important;
          }
        }
        @media screen and (min-width: 531px) {
          .col-sml {
            max-width: 27% !important;
          }
          .col-lge {
            max-width: 73% !important;
          }
        }
    </style>
    </head>
    <body>
        <div role="article" aria-roledescription="email" lang="es" style="text-size-adjust:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;background-color:#939297;">
            <table role="presentation" style="width:100%;height: 1200px;border:none;border-spacing:0;">
              <tr>
                <td align="center" style="padding:0;">
                  <table role="presentation" style="width:94%;border:none;border-spacing:0;text-align:left;font-family:Arial,sans-serif;font-size:16px;line-height:22px;color:#363636;">
                    <tr>
                      <td style="padding:0px 30px 30px 30px;text-align:center;font-size:24px;font-weight:bold;">
                        <a href="#" style="text-decoration:none;"><img src="http://18.234.82.208/admin/dist/img/Imagen1.png" width="250" alt="Logo" style="width:165px;max-width:80%;height:auto;border:none;text-decoration:none;color:#ffffff;"></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:30px;background-color:#ffffff;">
                        <h1 style="margin-top:0;margin-bottom:16px;font-size:26px;line-height:32px;font-weight:bold;letter-spacing:-0.02em;">Estimado(a), enviamos correo de respaldo de trabajo realizado.</h1>
                        <table cellspacing="0" cellpadding="0" style="width:100%; padding: 10px;">
                            <tr>
                                <td align="center" style="border:1px black solid; font-weight:bold;">Fecha de trabajo</td>
                                <td align="center" style="border:1px black solid; font-weight:bold;">Técnico visitante</td>
                                <td align="center" style="border:1px black solid; font-weight:bold;">Tipo de trabajo</td>
                                <td align="center" style="border:1px black solid; font-weight:bold;">Cliente</td>
                                <td align="center" style="border:1px black solid; font-weight:bold;">Ubicación</td>
                                <td align="center" style="border:1px black solid; font-weight:bold;">Patente</td>
                                <td align="center" style="border:1px black solid; font-weight:bold;">Serie Dispositivo</td>
                            </tr>
                            <tr>
                                <td align="center" style="border:1px black solid;">' . $fecha . '</td>
                                <td align="center" style="border:1px black solid;">' . $tecnico . '</td>
                                <td align="center" style="border:1px black solid;">' . $tipotrabajo . '</td>
                                <td align="center" style="border:1px black solid;">' . $cliente . '</td>
                                <td align="center" style="border:1px black solid;">' . $direccion . '</td>
                                <td align="center" style="border:1px black solid;">' . $patente . '</td>
                                <td align="center" style="border:1px black solid;">' . $ndispositivo . '</td>
                            </tr>
                        </table>
                        <!--<p style="margin:0;"><a href="https://example.com/" class="bg-primary" style=" text-decoration: none; padding: 10px 25px; color: #ffffff; border-radius: 4px; display:inline-block; mso-padding-alt:0;text-underline-color:#ff3884"><span style="mso-text-raise:10pt;font-weight:bold;">Ver Detalle</span></a></p>-->
                        </td>
                    </tr>
                    <!--<tr>
                      <td style="padding:0;font-size:24px;line-height:28px;font-weight:bold;">
                        <a href="#" style="text-decoration:none;"><img src="https://assets.codepen.io/210284/1200x800-2.png" width="600" alt="" style="width:100%;height:auto;display:block;border:none;text-decoration:none;color:#363636;"></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:35px 30px 11px 30px;font-size:0;background-color:#ffffff;border-bottom:1px solid #f0f0f5;border-color:rgba(201,201,207,.35);">
                        <div class="col-sml" style="display:inline-block;width:100%;max-width:145px;vertical-align:top;text-align:left;font-family:Arial,sans-serif;font-size:14px;color:#363636;">
                          <img src="https://assets.codepen.io/210284/icon.png" width="115" alt="" style="width:115px;max-width:80%;margin-bottom:20px;">
                        </div>
                        <div class="col-lge" style="display:inline-block;width:100%;max-width:395px;vertical-align:top;padding-bottom:20px;font-family:Arial,sans-serif;font-size:16px;line-height:22px;color:#363636;">
                          <p style="margin-top:0;margin-bottom:12px;">Nullam mollis sapien vel cursus fermentum. Integer porttitor augue id ligula facilisis pharetra. In eu ex et elit ultricies ornare nec ac ex. Mauris sapien massa, placerat non venenatis et, tincidunt eget leo.</p>
                          <p style="margin-top:0;margin-bottom:18px;">Nam non ante risus. Vestibulum vitae eleifend nisl, quis vehicula justo. Integer viverra efficitur pharetra. Nullam eget erat nibh.</p>
                          <p style="margin:0;"><a href="https://example.com/" class="bg-primary" style=" text-decoration: none; padding: 10px 25px; color: #ffffff; border-radius: 4px; display:inline-block; mso-padding-alt:0;text-underline-color:#ff3884"><span style="mso-text-raise:10pt;font-weight:bold;">Ver Detalle</span></a></p>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:30px;font-size:24px;line-height:28px;font-weight:bold;background-color:#ffffff;border-bottom:1px solid #f0f0f5;border-color:rgba(201,201,207,.35);">
                        <a href="#" style="text-decoration:none;"><img src="https://assets.codepen.io/210284/1200x800-1.png" width="540" alt="" style="width:100%;height:auto;border:none;text-decoration:none;color:#363636;"></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:30px;background-color:#ffffff;">
                        <p style="margin:0;">Duis sit amet accumsan nibh, varius tincidunt lectus. Quisque commodo, nulla ac feugiat cursus, arcu orci condimentum tellus, vel placerat libero sapien et libero. Suspendisse auctor vel orci nec finibus.</p>
                      </td>
                    </tr>
                    <tr>-->
                      <td style="padding:30px;text-align:center;font-size:12px;background-color:#404040;color:#cccccc;">
                        <!--<p style="margin:0 0 8px 0;"><a href="http://www.facebook.com/" style="text-decoration:none;"><img src="https://assets.codepen.io/210284/facebook_1.png" width="40" height="40" alt="f" style="display:inline-block;color:#cccccc;"></a> <a href="http://www.twitter.com/" style="text-decoration:none;"><img src="https://assets.codepen.io/210284/twitter_1.png" width="40" height="40" alt="t" style="display:inline-block;color:#cccccc;"></a></p>-->
                        <p style="margin:0;font-size:14px;line-height:20px;">&reg; Derechos reservados D-Solutions 2022<br><!--<a class="unsub" href="#" style="color:#cccccc;text-decoration:underline;">Unsubscribe instantly</a>--></p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </div>
    </body>
    </html>';

  $nom        = $cliente;
  $mail       = new PHPMailer(true);
  $mail->SMTPDebug = 0;
  $mail->isSMTP();
  //$mail->Host = "smtp.gmail.com";
  //$mail->SMTPAuth = true;                            
  /*$mail->Username = "noreply.dsolutionscl@gmail.com";                 
    $mail->Password = "gjgnldjxudvamfqc";   */
  //$mail->Username = "informes@d-solutions.cl";                 
  //$mail->Password = "zkeecgvydghvsgzq";                      
  //$mail->SMTPSecure = "tls";                           
  //$mail->Port = 587;                                   
  //$mail->From = $correo;

  $mail->SMTPAuth = true;
  $mail->SMTPSecure = "tls";

  //$mail->Host = "smtp.gmail.com";
  //$mail->Username = "informes@d-solutions.cl";                 
  //$mail->Password = "zkeecgvydghvsgzq";                                              
  //$mail->Port = 587;                                   
  //$mail->From = $correo;

  if (false) {
    $mail->Host = "smtp.gmail.com";
    $mail->Username = "informes@d-solutions.cl";
    $mail->Password = "zkeecgvydghvsgzq";
    $mail->Port = 587;
    $mail->From = $correo;
  } else {
    $mail->Host = "smtp.resend.com";
    $mail->Username = "resend";
    $mail->Password = "re_iHKLmTpb_EXMeMFU2UtwxhSewqzpcVNkk";
    $mail->Port = 587;
    $mail->From = "informes@d-solutions.cl";
  }

  $mail->FromName = "Finalización de trabajo";
  $mail->addAddress($correo, $nom);

  // Agrega otra copia de correo electrónico
  $mail->addAddress('jfigueroa@d-solutions.cl', 'Jaime Figueroa');

  $mail->isHTML(true);
  $mail->Subject = "Finalización de trabajo";
  $mensaje = utf8_decode($plantilla);
  $mail->msgHTML($mensaje);
  $mail->AltBody = "Notificacion";
  $mail->CharSet = 'UTF-8';

  $_bbddclient3 = strtolower('cloux');
  if ($bbddclient3 != '') {
    $_bbddclient3 = $bbddclient3;
  }

  $link = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', $_bbddclient3);

  if (mysqli_connect_errno()) {
    printf("Falló la conexión: %s\n", mysqli_connect_error());
    exit();
  }

  mysqli_set_charset($link, "utf8");

  try {
    $mail->send();

    $insertCorreo = "INSERT INTO log_correos(author_id, estado, de, a, asunto, mensaje, empresa) 
      values ('0','success','informes@d-solutions.cl','{$correo}','Finalización de trabajo','{$plantilla}','{$cliente}')";
    $res2 = $link->query($insertCorreo);
  } catch (Exception $e) {
    //echo "Error: " . $mail->ErrorInfo;
    $insertCorreo = "INSERT INTO log_correos(author_id, estado, de, a, asunto, mensaje, empresa, mensaje_error) 
      values ('0','error','informes@d-solutions.cl','{$correo}','Finalización de trabajo','{$plantilla}','{$cliente}','" . $mail->ErrorInfo . "')";
    $res2 = $link->query($insertCorreo);
  }

  /*if(!$mail->Send()) {
      $insertCorreo    = "INSERT INTO log_correos(author_id, estado, de, a, asunto, mensaje, empresa) 
      values ('0','error','informes@d-solutions.cl','{$correo}','Finalización de trabajo','{$plantilla}','{$cliente}')";
      $res2 = $link->query($insertCorreo); 
    }else {
      $insertCorreo    = "INSERT INTO log_correos(author_id, estado, de, a, asunto, mensaje, empresa) 
      values ('0','success','informes@d-solutions.cl','{$correo}','Finalización de trabajo','{$plantilla}','{$cliente}')";
      $res2 = $link->query($insertCorreo); 
    }*/
}

function enviaremailMoroso($correomoroso, $nombreMor)
{


  $plantilla  = "<p>Estimado $nombreMor:</p>
				  <p> Su cuenta ha sido bloqueada por presentar morosidad, favor comuníquese con nuestra area de administración</p>";

  //$path  = $title;
  $nombrecorr = $nombreMor;
  $mail       = new PHPMailer(true);
  //$mail->SMTPDebug = 1;                               
  $mail->isSMTP();

  //$mail->Host = "smtp.gmail.com";
  //$mail->SMTPAuth = true;                            
  //$mail->Username = "informes@d-solutions.cl";                 
  //$mail->Password = "zkeecgvydghvsgzq";                           
  //$mail->SMTPSecure = "tls";                           
  //$mail->Port = 587;                                   
  //$mail->From = $correomoroso;

  $mail->SMTPAuth = true;
  $mail->SMTPSecure = "tls";

  //$mail->Host = "smtp.gmail.com";
  //$mail->Username = "informes@d-solutions.cl";                 
  //$mail->Password = "zkeecgvydghvsgzq";                                              
  //$mail->Port = 587;                                   
  //$mail->From = $correo;

  if (false) {
    $mail->Host = "smtp.gmail.com";
    $mail->Username = "informes@d-solutions.cl";
    $mail->Password = "zkeecgvydghvsgzq";
    $mail->Port = 587;
    $mail->From = $correomoroso;
  } else {
    $mail->Host = "smtp.resend.com";
    $mail->Username = "resend";
    $mail->Password = "re_iHKLmTpb_EXMeMFU2UtwxhSewqzpcVNkk";
    $mail->Port = 587;
    $mail->From = "informes@d-solutions.cl";
  }

  $arrayCorreosbloqueados = array("rrojas@factorambiental.cl", "rrojas@factorambiental.cl", "rrojas@factorambiental.cl");
  if (in_array($correomoroso, $arrayCorreosbloqueados)) {
    return true;
  }

  $mail->FromName = "Soporte TI DSolutions";
  $mail->addAddress($correomoroso, $nombrecorr);
  $mail->isHTML(true);
  //$mail->AddAttachment($path);
  $mail->Subject = "Morosidad";
  $mensaje = utf8_decode($plantilla);
  $mail->msgHTML($mensaje);
  $mail->AltBody = 'Morosidad';

  try {
    $mail->send();
  } catch (Exception $e) {
    echo "Error: " . $mail->ErrorInfo;
  }
}


/*function cellColor($cells,$color){
    global $objPHPExcel;
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => $color),));
}

function celltextcolor($cells,$size=15,$colortext='FF0000'){
    global $objPHPExcel;
    $styleArray = array(
        'font'  => array('bold'  => true,'color' => array('rgb' => $colortext),'size'  => $size,'name'  => 'Verdana'));
    $objPHPExcel->getActiveSheet()->getStyle($cells)->applyFromArray($styleArray);
}*/

if ($_REQUEST["retornar"] != "no") {
  header("location:" . $sale_a . "");
}
