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

   
    case 'cearTikects2' :
       // Configuración para mostrar todos los errores
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);

        // Establecer la conexión a la base de datos
        $link = mysqli_connect("localhost", "root", 'GzBjYBY6krZUlbJD', 'cloux');
        if (!$link) {
            die('Error al conectar con la base de datos: ' . mysqli_connect_error());
        }
        mysqli_set_charset($link, "utf8");

        // Establecer la zona horaria predeterminada
        date_default_timezone_set("America/Santiago");

        // Obtener la fecha actual en formato chileno
        $fechachile = date("Y-m-d H:i:s");

        // Inicializar variables
        $filenames = "";
        $aTotal = -1;

        if (isset($_POST['id'])) {
            $idTckRes = $_POST['id'];
        
            // Obtener cuenta y asunto del ticket
            $cuenta = '';
            $asunto = '';
            $sql5 = "SELECT gti_cuenta, gti_asunto FROM gen_tickets WHERE gti_id=?";
            $stmt = mysqli_prepare($link, $sql5);
            mysqli_stmt_bind_param($stmt, "i", $idTckRes);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $cuenta, $asunto);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        
            // Obtener correo del usuario solicitante
            $correoUsu = obtenerCorreoUsuario($_POST['tck_solicitante'], $link);
        
            // Enviar correo al usuario solicitante si el correo está disponible
            if ($correoUsu) {
                //enviaremail($correoUsu, 'Soporte TI DSolutions', $idTckRes, $asunto);
            }
        
            // Actualizar gti_env_corr si es necesario
            $correnv = 0;
            $usuAsig = '';
            $sql7 = "SELECT gti_env_corr, gti_asignado FROM gen_tickets WHERE gti_id=?";
            $stmt = mysqli_prepare($link, $sql7);
            mysqli_stmt_bind_param($stmt, "i", $idTckRes);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $correnv, $usuAsig);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        
            if ($correnv == 0 || $_POST['tck_asig'] != $usuAsig) {
                $correoAsig = obtenerCorreoUsuario($_POST['tck_asig'], $link);
                if ($correoAsig) {
                    //enviaremailAsignado($correoAsig, 'Soporte TI DSolutions', $idTckRes, $asunto);
                }
                $sqlcorr = "UPDATE gen_tickets SET gti_env_corr=1 WHERE gti_id=?";
                $stmt = mysqli_prepare($link, $sqlcorr);
                mysqli_stmt_bind_param($stmt, "i", $idTckRes);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        
            // Actualizar datos del ticket
            $sql = "UPDATE gen_tickets SET gti_interacciones = 2, gti_asunto=?, gti_tipo=?, gti_descripcion=?, gti_comentarios=?, gti_asignado=?, gti_cliente_asig=? WHERE gti_id=?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "ssssssi", $_POST['tck_asunto'], $_POST['tck_tipo'], $_POST['tck_descripcion'], $_POST['tck_comentario'], $_POST['tck_asig'], $_POST['tck_asig_cli'], $idTckRes);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        
            // Insertar comentario
            $sql1 = "INSERT INTO gen_comentarios_tck (com_gti_id, com_comentario, com_usuario, fech_crea) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($link, $sql1);
            mysqli_stmt_bind_param($stmt, "isss", $idTckRes, $_POST['tck_comentario'], $dusuario['tusu_nombre'], $fechachile);
            mysqli_stmt_execute($stmt);
            $ultimoIDcom = mysqli_insert_id($link);
            mysqli_stmt_close($stmt);


            $response = array(
                "estado" => "warning",
                "mensaje" => "gen_comentarios_tck",
                "archivos" => null
            );

            //var_dump($_FILES['fileImg']);
        
            // Manejo de archivos adjuntos
            if (!empty($_FILES['files']['name'])) {
                $directorio = '../../admin/dist/img/tickets/';
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }
                foreach ($_FILES['files']['name'] as $i => $filename) {
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $validaExt = array("png", "jpeg", "jpg", "gif", "docx", "xlsx", "pptx", "pdf", "txt", "csv", "zip", "rar");
                    if (in_array($extension, $validaExt)) {
                        $filename = $i . '_' . date('YmdHis') . '.' . $extension;
                        $path = $directorio . $filename;
                        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $path)) {
                            $filenames .= $filename . ",";
                        }
                    } else {
                        $response = array(
                            "estado" => "warning",
                            "mensaje" => "Uno o más archivos no tienen un formato válido",
                            "archivos" => null
                        );
                    }
                }
                $filenames = rtrim($filenames, ',');
                $sql = "UPDATE gen_comentarios_tck SET com_archivo=? WHERE com_id=?";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, "si", $filenames, $ultimoIDcom);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            // Manejo de archivos adjuntos
            if (!empty($_FILES['fileImg']['name'])) {
                $directorio = '../../admin/dist/img/tickets/';
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                if (!empty($_FILES['fileImg']['name'])) {
                    foreach ($_FILES['fileImg']['name'] as $i => $filename) {
                        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        $validaExt = array("png", "jpeg", "jpg", "gif", "docx", "xlsx", "pptx", "pdf", "txt", "csv", "zip", "rar");
                        if (in_array($extension, $validaExt)) {
                            $filename = $i . '_' . date('YmdHis') . '.' . $extension;
                            $path = $directorio . $filename;
                            if (move_uploaded_file($_FILES['fileImg']['tmp_name'][$i], $path)) {
                                $filenames .= $filename . ",";
                            }
                        } else {
                            $response = array(
                                "estado" => "warning",
                                "mensaje" => "Uno o más archivos no tienen un formato válido",
                                "archivos" => null
                            );
                        }
                    }
                    $filenames = rtrim($filenames, ',');
                    $sql = "UPDATE gen_comentarios_tck SET com_archivo=? WHERE com_id=?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, "si", $filenames, $ultimoIDcom);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    // Manejo de la situación en la que no se han subido archivos
                    $response = array(
                        "estado" => "warning",
                        "mensaje" => "No se han subido archivos",
                        "archivos" => null
                    );
                }

                
            }


        } else {
            // Código para insertar un nuevo ticket
            $response = array(
                "estado" => "warning",
                "mensaje" => "Código para insertar un nuevo ticket",
                "archivos" => null
            );
        }

        mysqli_close($link);
        echo json_encode($response);



    break;

    case 'cearTikects':
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        

        $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
        mysqli_set_charset($link, "utf8");
        date_default_timezone_set("America/Santiago");
        $fechachile = date("Y-m-d H:i:s");
        
        $filenames = ""; // Variable para almacenar los nombres de archivo separados por comas
        $aTotal = "-1";

        if(isset($_REQUEST['id'])){
            
            $cuenta = '';
            $asunto ='';
            $idTckRes = $_REQUEST['id'];
            $sql5 = "SELECT * FROM `gen_tickets` WHERE gti_id='{$_REQUEST['id']}'";
            $res5 = $link->query($sql5);
            foreach($res5 as $key => $data5){
                $cuenta = $data5["gti_cuenta"];
                $asunto = $data5["gti_asunto"];
            }
            $conex = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD',$cuenta);
            mysqli_set_charset($link, "utf8");
            $correoUsu = '';
            $sql5 = "SELECT * FROM `usuarios` WHERE usu_nombre='{$_REQUEST['tck_solicitante']}'";
            $res5 = $conex->query($sql5);
            foreach($res5 as $key => $data5){
              $correoUsu =$data5["usu_correo"];
            }
           
            if($correoUsu!=null && $correoUsu!=''){
                //enviaremail($correoUsu,'Soporte TI DSolutions',$idTckRes,$asunto);
            }
            $correnv ='';
            $usuAsig = '';
            $sql7 = "SELECT * FROM `gen_tickets` WHERE gti_id = '{$_REQUEST['id']}'";
            $res7 = $link->query($sql7);
            foreach($res7 as $key => $data7){
                $correnv =$data7["gti_env_corr"];
                $usuAsig = $data7["gti_asignado"];
              }
              if($correnv == 0 || $_REQUEST['tck_asig'] != $usuAsig ){

                $correoAsig = '';
                $sql6 = "SELECT * FROM `usuarios` WHERE usu_nombre='{$_REQUEST['tck_asig']}'";
                $res6 = $link->query($sql6);
                foreach($res6 as $key => $data6){
                  $correoAsig =$data6["usu_correo"];
                }
                if($correoAsig!=null && $correoAsig!=''){
                    //enviaremailAsignado($correoAsig,'Soporte TI DSolutions',$idTckRes,$asunto);
                }
                $sqlcorr= "UPDATE  gen_tickets SET gti_env_corr= 1 WHERE gti_id = '{$_REQUEST['id']}' ";
                $rescorr = $link->query($sqlcorr);
            }
                        

            $sql= "UPDATE  gen_tickets SET gti_interacciones = 2, gti_asunto='{$_REQUEST['tck_asunto']}',gti_tipo = '{$_REQUEST['tck_tipo']}', gti_descripcion= '{$_REQUEST['tck_descripcion']}',gti_comentarios='{$_REQUEST['tck_comentario']}' ,gti_asignado='{$_REQUEST['tck_asig']}',gti_cliente_asig='{$_REQUEST['tck_asig_cli']}' WHERE gti_id = '{$_REQUEST['id']}' ";
            
            $res = $link->query($sql);

            $sql1= "INSERT INTO gen_comentarios_tck (com_gti_id, com_comentario,com_usuario,fech_crea) VALUES ('{$_REQUEST['id']}', '{$_REQUEST['tck_comentario']}','{$dusuario['tusu_nombre']}','{$fechachile}')";
            $res1 = $link->query($sql1);
            $ultimoIDcom = $link->insert_id;

            if (isset($_FILES['files2'])) {
                if (is_array($_FILES['files']['name'])) {
                    $aTotal = count($_FILES['files']['name']);
                } else {
                    // Manejar el caso en el que $_FILES['files']['name'] no es un array
                    $aTotal = 1;
                }
                
                // Directorio donde deseas guardar los archivos
                $directorio = '../../admin/dist/img/tickets/';
            
                // Verificar si el directorio existe, si no, crearlo
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }
        

                if (is_array($_FILES['files']['name'])) {
                    // Procesar archivos cuando se suben varios archivos
                    foreach ($_FILES['files']['name'] as $i => $filename) {
                        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        $filename = $i . '_' . date('YmdHis') . '.' . $extension;
                        $path = $directorio . $filename;
                        
                        // Mover el archivo al directorio deseado
                        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $path)) {
                            // Archivo movido con éxito
                            $filenames .= $filename . ",";
                        } else {
                            // Error al mover el archivo
                            $response = array(
                                "estado" => "warning",
                                "mensaje" => "Hubo un problema al guardar el archivo en el servidor",
                                "archivos" => null
                            );
                        }
                    }
                } else {
                    // Procesar archivo único
                    $filename = $_FILES['files']['name'];
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $filename = 'img_' . date('YmdHis') . '.' . $extension;
                    $path = $directorio . $filename;
                    
                    // Mover el archivo al directorio deseado
                    if (move_uploaded_file($_FILES['files']['tmp_name'], $path)) {
                        // Archivo movido con éxito
                        $filenames .= $filename . ",";
                    } else {
                        // Error al mover el archivo
                        $response = array(
                            "estado" => "warning",
                            "mensaje" => "Hubo un problema al guardar el archivo en el servidor",
                            "archivos" => null
                        );
                    }
                }

                // Eliminar la última coma de la variable $filenames
                $filenames = rtrim($filenames, ',');
                // Ejecutar la consulta INSERT con todos los nombres de archivo en un solo registro
                $sql = "UPDATE gen_comentarios_tck set com_archivo= '{$filenames}' where com_id= '{$ultimoIDcom}'";
                $res = $link->query($sql);
            }

        }
        else {
                       
            $sql= "insert into gen_tickets (gti_asunto,gti_tipo, gti_descripcion, gti_fech_crea, gti_estado,gti_usuario, gti_comentarios,gti_asignado, gti_cuenta, gti_cliente_asig) VALUES ('{$_REQUEST['tck_asunto']}',{$_REQUEST['tck_tipo']},'{$_REQUEST['tck_descripcion']}','{$fechachile}',1,'{$_REQUEST['tck_solicitante']}','{$_REQUEST['tck_comentario']}','{$_REQUEST['tck_asig']}','cloux','{$_REQUEST['tck_asig_cli']}')";
            $res = $link->query($sql);
            $ultimoID = $link->insert_id;
            if($ultimoID != null){

                // Insertar el registro en gen_comentarios_tck con el mismo ID
                $sqlTabla2 = "INSERT INTO gen_comentarios_tck (com_gti_id, com_comentario,com_usuario,fech_crea) VALUES ('$ultimoID', '{$_REQUEST['tck_comentario']}','{$_REQUEST['tck_solicitante']}','{$fechachile}')";
                $res = $link->query($sqlTabla2);
                $ultimoIDcom = $link->insert_id;

                $correnv ='';
                $usuAsig = '';
                $sql7 = "SELECT * FROM `gen_tickets` WHERE gti_id = {$ultimoID}";
                $res7 = $link->query($sql7);
                foreach($res7 as $key => $data7){
                    $usuAsig = $data7["gti_asignado"];
                    $asunto = $data7["gti_asunto"];
                  }
                  if($usuAsig != ''){
    
                    $correoAsig = '';
                    $sql6 = "SELECT * FROM `usuarios` WHERE usu_nombre='{$usuAsig}'";
                    $res6 = $link->query($sql6);
                    foreach($res6 as $key => $data6){
                      $correoAsig =$data6["usu_correo"];
                    }
                    if($correoAsig!=null && $correoAsig!=''){
                        //enviaremailAsignado($correoAsig,'Soporte TI DSolutions',$ultimoID,$asunto);
                    }
                    $sqlcorr= "UPDATE  gen_tickets SET gti_env_corr= 1 WHERE gti_id = {$ultimoID} ";
                    $rescorr = $link->query($sqlcorr);
                }
                  
            }

            if (isset($_FILES['files2'])) {

                if (is_array($_FILES['files']['name'])) {
                    $aTotal = count($_FILES['files']['name']);
                } else {
                    // Manejar el caso en el que $_FILES['files']['name'] no es un array
                    $aTotal = 1;
                }

                //$aTotal = count($_FILES['files']['name']);
                $directorio = '../../admin/dist/img/tickets/';
            
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                for ($i = 0; $i < $aTotal; $i++) {
                    $filename = $_FILES['files']['name'][$i];
            
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                    $extension = strtolower($extension);
            
                    $validaExt = array("png", "jpeg", "jpg", "gif", "docx", "xlsx", "pptx", "pdf", "txt", "csv", "zip", "rar");
            
                    if (in_array($extension, $validaExt)) {
                        $filename = $i . '_' . date('YmdHis') . '.' . $extension;
            
                        $path = $directorio . $filename;
                        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $path)) {
                            $filenames .= $filename . ","; // Concatenar los nombres de archivo separados por comas
                        }
                    } else {
                        $response = array(
                            "estado" => "warning",
                            "mensaje" => "Uno o más archivos no tienen un formato válido",
                            "archivos" => null
                        );
                    }
                }
            
                // Eliminar la última coma de la variable $filenames
                $filenames = rtrim($filenames, ',');
            
                // Ejecutar la consulta INSERT con todos los nombres de archivo en un solo registro
                $sql = "UPDATE gen_comentarios_tck set com_archivo= '{$filenames}' where com_id= '{$ultimoIDcom}'";
                $res = $link->query($sql);
            }
            
        }

        if (isset($_FILES['files'])) {
            if (is_array($_FILES['files']['name'])) {
                $aTotal = count($_FILES['files']['name']);
            } else {
                // Manejar el caso en el que $_FILES['files']['name'] no es un array
                $aTotal = 1;
            }
            
            // Directorio donde deseas guardar los archivos
            $directorio = '../../admin/dist/img/tickets/';
        
            // Verificar si el directorio existe, si no, crearlo
            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }
    

            if (is_array($_FILES['files']['name'])) {
                // Procesar archivos cuando se suben varios archivos
                foreach ($_FILES['files']['name'] as $i => $filename) {
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $filename = $i . '_' . date('YmdHis') . '.' . $extension;
                    $path = $directorio . $filename;
                    
                    // Mover el archivo al directorio deseado
                    if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $path)) {
                        // Archivo movido con éxito
                        $filenames .= $filename . ",";
                    } else {
                        // Error al mover el archivo
                        $response = array(
                            "estado" => "warning",
                            "mensaje" => "Hubo un problema al guardar el archivo en el servidor",
                            "archivos" => null
                        );
                    }
                }
            } else {
                // Procesar archivo único
                $filename = $_FILES['files']['name'];
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $filename = 'img_' . date('YmdHis') . '.' . $extension;
                $path = $directorio . $filename;
                
                // Mover el archivo al directorio deseado
                if (move_uploaded_file($_FILES['files']['tmp_name'], $path)) {
                    // Archivo movido con éxito
                    $filenames .= $filename . ",";
                } else {
                    // Error al mover el archivo
                    $response = array(
                        "estado" => "warning",
                        "mensaje" => "Hubo un problema al guardar el archivo en el servidor",
                        "archivos" => null
                    );
                }
            }

            // Eliminar la última coma de la variable $filenames
            $filenames = rtrim($filenames, ',');
            // Ejecutar la consulta INSERT con todos los nombres de archivo en un solo registro
            $sql = "UPDATE gen_comentarios_tck set com_archivo= '{$filenames}' where com_id= '{$ultimoIDcom}'";
            $res = $link->query($sql);
        }

        if($res || $res1){
            $response['status'] = 'OK';
            $response['message'] = 'Ticket creado correctamente';
            $response['filenames'] = $filenames;
            $response['count'] = $aTotal;
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = mysqli_error($link); 
        }
               
      
        
        mysqli_close($link);
        
        echo json_encode($response);
        
    break;
    case 'conticket' :
        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
        mysqli_set_charset($link, "utf8");
        date_default_timezone_set("America/Santiago");
        $fechachile = date("Y-m-d H:i:s");
        $usu='';
        $asunto='';
        $empresa='';
        $sql1= "SELECT * from gen_tickets where gti_id={$_REQUEST['id']}";
        $res1 = $link->query($sql1);
        $id_tck = $_REQUEST['id'];
          foreach($res1 as $key => $data){
              $usu = $data["gti_usuario"];
              $asunto = $data["gti_asunto"];
              $empresa = $data["gti_cuenta"];
          }
        $correoSol=''; 
        $sql2= "SELECT * from usuarios where usu_nombre='{$usu}'";
        $res2 = $link->query($sql2);
        foreach($res2 as $key => $data1){
            $correoSol = trim($data1["usu_correo"]);
            $response['correo'] = $correoSol;
        }
          if($data1){
            //$correoSol = 'jfigueroa@d-solutions.cl';
            //enviaremailFinalizado($correoSol,$usu,$id_tck,$asunto,$empresa);
          }
          
        $sql= "UPDATE gen_tickets SET gti_estado_proceso='4', gti_fecha_confirmado = '{$fechachile}' WHERE gti_id={$_REQUEST['id']}";
        $res = $link->query($sql);
        if($res){
            $response['status'] = 'OK';
            $response['message'] = 'Ticket Tomado correctamente';
            
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = mysqli_error($link); 
        }
        mysqli_close($link);
        
        echo json_encode($response);
    break;

    case 'listarTickets':
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $enviarTicket = json_decode($_REQUEST['envio'],true);
        

        $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
        mysqli_set_charset($link, "utf8");
        date_default_timezone_set("America/Santiago");
        $fechachile = date("Y-m-d H:i:s");
        $devuelve = array();
        $flcta = "'{$_REQUEST['filclie']}'";
        $filtro = '';
        $filtroCli = '';
        $fechas = '';
        
        if($enviarTicket['estado']!= ''){
           $filtro = ' and gti_estado_proceso='.$enviarTicket['estado'];
           
        }
        if($_REQUEST['filclie'] != 'TODOS'){
            
            $filtroCli = ' and gti_cuenta='.$flcta ;
        } 
        if($enviarTicket['fecHasta'] != null){
            
            $fechas = ' AND gti_fech_crea BETWEEN \'' . $enviarTicket['fecDesde'] . ' 00:00:00\' AND \'' . $enviarTicket['fecHasta'] . ' 23:59:59\'';            
        }
        $sql= " SELECT * FROM `gen_tickets` WHERE gti_estado = 1 {$filtro} {$filtroCli} {$fechas} ";
        $res = $link->query($sql);
         if(mysqli_num_rows($res)){
             foreach($res as $key => $data){
                
              
                $imagenes = array();
                $sql1= " SELECT * FROM `gti_imagenes_tck`  where img_id_tck = {$data["gti_id"]}";
                $res1 = $link->query($sql1);
                if(mysqli_num_rows($res1)>0){
                    foreach($res1 as $key1 => $data1)
                    {
                        $imagenes[]= array( 
                            "idImg" =>$data1["img_id"],
                            "NombreImg" =>$data1["img_nombre"],                                                      
                            
                        );
                    }
                }
                
                if($data["gti_fech_crea"] != null and $data["gti_fech_actualizacion"] ==null){
                    $fechacrea = $data['gti_fech_crea'];
                    $fecha_actualizacion = $data['gti_fech_actualizacion'];
                    $fechacrea1 = new DateTime($fechacrea);
                    $fecha_actualizacion1 = new DateTime($fecha_actualizacion);
                    $interval2 = $fecha_actualizacion1->diff($fechacrea1);
                    $tiempo_sop_tomado = $interval2->format('%m Meses %d Dias %h Hrs %i Min');
                 }
                 if ($data["gti_fech_crea"] != null and $data["gti_fech_actualizacion"] !=null){
                    $fechacrea = $data['gti_fech_crea'];
                    $fecha_actualizacion = $data['gti_fech_actualizacion'];
                    $fechacrea1 = new DateTime($fechacrea);
                    $fecha_actualizacion1 = new DateTime($fecha_actualizacion);
                    $interval2 = $fecha_actualizacion1->diff($fechacrea1);
                    $tiempo_sop_tomado = $interval2->format('%m Meses %d Dias %h Hrs %i Min');
                 }

                 if( $data["gti_estado_proceso"] != 5 ){
                    $fechacrea = $data['gti_fech_crea'];
                    $fecha_actualizacion = $data['gti_fecha_confirmado'];
                    $fechacrea1 = new DateTime($fechacrea);
                    $fecha_actualizacion1 = new DateTime($fecha_actualizacion);
                    $interval2 = $fecha_actualizacion1->diff($fechacrea1);
                    $tiempo_sop_tomado = $interval2->format('%m Meses %d Dias %h Hrs %i Min');
                 }
                    /**/

                 //calcular horas en ejecucion - envio
                 if($data["gti_fech_actualizacion"] ==null){
                    $tiempo_tomado_envio = '----';
                 }
                 if($data["gti_fecha_enviado"] == null and $data["gti_fech_actualizacion"] !=null){
                    $fecha_enviado = $data["gti_fecha_enviado"];
                 $fecha_actualizacion4 = $data["gti_fech_actualizacion"];
                 $fecha_enviado2 = new DateTime($fecha_enviado);
                 $fecha_actualizacion5 = new DateTime($fecha_actualizacion4);
                 $interval3 = $fecha_enviado2->diff($fecha_actualizacion5);
                 $tiempo_tomado_envio = $interval3->format('%m Meses %d Dias %h Hrs %i Min');
                 }
                 if($data["gti_fecha_enviado"] != null and $data["gti_fech_actualizacion"] !=null){
                    $fecha_enviado = $data["gti_fecha_enviado"];
                    $fecha_actualizacion4 = $data["gti_fech_actualizacion"];
                    $fecha_enviado2 = new DateTime($fecha_enviado);
                    $fecha_actualizacion5 = new DateTime($fecha_actualizacion4);
                    $interval3 = $fecha_enviado2->diff($fecha_actualizacion5);
                    $tiempo_tomado_envio = $interval3->format('%m Meses %d Dias %h Hrs %i Min');
                 }

                $devuelve[]= array( 
                    "id" =>$data["gti_id"],
                    "usuario" =>$data["gti_usuario"],
                    "Fecha_Hora" =>$data["gti_fech_crea"],
                    "Estado" =>$data["gti_estado"],
                    "Tipo" =>$data["gti_tipo"],
                    "Asunto" =>$data["gti_asunto"],
                    "Descripcion" =>$data["gti_descripcion"],
                    "Estado_proceso" =>$data["gti_estado_proceso"],
                    "Tiempo_transcurrido" =>$tiempo_sop_tomado,
                    "Fecha_Actualiza" =>$tiempo_tomado_envio,
                    "Imagenes" =>$imagenes,
                    "Interaccion" =>$data["gti_interacciones"],
                    "Cuenta" =>$data["gti_cuenta"],
                    "Asignado" =>$data["gti_asignado"],
                    "CliAsig" =>$data["gti_cliente_asig"],
                );

             }
         }
         

        
        mysqli_close($link);
        
        echo json_encode($devuelve);
    break;
    case 'eliminarTicket':
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    

        $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
        mysqli_set_charset($link, "utf8");
        date_default_timezone_set("America/Santiago");
        $sql= "UPDATE gen_tickets SET gti_estado_proceso=5 WHERE gti_id={$_REQUEST['id']}";
        $res = $link->query($sql);
        if($res){
            $response['status'] = 'OK';
            $response['message'] = 'Ticket anulado correctamente';
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = mysqli_error($link); 
        }
        
        mysqli_close($link);
        
        echo json_encode($response);
    break;

    case 'editarTicket':
        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        

        $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
        mysqli_set_charset($link, "utf8");
        date_default_timezone_set("America/Santiago");
        $fechachile = date("Y-m-d H:i:s");

        $sql= "UPDATE gen_tickets SET gti_asunto='{$_REQUEST['tck_asunto']}', gti_tipo='{$_REQUEST['tck_tipo']}', gti_usuario='{$_REQUEST['tck_solicitante']}', gti_descripcion='{$_REQUEST['tck_descripcion']}' WHERE gti_id={$_REQUEST['id']}";
        $res = $link->query($sql);
        if($res){
            $response['status'] = 'OK';
            $response['message'] = 'Ticket creado correctamente';
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = mysqli_error($link); 
        }
        
        mysqli_close($link);
        
        echo json_encode($response);

    break;
           
    case 'listarComentarios':
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        
    
            $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
            mysqli_set_charset($link, "utf8");
            date_default_timezone_set("America/Santiago");
            $fechachile = date("Y-m-d H:i:s");
            $devuelve = array();
            
            $sql= " SELECT * FROM `gen_comentarios_tck`  where com_gti_id = {$_REQUEST['id']}";
            $res = $link->query($sql);
             if (mysqli_num_rows($res)) {
              while ($data = mysqli_fetch_assoc($res)) {
                  $comentarios = $data["com_comentario"];
                  $usuario = $data["com_usuario"];
                  $fecha_com = $data["fech_crea"];
                  $nombreImg = $data["com_archivo"];
          
                  // Explode para separar los nombres de archivo por comas y crear un array
                  $nombreImgArray = explode(',', $nombreImg);
          
                  // Iterar a través del array para generar los enlaces <a href>
                  $enlaces = "";
                  foreach ($nombreImgArray as $filename) {
                      //$enlaces .= "<a href='../dist/img/tickets/$filename' download'>$filename</a><br>";
                      $enlaces .= "<a href='../../admin/dist/img/tickets/$filename' download' target='_blank'>$filename</a><br>";
                      
                  }
          
                  // Agregar la información al arreglo
                  $devuelve[] = array(
                      "Comentarios" => $comentarios,
                      "Usuario" => $usuario,
                      "Fecha_com" => $fecha_com,
                      "EnlacesImg" => $enlaces // Aquí se incluyen los enlaces generados en el arreglo
                  );
              }
          }
             
    
            
            mysqli_close($link);
            
            echo json_encode($devuelve);
    break;
    case 'listarFiltros':
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
        mysqli_set_charset($link, "utf8");
        date_default_timezone_set("America/Santiago");
        $fechachile = date("Y-m-d H:i:s");
        $devuelve = array();
          
        $sql= " SELECT * FROM `gen_tickets` where gti_estado=1 ";
        $res = $link->query($sql);
         if(mysqli_num_rows($res)>0){
             foreach($res as $key => $data){
                $devuelve[]= array( 
                    "Estado_proceso" =>$data["gti_estado_proceso"],
                );

             }
         }
       
        mysqli_close($link);
        echo json_encode($devuelve);
    break;

    case 'tomarTicket' :
        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        

        $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
        mysqli_set_charset($link, "utf8");
        date_default_timezone_set("America/Santiago");
        $fechachile = date("Y-m-d H:i:s");
        $cuenta = '';
        $idTckRes = $_REQUEST['id'];
        $sql5 = "SELECT * FROM `gen_tickets` WHERE gti_id='{$_REQUEST['id']}'";
        $res5 = $link->query($sql5);
        foreach($res5 as $key => $data5){
            $cuenta = $data5["gti_cuenta"];
            $usu = $data5["gti_usuario"];
            $asunto = $data5["gti_asunto"];
        }
        $conex = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD',$cuenta);
        mysqli_set_charset($link, "utf8");
        $correoUsu = '';
        $sql5 = "SELECT * FROM `usuarios` WHERE usu_nombre= '{$usu}'";
        $res5 = $conex->query($sql5);
        foreach($res5 as $key => $data5){
          $correoUsu =$data5["usu_correo"];
        }
        if($correoUsu!=null && $correoUsu!=''){
            //enviaremailTomado($correoUsu,'Soporte TI DSolutions',$idTckRes,$asunto);
        }
    
        
        $sql= "UPDATE gen_tickets SET gti_estado_proceso='2', gti_fech_actualizacion='{$fechachile}' WHERE gti_id={$_REQUEST['id']}";
        $res = $link->query($sql);
        if($res){
            $response['status'] = 'OK';
            $response['message'] = 'Ticket Tomado correctamente';
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = mysqli_error($link); 
        }
            
        mysqli_close($link);
        
        echo json_encode($response);
        break;
    
        case 'finalizarTicket' :
        
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            $fechachile = date("Y-m-d H:i:s");
    
            $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
            mysqli_set_charset($link, "utf8");
            date_default_timezone_set("America/Santiago");
            $cuenta = '';
            $idTckRes = $_REQUEST['id'];
            $sql5 = "SELECT * FROM `gen_tickets` WHERE gti_id='{$_REQUEST['id']}'";
            $res5 = $link->query($sql5);
            foreach($res5 as $key => $data5){
                $cuenta = $data5["gti_cuenta"];
                $usu = $data5["gti_usuario"];
                $asunto = $data5["gti_asunto"];
            }
            $conex = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD',$cuenta);
            mysqli_set_charset($link, "utf8");
            $correoUsu = '';
            $sql5 = "SELECT * FROM `usuarios` WHERE usu_nombre= '{$usu}'";
            $res5 = $conex->query($sql5);
            foreach($res5 as $key => $data5){
              $correoUsu =$data5["usu_correo"];
            }
            if($correoUsu!=null && $correoUsu!=''){
                //enviaremailEnviado($correoUsu,'Soporte TI DSolutions',$idTckRes,$asunto);
            }

            $sql= "UPDATE gen_tickets SET gti_estado_proceso='3', gti_fecha_enviado = '{$fechachile}' WHERE gti_id={$_REQUEST['id']}";
            $res = $link->query($sql);
            if($res){
                $response['status'] = 'OK';
                $response['message'] = 'Ticket Tomado correctamente';
            }
            else {
                $response['status'] = 'ERROR';
                $response['message'] = mysqli_error($link); 
            }
            mysqli_close($link);
            
            echo json_encode($response);
            break;

        case 'listarTiempos':
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
    

            $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
            mysqli_set_charset($link, "utf8");
            date_default_timezone_set("America/Santiago");
            $fechachile = date("Y-m-d H:i:s");
            $devuelve1 = array();

            $sql= "SELECT * FROM `gen_tickets`  where gti_estado = 1 and gti_id={$_REQUEST['id']}";
            $res = $link->query($sql);
            
        if(mysqli_num_rows($res)){
          foreach($res as $key => $data){
            
            if($data["gti_fech_crea"] != null and $data["gti_fech_actualizacion"] !=null){
                $fechacrea = $data['gti_fech_crea'];
             $fecha_actualizacion = $data['gti_fech_actualizacion'];
             $fechacrea1 = new DateTime($fechacrea);
             $fecha_actualizacion1 = new DateTime($fecha_actualizacion);
             $interval2 = $fecha_actualizacion1->diff($fechacrea1);
             $tiempo_sop_tomado = $interval2->format(' %h Hrs ');
             }else{
                $tiempo_sop_tomado = '-----';
             }
             //calcular horas en ejecucion - envio
             if($data["gti_fecha_enviado"] != null and $data["gti_fech_actualizacion"] !=null){
             $fecha_enviado = $data["gti_fecha_enviado"];
             $fecha_actualizacion4 = $data["gti_fech_actualizacion"];
             $fecha_enviado2 = new DateTime($fecha_enviado);
             $fecha_actualizacion5 = new DateTime($fecha_actualizacion4);
             $interval3 = $fecha_enviado2->diff($fecha_actualizacion5);
             $tiempo_tomado_envio = $interval3->format('%h Hrs');
             }else{
                $tiempo_tomado_envio = '-----';
             }
             
             //calcular horas envio - confirmado
             if($data["gti_fecha_enviado"] != null and $data["gti_fecha_confirmado"] !=null){
                $fecha_enviado1 = $data["gti_fecha_enviado"];
                $fecha_confirmado = $data["gti_fecha_confirmado"];
                $fecha_enviado6 = new DateTime($fecha_enviado1);
                $fecha_confirmado7 = new DateTime($fecha_confirmado);
                $interval4 = $fecha_confirmado7->diff($fecha_enviado6);
                $tiempo_envio_confirmado = $interval4->format('%h Hrs');
             }else{
                $tiempo_envio_confirmado = '-----';
             }
             
             //calcular horas soporte - confirmado
             if($data["gti_fech_crea"] != null and $data["gti_fecha_confirmado"] !=null){
                $fechacrea2 = $data["gti_fech_crea"];
                $fecha_confirmado1 = $data["gti_fecha_confirmado"];
                $fechacrea6 = new DateTime($fechacrea2);
                $fecha_confirmado6 = new DateTime($fecha_confirmado1);
                $interval5 = $fechacrea6->diff($fecha_confirmado6);
                $tiempo_soporte_confirmado = $interval5->format('%h Hrs');
             }else{
                $tiempo_soporte_confirmado = '-----';
             }

             $devuelve1[]= array( 
                "Tiempo_sop_tomado" =>$tiempo_sop_tomado,
                "Tiempo_ejecu_envio" =>$tiempo_tomado_envio,
                "Tiempo_envio_confirmo" =>$tiempo_envio_confirmado,
                "Tiempo_soporte_confirmo" =>$tiempo_soporte_confirmado,
                );
            }
        }
            
        mysqli_close($link);
        
        echo json_encode($devuelve1);
            break; 
    
       

    default:
        $response['status'] = 'ERROR';
        $response['message'] = 'Metodo no existe.';
        echo json_encode($response);
    break;

    case 'getTicketsExcel':
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $link = mysqli_connect("localhost", "root",'GzBjYBY6krZUlbJD','cloux');
        mysqli_set_charset($link, "utf8");
        date_default_timezone_set("America/Santiago");
        $recibeFecha = json_decode($_REQUEST['envio'],true);
        $fechachile = date("Y-m-d H:i:s");
        $flcta = "'{$_REQUEST['filclie']}'";
        $filtroCli = '';
        $fechas='';
        if($_REQUEST['filclie'] != 'TODOS'){
            
            $filtroCli = ' and gti_cuenta='.$flcta ;
            
        }
        if($recibeFecha['fecHasta'] != null){
            
            $fechas = ' AND gti_fech_crea BETWEEN \'' . $recibeFecha['fecDesde'] . ' 00:00:00\' AND \'' . $recibeFecha['fecHasta'] . ' 23:59:59\'';            
        }

        $recibe = json_decode($_REQUEST['envio'],true);
        $sql = "SELECT * FROM `gen_tickets` where gti_estado = 1 {$filtroCli} {$fechas}";
        $res = $link->query($sql);
        $tickRep = array();
        
        $propie = array();
        $contador = 0;
          while($data=mysqli_fetch_array($res)){

            if($data["gti_fech_crea"] != null and $data["gti_fech_actualizacion"] !=null){
                $fechacrea = $data['gti_fech_crea'];
             $fecha_actualizacion = $data['gti_fech_actualizacion'];
             $fechacrea1 = new DateTime($fechacrea);
             $fecha_actualizacion1 = new DateTime($fecha_actualizacion);
             $interval2 = $fecha_actualizacion1->diff($fechacrea1);
             $tiempo_sop_tomado = $interval2->format(' %h Hrs ');
             }else{
                $tiempo_sop_tomado = '-----';
             }
             //calcular horas en ejecucion - envio
             if($data["gti_fecha_enviado"] != null and $data["gti_fech_actualizacion"] !=null){
             $fecha_enviado = $data["gti_fecha_enviado"];
             $fecha_actualizacion4 = $data["gti_fech_actualizacion"];
             $fecha_enviado2 = new DateTime($fecha_enviado);
             $fecha_actualizacion5 = new DateTime($fecha_actualizacion4);
             $interval3 = $fecha_enviado2->diff($fecha_actualizacion5);
             $tiempo_tomado_envio = $interval3->format('%h Hrs');
             }else{
                $tiempo_tomado_envio = '-----';
             }
             
             //calcular horas envio - confirmado
             if($data["gti_fecha_enviado"] != null and $data["gti_fecha_confirmado"] !=null){
                $fecha_enviado1 = $data["gti_fecha_enviado"];
                $fecha_confirmado = $data["gti_fecha_confirmado"];
                $fecha_enviado6 = new DateTime($fecha_enviado1);
                $fecha_confirmado7 = new DateTime($fecha_confirmado);
                $interval4 = $fecha_confirmado7->diff($fecha_enviado6);
                $tiempo_envio_confirmado = $interval4->format('%h Hrs');
             }else{
                $tiempo_envio_confirmado = '-----';
             }
             
             //calcular horas soporte - confirmado
             if($data["gti_fech_crea"] != null and $data["gti_fecha_confirmado"] !=null){
                $fechacrea2 = $data["gti_fech_crea"];
                $fecha_confirmado1 = $data["gti_fecha_confirmado"];
                $fechacrea6 = new DateTime($fechacrea2);
                $fecha_confirmado6 = new DateTime($fecha_confirmado1);
                $interval5 = $fechacrea6->diff($fecha_confirmado6);
                $tiempo_soporte_confirmado = $interval5->format('%h Hrs');
             }else{
                $tiempo_soporte_confirmado = '-----';
             }
            $propie[] = array(
                'Codigo'=>$data['gti_id'],
                'Usuario'=>$data['gti_usuario'],
                'Fecha_cracion'=>$data['gti_fech_crea'],
                'Asunto'=>$data['gti_asunto'],
                'Descripcion'=>$data['gti_descripcion'],
                'tmp_sop_tom'=>$tiempo_sop_tomado,
                'tmp_tom_env'=>$tiempo_tomado_envio,
                'tmp_env_conf'=>$tiempo_envio_confirmado,
                'tmp_sop_conf'=>$tiempo_soporte_confirmado,
                "Cuenta" =>$data["gti_cuenta"],
            );
            $contador++;
          }
        mysqli_close($link);
        try {
          
          $objPHPExcel = new PHPExcel();
          $objPHPExcel->getProperties()->setCreator("DataSolutions");
          $objPHPExcel->getProperties()->setLastModifiedBy("DataSolutions");
          $objPHPExcel->getProperties()->setTitle("Tickets");
          $objPHPExcel->getProperties()->setSubject("Tickets");
          $objPHPExcel->getProperties()->setDescription("Lista de Tickets");
          $objPHPExcel->setActiveSheetIndex(0);
    
        
            $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Codigo");
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Cliente");
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', "Usuario");
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', "Fecha_Creacion");
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', "Asunto");
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', "Descripcion");
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', "Tiempo creado-tomado");
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', "Tiempo tomado-envio");
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', "Tiempo envio-confirmado");
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', "Tiempo creado-finalizado");
            
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(21);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(21);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
          $index=2;
          for($i=0; $i<count($propie); $i++){
            
              $objPHPExcel->getActiveSheet()->SetCellValue('A'.$index, $propie[$i]['Codigo']);
              $objPHPExcel->getActiveSheet()->SetCellValue('B'.$index, $propie[$i]['Cuenta'] );
              $objPHPExcel->getActiveSheet()->SetCellValue('C'.$index, $propie[$i]['Usuario']);
              $objPHPExcel->getActiveSheet()->SetCellValue('D'.$index, $propie[$i]['Fecha_cracion']);
              $objPHPExcel->getActiveSheet()->SetCellValue('E'.$index, $propie[$i]['Asunto']);
              $objPHPExcel->getActiveSheet()->SetCellValue('F'.$index, $propie[$i]['Descripcion']);
              $objPHPExcel->getActiveSheet()->SetCellValue('G'.$index, $propie[$i]['tmp_sop_tom'] );
              $objPHPExcel->getActiveSheet()->SetCellValue('H'.$index, $propie[$i]['tmp_tom_env'] );
              $objPHPExcel->getActiveSheet()->SetCellValue('I'.$index, $propie[$i]['tmp_env_conf'] );
              $objPHPExcel->getActiveSheet()->SetCellValue('J'.$index, $propie[$i]['tmp_sop_conf'] );
              $index++;
            }
            
            
          
          // Rename worksheet
          $objPHPExcel->getActiveSheet()->setTitle('Tickets');
          // Set active sheet index to the first sheet, so Excel opens this as the first sheet
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
          echo json_encode($response);
        } 
        catch (\Throwable $th) {
          $dataSend = array();
          $dataSend[0]=''.$th;
          echo json_encode($dataSend);
        }
    
