<?php
date_default_timezone_set("America/Santiago");
function enpesos($precio){$precioamostrar=number_format ($precio, 0, '.', '.');return $precioamostrar;}
/**************************************
FUNCIONES LOGIN
*******************************************/
function get_clave($hash, $pass) {
return crypt($pass, $hash) == $hash;
}

function usuariologeado($id){
global $link;
$sql="select * from usuarios where usu_id='".$id."'";
$res=$link->query($sql);
$datos=array();
while($fila=mysqli_fetch_array($res)){
$datos[$id]["ultimo"]=devfechahora($fila["usu_ultimologin"]);
$datos[$id]["usuario"]=$fila["usu_usuario"];
$datos[$id]["correo"]=$fila["usu_correo"];
$foto = $fila["usu_foto"];
if($foto ==""){$foto="avatar_usuario.jpg";}
$datos[$id]["foto"]=$foto;
}
return $datos;
}


function getPuntosRut( $rut ){
	$rutTmp = explode( "-", $rut );
	return number_format( $rutTmp[0], 0, "", ".") . '-' . $rutTmp[1];
}

function post_clave($password, $cost = 11) {
    // Genera sal de forma aleatoria
$salt=substr(base64_encode(openssl_random_pseudo_bytes(17)),0,22);
// reemplaza caracteres no permitidos
$salt=str_replace("+",".",$salt);
// genera una cadena con la configuración del algoritmo
$param='$'.implode('$',array("2y",str_pad($cost,2,"0",STR_PAD_LEFT),$salt));
// obtiene el hash de la contraseña
return crypt($password,$param);
}


/*************** funciones fecha ********************/
function nombredia($fecha){
$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$ndia = date('w', strtotime($fecha));
return $dias[$ndia];
}
function hoy(){return date("d/m/Y");}
function hoyhora(){return date("Y-m-d H:m:s");}
function hoyhora0(){return date("Y-m-d 00:00:01");}
function hoyhora23(){return date("Y-m-d 23:59:59");}
function hoydate(){return date("Y-m-d");}
function hhmm($hora){
    $cadena = explode(':', $hora);
    if( isset($cadena[0]) &&  isset($cadena[1]) ){
        return $cadena[0].":".$cadena[1]?$cadena[1]:'' ;
    }
    return $hora;
}
function convfecha($fechagringa){
$cadena=explode('/',$fechagringa);
$nuevafecha="".$cadena[2]."/".$cadena[1]."/".$cadena[0]."";
return $nuevafecha;
}
function convfechadate($fechagringa){$cadena=explode('/',$fechagringa);
$nuevafecha="".$cadena[2]."-".$cadena[1]."-".$cadena[0]."";return $nuevafecha;
}
function convfechahora($fecha){list($fechagringa,$lahora) = explode(' ',$fecha);list($eldia,$elmes,$elano) = explode('/',$fechagringa);$nuevafecha="".$elano."/".$elmes."/".$eldia." ".$lahora.":00";return $nuevafecha;}
function devfecha($fechagringa){list($elano,$elmes,$eldia) = explode('-',$fechagringa);$nuevafecha="".$eldia."/".$elmes."/".$elano."";return $nuevafecha;}
function devfechahora($fecha){list($fechagringa,$lahora) = explode(' ',$fecha);list($elano,$elmes,$eldia) = explode('-',$fechagringa);$nuevafecha="".$eldia."/".$elmes."/".$elano." ".$lahora."";return $nuevafecha;}
function tiempotrans($fecha){
$periodos = array("segundo", "minuto", "hora", "día", "semana", "mes", "año", "década");
$duraciones = array("60","60","24","7","4.35","12","10");
$now = time();
$diferencia = $now - $fecha;
for($j = 0; $diferencia >= $duraciones[$j] && $j < count($duraciones)-1; $j++) {
$diferencia /= $duraciones[$j];
}
$diferencia = round($diferencia,1);
// si la diferencia son minutos 
if($j == 1){$diferencia="0.".$diferencia."";}
// condicion para agregar informacion de tiempo transcurrido
if($diferencia != 1) {
if($j != 5){$periodos[$j].= "s";}else{
$periodos[$j].= "es";
}
}
return $diferencia;
}

 function get_format($df) {

    $str = '';
    $str .= ($df->invert == 1) ? ' - ' : '';
    if ($df->y > 0) {
        // years
        $str .= ($df->y > 1) ? $df->y . ' Años ' : $df->y . ' Año ';
    } if ($df->m > 0) {
        // month
        $str .= ($df->m > 1) ? $df->m . ' Meses ' : $df->m . ' Mes ';
    } if ($df->d > 0) {
        // days
        $str .= ($df->d > 1) ? $df->d . ' Dias ' : $df->d . ' Dia ';
    } if ($df->h > 0) {
        // hours
        $str .= ($df->h > 1) ? $df->h . ' Horas ' : $df->h . ' Hora ';
    } if ($df->i > 0) {
        // minutes
        $str .= ($df->i > 1) ? $df->i . ' Minutos ' : $df->i . ' Minuto ';
    } if ($df->s > 0) {
        // seconds
        $str .= ($df->s > 1) ? $df->s . ' Segundos ' : $df->s . ' Segundo ';
    }

    return $str;
}
function tiempoentrefechas($inicio,$fin){
$periodos = array("segundo", "minuto", "hora", "día", "semana", "mes", "año", "década");
$duraciones = array("60","60","24","7","4.35","12","10");
$now = $fin;
$diferencia = $now - $inicio;
for($j = 0; $diferencia >= $duraciones[$j] && $j < count($duraciones)-1; $j++) {
$diferencia /= $duraciones[$j];
}
$diferencia = round($diferencia,1);
// si la diferencia son minutos 
if($j == 1){$diferencia="0.".$diferencia."";}
// condicion para agregar informacion de tiempo transcurrido
if($diferencia != 1) {
if($j != 5){$periodos[$j].= "s";}else{
$periodos[$j].= "es";
}
}
return $diferencia;
}
function calcula_tiempo($segundosinicio,$segundostermino){ 
    $end_time = '';
    $start_time = '';
$total_seconds = strtotime($end_time)-strtotime($start_time); 
$horas = floor ( $total_seconds / 3600 );
$minutes = ( ( $total_seconds / 60 ) % 60 );
$seconds = ( $total_seconds % 60 );
$time['horas']= str_pad( $horas, 2, "0", STR_PAD_LEFT );
$time['minutes']= str_pad( $minutes, 2, "0", STR_PAD_LEFT );
$time['seconds']= str_pad( $seconds, 2, "0", STR_PAD_LEFT );
$time= implode( ':', $time );
return $time;
}
function devmes($mes){
switch ($mes){
case '01': $mes="Enereo";break;
case '02': $mes="Febrero";break;
case '03': $mes="Marzo";break;
case '04': $mes="Abril";break;
case '05': $mes="Mayo";break;
case '06': $mes="Junio";break;
case '07': $mes="Julio";break;
case '08': $mes="Agosto";break;
case '09': $mes="Septiembre";break;
case '10': $mes="Octubre";break;
case '11': $mes="Noviembre";break;
case '12': $mes="Diciembre";break;
}
return $mes;
}
function devabecedario(){
$abc=array();
$abc[0]="A";$abc[1]="B";$abc[2]="C";$abc[3]="D";$abc[4]="E";$abc[5]="F";$abc[6]="G";	
$abc[7]="H";$abc[8]="I";$abc[9]="J";$abc[10]="K";$abc[11]="L";$abc[12]="M";$abc[13]="N";$abc[14]="O";$abc[15]="P";$abc[16]="Q";$abc[17]="R";$abc[18]="S";$abc[19]="T";$abc[20]="U";$abc[21]="V";$abc[22]="W";$abc[23]="X";$abc[24]="Y";$abc[25]="Z";
return $abc;	
}
function getdiames($fecha){
$sepfecha=explode("/",$fecha);
$diames=$sepfecha[0]."/".$sepfecha[1];
return $diames;
}
function selectyears($inicio){
$fin = date("Y") + 1;
$opciones="";
for($x = $fin ; $x >= $inicio ; $x--){
$opciones.="<option value='".$x."'>$x</option>";
}
return $opciones;
}
function calculaFecha($modo,$valor,$fecha_inicio=false){
if($fecha_inicio!=false) {$fecha_base = strtotime($fecha_inicio);}else {
$time=time();
$fecha_actual=date("Y-m-d",$time);
$fecha_base=strtotime($fecha_actual);
}
$calculo = strtotime("$valor $modo","$fecha_base");
return date("Y-m-d", $calculo);
}
function dias_transcurridos($fecha_i,$fecha_f){
$dias	= (strtotime($fecha_i)- strtotime($fecha_f))/86400;
$dias 	= abs($dias); 
$dias = floor($dias);		
return $dias;
}

