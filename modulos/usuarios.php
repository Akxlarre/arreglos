<!-- modal -->
<div class="modal" id="musuarios">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title"></h4>
</div>
<div class="modal-body"></div>
<div class="modal-footer">
</div>
</div>
</div>
</div>
<!-- fin modal -->

<section class="content">

		
<div class="row submenu">
	<div class="col-md-12">
		<a href="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>&submenu=nuevousuario" class="btn btn-success btn-rounded" id="btn_nuevousuario">
			<i class="fa fa-plus" aria-hidden="true"></i> Agregar Usuario
		</a>
	</div>
</div>

<div class="row top20" id="listausuarios">
<div class="col-md-12" id="listadousuarios">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Listado de usuarios registrados</h3></div>
<div class="box-body">
<table class="table table-bordered table-striped table-sm" id="tbusuarios"> 
<thead class="thead-dark">
<th>N°</th>
<th>Nombre</th>
<th>Usuario</th>
<th>Correo</th>
<th>Perfil</th>
<th>Personal</th>
<th class="text-center">Permisos</th>
<th class="text-center"></th>
<th class="text-center"></th>
</thead>
<tbody>
<?php
$sqlu="SELECT u.*, tu.tusu_nombre, pe.per_nombrecorto as nombrepersonal from usuarios as u
left join tipo_usuario as tu on (u.usu_perfil = tu.tusu_id)
left join personal as pe on (u.usu_idpersonal = pe.per_id)
where u.usu_estado = 1";
$resu=$link->query($sqlu);
$x=0;
while($filau=mysqli_fetch_array($resu)){
$x++;
?>
<tr id="usuario<?=$filau["usu_id"];?>">
<td><?=$x;?></td>
<td><?=$filau["usu_nombre"];?></td>
<td><?=$filau["usu_usuario"];?></td>
<td><?=$filau["usu_correo"];?></td>
<td><?=$filau["tusu_nombre"];?></td>
<td><?=$filau["nombrepersonal"];?></td>
<td class="text-center"><a href="index.php?menu=usuarios&idmenu=<?=$_REQUEST["idmenu"];?>&usuario=<?=$filau["usu_id"];?>" class="btn btn-sm btn-info btn-circle"><i class="fa fa-unlock-alt" aria-hidden="true"></i></a></td>
<td class="text-center"><a href="index.php?menu=usuarios&idmenu=<?=$_REQUEST["idmenu"];?>&editar=<?=$filau["usu_id"];?>" class="btn btn-sm btn-warning btn-circle"><i class="fa fa-edit"></i></a></td>
<td class="text-center"><span class="pointer btn btn-danger btn-sm btn-circle" onclick="eliusuario('<?=$filau['usu_id'];?>','<?=$filau['usu_nombre'];?>')"><i class="fa fa-trash"></i></span></td>
</tr>
<?php
}
?>
</tbody>
</table>
<script>
window.usuarios;
function getTabUsuarios(){
var randomNo = Math.floor(Math.random()*9999999);
$.get("operaciones.php", {numero:''+randomNo+'',operacion:'getTabUsuarios',retornar:'no'}
,function(data){
usuarios = $.parseJSON(data);
fila = "";
x=0;
$.each(usuarios,function(index,valor){
x++;
fila+="<tr id='usuario"+valor.id+"'><td>"+x+"</td><td>"+valor.rut+"</td><td>"+valor.nombre+"</td><td>"+valor.area+"</td><td>"+valor.cargo+"</td><td>"+valor.usuario+"</td><td>"+valor.correo+"</td><td class='text-center'><span class='pointer btn btn-primary btn-circle'><i class='fa fa-unlock-alt' aria-hidden='true'></i></span></td><td class='text-center'><span class='pointer btn btn-warning btn-circle' onclick='editarUsuario(\""+index+"\")'><i class='fa fa-pencil-square-o'></i></span></td><td class='text-center'><span class='pointer btn btn-danger btn-circle' onclick='eliusuario(\""+valor.id+"\",\""+valor.nombre+"\")'><i class='fa fa-trash-o'></i></span></td></tr>";
	
});
$("#tbusuarios tbody").html(fila);
});

}

</script>
</div>
</div>
</div>
<?php

if(isset($_REQUEST['repetido'])){
	if($_REQUEST['repetido']=='1'){
		?>	
		<script type="text/javascript">
			Swal.fire({
			  title: "Error",
			  text: "Usuario repetido",
			  icon: "warning"
			});
		</script>
		<?php

	}
}

