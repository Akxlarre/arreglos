<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php 
$fecha = date('d-m-Y H:i:s');
$nticket = $_REQUEST['nticket'];
$tecnico = $_REQUEST['tecnico'];
$fhlabor = $_REQUEST['fhlabor'];
$ttrabajo = $_REQUEST['ttrabajo'];
$tservicio = $_REQUEST['tservicio'];
$nombre = $_REQUEST['nombre'];
$patente = $_REQUEST['patente'];
$cliente = $_REQUEST['cliente'];
$img = $_REQUEST['img'];
$accesorios = $_REQUEST['accesorios'];
$nserie = $_REQUEST['nserie'];
$nserieCan = $_REQUEST['nserieCan'];
$descripcion = $_REQUEST['descripcion'];
$comentario = $_REQUEST['comentario'];
$firmaTec = $_REQUEST['firmaTec'];
$firmaCli = $_REQUEST['firmaCli'];
$img0_1 = "";
$img0_2 = "";
$img0_3 = "";
$img0_4 = "";
$img1_1 = "";
$img1_2 = "";
$img1_3 = "";
$img1_4 = "";
if($fhlabor!=''){
    $fhlabor = date('d-m-Y H:i:s', strtotime($fhlabor));
}
foreach ($img as $key => $value) {
    if($value['idtipo']==0 && $value['idsubtipo']==1){ $img0_1 = $value['img']; }
    if($value['idtipo']==0 && $value['idsubtipo']==2){ $img0_2 = $value['img']; }
    if($value['idtipo']==0 && $value['idsubtipo']==3){ $img0_3 = $value['img']; }
    if($value['idtipo']==0 && $value['idsubtipo']==4){ $img0_4 = $value['img']; }
    if($value['idtipo']==1 && $value['idsubtipo']==1){ $img1_1 = $value['img']; }
    if($value['idtipo']==1 && $value['idsubtipo']==2){ $img1_2 = $value['img']; }
    if($value['idtipo']==1 && $value['idsubtipo']==3){ $img1_3 = $value['img']; }
    if($value['idtipo']==1 && $value['idsubtipo']==4){ $img1_4 = $value['img']; }
}
?>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <style type="text/css">
        @page {
            margin-left: 0.5cm;
            margin-right: 0.5cm;
            margin-top: 0.5cm;
            margin-bottom: 0.5cm;
            
        }
        body{
            /*border: 3px solid grey;
            border-radius:8px;*/
        }
        #watermark {
            position: fixed;
            bottom: 0px;
            left: -45px;
            top: -150px;
            width: 30cm;
            height: 40cm;
            z-index: -1000;
        }
        
        #icnesc {
            position: absolute;
            bottom: 0px;
            left: 30px;
            top: -85px;
            width: 2cm;
            height: 2cm;
            z-index: -1000;
        }
        :root {
            --blue: #007bff;
            --indigo: #6610f2;
            --purple: #6f42c1;
            --pink: #e83e8c;
            --red: #dc3545;
            --orange: #fd7e14;
            --yellow: #ffc107;
            --green: #28a745;
            --teal: #20c997;
            --cyan: #17a2b8;
            --white: #fff;
            --gray: #6c757d;
            --gray-dark: #7058c3;
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #7058c3;
            --breakpoint-xs: 0;
            --breakpoint-sm: 576px;
            --breakpoint-md: 768px;
            --breakpoint-lg: 992px;
            --breakpoint-xl: 1200px;
            --font-family-sans-serif: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
            --font-family-monospace: SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
        }

        .bsc-black{
            border:1px solid #80858A;
        }
        .bg-secondary {
            background-color: #6c757d!important;
        }
        .table-sm td, .table-sm th {
            padding: .3rem;
        }
        body{
            font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
        }
        .row {
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-flex-wrap: wrap;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -7.5px;
            margin-left: -7.5px;
        }
        .col-md-6{
            -webkit-flex: 0 0 50%;
            -ms-flex: 0 0 50%;
            flex: 0 0 50%;
            max-width: 50%;
        }
        .col, .col-1, .col-10, .col-11, .col-12, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-auto, .col-lg, .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-auto, .col-md, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-auto, .col-sm, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-auto, .col-xl, .col-xl-1, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-auto {
            position: relative;
            width: 100%;
            padding-right: 7.5px;
            padding-left: 7.5px;
        }
        /*.col, .col-1, .col-10, .col-11, .col-12, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-auto, .col-lg, .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-auto, .col-md, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-auto, .col-sm, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-auto, .col-xl, .col-xl-1, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-auto {
            position: relative;
            width: 100%;
            padding-right: 7.5px;
            padding-left: 7.5px;
        }*/
        .fontmontserrat{
            font-family: 'Montserrat', sans-serif;
        }
        .table:not(.table-dark) {
            color: inherit;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            background-color: transparent;
            display: table;
            border-collapse: collapse;
            box-sizing: border-box;
            text-indent: initial;
            white-space: normal;
            line-height: normal;
            font-weight: normal;
            font-size: medium;
            font-style: normal;
            color: -internal-quirk-inherit;
            text-align: start;
            border-spacing: 2px;
            border-color: gray;
            font-variant: normal;
        }
        .table tr {
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <img src="/var/www/html/admin/dist/img/ldsnegro2.png" style="width: 90px;height:50px; position:absolute; top:1px; right:15px;">
    <table cellspacing="0" style="width: 100%;font-size:12.5pt;font-weight:600;margin-top:20px;margin-left:10px;margin-right:10px;">
        <tr>
            <td align="center" style="padding-top:1px;padding-bottom:1px;text-transform: uppercase;">
                ot n° <?php echo $nticket; ?>
            </td>
        </tr>
    </table>
    <table cellspacing="0" style="width: 100%;font-size:12.5pt;font-weight:600;margin-top:20px;margin-left:10px;margin-right:10px;">
        <tr>
            <td align="left" style="padding-top:1px;padding-bottom:1px;padding-left:50px;text-transform: uppercase;">
                1.- datos generales
            </td>
        </tr>
    </table>
    <table cellspacing="0" style="width: 100%;font-size:11pt;margin-top:0px;margin-left:10px;margin-right:10px;font-weight:600;">
        <tr>
            <td align="left" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">Cliente</td>
            <td align="left" style="border: 1px solid #898A8A;font-weight:normal;font-size:10pt;text-transform: uppercase;"><?php echo $cliente; ?></td>
        </tr>
        <tr>
            <td align="left" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">Fecha Labor</td>
            <td align="left" style="border: 1px solid #898A8A;font-weight:normal;font-size:10pt;"><?php echo $fhlabor; ?></td>
        </tr>
        <tr>
            <td align="left" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">Tipo Labor</td>
            <td align="left" style="border: 1px solid #898A8A;font-weight:normal;font-size:10pt;"><?php echo $ttrabajo; ?></td>
        </tr>
        <tr>
            <td align="left" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">Tipo Servicio</td>
            <td align="left" style="border: 1px solid #898A8A;font-weight:normal;font-size:10pt;"><?php echo $tservicio; ?></td>
        </tr>
        <tr>
            <td align="left" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">Patente</td>
            <td align="left" style="border: 1px solid #898A8A;font-weight:normal;font-size:10pt;"><?php echo $patente; ?></td>
        </tr>
        <tr>
            <td align="left" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">Descripción</td>
            <td align="left" style="border: 1px solid #898A8A;font-weight:normal;font-size:10pt;"><?php echo $descripcion; ?></td>
        </tr>
    </table>
    
    <table cellspacing="0" style="width: 100%;font-size:12.5pt;font-weight:600;margin-top:20px;margin-left:10px;margin-right:10px;">
        <tr>
            <td align="left" style="padding-top:1px;padding-bottom:1px;padding-left:50px;text-transform: uppercase;">
                2.- datos servicio
            </td>
        </tr>
    </table>
    <table cellspacing="0" style="width: 100%;font-size:11pt;margin-top:0px;margin-left:10px;margin-right:10px;font-weight:600;">
        <tr>
            <td align="left" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">Imei (Serie Gps)</td>
            <td align="left" style="border: 1px solid #898A8A;font-weight:normal;font-size:10pt;"><?php echo $nserie; ?></td>
        </tr>
        <tr>
            <td align="left" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">Imei (Serie Can)</td>
            <td align="left" style="border: 1px solid #898A8A;font-weight:normal;font-size:10pt;"><?php echo $nserieCan; ?></td>
        </tr>
        <tr>
            <td align="left" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">Accesorios</td>
            <td align="left" style="border: 1px solid #898A8A;font-weight:normal;font-size:10pt;"><?php echo $accesorios; ?></td>
        </tr>
    </table>

    <table cellspacing="0" style="width: 100%;font-size:12.5pt;font-weight:600;margin-top:20px;margin-left:10px;margin-right:10px;">
        <tr>
            <td align="left" style="padding-top:1px;padding-bottom:1px;padding-left:50px;text-transform: uppercase;">
                3.- imágenes servicio
            </td>
        </tr>
    </table>
    <table cellspacing="0" style="width: 100%;font-size:11pt;margin-top:0px;margin-left:10px;margin-right:10px;font-weight:600;">
        <tr>
            <td align="center" colspan="4" style="border: 1px solid #898A8A;width:130px;padding-left:7px;background-color:#7058c3;color:white;font-weight:600;">PREVIA LABOR</td>
        </tr>
        <tr>
            <td align="center" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">PATENTE</td>
            <td align="center" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">T. INSTRUMENTO</td>
            <td align="center" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">P. TABLERO</td>
            <td align="center" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">DAÑOS</td>
        </tr>
        <tr>
            <td align="center" style="border: 1px solid #898A8A;height:90px;"><?php if($img0_1==""){?> <div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div> <?php }else{ ?><img src="/var/www/html/cloux/archivos/tickets/<?php echo $img0_1; ?>" style="width: 100px;"><?php } ?></td>
            <td align="center" style="border: 1px solid #898A8A;height:90px;"><?php if($img0_2==""){?> <div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div> <?php }else{ ?><img src="/var/www/html/cloux/archivos/tickets/<?php echo $img0_2; ?>" style="width: 100px;"><?php } ?></td>
            <td align="center" style="border: 1px solid #898A8A;height:90px;"><?php if($img0_3==""){?> <div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div> <?php }else{ ?><img src="/var/www/html/cloux/archivos/tickets/<?php echo $img0_3; ?>" style="width: 100px;"><?php } ?></td>
            <td align="center" style="border: 1px solid #898A8A;height:90px;"><?php if($img0_4==""){?> <div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div> <?php }else{ ?><img src="/var/www/html/cloux/archivos/tickets/<?php echo $img0_4; ?>" style="width: 100px;"><?php } ?></td>
        </tr>
    </table>

    <table cellspacing="0" style="width: 100%;font-size:11pt;margin-top:20px;margin-left:10px;margin-right:10px;font-weight:600;">
        <tr>
            <td align="center" colspan="4" style="border: 1px solid #898A8A;width:130px;padding-left:7px;background-color:#7058c3;color:white;font-weight:600;">POSTERIOR LABOR</td>
        </tr>
        <tr>
            <td align="center" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">T. INSTRUMENTO</td>
            <td align="center" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">P. CONEXIÓN</td>
            <td align="center" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">V. PANORÁMICA</td>
            <td align="center" style="border: 1px solid #898A8A;width:130px;padding-left:7px;">U. EQUIPO</td>
        </tr>
        <tr>
            <td align="center" style="border: 1px solid #898A8A;height:90px;"><?php if($img1_1==""){?> <div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div> <?php }else{ ?><img src="/var/www/html/cloux/archivos/tickets/<?php echo $img1_1; ?>" style="width: 100px;"><?php } ?></td>
            <td align="center" style="border: 1px solid #898A8A;height:90px;"><?php if($img1_2==""){?> <div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div> <?php }else{ ?><img src="/var/www/html/cloux/archivos/tickets/<?php echo $img1_2; ?>" style="width: 100px;"><?php } ?></td>
            <td align="center" style="border: 1px solid #898A8A;height:90px;"><?php if($img1_3==""){?> <div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div> <?php }else{ ?><img src="/var/www/html/cloux/archivos/tickets/<?php echo $img1_3; ?>" style="width: 100px;"><?php } ?></td>
            <td align="center" style="border: 1px solid #898A8A;height:90px;"><?php if($img1_4==""){?> <div style='font-size:25pt;color:#dc3545;font-weight:600;'>N/A</div> <?php }else{ ?><img src="/var/www/html/cloux/archivos/tickets/<?php echo $img1_4; ?>" style="width: 100px;"><?php } ?></td>
        </tr>
    </table>

    <table cellspacing="0" style="width: 100%;font-size:12.5pt;font-weight:600;margin-top:20px;margin-left:10px;margin-right:10px;">
        <tr>
            <td align="left" style="padding-top:1px;padding-bottom:1px;padding-left:50px;text-transform: uppercase;">
                4.- comentarios
            </td>
        </tr>
    </table>

    <table cellspacing="0" style="width: 100%;font-size:10pt;margin-top:20px;margin-left:10px;margin-right:10px;font-weight:600;">
        <tr>
            <td align="left" colspan="4" style="border: 1px solid #898A8A;width:130px;padding:7px;height:50px;font-weight:normal;"><?php echo $comentario; ?></td>
        </tr>
    </table>

    <table cellspacing="0" style="width: 100%;font-size:12.5pt;font-weight:600;margin-top:20px;margin-left:10px;margin-right:10px;">
        <tr>
            <td align="left" style="padding-top:1px;padding-bottom:1px;padding-left:50px;text-transform: uppercase;">
                5.- firma recepción
            </td>
        </tr>
    </table>

    <table cellspacing="0" style="width: 100%;font-size:11pt;margin-top:20px;margin-left:10px;margin-right:10px;font-weight:600;">
        <tr>
            <td align="left" style="padding:7px;height:70px;"></td>
            <td align="center" style="border: 1px solid #898A8A;padding:7px;height:70px;"><img src="/var/www/html/cloux/archivos/firmas/<?php echo $firmaTec; ?>" style="width: 90px;"></td>
            <td align="center" style="border: 1px solid #898A8A;padding:7px;height:70px;"><img src="/var/www/html/cloux/archivos/firmas/<?php echo $firmaCli; ?>" style="width: 90px;"></td>
            <td align="left" style="padding:7px;height:70px;"></td>
        </tr>
        <tr>
            <td align="left" style="padding:7px;"></td>
            <td align="center" style="border: 1px solid #898A8A;padding:7px;text-transform: uppercase;width:240px;"><?php echo $tecnico; ?></td>
            <td align="center" style="border: 1px solid #898A8A;padding:7px;text-transform: uppercase;width:240px;"><?php echo $nombre; ?></td>
            <td align="left" style="padding:7px;"></td>
        </tr>
    </table>
</body>

</html>