function getDiasMes($fechagringa){
$sfh= explode(" ",$fechagringa);
$sf=explode("-",$sfh[0]);
$numero = cal_days_in_month(CAL_GREGORIAN, $sf[1],$sf[0]);
return $numero;
}
/******** fin funciones fechas *****************/




function obtenervalor($tabla,$b,$where){
global $link;$elid="";
$sqlf="select * from ".$tabla." ".$where."";$resf=mysqli_query($link, $sqlf);if(mysqli_errno($link)) die(mysqli_error($link));
while($fila=mysqli_fetch_array($resf, MYSQLI_ASSOC)) {$elid=$fila["".$b.""];}return $elid;}

function htmlselect($name,$idselect,$tabla,$campoid,$campovalue,$ancho,$enganche,$sqlextra,$orden,$onchange,$tamano='1',$partircero,$ocultarselect='no',$multiple="no",$class=''){
$elid="";
$trozos = explode("|", $campovalue);// campos a mostrar como textos de los options
$nvariables = count($trozos);// cuento los campos separados
//$i=1;
global $link;
if($ocultarselect!='si'){// si el select no debe estar oculto
$varser="<select name=\"".$name."\" class='form-control {$class}' ";// inicializo variable del tipo string para crear select
if($ancho!=''){// si valor de parametro no es igual a vacio agrega ancho al selects
$varser.= "style=\"width:".$ancho."px;\" ";
}
$varser.="id=\"".$idselect."\"";// agrega id al select
if($onchange!=""){ $varser.=" onchange=\"".$onchange."\"";}// si parametro de funcion onchange no esta vacion agrega la funcion enviada
//$varser.=" size=\"".$tamano."\"";// agrega tamaño al select
if($multiple=="multiple"){ $varser.=" multiple";}// si es un select  multiple
$varser.=">";// finalizo la apertura de la etiqueta select
}
if($partircero!='no'){// si patir de sero es igual a si entonces agrega option vacio como primer elemento
$varser.="<option value=\"\">-- --</option>";
}

$sqlf="select * from ".$tabla." ".$sqlextra." order by ".$orden."";//ejecuto consulta
$resf=$link->query($sqlf);
$total=mysqli_num_rows($resf);//cuento resultados
//mysqli_set_charset($link,"utf8");// para problemas con los acentos
while($fila= mysqli_fetch_array($resf, MYSQLI_ASSOC)) {// recorro resultado y creo opciones
$elid=$fila["".$campoid.""];
//$elvalue=$fila["".$campovalue.""];
for ($i = 0; $i < $nvariables; $i++) {
$txtrozos[$i]=$fila["".$trozos[$i].""];
}
$varser.="<option value=\"$elid\" ";
if($enganche==$elid){
$varser.= "selected ";
}
$varser.=">";
for ($i = 0; $i < $nvariables; $i++) {
$varser.="".$txtrozos[$i]." ";
}
$varser.="</option>";
}
$varser.="</select>";
echo $varser;
}
function crear_array($tabla,$campos,$indice,$sqlextra=""){
global $link;
$arreglo=array();
$columna = explode("|", $campos);
$nvariables = count($columna)-1;
$sql="select * from ".$tabla." ".$sqlextra."";
$result=$link->query($sql);
while($fila=mysqli_fetch_array($result)){
for ($i = 0; $i <= $nvariables; $i++) {
$arreglo["".$fila["".$indice.""].""][$i]=htmlentities($fila["".$columna[$i].""])." ";}
}return $arreglo;}
function limpiarcadena($s,$espacio,$minuscula){
$s = str_replace("[áàâãª]","a",$s);
$s = str_replace("[ÁÀÂÃ]","A",$s);
$s = str_replace("[éèê]","e",$s);
$s = str_replace("[ÉÈÊ]","E",$s);
$s = str_replace("[íìî]","i",$s);
$s = str_replace("[ÍÌÎ]","I",$s);
$s = str_replace("[óòôõº]","o",$s);
$s = str_replace("[ÓÒÔÕ]","O",$s);
$s = str_replace("[úùû]","u",$s);
$s = str_replace("[ÚÙÛ]","U",$s);
$s = str_replace("ñ","n",$s);
$s = str_replace("Ñ","N",$s);
if($espacio=="si"){$s = str_replace(" ","",$s);}
if($minuscula =="si"){$s=strtolower($s);}
return $s;
}