/**********************************
EDITAR USUARIO 
**********************************/
$idusuario=0;
if(isset($_REQUEST["editar"])){
$idusuario=$_REQUEST["editar"];
$sql="select * from usuarios where usu_id='".$idusuario."'";
$res=$link->query($sql);
while($fila=mysqli_fetch_array($res)){
/*echo $fila["usu_claveoriginal"].'<br>';*/
$e_id=$fila["id"];
$e_nombre=$fila["usu_nombre"];
$e_perfil=$fila["usu_perfil"];
$e_clave=$fila["usu_claveoriginal"];
$e_usuario=$fila["usu_usuario"];
$e_foto=$fila["usu_foto"];
$e_bbdd=$fila['usu_bbdd'];
$e_empresa=$fila['usu_empresa'];

$e_personal=$fila['usu_idpersonal'];

/*echo $e_clave.'<br>';*/

if($e_bbdd=='cloux'){
	$e_empresa='dsolutions';
}
if($e_foto==""){
	$foto="avatar_usuario.jpg";
}else{
	$foto=$e_foto;
}
$e_correo=$fila["usu_correo"];
}

if(isset($_REQUEST['submenu'])){
	if($_REQUEST['submenu']=='nuevousuario'){
		$idusuario=0;
	}
}
?>
<script>
	$(document).ready(function(){
		let empresa = '<?=$e_empresa?>';
		let perfil = '<?=$e_perfil?>';
		let idpersonal = '<?=$e_personal?>';
		if(perfil==1){
			$('#clave').val('<?php echo $e_clave?>');
			$('#clave').attr('type', 'text');
			
		}else{
			$('#clave').val('<?php echo $e_clave?>');
			$('#clave').attr('type', 'password');
			
		}
		$("#listadousuarios").hide();
		let select = $('#cliente').html();
		$('#cliente').html('<option value="dsolutions">D-SOLUTIONS INTERNO</option>'+select);
		$('#cliente').val(empresa);

		$('#tusuario').val(perfil);
		$('#usu_idpersonal').val(idpersonal);

		
        //alert("1");
	})
	
</script>
<div class="col-md-12" id="editarusuario">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Editando usuario : <b><?=$e_nombre;?></b></h3>
<div class="box-tools pull-right">
<span class="pointer text-blanco" onclick="updateurl('index.php?menu=usuarios&idmenu=<?=$_REQUEST['idmenu'];?>')"><i class="fa fa-2x fa-times-circle-o"></i></span>
</div>
</div>
<div class="box-body">
	<form class="form-horizontal" action="operaciones.php" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="operacion" value="agregarusuario"/>
		<input type="hidden" name="retornar" value="index.php?menu=usuarios&idmenu=<?=$_REQUEST["idmenu"];?>"/>
		<input type="hidden" name="idusuario" value="<?php echo $idusuario?>">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="col-sm-2 control-label txtleft">Nombre</label>
					<div class="col-sm-12">
					<input type="text"  name="nombre" class="form-control" value="<?=$e_nombre;?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label txtleft">Correo</label>
					<div class="col-sm-12">
					<input type="text"  name="correo" class="form-control" value="<?=$e_correo;?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label txtleft">Empresa</label>
					<div class="col-sm-12">
					<?php htmlselect('cliente','cliente','clientes','id','cuenta','','','WHERE cuenta!="" group by cuenta','cuenta','','','si','no','no');?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label txtleft">Tipo de Usuario</label>
					<div class="col-sm-12">
					<?php htmlselect('tusuario','tusuario','tipo_usuario','tusu_id','tusu_nombre','',$idcliente,'where tusu_estado=1 ','tusu_nombre','','','si','no','no');?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label txtleft">Tipo de Personal</label>
					<div class="col-sm-12">
					<?php htmlselect('usu_idpersonal','usu_idpersonal','personal','per_id','per_nombrecorto','',$idcliente,'where deleted_at is NULL ','per_nombrecorto','','','si','no','no');?>
					</div>
				</div>

			</div>
			<div class="col-md-6">
			<div class="form-group">
				<label class="col-sm-2 control-label txtleft">Usuario</label>
				<div class="col-sm-12">
				<input type="text"  name="usuario" class="form-control" value="<?=$e_usuario;?>">
				</div>
				<div class="col-sm-1 txtcolor-azulmenu padtop7 oculto" id="validandouser"><i class="fa fa-cog fa-lg fa-spin fa-fw"></i></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label txtleft">Clave</label>
				<div class="col-sm-12">
				<input type="password" name="clave" class="form-control" value="<?=$e_clave;?>" id="clave">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label txtleft">Foto</label>
				<div class="col-sm-12">
				<input type="file" class='form-control' name="foto" >
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label txtleft">BBDD</label>
				<div class="col-sm-12">
				<input type="text"  name="bbdd" class="form-control" value="<?=$e_bbdd?>">
				</div>
			</div>
			</div>
		</div>
		
		<div class="form-group">
		<div class="col-sm-offset-2 col-sm-6">
		<?php 
		if(isset($_REQUEST['editar'])){
			if($_REQUEST['editar']>0){
				?>
				<button type="submit" class=" btn btn-success btn-rounded">Actualizar usuario</button>
				<?php
			}else{
				?>
				<button type="submit" class=" btn btn-success btn-rounded">Registrar nuevo usuario</button>
				<?php
			}
		}
		?>

		
		</div>
		</div>
	</form>
<!-- <form class="form-horizontal" action="operaciones.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="operacion" value="editarusuario"/>
<input type="hidden" name="idusuario" value="<?=$idusuario;?>"/>
<input type="hidden" name="retornar" value="index.php?menu=usuarios&idmenu=<?=$_REQUEST["idmenu"];?>"/>
<div class="col-sm-4">
<div class="text-center">
<img src="img/<?=$foto;?>" class="avatarperfil img-circle img-thumbnail" alt="avatar">
<h6>Cambiar foto</h6>
<div class="col-sm-12 input-group ">
<input type="file" class='form-control gruporight' name="e_foto" >
<div class="input-group-addon igaright iconlogin" id="iconfoto"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i></div>
</div>
</div>
</div>

<div class="col-sm-8">
<div id="formeditarusuario">
<div class="form-group">
<label class="col-sm-2 control-label txtleft">Nombre</label>
<div class="col-sm-6">
<input type="text"  name="nombre" class="form-control" value="<?=$e_nombre;?>">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label txtleft">Correo</label>
<div class="col-sm-6">
<input type="text"  name="correo" class="form-control" value="<?=$e_correo;?>">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label txtleft">Usuario</label>
<div class="col-sm-4">
<input type="text"  name="usuario" class="form-control" value="<?=$e_usuario;?>" disabled >
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label txtleft">Clave</label>
<div class="col-sm-4">
<input type="password"  name="clave" class="form-control" value="******" disabled >
</div>
<div class="col-sm-1" id="iconclave"><span class="pointer btn btn-info btn-circle" onclick="actualizapass();"><i class="fa fa-lock" aria-hidden="true"></i></span></div>
</div>

<div class="form-group">
<div class="col-sm-offset-2 col-sm-4">
<button type="submit" class="btn btn-success btn-rounded">Editar</button></div>
</div>
</div>

<div id="cambiarclave" class="oculto">
<div class="col-md-12" id="contentRP">
<div class="col-sm-12" id="titulo_RP">Cambiar Contraseña</div>
<div id="inputs_np">
<label class="col-sm-4 lblnormal color_RP top10">Nueva Contraseña</label>
<div class="col-sm-5 top10"><input type="password" name="nuevapass" class="form-control"/></div>
<div class="col-sm-1 top10 txtcolor-error padtop7 oculto errorpass"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></div>

<label class="col-sm-4 lblnormal color_RP top10">Repetir Contraseña</label>
<div class="col-sm-5 top10"><input type="password" name="nuevapass1" class="form-control"/></div>
<div class="col-sm-1 top10 txtcolor-error padtop7 oculto errorpass"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></div>

<label class="col-sm-12 txtnormal top10 txtcolor-error oculto" id="lbl_errorPass">¡Error las contraseñas no coinciden !</label>

<label class="col-sm-4">&nbsp;</label>
<div class="col-sm-3 top20">
<button type="button" class="btn btn-danger" onclick="volveralform()">Cancelar</button>
</div>
<div class="col-sm-3 top20"><button type="button" class="btn btn-success" onclick="e_actualizarpass();">Cambiar</button></div>
<div class="col-sm-1 top20 txtcolor-azulmenu padtop7 oculto" id="recuperando"><i class="fa fa-cog fa-lg fa-spin fa-fw"></i></div>
</div>
</div>
<div class="col-md-12 oculto" id="exitoRP">
<div class="col-sm-12" id="titulo_ERP">Contraseña actualizada <i class="fa fa-check-circle" aria-hidden="true"></i></div>
<div class="col-sm-12 top20">
<p>Tu contraseña ha sido actualizada, para volver al formulario haz clic en el siguiente boton.</p>
</div>
<div class="col-sm-5"><button type="button" class="btn btn-success" onclick="volveralform()">Volver al formulario</button></div>
</div>
</div>
</div>
</form> -->
</div>
</div>
</div>

<?php
}
if(isset($_REQUEST["usuario"])){
$idusuario=$_REQUEST["usuario"];
?>
<script>
	$(document).ready(function(){
		$("#listadousuarios").hide();
		let select = $('#cliente').html();
		$('#cliente').html('<option value="dsolutions">D-SOLUTIONS INTERNO</option>'+select);
		$('#cliente').val('')
	})
</script>
<div class="col-md-12" id="permisosusuario">
<div class="box box-inverse box-solid">
<div class="box-header with-border"><h3 class="box-title">Asignar permisos al usuario : <b><?=obtenervalor("usuarios","usu_nombre","where usu_id='".$idusuario."'");?></b></h3>
<div class="box-tools pull-right">
<span class="pointer text-blanco" onclick="updateurl('index.php?menu=usuarios&idmenu=<?=$_REQUEST['idmenu'];?>')"><i class="fa fa-2x fa-times-circle-o"></i></span>
</div>
</div>

<div class="box-body">

<?php
$col1="";
$col2="";

$sqlm="select * from menus";
$resm=$link->query($sqlm);
$x=0;
while($filam=mysqli_fetch_array($resm)){
$x++;
$idmenu=$filam["id"];
$menu=$filam["nombre"];
$modmenu="<div class='col-sm-6'>
<div class='card' id='menu".$id."'>
<div class='card-body'>
<h4 class='card-title'>".$menu."</h4>";

$sqlmo="select * from modulos where idmenu='".$idmenu."'";
$resmo=$link->query($sqlmo);
while($filamo=mysqli_fetch_array($resmo)){
$idmo=$filamo["id"];
$modulo=$filamo["nombre"];
$modmenu.="<div class='input-group'><input type='text' value='".$modulo." ' class='form-control gruporight' disabled ><div class='input-group-addon igaright'>
&nbsp;
<input style='margin-top: 13px' type='checkbox' id='check".$idmo."' class='pointer' onclick='asignarpermiso(\"".$idmo."\",\"".$idusuario."\");'/></div></div>";
//$modmenu.="<div class='filamodulo' id='modulo".$idmo."'><div class='left width50p'>".$modulo."</div><input type='checkbox' id='check".$idmo."' onclick='asignarpermiso(\"".$idmo."\",\"".$idusuario."\");'/></div>";
}
$modmenu.="</div></div></div>";
if ($x%2==0){
$col2.=$modmenu;
}else{
$col1.=$modmenu;
}

?>

<!--
<div class="col-sm-6">
<div class="box box-primary box-solid" id="menu<?=$id;?>">
<div class="box-header with-border"><h3 class="box-title"><?=$menu;?></h3></div>
<div class="box-body">
<?php
$sqlmo="select * from modulos where idmenu='".$id."'";
$resmo=$link->query($sqlmo);
while($filamo=mysqli_fetch_array($resmo)){
$idmo=$filamo["id"];
$modulo=$filamo["nombre"];
?>
<div class="filamodulo" id="modulo<?=$idmo;?>"><div class="left width50p"> <?=$modulo;?></div><input type="checkbox" id="check<?=$idmo;?>" onclick="asignarpermiso('<?=$idmo;?>','<?=$idusuario;?>');"/></div><?php
}?>
</div>
</div>
</div>-->
<?php
}
?>

<div class="col-sm-12 row" id="col1">
<?=$col1;?>
</div>
<div class="col-sm-12 row" id="col2">
<?=$col2;?>
</div>

</div>
</div>
</div>
<?php
}
if(isset($_REQUEST["submenu"])){?>
<script>
$(document).ready(function(){
		$("#listadousuarios").hide();
		let select = $('#cliente').html();
		$('#cliente').html('<option value="dsolutions">D-SOLUTIONS INTERNO</option>'+select);
		$('#cliente').val('')
		$("#btn_nuevousuario").attr("disabled","disabled");
	})

</script>
<!--AGREGAR USUARIO -->
<div class="col-md-12">
<div class="card">
	<div class="card-header p-1">
		<h4>Agregar Usuario</h4>
	</div>
	<div class="card-body">
		<form class="form-horizontal" action="operaciones.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="operacion" value="agregarusuario"/>
			<input type="hidden" name="retornar" value="index.php?menu=usuarios&idmenu=<?=$_REQUEST["idmenu"];?>"/>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="col-sm-2 control-label txtleft">Nombre</label>
						<div class="col-sm-12">
						<input type="text"  name="nombre" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label txtleft">Correo</label>
						<div class="col-sm-12">
						<input type="text"  name="correo" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label txtleft">Empresa</label>
						<div class="col-sm-12">
						<?php htmlselect('cliente','cliente','clientes','id','cuenta','',$idcliente,'WHERE cuenta!="" group by cuenta','cuenta','getVehCli()','','si','no','no');?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label txtleft">Tipo de Usuario</label>
						<div class="col-sm-12">
						<?php htmlselect('tusuario','tusuario','tipo_usuario','tusu_id','tusu_nombre','',$idcliente,'where tusu_estado=1 ','tusu_nombre','','','si','no','no');?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-4 control-label txtleft">Tipo de Personal</label>
						<div class="col-sm-12">
						<?php htmlselect('usu_idpersonal','usu_idpersonal','personal','per_id','per_nombrecorto','',$idcliente,'where deleted_at is NULL ','per_nombrecorto','','','si','no','no');?>
						</div>
					</div>

				</div>
				<div class="col-md-6">
				<div class="form-group">
					<label class="col-sm-2 control-label txtleft">Usuario</label>
					<div class="col-sm-12">
					<input type="text"  name="usuario" class="form-control">
					</div>
					<div class="col-sm-1 txtcolor-azulmenu padtop7 oculto" id="validandouser"><i class="fa fa-cog fa-lg fa-spin fa-fw"></i></div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label txtleft">Clave</label>
					<div class="col-sm-12">
					<input type="password"  name="clave" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label txtleft">Foto</label>
					<div class="col-sm-12">
					<input type="file" class='form-control' name="foto" >
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label txtleft">BBDD</label>
					<div class="col-sm-12">
					<input type="text"  name="bbdd" class="form-control">
					</div>
				</div>
				</div>
			</div>
			
			<div class="form-group">
			<div class="col-sm-offset-2 col-sm-6">
			<button type="submit" class=" btn btn-success btn-rounded">Registrar nuevo usuario</button>
			</div>
			</div>
		</form>
	</div>
</div>

</div><!-- fin formulario para agregar usuario -->
<?php 
}
?>
</div>
</section>
<script>


