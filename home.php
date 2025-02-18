<div class="content">

            <style type="text/css">
                .dashboard {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr); /* Dividir en 3 columnas */
                    grid-gap: 20px; /* Espacio entre los recuadros */
                    width: 95%;
                }

                .card {
                    width: 100%; /* Abarcar el 100% del ancho */
                }

                .expanded-card {
                    grid-column: span 2; /* Ampliar la tarjeta para ocupar 2 columnas en lugar de 1 */
                }

                .card-container {
                    display: flex;
                    justify-content: center;
                    overflow-x: hidden;
                }
            </style>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Ysabeau+Office&display=swap" rel="stylesheet"> 
            <div class="row table-responsive" id="divdashboard" style="max-height:90%; height: 90%;"> 
                <div class="col-md-12" style="text-align: center;">
                   <span style="font-family: 'Ysabeau Office', sans-serif; font-size: 30pt; text-align: center;">DASHBOARD</span>
                </div>
                <div class="col-md-12">
                    <div class="card-container">
                      <div class="dashboard" id="contsah">
                        <?php
                         for($i=1;$i<4;$i++){
                        ?>
                            <div class="card" style="display: none;" id="card<?php echo $i ?>">
                              <div id="chartMessage<?php echo $i ?>" style="text-align: center;">Sin Datos para mostrar</div>
                              <canvas id="myChart<?php echo $i ?>" style="position: relative;"></canvas>
                              <button class="expand-button" style="border: none; background: transparent;" onclick="toggleExpandChart('<?php echo $i ?>')">
                                  <i class="fas fa-expand" style="position: absolute; top: 10px; right: 10px;"></i>
                              </button>
                            </div>
                        <?php
                        }       
                        ?>
                      </div>
                    </div>
              </div>
            </div>

            <script type="text/javascript">

            var dataPerfil = <?php echo $_SESSION['perfil_new']; ?>;

              $(document).ready(async function() {

                  if(dataPerfil != 3){
                    for (var i = 1; i < 4; i++) {
                        $("#card"+i).show();
                        var data = await obtenergraficos(i)
                        opciones(i,data);
                    }
                  }
                  
              });

              async function obtenergraficos(opc){
                  var env   = {'opc':opc};
                  var send  = JSON.stringify(env);
                  const result = await $.ajax({
                      url     : 'operaciones.php',
                      data    : {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'cargadatos',retornar:'no',envio:send},
                      type    : 'post',
                      dataType: 'json',
                      beforeSend: function(res) {
                           $('#chartMessage'+opc).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
                           $('#chartMessage'+opc).show();                            
                      },error   : function(res) {
                          console.log(res);
                      },success : function(res) {
                          
                      }

                  });
                  return result
              }

              function toggleExpandChart(chartId) {
                  var card = document.getElementById('card' + chartId);
                  card.classList.toggle('expanded-card');
                  // También podrías realizar otras acciones adicionales si es necesario
              }


              function opciones (opc,res){
                if(opc==1){
                  if(res.labels.length>0){
                        $('#chartMessage'+opc).hide();
                        var labels = res.labels.map(obj => obj.mes);
                        var dataValues1 = res.labels.map(obj => obj.col1);
                        var dataValues2 = res.labels.map(obj => obj.col2);
                        var dataValues3 = res.labels.map(obj => obj.col3);
                        var dataValues4 = res.labels.map(obj => obj.col4);

                        // Datos del gráfico
                        var data = {
                        labels: labels,
                        datasets: [
                          {
                            label: 'Soporte',
                            data: dataValues1,
                            backgroundColor: 'rgba(230, 31, 18, 0.5)', // Color de las barras del dataset A
                            borderColor: 'rgba(230, 31, 18, 1)', // Color del borde de las barras del dataset A
                            borderWidth: 1 // Ancho del borde de las barras del dataset A
                          },
                          {
                            label: 'Instalacion',
                            data: dataValues2,
                            backgroundColor: 'rgba(236, 153, 47, 0.5)', // Color de las barras del dataset B
                            borderColor: 'rgba(236, 153, 47, 1)', // Color del borde de las barras del dataset B
                            borderWidth: 1 // Ancho del borde de las barras del dataset B
                          },
                          {
                            label: 'Desisntalacion',
                            data: dataValues3,
                            backgroundColor: 'rgba(127, 231, 87, 0.5)', // Color de las barras del dataset B
                            borderColor: 'rgba(127, 231, 87, 1)', // Color del borde de las barras del dataset B
                            borderWidth: 1 // Ancho del borde de las barras del dataset B
                          },
                          {
                            label: 'Demo',
                            data: dataValues4,
                            backgroundColor: 'rgba(107, 240, 223, 0.5)', // Color de las barras del dataset B
                            borderColor: 'rgba(107, 240, 223, 1)', // Color del borde de las barras del dataset B
                            borderWidth: 1 // Ancho del borde de las barras del dataset B
                          }
                        ]
                      };

                      var options = {
                        plugins: {
                          title: {
                            display: true, // Mostrar el título
                            text: 'Cantidad de trabajos por mes', // Texto del título
                            color: 'rgba(141, 139, 139, 1)',
                            font: {
                              size: 12 // Tamaño de fuente del título
                            }
                          }
                        },
                        scales: {
                          x: {
                            stacked: true,
                            grid: {
                              display: false
                            }
                          },
                          y: {
                            stacked: true,
                            grid: {
                              display: false
                            }
                          }
                        }
                      };

                      // Crear gráfico de barras apiladas
                      var ctx = document.getElementById('myChart'+opc).getContext('2d');
                      var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: data,
                        options: options
                      });
                    }else{
                      $('#chartMessage'+opc).html('<span>Sin Datos a mostrar</span>');
                      $('#chartMessage'+opc).show();
                    }
                }else if(opc==2){
                  if (res.labels.length > 0) {
                    $('#chartMessage' + opc).hide();

                      // Datos del gráfico
                      var labels = res.labels.map(obj => obj.mes);

                      var datasets = res.labels[0].lineas.map((linea, i) => {
                          return {
                              label: linea.tecnico,
                              data: res.labels.map(obj => obj.lineas[i].trabajos),
                              fill: false,
                              borderColor: getRandomColor(), // Puedes usar una función para obtener colores aleatorios
                              borderWidth: 2
                          };
                      });

                      var data = {
                          labels: labels,
                          datasets: datasets
                      };

                      // Configuración del gráfico
                      var options = {
                          tooltips: {
                              callbacks: {
                                  title: function () {
                                      return ''; // Dejar en blanco el título del tooltip
                                  },
                                  label: function (tooltipItem) {
                                      return tooltipItem.yLabel; // Mostrar solo el valor del gráfico
                                  }
                              }
                          },
                          scales: {
                              y: {
                                  beginAtZero: true,
                                  grid: {
                                      display: false
                                  }
                              },
                              x: {
                                  beginAtZero: true,
                                  grid: {
                                      display: false
                                  }
                              }
                          }
                      };

                      // Crear gráfico de líneas
                      var ctx = document.getElementById('myChart'+opc).getContext('2d');
                          if (Chart.getChart(ctx)) {
                              Chart.getChart(ctx).destroy();
                          }
                      var myChart = new Chart(ctx, {
                          type: 'line',
                          data: data,
                          options: options
                      });
                  }else {
                      $('#chartMessage' + opc).show();
                      $('#chartMessage' + opc).html('<span>Sin Datos a mostrar</span>');
                  }
                }else if(opc==3){
                  if(res.labels.length>0){
                        $('#chartMessage'+opc).hide();
                        var labels = res.labels.map(obj => obj.mes);
                        var dataValues1 = res.labels.map(obj => obj.interno);
                        var dataValues2 = res.labels.map(obj => obj.cliente);


                        var data = {
                        labels: labels,
                        datasets: [
                          {
                            label: 'Interno',
                            data: dataValues1,
                            backgroundColor: 'rgba(230, 31, 18, 0.5)', // Color de las barras del dataset A
                            borderColor: 'rgba(230, 31, 18, 1)', // Color del borde de las barras del dataset A
                            borderWidth: 1 // Ancho del borde de las barras del dataset A
                          },
                          {
                            label: 'Cliente',
                            data: dataValues2,
                            backgroundColor: 'rgba(236, 153, 47, 0.5)', // Color de las barras del dataset B
                            borderColor: 'rgba(236, 153, 47, 1)', // Color del borde de las barras del dataset B
                            borderWidth: 1 // Ancho del borde de las barras del dataset B
                          }
                        ]
                      };

                      var options = {
                        plugins: {
                          title: {
                            display: true, // Mostrar el título
                            text: 'Centro Costo Mensual', // Texto del título
                            color: 'rgba(141, 139, 139, 1)',
                            font: {
                              size: 12 // Tamaño de fuente del título
                            }
                          }
                        },
                        scales: {
                          x: {
                            stacked: true,
                            grid: {
                              display: false
                            }
                          },
                          y: {
                            stacked: true,
                            grid: {
                              display: false
                            }
                          }
                        }
                      };

                      // Crear gráfico de barras apiladas
                      var ctx = document.getElementById('myChart'+opc).getContext('2d');
                      var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: data,
                        options: options
                      });
                    }else{
                      $('#chartMessage'+opc).html('<span>Sin Datos a mostrar</span>');
                      $('#chartMessage'+opc).show();
                    }
                }
              }

              function getRandomColor() {
                  var letters = '0123456789ABCDEF';
                  var color = '#';
                  for (var i = 0; i < 6; i++) {
                      color += letters[Math.floor(Math.random() * 16)];
                  }
                  return color;
              }
            </script>
    <!-- <div class="container-fluid mt-5">
        <div class="row">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Online Store Visitors</h3>
                  <a href="javascript:void(0);">View Report</a>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg">820</span>
                    <span>Visitors Over Time</span>
                  </p>
                  <p class="ml-auto d-flex flex-column text-right">
                    <span class="text-success">
                      <i class="fas fa-arrow-up"></i> 12.5%
                    </span>
                    <span class="text-muted">Since last week</span>
                  </p>
                </div>

                <div class="position-relative mb-4">
                  <canvas id="visitors-chart" height="200"></canvas>
                </div>

                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> This Week
                  </span>

                  <span>
                    <i class="fas fa-square text-gray"></i> Last Week
                  </span>
                </div>
              </div>
            </div>


            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">Products</h3>
                <div class="card-tools">
                  <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-download"></i>
                  </a>
                  <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-bars"></i>
                  </a>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                  <thead>
                  <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Sales</th>
                    <th>More</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>
                      <img src="dist/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2">
                      Some Product
                    </td>
                    <td>$13 USD</td>
                    <td>
                      <small class="text-success mr-1">
                        <i class="fas fa-arrow-up"></i>
                        12%
                      </small>
                      12,000 Sold
                    </td>
                    <td>
                      <a href="#" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <img src="dist/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2">
                      Another Product
                    </td>
                    <td>$29 USD</td>
                    <td>
                      <small class="text-warning mr-1">
                        <i class="fas fa-arrow-down"></i>
                        0.5%
                      </small>
                      123,234 Sold
                    </td>
                    <td>
                      <a href="#" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <img src="dist/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2">
                      Amazing Product
                    </td>
                    <td>$1,230 USD</td>
                    <td>
                      <small class="text-danger mr-1">
                        <i class="fas fa-arrow-down"></i>
                        3%
                      </small>
                      198 Sold
                    </td>
                    <td>
                      <a href="#" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <img src="dist/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2">
                      Perfect Item
                      <span class="badge bg-danger">NEW</span>
                    </td>
                    <td>$199 USD</td>
                    <td>
                      <small class="text-success mr-1">
                        <i class="fas fa-arrow-up"></i>
                        63%
                      </small>
                      87 Sold
                    </td>
                    <td>
                      <a href="#" class="text-muted">
                        <i class="fas fa-search"></i>
                      </a>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <div class="col-lg-6">
            <div class="card">
              <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Sales</h3>
                  <a href="javascript:void(0);">View Report</a>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg">$18,230.00</span>
                    <span>Sales Over Time</span>
                  </p>
                  <p class="ml-auto d-flex flex-column text-right">
                    <span class="text-success">
                      <i class="fas fa-arrow-up"></i> 33.1%
                    </span>
                    <span class="text-muted">Since last month</span>
                  </p>
                </div>
   

                <div class="position-relative mb-4">
                  <canvas id="sales-chart" height="200"></canvas>
                </div>

                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> This year
                  </span>

                  <span>
                    <i class="fas fa-square text-gray"></i> Last year
                  </span>
                </div>
              </div>
            </div>


            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">Online Store Overview</h3>
                <div class="card-tools">
                  <a href="#" class="btn btn-sm btn-tool">
                    <i class="fas fa-download"></i>
                  </a>
                  <a href="#" class="btn btn-sm btn-tool">
                    <i class="fas fa-bars"></i>
                  </a>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                  <p class="text-success text-xl">
                    <i class="ion ion-ios-refresh-empty"></i>
                  </p>
                  <p class="d-flex flex-column text-right">
                    <span class="font-weight-bold">
                      <i class="ion ion-android-arrow-up text-success"></i> 12%
                    </span>
                    <span class="text-muted">CONVERSION RATE</span>
                  </p>
                </div>
     
                <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                  <p class="text-warning text-xl">
                    <i class="ion ion-ios-cart-outline"></i>
                  </p>
                  <p class="d-flex flex-column text-right">
                    <span class="font-weight-bold">
                      <i class="ion ion-android-arrow-up text-warning"></i> 0.8%
                    </span>
                    <span class="text-muted">SALES RATE</span>
                  </p>
                </div>
 
                <div class="d-flex justify-content-between align-items-center mb-0">
                  <p class="text-danger text-xl">
                    <i class="ion ion-ios-people-outline"></i>
                  </p>
                  <p class="d-flex flex-column text-right">
                    <span class="font-weight-bold">
                      <i class="ion ion-android-arrow-down text-danger"></i> 1%
                    </span>
                    <span class="text-muted">REGISTRATION RATE</span>
                  </p>
                </div>

              </div>
            </div>
          </div>

        </div>

      </div> -->