function quitarAcentosEspacios($cadena){
$cadena = str_replace("ñ","n",$cadena);
$cadena = str_replace("Ñ","N",$cadena);
$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
$modificadas= 'aaaaaaaceeeeiiiidnoooooouuuuy
bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
$cadena = utf8_decode($cadena);
$cadena = strtr($cadena, utf8_decode($originales), $modificadas);
$cadena = strtolower($cadena);
$cadena = str_replace(" ","",$cadena);;
return utf8_encode($cadena);
}


function generarCodigo($longitud) {
$key = '';
$pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
$max = strlen($pattern)-1;
for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
return $key;
}

/***************************
Funciones login
**************************/
function mesyears($cantidad){
$actual=date("Y") - $cantidad;
$fin = date("Y") + $cantidad;
$opciones="";
for($x = $actual ; $x <= $fin ; $x++){
if($x==date("Y")){$year="selected";}else{$year="";}
$opciones.="<option value='".$x."' ".$year.">$x</option>";
}
return $opciones;
}
function fechasegundos($fecha){
$cadena=explode('/',$fecha);
$nuevafecha="".$cadena[2]."-".$cadena[1]."-".$cadena[0]." 00:00:00";
$segundos = strtotime($nuevafecha);
return $segundos;
}
function pAvance($inicio,$fin){
$inicio = fechasegundos($inicio);
$fin = fechasegundos($fin);
$total = $fin - $inicio;
$actual = time();
$pasados = $actual - $inicio;
$porcentaje= round(($pasados*100)/$total,1);
return $porcentaje;
}


/*****************************
Funciones standard
*****************************/
function detalleUsuario($id){
global $link;
$sql="select * from usuarios where usu_id='".$id."'";
$res=$link->query($sql);
$datos=array();
$fila=mysqli_fetch_array($res);
$foto=$fila["usu_foto"];
if($foto==""){$foto="avatar_usuario.jpg";}else{$foto=$foto;}
$nombre=$fila["usu_nombre"];
$datos[$id]=array("nombre"=>$nombre,"foto"=>$foto);
return $datos;
}

function Notificar($emisor,$receptor,$mensaje,$titulo_enlace,$enlace,$estado){
global $link;
$sql="insert into notificaciones (not_fechahora,not_emisor,not_receptor,not_mensaje,not_tituloenlace,not_enlace,not_estado)values('".date("U")."','".$emisor."','".$receptor."','".$mensaje."','".$titulo_enlace."','".$enlace."','".$estado."')";
$res=$link->query($sql);
}
function diasfaltantes($fin){
$hoy=fechasegundos(hoy());
$fin=fechasegundos($fin);
$diferencia = $fin - $hoy;
$dias = round($diferencia / 86400);
return $dias;
}
function Historial($tipo,$referencia,$estado){
global $link;
$sql="insert into historiales(his_tipo,his_referencia,his_fechahora,his_estado)values('".$tipo."','".$referencia."','".date("U")."','".$estado."')";
$res=$link->query($sql);
/*echo $sql ;
    die() ;*/

}

