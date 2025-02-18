<?php
$sql2        = "SELECT * FROM sensores where sen_estado=1";
$res2        = $link->query($sql2);
$sensores    = array();
$thsensores  = '';
while($fila2 = mysqli_fetch_array($res2)){
    $sensores[] = array(
        'id'     =>$fila2['sen_id'],
        'nombre' =>$fila2['sen_nombre'],
        'fecha'  =>$fila2['sen_create_at'],
        'estado' =>$fila2['sen_estado'],
    );
    $thsensores .= '<th scope="col">'.ucfirst(strtolower($fila2['sen_nombre'])).'</th>';
}
?>
<!-- modal -->
<div class="modal" id="mlistpro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- fin modal -->

<div class="content">
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills" id="myTab">
                <li class="nav-item"><a onclick="getTabProductos()" id="navguia" class="nav-link active" href="#listaproductos" data-toggle="tab">Listado de productos</a></li>
                <li class="nav-item"><a onclick="getKitGps()" id="navlistguia" class="nav-link" href="#kitsgps" data-toggle="tab">Kits de GPS</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="active tab-pane" id="listaproductos">
                    <div class="row top20" id="listadoproductos">
                        <div class="col-md-12" id="tblistadopro">
                            <div class="box box-inverse box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Listado de Productos</h3>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="top20 table table-bordered table-striped table-sm" id="tab_productos">
                                        <thead class="thead-dark">
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Familia</th>
                                            <th>Subfamilia</th>
                                            <th>Marca</th>
                                            <th>Stock Mínimo</th>
                                            <th>Stock</th>
                                            <th>Valor</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </thead>
                                        <tbody >
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- form para mostrar inventario de producto  -->
                        <div class="col-md-8 oculto" id="fdetalleinventario">
                            <div class="box box-inverse box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Inventario de Producto</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" onclick="cancelarDI();">
                                            <i class="fa fa-lg fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- editar producto -->
                    <div class="row top20 oculto" id="editarproducto">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-inverse box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Editar Producto</h3>
                                        </div>
                                        <div class="box-body">
                                            <form action="operaciones.php" method="post" class="form-horizontal top20" id="fnpro">
                                                <input type="hidden" name="operacion" value="editarproducto"/>
                                                <input type="hidden" name="idpro" />
                                                <input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label txtleft">Código Interno</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="codigo" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label txtleft">Tiene Serie</label>
                                                    <div class="col-sm-2">
                                                        <input type="checkbox" id="serie" name="serie" value=1>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label txtleft">Familia</label>
                                                    <div class="col-sm-6">
                                                        <?= htmlselect('familia','familia','familias','fam_id','fam_nombre','','','','fam_nombre','getSubfamilias()','','si','no','no');?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label txtleft">Subfamilia</label>
                                                    <div class="col-sm-6">
                                                        <?= htmlselect('subfamilia','subfamilia','subfamilias','sfam_id','sfam_nombre','','','','sfam_nombre','','','si','no','no');?>
                                                            
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label txtleft">Marca</label>
                                                    <div class="col-sm-6">
                                                        <?= htmlselect('marca','marca','marcas','mar_id','mar_nombre','','','where mar_veh = 0','mar_nombre','','','si','no','no');?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label txtleft">Producto</label>
                                                    <div class="col-sm-6">
                                                        <textarea name="nombre" class="form-control rznone" rows=3 ></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label txtleft">Stock Mínimo</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="sminimo" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-offset-3 col-sm-6">
                                                        <button type="submit" class="btn btn-success btn-rounded">      Editar Producto
                                                        </button>
                                                        &nbsp;&nbsp;
                                                        <button type="button" class="btn btn-danger btn-rounded" onclick="cancelarEPRO();">
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
                    </div><!-- fin formulario de edicion de producto -->
                </div>
                <div class="tab-pane" id="kitsgps">
                    <table class="table table-bordered table-sm" id="tbl_kits">
                        <thead class="thead-dark">
                            <tr>
                            <th scope="col">N°</th>
                            <th scope="col" >GPS</th>
                            <th scope="col" nowrap>Serie GPS</th>
                            <th scope="col" nowrap>SIM</th>
                            <th scope="col" nowrap>Serie SIM</th>
                            <th scope="col" nowrap>Bodega</th>
                            <th scope="col" nowrap></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        getTabProductos();
    });

    window.productos;
    function getTabProductos(){
        if($.fn.DataTable.isDataTable('#tab_productos')) {
            $('#tab_productos').DataTable().destroy();
        }
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getTabProductos',retornar:'no'},function(data){
            console.log(data);
            datos     = $.parseJSON(data);
            productos = datos;
            filas     = "";
            x         = 0;
            $.each(datos, function(index,valor){
                x++;
                smin      = parseInt(valor.sminimo);
                sminalert = (smin / 2) + smin;
                stock     = parseInt(valor.stock);
                let style = 'height: 35px;width: 35px;';
                if(stock > sminalert){
                    color = "bg-success";	
                }else if(stock >= smin && stock <= sminalert){
                    color = "bg-warning";	
                }else{
                    color = "bg-danger";		
                }

                if(valor.marca =="" || valor.marca ==null){
                    marca="--";
                }else{
                    marca = valor.marca;
                }
                if(valor.subfamilia =="" || valor.subfamilia ==null){
                    subfamilia="--";
                }else{
                    subfamilia = valor.subfamilia;
                }

                filas += "<tr><td>"+x+"</td><td>"+valor.nombre+"</td><td>"+valor.familia+"</td><td>"+subfamilia+"</td><td>"+marca+"</td><td>"+valor.sminimo+"</td><td class='nstock' align='center'><span >"+valor.cantidad+"</span></td><td class='nvalor'><span class='label label-default btn-rounded pointer' onclick='modificarValor(\""+index+"\");'>$"+enpesos(valor.precio)+"</span></td><td class='text-center' width=50><span class='pointer btn btn-sm btn-info btn-circle' onclick='inventarioProducto("+index+","+valor.idpro+")'><i class='fa fa-cubes' aria-hidden='true'></i></span></td><td class='text-center' width=50><span class='pointer btn btn-sm btn-warning btn-circle' onclick='editarProducto(\""+index+"\")'><i class='fa fa-edit' aria-hidden='true'></i></span></td><td class='text-center' width=50><span class='pointer btn btn-sm btn-danger btn-circle' onclick='quitarProducto(\""+index+"\")'><i class='fa fa-trash'></i></span></td></tr>";
            });

            $("#tab_productos tbody").html(filas);
            $('#tab_productos').DataTable({
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

    function setestado (index,valor,idserie){
        env      = {'index':index,'valor':valor,'idserie':idserie};
        var send = JSON.stringify(env);
        $.ajax({
            url     : 'operaciones.php',
            data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'updateestado',retornar:'no',envio:send},
            type    : 'post',
            dataType: 'json',
            beforeSend: function(respuesta) {

            },error   : function(respuesta) {
                            console.log(respuesta);
            },success : function(respuesta) {
                if(respuesta.logo=='success'){
                    toastr.success(respuesta.mensaje);
                }else{
                    toastr.error(respuesta.mensaje);
                }
            }
        });
    }

    function inventarioProducto(index,idpro){
 
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'newgetInventarioxProducto',id:idpro,retornar:'no'},function(data){
            console.log(data);
            inv      = $.parseJSON(data);
            var bode = '';
            var tecn = '';
            var cabr = '';
            $.each(inv, function(i, item){
                var icono = '';
                if(item.ser_condicion==1){
                    icono = "<select name='estadopro' class='form-control' id='select"+i+"' onchange='setestado("+i+",this.value,"+item.ser_id+")'><option value='99'>SELECCIONAR</option><option value='1' selected>BUENO</option><option value='0'>MALO</option></select>";
                }else if(item.ser_condicion==0){
                    icono = "<select name='estadopro' class='form-control' id='select"+i+"' onchange='setestado("+i+",this.value,"+item.ser_id+")'><option value='99'>SELECCIONAR</option><option value='1'>BUENO</option><option value='0' selected>MALO</option></select>";
                }else{
                    icono = "<select name='estadopro' class='form-control' id='select"+i+"'><option value='99' selected>SELECCIONAR</option><option value='1'>BUENO</option><option value='0'>MALO</option></select>";
                }

                if(i==0){
                     cabr = "<div class='col-md-12'>Distribución de inventario de producto : <b>"+item.pro_nombre+"</b><hr></div><div class='col-md-12' id='paratotalesbodegas'></div>";

                     if(item.cargo==26){
                         bode += "<tbody><tr><td>1</td><td>Bodega Principal</td><td>"+item.ser_codigo+"</td><td>"+icono+"</td></tr>";
                     }else{
                        tecn += "<tbody><tr><td>1</td><td>"+item.bodega+"</td><td>"+item.ser_codigo+"</td><td>"+icono+"</td></tr>";
                     }                     
                }else{

                    if(item.cargo==26){
                        bode += "<tr><td>1</td><td>Bodega Principal</td><td>"+item.ser_codigo+"</td><td>"+icono+"</td></tr>";
                    }else{
                        tecn += "<tr><td>1</td><td>"+item.bodega+"</td><td>"+item.ser_codigo+"</td><td>"+icono+"</td></tr>";
                    }
                }
            });
            
            if(bode==''){
                bode = '<tr><td colspan="4" align="center">No hay Datos</td></tr>';
            }

            if(tecn==''){
                tecn  = '<tr><td colspan="4" align="center">No hay Datos</td></tr>';
            }
            var bodef = "<div class='col-md-12'><table class='table table-sm table-bordered table-striped'><thead class='thead-dark'><th>Cantidad</th><th>Bodega</th><th>Serie</th><th>Estado</th></thead>"+bode+"</tbody></table></div>";
            var tecnf = "<div class='col-md-12'><table class='table table-sm table-bordered table-striped'><thead class='thead-dark'><th>Cantidad</th><th>Bodega</th><th>Serie</th><th>Estado</th></thead>"+tecn+"</tbody></table></div>";
            foot      = "<table><tr><td>Total Series: "+inv.length+"</td></tr></table>";
            $("#tblistadopro").removeClass("col-md-12").addClass("col-md-4");
            $("#fdetalleinventario .box-body").html(cabr+bodef+tecnf+foot);
            $("#fdetalleinventario").show();
            $('html, body').animate( { scrollTop : 0 }, 400 );
        });
    }

    function cancelarDI(){
    $("#fdetalleinventario").hide();	
    $("#tblistadopro").removeClass("col-md-4").addClass("col-md-12");
    }


    function editarProducto(index){
    producto = productos[index];
    $("input[name='idpro']").val(producto["idpro"]);
    $("input[name='codigo']").val(producto["codigo"]);
    if(parseInt(producto["proserie"])==1){
    $("#serie").attr("checked",true);
    }else{
    $("#serie").attr("checked",false);	
    }
    $("#familia").val(producto["idfam"]);
    $("#subfamilia").val(producto["idsfam"]);
    $("#marca").val(producto["idmar"]);
    $("textarea[name='nombre']").val(producto["nombre"]);
    $("input[name='sminimo']").val(producto["sminimo"]);
    $("#listadoproductos").hide();
    $("#editarproducto").show();
    }
    function cancelarEPRO(){
    $("#editarproducto").hide();
    $("#listadoproductos").show();
    }
    function getSubfamilias(){
    idfam=$("#familia option:selected").val();
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getSubfamilias',familia:''+idfam+'',retornar:'no'},function(data){
    $("#subfamilia").html(data);
    });
    }

    function quitarProducto(index){
    producto = productos[index];
    pro="<div class='row'><div class='col-sm-12'>¿ Realmente deseas eliminar este producto : <b>"+producto["nombre"]+"</b> ?</div></div>";
    $("#mlistpro .modal-header").removeClass("header-verde").addClass("header-rojo");
    $("#mlistpro .modal-title").html("Eliminar Producto");
    $("#mlistpro .modal-body").html(pro);
    $("#mlistpro .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' onclick='eliminarProducto(\""+producto["idpro"]+"\")'>Confirmar</button>")
    $("#mlistpro").modal("toggle");	
    }
    function eliminarProducto(idpro){
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarproducto',id:''+idpro+'',retornar:'no'},function(data){
    $("#mlistpro").modal("hide");
    location.reload();
    });	
    }

    function modificarStock(index){
    producto = productos[index];
    if(parseInt(producto["proserie"])==0){
    info="";
    btncod="";
    }else{
    info="<div class='form-group'><label class='col-sm-3 control-label txtleft text-yellow'>Información</label><div class='col-sm-7'><textarea name='infopro' class='form-control rznone text-yellow' rows=4 disabled>Este producto tiene serie, según la cantidad será necesario agregar el numero de serie de cada producto.</textarea></div></div></div>";
    btncod="<button type='button' class='btn btn-info btn-rounded' onclick='agregarCodigos()'>Registrar Códigos</button>";
    }
    form="<div class='row'><div class='col-sm-12'><form class='form-horizontal top20'><input id='proserie' value="+producto["proserie"]+" type='hidden'><div class='form-group'><label class='col-sm-3 control-label txtleft'>Producto</label><div class='col-sm-7'><input type='text' name='producto' class='form-control' value='"+producto["nombre"]+"' disabled></div></div>"+info+"<div class='form-group'><label class='col-sm-3 control-label txtleft'>Stock Actual</label><div class='col-sm-2'><input type='text' name='nuevostock' class='form-control' value="+producto["stock"]+"></div><div class='col-sm-2'>"+btncod+"</div></div><div class='form-group oculto' id='agregarseries'><div class='col-sm-7 col-sm-offset-3 top20'><table class='table table-bordered table-striped'><thead><th>#</th><th>Codigo</th></thead><tbody></tbody></table></div></div><div class='form-group'><div class='col-sm-12 top20'><label class='control-label txtleft'>Justificación de la actualización</label><br><textarea name='justificacion' class='form-control rznone' rows=3 ></textarea></div></div></form></div></div>";
    $("#mlistpro .modal-dialog").css({"width":"50%"});
    $("#mlistpro .modal-header").removeClass("header-rojo").addClass("header-inverse");
    $("#mlistpro .modal-title").html("Actualizar Stock");
    $("#mlistpro .modal-body").html(form);
    $("#mlistpro .modal-footer").html("<button type='button' class='btn btn-success btn-rounded' onclick='actualizaStock(\""+producto["idpro"]+"\")'>Confirmar</button>");
    $("#mlistpro").modal("toggle");		
    }

    function agregarCodigos(){
    cantidad = parseInt($("input[name='nuevostock']").val());
    if(cantidad > 0){
    fcod="";
    for(x=1; x<=cantidad; x++){
    fcod+="<tr><td>"+x+"</td><td><input type='text' name='codigo[]' class='form-control'></td></tr>";
    }
    $("#agregarseries table tbody").html(fcod);
    $("#agregarseries").show();
    }else{
    alert("La cantidad debe ser mayor que 0");
    $("input[name='nuevostock']").focus();
    return;
    }

    }
    function actualizaStock(id){
    datastock={};
    datastock["idpro"]=id;
    datastock["serie"]=$("#proserie").val();
    datastock["usuario"]=$("#usuarioidjs").val();
    datastock["stock"]=$("input[name='nuevostock']").val();
    datastock["comentario"]=$("textarea[name='justificacion']").val();
    if(parseInt($("#proserie").val()) == 1){
    codigodepro=[];
    $("input[name='codigo[]']").each(function(e){
    codigodepro.push({nombre:$(this).val()});
    });	
    datastock["codigos"]=codigodepro;
    }

    json = JSON.stringify(datastock);
    console.log(json);
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'actualizarStock',datos:json,retornar:'no'},function(data){
    //console.log(data);
    location.reload();	
    });



    /*



    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'actualizarStock',idpro:''+id+'',usuario:''+iduser+'',stock:''+numero+'',comentario:''+detalle+'',retornar:'no'},function(data){
    $("#mlistpro").modal("hide");
    location.reload();
    });	
    */
    }

    function modificarValor(index){
    producto = productos[index];	
    form="<div class='row'><div class='col-sm-12'><form class='form-horizontal top20'><div class='form-group'><label class='col-sm-3 control-label txtleft'>Valor Actual</label><div class='col-sm-4'><input type='text' name='nuevovalor' class='form-control' value="+producto["precio"]+"></div></div><div class='form-group'><div class='col-sm-12'><label class='control-label txtleft'>Justificación de la actualización</label><br><textarea name='justificacion' class='form-control rznone' rows=3 ></textarea></div></div></form></div></div>";
    $("#mlistpro .modal-header").removeClass("header-rojo").addClass("header-inverse");
    $("#mlistpro .modal-title").html("Actualizar Valor");
    $("#mlistpro .modal-body").html(form);
    $("#mlistpro .modal-footer").html("<button type='button' class='btn btn-success btn-rounded' onclick='actualizaValor(\""+producto["idpro"]+"\")'>Confirmar</button>");
    $("#mlistpro").modal("toggle");		
    }

    function actualizaValor(id){
    iduser=$("#usuarioidjs").val();
    valor=$("input[name='nuevovalor']").val();
    detalle=$("textarea[name='justificacion']").val();
    $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'actualizarPrecio',idpro:''+id+'',usuario:''+iduser+'',precio:''+valor+'',comentario:''+detalle+'',retornar:'no'},function(data){
    $("#mlistpro").modal("hide");
    location.reload();
    });	
    }




    ////////////////////////// FUNCIONES DE KIST GPS //////////////////////////////////
    let listAsoc = [];
    function getKitGps(){
    //tbl_kits
        let cantSensores = <?=count($sensores)?>;
        let _Sensores = [<?php for($i=0; $i<count($sensores); $i++){ echo '{"id":"'.$sensores[$i]["id"].'", "nombre":"'.$sensores[$i]["nombre"].'", "fecha":"'.$sensores[$i]["fecha"].'", "estado":"'.$sensores[$i]["estado"].'"},'; } ?>];
        if($.fn.DataTable.isDataTable('#tbl_kits')){
            $('#tbl_kits').DataTable().destroy();
        }
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'listarAsociacion',retornar:'no'},function(data){
            if(data!='' && data!=null){
                data = $.parseJSON(data);
                if(data.data.length>0){
                    listAsoc = data.data;
                    let fila = '';
                    $('.tool').tooltip('dispose')
                    $('#tbl_kits tbody').html('');
                    $.each(data.data,function(i,item){
                        let num = $('#tbl_kits tbody tr').length;

                        let bodega = '';
                        if(parseInt(item.idbodega)==26){
                            bodega = 'Bodega principal';
                        }
                        else{
                            bodega = 'Bodega técnico';
                        }

                        let btn = '<span onclick="verItem('+i+','+item.id+')" class="btn btn-sm btn-circle btn-primary tool" data-toggle="tooltip" data-placement="top" title="Ver detalle asociación"><i class="fa fa-eye"></i></span>';
                        //btn += ' <span onclick="editarItem('+i+','+item.id+')" class="btn btn-sm btn-circle btn-warning tool" data-toggle="tooltip" data-placement="top" title="Editar asociación"><i class="fa fa-edit"></i></span>';
                        //btn += ' <span onclick="borrarItem('+i+','+item.id+')" class="btn btn-sm btn-circle btn-danger tool" data-toggle="tooltip" data-placement="top" title="borrar asociación"><i class="fa fa-trash"></i></span>';

                        let sensores = '';
                        // $.each(item.sensores,function(index, sensor){
                        //     sensores += '<td id="sensor_'+sensor.id+'_'+item.id+'" nowrap style="vertical-align:middle;">'+(sensor.estado==null?'<span style="cursor:pointer;" onclick="cambiarEstado('+sensor.id+','+sensor.estado+','+item.id+')" class="badge badge-danger">Apagado</span>':sensor.estado==''?'<span style="cursor:pointer;" onclick="cambiarEstado('+sensor.id+','+sensor.estado+','+item.id+')" class="badge badge-danger">Apagado</span>':parseInt(sensor.estado)==1?'<span style="cursor:pointer;" onclick="cambiarEstado('+sensor.id+','+sensor.estado+','+item.id+')" class="badge badge-success">Encendido</span>':parseInt(sensor.estado)==0?'<span style="cursor:pointer;" onclick="cambiarEstado('+sensor.id+','+sensor.estado+','+item.id+')" class="badge badge-danger">Apagado</span>':'<span style="cursor:pointer;" onclick="cambiarEstado('+sensor.id+','+sensor.estado+','+item.id+')" class="badge badge-danger">Apagado</span>')+'</td>';
                        // });
                        sensores += '<td nowrap style="vertical-align:middle;">'+btn+'</td>';

                        fila += '<tr id="fila_'+i+'"><td>'+(i+1)+'</td><td>'+item.gps+'</td><td>'+item.seriegps+'</td><td>'+item.accesorio+'</td><td>'+item.serieaccesorio+'</td><td>'+bodega+'</td>'+sensores+'</tr>';
                    });
                    $('#tbl_kits tbody').append(fila);
                    $('#tbl_kits').DataTable({
                        "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                        "paging": true,
                        "lengthChange": true,
                        "lengthMenu": [[20,-1], [20,"Todos"]],
                        "pageLength":20,
                        columnDefs:[{
                            targets: "_all",
                            sortable: false
                        }],
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false
                    });

                    $('.tool').tooltip()
                }
                else{
                    $('#tbl_kits').DataTable({
                        "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                        "paging": true,
                        "lengthChange": true,
                        "lengthMenu": [[20,-1], [20,"Todos"]],
                        "pageLength":20,
                        columnDefs:[{
                            targets: "_all",
                            sortable: false
                        }],
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false
                    });
                }
            }
            else{
                $('#tbl_kits').DataTable({
                    "language":{ url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json'},
                    "paging": true,
                    "lengthChange": true,
                    "lengthMenu": [[20,-1], [20,"Todos"]],
                    "pageLength":20,
                    columnDefs:[{
                        targets: "_all",
                        sortable: false
                    }],
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false
                });
            }
        });
    }

    function verItem(index, id){
        let table = '<table class="table table-sm table-bordered">';
        table += '<thead class="thead-dark">';
        table += '<th scope="col">Tipo</th>';
        table += '<th scope="col">Estado</th>';
        table += '</thead>';
        table += '<tbody>';
        table += '<tr>';
        table += '<td scope="row">Odometro</td><td></td>';
        table += '</tr>';
        table += '<tr>';
        table += '<td scope="row">Litros</td><td></td>';
        table += '</tr>';
        table += '<tr>';
        table += '<td scope="row">Velocidad</td><td></td>';
        table += '</tr>';
        table += '<tr>';
        table += '<td scope="row">Pedal Aceleración</td><td></td>';
        table += '</tr>';
        table += '<tr>';
        table += '<td scope="row">RPM</td><td></td>';
        table += '</tr>';
        table += '<tr>';
        table += '<td scope="row">Nivel Estanque</td><td></td>';
        table += '</tr>';
        table += '<tr>';
        table += '<td scope="row">T° Motor</td><td></td>';
        table += '</tr>';
        table += '<tr>';
        table += '<td scope="row">Torque</td><td></td>';
        table += '</tr>';
        table += '<tr>';
        table += '<td scope="row">AD Blue</td><td></td>';
        table += '</tr>';
        table += '<tr>';
        table += '<td scope="row">Peso Eje</td><td></td>';
        table += '</tr>';
        table += '</tbody>';
        table += '</table>';
        $('#mlistpro .modal-header').css({'background-color':'#338AFF','color':'white'});
        $('#mlistpro .modal-title').text('Info CAN');
        $('#mlistpro .modal-body').html(table);
        $('#mlistpro .modal-footer').html("<button type='button' class='btn btn-primary btn-rounded' data-dismiss='modal'>Aceptar</button>");
        $('#mlistpro').modal('show');
    }

</script>