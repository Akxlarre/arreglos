<div class="modal" id="mper">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title"></h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

</div>
<div class="modal-body"></div>
<div class="modal-footer">
</div>
</div>
</div>
</div>

<div class="content">
    <div class="row top20" id="l_personal">
        <div class="col-md-12" id="tblpersonal">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Personal Registrado</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped table-sm" id="tb_personal">
                        <thead class="thead-dark">
                            <th>#</th>
                            <th nowrap>Apellido Paterno </th>
                            <th nowrap>Apellido Materno </th>
                            <th>Nombres</th>
                            <th>Celular</th>
                            <th>Correo</th>
                            <th nowrap style="display: none;">Domicilio</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Productos</th>
                            <th class="text-center"></th>
                            <th class="text-center"></th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- inventario de personal-->
        <div class="col-md-6 oculto" id="finventariopersonal">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Productos en Bodega</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" onclick="cancelarVista();">
                            <i class="fa fa-lg fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                </div>
            </div>
        </div>
    </div>
    <!-- form editar personal -->
    <div class="row col-md-12 oculto top20" id="f_editarpersonal">
        <div class="col-md-12">
            <div class="box box-inverse box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Editar Personal</h3>
                </div>
                <div class="box-body">
                    <form action="operaciones.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="operacion" value="editarpersonal">
                        <input type="hidden" name="idpersonal">
                        <input type="hidden" name="retornar" id="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>">

                        <div class="row">
                            <div class="col-md-3">
                                <label>Apellido Paterno</label>
                                <input type="text" name="apaterno" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Apellido Materno</label>
                                <input type="text" name="amaterno" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Nombres</label>
                                <input type="text" name="nombres" class="form-control">
                            </div>
                        </div>
                        <div class="row top10">
                            <div class="col-md-3">
                                <label>Celular</label>
                                <input type="text" name="celular" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>E-mail</label>
                                <input type="text" name="email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Domicilio</label>
                                <input type="text" name="domicilio" class="form-control ">
                            </div>
                        </div>
                        <div class="row top10">
                            <div class="col-md-3">
                                <label>Usuario</label>
                                <input type="text" name="usuario" class="form-control ">
                            </div>
                            <div class="col-md-3">
                                <label>Clave</label>
                                <input type="password" name="clave" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Región</label>
                                <? htmlselect('region','region','regiones','id','region|ordinal','','','','id','getComunas()','','si','no','no');?>
                            </div>
                            <div class="col-md-3">
                                <label>Comuna</label>
                                    <!-- <select name="comuna" id="comuna" class="form-control"></select> -->
                                <? htmlselect('comuna','comuna','comunas','comuna_id','comuna_nombre','','','','comuna_id','','','si','no','no');?>
                            </div>
                        </div>
                        <div class="row top10">
                            <div class="col-md-7">
                                <br>
                                <button type="submit" class="btn btn-success btn-rounded">Editar Personal</button>
                                     &nbsp;&nbsp;
                                <button type="button" class="btn btn-danger btn-rounded" onclick="cancelarEP()">    
                                     Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    })