function Adjunto($tipo,$referencia,$nombre,$ruta){
global $link;
$sql="insert into adjuntos(adj_tipo,adj_referencia,adj_nombre,adj_ruta)values('".$tipo."','".$referencia."','".$nombre."','".$ruta."')";
$res=$link->query($sql);
}
function InsertComentario($tipo,$referencia,$emisor,$comentario){
global $link;
$sql="insert into comentarios(com_tipo,com_referencia,com_emisor,com_comentario)values('".$tipo."','".$referencia."','".$emisor."','".$comentario."')";
$res=$link->query($sql);
}
function DevComentarios($tipo,$referencia){
global $link;
$comentarios=array();
$sql="select * from comentarios where com_tipo='".$tipo."' && com_referencia='".$referencia."' order by com_id desc ";
$res=$link->query($sql);
while($fila=mysqli_fetch_array($res)){
//$emisor=detalleEmisor($fila["com_emisor"]);
//$comentarios[]=array("nombre"=>$emisor[$fila["com_emisor"]]["nombre"],"foto"=>$emisor[$fila["com_emisor"]]["foto"],"fechahora"=>devfechahora($fila["com_fechahora"]),"comentario"=>$fila["com_comentario"],"estado"=>$fila["com_estado"]);
}
return $comentarios;
}
function ArrayUploads($archivos) {
$array_archivos = array();
$contar_archivos = count($archivos['name']);
$keys_archivos = array_keys($archivos);
for ($i=0; $i<$contar_archivos; $i++) {
foreach ($keys_archivos as $index) {
$array_archivos[$i][$index] = $archivos[$index][$i];
}
}
return $array_archivos;
}



function devAdjuntos($tipo,$ref){
global $link;
$adjuntos=array();
$sql="select * from adjuntos where adj_tipo=".$tipo." && adj_referencia=".$ref." order by adj_fechahora desc ";
$res=$link->query($sql);
while($fila=mysqli_fetch_array($res)){
$detalleAdjunto=getEstructuraArchivo($fila["adj_ruta"]);
$adjuntos[]=array("id"=>$fila["adj_id"],"nombre"=>$fila["adj_nombre"],"extension"=>$detalleAdjunto["extension"],"ruta"=>$fila["adj_ruta"],"fechahora"=>devfechahora($fila["adj_fechahora"]));
}
return $adjuntos;	
}


function getEstructuraArchivo($archivo){
$pospunto= strripos($archivo, ".");
$extension="";
if ($pospunto=== false) {
$extension="error, no se reconoce extension";
} else {
$extension=substr($archivo,$pospunto+1);
}
$nombre=substr($archivo,0,$pospunto);
$estructura["extension"]=$extension;
$estructura["nombre"]=$nombre;
return $estructura;
}