break; 
}


// Función para obtener el correo del usuario
function obtenerCorreoUsuario($nombreUsuario, $conexión)
{
    $sql = "SELECT usu_correo FROM usuarios WHERE usu_nombre=?";
    $stmt = mysqli_prepare($conexión, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nombreUsuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $correo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $correo;
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
function enviaremail ($correo,$usuario,$idTicket,$asunto){


	$plantilla  = "<p>Estimado Usuario(a):</p>
				  <p>Su Ticket ha sido actualizado por $usuario y su respuesta ha sido programado para ser enviado a su correo.</p>
				  <p>Este correo se genera de forma automatica por lo tal motivo no responder.</p>";

	//$path  = $title;
	$nombrecorr = $usuario;
    $mail       = new PHPMailer(true);
	//$mail->SMTPDebug = 1;                               
	$mail->isSMTP();                                 
	
    //$mail->Host = "smtp.gmail.com";
	//$mail->SMTPAuth = true;                            
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

    if(false){
      $mail->Host = "smtp.gmail.com";      
      $mail->Username = "informes@d-solutions.cl";                 
      $mail->Password = "zkeecgvydghvsgzq";            
      $mail->Port = 587;
      $mail->From = $correo;
    }else{
      $mail->Host = "smtp.resend.com";
      $mail->Username = "resend";                 
      $mail->Password = "re_3CE6fmZE_Ey1TzyfrDjxy51xuwXtUxh8u";
      $mail->Port = 587; 
      $mail->From = "informes@d-solutions.cl";
    }

	$mail->FromName = "Soporte TI DSolutions".$idTicket;
	$mail->addAddress($correo, $nombrecorr);
	$mail->isHTML(true);
	//$mail->AddAttachment($path);
	$mail->Subject = "Ticket ".$idTicket." ".$asunto;
	$mensaje = utf8_decode($plantilla);
    $mail->msgHTML($mensaje);
	$mail->AltBody = 'Ticket';

	try {
		$mail->send();
		
	} catch (Exception $e) {
		//echo "Error: " . $mail->ErrorInfo;
	}
}
function enviaremailAsignado ($correo,$usuario,$idTicket,$asunto){


	$plantilla  = "<p>Estimado Usuario(a):</p>
				  <p>Se le asigno el ticket $idTicket por $usuario, favor revisar en modulo gestionar ticket de sistema interno.</p>
				  <p>Este correo se genera de forma automatica por lo tal motivo no responder.</p>";

	//$path  = $title;
	$nombrecorr = $usuario;
    $mail       = new PHPMailer(true);
	//$mail->SMTPDebug = 1;                               
	$mail->isSMTP();                                 
	
    
    //$mail->Host = "smtp.gmail.com";
	//$mail->SMTPAuth = true;                            
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

    if(false){
      $mail->Host = "smtp.gmail.com";      
      $mail->Username = "informes@d-solutions.cl";                 
      $mail->Password = "zkeecgvydghvsgzq";            
      $mail->Port = 587;
      $mail->From = $correo;
    }else{
      $mail->Host = "smtp.resend.com";
      $mail->Username = "resend";                 
      $mail->Password = "re_3CE6fmZE_Ey1TzyfrDjxy51xuwXtUxh8u";
      $mail->Port = 587; 
      $mail->From = "informes@d-solutions.cl";
    }

	$mail->FromName = "Soporte TI DSolutions".$idTicket;
	$mail->addAddress($correo, $nombrecorr);
	$mail->isHTML(true);
	//$mail->AddAttachment($path);
	$mail->Subject = "Ticket ".$idTicket." ".$asunto;
	$mensaje = utf8_decode($plantilla);
    $mail->msgHTML($mensaje);
	$mail->AltBody = 'Ticket';

	try {
		$mail->send();
		
	} catch (Exception $e) {
		//echo "Error: " . $mail->ErrorInfo;
	}
}
function enviaremailTomado ($correo,$usuario,$idTicket,$asunto){


	$plantilla  = "<p>Estimado Usuario(a):</p>
				  <p>Su Ticket $idTicket ha sido tomado por $usuario y comenzara el proceso de ejecución, el cual ha sido programado para ser enviado a su correo.</p>
				  <p>Este correo se genera de forma automatica por lo tal motivo no responder.</p>";

	//$path  = $title;
	$nombrecorr = $usuario;
    $mail       = new PHPMailer(true);
	//$mail->SMTPDebug = 1;                               
	$mail->isSMTP();                                 
	
    //$mail->Host = "smtp.gmail.com";
	//$mail->SMTPAuth = true;                            
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

    if(false){
      $mail->Host = "smtp.gmail.com";      
      $mail->Username = "informes@d-solutions.cl";                 
      $mail->Password = "zkeecgvydghvsgzq";            
      $mail->Port = 587;
      $mail->From = $correo;
    }else{
      $mail->Host = "smtp.resend.com";
      $mail->Username = "resend";                 
      $mail->Password = "re_3CE6fmZE_Ey1TzyfrDjxy51xuwXtUxh8u";
      $mail->Port = 587; 
      $mail->From = "informes@d-solutions.cl";
    }

	$mail->FromName = "Soporte TI DSolutions ".$idTicket;
	$mail->addAddress($correo, $nombrecorr);
	$mail->isHTML(true);
	//$mail->AddAttachment($path);
	$mail->Subject = "Ticket ".$idTicket." ".$asunto;
	$mensaje = utf8_decode($plantilla);
    $mail->msgHTML($mensaje);
	$mail->AltBody = 'Ticket';

	try {
		$mail->send();
		
	} catch (Exception $e) {
		//echo "Error: " . $mail->ErrorInfo;
	}
}
function enviaremailEnviado ($correo,$usuario,$idTicket,$asunto){


	$plantilla  = "<p>Estimado Usuario(a):</p>
				  <p>Su Ticket $idTicket ha sido enviado para su confirmación de finalización por $usuario.</p>
				  <p>Este correo se genera de forma automatica por lo tal motivo no responder.</p>";

	//$path  = $title;
	$nombrecorr = $usuario;
    $mail       = new PHPMailer(true);
	//$mail->SMTPDebug = 1;                               
	$mail->isSMTP();                                 
	
    //$mail->Host = "smtp.gmail.com";
	//$mail->SMTPAuth = true;                            
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

    if(false){
      $mail->Host = "smtp.gmail.com";      
      $mail->Username = "informes@d-solutions.cl";                 
      $mail->Password = "zkeecgvydghvsgzq";            
      $mail->Port = 587;
      $mail->From = $correo;
    }else{
      $mail->Host = "smtp.resend.com";
      $mail->Username = "resend";                 
      $mail->Password = "re_3CE6fmZE_Ey1TzyfrDjxy51xuwXtUxh8u";
      $mail->Port = 587; 
      $mail->From = "informes@d-solutions.cl";
    }

	$mail->FromName = "Soporte TI DSolutions ".$idTicket;
	$mail->addAddress($correo, $nombrecorr);
	$mail->isHTML(true);
	//$mail->AddAttachment($path);
	$mail->Subject = "Ticket ".$idTicket." ".$asunto;
	$mensaje = utf8_decode($plantilla);
    $mail->msgHTML($mensaje);
	$mail->AltBody = 'Ticket';

	try {
		$mail->send();
		
	} catch (Exception $e) {
		//echo "Error: " . $mail->ErrorInfo;
	}
}
function enviaremailFinalizado ($correoSol,$usuario,$idTicket,$asunto,$empresa){


	$plantilla  = "<p>Estimado Usuario(a):</p>
				  <p>Su Ticket $idTicket ha sido Finalizado</p>
				  <p>Este correo se genera de forma automatica por lo tal motivo no responder.</p>";

	//$path  = $title;
	$nombrecorr = $usuario;
    $mail       = new PHPMailer(true);
	//$mail->SMTPDebug = 1;                               
	$mail->isSMTP();                                 
	
    //$mail->Host = "smtp.gmail.com";
	//$mail->SMTPAuth = true;                            
	//$mail->Username = "informes@d-solutions.cl";                 
	//$mail->Password = "zkeecgvydghvsgzq";                           
	//$mail->SMTPSecure = "tls";                           
	//$mail->Port = 587;                                   
	//$mail->From = $correoSol;

    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = "tls"; 

    //$mail->Host = "smtp.gmail.com";
    //$mail->Username = "informes@d-solutions.cl";                 
    //$mail->Password = "zkeecgvydghvsgzq";                                              
    //$mail->Port = 587;                                   
    //$mail->From = $correo;

    if(false){
      $mail->Host = "smtp.gmail.com";      
      $mail->Username = "informes@d-solutions.cl";                 
      $mail->Password = "zkeecgvydghvsgzq";            
      $mail->Port = 587;
      $mail->From = $correoSol;
    }else{
      $mail->Host = "smtp.resend.com";
      $mail->Username = "resend";                 
      $mail->Password = "re_3CE6fmZE_Ey1TzyfrDjxy51xuwXtUxh8u";
      $mail->Port = 587; 
      $mail->From = "informes@d-solutions.cl";
    }

	$mail->FromName = $empresa." ".$idTicket." ".$asunto;
	$mail->addAddress($correoSol, $nombrecorr);
	$mail->isHTML(true);
	//$mail->AddAttachment($path);
	$mail->Subject = "Ticket ".$idTicket." ".$asunto;
	$mensaje = utf8_decode($plantilla);
    $mail->msgHTML($mensaje);
	$mail->AltBody = 'Ticket';

	try {
		$mail->send();
		
	} catch (Exception $e) {
		//echo "Error: " . $mail->ErrorInfo;
	}
}
?>