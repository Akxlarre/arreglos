<!-- modal -->
<div class="modal" id="mfam">
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
<section class="content">
    <div class="row top20">
        <div class="col-md-8" id="nfamilia">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-inverse box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Nuevo Sensor</h3>
                        </div>
                        <div class="box-body">
                            <form action="operaciones.php" method="post" class="form-horizontal" id="fnuevafam" >
                                <input type="hidden" name="operacion" value="nuevoSensor"/>
                                <input type="hidden" name="idsen"/>
                                <input type="hidden" name="retornar" value="index.php?menu=<?=$_REQUEST["menu"];?>&idmenu=<?=$_REQUEST["idmenu"];?>"/>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nombre">Sensor</label>
                                            <input type="text" class="form-control" name="nombre" id="nombre" aria-describedby="nombreHelp" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="estado1">Estado 1 (Valor 1)</label>
                                            <input type="text" class="form-control" name="estado1" id="estado1" aria-describedby="estado1Help">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="estado2">Estado 2 (Valor 0)</label>
                                            <input type="text" class="form-control" name="estado2" id="estado2" aria-describedby="estado2Help">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" style="margin-top: 32px;" class="btn btn-success btn-rounded" id="btnunidad">Registrar Sensor</button>&nbsp;&nbsp;<button type="button" style="margin-top: 32px;" class="btn btn-danger oculto btn-rounded" id="btn_CFAM" onclick="CancelarEFAM();"><i class="fa fa-times" aria-hidden="true"></i></button>
                                    </div>
                                </div>

                            </form>

                            <table class="table table-bordered table-striped table-sm">
                                <thead class="thead-dark">
                                    <th>N°</th>
                                    <th>Sensor</th>
                                    <th>Estado 1</th>
                                    <th>Estado 2</th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "select * from sensores where sen_estado = 1";
                                        $res = $link->query($sql);
                                        $x   = 0;
                                        while($fila=mysqli_fetch_array($res)){
                                            $x++;
                                    ?>
                                            <tr id="fam<?=$fila["sen_id"];?>">
                                            <td><?=$x;?></td>
                                            <td><?=$fila["sen_nombre"];?></td>
                                            <td><?=$fila["sen_estado1"];?></td>
                                            <td><?=$fila["sen_estado2"];?></td>
                                            <td class="text-center" width="50">
                                                <button class="btn btn-sm btn-warning btn-circle" onclick="editarFAM('<?=$fila['sen_id'];?>','<?=$fila['sen_nombre'];?>','<?=$fila['sen_estado1'];?>','<?=$fila['sen_estado2'];?>')">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                            <td class="text-center" width="50">
                                                <button class="btn btn-sm btn-danger btn-circle" onclick="quitarFAM('<?=$fila['sen_id'];?>','<?=$fila['sen_nombre'];?>')">
                                                    <i class="fa fa-trash"></i>
                                                </button></td>
                                            </tr>
                                    <?php 
                                        } 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 

<script>
    function quitarFAM(id,nombre){
        $("#mfam .modal-dialog").css({'width':'30%'});
        $("#mfam .modal-header").removeClass("header-verde").addClass("header-rojo");
        $("#mfam .modal-title").html("Eliminar Sensor");
        $("#mfam .modal-body").html("Realmente desea eliminar este Sensor : <b>"+nombre+"</b>");
        $("#mfam .modal-footer").html("<button type='button' class='btn btn-danger btn-rounded' data-dismiss='modal'>Cancelar</button><button type='button' onclick='eliminarFAM(\""+id+"\");' class='btn btn-success btn-rounded'>Eliminar</button>");
        $("#mfam").modal("toggle");
    }

    function eliminarFAM(id){
        $.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'eliminarSensor',idsen:''+id+'',retornar:'no'},function(data){
            $("#fam"+id+"").remove();
            $("#mfam").modal("hide");
        });
    }

    function editarFAM(id,nombre,estado1, estado2){
        $("#fnuevafam").find("input[name='operacion']").val("editarSensor");
        $("#fnuevafam").find("input[name='idsen']").val(id);
        $("#fnuevafam").find("input[name='nombre']").val(nombre);
        $("#fnuevafam").find("input[name='estado1']").val(estado1);
        $("#fnuevafam").find("input[name='estado2']").val(estado2);
        $("#fnuevafam").find("button[type='submit']").removeClass("btn-success").addClass("btn-warning").html("Editar");
        $("#btn_CFAM").show();
    }

    function CancelarEFAM(){
        $("#fnuevafam").find("input[name='operacion']").val("nuevoSensor");
        $("#fnuevafam").find("input[name='idsen']").val("");
        $("#fnuevafam").find("input[name='nombre']").val("");
        $("#fnuevafam").find("button[type='submit']").removeClass("btn-warning").addClass("btn-success").html("Registrar Sensor");
        $("#btn_CFAM").hide();
    }
</script>

  