/*************************************************
***************************************************/
function PlantillaNotificacionJornada($nombre,$mensaje){
$plantilla='<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
<meta name="viewport" content="width=device-width" />
<meta charset="utf-8"/>
<title></title>
<!--<link href="plantilla_correo.css" media="all" rel="stylesheet" type="text/css" />-->
</head>
<style>
*{margin: 0;font-family: Verdana, Arial;box-sizing: border-box;font-size: 14px;}
img {max-width: 100%;}
body {-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;
width: 100% !important;height: 100%;line-height: 1.6em;}
table td {vertical-align: top;}
body {background-color: #f6f6f6;}
.body-wrap {background-color: #f6f6f6;width: 100%;}
.container {display: block !important;max-width: 600px !important;margin: 0 auto !important;clear: both !important;}
.content {max-width: 600px;margin: 0 auto;display: block;padding: 20px;}
.main {background-color: #fff;border: 1px solid #e9e9e9;border-radius: 3px;}
.content-wrap {padding: 20px;}
.content-block {padding: 0 0 20px;}
.header {width: 100%;margin-bottom: 20px;}
.footer {width: 100%;clear: both;color: #999;padding: 20px;}
.footer p, .footer a, .footer td {color: #999;font-size: 12px;}
h1, h2, h3 {font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;color: #000;margin: 40px 0 0;line-height: 1.2em;font-weight: 400;}
h1 {font-size: 32px;font-weight: 500;}
h2 {font-size: 24px;}
h3 {font-size: 18px;}
h4 {font-size: 14px;font-weight: 600;}
p, ul, ol {margin-bottom: 10px;font-weight: normal;}
p li, ul li, ol li {margin-left: 5px;list-style-position: inside;}
a {color: #348eda;text-decoration: underline;}
.btn-primary {text-decoration: none;color: #FFF;background-color: #348eda;border: solid #348eda;border-width: 10px 20px;line-height: 2em;font-weight: bold;text-align: center;cursor: pointer;display: inline-block;border-radius: 5px;text-transform: capitalize;}
.last {margin-bottom: 0;}
.first {margin-top: 0;}
.aligncenter {text-align: center;}
.alignright {text-align: right;}
.alignleft {text-align: left;}
.clear {clear: both;}
.alert {font-size: 16px;color: #fff;font-weight: 500;padding: 20px;text-align: center;border-radius: 3px 3px 0 0;}
.alert a {color: #fff;text-decoration: none;font-weight: 500;font-size: 16px;}
.alert-warning {background: #0b9bd9;}
.alert .alert-bad {background-color: #D0021B;}
.alert .alert-good {background-color: #68B90F;}
.invoice {margin: 40px auto;text-align: left;width: 80%;}
.invoice td {padding: 5px 0;}
.invoice .invoice-items {width: 100%;}
.invoice .invoice-items td {border-top: #eee 1px solid;}
.invoice .invoice-items .total td {border-top: 2px solid #333;border-bottom: 2px solid #333;font-weight: 700;}
@media only screen and (max-width: 640px) {
body {padding: 0 !important;}
h1, h2, h3, h4 {font-weight: 800 !important;margin: 20px 0 5px !important;}
h1 {font-size: 22px !important;}
h2 {font-size: 18px !important;}
h3 {font-size: 16px !important;}
.container {padding: 0 !important;width: 100% !important;}
.content {padding: 0 !important;}
.content-wrap {padding: 10px !important;}
.invoice {width: 100% !important;}
}
</style>
<body itemscope itemtype="http://schema.org/EmailMessage">
<table class="body-wrap">
<tr>
<td></td>
<td class="container" width="600">
<div class="content">
<table class="main" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td class="alert alert-warning" bgcolor="#0b9bd9">CLOUX</td>
</tr>
<tr>
<td class="content-wrap">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td class="content-block"><strong>Estimado(a) : </strong> '.$nombre.'</td>
</tr>
<tr>
<td class="content-block">
'.$mensaje.'</td>
</tr>
<tr>
<!--<td class="content-block">
<a href="#" class="btn-primary"></a>
</td>-->
</tr>
<tr>
<td class="content-block"></td></tr>
</table>
</td>
</tr>
</table>
</div>
</td>
<td></td>
</tr>
</table>
</body>
</html>';
return $plantilla;
}

function consultarInicio($conductor,$patente){
global $link;
$sql="select * from historialdejornadas where hdj_patente='".$patente."' order by hdj_id desc limit 0,1";
$res=$link->query($sql);
$cuenta = mysqli_num_rows($res);
if($cuenta > 0){
$fila = mysqli_fetch_array($res);
if($fila["hdj_tipo"]==1 && $fila["hdj_conductor"] != $conductor){
$retornar["estado"]=true;
$nombre=obtenervalor("conductores","con_nombre","where con_id='".$fila["hdj_conductor"]."'");
$apaterno=obtenervalor("conductores","con_apaterno","where con_id='".$fila["hdj_conductor"]."'");
$conductor=$nombre." ".$apaterno;
$retornar["conductor"]=$conductor;   
}else{
$retornar["estado"]=false;
}

}else{
$retornar["estado"]= false;  
}
return $retornar;
}

function getPatentes($id){
global $link;
$sql="select * from vehiculos where veh_cliente ='".$id."' && veh_estado=0";
$res=$link->query($sql);
$patentes=array();
while($fila=mysqli_fetch_array($res)){
$patentes[]=array("id"=>$fila["veh_id"],"patente"=>$fila["veh_patente"]);
}
return $patentes;
}

function getTransmisiones($vehiculo){
global $link;
// buscamos el ultimo registro ws del vehiculo
$sql="select * from datagetws where dgw_idvehiculo ='".$vehiculo."' && dgw_estado !='Desconocido' && dgw_ultimaposicion !='0000-00-00 00:00:00' order by dgw_fhcaptura desc limit 0,1";
$res=$link->query($sql);
$cuenta = mysqli_num_rows($res);
if($cuenta > 0 ){
$fila=mysqli_fetch_array($res);
$dateinicio = new DateTime($fila["dgw_ultimaposicion"]);
$ndias=getDiasMes($fila["dgw_ultimaposicion"]);
$datenow=new DateTime(date("Y-m-d H:i:s"));
$tiempo2= $dateinicio->diff($datenow);	
//$transcurrido = get_format($tiempo2);
$meses=$tiempo2->m;
$dias=$tiempo2->d;
$nhoras = $tiempo2->h;
$hmes= ($meses * $ndias) * 24;
$hdias=  $dias * 24;
$horas = $hmes + $hdias + $nhoras;
// if($meses > 0){
// $horas = (($meses * 30) * 24) + $tiempo2->h;
// }else{

// if($dias > 0){
// $horas = $horas + (($dias * 24) + $tiempo2->h);
// }
// else{	
// $horas=$tiempo2->h;
// }
// }


$h2="--";$h12="--";$h24="--";$h48="--";
if($horas < 2){
$h2=$horas;
}
if($horas >= 2 && $horas <=12){
$h12=$horas;
}
if($horas > 12 && $horas <=24){
$h24=$horas;	
}
if($horas > 24 && $horas <=48){
$h48=$horas;	
}
if($horas >=48){
$h48=$horas;	
}

$transcurrido[2]=$h2;
$transcurrido[12]=$h12;
$transcurrido[24]=$h24;
$transcurrido[48]=$h48;
$transcurrido["localidad"]=$fila["dgw_localidad"];
$transcurrido["ultima"]=devfechahora($fila["dgw_ultimaposicion"]);
}else{
$transcurrido[2]="--";
$transcurrido[12]="--";
$transcurrido[24]="--";
$transcurrido[48]="--";
$transcurrido["localidad"]="";
$transcurrido["ultima"]="";
}

return $transcurrido;
}

function esnumero($valor){
if(is_numeric($valor)){
return true;
}else{
return false;
}
}

function getObservacionesVeh($veh_id){
global $link;
$sql="select * from observacionesvehiculos where odv_vehiculo = '".$veh_id."' order by odv_fechahora";
$res=$link->query($sql);
$cuenta=mysqli_num_rows($res);
$obs=array();
if($cuenta > 0){
while($fila=mysqli_fetch_array($res)){
$detusuario = detalleUsuario($fila["odv_usuario"]);
$usuario=$detusuario[$fila["odv_usuario"]]["nombre"];
$foto=$detusuario[$fila["odv_usuario"]]["foto"];
$obs[]=array("id"=>$fila["odv_id"],"usuario"=>$usuario,"foto"=>$foto,"fecha"=>devfechahora($fila["odv_fechahora"]),"observacion"=>$fila["odv_observacion"]);
}
}
return $obs;
}
function formatpatente($patente){
$letras = strlen($patente);
$parte1 = substr($patente, 0,2);
$parte2 = substr($patente, 2,2);
$parte3 = substr($patente, 4,2);
$esnump2= esnumero($parte2);
$esnump3= esnumero($parte3);
if( $esnump2 && $esnump3){
$retornar=$parte1."-".$parte2."".$parte3;
}else{
$retornar=$parte1."".$parte2."-".$parte3;
}
if($letras > 6){
$parte4 = substr($patente, -1); 
$retornar=$retornar."-".$parte4;
}
return $retornar;
}
function convfechal1($fecha){
  list($fechagringa,$lahora) = explode(' ',$fecha);
  list($eldia,$elmes,$elano) = explode('/',$fechagringa);
  $nuevafecha="".$elano."-".$elmes."-".$eldia." ".$lahora."";
  return $nuevafecha;
  }
  
function convfechal($fecha){
  list($fechagringa,$lahora) = explode(' ',$fecha);
  list($eldia,$elmes,$elano) = explode('/',$fechagringa);
  $nuevafecha="".$elano."-".$elmes."-".$eldia."T".$lahora."";
  return $nuevafecha;
  }
  

/**********************************
FUNCIONES APP ESTANDARD
******************************/
function getInforJornada($conductor){
global $link;
$sql="select * from historialdejornadas where  hdj_conductor='".$conductor."' order by hdj_id desc limit 0,1";
$res=$link->query($sql);
$cuenta = mysqli_num_rows($res);
$datos["inicio"]=true;
$datos["fin"]=false;
$datos["idpatente"]=0; 
$datos["patente"]=0;
if($cuenta > 0){
$fila=mysqli_fetch_array($res);
if($fila["hdj_tipo"]==1){
$datos["inicio"]=false;
$datos["fin"]=true; 
$datos["idpatente"]=$fila["hdj_patente"]; 
$datos["patente"]=obtenervalor("vehiculos","veh_patente","where veh_id='".$fila["hdj_patente"]."'");
}
if($fila["hdj_tipo"]==2){
$datos["inicio"]=true;
$datos["fin"]=false; 
$datos["idpatente"]=0;
$datos["patente"]=0;   
}
}
return $datos;
}
function getDataEmpresa($conductor){
global $link;
$idempresa = obtenervalor("conductores","con_cliente","where con_id='".$conductor."'");
$sql="select cli_usuariows,cli_clavews,cli_nombrews from clientes where id='".$idempresa."'";
$res=$link->query($sql);
$fila=mysqli_fetch_array($res);
$datos["usuario"]=$fila["cli_usuariows"];
$datos["clave"]=$fila["cli_clavews"];
$datos["nombre"]=$fila["cli_nombrews"];
return $datos;

}
function getAlertasxG($id){
global $link;
$sql="select * from alertas where ale_grupo='".$id."'";
$res=$link->query($sql);
$alertas=array();
while($fila=mysqli_fetch_array($res)){
$patentes = array();
$sql2="select * from patentesxalerta where pxa_alerta='".$fila["ale_id"]."'";
$res2=$link->query($sql2);
while($fila2=mysqli_fetch_array($res2)){
$patentes[]=$fila2["pxa_patente"];
}	
$contactos = array();
$sql3="select * from contactosxalerta where cxa_alerta='".$fila["ale_id"]."'";
$res3=$link->query($sql3);
while($fila3=mysqli_fetch_array($res3)){
$contactos[]=array("nombre"=>$fila3["cxa_nombre"],"correo"=>$fila3["cxa_correo"]);	
}
$alertas[$fila["ale_id"]]["patentes"]=$patentes;
$alertas[$fila["ale_id"]]["contactos"]=$contactos;

}
return $alertas;
}

function getProxVeh($id){
    global $link;
    $sql="select pxv.*,pro.pro_codigo,pro.pro_nombre,pro.pro_serie, t3.ser_id , t3.ser_condicion, t3.ser_codigo, t5.sen_nombre as din1, t6.sen_nombre as din2, t7.sen_nombre as din3, t8.fam_nombre
            from productosxvehiculos pxv 
            left outer join productos pro on pxv.pxv_idpro=pro.pro_id
            left outer join familias t8 on t8.fam_id = pro.pro_familia
            left outer join serie_guia t3 on t3.ser_id = pxv.pxv_ideasi
            left outer join asociacion_vehiculos_sensores t4 on t4.veh_id = pxv.pxv_idveh and t4.avx_estado = 1 and t4.ser_id = t3.ser_id
            left outer join sensores t5 on t5.sen_id = t4.sen_id_1
            left outer join sensores t6 on t6.sen_id = t4.sen_id_2
            left outer join sensores t7 on t7.sen_id = t4.sen_id_3
            where pxv.pxv_idveh = ".$id." and pxv.pxv_estado = 1 GROUP by 1";
    $res        = $link->query($sql);
    $productos  = array();
    while($fila = mysqli_fetch_array($res)){
        $serie      = $fila["pxv_nserie"];
        if($fila["pro_serie"]==1){
            $tieneserie = "SI"; 
        }else{
            $tieneserie = "NO";
        }
        if($fila["pxv_cantidad"]==0){
            $cantidad = 1;
        }else{
            $cantidad = $fila["pxv_cantidad"];
        }

        $detalleKits = array();
        $sql1        = "SELECT easi.*, pro1.pro_nombre namegps, pro2.pro_nombre namesim FROM equipos_asociados easi LEFT OUTER JOIN productos pro1 ON pro1.pro_id=easi.easi_idgps LEFT OUTER JOIN productos pro2 ON pro2.pro_id=easi.easi_accesorio WHERE easi.easi_id={$fila['pxv_ideasi']}";
        $res1        = $link->query($sql1);

        while($fila1 = mysqli_fetch_array($res1)){
            $detalleKits[] = array(
                'id'       => $fila1['easi_id'],
                'idgps'    => $fila1['easi_idgps'],
                'ngps'     => $fila1['namegps'],
                'seriegps' => $fila1['easi_seriegps'],
                'idsim'    => $fila1['easi_accesorio'],
                'nsim'     => $fila1['namesim'],
                'seriesim' => $fila1['easi_seriesim'],
                'bodega'   => $fila1['easi_bodega'],
                'usercrea' => $fila1['easi_user_create'],
                'fcrea'    => $fila1['easi_create_at'],
                'estado'   => $fila1['easi_estado'],
            );
        }
        $productos[]=array("din1"=>$fila["din1"],"din2"=>$fila["din2"],"din3"=>$fila["din3"],"ser_condicion"=>$fila["ser_condicion"],"ser_id"=>$fila["ser_id"],"idpxv"=>$fila["pxv_id"],"codigo"=>$fila["pro_codigo"],"cantidad"=>$cantidad,"producto"=>$fila["pro_nombre"],"tieneserie"=>$tieneserie,"serie"=>$serie,"tipo"=>$fila['pxv_tipo'],"ideasi"=>$fila['pxv_ideasi'],"proid"=>$fila['pxv_idpro'], 'familia'=>$fila['fam_nombre'],"kitdetalle"=>$detalleKits);	
    }
    return $productos;
}

function getPXV($id){
global $link;
$sql="select * from productosxvehiculos where pxv_id='".$id."'";
$res=$link->query($sql);
$fila=mysqli_fetch_array($res);
$detalle["id"]=$fila["pxv_id"];
$detalle["idveh"]=$fila["pxv_idveh"];
$detalle["cantidad"]=$fila["pxv_cantidad"];
$detalle["idpro"]=$fila["pxv_idpro"];
$detalle["serie"]=$fila["pxv_nserie"];
return $detalle;
}

function getPXT($id){
global $link;
$sql="select * from productosxtecnico where pxt_id='".$id."'";
$res=$link->query($sql);
$fila=mysqli_fetch_array($res);
$detalle["id"]=$fila["pxt_id"];
$detalle["cantidad"]=$fila["pxt_cantidad"];
$detalle["idpro"]=$fila["pxt_idpro"];
$detalle["serie"]=$fila["pxt_nserie"];
return $detalle;
}


function getProxTec($id){

    global $link;

    $sql = "select pxt.*,pro.pro_codigo,pro.pro_nombre,pro.pro_serie, t3.per_nombrecorto 
            from productosxtecnico pxt 
            left outer join productos pro on pxt.pxt_idpro=pro.pro_id 
            left outer join personal t3 on t3.per_id = pxt.pxt_idtecnico
            where pxt.pxt_idtecnico ='".$id."'";
    $res       = $link->query($sql);
    $productos = array();

    while($fila=mysqli_fetch_array($res)){
        if($fila["pro_serie"]==1){
             $tieneserie = "SI";
             $cantidad   = 1;
        }else{
             $tieneserie = "NO";
             $cantidad   = $fila["pxt_cantidad"];
        }
        if($fila["pxt_estado"]==1){$estado="BUENO";}
        if($fila["pxt_estado"]==2){$estado="MALO";}
        if($fila["pxt_estado"]==0){$estado="NO REGISTRADO";}

        $detalleKits = array();
        $sql1        = "SELECT easi.*, pro1.pro_nombre namegps, pro2.pro_nombre namesim FROM equipos_asociados easi LEFT OUTER JOIN productos pro1 ON pro1.pro_id=easi.easi_idgps LEFT OUTER JOIN productos pro2 ON pro2.pro_id=easi.easi_accesorio WHERE easi.easi_id={$fila['pxt_ideasi']}";

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
        }
        
        //if(!is_null($fila["pro_nombre"])){
        $productos[]=array("nomtecnico"=>$fila["per_nombrecorto"],"idpxt"=>$fila["pxt_id"],"idpro"=>$fila["pxt_idpro"],"codigo"=>$fila["pro_codigo"],"cantidad"=>$cantidad,"producto"=>$fila["pro_nombre"],"tieneserie"=>$tieneserie,"serie"=>$fila["pxt_nserie"],"estado"=>$estado,"observaciones"=>$fila["pxt_observaciones"],'tipo'=>$fila['pxt_tipo'],'ideasi'=>$fila['pxt_ideasi'],"kitdetalle"=>$detalleKits);    
        //}
    }
    return $productos;

}

function getSeriesPro($id){
    global $link;
    // estado 1=> producto disponible en bodega principal
    // estado 2=> producto en bodega tecnico
    $sql    = "select * from codigosxproducto where cxp_producto='".$id."' && cxp_estado=1";
    $res    = $link->query($sql);
    $series = array();

    while($fila=mysqli_fetch_array($res)){
         $series[$fila["cxp_id"]] = $fila["cxp_codigo"];
    }
    return $series;
}

function getCodigoxTraspaso($id){
    global $link;

    $sql     = "select * from codigosxtraspaso where cxt_detalletraspaso='".$id."'";
    $res     = $link->query($sql);
    $cuenta  = mysqli_num_rows($res);
    $codigos = array();

    if($cuenta > 0){
         while($fila = mysqli_fetch_array($res)){
         $codigos[] = $fila["cxt_serie"];
    }
}
return $codigos;
}


function getDetalleTraspaso($id){
    global $link;
    $sql   = "select dtras.*, pro.pro_nombre from detalletraspaso dtras left outer join productos pro on dtras.dtras_producto = pro.pro_id where dtras.dtras_traspaso='".$id."'";
    $res   = $link->query($sql);
    $dtras = array();

    while($fila=mysqli_fetch_array($res)){
         $codigos = getCodigoxTraspaso($fila["dtras_id"]);
         $dtras[] = array("idtras"=>$fila["dtras_id"],"producto"=>$fila["pro_nombre"],"cantidad"=>$fila["dtras_cantidad"],"codigos"=>$codigos,'tipo'=>$fila["dtras_tipo"],'idpro'=>$fila["dtras_producto"],'seriegps'=>$fila["dtras_seriegps"],'seriesim'=>$fila["dtras_seriesim"],'idasi'=>$fila["dtras_idasi"]);	
    }
    return $dtras;
}


function getCodigoxDevolucion($id){
global $link;
$sql="select * from codigosxdevolucion where cxd_detalledevolucion='".$id."'";
$res=$link->query($sql);
$cuenta = mysqli_num_rows($res);
$codigos=array();
if($cuenta > 0){
while($fila=mysqli_fetch_array($res)){
$codigos[]=$fila["cxd_serie"];
}

}
return $codigos;
}

function getDetalleDevolucion($id){
global $link;
$sql="select ddev.*, pro.pro_nombre from detalledevolucion ddev left outer join productos pro on ddev.ddev_producto = pro.pro_id where ddev.ddev_devolucion='".$id."'";
$res=$link->query($sql);
$dtras=array();
while($fila=mysqli_fetch_array($res)){
$codigos=getCodigoxDevolucion($fila["ddev_id"]);
$ddev[]=array("iddev"=>$fila["ddev_id"],"producto"=>$fila["pro_nombre"],"cantidad"=>$fila["ddev_cantidad"],"codigos"=>$codigos);	
}
return $ddev;
}
function getStockxProducto($id,$serie){
global $link;
$idpro=$id;
$proserie=$serie;
$npro=0;
//$stockpro=obtenervalor("productos","pro_stock","where pro_id='".$idpro."'");
$series=array();
$prosinseriemalos=0;
if($proserie==1){
$sql="select cxp_codigo, cxp_estado,cxp_info from codigosxproducto where cxp_producto='".$idpro."' && (cxp_estado=1 || cxp_estado = 3)";
$res=$link->query($sql);
while($fila=mysqli_fetch_array($res)){
if(intval($fila["cxp_estado"])==1){$estado="BUENO";$info="";}else{$estado="MALO";$info=$fila["cxp_info"];}
$series[$fila["cxp_codigo"]]=array("estado"=>$estado,"info"=>$info);
}
$npro=count($series);
}else{
$npro = obtenervalor("productos","pro_stock","where pro_id='".$idpro."'");
$sql="select cxp_cantidad from codigosxproducto where cxp_producto='".$idpro."' &&  cxp_estado = 3";
$res=$link->query($sql);
if(mysqli_num_rows($res) > 0){
$fila=mysqli_fetch_array($res);
$prosinseriemalos= $fila["cxp_cantidad"];
//$npro = $npro + $prosinseriemalos;
}
}



// productos x tecnico
$sql1="select * from productosxtecnico  where pxt_idpro='".$idpro."'";
$res1=$link->query($sql1);
$pxt=array();
if($proserie == 1){
$pxtcantidad=mysqli_num_rows($res1);
while($fila1=mysqli_fetch_array($res1)){
$tecnico=obtenervalor("personal","per_nombrecorto","where per_id='".$fila1["pxt_idtecnico"]."'");
if($tecnico!=""){
$pxt[]=array("tecnico"=>$tecnico,"serie"=>$fila1["pxt_nserie"],"estado"=>$fila1["pxt_estado"]); 
}
}
}else{
$pxtcantidad=0;
while($fila1=mysqli_fetch_array($res1)){
$tecnico=obtenervalor("personal","per_nombrecorto","where per_id='".$fila1["pxt_idtecnico"]."'");
if($tecnico!=""){
$pxtcantidad= $pxtcantidad + $fila1["pxt_cantidad"];
$pxt[]=array("tecnico"=>$tecnico,"cantidad"=>$fila1["pxt_cantidad"],"estado"=>$fila1["pxt_estado"]);
}
}   
}

// productos instalados en vehiculos
$sql2="select * from productosxvehiculos where pxv_idpro='".$idpro."'";
$res2=$link->query($sql2);
$pxv=array();
while($fila2=mysqli_fetch_array($res2)){
$patente=obtenervalor("vehiculos","veh_patente","where veh_id='".$fila2["pxv_idveh"]."'");
if($fila2["pxv_cantidad"] == 0){
$cantidad=1;
}else{
$cantidad=$fila2["pxv_cantidad"];
}
$pxv[]=array("patente"=>$patente,"cantidad"=>$cantidad,"serie"=>$fila2["pxv_nserie"]);
}
/*
$inventario["idpro"]=$idpro;
$inventario["stock"]=$stockpro;


*/
$spro= $npro + $pxtcantidad + count($pxv) + $prosinseriemalos;
$inventario["stock"]=$spro;
$inventario["series"]=$series;
$inventario["pxtcantidad"]=$pxtcantidad;
$inventario["pxtcantidadmalos"]=$prosinseriemalos;
$inventario["pxt"]=$pxt;
$inventario["pxv"]=$pxv;
return $inventario;

}

function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
    }

    return $token;
}

function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd > $range);
    return $min + $rnd;
}
?>