$("input[name='nombre']").on("focus",function(){
$("#iconnombre").removeClass("iconerror").addClass("iconlogin").html("<i class='fa fa-user' aria-hidden='true'></i>");
});
$("input[name='usuario']").on("focus",function(){
$("#iconusuario").removeClass("iconerror").addClass("iconlogin").html("<i class='fa fa-user' aria-hidden='true'></i>");
});
$("input[name='clave']").on("focus",function(){
$("#iconclave").removeClass("iconerror").addClass("iconlogin").html("<i class='fa fa-lock' aria-hidden='true'></i>");
});
$("input[name='correo']").on("focus",function(){
$("#iconcorreo").removeClass("iconerror").addClass("iconlogin").html("<i class='fa fa-envelope-o' aria-hidden='true'></i>");
});





function validanuevousuario(){
validacion="";	
nombre=$("input[name='nombre']").val();
if(nombre == ""){// valido nombre
$("#iconnombre").removeClass("iconlogin").addClass("iconerror").html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>");
return false;
}

if($("input[name='usuario']").val() == ""){// valido usuario
$("#iconusuario").removeClass("iconlogin").addClass("iconerror").html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>");
return false;
}
$("#validandouser").show();
$.ajax({
async: false,
url : 'operaciones.php',
data : { operacion : "validaruser",usuario:''+$("input[name='usuario']").val(),retornar:'no'},
type : 'GET',
dataType : 'text',
success : function(respuesta) {
$("#validandouser").hide();	
validacion=respuesta;
},
error : function(xhr, status) {
alert('error al ejecutar');
},
});

if(validacion == "existe"){
$("input[name='usuario']").val("");
$("input[name='usuario']").focus();
$("#iconusuario").removeClass("iconlogin").addClass("iconerror").html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>");	
return false;
}else{
$("#iconusuario").removeClass("iconerror").addClass("iconlogin").html("<i class='fa fa-user' aria-hidden='true'></i>");
}

if($("input[name='clave']").val() == ""){// valido clave
$("#iconclave").removeClass("iconlogin").addClass("iconerror").html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>");
return false;
}

correo = $("input[name='correo']").val().trim();// correo
if(!validarcorreo(correo)){// valido correo
$("#iconcorreo").removeClass("iconlogin").addClass("iconerror").html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>");
return false;
}
return true;	
}
<?php
$sqlf="select * from permisosusuarios where idusuario='".$idusuario."'";
$resf=$link->query($sqlf);
while($fila=mysqli_fetch_array($resf)) {
$id=$fila["idmodulo"];
echo"$('#check".$id."').prop('checked', true);";
}
?>

