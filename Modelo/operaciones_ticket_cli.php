<?php
ini_set('max_execution_time', '0');
session_start();
include("../conexion.php");
include("../funciones.php");
require_once '../lib/phpmailer/PHPMailerAutoload.php';
require_once "../../admin/dompdf2-0/lib/html5lib/Parser.php";
require_once "../../admin/dompdf2-0/lib/php-font-lib/src/FontLib/Autoloader.php";
require_once "../../admin/dompdf2-0/lib/php-svg-lib/src/autoload.php";
require_once "../../admin/dompdf2-0/src/Autoloader.php";
require_once '../../admin/dompdf2-0/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

switch ($_REQUEST['operacion']) {
    case 'getOTPDF':
        //$_REQUEST['idticket'] = 854;

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
        while($fila=mysqli_fetch_array($res)){
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
        while($fila1=mysqli_fetch_array($res1)){
            $tipo = '';
            $ntipo = '';
            if($fila1['timg_tipo']==0){
                $ntipo = 'Previa';
                if($fila1['timg_subtipo']==1){ $tipo = 'F. Patente'; }
                if($fila1['timg_subtipo']==2){ $tipo = 'T. Instrumento'; }
                if($fila1['timg_subtipo']==3){ $tipo = 'P. Tablero'; }
                if($fila1['timg_subtipo']==4){ $tipo = 'D. Daños'; }
            }
            if($fila1['timg_tipo']==1){
                $ntipo = 'Posterior';
                if($fila1['timg_subtipo']==1){ $tipo = 'T. Instrumento'; }
                if($fila1['timg_subtipo']==2){ $tipo = 'Puntos Conexión'; }
                if($fila1['timg_subtipo']==3){ $tipo = 'V. Panorámica'; }
                if($fila1['timg_subtipo']==4){ $tipo = 'U. Equipo'; }
            }
            $imgTrab[] = array(
                'id'=>$fila1['timg_id'],
                'tipo'=>$ntipo,
                'ntipo'=>$tipo,
                'idtipo'=>$fila1['timg_tipo'],
                'idsubtipo'=>$fila1['timg_subtipo'],
                'img'=>$fila1['timg_name']
            );
        }

        $accesorios = array();
        $sql1 = "SELECT ava_id, ava_idveh, ava_idguia, ava_serie, ava_estado FROM asociacion_vehiculos_accesorios WHERE ava_estado=1 AND ava_idveh='{$idveh}'";
        $res1 = $link->query($sql1);
        while($fila1=mysqli_fetch_array($res1)){
            $npro = "";
            $sql2 = "SELECT pro.pro_nombre FROM serie_guia ser INNER JOIN productos pro ON pro.pro_id=ser.pro_id WHERE ser_id='{$fila1['ava_idguia']}'";
            $res2 = $link->query($sql2);
            while($fila2=mysqli_fetch_array($res2)){
                $npro = $fila2['pro_nombre'];
            }
            $accesorios[] = array(
                'ser_id'=>$fila1['ava_idguia'],
                'ser_codigo'=>$fila1['ava_serie'],
                'pro_nombre'=>$npro,
            );
        }

        $nserieCan = '';
        if($idveh!='' && $idveh!=null && $idveh!=0){
            $sql1 = "SELECT ser_idcan FROM asociacion_vehiculos_sensores WHERE veh_id='{$idveh}'";
            $res1 = $link->query($sql1);
            while($fila1=mysqli_fetch_array($res1)){
                $sql2 = "SELECT ser_codigo FROM serie_guia WHERE ser_id='{$fila1['ser_idcan']}'";
                $res2 = $link->query($sql2);
                if(mysqli_num_rows($res2)>0){
                    $ser_id1=mysqli_fetch_array($res2);
                    $nserieCan = $ser_id1['ser_codigo'];
                }
            }
        }

        $nserie = '';
        $sql = "SELECT pxv_nserie FROM productosxvehiculos WHERE pxv_estado=1 AND pxv_idveh='{$idveh}'";
        $res = $link->query($sql);
        while($fila=mysqli_fetch_array($res)){
            $nserie = $fila['pxv_nserie'];
        }

        $kms = 0;
        $origen = "";
        $destino = "";
        $sql1 = "SELECT mcom_kms,mcom_comorigen,mcom_comdestino FROM matriz_comunas WHERE mcom_idorigen='{$idorigen}' AND mcom_iddestino='{$iddestino}'";
        $res1 = $link->query($sql1);
        while($fila1=mysqli_fetch_array($res1)){
            $kms = $fila1["mcom_kms"];
            $origen = $fila1["mcom_comorigen"];
            $destino = $fila1["mcom_comdestino"];
        }
        if($kms==null || $kms=='' || $kms=='0'){
            $kms = "N/A";
        }

        $acc = '';
        foreach ($accesorios as $key => $value) {
            $acc = $value['pro_nombre']+($value['ser_codigo']!="" ? "("+$value['ser_codigo']+")" : "")+", ";
        }

        $fecha = date('d-m-Y H:i:s');
        $fields = array(
            'fecha' => $fecha,
            'nticket' => $_REQUEST['idticket'],
            'tecnico' => $tecnico,
            'nombre' => $nombrefirma,
            'patente' =>$patente,
            'fhlabor' => $fhlabor,
            'ttrabajo' => $ttrabajo,
            'tservicio' => $tservicio,
            'img'=> $imgTrab,
            'accesorios' => $acc,
            'nserie' => ($nserie==null ? 'N/A' : $nserie),
            'nserieCan' => ($nserieCan==null ? 'N/A' : $nserieCan),
            'origen' => ($origen==null ? 'N/A' : $origen),
            'destino' => ($destino==null ? 'N/A' : $destino),
            'kms' => ($kms==null ? 'N/A' : $kms),
            'firmaTec' => $firmaTec,
            'firmaCli' => $firmaCli,
            'descripcion'=> $descripcion,
            'comentario'=> $comentario,
            'cliente' => $cliente,
        );
        $fields_string = http_build_query($fields);
        $url = 'http://18.234.82.208/cloux/pdfOt.php';
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
        $base64pdf=base64_encode($pdf);

        $target_file = '../archivos/';

		$name = strtotime(date('Y-m-d H:i:s')).'_'.$cliente.'.pdf';
		$ruta = $target_file.$name;
        $cuerpo = "<p>Estimado Cliente(a):</p>
        <p>A continuación adjuntamos detalle de servicio realizado el ".(date('d-m-Y',strtotime($fhlabor)))." en vehiculo ".$patente." en OT ".$_REQUEST['idticket']." , tipo labor ".$ttrabajo.".</p>
        <p>Este correo se genera de forma automatica por lo tal motivo no responder.</p>
          <p>Ante cualquier solicitud o dudas relacionado con el presente correo, por favor escriba a clientes@d-solutions.cl</p>";
		if(file_put_contents($ruta, base64_decode($base64pdf))){
            $sql = "SELECT nombre, correo FROM contactoclientes WHERE cliente='{$idcliente}' AND correo IS NOT NULL AND correo!=''";
            $res = $link->query($sql);
            while($fila=mysqli_fetch_array($res)){
                enviaremail($fila['nombre'], $fila['correo'],'POST VENTA D-SOLUTIONS/OT '.$_REQUEST['idticket'].'/'.$patente.'/',$cuerpo,$ruta,$name);
            }
            //enviaremail('Jaime', 'jaime@d-solutions.cl','POST VENTA D-SOLUTIONS/OT '.$_REQUEST['idticket'].'/'.$patente.'/',$cuerpo,$ruta,$name);
		}
		

        //$pdf_data = base64_decode($base64pdf);

        // Definir el nombre del archivo PDF
        //$pdf_filename = 'archivo.pdf';

        // Cabeceras HTTP para indicar que es un archivo PDF y descargarlo
        //header("Content-type: application/pdf");
        //header("Content-Disposition: attachment; filename=$pdf_filename");

        // Enviar los datos binarios del PDF al navegador
        //echo $pdf_data;
            
        echo $base64pdf;
    break;
    
    default:
        
    break;
}

function enviaremail($nombre='', $correo = '', $asunto='', $cuerpo='',$path = '',$nombrePath=''){

	//$correo = 'michael@d-solutions.cl';
	$plantilla  = $cuerpo;

	$nom = $nombre; 
    $mail = new PHPMailer(true);
	$mail->SMTPDebug = 3;                               
	$mail->isSMTP();                                 
	$mail->Host = "smtp.gmail.com";
	$mail->SMTPAuth = true;                            
	$mail->Username = "informes@d-solutions.cl";                 
	$mail->Password = "zkeecgvydghvsgzq";                         
	$mail->SMTPSecure = "tls";                           
	$mail->Port = 587;                                   
	$mail->From = $correo;
	$mail->FromName = "OT D-Solutions";
	$mail->addAddress($correo, $nom);
	$mail->isHTML(true);
	$mail->AddAttachment($path);
	$mail->Subject = $asunto;
	$mensaje = utf8_decode($plantilla);
    $mail->msgHTML($mensaje);
	$mail->AltBody = $nombrePath;

	try {
		$mail->send();
		
	} catch (Exception $e) {
		echo "Error: " . $mail->ErrorInfo;
	}
}
?>