</div>
   <!--  <script src="plugins/chart.js/Chart.min.js"></script>
    <script src="dist/js/pages/dashboard3.js"></script> -->
<!-- <section class="content">
<div class="row top20">
<div class="col-md-12">
<div class="col-sm-3">
<div class="small-box bg-red">
<div class="inner">
<h3 id="nrojo">0</h3>
<p>Tickets de Soporte</p>
</div>
<div class="icon">
<i class="ion ion-document"></i>
</div>
<a href="index.php?menu=tickets&idmenu=100" class="small-box-footer">Ver Tickets <i class="fa fa-arrow-circle-right"></i></a>
</div>
</div>

<div class="col-sm-3">
<div class="small-box bg-yellow">
<div class="inner">
<h3 id="namarillo">0</h3>
<p>Tickets de Soporte</p>
</div>
<div class="icon">
<i class="ion ion-document"></i>
</div>
<a href="index.php?menu=tickets&idmenu=100" class="small-box-footer">Ver Tickets <i class="fa fa-arrow-circle-right"></i></a>
</div>
</div>

<div class="col-sm-3">
<div class="small-box bg-green">
<div class="inner">
<h3 id="nverde">0</h3>
<p>Tickets de Soporte</p>
</div>
<div class="icon">
<i class="ion ion-document"></i>
</div>
<a href="index.php?menu=tickets&idmenu=100" class="small-box-footer">Ver Tickets <i class="fa fa-arrow-circle-right"></i></a>
</div>
</div>
</div>
</div>
</section>

<script>
$(function(){
getNtickets();	
});

function getNtickets(){
$("#ntickets").html("<i class='fa fa-refresh fa-spin  fa-fw'></i>");
$.get("operaciones.php", {numero:''+Math.floor(Math.random()*9999999)+'',operacion:'getNtickets',retornar:'no'},function(data){
console.log(data);
datos = $.parseJSON(data);
$("#nrojo").html(datos["rojo"]);
$("#namarillo").html(datos["amarillo"]);
$("#nverde").html(datos["verde"]);
});


}
</script> -->