function eliusuario(id,usuario){
info="Realmente desea eliminar este usuario : <b>"+usuario+"</b>";
$("#musuarios .modal-dialog").css({'width':'30%'});
$("#musuarios .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#musuarios .modal-title").html("Eliminar Usuario");
$("#musuarios .modal-body").html(info);
// $("#vehiculo .modal-footer").css({display:"none"})
$("#musuarios .modal-footer").html("<button type='button' class='btn btn-danger pull-left btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' class='btn btn-success btn-rounded' onclick='borrarUsuario(\""+id+"\")'>Confirmar</button>")
$("#musuarios").modal("toggle"); 
}


function borrarUsuario(id){
	var randomNo = Math.floor(Math.random()*9999999);
	$.get("operaciones.php", {numero:''+randomNo+'',operacion:'eliminarusuario',idusuario:''+id+'',retornar:'no'},function(data){
		if(data!=='' && data!==null){
			data = $.parseJSON(data);
			if(data.status==='OK'){
				$("#usuario"+id+"").remove();
			}
		}
		$("#musuarios").modal("hide");
	});	
}

function asignarpermiso(a,b){
console.log(a);
console.log(b);
tipo=0;
check=$("#check"+a+"").val();
if($("#check"+a+"").is(':checked')){tipo=1;}
console.log(tipo);
var randomNo = Math.floor(Math.random()*9999999);
$.get("operaciones.php", {numero:''+randomNo+'',operacion:'permisos',idpermiso:''+a+'',accion:''+tipo+'',idusuario:''+b+'',idmodulo:''+a+'',retornar:'no'}
,function(data){
console.log(data);
});
	
}