$(function(){
    
getPersonal();

});
window.personal;
function getPersonal(){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getPersonal',retornar:'no'},function(data){
personal=$.parseJSON(data);
tbpersonal="";
x=0;
$.each(personal,function(index,valor){
x++;
per_id = valor.id;
estado=valor.estado;
if(estado == 1){estado ="<span class='text-green pointer' onclick='cambiarestadoPersonal(\""+valor.id+"\",\"0\")'><i class='fa fa-lg fa-toggle-on' aria-hidden='true'></i></span>"; retro="";}else{estado="<span class='text-muted pointer' onclick='cambiarestadoPersonal(\""+valor.id+"\",\"1\")'><i class='fa fa-lg fa-toggle-off' aria-hidden='true'></i></span>";retro="retro";}
tbpersonal+="<tr id='personal"+valor.id+"'>";
tbpersonal+="<td>"+x+"</td>";
tbpersonal+="<td>"+valor.apaterno+"</td>";
tbpersonal+="<td>"+valor.amaterno+"</td>";
tbpersonal+="<td>"+valor.nombres+"</td>";
tbpersonal+="<td>"+valor.celular+"</td>";
tbpersonal+="<td>"+valor.correo+"</td>";
tbpersonal+="<td style='display: none;'>"+valor.domicilio+"</td>";
tbpersonal+="<td class='text-center' id='estadoper"+valor.id+"'>"+estado+"</td>";
tbpersonal+="<td class='text-center'><span data-toggle='tooltip' data-placement='top' title='Inventario personal' class='btn btn-sm btn-info btn-circle' onclick='inventarioPersonal("+index+","+valor.id+")'><i class='fa fa-list-alt' aria-hidden='true'></i></span></td>";
tbpersonal+="<td class='text-center'><span data-toggle='tooltip' data-placement='top' title='Editar personal' class='btn btn-sm btn-warning btn-circle text-white' onclick='editarpersonal(\""+valor.id+"\")'><i class='fas fa-edit' aria-hidden='true'></i></span></td>";
tbpersonal+="<td class='text-center'><span data-toggle='tooltip' data-placement='top' title='Quitar personal' class='btn btn-sm btn-danger btn-circle' onclick='quitarpersonal(\""+index+"\")'><i class='fa fa-trash' aria-hidden='true'></i></span></td>";
tbpersonal+="</tr>";

// console.log(valor["paso1"]["apaterno"]+" "+estado+" "+foto);

});

$("#tb_personal tbody").html(tbpersonal);
$('#tb_personal').DataTable({
"language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
"paging": true,
"lengthChange": true,
"lengthMenu": [[20,-1], [20,"Todos"]],
"pageLength":20,
"searching": true,
"ordering": true,
"info": true,
"autoWidth": false
});
});
}

function inventarioPersonal(index,idpersonal){

    persona = personal[index];
    id      = persona.id;

    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'productoxTecnico',idtecnico:id,retornar:'no'},function(data){
        // console.log(data);
        datos       = $.parseJSON(data);
        var trcolor = '';
        var extra   = '';
        if(idpersonal==26){
            extra   = '<th>Courrier</th><th>Datos Courrier</th>';
        }else{
            extra   = '';
        }

        form        = "<div class='col-sm-10 table-responsive'><b>Listado de productos disponibles en bodega de  : "+persona.nombrecorto+"</b><hr><table class='table table-bordered table-striped' id='tbpxt'><thead><tr><th>Cantidad</th><th>Producto</th><th>Serie</th><th>N° Serie</th><th>Estado</th>"+extra+"<th>Observacion</th></tr></thead><tbody>";

        $.each(datos,function (ipxt,vpxt){
            switch(vpxt.estado){
                case 'BUENO':
                    trcolor = "";
                    estado  = "<td class='text-center'><span class='text-success'><i class='fa fa-check' aria-hidden='true'></i></span></td>";
                break;
                case 'MALO':
                    trcolor = "danger";
                    estado  = "<td class='text-center'><span class='text-danger'><i class='fa fa-times' aria-hidden='true'></i></span></td>";
                break;
                case 'NO REGISTRADO':
                    trcolor = "warning";
                    estado  = "<td class='text-center'><span class='text-warning'><i class='fa fa-exclamation' aria-hidden='true'></i></span></td>";
                break;
            }

            var otrotd = "";
            if(vpxt.tracking=='En Transito' && idpersonal==26){
                otrotd = "<td>"+vpxt.tracking+"</td><td><label>"+vpxt.fechatracking+"</label><br><label>"+vpxt.nomtracking+"</label><br><label>"+vpxt.codtracking+"</label><br><label>"+vpxt.rectracking+"</label></td>";
            }else{
                if(idpersonal==26){
                    otrotd = "<td>"+vpxt.tracking+"</td><td></td>";
                }else{
                    otrotd = "";
                }
                
            }

            if(vpxt.ser_instalado==0){
                form += "<tr class='"+trcolor+"'><td class='text-center'>"+vpxt.cantidad+"</td><td>"+vpxt.producto+"</td><td>"+vpxt.tieneserie+"</td><td>"+vpxt.serie+"</td>"+estado+otrotd+"</td><td class='text-center'>"+vpxt.observacion+"</td></tr>";  
            }
            
        });
        form += "</tbody></table></div></div>";

        $("#tblpersonal").removeClass("col-md-12").addClass("col-md-6");
        $("#finventariopersonal .box-body").html(form);
        $("#finventariopersonal").show();
        $('html, body').animate( { scrollTop : 0 }, 400 );
    });
}
function cancelarVista(){
$("#finventariopersonal").hide();	
$("#tblpersonal").removeClass("col-md-6").addClass("col-md-12");
}

