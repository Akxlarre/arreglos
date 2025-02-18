
function enpesos(n) {
n += '';
var x = n.split('.'),
x1 = x[0],
x2 = x.length > 1 ? '.' + x[1] : '',
rgxp = /(\d+)(\d{3})/;
while (rgxp.test(x1)) {
x1 = x1.replace(rgxp, '$1' + '.' + '$2');
}
return x1 + x2;
}
/********************************
funciones login 
********************************/


function validarcorreo(correo){
var regex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i
;// expresion regular
if (regex.test(correo.trim())) {return true;} else {
return false;
}
}
function CorreoUser(correo){
retornar = 0;
$.ajax({
url : "operaciones.php",
type : "get",
async: false,
data:{operacion:'getCorreoUser',mail:correo,retornar:'no'},
success : function(datos) {
retornar = datos;
},
error: function() {retornar=0;}
});
return retornar;
}

$(function(){
$('.solonumeros').keyup(function (){
this.value = (this.value + '').replace(/[^0-9]/g,'');
});	 
});

function volver(ocultar,mostrar){
$("#"+ocultar+"").hide();
$("#"+mostrar+"").show();
}

function ndias(f1,f2)
 {
 var separaf1 = f1.split('/'); 
 var separaf2 = f2.split('/'); 
 var f_f1 = Date.UTC(separaf1[2],separaf1[1]-1,separaf1[0]); 
 var f_f2 = Date.UTC(separaf2[2],separaf2[1]-1,separaf2[0]); 
 var diferencia = f_f2 - f_f1;
 var dias = Math.floor(diferencia / (1000 * 60 * 60 * 24)); 
 return dias;
 }

 function miles(numero){
	
numero = numero.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
numero = numero.split('').reverse().join('').replace(/^[\.]/,'');
return numero;
}
 
function fechaDate(fecha){
separar = fecha.split("/");
dia = separar[0];
mes = separar[1];
year = separar[2];
devfecha = year+","+mes+","+dia;
return devfecha;
}