function back(ocultar,mostrar){
$("#"+ocultar+"").hide();
$("#"+mostrar+"").show();
}

/**************************
funciones actualizar pass
**************************/
$("input[name='nuevapass']").on("focus",function(){
$(".errorpass").hide();
$("#lbl_errorPass").hide();	
});
$("input[name='nuevapass1']").on("focus",function(){
$(".errorpass").hide();
$("#lbl_errorPass").hide();	
});

function actualizapass(){
$("#formeditarusuario").hide();
$("#cambiarclave").show();
}
function volveralform(){
$("#cambiarclave").hide();
$("#formeditarusuario").show();
$(".errorpass").hide();
$("#lbl_errorPass").hide();	
}

function e_actualizarpass(){
if($("input[name='nuevapass']").val().length > 0 && $("input[name='nuevapass1']").val().length > 0 ){
if($("input[name='nuevapass']").val() == $("input[name='nuevapass1']").val()){
user=$("input[name='idusuario']").val();
$("#recuperando").show();
$.get('operaciones.php',{bncnr:''+Math.floor(Math.random()*9999999)+'',operacion:'changePassword',id:''+user+'',nueva:''+$("input[name='nuevapass']").val()+'',retornar:'no'}, function(data){
$("#recuperando").hide();
$("#contentRP").hide();
$("#exitoRP").show();
});	
}else{
$(".errorpass").show();
$("#lbl_errorPass").show();
}
}else{
$(".errorpass").show();
$("#lbl_errorPass").show();
}

}


</script>