function editarpersonal(id){
$.each(personal, function(index,valor){
if(valor.id==id){
$("input[name='idpersonal']").val(id);
$("input[name='apaterno']").val(valor.apaterno);
$("input[name='amaterno']").val(valor.amaterno);
$("input[name='nombres']").val(valor.nombres);
$("input[name='domicilio']").val(valor.domicilio);
$("input[name='celular']").val(valor.celular);
$("input[name='email']").val(valor.correo);
$("input[name='usuario']").val(valor.usuario);
$("input[name='clave']").val(valor.clave);
$("#l_personal").hide();
$("#f_editarpersonal").show();
$("#region").val(valor.region);
$("#comuna").val(valor.comuna);
}
});
}
function cancelarEP(){
// $("#f_editarpersonal").hide();
// $("#l_personal").show();
    let retorno = $('#retornar').val();
    location.href = retorno;
}

function cambiarestadoPersonal(personal,estado){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'cambiarestadoPersonal',id:personal,idestado:estado,retornar:'no'},function(data){
if(estado == 1){estadoper ="<span class='text-green pointer' onclick='cambiarestadoPersonal(\""+personal+"\",\"0\")'><i class='fa fa-lg fa-toggle-on' aria-hidden='true'></i></span>";$("#imgpersonal"+personal+"").removeClass("retro");}else{estadoper="<span class='text-muted pointer' onclick='cambiarestadoPersonal(\""+personal+"\",\"1\")'><i class='fa fa-lg fa-toggle-off' aria-hidden='true'></i></span>";$("#imgpersonal"+personal+"").addClass("retro");}
$("#estadoper"+personal+"").html(estadoper);
});	
}

function quitarpersonal(index){
persona = personal[index];
per="<div class='row'>";
per+="<div class='col-md-12'><table class='table table-bordered table-striped'><tr><td>Apellido Paterno</td><td>"+persona["apaterno"]+"</td></tr><tr><td>Apellido Materno</td><td>"+persona["amaterno"]+"</td></tr><tr><td>Nombre</td><td>"+persona["nombres"]+"</td></tr><tr><td>Celular</td><td>"+persona["celular"]+"</td></tr><tr><td>Correo</td><td>"+persona["correo"]+"</td></tr><tr><td>Domicilio</td><td>"+persona["domicilio"]+"</td></tr></table></div></div>";
$("#mper .modal-header").removeClass("header-verde").addClass("header-rojo");
$("#mper .modal-title").html("Eliminar Personal");
$("#mper .modal-body").html(per);
$("#mper .modal-footer").show().html("<button type='button' class='btn btn-danger btn-rounded' onclick='borrarPersonal(\""+persona["id"]+"\")'>Eliminar</button>")
$("#mper").modal("toggle");	
}

function borrarPersonal(id){
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarpersonal',idper:id,retornar:'no'},function(data){
location.reload();
});
}

function getComunas(){
idregion=$("#region option:selected").val();
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getComunas',region:''+idregion+'',retornar:'no'},function(data){
console.log(data);
$("#comuna").html(data);
});
}

</script>